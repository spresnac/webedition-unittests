<?php

/**
 * webEdition CMS
 *
 * $Rev: 4638 $
 * $Author: mokraemer $
 * $Date: 2012-07-01 21:40:14 +0200 (Sun, 01 Jul 2012) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * WorkfFlow Document Step definition
 * This class describe document step in workflow process
 */
class we_workflow_documentStep extends we_workflow_base{

	const STATUS_UNKNOWN = 0;
	const STATUS_APPROVED = 1;
	const STATUS_CANCELED = 2;
	const STATUS_AUTOPUBLISHED = 3;

	var $ID;
	var $workflowStepID;
	var $startDate;
	var $finishDate;
	var $workflowDocID;

	/**
	 * list of document tasks
	 */
	var $tasks = array();

	/**
	 * Default Constructor
	 *
	 * Can load or create new Workflow Step Definition depends of parameter
	 */
	function __construct($wfDocumentStep = 0){
		parent::__construct();
		$this->table = WORKFLOW_DOC_STEP_TABLE;
		$this->ClassName = __CLASS__;

		$this->persistents[] = "ID";
		$this->persistents[] = "workflowDocID";
		$this->persistents[] = "workflowStepID";
		$this->persistents[] = "startDate";
		$this->persistents[] = "finishDate";
		$this->persistents[] = "Status";

		$this->ID = 0;
		$this->workflowDocID = 0;
		$this->workflowStepID = 0;
		$this->startDate = 0;
		$this->finishDate = 0;
		$this->Status = 0;

		$this->tasks = array();
		if($wfDocumentStep){
			$this->ID = $wfDocumentStep;
			$this->load();
		}
	}

	/**
	 * Load data from database
	 *
	 */
	function load($id = 0){
		if($id)
			$this->ID = $id;

		if($this->ID){
			parent::load();
			## get tasks for workflow
			$this->tasks = we_workflow_documentTask::__getAllTasks($this->ID);
			return true;
		}
		else
			return false;
	}

	/**
	 * Start step, activate it
	 *
	 */
	function start($desc = ""){
		$this->startDate = time();


		$workflowDoc = new we_workflow_document($this->workflowDocID);
		$workflowStep = new we_workflow_step($this->workflowStepID);
		$deadline = $this->startDate + round($workflowStep->Worktime * 3600);

		// set all tasks to pending
		for($i = 0; $i < count($this->tasks); $i++){
			$workflowTask = new we_workflow_task($this->tasks[$i]->workflowTaskID);
			if($workflowTask->userID){
				//send todo to next user
				$path = "<b>" . g_l('modules_workflow', '[' . stripTblPrefix($workflowDoc->document->ContentType == 'objectFile' ? OBJECT_FILES_TABLE : FILE_TABLE) . '][messagePath]') . ':</b>&nbsp;<a href="javascript:top.opener.top.weEditorFrameController.openDocument(\'' . $workflowDoc->document->Table . '\',\'' . $workflowDoc->document->ID . '\',\'' . $workflowDoc->document->ContentType . '\');");" >' . $workflowDoc->document->Path . '</a>';
				$mess = "<p><b>" . g_l('modules_workflow', '[todo_next]') . "</b></p><p>" . $desc . "</p><p>" . $path . "</p>";

				$this->tasks[$i]->todoID = $this->sendTodo($workflowTask->userID, g_l('modules_workflow', '[todo_subject]'), $mess . "<p>" . $path . "</p>", $deadline);
				if($workflowTask->Mail){
					$foo = f("SELECT Email FROM " . USER_TABLE . " WHERE ID=" . intval($workflowTask->userID), "Email", $this->db);
					$this_user = getHash("SELECT First,Second,Email FROM " . USER_TABLE . " WHERE ID=" . intval($_SESSION["user"]["ID"]), $this->db);
					//if($foo) we_mail($foo,correctUml(g_l('modules_workflow','[todo_next]')),$desc,(isset($this_user["Email"]) && $this_user["Email"]!="" ? "From: ".$this_user["First"]." ".$this_user["Second"]." <".$this_user["Email"].">\n":"")."Content-Type: text/html; charset=iso-8859-1");
					if($foo){
						$desc = str_replace('<br />', "\n", $desc);
						$mess = g_l('modules_workflow', '[todo_next]') . " ID:" . $workflowDoc->document->ID . ", Pfad:" . $workflowDoc->document->Path . "\n\n" . $desc;

						we_mail($foo, correctUml(g_l('modules_workflow', '[todo_next]') . ($this->EmailPath ? ' ' . $workflowDoc->document->Path : '')), $mess, (isset($this_user["Email"]) && $this_user["Email"] != "" ? $this_user["First"] . " " . $this_user["Second"] . " <" . $this_user["Email"] . ">" : ""));
					}
				}
			}
		}
		return true;
	}

	function finish(){
		$this->finishDate = time();
		return true;
	}

	/**
	 * create all tasks for step
	 */
	function createAllTasks(){
		$this->tasks = we_workflow_documentTask::__createAllTasks($this->workflowStepID);
		return true;
	}

	/**
	 * save workflow step in database
	 *
	 */
	function save(){
		$db = new DB_WE();

		parent::save();

		## save all tasks also ##
		foreach($this->tasks as $k => $v){
			$this->tasks[$k]->documentStepID = $this->ID;
			$this->tasks[$k]->save();
		}
	}

	function delete(){
		foreach($this->tasks as $v)
			$v->delete();
		parent::delete();
	}

	function approve($uID, $desc, $force = false){
		if($force){
			foreach($this->tasks as $tk => $tv){
				$this->tasks[$tk]->approve();
			}
			$this->Status = self::STATUS_APPROVED;
			$this->finishDate = time();
			//insert into document Log
			$this->Log->logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE_FORCE, $desc);
			return true;
		}
		$i = $this->findTaskByUser($uID);
		if($i > -1){
			$this->tasks[$i]->approve();

			$workflowStep = new we_workflow_step($this->workflowStepID);
			if($workflowStep->stepCondition == 0)
				$this->Status = self::STATUS_APPROVED;
			else{
				$num = $this->findNumOfFinishedTasks();
				if($num == count($this->tasks)){
					$status = true;
					foreach($this->tasks as $k => $v){
						$status = $status && ($v->Status == we_workflow_documentTask::STATUS_APPROVED ? true : false);
					}

					if($status)
						$this->Status = self::STATUS_APPROVED;
				}
			}
			if($this->Status == self::STATUS_APPROVED || $this->Status == self::STATUS_CANCELED){
				$this->finishDate = time();
				foreach($this->tasks as $tk => $tv){
					if($tv->Status == we_workflow_documentTask::STATUS_UNKNOWN)
						$this->tasks[$tk]->removeTodo();
				}
			}
			//insert into document Log
			$this->Log->logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE, $desc);
			return true;
		}
		return false;
	}

	function autopublish($uID, $desc, $force = false){
		if($force){
			foreach($this->tasks as $tk => $tv){
				$this->tasks[$tk]->approve();
			}
			$this->Status = self::STATUS_APPROVED;
			$this->finishDate = time();
			//insert into document Log
			$this->Log->logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE_FORCE, $desc);
			return true;
		}
		$i = $this->findTaskByUser($uID);
		if($i > -1){
			$this->tasks[$i]->approve();

			$workflowStep = new we_workflow_step($this->workflowStepID);
			if($workflowStep->stepCondition == 0)
				$this->Status = self::STATUS_APPROVED;
			else{
				$num = $this->findNumOfFinishedTasks();
				if($num == count($this->tasks)){
					$status = true;
					foreach($this->tasks as $k => $v){
						$status = $status && ($v->Status == we_workflow_documentTask::STATUS_APPROVED ? true : false);
					}

					if($status)
						$this->Status = self::STATUS_APPROVED;
				}
			}
			if($this->Status == self::STATUS_APPROVED || $this->Status == self::STATUS_CANCELED){
				$this->finishDate = time();
				foreach($this->tasks as $tk => $tv){
					if($tv->Status == we_workflow_documentTask::STATUS_UNKNOWN)
						$this->tasks[$tk]->removeTodo();
				}
			}
			//insert into document Log
			$this->Log->logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_APPROVE, $desc);
			return true;
		}
		return false;
	}

	function decline($uID, $desc, $force = false){
		if($force){
			foreach($this->tasks as $tk => $tv)
				$this->tasks[$tk]->decline();;
			$this->Status = self::STATUS_CANCELED;
			$this->finishDate = time();
			//insert into document Log
			$this->Log->logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_DECLINE, $desc);
			return true;
		}
		$i = $this->findTaskByUser($uID);
		if($i > -1){
			$this->tasks[$i]->decline();
			$workflowStep = new we_workflow_step($this->workflowStepID);
			$this->Status = self::STATUS_CANCELED;
			if($this->Status == self::STATUS_APPROVED || $this->Status == self::STATUS_CANCELED)
				$this->finishDate = time();
			//insert into document Log
			$this->Log->logDocumentEvent($this->workflowDocID, $uID, we_workflow_log::TYPE_DECLINE, $desc);
			return true;
		}
		return false;
	}

	function findTaskByUser($uID){
		for($i = 0; $i < count($this->tasks); $i++){
			$workflowTask = new we_workflow_task($this->tasks[$i]->workflowTaskID);
			if($workflowTask->userID == $uID)
				return $i;
		}
		return -1;
	}

	function findNumOfFinishedTasks(){
		$num = 0;
		for($i = 0; $i < count($this->tasks); $i++){
			if($this->tasks[$i]->Status != 0)
				$num++;
		}
		return $num;
	}

	//---------------------------------- STATIC FUNCTIONS -------------------------------

	/**
	 * return all steps for workflow document (created)
	 *
	 */
	static function __getAllSteps($workflowDocumentID){

		$db = new DB_WE();

		$db->query("SELECT ID FROM " . WORKFLOW_DOC_STEP_TABLE . " WHERE workflowDocID=" . intval($workflowDocumentID) . " ORDER BY ID");
		$docSteps = array();
		while($db->next_record()) {
			$docSteps[] = new self($db->f("ID"));
		}
		return $docSteps;
	}

	/**
	 * create all steps for workflow document
	 *
	 */
	static function __createAllSteps($workflowID){

		$db = new DB_WE();
		$db->query('SELECT ID FROM ' . WORKFLOW_STEP_TABLE . ' WHERE workflowID =' . intval($workflowID) . ' ORDER BY ID');
		$docSteps = array();
		while($db->next_record()) {
			$docSteps[] = self::__createStep($db->f("ID"));
		}
		return $docSteps;
	}

	/**
	 * Create step
	 *
	 */
	static function __createStep($WorkflowStep){

		if(is_array($WorkflowStep))
			return self::__createStepFromHash($WorkflowStep);

		$tmp = getHash('SELECT * FROM ' . WORKFLOW_STEP_TABLE . ' WHERE ID=' . intval($WorkflowStep) . ' ORDER BY ID', new DB_WE());
		if(count($tmp) == 0){
			return false;
		}
		return self::__createStepFromHash($tmp);
	}

	/**
	 * Create step from hash
	 *
	 */
	static function __createStepFromHash($WorkflowStepArray){
		$docStep = new self();

		$docStep->workflowStepID = $WorkflowStepArray["ID"];
		$docStep->startDate = 0;
		$docStep->finishDate = 0;
		$docStep->Status = self::STATUS_UNKNOWN;
		$docStep->tasks = we_workflow_documentTask::__createAllTasks($docStep->workflowStepID);
		return $docStep;
	}

	//-------------------------------STATIC FUNCTIONS END -----------------------------------
}

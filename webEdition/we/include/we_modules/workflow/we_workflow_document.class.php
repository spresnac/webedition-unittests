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
class we_workflow_document extends we_workflow_base{
	const STATUS_UNKNOWN=0;
	const STATUS_FINISHED=1;
	const STATUS_CANCELED=2;

	//properties
	var $ID;
	var $workflowID;
	var $documentID;
	var $userID;
	var $Status;
	//accossiations
	var $workflow;
	var $document;
	var $steps = array();

	/**
	 * Default Constructor
	 *
	 */
	function __construct($wfDocument=0){
		parent::__construct();
		$this->table = WORKFLOW_DOC_TABLE;
		$this->ClassName = __CLASS__;
		$this->persistents[] = "ID";
		$this->persistents[] = "workflowID";
		$this->persistents[] = "documentID";
		$this->persistents[] = "userID";
		$this->persistents[] = "Status";

		$this->ID = 0;
		$this->workflowID = 0;
		$this->documentID = 0;
		$this->userID = 0;
		$this->Status = 0;
		$this->steps = array();
		$this->document = false;
		$this->workflow = false;

		if($wfDocument){
			$this->ID = $wfDocument;
			$this->load($wfDocument);
		}
	}

	/**
	 * Load data from database
	 */
	function load($id=0){
		if($id)
			$this->ID = $id;

		if($this->ID){
			parent::load();
			$this->workflow = new we_workflow_workflow($this->workflowID);

			$docTable = $this->workflow->Type == we_workflow_workflow::OBJECT ? OBJECT_FILES_TABLE : FILE_TABLE;
			$this->db->query("SELECT * FROM $docTable WHERE ID=" . intval($this->documentID));
			if($this->db->next_record())
				if($this->db->f("ClassName")){
					$tmp = $this->db->f("ClassName");
					$this->document = new $tmp();
					if($this->document){
						$this->document->initByID($this->documentID, $docTable);
						$this->document->we_load(we_class::LOAD_TEMP_DB);
					}
				}

			$this->steps = we_workflow_documentStep::__getAllSteps($this->ID);
		}
	}

	function approve($uID, $desc, $force=false){
		$i = $this->findLastActiveStep();
		if($i < 0 && !$force){
			return false;
		}
		$ret = $this->steps[$i]->approve($uID, $desc, $force);
		if($this->steps[$i]->Status == we_workflow_documentStep::STATUS_APPROVED){
			$this->nextStep($i, $desc, $uID);
		}
		return $ret;
	}

	function autopublish($uID, $desc, $force=false){
		$i = $this->findLastActiveStep();
		if($i < 0 && !$force){
			return false;
		}
		$ret = $this->steps[$i]->approve($uID, $desc, $force);
		if($this->steps[$i]->Status == we_workflow_documentStep::STATUS_APPROVED){
			$this->finishWorkflow(1, $uID);
			$this->document->save();
			if($this->document->i_publInScheduleTable()){
				$foo = $this->document->getNextPublishDate();
			} else{
				$this->document->we_publish();
			}
			$path = "<b>" . g_l('modules_workflow', '[' . stripTblPrefix($this->workflow->Type == 2 ? OBJECT_FILES_TABLE : FILE_TABLE) . '][messagePath]') . ':</b>&nbsp;<a href="javascript:top.opener.top.weEditorFrameController.openDocument(\'' . $this->document->Table . '\',\'' . $this->document->ID . '\',\'' . $this->document->ContentType . '\');");" >' . $this->document->Path . '</a>';
			$mess = "<p><b>" . g_l('modules_workflow', '[auto_published]') . "</b></p><p>" . $desc . "</p><p>" . $path . "</p>";
			$deadline = time();
			$this->sendTodo($this->userID, g_l('modules_workflow', '[auto_published]'), $mess, $deadline, 1);
			$desc = str_replace('<br />', "\n", $desc);
			$mess = g_l('modules_workflow', '[auto_published]') . "\n\n" . $desc . "\n\n" . $this->document->Path;
			$this->sendMail($this->userID, g_l('modules_workflow', '[auto_published]') . ($this->workflow->EmailPath ? ' ' . $this->document->Path : ''), $mess);
		}
		return $ret;
	}

	function decline($uID, $desc, $force=false){
		$i = $this->findLastActiveStep();
		if($i < 0 && !$force)
			return false;
		$ret = $this->steps[$i]->decline($uID, $desc, $force);
		if($this->steps[$i]->Status == we_workflow_documentStep::STATUS_CANCELED){
			$this->finishWorkflow(1, $uID);

			$path = "<b>" . g_l('modules_workflow', '[' . stripTblPrefix($this->workflow->Type == 2 ? OBJECT_FILES_TABLE : FILE_TABLE) . '][messagePath]') . ':</b>&nbsp;<a href="javascript:top.opener.top.weEditorFrameController.openDocument(\'' . $this->document->Table . '\',\'' . $this->document->ID . '\',\'' . $this->document->ContentType . '\');");" >' . $this->document->Path . '</a>';
			$mess = "<p><b>" . g_l('modules_workflow', '[todo_returned]') . "</b></p><p>" . $desc . "</p><p>" . $path . "</p>";
			$deadline = time() + 3600;
			$this->sendTodo($this->userID, g_l('modules_workflow', '[todo_returned]'), $mess, $deadline, 1);
			$desc = str_replace('<br />', "\n", $desc);
			$mess = g_l('modules_workflow', '[todo_returned]') . "\n\n" . $desc . "\n\n" . $this->document->Path;
			$this->sendMail($this->userID, g_l('modules_workflow', '[todo_returned]') . ($this->workflow->EmailPath ? ' ' . $this->document->Path : ''), $mess);
		}
		return $ret;
	}

	function restartWorkflow($desc){
		foreach($this->steps as $k => $v)
			$this->steps[$k]->delete();
		$this->steps = we_workflow_documentStep::__createAllSteps($this->workflowID);
		$this->steps[0]->start($desc);
	}

	function nextStep($index=-1, $desc="", $uid=0){
		if($index > -1){
			if($index < count($this->steps) - 1)
				$this->steps[$index + 1]->start($desc);
			else
				$this->finishWorkflow(0, $uid);
		}
	}

	function finishWorkflow($force=0, $uID=0){
		if($force){
			$this->Status = self::STATUS_CANCELED;
			foreach($this->steps as $sk => $sv){
				if($this->steps[$sk]->Status == we_workflow_documentStep::STATUS_UNKNOWN)
					$this->steps[$sk]->Status = we_workflow_documentStep::STATUS_CANCELED;
				foreach($this->steps[$sk]->tasks as $tk => $tv){
					if($this->steps[$sk]->tasks[$tk]->Status == we_workflow_documentTask::STATUS_UNKNOWN)
						$this->steps[$sk]->tasks[$tk]->Status = we_workflow_documentTask::STATUS_CANCELED;
				}
			}
			//insert into document Log
			$this->Log->logDocumentEvent($this->ID, $uID, we_workflow_log::TYPE_DOC_FINISHED_FORCE, "");
		}
		else{
			$this->Status = self::STATUS_FINISHED;
			$this->Log->logDocumentEvent($this->ID, $uID, we_workflow_log::TYPE_DOC_FINISHED, "");
		}
		return true;
	}

	/**
	 * Create next step or finish workflow document if last step is done
	 *
	 */
	function createNextStep($stepKey, $uid=0){
		if($stepKey >= count($this->steps)){
			// no more steps, finish workflow
			return $this->finishWorkflow(0, $uid);
		}
		$step = &$this->steps[$stepKey];
		$step->start();
		return true;
	}

	/**
	 * Find last document Status step
	 *
	 */
	function findLastActiveStep(){
		for($i = count($this->steps) - 1; $i >= 0; $i--){
			if($this->steps[$i]->startDate > 0){
				return $i;
			}
		}
		return -1;
	}

	/**
	 * save workflow document in database
	 *
	 */
	function save(){
		if(!$this->documentID){
			return false;
		}
		parent::save();
		for($i = 0; $i < count($this->steps); $i++){
			$this->steps[$i]->workflowDocID = $this->ID;
			$this->steps[$i]->save();
		}
		return true;
	}

	function delete(){
		if(!$this->ID){
			return false;
		}

		foreach($this->steps as $k => $v)
			$v->delete();
		parent::delete();
		return true;
	}

	/*	 * ***************** STATIC FUNCTIONS**************************
	  /**
	 * return workflowDocument for document
	 *    return false if no workflow
	 */

	function find($documentID, $type="0,1", $status=self::STATUS_UNKNOWN){

		$db = new DB_WE();
		$db->query("SELECT " . WORKFLOW_DOC_TABLE . ".ID FROM " . WORKFLOW_DOC_TABLE . "," . WORKFLOW_TABLE . " WHERE " . WORKFLOW_DOC_TABLE . ".workflowID=" . WORKFLOW_TABLE . ".ID AND " . WORKFLOW_DOC_TABLE . ".documentID=" . intval($documentID) . " AND " . WORKFLOW_DOC_TABLE . ".Status IN (" . $db->escape($status) . ")" . ($type != "" ? " AND " . WORKFLOW_TABLE . ".Type IN (" . $db->escape($type) . ")" : "") . " ORDER BY " . WORKFLOW_DOC_TABLE . ".ID DESC");
		if($db->next_record()){
			return new self($db->f("ID"));
		} else{
			return false;
		}
	}

	/**
	 * Create new workflow document
	 *    if workflow for that document exists, function will return it
	 */
	function createNew($documentID, $type, $workflowID, $userID){
		$newWfDoc = self::find($documentID, $type);

		if(isset($newWfDoc->ID)){
			return $newWfDoc;
		}

		$newWFDoc = new self();
		$newWFDoc->documentID = $documentID;
		$newWFDoc->userID = $userID;
		$newWFDoc->workflowID = $workflowID;
		$newWFDoc->workflow = new we_workflow_workflow($workflowID);
		$newWFDoc->steps = we_workflow_documentStep::__createAllSteps($workflowID);

		return $newWFDoc;
	}

}

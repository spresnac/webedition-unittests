<?php

/**
 * webEdition CMS
 *
 * $Rev: 3954 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 20:19:34 +0100 (Tue, 07 Feb 2012) $
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
 * WorkfFlow Document Task definition
 *
 * This class describe document task in workflow process
 *
 */
class we_workflow_documentTask extends we_workflow_base{
	const STATUS_UNKNOWN=0;
	const STATUS_APPROVED=1;
	const STATUS_CANCELED=2;

	// workflow document task ID
	var $ID;
	// workflow document step ID
	var $documentStepID;
	// workflow task ID
	var $workflowTaskID;
	// date when task is done
	var $Date;
	// todo id
	var $todoID;
	// Status of document task
	var $Status;

	/**
	 * Default Constructor
	 */
	function __construct($wfDocumentTask=0){
		parent::__construct();
		$this->table = WORKFLOW_DOC_TASK_TABLE;
		$this->ClassName = __CLASS__;

		$this->persistents[] = "ID";
		$this->persistents[] = "documentStepID";
		$this->persistents[] = "workflowTaskID";
		$this->persistents[] = "Date";
		$this->persistents[] = "todoID";
		$this->persistents[] = "Status";

		$this->ID = 0;
		$this->documentStepID = 0;
		$this->workflowTaskID = 0;
		$this->Date = 0;
		$this->todoID = 0;
		$this->Status = self::STATUS_UNKNOWN;

		if($wfDocumentTask){
			$this->ID = $wfDocumentTask;
			$this->load();
		}
	}

	function approve(){
		$this->Status = self::STATUS_APPROVED;
		$this->Date = time();
		$this->doneTodo();
	}

	function decline(){
		$this->Status = self::STATUS_CANCELED;
		$this->Date = time();
		$this->rejectTodo();
	}

	function removeTodo(){
		if($this->todoID)
			parent::removeTodo($this->todoID);
	}

	function doneTodo(){
		if($this->todoID)
			parent::doneTodo($this->todoID);
	}

	function rejectTodo(){
		if($this->todoID){
			parent::rejectTodo($this->todoID);
		}
	}

	//--------------------------------STATIC FUNCTIONS ------------------------------
	/**
	 * returns all tasks for workflow step
	 *
	 */
	function __getAllTasks($workflowDocumentStep){

		$db = new DB_WE();


		$db->query("SELECT ID FROM " . WORKFLOW_DOC_TASK_TABLE . " WHERE documentStepID =" . intval($workflowDocumentStep) . " ORDER BY ID");

		$docTasks = array();

		while($db->next_record()) {
			$docTasks[] = new we_workflow_documentTask($db->f("ID"));
		}
		return $docTasks;
	}

	/**
	 * creates all tasks for workflow step
	 *
	 */
	function __createAllTasks($workflowStepID){
		$db = new DB_WE();

		$db->query("SELECT ID FROM " . WORKFLOW_TASK_TABLE . " WHERE stepID=" . intval($workflowStepID) . " ORDER BY ID");
		$docTasks = array();
		while($db->next_record()) {
			$docTasks[] = we_workflow_documentTask::__createTask($db->f("ID"));
		}
		return $docTasks;
	}

	/**
	 * Create task
	 */
	function __createTask($WorkflowTask){
		if(is_array($WorkflowTask))
			return we_workflow_documentTask::__createTaskFromHash($WorkflowTask);

		$db = new DB_WE;

		$db->query("SELECT * FROM " . WORKFLOW_TASK_TABLE . " WHERE ID=" . intval($WorkflowTask) . " ORDER BY ID");
		if(!$db->next_record()){
			return false;
		}
		return we_workflow_documentTask::__createTaskFromHash($db->Record);
	}

	/**
	 * Create task from hash
	 */
	function __createTaskFromHash($WorkflowTaskArray){
		$docTask = new we_workflow_documentTask();
		$docTask->workflowTaskID = $WorkflowTaskArray["ID"];
		return $docTask;
	}

	//--------------------------------STATIC FUNCTIONS END ------------------------------
}

?>
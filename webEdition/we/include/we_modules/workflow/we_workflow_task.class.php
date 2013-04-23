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
 * General Definition of WebEdition Workflow Task
 */
class we_workflow_task extends we_workflow_base{

	var $ID;
	var $stepID;
	var $userID;
	var $Edit;
	var $Mail;

	/**
	 * Default Constructor
	 *
	 * Can load or create new Workflow Task Definition depends of parameter
	 */
	function __construct($taskID = 0){
		parent::__construct();
		$this->table = WORKFLOW_TASK_TABLE;

		$this->persistents[] = "ID";
		$this->persistents[] = "userID";
		$this->persistents[] = "Edit";
		$this->persistents[] = "Mail";
		$this->persistents[] = "stepID";


		$this->ID = 0;
		$this->stepID = 0;
		$this->userID = 0;
		$this->Edit = 0;
		$this->Mail = 0;

		if($taskID > 0){
			$this->load($taskID);
		}
	}

	/**
	 * get all workflow tasks from database (STATIC)
	 */
	function getAllTasks($stepID){
		$db = new DB_WE;

		$db->query("SELECT ID FROM " . WORKFLOW_TASK_TABLE . " WHERE stepID  =" . intval($stepID) . " ORDER BY ID");

		$tasks = array();

		while($db->next_record()) {
			$tasks[] = new self($db->f("ID"));
		}
		return $tasks;
	}

	/**
	 * Load task from database
	 */
	function load($id=0){
		if($id)
			$this->ID = $id;
		if($this->ID){
			parent::load();
			return true;
		} else{
			$this->ErrorReporter->Error("No Task with ID $taskID !");
			return false;
		}
	}

}
<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
include_once(WE_MODULES_PATH . 'workflow/we_conf_workflow.inc.php');

class we_workflow_utility{

	function getTypeForTable($table){
		switch($table){
			case FILE_TABLE:
				return '0,1';
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : -1):
				return '2';
			default:
				return '0,1';
		}
	}

	function insertDocInWorkflow($docID, $table, $workflowID, $userID, $desc){
		$desc = nl2br($desc);
		$type = self::getTypeForTable($table);
		//create new workflow document
		$doc = we_workflow_document::createNew($docID, $type, $workflowID, $userID, $desc);
		if(isset($doc->ID)){
			$doc->save();
			if(isset($doc->steps[0]))
				$doc->steps[0]->start($desc);
			//insert into document history
			$doc->Log->logDocumentEvent($doc->ID, $userID, we_workflow_log::TYPE_DOC_INSERTED, $desc);
			$doc->save();
			return true;
		}
		return false;
	}

	function approve($docID, $table, $userID, $desc, $force = false){
		/* approve step */
		$desc = nl2br($desc);
		$doc = self::getWorkflowDocument($docID, $table);
		if(isset($doc->ID)){
			if($doc->approve($userID, $desc, $force)){
				$doc->save();
				return true;
			}
		}
		return false;
	}

	/*

	 */

	function decline($docID, $table, $userID, $desc, $force = false){
		//decline step
		$desc = nl2br($desc);
		$doc = self::getWorkflowDocument($docID, $table);
		if(!isset($doc->ID)){
			if($doc->decline($userID, $desc, $force)){
				$doc->save();
				return true;
			}
		}
		return false;
	}

	/**
	  This function can be used to force removal
	  of document from workflow.
	 */
	function removeDocFromWorkflow($docID, $table, $userID, $desc){
		$desc = nl2br($desc);
		$doc = self::getWorkflowDocument($docID, $table);
		if(isset($doc->ID))
			if($doc->finishWorkflow(1, $userID)){
				$doc->save();
				//insert into document history
				$doc->Log->logDocumentEvent($doc->ID, $userID, we_workflow_log::TYPE_DOC_REMOVED, $desc);
				return true;
			}
		return false;
	}

	/**
	  Function returns workflow document object for defined docID
	  If workflow documnet is not defined for that document false
	  will be returned
	 */
	function getWorkflowDocument($docID, $table, $status = we_workflow_document::STATUS_UNKNOWN){
		$type = self::getTypeForTable($table);
		return we_workflow_document::find($docID, $type, $status);
	}

	/**
	  Same like getWorkflowDocument but returns
	  workflow document id (not object)
	 */
	static function getWorkflowDocumentID($docID, $table, $status = we_workflow_document::STATUS_UNKNOWN){
		$doc = self::getWorkflowDocument($docID, $table, $status);
		if(!isset($doc->ID) || !$doc->ID){
			return false;
		}
		return $doc->ID;
	}

	/**
	  Functions tries to find workflow for defined
	  documents parameters and returns new document object
	 */
	function getWorkflowDocumentForDoc($doctype = 0, $categories = "", $folder = -1){
		$workflowID = we_workflow_workflow::getDocumentWorkflow($doctype, $categories, $folder);
		$newDoc = new we_workflow_document();
		$newDoc->workflowID = $workflowID;
		$newDoc->steps = we_workflow_documentStep::__createAllSteps($workflowID);
		return $newDoc;
	}

	/**
	  Functions tries to find workflow for defined
	  objects parametars and returns new document object
	 */
	function getWorkflowDocumentForObject($object, $categories = '', $folderID = 0){
		$workflowID = we_workflow_workflow::getObjectWorkflow($object, $categories, $folderID);
		$newDoc = new we_workflow_document();
		$newDoc->workflowID = $workflowID;
		$newDoc->steps = we_workflow_documentStep::__createAllSteps($workflowID);
		return $newDoc;
	}

	function getWorkflowName($workflowID, $table){
		$foo = self::getAllWorkflows(we_workflow_workflow::STATE_ACTIVE, $table);
		return $foo[$workflowID];
	}

	function getWorkflowID($workflowName, $table){
		$foo = self::getAllWorkflows(we_workflow_workflow::STATE_ACTIVE, $table);
		return array_search($workflowName, $foo);
	}

	function getAllWorkflows($status = we_workflow_workflow::STATE_ACTIVE, $table = FILE_TABLE){ // returns hash array with ID as key and Name as value
		$type = self::getTypeForTable($table);
		return we_workflow_workflow::getAllWorkflowsInfo($status, $type);
	}

	function inWorkflow($docID, $table){
		$doc = self::getWorkflowDocument($docID, $table);
		return (isset($doc->ID) && $doc->ID);
	}

	function isWorkflowFinished($docID, $table){
		$doc = self::getWorkflowDocument($docID, $table);
		if(!isset($doc->ID))
			return false;
		$i = $doc->findLastActiveStep();
		if(($i <= 0) || ($i < count($doc->steps) - 1) || ($doc->steps[$i]->findNumOfFinishedTasks() < count($doc->steps[$i]->tasks))){
			return false;
		}
		return true;
	}

	/**
	  Function returns true if user is in workflow for
	  defined documnet id, otherwise false
	 */
	function isUserInWorkflow($docID, $table, $userID){
		$doc = self::getWorkflowDocument($docID, $table);
		if(isset($doc->ID)){
			$i = $doc->findLastActiveStep();
			if($i < 0)
				return false;
			$j = $doc->steps[$i]->findTaskByUser($userID);
			if($j > -1){
				if($doc->steps[$i]->tasks[$j]->Status == we_workflow_documentTask::STATUS_UNKNOWN)
					return true;
				else
					return false;
			}
			else
				return false;
		}
		return false;
	}

	/**
	  Function returns true if user can edit
	  defined documnet, otherwise false
	 */
	function canUserEditDoc($docID, $table, $userID){
		if($_SESSION["perms"]["ADMINISTRATOR"])
			return true;
		$doc = self::getWorkflowDocument($docID, $table);
		if(isset($doc->ID)){
			$i = $doc->findLastActiveStep();
			if($i < 0){
				return false;
			}
			$wStep = new we_workflow_step($doc->steps[$i]->workflowStepID);
			foreach($wStep->tasks as $k => $v){
				if($v->userID == $userID && $v->Edit){
					return true;
				}
			}
		}
		return false;
	}

	function getWorkflowDocsForUser($userID, $table, $isAdmin = false, $permPublish = false, $ws = ""){
		if($isAdmin){
			return self::getAllWorkflowDocs($table);
		}
		$ids = ($permPublish ? self::getWorkflowDocsFromWorkspace($table, $ws) : array());
		$wids = self::getAllWorkflowDocs($table);

		foreach($wids as $id){
			if(!in_array($id, $ids)){
				if(self::isUserInWorkflow($id, $table, $userID)){
					array_push($ids, $id);
				}
			}
		}

		return $ids;
	}

	function getAllWorkflowDocs($table){
		$type = self::getTypeForTable($table);
		$db = new DB_WE();
		$ids = array();
		$db->query("SELECT DISTINCT " . WORKFLOW_DOC_TABLE . ".documentID as ID FROM " . WORKFLOW_DOC_TABLE . "," . WORKFLOW_TABLE . " WHERE " . WORKFLOW_DOC_TABLE . ".workflowID=" . WORKFLOW_TABLE . ".ID AND " . WORKFLOW_DOC_TABLE . ".Status = " . we_workflow_document::STATUS_UNKNOWN . " AND " . WORKFLOW_TABLE . ".Type IN(" . $type . ")");
		while($db->next_record()) {
			if(!in_array($db->f("ID"), $ids)){
				array_push($ids, $db->f("ID"));
			}
		}
		return $ids;
	}

	function getWorkflowDocsFromWorkspace($table, $ws){
		$wids = self::getAllWorkflowDocs($table);
		$ids = array();

		foreach($wids as $id){
			if(!in_array($id, $ids)){
				if(is_array($ws) && !empty($ws)){
					if(in_workspace($id, $ws, $table, $db)){
						$ids[]= $id;
					}
				} else{
					$ids[]= $id;
				}
			}
		}

		return $ids;
	}

	function findLastActiveStep($docID, $table){
		$doc = self::getWorkflowDocument($docID, $table);
		if(!isset($doc->ID))
			return false;
		return $doc->findLastActiveStep();
	}

	function getNumberOfSteps($docID, $table){
		$doc = self::getWorkflowDocument($docID, $table);
		if(!isset($doc->ID))
			return false;
		return $doc->steps;
	}

	static function getDocumentStatusInfo($docID, $table){
		$doc = self::getWorkflowDocumentID($docID, $table);
		if($doc){
			return we_workflow_view::getDocumentStatus($doc, 700);
		}
	}

	/*
	  Cronjob function
	 */

	function forceOverdueDocuments($userID = 0){
		$db = new DB_WE();
		$ret = '';
		$db->query('SELECT ' . WORKFLOW_DOC_TABLE . '.ID AS docID,' . WORKFLOW_DOC_STEP_TABLE . ".ID AS docstepID," . WORKFLOW_STEP_TABLE . ".ID AS stepID FROM " . WORKFLOW_DOC_TABLE . "," . WORKFLOW_DOC_STEP_TABLE . "," . WORKFLOW_STEP_TABLE . " WHERE " . WORKFLOW_DOC_TABLE . ".ID=" . WORKFLOW_DOC_STEP_TABLE . ".workflowDocID AND " . WORKFLOW_DOC_STEP_TABLE . ".workflowStepID=" . WORKFLOW_STEP_TABLE . ".ID AND " . WORKFLOW_DOC_STEP_TABLE . ".startDate<>0 AND (" . WORKFLOW_DOC_STEP_TABLE . ".startDate+ ROUND(" . WORKFLOW_STEP_TABLE . ".Worktime*3600))<" . time() . " AND " . WORKFLOW_DOC_STEP_TABLE . ".finishDate=0 AND " . WORKFLOW_DOC_STEP_TABLE . ".Status=" . we_workflow_documentStep::STATUS_UNKNOWN . " AND " . WORKFLOW_DOC_TABLE . ".Status=" . we_workflow_document::STATUS_UNKNOWN);
		while($db->next_record()) {
			@set_time_limit(50);
			$workflowDocument = new we_workflow_document($db->f('docID'));
			$userID = $userID ? $userID : $workflowDocument->userID;
			$_SESSION['user']['ID'] = $userID;
			if(!self::isWorkflowFinished($workflowDocument->document->ID, $workflowDocument->document->Table)){
				$next = false;
				$workflowStep = new we_workflow_step($db->f('stepID'));
				$next = $workflowStep->timeAction == 1 ? true : false;
				if($next){
					if($workflowDocument->findLastActiveStep() >= count($workflowDocument->steps) - 1){
						if($workflowDocument->workflow->LastStepAutoPublish){
							$workflowDocument->autopublish($userID, g_l('modules_workflow', '[auto_published]'), true);
							$ret.="(ID: " . $workflowDocument->ID . ") " . g_l('modules_workflow', '[auto_published]') . "\n";
						} else{
							$workflowDocument->decline($userID, g_l('modules_workflow', '[auto_declined]'), true);
							$ret.="(ID: " . $workflowDocument->ID . ") " . g_l('modules_workflow', '[auto_declined]') . "\n";
						}
					} else{
						$workflowDocument->approve($userID, g_l('modules_workflow', '[auto_approved]'), true);
						$ret.="(ID: " . $workflowDocument->ID . ") " . g_l('modules_workflow', '[auto_approved]') . "\n";
					}
				}
				$workflowDocument->save();
			}
		}
		return $ret;
	}

	function getLogButton($docID, $table){
		$type = self::getTypeForTable($table);
		return we_button::create_button("logbook", "javascript:new jsWindow('" . WE_WORKFLOW_MODULE_DIR . "edit_workflow_frameset.php?pnt=log&art=" . $docID . "&type=" . $type . "','workflow_history',-1,-1,640,480,true,false,true);");
	}

}
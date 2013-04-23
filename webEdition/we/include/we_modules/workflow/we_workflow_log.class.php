<?php

/**
 * webEdition CMS
 *
 * $Rev: 4630 $
 * $Author: mokraemer $
 * $Date: 2012-06-29 20:03:22 +0200 (Fri, 29 Jun 2012) $
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
class we_workflow_log{
	const TYPE_APPROVE=1;
	const TYPE_APPROVE_FORCE=2;
	const TYPE_DECLINE=3;
	const TYPE_DECLINE_FORCE=4;
	const TYPE_DOC_FINISHED=5;
	const TYPE_DOC_FINISHED_FORCE=6;
	const TYPE_DOC_INSERTED=7;
	const TYPE_DOC_REMOVED=8;
	const NUMBER_LOGS=8;

	function logDocumentEvent($workflowDocID, $userID, $type, $description){
		$db = new DB_WE();
		$db->query("INSERT INTO " . WORKFLOW_LOG_TABLE . " (ID, RefID, docTable, userID, logDate, Type, Description) VALUES ('', " . intval($workflowDocID) . ", '" . WORKFLOW_TABLE . "', " . intval($userID) . ", UNIX_TIMESTAMP(), " . intval($type) . ", '" . $db->escape($description) . "');");
	}

	function logWorkflowEvent($workflowID, $userID, $type, $description){
		$db = new DB_WE();
		$db->query("INSERT INTO " . WORKFLOW_LOG_TABLE . " (ID, RefID, docTable, userID, logDate, Type, Description) VALUES ('', " . intval($workflowDocID) . ", '" . WORKFLOW_TABLE . "', " . intval($userID) . ", UNIX_TIMESTAMP(), " . intval($type) . ", '" . $db->escape($description) . "');");
	}

	static function getLogForDocument($docID, $order="DESC", $wfType=0){

		$offset = isset($_REQUEST["offset"]) ? abs($_REQUEST["offset"]) : 0;
		$db = new DB_WE();
		$q = "SELECT " . WORKFLOW_LOG_TABLE . ".* FROM " . WORKFLOW_LOG_TABLE . "," . WORKFLOW_DOC_TABLE . "," . WORKFLOW_TABLE . " WHERE " . WORKFLOW_DOC_TABLE . ".workflowID=" . WORKFLOW_TABLE . ".ID AND " . WORKFLOW_TABLE . ".Type IN(" . $wfType . ") AND " . WORKFLOW_LOG_TABLE . ".RefID=" . WORKFLOW_DOC_TABLE . ".ID AND  " . WORKFLOW_DOC_TABLE . ".documentID=" . intval($docID) . " ORDER BY " . WORKFLOW_LOG_TABLE . ".logDate " . $db->escape($order) . ",ID DESC";


		$db->query($q);

		$GLOBALS["ANZ_LOGS"] = $db->num_rows();

		$q .= " LIMIT $offset," . self::NUMBER_LOGS;
		$db->query($q);

		$hash = array();
		while($db->next_record())
			$hash[] = $db->Record;
		foreach($hash as $k => $v){
			switch($hash[$k]["Type"]){
				case self::TYPE_APPROVE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_approve]');
					break;
				case self::TYPE_APPROVE_FORCE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_approve_force]');
					break;
				case self::TYPE_DECLINE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_decline]');
					break;
				case self::TYPE_DECLINE_FORCE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_decline_force]');
					break;
				case self::TYPE_DOC_FINISHED:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_doc_finished]');
					break;
				case self::TYPE_DOC_FINISHED_FORCE:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_doc_finished_force]');
					break;
				case self::TYPE_DOC_INSERTED:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_insert_doc]');
					break;
				case self::TYPE_DOC_REMOVED:
					$hash[$k]["Type"] = g_l('modules_workflow', '[log_remove_doc]');
					break;
			}
		}
		return $hash;
	}

	function getLogForUser($userID){
		$db = new DB_WE();
		$db->query("SELECT * FROM " . WORKFLOW_LOG_TABLE . " WHERE userID=" . intval($userID));
		return $db->Record;
	}

	function clearLog($stamp=0){
		$db = new DB_WE();
		$db->query("DELETE FROM " . WORKFLOW_LOG_TABLE . " " . ($stamp ? "WHERE logDate<" . intval($stamp) : "") . ";");
	}

}

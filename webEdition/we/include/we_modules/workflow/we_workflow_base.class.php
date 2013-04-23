<?php

/**
 * webEdition CMS
 *
 * $Rev: 5039 $
 * $Author: mokraemer $
 * $Date: 2012-10-31 01:13:32 +0100 (Wed, 31 Oct 2012) $
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
include_once(WE_MESSAGING_MODULE_PATH . "messaging_interfaces.inc.php");

/**
 * Document Definition base class
 */
class we_workflow_base{

	var $uid;
	var $db;
	var $persistents = array();
	var $table = "";
	var $ClassName = __CLASS__;
	var $Log;

	function __construct(){
		$this->uid = "wf_" . md5(uniqid(__FILE__, true));
		$this->db = new DB_WE();
		$this->Log = new we_workflow_log();
	}

	function load(){
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query("SELECT * FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->ID));
		if($this->db->next_record())
			foreach($tableInfo as $cur){
				$fieldName = $cur["name"];
				if(in_array($fieldName, $this->persistents)){
					$this->$fieldName = $this->db->f($fieldName);
				}
			}
	}

	function save(){
		$sets = array();
		$wheres = array();
		foreach($this->persistents as $val){
			if($val == "ID"){
				$wheres[] = $val . '="' . $this->{$val} . '"';
			}
			$sets[$val] =$this->{$val};
		}
		$where = implode(',', $wheres);
		$set = we_database_base::arraySetter($sets);

		if($this->ID == 0){
			$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . $set);
			# get ID #
			$this->ID = $this->db->getInsertId();
		} else{
			$this->db->query('UPDATE ' . $this->db->escape($this->table) . ' SET ' . $set . ' WHERE ' . $where);
		}
	}

	function delete(){
		if($this->ID){
			$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->ID));
			return true;
		}
		return false;
	}

	function sendMessage($userID, $subject, $description){
		$errs = array();
		$foo = f("SELECT username FROM " . USER_TABLE . " WHERE ID=" . intval($userID), "username", $this->db);
		$rcpts = array($foo); /* user names */
		msg_new_message($rcpts, $subject, $description, $errs);
	}

	function sendMail($userID, $subject, $description, $contecttype = 'text/plain'){
		$errs = array();
		$foo = f("SELECT Email FROM " . USER_TABLE . " WHERE ID=" . intval($userID), "Email", $this->db);
		if(!empty($foo) && we_check_email($foo)){
			$this_user = getHash("SELECT First,Second,Email FROM " . USER_TABLE . " WHERE ID=" . intval($_SESSION["user"]["ID"]), $this->db);
			we_mail($foo, correctUml($subject), $description, (isset($this_user["Email"]) && $this_user["Email"] != "" ? $this_user["First"] . " " . $this_user["Second"] . " <" . $this_user["Email"] . ">" : ""));
		}
	}

	function sendTodo($userID, $subject, $description, $deadline){
		$errs = array();
		$foo = f("SELECT username FROM " . USER_TABLE . " WHERE ID=" . intval($userID), "username", $this->db);
		$rcpts = array($foo); /* user names */
		return msg_new_todo($rcpts, $subject, $description, $errs, "html", $deadline);
	}

	function doneTodo($id){
		$errs = '';
		return msg_done_todo($id, $errs);
	}

	function removeTodo($id){
		return msg_rm_todo($id);
	}

	function rejectTodo($id){
		return msg_reject_todo($id);
	}

}
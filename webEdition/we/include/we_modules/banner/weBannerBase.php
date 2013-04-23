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

/**
 * Document Definition base class
 */
abstract class weBannerBase{

	var $uid;
	var $db;
	var $persistents = array();
	var $table = "";
	var $ClassName = __CLASS__;

	protected function __construct(){
		$this->uid = "ba_" . md5(uniqid(__FILE__, true));
		$this->db = new DB_WE();
	}

	public function load(){
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query('SELECT * FROM ' . $this->table . ' WHERE ID=' . intval($this->ID));
		if($this->db->next_record())
			foreach($tableInfo as $cur){
				$fieldName = $cur["name"];
				if(in_array($fieldName, $this->persistents)){
					$foo = $this->db->f($fieldName);
					$this->{$fieldName} = $foo;
				}
			}
	}

	public function save(){
		$sets = array();
		$wheres = array();
		foreach($this->persistents as $key => $val){
			if($val == "ID"){
				eval('$wheres[]="' . $val . '=\'".$this->' . $val . '."\'";');
			}
			eval('$sets[]="' . $val . '=\'".$this->' . $val . '."\'";');
		}
		$where = implode(",", $wheres);
		$set = implode(",", $sets);
		if($this->ID == 0){
			$this->db->query('INSERT INTO ' . $this->table . ' SET ' . $set);
			# get ID #
			$this->db->query("SELECT LAST_INSERT_ID()");
			$this->db->next_record();
			$this->ID = $this->db->f(0);
		} else{
			$query = 'UPDATE ' . $this->table . ' SET ' . $set . ' WHERE ' . $where;
			$this->db->query($query);
		}
	}

	public function delete(){
		if(!$this->ID){
			return false;
		}
		$this->db->query('DELETE FROM ' . $this->table . ' WHERE ID=' . intval($this->ID));
		return true;
	}

}

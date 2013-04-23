<?php

/**
 * webEdition CMS
 *
 * $Rev: 4258 $
 * $Author: mokraemer $
 * $Date: 2012-03-11 21:10:50 +0100 (Sun, 11 Mar 2012) $
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
abstract class logging{

	public $db;
	public $table;
	public $userID;
	public $timestamp;
	public $persistent_slots = array();

	function __construct($_table){

		$this->db = new DB_WE();
		$this->userID = $_SESSION['user']['ID'];
		$this->timestamp = time();
		$this->table = $_table;
		$this->loadPresistents();
	}

	function loadPresistents(){

		$tableInfo = $this->db->metadata($this->table);
		foreach($tableInfo as $t){
			$columnName = $t["name"];
			$this->persistent_slots[] = $columnName;
			if(!isset($this->$columnName))
				$this->$columnName = "";
		}
	}

	function load(){

		$content = array();
		$tableInfo = $this->db->metadata($this->table);
		$this->db->query("SELECT ID,timestamp,action,userID FROM " . $this->db->escape($this->table) . " ORDER BY timestamp DESC");
		$m = 0;
		while($this->db->next_record()) {
			for($i = 0; $i < count($tableInfo); $i++){
				$columnName = $tableInfo[$i]["name"];
				if(in_array($columnName, $this->persistent_slots)){
					$content[$m][$columnName] = $this->db->f($columnName);
				}
			}
			$m++;
		}

		return $content;
	}

	function saveLog(){

		$keys = array();
		$values = array();

		foreach($this->persistent_slots as $key => $val){

			if(isset($this->$val)){
				$keys[] = '`' . $val . '`';
				$values[] = "'" . ($this->$val) . "'";
			}
		}


		$keys = implode(",", $keys);
		$values = implode(",", $values);

		if(!empty($keys) && !empty($values)){

			$query = 'INSERT INTO ' . $this->db->escape($this->table) . ' (' . $keys . ') VALUES (' . $values . ')';
			$this->db->query($query);
		}
	}

}

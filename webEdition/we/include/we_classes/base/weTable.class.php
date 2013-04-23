<?php

/**
 * webEdition CMS
 *
 * $Rev: 5965 $
 * $Author: mokraemer $
 * $Date: 2013-03-16 17:28:12 +0100 (Sat, 16 Mar 2013) $
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
 * Class weTable
 *
 * Provides functions for loading and saving db tables.
 */
class weTable{

	var $ClassName = __CLASS__;
	var $db;
	var $table = "";
	var $elements;
	var $persistent_slots = array();
	var $attribute_slots = array();

	function __construct($table, $force_columns = false){
		$this->db = new DB_WE();
		$this->table = $table;
		$this->elements = array();

		$this->attribute_slots["name"] = stripTblPrefix($table);

		$update_table = true;

		if(defined("OBJECT_X_TABLE") && !$force_columns){
			if(strtolower(substr($table, 0, 10)) == strtolower(stripTblPrefix(OBJECT_X_TABLE)))
				$update_table = false;
		}

		if(defined("CUSTOMER_TABLE") && !$force_columns){
			if(strtolower($table) == strtolower(CUSTOMER_TABLE))
				$update_table = false;
		}

		if($update_table)
			$this->getColumns();
	}

	function getColumns(){
		if($this->db->isTabExist($this->table)){
			$this->db->query("SHOW COLUMNS FROM $this->table;");
			while($this->db->next_record()) {
				$this->elements[$this->db->f("Field")] = array(
					"Field" => $this->db->f("Field"),
					"Type" => $this->db->f("Type"),
					"Null" => $this->db->f("Null"),
					"Key" => $this->db->f("Key"),
					"Default" => $this->db->f("Default"),
					"Extra" => $this->db->f("Extra")
				);
			}
		}

		$this->fetchNewColumns();
	}

	function save(){
		if(!(isset($_SESSION['weS']['weBackupVars']['tablekeys']) && is_array($_SESSION['weS']['weBackupVars']['tablekeys']))){
			$_SESSION['weS']['weBackupVars']['tablekeys'] = array();
		}
		$_SESSION['weS']['weBackupVars']['tablekeys'][$this->table] = $this->db->getTableKeyArray($this->table);
		$this->db->delTable($this->table);
		$cols = array();
		$keys = array();

		foreach($this->elements as $element){

			$_defalut_for_type = stripos($element["Type"], 'int') !== false || stripos($element["Type"], 'double') !== false || stripos($element["Type"], 'float') !== false ? 0 : "''";

			$_default_value = ("DEFAULT " . ((isset($element["Default"]) && $element["Default"] != "") ? ("'" . $element["Default"] . "'") : ((isset($element["Null"]) && $element["Null"] == "YES") ? "NULL" : $_defalut_for_type)));

			$cols[$element["Field"]] = $element["Type"] . " " . ((isset($element["Null"]) && $element["Null"] == "YES") ? "NULL " : "NOT NULL ") . ((isset($element["Extra"]) && strtolower($element["Extra"]) != "auto_increment") ? $_default_value : "") . " " . ((isset($element["Extra"])) ? $element["Extra"] : '');

			if(isset($element["Key"]) && $element["Key"]){
				if($element["Key"] == "PRI")
					$keys[] = "PRIMARY KEY (" . $element["Field"] . ")";
			}
		}

		if(!empty($cols)){
			return $this->db->addTable($this->table, $cols, $keys);
		}

		return false;
	}

	// add new fields to the table before import
	function fetchNewColumns(){
		// fix for bannerclicks table - primary key has been added
		if(defined('BANNER_CLICKS_TABLE') && $this->table == BANNER_CLICKS_TABLE){
			if(!isset($this->elements['clickid'])){
				$this->elements['clickid'] = array(
					'Field' => 'clickid',
					'Type' => 'BIGINT',
					'Null' => 'NO',
					'Key' => 'PRI',
					'Default' => '',
					'Extra' => 'auto_increment'
				);
			}
		}
		// fix for bannerviews table - primary key has been added
		if(defined('BANNER_VIEWS_TABLE') && $this->table == BANNER_VIEWS_TABLE){
			if(!isset($this->elements['viewid'])){
				$this->elements['viewid'] = array(
					'Field' => 'viewid',
					'Type' => 'BIGINT',
					'Null' => 'NO',
					'Key' => 'PRI',
					'Default' => '',
					'Extra' => 'auto_increment'
				);
			}
		}
	}

}

class weTableAdv extends weTable{

	var $ClassName = __CLASS__;

	function __construct($table, $force_columns = false){
		parent::__construct($table, $force_columns);
	}

	function getColumns(){
		if($this->db->isTabExist($this->table)){
			$this->db->query("SHOW CREATE TABLE $this->table;");
			if($this->db->next_record()){
				$zw = explode("\n", $this->db->f("Create Table"));
				if(TBL_PREFIX != ''){
					$zw[0] = str_replace($this->table, stripTblPrefix($this->table), $zw[0]);
				}
			}
			$this->elements[$this->db->f("Table")] = array('Field' => 'create');
			foreach($zw as $k => $v){
				$this->elements[$this->db->f("Table")]['line' . $k] = $v;
			}
		}
		//$this->fetchNewColumns();
	}

	function save(){
		global $DB_WE;
		if(!(isset($_SESSION['weS']['weBackupVars']['tablekeys']) && is_array($_SESSION['weS']['weBackupVars']['tablekeys']))){
			$_SESSION['weS']['weBackupVars']['tablekeys'] = array();
		}
		if(isset($_SESSION['weS']['weBackupVars']['options']['convert_charset']) && $_SESSION['weS']['weBackupVars']['options']['convert_charset']){
			$doConvert = true;
			$searchArray = array('CHARACTER SET latin1', 'COLLATE latin1_bin', 'COLLATE latin1_danish_ci', 'COLLATE latin1_general_ci', 'COLLATE latin1_general_cs', 'COLLATE latin1_german1_ci', 'COLLATE latin1_german2_ci', 'COLLATE latin1_spanish_ci', 'COLLATE latin1_swedish_ci');
		} else{
			$doConvert = false;
		}
		if($this->db->isTabExist($this->table)){
			$_SESSION['weS']['weBackupVars']['tablekeys'][$this->table] = $this->db->getTableKeyArray($this->table);
			$this->db->delTable($this->table);
		}
		$myarray = $this->elements['create'];
		unset($myarray['Field']);
		foreach($myarray as &$cur){
			if(substr($cur, 0, 6) == 'CREATE'){
				//Regex because of backups <6.2.4
				$cur = preg_replace('/(CREATE *\w* *`?)\w*' . stripTblPrefix($this->table) . '/i', '\\1' . $this->table, $cur, 1);
			}
			if($doConvert){
				$cur = str_replace($searchArray, '', $cur);
			}
		}

		//FIXME: this is NOT Save for MySQL Updates!!!!
		array_pop($myarray); //get rid of old Engine statement
		$myarray[] = ' ) ' . we_database_base::getCharsetCollation() . ' ENGINE=MyISAM;';

		$query = implode(' ', $myarray);
		return ($DB_WE->query($query));
	}

}

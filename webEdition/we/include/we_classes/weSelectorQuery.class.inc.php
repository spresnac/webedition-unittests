<?php

/**
 * webEdition CMS
 *
 * $Rev: 5777 $
 * $Author: mokraemer $
 * $Date: 2013-02-09 19:04:18 +0100 (Sat, 09 Feb 2013) $
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
 * @name we_selectorQuery
 */
class weSelectorQuery{
	/*	 * ***********************************************************************
	 * VARIABLES
	 * *********************************************************************** */

	private $db;
	var $result = array();
	var $fields;
	var $condition = array();

	/*	 * ***********************************************************************
	 * CONSTRUCTOR
	 * *********************************************************************** */

	/**
	 * Constructor of class
	 *
	 * @return weSelectorQuery
	 */
	function __construct(){
		$this->db = new DB_WE();
		$this->fields = array('ID', 'Path');
	}

	/**
	 * query
	 * Die Funktion 'query' f�hrt die Abfrage f�r den �bergebenen Selektor-Typ durch.
	 * Mit dem Parameter 'limit' kann man die Anzahl der Suchergebnisse begrenzen.
	 *
	 * @param search
	 * @param table
	 * @param types
	 * @param limit
	 *
	 * @return void
	 */
	function queryTable($search, $table, $types = null, $limit = null){
		$search = strtr($search, array('[' => '\\[', ']' => '\\]'));
		$userExtraSQL = $this->getUserExtraQuery($table);

		switch($table){
			case USER_TABLE:
				$this->addQueryField("IsFolder");
				$typeField = "Type";
				break;
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				$typeField = "ContentType";
				$userExtraSQL = '';
				break;
			case CATEGORY_TABLE:
			case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : ""):
				break;
			default:
				$typeField = "ContentType";
		}

		$where = "Path = '" . $this->db->escape($search) . "'";
		$isFolder = 1;
		$addCT = 0;

		if(isset($types) && is_array($types)){
			for($i = 0; $i < count($types); $i++){
				if($types[$i] != ""){
					$types[$i] = str_replace(" ", "", $types[$i]);
					if($types[$i] == "folder"){
						$where .= ($i < 1 ? " AND (" : " OR ") . "IsFolder=1";
					} elseif(isset($typeField) && $typeField != ""){
						$where .= ($i < 1 ? " AND (" : " OR ") . "$typeField='" . $this->db->escape($types[$i]) . "'";
						$isFolder = 0;
						$addCT = 1;
					}
					$where .= $i == (count($types) - 1) ? ")" : "";
				}
			}
		}
		if($addCT){
			$this->addQueryField($typeField);
		}
		if(!empty($userExtraSQL)){
			$where .= $userExtraSQL;
		}

		if(count($this->condition) > 0){
			foreach($this->condition as $val){
				$where .= ' ' . $val['queryOperator'] . " " . $val['field'] . $val['conditionOperator'] . "'" . $val['value'] . "'";
			}
		}

		$order = 'ORDER BY ' . ($isFolder ? "Path" : "isFolder  ASC, Path") . ' ASC ';
		$fields = implode(', ', $this->fields);
		$this->db->query("SELECT $fields FROM " . $this->db->escape($table) . " WHERE $where $order" . ($limit ? " LIMIT $limit" : ""));
	}

	/**
	 * search
	 * Die Funktion 'search' f�hrt die Suche nach den Anfangszeichen f�r den �bergebenen Selektor-Typ durch.
	 * Mit dem Parameter 'limit' kann man die Anzahl der Suchergebnisse begrenzen.
	 *
	 * @param search
	 * @param table
	 * @param types
	 * @param limit
	 *
	 * @return void
	 */
	function search($search, $table, $types = null, $limit = null, $rootDir = ""){
		$search = strtr($search, array("[" => "\\\[", "]" => "\\\]"));
		$userExtraSQL = $this->getUserExtraQuery($table);
		switch($table){
			case USER_TABLE:
				$this->addQueryField("IsFolder");
				$typeField = "Type";
				break;
			case (defined('CUSTOMER_TABLE') ? CUSTOMER_TABLE : 'CUSTOMER_TABLE'):
				$typeField = "ContentType";
				$userExtraSQL = '';
				break;
			case CATEGORY_TABLE:
			case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : ""):
				break;
			default:
				$typeField = "ContentType";
		}

		$where = "Path REGEXP '^" . preg_quote(preg_quote($search)) . "[^/]*$'" . (isset($rootDir) && !empty($rootDir) ? " AND  (Path LIKE '" . $this->db->escape($rootDir) . "' OR Path LIKE '" . $this->db->escape($rootDir) . "%')" : "");
		$isFolder = 1;
		$addCT = 0;

		if(isset($types) && is_array($types)){
			$types = array_unique($types);
			for($i = 0; $i < count($types); $i++){
				if($types[$i] != ""){
					$types[$i] = str_replace(" ", "", $types[$i]);
					if($types[$i] == "folder"){
						$where .= ($i < 1 ? ' AND (' : ' OR ') . 'IsFolder=1';
					} elseif(isset($typeField) && $typeField != ""){
						$where .= ($i < 1 ? " AND (" : " OR ") . "$typeField='" . $this->db->escape($types[$i]) . "'";
						$isFolder = 0;
						$addCT = 1;
					}
					$where .= $i == (count($types) - 1) ? ')' : '';
				}
			}
		}
		if($addCT){
			$this->addQueryField($typeField);
		}
		$where .= $userExtraSQL;

		if(count($this->condition) > 0){
			foreach($this->condition as $val){
				$where .= ' ' . $val['queryOperator'] . " " . $val['field'] . $val['conditionOperator'] . "'" . $this->db->escape($val['value']) . "'";
			}
		}

		$this->db->query('SELECT ' . implode(", ", $this->fields) . ' FROM ' . $this->db->escape($table) . ' WHERE ' . $where . ' ORDER BY ' . ($isFolder ? 'Path' : 'isFolder  ASC, Path') . ' ASC ' . ($limit ? ' LIMIT ' . $limit : ''));
	}

	/**
	 * Returns all entries of a folder, depending on the contenttype.
	 *
	 * @param integer $id
	 * @param string $table
	 * @param array $types
	 * @param integer $limit
	 */
	function queryFolderContents($id, $table, $types = null, $limit = null){
		$userExtraSQL = $this->getUserExtraQuery($table);
		if(is_array($types) && $table != CATEGORY_TABLE){
			$this->addQueryField('ContentType');
		}

		$this->addQueryField("Text");
		$this->addQueryField("ParentID");

		// deal with contenttypes
		$ctntQuery = ' OR ( 0 ';
		if($types){
			foreach($types as $type){
				$ctntQuery .= ' OR ContentType = "' . $type . '"';
			}
		}
		$ctntQuery .= ' ) ';
		if($table == CATEGORY_TABLE){
			$ctntQuery = '';
		}

		$this->db->query('SELECT ' . implode(',', $this->fields) . ' FROM ' . $this->db->escape($table) . ' WHERE ParentID = ' . intval($id) . ' AND ( IsFolder = 1 ' . $ctntQuery . ' ) ' .
			$userExtraSQL . ' ORDER BY IsFolder DESC, Path ');
	}

	/**
	 * returns single item by id
	 *
	 * @param integer $id
	 * @param string $table
	 */
	function getItemById($id, $table, $fields = "", $useExtraSQL = true){
		$_votingTable = defined('VOTING_TABLE') ? VOTING_TABLE : "";
		switch($table){
			case $_votingTable:
				$useCreatorID = false;
				break;
			default:
				$useCreatorID = true;
		}

		$userExtraSQL = (!defined('BANNER_TABLE') || $table != BANNER_TABLE ?
				($useExtraSQL ? $this->getUserExtraQuery($table, $useCreatorID) : '') : '');

		$this->addQueryField("Text");
		$this->addQueryField("ParentID");
		if(is_array($fields)){
			foreach($fields as $val){
				$this->addQueryField($val);
			}
		}
		$this->db->query('SELECT ' . implode(',', $this->fields) . ' FROM ' . $this->db->escape($table) . " WHERE ID = " . intval($id) . ' ' . $userExtraSQL);
		return $this->getResult();
	}

	/**
	 * returns single item by id
	 *
	 * @param integer $id
	 * @param string $table
	 */
	function getItemByPath($path, $table, $fields = ""){
		$userExtraSQL = $this->getUserExtraQuery($table);

		$this->addQueryField("Text");
		$this->addQueryField("ParentID");
		if(is_array($fields)){
			foreach($fields as $val){
				$this->addQueryField($val);
			}
		}
		$this->db->query('SELECT ' . implode(',', $this->fields) . ' FROM ' . $this->db->escape($table) . ' WHERE	Path = "' . $this->db->escape($path) . '" ' . $userExtraSQL);
		return $this->getResult();
	}

	/**
	 * getResult
	 * Liefert das komplette Erg�bnis der Abfrage als Hash mit den Feldnamen als Spalten.
	 * @return Array
	 */
	function getResult(){
		$i = 0;
		$result = array();
		while($this->db->next_record()) {
			foreach($this->fields as $val){
				$result[$i][$val] = htmlspecialchars_decode($this->db->f($val));
			}
			$i++;
		}
		return $result;
	}

	/**
	 * addQueryField
	 * F�gt den �bergebenen String zur Liste der gesuchten Felder hinzu.
	 * @param field
	 * @return void
	 */
	function addQueryField($field){
		$this->fields[] = $field;
	}

	/**
	 * delQueryField
	 * Entfernt den �bergebenen String von der Liste der gesuchten Felder.
	 * @param field
	 * @return void
	 */
	function delQueryField($field){
		foreach($this->fields as $key => $val){
			if($val == $field)
				unset($this->fields[$key]);
		}
	}

	/**
	 * addCondition
	 * F�gt die �bergeben Abfragebedingung hinzu.
	 * @param array $condition
	 */
	function addCondition($condition){
		if(is_array($condition)){
			$arrayIndex = count($this->condition);
			$this->condition[$arrayIndex]['queryOperator'] = $condition[0];
			$this->condition[$arrayIndex]['conditionOperator'] = $condition[1];
			$this->condition[$arrayIndex]['field'] = $condition[2];
			$this->condition[$arrayIndex]['value'] = $condition[3];
		}
	}

	/**
	 * getUserExtraQuery
	 * Erzeugt ein Bedingungen zur Filterung der Arbeitsbereiche
	 * @param string $table
	 * @return string
	 */
	function getUserExtraQuery($table){
		if((defined('NAVIGATION_TABLE') && $table == NAVIGATION_TABLE) || (defined('BANNER_TABLE') && $table == BANNER_TABLE) || $table == CATEGORY_TABLE){
			return '';
		}
		$userExtraSQL = ' AND((1 ' . makeOwnersSql(false) . ') ';

		if(get_ws($table)){
			$userExtraSQL .= getWsQueryForSelector($table);
		} else if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE && (!$_SESSION["perms"]["ADMINISTRATOR"])){
			$wsQuery = "";
			$ac = getAllowedClasses($this->db);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				$wsQuery .= ' Path LIKE "' . $this->db->escape($path) . '/%" OR Path="' . $this->db->escape($path) . '" OR ';
			}
			if($wsQuery){
				$userExtraSQL .= ' AND (' . substr($wsQuery, 0, strlen($wsQuery) - 3) . ')';
			}
		} else{
			if($table != USER_TABLE){
				$userExtraSQL.=' OR RestrictOwners=0 ';
			}
		}
		return $userExtraSQL . ')';
	}

}

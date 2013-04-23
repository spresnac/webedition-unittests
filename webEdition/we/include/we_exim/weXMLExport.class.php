<?php

/**
 * webEdition CMS
 *
 * $Rev: 5934 $
 * $Author: mokraemer $
 * $Date: 2013-03-09 20:06:43 +0100 (Sat, 09 Mar 2013) $
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
class weXMLExport extends weXMLExIm{

	var $db;
	var $prepare = true;

	function __construct(){
		$this->RefTable = new RefTable();
	}

	function setRefTable(&$rTable){
		$this->RefTable = $rTable;
	}

	function export($id, $ct, $fname, $table = "", $export_binary = true, $compression = ""){
		@set_time_limit(0);
		$doc = weContentProvider::getInstance($ct, $id, $table);
		// add binary data separately to stay compatible with the new binary feature in v5.1
		if(isset($doc->ContentType) && (
			strpos($doc->ContentType, "image/") === 0 ||
			strpos($doc->ContentType, "application/") === 0 ||
			strpos($doc->ContentType, "video/") === 0)){
			$doc->setElement("data", weFile::load($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $doc->Path));
		}

		$fh = fopen($fname, 'ab');
		if(!$fh){
			return -1;
		}

		$params = array();
		if(isset($doc->ID)){
			$params["ID"] = $doc->ID;
		}
		if(isset($doc->ContentType)){
			$params["ContentType"] = $doc->ContentType;
		} elseif(isset($doc->Table) && $doc->Table == DOC_TYPES_TABLE){
			$params["ContentType"] = "doctype";
		}

		$this->RefTable->setProp($params, "Eximed", 1);

		$classname = (isset($doc->Pseudo) ? $doc->Pseudo : $doc->ClassName);

		if($classname == "weBinary" && !is_numeric($id)){
			$doc->Path = $doc->ID;
			$doc->ID = 0;
		}

		$attribute = (isset($doc->attribute_slots) ? $doc->attribute_slots : array());

		switch($classname){
			case "weTable":
				if((defined("OBJECT_X_TABLE") && strtolower(substr($doc->table, 0, 10)) == strtolower(stripTblPrefix(OBJECT_X_TABLE))) ||
					defined("CUSTOMER_TABLE")){
					$doc->getColumns();
				}
				break;
			case "weBinary":
				weContentProvider::binary2file($doc, $fh);
				break;
		}

		if($classname != "weBinary"){
			weContentProvider::object2xml($doc, $fh, $attribute);
		}

		fwrite($fh, weBackup::backupMarker . "\n");

		if($classname == "weTableItem" && $export_binary &&
			strtolower($doc->table) == strtolower(FILE_TABLE) &&
			($doc->ContentType == "image/*" || stripos($doc->ContentType, "application/") !== false)){
			$bin = weContentProvider::getInstance("weBinary", $doc->ID);
			$attribute = (isset($bin->attribute_slots) ? $bin->attribute_slots : array());
			weContentProvider::binary2file($bin, $fh);
		}

		fclose($fh);
		unset($doc);
	}

	function getSelectedItems($selection, $extype, $art, $type, $doctype, $classname, $categories, $dir, &$selDocs, &$selTempl, &$selObjs, &$selClasses){
		$this->db = new DB_WE();
		if($selection == "manual"){
			if($extype == "wxml"){
				$selDocs = array_unique($this->getIDs($selDocs, FILE_TABLE, false));
				$selTempl = array_unique($this->getIDs($selTempl, TEMPLATES_TABLE, false));
				$selObjs = defined("OBJECT_FILES_TABLE") ? array_unique($this->getIDs($selObjs, OBJECT_FILES_TABLE, false)) : "";
				$selClasses = defined("OBJECT_FILES_TABLE") ? array_unique($this->getIDs($selClasses, OBJECT_TABLE, false)) : "";
			} else{
				switch($art){
					case "docs":
						$selDocs = $this->getIDs($selDocs, FILE_TABLE);
						break;
					case "objects":
						$selObjs = defined("OBJECT_FILES_TABLE") ? $this->getIDs($selObjs, OBJECT_FILES_TABLE) : "";
						break;
				}
			}
		} elseif($type == "doctype"){
			$cat_sql = ($categories ? we_category::getCatSQLTail('', FILE_TABLE, true, $this->db, 'Category', true, $categories) : '');
			if($dir != 0){
				$workspace = id_to_path($dir, FILE_TABLE, $this->db);
				$ws_where = ' AND (' . FILE_TABLE . ".Path LIKE '" . $this->db->escape($workspace) . "/%' OR " . FILE_TABLE . ".Path='" . $this->db->escape($workspace) . "')";
			} else{
				$ws_where = '';
			}

			$this->db->query('SELECT distinct ID FROM ' . FILE_TABLE . ' WHERE 1 ' . $ws_where . '  AND ' . FILE_TABLE . '.IsFolder=0 AND ' . FILE_TABLE . '.DocType="' . $this->db->escape($doctype) . '"' . $cat_sql);
			$selDocs = $this->db->getAll(true);
		} elseif(defined("OBJECT_FILES_TABLE")){
			$where = $this->queryForAllowed(OBJECT_FILES_TABLE);
			$cat_sql = ' ' . ($categories ? we_category::getCatSQLTail('', OBJECT_FILES_TABLE, true, $db, 'Category', true, $categories) : '');

			$this->db->query('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND TableID=' . intval($classname) . $cat_sql . $where);
			$selObjs = $this->db->getAll(true);
		}

		foreach($selDocs as $k => $v){
			$this->RefTable->add2(array(
				"ID" => $v,
				"ContentType" => f('Select ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($v), "ContentType", $this->db),
				"level" => 0
				)
			);
		}

		foreach($selTempl as $k => $v){
			$this->RefTable->add2(array(
				"ID" => $v,
				"ContentType" => "text/weTmpl",
				"level" => 0
				)
			);
		}
		if(is_array($selObjs)){
			foreach($selObjs as $k => $v){
				$this->RefTable->add2(array(
					"ID" => $v,
					"ContentType" => "objectFile",
					"level" => 0
					)
				);
			}
		}
		if(is_array($selClasses)){
			foreach($selClasses as $k => $v){
				$this->RefTable->add2(array(
					"ID" => $v,
					"ContentType" => "object",
					"level" => 0
				));
			}
		}
	}

	function queryForAllowed($table){
		$db = new DB_WE();
		$parentpaths = array();
		$wsQuery = '';
		if(($ws = get_ws($table))){
			$wsPathArray = id_to_path($ws, $table, $db, false, true);
			foreach($wsPathArray as $path){
				if($wsQuery != ''){
					$wsQuery .=' OR ';
				}
				$wsQuery .= " Path LIKE '" . $db->escape($path) . "/%' OR " . weXMLExIm::getQueryParents($path);
				while($path != "/" && $path) {
					$parentpaths[] = $path;
					$path = dirname($path);
				}
			}
		} else if(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE && (!$_SESSION["perms"]["ADMINISTRATOR"])){
			$ac = getAllowedClasses($db);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				if($wsQuery != '')
					$wsQuery .=' OR ';
				$wsQuery .= " Path LIKE '" . $db->escape($path) . "/%' OR Path='" . $db->escape($path) . "'";
			}
		}

		return makeOwnersSql() . ( $wsQuery ? 'OR (' . $wsQuery . ')' : '');
	}

	function getIDs($selIDs, $table, $with_dirs = false){
		$ret = array();
		$tmp = array();
		$db = new DB_WE();
		$allow = $this->queryForAllowed($table);
		foreach($selIDs as $v){
			if($v){
				$isfolder = f('SELECT IsFolder FROM ' . $db->escape($table) . ' WHERE ID=' . intval($v), "IsFolder", $db);
				if($isfolder){
					we_readChilds($v, $tmp, $table, false, $allow);
					if($with_dirs){
						$tmp[] = $v;
					}
				} else{
					$tmp[] = $v;
				}
			}
		}
		if($with_dirs){
			return $tmp;
		}
		foreach($tmp as $v){
			$isfolder = f('SELECT IsFolder FROM ' . $db->escape($table) . ' WHERE ID=' . intval($v), 'IsFolder', new DB_WE());
			if(!$isfolder){
				$ret[] = $v;
			}
		}
		return $ret;
	}

	function prepareExport(){
		//$this->RefTable = new RefTable();
		$_preparer = new weExportPreparer($this->options, $this->RefTable);
		$_preparer->prepareExport();
	}

	function exportInfoMap($info){
		$out = '<we:info>';
		foreach($info as $inf){
			$out.='<we:map';
			foreach($inf as $key => $value){
				$out.=' ' . $key . '="' . $value . '"';
			}
			$out.='></we:map>';
		}
		$out.='</we:info>' .
			weBackup::backupMarker . "\n";
		return $out;
	}

	function loadPerserves(){
		parent::loadPerserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			$this->prepare = $_SESSION['weS']['ExImPrepare'];
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			$this->options = $_SESSION['weS']['ExImOptions'];
		}
	}

	function savePerserves(){
		parent::savePerserves();
		$_SESSION['weS']['ExImPrepare'] = $this->prepare;
		$_SESSION['weS']['ExImOptions'] = $this->options;
	}

	function unsetPerserves(){
		parent::unsetPerserves();
		if(isset($_SESSION['weS']['ExImPrepare'])){
			unset($_SESSION['weS']['ExImPrepare']);
		}
		if(isset($_SESSION['weS']['ExImOptions'])){
			unset($_SESSION['weS']['ExImOptions']);
		}
	}

}
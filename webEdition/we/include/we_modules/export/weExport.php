<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
 * General Definition of WebEdition Export
 *
 */
class weExport extends weModelBase{

	//properties
	var $ID;
	var $Text;
	var $ParentID;
	var $Icon;
	var $IsFolder;
	var $Path;
	var $ExportTo; // local | server
	var $ServerPath;
	var $Filename;
	var $Selection = 'auto'; // auto | manual
	var $SelectionType = 'doctype'; // doctype | classname
	var $DocType;
	var $Folder;
	var $ClassName;
	var $Categorys;
	var $selDocs;
	var $selTempl;
	var $selObjs;
	var $selClasses;
	var $HandleDefTemplates;
	var $HandleDocIncludes;
	var $HandleObjIncludes;
	var $HandleDocLinked;
	var $HandleDefClasses;
	var $HandleObjEmbeds;
	var $HandleDoctypes;
	var $HandleCategorys;
	var $HandleOwners;
	var $HandleNavigation;
	var $HandleThumbnails;
	var $ExportDepth;
	var $Log = array();
	var $ExportFilename;
	var $protected = array("ID", "ParentID", "Icon", "IsFolder", "Path", "Text");

	/**
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 */
	function __construct($exportID = 0){
		parent::__construct(EXPORT_TABLE);
		$this->setDefaults();
		if($exportID){
			$this->ID = $exportID;
			$this->load($exportID);
		}
		// clear expiered stuff
		$this->selDocs = $this->clearExpiered($this->selDocs, FILE_TABLE);
		$this->selTempl = $this->clearExpiered($this->selTempl, TEMPLATES_TABLE);
		if(defined('OBJECT_TABLE')){
			$this->selObjs = $this->clearExpiered($this->selObjs, OBJECT_FILES_TABLE);
			$this->selClasses = $this->clearExpiered($this->selClasses, OBJECT_TABLE);
		} else{
			$this->selObjs = '';
			$this->selClasses = '';
		}
	}

	function clearExpiered($ids, $table, $idfield='ID'){
		$idsarr = makeArrayFromCSV($ids);
		$new = array();
		$db = new DB_WE();
		foreach($idsarr as $id){
			if(f('SELECT ' . $db->escape($idfield) . ' FROM ' . $db->escape($table) . ' WHERE ' . $db->escape($idfield) . '=\'' . (is_numeric($id) ? $id : $db->escape($id)) . '\';', $idfield, $db)){
				$new[] = $id;
			}
		}
		return makeCSVFromArray($new);
	}

	function save($force_new=false){
		$this->Icon = ($this->IsFolder == 1 ? we_base_ContentTypes::FOLDER_ICON : we_base_ContentTypes::LINK_ICON);
		$sets = array();
		$wheres = array();
		foreach($this->persistent_slots as $key => $val){
			//if(!in_array($val,$this->keys))
			if(isset($this->{$val})){
				$sets[] = '`' . $this->db->escape($val) . '`="' . $this->db->escape($this->{$val}) . '"';
			}
		}
		$where = $this->getKeyWhere();
		$set = implode(",", $sets);

		$this->table = $this->db->escape($this->table);
		if(!$this->ID || $force_new){

			$ret = $this->db->query('REPLACE INTO ' . $this->table . ' SET ' . $set);
			if($ret){
				# get ID #
				$this->ID = $this->db->getInsertId();
			}
			return $ret;
		} else{
			return $this->db->query('UPDATE ' . $this->table . ' SET ' . $set . ' WHERE ' . $where);
		}

		return false;
	}

	function delete(){
		//if (!$this->ID) return false;
		if($this->IsFolder)
			$this->deleteChilds();
		parent::delete();
		return true;
	}

	/*	 * *******************************
	 * delete childs from database
	 *
	 * ******************************** */

	function deleteChilds(){
		$this->db->query("SELECT ID FROM " . EXPORT_TABLE . ' WHERE ParentID=' . intval($this->ID));
		while($this->db->next_record()) {
			$child = new weExport($this->db->f("ID"));
			$child->delete();
		}
	}

	function clearSessionVars(){
		if(isset($_SESSION['weS']['ExportSession']))
			unset($_SESSION['weS']['ExportSession']);
		if(isset($_SESSION['weS']['exportVars']))
			unset($_SESSION['weS']['exportVars']);
	}

	function filenameNotValid($text){
		//FIXME: check on utf-8 systems!! this string is not readable!
		return preg_match('%[^a-z0-9äöü\._\@\ \-]%i', $text);
	}

	function exportToFilenameValid($filename){
		return (preg_match('%p?html?%i', $filename) || stripos($filename, 'inc') !== false || preg_match('%php3?%i', $filename));
	}

	function setDefaults(){
		$this->ParentID = 0;
		$this->Text = "weExport_" . time();
		$this->Icon = we_base_ContentTypes::LINK_ICON;
		$this->Selection = 'auto';
		$this->SelectionType = 'doctype';
		$this->Filename = $this->Text . ".xml";
		$this->ExportDepth = 5;

		$this->HandleDefTemplates = 0;
		$this->HandleDocIncludes = 0;
		$this->HandleObjIncludes = 0;
		$this->HandleDocLinked = 0;
		$this->HandleDefClasses = 0;
		$this->HandleObjEmbeds = 0;
		$this->HandleDoctypes = 0;
		$this->HandleCategorys = 0;
		$this->HandleOwners = 0;
		$this->HandleNavigation = 0;
	}

	function setPath(){
		$ppath = f('SELECT Path FROM ' . EXPORT_TABLE . ' WHERE ID=' . $this->ParentID . ';', 'Path', $this->db);
		$this->Path = $ppath . "/" . $this->Text;
	}

	function pathExists($path){
		$this->db->query('SELECT * FROM ' . $this->table . ' WHERE Path = \'' . $path . '\' AND ID <> \'' . $this->ID . '\';');
		if($this->db->next_record())
			return true;
		else
			return false;
	}

	function isSelf(){
		return strpos(clearPath(dirname($this->Path) . '/'), '/' . $this->Text . '/') !== false;
	}

	function evalPath($id=0){
		$db_tmp = new DB_WE();
		$path = "";
		if($id == 0){
			$id = $this->ParentID;
			$path = $this->Text;
		}

		$foo = getHash("SELECT Text,ParentID FROM " . EXPORT_TABLE . " WHERE ID='" . $id . "';", $db_tmp);
		$path = "/" . (isset($foo["Text"]) ? $foo["Text"] : "") . $path;

		$pid = isset($foo["ParentID"]) ? $foo["ParentID"] : "";
		while($pid > 0) {
			$db_tmp->query("SELECT Text,ParentID FROM " . EXPORT_TABLE . " WHERE ID='$pid'");
			while($db_tmp->next_record()) {
				$path = "/" . $db_tmp->f("Text") . $path;
				$pid = $db_tmp->f("ParentID");
			}
		}
		return $path;
	}

}


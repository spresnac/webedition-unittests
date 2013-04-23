<?php

/**
 * webEdition CMS
 *
 * $Rev: 4637 $
 * $Author: mokraemer $
 * $Date: 2012-07-01 21:20:18 +0200 (Sun, 01 Jul 2012) $
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
 * Class weVersion
 *
 * Provides functions for exporting and importing backups.
 */
class weVersion{

	var $db;
	var $ClassName = __CLASS__;
	var $Pseudo = "weVersion";
	var $attribute_slots = array();
	var $persistent_slots = array();
	var $ID = 0;
	var $Path = "";
	var $Data = "";
	var $SeqN = 0;
	var $linkData = true;

	function __construct($id = 0){
		$this->Pseudo = "weVersion";
		$this->persistent_slots = array("ID", "ClassName", "Path", "Data", "SeqN");
		foreach($this->persistent_slots as $slot)
			$this->$slot = "";
		$this->SeqN = 0;
		$this->ClassName = "weVersion";
		$this->db = new DB_WE();
		if($id)
			$this->load($id);
	}

	function load($id, $loadData = true){
		$this->ID = $id;
		$this->Path = f('SELECT binaryPath FROM ' . VERSIONS_TABLE . ' WHERE ID=' . intval($id), 'binaryPath', $this->db);
		if($this->Path && $loadData){
			return $this->loadFile($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $this->Path);
		}
		return false;
	}

	function loadFile($file){
		$this->Path = stri_replace(array($_SERVER['DOCUMENT_ROOT'], SITE_DIR), '', $file);
		if($this->linkData)
			return $this->Data = weFile::load($file, 'rb', 8192, weFile::isCompressed($file));
		else
			return true;
	}

	function save($force = true){
		if($this->ID){
			$path = $_SERVER['DOCUMENT_ROOT'] . $this->Path;
			if(file_exists($path) && !$force)
				return false;
			if(!is_dir(dirname($path))){
				we_util_File::createLocalFolderByPath(dirname($path));
			}
			weFile::save($_SERVER['DOCUMENT_ROOT'] . $this->Path, $this->Data, ($this->SeqN == 0 ? 'wb' : 'ab'));
		} else{
			$path = $_SERVER['DOCUMENT_ROOT'] . $this->Path;
			if(file_exists($path) && !$force)
				return false;
			if(!is_dir(dirname($path))){
				we_util_File::createLocalFolderByPath(dirname($path));
			}
			weFile::save($_SERVER['DOCUMENT_ROOT'] . $this->Path, $this->Data, ($this->SeqN == 0 ? 'wb' : 'ab'));
		}
		return true;
	}

	//alias
	function we_save(){
		return $this->save();
	}

}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5123 $
 * $Author: mokraemer $
 * $Date: 2012-11-12 13:14:29 +0100 (Mon, 12 Nov 2012) $
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
 * Class weBinary
 *
 * Provides functions for exporting and importing backups.
 */
class weBinary{

	var $db;
	var $ClassName = __CLASS__;
	var $Pseudo = "weBinary";
	var $attribute_slots = array();
	var $persistent_slots = array();
	var $ID = 0;
	var $Path = "";
	var $Data = "";
	var $SeqN = 0;
	var $linkData = true;

	function __construct($id = 0){
		$this->Pseudo = "weBinary";
		$this->persistent_slots = array("ID", "ClassName", "Path", "Data", "SeqN");
		foreach($this->persistent_slots as $slot)
			$this->$slot = "";
		$this->SeqN = 0;
		$this->ClassName = "weBinary";
		$this->db = new DB_WE();
		if($id)
			$this->load($id);
	}

	function load($id, $loadData = true){
		$this->ID = $id;
		$path = f("SELECT Path FROM " . FILE_TABLE . " WHERE ID=" . intval($id), 'Path', $this->db);
		if($path){
			$this->Path = $path;
			if($this->Path && $loadData){
				return $this->loadFile($this->Path);
			}
			return false;
		}
		else
			return false;
	}

	function loadFile($file){
		$path = str_replace(array($_SERVER['DOCUMENT_ROOT'], SITE_DIR), '', $file);
		$this->Path = $path;
		return ($this->linkData ? $this->Data = weFile::load($file) : true);
	}

	function save($force = true){
		$path = $_SERVER['DOCUMENT_ROOT'] . ($this->ID ? SITE_DIR : '') . $this->Path;
		if(file_exists($path) && !$force){
			return false;
		}
		if(!is_dir(dirname($path))){
			we_util_File::createLocalFolderByPath(dirname($path));
		}
		weFile::save($path, $this->Data, ($this->SeqN == 0 ? 'wb' : 'ab'));
		return true;
	}

	//alias
	function we_save(){
		return $this->save();
	}

}


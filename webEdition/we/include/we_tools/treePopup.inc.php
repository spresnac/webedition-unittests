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
class treePopup{

	var $size = 1;
	var $multiple = false;
	var $name = "treePopup";
	var $table = "";
	var $selected = array();
	var $showEntries = false;
	var $firstEntry = "";
	var $filter = "";
	var $Options = "";
	var $onChange = "";
	var $width = "";

	function treePopup($size, $multiple, $name, $table, $showEntries = false, $onChange = ""){
		$this->size = $size;
		$this->multiple = $multiple;
		$this->name = $name;
		$this->table = $table;
		$this->showEntries = $showEntries;
		$this->onChange = $onChange;
	}

	function dirEntry($name, $vorfahr, $text, $IsFolder){
		return array("name" => $name, "vorfahr" => $vorfahr, "text" => $text, "IsFolder" => $IsFolder);
	}

	function search($eintrag, $filter){
		if($this->showEntries){
			$query = "SELECT * from " . $DB_WE->escape($this->table) . " WHERE ParentID=" . intval($eintrag) . ' ' . (($filter) ? (" AND ($filter)") : "") . " ORDER BY Text";
		} else{
			$query = "SELECT * from " . $DB_WE->escape($this->table) . " WHERE IsFolder=1 AND ParentID=" . intval($eintrag) . ' ' . (($filter) ? (" AND ($filter)") : "") . " ORDER BY Text";
		}
		$GLOBALS['DB_WE']->query($query);
		$container = array();
		while($GLOBALS['DB_WE']->next_record()) {
			array_push($container, $this->dirEntry($GLOBALS['DB_WE']->f("ID"), $GLOBALS['DB_WE']->f("ParentID"), $GLOBALS['DB_WE']->f("Text"), $GLOBALS['DB_WE']->f("IsFolder")));
		}
		return $container;
	}

	function getCode($parent = 0){
		$this->Options = "";
		$this->pOption($parent, "", $this->filter, ($this->firstEntry ? 4 : 0));
		return sprintf('<select' . ($this->width ? ' style="width: ' . $this->width . 'px"' : '') . ' class="defaultfont" name="%s" size="%s"%s%s>' . "\n", $this->name, $this->size, ($this->multiple ? " multiple" : ""), ($this->onChange ? (' onChange="' . $this->onChange . '"') : "")) .
			($this->firstEntry ? '<option value="0">' . $this->firstEntry . "\n" : "") . $this->Options . "\n</select>\n";
	}

	function pOption($startEntry, $zweigEintrag, $filter = "", $nbsp = 0){
		$spaces = "";
		for($i = 0; $i < $nbsp; $i++){
			$spaces .= "&nbsp;";
		}
		$nf = $this->search($startEntry, $filter);
		if(!empty($nf)){

			$flag = false;
			for($ai = 0; $ai < count($nf); $ai++){
				$newAst = $zweigEintrag;
				if(!empty($this->selected)){
					$flag = false;
					for($i = 0; $i < count($this->selected); $i++){
						if($nf[$ai]["name"] == $this->selected[$i]){
							$flag = true;
							break;
						}
					}
				}
				if($nf[$ai]["IsFolder"] == 1 && $this->showEntries){
					$this->Options .= "<option" . ($flag ? " selected" : "") . " value=\"" . $nf[$ai]["name"] . "\">" . $spaces . $newAst . "[" . $nf[$ai]["text"] . "]\n";
				} else{
					$this->Options .= "<option" . ($flag ? " selected" : "") . " value=\"" . $nf[$ai]["name"] . "\">" . $spaces . $newAst . $nf[$ai]["text"] . "\n";
				}
				$newAst = $newAst . "&nbsp;&nbsp;&nbsp;&nbsp;";
				$this->pOption($nf[$ai]["name"], $newAst, $filter, $nbsp);
			}
		}
	}

}

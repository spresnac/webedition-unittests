<?php

/**
 * webEdition CMS
 *
 * $Rev: 3636 $
 * $Author: mokraemer $
 * $Date: 2011-12-23 19:38:26 +0100 (Fri, 23 Dec 2011) $
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
class MultiDirTemplateAndDefaultChooser extends MultiDirAndTemplateChooser{

	var $lines = 3;
	var $defaultName = "";
	var $defaultArr = array();

	function __construct($width, $ids, $cmd_del, $addbut, $ws="", $tmplcsv="", $tmplSelectName="", $mustTemplateIDs="", $tmplWs="", $defaultName="", $defaultCSV="", $fields="Icon,Path", $table=FILE_TABLE, $css="defaultfont"){
		$this->defaultName = $defaultName;
		$this->defaultArr = makeArrayFromCSV($defaultCSV);
		parent::__construct($width, $ids, $cmd_del, $addbut, $ws, $tmplcsv, $tmplSelectName, $mustTemplateIDs, $tmplWs, $fields, $table, $css);
	}

	function getRootLine($lineNr){

		switch($lineNr){
			case 0:
				return MultiDirAndTemplateChooser::getRootLine($lineNr);
			default:
				return $this->getLine($lineNr);
		}
	}

	function getLine($lineNr){

		$editable = $this->isEditable();
		switch($lineNr){
			case 0:
				return MultiDirAndTemplateChooser::getLine(0);
			case 1:
				$idArr = makeArrayFromCSV($this->ids);
				$checkbox = we_forms::checkbox($idArr[$this->nr], (in_array($idArr[$this->nr], $this->defaultArr) ? true : false), $this->defaultName . "_" . $this->nr, g_l('weClass', '[standard_workspace]'));
				return '<tr><td></td><td>' . $checkbox . '</td><td>' . we_html_tools::getPixel(50, 1) . '</td></tr>';
			case 2:
				return MultiDirAndTemplateChooser::getLine(1);
		}
	}

}

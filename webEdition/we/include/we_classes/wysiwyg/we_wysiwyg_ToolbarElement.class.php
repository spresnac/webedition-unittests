<?php

/**
 * webEdition CMS
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
class we_wysiwyg_ToolbarElement{

	var $width;
	var $height;
	var $cmd;
	var $editor;
	var $classname = __CLASS__;
	var $showMe = false;

	function __construct($editor, $cmd, $width, $height = ""){
		$this->editor = $editor;
		$this->width = $width;
		$this->height = $height;
		$this->cmd = $cmd;
		$this->showMe = $this->hasProp();
	}

	function getHTML(){
		return '';
	}

	function hasProp($cmd = ''){
		$cmd = ($cmd ? $cmd : $this->cmd);
		return stripos($this->editor->propstring, ',' . $cmd . ',') !== false || ($this->editor->propstring == '');
	}

}

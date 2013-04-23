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
class we_wysiwyg_ToolbarSeparator extends we_wysiwyg_ToolbarElement{

	var $classname = __CLASS__;

	function __construct($editor, $width = 3, $height = 22){
		$width = we_wysiwyg::$editorType == 'tinyMCE' ? 6 : $width; // correct value: 5: imi
		parent::__construct($editor, "", $width, $height);
	}

	function getHTML(){
		return '<div style="border-right: #999999 solid 1px; font-size: 0px; height: ' . $this->height . 'px ! important; width: ' . ($this->width - 1) . 'px;position: relative;" class="tbButtonWysiwygDefaultStyle"></div>';
	}

	function hasProp(){
		return true;
	}

}

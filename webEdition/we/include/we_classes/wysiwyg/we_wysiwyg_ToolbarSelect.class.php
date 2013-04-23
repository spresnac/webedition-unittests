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
class we_wysiwyg_ToolbarSelect extends we_wysiwyg_ToolbarElement{

	var $classname = __CLASS__;
	var $title = "";
	var $vals = array();

	function __construct($editor, $cmd, $title, $vals, $width = 0, $height = 20){
		parent::__construct($editor, $cmd, $width, $height);
		$this->title = $title;
		$this->vals = $vals;
	}

	function hasProp(){
		switch($this->cmd){
			case "fontname":
			case "fontsize":
			case "formatblock":
				return parent::hasProp() || parent::hasProp('font');
			default:
				return parent::hasProp();
		}
	}

	function getHTML(){
		if(we_base_browserDetect::isSafari()){
			$out = '<select id="' . $this->editor->ref . '_sel_' . $this->cmd . '" style="width:' . $this->width . 'px;margin-right:3px;" size="1" onmousedown="' . $this->editor->ref . 'Obj.saveSelection();" onmouseup="' . $this->editor->ref . 'Obj.restoreSelection();" onchange="' . $this->editor->ref . 'Obj.restoreSelection();' . $this->editor->ref . 'Obj.selectChanged(\'' . $this->cmd . '\',this.value);this.selectedIndex=0">' .
				'<option value="">' . oldHtmlspecialchars($this->title) . '</option>' . "\n";
			foreach($this->vals as $val => $txt){
				$out .= '<option value="' . oldHtmlspecialchars($val) . '">' . oldHtmlspecialchars($txt) . '</option>' . "\n";
			}
			$out .= '</select>';
		} else{
			$out = '<table id="' . $this->editor->ref . '_sel_' . $this->cmd . '"  onclick="if(' . $this->editor->ref . 'Obj.menus[\'' . $this->cmd . '\'].disabled==false){' . $this->editor->ref . 'Obj.showPopupmenu(\'' . $this->cmd . '\');}" class="tbButtonWysiwygDefaultStyle" width="' . $this->width . '" height="' . $this->height . '" cellpadding="0" cellspacing="0" border="0" title="' . ($this->title) . '" style="cursor:pointer;position: relative;">
	<tr>
		<td width="' . ($this->width - 20) . '" style="padding-left:10px;background-image: url(' . IMAGE_DIR . 'wysiwyg/menuback.gif);"  class="tbButtonWysiwygDefaultStyle"><input value="' . oldHtmlspecialchars($this->title) . '" type="text" name="' . $this->editor->ref . '_seli_' . $this->cmd . '" id="' . $this->editor->ref . '_seli_' . $this->cmd . '" readonly="readonly" style="cursor:pointer;height:16px;width:' . ($this->width - 30) . 'px;border:0px;background-color:transparent;color:black;font: 10px Verdana, Arial, Helvetica, sans-serif;" /></td>
		<td width="20" class="tbButtonWysiwygDefaultStyle"><img src="' . IMAGE_DIR . 'wysiwyg/menudown.gif" width="20" height="20" alt="" /></td>
	</tr>
</table><iframe src="' . HTML_DIR . 'white.html" width="280" height="160" id="' . $this->editor->ref . 'edit_' . $this->cmd . '" style=" z-index: 100000;position: absolute; display:none;"></iframe>';

			$js = 'wePopupMenuArray[wefoo]["' . $this->cmd . '"] = new Array();';
			foreach($this->vals as $val => $txt){
				$js .= 'wePopupMenuArray[wefoo]["' . $this->cmd . '"]["' . $val . '"]="' . $txt . '";	';
			}
			$out .= we_html_element::jsElement($js);
		}
		return $out;
	}

}
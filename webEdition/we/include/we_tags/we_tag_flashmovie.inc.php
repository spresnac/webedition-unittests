<?php

/**
 * webEdition CMS
 *
 * $Rev: 4548 $
 * $Author: arminschulz $
 * $Date: 2012-05-18 17:46:50 +0200 (Fri, 18 May 2012) $
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
function we_tag_flashmovie($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__)))
		return $foo;
	$name = weTag_getAttribute("name", $attribs);
	$showcontrol = weTag_getAttribute("showcontrol", $attribs, true, true);
	$showflash = weTag_getAttribute("showflash", $attribs, true, true);
	
	$id = $GLOBALS['we_doc']->getElement($name, "bdid");
	$id = $id ? $id : weTag_getAttribute("id", $attribs);
	if(isset($attribs['showcontrol']) && !$showcontrol &&  weTag_getAttribute("id", $attribs)){//bug 6433: später wird so ohne weiteres gar nicht mehr auf die id zurückgegriffen
		$id = weTag_getAttribute("id", $attribs);
		$attribs['id']=$id; //siehe korrespondierende Änderung in we:document::getField
		$attribs['showcontrol']=$showcontrol;//sicherstellen das es boolean iost
	}
	$fname = 'we_' . $GLOBALS['we_doc']->Name . '_img[' . $name . '#bdid]';
	$wmode = weTag_getAttribute("wmode", $attribs, "window");
	$startid = weTag_getAttribute("startid", $attribs);
	$parentid = weTag_getAttribute("parentid", $attribs, "0");
	

	$attribs = removeAttribs($attribs, array('showflash'));

	if($GLOBALS['we_editmode'] && !$showflash){
		$out = '';
	} else{
		$out = $GLOBALS['we_doc']->getField($attribs, "flashmovie");
	}

	if($showcontrol && $GLOBALS['we_editmode']){
		// Create "Edit Flash" button
		//				"javascript:we_cmd('openDocselector','" . ($id != "" ? $id : $startid) . "', '" . FILE_TABLE . "', 'document.forms[\'we_form\'].elements[\'" . $fname . "\'].value', '', 'opener.setScrollTo();opener.top.we_cmd(\'reload_editpage\');opener._EditorFrame.setEditorIsHot(true);', '" . session_id() . "'," .$parentid. ", 'application/x-shockwave-flash', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")",
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_cmd_enc("opener.setScrollTo(); opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('reload_editpage'); opener._EditorFrame.setEditorIsHot(true);");
		$flash_button = we_button::create_button(
				"image:btn_edit_flash", "javascript:we_cmd('openDocselector','" . ($id != "" ? $id : $startid) . "', '" . FILE_TABLE . "', '" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "'," . $parentid . ", 'application/x-shockwave-flash', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")", true);

		// Create "Delete/Clear Flash" button
		$clear_button = we_button::create_button(
				"image:btn_function_trash", "javascript:we_cmd('remove_image', '" . $name . "')", true);

		// Create HTML output


		$out = "
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"2\" background=\"" . IMAGE_DIR . "backgrounds/aquaBackground.gif\" style=\"border: solid #006DB8 1px;\">
				<tr>
					<td class=\"weEditmodeStyle\">$out
						<input type=\"hidden\" name=\"$fname\" value=\"" . $GLOBALS['we_doc']->getElement(
				$name, "bdid") . "\" /></td>
				</tr>
				<tr>
					<td class=\"weEditmodeStyle\" align=\"center\">";
		$out .= we_button::create_button_table(array(
				$flash_button, $clear_button
				), 5) . "</td></tr></table>";
	}
	//	When in SEEM - Mode add edit-Button to tag - textarea
	return $out;
}

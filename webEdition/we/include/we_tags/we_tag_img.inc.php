<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
function we_tag_img($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs);
	$startid = weTag_getAttribute('startid', $attribs);
	$parentid = weTag_getAttribute('parentid', $attribs, '0');
	$showcontrol = weTag_getAttribute('showcontrol', $attribs, true, true);
	//FIXME: wtf is showthumbcontrol?! what does it do?
	$showThumb = weTag_getAttribute('showthumbcontrol', $attribs, false, true);
	$showimage = weTag_getAttribute('showimage', $attribs, true, true);
	$showinputs = weTag_getAttribute('showinputs', $attribs, SHOWINPUTS_DEFAULT, true);

	//$attribs = removeAttribs($attribs, array('name', 'xmltype','to','nameto'));



	$id = $GLOBALS['we_doc']->getElement($name, 'bdid');
	$id = $id ? $id : $GLOBALS['we_doc']->getElement($name);
	$id = $id ? $id : weTag_getAttribute('id', $attribs);

	if(isset($attribs['showcontrol']) && !$showcontrol && weTag_getAttribute('id', $attribs)){//bug 6433: später wird so ohne weiteres gar nicht mehr auf die id zurückgegriffen
		$id = weTag_getAttribute('id', $attribs);
		$attribs['id'] = $id; //siehe korrespondierende Änderung in we:document::getField
		$attribs['showcontrol'] = $showcontrol; //sicherstellen das es boolean iost
	}

	//look if image exists in tblfile, and is an image
	if(f('SELECT 1 AS a FROM ' . FILE_TABLE . ' WHERE ContentType="image/*" AND ID=' . intval($id), 'a', $GLOBALS['DB_WE']) !== '1'){
		$id = 0;
	}
	// images can now have custom attribs
	$alt = '';
	$title = '';

	$altField = $name . '_img_custom_alt';
	$titleField = $name . '_img_custom_title';
	$thumbField = $name . '_img_custom_thumb';

	$fname = 'we_' . $GLOBALS['we_doc']->Name . '_img[' . $name . '#bdid]';
	$altname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $altField . ']';
	$titlename = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $titleField . ']';
	$thumbname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $thumbField . ']';

	if($id){
		$img = new we_imageDocument();
		$img->initByID($id);

		$alt = $img->getElement('alt');
		$title = $img->getElement('title');
		if($showThumb){
			$thumb = $img->getElement($thumbname);
		}
	}

	// images can now have custom attribs
	if(!(isset($_REQUEST['we_cmd'][0]) &&
		$_REQUEST['we_cmd'][0] == 'reload_editpage' &&
		(isset($_REQUEST['we_cmd'][1]) && $name == $_REQUEST['we_cmd'][1]) &&
		isset($_REQUEST['we_cmd'][2]) &&
		$_REQUEST['we_cmd'][2] == 'change_image') &&
		isset($GLOBALS['we_doc']->elements[$altField])){ // if no other image is selected.
		$alt = $GLOBALS['we_doc']->getElement($altField);
		$title = $GLOBALS['we_doc']->getElement($titleField);
		if($showThumb){
			$thumb = $GLOBALS['we_doc']->getElement($thumbField);
			$thumbattr = $thumb;
			$attribs['thumbnail'] = $thumbattr;
		}
	} elseif(isset($GLOBALS['we_doc'])){
		$altattr = $GLOBALS['we_doc']->getElement($altField);
		$titleattr = $GLOBALS['we_doc']->getElement($titleField);
		$altattr == "" ? "" : $attribs['alt'] = $altattr;
		$titleattr == "" ? "" : $attribs['title'] = $titleattr;
		if($showThumb){
			$thumbattr = $GLOBALS['we_doc']->getElement($thumbField);
			$attribs['thumbnail'] = $thumbattr;
		}
	}

	if($GLOBALS['we_editmode'] && !$showimage){
		$out = '';
	} elseif(!$id){
		if($GLOBALS['we_editmode'] && $GLOBALS['we_doc']->InWebEdition){
			$attribs['src'] = IMAGE_DIR . 'icons/no_image.gif';
			$attribs['style'] = 'width:64px;height:64px;border-style:none;';
			$attribs['alt'] = 'no-img';
			$attribs = removeAttribs($attribs, array('thumbnail'));
			$out = getHtmlTag('img', $attribs);
		} else{
			$out = ''; //no_image war noch in der Vorscha sichtbar
		}
	} else{
		$out = $GLOBALS['we_doc']->getField($attribs, 'img');
	}

	if(!$id && (!$GLOBALS['we_editmode'])){
		return "";
	}
	if(!$id){
		$id = "";
	}

	if($showcontrol && $GLOBALS['we_editmode']){
		$out = '
<table border="0" cellpadding="2" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
	<tr>
		<td class="weEditmodeStyle" colspan="2" align="center">' . $out . '
			<input onchange="_EditorFrame.setEditorIsHot(true);" type="hidden" name="' . $fname . '" value="' . $id . '" />
		</td>
	</tr>' .
			($showinputs ? //  only when wanted
				"<tr>
		            <td class=\"weEditmodeStyle\" align=\"center\" colspan=\"2\" style=\"width: 180px;\">
		            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                    <tr>
                        <td class=\"weEditmodeStyle\" style=\"color: black; font-size: 12px; font-family: " . g_l('css', '[font_family]') . ";\">" . g_l('weClass', "[alt_kurz]") . ":&nbsp;</td>
                        <td class=\"weEditmodeStyle\">" . we_html_tools::htmlTextInput($altname, 16, $alt, '', 'onchange="_EditorFrame.setEditorIsHot(true);"') . "</td>
                    </tr>
					<tr>
						<td class=\"weEditmodeStyle\"></td>
						<td class=\"weEditmodeStyle\"></td>
					</tr>
				    <tr>
		                <td class=\"weEditmodeStyle\" style=\"color: black; font-size: 12px; font-family: " . g_l('css', '[font_family]') . ";\">" . g_l('weClass', "[Title]") . ":&nbsp;</td>
		                <td class=\"weEditmodeStyle\">" . we_html_tools::htmlTextInput($titlename, 16, $title, '', 'onchange="_EditorFrame.setEditorIsHot(true);"') . "</td>
                    </tr>
		            </table>
                </tr>" : ''
			);

		if($showThumb){ //  only when wanted
			$db = new DB_WE();
			$db->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');
			if($db->num_rows()){
				$thumbnails = '<select name="' . $thumbname . '" size="1" onchange="top.we_cmd(\'reload_editpage\'); _EditorFrame.setEditorIsHot(true);">' .
					'<option value=""' . (($thumbattr == '') ? (' selected="selected"') : "") . '></option>';
				while($db->next_record()) {
					$thumbnails .= '<option value="' . $db->f("Name") . '"' . (($thumbattr == $db->f("Name")) ? (' selected="selected"') : "") . '>' . $db->f("Name") . '</option>';
				}
				$thumbnails .= '</select>';
				$out .= "
	<tr>
		<td class=\"weEditmodeStyle\" align=\"center\" colspan=\"2\" style=\"width: 180px;\">
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
								<tr>
										<td class=\"weEditmodeStyle\" style=\"color: black; font-size: 12px; font-family: " . g_l('css', '[font_family]') . ";\">" . g_l('weClass', "[thumbnails]") . ":&nbsp;</td>
										<td class=\"weEditmodeStyle\">" . $thumbnails . "</td>
								</tr>
			</table>
		</td>
	</tr>";
			}
		}
		$out .= '
	<tr>
		<td class="weEditmodeStyle" colspan="2" align="center">';

		if($id == ""){ // disable edit_image_button
			$_editButton = we_button::create_button("image:btn_edit_image", "#", false, 100, 20, "", "", true);
		} else{ //	show edit_image_button
			//	we use hardcoded Content-Type - because it must be an image -> <we:img  >
			$_editButton = we_button::create_button(
					"image:btn_edit_image", "javascript:top.doClickDirect($id,'image/*', '" . FILE_TABLE . "'  )");
		}
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_cmd_enc("opener.setScrollTo(); opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('reload_editpage','" . $name . "','change_image'); opener.top.hot = 1;");

		$out .= we_button::create_button_table(
				array(
				$_editButton,
				we_button::create_button(
					"image:btn_select_image", "javascript:we_cmd('openDocselector', '" . ($id != "" ? $id : $startid) . "', '" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "'," . $parentid . ",'image/*', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")", true),
				we_button::create_button(
					"image:btn_function_trash", "javascript:we_cmd('remove_image', '" . $name . "')", true)
				), 5) . '</td></tr></table>';
	}
	return $out;
}

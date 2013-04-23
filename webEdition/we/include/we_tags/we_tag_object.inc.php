<?php

/**
 * webEdition CMS
 *
 * $Rev: 5612 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 22:46:14 +0100 (Mon, 21 Jan 2013) $
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
function we_parse_tag_object($attribs, $content){
	$arr = array();
	eval('$arr = ' . (PHPLOCALSCOPE ? str_replace('$', '\$', $attribs) : $attribs) . ';'); //Bug #6516
	$name = weTag_getParserAttribute('name', $arr);
	if($name && strpos($name, ' ') !== false){
		return parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
	}

	return '<?php global $lv;
		if(' . we_tag_tagParser::printTag('object', $attribs) . '){?>' . $content . '<?php }
		we_post_tag_listview(); ?>';
}

function we_tag_object($attribs){
	if(!defined('WE_OBJECT_MODULE_PATH')){
		print modulFehltError('Object/DB', __FUNCTION__);
		return false;
	}

	$condition = weTag_getAttribute('condition', $attribs, 0);
	$classid = weTag_getAttribute('classid', $attribs);
	$we_oid = weTag_getAttribute('id', $attribs, 0);
	$name = weTag_getAttribute('name', $attribs);
	//never show name generated inside blocks
	$_showName = weTag_getAttribute('_name_orig', $attribs);
	$size = weTag_getAttribute('size', $attribs, 30);
	$triggerid = weTag_getAttribute('triggerid', $attribs, '0');
	$searchable = weTag_getAttribute('searchable', $attribs, false, true);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);
	$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, true);

	if(!isset($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = array();
	}

	$rootDirID = ($classid ? f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path=(SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid) . ')', 'ID', $GLOBALS['DB_WE']) : 0);

	if($name){
		if(strpos($name, ' ') !== false){
			print parseError(sprintf(g_l('parser', '[name_with_space]'), 'object'));
			return false;
		}

		$we_doc = $GLOBALS['we_doc'];
		//handle listview of documents
		$we_oid = isset($GLOBALS['lv']) && is_object($GLOBALS['lv']) && $GLOBALS['lv']->f($name) ? $GLOBALS['lv']->f($name) : ($we_doc->getElement($name) ? $we_doc->getElement($name) : $we_oid);

		$path = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $we_oid, 'Path', $GLOBALS['DB_WE']);
		$textname = 'we_' . $we_doc->Name . '_txt[' . $name . '_path]';
		$idname = 'we_' . $we_doc->Name . '_txt[' . $name . ']';
		$table = OBJECT_FILES_TABLE;

		if($GLOBALS['we_editmode']){
			$delbutton = we_button::create_button('image:btn_function_trash', "javascript:document.forms[0].elements['$idname'].value=0;document.forms[0].elements['$textname'].value='';_EditorFrame.setEditorIsHot(false);we_cmd('reload_editpage');");
			$button = we_button::create_button('select', "javascript:we_cmd('openDocselector',document.forms[0].elements['$idname'].value,'$table','document.forms[\'we_form\'].elements[\'$idname\'].value','document.forms[\'we_form\'].elements[\'$textname\'].value','opener.we_cmd(\'reload_editpage\');opener._EditorFrame.setEditorIsHot(true);','" . session_id() . "','$rootDirID','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")");
			?>
			<table border="0" cellpadding="0" cellspacing="0" background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif">
				<tr>
					<td style="padding:0 6px;"><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif"><b><?php echo $_showName; ?></b></span></td>
					<td><?php print we_html_tools::hidden($idname, $we_oid); ?></td>
					<td><?php print we_html_tools::htmlTextInput($textname, $size, $path, "", ' readonly', "text", 0, 0); ?></td>
					<td><?php we_html_tools::getPixel(6, 4); ?></td>
					<td><?php print $button; ?></td>
					<td><?php we_html_tools::getPixel(6, 4); ?></td>
					<td><?php print $delbutton; ?></td>
				</tr>
			</table><?php
		}
	} else{

		$we_oid = $we_oid ? $we_oid : (isset($_REQUEST['we_oid']) ? intval($_REQUEST['we_oid']) : 0);
	}
	$GLOBALS['lv'] = new we_objecttag($classid, $we_oid, $triggerid, (empty($searchable) ? false : $searchable), $condition, $hidedirindex, $objectseourls);
	if(is_array($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'][] = clone($GLOBALS['lv']);
	}

	if($GLOBALS['lv']->avail){
		if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == 'seem'){
			print '<a href="' . $we_oid . '" seem="object"></a>';
		}
	}
	return $GLOBALS['lv']->avail;
}
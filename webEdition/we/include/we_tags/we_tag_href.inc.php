<?php

/**
 * webEdition CMS
 *
 * $Rev: 5787 $
 * $Author: mokraemer $
 * $Date: 2013-02-10 03:02:29 +0100 (Sun, 10 Feb 2013) $
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
function we_tag_href($attribs){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute('name', $attribs);
	$type = weTag_getAttribute('type', $attribs, 'all');
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);
	$include = weTag_getAttribute('include', $attribs, false, true);
	$reload = weTag_getAttribute('reload', $attribs, false, true);
	$rootdir = weTag_getAttribute('rootdir', $attribs, '/');
	$seeMode = weTag_getAttribute((isset($attribs['seem']) ? 'seem' : 'seeMode'), $attribs, true, true);

	if(substr($rootdir, 0, 1) != '/'){
		$rootdirid = $rootdir;
		$rootdir = id_to_path($rootdir, FILE_TABLE);
	} else{
		if(strlen($rootdir) > 1){
			$rootdir = rtrim($rootdir, '/');
		}
		$rootdirid = path_to_id($rootdir, FILE_TABLE);
	}
	// Bug Fix #7045
	if($rootdir == '/'){
		$rootdir = '';
	}

	$file = weTag_getAttribute('file', $attribs, true, true);
	$directory = weTag_getAttribute('directory', $attribs, false, true);
	$attribs = removeAttribs($attribs, array('rootdir'));

	if($GLOBALS['we_doc']->ClassName == 'we_objectFile'){
		$hrefArr = $GLOBALS['we_doc']->getElement($name) ? unserialize($GLOBALS['we_doc']->getElement($name)) : array();
		if(!is_array($hrefArr)){
			$hrefArr = array();
		}
		return (!empty($hrefArr) ? we_document::getHrefByArray($hrefArr) : '');
	}

	$nint = $name . '_we_jkhdsf_int';
	$nintID = $name . '_we_jkhdsf_intID';
	$nintPath = $name . '_we_jkhdsf_intPath';
	$extPath = $GLOBALS['we_doc']->getElement($name);

	// we have to use a html_entity_decode first in case a user has set &amp, &uuml; by himself
	$extPath = !empty($extPath) ? oldHtmlspecialchars(html_entity_decode($extPath)) : $extPath;

	switch($type){
		case 'int':
		case 'all':
			$int = ($type == 'int' || $GLOBALS['we_doc']->getElement($nint) != '') ? $GLOBALS['we_doc']->getElement($nint) : false;
			$intID = $GLOBALS['we_doc']->getElement($nintID);
/*			if(!isset($GLOBALS['we_doc']->elements[$nintID]['dat']) && $rootdirid){
				$intID = $rootdirid;
			}*/
			$intPath = $ct = '';

			if($intID){
				$foo = getHash('SELECT Path,ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($intID), $GLOBALS['DB_WE']);
				if(!empty($foo)){
					list($intPath, $ct) = $foo;
				}
			}

			if($int){
				$href = $intPath;
				$include_path = $href ? $_SERVER['DOCUMENT_ROOT'] . '/' . $href : '';
				$path_parts = pathinfo($href);
				if($hidedirindex && show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$href = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
				}
				break;
			}
		//no break;
		case 'ext':
			$int = false;
			$href = $extPath;
			$include_path = $href ? $_SERVER['DOCUMENT_ROOT'] . '/' . $href : '';
			break;
	}

	if(!$GLOBALS['we_editmode']){
		if($int && defined('CUSTOMER_TABLE') && $intID){
			$filter = weDocumentCustomerFilter::getFilterByIdAndTable($intID, FILE_TABLE);

			if(is_object($filter)){
				$obj = (object) array('ID' => $intID, 'ContentType' => $ct);
				if($filter->accessForVisitor($obj, array(), true) != weDocumentCustomerFilter::ACCESS){
					return '';
				}
			}
		}

		return ($include ? ($include_path && file_exists($include_path) ? '<?php include("' . $include_path . '"); ?>' : '') : $href);
	}

	$int_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $nint . ']';
	$intPath_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $nintPath . ']';
	$intID_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $nintID . ']';
	$ext_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';

	$trashbut = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $intID_elem_Name . "'].value = ''; document.we_form.elements['" . $intPath_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);" . (($include || $reload) ? "setScrollTo(); top.we_cmd('reload_editpage');" : ""), true);
	$span = '<span style="color: black;font-size:' . ((we_base_browserDetect::isMAC()) ? "11px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px")) . ';font-family:' . g_l('css', '[font_family]') . ';">';
	$attr = we_make_attribs($attribs, 'name,value,type,onkeydown,onKeyDown,_name_orig');

	switch($type){
		case "all":
			$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$intID_elem_Name'].value");
			$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$intPath_elem_Name'].value");
			$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements['$int_elem_Name'][0].checked = true;" . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : ""));
			if(($directory && $file) || $file){
				$but = we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('openDocselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "', '" . $rootdirid . "', '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");");
				$but2 = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', '" . (($directory && $file) ? "filefolder" : "") . "', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements[\'$int_elem_Name\'][1].checked = true;','" . $rootdir . "')") : '';
			} else{
				$but = we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('openDirselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "', '" . $rootdirid . "');");
				$but2 = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', 'folder', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements[\'$int_elem_Name\'][1].checked = true;','" . $rootdir . "')") : '';
			}
			$open = we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', "javascript:if(document.forms[0].elements['$intID_elem_Name'].value){top.weEditorFrameController.openDocument('" . FILE_TABLE . "', document.forms[0].elements['$intID_elem_Name'].value,'');}");
			$trashbut2 = we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'btn_function_trash', "javascript:document.we_form.elements['" . $ext_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);", true);
			return
				'<table border="0" cellpadding="0" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
					<tr>
						<td class="weEditmodeStyle">' . we_forms::radiobutton(1, $int, $int_elem_Name, $span . g_l('tags', "[int_href]") . ":</span>") . '</td>
						<td class="weEditmodeStyle"><input type="hidden" name="' . $intID_elem_Name . '" value="' . $intID . '" /><input type="text" name="' . $intPath_elem_Name . '" value="' . $intPath . '" ' . $attr . ' readonly /></td>
						<td class="weEditmodeStyle">' . we_html_tools::getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">' . $but . '</td>
						<td class="weEditmodeStyle">' . $open . '</td>
						<td class="weEditmodeStyle">' . $trashbut . '</td>
					</tr>
					<tr>
						<td class="weEditmodeStyle">' . we_forms::radiobutton(0, !$int, $int_elem_Name, $span . g_l('tags', "[ext_href]") . ":</span>") . '</td>
						<td class="weEditmodeStyle"><input onchange="this.form.elements[\'' . $int_elem_Name . '\'][1].checked = true;" type="text" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $extPath . '" ' . $attr . ' /></td>
						<td class="weEditmodeStyle">' . we_html_tools::getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">' . $but2 . '</td>
						<td class="weEditmodeStyle">' . $trashbut2 . '</td>
					</tr>
				</table>' .
				($include && $include_path && file_exists($include_path) ? '<?php include("' . $include_path . '"); ?>' : '');

		case 'int':
			$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$intID_elem_Name'].value");
			$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$intPath_elem_Name'].value");
			$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true); " . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : ""));
			$but = ((($directory && $file) || $file) ?
					we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('openDocselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "', '" . $rootdirid . "', '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");") :
					we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'edit_link', "javascript:we_cmd('openDirselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "', '" . $rootdirid . "');")
				);
			$open = we_button::create_button(we_button::WE_IMAGE_BUTTON_IDENTIFY . 'function_view', "javascript:if(document.forms[0].elements['$intID_elem_Name'].value){top.weEditorFrameController.openDocument('" . FILE_TABLE . "', document.forms[0].elements['$intID_elem_Name'].value,'');}");

			return '<table border="0" cellpadding="0" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
					<tr>
						<td class="weEditmodeStyle defaultfont" nowrap="nowrap"><input type="hidden" name="' . $int_elem_Name . '" value="1" />' . $span . g_l('tags', "[int_href]") . ':</span></td>
						<td class="weEditmodeStyle"><input type="hidden" name="' . $ext_elem_Name . '" /><input type="hidden" name="' . $intID_elem_Name . '" value="' . $intID . '" /><input type="text" name="' . $intPath_elem_Name . '" value="' . $intPath . '" ' . $attr . ' readonly /></td>
						<td class="weEditmodeStyle">' . we_html_tools::getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">' . $but . '</td>
						<td class="weEditmodeStyle">' . $open . '</td>
						<td class="weEditmodeStyle">' . $trashbut . '</td>
					</tr>
				</table>' .
				($include && $include_path && file_exists($include_path) ? '<?php include("' . $include_path . '"); ?>' : '');
		case 'ext':
			$ext_elem_Name = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';

			$trashbut2 = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $ext_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true)", true);
			$but2 = (we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ?
					((($directory && $file) || $file) ?
						we_button::create_button("select", "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', '" . (($directory && $file) ? "filefolder" : "") . "', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true);', '" . $rootdir . "')") :
						we_button::create_button("select", "javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', 'folder', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true);', '" . $rootdir . "')")
					) :
					'');


			return '<table border="0" cellpadding="0" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
					<tr>
						<td class="weEditmodeStyle defaultfont" nowrap="nowrap"><input type="hidden" name="' . $int_elem_Name . '" value="0" />' . $span . g_l('tags', '[ext_href]') . ':</span></td>
						<td class="weEditmodeStyle"><input type="text" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $extPath . '" ' . $attr . ' /></td>
						<td class="weEditmodeStyle">' . we_html_tools::getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">' . $but2 . '</td>
						<td class="weEditmodeStyle">' . $trashbut2 . '</td>
					</tr>
				</table>' .
				($include && $include_path && file_exists($include_path) ? '<?php include("' . $include_path . '"); ?>' : '');
	}
}

<?php
/**
 * webEdition CMS
 *
 * $Rev: 5827 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 13:54:15 +0100 (Sun, 17 Feb 2013) $
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
we_html_tools::protect();
we_html_tools::htmlTop();

// init document
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

$z = 0;
$tab_head = 'var weTabs;';
$tab_js = '';

if($_SESSION['weS']['we_mode'] != 'seem'){ //	No tabs in Super-Easy-Edit_mode
	$we_tabs = new we_tabs();
	// user has no access to file - only preview mode.
	if($we_doc->userHasAccess() != we_root::USER_HASACCESS && $we_doc->userHasAccess() != we_root::USER_NO_SAVE){
		if(in_array(WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[preview]"), (($we_doc->EditPageNr == WE_EDITPAGE_PREVIEW) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_PREVIEW)));
		}
	} else{ //	show tabs according to permissions
		if(in_array(WE_EDITPAGE_PROPERTIES, $we_doc->EditPageNrs) && we_hasPerm("CAN_SEE_PROPERTIES")){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[tab_properties]"), (($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_PROPERTIES . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_PROPERTIES)));
		}
		if(in_array(WE_EDITPAGE_CONTENT, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[edit]"), (($we_doc->EditPageNr == WE_EDITPAGE_CONTENT) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_CONTENT . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_CONTENT)));
		}

		if(in_array(WE_EDITPAGE_IMAGEEDIT, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[edit_image]"), (($we_doc->EditPageNr == WE_EDITPAGE_IMAGEEDIT) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_IMAGEEDIT . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_IMAGEEDIT)));
		}

		if(in_array(WE_EDITPAGE_THUMBNAILS, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[thumbnails]"), (($we_doc->EditPageNr == WE_EDITPAGE_THUMBNAILS) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_THUMBNAILS . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_THUMBNAILS)));
		}

		if(in_array(WE_EDITPAGE_WORKSPACE, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[workspace]"), (($we_doc->EditPageNr == WE_EDITPAGE_WORKSPACE) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_WORKSPACE . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_WORKSPACE)));
		}

		// Bug Fix #6062
		if(in_array(WE_EDITPAGE_CFWORKSPACE, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[workspace]"), (($we_doc->EditPageNr == WE_EDITPAGE_CFWORKSPACE) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_CFWORKSPACE . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_CFWORKSPACE)));
		}

		if(in_array(WE_EDITPAGE_INFO, $we_doc->EditPageNrs) && we_hasPerm("CAN_SEE_INFO")){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[information]"), (($we_doc->EditPageNr == WE_EDITPAGE_INFO) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_INFO . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_INFO)));
		}

		if(in_array(WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", ($we_doc->ContentType == "text/weTmpl" ? g_l('weClass', "[previeweditmode]") : g_l('weClass', "[preview]")), (($we_doc->EditPageNr == WE_EDITPAGE_PREVIEW) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_PREVIEW)));
		}

		if(in_array(WE_EDITPAGE_PREVIEW_TEMPLATE, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[preview]"), (($we_doc->EditPageNr == WE_EDITPAGE_PREVIEW_TEMPLATE) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW_TEMPLATE . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_PREVIEW_TEMPLATE)));
		}

		if(in_array(WE_EDITPAGE_METAINFO, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[metainfos]"), (($we_doc->EditPageNr == WE_EDITPAGE_METAINFO) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_METAINFO . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_METAINFO)));
		}

		if(in_array(WE_EDITPAGE_FIELDS, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[fields]"), (($we_doc->EditPageNr == WE_EDITPAGE_FIELDS) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_FIELDS . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_FIELDS)));
		}

		if(in_array(WE_EDITPAGE_SEARCH, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[search]"), (($we_doc->EditPageNr == WE_EDITPAGE_SEARCH) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_SEARCH . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_SEARCH)));
		}

		// Bug Fix #6062
		/*
		  if(in_array(WE_EDITPAGE_CFSEARCH,$we_doc->EditPageNrs)){

		  $we_tabs->addTab(new we_tab("#", g_l('weClass',"[search]"),(($we_doc->EditPageNr == WE_EDITPAGE_CFSEARCH) ? "TAB_ACTIVE" : "TAB_NORMAL"),"we_cmd('switch_edit_page'," . WE_EDITPAGE_CFSEARCH . ",'" . $we_transaction . "');"));
		  }
		 */

		if(we_hasPerm("CAN_SEE_SCHEDULER") && weModuleInfo::isActive("schedule") && in_array(WE_EDITPAGE_SCHEDULER, $we_doc->EditPageNrs) && $we_doc->ContentType != "folder"){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[scheduler]"), (($we_doc->EditPageNr == WE_EDITPAGE_SCHEDULER) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_SCHEDULER . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_SCHEDULER)));
		}
		if((in_array(WE_EDITPAGE_VALIDATION, $we_doc->EditPageNrs) && ($we_doc->ContentType == 'text/webedition' || $we_doc->ContentType == 'text/css' || $we_doc->ContentType == 'text/html' )) && we_hasPerm("CAN_SEE_VALIDATION")){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[validation]"), (($we_doc->EditPageNr == WE_EDITPAGE_VALIDATION) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_VALIDATION . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_VALIDATION)));
		}

		if(in_array(WE_EDITPAGE_WEBUSER, $we_doc->EditPageNrs) && (we_hasPerm("CAN_EDIT_CUSTOMERFILTER") || we_hasPerm("CAN_CHANGE_DOCS_CUSTOMER"))){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[webUser]"), (($we_doc->EditPageNr == WE_EDITPAGE_WEBUSER) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_WEBUSER . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_WEBUSER)));
		}

		if((we_hasPerm("ADMINISTRATOR") || we_hasPerm("SEE_VERSIONS")) && in_array(WE_EDITPAGE_VERSIONS, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[version]"), (($we_doc->EditPageNr == WE_EDITPAGE_VERSIONS) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_VERSIONS . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_VERSIONS)));
		}

		$we_doc->we_initSessDat($we_dt);

		if((in_array(WE_EDITPAGE_VARIANTS, $we_doc->EditPageNrs) && ($we_doc->canHaveVariants(($we_doc->ContentType == 'text/webedition' || $we_doc->ContentType == 'objectFile')) )) && we_hasPerm("CAN_EDIT_VARIANTS")){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[variants]"), (($we_doc->EditPageNr == WE_EDITPAGE_VARIANTS) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_VARIANTS . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_VARIANTS)));
		}

		if(in_array(WE_EDITPAGE_DOCLIST, $we_doc->EditPageNrs)){
			$we_tabs->addTab(new we_tab("#", g_l('weClass', "[docList]"), (($we_doc->EditPageNr == WE_EDITPAGE_DOCLIST) ? "TAB_ACTIVE" : "TAB_NORMAL"), "we_cmd('switch_edit_page'," . WE_EDITPAGE_DOCLIST . ",'" . $we_transaction . "');", array("id" => "tab_" . WE_EDITPAGE_DOCLIST)));
		}
	}

	$we_tabs->onResize('editHeader');
	$tab_head = $we_tabs->getHeader();
	$tab_js = $we_tabs->getJS();
	$tab_js .= $we_tabs->getJSRebuildTabs();
} else{
	$tab_head = we_html_element::jsElement($tab_head . 'function setFrameSize(){}');
}

if($tab_head){
	print $tab_head;
}



$_js_we_setPath = "
function we_setPath(path, text, id) {

	// update document-tab
	_EditorFrame.initEditorFrameData({\"EditorDocumentText\":text,\"EditorDocumentPath\":path});

	path = path.replace(/</g,'&lt;');
  path = path.replace(/>/g,'&gt;');
	path = '<b><font color=\"#006699\">'+path+'</font></b>';
	if(document.getElementById) {
		var div = document.getElementById('h_path');
		div.innerHTML = path;
		if(id>0){
			var div = document.getElementById('h_id');
			div.innerHTML = id;
		}
	}else if(document.all) {
		var div = document.all['h_path'];
		div.innerHTML = path;
		if(id>0){
			var div = document.all['h_id'];
			div.innerHTML = id;
		}
	}
}";

$_js_we_cmd =
	'function we_cmd() {
' . ($GLOBALS['we_doc']->ContentType != 'text/weTmpl' ? 'parent.openedWithWE = 1;' : '') . "

	var args = '';
	var url = '" . WEBEDITION_DIR . "we_cmd.php?';
	for(var i = 0; i < arguments.length; i++){
		url += 'we_cmd['+i+']='+escape(arguments[i]);
		if(i < (arguments.length - 1)){
			url += '&';
		}
	}
	for(var i = 0; i < arguments.length; i++) {
		args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
	}

	switch ( arguments[0] ) {

		case 'switch_edit_page':
			_EditorFrame.setEditorEditPageNr(arguments[1]);
			eval('parent.we_cmd('+args+')');
		break;
	}

}";

print we_html_element::jsElement(
		'var _EditorFrame = top.weEditorFrameController.getEditorFrame(parent.name);
_EditorFrame.setEditorEditPageNr(' . $we_doc->EditPageNr . ');' .
		$_js_we_setPath .
		$_js_we_cmd) .
//	Stylesheet and image prepader for buttons
	STYLESHEET;
?>
</head>
<body id="eHeaderBody" bgcolor="white" background="<?php print IMAGE_DIR; ?>backgrounds/header.gif" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onLoad="setFrameSize()" onResize="setFrameSize()">
	<div id="main" ><?php
		print '<div style="margin:3px 0px 3px 5px;" id="headrow">&nbsp;' . we_html_element::htmlB(str_replace(' ', '&nbsp;', g_l('contentTypes', '[' . $we_doc->ContentType . ']'))) . ': ' .
			($we_doc->Table == FILE_TABLE && $we_doc->ID ? '<a href="javascript:top.wasdblclick=1;top.doClick(\'' . $we_doc->ID . '\');">' : '') .
			'<span id="h_path"></span>' . ($we_doc->Table == FILE_TABLE && $we_doc->ID ? '</a>' : '') . ' (ID: <span id="h_id"></span>)</div>' .
			($_SESSION['weS']['we_mode'] != 'seem' ?
				$we_tabs->getHTML() : '');
		?></div>
</body>
</html>
<?php
$_text = ($we_doc->Filename ? $we_doc->Filename . (isset($we_doc->Extension) ? $we_doc->Extension : '') : $we_doc->Text);
echo we_html_element::jsElement('we_setPath("' . $we_doc->Path . '", "' . $_text . '", ' . intval($we_doc->ID) . ');');
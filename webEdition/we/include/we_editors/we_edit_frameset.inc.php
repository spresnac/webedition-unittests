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

/**
 * Searches for the first Page in the editor, which the user is allowed to see.
 * If he can see the given Nr, then that page will be shown.
 *
 * @see     getFirstValidEditPageNr
 *
 * @param   doc         string
 * @param   EditPageNr  string

 * @return   string
 */
function getFirstValidEditPageNr($doc, $EditPageNr){
	if($doc->isValidEditPage($EditPageNr) && permissionhandler::isUserAllowedForAction('switch_edit_page', $EditPageNr)){
		return $EditPageNr;
	} else{
		//	bugfix for new tag: we:hidePages
		foreach($doc->EditPageNrs AS $key => $_editpage){
			//  the command in this case is swith_edit_page, because in this funtion
			//  the editor tries to select a certain edit_page
			//  in some cases it must switch it
			if(permissionhandler::isUserAllowedForAction('switch_edit_page', $doc->EditPageNrs[$key])){
				return $doc->EditPageNrs[$key];
				break;
			}
		}
		return -1;
	}
}

function getTabs($classname, $predefined = 0){
	$ret = $predefined;
	$documentClasses = array('we_webEditionDocument', 'we_htmlDocument', 'we_flashDocument', 'we_imageDocument', 'we_otherDocument', 'we_textDocument', 'we_objectFile');
	// Check which tab the user can see
	if(in_array($classname, $documentClasses)){
		$ret = getFirstValidEditPageNr($GLOBALS['we_doc'], $predefined);
	}
	return $ret;
}

$we_Table = $_REQUEST['we_cmd'][1];

if($_REQUEST['we_cmd'][2]){
	$we_ID = intval($_REQUEST['we_cmd'][2]);
}

if(isset($_REQUEST['we_cmd'][3])){
	$we_ContentType = $_REQUEST['we_cmd'][3];
	if(empty($we_ContentType)){
		//get ct from DB
		$we_ContentType = f('SELECT ContentType FROM ' . $GLOBALS['DB_WE']->escape($we_Table) . ' WHERE ID=' . intval($we_ID), 'ContentType', $GLOBALS['DB_WE']);
	}
}

// init document
if(isset($_SESSION['weS']['we_data'][$we_transaction])){
	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
}

include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
if(!$we_doc->fileExists){
	include(WE_INCLUDES_PATH . 'weInfoPages/weNoResource.inc.php');
	exit();
}
$_needPerm = '';
if(isset($_REQUEST['we_cmd'][1])){
	switch($_REQUEST['we_cmd'][1]){
		case TEMPLATES_TABLE:
			$_needPerm = 'CAN_SEE_TEMPLATES';
			break;
		case FILE_TABLE:
			$_needPerm = 'CAN_SEE_DOCUMENTS';
			break;
	}
}
if($_needPerm != '' && !we_hasPerm($_needPerm)){
	include(WE_INCLUDES_PATH . 'weInfoPages/weNoPerms.inc.php');
	exit();
}

$we_doc->InWebEdition = true;
$we_doc->i_loadNavigationItems();

//	check template for hidePages
$we_doc->setDocumentControlElements();

//	in SEEM-Mode the first page is the preview page.
//	when editing an image-document we go to edit page
if($_SESSION['weS']['we_mode'] == 'seem'){
	if(isset($_REQUEST['SEEM_edit_include']) && $_REQUEST['SEEM_edit_include'] && $we_doc->userHasAccess() == 1){ //	Open seem_edit_include pages in edit-mode
		$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_CONTENT;
		$we_doc->EditPageNr = WE_EDITPAGE_CONTENT;
	} else{
		if($we_doc->ClassName == 'we_imageDocument'){
			$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_CONTENT;
			$we_doc->EditPageNr = WE_EDITPAGE_CONTENT;
		} else{
			$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PREVIEW;
			$we_doc->EditPageNr = WE_EDITPAGE_PREVIEW;
		}
	}
}

//  This code was over the comment: init document !!!!!!! (line 82?)
if(!isset($we_ID)){
	$_SESSION['weS']['EditPageNr'] = getTabs('we_webEditionDocument', WE_EDITPAGE_PROPERTIES);
}

if((isset($_REQUEST['we_cmd'][10])) && ($we_Table == FILE_TABLE) && ($we_ContentType == 'text/webedition')){
	$we_doc->setTemplateID($_REQUEST['we_cmd'][10]);
	$_SESSION['weS']['EditPageNr'] = getTabs($we_doc->ClassName, 1);
}

//predefine ParentPath
if(isset($_REQUEST['we_cmd'][0]) && isset($_REQUEST['we_cmd'][5]) && $_REQUEST['we_cmd'][5] != '' && $_REQUEST['we_cmd'][0] == 'new_document' && $we_doc->ParentID == 0){
	if($we_doc->ContentType == 'folder'){
		$we_doc->setParentID($_REQUEST['we_cmd'][5]);
	}
}


if((isset($_REQUEST['we_cmd'][8])) && ($we_Table == FILE_TABLE) && ($we_ContentType == 'text/webedition')){
	$we_doc->changeDoctype($_REQUEST['we_cmd'][8]);
	$_SESSION['weS']['EditPageNr'] = getTabs($we_doc->ClassName, 1);
} else if(isset($_REQUEST['we_cmd'][8]) && (defined('OBJECT_FILES_TABLE') && $we_Table == OBJECT_FILES_TABLE) && ($we_ContentType == 'objectFile')){
	$we_doc->TableID = $_REQUEST['we_cmd'][8];
	$we_doc->setRootDirID(true);
	$we_doc->restoreDefaults();
	$_SESSION['weS']['EditPageNr'] = getTabs($we_doc->ClassName, WE_EDITPAGE_CONTENT);
}


if($we_doc->ID){
	if(($ws = get_ws($we_Table))){
		if(!(in_workspace($we_doc->ID, $ws, $we_Table, $DB_WE))){
			switch($we_Table){
				case TEMPLATES_TABLE: //	different workspace. for template
					$we_message = g_l('alert', '[' . ($we_ContentType == 'folder') ? 'folder' : $we_Table . '][not_im_ws]');
					include(WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
					exit();
				case FILE_TABLE: //	only preview mode allowed for docs
					//	MUST change to Preview-Mode
					$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PREVIEW;
					break;
			}
		}
	}
	$_access = $we_doc->userHasAccess();
	if(($_access !== we_root::USER_HASACCESS && $_access !== we_root::FILE_LOCKED)){ //   user has no access to object/document - bugfix #2481
		if($we_ContentType != 'object'){
			$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PREVIEW;
		} else{
			include(WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
			exit();
		}
	}
}


if(isset($we_sess_folderID) && is_array($we_sess_folderID) && (!$we_doc->ID)){
	if($we_sess_folderID[$we_doc->Table])
		$we_doc->setParentID($we_sess_folderID[$we_doc->Table]);
}

if($we_doc->ID == 0){
	$we_doc->EditPageNr = getTabs($we_doc->ClassName, WE_EDITPAGE_PROPERTIES);
} else if(isset($_SESSION['weS']['EditPageNr'])){
	if(defined('SHOP_TABLE'))
		$we_doc->checkTabs();
	if(in_array($_SESSION['weS']['EditPageNr'], $we_doc->EditPageNrs)){
		$we_doc->EditPageNr = getTabs($we_doc->ClassName, $_SESSION['weS']['EditPageNr']);
	} else{
		//	Here we must get the first valid EDIT_PAGE
		$we_doc->EditPageNr = getFirstValidEditPageNr($we_doc, WE_EDITPAGE_CONTENT);
	}
}

if($we_Table == FILE_TABLE && $we_ContentType == 'folder' && isset($we_ID) && !empty($we_ID)){
	$we_doc->EditPageNr = WE_EDITPAGE_DOCLIST;
	$_SESSION['weS']['EditPageNr'] = getTabs($we_doc->ClassName, 16);
}

if($we_doc->EditPageNr === -1){ //	there is no view available for this document
	//	show errorMessage - no view for this document (we:hidePages)
	print we_html_element::htmlDocType() . we_html_element::htmlHtml(
			we_html_element::htmlHead(
				we_html_element::jsElement('top.toggleBusy(0);') .
				STYLESHEET
			) .
			we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_tools::htmlDialogLayout(we_html_tools::htmlAlertAttentionBox(g_l('alert', '[no_views][description]'), 1, 500, true), g_l('alert', '[no_views][headline]'))
			)
	);
	exit;
}


if(!isset($we_doc->IsClassFolder)){
	//update already offline users

	$_userID = $we_doc->isLockedByUser(); //	Check if file is locked.
	$GLOBALS['DB_WE']->query('UPDATE ' . USER_TABLE . ' SET Ping=0 WHERE Ping<UNIX_TIMESTAMP(NOW()-' . (PING_TIME + PING_TOLERANZ) . ')');

	$_filelocked = ($_userID != 0 && $_userID != $_SESSION['user']['ID']);

	if(!$_filelocked){ // file can be edited
		//	#####	Lock the new file
		//	before lock - check if user can edit the file.
		if($we_doc->userHasAccess() == we_root::USER_HASACCESS){ //	only when user has access to file
			if($_SESSION['weS']['we_mode'] == 'normal' || $we_doc->EditPageNr != WE_EDITPAGE_PREVIEW){
				$we_doc->lockDocument();
			}
		}
	}

	if($we_doc->ContentType == 'objectFile' && (!$we_doc->canMakeNew())){ // at this time only in objectFiles
		$we_message = g_l('alert', '[no_new][objectFile]');
		include(WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
		exit;
	}
}

// objects need to know the last webEdition Path, because of Workspaces
if($we_doc->ContentType == 'text/webedition'){
	$_SESSION['weS']['last_webEdition_document'] = array(
		'Path' => $we_doc->Path
	);
}

// get default code
if(!isset($we_doc->elements['data']['dat'])){
	$we_doc->elements['data']['dat'] = (isset($_REQUEST['we_cmd'][10]) && $we_doc->ContentType == 'text/weTmpl' ?
			base64_decode($_REQUEST['we_cmd'][10]) :
			we_base_ContentTypes::inst()->getDefaultCode($we_doc->ContentType));
}
we_html_tools::htmlTop();
?>
<script  type="text/javascript">
	<!--
	var unlock = false;
	var scrollToVal = 0;
	var editorScrollPosTop = 0;
	var editorScrollPosLeft = 0;
	var weAutoCompetionFields = new Array();
	var openedInEditor = true;

	var _EditorFrame = top.weEditorFrameController.getEditorFrame(window.name);
	_EditorFrame.initEditorFrameData(
					{
						"EditorType": "model",
						"EditorDocumentText": "<?php print oldHtmlspecialchars($we_doc->Text); ?>",
						"EditorDocumentPath": "<?php print $we_doc->Path; ?>",
						"EditorEditorTable": "<?php print $we_doc->Table; ?>",
						"EditorDocumentId": "<?php print $we_doc->ID; ?>",
						"EditorTransaction": "<?php print $we_transaction; ?>",
						"EditorContentType": "<?php print $we_doc->ContentType; ?>",
						"EditorDocumentParameters":<?php print (isset($parastr) ? '"' . $parastr . '"' : '""'); ?>
					}
	);

	function we_cmd() {
		if (!unlock) {
			var args = "";
			for (var i = 0; i < arguments.length; i++) {
				args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
			}
			if (top.we_cmd)
				eval('top.we_cmd(' + args + ')');
		}
	}

	function closeAllModalWindows() {
		try {
			var _editor1 = self.frames[1];
			var _editor2 = self.frames[2];
			if (_editor1.jsWindow_count) {
				for (i = 0; i < _editor1.jsWindow_count; i++) {
					eval("_editor1.jsWindow" + i + "Object.close()");
				}
			}
			if (_editor2.jsWindow_count) {
				for (i = 0; i < _editor2.jsWindow_count; i++) {
					eval("_editor2.jsWindow" + i + "Object.close()");
				}
			}
		} catch (e) {

		}
	}

	function doUnload() {

		closeAllModalWindows();

<?php if($we_doc->userHasAccess() == we_root::USER_HASACCESS){ ?>
			if (!unlock) {
				if (!top.opener || top.opener.win) {	//	login to super easy edit mode
					unlock = true;
				}
			}
<?php } ?>
	}


<?php if(!$we_doc->ID){ ?>
		if (top.Tree && top.Tree.treeData && top.Tree.treeData.table != "<?php print $we_Table; ?>") {
			top.we_cmd('load', "<?php print $we_Table ?>");

		}
	<?php
	if(strpos($we_doc->ContentType, "image/") !== false || strpos($we_doc->ContentType, "application/") !== false){
		$we_doc->EditPageNr = 1;
	}
}
?>

	if (top.treeData && (top.treeData.state == top.treeData.tree_states["select"] || top.treeData.state == top.treeData.tree_states["selectitem"])) {
		top.we_cmd("exit_delete");
	}
	//	SEEM
	//	With this var we can see, if the document is opened via webEdition
	//	or just opened in the bm_content Frame, p.ex javascript location.replace or reload or sthg..
	//	we must check, if the tab is switched ... etc.
	var openedWithWE = 1;

<?php
if(isset($_REQUEST['we_cmd'][0]) && isset($parastr) && ($_REQUEST['we_cmd'][0] == "edit_document_with_parameters")){
	echo 'var parameters = "' . $parastr . '";';
}


if($GLOBALS['we_doc']->ContentType != 'text/weTmpl'){
	?>
		function setOpenedWithWE(val) {
			openedWithWE = val;
		}

		function checkDocument() {
			loc = null;
			try {
				loc = String(editor.location);
			} catch (e) {
			}

			_EditorFrame.setEditorIsHot(false);

			if (loc) {	//	Page is on webEdition-Server, open it with matching command
				// close existing editor, it was closed very hard
				top.weEditorFrameController.closeDocument(_EditorFrame.getFrameId());

				// build command for this location
				top.we_cmd("open_url_in_editor", loc);

			} else {	//	Page is not known - replace top and bottom frame of editor
				//	Fill upper and lower Frame with white
				//	If the document is editable with webedition, it will be replaced
				//	Location not known - empty top and footer

				//	close window, when in seeMode include window.
	<?php
	if(isset($_REQUEST["SEEM_edit_include"]) && $_REQUEST["SEEM_edit_include"]){

		print we_message_reporting::getShowMessageCall(g_l('SEEM', "[alert][close_include]"), we_message_reporting::WE_MESSAGE_ERROR)
		?>
					top.close();
		<?php
	} else{
		?>
					_EditorFrame.initEditorFrameData(
									{
										"EditorType": "none_webedition",
										"EditorContentType": "none_webedition",
										"EditorDocumentText": "Unknown",
										"EditorDocumentPath": "Unknown"
									}
					);

					editHeader.location = "about:blank";
					editFooter.location = "<?php print WE_INCLUDES_DIR . "we_seem/we_SEEM_openExtDoc_footer.php" ?>";

		<?php
	}
	?>
			}
		}
	<?php
}
?>
	//-->
</script>
<?php

function setOnload(){
	// Don't do this with Templates and only in Preview Mode
	// in Edit-Mode all must be reloaded !!!
	// To remove this functionality - just use the second condition as well.
	return ($GLOBALS['we_doc']->ContentType != 'text/weTmpl'/* && $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PREVIEW */ ?
			'onload="if(top.edit_include){top.edit_include.close();} if(openedWithWE == 0){ checkDocument(); } setOpenedWithWE(0);"' :
			'');
}
?>
</head>
<?php
$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

if($_SESSION['weS']['we_mode'] == 'seem'){
	?>
	<frameset onLoad="_EditorFrame.initEditorFrameData({'EditorIsLoading': false});" rows="1,*,0,40" framespacing="0" border="0" frameborder="NO" onUnload="doUnload()">
		<frame src="<?php print we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_edit_header"); ?>" name="editHeader" noresize scrolling="no"/>
		<frame <?php print setOnload(); ?> src="<?php print we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_editor") . (isset($parastr) ? '&' . $parastr : ''); ?>" name="editor_<?php print $_REQUEST["frameId"]; ?>" noresize/>
		<frame  src="about:blank" name="contenteditor_<?php print $_REQUEST["frameId"]; ?>" noresize/>
		<frame src="<?php print we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_edit_footer"); ?>&SEEM_edit_include=<?php print ( isset($_REQUEST["SEEM_edit_include"]) && $_REQUEST["SEEM_edit_include"] ? "true" : "false") ?>" name="editFooter" scrolling=no noresize/>
	</frameset><noframes></noframes>
	<?php
} else{

	$showContentEditor = ($we_doc->EditPageNr == WE_EDITPAGE_CONTENT && substr($we_doc->ContentType, 0, 5) == "text/" && $we_doc->ContentType != "text/webedition");
	?>
	<frameset onload="_EditorFrame.initEditorFrameData({'EditorIsLoading': false});" rows="39,<?php echo $showContentEditor ? "0,*" : "*,0"; ?>,40" framespacing="0" border="0" frameborder="NO" onUnload="doUnload();">
		<frame src="<?php print we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_edit_header"); ?>" name="editHeader" noresize scrolling="no"/>
		<?php if($showContentEditor){ ?>
			<frame <?php print setOnload(); ?> src="about:blank" name="editor_<?php print $_REQUEST["frameId"]; ?>" noresize/>
			<frame  src="<?php print we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_editor") . (isset($parastr) ? '&' . $parastr : ''); ?>" name="contenteditor_<?php print $_REQUEST['frameId']; ?>" noresize/>
		<?php } else{ ?>
			<frame <?php print setOnload(); ?> src="<?php print we_class::url(WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=load_editor") . (isset($parastr) ? '&' . $parastr : ''); ?>" name="editor_<?php print $_REQUEST["frameId"]; ?>" noresize/>
			<frame  src="about:blank" name="contenteditor_<?php print $_REQUEST["frameId"]; ?>" noresize/>
		<?php } ?>
		<frame src="<?php print we_class::url(WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=load_edit_footer'); ?>" name="editFooter" scrolling=no noresize/>
	</frameset>
	<?php
}
?>
<body>
</body>
</html>
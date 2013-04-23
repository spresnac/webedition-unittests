<?php
/**
 * webEdition CMS
 *
 * $Rev: 5594 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:19:42 +0100 (Sat, 19 Jan 2013) $
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
@we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
we_html_tools::htmlTop();
?>

<script  type="text/javascript">
	<!--
	var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("<?php print $GLOBALS['we_transaction']; ?>");
	var _EditorFrameDocumentRef = _EditorFrame.getDocumentReference();

<?php
if($we_responseText && $we_responseTextType == we_message_reporting::WE_MESSAGE_ERROR){
	print "_EditorFrame.setEditorIsHot(true);";
}

if(isset($wasSaved) && $wasSaved){
	// DOC was saved, mark open tabs to reload if necessary
	// outsource this, if code gets too big
	// was saved - not hot anymore
	print "_EditorFrame.setEditorIsHot(false);";


	$_reloadJs = "";
	switch($GLOBALS['we_doc']->ContentType){

		case "text/weTmpl": // #538 reload documents based on this template

			$_reloadDocsTempls = we_rebuild::getTemplAndDocIDsOfTemplate($GLOBALS['we_doc']->ID, false, false, true);

			$_reload[FILE_TABLE] = $_reloadDocsTempls['documentIDs'];
			$_reload[TEMPLATES_TABLE] = $_reloadDocsTempls['templateIDs'];

			// reload all documents based on this template
			if(!empty($_reload[FILE_TABLE]) || !empty($_reload[TEMPLATES_TABLE])){

				$_reloadJs .= "
var _reloadTabs = new Object();
_reloadTabs['" . FILE_TABLE . "'] = '," . implode(",", $_reload[FILE_TABLE]) . ",';
_reloadTabs['" . TEMPLATES_TABLE . "'] = '," . implode(",", $_reload[TEMPLATES_TABLE]) . ",';
				";

				$_reloadJs .= "
var _usedEditors = top.weEditorFrameController.getEditorsInUse();

for (frameId in _usedEditors) {

	if ( _reloadTabs[_usedEditors[frameId].getEditorEditorTable()] && (_reloadTabs[_usedEditors[frameId].getEditorEditorTable()]).indexOf(',' + _usedEditors[frameId].getEditorDocumentId() + ',') != -1 ) {
		_usedEditors[frameId].setEditorReloadNeeded(true);

 	}
}
				";
			}
			break;
	}
	print $_reloadJs;


	if($_SESSION['weS']['we_mode'] != "seem"){

		$_newDocJs = "";

		//	JS, when not in seem
		$isTmpl = $we_doc->ContentType == "text/weTmpl" && (we_hasPerm("NEW_WEBEDITIONSITE") || we_hasPerm("ADMINISTRATOR"));
		if(!$isTmpl){
			$isObject = $we_doc->ContentType == "object" && (we_hasPerm("NEW_OBJECTFILE") || we_hasPerm("ADMINISTRATOR"));
		}
		if($isTmpl){
			$_newDocJs .= 'if( _EditorFrame.getEditorMakeNewDoc() == true ) {' .
				(isset($saveTemplate) && $saveTemplate ?
					"	top.we_cmd('new','" . FILE_TABLE . "','','text/webedition','','" . $we_doc->ID . "');" :
					''
				) .
				'} else {';
		} elseif($isObject){
			$_newDocJs .=
				"if( _EditorFrame.getEditorMakeNewDoc() == true ) {
				top.we_cmd('new','" . OBJECT_FILES_TABLE . "','','objectFile','" . $we_doc->ID . "');
			} else {";
		}
		$_newDocJs .= 'if ( _EditorFrame.getEditorIsInUse() ) {_EditorFrameDocumentRef.frames[0].location.reload();}';
		if($isTmpl || $isObject){
			$_newDocJs .= "}";
		}
		print $_newDocJs;
	}
}

print (isset($we_JavaScript) ? $we_JavaScript : "");

if($we_responseText){
	$_jsCommand = "";
	print 'self.focus();
		top.toggleBusy(0);
		showAlert = 0;
		var contentEditor = top.weEditorFrameController.getVisibleEditorFrame();';

	// enable navigation box if doc has been published
	if(isset($GLOBALS['we_doc']->Published) && $GLOBALS['we_doc']->Published){
		print "try{ if( _EditorFrame && _EditorFrame.getEditorIsInUse() && contentEditor && contentEditor.switch_button_state) contentEditor.switch_button_state('add', 'add_enabled', 'enabled'); } catch(e) {}";
	}

	if($_SESSION['weS']['we_mode'] == "seem" && (!isset($_showAlert) || !$_showAlert)){ //	Confirm Box or alert in seeMode
		if(isset($GLOBALS["publish_doc"]) && $GLOBALS["publish_doc"] == true){ //	edit include and pulish then close window and reload
			$_jsCommand .='
			if(isEditInclude){
				showAlert = 1;
			}';
		}

		if(in_array(WE_EDITPAGE_PREVIEW, $GLOBALS['we_doc']->EditPageNrs) && $GLOBALS['we_doc']->EditPageNr != WE_EDITPAGE_PREVIEW){ //	alert or confirm
			$_jsCommand .= "
			if(!showAlert){
				if(confirm(\"" . $we_responseText . "\\n\\n" . g_l('SEEM', "[confirm][change_to_preview]") . "\")){
					_EditorFrameDocumentRef.frames[0].we_cmd('switch_edit_page'," . WE_EDITPAGE_PREVIEW . ",'" . $GLOBALS['we_transaction'] . "');
				} else {
					_EditorFrameDocumentRef.frames[0].we_cmd('switch_edit_page'," . $GLOBALS['we_doc']->EditPageNr . ",'" . $GLOBALS['we_transaction'] . "');
				}
			} else {
				" . we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) . "
			}
			";
		} else{ //	alert when in preview mode
			$_jsCommand .= we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) .
				"_EditorFrameDocumentRef.frames[0].we_cmd('switch_edit_page'," . $GLOBALS['we_doc']->EditPageNr . ",'" . $GLOBALS['we_transaction'] . "');" .
				//	JavaScript: generated in we_editor.inc.php
				(isset($_REQUEST['we_cmd'][5]) ? $_REQUEST['we_cmd'][5] : '' );
		}

		if(isset($GLOBALS["publish_doc"]) && $GLOBALS["publish_doc"] == true){

			$_jsCommand .="
			if(isEditInclude){
				" . we_message_reporting::getShowMessageCall(g_l('SEEM', "[alert][changed_include]"), we_message_reporting::WE_MESSAGE_NOTICE) . "
						weWindow.top.we_cmd(\"reload_editpage\");
						weWindow.edit_include.close();
						top.close();
			}
			";
		}
	} else{ //	alert in normal mode
		$_jsCommand .= we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType) .
			//	JavaScript: generated in we_editor.inc.php
			(isset($_REQUEST['we_cmd'][5]) ? $_REQUEST['we_cmd'][5] : '' );
	}
	print $_jsCommand;
}
?>
	//-->
</script></head><body></body></html>
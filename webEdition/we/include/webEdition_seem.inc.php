<?php
/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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

/**
 * @return void
 * @desc only used in normal mode
 */
function pWebEdition_Tree(){

}

/**
 * @return void
 * @desc prints JavaScript functions only needed in SeeMode
 */
function pWebEdition_JSFunctions(){
	?>
	function makeNewEntry(icon,id,pid,txt,open,typ,tab){
	}
	function drawTree(){
	}

	function info(text){
	}

	function toggleBusy(w) {
/*	if(w == busy)
	return;
	if(self.header) {
	if(self.header.toggleBusy) {
	busy=w;
	self.header.toggleBusy(w);
	return;
	}
	}
	setTimeout("toggleBusy("+w+");",300);*/
	}

	function doUnload(whichWindow) {

	// unlock all open documents
	var _usedEditors = top.weEditorFrameController.getEditorsInUse();

	var docIds = "";
	var docTables = "";

	for (frameId in _usedEditors) {

	if (_usedEditors[frameId].EditorType != "cockpit") {

	docIds += _usedEditors[frameId].getEditorDocumentId() + ",";
	docTables += _usedEditors[frameId].getEditorEditorTable() + ",";
	}
	}

	if (docIds) {

	top.we_cmd('unlock',docIds,'<?php print $_SESSION["user"]["ID"]; ?>',docTables);

	if(top.opener){
	top.opener.focus();

	}
	}
	//  close the SEEM-edit-include when exists
	if(top.edit_include){
	top.edit_include.close();
	}
	try{
	if(jsWindow_count) {
	for(i = 0; i < jsWindow_count; i++){
	eval("jsWindow"+i+"Object.close()");
	}
	}
	if(browserwind){
	browserwind.close();
	}
	} catch(e){

	}

	//  only when no SEEM-edit-include window is closed

	if(whichWindow !="include"){
	if(opener) {
	opener.location.replace('<?php
	print WEBEDITION_DIR;
	?>
	we_loggingOut.php');
	}
	}
	}
	<?php
}

/**
 * @return void
 * @desc prints the different cases for the function we_cmd
 */
function pWebEdition_JSwe_cmds(){
	?>
	case "new":
	top.weEditorFrameController.openDocument(arguments[1],arguments[2],arguments[3],"",arguments[4],"",arguments[5]);
	break;
	case "load":
	//	toggleBusy(1);
	break;
	case "exit_delete": case "exit_move":
	deleteMode=false; case "delete": case "move":
	if(top.deleteMode !=arguments[1]){ top.deleteMode=arguments[1];
	}
	if(arguments[2] !=1)
	we_repl(top.weEditorFrameController.getActiveDocumentReference(),url,arguments[0]);
	break;
	<?php
}

/**
 * @return void
 * @desc the frameset for the SeeMode
 */
function pWebEdition_Frameset(){
	$we_cmds = $seem = '';
	if(isset($GLOBALS["SEEM_edit_include"]) && $GLOBALS["SEEM_edit_include"]){ // edit include file
		$_REQUEST["SEEM_edit_include"] = true;
		$we_cmds = "?we_cmd[0]=edit_document&";

		for($i = 1; $i < count($_REQUEST['we_cmd']); $i++){
			$we_cmds .= "we_cmd[" . $i . "]=" . $_REQUEST['we_cmd'][$i] . "&";
		}
		$we_cmds.='&SEEM_edit_include=true';
	}
	?>
		<div style="position:absolute;top:0px;left:0px;right:0px;height:32px;border-bottom: 1px solid black;">
			<?php we_main_header::pbody();?>
		</div>
		<div style="position:absolute;top:32px;left:0px;right:0px;bottom:<?php print ( (isset($_SESSION["prefs"]["debug_seem"]) && $_SESSION["prefs"]["debug_seem"] != 0)) ? 100 : 0; ?>px;border: 0px;">
			<iframe src="<?php print WEBEDITION_DIR; ?>resizeframe.php?<?php print $we_cmds ?>" style="border:0px;width:100%;height:100%;overflow: hidden;" id="rframe" name="rframe"></iframe>
		</div>
		<div style="position:absolute;left:0px;right:0px;bottom:0px;height:<?php print ( (isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0)) ? 100 : 1; ?>px;border: 1px solid;">
			<div style="height:100%;float:left;width:25%;border:0px;">
				<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="load"></iframe>
			</div>
			<div style="height:100%;float:left;width:25%;border:0px;">
				<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="load2"></iframe>
			</div>
			<!-- Bugfix Opera >=10.5  target name is always "ad" -->
			<div style="height:100%;float:left;width:10%;border:0px;">
				<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="ad"></iframe>
			</div>
			<div style="height:100%;float:left;width:10%;border:0px;"><?php include(WE_USERS_MODULE_PATH . 'we_users_ping.inc.php'); ?></div>
			<div style="height:100%;float:left;width:10%;border:0px;">
				<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="postframe"></iframe>
			</div>
			<div style="height:100%;float:left;width:10%;border:0px;">
				<iframe src="<?php print HTML_DIR ?>white.html" style="border-right:1px solid black;width:100%;height:100%;overflow: hidden;" name="plugin"></iframe>
			</div>
		</div>
	<?php
}

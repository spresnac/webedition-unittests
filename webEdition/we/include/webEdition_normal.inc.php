<?php
/**
 * webEdition CMS
 *
 * $Rev: 5475 $
 * $Author: mokraemer $
 * $Date: 2012-12-29 19:33:58 +0100 (Sat, 29 Dec 2012) $
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
 * @desc prints the functions needed for the tree.
 */
function pWebEdition_Tree(){
	$Tree = new weMainTree("webEdition.php", "top", "self.Tree", "top.load");
	print $Tree->getJSTreeCode();
}

/**
 * @return void
 * @desc prints JavaScript functions only needed in normal mode
 */
function pWebEdition_JSFunctions(){
	?>
	function toggleBusy(w) {
/*	if(w == busy || firstLoad==false){
	return;
	}
	if(self.header){
	if(self.header.toggleBusy){
	busy=w;
	self.header.toggleBusy(w);
	return;
	}
	}
	setTimeout("toggleBusy("+w+");",300);*/
	}

	var regular_logout = false;
	function doUnload(whichWindow) { // triggered when webEdition-window is closed
	if(!regular_logout){

	if(typeof(tinyMceDialog) !== "undefinded" && tinyMceDialog !== null){
	var tinyDialog = tinyMceDialog;
	try{
	tinyDialog.close();
	}catch(err){}
	}

	if(typeof(tinyMceSecondaryDialog) !== "undefinded" && tinyMceSecondaryDialog !== null){
	var tinyDialog = tinyMceSecondaryDialog;
	try{
	tinyDialog.close();
	}catch(err){}
	}

	try{
	if(jsWindow_count){
	for(i = 0;i < jsWindow_count;i++){
	eval("jsWindow"+i+"Object.close()");
	}
	}
	if(browserwind){
	browserwind.close();
	}
	} catch(e){}

	if(whichWindow != "include"){ 	// only when no SEEM-edit-include window is closed
	// FIXME: closing-actions for SEEM
	if(top.opener) {
	<?php if(!(we_base_browserDetect::isCHROME() || we_base_browserDetect::isSAFARI())){ ?>
		top.opener.location.replace('<?php print WEBEDITION_DIR; ?>we_loggingOut.php?isopener=1');
		top.opener.focus();
	<?php } else{ ?>
		top.opener.location.reload();
		var logoutpopup = window.open('<?php print WEBEDITION_DIR; ?>we_loggingOut.php?isopener=0', "webEdition","width=350,height=70,toolbar=no,menubar=no,directories=no,location=no,resizable=no,status=no,scrollbars=no,top=300,left=500");
		logoutpopup.focus();
	<?php } ?>
	}
	else{
	var logoutpopup = window.open('<?php print WEBEDITION_DIR; ?>we_loggingOut.php?isopener=0', "webEdition","width=350,height=70,toolbar=no,menubar=no,directories=no,location=no,resizable=no,status=no,scrollbars=no,top=300,left=500");
	logoutpopup.focus();
	}
	}
	}
	}

	var widthBeforeDeleteMode = 0;
	var widthBeforeDeleteModeSidebar = 0;
	<?php
}

/**
 * @return void
 * @desc prints the different cases for the function we_cmd
 */
function pWebEdition_JSwe_cmds(){
	?>
	case "new":
	treeData.unselectnode();
	if(typeof(arguments[5])!="undefined") {
	top.weEditorFrameController.openDocument(arguments[1],arguments[2],arguments[3],"",arguments[4],"",arguments[5]);
	} else if(typeof(arguments[4])!="undefined" && arguments[5]=="undefined") {
	top.weEditorFrameController.openDocument(arguments[1],arguments[2],arguments[3],"","","",arguments[5]);
	} else {
	top.weEditorFrameController.openDocument(arguments[1],arguments[2],arguments[3],"",arguments[4]);
	}
	break;

	case "load":
	if(self.Tree)
	if(self.Tree.setScrollY)
	self.Tree.setScrollY();
	we_cmd("setTab",arguments[1]);
	//toggleBusy(1);
	we_repl(self.load,url,arguments[0]);
	break;
	case "exit_delete":
	case "exit_move":
	deleteMode = false;
	treeData.setstate(treeData.tree_states["edit"]);
	drawTree();

	self.rframe.document.getElementById("bm_treeheaderDiv").style.height = "1px";
	self.rframe.document.getElementById("bm_mainDiv").style.top = "1px";
	top.setTreeWidth(widthBeforeDeleteMode);
	top.setSidebarWidth(widthBeforeDeleteModeSidebar);
	break;
	case "delete":
	if(top.deleteMode != arguments[1]){
	top.deleteMode=arguments[1];
	}
	if(!top.deleteMode &&  treeData.state==treeData.tree_states["select"]){
	treeData.setstate(treeData.tree_states["edit"]);
	drawTree();
	}
	self.rframe.document.getElementById("bm_treeheaderDiv").style.height = "150px";
	self.rframe.document.getElementById("bm_mainDiv").style.top = "150px";

	var width = top.getTreeWidth();

	widthBeforeDeleteMode = width;

	if (width < <?php echo weTree::DeleteWidth; ?>) {
	top.setTreeWidth(<?php echo weTree::DeleteWidth; ?>);
	}
	top.storeTreeWidth(widthBeforeDeleteMode);

	var widthSidebar = top.getSidebarWidth();

	widthBeforeDeleteModeSidebar = widthSidebar;

	if(arguments[2] != 1) we_repl(self.rframe.treeheader,url,arguments[0]);
	break;
	case "move":
	if(top.deleteMode != arguments[1]){
	top.deleteMode=arguments[1];
	}
	if(!top.deleteMode && treeData.state==treeData.tree_states["selectitem"]){
	treeData.setstate(treeData.tree_states["edit"]);
	drawTree();
	}
	self.rframe.document.getElementById("bm_treeheaderDiv").style.height = "160px";
	self.rframe.document.getElementById("bm_mainDiv").style.top = "160px";

	var width = top.getTreeWidth();

	widthBeforeDeleteMode = width;

	if (width < <?php echo weTree::MoveWidth; ?>) {
	top.setTreeWidth(<?php echo weTree::MoveWidth; ?>);
	}
	top.storeTreeWidth(widthBeforeDeleteMode);

	var widthSidebar = top.getSidebarWidth();

	widthBeforeDeleteModeSidebar = widthSidebar;

	if(arguments[2] != 1) {
	we_repl(self.rframe.treeheader,url,arguments[0]);
	}
	break;

	<?php
}

/**
 * @return void
 * @desc the frameset for the SeeMode
 */
function pWebEdition_Frameset(){
	?>
	<div style="position:absolute;top:0px;left:0px;right:0px;height:32px;border-bottom: 1px solid black;">
		<?php we_main_header::pbody(); ?>
	</div>
	<div style="position:absolute;top:32px;left:0px;right:0px;bottom:<?php print ( (isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0)) ? 100 : 0; ?>px;border: 0;">
		<iframe frameBorder="0" src="<?php print WEBEDITION_DIR; ?>resizeframe.php" style="border:0px;width:100%;height:100%;overflow: hidden;" id="rframe" name="rframe"></iframe>
	</div>
	<div style="position:absolute;left:0px;right:0px;bottom:0px;height: <?php echo (isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0) ? '100' : '0'; ?>px;border: 1px solid;">
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

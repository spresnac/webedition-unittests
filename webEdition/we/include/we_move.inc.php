<?php
/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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
include_once (WE_INCLUDES_PATH . 'we_move_fn.inc.php');

we_html_tools::protect();
$table = $_REQUEST['we_cmd'][2];

$script = "";

if(($table == TEMPLATES_TABLE && !we_hasPerm("MOVE_TEMPLATE")) ||
	($table == FILE_TABLE && !we_hasPerm("MOVE_DOCUMENT")) ||
	(defined("OBJECT_TABLE") && $table == OBJECT_TABLE && !we_hasPerm("MOVE_OBJECTFILES"))){
	include_once (WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
	exit();
}

$yuiSuggest = & weSuggest::getInstance();

if($_REQUEST['we_cmd'][0] == "do_move" || $_REQUEST['we_cmd'][0] == 'move_single_document'){

	if(isset($_REQUEST["sel"]) && $_REQUEST["sel"] && isset($_REQUEST["we_target"])){

		$targetDirectroy = $_REQUEST["we_target"];
		// list of all item names which should be moved
		$items2move = array();

		// list of the selected items
		$selectedItems = explode(",", $_REQUEST["sel"]);
		$retVal = 1;
		foreach($selectedItems as $selectedItem){

			// check if user is allowed to move this item
			if(!checkIfRestrictUserIsAllowed($selectedItem, $table)){
				$retVal = -1;
				break;
			}

			// check if item could be moved to the target directory
			$check = checkMoveItem($targetDirectroy, $selectedItem, $table, $items2move);
			switch($check){
				case 1 :
					break;
				case -1 :
					$message = g_l('alert', "[move_nofolder]");
					$retVal = 0;
					break;
				case -2 :
					$message = g_l('alert', "[move_duplicate]");
					$retVal = 0;
					break;
				case -3 :
					$message = g_l('alert', "[move_onlysametype]");
					$retVal = 0;
					break;
				default :
					break;
			}
			if(!$check){
				break;
			}
		}

		if($retVal == -1){ //	not allowed to move document
			$script .= 'top.toggleBusy(0);' .
				we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[noRightsToMove]"), id_to_path($selectedItem, $table)), we_message_reporting::WE_MESSAGE_ERROR);
		} else
		if($retVal){ //	move files !
			$notMovedItems = array();
			foreach($selectedItems as $selectedItem){
				moveItem($targetDirectroy, $selectedItem, $table, $notMovedItems);
			}

			if($_SESSION['weS']['we_mode'] == "normal"){ //	only update tree when in normal mode
				$script .= moveTreeEntries($table == OBJECT_FILES_TABLE);
			}

			$script .= "top.toggleBusy(0);";
			if($_SESSION['weS']['we_mode'] == "normal"){ //	different messages in normal or seeMode
				if(!empty($notMovedItems)){
					$_SESSION['weS']['move_files_nok'] = array();
					$_SESSION["move_files_info"] = str_replace("\\n", "", sprintf(g_l('alert', "[move_of_files_failed]"), ""));
					foreach($notMovedItems as $item){
						$_SESSION['weS']['move_files_nok'][] = array(
							"icon" => $item['Icon'], "path" => $item['Path']
						);
					}
					$script .= 'new jsWindow("' . WEBEDITION_DIR . 'moveInfo.php","we_moveinfo",-1,-1,550,550,true,true,true);' . "\n";
				} else{
					$script .= we_message_reporting::getShowMessageCall(g_l('alert', "[move_ok]"), we_message_reporting::WE_MESSAGE_NOTICE);
				}
			}
		} else{
			$script .= 'top.toggleBusy(0);' .
				we_message_reporting::getShowMessageCall($message, we_message_reporting::WE_MESSAGE_ERROR);
		}
	} elseif(!isset($_REQUEST["we_target"]) || !$_REQUEST["we_target"]){
		$script .= 'top.toggleBusy(0);' .
			we_message_reporting::getShowMessageCall(g_l('alert', "[move_no_dir]"), we_message_reporting::WE_MESSAGE_ERROR);
	} else{
		$script .= 'top.toggleBusy(0);' .
			we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_move]"), we_message_reporting::WE_MESSAGE_ERROR);
	}
	print we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsElement($script);
	//exit;
}

//	in seeMode return to startDocument ...


if($_SESSION['weS']['we_mode'] == "seem"){
	$js = ($retVal ? //	document moved -> go to seeMode startPage
			we_message_reporting::getShowMessageCall(g_l('alert', '[move_single][return_to_start]'), we_message_reporting::WE_MESSAGE_NOTICE) . ";top.we_cmd('start_multi_editor');" :
			we_message_reporting::getShowMessageCall(g_l('alert', '[move_single][no_delete]'), we_message_reporting::WE_MESSAGE_ERROR));

	print we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead(we_html_element::jsElement($js)));
	exit();
}

we_html_tools::htmlTop();

print STYLESHEET .
	$yuiSuggest->getYuiJsFiles();
?>
<script type="text/javascript"><!--
	top.treeData.setstate(top.treeData.tree_states["selectitem"]);
	if(top.treeData.table != "<?php
print $table;
?>"){
	top.treeData.table = "<?php
print $table;
?>";
	we_cmd("load","<?php
print $table;
?>");
}else{
	we_cmd("load","<?php
print $table;
?>");
	top.drawTree();
}

function press_ok_move() {

	var sel = "";
	for(var i=1;i<=top.treeData.len;i++){
		if(top.treeData[i].checked==1) sel += (top.treeData[i].id+",");
	}
	if(!sel){
		top.toggleBusy(0);
<?php print we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_move]'), we_message_reporting::WE_MESSAGE_ERROR) ?>
		return;
	}

	// check if selected target exists
	var acStatus = '';
	var invalidAcFields = false;
	acStatus = YAHOO.autocoml.checkACFields();
	acStatusType = typeof acStatus;
	if(acStatusType.toLowerCase() == 'object') {
		if(acStatus.running) {
			setTimeout('press_ok_move()',100);
			return;
		} else if(!acStatus.valid) {
<?php print we_message_reporting::getShowMessageCall(g_l('weClass', "[notValidFolder]"), we_message_reporting::WE_MESSAGE_ERROR) ?>
			return;
		}
	}

	// close all documents before moving.


	// no open document can be moved
	// close all Editors with deleted documents
	var _usedEditors =  top.weEditorFrameController.getEditorsInUse();

	var _move_table = "<?php
print $table;
?>";
	var _move_ids = "," + sel;

	var _open_move_editors = new Array();

	for ( frameId in _usedEditors ) {
		if ( _move_table == _usedEditors[frameId].getEditorEditorTable() ) {
			_open_move_editors.push( _usedEditors[frameId] );
		}
	}

	if ( _open_move_editors.length ) {

		_openDocs_Str = "";

		for ( i=0; i<_open_move_editors.length;i++ ) {
			_openDocs_Str += "- " + _open_move_editors[i].getEditorDocumentPath() + "\n";

		}
<?php
switch($table){
	case TEMPLATES_TABLE:
		$_type = g_l('global', "[templates]");
		break;
	case defined("OBJECT_TABLE") ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE':
		$_type = g_l('global', "[objects]");
		break;
	default:
		$_type = g_l('global', "[documents]");
		break;
}
?>
		if ( confirm("<?php
printf(g_l('alert', "[move_exit_open_docs_question]"), $_type, $_type);
?>" + _openDocs_Str + "\n<?php
print g_l('alert', "[move_exit_open_docs_continue]");
?>") ) {

		for ( i=0; i<_open_move_editors.length;i++ ) {
			_open_move_editors[i].setEditorIsHot(false);
			top.weEditorFrameController.closeDocument( _open_move_editors[i].getFrameId() );

		}
		we_cmd('do_move','','<?php
print $table;
?>');
	}

} else {

	if(confirm('<?php
print g_l('alert', "[move]");
?>')) {
	we_cmd('do_move','','<?php
print $table;
?>');
}
}
}

function we_submitForm(target,url){
var f = self.document.we_form;
var sel = "";
for(var i=1;i<=top.treeData.len;i++){
if(top.treeData[i].checked==1) sel += (top.treeData[i].id+",");
}
if(!sel){
top.toggleBusy(0);
<?php print we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_move]'), we_message_reporting::WE_MESSAGE_ERROR) ?>
return;
}

sel = sel.substring(0,sel.length-1);

f.sel.value = sel;
f.target = target;
f.action = url;
f.method = "post";
f.submit();
}
function we_cmd(){
var args = "";
for(var i = 0; i < arguments.length; i++){
args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
}

eval('parent.we_cmd('+args+')');
}
//-->
</script>
<?php
if($_REQUEST['we_cmd'][0] == "do_move"){
	print "</head><body></body></html>";
	exit();
}


$ws_Id = get_def_ws($table);

if($ws_Id){
	$ws_path = id_to_path($ws_Id, $table);
} else{
	$ws_Id = '0';
	$ws_path = '/';
}

$textname = 'we_targetname';
$idname = 'we_target';

$yuiSuggest->setAcId("Dir");
$yuiSuggest->setContentType("folder");
$yuiSuggest->setInput($textname, $ws_path);
$yuiSuggest->setMaxResults(4);
$yuiSuggest->setMayBeEmpty(false);
$yuiSuggest->setResult(trim($idname), $ws_Id);
$yuiSuggest->setSelector("Dirselector");
$yuiSuggest->setTable($table);
$yuiSuggest->setWidth(250);
$yuiSuggest->setContainerWidth(360);
$wecmdenc1 = we_cmd_enc("top.rframe.treeheader.document.we_form.elements." . $idname . ".value");
$wecmdenc2 = we_cmd_enc("top.rframe.treeheader.document.we_form.elements." . $textname . ".value");
$yuiSuggest->setSelectButton(
	we_button::create_button("select", "javascript:we_cmd('openDirselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','" . session_id() . "',0)"), 10);

$weAcSelector = $yuiSuggest->getHTML();

$content = '<span class="middlefont" style="padding-right:5px;padding-bottom:10px;">' . g_l('newFile', "[move_text]") . '</span>
			<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weAcSelector . '</p>';

$_buttons = we_button::position_yes_no_cancel(
		we_button::create_button("ok", "javascript:press_ok_move();"), "", we_button::create_button("quit_move", "javascript:we_cmd('exit_move','','$table')"), 10, "left");

$form = we_html_tools::hidden("sel", "") . '</form>';



print
	'</head><body class="weTreeHeaderMove">
<form name="we_form" method="post" onsubmit="return false">
<div style="width:460px;">
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(
		g_l('newFile', "[title_move]")) . '</h1>
<p class="small">' . $content . '</p>
<div>' . $_buttons . '</div></div>' . $form .
	$yuiSuggest->getYuiCss() .
	$yuiSuggest->getYuiJs() .
	'</body>
</html>';
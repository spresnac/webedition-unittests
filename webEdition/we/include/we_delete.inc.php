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
include_once (WE_INCLUDES_PATH . 'we_delete_fn.inc.php');

we_html_tools::protect();
we_html_tools::htmlTop();
print STYLESHEET;

function getObjectsForDocWorkspace($id){
	$ids = (is_array($id)) ? $id : array($id);
	$db = new DB_WE();

	if(!defined('OBJECT_FILES_TABLE')){
		return array();
	}

	$where = array();
	foreach($ids as $id){
		$where[] = 'Workspaces LIKE "%,' . $id . ',%"';
		$where[] = 'ExtraWorkspaces LIKE "%,' . $id . ',%"';
	}

	$out = array();
	$db->query('SELECT ID,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ' . implode(' OR ', $where));

	while($db->next_record()) {
		$out[$db->f('ID')] = $db->f('Path');
	}

	return $out;
}

$table = $_REQUEST['we_cmd'][2];
$wfchk = defined("WORKFLOW_TABLE") && ($table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE)) ?
	(isset($_REQUEST['we_cmd'][3]) ?
		$_REQUEST['we_cmd'][3] :
		0) :
	1;
$wfchk_html = "";
$script = "";

if(!$wfchk){

	if(isset($_REQUEST["sel"])){
		$found = false;
		$selectedItems = explode(",", $_REQUEST["sel"]);
		foreach($selectedItems as $selectedItem){
			if(we_workflow_utility::inWorkflow($selectedItem, $table)){
				$found = true;
				break;
			}
		}
		$wfchk_html .= we_html_element::jsElement(
				'function confirmDel(){' .
				($found ? 'if(confirm("' . g_l('alert', "[found_in_workflow]") . '")){' : '') .
				'we_cmd("' . $_REQUEST['we_cmd'][0] . '","","' . $table . '",1);' .
				($found ? '}else{ top.toggleBusy(0)}' : '') .
				'}');
	} else{
		$script = "top.toggleBusy(0);" . we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_delete]"), we_message_reporting::WE_MESSAGE_WARNING);
	}
	$wfchk_html .= '</head><body onload="confirmDel()"><form name="we_form" method="post">' .
		we_html_tools::hidden("sel", isset($_REQUEST["sel"]) ? $_REQUEST["sel"] : "") . "</form>";
} else
if($_REQUEST['we_cmd'][0] == "do_delete" || $_REQUEST['we_cmd'][0] == "delete_single_document"){
	if(isset($_REQUEST["sel"]) && $_REQUEST["sel"]){
		//	look which documents must be deleted.
		$selectedItems = explode(',', $_REQUEST["sel"]);
		$retVal = 1;
		$idInfos = array(
			'IsFolder' => 0, 'Path' => '', 'hasFiles' => 0
		);
		if(!empty($_REQUEST["sel"]) && !empty($selectedItems) && ($table == FILE_TABLE || $table == TEMPLATES_TABLE)){
			$idInfos = getHash('SELECT IsFolder, Path FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($selectedItems[0]), $DB_WE);
			if(empty($idInfos)){
				t_e('ID ' . $selectedItems[0] . ' not present in table ' . $table);
			} elseif($idInfos['IsFolder']){
				$idInfos['hasFiles'] = f('SELECT ID FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($selectedItems[0]) . " AND  IsFolder = 0 AND Path LIKE '" . $DB_WE->escape($idInfos['Path']) . "%'", 'ID', $DB_WE) > 0 ? 1 : 0;
			}
		}

		$hasPerm = 0;
		if(we_hasPerm("ADMINISTRATOR")){
			$hasPerm = 1;
		} else{
			switch($table){
				case FILE_TABLE:
					if(($idInfos['IsFolder'] && we_hasPerm("DELETE_DOC_FOLDER") && !$idInfos['hasFiles']) || (!$idInfos['IsFolder'] && we_hasPerm("DELETE_DOCUMENT"))){
						$hasPerm = 1;
					}
					break;
				case TEMPLATES_TABLE:
					if(($idInfos['IsFolder'] && we_hasPerm("DELETE_TEMP_FOLDER") && !$idInfos['hasFiles']) || (!$idInfos['IsFolder'] && we_hasPerm("DELETE_TEMPLATE"))){
						$hasPerm = 1;
					}
					break;
				case OBJECT_FILES_TABLE:
					if(we_hasPerm("DELETE_OBJECTFILE")){
						$hasPerm = 1;
					}
					break;
				case OBJECT_TABLE:
					if($idInfos['IsFolder'] && we_hasPerm("DELETE_OBJECT")){
						$hasPerm = 1;
					}
					break;
			}
		}
		unset($idInfos);

		if(!$hasPerm){
			$retVal = -6;
		} else
		if((!we_hasPerm("ADMINISTRATOR")) && ($table == FILE_TABLE . "_cache" || $table == OBJECT_FILES_TABLE . "_cache")){ //check if mey delete cache
			$retVal = -1;
		} else{

			foreach($selectedItems as $selectedItem){
				if(!checkIfRestrictUserIsAllowed($selectedItem, $table)){
					$retVal = -1;
					break;
				}

				if(!checkDeleteEntry($selectedItem, $table)){
					$retVal = 0;
					break;
				}
			}
		}

		if($retVal == 1){ // only if no error occurs
			foreach($selectedItems as $selectedItem){

				if($table == FILE_TABLE && defined('USER_TABLE')){
					$users = we_users_util::getUsersForDocWorkspace($selectedItem);
					if(count($users) > 0){
						$retVal = -2;
						break;
					}

					// check if childrenfolders are workspaces
					$childs = array();

					pushChilds($childs, $selectedItem, $table, true);
					$users = array();
					foreach($childs as $ch){
						$users = array_merge($users, we_users_util::getUsersForDocWorkspace($childs));
					}
					$users = array_unique($users);

					if(count($users)){
						$retVal = -4;
						break;
					}
				}

				if($table == TEMPLATES_TABLE && defined('USER_TABLE')){
					$users = we_users_util::getUsersForDocWorkspace($selectedItem, "workSpaceTmp");
					if(count($users) > 0){
						$retVal = -2;
						break;
					}

					// check if childrenfolders are workspaces
					$childs = array();

					pushChilds($childs, $selectedItem, $table, true);
					$users = array();
					foreach($childs as $ch){
						$users = array_merge($users, we_users_util::getUsersForDocWorkspace($childs, "workSpaceTmp"));
					}
					$users = array_unique($users);

					if(!empty($users)){
						$retVal = -4;
						break;
					}
				}

				if(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE && defined('USER_TABLE')){

					$users = we_users_util::getUsersForDocWorkspace($selectedItem, "workSpaceObj");
					if(count($users) > 0){
						$retVal = -2;
						break;
					}

					$childs = array();

					pushChilds($childs, $selectedItem, $table, true);
					$users = we_users_util::getUsersForDocWorkspace($childs, "workSpaceObj");

					if(count($users)){
						$retVal = -4;
						break;
					}
				}
				if(defined("OBJECT_FILES_TABLE") && $table == FILE_TABLE){
					$objects = getObjectsForDocWorkspace($selectedItem);
					if(count($objects) > 0){
						$retVal = -3;
						break;
					}

					$childs = array();

					pushChilds($childs, $selectedItem, $table, true);
					$objects = getObjectsForDocWorkspace($childs);

					if(count($objects)){
						$retVal = -5;
						break;
					}
				}
			}
		}

		switch($retVal){
			case -6:
				$script .= 'top.toggleBusy(0);' .
					we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms_action]'), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -5: //	not allowed to delete workspace
				$script .= 'top.toggleBusy(0);';
				$objList = '';
				foreach($objects as $val){
					$objList .= '- ' . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_object_r]'), id_to_path($selectedItem, $table), $objList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -4: //	not allowed to delete workspace
				$script .= 'top.toggleBusy(0);';
				$usrList = '';
				foreach($users as $val){
					$usrList .= '- ' . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_user_r]'), id_to_path($selectedItem, $table), $usrList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -3: //	not allowed to delete workspace
				$script .= 'top.toggleBusy(0);';
				$objList = '';
				foreach($objects as $val){
					$objList .= "- " . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_object]'), id_to_path($selectedItem, $table), $objList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -2: //	not allowed to delete workspace
				$script .= 'top.toggleBusy(0);';
				$usrList = '';
				foreach($users as $val){
					$usrList .= '- ' . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_user]'), id_to_path($selectedItem, $table), $usrList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -1: //	not allowed to delete document
				$script .= 'top.toggleBusy(0);' .
					we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[noRightsToDelete]"), id_to_path($selectedItem, $table)), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			default:
				if($retVal){ //	user may delete -> delete files !
					$GLOBALS["we_folder_not_del"] = array();

					$deletedItems = array();

					foreach($selectedItems as $sel){
						deleteEntry($sel, $table);
					}

					if($_SESSION['weS']['we_mode'] == "normal"){ //	only update tree when in normal mode
						$script .= deleteTreeEntries(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE);
					}

					if(count($deletedItems)){

						$class_condition = '';
						$deleted_objects = array();

						if(defined("OBJECT_TABLE") && $table == OBJECT_TABLE){ // close all open objects, if a class is deleted
							$_deletedItems = array();

							// if its deleted and not selected, it must be an object
							foreach($deletedItems as $cur){
								if(in_array($cur, $selectedItems)){
									$_deletedItems[] = $cur;
								} else{
									$deleted_objects[] = $cur; // deleted objects when classes are deleted
								}
							}
							$deletedItems = $_deletedItems;
							$class_condition = ' || (_usedEditors[frameId].getEditorEditorTable() == "' . OBJECT_FILES_TABLE . '" && (_delete_objects.indexOf( "," + _usedEditors[frameId].getEditorDocumentId() + "," ) != -1) ) ';
						}

						if(defined("CUSTOMER_TABLE")){ // delete the customerfilters
							weDocumentCustomerFilter::deleteModel(
								$deletedItems, $table);
							if(defined("OBJECT_FILES_TABLE") && $table == OBJECT_TABLE){
								if(!empty($deleted_objects)){
									weDocumentCustomerFilter::deleteModel(
										$deleted_objects, OBJECT_FILES_TABLE);
								}
							}
						}

						we_history::deleteFromHistory(
							$deletedItems, $table);
						if(defined("OBJECT_FILES_TABLE") && $table == OBJECT_TABLE){
							if(!empty($deleted_objects)){
								we_history::deleteFromHistory(
									$deleted_objects, OBJECT_FILES_TABLE);
							}
						}

						$script .= '
						// close all Editors with deleted documents
						var _usedEditors =  top.weEditorFrameController.getEditorsInUse();

						// if a class is deleted, close all open objects of this class
						var _delete_table = "' . $table . '";
						var _delete_Ids = ",' . implode(",", $deletedItems) . ',";
						var _delete_objects = ",' . implode(",", $deleted_objects) . ',";

						for ( frameId in _usedEditors ) {

							if ( _delete_table == _usedEditors[frameId].getEditorEditorTable() && (_delete_Ids.indexOf( "," + _usedEditors[frameId].getEditorDocumentId() + "," ) != -1)
								' . $class_condition . '
								) {
								_usedEditors[frameId].setEditorIsHot(false);
								top.weEditorFrameController.closeDocument(frameId);
							}
						}
					';
					}


					$script .= 'top.toggleBusy(0);';

					if($_SESSION['weS']['we_mode'] == 'normal'){ //	different messages in normal or seeMode
						if(!empty($GLOBALS['we_folder_not_del'])){
							$_SESSION['weS']['delete_files_nok'] = array();
							$_SESSION['delete_files_info'] = str_replace('\n', '', sprintf(g_l('alert', '[folder_not_empty]'), ''));
							foreach($GLOBALS["we_folder_not_del"] as $datafile){
								$_SESSION['weS']['delete_files_nok'][] = array('icon' => we_base_ContentTypes::FOLDER_ICON, "path" => $datafile);
							}
							$script .= 'new jsWindow("' . WEBEDITION_DIR . 'delInfo.php","we_delinfo",-1,-1,550,550,true,true,true);';
						} else{
							$delete_ok = g_l('alert', '[delete_ok]');
							$script .= we_message_reporting::getShowMessageCall($delete_ok, we_message_reporting::WE_MESSAGE_NOTICE);
						}
					}
				} else{
					$script .= 'top.toggleBusy(0);';
					switch($table){
						case TEMPLATES_TABLE:
							$script .= we_message_reporting::getShowMessageCall(g_l('alert', "[deleteTempl_notok_used]"), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						case OBJECT_TABLE:
							$script .= we_message_reporting::getShowMessageCall(g_l('alert', "[deleteClass_notok_used]"), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						default:
							$script .= we_message_reporting::getShowMessageCall(g_l('alert', "[delete_notok]"), we_message_reporting::WE_MESSAGE_ERROR);
					}
				}
		}
	} else{
		$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_delete]"), we_message_reporting::WE_MESSAGE_WARNING);
	}
	print we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsElement($script);

	//exit;
}

//	in seeMode return to startDocument ...


if($_SESSION['weS']['we_mode'] == "seem"){
	print we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead(we_html_element::jsElement(
					($retVal ? //	document deleted -> go to seeMode startPage
						we_message_reporting::getShowMessageCall(g_l('alert', '[delete_single][return_to_start]'), we_message_reporting::WE_MESSAGE_NOTICE) . "top.we_cmd('start_multi_editor');" :
						we_message_reporting::getShowMessageCall(g_l('alert', '[delete_single][no_delete]'), we_message_reporting::WE_MESSAGE_ERROR))
				)));
	exit();
}
?>
<script type="text/javascript"><!--
<?php
if($_REQUEST['we_cmd'][0] != "delete_single_document"){ // no select mode in delete_single_document
	switch($table){
		/* case FILE_TABLE . "_cache":
		  if(we_hasPerm("ADMINISTRATOR")){
		  print 'top.treeData.setstate(top.treeData.tree_states["selectitem"]);';
		  }
		  break; */
		case FILE_TABLE:
			if(we_hasPerm("DELETE_DOC_FOLDER") && we_hasPerm("DELETE_DOCUMENT")){
				print 'top.treeData.setstate(top.treeData.tree_states["select"]);';
			} elseif(we_hasPerm("DELETE_DOCUMENT")){
				print 'top.treeData.setstate(top.treeData.tree_states["selectitem"]);';
			}
			break;
		case TEMPLATES_TABLE:
			if(we_hasPerm("DELETE_TEMP_FOLDER") && we_hasPerm("DELETE_TEMPLATE")){
				print 'top.treeData.setstate(top.treeData.tree_states["select"]);';
			} elseif(we_hasPerm("DELETE_TEMPLATE")){
				print 'top.treeData.setstate(top.treeData.tree_states["selectitem"]);';
			}
			break;
		case (defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : 1):
			if(we_hasPerm("DELETE_OBJECTFILE")){
				print 'top.treeData.setstate(top.treeData.tree_states["select"]);';
			}
			break;
		case (defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE . "_cache" : 2):
			if(we_hasPerm("ADMINISTRATOR")){
				print 'top.treeData.setstate(top.treeData.tree_states["selectitem"]);';
			}
			break;
		default:
			print 'top.treeData.setstate(top.treeData.tree_states["selectitem"]);';
	}
}
?>
	if(top.treeData.table != "<?php
print preg_replace('#_cache$#', '', $table);
?>"){
	top.treeData.table = "<?php
print preg_replace('#_cache$#', '', $table);
?>";
	we_cmd("load","<?php
print preg_replace('#_cache$#', '', $table);
?>");
}else{
	top.drawTree();
}

function we_submitForm(target,url){
	var f = self.document.we_form;
	var sel = "";
	for(var i=1;i<=top.treeData.len;i++){
		if(top.treeData[i].checked==1) sel += (top.treeData[i].id+",");
	}
	if(!sel){
		top.toggleBusy(0);
<?php
print we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_delete]"), we_message_reporting::WE_MESSAGE_ERROR);
?>
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
	eval('top.we_cmd('+args+')');
}
//-->
</script>
<?php
if(!$wfchk && $_REQUEST['we_cmd'][0] != "delete"){
	print $wfchk_html;
	exit();
}
if($_REQUEST['we_cmd'][0] == "do_delete"){
	print '</head><body></body></html>';
	exit();
}


if((defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE . "_cache") || (defined("FILE_TABLE") && $table == FILE_TABLE . "_cache")){
	$delete_text = g_l('newFile', "[delete_text_cache]");
	$delete_confirm = g_l('alert', "[delete_cache]");
} else{
	$delete_text = g_l('newFile', "[delete_text]");
	$delete_confirm = g_l('alert', "[delete]");
}
$content = '<span class="middlefont">' . $delete_text . '</span>';

$_buttons = we_button::position_yes_no_cancel(
		we_button::create_button("ok", "javascript:if(confirm('" . $delete_confirm . "')) we_cmd('do_delete','','$table')"), "", we_button::create_button("quit_delete", "javascript:we_cmd('exit_delete','','$table')"), 10, "left");

$form = '<form name="we_form" method="post">' . we_html_tools::hidden('sel', '') . '</form>';

print '</head><body class="weTreeHeader">
<div style="width:380px;">
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(g_l('newFile', "[title_delete]")) . '</h1>
<p class="small">' . $content . '</p>
<div>' . $_buttons . '</div></div>' . $form . '
</body>
</html>';
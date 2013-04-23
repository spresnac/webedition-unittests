<?php
/**
 * webEdition CMS
 *
 * $Rev: 5701 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 14:30:37 +0100 (Sat, 02 Feb 2013) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::htmlTop();
we_html_tools::protect();

if(isset($_REQUEST["ucmd"])){
	switch($_REQUEST["ucmd"]){
		case "new_group":
			if(!we_hasPerm("NEW_GROUP")){
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
				break;
			}

			$user_object = new we_user();

			if(isset($_REQUEST["cgroup"]) && $_REQUEST["cgroup"]){
				$user_group = new we_user();
				if($user_group->initFromDB($_REQUEST["cgroup"])){
					$user_object->ParentID = $_REQUEST["cgroup"];
				}
			}

			$user_object->initType(we_user::TYPE_USER_GROUP);

			$_SESSION["user_session_data"] = $user_object->getState();

			print we_html_element::jsElement('
top.content.user_resize.user_right.user_editor.user_edheader.location="' . WE_USERS_MODULE_DIR . 'edit_users_edheader.php";
top.content.user_resize.user_right.user_editor.user_properties.location="' . WE_USERS_MODULE_DIR . 'edit_users_properties.php";
top.content.user_resize.user_right.user_editor.user_edfooter.location="' . WE_USERS_MODULE_DIR . 'edit_users_edfooter.php";');
			break;

		case "new_alias":
			if(!we_hasPerm("NEW_USER")){
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
				break;
			}

			$user_object = new we_user();

			if(isset($_REQUEST["cgroup"]) && $_REQUEST["cgroup"]){
				$user_group = new we_user();
				if($user_group->initFromDB($_REQUEST["cgroup"])){
					$user_object->ParentID = $_REQUEST["cgroup"];
				}
			}

			$user_object->initType(we_user::TYPE_ALIAS);

			$_SESSION["user_session_data"] = $user_object->getState();
			print we_html_element::jsElement('
top.content.user_resize.user_right.user_editor.user_edheader.location="' . WE_USERS_MODULE_DIR . 'edit_users_edheader.php";
top.content.user_resize.user_right.user_editor.user_properties.location="' . WE_USERS_MODULE_DIR . 'edit_users_properties.php";
top.content.user_resize.user_right.user_editor.user_edfooter.location="' . WE_USERS_MODULE_DIR . 'edit_users_edfooter.php";');
			break;

		case "search":
			print we_html_element::jsElement('
                    top.content.user_resize.user_right.user_editor.user_properties.location="' . WE_USERS_MODULE_DIR . 'edit_users_sresults.php?kwd=' . $_REQUEST["kwd"] . '";
                ');
			break;

		case "display_alias":
			if($uid && $ctype && $ctable){
				print we_html_element::jsElement('
top.content.usetHot();
top.content.user_resize.user_right.user_editor.user_edheader.location="' . WE_USERS_MODULE_DIR . 'edit_users_edheader.php?uid=".$uid."&ctype=".ctype."&ctable=".$ctable;
top.content.user_resize.user_right.user_editor.user_properties.location="' . WE_USERS_MODULE_DIR . 'edit_users_properties.php?uid=".$uid."&ctype=".ctype."&ctable=".$ctable;
top.content.user_resize.user_right.user_editor.user_edfooter.location="' . WE_USERS_MODULE_DIR . 'edit_users_edfooter.php?uid=".$uid."&ctype=".ctype."&ctable=".$ctable;');
			}
			break;

		case "new_user":
			if(!we_hasPerm("NEW_USER")){
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
				break;
			}
			$user_object = new we_user();

			if(isset($_REQUEST["cgroup"]) && $_REQUEST["cgroup"]){
				$user_group = new we_user();
				if($user_group->initFromDB($_REQUEST["cgroup"])){
					$user_object->ParentID = $_REQUEST["cgroup"];
				}
			}
			$user_object->initType(we_user::TYPE_USER);

			$_SESSION["user_session_data"] = $user_object->getState();
			print we_html_element::jsElement('
top.content.user_resize.user_right.user_editor.user_edheader.location="' . WE_USERS_MODULE_DIR . 'edit_users_edheader.php";
top.content.user_resize.user_right.user_editor.user_properties.location="' . WE_USERS_MODULE_DIR . 'edit_users_properties.php?oldtab=0";
top.content.user_resize.user_right.user_editor.user_edfooter.location="' . WE_USERS_MODULE_DIR . 'edit_users_edfooter.php";');
			break;
		case "display_user":
			if($_REQUEST["uid"]){
				$user_object = new we_user();
				$user_object->initFromDB($_REQUEST['uid']);
				if(!we_hasPerm("ADMINISTRATOR") && $user_object->checkPermission("ADMINISTRATOR")){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					$user_object = new we_user();
					break;
				}

				$_SESSION["user_session_data"] = $user_object->getState();
				$setgroup = "";
				if($user_object->Type == 1){
					$setgroup = 'top.content.cgroup=' . $user_object->ID . ";\n";
				}
				print we_html_element::jsElement('
top.content.usetHot();
' . $setgroup . '
top.content.user_resize.user_right.user_editor.user_edheader.location="' . WE_USERS_MODULE_DIR . 'edit_users_edheader.php";
top.content.user_resize.user_right.user_editor.user_properties.location="' . WE_USERS_MODULE_DIR . 'edit_users_properties.php?oldtab=0";
top.content.user_resize.user_right.user_editor.user_edfooter.location="' . WE_USERS_MODULE_DIR . 'edit_users_edfooter.php";');
			}
			break;
		case "save_user":
			$isAcError = false;
			$weAcQuery = new weSelectorQuery();

			// bugfix #1665 for php 4.1.2: "-" moved to the end of the regex-pattern
			if(isset($_REQUEST[$_REQUEST['obj_name'] . '_username']) && !preg_match("|^[A-Za-z0-9._-]+$|", $_REQUEST[$_REQUEST['obj_name'] . '_username'])){
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('global', "[username_wrong_chars]"), we_message_reporting::WE_MESSAGE_ERROR));
				break;
			}
			if(!isset($_SESSION["user_session_data"])){
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR));
				break;
			}
			if(isset($_REQUEST[$_REQUEST['obj_name'] . '_ParentID']) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_ParentID']) && $_REQUEST[$_REQUEST['obj_name'] . '_ParentID'] > 0){
				$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_ParentID'], USER_TABLE, array("IsFolder"), false);
				if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
			}
			$i = 0;
			while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . FILE_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . FILE_TABLE . '_' . $i])) {
				$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . FILE_TABLE . '_' . $i], FILE_TABLE, array("IsFolder"));
				if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
					$isAcError = true;
					break;
				}
				$i++;
			}
			$i = 0;
			while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . TEMPLATES_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . TEMPLATES_TABLE . '_' . $i])) {
				$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . TEMPLATES_TABLE . '_' . $i], TEMPLATES_TABLE, array("IsFolder"));
				if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
					$isAcError = true;
					break;
				}
				$i++;
			}
			$i = 0;
			while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NAVIGATION_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NAVIGATION_TABLE . '_' . $i])) {
				$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NAVIGATION_TABLE . '_' . $i], NAVIGATION_TABLE, array("IsFolder"));
				if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
					$isAcError = true;
					break;
				}
				$i++;
			}
			if(defined("OBJECT_FILES_TABLE")){
				while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . OBJECT_FILES_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . OBJECT_FILES_TABLE . '_' . $i])) {
					$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . OBJECT_FILES_TABLE . '_' . $i], OBJECT_FILES_TABLE, array("IsFolder"));
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						$isAcError = true;
						break;
					}
					$i++;
				}
			}

			if(defined("NEWSLETTER_TABLE")){
				while(isset($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NEWSLETTER_TABLE . '_' . $i]) && !empty($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NEWSLETTER_TABLE . '_' . $i])) {
					$weAcResult = $weAcQuery->getItemById($_REQUEST[$_REQUEST['obj_name'] . '_Workspace_' . NEWSLETTER_TABLE . '_' . $i], NEWSLETTER_TABLE, array("IsFolder"));
					if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
						$isAcError = true;
						break;
					}
					$i++;
				}
			}

			if($isAcError){
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[workspaceFieldError]"), we_message_reporting::WE_MESSAGE_ERROR));
				break;
			}
			$user_object = new we_user();
			$user_object->setState($_SESSION["user_session_data"]);

			if(!we_hasPerm("ADMINISTRATOR") && $user_object->checkPermission("ADMINISTRATOR")){
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
				$user_object = new we_user();
				break;
			}
			$oldperm = $user_object->checkPermission("ADMINISTRATOR");
			if($user_object){

				if(!we_hasPerm("SAVE_USER") && ($user_object->Type == we_user::TYPE_USER || $user_object->Type == we_user::TYPE_ALIAS) && $user_object->ID != 0){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(!we_hasPerm("NEW_USER") && ($user_object->Type == we_user::TYPE_USER || $user_object->Type == we_user::TYPE_ALIAS) && $user_object->ID == 0){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(!we_hasPerm("SAVE_GROUP") && $user_object->Type == we_user::TYPE_USER_GROUP && $user_object->ID != 0){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(!we_hasPerm("NEW_GROUP") && $user_object->Type == we_user::TYPE_USER_GROUP && $user_object->ID == 0){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(isset($_REQUEST["oldtab"])){
					$user_object->preserveState(intval($_REQUEST["oldtab"]), $_REQUEST["old_perm_branch"]);
				}

				$id = $user_object->ID;
				if($user_object->username == '' && $user_object->Type != we_user::TYPE_ALIAS){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[username_empty]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($user_object->Alias == 0 && $user_object->Type == we_user::TYPE_ALIAS){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[username_empty]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				$exist = (f('SELECT 1 AS a FROM ' . USER_TABLE . ' WHERE ID!=' . intval($user_object->ID) . " AND username='" . $user_object->username . "'", 'a', $GLOBALS['DB_WE']) == '1');
				if($exist && $user_object->Type != we_user::TYPE_ALIAS){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[username_exists]"), $user_object->username), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(($oldperm) && (!$user_object->checkPermission("ADMINISTRATOR")) && ($user_object->isLastAdmin())){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[modify_last_admin]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($user_object->ID){
					$foo = getHash('SELECT ParentID FROM ' . USER_TABLE . ' WHERE ID=' . intval($user_object->ID), $user_object->DB_WE);
				} else{
					$foo = array(
						"ParentID" => 0
					);
				}
				$ret = $user_object->saveToDB();
				$_SESSION["user_session_data"] = $user_object->getState();

				//	Save seem_startfile to DB when needed.
				if(isset($_REQUEST["seem_start_file"])){
					if(($_REQUEST["seem_start_file"] && $_REQUEST["seem_start_file"] != 0) || (isset($_SESSION["save_user_seem_start_file"][$_REQUEST["uid"]]))){
						$tmp = new DB_WE();

						if(isset($_REQUEST["seem_start_file"])){
							//	save seem_start_file from REQUEST
							$seem_start_file = $_REQUEST["seem_start_file"];
							if($user_object->ID == $_SESSION['user']['ID']){ // change preferences if user edits his own startfile
								$_SESSION['prefs']['seem_start_file'] = $seem_start_file;
							}
						} else{
							//	Speichere seem_start_file aus SESSION
							$seem_start_file = $_SESSION["save_user_seem_start_file"][$_REQUEST["uid"]];
						}

						$tmp->query('REPLACE INTO ' . PREFS_TABLE . ' SET userID=' . intval($_REQUEST['uid']).',`key`="seem_start_file",`value`="' . $tmp->escape($seem_start_file).'"');
						unset($tmp);
						unset($seem_start_file);
						if(isset($_SESSION["save_user_seem_start_file"][$_REQUEST["uid"]])){
							unset($_SESSION["save_user_seem_start_file"][$_REQUEST["uid"]]);
						}
					}
				}

				if($ret == -5){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[user_path_nok]"), we_message_reporting::WE_MESSAGE_ERROR));
				} else{
					if($id){
						$tree_code = 'top.content.updateEntry(' . $user_object->ID . ',' . $user_object->ParentID . ',"' . $user_object->Text . '",' . ($user_object->checkPermission("ADMINISTRATOR") ? 1 : 0) . ',' . ($user_object->LoginDenied ? 1 : 0) . ');';
					} else{
						$tree_code = 'top.content.makeNewEntry("user.gif",' . $user_object->ID . ',' . $user_object->ParentID . ',"' . $user_object->Text . '",false,"' . (($user_object->Type == we_user::TYPE_USER_GROUP) ? ("folder") : (($user_object->Type == we_user::TYPE_ALIAS) ? ("alias") : ("user"))) . '","' . USER_TABLE . '",' . ($user_object->checkPermission("ADMINISTRATOR") ? 1 : 0) . ',' . ($user_object->LoginDenied ? 1 : 0) . ');';
					}

					switch($user_object->Type){
						case we_user::TYPE_ALIAS:
							$savemessage = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[alias_saved_ok]"), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
							break;
						case we_user::TYPE_USER_GROUP:
							$savemessage = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[group_saved_ok]"), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
							break;
						case we_user::TYPE_USER:
						default:
							$savemessage = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_users', "[user_saved_ok]"), $user_object->Text), we_message_reporting::WE_MESSAGE_NOTICE);
							break;
					}

					if($user_object->Type == we_user::TYPE_USER){
						$tree_code .= 'top.content.cgroup=' . $user_object->ParentID . ';';
					}
					print we_html_element::jsElement('top.content.usetHot();' . $tree_code . $savemessage . $ret);
				}
			}
			break;
		case "delete_user":
			if(isset($_SESSION["user_session_data"]) && $_SESSION["user_session_data"]){
				$user_object = new we_user();
				$user_object->setState($_SESSION["user_session_data"]);

				if($user_object->ID == $_SESSION["user"]["ID"]){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[delete_user_same]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if(we_users_util::isUserInGroup($_SESSION["user"]["ID"], $user_object->ID)){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[delete_group_user_same]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if(!we_hasPerm("ADMINISTRATOR") && $user_object->checkPermission("ADMINISTRATOR")){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					$user_object = new we_user();
					break;
				}
				if(!we_hasPerm("DELETE_USER") && $user_object->Type == we_user::TYPE_USER){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(!we_hasPerm("DELETE_GROUP") && $user_object->Type == we_user::TYPE_USER_GROUP){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if(isset($GLOBALS["user"]) && $user_object->Text == $GLOBALS["user"]["Username"]){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[user_same]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($user_object->checkPermission("ADMINISTRATOR")){
					if($user_object->isLastAdmin()){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_users', "[modify_last_admin]"), we_message_reporting::WE_MESSAGE_ERROR));
						exit();
					}
				}

				switch($user_object->Type){
					case we_user::TYPE_USER_GROUP:
						$question = sprintf(g_l('modules_users', "[delete_alert_group]"), $user_object->Text);
						break;
					case we_user::TYPE_ALIAS:
						$question = sprintf(g_l('modules_users', "[delete_alert_alias]"), $user_object->Text);
						break;
					case we_user::TYPE_USER:
					default:
						$question = sprintf(g_l('modules_users', "[delete_alert_user]"), $user_object->Text);
						break;
				}
				print we_html_element::jsElement('
if(confirm("' . $question . '")){
	top.content.user_cmd.location="' . WE_USERS_MODULE_DIR . basename(__FILE__) . '?ucmd=do_delete";
}');
			}
			break;
		case "do_delete":
			if($_SESSION["user_session_data"]){
				$user_object = new we_user();
				$user_object->setState($_SESSION["user_session_data"]);
				if(!we_hasPerm("DELETE_USER") && $user_object->Type == we_user::TYPE_USER){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if(!we_hasPerm("DELETE_GROUP") && $user_object->Type == we_user::TYPE_USER_GROUP){
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}
				if($user_object->deleteMe()){
					print we_html_element::jsElement('
top.content.deleteEntry(' . $user_object->ID . ');
top.content.user_resize.user_right.user_editor.user_edheader.location="' . WEBEDITION_DIR . 'html/grayWithTopLine.html";
top.content.user_resize.user_right.user_editor.user_properties.location="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=mod_home&mod=users";
top.content.user_resize.user_right.user_editor.user_edfooter.location="' . WEBEDITION_DIR . 'html/gray.html";');
					unset($_SESSION["user_session_data"]);
				}
			}
			break;

		case "check_user_display":
			if($_REQUEST["uid"]){
				$mpid = f("SELECT ParentID FROM " . USER_TABLE . " WHERE ID=" . intval($_SESSION["user"]["ID"]), 'ParentID', $DB_WE);
				$pid = f("SELECT ParentID FROM " . USER_TABLE . " WHERE ID=" . intval($_REQUEST["uid"]), 'ParentID', $DB_WE);

				$search = true;
				$found = false;
				$first = true;

				while($search) {
					if($mpid == $pid){
						$search = false;
						if(!$first){
							$found = true;
						}
					}
					$first = false;
					if($pid == 0){
						$search = false;
					}
					$pid = intval(f("SELECT ParentID FROM " . USER_TABLE . " WHERE ID=" . intval($pid), 'ParentID', $DB_WE));
				}

				print we_html_element::jsElement(
						($found || we_hasPerm("ADMINISTRATOR") ?
							'top.content.we_cmd(\'display_user\',' . $_REQUEST["uid"] . ')' :
							we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR)
					));
			}
			break;
	}
}
?>
</head>
<body>
</body>
</html>
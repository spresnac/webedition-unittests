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
/* the parent class of storagable webEdition classes */
class weVotingView{

	var $db;
	var $frameset;
	var $topFrame;
	var $voting;
	var $editorBodyFrame;
	var $editorBodyForm;
	var $editorHeaderFrame;
	var $icon_pattern = "";
	var $item_pattern = "";
	var $group_pattern = "";

	function __construct($frameset = "", $topframe = "top.content"){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
		$this->voting = new weVoting();
		$this->item_pattern = addslashes('<img style="vertical-align: bottom" src="' . ICON_DIR . 'user.gif" />&nbsp;');
		$this->group_pattern = addslashes('<img style="vertical-align: bottom" src="' . ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '" />&nbsp;');
	}

	//----------- Utility functions ------------------

	function htmlHidden($name, $value = ""){
		return we_html_element::htmlHidden(array("name" => trim($name), "value" => oldHtmlspecialchars($value)));
	}

	//-----------------Init -------------------------------

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
		$this->editorBodyFrame = $frame . '.resize.right.editor.edbody';
		$this->editorBodyForm = $this->editorBodyFrame . '.document.we_form';
		$this->editorHeaderFrame = $frame . '.resize.right.editor.edheader';
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		$out = $this->htmlHidden("cmd", (isset($cmds["cmd"]) ? $cmds["cmd"] : ""));
		$out.=$this->htmlHidden("cmdid", (isset($cmds["cmdid"]) ? $cmds["cmdid"] : ""));
		$out.=$this->htmlHidden("pnt", (isset($cmds["pnt"]) ? $cmds["pnt"] : ""));
		$out.=$this->htmlHidden("tabnr", (isset($cmds["tabnr"]) ? $cmds["tabnr"] : ""));
		$out.=$this->htmlHidden("vernr", (isset($cmds["vernr"]) ? $cmds["vernr"] : 0));
		$out.=$this->htmlHidden("IsFolder", (isset($this->voting->IsFolder) ? $this->voting->IsFolder : '0'));
		return $out;
	}

	function getJSTop(){
		$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
		$title = '';
		foreach($GLOBALS["_we_available_modules"] as $modData){
			if($modData["name"] == $mod){
				$title = "webEdition " . g_l('global', "[modules]") . ' - ' . $modData["text"];
				break;
			}
		}

		$js = '
			var get_focus = 1;
			var activ_tab = 1;
			var hot = 0;
			var scrollToVal = 0;

			function setHot() {
				hot = 1;
			}
			function usetHot() {
				hot = 0;
			}
			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			parent.document.title = "' . $title . '";

			function we_cmd() {
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				if(hot == "1" && arguments[0] != "save_voting") {
					if(confirm("' . g_l('modules_voting', '[save_changed_voting]') . '")) {
						arguments[0] = "save_voting";
					} else {
						top.content.usetHot();
					}
				}
				switch (arguments[0]) {
					case "exit_voting":
						if(hot != "1") {
							eval(\'top.opener.top.we_cmd("exit_modules")\');
						}
				        break;

					case "vote":
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value = arguments[0];
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value = 3;
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.votnr.value = arguments[1];
							' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
							break;
					case "resetscores":
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value = arguments[0];
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value = 3;
							' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
							break;
		            case "new_voting":
		            case "new_voting_group":
						if(' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value = arguments[0];
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value = arguments[1];
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value = 1;
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.vernr.value = 0;
							' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
						} else {
							setTimeout(\'we_cmd("new_voting");\', 10);
						}
						break;
					case "delete_voting":
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;
						if(top.content.resize.right.editor.edbody.document.we_form.newone.value==1){
							' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}
						' . (!we_hasPerm("DELETE_VOTING") ?
				(
				we_message_reporting::getShowMessageCall(g_l('modules_voting', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
				) :
				('
								if (' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
									if (confirm("' . g_l('modules_voting', '[delete_alert]') . '")) {
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
										' . $this->editorHeaderFrame . '.location="' . $this->frameset . '?home=1&pnt=edheader";
										' . $this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";
										' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
									}
								} else {
									' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								}

						')) . '
						break;

					case "save_voting":
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;


								if (' . $this->topFrame . '.resize.right.editor.edbody.loaded) {

										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.owners_name.value=' . $this->topFrame . '.resize.right.editor.edbody.owners_label.name;
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.owners_count.value=' . $this->topFrame . '.resize.right.editor.edbody.owners_label.itemCount;
										' . '
										if(' . $this->editorBodyForm . '.IsFolder.value!=1){
											' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.question_name.value=' . $this->topFrame . '.resize.right.editor.edbody.question_edit.name;
											' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.answers_name.value=' . $this->topFrame . '.resize.right.editor.edbody.answers_edit.name;
											' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.variant_count.value=' . $this->topFrame . '.resize.right.editor.edbody.answers_edit.variantCount;
											' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.item_count.value=' . $this->topFrame . '.resize.right.editor.edbody.answers_edit.itemCount;
											' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.iptable_name.value=' . $this->topFrame . '.resize.right.editor.edbody.iptable_label.name;
											' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.iptable_count.value=' . $this->topFrame . '.resize.right.editor.edbody.iptable_label.itemCount;
										}

										' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
										top.content.usetHot();
								} else {
									' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								}

						break;

					case "edit_voting":
						' . (!we_hasPerm("EDIT_VOTING") ? we_message_reporting::getShowMessageCall(g_l('modules_voting', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : '') . '
						' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
						' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value=arguments[1];
						' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
						' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
					break;
					case "load":
						' . $this->topFrame . '.cmd.location="' . $this->frameset . '?pnt=cmd&pid="+arguments[1]+"&offset="+arguments[2]+"&sort="+arguments[3];
					break;
					case "home":
						' . $this->editorBodyFrame . '.parent.location="' . $this->frameset . '?pnt=editor";
					break;
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.opener.top.we_cmd(" + args + ")");
				}
			}
			';

		return we_html_element::jsScript(JS_DIR . "windows.js") . we_html_element::jsElement($js);
	}

	function getJSProperty(){
		return we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
			var loaded=0;

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd() {
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]) {
					case "switchPage":
						document.we_form.cmd.value=arguments[0];
						document.we_form.tabnr.value=arguments[1];
						submitForm();
					break;
					case "openVotingDirselector":
						url="' . WE_VOTING_MODULE_DIR . 'we_votingDirSelectorFrameset.php?";
						for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
						new jsWindow(url,"we_votingSelector",-1,-1,600,350,true,true,true);
					break;
					case "browse_server":
						new jsWindow(url,"browse_server",-1,-1,840,400,true,false,true);
						break;
					case "browse_users":
						new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
					break;
					case "add_owner":
						var owners = arguments[1];
						var isfolders = arguments[2];

						var own_arr = owners.split(",");
						var isfolders_arr = isfolders.split(",");
						for(var i=0;i<own_arr.length;i++){
							if(own_arr[i]!=""){
								owners_label.addItem();
								owners_label.setItem(0,(owners_label.itemCount-1),(isfolders_arr[i]==1 ? "' . $this->group_pattern . '" : "' . $this->item_pattern . '")+own_arr[i]);
								owners_label.showVariant(0);
							}
						}
					break;
					case "export_csv":
						oldcmd = document.we_form.cmd.value;
						oldpnt = document.we_form.pnt.value;
						document.we_form.question_name.value=question_edit.name;
						document.we_form.answers_name.value=answers_edit.name;
						document.we_form.variant_count.value=answers_edit.variantCount;
						document.we_form.item_count.value=answers_edit.itemCount;
						document.we_form.cmd.value=arguments[0];
						document.we_form.pnt.value=arguments[0];
						new jsWindow("","export_csv",-1,-1,420,250,true,false,true);
						submitForm("export_csv");
						document.we_form.cmd.value=oldcmd;
						document.we_form.pnt.value=oldpnt;
					break;
					case "exportGroup_csv":
						oldcmd = document.we_form.cmd.value;
						oldpnt = document.we_form.pnt.value;
						document.we_form.cmd.value=arguments[0];
						document.we_form.pnt.value=arguments[0];
						new jsWindow("","exportGroup_csv",-1,-1,420,250,true,false,true);
						submitForm("exportGroup_csv");
						document.we_form.cmd.value=oldcmd;
						document.we_form.pnt.value=oldpnt;
					break;

					case "reset_ipdata":
						if(confirm("' . g_l('modules_voting', '[delete_ipdata_question]') . '")){
							url = "' . WE_VOTING_MODULE_DIR . 'edit_voting_frameset.php?pnt="+arguments[0];
							new jsWindow(url,arguments[0],-1,-1,420,230,true,false,true);
							var t = document.getElementById("ip_mem_size");
							setVisible("delete_ip_data",false);
							t.innerHTML = "0";
						}
					break;
					case "delete_log":
						if(confirm("' . g_l('modules_voting', '[delete_log_question]') . '")){
							url = "' . WE_VOTING_MODULE_DIR . 'edit_voting_frameset.php?pnt="+arguments[0];
							new jsWindow(url,arguments[0],-1,-1,420,230,true,false,true);
						}
					break;
					case "show_log":
						url = "' . WE_VOTING_MODULE_DIR . 'edit_voting_frameset.php?pnt="+arguments[0];
						new jsWindow(url,arguments[0],-1,-1,810,600,true,true,true);
					break;
					break;
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.content.we_cmd("+args+")");
				}
			}


			' . $this->getJSSubmitFunction() . '

		');
	}

	function getJSTreeHeader(){
		return '
			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd(){
				var args = "";
				var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]) {
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
						}
						eval(\'top.content.we_cmd(\'+args+\')\');
				}
			}

	' . $this->getJSSubmitFunction("cmd");
	}

	function getJSSubmitFunction($def_target = "edbody", $def_method = "post"){
		return '
			function submitForm() {
				var f = self.document.we_form;

				if (arguments[0]) {
					f.target = arguments[0];
				} else {
					f.target = "' . $def_target . '";
				}

				if (arguments[1]) {
					f.action = arguments[1];
				} else {
					f.action = "' . $this->frameset . '";
				}

				if (arguments[2]) {
					f.method = arguments[2];
				} else {
					f.method = "' . $def_method . '";
				}

				f.submit();
			}

	';
	}

	function processCommands(){
		if(isset($_REQUEST["cmd"])){
			switch($_REQUEST["cmd"]){
				case "resetscores":
					foreach($this->voting->arr_Scores as $key => $val){
						$this->voting->arr_Scores[$key] = 0;
					}
					break;
				case "new_voting":
				case "new_voting_group":
					if(!we_hasPerm("NEW_VOTING")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}
					$this->voting = new weVoting();
					$this->voting->IsFolder = $_REQUEST["cmd"] == 'new_voting_group' ? 1 : 0;
					print we_html_element::jsElement('
								' . $this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->voting->Text) . '";
								' . $this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
					');
					break;
				case "edit_voting":
					if(!we_hasPerm("EDIT_VOTING")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						$_REQUEST['home'] = '1';
						$_REQUEST['pnt'] = 'edbody';
						break;
					}

					$this->voting = new weVoting($_REQUEST["cmdid"]);

					if(!$this->voting->isAllowedForUser()){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						$this->voting = new weVoting();
						$_REQUEST["home"] = true;
						break;
					}
					print we_html_element::jsElement(
							$this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->voting->Text) . '";' .
							$this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
					break;
				case "save_voting":
					if(!we_hasPerm("NEW_VOTING") && !we_hasPerm("EDIT_VOTING")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					$js = "";
					if($this->voting->filenameNotValid($this->voting->Text)){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					if(trim($this->voting->Text) == ''){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					if($this->voting->Active == 1 && $this->voting->ActiveTime && $this->voting->Valid < time()){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[not_active]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					$oldpath = $this->voting->Path;
					// set the path and check it
					$this->voting->setPath();

					if($this->voting->pathExists($this->voting->Path)){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}
					if($this->voting->isSelf()){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}


					$error = false;

					$q_empty = true;
					$a_empty = true;
					if(!$this->voting->IsFolder && count($this->voting->QASet) != 0){
						foreach($this->voting->QASet as $set){
							$q = trim($set['question']);
							if($q === ''){
								$q_empty = true;
								break;
							} else
								$q_empty = false;

							foreach($set['answers'] as $ans){
								$q = trim($ans);
								if($q === ''){
									$a_empty = true;
									break;
								} else
									$a_empty = false;
							}
						}

						if($q_empty){
							$error = true;
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_voting', '[question_empty]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							break;
						} else if($a_empty){
							$error = true;
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_voting', '[answer_empty]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							break;
						}
					}

					if($this->voting->ParentID > 0){
						$weAcQuery = new weSelectorQuery();
						$weAcResult = $weAcQuery->getItemById($this->voting->ParentID, VOTING_TABLE, array("IsFolder"));
						if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_voting', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							break;
						}
					}
					if(!$error){
						$newone = ($this->voting->ID == 0);

						$this->voting->save((isset($_REQUEST['scores_changed']) && $_REQUEST['scores_changed']) ? true : false);

						if($this->voting->IsFolder && $oldpath != '' && $oldpath != '/' && $oldpath != $this->voting->Path){
							$db_tmp = new DB_WE();
							$this->db->query('SELECT ID FROM ' . VOTING_TABLE . ' WHERE Path LIKE "' . $db_tmp->escape($oldpath) . '%" AND ID!=' . intval($this->voting->ID));
							while($this->db->next_record()) {
								$db_tmp->query('UPDATE ' . VOTING_TABLE . ' SET Path="' . $this->voting->evalPath($this->db->f('ID')) . '" WHERE ID=' . $this->db->f('ID'));
							}
						}

						$js = ($newone ?
								$this->topFrame . '.makeNewEntry(\'' . $this->voting->Icon . '\',\'' . $this->voting->ID . '\',\'' . $this->voting->ParentID . '\',\'' . $this->voting->Text . '\',0,\'' . ($this->voting->IsFolder ? 'folder' : 'item') . '\',\'' . VOTING_TABLE . '\',' . ($this->voting->isActive() ? 1 : 0) . ');' . $this->topFrame . '.drawTree();' :
								$this->topFrame . '.updateEntry(' . $this->voting->ID . ',"' . $this->voting->Text . '","' . $this->voting->ParentID . '",' . ($this->voting->isActive() ? 1 : 0) . ');'
							);
						print we_html_element::jsElement($js .
								$this->editorHeaderFrame . '.location.reload();' .
								we_message_reporting::getShowMessageCall(($this->voting->IsFolder == 1 ? g_l('modules_voting', '[save_group_ok]') : g_l('modules_voting', '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE));
					}
					break;
				case "delete_voting":

					if(!we_hasPerm("DELETE_VOTING")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_voting', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						return;
					} else{
						if($this->voting->delete()){
							print we_html_element::jsElement(
									$this->topFrame . '.deleteEntry(' . $this->voting->ID . ');
									setTimeout(\'' . we_message_reporting::getShowMessageCall(($this->voting->IsFolder == 1 ? g_l('modules_voting', '[group_deleted]') : g_l('modules_voting', '[voting_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE) . '\',500);');
							$this->voting = new weVoting();
							$_REQUEST['home'] = '1';
							$_REQUEST['pnt'] = 'edbody';
						} else{
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_voting', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
						}
					}
					break;
				case "switchPage":
					break;
				case "export_csv":
					$fname = ($_REQUEST["csv_dir"] == '/' ? '' : $_REQUEST['csv_dir']) . '/voting_' . $this->voting->ID . '_export_' . time() . '.csv';

					$enclose = isset($_REQUEST['csv_enclose']) ? ($_REQUEST['csv_enclose'] == 0 ? '"' : '\'') : '"';
					$delimiter = isset($_REQUEST['csv_delimiter']) ? ($_REQUEST['csv_delimiter'] == '\t' ? "\t" : $_REQUEST['csv_delimiter']) : ';';
					if(isset($_REQUEST['csv_lineend'])){
						switch($_REQUEST['csv_lineend']){
							default:
							case 'windows':
								$lineend = "\r\n";
								break;
							case 'unix':
								$lineend = "\n";
								break;
							case 'mac':
								$lineend = "\r";
								break;
						}
					}

					$content = array();
					if(isset($_REQUEST['question_name']) && isset($_REQUEST[$_REQUEST['question_name'] . '_item0']))
						$content[] = $enclose . addslashes($_REQUEST[$_REQUEST['question_name'] . '_item0']) . $enclose . $delimiter;
					if(isset($_REQUEST['answers_name']) && isset($_REQUEST['item_count'])){
						for($i = 0; $i < $_REQUEST['item_count']; $i++){
							if(isset($_REQUEST[$_REQUEST['answers_name'] . '_item' . $i]))
								$content[] = $enclose . addslashes($_REQUEST[$_REQUEST['answers_name'] . '_item' . $i]) . $enclose . $delimiter . $this->voting->Scores[$i];
						}
					}
					weFile::save($_SERVER['DOCUMENT_ROOT'] . $fname, implode($lineend, $content));
					$_REQUEST["lnk"] = $fname;
					break;
				case "exportGroup_csv":
					$fname = ($_REQUEST['csv_dir'] == '/' ? '' : $_REQUEST['csv_dir']) . '/votingGroup_' . $this->voting->ID . '_export_' . time() . '.csv';

					$enclose = isset($_REQUEST['csv_enclose']) ? ($_REQUEST['csv_enclose'] == 0 ? '"' : '\'') : '"';
					$delimiter = isset($_REQUEST['csv_delimiter']) ? ($_REQUEST['csv_delimiter'] == '\t' ? "\t" : $_REQUEST['csv_delimiter']) : ';';
					if(isset($_REQUEST['csv_lineend'])){
						switch($_REQUEST['csv_lineend']){
							default:
							case 'windows':
								$lineend = "\r\n";
								break;
							case 'unix':
								$lineend = "\n";
								break;
							case 'mac':
								$lineend = "\r";
								break;
						}
					}

					$allData = $this->voting->loadDB();
					$CSV_Charset = (isset($_REQUEST['the_charset']) && $_REQUEST['the_charset'] != '' ? $_REQUEST['the_charset'] : 'UTF-8');
					$content = array(
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[voting-session]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[voting-id]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[time]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[ip]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[user_agent]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[cookie]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[log_fallback]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[status]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[answerID]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[answerText]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[voting-successor]'))) . $enclose . $delimiter .
						$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(g_l('modules_voting', '[voting-additionalfields]'))) . $enclose . $delimiter
					);

					foreach($allData as $key => $data){
						$cookie = $data['cookie'] ? g_l('modules_voting', '[enabled]') : g_l('modules_voting', '[disabled]');
						$fallback = $data['fallback'] ? g_l('global', '[yes]') : g_l('global', '[no]');

						if($data['status'] != weVoting::SUCCESS){
							switch($data['status']){
								case weVoting::ERROR :
									$mess = g_l('modules_voting', '[log_error]');
									break;
								case weVoting::ERROR_ACTIVE :
									$mess = g_l('modules_voting', '[log_error_active]');
									break;
								case weVoting::ERROR_REVOTE :
									$mess = g_l('modules_voting', '[log_error_revote]');
									break;
								case weVoting::ERROR_BLACKIP :
									$mess = g_l('modules_voting', '[log_error_blackip]');
									break;
								default:
									$mess = g_l('modules_voting', '[log_error]');
							}
						} else{
							$mess = g_l('modules_voting', '[log_success]');
						}

						$myline = $enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($data['votingsession'])) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($data['voting'])) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim(date(g_l('weEditorInfo', "[date_format]"), $data['time']))) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($data['ip'])) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($data['agent'])) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($cookie)) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($fallback)) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($mess)) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($data['answer'])) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($data['answertext'])) . $enclose . $delimiter .
							$enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($data['successor'])) . $enclose . $delimiter;

						if($data['additionalfields'] != ''){
							$addData = unserialize($data['additionalfields']);

							if(is_array($addData) && !empty($addData)){
								foreach($addData as $key => $values){
									$myline .= $enclose . iconv(DEFAULT_CHARSET, $CSV_Charset . '//TRANSLIT', trim($values)) . $enclose . $delimiter;
								}
							} else{
								$myline.= $enclose . '-' . $enclose . $delimiter;
							}
						} else{
							$myline.= $enclose . '-' . $enclose . $delimiter;
						}
						$content[] = $myline;
					}

					weFile::save($_SERVER['DOCUMENT_ROOT'] . $fname, implode($lineend, $content));
					$_REQUEST['lnk'] = $fname;
					break;

				default:
			}
		}

		$_SESSION['weS']['voting_session'] = serialize($this->voting);
	}

	function processVariables(){

		if(isset($_SESSION['weS']['voting_session'])){
			$this->voting = unserialize($_SESSION['weS']['voting_session']);
		}

		if(is_array($this->voting->persistent_slots)){
			foreach($this->voting->persistent_slots as $val){
				$varname = $val;
				if(isset($_REQUEST[$varname])){
					$this->voting->{$val} = $_REQUEST[$varname];
				}
			}
		}

		if(isset($_REQUEST["page"]))
			if(isset($_REQUEST["page"])){
				$this->page = $_REQUEST["page"];
			}

		$qaset = array();
		$qaADDset = array();
		if(isset($_REQUEST['question_name']) && isset($_REQUEST['variant_count']) && isset($_REQUEST['answers_name']) && isset($_REQUEST['item_count'])){
			for($i = 0; $i < $_REQUEST['variant_count']; $i++){
				if(isset($_REQUEST[$_REQUEST['question_name'] . '_variant' . $i . '_' . $_REQUEST['question_name'] . '_item0'])){
					$set = array(
						'question' => addslashes($_REQUEST[$_REQUEST['question_name'] . '_variant' . $i . '_' . $_REQUEST['question_name'] . '_item0']),
						'answers' => array(),
					);

					$an = $_REQUEST['answers_name'] . '_variant' . $i . '_' . $_REQUEST['answers_name'] . '_item';
					$anImage = $an . 'ImageID';
					$anMedia = $an . 'MediaID';
					$anSuccessor = $an . 'SuccessorID';
					$addset = array();
					for($j = 0; $j < $_REQUEST['item_count']; $j++){
						if(isset($_REQUEST[$an . $j])){
							$set['answers'][] = addslashes($_REQUEST[$an . $j]);
						}
						if(isset($_REQUEST[$anImage . $j])){
							$addset['imageID'][] = ($_REQUEST[$anImage . $j] != 'Array' ? addslashes($_REQUEST[$anImage . $j]) : 0);
						}
						if(isset($_REQUEST[$anMedia . $j])){
							$addset['mediaID'][] = ($_REQUEST[$anMedia . $j] != 'Array' ? addslashes($_REQUEST[$anMedia . $j]) : 0);
						}
						if(isset($_REQUEST[$anSuccessor . $j])){
							$addset['successorID'][] = ($_REQUEST[$anSuccessor . $j] != 'Array' ? addslashes($_REQUEST[$anSuccessor . $j]) : 0);
						}
					}
					$qaset[] = $set;
					$qaADDset[] = $addset;
				}
			}
		}

		$this->voting->QASet = $qaset;
		$this->voting->QASetAdditions = $qaADDset;

		if(isset($_REQUEST['owners_name']) && isset($_REQUEST['owners_count'])){
			$this->voting->Owners = array();
			$an = $_REQUEST['owners_name'] . '_variant0_' . $_REQUEST['owners_name'] . '_item';
			for($i = 0; $i < $_REQUEST['owners_count']; $i++){
				$up = str_replace(array(stripslashes($this->item_pattern), stripslashes($this->group_pattern)), '', $_REQUEST[$an . $i]);
				if(isset($_REQUEST[$an . $i]))
					$this->voting->Owners[] = path_to_id($up, USER_TABLE);
			}
			$this->voting->Owners = array_unique($this->voting->Owners);
		}

		$ipset = array();
		if(isset($_REQUEST['iptable_name']) && isset($_REQUEST['iptable_count'])){
			$in = $_REQUEST['iptable_name'] . '_variant0_' . $_REQUEST['iptable_name'] . '_item';
			for($i = 0; $i < $_REQUEST['iptable_count']; $i++){
				if(isset($_REQUEST[$in . $i]))
					$ipset[] = addslashes($_REQUEST[$in . $i]);
			}
			$this->voting->BlackList = $ipset;
		}


		if(isset($_REQUEST['PublishDate_day'])){
			$this->voting->PublishDate = mktime($_REQUEST['PublishDate_hour'], $_REQUEST['PublishDate_minute'], 0, $_REQUEST['PublishDate_month'], $_REQUEST['PublishDate_day'], $_REQUEST['PublishDate_year']);
		}

		if(isset($_REQUEST['Valid_day'])){
			$this->voting->Valid = mktime($_REQUEST['Valid_hour'], $_REQUEST['Valid_minute'], 0, $_REQUEST['Valid_month'], $_REQUEST['Valid_day'], $_REQUEST['Valid_year']);
		}

		if(isset($_REQUEST['scores_0']) && isset($_REQUEST['item_count']) && isset($_REQUEST['scores_changed']) && $_REQUEST['scores_changed']){
			$this->voting->Scores = array();
			for($j = 0; $j < $_REQUEST['item_count']; $j++){
				if(isset($_REQUEST['scores_' . $j]))
					$this->voting->Scores[] = $_REQUEST['scores_' . $j];
			}
		}
	}

	function new_array_splice(&$a, $start, $len = 1){
		$ks = array_keys($a);
		$k = array_search($start, $ks);
		if($k !== false){
			$ks = array_splice($ks, $k, $len);
			foreach($ks as $k)
				unset($a[$k]);
		}
	}

}
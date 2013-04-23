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
class weGlossaryView{

	/**
	 * Database Instance
	 *
	 * @var object
	 */
	var $Db;

	/**
	 * Glossary Instance
	 *
	 * @var object
	 */
	var $Glossary;

	/**
	 * Name of the frameset
	 *
	 * @var string
	 */
	var $FrameSet;

	/**
	 * Name of the Top-Frame
	 *
	 * @var string
	 */
	var $TopFrame;

	/**
	 * Name of the Editor-Body-Frame
	 *
	 * @var string
	 */
	var $EditorBodyFrame;

	/**
	 * Name of the Editor-Body-Form
	 *
	 * @var string
	 */
	var $EditorBodyForm;

	/**
	 * Name of the Editor-Header-Frame
	 *
	 * @var string
	 */
	var $EditorHeaderFrame;

	/**
	 * RegExp-Pattern of the item Icon
	 *
	 * @var string
	 */
	var $ItemPattern = "";

	/**
	 * RegExp-Pattern of the folder Icon
	 *
	 * @var string
	 */
	var $GroupPattern = "";

	/**
	 * PHP 5 Constructor
	 *
	 * @param string $FrameSet
	 * @param string $TopFrame
	 */
	function __construct($FrameSet = "", $TopFrame = "top.content"){
		$this->Db = new DB_WE();
		$this->Glossary = new weGlossary();

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				case 'new_glossary_abbreviation':
					$this->Glossary->Type = 'abbreviation';
					break;

				case 'new_glossary_acronym':
					$this->Glossary->Type = 'acronym';
					break;

				case 'new_glossary_foreignword':
					$this->Glossary->Type = 'foreignword';
					break;

				case 'new_glossary_link':
					$this->Glossary->Type = 'link';
					break;

				case 'new_glossary_textreplacement':
					$this->Glossary->Type = 'textreplacement';
					break;
			}
		} elseif(isset($_REQUEST['type'])){
			$this->Glossary->Type = $_REQUEST['type'];
		}

		$this->setFramesetName($FrameSet);
		$this->setTopFrame($TopFrame);

		$this->ItemPattern = '<img style=\"vertical-align: bottom\" src=\"' . ICON_DIR . 'prog.gif\" />&nbsp;';
		$this->GroupPattern = '<img style=\"vertical-align: bottom\" src=\"' . ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '\" />&nbsp;';
	}

	//----------- Utility functions ------------------

	/**
	 * return the html code for a hidden field
	 *
	 * @param string $name
	 * @param string $value
	 * @return string
	 */
	function htmlHidden($name, $value = ""){

		$hidden = array(
			'name' => trim($name),
			'value' => oldHtmlspecialchars($value),
		);
		return we_html_element::htmlHidden($hidden);
	}

	//-----------------Init -------------------------------

	/**
	 * set the name of the frameset
	 *
	 * @param string $frameset
	 */
	function setFramesetName($FrameSet){

		$this->FrameSet = $FrameSet;
	}

	/**
	 * set the name of the topframe, editorBodyFrame, editorBodyForm
	 * and the editorHeaderFrame
	 *
	 * @param string $Frame
	 */
	function setTopFrame($Frame){

		$this->TopFrame = $Frame;
		$this->EditorBodyFrame = $Frame . '.resize.right.editor.edbody';
		$this->EditorBodyForm = $this->EditorBodyFrame . '.document.we_form';
		$this->EditorHeaderFrame = $Frame . '.resize.right.editor.edheader';
	}

	//------------------------------------------------

	function getCommonHiddens($cmds = array()){

		$out = $this->htmlHidden("cmd", (isset($cmds["cmd"]) ? $cmds["cmd"] : ""))
			. $this->htmlHidden("cmdid", (isset($cmds["cmdid"]) ? $cmds["cmdid"] : ""))
			. $this->htmlHidden("pnt", (isset($cmds["pnt"]) ? $cmds["pnt"] : ""))
			. $this->htmlHidden("tabnr", (isset($cmds["tabnr"]) ? $cmds["tabnr"] : ""))
			. $this->htmlHidden("IsFolder", (isset($this->Glossary->IsFolder) ? $this->Glossary->IsFolder : '0'));

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
				hot = "1";
			}

			function usetHot() {
				hot = "0";
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

				if(hot == "1" && arguments[0] != "save_glossary") {
					if(confirm("' . g_l('modules_glossary', "[save_changed_glossary]") . '")) {
						arguments[0] = "save_glossary";
					} else {
						top.content.usetHot();
					}
				}
				switch (arguments[0]) {
					case "exit_glossary":
						if(hot != "1") {
							eval(\'top.opener.top.we_cmd("exit_modules")\');
						}
						break;
					case "new_glossary_acronym":
					case "new_glossary_abbreviation":
					case "new_glossary_foreignword":
					case "new_glossary_link":
					case "new_glossary_textreplacement":
						if(' . $this->TopFrame . '.resize.right.editor.edbody.loaded) {
							' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmd.value = arguments[0];
							if(arguments[1] != undefined) {
								' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value = arguments[1];
							}
							' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value = 1;
							' . $this->TopFrame . '.resize.right.editor.edbody.submitForm();
						} else {
							if(arguments[1] != undefined) {
								str = \'we_cmd("\' + arguments[0] + \'", "\' + arguments[1] + \'");\';
								setTimeout(str, 10);
							} else {
								str = \'we_cmd(\' + arguments[0] + \');\';
								setTimeout(str, 10);
							}
						}
						break;

					case "delete_glossary":
						var exc = ' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value;
						if (exc.substring(exc.length-10, exc.length)=="_exception") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							break;
						}
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="view_folder") return;
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="view_type") return;
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="view_exception") return;
						if(top.content.resize.right.editor.edbody.document.we_form.newone.value==1){
							' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}
						' . (!we_hasPerm("DELETE_GLOSSARY") ?
				(
				we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR)
				) :
				('
							if (' . $this->TopFrame . '.resize.right.editor.edbody.loaded) {
								if (confirm("' . g_l('modules_glossary', "[delete_alert]") . '")) {
									' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
									' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->TopFrame . '.activ_tab;
									' . $this->EditorHeaderFrame . '.location="' . $this->FrameSet . '?home=1&pnt=edheader";
									' . $this->TopFrame . '.resize.right.editor.edfooter.location="' . $this->FrameSet . '?home=1&pnt=edfooter";
									' . $this->TopFrame . '.resize.right.editor.edbody.submitForm();
								}
							} else {
								' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[nothing_to_delete]"), we_message_reporting::WE_MESSAGE_ERROR) . '
							}
						')) . '
						break;

					case "save_exception":
					case "save_glossary":
						var exc = ' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value;
						if (exc.substring(exc.length-10, exc.length)=="_exception") {
							arguments[0] = "save_exception";
						}
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="view_folder") return;
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="view_type") return;
						if (' . $this->TopFrame . '.resize.right.editor.edbody.loaded) {
							' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
							' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->TopFrame . '.activ_tab;
							if(top.makeNewEntry==1) {
								' . $this->TopFrame . '.resize.right.editor.edbody.submitForm("cmd");
							} else {
								' . $this->TopFrame . '.resize.right.editor.edbody.submitForm();
							}
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[nothing_to_save]"), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
						top.content.usetHot();
						break;

					case "edit_glossary_acronym":
					case "edit_glossary_abbreviation":
					case "edit_glossary_foreignword":
					case "edit_glossary_link":
					case "edit_glossary_textreplacement":
						' . (!we_hasPerm("EDIT_GLOSSARY") ? we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR) . 'return;' : '') . '
						' . $this->TopFrame . '.hot=0;
						' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
						' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value=arguments[1];
						' . $this->TopFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->TopFrame . '.activ_tab;
						' . $this->TopFrame . '.resize.right.editor.edbody.submitForm();
						break;

					case "load":
						' . $this->TopFrame . '.cmd.location="' . $this->FrameSet . '?pnt=cmd&pid="+arguments[1]+"&offset="+arguments[2]+"&sort="+arguments[3];
						break;

					case "home":
						' . $this->EditorBodyFrame . '.parent.location="' . $this->FrameSet . '?pnt=editor";
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

		$out = we_html_element::jsScript(JS_DIR . "windows.js");

		$js = '
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
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.content.we_cmd("+args+")");
				}
			}
			' . $this->getJSSubmitFunction() . '

		';

		$out .= we_html_element::jsElement($js);

		return $out;
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
				var url = "' . $this->FrameSet . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
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
					f.action = "' . $this->FrameSet . '";
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

				case "new_glossary_acronym":
				case "new_glossary_abbreviation":
				case "new_glossary_foreignword":
				case "new_glossary_link":
				case "new_glossary_textreplacement":
					if(!we_hasPerm("NEW_GLOSSARY")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}
					$this->Glossary = new weGlossary();
					$this->Glossary->Type = array_pop(explode("_", $_REQUEST["cmd"], 4));

					print we_html_element::jsElement('
							' . $this->TopFrame . '.resize.right.editor.edheader.location="' . $this->FrameSet . '?pnt=edheader&text=' . urlencode($this->Glossary->Text) . '";
							' . $this->TopFrame . '.resize.right.editor.edfooter.location="' . $this->FrameSet . '?pnt=edfooter";
					');
					break;

				case "edit_glossary_acronym":
				case "edit_glossary_abbreviation":
				case "edit_glossary_foreignword":
				case "edit_glossary_link":
				case "edit_glossary_textreplacement":
					if(!we_hasPerm("EDIT_GLOSSARY")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						$_REQUEST['home'] = '1';
						$_REQUEST['pnt'] = 'edbody';
						break;
					}
					$this->Glossary = new weGlossary($_REQUEST["cmdid"]);

					print we_html_element::jsElement('
						' . $this->TopFrame . '.resize.right.editor.edheader.location="' . $this->FrameSet . '?pnt=edheader&text=' . urlencode($this->Glossary->Text) . '";
						' . $this->TopFrame . '.resize.right.editor.edfooter.location="' . $this->FrameSet . '?pnt=edfooter";
					');
					break;

				case 'populateWorkspaces':
					$objectLinkID = (isset($_REQUEST['link']['Attributes']['ObjectLinkID']) && $_REQUEST['link']['Attributes']['ObjectLinkID'] != "" ? $_REQUEST['link']['Attributes']['ObjectLinkID'] : 0);
					$_values = weDynList::getWorkspacesForObject($objectLinkID);
					$_js = '';

					if(!empty($_values)){

						foreach($_values as $_id => $_path){
							$_js .= $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options[' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length] = new Option("' . $_path . '",' . $_id . ');
							';
						}
						print we_html_element::jsElement(
								$this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length = 0;
							' . $_js . '
							' . $this->EditorBodyFrame . '.setDisplay("ObjectWorkspaceID","block");
						');
					} else{
						if(weDynList::getWorkspaceFlag($objectLinkID)){
							print we_html_element::jsElement(
									$this->EditorBodyFrame . '.setDisplay("ObjectWorkspaceID","block");
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length = 0;
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options[' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length] = new Option("/",0);
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].selectedIndex = 0;'
								);
						} else{
							print we_html_element::jsElement(
									$this->EditorBodyFrame . '.setDisplay("ObjectWorkspaceID","none");
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length = 0;
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options[' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectWorkspaceID]\'].options.length] = new Option("-1",-1);
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectLinkID]\'].value = "";
								' . $this->EditorBodyForm . '.elements[\'link[Attributes][ObjectLinkPath]\'].value = "";
								' . we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[no_workspace]"), we_message_reporting::WE_MESSAGE_ERROR) . '
							');
						}
					}
					break;

				case 'save_exception':
					if(!isset($_REQUEST['cmdid']) || !isset($_REQUEST['Exception'])){
						break;
					}

					$language = substr($_REQUEST['cmdid'], 0, 5);

					weGlossary::editException($language, $_REQUEST['Exception']);

					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[save_ok]"), we_message_reporting::WE_MESSAGE_NOTICE)
						);

					break;

				case "save_glossary":
					if(isset($_REQUEST['Exception'])){
						$language = substr($_REQUEST['cmdid'], 0, 5);

						weGlossary::editException($language, $_REQUEST['Exception']);
						break;
					}
					$this->Glossary->Text = $_REQUEST[$_REQUEST['Type']]['Text'];
					if($this->Glossary->Type != "foreignword"){
						$this->Glossary->Title = $_REQUEST[$_REQUEST['Type']]['Title'];
					}
					if(isset($_REQUEST[$_REQUEST['Type']]['Attributes'])){
						$this->Glossary->Attributes = $_REQUEST[$_REQUEST['Type']]['Attributes'];
					} else{
						$this->Glossary->Attributes = '';
					}

					if(!we_hasPerm("NEW_GLOSSARY") && !we_hasPerm("EDIT_GLOSSARY")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					if(trim($this->Glossary->Text) == ''){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[name_empty]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					if($this->Glossary->checkFieldText($this->Glossary->Text)){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[text_notValid]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					if($this->Glossary->checkFieldText($this->Glossary->Title)){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[title_notValid]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					$oldpath = $this->Glossary->Path;

					// set the path and check it
					$this->Glossary->setPath();

					if($this->Glossary->pathExists($this->Glossary->Path)){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[name_exists]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					if($this->Glossary->isSelf()){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[path_nok]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}


					if($this->Glossary->ID != 0){
						$StateBefore = f("SELECT Published FROM " . GLOSSARY_TABLE . " WHERE ID = " . intval($this->Glossary->ID), "Published", new DB_WE());
					} else{
						$StateBefore = 0;
					}


					$isNew = $this->Glossary->ID == 0;

					if($this->Glossary->save()){

						$this->Glossary->Text = htmlentities($this->Glossary->Text, ENT_QUOTES);
						$this->Glossary->Title = htmlentities($this->Glossary->Title, ENT_QUOTES);

						if($isNew){
							$js = $this->TopFrame . '.makeNewEntry(\'' . $this->Glossary->Icon . '\',\'' . $this->Glossary->ID . '\',\'' . $this->Glossary->Language . '_' . $this->Glossary->Type . '\',\'' . $this->Glossary->Text . '\',0,\'' . ($this->Glossary->IsFolder ? 'folder' : 'item') . '\',\'' . GLOSSARY_TABLE . '\',' . ($this->Glossary->Published > 0 ? 1 : 0) . ');
								' . $this->TopFrame . '.drawTree();';
						} else{
							$js = $this->TopFrame . '.updateEntry(' . $this->Glossary->ID . ',"' . $this->Glossary->Text . '","' . $this->Glossary->Language . '_' . $this->Glossary->Type . '",' . ($this->Glossary->Published > 0 ? 1 : 0) . ');' . "\n";
						}

						$this->Glossary->Text = html_entity_decode($this->Glossary->Text, ENT_QUOTES);
						$this->Glossary->Title = html_entity_decode($this->Glossary->Title, ENT_QUOTES);

						$message = "";
						// Replacment of item is activated
						if($StateBefore == 0 && $_REQUEST['Published'] == "1"){
							$message .= sprintf(g_l('modules_glossary', "[replace_activated]"), $this->Glossary->Text);
							$message .= "\\n";

							// Replacement of item is deactivated
						} else if($StateBefore > 0 && $_REQUEST['Published'] == "0"){
							$message .= sprintf(g_l('modules_glossary', "[replace_deactivated]"), $this->Glossary->Text);
							$message .= "\\n";
						}
						$message .= sprintf(g_l('modules_glossary', "[item_saved]"), $this->Glossary->Text);

						print we_html_element::jsElement(
								$js .
								we_message_reporting::getShowMessageCall($message, we_message_reporting::WE_MESSAGE_NOTICE) . '
							if(top.makeNewEntry==1) {
								' . $this->TopFrame . '.we_cmd("new_glossary_' . $this->Glossary->Type . '", "' . $this->Glossary->Language . '");
							} else {
								' . $this->EditorHeaderFrame . '.location.reload();
							}
							' . $this->TopFrame . '.hot=0;
						');

						//
						// --> Save to Cache
						//

						$Cache = new weGlossaryCache($this->Glossary->Language);
						$Cache->write();
						unset($Cache);

						//
						// --> Save to Cache End
					//

	                }
					break;

				case "delete_glossary":

					if(!we_hasPerm("DELETE_GLOSSARY")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_glossary', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR)
							);
						return;
					} else{
						if($this->Glossary->delete()){
							print we_html_element::jsElement('
								' . $this->TopFrame . '.deleteEntry(' . $this->Glossary->ID . ');
								setTimeout(\'' . we_message_reporting::getShowMessageCall(($this->Glossary->IsFolder == 1 ? g_l('modules_glossary', '[group_deleted]') : g_l('modules_glossary', '[item_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE) . '\',500);
							');

							//
							// --> Save to Cache
							//

							$Cache = new weGlossaryCache($this->Glossary->Language);
							$Cache->write();
							unset($Cache);

							//
							// --> Save to Cache End
							//

							$this->Glossary = new weGlossary();
							$_REQUEST['home'] = '1';
							$_REQUEST['pnt'] = 'edbody';
						} else{
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR));
						}
					}
					break;

				case "switchPage":
					break;

				default:
					break;
			}
		}

		$_SESSION['weS']['weGlossarySession'] = serialize($this->Glossary);
	}

	function processVariables(){

		if(isset($_SESSION['weS']['weGlossarySession'])){
			$this->Glossary = unserialize($_SESSION['weS']['weGlossarySession']);
		}

		if(is_array($this->Glossary->persistent_slots)){
			foreach($this->Glossary->persistent_slots as $val){
				if(isset($_REQUEST[$val])){
					if($val == "Published"){
						if($this->Glossary->Published == 0 && $_REQUEST['Published'] == "1"){
							$this->Glossary->Published = time();
						} elseif($_REQUEST['Published'] == "0"){
							$this->Glossary->Published = 0;
						}
					} else if(is_array($_REQUEST[$val])){
						$this->Glossary->$val = $_REQUEST[$val];
					} else{
						$this->Glossary->$val = $_REQUEST[$val];
					}
				}
			}
		}

		if(isset($_REQUEST["page"])){
			$this->page = $_REQUEST["page"];
		}
	}

}

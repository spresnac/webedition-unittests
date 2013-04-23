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

class weNewsletterView{

	var $db;
	// settings array; format settings[setting_name]=settings_value
	var $settings = array();
	//default newsletter
	var $newsletter;
	//wat page is currentlly displed 0-properties(default);1-overview;
	var $page = 0;
	var $get_import = 0;
	var $hiddens = array();
	var $customers_fields;
	var $frameset;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;

	function __construct(){
		$this->db = new DB_WE();
		$this->newsletter = new weNewsletter();

		$this->hiddens[] = "ID";

		$this->page = 0;

		$this->show_import_box = -1;
		$this->show_export_box = -1;

		$this->frameset = WE_NEWSLETTER_MODULE_DIR . "edit_newsletter_frameset.php";

		$this->settings = self::getSettings();

		if(defined("CUSTOMER_TABLE")){
			$this->customers_fields = array();
			$this->db->query("SHOW FIELDS FROM " . CUSTOMER_TABLE);
			while($this->db->next_record()) {
				$this->customers_fields[] = $this->db->f("Field");
			}
		}
		$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter]');
		$this->newsletter->Sender = $this->settings["default_sender"];
		$this->newsletter->Reply = $this->settings["default_reply"];
		$this->newsletter->Test = $this->settings["test_account"];
		$this->newsletter->isEmbedImages = $this->settings["isEmbedImages"];
	}

	function setFrames($topFrame, $treeFrame, $cmdFrame){
		$this->topFrame = $topFrame;
		$this->treeFrame = $treeFrame;
		$this->cmdFrame = $cmdFrame;
	}

	function getHiddens($predefs = array()){
		$out = $this->htmlHidden("ncmd", (isset($predefs["ncmd"]) ? $predefs["ncmd"] : "new_newsletter"));
		$out .= $this->htmlHidden("we_cmd[0]", "show_newsletter");
		$out .= $this->htmlHidden("nid", (isset($predefs["nid"]) ? $predefs["nid"] : $this->newsletter->ID));
		$out .= $this->htmlHidden("pnt", (isset($predefs["pnt"]) ? $predefs["pnt"] : $_REQUEST["pnt"]));
		$out .= $this->htmlHidden("page", (isset($predefs["page"]) ? $predefs["page"] : $this->page));
		$out .= $this->htmlHidden("gview", (isset($predefs["gview"]) ? $predefs["gview"] : 0));
		$out .= $this->htmlHidden("hm", (isset($predefs["hm"]) ? $predefs["hm"] : 0));
		$out .= $this->htmlHidden("ask", (isset($predefs["ask"]) ? $predefs["ask"] : 1));
		$out .= $this->htmlHidden("test", (isset($predefs["test"]) ? $predefs["test"] : 0));
		return $out;
	}

	function newsletterHiddens(){
		$out = "";
		foreach($this->hiddens as $val){
			$attval = "";

			if(in_array($val, $this->newsletter->persistents)){
				$attval = $this->newsletter->$val;
			} else{
				$attval = $this->$val;
			}
			$out .= $this->htmlHidden($val, $attval);
		}

		return $out;
	}

	function getHiddensProperty(){
		$out = "";
		$counter = 0;
		$val = "";

		foreach($this->newsletter->groups as $group){

			foreach($group->persistents as $per){
				$val = $group->$per;
				$out .= $this->htmlHidden("group" . $counter . "_" . $per, $val);
			}

			$counter++;
		}

		$out .= $this->htmlHidden("groups", $counter);
		$out .= $this->htmlHidden("Step", $this->newsletter->Step);
		$out .= $this->htmlHidden("Offset", $this->newsletter->Offset);
		$out .= $this->htmlHidden("IsFolder", $this->newsletter->IsFolder);
		return $out;
	}

	function getHiddensPropertyPage(){
		$out = "";

		$out.=$this->htmlHidden("Text", $this->newsletter->Text);
		$out.=$this->htmlHidden("Subject", $this->newsletter->Subject);
		$out.=$this->htmlHidden("ParentID", $this->newsletter->ParentID);
		$out.=$this->htmlHidden("Sender", $this->newsletter->Sender);
		$out.=$this->htmlHidden("Reply", $this->newsletter->Reply);
		$out.=$this->htmlHidden("Test", $this->newsletter->Test);
		$out.=$this->htmlHidden("Charset", $this->newsletter->Charset);
		$out.=$this->htmlHidden("isEmbedImages", $this->newsletter->isEmbedImages);

		return $out;
	}

	function getHiddensMailingPage(){
		$out = "";

		$fields_names = array("fieldname", "operator", "fieldvalue", "logic", "hours", "minutes");
		foreach($this->newsletter->groups as $g => $group){
			if(is_array($group->aFilter)){
				$out.=$this->htmlHidden("filter_" . $g, count($group->aFilter));

				foreach($group->aFilter as $k => $v){
					foreach($fields_names as $field){
						if(isset($v["$field"]))
							$out.=$this->htmlHidden("filter_" . $field . "_" . $g . "_" . $k, $v["$field"]);
					}
				}
			}
		}

		return $out;
	}

	function getHiddensContentPage(){
		$out = "";
		$counter = 0;

		foreach($this->newsletter->blocks as $bk => $bv){

			foreach($this->newsletter->blocks[$bk]->persistents as $per){
				$out .= $this->htmlHidden("block" . $counter . "_" . $per, $bv->$per);
			}

			$counter++;
		}

		$out .= $this->htmlHidden("blocks", $counter);

		return $out;
	}

	function htmlHidden($name, $value = ""){
		return '<input type="hidden" name="' . trim($name) . '" value="' . oldHtmlspecialchars($value) . '" />';
	}

	/* creates the DocumentChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDocChooser($width = "", $rootDirID = 0, $Pathname = "ParentPath", $Pathvalue = "/", $IDName = "ParentID", $IDValue = "0", $cmd = ""){
		$Pathvalue = f("SELECT Path FROM " . FILE_TABLE . " WHERE ID=" . intval($IDValue), "Path", $this->db);

		//javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'".FILE_TABLE."','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','".session_id()."','$rootDirID','',".(we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1).")
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID',''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button
		);
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = "", $acObject = null, $contentType = ""){
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$button = we_button::create_button("select", "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','$filter',document.we_form.elements['$IDName'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, "", 'readonly', "text", $width, 0), "", "left", "defaultfont", "", we_html_tools::getPixel(20, 4), we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	function formWeChooser($table = FILE_TABLE, $width = "", $rootDirID = 0, $IDName = "ID", $IDValue = "0", $Pathname = "Path", $Pathvalue = "/", $cmd = "", $open_doc = "", $acObject = null, $contentType = ""){
		if($Pathvalue == ""){
			$Pathvalue = f("SELECT Path FROM " . $this->db->escape($table) . " WHERE ID=" . intval($IDValue), "Path", $this->db);
		}

		//javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'$table','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','".session_id()."','$rootDirID','','$open_doc')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID','','$open_doc')");
		if(is_object($acObject)){

			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId($IDName);
			$yuiSuggest->setContentType("folder,text/weTmpl");
			$yuiSuggest->setInput($Pathname, $Pathvalue);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector("Docselector");
			$yuiSuggest->setTable($table);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);
			return $yuiSuggest->getHTML();
		} else{
			return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", 'top.content.hot=1; readonly', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button);
		}
	}

	function formWeDocChooser($table = FILE_TABLE, $width = "", $rootDirID = 0, $IDName = "ID", $IDValue = "0", $Pathname = "Path", $Pathvalue = "/", $cmd = "", $filter = "text/webedition", $acObject = null){
		if($Pathvalue == "")
			$Pathvalue = f("SELECT Path FROM " . $this->db->escape($table) . " WHERE ID=" . intval($IDValue), "Path", $this->db);

		//javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'$table','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','".session_id()."','$rootDirID','$filter',".(we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1).")"
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));

		$button = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID','$filter'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")");
		if(is_object($acObject)){

			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId($IDName);
			$yuiSuggest->setContentType("folder,text/xml,text/webedition,image/*,text/html,application/*,application/x-shockwave-flash,video/quicktime");
			$yuiSuggest->setInput($Pathname, $Pathvalue);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector("Docselector");
			$yuiSuggest->setTable($table);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);
			return $yuiSuggest->getHTML();
		} else{
			return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", 'top.content.hot=1; readonly', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button);
		}
	}

	function formNewsletterDirChooser($width = "", $rootDirID = 0, $IDName = "ID", $IDValue = "0", $Pathname = "Path", $Pathvalue = "/", $cmd = "", $acObject = null){
		$table = NEWSLETTER_TABLE;
		if($Pathvalue == ""){
			$Pathvalue = f("SELECT Path FROM " . $this->db->escape($table) . " WHERE ID=" . intval($IDValue), "Path", $this->db);
		}

		//javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','top.opener._EditorFrame.setEditorIsHot(true);','".session_id()."','$rootDir','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));

		$button = we_button::create_button("select", "javascript:we_cmd('openNewsletterDirselector',document.we_form.elements['$IDName'].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID')");
		if(is_object($acObject)){
			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId("PathGroup");
			$yuiSuggest->setContentType("folder");
			$yuiSuggest->setInput($Pathname, str_replace('\\', '/', $Pathvalue));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector("Dirselector");
			$yuiSuggest->setTable(NEWSLETTER_TABLE);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);

			return $yuiSuggest->getHTML();
		} else{

			return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", 'top.content.hot=1; readonly id="yuiAcInputPathGroup"', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button
			);
		}
	}

	function getFields($id, $table){
		$ClassName = f('SELECT ClassName FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($id), "ClassName", $this->db);
		$foo = array();

		if($ClassName){
			$ent = new $ClassName();
			$ent->initByID($id, $table);
			$tmp = array_keys($ent->elements);

			foreach($tmp as $v){
				$foo[$v] = $v;
			}
		}

		return $foo;
	}

	/* 	function getObjectFields(){
	  $ClassName = f("SELECT ClassName FROM " . FILE_TABLE . " WHERE ID=" . intval($id), "ClassName", $this->db);

	  $doc = new $ClassName();

	  $doc->initByID($id);
	  $tmp = array_keys($doc->elements);
	  $foo = array();

	  foreach($tmp as $k => $v){
	  $foo[$v] = $v;
	  }

	  return $foo;
	  return array();
	  } */

	function getJSTopCode(){
		$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
		$title = '';
		foreach($GLOBALS["_we_available_modules"] as $modData){
			if($modData["name"] == $mod){
				$title = "webEdition " . g_l('global', "[modules]") . ' - ' . $modData["text"];
				break;
			}
		}

		return we_html_element::jsElement('
			var get_focus = 1;
			var hot = 0;

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

			/**
			  * Menu command controler
			  */
			function we_cmd() {
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

				if(hot == "1" && arguments[0] != "save_newsletter") {
					if(confirm("' . g_l('modules_newsletter', '[save_changed_newsletter]') . '")) {
						arguments[0] = "save_newsletter";
					} else {
						top.content.usetHot();
					}
				}
				switch (arguments[0]) {
					case "exit_newsletter":
						if(hot != "1") {
							eval(\'top.opener.top.we_cmd("exit_modules")\');
						}
						break;

					case "new_newsletter":
						if(top.content.resize.right.editor.edbody.loaded) {
							top.content.resize.right.editor.edbody.document.we_form.ncmd.value = arguments[0];
							top.content.resize.right.editor.edbody.submitForm();
						} else {
							setTimeout("we_cmd(\"new_newsletter\");", 10);
						}
						break;

					case "new_newsletter_group":
						if(top.content.resize.right.editor.edbody.loaded) {
							top.content.resize.right.editor.edbody.document.we_form.ncmd.value = arguments[0];
							top.content.resize.right.editor.edbody.submitForm();
						} else {
							setTimeout("we_cmd(\"new_newsletter_group\");", 10);
						}
						break;

					case "delete_newsletter":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							' . ((!we_hasPerm("DELETE_NEWSLETTER")) ? (
					we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					) : ('
									if (top.content.resize.right.editor.edbody.loaded) {
										var delQuestion = top.content.resize.right.editor.edbody.document.we_form.IsFolder.value == 1 ? "' . g_l('modules_newsletter', '[delete_group_question]') . '" : "' . g_l('modules_newsletter', '[delete_question]') . '";
										if (!confirm(delQuestion)) {
											return;
										}
									} else {
										' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
									}
									' . $this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?home=1&pnt=edheader";
									' . $this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";
									top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
									top.content.resize.right.editor.edbody.submitForm();
							')) . '
						}
						break;

					case "save_newsletter":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							' . ((!we_hasPerm("EDIT_NEWSLETTER") && !we_hasPerm("NEW_NEWSLETTER")) ? (
					we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					) : ('
									if (top.content.resize.right.editor.edbody.loaded) {
										if (!top.content.resize.right.editor.edbody.checkData()) {
											return;
										}
										top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
										top.content.resize.right.editor.edbody.submitForm();

									} else {
										' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
									}
							')) . '
							top.content.usetHot();
						}
						break;

					case "edit_newsletter":
						top.content.hot=0;
						top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
						top.content.resize.right.editor.edbody.document.we_form.nid.value=arguments[1];
						top.content.resize.right.editor.edbody.submitForm();
						break;

					case "send_test":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(top.content.resize.right.editor.edbody.document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							top.content.resize.right.editor.edbody.we_cmd("send_test");
						}
						break;

					case "empty_log":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(top.content.resize.right.editor.edbody.document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							new jsWindow("' . $this->frameset . '?pnt=qlog","log_question",-1,-1,330,230,true,false,true);
						}
						break;

					case "preview_newsletter":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(top.content.resize.right.editor.edbody.document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							top.content.resize.right.editor.edbody.we_cmd("popPreview");
						}
						break;

					case "send_newsletter":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(top.content.resize.right.editor.edbody.document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							top.content.resize.right.editor.edbody.we_cmd("popSend");
						}
						break;

					case "test_newsletter":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(top.content.resize.right.editor.edbody.document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							top.content.resize.right.editor.edbody.we_cmd("popSend","1");
						}
						break;

					case "domain_check":
					case "show_log":
					case "print_lists":
					case "search_email":
					case "clear_log":
						if(top.content.resize.right.editor.edbody.document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(top.content.resize.right.editor.edbody.document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							top.content.resize.right.editor.edbody.we_cmd(arguments[0]);
						}
						break;

					case "newsletter_settings":
					case "black_list":
					case "edit_file":
						top.content.resize.right.editor.edbody.we_cmd(arguments[0]);
						break;

					case "home":
						top.content.resize.right.editor.location="' . $this->frameset . '?pnt=editor";
						break;

					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.opener.top.we_cmd(" + args + ")");
				}
			}


		');
	}

	function getJSFooterCode(){
		return we_html_element::jsElement(
				'function doUnload() {
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
					case "empty_log":
						break;

					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("parent.edbody.we_cmd(" + args + ")");
				}
			}');
	}

	function getJSCmd(){
		return we_html_element::jsElement(
				'function submitForm() {
				var f = self.document.we_form;

				f.target = "cmd";
				f.method = "post";
				f.submit();
			}');
	}

	function getJSProperty(){
		if(isset($this->settings['reject_save_malformed']) && $this->settings['reject_save_malformed']){
			$_mailCheck = "we.validate.email(email);";
		} else{
			$_mailCheck = "true";
		}
		$js = we_html_element::jsScript(JS_DIR . "windows.js");
		$js.=we_html_element::jsScript(JS_DIR . "libs/we/weValidate.js");

		$js.=we_html_element::jsElement(
				'function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			/**
			  * Newsletter command controler
			  */
			function we_cmd() {

				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

				if (arguments[0] != "switchPage") {
					self.setScrollTo();
				}

				switch (arguments[0]) {
					case "browse_users":
						new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
						break;

					case "browse_server":
						new jsWindow(url,"browse_server",-1,-1,840,400,true,false,true);
						break;

					case "openDocselector":
						new jsWindow(url,"we_docselector",-1,-1,' . WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
						break;

					case "openSelector":
						new jsWindow(url,"we_selector",-1,-1,' . WINDOW_SELECTOR_WIDTH . ',' . WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
						break;

					case "openNewsletterDirselector":
						url = "' . WE_MODULES_DIR . 'newsletter/we_dirfs.php?";
						for(var i = 0; i < arguments.length; i++){
							url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }
						}
						new jsWindow(url,"we_newsletter_dirselector",-1,-1,600,400,true,true,true);
						break;

					case "add_customer":
						document.we_form.ngroup.value=arguments[2];

					case "del_customer":
						document.we_form.ncmd.value=arguments[0];
						document.we_form.ncustomer.value=arguments[1];
						top.content.hot=1;
						submitForm();
						break;

					case "del_all_customers":
					case "del_all_files":
						top.content.hot=1;
						document.we_form.ncmd.value=arguments[0];
						document.we_form.ngroup.value=arguments[1];
						submitForm();
						break;

					case "add_file":
						document.we_form.ngroup.value=arguments[2];
					case "del_file":
						document.we_form.ncmd.value=arguments[0];
						document.we_form.nfile.value=arguments[1];
						top.content.hot=1;
						submitForm();
						break;

					case "switchPage":
						document.we_form.ncmd.value=arguments[0];
						document.we_form.page.value=arguments[1];
						submitForm();
						break;

					case "set_import":
					case "reset_import":
					case "set_export":
					case "reset_export":
						document.we_form.ncmd.value=arguments[0];
						document.we_form.ngroup.value=arguments[1];
						submitForm();
						break;

					case "addBlock":
					case "delBlock":
						document.we_form.ncmd.value=arguments[0];
						document.we_form.blockid.value=arguments[1];
						top.content.hot=1;
						submitForm();
						break;

					case "addGroup":
					case "delGroup":
						document.we_form.ncmd.value=arguments[0];
						document.we_form.ngroup.value=arguments[1];
						top.content.hot=1;
						submitForm();
						break;

					case "popPreview":
						if(document.we_form.ncmd.value=="home") return;
						if (top.content.hot!=0) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[must_save_preview]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							document.we_form.elements["we_cmd[0]"].value="preview_newsletter";
							document.we_form.gview.value=parent.edfooter.document.we_form.gview.value;
							document.we_form.hm.value=parent.edfooter.document.we_form.hm.value;
							popAndSubmit("newsletter_preview","preview",800,800)
						}
						break;

					case "popSend":
						if(document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if (top.content.hot!=0) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[must_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {

							if (arguments[1]) {
								message_text="' . g_l('modules_newsletter', '[send_test_question]') . '";
							} else {
								message_text="' . g_l('modules_newsletter', '[send_question]') . '";
							}

							if (confirm(message_text)) {
									document.we_form.ncmd.value=arguments[0];
									if(arguments[1]) document.we_form.test.value=arguments[1];
									submitForm();
							}
						}
						break;

					case "send_test":
						if(document.we_form.ncmd.value=="home") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if (top.content.hot!=0) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[must_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(document.we_form.IsFolder.value==1) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_newsletter_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else {
							if (confirm("' . sprintf(g_l('modules_newsletter', '[test_email_question]'), $this->newsletter->Test) . '")) {
								document.we_form.ncmd.value=arguments[0];
								document.we_form.gview.value=parent.edfooter.document.we_form.gview.value;
								document.we_form.hm.value=parent.edfooter.document.we_form.hm.value;
								submitForm();
							}
						}
						break;
					case "print_lists":
					case "domain_check":
					case "show_log":
						if(document.we_form.ncmd.value!="home")
							popAndSubmit(arguments[0],arguments[0],650,650);
						break;
					case "newsletter_settings":
						new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,600,750,true,true,true,true);
						break;

					case "black_list":
						new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,560,460,true,true,true,true);
						break;

					case "edit_file":
						if (arguments[1]){
							new jsWindow("' . $this->frameset . '?pnt="+arguments[0]+"&art="+arguments[1],arguments[0],-1,-1,950,640,true,true,true,true);
						} else {
							new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,950,640,true,true,true,true);
						}
						break;

					case "reload_table":
					case "copy_newsletter":
						top.content.hot=1;
						document.we_form.ncmd.value=arguments[0];
						submitForm();
						break;

					case "add_filter":
					case "del_filter":
					case "del_all_filters":
						top.content.hot=1;
						document.we_form.ncmd.value=arguments[0];
						document.we_form.ngroup.value=arguments[1];
						submitForm();
						break;

					case "switch_sendall":
						document.we_form.ncmd.value=arguments[0];
						top.content.hot=1;
						eval("if(document.we_form.sendallcheck_"+arguments[1]+".checked) document.we_form.group"+arguments[1]+"_SendAll.value=1; else document.we_form.group"+arguments[1]+"_SendAll.value=0;");
						submitForm();
						break;

					case "save_settings":
						document.we_form.ncmd.value=arguments[0];
						submitForm("newsletter_settings");
						break;

					case "import_csv":
					case "export_csv":
						document.we_form.ncmd.value=arguments[0];
						submitForm();
						break;

					case "do_upload_csv":
						document.we_form.ncmd.value=arguments[0];
						submitForm("upload_csv");
						break;

					case "do_upload_black":
						document.we_form.ncmd.value=arguments[0];
						submitForm("upload_black");
						break;

					case "upload_csv":
					case "upload_black":
						new jsWindow("' . $this->frameset . '?pnt="+arguments[0]+"&grp="+arguments[1],arguments[0],-1,-1,450,270,true,true,true,true);
						break;

					case "add_email":
						var email=document.we_form.group=arguments[1];
						new jsWindow("' . $this->frameset . '?pnt=eemail&grp="+arguments[1],"edit_email",-1,-1,450,270,true,true,true,true);
						break;

					case "edit_email":
						eval("var p=document.we_form.we_recipient"+arguments[1]+";");

						if (p.selectedIndex < 0) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}

						eval("var dest=document.we_form.group"+arguments[1]+"_Emails;");

						var str=dest.value;

						var arr = str.split("\n");

						var str2=arr[p.selectedIndex];
						var arr2=str2.split(",");
						var eid=p.selectedIndex;
						var email=p.options[p.selectedIndex].text;
						var htmlmail=arr2[1];
						var salutation=arr2[2];
						var title=arr2[3];
						var firstname=arr2[4];
						var lastname=arr2[5];

						salutation=encodeURIComponent(salutation.replace("+","[:plus:]"));
						title=encodeURIComponent(title.replace("+","[:plus:]"));
						firstname=encodeURIComponent(firstname.replace("+","[:plus:]"));
						lastname=encodeURIComponent(lastname.replace("+","[:plus:]"));
						email = encodeURIComponent(email);
						new jsWindow("' . $this->frameset . '?pnt=eemail&grp="+arguments[1]+"&etyp=1&eid="+eid+"&email="+email+"&htmlmail="+htmlmail+"&salutation="+salutation+"&title="+title+"&firstname="+firstname+"&lastname="+lastname,"edit_email",-1,-1,450,270,true,true,true,true);
						break;

					case "save_black":
					case "import_black":
					case "export_black":
						document.we_form.ncmd.value=arguments[0];
						PopulateVar(document.we_form.blacklist_sel,document.we_form.black_list);
						submitForm("black_list");
						break;
					case "search_email":
						if(document.we_form.ncmd.value=="home") return;
						var searchname=prompt("' . g_l('modules_newsletter', '[search_text]') . '","");

						if (searchname != null) {
							searchEmail(searchname);
						}

						break;
					case "clear_log":
						new jsWindow("' . $this->frameset . '?pnt="+arguments[0],arguments[0],-1,-1,450,300,true,true,true,true);
						break;

					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.content.we_cmd("+args+")");
				}
			}

			function submitForm() {

				if (self.weWysiwygSetHiddenText) {
					weWysiwygSetHiddenText();
				}

				var f = self.document.we_form;

				if (arguments[0]) {
					f.target = arguments[0];
				} else {
					f.target = "edbody";
				}

				if (arguments[1]) {
					f.action = arguments[1];
				} else {
					f.action = "' . $this->frameset . '";
				}

				if (arguments[2]) {
					f.method = arguments[2];
				} else {
					f.method = "post";
				}
				f.submit();
			}

			function popAndSubmit(wname, pnt, width, height) {

				old = document.we_form.pnt.value;
				document.we_form.pnt.value=pnt;

				new jsWindow("' . HTML_DIR . 'white.html",wname,-1,-1,width,height,true,true,true,true);


				' . (((we_base_browserDetect::isMAC()) && (we_base_browserDetect::isIE())) ? '
							setTimeout("submitForm(\'"+wname+"\');", 250);
							setTimeout("document.we_form.pnt.value=old;", 350);
				' : '

					submitForm(wname);
					document.we_form.pnt.value=old;
				') . '

			}

			function doScrollTo() {
				if (parent.scrollToVal) {
					window.scrollTo(0, parent.scrollToVal);
					parent.scrollToVal = 0;
				}
			}

			function setScrollTo() {
				parent.scrollToVal = ' . ((we_base_browserDetect::isIE()) ? 'document.body.scrollTop' : 'pageYOffset') . ';
			}

			function switchRadio(a, b) {
				a.value = 1;
				a.checked = true;
				b.value = 0;
				b.checked = false;

				if (arguments[3]) {
					c = arguments[3];
					c.value = 0;
					c.checked = false;
				}
			}

			function clickCheck(a) {
				if (a.checked) {
					a.value = 1;
				} else {
					a.value=0;
				}
			}

			function getStatusContol() {
				return document.we_form.' . (isset($this->uid) ? $this->uid : "") . '_Status.value;
			}

			function getNumOfDocs() {
				return 0;
			}

			function sprintf() {
				if (!arguments || arguments.length < 1) {
					return;
				}

				var argum = arguments[0];
				var regex = /([^%]*)%(%|d|s)(.*)/;
				var arr = new Array();
				var iterator = 0;
				var matches = 0;

				while (arr = regex.exec(argum)) {
					var left = arr[1];
					var type = arr[2];
					var right = arr[3];

					matches++;
					iterator++;

					var replace = arguments[iterator];

					if (type=="d") {
						replace = parseInt(param) ? parseInt(param) : 0;
					} else if (type=="s") {
						replace = arguments[iterator];
					}

					argum = left + replace + right;
				}

				return argum;
			}

			function checkData() {
				if (document.we_form.Text.value == "") {
					' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[empty_name]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					return false;
				}

				return true;
			}

			function markEMails() {

			}
			function add(group, newRecipient, htmlmail, salutation, title, firstname, lastname) {
				var p = document.forms[0].elements["we_recipient"+group];

				if (newRecipient != null) {

					if (newRecipient.length > 0) {

						if (newRecipient.length > 255 ) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}

						if (!inSelectBox(document.forms[0].elements["we_recipient"+group], newRecipient)) {
							if(isValidEmail(newRecipient)) optionClassName = "markValid";
							else optionClassName = "markNotValid";
							addElement(document.forms[0].elements["we_recipient"+group],"#",newRecipient,true,optionClassName);
							addEmail(group,newRecipient,htmlmail,salutation,title,firstname,lastname);
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_exists]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}
					//set_state_edit_delete_recipient("we_recipient"+group);
				}
			}

			function isValidEmail(email){
				email = email.toLowerCase();
				return ' . ($_mailCheck) . ';
				//return (email.match(/^([[:space:]_:\+\.0-9a-z-]+[\<]{1})?[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,6}(\>)?$/) ? true : false);
			}

			function deleteit(group) {
				var p=document.forms[0].elements["we_recipient"+group];

				if (p.selectedIndex >= 0) {
					if (confirm("' . g_l('modules_newsletter', '[email_delete]') . '")) {
						delEmail(group,p.selectedIndex);
						p.options[p.selectedIndex] = null;
					}
				} else {
					' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				}
				//set_state_edit_delete_recipient("we_recipient"+group);
			}

			function deleteall(group) {
				var p = document.forms[0].elements["we_recipient"+group];

				if (confirm("' . g_l('modules_newsletter', '[email_delete_all]') . '")) {
					delallEmails(group);
					we_cmd("switchPage",1);
				}
				//set_state_edit_delete_recipient("we_recipient"+group);
			}

			function in_array(n, h) {
				for (var i = 0; i < h.length; i++) {

					if (h[i] == n) {
						return true;
					}
				}

				return false;
			}

			function editIt(group, index, editRecipient, htmlmail, salutation, title, firstname, lastname) {
				var p = document.forms[0].elements["we_recipient"+group];

				if (index >= 0 && editRecipient != null) {

					if (editRecipient != "") {

						if (editRecipient.length > 255 ) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}
						if(isValidEmail(editRecipient)) optionClassName = "markValid";
						else optionClassName = "markNotValid";
						p.options[index].text = editRecipient;
						p.options[index].className = optionClassName;
						editEmail(group,index,editRecipient,htmlmail,salutation,title,firstname,lastname);
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}
				}
			}

			function PopulateVar(p, dest) {
				var arr = new Array();

				for (i = 0; i < p.length; i++) {
					arr[i] = p.options[i].text;
				}

				dest.value=arr.join();
			}

			function PopulateMultipleVar(p, dest){
				var arr = new Array();
				c = 0;

				for (i = 0; i < p.length; i++) {

					if (p.options[i].selected) {
						c++;
						arr[c] = p.options[i].value;
					}
				}

				dest.value=arr.join();
			}

			function addEmail(group, email, html, salutation, title, firstname, lastname) {
				var dest = document.forms[0].elements["group"+group+"_Emails"]
				var str = dest.value;

				if( str.length > 0) {

					var arr = str.split("\n");

				} else {
					var arr=new Array();
				}

				arr[arr.length] = email+","+html+","+salutation+","+title+","+firstname+","+lastname;


				dest.value = arr.join("\n");

				top.content.hot=1;
			}

			function editEmail(group, id, email, html, salutation, title, firstname, lastname) {
				var dest = document.forms[0].elements["group"+group+"_Emails"]
				var str = dest.value;

				var arr = str.split("\n");

				arr[id] = email+","+html+","+salutation+","+title+","+firstname+","+lastname;


				dest.value = arr.join("\n");

				top.content.hot = 1;
			}

			function mysplice(arr, id) {
				var newarr = new Array();

				for (i = 0; i < arr.lenght; i++) {

					if (i!=id) {
						newarr[newarr.lenght] = arr[id];
					}
				}
				return newarr;
			}

			function delEmail(group,id) {
				var dest = document.forms[0].elements["group"+group+"_Emails"]
				var str = dest.value;

				var arr = str.split("\n");

				arr.splice(id, 1);

				dest.value = arr.join("\n");

				top.content.hot=1;
			}

			function delallEmails(group) {
				var dest = document.forms[0].elements["group"+group+"_Emails"]

				dest.value = "";
				top.content.hot = 1;
			}

			function inSelectBox(p, val) {
				for (var i = 0; i < p.options.length; i++) {

					if (p.options[i].text == val) {
						return true;
					}
				}
				return false;
			}

			function addElement(p,value, text, sel, optionClassName) {
				var i = p.length;

				p.options[i] =  new Option(text,value);
				p.options[i].className = optionClassName;

				if (sel) {
					p.selectedIndex = i;
				}
			}

			function getGroupsNum() {
				return document.we_form.groups.value;
			}

			function searchEmail(searchname) {
				var f = document.we_form;
				var c;
				var hit = 0;

				if(document.we_form.page.value==1){
					for (i = 0; i < f.groups.value; i++) {
						c = f.elements["we_recipient" + i];
						c.selectedIndex = -1;

						for (j = 0; j < c.length; j++) {
							if (c.options[j].text == searchname) {
								c.selectedIndex = j;
								hit++;
							}
						}
					}
					msg = sprintf("' . g_l('modules_newsletter', '[search_finished]') . '",hit);
					' . we_message_reporting::getShowMessageCall("msg", we_message_reporting::WE_MESSAGE_NOTICE, true) . '
				}

			}

			function set_state_edit_delete_recipient(control) {
					var p = document.forms[0].elements[control];
					var i = p.length;

					if (i == 0) {
						switch_button_state("edit", "edit_enabled", "disabled");
						switch_button_state("delete", "delete_enabled", "disabled");
						switch_button_state("delete_all", "delete_all_enabled", "disabled");
						//edit_enabled = switch_button_state("edit", "edit_enabled", "disabled");
						//delete_enabled = switch_button_state("delete", "delete_enabled", "disabled");
						//delete_all_enabled = switch_button_state("delete_all", "delete_all_enabled", "disabled");

					} else {
						switch_button_state("edit", "edit_enabled", "enabled");
						switch_button_state("delete", "delete_enabled", "enabled");
						switch_button_state("delete_all", "delete_all_enabled", "enabled");
						//edit_enabled = switch_button_state("edit", "edit_enabled", "enabled");
						//delete_enabled = switch_button_state("delete", "delete_enabled", "enabled");
						//delete_all_enabled = switch_button_state("delete_all", "delete_all_enabled", "enabled");
					}
			}


		');
		//$js.=we_button::create_state_changer();

		return $js;
	}

	function processCommands(){
		if(isset($_REQUEST["ncmd"])){
			switch($_REQUEST["ncmd"]){
				case "new_newsletter":
					$this->newsletter = new weNewsletter();
					$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter]');
					$this->newsletter->Sender = $this->settings["default_sender"];
					$this->newsletter->Reply = $this->settings["default_reply"];
					$this->newsletter->Test = $this->settings["test_account"];
					$this->newsletter->isEmbedImages = $this->settings['isEmbedImages'];

					print we_html_element::jsElement('
							top.content.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader' . (isset($_REQUEST["page"]) ? ("&page=" . $_REQUEST["page"]) : ("")) . '";
							top.content.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
					');
					break;
				case "new_newsletter_group":
					$this->page = 0;
					$this->newsletter = new weNewsletter();
					$this->newsletter->IsFolder = "1";
					$this->newsletter->Text = g_l('modules_newsletter', '[new_newsletter_group]');
					print we_html_element::jsElement('
							top.content.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&group=1";
							top.content.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter&group=1";
					');
					break;
				case "add_customer":

					if(isset($_REQUEST["ngroup"])){
						$arr = makeArrayFromCSV($this->newsletter->groups[$_REQUEST["ngroup"]]->Customers);

						if(isset($_REQUEST["ncustomer"])){

							$ids = makeArrayFromCSV($_REQUEST["ncustomer"]);
							foreach($ids as $id){
								if($id && (!in_array($id, $arr))){
									array_push($arr, $id);
								}
							}

							$this->newsletter->groups[$_REQUEST["ngroup"]]->Customers = makeCSVFromArray($arr, true);
						}
					}
					break;

				case "del_customer":
					$arr = array();

					if(isset($_REQUEST["ngroup"])){
						$arr = makeArrayFromCSV($this->newsletter->groups[$_REQUEST["ngroup"]]->Customers);

						if(isset($_REQUEST["ncustomer"])){

							foreach($arr as $k => $v){

								if($v == $_REQUEST["ncustomer"]){
									array_splice($arr, $k, 1);
								}
							}
							$this->newsletter->groups[$_REQUEST["ngroup"]]->Customers = makeCSVFromArray($arr, true);
						}
					}
					break;

				case "add_file":
					$arr = array();
					if(isset($_REQUEST["ngroup"])){
						$arr = makeArrayFromCSV($this->newsletter->groups[$_REQUEST["ngroup"]]->Extern);
						if(isset($_REQUEST["nfile"])){
							$_sd = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
							$arr[] = str_replace($_sd, (substr($_sd, -1) == "/" ? "/" : ""), $_REQUEST["nfile"]);
							$this->newsletter->groups[$_REQUEST["ngroup"]]->Extern = makeCSVFromArray($arr, true);
						}
					}
					break;

				case "del_file":
					$arr = array();
					if(isset($_REQUEST["ngroup"])){
						$arr = makeArrayFromCSV($this->newsletter->groups[$_REQUEST["ngroup"]]->Extern);
						if(isset($_REQUEST["nfile"])){
							foreach($arr as $k => $v){
								if($v == $_REQUEST["nfile"]){
									array_splice($arr, $k, 1);
								}
							}
							$this->newsletter->groups[$_REQUEST["ngroup"]]->Extern = makeCSVFromArray($arr, true);
						}
					}
					break;
				case "del_all_files":
					if(isset($_REQUEST["ngroup"])){
						$this->newsletter->groups[$_REQUEST["ngroup"]]->Extern = "";
					}
					break;
				case "del_all_customers":
					if(isset($_REQUEST["ngroup"])){
						$this->newsletter->groups[$_REQUEST["ngroup"]]->Customers = "";
					}
					break;

				case "reload":
					print we_html_element::jsElement('
							top.content.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&page=' . $this->page . '&txt=' . urlencode($this->newsletter->Text) . ($this->newsletter->IsFolder ? '&group=1' : '') . '";
							top.content.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter' . ($this->newsletter->IsFolder ? '&group=1' : '') . '";
					');
					break;

				case "edit_newsletter":

					if(isset($_REQUEST["nid"])){
						$this->newsletter = new weNewsletter($_REQUEST["nid"]);
					}
					if($this->newsletter->IsFolder)
						$this->page = 0;
					$_REQUEST["ncmd"] = "reload";
					$this->processCommands();
					break;

				case "switchPage":
					if(isset($_REQUEST["page"])){
						$this->page = $_REQUEST["page"];
					}
					break;

				case "save_newsletter":
					if(isset($_REQUEST["nid"])){
						$weAcQuery = new weSelectorQuery();
						$newone = false;



						if($this->newsletter->filenameNotValid()){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							return;
						}

						if($this->newsletter->ParentID > 0){
							$weAcResult = $weAcQuery->getItemById($this->newsletter->ParentID, NEWSLETTER_TABLE, array("IsFolder"), false);
							if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 0){
								print we_html_element::jsElement(
										we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
									);
								return;
							}
						}

						if(isset($_REQUEST['blocks'])){

							for($i = 0; $i < $_REQUEST['blocks']; $i++){
								switch($_REQUEST['block' . $i . "_Type"]){
									case 0:
									case 1:
										$acTable = FILE_TABLE;
										$acErrorField = g_l('modules_newsletter', '[block_document]');
										break;
									case 2:
									case 3:
										$acTable = OBJECT_FILES_TABLE;
										$acErrorField = g_l('modules_newsletter', '[block_object]');
										break;
									default:
										$acTable = '';
										$acErrorField = '';
								}
								if(!empty($acTable)){
									$weAcResult = $weAcQuery->getItemById($_REQUEST['block' . $i . "_LinkID"], $acTable, array("IsFolder"));

									if(!is_array($weAcResult) || count($weAcResult) < 1 || $weAcResult[0]['IsFolder'] == 1){
										print we_html_element::jsElement(
												we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[blockFieldError]'), ($i + 1), $acErrorField), we_message_reporting::WE_MESSAGE_ERROR)
											);
										return;
									}
									if(!empty($_REQUEST['block' . $i . "_Field"]) && $_REQUEST['block' . $i . "_Field"] > 0){
										$weAcResult = $weAcQuery->getItemById($_REQUEST['block' . $i . "_Field"], TEMPLATES_TABLE, array("IsFolder"));
										if(!is_array($weAcResult) || $weAcResult[0]['IsFolder'] == 1){
											print we_html_element::jsElement(
													we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[blockFieldError]'), $i, g_l('modules_newsletter', '[block_template]')), we_message_reporting::WE_MESSAGE_ERROR)
												);
											return;
										}
									}
								}
							}
						}

						if(!$this->newsletter->ID){
							$newone = true;
						}

						if(!$newone && $_REQUEST["ask"]){
							$h = getHash("SELECT Step,Offset FROM " . NEWSLETTER_TABLE . " WHERE ID=" . $this->newsletter->ID, $this->db);

							if($h["Step"] != 0 || $h["Offset"] != 0){
								print we_html_element::jsScript(JS_DIR . "windows.js");
								print we_html_element::jsElement('
										self.focus();
										top.content.get_focus=0;
										new jsWindow("' . $this->frameset . '?pnt=qsave1","save_question",-1,-1,350,200,true,true,true,false);
									');
								break;
							}
						}

						if($this->newsletter->Sender == ""){
							$this->newsletter->Sender = $this->settings["default_sender"];
						}

						if($this->newsletter->Reply == ""){
							$this->newsletter->Reply = $this->settings["default_reply"];
						}

						if($this->newsletter->Test == ""){
							$this->newsletter->Test = $this->settings["test_account"];
						}
						if($this->newsletter->isEmbedImages == ""){
							$this->newsletter->isEmbedImages = $this->settings["isEmbedImages"];
						}

						$double = intval(f('SELECT COUNT(1) AS Count FROM ' . NEWSLETTER_TABLE . " WHERE Path='" . $this->db->escape($this->newsletter->Path) . "'" . ($newone ? '' : ' AND ID<>' . $this->newsletter->ID), 'Count', $this->db));

						if(!we_hasPerm("EDIT_NEWSLETTER") && !we_hasPerm("NEW_NEWSLETTER")){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							return;
						} else if($newone && !we_hasPerm("NEW_NEWSLETTER")){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							return;
						} else if(!$newone && !we_hasPerm("EDIT_NEWSLETTER")){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							return;
						} else{

							if($double){
								print we_html_element::jsElement(
										we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[double_name]'), we_message_reporting::WE_MESSAGE_ERROR)
									);
								return;
							}

							$message = "";

							$ret = $this->newsletter->save($message, (isset($this->settings["reject_save_malformed"]) ? $this->settings["reject_save_malformed"] : true));

							if($ret != 0){
								$jsmess = "";
								if($ret > 0){
									$jsmess .= we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_group]'), $ret, $message), we_message_reporting::WE_MESSAGE_ERROR);
								} else if($ret == -1){
									$jsmess .= we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_sender]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
								} else if($ret == -2){
									$jsmess .= we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_reply]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
								} else if($ret == -3){
									$jsmess .= we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[malformed_mail_test]'), $message), we_message_reporting::WE_MESSAGE_ERROR);
								} else if($ret == -10){
									$jsmess .= we_message_reporting::getShowMessageCall($message, we_message_reporting::WE_MESSAGE_ERROR);
								}
							} else{
								$jsmess = "";

								if($newone){
									$jsmess.='
									' . $this->topFrame . '.makeNewEntry(\'' . $this->newsletter->Icon . '\',\'' . $this->newsletter->ID . '\',\'' . $this->newsletter->ParentID . '\',\'' . $this->newsletter->Text . '\',0,\'' . ($this->newsletter->IsFolder ? 'folder' : 'item') . '\',\'' . NEWSLETTER_TABLE . '\');
									';
								} else{
									$jsmess.=$this->topFrame . '.updateEntry("' . $this->newsletter->ID . '","' . $this->newsletter->Text . '","' . $this->newsletter->ParentID . '");';
								}

								$jsmess.='
										' . $this->topFrame . '.drawTree();
										' . we_message_reporting::getShowMessageCall(($this->newsletter->IsFolder == 1 ? g_l('modules_newsletter', '[save_group_ok]') : g_l('modules_newsletter', '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '
										top.content.hot=0;
								';
							}
							print we_html_element::jsElement($jsmess);
						}
					}
					break;

				case "delete_newsletter":
					if(isset($_REQUEST["nid"])){
						if(!$_REQUEST["nid"]){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[delete_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							return;
						}
						if(!we_hasPerm("DELETE_NEWSLETTER")){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							return;
						} else{
							$this->newsletter = new weNewsletter($_REQUEST["nid"]);

							if($this->newsletter->delete()){
								$this->newsletter = new weNewsletter();
								print we_html_element::jsElement('
										top.content.deleteEntry(' . $_REQUEST["nid"] . ',"file");
										setTimeout(\'' . we_message_reporting::getShowMessageCall(($_REQUEST["IsFolder"] == 1 ? g_l('modules_newsletter', '[delete_group_ok]') : g_l('modules_newsletter', '[delete_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '\',500);
								');
								$_REQUEST['home'] = '1';
								$_REQUEST['pnt'] = 'edbody';
							} else{
								print we_html_element::jsElement(
										we_message_reporting::getShowMessageCall(($_REQUEST["IsFolder"] == 1 ? g_l('modules_newsletter', '[delete_group_nok]') : g_l('modules_newsletter', '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR)
									);
							}
						}
					} else{
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(($_REQUEST["IsFolder"] == 1 ? g_l('modules_newsletter', '[delete_group_nok]') : g_l('modules_newsletter', '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR)
							);
					}
					break;

				case "reload_table":
					$this->page = 1;
					break;

				case "set_import":
					$this->show_import_box = $_REQUEST["ngroup"];
					break;

				case "set_export":
					$this->show_export_box = $_REQUEST["ngroup"];
					break;

				case "reset_import":
					$this->show_import_box = -1;
					break;

				case "reset_export":
					$this->show_export_box = -1;
					break;

				case "addBlock":
					if(isset($_REQUEST["blockid"])){
						$this->newsletter->addBlock($_REQUEST["blockid"] + 1);
					}
					break;

				case "delBlock":
					if(isset($_REQUEST["blockid"])){
						$this->newsletter->removeBlock($_REQUEST["blockid"]);
					}
					break;

				case "addGroup":
					$this->newsletter->addGroup();
					print we_html_element::jsElement('
								var edf=top.content.resize.right.editor.edfooter;
								edf.document.we_form.gview.length = 0;
								edf.populateGroups();
					');
					break;

				case "delGroup":
					if(isset($_REQUEST["ngroup"])){
						$this->newsletter->removeGroup($_REQUEST["ngroup"]);
						print we_html_element::jsElement('
								var edf=top.content.resize.right.editor.edfooter;
								edf.document.we_form.gview.length = 0;
								edf.populateGroups();
						');
					}
					break;

				case "send_test":
					if(!we_hasPerm("SEND_TEST_EMAIL")){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						return;
					}
					$this->sendTestMail($_REQUEST["gview"], $_REQUEST["hm"]);
					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(sprintf(g_l('modules_newsletter', '[test_mail_sent]'), $this->newsletter->Test), we_message_reporting::WE_MESSAGE_NOTICE)
						);
					break;

				case "add_filter":
					$this->newsletter->groups[$_REQUEST["ngroup"]]->addFilter($this->customers_fields[0]);
					break;

				case "del_filter":
					$this->newsletter->groups[$_REQUEST["ngroup"]]->delFilter();
					break;

				case "del_all_filters":
					$this->newsletter->groups[$_REQUEST["ngroup"]]->delallFilter();
					break;

				case "copy_newsletter":
					$id = 0;
					if(isset($_REQUEST["copyid"])){
						$id = $this->newsletter->ID;
						$this->newsletter = new weNewsletter($_REQUEST["copyid"]);
						$this->newsletter->ID = $id;
						$this->newsletter->Text.="_" . g_l('modules_newsletter', '[copy]');
					}
					break;

				case "save_settings":
					foreach($this->settings as $k => $v){

						if(isset($_REQUEST[$k])){
							$this->settings[$k] = $_REQUEST[$k];
						} else{
							$this->settings[$k] = 0;
						}
					}
					$this->saveSettings();
					break;

				case "import_csv":
					if(isset($_REQUEST["csv_import"])){
						$importno = $_REQUEST["csv_import"];
						$filepath = $_REQUEST["csv_file" . $importno];
						$delimiter = $_REQUEST["csv_delimiter" . $importno];
						$col = $_REQUEST["csv_col" . $importno];

						if(isset($_REQUEST["csv_hmcol" . $importno]) && $_REQUEST["csv_hmcol" . $importno]){
							$hmcol = $_REQUEST["csv_hmcol" . $importno];
							$importHTML = true;
						} else{
							$hmcol = 0;
							$importHTML = false;
						}

						if(isset($_REQUEST["csv_salutationcol" . $importno]) && $_REQUEST["csv_salutationcol" . $importno]){
							$salutationcol = $_REQUEST["csv_salutationcol" . $importno];
							$importSalutation = true;
						} else{
							$salutationcol = 0;
							$importSalutation = false;
						}

						if(isset($_REQUEST["csv_titlecol" . $importno]) && $_REQUEST["csv_titlecol" . $importno]){
							$titlecol = $_REQUEST["csv_titlecol" . $importno];
							$importTitle = true;
						} else{
							$titlecol = 0;
							$importTitle = false;
						}

						if(isset($_REQUEST["csv_firstnamecol" . $importno]) && $_REQUEST["csv_firstnamecol" . $importno]){
							$firstnamecol = $_REQUEST["csv_firstnamecol" . $importno];
							$importFirstname = true;
						} else{
							$firstnamecol = 0;
							$importFirstname = false;
						}

						if(isset($_REQUEST["csv_lastnamecol" . $importno]) && $_REQUEST["csv_lastnamecol" . $importno]){
							$lastnamecol = $_REQUEST["csv_lastnamecol" . $importno];
							$importLastname = true;
						} else{
							$lastnamecol = 0;
							$importLastname = false;
						}

						if($col){
							$col--;
						}

						if($hmcol){
							$hmcol--;
						}

						if($salutationcol){
							$salutationcol--;
						}

						if($titlecol){
							$titlecol--;
						}

						if($firstnamecol){
							$firstnamecol--;
						}

						if($lastnamecol){
							$lastnamecol--;
						}

						if(strpos($filepath, '..') !== false){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
						} else{
							$row = array();
							$control = array();
							$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $filepath, "rb");
							$mailListArray = array();
							if($fh){
								$_mailListArray = explode("\n", $this->newsletter->groups[$importno]->Emails);
								foreach($_mailListArray as $line){
									$mailListArray[] = substr($line, 0, strpos($line, ","));
								}
								unset($_mailListArray);
								while($dat = fgetcsv($fh, 1000, $delimiter)) {

									if(!isset($control[$dat[$col]])){
										$_alldat = implode("", $dat);
										if(str_replace(" ", "", $_alldat) == ""){
											continue;
										}
										$mailrecip = (str_replace(" ", "", $dat[$col]) == "") ? "--- " . g_l('modules_newsletter', '[email_missing]') . " ---" : $dat[$col];
										if(!empty($mailrecip) && !in_array($mailrecip, $mailListArray)){
											$row[] = $mailrecip . "," .
												( ($importHTML && isset($dat[$hmcol])) ? $dat[$hmcol] : "") . "," .
												( ($importSalutation && isset($dat[$salutationcol])) ? $dat[$salutationcol] : "") . "," .
												( ($importTitle && isset($dat[$titlecol])) ? $dat[$titlecol] : "") . "," .
												( ($importFirstname && isset($dat[$firstnamecol])) ? $dat[$firstnamecol] : "") . "," .
												( ($importLastname && isset($dat[$lastnamecol])) ? $dat[$lastnamecol] : "");
											$control[$dat[$col]] = 1;
										}
									}
								}
								fclose($fh);
								if($this->newsletter->groups[$importno]->Emails != ""){
									$this->newsletter->groups[$importno]->Emails.="\n" . implode("\n", $row);
								} else{
									$this->newsletter->groups[$importno]->Emails.=implode("\n", $row);
								}
							} else{
								print we_html_element::jsElement(
										we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
									);
							}
						}
					}
					break;

				case "export_csv":
					if(isset($_REQUEST["csv_export"])){
						$exportno = $_REQUEST["csv_export"];

						if($_REQUEST["csv_dir" . $exportno] == "/"){
							$fname = "/emails_export_" . time() . ".csv";
						} else{
							$fname = $_REQUEST["csv_dir" . $exportno] . "/emails_export_" . time() . ".csv";
						}
						weFile::save($_SERVER['DOCUMENT_ROOT'] . $fname, $this->newsletter->groups[$exportno]->Emails);
						print we_html_element::jsScript(JS_DIR . "windows.js");
						print we_html_element::jsElement('
							new jsWindow("' . $this->frameset . '?pnt=export_csv_mes&lnk=' . $fname . '","edit_email",-1,-1,440,250,true,true,true,true);
						');
					}
					break;

				case "save_black":
					$this->saveSetting("black_list", $this->settings["black_list"]);
					print we_html_element::jsElement('
							self.close();
					');
					break;

				case "do_upload_csv":
					if(isset($_FILES["we_File"]))
						$we_File = $_FILES["we_File"];
					$group = 0;
					if(isset($_REQUEST["group"])){
						$group = $_REQUEST["group"];
					}

					if(isset($we_File)){
						$unique = weFile::getUniqueId();
						$tempName = TEMP_PATH . "/" . $unique;

						if(move_uploaded_file($we_File["tmp_name"], $tempName)){
							$tempName = str_replace($_SERVER['DOCUMENT_ROOT'], "", $tempName);
							print we_html_element::jsElement('
									opener.document.we_form.csv_file' . $group . '.value="' . $tempName . '";
									opener.we_cmd("import_csv");
									self.close();
							');
						} else{
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[upload_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
						}
					}
					break;

				case "do_upload_black":
					if(isset($_FILES["we_File"]))
						$we_File = $_FILES["we_File"];
					if(isset($we_File)){
						$unique = weFile::getUniqueId();
						$tempName = TEMP_PATH . "/" . $unique;

						if(move_uploaded_file($we_File["tmp_name"], $tempName)){
							$tempName = str_replace($_SERVER['DOCUMENT_ROOT'], "", $tempName);
							print we_html_element::jsElement('
								opener.document.we_form.csv_file.value="' . $tempName . '";
								opener.document.we_form.sib.value=0;
								opener.we_cmd("import_black");
								self.close();
							');
						} else{
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[upload_nok]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
						}
					}
					break;

				case "save_email_file":
					if(isset($_REQUEST["csv_file"]))
						$csv_file = $_REQUEST["csv_file"];
					if(isset($_REQUEST["nrid"]))
						$nrid = $_REQUEST["nrid"];
					if(isset($_REQUEST["email"]))
						$email = $_REQUEST["email"];
					if(isset($_REQUEST["htmlmail"]))
						$htmlmail = $_REQUEST["htmlmail"];
					if(isset($_REQUEST["salutation"]))
						$salutation = $_REQUEST["salutation"];
					if(isset($_REQUEST["title"]))
						$title = $_REQUEST["title"];
					if(isset($_REQUEST["firstname"]))
						$firstname = $_REQUEST["firstname"];
					if(isset($_REQUEST["lastname"]))
						$lastname = $_REQUEST["lastname"];

					$emails = array();
					if($csv_file)
						$emails = weNewsletter::getEmailsFromExtern($csv_file);
					$emails[$nrid] = array($email, $htmlmail, $salutation, $title, $firstname, $lastname);
					$emails_out = "";
					foreach($emails as $email){
						$emails_out.=makeCSVFromArray(array_slice($email, 0, 6)) . "\n";
					}

					if($csv_file){
						weFile::save($_SERVER['DOCUMENT_ROOT'] . $csv_file, $emails_out);
					}

					break;

				case "delete_email_file":
					if(isset($_REQUEST["nrid"]))
						$nrid = $_REQUEST["nrid"];
					if(isset($_REQUEST["csv_file"]))
						$csv_file = $_REQUEST["csv_file"];
					$emails = array();
					if($csv_file)
						$emails = weNewsletter::getEmailsFromExtern($csv_file, 2);

					if(isset($nrid)){
						array_splice($emails, $nrid, 1);
						$emails_out = "";
						foreach($emails as $email){
							$emails_out.=makeCSVFromArray($email) . "\n";
						}

						if($csv_file){
							weFile::save($_SERVER['DOCUMENT_ROOT'] . $csv_file, $emails_out);
						}
					}
					break;
				case "popSend":
					if(isset($_REQUEST["test"]) && $_REQUEST["test"] != 0)
						$url = 'url ="' . $this->frameset . '?pnt=send&nid=' . $this->newsletter->ID . '&test=1";';
					else
						$url = 'url ="' . $this->frameset . '?pnt=send&nid=' . $this->newsletter->ID . '";';

					print we_html_element::jsScript(JS_DIR . "windows.js");
					print we_html_element::jsElement(
							((trim($this->newsletter->Subject) == "") ? 'if(confirm("' . g_l('modules_newsletter', '[no_subject]') . '")){' : '') . '
							' . $url . '
							new jsWindow(url,"newsletter_send",-1,-1,600,400,true,true,true,false);
						' . ((trim($this->newsletter->Subject) == "") ? '}' : '')
						);
					break;
				default:
			}
		}
	}

	function processVariables(){
		if(isset($_REQUEST["wname"])){
			$this->uid = $_REQUEST["wname"];
		}

		if(is_array($this->newsletter->persistents)){
			foreach($this->newsletter->persistents as $val){
				if(isset($_REQUEST[$val])){
					$this->newsletter->$val = $_REQUEST[$val];
				}
			}
		}

		if($this->newsletter->ParentID)
			$this->newsletter->Path = f("SELECT Path FROM " . NEWSLETTER_TABLE . " WHERE ID=" . $this->newsletter->ParentID, "Path", $this->db) . "/" . $this->newsletter->Text;
		elseif(!$this->newsletter->filenameNotValid($this->newsletter->Text))
			$this->newsletter->Path = "/" . $this->newsletter->Text;

		if(isset($_REQUEST["page"])){
			$this->page = $_REQUEST["page"];
		}

		$groups = 0;

		if(isset($_REQUEST["groups"])){
			$groups = $_REQUEST["groups"];
		}

		$this->newsletter->groups = array();

		if($groups == 0){
			$this->newsletter->addGroup();
		}

		for($i = 0; $i < $groups; $i++){
			$this->newsletter->addGroup();
		}

		$fields_names = array("fieldname", "operator", "fieldvalue", "logic", "hours", "minutes");

		foreach($this->newsletter->groups as $gkey => &$gval){
			// persistens
			$gval->NewsletterID = $this->newsletter->ID;

			foreach($gval->persistents as $per){
				$varname = "group" . $gkey . "_" . $per;

				if(isset($_REQUEST[$varname])){
					$gval->$per = $_REQUEST[$varname];
				}
			}

			// Filter
			$count = 0;

			if(isset($_REQUEST["filter_" . $gkey])){
				$count = $_REQUEST["filter_" . $gkey];
			}

			if($count){
				$count++;
			}

			for($i = 0; $i < $count; $i++){
				$new = array();

				foreach($fields_names as $field){
					$varname = "filter_" . $field . "_" . $gkey . "_" . $i;

					if(isset($_REQUEST[$varname])){
						$new[$field] = $_REQUEST[$varname];
					}
				}

				if(count($new)){
					$gval->aFilter[] = $new;
				}
			}
		}
		unset($gval);
		$blocks = 0;

		if(isset($_REQUEST["blocks"])){
			$blocks = $_REQUEST["blocks"];
		}

		$this->newsletter->blocks = array();

		if($blocks == 0){
			$this->newsletter->addBlock();
		}

		for($i = 0; $i < $blocks; $i++){
			$this->newsletter->addBlock();
		}

		foreach($this->newsletter->blocks as $skey => &$sval){
			$sval->NewsletterID = $this->newsletter->ID;

			foreach($sval->persistents as $per){
				$varname = "block" . $skey . "_" . $per;

				if(isset($_REQUEST[$varname])){
					$sval->$per = $_REQUEST[$varname];
				}
			}
		}
		unset($gval);
	}

	function getTime($seconds){
		$ret = array("hour" => 0, "min" => 0, "sec" => 0);
		$ret["min"] = floor($seconds / 60);
		$ret["sec"] = $seconds - ($ret["min"] * 60);
		$ret["hour"] = floor($ret["min"] / 60);
		$ret["min"] = $ret["min"] - ($ret["hour"] * 60);
		return $ret;
	}

	/**
	 * Newsletter printing functions
	 */
	function initDocByObject(&$we_doc, $we_objectID){

		$we_obj = new we_objectFile();
		$we_obj->initByID($we_objectID, OBJECT_FILES_TABLE);

		$this->initDoc($we_doc);
		$we_doc->elements = $we_obj->elements;
		$we_doc->Templates = $we_obj->Templates;
		$we_doc->ExtraTemplates = $we_obj->ExtraTemplates;
		$we_doc->TableID = $we_obj->TableID;
		$we_doc->CreatorID = $we_obj->CreatorID;
		$we_doc->ModifierID = $we_obj->ModifierID;
		$we_doc->RestrictOwners = $we_obj->RestrictOwners;
		$we_doc->Owners = $we_obj->Owners;
		$we_doc->OwnersReadOnly = $we_obj->OwnersReadOnly;
		$we_doc->Category = $we_obj->Category;
		$we_doc->ObjectID = $we_obj->ObjectID;
		$we_doc->OF_ID = $we_obj->ID;
	}

	function initDoc(&$we_doc, $id = 0){
		$we_doc = new we_webEditionDocument();

		if($id){
			$we_doc->initByID($id);
		}
	}

	function we_includeEntity(&$we_doc, $tmpid){
		if($tmpid != "" && $tmpid != 0){
			$path = id_to_path($tmpid, TEMPLATES_TABLE);
		}

		$path = ($path ? TEMPLATES_PATH . $path : $we_doc->TemplatePath);

		if(file_exists($path)){
			include($path);
		} else{
			print STYLESHEET;
			print '<div class="defaultgray"><center>' . g_l('modules_newsletter', '[cannot_preview]') . '</center></div>';
		}
	}

	function getContent($pblk = 0, $gview = 0, $hm = 0, $salutation = "", $title = "", $firstname = "", $lastname = "", $customerid = 0){

		$content = "";
		$GLOBALS['we_doc'] = "";

		$GLOBALS["WE_MAIL"] = "###EMAIL###";
		$GLOBALS["WE_HTMLMAIL"] = $hm;
		$GLOBALS["WE_TITLE"] = $title;
		$GLOBALS["WE_SALUTATION"] = $salutation;
		$GLOBALS["WE_FIRSTNAME"] = $firstname;
		$GLOBALS["WE_LASTNAME"] = $lastname;
		$GLOBALS["WE_CUSTOMERID"] = $customerid;
		$patterns = array();

		if(isset($this->newsletter->blocks[$pblk])){
			$block = $this->newsletter->blocks[$pblk];
			$groups = makeArrayFromCSV($block->Groups);
			if(in_array($gview, $groups) || $gview == 0){
				switch($block->Type){
					case weNewsletterBlock::DOCUMENT:
						$path = "";
						if($block->Field != "" && $block->Field != 0){
							$path = TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', id_to_path($block->Field, TEMPLATES_TABLE));
						} else if($block->LinkID){
							$tid = f("SELECT TemplateID FROM " . FILE_TABLE . " WHERE ID='" . $block->LinkID . "';", "TemplateID", $this->db);
							$path = TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', id_to_path($tid, TEMPLATES_TABLE));
						}
						if($block->LinkID && $path)
							$content .= ($block->LinkID > 0) && weFileExists($block->LinkID) ? we_getDocumentByID($block->LinkID, $path) : 'No such File';
						break;
					case weNewsletterBlock::DOCUMENT_FIELD:
						if($block->LinkID){
							$we_doc = '';
							$this->initDoc($we_doc, $block->LinkID);
							$content .= $we_doc->getElement($block->Field);
						}
						break;
					case weNewsletterBlock::OBJECT:
						$path = "";
						if($block->Field != "" && $block->Field != 0){
							$path = TEMPLATES_PATH . preg_replace('/\.tmpl$/i', '.php', id_to_path($block->Field, TEMPLATES_TABLE));
						}
						if($block->LinkID && $path)
							$content = we_getObjectFileByID($block->LinkID, $path);

						break;
					case weNewsletterBlock::OBJECT_FIELD:
						if($block->LinkID){
							$this->initDocByObject($we_doc, $block->LinkID);
							$content .= $we_doc->getElement($block->Field);
						}
						break;
					case weNewsletterBlock::TEXT:
						if($hm){
							if($block->Html != ""){
								$content .= $block->Html;
							} else{
								$newhtml = "";
								$newhtml = str_replace("\r\n", "\n", $block->Source);
								$newhtml = str_replace("\r", "\n", $newhtml);
								$newhtml = str_replace("&", "&amp;", $newhtml);
								$newhtml = str_replace("<", "&lt;", $newhtml);
								$newhtml = str_replace(">", "&gt;", $newhtml);
								$newhtml = str_replace("\n", "<br>", $newhtml);
								$newhtml = str_replace("\t", "&nbsp;&nbsp;&nbsp;", $newhtml);
								$content .= $newhtml;
							}
						} else{
							if($block->Source != ""){
								$content .= $block->Source;
							} else{
								$newplain = "";
								$newplain = str_ireplace("<br>", "\n", $block->Html);
								$newplain = trim(strip_tags($newplain));
								$newplain = preg_replace("|&nbsp;(&nbsp;)+|i", "\t", $newplain);
								$newplain = str_ireplace('&nbsp;', ' ', $newplain);
								$newplain = str_ireplace("&lt;", "<", $newplain);
								$newplain = str_ireplace("&gt;", ">", $newplain);
								$newplain = str_ireplace("&quot;", '"', $newplain);
								$newplain = str_ireplace("&amp;", "&", $newplain);
								$content .= $newplain;
							}
						}
						break;
					case weNewsletterBlock::FILE:
						$content = weFile::load($_SERVER['DOCUMENT_ROOT'] . $block->Field);
						if(!$content)
							print g_l('modules_newsletter', '[cannot_open]') . ": " . $_SERVER['DOCUMENT_ROOT'] . $block->Field;
						break;
					case weNewsletterBlock::URL:
						if($block->Field){
							if(substr(trim($block->Field), 0, 4) != "http"){
								$block->Field = "http://" . $block->Field;
							}

							$url = parse_url($block->Field);
							$content .= getHTTP($url["host"], (isset($url["path"]) ? $url["path"] : ""), "", defined("HTTP_USERNAME") ? HTTP_USERNAME : "", defined("HTTP_PASSWORD") ? HTTP_PASSWORD : "");

							$trenner = "[\040|\n|\t|\r]*";
							$patterns[] = "/<(img" . $trenner . "[^>]+src" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\">\040? \\\]*)([^\"\'\040\\\\>]*)(" . $trenner . "[^>]*)>/sie";
							$patterns[] = "/<(link" . $trenner . "[^>]+href" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\">\040? \\\]*)([^\"\'\040\\\\>]*)(" . $trenner . "[^>]*)>/sie";
							$match = array();

							foreach($patterns as $pattern){
								if(preg_match_all($pattern, $content, $match)){
									$unique = array_unique($match[2]);
									foreach($unique as $name){
										$src = parse_url($name);

										if(!isset($src["host"])){

											if(isset($src["path"])){
												if(dirname($src["path"]))
													$path = dirname($src["path"]) . "/";
												else if(isset($url["path"]))
													$path = dirname($url["path"]) . "/";
												else
													$path = "";
											}
											$newname = $url["scheme"] . "://" . preg_replace("|/+|", "/", $url["host"] . "/" . $path . basename($name));
											$content = str_replace($name, $newname, $content);
										}
									}
								}
							}
						}
						break;
					case weNewsletterBlock::ATTACHMENT:
						$content .= "";
						break;
				}
			}



			$port = (isset($this->settings["use_port"]) && $this->settings["use_port"]) ? ':' . $this->settings["use_port"] : '';
			$protocol = (isset($this->settings["use_https_refer"]) && $this->settings["use_https_refer"] ? 'https://' : 'http://');

			if($hm){
				if($block->Type != weNewsletterBlock::URL){
					$spacer = '[\040|\n|\t|\r]*';
					parseInternalLinks($content, 0);

					$content = preg_replace('-(<[^>]+src' . $spacer . '=' . $spacer . '[\'"]?)(/)-i', '\\1' . $protocol . $_SERVER['SERVER_NAME'] . $port . '\\2', $content);
					$content = preg_replace('-(<[^>]+href' . $spacer . '=' . $spacer . '[\'"]?)(/)-i', '\\1' . $protocol . $_SERVER['SERVER_NAME'] . $port . '\\2', $content);
					$content = preg_replace('-(<[^>]+background' . $spacer . '=' . $spacer . '[\'"]?)(/)-i', '\\1' . $protocol . $_SERVER['SERVER_NAME'] . $port . '\\2', $content);
					$content = preg_replace('-(background' . $spacer . ':' . $spacer . '[^url]*url' . $spacer . '\\([\'"]?)(/)-i', '\\1' . $protocol . $_SERVER['SERVER_NAME'] . $port . '\\2', $content);
					$content = preg_replace('+(background-image' . $spacer . ':' . $spacer . '[^url]*url' . $spacer . '\\([\'"]?)(/)+i', '\\1' . $protocol . $_SERVER['SERVER_NAME'] . $port . '\\2', $content);
				}
			} else{
				$newplain = preg_replace('|<br */? *>|', "\n", $content);
				$newplain = preg_replace('|<title>.*</title>|i', "\n", $newplain);
				if($block->Type != weNewsletterBlock::TEXT){
					$newplain = strip_tags($newplain);
				}
				$newplain = preg_replace("|&nbsp;(&nbsp;)+|i", "\t", $newplain);
				$content = $newplain = str_ireplace(array('&nbsp;', '&lt;', '&gt;', '&quot;', '&amp;',), array(' ', '<', '>', '"', '&'), $newplain);
			}
		}
		return $content;
	}

	function getBlockContents(){
		$content = array();
		$keys = array_keys($this->newsletter->blocks);
		foreach($keys as $kblock){
			$blockid = $kblock + 1;

			$out["plain"]["defaultC"] = $this->getContent($blockid, 0, 0, "", "", "", "", "###CUSTOMERID###");
			$out["plain"]["femaleC"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["plain"]["maleC"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["plain"]["title_firstname_lastnameC"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["plain"]["title_lastnameC"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", "###CUSTOMERID###");
			$out["plain"]["firstname_lastnameC"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["plain"]["firstnameC"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", "###CUSTOMERID###");
			$out["plain"]["lastnameC"] = $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", "###CUSTOMERID###");

			$out["plain"]["default"] = $this->getContent($blockid, 0, 0, "", "", "", "", "");
			$out["plain"]["female"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["plain"]["male"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["plain"]["title_firstname_lastname"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["plain"]["title_lastname"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", "");
			$out["plain"]["firstname_lastname"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["plain"]["firstname"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", "");
			$out["plain"]["lastname"] = $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", "");

			$out["html"]["defaultC"] = $this->getContent($blockid, 0, 1, "", "", "", "", "###CUSTOMERID###");
			$out["html"]["femaleC"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["html"]["maleC"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["html"]["title_firstname_lastnameC"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "", "###LASTNAME###", "###CUSTOMERID###");
			$out["html"]["title_lastnameC"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["html"]["firstname_lastnameC"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$out["html"]["firstnameC"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "", "###CUSTOMERID###");
			$out["html"]["lastnameC"] = $this->getContent($blockid, 0, 1, "", "", "", "###LASTNAME###", "###CUSTOMERID###");

			$out["html"]["default"] = $this->getContent($blockid, 0, 1, "", "", "", "", "");
			$out["html"]["female"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["html"]["male"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["html"]["title_firstname_lastname"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "", "###LASTNAME###", "");
			$out["html"]["title_lastname"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["html"]["firstname_lastname"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "###LASTNAME###", "");
			$out["html"]["firstname"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "", "");
			$out["html"]["lastname"] = $this->getContent($blockid, 0, 1, "", "", "", "###LASTNAME###", "");

			$content[] = $out;
		}
		return $content;
	}

	function getGroupBlocks($group){
		$content = array();
		$count = count($this->newsletter->blocks);
		if($group == 0){
			for($i = 0; $i < $count; $i++)
				$content[] = $i;
		} else{
			foreach($this->newsletter->blocks as $kblock => $block){
				if(strpos($block->Groups, "," . $group . ",") !== false){
					$content[] = $kblock;
				}
			}
		}
		return $content;
	}

	function getGroupsForEmail($email){
		$ret = array();

		if(is_array($this->newsletter->groups)){
			$keys = array_keys($this->newsletter->groups);
			foreach($keys as $gk){
				$emails = $this->getEmails($gk + 1, 0, 1);

				if(in_array($email, $emails)){
					$ret[] = $gk + 1;
				}
			}
		}

		return $ret;
	}

	function getAttachments($group){
		$atts = array();
		$dbtmp = new DB_WE();
		if($group)
			$this->db->query("SELECT LinkID FROM " . NEWSLETTER_BLOCK_TABLE . " WHERE NewsletterID=" . $this->newsletter->ID . " AND Type=" . weNewsletterBlock::ATTACHMENT . " AND Groups LIKE '%," . $this->db->escape($group) . ",%'");
		else
			$this->db->query("SELECT LinkID FROM " . NEWSLETTER_BLOCK_TABLE . " WHERE NewsletterID=" . $this->newsletter->ID . " AND Type=" . weNewsletterBlock::ATTACHMENT . ";");

		while($this->db->next_record()) {

			if($this->db->f("LinkID")){
				$path = f("SELECT Path FROM " . FILE_TABLE . " WHERE ID=" . $this->db->f("LinkID"), "Path", $dbtmp);

				if($path){
					$atts[] = $_SERVER['DOCUMENT_ROOT'] . $path;
				}
			}
		}
		return $atts;
	}

	function sendTestMail($group, $hm){
		$plain = "";
		$content = "";
		$inlines = array();

		$ret = $this->cacheNewsletter($this->newsletter->ID, false);
		$blocks = $this->getGroupBlocks($group);
		foreach($blocks as $i){
			if($hm){
				$block = $this->getFromCache($ret["blockcache"] . "_h_" . $i);
				$inlines = array_merge($inlines, $block["inlines"]);
				$content.=$block["default"];
				$block = $this->getFromCache($ret["blockcache"] . "_p_" . $i);
				$plain.=$block["default"];
			} else{
				$block = $this->getFromCache($ret["blockcache"] . "_p_" . $i);
				$content.=$block["default"];
				$plain.=$block["default"];
			}
		}

		$atts = array();

		$atts = $this->getAttachments($group);
		//$_clean = $this->getCleanMail($this->newsletter->Reply);
		$phpmail = new we_util_Mailer($this->newsletter->Test, $this->newsletter->Subject, $this->newsletter->Sender, $this->newsletter->Reply, $this->newsletter->isEmbedImages);
		if(!$this->settings["use_base_href"]){
			$phpmail->setIsUseBaseHref($this->settings["use_base_href"]);
		}
		$phpmail->setCharSet($this->newsletter->Charset != "" ? $this->newsletter->Charset : $GLOBALS['WE_BACKENDCHARSET']);
		if($hm){
			$phpmail->addHTMLPart($content);
		}
		$phpmail->addTextPart(trim($plain));
		foreach($atts as $att){
			$phpmail->doaddAttachment($att);
		}
		$phpmail->buildMessage();
		$phpmail->Send();

		$cc = 0;
		while(true) {
			if(file_exists(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_p_" . $cc))
				weFile::delete(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_p_" . $cc);
			else
				break;

			//if(file_exists(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"]."_h_".$cc)) weFile::delete(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"]."_h_".$cc);
			if(file_exists(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_h_" . $cc)){
				$_buffer = @unserialize(weFile::load(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_h_" . $cc));
				if(is_array($_buffer) && isset($_buffer['inlines'])){
					foreach($_buffer['inlines'] as $_fn){
						if(file_exists($_fn)){
							weFile::delete($_fn);
						}
					}
				}
				weFile::delete(WE_NEWSLETTER_CACHE_DIR . $ret["blockcache"] . "_h_" . $cc);
			}
			else
				break;
			$cc++;
		}
		foreach($inlines as $ins){
			weFile::delete($ins);
		}
	}

	function getFilterSQL($filter){
		$filterSQL = $filter["fieldname"];
		if($filter["fieldname"] == 'MemberSince' || $filter["fieldname"] == 'LastLogin' || $filter["fieldname"] == 'LastAccess'){
			if(stristr($filter['fieldvalue'], '.')){
				$date = explode(".", $filter['fieldvalue']);
				$day = $date[0];
				$month = $date[1];
				$year = $date[2];
				$hour = $filter['hours'];
				$minute = $filter['minutes'];
				$filter['fieldvalue'] = mktime($hour, $minute, 0, $month, $day, $year);
			}
		}

		switch($filter["operator"]){
			case 0:
				$filterSQL .= " = '" . $filter["fieldvalue"] . "'";
				break;

			case 1:
				$filterSQL .= " <> '" . $filter["fieldvalue"] . "'";
				break;

			case 2:
				$filterSQL .= " < '" . $filter["fieldvalue"] . "'";
				break;

			case 3:
				$filterSQL .= " <= '" . $filter["fieldvalue"] . "'";
				break;

			case 4:
				$filterSQL .= " > '" . $filter["fieldvalue"] . "'";
				break;

			case 5:
				$filterSQL .= " >= '" . $filter["fieldvalue"] . "'";
				break;

			case 6:
				$filterSQL .= " LIKE '" . $filter["fieldvalue"] . "'";
				break;
			case 7:
				$filterSQL .= " LIKE '%" . $filter["fieldvalue"] . "%'";
				break;
			case 8:
				$filterSQL .= " LIKE '" . $filter["fieldvalue"] . "%'";
				break;
			case 9:
				$filterSQL .= " LIKE '%" . $filter["fieldvalue"] . "'";
				break;
		}
		return $filterSQL;
	}

	function getEmails($group, $select = 0, $emails_only = 0){

		@set_time_limit(0);
		@ini_set("memory_limit", "128M");

		$extern = ($select == 0 || $select == 3) ? weNewsletterBase::getEmailsFromExtern($this->newsletter->groups[$group - 1]->Extern, $emails_only, $group, $this->getGroupBlocks($group)) : array();

		if($select == 3){
			return $extern;
		}

		$list = ($select == 0 || $select == 2) ? weNewsletterBase::getEmailsFromList($this->newsletter->groups[$group - 1]->Emails, $emails_only, $group, $this->getGroupBlocks($group)) : array();

		if($select == 2){
			return $list;
		}

		$customer_mail = array();
		$customers = array();

		if(defined("CUSTOMER_TABLE")){

			$filterarr = array();
			$filtersql = "";

			if(is_array($this->newsletter->groups[$group - 1]->aFilter)){

				foreach($this->newsletter->groups[$group - 1]->aFilter as $k => $filter){
					$filterarr[] = ($k != 0 ? (" " . $filter["logic"] . " ") : " ") . $this->getFilterSQL($filter);
				}
			}

			$filtersql = implode(" ", $filterarr);

			if($this->newsletter->groups[$group - 1]->SendAll){
				$customers = 'SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE ' . ($filtersql !== '' ? $filtersql : '1');
			} else{
				$customers = implode(',', array_map('intval', explode(',', $this->newsletter->groups[$group - 1]->Customers)));
			}

			$_default_html = f('SELECT pref_value FROM ' . NEWSLETTER_PREFS_TABLE . ' WHERE pref_name="default_htmlmail";', 'pref_value', $this->db);
			$select = $this->settings['customer_email_field'] .
				($emails_only ? '' :
					',' . $this->settings['customer_html_field'] . ',' .
					$this->settings['customer_salutation_field'] . ',' .
					$this->settings['customer_title_field'] . ',' .
					$this->settings['customer_firstname_field'] . ',' .
					$this->settings['customer_lastname_field']
				);
			$this->db->query('SELECT ID,' . $select . ' FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $customers . ')' . ($filtersql != '' ? ' AND (' . $filtersql . ')' : ''));
			while($this->db->next_record()) {
				if($this->db->f($this->settings["customer_email_field"])){
					$email = trim($this->db->f($this->settings["customer_email_field"]));
					if($emails_only){
						$customer_mail[] = $email;
					} else{
						$htmlmail = (trim($this->db->f($this->settings["customer_html_field"])) != '') ? trim($this->db->f($this->settings["customer_html_field"])) : $_default_html;
						$salutation = $this->db->f($this->settings["customer_salutation_field"]);
						$title = $this->db->f($this->settings["customer_title_field"]);
						$firstname = $this->db->f($this->settings["customer_firstname_field"]);
						$lastname = $this->db->f($this->settings["customer_lastname_field"]);

						// damd: Parmeter $customer (Kunden ID in der Kundenverwaltung) und Flag dass es sich um Daten aus der Kundenverwaltung handelt angehängt
						$customer = $this->db->f('ID');
						$customer_mail[] = array($email, $htmlmail, $salutation, $title, $firstname, $lastname, $group, $this->getGroupBlocks($group), $customer, 'customer');
					}
				}
			}
			if($select == 1){
				return $customer_mail;
			}
		}
		return array_merge($customer_mail, $list, $extern);
	}

	function getEmailsNum(){
		$out = 0;
		$count = count($this->newsletter->groups);
		for($i = 0; $i < $count; $i++){
			$out+=count($this->getEmails($i + 1, 0, 1));
		}
		return $out;
	}

	/**
	 * Static function - Settings
	 */
	static function getSettings(){
		$db = new DB_WE();
		$ret = array();
		$_domainName = str_replace("www.", "", $_SERVER['SERVER_NAME']);
		$ret = array(
			'black_list' => '',
			'customer_email_field' => 'Kontakt_Email',
			'customer_firstname_field' => 'Forename',
			'customer_html_field' => 'Newsletter_HTMLNewsletter',
			'customer_lastname_field' => 'Surname',
			'customer_salutation_field' => 'Anrede_Anrede',
			'customer_title_field' => 'Anrede_Titel',
			'default_htmlmail' => '0',
			'isEmbedImages' => '0',
			'default_reply' => 'replay@' . $_domainName,
			'default_sender' => 'mailer@' . $_domainName,
			weNewsletter::FEMALE_SALUTATION_FIELD => g_l('modules_newsletter', '[default][female]'),
			'global_mailing_list' => '',
			'log_sending' => '1',
			weNewsletter::MALE_SALUTATION_FIELD => g_l('modules_newsletter', '[default][male]'),
			'reject_malformed' => '1',
			'reject_not_verified' => '1',
			'send_step' => '20',
			'send_wait' => '0',
			'test_account' => 'test@' . $_domainName,
			'title_or_salutation' => '0',
			'use_port' => '0',
			'use_https_refer' => '0',
			'use_base_href' => '1'
		);

		$db->query('SELECT * FROM ' . NEWSLETTER_PREFS_TABLE);
		while($db->next_record()) {
			$ret[$db->f("pref_name")] = $db->f("pref_value");
		}
		//make sure blacklist is correct
		$tmp = explode(',', $ret['black_list']);
		if(is_array($tmp)){
			foreach($tmp as &$t){
				$t = trim($t);
			}
		}
		$ret['black_list'] = implode(',', $tmp);

		return $ret;
	}

	function putSetting($name, $value){
		$db = new DB_WE();
		$name = $db->escape($name);
		$value = $db->escape($value);
		$db->query("SELECT pref_value FROM " . NEWSLETTER_PREFS_TABLE . " WHERE pref_name='$name';");
		if(!$db->next_record())
			$db->query("INSERT INTO " . NEWSLETTER_PREFS_TABLE . "(pref_name,pref_value) VALUES('$name','$value');");
	}

	function saveSettings(){
		$db = new DB_WE();
		// WORKARROUND BUG NR 7450
		foreach($this->settings as $key => $value){
			if($key != 'black_list'){
				$db->query('REPLACE INTO ' . NEWSLETTER_PREFS_TABLE . ' SET ' . we_database_base::arraySetter(array('pref_name' => $key, 'pref_value' => $value)));
			}
		}
	}

	function saveSetting($name, $value){
		$db = new DB_WE();
		$db->query('REPLACE INTO ' . NEWSLETTER_PREFS_TABLE . ' SET ' . we_database_base::arraySetter(array('pref_name' => $name, 'pref_value' => $value)));
	}

	function getBlackList(){
		return array();
	}

	function isBlack($email){
		static $black = 0;
		if(!$black){
			//remove whitespaces
			$black = explode(',', strtolower($this->settings['black_list']));
			foreach($black as &$b){
				$b = trim($b, " \t\n\r\n"); //intentionally duplicate \n!
			}
		}
		return in_array(trim(strtolower($email), " \t\n\r\n"), $black);
	}

	/**
	 * Write newsletter and mailing lists temp files
	 *
	 * @param Integer $nid
	 * @param Boolean $cachemails
	 * @return Array
	 */
	function cacheNewsletter($nid = 0, $cachemails = true){

		$ret = array();
		if($nid)
			$this->newsletter = new weNewsletter($nid);

		if($cachemails){
			// BEGIN cache emails groups
			$emailcache = weFile::getUniqueId();
			$groupcount = count($this->newsletter->groups) + 1;

			$ret["emailcache"] = $emailcache;
			$buffer = array();

			for($groupid = 1; $groupid < $groupcount; $groupid++){
				$tmp = $this->getEmails($groupid);
				$tcount = count($tmp);
				for($t = 0; $t < $tcount; $t++){
					if(isset($tmp[$t][0]) && isset($tmp[$t][7]) && count($tmp[$t][7])){
						$index = strtolower($tmp[$t][0]);
						if(isset($buffer[$index])){
							if(!in_array($tmp[$t][6], explode(",", $buffer[$index][6])))
								$buffer[$index][6].="," . $tmp[$t][6];
							$buffer[$index][7] = array_merge($buffer[$index][7], $tmp[$t][7]);
						}
						else{
							$buffer[$index] = $tmp[$t];
						}
					}
				}
			}


			$cc = 0;
			foreach($buffer as $k => $one){
				$buffer[$cc] = $one;
				unset($buffer[$k]);
				$cc++;
			}

			$ret["ecount"] = count($buffer);

			$groups = 0;
			$tmp = array();
			$go = true;
			$offset = 0;


			while($go) {
				$tmp = array_slice($buffer, $offset, $this->settings["send_step"]);
				if(count($tmp)){
					$offset+=$this->settings["send_step"];
					$groups++;
					$this->saveToCache(serialize($tmp), $emailcache . "_$groups");
				}else
					$go = false;
			}

			$ret["gcount"] = $groups + 1;
		}

		// END cache emails groups
		// BEGIN cache newlsetter blocks
		$blockcache = weFile::getUniqueId();
		$blockcount = count($this->newsletter->blocks);

		$ret["blockcache"] = $blockcache;

		for($blockid = 0; $blockid < $blockcount; $blockid++){

			$buffer = array();
			$buffer["defaultC"] = $this->getContent($blockid, 0, 0, "", "", "", "", "###CUSTOMERID###");
			$buffer["femaleC"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["maleC"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["title_firstname_lastnameC"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["title_lastnameC"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["firstname_lastnameC"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["firstnameC"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", "###CUSTOMERID###");
			$buffer["lastnameC"] = $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", "###CUSTOMERID###");

			$buffer["default"] = $this->getContent($blockid, 0, 0, "", "", "", "", "");
			$buffer["female"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["male"] = $this->getContent($blockid, 0, 0, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["title_firstname_lastname"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["title_lastname"] = $this->getContent($blockid, 0, 0, "", "###TITLE###", "", "###LASTNAME###", "");
			$buffer["firstname_lastname"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["firstname"] = $this->getContent($blockid, 0, 0, "", "", "###FIRSTNAME###", "", "");
			$buffer["lastname"] = $this->getContent($blockid, 0, 0, "", "", "", "###LASTNAME###", "");

			$this->saveToCache(serialize($buffer), $blockcache . "_p_" . $blockid);

			$buffer = array();
			$buffer["defaultC"] = $this->getContent($blockid, 0, 1, "", "", "", "", "###CUSTOMERID###");
			$buffer["femaleC"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["maleC"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["title_firstname_lastnameC"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["title_lastnameC"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["firstname_lastnameC"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "###LASTNAME###", "###CUSTOMERID###");
			$buffer["firstnameC"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "", "###CUSTOMERID###");
			$buffer["lastnameC"] = $this->getContent($blockid, 0, 1, "", "", "", "###LASTNAME###", "###CUSTOMERID###");

			$buffer["default"] = $this->getContent($blockid, 0, 1, "", "", "", "", "");
			$buffer["female"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::FEMALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["male"] = $this->getContent($blockid, 0, 1, $this->settings[weNewsletter::MALE_SALUTATION_FIELD], "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["title_firstname_lastname"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["title_lastname"] = $this->getContent($blockid, 0, 1, "", "###TITLE###", "", "###LASTNAME###", "");
			$buffer["firstname_lastname"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "###LASTNAME###", "");
			$buffer["firstname"] = $this->getContent($blockid, 0, 1, "", "", "###FIRSTNAME###", "", "");
			$buffer["lastname"] = $this->getContent($blockid, 0, 1, "", "", "", "###LASTNAME###", "");

			if($this->newsletter->blocks[$blockid]->Pack)
				$buffer["inlines"] = $this->cacheInlines($buffer);
			else
				$buffer["inlines"] = array();

			$this->saveToCache(serialize($buffer), $blockcache . "_h_" . $blockid);
		}
		// END cache newlsetter blocks

		return $ret;
	}

	function cacheInlines(&$buffer){

		$trenner = "[\040|\n|\t|\r]*";
		$patterns[] = "/<(img" . $trenner . "[^>]+src" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\">\040? \\\]*)([^\"\'\040\\\\>]*)(" . $trenner . "[^>]*)>/sie";
		$patterns[] = "/<(body" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\">\040? \\\]*)([^\"\'\040\\\\>]*)(" . $trenner . "[^>]*)>/sie";
		$patterns[] = "/<(table" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\">\040? \\\]*)([^\"\'\040\\\\>]*)(" . $trenner . "[^>]*)>/sie";
		$patterns[] = "/<(td" . $trenner . "[^>]+background" . $trenner . "[\=\"|\=\'|\=\\\\|\=]*" . $trenner . ")([^\'\">\040? \\\]*)([^\"\'\040\\\\>]*)(" . $trenner . "[^>]*)>/sie";
		$patterns[] = "/background" . $trenner . ":" . $trenner . "([^url]*url" . $trenner . "\([\"|\'|\\\\])?(.[^\)|^\"|^\'|^\\\\]+)([\"|\'|\\\\])?/sie";
		$patterns[] = "/background-image" . $trenner . ":" . $trenner . "([^url]*url" . $trenner . "\([\"|\'|\\\\])?(.[^\)|^\"|^\'|^\\\\]+)([\"|\'|\\\\])?/sie";

		$match = array();
		$inlines = array();

		foreach($buffer as $v){
			foreach($patterns as $pattern){
				if(preg_match_all($pattern, $v, $match)){
					foreach($match[2] as $name){
						if(!in_array($name, array_keys($inlines))){
							$newname = WE_NEWSLETTER_CACHE_DIR . weFile::getUniqueID();
							$inlines[$name] = $newname;

							$fcontent = weFile::load($name);
							$fcontent = chunk_split(base64_encode($fcontent), 76, "\n");
							weFile::save($newname, $fcontent);
						}
					}
				}
			}
		}
		return $inlines;
	}

	function getFromCache($cache){
		$cache = WE_NEWSLETTER_CACHE_DIR . basename($cache);
		$buffer = weFile::load($cache);
		if($buffer){
			return unserialize($buffer);
		}
		else
			return array();
	}

	function getCleanMail($mail){
		$_match = array();
		$_pattern = '|[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,6}|i';
		if(preg_match($_pattern, $mail, $_match)){
			return ($_match[0]);
		}
		return '';
	}

	function saveToCache($content, $filename){
		if(!is_dir(WE_NEWSLETTER_CACHE_DIR)){
			we_util_File::createLocalFolder(WE_NEWSLETTER_CACHE_DIR);
		}

		$filename = WE_NEWSLETTER_CACHE_DIR . basename($filename);
		return weFile::save($filename, $content);
	}

}

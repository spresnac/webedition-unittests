<?php

/**
 * webEdition CMS
 *
 * $Rev: 5265 $
 * $Author: mokraemer $
 * $Date: 2012-11-30 14:56:59 +0100 (Fri, 30 Nov 2012) $
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

we_html_tools::protect();

$js = we_html_element::jsElement('
	var code;
	var to;

	var isLodaed = false;

	function setIsLoaded(flag) {
		self.isLoaded = flag;
	}

	function editSettings() {
		if (self.isLoaded) {
			document.WePlugin.editSettings();
		}
	}

	function editSource(filename,ct,charset){

		var _EditorFrame = top.weEditorFrameController.getActiveEditorFrame();

		var source = "###EDITORPLUGIN:EMPTYSTRING###";
		if(_EditorFrame.getContentEditor().getSource){
			source = _EditorFrame.getContentEditor().getSource();
			document.we_form.acceptCharset = _EditorFrame.getContentEditor().getCharset();
		}

		document.we_form.elements[\'we_cmd[0]\'].value="editSource";
		document.we_form.elements[\'we_cmd[1]\'].value=filename;
		document.we_form.elements[\'we_cmd[2]\'].value=_EditorFrame.getEditorTransaction();
		document.we_form.elements[\'we_cmd[3]\'].value=ct;
		document.we_form.elements[\'we_cmd[4]\'].value=source;

		document.we_form.submit();

	}

	function editFile(){
		var _EditorFrame = top.weEditorFrameController.getActiveEditorFrame();
		document.we_form.elements[\'we_cmd[0]\'].value="editFile";
		document.we_form.elements[\'we_cmd[1]\'].value=_EditorFrame.getEditorTransaction();
		document.we_form.submit();
	}

	function setSource(trans){

		var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction(trans);
		if(_EditorFrame) {

			_EditorFrame.setEditorIsHot(true);

			var source =  (self.isLoaded) ? document.WePlugin.getSource(trans) : "";

			if(_EditorFrame && _EditorFrame.getContentEditor().setSource){
				_EditorFrame.getContentEditor().setSource(source);
			} else{
				document.we_form.elements[\'we_cmd[0]\'].value="setSource";
				document.we_form.elements[\'we_cmd[1]\'].value=trans;
				document.we_form.elements[\'we_cmd[2]\'].value=source;

				document.we_form.submit();
			}
		}
	}

	function setFile(source,trans){
		document.we_form.elements[\'we_cmd[0]\'].value="setFile";
		document.we_form.elements[\'we_cmd[1]\'].value=trans;
		document.we_form.elements[\'we_cmd[2]\'].value=source;
		document.we_form.submit();
	}



	function reloadContentFrame(trans){
		document.we_form.elements[\'we_cmd[0]\'].value="reloadContentFrame";
		document.we_form.elements[\'we_cmd[1]\'].value=trans;
		document.we_form.submit();
	}


	function remove(transaction) {
		if (self.isLoaded && (typeof document.WePlugin.removeDocument == "function")) {
			document.WePlugin.removeDocument(transaction);
		}else{
			self.isLoaded =false;
		}
	}

	function isInEditor(transaction) {
		if (self.isLoaded && transaction!=null && (typeof document.WePlugin.inEditor == "function")) {
			return document.WePlugin.inEditor(transaction);
		}
		return false;
	}

	function getDocCount() {
		if (self.isLoaded) {
			return document.WePlugin.getDocCount();
		}
		return 1;
	}

	function pingPlugin() {
		if(document.WePlugin && self.isLoaded) {

			c++;
			//document.getElementById("debug").innerHTML += c + "<br>";

			if(document.WePlugin.hasMessages) {
				if(document.WePlugin.hasMessages()) {
					var messages = document.WePlugin.getMessages();
					eval(""+messages);
					//document.getElementById("debug").innerHTML += c + "<br>" + messages+"<br>";

				}
			}

		}

		to = window.setTimeout("pingPlugin()",1000);

	}


	var c = 0;

	');

$applet = we_html_element::htmlApplet(array(
		"name" => "WePlugin",
		"code" => "EPlugin",
		"archive" => "weplugin.jar",
		"codebase" => getServerUrl() . WEBEDITION_DIR . 'editors/content/eplugin/',
		"width" => "10",
		"height" => "10",
		' width' => 100, //keep html attributes
		' height' => 100,
		), we_html_element::htmlParam(array("name" => "param_list", "value" => "lan_main_dialog_title,lan_alert_noeditor_title,lan_alert_noeditor_text,lan_select_text,lan_select_button,lan_start_button,lan_close_button,lan_clear_button,lan_list_label,lan_showall_label,lan_edit_button,lan_default_for,lan_editor_name,lan_path,lan_args,lan_contenttypes,lan_defaultfor_label,lan_del_button,lan_save_button,lan_autostart_label,lan_settings_dialog_title,lan_alert_nodefeditor_text,lan_del_question,lan_clear_question,lan_encoding,lan_add_button")) .
		we_html_element::htmlParam(array("name" => "host", "value" => getServerUrl())) .
		we_html_element::htmlParam(array("name" => "cmdentry", "value" => getServerUrl() . WEBEDITION_DIR . 'editors/content/eplugin/weplugin_cmd.php')) .
		we_html_element::htmlParam(array("name" => "lan_main_dialog_title", "value" => g_l('eplugin', "[lan_main_dialog_title]"))) .
		we_html_element::htmlParam(array("name" => "lan_settings_dialog_title", "value" => g_l('eplugin', "[lan_settings_dialog_title]"))) .
		we_html_element::htmlParam(array("name" => "lan_alert_noeditor_title", "value" => g_l('eplugin', "[lan_alert_noeditor_title]"))) .
		we_html_element::htmlParam(array("name" => "lan_alert_noeditor_text", "value" => g_l('eplugin', "[lan_alert_noeditor_text]"))) .
		we_html_element::htmlParam(array("name" => "lan_select_text", "value" => g_l('eplugin', "[lan_select_text]"))) .
		we_html_element::htmlParam(array("name" => "lan_select_button", "value" => g_l('eplugin', "[lan_select_button]"))) .
		we_html_element::htmlParam(array("name" => "lan_start_button", "value" => g_l('eplugin', "[lan_start_button]"))) .
		we_html_element::htmlParam(array("name" => "lan_close_button", "value" => g_l('eplugin', "[lan_close_button]"))) .
		we_html_element::htmlParam(array("name" => "lan_clear_button", "value" => g_l('eplugin', "[lan_clear_button]"))) .
		we_html_element::htmlParam(array("name" => "lan_list_label", "value" => g_l('eplugin', "[lan_list_label]"))) .
		we_html_element::htmlParam(array("name" => "lan_showall_label", "value" => g_l('eplugin', "[lan_showall_label]"))) .
		we_html_element::htmlParam(array("name" => "lan_edit_button", "value" => g_l('eplugin', "[lan_edit_button]"))) .
		we_html_element::htmlParam(array("name" => "lan_default_for", "value" => g_l('eplugin', "[lan_default_for]"))) .
		we_html_element::htmlParam(array("name" => "lan_editor_name", "value" => g_l('eplugin', "[lan_editor_name]"))) .
		we_html_element::htmlParam(array("name" => "lan_path", "value" => g_l('eplugin', "[lan_path]"))) .
		we_html_element::htmlParam(array("name" => "lan_args", "value" => g_l('eplugin', "[lan_args]"))) .
		we_html_element::htmlParam(array("name" => "lan_contenttypes", "value" => g_l('eplugin', "[lan_contenttypes]"))) .
		we_html_element::htmlParam(array("name" => "lan_defaultfor_label", "value" => g_l('eplugin', "[lan_defaultfor_label]"))) .
		we_html_element::htmlParam(array("name" => "lan_del_button", "value" => g_l('eplugin', "[lan_del_button]"))) .
		we_html_element::htmlParam(array("name" => "lan_save_button", "value" => g_l('eplugin', "[lan_save_button]"))) .
		we_html_element::htmlParam(array("name" => "lan_editor_prop", "value" => g_l('eplugin', "[lan_editor_prop]"))) .
		we_html_element::htmlParam(array("name" => "lan_autostart_label", "value" => g_l('eplugin', "[lan_autostart_label]"))) .
		we_html_element::htmlParam(array("name" => "lan_alert_nodefeditor_text", "value" => g_l('eplugin', "[lan_alert_nodefeditor_text]"))) .
		we_html_element::htmlParam(array("name" => "lan_del_question", "value" => g_l('eplugin', "[lan_del_question]"))) .
		we_html_element::htmlParam(array("name" => "lan_clear_question", "value" => g_l('eplugin', "[lan_clear_question]"))) .
		we_html_element::htmlParam(array("name" => "lan_encoding", "value" => g_l('eplugin', "[lan_encoding]"))) .
		we_html_element::htmlParam(array("name" => "lan_add_button", "value" => g_l('eplugin', "[lan_add_button]")))
);

$charset = '';

//FIXME: charset
print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_element::htmlMeta(array("http-equiv" => "content-type", "content" => "text/html; charset=" . $GLOBALS['WE_BACKENDCHARSET'])) .
			we_html_element::htmlTitle('start wePlugin') .
			$js) .
		we_html_element::htmlBody(array("bgcolor" => "white", "onload" => "to=window.setTimeout('pingPlugin()',5000);"), we_html_element::htmlDiv(array("id" => "debug"), "") .
			we_html_element::htmlHidden(array("name" => "hm", "value" => "0")) .
			$applet .
			we_html_element::htmlForm(array("name" => "we_form", "target" => "load", "action" => WEBEDITION_DIR . "editors/content/eplugin/weplugin_cmd.php", "method" => "post", "accept-charset" => $charset), we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "")) .
				we_html_element::htmlHidden(array("name" => "we_cmd[1]", "value" => "")) .
				we_html_element::htmlHidden(array("name" => "we_cmd[2]", "value" => "")) .
				we_html_element::htmlHidden(array("name" => "we_cmd[3]", "value" => "")) .
				we_html_element::htmlHidden(array("name" => "we_cmd[4]", "value" => "")) . "\n"
				//we_html_element::htmlInput(array("name"=>"wePluginUpload","type"=>"file","value"=>""))."\n"
			)
			//.we_html_element::htmlInput(array("type"=>"button","onclick"=>"setFile('file');"))
		)
	);

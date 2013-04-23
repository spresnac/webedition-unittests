<?php

/**
 * webEdition CMS
 *
 * $Rev: 5889 $
 * $Author: mokraemer $
 * $Date: 2013-02-25 21:53:09 +0100 (Mon, 25 Feb 2013) $
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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weHyperlinkDialog extends weDialog{

	var $ClassName = __CLASS__;
	var $changeableArgs = array(
		'type', 'extHref', 'fileID', 'href', 'fileHref', 'objID', 'objHref', 'mailHref', 'target', 'class',
		'param', 'anchor', 'lang', 'hreflang', 'title', 'accesskey', 'tabindex', 'rel', 'rev'
	);

	function __construct($href = '', $target = '', $fileID = 0, $objID = 0){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', '[edit_hyperlink]');
	}

	function getDialogButtons(){
		if($this->pageNr == $this->numPages && $this->JsOnly == false){
			$okBut = ($this->getBackBut() != "") ? we_button::create_button_table(array($this->getBackBut(), we_button::create_button("ok", "javascript:weCheckAcFields()"))) : we_button::create_button("ok", "javascript:weCheckAcFields()");
		} else if($this->pageNr < $this->numPages){
			$okBut = (($this->getBackBut() != "") && ($this->getNextBut()) != "") ? we_button::create_button_table(array($this->getBackBut(), $this->getNextBut())) : (($this->getBackBut() == "") ? $this->getNextBut() : $this->getBackBut());
		} else{
			$okBut = (($this->getBackBut() != "") && ($this->getOkBut()) != "") ? we_button::create_button_table(array($this->getBackBut(), $this->getOkBut())) : (($this->getBackBut() == "") ? $this->getOkBut() : $this->getBackBut());
		}

		return we_button::position_yes_no_cancel($okBut, "", we_button::create_button("cancel", "javascript:top.close();"));
	}

	function initByHref($href, $target = "", $class = "", $param = "", $anchor = "", $lang = "", $hreflang = "", $title = "", $accesskey = "", $tabindex = "", $rel = "", $rev = ""){
		if($href){
			$this->args["href"] = $href;
			list($type, $ref) = explode(':', $this->args["href"]);

			// Object Links and internal links are not possible when outside webEdition
			// for exmaple in the wysiwyg (Mantis Bug #138)
			if(isset($this->args["outsideWE"]) && $this->args["outsideWE"] == 1 && (
				$type == "object" || $type == "document:"
				)
			){
				$this->args["href"] = $type = $ref = '';
			}


			switch($type){
				case 'object':
					$this->args['type'] = 'obj';
					$this->args['extHref'] = '';
					$this->args['fileID'] = '';
					$this->args['fileHref'] = '';
					$this->args['mailHref'] = '';
					$this->args['objID'] = $ref;
					$this->args['objHref'] = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->args['objID']), 'Path', $this->db);
					break;
				case 'document':
					$this->args['type'] = 'int';
					$this->args['extHref'] = '';
					$this->args['fileID'] = $ref;
					$this->args['fileHref'] = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->args['fileID']), 'Path', $this->db);
					$this->args['mailHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
					break;
				case 'mailto':
					$this->args['type'] = 'mail';
					$this->args['mailHref'] = preg_replace('|^([^\?#]+).*$|', '\1', $ref);
					$this->args['extHref'] = '';
					$this->args['fileID'] = '';
					$this->args['fileHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
					break;
				default:
					$this->args['type'] = 'ext';
					$this->args['extHref'] = preg_replace('|^([^\?#]+).*$|', '\1', preg_replace('|^' . WEBEDITION_DIR . '|', '', preg_replace('|^' . WEBEDITION_DIR . 'we_cmd.php[^"\'#]+(#.*)$|', '\1', $this->args["href"])));
					$this->args['fileID'] = '';
					$this->args['fileHref'] = '';
					$this->args['mailHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
			}
		}
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['param'] = str_replace('&amp;', '&', $param);
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
	}

	function initByFileID($fileID, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($fileID){
			$this->args['href'] = 'document:' . $fileID;
			$this->args['type'] = 'int';
			$this->args['extHref'] = '';
			$this->args['fileID'] = $fileID;
			$this->args['fileHref'] = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->args['fileID']), 'Path', $this->db);
			$this->args['objID'] = '';
			$this->args['mailHref'] = '';
			$this->args['objHref'] = '';
		}
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['param'] = $param;
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
	}

	function initByObjectID($objID, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($objID){
			$this->args['href'] = 'object:' . $objID;
			$this->args['type'] = 'obj';
			$this->args['extHref'] = '';
			$this->args['fileID'] = '';
			$this->args['fileHref'] = '';
			$this->args['mailHref'] = '';
			$this->args['objID'] = $objID;
			$this->args['objHref'] = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->args['objID']), 'Path', $this->db);
		}
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['param'] = $param;
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
	}

	function initByMailHref($mailHref, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($mailHref){
			$this->args['href'] = 'mailto:' . $mailHref;
			$this->args['type'] = 'mail';
			$this->args['extHref'] = '';
			$this->args['fileID'] = '';
			$this->args['fileHref'] = '';
			$this->args['mailHref'] = $mailHref;
			$this->args['objID'] = '';
			$this->args['objHref'] = '';
		}
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['param'] = $param;
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
	}

	function glue_url($parsed){
		if(!is_array($parsed)){
			return false;
		}
		return ($parsed['scheme'] ? $parsed['scheme'] . ':' . ((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '') .
			($parsed['user'] ? $parsed['user'] . ($parsed['pass'] ? ':' . $parsed['pass'] : '') . '@' : '') .
			($parsed['host'] ? $parsed['host'] : '') .
			($parsed['port'] ? ':' . $parsed['port'] : '') .
			($parsed['path'] ? $parsed['path'] : '') .
			($parsed['query'] ? '?' . $parsed['query'] : '') .
			($parsed['fragment'] ? '#' . $parsed['fragment'] : '');
	}

	function initByHttp(){
		parent::initByHttp();
		$href = $this->getHttpVar('href');
		$target = $this->getHttpVar('target');
		$param = $this->getHttpVar('param');
		$anchor = $this->getHttpVar('anchor');
		$lang = $this->getHttpVar('lang');
		$hreflang = $this->getHttpVar('hreflang');
		$title = $this->getHttpVar('title');
		$accesskey = $this->getHttpVar('accesskey');
		$tabindex = $this->getHttpVar('tabindex');
		$rel = $this->getHttpVar('rel');
		$rev = $this->getHttpVar('rev');

		if($href && (strpos($href, "?") !== false || strpos($href, "#") !== false)){
			$urlparts = parse_url($href);

			if((!$param) && isset($urlparts["query"]) && $urlparts["query"]){
				$param = $urlparts["query"];
			}
			if((!$anchor) && isset($urlparts["fragment"]) && $urlparts["fragment"]){
				$anchor = $urlparts["fragment"];
			}
		}

		$class = $this->getHttpVar("class");
		$type = $this->getHttpVar("type");
		if($href){
			$this->initByHref($href, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
		} else if($type){
			$fileID = $this->getHttpVar("fileID", 0);
			$objID = $this->getHttpVar("objID", 0);
			switch($type){
				case "ext":
					$extHref = $this->getHttpVar("extHref", "#");
					$this->initByHref($extHref, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
				case "int":
					$this->initByFileID($fileID, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
				case "obj":
					$this->initByObjectID($objID, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
				case "mail":
					$mailhref = $this->getHttpVar("mailHref");
					$this->initByMailHref($mailhref, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
			}
		} else{
			$this->defaultInit();
		}
	}

	function defaultInit(){
		$this->args = array_merge($this->args, array(
			'href' => 'document:',
			'type' => 'int',
			'extHref' => '',
			'fileID' => '',
			'fileHref' => '',
			'objID' => '',
			'objHref' => '',
			'mailHref' => '',
			'target' => '',
			'class' => '',
			'param' => '',
			'anchor' => '',
			'lang' => '',
			'hreflang' => '',
			'title' => '',
			'accesskey' => '',
			'tabindex' => '',
			'rel' => '',
			'rev' => '',
		));
	}

	function getDialogContentHTML(){
		// Initialize we_button class
		$yuiSuggest = & weSuggest::getInstance();

		$extHref = utf8_decode((substr($this->args["extHref"], 0, 1) == "#") ? "" : $this->args["extHref"]);
		if(isset($this->args["outsideWE"]) && $this->args["outsideWE"] == 1){


			$_select_type = '<select name="we_dialog_args[type]" size="1" style="margin-bottom:5px;" onchange="changeTypeSelect(this);">
<option value="ext"' . (($this->args["type"] == "ext") ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', "[external_link]") . '</option>
<option value="mail"' . (($this->args["type"] == "mail") ? ' selected="selected"' : '') . '>' . g_l('wysiwyg', "[emaillink]") . '</option>
</select>';


			$_external_link = we_html_tools::htmlTextInput("we_dialog_args[extHref]", 30, $extHref, "", '', "url", 300);


			// INTERNAL LINK
			$_internal_link = '';

			// E-MAIL LINK
			$_email_link = we_html_tools::htmlTextInput("we_dialog_args[mailHref]", 30, $this->args["mailHref"], "", '', "email", 300);

			// OBJECT LINK
			$_object_link = '';
		} else{
			$_select_type = '<select name="we_dialog_args[type]" id="weDialogType" size="1" style="margin-bottom:5px;width:300px;" onchange="changeTypeSelect(this);">
<option value="ext"' . (($this->args["type"] == "ext") ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', "[external_link]") . '</option>
<option value="int"' . (($this->args["type"] == "int") ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', "[internal_link]") . '</option>
<option value="mail"' . (($this->args["type"] == "mail") ? ' selected="selected"' : '') . '>' . g_l('wysiwyg', "[emaillink]") . '</option>' .
				((defined("OBJECT_TABLE") && ($_SESSION['weS']['we_mode'] == "normal" || we_hasPerm("CAN_SEE_OBJECTFILES"))) ?
					'<option value="obj"' . (($this->args["type"] == "obj") ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', "[objectFile]") . '</option>' :
					''
				) . '</select>';

			// EXTERNAL LINK
			//javascript:we_cmd('browse_server', 'document.we_form.elements[\\'we_dialog_args[extHref]\\'].value', '', document.we_form.elements['we_dialog_args[extHref]'].value, '')
			$wecmdenc1 = we_cmd_enc("document.we_form.elements['we_dialog_args[extHref]'].value");
			$_external_select_button = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_button::create_button("select", "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.we_form.elements['we_dialog_args[extHref]'].value, '')") : "";

			$_external_link = "<div style='margin-top:1px'>" . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[extHref]", 30, $extHref ? $extHref : "http://", "", 'onchange="if(this.value==\'\'){
					this.value=\'http://\';
}else{
	var x=this.value.match(/(.*:\/\/[^#?]*)(\?([^?#]*))?(#([^?#]*))?/);
	this.value=x[1];
	if(x[3]!=undefined){
		document.getElementsByName(\'we_dialog_args[param]\')[0].value=x[5];
	}
	if(x[5]!=undefined){
		document.getElementsByName(\'we_dialog_args[anchor]\')[0].value=x[3];
	}}"', "url", 300), "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $_external_select_button, '', '', '', 0) . '</div>';


			// INTERNAL LINK
			//javascript:we_cmd('openDocselector', document.we_form.elements['we_dialog_args[fileID]'].value, '" . FILE_TABLE . "', 'document.we_form.elements[\\'we_dialog_args[fileID]\\'].value', 'document.we_form.elements[\\'we_dialog_args[fileHref]\\'].value', '', '', 0, '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");
			$wecmdenc1 = we_cmd_enc("document.we_form.elements['we_dialog_args[fileID]'].value");
			$wecmdenc2 = we_cmd_enc("document.we_form.elements['we_dialog_args[fileHref]'].value");
			$_internal_select_button = we_button::create_button("select", "javascript:we_cmd('openDocselector', document.we_form.elements['we_dialog_args[fileID]'].value, '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','',0, '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");

			$yuiSuggest->setAcId("Path");
			$yuiSuggest->setContentType("folder,text/webedition,image/*,text/js,text/css,text/html,application/*,video/quicktime");
			$yuiSuggest->setInput("we_dialog_args[fileHref]", $this->args["fileHref"]);
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("we_dialog_args[fileID]", ($this->args["fileID"] == 0 ? "" : $this->args["fileID"]));
			$yuiSuggest->setSelector("Docselector");
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton($_internal_select_button, 10);

			$_internal_link = $yuiSuggest->getHTML();
			// E-MAIL LINK

			$_email_link = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[mailHref]", 30, $this->args["mailHref"], "", '', "email", 300), "", "left", "defaultfont", "", "", "", "", "", 0);

			// OBJECT LINK
			if(defined("OBJECT_TABLE") && ($_SESSION['weS']['we_mode'] == "normal" || we_hasPerm("CAN_SEE_OBJECTFILES"))){
				//javascript:we_cmd('openDocselector', document.we_form.elements['we_dialog_args[objID]'].value, '" . OBJECT_FILES_TABLE . "', 'document.we_form.elements[\\'we_dialog_args[objID]\\'].value', 'document.we_form.elements[\\'we_dialog_args[objHref]\\'].value', '', '', '', 'objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).");", false, 100, 22, "", "", !we_hasPerm("CAN_SEE_OBJECTFILES")
				$wecmdenc1 = we_cmd_enc("document.we_form.elements['we_dialog_args[objID]'].value");
				$wecmdenc2 = we_cmd_enc("document.we_form.elements['we_dialog_args[objHref]'].value");
				$wecmdenc3 = we_cmd_enc("top.opener._EditorFrame.setEditorIsHot(true);");
				$_object_select_button = we_button::create_button("select", "javascript:we_cmd('openDocselector', document.we_form.elements['we_dialog_args[objID]'].value, '" . OBJECT_FILES_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "', '', '', '', 'objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ");", false, 100, 22, "", "", !we_hasPerm("CAN_SEE_OBJECTFILES"));

				$yuiSuggest->setAcId("Obj");
				$yuiSuggest->setContentType("folder,objectFile");
				$yuiSuggest->setInput("we_dialog_args[objHref]", $this->args["objHref"]);
				$yuiSuggest->setMaxResults(20);
				$yuiSuggest->setMayBeEmpty(0);
				$yuiSuggest->setResult("we_dialog_args[objID]", ($this->args["objID"] == 0 ? "" : $this->args["objID"]));
				$yuiSuggest->setSelector("Docselector");
				$yuiSuggest->setTable(OBJECT_FILES_TABLE);
				$yuiSuggest->setWidth(300);
				$yuiSuggest->setSelectButton($_object_select_button, 10);

				$_object_link = $yuiSuggest->getHTML();
				/*
				  $_object_link = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[objHref]",30,$this->args["objHref"],"",' readonly="readonly"',"text",300, "0", "", !we_hasPerm("CAN_SEE_OBJECTFILES")) .
				  '<input type="hidden" name="we_dialog_args[objID]" value="'.$this->args["objID"].'" />', "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $_object_select_button, "", "", "", 0);
				 */
			}
		}

		$_anchorSel = (isset($this->args["editor"]) && $this->args["editor"] == 'tinyMce') ? '<div id="anchorlistcontainer"></div>' : we_html_element::jsElement('showanchors("anchors","","this.form.elements[\'we_dialog_args[anchor]\'].value=this.options[this.selectedIndex].value;this.selectedIndex=0;")');
		$_anchorInput = we_html_tools::htmlTextInput("we_dialog_args[anchor]", 30, $this->args["anchor"], "", "", "text", 300);

		$_anchor = we_html_tools::htmlFormElementTable($_anchorInput, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $_anchorSel, "", "", "", 0);

		$_param = we_html_tools::htmlTextInput("we_dialog_args[param]", 30, utf8_decode($this->args["param"]), '', '', 'text', 300);

		// CSS STYLE
		$classSelect = $this->args["editor"] == 'tinyMce' ? $this->getClassSelect() :
			$classSelect = we_html_element::jsElement('showclasss("we_dialog_args[class]", "' . $this->args["class"] . '", "");');


		// lang
		$_lang = $this->getLangField("lang", g_l('wysiwyg', "[link_lang]"), 145);
		$_hreflang = $this->getLangField("hreflang", g_l('wysiwyg', "[href_lang]"), 145);

		$_title = we_html_tools::htmlTextInput("we_dialog_args[title]", 30, $this->args["title"], "", "", "text", 300);


		$_accesskey = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[accesskey]", 30, $this->args["accesskey"], "", "", "text", 145), "accesskey");
		$_tabindex = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[tabindex]", 30, $this->args["tabindex"], "", ' onkeypress="return IsDigit(event);"', "text", 145), "tabindex");


		$_rev = we_html_tools::htmlFormElementTable($this->getRevRelSelect("rev"), "rev");
		$_rel = we_html_tools::htmlFormElementTable($this->getRevRelSelect("rel"), "rel");


		$parts = array();
		// Create table output
		$table = '<div style="position:relative; top:15px"><table cellpadding="0" cellspacing="0" border="0" height="65">
				<tr>
					<td class="defaultgray" valign="top" width="100" height="20">' . g_l('weClass', "[linkType]") . '</td>
					<td valign="top">' . $_select_type . '</td>
				</tr>
				<tr id="ext_tr" style="display:' . (($this->args["type"] == "ext") ? "table-row" : "none") . ';">
					<td class="defaultgray" valign="top" width="100">' . g_l('linklistEdit', "[external_link]") . '</td><td valign="top" >' . $_external_link . '</td>
				</tr>';

		if(isset($_internal_link)){
			$autoSuggest = $_internal_link .
				we_html_element::jsElement(
					'document.we_form.onsubmit = weonsubmit;
			function weonsubmit() {
				return false;
			}');

			$table .= '
				<tr id="int_tr" style="display:' . (($this->args["type"] == "int") ? "table-row" : "none") . ';">
					<td class="defaultgray" valign="top" width="100"> ' . g_l('weClass', "[document]") . '</td>
					<td valign="top"> ' . $autoSuggest . '</td>
				</tr>';
		}

		$table .= '
				<tr id="mail_tr" style="display:' . (($this->args["type"] == "mail") ? "table-row" : "none") . ';">
					<td class="defaultgray" valign="top" width="100">' . g_l('wysiwyg', "[emaillink]") . '</td>
					<td valign="top">
						' . $_email_link . '</td>
				</tr>';

		if(defined("OBJECT_TABLE") && isset($_object_link)){
			$table .= '
				<tr id="obj_tr" style="display:' . (($this->args["type"] == "obj") ? "table-row" : "none") . ';">
					<td class="defaultgray" valign="top" width="100" height="0">' . g_l('contentTypes', '[objectFile]') . '</td>
					<td valign="top">
						' . $_object_link . '</td>
				</tr>';
		}

		$show_accessible_class = (we_hasPerm("CAN_SEE_ACCESSIBLE_PARAMETERS") ? '' : ' class="weHide"');
		$table .= '</table></div>' .
			$yuiSuggest->getYuiCssFiles() .
			$yuiSuggest->getYuiCss() .
			$yuiSuggest->getYuiJsFiles() .
			$yuiSuggest->getYuiJs();

		$parts[] = array("html" => $table);

		$table = '<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="defaultgray" valign="top" width="100">' . g_l('wysiwyg', "[anchor]") . '</td>
					<td>' . $_anchor . '</td>
				</tr>
				<tr>
					<td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td>
				</tr>
				<tr>
					<td class="defaultgray" valign="top">' . g_l('linklistEdit', "[link_params]") . '</td>
					<td>' . $_param . '</td>
				</tr>
				<tr>
					<td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td>
				</tr>
				<tr>
					<td class="defaultgray" valign="top">' . g_l('wysiwyg', "[css_style]") . '</td>
					<td>' . $classSelect . '</td>
				</tr>
			 	<tr>
					<td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td>
				</tr>
				<tr>
					<td class="defaultgray" valign="top">' . g_l('linklistEdit', "[link_target]") . '</td>
					<td>' . we_html_tools::targetBox("we_dialog_args[target]", 29, 300, "we_dialog_args[target]", $this->args["target"], "", 10, 100) . '</td>
				</tr>
			</table>';
		$parts[] = array("html" => $table);


		$table = '<table cellpadding="0" cellspacing="0" border="0">
				<tr' . $show_accessible_class . '>
					<td class="defaultgray" valign="top" width="100">
						' . g_l('wysiwyg', "[language]") . '</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_lang . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' . $_hreflang . '</td></tr></table></td>
				</tr>
				<tr' . $show_accessible_class . '>
					<td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td>
				</tr>
				<tr' . $show_accessible_class . '>
					<td class="defaultgray" valign="top">' . g_l('wysiwyg', "[title]") . '</td>
					<td>' . $_title . '</td>
				</tr>
				<tr' . $show_accessible_class . '>
					<td colspan="2">' . we_html_tools::getPixel(110, 5) . '</td>
				</tr>
				<tr' . $show_accessible_class . '>
					<td class="defaultgray" valign="top">' . g_l('wysiwyg', "[keyboard]") . '</td>
					<td><table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_accesskey . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' . $_tabindex . '</td></tr></table></td>
				</tr>
				<tr' . $show_accessible_class . '>
					<td colspan="2">' . we_html_tools::getPixel(110, 5) . '</td>
				</tr>
				<tr' . $show_accessible_class . '>
					<td class="defaultgray" valign="top">' . g_l('wysiwyg', "[relation]") . '</td>
					<td><table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_rel . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' . $_rev . '</td></tr></table></td>
				</tr>
				<tr>
					<td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td>
				</tr>
			</table>';

		$parts[] = array("html" => $table);


		// Return output
		return $parts;
	}

	function getRevRelSelect($type){
		return '<input type="text" name="we_dialog_args[' . $type . ']" value="' . oldHtmlspecialchars($this->args["$type"]) . '" style="width:70px;" /><select name="' . $type . '_sel" size="1" style="width:75px;" onchange="this.form.elements[\'we_dialog_args[' . $type . ']\'].value=this.options[this.selectedIndex].text;this.selectedIndex=0;">
<option></option>
<option>contents</option>
<option>chapter</option>
<option>section</option>
<option>subsection</option>
<option>index</option>
<option>glossary</option>
<option>appendix</option>
<option>copyright</option>
<option>next</option>
<option>prev</option>
<option>start</option>
<option>help</option>
<option>bookmark</option>
<option>alternate</option>
<option>nofollow</option>
</select>';
	}

	function getTinyMceJS(){
		return parent::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/welink/js/welink_init.js');
	}

	function getJs(){
		return weDialog::getJs() . we_html_element::jsElement('
				var weAcCheckLoop = 0;
				var weFocusedField;
				function setFocusedField(elem){
					weFocusedField = elem;
				}

				function weCheckAcFields(){
					if(!!weFocusedField) weFocusedField.blur();
					if(document.getElementById("weDialogType").value=="int"){
						setTimeout("weDoCheckAcFields()",100);
					} else {
						document.forms["we_form"].submit();
					}
				}

				function weDoCheckAcFields(){
					acStatus = YAHOO.autocoml.checkACFields();
					acStatusType = typeof acStatus;
					if (weAcCheckLoop > 10) {
						' . we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						weAcCheckLoop = 0;
					} else if(acStatusType.toLowerCase() == "object") {
						if(acStatus.running) {
							weAcCheckLoop++;
							setTimeout("weDoCheckAcFields",100);
						} else if(!acStatus.valid) {
							' . we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							weAcCheckLoop=0;
						} else {
							weAcCheckLoop=0;
							document.forms["we_form"].submit();
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}
				}

			 	function changeTypeSelect(s){
					for(var i=0; i< s.options.length; i++){
						var trObj = document.getElementById(s.options[i].value+"_tr");
						if(i != s.selectedIndex){
							trObj.style.display = "none";
						}else{
							trObj.style.display = "";
						}
					}
			 	}

				function we_cmd() {
					var args = "";
					var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

					switch (arguments[0]) {
						case "openDocselector":
							new jsWindow(url,"we_docselector",-1,-1,' . WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT . ',true,false,true,true);
							break;

						case "browse_server":
							new jsWindow(url,"browse_server",-1,-1,800,400,true,false,true);
							break;
					}
				}

				function showclasss(name, val, onCh) {' .
				(isset($this->args["cssClasses"]) && $this->args["cssClasses"] ?
					'					var classCSV = "' . $this->args["cssClasses"] . '";
									classNames = classCSV.split(/,/);' : ($this->args["editor"] == "tinyMce" ? 'classNames = top.opener.weclassNames_tinyMce;' :
						'					classNames = top.opener.we_classNames;')) . '
					document.writeln(\'<select class="defaultfont" style="width:300px" name="\'+name+\'" id="\'+name+\'" size="1"\'+(onCh ? \' onChange="\'+onCh+\'"\' : \'\')+\'>\');
					document.writeln(\'<option value="">' . g_l('wysiwyg', "[none]") . '\');
					if(typeof(classNames) != "undefined"){
						for (var i = 0; i < classNames.length; i++) {
							var foo = classNames[i].substring(0,1) == "." ?
								classNames[i].substring(1,classNames[i].length) :
								classNames[i];
							document.writeln(\'<option value="\'+foo+\'"\'+((val==foo) ? \' selected\' : \'\')+\'>.\'+foo);
						}
					}
					document.writeln(\'</select>\');
				}' .
				(isset($this->args["editname"]) ? '

				function showanchors(name, val, onCh) {
					var pageAnchors = top.opener.document.getElementsByTagName("A");
					var objAnchors = top.opener.weWysiwygObject_' . $this->args["editname"] . '.eDocument.getElementsByTagName("A");
					var allAnchors = new Array();

					for(var i = 0; i < pageAnchors.length; i++) {
						if (!pageAnchors[i].href && pageAnchors[i].name != "") {
							allAnchors.push(pageAnchors[i].name);
						}
					}

					for (var i = 0; i < objAnchors.length; i++) {
						if(!objAnchors[i].href && objAnchors[i].name != "") {
							allAnchors.push(objAnchors[i].name);
						}
					}
					if(allAnchors.length){
						document.writeln(\'<select class="defaultfont" style="width:100px" name="\'+name+\'" id="\'+name+\'" size="1"\'+(onCh ? \' onChange="\'+onCh+\'"\' : \'\')+\'>\');
						document.writeln(\'<option value="">\');

						for (var i = 0; i < allAnchors.length; i++) {
							document.writeln(\'<option value="\'+allAnchors[i]+\'"\'+((val==allAnchors[i]) ? \' selected\' : \'\')+\'>\'+allAnchors[i]);
						}

						document.writeln(\'</select>\');
					}
				}
			' : '')
		);
	}

}
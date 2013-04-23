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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weDialog{
	/*	 * ***********************************************************************
	 * VARIABLES
	 * *********************************************************************** */

	var $db = "";
	var $what = "";
	var $args = array();
	var $cmdFN = "";
	var $okJsFN = "";
	var $dialogTitle = "";
	var $ClassName = __CLASS__;
	var $changeableArgs = array();
	var $pageNr = 1;
	var $numPages = 1;
	var $JsOnly = false;
	var $dialogWidth = 350;
	var $charset = "";
	var $tinyMCEPopupManagment = true;

	/*	 * ***********************************************************************
	 * CONSTRUCTOR
	 * *********************************************************************** */

	/**
	 * Constructor of class.
	 *
	 * @return     weDialog
	 */
	function __construct(){
		$this->db = new DB_WE();
	}

	/*	 * ***********************************************************************
	 * FUNCTIONS
	 * *********************************************************************** */

	function setTitle($title){
		$this->dialogTitle = $title;
	}

	function registerOkJsFN($fnName){
		$this->okJsFN = $fnName;
	}

	function registerCmdFn($fnName){
		$this->cmdFN = $fnName;
	}

	function initByHttp(){
		$this->tinyMCEPopupManagment = (isset($_REQUEST["tinyMCEPopupManagment"]) && $_REQUEST["tinyMCEPopupManagment"] == "n") ? false : $this->tinyMCEPopupManagment;
		$this->what = isset($_REQUEST["we_what"]) ? $_REQUEST["we_what"] : "";

		if(isset($_REQUEST["we_dialog_args"]) && is_array($_REQUEST["we_dialog_args"])){
			$this->args = $_REQUEST["we_dialog_args"];
			foreach($this->args as $key => $value){
				$this->args[$key] = urldecode($value);
			}
		}

		if(isset($_REQUEST["we_pageNr"])){
			$this->pageNr = $_REQUEST["we_pageNr"];
		}
	}

	function getHTML(){
		if($this->JsOnly){
			$this->what = "dialog";
		}

		switch($this->what){
			case "dialog":
				return $this->getHeaderHTML(true) .
					$this->getBodyTagHTML() .
					$this->getDialogHTML() .
					$this->getFooterHTML();
			case "cmd":
				return $this->getCmdHTML();
			default:
				return $this->getHeaderHTML() .
					$this->getFramesetHTML() .
					$this->getBodyTagHTML() .
					$this->getFooterHTML();
		}
	}

	function getCmdHTML(){
		$fn = $this->cmdFN;
		$send = array();

		// " quote for correct work within ""
		foreach($this->args as $k => $v){
			$send[$k] = str_replace('"', '\"', $v);
		}
		if($this->cmdFN){
			return $fn($send);
		} else{
			return $this->cmdFunction($send);
		}
	}

	function cmdFunction($args){
		// overwrite
	}

	function getOkJs(){
		if($this->okJsFN){
			$fn = $this->okJsFN;
			return $fn();
		}
	}

	function getQueryString($what = ""){
		$query = "";
		if(isset($_REQUEST['we_cmd']) && is_array($_REQUEST['we_cmd'])){
			foreach($_REQUEST['we_cmd'] as $k => $v){
				$query .= "we_cmd[" . rawurlencode($k) . "]=" . rawurlencode($v) . "&";
			}
		}
		if(isset($this->args) && is_array($this->args)){
			foreach($this->args as $k => $v){
				$query .= "we_dialog_args[" . rawurlencode($k) . "]=" . rawurlencode($v) . "&";
			}
		}
		return rtrim($query, '&') . ($what ? "&we_what=" . rawurlencode($what) : '');
	}

	function getFramesetHTML(){
		return we_html_element::jsElement('
				var isGecko = ' . (we_base_browserDetect::isGecko() ? 'true' : 'false') . ';
				var isOpera = ' . (we_base_browserDetect::isOpera() ? 'true' : 'false') . ';' .
				((!(we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera())) ?
					'document.onkeydown = doKeyDown;' : '') . '

				function doKeyDown() {
					var key = event.keyCode;

					switch(key) {
						case 27:
							top.close();
							break;

						case 13:
							self.we_' . $this->ClassName . '_edit_area.weDoOk();
							break;
					}
				}') . '

			<frameset rows="*,0" framespacing="0" border="0" frameborder="no">
				<frame src="' . $_SERVER["SCRIPT_NAME"] . '?' . $this->getQueryString("dialog") . '&tinyMCEPopupManagment=n" name="we_' . $this->ClassName . '_edit_area" scrolling="no" noresize="noresize">
				<frame src="' . HTML_DIR . 'white.html" name="we_' . $this->ClassName . '_cmd_frame" scrolling="no" noresize="noresize">
			</frameset>';
	}

	function getNextBut(){
		return we_button::create_button("next", "javascript:document.forms['0'].submit();");
	}

	function getOkBut(){
		return we_button::create_button("ok", "javascript:weDoOk();");
	}

	function getCancelBut(){
		return we_button::create_button("cancel", "javascript:top.close();");
	}

	function getbackBut(){
		return ($this->pageNr > 1) ? we_button::create_button("back", "javascript:history.back();") . we_html_tools::getPixel(10, 2) : "";
	}

	function getDialogHTML(){
		$dc = $this->getDialogContentHTML();

		$dialogContent = (is_array($dc) ?
				we_multiIconBox::getHTML("", "100%", $dc, 30, $this->getDialogButtons(), -1, "", "", false, $this->dialogTitle, "", $this->getDialogHeight()) :
				we_html_tools::htmlDialogLayout($dc, $this->dialogTitle, $this->getDialogButtons()));

		return $this->getFormHTML() . $dialogContent .
			'<input type="hidden" name="we_what" value="cmd" />' . $this->getHiddenArgs() . '</form>';
	}

	function getDialogHeight(){
		return "";
	}

	function getDialogButtons(){
		if($this->pageNr == $this->numPages && $this->JsOnly == false){
			$okBut = ($this->getBackBut() != "") ? we_button::create_button_table(array($this->getBackBut(), we_button::create_button("ok", "form:we_form"))) : we_button::create_button("ok", "form:we_form");
		} else if($this->pageNr < $this->numPages){
			$okBut = (($this->getBackBut() != "") && ($this->getNextBut()) != "") ? we_button::create_button_table(array($this->getBackBut(), $this->getNextBut())) : (($this->getBackBut() == "") ? $this->getNextBut() : $this->getBackBut());
		} else{
			$okBut = (($this->getBackBut() != "") && ($this->getOkBut()) != "") ? we_button::create_button_table(array($this->getBackBut(), $this->getOkBut())) : (($this->getBackBut() == "") ? $this->getOkBut() : $this->getBackBut());
		}
		return we_button::position_yes_no_cancel($okBut, "", $this->getCancelBut());
	}

	function getFormHTML(){
		$hiddens = "";
		if(isset($_REQUEST['we_cmd']) && is_array($_REQUEST['we_cmd'])){
			foreach($_REQUEST['we_cmd'] as $k => $v){
				$hiddens .= '<input type="hidden" name="we_cmd[' . $k . ']" value="' . rawurlencode($v) . '" />';
			}
		}
		$target = (!$this->JsOnly ? ' target="we_' . $this->ClassName . '_cmd_frame"' : '');

		return '<form name="we_form" action="' . $_SERVER["SCRIPT_NAME"] . '" method="post"' . $target . '>' . $hiddens;
	}

	function getHiddenArgs(){
		$hiddenArgs = '';

		foreach($this->args as $k => $v){
			if(!in_array($k, $this->changeableArgs)){
				$hiddenArgs .= '<input type="hidden" name="we_dialog_args[' . $k . ']" value="' . oldHtmlspecialchars($v) . '" />';
			}
		}
		return $hiddenArgs;
	}

	function getDialogContentHTML(){
		return ""; // overwrite !!
	}

	function getHeaderHTML($printJS_Style = false){
		return we_html_tools::htmlTop($this->dialogTitle, $this->charset) .
			(isset($this->args['editor']) && $this->args['editor'] == 'tinyMce' ? $this->getTinyMceJS() : '') .
			($printJS_Style ? STYLESHEET . $this->getJs() : '') . we_html_element::cssLink(WEBEDITION_DIR . 'editors/content/tinymce/we_tinymce/weDialogCss.css') .
			'</head>';
	}

	function getTinyMceJS(){
		return
			we_html_element::jsElement('var isWeDialog = true;') .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'tiny_mce_popup.js') .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'utils/mctabs.js') .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'utils/form_utils.js') .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'utils/validate.js') .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'utils/editable_selects.js');
	}

	function getJs(){
		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
				var isGecko = ' . (we_base_browserDetect::isGecko() ? 'true' : 'false') . ';
				var textareaFocus = false;
				' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? '
					document.addEventListener("keyup",doKeyDown,true);' : 'document.onkeydown = doKeyDown;') . '

				function doKeyDown(e) {
					var key;

' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'key = e.keyCode;' : 'key = event.keyCode;') . '

					switch (key) {
						case 27:
							top.close();
							break;' .
				($this->pageNr == $this->numPages && $this->JsOnly ? '
								case 13:
									if (!textareaFocus) {
										weDoOk();
									}
									break;' : '') .
				'	}
				}

				function weDoOk() {' .
				($this->pageNr == $this->numPages && $this->JsOnly ? '
							if (!textareaFocus) {
								' . $this->getOkJs() . '
							}' : '') .
				'
				}

				function IsDigit(e) {
					var key;

					if (e.charCode == undefined) {
						key = event.keyCode;
					} else {
						key = e.charCode;
					}

					return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13)  || (key == 8) || (key <= 63235 && key >= 63232) || (key == 63272));
				}

				function openColorChooser(name,value) {
					var win = new jsWindow("colorDialog.php?we_dialog_args[type]=dialog&we_dialog_args[name]="+escape(name)+"&we_dialog_args[color]="+escape(value),"colordialog",-1,-1,400,380,true,false,true,false);
				}

				function IsDigitPercent(e) {
					var key;
					if (e.charCode == undefined) {
						key = event.keyCode;
					} else {
						key = e.charCode;
					}

					return (((key >= 48) && (key <= 57)) || (key == 37) || (key == 0) || (key == 46)  || (key == 101)  || (key == 109)  || (key == 13)  || (key == 8) || (key <= 63235 && key >= 63232) || (key == 63272));
				}

				function doUnload() {
					if (jsWindow_count) {
						for (i = 0; i < jsWindow_count; i++) {
							eval("jsWindow" + i + "Object.close()");
						}
					}
				}

				self.focus();');
	}

	function formColor($size, $name, $value, $width = ""){
		return '<input size="' . $size . '" type="text" name="' . $name . '" style="' . ($width ? 'width:' . $width . 'px;' : '') . 'background-color:' . $value . '" value="' . $value . '" onClick="openColorChooser(\'' . $name . '\',this.value);" readonly />';
	}

	function getBodyTagHTML(){
		return '<body ' . (!$this->tinyMCEPopupManagment ? 'id="weDialogInnerFrame" ' : '') . 'class="weDialogBody" onUnload="doUnload()">';
	}

	function getFooterHTML(){
		return '</body></html>';
	}

	function getHttpVar($name, $alt = ""){
		return isset($_REQUEST["we_dialog_args"][$name]) ? $_REQUEST["we_dialog_args"][$name] : $alt;
	}

	function getLangField($name, $title, $width){
		$foo = we_html_tools::htmlTextInput("we_dialog_args[" . $name . "]", 15, (isset($this->args[$name]) ? $this->args[$name] : ""), "", '', "text", $width - 50);
		$foo2 = '<select style="width:50px;" class="defaultfont" name="' . $name . '_select" size="1" onChange="this.form.elements[\'we_dialog_args[' . $name . ']\'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;">
							<option value=""></option>
							<option value="en">en</option>
							<option value="de">de</option>
							<option value="es">es</option>
							<option value="fi">fi</option>
							<option value="ru">ru</option>
							<option value="fr">fr</option>
							<option value="nl">nl</option>
							<option value="pl">pl</option>
						</select>';
		return we_html_tools::htmlFormElementTable($foo, $title, "left", "defaultfont", $foo2);
	}

	function getClassSelect(){
			$clSelect = new we_html_select(array("name" => "we_dialog_args[class]", "id" => "we_dialog_args[class]", "size" => "1", "style" => "width: 300px;"));
			$clSelect->addOption("", g_l('wysiwyg', "[none]"));
			$classesCSV = trim($this->args["cssclasses"], ",");
			if(!empty($classesCSV)){
				foreach(explode(",", $classesCSV) as $val){
					$clSelect->addOption($val, "." . $val);
				}
			}
			if(isset($this->args["class"]) && !empty($this->args["class"])){
				$clSelect->selectOption($this->args["class"]);
			}

			return $clSelect->getHTML() . '<input type="hidden" name="we_dialog_args[cssclasses]" value="' . oldHtmlspecialchars($classesCSV) . '" />';
	}

}
<?php

/**
 * webEdition CMS
 *
 * $Rev: 5906 $
 * $Author: lukasimhof $
 * $Date: 2013-03-01 14:08:18 +0100 (Fri, 01 Mar 2013) $
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
class weFullscreenEditDialog extends weDialog{

	var $JsOnly = true;
	var $ClassName = __CLASS__;
	var $changeableArgs = array("src");

	function __construct(){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', "[fullscreen_editor]");
		$this->args["src"] = "";
	}

	function getDialogContentHTML(){
		$js = we_html_element::jsElement('isFullScreen = true;');
		$e = new we_wysiwyg("we_dialog_args[src]", $this->args["screenWidth"] - 90, $this->args["screenHeight"] - 200, '', $this->args["propString"], $this->args["bgcolor"], $this->args["editname"], $this->args["className"], $this->args["outsideWE"], $this->args["outsideWE"], $this->args["xml"], $this->args["removeFirstParagraph"], true, $this->args["baseHref"], $this->args["charset"], $this->args["cssClasses"], $this->args['language'], '', true, false, 'top', true, $this->args["contentCss"], $this->args["origName"], $this->args["tinyParams"]);
		return we_wysiwyg::getHeaderHTML() . $js . $e->getHTML();
	}

	function getTinyMceJS(){
		return parent::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/wefullscreen/js/fullscreen_init.js');
	}

	function getJs(){
		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
				var isGecko = ' . (we_base_browserDetect::isGecko() ? 'true' : 'false') . ';
				var textareaFocus = false;

				' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'document.addEventListener("keyup",doKeyDown,true);' : 'document.onkeydown = doKeyDown;') . '

				function doKeyDown(e) {
					var ' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'key = e.keyCode;' : 'key = event.keyCode;') . '

					switch (key) {
						case 27:
							top.close();
							break;
					}
				}

				function weDoOk() {
					if(typeof(isTinyMCE) != "undefined" && isTinyMCE === true){
						WefullscreenDialog.writeback();
						top.close();
					} else{' .
							($this->pageNr == $this->numPages && $this->JsOnly ? '
							if (!textareaFocus) {
								' . $this->getOkJs() . '
							}' : '') . '
					}
				}

		function IsDigit(e) {
					var ' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'key = e.charCode;' : 'key = event.keyCode;') . '
					return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13));
				}

				function openColorChooser(name,value) {
					var win = new jsWindow("colorDialog.php?we_dialog_args[type]=dialog&we_dialog_args[name]="+escape(name)+"&we_dialog_args[color]="+escape(value),"colordialog",-1,-1,400,380,true,false,true,false);
				}

				function IsDigitPercent(e) {
					var ' . (we_base_browserDetect::isGecko() || we_base_browserDetect::isOpera() ? 'key = e.charCode;' : 'key = event.keyCode;') . '

					return (((key >= 48) && (key <= 57)) || (key == 37) || (key == 0)  || (key == 13));
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
	
	function getOkBut(){
		return we_button::create_button("ok", "javascript:top.opener.tinyMCECallRegisterDialog({},'unregisterDialog');weDoOk();");
	}
	
	function getCancelBut(){
		return we_button::create_button("cancel", "javascript:top.opener.tinyMCECallRegisterDialog({},'unregisterDialog');top.close();");
	}
	
	function getBodyTagHTML(){
		return '<body id="weFullscreenDialog" class="weDialogBody" onUnload="doUnload()" onbeforeunload="top.opener.tinyMCECallRegisterDialog({},\'unregisterDialog\')" >';
	}

}
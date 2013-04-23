<?php

/**
 * webEdition CMS
 *
 * $Rev: 5174 $
 * $Author: mokraemer $
 * $Date: 2012-11-19 13:55:03 +0100 (Mon, 19 Nov 2012) $
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
 * @package    webEdition_modules
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class weModuleFrames{

	var $module;
	var $db;
	var $frameset;
	var $Tree;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;

	function __construct($frameset){
		$this->db = new DB_WE();
		$this->frameset = $frameset;
		$this->Tree = new weTree();
	}

	function setFrames($topFrame, $treeFrame, $cmdFrame){
		$this->topFrame = $topFrame;
		$this->treeFrame = $treeFrame;
		$this->cmdFrame = $cmdFrame;
	}

	public function setupTree($table, $topFrame, $treeFrame, $cmdFrame){
		$this->setFrames($topFrame, $treeFrame, $cmdFrame);
		$this->Tree->init($this->frameset, $topFrame, $treeFrame, $cmdFrame);
	}

	function getJSStart(){
		return ($this->Tree->initialized ? 'function start(){startTree();}' : 'function start(){}');
	}

	function getHTMLDocument($body, $extraHead = ''){
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead($this->module) . STYLESHEET . $extraHead
				) . $body
		);
	}

	function getJSCmdCode(){
		return we_html_element::jsElement('function we_cmd(){}');
	}

	function getHTMLFrameset(){

		$js = $this->getJSCmdCode() .
			$this->Tree->getJSTreeCode() .
			we_html_element::jsElement($this->getJSStart()) .
			we_html_element::jsScript(JS_DIR . 'we_showMessage.js');

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => ((isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0) ? "32,*,100" : "32,*,0" ), "onLoad" => "start();"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=header", "name" => "header", "scrolling" => "no", "noresize" => null));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=resize" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "resize", "scrolling" => "no"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=cmd", "name" => "cmd", "scrolling" => "no", "noresize" => null));

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLHeader(){
		//	Include the menu.
		include(WE_INCLUDES_PATH . "java_menu/modules/module_menu_" . $this->module . ".inc.php");
		include_once(WE_INCLUDES_PATH . "jsMessageConsole/messageConsole.inc.php" );

		$lang_arr = "we_menu_" . $this->module;
		$jmenu = new weJavaMenu($$lang_arr, "top.opener.top.load", 350, 30);

		$menu = $jmenu->getCode(true, 'cmd');

		$table = new we_html_table(array("width" => "100%", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 1, 2);
		$table->setCol(0, 0, array("align" => "left", "valign" => "top"), $menu);
		$table->setCol(0, 1, array("align" => "right", "valign" => "top"), createMessageConsole("moduleFrame"));

		$body = we_html_element::htmlBody(array('style' => 'background-color:#efefef;background-image: url(' . IMAGE_DIR . 'java_menu/background.gif); background-repeat:repeat;margin:0px;'), $table->getHtml());

		return $this->getHTMLDocument($body);
	}

	function getHTMLResize(){

		if(we_base_browserDetect::isGecko()){
			$frameset = new we_html_frameset(array("cols" => "200,*", "border" => "1", "id" => "resizeframeid"));
		} else{
			$frameset = new we_html_frameset(array("cols" => "200,*", "border" => "0", "frameborder" => "0", "framespacing" => "0", "id" => "resizeframeid"));
		}
		if(we_base_browserDetect::isIE()){
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=left", "name" => "left", "scrolling" => "no", "frameborder" => "no"));
		} else{
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=left", "name" => "left", "scrolling" => "no"));
		}
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=right" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "right"));

		$noframeset = new we_baseElement("noframes");

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLLeft(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "1,*"));
		$frameset->addFrame(array("src" => HTML_DIR . "whiteWithTopLine.html", "name" => "treeheader", "noresize" => null, "scrolling" => "no"));
		$frameset->addFrame(array("src" => WEBEDITION_DIR . "treeMain.php", "name" => "tree", "noresize" => null, "scrolling" => "auto"));

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLRight(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		if((we_base_browserDetect::isGecko()) || we_base_browserDetect::isOpera()){
			$frameset->setAttributes(array("cols" => "*"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "editor", "noresize" => null, "scrolling" => "no"));
		} else if(we_base_browserDetect::isSafari()){
			$frameset->setAttributes(array("cols" => "1,*"));
			$frameset->addFrame(array("src" => HTML_DIR . "safariResize.html", "name" => "separator", "noresize" => null, "scrolling" => "no"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "editor", "noresize" => null, "scrolling" => "no"));
		} else{
			$frameset->setAttributes(array("cols" => "2,*"));
			$frameset->addFrame(array("src" => HTML_DIR . "ieResize.html", "name" => "separator", "noresize" => null, "scrolling" => "no"));
			$frameset->addFrame(array("src" => $this->frameset . "?pnt=editor" . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : ''), "name" => "editor", "noresize" => null, "scrolling" => "no"));
		}
		$noframeset = new we_baseElement("noframes");
		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLEditor(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "40,*,40"));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edheader', 'name' => 'edheader', 'noresize' => null, 'scrolling' => 'no'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edbody', 'name' => 'edbody', 'scrolling' => 'auto'));
		$frameset->addFrame(array('src' => $this->frameset . (isset($_REQUEST['sid']) ? '?sid=' . $_REQUEST['sid'] : '?home=1') . '&pnt=edfooter', 'name' => 'edfooter', 'scrolling' => 'no'));

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLCmd(){
		// set and return html code
		$head = $this->Tree->getJSLoadTree();
		$body = we_html_element::htmlBody();

		return $this->getHTMLDocument($body, $head);
	}

	function getHTMLBox($content, $headline = "", $width = "100", $height = "50", $w = "25", $vh = "0", $ident = "0", $space = "5", $headline_align = "left", $content_align = "left"){
		$headline = str_replace(" ", "&nbsp;", $headline);
		if($ident){
			$pix1 = we_html_tools::getPixel($ident, $vh);
		}
		if($w){
			if(!$vh){
				$vh = 1;
			}
			$pix2 = we_html_tools::getPixel($w, $vh);
		}

		$pix3 = we_html_tools::getPixel($space, 1);

		$table = new we_html_table(array("width" => $width, "height" => $height, "cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 4);

		if($ident){
			$table->setCol(0, 0, array("valign" => "top"), $pix1);
		}
		if($w){
			$table->setCol(0, 1, array("valign" => "top"), $pix2);
		}
		$table->setCol(1, 1, array("valign" => "middle", "class" => "defaultgray", "align" => $headline_align), $headline);
		$table->setCol(1, 2, array(), $pix3);
		$table->setCol(1, 3, array("valign" => "middle", "align" => $content_align), $content);
		if($w && $headline != ""){
			$table->setCol(2, 1, array("valign" => "top"), $pix2);
		}
		return $table->getHtml();
	}

}

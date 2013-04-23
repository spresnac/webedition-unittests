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
//make sure we know which browser is used

class we_wysiwyg{

	var $name = '';
	private $origName = '';
	var $width = '';
	var $height = '';
	var $ref = '';
	var $propstring = '';
	var $elements = array();
	var $value = '';
	private $filteredElements = array();
	private $bgcol = '';
	private $tinyParams = '';
	private $fullscreen = '';
	private $className = '';
	private $fontnamesCSV = '';
	private $fontnames = array();
	private $tinyFonts = '';
	private $tinyFormatblock = '';
	private $maxGroupWidth = 0;
	private $outsideWE = false;
	private $xml = false;
	private $removeFirstParagraph = true;
	var $charset = '';
	private $inlineedit = true;
	private $cssClasses = '';
	private $cssClassesJS = '';
	private $cssClassesCSV = '';
	var $Language = '';
	private $_imagePath;
	private $_image_languagePath;
	private $baseHref = '';
	private $showSpell = true;
	private $isFrontendEdit = false;
	private $htmlSpecialchars = true; // in wysiwyg default was "true" (although Tag-Hilfe says "false")
	private $contentCss = '';
	public static $editorType = WYSIWYG_TYPE; //FIXME: remove after old editor is removed

	function __construct($name, $width, $height, $value = '', $propstring = '', $bgcol = '', $fullscreen = '', $className = '', $fontnames = '', $outsideWE = false, $xml = false, $removeFirstParagraph = true, $inlineedit = true, $baseHref = '', $charset = '', $cssClasses = '', $Language = '', $test = '', $spell = true, $isFrontendEdit = false, $buttonpos = 'top', $oldHtmlspecialchars = true, $contentCss = '', $origName = '', $tinyParams = ''){
		$this->propstring = $propstring ? ',' . $propstring . ',' : '';
		$this->name = $name;
		$this->origName = $origName;
		$this->bgcol = (self::$editorType != 'tinyMCE' && empty($bgcol)) ? 'white' : $bgcol;
		$this->tinyParams = str_replace('\'', '"', trim($tinyParams, ' ,'));
		$this->xml = $xml;
		if(self::$editorType == 'tinyMCE'){
			$this->xml = $this->xml ? "xhtml" : "html";
		}
		$this->removeFirstParagraph = $removeFirstParagraph;
		$this->inlineedit = $inlineedit;
		$this->fullscreen = $fullscreen;
		$this->className = $className;
		$this->buttonpos = (self::$editorType == 'tinyMCE' ? $buttonpos : 'top');
		$this->statuspos = $this->buttonpos != 'external' ? $this->buttonpos : 'bottom';
		$this->outsideWE = $outsideWE;
		$this->fontnamesCSV = $fontnames;
		if(self::$editorType == 'tinyMCE'){
			if($fontnames){
				$fn = explode(',', $fontnames);
				$tf = '';
				foreach($fn as $val){
					$tf .= $val . '=' . strtolower($val) . ';';
				}
				$this->tinyFonts = substr($tf, 0, -1);
			} else{
				$this->tinyFonts = 'Arial=arial,helvetica,sans-serif;' .
					'Courier New=courier new,courier;' .
					'Geneva=Geneva, Arial, Helvetica, sans-serif;' .
					'Georgia=Georgia, Times New Roman, Times, serif;' .
					'Tahoma=Tahoma;' .
					'Times New Roman=Times New Roman,Times,serif;' .
					'Verdana=Verdana, Arial, Helvetica, sans-serif;' .
					'Wingdings=wingdings,zapf dingbats';
			}
		} else{
			$fn = $fontnames ? explode(',', $fontnames) : array('Arial, Helvetica, sans-serif', 'Courier New, Courier, mono', 'Geneva, Arial, Helvetica, sans-serif', 'Georgia, Times New Roman, Times, serif', 'Tahoma', 'Times New Roman, Times, serif', 'Verdana, Arial, Helvetica, sans-serif', 'Wingdings');
			foreach($fn as &$font){
				$font = strtolower(str_replace(';', ',', $font));
				$this->fontnames[$font] = $font;
			}
		}
		$this->cssClasses = $cssClasses;
		$this->cssClassesCSV = $cssClasses;
		if($this->cssClasses != '' && self::$editorType == 'tinyMCE'){
			$cc = explode(',', $this->cssClasses);
			$tf = '';
			$jsCl = '';
			foreach($cc as $val){
				$tf .= $val . '=' . $val . ';';
				$jsCl .= '"' . $val . '"' . ',';
			}
			$this->cssClasses = rtrim($tf, ';');
			$this->cssClassesJS = rtrim($jsCl, ',');
		}
		$this->contentCss = $contentCss;

		$this->Language = $Language;
		$this->showSpell = $spell;
		$this->htmlSpecialchars = $oldHtmlspecialchars;
		$this->isFrontendEdit = $isFrontendEdit;

		$this->_imagePath = IMAGE_DIR . 'wysiwyg/';
		$this->_image_languagePath = WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/wysiwyg/';

		$this->baseHref = $baseHref ? $baseHref : we_util::getGlobalPath();
		$this->charset = $charset;

		$this->width = (self::$editorType == 'tinyMCE' ? $width - 20 : $width); //imi
		$this->height = $height;
		$this->ref = preg_replace('%[^0-9a-zA-Z_]%', '', $this->name);
		$this->hiddenValue = $value;

		if($inlineedit){
			if($value){
				if(self::$editorType == 'tinyMCE'){
					//FIXME: what to do with scripts??
				}else{
					$value = strtr($value, array("\\" => "\\\\", "\n" => '\n', "\r" => '\r'));
					$value = str_replace(array('script', 'Script', 'SCRIPT',), array('##scr#ipt##', '##Scr#ipt##', '##SCR#IPT##',), $value);
					$value = preg_replace('%<\?xml[^>]*>%i', '', $value);
					$value = str_replace(array('<?', '?>',), array('||##?##||', '##||?||##'), $value);
				}
			}
		}

		$this->setToolbarElements();
		$this->setFilteredElements();
		$this->getMaxGroupWidth();
		$this->value = $value;
	}

	function getMaxGroupWidth(){
		$w = 0;
		foreach($this->filteredElements as $i => $v){
			if($v->classname == 'we_wysiwyg_ToolbarSeparator'){
				$this->maxGroupWidth = max($w, $this->maxGroupWidth);
				$w = 0;
			} else{
				$w += $v->width;
			}
		}
		$this->maxGroupWidth = max($w, $this->maxGroupWidth);
	}

	static function getHeaderHTML(){
		if(defined('WE_WYSIWG_HEADER')){
			return '';
		}

		define('WE_WYSIWG_HEADER', 1);
		switch(self::$editorType){
			case 'tinyMCE':
				//FIXME: remove onchange - bad practise
				return '
				<style type="text/css">
					.tbButtonWysiwygBorder {
						border: 1px solid #006DB8;
						background-image: url(' . IMAGE_DIR . 'pixel.gif);
					  background-image: url(' . IMAGE_DIR . 'pixel.gif);
						margin: 0px;
						padding:4px;
						text-align: left;
						text-decoration: none;
						position: relative;
						overflow: auto;
					}
				</style>
				'
					.
					we_html_element::jsScript(WEBEDITION_DIR . 'editors/content/tinymce/jscripts/tiny_mce/tiny_mce.js') . we_html_element::jsElement('
					tinyMceGL = {
						welink : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[hyperlink]")) . '"},
						weimage: {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[insert_edit_image]")) . '"},
						weabbr : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[abbr]")) . '"},
						weacronym : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[acronym]")) . '"},
						wefullscreen : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[fullscreen]")) . '"},
						weinsertbreak : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[insert_br]")) . '"},
						weinsertrtf : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[rtf_import]")) . '"},
						welang : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[language]")) . '"},
						wespellchecker : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[spellcheck]")) . '"},
						wevisualaid : {tooltip : "' . CheckAndConvertISOfrontend(g_l('wysiwyg', "[visible_borders]")) . '"}
					};
				') . we_html_element::jsElement('

function tinyMCEchanged(inst){
	// moved to tinymce init.setup()
}

				') . we_html_element::jsElement('
function tinyMCECallRegisterDialog(win,action){
	if(typeof(top.isRegisterDialogHere) != "undefined"){
		try{
			top.weRegisterTinyMcePopup(win,action);
		} catch(err) {}
	} else {
		if(typeof(top.opener.isRegisterDialogHere) != "undefined"){
			try{
				top.opener.weRegisterTinyMcePopup(win,action);
			} catch(err){}
		} else {
			try{
				top.opener.tinyMCECallRegisterDialog(win,action);
			} catch(err){}
		}
	}
}
				') .
					we_html_element::jsElement('
					function weWysiwygSetHiddenTextSync(){
						weWysiwygSetHiddenText(1);
						setTimeout(weWysiwygSetHiddenTextSync,500);
					}

					function weWysiwygSetHiddenText(arg) {
						try {
							if (weWysiwygIsIntialized) {
								for (var i = 0; i < we_wysiwygs.length; i++) {
									we_wysiwygs[i].setHiddenText(arg);
								}
							}else{
								}
						} catch(e) {
							// Nothing
						}
					}');
			default:
			case 'default':
				include_once(WEBEDITION_PATH . 'editors/content/wysiwyg/weWysiwygLang.inc.php');
				return getWysiwygLang() . '
				<style type="text/css">
					.tbButton {
						border: 1px solid #F4F4F4;
						padding: 0px;
						margin: 0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonMouseOverUp {
						border-bottom: 1px solid #000000;
						border-left: 1px solid #CCCCCC;
						border-right: 1px solid #000000;
						border-top: 1px solid #CCCCCC;
						cursor:pointer;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonMouseOverDown {
						border-bottom: 1px solid #CCCCCC;
						border-left: 1px solid #000000;
						border-right: 1px solid #CCCCCC;
						border-top: 1px solid #000000;
						cursor: pointer;
						margin: 0px;
						padding: 0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonDown {
						background-image: url(' . IMAGE_DIR . 'java_menu/background_dark.gif);
						border-bottom: #CCCCCC solid 1px;
						border-left: #000000 solid 1px;
						border-right: #CCCCCC solid 1px;
						border-top:  #000000 solid 1px;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonsHR {
						border-top:  #000000 solid 1px;
						border-bottom:  #CCCCCC solid 1px;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						position: relative;
					}
					.tbButtonWysiwygBorder {
						border: 1px solid #006DB8;
						background-image: url(' . IMAGE_DIR . 'pixel.gif);
						margin: 0px;
						padding:4px;
						text-align: left;
						text-decoration: none;
						position: relative;
						overflow: auto;
					}
					.tbButtonWysiwygBackground{
						background-image: url(' . IMAGE_DIR . 'backgrounds/aquaBackground.gif) ! important;
					}
					.tbButtonWysiwygDefaultStyle{
						background: transparent;
						background-color: transparent;
						background-image: url(' . IMAGE_DIR . 'pixel.gif);
						border: 0px;
						color: #000000;
						cursor: default;
						font-size: ' . ((we_base_browserDetect::isMAC()) ? "11px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px")) . ';
						font-family: ' . g_l('css', '[font_family]') . ';
						font-weight: normal;
						margin: 0px;
						padding:0px;
						text-align: left;
						text-decoration: none;
						left: auto ! important;
						right: auto ! important;
						width: auto ! important;
						height: auto ! important;
					}

				</style>' . we_html_element::jsElement('
					var we_wysiwygs = new Array();
					var we_wysiwyg_lng = new Array();
					var isGecko = ' . (we_base_browserDetect::isGecko() ? 'true' : 'false') . ';
					var isOpera = ' . (we_base_browserDetect::isOpera() ? 'true' : 'false') . ';
					var isIE = ' . (we_base_browserDetect::isIE() ? 'true' : 'false') . ';
					var ieVersion = ' . we_base_browserDetect::getIEVersion() . ';
					var isIE9 = ' . ((we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() == 9) ? 'true' : 'false') . ';
					var weWysiwygLoaded = false;
					var weNodeList = new Array();
					var weWysiwygFolderPath = "' . WEBEDITION_DIR . 'editors/content/wysiwyg/";
					var weWysiwygImagesFolderPath = "' . IMAGE_DIR . 'wysiwyg/";
					var weWysiwygBgGifPath = "' . IMAGE_DIR . 'backgrounds/aquaBackground.gif";
					var weWysiwygIsIntialized = false;

					var wePopupMenuArray = new Array();

					// Bugfix do not overwrite body.onload !!!
					function weEvent(){}
					weEvent.addEvent = function(e, name, f) {
						if (e.addEventListener) {
							e.addEventListener(
								name,
								f,
								true);
						}
						if(e.attachEvent){
							e.attachEvent("on" + name, f);
						}
					}

					//window.onerror = weNothing;
					//  Bugfix do not overwrite body.onload !!!
					weEvent.addEvent(window,"load", weWysiwygInitializeIt);
					//window.onload = weWysiwygInitializeIt + window.onload;

					function weNothing() {
						return true;
					}

					function weWysiwygInitializeIt() {
						for (var i=0;i<we_wysiwygs.length;i++) {
							we_wysiwygs[i].start();
						}
						for (var i=0;i<we_wysiwygs.length;i++) {
							we_wysiwygs[i].finalize();
							we_wysiwygs[i].windowFocus();
							we_wysiwygs[i].setButtonsState();
						}
						self.focus();
						weWysiwygIsIntialized = true;
						weWysiwygSetHiddenTextSync();
					}

					function weWysiwygSetHiddenTextSync(){
						weWysiwygSetHiddenText(1);
						setTimeout(weWysiwygSetHiddenTextSync,500);
					}

					function weWysiwygSetHiddenText(arg) {
						try {
							if (weWysiwygIsIntialized) {
								for (var i = 0; i < we_wysiwygs.length; i++) {
									we_wysiwygs[i].setHiddenText(arg);
								}
							}else{
								}
						} catch(e) {
							// Nothing
						}
					}') .
					we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
					(we_base_browserDetect::isSafari() ? we_html_element::jsScript(WEBEDITION_DIR . 'editors/content/wysiwyg/weWysiwygSafari.js') .
						we_html_element::jsScript(JS_DIR . 'weDOM_Safari.js') : we_html_element::jsScript(WEBEDITION_DIR . 'editors/content/wysiwyg/weWysiwyg.js'));
		}
	}

	function getAllCmds(){
		$arr = array('formatblock',
			'fontname',
			'fontsize',
			'applystyle',
			'bold',
			'italic',
			'underline',
			'subscript',
			'superscript',
			'strikethrough',
			'removeformat',
			'removetags',
			'forecolor',
			'backcolor',
			'justifyleft',
			'justifycenter',
			'justifyright',
			'justifyfull',
			'insertunorderedlist',
			'insertorderedlist',
			'indent',
			'outdent',
			'createlink',
			'unlink',
			'anchor',
			'insertimage',
			'inserthorizontalrule',
			'insertspecialchar',
			'inserttable',
			'edittable',
			'editcell',
			'insertcolumnright',
			'insertcolumnleft',
			'insertrowabove',
			'insertrowbelow',
			'deletecol',
			'deleterow',
			'increasecolspan',
			'decreasecolspan',
			'caption',
			'removecaption',
			'importrtf',
			'fullscreen',
			'cut',
			'copy',
			'paste',
			'undo',
			'redo',
			'visibleborders',
			'editsource',
			'insertbreak',
			'acronym',
			'abbr',
			'lang'
		);

		// the following are tinyMCE only
		if(self::$editorType == 'tinyMCE'){
			array_push($arr, 'absolute', 'blockquote', 'cite', 'del', 'emotions', 'hr', 'ins', 'insertdate', 'insertlayer', 'inserttime', 'ltr', 'movebackward', 'moveforward', 'nonbreaking', 'pastetext', 'pasteword', 'replace', 'rtl', 'search', 'styleprops'
			);
		}

		if(defined('SPELLCHECKER')){
			$arr[] = "spellcheck";
		}
		return $arr;
	}

	function setToolbarElements(){// TODO: declare setToolbarElements
		$formatblockArr = we_base_browserDetect::isIE() ? array(
			"normal" => g_l('wysiwyg', "[normal]"),
			"p" => g_l('wysiwyg', "[paragraph]"),
			"h1" => g_l('wysiwyg', "[h1]"),
			"h2" => g_l('wysiwyg', "[h2]"),
			"h3" => g_l('wysiwyg', "[h3]"),
			"h4" => g_l('wysiwyg', "[h4]"),
			"h5" => g_l('wysiwyg', "[h5]"),
			"h6" => g_l('wysiwyg', "[h6]"),
			"pre" => g_l('wysiwyg', "[pre]"),
			"address" => g_l('wysiwyg', "[address]")
			) : (we_base_browserDetect::isSafari() ? array(
				"div" => g_l('wysiwyg', "[normal]"),
				"p" => g_l('wysiwyg', "[paragraph]"),
				"h1" => g_l('wysiwyg', "[h1]"),
				"h2" => g_l('wysiwyg', "[h2]"),
				"h3" => g_l('wysiwyg', "[h3]"),
				"h4" => g_l('wysiwyg', "[h4]"),
				"h5" => g_l('wysiwyg', "[h5]"),
				"h6" => g_l('wysiwyg', "[h6]"),
				"pre" => g_l('wysiwyg', "[pre]"),
				"address" => g_l('wysiwyg', "[address]"),
				"blockquote" => "blockquote"
				) : array(
				"normal" => g_l('wysiwyg', "[normal]"),
				"p" => g_l('wysiwyg', "[paragraph]"),
				"h1" => g_l('wysiwyg', "[h1]"),
				"h2" => g_l('wysiwyg', "[h2]"),
				"h3" => g_l('wysiwyg', "[h3]"),
				"h4" => g_l('wysiwyg', "[h4]"),
				"h5" => g_l('wysiwyg', "[h5]"),
				"h6" => g_l('wysiwyg', "[h6]"),
				"pre" => g_l('wysiwyg', "[pre]"),
				"address" => g_l('wysiwyg', "[address]"),
				"code" => "Code",
				//"cite" => "Cite",
				//"q" => "q",
				"blockquote" => "blockquote"
				));

		if(self::$editorType == 'tinyMCE'){
			$this->tinyFormatblock = implode(',', array_keys($formatblockArr));
		}

		$this->elements = array(
			new we_wysiwyg_ToolbarSelect(
				$this,
				"formatblock",
				g_l('wysiwyg', "[format]"),
				$formatblockArr,
				120
			),
			new we_wysiwyg_ToolbarSelect(
				$this,
				"fontname",
				g_l('wysiwyg', "[fontname]"),
				$this->fontnames,
				120
			),
			new we_wysiwyg_ToolbarSelect(
				$this,
				'fontsize',
				g_l('wysiwyg', '[fontsize]'),
				we_base_browserDetect::isSafari() ? array(
					'8px' => '8px',
					'9px' => '9px',
					'10px' => '10px',
					'11px' => '11px',
					'12px' => '12px',
					'13px' => '13px',
					'14px' => '14px',
					'15px' => '15px',
					'16px' => '16px',
					'17px' => '17px',
					'18px' => '18px',
					'19px' => '19px',
					'20px' => '20px',
					'21px' => '21px',
					'22px' => '22px',
					'24px' => '24px',
					'26px' => '26px',
					'28px' => '28px',
					'30px' => '30px',
					'36px' => '36px'
					) : array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
					7 => 7
					),
				120
			),
			new we_wysiwyg_ToolbarSelect(
				$this,
				"applystyle",
				g_l('wysiwyg', "[css_style]"),
				array(),
				120
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"bold",
				$this->_image_languagePath . "bold.gif",
				g_l('wysiwyg', "[bold]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"italic",
				$this->_image_languagePath . "italic.gif",
				g_l('wysiwyg', "[italic]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"underline",
				$this->_image_languagePath . "underline.gif",
				g_l('wysiwyg', "[underline]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"subscript",
				$this->_imagePath . "subscript.gif",
				g_l('wysiwyg', "[subscript]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"superscript",
				$this->_imagePath . "superscript.gif",
				g_l('wysiwyg', "[superscript]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"strikethrough",
				$this->_imagePath . "strikethrough.gif",
				g_l('wysiwyg', "[strikethrough]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"removeformat",
				$this->_imagePath . "removeformat.gif",
				g_l('wysiwyg', "[removeformat]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"removetags",
				$this->_imagePath . "removetags.gif",
				g_l('wysiwyg', "[removetags]")
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"cite",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"acronym",
				$this->_image_languagePath . "acronym.gif",
				g_l('wysiwyg', "[acronym]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"abbr",
				$this->_image_languagePath . "abbr.gif",
				g_l('wysiwyg', "[abbr]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"lang",
				$this->_imagePath . "lang.gif",
				g_l('wysiwyg', "[language]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"styleprops",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"del",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"ins",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"forecolor",
				$this->_imagePath . "setforecolor.gif",
				g_l('wysiwyg', "[fore_color]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"backcolor",
				$this->_imagePath . "setbackcolor.gif",
				g_l('wysiwyg', "[back_color]")
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"justifyleft",
				$this->_imagePath . "justifyleft.gif",
				g_l('wysiwyg', "[justify_left]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"justifycenter",
				$this->_imagePath . "justifycenter.gif",
				g_l('wysiwyg', "[justify_center]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"justifyright",
				$this->_imagePath . "justifyright.gif",
				g_l('wysiwyg', "[justify_right]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"justifyfull",
				$this->_imagePath . "justifyfull.gif",
				g_l('wysiwyg', "[justify_full]")
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertunorderedlist",
				$this->_imagePath . "unorderlist.gif",
				g_l('wysiwyg', "[unordered_list]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertorderedlist",
				$this->_imagePath . "orderlist.gif",
				g_l('wysiwyg', "[ordered_list]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"indent",
				$this->_imagePath . "indent.gif",
				g_l('wysiwyg', "[indent]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"outdent",
				$this->_imagePath . "outdent.gif",
				g_l('wysiwyg', "[outdent]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"blockquote",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"createlink",
				$this->_imagePath . "hyperlink.gif",
				g_l('wysiwyg', "[hyperlink]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"unlink",
				$this->_imagePath . "unlink.gif",
				g_l('wysiwyg', "[unlink]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"anchor",
				$this->_imagePath . "anchor.gif",
				g_l('wysiwyg', "[insert_edit_anchor]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertimage",
				$this->_imagePath . "image.gif",
				g_l('wysiwyg', "[insert_edit_image]")
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertdate",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"inserttime",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"hr",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"inserthorizontalrule",
				$this->_imagePath . "rule.gif",
				g_l('wysiwyg', "[inserthorizontalrule]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertspecialchar",
				$this->_imagePath . "specialchar.gif",
				g_l('wysiwyg', "[insertspecialchar]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"emotions",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"nonbreaking",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertbreak",
				$this->_imagePath . "br.gif",
				g_l('wysiwyg', "[insert_br]")
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"ltr",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"rtl",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"inserttable",
				$this->_imagePath . "inserttable.gif",
				g_l('wysiwyg', "[inserttable]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"edittable",
				$this->_imagePath . "edittable.gif",
				g_l('wysiwyg', "[edittable]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"editcell",
				$this->_imagePath . "editcell.gif",
				g_l('wysiwyg', "[editcell]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"editrow",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			//new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertcolumnleft",
				$this->_imagePath . "insertcol_left.gif",
				g_l('wysiwyg', "[insertcolumnleft]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertcolumnright",
				$this->_imagePath . "insertcol_right.gif",
				g_l('wysiwyg', "[insertcolumnright]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertrowabove",
				$this->_imagePath . "insertrow_above.gif",
				g_l('wysiwyg', "[insertrowabove]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"insertrowbelow",
				$this->_imagePath . "insertrow_below.gif",
				g_l('wysiwyg', "[insertrowbelow]")
			),
			//new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"deletecol",
				$this->_imagePath . "deletecols.gif",
				g_l('wysiwyg', "[deletecol]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"deleterow",
				$this->_imagePath . "deleterows.gif",
				g_l('wysiwyg', "[deleterow]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"deletetable",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			//new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"increasecolspan",
				$this->_imagePath . "inc_col.gif",
				g_l('wysiwyg', "[increasecolspan]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"decreasecolspan",
				$this->_imagePath . "dec_col.gif",
				g_l('wysiwyg', "[decreasecolspan]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"caption",
				$this->_imagePath . "caption.gif",
				g_l('wysiwyg', "[addcaption]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"removecaption",
				$this->_imagePath . "removecaption.gif",
				g_l('wysiwyg', "[removecaption]")
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"importrtf",
				$this->_imagePath . "rtf.gif",
				g_l('wysiwyg', "[rtf_import]")
			),
			new we_wysiwyg_ToolbarSeparator($this),
			new we_wysiwyg_ToolbarButton(
				$this,
				"selectall",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"cut",
				$this->_imagePath . "cut.gif",
				g_l('wysiwyg', "[cut]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"copy",
				$this->_imagePath . "copy.gif",
				g_l('wysiwyg', "[copy]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"paste",
				$this->_imagePath . "paste.gif",
				g_l('wysiwyg', "[paste]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"pastetext",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"pasteword",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this), new we_wysiwyg_ToolbarButton(
				$this,
				"search",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"replace",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this), new we_wysiwyg_ToolbarButton(
				$this,
				"insertlayer",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"movebackward",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"moveforward",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"absolute",
				"", // tinyMCE only: we do not need icon or tooltip
				""
			),
			new we_wysiwyg_ToolbarSeparator($this), new we_wysiwyg_ToolbarButton(
				$this,
				"undo",
				$this->_imagePath . "undo.gif",
				g_l('wysiwyg', "[undo]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"redo",
				$this->_imagePath . "redo.gif",
				g_l('wysiwyg', "[redo]")
			),
			new we_wysiwyg_ToolbarSeparator($this), new we_wysiwyg_ToolbarButton(
				$this,
				"visibleborders",
				$this->_imagePath . "visibleborders.gif",
				g_l('wysiwyg', "[visible_borders]")
			),
			new we_wysiwyg_ToolbarButton(
				$this,
				"editsource",
				$this->_imagePath . "editsourcecode.gif",
				g_l('wysiwyg', "[edit_sourcecode]")
			)
		);
		if(defined('SPELLCHECKER') && $this->showSpell){
			$this->elements[] = new we_wysiwyg_ToolbarButton(
					$this,
					'spellcheck',
					$this->_imagePath . 'spellcheck.gif',
					g_l('wysiwyg', '[spellcheck]')
			);
		}
		if(!$this->fullscreen){
			$this->elements[] = new we_wysiwyg_ToolbarButton(
					$this,
					"fullscreen",
					$this->_imagePath . "fullscreen.gif",
					g_l('wysiwyg', "[fullscreen]")
			);
		}
	}

	function getWidthOfElem($startPos, $end){
		$w = 0;
		for($i = $startPos; $i <= $end; $i++){
			$w += $this->filteredElements[$i]->width;
		}
		return $w;
	}

	function setFilteredElements(){
		$lastSep = true;
		foreach($this->elements as $i => $elem){
			if($elem->showMe){
				if((!$lastSep) || ($elem->classname != "we_wysiwyg_ToolbarSeparator")){
					array_push($this->filteredElements, $elem);
				}
				$lastSep = ($elem->classname == "we_wysiwyg_ToolbarSeparator");
			}
		}
		if(!empty($this->filteredElements)){
			if($this->filteredElements[count($this->filteredElements) - 1]->classname == "we_wysiwyg_ToolbarSeparator"){
				array_pop($this->filteredElements);
			}
		}
	}

	function hasSep($rowArr){
		foreach($rowArr as $i => $elem){
			if($elem->classname == "we_wysiwyg_ToolbarSeparator")
				return true;
		}
		return false;
	}

	function getEditButtonHTML(){
		list($tbwidth, $tbheight) = $this->getToolbarWidthAndHeight();
		$tbheight += self::$editorType == 'tinyMCE' ? 18 : 0;
		$fns = '';
		foreach($this->fontnames as $fn){
			$fns .= str_replace(",", ";", $fn) . ",";
		}
		return we_button::create_button("image:btn_edit_edit", "javascript:we_cmd('open_wysiwyg_window', '" . $this->name . "', '" . max(220, $this->width) . "', '" . $this->height . "','" . $GLOBALS["we_transaction"] . "','" . $this->propstring . "','" . $this->className . "','" . rtrim($fns, ',') . "','" . $this->outsideWE . "','" . $tbwidth . "','" . $tbheight . "','" . $this->xml . "','" . $this->removeFirstParagraph . "','" . $this->bgcol . "','" . $this->baseHref . "','" . $this->charset . "','" . $this->cssClassesCSV . "','" . $this->Language . "','" . we_cmd_enc($this->contentCss) . "','" . $this->origName . "','" . we_cmd_enc($this->tinyParams) . "');", true, 25);
	}

	function getHTML(){
		return ($this->inlineedit ? $this->getInlineHTML() : $this->getEditButtonHTML());
	}

	function getToolbarRows(){
		$tmpElements = $this->filteredElements;
		$rows = array();
		$rownr = 0;
		$rows[$rownr] = array();
		$rowwidth = 0;
		while(!empty($tmpElements)) {
			if(!$this->hasSep($rows[$rownr]) || $rowwidth <= max($this->width, $this->maxGroupWidth)){
				array_push($rows[$rownr], array_shift($tmpElements));
				$rowwidth += $rows[$rownr][count($rows[$rownr]) - 1]->width;
			} else{
				if(!empty($rows[$rownr])){
					if($rows[$rownr][count($rows[$rownr]) - 1]->classname == "we_wysiwyg_ToolbarSeparator"){
						array_pop($rows[$rownr]);
						$rownr++;
						$rowwidth = 0;
						$rows[$rownr] = array();
					} else{
						while($tmpElements[0]->classname != "we_wysiwyg_ToolbarSeparator") {
							array_unshift($tmpElements, array_pop($rows[$rownr]));
						}
						array_shift($tmpElements);
						$rownr++;
						$rowwidth = 0;
						$rows[$rownr] = array();
					}
				}
			}
		}
		return $rows;
	}

	function getToolbarWidthAndHeight(){

		$rows = $this->getToolbarRows();
		$toolbarheight = 0;
		$min_w = 0;
		$row_w = 0;
		foreach($rows as $curRow){
			$rowheight = 0;
			foreach($curRow as $curCol){
				$rowheight = max($rowheight, $curCol->height);
				$row_w += $curCol->width;
			}
			$toolbarheight += ($rowheight + 2);
			$min_w = max($min_w, $row_w);
			$row_w = 0;
		}

		$realWidth = max($min_w, $this->width);
		return array($realWidth, $toolbarheight);
	}

	function getInlineHTML(){
		$rows = $this->getToolbarRows();
		$editValue = $this->value;
		$regs = array();
		if(preg_match_all('/src="document:(\\d+)/i', $editValue, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[1]), 'Path', $GLOBALS['DB_WE']);
				$editValue = str_ireplace('src="document:' . $reg[1], 'src="' . $path . "?id=" . $reg[1], $editValue);
			}
		}
		if(preg_match_all('/src="thumbnail:([^" ]+)/i', $editValue, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				list($imgID, $thumbID) = explode(',', $reg[1]);
				$thumbObj = new we_thumbnail();
				$thumbObj->initByImageIDAndThumbID($imgID, $thumbID);
				$editValue = str_ireplace('src="thumbnail:' . $reg[1], 'src="' . $thumbObj->getOutputPath() . "?thumb=" . $reg[1], $editValue);
				unset($thumbObj);
			}
		}

		switch(self::$editorType){
			case 'tinyMCE':
				$this->width = $this->width + 20; //imi
				list($lang) = explode('_', $GLOBALS["weDefaultFrontendLanguage"]);

				$cmdMapping = array(
					'abbr' => 'weabbr',
					'acronym' => 'weacronym',
					'anchor' => 'anchor',
					'applystyle' => 'styleselect',
					'backcolor' => 'backcolor',
					'bold' => 'bold',
					'copy' => 'copy',
					'createlink' => 'welink',
					'cut' => 'cut',
					'decreasecolspan' => 'split_cells',
					'deletecol' => 'delete_col',
					'deleterow' => 'delete_row',
					'editcell' => 'cell_props',
					'editsource' => 'code',
					'fontname' => 'fontselect',
					'fontsize' => 'fontsizeselect',
					'forecolor' => 'forecolor',
					'formatblock' => 'formatselect',
					'fullscreen' => 'wefullscreen',
					'increasecolspan' => 'merge_cells',
					'indent' => 'indent',
					'insertbreak' => 'weinsertbreak',
					'insertcolumnleft' => 'col_before ',
					'insertcolumnright' => 'col_after',
					'inserthorizontalrule' => 'advhr',
					'insertimage' => 'weimage',
					'insertorderedlist' => 'numlist',
					'insertrowabove' => 'row_before',
					'insertrowbelow' => 'row_after',
					'insertspecialchar' => 'charmap',
					'inserttable' => 'table',
					'insertunorderedlist' => 'bullist',
					'italic' => 'italic',
					'justifycenter' => 'justifycenter',
					'justifyfull' => 'justifyfull',
					'justifyleft' => 'justifyleft',
					'justifyright' => 'justifyright',
					'lang' => 'welang',
					'outdent' => 'outdent',
					'paste' => 'paste',
					'redo' => 'redo',
					'removeformat' => 'removeformat',
					'removetags' => 'cleanup',
					'spellcheck' => 'wespellchecker',
					'strikethrough' => 'strikethrough',
					'subscript' => 'sub',
					'superscript' => 'sup',
					'underline' => 'underline',
					'undo' => 'undo',
					'unlink' => 'unlink',
					'visibleborders' => 'wevisualaid',
					// the following commands exist only in tinyMCE
					'absolute' => 'absolute',
					'blockquote' => 'blockquote',
					'cite' => 'cite',
					'del' => 'del',
					'deletetable' => 'delete_table',
					'editrow' => 'row_props',
					'emotions' => 'emotions',
					'hr' => 'hr',
					'ins' => 'ins',
					'insertdate' => 'insertdate',
					'insertlayer' => 'insertlayer',
					'inserttime' => 'inserttime',
					'ltr' => 'ltr',
					'movebackward' => 'movebackward',
					'moveforward' => 'moveforward',
					'nonbreaking' => 'nonbreaking',
					'pastetext' => 'pastetext',
					'pasteword' => 'pasteword',
					'replace' => 'replace',
					'rtl' => 'rtl',
					'search' => 'search',
					'selectall' => 'selectall',
					'styleprops' => 'styleprops',
					// table controlls are not mapped from wysiwyg to tinyMCE:
					//'notmapped1' => 'attribs',
					//'notmapped2' => 'insertimage', // replaced by weimage
					//'notmapped3' => 'insertfile',
					//'notmapped4' => 'preview', // will not be implemented: we should only use we-preview
					//'notmapped5' => 'media',
					//'notmapped6' => 'visualchars', //seems not to work
					//'notmapped7' => 'iespell',
					//'notmapped8' => 'pagebreak',
					//'notmapped9' => 'template',
				);

				//write theme_advanced_buttons_X
				$tinyRows = '';
				$i = 0;
				$k = 1;
				$pastetext = 0;
				foreach($rows as $outer){
					$tinyRows .= 'theme_advanced_buttons' . $k . ' : "';
					$j = 0;
					foreach($outer as $inner){
						//if($cmdMapping[$rows[$i][$j]->cmd] == 'pastetext'){ // TODO: implement pastetext-toggle in we:textarea and throw this out again
						//$pastetext = 1;
						//}
						$tinyRows .= $rows[$i][$j]->cmd == '' ? 'separator,' : ($cmdMapping[$rows[$i][$j]->cmd] != '--' ? $cmdMapping[$rows[$i][$j]->cmd] . ',' : '');
						$j++;
					}
					$tinyRows = substr($tinyRows, 0, -1) . '",';
					$i++;
					$k++;
				}

				//very fast fix for textarea-height. TODO, when wysiwyg is thrown out: use or rewrite existing methods like getToolbarWithAndHeight()
				$toolBarHeight = $k * 24 - 10;
				$this->height += $toolBarHeight;

				$tinyRows .= 'theme_advanced_buttons' . $k . ' : "",';
				//function openWeFileBrowser(): not needed anymore: imi

				if(preg_match('/^#[a-f0-9]{6}$/i', $this->bgcol)){
					$this->bgcol = substr($this->bgcol, 1);
				} else if(!preg_match('/^[a-f0-9]{6}$/i', $this->bgcol) && !preg_match('/^[a-z]*$/i', $this->bgcol)){
					$this->bgcol = '';
				}

				$wefullscreenVars = array(
					'outsideWE' => $this->outsideWE ? "1" : "",
					'xml' => $this->xml ? "1" : "",
					'removeFirstParagraph' => $this->removeFirstParagraph ? "1" : "",
				);

				$contentCss = empty($this->contentCss) ? '' : $this->contentCss . ',';

				return we_html_element::jsElement('
					var weclassNames_tinyMce = new Array (' . $this->cssClassesJS . ');

					tinyMCE.init({
						weFullscrenParams : {
							"outsideWE" : "' . $wefullscreenVars['outsideWE'] . '",
							"xml" : "' . $wefullscreenVars['xml'] . '",
							"removeFirstParagraph" : "' . $wefullscreenVars['removeFirstParagraph'] . '",
							"baseHref" : "' . urlencode($this->baseHref) . '",
							"charset" : "' . $this->charset . '",
							"cssClasses" : "' . urlencode($this->cssClasses) . '",
							"fontnames" : "' . urlencode($this->fontnamesCSV) . '",
							"bgcolor" : "' . $this->bgcol . '",
							"language" : "' . $this->Language . '",
							"screenWidth" : screen.availWidth-10,
							"screenHeight" : screen.availHeight - 70,
							"className" : "' . $this->className . '",
							"propString" : "' . urlencode($this->propstring) . '",
							"contentCss" : "' . urlencode($this->contentCss) . '",
							"origName" : "' . urlencode($this->origName) . '",
							"tinyParams" : "' . urlencode($this->tinyParams) . '"
						},
						weClassNames_urlEncoded : "' . urlencode($this->cssClassesCSV) . '",

						language : "' . $lang . '",
						mode : "exact",
						elements : "' . $this->name . '",
						theme : "advanced",
						//dialog_type : "modal",

						accessibility_warnings : false,
						relative_urls : false, //important!
						convert_urls : false, //important!
						//force_br_newlines : true,
						force_p_newlines : 0, // value 0 instead of true (!) prevents adding additional lines with <p>&nbsp</p> when inlineedit="true"
						//forced_root_block : "",

						entity_encoding : "named",
						entities : "160,nbsp",
						element_format: "' . $this->xml . '",
						body_class : "' . ($this->className != "" ? $this->className . " " : "") . 'wetextarea tiny-wetextarea wetextarea-' . $this->origName . '",

						//CallBacks
						//file_browser_callback : "openWeFileBrowser",
						//onchange_callback : "tinyMCEchanged",

						plugins : "style,table,advhr,weimage,welink,emotions,insertdatetime,preview,searchreplace,contextmenu,paste,directionality,nonbreaking,xhtmlxtras,weabbr,weacronym,welang,wevisualaid,weinsertbreak,wespellchecker,layer,autolink,wefullscreen,lists,autolink,advlist",

						// Theme options
						' . $tinyRows . '
						theme_advanced_toolbar_location : "' . $this->buttonpos . '", //external: toolbar floating on top of textarea
						theme_advanced_fonts: "' . $this->tinyFonts . '",
						theme_advanced_styles: "' . $this->cssClasses . '",
						theme_advanced_blockformats : "' . $this->tinyFormatblock . '",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "' . $this->statuspos . '",
						theme_advanced_resizing : false,
						theme_advanced_source_editor_height : "500",
						theme_advanced_source_editor_width : "700",
						theme_advanced_default_foreground_color : "#FF0000",
						theme_advanced_default_background_color : "#FFFF99",
						plugin_preview_height : "300",
						plugin_preview_width : "500",
						theme_advanced_disable : "",
						//paste_text_use_dialog: true,
						//fullscreen_new_window: true,
						content_css : "' . WEBEDITION_DIR . 'editors/content/tinymce/we_tinymce/contentCssFirst.php?' . time() . '=,' . $contentCss . WEBEDITION_DIR . 'editors/content/tinymce/we_tinymce/contentCssLast.php?' . time() . '=&tinyMceBackgroundColor=' . $this->bgcol . '",
						popup_css_add : "' . WEBEDITION_DIR . 'editors/content/tinymce/we_tinymce/tinyDialogCss.php",

						// Skin options
						skin : "o2k7",
						skin_variant : "silver",

						' . ($this->tinyParams != '' ? '//paramas from attribute tinyparams
						' . $this->tinyParams . ',' : '') . '

						setup : function(ed){
							ed.onInit.add(function(ed){
								ed.pasteAsPlainText = ' . $pastetext . ';
								ed.controlManager.setActive("pastetext", ' . $pastetext . ');
							});

							'
						. (!$this->removeFirstParagraph ? '' : '
							ed.onPostProcess.add(function(ed, o) {
								o.content = o.content.replace(/<p [^>]*>|<p>/, "").replace(/<\/p>/, "");
							});') .

							($this->isFrontendEdit ? '' : '
							/* set EditorFrame.setEditorIsHot(true) */

							// we look for editorLevel and weEditorFrameController just once at editor init
							var editorLevel = "";
							var weEditorFrame = null;

							if(typeof(_EditorFrame) != "undefined"){
								editorLevel = "inline";
								weEditorFrame = _EditorFrame;
							} else {
								if(top.opener != null && typeof(top.opener.top.weEditorFrameController) != "undefined" && typeof(top.isWeDialog) == "undefined"){
									editorLevel = "popup";
									weEditorFrame = top.opener.top.weEditorFrameController;
								} else {
									editorLevel = "fullscreen";
									weEditorFrame = null;
								}
							}

							// if editorLevel = "inline" we use a local copy of weEditorFrame.EditorIsHot
							var weEditorFrameIsHot = false;
							try{
								weEditorFrameIsHot = editorLevel == "inline" ? weEditorFrame.EditorIsHot : false;
							}catch(e){}

							// listeners for editorLevel = "inline"
							//could be rather CPU-intensive. But weEditorFrameIsHot is nearly allways true, so we could try
							/*
							ed.onKeyDown.add(function(ed) {
								if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
									try{
										weEditorFrame.setEditorIsHot(true);
									} catch(e) {}
									weEditorFrameIsHot = true;
								}
							});
							*/

							ed.onChange.add(function(ed) {
								if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
									try{
										weEditorFrame.setEditorIsHot(true);
									} catch(e) {}
									weEditorFrameIsHot = true;
								}
							});

							ed.onClick.add(function(ed) {
								if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
									try{
										weEditorFrame.setEditorIsHot(true);
									} catch(e) {}
									weEditorFrameIsHot = true;
								}
							});

							ed.onPaste.add(function(ed) {
								if(!weEditorFrameIsHot && editorLevel == "inline" && ed.isDirty()){
									try{
										weEditorFrame.setEditorIsHot(true);
									} catch(e) {}
									weEditorFrameIsHot = true;
								}
							});

							// onSave (= we_save and we_publish) we reset the (tiny-internal) flag weEditorFrameIsHot to false
							ed.onSaveContent.add(function(ed) {
								weEditorFrameIsHot = false;
								// if is popup and we click on ok
								if(editorLevel == "popup" && ed.isDirty()){
									try{
										weEditorFrame.setEditorIsHot(true);
									} catch(e) {}
								}
							});
							') . '
						}
					});') .
					'
<textarea wrap="off" style="color:#eeeeee; background-color:#eeeeee;  width:' . $this->width . 'px; height:' . $this->height . 'px;" id="' . $this->name . '" name="' . $this->name . '">' . str_replace(array('\n', '&'), array('', '&amp;'), $editValue) . '</textarea>';

			case 'default':

//parseInternalLinks($editValue,0);

				$min_w = 0;
				$row_w = 0;
				$pixelrow = '<tr><td background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" class="tbButtonWysiwygDefaultStyle tbButtonWysiwygBackground">' . we_html_tools::getPixel($this->width, 2) . '</td></tr>';
				$linerow = '<tr><td ><div class="tbButtonsHR" class="tbButtonWysiwygDefaultStyle"></div></td></tr>';
				$out = we_html_element::jsElement('var weLastPopupMenu = null; var wefoo = "' . $this->ref . 'edit"; wePopupMenuArray[wefoo] = new Array();') . '<table id="' . $this->ref . 'edit_table" border="0" cellpadding="0" cellspacing="0" width="' . $this->width . '" class="tbButtonWysiwygDefaultStyle"><tr><td  background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" class="tbButtonWysiwygDefaultStyle tbButtonWysiwygBackground">';
				foreach($rows as $r=>$curRow){
					$out .= '<table border="0" cellpadding="0" cellspacing="0" class="tbButtonWysiwygDefaultStyle"><tr>';
					foreach($curRow as $curCol){
						$out .= '<td class="tbButtonWysiwygDefaultStyle">' . $curCol->getHTML() . '</td>';
						$row_w += $curCol->width;
					}
					$min_w = max($min_w, $row_w);
					$row_w = 0;
					$out .= '</tr></table></td></tr>' . (($r < count($rows) - 1) ? $linerow : $pixelrow) . '<tr><td ' . (($r < (count($rows) - 1)) ? (' bgcolor="white"  background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif"') : '') . ' class="tbButtonWysiwygDefaultStyle' . (($r < (count($rows) - 1)) ? ' tbButtonWysiwygBackground' : '') . '">';
				}

				$realWidth = max($min_w, $this->width);
				$out .= '<table border="0" cellpadding="0" cellspacing="0"  class="tbButtonWysiwygDefaultStyle"><tr><td class="tbButtonWysiwygDefaultStyle"><textarea wrap="off" style="color:black; display: none;font-family: courier; font-size: 10pt; width:' . $realWidth . 'px; height:' . $this->height . 'px;" id="' . $this->ref . 'edit_src" name="' . $this->ref . 'edit_src"></textarea><iframe contenteditable  width="' . $realWidth . 'px" height="' . $this->height . 'px" name="' . $this->ref . 'edit" id="' . $this->ref . 'edit" allowTransparency="true" ' .
					'style="display: block;color: black;border: 1px solid #A5ACB2;' .
					(we_base_browserDetect::isSafari() ? '-khtml-user-select:none;"  src="' . WEBEDITION_DIR . 'editors/content/wysiwyg/empty.html"' : '"') .
					'></iframe></td></tr>
</table></td></tr></table><input type="hidden" id="' . $this->name . '" name="' . $this->name . '" value="' . oldHtmlspecialchars($this->hiddenValue) . '" /><div id="' . $this->ref . 'edit_buffer" style="display: none;"></div>
' . we_html_element::jsElement('
var ' . $this->ref . 'Obj = null;
' . $this->ref . 'Obj = new weWysiwyg("' . $this->ref . 'edit","' . $this->name . '","' . str_replace("\"", "\\\"", $this->value) . '","' . str_replace("\"", "\\\"", $editValue) . '",\'' . $this->fullscreen . '\',\'' . $this->className . '\',\'' . $this->propstring . '\',\'' . $this->bgcol . '\',' . ($this->outsideWE ? "true" : "false") . ',"' . $this->baseHref . '","' . $this->xml . '","' . $this->removeFirstParagraph . '","' . $this->charset . '","' . $this->cssClasses . '","' . $this->Language . '", "' . ($this->isFrontendEdit ? 1 : 0) . '");
we_wysiwygs[we_wysiwygs.length] = ' . $this->ref . 'Obj;

function ' . $this->ref . 'editShowContextMenu(event){
	return ' . $this->ref . 'Obj.showContextMenu(event);
}
function ' . $this->ref . 'editonkeydown(){
	return we_on_key_down(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonkeyup(){
	return we_on_key_up(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonmouseup(){
	return we_on_mouse_up(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonfocus(){
	return we_on_focus(' . $this->ref . 'Obj);
}
function ' . $this->ref . 'editonblur(){
	return we_on_blur(' . $this->ref . 'Obj);
}');
				return $out;
		}
	}

}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5964 $
 * $Author: mokraemer $
 * $Date: 2013-03-15 12:41:02 +0100 (Fri, 15 Mar 2013) $
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
define("EXPORT_PATH", WE_INCLUDES_DIR . "we_export/");

$yuiSuggest = & weSuggest::getInstance();

class weExportWizard{

	var $frameset;
	var $db;
	var $Tree;
	var $topFrame = "top";
	var $headerFrame = "top.header";
	var $loadFrame = "top.load";
	var $bodyFrame = "top.body";
	var $footerFrame = "top.footer";
	var $exportVars;

	function __construct($frameset = ""){
		$this->setFrameset($frameset);
		$this->db = new DB_WE();

		$this->exportVars = array(
			"extype" => "",
			"selection" => "auto",
			"type" => "doctype",
			"doctype" => "",
			"classname" => "",
			"dir" => 0,
			"art" => "docs",
			"categories" => "",
			"selDocs" => "",
			"selTempl" => "",
			"selObjs" => "",
			"selClasses" => "",
			"finalDocs" => array(),
			"finalObjs" => array(),
			"file_name" => "",
			"export_to" => "local",
			"path" => "",
			"filename" => "",
			"csv_delimiter" => ';',
			"csv_enclose" => '"',
			"csv_lineend" => "windows",
			"csv_fieldnames" => "",
			"csv_fields" => '0',
			"cdata" => "true",
			"RefTable" => array(),
			"CurrentRef" => 0,
			"step" => 0,
			"handle_def_templates" => 0,
			"handle_document_includes" => 0,
			"handle_document_linked" => 0,
			"handle_def_classes" => 0,
			"handle_object_includes" => 0,
			"handle_object_linked" => 0,
			"handle_object_embeds" => 0,
			"handle_class_defs" => 0,
			"handle_doctypes" => 0,
			"handle_categorys" => 0,
			"export_depth" => 1
		);


		if(isset($_SESSION['weS']['exportVars'])){
			foreach($this->exportVars as $k => $v){
				if(isset($_SESSION['weS']['exportVars'][$k]))
					$this->exportVars[$k] = $_SESSION['weS']['exportVars'][$k];
				else
					$_SESSION['weS']['exportVars'][$k] = $v;
			}
		}
		else{
			$_SESSION['weS']['exportVars'] = $this->exportVars;
		}
	}

	function setFrameset($frameset){
		$this->frameset = $frameset;
	}

	function getJSTop(){
		return we_html_element::jsElement('
 			var table="' . FILE_TABLE . '";
 		');
	}

	function getExportVars(){
		if(isset($_SESSION['weS']['exportVars'])){
			$this->exportVars = $_SESSION['weS']['exportVars'];
		}
		foreach($this->exportVars as $k => $v){
			$var = isset($_REQUEST[$k]) ? $_REQUEST[$k] : null;
			if($var !== null){
				$this->exportVars[$k] = $var;
			}
		}
		$_SESSION['weS']['exportVars'] = $this->exportVars;
	}

	function getHTMLFrameset(){
		$args = "";
		$_SESSION['weS']['exportVars'] = array();
		if(isset($_REQUEST['we_cmd'][1]))
			$args .= "&we_cmd[1]=" . $_REQUEST['we_cmd'][1];
		$this->Tree = new weExportTree(WE_INCLUDES_DIR . "we_export/export_frameset.php", $this->topFrame, $this->bodyFrame, $this->loadFrame);

		$js = $this->getJSTop();
		$js.=$this->Tree->getJSTreeCode();

		$js.=we_html_element::jsElement('

    		var step = 0;

			var activetab=0;
			var selection="auto";

			var extype="wxml";
			var type="doctype";
			var categories="";
			var doctype="";
			var classname="";
			var dir="";

			var file_format="gxml";
			var filename="";
			var export_to="server";
			var path="/";

			var SelectedItems= new Array();
			SelectedItems["' . FILE_TABLE . '"]=new Array();' .
				(defined("OBJECT_FILES_TABLE") ? (
					'SelectedItems["' . OBJECT_FILES_TABLE . '"]=new Array();
				SelectedItems["' . OBJECT_TABLE . '"]=new Array();
				') : '') . '

			SelectedItems["' . TEMPLATES_TABLE . '"]=new Array();

			var openFolders= new Array();
			openFolders["' . FILE_TABLE . '"]="";' .
				(defined("OBJECT_FILES_TABLE") ? ('
			openFolders["' . OBJECT_FILES_TABLE . '"]="";
			openFolders["' . OBJECT_TABLE . '"]="";
			') : '') . '
			openFolders["' . TEMPLATES_TABLE . '"]="";

		');

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => ((isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0) ? "1,*,45,60" : "1,*,45,0" ), "onLoad" => $this->bodyFrame . ".location='" . $this->frameset . "?pnt=body" . $args . "&step=' + step;"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=header", "name" => "header", "scrolling" => "no", "noresize" => null));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=body" . $args, "name" => "body", "scrolling" => "auto", "noresize" => null));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=footer", "name" => "footer", "scrolling" => "no"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=load", "name" => "load", "scrolling" => "no", "noresize" => null));

		$head = we_html_tools::getHtmlInnerHead(g_l('export', '[title]')) . STYLESHEET . $js;
		$body = $frameset->getHtml() . "\n" . $noframeset->getHTML();

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				$body
		);
	}

	function getHTMLStep($step = 0){
		$this->getExportVars();
		$function = "getHTMLStep" . $step;
		return $this->$function();
	}

	function getHTMLStep0(){
		$wexpotEnabled = (we_hasPerm('NEW_EXPORT') || we_hasPerm('DELETE_EXPORT') || we_hasPerm('EDIT_EXPORT') || we_hasPerm('MAKE_EXPORT') || we_hasPerm('ADMINISTRATOR'));

		$extype = $this->exportVars["extype"];

		if(!$extype){
			$extype = "wxml";
			if(!$wexpotEnabled){
				$extype = "gxml";
				if(!we_hasPerm("GENERICXML_EXPORT")){
					$extype = "csv";
					if(!we_hasPerm("CSV_EXPORT")){
						$extype = "";
					}
				}
			}
		}


		$js = we_html_element::jsElement('
					' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=footer&step=0";
					' . $this->headerFrame . '.location="' . $this->frameset . '?pnt=header&step=0";
					self.focus();
		');

		$parts = array();

		/* 		array_push($parts, array(
		  "headline"	=> g_l('export',"[we_export]"),
		  "html"		=> we_forms::radiobutton("wxml",($extype=="wxml" && we_hasPerm("WXML_EXPORT")), "extype", g_l('export',"[wxml_export]"),true, "defaultfont", "",  !we_hasPerm("WXML_EXPORT"), g_l('export',"[txt_wxml_export]"), 0, 384),
		  "space"		=> 120,
		  "noline"	=> 1)
		  ); */

		array_push($parts, array(
			"html" => we_forms::radiobutton("wxml", ($extype == "wxml" && $wexpotEnabled), "extype", g_l('export', "[wxml_export]"), true, "defaultfont", "", !$wexpotEnabled, g_l('export', "[txt_wxml_export]"), 0, 500),
			"space" => 0,
			"noline" => 1)
		);


		array_push($parts, array(
			"html" => we_forms::radiobutton("gxml", ($extype == "gxml" && we_hasPerm("GENERICXML_EXPORT")), "extype", g_l('export', "[gxml_export]"), true, "defaultfont", "", !we_hasPerm("GENERICXML_EXPORT"), g_l('export', "[txt_gxml_export]"), 0, 500),
			"space" => 0,
			"noline" => 1)
		);

		if(in_array("object", $GLOBALS['_we_active_integrated_modules'])){
			array_push($parts, array(
				"html" => we_forms::radiobutton("csv", ($extype == "csv" && we_hasPerm("CSV_EXPORT")), "extype", g_l('export', "[csv_export]"), true, "defaultfont", "", !we_hasPerm("CSV_EXPORT"), g_l('export', "[txt_csv_export]"), 0, 500),
				"space" => 0,
				"noline" => 1)
			);
		}


		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(//FIXME: missing title
					we_html_tools::getHtmlInnerHead() . STYLESHEET . $js) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "pnt", "value" => "body")) .
						we_html_element::htmlHidden(array("name" => "step", "value" => "1")) .
						we_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('export', "[title]"))
					)
				)
		);
	}

	function getHTMLStep1(){
		$extype = $this->exportVars["extype"];

		if($extype == "wxml"){
			return we_html_element::jsElement('
top.opener.top.we_cmd("edit_export_ifthere");
top.close();');
			exit();
		}


		$js = we_html_element::jsElement('
					' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=footer&step=1";
					' . $this->headerFrame . '.location="' . $this->frameset . '?pnt=header&step=1";
					self.focus();

					function we_submit(){
						' . ($this->exportVars["extype"] == "csv" ? '
						if(document.we_form.selection[1].checked){
							document.we_form.step.value=3;
						}
						' : '') . '
						document.we_form.submit();
					}

			');

		$selection = $this->exportVars["selection"];

		$parts = array();

		array_push($parts, array(
			"html" => we_forms::radiobutton("auto", ($selection == "auto" ? true : false), "selection", g_l('export', "[auto_selection]"), true, "defaultfont", "", false, (($this->exportVars["extype"] == "csv") ? g_l('export', "[txt_auto_selection_csv]") : g_l('export', "[txt_auto_selection]")), 0, 500),
			"space" => 0,
			"noline" => 1)
		);

		array_push($parts, array(
			"html" => we_forms::radiobutton("manual", ($selection == "manual" ? true : false), "selection", g_l('export', "[manual_selection]"), true, "defaultfont", "", false, (($this->exportVars["extype"] == "csv") ? g_l('export', "[txt_manual_selection_csv]") : g_l('export', "[txt_manual_selection]")), 0, 500),
			"space" => 0,
			"noline" => 1)
		);



		$head = we_html_tools::getHtmlInnerHead(g_l('export', "[wizard_title]")) . STYLESHEET . $js;

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead($head) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "pnt", "value" => "body")) .
						we_html_element::htmlHidden(array("name" => "step", "value" => "2")) .
						we_html_element::htmlHidden(array("name" => "art", "value" => ($this->exportVars["extype"] == "csv" ? "objects" : "docs"))) .
						we_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('export', "[step1]"))
					)
				)
		);
	}

	function getHTMLStep2(){
		if($this->exportVars["selection"] == "auto")
			return $this->getHTMLStep2a();
		else if($this->exportVars["selection"] == "manual"){
			/* if($this->exportVars["extype"]=="wxml") return $this->getHTMLStep3();
			  else */
			if($this->exportVars["extype"] == "csv"){
				return $this->getHTMLStep1();
			} else{
				return $this->getHTMLStep2b();
			}
		}
	}

	function getHTMLStep2a(){
		$yuiSuggest = & weSuggest::getInstance();

		$_space = 10;

		$js = we_html_element::jsScript(JS_DIR . "windows.js");
		$js.=we_html_element::jsElement('
			function we_cmd(){
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if (i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]){
					case "openCatselector":
						new jsWindow(url,"we_catselector",-1,-1,' . WINDOW_CATSELECTOR_WIDTH . ',' . WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
					break;
					case "add_cat":
					case "del_cat":
					case "del_all_cats":
						document.we_form.wcmd.value=arguments[0];
						document.we_form.cat.value=arguments[1];
						document.we_form.step.value=2;
						document.we_form.submit();
					break;
					case "openDirselector":
						new jsWindow(url,"we_selector",-1,-1,' . WINDOW_SELECTOR_WIDTH . ',' . WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
					break;
				}
			}

		');
		$js.=we_html_element::jsElement('
					' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=footer&step=2";
		');

		$parts = array();
		$showdocs = false;
		if(!isset($this->exportVars["extype"]) || (isset($this->exportVars["extype"]) && $this->exportVars["extype"] != "csv")){
			$doc_type = $this->getHTMLDocType();
			$showdocs = true;
			$_tmp = array("headline" => "", "html" => $doc_type, "space" => $_space);
			if(defined("OBJECT_FILES_TABLE")){
				$_tmp["noline"] = 1;
			}
			array_push($parts, $_tmp);
		}

		if(!$showdocs)
			$js.= we_html_element::jsElement($this->topFrame . ".type='classname';");
		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => ($showdocs ? "doctype" : "classname"))) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "4"));
		if(defined("OBJECT_FILES_TABLE")){
			$classname = $this->getHTMLObjectType(350, $showdocs);

			array_push($parts, array("headline" => "", "html" => $classname, "space" => $_space));
		}

		$category = $this->getHTMLCategory();
		array_push($parts, array("headline" => "", "html" => $category, "space" => $_space, "noline" => 1));


		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET . $js . $yuiSuggest->getYuiCssFiles() . $yuiSuggest->getYuiJsFiles()) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_multiIconBox::getHTML("weExportWizard", "100%", $parts, 30, "", -1, "", "", false, g_l('export', "[step2]"))
					) . $yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs()
				)
		);
	}

	function getHTMLStep2b(){
		$_space = 10;
		$art = $this->exportVars["art"];
		$js = we_html_element::jsElement(
				$this->headerFrame . '.location="' . $this->frameset . '?pnt=header&step=2";' .
				$this->footerFrame . '.location="' . $this->frameset . '?pnt=footer&step=2";');

		$parts = array(
			array("headline" => "", "html" => we_forms::radiobutton("docs", ($art == "docs" ? true : ($art != "objects" ? true : false)), "art", g_l('export', "[documents]"), true, "defaultfont", $this->topFrame . ".art='docs'"), "space" => $_space, "noline" => "1")
		);
		if(defined("OBJECT_FILES_TABLE")){
			$parts[] = array("headline" => "", "html" => we_forms::radiobutton("objects", ($art == "objects" ? true : ($art != "docs" ? true : false)), "art", g_l('export', "[objects]"), true, "defaultfont", $this->topFrame . ".art='objects'"), "space" => $_space, "noline" => "1");
		}

		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "selection", "value" => "manual")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "2"));

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET . $js) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						//we_html_element::htmlInput(array("type" => "text","name" => "selectedItems")).
						we_multiIconBox::getHTML("weExportWizard", "100%", $parts, 30, "", -1, "", "", false, g_l('export', "[step2]"))
					)
				)
		);
	}

	function getHTMLStep3(){
		$art = $this->exportVars["art"];

		$js = ($art == "objects" && defined("OBJECT_FILES_TABLE") ?
				we_html_element::jsElement($this->topFrame . '.table="' . OBJECT_FILES_TABLE . '";') :
				($art == "docs" ?
					we_html_element::jsElement($this->topFrame . '.table="' . FILE_TABLE . '";') :
					'')
			);


		$js.=we_html_element::jsElement(
				$this->footerFrame . '.location="' . $this->frameset . '?pnt=footer&step=3";
				setTimeout("' . $this->topFrame . '.startTree()",100);

			function populate(id,table){
				//if(table=="' . FILE_TABLE . '") document.we_form.selDocs.value+=","+id;
			//' . (defined("OBJECT_FILES_TABLE") ? 'else if(table=="' . OBJECT_FILES_TABLE . '") document.we_form.selObjs.value+=","+id;' : "") . '
			}

			function setHead(tab){
				var c0="#DDDDDD";
				var c1="#DDDDDD";
				var c2="#DDDDDD";
				var c3="#DDDDDD";
				eval("c"+tab+"=\"#DFE9F5\"");
				var fw0="normal";
				var fw1="normal";
				var fw2="normal";
				var fw3="normal";
				eval("fw"+tab+"=\"bold\"");


				switch (tab){
					case 0:
						' . $this->topFrame . '.table="' . FILE_TABLE . '";
					break;
					case 1:
						' . $this->topFrame . '.table="' . TEMPLATES_TABLE . '";
					break;
					' . (defined("OBJECT_FILES_TABLE") ? '
					case 2:
						' . $this->topFrame . '.table="' . OBJECT_FILES_TABLE . '";
					break;
					' : '') .
				(defined("OBJECT_TABLE") ? '
					case 3:
						' . $this->topFrame . '.table="' . OBJECT_TABLE . '";
					break;
					' : '') . '
				}

				setTimeout("' . $this->topFrame . '.startTree()",100);
				document.getElementById("' . FILE_TABLE . '").style.backgroundColor=c0;
				document.getElementById("' . TEMPLATES_TABLE . '").style.backgroundColor=c1;' .
				(defined("OBJECT_FILES_TABLE") ? 'document.getElementById("' . OBJECT_FILES_TABLE . '").style.backgroundColor=c2;' : '' ) .
				(defined("OBJECT_TABLE") ? 'document.getElementById("' . OBJECT_TABLE . '").style.backgroundColor=c3;' : '') . '

				document.getElementById("' . FILE_TABLE . '").style.fontWeight=fw0;
				document.getElementById("' . TEMPLATES_TABLE . '").style.fontWeight=fw1;' .
				(defined("OBJECT_FILES_TABLE") ? 'document.getElementById("' . OBJECT_FILES_TABLE . '").style.fontWeight=fw2;' : '' ) .
				(defined("OBJECT_TABLE") ? 'document.getElementById("' . OBJECT_TABLE . '").style.fontWeight=fw3;' : '') . '
			}

			function we_submit(){
				document.we_form.selDocs.value=' . $this->topFrame . '.SelectedItems["' . FILE_TABLE . '"].join(",");
				document.we_form.selTempl.value=' . $this->topFrame . '.SelectedItems["' . TEMPLATES_TABLE . '"].join(",");' .
				(defined("OBJECT_FILES_TABLE") ? 'document.we_form.selObjs.value=' . $this->topFrame . '.SelectedItems["' . OBJECT_FILES_TABLE . '"].join(",");' : '') .
				(defined("OBJECT_TABLE") ? 'document.we_form.selClasses.value=' . $this->topFrame . '.SelectedItems["' . OBJECT_TABLE . '"].join(",");' : '') . '
				document.we_form.submit();
			}');


		$style_code = "";
		if(isset($this->Tree->styles)){
			foreach($this->Tree->styles as $st){
				$style_code.=$st . "\n";
			}
		}

		$header = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => "0"), 2, 9);
		/* 		if($this->exportVars["extype"]=="wxml"){
		  $header->setCol(0,1,array("bgcolor"=>"white"),we_html_tools::getPixel(80,2));
		  $header->setCol(0,3,array("bgcolor"=>"white"),we_html_tools::getPixel(80,2));
		  $header->setCol(0,5,array("bgcolor"=>"white"),we_html_tools::getPixel(80,2));
		  $header->setCol(0,7,array("bgcolor"=>"white"),we_html_tools::getPixel(80,2));

		  $header->setCol(1,0,array("bgcolor"=>"#DFE9F5"),we_html_tools::getPixel(1,1));
		  $header->setCol(1,1,array("id"=>FILE_TABLE,"class"=>"header_small","bgcolor"=>"#DFE9F5","onclick"=>"setHead(0);","style"=>"{cursor: pointer;font-weight: bold;}"),we_html_tools::getPixel(5,2).g_l('export',"[documents]").we_html_tools::getPixel(5,2));
		  $header->setCol(1,2,array("bgcolor"=>"grey"),we_html_tools::getPixel(2,20));
		  $header->setCol(1,3,array("id"=>TEMPLATES_TABLE,"class"=>"header_small","bgcolor"=>"#DDDDDD","onclick"=>"setHead(1);","style"=>"{cursor: pointer;}"),we_html_tools::getPixel(5,2).g_l('export',"[templates]").we_html_tools::getPixel(5,2));
		  $header->setCol(1,4,array("bgcolor"=>"grey"),we_html_tools::getPixel(2,20));
		  $header->setCol(1,5,array("id"=>OBJECT_FILES_TABLE,"class"=>"header_small","bgcolor"=>"#DDDDDD","onclick"=>"setHead(2);","style"=>"{cursor: pointer;}"),we_html_tools::getPixel(5,2).g_l('export',"[objects]").we_html_tools::getPixel(5,2));
		  $header->setCol(1,6,array("bgcolor"=>"grey"),we_html_tools::getPixel(2,20));
		  $header->setCol(1,7,array("id"=>OBJECT_TABLE,"class"=>"header_small","bgcolor"=>"#DDDDDD","onclick"=>"setHead(3);","style"=>"{cursor: pointer;}"),we_html_tools::getPixel(5,2).g_l('export',"[classes]").we_html_tools::getPixel(5,2));
		  $header->setCol(1,8,array("bgcolor"=>"grey"),we_html_tools::getPixel(2,20));
		  } */
		$parts = array(
			array(
				"headline" => "",
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', "[select_export]"), 2, 540),
				"space" => 0,
				"noline" => 1
			),
			array(
				"headline" => "",
				"html" => $header->getHtml() . we_html_element::htmlDiv(array("id" => "treetable", "class" => "blockwrapper", "style" => "width: 540px; height: 250px; border:1px #dce6f2 solid;"), ""),
				"space" => 0
			)
		);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) .
					STYLESHEET .
					we_html_element::cssElement($style_code) .
					$js
				) .
				we_html_element::htmlBody(array(
					"class" => "weDialogBody"
					), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "pnt", "value" => "body")) .
						we_html_element::htmlHidden(array("name" => "step", "value" => "4")) .
						we_html_element::htmlHidden(array("name" => "selDocs", "value" => "")) .
						we_html_element::htmlHidden(array("name" => "selTempl", "value" => "")) .
						we_html_element::htmlHidden(array("name" => "selObjs", "value" => (isset($_SESSION['weS']['exportVars']["selObjs"]) ? $_SESSION['weS']['exportVars']["selObjs"] : ""))) .
						we_html_element::htmlHidden(array("name" => "selClasses", "value" => (isset($_SESSION['weS']['exportVars']["selClasses"]) ? $_SESSION['weS']['exportVars']["selClasses"] : ""))) .
						we_multiIconBox::getHTML("", 530, $parts, 30, "", -1, "", "", false, g_l('export', "[title]"))
					)
				)
		);
	}

	function getHTMLStep4(){
		//	define different parts of the export wizard
		$_space = 100;
		$_input_size = 42;

		$parts = array();

		$extype = $this->exportVars["extype"];
		$filename = $this->exportVars["filename"];
		$export_to = $this->exportVars["export_to"];
		$cdata = $this->exportVars["cdata"];
		$path = $this->exportVars["path"];
		$art = $this->exportVars["art"];

		$handle_def_templates = $this->exportVars["handle_def_templates"];
		$handle_document_includes = $this->exportVars["handle_document_includes"];
		$handle_document_linked = $this->exportVars["handle_document_linked"];

		$handle_def_classes = $this->exportVars["handle_def_classes"];
		$handle_object_includes = $this->exportVars["handle_object_includes"];
		$handle_object_linked = $this->exportVars["handle_object_linked"];

		$handle_doctypes = $this->exportVars["handle_doctypes"];
		$handle_categorys = $this->exportVars["handle_categorys"];


		$handle_object_embeds = $this->exportVars["handle_object_embeds"];
		$handle_class_defs = $this->exportVars["handle_class_defs"];


		$export_depth = $this->exportVars["export_depth"];


		if($filename == "")
			$filename = "weExport_" . time() . ($extype == "gxml" ? ".xml" : ".csv");

		//set variables in top frame
		$js = we_html_element::jsElement('
function setLabelState(l,disable){
	if(disable) document.getElementById(l).style.color = "grey";
	else document.getElementById(l).style.color = "black";
}

function setState(a) {
		if (document.getElementsByName(a)[0].checked == true) {
			_new_state = false;
		} else {
			_new_state = true;
		}
		if(a=="_handle_templates"){
			if(_new_state==true){
				document.getElementsByName("handle_document_linked")[0].value = 0;
				document.getElementsByName("handle_object_linked")[0].value = 0;

				document.getElementsByName("_handle_document_linked")[0].checked = false;
				document.getElementsByName("_handle_object_linked")[0].checked = false;
			}

			document.getElementsByName("_handle_document_linked")[0].disabled = _new_state;
			setLabelState("label__handle_document_linked",_new_state);

			document.getElementsByName("_handle_object_linked")[0].disabled = _new_state;
			setLabelState("label__handle_object_linked",_new_state);
		}
		if(a=="_handle_classesfff"){
			if(_new_state==true){
				document.getElementsByName("handle_object_includes")[0].value = 0;
				document.getElementsByName("_handle_object_includes")[0].checked = false;
			}
			document.getElementsByName("_handle_object_includes")[0].disabled = _new_state;
			setLabelState("label__handle_object_includes",_new_state);

			document.getElementsByName("link_object_depth")[0].disabled = _new_state;
			setLabelState("label_link_object_depth",_new_state);


		}
}
' . $this->headerFrame . '.location="' . $this->frameset . '?pnt=header&step=4";
' . $this->footerFrame . '.location="' . $this->frameset . '?pnt=footer&step=4";');


		$formattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 4, 1);
		$formattable->setCol(0, 0, null, we_forms::checkboxWithHidden($handle_def_templates, "handle_def_templates", g_l('export', "[handle_def_templates]")));
		$formattable->setCol(1, 0, null, we_forms::checkboxWithHidden(($handle_document_includes ? true : false), "handle_document_includes", g_l('export', "[handle_document_includes]")));
		$formattable->setCol(2, 0, null, we_forms::checkboxWithHidden(($handle_object_includes ? true : false), "handle_object_includes", g_l('export', "[handle_object_includes]")));
		$formattable->setCol(3, 0, null, we_forms::checkboxWithHidden(($handle_document_linked ? true : false), "handle_document_linked", g_l('export', "[handle_document_linked]")));

		$parts[] = array("headline" => g_l('export', "[handle_document_options]") . we_html_element::htmlBr() . g_l('export', "[handle_template_options]"), "html" => $formattable->getHtml(), "space" => $_space);

		$formattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 3, 1);
		$formattable->setCol(0, 0, array("colspan" => "2"), we_forms::checkboxWithHidden(($handle_def_classes ? true : false), "handle_def_classes", g_l('export', "[handle_def_classes]")));
		$formattable->setCol(1, 0, null, we_forms::checkboxWithHidden(($handle_object_embeds ? true : false), "handle_object_embeds", g_l('export', "[handle_object_embeds]")));
		//$formattable->setCol(2,0,null,we_forms::checkboxWithHidden(($handle_class_defs ? true : false),"handle_class_defs",g_l('export',"[handle_class_defs]")));

		$parts[] = array("headline" => g_l('export', "[handle_object_options]") . we_html_element::htmlBr() . g_l('export', "[handle_classes_options]"), "html" => $formattable->getHtml(), "space" => $_space);

		$formattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 2, 1);
		$formattable->setCol(0, 0, null, we_forms::checkboxWithHidden(($handle_doctypes ? true : false), "handle_doctypes", g_l('export', "[handle_doctypes]")));
		$formattable->setCol(1, 0, null, we_forms::checkboxWithHidden(($handle_categorys ? true : false), "handle_categorys", g_l('export', "[handle_categorys]")));

		$parts[] = array("headline" => g_l('export', "[handle_doctype_options]"), "html" => $formattable->getHtml(), "space" => $_space);

		$parts[] = array("headline" => g_l('export', "[export_depth]"), "html" => we_html_element::htmlLabel(null, g_l('export', "[to_level]")) . we_html_tools::getPixel(5, 5) . we_html_tools::htmlTextInput("export_depth", 10, $export_depth, "", "", "text", 50), "space" => $_space);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(STYLESHEET . $js) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "pnt", "value" => "body")) .
						we_html_element::htmlHidden(array("name" => "step", "value" => "7")) .
						we_multiIconBox::getHTML("weExportWizard", "100%", $parts, 30, "", -1, "", "", false, g_l('export', "[options]"))
					)
				)
		);
	}

	function getHTMLStep7(){
		//	define different parts of the export wizard
		$_space = 130;
		$_input_size = 42;

		$parts = array();

		$extype = $this->exportVars["extype"];
		$filename = $this->exportVars["filename"];
		$export_to = $this->exportVars["export_to"];
		$cdata = $this->exportVars["cdata"];
		$path = $this->exportVars["path"];
		$art = $this->exportVars["art"];
		$csv_delimiter = $this->exportVars["csv_delimiter"];
		$csv_enclose = $this->exportVars["csv_enclose"];
		$csv_lineend = $this->exportVars["csv_lineend"];
		$csv_fields = $this->exportVars["csv_fields"];

		if($filename == ""){
			$filename = "weExport_" . date('d_m_Y_H_i') . ($extype == "gxml" ? ".xml" : ".csv");
		}

		//set variables in top frame
		$js = we_html_element::jsElement(
				$this->headerFrame . '.location="' . $this->frameset . '?pnt=header&step=7";' .
				$this->footerFrame . '.location="' . $this->frameset . '?pnt=footer&step=7";');

		$parts[] = array("headline" => g_l('export', "[filename]"), "html" => we_html_tools::getPixel(5, 5) . we_html_tools::htmlTextInput("filename", $_input_size, $filename, "", "", "text", 260), "space" => $_space);

		//	Filetype
		switch($extype){
			case "csv":
				$csv_input_size = 3;

				$fileformattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 4, 1);

				$_file_encoding = new we_html_select(array("name" => "csv_lineend", "size" => "1", "class" => "weSelect", "style" => "width: 254px"));
				$_file_encoding->addOption("windows", g_l('export', "[windows]"));
				$_file_encoding->addOption("unix", g_l('export', "[unix]"));
				$_file_encoding->addOption("mac", g_l('export', "[mac]"));
				$_file_encoding->selectOption($csv_lineend);

				$fileformattable->setCol(0, 0, array("class" => "defaultfont"), g_l('export', "[csv_lineend]") . "<br>" . $_file_encoding->getHtml());
				$fileformattable->setColContent(1, 0, $this->getHTMLChooser("csv_delimiter", $csv_delimiter, array(";" => g_l('export', "[semicolon]"), "," => g_l('export', "[comma]"), ":" => g_l('export', "[colon]"), "\\t" => g_l('export', "[tab]"), " " => g_l('export', "[space]")), g_l('export', "[csv_delimiter]")));
				$fileformattable->setColContent(2, 0, $this->getHTMLChooser("csv_enclose", $csv_enclose, array("\"" => g_l('export', "[double_quote]"), "'" => g_l('export', "[single_quote]")), g_l('export', "[csv_enclose]")));

				$fileformattable->setColContent(3, 0, we_forms::checkbox(1, true, "csv_fieldnames", g_l('export', "[csv_fieldnames]")));

				$parts[] = array("headline" => g_l('export', "[csv_params]"), "html" => $fileformattable->getHtml(), "space" => $_space);
				break;

			case "gxml":
				$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);

				$table->setColContent(1, 0, we_html_tools::getPixel(1, 10));
				$table->setColContent(0, 0, we_forms::radiobutton("true", ($cdata == "true"), "cdata", g_l('export', "[export_xml_cdata]"), true, "defaultfont", $this->topFrame . ".cdata='true'"));
				$table->setColContent(2, 0, we_forms::radiobutton("false", ($cdata == "false"), "cdata", g_l('export', "[export_xml_entities]"), true, "defaultfont", $this->topFrame . ".cdata='false'"));

				$parts[] = array("headline" => g_l('export', "[cdata]"), "html" => $table->getHtml(), "space" => $_space);
				break;
		}

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);

		$table->setColContent(0, 0, we_forms::radiobutton("local", ($export_to == "local" ? true : false), "export_to", g_l('export', "[export_to_local]"), true, "defaultfont", $this->topFrame . ".export_to='local'"));
		$table->setColContent(1, 0, we_html_tools::getPixel(20, 20));
		$table->setColContent(2, 0, we_html_tools::htmlFormElementTable($this->formFileChooser(260, "path", $path, "", "folder"), we_forms::radiobutton("server", ($export_to == "server" ? true : false), "export_to", g_l('export', "[export_to_server]"), true, "defaultfont", $this->topFrame . ".export_to='server'")));

		$parts[] = array("headline" => g_l('export', "[export_to]"), "html" => $table->getHtml(), "space" => $_space);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(STYLESHEET . "\n" . $js) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "pnt", "value" => "load")) .
						we_html_element::htmlHidden(array("name" => "cmd", "value" => "export")) .
						we_html_element::htmlHidden(array("name" => "step", "" => "7")) .
						we_multiIconBox::getHTML("weExportWizard", "100%", $parts, 30, "", -1, "", "", false, g_l('export', "[step3]"))
					)
				)
		);
	}

	function getHTMLStep10(){
		$filename = isset($_REQUEST["file_name"]) ? urldecode($_REQUEST["file_name"]) : false;

		$message = we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('export', "[backup_finished]") . "<br><br>" .
				g_l('export', "[download_starting]") .
				we_html_element::htmlA(array("href" => $this->frameset . "?pnt=body&step=50&exportfile=" . $filename), g_l('export', "[download]")));

		unset($_SESSION['weS']['exportVars']);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET .
					we_html_element::htmlMeta(array("http-equiv" => "refresh", "content" => "2; URL=" . $this->frameset . "?pnt=body&step=50&exportfile=" . $filename))) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout($message, g_l('export', "[step10]"))
				)
		);
	}

	function getHTMLStep50(){
		@set_time_limit(0);
		$preurl = getServerUrl();
		if(isset($_GET["exportfile"])){
			$_filename = basename(urldecode($_GET["exportfile"]));

			if(file_exists(TEMP_PATH . $_filename) // Does file exist?
				&& !preg_match('%p?html?%i', $_filename) && stripos($_filename, "inc") === false && !preg_match('%php3?%i', $_filename)){ // Security check
				$_size = filesize(TEMP_PATH . $_filename);

				if(we_isHttps()){ // Additional headers to make downloads work using IE in HTTPS mode.
					header("Pragma: ");
					header("Cache-Control: ");
					header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
					header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
					header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
					header("Cache-Control: post-check=0, pre-check=0", false);
				} else{
					header("Cache-control: private, max-age=0, must-revalidate");
				}

				header("Content-Type: application/octet-stream");
				header("Content-Disposition: attachment; filename=\"" . trim(htmlentities($_filename)) . "\"");
				header("Content-Description: " . trim(htmlentities($_filename)));
				header("Content-Length: " . $_size);

				$_filehandler = readfile(TEMP_PATH . $_filename);

				exit;
			} else{
				header("Location: " . $preurl . $this->frameset . "?pnt=body&step=99&error=download_failed");
				exit;
			}
		} else{
			header("Location: " . $preurl . $this->frameset . "?pnt=body&step=99&error=download_failed");
			exit;
		}
	}

	function getHTMLStep99(){
		$errortype = isset($_REQUEST["error"]) ? $_REQUEST["error"] : "unknown";

		switch($errortype){
			case "no_object_module":
				$returned_message = array(g_l('export', "[error_object_module]"), false);

				break;

			case "nothing_selected_docs":
				$returned_message = array(g_l('export', "[error_nothing_selected_docs]"), false);

				break;

			case "nothing_selected_objs":
				$returned_message = array(g_l('export', "[error_nothing_selected_objs]"), false);

				break;

			case "download_failed":
				$returned_message = array(g_l('export', "[error_download_failed]"), true);

				break;
			case "unknown":
			default:
				$returned_message = array(g_l('export', "[error_unknown]"), true);

				break;
		}

		$message = we_html_element::htmlSpan(array("class" => "defaultfont"), ($returned_message[1] ? (g_l('export', "[error]") . "<br><br>") : "") . $returned_message[0]);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET) .
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout($message, ($returned_message[1] ? g_l('export', "[step99]") : g_l('export', "[step99_notice]")))
				)
		);
	}

	function getHTMLHeader($step = 0){
		$js = "";
		$js2 = "";

		$art = $this->exportVars["art"];
		$selection = $this->exportVars["selection"];

		$table = new we_html_table(array("width" => "3000", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 3, 1);
		//print $step;

		if($step == 3){
			$js.=we_html_element::jsElement('
				function addOpenFolder(id){
					if (top.openFolders[top.table]=="") top.openFolders[top.table]+=id;
					else top.openFolders[top.table]+=","+id;
				}

				function delOpenFolder(id){
					var of=top.openFolders[top.table];
					var arr=new Array();
					var arr1=new Array();
					arr=of.split(",");
					for(i=0;i<arr.length;i++){
							if (arr[i]!=id) arr1.push(arr[i]);
					}
					top.openFolders[top.table]=arr1.join(",");
				}


				function populateVars(){
					' . $this->bodyFrame . '.document.we_form.selDocs.value="' . (isset($_SESSION['weS']['exportVars']["selDocs"]) ? $_SESSION['weS']['exportVars']["selDocs"] : "") . '";
					' . $this->bodyFrame . '.document.we_form.selObjs.value="' . (isset($_SESSION['weS']['exportVars']["selObjs"]) ? $_SESSION['weS']['exportVars']["selObjs"] : "") . '";
				}

				function setTab(tab) {
					' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.treeData.table]=new Array();
					for(i=1;i<' . $this->topFrame . '.treeData.len;i++) {
						if (' . $this->topFrame . '.treeData[i].checked==1) {
							' . $this->topFrame . '.SelectedItems[' . $this->topFrame . '.treeData.table].push(' . $this->topFrame . '.treeData[i].id);
						}
					}

					switch (tab) {
						case 0:
							top.table="' . FILE_TABLE . '";
						break;' .
					(defined("OBJECT_FILES_TABLE") ? ('
						case 1:
							top.table="' . OBJECT_FILES_TABLE . '";
						break;') : '') . '
					}


					document.we_form.openFolders.value=' . $this->topFrame . '.openFolders[top.table];
					document.we_form.tab.value=' . $this->topFrame . '.table;
					' . $this->topFrame . '.activetab=tab;
					document.we_form.submit();

				}

				var js_path  = "' . JS_DIR . '";
				var img_path = "' . IMAGE_DIR . "tabs/" . '";
				var suffix   = "";
				var layerPosYOffset = 22;
		');
			$js.=we_html_element::jsScript(JS_DIR . "images.js") .
				we_html_element::jsScript(JS_DIR . "we_tabs/tabs_inc.js");

			$js2 = we_html_element::jsElement('
				var winWidth  = getWindowWidth(window);
				var winHeight = getWindowHeight(window);

				var we_tabs = new Array();
				' . ($art == "docs" ? ('we_tabs.push(new We_Tab("#","' . g_l('export', "[documents]") . '",(' . $this->topFrame . '.table=="' . FILE_TABLE . '" ? TAB_ACTIVE : TAB_NORMAL),"self.setTab(0);"));') : '') . '
				' . ($art == "objects" && defined("OBJECT_FILES_TABLE") ? ('we_tabs.push(new We_Tab("#","' . g_l('export', "[objects]") . '",(' . $this->topFrame . '.table=="' . OBJECT_FILES_TABLE . '" ? TAB_ACTIVE : TAB_NORMAL),"self.setTab(1);"));') : '') . '

		');


			$table->setCol(0, 0, array("class" => "header_small"), we_html_tools::getPixel(5, 15) . we_html_element::htmlB(g_l('export', "[step2]")));
			$table->setCol(1, 0, array("valign" => "top"), we_html_tools::getPixel(15, 2));
			$table->setCol(2, 0, array("nowrap" => "nowrap"), we_html_element::jsElement('setTimeout("we_tabInit()",500);')
			);
		} else if($step == 1 || $step == 2 || $step == 4)
			$js2 = we_html_element::jsElement('
				if (parent.frames.length > 0) {
					var frmRows = parent.document.body.rows;
					var rows = frmRows.split(",");

					var newFrmRows = 1;
					for (var i=1; i<rows.length; i++) {
						newFrmRows += ","+rows[i];
					}

					if (frmRows != newFrmRows) {
						parent.document.body.rows = newFrmRows;
					}
				}
		');

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET . $js) .
				we_html_element::htmlBody(array("bgcolor" => "#ffffff", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0"), $js2 .
					$table->getHtml() .
					we_html_element::htmlForm(array("name" => "we_form", "target" => "load", "action" => $this->frameset), we_html_element::htmlHidden(array("name" => "pnt", "value" => "load")) .
						we_html_element::htmlHidden(array("name" => "cmd", "value" => "load")) .
						we_html_element::htmlHidden(array("name" => "tab", "value" => "")) .
						we_html_element::htmlHidden(array("name" => "pid", "value" => "0")) .
						we_html_element::htmlHidden(array("name" => "openFolders", "value" => ""))
					)
				)
		);
	}

	function getHTMLFooter($step = 0){
		$this->getExportVars();
		$errortype = isset($_REQUEST["error"]) ? $_REQUEST["error"] : "no_error";
		$selection = isset($_REQUEST["selection"]) ? $_REQUEST["selection"] : "auto";
		$show_controls = false;
		$js = "";
		switch($errortype){
			case "no_object_module":
			default:
				$show_controls = true;

				break;
		}
		switch($step){
			case 0:
				$buttons = we_button::position_yes_no_cancel(
						we_button::create_button_table(array(
							we_button::create_button("back", "", false, 100, 22, "", "", true),
							we_button::create_button("next", "javascript:" . $this->bodyFrame . ".document.we_form.submit();"))), we_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			case 1:
				$buttons = we_button::position_yes_no_cancel(
						we_button::create_button_table(array(
							we_button::create_button("back", "javascript:" . $this->bodyFrame . ".document.we_form.step.value=0;" . $this->bodyFrame . ".document.we_form.submit();"),
							we_button::create_button("next", "javascript:" . $this->bodyFrame . ".we_submit();"))), we_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			case 2:
				$buttons = we_button::position_yes_no_cancel(
						we_button::create_button_table(array(
							we_button::create_button("back", "javascript:" . $this->bodyFrame . ".document.we_form.step.value=1;" . $this->bodyFrame . ".document.we_form.submit();"),
							we_button::create_button("next", "javascript:" . $this->bodyFrame . ".document.we_form.step.value=" . ($this->exportVars["selection"] == "auto" ? "7" : "3") . ";" . $this->bodyFrame . ".document.we_form.submit();"))), we_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			case 3:
				$buttons = we_button::position_yes_no_cancel(
						we_button::create_button_table(array(
							we_button::create_button("back", "javascript:" . $this->bodyFrame . ".document.we_form.step.value=2;" . $this->bodyFrame . ".we_submit();"),
							we_button::create_button("next", "javascript:" . $this->bodyFrame . ".document.we_form.step.value=7;" . $this->bodyFrame . ".we_submit();"))), we_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			case 4:
				$buttons = we_button::position_yes_no_cancel(
						we_button::create_button_table(array(
							we_button::create_button("back", "javascript:" . $this->bodyFrame . ".document.we_form.target='body';" . $this->bodyFrame . ".document.we_form.pnt.value='body';" . $this->bodyFrame . ".document.we_form.step.value=" . ($this->exportVars["selection"] == "auto" ? "2" : "3") . ";" . $this->bodyFrame . ".document.we_form.submit();"),
							we_button::create_button("next", "javascript:" . $this->bodyFrame . ".document.we_form.submit();"))), we_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			case 7:
				$buttons = we_button::position_yes_no_cancel(
						we_button::create_button_table(array(
							we_button::create_button("back", "javascript:" . $this->bodyFrame . ".document.we_form.target='body';" . $this->bodyFrame . ".document.we_form.pnt.value='body';" . $this->bodyFrame . ".document.we_form.step.value=" . ($this->exportVars["selection"] == "auto" ? "2" : "3") . ";" . $this->bodyFrame . ".document.we_form.submit();"),
							we_button::create_button("next", "javascript:" . $this->bodyFrame . ".document.we_form.target='load';;" . $this->bodyFrame . ".document.we_form.pnt.value='load';" . $this->bodyFrame . ".document.we_form.submit();"))), we_button::create_button("cancel", "javascript:top.close();")
				);
				break;
			case 10:
			case 99:
				if($step == 10 || ($step == 99 && !$show_controls)){
					$buttons = we_button::create_button("close", "javascript:top.close();");
				} else if($step == 99 && $show_controls){
					$buttons = we_button::position_yes_no_cancel(
							we_button::create_button_table(array(
								we_button::create_button("back", "javascript:" . $this->bodyFrame . ".location='" . $this->frameset . "?pnt=body&step=0';" . $this->footerFrame . ".location='" . $this->frameset . "?pnt=footer&step=0';"),
								we_button::create_button("next", "", false, 100, 22, "", "", true))), we_button::create_button("cancel", "javascript:top.close();")
					);
				}
				break;
			default:
				$buttons = we_button::position_yes_no_cancel(
						we_button::create_button_table(array(
							we_button::create_button("back", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=load&cmd=back&step=" . $step . "';"),
							we_button::create_button("next", "javascript:" . $this->loadFrame . ".location='" . $this->frameset . "?pnt=load&cmd=next&step=" . $step . "';"))), we_button::create_button("cancel", "javascript:top.close();")
				);
		}

		if(isset($_REQUEST["mode"]) && $_REQUEST["mode"] == "progress"){
			$text = g_l('backup', "[working]");
			$progress = 0;

			if(isset($_REQUEST["current_description"]) && $_REQUEST["current_description"]){
				$text = $_REQUEST["current_description"];
			}

			if(isset($_REQUEST["percent"]) && $_REQUEST["percent"]){
				$progress = $_REQUEST["percent"];
			}

			$progressbar = new we_progressBar($progress);
			$progressbar->setStudLen(200);
			$progressbar->addText($text, 0, "current_description");
		}

		$content = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "100%"), 1, 2);
		$content->setCol(0, 0, null, (isset($progressbar) ? $progressbar->getHtml() : ""));
		$content->setCol(0, 1, array("align" => "right"), $buttons);

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET . (isset($progressbar) ? $progressbar->getJSCode() : "")
				) .
				we_html_element::htmlBody(array("class" => "weDialogButtonsBody"), we_html_element::htmlForm(array(
						"name" => "we_form",
						"method" => "post",
						"target" => "load",
						"action" => $this->frameset
						), $content->getHtml()
					)
				)
		);
	}

	function getHTMLCmd(){
		$out = "";
		$this->getExportVars();

		if(isset($_REQUEST["cmd"])){
			switch($_REQUEST["cmd"]){
				case "load":
					if(isset($_REQUEST["pid"])){
						return we_html_element::jsElement("self.location='" . EXPORT_PATH . "exportLoadTree.php?we_cmd[1]=" . $_REQUEST["tab"] . "&we_cmd[2]=" . $_REQUEST["pid"] . "&we_cmd[3]=" . (isset($_REQUEST["openFolders"]) ? $_REQUEST["openFolders"] : "") . "'");
					}
					break;
				case "export":
					$xmlExIm = new weXMLExIm();

					$file_format = isset($_REQUEST["file_format"]) ? $_REQUEST["file_format"] : "";
					//$file_name = isset($_REQUEST["filename"]) ? $_REQUEST["filename"] : "";
					$export_to = isset($_REQUEST["export_to"]) ? $_REQUEST["export_to"] : "";
					$path = isset($_REQUEST["path"]) ? $_REQUEST["path"] . "/" : "/";
					$csv_delimiter = isset($_REQUEST["csv_delimiter"]) ? $_REQUEST["csv_delimiter"] : "";
					$csv_enclose = isset($_REQUEST["csv_enclose"]) ? $_REQUEST["csv_enclose"] : "";
					$csv_lineend = isset($_REQUEST["csv_lineend"]) ? $_REQUEST["csv_lineend"] : "";
					$csv_fieldnames = isset($_REQUEST["csv_fieldnames"]) ? $_REQUEST["csv_fieldnames"] : "";
					$cdata = (isset($_REQUEST["cdata"]) && $_REQUEST["cdata"] == "false") ? false : true;

					$extype = $this->exportVars["extype"];
					$filename = $this->exportVars["filename"];
					$export_to = $this->exportVars["export_to"];
					$path = $this->exportVars["path"];
					$csv_delimiter = $this->exportVars["csv_delimiter"];
					$csv_enclose = $this->exportVars["csv_enclose"];
					$csv_lineend = $this->exportVars["csv_lineend"];
					$csv_fieldnames = $this->exportVars["csv_fieldnames"];
					$cdata = $this->exportVars["cdata"];

					$finalDocs = makeArrayFromCSV($this->exportVars["selDocs"]);
					$finalTempl = makeArrayFromCSV($this->exportVars["selTempl"]);
					$finalObjs = makeArrayFromCSV($this->exportVars["selObjs"]);
					$finalClasses = makeArrayFromCSV($this->exportVars["selClasses"]);

					$xmlExIm->getSelectedItems($this->exportVars["selection"], $extype, $this->exportVars["art"], $this->exportVars["type"], $this->exportVars["doctype"], $this->exportVars["classname"], $this->exportVars["categories"], $this->exportVars["dir"], $finalDocs, $finalTempl, $finalObjs, $finalClasses);


					/* if ($this->exportVars["selection"]=="manual"){
					  $selDocs = makeArrayFromCSV($this->exportVars["selDocs"]);
					  $selTempl = makeArrayFromCSV($this->exportVars["selTempl"]);
					  $selObjs = makeArrayFromCSV($this->exportVars["selObjs"]);
					  $selClasses = makeArrayFromCSV($this->exportVars["selClasses"]);
					  //if($extype=="wxml"){
					  //	$finalDocs = $this->getIDs($selDocs,FILE_TABLE,false);
					  //	$finalTempl = $this->getIDs($selTempl,TEMPLATES_TABLE,false);
					  //	$finalObjs = defined("OBJECT_FILES_TABLE") ? $this->getIDs($selObjs,OBJECT_FILES_TABLE,false) : "";
					  //	$finalClasses = defined("OBJECT_FILES_TABLE") ? $this->getIDs($selClasses,OBJECT_TABLE,false) : "";
					  //}
					  else{
					  if($this->exportVars["art"]=="docs") $finalDocs = $this->getIDs($selDocs,FILE_TABLE);
					  else if($this->exportVars["art"]=="objects") $finalObjs = defined("OBJECT_FILES_TABLE") ? $this->getIDs($selObjs,OBJECT_FILES_TABLE) : "";
					  //}

					  }
					  else{
					  if ($this->exportVars["type"]=="doctype"){
					  $doctypename=f("SELECT DocType FROM ".DOC_TYPES_TABLE." WHERE ID='".$this->exportVars["doctype"]."';","DocType",$this->db);

					  $catss="";
					  if ($this->exportVars["categories"]){
					  $catids=makeCSVFromArray(makeArrayFromCSV($this->exportVars["categories"]));
					  $this->db->query("SELECT Category FROM ".CATEGORY_TABLE." WHERE ID IN (".$catids.");");
					  while($this->db->next_record()){
					  $cats[]=$this->db->f("Category");
					  }
					  $catss=makeCSVFromArray($cats);
					  }
					  $lv=new we_listview("export_docs",999999999,0,"",false, $doctypename, $catss,false,false,$this->exportVars["dir"]);
					  while($lv->DB_WE->next_record()){
					  $finalDocs[]=$lv->DB_WE->f("ID");
					  }
					  }
					  else {
					  if (defined("OBJECT_FILES_TABLE")) {

					  $catss = "";

					  if ($this->exportVars["categories"]) {
					  $catss=$this->exportVars["categories"];
					  }

					  $this->db->query("SELECT ID FROM ".OBJECT_FILES_TABLE." WHERE IsFolder=0 AND TableID='".$this->exportVars["classname"]."'".($catss!="" ? " AND Category IN (".$catss.");" : ";"));

					  while($this->db->next_record()){
					  $finalObjs[]=$this->db->f("ID");
					  }
					  }
					  }
					  } */

					$_SESSION['weS']['exportVars']["finalDocs"] = $finalDocs;
					$_SESSION['weS']['exportVars']["finalTempl"] = $finalTempl;
					$_SESSION['weS']['exportVars']["finalObjs"] = $finalObjs;
					$_SESSION['weS']['exportVars']["finalClasses"] = $finalClasses;

					// Description of the variables:
					//  $finalDocs - contains documents IDs that need to be exported
					//  $finalObjs - contains objects IDs that need to be exported
					//  $file_format - export format; possible values are "xml","csv"
					//  $file_name - name of the file that contains exported docs and objects
					//  $export_to - where the file should be stored; possible values are "server","local"
					//  $path - if the file will be stored on server then this variable contains the server path
					//  $csv_delimiter - non-empty if csv file has been specified
					//  $csv_enclose - non-empty if csv file has been specified
					//  $csv_lineend - non-empty if csv file has been specified
					//  $csv_fieldnames - non-empty if first row conains field names
					//  $cdata - non-empty if xml file has been specified - coding of file

					$start_export = false;

					$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "load"));

					if(!empty($finalDocs)){
						$start_export = true;
						$hiddens .= we_html_element::htmlHidden(array("name" => "all", "value" => count($finalDocs)));
					} else if(!empty($finalObjs)){
						$start_export = true;
						$hiddens .= we_html_element::htmlHidden(array("name" => "all", "value" => count($finalObjs)));

						/* } else if ((count($finalTempl) > 0 && $extype=="wxml") || (count($finalClasses) > 0  && $extype=="wxml")) {
						  $start_export = true; */
					} else{
						$export_error = (defined("OBJECT_TABLE") ? "nothing_selected_objs" : "nothing_selected_docs");
					}

					if($start_export){
						$hiddens .= we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_export"));

						$out .=
							we_html_element::jsElement('
								if (top.footer.setProgressText) top.footer.setProgressText("current_description","Exportiere ...");
								if (top.footer.setProgress) top.footer.setProgress(0);
							');
					}

					return we_html_element::htmlDocType() . we_html_element::htmlHtml(
							we_html_element::htmlHead(we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET) .
							we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => "5", "marginheight" => "5", "leftmargin" => "5", "topmargin" => "5", "onLoad" => ($start_export ? ($this->footerFrame . ".location='" . $this->frameset . "?pnt=footer&mode=progress&step=4';document.we_form.submit()") : ($this->bodyFrame . ".location='" . $this->frameset . "?pnt=body&step=99&error=" . $export_error . "';" . $this->footerFrame . ".location='" . $this->frameset . "?pnt=footer&step=99';"))), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens)
							)
					);

				case "do_export":
					$this->getExportVars();

					$file_format = $this->exportVars["extype"];
					$filename = $this->exportVars["filename"];
					$path = $this->exportVars["path"] . "/";

					$remaining_docs = $this->exportVars["finalDocs"];
					$remaining_objs = $this->exportVars["finalObjs"];
					$export_local = $this->exportVars["export_to"] == "local" ? true : false;

					$csv_delimiter = $this->exportVars["csv_delimiter"];
					$csv_enclose = $this->exportVars["csv_enclose"];
					$csv_lineend = $this->exportVars["csv_lineend"];
					$csv_fieldnames = $this->exportVars["csv_fieldnames"];

					$cdata = $this->exportVars["cdata"] == "true" ? true : false;


					$all = (isset($_REQUEST["all"]) && $_REQUEST["all"] > 0) ? $_REQUEST["all"] : 0;
					$exports = 0;

					if(isset($remaining_docs) && !empty($remaining_docs)){
						$exports = count($remaining_docs);
						$file_create = ($exports == $all);
						$file_complete = ($exports == "1");

						exportFunctions::exportDocument($remaining_docs[0], $file_format, $filename, ($export_local ? "###temp###" : $path), $file_create, $file_complete, $cdata);
					} else if(isset($remaining_objs) && !empty($remaining_objs)){
						if(defined("OBJECT_FILES_TABLE")){
							$exports = count($remaining_objs);
							exportFunctions::exportObject($remaining_objs[0], $file_format, $filename, ($export_local ? "###temp###" : $path), ($exports == $all), $exports == 1, $cdata, $csv_delimiter, $csv_enclose, $csv_lineend, ($csv_fieldnames == 1) && ($all == $exports));
						}
					}

					$percent = (int) ((($all - $exports + 2) / $all) * 100);

					if($percent < 0){
						$percent = 0;
					} else if($percent > 100){
						$percent = 100;
					}

					$_progress_update =
						we_html_element::jsElement('
							if (top.footer.setProgress) top.footer.setProgress(' . $percent . ');
						');

					if(count($remaining_docs) > 0){
						$cut = array_shift($remaining_docs);
						$_SESSION['weS']['exportVars']["finalDocs"] = $remaining_docs;
					} else if(count($remaining_objs) > 0){
						$cut = array_shift($remaining_objs);
						$_SESSION['weS']['exportVars']["finalObjs"] = $remaining_objs;
					}

					$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "load")) .
						we_html_element::htmlHidden(array("name" => "all", "value" => $all)) .
						we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_export"));
					if((count($remaining_docs) > 0) || (count($remaining_objs) > 0)){
						$head = we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET;

						return we_html_element::htmlDocType() . we_html_element::htmlHtml(
								we_html_element::htmlHead($head) .
								we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => "5", "marginheight" => "5", "leftmargin" => "5", "topmargin" => "5", "onLoad" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens) . $_progress_update
								)
						);
					}
					if(!$export_local){
						unset($_SESSION['weS']['exportVars']);
					}
					$head = we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET;
					return we_html_element::htmlDocType() . we_html_element::htmlHtml(
							we_html_element::htmlHead($head) .
							we_html_element::htmlBody(
								array(
									"bgcolor" => "#ffffff",
									"marginwidth" => "5",
									"marginheight" => "5",
									"leftmargin" => "5",
									"topmargin" => "5",
									"onLoad" => oldHtmlspecialchars($export_local ? ($this->bodyFrame . ".location='" . $this->frameset . "?pnt=body&step=10&file_name=" . urlencode($filename) . "';" . $this->footerFrame . ".location='" . $this->frameset . "?pnt=footer&step=10';") : (we_message_reporting::getShowMessageCall(g_l('export', "[server_finished]"), we_message_reporting::WE_MESSAGE_NOTICE) . "top.close();")))), null
					);



				case "do_wexport":
					$this->getExportVars();

					$file_format = $this->exportVars["extype"];
					$filename = $this->exportVars["filename"];
					$path = $this->exportVars["path"] . "/";

					$remaining_docs = $this->exportVars["finalDocs"];
					$remaining_objs = $this->exportVars["finalObjs"];
					$export_local = $this->exportVars["export_to"] == "local" ? true : false;

					$csv_delimiter = $this->exportVars["csv_delimiter"];
					$csv_enclose = $this->exportVars["csv_enclose"];
					$csv_lineend = $this->exportVars["csv_lineend"];
					$csv_fieldnames = $this->exportVars["csv_fieldnames"];

					$cdata = $this->exportVars["cdata"] == "true" ? true : false;

					$xmlExIm = new weXMLExIm();

					if(empty($this->exportVars["RefTable"])){
						$finalDocs = $this->exportVars["finalDocs"];
						$finalTempl = $this->exportVars["finalTempl"];
						$finalObjs = $this->exportVars["finalObjs"];
						$finalClasses = $this->exportVars["finalClasses"];

						$ids = array();
						foreach($finalDocs as $k => $v){
							$ct = f("Select ContentType FROM " . FILE_TABLE . " WHERE ID=" . $v . ";", "ContentType", $this->db);
							$ids[] = array(
								"ID" => $v,
								"ContentType" => $ct,
								"level" => 0
							);
						}
						foreach($finalTempl as $k => $v){
							$ids[] = array(
								"ID" => $v,
								"ContentType" => "text/weTmpl",
								"level" => 0
							);
						}
						foreach($finalObjs as $k => $v){
							$ids[] = array(
								"ID" => $v,
								"ContentType" => "objectFile",
								"level" => 0
							);
						}
						foreach($finalClasses as $k => $v){
							$ids[] = array(
								"ID" => $v,
								"ContentType" => "object",
								"level" => 0
							);
						}
						$xmlExIm->setOptions($this->exportVars);
						$xmlExIm->prepareExport($ids);
						$_SESSION['weS']['exportVars']["RefTable"] = $xmlExIm->RefTable->RefTable2Array();
						$all = count($xmlExIm->RefTable);
						$exports = 0;
						$_SESSION['weS']['exportVars']["filename"] = ($export_local ? TEMP_PATH . '/' . $filename : $_SERVER['DOCUMENT_ROOT'] . $path . $filename);
						//FIXME set export type in getHeader
						$ret = weFile::save($_SESSION['weS']['exportVars']["filename"], weXMLExIm::getHeader(), "wb");
					} else{
						$xmlExIm->RefTable->Array2RefTable($this->exportVars["RefTable"]);
						$xmlExIm->RefTable->current = $this->exportVars["CurrentRef"];
						$all = count($xmlExIm->RefTable->Storage);
						$ref = $xmlExIm->RefTable->getNext();
						if(!empty($ref->ID) && !empty($ref->ContentType))
							$xmlExIm->exportChunk($ref->ID, $ref->ContentType, $filename);
						$exports = $xmlExIm->RefTable->current;
					}

					$percent = 0;
					if($all != 0){
						$percent = (int) (($exports / $all) * 100);
					}
					if($percent < 0){
						$percent = 0;
					} else if($percent > 100){
						$percent = 100;
					}
					$_progress_update =
						we_html_element::jsElement('
								if (top.footer.setProgress) top.footer.setProgress(' . $percent . ');
					');
					$_SESSION['weS']['exportVars']["CurrentRef"] = $xmlExIm->RefTable->current;

					$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "load")) .
						we_html_element::htmlHidden(array("name" => "all", "value" => $all)) .
						we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_wexport"));

					$head = we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET;

					if($all > $exports){
						return we_html_element::htmlDocType() . we_html_element::htmlHtml(
								we_html_element::htmlHead($head) .
								we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => "5", "marginheight" => "5", "leftmargin" => "5", "topmargin" => "5", "onLoad" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "load", "action" => $this->frameset), $hiddens) . $_progress_update
								)
						);
					}
					if(is_writable($filename)){
						weFile::save($filename, weXMLExIm::getFooter(), "ab");
					}
					$_progress_update =
						we_html_element::jsElement('if (top.footer.setProgress) top.footer.setProgress(100);');
					return we_html_element::htmlDocType() . we_html_element::htmlHtml(
							we_html_element::htmlHead($head . $_progress_update) .
							we_html_element::htmlBody(
								array(
									"bgcolor" => "#ffffff",
									"marginwidth" => "5",
									"marginheight" => "5",
									"leftmargin" => "5",
									"topmargin" => "5",
									"onLoad" => oldHtmlspecialchars($export_local ? ($this->bodyFrame . ".location='" . $this->frameset . "?pnt=body&step=10&file_name=" . urlencode($filename) . "';" . $this->footerFrame . ".location='" . $this->frameset . "?pnt=footer&step=10';") : ( we_message_reporting::getShowMessageCall(g_l('export', "[server_finished]"), we_message_reporting::WE_MESSAGE_NOTICE) . ";top.close();")))), null
					);
			}
		}

		return $out;
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = ""){

		$js = we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
				function formFileChooser() {
					var args = "";
					var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if (i < (arguments.length - 1)){ url += "&"; }}
					switch (arguments[0]) {
						case "browse_server":
							new jsWindow(url,"server_selector",-1,-1,500,300,true,false,true);
						break;
					}
				}
		');

		//javascript:formFileChooser('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value);
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc4 = '';
		$button = we_button::create_button("select", "javascript:formFileChooser('browse_server','" . $wecmdenc1 . "','$filter',document.we_form.elements['$IDName'].value);");

		return $js . we_html_tools::htmlFormElementTable(we_html_tools::getPixel(5, 5) . we_html_tools::htmlTextInput($IDName, 42, $IDValue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", "", we_html_tools::getPixel(20, 4), we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = "", $rootDirID = 0, $table = FILE_TABLE, $Pathname = "ParentPath", $Pathvalue = "", $IDName = "ParentID", $IDValue = "", $cmd = ""){
		$table = FILE_TABLE;

		$js = we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
				function formDirChooser() {
					var args = "";
					var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if (i < (arguments.length - 1)){ url += "&"; }}
					switch (arguments[0]) {
						case "openDirselector":
							new jsWindow(url,"dir_selector",-1,-1,' . WINDOW_DIRSELECTOR_WIDTH . ',' . WINDOW_DIRSELECTOR_HEIGHT . ',true,false,true true);
						break;
					}
				}
		');

		//javascript:formDirChooser('openDirselector',document.we_form.elements['$IDName'].value,'$table','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','".session_id()."','$rootDirID')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:formDirChooser('openDirselector',document.we_form.elements['$IDName'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID')");
		return $js . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden(array("name" => $IDName, "value" => $IDValue)), we_html_tools::getPixel(20, 4), $button);
	}

	function getHTMLDocType($width = 350){
		$pop = "";
		$vals = array();

		$q = getDoctypeQuery($this->db);
		$this->db->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " $q");
		$select = new we_html_select(array("name" => "doctype", "size" => "1", "class" => "weSelect", "style" => "{width: $width;}", "onChange" => ""));
		$first = "";
		while($this->db->next_record()) {
			if($first == "")
				$first = $this->db->f("ID");
			$select->addOption($this->db->f("ID"), $this->db->f("DocType"));
		}

		$doctype = $this->exportVars["doctype"];
		$type = $this->exportVars["type"];
		$dir = $this->exportVars["dir"];

		$select->selectOption($doctype);

		$path = $dir ? f("SELECT Path FROM " . FILE_TABLE . " WHERE ID=" . intval($dir), "Path", $this->db) : "/";
		$dir = we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, $width, 0, "dir", $dir, "Path", $path), g_l('export', "[dir]"));

		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 3, 2);
		$table->setColContent(0, 1, $select->getHtml());
		$table->setColContent(1, 0, we_html_tools::getPixel(defined("OBJECT_FILES_TABLE") ? 25 : 0, 5));
		$table->setColContent(2, 1, $dir);

		$headline = defined("OBJECT_FILES_TABLE") ?
			we_forms::radiobutton("doctype", ($type == "doctype" ? true : ($type != "classname" ? true : false)), "type", g_l('export', "[doctypename]"), true, "defaultfont", $this->topFrame . ".type='doctype'") :
			we_html_element::htmlSpan(array("class" => "defaultfont"), g_l('export', "[doctypename]"));

		return we_html_tools::htmlFormElementTable(
				$table->getHtml(), $headline
		);
	}

	function getHTMLObjectType($width = 350, $showdocs = false){
		if(defined("OBJECT_FILES_TABLE")){
			$vals = array();
			$this->db->query("SELECT ID,Text FROM " . OBJECT_TABLE);
			$select = new we_html_select(array("name" => "classname", "class" => "weSelect", "size" => "1", "style" => "{width: $width}", "onChange" => $this->topFrame . ".classname=document.we_form.classname.options[document.we_form.classname.selectedIndex].value;"));
			$first = "";
			while($this->db->next_record()) {
				if($first == "")
					$first = $this->db->f("ID");
				$select->addOption($this->db->f("ID"), $this->db->f("Text"));
			}

			$classname = $this->exportVars["classname"];


			$js = we_html_element::jsElement('
					' . $this->topFrame . '.classname="' . $classname . '";
			');
			$select->selectOption($classname);

			if(isset($_REQUEST["type"]))
				$type = $_REQUEST["type"];
			else
				$type = "";

			$radio = $showdocs ? we_forms::radiobutton("classname", ($type == "classname" ? true : false), "type", g_l('export', "[classname]"), true, "defaultfont", $this->topFrame . ".type='classname'") : we_html_tools::getPixel(25, 5) . g_l('export', "[classname]");
			return $js . we_html_tools::htmlFormElementTable(we_html_tools::getPixel(25, 5) . $select->getHtml(), $radio);
		} else{
			return null;
		}
	}

	function getHTMLCategory(){
		if(isset($_REQUEST["wcmd"])){
			switch($_REQUEST["wcmd"]){
				case "add_cat":
					$arr = makeArrayFromCSV($this->exportVars["categories"]);
					if(isset($_REQUEST["cat"])){
						$ids = makeArrayFromCSV($_REQUEST["cat"]);
						foreach($ids as $id){
							if(strlen($id) && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->exportVars["categories"] = makeCSVFromArray($arr, true);
					}
					break;
				case "del_cat":
					$arr = makeArrayFromCSV($this->exportVars["categories"]);
					if(isset($_REQUEST["cat"])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST["cat"])
								array_splice($arr, $k, 1);
						}
						$this->exportVars["categories"] = makeCSVFromArray($arr, true);
					}
					break;
				case "del_all_cats":
					$this->exportVars["categories"] = "";
					break;
				default:
			}
		}

		//$js=we_html_element::jsElement($this->topFrame.'.categories="'.(isset($_REQUEST["categories"]) ? $_REQUEST["categories"] : "").'";');

		$hiddens = we_html_element::htmlHidden(array("name" => "wcmd", "value" => "")) .
			we_html_element::htmlHidden(array("name" => "categories", "value" => $this->exportVars["categories"])) .
			we_html_element::htmlHidden(array("name" => "cat", "value" => (isset($_REQUEST["cat"]) ? $_REQUEST["cat"] : "")));


		$delallbut = we_button::create_button("delete_all", "javascript:we_cmd('del_all_cats')", true, -1, -1, "", "", (isset($this->exportVars["categories"]) ? false : true));
		$addbut = we_button::create_button("add", "javascript:we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener." . $this->bodyFrame . ".we_cmd(\\'add_cat\\',top.allIDs);')");
		$cats = new MultiDirChooser(350, $this->exportVars["categories"], "del_cat", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CATEGORY_TABLE);

		if(!we_hasPerm("EDIT_KATEGORIE")){
			$cats->isEditable = false;
		}
		return '<table border="0"  cellpadding="0" cellspacing="0"><tr><td>' . (defined("OBJECT_FILES_TABLE") ? we_html_tools::getPixel(25, 2) : "") . '</td><td>' .
			$hiddens . we_html_tools::htmlFormElementTable($cats->get(), g_l('export', "[categories]"), "left", "defaultfont") .
			'</td></tr></table>';
	}

	function formWeChooser($table = FILE_TABLE, $width = "", $rootDirID = 0, $IDName = "ID", $IDValue = "0", $Pathname = "Path", $Pathvalue = "/", $cmd = ""){
		$yuiSuggest = & weSuggest::getInstance();
		if($Pathvalue == ""){
			$Pathvalue = f("SELECT Path FROM " . $this->db->escape($table) . " WHERE ID=" . intval($IDValue), "Path", $this->db);
		}

		//javascript:we_cmd('openDirselector',document.we_form.elements['$IDName'].value,'$table','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','".session_id()."','$rootDirID')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:we_cmd('openDirselector',document.we_form.elements['$IDName'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID')");

		$yuiSuggest->setAcId("Dir");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($Pathname, $Pathvalue);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button, 10);

		return $yuiSuggest->getHTML();
	}

	function getHTMLChooser($name, $value, $values, $title){
		$input_size = 5;

		$select = new we_html_select(array("name" => $name . "_select", "class" => "weSelect", "onChange" => "document.we_form." . $name . ".value=this.options[this.selectedIndex].value;this.selectedIndex=0", "style" => "width:200;"));
		$select->addOption("", "");
		foreach($values as $k => $v)
			$select->addOption(oldHtmlspecialchars($k), oldHtmlspecialchars($v));

		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "250"), 1, 3);

		$table->setColContent(0, 0, we_html_tools::htmlTextInput($name, $input_size, $value));
		$table->setColContent(0, 1, we_html_tools::getPixel(10, 10));
		$table->setColContent(0, 2, $select->getHtml());

		return we_html_tools::htmlFormElementTable($table->getHtml(), $title);
	}

}

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
class weExportFrames extends weModuleFrames{

	var $View;
	var $SelectionTree;
	var $editorBodyFrame;
	var $_space_size = 130;
	var $_text_size = 75;
	var $_width_size = 535;

	function __construct(){
		parent::__construct(WE_EXPORT_MODULE_DIR . "edit_export_frameset.php");
		$this->Tree = new weExportTreeMain();
		$this->SelectionTree = new weExportTree();
		$this->View = new weExportView(WE_EXPORT_MODULE_DIR . "edit_export_frameset.php", "top.content");
		$this->setupTree(EXPORT_TABLE, "top.content", "top.content.resize.left.tree", "top.content.cmd");
		$this->module = "export";

		$this->editorBodyFrame = $this->topFrame . '.resize.right.editor.edbody';
	}

	function getHTML($what){
		switch($what){
			case "frameset": print $this->getHTMLFrameset();
				break;
			case "header": print $this->getHTMLHeader();
				break;
			case "resize": print $this->getHTMLResize();
				break;
			case "left": print $this->getHTMLLeft();
				break;
			case "right": print $this->getHTMLRight();
				break;
			case "editor": print $this->getHTMLEditor();
				break;
			case "edheader": print $this->getHTMLEditorHeader();
				break;
			case "edbody": print $this->getHTMLEditorBody();
				break;
			case "edfooter": print $this->getHTMLEditorFooter();
				break;
			case "load":
			case "cmd": print $this->getHTMLCmd();
				break;
			case "treeheader": print $this->getHTMLTreeHeader();
				break;
			case "treefooter": print $this->getHTMLTreeFooter();
				break;

			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset(){
		$this->View->export->clearSessionVars();
		return weModuleFrames::getHTMLFrameset();
	}

	function getJSCmdCode(){
		return $this->View->getJSTop() .
			we_html_element::jsElement($this->Tree->getJSMakeNewEntry() .
				$this->Tree->getJSUpdateItem()
		);
	}

	function getHTMLEditorHeader(){
		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$we_tabs = new we_tabs();
		$we_tabs->addTab(new we_tab("#", g_l('export', '[property]'), '((' . $this->topFrame . '.activ_tab==1) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('1');", array("id" => "tab_1")));
		if($this->View->export->IsFolder == 0){
			$we_tabs->addTab(new we_tab("#", g_l('export', '[options]'), '((' . $this->topFrame . '.activ_tab==2) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('2');", array("id" => "tab_2")));
			$we_tabs->addTab(new we_tab("#", g_l('export', '[log]'), '((' . $this->topFrame . '.activ_tab==3) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('3');", array("id" => "tab_3")));
		}

		$we_tabs->onResize();
		$tabsHead = $we_tabs->getHeader();
		$tabsBody = $we_tabs->getJS();

		$js = we_html_element::jsElement('
				function setTab(tab) {
					parent.edbody.toggle("tab"+' . $this->topFrame . '.activ_tab);
					parent.edbody.toggle("tab"+tab);
					' . $this->topFrame . '.activ_tab=tab;
				}

				' . ($this->View->export->ID ? '' : $this->topFrame . '.activ_tab=1;') . '

				' . ($this->View->export->IsFolder == 1 ? $this->topFrame . '.activ_tab=1;' : '') . '

				top.content.hloaded = 1;
		');

		$tabsHead .=$js;

		$table = new we_html_table(array("width" => "3000", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 3, 1);

		$table->setCol(0, 0, array(), we_html_tools::getPixel(1, 3));

		$table->setCol(1, 0, array("valign" => "top", "class" => "small"), we_html_tools::getPixel(15, 2) .
			we_html_element::htmlB(
				g_l('export', '[export]') . ':&nbsp;' . $this->View->export->Text .
				we_html_tools::getPixel(1600, 19)
			)
		);
		$text = !empty($this->View->export->Path) ? $this->View->export->Path : "/" . $this->View->export->Text;
		$extraJS = 'document.getElementById("tab_"+top.content.activ_tab).className="tabActive";';
		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0", "onload" => "setFrameSize()", "onresize" => "setFrameSize()"),
				//	'<div id="main" >' . we_html_tools::getPixel(100,3) . '<div style="margin:0px;" id="headrow">&nbsp;'.we_html_element::htmlB(g_l('export','[export]') . ':&nbsp;'.$this->View->export->Text).'</div>' . we_html_tools::getPixel(100,3) .
				'<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", we_html_element::htmlB(g_l('export', '[export]'))) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) .
				$we_tabs->getHTML() .
				'</div>' . we_html_element::jsElement($extraJS)
//			$js.
//			$table->getHtml() .
//			$tabsBody
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	function getHTMLEditorBody(){

		$hiddens = array('cmd' => 'edit_export', 'pnt' => 'edbody');

		if(isset($_REQUEST["home"]) && $_REQUEST["home"]){
			$hiddens["cmd"] = "home";
			$GLOBALS["we_print_not_htmltop"] = true;
			$GLOBALS["we_head_insert"] = $this->View->getJSProperty();
			$GLOBALS["we_body_insert"] = we_html_element::htmlForm(array("name" => "we_form"), $this->View->getCommonHiddens($hiddens) . we_html_element::htmlHidden(array("name" => "home", "value" => "0"))
			);
			$GLOBALS["mod"] = "export";
			ob_start();
			include(WE_MODULES_PATH . 'home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}
		$yuiSuggest = & weSuggest::getInstance();
		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onLoad" => "loaded=1;start();", "onunload" => "doUnload()"), $yuiSuggest->getYuiJsFiles() . we_html_element::htmlForm(array("name" => "we_form"), $this->View->getCommonHiddens($hiddens) . $this->getHTMLProperties()) . $yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs()
		);
		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	function getHTMLEditorFooter(){
		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		$col = 0;
		$table1 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "3000"), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1600, 10));

		$table2 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "210"), 1, 5);
		$table2->setRow(0, array("valign" => "middle"));
		$table2->setCol(0, $col++, array("nowrap" => null), we_html_tools::getPixel(5, 5));
		$table2->setCol(0, $col++, array("nowrap" => null), we_button::create_button("save", "javascript:we_save()")
		);

		$table2->setCol(0, $col++, array("nowrap" => null), we_html_tools::getPixel(5, 5));

		if($this->View->export->IsFolder == 0){
			$table2->setCol(0, $col++, array("nowrap" => null), we_button::create_button("export", "javascript:top.content.we_cmd('start_export')", true, 100, 22, '', '', !we_hasPerm("MAKE_EXPORT"))
			);
		}

		$table2->setCol(0, $col++, array("nowrap" => null), we_html_tools::getPixel(290, 5));

		$js = we_html_element::jsElement('
				function we_save() {
					top.content.we_cmd("save_export");

				}
				function doProgress(progress) {
					var elem = document.getElementById("progress");
					if(elem.style.display == "none") elem.style.display = "";
					setProgress(progress);
				}

				function hideProgress() {
					var elem = document.getElementById("progress");
					if(elem.style.display != "none") elem.style.display = "none";
				}

		');

		$text = g_l('export', '[working]');
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

		$table2->setCol(0, 4, array("id" => "progress", "style" => "display: none", "nowrap" => null), $progressbar->getHtml());

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => "15", "marginheight" => "0", "leftmargin" => "15", "topmargin" => "0"), we_html_element::htmlForm(array(), $table1->getHtml() . $table2->getHtml())
				), (isset($progressbar) ? $progressbar->getJSCode() . "\n" : "") . $js
		);
	}

	function getHTMLProperties($preselect = ""){
		$this->SelectionTree->init($this->frameset, $this->editorBodyFrame, $this->editorBodyFrame, $this->cmdFrame);

		$out = "";
		$tabNr = isset($_REQUEST["tabnr"]) ? $_REQUEST["tabnr"] : 1;

		$out .= we_html_element::jsElement('

			var log_counter=0;
			function toggle(id){
				var elem = document.getElementById(id);
				if(elem.style.display == "none") elem.style.display = "";
				else elem.style.display = "none";
			}


			function clearLog(){
				' . $this->editorBodyFrame . '.document.getElementById("log").innerHTML = "";
			}

			function addLog(text){
				' . $this->editorBodyFrame . '.document.getElementById("log").innerHTML+= text;
				' . $this->editorBodyFrame . '.document.getElementById("log").scrollTop = 50000;
			}


		');

		$out .= we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')), we_multiIconBox::getHTML('', "100%", $this->getHTMLTab1(), 30, '', -1, '', '', false, $preselect)) .
			we_html_element::htmlDiv(array('id' => 'tab2', 'style' => ($tabNr == 2 ? '' : 'display: none')), we_multiIconBox::getHTML('', "100%", $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect)) .
			we_html_element::htmlDiv(array('id' => 'tab3', 'style' => ($tabNr == 3 ? '' : 'display: none')), we_multiIconBox::getHTML('', "100%", $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect));

		return $out;
	}

	function getHTMLTab1(){
		$parts = array();
		array_push($parts, array(
			"headline" => g_l('export', "[property]"),
			"html" => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Text", '', $this->View->export->Text, '', 'style="width: ' . $this->_width_size . 'px;" id="yuiAcInputPathName" onchange="top.content.setHot();" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()" onChange="' . $this->topFrame . '.hot=1;"'), g_l('export', '[name]')) . '<br>' .
			$this->getHTMLDirChooser(),
			"space" => $this->_space_size)
		);

		if($this->View->export->IsFolder == 1)
			return $parts;

		array_push($parts, array(
			"headline" => g_l('export', "[export_to]"),
			"html" => we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Filename", $this->_text_size, $this->View->export->Filename, '', 'style="width: ' . $this->_width_size . 'px;" onChange="' . $this->topFrame . '.hot=1;"'), g_l('export', "[filename]")),
			"space" => $this->_space_size,
			"noline" => 1)
		);

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);
		$table->setColContent(0, 0, we_html_tools::htmlSelect('ExportTo', array('local' => g_l('export', '[export_to_local]'), "server" => g_l('export', "[export_to_server]")), 1, $this->View->export->ExportTo, false, 'onChange="toggle(\'save_to\');' . $this->topFrame . '.hot=1;"', 'value', $this->_width_size));
		$table->setColContent(1, 0, we_html_tools::getPixel(10, 10));
		$table->setCol(2, 0, array("id" => "save_to", "style" => ($this->View->export->ExportTo == 'server' ? 'display: ""' : 'display: none')), we_html_tools::htmlFormElementTable($this->formFileChooser(($this->_width_size - 120), "ServerPath", $this->View->export->ServerPath, "", "folder"), g_l('export', "[save_to]")));


		array_push($parts, array(
			"headline" => "",
			"html" => $table->getHtml(),
			"space" => $this->_space_size)
		);

		$js = we_html_element::jsElement('

			function closeAllSelection(){
				var elem = document.getElementById("auto");
				elem.style.display = "none";
				elem = document.getElementById("manual");
				elem.style.display = "none";
			}
			function closeAllType(){
				var elem = document.getElementById("doctype");
				elem.style.display = "none";
				' . (defined("OBJECT_TABLE") ? '
				elem = document.getElementById("classname");
				elem.style.display = "none";' : '') . '
			}

		');

		$docTypes = array();
		$q = getDoctypeQuery($this->db);
		$this->db->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " $q");
		while($this->db->next_record()) {
			$docTypes[$this->db->f("ID")] = $this->db->f("DocType");
		}

		if(defined("OBJECT_TABLE")){
			$classNames = array();
			$this->db->query("SELECT ID,Text FROM " . OBJECT_TABLE);
			while($this->db->next_record()) {
				$classNames[$this->db->f("ID")] = $this->db->f("Text");
			}
		}

		$FolderPath = $this->View->export->Folder ? f("SELECT Path FROM " . FILE_TABLE . " WHERE ID=" . intval($this->View->export->Folder), "Path", $this->db) : "/";

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 5, 1);

		$seltype = array('doctype' => g_l('export', "[doctypename]"));
		if(defined("OBJECT_TABLE"))
			$seltype['classname'] = g_l('export', '[classname]');

		$table->setColContent(0, 0, we_html_tools::htmlSelect('SelectionType', $seltype, 1, $this->View->export->SelectionType, false, 'onChange="closeAllType();toggle(this.value);' . $this->topFrame . '.hot=1;"', 'value', $this->_width_size));
		$table->setColContent(1, 0, we_html_tools::getPixel(5, 5));
		$table->setCol(2, 0, array("id" => "doctype", "style" => ($this->View->export->SelectionType == 'doctype' ? 'display: ""' : 'display: none')), we_html_tools::htmlSelect('DocType', $docTypes, 1, $this->View->export->DocType, false, 'onChange="' . $this->topFrame . '.hot=1;"', 'value', $this->_width_size) .
			we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, ($this->_width_size - 120), 0, 'Folder', $this->View->export->Folder, 'FolderPath', $FolderPath), g_l('export', '[dir]'))
		);
		if(defined("OBJECT_TABLE")){
			$table->setCol(3, 0, array("id" => "classname", "style" => ($this->View->export->SelectionType == "classname" ? "display: ''" : "display: none")), we_html_tools::htmlSelect('ClassName', $classNames, 1, $this->View->export->ClassName, false, 'onChange="' . $this->topFrame . '.hot=1;"', 'value', $this->_width_size)
			);
		}

		$table->setColContent(4, 0, $this->getHTMLCategory());

		$selectionTypeHtml = $table->getHTML();

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 4, 1);
		$table->setColContent(0, 0, we_html_tools::htmlSelect('Selection', array('auto' => g_l('export', "[auto_selection]"), "manual" => g_l('export', "[manual_selection]")), 1, $this->View->export->Selection, false, 'onChange="closeAllSelection();toggle(this.value);closeAllType();toggle(\'doctype\');' . $this->topFrame . '.hot=1;"', 'value', $this->_width_size));
		$table->setColContent(1, 0, we_html_tools::getPixel(5, 5));
		$table->setCol(2, 0, array("id" => "auto", "style" => ($this->View->export->Selection == 'auto' ? 'display: ""' : 'display: none')), we_html_tools::htmlAlertAttentionBox(g_l('export', "[txt_auto_selection]"), 2, $this->_width_size) .
			$selectionTypeHtml
		);

		$table->setCol(3, 0, array("id" => "manual", "style" => ($this->View->export->Selection == "manual" ? "display: ''" : "display: none")), we_html_tools::htmlAlertAttentionBox(g_l('export', "[txt_manual_selection]") . " " . g_l('export', "[select_export]"), 2, $this->_width_size) .
			$this->SelectionTree->getHTMLMultiExplorer($this->_width_size, 200)
		);

		array_push($parts, array(
			"headline" => g_l('export', '[selection]'),
			"html" => $js . $table->getHtml(),
			"space" => $this->_space_size)
		);

		return $parts;
	}

	function getHTMLTab2(){
		$parts = array();
		$formattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 5, 1);
		$formattable->setCol(0, 0, null, we_forms::checkboxWithHidden($this->View->export->HandleDefTemplates, "HandleDefTemplates", g_l('export', "[handle_def_templates]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(1, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleDocIncludes ? true : false), "HandleDocIncludes", g_l('export', "[handle_document_includes]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		if(defined("OBJECT_TABLE"))
			$formattable->setCol(2, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleObjIncludes ? true : false), "HandleObjIncludes", g_l('export', "[handle_object_includes]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(3, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleDocLinked ? true : false), "HandleDocLinked", g_l('export', "[handle_document_linked]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(4, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleThumbnails ? true : false), "HandleThumbnails", g_l('export', "[handle_thumbnails]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));

		array_push($parts, array(
			"headline" => g_l('export', "[handle_document_options]") . we_html_element::htmlBr() . g_l('export', "[handle_template_options]"),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_document_options]'), 2, $this->_width_size, true, 70) . $formattable->getHtml(),
			"space" => $this->_space_size)
		);

		if(defined("OBJECT_TABLE")){
			$formattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 3, 1);
			$formattable->setCol(0, 0, array("colspan" => "2"), we_forms::checkboxWithHidden(($this->View->export->HandleDefClasses ? true : false), "HandleDefClasses", g_l('export', "[handle_def_classes]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
			$formattable->setCol(1, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleObjEmbeds ? true : false), "HandleObjEmbeds", g_l('export', "[handle_object_embeds]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
			array_push($parts, array(
				"headline" => g_l('export', "[handle_object_options]") . we_html_element::htmlBr() . g_l('export', "[handle_classes_options]"),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_object_options]'), 2, $this->_width_size, true, 70) . $formattable->getHtml(),
				"space" => $this->_space_size)
			);
		}

		$formattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 3, 1);
		$formattable->setCol(0, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleDoctypes ? true : false), "HandleDoctypes", g_l('export', "[handle_doctypes]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(1, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleCategorys ? true : false), "HandleCategorys", g_l('export', "[handle_categorys]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));
		$formattable->setCol(2, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleNavigation ? true : false), "HandleNavigation", g_l('export', "[handle_navigation]"), false, 'defaultfont', $this->topFrame . '.hot=1;', false, g_l('export', "[navigation_hint]"), 1, 509));

		array_push($parts, array(
			"headline" => g_l('export', "[handle_doctype_options]"),
			"html" => $formattable->getHtml(),
			"space" => $this->_space_size)
		);

		array_push($parts, array(
			"headline" => g_l('export', "[export_depth]"),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_exportdeep_options]'), 2, $this->_width_size) . '<br>' . we_html_element::htmlLabel(null, g_l('export', "[to_level]")) . we_html_tools::getPixel(5, 5) . we_html_tools::htmlTextInput("ExportDepth", 10, $this->View->export->ExportDepth, "", "onBlur=\"var r=parseInt(this.value);if(isNaN(r)) this.value=" . $this->View->export->ExportDepth . "; else{ this.value=r; " . $this->topFrame . ".hot=1;}\"", "text", 50),
			"space" => $this->_space_size)
		);

		$formattable = new we_html_table(array("cellpadding" => 2, "cellspacing" => 2, "border" => 0), 1, 1);
		$formattable->setCol(0, 0, null, we_forms::checkboxWithHidden(($this->View->export->HandleOwners ? true : false), "HandleOwners", g_l('export', "[handle_owners]"), false, 'defaultfont', $this->topFrame . '.hot=1;'));

		array_push($parts, array(
			"headline" => g_l('export', "[handle_owners_option]"),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('export', '[txt_owners]'), 2, $this->_width_size) . $formattable->getHtml(),
			"space" => $this->_space_size)
		);


		return $parts;
	}

	function getHTMLTab3(){
		$parts = array();

		array_push($parts, array(
			"headline" => '',
			"html" => we_html_element::htmlDiv(array('class' => 'blockwrapper', 'style' => 'width: 650px; height: 400px; border:1px #dce6f2 solid;', 'id' => 'log'), ''),
			"space" => 0)
		);
		return $parts;
	}

	function getHTMLDirChooser(){

		$path = id_to_path($this->View->export->ParentID, EXPORT_TABLE);

		//javascript:top.content.setHot();we_cmd('openExportDirselector',document.we_form.elements['ParentID'].value,'document.we_form.elements[\'ParentID\'].value','document.we_form.elements[\'ParentPath\'].value','top.hot=1;')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['ParentID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['ParentPath'].value");
		$wecmdenc3 = we_cmd_enc("top.hot=1;");

		$button = we_button::create_button('select', "javascript:top.content.setHot();we_cmd('openExportDirselector',document.we_form.elements['ParentID'].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "')");

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("PathGroup");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("ParentPath", $path, array("onChange" => $this->topFrame . '.hot=1;'));
		$yuiSuggest->setLabel(g_l('export', "[group]"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult("ParentID", $this->View->export->ParentID);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setTable(EXPORT_TABLE);
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	function getHTMLLeft(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "1,*,0"));
		$frameset->addFrame(array("src" => HTML_DIR . "whiteWithTopLine.html", "name" => "treeheader", "noresize" => null, "scrolling" => "no"));

		$frameset->addFrame(array("src" => WEBEDITION_DIR . "treeMain.php", "name" => "tree", "noresize" => null, "scrolling" => "auto"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=treefooter", "name" => "treefooter", "noresize" => null, "scrolling" => "no"));

		// set and return html code
		$body = $frameset->getHtml() . "\n" . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLTreeHeader(){

		return "";
	}

	function getHTMLTreeFooter(){

		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => "5", "marginheight" => "0", "leftmargin" => "5", "topmargin" => "0"), ""
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLCmd(){
		$out = "";
		@set_time_limit(0);
		if(isset($_REQUEST["cmd"])){
			switch($_REQUEST["cmd"]){
				case "load":
					if(isset($_REQUEST["pid"])){
						$out = we_html_element::jsElement("self.location='" . WE_INCLUDES_DIR . "we_export/exportLoadTree.php?we_cmd[1]=" . $_REQUEST["tab"] . "&we_cmd[2]=" . $_REQUEST["pid"] . "&we_cmd[3]=" . (isset($_REQUEST["openFolders"]) ? $_REQUEST["openFolders"] : "") . "&we_cmd[4]=" . $this->editorBodyFrame . "'");
					}
					break;
				case "mainload":
					if(isset($_REQUEST["pid"])){

						$treeItems = weExportTreeLoader::getItems($_REQUEST["pid"]);

						$js = 'if(!' . $this->Tree->topFrame . '.treeData) {
								' . we_message_reporting::getShowMessageCall("A fatal Error ocured", we_message_reporting::WE_MESSAGE_ERROR) . '
							}';

						if(!$_REQUEST["pid"])
							$js.=$this->Tree->topFrame . '.treeData.clear();' .
								$this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $_REQUEST["pid"] . '\',\'root\',\'root\'));';

						$js.=$this->Tree->getJSLoadTree($treeItems);
						$out = we_html_element::jsElement($js);
					}
					break;
				case "do_export":
					if(!we_hasPerm("MAKE_EXPORT")){
						$out = we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('export', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR)
						);
						break;
					}


					$_progress_update = '';
					$exports = 0;
					if(!isset($_SESSION['weS']['ExImRefTable'])){

						if($this->View->export->Selection == 'manual'){
							$finalDocs = makeArrayFromCSV($this->View->export->selDocs);
							$finalTempl = makeArrayFromCSV($this->View->export->selTempl);
							$finalObjs = makeArrayFromCSV($this->View->export->selObjs);
							$finalClasses = makeArrayFromCSV($this->View->export->selClasses);
						} else{
							$finalDocs = array();
							$finalTempl = array();
							$finalObjs = array();
							$finalClasses = array();
						}
						$xmlExIm = new weXMLExport();
						$xmlExIm->getSelectedItems($this->View->export->Selection, "wxml", "", $this->View->export->SelectionType, $this->View->export->DocType, $this->View->export->ClassName, $this->View->export->Categorys, $this->View->export->Folder, $finalDocs, $finalTempl, $finalObjs, $finalClasses);



						$xmlExIm->setOptions(array(
							"handle_def_templates" => $this->View->export->HandleDefTemplates,
							"handle_doctypes" => $this->View->export->HandleDoctypes,
							"handle_categorys" => $this->View->export->HandleCategorys,
							"handle_def_classes" => $this->View->export->HandleDefClasses,
							"handle_document_includes" => $this->View->export->HandleDocIncludes,
							"handle_document_linked" => $this->View->export->HandleDocLinked,
							"handle_object_includes" => $this->View->export->HandleObjIncludes,
							"handle_object_embeds" => $this->View->export->HandleObjEmbeds,
							"handle_class_defs" => $this->View->export->HandleDefClasses,
							"handle_owners" => $this->View->export->HandleOwners,
							"export_depth" => $this->View->export->ExportDepth,
							"handle_documents" => 1,
							"handle_templates" => 1,
							"handle_classes" => 1,
							"handle_objects" => 1,
							"handle_navigation" => $this->View->export->HandleNavigation,
							"handle_thumbnails" => $this->View->export->HandleThumbnails
						));

						$xmlExIm->RefTable->reset();
						$xmlExIm->savePerserves();

						$all = $xmlExIm->RefTable->getLastCount();
						$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
							we_html_element::htmlHidden(array("name" => "all", "value" => $all)) .
							we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_export"));

						$out = we_html_element::htmlDocType() . we_html_element::htmlHtml(
								we_html_element::htmlHead('') .
								we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => "5", "marginheight" => "5", "leftmargin" => "5", "topmargin" => "5", "onLoad" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "action" => $this->frameset), $hiddens)
								)
						);
					} else if($_SESSION['weS']['ExImPrepare']){
						$xmlExIm = new weExportPreparer();

						$xmlExIm->loadPerserves();
						$xmlExIm->prepareExport();
						$all = count($xmlExIm->RefTable->Storage) - 1;
						$xmlExIm->prepare = ($all > $xmlExIm->RefTable->current) && ($xmlExIm->RefTable->current != 0);



						$_progress_update = '';
						if(!$xmlExIm->prepare){
							$_progress_update =
								we_html_element::jsElement('
										if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(10, 10)) . we_html_element::htmlB(g_l('export', '[start_export]') . ' - ' . date("d.m.Y H:i:s")) . '<br><br>");
										if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(20, 5)) . we_html_element::htmlB(g_l('export', '[prepare]')) . '<br>");
										if (' . $this->topFrame . '.resize.right.editor.edfooter.doProgress) ' . $this->topFrame . '.resize.right.editor.edfooter.doProgress(0);
										if(' . $this->topFrame . '.resize.right.editor.edfooter.setProgressText) ' . $this->topFrame . '.resize.right.editor.edfooter.setProgressText("current_description","' . g_l('export', '[working]') . '");
										if (' . $this->editorBodyFrame . '.addLog){
										' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(20, 5)) . we_html_element::htmlB(g_l('export', '[export]')) . '<br>");
									}
								');
						//FIXME: set export type in getHeader
						weFile::save($this->View->export->ExportFilename, weXMLExIm::getHeader(), "wb");
							if($this->View->export->HandleOwners){
								weFile::save($this->View->export->ExportFilename, weXMLExport::exportInfoMap($xmlExIm->RefTable->Users), "ab");
							}

							$xmlExIm->RefTable->reset();
						} else{

							$percent = 0;
							if($all != 0)
								$percent = (int) (($xmlExIm->RefTable->current / $all) * 100);

							if($percent < 0){
								$percent = 0;
							} else if($percent > 100){
								$percent = 100;
							}
							$_progress_update =
								we_html_element::jsElement('
									if (' . $this->topFrame . '.resize.right.editor.edfooter.doProgress) ' . $this->topFrame . '.resize.right.editor.edfooter.doProgress("' . $percent . '");
									if(' . $this->topFrame . '.resize.right.editor.edfooter.setProgressText) ' . $this->topFrame . '.resize.right.editor.edfooter.setProgressText("current_description","' . g_l('export', '[prepare]') . '");
							');
						}

						$xmlExIm->savePerserves();

						$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
							we_html_element::htmlHidden(array("name" => "all", "value" => $all)) .
							we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_export"));

						$out = we_html_element::htmlDocType() . we_html_element::htmlHtml(
								we_html_element::htmlHead('') .
								we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => "5", "marginheight" => "5", "leftmargin" => "5", "topmargin" => "5", "onLoad" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "action" => $this->frameset), $hiddens) . $_progress_update
								)
						);
					} else{
						$xmlExIm = new weXMLExport();
						$xmlExIm->loadPerserves();
						$exports = 0;

						$all = count($xmlExIm->RefTable->Storage);

						$ref = $xmlExIm->RefTable->getNext();

						if(!empty($ref->ID) && !empty($ref->ContentType)){

							$table = $this->db->escape($ref->Table);

							$exists = f('SELECT ID FROM ' . $table . ' WHERE ID=' . intval($ref->ID), 'ID', $this->db) || ($ref->ContentType == "weBinary");

							if($exists){
								$xmlExIm->export($ref->ID, $ref->ContentType, $this->View->export->ExportFilename);
								$exports = $xmlExIm->RefTable->current;

								if($ref->ContentType == "weBinary"){
									$_progress_update .= "\n" .
										we_html_element::jsElement('
											if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(50, 5)) . we_html_element::htmlB(g_l('export', '[weBinary]')) . '&nbsp;&nbsp;' . $ref->ID . '<br>");
										') . "\n";
								} else{
									if($ref->ContentType == 'doctype'){
										$_path = f('SELECT DocType FROM ' . $table . ' WHERE ID = ' . intval($ref->ID), 'DocType', $this->db);
									} else if($ref->ContentType == 'weNavigationRule'){
										$_path = f('SELECT NavigationName FROM ' . $table . ' WHERE ID = ' . intval($ref->ID), 'NavigationName', $this->db);
									} else if($ref->ContentType == 'weThumbnail'){
										$_path = f('SELECT Name FROM ' . $table . ' WHERE ID = ' . intval($ref->ID), 'Name', $this->db);
									} else{
										$_path = id_to_path($ref->ID, $table);
									}

									$_progress_text = we_html_element::htmlB(g_l('contentTypes', '[' . $ref->ContentType . ']') !== false ? g_l('contentTypes', '[' . $ref->ContentType . ']') : (g_l('export', '[' . $ref->ContentType . ']') !== false ? g_l('export', '[' . $ref->ContentType . ']') : '')) . '&nbsp;&nbsp;' . $_path;

									if(strlen($_path) > 75){
										$_progress_text = addslashes(substr($_progress_text, 0, 65) . '<acronym title="' . $_path . '">...</acronym>' . substr($_progress_text, -10));
									}

									$_progress_update .= "\n" .
										we_html_element::jsElement('
											if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("' . addslashes(we_html_tools::getPixel(50, 5)) . $_progress_text . '<br>");
										');
								}
							}
						}

						$percent = 0;
						if($all != 0)
							$percent = (int) (($exports / $all) * 100);

						if($percent < 0){
							$percent = 0;
						} else if($percent > 100){
							$percent = 100;
						}
						$_progress_update .= "\n" .
							we_html_element::jsElement('
									if (' . $this->topFrame . '.resize.right.editor.edfooter.doProgress) ' . $this->topFrame . '.resize.right.editor.edfooter.doProgress(' . $percent . ');
						') . "\n";
						$_SESSION['weS']['ExImCurrentRef'] = $xmlExIm->RefTable->current;

						$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
							we_html_element::htmlHidden(array("name" => "all", "value" => $all)) .
							we_html_element::htmlHidden(array("name" => "cmd", "value" => "do_export"));

						$head = //FIXME: missing title
							we_html_tools::getHtmlInnerHead() . STYLESHEET;

						if($all > $exports){
							$out = we_html_element::htmlDocType() . we_html_element::htmlHtml(
									we_html_element::htmlHead($head) .
									we_html_element::htmlBody(array("bgcolor" => "#ffffff", "marginwidth" => "5", "marginheight" => "5", "leftmargin" => "5", "topmargin" => "5", "onLoad" => "document.we_form.submit()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "action" => $this->frameset), $hiddens) . $_progress_update
									)
							);
						} else{
							if(is_writable($this->View->export->ExportFilename)){
								weFile::save($this->View->export->ExportFilename, weXMLExIm::getFooter(), "ab");
							}
							$_progress_update .= "\n" .
								we_html_element::jsElement('
									if (' . $this->topFrame . '.resize.right.editor.edfooter.doProgress) ' . $this->topFrame . '.resize.right.editor.edfooter.doProgress(100);
									if (' . $this->editorBodyFrame . '.addLog) ' . $this->editorBodyFrame . '.addLog("<br>' . addslashes(we_html_tools::getPixel(10, 10)) . we_html_element::htmlB(g_l('export', '[end_export]') . ' - ' . date("d.m.Y H:i:s")) . '<br><br>");
							') . "\n" .
								($this->View->export->ExportTo == 'local' ?
									we_html_element::jsElement($this->editorBodyFrame . '.addLog(\'' .
										we_html_element::htmlSpan(array("class" => "defaultfont"), addslashes(we_html_tools::getPixel(10, 1)) . g_l('export', "[backup_finished]") . "<br>" .
											addslashes(we_html_tools::getPixel(10, 1)) . g_l('export', "[download_starting2]") . "<br><br>" .
											addslashes(we_html_tools::getPixel(10, 1)) . g_l('export', "[download_starting3]") . "<br>" .
											addslashes(we_html_tools::getPixel(10, 1)) . we_html_element::htmlB(we_html_element::htmlA(array("href" => $this->frameset . "?pnt=cmd&cmd=upload&exportfile=" . urlencode($this->View->export->ExportFilename)), g_l('export', "[download]"))) . "<br><br>"
										) .
										'\');') :
									''
								);

							$out = we_html_element::htmlDocType() . we_html_element::htmlHtml(
									we_html_element::htmlHead($head . $_progress_update) .
									we_html_element::htmlBody(
										array(
											"bgcolor" => "#ffffff",
											"marginwidth" => "5",
											"marginheight" => "5",
											"leftmargin" => "5",
											"topmargin" => "5",
											"onLoad" => ($this->View->export->ExportTo == 'local' ? ($this->cmdFrame . ".location='" . $this->frameset . "?pnt=cmd&cmd=upload&exportfile=" . urlencode($this->View->export->ExportFilename) . "';") : ( we_message_reporting::getShowMessageCall(g_l('export', "[server_finished]"), we_message_reporting::WE_MESSAGE_NOTICE) )) . $this->topFrame . ".resize.right.editor.edfooter.hideProgress();")
									), null
							);
							$xmlExIm->unsetPerserves();
						}
					}
					break;
				case 'upload':
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

							header("Location: " . $preurl . $this->frameset . "?pnt=cmd&cmd=upload_failed");
							exit;
						}
					} else{
						header("Location: " . $preurl . $this->frameset . "?pnt=cmd&cmd=error=upload_failed");
						exit;
					}

					break;
				case 'upload_failed':
					$out = we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('export', "[error_download_failed]"), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
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
							new jsWindow(url,"server_selector",-1,-1,660,330,true,false,true);
						break;
					}
				}
		');

		//javascript:top.content.setHot();formFileChooser('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value);
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc4 = '';
		$button = we_button::create_button("select", "javascript:top.content.setHot();formFileChooser('browse_server','" . $wecmdenc1 . "','$filter',document.we_form.elements['$IDName'].value);");

		return $js . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 42, $IDValue, "", ' readonly onChange="' . $this->topFrame . '.hot=1;"', "text", $width, 0), "", "left", "defaultfont", "", we_html_tools::getPixel(20, 4), we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	function formWeChooser($table = FILE_TABLE, $width = "", $rootDirID = 0, $IDName = "ID", $IDValue = "0", $Pathname = "Path", $Pathvalue = "/", $cmd = ""){
		if($Pathvalue == ""){
			$Pathvalue = f("SELECT Path FROM " . $this->db->escape($table) . " WHERE ID=" . intval($IDValue) . ";", "Path", $this->db);
		}

		//javascript:top.content.setHot();we_cmd('openDirselector',document.we_form.elements['$IDName'].value,'$table','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','".session_id()."','$rootDirID'))
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:top.content.setHot();we_cmd('openDirselector',document.we_form.elements['$IDName'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID')");
		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("SelPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($Pathname, $Pathvalue, array("onChange" => $this->topFrame . '.hot=1;'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setTable(FILE_TABLE);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	function getHTMLCategory(){
		if(isset($_REQUEST["cmd"])){
			switch($_REQUEST["cmd"]){
				case "add_cat":
					$arr = makeArrayFromCSV($this->View->export->Categorys);
					if(isset($_REQUEST["cat"])){
						$ids = makeArrayFromCSV($_REQUEST["cat"]);
						foreach($ids as $id){
							if(strlen($id) && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->View->export->Categorys = makeCSVFromArray($arr, true);
					}
					break;
				case "del_cat":
					$arr = makeArrayFromCSV($this->View->export->Categorys);
					if(isset($_REQUEST["cat"])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST["cat"])
								array_splice($arr, $k, 1);
						}
						$this->View->export->Categorys = makeCSVFromArray($arr, true);
					}
					break;
				case "del_all_cats":
					$this->View->export->Categorys = "";
					break;
				default:
			}
		}


		$hiddens = we_html_element::htmlHidden(array("name" => "Categorys", "value" => $this->View->export->Categorys)) .
			we_html_element::htmlHidden(array("name" => "cat", "value" => (isset($_REQUEST["cat"]) ? $_REQUEST["cat"] : "")));


		$delallbut = we_button::create_button("delete_all", "javascript:top.content.setHot(); we_cmd('del_all_cats')", true, -1, -1, "", "", (isset($this->View->export->Categorys) ? false : true));
		$addbut = we_button::create_button("add", "javascript:top.content.setHot(); we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener." . $this->editorBodyFrame . ".we_cmd(\\'add_cat\\',top.allIDs);')");
		//$addbut    = we_button::create_button("add", "javascript:we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.".$this->editorBodyFrame.".addCat(top.allIDs,top.allPaths);')");

		$cats = new MultiDirChooser($this->_width_size, $this->View->export->Categorys, "del_cat", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CATEGORY_TABLE);

		if(!we_hasPerm("EDIT_KATEGORIE")){
			$cats->isEditable = false;
		}
		return $hiddens . we_html_tools::htmlFormElementTable($cats->get(), g_l('export', "[categories]"), "left", "defaultfont");
	}

}

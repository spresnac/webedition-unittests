<?php

/**
 * webEdition CMS
 *
 * $Rev: 5949 $
 * $Author: mokraemer $
 * $Date: 2013-03-13 13:03:10 +0100 (Wed, 13 Mar 2013) $
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
class we_wizard_import extends we_wizard{

	var $TemplateID = 0;

	function __construct(){
		parent::__construct();
	}

	function formCategory($obj, $categories){
		$js = (defined("OBJECT_TABLE")) ? "opener.wizbody.document.we_form.elements[\\'v[import_type]\\'][0].checked=true;" : "";
		$addbut = we_button::create_button("add", "javascript:top.we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','" . $js . "fillIDs();opener.top.we_cmd(\\'add_" . $obj . "Cat\\',top.allIDs);')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));
		$cats = new MultiDirChooser(410, $categories, "delete_" . $obj . "Cat", $addbut, "", "Icon,Path", CATEGORY_TABLE);
		return $cats->get();
	}

	function formCategory2($obj, $categories){
		$js = (defined("OBJECT_TABLE")) ? "opener.wizbody.document.we_form.elements[\\'v[import_type]\\'][0].checked=true;" : "";
		$addbut = we_button::create_button("add", "javascript:top.we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','" . $js . "fillIDs();opener.top.we_cmd(\\'add_" . $obj . "Cat\\',top.allIDs);')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));
		$cats = new MultiDirChooser2(410, $categories, "delete_" . $obj . "Cat", $addbut, "", "Icon,Path", CATEGORY_TABLE);
		$cats->setRowPrefix($obj);
		$cats->setCatField("self.document.forms['we_form'].elements['v[" . $obj . "Categories]']");
		return $cats->get();
	}

	/**
	 * @return array
	 * @param integer $classID
	 * @desc returns an array with all the fields of the class with the given $classID
	 */
	function getClassFields($classID){
		$db = new DB_WE();
		$foo = getHash('SELECT strOrder,DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . (int) $classID, $db);
		$order = makeArrayFromCSV($foo["strOrder"]);
		$dv = $foo["DefaultValues"] ? unserialize($foo["DefaultValues"]) : array();
		if(!is_array($dv))
			$dv = array();
		$tableInfo_sorted = we_objectFile::getSortedTableInfo($classID, true, $db);
		$fields = array();
		$regs = array();
		foreach($tableInfo_sorted as $cur){
			// bugfix 8141
			if(preg_match('/(.+?)_(.*)/', $cur["name"], $regs)){
				$fields[] = array("name" => $regs[2], "type" => $regs[1]);
			}
		}
		return $fields;
	}

	/**
	 * @return boolean
	 * @param string $type
	 * @desc returns true if the field is a text field
	 */
	function isTextField($type){
		switch($type){
			case "input":
			case "text":
			case "meta":
			case "checkbox": //Bugfix #4733
				return true;
			default:
				return false;
		}
	}

	/**
	 * @return boolean
	 * @param string $type
	 * @desc returns true if the field is a text field
	 */
	function isDateField($type){
		switch($type){
			case "date":
				return true;
			default:
				return false;
		}
	}

	/**
	 * @return boolean
	 * @param string $type
	 * @desc returns true if the field is numeric
	 */
	function isNumericField($type){
		switch($type){
			case "int":
			case "float":
				return true;
			default:
				return false;
		}
	}

	/**
	 * @return string
	 * @param array $v
	 * @desc returns a string of hidden fields
	 */
	function getHdns($v, $a, $ignore = array()){
		$hdns = "\n";
		foreach($a as $key => $value){
			if(!in_array($key, $ignore)){
				$hdns .= we_html_element::htmlHidden(array("name" => $v . "[" . $key . "]", "value" => $value)) . "\n";
			}
		}
		return $hdns;
	}

	/**
	 * @return array
	 * @param string $attr
	 * @desc returns array of attributes
	 */
	function parseAttributes($attr){
		//FIXME: use tagParser
		$attribs = "";
		preg_match_all('/([^=]+)= *("[^"]*")/', $attr, $foo, PREG_SET_ORDER);
		foreach($foo as $cur){
			$attribs .= '"' . trim($cur[1]) . '"=>' . trim($cur[2]) . ',';
		}
		$arrstr = "array(" . rtrim($attribs, ',') . ")";
		eval('$arr = ' . $arrstr . ';');

		return $arr;
	}

	/**
	 * @param string $string1
	 * @param string $string2
	 * @param string $file
	 * @desc replaces string1 by string2 in file
	 */
	function massReplace($string1, $string2, $file){
		$contents = weFile::load($file, 'r');
		$replacement = preg_replace("/$string1/i", $string2, $contents);
		weFile::save($file, $replacement, 'w');
	}

	function getStep0(){
		$defaultVal = "import_files";


		if(!we_hasPerm("FILE_IMPORT")){
			$defaultVal = "siteImport";
			if(!we_hasPerm("SITE_IMPORT")){
				$defaultVal = "WXMLImport";
				if(!we_hasPerm("WXML_IMPORT")){
					$defaultVal = "GXMLImport";
					if(!we_hasPerm("GENERICXML_IMPORT")){
						$defaultVal = "CSVImport";
						if(!we_hasPerm("CSV_IMPORT")){
							$defaultVal = "";
						}
					}
				}
			}
		}

		$cmd = isset($_REQUEST['we_cmd']) ? $_REQUEST['we_cmd'] : array("import", $defaultVal);
		$expat = (function_exists("xml_parser_create")) ? true : false;

		$tblFiles = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 3, 1);
		$tblFiles->setCol(0, 0, array(), we_forms::radiobutton("file_import", ($cmd[1] == "import_files"), "type", g_l('import', "[file_import]"), true, "defaultfont", "", !we_hasPerm("FILE_IMPORT"), g_l('import', "[txt_file_import]"), 0, 384));
		$tblFiles->setCol(1, 0, array(), we_html_tools::getPixel(0, 4));
		$tblFiles->setCol(2, 0, array(), we_forms::radiobutton("site_import", ($cmd[1] == "siteImport"), "type", g_l('import', "[site_import]"), true, "defaultfont", "", !we_hasPerm("SITE_IMPORT"), g_l('import', "[txt_site_import]"), 0, 384));
		$tblData = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 5, 1);
		$tblData->setCol(0, 0, array(), we_forms::radiobutton("WXMLImport", ($cmd[1] == "WXMLImport"), "type", g_l('import', "[wxml_import]"), true, "defaultfont", "", (!we_hasPerm("WXML_IMPORT") || !$expat), ($expat ? g_l('import', "[txt_wxml_import]") : g_l('import', "[add_expat_support]")), 0, 384));
		$tblData->setCol(1, 0, array(), we_html_tools::getPixel(0, 4));
		$tblData->setCol(2, 0, array(), we_forms::radiobutton("GXMLImport", ($cmd[1] == "GXMLImport"), "type", g_l('import', "[gxml_import]"), true, "defaultfont", "", (!we_hasPerm("GENERICXML_IMPORT") || !$expat), ($expat) ? g_l('import', "[txt_gxml_import]") : g_l('import', "[add_expat_support]"), 0, 384));
		$tblData->setCol(3, 0, array(), we_html_tools::getPixel(0, 4));
		$tblData->setCol(4, 0, array(), we_forms::radiobutton("CSVImport", ($cmd[1] == "CSVImport"), "type", g_l('import', "[csv_import]"), true, "defaultfont", "", !we_hasPerm("CSV_IMPORT"), g_l('import', "[txt_csv_import]"), 0, 384));

		$tblTemplates = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 1);
		$tblTemplates->setCol(0, 0, array(), we_forms::radiobutton("template_import", ($cmd[1] == "import_templates"), "type", g_l('import', "[template_import]"), true, "defaultfont", "", !we_hasPerm("ADMINISTRATOR"), g_l('import', "[txt_template_import]"), 0, 384));


		$parts = array();
		array_push($parts, array(
			"headline" => g_l('import', "[import_file]"),
			"html" => $tblFiles->getHTML(),
			"space" => 120,
			"noline" => 1));
		array_push($parts, array(
			"headline" => g_l('import', "[import_data]"),
			"html" => $tblData->getHTML(),
			"space" => 120,
			"noline" => 1));
		/* First Step Wizard #4606
		  array_push($parts, array(
		  "headline" => g_l('import',"[import_templates]"),
		  "html" => $tblTemplates->getHTML(),
		  "space" => 120,
		  "noline" => 1));
		 */
		return array("\n" .
			"function we_cmd() {\n" .
			"	var args = '';\n" .
			"	var url = '" . WEBEDITION_DIR . "we_cmd.php?';\n" .
			"	for(var i = 0; i < arguments.length; i++) {\n" .
			"		url += 'we_cmd['+i+']='+escape(arguments[i]);\n" .
			"		if(i < (arguments.length - 1)) {\n" .
			"			url += '&';\n" .
			"		}\n" .
			"	}\n" .
			"	switch (arguments[0]) {\n" .
			"		default:\n" .
			"			for (var i=0; i < arguments.length; i++) {\n" .
			"				args += 'arguments['+i+']' + ((i < (arguments.length-1))? ',' : '');\n" .
			"			}\n" .
			"			eval('parent.we_cmd('+args+')');\n" .
			"	}\n" .
			"}\n" .
			"function set_button_state() {\n" .
			"	top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'disabled');\n" .
			"	top.frames['wizbusy'].switch_button_state('next', 'next_enabled', 'enabled');\n" .
			"}\n" .
			"function handle_event(evt) {\n" .
			"	var f = self.document.forms['we_form'];\n" .
			"	switch(evt) {\n" .
			"		case 'previous':\n" .
			"			break;\n" .
			"		case 'next':\n" .
			"			for (var i = 0; i < f.type.length; i++) {\n" .
			"				if (f.type[i].checked == true) {\n" .
			"					switch(f.type[i].value) {\n" .
			"						case 'file_import':\n" .
			"							top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files';\n" .
			"							break;\n" .
			"						case 'site_import':\n" .
			"							top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=siteImport';\n" .
			"							break;\n" .
			"						case 'template_import':\n" .
			"							top.opener.top.we_cmd('openFirstStepsWizardDetailTemplates');top.close();" .
			"							break;\n" .
			"						default:\n" .
			"							f.type.value=f.type[i].value;\n" .
			"							f.step.value=1;\n" .
			"							f.mode.value=0;\n" .
			"							f.target='wizbody';\n" .
			"							f.action='" . $this->path . "';\n" .
			"							f.method='post';\n" .
			"							f.submit();\n" .
			"							break;\n" .
			"					}\n" .
			"				}\n" .
			"			}\n" .
			"			break;\n" .
			"		case 'cancel':\n" .
			"			top.close();\n" .
			"			break;\n" .
			"	}\n" .
			"}\n",
			we_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false, g_l('import', "[title]"))
		);
	}

	function getWXMLImportStep1(){
		$v = isset($_REQUEST['v']) ? $_REQUEST['v'] : array();
		$doc_root = get_def_ws();
		$tmpl_root = get_def_ws(TEMPLATES_TABLE);
		$nav_root = get_def_ws(NAVIGATION_TABLE);

		$hdns = we_html_element::htmlHidden(array("name" => "v[doc_dir_id]", "value" => (isset($v["doc_dir_id"]) ? $v["doc_dir_id"] : $doc_root))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[tpl_dir_id]", "value" => (isset($v["tpl_dir_id"]) ? $v["tpl_dir_id"] : $tmpl_root))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[doc_dir]", "value" => (isset($v["doc_dir"]) ? $v["doc_dir"] : id_to_path($doc_root)))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[tpl_dir]", "value" => (isset($v["tpl_dir"]) ? $v["tpl_dir"] : id_to_path($tmpl_root, TEMPLATES_TABLE)))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[import_from]", "value" => (isset($v["import_from"]) ? $v["import_from"] : 0))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[navigation_dir_id]", "value" => (isset($v["navigation_dir_id"]) ? $v["navigation_dir_id"] : $nav_root))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[navigation_dir]", "value" => (isset($v["navigation_dir"]) ? $v["navigation_dir"] : id_to_path($nav_root, NAVIGATION_TABLE)))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[import_docs]", "value" => (isset($v["import_docs"])) ? $v["import_docs"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[import_templ]", "value" => (isset($v["import_templ"])) ? $v["import_templ"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[import_thumbnails]", "value" => (isset($v["import_thumbnails"])) ? $v["import_thumbnails"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[import_objs]", "value" => (isset($v["import_objs"])) ? $v["import_objs"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[import_classes]", "value" => (isset($v["import_classes"])) ? $v["import_classes"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[restore_doc_path]", "value" => (isset($v["restore_doc_path"])) ? $v["restore_doc_path"] : 1)) .
			we_html_element::htmlHidden(array("name" => "v[restore_tpl_path]", "value" => (isset($v["restore_tpl_path"])) ? $v["restore_tpl_path"] : 1)) .
			we_html_element::htmlHidden(array("name" => "v[import_dt]", "value" => (isset($v["import_dt"])) ? $v["import_dt"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[import_ct]", "value" => (isset($v["import_ct"])) ? $v["import_ct"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[import_binarys]", "value" => (isset($v["import_binarys"])) ? $v["import_binarys"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[import_owners]", "value" => (isset($v["import_owners"])) ? $v["import_owners"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[owners_overwrite]", "value" => (isset($v["owners_overwrite"])) ? $v["owners_overwrite"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[owners_overwrite_id]", "value" => (isset($v["owners_overwrite_id"])) ? $v["owners_overwrite_id"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[owners_overwrite_path]", "value" => (isset($v["owners_overwrite_path"])) ? $v["owners_overwrite_path"] : '/')) .
			we_html_element::htmlHidden(array("name" => "v[import_navigation]", "value" => (isset($v["import_navigation"])) ? $v["import_navigation"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[rebuild]", "value" => (isset($v["rebuild"])) ? $v["rebuild"] : 1)) .
			we_html_element::htmlHidden(array("name" => "v[mode]", "value" => (isset($v["mode"]) ? $v["mode"] : 0)));

		$functions = we_button::create_state_changer(false) .
			"function we_cmd() {
				var args = '';
				var url = '" . WEBEDITION_DIR . "we_cmd.php?';
				for(var i = 0; i < arguments.length; i++) {
					url += 'we_cmd['+i+']='+escape(arguments[i]);
					if(i < (arguments.length - 1)) {
						url += '&';
					}
				}
				switch (arguments[0]) {
					default:
						for (var i=0; i < arguments.length; i++) {
							args += 'arguments['+i+']' + ((i < (arguments.length-1))? ',' : '');
						}
						eval('parent.we_cmd('+args+')');
				}
			}
			function set_button_state() {
				top.frames['wizbusy'].back_enabled = top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'enabled');
				top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', 'enabled');
			}
			function we_submit_form(f, target, url) {
				f.target = target;
				f.action = url;
				f.method = 'post';
				f.submit();
			}
			function handle_event(evt) {
				var f = self.document.forms['we_form'];
				switch(evt) {
					case 'previous':
						f.step.value = 0;
						top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import&we_cmd[1]=WXMLImport';
						break;
					case 'next':
						var fs = f.elements['v[fserver]'].value;
						var fl = f.elements['uploaded_xml_file'].value;
						var ext = '';
						if (f.elements['v[rdofloc]'][0].checked==true && fs!='/') {
							if (fs.match(/\.\./)=='..') { " . (we_message_reporting::getShowMessageCall(g_l('import', "[invalid_path]"), we_message_reporting::WE_MESSAGE_ERROR)) . "; break; }
							ext = fs.substr(fs.length-4,4);
							f.elements['v[import_from]'].value = fs;
						}
						else if (f.elements['v[rdofloc]'][1].checked==true && fl!='') {
							ext = fl.substr(fl.length-4,4);
							f.elements['v[import_from]'].value = fl;
						}
						else if (fs=='/' || fl=='') {
							" . (we_message_reporting::getShowMessageCall(g_l('import', "[select_source_file]"), we_message_reporting::WE_MESSAGE_ERROR)) . "break;
						}
						f.step.value = 2;
			// timing Problem with Safari
						setTimeout('we_submit_form(self.document.forms[\"we_form\"], \"wizbody\", \"" . $this->path . "\")',50);
						break;
					case 'cancel':
						top.close();
						break;
				}
			}";

		//javascript: self.document.forms['we_form'].elements['v[rdofloc]'][0].checked=true;we_cmd('browse_server', 'self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[fserver]\'].value', '', document.forms['we_form'].elements['v[fserver]'].value)"
		$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[fserver]'].value");
		$wecmdenc4 = '';
		$importFromButton = (we_hasPerm("CAN_SELECT_EXTERNAL_FILES")) ? we_button::create_button("select", "javascript: self.document.forms['we_form'].elements['v[rdofloc]'][0].checked=true;we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.forms['we_form'].elements['v[fserver]'].value)") : "";
		$inputLServer = we_html_tools::htmlTextInput("v[fserver]", 30, (isset($v["fserver"]) ? $v["fserver"] : "/"), 255, "readonly", "text", 300);
		$importFromServer = we_html_tools::htmlFormElementTable($inputLServer, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $importFromButton, "", "", "", 0);

		$inputLLocal = we_html_tools::htmlTextInput("uploaded_xml_file", 30, "", 255, "accept=\"text/xml\" onClick=\"self.document.forms['we_form'].elements['v[rdofloc]'][1].checked=true;\"", "file");
		$importFromLocal = we_html_tools::htmlFormElementTable($inputLLocal, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), "", "", "", "", 0);

		$rdoLServer = we_forms::radiobutton("lServer", (isset($v["rdofloc"])) ? ($v["rdofloc"] == "lServer") : 1, "v[rdofloc]", g_l('import', "[fileselect_server]"));
		$rdoLLocal = we_forms::radiobutton("lLocal", (isset($v["rdofloc"])) ? ($v["rdofloc"] == "lLocal") : 0, "v[rdofloc]", g_l('import', "[fileselect_local]"));

		$importLocs = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 7, 1);
		$importLocs->setCol(0, 0, array(), $rdoLServer);
		$importLocs->setCol(1, 0, array(), $importFromServer);
		$importLocs->setCol(2, 0, array(), we_html_tools::getPixel(1, 4));
		$importLocs->setCol(3, 0, array(), $rdoLLocal);
		$maxsize = getUploadMaxFilesize(false);
		$importLocs->setCol(4, 0, array(), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('import', "[filesize_local]"), round($maxsize / (1024 * 1024), 3) . "MB"), 1, "410"));
		$importLocs->setCol(5, 0, array(), we_html_tools::getPixel(1, 2));
		$importLocs->setCol(6, 0, array(), $importFromLocal);

		$parts = array();
		array_push($parts, array(
			"headline" => g_l('import', "[import]"),
			"html" => $importLocs->getHTML(),
			"space" => 120)
		);


		$fn_colsn = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 7, 1);
		$fn_colsn->setCol(0, 0, array(), we_html_tools::htmlAlertAttentionBox(g_l('import', "[collision_txt]"), 1, "410"));
		$fn_colsn->setCol(1, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(2, 0, array(), we_forms::radiobutton("replace", (isset($v["collision"])) ? ($v["collision"] == "replace") : true, "v[collision]", g_l('import', "[replace]"), true, "defaultfont", "", false, g_l('import', "[replace_txt]"), 0, 384));
		$fn_colsn->setCol(3, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(4, 0, array(), we_forms::radiobutton("rename", (isset($v["collision"])) ? ($v["collision"] == "rename") : false, "v[collision]", g_l('import', "[rename]"), true, "defaultfont", "", false, g_l('import', "[rename_txt]"), 0, 384));
		$fn_colsn->setCol(5, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(6, 0, array(), we_forms::radiobutton("skip", (isset($v["collision"])) ? ($v["collision"] == "skip") : false, "v[collision]", g_l('import', "[skip]"), true, "defaultfont", "", false, g_l('import', "[skip_txt]"), 0, 384));


		array_push($parts, array(
			"headline" => g_l('import', '[file_collision]'),
			"html" => $fn_colsn->getHTML(),
			"space" => 120)
		);

		$wepos = weGetCookieVariable("but_wxml");
		$znr = -1;
		$content = $hdns . we_multiIconBox::getHTML("wxml", "100%", $parts, 30, "", $znr, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]"), ($wepos == "down"), g_l('import', "[wxml_import]"));
		return array($functions, $content);
	}

	function getWXMLImportStep2(){
		$v = $_REQUEST["v"];
		$_upload_error = false;

		if($v["rdofloc"] == "lLocal" && (isset($_FILES['uploaded_xml_file']))){
			if(empty($_FILES['uploaded_xml_file']['tmp_name']) || $_FILES['uploaded_xml_file']['error']){

				$_upload_error = true;
			} else{

				$v["import_from"] = TEMP_DIR . weFile::getUniqueId() . "_w.xml";
				move_uploaded_file($_FILES["uploaded_xml_file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
			}
		}

		$we_valid = true;

		$event_handler = '
				function handle_event(evt) {
						var we_form = self.document.forms["we_form"];
						switch(evt) {
							case "previous":
								we_form.step.value = 1;
								we_submit_form(we_form, "wizbody", "' . $this->path . '");
								break;
							case "next":
								we_form.elements["step"].value=3;
								we_form.mode.value=1;
								we_form.elements["v[mode]"].value=1;
								we_submit_form(we_form,"wizbusy","' . $this->path . '?pnt=wizcmd");
								break;
							case "cancel":
								top.close();
								break;
						}
					}
					function we_submit_form(we_form, target, url) {
						we_form.target = target;
						we_form.action = url;
						we_form.method = "post";
						we_form.submit();
					}
				';

		$hdns = we_html_element::htmlHidden(array("name" => "v[type]", "value" => $v["type"])) .
			we_html_element::htmlHidden(array("name" => "v[mode]", "value" => (isset($v["mode"])) ? $v["mode"] : 0)) .
			we_html_element::htmlHidden(array("name" => "v[fserver]", "value" => $v["fserver"])) .
			we_html_element::htmlHidden(array("name" => "v[rdofloc]", "value" => $v["rdofloc"])) .
			we_html_element::htmlHidden(array("name" => "v[import_from]", "value" => $v["import_from"])) .
			we_html_element::htmlHidden(array("name" => "v[collision]", "value" => isset($v["collision"]) ? $v["collision"] : 0));


		$functions = we_button::create_state_changer(false) .
			"function we_cmd() {
				var args = '';
				var url = '" . WEBEDITION_DIR . "we_cmd.php?';
				for(var i = 0; i < arguments.length; i++) {
					url += 'we_cmd['+i+']='+escape(arguments[i]);
					if(i < (arguments.length - 1)) {
						url += '&';
					}
				}
				switch (arguments[0]) {" .
			'case "openNavigationDirselector":
				url = "' . WE_INCLUDES_DIR . 'we_tools/navigation/we_navigationDirSelect.php?";
				for(var i = 0; i < arguments.length; i++){
					url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }
				}
				new jsWindow(url,"we_navigation_dirselector",-1,-1,600,400,true,true,true);
			break;' .
			"		case 'openSelector':
						new jsWindow(url,'we_selector',-1,-1," . WINDOW_SELECTOR_WIDTH . "," . WINDOW_SELECTOR_HEIGHT . ",true,true,true,true);
					break;
					default:
						for (var i=0; i < arguments.length; i++) {
							args += 'arguments['+i+']' + ((i < (arguments.length-1))? ',' : '');
						}
						eval('parent.we_cmd('+args+')');
				}
			}
			function set_button_state() {
				top.frames['wizbusy'].back_enabled = top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'enabled');
				top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', " .
			(($we_valid) ? ((isset($v["mode"]) && $v["mode"] == 1) ? "'disabled'" : "'enabled'") : "'disabled'") . ");
			}" . $event_handler .
			'function toggle(name){
			     var con = document.getElementById(name);
			     if(con.style.display == "none") con.style.display = "";
			     else con.style.display = "none";
			    }
			';

		$_return = array('', '');
		if($_upload_error){

			$maxsize = getUploadMaxFilesize();
			$_return[1] = we_html_element::jsElement($functions . ' ' .
					we_message_reporting::getShowMessageCall(sprintf(g_l('import', '[upload_failed]'), round($maxsize / (1024 * 1024), 3) . "MB"), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
			return $_return;
		}

		$_import_file = $_SERVER['DOCUMENT_ROOT'] . $v['import_from'];
		if(weBackupUtil::getFormat($_import_file) != 'xml'){
			$_return[1] = we_html_element::jsElement($functions . ' ' .
					we_message_reporting::getShowMessageCall(g_l('import', '[format_unknown]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
			return $_return;
		} else{
			$_xml_type = weBackupUtil::getXMLImportType($_import_file);
			if($_xml_type == 'backup'){
				$_return[0] = '';

				if(we_hasPerm('IMPORT')){
					$_return[1] = we_html_element::jsElement('
							' . $functions . '
							if(confirm("' . str_replace('"', '\'', g_l('import', '[backup_file_found]') . ' \n\n' . g_l('import', '[backup_file_found_question]')) . '")){
								top.opener.top.we_cmd("recover_backup");
								top.close();
							}
							handle_event("previous");');
				} else{
					$_return[1] = we_html_element::jsElement(
							$functions .
							we_message_reporting::getShowMessageCall(g_l('import', '[backup_file_found]'), we_message_reporting::WE_MESSAGE_ERROR) .
							'handle_event("previous");');
				}
				return $_return;
			} else if($_xml_type == 'customer'){
				$_return[1] = we_html_element::jsElement($functions . '
							' . we_message_reporting::getShowMessageCall(g_l('import', '[customer_import_file_found]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
				return $_return;
			} else if($_xml_type == 'unknown'){
				$_return[1] = we_html_element::jsElement($functions . '
							' . we_message_reporting::getShowMessageCall(g_l('import', '[format_unknown]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
				return $_return;
			}
		}

		$parts = array();
		if($we_valid){

			$tbl_extra = new we_html_table(array("cellpadding" => 2, "cellspacing" => 0, "border" => 0), 5, 1);

			// import documents
			$tbl_extra->setCol(0, 0, null, we_forms::checkboxWithHidden((isset($v["import_docs"]) && $v["import_docs"]) ? true : false, "v[import_docs]", g_l('import', "[import_docs]"), false, "defaultfont", "toggle('doc_table')"));

			$rootDirID = get_def_ws();
			//javascript:we_cmd('openDirselector',document.we_form.elements['v[doc_dir]'].value,'".FILE_TABLE."','self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[doc_dir_id]\'].value','self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[doc_dir]\'].value','','','$rootDirID')
			$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[doc_dir_id]'].value");
			$wecmdenc2 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[doc_dir]'].value");
			$wecmdenc3 = '';

			$btnDocDir = we_button::create_button(
					"select", "javascript:we_cmd('openDirselector',document.we_form.elements['v[doc_dir]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','$rootDirID')"
			);
			$yuiSuggest = & weSuggest::getInstance();
			$yuiSuggest->setAcId("DocPath");
			$yuiSuggest->setContentType("folder");
			$yuiSuggest->setInput("v[doc_dir]", (isset($v["doc_dir"]) ? $v["doc_dir"] : id_to_path($rootDirID)), array("onFocus" => "self.document.forms['we_form'].elements['_v[restore_doc_path]'].checked=false;"));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("v[doc_dir_id]", (isset($v["doc_dir_id"]) ? $v["doc_dir_id"] : $rootDirID));
			$yuiSuggest->setSelector("Dirselector");
			$yuiSuggest->setTable(FILE_TABLE);
			$yuiSuggest->setWidth(280);
			$yuiSuggest->setSelectButton($btnDocDir, 10);


			$docPath = $yuiSuggest->getYuiFiles() . $yuiSuggest->getHTML();

			$attribs = array("cellpadding" => 2, "cellspacing" => 2, "border" => 0, "id" => "doc_table");

			$dir_table = new we_html_table($attribs, 3, 2);
			if((isset($v["import_docs"]) && !$v["import_docs"])){
				$dir_table->setStyle('display', 'none');
			}
			$dir_table->setCol(0, 0, null, we_html_tools::getPixel(20, 1));
			$dir_table->setCol(0, 1, null, we_html_tools::htmlAlertAttentionBox(g_l('import', "[documents_desc]"), 1, "390", true, 50));
			$dir_table->setCol(1, 1, null, $docPath);
			$dir_table->setCol(2, 1, null, we_forms::checkboxWithHidden((isset($v["restore_doc_path"]) && $v["restore_doc_path"]) ? true : false, "v[restore_doc_path]", g_l('import', "[maintain_paths]"), false, "defaultfont", "self.document.forms['we_form'].elements['v[doc_dir]'].value='/';"));

			$tbl_extra->setCol(1, 0, null, $dir_table->getHtml());

			// --------------
			// import templates
			$rootDirID = get_def_ws(TEMPLATES_TABLE);
			$tbl_extra->setCol(2, 0, array("colspan" => "2"), we_forms::checkboxWithHidden((isset($v["import_templ"]) && $v["import_templ"]) ? true : false, "v[import_templ]", g_l('import', "[import_templ]"), false, "defaultfont", "toggle('tpl_table')"));
			//javascript:we_cmd('openDirselector',document.we_form.elements['v[tpl_dir]'].value,'".TEMPLATES_TABLE."','self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[tpl_dir_id]\'].value','self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[tpl_dir]\'].value','','','$rootDirID')
			$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[tpl_dir_id]'].value");
			$wecmdenc2 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[tpl_dir]'].value");
			$wecmdenc3 = '';
			$btnDocDir = we_button::create_button(
					"select", "javascript:we_cmd('openDirselector',document.we_form.elements['v[tpl_dir]'].value,'" . TEMPLATES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','$rootDirID')"
			);

			$yuiSuggest->setAcId("TemplPath");
			$yuiSuggest->setContentType("folder");
			$yuiSuggest->setInput("v[tpl_dir]", (isset($v["tpl_dir"]) ? $v["tpl_dir"] : id_to_path($rootDirID, TEMPLATES_TABLE)), array("onFocus" => "self.document.forms['we_form'].elements['_v[restore_tpl_path]'].checked=false;"));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("v[tpl_dir_id]", (isset($v["tpl_dir_id"])) ? $v["tpl_dir_id"] : $rootDirID);
			$yuiSuggest->setSelector("Dirselector");
			$yuiSuggest->setTable(TEMPLATES_TABLE);
			$yuiSuggest->setWidth(280);
			$yuiSuggest->setSelectButton($btnDocDir, 10);

			$docPath = $yuiSuggest->getHTML();

			if((isset($v["import_templ"]) && !$v["import_templ"]))
				$dir_table->setStyle('display', 'none');
			$dir_table->setAttribute("id", "tpl_table");
			$dir_table->setCol(0, 1, null, we_html_tools::htmlAlertAttentionBox(g_l('import', "[templates_desc]"), 1, "390", true, 50));
			$dir_table->setCol(1, 1, null, $docPath);
			$dir_table->setCol(2, 1, null, we_forms::checkboxWithHidden((isset($v["restore_tpl_path"]) && $v["restore_tpl_path"]) ? true : false, "v[restore_tpl_path]", g_l('import', "[maintain_paths]"), false, "defaultfont", "self.document.forms['we_form'].elements['v[tpl_dir]'].value='/';"));


			$tbl_extra->setCol(3, 0, null, $dir_table->getHtml());

			$tbl_extra->setCol(4, 0, array("colspan" => "2"), we_forms::checkboxWithHidden((isset($v["import_thumbnails"]) && $v["import_thumbnails"]) ? true : false, "v[import_thumbnails]", g_l('import', "[import_thumbnails]"), false, "defaultfont"));


			// --------------


			array_push($parts, array(
				"headline" => g_l('import', '[handle_document_options]') . '<br>' . g_l('import', '[handle_template_options]'),
				"html" => $tbl_extra->getHTML(),
				"space" => 120)
			);


			if(defined('OBJECT_TABLE')){
				$tbl_extra = new we_html_table(array("cellpadding" => 2, "cellspacing" => 0, "border" => 0), 2, 1);
				$tbl_extra->setCol(0, 0, null, we_forms::checkboxWithHidden((isset($v["import_objs"]) && $v["import_objs"]) ? true : false, "v[import_objs]", g_l('import', "[import_objs]")));
				$tbl_extra->setCol(1, 0, null, we_forms::checkboxWithHidden((isset($v["import_classes"]) && $v["import_classes"]) ? true : false, "v[import_classes]", g_l('import', "[import_classes]")));

				array_push($parts, array(
					"headline" => g_l('import', '[handle_object_options]') . '<br>' . g_l('import', '[handle_class_options]'),
					"html" => $tbl_extra->getHTML(),
					"space" => 120)
				);
			}

			$tbl_extra = new we_html_table(array("cellpadding" => 2, "cellspacing" => 0, "border" => 0), 4, 1);
			$tbl_extra->setCol(0, 0, null, we_forms::checkboxWithHidden((isset($v["import_dt"]) && $v["import_dt"]) ? true : false, "v[import_dt]", g_l('import', "[import_doctypes]")));
			$tbl_extra->setCol(1, 0, null, we_forms::checkboxWithHidden((isset($v["import_ct"]) && $v["import_ct"]) ? true : false, "v[import_ct]", g_l('import', "[import_cats]")));
			$tbl_extra->setCol(2, 0, null, we_forms::checkboxWithHidden((isset($v["import_navigation"]) && $v["import_navigation"]) ? true : false, "v[import_navigation]", g_l('import', "[import_navigation]"), false, 'defaultfont', "toggle('navigation_table')"));

			// --

			$btnDocDir = we_button::create_button(
					"select", "javascript:we_cmd('openNavigationDirselector','document.we_form.elements[\"v[navigation_dir]\"].value','document.forms[\"we_form\"].elements[\"v[navigation_dir_id]\"].value','document.forms[\"we_form\"].elements[\"v[navigation_dir]\"].value');"
			);

			$attribs = array("cellpadding" => 2, "cellspacing" => 2, "border" => 0, "id" => "navigation_table");
			$yuiSuggest->setAcId("NaviPath");
			$yuiSuggest->setContentType("folder");
			$yuiSuggest->setInput("v[navigation_dir]", (isset($v["navigation_dir"]) ? $v["navigation_dir"] : id_to_path($rootDirID)));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("v[navigation_dir_id]", (isset($v["navigation_dir_id"])) ? $v["navigation_dir_id"] : $rootDirID);
			$yuiSuggest->setSelector("Dirselector");
			$yuiSuggest->setTable(NAVIGATION_TABLE);
			$yuiSuggest->setWidth(280);
			$yuiSuggest->setSelectButton($btnDocDir, 10);

			$docPath = $yuiSuggest->getHTML() . $yuiSuggest->getYuiCode();

			$dir_table = new we_html_table($attribs, 2, 2);
			if((isset($v["import_navigation"]) && !$v["import_navigation"]))
				$dir_table->setStyle('display', 'none');
			$dir_table->setCol(0, 0, null, we_html_tools::getPixel(20, 1));
			$dir_table->setCol(0, 1, null, we_html_tools::htmlAlertAttentionBox(g_l('import', "[navigation_desc]"), 1, "390"));
			$dir_table->setCol(1, 1, null, $docPath);

			$tbl_extra->setCol(3, 0, null, $dir_table->getHtml());


			array_push($parts, array(
				"headline" => g_l('import', "[handle_doctype_options]") . '<br>' . g_l('import', "[handle_category_options]"),
				"html" => $tbl_extra->getHTML(),
				"space" => 120)
			);


			$xml_encoding = we_xml_parser::getEncoding($_import_file);
			if(DEFAULT_CHARSET != '' && (DEFAULT_CHARSET == 'ISO-8859-1' || DEFAULT_CHARSET == 'UTF-8' ) && ($xml_encoding == 'ISO-8859-1' || $xml_encoding == 'UTF-8' )){
				if(($xml_encoding != DEFAULT_CHARSET)){
					array_push($parts, array(
						'headline' => g_l('import', '[encoding_headline]'),
						'html' => we_forms::checkboxWithHidden((isset($v['import_ChangeEncoding']) && $v['import_ChangeEncoding']) ? true : false, 'v[import_ChangeEncoding]', g_l('import', '[encoding_change]') . $xml_encoding . g_l('import', '[encoding_to]') . DEFAULT_CHARSET . g_l('import', '[encoding_default]')) . '<input type="hidden" name="v[import_XMLencoding]" value="' . $xml_encoding . '" /><input type="hidden" name="v[import_TARGETencoding]" value="' . DEFAULT_CHARSET . '" />',
						'space' => 120)
					);
				}
			} else{
				array_push($parts, array(
					'headline' => g_l('import', '[encoding_headline]'),
					'html' => we_forms::checkboxWithHidden((isset($v['import_ChangeEncoding']) && $v['import_ChangeEncoding']) ? true : false, 'v[import_ChangeEncoding]', g_l('import', '[encoding_noway]'), false, "defaultfont", '', true),
					'space' => 120)
				);
			}



			array_push($parts, array(
				'headline' => g_l('import', '[handle_file_options]'),
				'html' => we_forms::checkboxWithHidden((isset($v['import_binarys']) && $v['import_binarys']) ? true : false, 'v[import_binarys]', g_l('import', '[import_files]')),
				'space' => 120)
			);

			array_push($parts, array(
				'headline' => g_l('import', '[rebuild]'),
				'html' => we_forms::checkboxWithHidden((isset($v['rebuild']) && $v['rebuild']) ? true : false, 'v[rebuild]', g_l('import', '[rebuild_txt]')),
				'space' => 120)
			);

			$header = weFile::loadPart($_SERVER['DOCUMENT_ROOT'] . $v['import_from'], 0, 512);

			if(empty($header)){
				$functions = '
					function set_button_state() {
						top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "enabled");
						top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "disabled");
					}
				' . $event_handler;
				$parts = array();
				array_push($parts, array(
					"headline" => '',
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('import', '[invalid_path]'), 1, "530"),
					"space" => 0)
				);
				$content = $hdns . we_multiIconBox::getHTML("wxml", "100%", $parts, 30, '', -1, '', '', false, g_l('import', '[warning]'));
				return array($functions, $content);
			}

			$show_owner_opt = strpos($header, '<we:info>') !== false;

			if($show_owner_opt){
				$tbl_extra = new we_html_table(array("cellpadding" => 2, "cellspacing" => 0, "border" => 0), 2, 1);
				$tbl_extra->setCol(0, 0, null, we_forms::checkboxWithHidden((isset($v["import_owners"]) && $v["import_owners"]) ? true : false, "v[import_owners]", g_l('import', "[handle_owners]")));
				$tbl_extra->setCol(1, 0, null, we_forms::checkboxWithHidden((isset($v["owners_overwrite"]) && $v["owners_overwrite"]) ? true : false, "v[owners_overwrite]", g_l('import', "[owner_overwrite]")));

				$tbl_extra2 = new we_html_table(array("cellpadding" => 2, "cellspacing" => 0, "border" => 0), 1, 2);
				$tbl_extra2->setCol(0, 0, null, we_html_tools::getPixel(20, 20));
				$tbl_extra2->setCol(0, 1, null, $this->formWeChooser(USER_TABLE, '', 0, 'v[owners_overwrite_id]', (isset($v["owners_overwrite_id"]) ? $v["owners_overwrite_id"] : 0), 'v[owners_overwrite_path]', (isset($v["owners_overwrite_path"]) ? $v["owners_overwrite_path"] : '/')));

				array_push($parts, array(
					"headline" => g_l('import', '[handle_owners_option]'),
					"html" => we_html_tools::htmlAlertAttentionBox(g_l('import', "[notexist_overwrite]"), 1, "530") . $tbl_extra->getHTML() . $tbl_extra2->getHTML(),
					"space" => 120
					)
				);
			} else{
				$hdns .= we_html_element::htmlHidden(array("name" => "v[import_owners]", "value" => "0")) .
					we_html_element::htmlHidden(array("name" => "v[owners_overwrite]", "value" => "0")) .
					we_html_element::htmlHidden(array("name" => "v[owners_overwrite_id]", "value" => "0"));
			}
		} else{
			array_push($parts, array(
				"headline" => g_l('import', "[xml_file]"),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('import', "[invalid_wxml]"), 1, "530"),
				"space" => 120)
			);
		}
		$wepos = weGetCookieVariable("but_wxml");
		$znr = -1;
		$content = $hdns . we_multiIconBox::getHTML("wxml", "100%", $parts, 30, "", $znr, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]"), ($wepos == "down"), ($we_valid) ? g_l('import', '[import_options]') : g_l('import', '[wxml_import]'));
		return array($functions, $content);
	}

	function getWXMLImportStep3(){

		$functions = we_button::create_state_changer(false) . '

			function addLog(text){
				document.getElementById("log").innerHTML+= text;
				document.getElementById("log").scrollTop = 50000;
			}

			function set_button_state() {

				top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_disabled", "disabled");
				top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_disabled", "disabled");


			}

			function handle_event(evt) {
				switch(evt) {
					case "cancel":
						top.close();
						break;
				}
			}
		';

		$hdns = "";
		$parts = array();
		array_push($parts, array(
			"headline" => '',
			"html" => we_html_element::htmlDiv(array('class' => 'blockwrapper', 'style' => 'width: 520px; height: 400px; border:1px #dce6f2 solid;', 'id' => 'log'), ''),
			"space" => 0)
		);
		$content = $hdns . we_multiIconBox::getHTML("wxml", "100%", $parts, 30, "", -1, '', '', false, g_l('import', '[log]'));

		return array($functions, $content);
	}

	/**
	 * Generic XML Import Step 1
	 *
	 * @return unknown
	 */
	function getGXMLImportStep1(){
		global $DB_WE;

		$v = isset($_REQUEST['v']) ? $_REQUEST['v'] : array();

		if(isset($v["docType"]) && $v["docType"] != -1 && isset($_REQUEST["doctypeChanged"]) && $_REQUEST["doctypeChanged"]){
			$values = getHash("SELECT * FROM " . DOC_TYPES_TABLE . " WHERE ID='" . $v["docType"] . "'", $GLOBALS['DB_WE']);
			$v["store_to_id"] = $values["ParentID"];
			$v["store_to_path"] = id_to_path($v["store_to_id"]);
			$v["we_Extension"] = $values["Extension"];
			$v["is_dynamic"] = $values["IsDynamic"];
			$v["docCategories"] = $values["Category"];
		}

		$hdns =
			we_html_element::htmlHidden(array("name" => "v[importDataType]", "value" => "")) .
			we_html_element::htmlHidden(array("name" => "v[import_from]", "value" => (isset($v["import_from"]) ? $v["import_from"] : ""))) .
			we_html_element::htmlHidden(array("name" => "v[docCategories]", "value" => (isset($v["docCategories"]) ? $v["docCategories"] : ""))) .
			we_html_element::htmlHidden(array("name" => "v[objCategories]", "value" => (isset($v["objCategories"]) ? $v["objCategories"] : ""))) .
			//we_html_element::htmlHidden(array("name" => "v[store_to_id]", "value" => (isset($v["store_to_id"]) ? $v["store_to_id"] : 0))).
			we_html_element::htmlHidden(array("name" => "v[collision]", "value" => (isset($v["collision"]) ? $v["collision"] : 'rename'))) .
			we_html_element::htmlHidden(array("name" => "doctypeChanged", "value" => 0)) .
			we_html_element::htmlHidden(array("name" => "v[we_TemplateID]", "value" => 0)) .
			//we_html_element::htmlHidden(array("name" => "v[we_TemplateName]", "value" => "/")).
			we_html_element::htmlHidden(array("name" => "v[is_dynamic]", "value" => (isset($v["is_dynamic"]) ? $v["is_dynamic"] : 0)));

		if(!defined("OBJECT_TABLE"))
			$hdns .= we_html_element::htmlHidden(array("name" => "v[import_type]", "value" => "documents")) . "\n";

		$DefaultDynamicExt = DEFAULT_DYNAMIC_EXT;
		$DefaultStaticExt = DEFAULT_STATIC_EXT;

		$functions = we_button::create_state_changer(false) . "
			function we_cmd() {
				var args = '';
				var url = '" . WEBEDITION_DIR . "we_cmd.php?';
				for(var i = 0; i < arguments.length; i++) {
					url += 'we_cmd['+i+']='+escape(arguments[i]);
					if(i < (arguments.length - 1)) {
						url += '&';
					}
				}
				switch (arguments[0]) {
					default:
						for (var i=0; i < arguments.length; i++) {
							args += 'arguments['+i+']' + ((i < (arguments.length-1))? ',' : '');
						}
						eval('parent.we_cmd('+args+')');
				}
			}
			function set_button_state() {
				top.frames['wizbusy'].back_enabled = top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'enabled');
				top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', 'enabled');
			}
			function we_submit_form(f, target, url) {
				f.target = target;
				f.action = url;
				f.method = 'post';
				f.submit();
			}
			function switchExt() {
				var a = self.document.forms['we_form'].elements;
				if (a['v[is_dynamic]'].value==1) var changeto='" . $DefaultDynamicExt . "'; else var changeto='" . $DefaultStaticExt . "';
				a['v[we_Extension]'].value=changeto;
			}
			function handle_event(evt) {
				var f = self.document.forms['we_form'];
				if(f.elements['v[docType]'].value == -1) {
					f.elements['v[we_TemplateID]'].value = f.elements['noDocTypeTemplateId'].value;
				} else {
					f.elements['v[we_TemplateID]'].value = f.elements['docTypeTemplateId'].value;
				}
				switch(evt) {
					case 'previous':
						f.step.value = 0;
						top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import&we_cmd[1]=GXMLImport';
						break;
					case 'next':
					var fs = f.elements['v[fserver]'].value;
						var fl = f.elements['uploaded_xml_file'].value;
						var ext = '';
						if ( (f.elements['v[rdofloc]'][0].checked==true) && fs!='/') {
							if (fs.match(/\.\./)=='..') { " . we_message_reporting::getShowMessageCall(g_l('import', "[invalid_path]"), we_message_reporting::WE_MESSAGE_ERROR) . "break; }
							ext = fs.substr(fs.length-4,4);
							f.elements['v[import_from]'].value = fs;
						}else if (f.elements['v[rdofloc]'][1].checked==true && fl!='') {
							ext = fl.substr(fl.length-4,4);
							f.elements['v[import_from]'].value = fl;
						}else if (fs=='/' || fl=='') {
							" . we_message_reporting::getShowMessageCall(g_l('import', "[select_source_file]"), we_message_reporting::WE_MESSAGE_ERROR) . "break;
						}
						if(!f.elements['v[we_TemplateID]'].value ) f.elements['v[we_TemplateID]'].value =f.elements['noDocTypeTemplateId'].value;" .
			(defined("OBJECT_TABLE") ?
				"if((f.elements['v[import_type]'][0].checked == true && f.elements['v[we_TemplateID]'].value != 0) || (f.elements['v[import_type]'][1].checked == true)) {\n" :
				"if(f.elements['v[we_TemplateID]'].value!=0) {\n"
			) . "
							f.step.value = 2;
							we_submit_form(f, 'wizbody', '" . $this->path . "');
						} else {" .
			(defined("OBJECT_TABLE") ?
				"				if(f.elements['v[import_type]'][0].checked == true) " . we_message_reporting::getShowMessageCall(g_l('import', "[select_docType]"), we_message_reporting::WE_MESSAGE_ERROR) :
				"				" . we_message_reporting::getShowMessageCall(g_l('import', "[select_docType]"), we_message_reporting::WE_MESSAGE_ERROR)
			) . "
					}
						break;
					case 'cancel':
						top.close();
						break;
				}
			}";
		$weSessionId = session_id();
		$functions .= <<<HTS

function deleteCategory(obj,cat){
	if(document.forms['we_form'].elements['v['+obj+'Categories]'].value.indexOf(','+arguments[1]+',') != -1) {
		re = new RegExp(','+arguments[1]+',');
		document.forms['we_form'].elements['v['+obj+'Categories]'].value = document.forms['we_form'].elements['v['+obj+'Categories]'].value.replace(re,',');
		document.getElementById(obj+"Cat"+cat).parentNode.removeChild(document.getElementById(obj+"Cat"+cat));
		if(document.forms['we_form'].elements['v['+obj+'Categories]'].value == ',') {
			document.forms['we_form'].elements['v['+obj+'Categories]'].value = '';
			document.getElementById(obj+"CatTable").innerHTML = "<tr><td style='font-size:8px'>&nbsp;</td></tr>";
		}
	}
}
var ajaxUrl = "/webEdition/rpc/rpc.php";

var handleSuccess = function(o){
	if(o.responseText !== undefined){
		var json = eval('('+o.responseText+')');

		for(var elemNr in json.elems){
			for(var propNr in json.elems[elemNr].props){
				var propval = json.elems[elemNr].props[propNr].val;
				propval = propval.replace(/\\\'/g,"'");
				propval = propval.replace(/'/g,"\\\'");
				var e;
				if(e = json.elems[elemNr].elem) {
					eval("e."+json.elems[elemNr].props[propNr].prop+"='"+propval+"'");
				}
			}
		}

		switchExt();
	}
}

var handleFailure = function(o){

}

var callback = {
  success: handleSuccess,
  failure: handleFailure,
  timeout: 1500
};


function weChangeDocType(f) {
	ajaxData = 'protocol=json&cmd=ChangeDocType&cns=importExport&weSessionId={$weSessionId}&docType='+f.value;
	_executeAjaxRequest('POST',ajaxUrl, callback, ajaxData);
}

function _executeAjaxRequest(aMethod, aUrl, aCallback, aData){
	return YAHOO.util.Connect.asyncRequest(aMethod, aUrl, aCallback, aData);
}

HTS;

		$v["import_type"] = isset($v["import_type"]) ? $v["import_type"] : "documents";
		//javascript:formFileChooser('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value,'$cmd');
		$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[fserver]'].value");
		$importFromButton = (we_hasPerm("CAN_SELECT_EXTERNAL_FILES")) ? we_button::create_button("select", "javascript: self.document.forms['we_form'].elements['v[rdofloc]'][0].checked=true;we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.forms['we_form'].elements['v[fserver]'].value);") : "";
		$inputLServer = we_html_tools::htmlTextInput("v[fserver]", 30, (isset($v["fserver"]) ? $v["fserver"] : "/"), 255, "readonly", "text", 300);
		$importFromServer = we_html_tools::htmlFormElementTable($inputLServer, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $importFromButton, "", "", "", 0);

		$inputLLocal = we_html_tools::htmlTextInput("uploaded_xml_file", 30, "", 255, "accept=\"text/xml\" onClick=\"self.document.forms['we_form'].elements['v[rdofloc]'][1].checked=true;\"", "file");
		$importFromLocal = we_html_tools::htmlFormElementTable($inputLLocal, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), "", "", "", "", 0);

		$rdoLServer = we_forms::radiobutton("lServer", (isset($v["rdofloc"])) ? ($v["rdofloc"] == "lServer") : 1, "v[rdofloc]", g_l('import', "[fileselect_server]"));
		$rdoLLocal = we_forms::radiobutton("lLocal", (isset($v["rdofloc"])) ? ($v["rdofloc"] == "lLocal") : 0, "v[rdofloc]", g_l('import', "[fileselect_local]"));

		$importLocs = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 7, 1);

		$_tblRow = 0;

		$importLocs->setCol($_tblRow++, 0, array(), $rdoLServer);
		$importLocs->setCol($_tblRow++, 0, array(), $importFromServer);

		$importLocs->setCol($_tblRow++, 0, array(), we_html_tools::getPixel(1, 4));
		$importLocs->setCol($_tblRow++, 0, array(), $rdoLLocal);
		$maxsize = getUploadMaxFilesize(false);
		$importLocs->setCol($_tblRow++, 0, array(), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('import', "[filesize_local]"), round($maxsize / (1024 * 1024), 3) . "MB"), 1, "410"));
		$importLocs->setCol($_tblRow++, 0, array(), we_html_tools::getPixel(1, 2));
		$importLocs->setCol($_tblRow++, 0, array(), $importFromLocal);

		$DB_WE->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " Order By DocType");
		$DTselect = new we_html_select(array(
			"name" => "v[docType]",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => (defined("OBJECT_TABLE")) ? "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;" : "",
			"onChange" => "this.form.doctypeChanged.value=1; weChangeDocType(this);",
			"style" => "width: 300px")
		);
		$optid = 0;
		$DTselect->insertOption($optid, -1, g_l('import', "[none]"));

		$v["docType"] = isset($v["docType"]) ? $v["docType"] : -1;
		while($DB_WE->next_record()) {
			$optid++;
			$DTselect->insertOption($optid, $DB_WE->f("ID"), $DB_WE->f("DocType"));
			if($v["docType"] == $DB_WE->f("ID"))
				$DTselect->selectOption($DB_WE->f("ID"));
		}
		$doctypeElement = we_html_tools::htmlFormElementTable($DTselect->getHTML(), g_l('import', "[doctype]"), "left", "defaultfont");

		/*		 * * templateElement *************************************************** */
		$table = TEMPLATES_TABLE;
		$textname = "v[we_TemplateName]";
		$idname = "noDocTypeTemplateId";
		if(we_hasPerm("CAN_SEE_TEMPLATES")){
			$ueberschrift = '<a href="javascript:goTemplate(document.we_form.elements[\'' . $idname . '\'].value)">' . g_l('import', "[template]") . '</a>';
		} else{
			$ueberschrift = g_l('import', "[template]");
		}
		$myid = (isset($v["we_TemplateID"])) ? $v["we_TemplateID"] : 0;
		$path = f("SELECT Path FROM " . $DB_WE->escape($table) . " WHERE ID=" . intval($myid), "Path", $DB_WE);

		$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener.top.we_cmd('reload_editpage');");
		$button = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','','text/weTmpl',1)");
		/*		 * ******************************************************************** */
		$yuiSuggest = & weSuggest::getInstance();

		$TPLselect = new we_html_select(array(
			"name" => "docTypeTemplateId",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => (defined("OBJECT_TABLE")) ? "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;" : "",
			//"onChange"  => "we_submit_form(self.document.forms['we_form'], 'wizbody', '".$this->path."');",
			"style" => "width: 300px")
		);

		if($v["docType"] != -1 && count($TPLselect->childs)){
			$displayDocType = "display:block";
			$displayNoDocType = "display:none";
			$foo = getHash("SELECT TemplateID,Templates FROM " . DOC_TYPES_TABLE . " WHERE ID =" . intval($v["docType"]), $DB_WE);
			$ids_arr = makeArrayFromCSV($foo["Templates"]);
			$paths_arr = id_to_path($foo["Templates"], TEMPLATES_TABLE, "", false, true);

			$optid = 0;
			while(list(, $templateID) = each($ids_arr)) {
				$TPLselect->insertOption($optid, $templateID, $paths_arr[$optid]);
				$optid++;
				if(isset($v["we_TemplateID"]) && $v["we_TemplateID"] == $templateID)
					$TPLselect->selectOption($templateID);
			}
		} else{
			$displayDocType = "display:none";
			$displayNoDocType = "display:block";
		}

		$templateElement = "<div id='docTypeLayer' style='$displayDocType'>" . we_html_tools::htmlFormElementTable($TPLselect->getHTML(), g_l('import', '[template]'), "left", "defaultfont") . "</div>";

		$yuiSuggest->setAcId("TmplPath");
		$yuiSuggest->setContentType("folder,text/weTmpl");
		$yuiSuggest->setInput("v[we_TemplateName]", (isset($v["we_TemplateName"]) ? $v["we_TemplateName"] : ""), array("onFocus" => "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setTable(TEMPLATES_TABLE);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($button, 10);
		$yuiSuggest->setLabel(g_l('import', "[template]"));

		$templateElement .= "<div id='noDocTypeLayer' style='$displayNoDocType'>" . $yuiSuggest->getHTML() . "</div>";


		$docCategories = $this->formCategory2("doc", isset($v["docCategories"]) ? $v["docCategories"] : "");
		$docCats = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 2, 2);
		$docCats->setCol(0, 0, array("valign" => "top", "class" => "defaultgray"), g_l('import', "[categories]"));
		$docCats->setCol(0, 1, array(), $docCategories);
		$docCats->setCol(1, 0, array(), we_html_tools::getPixel(130, 1));
		$docCats->setCol(1, 1, array(), we_html_tools::getPixel(150, 1));
		//javascript:we_cmd('openDirselector',document.we_form.elements['v[store_to_path]'].value,'" . FILE_TABLE . "','self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[store_to_id]\'].value','self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[store_to_path]\'].value','','','0')
		$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[store_to_id]'].value");
		$wecmdenc2 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[store_to_path]'].value");
		$wecmdenc3 = '';
		$storeToButton = we_button::create_button(
				"select", "javascript:we_cmd('openDirselector',document.we_form.elements['v[store_to_path]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','0')"
		);

		$yuiSuggest->setAcId("DirPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("v[store_to_path]", (isset($v["store_to_path"]) ? $v["store_to_path"] : "/"), array("onFocus" => "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult("v[store_to_id]", (isset($v["store_to_id"]) ? $v["store_to_id"] : 0));
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($storeToButton, 10);
		$yuiSuggest->setLabel(g_l('import', "[import_dir]"));

		$storeTo = $yuiSuggest->getHTML();

		$radioDocs = we_forms::radiobutton("documents", ($v["import_type"] == "documents"), "v[import_type]", g_l('import', "[documents]"));
		$radioObjs = we_forms::radiobutton("objects", ($v["import_type"] == "objects"), "v[import_type]", g_l('import', "[objects]"), true, "defaultfont", "self.document.forms['we_form'].elements['v[store_to_path]'].value='/'; YAHOO.autocoml.setValidById(self.document.forms['we_form'].elements['v[store_to_path]'].id); if(!!self.document.forms['we_form'].elements['v[we_TemplateName]']) { self.document.forms['we_form'].elements['v[we_TemplateName]'].value=''; YAHOO.autocoml.setValidById(self.document.forms['we_form'].elements['v[we_TemplateName]'].id); }", (defined("OBJECT_TABLE") ? false : true));

		$v["classID"] = isset($v["classID"]) ? $v["classID"] : -1;
		$CLselect = new we_html_select(array(
			"name" => "v[classID]",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => "self.document.forms['we_form'].elements['v[import_type]'][1].checked=true;",
			"style" => "width: 150px")
		);
		$optid = 0;
		$ac = makeCSVFromArray(getAllowedClasses($DB_WE));
		if($ac){
			$DB_WE->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . $ac . ') ' : '') . 'ORDER BY Text');
			while($DB_WE->next_record()) {
				$optid++;
				$CLselect->insertOption($optid, $DB_WE->f("ID"), $DB_WE->f("Text"));
				if($DB_WE->f("ID") == $v["classID"]){
					$CLselect->selectOption($DB_WE->f("ID"));
				}
			}
		}
		else
			$CLselect->insertOption($optid, -1, g_l('import', "[none]"));

		$objClass = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 2, 2);
		$objClass->setCol(0, 0, array("valign" => "top", "class" => "defaultgray"), g_l('import', "[class]"));
		$objClass->setCol(0, 1, array(), $CLselect->getHTML());
		$objClass->setCol(1, 0, array(), we_html_tools::getPixel(130, 10));
		$objClass->setCol(1, 1, array(), we_html_tools::getPixel(150, 10));

		$objCategories = $this->formCategory2("obj", isset($v["objCategories"]) ? $v["objCategories"] : "");
		$objCats = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 2, 2);
		$objCats->setCol(0, 0, array("valign" => "top", "class" => "defaultgray"), g_l('import', "[categories]"));
		$objCats->setCol(0, 1, array(), $objCategories);
		$objCats->setCol(1, 0, array(), we_html_tools::getPixel(130, 1));
		$objCats->setCol(1, 1, array(), we_html_tools::getPixel(150, 1));

		$objects = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 4, 2);
		$objects->setCol(0, 0, array("colspan" => 3), $radioObjs);
		$objects->setCol(1, 0, array(), we_html_tools::getPixel(1, 10));
		$objects->setCol(2, 0, array(), we_html_tools::getPixel(50, 1));
		$objects->setCol(2, 1, array(), $objClass->getHTML());
		$objects->setCol(3, 1, array(), $objCats->getHTML());

		$specifyDoc = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 3);
		$specifyDoc->setCol(0, 2, array("valign" => "bottom"), we_forms::checkbox(3, (isset($v["is_dynamic"]) ? $v["is_dynamic"] : 0), "chbxIsDynamic", g_l('import', "[isDynamic]"), true, "defaultfont", "this.form.elements['v[is_dynamic]'].value=this.checked? 1 : 0; switchExt();"));
		$specifyDoc->setCol(0, 1, array("width" => 20), we_html_tools::getPixel(20, 1));
		$specifyDoc->setCol(0, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup("v[we_Extension]", (isset($v["we_Extension"]) ? $v["we_Extension"] : ".html"), we_base_ContentTypes::inst()->getExtension("text/webedition"), "100"), g_l('import', "[extension]")));

		$parts = array();
		array_push($parts, array(
			"headline" => g_l('import', "[import]"),
			"html" => $importLocs->getHTML(),
			"space" => 120)
		);

		array_push($parts, array(
			"headline" => (defined("OBJECT_TABLE")) ? $radioDocs : g_l('import', "[documents]"),
			"html" => $yuiSuggest->getYuiFiles() . $doctypeElement . we_html_tools::getPixel(1, 4) . $templateElement . we_html_tools::getPixel(1, 4) . $storeTo . $yuiSuggest->getYuiCode() . we_html_tools::getPixel(1, 4) . $specifyDoc->getHTML() . we_html_tools::getPixel(1, 4) .
			we_html_tools::htmlFormElementTable($docCategories, g_l('import', "[categories]"), "left", "defaultfont"),
			"space" => 120,
			"noline" => 1)
		);

		if(defined("OBJECT_TABLE")){
			array_push($parts, array(
				"headline" => $radioObjs,
				"html" => (defined("OBJECT_TABLE")) ? we_html_tools::htmlFormElementTable($CLselect->getHTML(), g_l('import', "[class]"), "left", "defaultfont") .
					we_html_tools::getPixel(1, 4) .
					we_html_tools::htmlFormElementTable($objCategories, g_l('import', "[categories]"), "left", "defaultfont") : "",
				"space" => 120,
				"noline" => 1)
			);
		}

		$wepos = weGetCookieVariable("but_xml");
		$znr = -1;

		$content = we_html_element::jsScript(JS_DIR . "libs/yui/yahoo-min.js");
		$content.= we_html_element::jsScript(JS_DIR . "libs/yui/event-min.js");
		$content.= we_html_element::jsScript(JS_DIR . "libs/yui/connection-min.js");
		$content.= $hdns;
		$content.= we_multiIconBox::getJS();
		$content.= we_multiIconBox::getHTML("xml", "100%", $parts, 30, "", $znr, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]"), ($wepos == "down"), g_l('import', "[gxml_import]"));

		return array($functions, $content);
	}

	/**
	 * Generic XML Import Step 2
	 *
	 */
	function getGXMLImportStep2(){
		$parts = array();
		$hdns = "\n";
		$v = $_REQUEST["v"];

		if($v["rdofloc"] == "lLocal" && (isset($_FILES['uploaded_xml_file']) and $_FILES["uploaded_xml_file"]["size"])){
			$uniqueId = weFile::getUniqueId(); // #6590, changed from: uniqid(microtime())
			$v["import_from"] = TEMP_DIR . "we_xml_" . $uniqueId . ".xml";
			move_uploaded_file($_FILES["uploaded_xml_file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
		}

		$vars = array("rdofloc", "fserver", "flocal", "importDataType", "docCategories", "objCategories", "store_to_id", "is_dynamic", "import_from", "docType",
			"we_TemplateName", "we_TemplateID", "store_to_path", "we_Extension", "import_type", "classID", "sct_node", "rcd", "from_elem", "to_elem", "collision");
		foreach($vars as $var)
			$hdns.= we_html_element::htmlHidden(array("name" => "v[$var]", "value" => (isset($v[$var])) ? $v[$var] : "")) . "\n";
		$hdns.= we_html_element::htmlHidden(array("name" => "v[mode]", "value" => 0)) . "\n" . we_html_element::htmlHidden(array("name" => "v[cid]", "value" => -2)) . "\n";

		if((file_exists($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]) && is_readable($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]))){
			$xp = new we_xml_parser($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
			$xmlWellFormed = ($xp->parseError == "") ? true : false;

			if($xmlWellFormed){
				// Node-set with paths to the child nodes.
				$node_set = $xp->evaluate("*/child::*");
				$children = $xp->nodes[$xp->root]["children"];

				$recs = array();
				foreach($children as $key => $value){
					$flag = true;
					for($k = 1; $k < ($value + 1); $k++)
						if(!$xp->hasChildNodes($xp->root . "/" . $key . "[" . $k . "]"))
							$flag = false;
					if($flag)
						$recs[$key] = $value;
				}
				$isSingleNode = (count($recs) == 1) ? true : false;
				$hasChildNode = (count($recs) > 0) ? true : false;
			}
			if($xmlWellFormed && $hasChildNode){
				$rcdSelect = new we_html_select(array(
					"name" => "we_select",
					"size" => "1",
					"class" => "weSelect",
					(($isSingleNode) ? "disabled" : "style") => "",
					"onChange" => "this.form.elements['v[to_iElem]'].value=this.options[this.selectedIndex].value; this.form.elements['v[from_iElem]'].value=1;this.form.elements['v[sct_node]'].value=this.options[this.selectedIndex].text;" .
					"if(this.options[this.selectedIndex].value==1) {this.form.elements['v[from_iElem]'].disabled=true;this.form.elements['v[to_iElem]'].disabled=true;} else {this.form.elements['v[from_iElem]'].disabled=false;this.form.elements['v[to_iElem]'].disabled=false;}")
				);
				$optid = 0;
				while(list($value, $text) = each($recs)) {
					if($optid == 0)
						$firstOptVal = $text;
					$rcdSelect->addOption($text, $value);
					if(isset($v["rcd"]))
						if($text == $v["rcd"])
							$rcdSelect->selectOption($value);
					$optid++;
				}

				$tblSelect = new we_html_table(array(), 1, 7);
				$tblSelect->setCol(0, 1, array(), $rcdSelect->getHtml());
				$tblSelect->setCol(0, 2, array("width" => 20));
				$tblSelect->setCol(0, 3, array("class" => "defaultfont"), g_l('import', "[num_data_sets]"));
				$tblSelect->setCol(0, 4, array(), we_html_tools::htmlTextInput("v[from_iElem]", 4, 1, 5, "align=right", "text", 30, "", "", ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));
				$tblSelect->setCol(0, 5, array("class" => "defaultfont"), g_l('import', "[to]"));
				$tblSelect->setCol(0, 6, array(), we_html_tools::htmlTextInput("v[to_iElem]", 4, $firstOptVal, 5, "align=right", "text", 30, "", "", ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));

				$tblFrame = new we_html_table(array(), 3, 2);
				$tblFrame->setCol(0, 0, array("colspan" => "2", "class" => "defaultfont"), ($isSingleNode) ? we_html_tools::htmlAlertAttentionBox(g_l('import', "[well_formed]") . " " . g_l('import', "[select_elements]"), 2, "530") :
						we_html_tools::htmlAlertAttentionBox(g_l('import', "[xml_valid_1]") . " $optid " . g_l('import', "[xml_valid_m2]"), 2, "530"));
				$tblFrame->setCol(1, 0, array("colspan" => "2"));
				$tblFrame->setCol(2, 1, array(), $tblSelect->getHtml());

				array_push($parts, array("html" => $tblFrame->getHtml(), "space" => 0, "noline" => 1));
			}
			else
				array_push($parts, array("html" => we_html_tools::htmlAlertAttentionBox((!$xmlWellFormed) ? g_l('import', "[not_well_formed]") : g_l('import', "[missing_child_node]"), 1, "530"), "space" => 0, "noline" => 1));
		}
		else{
			$xmlWellFormed = $hasChildNode = false;
			if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]))
				array_push($parts, array("html" => we_html_tools::htmlAlertAttentionBox(g_l('import', "[file_exists]") . $_SERVER['DOCUMENT_ROOT'] . $v["import_from"], 1, "530"), "space" => 0, "noline" => 1));
			else if(!is_readable($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]))
				array_push($parts, array("html" => we_html_tools::htmlAlertAttentionBox(g_l('import', "[file_readable]"), 1, "530"), "space" => 0, "noline" => 1));
		}

		$functions = "
function set_button_state() {
	top.frames['wizbusy'].back_enabled=top.frames['wizbusy'].switch_button_state('back','back_enabled','enabled');
	top.frames['wizbusy'].next_enabled=top.frames['wizbusy'].switch_button_state('next','next_enabled','" . (($xmlWellFormed && $hasChildNode) ? "enabled" : "disabled") . "');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function handle_event(evt) {
	var f = self.document.forms['we_form'];
	switch(evt) {
	case 'previous':
		f.step.value = 1;
		we_submit_form(f, 'wizbody', '" . $this->path . "');
		break;
	case 'next':
		f.elements['v[from_elem]'].value = f.elements['v[from_iElem]'].value;
		f.elements['v[to_elem]'].value = f.elements['v[to_iElem]'].value;
		iStart = isNaN(parseInt(f.elements['v[from_iElem]'].value))? 0 : f.elements['v[from_iElem]'].value;
		iEnd = isNaN(parseInt(f.elements['v[to_iElem]'].value))? 0 : f.elements['v[to_iElem]'].value;
		iElements = parseInt(f.elements['we_select'].options[f.elements['we_select'].selectedIndex].value);
		if ((iStart < 1) || (iStart > iElements) || (iEnd < 1) || (iEnd > iElements)) {
			msg = \"" . g_l('import', "[num_elements]") . "\" +iElements;" . we_message_reporting::getShowMessageCall("msg", we_message_reporting::WE_MESSAGE_ERROR, true) . "
		} else {
			f.elements['v[rcd]'].value = f.we_select.options[f.we_select.selectedIndex].text;
			f.step.value = 3;
			we_submit_form(f, 'wizbody', '" . $this->path . "');
		}
		break;
	case 'cancel':
		top.close();
		break;
	}
}";

		$wepos = weGetCookieVariable("but_xml");
		$znr = -1;

		$content = $hdns;
		$content.= we_multiIconBox::getJS();
		$content.= we_multiIconBox::getHTML("xml", "100%", $parts, 30, "", $znr, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]"), ($wepos == "down"), g_l('import', "[select_data_set]"));

		return array($functions, $content);
	}

	function getGXMLImportStep3(){
		$v = $_REQUEST["v"];
		if(isset($v["att_pfx"]))
			$v["att_pfx"] = base64_encode($v["att_pfx"]);
		$records = (isset($_REQUEST["records"])) ? $_REQUEST["records"] : array();
		$we_flds = (isset($_REQUEST["we_flds"])) ? $_REQUEST["we_flds"] : array();
		$attrs = (isset($_REQUEST["attrs"])) ? $_REQUEST["attrs"] : array();
		foreach($attrs as $name => $value)
			$attrs[$name] = base64_encode($value);

		$hdns = $this->getHdns("v", $v);
		$hdns .= (isset($_REQUEST["records"])) ? $this->getHdns("records", $records) : "";
		$hdns .= (isset($_REQUEST["we_flds"])) ? $this->getHdns("we_flds", $we_flds) : "";
		$hdns .= (isset($_REQUEST["attrs"])) ? $this->getHdns("attributes", $attrs) : "";
		//$hdns .= we_html_element::htmlHidden(array("name" => "v[cid]", "value" => -2));
		$hdns .= we_html_element::htmlHidden(array("name" => "v[pfx_fn]", "value" => ((!isset($v["pfx_fn"])) ? 0 : $v["pfx_fn"])));
		if(isset($v["rdo_timestamp"]))
			$hdns .= we_html_element::htmlHidden(array("name" => "v[sTimeStamp]", "value" => $v["rdo_timestamp"]));

		$functions = "\n" .
			"function set_button_state() {\n" .
			"	top.frames['wizbusy'].back_enabled = top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'enabled');\n" .
			"	top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', " . (($_REQUEST["mode"] != 1) ? "'enabled'" : "'disabled'") . ");\n" .
			"}\n" .
			"function we_submit_form(f, target, url) {\n" .
			"	f.target = target;\n" .
			"	f.action = url;\n" .
			"	f.method = 'post';\n" .
			"	f.submit();\n" .
			"}\n" .
			"function handle_event(evt) {\n" .
			"	var f = self.document.forms['we_form'];\n" .
			"	switch(evt) {\n" .
			"		case 'previous':\n" .
			"			f.step.value = 2;\n" .
			"			we_submit_form(f, 'wizbody', '" . $this->path . "');\n" .
			"			break;\n" .
			"		case 'next':\n" .
			"			f.step.value=3;\n" .
			"			f.mode.value=1;\n" .
			"			f.elements['v[mode]'].value=1;\n" .
			"			top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', 'disabled');\n" .
			"			we_submit_form(f, 'wizbody', '" . $this->path . "?mode=1');\n" .
			"			break;\n" .
			"		case 'cancel':\n" .
			"			top.close();\n" .
			"			break;\n" .
			"	}\n" .
			"}\n";

		$db = new DB_WE();

		$records = array();
		$dateFields = array();

		if($v["import_type"] == "documents"){
			$sql_select = "SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . CONTENT_TABLE . "," . LINK_TABLE . " WHERE " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " .
				LINK_TABLE . ".DocumentTable='" . stripTblPrefix(TEMPLATES_TABLE) . "' AND " . LINK_TABLE . ".DID=" . intval($v["we_TemplateID"]) . " AND " . LINK_TABLE . ".Name='completeData'";

			$templateCode = f($sql_select, "Dat", $db);
			$tp = new we_tag_tagParser($templateCode);
			$tags = $tp->getAllTags();

			foreach($tags as $tag){
				if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){
					$tagname = $regs[1];
					if(preg_match('|name="([^"]+)"|i', $tag, $regs) && ($tagname != "var") && ($tagname != "field")){
						$name = $regs[1];
						switch($tagname){
							// tags with text content, links and hrefs
							case "input":
								if(in_array("date", $this->parseAttributes($tag)))
									array_push($dateFields, $name);
							case "textarea":
							case "href":
							case "link":
								array_push($records, $name);
								break;
						}
					}
				}
			}
			array_push($records, "Title");
			array_push($records, "Description");
			array_push($records, "Keywords");
		}
		else{
			$classFields = $this->getClassFields($v["classID"]);
			foreach($classFields as $classField){
				if($this->isTextField($classField["type"]) || $this->isNumericField($classField["type"]) || $this->isDateField($classField["type"])){
					array_push($records, $classField["name"]);
				}
				if($this->isDateField($classField["type"])){
					array_push($dateFields, $classField["name"]);
				}
			}
		}
		$xp = new we_xml_parser($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
		$nodeSet = $xp->evaluate($xp->root . '/' . $v["rcd"] . '[1]/child::*');
		$val_nodes = array();
		$val_attrs = array();

		foreach($nodeSet as $node){
			$nodeName = $xp->nodeName($node);
			$tmp_nodes = array($nodeName => $nodeName);
			$val_nodes = $val_nodes + $tmp_nodes;

			if($xp->hasAttributes($node)){
				$val_attrs = $val_attrs + array("@n:" => g_l('import', "[none]"));
				$attributes = $xp->getAttributes($node);

				foreach($attributes as $name => $value){
					$tmp_attrs = array($name => $name);
					$val_attrs = $val_attrs + $tmp_attrs;
				}
			}
		}
		if(count($val_attrs) == 0)
			$val_attrs = array("@n:" => g_l('import', "[none]"));

		$th = array(array("dat" => g_l('import', "[we_flds]")), array("dat" => g_l('import', "[rcd_flds]")), array("dat" => g_l('import', "[attributes]")));
		$rows = array();

		reset($records);
		$i = 0;
		while(list(, $record) = each($records)) {
			$hdns .= we_html_element::htmlHidden(array("name" => "records[$i]", "value" => $record));
			$sct_we_fields = new we_html_select(array(
				"name" => "we_flds[$record]",
				"size" => "1",
				"class" => "weSelect",
				"onClick" => "",
				"style" => "")
			);

			reset($val_nodes);
			$sct_we_fields->addOption("", g_l('import', "[any]"));
			while(list($value, $text) = each($val_nodes)) {
				$sct_we_fields->addOption(oldHtmlspecialchars($value), $text);
				if(isset($we_flds[$record])){
					if($value == $we_flds[$record])
						$sct_we_fields->selectOption($value);
				}
				else{
					if($value == $record)
						$sct_we_fields->selectOption($value);
				}
			}
			$new_record = "";
			if($record == "Title")
				$new_record = g_l('import', "[we_title]");
			if($record == "Description")
				$new_record = g_l('import', "[we_description]");
			if($record == "Keywords")
				$new_record = g_l('import', "[we_keywords]");
			array_push($rows, array(
				array("dat" => ($new_record != "") ? $new_record : $record), array("dat" => $sct_we_fields->getHTML()),
				array("dat" => we_html_tools::htmlTextInput("attrs[$record]", 30, (isset($attrs[$record]) ? base64_decode($attrs[$record]) : ""), 255, "", "text", 100))
				)
			);
			$i++;
		}

		// Associated prefix selector.
		$asocPfx = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 1);
		$asocPfx->setCol(0, 0, array("class" => "defaultfont"), g_l('import', "[pfx]") . "<br>" . we_html_tools::getPixel(1, 2) . "<br>" .
			we_html_tools::htmlTextInput("v[asoc_prefix]", 30, (isset($v["asoc_prefix"]) ? $v["asoc_prefix"] : (($v["import_type"] == "documents") ? g_l('import', "[pfx_doc]") : g_l('import', "[pfx_obj]"))), 255, "onClick=\"self.document.forms['we_form'].elements['v[rdo_filename]'][0].checked=true;\"", "text", 150));

		// Assigned record or attribute field selectors.
		$rcdPfxSelect = new we_html_select(array(
			"name" => "v[rcd_pfx]",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => "self.document.forms['we_form'].elements['v[pfx_fn]'].value=1;self.document.forms['we_form'].elements['v[rdo_filename]'][1].checked=true;",
			"style" => "width: 150px")
		);
		reset($val_nodes);
		while(list($value, $text) = each($val_nodes)) {
			$rcdPfxSelect->addOption(oldHtmlspecialchars($value), $text);
			if(isset($v["rcd_pfx"]))
				if($text == $v["rcd_pfx"])
					$rcdPfxSelect->selectOption($value);
		}

		$attPfxSelect = we_html_tools::htmlTextInput("v[att_pfx]", 30, (isset($v["att_pfx"]) ? base64_decode($v["att_pfx"]) : ""), 255, "onClick=\"self.document.forms['we_form'].elements['v[rdo_filename]'][1].checked=true;\"", "text", 100);

		$asgndFld = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 3);
		$asgndFld->setCol(0, 0, array("class" => "defaultfont"), g_l('import', "[rcd_fld]") . "<br>" . we_html_tools::getPixel(1, 2) . "<br>" . $rcdPfxSelect->getHTML());
		$asgndFld->setCol(0, 1, array("width" => 20), "");
		$asgndFld->setCol(0, 2, array("class" => "defaultfont"), g_l('import', "[attributes]") . "<br>" . we_html_tools::getPixel(1, 2) . "<br>" . $attPfxSelect);

		// Filename selector.
		$fn = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 5, 2);
		$fn->setCol(0, 0, array("colspan" => 2), we_forms::radiobutton(0, (!isset($v["rdo_filename"]) ? true : ($v["rdo_filename"] == 0) ? true : false), "v[rdo_filename]", g_l('import', "[auto]"), true, "defaultfont", "self.document.forms['we_form'].elements['v[pfx_fn]'].value=0;"));
		$fn->setCol(1, 0, array("width" => 25), we_html_tools::getPixel(25, 0));
		$fn->setCol(1, 1, array(), $asocPfx->getHTML());
		$fn->setCol(2, 0, array("height" => 5), "");
		$fn->setCol(3, 0, array("colspan" => 2), we_forms::radiobutton(1, (!isset($v["rdo_filename"]) ? false : ($v["rdo_filename"] == 1) ? true : false), "v[rdo_filename]", g_l('import', "[asgnd]"), true, "defaultfont", "self.document.forms['we_form'].elements['v[pfx_fn]'].value=1;"));
		$fn->setCol(4, 0, array("width" => 25), we_html_tools::getPixel(25, 0));
		$fn->setCol(4, 1, array(), $asgndFld->getHTML());

		$parts = array();
		array_push($parts, array(
			"html" => we_html_tools::getPixel(1, 8) . "<br>" . we_html_tools::htmlDialogBorder3(510, 255, $rows, $th, "defaultfont"),
			"space" => 0)
		);
		if(count($dateFields) > 0){
			// Timestamp
			$tStamp = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 7, 2);
			$tStamp->setCol(0, 0, array("colspan" => 2), we_forms::radiobutton("Unix", (!isset($v["rdo_timestamp"]) ? 1 : ($v["rdo_timestamp"] == "Unix") ? 1 : 0), "v[rdo_timestamp]", g_l('import', "[uts]"), true, "defaultfont", "", 0, g_l('import', "[unix_timestamp]"), 0, 384));
			$tStamp->setCol(1, 0, array("colspan" => 2), we_html_tools::getPixel(0, 4));
			$tStamp->setCol(2, 0, array("colspan" => 2), we_forms::radiobutton("GMT", (!isset($v["rdo_timestamp"]) ? 0 : ($v["rdo_timestamp"] == "GMT") ? 1 : 0), "v[rdo_timestamp]", g_l('import', "[gts]"), true, "defaultfont", "", 0, g_l('import', "[gmt_timestamp]"), 0, 384));
			$tStamp->setCol(3, 0, array("colspan" => 2), we_html_tools::getPixel(0, 4));
			$tStamp->setCol(4, 0, array("colspan" => 2), we_forms::radiobutton("Format", (!isset($v["rdo_timestamp"]) ? 0 : ($v["rdo_timestamp"] == "Format") ? 1 : 0), "v[rdo_timestamp]", g_l('import', "[fts]"), true, "defaultfont", "", 0, g_l('import', "[format_timestamp]"), 0, 384));
			$tStamp->setCol(5, 0, array("colspan" => 2), we_html_tools::getPixel(0, 4));
			$tStamp->setCol(6, 0, array("width" => 25), we_html_tools::getPixel(25, 0));
			$tStamp->setCol(6, 1, array(), we_html_tools::htmlTextInput("v[timestamp]", 30, (isset($v["timestamp"]) ? $v["timestamp"] : ""), "", "onClick=\"self.document.forms['we_form'].elements['v[rdo_timestamp]'][2].checked=true;\"", "text", 150));

			array_push($parts, array(
				"headline" => g_l('import', "[format_date]"),
				"html" => $tStamp->getHTML(),
				"space" => 120)
			);
			if(!isset($v["dateFields"])){
				$hdns .= we_html_element::htmlHidden(array("name" => "v[dateFields]", "value" => makeCSVFromArray($dateFields)));
			}
		}

		array_push($parts, array(
			"headline" => g_l('import', "[name]"),
			"html" => $fn->getHTML(),
			"space" => 120,
			"noline" => 1)
		);

		$conflict = isset($v["collision"]) ? $v["collision"] : 'rename';
		$fn_colsn = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 6, 1);
		$fn_colsn->setCol(0, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(1, 0, array(), we_forms::radiobutton("rename", $conflict == "rename", "nameconflict", g_l('import', "[rename]"), true, 'defaultfont', "self.document.forms['we_form'].elements['v[collision]'].value='rename';"));
		$fn_colsn->setCol(2, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(3, 0, array(), we_forms::radiobutton("replace", $conflict == "replace", "nameconflict", g_l('import', "[replace]"), true, 'defaultfont', "self.document.forms['we_form'].elements['v[collision]'].value='replace';"));
		$fn_colsn->setCol(4, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(5, 0, array(), we_forms::radiobutton("skip", $conflict == "skip", "nameconflict", g_l('import', "[skip]"), true, 'defaultfont', "self.document.forms['we_form'].elements['v[collision]'].value='skip';"));

		array_push($parts, array(
			"headline" => g_l('import', '[name_collision]'),
			"html" => $fn_colsn->getHTML(),
			"space" => 140)
		);

		$wepos = weGetCookieVariable("but_xml");
		$znr = -1;

		$content = $hdns;
		$content.= we_multiIconBox::getJS();
		$content.= we_multiIconBox::getHTML("xml", "100%", $parts, 30, "", $znr, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]"), ($wepos == "down"), g_l('import', "[asgn_rcd_flds]"));

		return array($functions, $content);
	}

	function getCSVImportStep1(){
		global $DB_WE;
		$v = $_REQUEST["v"];

		$functions = we_button::create_state_changer(false) . "
function we_cmd() {
	var args = '';
	var url = '" . WEBEDITION_DIR . "we_cmd.php?';
	for(var i = 0; i < arguments.length; i++) {
		url += 'we_cmd['+i+']='+escape(arguments[i]);
		if(i < (arguments.length - 1)) {
			url += '&';
		}
	}
	switch (arguments[0]) {
		default:
			for (var i=0; i < arguments.length; i++) {
				args += 'arguments['+i+']' + ((i < (arguments.length-1))? ',' : '');
			}
			eval('parent.we_cmd('+args+')');
	}
}
function set_button_state() {
	top.frames['wizbusy'].back_enabled = top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'enabled');
	top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', 'enabled');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function handle_event(evt) {
	var f = self.document.forms['we_form'];
	switch(evt) {
		case 'previous':
			f.step.value = 0;
			top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import&we_cmd[1]=CSVImport';
			break;
		case 'next':
			var fvalid = true;
			var fs = f.elements['v[fserver]'].value;
			var fl = f.elements['uploaded_csv_file'].value;
			var ext = '';
			if ((f.elements['v[rdofloc]'][0].checked==true) && fs!='/') {
				if (fs.match(/\.\./)=='..') { " . we_message_reporting::getShowMessageCall(g_l('import', "[invalid_path]"), we_message_reporting::WE_MESSAGE_ERROR) . "break; }
				ext = fs.substr(fs.length-4,4);
				f.elements['v[import_from]'].value = fs;
			}else if (f.elements['v[rdofloc]'][1].checked==true && fl!='') {
				ext = fl.substr(fl.length-4,4);
				f.elements['v[import_from]'].value = fl;
			}else if (fs=='/' || fl=='') {" .
			(we_message_reporting::getShowMessageCall(g_l('import', "[select_source_file]"), we_message_reporting::WE_MESSAGE_ERROR)) . "break;
			}
			if (fvalid && f.elements['v[csv_seperator]'].value=='') { fvalid=false; " . we_message_reporting::getShowMessageCall(g_l('import', "[select_seperator]"), we_message_reporting::WE_MESSAGE_ERROR) . "}
			if (fvalid) {
				f.step.value = 2;
				we_submit_form(f, 'wizbody', '" . $this->path . "');
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}";

		$v["import_type"] = isset($v["import_type"]) ? $v["import_type"] : "documents";
		/*		 * *************************************************************************************************************** */
		//javascript:we_cmd('browse_server', 'self.frames[\'wizbody\'].document.forms[\'we_form\'].elements[\'v[fserver]\'].value', '', document.forms['we_form'].elements['v[fserver]'].value)
		$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[fserver]'].value");
		$wecmdenc4 = '';
		$importFromButton = (we_hasPerm("CAN_SELECT_EXTERNAL_FILES")) ? we_button::create_button("select", "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.forms['we_form'].elements['v[fserver]'].value)") : "";
		$inputLServer = we_html_tools::htmlTextInput("v[fserver]", 30, (isset($v["fserver"]) ? $v["fserver"] : "/"), 255, "readonly onClick=\"self.document.forms['we_form'].elements['v[rdofloc]'][0].checked=true;\"", "text", 300);
		$importFromServer = we_html_tools::htmlFormElementTable($inputLServer, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $importFromButton, "", "", "", 0);

		$inputLLocal = we_html_tools::htmlTextInput("uploaded_csv_file", 30, "", 255, "accept=\"text/xml\" onClick=\"" . "self.document.forms['we_form'].elements['v[rdofloc]'][1].checked=true;\"" . "\"", "file");
		$importFromLocal = we_html_tools::htmlFormElementTable($inputLLocal, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), "", "", "", "", 0);

		$rdoLServer = we_forms::radiobutton("lServer", (isset($v["rdofloc"])) ? ($v["rdofloc"] == "lServer") : 1, "v[rdofloc]", g_l('import', "[fileselect_server]"));
		$rdoLLocal = we_forms::radiobutton("lLocal", (isset($v["rdofloc"])) ? ($v["rdofloc"] == "lLocal") : 0, "v[rdofloc]", g_l('import', "[fileselect_local]"));

		$importLocs = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 7, 1);

		$_tblRow = 0;

		$importLocs->setCol($_tblRow++, 0, array(), $rdoLServer);
		$importLocs->setCol($_tblRow++, 0, array(), $importFromServer);

		$importLocs->setCol($_tblRow++, 0, array(), we_html_tools::getPixel(1, 4));
		$importLocs->setCol($_tblRow++, 0, array(), $rdoLLocal);
		$maxsize = getUploadMaxFilesize(false);
		$importLocs->setCol($_tblRow++, 0, array(), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('import', "[filesize_local]"), round($maxsize / (1024 * 1024), 3) . "MB"), 1, "410"));
		$importLocs->setCol($_tblRow++, 0, array(), we_html_tools::getPixel(1, 2));
		$importLocs->setCol($_tblRow++, 0, array(), $importFromLocal);
		/*		 * *************************************************************************************************************** */
		$iptDel = we_html_tools::htmlTextInput("v[csv_seperator]", 2, (isset($v["csv_seperator"]) ? (($v["csv_seperator"] != "") ? $v["csv_seperator"] : " ") : ";"), 2, "", "text", 20);
		$fldDel = new we_html_select(array("name" => "v[sct_csv_seperator]", "size" => "1", "class" => "weSelect", "onChange" => "this.form.elements['v[csv_seperator]'].value=this.options[this.selectedIndex].innerHTML.substr(0,2);this.selectedIndex=options[0];", "style" => "width: 130px"));
		$fldDel->addOption("", "");
		$fldDel->addOption("semicolon", g_l('import', "[semicolon]"));
		$fldDel->addOption("comma", g_l('import', "[comma]"));
		$fldDel->addOption("colon", g_l('import', "[colon]"));
		$fldDel->addOption("tab", g_l('import', "[tab]"));
		$fldDel->addOption("space", g_l('import', "[space]"));
		if(isset($v["sct_csv_seperator"])){
			$fldDel->selectOption($v["sct_csv_seperator"]);
		}

		$charSet = new we_html_select(array("name" => "v[file_format]", "size" => "1", "class" => "weSelect", "onChange" => "", "style" => ""));
		$charSet->addOption("win", "Windows");
		$charSet->addOption("unix", "Unix");
		$charSet->addOption("mac", "Mac");
		if(isset($v["file_format"])){
			$charSet->selectOption($v["file_format"]);
		}

		$txtDel = new we_html_select(array("name" => "v[csv_enclosed]", "size" => "1", "class" => "weSelect", "onChange" => "", "style" => "width: 300px"));
		$txtDel->addOption("double_quote", g_l('import', "[double_quote]"));
		$txtDel->addOption("single_quote", g_l('import', "[single_quote]"));
		$txtDel->addOption("none", g_l('import', "[none]"));
		if(isset($v["csv_enclosed"])){
			$txtDel->selectOption($v["csv_enclosed"]);
		}

		$rowDef = we_forms::checkbox("", (isset($v["csv_fieldnames"]) ? $v["csv_fieldnames"] : true), "checkbox_fieldnames", g_l('import', "[contains]"), true, "defaultfont", "this.form.elements['v[csv_fieldnames]'].value=this.checked ? 1 : 0;");

		$csvSettings = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 7, 1);
		$csvSettings->setCol(0, 0, array("class" => "defaultfont"), g_l('import', "[file_format]") . "<br>" . we_html_tools::getPixel(1, 1) . "<br>" . $charSet->getHtml());
		$csvSettings->setCol(1, 0, array(), we_html_tools::getPixel(1, 6));
		$csvSettings->setCol(2, 0, array("class" => "defaultfont"), g_l('import', "[field_delimiter]") . "<br>" . we_html_tools::getPixel(1, 1) . "<br>" . $iptDel . " " . $fldDel->getHtml());
		$csvSettings->setCol(3, 0, array(), we_html_tools::getPixel(1, 6));
		$csvSettings->setCol(4, 0, array("class" => "defaultfont"), g_l('import', "[text_delimiter]") . "<br>" . we_html_tools::getPixel(1, 1) . "<br>" . $txtDel->getHtml());
		$csvSettings->setCol(5, 0, array(), we_html_tools::getPixel(1, 6));
		$csvSettings->setCol(6, 0, array(), $rowDef);

		$parts = array();

		array_push($parts, array(
			"headline" => g_l('import', "[import]"),
			"html" => $importLocs->getHTML(),
			"space" => 120)
		);
		array_push($parts, array(
			"headline" => g_l('import', "[field_options]"),
			"html" => $csvSettings->getHTML(),
			"space" => 120,
			"noline" => 1)
		);


		$content = we_html_element::htmlHidden(array("name" => "v[csv_fieldnames]", "value" => (isset($v["csv_fieldnames"])) ? $v["csv_fieldnames"] : 1)) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[import_from]", "value" => (isset($v["import_from"]) ? $v["import_from"] : ""))) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[csv_escaped]", "value" => (isset($v["csv_escaped"])) ? $v["csv_escaped"] : "")) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[collision]", "value" => (isset($v["collision"])) ? $v["collision"] : "rename")) . "\n" .
			we_html_element::htmlHidden(array("name" => "v[csv_terminated]", "value" => (isset($v["csv_terminated"])) ? $v["csv_terminated"] : ""));
		$content.= we_multiIconBox::getHTML("csv", "100%", $parts, 30, "", -1, "", "", false, g_l('import', "[csv_import]"));

		return array($functions, $content);
	}

	function getCSVImportStep2(){
		global $DB_WE;
		$v = $_REQUEST["v"];
		if(((isset($_FILES['uploaded_csv_file']) and $_FILES["uploaded_csv_file"]["size"])) || $v["file_format"] == "mac"){
			$uniqueId = weFile::getUniqueId(); // #6590, changed from: uniqid(microtime())

			switch($v["rdofloc"]){
				case "lLocal":
					if(isset($_FILES["uploaded_csv_file"])){
						$v["import_from"] = TEMP_DIR . "we_csv_" . $uniqueId . ".csv";
						move_uploaded_file($_FILES["uploaded_csv_file"]["tmp_name"], $_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
						if($v["file_format"] == "mac")
							$this->massReplace("\r", "\n", $_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
					}
					break;
				case "lServer":
					$realPath = realpath($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]);
					if(strpos($realPath, $_SERVER['DOCUMENT_ROOT']) === FALSE){
						t_e('warning', 'Acess outside document_root forbidden!', $realPath);
						break;
					}

					$contents = weFile::load($fp, 'r');
					$v["import_from"] = TEMP_DIR . "we_csv_" . $uniqueId . ".csv";
					$replacement = str_replace("\r", "\n", $contents);
					weFile::save($fp, $replacement, 'w+');
					break;
			}
		}

		if(isset($v["docType"]) && $v["docType"] != -1 && isset($_REQUEST["doctypeChanged"]) && $_REQUEST["doctypeChanged"]){
			$values = getHash("SELECT * FROM " . DOC_TYPES_TABLE . " WHERE ID=" . intval($v["docType"]), $GLOBALS['DB_WE']);
			$v["store_to_id"] = $values["ParentID"];
			;
			$v["store_to_path"] = id_to_path($v["store_to_id"]);
			$v["we_Extension"] = $values["Extension"];
			$v["is_dynamic"] = $values["IsDynamic"];
			$v["docCategories"] = $values["Category"];
		}
		$hdns =
			we_html_element::htmlHidden(array("name" => "v[mode]", "value" => (isset($v["mode"]) ? $v["mode"] : ""))) .
			we_html_element::htmlHidden(array("name" => "v[import_from]", "value" => $v["import_from"])) .
			we_html_element::htmlHidden(array("name" => "v[collision]", "value" => $v["collision"])) .
			we_html_element::htmlHidden(array("name" => "v[rdofloc]", "value" => $v["rdofloc"])) .
			we_html_element::htmlHidden(array("name" => "v[fserver]", "value" => $v["fserver"])) .
			we_html_element::htmlHidden(array("name" => "v[csv_fieldnames]", "value" => $v["csv_fieldnames"])) .
			we_html_element::htmlHidden(array("name" => "v[csv_seperator]", "value" => trim($v["csv_seperator"]))) .
			we_html_element::htmlHidden(array("name" => "v[csv_enclosed]", "value" => $v["csv_enclosed"])) .
			we_html_element::htmlHidden(array("name" => "v[csv_escaped]", "value" => $v["csv_escaped"])) .
			we_html_element::htmlHidden(array("name" => "v[csv_terminated]", "value" => $v["csv_terminated"])) .
			we_html_element::htmlHidden(array("name" => "v[docCategories]", "value" => (isset($v["docCategories"]) ? $v["docCategories"] : ""))) .
			we_html_element::htmlHidden(array("name" => "v[objCategories]", "value" => (isset($v["objCategories"]) ? $v["objCategories"] : ""))) .
			//we_html_element::htmlHidden(array("name" => "v[store_to_id]", "value" => (isset($v["store_to_id"]) ? $v["store_to_id"] : 0))).
			we_html_element::htmlHidden(array("name" => "v[is_dynamic]", "value" => (isset($v["is_dynamic"]) ? $v["is_dynamic"] : 0))) .
			we_html_element::htmlHidden(array("name" => "doctypeChanged", "value" => 0)) .
			we_html_element::htmlHidden(array("name" => "v[file_format]", "value" => $v["file_format"])) .
			(defined("OBJECT_TABLE") ? '' :
				$hdns .= we_html_element::htmlHidden(array("name" => "v[import_type]", "value" => "documents"))
			);

		$DefaultDynamicExt = DEFAULT_DYNAMIC_EXT;
		$DefaultStaticExt = DEFAULT_STATIC_EXT;

		$functions = we_button::create_state_changer(false) . "
function we_cmd() {
	var args = '';
	var url = '" . WEBEDITION_DIR . "we_cmd.php?';
	for(var i = 0; i < arguments.length; i++) {
		url += 'we_cmd['+i+']='+escape(arguments[i]);
		if(i < (arguments.length - 1)) {
			url += '&';
		}
	}
	for (var i=0; i < arguments.length; i++) {
		args += 'arguments['+i+']' + ((i < (arguments.length-1))? ',' : '');
	}
	eval('parent.we_cmd('+args+')');
}
function set_button_state() {
	top.frames['wizbusy'].back_enabled = top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'enabled');
	top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', 'enabled');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function switchExt() {
	var a = self.document.forms['we_form'].elements;
	if (a['v[is_dynamic]'].value==1) var changeto='" . $DefaultDynamicExt . "'; else var changeto='" . $DefaultStaticExt . "';
	a['v[we_Extension]'].value=changeto;
}
function handle_event(evt) {
	var f = self.document.forms['we_form'];
	if(f.elements['v[import_type]']== 'documents'){
	if(f.elements['v[docType]'].value == -1) {
		f.elements['v[we_TemplateID]'].value = f.elements['noDocTypeTemplateId'].value;
	} else {
		f.elements['v[we_TemplateID]'].value = f.elements['docTypeTemplateId'].value;
	}
}
	switch(evt) {
	case 'previous':
		f.step.value = 1;
		we_submit_form(f, 'wizbody', '" . $this->path . "');
		break;
	case 'next':
		if(f.elements['v[import_type]']== 'documents'){
				if(!f.elements['v[we_TemplateID]'].value ) f.elements['v[we_TemplateID]'].value =f.elements['DocTypeTemplateId'].value;
				}" . (defined("OBJECT_TABLE") ?
				"			if(f.elements['v[import_from]'].value != '/' && ((f.elements['v[import_type]'][0].checked == true && f.elements['v[we_TemplateID]'].value != 0) || (f.elements['v[import_type]'][1].checked == true)))" :
				"			if(f.elements['v[import_from]'].value != '/' && f.elements['v[we_TemplateID]'].value != 0)") . "
			{
				f.step.value = 3;
				we_submit_form(f, 'wizbody', '" . $this->path . "');
			}else {
				if(f.elements['v[import_from]'].value == '/') " . we_message_reporting::getShowMessageCall(g_l('import', "[select_source_file]"), we_message_reporting::WE_MESSAGE_ERROR) .
			(defined("OBJECT_TABLE") ?
				"				else if(f.elements['v[import_type]'][0].checked == true) " . we_message_reporting::getShowMessageCall(g_l('import', "[select_docType]"), we_message_reporting::WE_MESSAGE_ERROR) :
				"				else " . we_message_reporting::getShowMessageCall(g_l('import', "[select_docType]"), we_message_reporting::WE_MESSAGE_ERROR)) . "
			}
			break;
	case 'cancel':
		top.close();
		break;
	}
}";

		$weSessionId = session_id();
		$functions .= <<<HTS

function deleteCategory(obj,cat){
	if(document.forms['we_form'].elements['v['+obj+'Categories]'].value.indexOf(','+arguments[1]+',') != -1) {
		re = new RegExp(','+arguments[1]+',');
		document.forms['we_form'].elements['v['+obj+'Categories]'].value = document.forms['we_form'].elements['v['+obj+'Categories]'].value.replace(re,',');
		document.getElementById(obj+"Cat"+cat).parentNode.removeChild(document.getElementById(obj+"Cat"+cat));
		if(document.forms['we_form'].elements['v['+obj+'Categories]'].value == ',') {
			document.forms['we_form'].elements['v['+obj+'Categories]'].value = '';
			document.getElementById(obj+"CatTable").innerHTML = "<tr><td style='font-size:8px'>&nbsp;</td></tr>";
		}
	}
}
var ajaxUrl = "/webEdition/rpc/rpc.php";

var handleSuccess = function(o){
	if(o.responseText !== undefined){
		var json = eval('('+o.responseText+')');
		for(var elemNr in json.elems){
			for(var propNr in json.elems[elemNr].props){
				var propval = json.elems[elemNr].props[propNr].val;
				propval = propval.replace(/\\\'/g,"'");
				propval = propval.replace(/'/g,"\\\'");
				var e;
				if(e = json.elems[elemNr].elem) {
					eval("e."+json.elems[elemNr].props[propNr].prop+"='"+propval+"'");
				}
			}
		}
		switchExt();
	}
}

var handleFailure = function(o){

}

var callback = {
  success: handleSuccess,
  failure: handleFailure,
  timeout: 1500
};


function weChangeDocType(f) {
	ajaxData = 'protocol=json&cmd=ChangeDocType&cns=importExport&weSessionId={$weSessionId}&docType='+f.value;
	_executeAjaxRequest('POST',ajaxUrl, callback, ajaxData);
}

function _executeAjaxRequest(aMethod, aUrl, aCallback, aData){
	return YAHOO.util.Connect.asyncRequest(aMethod, aUrl, aCallback, aData);
}

HTS;
		$v["import_type"] = isset($v["import_type"]) ? $v["import_type"] : "documents";
		$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[store_to_id]'].value");
		$wecmdenc2 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['v[store_to_path]'].value");

		$storeToButton = we_button::create_button('select', "javascript:we_cmd('openDirselector',document.we_form.elements['v[store_to_path]'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','0')");

		$DB_WE->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " Order By DocType");
		$DTselect = new we_html_select(array(
			"name" => "v[docType]",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => (defined("OBJECT_TABLE")) ? "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;" : "",
			"onChange" => "this.form.doctypeChanged.value=1; weChangeDocType(this);",
			//"onChange"  => "this.form.doctypeChanged.value=1;we_submit_form(self.document.forms['we_form'], 'wizbody', '".$this->path."');",
			"style" => "width: 300px")
		);
		$optid = 0;
		$DTselect->insertOption($optid, -1, g_l('import', "[none]"));

		$v["docType"] = isset($v["docType"]) ? $v["docType"] : -1;
		while($DB_WE->next_record()) {
			$optid++;
			$DTselect->insertOption($optid, $DB_WE->f("ID"), $DB_WE->f("DocType"));
			if($v["docType"] == $DB_WE->f("ID")){
				$DTselect->selectOption($DB_WE->f("ID"));
			}
		}

		$doctypeElement = we_html_tools::htmlFormElementTable($DTselect->getHTML(), g_l('import', "[doctype]"), "left", "defaultfont");

		/*		 * * templateElement *************************************************** */
		$table = TEMPLATES_TABLE;
		$textname = "v[we_TemplateName]";
		$idname = "v[we_TemplateID]";
		$ueberschrift = (we_hasPerm("CAN_SEE_TEMPLATES") ?
				'<a href="javascript:goTemplate(document.we_form.elements[\'' . $idname . '\'].value)">' . g_l('import', "[template]") . '</a>' :
				g_l('import', "[template]"));

		$myid = (isset($v["we_TemplateID"])) ? $v["we_TemplateID"] : 0;
		$path = f("SELECT Path FROM " . $DB_WE->escape($table) . " WHERE ID=" . intval($myid), "Path", $DB_WE);
		//javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','self.frames[\\'wizbody\\'].document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','self.frames[\\'wizbody\\'].document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','opener.top.we_cmd(\'reload_editpage\');','".session_id()."','','text/weTmpl',1)
		$wecmdenc1 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("self.frames['wizbody'].document.forms['we_form'].elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener.top.we_cmd('reload_editpage');");
		$button = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','','text/weTmpl',1)");

		$yuiSuggest = & weSuggest::getInstance();

		$TPLselect = new we_html_select(array(
			"name" => "v[we_TemplateID]",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;",
			//"onChange"  => "we_submit_form(self.document.forms['we_form'], 'wizbody', '".$this->path."');",
			"style" => "width: 300px")
		);

		if($v["docType"] != -1){
			$foo = getHash('SELECT TemplateID,Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID =' . intval($v["docType"]), $DB_WE);
			$ids_arr = makeArrayFromCSV($foo["Templates"]);
			$paths_arr = id_to_path($foo["Templates"], TEMPLATES_TABLE, '', false, true);


			$optid = 0;
			foreach($ids_arr as $templateID){
				$TPLselect->insertOption($optid, $templateID, $paths_arr[$optid]);
				++$optid;
				if(isset($v["we_TemplateID"]) && $v["we_TemplateID"] == $templateID)
					$TPLselect->selectOption($templateID);
			}
		} else{
			$displayDocType = 'display:none';
			$displayNoDocType = 'display:block';
			/*
			  $templateElement = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("v[we_TemplateName]", 30, (isset($v["we_TemplateName"])? $v["we_TemplateName"] : ""), "", " readonly", "text", 300, 0),
			  g_l('import',"[template]"),
			  "left",
			  "defaultfont",
			  we_html_element::htmlHidden(array("name" => $idname, "value" => $myid)),
			  we_html_tools::getPixel(10,4),
			  $button); */
		}
		$yuiSuggest->setAcId("TmplPath");
		$yuiSuggest->setContentType("folder,text/weTmpl");
		$yuiSuggest->setInput("v[we_TemplateName]", (isset($v["we_TemplateName"]) ? $v["we_TemplateName"] : ""), array("onFocus" => "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setTable(TEMPLATES_TABLE);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($button, 10);
		$yuiSuggest->setLabel(g_l('import', "[template]"));

		$templateElement = "<div id='docTypeLayer' style='$displayDocType'>" . we_html_tools::htmlFormElementTable($TPLselect->getHTML(), g_l('import', '[template]'), "left", "defaultfont") . "</div>
<div id='noDocTypeLayer' style='$displayNoDocType'>" . $yuiSuggest->getHTML() . "</div>";

//		$input = we_html_tools::htmlTextInput("v[store_to_path]", 30, (isset($v["store_to_path"]) ? $v["store_to_path"] : "/"), "", "", "text", 300, 0, "", false);
//		$storeTo = we_html_tools::htmlFormElementTable($input, g_l('import',"[import_dir]"), "left", "defaultfont", we_html_tools::getPixel(10, 1), $storeToButton, "", "", "", 0);

		$yuiSuggest->setAcId("DirPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("v[store_to_path]", (isset($v["store_to_path"]) ? $v["store_to_path"] : "/"), array("onFocus" => "self.document.forms['we_form'].elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult("v[store_to_id]", (isset($v["store_to_id"]) ? $v["store_to_id"] : 0));
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($storeToButton, 10);
		$yuiSuggest->setLabel(g_l('import', "[import_dir]"));

		$storeTo = $yuiSuggest->getHTML();

		$docCategories = $this->formCategory2("doc", isset($v["docCategories"]) ? $v["docCategories"] : "");
		$docCats = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 2, 2);
		$docCats->setCol(0, 0, array("valign" => "top", "class" => "defaultgray"), g_l('import', "[categories]"));
		$docCats->setCol(0, 1, array(), $docCategories);
		$docCats->setCol(1, 0, array(), we_html_tools::getPixel(130, 1));
		$docCats->setCol(1, 1, array(), we_html_tools::getPixel(150, 1));

		$radioDocs = we_forms::radiobutton("documents", ($v["import_type"] == "documents"), "v[import_type]", g_l('import', "[documents]"));
//		$radioObjs = we_forms::radiobutton("objects", ($v["import_type"] == "objects"), "v[import_type]", g_l('import',"[objects]"), true, "defaultfont", "", (defined("OBJECT_TABLE"))? false : true);
		$radioObjs = we_forms::radiobutton("objects", ($v["import_type"] == "objects"), "v[import_type]", g_l('import', "[objects]"), true, "defaultfont", "self.document.forms['we_form'].elements['v[store_to_path]'].value='/'; YAHOO.autocoml.setValidById(self.document.forms['we_form'].elements['v[store_to_path]'].id); if(!!self.document.forms['we_form'].elements['v[we_TemplateName]']) { self.document.forms['we_form'].elements['v[we_TemplateName]'].value=''; YAHOO.autocoml.setValidById(self.document.forms['we_form'].elements['v[we_TemplateName]'].id); }", (defined("OBJECT_TABLE") ? false : true));

		$v["classID"] = isset($v["classID"]) ? $v["classID"] : -1;
		$CLselect = new we_html_select(array(
			"name" => "v[classID]",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => "self.document.forms['we_form'].elements['v[import_type]'][1].checked=true;",
			"style" => "width: 150px")
		);
		$optid = 0;
		$ac = makeCSVFromArray(getAllowedClasses($DB_WE));
		if($ac){
			$DB_WE->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . $ac . ') ' : '') . 'ORDER BY Text');
			while($DB_WE->next_record()) {
				$optid++;
				$CLselect->insertOption($optid, $DB_WE->f("ID"), $DB_WE->f("Text"));
				if($DB_WE->f("ID") == $v["classID"])
					$CLselect->selectOption($DB_WE->f("ID"));
			}
		}else{
			$CLselect->insertOption($optid, -1, g_l('import', "[none]"));
		}

		$objClass = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 2, 2);
		$objClass->setCol(0, 0, array("valign" => "top", "class" => "defaultgray"), g_l('import', "[class]"));
		$objClass->setCol(0, 1, array(), $CLselect->getHTML());
		$objClass->setCol(1, 0, array(), we_html_tools::getPixel(130, 10));
		$objClass->setCol(1, 1, array(), we_html_tools::getPixel(150, 10));

		$objCategories = $this->formCategory2("obj", isset($v["objCategories"]) ? $v["objCategories"] : "");
		$objCats = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 2, 2);
		$objCats->setCol(0, 0, array("valign" => "top", "class" => "defaultgray"), g_l('import', "[categories]"));
		$objCats->setCol(0, 1, array(), $objCategories);
		$objCats->setCol(1, 0, array(), we_html_tools::getPixel(130, 1));
		$objCats->setCol(1, 1, array(), we_html_tools::getPixel(150, 1));

		$objects = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 4, 2);
		$objects->setCol(0, 0, array("colspan" => 3), $radioObjs);
		$objects->setCol(1, 0, array(), we_html_tools::getPixel(1, 10));
		$objects->setCol(2, 0, array(), we_html_tools::getPixel(50, 1));
		$objects->setCol(2, 1, array(), $objClass->getHTML());
		$objects->setCol(3, 1, array(), $objCats->getHTML());

		$specifyDoc = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 3);
		$specifyDoc->setCol(0, 2, array("valign" => "bottom"), we_forms::checkbox(3, (isset($v["is_dynamic"]) ? $v["is_dynamic"] : 0), "chbxIsDynamic", g_l('import', "[isDynamic]"), true, "defaultfont", "this.form.elements['v[is_dynamic]'].value=this.checked? 1 : 0; switchExt();"));
		$specifyDoc->setCol(0, 1, array("width" => 20), we_html_tools::getPixel(20, 1));
		$specifyDoc->setCol(0, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup("v[we_Extension]", (isset($v["we_Extension"]) ? $v["we_Extension"] : ".html"), we_base_ContentTypes::inst()->getExtension("text/webedition"), "100"), g_l('import', "[extension]")));

		$parts = array();
		if((file_exists($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]) && is_readable($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]))){
			$parts[] = array(
				"headline" => (defined("OBJECT_TABLE")) ? $radioDocs : g_l('import', "[documents]"),
				"html" => $yuiSuggest->getYuiFiles() . $doctypeElement . we_html_tools::getPixel(1, 4) . $templateElement . we_html_tools::getPixel(1, 4) . $storeTo . $yuiSuggest->getYuiCode() . we_html_tools::getPixel(1, 4) . $specifyDoc->getHTML() . we_html_tools::getPixel(1, 4) .
				we_html_tools::htmlFormElementTable($docCategories, g_l('import', "[categories]"), "left", "defaultfont"),
				"space" => 120,
				"noline" => 1
			);
			if(defined("OBJECT_TABLE")){
				$parts[] = array(
					"headline" => $radioObjs,
					"html" => (defined("OBJECT_TABLE")) ? we_html_tools::htmlFormElementTable($CLselect->getHTML(), g_l('import', "[class]"), "left", "defaultfont") .
						we_html_tools::getPixel(1, 4) .
						we_html_tools::htmlFormElementTable($objCategories, g_l('import', "[categories]"), "left", "defaultfont") : "",
					"space" => 120,
					"noline" => 1
				);
			}
		} else{
			if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $v["import_from"])){
				$parts[] = array("html" => we_html_tools::htmlAlertAttentionBox(g_l('import', "[file_exists]") . $_SERVER['DOCUMENT_ROOT'] . $v["import_from"], 1, "530"), "space" => 0, "noline" => 1);
			} else if(!is_readable($_SERVER['DOCUMENT_ROOT'] . $v["import_from"])){
				$parts[] = array("html" => we_html_tools::htmlAlertAttentionBox(g_l('import', "[file_readable]"), 1, "530"), "space" => 0, "noline" => 1);
			}
		}


		$content = we_html_element::jsScript(JS_DIR . "libs/yui/yahoo-min.js") .
			we_html_element::jsScript(JS_DIR . "libs/yui/event-min.js") .
			we_html_element::jsScript(JS_DIR . "libs/yui/connection-min.js") .
			$hdns .
			we_multiIconBox::getHTML("csv", "100%", $parts, 30, "", -1, "", "", false, g_l('import', "[csv_import]"));

		return array($functions, $content);
	}

	function getCSVImportStep3(){

		if(isset($_REQUEST["v"]['we_TemplateName']) && ($_REQUEST["v"]['we_TemplateID'] == 0 || $_REQUEST["v"]['we_TemplateID'] == "")){
			$_REQUEST["v"]['we_TemplateID'] = path_to_id($_REQUEST["v"]['we_TemplateName'], TEMPLATES_TABLE);
		}

		$v = $_REQUEST["v"];

		$records = (isset($_REQUEST["records"])) ? $_REQUEST["records"] : array();
		$we_flds = (isset($_REQUEST["we_flds"])) ? $_REQUEST["we_flds"] : array();
		$attrs = (isset($_REQUEST["attrs"])) ? $_REQUEST["attrs"] : array();

		$hdns = $this->getHdns("v", $v) .
			(isset($_REQUEST["records"]) ? $this->getHdns("records", $_REQUEST["records"]) : "") .
			(isset($_REQUEST["we_flds"]) ? $this->getHdns("we_flds", $_REQUEST["we_flds"]) : "") .
			(isset($_REQUEST["attrs"]) ? $this->getHdns("attrs", $_REQUEST["attrs"]) : "") .
			we_html_element::htmlHidden(array("name" => "v[startCSVImport]", "value" => (isset($v["startCSVImport"]) && $v["startCSVImport"] == 1) ? 1 : 0)) .
			we_html_element::htmlHidden(array("name" => "v[cid]", "value" => -2)) .
			we_html_element::htmlHidden(array("name" => "v[pfx_fn]", "value" => ((!isset($v["pfx_fn"])) ? 0 : $v["pfx_fn"])));
		if(isset($v["rdo_timestamp"])){
			$hdns .= we_html_element::htmlHidden(array("name" => "v[sTimeStamp]", "value" => $v["rdo_timestamp"]));
		}

		$functions = "
function set_button_state() {
	top.frames['wizbusy'].back_enabled = top.frames['wizbusy'].switch_button_state('back', 'back_enabled', 'enabled');
	top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', " . (($_REQUEST["mode"] != 1) ? "'enabled'" : "'disabled'") . ");
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function handle_event(evt) {
	var f = self.document.forms['we_form'];
	switch(evt) {
	case 'previous':
		f.step.value = 2;
		we_submit_form(f, 'wizbody', '" . $this->path . "');
		break;
	case 'next':
		f.step.value=3;
		f.mode.value=1;
		f.elements['v[mode]'].value=1;
		f.elements['v[startCSVImport]'].value=1;
		top.frames['wizbusy'].next_enabled = top.frames['wizbusy'].switch_button_state('next', 'next_enabled', 'disabled');
		we_submit_form(f, 'wizbody', '" . $this->path . "?mode=1');
		break;
	case 'cancel':
		top.close();
		break;
	}
}";

		$db = new DB_WE();

		$records = array();
		$dateFields = array();

		if($v["import_type"] == "documents"){
			$sql_select = "SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . CONTENT_TABLE . "," . LINK_TABLE . " WHERE " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " .
				LINK_TABLE . ".DocumentTable='" . stripTblPrefix(TEMPLATES_TABLE) . "' AND " . LINK_TABLE . ".DID=" . intval($v["we_TemplateID"]) . " AND " . LINK_TABLE . ".Name='completeData'";

			$templateCode = f($sql_select, "Dat", $db);
			$tp = new we_tag_tagParser($templateCode);

			$tags = $tp->getAllTags();

			if(!empty($tags)){
				foreach($tags as $tag){
					if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){
						$tagname = $regs[1];
						if(preg_match('|name="([^"]+)"|i', $tag, $regs) && ($tagname != "var") && ($tagname != "field")){
							$name = $regs[1];
							switch($tagname){
								// tags with text content, links and hrefs
								case "input":
									if(in_array("date", $this->parseAttributes($tag)))
										$dateFields[] = $name;
								case "textarea":
								case "href":
								case "link":
									$records[] = $name;
									break;
							}
						}
					}
				}
			} else{
				array_push($records, "Title");
				array_push($records, "Description");
				array_push($records, "Keywords");
			}
		} else{
			$classFields = $this->getClassFields($v["classID"]);
			foreach($classFields as $classField){
				if($this->isTextField($classField["type"]) || $this->isNumericField($classField["type"]) || $this->isDateField($classField["type"])){
					$records[] = $classField["name"];
				}
				if($this->isDateField($classField["type"])){
					$dateFields[] = $classField["name"];
				}
			}
		}

		$csvFile = $_SERVER['DOCUMENT_ROOT'] . $v["import_from"];
		if(file_exists($csvFile) && is_readable($csvFile)){
			switch($v["csv_enclosed"]){
				case "double_quote":
					$encl = "\"";
					break;
				case "single_quote":
					$encl = "'";
					break;
				case "none":
					$encl = "";
					break;
			}

			$cp = new we_import_CSV;

			$_data = weFile::loadPart($csvFile);

			$cp->setData($_data);

			$cp->setDelim($v["csv_seperator"]);
			$cp->setEnclosure($encl);
			$cp->parseCSV();
			$num = count($cp->FieldNames);
			$recs = array();
			for($c = 0; $c < $num; $c++){
				$recs[$c] = $cp->CSVFieldName($c);
			}
			$val_nodes = array();
			for($i = 0; $i < count($recs); $i++){
				if($v["csv_fieldnames"] && $recs[$i] != ""){
					$val_nodes[$recs[$i]] = $recs[$i];
				} else{
					$val_nodes["f_" . $i] = g_l('import', "[record_field]") . ($i + 1);
				}
			}
		}

		$th = array(array("dat" => g_l('import', "[we_flds]")), array("dat" => g_l('import', "[rcd_flds]")));
		$rows = array();

		reset($records);
		$i = 0;
		while(list(, $record) = each($records)) {
			$hdns .= we_html_element::htmlHidden(array("name" => "records[$i]", "value" => $record));
			$sct_we_fields = new we_html_select(array(
				"name" => "we_flds[$record]",
				"size" => "1",
				"class" => "weSelect",
				"onClick" => "",
				"style" => "")
			);
			reset($val_nodes);
			$sct_we_fields->addOption("", g_l('import', "[any]"));
			foreach($val_nodes as $value => $text){
				$b64_value = (!isset($v["startCSVImport"])) ? base64_encode($value) : $value;
				$sct_we_fields->addOption($b64_value, oldHtmlspecialchars($text));
				if(isset($we_flds[$record])){
					if($value == base64_decode($we_flds[$record])){
						$sct_we_fields->selectOption($b64_value);
					}
				} elseif($value == $record){
					$sct_we_fields->selectOption($b64_value);
				}
			}


			switch($record){
				case "Title":
					$new_record = g_l('import', "[we_title]");
					break;
				case "Description":
					$new_record = g_l('import', "[we_description]");
					break;
				case "Keywords":
					$new_record = g_l('import', "[we_keywords]");
					break;
				default:
					$new_record = '';
			}
			$rows[] = array(
				array("dat" => ($new_record != "") ? $new_record : $record), array("dat" => $sct_we_fields->getHTML()),
			);
			$i++;
		}

		// Associated prefix selector.
		$asocPfx = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 1);
		$asocPfx->setCol(0, 0, array("class" => "defaultfont"), g_l('import', "[pfx]") . "<br>" . we_html_tools::getPixel(1, 2) . "<br>" .
			we_html_tools::htmlTextInput("v[asoc_prefix]", 30, (isset($v["asoc_prefix"]) ? $v["asoc_prefix"] : (($v["import_type"] == "documents") ? g_l('import', "[pfx_doc]") : g_l('import', "[pfx_obj]"))), 255, "onClick=\"self.document.forms['we_form'].elements['v[rdo_filename]'][0].checked=true;\"", "text", 150));

		// Assigned record or attribute field selectors.
		$rcdPfxSelect = new we_html_select(array(
			"name" => "v[rcd_pfx]",
			"size" => "1",
			"class" => "weSelect",
			"onClick" => "self.document.forms['we_form'].elements['v[pfx_fn]'].value=1;self.document.forms['we_form'].elements['v[rdo_filename]'][1].checked=true;",
			"style" => "width: 150px")
		);

		reset($val_nodes);
		foreach($val_nodes as $value => $text){
			$rcdPfxSelect->addOption(oldHtmlspecialchars($value), $text);
			if(isset($v["rcd_pfx"]) && $value == $v["rcd_pfx"]){
				$rcdPfxSelect->selectOption($value);
			}
		}

		$asgndFld = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 1);
		$asgndFld->setCol(0, 0, array("class" => "defaultfont"), g_l('import', "[rcd_fld]") . "<br>" . we_html_tools::getPixel(1, 2) . "<br>" . $rcdPfxSelect->getHTML());

		// Filename selector.
		$fn = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 5, 2);
		$fn->setCol(0, 0, array("colspan" => 2), we_forms::radiobutton(0, (!isset($v["rdo_filename"]) ? true : ($v["rdo_filename"] == 0) ? true : false), "v[rdo_filename]", g_l('import', "[auto]"), true, "defaultfont", "self.document.forms['we_form'].elements['v[pfx_fn]'].value=0;"));
		$fn->setCol(1, 0, array("width" => 25), we_html_tools::getPixel(25, 0));
		$fn->setCol(1, 1, array(), $asocPfx->getHTML());
		$fn->setCol(2, 0, array("height" => 5), "");
		$fn->setCol(3, 0, array("colspan" => 2), we_forms::radiobutton(1, (!isset($v["rdo_filename"]) ? false : ($v["rdo_filename"] == 1) ? true : false), "v[rdo_filename]", g_l('import', "[asgnd]"), true, "defaultfont", "self.document.forms['we_form'].elements['v[pfx_fn]'].value=1;"));
		$fn->setCol(4, 0, array("width" => 25), we_html_tools::getPixel(25, 0));
		$fn->setCol(4, 1, array(), $asgndFld->getHTML());

		$parts = array(
			array(
				"html" => we_html_tools::getPixel(1, 8) . "<br>" . we_html_tools::htmlDialogBorder3(510, 255, $rows, $th, "defaultfont"),
				"space" => 0)
		);


		if(!empty($dateFields)){
			// Timestamp
			$tStamp = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 7, 2);
			$tStamp->setCol(0, 0, array("colspan" => 2), we_forms::radiobutton("Unix", (!isset($v["rdo_timestamp"]) ? 1 : ($v["rdo_timestamp"] == "Unix") ? 1 : 0), "v[rdo_timestamp]", g_l('import', "[uts]"), true, "defaultfont", "", 0, g_l('import', "[unix_timestamp]"), 0, 384));
			$tStamp->setCol(1, 0, array("colspan" => 2), we_html_tools::getPixel(0, 4));
			$tStamp->setCol(2, 0, array("colspan" => 2), we_forms::radiobutton("GMT", (!isset($v["rdo_timestamp"]) ? 0 : ($v["rdo_timestamp"] == "GMT") ? 1 : 0), "v[rdo_timestamp]", g_l('import', "[gts]"), true, "defaultfont", "", 0, g_l('import', "[gmt_timestamp]"), 0, 384));
			$tStamp->setCol(3, 0, array("colspan" => 2), we_html_tools::getPixel(0, 4));
			$tStamp->setCol(4, 0, array("colspan" => 2), we_forms::radiobutton("Format", (!isset($v["rdo_timestamp"]) ? 0 : ($v["rdo_timestamp"] == "Format") ? 1 : 0), "v[rdo_timestamp]", g_l('import', "[fts]"), true, "defaultfont", "", 0, g_l('import', "[format_timestamp]"), 0, 384));
			$tStamp->setCol(5, 0, array("colspan" => 2), we_html_tools::getPixel(0, 4));
			$tStamp->setCol(6, 0, array("width" => 25), we_html_tools::getPixel(25, 0));
			$tStamp->setCol(6, 1, array(), we_html_tools::htmlTextInput("v[timestamp]", 30, (isset($v["timestamp"]) ? $v["timestamp"] : ""), "", "onClick=\"self.document.forms['we_form'].elements['v[rdo_timestamp]'][2].checked=true;\"", "text", 150));

			$parts[] = array(
				"headline" => g_l('import', "[format_date]"),
				"html" => $tStamp->getHTML(),
				"space" => 140
			);
			if(!isset($v["dateFields"])){
				$hdns .= we_html_element::htmlHidden(array("name" => "v[dateFields]", "value" => makeCSVFromArray($dateFields)));
			}
		}

		$conflict = isset($v["collision"]) ? $v["collision"] : 'rename';
		$fn_colsn = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 6, 1);
		$fn_colsn->setCol(0, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(1, 0, array(), we_forms::radiobutton("rename", $conflict == "rename", "nameconflict", g_l('import', "[rename]"), true, 'defaultfont', "self.document.forms['we_form'].elements['v[collision]'].value='rename';"));
		$fn_colsn->setCol(2, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(3, 0, array(), we_forms::radiobutton("replace", $conflict == "replace", "nameconflict", g_l('import', "[replace]"), true, 'defaultfont', "self.document.forms['we_form'].elements['v[collision]'].value='replace';"));
		$fn_colsn->setCol(4, 0, array(), we_html_tools::getPixel(0, 4));
		$fn_colsn->setCol(5, 0, array(), we_forms::radiobutton("skip", $conflict == "skip", "nameconflict", g_l('import', "[skip]"), true, 'defaultfont', "self.document.forms['we_form'].elements['v[collision]'].value='skip';"));

		array_push($parts, array(
			"headline" => g_l('import', '[name_collision]'),
			"html" => $fn_colsn->getHTML(),
			"space" => 140)
		);

		$parts[] = array(
			"headline" => g_l('import', "[name]"),
			"html" => $fn->getHTML(),
			"space" => 140
		);

		$wepos = weGetCookieVariable("but_csv");
		$znr = -1;

		$content = $hdns .
			we_multiIconBox::getJS() .
			we_multiIconBox::getHTML("csv", "100%", $parts, 30, "", $znr, g_l('weClass', "[moreProps]"), g_l('weClass', "[lessProps]"), ($wepos == "down"), g_l('import', "[asgn_rcd_flds]"));

		return array($functions, $content);
	}

	function formWeChooser($table = FILE_TABLE, $width = "", $rootDirID = 0, $IDName = "ID", $IDValue = "0", $Pathname = "Path", $Pathvalue = "/", $cmd = ""){
		if($Pathvalue == ""){
			$Pathvalue = f('SELECT Path FROM ' . escape_sql_query($table) . ' WHERE ID=' . intval($IDValue), "Path", new DB_WE());
		}

		$button = we_button::create_button("select", "javascript:we_cmd('openSelector',document.we_form.elements['$IDName'].value,'$table','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','" . $cmd . "','" . session_id() . "','$rootDirID')");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", 'readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden(array("name" => $IDName, "value" => $IDValue)), we_html_tools::getPixel(20, 4), $button);
	}

}

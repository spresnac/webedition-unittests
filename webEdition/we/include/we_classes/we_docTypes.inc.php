<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_docTypes extends we_class{
	/* The Text that will be shown in the tree-menue */

	var $DocType = 'New DocType';
	var $Extension = DEFAULT_STATIC_EXT;
	var $ParentID = "0";
	var $ParentPath = "";
	var $TemplateID = 0;
	var $ContentTable = "";
	var $IsDynamic = false;
	var $IsSearchable = false;
	var $JavaScript = "";
	var $Notify = "";
	var $NotifyTemplateID = "";
	var $NotifySubject = "";
	var $NotifyOnChange = "";
	var $Templates = "";
	var $SubDir = self::SUB_DIR_NO;
	var $Category = "";
	var $Language = "";

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, "Category", "DocType", "Extension", "ParentID", "ParentPath", "TemplateID", "ContentTable", "IsDynamic", "IsSearchable", "Notify", "NotifyTemplateID", "NotifySubject", "NotifyOnChange", "SubDir", "Templates", "Language");
		$this->Table = DOC_TYPES_TABLE;
	}

	public function we_save($resave = 0){
		$idArr = makeArrayFromCSV($this->Templates);
		$newIdArr = array();
		foreach($idArr as $id){
			$path = id_to_path($id, TEMPLATES_TABLE);
			if($id && $path){
				array_push($newIdArr, $id);
			}
		}
		$this->Templates = makeCSVFromArray($newIdArr);

		if(LANGLINK_SUPPORT){
			if(isset($_REQUEST["we_" . $this->Name . "_LangDocType"])){
				$this->setLanguageLink($_REQUEST["we_" . $this->Name . "_LangDocType"], 'tblDocTypes');
			}
		} else{
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, false);
		}

		return we_class::we_save($resave);
	}

	function we_save_exim(){
		return we_class::we_save(0);
	}

	function saveInSession(&$save){
		$save = array(array());
		foreach($this->persistent_slots as $cur){
			$save[0][$cur] = $this->{$cur};
		}
	}

	function we_initSessDat($sessDat){
		we_class::we_initSessDat($sessDat);
		if(is_array($sessDat)){
			foreach($this->persistent_slots as $cur){
				if(isset($sessDat[0][$cur])){
					$this->{$cur} = $sessDat[0][$cur];
				}
			}
		}
		$this->i_setElementsFromHTTP();

		if($this->Language == ""){
			$this->initLanguageFromParent();
		}
	}

	function initLanguageFromParent(){

		$ParentID = $this->ParentID;
		$i = 0;
		while($this->Language == "") {
			if($ParentID == 0 || $i > 20){
				we_loadLanguageConfig();
				$this->Language = $GLOBALS['weDefaultFrontendLanguage'];
				if($this->Language == ""){
					$this->Language = "de_DE";
				}
			} else{
				$Query = "SELECT Language, ParentID FROM " . $this->DB_WE->escape($this->Table) . " WHERE ID = " . intval($ParentID);
				$this->DB_WE->query($Query);

				while($this->DB_WE->next_record()) {
					$ParentID = $this->DB_WE->f("ParentID");
					$this->Language = $this->DB_WE->f("Language");
				}
			}
			$i++;
		}
	}

	function formLanguage(){

		we_loadLanguageConfig();

		$value = ($this->Language != "" ? $this->Language : $GLOBALS['weDefaultFrontendLanguage']);

		$inputName = "we_" . $this->Name . "_Language";

		$_languages = getWeFrontendLanguagesForBackend();

		if(LANGLINK_SUPPORT){

			$htmlzw = '';
			foreach($_languages as $langkey => $lang){
				$LDID = f('SELECT LDID FROM ' . LANGLINK_TABLE . " WHERE DocumentTable='tblDocTypes' AND DID=" . $this->ID . " AND Locale='" . $langkey . "'", 'LDID', $this->DB_WE);
				if(!$LDID){
					$LDID = 0;
				}
				$htmlzw.= $this->formDocTypes3($lang, $langkey, $LDID);
				$langkeys[] = $langkey;
			}
			$html = $this->htmlFormElementTable($this->htmlSelect($inputName, $_languages, 1, $value, false, 'onchange="dieWerte=\'' . implode(',', $langkeys) . '\'; disableLangDefault(\'we_' . $this->Name . '_LangDocType\',dieWerte,this.options[this.selectedIndex].value);"', "value", 521), g_l('weClass', '[language]'), "left", "defaultfont");
			$html .= "<br/>" . $this->htmlFormElementTable($htmlzw, g_l('weClass', '[languageLinksDefaults]'), 'left', 'defaultfont');
			return $html;
		} else{
			return $this->htmlFormElementTable($this->htmlSelect($inputName, $_languages, 1, $value, false, "", "value", 521), g_l('weClass', '[language]'), "left", "defaultfont");
		}
	}

	function formCategory(){
		$addbut = we_button::create_button("add", "javascript:we_cmd('openCatselector', '', '" . CATEGORY_TABLE . "', '', '', 'fillIDs();opener.we_cmd(\\'dt_add_cat\\', top.allIDs);')", false, 92, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));

		$cats = new MultiDirChooser(521, $this->Category, "dt_delete_cat", $addbut, "", "Icon,Path", CATEGORY_TABLE);
		return $this->htmlFormElementTable($cats->get(), g_l('weClass', "[category]"));
	}

	function addCat($id){
		$cats = makeArrayFromCSV($this->Category);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id, $cats))){
				array_push($cats, $id);
			}
		}
		$this->Category = makeCSVFromArray($cats, true);
	}

	function delCat($id){
		$cats = makeArrayFromCSV($this->Category);
		if(in_array($id, $cats)){
			$pos = getArrayKey($id, $cats);
			if($pos != "" || $pos == "0"){
				array_splice($cats, $pos, 1);
			}
		}
		$this->Category = makeCSVFromArray($cats, true);
	}

	/*
	 * Form functions for generating the html of the input fields
	 */

	function formDocTypeHeader(){
		return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>' . $this->formDocTypes2() . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . $this->formNewDocType() . we_html_tools::getPixel(2, 10) . $this->formDeleteDocType() . '</td>
	</tr>
</table>';
	}

	function formName(){
		return $this->formInputField("", "DocType", "", 24, 520, 32);
	}

	function formDocTypeTemplates(){
		$wecmdenc3 = we_cmd_enc("fillIDs();opener.we_cmd('add_dt_template', top.allIDs);");

		$addbut = we_button::create_button("add", "javascript:we_cmd('openDocselector', '', '" . TEMPLATES_TABLE . "','','','" . $wecmdenc3 . "', '', '', 'text/weTmpl', 1,1)");

		$templ = new MultiDirChooser(521, $this->Templates, "delete_dt_template", $addbut, "", "Icon,Path", TEMPLATES_TABLE);
		return $templ->get();
	}

	function formDocTypeDefaults(){
		return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td colspan="3">' . $this->formDirChooser(we_base_browserDetect::isIE() ? 403 : 409) . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(300, 5) . '</td>
		<td>' . we_html_tools::getPixel(20, 5) . '</td>
		<td>' . we_html_tools::getPixel(200, 5) . '</td>
	</tr>
	<tr>
		<td>' . $this->formSubDir(300) . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . $this->formExtension(200) . '</td>
	</tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr><td colspan="3">' . $this->formTemplatePopup(521) . '</td></tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr>
		<td>' . $this->formIsDynamic() . '</td>
		<td></td>
		<td>' . $this->formIsSearchable() . '</td>
	</tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr><td colspan="3">' . $this->formLanguage(521) . '</td></tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr><td colspan="3">' . $this->formCategory(521) . '</td></tr>
</table>';
	}

	/**
	 * @return string
	 * @param  array $arrHide
	 * @desc   returns HTML-Code for a doctype select-box without doctypes given in $array
	 * @return string
	 */
	function formDocTypes2($arrHide = array()){
		$vals = array();
		$q = getDoctypeQuery($this->DB_WE);
		$this->DB_WE->query('SELECT ID,DocType FROM ' . DOC_TYPES_TABLE . ' ' . $q);

		while($this->DB_WE->next_record()) {
			$v = $this->DB_WE->f("ID");
			$t = $this->DB_WE->f("DocType");
			if(in_array($t, $arrHide)){
				continue;
			}
			$vals[$v] = $t;
		}
		return $this->htmlSelect("DocTypes", $vals, "8", $this->ID, false, 'style="width:328px" onChange="we_cmd(\'change_docType\',this.options[this.selectedIndex].value)"');
	}

	function formDocTypes3($headline, $langkey, $derDT = 0){
		$vals = array();
		$q = getDoctypeQuery($this->DB_WE);
		$this->DB_WE->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " $q");
		$vals[0] = g_l('weClass', '[nodoctype]');
		while($this->DB_WE->next_record()) {
			$v = $this->DB_WE->f("ID");
			$t = $this->DB_WE->f("DocType");
			$vals[$v] = $t;
		}
		return we_html_tools::htmlFormElementTable($this->htmlSelect("we_" . $this->Name . "_LangDocType[" . $langkey . "]", $vals, "1", $derDT, false, ($langkey == $this->Language ? ' disabled="disabled" ' : '') . 'style="width:328px" onChange=""'), $headline, "left", "defaultfont");
	}

	function formDirChooser($width = 100){
		$yuiSuggest = & weSuggest::getInstance();

		$textname = 'we_' . $this->Name . '_ParentPath';
		$idname = 'we_' . $this->Name . '_ParentID';

		//javascript:we_cmd('openDirselector', document.forms['we_form'].elements['" . $idname . "'].value, '" . FILE_TABLE . "', 'document.forms[\\'we_form\\'].elements[\\'" . $idname . "\\'].value', 'document.forms[\\'we_form\\'].elements[\\'" . $textname  . "\\'].value', '', '" . session_id() . "')
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['" . $textname . "'].value");
		$button = we_button::create_button("select", "javascript:we_cmd('openDirselector', document.forms['we_form'].elements['" . $idname . "'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "', '" . $wecmdenc2 . "', '', '" . session_id() . "')");
		$yuiSuggest->setAcId("Path");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($textname, $this->ParentPath);
		$yuiSuggest->setLabel(g_l('weClass', "[dir]"));
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($idname, $this->ParentID);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setWidth($width - (we_base_browserDetect::isIE() ? 0 : 10));
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	function formExtension($width = 100){
		$exts = we_base_ContentTypes::inst()->getExtension('text/webedition');
		return $this->htmlFormElementTable(we_html_tools::getExtensionPopup('we_' . $this->Name . '_Extension', $this->Extension, $exts, $width), g_l('weClass', "[extension]"));
	}

	/* creates the Template PopupMenue */

	function formTemplatePopup($width = 100){
		$tlist = ($this->TemplateID != "" ? $this->TemplateID : '');
		if($this->Templates != ""){
			$tlist.=',' . $this->Templates;
		}
		$tlist = implode(',', array_unique(explode(',', $tlist)));
		$sqlTeil = 'WHERE IsFolder=0 ' . ($tlist != '' ?
				"AND ID IN($tlist)" :
				'');
		return $this->formSelect2("", $width, "TemplateID", TEMPLATES_TABLE, "ID", "Path", g_l('weClass', "[standard_template]"), $sqlTeil, 1, $this->TemplateID, false, "", "", "left", "defaultfont", "", "", array(0, g_l('weClass', "[none]")));
	}

	// return DocumentType HTML
	function formDocTypeDropDown($selected = -1, $width = 200, $onChange = ""){
		$this->DocType = $selected;
		return $this->formSelect2(
				"", // element type
				$width, // width
				"DocType", // name
				DOC_TYPES_TABLE, // table
				"ID", // value in DB
				"DocType", // txt in DB
				g_l('weClass', "[doctype]"), // text
				"ORDER BY DocType", // sql Part
				1, // size
				$selected, // selectedIndex
				false, // multiply
				$onChange, // on change part
				"", // attribs
				"left", // textalign
				"defaultfont", // textclass
				"", // pre code
				"", // postcode
				array(-1, g_l('weClass', "[nodoctype]")) // first element
		);
	}

	function formIsDynamic(){
		$isDyn = $this->IsDynamic ? 1 : 0;
		$n = "we_" . $this->Name . "_IsDynamic";
		$v = $this->IsDynamic;
		$DefaultDynamicExt = DEFAULT_DYNAMIC_EXT;
		$DefaultStaticExt = DEFAULT_STATIC_EXT;

		$out = we_html_element::jsElement('
function switchExt(){
	var a=document.we_form.elements;' .
				($this->ID ?
					'if(confirm("' . g_l('weClass', "[confirm_ext_change]") . '")){' : '') .
				'if(a["we_' . $this->Name . '_IsDynamic"].value==1) {var changeto="' . $DefaultDynamicExt . '";} else {var changeto="' . $DefaultStaticExt . '";}
	a["we_' . $this->Name . '_Extension"].value=changeto;' .
				($this->ID ? '}' : '') . '
}');

		return we_forms::checkbox(1, $v ? true : false, "check_" . $n, g_l('weClass', "[IsDynamic]"), true, "defaultfont", "this.form.elements['" . $n . "'].value = (this.checked ? '1' : '0'); switchExt();") . $this->htmlHidden($n, $v) . $out;
	}

	function formIsSearchable(){
		$isSearchable = $this->IsSearchable ? 1 : 0;
		$n = "we_" . $this->Name . "_IsSearchable";
		$v = $isSearchable;

		return we_forms::checkbox(1, $v ? true : false, "check_" . $n, g_l('weClass', "[IsSearchable]"), false, "defaultfont", "this.form.elements['" . $n . "'].value = (this.checked ? '1' : '0');") . $this->htmlHidden($n, $v);
	}

	function formSubDir($width = 100){
		$vals = array();
		for($i = 0; $i < count(g_l('weClass', "[subdir]")); $i++){
			$vals[] = g_l('weClass', "[subdir][" . $i . ']');
		}
		return $this->htmlFormElementTable($this->htmlSelect('we_' . $this->Name . '_SubDir', $vals, $size = 1, $this->SubDir, false, "", "value", $width), g_l('weClass', "[subdirectory]"));
	}

	function formNewDocType(){
		return we_button::create_button("new_doctype", "javascript:we_cmd('newDocType')");
	}

	function formDeleteDocType(){
		return we_button::create_button("delete_doctype", "javascript:we_cmd('deleteDocType', '" . $this->ID . "')");
	}

}


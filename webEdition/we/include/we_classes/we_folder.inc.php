<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
/* a class for handling directories */
class we_folder extends we_root{
	/* Flag which is set, when the file is a folder  */

	var $IsFolder = 1;
	var $IsClassFolder = 0;
	var $IsNotEditable = 0;
	var $WorkspacePath = '';
	var $WorkspaceID = '';
	var $Language = '';
	var $GreenOnly = 0;
	var $searchclassFolder;
	var $searchclassFolder_class;

	/**
	 * @var weDocumentCustomerFilter
	 */
	var $documentCustomerFilter = ''; // DON'T SET TO NULL !!!!

	/* Constructor */

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'SearchStart', 'SearchField', 'Search', 'Order', 'GreenOnly', 'IsClassFolder', 'IsNotEditable', 'WorkspacePath', 'WorkspaceID', 'Language', 'TriggerID', 'searchclassFolder', 'searchclassFolder_class');
		array_push($this->EditPageNrs, WE_EDITPAGE_PROPERTIES, WE_EDITPAGE_INFO);
		$this->Table = FILE_TABLE;
		$this->ContentType = 'folder';
		$this->Icon = we_base_ContentTypes::FOLDER_ICON;
	}

	public function we_new(){
		parent::we_new();
		$this->adjustEditPageNr();
	}

	function getPath(){
		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){
			return we_root::getPath();
		} else{
			$ParentPath = $this->getParentPath();
			$ParentPath .= ($ParentPath != '/') ? '/' : '';
			return $ParentPath . $this->Text;
		}
	}

	function we_initSessDat($sessDat){
		we_root::we_initSessDat($sessDat);

		if($this->Table == FILE_TABLE || $this->Table == OBJECT_FILES_TABLE){
			if($this->Language == ''){
				$this->initLanguageFromParent();
			}
			if(isset($_REQUEST['we_edit_weDocumentCustomerFilter'])){
				$this->documentCustomerFilter = weDocumentCustomerFilter::getCustomerFilterFromRequest($this);
			} else if(isset($sessDat[3])){ // init webUser from session
				$this->documentCustomerFilter = unserialize($sessDat[3]);
			}
		}
		$this->adjustEditPageNr();

		if(isset($this->searchclassFolder_class) && !is_object($this->searchclassFolder_class)){
			$this->searchclassFolder_class = unserialize($this->searchclassFolder_class);
		}
		if(is_object($this->searchclassFolder_class)){
			$this->searchclassFolder = $this->searchclassFolder_class;
		} else{
			$this->searchclassFolder = new searchtoolsearch();
			$this->searchclassFolder_class = serialize($this->searchclassFolder);
		}
		$this->searchclassFolder->initSearchData();
	}

	/**
	 * adjust EditPageNrs for CUSTOMERFILTER AND DOCLIST
	 */
	function adjustEditPageNr(){
		if(defined('CUSTOMER_FILTER_TABLE')){

			if($this->Table == FILE_TABLE || $this->Table == OBJECT_FILES_TABLE){
				array_push($this->EditPageNrs, WE_EDITPAGE_WEBUSER);
			}
		}
		if($this->Table == FILE_TABLE){
			array_push($this->EditPageNrs, WE_EDITPAGE_DOCLIST);
		}
	}

	function initLanguageFromParent(){

		$ParentID = $this->ParentID;
		$i = 0;
		while($this->Language == '') {
			if($ParentID == 0 || $i > 20){
				we_loadLanguageConfig();
				$this->Language = $GLOBALS['weDefaultFrontendLanguage'];
				if($this->Language == ''){
					$this->Language = 'de_DE';
				}
			} else{
				$Query = 'SELECT Language, ParentID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID = ' . intval($ParentID);
				$this->DB_WE->query($Query);

				while($this->DB_WE->next_record()) {
					$ParentID = $this->DB_WE->f('ParentID');
					$this->Language = $this->DB_WE->f('Language');
				}
			}
			$i++;
		}
	}

	function initByPath($path, $tblName = FILE_TABLE, $IsClassFolder = 0, $IsNotEditable = 0){
		if(substr($path, -1) == '/'){
			$path = substr($path, 0, strlen($path) - 1);
		}
		$id = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $this->DB_WE->escape($path) . '" AND IsFolder=1', 'ID', $this->DB_WE);
		if($id != ''){
			$this->initByID($id, $tblName);
			if(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
				$this->ClassName = 'we_class_folder';
			}
		} else{
			## Folder does not exist, so we have to create it (if user has permissons to create folders)

			$spl = explode('/', $path);
			$folderName = array_pop($spl);
			$p = array();
			$anz = count($spl);
			$last_pid = 0;
			for($i = 0; $i < $anz; $i++){
				array_push($p, array_shift($spl));
				$pa = implode('/', $p);
				if($pa){
					$pid = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $this->DB_WE->escape($pa) . '"', 'ID', $this->DB_WE);
					if(!$pid){
						if(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
							$folder = new we_class_folder();
						} else{
							$folder = new we_folder();
						}
						$folder->we_new();
						$folder->Table = $tblName;
						$folder->ParentID = $last_pid;
						$folder->Text = $p[$i];
						$folder->Filename = $p[$i];
						$folder->IsClassFolder = $IsClassFolder;
						$folder->IsNotEditable = $IsClassFolder;
						$folder->Path = $pa;
						$folder->save();
						$last_pid = $folder->ID;
					} else{
						$last_pid = $pid;
					}
				}
			}
			$this->we_new();
			$this->Icon = $IsClassFolder ? we_base_ContentTypes::CLASS_FOLDER_ICON : we_base_ContentTypes::FOLDER_ICON;
			$this->Table = $tblName;
			$this->IsClassFolder = $IsClassFolder;
			$this->ParentID = $last_pid;
			$this->Text = $folderName;
			$this->Filename = $folderName;
			$this->Path = $path;
			$this->IsNotEditable = $IsNotEditable;
			$this->save();
		}
		return true;
	}

	function i_canSaveDirinDir(){
		if(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
			if($this->Icon == '' && $this->ParentID == 0){
				return false;
			} else{
				if($this->ParentID != 0){
					$this->Icon = we_base_ContentTypes::FOLDER_ICON;
					$this->IsClassFolder = 0;
				}
			}

			if($this->ParentID != 0){
				$this->DB_WE->query('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE IsNotEditable=1');
				while($this->DB_WE->next_record()) {
					if($this->DB_WE->f('ID') == $this->ParentID){
						return false;
					}
				}
			}
		}
		return true;
	}

	function i_sameAsParent(){
		if($this->ID){
			$db = new DB_WE();
			$pid = $this->ParentID;
			while($pid) {
				if($this->ID == $pid){
					return true;
				}
				$pid = f('SELECT ParentID FROM ' . $this->Table . '  WHERE ID=' . intval($pid), 'ParentID', $db);
			}
		}
		return false;
	}

	/* saves the folder */

	public function we_save($resave = 0, $skipHook = 0){
		$this->i_setText();
		$objFolder = (defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE);
		if($objFolder){
			$this->ClassName = 'we_class_folder';
		}
		$update = ($this->OldPath != '' && $this->OldPath != $this->Path);
		if($update && !$objFolder){
			if(file_exists($this->OldPath) && file_exists($this->Path)){
				t_e('Both paths exists!', $this->OldPath, $this->Path);
				return false;
			}
			//leave old dir for parent save
			$tmp = $this->Path;
			$this->Path = $this->OldPath;
			if(!parent::we_save($resave)){
				return false;
			}
			//set back path, since we want to move the dir
			$this->Path = $tmp;
			if(!$this->writeFolder()){
				return false;
			}
		}

		if(!$update || $objFolder){
			if(!parent::we_save($resave)){
				return false;
			}
			if(!$this->writeFolder()){
				return false;
			}
		}
		if(defined('OBJECT_TABLE') && $this->Table == OBJECT_TABLE){
			$f = new we_class_folder();
			$f->initByPath($this->Path, OBJECT_FILES_TABLE, 0, 1);
		}
		$this->resaveWeDocumentCustomerFilter();

		if($resave == 0 && $update){
			weNavigationCache::clean(true);
		}
		if(LANGLINK_SUPPORT && isset($_REQUEST['we_' . $this->Name . '_LanguageDocID']) && $_REQUEST['we_' . $this->Name . '_LanguageDocID'] != 0){
			$this->setLanguageLink($_REQUEST['we_' . $this->Name . '_LanguageDocID'], 'tblFile', true, ($this->ClassName == 'we_class_folder'));
		} else{
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, true); //if language changed, we
		}
		/* hook */
		if($skipHook == 0){
			$hook = new weHook('save', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		return true;
	}

	function changeLanguageRecursive(){

		$DB_WE = new DB_WE;
		$DB_WE2 = new DB_WE;
		$DB_WE3 = new DB_WE;

		$language = $this->Language;

		// Adapt tblLangLink-entries of documents and objects to the new language (all published and unpublished)
		//$query = "SELECT ID, Language FROM " . $DB_WE->escape($this->Table) . " WHERE Path LIKE '" . $DB_WE->escape($this->Path) . "/%' AND ((Published = 0 AND ContentType = 'folder') OR (Published > 0 AND (ContentType = 'text/webEdition' OR ContentType = 'text/html' OR ContentType = 'objectFile')))";
		$query = 'SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType IN ("text/webEdition","text/html","objectFile")';

		if(!$DB_WE->query($query)){
			return false;
		}
		while($DB_WE->next_record()) {
			if($DB_WE->Record['Language'] != $language){
				$documentTable = ($DB_WE->escape($this->Table) == FILE_TABLE) ? 'tblFile' : 'tblObjectFile';
				$query = 'SELECT LDID, Locale FROM ' . LANGLINK_TABLE . ' WHERE DID = ' . intval($DB_WE->Record['ID']) . ' AND DocumentTable = "' . $documentTable . '"';
				$existLangLinks = false;
				$deleteLangLinks = false;
				if($DB_WE2->query($query)){
					$ldidArray = array();
					while($DB_WE2->next_record()) {
						$existLangLinks = true;
						$ldidArray[] = $DB_WE2->Record['LDID'];
						if($DB_WE2->Record['Locale'] == $language){
							$deleteLangLinks = true;
						}
					}
					if($existLangLinks){
						if($deleteLangLinks){
							$didCondition = 'DID = ' . intval($DB_WE->Record['ID']);
							foreach($ldidArray as $ldid){
								$didCondition .= ' OR DID = ' . intval($ldid);
							}
							$query = 'DELETE FROM ' . LANGLINK_TABLE . ' WHERE (' . $didCondition . ')  AND DocumentTable = "' . $documentTable . '"';
							$DB_WE3->query($query);
						} else{
							$query = 'UPDATE ' . LANGLINK_TABLE . ' SET DLOCALE = "' . $language . '" WHERE DID = ' . intval($DB_WE->Record['ID']);
							$DB_WE3->query($query);
							$query = 'UPDATE ' . LANGLINK_TABLE . ' SET LOCALE = "' . $language . '" WHERE LDID = ' . intval($DB_WE->Record['ID']);
							$DB_WE3->query($query);
						}
					}
				}
			}
		}

		// Adapt tblLangLink-entries of folders to the new language
		$query = 'SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType = "folder"';
		if(!$DB_WE->query($query)){
			return false;
		}
		while($DB_WE->next_record()) {
			$documentTable = 'tblFile';
			$query = 'DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID = ' . intval($DB_WE->Record['ID']) . ' AND DocumentTable = "' . $documentTable . '" AND IsFolder > 0 AND Locale = "' . $language . '"';
			$DB_WE2->query($query);
			$query = 'UPDATE ' . LANGLINK_TABLE . ' SET DLOCALE = "' . $language . '" WHERE DID = ' . intval($DB_WE->Record['ID']) . ' AND DocumentTable = "' . $documentTable . '" AND IsFolder > 0';
			$DB_WE2->query($query);
		}

		// Change language of published documents, objects
		$query = 'UPDATE ' . $DB_WE->escape($this->Table) . ' SET Language = "' . $DB_WE->escape($this->Language) . '" WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ((Published = 0 AND ContentType = "folder") OR (Published > 0 AND ContentType IN ("text/webEdition","text/html","objectFile")))';

		if(!$DB_WE->query($query)){
			return false;
		}

		// Change Language of unpublished documents
		$query = 'SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType IN ("text/webEdition","text/html","objectFile")';

		if(!$DB_WE->query($query)){
			return false;
		}
		while($DB_WE->next_record()) {
			$query = 'SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID = ' . intval($DB_WE->f('ID')) . ' AND DocTable = "' . stripTblPrefix($this->Table) . '" AND Active = 1';
			$DocumentObject = f($query, 'DocumentObject', $DB_WE2);
			if($DocumentObject != ''){
				$DocumentObject = unserialize($DocumentObject);
				$DocumentObject[0]['Language'] = $this->Language;
				$DocumentObject = serialize($DocumentObject);
				$DocumentObject = str_replace("'", "\'", $DocumentObject);

				$query = 'UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocumentObject="' . $DB_WE->escape($DocumentObject) . '" WHERE DocumentID=' . intval($DB_WE->f('ID')) . ' AND DocTable = "' . stripTblPrefix($this->Table) . '" AND Active = 1';
				if(!$DB_WE2->query($query)){
					return false;
				}
			}
		}

		// Sprache auch bei den einzelnen Objekten aendern
		if($this->Table == OBJECT_FILES_TABLE){
			// Klasse feststellen
			$ClassPathArray = explode('/', $this->Path);
			$ClassPath = '/' . $ClassPathArray[1];
			$q = 'SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path = "' . $ClassPath . '"';
			$cid = $pid = f($q, 'ID', $DB_WE);
			$_obxTable = OBJECT_X_TABLE . $cid;

			$query = 'UPDATE ' . $DB_WE->escape($_obxTable) . ' SET OF_Language = "' . $DB_WE->escape($this->Language) . '" WHERE OF_Path LIKE "' . $DB_WE->escape($this->Path) . '/%" ';

			if(!$DB_WE->query($query)){
				return false;
			}
		}

		return true;
	}

	function changeTriggerIDRecursive(){

		$DB_WE = new DB_WE;
		$DB_WE2 = new DB_WE;

		$language = $this->TriggerID;

		// Change TriggerID of published documents first
		if(!$DB_WE->query('UPDATE ' . $DB_WE->escape($this->Table) . ' SET TriggerID = ' . intval($this->TriggerID) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ((Published = 0 AND ContentType = "folder") OR (Published > 0 AND ContentType IN ("text/webEdition","text/html","objectFile")))')){
			return false;
		}
		// Change Language of unpublished documents

		if(!$DB_WE->query('SELECT ID FROM ' . $DB_WE->escape($this->Table) . ' WHERE Path LIKE "' . $DB_WE->escape($this->Path) . '/%" AND ContentType IN ("text/webEdition","text/html","objectFile")')){
			return false;
		}
		while($DB_WE->next_record()) {
			$DocumentObject = f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID = ' . intval($DB_WE->f('ID')) . ' AND DocTable = "' . stripTblPrefix($this->Table) . '" AND Active = 1', 'DocumentObject', $DB_WE2);
			if($DocumentObject != ''){
				$DocumentObject = unserialize($DocumentObject);
				$DocumentObject[0]['TriggerID'] = $this->TriggerID;
				$DocumentObject = str_replace("'", "\'", serialize($DocumentObject));

				if(!$DB_WE2->query('UPDATE ' . TEMPORARY_DOC_TABLE . ' SET DocumentObject="' . $DB_WE->escape($DocumentObject) . '" WHERE DocumentID=' . intval($DB_WE->f('ID')) . ' AND DocTable = "' . stripTblPrefix($this->Table) . '" AND Active = 1')){
					return false;
				}
			}
		}

		// TriggerID auch bei den einzelnen Objekten aendern
		if($this->Table == OBJECT_FILES_TABLE){
			// Klasse feststellen
			list(, $ClassPath) = explode('/', $this->Path);
			$cid = $pid = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path = "/' . $DB_WE->escape($ClassPath) . '"', 'ID', $DB_WE);
			$_obxTable = OBJECT_X_TABLE . $cid;

			if(!$DB_WE->query('UPDATE ' . $DB_WE->escape($_obxTable) . ' SET OF_TriggerID = ' . intval($this->TriggerID) . ' WHERE OF_Path LIKE "' . $DB_WE->escape($this->Path) . '/%" ')){
				return false;
			}
		}

		return true;
	}

	protected function i_setText(){
		$this->Text = ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? $this->Filename : $this->Text;
	}

	function i_filenameDouble(){
		return f('SELECT ID FROM ' . escape_sql_query($this->Table) . ' WHERE Path="' . escape_sql_query($this->Path) . '" AND ID != ' . intval($this->ID), 'ID', $this->DB_WE);
	}

	function i_filenameEmpty(){
		$fn = ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? $this->Filename : $this->Text;
		return ($fn == '') ? true : false;
	}

	/* returns 0 because it is a directory */

	function getfilesize(){
		return 0;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
				return 'we_templates/we_editor_properties.inc.php';
			case WE_EDITPAGE_INFO:
				return 'we_templates/we_editor_info.inc.php';
			case WE_EDITPAGE_WEBUSER:
				return 'we_modules/customer/editor_weDocumentCustomerFilter.inc.php';
			case WE_EDITPAGE_DOCLIST:
				return 'we_doclist/we_editor_doclist.inc.php';
			default:
				$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PROPERTIES;
				return 'we_templates/we_editor_properties.inc.php';
		}
	}

	function formPath(){
		$ws = get_ws($this->Table);
		if(intval($this->ParentID) == 0 && $ws){
			$wsa = makeArrayFromCSV($ws);
			$this->ParentID = $wsa[0];
			$this->ParentPath = id_to_path($this->ParentID, $this->Table, $this->DB_WE);
		}

		$userCanChange = we_hasPerm('CHANGE_DOC_FOLDER_PATH') || ($this->CreatorID == $_SESSION['user']['ID']) || (!$this->ID);
		if($this->ID != 0 && $this->ParentID == 0 && $this->ParentPath == '/' && defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
			$userCanChange = false;
		}
		$content = (!$userCanChange) ? ('<table border="0" cellpadding="0" cellspacing="0"><tr><td><span class="defaultfont">' . $this->Path . '</span></td></tr>') : '<table border="0" cellpadding="0" cellspacing="0">
	<tr><td class="defaultfont">' . $this->formInputField('', ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE) ? 'Filename' : 'Text', g_l('weClass', '[filename]'), 50, 388, 255, 'onChange=_EditorFrame.setEditorIsHot(true);pathOfDocumentChanged();') . '</td><td></td><td></td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 10) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3" class="defaultfont">' . $this->formDirChooser(388) . '</td></tr>';
		if(defined('OBJECT_FILES_TABLE') && $this->Table == OBJECT_FILES_TABLE){
			$content .='	<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
		<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
		<tr><td colspan="3" class="defaultfont">' . $this->formTriggerDocument() . '</td></tr>';

			$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', "[availableAfterSave]"));

			$content .='<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[grant_tid_expl]") . $_disabledNote, 2, 388, false) . '</td><td>' .
				we_button::create_button("ok", "javascript:if(_EditorFrame.getEditorIsHot()) { " . we_message_reporting::getShowMessageCall(g_l('weClass', "[saveFirstMessage]"), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('changeTriggerIDRecursive','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', !empty($_disabledNote)) . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
		}

		$content .='</table>';
		return $content;
	}

	function formLanguage(){
		we_loadLanguageConfig();

		$value = ($this->Language != '' ? $this->Language : $GLOBALS['weDefaultFrontendLanguage']);

		$inputName = 'we_' . $this->Name . '_Language';

		$_languages = getWeFrontendLanguagesForBackend();
		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			$isobject = (defined('OBJECT_FILES_TABLE') && ($this->Table == OBJECT_FILES_TABLE) ? 1 : 0);
			foreach($_languages as $langkey => $lang){
				$LDID = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblFile" AND IsObject=' . intval($isobject) . ' AND DID=' . intval($this->ID) . ' AND Locale="' . $langkey . '"', 'LDID', $this->DB_WE);
				if(!$LDID){
					$LDID = 0;
				}
				$divname = 'we_' . $this->Name . '_LanguageDocDiv[' . $langkey . ']';
				$htmlzw.= '<div id="' . $divname . '" ' . ($this->Language == $langkey ? ' style="display:none" ' : '') . '>' . $this->formLanguageDocument($lang, $langkey, $LDID) . '</div>';
				$langkeys[] = $langkey;
			}

			return
				'<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
				<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, " onblur=\"_EditorFrame.setEditorIsHot(true);\" onchange=\"dieWerte='" . implode(',', $langkeys) . "';showhideLangLink('we_" . $this->Name . "_LanguageDocDiv',dieWerte,this.options[this.selectedIndex].value);_EditorFrame.setEditorIsHot(true);\"", "value", 508) . '</td></tr>
				<tr><td>' . we_html_tools::getPixel(2, 20) . '</td></tr>
				<tr><td class="defaultfont" align="left">' . g_l('weClass', '[languageLinksDir]') . '</td></tr>
			</table>' . we_html_element::htmlBr() . $htmlzw;
		} else{

			return '<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, " onblur=\"_EditorFrame.setEditorIsHot(true);\" onchange=\"_EditorFrame.setEditorIsHot(true);\"", "value", 388) . '</td></tr>
			</table>';
		}
	}

	function formChangeOwners(){
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('modules_users', "[grant_owners_expl]") . $_disabledNote, 2, 388, false) . '</td><td>' .
			we_button::create_button('ok', 'javascript:if(_EditorFrame.getEditorIsHot()) { ' . we_message_reporting::getShowMessageCall(g_l('weClass', '[saveFirstMessage]'), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('changeR','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', !empty($_disabledNote)) . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
	}

	function formChangeLanguage(){
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[grant_language_expl]") . $_disabledNote, 2, 388, false) . '</td><td>' .
			we_button::create_button("ok", "javascript:if(_EditorFrame.getEditorIsHot()) { " . we_message_reporting::getShowMessageCall(g_l('weClass', "[saveFirstMessage]"), we_message_reporting::WE_MESSAGE_ERROR) . "; } else {;we_cmd('changeLanguageRecursive','" . $GLOBALS["we_transaction"] . "');}", true, 100, 22, '', '', !empty($_disabledNote)) . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$parents = array(0, $this->ID);
		we_getParentIDs(FILE_TABLE, $this->ID, $parents);
		$ParentsCSV = makeCSVFromArray($parents, true);
		$_disabledNote = ($this->ID ? '' : ' ' . g_l('weClass', '[availableAfterSave]'));

		//javascript:we_cmd('openDirselector', document.forms[0].elements['" . $idname . "'].value, '" . $this->Table . "', 'document.forms[\\'we_form\\'].elements[\\'" . $idname . "\\'].value', '', 'var parents = \\'".$ParentsCSV."\\';if(parents.indexOf(\\',\\' WE_PLUS currentID WE_PLUS \\',\\') > -1){" . we_message_reporting::getShowMessageCall(g_l('alert',"[copy_folder_not_valid]"), we_message_reporting::WE_MESSAGE_ERROR) . "}else{opener.top.we_cmd(\\'copyFolder\\', currentID,".$this->ID.",1,\\'".$this->Table."\\');}');
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc3 = we_cmd_enc("var parents = '" . $ParentsCSV . "';if(parents.indexOf(',' WE_PLUS currentID WE_PLUS ',') > -1){" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folder_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . "}else{opener.top.we_cmd('copyFolder', currentID," . $this->ID . ",1,'" . $this->Table . "');}");
		$but = we_button::create_button("select", ($this->ID ?
					"javascript:we_cmd('openDirselector', '" . $wecmdenc1 . "', '" . $this->Table . "', '" . $wecmdenc1 . "', '', '" . $wecmdenc3 . "')" :
					"javascript:" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folders_no_id]'), we_message_reporting::WE_MESSAGE_ERROR))
				, true, 100, 22, "", "", !empty($_disabledNote));

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[copy_owners_expl]") . $_disabledNote, 2, 388, false) . '</td><td>' .
			$this->htmlHidden($idname, $this->CopyID) . $but . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
	}

	################ internal functions ######

	function writeFolder($pub = 0){
		if($this->Path == $this->OldPath || !$this->OldPath){
			return $this->saveToServer();
		} else{
			if(!$this->moveAtServer()){
				return false;
			}
			$this->modifyIndexPath();
			$this->modifyLinks();
			$this->modifyChildrenPath();
		}
		$this->OldPath = $this->Path;
		return true;
	}

	function modifyIndexPath(){
		$this->DB_WE->query('UPDATE ' . INDEX_TABLE . ' SET Workspace="' . $this->DB_WE->escape($this->Path . substr($this->DB_WE->f('Workspace'), strlen($this->OldPath))) . '" WHERE Workspace LIKE "' . $this->DB_WE->escape($this->OldPath) . '%"');
	}

	function modifyLinks(){
		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){
			$this->DB_WE->query('UPDATE ' . $this->Table . ' SET Path=CONCAT("' . $this->Path . '",SUBSTRING(Path,' . (strlen($this->OldPath) + 1) . ')) WHERE Path LIKE "' . $this->OldPath . '/%" OR Path="' . $this->OldPath . '"');
		}
	}

	function modifyChildrenPath(){
		@ignore_user_abort(true);
		$DB_WE = new DB_WE;
		// Update Paths also in Doctype Table
		$DB_WE->query('UPDATE ' . DOC_TYPES_TABLE . ' SET ParentPath="' . $DB_WE->escape($this->Path) . '" WHERE ParentID=' . intval($this->ID));
		$DB_WE->query('SELECT ID,ClassName FROM ' . $DB_WE->escape($this->Table) . ' WHERE ParentID=' . intval($this->ID));
		while($DB_WE->next_record()) {
			@set_time_limit(30);
			$we_doc = $DB_WE->f('ClassName');
			if($we_doc){
				$we_doc = new $we_doc();
				$we_doc->initByID($DB_WE->f('ID'), $this->Table, we_class::LOAD_TEMP_DB); // BUG4397 - added LOAD_TEMP_DB to parameters
				$we_doc->ModifyPathInformation($this->ID);
			} else{
				t_e('No class set at entry ', $DB_WE->f('ID'), $this->Table);
			}
		}
		@ignore_user_abort(false);
	}

	/* for internal use */

	function moveAtServer(){
		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){

			// renames the folder on the local machine in the root-dir
			$path = $this->getRealPath();
			$oldPath = $this->getRealPath(true);
			if(!file_exists($path) && !file_exists($oldPath)){
				t_e('old path doesn\'t exist', $oldPath);
				return false;
			}
			if($this->Table != TEMPLATES_TABLE){
				// renames the folder on the local machine in the root-dir+site-dir
				$sitepath = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($this->Path, 1);
				$siteoldPath = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr($this->OldPath, 1);
				if(file_exists($sitepath) && file_exists($siteoldPath)){
					t_e('old and new dir exists!', $sitepath, $siteoldPath);
					return false;
				}
				if(!file_exists($sitepath) && !file_exists($siteoldPath)){
					t_e('old directory doesn\'t exist!', $oldPath);
					return false;
				}
			}

			if(!file_exists($path) && file_exists($oldPath)){
				if(!rename($oldPath, $path)){
					return false;
				}
			}

			if($this->Table != TEMPLATES_TABLE){
				//we are responsible for site dir!
				if(!file_exists($sitepath) && file_exists($siteoldPath)){
					if(!rename($siteoldPath, $sitepath)){
						//move back other dir!
						rename($path, $oldPath);
						return false;
					}
				}
			}
		}
		return true;
	}

	/* for internal use */

	function saveToServer(){
		if($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE){
			$isTemplFolder = ($this->Table == TEMPLATES_TABLE);

			$path = $this->getPath();

			// creates the folder on the local machine in the root-dir
			if(!we_util_File::createLocalFolder(($isTemplFolder ? TEMPLATES_PATH : $_SERVER['DOCUMENT_ROOT']), $path))
				return false;

			// creates the folder on the local machine in the root-dir+site-dir
			if(!$isTemplFolder){
				if(!we_util_File::createLocalFolder($_SERVER['DOCUMENT_ROOT'] . SITE_DIR, $path))
					return false;
			}
		}
		return true;
	}

	/**
	 * Beseitigt #Bug 3705: sorgt daf�r, das auch leere Dokumentenordner bei einem REbuild angelegt werden
	 */
	function we_rewrite(){
		if(parent::we_rewrite()){
			if($this->Table == FILE_TABLE){
				$this->we_save(1);
			} else{
				return true;
			}
		} else{
			return false;
		};
	}

	/**
	 * @desc	the function modifies document EditPageNrs set
	 */
	function checkTabs(){

	}

	protected function updateRemoteLang($db, $id, $lang, $type){
		$oldLang = f('SELECT Language FROM ' . $this->Table . ' WHERE ID=' . $id, 'Language', $db);
		if($oldLang == $lang){
			return;
		}
		//update Lang of doc
		$db->query('UPDATE ' . $this->Table . ' SET Language="' . $lang . '" WHERE ID=' . $id);
		//update LangLink:
		$db->query('UPDATE ' . LANGLINK_TABLE . ' SET DLocale="' . $lang . '" WHERE DID=' . $id . ' AND DocumentTable="' . $type . '"');
		//drop invalid entries => is this safe???
		$db->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=' . $id . ' AND DocumentTable="' . $type . '" AND DLocale!="' . $lang . '"');
	}

}


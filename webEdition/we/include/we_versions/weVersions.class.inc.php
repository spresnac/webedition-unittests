<?php

/**
 * webEdition CMS
 *
 * $Rev: 5977 $
 * $Author: lukasimhof $
 * $Date: 2013-03-19 17:21:49 +0100 (Tue, 19 Mar 2013) $
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
class weVersions{

	protected $ID;
	protected $documentID;
	protected $documentTable;
	protected $documentElements;
	protected $documentScheduler;
	protected $documentCustomFilter;
	protected $timestamp;
	protected $status;
	protected $version = 1;
	protected $binaryPath;
	protected $modifications;
	protected $modifierID;
	protected $IP;
	protected $Browser;
	protected $ContentType;
	protected $Text;
	protected $ParentID;
	protected $Icon;
	protected $CreationDate;
	protected $CreatorID;
	protected $Path;
	protected $TemplateID;
	protected $Filename;
	protected $Extension;
	protected $IsDynamic;
	protected $IsSearchable;
	protected $ClassName;
	protected $DocType;
	protected $Category;
	protected $RestrictOwners;
	protected $Owners;
	protected $OwnersReadOnly;
	protected $Language;
	protected $WebUserID;
	protected $Workspaces;
	protected $ExtraWorkspaces;
	protected $ExtraWorkspacesSelected;
	protected $Templates;
	protected $ExtraTemplates;
	protected $MasterTemplateID;
	protected $TableID;
	protected $ObjectID;
	protected $IsClassFolder;
	protected $IsNotEditable;
	protected $Charset;
	protected $active;
	protected $fromScheduler;
	protected $fromImport;
	protected $resetFromVersion;
	public $contentTypes = array();
	public $persistent_slots = array();
	public $modFields = array();

	/**
	 *  Constructor for class 'weVersions'
	 */
	public function __construct(){
		$this->contentTypes = self::getContentTypesVersioning();

		/**
		 * fields from tblFile and tblObjectFiles which can be modified
		 */
		$this->modFields = array(
			'status' => 1,
			'ParentID' => 2,
			'Text' => 3,
			'IsSearchable' => 4,
			'Category' => 5,
			'CreatorID' => 6,
			'RestrictOwners' => 7,
			'Owners' => 8,
			'OwnersReadOnly' => 9,
			'Language' => 10,
			'WebUserID' => 11,
			'documentElements' => 12,
			'documentScheduler' => 13,
			'documentCustomFilter' => 14,
			'TemplateID' => 15,
			'Filename' => 16,
			'Extension' => 17,
			'IsDynamic' => 18,
			'DocType' => 19,
			'Workspaces' => 20,
			'ExtraWorkspaces' => 21,
			'ExtraWorkspacesSelected' => 22,
			'Templates' => 23,
			'ExtraTemplates' => 24,
			'Charset' => 25,
			'InGlossar' => 26
		);
	}

	/**
	 * @return unknown
	 */
	public function getActive(){
		return $this->active;
	}

	/**
	 * @return unknown
	 */
	public function getBinaryPath(){
		return $this->binaryPath;
	}

	/**
	 * @return unknown
	 */
	public function getBrowser(){
		return $this->browser;
	}

	/**
	 * @return unknown
	 */
	public function getCategory(){
		return $this->category;
	}

	/**
	 * @return unknown
	 */
	public function getCharset(){
		return $this->charset;
	}

	/**
	 * @return unknown
	 */
	public function getClassName(){
		return $this->className;
	}

	/**
	 * @return unknown
	 */
	public function getContentType(){
		return $this->contentType;
	}

	/**
	 * @return unknown
	 */
	public function getCreationDate(){
		return $this->creationDate;
	}

	/**
	 * @return unknown
	 */
	public function getCreatorID(){
		return $this->creatorID;
	}

	/**
	 * @return unknown
	 */
	public function getDocType(){
		return $this->docType;
	}

	/**
	 * @return unknown
	 */
	public function getDocumentCustomFilter(){
		return $this->documentCustomFilter;
	}

	/**
	 * @return unknown
	 */
	public function getDocumentElements(){
		return $this->documentElements;
	}

	/**
	 * @return unknown
	 */
	public function getDocumentID(){
		return $this->documentID;
	}

	/**
	 * @return unknown
	 */
	public function getDocumentScheduler(){
		return $this->documentScheduler;
	}

	/**
	 * @return unknown
	 */
	public function getDocumentTable(){
		return $this->documentTable;
	}

	/**
	 * @return unknown
	 */
	public function getExtension(){
		return $this->extension;
	}

	/**
	 * @return unknown
	 */
	public function getExtraTemplates(){
		return $this->ExtraTemplates;
	}

	/**
	 * @return unknown
	 */
	public function getMasterTemplateID(){
		return $this->MasterTemplateID;
	}

	/**
	 * @return unknown
	 */
	public function getExtraWorkspaces(){
		return $this->extraWorkspaces;
	}

	/**
	 * @return unknown
	 */
	public function getExtraWorkspacesSelected(){
		return $this->extraWorkspacesSelected;
	}

	/**
	 * @return unknown
	 */
	public function getFilename(){
		return $this->filename;
	}

	/**
	 * @return unknown
	 */
	public function getFromImport(){
		return $this->fromImport;
	}

	/**
	 * @return unknown
	 */
	public function getFromScheduler(){
		return $this->fromScheduler;
	}

	/**
	 * @return unknown
	 */
	public function getIcon(){
		return $this->icon;
	}

	/**
	 * @return unknown
	 */
	public function getID(){
		return $this->iD;
	}

	/**
	 * @return unknown
	 */
	public function getIP(){
		return $this->iP;
	}

	/**
	 * @return unknown
	 */
	public function getIsClassFolder(){
		return $this->isClassFolder;
	}

	/**
	 * @return unknown
	 */
	public function getIsDynamic(){
		return $this->isDynamic;
	}

	/**
	 * @return unknown
	 */
	public function getIsNotEditable(){
		return $this->isNotEditable;
	}

	/**
	 * @return unknown
	 */
	public function getIsSearchable(){
		return $this->isSearchable;
	}

	/**
	 * @return unknown
	 */
	public function getLanguage(){
		return $this->language;
	}

	/**
	 * @return unknown
	 */
	public function getModifications(){
		return $this->modifications;
	}

	/**
	 * @return unknown
	 */
	public function getModifierID(){
		return $this->modifierID;
	}

	/**
	 * @return unknown
	 */
	public function getObjectID(){
		return $this->objectID;
	}

	/**
	 * @return unknown
	 */
	public function getOwners(){
		return $this->owners;
	}

	/**
	 * @return unknown
	 */
	public function getOwnersReadOnly(){
		return $this->ownersReadOnly;
	}

	/**
	 * @return unknown
	 */
	public function getParentID(){
		return $this->parentID;
	}

	/**
	 * @return unknown
	 */
	public function getPath(){
		return $this->path;
	}

	/**
	 * @return unknown
	 */
	public function getResetFromVersion(){
		return $this->resetFromVersion;
	}

	/**
	 * @return unknown
	 */
	public function getRestrictOwners(){
		return $this->restrictOwners;
	}

	function getStatus(){
		return $this->status;
	}

	/**
	 * @return unknown
	 */
	public function getTableID(){
		return $this->tableID;
	}

	/**
	 * @return unknown
	 */
	public function getTemplateID(){
		return $this->templateID;
	}

	/**
	 * @return unknown
	 */
	public function getTemplates(){
		return $this->templates;
	}

	/**
	 * @return unknown
	 */
	public function getText(){
		return $this->text;
	}

	/**
	 * @return unknown
	 */
	public function getTimestamp(){
		return $this->timestamp;
	}

	function getVersion(){
		return $this->version;
	}

	/**
	 * @return unknown
	 */
	public function getWebUserID(){
		return $this->webUserID;
	}

	/**
	 * @return unknown
	 */
	public function getWorkspaces(){
		return $this->workspaces;
	}

	/**
	 * @param unknown_type $active
	 */
	public function setActive($active){
		$this->active = $active;
	}

	/**
	 * @param unknown_type $binaryPath
	 */
	public function setBinaryPath($binaryPath){
		$this->binaryPath = $binaryPath;
	}

	/**
	 * @param unknown_type $Browser
	 */
	public function setBrowser($browser){
		$this->browser = $browser;
	}

	/**
	 * @param unknown_type $Category
	 */
	public function setCategory($category){
		$this->category = $category;
	}

	/**
	 * @param unknown_type $Charset
	 */
	public function setCharset($charset){
		$this->charset = $charset;
	}

	/**
	 * @param unknown_type $ClassName
	 */
	public function setClassName($className){
		$this->className = $className;
	}

	/**
	 * @param unknown_type $ContentType
	 */
	public function setContentType($contentType){
		$this->contentType = $contentType;
	}

	/**
	 * @param unknown_type $CreationDate
	 */
	public function setCreationDate($creationDate){
		$this->creationDate = $creationDate;
	}

	/**
	 * @param unknown_type $CreatorID
	 */
	public function setCreatorID($creatorID){
		$this->creatorID = $creatorID;
	}

	/**
	 * @param unknown_type $DocType
	 */
	public function setDocType($docType){
		$this->docType = $docType;
	}

	/**
	 * @param unknown_type $documentCustomFilter
	 */
	public function setDocumentCustomFilter($documentCustomFilter){
		$this->documentCustomFilter = $documentCustomFilter;
	}

	/**
	 * @param unknown_type $documentElements
	 */
	public function setDocumentElements($documentElements){
		$this->documentElements = $documentElements;
	}

	/**
	 * @param unknown_type $documentID
	 */
	public function setDocumentID($documentID){
		$this->documentID = $documentID;
	}

	/**
	 * @param unknown_type $documentScheduler
	 */
	public function setDocumentScheduler($documentScheduler){
		$this->documentScheduler = $documentScheduler;
	}

	/**
	 * @param unknown_type $documentTable
	 */
	public function setDocumentTable($documentTable){
		$this->documentTable = $documentTable;
	}

	/**
	 * @param unknown_type $Extension
	 */
	public function setExtension($extension){
		$this->extension = $extension;
	}

	/**
	 * @param unknown_type $ExtraTemplates
	 */
	public function setExtraTemplates($ExtraTemplates){
		$this->ExtraTemplates = $ExtraTemplates;
	}

	/**
	 * @param unknown_type $MasterTemplateID
	 */
	public function setMasterTemplateID($MasterTemplateID){
		$this->MasterTemplateID = $MasterTemplateID;
	}

	/**
	 * @param unknown_type $ExtraWorkspaces
	 */
	public function setExtraWorkspaces($extraWorkspaces){
		$this->extraWorkspaces = $extraWorkspaces;
	}

	/**
	 * @param unknown_type $ExtraWorkspacesSelected
	 */
	public function setExtraWorkspacesSelected($extraWorkspacesSelected){
		$this->extraWorkspacesSelected = $extraWorkspacesSelected;
	}

	/**
	 * @param unknown_type $Filename
	 */
	public function setFilename($filename){
		$this->filename = $filename;
	}

	/**
	 * @param unknown_type $fromImport
	 */
	public function setFromImport($fromImport){
		$this->fromImport = $fromImport;
	}

	/**
	 * @param unknown_type $fromScheduler
	 */
	public function setFromScheduler($fromScheduler){
		$this->fromScheduler = $fromScheduler;
	}

	/**
	 * @param unknown_type $Icon
	 */
	public function setIcon($icon){
		$this->icon = $icon;
	}

	/**
	 * @param unknown_type $ID
	 */
	public function setID($iD){
		$this->iD = $iD;
	}

	/**
	 * @param unknown_type $IP
	 */
	public function setIP($iP){
		$this->iP = $iP;
	}

	/**
	 * @param unknown_type $IsClassFolder
	 */
	public function setIsClassFolder($isClassFolder){
		$this->isClassFolder = $isClassFolder;
	}

	/**
	 * @param unknown_type $IsDynamic
	 */
	public function setIsDynamic($isDynamic){
		$this->isDynamic = $isDynamic;
	}

	/**
	 * @param unknown_type $IsNotEditable
	 */
	public function setIsNotEditable($isNotEditable){
		$this->isNotEditable = $isNotEditable;
	}

	/**
	 * @param unknown_type $IsSearchable
	 */
	public function setIsSearchable($isSearchable){
		$this->isSearchable = $isSearchable;
	}

	/**
	 * @param unknown_type $Language
	 */
	public function setLanguage($language){
		$this->language = $language;
	}

	/**
	 * @param unknown_type $modifications
	 */
	public function setModifications($modifications){
		$this->modifications = $modifications;
	}

	/**
	 * @param unknown_type $modifierID
	 */
	public function setModifierID($modifierID){
		$this->modifierID = $modifierID;
	}

	/**
	 * @param unknown_type $ObjectID
	 */
	public function setObjectID($objectID){
		$this->objectID = $objectID;
	}

	/**
	 * @param unknown_type $Owners
	 */
	public function setOwners($owners){
		$this->owners = $owners;
	}

	/**
	 * @param unknown_type $OwnersReadOnly
	 */
	public function setOwnersReadOnly($ownersReadOnly){
		$this->ownersReadOnly = $ownersReadOnly;
	}

	/**
	 * @param unknown_type $ParentID
	 */
	public function setParentID($parentID){
		$this->parentID = $parentID;
	}

	/**
	 * @param unknown_type $Path
	 */
	public function setPath($path){
		$this->path = $path;
	}

	/**
	 * @param unknown_type $resetFromVersion
	 */
	public function setResetFromVersion($resetFromVersion){
		$this->resetFromVersion = $resetFromVersion;
	}

	/**
	 * @param unknown_type $RestrictOwners
	 */
	public function setRestrictOwners($restrictOwners){
		$this->restrictOwners = $restrictOwners;
	}

	function setStatus($status){
		$this->status = $status;
	}

	/**
	 * @param unknown_type $TableID
	 */
	public function setTableID($tableID){
		$this->tableID = $tableID;
	}

	/**
	 * @param unknown_type $TemplateID
	 */
	public function setTemplateID($templateID){
		$this->templateID = $templateID;
	}

	/**
	 * @param unknown_type $Templates
	 */
	public function setTemplates($templates){
		$this->templates = $templates;
	}

	/**
	 * @param unknown_type $Text
	 */
	public function setText($text){
		$this->text = $text;
	}

	/**
	 * @param unknown_type $timestamp
	 */
	public function setTimestamp($timestamp){
		$this->timestamp = $timestamp;
	}

	function setVersion($version){
		$this->version = $version;
	}

	/**
	 * @param unknown_type $WebUserID
	 */
	public function setWebUserID($webUserID){
		$this->webUserID = $webUserID;
	}

	/**
	 * @param unknown_type $Workspaces
	 */
	public function setWorkspaces($workspaces){
		$this->workspaces = $workspaces;
	}

	/**
	 * ContentTypes which apply for versioning
	 * all except classes, templates and folders
	 */
	public static function getContentTypesVersioning(){

		$contentTypes = array();
		$contentTypes[] = 'all';
		$ct = we_base_ContentTypes::inst();
		foreach($ct->getContentTypes() as $k){
			//if($k != "object" && $k != "text/weTmpl" && $k != "folder") { vor #4120
			if($k != "object" && $k != "folder" && $k != "class_folder"){
				$contentTypes[] = $k;
			}
		}
		return $contentTypes;
	}

	/**
	 * @abstract set first document object if no versions exist
	 * for contentType = text/webedition
	 */
	public function setInitialDocObject($obj){
		if(is_object($obj)){
			$index = $obj->ID . '_' . $obj->Table;
			$_SESSION['weS']['versions']['versionToCompare'][$index] = serialize($obj);
			if(in_array($obj->ContentType, self::getContentTypesVersioning()) && $obj->ID != 0 && !$this->versionsExist($obj->ID, $obj->ContentType)){
				$_SESSION['weS']['versions']['initialVersions'] = true;
				$this->save($obj);
			}
		}
	}

	/**
	 * @abstract count versions
	 */
	public function countVersions($id, $contentType){
		return f("SELECT COUNT(1) AS Count FROM " . VERSIONS_TABLE . " WHERE documentId = " . intval($id) . " AND ContentType = '" . escape_sql_query($contentType) . "'", 'Count', new DB_WE());
	}

	/**
	 * @abstract looks if versions exist for the document
	 */
	public static function versionsExist($id, $contentType){
		if(self::countVersions($id, $contentType) == 0){
			return false;
		}
		return true;
	}

	/**
	 * @abstract get versions of one document / object
	 * @return array of version-records of one document / object
	 */
	function loadVersionsOfId($id, $table, $where = ''){

		$versionArr = array();
		$versionArray = array();
		$db = new DB_WE();
		$tblFields = weVersions::getFieldsFromTable(VERSIONS_TABLE);

		$db->query('SELECT * FROM ' . VERSIONS_TABLE . ' WHERE documentID=' . intval($id) . ' AND documentTable="' . $db->escape($table) . '" ' . $where . ' ORDER BY version ASC');
		while($db->next_record()) {
			foreach($tblFields as $k => $v){
				$versionArray[$v] = $db->f("" . $v);
			}

			$versionArr[] = $versionArray;
		}

		return $versionArr;
	}

	/**
	 * @abstract get one version of document / object
	 * @return array of version-records of one document / object
	 */
	function loadVersion($where = "1"){
		$versionArray = array();
		$db = new DB_WE();
		$tblFields = weVersions::getFieldsFromTable(VERSIONS_TABLE);

		$db->query('SELECT * FROM ' . VERSIONS_TABLE . ' ' . $where);
		while($db->next_record()) {
			foreach($tblFields as $k => $v){
				$versionArray[$v] = $db->f("" . $v);
			}
		}

		return $versionArray;
	}

	/**
	 * @abstract cases in which versions are created
	 * 1. if documents are imported
	 * 2. there exists no version-record of a document but in tblfile oder tblobjectsfile (document/object was not created new)
	 * 3. if document / object is saved, published or unpublished
	 */
	public function save($docObj, $status = "saved"){

		if(isset($_SESSION["user"]["ID"])){
			$_SESSION['weS']['versions']['fromImport'] = 0;

			//import
			if(isset($_REQUEST["jupl"]) && $_REQUEST["jupl"]){
				$_SESSION['weS']['versions']['fromImport'] = 1;
				$this->saveVersion($docObj);
			} elseif(isset($_REQUEST["pnt"]) && $_REQUEST["pnt"] == "wizcmd"){
				if($_REQUEST["v"]["type"] == "CSVImport" || $_REQUEST["v"]["type"] == "GXMLImport"){
					$_SESSION['weS']['versions']['fromImport'] = 1;
					$this->saveVersion($docObj);
				} elseif(isset($_SESSION['weS']['ExImRefTable'])){
					foreach($_SESSION['weS']['ExImRefTable'] as $k => $v){
						if($v["ID"] == $docObj->ID){
							$_SESSION['weS']['versions']['fromImport'] = 1;
							$this->saveVersion($docObj);
						}
					}
				}
			} elseif(isset($_REQUEST['we_cmd'][0]) && ($_REQUEST['we_cmd'][0] == "siteImport" || $_REQUEST['we_cmd'][0] == "import_files")){
				$_SESSION['weS']['versions']['fromImport'] = 1;
				$this->saveVersion($docObj);
			} else{
				if((isset($_SESSION['weS']['versions']['fromScheduler']) && $_SESSION['weS']['versions']['fromScheduler']) || (isset($_REQUEST['we_cmd'][0]) && ($_REQUEST['we_cmd'][0] == "save_document" || $_REQUEST['we_cmd'][0] == "unpublish" || $_REQUEST['we_cmd'][0] == "revert_published")) || (isset($_REQUEST["cmd"]) && ($_REQUEST["cmd"] == "ResetVersion" || $_REQUEST["cmd"] == "PublishDocs" || $_REQUEST["cmd"] == "ResetVersionsWizard")) || (isset($_REQUEST["type"]) && $_REQUEST["type"] == "reset_versions") || (isset($_SESSION['weS']['versions']['initialVersions']) && $_SESSION['weS']['versions']['initialVersions'])){
					if(isset($_SESSION['weS']['versions']['initialVersions'])){
						unset($_SESSION['weS']['versions']['initialVersions']);
					}
					$this->saveVersion($docObj, $status);
				}
			}
		}
	}

	/**
	 * @abstract apply preferences
	 */
	function CheckPreferencesCtypes($ct){

		//if folder was saved don' make versions (if path was changed of folder)
		if(isset($GLOBALS['we_doc']->ClassName)){
			if($GLOBALS['we_doc']->ClassName == "we_folder" || $GLOBALS['we_doc']->ClassName == "we_class_folder"){
				return false;
			}
		}

		//apply content types in preferences

		switch($ct){
			case "text/webedition":
				return VERSIONING_TEXT_WEBEDITION;
			case "image/*":
				return VERSIONING_IMAGE;
			case "text/html":
				return VERSIONING_TEXT_HTML;
			case "text/js":
				return VERSIONING_TEXT_JS;
			case "text/css":
				return VERSIONING_TEXT_CSS;
			case "text/plain":
				return VERSIONING_TEXT_PLAIN;
			case "text/htaccess":
				return VERSIONING_TEXT_HTACCESS;
			case "text/weTmpl":
				return VERSIONING_TEXT_WETMPL;
			case "application/x-shockwave-flash":
				return VERSIONING_FLASH;
			case "video/quicktime":
				return VERSIONING_QUICKTIME;
			case "application/*":
				return VERSIONING_SONSTIGE;
			case "text/xml":
				return VERSIONING_TEXT_XML;
			case "objectFile":
				return VERSIONING_OBJECT;
		}

		return true;
	}

	function CheckPreferencesTime($docID, $docTable){

		$db = new DB_WE();

		if($docTable == TEMPLATES_TABLE){
			$prefTimeDays = (VERSIONS_TIME_DAYS_TMPL != "-1") ? VERSIONS_TIME_DAYS_TMPL : "";
			$prefTimeWeeks = (VERSIONS_TIME_WEEKS_TMPL != "-1") ? VERSIONS_TIME_WEEKS_TMPL : "";
			$prefTimeYears = (VERSIONS_TIME_YEARS_TMPL != "-1") ? VERSIONS_TIME_YEARS_TMPL : "";
		} else{
			$prefTimeDays = (VERSIONS_TIME_DAYS != "-1") ? VERSIONS_TIME_DAYS : "";
			$prefTimeWeeks = (VERSIONS_TIME_WEEKS != "-1") ? VERSIONS_TIME_WEEKS : "";
			$prefTimeYears = (VERSIONS_TIME_YEARS != "-1") ? VERSIONS_TIME_YEARS : "";
		}

		$prefTime = 0;
		if($prefTimeDays != ""){
			$prefTime = $prefTime + $prefTimeDays;
		}
		if($prefTimeWeeks != ""){
			$prefTime = $prefTime + $prefTimeWeeks;
		}
		if($prefTimeYears != ""){
			$prefTime = $prefTime + $prefTimeYears;
		}

		if($prefTime != 0){
			$deletetime = time() - $prefTime;
			//initial version always stays
			$where = " timestamp < " . $deletetime . " AND CreationDate!=timestamp ";
			$this->deleteVersion("", $where);
		}
		$prefAnzahl = intval($docTable == TEMPLATES_TABLE ? VERSIONS_ANZAHL_TMPL : VERSIONS_ANZAHL);

		$anzahl = f("SELECT COUNT(1) AS Count FROM " . VERSIONS_TABLE . " WHERE documentId = " . intval($docID) . " AND documentTable = '" . $db->escape($docTable) . "'", "Count", $db);

		if($anzahl > $prefAnzahl && $prefAnzahl != ""){
			$toDelete = $anzahl - $prefAnzahl;
			$m = 0;
			$db->query("SELECT ID, version FROM " . VERSIONS_TABLE . " WHERE documentId = " . intval($docID) . " AND documentTable = '" . $db->escape($docTable) . "' ORDER BY version ASC LIMIT " . intval($toDelete));
			while($db->next_record()) {
				if($m < $toDelete){
					$this->deleteVersion($db->f('ID'), '');
					$m++;
				}
			}
		}
	}

	/**
	 * @abstract make new version-entry in DB
	 */
	function saveVersion($document, $status = "saved"){
		if(isset($_SESSION["user"]["ID"])){
			$documentObj = "";
			$db = new DB_WE();
			if(is_object($document)){
				$documentObj = $document;
				$document = $this->objectToArray($document);
			}

			if(isset($document["documentCustomerFilter"]) && is_object($document["documentCustomerFilter"])){
				$document["documentCustomerFilter"] = $this->objectToArray($document["documentCustomerFilter"]);
			}

			$writeVersion = true;

			//preferences
			if(!$this->CheckPreferencesCtypes($document["ContentType"])){
				$writeVersion = false;
			}

			if((isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] == "save_document") &&
				isset($_REQUEST['we_cmd'][5]) && $_REQUEST['we_cmd'][5]){
				$status = "published";
			}

			if($document["ContentType"] != "objectFile" && $document["ContentType"] != "text/webedition" && $document["ContentType"] != "text/html" && !($document["ContentType"] == "text/weTmpl" && defined("VERSIONS_CREATE_TMPL") && VERSIONS_CREATE_TMPL)){
				$status = "saved";
			}

			if($this->IsScheduler() && $status != "unpublished" && $status != "deleted"){
				$status = "published";
			}

			if(isset($_SESSION['weS']['versions']['doPublish']) && $_SESSION['weS']['versions']['doPublish']){
				$status = "published";
			}

			if($document["ContentType"] == "objectFile" || $document["ContentType"] == "text/webedition" || $document["ContentType"] == "text/html" || ($document["ContentType"] == "text/weTmpl" && defined("VERSIONS_CREATE_TMPL") && VERSIONS_CREATE_TMPL)){
				if($document["ContentType"] != "text/weTmpl" && (defined("VERSIONS_CREATE") && VERSIONS_CREATE) && $status != "published" && isset($_REQUEST['we_cmd'][5]) && !$_REQUEST['we_cmd'][5]){
					$writeVersion = false;
				}
				if($document["ContentType"] == "text/weTmpl" && (defined("VERSIONS_CREATE_TMPL") && VERSIONS_CREATE_TMPL) && $status != "published" && isset($_REQUEST['we_cmd'][5]) && !$_REQUEST['we_cmd'][5]){
					$writeVersion = false;
				}
			}

			//look if there were made changes
			if(isset($_SESSION['weS']['versions']['versionToCompare'][$document["ID"] . '_' . $document["Table"]]) && $_SESSION['weS']['versions']['versionToCompare'][$document["ID"] . '_' . $document["Table"]] != ''){
				$lastEntry = unserialize($_SESSION['weS']['versions']['versionToCompare'][$document["ID"] . '_' . $document["Table"]]);
				$lastEntry = $this->objectToArray($lastEntry);

				$diffExists = array();
				if(is_array($document) && is_array($lastEntry)){
					$diffExists = $this->array_diff_values($document, $lastEntry);
				}
				$lastEntry = $this->getLastEntry($document["ID"], $document["Table"]);

				if((($status == 'published' || $status == 'saved') && isset($lastEntry['status']) && $status == $lastEntry['status']) && empty($diffExists) && $this->versionsExist($document["ID"], $document["ContentType"])){
					$writeVersion = false;
				}
			}

			if($writeVersion){
				$mods = true;
				$tblversionsFields = $this->getFieldsFromTable(VERSIONS_TABLE);

				$set = array();

				foreach($tblversionsFields as $fieldName){
					if($fieldName != 'ID'){
						if(isset($document[$fieldName])){
							$set[$fieldName] = $document[$fieldName];
						} else{
							$set[$fieldName] = $this->makePersistentEntry($fieldName, $status, $document, $documentObj);
						}
					}
				}

				if(!empty($set) && $mods){
					$theSet = we_database_base::arraySetter($set);
					$db->query('INSERT INTO ' . VERSIONS_TABLE . ' SET ' . $theSet);
					$vers = (isset($document["version"]) ? $document["version"] : $this->version);
					$db->query('UPDATE ' . VERSIONS_TABLE . ' SET active = 0 WHERE documentID = ' . intval($document['ID']) . ' AND documentTable = "' . $db->escape($document["Table"]) . '" AND version != ' . intval($vers));
					$_SESSION['weS']['versions']['versionToCompare'][$document["ID"] . '_' . $document["Table"]] = serialize($documentObj);
				}
			}

			$this->CheckPreferencesTime($document["ID"], $document["Table"]);
		}
	}

	/**
	 * @abstract give the persistent fieldnames the values if you save, publish or unpublish
	 * persistent fieldnames are fields which are not in tblfile or tblobjectsfile and are always saved
	 * @return value of field
	 */
	function makePersistentEntry($fieldName, $status, $document, $documentObj){
		$entry = '';
		$db = new DB_WE();

		switch($fieldName){
			case "documentID":
				$entry = $document["ID"];
				break;
			case "documentTable":
				$entry = $document["Table"];
				break;
			case "documentElements":
				if(!empty($document["elements"]) && is_array($document["elements"])){
					//$entry = urlencode(htmlentities(serialize($document["elements"]), ENT_QUOTES));
					$entry = gzcompress(serialize($document["elements"]), 9);
				}
				break;
			case "documentScheduler":
				if(!empty($document["schedArr"]) && is_array($document["schedArr"])){
					//$entry = urlencode(htmlentities(serialize($document["schedArr"]), ENT_QUOTES));
					$entry = gzcompress(serialize($document["schedArr"]), 9);
				}
				break;
			case "documentCustomFilter":
				if(!empty($document["documentCustomerFilter"]) && is_array($document["documentCustomerFilter"])){
					//$entry = urlencode(htmlentities(serialize($document["documentCustomerFilter"]), ENT_QUOTES));
					$entry = gzcompress(serialize($document["documentCustomerFilter"]), 9);
				}
				break;
			case "timestamp":
				$lastEntryVersion = f("SELECT ID FROM " . VERSIONS_TABLE . " WHERE documentID=" . intval($document["ID"]) . " AND documentTable='" . $db->escape($document["Table"]) . "' LIMIT 1", 'ID', $db);
				$entry = ($lastEntryVersion ? time() : $document['CreationDate']);
				break;
			case "status":
				$this->setStatus($status);
				$entry = $status;
				break;
			case "Charset":
				if(isset($document['elements']['Charset']['dat'])){
					$entry = $document['elements']['Charset']['dat'];
				}
				break;
			case "version":
				$lastEntryVersion = f('SELECT MAX(version) AS version FROM ' . VERSIONS_TABLE . ' WHERE documentID=' . intval($document["ID"]) . ' AND documentTable="' . $db->escape($document["Table"]) . '"', 'version', $db);
				if($lastEntryVersion){
					$newVersion = $lastEntryVersion + 1;
					$this->setVersion($newVersion);
				}
				$entry = $this->getVersion();
				break;
			case "binaryPath":
				$binaryPath = "";

				//$binaryPath = f("SELECT binaryPath FROM " . VERSIONS_TABLE . " WHERE binaryPath!='' AND version<'".intval($this->version)."' AND documentTable='".$db->escape($document['Table'])."' AND documentID='".abs($document['ID'])."'  ORDER BY version DESC limit 1 ","binaryPath",$db);
				//if($document["ContentType"]=="objectFile") { vor #4120
				if($document["ContentType"] == "objectFile" || $document["ContentType"] == "text/weTmpl"){
					$binaryPath = "";
				} else{
					$documentPath = substr($document["Path"], 1);
					$siteFile = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $documentPath;

					$vers = $this->getVersion();

					$versionName = $document["ID"] . "_" . $document["Table"] . "_" . $vers . $document["Extension"];
					$binaryPath = VERSION_DIR . $versionName . '.gz';

					if($document["IsDynamic"]){
						$this->writePreviewDynFile($document['ID'], $siteFile, $_SERVER['DOCUMENT_ROOT'] . $binaryPath, $documentObj);
					} elseif(file_exists($siteFile) && $document["Extension"] == ".php" && ($document["ContentType"] == 'text/webedition' || $document["ContentType"] == 'text/html')){

						we_util_File::saveFile($_SERVER['DOCUMENT_ROOT'] . $binaryPath, gzencode(file_get_contents($siteFile), 9));
					} else{
						if(isset($document['TemplatePath']) && $document['TemplatePath'] != "" && substr($document['TemplatePath'], -18) != "/we_noTmpl.inc.php" && $document['ContentType'] == "text/webedition"){
							$includeTemplate = preg_replace('/.tmpl$/i', '.php', $document['TemplatePath']);
							$this->writePreviewDynFile($document['ID'], $includeTemplate, $_SERVER['DOCUMENT_ROOT'] . $binaryPath, $documentObj);
						} else{
							we_util_File::saveFile($_SERVER['DOCUMENT_ROOT'] . $binaryPath, gzencode(file_get_contents($siteFile), 9));
						}
					}
				}

				$this->binaryPath = $binaryPath;
				$entry = $binaryPath;
				break;
			case "modifications":

				$modifications = array();

				/* get fields which can be changed */
				$fields = $this->getFieldsFromTable(VERSIONS_TABLE);

				foreach($fields as $key => $val){
					if(isset($this->modFields[$val])){

						$query = "SELECT " . $val . " FROM " . VERSIONS_TABLE . " WHERE version <" . intval($this->version) . " AND status != 'deleted' AND documentID=" . intval($document["ID"]) . " AND documentTable='" . $db->escape($document["Table"]) . "' ORDER BY version DESC LIMIT 1";
						$db->query($query);
						if($db->next_record()){
							$lastEntryField = $db->f("" . $val);
						}

						if(isset($lastEntryField)){

							if($val == "Text" && $document["ContentType"] != "objectFile"){
								$val = "";
							}

							if(isset($document[$val])){
								if($document[$val] == ""){
									switch($val){
										case 'DocType':
										case 'IsSearchable':
										case 'WebUserID':
										case 'TemplateID':
											$document[$val] = 0;
											break;
									}
								}
								//if($lastEntryField!="" && $document[$val]!="") {
								if($document[$val] != $lastEntryField){
									$modifications[] = $val;
								}
								//}
								elseif(($lastEntryField == "" && $document[$val] == "") || ($lastEntryField == $document[$val])){
									// do nothing
								} else{
									$modifications[] = $val;
								}
							} else{
								if($val == "documentElements" || $val == "documentScheduler" || $val == "documentCustomFilter"){
									$newData = array();
									$diff = array();
									if($lastEntryField == ""){
										$lastEntryField = array();
									} else{
										$lastEntryField = unserialize((substr_compare($lastEntryField, 'a%3A', 0, 4) == 0 ?
												html_entity_decode(urldecode($lastEntryField), ENT_QUOTES) : gzuncompress($lastEntryField))
										);
									}
									switch($val){
										case "documentElements":
											//TODO: imi: check if we need next-level information from nested arrays
											if(!empty($document["elements"])){
												$newData = $document["elements"];
												foreach($newData as $k => $vl){
													if(isset($lastEntryField[$k]) && is_array($lastEntryField[$k]) && is_array($vl)){
														if(isset($vl['dat'])){
															$vl['dat'] = is_array($vl['dat']) ? serialize($vl['dat']) : $vl['dat'];
														}
														if(isset($lastEntryField[$k]['dat'])){
															$lastEntryField[$k]['dat'] = is_array($lastEntryField[$k]['dat']) ? serialize($lastEntryField[$k]['dat']) : $lastEntryField[$k]['dat'];
														}
														$_diff = array_diff_assoc($vl, $lastEntryField[$k]);
														if(!empty($_diff) && isset($_diff['dat'])){
															$diff[] = $_diff;
														}
													}
												}
											}
											break;
										case "documentScheduler":
											//TODO: imi: check if count() is ok (do we allways have two arrays?)
											if(count($document["schedArr"]) != count($lastEntryField)){
												$diff['schedArr'] = true;
											} elseif(!empty($document["schedArr"])){
												$newData = $document["schedArr"];
												foreach($newData as $k => $vl){
													if(isset($lastEntryField[$k]) && is_array($lastEntryField[$k]) && is_array($vl)){
														$_tmpArr1 = array();
														$_tmpArr2 = array();
														foreach($vl as $_k => $_v){
															$_tmpArr1[$_k] = is_array($_v) ? serialize($_v) : $_v;
														}
														foreach($lastEntryField[$k] as $_k => $_v){
															$_tmpArr2[$_k] = is_array($_v) ? serialize($_v) : $_v;
														}
														$_diff = array_diff_assoc($_tmpArr1, $_tmpArr2);
														if(!empty($_diff)){
															$diff = $_diff;
														}
													}
												}
											}
											break;
										case "documentCustomFilter":
											//TODO: imi: check if we need both foreach
											if(isset($document["documentCustomerFilter"]) && is_array($document["documentCustomerFilter"]) && is_array($lastEntryField)){
												$_tmpArr1 = array();
												$_tmpArr2 = array();
												foreach($document["documentCustomerFilter"] as $_k => $_v){
													$_tmpArr1[$_k] = is_array($_v) ? serialize($_v) : $_v;
												}
												foreach($lastEntryField as $_k => $_v){
													$_tmpArr2[$_k] = is_array($_v) ? serialize($_v) : $_v;
												}
												$_diff = array_diff_assoc($_tmpArr1, $_tmpArr2);
												if(!empty($_diff)){
													$diff['documentCustomerFilter'] = $_diff;
												}
											}

											break;
									}

									if(!empty($diff)){
										$modifications[] = $val;
									}
								}
								/* if($document["ContentType"]=="application/x-shockwave-flash" || $document["ContentType"]=="application/*"
								  || $document["ContentType"]=="video/quicktime" || $document["ContentType"]=="image/*") {
								  if($val=="binaryPath" && $this->binaryPath!="" && $lastEntryField!=$this->binaryPath) {
								  //$modifications[] = $val;
								  }
								  } */

								if($val == "status" && $lastEntryField != $this->status){
									$modifications[] = $val;
								}
							}
						}
					}
				}

				$modConstants = $this->getConstantsOfMod($modifications);

				if($modConstants != ""){
					$entry = $modConstants;
				} else{
					$entry = "";
				}
				break;
			case "modifierID":
				$modifierID = (isset($_SESSION["user"]["ID"])) ? $_SESSION["user"]["ID"] : '';
				$entry = $modifierID;
				break;
			case "IP":
				$ip = $_SERVER['REMOTE_ADDR'];
				$entry = $ip;
				break;
			case "Browser":
				$browser = $_SERVER['HTTP_USER_AGENT'];
				$entry = $browser;
				break;
			case "active":
				$entry = 1;
				break;
			case "fromScheduler":
				$entry = $this->IsScheduler();
				break;
			case "fromImport":
				$entry = (isset($_SESSION['weS']['versions']['fromImport']) && $_SESSION['weS']['versions']['fromImport']) ? 1 : 0;
				break;
			case "resetFromVersion":
				$entry = (isset($document["resetFromVersion"]) && $document["resetFromVersion"] != "") ? $document["resetFromVersion"] : 0;
				break;
			default:
				$entry = "";
		}

		return $entry;
	}

	/**
	 * @abstract look if scheduler was called
	 * @return boolean
	 */
	function IsScheduler(){
		$fromScheduler = 0;
		if(isset($_SESSION['weS']['versions']['fromScheduler'])){
			$fromScheduler = $_SESSION['weS']['versions']['fromScheduler'];
		}

		return $fromScheduler;
	}

	/**
	 * @abstract get differences between two arrays
	 * @return array with difference
	 */
	function array_diff_values($newArr, $oldArr){

		$diff = array();

		if(!is_array($newArr)){
			$newArr = array();
		}
		if(!is_array($oldArr)){
			$oldArr = array();
		}
		if(empty($newArr) && empty($oldArr)){

		} elseif(!empty($newArr) && !empty($oldArr)){
			$newTestArr = $newArr; // bug #7191
			$oldTestArr = $oldArr;
			foreach($newTestArr as $tk => $tv){
				if(is_array($tv)){
					//TODO: imi: maybe we should serialize instead of unset: to prevent loss of information
					unset($newTestArr[$tk]);
					unset($oldTestArr[$tk]);
				}
			}
			foreach($oldTestArr as $tk => $tv){
				if(is_array($tv)){
					unset($newTestArr[$tk]);
					unset($oldTestArr[$tk]);
				}
			}

			$_diff = array_diff_assoc($newTestArr, $oldTestArr);
			if(isset($_diff['Published'])){
				unset($_diff['Published']);
			}
			if(isset($_diff['ModDate'])){
				unset($_diff['ModDate']);
			}
			if(isset($_diff['EditPageNr'])){
				unset($_diff['EditPageNr']);
			}
			if(isset($_diff['DocStream'])){
				unset($_diff['DocStream']);
			}
			if(isset($_diff['DB_WE'])){
				unset($_diff['DB_WE']);
			}
			if(!empty($_diff)){
				$diff = $_diff;
			}

			foreach($newArr as $k => $v){
				if(is_array($v)){
					if($k == 'schedArr'){
						//TODO: imi: check if count() is ok (do we allways have two arrays?)
						if(count($v) != count($oldArr['schedArr'])){
							$diff['schedArr'] = true;
						} else{
							foreach($v as $key => $val){
								if(isset($oldArr['schedArr'][$key]) && is_array($oldArr['schedArr'][$key]) && is_array($val)){
									$_tmpArr1 = array();
									$_tmpArr2 = array();
									foreach($val as $_k => $_v){
										$_tmpArr1[$_k] = is_array($_v) ? serialize($_v) : $_v;
									}
									foreach($oldArr['schedArr'][$key] as $_k => $_v){
										$_tmpArr2[$_k] = is_array($_v) ? serialize($_v) : $_v;
									}
									$_diff = array_diff_assoc($_tmpArr1, $_tmpArr2);
									if(!empty($_diff)){
										$diff['schedArr'][$key] = $_diff;
									}
								}
							}
						}
					} elseif($k == 'elements'){
						foreach($v as $key => $val){
							//TODO: imi: should we serialize inside the foreachs instead of simulating the array to string conversion?
							if(isset($oldArr['elements'][$key]) && is_array($oldArr['elements'][$key]) && is_array($val)){
								if(isset($val['dat']) && is_array($val['dat']) && isset($oldArr['elements'][$key]['dat']) && is_array($oldArr['elements'][$key]['dat'])){
									$_tmpArr1 = array();
									$_tmpArr2 = array();

									foreach($val['dat'] as $_k => $_v){
										//$valDat[$index] = is_array($value) ? serialize($value) : $value;
										$_tmpArr1[$_k] = is_array($_v) ? 'Array' : $_v;
									}

									foreach($oldArr['elements'][$key]['dat'] as $_k => $_v){
										//$oldArrDat[$index] = is_array($value) ? serialize($value) : $value;
										$_tmpArr2[$_k] = is_array($_v) ? 'Array' : $_v;
									}

									$_diff = array_diff_assoc($_tmpArr1, $_tmpArr2);
									unset($val['dat']);
									unset($oldArr['elements'][$key]['dat']);
									$diff['elements'][$key] = $_diff; 
								} else{
									if(isset($val['dat']) && is_array($val['dat'])){
										$val['dat'] = serialize($val['dat']);
									}
									if(isset($oldArr['elements'][$key]['dat']) && is_array($oldArr['elements'][$key]['dat'])){
										$oldArr['elements'][$key]['dat'] = serialize($oldArr['elements'][$key]['dat']);
									}
								}

								$_diff = array_diff_assoc($val, $oldArr['elements'][$key]);
								if(!empty($_diff) && isset($_diff['dat'])){
									$diff['elements'][$key] = $_diff;
								}
							}
						}
					} elseif($k == 'documentCustomerFilter'){
						//TODO: imi: check if we need the information of serialized arrays instead of array to string = Array
						if(is_array($v) && isset($oldArr['documentCustomerFilter']) && is_array($oldArr['documentCustomerFilter'])){
							
							$_tmpArr1 = array();
							$_tmpArr2 = array();
							foreach($v as $_k => $_v){
								$_tmpArr1[$_k] = is_array($_v) ? serialize($_v) : $_v;
								//$vContent[$index] = is_array($value) ? 'Array' : $value;
							}
							foreach($oldArr['documentCustomerFilter'] as $_k => $_v){
								$_tmpArr2[$_k] = is_array($_v) ? serialize($_v) : $_v;
								//$oldArrContent[$index] = is_array($value) ? 'Array' : $value;
							}
							$_diff = array_diff_assoc($_tmpArr1, $_tmpArr2);
							if(!empty($_diff)){
								$diff['documentCustomerFilter'] = $_diff;
							}
						}
					}
				} else{

				}
			}
		}
		return $diff;
	}

	/**
	 * @abstract create file to preview dynamic documents
	 */
	function writePreviewDynFile($id, $siteFile, $tmpFile, $document){
		weFile::save($tmpFile, gzencode($this->getDocContent($document, $siteFile), 9));
	}

	function getDocContent($we_doc, $includepath = ""){

		$contents = "";
		set_time_limit(0);
		$requestBackup = $_REQUEST;
		$docBackup = $GLOBALS['we_doc'];

		$GLOBALS["getDocContentVersioning"] = true;

		$glob = "";
		foreach($GLOBALS as $k => $v){
			if((!preg_match('|^[0-9]|', $k)) && (!preg_match('|[^a-z0-9_]|i', $k)) && $k != "FROM_WE_SHOW_DOC" && $k != 'we_doc' && $k != "we_transaction" && $k != "GLOBALS" && $k != "HTTP_ENV_VARS" && $k != "HTTP_POST_VARS" && $k != "HTTP_GET_VARS" && $k != "HTTP_COOKIE_VARS" && $k != "HTTP_SERVER_VARS" && $k != "HTTP_POST_FILES" && $k != "HTTP_SESSION_VARS" && $k != "_GET" && $k != "_POST" && $k != "_REQUEST" && $k != "_SERVER" && $k != "_FILES" && $k != "_SESSION" && $k != "_ENV" && $k != "_COOKIE" && $k != "")
				$glob .= '$' . $k . ",";
		}
		$glob = rtrim($glob, ',');
		eval('global ' . $glob . ';');

		$isdyn = !isset($GLOBALS['WE_IS_DYN']) ? 'notSet' : $GLOBALS['WE_IS_DYN'];

		//usually the site file always exists
		if($includepath != '' && file_exists($includepath)){

			$_opt = getHttpOption();
			if($_opt != "none"){
				$f = $_SERVER['DOCUMENT_ROOT'] . VERSION_DIR . 'tmpSavedObj.txt';
				weFile::save($f, serialize($we_doc));

				$path = substr($we_doc->Path, 1);
				$location = SITE_DIR . $path;
				$contents = getHTTP(getServerUrl(true), $location . "?vers_we_obj=1");

				if(ini_get("short_open_tag") == 1){
					$contents = str_replace("<?xml", '<?php print "<?xml"; ?>', $contents);
				}

				weFile::delete($f);
			} else{
				ob_start();
				@include($includepath);
				ob_end_clean();

				$_REQUEST = $requestBackup;

				$glob = "";
				foreach($GLOBALS as $k => $v){
					if((!preg_match('|^[0-9]|', $k)) && (!preg_match('|[^a-z0-9_]|i', $k)) && $k != "FROM_WE_SHOW_DOC" && $k != 'we_doc' && $k != "we_transaction" && $k != "GLOBALS" && $k != "HTTP_ENV_VARS" && $k != "HTTP_POST_VARS" && $k != "HTTP_GET_VARS" && $k != "HTTP_COOKIE_VARS" && $k != "HTTP_SERVER_VARS" && $k != "HTTP_POST_FILES" && $k != "HTTP_SESSION_VARS" && $k != "_GET" && $k != "_POST" && $k != "_REQUEST" && $k != "_SERVER" && $k != "_FILES" && $k != "_SESSION" && $k != "_ENV" && $k != "_COOKIE" && $k != "")
						$glob .= '$' . $k . ",";
				}
				$glob = rtrim($glob, ',');
				eval('global ' . $glob . ';');


				ob_start();
				@include($includepath);
				$contents = ob_get_contents();
				ob_end_clean();
			}
		}else{
			ob_start();
			$noSess = true;
			$GLOBALS['WE_IS_DYN'] = 1;
			$we_transaction = '';
			$we_ContentType = $we_doc->ContentType;
			$_REQUEST['we_cmd'] = array();
			$_REQUEST['we_cmd'][1] = $we_doc->ID;
			$FROM_WE_SHOW_DOC = true;
			include(WE_INCLUDES_PATH . 'we_showDocument.inc.php');
			$contents = ob_get_contents();
			ob_end_clean();
		}

		$GLOBALS['we_doc'] = $docBackup;
		$_REQUEST = $requestBackup;

		if($isdyn == 'notSet'){
			if(isset($GLOBALS['WE_IS_DYN'])){
				unset($GLOBALS['WE_IS_DYN']);
			}
		} else{
			$GLOBALS['WE_IS_DYN'] = $isdyn;
		}

		unset($GLOBALS["getDocContentVersioning"]);

		return $contents;
	}

	/**
	 * @abstract save version-entry in DB which is marked as deleted
	 */
	function setVersionOnDelete($docID, $docTable, $ct, $db){

		if(isset($_SESSION["user"]["ID"])){
			$lastEntry = $this->getLastEntry($docID, $docTable);

			$lastEntry['timestamp'] = time();
			$lastEntry['status'] = "deleted";
			$lastEntry['version'] = (isset($lastEntry['version'])) ? $lastEntry['version'] + 1 : 1;
			$lastEntry['modifications'] = 1;
			$lastEntry['modifierID'] = $_SESSION["user"]["ID"];
			$lastEntry['IP'] = $_SERVER['REMOTE_ADDR'];
			$lastEntry['Browser'] = $_SERVER['HTTP_USER_AGENT'];
			$lastEntry['active'] = 1;
			$lastEntry['fromScheduler'] = $this->IsScheduler();

			$keys = array();
			$vals = array();
			$db = new DB_WE();

			foreach($lastEntry as $k => $v){
				if($k != "ID"){
					$keys[] = $db->escape($k);
					$vals[] = '"' . $db->escape($v) . '"';
				}
			}

			$doDelete = true;
			//preferences
			if(!$this->CheckPreferencesCtypes($ct)){
				$doDelete = false;
			}


			if(defined("VERSIONS_CREATE") && VERSIONS_CREATE){
				$doDelete = false;
			}


			if(!empty($keys) && !empty($vals) && $doDelete){
				$db->query('INSERT INTO ' . VERSIONS_TABLE . ' (' . implode(',', $keys) . ') VALUES(' . implode(',', $vals) . ')');

				$db->query("UPDATE " . VERSIONS_TABLE . " SET active = '0' WHERE documentID = " . intval($docID) . " AND documentTable = '" . $db->escape($docTable) . "' AND version != " . intval($lastEntry['version']));
			}

			$this->CheckPreferencesTime($docID, $docTable);
		}
	}

	/**
	 * @abstract delete version entry from db and delete version files
	 */
	function deleteVersion($ID = "", $where = ""){

		if(isset($_SESSION["user"]["ID"])){
			$db = new DB_WE();


			if($ID != ""){
				$w = "ID = " . intval($ID);
			} elseif($where != ""){
				$w = $where;
			}

			$query = "SELECT ID,documentID,version,Text,ContentType,documentTable,Path,binaryPath FROM " . VERSIONS_TABLE . " WHERE " . $w . " LIMIT 1";

			$db->query($query);
			$binaryPath = "";
			while($db->next_record()) {
				$binaryPath = $db->f('binaryPath');
				$_SESSION['weS']['versions']['logDeleteIds'][$db->f('ID')]['Text'] = $db->f('Text');
				$_SESSION['weS']['versions']['logDeleteIds'][$db->f('ID')]['ContentType'] = $db->f('ContentType');
				$_SESSION['weS']['versions']['logDeleteIds'][$db->f('ID')]['Path'] = $db->f('Path');
				$_SESSION['weS']['versions']['logDeleteIds'][$db->f('ID')]['Version'] = $db->f('version');
				$_SESSION['weS']['versions']['logDeleteIds'][$db->f('ID')]['documentID'] = $db->f('documentID');
			}

			$filePath = $_SERVER['DOCUMENT_ROOT'] . $binaryPath;
			$binaryPathUsed = f("SELECT binaryPath FROM " . VERSIONS_TABLE . " WHERE ID!=" . intval($ID) . " AND binaryPath='" . $db->escape($binaryPath) . "' LIMIT 1", "binaryPath", $db);

			if(file_exists($filePath) && $binaryPathUsed == ""){
				@unlink($filePath);
			}

			$query = "DELETE FROM " . VERSIONS_TABLE . " WHERE " . $w . " ;";

			$db->query($query);
		}
	}

	/**
	 * @abstract reset version
	 */
	function resetVersion($ID, $version, $publish){

		$db = new DB_WE();
		$db2 = new DB_WE();

		if(isset($_SESSION["user"]["ID"])){
			$resetArray = array();
			$tblFields = array();
			$tableInfo = $db->metadata(VERSIONS_TABLE);

			$we_transaction = (isset($_REQUEST["we_transaction"]) ?
					(preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0) :
					$GLOBALS["we_transaction"]);

			foreach($tableInfo as $cur){
				$tblFields[] = $cur["name"];
			}

			$db->query('SELECT * FROM ' . VERSIONS_TABLE . ' WHERE ID=' . intval($ID));

			if($db->next_record()){
				foreach($tblFields as $k => $v){
					$resetArray[$v] = $db->f("" . $v);
				}
			}

			if(is_array($resetArray) && !empty($resetArray)){
				$resetDoc = new $resetArray["ClassName"]();

				foreach($resetArray as $k => $v){

					if(isset($resetDoc->$k)){
						if($k != "ID"){
							$resetDoc->$k = $v;
						}
					} elseif($k == "documentID"){
						$resetDoc->ID = $v;
					} elseif($k == "documentElements"){
						if($v != ""){
							$docElements = unserialize((substr_compare($v, 'a%3A', 0, 4) == 0 ?
									html_entity_decode(urldecode($v), ENT_QUOTES) :
									gzuncompress($v))
							);
							$resetDoc->elements = $docElements;
						}
					} elseif($k == "documentScheduler"){
						if($v != ""){
							$docElements = unserialize((substr_compare($v, 'a%3A', 0, 4) == 0 ?
									html_entity_decode(urldecode($v), ENT_QUOTES) :
									gzuncompress($v))
							);
							$resetDoc->schedArr = $docElements;
						}
					} elseif($k == "documentCustomFilter"){
						if($v != ""){
							$docElements = unserialize((substr_compare($v, 'a%3A', 0, 4) == 0 ?
									html_entity_decode(urldecode($v), ENT_QUOTES) :
									gzuncompress($v))
							);
							$resetDoc->documentCustomerFilter = new weDocumentCustomerFilter();
							foreach($docElements as $k => $v){
								if(isset($resetDoc->documentCustomerFilter->$k)){
									if($v != "" || !empty($v)){
										$resetDoc->documentCustomerFilter->$k = $v;
									}
								}
							}
						}
					}
				}

				if($resetDoc->ContentType == "image/*"){
					$lastBinaryPath = f("SELECT binaryPath FROM " . VERSIONS_TABLE . " WHERE documentID='" . $resetArray["documentID"] . "' AND documentTable='" . $resetArray["documentTable"] . "' AND version <='" . $version . "' AND binaryPath !='' ORDER BY version DESC LIMIT 1", "binaryPath", $db);
					$resetDoc->elements["data"]["dat"] = $_SERVER['DOCUMENT_ROOT'] . $lastBinaryPath;
				}

				$resetDoc->EditPageNr = $_SESSION['weS']['EditPageNr'];

				$existsInFileTable = f("SELECT ID FROM " . $db->escape($resetArray["documentTable"]) . " WHERE ID=" . intval($resetDoc->ID), "ID", $db);
				//if document was deleted

				if(empty($existsInFileTable)){
					//save this id and contenttype to turn the id for the versions
					$oldId = $resetDoc->ID;
					$oldCt = $resetDoc->ContentType;
					$resetDoc->ID = 0;
					$lastEntryVersion = f("SELECT version FROM " . VERSIONS_TABLE . " WHERE documentID=" . intval($resetArray["documentID"]) . " AND documentTable='" . $db->escape($resetArray["documentTable"]) . "' ORDER BY version DESC LIMIT 1", "version", $db);
					$resetDoc->version = $lastEntryVersion + 1;
				}

				if($resetArray["ParentID"] != 0){
					//if folder was deleted
					$existsPath = f("SELECT Path FROM " . $db->escape($resetArray["documentTable"]) . " WHERE ID=" . intval($resetArray["ParentID"]) . " AND IsFolder='1' ", "Path", $db);

					if(empty($existsPath)){
						// create old folder if it does not exists

						$folders = explode("/", $resetArray["Path"]);
						foreach($folders as $k => $v){
							if($k != 0 && $k != (count($folders) - 1)){

								$parentID = (isset($_SESSION['weS']['versions']['lastPathID'])) ? $_SESSION['weS']['versions']['lastPathID'] : 0;
								if(defined("OBJECT_FILES_TABLE") && $resetArray["documentTable"] == OBJECT_FILES_TABLE){
									$folder = new we_class_folder();
								} else{
									$folder = new we_folder();
								}
								$folder->we_new();
								$folder->setParentID($parentID);
								$folder->Table = $resetArray["documentTable"];
								$folder->Text = $v;
								$folder->CreationDate = time();
								$folder->ModDate = time();
								$folder->Filename = $v;
								$folder->Published = time();
								$folder->Path = $folder->getPath();
								$folder->CreatorID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
								$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
								$existsFolderPathID = f("SELECT ID FROM " . $db->escape($resetArray["documentTable"]) . " WHERE Path='" . $db->escape($folder->Path) . "' AND IsFolder='1' ", "ID", $db);
								if(empty($existsFolderPathID)){
									$folder->we_save();
									$_SESSION['weS']['versions']['lastPathID'] = $folder->ID;
								} else{
									$_SESSION['weS']['versions']['lastPathID'] = $existsFolderPathID;
								}
							}
						}

						$resetDoc->ID = 0;
						$resetDoc->ParentID = $_SESSION['weS']['versions']['lastPathID'];
						$resetDoc->Path = $resetArray["Path"];
					}
				}

				$existsFile = f("SELECT COUNT(1) as Count FROM " . $db->escape($resetArray["documentTable"]) . " WHERE ID!= " . intval($resetArray["documentID"]) . " AND Path= '" . $db->escape($resetDoc->Path) . "' ", "Count", $db);

				$doPark = false;
				if(!empty($existsFile)){
					$resetDoc->Path = str_replace($resetDoc->Text, "_" . $resetArray["documentID"] . "_" . $resetDoc->Text, $resetDoc->Path);
					$resetDoc->Text = "_" . $resetArray["documentID"] . "_" . $resetDoc->Text;
					if(isset($resetDoc->Filename) && $resetDoc->Filename != ""){
						$resetDoc->Filename = "_" . $resetArray["documentID"] . "_" . $resetDoc->Filename;
						$publish = 0;
						$doPark = true;
					}
				}

				if((isset($_SESSION['weS']['versions']['lastPathID']))){
					unset($_SESSION['weS']['versions']['lastPathID']);
				}

				$resetDoc->resetFromVersion = $version;

				$resetDoc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);

				$GLOBALS['we_doc'] = $resetDoc;


				we_temporaryDocument::delete($resetDoc->ID, $resetDoc->Table);
				//$resetDoc->initByID($resetDoc->ID);
				$resetDoc->ModDate = time();
				$resetDoc->Published = $resetArray["timestamp"];

				$wasPublished = f("SELECT status FROM " . VERSIONS_TABLE . " WHERE documentID=" . intval($resetArray["documentID"]) . " AND documentTable= '" . $db->escape($resetArray["documentTable"]) . "' and status='published' ORDER BY version DESC LIMIT 1 ", "status", $db);
				$publishedDoc = $_SERVER['DOCUMENT_ROOT'] . $resetDoc->Path;
				$publishedDocExists = true;
				if($resetArray["ContentType"] != "objectFile"){
					$publishedDocExists = file_exists($publishedDoc);
				}
				if($doPark || $wasPublished == "" || !$publishedDocExists){
					$resetDoc->Published = 0;
				}
				if($publish){
					$_SESSION['weS']['versions']['doPublish'] = true;
				}
				$resetDoc->we_save();
				if($publish){
					unset($_SESSION['weS']['versions']['doPublish']);
					$resetDoc->we_publish();
				}

				if(defined("WORKFLOW_TABLE") && $resetDoc->ContentType == "text/webedition"){
					if(we_workflow_utility::inWorkflow($resetDoc->ID, $resetDoc->Table)){
						we_workflow_utility::removeDocFromWorkflow($resetDoc->ID, $resetDoc->Table, $_SESSION["user"]["ID"], "");
					}
				}

				$_SESSION['weS']['versions']['logResetIds'][$resetArray['ID']]['Text'] = $resetArray['Text'];
				$_SESSION['weS']['versions']['logResetIds'][$resetArray['ID']]['ContentType'] = $resetArray['ContentType'];
				$_SESSION['weS']['versions']['logResetIds'][$resetArray['ID']]['Path'] = $resetArray['Path'];
				$_SESSION['weS']['versions']['logResetIds'][$resetArray['ID']]['Version'] = $resetArray['version'];
				$_SESSION['weS']['versions']['logResetIds'][$resetArray['ID']]['documentID'] = $resetArray['documentID'];

				//update versions if id or path were changed
				if(empty($existsInFileTable)){
					$q = "UPDATE " . VERSIONS_TABLE . " SET documentID = " . intval($resetDoc->ID) . ",ParentID = " . intval($resetDoc->ParentID) . ",active = 0 WHERE documentID = " . intval($oldId) . " AND ContentType = '" . $db->escape($oldCt) . "'";
					$db->query($q);
				}
			}
		}
	}

	public static function showValue($k, $v, $table = ''){
		$val = self::_showValue($k, $v, $table);
		return ($val ? $val : '&nbsp;');
	}

	/**
	 * @abstract return the fieldvalue that has been changed
	 */
	private static function _showValue($k, $v, $table){

		$pathLength = 41;

		$db = new DB_WE();

		switch($k){
			case 'timestamp':
				return date("d.m.y - H:i:s", $v);
			case 'status':
				return g_l('versions', '[' . $v . ']');
			case 'ParentID':
				return id_to_path($v, $table);
			case 'modifierID':
			case 'CreatorID':
				return id_to_path($v, USER_TABLE);
			case 'MasterTemplateID':
			case 'TemplateID':
				return ($v == 0 ? '' : id_to_path($v, TEMPLATES_TABLE));
			case 'InGlossar':
			case 'IsDynamic':
			case 'IsSearchable':
				return ($v == 1) ? g_l('versions', '[activ]') : g_l('versions', '[notactiv]');
			case 'DocType':
				return f("SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = '" . $v . "'", "DocType", $db);
			case 'RestrictOwners':
				return ($v == 1) ? g_l('versions', '[activ]') : g_l('versions', '[notactiv]');
			case 'Language':
				return isset($GLOBALS['weFrontendLanguages'][$v]) ? $GLOBALS['weFrontendLanguages'][$v] : '';
			case 'WebUserID':
				return id_to_path($v, CUSTOMER_TABLE);
			case 'Workspaces':
				$fieldValueText = '';
				if($v != ''){
					$vals = makeArrayFromCSV($v);
					if(!empty($vals)){
						foreach($vals as $k){
							if($fieldValueText != ''){
								$fieldValueText .= "<br/>";
							}
							$fieldValueText .= shortenPathSpace(id_to_path($k, FILE_TABLE), $pathLength);
						}
					}
				}
				return $fieldValueText;
			case 'ExtraWorkspaces':
				$fieldValueText = '';
				if($v != ''){
					$vals = makeArrayFromCSV($v);
					if(!empty($vals)){
						foreach($vals as $k){
							if($fieldValueText != ""){
								$fieldValueText .= "<br/>";
							}
							$fieldValueText .= shortenPathSpace(id_to_path($k, FILE_TABLE), $pathLength);
						}
					}
				}
				return $fieldValueText;
			case 'ExtraWorkspacesSelected':
				$fieldValueText = '';
				if($v != ''){
					$vals = makeArrayFromCSV($v);
					if(!empty($vals)){
						foreach($vals as $k){
							if($fieldValueText != ""){
								$fieldValueText .= "<br/>";
							}
							$fieldValueText .= shortenPathSpace(id_to_path($k, FILE_TABLE), $pathLength);
						}
					}
				}
				return $fieldValueText;
			case 'Templates':
				$fieldValueText = '';
				if($v != ''){
					$vals = makeArrayFromCSV($v);
					if(!empty($vals)){
						foreach($vals as $k){
							if($fieldValueText != ""){
								$fieldValueText .= "<br/>";
							}
							$fieldValueText .= shortenPathSpace(id_to_path($k, FILE_TABLE), $pathLength);
						}
					}
				}
				return $fieldValueText;
			case 'ExtraTemplates':
				$fieldValueText = '';
				if($v != ''){
					$vals = makeArrayFromCSV($v);
					if(!empty($vals)){
						foreach($vals as $k){
							if($fieldValueText != ""){
								$fieldValueText .= "<br/>";
							}
							$fieldValueText .= shortenPathSpace(id_to_path($k, FILE_TABLE), $pathLength);
						}
					}
				}
				return $fieldValueText;
			case 'fromScheduler':
				return ($v == 1) ? g_l('versions', '[yes]') : g_l('versions', '[no]');
			case 'fromImport':
				return ($v == 1) ? g_l('versions', '[yes]') : g_l('versions', '[no]');
			case 'resetFromVersion':
				return ($v == 0) ? "-" : $v;
			case 'Category':
				$fieldValueText = "";
				$v = makeArrayFromCSV($v);
				if(!empty($v)){
					foreach($v as $key){
						if($fieldValueText != ""){
							$fieldValueText .= "<br/>";
						}
						$fieldValueText .= shortenPathSpace(id_to_path($key, CATEGORY_TABLE), $pathLength);
					}
				}
				return $fieldValueText;
			case 'Owners':
				$fieldValueText = "";
				$v = makeArrayFromCSV($v);
				if(!empty($v)){
					foreach($v as $key){
						if($fieldValueText != ""){
							$fieldValueText .= "<br/>";
						}
						$fieldValueText .= shortenPathSpace(id_to_path($key, USER_TABLE), $pathLength);
					}
				}
				return $fieldValueText;
			case 'OwnersReadOnly':
				$fieldValueText = "";
				if($v != '' && !is_array($v)){
					$v = unserialize($v);
				}
				if(is_array($v) && !empty($v)){
					foreach($v as $key => $val){
						if($fieldValueText != ""){
							$fieldValueText .= "<br/>";
						}
						$stat = ($val == 1) ? g_l('versions', '[activ]') : g_l('versions', '[notactiv]');
						$fieldValueText .= shortenPathSpace(id_to_path($key, USER_TABLE), $pathLength) . ": " . $stat;
					}
				}
				return $fieldValueText;
			case 'weInternVariantElement':
				$fieldValueText = "";
				if($v != '' && !is_array($v)){
					$v = unserialize($v);
				}
				if(is_array($v) && !empty($v)){
					foreach($v as $key => $val){
						if(is_array($val)){
							foreach($val as $k => $vl){
								if($k != ""){
									$fieldValueText .= "<strong>" . $k . "</strong><br/>";
								}
								if(is_array($val)){
									foreach($vl as $key3 => $val3){
										if($key3 != ""){
											$fieldValueText .= $key3 . ": ";
										}
										if(isset($val3['dat']) && $val3['dat'] != ""){
											$fieldValueText .= $val3['dat'] . "<br/>";
										}
									}
								}
							}
						}
					}
				}
				return $fieldValueText . "<br/>";
			//Scheduler
			case 'task':
				return ($v != '' ? g_l('versions', '[' . $k . '_' . $v . ']') : '');
			case 'type':
				return g_l('versions', '[type_' . $v . ']');
			case 'active':
				return ($v == 1) ? g_l('versions', '[yes]') : g_l('versions', '[no]');
			case 'months':
				$months = array();
				if(is_array($v) && !empty($v)){
					foreach($v as $k => $v){
						if($v == 1){
							$months[] = g_l('date', '[month][short][' . $k . ']');
						}
					}
				}
				return makeCSVFromArray($months, false, ", ");
			case 'days':
				$days = array();
				if(is_array($v) && !empty($v)){
					foreach($v as $k => $v){
						if($v == 1){
							$day = $k + 1;
							if(strlen($day) == 1){
								$day = "0" . $day;
							}
							$days[] = $day;
						}
					}
				}
				return makeCSVFromArray($days, false, ", ");
			case 'weekdays':
				$weekdays = array();
				if(is_array($v) && !empty($v)){
					foreach($v as $k => $v){
						if($v == 1){
							$weekdays[] = g_l('date', '[day][short][' . $k . ']');
						}
					}
				}

				return makeCSVFromArray($weekdays, false, ", ");
			case 'time':
				return date("d.m.y - H:i:s", $v);
			case 'doctypeAll':
				return ($v == 1) ? g_l('versions', '[yes]') : '';
			case 'DoctypeID':
				return f("SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID = " . intval($v), "DocType", $db);
			case 'CategoryIDs':
				$fieldValueText = "";
				$v = makeArrayFromCSV($v);
				if(!empty($v)){
					foreach($v as $key){
						if($fieldValueText != ""){
							$fieldValueText .= "<br/>";
						}
						$fieldValueText .= shortenPathSpace(id_to_path($key, CATEGORY_TABLE), $pathLength);
					}
				}
				return $fieldValueText;
			//Customer Filter
			case '_id':
				return ($v == "") ? 0 : $v;
			case '_accessControlOnTemplate':
				return ($v == 1) ? g_l('versions', '[yes]') : g_l('versions', '[no]');
			case '_errorDocNoLogin':
				return shortenPathSpace(id_to_path($v, FILE_TABLE), $pathLength);
			case '_errorDocNoAccess':
				return shortenPathSpace(id_to_path($v, FILE_TABLE), $pathLength);
			case '_mode':
				switch($v){
					case 0:
						return g_l('modules_customerFilter', '[mode_off]');
					case 1:
						return g_l('modules_customerFilter', '[mode_all]');
					case 2:
						return g_l('modules_customerFilter', '[mode_specific]');
					case 3:
						return g_l('modules_customerFilter', '[mode_filter]');
					default:
						return '';
				}
			case '_specificCustomers':
				$fieldValueText = "";
				if(is_array($v) && !empty($v)){
					foreach($v as $key){
						if($fieldValueText != ""){
							$fieldValueText .= "<br/>";
						}
						$fieldValueText .= shortenPathSpace(id_to_path($key, CUSTOMER_TABLE), $pathLength);
					}
				}
				return $fieldValueText;
			case '_blackList':
				$fieldValueText = "";
				if(is_array($v) && !empty($v)){
					foreach($v as $key){
						if($fieldValueText != ""){
							$fieldValueText .= "<br/>";
						}
						$fieldValueText .= shortenPathSpace(id_to_path($key, CUSTOMER_TABLE), $pathLength);
					}
				}
				return $fieldValueText;
			case '_whiteList':
				$fieldValueText = "";
				if(is_array($v) && !empty($v)){
					foreach($v as $key){
						if($fieldValueText != ""){
							$fieldValueText .= "<br/>";
						}
						$fieldValueText .= shortenPathSpace(id_to_path($key, CUSTOMER_TABLE), $pathLength);
					}
				}
				return $fieldValueText;
			case '_filter':
				$fieldValueText = "";
				if(is_array($v) && !empty($v)){
					foreach($v as $key => $val){
						$fieldValueText .= $key . ":<br/>";
						if(is_array($val) && !empty($val)){
							foreach($val as $key2 => $val2){
								$fieldValueText .= $key2 . ":" . $val2 . "<br/>";
							}
						}
					}
				}
				return $fieldValueText;
			default:
				return $v;
		}
	}

	/**
	 * @abstract get array of fieldnames from $table
	 * @return array of fieldnames
	 */
	function getFieldsFromTable($table){

		$fieldNames = array();

		$db = new DB_WE();

		$tableInfo = $db->metadata($table);
		foreach($tableInfo as $cur){
			$fieldNames[] = $cur["name"];
		}

		return $fieldNames;
	}

	/**
	 * @abstract convert object to array
	 * @return array
	 */
	function objectToArray($obj){

		$arr = array();
		$_arr = is_object($obj) ? get_object_vars($obj) : $obj;

		foreach($_arr as $key => $val){
			$val = (is_array($val) || is_object($val)) ? $this->objectToArray($val) : $val;
			$arr[$key] = $val;
		}

		return $arr;
	}

	/**
	 * @abstract get last record of $docID which was saved or published
	 * @return array with fields and values
	 */
	function getLastEntry($docID, $docTable){

		$modArray = array();
		$db = new DB_WE();
		$tblFields = $this->getFieldsFromTable(VERSIONS_TABLE);

		$db->query("SELECT * FROM " . VERSIONS_TABLE . " WHERE documentID=" . intval($docID) . " AND documentTable='" . $db->escape($docTable) . "' AND status IN ('saved','published','unpublished','deleted') ORDER BY version DESC LIMIT 1");
		if($db->next_record()){
			foreach($tblFields as $k => $v){
				$modArray[$v] = $db->f("" . $v);
			}
		}

		return $modArray;
	}

	/**
	 * @abstract get values of modifications for DB-entry
	 * @return array with fields and values
	 */
	function getConstantsOfMod($modArray){

		$const = array();

		foreach($modArray as $k => $v){
			if(isset($this->modFields[$v])){
				$const[] = $this->modFields[$v];
			}
		}

		return makeCSVFromArray($const);
	}

}

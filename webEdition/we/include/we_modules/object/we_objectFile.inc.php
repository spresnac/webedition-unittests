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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_objectFile extends we_document{

	var $TableID = 0;
	var $ObjectID = 0;
	var $rootDirID = 0;
	var $RootDirPath = '/';
	var $Workspaces = '';
	var $ExtraWorkspaces = '';
	var $ExtraWorkspacesSelected = '';
	var $AllowedWorkspaces = array();
	var $AllowedClasses = '';
	var $CSS = '';
	var $Charset = '';
	var $Language = '';
	var $Templates = '';
	var $ExtraTemplates = '';
	var $DefArray = array();
	var $documentCustomerFilter = ''; // DON'T SET TO NULL !!!!
	var $Url = '';
	var $TriggerID = 0;

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->Icon = 'objectFile.gif';
		$this->Table = OBJECT_FILES_TABLE;
		$this->ContentType = 'objectFile';
		$this->PublWhenSave = 0;
		$this->IsTextContentDoc = true;
		array_push($this->persistent_slots, 'CSS', 'DefArray', 'Text', 'AllowedClasses', 'Templates', 'ExtraTemplates', 'Workspaces', 'ExtraWorkspaces', 'ExtraWorkspacesSelected', 'RootDirPath', 'rootDirID', 'TableID', 'ObjectID', 'Category', 'IsSearchable', 'Charset', 'Language', 'Url', 'TriggerID');
		if(defined('SCHEDULE_TABLE')){
			array_push($this->persistent_slots, 'FromOk', 'ToOk', 'From', 'To');
		}
		if(!isset($GLOBALS['WE_IS_DYN'])){
			$ac = $this->getAllowedClasses();
			$this->AllowedClasses = makeCSVFromArray($ac);
		}
		if(defined('CUSTOMER_TABLE')){
			$this->EditPageNrs[] = WE_EDITPAGE_WEBUSER;
		}
		array_push($this->EditPageNrs, WE_EDITPAGE_PROPERTIES, WE_EDITPAGE_INFO, WE_EDITPAGE_CONTENT, WE_EDITPAGE_WORKSPACE, WE_EDITPAGE_PREVIEW, WE_EDITPAGE_VARIANTS, WE_EDITPAGE_VERSIONS, WE_EDITPAGE_SCHEDULER);
	}

	public static function initObject($classID, $formname = 'we_global_form', $categories = '', $parentid = 0){
		$session = isset($GLOBALS['WE_SESSION_START']) && $GLOBALS['WE_SESSION_START'];

		if(!(isset($GLOBALS['we_object']) && is_array($GLOBALS['we_object']))){
			$GLOBALS['we_object'] = array();
		}
		$GLOBALS['we_object'][$formname] = new we_objectFile();
		if((!$session) || (!isset($_SESSION['we_object_session_' . $formname]))){
			if($session){
				$_SESSION['we_object_session_' . $formname] = array();
			}
			$GLOBALS['we_object'][$formname]->we_new();
			if(isset($_REQUEST['we_editObject_ID']) && $_REQUEST['we_editObject_ID']){
				$GLOBALS['we_object'][$formname]->initByID(intval($_REQUEST['we_editObject_ID']), OBJECT_FILES_TABLE);
			} else{
				$GLOBALS['we_object'][$formname]->TableID = $classID;
				$GLOBALS['we_object'][$formname]->setRootDirID(true);
				$GLOBALS['we_object'][$formname]->resetParentID();
				$GLOBALS['we_object'][$formname]->restoreDefaults();
				if(strlen($categories)){
					$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
					$GLOBALS['we_object'][$formname]->Category = $categories;
				}
			}

			// save parentid
			if($parentid){
				// check if parentid is in correct folder ...
				$parentfolder = new we_class_folder();
				$parentfolder->initByID($parentid, OBJECT_FILES_TABLE);

				if(($GLOBALS['we_object'][$formname]->ParentPath == $parentfolder->Path) ||
					strpos($parentfolder->Path . '/', $GLOBALS['we_object'][$formname]->ParentPath) === 0){
					$GLOBALS['we_object'][$formname]->ParentID = $parentfolder->ID;
					$GLOBALS['we_object'][$formname]->Path = $parentfolder->Path . '/' . $GLOBALS['we_object'][$formname]->Filename;
				}
			}

			if($session){
				$GLOBALS['we_object'][$formname]->saveInSession($_SESSION['we_object_session_' . $formname]);
			}
		} else{
			if(isset($_REQUEST['we_editObject_ID']) && $_REQUEST['we_editObject_ID']){
				$GLOBALS['we_object'][$formname]->initByID(intval($_REQUEST['we_editObject_ID']), OBJECT_FILES_TABLE);
			} else{
				if($session){
					$GLOBALS['we_object'][$formname]->we_initSessDat($_SESSION['we_object_session_' . $formname]);
				}
			}
			if($classID && ($GLOBALS['we_object'][$formname]->TableID != $classID)){
				$GLOBALS['we_object'][$formname]->TableID = $classID;
			}
			if(strlen($categories)){
				$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
				$GLOBALS['we_object'][$formname]->Category = $categories;
			}
		}

		$GLOBALS['we_object'][$formname]->DefArray = empty($GLOBALS['we_object'][$formname]->DefArray) ? $GLOBALS['we_object'][$formname]->getDefaultValueArray() :
			$GLOBALS['we_object'][$formname]->DefArray; //bug #7426

		if(isset($_REQUEST['we_returnpage'])){
			$GLOBALS['we_object'][$formname]->setElement('we_returnpage', $_REQUEST['we_returnpage']);
		}

		if(isset($_REQUEST['we_ui_' . $formname]) && is_array($_REQUEST['we_ui_' . $formname])){
			$dates = array();

			foreach($_REQUEST['we_ui_' . $formname] as $n => $v){
				if(preg_match('/^we_date_([a-zA-Z0-9_]+)_(day|month|year|minute|hour)$/', $n, $regs)){
					$dates[$regs[1]][$regs[2]] = $v;
				} else{
					$v = we_util::rmPhp($v);
					$GLOBALS['we_object'][$formname]->i_convertElemFromRequest('', $v, $n);
					$GLOBALS['we_object'][$formname]->setElement($n, $v);
				}
			}

			foreach($dates as $k => $v){
				$GLOBALS['we_object'][$formname]->setElement(
					$k, mktime(
						intval($dates[$k]['hour']), intval($dates[$k]['minute']), 0, intval($dates[$k]['month']), intval($dates[$k]['day']), intval($dates[$k]['year'])));
			}
		}
		if(isset($_REQUEST['we_ui_' . $formname . '_categories'])){
			$cats = $_REQUEST['we_ui_' . $formname . '_categories'];
			// Bug Fix #750
			if(is_array($cats)){
				$cats = implode(',', $cats);
			}
			$cats = makeIDsFromPathCVS($cats, CATEGORY_TABLE);
			$GLOBALS['we_object'][$formname]->Category = $cats;
		}
		if(isset($_REQUEST['we_ui_' . $formname . '_Category'])){
			$_REQUEST['we_ui_' . $formname . '_Category'] = (is_array($_REQUEST['we_ui_' . $formname . '_Category']) ?
					makeCSVFromArray($_REQUEST['we_ui_' . $formname . '_Category'], true) :
					makeCSVFromArray(makeArrayFromCSV($_REQUEST['we_ui_' . $formname . '_Category']), true));
		}
		foreach($GLOBALS['we_object'][$formname]->persistent_slots as $slotname){
			if($slotname != 'categories' && isset($_REQUEST['we_ui_' . $formname . '_' . $slotname])){
				$v = we_util::rmPhp($_REQUEST['we_ui_' . $formname . '_' . $slotname]);
				$GLOBALS['we_object'][$formname]->i_convertElemFromRequest('', $v, $slotname);
				$GLOBALS['we_object'][$formname]->{$slotname} = $v;
			}
		}

		we_imageDocument::checkAndPrepare($formname, 'we_object');
		we_flashDocument::checkAndPrepare($formname, 'we_object');
		we_quicktimeDocument::checkAndPrepare($formname, 'we_object');
		we_otherDocument::checkAndPrepare($formname, 'we_object');

		if($session){
			$GLOBALS['we_object'][$formname]->saveInSession($_SESSION['we_object_session_' . $formname]);
		}
		return $GLOBALS['we_object'][$formname];
	}

	function makeSameNew(){
		$Category = $this->Category;
		$TableID = $this->TableID;
		$rootDirID = $this->rootDirID;
		$RootDirPath = $this->RootDirPath;
		$Workspaces = $this->Workspaces;
		$ExtraWorkspaces = $this->ExtraWorkspaces;
		$ExtraWorkspacesSelected = $this->ExtraWorkspacesSelected;
		$IsSearchable = $this->IsSearchable;
		$Charset = $this->Charset;
		$Url = $this->Url;
		$TriggerID = $this->TriggerID;
		we_root::makeSameNew();
		$this->Category = $Category;
		$this->TableID = $TableID;
		$this->rootDirID = $rootDirID;
		$this->RootDirPath = $RootDirPath;
		$this->DefaultInit = false;

		$this->i_objectFileInit(true);

		$this->Url = $Url;
		$this->TriggerID = $TriggerID;
		$this->Workspaces = $Workspaces;
		$this->ExtraWorkspaces = $ExtraWorkspaces;
		$this->ExtraWorkspacesSelected = $ExtraWorkspacesSelected;
		$this->IsSearchable = $IsSearchable;
		$this->Charset = $Charset;
	}

	function we_rewrite(){
		$this->setLanguage();
		$this->setUrl();
		if(!$this->DB_WE->query('UPDATE ' . $this->Table . ' SET Url="' . $this->DB_WE->escape($this->Url) . '" WHERE ID=' . intval($this->ID)) ||
			!$this->DB_WE->query('UPDATE ' . OBJECT_X_TABLE . $this->TableID . ' SET OF_Url="' . $this->DB_WE->escape($this->Url) . '" WHERE OF_ID=' . intval($this->ID))){
			return false;
		}

		return parent::we_rewrite();
	}

	private static function getObjectRootPathOfObjectWorkspace($classDir, $classId, $db = ''){
		$db = ($db ? $db : new DB_WE());
		$classDir = rtrim($classDir, '/');
		$rootId = $classId;
		$cnt = 1;
		$all = array();
		if(defined('OBJECT_TABLE')){
			$slash = PHP_INT_MAX;
			$ws = get_ws(OBJECT_FILES_TABLE);
			if(intval($ws) == 0){
				$ws = 0;
			}
			$db->query('SELECT ID,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=1 AND (Path="' . $db->escape($classDir) . '" OR Path LIKE "' . $db->escape($classDir) . '/%")');
			while($db->next_record()) {
				$all[$db->f('Path')] = $db->f('ID');
				if((($tmp = substr_count($db->f('Path'), '/')) <= $slash) && (!$ws || in_workspace($db->f('ID'), $ws, OBJECT_FILES_TABLE, '', true))){
					$rootId = $db->f('ID');
					$cnt = ($tmp == $slash ? $cnt : 0) + 1;
					if($cnt == 1){
						$path = substr($db->f('Path'), 0, strrpos($db->f('Path'), '/'));
					}
					$slash = $tmp;
				}
			}
		}
		return ($cnt == 1 ? $rootId : $all[$path]);
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$rootDirId = self::getObjectRootPathOfObjectWorkspace($this->RootDirPath, $this->rootDirID);
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('copyDocument',currentID);");
		$but = we_button::create_button('select', "javascript:we_cmd('openDocselector',document.forms[0].elements['$idname'].value,'" . $this->Table . "','" . $wecmdenc2 . "','','" . $wecmdenc3 . "','" . session_id() . "','" . $rootDirId . "','" . $this->ContentType . "');");
		return $this->htmlHidden($idname, $this->CopyID) . $but;
	}

	function formLanguage(){
		we_loadLanguageConfig();

		$value = (isset($this->Language) ? $this->Language : $GLOBALS['weDefaultFrontendLanguage']);

		$inputName = 'we_' . $this->Name . '_Language';

		$_languages = getWeFrontendLanguagesForBackend();
		$this->setRootDirID(true);

		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			foreach($_languages as $langkey => $lang){
				$LDID = intval(f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblObjectFile" AND DID=' . (int) $this->ID . ' AND Locale="' . $langkey . '"', 'LDID', $this->DB_WE));
				$divname = 'we_' . $this->Name . '_LanguageDocDiv[' . $langkey . ']';
				$htmlzw.= '<div id="' . $divname . '" ' . ($this->Language == $langkey ? ' style="display:none" ' : '') . '>' . $this->formLanguageDocument($lang, $langkey, $LDID, $this->Table, $this->rootDirID) . '</div>';
				$langkeys[] = $langkey;
			}

			return '<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
				<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, " onblur=\"_EditorFrame.setEditorIsHot(true);\" onchange=\"dieWerte='" . implode(',', $langkeys) . "';showhideLangLink('we_" . $this->Name . "_LanguageDocDiv',dieWerte,this.options[this.selectedIndex].value);_EditorFrame.setEditorIsHot(true);\"", "value", 508) . '</td></tr>
				<tr><td>' . we_html_tools::getPixel(2, 20) . '</td></tr>
				<tr><td class="defaultfont" align="left">' . g_l('weClass', '[languageLinks]') . '</td></tr>
			</table>' .
				we_html_element::htmlBr() . $htmlzw;
		} else{
			return '<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
				<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, " onblur=\"_EditorFrame.setEditorIsHot(true);\" onchange=\"_EditorFrame.setEditorIsHot(true);\"", "value", 508) . '</td></tr>
			</table>';
		}
	}

	function copyDoc($id){
		if(!$id){
			return;
		}

		$doc = new we_objectFile();
		$doc->InitByID($id, $this->Table, we_class::LOAD_TEMP_DB);
		$doc->setRootDirID(true);
		if($this->ID == 0){
			foreach($this->persistent_slots as $pers){
				$this->{$pers} = isset($doc->{$pers}) ? $doc->{$pers} : '';
			}
			$this->ObjectID = 0;
			$this->CreationDate = time();
			$this->CreatorID = $_SESSION['user']['ID'];
			$this->DefaultInit = true;
			$this->rootDirID = $doc->rootDirID;
			$this->RootDirPath = $doc->RootDirPath;
			$this->ID = 0;
			$this->OldPath = '';
			$this->Published = 0;
			$this->Text .= '_copy';
			$this->Path = $this->ParentPath . $this->Text;
			$this->OldPath = $this->Path;
		}
		$this->elements = $doc->elements;
		foreach(array_keys($this->elements) as $n){
			$this->elements[$n]['cid'] = 0;
		}
		$this->EditPageNr = 0;
		$this->Category = $doc->Category;
		$this->documentCustomerFilter = $doc->documentCustomerFilter;
	}

	function restoreWorkspaces(){
		if(!$this->TableID){ // WORKARROUND for bug 4631
			$ac = makeCSVFromArray(getAllowedClasses($this->DB_WE));
			$this->TableID = count($ac) ? $ac[0] : 0;
		}
		$ws = get_ws();
		$foo = getHash('SELECT Workspaces,DefaultWorkspaces,Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . (int) $this->TableID, $this->DB_WE);
		$def_ws = isset($foo['DefaultWorkspaces']) ? $foo['DefaultWorkspaces'] : '';
		$owsCSV = isset($foo['Workspaces']) ? $foo['Workspaces'] : '';
		$otmplsCSV = isset($foo['Templates']) ? $foo['Templates'] : '';
		$owsCSVArray = makeArrayFromCSV($owsCSV);
		$defwsCSVArray = makeArrayFromCSV($def_ws);
		$otmplsCSVArray = makeArrayFromCSV($otmplsCSV);
		$this->Workspaces = '';
		$this->Templates = '';
		$this->ExtraWorkspaces = '';
		$this->ExtraTemplates = '';
		$processedWs = array();

// loop throgh all default workspaces
		foreach($defwsCSVArray as $_defWs){
// loop through each object workspace
			foreach($owsCSVArray as $i => $ows){
				if((!in_array($_defWs, $processedWs)) && in_workspace($_defWs, $ows, FILE_TABLE, $this->DB_WE)){ // if default workspace is within object workspace
					$processedWs = array($_defWs);
					$this->Workspaces .= $_defWs . ',';
					$this->Templates .= $otmplsCSVArray[$i] . ',';
				}
			}
		}
		unset($processedWs);

		if($this->Workspaces){
			$this->Workspaces = ',' . $this->Workspaces;
		}
		if($this->Templates){
			$this->Templates = ',' . $this->Templates;
		}
	}

	function setRootDirID($doit = false){
		if($this->InWebEdition || $doit){
			list($this->RootDirPath, $this->rootDirID) = getHash('SELECT o.Path,of.ID FROM ' . OBJECT_FILES_TABLE . ' of, ' . OBJECT_TABLE . ' o WHERE o.Path=of.Path AND o.ID=' . intval($this->TableID), $this->DB_WE);
		}
	}

	function resetParentID(){
		$len = strlen($this->RootDirPath . '/');
		if($this->ParentPath == '/' || (substr($this->ParentPath . '/', 0, $len) != substr($this->RootDirPath . '/', 0, $len))){
			$this->setParentID($this->rootDirID);
		}
// adjust to bug #376 regarding workspace
		$workspaceRootDirId = self::getObjectRootPathOfObjectWorkspace($this->RootDirPath, $this->rootDirID, $this->DB_WE);
		$this->ParentPath = id_to_path($workspaceRootDirId, OBJECT_FILES_TABLE, $this->DB_WE);
		$this->ParentID = $workspaceRootDirId;
	}

	function restoreDefaults($makeSameNewFlag = false){
		$this->DefaultInit = true;
		if(!$makeSameNewFlag){
			$this->resetParentID();
		}
		$this->Owners = '';
		$this->OwnersReadOnly = '';
		$this->RestrictOwners = '';
		$this->Category = '';
		$this->Text = '';
		$this->IsSearchable = 1;
		$this->Charset = '';
		$this->restoreWorkspaces();
		$this->elements = array();
		$hash = getHash('SELECT Users,UsersReadOnly,RestrictUsers,DefaultCategory,DefaultText,DefaultValues,DefaultTriggerID FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);
		if(count($hash)){
// fix - the class access permissions should not be applied
			/* if($this->DB_WE->f("Users")){
			  $this->Owners = $this->DB_WE->f("Users");
			  }
			  if($this->DB_WE->f("UsersReadOnly")){
			  $this->OwnersReadOnly = $this->DB_WE->f("UsersReadOnly");
			  }
			  if($this->DB_WE->f("RestrictUsers")){
			  $this->RestrictOwners = $this->DB_WE->f("RestrictUsers");
			  }

			  if($this->DB_WE->f('DefaultTriggerID')){
			  $this->TriggerID = $this->DB_WE->f('DefaultTriggerID');
			  }
			 */
			$this->Category = $hash['DefaultCategory'] ? $hash['DefaultCategory'] : '';
			if($hash['DefaultText']){
				$text = $hash['DefaultText'];
				$regs = array();
				if(preg_match('/%unique([^%]*)%/', $text, $regs)){
					$anz = ($regs[1] ? abs($regs[1]) : 16);
					$unique = substr(md5(uniqid(__FUNCTION__, true)), 0, min($anz, 32));
					$text = preg_replace('/%unique[^%]*%/', $unique, $text);
				}
				if(strpos($text, '%ID%') !== false){
//FIXME: this is NOT safe!!! Insert entry, and update afterwards
					$id = 1 + intval(f('SELECT max(ID) as ID FROM ' . OBJECT_FILES_TABLE, 'ID', $this->DB_WE));
					$text = str_replace('%ID%', '' . $id, $text);
				}
				$text = str_replace(array('%d%', '%j%', '%m%', '%y%', '%Y%', '%n%', '%h%', '%H%', '%g%', '%G%',), array(date('d'), date('j'), date('m'), date('y'), date('Y'), date('n'), date('h'), date('H'), date('g'), date('G'),), $text);
				$this->Text = $text;
			}

			if($hash['DefaultValues']){
				$vals = unserialize($hash['DefaultValues']);
				if(isset($vals['WE_CSS_FOR_CLASS'])){
					$this->CSS = $vals['WE_CSS_FOR_CLASS'];
				}
				if(isset($vals['elements']) && isset($vals['elements']['Charset']) && isset($vals['elements']['Charset']['dat'])){
					$this->Charset = $vals['elements']['Charset']['dat'];
				}
				if(is_array($vals)){
					foreach($vals as $name => $field){
						if(is_array($field)){
							$foo = explode('_', $name);
							$type = $foo[0];
							unset($foo[0]);
							$name = implode('_', $foo);
							$n = ($type == 'object' ? 'we_object_' . $name : (isset($name) ? $name : ''));
							$this->setElement($n, isset($field['default']) ? $field['default'] : '', $type, 0, (isset($field['autobr']) && $field['autobr'] == 'on') ? 'on' : 'off');
							if($type == 'multiobject'){
								$temp = array(
									'class' => $field['class'],
									'max' => $field['max'],
									'objects' => array(),
								);
								if(is_array($field['meta'])){
									foreach($field['meta'] as $val){
										$temp['objects'][] = $val;
									}
								}
								$this->setElement($name, serialize($temp));
							}
						}
					}
				}
			}
		}
		$this->setTypeAndLength();
	}

	function i_check_requiredFields(){
		foreach($this->DefArray as $n => $v){
			if(is_array($v) && isset($v['required']) && $v['required']){
				list($type, $name) = explode('_', $n, 2);
				switch($type){
					case 'object':
						$val = $this->getElement('we_object_' . $name);
						break;
					case 'multiobject':
						$temp = @unserialize($this->getElement($name));
						$_array = isset($temp['objects']) ? $temp['objects'] : array();
						if(count($_array) === 0){
							$val = 0;
						} else{
							$_empty = true;
							foreach($_array as $tmp){
								if($tmp){
									$_empty = false;
									break;
								}
							}
							$val = ($_empty ? 0 : 1);
						}
						break;
					case 'checkbox':
						$val = $this->getElement($name);
						break;
					case 'meta':
						$val = $this->getElement($name);
						break;
					default:
						$val = $this->geFieldValue($name, $type);
				}
				if((strlen($val) == 0) || (($type == 'object' || $type == 'multiobject' || $type == 'checkbox' || $type == 'img') && ($val == '0'))){
					if($type == 'object'){
						$name = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . (int) $name, 'Text', $this->DB_WE);
					}
					return $name;
				}
			}
		}
		return '';
	}

	function i_areVariantNamesValid(){
		if(!defined('SHOP_TABLE')){
			return true;
		}
		$variationFields = weShopVariants::getAllVariationFields($this);

		if(count($variationFields)){
			$i = 0;
			while(isset($this->elements[WE_SHOP_VARIANTS_PREFIX . $i])) {
				if(!trim($this->elements[WE_SHOP_VARIANTS_PREFIX . $i++]['dat'])){
					return false;
				}
			}
		}

		return true;
	}

	function getPath(){
		$ParentPath = $this->getParentPath();
		return $ParentPath . ($ParentPath != '/' ? '/' : '') . $this->Text;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
			case WE_EDITPAGE_WORKSPACE:
				return 'we_templates/we_editor_properties.inc.php';
			case WE_EDITPAGE_INFO:
				return 'we_modules/object/we_editor_info_objectFile.inc.php';
			case WE_EDITPAGE_CONTENT:
				return 'we_modules/object/we_editor_contentobjectFile.inc.php';
			case WE_EDITPAGE_PREVIEW:
				return 'we_modules/object/we_object_showDocument.inc.php';
			case WE_EDITPAGE_SCHEDULER:
				return 'we_modules/schedule/we_editor_schedpro.inc.php';
			case WE_EDITPAGE_VARIANTS:
				return 'we_templates/we_editor_variants.inc.php';
			case WE_EDITPAGE_WEBUSER:
				return 'we_modules/customer/editor_weDocumentCustomerFilter.inc.php';
			case WE_EDITPAGE_VERSIONS:
				return 'we_versions/we_editor_versions.inc.php';
			default:
				$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PROPERTIES;
				return 'we_templates/we_editor_properties.inc.php';
		}
	}

	function publishFromInsideDocument(){
		$this->publish();
		if($this->EditPageNr == WE_EDITPAGE_PROPERTIES || $this->EditPageNr == WE_EDITPAGE_INFO){
			$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page",' . $this->EditPageNr . ',"' . $GLOBALS["we_transaction"] . '");';
		}
		$GLOBALS['we_JavaScript'] = "_EditorFrame.setEditorDocumentId(" . $this->ID . ");" . $this->getUpdateTreeScript();
	}

	function unpublishFromInsideDocument(){
		$this->unpublish();
		if($this->EditPageNr == WE_EDITPAGE_PROPERTIES || $this->EditPageNr == WE_EDITPAGE_INFO){
			$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page",' . $this->EditPageNr . ',"' . $GLOBALS["we_transaction"] . '");';
		}
		$GLOBALS["we_JavaScript"] = "_EditorFrame.setEditorDocumentId(" . $this->ID . ");" . $this->getUpdateTreeScript();
	}

	function formPath(){
		$rootDirId = self::getObjectRootPathOfObjectWorkspace($this->RootDirPath, $this->rootDirID);
		if($this->ParentID == ""){
			$this->ParentID = $rootDirId;
			$this->ParentPath = id_to_path($rootDirId, OBJECT_FILES_TABLE);
		}
		$this->setUrl();
		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>' . $this->formInputField("", "Text", g_l('modules_object', '[objectname]'), 30, 388, 255, 'onChange="_EditorFrame.setEditorIsHot(true);pathOfDocumentChanged();"') . '</td><td></td><td></td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3">' . $this->formDirChooser(388, $rootDirId) . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr><td>' . $this->formIsSearchable() . '</td><td class="defaultfont">&nbsp;</td><td>&nbsp;</td></tr>
			</table></td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr><td class="defaultfont">' . g_l('modules_object', '[seourl]') . ':</td><td class="defaultfont">&nbsp;</td><td class="defaultfont">&nbsp;' . $this->Url . '</td></tr>
			</table></td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 4) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3">' . $this->formTriggerDocument() . '</td></tr>
</table>';
	}

	function formIsSearchable(){
		return we_forms::checkboxWithHidden($this->IsSearchable, 'we_' . $this->Name . '_IsSearchable', g_l('weClass', '[IsSearchable]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
	}

	/**
	 * returns	a select menu within a html table. to ATTENTION this function is also used in classes object and objectFile !!!!
	 * 			when $withHeadline is true, a table with headline is returned, default is false
	 * @return	select menue to determine charset
	 * @param	boolean
	 */
	function formCharset($withHeadline = false){
		$_charsetHandler = new charsetHandler();

		$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
		$_charsets[''] = '';
		asort($_charsets);
		reset($_charsets);

		$name = 'Charset';

		$inputName = 'we_' . $this->Name . '_Charset';

		$_headline = ($withHeadline ? '<tr><td class="defaultfont">' . g_l('weClass', '[Charset]') . '</td></tr>' : '');
		return '
			<table border="0" cellpadding="0" cellspacing="0">
				' . $_headline . '
				<tr><td>' . $this->htmlTextInput($inputName, 24, $this->Charset) . '</td><td></td><td>' . $this->htmlSelect('we_tmp_' . $this->Name . '_select[' . $name . ']', $_charsets, 1, $this->Charset, false, "  onblur=_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName . "'].value=this.options[this.selectedIndex].value;top.we_cmd(\"reload_editpage\"); onchange=_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName . "'].value=this.options[this.selectedIndex].value;top.we_cmd(\"reload_editpage\");", "value", 330) . '</td></tr>
			</table>';
	}

	function formClass(){
		return ($this->ID ?
				'<span class="defaultfont">' . f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), 'Text', $this->DB_WE) . '</span>' :
				$this->formSelect2('', 388, 'TableID', OBJECT_TABLE, 'ID', 'Text', '', 'WHERE IsFolder=0' . ($this->AllowedClasses ? ' AND ID IN(' . $this->AllowedClasses . ')' : '') . ' ORDER BY Path ', 1, $this->TableID, false, "if(_EditorFrame.getEditorDocumentId() != 0){we_cmd('reload_editpage');}else{we_cmd('restore_defaults');};_EditorFrame.setEditorIsHot(true);"));
	}

	function formClassId(){
		return '<span class="defaultfont">' . $this->TableID . '</span>';
	}

	static function getSortArray($tableID, $db){
		if(!$tableID){
			return array();
		}
		$order = makeArrayFromCSV(f('SELECT strOrder FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($tableID), 'strOrder', $db));
		$ctable = OBJECT_X_TABLE . $tableID;
		$tableInfo = $db->metadata($ctable);
		$fields = array();
		foreach($tableInfo as $info){
			if(preg_match('/(.+?)_(.*)/', $info["name"], $regs)){
				if($regs[1] != "OF" && $regs[1] != "variant"){
					$fields[] = array("name" => $regs[2], "type" => $regs[1], "length" => $info["len"]);
				}
			}
		}

		if((count($order) != count($fields)) || !in_array(0, $order)){
			$order = array();
			for($y = 0; $y < count($fields); $y++){
				$order[$y] = $y;
			}
		}

		return $order;
	}

	static function getSortedTableInfo($tableID, $contentOnly = false, $db = '', $checkVariants = false){
		if(!$tableID){
			return array();
		}
		$db = ($db ? $db : new DB_WE());

		$ctable = OBJECT_X_TABLE . $tableID;
		$tableInfo = $db->metadata($ctable);
		$tableInfo2 = array();
		foreach($tableInfo as $arr){
			if($arr['name'] != 'input_' &&
				$arr['name'] != 'text_' &&
				$arr['name'] != 'int_' &&
				$arr['name'] != 'float_' &&
				$arr['name'] != 'date_' &&
				$arr['name'] != 'img_' &&
				$arr['name'] != 'object_' &&
				$arr['name'] != 'multiobject_' &&
				$arr['name'] != 'meta_' &&
				(!defined('WE_SHOP_VARIANTS_ELEMENT_NAME') || $arr['name'] != 'variant_' . WE_SHOP_VARIANTS_ELEMENT_NAME )
			){
				$tableInfo2[] = $arr;
			} elseif(defined('WE_SHOP_VARIANTS_ELEMENT_NAME') && $checkVariants && $arr['name'] == 'variant_' . WE_SHOP_VARIANTS_ELEMENT_NAME){
				$variantdata = $arr;
			}
		}
		if($contentOnly == false){
			return $tableInfo2;
		}
		$tableInfo_sorted = array();

		$order = self::getSortArray(intval($tableID), $db);
		$start = we_objectFile::getFirstTableInfoEntry($tableInfo2);
		foreach($order as $o){
			$tableInfo_sorted[] = $tableInfo2[$start + $o];
		}
		if($checkVariants && isset($variantdata) && is_array($variantdata)){
			$tableInfo_sorted[] = $variantdata;
		}

		return $tableInfo_sorted;
	}

	function getFirstTableInfoEntry($tableInfo){
		foreach($tableInfo as $nr => $field){
			if($field['name'] != 'ID' && substr($field['name'], 0, 3) != 'OF_'){
				return $nr;
			}
		}
		return 0;
	}

	function getFieldHTML($name, $type, $attribs, $editable = true, $variant = false){
		switch($type){
			case 'input':
				return $this->getInputFieldHTML($name, $attribs, $editable, $variant);
			case 'country':
				return $this->getCountryFieldHTML($name, $attribs, $editable, $variant);
			case 'language':
				return $this->getLanguageFieldHTML($name, $attribs, $editable, $variant);
			case 'href':
				return $this->getHrefFieldHTML($name, $attribs, $editable, $variant);
			case 'link':
				return $this->htmlLinkInput($name, $attribs, $editable);
			case 'text':
				return $this->getTextareaHTML($name, $attribs, $editable, $variant);
			case 'img':
				return $this->getImageHTML($name, $attribs, $editable, $variant);
			case 'binary':
				return $this->getBinaryHTML($name, $attribs, $editable);
			case 'flashmovie':
				return $this->getFlashmovieHTML($name, $attribs, $editable);
			case 'quicktime':
				return $this->getQuicktimeHTML($name, $attribs, $editable);
			case 'date':
				return $this->getDateFieldHTML($name, $attribs, $editable, $variant);
			case 'checkbox':
				return $this->getCheckboxFieldHTML($name, $attribs, $editable, $variant);
			case 'int':
				return $this->getIntFieldHTML($name, $attribs, $editable, $variant);
			case 'float':
				return $this->getFloatFieldHTML($name, $attribs, $editable, $variant);
			case 'object':
				return $this->getObjectFieldHTML($name, $attribs, $editable);
			case 'multiobject':
				return $this->getMultiObjectFieldHTML($name, $attribs, $editable);
			case 'meta':
				return $this->getMetaFieldHTML($name, $attribs, $editable, $variant);
			case 'shopVat':
				return $this->getShopVatFieldHtml($name, $attribs, $editable);
		}
	}

	function getElementByType($name, $type, $attribs){
		switch($type){
			case 'text':
			case 'input':
			case 'country':
			case 'language':
				return $this->getElement($name);
			case 'href':
				$hrefArr = $this->getElement($name) ? unserialize($this->getElement($name)) : array();
				if(!is_array($hrefArr)){
					$hrefArr = array();
				}
				return parent::getHrefByArray($hrefArr);
			case 'link':
				return $this->htmlLinkInput($name, $attribs, false, false);
			case 'date':
				return $this->getElement($name);
			case 'float':
			case 'int':
				return strlen($this->getElement($name)) ? $this->getElement($name) : '';
			case 'meta':
				return $this->getElement($name);
			default:
				return $this->getElement($name);
		}
	}

	function getFieldsHTML($editable, $asString = false){
		$foo = getHash('SELECT strOrder,DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . (int) $this->TableID, $this->DB_WE);

		$dv = $foo['DefaultValues'] ? unserialize($foo['DefaultValues']) : array();
		if(!is_array($dv)){
			$dv = array();
		}
		$tableInfo_sorted = $this->getSortedTableInfo($this->TableID, true, $this->DB_WE);
		$fields = array();
		$regs = array();
		foreach($tableInfo_sorted as $cur){
			if(preg_match('/(.+?)_(.*)/', $cur['name'], $regs)){
				$fields[] = array('name' => $regs[2], 'type' => $regs[1]);
			}
		}

		$c = '';
		$parts = array();
		foreach($fields as $field){

			$realName = $field['type'] . '_' . $field['name'];
			$edMerk = $editable;
			if(!((!$dv[$realName]['users']) || $_SESSION['perms']['ADMINISTRATOR'] || we_users_util::isUserInUsers($_SESSION['user']['ID'], $dv[$realName]['users']))){
				$editable = false;
			}

			if($asString){
				$c2 = $this->getFieldHTML($field['name'], $field['type'], $dv[$realName], $editable);
				if($c2){
					$c .= $c2 . we_html_element::htmlBr() . we_html_tools::getPixel(2, 5) . we_html_element::htmlBr();
				}
			} else{
				$c2 = $this->getFieldHTML($field['name'], $field['type'], $dv[$realName], $editable);
				$parts[] = array(
					'headline' => '',
					'html' => $c2,
					'space' => 0,
					'name' => $realName);
			}

			$editable = $edMerk;
		}
		return $asString ? $c : $parts;
	}

	function getMetaFieldHTML($name, $attribs, $editable = true, $variant = false){
		$vals = ($variant ? $attribs['meta'] : $this->DefArray['meta_' . $name]['meta']);

		if(!$editable){
			return $this->getPreviewView($name, $vals[$this->getElement($name)]);
		}
		if($variant){
			$fname = 'we_' . $this->Name . '_meta[' . $name . ']';
			return $this->htmlSelect($fname, $vals, 1, $this->getElement($name));
		}
		return $this->formSelectFromArray('meta', $name, $vals, '<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["meta_" . $name]["required"] ? "*" : "") . "</span>" . ( isset($this->DefArray["meta_$name"]['editdescription']) && $this->DefArray["meta_$name"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["meta_$name"]['editdescription'] . '</div>' : we_html_element::htmlBr()), 1, '', false, 'onChange="_EditorFrame.setEditorIsHot(true);"', 'left', 'defaultfont', '', '', '', $variant);
	}

	function getObjectFieldHTML($ObjectID, $attribs, $editable = true){
		$db = new DB_WE();
		$foo = getHash('SELECT o.Text,of.ID FROM ' . OBJECT_TABLE . ' o,' . OBJECT_FILES_TABLE . ' of WHERE of.Path=o.Path AND o.ID=' . intval($ObjectID), $db);
		$name = isset($foo['Text']) ? $foo['Text'] : '';
		$pid = $foo['ID'];
		$textname = 'we_' . $this->Name . '_txt[we_object_' . $ObjectID . '_path]';
		$idname = 'we_' . $this->Name . '_object[we_object_' . $ObjectID . ']';
		$myid = $this->getElement('we_object_' . $ObjectID);
		$path = $this->getElement('we_object_' . $ObjectID . '_path');
		$tmp = getHash('SELECT Path,Published FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($myid), $db);
		if(!empty($tmp)){
			list($path, $npubl) = $tmp;
		}
		if($path == ''){
			$myid = 0;
			$npubl = 1;
		}
		if($myid){
			$ob = new we_objectFile();
			$ob->initByID($myid, OBJECT_FILES_TABLE);
			$ob->DefArray = $ob->getDefaultValueArray();
		} else{
			$ob = new we_objectFile();
		}
		$table = OBJECT_FILES_TABLE;

//	editObjectFile Button
		if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == 'seem'){
			$editObjectButton = we_button::create_button('image:btn_edit_object', "javascript:top.doClickDirect('" . $myid . "','objectFile','" . OBJECT_FILES_TABLE . "');");
			$editObjectButtonDis = we_button::create_button("image:btn_edit_object", "", true, 44, 22, "", "", true);
			$inputWidth = 443;

			$uniq = md5(uniqid(__FUNCTION__, true)); // FIXME: #6590: str_replace('.', '', uniqid("",true))
			$openCloseButton = we_multiIconBox::_getButton($uniq, "weToggleBox('$uniq','','')", "down", g_l('global', "[openCloseBox]"));
			$openCloseButtonDis = we_html_tools::getPixel(21, 1);

			$objectpreview = "<div id=\"text_" . $uniq . "\"></div><div id=\"table_" . $uniq . "\" style=\"display:block; padding: 10px 0px 20px 30px;\">";
			$objectpreview .= $myid ? $ob->getFieldsHTML(0, true) : "";
			$objectpreview .= "</div>";
		} else{
			$editObjectButton = '';
			$editObjectButtonDis = '';
			$openCloseButton = '';
			$openCloseButtonDis = '';
			$inputWidth = 508;
			$objectpreview = '';
		}

		if(!$editable){
			$uniq = md5(uniqid(__FUNCTION__, true)); // FIXME: #6590: str_replace('.', '', uniqid("",true))
			$txt = $ob->Text ? $ob->Text : $name;
			$but = we_multiIconBox::_getButton($uniq, "weToggleBox('$uniq','" . $txt . "','" . $txt . "')", "down", g_l('global', "[openCloseBox]"));

			return we_button::create_button_table(
					array(
						$but,
						'<span style="cursor: pointer;" class="weObjectPreviewHeadline" id="text_' . $uniq . '" onClick="weToggleBox(\'' . $uniq . '\',\'' . $txt . '\',\'' . $txt . '\');">' . $txt . '</span>' . ($npubl ? '' : ' <span class="weObjectPreviewHeadline" style="color:red">' . g_l('modules_object', '[not_published]') . '</span>')
					)
				) .
				'<div id="table_' . $uniq . '" style="display:block; padding: 10px 0px 20px 30px;">' .
				$myid ? $ob->getFieldsHTML(0, true) : '' .
				'</div>';
		}

		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('change_objectlink','" . $GLOBALS['we_transaction'] . "','object_" . $pid . "');");

		$_buttons = array(
			we_button::create_button('select', "javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$pid','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")")
		);

		if(($_but = $myid ? $editObjectButton : $editObjectButtonDis)){
			$_buttons[] = $_but;
		}

		if(($_but = $myid ? $openCloseButton : $openCloseButtonDis)){
			$_buttons[] = $_but;
		}

		$_buttons[] = we_button::create_button("image:btn_function_trash", "javascript:document.forms['we_form'].elements['$idname'].value=0;document.forms['we_form'].elements['$textname'].value='';_EditorFrame.setEditorIsHot(true);top.we_cmd('reload_entry_at_object','" . $GLOBALS['we_transaction'] . "','object_" . $pid . "')");

		$button = we_button::create_button_table($_buttons, 5);

		return $this->htmlFormElementTable(
				$this->htmlTextInput($textname, 30, $path, "", ' readonly', "text", $inputWidth, 0), '<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["object_" . $ObjectID]["required"] ? "*" : "") . '</span>' . ($npubl ? '' : ' <span style="color:red">' . g_l('modules_object', '[not_published]') . '</span>') . ( isset($this->DefArray["object_$ObjectID"]['editdescription']) && $this->DefArray["object_$ObjectID"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["object_$ObjectID"]['editdescription'] . '</div>' : we_html_element::htmlBr() ), "left", "defaultfont", $this->htmlHidden($idname, $myid), we_html_tools::getPixel(5, 4), $button) .
			$objectpreview;
	}

	function getMultiObjectFieldHTML($name, $attribs, $editable = true){
		$db = new DB_WE();
		$table = OBJECT_FILES_TABLE;
		$temp = unserialize($this->getElement($name, 'dat'));
		$objects = isset($temp['objects']) ? $temp['objects'] : array();
		$classid = $this->DefArray['multiobject_' . $name]['class'];
		$max = intval($this->DefArray['multiobject_' . $name]['max']);

		$show = (($max == 0) || ($max >= count($objects)) ? count($objects) : $max);

		if($editable){
			$f = 1;

			$text = '<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["multiobject_" . $name]["required"] ? "*" : "") . '</span>' . ( isset($this->DefArray["multiobject_$name"]['editdescription']) && $this->DefArray["multiobject_$name"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["multiobject_$name"]['editdescription'] . '</div>' : we_html_element::htmlBr() );
			$content = $this->htmlFormElementTable('', $text);

			for($f = 0; $f < $show; $f++){
				$textname = 'we_' . $this->Name . '_txt[' . $name . '_path' . $f . ']';
				$idname = 'we_' . $this->Name . '_multiobject[' . $name . '_default' . $f . ']';

				$classPath = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid), 'Path', $db);
				$rootDir = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $classPath . '"', 'ID', $db);
				$path = $this->getElement('we_object_' . $name . '_path');
				if(($myid = intval($objects[$f]))){
					$path = $path ? $path : f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $myid, 'Path', $db);
				}

				if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == 'seem'){

					$ob = new we_objectFile();
					$ob->initByID($myid, OBJECT_FILES_TABLE);
					$ob->DefArray = $ob->getDefaultValueArray();
					$uniq = md5(uniqid(__FUNCTION__, true));

					$editObjectButton = we_button::create_button("image:btn_edit_object", "javascript:top.doClickDirect('" . $myid . "','objectFile','" . OBJECT_FILES_TABLE . "');");
					$editObjectButtonDis = we_button::create_button("image:btn_edit_object", "", true, 44, 22, "", "", true);

					$inputWidth = 346;

					$openCloseButton = we_multiIconBox::_getButton($uniq, "weToggleBox('$uniq','','')", "right", g_l('global', "[openCloseBox]"));
					$openCloseButtonDis = we_html_tools::getPixel(21, 1);

					$reloadEntry = "opener.top.we_cmd('change_objectlink','" . $GLOBALS['we_transaction'] . "','multiobject_" . $name . "');";
				} else{
					$editObjectButton = '';
					$editObjectButtonDis = '';
					$inputWidth = 411;

					$openCloseButton = '';
					$openCloseButtonDis = '';

					$reloadEntry = '';
				}
				$alerttext = g_l('modules_object', '[multiobject_recursion]');
				$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
				$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
				$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);if(currentID==" . $this->ID . "){" . we_message_reporting::getShowMessageCall($alerttext, we_message_reporting::WE_MESSAGE_ERROR) . "opener.document.we_form.elements['$idname'].value='';opener.document.we_form.elements['$textname'].value='';;};" . $reloadEntry);

				$selectObject = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDir','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")");

				$upbut = we_button::create_button("image:btn_direction_up", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('up_meta_at_object','" . $GLOBALS['we_transaction'] . "','multiobject_" . $name . "','" . ($f) . "')");
				$upbutDis = we_button::create_button("image:btn_direction_up", "#", true, -1, -1, "", "", true);
				$downbut = we_button::create_button("image:btn_direction_down", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('down_meta_at_object','" . $GLOBALS['we_transaction'] . "','multiobject_" . $name . "','" . ($f) . "')");
				$downbutDis = we_button::create_button("image:btn_direction_down", "#", true, -1, -1, "", "", true);

				$plusbut = we_button::create_button("image:btn_add_listelement", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('insert_meta_at_object','" . $GLOBALS['we_transaction'] . "','multiobject_" . $name . "','" . ($f) . "')");
				$plusbutDis = we_button::create_button("image:btn_add_listelement", "#", true, -1, -1, "", "", true);
				$trashbut = we_button::create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('delete_meta_at_object','" . $GLOBALS['we_transaction'] . "','multiobject_" . $name . "','" . ($f) . "')");

				$buttontable = we_button::create_button_table(
						array(
						$selectObject,
						($myid ? $editObjectButton : $editObjectButtonDis),
						($myid ? $openCloseButton : $openCloseButtonDis),
						$this->htmlHidden($idname, $myid),
						((count($objects) < $max || $max == "" || $max == 0) ? $plusbut : $plusbutDis),
						($f > 0 ? $upbut : $upbutDis ),
						($f < count($objects) - 1 ? $downbut : $downbutDis),
						$trashbut
						), 5
				);

				$content .= $this->htmlFormElementTable(
					$this->htmlTextInput($textname, 30, $path, 255, 'onChange="_EditorFrame.setEditorIsHot(true);" readonly ', "text", $inputWidth), '', 'left', 'defaultfont', we_html_tools::getPixel(20, 4), $buttontable);

				if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == "seem" && $myid){
					$ob = new we_objectFile();
					$ob->initByID($myid, OBJECT_FILES_TABLE);
					$ob->DefArray = $ob->getDefaultValueArray();

					$content .= '<div id="text_' . $uniq . '"></div><div id="table_' . $uniq . '" style="display:none; padding: 10px 0px 20px 30px;">' .
						$ob->getFieldsHTML(0, true) .
						'</div>';
				}
			}

			$content .= (count($objects) < $max || $max == "" || $max == 0 ?
					we_button::create_button("image:btn_add_listelement", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('insert_meta_at_object','" . $GLOBALS['we_transaction'] . "','multiobject_" . $name . "','" . ($f - 1) . "')") :
					we_button::create_button("image:btn_add_listelement", "#", true, 21, 22, "", "", true));

			$new = array(
				'class' => $classid,
				'max' => $max,
				'objects' => $objects,
			);
			$this->setElement($name, serialize($new));

			return $content;
		}

		$content = '';
		if($show){
			for($f = 0; $f < $show; $f++){
				$myid = $objects[$f];
				if($myid){
					$uniq = md5(uniqid(__FUNCTION__, true)); // FIXME: #6590: str_replace('.', '', uniqid("",true))
					$ob = new we_objectFile();
					$ob->initByID($myid, OBJECT_FILES_TABLE);
					$ob->DefArray = $ob->getDefaultValueArray();
					$txt = $ob->Text;

					$but = we_multiIconBox::_getButton($uniq, "weToggleBox('$uniq','" . $txt . "','" . $txt . "')", "right", g_l('global', "[openCloseBox]"));
					$content .= we_button::create_button_table(
							array(
								$but,
								'<span style="cursor: pointer;" class="weObjectPreviewHeadline" id="text_' . $uniq . '" onClick="weToggleBox(\'' . $uniq . '\',\'' . $txt . '\',\'' . $txt . '\');" >' . $txt . '</span>'
							)
					);

					$content .= "<div id=\"table_" . $uniq . "\" style=\"display:none; padding: 10px 0px 20px 30px;\">" .
						$ob->getFieldsHTML(0, true) .
						'</div>';
				}
			}

			$new = array(
				'class' => $classid,
				'max' => $max,
				'objects' => $objects,
			);
			$this->setElement($name, serialize($new));

			return $content;
		}
		return $this->getPreviewView($name, $content);
	}

	function getShopVatFieldHtml($name, $attribs, $we_editmode = true){
		if($we_editmode){

			$shopVats = weShopVats::getAllShopVATs();

			$values = array();
			foreach($shopVats as $shopVat){
				$values[$shopVat->id] = $shopVat->vat . '% - ' . $shopVat->text;
			}

			$val = $this->getElement($name) ? $this->getElement($name) : $attribs['default'];

			return
				'<table class="defaultfont">
				<tr><td><span class="weObjectPreviewHeadline">' . $name . '</span>' . ( isset($this->DefArray["shopVat_shopvat"]['editdescription']) && $this->DefArray["shopVat_shopvat"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["shopVat_shopvat"]['editdescription'] . '</div>' : '' ) . '</td></tr>
				<tr><td>' . we_class::htmlSelect('we_' . $this->Name . '_shopVat[' . $name . ']', $values, 1, $val) . '</td></tr>
			</table>';
		}
		$val = $this->getElement($name);

		$weShopVat = weShopVats::getShopVATById($val);
		if(!$weShopVat){
			$weShopVat = weShopVats::getStandardShopVat();
		}
		return $this->getPreviewView($name, $weShopVat->vat);
	}

	function getHrefFieldHTML($n, $attribs, $we_editmode = true, $variant = false){
		$type = isset($attribs["hreftype"]) ? $attribs["hreftype"] : '';
		$directory = (isset($attribs["hrefdirectory"]) && $attribs["hrefdirectory"] == "true") ? true : false;
		$file = (isset($attribs["hreffile"]) && $attribs["hreffile"] == "false") ? false : true;
		$hrefArr = $this->getElement($n) ? unserialize($this->getElement($n)) : array();
		if(!is_array($hrefArr)){
			$hrefArr = array();
		}
		if($we_editmode){
			$nint = $n . "_we_jkhdsf_int";
			$nintID = $n . "_we_jkhdsf_intID";
			$nintPath = $n . "_we_jkhdsf_intPath";
			$nextPath = $n . "_we_jkhdsf_extPath";

			$attr = ' size="20" ';

			$int = isset($hrefArr["int"]) ? $hrefArr["int"] : false;
			$intID = (isset($hrefArr["intID"]) && $hrefArr["intID"]) ? $hrefArr["intID"] : '';
			$intPath = $intID ? id_to_path($intID) : "";
			$extPath = isset($hrefArr["extPath"]) ? $hrefArr["extPath"] : "";
			$int_elem_Name = 'we_' . $this->Name . '_href[' . $nint . ']';
			$intPath_elem_Name = 'we_' . $this->Name . '_href[' . $nintPath . ']';
			$intID_elem_Name = 'we_' . $this->Name . '_href[' . $nintID . ']';
			$ext_elem_Name = 'we_' . $this->Name . '_href[' . $nextPath . ']';
			switch($type){
				case "int":
					$out = $this->hrefRow($intID_elem_Name, $intID, $intPath_elem_Name, $intPath, $attr, $int_elem_Name, false, true, "", $file, $directory);
					break;
				case "ext":
					$out = $this->hrefRow("", "", $ext_elem_Name, $extPath, $attr, $int_elem_Name, false, true, "", $file, $directory);
					break;
				default:
					$out = $this->hrefRow($intID_elem_Name, $intID, $intPath_elem_Name, $intPath, $attr, $int_elem_Name, true, $int, "", $file, $directory) .
						$this->hrefRow("", "", $ext_elem_Name, $extPath, $attr, $int_elem_Name, true, $int, "", $file, $directory);
			}
			return ($variant ? '' : '<span class="weObjectPreviewHeadline"><b>' . $n . ($this->DefArray["href_" . $n]["required"] ? '*' : '') . '</b></span>' . (isset($this->DefArray["href_" . $n]['editdescription']) && $this->DefArray["href_" . $n]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["href_" . $n]['editdescription'] . '</div>' : we_html_element::htmlBr() )) .
				'<table border="0" cellpadding="0" cellspacing="0" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif">' . $out . '</table>';
		}
		return $this->getPreviewView($n, parent::getHrefByArray($hrefArr));
	}

	function htmlLinkInput($n, $attribs, $we_editmode = true, $headline = true){
		$attribs["name"] = $n;
		$out = '';
		$link = $this->getElement($n) ? unserialize($this->getElement($n)) : array();
		if(is_array($link)){
			if(!count($link)){
				$link = array("ctype" => "text", "type" => "ext", "href" => "#", "text" => g_l('global', "[new_link]"));
			}
			$img = new we_imageDocument();
			$content = parent::getLinkContent($link, $this->ParentID, $this->Path, $GLOBALS['DB_WE'], $img);

			$startTag = $this->getLinkStartTag($link, array(), $this->ParentID, $this->Path, $GLOBALS['DB_WE'], $img);

			$editbut = we_button::create_button("edit", "javascript:we_cmd('edit_link_at_object','" . $n . "')");
			$delbut = we_button::create_button("image:btn_function_trash", "javascript:we_cmd('delete_link_at_object','" . $GLOBALS['we_transaction'] . "', 'link_" . $n . "')");
			$buttons = we_button::create_button_table(array($editbut, $delbut));
			if(!$content){
				$content = g_l('global', "[new_link]");
			}
			$out = ($startTag ? $startTag . $content . '</a>' : $content) . ($we_editmode ? ($buttons) : "");
		}
		return ($headline ?
				'<span class="weObjectPreviewHeadline">' . $n . '</span>' . ( $we_editmode && isset($this->DefArray["link_" . $n]['editdescription']) && $this->DefArray["link_" . $n]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["link_" . $n]['editdescription'] . '</div>' : we_html_element::htmlBr() ) :
				'') . $out;
	}

	function getPreviewView($name, $content){
		return '<div class="weObjectPreviewHeadline">' . $name . '</div>' . (($content !== '') ?
				'<div class="defaultfont">' . $content . '</div>' : '');
	}

	function getInputFieldHTML($name, $attribs, $editable = true, $variant = false){
		if(!$editable){
			return $this->getPreviewView($name, $this->getElement($name));
		}
		return ($variant ?
				'' :
				'<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["input_" . $name]["required"] ? '*' : '') . '</span>' . (isset($this->DefArray["input_" . $name]['editdescription']) && $this->DefArray["input_" . $name]['editdescription'] ? we_html_element::htmlBr() . '<div class="objectDescription">' . $this->DefArray["input_" . $name]['editdescription'] . '</div>' : we_html_element::htmlBr() )
			) .
			$this->htmlTextInput("we_" . $this->Name . "_input[$name]", 40, $this->getElement($name), $this->getElement($name, "len"), 'onChange="_EditorFrame.setEditorIsHot(true);"', "text", 620);
	}

	function getCountryFieldHTML($name, $attribs, $editable = true, $variant = false){
		if(!Zend_Locale::hasCache()){
			Zend_Locale::setCache(getWEZendCache());
		}

		$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
		$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);

		if(!$editable){
			return '<div class="weObjectPreviewHeadline">' . $name . '</div>' .
				($this->getElement($name) != '--' || $this->getElement($name) != '' ? '<div class="defaultfont">' . CheckAndConvertISObackend(Zend_Locale::getTranslation($this->getElement($name), 'territory', $langcode)) . '</div>' :
					'');
		}

		$countrycode = array_search($langcode, $GLOBALS['WE_LANGS_COUNTRIES']);
		$countryselect = new we_html_select(array("name" => "we_" . $this->Name . "_language[$name]", "size" => 1, "style" => "width:620;", "class" => "wetextinput", "onChange" => "_EditorFrame.setEditorIsHot(true);"));

		$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));

		foreach($topCountries as $countrykey => &$countryvalue){
			$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
		}
		unset($countryvalue);
		$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
		foreach($shownCountries as $countrykey => &$countryvalue){
			$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
		}
		unset($countryvalue);
		$oldLocale = setlocale(LC_ALL, NULL);
		setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
		asort($topCountries, SORT_LOCALE_STRING);
		asort($shownCountries, SORT_LOCALE_STRING);
		setlocale(LC_ALL, $oldLocale);

		if(WE_COUNTRIES_DEFAULT != ''){
			$countryselect->addOption('--', CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT));
		}
		foreach($topCountries as $countrykey => &$countryvalue){
			$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
		}
		unset($countryvalue);
		if(!empty($topCountries) && !empty($shownCountries)){
			$countryselect->addOption('-', '----', array("disabled" => "disabled"));
		}

		foreach($shownCountries as $countrykey => &$countryvalue){
			$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
		}
		unset($countryvalue);
		$countryselect->selectOption($this->getElement($name));

		return ($variant ?
				'' :
				'<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["country_" . $name]["required"] ? "*" : "") . "</span>" . (isset($this->DefArray["country_" . $name]['editdescription']) && $this->DefArray["country_" . $name]['editdescription'] ? we_html_element::htmlBr() . '<div class="objectDescription">' . $this->DefArray["country_" . $name]['editdescription'] . '</div>' : we_html_element::htmlBr() )
			) .
			$countryselect->getHtml();
	}

	function getLanguageFieldHTML($name, $attribs, $editable = true, $variant = false){
		if(!$editable){
			return '<div class="weObjectPreviewHeadline">' . $name . '</div>' .
				($this->getElement($name) != '--' || $this->getElement($name) != '' ? '<div class="defaultfont">' . CheckAndConvertISObackend(Zend_Locale::getTranslation($this->getElement($name), 'language', we_core_Local::weLangToLocale($GLOBALS['WE_LANGUAGE']))) . '</div>' :
					'');
		}
		$frontendL = $GLOBALS["weFrontendLanguages"];
		foreach($frontendL as &$lcvalue){
			$lccode = explode('_', $lcvalue);
			$lcvalue = $lccode[0];
		}
		$languageselect = new we_html_select(array("name" => "we_" . $this->Name . "_language[$name]", "size" => 1, "style" => "width:620;", "class" => "wetextinput", "onChange" => "_EditorFrame.setEditorIsHot(true);"));
		if(!$this->DefArray["language_" . $name]["required"]){
			$languageselect->addOption('--', '');
		}

		foreach(g_l('languages', '') as $languagekey => $languagevalue){
			if(in_array($languagekey, $frontendL)){
				$languageselect->addOption($languagekey, $languagevalue);
			}
		}
		$languageselect->selectOption($this->getElement($name));
		return ($variant ?
				'' :
				'<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["language_" . $name]["required"] ? "*" : "") . "</span>" . (isset($this->DefArray["language_" . $name]['editdescription']) && $this->DefArray["language_" . $name]['editdescription'] ? we_html_element::htmlBr() . '<div class="objectDescription">' . $this->DefArray["language_" . $name]['editdescription'] . '</div>' : we_html_element::htmlBr() )
			) . $languageselect->getHtml();
	}

	function getCheckboxFieldHTML($name, $attribs, $editable = true){
		if(!$editable){
			return $this->getPreviewView($name, ($this->getElement($name) ? g_l('global', "[yes]") : g_l('global', "[no]")));
		}
		return '<span class="weObjectPreviewHeadline"><b>' . $name . ($this->DefArray["checkbox_" . $name]["required"] ? "*" : "") . "</b></span>" . ( isset($this->DefArray["checkbox_" . $name]['editdescription']) && $this->DefArray["checkbox_" . $name]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["checkbox_" . $name]['editdescription'] . '</div>' : we_html_element::htmlBr()) .
			we_forms::checkboxWithHidden(($this->getElement($name) ? true : false), "we_" . $this->Name . "_checkbox[$name]", "", false, "defaultfont", "_EditorFrame.setEditorIsHot(true);");
	}

	function getIntFieldHTML($name, $attribs, $editable = true, $variant = false){
		if(!$editable){
			return $this->getPreviewView($name, (strlen($this->getElement($name)) ? $this->getElement($name) : ''));
		}
		return ($variant ? '' : '<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["int_" . $name]["required"] ? "*" : "") . "</span>" . ( isset($this->DefArray["int_" . $name]['editdescription']) && $this->DefArray["int_" . $name]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["int_" . $name]['editdescription'] . '</div>' : we_html_element::htmlBr() )
			) .
			$this->htmlTextInput("we_" . $this->Name . "_int[$name]", 40, $this->getElement($name), $this->getElement($name, "len"), 'onChange="_EditorFrame.setEditorIsHot(true);"', "text", 620);
	}

	function getFloatFieldHTML($name, $attribs, $editable = true, $variant = false){
		if(!$editable){
			return $this->getPreviewView($name, $this->getElement($name));
		}

		return ($variant ?
				'' :
				'<span class="weObjectPreviewHeadline"><b>' . $name . ($this->DefArray["float_" . $name]["required"] ? "*" : "") . "</b></span>" . ( isset($this->DefArray["float_" . $name]['editdescription']) && $this->DefArray["float_" . $name]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["float_" . $name]['editdescription'] . '</div>' : we_html_element::htmlBr())
			) .
			$this->htmlTextInput("we_" . $this->Name . "_float[$name]", 40, strlen($this->getElement($name)) ? $this->getElement($name) : "", $this->getElement($name, "len"), 'onChange="_EditorFrame.setEditorIsHot(true);"', "text", 620);
	}

	function getDateFieldHTML($name, $attribs, $editable = true, $variant = false){
		if(!$editable){
			return $this->getPreviewView($name, date(g_l('date', '[format][default]'), abs($this->getElement($name))));
		}
		$d = abs($this->getElement($name));
		return ($variant ?
				'' :
				'<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray['date_' . $name]["required"] ? '*' : '') . '</span>' . ( isset($this->DefArray["date_$name"]['editdescription']) && $this->DefArray["date_$name"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["date_$name"]['editdescription'] . '</div>' : we_html_element::htmlBr()) . we_html_tools::getPixel(2, 2) . we_html_element::htmlBr()
			) .
			we_html_tools::getDateInput2("we_" . $this->Name . '_date[' . $name . ']', ($d ? $d : time()), true);
	}

	function getTextareaHTML($name, $attribs, $editable = true, $variant = false){
		if($editable){
			//	send charset which might be determined in template
			$charset = (isset($this->Charset) ? $this->Charset : $GLOBALS['WE_BACKENDCHARSET']);

			$value = $this->getElement($name);
			$attribs["width"] = isset($attribs["width"]) ? $attribs["width"] : 620;
			$attribs["height"] = isset($attribs["height"]) ? $attribs["height"] : 200;
			$attribs["rows"] = 10;
			$attribs["cols"] = 60;
			$attribs['bgcolor'] = isset($attribs["bgcolor"]) ? $attribs["bgcolor"] : (WYSIWYG_TYPE == 'tinyMCE' ? '' : 'white');
			$attribs['tinyparams'] = isset($attribs["tinyparams"]) ? $attribs["tinyparams"] : "";
			$attribs["class"] = isset($attribs["class"]) ? $attribs["class"] : "";
			if(isset($attribs["cssClasses"])){
				$attribs["classes"] = $attribs["cssClasses"];
			}

			$removefirstparagraph = ((!isset($attribs["removefirstparagraph"])) || ($attribs["removefirstparagraph"] == "on")) ? true : false;
			$xml = (isset($attribs["xml"]) && ($attribs["xml"] == "on")) ? true : false;

			$autobr = $this->getElement($name, "autobr") ? $this->getElement($name, "autobr") : (isset($attribs["autobr"]) ? $attribs["autobr"] : "");
			$autobrName = 'we_' . $this->Name . '_text[' . $name . '#autobr]';
			$textarea = we_forms::weTextarea('we_' . $this->Name . '_text[' . $name . ']', $value, $attribs, $autobr, $autobrName, true, "", (isset($attribs["classes"]) && $attribs["classes"]) ? false : true, false, $xml, $removefirstparagraph, $charset, true, false, $name);

			return ($variant ? '' :
					'<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["text_" . $name]["required"] ? "*" : "") . "</span>" . ( isset($this->DefArray["text_" . $name]['editdescription']) && $this->DefArray["text_" . $name]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["text_" . $name]['editdescription'] . '</div>' : we_html_element::htmlBr())
				) . $textarea;
		} else{
			return $this->getPreviewView($name, $this->getFieldByVal($this->getElement($name), "txt", $attribs));
		}
	}

	function getImageHTML($name, $attribs, $editable = true, $variant = false){
		$img = new we_imageDocument();
		$id = $this->getElement($name);
		if(!id_to_path($id)){
			$id = 0;
			$this->setElement($name, 0);
		}
		$img->initByID($id, FILE_TABLE, false);

// handling thumbnails for this image
// identifying default thumbnail of class:
		$defvals = $this->getDefaultValueArray();
		$thumbID = isset($defvals["img_" . $name]["defaultThumb"]) ? $defvals["img_" . $name]["defaultThumb"] : "";
// creating thumbnail only if it really exists:
		$thumbdb = new DB_WE();
		$thumbdb->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE);
		$thumbs = $thumbdb->getAll();
		array_unshift($thumbs, '');
		if(!empty($thumbID) && isset($thumbs[$thumbID]["ID"]) && $thumbID <= count($thumbs)){
			if($img->ID > 0){
				$thumbObj = new we_thumbnail();
				$thumbObj->initByThumbID($thumbs[$thumbID]["ID"], $img->ID, $img->Filename, $img->Path, $img->Extension, $img->getElement("origwidth"), $img->getElement("origheight"), $img->getDocument());
				$thumbObj->createThumb();
				$_imgSrc = $thumbObj->getOutputPath();
				$_imgHeight = $thumbObj->getOutputHeight();
				$_imgWight = $thumbObj->getOutputWidth();
			} else{
				$_imgSrc = IMAGE_DIR . 'icons/no_image.gif';
				$_imgHeight = 64;
				$_imgWight = 64;
			}
		} else{
			$thumbID = "";
		}

		if(!$editable){
			return $this->getPreviewView($name, $img->getHtml());
		}
		$fname = 'we_' . $this->Name . '_img[' . $name . ']';
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_cmd_enc("opener.top.we_cmd('reload_entry_at_object','" . $GLOBALS['we_transaction'] . "','img_" . $name . "');opener._EditorFrame.setEditorIsHot(true);opener.setScrollTo();");

		$content = '<input type=hidden name="' . $fname . '" value="' . $this->getElement($name) . '" />' .
// show thumbnail of image if there exists one:
			(!empty($thumbID) ?
				'<img src="' . $_imgSrc . '" height="' . $_imgHeight . '" width="' . $_imgWight . '" />' :
				$img->getHtml()) .
			we_button::create_button_table(array(we_button::create_button("edit", "javascript:we_cmd('openDocselector','" . ($id != 0 ? $id : (isset($this->DefArray["img_$name"]['defaultdir']) ? $this->DefArray["img_$name"]['defaultdir'] : 0)) . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "', " . (isset($this->DefArray["img_$name"]['rootdir']) && $this->DefArray["img_$name"]['rootdir'] != "" ? $this->DefArray["img_$name"]['rootdir'] : 0) . ",'image/*')"),
				we_button::create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_object','" . $GLOBALS['we_transaction'] . "','img_" . $name . "');setScrollTo();")));

		return ($variant ?
				'' :
				'<span class="weObjectPreviewHeadline"><b>' . $name . ($this->DefArray["img_" . $name]["required"] ? '*' : '') . '</b></span>' . ( isset($this->DefArray["img_$name"]['editdescription']) && $this->DefArray["img_$name"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["img_$name"]['editdescription'] . '</div>' : we_html_element::htmlBr())
			) . $content;
	}

	function getBinaryHTML($name, $attribs, $editable = true){
		$img = new we_otherDocument();
		$id = $this->getElement($name);
		$img->initByID($id, FILE_TABLE, false);

		if($editable){
			$fname = 'we_' . $this->Name . '_img[' . $name . ']';
			$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
			$wecmdenc3 = we_cmd_enc("opener.top.we_cmd('reload_entry_at_object','" . $GLOBALS['we_transaction'] . "','binary_" . $name . "');opener._EditorFrame.setEditorIsHot(true);");

			$content = '<input type=hidden name="' . $fname . '" value="' . $this->getElement($name) . '" />' .
				$img->getHtml() .
				we_button::create_button_table(array(we_button::create_button("edit", "javascript:we_cmd('openDocselector','" . ($id != 0 ? $id : (isset($this->DefArray["binary_$name"]['defaultdir']) ? $this->DefArray["binary_$name"]['defaultdir'] : 0)) . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "', " . (isset($this->DefArray["binary_$name"]['rootdir']) && $this->DefArray["binary_$name"]['rootdir'] != "" ? $this->DefArray["binary_$name"]['rootdir'] : 0) . ",'application/*')"),
					we_button::create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_object','" . $GLOBALS['we_transaction'] . "','binary_" . $name . "')")));
			return '<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["binary_" . $name]["required"] ? "*" : "") . "</span>" . ( isset($this->DefArray["binary_$name"]['editdescription']) && $this->DefArray["binary_$name"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["binary_$name"]['editdescription'] . '</div>' : we_html_element::htmlBr()) . $content;
		} else{
			$content = $img->getHtml();
			return $this->getPreviewView($name, $content);
		}
	}

	function getFlashmovieHTML($name, $attribs, $editable = true){
		$img = new we_flashDocument();
		$id = $this->getElement($name);
		$img->initByID($id, FILE_TABLE, false);

		if($editable){
			$content = '';
			$fname = 'we_' . $this->Name . '_img[' . $name . ']';
			$content .= '<input type=hidden name="' . $fname . '" value="' . $this->getElement($name) . '" />' . $img->getHtml();
			$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
			$wecmdenc3 = we_cmd_enc("opener.top.we_cmd('reload_entry_at_object','" . $GLOBALS['we_transaction'] . "','flashmovie_" . $name . "');opener._EditorFrame.setEditorIsHot(true);");

			$content .= we_button::create_button_table(array(we_button::create_button("edit", "javascript:we_cmd('openDocselector','" . ($id != 0 ? $id : (isset($this->DefArray["flashmovie_$name"]['defaultdir']) ? $this->DefArray["flashmovie_$name"]['defaultdir'] : 0)) . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "', " . (isset($this->DefArray["flashmovie_$name"]['rootdir']) && $this->DefArray["flashmovie_$name"]['rootdir'] != "" ? $this->DefArray["flashmovie_$name"]['rootdir'] : 0) . ",'application/x-shockwave-flash')"),
					we_button::create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_object','" . $GLOBALS['we_transaction'] . "','flashmovie_" . $name . "')")));
			return '<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["flashmovie_" . $name]["required"] ? "*" : "") . "</span>" . ( isset($this->DefArray["flashmovie_$name"]['editdescription']) && $this->DefArray["flashmovie_$name"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["flashmovie_$name"]['editdescription'] . '</div>' : we_html_element::htmlBr()) . $content;
		}
		return $this->getPreviewView($name, $img->getHtml());
	}

	function getQuicktimeHTML($name, $attribs, $editable = true){
		$img = new we_quicktimeDocument();
		$id = $this->getElement($name);
		$img->initByID($id, FILE_TABLE, false);

		if($editable){
			$fname = 'we_' . $this->Name . '_img[' . $name . ']';
			$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $fname . "'].value");
			$wecmdenc3 = we_cmd_enc("opener.top.we_cmd('reload_entry_at_object','" . $GLOBALS['we_transaction'] . "','quicktime_" . $name . "');opener._EditorFrame.setEditorIsHot(true);");

			return '<span class="weObjectPreviewHeadline">' . $name . ($this->DefArray["quicktime_" . $name]["required"] ? "*" : "") . "</span>" . ( isset($this->DefArray["quicktime_$name"]['editdescription']) && $this->DefArray["quicktime_$name"]['editdescription'] ? '<div class="objectDescription">' . $this->DefArray["quicktime_$name"]['editdescription'] . '</div>' : we_html_element::htmlBr()) .
				'<input type=hidden name="' . $fname . '" value="' . $this->getElement($name) . '" />' . $img->getHtml() .
				we_button::create_button_table(array(we_button::create_button("edit", "javascript:we_cmd('openDocselector','" . ($id != 0 ? $id : (isset($this->DefArray["quicktime_$name"]['defaultdir']) ? $this->DefArray["quicktime_$name"]['defaultdir'] : 0)) . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "', " . (isset($this->DefArray["quicktime_$name"]['rootdir']) && $this->DefArray["quicktime_$name"]['rootdir'] != "" ? $this->DefArray["quicktime_$name"]['rootdir'] : 0) . ",'video/quicktime')"),
					we_button::create_button("image:btn_function_trash", "javascript:we_cmd('remove_image_at_object','" . $GLOBALS['we_transaction'] . "',quicktime_" . $name . "')")));
		}
		return $this->getPreviewView($name, $img->getHtml());
	}

	function getDefaultValueArray(){
		if($this->TableID){
			$foo = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), 'DefaultValues', $this->DB_WE);
			return $foo ? unserialize($foo) : array();
		}
		t_e('error no tableID!', $this);
		t_e('error', 'error no tableID!');
	}

	function getContentData(/* $loadBinary = 0 */){
		if(!$this->TableID){
			return;
		}
		$ID = $this->ObjectID;
		$DataTable = OBJECT_X_TABLE . $this->TableID;
		$db = $this->DB_WE;
		$tableInfo = $this->getSortedTableInfo($this->TableID, false, $db);

		$db->query('SELECT * FROM ' . $db->escape($DataTable) . ' WHERE ID=' . intval($ID));
		if($db->next_record()){
			$regs = array();
			foreach($tableInfo as $cur){
				if(preg_match('/(.+?)_(.*)/', $cur["name"], $regs)){
					if($regs[1] != "OF"){
						$name = $regs[2];
						if($regs[1] == "object"){
							$name = "we_object_" . $name;
						}
//						if($regs[1] == "multiobject"){
//							$this->elements[$name]["class"] = $db->f($tableInfo[$i]["name"]);
//						}
						if($regs[1] == "img"){
							$this->elements[$name]["bdid"] = $db->f($cur["name"]);
						}
						$this->elements[$name]["dat"] = $db->f($cur["name"]);
						$this->elements[$name]["type"] = $regs[1];
						$this->elements[$name]["len"] = $cur["len"];
					}
				}
			}
		}
	}

	function canMakeNew(){
		if($_SESSION['perms']['ADMINISTRATOR']){
			return true;
		}
		$ac = $this->getAllowedClasses();
		return count($ac);
	}

	function getPossibleWorkspaces($ClassWs, $all = false){
		if(!$ClassWs){
			$ClassWs = f('SELECT Workspaces FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), 'Workspaces', $this->DB_WE);
		}
		$userWs = get_ws(FILE_TABLE);
// wenn User Admin ist oder keine Workspaces zugeteilt wurden
		if($_SESSION['perms']['ADMINISTRATOR'] || ((!$userWs) && $all)){
// alle ws, welche in Klasse definiert wurden und deren Unterordner zur?ckgeben
//$foo = makeArrayFromCSV($ClassWs);
			$paths = id_to_path($ClassWs, FILE_TABLE, $this->DB_WE, false, true);
			if(count($paths) > 0){
				$where = array();
				if(is_array($paths)){
					foreach($paths as $path){
						if($path != '/'){
							$where[] = 'Path LIKE "' . $path . '/%" OR Path = "' . $path . '"';
						}
					}
				}
				$where = (empty($where) ? '' : ' AND (' . implode(' OR ', $where) . ')');

				$this->DB_WE->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE IsFolder=1' . $where . ' ORDER BY Path');
				while($this->DB_WE->next_record()) {
					$ClassWs .= $this->DB_WE->f('ID') . ',';
				}
				if($ClassWs && substr($ClassWs, 0, 1) != ','){
					$ClassWs = ',' . $ClassWs;
				}
			}
//$foo = pushChildsFromArr($foo,FILE_TABLE,1);
//return makeCSVFromArray($foo);
		} else{
// alle UserWs, welche sich in einem der ClassWs befinden zur�ckgeben
			$userWsArr = makeArrayFromCSV($userWs);
			$out = array();
			foreach($userWsArr as $ws){
				if(in_workspace($ws, $ClassWs, FILE_TABLE, $this->DB_WE)){
					$out[] = $ws;
				}
			}
			$paths = id_to_path($out, FILE_TABLE, $this->DB_WE, false, true);
			if(count($paths) > 0){
				$ClassWs = '';
				$where = array();
				foreach($paths as $path){
					if($path != '/'){
						$where [] = "Path LIKE '" . $this->DB_WE->escape($path) . "/%' OR Path = '" . $this->DB_WE->escape($path) . "'";
					}
				}
				$where = (empty($where) ? '' : ' AND (' . implode(' OR ', $where) . ')');
				$this->DB_WE->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE IsFolder=1' . $where . ' ORDER BY Path');
				while($this->DB_WE->next_record()) {
					$ClassWs .= $this->DB_WE->f('ID') . ',';
				}
				if($ClassWs && substr($ClassWs, 0, 1) != ','){
					$ClassWs = ',' . $ClassWs;
				}
			}
		}
		return $ClassWs;
	}

	function formWorkspaces(){
		list($ws, $ts) = getHash('SELECT Workspaces,Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);

		$values = getHashArrayFromCSV($this->getPossibleWorkspaces($ws), '', $this->DB_WE);
		foreach($values as $id => $val){
			if(!weFileExists($id))
				unset($values[$id]);
		}
//    remove not existing workspaces and templates
		$arr = makeArrayFromCSV($this->Workspaces);
		$tmpls = makeArrayFromCSV($this->Templates);

		$newArr = array();
		$newTmpls = array();
//$newDefaultArr = array();
		foreach($arr as $nr => $id){
			if(weFileExists($id)){
				$newArr[] = $id;
				$newTmpls[] = (isset($tmpls[$nr]) ? $tmpls[$nr] : '');
			}
		}

		$this->Workspaces = makeCSVFromArray($newArr, true);
		$this->Templates = makeCSVFromArray($newTmpls, true);

		$arr = makeArrayFromCSV($this->ExtraWorkspaces);
		$newArr = array();
		foreach($arr as $nr => $id){
			if(weFileExists($id))
				$newArr[] = $id;
		}
		$this->ExtraWorkspaces = makeCSVFromArray($newArr, true);

		$arr = makeArrayFromCSV($this->Workspaces);
		foreach($arr as $nr => $id){
			if(isset($values[$id]))
				unset($values[$id]);
		}
		if(count($values) < 1){
			$addbut = '';
		} else{
			$textname = md5(uniqid(__FUNCTION__, true));
//$idname = md5(uniqid(rand(), 1));
			$foo = array("" => g_l('global', "[add_workspace]"));
			foreach($values as $key => $val){
				$foo[$key] = $val;
			}
			$addbut = we_html_tools::htmlSelect($textname, $foo, 1, '', false, 'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'add_workspace\',this.options[this.selectedIndex].value);"');
		}
		$obj = new MultiDirAndTemplateChooser(450, $this->Workspaces, 'del_workspace', $addbut, get_ws(FILE_TABLE), $this->Templates, "we_" . $this->Name . "_Templates", $ts, get_ws(TEMPLATES_TABLE));

// Bug Fix #207
		$obj->isEditable = true; //$this->userIsCreator();

		return $obj->get();
	}

	function getTemplateFromWs($wsID){
		$foo = getHash('SELECT Templates,Workspaces FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);

		$mwsp = id_to_path($wsID, FILE_TABLE, $this->DB_WE);

		$tarr = makeArrayFromCSV($foo["Templates"]);
		$warr = makeArrayFromCSV($foo["Workspaces"]);
		$pos = getArrayKey($wsID, $warr);
		if($pos == ""){
			foreach($warr as $wsi){
				$wsp = id_to_path($wsi, FILE_TABLE, $this->DB_WE);
				if(substr($mwsp, 0, strlen($wsp)) == $wsp){
					$pos = getArrayKey($wsi, $warr);
					break;
				}
			}
		}
		return $tarr[$pos];
	}

	function add_workspace($id){
		//$ExtraWorkspaces = makeArrayFromCSV($this->ExtraWorkspaces);
		$workspaces = makeArrayFromCSV($this->Workspaces);
		$templates = makeArrayFromCSV($this->Templates);
		//$extraTemplates = makeArrayFromCSV($this->ExtraTemplates);

		if(!in_array($id, $workspaces)){
			$workspaces[] = $id;
			$tid = $this->getTemplateFromWs($id);
			$templates[] = $tid;
			$this->Workspaces = makeCSVFromArray($workspaces, true);
			$this->Templates = makeCSVFromArray($templates, true);
		}
	}

	function del_workspace($id){
		$workspaces = makeArrayFromCSV($this->Workspaces);
		$Templates = makeArrayFromCSV($this->Templates);
		foreach($workspaces as $key => $val){
			if($val == $id){
				unset($workspaces[$key]);
				unset($Templates[$key]);
				break;
			}
		}
		$tempArr = array();

		foreach($workspaces as $ws){
			$tempArr[] = $ws;
		}

		$this->Workspaces = makeCSVFromArray($tempArr, true);

		$tempArr = array();

		foreach($Templates as $t){
			$tempArr[] = $t;
		}

		$this->Templates = makeCSVFromArray($tempArr, true);
	}

	function ws_from_class(){
		$foo = getHash('SELECT Workspaces,Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);
		$this->Workspaces = $foo["Workspaces"];
		$this->Templates = $foo["Templates"];
		$this->ExtraTemplates = "";
		$this->ExtraWorkspaces = "";
		$this->ExtraWorkspacesSelected = "";
	}

	function formExtraWorkspaces(){
		list($ws, $ts) = getHash('SELECT Workspaces,Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);


// values bekommen aller workspaces, welche hinzugef�gt werden d�rfen.
		$values = getHashArrayFromCSV($this->getPossibleWorkspaces($ws, true), '', $this->DB_WE);
		foreach($values as $id => $val){
			if(!weFileExists($id))
				unset($values[$id]);
		}

		$arr = makeArrayFromCSV($this->ExtraWorkspaces);
		foreach($arr as $id){
			if(isset($values[$id]) || (!weFileExists($id)))
				unset($values[$id]);
		}

		if(count($values) < 1){
			$addbut = "";
		} else{
			$textname = md5(uniqid(__FUNCTION__, true));
			$idname = md5(uniqid(__FUNCTION__, true));
			$foo = array("" => g_l('global', "[add_workspace]"));
			foreach($values as $key => $val){
				$foo[$key] = $val;
			}
			$addbut = we_html_tools::htmlSelect($textname, $foo, 1, "", false, 'onChange="_EditorFrame.setEditorIsHot(true);we_cmd(\'add_extraworkspace\',this.options[this.selectedIndex].value);"');
		}

		$obj = new MultiDirAndTemplateChooser(450, $this->ExtraWorkspaces, "del_extraworkspace", $addbut, get_ws(FILE_TABLE), $this->ExtraTemplates, "we_" . $this->Name . "_ExtraTemplates", $ts, get_ws(TEMPLATES_TABLE));
		$obj->CanDelete = true;
		return $obj->get();
	}

	function add_extraWorkspace($id){
		$ExtraWorkspaces = makeArrayFromCSV($this->ExtraWorkspaces);
		/* $workspaces = makeArrayFromCSV($this->Workspaces);
		  $templates = makeArrayFromCSV($this->Templates); */
		$extraTemplates = makeArrayFromCSV($this->ExtraTemplates);

		if(!in_array($id, $ExtraWorkspaces)){
			$ExtraWorkspaces[] = $id;
			$tid = $this->getTemplateFromWs($id);
			$extraTemplates[] = $tid;
			$this->ExtraWorkspaces = makeCSVFromArray($ExtraWorkspaces, true);
			$this->ExtraTemplates = makeCSVFromArray($extraTemplates, true);
		}
	}

	function del_extraWorkspace($id){
		$ExtraWorkspaces = makeArrayFromCSV($this->ExtraWorkspaces);
		$ExtraTemplates = makeArrayFromCSV($this->ExtraTemplates);
		foreach($ExtraWorkspaces as $key => $val){
			if($val == $id){
				unset($ExtraWorkspaces[$key]);
				unset($ExtraTemplates[$key]);
				break;
			}
		}
		$tempArr = array();

		foreach($ExtraWorkspaces as $ws){
			$tempArr[] = $ws;
		}

		$this->ExtraWorkspaces = makeCSVFromArray($tempArr, true);

		$tempArr = array();

		foreach($ExtraTemplates as $t){
			$tempArr[] = $t;
		}

		$this->ExtraTemplates = makeCSVFromArray($tempArr, true);
	}

	function getAllowedClasses(){
		return getAllowedClasses($this->DB_WE);
	}

	function getTemplateFromWorkspace($wsArr, $tmplArr, $parentID, $mode = 0){
		foreach($wsArr as $key => $val){
			if($mode){
				if($val == $parentID){
					return $tmplArr[$key];
				}
			} elseif(in_workspace($parentID, $val)){
				return $tmplArr[$key];
			}
		}
		return 0;
	}

	function getTemplateID($parentID){
		$wsArr = makeArrayFromCSV($this->Workspaces);
		$tmplArr = makeArrayFromCSV($this->Templates);
		$wsArrExtra = makeArrayFromCSV($this->ExtraWorkspaces);
		$tmplArrExtra = makeArrayFromCSV($this->ExtraTemplates);


		$tid = $this->getTemplateFromWorkspace($wsArr, $tmplArr, $parentID, 1);
		if(!$tid){
			$tid = $this->getTemplateFromWorkspace($wsArrExtra, $tmplArrExtra, $parentID, 1);
		}
		if(!$tid){
			$tid = $this->getTemplateFromWorkspace($wsArr, $tmplArr, $parentID, 0);
		}
		if(!$tid){
			$tid = $this->getTemplateFromWorkspace($wsArrExtra, $tmplArrExtra, $parentID, 0);
		}
		if(!$tid){
			if(count($tmplArr)){
				$tid = $tmplArr[0];
			}
		}
		if(!$tid){
			$foo = makeArrayFromCSV(f('SELECT Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . (int) $this->TableID, 'Templates', new DB_WE()));
			if(count($foo)){
				$tid = $foo[0];
			}
		}
		return $tid;
	}

	function geFieldValue($t, $f){
		$elem = $this->getElement($t);
		switch($f){
			case "href":
				$hrefArr = $elem ? unserialize($elem) : array();
				if(!is_array($hrefArr)){
					$hrefArr = array();
				}
				return parent::getHrefByArray($hrefArr);
			case "link":
				$link = $elem ? unserialize($elem) : array();
				if(is_array($link)){
					$img = new we_imageDocument();
					return parent::getLinkContent($link, 0, '', $this->DB_WE, $img);
				} else{
					return '';
				}
			case "meta":
				if(!$this->DefArray){
					$this->DefArray = $this->getDefaultValueArray();
				}
				$vals = $this->DefArray["meta_" . $t]["meta"];
				return $vals[$this->getElement($t)];
			default:
				return $elem;
		}
	}

	function setTitleAndDescription(){
		$fields = array('Description' => 'DefaultDesc', 'Title' => 'DefaultTitle', 'Keywords' => 'DefaultKeywords');
		$foo = getHash('SELECT ' . implode(',', $fields) . ' FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);

		foreach($fields as $key => $field){
			if(isset($foo[$field]) && $foo[$field]){
				$regs = explode('_', $foo[$field], 2);
				if(isset($regs[0]) && $regs[0] !== '' && isset($regs[1]) && $regs[1] !== ''){
					$elem = $this->geFieldValue($regs[1], $regs[0]);
					$this->setElement($key, $elem);
				}
			}
		}
	}

	function setUrl(){
		$foo = getHash('SELECT DefaultUrl,DefaultUrlfield0, DefaultUrlfield1, DefaultUrlfield2, DefaultUrlfield3 FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);
		$max = 3;
		$urlfield = array();
		if(isset($foo["DefaultUrl"]) && $foo["DefaultUrl"]){
			$regs = array();
			$text = $foo["DefaultUrl"];
			for($i = 0; $i <= $max; ++$i){
				$cur = '';
				if(isset($foo['DefaultUrlfield' . $i]) && $foo['DefaultUrlfield' . $i]){
					preg_match('/(.+?)_(.*)/', $foo['DefaultUrlfield' . $i], $regs);
					$cur = $urlfield[$i] = (isset($regs[1]) && $regs[1] !== '' && isset($regs[2]) && $regs[2] !== '' ?
							$this->geFieldValue($regs[2], $regs[1]) : '');
				}
				if($i > 0 && preg_match('/%urlfield' . $i . '([^%]*)%/', $text, $regs)){
					$anz = (!$regs[1] ? 64 : abs($regs[1]));
					$text = preg_replace('/%urlfield' . $i . '[^%]*%/', substr($cur, 0, $anz), $text);
				}
			}
			if(!isset($urlfield[0]) || $urlfield[0] == ''){
				$urlfield[0] = time();
			}

			if(preg_match('/%urlunique([^%]*)%/', $text, $regs)){
				$anz = (!$regs[1] ? 16 : abs($regs[1]));
				$unique = substr(md5(uniqid(__FUNCTION__, true)), 0, min($anz, 32));
				$text = preg_replace('/%urlunique[^%]*%/', $unique, $text);
			}

			$text = str_replace(
				array('%ID%', '%locale%', '%language%', '%country%',)
				, array($this->ID, $this->Language, substr($this->Language, 0, 2), substr($this->Language, 4, 2),)
				, $text);
			$text = str_replace(
				array('%d%', 'j%', '%m%', '%y%', '%Y%', '%n%', '%g%', '%G%', '%h%', '%H%',)
				, array(date("d", $this->CreationDate), date("j", $this->CreationDate), date("m", $this->CreationDate), date("y", $this->CreationDate), date("Y", $this->CreationDate), date("n", $this->CreationDate), date("g", $this->CreationDate), date("G", $this->CreationDate), date("h", $this->CreationDate), date("H", $this->CreationDate),)
				, $text);

			$text = str_replace(
				array('%Md%', '%Mj%', '%Mm%', '%My%', '%MY%', '%Mn%', '%Mg%', '%MG%', '%Mh%', '%MH%',)
				, array(date("d", $this->ModDate), date("j", $this->ModDate), date("m", $this->ModDate), date("y", $this->ModDate), date("Y", $this->ModDate), date("n", $this->ModDate), date("g", $this->ModDate), date("G", $this->ModDate), date("h", $this->ModDate), date("H", $this->ModDate))
				, $text);

			$text = str_replace(
				array('%Fd%', '%Fj%', '%Fm%', '%Fy%', '%FY%', '%Fn%', '%Fg%', '%FG%', '%Fh%', '%FH%',)
				, array(date("d", $urlfield[0]), date("j", $urlfield[0]), date("m", $urlfield[0]), date("y", $urlfield[0]), date("Y", $urlfield[0]), date("n", $urlfield[0]), date("g", $urlfield[0]), date("G", $urlfield[0]), date("h", $urlfield[0]), date("H", $urlfield[0]))
				, $text);
			$text = str_replace('%DirSep%', '/', $text);

			if(strpos($text, '%Parent%') !== false){
				$fooo = getHash('SELECT Text FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->ParentID), $this->DB_WE);
				if(isset($fooo["Text"]) && $fooo["Text"]){
					$text = str_replace('%Parent%', $fooo["Text"], $text);
				}
			}
			if(strpos($text, '%PathIncC%') !== false){
				$zwtext = ltrim(str_replace($this->Text, '', $this->Path), '/');
				$text = str_replace('%PathIncC%', $zwtext, $text);
			}
			if(strpos($text, '%PathNoC%') !== false){
				$zwtext = str_replace($this->Text, '', $this->Path);
				$classN = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), 'Path', $this->DB_WE);
				$zwtext = ltrim(str_replace($classN, '', $zwtext), '/');
				$text = str_replace('%PathNoC%', $zwtext, $text);
			}
			//remove duplicate "//" which will produce errors
			$text = str_replace(array(' ', '//'), array('-', '/'), $text);
			$text = (URLENCODE_OBJECTSEOURLS) ?
				str_replace('%2F', '/', urlencode($text)) :
				preg_replace('~[^0-9a-zA-Z/._-]~', '', correctUml($text));
			$this->Url = substr($text, 0, 256);
		} else{
			$this->Url = '';
		}
	}

	function insertAtIndex(){
		$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE OID=' . $this->ID);
		if(!$this->IsSearchable){
			return true;
		}

		$this->setTitleAndDescription();
		$this->resetElements();
		$text = '';
		while((list($k, $v) = $this->nextElement(""))) {
			if(isset($v["dat"]) && !empty($v["dat"])){
				switch(isset($v['type']) ? $v['type'] : ''){
					default:
					case 'object':
					case 'multiobject':
					case 'language':
					case 'href':
						//not handled
						break;
					case 'date':
						$text .= ' ' . date(g_l('date', '[format][default]'), $v["dat"]);
						break;
					case 'int':
						$text.=' ' . intval($v["dat"]);
						break;
					case 'float':
						$text.=' ' . floatval($v["dat"]);
						break;

					case 'meta'://FIXME: meta returns the key not the value
					case 'input':
					case 'txt':
					case 'text':
						if(strpos($v["dat"], 'a:') === 0){
							//link/href
							$tmp = @unserialize($v["dat"]);
							if($tmp && isset($tmp['text'])){
								$text .= ' ' . $tmp['text'];
							}
						} else{
							$text .= ' ' . $v["dat"];
						}
						break;
				}
			}
		}
		$maxDB = min(1000000, getMaxAllowedPacket($this->DB_WE) - 1024);
		$text = substr(preg_replace(array("/\n+/", '/  +/'), ' ', trim(strip_tags($text))), 0, $maxDB);

		if(empty($text)){
			//no need to keep an entry without relevant data in the index
			return true;
		}

		$ws = makeArrayFromCSV($this->Workspaces);
		$ws2 = makeArrayFromCSV($this->ExtraWorkspacesSelected);
		foreach($ws2 as $w){
			$ws[] = $w;
		}
		$ws = array_unique($ws);

		if(empty($ws)){
			return $this->DB_WE->query('INSERT INTO ' . INDEX_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'OID' => $this->ID,
						'Text' => $text,
						'Workspace' => '',
						'WorkspaceID' => 0,
						'Category' => $this->Category,
						'ClassID' => $this->TableID,
						'Title' => $this->getElement("Title"),
						'Description' => $this->getElement("Description"),
						'Path' => $this->Text,
						'Language' => $this->Language
			)));
		}

		foreach($ws as $w){
			$wsPath = id_to_path($w, FILE_TABLE, $this->DB_WE);
			if((strlen($wsPath) > 0) || (intval($w) == 0)){
				if($w == '0'){
					$wsPath = '/';
				}
				if(!$this->DB_WE->query(
						'INSERT INTO ' . INDEX_TABLE . ' SET ' . we_database_base::arraySetter(array(
							'OID' => $this->ID,
							'Text' => $text,
							'Workspace' => $wsPath,
							'WorkspaceID' => $w,
							'Category' => $this->Category,
							'ClassID' => $this->TableID,
							'Title' => $this->getElement("Title"),
							'Description' => $this->getElement("Description"),
							'Path' => $this->Text,
							'Language' => $this->Language
					)))){
					return false;
				}
			}
		}
		return true;
	}

	function setLanguage($language = ''){
		$this->Language = $language ? $language : $this->Language;
		$this->DB_WE->query('UPDATE ' . OBJECT_X_TABLE . $this->TableID . ' SET OF_Language="' . $this->Language . '" WHERE OF_ID=' . intval($this->ID));
	}

	private function setPublishTime($time){
		$this->Published = $time;
		return
			$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . ' SET Published=' . $time . ' WHERE ID=' . $this->ID) &&
			$this->DB_WE->query('UPDATE ' . OBJECT_X_TABLE . $this->TableID . ' SET OF_Published=' . $time . ' WHERE OF_ID=' . $this->ID);
	}

	function markAsPublished(){
		return $this->setPublishTime(time());
	}

	function markAsUnPublished(){
		return $this->setPublishTime(0);
	}

	protected function i_convertElemFromRequest($type, &$v, $k){
		if(!$type){
			foreach(array_keys($this->DefArray) as $n){
				$regs = explode('_', $n, 2);
				if(isset($regs[0])){
					$testtype = $regs[0];
					unset($regs[0]);
					if(isset($regs[1])){
						$fieldname = $regs[1];
						if($k == $fieldname){
							$type = $testtype;
							break;
						}
					}
				}
			}
		}
		parent::i_convertElemFromRequest($type, $v, $k);
	}

	function we_initSessDat($sessDat){
		parent::we_initSessDat($sessDat);
		$this->DefArray = $this->getDefaultValueArray();
		$this->i_objectFileInit();
	}

	function we_ImportSave(){
		$this->Icon = 'objectFile.gif';
		if(!parent::we_save(1) || !$this->ObjectID){
			return false;
		}
		$this->wasUpdate = 1;
		return $this->i_saveTmp();
	}

	function correctWorkspaces(){
		if($this->Workspaces){
			$ws = makeArrayFromCSV($this->Workspaces);
			$newWs = array();
			foreach($ws as $wsID){
				if(f('SELECT 1 AS a FROM ' . FILE_TABLE . ' WHERE ID=' . intval($wsID) . ' AND IsFolder=1', 'a', $this->DB_WE)){
					$newWs[] = $wsID;
				} else if($wsID == 0 && strlen($wsID) == 1){
					$newWs[] = $wsID;
				}
			}
			$this->Workspaces = makeCSVFromArray($newWs, true);
		}
		if($this->ExtraWorkspaces){
			$ws = makeArrayFromCSV($this->ExtraWorkspaces);
			$newWs = array();
			foreach($ws as $wsID){
				if(f('SELECT 1 AS a FROM ' . FILE_TABLE . ' WHERE ID=' . intval($wsID) . ' AND IsFolder=1', 'a', $this->DB_WE)){
					$newWs[] = $wsID;
				}
			}
			$this->ExtraWorkspaces = makeCSVFromArray($newWs, true);
		}
		if($this->ExtraWorkspacesSelected){
			$ws = makeArrayFromCSV($this->ExtraWorkspacesSelected);
			$newWs = array();
			foreach($ws as $wsID){
				if(f('SELECT 1 AS a FROM ' . FILE_TABLE . ' WHERE ID=' . intval($wsID) . ' AND IsFolder=1', 'a', $this->DB_WE)){
					$newWs[] = $wsID;
				}
			}
			$this->ExtraWorkspacesSelected = makeCSVFromArray($newWs, true);
		}
	}

	function i_pathNotValid(){
		return parent::i_pathNotValid() || ($this->ParentID == 0 || $this->ParentPath == '/' || strpos($this->Path, $this->RootDirPath) !== 0);
	}

	public function we_save($resave = 0, $skipHook = 0){
		$this->errMsg = '';

		if($this->i_pathNotValid()){
			return false;
		}

		$foo = getHash('SELECT strOrder,DefaultValues,DefaultTriggerID FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);
		$dv = $foo['DefaultValues'] ? unserialize($foo["DefaultValues"]) : array();

		foreach($this->elements as $n => $elem){
			if(isset($elem["type"]) && $elem["type"] == "text"){
				if(isset($dv["text_$n"]["xml"]) && $dv["text_$n"]["xml"] == "on"){
//$elem["dat"] = we_xhtmlConverter::correct_HTML_source($elem["dat"],true);
					$this->elements[$n] = $elem;
				}
			}
		}
		if($this->canHaveVariants()){
			weShopVariants::correctModelFields($this);
		}
		if(!$this->TriggerID){
			$this->TriggerID = f('SELECT TriggerID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->ParentID), 'TriggerID', $this->DB_WE);
			if(!$this->TriggerID){
				$this->TriggerID = $foo["DefaultTriggerID"];
			}
		}
		$_resaveWeDocumentCustomerFilter = true;
		$this->correctWorkspaces();

		if($skipHook == 0){
			$hook = new weHook('preSave', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		if((!$this->ID || $resave)){
			$_resaveWeDocumentCustomerFilter = false;
			if((!parent::we_save($resave, 1)) || (!$this->ObjectID) || ($resave) || (!$this->we_republish())){
				return false;
			}
		}
		$this->ModDate = time();
		$this->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : 0;
		$this->wasUpdate = 1;

		$this->setUrl();

		if($resave == 0){
			if($this->ID){
				we_history::insertIntoHistory($this);
			}
			if($_resaveWeDocumentCustomerFilter){
				$this->resaveWeDocumentCustomerFilter();
			}
		}

		if(!$this->Published){
			if(!we_root::we_save(1)){
				return false;
			}
			if(we_temporaryDocument::isInTempDB($this->ID, $this->Table, $this->DB_WE)){
				we_temporaryDocument::delete($this->ID, $this->Table, $this->DB_WE);
			}
		}
		$a = $this->i_saveTmp();

// version
		if($this->ContentType == "objectFile" && defined('VERSIONING_OBJECT') && VERSIONING_OBJECT){
			$version = new weVersions();
			$version->save($this);
		}
		if(LANGLINK_SUPPORT && isset($_REQUEST["we_" . $this->Name . "_LanguageDocID"]) && $_REQUEST["we_" . $this->Name . "_LanguageDocID"] != 0){
			$this->setLanguageLink($_REQUEST["we_" . $this->Name . "_LanguageDocID"], 'tblObjectFile', false, true);
		} else{
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, false);
		}
// hook
		if($skipHook == 0){
			$hook = new weHook('save', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		return $a;
	}

	function ModifyPathInformation($parentID){
		$this->setParentID($parentID);
		$this->Path = $this->getPath();
		$this->wasUpdate = 1;
		$this->i_savePersistentSlotsToDB('Text,Path,ParentID');
		$this->i_saveTmp();
		$this->insertAtIndex();
		$this->modifyChildrenPath(); // only on folders, because on other classes this function is empty
	}

	function hasWorkspaces(){
		return f('SELECT Workspaces FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), 'Workspaces', $this->DB_WE);
	}

	function setTypeAndLength(){
		if($this->TableID){
			$DataTable = OBJECT_X_TABLE . $this->TableID;
			$db = $this->DB_WE;
			$tableInfo = $db->metadata($DataTable);
			$regs = array();
			foreach($tableInfo as $cur){
				if(preg_match('/(.+?)_(.*)/', $cur["name"], $regs)){
					if($regs[1] != 'OF'){
						$name = $regs[2];
						$this->elements[$name]["type"] = $regs[1];
						$this->elements[$name]["len"] = $cur["len"];
					}
				}
			}
		}
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		switch($from){
			case we_class::LOAD_SCHEDULE_DB:
				$sessDat = f('SELECT SerializedData FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($this->ID) . " AND ClassName='" . $this->ClassName . "' AND Was=" . we_schedpro::SCHEDULE_FROM, 'SerializedData', $this->DB_WE);
				if($sessDat){
					$this->i_getPersistentSlotsFromDB(/* "Path,Text,ParentID,CreatorID,Published,ModDate,Owners,ModifierID,RestrictOwners,OwnersReadOnly,IsSearchable,Charset,Url,TriggerID" */);
					if($this->i_initSerializedDat(unserialize(substr_compare($sessDat, 'a:', 0, 2) == 0 ? $sessDat : gzuncompress($sessDat)))){

						//make sure at least TableID is set from db
						//and Published as well #5742
						$this->i_getPersistentSlotsFromDB('TableID,Published');
						$this->i_getUniqueIDsAndFixNames();
						break;
					}
				}
				$from = we_class::LOAD_MAID_DB;

			case we_class::LOAD_MAID_DB:
				parent::we_load($from);
				break;
			case we_class::LOAD_TEMP_DB:
				$sessDat = unserialize(we_temporaryDocument::load($this->ID, $this->Table, $this->DB_WE));
				if($sessDat){
//fixed: at least TableID must be fetched
					$this->i_getPersistentSlotsFromDB(/* "TableID,Path,Text,ParentID,CreatorID,Published,ModDate,Owners,ModifierID,RestrictOwners,OwnersReadOnly,IsSearchable,Charset,Url,TriggerID" */);
//overwrite with new data
					$this->i_initSerializedDat($sessDat, false);
//make sure at least TableID is set from db
//and Published as well #5742
					$this->i_getPersistentSlotsFromDB('TableID,Published');
					$this->i_getUniqueIDsAndFixNames();
				} else{
					$this->we_load(we_class::LOAD_MAID_DB);
				}
				$this->setTypeAndLength();
				break;
			case we_class::LOAD_REVERT_DB: //we_temporaryDocument::revert gibst nicht mehr siehe #5789
				$this->we_load(we_class::LOAD_TEMP_DB);
				$this->setTypeAndLength();
				break;
		}
		$this->loadSchedule();
		$this->setTitleAndDescription();
		$this->i_getLinkedObjects();
		$this->initVariantDataFromDb();
// init Customer Filter !!!!
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$this->initWeDocumentCustomerFilterFromDB();
		}
	}

	function i_getUniqueIDsAndFixNames(){
		if(is_array($this->DefArray) && count($this->DefArray)){
			$newDefArr = $this->getDefaultValueArray();
			foreach($newDefArr as $n => $v){
				if(is_array($v) && isset($v["uniqueID"])){
					if(($oldName = $this->i_DefArrayNameNotEqual($n, $v["uniqueID"]))){
						$foo = explode("_", $n);
						unset($foo[0]);
						$nn = implode("_", $foo);
						$foo = explode("_", $oldName);
						unset($foo[0]);
						$no = implode("_", $foo);
						$this->elements[$nn] = isset($this->elements[$no]) ? $this->elements[$no] : '';
						unset($this->elements[$no]);
					}
				}
			}
		}
	}

	function i_DefArrayNameNotEqual($name, $uniqueID){
		foreach($this->DefArray as $n => $v){
			if(is_array($v) && isset($v["uniqueID"])){
				if($v["uniqueID"] == $uniqueID){
					return ($n == $name) ? '' : $n;
				}
			}
		}
		return '';
	}

	public function we_publish($DoNotMark = false, $saveinMainDB = true, $skipHook = 0){
		if($skipHook == 0){
			$hook = new weHook('prePublish', '', array($this));
			$ret = $hook->executeHook();
//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		$oldUrl = f('SELECT Url FROM ' . $this->Table . ' WHERE ID=' . $this->ID, 'Url', $this->DB_WE);
		if($saveinMainDB && !we_root::we_save(1)){
			return false;
		}
		if($DoNotMark == false){
			if(!$this->markAsPublished()){
				return false;
			}
		}
		//hook
		if($skipHook == 0){
			$hook = new weHook('publish', '', array($this));
			$ret = $hook->executeHook();
//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		we_temporaryDocument::delete($this->ID, $this->Table, $this->DB_WE);
		if($oldUrl != $this->Url){
			$this->rewriteNavigation();
		}
//clear navigation cache to see change if object in navigation #6916
		weNavigationCache::clean(true);

		return $this->insertAtIndex();
	}

	public function we_unpublish($skipHook = 0){
		if(!$this->ID || !$this->markAsUnPublished()){
			return false;
		}

		/* version */
		if($this->ContentType == 'objectFile' && defined('VERSIONING_OBJECT') && VERSIONING_OBJECT){
			$version = new weVersions();
			$version->save($this, 'unpublished');
		}
		/* hook */
		if($skipHook == 0){
			$hook = new weHook('unpublish', '', array($this));
			$ret = $hook->executeHook();
//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
//clear navigation cache to see change if object in navigation #6916
		weNavigationCache::clean(true);

		return $this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE OID=' . $this->ID);
	}

	public function we_delete(){
		if(!$this->ID){
			return false;
		}
// Bug 2892, siehe auch we_delete_fn.inc.php
		$this->DB_WE->query('SELECT ID FROM ' . OBJECT_TABLE);
		$foo = $this->DB_WE->getAll(true);
		foreach($foo as $testclassID){
			if($this->DB_WE->isColExist(OBJECT_X_TABLE . $testclassID, "object_" . $this->TableID)){
				$this->DB_WE->query('UPDATE ' . OBJECT_X_TABLE . $testclassID . ' SET object_' . $this->TableID . '="0" WHERE object_' . $this->TableID . '= ' . $this->ID);
			}
		}

		return parent::we_delete();
	}

	public function we_republish($rebuildMain = true){
		return ($this->Published ?
				$this->we_publish(true, $rebuildMain) :
				$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE OID=' . $this->ID)
			);
	}

	function i_objectFileInit($makeSameNewFlag = false){
		if($this->ID){

			$this->setRootDirID();
			$oldTableID = f('SELECT TableID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $this->ID, "TableID", $this->DB_WE);
			if($oldTableID != $this->TableID){
				$this->resetParentID();
			}
			$this->DB_WE->query('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->TableID);
			if($this->DB_WE->next_record()){
				if($this->DB_WE->f("DefaultValues")){
					$vals = unserialize($this->DB_WE->f("DefaultValues"));
					if(isset($vals["WE_CSS_FOR_CLASS"])){
						$this->CSS = $vals["WE_CSS_FOR_CLASS"];
					}
				}
			}
		} else if(isset($GLOBALS["we_EDITOR"]) && $GLOBALS["we_EDITOR"] && $this->DefaultInit == false && (!$this->ID)){
			if(!$this->TableID){
				$ac = $this->getAllowedClasses();
				$this->AllowedClasses = makeCSVFromArray($ac);
				$this->TableID = $ac[0];
			}
			if($this->TableID){
				$this->setRootDirID();
				if(!$makeSameNewFlag){
					$this->resetParentID();
				}
				$this->restoreDefaults($makeSameNewFlag);
			}
		} else if(isset($GLOBALS["we_EDITOR"]) && $GLOBALS["we_EDITOR"] && (!$this->ID)){
			$_initWeDocumentCustomerFilter = false;
			if(!$this->ParentID){
				$_initWeDocumentCustomerFilter = true;
			}

			if($this->Charset == "" && isset($this->DefArray['elements']['Charset'])){
				$this->Charset = $this->DefArray['elements']['Charset']['dat'];
			}

			$this->setRootDirID();
			/*
			  if(!isset($this->ParentID)) {
			  $this->resetParentID();
			  }
			 */
			$this->checkAndCorrectParent();
			if($_initWeDocumentCustomerFilter){
// get customerFilter of parent Folder
				$_tmpFolder = new we_class_folder();
				$_tmpFolder->initByID($this->rootDirID, $this->Table);
				$this->documentCustomerFilter = $_tmpFolder->documentCustomerFilter;
				unset($_tmpFolder);
			}
		}
	}

	protected function i_set_PersistentSlot($name, $value){
		if(in_array($name, $this->persistent_slots)){
			$this->$name = $value;
			return;
		}
		switch($name){
			case 'Templates_0':

				$this->Templates = '';
				$cnt = count(makeArrayFromCSV($this->Workspaces));
				for($i = 0; $i < $cnt; ++$i){
					$this->Templates .= $_REQUEST['we_' . $this->Name . '_Templates_' . $i] . ',';
				}
				if($this->Templates){
					$this->Templates = ',' . $this->Templates;
				}
				break;
			case 'we_' . $this->Name . '_ExtraTemplates_0':
				$this->ExtraTemplates = '';
				$cnt = count(makeArrayFromCSV($this->ExtraWorkspaces));
				for($i = 0; $i < $cnt; ++$i){
					$this->ExtraTemplates .= $_REQUEST['we_' . $this->Name . '_ExtraTemplates_' . $i] . ',';
				}
				if($this->ExtraTemplates){
					$this->ExtraTemplates = ',' . $this->ExtraTemplates;
				}
				break;
		}
	}

	function i_getLinkedObjects(){
		if(!$this->TableID){
			return;
		}
		$linkObjects = array();
		$tableInfo = $this->getSortedTableInfo($this->TableID, false, $this->DB_WE);
		$regs = array();
		foreach($tableInfo as $cur){
			if(preg_match('/(.+?)_(.*)/', $cur["name"], $regs)){
				if($regs[1] != "OF"){
					if($regs[1] == "object"){
						$id = $this->getElement('we_' . $cur["name"]);
						if($id)
							$linkObjects[] = $id;
					}
				}
			}
		}
		foreach($linkObjects as $id){
			$tmpObj = new we_objectFile();
			$tmpObj->initByID($id, OBJECT_FILES_TABLE, 0);
			foreach($tmpObj->elements as $n => $elem){
				if($elem["type"] != "object" && $n != "Title" && $n != "Description"){
					if(!isset($this->elements[$n])){
						$this->elements[$n] = $elem;
					}
				}
			}
		}
	}

	protected function i_getContentData(/* $loadBinary = 0 */){
		if(!$this->TableID){
			return;
		}
		$ID = $this->ObjectID;
		$DataTable = OBJECT_X_TABLE . $this->TableID;
		$db = $this->DB_WE;
		$tableInfo = $this->getSortedTableInfo($this->TableID, false, $db);

		$db->query('SELECT * FROM ' . $DataTable . ' WHERE ID=' . $ID);
		if($db->next_record()){
			foreach($tableInfo as $cur){
				$regs = explode('_', $cur["name"], 2);
				if(count($regs) > 1){
					if($regs[0] != "OF"){
						$realname = $regs[1];
						$name = ($regs[0] == 'object' ? 'we_object_' : '') . $realname;
						$this->elements[$name] = array(
							'dat' => $db->f($cur["name"]),
							'type' => $regs[0],
							'len' => $cur["len"]
						);
//						if($regs[0] == "multiobject"){
//							$this->elements[$name]["class"] = $db->f($tableInfo[$i]["name"]);
//						}
						if($regs[0] == "img"){
							$this->elements[$name]["bdid"] = $db->f($cur["name"]);
						}
					}
				}
			}
// add variant data if available
			if(defined('SHOP_TABLE')){
				$fieldname = 'variant_' . WE_SHOP_VARIANTS_ELEMENT_NAME;
				$elementName = WE_SHOP_VARIANTS_ELEMENT_NAME;

				if($db->f($fieldname)){
					$this->elements[$elementName] = array(
						"dat" => $db->f($fieldname),
						"type" => 'variant',
						"len" => strlen($db->f($fieldname))
					);
				}
			}
		}
	}

	protected function i_setText(){
// do nothing here!
	}

	function i_filenameEmpty(){
		return ($this->Text == '');
	}

	function i_filenameNotValid(){
		return preg_match('/[^a-z0-9\._\-]/i', $this->Text);
	}

	function i_filenameNotAllowed(){
		return false;
	}

	function i_filenameDouble(){
		return f('SELECT ID FROM ' . $this->Table . ' WHERE ParentID=' . $this->ParentID . " AND Text='" . escape_sql_query($this->Text) . "' AND ID!=" . intval($this->ID), "ID", $this->DB_WE);
	}

	function i_urlDouble(){
		$this->setUrl();
		return ($this->Url != '' ? f('SELECT ID FROM ' . $this->Table . " WHERE Url='" . escape_sql_query($this->Url) . "' AND ID!=" . intval($this->ID), "ID", new DB_WE()) : false);
	}

	function i_checkPathDiffAndCreate(){
		return true;
	}

	function i_scheduleToBeforeNow(){
		return (defined('SCHEDULE_TABLE') && ($this->To < time() && $this->ToOk));
	}

	function i_publInScheduleTable(){
		return (defined('SCHEDULE_TABLE') ?
				we_schedpro::publInScheduleTable($this, $this->DB_WE) :
				false);
	}

	protected function i_writeDocument(){
		return true; // do nothing;
	}

	function getContentDataFromTemporaryDocs($ObjectID/* , $loadBinary = 0 */){
		$DocumentObject = f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($ObjectID) . ' AND Active=1 AND  DocTable="tblObjectFiles"', 'DocumentObject', $this->DB_WE);
		if($DocumentObject){
			$DocumentObject = unserialize($DocumentObject);
			if(isset($DocumentObject[0]['elements']) && is_array($DocumentObject[0]['elements'])){
				$this->elements = $DocumentObject[0]['elements'];
			}
		}
	}

	function i_saveContentDataInDB(){
		$ctable = OBJECT_X_TABLE . $this->TableID;

		$tableInfo = $this->DB_WE->metadata($ctable);
		$foo = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->TableID, 'DefaultValues', $this->DB_WE);
		$defVal = ($foo ? unserialize($foo) : array());

		if($this->wasUpdate && $this->ExtraWorkspacesSelected){
			$ews = makeArrayFromCSV($this->ExtraWorkspacesSelected);
			$ew = makeArrayFromCSV($this->ExtraWorkspaces);
			$newews = array();
			foreach($ews as $ws){
				if(in_array($ws, $ew)){
					$newews[] = $ws;
				}
			}
			$this->ExtraWorkspacesSelected = makeCSVFromArray($newews, true);
		}
		if(!$this->wasUpdate){
			$this->CreatorID = $this->CreatorID ? $this->CreatorID : (isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0);
		}

		$data = array();
		$regs = array();
		foreach($tableInfo as $cur){
			$regs = explode('_', $cur['name'], 2);
			if(count($regs) > 1){
				$name = $regs[1];
				if($regs[0] == 'OF'){
					$data[$cur['name']] = (isset($this->$name) ? $this->$name : '');
				} else{
					$name = ($regs[0] == 'object') ? ('we_object_' . $name) : $name;
					$data[$cur['name']] = $this->getElement($name);
				}
			}
		}
		$where = ($this->wasUpdate) ? ' WHERE OF_ID=' . intval($this->ID) : '';
		$ret = (bool) ($this->DB_WE->query(($this->wasUpdate ? 'UPDATE ' : 'INSERT INTO ') . $this->DB_WE->escape($ctable) . ' SET ' . we_database_base::arraySetter($data) . $where));
		$this->ObjectID = ($this->wasUpdate ? $this->ObjectID : $this->DB_WE->getInsertId());
		return $ret;
	}

	private function i_saveTmp(){
		$saveArr = array();
		$this->saveInSession($saveArr);
		if(($this->ModDate > $this->Published) && $this->Published){
			if(!we_temporaryDocument::save($this->ID, $this->Table, $saveArr, $this->DB_WE)){
				return false;
			}
		}
		if($this->ID){
			$this->DB_WE->query('UPDATE ' . OBJECT_X_TABLE . $this->TableID . " SET OF_TEXT='" . $this->Text . "',OF_PATH='" . $this->Path . "' WHERE OF_ID=" . $this->ID);
		}
		return $this->i_savePersistentSlotsToDB('Path,Text,ParentID,CreatorID,ModifierID,RestrictOwners,Owners,OwnersReadOnly,Published,ModDate,ObjectID,IsSearchable,Charset,Url,TriggerID');
	}

	function i_getDocument($includepath = ''){
		$glob = array();
		foreach(array_keys($GLOBALS) as $k){
			if((!preg_match('|^[0-9]|', $k)) && (!preg_match('|[^a-z0-9_+-]|i', $k)) && $k != "_SESSION" && $k != "_GET" && $k != "_POST" && $k != "_REQUEST" && $k != "_SERVER" && $k != "_FILES" && $k != "_SESSION" && $k != "_ENV" && $k != "_COOKIE")
				$glob [] = '$' . $k;
		}
		eval('global ' . implode(',', $glob) . ';'); // globalen Namensraum herstellen.
		unset($glob);

		$we_doc = new we_webEditionDocument();
		$we_doc->elements = $this->elements;
		$we_doc->Templates = $this->Templates;
		$we_doc->ExtraTemplates = $this->ExtraTemplates;
		$we_doc->TableID = $this->TableID;
		$we_doc->CreatorID = $this->CreatorID;
		$we_doc->ModifierID = $this->ModifierID;
		$we_doc->RestrictOwners = $this->RestrictOwners;
		$we_doc->Owners = $this->Owners;
		$we_doc->OwnersReadOnly = $this->OwnersReadOnly;
		$we_doc->Category = $this->Category;
		$we_doc->ObjectID = $this->ObjectID;
		$we_doc->OF_ID = $this->ID;

		$we_doc->InWebEdition = false;
		$we_include = $includepath ? $includepath : $we_doc->TemplatePath;
		ob_start();
		include($we_include);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	protected function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		if(count($_REQUEST)){
			$regs = array();
			$hrefFields = false;
			$multiobjectFields = false;

			foreach(array_keys($_REQUEST) as $n){
				if(preg_match('/^we_' . $this->Name . '_(href|multiobject)$/', $n, $regs)){
					${$regs[1] . 'Fields'}|=true;
				}
			}

			if($hrefFields){
				$this->resetElements();
				$hrefs = array();
				$realName = $key = '';
				while((list($k, $v) = $this->nextElement('href'))) {
					if(strstr($k, '_we_jkhdsf_') === FALSE){
						continue;
					}
					list($realName, $key) = explode('_we_jkhdsf_', $k);
					if(!isset($hrefs[$realName])){
						$hrefs[$realName] = array();
					}
					$hrefs[$realName][$key] = $v['dat'];
				}
				foreach($hrefs as $k => $v){
					$this->setElement($k, serialize($v));
				}
			}

			if($multiobjectFields){
				$this->resetElements();
				$multiobjects = array();
				while((list($k, $v) = $this->nextElement('multiobject'))) {
					$realName = preg_replace('/^(.+)_default.+$/', '\1', $k);
					$key = preg_replace('/^.+_default(.+)$/', '\1', $k);
					if(!isset($multiobjects[$realName])){
						$multiobjects[$realName] = array();
					}
					if(isset($_REQUEST['we_' . $this->Name . '_multiobject'][$k])){
						$multiobjects[$realName][$key] = $_REQUEST['we_' . $this->Name . '_multiobject'][$k];
					}
				}
				foreach($multiobjects as $realName => $data){
					$old = unserialize($this->getElement($realName));
					$temp = array(
						'class' => $old['class'],
						'max' => $old['max'],
						'objects' => $data,
					);
					$this->setElement($realName, serialize($temp));
				}
			}
		}
	}

	function userCanSave(){

		if($_SESSION['perms']['ADMINISTRATOR']){
			return true;
		}
		if(!we_hasPerm('CAN_SEE_OBJECTFILES')){
			return false;
		}
		if(!$this->RestrictOwners){
			return true;
		}

		$ownersReadOnly = $this->OwnersReadOnly ? unserialize($this->OwnersReadOnly) : array();
		$readers = array();
		foreach(array_keys($ownersReadOnly) as $key){
			if(isset($ownersReadOnly[$key]) && $ownersReadOnly[$key] == 1)
				$readers[] = $key;
		}
		return !we_users_util::isUserInUsers($_SESSION['user']['ID'], $readers);
	}

	/**
	 * @return bool
	 * @desc	checks if the user has the right to see an objectfile
	 */
	function userHasPerms(){
		if($_SESSION['perms']['ADMINISTRATOR']){
			return true;
		}
		if(!we_hasPerm('CAN_SEE_OBJECTFILES')){
			return false;
		}
		if((!$this->RestrictOwners) || we_isOwner($this->Owners) || we_isOwner($this->CreatorID)){
			return true;
		}
		return false;
	}

	/**
	 * checks if this object can have variants
	 *
	 * if paramter checkField is true, this function checks also, if there are
	 * already fields selected for the variants.
	 *
	 * @return boolean
	 */
	function canHaveVariants($checkFields = false){
		if(!defined('SHOP_TABLE') || $this->TableID == 0){
			return false;
		}
		$object = new we_object();
		$object->initByID($this->TableID, OBJECT_TABLE);

		if($checkFields){
			return $object->canHaveVariants() && count($object->getVariantFields());
		} else{
			return $object->canHaveVariants();
		}
	}

	public function initByID($we_ID, $we_Table = OBJECT_FILES_TABLE, $from = we_class::LOAD_MAID_DB){
		parent::initByID(intval($we_ID), $we_Table, $from);
		if(isset($this->elements['Charset'])){
			$this->Charset = $this->elements['Charset']['dat'];
			unset($this->elements['Charset']);
		}

// Fix for added field OF_IsSearchable
		if($this->IsSearchable != 1 && $this->IsSearchable != 0){
			$this->IsSearchable = true;
		}
	}

	function initVariantDataFromDb(){
		if(defined('WE_SHOP_VARIANTS_ELEMENT_NAME') && isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME])){
			if(isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]) && $this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'] && !is_array($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'])){
// unserialize the variant data when loading the model
				$this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'] = unserialize($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']);
			}
			weShopVariants::setVariantDataForModel($this);
		}
	}

	/**
	 * @return	array with the filed names as keys and attributes as values
	 */
	function getVariantFields(){
		if($this->TableID == 0){
			return array();
		}
		$object = new we_object();
		$object->initByID($this->TableID, OBJECT_TABLE);
		return $object->getVariantFields();
	}

	function downMetaAtObject($name, $i){
		$old = unserialize($this->getElement($name));
		$objects = $old['objects'];
		$temp = $objects[($i + 1)];
		$objects[($i + 1)] = $objects[$i];
		$objects[$i] = $temp;
		$new = array(
			'class' => $old['class'],
			'max' => $old['max'],
			'objects' => $objects,
		);
		$this->setElement($name, serialize($new));
	}

	function upMetaAtObject($name, $i){
		$old = unserialize($this->getElement($name));
		$objects = $old['objects'];
		$temp = $objects[($i - 1)];
		$objects[($i - 1)] = $objects[$i];
		$objects[$i] = $temp;
		$new = array(
			'class' => $old['class'],
			'max' => $old['max'],
			'objects' => $objects,
		);
		$this->setElement($name, serialize($new));
	}

	function addMetaToObject($name, $pos){
		$amount = 1;
		$old = unserialize($this->getElement($name));
		$objects = $old['objects'];
		for($i = count($objects) + $amount - 1; 0 <= $i; $i--){
			if(($pos + $amount) < $i){
				$objects[$i] = $objects[($i - $amount)];
			} else if($pos < $i && $i <= ($pos + $amount)){
				$objects[$i] = '';
			}
		}
		$new = array(
			'class' => $old['class'],
			'max' => $old['max'],
			'objects' => $objects,
		);
		$this->setElement($name, serialize($new));
	}

	function removeMetaFromObject($name, $nr){
		$old = unserialize($this->getElement($name));
		$objects = $old['objects'];
		for($i = 0; $i < count($objects) - 1; $i++){
			if($i >= $nr){
				$objects[$i] = $objects[($i + 1)];
			}
		}
		unset($objects[$i]);
		$new = array(
			'class' => $old['class'],
			'max' => $old['max'],
			'objects' => $objects,
		);
		$this->setElement($name, serialize($new));
	}

	function checkAndCorrectParent(){
		if(!isset($this->ParentID) || $this->ParentID == ''){
			$this->resetParentID();
		}
		$len = strlen($this->RootDirPath . '/');
		if(substr($this->ParentPath . '/', 0, $len) != substr($this->RootDirPath . '/', 0, $len)){
			$this->resetParentID();
		}
	}

	protected function updateRemoteLang($db, $id, $lang, $type){
		list($oldLang, $tid) = getHash('SELECT Language,TableID FROM ' . $this->Table . ' WHERE ID=' . $id, $db);
		if($oldLang == $lang){
			return;
		}
//update Lang of doc
		$db->query('UPDATE ' . $this->Table . ' SET Language="' . $lang . '" WHERE ID=' . $id);
		$db->query('UPDATE ' . OBJECT_X_TABLE . $tid . 'SET OF_Language="' . $lang . '" WHERE ID=' . $id);
//update LangLink:
		$db->query('UPDATE ' . LANGLINK_TABLE . ' SET DLocale="' . $lang . '" WHERE DID=' . $id . ' AND DocumentTable="' . $type . '"');
//drop invalid entries => is this safe???
		$db->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=' . $id . ' AND DocumentTable="' . $type . '" AND Locale!="' . $lang . '"');
	}

}

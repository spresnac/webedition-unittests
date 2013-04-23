<?php

/**
 * webEdition CMS
 *
 * $Rev: 5836 $
 * $Author: lukasimhof $
 * $Date: 2013-02-18 16:16:47 +0100 (Mon, 18 Feb 2013) $
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
$yuiSuggest = & weSuggest::getInstance();

/* a class for handling templates */

class we_class_folder extends we_folder{

	var $IsClassFolder = '1';
	var $ClassPath = ''; //#4076
	var $ClassID = ''; //#4076
	var $RootfolderID = ''; //#4076
	var $searchclass;
	var $searchclass_class;
	var $GreenOnly = 0;
	var $Order = 'OF_Path';
	var $Search = '';
	var $SearchField = '';
	var $SearchStart = 0;
	var $TriggerID = 0;

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'searchclass', 'searchclass_class', 'TriggerID');
		array_push($this->EditPageNrs, WE_EDITPAGE_PROPERTIES, WE_EDITPAGE_CFWORKSPACE, WE_EDITPAGE_FIELDS, WE_EDITPAGE_INFO);
		$this->Icon = we_base_ContentTypes::CLASS_FOLDER_ICON;
		$this->ContentType = 'folder';
	}

	function setClassProp(){
		$DB_WE = new DB_WE();
		$sp = explode('/', $this->Path);
		$this->ClassPath = '/' . $sp[1];
		$this->ClassID = f('SELECT ID FROM ' . OBJECT_TABLE . " WHERE Path='" . $DB_WE->escape($this->ClassPath) . "'", "ID", $DB_WE);
		$this->RootfolderID = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='" . $DB_WE->escape($this->ClassPath) . "'", "ID", $DB_WE);
	}

	function we_rewrite(){
		$this->ClassName = 'we_class_folder';
		$this->IsNotEditable = 0;
		$this->we_save(0, 1);
	}

	function we_initSessDat($sessDat){
		we_folder::we_initSessDat($sessDat);
		if(isset($this->searchclass_class) && !is_object($this->searchclass_class)){
			$this->searchclass_class = unserialize($this->searchclass_class);
		} else if(isset($_SESSION['weS']['we_objectSearch'][$this->ID])){
			$temp = unserialize($_SESSION['weS']['we_objectSearch'][$this->ID]);
			$this->searchclass_class = unserialize($temp['Serialized']);
			$this->SearchStart = $temp['SearchStart'];
			$this->GreenOnly = $temp['GreenOnly'];
			$this->EditPageNr = $temp['EditPageNr'];
			$this->Order = $temp['Order'];
		}

		if(is_object($this->searchclass_class)){
			$this->searchclass = $this->searchclass_class;
		} else{
			$this->searchclass = new objectsearch();
			$this->searchclass_class = serialize($this->searchclass);
		}

		if(empty($this->EditPageNr)){
			$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
		}
		$this->setClassProp();
	}

	public function we_save($resave = 0, $skipHook = 0){
		$sp = explode('/', $this->Path);
		if(isset($sp[2]) && $sp[2] != ''){
			$this->IsClassFolder = 0;
		}
		parent::we_save($resave, $skipHook);
		return true;
	}

	function initByPath($path, $tblName = OBJECT_FILES_TABLE, $IsClassFolder = 0, $IsNotEditable = 0, $skipHook = 0){
		$id = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $path . '" AND IsFolder=1', 'ID', $this->DB_WE);
		if($id != ''){
			$this->initByID($id, $tblName);
		} else{
			## Folder does not exist, so we have to create it (if user has permissons to create folders)
			$spl = explode('/', $path);
			$folderName = array_pop($spl);
			$p = array();
			$anz = count($spl);
			$last_pid = 0;
			for($i = 0; $i < $anz; $i++){
				$p[] = array_shift($spl);
				$pa = $this->DB_WE->escape(implode('/', $p));
				if($pa){
					$pid = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $pa . '"', 'ID', $this->DB_WE);
					if(!$pid){
						$folder = new self();
						$folder->init();
						$folder->Table = $tblName;
						$folder->ParentID = $last_pid;
						$folder->Text = $p[$i];
						$folder->Filename = $p[$i];
						/* code vor 4076
						  $folder->IsNotEditable = $IsClassFolder;
						  $folder->ClassName=($IsClassFolder)?"we_class_folder":"we_folder";
						  $this->IsClassFolder=$IsClassFolder;
						 */
						$folder->IsNotEditable = 0;
						//$folder->ClassName = 'we_class_folder';
						$folder->IsClassFolder = $IsClassFolder;
						$folder->Icon = ($IsClassFolder) ? we_base_ContentTypes::CLASS_FOLDER_ICON : we_base_ContentTypes::FOLDER_ICON;

						$folder->Path = $pa;
						$folder->save($skipHook);
						$last_pid = $folder->ID;
					} else{
						$last_pid = $pid;
					}
				}
			}
			$this->init();
			$this->Table = $tblName;
			/* code vor 4076
			  $this->ClassName=($IsClassFolder)?"we_class_folder":"we_folder";

			 */
			$this->ClassName = 'we_class_folder';
			$this->IsClassFolder = $IsClassFolder;
			$this->Icon = $IsClassFolder ? we_base_ContentTypes::CLASS_FOLDER_ICON : we_base_ContentTypes::FOLDER_ICON;

			$this->ParentID = $last_pid;
			$this->Text = $folderName;
			$this->Filename = $folderName;
			$this->Path = $path;
			//#4076
			$this->setClassProp();

			$this->IsNotEditable = $IsNotEditable;
			$this->save(0, $skipHook);
		}
		return true;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		$this->ContentType = 'folder';

		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
				return 'we_templates/we_editor_properties.inc.php';

			case WE_EDITPAGE_INFO:
				return 'we_templates/we_editor_info.inc.php';

			case WE_EDITPAGE_CFWORKSPACE:
				return 'we_modules/object/we_classFolder_properties.inc.php';
			case WE_EDITPAGE_FIELDS:
				return 'we_modules/object/we_classFolder_fields.inc.php';
			case WE_EDITPAGE_WEBUSER:
				return 'we_modules/customer/editor_weDocumentCustomerFilter.inc.php';
			/*
			  case WE_EDITPAGE_CFSEARCH:
			  return 'we_modules/object/we_classFolder_search.inc.php';
			  break;
			 */
			default:
				$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PROPERTIES;
				return 'we_templates/we_editor_properties.inc.php';
		}
	}

	function getUserDefaultWsPath(){
		$userWSArray = makeArrayFromCSV(get_ws());

		$userDefaultWsID = empty($userWSArray) ? 0:$userWSArray[0];
		return (intval($userDefaultWsID) != 0 ?
				id_to_path($userDefaultWsID, FILE_TABLE, $GLOBALS['DB_WE']) :
				'/');
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$parents = array(0, $this->ID);
		we_getParentIDs(FILE_TABLE, $this->ID, $parents);
		$this->setClassProp();
		$ParentsCSV = makeCSVFromArray($parents, true);
		if($this->ID){
			$_disabled = false;
			$_disabledNote = '';
		} else{
			$_disabled = true;
			$_disabledNote = ' ' . g_l('weClass', '[availableAfterSave]');
		}

		//javascript:we_cmd('openDirselector', document.forms[0].elements['" . $idname . "'].value, '" . $this->Table . "', 'document.forms[\\'we_form\\'].elements[\\'" . $idname . "\\'].value', '', 'var parents = \\'".$ParentsCSV."\\';if(parents.indexOf(\\',\\' WE_PLUS currentID WE_PLUS \\',\\') > -1){" . we_message_reporting::getShowMessageCall(g_l('alert',"[copy_folder_not_valid]"), we_message_reporting::WE_MESSAGE_ERROR) . "}else{opener.top.we_cmd(\\'copyFolder\\', currentID,".$this->ID.",1,\\'".$this->Table."\\');}','',".$this->RootfolderID.");
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc3 = we_cmd_enc("var parents = '" . $ParentsCSV . "';if(parents.indexOf(',' WE_PLUS currentID WE_PLUS ',') > -1){" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folder_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . "}else{opener.top.we_cmd('copyFolder', currentID," . $this->ID . ",1,'" . $this->Table . "');};");
		$but = we_button::create_button('select', $this->ID ? "javascript:we_cmd('openDirselector', document.forms[0].elements['" . $idname . "'].value, '" . $this->Table . "', '" . $wecmdenc1 . "', '', '" . $wecmdenc3 . "',''," . $this->RootfolderID . ");" : "javascript:" . we_message_reporting::getShowMessageCall(g_l('alert', "[copy_folders_no_id]"), we_message_reporting::WE_MESSAGE_ERROR), true, 100, 22, "", "", $_disabled);

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', "[copy_owners_expl]") . $_disabledNote, 2, 388, false) . '</td><td>' .
			$this->htmlHidden($idname, $this->CopyID) . $but . '</td></tr>
					<tr><td>' . we_html_tools::getPixel(409, 2) . '</td><td></td></tr></table>';
	}

	function searchProperties(){
		$DB_WE = new DB_WE();

		if(!isset($_REQUEST['Order'])){
			$_REQUEST['Order'] = (isset($this->Order) ? $this->Order : 'ModDate DESC');
		} else{
			$this->searchclass->Order = $_REQUEST['Order'];
		}
		$this->Order = $_REQUEST['Order'];

		if(isset($_POST['SearchStart'])){
			$this->searchclass->searchstart = $_POST['SearchStart'];
		}

		if(isset($_REQUEST['Anzahl'])){
			$this->searchclass->anzahl = $_REQUEST['Anzahl'];
		}

		//$this->searchclass->setlimit(1);
		$we_obectPathLength = 32;

		//$this->searchclass->setlimit(2);
		$we_wsLength = 26;
		$we_extraWsLength = 26;

		$userWSArray = makeArrayFromCSV(get_ws());

		$userDefaultWsID = !empty($userWSArray) ? $userWSArray[0] : 0;
		$userDefaultWsPath = (intval($userDefaultWsID) != 0 ? id_to_path($userDefaultWsID, FILE_TABLE, $DB_WE) : '/');

		//#4076
		$this->setClassProp();

		// get Class
		$classArray = getHash('SELECT * FROM ' . OBJECT_TABLE . ' WHERE Path="' . $DB_WE->escape($this->ClassPath) . '"', $DB_WE);


		$userDefaultWsPath = $this->getUserDefaultWsPath();
		$this->WorkspacePath = ($this->WorkspacePath != '') ? $this->WorkspacePath : $userDefaultWsPath;
		$this->WorkspaceID = ($this->WorkspaceID != '') ? $this->WorkspaceID : $userDefaultWsID;

		$where = (isset($this->searchclass->searchname) ?
				'1 ' . $this->searchclass->searchfor($this->searchclass->searchname, $this->searchclass->searchfield, $this->searchclass->searchlocation, OBJECT_X_TABLE . $classArray["ID"], $rows = -1, $start = 0, $order = "", $desc = 0) . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $classArray["ID"]) :
				"1" . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $classArray["ID"]));

		$this->searchclass->settable(OBJECT_X_TABLE . $classArray["ID"] . ", " . OBJECT_FILES_TABLE);
		//$this->searchclass->setwhere($where.' AND '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ID !=0 AND '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ID = '.OBJECT_FILES_TABLE.'.ID'); #4076 orig
		$this->searchclass->setwhere($where . ' AND ' . OBJECT_X_TABLE . $classArray["ID"] . ".OF_PATH LIKE '" . $this->Path . "/%' " . ' AND ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ID !=0 AND ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ID = ' . OBJECT_FILES_TABLE . '.ID');

		$foundItems = $this->searchclass->countitems();

		//$this->searchclass->setorder($z);
		//$this->searchclass->setstart(1);
		//$this->searchclass->searchquery($where.' AND '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ID !=0 AND '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ID = '.OBJECT_FILES_TABLE.'.ID' , OBJECT_X_TABLE.$classArray["ID"].'.ID, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_Text, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ID, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_Path, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ParentID, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_Workspaces, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ExtraWorkspaces, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_ExtraWorkspacesSelected, '.OBJECT_X_TABLE.$classArray["ID"].'.OF_Published, '.OBJECT_FILES_TABLE.'.ModDate'); +4076 orig

		$this->searchclass->searchquery($where . ' AND ' . OBJECT_X_TABLE . $classArray["ID"] . ".OF_PATH LIKE '" . $this->Path . "/%' " . ' AND ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ID !=0 AND ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ID = ' . OBJECT_FILES_TABLE . '.ID', OBJECT_X_TABLE . $classArray["ID"] . '.ID, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_Text, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ID, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_Path, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ParentID, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_Workspaces, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ExtraWorkspaces, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_ExtraWorkspacesSelected, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_Published, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_IsSearchable, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_Charset, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_Language, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_Url, ' . OBJECT_X_TABLE . $classArray["ID"] . '.OF_TriggerID, ' . OBJECT_FILES_TABLE . '.ModDate');


		$content = array();
		$foo = unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classArray["ID"]), "DefaultValues", $DB_WE));

		$ok = isset($foo["WorkspaceFlag"]) ? $foo["WorkspaceFlag"] : "";

		$javascriptAll = "";
		if($foundItems){

			while($this->searchclass->next_record()) {
				$content[] = array(
					array(
						"align" => "center",
						"dat" => ((we_hasPerm("DELETE_OBJECTFILE") || we_hasPerm("NEW_OBJECTFILE")) && checkIfRestrictUserIsAllowed($this->searchclass->f("OF_ID"), OBJECT_FILES_TABLE) ?
							'<input type="checkbox" name="weg' . $this->searchclass->f("ID") . '" />' :
							'<img src="' . TREE_IMAGE_DIR . 'check0_disabled.gif" />')),
					array(
						"align" => "center",
						"height" => 35,
						"dat" => ($this->searchclass->f("OF_Published") && (((in_workspace($this->WorkspaceID, $this->searchclass->f("OF_Workspaces")) && $this->searchclass->f("OF_Workspaces") != "") || (in_workspace($this->WorkspaceID, $this->searchclass->f("OF_ExtraWorkspacesSelected")) && $this->searchclass->f("OF_ExtraWorkspacesSelected") != "" ) ) || ($this->searchclass->f("OF_Workspaces") == "" && $ok)) ?
							'<img src="' . IMAGE_DIR . 'we_boebbel_blau.gif" width="16" height="18" />' :
							'<img src="' . IMAGE_DIR . 'we_boebbel_grau.gif" width="16" height="18" />')),
					array("dat" => ($this->searchclass->f("OF_IsSearchable") ?
							'<img src="' . IMAGE_DIR . 'we_boebbel_blau.gif" width="16" height="18" title="' . g_l('modules_objectClassfoldersearch', '[issearchable]') . '" />' :
							'<img src="' . IMAGE_DIR . 'we_boebbel_grau.gif" width="16" height="18" title="' . g_l('modules_objectClassfoldersearch', '[isnotsearchable]') . '" />')),
					array("dat" => '<a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" style="text-decoration:none" class="middlefont" title="' . $this->searchclass->f("OF_Path") . '">' . $this->searchclass->f("OF_ID") . '</a>'),
					array("dat" => '<a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" style="text-decoration:none" class="middlefont" title="' . $this->searchclass->f("OF_Path") . '">' . shortenPath($this->searchclass->f("OF_Text"), $we_obectPathLength) . '</a>'),
					array("dat" => $this->searchclass->getWorkspaces(makeArrayFromCSV($this->searchclass->f("OF_Workspaces")), $we_wsLength)),
					array("dat" => $this->searchclass->getExtraWorkspace(makeArrayFromCSV($this->searchclass->f("OF_ExtraWorkspaces")), $we_extraWsLength, $classArray["ID"], $userWSArray)),
					array("dat" => '<nobr>' . ($this->searchclass->f("OF_Published") ? date(g_l('date', '[format][default]'), $this->searchclass->f("OF_Published")) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($this->searchclass->f("ModDate") ? date(g_l('date', '[format][default]'), $this->searchclass->f("ModDate")) : "-") . '</nobr>'),
					array("dat" => $this->searchclass->f("OF_Url")),
					array("dat" => $this->searchclass->f("OF_TriggerID") ? id_to_path($this->searchclass->f("OF_TriggerID")) : ''),
					array("dat" => $this->searchclass->f("OF_Charset")),
					array("dat" => $this->searchclass->f("OF_Language")),
				);

				$javascriptAll .= "var flo=document.we_form.elements['weg" . $this->searchclass->f("ID") . "'].checked=true;";
			}
		} else{
			//echo "Leider nichts gefunden!";
		}

		$headline = array(
			array("dat" => ""),
			array("dat" => g_l('modules_objectClassfoldersearch', '[zeige]')),
			array("dat" => ""),
			array("dat" => '<a href="javascript:setOrder(\'OF_ID\');">' . g_l('modules_objectClassfoldersearch', '[ID]') . '</a> ' . $this->getSortImage('OF_ID')),
			array("dat" => '<a href="javascript:setOrder(\'OF_Path\');">' . g_l('modules_objectClassfoldersearch', '[Objekt]') . '</a> ' . $this->getSortImage('OF_Path')),
			array("dat" => g_l('modules_objectClassfoldersearch', '[Arbeitsbereiche]')),
			array("dat" => g_l('modules_objectClassfoldersearch', '[xtraArbeitsbereiche]')),
			array("dat" => '<a href="javascript:setOrder(\'OF_Published\');">' . g_l('modules_objectClassfoldersearch', '[Veroeffentlicht]') . '</a> ' . $this->getSortImage('OF_Published')),
			array("dat" => '<a href="javascript:setOrder(\'ModDate\');">' . g_l('modules_objectClassfoldersearch', '[geaendert]') . '</a> ' . $this->getSortImage('ModDate')),
			array("dat" => '<a href="javascript:setOrder(\'OF_Url\');">' . g_l('modules_objectClassfoldersearch', '[url]') . '</a> ' . $this->getSortImage('OF_Url')),
			array("dat" => '<a href="javascript:setOrder(\'OF_TriggerID\');">' . g_l('modules_objectClassfoldersearch', '[triggerid]') . '</a> ' . $this->getSortImage('OF_TriggerID')),
			array("dat" => g_l('modules_objectClassfoldersearch', '[charset]')),
			array("dat" => g_l('modules_objectClassfoldersearch', '[language]')),
		);

		return $this->getSearchresult($content, $headline, $foundItems, $javascriptAll);
	}

	function searchFields(){

		$DB_WE = new DB_WE();

		if(!isset($_REQUEST['Order'])){
			$_REQUEST['Order'] = (isset($this->Order) ? $this->Order : 'OF_PATH');
		} else{
			if(stripos($_REQUEST['Order'], "ModDate") === 0 || stripos($_REQUEST['Order'], "OF_Published") === 0){
				$_REQUEST['Order'] = 'OF_PATH';
			}
			$this->searchclass->Order = $_REQUEST["Order"];
		}
		$this->Order = $_REQUEST["Order"];


		if(isset($_POST["SearchStart"])){
			$this->searchclass->searchstart = $_POST["SearchStart"];
		}

		if(isset($_REQUEST["Anzahl"])){
			$this->searchclass->anzahl = $_REQUEST["Anzahl"];
		}

		//$this->searchclass->setlimit(1);
		$we_obectPathLength = 32;
		$values = array(10 => 10, 25 => 25, 50 => 50, 100 => 100, 500 => 500, 1000 => 1000, 5000 => 5000, 10000 => 10000, 50000 => 50000, 100000 => 100000);
		$strlen = 20;

		//#4076
		$this->setClassProp();

		// get Class
		$classArray = getHash('SELECT * FROM ' . OBJECT_TABLE . ' WHERE Path="' . $DB_WE->escape($this->ClassPath) . '"', $DB_WE);

		if(isset($_REQUEST["do"]) && $_REQUEST["do"] == "delete"){
			foreach(array_keys($_REQUEST) as $f){
				if(substr($f, 0, 3) == "weg"){
					//$this->query("");
					$ofid = f('SELECT OF_ID FROM ' . OBJECT_X_TABLE . intval($classArray["ID"]) . ' WHERE ID=' . intval(substr($f, 3), 'OF_ID', $DB_WE));

					if(checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE)){
						$DB_WE->query('DELETE FROM ' . OBJECT_X_TABLE . intval($classArray["ID"]) . ' WHERE ID=' . intval(substr($f, 3)));
						$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE OID=' . intval($ofid));
						$DB_WE->query('DELETE FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($ofid));
						we_temporaryDocument::delete($ofid, OBJECT_FILES_TABLE);
					}
				}
			}
		}

		$userWSArray = makeArrayFromCSV(get_ws());

		$userDefaultWsID = !empty($userWSArray) ? $userWSArray[0] : 0;
		$userDefaultWsPath = (intval($userDefaultWsID) != 0 ? id_to_path($userDefaultWsID, FILE_TABLE, $DB_WE) : '/');

		$fields = '*';

		$userDefaultWsPath = $this->getUserDefaultWsPath();
		$this->WorkspacePath = ($this->WorkspacePath != "") ? $this->WorkspacePath : $userDefaultWsPath;
		$this->WorkspaceID = ($this->WorkspaceID != "") ? $this->WorkspaceID : $userDefaultWsID;

		if(isset($this->searchclass->searchname)){
			$where = '1' . $this->searchclass->searchfor($this->searchclass->searchname, $this->searchclass->searchfield, $this->searchclass->searchlocation, OBJECT_X_TABLE . $classArray["ID"], $rows = -1, $start = 0, $order = "", $desc = 0) .
				$this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $classArray["ID"]);
		} else{
			$where = '1' . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $classArray["ID"]);
		}

		$this->searchclass->settable(OBJECT_X_TABLE . $classArray["ID"]);
		//$this->searchclass->setwhere($where." AND OF_ID !=0 "); #4076 orig
		$this->searchclass->setwhere($where . " AND OF_PATH LIKE '" . $this->Path . "/%' AND OF_ID !=0 ");


		$foundItems = $this->searchclass->countitems();

		//$this->searchclass->setorder($z);
		//$this->searchclass->setstart(1);
		//$this->searchclass->searchquery($where." AND OF_ID !=0 ",$fields); #4076 orig
		$this->searchclass->searchquery($where . " AND OF_PATH LIKE '" . $this->Path . "/%' AND OF_ID !=0 ", $fields);

		$DefaultValues = unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->ClassID), 'DefaultValues', $DB_WE));

		$content = array();
		$foo = unserialize(f("SELECT DefaultValues FROM " . OBJECT_TABLE . " WHERE ID=" . intval($classArray["ID"]), "DefaultValues", $DB_WE));

		$ok = isset($foo["WorkspaceFlag"]) ? $foo["WorkspaceFlag"] : "";

		$javascriptAll = "";
		$headline = array(
			array("dat" => ""),
			array("dat" => '<table border="0" cellpadding="0" cellspacing="0" class="defaultfont"><tr><td>' . g_l('modules_objectClassfoldersearch', '[zeige]') . '</td><td></td></tr></table>'),
			array("dat" => ''),
			array("dat" => '<table border="0" cellpadding="0" cellspacing="0" class="defaultfont"><tr><td><a href="javascript:setOrder(\'OF_ID\');">' . g_l('modules_objectClassfoldersearch', '[ID]') . '</a></td><td> ' . $this->getSortImage('OF_ID') . '</td></tr></table>'),
			array("dat" => '<table border="0" cellpadding="0" cellspacing="0" class="defaultfont"><tr><td><a href="javascript:setOrder(\'OF_Path\');">' . g_l('modules_objectClassfoldersearch', '[Objekt]') . '</a></td><td> ' . $this->getSortImage('OF_Path') . '</td></tr></table>'),
		);

		if($foundItems){

			$f = 0;
			while($this->searchclass->next_record()) {
				/*
				  $out .= "<pre>".sizeof($this->searchclass->Record);
				  print_r($this->searchclass->Record);
				  $out .= "</pre>";
				 */
				if($f == 0){
					$i = 0;
					$regs = array();
					foreach($this->searchclass->getRecord() as $key => $val){
						if(preg_match('/(.+?)_(.*)/', $key, $regs)){
							switch($regs[1]){
								case "object":
									$object[$i + 5] = $regs[2];
									$headline[$i + 5]["dat"] = '<table border="0" cellpadding="0" cellspacing="0" class="defaultfont"><tr><td>' . f("SELECT Text FROM " . OBJECT_TABLE . " WHERE ID='" . $regs[2] . "'", "Text", $DB_WE) . '</td><td></td></tr></table>';
									$type[$i + 5] = $regs[1];
									$i++;
									break;
								case "multiobject":
									$headline[$i + 5]["dat"] = '<table border="0" cellpadding="0" cellspacing="0" class="defaultfont"><tr><td>' . $regs[2] . '</td><td></td></tr></table>';
									$head[$i + 5]["dat"] = $regs[2];
									$type[$i + 5] = $regs[1];
									$i++;
									break;
								default:
									if($regs[1] != "OF"){
										$headline[$i + 5]["dat"] = '<table border="0" cellpadding="0" cellspacing="0" class="defaultfont"><tr><td><a href="javascript:setOrder(\'' . $key . '\');">' . $regs[2] . '</a></td><td> ' . $this->getSortImage($key) . '</td></tr></table>';
										$head[$i + 5]["dat"] = $regs[2];
										$type[$i + 5] = $regs[1];
										$i++;
									}
							}
						}
					}

					$count = $i;
				}

				$javascriptAll .= "var flo=document.we_form.elements['weg" . $this->searchclass->f("ID") . "'].checked=true;";
				$content[$f] =
					array(
						array(
							"height" => 35,
							"align" => "center",
							"dat" => (we_hasPerm("DELETE_OBJECTFILE") ?
								'<input type="checkbox" name="weg' . $this->searchclass->f("ID") . '" />' :
								'<img src="' . TREE_IMAGE_DIR . 'check0_disabled.gif" />'
							)),
						array(
							"align" => "center",
							"dat" => '<img src="' . IMAGE_DIR . ($this->searchclass->f("OF_Published") && (((in_workspace($this->WorkspaceID, $this->searchclass->f("OF_Workspaces")) && $this->searchclass->f("OF_Workspaces") != "") || (in_workspace($this->WorkspaceID, $this->searchclass->f("OF_ExtraWorkspacesSelected")) && $this->searchclass->f("OF_ExtraWorkspacesSelected") != "" ) ) || ($this->searchclass->f("OF_Workspaces") == "" && $ok)) ?
								'we_boebbel_blau.gif' :
								'we_boebbel_grau.gif'
							) . '" width="16" height="18" />'
						),
						array(
							"dat" => ($this->searchclass->f("OF_IsSearchable") ?
								'<img src="' . IMAGE_DIR . 'we_boebbel_blau.gif" width="16" height="18" title="' . g_l('modules_objectClassfoldersearch', '[issearchable]') . '" />' :
								'<img src="' . IMAGE_DIR . 'we_boebbel_grau.gif" width="16" height="18" title="' . g_l('modules_objectClassfoldersearch', '[isnotsearchable]') . '" />'
							)),
						array("dat" => '<a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" style="text-decoration:none" class="middlefont" title="' . $this->searchclass->f("OF_Path") . '">' . $this->searchclass->f("OF_ID") . '</a>'),
						array("dat" => '<a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" style="text-decoration:none" class="defaultfont" title="' . $this->searchclass->f("OF_Path") . '">' . shortenPath($this->searchclass->f("OF_Text"), $we_obectPathLength) . '</a>'),
				);
				for($i = 0; $i < $count; $i++){
					switch($type[$i + 5]){
						case "date":
							$content[$f][$i + 5]["dat"] = date(g_l('date', '[format][default]'), $this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"]));
							break;
						case "object":
							$tmp = f("SELECT OF_Path FROM " . OBJECT_X_TABLE . $object[$i + 5] . " WHERE OF_ID='" . $this->searchclass->f($type[$i + 5] . "_" . $object[$i + 5]) . "'", "OF_Path", $DB_WE);
							if($tmp != ""){
								$publ = f("SELECT Published FROM " . OBJECT_FILES_TABLE . " WHERE ID='" . $this->searchclass->f($type[$i + 5] . "_" . $object[$i + 5]) . "'", "Published", $DB_WE);
								$obj = '<a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f($type[$i + 5] . "_" . $object[$i + 5]) . ',\'objectFile\');" style="text-decoration:none; ' . ($publ ? '' : 'color:red;') . '" class="defaultfont" title="' . $tmp . '">' . shortenPath(f("SELECT OF_Path FROM " . OBJECT_X_TABLE . $object[$i + 5] . " WHERE OF_ID='" . $this->searchclass->f($type[$i + 5] . "_" . $object[$i + 5]) . "'", "OF_Path", $DB_WE), $we_obectPathLength) . '</a>';
							} else{
								$obj = "&nbsp;";
							}
							$content[$f][$i + 5]["dat"] = $obj;
							break;
						case "multiobject":
							$temp = unserialize($this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"]));
							if(is_array($temp['objects']) && !empty($temp['objects'])){
								$objects = $temp['objects'];
								$class = $temp['class'];
								$content[$f][$i + 5]["dat"] = "";
								foreach($objects as $idx => $id){
									$content[$f][$i + 5]["dat"] .= '<a href="javascript:top.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $id . ',\'objectFile\');" style="text-decoration:none" class="defaultfont" title="' . f("SELECT OF_Path FROM " . OBJECT_X_TABLE . $class . " WHERE OF_ID='" . $id . "'", "OF_Path", $DB_WE) . '">' . shortenPath(f("SELECT OF_Path FROM " . OBJECT_X_TABLE . $class . " WHERE OF_ID='" . $id . "'", "OF_Path", $DB_WE), $we_obectPathLength) . ".</a><br />"; //
								}
							} else{
								$content[$f][$i + 5]["dat"] = "-";
							}
							break;
						case "checkbox":
							$text = $this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"]);
							$content[$f][$i + 5]["dat"] = ($text == "1" ? g_l('global', "[yes]") : g_l('global', "[no]") );
							break;
						case "meta":
							if($this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]["dat"]) != ''
										&& isset($DefaultValues[$type[$i + 5] . '_' . $head[$i + 5]["dat"]]["meta"][$this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"])])){
								$text = $DefaultValues[$type[$i + 5] . '_' . $head[$i + 5]["dat"]]["meta"][$this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"])];
								$content[$f][$i + 5]["dat"] = (strlen($text) > $strlen) ? substr($text, 0, $strlen) . " ..." : $text;
							} else{
								$content[$f][$i + 5]["dat"] = '&nbsp;';
							}
							break;
						case 'link':
							$text = $this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"]);
							//FIXME: this is not php compliant getFieldByVal is a dynamic method - and must be
							$content[$f][$i + 5]["dat"] = we_document::getFieldByVal($text, "link");
							break;
						case 'href':
							$text = $this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"]);
							$hrefArr = $text ? unserialize($text) : array();
							if(!is_array($hrefArr))
								$hrefArr = array();

							//FIXME: this is not php compliant getHrefByArray is a dynamic method - and must be
							$content[$f][$i + 5]["dat"] = we_document::getHrefByArray($hrefArr);
							//$text = $DefaultValues[$type[$i+3]."_".$head[$i+3]["dat"]]["meta"][$this->searchclass->f($type[$i+3]."_".$head[$i+3]["dat"])];
							//$content[$f][$i+3]["dat"] = "TEST";
							break;
						default:
							$text = strip_tags($this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]["dat"]));
							$content[$f][$i + 5]["dat"] = (strlen($text) > $strlen) ? substr($text, 0, $strlen) . " ..." : $text;
							break;
					}
				}

				$f++;
			}
			//} else{
			//$out .= "Leider nichts gefunden!";
		}
		return $this->getSearchresult($content, $headline, $foundItems, $javascriptAll);
	}

	function getSearchDialog(){

		$DB_WE = new DB_WE();

		//#4076
		$this->setClassProp();

		$out = '
				<table cellpadding="2" cellspacing="0" border="0" width="510">
				<form name="we_form_search"  onSubmit="sub();return false;" methode="GET">
				' . we_class::hiddenTrans() . '
				<input type="hidden" name="todo" />
				<input type="hidden" name="position" />';

		for($i = 0; $i <= $this->searchclass->height; $i++){

			$button = ($i == 0 ?
					we_html_tools::getPixel(26, 10) :
					we_button::create_button("image:btn_function_trash", "javascript:del(" . $i . ");", true, 26, 22, "", "", false)
				);


			if(isset($this->searchclass->objsearchField) && is_array($this->searchclass->objsearchField) && isset($this->searchclass->objsearchField[$i]) && (substr($this->searchclass->objsearchField[$i], 0, 4) == "meta" || substr($this->searchclass->objsearchField[$i], 0, 8) == "checkbox")){
				$DefaultValues = unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->ClassID), 'DefaultValues', $DB_WE));

				$values = (substr($this->searchclass->objsearchField[$i], 0, 4) == "meta" ?
						$DefaultValues[$this->searchclass->objsearchField[$i]]["meta"] :
						array(
						0 => g_l('global', '[no]'),
						1 => g_l('global', '[yes]'),
						)
					);

				$out .= '
<tr>
	<td class="defaultfont">' . g_l('global', "[search]") . '</td>
	<td width="50">' . we_html_tools::getPixel(5, 2) . '</td>'
					//<td>'.$this->searchclass->getFields("objsearchField[".$i."]",1,$this->searchclass->objsearchField[$i],$this->Path).'</td> #4076 orig
					. '<td>' . $this->searchclass->getFields("objsearchField[" . $i . "]", 1, $this->searchclass->objsearchField[$i], $this->ClassPath) . '</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td width="50">' . $this->searchclass->getLocationMeta("objlocation[" . $i . "]", (isset($this->searchclass->objlocation[$i]) ? $this->searchclass->objlocation[$i] : '')) . '</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td>' . we_html_tools::htmlSelect('objsearch[' . $i . ']', $values, 1, $this->searchclass->objsearch[$i], false, '', 'value') . '</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td align="right">' . $button . '</td>
</tr>';
			} elseif(isset($this->searchclass->objsearchField) && is_array($this->searchclass->objsearchField) && isset($this->searchclass->objsearchField[$i]) && substr($this->searchclass->objsearchField[$i], 0, 4) == "date"){
				$DefaultValues = unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->ClassID), 'DefaultValues', $DB_WE));

				$month = array('' => '');
				for($j = 1; $j <= 12; $j++){
					$month[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$day = array('' => '');
				for($j = 1; $j <= 31; $j++){
					$day[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$hour = array('' => '');
				for($j = 0; $j <= 23; $j++){
					$hour[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$minute = array('' => '');
				for($j = 0; $j <= 59; $j++){
					$minute[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$out .= '
<tr>
	<td class="defaultfont">' . g_l('global', "[search]") . '</td>
	<td>' . we_html_tools::getPixel(5, 2) . '</td>
	<td>' . $this->searchclass->getFields("objsearchField[" . $i . "]", 1, $this->searchclass->objsearchField[$i], $this->ClassPath) . '</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td>' . $this->searchclass->getLocationDate("objlocation[" . $i . "]", (isset($this->searchclass->objlocation[$i]) ? $this->searchclass->objlocation[$i] : '')) . '</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td>
		' . we_html_tools::htmlTextInput('objsearch[' . $i . '][year]', 4, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['year']) ? $this->searchclass->objsearch[$i]['year'] : date("Y")), 4) . ' -
		' . we_html_tools::htmlSelect('objsearch[' . $i . '][month]', $month, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['month']) ? $this->searchclass->objsearch[$i]['month'] : date("m"))) . ' -
		' . we_html_tools::htmlSelect('objsearch[' . $i . '][day]', $day, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['day']) ? $this->searchclass->objsearch[$i]['day'] : date("d"))) . ' &nbsp;
		' . we_html_tools::htmlSelect('objsearch[' . $i . '][hour]', $hour, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['hour']) ? $this->searchclass->objsearch[$i]['hour'] : date("H"))) . ' :
		' . we_html_tools::htmlSelect('objsearch[' . $i . '][minute]', $minute, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['minute']) ? $this->searchclass->objsearch[$i]['minute'] : date("i"))) . '
	</td>
	<td>' . we_html_tools::getPixel(10, 2) . '</td>
	<td align="right">' . $button . '</td>
</tr>';
			} else{
				$out .= '
<tr>
	<td class="defaultfont">' . g_l('global', "[search]") . '</td>
	<td>' . we_html_tools::getPixel(1, 2) . '</td>
	<td>' . $this->searchclass->getFields("objsearchField[" . $i . "]", 1, (isset($this->searchclass->objsearchField) && is_array($this->searchclass->objsearchField) && isset($this->searchclass->objsearchField[$i]) ? $this->searchclass->objsearchField[$i] : ""), $this->ClassPath) . '</td>
	<td>' . we_html_tools::getPixel(1, 2) . '</td>
	<td>' . $this->searchclass->getLocation("objlocation[" . $i . "]", (isset($this->searchclass->objlocation[$i]) ? $this->searchclass->objlocation[$i] : '')) . '</td>
	<td>' . we_html_tools::getPixel(1, 2) . '</td>
	<td>' . we_html_tools::htmlTextInput("objsearch[" . $i . "]", 30, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]) ? $this->searchclass->objsearch[$i] : ''), "", "", "text", 200) . '</td>
	<td>' . we_html_tools::getPixel(1, 2) . '</td>
	<td align="right">' . $button . '</td>
</tr>';
			}
		}

		$out .= '
<tr>
	<td colspan="9"><br></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td colspan="3">' . we_button::create_button("add", "javascript:newinput();") . '</td>
	<td colspan="4" align="right">' . we_button::create_button("search", "javascript:sub();") . '</td>
</tr>
</form>
</table>';

		return $out;
	}

	function getSearchresult($content, $headline, $foundItems, $javascriptAll){
		$yuiSuggest = & weSuggest::getInstance();

		$values = array(10 => 10, 25 => 25, 50 => 50, 100 => 100, 500 => 500, 1000 => 1000, 5000 => 5000, 10000 => 10000, 50000 => 50000, 100000 => 100000);

		// JS einbinden
		return $this->searchclass->getJSinWEsearchobj($this->Name) . '
		<form name="we_form" method="post">
		' . we_class::hiddenTrans() . '
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="defaultgray">' . g_l('modules_objectClassfoldersearch', '[Verzeichnis]') . '</td>
			<td colspan="3">' . $this->formDirChooser(388, 0, FILE_TABLE, "WorkspacePath", "WorkspaceID", "opener.we_cmd('reload_editpage');", false) . '</td>
		</tr>
		<tr>
			<td colspan="4">' . we_html_tools::getPixel(18, 12) . '</td>
		</tr>
		<tr>
			<td class="defaultgray">' . g_l('modules_objectClassfoldersearch', '[Ansicht]') . '</td>
			<td>' . we_html_tools::htmlSelect("Anzahl", $values, 1, $this->searchclass->anzahl, "", 'onChange=\'this.form.elements["SearchStart"].value=0;we_cmd("reload_editpage");\'') .
			we_html_tools::hidden("Order", $this->searchclass->Order) .
			we_html_tools::hidden("do", "") .
			'</td>
			<td>&nbsp;</td>
			<td>' . we_forms::checkboxWithHidden($this->GreenOnly == 1 ? true : false, "we_" . $this->Name . "_GreenOnly", g_l('modules_objectClassfoldersearch', '[sicht]'), false, "defaultfont", "toggleShowVisible(document.getElementById('_we_" . $this->Name . "_GreenOnly'));") . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(128, 20) . '</td>
			<td>' . we_html_tools::getPixel(40, 15) . '</td>
			<td>' . we_html_tools::getPixel(10, 15) . '</td>
			<td>' . we_html_tools::getPixel(350, 15) . '</td>
		</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="defaultgray">' .
			(isset($this->searchclass->searchname) ? g_l('modules_objectClassfoldersearch', '[teilsuche]') : '') .
			'</td>
			<td align="right">' . $this->searchclass->getNextPrev($foundItems) . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		</table>' .
			we_html_tools::htmlDialogBorder3(900, 0, $content, $headline) . '
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(5, 1) . (we_hasPerm("DELETE_OBJECTFILE") || we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("selectAllObjects", "javascript: " . $javascriptAll) : "") . '</td>
			<td align="right">' . $this->searchclass->getNextPrev($foundItems) . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("DELETE_OBJECTFILE") ? we_button::create_button("image:btn_function_trash", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichloeschen]') . "'))document.we_form.elements['do'].value='delete';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[loesch]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("image:btn_function_publish", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichveroeffentlichen]') . "'))document.we_form.elements['do'].value='publish';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[veroeffentlichen]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("image:btn_function_unpublish", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichparken]') . "'))document.we_form.elements['do'].value='unpublish';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[parken]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("image:btn_function_searchable", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichsearchable]') . "'))document.we_form.elements['do'].value='searchable';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[searchable]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("image:btn_function_unsearchable", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichunsearchable]') . "'))document.we_form.elements['do'].value='unsearchable';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[unsearchable]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("image:btn_function_copy", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopychar]') . "'))document.we_form.elements['do'].value='copychar';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[copychar]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("image:btn_function_copy", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopyws]') . "'))document.we_form.elements['do'].value='copyws';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[copyws]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(175, 12) . '</td>
			<td>' . we_html_tools::getPixel(460, 12) . '</td>
		</tr>
		<tr>
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">' . (we_hasPerm("NEW_OBJECTFILE") ? we_button::create_button("image:btn_function_copy", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopytid]') . "'))document.we_form.elements['do'].value='copytid';we_cmd('reload_editpage');") . '</td>
					<td>' . we_html_tools::getPixel(5, 1) . '</td>
					<td class="small">&nbsp;' . g_l('modules_objectClassfoldersearch', '[copytid]') : "") . '</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
		</form>
		' .
			$yuiSuggest->getYuiCssFiles() .
			$yuiSuggest->getYuiCss() .
			$yuiSuggest->getYuiJsFiles() .
			$yuiSuggest->getYuiJs();
	}

	function getSearchJS(){

		$DB_WE = new DB_WE();

		$modulepath = WE_OBJECT_MODULE_DIR;

		$ret = <<<EOF
		function sub(){
			document.we_form_search.target="load";
			document.we_form_search.action="{$modulepath}search_submit.php";
			document.we_form_search.todo.value="search";
			document.we_form_search.submit();


		}

		function newinput(){
			document.we_form_search.target='load';
			document.we_form_search.action='{$modulepath}search_submit.php';
			document.we_form_search.todo.value="add";
			document.we_form_search.submit();
		}

		function del(pos){
			document.we_form_search.target='load';
			document.we_form_search.action='{$modulepath}search_submit.php';
			document.we_form_search.todo.value="delete";
			document.we_form_search.position.value=pos;
			document.we_form_search.submit();
		}


		function changeit(f){
EOF;
		$objID = f("SELECT ID FROM " . OBJECT_TABLE . " WHERE Path='" . $DB_WE->escape($this->Path) . "'", "ID", $DB_WE);
		if($objID){
			$tableInfo = $DB_WE->metadata(OBJECT_X_TABLE . $objID);

			for($i = 0; $i < count($tableInfo); $i++){
//fixme: explode?
				if(substr($tableInfo[$i]["name"], 0, 5) == "meta_"){

					$ret.= "
			if(f=='" . $tableInfo[$i]["name"] . "'){
				document.we_form_search.target='load';
				document.we_form_search.action='{$modulepath}search_submit.php';
				document.we_form_search.todo.value='changemeta';
				document.we_form_search.submit();
			}
		";
				} else if(substr($tableInfo[$i]["name"], 0, 5) == "date_"){

					$ret.= "
			if(f=='" . $tableInfo[$i]["name"] . "'){
				document.we_form_search.target='load';
				document.we_form_search.action='{$modulepath}search_submit.php';
				document.we_form_search.todo.value='changedate';
				document.we_form_search.submit();
			}
		";
				} else if(substr($tableInfo[$i]["name"], 0, 9) == "checkbox_"){

					$ret.= "
			if(f=='" . $tableInfo[$i]["name"] . "'){
				document.we_form_search.target='load';
				document.we_form_search.action='{$modulepath}search_submit.php';
				document.we_form_search.todo.value='changecheckbox';
				document.we_form_search.submit();
			}
		";
				}
			}
		}
		$ret .= <<<EOF

		}

		function changeitanyway(f){
				document.we_form_search.target='load';
				document.we_form_search.action='{$modulepath}search_submit.php';
				document.we_form_search.todo.value="changemeta";
				document.we_form_search.submit();
		}
EOF;

		return we_html_element::jsElement($ret);
	}

	function getSortImage($for){
		if(strpos($_REQUEST['Order'], $for) === 0){
			if(strpos($_REQUEST['Order'], 'DESC')){
				return '<img border="0" width="11" height="8" src="' . IMAGE_DIR . 'arrow_sort_desc.gif" />';
			}
			return '<img border="0" width="11" height="8" src="' . IMAGE_DIR . 'arrow_sort_asc.gif" />';
		}
		return we_html_tools::getPixel(11, 8);
	}

	function saveInSession(&$save){
		parent::saveInSession($save);

		if(!isset($_SESSION['weS']['we_objectSearch'])){
			$_SESSION['weS']['we_objectSearch'] = array();
		}
		if(!isset($_SESSION['weS']['we_objectSearch'][$this->ID])){
			$_SESSION['weS']['we_objectSearch'][$this->ID] = array();
		}
		$_SESSION['weS']['we_objectSearch'][$this->ID] = serialize(array(
			'Serialized' => serialize($this->searchclass),
			'SearchStart' => $this->SearchStart,
			'GreenOnly' => $this->GreenOnly,
			'Order' => $this->Order,
			'EditPageNr' => $this->EditPageNr,
			));
	}

	function deleteObjects(){

		$DB_WE = new DB_WE();
		$this->setClassProp(); //4076
		$javascript = '';
		$deletedItems = array();

		// get Class
		$classArray = getHash('SELECT * FROM ' . OBJECT_TABLE . ' WHERE Path="' . $DB_WE->escape($this->ClassPath) . '"', $DB_WE);
		foreach(array_keys($_REQUEST) as $f){
			if(substr($f, 0, 3) == 'weg'){
				$tid = intval(substr($f, 3));
				$ofid = f('SELECT OF_ID FROM ' . OBJECT_X_TABLE . intval($classArray['ID']) . ' WHERE ID=' . $tid, 'OF_ID', $DB_WE);
				if(checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE)){
					$DB_WE->query('DELETE FROM ' . OBJECT_X_TABLE . intval($classArray['ID']) . ' WHERE ID=' . $tid);
					$DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE OID=' . intval($ofid));
					$DB_WE->query('DELETE FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($ofid));

					$obj = new we_objectFile();
					$obj->initByID($ofid, OBJECT_FILES_TABLE);

					we_temporaryDocument::delete($ofid, OBJECT_FILES_TABLE, $DB_WE);
					$javascript .= 'top.deleteEntry(' . $obj->ID . ');';

					$deletedItems[] = $obj->ID;
				}
			}
		}

		return $javascript . "
			top.drawTree();

			// close all Editors with deleted documents
			var _usedEditors =  top.weEditorFrameController.getEditorsInUse();

			var _delete_table = '" . OBJECT_FILES_TABLE . "';
			var _delete_Ids = '," . implode(",", $deletedItems) . ",';

			for ( frameId in _usedEditors ) {

				if ( _delete_table == _usedEditors[frameId].getEditorEditorTable() && (_delete_Ids.indexOf( ',' + _usedEditors[frameId].getEditorDocumentId() + ',' ) != -1) ) {
					_usedEditors[frameId].setEditorIsHot(false);
					top.weEditorFrameController.closeDocument(frameId);
				}
			}";
	}

	function copyWSfromClass(){
		$DB_WE = new DB_WE();

		$this->setClassProp(); //4076
		$foo = getHash('SELECT Workspaces,Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->ClassID), $DB_WE);

		foreach(array_keys($_REQUEST) as $f){
			if(substr($f, 0, 3) == "weg"){
				$ofid = f("SELECT OF_ID FROM " . OBJECT_X_TABLE . $this->ClassID . ' WHERE ID=' . substr($f, 3), 'OF_ID', $DB_WE);
				if(checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE)){
					$obj = new we_objectFile();
					$obj->initByID($ofid, OBJECT_FILES_TABLE);
					$obj->getContentDataFromTemporaryDocs($ofid);
					$obj->Workspaces = $foo["Workspaces"];
					$obj->Templates = $foo["Templates"];
					$obj->ExtraTemplates = "";
					$obj->ExtraWorkspaces = "";
					$obj->ExtraWorkspacesSelected = "";
					$oldModDate = $obj->ModDate;
					$obj->we_save(0, 1);
					if($obj->Published != 0 && $obj->Published == $oldModDate){
						$obj->we_publish(0, 1, 1);
					}
				}
			}
		}
		return 'top.drawTree();';
	}

	function copyCharsetfromClass(){
		$DB_WE = new DB_WE();
		$this->setClassProp();
		$fooo = unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->ClassID), 'DefaultValues', $DB_WE));

		$Charset = (isset($fooo["elements"]["Charset"]["dat"]) ? $fooo["elements"]["Charset"]["dat"] : DEFAULT_CHARSET );
		// get Class
		$classArray = getHash('SELECT * FROM ' . OBJECT_TABLE . " WHERE Path='" . $this->ClassPath . "'", $DB_WE);
		foreach(array_keys($_REQUEST) as $f){
			if(substr($f, 0, 3) == "weg"){
				$ofid = f("SELECT OF_ID FROM " . OBJECT_X_TABLE . $classArray["ID"] . " WHERE ID=" . substr($f, 3), 'OF_ID', $DB_WE);
				if(checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE)){
					$obj = new we_objectFile();
					$obj->initByID($ofid, OBJECT_FILES_TABLE);
					$obj->getContentDataFromTemporaryDocs($ofid);
					$obj->Charset = $Charset;
					$oldModDate = $obj->ModDate;
					$obj->we_save(0, 1);
					if($obj->Published != 0 && $obj->Published == $oldModDate){
						$obj->we_publish(0, 1, 1);
					}
				}
			}
		}
		return '';
	}

	function copyTIDfromClass(){
		$DB_WE = new DB_WE();
		$this->setClassProp();
		$DefaultTriggerID = f('SELECT DefaultTriggerID FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->ClassID), 'DefaultTriggerID', $DB_WE);

		// get Class
		$classArray = getHash('SELECT * FROM ' . OBJECT_TABLE . " WHERE Path='" . $this->ClassPath . "'", $DB_WE);
		foreach(array_keys($_REQUEST) as $f){
			if(substr($f, 0, 3) == "weg"){
				$ofid = f('SELECT OF_ID FROM ' . OBJECT_X_TABLE . $classArray["ID"] . ' WHERE ID=' . substr($f, 3), 'OF_ID', $DB_WE);
				if(checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE)){
					$obj = new we_objectFile();
					$obj->initByID($ofid, OBJECT_FILES_TABLE);
					$obj->getContentDataFromTemporaryDocs($ofid);
					$obj->TriggerID = $DefaultTriggerID;
					$oldModDate = $obj->ModDate;
					$obj->we_save(0, 1);
					if($obj->Published != 0 && $obj->Published == $oldModDate){
						$obj->we_publish(0, 1, 1);
					}
				}
			}
		}
		return '';
	}

	function searchableObjects($searchable = true){

		$DB_WE = new DB_WE();
		$this->setClassProp();

		// get Class
		$classArray = getHash('SELECT * FROM ' . OBJECT_TABLE . " WHERE Path='" . $this->ClassPath . "'", $DB_WE);
		foreach(array_keys($_REQUEST) as $f){
			if(substr($f, 0, 3) == "weg"){
				$ofid = f('SELECT OF_ID FROM ' . OBJECT_X_TABLE . $classArray["ID"] . " WHERE ID=" . substr($f, 3), 'OF_ID', $DB_WE);

				if(checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE)){
					$obj = new we_objectFile();
					$obj->initByID($ofid, OBJECT_FILES_TABLE);
					$obj->getContentDataFromTemporaryDocs($ofid);
					$obj->IsSearchable = ($searchable != true ? 0 : 1);
					$oldModDate = $obj->ModDate;
					$obj->we_save(0, 1);
					if($obj->Published != 0 && $obj->Published == $oldModDate){
						$obj->we_publish(0, 1, 1);
					}
				}
			}
		}

		return '';
	}

	function publishObjects($publish = true){

		$DB_WE = new DB_WE();
		$this->setClassProp();

		$javascript = "";

		// get Class
		$classArray = getHash('SELECT * FROM ' . OBJECT_TABLE . " WHERE Path='" . $this->ClassPath . "'", $DB_WE);
		foreach(array_keys($_REQUEST) as $f){
			if(substr($f, 0, 3) == "weg"){
				$ofid = f('SELECT OF_ID FROM ' . OBJECT_X_TABLE . $classArray["ID"] . " WHERE ID=" . substr($f, 3), 'OF_ID', $DB_WE);

				if(checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE)){
					if($publish != true){

						$obj = new we_objectFile();
						$obj->initByID($ofid, OBJECT_FILES_TABLE);

						if($obj->we_unpublish()){
							$javascript .= "_EditorFrame = top.weEditorFrameController.getActiveEditorFrame();"
								//.	"_EditorFrame.setEditorDocumentId(".$obj->ID.");\n"
								. $obj->getUpdateTreeScript(false)
								. "if(top.treeData.table!='" . OBJECT_FILES_TABLE . "') {"
								. "top.rframe.we_cmd('loadVTab', '" . OBJECT_FILES_TABLE . "', 0);"
								. "}"
								. "weWindow.treeData.selectnode(" . $GLOBALS['we_doc']->ID . ");";
						}
					} else{

						$obj = new we_objectFile();
						$obj->initByID($ofid, OBJECT_FILES_TABLE);

						$obj->getContentDataFromTemporaryDocs($ofid);

						if($obj->we_publish()){
							$javascript .= "_EditorFrame = top.weEditorFrameController.getActiveEditorFrame();"
								//.	"_EditorFrame.setEditorDocumentId(".$obj->ID.");\n"
								. $obj->getUpdateTreeScript(false)
								. "if(top.treeData.table!='" . OBJECT_FILES_TABLE . "') {"
								. "top.rframe.we_cmd('loadVTab', '" . OBJECT_FILES_TABLE . "', 0);"
								. "}"
								. "weWindow.treeData.selectnode(" . $GLOBALS['we_doc']->ID . ");";
						}
					}
				}
			}
		}

		return $javascript;
	}

}
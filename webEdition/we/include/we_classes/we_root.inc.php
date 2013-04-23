<?php

/**
 * webEdition CMS
 *
 * $Rev: 5878 $
 * $Author: mokraemer $
 * $Date: 2013-02-24 03:42:12 +0100 (Sun, 24 Feb 2013) $
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
/* the parent class for tree-objects */
abstract class we_root extends we_class{

	const USER_HASACCESS = 1;
	const FILE_LOCKED = -3;
	const USER_NO_PERM = -2;
	const USER_NO_SAVE = -4;
	const FILE_NOT_IN_USER_WORKSPACE = -1;

	/* ParentID of the object (ID of the Parent-Folder of the Object) */

	var $ParentID = 0;

	/* Parent Path of the object (Path of the Parent-Folder of the Object) */
	var $ParentPath = '/';

	/* The Text that will be shown in the tree-menue */
	var $Text = '';

	/* Filename of the file */
	var $Filename = '';

	/* Path of the File  */
	var $Path = '';

	/* OldPath of the File => used internal  */
	var $OldPath = '';

	/* Creation Date as UnixTimestamp  */
	var $CreationDate = 0;

	/* Modification Date as UnixTimestamp  */
	var $ModDate = 0;

	/* Flag which is set, when the file is a folder  */
	var $IsFolder = 0;

	/* ContentType of the Object  */
	var $ContentType = '';

	/* Icon which is shown at the tree-menue  */
	public $Icon = '';

	/* array which holds the content of the Object */
	var $elements = array();

	/* Number of the EditPage when editor() is called */
	public $EditPageNr = 1;
	var $CopyID;
	var $EditPageNrs = array();
	var $Owners = '';
	var $OwnersReadOnly = '';
	var $WebUserID = '';

	/* ID of the Autor who created the document */
	var $CreatorID = 0;

	/* ID of the user who last modify the document */
	var $ModifierID = 0;
	var $RestrictOwners = 0;
	protected $DefaultInit = false; // this flag is set when the document was first initialized with default values e.g. from Doc-Types

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->CreationDate = time();
		$this->ModDate = time();
		array_push($this->persistent_slots, 'OwnersReadOnly', 'ParentID', 'ParentPath', 'Text', 'Filename', 'Path', 'OldPath', 'CreationDate', 'ModDate', 'IsFolder', 'ContentType', 'Icon', 'elements', 'EditPageNr', 'CopyID', 'Owners', 'CreatorID', 'ModifierID', 'DefaultInit', 'RestrictOwners', 'WebUserID');
	}

	function makeSameNew(){
		$ParentID = $this->ParentID;
		$ParentPath = $this->ParentPath;
		$EditPageNr = $this->EditPageNr;
		$tempDoc = $this->ClassName;
		$tempDoc = new $tempDoc();
		$tempDoc->we_new();
		foreach($tempDoc->persistent_slots as $name){
			$this->{$name} = isset($tempDoc->{$name}) ? $tempDoc->{$name} : '';
		}
		$this->InWebEdition = true;
		$this->ParentID = $ParentID;
		$this->ParentPath = $ParentPath;
		$this->EditPageNr = $EditPageNr;
	}

	function equals($obj){
		foreach($this->persistent_slots as $cur){
			switch($cur){
				case 'Name':
				case 'elements':
				case 'EditPageNr':
				case 'wasUpdate':
					continue;
				default:
					if($this->{$cur} != $obj->{$cur}){
						return false;
					}
			}
		}
		foreach($this->elements as $key => $val){
			if($val['dat'] != $obj->elements[$key]['dat'] || $val['bdid'] != $obj->elements[$key]['bdid']){
				return false;
			}
		}
		return true;
	}

	function setParentID($newID){
		$this->ParentID = $newID;
		$this->ParentPath = $this->getParentPath();
	}

	function ModifyPathInformation($parentID){
		$this->setParentID($parentID);
		$this->Path = $this->getPath();
		$this->wasUpdate = 1;
		$this->we_save(); //i_savePersistentSlotsToDB("Filename,Extension,Text,Path,ParentID");
		$this->modifyChildrenPath(); // only on folders, because on other classes this function is empty
	}

	function modifyChildrenPath(){
		// do nothing, only in Folder-Classes this Function schould have code!!
	}

	function checkIfPathOk(){
		### check if Path has changed
		$Path = $this->getPath();
		if($Path != $this->Path){

			### check if Path exists in db
			if(f('SELECT Path FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE Path="' . $this->DB_WE->escape($Path) . '"', 'Path', $this->DB_WE)){
				$GLOBALS['we_responseText'] = sprintf(g_l('weClass', '[response_path_exists]'), $Path);
				return false;
			}
			$this->Path = $Path;
		}
		return true;
	}

	function saveInSession(&$save){
		$save = array(
			array(),
			$this->elements
		);
		foreach($this->persistent_slots as $slot){
			$bb = isset($this->{$slot}) ? $this->{$slot} : '';
			if(!is_object($bb)){
				$save[0][$slot] = $bb;
			} else{
				$save[0][$slot . '_class'] = serialize($bb);
			}
		}
		// save weDocumentCustomerFilter in Session
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$save[3] = serialize($this->documentCustomerFilter);
		}
	}

	function applyWeDocumentCustomerFilterFromFolder(){
		if(isset($this->documentCustomerFilter) && defined('CUSTOMER_TABLE')){
			$_tmpFolder = new we_folder();
			$_tmpFolder->initByID($this->ParentID, $this->Table);
			$this->documentCustomerFilter = $_tmpFolder->documentCustomerFilter;

			if($this->IsFolder){
				$this->ApplyWeDocumentCustomerFiltersToChilds = true;
			}
			unset($_tmpFolder);
		}
	}

	/** load  data in the object from $filename */
	function loadFromFile($filename){
		$str = weFile::load($filename);
		if($str){
			$arr = unserialize($str);
			foreach($this->persistent_slots as $cur){
				if(isset($arr[0][$cur])){
					$this->{$cur} = $arr[0][$cur];
				}
			}
			if(isset($arr[1])){
				$this->elements = $arr[1];
			}
			return true;
		} else{
			return false;
		}
	}

	/* init the object with data from the database */

	function copyDoc(/* $id */){
		// overwrite
	}

######### Form functions for generating the html of the input fields ##########

	/* creates a text-input field for entering Data that will be stored at the $elements Array */

	/* creates the filename input-field */

	function formFilename($text = ''){
		return $this->formTextInput('', 'Filename', $text ? $text : g_l('weClass', '[filename]'), 24, 255);
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = 0, $rootDirID = 0, $table = '', $Pathname = 'ParentPath', $IDName = 'ParentID', $cmd = '', $showTitle = true){
		$yuiSuggest = & weSuggest::getInstance();

		if(!$table){
			$table = $this->Table;
		}
		$textname = 'we_' . $this->Name . '_' . $Pathname;
		$idname = 'we_' . $this->Name . '_' . $IDName;
		$path = $this->$Pathname;
		$myid = $this->$IDName;

		$_parentPathChanged = '';
		$_parentPathChangedBlur = '';
		if($Pathname == 'ParentPath'){
			$_parentPathChanged = 'if (opener.pathOfDocumentChanged) { opener.pathOfDocumentChanged(); }';
			$_parentPathChangedBlur = 'if (pathOfDocumentChanged) { pathOfDocumentChanged(); }';
		}

		if($width){
			$_attribs['style'] = 'width: ' . $width . 'px';
		} else{
			$width = 0;
		}
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);" . $_parentPathChanged . str_replace('\\', '', $cmd));
		$button = we_button::create_button('select', "javascript:we_cmd('openDirselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID')");

		$yuiSuggest->setAcId('Path', id_to_path(array($rootDirID), $table));
		$yuiSuggest->setContentType('folder,class_folder');
		$yuiSuggest->setInput($textname, $path, array('onBlur' => $_parentPathChangedBlur));
		$yuiSuggest->setLabel(g_l('weClass', '[dir]'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector('Dirselector');
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);
		return $yuiSuggest->getHTML();
		//return $yuiSuggest->getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiCode();
	}

	function htmlTextInput_formDirChooser($attribs = array(), $addAttribs = array()){
		$_attribs = array(
			'onfocus' => "this.className='wetextinputselected';",
			'onblur' => "this.className='wetextinput';",
			'class' => 'wetextinput',
			'size' => 30,
			'value' => '',
		);

		foreach($addAttribs as $key => $value){
			if(isset($_attribs[$key])){
				$_attribs[$key] .= $value;
			} else{
				$_attribs[$key] = $value;
			}
		}

		foreach($attribs as $key => $value){
			$_attribs[$key] = $value;
		}

		$_attribs['type'] = 'text';

		return getHtmlTag('input', $_attribs);
	}

	function formCreator($canChange, $width = 388){
		if(!$this->CreatorID)
			$this->CreatorID = 0;

		$creator = $this->CreatorID ? id_to_path($this->CreatorID, USER_TABLE, $this->DB_WE) : g_l('weClass', '[nobody]');


		if($canChange){

			$textname = 'wetmp_' . $this->Name . '_CreatorID';
			$idname = 'we_' . $this->Name . '_CreatorID';

			$attribs = ' readonly';

			$inputFeld = $this->htmlTextInput($textname, 24, $creator, '', $attribs, '', $width);
			$idfield = $this->htmlHidden($idname, $this->CreatorID);
			//javascript:we_cmd('browse_users','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','user',document.forms[0].elements['$idname'].value,'opener._EditorFrame.setEditorIsHot(true);')
			$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
			$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
			$wecmdenc5 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);");
			$button = we_button::create_button('edit', "javascript:we_cmd('browse_users','" . $wecmdenc1 . "','" . $wecmdenc2 . "','user',document.forms[0].elements['$idname'].value,'" . $wecmdenc5 . "')");

			$out = $this->htmlFormElementTable($inputFeld, g_l('weClass', '[maincreator]'), 'left', 'defaultfont', $idfield, we_html_tools::getPixel(20, 4), $button);
		} else{
			$out = $creator;
		}
		return $out;
	}

	function formRestrictOwners($canChange){
		if($canChange){
			$n = 'we_' . $this->Name . '_RestrictOwners';
			$v = $this->RestrictOwners ? true : false;
			return we_forms::checkboxWithHidden($v ? true : false, $n, g_l('weClass', '[limitedAccess]'), false, 'defaultfont', "setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('reload_editpage');");
		} else{
			return '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="' . TREE_IMAGE_DIR . ($this->RestrictOwners ? 'check1_disabled.gif' : 'check0_disabled.gif') . '" /></td><td class="defaultfont">&nbsp;' . g_l('weClass', "[limitedAccess]") . '</td></tr></table>';
		}
	}

	function formOwners($canChange = true){
		$owners = makeArrayFromCSV($this->Owners);
		$ownersReadOnly = $this->OwnersReadOnly ? unserialize($this->OwnersReadOnly) : array();

		$content = '<table border="0" cellpadding="0" cellspacing="0" width="370">';
		$content .= '<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(351, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr>' . "\n";
		if(count($owners)){
			foreach($owners as $owner){
				$foo = getHash('SELECT ID,Path,Icon from ' . USER_TABLE . ' WHERE ID=' . intval($owner), $this->DB_WE);
				$icon = isset($foo['Icon']) ? ICON_DIR . $foo['Icon'] : ICON_DIR . 'user.gif';
				$_path = isset($foo['Path']) ? $foo['Path'] : '';
				$content .= '<tr><td><img src="' . $icon . '" width="16" height="18" /></td><td class="defaultfont">' . $_path . '</td><td>' .
					we_forms::checkboxWithHidden(isset($ownersReadOnly[$owner]) ? $ownersReadOnly[$owner] : '', 'we_owners_read_only[' . $owner . ']', g_l('weClass', '[readOnly]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);', !$canChange) .
					'</td><td>' . ($canChange ? we_button::create_button('image:btn_function_trash', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('del_owner','" . $owner . "');") : '') . '</td></tr>' . "\n";
			}
		} else{
			$content .= '<tr><td><img src="' . ICON_DIR . "user.gif" . '" width="16" height="18" /></td><td class="defaultfont">' . g_l('weClass', "[onlyOwner]") . '</td><td></td><td></td></tr>' . "\n";
		}
		$content .= '<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(351, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr></table>' . "\n";

		$textname = 'OwnerNameTmp';
		$idname = 'OwnerIDTmp';
		$delallbut = we_button::create_button('delete_all', "javascript:we_cmd('del_all_owners','')", true, -1, -1, "", "", $this->Owners ? false : true);
		//javascript:we_cmd('browse_users','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','',document.forms[0].elements['$idname'].value,'opener._EditorFrame.setEditorIsHot(true);opener.setScrollTo();fillIDs();opener.we_cmd(\\'add_owner\\',top.allIDs)','','',1);
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
		$wecmdenc5 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.setScrollTo();fillIDs();opener.we_cmd('add_owner',top.allIDs);");
		$addbut = $canChange ?
			$this->htmlHidden($idname, '') . $this->htmlHidden($textname, '') . we_button::create_button('add', "javascript:we_cmd('browse_users','" . $wecmdenc1 . "','" . $wecmdenc2 . "','',document.forms[0].elements['$idname'].value,'" . $wecmdenc5 . "','','',1);") : "";

		$content = '<table border="0" cellpadding="0" cellspacing="0" width="500">
<tr><td><div class="multichooser">' . $content . '</div></td></tr>
' . ($canChange ? '<tr><td align="right">' . we_html_tools::getPixel(2, 8) . '<br>' . we_button::create_button_table(array($delallbut, $addbut)) . '</td></tr>' : "") . '</table' . "\n";

		return $this->htmlFormElementTable($content, g_l('weClass', '[otherowners]'), 'left', 'defaultfont');
	}

	function formCreatorOwners(){
		$width = 388;
		$canChange = (!$this->ID) || we_users_util::isUserInUsers($_SESSION['user']['ID'], $GLOBALS['we_doc']->CreatorID);

		$out = '<table border="0" cellpadding="0" cellspacing="0">
<tr><td class="defaultfont">' . $this->formCreator($canChange, $width) . '</td></tr>
<tr><td>' . we_html_tools::getPixel(2, 20) . '</td></tr>
<tr><td>' . $this->formRestrictOwners($canChange) . '</td></tr>
';
		if($this->RestrictOwners){
			$out .= '<tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>
<tr><td>' . $this->formOwners($canChange) . '</td></tr>';
		}
		$out .= '</table>';

		return $out;
	}

	function del_all_owners(){
		$this->Owners = '';
	}

	function add_owner($id){

		$owners = makeArrayFromCSV($this->Owners);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id, $owners))){
				$owners[] = $id;
			}
		}
		$this->Owners = makeCSVFromArray($owners, true);
	}

	function del_owner($id){
		$owners = makeArrayFromCSV($this->Owners);
		if(in_array($id, $owners)){
			$pos = getArrayKey($id, $owners);
			if($pos != '' || $pos == '0'){
				array_splice($owners, $pos, 1);
			}
		}
		$this->Owners = makeCSVFromArray($owners, true);
	}

	/**
	 * @return bool
	 * @desc	checks if a document is restricted to several users and if
	  the user is one of the restricted users
	 */
	function userHasPerms(){
		if($_SESSION['perms']['ADMINISTRATOR'] || !$this->RestrictOwners || we_isOwner($this->Owners) || we_isOwner($this->CreatorID)){
			return true;
		}
		return false;
	}

	function userIsCreator(){
		if($_SESSION['perms']['ADMINISTRATOR']){
			return true;
		}
		return we_isOwner($this->CreatorID);
	}

	function userCanSave(){
		if($_SESSION['perms']['ADMINISTRATOR']){
			return true;
		}
		if(defined('OBJECT_TABLE') && ($this->Table == OBJECT_FILES_TABLE)){
			if(!(we_hasPerm('NEW_OBJECTFILE_FOLDER') || we_hasPerm('NEW_OBJECTFILE')))
				return false;
		}else{
			if(!we_hasPerm('SAVE_DOCUMENT_TEMPLATE'))
				return false;
		}
		if(!$this->RestrictOwners)
			return true;
		if(!$this->userHasPerms())
			return false;
		$ownersReadOnly = $this->OwnersReadOnly ? unserialize($this->OwnersReadOnly) : array();
		$readers = array();
		foreach(array_keys($ownersReadOnly) as $key){
			if(isset($ownersReadOnly[$key]) && $ownersReadOnly[$key] == 1)
				$readers[] = $key;
		}
		return !we_users_util::isUserInUsers($_SESSION['user']['ID'], $readers);
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		//javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','top.opener._EditorFrame.setEditorIsHot(true);','".session_id()."','$rootDir','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true); opener.top.we_cmd('copyDocument', currentID);");
		$but = we_button::create_button("select", "javascript:we_cmd('openDocselector', document.forms[0].elements['" . $idname . "'].value, '" . $this->Table . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "', '0', '" . $this->ContentType . "',1);");

		return $this->htmlHidden($idname, $this->CopyID) . $but;
	}

	# return html code for button and field to select user
	# ATTENTION !!: You have to have we_cmd function in your file and browse_user section

	#
	function formUserChooser($old_userID = -1, $width = '', $in_textname = '', $in_idname = ''){
		$textname = $in_textname == '' ? 'we_' . $this->Name . '_UserName' : $in_textname;
		$idname = $in_idname == '' ? 'we_' . $this->Name . '_UserID' : $in_idname;

		$username = '';
		$userid = $old_userID;

		if((int) $userid > 0){
			$username = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userid), 'username', $this->DB_WE);
		}

		//javascript:we_cmd('browse_users','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','user')
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
		return we_root::htmlFormElementTable(we_root::htmlTextInput($textname, 30, $username, '', ' readonly', 'text', $width, 0), 'User', 'left', 'defaultfont', we_root::htmlHidden($idname, $userid), we_html_tools::getPixel(20, 4), we_button::create_button('select', "javascript:we_cmd('browse_users','" . $wecmdenc1 . "','" . $wecmdenc2 . "','user')"));
	}

	function formTriggerDocument($isclass = false){
		$yuiSuggest = & weSuggest::getInstance();
		$table = FILE_TABLE;
		if($isclass){
			$textname = 'we_' . $this->Name . '_TriggerName';
			$idname = 'we_' . $this->Name . '_DefaultTriggerID';
			$myid = $this->DefaultTriggerID ? $this->DefaultTriggerID : '';
		} else{
			$textname = 'we_' . $this->Name . '_TriggerName';
			$idname = 'we_' . $this->Name . '_TriggerID';
			$myid = $this->TriggerID ? $this->TriggerID : '';
		}
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), 'Path', $this->DB_WE);
		//javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','','document.we_form.elements[\\'$textname\\'].value','opener._EditorFrame.setEditorIsHot(true);','".session_id()."','','text/webedition',1)"
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);");
		$button = we_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','','text/webedition',1)");
		$trashButton = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['$idname'].value='';document.we_form.elements['$textname'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputTriggerID');_EditorFrame.setEditorIsHot(true);", true, 27, 22);

		$yuiSuggest->setAcId('TriggerID');
		$yuiSuggest->setContentType('folder,text/webedition');
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setLabel(g_l('modules_object', '[seourltrigger]'));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector('Docselector');
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(388);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setTrashButton($trashButton);
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(yuiAcFields[yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
		return $yuiSuggest->getHTML();
	}

	function formLanguageDocument($headline, $langkey, $LDID = 0, $table = FILE_TABLE, $rootDirID = 0){
		$yuiSuggest = & weSuggest::getInstance();
		$textname = 'we_' . $this->Name . '_LanguageDocName[' . $langkey . ']';
		$idname = 'we_' . $this->Name . '_LanguageDocID[' . $langkey . ']';
		$ackeyshort = 'LanguageDoc' . str_replace('_', '', $langkey);
		//$textname = 'we_'.$this->Name.'_LanguageDocNam-'.$langkey;
		//$idname = 'we_'.$this->Name.'_LanguageDocID-'.$langkey;
		//$myid = $this->TriggerID ? $this->TriggerID : '';
		$myid = $LDID ? $LDID : '';
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), 'Path', $this->DB_WE);
		if($rootDirID && $path == ''){
			$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($rootDirID), 'Path', $this->DB_WE);
		}
		$yuiSuggest->setAcId($ackeyshort, $path);
		if($table == FILE_TABLE){
			$yuiSuggest->setContentType('folder,text/webedition');
			$ctype = 'text/webedition';
			$etype = FILE_TABLE;
		} else{
			$yuiSuggest->setContentType('folder,objectFile');
			$ctype = 'objectFile';
			$etype = OBJECT_FILES_TABLE;
		}
		//javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','document.we_form.elements[\\'$idname\\'].value','document.we_form.elements[\\'$textname\\'].value','opener._EditorFrame.setEditorIsHot(true);','".session_id()."','" . $rootDir . "','".$ctype."',1)
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc('opener._EditorFrame.setEditorIsHot(true);');

		$button = we_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','" . $rootDirID . "','" . $ctype . "',1)");
		$trashButton = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['$idname'].value='-1';document.we_form.elements['$textname'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInput" . $ackeyshort . "');_EditorFrame.setEditorIsHot(true);", true, 27, 22);
		$openbutton = we_button::create_button("image:edit_edit", "javascript:if(document.we_form.elements['$idname'].value){top.doClickDirect(document.we_form.elements['$idname'].value,'" . $ctype . "','" . $etype . "'); }");
		if(isset($this->DocType) && $this->DocType && we_hasPerm("NEW_WEBEDITIONSITE")){
			$LDcoType = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblDocTypes" AND DID=' . $this->DocType . ' AND Locale="' . $langkey . '"', 'LDID', new DB_WE());
			if($LDcoType){
				$createbutton = we_button::create_button("image:add_doc", "javascript:top.we_cmd('new','" . FILE_TABLE . "','','text/webedition','" . $LDcoType . "');");
				$yuiSuggest->setCreateButton($createbutton);
			}
		}
		$yuiSuggest->setInput($textname, $path, '', true);
		//$yuiSuggest->setInput($textname);
		$yuiSuggest->setLabel($headline);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector('Docselector');
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(388);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setTrashButton($trashButton);
		$yuiSuggest->setOpenButton($openbutton);
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		//$yuiSuggest->setDoOnTextfieldBlur("if(yuiAcFields[yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");
		return $yuiSuggest->getHTML();
	}

	#################### Function for getting and setting the $elements Array #########################################################################

	/* returns true if the element with the name $name is set */

	function issetElement($name){
		return isset($this->elements[$name]);
	}

	/* set the Data for an element */

	function setElement($name, $data, $type = 'txt', $id = 0, $autobr = 0){
		$this->elements[$name]['dat'] = $data;
		$this->elements[$name]['type'] = $type;
		if($id){
			$this->elements[$name]['id'] = $id;
		}
		if($autobr){
			$this->elements[$name]['autobr'] = $autobr;
		}
	}

	/* get the data from an element */

	function getElement($name, $key = 'dat'){
		return (isset($this->elements[$name][$key]) ? $this->elements[$name][$key] : '');
	}

	/* reset the array-pointer (for use with nextElement()) */

	function resetElements(){
		if(is_array($this->elements))
			reset($this->elements);
	}

	/* returns the next element or false if the array-pointer is at the end of the array */

	function nextElement($type = 'txt'){
		if(is_array($this->elements)){
			while($arr = each($this->elements)) {
				if((isset($arr['value']['type']) && $arr['value']['type'] == $type) || $type == ''){
					return $arr;
				}
			}
		}
		return false;
	}

	##### Functions for generating JavaScrit to update the document tree

	/* returns the JavaScript-Code which modifies the tree-menue */

	function getUpdateTreeScript($select = true){
		return $this->getMoveTreeEntryScript($select);
	}

	function getMoveTreeEntryScript($select = true){
		$Tree = new weMainTree('webEdition.php', 'top', 'self.Tree', 'top.load');
		return $Tree->getJSUpdateTreeScript($this, $select);
	}

	/** returns the Path dynamically (use it, when the class-variable Path is not set)  */
	function getPath(){
		return rtrim($this->getParentPath(), '/') . '/' . ( isset($this->Filename) ? $this->Filename : '' ) . ( isset($this->Extension) ? $this->Extension : '' );
	}

	/** get the Path of the Parent-Object */
	function getParentPath(){
		return (!$this->ParentID) ? '/' : f('SELECT Path FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($this->ParentID), 'Path', $this->DB_WE);
	}

	function constructPath(){
		if($this->ID){
			$pid = $this->ParentID;
			$p = '/' . $this->Text;
			$z = 0;
			while($pid && $z < 50) {
				list($pid, $text) = getHash('SELECT ParentID,Text FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($pid), $this->DB_WE);
				$p = '/' . $text . $p;
				$z++;
			}
			if($z >= 50){
				return false;
			}
			return $p;
		}
		return false;
	}

	/* get the Real-Path of the Object (Server-Path) */

	function getRealPath($old = false){
		return (($this->Table == FILE_TABLE) ? $_SERVER['DOCUMENT_ROOT'] : TEMPLATES_PATH) . ($old ? $this->OldPath : $this->getPath());
	}

	/* get the Site-Path of the Object */

	function getSitePath($old = false){
		return $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . substr(($old ? $this->OldPath : $this->getPath()), 1);
	}

	/* get the HTTP-Path of the Object */

	function getHttpPath(){
		return getServerUrl() . $this->getPath();
	}

	/* get the HTTP-Path of the Object */

	function getHttpSitePath(){
		return getServerUrl() . SITE_DIR . substr($this->getPath(), 1);
	}

	function editor(){

	}

	function getParentIDFromParentPath(){
		return 0;
	}

	function makeHrefByID($id, $db = ''){
		return f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'Path', $this->DB_WE);
	}

	function save($resave = 0, $skipHook = 0){
		return $this->we_save($resave, $skipHook);
	}

# public ##################

	public function we_new(){
		parent::we_new();
		$this->CreatorID = isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;
		if(isset($this->ContentType) && $this->ContentType){
			$this->Icon = we_base_ContentTypes::inst()->getIcon($this->ContentType);
		}
		$this->ParentPath = $this->getParentPath();
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);

		$this->i_getContentData($this->LoadBinaryContent);
		$this->OldPath = $this->Path;
	}

	public function we_save($resave = 0){
		//$this->i_setText;
		if($this->PublWhenSave){
			$this->Published = time();
		}
		if($resave == 0){
			$this->ModDate = time();
			$this->ModifierID = isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;
		}
		if(!parent::we_save($resave)){
			return false;
		}
		$a = $this->i_saveContentDataInDB();
		if($resave == 0 && $this->ClassName != 'we_class_folder'){
			we_history::insertIntoHistory($this);
		}
		return $a;
	}

	/**
	 * resave weDocumentCustomerFilter
	 *
	 */
	function resaveWeDocumentCustomerFilter(){
		if(isset($this->documentCustomerFilter) && $this->documentCustomerFilter){
			weDocumentCustomerFilter::saveForModel($this);
		}
	}

	public function we_delete(){
		if(!parent::we_delete()){
			return false;
		}
		return deleteContentFromDB($this->ID, $this->Table, $this->DB_WE);
	}

	protected function i_getDefaultFilename(){
		return f('SELECT MAX(ID) as ID FROM ' . $this->DB_WE->escape($this->Table), 'ID', $this->DB_WE) + 1;
	}

	function we_initSessDat($sessDat){
		we_class::we_initSessDat($sessDat);
		if(is_array($sessDat)){
			foreach($this->persistent_slots as $cur){
				if(isset($sessDat[0][$cur])){
					$this->{$cur} = $sessDat[0][$cur];
				}
			}
			if(isset($sessDat[1])){
				$this->elements = $sessDat[1];
			}
		}
		$this->i_setElementsFromHTTP();
	}

	protected function i_initSerializedDat($sessDat){
		if(!is_array($sessDat)){
			$this->Name = md5(uniqid(__FUNCTION__, true));
			return false;
		}
		foreach($this->persistent_slots as $cur){
			if(isset($sessDat[0][$cur])){
				$this->{$cur} = $sessDat[0][$cur];
			}
		}
		if(isset($sessDat[1])){
			$this->elements = $sessDat[1];
		}
		if(isset($sessDat[2])){
			$this->NavigationItems = $sessDat[2];
		} else{
			$this->i_loadNavigationItems();
		}

		$this->Name = md5(uniqid(__FUNCTION__, true));
		return true;
	}

# private ###################

	protected function i_setText(){
		$this->Text = $this->Filename;
	}

	protected function i_convertElemFromRequest($type, &$v, $k){
		switch($type){
			case 'float':
				$v = floatval(str_replace(',', '.', $v));
				break;
			case 'int':
				$v = intval($v);
				break;
			case 'text':
				if($this->DefArray[$type . '_' . $k]['dhtmledit'] == 'on'){
					$v = we_util::rmPhp($v);
					break;
				}
			case 'input':
				if($this->DefArray[$type . '_' . $k]['forbidphp'] == 'on'){
					$v = we_util::rmPhp($v);
				}
				if($this->DefArray[$type . '_' . $k]['forbidhtml'] == 'on'){
					$v = removeHTML($v);
				}
				break;
			case 'internal'://pseudo-element for i_setElementsFromHTTP
				break;
			default:
				$v = removeHTML(we_util::rmPhp($v));
				break;
		}
	}

	protected function i_set_PersistentSlot($name, $value){
		if(in_array($name, $this->persistent_slots)){
			$this->$name = $value;
		}
	}

	protected function i_setElementsFromHTTP(){
		// do not set REQUEST VARS into the document
		if(isset($_REQUEST['we_cmd'][0])){
			if(($_REQUEST['we_cmd'][0] == 'switch_edit_page' && isset($_REQUEST['we_cmd'][3])) || ($_REQUEST['we_cmd'][0] == 'save_document' && isset($_REQUEST['we_cmd'][7]) && $_REQUEST['we_cmd'][7] == 'save_document')){
				return true;
			}
		}
		$regs = array();
		if(!empty($_REQUEST)){
			$dates = array();
			foreach($_REQUEST as $n => $v){
				if(preg_match('/^we_' . preg_quote($this->Name) . '_([^\[]+)$/', $n, $regs)){
					if(is_array($v)){
						$type = $regs[1];
						foreach($v as $name => $v2){
							$v2 = we_util::cleanNewLine($v2);
							if($type == 'date'){
								preg_match('|(.*)_(.*)|', $name, $regs);
								list(, $name, $what) = $regs;
								$dates[$name][$what] = $v2;
							} else{
								if(preg_match('/(.+)#(.+)/', $name, $regs)){
									$this->elements[$regs[1]]['type'] = $type;
									$this->elements[$regs[1]][$regs[2]] = $v2;
								} else{
									$this->elements[$name]['type'] = $type;
									//FIXME: check if we can apply the correct type
									$this->i_convertElemFromRequest('internal', $v2, $name);
									$this->elements[$name]['dat'] = $v2;
								}
							}
						}
					} else{
						$this->i_set_PersistentSlot($regs[1], $v);
					}
				} else if($n == 'we_owners_read_only'){
					$this->OwnersReadOnly = serialize($v);
				}
			}
			foreach($dates as $k => $v){
				$this->elements[$k]['type'] = 'date';
				$this->elements[$k]['dat'] = mktime($dates[$k]['hour'], $dates[$k]['minute'], 0, $dates[$k]['month'], $dates[$k]['day'], $dates[$k]['year']);
			}
		}
		$this->Path = $this->getPath();
	}

	protected function i_isElement(/* $Name */){
		return true; // overwrite
	}

	protected function i_getContentData($loadBinary = 0){

		$this->DB_WE->query('SELECT * FROM ' . CONTENT_TABLE . ',' . LINK_TABLE . ' WHERE ' . LINK_TABLE . '.DID=' . intval($this->ID) .
			' AND ' . LINK_TABLE . '.DocumentTable="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) .
			'" AND ' . CONTENT_TABLE . '.ID=' . LINK_TABLE . '.CID ' .
			($loadBinary ? '' : ' AND ' . CONTENT_TABLE . '.IsBinary=0'));
		$filter = array('Name', 'DID', 'Ord');
		while($this->DB_WE->next_record()) {
			$Name = $this->DB_WE->f('Name');
			$type = $this->DB_WE->f('Type');

			if($type == 'formfield'){ // Artjom garbage fix!
				$this->elements[$Name] = unserialize($this->DB_WE->f('Dat'));
			} else{
				if($this->i_isElement($Name)){
					foreach($this->DB_WE->Record as $k => $v){
						if(!in_array($k, $filter) && !is_numeric($k)){
							$k = strtolower($k);
							$this->elements[$Name][$k] = $v;
						}
					}
					$this->elements[$Name]['table'] = CONTENT_TABLE;
				}
			}
		}
	}

	private function getLinkReplaceArray(){
		$ret = array();
		$this->DB_WE->query('SELECT CONCAT_WS("_",Type,Name) AS Name,CID FROM ' . LINK_TABLE . ' WHERE DID=' . $this->ID . ' AND DocumentTable="' . stripTblPrefix($this->Table) . '"');
		while($this->DB_WE->next_record()) {
			$ret[$this->DB_WE->f('Name')] = $this->DB_WE->f('CID');
		}
		return $ret;
	}

	function i_saveContentDataInDB(){
		if(!is_array($this->elements)){
			return deleteContentFromDB($this->ID, $this->Table, $this->DB_WE);
		}
		//don't stress index:
		$replace = $this->getLinkReplaceArray();
		foreach($this->elements as $k => $v){
			if($this->i_isElement($k)){
				if((!isset($v['type']) || $v['type'] != 'vars') && (( isset($v['dat']) && $v['dat'] != '' ) || (isset($v['bdid']) && $v['bdid']) || (isset($v['ffname']) && $v['ffname']))){

					$tableInfo = $this->DB_WE->metadata(CONTENT_TABLE);
					$data = array();
					foreach($tableInfo as $t){
						$fieldName = $t['name'];
						$val = isset($v[strtolower($fieldName)]) ? $v[strtolower($fieldName)] : '';
						if($k == 'data' && $this->isBinary()){
							break;
						}
						if($fieldName == 'Dat' && (isset($v['ffname']) && $v['ffname'])){
							$v['type'] = 'formfield';
							$val = serialize($v);
							// Artjom garbage fix
						}

						if(!isset($v['type']) || $v['type'] == ''){
							$v['type'] = 'txt';
						}
						if($v['type'] == 'date'){
							$val = sprintf('%016d', $val);
						}
						if($fieldName != 'ID'){
							$data[$fieldName] = is_array($val) ? serialize($val) : $val;
						}
					}
					if(count($data)){
						$data = we_database_base::arraySetter($data);
						$key = $v['type'] . '_' . $k;
						$cid = 0;
						if(isset($replace[$key])){
							$cid = $replace[$key];
							$data.=',ID=' . $cid;
							unset($replace[$key]);
						}
						$this->DB_WE->query('REPLACE INTO ' . CONTENT_TABLE . ' SET ' . $data);
						$cid = $cid ? $cid : $this->DB_WE->getInsertId();
						$this->elements[$k]['id'] = $cid; // update Object itself
						$q = 'REPLACE INTO ' . LINK_TABLE . " (DID,CID,Name,Type,DocumentTable) VALUES ('" . intval($this->ID) . "'," . $cid . ",'" . $this->DB_WE->escape($k) . "','" . $this->DB_WE->escape($v["type"]) . "','" . $this->DB_WE->escape(stripTblPrefix($this->Table)) . "')";
						if(!$cid || !$this->DB_WE->query($q)){
							//this should never happen
							return false;
						}
					}
				}
			}
		}

		$replace = implode(',', $replace);
		if($replace){
			/* 			t_e($replace,$this);
			  exit(); */
			$this->DB_WE->query('DELETE FROM ' . LINK_TABLE . ' WHERE DocumentTable="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '" AND CID IN(' . $replace . ')');
			$this->DB_WE->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (' . $replace . ')');
		}
		return true;
	}

	protected function i_getPersistentSlotsFromDB($felder = '*'){
		parent::i_getPersistentSlotsFromDB($felder);
		$this->ParentPath = $this->getParentPath();
	}

	function i_areVariantNamesValid(){
		return true;
	}

	function i_canSaveDirinDir(){
		return true;
	}

	function i_sameAsParent(){
		return false;
	}

	function i_filenameEmpty(){
		return ($this->Filename == '');
	}

	function i_pathNotValid(){
		return strpos($this->ParentPath, '..') !== false || $this->ParentPath{0} != '/';
	}

	function i_filenameNotValid(){
		return we_filenameNotValid($this->Filename);
	}

	function i_filenameNotAllowed(){
		if($this->Table == FILE_TABLE && $this->ParentID == 0 && strtolower($this->Filename . (isset($this->Extension) ? $this->Extension : '')) == 'webedition'){
			return true;
		}
		if(substr(strtolower($this->Filename . (isset($this->Extension) ? $this->Extension : '')), -1) == '.'){
			return true;
		}
		return false;
	}

	function i_fileExtensionNotValid(){
		if(isset($this->Extension)){
			$ext = (substr($this->Extension, 0, 1) == '.' ?
					substr($this->Extension, 1) :
					$this->Extension);

			return !(preg_match('/^[a-zA-Z0-9]+$/iD', $ext) || $ext == '');
		}
		return false;
	}

	function i_filenameDouble(){
		return f('SELECT ID FROM ' . $this->Table . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Filename="' . escape_sql_query($this->Filename) . '" AND ID != ' . intval($this->ID), 'ID', $this->DB_WE);
	}

	function i_urlDouble(){
		return false;
	}

	### check if ParentPath is diffrent as ParentID, so we need to look what ParentID it is.
	### If it donesn't exists we have to create the folders (for auto Date-Folder Names)

	function i_checkPathDiffAndCreate(){
		if($this->getParentPath() != $this->ParentPath && $this->ParentPath != '' && $this->ParentPath != '/'){
			if(!$this->IsTextContentDoc || empty($this->DocType)){
				return false;
			} else if($this->IsTextContentDoc && $this->DocType){
				$doctype = new we_docTypes();
				$doctype->initByID($this->DocType, DOC_TYPES_TABLE);
				if(empty($doctype->SubDir)){
					return false;
				}
				$_pathFirstPart = substr($this->getParentPath(), -1) == '/' ? '' : '/';
				$tail = '';
				switch($doctype->SubDir){
					case we_class::SUB_DIR_YEAR:
						$tail = $_pathFirstPart . date('Y');
						break;
					case we_class::SUB_DIR_YEAR_MONTH:
						$tail = $_pathFirstPart . date('Y') . '/' . date('m');
						break;
					case we_class::SUB_DIR_YEAR_MONTH_DAY:
						$tail = $_pathFirstPart . date('Y') . '/' . date('m') . '/' . date('d');
						break;
				}
				if($this->getParentPath() . $tail != $this->ParentPath){
					return false;
				}
			}

			$this->ParentID = $this->getParentIDFromParentPath();
			$this->Path = $this->getPath();
		}
		if($this->ParentID == -1){
			return false;
		}
		return true;
	}

	function i_correctDoublePath(){
		if($this->Filename){
			if(f('SELECT ID  FROM  ' . $this->DB_WE->escape($this->Table) . '  WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($this->Filename . (isset($this->Extension) ? $this->Extension : '')) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)){
				$z = 0;
				$footext = $this->Filename . '_' . $z . (isset($this->Extension) ? $this->Extension : '');
				while(f('SELECT ID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($footext) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)) {
					$z++;
					$footext = $this->Filename . '_' . $z . (isset($this->Extension) ? $this->Extension : '');
				}
				$this->Text = $footext;
				$this->Filename = $this->Filename . '_' . $z;
				$this->Path = $this->getParentPath() . (($this->getParentPath() != '/') ? '/' : '') . $this->Text;
			}
		} else{
			if(f('SELECT ID  FROM  ' . $this->DB_WE->escape($this->Table) . '  WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($this->Text) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)){
				$z = 0;
				$footext = $this->Text . '_' . $z;
				while(f('SELECT ID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID!=' . intval($this->ID) . ' AND Text="' . $this->DB_WE->escape($footext) . '" AND ParentID=' . intval($this->ParentID), 'ID', $this->DB_WE)) {
					$z++;
					$footext = $this->Text . '_' . $z;
				}
				$this->Text = $footext;
				$this->Path = $this->getParentPath() . (($this->getParentPath() != '/') ? '/' : '') . $this->Text;
			}
		}
	}

	function i_check_requiredFields(){
		return ''; // overwrite
	}

	function i_scheduleToBeforeNow(){
		return false; // overwrite
	}

	function i_publInScheduleTable(){
		return false; // overwrite
	}

	function i_hasDoubbleFieldNames(){
		return false;
	}

	function we_resaveTemporaryTable(){
		return true;
	}

	function we_resaveMainTable(){
		$this->wasUpdate = 1;
		return we_root::we_save(1);
	}

	function we_rewrite(){
		return true;
	}

	function correctFields(){

	}

	public function we_republish(){
		return true;
	}

	/**
	 * @return	int
	 * @desc	checks if the user can modify a document, or only read the doc (only preview tab).
	  returns	 1	if doc is not restricted any rules
	  -1	if doc is not in workspace of user
	  -2	if doc is restricted and user has nor rights
	  -3	if doc is locked by another user
	  -4	if user has not the right to save a file.
	 */
	function userHasAccess(){
		$uid = $this->isLockedByUser();
		if($uid > 0 && $uid != $_SESSION['user']['ID'] && $GLOBALS['we_doc']->ID){ // file is locked
			return self::FILE_LOCKED;
		}

		if(!$this->userHasPerms()){ //	File is restricted !!!!!
			return self::USER_NO_PERM;
		}

		if(!$this->userCanSave()){ //	user has no right to save.
			return self::USER_NO_SAVE;
		}

		if(we_isOwner($this->CreatorID) || we_isOwner($this->Owners)){ //	user is creator/owner of doc - all is allowed.
			return self::USER_HASACCESS;
		}

		if($this->userHasPerms()){ //	access to doc is not restricted, check workspaces of user
			if($GLOBALS['we_doc']->ID){ //	userModule installed
				$ws = get_ws($GLOBALS['we_doc']->Table);
				if($ws){ //	doc has workspaces
					if(!(in_workspace($GLOBALS['we_doc']->ID, $ws, $GLOBALS['we_doc']->Table, $GLOBALS['DB_WE']))){
						return self::FILE_NOT_IN_USER_WORKSPACE;
					}
				}
			}
			return self::USER_HASACCESS;
		}
	}

	/**
	 * @return int
	 * @desc	checks if a file is locked by another user. returns that userID
	  or 0 when file is not locked
	 */
	function isLockedByUser(){
		//select only own ID if not in same session
		return intval(f('SELECT UserID FROM ' . LOCK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND tbl="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '" AND sessionID!="' . session_id() . '" AND lockTime>NOW()', 'UserID', $this->DB_WE));
	}

	function lockDocument(){
		if($_SESSION['user']['ID'] && $this->ID){ // only if user->id != 0
			//if lock is used by other user and time is up, update table
			$this->DB_WE->query('INSERT INTO ' . LOCK_TABLE . ' SET ID=' . intval($this->ID) . ',UserID=' . intval($_SESSION['user']['ID']) . ',tbl="' . $this->DB_WE->escape(stripTblPrefix($this->Table)) . '",sessionID="' . session_id() . '",lockTime=NOW()+INTERVAL ' . (PING_TIME + PING_TOLERANZ) . ' SECOND
				ON DUPLICATE KEY UPDATE UserID=' . intval($_SESSION['user']['ID']) . ',sessionID="' . session_id() . '",lockTime= NOW() + INTERVAL ' . (PING_TIME + PING_TOLERANZ) . ' SECOND');
		}
	}

	function i_loadNavigationItems(){
		if($this->Table == FILE_TABLE && $this->ID && $this->InWebEdition){
			$this->DB_WE->query('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ((Selection="static" AND SelectionType="docLink") OR (IsFolder=1)) AND LinkID=' . intval($this->ID));
			$this->NavigationItems = makeCSVFromArray($this->DB_WE->getAll(true), true);
		}
	}

	/**
	 * Gets the navigation folders for the current document
	 *
	 * @return Array
	 */
	function getNavigationFoldersForDoc(){
		switch($this->Table){
			case FILE_TABLE:
				if(isset($this->DocType)){
					$this->DB_WE->query('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ((Selection="' . weNavigation::SELECTION_DYNAMIC . '") AND (DocTypeID="' . $this->DB_WE->escape($this->DocType) . '" OR FolderID=' . intval($this->ParentID) . ')) OR
						(((Selection="' . weNavigation::SELECTION_STATIC . '" AND SelectionType="' . weNavigation::STPYE_DOCLINK . '") OR (IsFolder=1 AND FolderSelection="' . weNavigation::STPYE_DOCLINK . '")) AND LinkID=' . intval($this->ID) . ')');
					return $this->DB_WE->getAll(true);
				} else{
					$this->DB_WE->query('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ((Selection="static" AND SelectionType="' . weNavigation::STPYE_DOCLINK . '") OR (IsFolder=1 AND FolderSelection="' . weNavigation::STPYE_DOCLINK . '")) AND LinkID=' . intval($this->ID));
					return $this->DB_WE->getAll(true);
				}
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$this->DB_WE->query('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ((Selection="' . weNavigation::SELECTION_STATIC . '" AND SelectionType="' . weNavigation::STPYE_OBJLINK . '") OR (IsFolder=1 AND FolderSelection="' . weNavigation::STPYE_OBJLINK . '")) AND LinkID=' . intval($this->ID));
				return $this->DB_WE->getAll(true);
			default:
				return array();
		}
	}

	function insertAtIndex(){

	}

	/**
	 * Rewrites the navigation cache files
	 *
	 */
	function rewriteNavigation(){
		// rewrite filter
		if(defined('CUSTOMER_TABLE') && isset($this->documentCustomerFilter) && $this->documentCustomerFilter != false){
			weNavigationCustomerFilter::updateByFilter($this->documentCustomerFilter, $this->ID, $this->Table);
		}

		$_folders = $this->getNavigationFoldersForDoc();
		$_folders = array_unique($_folders);
		foreach($_folders as $_f){
			weNavigationCache::delNavigationTree($_f);
		}
	}

	function revert_published(){

	}

	public function isBinary(){
		return false;
	}

}

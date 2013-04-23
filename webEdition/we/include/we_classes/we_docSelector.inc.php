<?php

/**
 * webEdition CMS
 *
 * $Rev: 5823 $
 * $Author: mokraemer $
 * $Date: 2013-02-16 18:35:16 +0100 (Sat, 16 Feb 2013) $
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
class we_docSelector extends we_dirSelector{

	protected $userCanMakeNewFile = true;
	protected $open_doc = 0;
	protected $titles = array();
	protected $titleName = '';
	protected $startPath;
	protected $ctp = array("image/*" => "NEW_GRAFIK", "video/quicktime" => "NEW_QUICKTIME", "application/x-shockwave-flash" => "NEW_FLASH");
	protected $ctb = array("" => "btn_add_file", "image/*" => "btn_add_image", "video/quicktime" => "btn_add_quicktime", "application/x-shockwave-flash" => "btn_add_flash");

	function __construct($id, $table = "", $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $we_editDirID = "", $FolderText = "", $filter = "", $rootDirID = 0, $open_doc = 0, $multiple = 0, $canSelectDir = 0){
		parent::__construct($id, ($table == '' ? FILE_TABLE : $table), $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $we_editDirID, $FolderText, $rootDirID, $multiple, $filter);
		$this->fields.=',ModDate,RestrictOwners,Owners,OwnersReadOnly,CreatorID';
		if($this->table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && $this->table == OBJECT_FILES_TABLE)){
			$this->fields .= ",Published";
		}
		$this->canSelectDir = $canSelectDir;

		$this->title = g_l('fileselector', '[docSelector][title]');
		$this->userCanMakeNewFile = $this->_userCanMakeNewFile();
		$this->open_doc = $open_doc;
	}

	function query(){
		$filterQuery = '';
		if($this->filter){
			if(strpos($this->filter, ',')){
				$contentTypes = explode(',', $this->filter);
				$filterQuery .= ' AND (  ';
				foreach($contentTypes AS $ct){
					$filterQuery .= 'ContentType="' . $this->db->escape($ct) . '" OR ';
				}
				$filterQuery .= ' isFolder=1)';
			} else{
				$filterQuery = ' AND (ContentType="' . $this->db->escape($this->filter) . '" OR IsFolder=1 ) ';
			}
		}

		// deal with workspaces
		if($_SESSION["perms"]["ADMINISTRATOR"]){
			$wsQuery = '';
		} else{
			$wsQuery = '';
			if(get_ws($this->table)){
				$wsQuery = getWsQueryForSelector($this->table);
			} else if(defined("OBJECT_FILES_TABLE") && $this->table == OBJECT_FILES_TABLE && (!$_SESSION["perms"]["ADMINISTRATOR"])){
				$ac = getAllowedClasses($this->db);
				foreach($ac as $cid){
					$path = id_to_path($cid, OBJECT_TABLE);
					$wsQuery .= " Path LIKE '" . $this->db->escape($path) . "/%' OR Path='" . $this->db->escape($path) . "' OR ";
				}
				if($wsQuery){
					$wsQuery = ' AND (' . substr($wsQuery, 0, strlen($wsQuery) - 3) . ')';
				}
			}
			if(empty($wsQuery)){
				$wsQuery = ' OR RestrictOwners=0 ';
			}
		}
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' AND((1 ' .
			makeOwnersSql() . ')' .
			$wsQuery . ')' .
			$filterQuery . //$publ_q.
			($this->order ? (' ORDER BY ' . $this->order) : '')
		);

		$_db = new DB_WE();
		switch($this->table){
			case FILE_TABLE:

				$_db->query('SELECT a.ID, c.Dat FROM (' . FILE_TABLE . ' a LEFT JOIN ' . LINK_TABLE . ' b ON (a.ID=b.DID)) LEFT JOIN ' . CONTENT_TABLE . ' c ON (b.CID=c.ID) WHERE a.ParentID=' . intval($this->dir) . ' AND b.Name="Title"');
				while($_db->next_record()) {
					$this->titles[$_db->f('ID')] = $_db->f('Dat');
				}
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$_path = $this->path;
				while($_path !== "" && dirname($_path) != "\\" && dirname($_path) != "/") {
					$_path = dirname($_path);
				}
				$_cid = f('SELECT ID FROM ' . OBJECT_TABLE . " WHERE Path='" . $_db->escape($_path) . "'", "ID", $_db);
				$this->titleName = f('SELECT DefaultTitle FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($_cid), 'DefaultTitle', $_db);
				if($this->titleName && strpos($this->titleName, '_')){
					$_db->query('SELECT OF_ID, ' . $this->titleName . ' FROM ' . OBJECT_X_TABLE . $_cid . ' WHERE OF_ParentID=' . intval($this->dir));
					while($_db->next_record()) {
						$this->titles[$_db->f('OF_ID')] = $_db->f($this->titleName);
					}
				}
				break;
		}
	}

	function printHTML($what = we_fileselector::FRAMESET){
		switch($what){
			case self::PREVIEW:
				$this->printPreviewHTML();
				break;
			default:
				parent::printHTML($what);
		}
	}

	function getExitOpen(){
		$frameRef = $this->JSTextName && strpos($this->JSTextName, ".document.") > 0 ? substr($this->JSTextName, 0, strpos($this->JSTextName, ".document.") + 1) : '';
		return we_html_element::jsElement('
function exit_open() {
	if(currentID) {' . ($this->JSIDName ?
					'top.opener.' . $this->JSIDName . '= currentID ? currentID : "";' : '') .
				($this->JSTextName ?
					'top.opener.' . $this->JSTextName . '= currentID ? currentPath : "";
		if(!!top.opener.' . $frameRef . 'YAHOO && !!top.opener.' . $frameRef . 'YAHOO.autocoml) {  top.opener.' . $frameRef . 'YAHOO.autocoml.selectorSetValid(top.opener.' . str_replace('.value', '.id', $this->JSTextName) . '); }
		' : '') .
				($this->JSCommand ?
					$this->JSCommand . ';' : '') . '
	}
	self.close();
}');
	}

	function setDefaultDirAndID($setLastDir){
		$this->dir = $setLastDir && isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : 0;
		if($this->rootDirID){
			if(!in_parentID($this->dir, $this->rootDirID, $this->table, $this->db)){
				$this->dir = $this->rootDirID;
			}
		}
		$this->path = "";
		$this->values = array(
			"ParentID" => 0,
			"Text" => "/",
			"Path" => "/",
			"IsFolder" => 1,
			"ModDate" => 0,
			"RestrictOwners" => 0,
			"Owners" => "",
			"OwnersReadOnly" => "",
			"CreatorID" => 0,
			"ContentType" => "");
		$this->id = '';
	}

	function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "?what=$what&rootDirID=" . $this->rootDirID . "&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . "&filter=" . $this->filter . (isset($this->open_doc) ? ("&open_doc=" . $this->open_doc) : "");
	}

	function printFramesetJSFunctions(){
		$out = '
var contentTypes = new Array();';
		$ct = we_base_ContentTypes::inst();
		foreach($ct->getContentTypes() as $ctypes){
			if(g_l('contentTypes', '[' . $ctypes . ']') !== false){
				$out.='contentTypes["' . $ctypes . '"]  = "' . g_l('contentTypes', '[' . $ctypes . ']') . '";';
			}
		}
		return parent::printFramesetJSFunctions() . we_html_element::jsElement($out . '
function setFilter(ct) {
	top.fscmd.location.replace(top.queryString(' . we_fileselector::CMD . ',top.currentDir,"","",ct));
}

function showPreview(id) {
	if(top.fspreview) {
		top.fspreview.location.replace(top.queryString(' . self::PREVIEW . ',id));
	}
}

function newFile() {
	url="we_fs_uploadFile.php?dir="+top.currentDir+"&tab="+top.table+"&ct=' . rawurlencode($this->filter) . '";
	new jsWindow(url,"we_fsuploadFile",-1,-1,450,590,true,false,true);
}

function reloadDir() {
	top.fscmd.location.replace(top.queryString(' . we_fileselector::CMD . ',top.currentDir));
}');
	}

	function printFramesetJSFunctioWriteBody(){
		echo we_html_element::jsElement('
function writeBody(d){
	d.open();' .
			self::makeWriteDoc(we_html_tools::getHtmlTop('', '', '4Trans', true) . STYLESHEET_SCRIPT . we_html_element::jsElement('
var ctrlpressed=false
var shiftpressed=false
var inputklick=false
var wasdblclick=false
function submitFolderMods(){
	document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);document.we_form.submit();
}
document.onclick = weonclick;
function weonclick(e){
#	if(top.makeNewFolder ||  top.we_editDirID){
		if(!inputklick){
			document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);document.we_form.submit();
		}else{
			inputklick=false;
		}
#	}else{
		inputklick=false;
		if(document.all){
			if(event.ctrlKey || event.altKey){ ctrlpressed=true;}
			if(event.shiftKey){ shiftpressed=true;}
		}else{
			if(e.altKey || e.metaKey || e.ctrlKey){ ctrlpressed=true;}
			if(e.shiftKey){ shiftpressed=true;}
		}' . ($this->multiple ? '
		if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}' : '
		top.unselectAllFiles();') . '
#	}
}') . '</head>
<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"#\'+((makeNewFolder || top.we_editDirID) ? #\' onload="document.we_form.we_FolderText_tmp.focus();document.we_form.we_FolderText_tmp.select();"#\' : "")+#\'>
<form name="we_form" target="fscmd" action="' . $_SERVER["SCRIPT_NAME"] . '" onSubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">
#if(we_editDirID){
	<input type="hidden" name="what" value="' . self::DORENAMEFOLDER . '" />
	<input type="hidden" name="we_editDirID" value="#\'+top.we_editDirID+#\'" />
#}else{
	<input type="hidden" name="what" value="' . self::CREATEFOLDER . '" />
#}
	<input type="hidden" name="order" value="#\'+top.order+#\'" />
	<input type="hidden" name="rootDirID" value="' . $this->rootDirID . '" />
	<input type="hidden" name="table" value="' . $this->table . '" />
	<input type="hidden" name="id" value="#\'+top.currentDir+#\'" />
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
#if(makeNewFolder){
	<tr>
		<td align="center"><img src="' . ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '" width="16" height="18" border="0"></td>
		<td><input type="hidden" name="we_FolderText" value="' . g_l('fileselector', "[new_folder_name]") . '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' . g_l('fileselector', "[new_folder_name]") . '" class="wetextinput" onBlur="this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" /></td>
		<td class="selector">' . g_l('contentTypes', "[folder]") . '</td>
		<td class="selector">' . date(g_l('date', '[format][default]')) . '</td>
	</tr>
#}

#	for(i=0;i < entries.length; i++){
#		var onclick = #\' onClick="weonclick(' . (we_base_browserDetect::isIE() ? "this" : "event") . ');tout=setTimeout(\'if(top.wasdblclick==0){top.doClick(#\'+entries[i].ID+#\',0);}else{top.wasdblclick=0;}\',300);return true"#\';
#		var ondblclick = #\' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick(#\'+entries[i].ID+#\',1);return true;"#\';
	<tr#\' + ((entries[i].ID == top.currentID)  ? #\' style="background-color:#DFE9F5;cursor:pointer;"#\' : "") + #\' id="line_#\'+entries[i].ID+#\'" style="cursor:pointer;" #\'+((we_editDirID || makeNewFolder) ? "" : onclick)+ (entries[i].isFolder ? ondblclick : "") + #\'>
		<td class="selector" align="center"><img src="' . ICON_DIR . '#\'+entries[i].icon+#\'" width="16" height="18" border="0" /></td>
		<td class="selector"#\'+(entries[i].published==0 && entries[i].isFolder==0 ? #\' style="color: red;"#\' : "")+#\' title="#\'+entries[i].text+#\'">

#	if(we_editDirID == entries[i].ID){
			<input type="hidden" name="we_FolderText" value="#\'+entries[i].text+#\'" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="#\'+entries[i].text+#\'" class="wetextinput" onBlur="this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" />
#	}else{
			#\'+cutText(entries[i].text,25)+#\'
#	}
		</td>
		<td class="selector" title="#\'+' . $this->col2js . '+#\'">#\'+cutText(' . $this->col2js . ',30)+#\'</td>
		<td class="selector">#\'+entries[i].modDate+#\'</td>
	</tr><tr><td colspan="4">' . we_html_tools::getPixel(2, 1) . '</td></tr>
#	}
	<tr>' . str_replace("'", "\\'", $this->tableSizer) . '</tr>
</table></form>
</body></html>') . '
	d.close();
}');
	}

	function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
function queryString(what,id,o,we_editDirID,filter){
	if(!o) o=top.order;
	if(!we_editDirID) we_editDirID="";
	if(!filter) filter="' . $this->filter . '";
	return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&rootDirID=' .
				$this->rootDirID . (isset($this->open_doc) ?
					"&open_doc=" . $this->open_doc : '') .
				'&table=' . $this->table . '&id=\'+id+(o ? ("&order="+o) : "")+(we_editDirID ? ("&we_editDirID="+we_editDirID) : "")+(filter ? ("&filter="+filter) : "");
}');
	}

	function printFramesetJSFunctionEntry(){
		return we_html_element::jsElement('
function entry(ID,icon,text,isFolder,path,modDate,contentType,published,title) {
	this.ID=ID;
	this.icon=icon;
	this.text=text;
	this.isFolder=isFolder;
	this.path=path;
	this.modDate=modDate;
	this.contentType=contentType;
	this.published=published;
	this.title=title;
}');
	}

	function printFramesetJSFunctionAddEntry(){
		return we_html_element::jsElement('
		function addEntry(ID,icon,text,isFolder,path,modDate,contentType,published,title) {
		entries[entries.length] = new entry(ID,icon,text,isFolder,path,modDate,contentType,published,title);
		}');
	}

	function printFramesetJSFunctionAddEntries(){
		$ret = '';
		if($this->userCanSeeDir(true)){
			while($this->next_record()) {
				$title = isset($this->titles[$this->f("ID")]) ? $this->titles[$this->f("ID")] : '&nbsp;';
				$title = strip_tags(str_replace(array('\\', '"', "\n",), array('\\\\', '\"', ' '), $title));
				$title = $title == '&nbsp;' ? '-' : oldHtmlspecialchars($title);
				$published = ($this->table == FILE_TABLE || (defined("OBJECT_FILES_TABLE") && $this->table == OBJECT_FILES_TABLE) ? $this->f("Published") : 1);
				$ret.= 'addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . addcslashes($this->f("Text"), '"') . '",' . $this->f("IsFolder") . ',"' . addcslashes($this->f("Path"), '"') . '","' . date(g_l('date', '[format][default]'), $this->f("ModDate")) . '","' . $this->f("ContentType") . '","' . $published . '","' . addcslashes($title, '"') . '");';
			}
		}
		return we_html_element::jsElement($ret);
	}

	function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->next_record()) {
			$title = isset($this->titles[$this->f("ID")]) ? $this->titles[$this->f("ID")] : '&nbsp;';
			$published = $this->table == FILE_TABLE ? $this->f("Published") : 1;
			$title = $title == '&nbsp;' ? '-' : oldHtmlspecialchars($title);
			$title = strip_tags(str_replace(array('"', "\n\r", "\n", "\\", '°',), array('\"', ' ', ' ', "\\\\", '&deg;'), $title));
			$ret.='top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '","' . date(g_l('date', '[format][default]'), $this->f("ModDate")) . '","' . $this->f("ContentType") . '","' . $published . '","' . $title . '");';
		}

		if($this->filter != "text/weTmpl" && $this->filter != "object" && $this->filter != "objectFile" && $this->filter != "text/webedition"){

			$tmp = ((in_workspace($this->dir, get_ws($this->table))) && $this->userCanMakeNewFile) ? 'enable' : 'disable';
			$ret.= 'if(top.fsheader.' . $tmp . 'NewFileBut) top.fsheader.' . $tmp . 'NewFileBut();';
		}

		$tmp = ($this->userCanMakeNewDir() ? 'enable' : 'disable');
		$ret.='top.fsheader.' . $tmp . 'NewFolderBut();';
		return $ret;
	}

	function getFramesetJavaScriptIncludes(){
		return we_html_element::jsScript(JS_DIR . 'windows.js');
	}

	function printHeaderHeadlines(){
		return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr>' . $this->tableHeadlines . '</tr>
	<tr>' . $this->tableSizer . '</tr>
</table>';
	}

	function printHeaderTableExtraCols(){
		$newFileState = $this->userCanMakeNewFile ? 1 : 0;
		return parent::printHeaderTableExtraCols() .
			($this->filter != "text/weTmpl" && $this->filter != "object" && $this->filter != "objectFile" && $this->filter != "text/webedition" ?
				'<td width="10">' . we_html_tools::getPixel(10, 10) . '</td><td width="40">' .
				we_html_element::jsElement('newFileState=' . $newFileState . ';') .
				($this->filter == "image/*" || $this->filter == "video/quicktime" || $this->filter == "application/x-shockwave-flash" ?
					we_button::create_button("image:" . $this->ctb[$this->filter], "javascript:top.newFile();", true, -1, 22, "", "", !$newFileState, false) :
					we_button::create_button("image:btn_add_file", "javascript:top.newFile();", true, -1, 22, "", "", !$newFileState, false)) .
				'</td>' : '');
	}

	function printHeaderJSDef(){
		$ret = parent::printHeaderJSDef();
		if($this->filter != "text/weTmpl" && $this->filter != "object" && $this->filter != "objectFile" && $this->filter != "text/webedition"){
			$ret.= '
var newFileState = ' . ($this->userCanMakeNewFile ? 1 : 0) . ';';
			if($this->filter == "image/*" || $this->filter == "video/quicktime" || $this->filter == "application/x-shockwave-flash"){
				return $ret . '
function disableNewFileBut() {
	' . ((isset($this->ctb[$this->filter])) ? $this->ctb[$this->filter] : "") . '_enabled = switch_button_state("' . ((isset($this->ctb[$this->filter])) ? $this->ctb[$this->filter] : "") . '", "", "disabled", "image");
	newFileState = 0;
}

function enableNewFileBut() {
	' . ((isset($this->ctb[$this->filter])) ? $this->ctb[$this->filter] : "") . '_enabled = switch_button_state("' . ((isset($this->ctb[$this->filter])) ? $this->ctb[$this->filter] : "") . '", "", "enabled", "image");
	newFileState = 1;
}';
			} else{
				return $ret . '
function disableNewFileBut() {
	btn_add_file_enabled = switch_button_state("btn_add_file", "", "disabled", "image");
	newFileState = 0;
}

function enableNewFileBut() {
	btn_add_file_enabled = switch_button_state("btn_add_file", "", "enabled", "image");
	newFileState = 1;
}';
			}
		}
	}

	function _userCanMakeNewFile(){
		if($_SESSION["perms"]["ADMINISTRATOR"]){
			return true;
		}
		if(!$this->userCanSeeDir()){
			return false;
		}
		if($this->filter == "image/*" || $this->filter == "video/quicktime" || $this->filter == "application/x-shockwave-flash"){
			if(!we_hasPerm($this->ctp[$this->filter])){
				return false;
			}
		} elseif(!
			(
			we_hasPerm("NEW_GRAFIK") ||
			we_hasPerm("NEW_QUICKTIME") ||
			we_hasPerm("NEW_HTML") ||
			we_hasPerm("NEW_JS") ||
			we_hasPerm("NEW_CSS") ||
			we_hasPerm("NEW_TEXT") ||
			we_hasPerm("NEW_HTACCESS") ||
			we_hasPerm("NEW_FLASH") ||
			we_hasPerm("NEW_SONSTIGE") ||
			we_hasPerm('FILE_IMPORT')
			)
		){
			return false;
		}

		return true;
	}

	function printHeaderTableSpaceRow(){
		return '<tr><td colspan="13">' . we_html_tools::getPixel(5, 10) . '</td></tr>';
	}

	function printSetDirHTML(){
		print '<script type="text/javascript"><!--
top.clearEntries();' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() . '
top.fsheader.' . (intval($this->dir) == 0 ? 'disable' : 'enable') . 'RootDirButs();
top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values["ParentID"] . '";
//-->
</script>';
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
	}

	function printFooterTable(){
		$ret = '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="5"><img src="' . IMAGE_DIR . 'umr_h_small.gif" width="100%" height="2" border="0" /></td>
	</tr>
	<tr>
		<td colspan="5">' . we_html_tools::getPixel(5, 5) . '</td>
	</tr>';
		if($this->filter == ''){
			$ret.= '
	<tr>
		<td></td>
		<td class="defaultfont">
			<b>' . g_l('fileselector', "[type]") . '</b></td>
		<td></td>
		<td class="defaultfont">
			<select name="filter" class="weSelect" size="1" onchange="top.setFilter(this.options[this.selectedIndex].value)" class="defaultfont" style="width:100%">
				<option value="">' . g_l('fileselector', "[all_Types]") . '</option>';
			foreach(we_base_ContentTypes::inst()->getWETypes() as $ctype){
				$ret.= '<option value="' . oldHtmlspecialchars($ctype) . '">' . g_l('contentTypes', '[' . $ctype . ']') . '</option>';
			}
			$ret.= '
			</select></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="5">' . we_html_tools::getPixel(5, 5) . '</td>
	</tr>';
		}
		$buttons = we_button::position_yes_no_cancel(we_button::create_button("ok", "javascript:press_ok_button();"), null, we_button::create_button("cancel", "javascript:top.exit_close();"));

		$seval = $this->values["Text"] == "/" ? "" : $this->values["Text"];
		$ret.= '
	<tr>
		<td></td>
		<td class="defaultfont">
			<b>' . g_l('fileselector', "[name]") . '</b>
		</td>
		<td></td>
		<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, $seval, "", "style=\"width:100%\" readonly=\"readonly\"") . '
		</td>
		<td></td>
	</tr>
	<tr>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
		<td width="70">' . we_html_tools::getPixel(70, 5) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
		<td>' . we_html_tools::getPixel(5, 5) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
	</tr>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="right">' . $buttons . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 5) . '</td>
	</tr>
</table>';
		return $ret;
	}

	function getFrameset(){
		return '
<frameset rows="' . (((!defined("OBJECT_TABLE")) || $this->table != OBJECT_TABLE) ? '67' : '16') . ',*,' . (!$this->filter ? 90 : 65) . ',20,0" border="0"  onunload="if(top.opener && top.opener.top && top.opener.top.toggleBusy){top.opener.top.toggleBusy();}">
	<frame src="' . $this->getFsQueryString(we_fileselector::HEADER) . '" name="fsheader" noresize scrolling="no">
	<frameset cols="605,*" border="1">
		<frame src="' . $this->getFsQueryString(we_fileselector::BODY) . '" name="fsbody" noresize scrolling="auto">
		<frame src="' . $this->getFsQueryString(self::PREVIEW) . '" name="fspreview" noresize scrolling="no"' . ((!we_base_browserDetect::isGecko()) ? ' style="border-left:1px solid black"' : '') . '>
	</frameset>
	<frame src="' . $this->getFsQueryString(we_fileselector::FOOTER) . '"  name="fsfooter" noresize scrolling="no">
	<frame src="' . HTML_DIR . 'gray2.html"  name="fspath" noresize scrolling="no">
	<frame src="' . HTML_DIR . 'white.html"  name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>';
	}

	function printPreviewHTML(){
		if(!$this->id){
			return;
		}

		$result = getHash('SELECT * FROM ' . $this->table . ' WHERE ID=' . intval($this->id), $this->db);
		$path = isset($result['Path']) ? $result['Path'] : "";
		$out = we_html_tools::getHtmlTop() .
			STYLESHEET . we_html_element::cssElement('
	body {
		margin:0px;
		padding:0px;
		background-color:#FFFFFF;
	}
	td {
		font-size: 10px;
		padding: 3px 6px;
		vertical-align:top;
	}
	td.image {
		vertical-align:middle;
		padding: 0px;
	}
	td.info {
		padding: 0px;
	}
	.headline {
		padding:3px 6px;
		background-color:#BABBBA;
		font-weight:bold;
		border-top:0px solid black;
		border-bottom:0px solid black;
	}
	.odd {
		padding:3px 6px;
		background-color:#FFFFFF;
	}
	.even {
		padding:3px 6px;
		background-color:#F2F2F1;
	}') . we_html_element::jsElement('
	function setInfoSize() {
		infoSize = document.body.clientHeight;
		if(infoElem=document.getElementById("info")) {
			infoElem.style.height = document.body.clientHeight - (prieviewpic = document.getElementById("previewpic") ? 160 : 0 );
		}
	}
	function openToEdit(tab,id,contentType){
		if(top.opener && top.opener.top.weEditorFrameController) {
			top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		}
	}
	var weCountWriteBC = 0;
	setTimeout(\'weWriteBreadCrumb("' . $path . '")\',100);
	function weWriteBreadCrumb(BreadCrumb){
		if(typeof top.fspath != "undefined") top.fspath.document.body.innerHTML = BreadCrumb;
		else if(weCountWriteBC<10) setTimeout(\'weWriteBreadCrumb("' . $path . '")\',100);
		weCountWriteBC++;
	}') . '
</head>
<body bgcolor="white" class="defaultfont" onresize="setInfoSize()" onload="setTimeout(\'setInfoSize()\',50)">
					';
		if(isset($result['ContentType']) && !empty($result['ContentType'])){
			if($result['ContentType'] != "folder"){
				$this->db->query("SELECT ID, Text, IsFolder FROM " . $this->db->escape($this->table) . " WHERE ParentID=" . intval($this->id));
				$folderFolders = array();
				$folderFiles = array();
				while($this->db->next_record()) {
					$this->db->f('IsFolder') ? $folderFolders[$this->db->f('ID')] = $this->db->f('Text') : $folderFiles[$this->db->f('ID')] = $this->db->f('Text');
				}
			} else{
				switch($this->table){
					case FILE_TABLE:
						$this->db->query('SELECT a.Name, b.Dat FROM ' . LINK_TABLE . ' a LEFT JOIN ' . CONTENT_TABLE . ' b on (a.CID = b.ID) WHERE a.DID=' . intval($this->id) . " AND NOT a.DocumentTable='tblTemplates'");
						while($this->db->next_record()) {
							$metainfos[$this->db->f('Name')] = $this->db->f('Dat');
						}
						break;
					case (defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
						$_fieldnames = getHash('SELECT DefaultDesc,DefaultTitle,DefaultKeywords FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($result["TableID"]), $this->db);
						$_selFields = "";
						foreach($_fieldnames as $_key => $_val){
							if(empty($_val) || $_val == '_') // bug #4657
								continue;
							if(!is_numeric($_key)){
								if($_val == '_'){
									$_val = '';
								}
								if($_val){
									switch($_key){
										case "DefaultDesc":
											$_selFields .= $_val . " as Description,";
											break;
										case "DefaultTitle":
											$_selFields .= $_val . " as Title,";
											break;
										case "DefaultKeywords":
											$_selFields .= $_val . " as Keywords,";
											break;
									}
								}
							}
						}
						if($_selFields){
							$_selFields = substr($_selFields, 0, strlen($_selFields) - 1);
							$metainfos = getHash("SELECT " . $_selFields . " FROM " . OBJECT_X_TABLE . $result["TableID"] . " WHERE OF_ID=" . intval($result["ID"]), $this->db);
						}
				}
			}
			switch($result['ContentType']){
				case "image/*":
				case "text/webedition":
				case "text/html":
				case "application/*":
					$showPriview = $result['Published'] > 0 ? true : false;
					break;

				default:
					$showPriview = false;
					break;
			}

			$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path']) ? filesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']) : 0;

			$_filesize = $fs < 1000 ? $fs . ' byte' : ($fs < 1024000 ? round(($fs / 1024), 2) . ' kb' : round(($fs / (1024 * 1024)), 2) . ' mb');


			if($result['ContentType'] == "image/*" && file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path'])){
				if($fs === 0){
					$_imagesize = array(0, 0);
					$_thumbpath = IMAGE_DIR . 'icons/no_image.gif';
					$_imagepreview = "<img src='$_thumbpath' border='0' id='previewpic'><p>" . g_l('fileselector', "[image_not_uploaded]") . "</p>";
				} else{
					$_imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
					$_thumbpath = WEBEDITION_DIR . 'thumbnail.php?id=' . $this->id . '&size=150&path=' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $result['Path']) . '&extension=' . $result['Extension'] . '&size2=200';
					$_imagepreview = "<a href='" . $result['Path'] . "' target='_blank' align='center'><img src='$_thumbpath' border='0' id='previewpic'></a>";
				}
			}

			$_previewFields = array(
				"properies" => array("headline" => g_l('weClass', "[tab_properties]"), "data" => array()),
				"metainfos" => array("headline" => g_l('weClass', "[metainfo]"), "data" => array()),
				"attributes" => array("headline" => g_l('weClass', "[attribs]"), "data" => array()),
				"folders" => array("headline" => g_l('fileselector', "[folders]"), "data" => array()),
				"files" => array("headline" => g_l('fileselector', "[files]"), "data" => array()),
				"masterTemplate" => array("headline" => g_l('weClass', "[master_template]"), "data" => array())
			);

			$_previewFields["properies"]["data"][] = array(
				"caption" => g_l('fileselector', "[name]"),
				"content" => (
				$showPriview ? "<div style='float:left; vertical-align:baseline; margin-right:4px;'><a href='" . getServerUrl(true) . $result['Path'] .
					"' target='_blank' style='color:black'><img src='" . ICON_DIR . "browser.gif' border='0' vspace='0' hspace='0'></a></div>" : ""
				) . "<div style='margin-right:14px'>" . (
				$showPriview ? "<a href='" . getServerUrl(true) . $result['Path'] . "' target='_blank' style='color:black'>" . $result['Text'] . "</a>" : $result['Text']
				) . "</div>"
			);

			$_previewFields["properies"]["data"][] = array(
				"caption" => "ID",
				"content" => "<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'>
					<div style='float:left; vertical-align:baseline; margin-right:4px;'>
					<img src='" . ICON_DIR . "bearbeiten.gif' border='0' vspace='0' hspace='0'>
					</div></a>
					<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'>
						<div>" . $this->id . "</div>
					</a>"
			);

			if($result['CreationDate']){
				$_previewFields["properies"]["data"][] = array(
					"caption" => g_l('fileselector', "[created]"),
					"content" => date(g_l('date', '[format][default]'), $result['CreationDate'])
				);
			}

			if($result['ModDate']){
				$_previewFields["properies"]["data"][] = array(
					"caption" => g_l('fileselector', "[modified]"),
					"content" => date(g_l('date', '[format][default]'), $result['ModDate'])
				);
			}

			$_previewFields["properies"]["data"][] = array(
				"caption" => g_l('fileselector', "[type]"),
				"content" => ((g_l('contentTypes', '[' . $result['ContentType'] . ']') !== false) ? g_l('contentTypes', '[' . $result['ContentType'] . ']') : $result['ContentType'])
			);


			if(isset($_imagesize)){
				$_previewFields["properies"]["data"][] = array(
					"caption" => g_l('weClass', "[width]") . " x " . g_l('weClass', "[height]"),
					"content" => $_imagesize[0] . " x " . $_imagesize[1] . " px "
				);
			}

			if($result['ContentType'] != "folder" && $result['ContentType'] != "text/weTmpl" && $result['ContentType'] != "object" && $result['ContentType'] != "objectFile"){
				$_previewFields["properies"]["data"][] = array(
					"caption" => g_l('fileselector', "[filesize]"),
					"content" => $_filesize
				);
			}


			if(isset($metainfos['Title'])){
				$_previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', "[Title]"),
					"content" => $metainfos['Title']
				);
			}

			if(isset($metainfos['Description'])){
				$_previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', "[Description]"),
					"content" => $metainfos['Description']
				);
			}

			if(isset($metainfos['Keywords'])){
				$_previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', "[Keywords]"),
					"content" => $metainfos['Keywords']
				);
			}

			switch($result['ContentType']){
				case "image/*":
					$Title = (isset($metainfos['title']) ? $metainfos['title'] : ((isset($metainfos['Title']) && isset($metainfos['useMetaTitle']) && $metainfos['useMetaTitle']) ? $metainfos['Title'] : ''));
					$name = (isset($metainfos['name']) ? $metainfos['name'] : '');
					$alt = (isset($metainfos['alt']) ? $metainfos['alt'] : '');
					if($Title !== ""){
						$_previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', "[Title]"),
							"content" => oldHtmlspecialchars($Title)
						);
					}
					if($name !== ""){
						$_previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', "[name]"),
							"content" => $name
						);
					}
					if($alt !== ""){
						$_previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', "[alt]"),
							"content" => oldHtmlspecialchars($alt)
						);
					}
				//no break!
				case "application/x-shockwave-flash":
				case "video/quicktime":
				case "application/*":
					// only binary data have additional metadata
					$metaDataFields = weMetaData::getDefinedMetaDataFields();
					foreach($metaDataFields as $md){
						if($md['tag'] != "Title" && $md['tag'] != "Description" && $md['tag'] != "Keywords"){
							if(isset($metainfos[$md['tag']])){
								$_previewFields["metainfos"]["data"][] = array(
									"caption" => $md['tag'],
									"content" => $metainfos[$md['tag']]
								);
							}
						}
					}
					break;

				case "folder":
					if(isset($folderFolders) && is_array($folderFolders) && count($folderFolders)){
						foreach($folderFolders as $fId => $fxVal){
							$_previewFields["folders"]["data"][] = array(
								"caption" => $fId,
								"content" => $fxVal
							);
						}
					}
					if(isset($folderFiles) && is_array($folderFiles) && count($folderFiles)){
						foreach($folderFiles as $fId => $fxVal){
							$_previewFields["files"]["data"][] = array(
								"caption" => $fId,
								"content" => $fxVal
							);
						}
					}
					break;

				case "text/weTmpl":
					if(isset($result['MasterTemplateID']) && !empty($result['MasterTemplateID'])){
						$mastertemppath = f("SELECT Text, Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($result['MasterTemplateID']), "Path", $this->db);
						$_previewFields["masterTemplate"]["data"][] = array(
							"caption" => "ID",
							"content" => $result['MasterTemplateID']
						);
						$_previewFields["masterTemplate"]["data"][] = array(
							"caption" => g_l('weClass', "[path]"),
							"content" => $mastertemppath
						);
					}
					break;
			}

			$out .= "<table cellpadding='0' cellspacing='0' width='100%'>";
			if(isset($_imagepreview) && $_imagepreview){
				$out .= "<tr><td colspan='2' valign='middle' class='image' height='160' align='center' bgcolor='#EDEEED'>" . $_imagepreview . "</td></tr>";
			}

			foreach($_previewFields as $_part){
				if(count($_part["data"]) > 0){
					$out .= "<tr><td colspan='2' class='headline'>" . $_part["headline"] . "</td></tr>";
					foreach($_part["data"] as $z => $_row){
						$_class = (($z % 2) == 0) ? "odd" : "even";
						$out .= "<tr class='$_class'><td>" . $_row['caption'] . ": </td><td>" . $_row['content'] . "</td></tr>";
					}
				}
			}


			$out .= "</table></div></td></tr></table>";
		}
		$out .= '</body></html>';
		echo $out;
	}

	function printFramesetJSsetDir(){
		return we_html_element::jsElement('
function setDir(id) {
	showPreview(id);
	top.fspreview.document.body.innerHTML = "";
	top.fscmd.location.replace(top.queryString(' . we_multiSelector::SETDIR . ',id));
	e = getEntry(id);
	fspath.document.body.innerHTML = e.path;
}');
	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function selectFile(id){

	if(id){
		e = getEntry(id);
		fspath.document.body.innerHTML = e.path;

		if( top.fsfooter.document.we_form.fname.value != e.text &&
			top.fsfooter.document.we_form.fname.value.indexOf(e.text+",") == -1 &&
			top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 &&
			top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 ){

			top.fsfooter.document.we_form.fname.value =  top.fsfooter.document.we_form.fname.value ?
				(top.fsfooter.document.we_form.fname.value + "," + e.text) :
				e.text;
		}

		if(top.fsbody.document.getElementById("line_"+id)) top.fsbody.document.getElementById("line_"+id).style.backgroundColor="#DFE9F5";
		currentPath = e.path;
		currentID = id;
		we_editDirID = 0;
		currentType = e.contentType;

		showPreview(id);

	}else{
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editDirID = 0;
	}

}');
	}

	function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function doClick(id,ct){
	top.fspreview.document.body.innerHTML = "";
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
		}
	} else {
		if(getEntry(id).contentType != "folder" || ' . ($this->canSelectDir ? "true" : "false") . '){' .
				($this->multiple ? '
			if(fsbody.shiftpressed){
				var oldid = currentID;
				var currendPos = getPositionByID(id);
				var firstSelected = getFirstSelected();

				if(currendPos > firstSelected){
					selectFilesFrom(firstSelected,currendPos);
				}else if(currendPos < firstSelected){
					selectFilesFrom(currendPos,firstSelected);
				}else{
					selectFile(id);
				}
				currentID = oldid;

			}else if(!fsbody.ctrlpressed){
				selectFile(id);
			}else{
				if (isFileSelected(id)) {
					unselectFile(id);
				}else{' : '') . '
					selectFile(id);' .
				($this->multiple ? '
				}
			}' : '') . '
		} else {
			showPreview(id);
		}
	}
	if(fsbody.ctrlpressed){
		fsbody.ctrlpressed = 0;
	}
	if(fsbody.shiftpressed){
		fsbody.shiftpressed = 0;
	}
}

function previewFolder(id) {
	alert(id);
}');
	}

}


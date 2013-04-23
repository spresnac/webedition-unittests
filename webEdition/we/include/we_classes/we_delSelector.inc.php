<?php

/**
 * webEdition CMS
 *
 * $Rev: 5399 $
 * $Author: mokraemer $
 * $Date: 2012-12-21 12:52:23 +0100 (Fri, 21 Dec 2012) $
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
class we_delSelector extends we_multiSelector{

	function __construct($id, $table = FILE_TABLE){
		parent::__construct($id, $table);
		$this->title = g_l('fileselector', '[delSelector][title]');
	}

	function printHTML($what = we_fileselector::FRAMESET){
		switch($what){
			case we_fileselector::HEADER:
				$this->printHeaderHTML();
				break;
			case we_fileselector::FOOTER:
				$this->printFooterHTML();
				break;
			case we_fileselector::BODY:
				$this->printBodyHTML();
				break;
			case we_fileselector::CMD:
				$this->printCmdHTML();
				break;
			case self::DEL:
				$this->printDoDelEntryHTML();
				break;
			case we_fileselector::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	function printFooterJS(){
		return we_button::create_state_changer() . we_html_element::jsElement('
function disableDelBut(){
	delete_enabled = switch_button_state("delete", "delete_enabled", "disabled");
}
function enableDelBut(){
	delete_enabled = switch_button_state("delete", "delete_enabled", "enabled");
}
');
	}

	function printFramesetJSFunctions(){
		$tmp = (isset($_SESSION['weS']['seemForOpenDelSelector']['ID']) ? $_SESSION['weS']['seemForOpenDelSelector']['ID'] : 0);
		unset($_SESSION['weS']['seemForOpenDelSelector']['ID']);

		return parent::printFramesetJSFunctions() . we_html_element::jsElement('
function deleteEntry(){
	if(confirm(\'' . g_l('fileselector', "[deleteQuestion]") . '\')){
		var todel = "";
		var docIsOpen = false;
		for	(var i=0;i < entries.length; i++){
			if(isFileSelected(entries[i].ID)){
				todel += entries[i].ID + ",";' .
				($tmp ? '
						if(entries[i].ID=="' . $_SESSION['weS']['seemForOpenDelSelector']['ID'] . '") {
							docIsOpen = true;
						}' : '') . '
			}
		}
		if (todel) {
			todel = "," + todel;
		}

		top.fscmd.location.replace(top.queryString(' . self::DEL . ',top.currentID)+"&todel="+escape(todel));
		top.fsfooter.disableDelBut();

		if(docIsOpen) {
			//top.opener.top.weEditorFrameController.openDocument("", "", "cockpit", "open_cockpit", "", "", "", "", "");
			top.opener.top.we_cmd("close_all_documents");
			top.opener.top.we_cmd("start_multi_editor");
		}
	}
}');
	}

	function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function doClick(id,ct){
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
		}
	}else{
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
			}else{
				selectFile(id);
			}
		}

	}
	if(fsbody.ctrlpressed){
		fsbody.ctrlpressed = 0;
	}
	if(fsbody.shiftpressed){
		fsbody.shiftpressed = 0;
	}
}');
	}

	function printCmdHTML(){
		print we_html_element::jsElement('
top.clearEntries();' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				(intval($this->dir) == 0 ? '
top.fsheader.disableRootDirButs();
top.fsfooter.disableDelBut();' : '
top.fsheader.enableRootDirButs();
top.fsfooter.enableDelBut();') . '
top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";');
	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function selectFile(id){
	if(id){
		e = getEntry(id);

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
		if(id) top.fsfooter.enableDelBut();
		we_editDelID = 0;
	}else{
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editDelID = 0;
	}
}');
	}

	function printFramesetUnselectAllFilesHTML(){
		return we_html_element::jsElement('
function unselectAllFiles(){
	for	(var i=0;i < entries.length; i++){
		top.fsbody.document.getElementById("line_"+entries[i].ID).style.backgroundColor="white";
	}
	top.fsfooter.document.we_form.fname.value = "";
	top.fsfooter.disableDelBut();
}');
	}

	function printFramesetJSsetDir(){
		return we_html_element::jsElement('
function setDir(id){
	e = getEntry(id);
	if(id==0) e.text="";
	currentID = id;
	currentDir = id;
	currentPath = e.path;
	top.fsfooter.document.we_form.fname.value = e.text;
	if(id) top.fsfooter.enableDelBut();
	top.fscmd.location.replace(top.queryString(' . we_fileselector::CMD . ',id));
}');
	}

	function renameChildrenPath($id){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,IsFolder,Text FROM " . $db->escape($this->table) . " WHERE ParentID=" . intval($id));
		while($db->next_record()) {
			$newPath = f("SELECT Path FROM " . $db->escape($this->table) . " WHERE ID=" . intval($id), "Path", $db2) . "/" . $db->f("Text");
			$db2->query("UPDATE " . $db->escape($this->table) . " SET Path='" . $db->escape($newPath) . "' WHERE ID=" . intval($db->f("ID")));
			if($db->f("IsFolder")){
				$this->renameChildrenPath($db->f("ID"));
			}
		}
	}

	function printDoDelEntryHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		if(isset($_REQUEST["todel"])){
			$_SESSION['weS']['todel'] = $_REQUEST["todel"];
			print we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
top.opener.top.we_cmd("del_frag", "' . $_REQUEST["todel"] . '");
top.close();');
		}
		print '</head><body></body></html>';
	}

	function printFooterTable(){
		if($this->values["Text"] == "/")
			$this->values["Text"] = "";
		$okBut = we_button::create_button("delete", "javascript:if(document.we_form.fname.value==''){top.exit_close();}else{top.deleteEntry();}", true, 100, 22, "", "", true, false);

		$cancelbut = we_button::create_button("cancel", "javascript:top.exit_close();");
		$buttons = ($okBut ? we_button::position_yes_no_cancel($okBut, null, $cancelbut) : $cancelbut);

		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td colspan="5"><img src="' . IMAGE_DIR . 'umr_h_small.gif" width="100%" height="2" border="0" /></td>
	</tr>
	<tr>
		<td colspan="5">' . we_html_tools::getPixel(5, 5) . '</td>
	</tr>
	<tr>
		<td></td>
		<td class="defaultfont">
			<b>' . g_l('fileselector', "[filename]") . '</b>
		</td>
		<td></td>
		<td class="defaultfont" align="left">' . we_html_tools::htmlTextInput("fname", 24, $this->values["Text"], "", "style=\"width:100%\" readonly=\"readonly\"") . '
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
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' AND((1' . makeOwnersSql() . ')' .
			getWsQueryForSelector($this->table, false) . ')' . ($this->order ? (' ORDER BY ' . $this->order) : '')
		);
	}

}
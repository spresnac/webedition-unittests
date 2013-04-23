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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_catSelector extends we_multiSelector{

	const CREATECAT = 7;
	const DORENAMECAT = 9;
	const DORENAMEENTRY = 10;
	const PROPERTIES = 12;
	const CHANGE_CAT = 13;

	var $editCatState = true;
	var $we_editCatID = "";
	var $EntryText = "";
	var $noChoose = "";

	function __construct($id, $table = FILE_TABLE, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $we_editCatID = "", $EntryText = "", $rootDirID = 0, $noChoose = ""){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $rootDirID);
		$this->title = g_l('fileselector', '[catSelector][title]');

		$this->editCatState = $this->userCanEditCat();
		$this->we_editCatID = $we_editCatID;
		$this->EntryText = $EntryText;
		$this->noChoose = $noChoose;
	}

	function printHTML($what = we_fileselector::FRAMESET){
		switch($what){
			case self::HEADER:
				$this->printHeaderHTML();
				break;
			case self::FOOTER:
				$this->printFooterHTML();
				break;
			case self::BODY:
				$this->printBodyHTML();
				break;
			case self::CMD:
				$this->printCmdHTML();
				break;
			case self::CREATEFOLDER:
				$this->printCreateEntryHTML(1);
				break;
			case self::DORENAMEENTRY:
				$this->printDoRenameEntryHTML();
				break;
			case self::CREATECAT:
				$this->printCreateEntryHTML(0);
				break;
			case self::DORENAMECAT:
				$this->printDoRenameEntryHTML();
				break;
			case self::DEL:
				$this->printDoDelEntryHTML();
				break;
			case self::PROPERTIES:
				$this->printPropertiesHTML();
				break;
			case self::CHANGE_CAT:
				$this->printchangeCatHTML();
				break;
			case self::FRAMESET:
			default:
				$this->printFramesetHTML();
		}
	}

	function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "?what=$what&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . "&noChoose=" . $this->noChoose;
	}

	function printHeaderTable(){
		$editCatState = $this->userCanEditCat() ? 1 : 0;
		$changeCatState = $this->userCanChangeCat() ? 1 : 0;
		return we_html_element::jsElement('editCatState=' . $editCatState . ';') . '
<table border="0" cellpadding="0" cellspacing="0" width="100%">' .
			$this->printHeaderTableSpaceRow() . '
	<tr valign="middle">
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="70" class="defaultfont"><b>' . g_l('fileselector', "[lookin]") . '</b></td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td><select name="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%">' . $this->printHeaderOptions() . '</select></td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_button::create_button("root_dir", "javascript:top.setRootDir();", true, -1, 22, "", "", $this->dir == intval($this->rootDirID), false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_button::create_button("image:btn_fs_back", "javascript:top.goBackDir();", true, -1, 22, "", "", $this->dir == intval($this->rootDirID), false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="40">' . we_button::create_button("image:btn_new_dir", "javascript:if(editCatState==1){top.drawNewFolder();}", true, -1, 22, "", "", !$editCatState, false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="38">' . we_button::create_button("image:btn_add_cat", "javascript:if(editCatState==1){top.drawNewCat();}", true, -1, 22, "", "", !$editCatState, false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
		<td width="27">' . we_button::create_button("image:btn_function_trash", "javascript:if(changeCatState==1){top.deleteEntry();}", true, 27, 22, "", "", !$changeCatState, false) . '</td>
		<td width="10">' . we_html_tools::getPixel(10, 29) . '</td>
	</tr>' .
			$this->printHeaderTableSpaceRow() . '
</table>';
	}

	function printHeaderTableSpaceRow(){
		return '<tr><td colspan="15">' . we_html_tools::getPixel(5, 10) . '</td></tr>';
	}

	function userCanEditCat(){
		return we_hasPerm("EDIT_KATEGORIE");
	}

	function userCanChangeCat(){
		return (!$this->userCanEditCat() || $this->id == 0) ? false : true;
	}

	function printHeaderJSDef(){
		return 'var editCatState = ' . ($this->userCanEditCat() ? 1 : 0) . ';
			var changeCatState = ' . ($this->userCanChangeCat() ? 1 : 0) . ';';
	}

	function printHeaderJS(){
		return we_button::create_state_changer(false) . '

function disableRootDirButs(){
	root_dir_enabled = switch_button_state("root_dir", "root_dir_enabled", "disabled");
	btn_fs_back_enabled = switch_button_state("btn_fs_back", "back_enabled", "disabled", "image");
	rootDirButsState = 0;
}
function enableRootDirButs(){
	root_dir_enabled = switch_button_state("root_dir", "root_dir_enabled", "enabled");
	btn_fs_back_enabled = switch_button_state("btn_fs_back", "back_enabled", "enabled", "image");
	rootDirButsState = 1;
}

function disableNewBut(){
	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "disabled", "image");
	btn_add_cat_enabled = switch_button_state("btn_add_cat", "newCategorie_enabled", "disabled", "image");
	editCatState = 0;
}
function enableNewBut(){
	btn_new_dir_enabled = switch_button_state("btn_new_dir", "new_directory_enabled", "enabled", "image");
	btn_add_cat_enabled = switch_button_state("btn_add_cat", "newCategorie_enabled", "enabled", "image");
	editCatState = 1;
}
function disableDelBut(){
	btn_function_trash_enabled = switch_button_state("btn_function_trash", "btn_function_trash_enabled", "disabled", "image");
	changeCatState = 0;
}
function enableDelBut(){
' . (we_hasPerm("EDIT_KATEGORIE") ? '
	btn_function_trash_enabled = switch_button_state("btn_function_trash", "btn_function_trash_enabled", "enabled", "image");
	changeCatState = 1;
' : '') . '
}';
	}

	function getExitClose(){
		return we_html_element::jsElement('	function exit_close(){' .
				(!$this->noChoose ? '		if(hot){
			opener.setScrollTo();opener.top.we_cmd("reload_editpage");
		}' : '') .
				'		self.close();
	}');
	}

	function printFramesetJSFunctioWriteBody(){
		?><script type="text/javascript"><!--
					function writeBody(d){
						d.open();
		<?php
		echo self::makeWriteDoc(we_html_tools::getHtmlTop('', '', '4Trans', true) . STYLESHEET_SCRIPT . we_html_element::jsElement('
var ctrlpressed=false
var shiftpressed=false
var inputklick=false
var wasdblclick=false
var tout=null
document.onclick = weonclick;
function weonclick(e){
#	if(makeNewFolder || makeNewCat || we_editCatID){
if(!inputklick){' . (we_base_browserDetect::isIE() && $GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? '
document.we_form.we_EntryText.value=escape(document.we_form.we_EntryText_tmp.value);document.we_form.submit();' : '
document.we_form.we_EntryText.value=document.we_form.we_EntryText_tmp.value;document.we_form.submit();') . '
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
}
if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}
#	}
}') . '</head>
<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"#\'+((makeNewFolder || makeNewCat || we_editCatID) ? #\' onload="document.we_form.we_EntryText_tmp.focus();document.we_form.we_EntryText_tmp.select();"#\' : "")+#\'>
');
		?>

		<?php if(we_base_browserDetect::isIE() && substr($GLOBALS["WE_LANGUAGE"], -5) !== "UTF-8"){ ?>
					d.writeln('<form name="we_form" target="fscmd" action="<?php print $_SERVER["SCRIPT_NAME"]; ?>" onsubmit="document.we_form.we_EntryText.value=escape(document.we_form.we_EntryText_tmp.value);return true;">');

		<?php } else{ ?>
					d.writeln('<form name="we_form" target="fscmd" action="<?php print $_SERVER["SCRIPT_NAME"]; ?>" onsubmit="document.we_form.we_EntryText.value=document.we_form.we_EntryText_tmp.value;return true;">');

		<?php } ?>
				if(top.we_editCatID){
					d.writeln('<input type="hidden" name="what" value="<?php print self::DORENAMEENTRY; ?>" />');
					d.writeln('<input type="hidden" name="we_editCatID" value="'+top.we_editCatID+'" />');
				}else{
					if(makeNewFolder){
						d.writeln('<input type="hidden" name="what" value="<?php print self::CREATEFOLDER; ?>" />');
					}else{
						d.writeln('<input type="hidden" name="what" value="<?php print self::CREATECAT; ?>" />');
					}
				}
				d.writeln('<input type="hidden" name="order" value="'+top.order+'" />');
				d.writeln('<input type="hidden" name="rootDirID" value="<?php print $this->rootDirID; ?>" />');
				d.writeln('<input type="hidden" name="table" value="<?php print $this->table; ?>" />');
				d.writeln('<input type="hidden" name="id" value="'+top.currentDir+'" />');
				d.writeln('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
				if(makeNewFolder){
					d.writeln('<tr style="background-color:#DFE9F5;">');
					d.writeln('<td align="center"><img src="<?php print ICON_DIR . we_base_ContentTypes::FOLDER_ICON; ?>" width="16" height="18" border="0" /></td>');
					d.writeln('<td><input type="hidden" name="we_EntryText" value="<?php print g_l('fileselector', "[new_folder_name]"); ?>" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="<?php print g_l('fileselector', "[new_folder_name]") ?>" class="wetextinput" onblur="this.className=\'wetextinput\';" onfocus="this.className=\'wetextinputselected\'" style="width:100%" /></td>');
					d.writeln('</tr>');
				}else if(makeNewCat){
					d.writeln('<tr style="background-color:#DFE9F5;">');
					d.writeln('<td align="center"><img src="<?php print ICON_DIR ?>cat.gif" width="16" height="18" border="0" /></td>');
					d.writeln('<td><input type="hidden" name="we_EntryText" value="<?php print g_l('fileselector', "[new_cat_name]"); ?>" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="<?php print g_l('fileselector', "[new_cat_name]") ?>" class="wetextinput" onblur="this.className=\'wetextinput\';" onfocus="this.className=\'wetextinputselected\'" style="width:100%" /></td>');
					d.writeln('</tr>');
				}
				for(i=0;i < entries.length; i++){
					var onclick = ' onClick="weonclick(<?php echo (we_base_browserDetect::isIE() ? "this" : "event") ?>);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick('+entries[i].ID+',0);}else{top.wasdblclick=0;}\',300);return true;"';
					var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick('+entries[i].ID+',1);return true;"';
					d.writeln('<tr id="line_'+entries[i].ID+'" style="cursor:pointer;'+((we_editCatID != entries[i].ID) ? '' : '' )+'"'+((we_editCatID || makeNewFolder || makeNewCat) ? '' : onclick)+ (entries[i].isFolder ? ondblclick : '') + ' >');
					d.writeln('<td class="selector" width="25" align="center">');
					if(we_editCatID == entries[i].ID){
						d.writeln('<img src="<?php print ICON_DIR; ?>'+entries[i].icon+'" width="16" height="18" border="0" />');
						d.writeln('</td>');
						d.writeln('<td class="selector">');
						d.writeln('<input type="hidden" name="we_EntryText" value="'+entries[i].text+'" /><input onMouseDown="self.inputklick=true" name="we_EntryText_tmp" type="text" value="'+entries[i].text+'" class="wetextinput" onblur="this.className=\'wetextinput\';" onfocus="this.className=\'wetextinputselected\'" style="width:100%" />');
					}else{
						d.writeln('<img src="<?php print ICON_DIR; ?>'+entries[i].icon+'" width="16" height="18" border="0" />');
						d.writeln('</td>');
						d.writeln('<td class="selector"' + (we_editCatID ? '' : '') + ' title="'+entries[i].text+'">');
						d.writeln(cutText(entries[i].text,80));
					}
					d.writeln('</td>');
					d.writeln('</tr><tr><td colspan="2"><?php print we_html_tools::getPixel(2, 1); ?></td></tr>');
				}
				d.writeln('<tr>');
				d.writeln('<td width="25"><?php print we_html_tools::getPixel(25, 2) ?></td>');
				d.writeln('<td><?php print we_html_tools::getPixel(150, 2) ?></td>');
				d.writeln('</tr>');
				d.writeln('</table></form>');
				d.writeln('</body>');
				d.close();
			}
			//-->
		</script>
		<?php
	}

	function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
		function queryString(what,id,o,we_editCatID){
		if(!o) o=top.order;
		if(!we_editCatID) we_editCatID="";
		return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&rootDirID=' . $this->rootDirID . '&table=' . $this->table . '&id=\'+id+(o ? ("&order="+o) : "")+(we_editCatID ? ("&we_editCatID="+we_editCatID) : "");
		}');
	}

	function printFramesetJSFunctions(){
		return parent::printFramesetJSFunctions() . we_html_element::jsElement('
function drawNewFolder(){
	unselectAllFiles();
	top.makeNewFolder=true;
	top.writeBody(top.fsbody.document);
	top.makeNewFolder=false;
}
function drawNewCat(){
	unselectAllFiles();
	top.makeNewCat=true;
	top.writeBody(top.fsbody.document);
	top.makeNewCat=false;
}
function deleteEntry(){
	if(confirm(\'' . g_l('fileselector', "[deleteQuestion]") . '\')){
		var todel = "";
		for	(var i=0;i < entries.length; i++){
			if(isFileSelected(entries[i].ID)){
				todel += entries[i].ID + ",";
			}
		}
		if (todel) {
			todel = "," + todel;
		}
		top.fscmd.location.replace(top.queryString(' . self::DEL . ',top.currentID)+"&todel="+escape(todel));
		if(top.fsvalues) top.fsvalues.location.replace(top.queryString(' . self::PROPERTIES . ',0));
		top.fsheader.disableDelBut();
	}

}
function RenameEntry(id){
	top.we_editCatID=id;
	top.writeBody(top.fsbody.document);
	selectFile(id);
	top.we_editCatID=0;
}');
	}

	function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
var makeNewFolder=0;
var hot=0; // this is hot for category edit!!
var makeNewCat=0;
var we_editCatID="";
var old=0;');
	}

	function printCreateEntryHTML($what = 0){
		we_html_tools::htmlTop();
		print '<script type="text/javascript"><!--
top.clearEntries();
';
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = $this->EntryText;
		if($txt == ""){
			if($what == 1){
				print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				print we_message_reporting::getShowMessageCall(g_l('weEditor', "[category][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
			}
		} else if(strpos($txt, ',') !== false){
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[category][name_komma]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$txt = trim($txt);
			$parentPath = (!intval($this->dir)) ? "" : f('SELECT Path FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->dir), 'Path', $this->db);
			$Path = $parentPath . "/" . $txt;

			$this->db->query("SELECT ID FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($Path) . "'");
			if($this->db->next_record()){
				if($what == 1){
					$we_responseText = sprintf(g_l('weEditor', "[folder][response_path_exists]"), $Path);
				} else{
					$we_responseText = sprintf(g_l('weEditor', "[category][response_path_exists]"), $Path);
				}
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('|[\\\'"<>/]|', $txt)){

					$we_responseText = sprintf(g_l('weEditor', "[category][we_filename_notValid]"), $Path);
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					$set = array(
						'Category' => $txt,
						'ParentID' => intval($this->dir),
						'Text' => $txt,
						'Path' => $Path,
						'IsFolder' => intval($what),
						'Icon' => (($what == 1) ? we_base_ContentTypes::FOLDER_ICON : 'cat.gif'),
					);
					$this->db->query('INSERT INTO ' . $this->db->escape($this->table) . ' SET ' . we_database_base::arraySetter($set));
					$folderID = $this->db->getInsertId();
					print 'top.currentPath = "' . $Path . '";
top.currentID = "' . $folderID . '";
top.hot = 1; // this is hot for category edit!!

if(top.currentID){
	top.fsheader.enableDelBut();
	top.showPref(top.currentID);
}
';
				}
			}
		}

		print
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>';
		print '</head><body></body></html>';
	}

	function printHeaderHeadlines(){
		print '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="35%" class="selector" style="padding-left:10px;"><b><a href="#" onclick="javascript:top.orderIt(\'IsFolder DESC, Text\');">' . g_l('fileselector', "[catname]") . '</a></b></td>
		<td width="65%" class="selector" style="padding-left:10px;"><b>' . g_l('button', '[properties][value]') . '</b></td>
	</tr>
	<tr>
		<td width="35%"></td>
		<td width="65%"></td>
	</tr>
</table>';
	}

	function printDoRenameEntryHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();
		$foo = getHash('SELECT IsFolder,Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editCatID), $this->db);
		$IsDir = $foo["IsFolder"];
		$oldname = $foo["Text"];
		$what = f('SELECT IsFolder FROM ' . CATEGORY_TABLE . ' WHERE ID=' . intval($this->we_editCatID), 'IsFolder', $this->db);
		print '<script type="text/javascript"><!--
top.clearEntries();
';
		$this->EntryText = rawurldecode($this->EntryText);
		$txt = $this->EntryText;
		if($txt == ""){
			if($what == 1){
				print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				print we_message_reporting::getShowMessageCall(g_l('weEditor', "[category][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
			}
		} else if(strpos($txt, ',') !== false){
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[category][name_komma]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$parentPath = (!intval($this->dir)) ? "" : f("SELECT Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->dir), 'Path', $this->db);
			$Path = $parentPath . "/" . $txt;
			$this->db->query("SELECT ID,Text FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($Path) . "' AND ID != " . intval($this->we_editCatID));
			if($this->db->next_record()){
				if($what == 1){
					$we_responseText = sprintf(g_l('weEditor', "[folder][response_path_exists]"), $Path);
				} else{
					$we_responseText = sprintf(g_l('weEditor', "[category][response_path_exists]"), $Path);
				}
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('|[\'"<>/]|', $txt)){
					$we_responseText = sprintf(g_l('weEditor', "[category][we_filename_notValid]"), $Path);
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					if(f("SELECT Text FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->we_editCatID), "Text", $this->db) != $txt){
						$this->db->query("UPDATE " . $this->db->escape($this->table) . "
								SET Category='" . $this->db->escape($txt) . "',
								ParentID=" . intval($this->dir) . ",
								Text='" . $this->db->escape($txt) . "',
								Path='" . $this->db->escape($Path) . "'
								WHERE ID=" . intval($this->we_editCatID));
						if($IsDir){
							$this->renameChildrenPath($this->we_editCatID);
						}
						print 'top.currentPath = "' . $Path . '";
top.hot = 1; // this is hot for category edit!!
top.currentID = "' . $this->we_editCatID . '";
if(top.currentID){
	top.fsheader.enableDelBut();
	top.showPref(top.currentID);
}';
					}
				}
			}
		}

		print
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.fsfooter.document.we_form.fname.value = "";
top.selectFile(' . $this->we_editCatID . ');top.makeNewFolder = 0;
//-->
</script></head><body></body></html>';
	}

	function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function doClick(id,ct){
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
		}else if(top.currentID == id){' .
				(we_hasPerm("EDIT_KATEGORIE") ? '
				top.RenameEntry(id);' : '') . '
		}
	}else{
		if(top.currentID == id && (!fsbody.ctrlpressed)){' .
				(we_hasPerm("EDIT_KATEGORIE") ? '
				top.RenameEntry(id);' : '') . '

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
				hidePref(id);
			}else if(!fsbody.ctrlpressed){
				showPref(id);
				selectFile(id);
			}else{
				hidePref(id);
				if (isFileSelected(id)) {
					unselectFile(id);
				}else{
					selectFile(id);
				}
			}
		}
	}
	if(fsbody.ctrlpressed){
		fsbody.ctrlpressed = 0;
	}
	if(fsbody.shiftpressed){
		fsbody.shiftpressed = 0;
	}
}

function showPref(id) {
	if(self.fsvalues) self.fsvalues.location = "' . $this->getFsQueryString(self::PROPERTIES) . '&catid="+id;
}

function hidePref() {
	if(self.fsvalues) self.fsvalues.location = "' . $this->getFsQueryString(self::PROPERTIES) . '";
}');
	}

	function printCmdHTML(){
		print we_html_element::jsElement('
top.clearEntries();' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				(intval($this->dir) == 0 ? '
top.fsheader.disableRootDirButs();
top.fsheader.disableDelBut();' :					'
top.fsheader.enableRootDirButs();
top.fsheader.enableDelBut();' ) .				'
top.currentPath = "' . $this->path . '";
top.parentID = "' . $this->values["ParentID"] . '";');
	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function unselectFile(id){
	e = getEntry(id);
	top.fsbody.document.getElementById("line_"+id).style.backgroundColor="white";

	var foo = top.fsfooter.document.we_form.fname.value.split(/,/);

	for (var i=0; i < foo.length; i++) {
		if (foo[i] == e.text) {
			foo[i] = "";
			break;
		}
	}
	var str = "";
	for (var i=0; i < foo.length; i++) {
		if(foo[i]){
			str += foo[i]+",";
		}
	}
	str = str.replace(/(.*),$/,"$1");
	top.fsfooter.document.we_form.fname.value = str;
}

function selectFilesFrom(from,to){
	unselectAllFiles();
	for	(var i=from;i <= to; i++){
		selectFile(entries[i].ID);
	}
}

function getFirstSelected(){
	for	(var i=0;i < entries.length; i++){
		if(top.fsbody.document.getElementById("line_"+entries[i].ID).style.backgroundColor!="white"){
			return i;
		}
	}
	return -1;
}

function getPositionByID(id){
	for	(var i=0;i < entries.length; i++){
		if(entries[i].ID == id){
			return i;
		}
	}
	return -1;
}
function isFileSelected(id){
	return (top.fsbody.document.getElementById("line_"+id).style.backgroundColor && (top.fsbody.document.getElementById("line_"+id).style.backgroundColor!="white"));
}

function unselectAllFiles(){
	for	(var i=0;i < entries.length; i++){
		top.fsbody.document.getElementById("line_"+entries[i].ID).style.backgroundColor="white";
	}
	top.fsfooter.document.we_form.fname.value = "";
	top.fsheader.disableDelBut()
}

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
		if(id) top.fsheader.enableDelBut();
		we_editCatID = 0;
	}else{
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
		we_editCatID = 0;
	}
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
	if(id) top.fsheader.enableDelBut();
	top.fscmd.location.replace(top.queryString(' . we_fileselector::CMD . ',id));
}');
	}

	function renameChildrenPath($id){
		$db = new DB_WE();
		$db2 = new DB_WE();
		$db->query("SELECT ID,IsFolder,Text FROM " . CATEGORY_TABLE . ' WHERE ParentID=' . intval($id));
		while($db->next_record()) {
			$newPath = f("SELECT Path FROM " . CATEGORY_TABLE . " WHERE ID=" . intval($id), 'Path', $db2) . "/" . $db->f("Text");
			$db2->query("UPDATE " . CATEGORY_TABLE . " SET Path='" . $db2->escape($newPath) . "' WHERE ID=" . intval($db->f("ID")));
			if($db->f("IsFolder")){
				$this->renameChildrenPath($db->f("ID"));
			}
		}
	}

	function CatInUse($id, $IsDir){
		$db = new DB_WE();
		if($IsDir){
			return $this->DirInUse($id);
		} else{
			$ret = f("SELECT ID FROM " . FILE_TABLE . " WHERE Category LIKE '%," . intval($id) . ",%' OR temp_category LIKE '%," . intval($id) . ",%'", "ID", $db);
			if($ret)
				return true;
			if(defined("OBJECT_TABLE")){
				$ret = f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Category LIKE '%," . intval($id) . ",%'", "ID", $db);
				if($ret)
					return true;
			}
		}
		return false;
	}

	function DirInUse($id){
		$db = new DB_WE();
		if($this->CatInUse($id, 0))
			return true;

		$db->query("SELECT ID,IsFolder FROM " . $db->escape($this->table) . " WHERE ParentID=" . intval($id));
		while($db->next_record()) {
			if($this->CatInUse($db->f("ID"), $db->f("IsFolder")))
				return true;
		}
		return false;
	}

	function printDoDelEntryHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		if(isset($_REQUEST["todel"])){
			$finalDelete = array();
			$catsToDel = makeArrayFromCSV($_REQUEST["todel"]);
			$catlistNotDeleted = "";
			$changeToParent = false;
			foreach($catsToDel as $id){
				$IsDir = f("SELECT IsFolder FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->id), "IsFolder", $this->db);
				if($this->CatInUse($id, $IsDir)){
					$catlistNotDeleted .= id_to_path($id, CATEGORY_TABLE) . "\\n";
				} else{
					array_push($finalDelete, array("id" => $id, "IsDir" => $IsDir));
				}
			}
			if(!empty($finalDelete)){
				foreach($finalDelete as $foo){
					if($foo["IsDir"]){
						$this->delDir($foo["id"]);
					} else{
						$this->delEntry($foo["id"]);
					}
					if($this->dir == $foo["id"]){
						$changeToParent = true;
					}
				}
			}
			if($catlistNotDeleted){

				print we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('fileselector', "[cat_in_use]") . '\n\n' . $catlistNotDeleted, we_message_reporting::WE_MESSAGE_ERROR)
					);
			}
			if($changeToParent){
				$this->dir = $this->values["ParentID"];
			}
			$this->id = $this->dir;
			if($this->id){
				$foo = getHash("SELECT Path,Text FROM " . CATEGORY_TABLE . " WHERE ID=" . intval($this->id), $this->db);
				$Path = $foo["Path"];
				$Text = $foo["Text"];
			} else{
				$Path = "";
				$Text = "";
			}

			print '<script type="text/javascript"><!--
top.clearEntries();' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .
				'top.makeNewFolder = 0;
top.currentPath = "' . $Path . '";
top.currentID = "' . $this->id . '";
top.selectFile(' . $this->id . ');
if(top.currentID && top.fsfooter.document.we_form.fname.value != ""){
	top.fsheader.enableDelBut();
}
	//-->
</script>
';
		}
		print '</head><body></body></html>';

		return;

		$IsDir = f("SELECT IsFolder FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->id), "IsFolder", $this->db);
		if($this->CatInUse($this->id, $IsDir)){

			print we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(g_l('fileselector', "[cat_in_use]") . '\n\n' . $catlistNotDeleted, we_message_reporting::WE_MESSAGE_ERROR)
				);
		} else{
			print '<script type="text/javascript"><!--
top.clearEntries();';
			if($IsDir){
				$this->delDir($this->id);
			} else{
				$this->delEntry($this->id);
			}
			if($this->dir && ($this->dir == $this->id)){
				$this->dir = $this->values["ParentDir"];
			}
			$this->id = $this->dir;

			if($this->id){
				$foo = getHash("SELECT Path,Text FROM " . CATEGORY_TABLE . " WHERE ID=" . intval($this->id), $this->db);
				$Path = $foo["Path"];
				$Text = $foo["Text"];
			} else{
				$Path = "";
				$Text = "";
			}
			print
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML() .				'
top.makeNewFolder = 0;
top.currentPath = "' . $Path . '";
top.currentID = "' . $this->id . '";
top.fsfooter.document.we_form.fname.value = "' . $Text . '";
if(top.currentID && top.fsfooter.document.we_form.fname.value != ""){
	top.fsheader.enableDelBut();
}
	//-->
</script>
';
		}

		print '</head><body></body></html>';
	}

	function delDir($id){
		$entries = f('SELECT GROUP_CONCAT(ID) AS entries FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($id), 'entries', $this->db);
		if($entries){
			$entries = explode(',', $entries);
			foreach($entries as $entry){
				$this->delDir($entry);
			}
		}
		$entries = f('SELECT GROUP_CONCAT(ID) AS entries FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=0 AND ParentID=' . intval($id), 'entries', $this->db);
		$entries = ($entries ? explode(',', $entries) : array());
		$entries[] = $id;
		foreach($entries as $entry){
			$this->delEntry($entry);
		}
	}

	function delEntry($id){
		$this->db->query('DELETE FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($id));
	}

	function printFooterTable(){
		if($this->values["Text"] == "/")
			$this->values["Text"] = "";
		$csp = $this->noChoose ? 4 : 5;

		$okBut = (!$this->noChoose ? we_button::create_button("ok", "javascript:press_ok_button();") : '');
		$cancelbut = we_button::create_button("close", "javascript:top.exit_close();");
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
			<b>' . g_l('fileselector', "[catname]") . '</b>
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

	function getFrameset(){
		$isMainChooser = isset($_REQUEST['we_cmd']) && $_REQUEST['we_cmd'][0] == "openCatselector" && !($_REQUEST['we_cmd'][3] || $_REQUEST['we_cmd'][5]);
		return '<frameset rows="67,*,65,0" border="0">
	<frame src="' . $this->getFsQueryString(we_fileselector::HEADER) . '" name="fsheader" noresize scrolling="no">
' . ($isMainChooser ? '
	<frameset cols="35%,65%" border="0">
' : '') . '
    	<frame src="' . $this->getFsQueryString(we_fileselector::BODY) . '" name="fsbody" scrolling="auto">
' . ($isMainChooser ? '
    	<frame src="' . $this->getFsQueryString(self::PROPERTIES) . '" name="fsvalues"  scrolling="auto">
    </frameset>
' : '') . '
    <frame src="' . $this->getFsQueryString(we_fileselector::FOOTER) . '"  name="fsfooter" noresize scrolling="no">
    <frame src="' . HTML_DIR . 'white.html"  name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>';
	}

	function printChangeCatHTML(){
		if(isset($_POST["catid"])){
			$db = new DB_WE();
			$result = getHash("SELECT Category,Catfields,ParentID,Path FROM " . CATEGORY_TABLE . ' WHERE ID=' . intval($_POST["catid"]), $db);
			$fields = isset($result["Catfields"]) ? $result["Catfields"] : "";
			if($fields){
				$fields = unserialize($fields);
			} else{
				$fields = array(
					"default" => array("Title" => "", "Description" => "")
				);
			}
			$fields[$_SESSION["we_catVariant"]]["Title"] = $_REQUEST["catTitle"];
			$fields[$_SESSION["we_catVariant"]]["Description"] = $_REQUEST["catDescription"];
			$path = $result['Path'];
			$parentid = ($_REQUEST["FolderID"] != "" ? $_REQUEST["FolderID"] : $result['ParentID']);
			$category = (isset($_REQUEST['Category']) ? $_REQUEST['Category'] : $result['Category']);

			$targetPath = id_to_path($parentid, CATEGORY_TABLE);

			$js = "";
			if(preg_match("|^" . preg_quote($path, "|") . "|", $targetPath) || preg_match("|^" . preg_quote($path, "|") . "/|", $targetPath)){
				// Verschieben nicht m�glich
				$parentid = $result['ParentID'];

				if($parentid == 0){
					$parentPath = '/';
					$path = '/' . $category;
				} else{
					$tmp = explode('/', $path);
					array_pop($tmp);
					$parentPath = implode('/', $tmp);
					$path = $parentPath . '/' . $category;
				}
				$js = "top.frames['fsvalues'].document.we_form.elements['FolderID'].value = '$parentid';top.frames['fsvalues'].document.we_form.elements['FolderIDPath'].value = '$parentPath';";
			} else{
				$path = ($parentid != 0 ? $targetPath : '') . '/' . $category;
			}
			$updateok = $db->query("UPDATE " . CATEGORY_TABLE . " SET Category='" . $db->escape($category) . "', Text='" . $db->escape($category) . "', Path='" . $db->escape($path) . "', ParentID=" . intval($parentid) . ", Catfields='" . $db->escape(serialize($fields)) . "' WHERE ID=" . intval($_POST["catid"]));
			if($updateok){
				$this->renameChildrenPath(intval($_POST["catid"]));
			}
			we_html_tools::htmlTop();
			we_html_tools::protect();
			print we_html_element::jsElement($js . 'top.setDir(top.frames[\'fsheader\'].document.we_form.elements[\'lookin\'].value);' .
					($updateok ? we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', "[category][response_save_ok]"), $category), we_message_reporting::WE_MESSAGE_NOTICE) : we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', "[category][response_save_notok]"), $category), we_message_reporting::WE_MESSAGE_ERROR) )
				) .
				'</head><body></body></html>';
		}
	}

	function printPropertiesHTML(){

		$showPrefs = (isset($_REQUEST["catid"]) && $_REQUEST["catid"] );

		$path = "";

		$title = "";
		$variant = isset($_SESSION["we_catVariant"]) ? $_SESSION["we_catVariant"] : "default";
		$_SESSION["we_catVariant"] = $variant;
		$description = "";
		if($showPrefs){
			$result = getHash("SELECT ID,Category,Catfields,Path,ParentID FROM " . CATEGORY_TABLE . " WHERE id=" . intval($_REQUEST["catid"]), new DB_WE());
			$fields = (isset($result["Catfields"]) && $result["Catfields"] ?
					unserialize($result["Catfields"]) :
					array("default" => array("Title" => "", "Description" => ""))
				);

			if($result["ParentID"] != 0){
				$result2 = getHash("SELECT Path FROM " . CATEGORY_TABLE . " WHERE ID=" . intval($result["ParentID"]), new DB_WE());
				$path = isset($result2["Path"]) ? $result2["Path"] : '/';
			} else{
				$path = "/";
			}
			$parentId = isset($result["ParentID"]) ? $result["ParentID"] : '0';
			$category = isset($result["Category"]) ? $result["Category"] : '';
			$catID = isset($result["ID"]) ? intval($result["ID"]) : 0;
			$title = $fields[$_SESSION["we_catVariant"]]["Title"];
			$description = $fields[$_SESSION["we_catVariant"]]["Description"];
			unset($result);

			$dir_hidden = we_html_tools::hidden('FolderID', $parentId);
			$dir_input = we_html_tools::htmlTextInput('FolderIDPath', 24, $path, '', "style='width: 240px;'");

			$dir_chooser = we_button::create_button('select', "javascript:we_cmd('openSelector', document.we_form.elements['FolderID'].value, '" . CATEGORY_TABLE . "', 'document.we_form.elements[\\'FolderID\\'].value', 'document.we_form.elements[\\'FolderIDPath\\'].value', '', '', '', '1', '', 'false', 1)");

			$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 4, 3);

			$table->setCol(0, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), '<b>' . g_l('weClass', "[category]") . '</b>');
			$table->setCol(0, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), we_html_tools::htmlTextInput("Category", 50, $category, "", ' id="category"', "text", 360));

			$table->setCol(1, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), "<b>ID</b>");
			$table->setCol(1, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), $catID);

			$table->setCol(2, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), '<b>' . g_l('weClass', "[dir]") . '</b>');
			$table->setCol(2, 1, array("style" => "width:240px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), $dir_hidden . $dir_input);
			$table->setCol(2, 2, array("style" => "width:110px; padding: 0px 0px 10px 0px;", "class" => "defaultfont", "align" => "right"), $dir_chooser);

			$table->setCol(3, 0, array("style" => "width:100px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), "<b>" . g_l('global', "[title]") . "</b>");
			$table->setCol(3, 1, array("colspan" => 2, "style" => "width:350px; padding: 0px 0px 10px 0px;", "class" => "defaultfont"), we_html_tools::htmlTextInput("catTitle", 50, $title, "", '', "text", 360));

			$ta = we_html_tools::htmlFormElementTable(we_forms::weTextarea("catDescription", $description, array("bgcolor" => "white", "inlineedit" => "true", "wysiwyg" => "true", "width" => "450", "height" => "130"), true, 'autobr', true, "", true, true, true, false, ""), "<b>" . g_l('global', "[description]") . "</b>", "left", "defaultfont", "", "", "", "", "", 0);
			$saveBut = we_button::create_button("save", "javascript:weWysiwygSetHiddenText();we_checkName();");
		}



		we_html_tools::htmlTop();
		we_html_tools::protect();
		print we_html_element::jsScript(JS_DIR . 'we_textarea.js') . we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
function we_cmd(){
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]){
		case "openSelector":
			new jsWindow(url,"we_selector",-1,-1,' . WINDOW_SELECTOR_WIDTH . ',' . WINDOW_SELECTOR_HEIGHT . ',true,true,true,true);
			break;
		default:
			for(var i = 0; i < arguments.length; i++){
				args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
			}
			eval(\'parent.we_cmd(\'+args+\')\');
	}
}
function we_checkName() {
	var regExp = /\'|"|>|<|\\\|\\//;
	if(regExp.test(document.getElementById("category").value)) {
' .
				we_message_reporting::getShowMessageCall(sprintf(g_l('weEditor', "[category][we_filename_notValid]"), $path), we_message_reporting::WE_MESSAGE_ERROR) . '
	} else {
		document.we_form.submit();
	}
}');
		print STYLESHEET . '</head><body class="defaultfont" style="margin:0px;padding: 15px 0 0 10px;background-image:url(' . IMAGE_DIR . 'backgrounds/aquaBackgroundLineLeft.gif);">
' . ($showPrefs ? '
	<form onsubmit="weWysiwygSetHiddenText();"; action="' . $_SERVER["SCRIPT_NAME"] . '" name="we_form" method="post" target="fscmd"><input type="hidden" name="what" value="' . self::CHANGE_CAT . '" /><input type="hidden" name="catid" value="' . $_REQUEST["catid"] . '" />
		' . $table->getHtml() . "<br />" . $ta . "<br />" . $saveBut . '
	</div>		' : '' ) . '
</body></html>';
	}

}
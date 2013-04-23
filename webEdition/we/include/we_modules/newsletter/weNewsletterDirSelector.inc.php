<?php

/**
 * webEdition CMS
 *
 * $Rev: 5634 $
 * $Author: mokraemer $
 * $Date: 2013-01-24 00:04:56 +0100 (Thu, 24 Jan 2013) $
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
class weNewsletterDirSelector extends we_dirSelector{

	var $fields = "ID,ParentID,Text,Path,IsFolder,Icon";

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $we_editDirID = "", $FolderText = "", $rootDirID = 0, $multiple = 0){
		$table = NEWSLETTER_TABLE;
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $we_editDirID, $FolderText, $rootDirID, $multiple);
	}

	function printCreateFolderHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		print '<script type="text/javascript"><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if($txt == ""){
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$folder = new we_folder();
			$folder->we_new();
			$folder->setParentID($this->dir);
			$folder->Table = $this->table;
			$folder->Text = $txt;
			$folder->CreationDate = time();
			$folder->ModDate = time();
			$folder->Filename = $txt;
			$folder->Published = time();
			$folder->Path = $folder->getPath();
			$folder->CreatorID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
			$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
			$this->db->query("SELECT ID FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "'");
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('weEditor', "[folder][response_path_exists]"), $folder->Path);
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('/[^a-z0-9\._\-]/i', $folder->Filename)){
					$we_responseText = sprintf(g_l('weEditor', "[folder][we_filename_notValid]"), $folder->Path);
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					$folder->we_save();
					print 'var ref;
if(top.opener.top.content.makeNewEntry) ref = top.opener.top.content;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
ref.makeNewEntry("' . $folder->Icon . '",' . $folder->ID . ',"' . $folder->ParentID . '","' . $txt . '",1,"' . $folder->ContentType . '","' . $this->table . '");
';
					if($this->canSelectDir){
						print 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";
';
					}
				}
			}
		}


		print
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	function printDoRenameFolderHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		print '<script type="text/javascript"><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		if($txt == ""){
			print we_message_reporting::getShowMessageCall(g_l('weEditor', "[folder][filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->ModDate = time();
			$folder->Filename = $txt;
			$folder->Published = time();
			$folder->Path = $folder->getPath();
			$folder->ModifierID = isset($_SESSION["user"]["ID"]) ? $_SESSION["user"]["ID"] : "";
			$this->db->query("SELECT ID,Text FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "' AND ID != " . intval($this->we_editDirID));
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('weEditor', "[folder][response_path_exists]"), $folder->Path);
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('/[^a-z0-9\._\-]/i', $folder->Filename)){
					$we_responseText = sprintf(g_l('weEditor', "[folder][we_filename_notValid]"), $folder->Path);
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					if(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editDirID), "Text", $this->db) != $txt){
						$folder->we_save();
						print 'var ref;
if(top.opener.top.content.makeNewEntry) ref = top.opener.top.content;
else if(top.opener.top.opener) ref = top.opener.top.opener.top;
';
						print 'ref.updateEntry(' . $folder->ID . ',"' . $txt . '","' . $folder->ParentID . '","' . $this->table . '");
';
						if($this->canSelectDir){
							print 'top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.fsfooter.document.we_form.fname.value = "' . $folder->Text . '";
';
						}
					}
				}
			}
		}


		print $this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	function query(){
		$this->db->query('SELECT ' . $this->db->escape($this->fields) . ' FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) .
			getWsQueryForSelector(NEWSLETTER_TABLE) .
			($this->order ? (' ORDER BY ' . $this->order) : '')
		);
	}

	function printFramesetJSFunctionAddEntries(){
		$ret = '';
		while($this->next_record()) {
			$ret.='addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . addcslashes($this->f("Text"), '"') . '",' . $this->f("IsFolder") . ',"' . addcslashes($this->f("Path"), '"') . '");';
		}
		return we_html_element::jsElement($ret);
	}

	function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->next_record()) {
			$ret.='top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '");' . "\n";
		}
		$ret.=($this->userCanMakeNewDir() ?
				'top.fsheader.enableNewFolderBut();' :
				'top.fsheader.disableNewFolderBut();');
		return $ret;
	}

	function printFramesetJSFunctionAddEntry(){
		return we_html_element::jsElement('
		function addEntry(ID,icon,text,isFolder,path){
		entries[entries.length] = new entry(ID,icon,text,isFolder,path);
		}');
	}

	function printHeaderHeadlines(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="550">
	<tr>
		<td>' . we_html_tools::getPixel(25, 14) . '</td>
		<td class="selector"colspan="2"><b><a href="#" onclick="javascript:top.orderIt(\'IsFolder DESC, Text\');">' . g_l('fileselector', "[filename]") . '</a></b></td>
	</tr>
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
		<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
		<td width="300">' . we_html_tools::getPixel(300, 1) . '</td>
	</tr>
</table>';
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
#	if(makeNewFolder ||  we_editDirID){
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
}
') . '</head>
<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
<form name="we_form" target="fscmd" action="' . $_SERVER["SCRIPT_NAME"] . '" onSubmit="document.we_form.we_FolderText.value=escape(document.we_form.we_FolderText_tmp.value);return true;">
#				if(top.we_editDirID){
<input type="hidden" name="what" value="' . self::DORENAMEFOLDER . '" />
<input type="hidden" name="we_editDirID" value="#\'+top.we_editDirID+#\'" />
#				}else{
<input type="hidden" name="what" value="' . self::CREATEFOLDER . '" />
#				}
<input type="hidden" name="order" value="#\'+top.order+#\'" />
<input type="hidden" name="rootDirID" value="' . $this->rootDirID . '" />
<input type="hidden" name="table" value="' . $this->table . '" />
<input type="hidden" name="id" value="#\'+top.currentDir+#\'" />
<table border="0" cellpadding="0" cellspacing="0" width="100%">
#				if(makeNewFolder){
<tr style="background-color:#DFE9F5;">
<td align="center"><img src="' . ICON_DIR . we_base_ContentTypes::FOLDER_ICON . '" width="16" height="18" border="0" /></td>
<td><input type="hidden" name="we_FolderText" value="' . g_l('fileselector', "[new_folder_name]") . '" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="' . g_l('fileselector', "[new_folder_name]") . '"  class="wetextinput" onBlur="this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" /></td>
</tr>
#				}
');
		?>

				for(i=0;i < entries.length; i++){
					var onclick = ' onClick="weonclick(<?php echo (we_base_browserDetect::isIE() ? "this" : "event") ?>);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick('+entries[i].ID+',0);}else{top.wasdblclick=0;}\',300);return true"';
					var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick('+entries[i].ID+',1);return true;"';
					d.writeln('<tr id="line_'+entries[i].ID+'" style="' + ((entries[i].ID == top.currentID && (!makeNewFolder) )  ? 'background-color:#DFE9F5;' : '')+'cursor:pointer;'+((we_editDirID != entries[i].ID) ? '' : '' )+'"'+((we_editDirID || makeNewFolder) ? '' : onclick)+ (entries[i].isFolder ? ondblclick : '') + ' >');
					d.writeln('<td class="selector" width="25" align="center">');
					d.writeln('<img src="<?php print ICON_DIR; ?>'+entries[i].icon+'" width="16" height="18" border="0" />');
					d.writeln('</td>');
					if(we_editDirID == entries[i].ID){
						d.writeln('<td class="selector">');
						d.writeln('<input type="hidden" name="we_FolderText" value="'+entries[i].text+'" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="'+entries[i].text+'" class="wetextinput" onBlur="this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" />');
					}else{
						d.writeln('<td class="selector" style="" >');
						d.writeln(cutText(entries[i].text,24));
					}
					d.writeln('</td>');
					d.writeln('</tr><tr><td colspan="3"><?php print we_html_tools::getPixel(2, 1); ?></td></tr>');
				}
		<?php echo self::makeWriteDoc('
<tr>
<td width="25">' . we_html_tools::getPixel(25, 2) . '</td>
<td>' . we_html_tools::getPixel(200, 2) . '</td>
</tr>
</table></form>
#				if(makeNewFolder || top.we_editDirID){
	<script type="text/javascript">document.we_form.we_FolderText_tmp.focus();document.we_form.we_FolderText_tmp.select();</script>
#				}
</body>
'); ?>
				d.close();
			}
			//-->
		</script>
		<?php
	}

	function userCanSeeDir($showAll = false){
		return true;
	}

	function userCanRenameFolder(){
		return we_hasPerm('EDIT_NEWSLETTER');
	}

	function userCanMakeNewDir(){
		return we_hasPerm('NEW_NEWSLETTER');
	}

	function userHasRenameFolderPerms(){
		return we_hasPerm('EDIT_NEWSLETTER');
	}

	function userHasFolderPerms(){
		return we_hasPerm('NEW_NEWSLETTER');
	}

}
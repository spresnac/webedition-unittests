<?php

/**
 * webEdition CMS
 *
 * $Rev: 5443 $
 * $Author: mokraemer $
 * $Date: 2012-12-25 22:12:27 +0100 (Tue, 25 Dec 2012) $
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
class we_votingDirSelector extends we_dirSelector{

	function __construct($id, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $we_editDirID = "", $FolderText = ""){
		parent::__construct($id, VOTING_TABLE, stripslashes($JSIDName), stripslashes($JSTextName), $JSCommand, $order, "", $we_editDirID, $FolderText);
		$this->title = g_l('fileselector', '[votingDirSelector][title]');
		$this->userCanMakeNewFolder = true;
	}

	function printHeaderHeadlines(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="550">
	<tr>
		<td>' . we_html_tools::getPixel(25, 14) . '</td>
		<td class="selector"colspan="2"><b><a href="#" onclick="javascript:top.orderIt(\'IsFolder DESC, Text\');">' . g_l('modules_voting', '[name]') . '</a></b></td>
	</tr>
	<tr>
		<td width="25">' . we_html_tools::getPixel(25, 1) . '</td>
		<td width="200">' . we_html_tools::getPixel(200, 1) . '</td>
		<td width="300">' . we_html_tools::getPixel(300, 1) . '</td>
	</tr>
</table>';
	}

	function printFooterTable(){
		$cancel_button = we_button::create_button("cancel", "javascript:top.exit_close();");
		$yes_button = we_button::create_button("ok", "javascript:press_ok_button();");
		$buttons = we_button::position_yes_no_cancel($yes_button, null, $cancel_button);
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
			<b>' . g_l('modules_voting', '[name]') . '</b>
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

	function printHeaderTableExtraCols(){
		$makefolderState = we_hasPerm("NEW_VOTING");
		return '<td width="10">' . we_html_tools::getPixel(10, 10) . '</td><td width="40">' .
			we_html_element::jsElement('makefolderState=' . $makefolderState . ';') .
			we_button::create_button("image:btn_new_dir", "javascript:if(makefolderState==1){top.drawNewFolder();}", true, -1, 22, "", "", $makefolderState ? false : true) .
			'</td>';
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
}') . '</head>
<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
<form name="we_form" target="fscmd" action="' . $_SERVER["SCRIPT_NAME"] . '">');

		?>
				if(top.we_editDirID){
					d.writeln('<input type="hidden" name="what" value="<?php print self::DORENAMEFOLDER; ?>" />');
					d.writeln('<input type="hidden" name="we_editDirID" value="'+top.we_editDirID+'" />');
				}else{
					d.writeln('<input type="hidden" name="what" value="<?php print self::CREATEFOLDER; ?>" />');
				}
				d.writeln('<input type="hidden" name="order" value="'+top.order+'" />');
				d.writeln('<input type="hidden" name="rootDirID" value="<?php print $this->rootDirID; ?>" />');
				d.writeln('<input type="hidden" name="table" value="<?php print $this->table; ?>" />');
				d.writeln('<input type="hidden" name="id" value="'+top.currentDir+'" />');
				d.writeln('<table border="0" cellpadding="0" cellspacing="0" width="100%">');
				if(makeNewFolder){
					d.writeln('<tr style="background-color:#DFE9F5;">');
					d.writeln('<td align="center"><img src="<?php print ICON_DIR . we_base_ContentTypes::FOLDER_ICON; ?>" width="16" height="18" border="0" /></td>');
					d.writeln('<td><input type="hidden" name="we_FolderText" value="<?php print g_l('modules_voting', '[newFolder]') ?>" /><input onMouseDown="self.inputklick=true" name="we_FolderText_tmp" type="text" value="<?php print g_l('modules_voting', '[newFolder]') ?>"  class="wetextinput" onBlur="this.className=\'wetextinput\';" onFocus="this.className=\'wetextinputselected\'" style="width:100%" /></td>');
					d.writeln('</tr>');
				}
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
				d.writeln('<tr>');
				d.writeln('<td width="25"><?php print we_html_tools::getPixel(25, 2) ?></td>');
				d.writeln('<td><?php print we_html_tools::getPixel(200, 2) ?></td>');
				d.writeln('</tr>');
				d.writeln('</table></form>');
				if(makeNewFolder || top.we_editDirID){
					d.writeln('<scr'+'ipt type="text/javascript">document.we_form.we_FolderText_tmp.focus();document.we_form.we_FolderText_tmp.select();</scr'+'ipt>');
				}
				d.writeln('</body>');
				d.close();
			}
			//-->
		</script>
		<?php
	}

	function printFramesetJSFunctionQueryString(){
		return we_html_element::jsElement('
		function queryString(what,id,o,we_editDirID){
		if(!o) o=top.order;
		if(!we_editDirID) we_editDirID="";
		return \'' . $_SERVER["SCRIPT_NAME"] . '?what=\'+what+\'&rootDirID=' .
				$this->rootDirID . (isset($this->open_doc) ?
					"&open_doc=" . $this->open_doc : '') .
				'&table=' . $this->table . '&id=\'+id+(o ? ("&order="+o) : "")+(we_editDirID ? ("&we_editDirID="+we_editDirID) : "");
		}');
	}

	function printFramesetJSFunctionEntry(){
		return we_html_element::jsElement('
		function entry(ID,icon,text,isFolder,path){
		this.ID=ID;
		this.icon=icon;
		this.text=text;
		this.isFolder=isFolder;
		this.path=path;
		}');
	}

	function printFramesetJSFunctionAddEntry(){
		return we_html_element::jsElement('
		function addEntry(ID,icon,text,isFolder,path){
		entries[entries.length] = new entry(ID,icon,text,isFolder,path);
		}');
	}

	function printFramesetJSFunctionAddEntries(){
		$ret = '';
		while($this->next_record()) {
			$ret.='addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '");' . "\n";
		}
		return we_html_element::jsElement($ret);
	}

	function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->next_record()) {
			$ret.= 'top.addEntry(' . $this->f("ID") . ',"' . $this->f("Icon") . '","' . $this->f("Text") . '",' . $this->f("IsFolder") . ',"' . $this->f("Path") . '");' . "\n";
		}
		return $ret;
	}

	function printCreateFolderHTML(){
		we_html_tools::htmlTop();
		we_html_tools::protect();

		print '<script type="text/javascript"><!--
top.clearEntries();
';
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = '';
		if(isset($_REQUEST['we_FolderText_tmp'])){
			$txt = rawurldecode($_REQUEST['we_FolderText_tmp']);
		}
		if($txt == ""){
			print we_message_reporting::getShowMessageCall(g_l('modules_voting', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$folder = new we_folder();
			$folder->we_new();
			$folder->setParentID($this->dir);
			$folder->Table = $this->table;
			$folder->Icon = we_base_ContentTypes::FOLDER_ICON;
			$folder->Text = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query("SELECT ID FROM " . $this->db->escape($this->table) . " WHERE Path='" . $this->db->escape($folder->Path) . "'");
			if($this->db->next_record()){
				print we_message_reporting::getShowMessageCall(g_l('modules_voting', '[folder_path_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(weVoting::filenameNotValid($folder->Text)){
					print we_message_reporting::getShowMessageCall(g_l('modules_voting', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					$folder->we_save();
					print 'var ref;
if(top.opener.top.content.makeNewEntry){
	ref = top.opener.top.content;
	ref.makeNewEntry("' . we_base_ContentTypes::FOLDER_ICON . '",' . $folder->ID . ',"' . $folder->ParentID . '","' . $txt . '",1,"folder","' . $this->table . '",1);
}
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

	function query(){
		$this->db->query('SELECT ' . $this->db->escape($this->fields) . ' FROM ' .
			$this->db->escape($this->table) .
			" WHERE IsFolder=1 AND ParentID=" . intval($this->dir) . " " . self::getUserExtraQuery($this->table));
	}

	static function getUserExtraQuery($table, $useCreatorID = true){
		$userExtraSQL = ' AND ((1 ' . makeOwnersSql(false) . ') ';

		if(get_ws($table)){
			$userExtraSQL .= getWsQueryForSelector($table);
		} else if(defined("OBJECT_FILES_TABLE") && $table == OBJECT_FILES_TABLE && (!$_SESSION["perms"]["ADMINISTRATOR"])){
			$wsQuery = "";
			$ac = getAllowedClasses($this->db);
			foreach($ac as $cid){
				$path = id_to_path($cid, OBJECT_TABLE);
				$wsQuery .= " Path LIKE '$path/%' OR Path='$path' OR ";
			}
			if($wsQuery){
				$userExtraSQL .= ' AND (' . substr($wsQuery, 0, strlen($wsQuery) - 3) . ')';
			}
		} else{
			$userExtraSQL.=' OR RestrictOwners=0 ';
		}
		return $userExtraSQL . ')';
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
			print we_message_reporting::getShowMessageCall(g_l('modules_voting', '[folder_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$folder = new we_folder();
			$folder->initByID($this->we_editDirID, $this->table);
			$folder->Text = $txt;
			$folder->Filename = $txt;
			$folder->Path = $folder->getPath();
			$this->db->query("SELECT ID,Text FROM " . $this->db->escape($this->table) . " WHERE Path='" . $folder->Path . "' AND ID != '" . $this->we_editDirID . "'");
			if($this->db->next_record()){
				$we_responseText = sprintf(g_l('modules_voting', '[folder_exists]'), $folder->Path);
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(preg_match('/[%/\\"\']/', $folder->Text)){
					$we_responseText = g_l('modules_voting', '[wrongtext]');
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					if(f("SELECT Text FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($this->we_editDirID), "Text", $this->db) != $txt){
						$folder->we_save();
						print 'var ref;
if(top.opener.top.content.updateEntry){
	ref = top.opener.top.content;
	ref.updateEntry(' . $folder->ID . ',"' . $txt . '","' . $folder->ParentID . '",1);
}
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

		print
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

}
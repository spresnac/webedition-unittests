<?php

/**
 * webEdition CMS
 *
 * $Rev: 5576 $
 * $Author: mokraemer $
 * $Date: 2013-01-16 21:56:32 +0100 (Wed, 16 Jan 2013) $
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
class we_multiSelector extends we_fileselector{

	const SETDIR = 5;
	const CREATEFOLDER = 8;
	const DEL = 11;

	var $multiple = true;

	function __construct($id, $table = FILE_TABLE, $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $rootDirID = 0, $multiple = true, $filter = ""){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $sessionID, $rootDirID, $filter);
		if(defined('CUSTOMER_TABLE') && $table == CUSTOMER_TABLE){
			$this->fields = str_replace('Text', 'CONCAT(Text," (",Forename," ", Surname,")") AS Text', $this->fields);
		}

		$this->multiple = $multiple;
	}

	function printFramesetJSFunctions(){
		return parent::printFramesetJSFunctions() . we_html_element::jsElement('
var allIDs ="";
var allPaths ="";
var allTexts ="";
var allIsFolder ="";

function fillIDs() {
	allIDs =",";
	allPaths =",";
	allTexts =",";
	allIsFolder =",";

	for	(var i=0;i < entries.length; i++) {
		if (isFileSelected(entries[i].ID)) {
			allIDs += (entries[i].ID + ",");
			allPaths += (entries[i].path + ",");
			allTexts += (entries[i].text + ",");
			allIsFolder += (entries[i].isFolder + ",");
		}
	}
	if(currentID != ""){
		if(allIDs.indexOf(","+currentID+",") == -1){
			allIDs += (currentID + ",");
		}
	}
	if(currentPath != ""){
		if(allPaths.indexOf(","+currentPath+",") == -1){
			allPaths += (currentPath + ",");
			allTexts += (we_makeTextFromPath(currentPath) + ",");
		}
	}

	if (allIDs == ",") {
		allIDs = "";
	}
	if (allPaths == ",") {
		allPaths = "";
	}
	if (allTexts == ",") {
		allTexts = "";
	}

	if (allIsFolder == ",") {
		allIsFolder = "";
	}
}

function we_makeTextFromPath(path){
	position =  path.lastIndexOf("/");
	if(position > -1 &&  position < path.length){
		return path.substring(position+1);
	}else{
		return "";
	}
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
var wasdblclick=false
var tout=null
document.onclick = weonclick;
function weonclick(e){
if(document.all){
if(event.ctrlKey || event.altKey){ ctrlpressed=true;}
if(event.shiftKey){ shiftpressed=true;}
}else{
if(e.altKey || e.metaKey || e.ctrlKey){ ctrlpressed=true;}
if(e.shiftKey){ shiftpressed=true;}
}' . ($this->multiple ? '
if((self.shiftpressed==false) && (self.ctrlpressed==false)){top.unselectAllFiles();}' : '
top.unselectAllFiles();') . '
}
') . '</head>
<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">');
		?>
				for(i=0;i < entries.length; i++){
					var onclick = ' onClick="weonclick(<?php echo (we_base_browserDetect::isIE() ? "this" : "event") ?>);tout=setTimeout(\'if(top.wasdblclick==0){top.doClick('+entries[i].ID+',0);}else{top.wasdblclick=0;}\',300);return true;"';
					var ondblclick = ' onDblClick="top.wasdblclick=1;clearTimeout(tout);top.doClick('+entries[i].ID+',1);return true;"';
					d.writeln('<tr' + ((entries[i].ID == top.currentID)  ? ' style="background-color:#DFE9F5;cursor:pointer;"' : '') + ' id="line_'+entries[i].ID+'" style="cursor:pointer;"'+onclick+ (entries[i].isFolder ? ondblclick : '') + ' >');
					d.writeln('<td class="selector" width="25" align="center">');
					d.writeln('<img src="<?php print ICON_DIR; ?>'+entries[i].icon+'" width="16" height="18" border="0" />');
					d.writeln('</td>');
					d.writeln('<td class="selector"  title="'+entries[i].text+'">');
					d.writeln(cutText(entries[i].text,80));
					d.writeln('</td>');
					d.writeln('</tr><tr><td colspan="2"><?php print we_html_tools::getPixel(2, 1); ?></td></tr>');
				}<?php echo self::makeWriteDoc('
		<tr><td width="25">' . we_html_tools::getPixel(25, 2) . '</td>
		<td>' . we_html_tools::getPixel(150, 2) . '</td>
		</tr></table></body>'); ?>
						d.close();
					}
					//->
		</script>
		<?php
	}

	function printFramesetJSDoClickFn(){
		return we_html_element::jsElement('
function doClick(id,ct){
	if(ct==1){
		if(wasdblclick){
			setDir(id);
			setTimeout("wasdblclick=0;",400);
		}
	}else{' .
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

			}else if(!fsbody.ctrlpressed){' : '') . '

			selectFile(id);' .
				($this->multiple ? '
			}else{
				if (isFileSelected(id)) {
					unselectFile(id);
				}else{
					selectFile(id);
				}
			}' : '') . '

	}
	if(fsbody.ctrlpressed){
		fsbody.ctrlpressed = 0;
	}
	if(fsbody.shiftpressed){
		fsbody.shiftpressed = 0;
	}
}');
	}

	function printFramesetUnselectFileHTML(){
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
}');
	}

	function printFramesetSelectFilesFromHTML(){
		return we_html_element::jsElement('
function selectFilesFrom(from,to){
	unselectAllFiles();
	for	(var i=from;i <= to; i++){
		selectFile(entries[i].ID);
	}
}');
	}

	function printFramesetGetFirstSelectedHTML(){
		return we_html_element::jsElement('
function getFirstSelected(){
	for	(var i=0;i < entries.length; i++){
		if(top.fsbody.document.getElementById("line_"+entries[i].ID).style.backgroundColor!="white"){
			return i;
		}
	}
	return -1;
}');
	}

	function printFramesetGetPositionByIDHTML(){
		return we_html_element::jsElement('
function getPositionByID(id){
	for	(var i=0;i < entries.length; i++){
		if(entries[i].ID == id){
			return i;
		}
	}
	return -1;
}');
	}

	function printFramesetIsFileSelectedHTML(){
		return we_html_element::jsElement('
function isFileSelected(id){
	return (top.fsbody.document.getElementById("line_"+id).style.backgroundColor && (top.fsbody.document.getElementById("line_"+id).style.backgroundColor!="white"));
}');
	}

	function printFramesetUnselectAllFilesHTML(){
		return we_html_element::jsElement('
		function unselectAllFiles(){
			for	(var i=0;i < entries.length; i++){
				if(elem = top.fsbody.document.getElementById("line_"+entries[i].ID))
					elem.style.backgroundColor="white";
			}
			top.fsfooter.document.we_form.fname.value = "";
		}');
	}

	function printFramesetSelectFileHTML(){
		return we_html_element::jsElement('
function selectFile(id){
	if(id){
		e = getEntry(id);

		if(
		top.fsfooter.document.we_form.fname.value != e.text &&
			top.fsfooter.document.we_form.fname.value.indexOf(e.text+",") == -1 &&
			top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 &&
			top.fsfooter.document.we_form.fname.value.indexOf(","+e.text+",") == -1 ){

			top.fsfooter.document.we_form.fname.value =  top.fsfooter.document.we_form.fname.value ?
				(top.fsfooter.document.we_form.fname.value + "," + e.text) :
				e.text;
		}
		top.fsbody.document.getElementById("line_"+id).style.backgroundColor="#DFE9F5";
		currentPath = e.path;
		currentID = id;
	}else{
		top.fsfooter.document.we_form.fname.value = "";
		currentPath = "";
	}
}');
	}

}
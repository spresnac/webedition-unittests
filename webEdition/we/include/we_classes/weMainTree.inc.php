<?php

/**
 * webEdition CMS
 *
 * $Rev: 5847 $
 * $Author: mokraemer $
 * $Date: 2013-02-19 20:54:58 +0100 (Tue, 19 Feb 2013) $
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
class weMainTree extends weTree{

	function __construct($frameset = "", $topFrame = "", $treeFrame = "", $cmdFrame = ""){
		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);

		$this->setNodeLayouts(array(
			'item' => 'item',
			'group' => 'group',
			'threedots' => 'changed',
			'item-disabled' => 'disabled',
			'group-disabled' => 'disabled',
			'group-disabled-open' => 'disabled',
			'item-checked' => 'checked_item',
			'group-checked' => 'checked_group',
			'group-open' => 'group',
			'group-checked-open' => 'checked_group',
			'item-notpublished' => 'notpublished',
			'item-checked-notpublished' => 'checked_notpublished',
			'item-changed' => 'changed',
			'item-checked-changed' => 'checked_changed',
			'item-selected' => 'selected_item',
			'item-selected-notpublished' => 'selected_notpublished_item',
			'item-selected-changed' => 'selected_changed_item',
			'group-selected' => 'selected_group',
			'group-selected-open' => 'selected_open_group'
		));

		$this->setStyles(array(
			'.item {color: black; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; cursor: pointer;}',
			'.item a { text-decoration:none;}',
			'.group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; cursor: pointer;}',
			'.group a { text-decoration:none;}',
			'.checked_item {color: black; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.checked_item a { text-decoration:none;}',
			'.checked_group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.checked_group a { text-decoration:none;}',
			'.notpublished {color: red; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; cursor: pointer;}',
			'.notpublished a { text-decoration:none;}',
			'.checked_notpublished {color: red; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.checked_notpublished a { text-decoration:none;}',
			'.changed {color: #3366CC; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; cursor: pointer;}',
			'.changed a { text-decoration:none;}',
			'.checked_changed {color: #3366CC; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.checked_changed a { text-decoration:none;}',
			'.disabled {color: grey; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; cursor: pointer;}',
			'.disabled a { text-decoration:none;}',
			'.selected_item {color: black; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.selected_item a { text-decoration:none;}',
			'.selected_notpublished_item {color: red; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.selected_notpublished_item a { text-decoration:none;}',
			'.selected_changed_item {color: #3366CC; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.selected_changed_item a { text-decoration:none;}',
			'.selected_group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.selected_group a { text-decoration:none;}',
			'.selected_open_group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #D4DBFA; cursor: pointer;}',
			'.selected_open_group a { text-decoration:none;}',
			)
		);
	}

	function getJSOpenClose(){

		return '
function openClose(id) {

	if(id=="") return;
	var eintragsIndex = indexOfEntry(id);
	var status;

	if(treeData[eintragsIndex].open==0) openstatus=1;
	else openstatus=0;
	treeData[eintragsIndex].open=openstatus;
	if(openstatus && treeData[eintragsIndex].loaded!=1){
		we_cmd("loadFolder",top.treeData.table,treeData[eintragsIndex].id);
		toggleBusy(1);
	}else{
		we_cmd("closeFolder",top.treeData.table,treeData[eintragsIndex].id);
		drawTree();
	}
	if(openstatus==1) treeData[eintragsIndex].loaded=1;
}';
	}

	function getJSTreeFunctions(){

		return weTree::getJSTreeFunctions() . '
function doClick(id){
	var node=' . $this->topFrame . '.get(id);
	var ct=node.contenttype;
	var table=node.table;
	setScrollY();
	if(' . $this->topFrame . '.wasdblclick && ct != \'folder\' && table!=\'' . TEMPLATES_TABLE . '\'' . (defined("OBJECT_TABLE") ? ' && table!=\'' . OBJECT_TABLE . '\' && table!=\'' . OBJECT_FILES_TABLE . '\'' : '' ) . '){
		top.openBrowser(id);
		setTimeout(\'wasdblclick=0;\',400);
	} else {
		top.weEditorFrameController.openDocument(table,id,ct);
	}
}';
	}

	function getJSUpdateTreeScript($doc, $select = true){
		$published = ((($doc->Published != 0) && ($doc->Published < $doc->ModDate) && ($doc->ContentType == "text/html" || $doc->ContentType == "text/webedition" || $doc->ContentType == "objectFile")) ? -1 : $doc->Published);

		//	This is needed in SeeMode
		$s = '
isEditInclude = false;
weWindow = top;
while(1){
	if(!weWindow.top.opener || weWindow.top.opener.top.win){
			break;
	} else {
		 isEditInclude = true;
		 weWindow = weWindow.opener.top;
	}
}';
		if($_SESSION['weS']['we_mode'] == "seem"){
			return $s;
		}

		$s .= '
if(weWindow.treeData){
	var obj = weWindow.treeData;
	var isIn = false;' .
			($select ? '
	weWindow.treeData.selection_table="' . $doc->Table . '";
	weWindow.treeData.selection="' . $doc->ID . '";' :
				'weWindow.treeData.unselectnode();') . '
	if(weWindow.treeData.table == "' . $doc->Table . '"){
		if(weWindow.treeData[top.indexOfEntry(' . $doc->ParentID . ')]){
				var attribs=new Array();
				attribs["id"]=\'' . $doc->ID . '\';
				attribs["parentid"]=\'' . $doc->ParentID . '\';
				attribs["text"]=\'' . $doc->Text . '\';
				attribs["published"]=\'' . $published . '\';
				attribs["table"]=\'' . $doc->Table . '\';

				if(' . $this->topFrame . '.indexOfEntry(' . $doc->ParentID . ')!=-1){
					var visible=' . $this->topFrame . '.treeData[' . $this->topFrame . '.indexOfEntry(' . $doc->ParentID . ')].open;
				}else{
					var visible=0
				}

				if(' . $this->topFrame . '.indexOfEntry(' . $doc->ID . ')!=-1){
						isIn=true;
						var ai = 1;
						while (ai <= ' . $this->topFrame . '.treeData.len) {
							if (' . $this->topFrame . '.treeData[ai].id==attribs["id"]){
								' . $this->topFrame . '.treeData[ai].text=attribs["text"];
								' . $this->topFrame . '.treeData[ai].parentid=attribs["parentid"];
								' . $this->topFrame . '.treeData[ai].table=attribs["table"];
								' . $this->topFrame . '.treeData[ai].published=attribs["published"];
							}
							++ai;
						}
			}else{
				attribs["icon"]=\'' . $doc->Icon . '\';
				attribs["contenttype"]=\'' . $doc->ContentType . '\';
				attribs["isclassfolder"]=\'' . (isset($doc->IsClassFolder) ? $doc->IsClassFolder : false) . '\';
				attribs["isnoteditable"]=\'' . (isset($doc->IsNotEditable) ? $doc->IsNotEditable : false) . '\';
				attribs["checked"]=\'0\';
				attribs["typ"]=\'' . ($doc->IsFolder ? "group" : "item") . '\';
				attribs["open"]=\'0\';
				attribs["disabled"]=\'0\';
				attribs["tooltip"]=\'' . $doc->ID . '\';
				' . $this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
		}
		weWindow.drawTree();
	}else if(' . $this->topFrame . '.indexOfEntry(' . $doc->ID . ')!=-1){
		' . $this->topFrame . '.deleteEntry(' . $doc->ID . ');
	}
}
}';


		return $s;
	}

	function getJSGetLayout(){
		return '
function getLayout(){
		if(this.typ=="threedots") return treeData.node_layouts["threedots"];
		var layout_key=(this.typ=="group" ? "group" : "item")+
			(this.selected==1 ? "-selected" : "")+
			(this.disabled==1 ? "-disabled" : "")+
			(this.checked==1 ? "-checked" : "")+
			(this.open==1 ? "-open" : "")+
			(this.typ=="item" && this.published==0 ? "-notpublished" : "")+
			(this.typ=="item" && this.published==-1 ? "-changed" : "") ;

		return treeData.node_layouts[layout_key];
}';
	}

	function getJSInfo(){
		return '
function info(text) {
	t=TreeInfo.window.document.getElementById("infoField");
	s=TreeInfo.window.document.getElementById("search");
	if(text!=" "){
		s.style.display="none";
		t.style.display="block";
		t.innerHTML = text;
	} else {
		s.style.display="block";
		t.innerHTML = text;
		t.style.display="none";
	}
}';
	}

	function getJSUpdateItem(){
		return '
function updateEntry(id,text,pid,tab){
	//if((treeData.table == tab)&&(treeData[indexOfEntry(pid)])&&(treeData[indexOfEntry(pid)].loaded)){
	if((treeData.table == tab)&&(treeData[indexOfEntry(pid)])){
		var ai = 1;
		while (ai <= treeData.len) {
			if (treeData[ai].id==id){
				if(text) treeData[ai].text=text;
				if(pid) treeData[ai].parentid=pid;
				if(tab) treeData[ai].table=tab;
			}
			ai++;
		}
		drawTree();
	}
}';
	}

	function getJSMakeNewEntry(){
		return '
function makeNewEntry(icon,id,pid,txt,open,ct,tab){
	if(treeData.table == tab){
		if(treeData[indexOfEntry(pid)]){
			if(treeData[indexOfEntry(pid)].loaded){

				var attribs=new Array();

				attribs["id"]=id;
				attribs["icon"]=icon;
				attribs["text"]=txt;
				attribs["parentid"]=pid;
				attribs["open"]=open;
				attribs["typ"]=(ct=="folder" ? "group" : "item");
				attribs["table"]=tab;
				attribs["tooltip"]=id;
				attribs["contenttype"]=ct;


				attribs["disabled"]=0;
				if(attribs["typ"]=="item") attribs["published"]=0;

				attribs["selected"]=0;

				treeData.addSort(new node(attribs));

				drawTree();
			}
		}
	}
}';
	}

	function getJSIncludeFunctions(){
		return weTree::getJSIncludeFunctions() . '
we_scrollY["' . FILE_TABLE . '"] = 0;
we_scrollY["' . TEMPLATES_TABLE . '"] = 0;' .
			(defined("OBJECT_TABLE") ? '
we_scrollY["' . OBJECT_TABLE . '"] = 0;
we_scrollY["' . OBJECT_FILES_TABLE . '"] = 0;' :
				'') . '
treeData.table="' . FILE_TABLE . '";' .
			$this->getJSMakeNewEntry();
	}

	function getJSLoadTree($treeItems){
		$js = 'var attribs=new Array();';


		if(is_array($treeItems)){
			foreach($treeItems as $item){
				$buff = 'if(' . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){";
				foreach($item as $k => $v){
					$buff.='attribs["' . strtolower($k) . '"]=\'' . addslashes($v) . '\';';
				}

				$js.=$buff . $this->topFrame . '.treeData.add(new ' . $this->topFrame . '.node(attribs));
					}';
			}
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

}
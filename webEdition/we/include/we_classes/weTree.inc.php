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
class weTree{

	const DefaultWidth = 300;
	const MinWidth = 200;
	const MaxWidth = 1000;
	const StepWidth = 20;
	const DeleteWidth = 420;
	const MoveWidth = 500;
	const HiddenWidth = 24;

	protected $db;
	var $topFrame;
	var $treeFrame;
	var $cmdFrame;
	var $initialized = 0;
	var $treeItems = array();
	var $frameset = "";
	var $styles = array();
	var $tree_states = array(
		"edit" => 0,
		"select" => 1,
		"selectitem" => 2,
		"selectgroup" => 3,
	);
	var $tree_layouts = array(
		0 => "tree",
		1 => "tree",
		2 => "tree",
		3 => "tree"
	);
	var $node_layouts = array(
		"item" => "tree",
		"group" => "group"
	);
	var $tree_image_dir;
	var $tree_icon_dir;
	var $default_segment = 30;

//Initialization

	function __construct($frameset = "", $topFrame = "", $treeFrame = "", $cmdFrame = ""){
		$this->db = new DB_WE();
		$this->setTreeImageDir(TREE_IMAGE_DIR);
		$this->setTreeIconDir(ICON_DIR);
		if($frameset != "" && $topFrame != "" && $treeFrame != "" && $cmdFrame != ""){
			$this->init($frameset, $topFrame, $treeFrame, $cmdFrame);
		}

		$this->setStyles(array(
			'.item {color: black; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . ';}',
			'.item a { text-decoration:none;}',
			'.group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . ';}',
			'.group a { text-decoration:none;}',
			'.selected_item {color: black; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #6070B6; cursor: pointer;}',
			'.selected_item a { text-decoration:none;}',
			'.selected_group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . '; background-color: #6070B6; cursor: pointer;}',
			'.selected_group a { text-decoration:none;}',
		));

		$this->setItemsCount(getPref("default_tree_count"));
	}

	function init($frameset, $topFrame, $treeFrame, $cmdFrame){
		$this->frameset = $frameset;
		$this->setTopFrame($topFrame);
		$this->setTreeFrame($treeFrame);
		$this->setCmdFrame($cmdFrame);
		$this->initialized = 1;
	}

	function setTreeFrame($treeFrame){
		$this->treeFrame = $treeFrame;
	}

	function setTopFrame($topFrame){
		$this->topFrame = $topFrame;
	}

	function setCmdFrame($cmdFrame){
		$this->cmdFrame = $cmdFrame;
	}

	function setTreeImageDir($dir){
		$this->tree_image_dir = $dir;
	}

	function setTreeIconDir($dir){
		$this->tree_icon_dir = $dir;
	}

	function setTreeStates($tree_states){
		$this->tree_states = $tree_states;
	}

	function setTreeLayouts($tree_layout){
		$this->tree_layouts = $tree_layout;
	}

	function setNodeLayouts($node_layout){
		$this->node_layouts = $node_layout;
	}

	function setStyles($styles){
		$this->styles = $styles;
	}

	/*
	  the functions prints tree javascript
	  should be placed in a frame which doesn't reloads

	 */

	function getJSIncludeFunctions(){

		return $this->getJSDrawTree() .
			$this->getJSUpdateItem() .
			$this->getJSDeleteItem() .
			$this->getJSClearTree() .
			$this->getJSSetTreeState() .
			$this->getJSOpenClose() .
			$this->getJSGetTreeLayout() .
			$this->getJSApplyLayout() .
			$this->getJSGetLayout() .
			$this->getJSContainer() .
			$this->getJSAddNode() .
			$this->getJSRootAdd() .
			$this->getJSMakeFoldersOpenString() .
			$this->getJSCheckNode() .
			$this->getJSInfo() .
			$this->getJSSelectNode() .
			$this->getJSUnselectNode() .
			$this->getJSShowSegment() .
			$this->getJSClearItems();
	}

	function getJSTreeCode($withTag = true){
		$js = '
var treeData = new container();

var we_scrollY = new Array();
//var 	setScrollY;

' . $this->getJSIncludeFunctions() . '

function indexOfEntry(id){
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id == id){
			return ai;
		}
		ai++;
	}
	return -1;
}

function get(eintrag){var nf = new container();var ai = 1;while (ai <= treeData.len) {if (treeData[ai].id == eintrag) nf=treeData[ai];ai++;}return nf;}

function search(eintrag){var nf = new container();var ai = 1;while (ai <= treeData.len) {if (treeData[ai].parentid == eintrag) nf.add(treeData[ai]);ai++;}return nf;}

function add(object){this[++this.len] = object;}

function containerClear(){this.len =0;}

' . $this->getJSAddSortFunction() . '
' . $this->getJSTreeFunctions() . '

var startloc=0;
var treeHTML;
self.focus();
		';

		return ($withTag ? we_html_element::jsScript(JS_DIR . "images.js") . we_html_element::jsElement($js) : $js);
	}

	function getJSAddSortFunction(){
		return '
function addSort(object){
		this.len++;
		for(var i=this.len; i>0; i--){
			if(i > 1 && (this[i-1].text.toLowerCase() > object.text.toLowerCase()' . (!we_base_browserDetect::isMAC() ? " || (this[i-1].typ>object.typ)" : "" ) . ')){
				this[i] = this[i-1];
			}else{
				this[i] = object;
				break;
			}
		}
}';
	}

	function getJSTreeFunctions(){
		return '
//var clickCount=0;
var wasdblclick=0;
var tout=null;

function setScrollY(){
	if(' . $this->topFrame . '){
		if(' . $this->topFrame . '.we_scrollY){
			' . $this->topFrame . '.we_scrollY[treeData.table]=' . (we_base_browserDetect::isIE() ? 'document.body.scrollTop' : 'pageYOffset') . ';
		}
	}
}

function setSegment(id){
	var node=' . $this->topFrame . '.get(id);
	node.showsegment();
}';
	}

	function getJSOpenClose(){
		return '
function openClose(id){

	if(id=="") return;

	var eintragsIndex = indexOfEntry(id);
	var status;

	if(treeData[eintragsIndex].open==0) openstatus=1;
	else openstatus=0;
	treeData[eintragsIndex].open=openstatus;
	if(openstatus && treeData[eintragsIndex].loaded!=1){
		' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid="+id;
	}else{
		drawTree();
	}
	if(openstatus==1) treeData[eintragsIndex].loaded=1;
}';
	}

	function getJSCheckNode(){
		return '
function checkNode(imgName) {
	var object_name = imgName.substring(4,imgName.length);
	for(i=1;i<=treeData.len;i++) {

		if(treeData[i].id == object_name) {
			if(treeData[i].checked==1) {
				treeData[i].checked=0;
				treeData[i].applylayout();
				if(document.images) {
					try{
						eval("if("+treeData.treeFrame+".document.images[imgName]) "+treeData.treeFrame+".document.images[imgName].src=treeData.check0_img.src;");
					} catch(e) {
						self.Tree.setCheckNode(imgName);
					}
				}
				break;
			}else {
				treeData[i].checked=1;
				treeData[i].applylayout();
				if(document.images) {
					try{
						eval("if("+treeData.treeFrame+".document.images[imgName]) "+treeData.treeFrame+".document.images[imgName].src=treeData.check1_img.src;");
					} catch(e) {
						self.Tree.setUnCheckNode(imgName);
					}
				}
				break;
			}
		}

	}
	if(!document.images) {
		drawTree();
	}
}';
	}

	function getJSGetTreeLayout(){
		return '
				function getTreeLayout(){
						return this.tree_layouts[this.state];
				}';
	}

	function getJSGetLayout(){
		return '
				function getLayout(){
						var layout_key=(this.typ=="group" ? "group" : "item");
						return treeData.node_layouts[layout_key];
				}
';
	}

	function getJSSetTreeState(){
		return '
function setTreeState(){
	this.state=arguments[0];

	if(this.state==this.tree_states["edit"]){
		for(i=1;i<=this.len;i++) {
			if(this[i].checked == 1) this[i].checked=0;
		}

	}

}';
	}

	function getJSApplyLayout(){
		return '
		function applyLayout(){
			if(arguments[0])
				eval("if("+treeData.treeFrame+".document.getElementById(\"lab_"+this.id+"\"))"+treeData.treeFrame+".document.getElementById(\"lab_"+this.id+"\").className =\""+arguments[0]+"\";");
			else
				eval("if("+treeData.treeFrame+".document.getElementById(\"lab_"+this.id+"\"))"+treeData.treeFrame+".document.getElementById(\"lab_"+this.id+"\").className =\""+this.getlayout()+"\";");
		}
	';
	}

	function getJSRootAdd(){
		return '
		function rootEntry(id,text,rootstat,offset){
			this.id = id;
			this.text = text;
			this.open=1;
			this.loaded=1;
			this.typ = "root";
			this.offset = offset;
			this.rootstat = rootstat;
			this.showsegment=showSegment;
			this.clear=clearItems;

			return this;
		}
	';
	}

	function getJSAddNode(){
		return '
		function node(attribs){

			for(aname in attribs){
				var val=""+attribs[aname];
				this[aname] = val;
			}

			this.getlayout=getLayout;
			this.applylayout=applyLayout;
			this.showsegment=showSegment;
			this.clear=clearItems;
			return this;
		}
	';
	}

	function getJSSelectNode(){
		return '
		function selectNode(){
				if(arguments[0]){
        			var ind;
					if(treeData.selection!="" && treeData.selection_table==treeData.table){
						ind=indexOfEntry(treeData.selection);
						if(ind!=-1){
							var oldnode=get(treeData.selection);
							oldnode.selected=0;
							oldnode.applylayout();
						}
					}
					ind=indexOfEntry(arguments[0]);
					if(ind!=-1){
						var newnode=get(arguments[0]);
						newnode.selected=1;
						newnode.applylayout();
					}
					treeData.selection=arguments[0];
					treeData.selection_table=treeData.table;
				}
		}

	';
	}

	function getJSUnselectNode(){
		return '
 		function unselectNode(){
			if(treeData.selection!="" && treeData.table==treeData.selection_table){
				var ind=indexOfEntry(treeData.selection);
				if(ind!=-1){
					var node=get(treeData.selection);
					node.selected=0;
					if(node.applylayout) node.applylayout();
				}
				treeData.selection="";
			}
		}
	';
	}

	function getJSShowSegment(){
		return '
 		function showSegment(){
			parentnode=' . $this->topFrame . '.get(this.parentid);
			parentnode.clear();
			we_cmd("loadFolder",treeData.table,parentnode.id,"","","",this.offset);
			toggleBusy(1);
		}
	';
	}

	function getJSClearItems(){
		return '
 		function clearItems(){
			var ai = 1;
			var delid = 1;
			var deleted = 0;

			while (ai <= treeData.len) {
				if (treeData[ai].parentid == this.id){
					if(treeData[ai].contenttype=="group") deleted+=treeData[ai].clear();
					else{
						ind=ai;
                		while (ind <= treeData.len-1) {
                        		treeData[ind]=treeData[ind+1];
		                        ind++;
                		}
                		treeData.len[treeData.len]=null;
                		treeData.len--;
					}
					deleted++;
				}
				else{
					ai++;
				}
			}
			drawTree();
			return deleted;
		}
	';
	}

	function getJSContainer(){
		$ts = 'this.tree_states=new Array();';
		foreach($this->tree_states as $k => $v){
			$ts.='this.tree_states["' . $k . '"]="' . $v . '";';
		}

		$tl = 'this.tree_layouts=new Array();';
		foreach($this->tree_layouts as $k => $v){
			$tl.='this.tree_layouts["' . $k . '"]="' . $v . '";';
		}

		$nl = 'this.node_layouts=new Array();';
		foreach($this->node_layouts as $k => $v){
			$nl.='this.node_layouts["' . $k . '"]="' . $v . '";';
		}

		return '
function container(){
			this.len = 0;
			this.state=0;
			this.startloc=0;
			this.clear=containerClear;
			this.add = add;
			this.addSort = addSort;

			this.table="";

			this.selection="";
			this.selection_table="";
			this.selectnode=selectNode;
			this.unselectnode=unselectNode;

			this.setstate=setTreeState;
			this.getlayout=getTreeLayout;

			this.tree_image_dir="' . $this->tree_image_dir . '";
			this.tree_icon_dir="' . $this->tree_icon_dir . '";
			this.topFrame="' . $this->topFrame . '";
			this.treeFrame="' . $this->treeFrame . '";

			' . $ts . '
			' . $tl . '
			' . (isset($ns) ? $ns : "") . '
			' . $nl . '

			this.check0_img=new Image();
			this.check0_img.src="' . $this->tree_image_dir . 'check0.gif";

			this.check1_img=new Image();
			this.check1_img.src="' . $this->tree_image_dir . 'check1.gif";

			return this;
}';
	}

	function getJSUpdateItem(){
		return '
function updateEntry(attribs){
	var ai = 1;
	while (ai <= treeData.len) {
		if (treeData[ai].id==attribs["id"]) {
			for(aname in attribs){
				treeData[ai][aname] = attribs[aname];
			}
		}
		ai++;
	}
}';
	}

	function getJSDeleteItem(){
		return '
function deleteEntry(id){
	var ai = 1;
	var ind=0;
	while (ai <= treeData.len) {
		if (treeData[ai].id==id) {
				ind=ai;
				break;
		}
		ai++;
	}
	if(ind!=0){
		ai = ind;
		while (ai <= treeData.len-1) {
						treeData[ai]=treeData[ai+1];
						ai++;
		}
		treeData.len[treeData.len]=null;
		treeData.len--;
		drawTree();
	}
}';
	}

	function getJSMakeFoldersOpenString(){
		return '
function makeFoldersOpenString() {
	var op = "";
	for(i=1;i<=treeData.len;i++) {
		if(treeData[i].typ == "group" && treeData[i].open == 1)
			op +=  treeData[i].id+",";
	}
	op = op.substring(0,op.length-1);
	return op;
}';
	}

	function getJSClearTree(){
		return '
		function clearTree(){
			treeData.clear();
		}';
	}

	// Function which control how tree contenet will be displayed

	function getHTMLContruct($onresize = ''){
		$js = we_html_element::jsElement('
function setCheckNode(imgName){
	if(document.images[imgName]){document.images[imgName].src="' . TREE_IMAGE_DIR . 'check0.gif";}
}
function setUnCheckNode(imgName){
	if(document.images[imgName]){document.images[imgName].src="' . TREE_IMAGE_DIR . 'check1.gif";}
}');
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(//FIXME: missing title
					we_html_tools::getHtmlInnerHead() .
					STYLESHEET .
					we_html_element::cssElement(implode("\n", $this->styles)) . $js
				) .
				we_html_element::htmlBody(array(
					'bgcolor' => '#F3F7FF',
					'link' => '#000000',
					'alink' => '#000000',
					'vlink' => '#000000',
					'marginwidth' => '0',
					'marginheight' => '4',
					'leftmargin' => '0',
					'topmargin' => '4',
					'id' => 'treetable',
					'onresize' => $onresize
					), ''
				)
		);
	}

	function getHTMLContructX($onresize = ''){
		$js = we_html_element::jsElement('
function setCheckNode(imgName){
	if(document.images[imgName]){document.images[imgName].src="' . TREE_IMAGE_DIR . 'check0.gif";}
}
function setUnCheckNode(imgName){
	if(document.images[imgName]){document.images[imgName].src="' . TREE_IMAGE_DIR . 'check1.gif";}
}');
		return
			we_html_element::cssElement(implode("\n", $this->styles)) . $js .
			we_html_element::htmlDiv(array(
				'link' => '#000000',
				'alink' => '#000000',
				'vlink' => '#000000',
				'marginwidth' => '0',
				'marginheight' => '4',
				'leftmargin' => '0',
				'topmargin' => '4',
				'id' => 'treetable',
				'onresize' => $onresize
				), ''
		);
	}

	function getJSDrawTree(){

		return '
function drawTree(){
	if (typeof(' . $this->treeFrame . ') != "undefined") {
	} else {
		window.setTimeout("drawTree()", 500);
		return;
	}
	var out="<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td class=\""+treeData.getlayout()+"\"><nobr>"+draw(treeData.startloc,"")+"</nobr></td></tr></table>";' .
			$this->treeFrame . '.document.getElementById("treetable").innerHTML=out;
}' .
			$this->getJSDraw();
	}

	function getJSDraw(){
		$custom_draw = $this->getJSCustomDraw();
		$draw_code = empty($custom_draw) ? '' : 'switch(nf[ai].typ){';
		foreach($custom_draw as $ck => $cv){
			$draw_code.=' case "' . $ck . '":' . $cv . ' break;';
		}
		$draw_code .= empty($custom_draw) ? '' : '}';
		return'
function draw(startEntry,zweigEintrag){
	var nf = search(startEntry);
	var ai = 1;
	var row="";
	while (ai <= nf.len) {
		row+=zweigEintrag;
		var pind=indexOfEntry(nf[ai].parentid);
		if(pind!=-1)
			if(treeData[pind].open==1){
				' . $draw_code . '
			}
		ai++;
	}
	return row;
}

function zeichne(startEntry,zweigEintrag){
		draw(startEntry,zweigEintrag);
}';
	}

	function getJSCustomDraw($click_handler = ''){
		if($click_handler == ''){
			$click_handler = '
if(treeData.selection_table==treeData.table && nf[ai].id==treeData.selection) nf[ai].selected=1;

if(treeData.state==treeData.tree_states["select"] && nf[ai].disabled!=1) {
	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">";
} else if(treeData.state==treeData.tree_states["selectitem"] && nf[ai].disabled!=1 && nf[ai].typ == "item") {
	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">";
} else if(treeData.state==treeData.tree_states["selectgroup"] && nf[ai].disabled!=1 && nf[ai].typ == "group") {
	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">";
} else {
	if(nf[ai].disabled!=1) {
		row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript://\"  onDblClick=\"' . $this->topFrame . '.wasdblclick=1;clearTimeout(' . $this->topFrame . '.tout);' . $this->topFrame . '.doClick(\'"+nf[ai].id+"\');return true;\" onClick=\"' . $this->topFrame . '.tout=setTimeout(\'if(' . $this->topFrame . '.wasdblclick==0) ' . $this->topFrame . '.doClick(\\\\\'"+nf[ai].id+"\\\\\'); else ' . $this->topFrame . '.wasdblclick=0;\',300);return true;\" onMouseOver=\"' . $this->topFrame . '.info(\'ID:"+nf[ai].id+"\')\" onMouseOut=\"' . $this->topFrame . '.info(\' \');\">";
	}
}

row+="<img src="+treeData.tree_icon_dir+nf[ai].icon+" align=absmiddle border=0 alt=\"\">";

if(nf[ai].disabled!=1){
	row+="</a>";
}

if(treeData.state==treeData.tree_states["selectitem"] && (nf[ai].disabled!=1)) {
	var ci;

	if (nf[ai].typ == "group") {
		row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text +"</label>";
	} else {
		ci="' . $this->tree_image_dir . '"+(nf[ai].checked==1?"check1.gif":"check0.gif");
		row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\"><img src=\""+ci+"\" width=16 height=18 align=absmiddle border=0 alt=\"\" name=\"img_"+nf[ai].id+"\"></a>";
		row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\" onClick=\""+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">&nbsp;" + nf[ai].text +"</label>";

	}

}else if(treeData.state==treeData.tree_states["selectgroup"] && (nf[ai].disabled!=1)) {
	var ci;

	if (nf[ai].typ == "item") {
		row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text +"</label>";
	} else {
		ci="' . $this->tree_image_dir . '"+(nf[ai].checked==1?"check1.gif":"check0.gif");
		row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\"><img src=\""+ci+"\" width=16 height=18 align=absmiddle border=0 alt=\"\" name=\"img_"+nf[ai].id+"\"></a>";
		row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\" onClick=\""+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">&nbsp;" + nf[ai].text +"</label>";
	}
}else if(treeData.state==treeData.tree_states["select"] && (nf[ai].disabled!=1)) {
	var ci;
	ci="' . $this->tree_image_dir . '"+(nf[ai].checked==1?"check1.gif":"check0.gif");

	row+="<a href=\"javascript:"+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\"><img src=\""+ci+"\" width=16 height=18 align=absmiddle border=0 alt=\"\" name=\"img_"+nf[ai].id+"\"></a>";
	row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\" onClick=\""+treeData.topFrame+".checkNode(\'img_" + nf[ai].id + "\')\">&nbsp;" + nf[ai].text +"</label>";

}else{
	if(nf[ai].disabled!=1)
			row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript://\"  onDblClick=\"' . $this->topFrame . '.wasdblclick=1;clearTimeout(' . $this->topFrame . '.tout);' . $this->topFrame . '.doClick(\'"+nf[ai].id+"\');return true;\" onClick=\"' . $this->topFrame . '.tout=setTimeout(\'if(' . $this->topFrame . '.wasdblclick==0) ' . $this->topFrame . '.doClick(\\\\\'"+nf[ai].id+"\\\\\'); else ' . $this->topFrame . '.wasdblclick=0;\',300);return true;\" onMouseOver=\"' . $this->topFrame . '.info(\'ID:"+nf[ai].id+"\')\" onMouseOut=\"' . $this->topFrame . '.info(\' \');\">";

	row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\""+(nf[ai].tooltip!="" ? " title=\""+nf[ai].tooltip+"\"" : "")+" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text +"</label>";
	if(nf[ai].disabled!=1) row+="</a>";
}
row+="&nbsp;&nbsp;<br/>";';
		}

		return array(
			"item" => 'row+="&nbsp;&nbsp;<img src=' . $this->tree_image_dir . '"+(ai == nf.len?"kreuzungend.gif":"kreuzung.gif")+" width=19 height=18 align=absmiddle border=0>";' . $click_handler,
			"group" => '
var newAst = zweigEintrag;

var zusatz = (ai == nf.len) ? "end" : "";
var oc_img;
var oc_js;

oc_img="' . $this->tree_image_dir . '"+(nf[ai].open == 0?"auf":"zu")+zusatz+".gif";

if(nf[ai].disabled!=1) oc_js=treeData.topFrame+".setScrollY();"+treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"";
else oc_js="//";

oc_js=treeData.topFrame+".setScrollY();"+treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"";

row+="&nbsp;&nbsp;<a href=\"javascript:"+oc_js+" border=0><img src="+oc_img+" width=\"19\" height=\"18\" align=\"absmiddle\" border=\"0\" Alt=\"\"></a>";

var folder_icon;
folder_icon="folder"+(nf[ai].open==1 ? "open" : "")+(nf[ai].disabled==1 ? "_disabled" : "")+".gif";

nf[ai].icon=folder_icon;

' . $click_handler . '

if (nf[ai].open==1){
	newAst = newAst + "<img src=' . $this->tree_image_dir . '"+(ai == nf.len?"leer.gif":"strich2.gif")+" width=\"19\" height=\"18\" align=\"absmiddle\" border=\"0\">";
	row+=draw(nf[ai].id,newAst);
		}',
			"threedots" => '
row+="&nbsp;&nbsp;<img src=' . $this->tree_image_dir . '"+(ai == nf.len?"kreuzungend.gif":"kreuzung.gif")+" width=\"19\" height=\"18\" align=\"absmiddle\" border=\"0\">";
row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript://\"  onClick=\"' . $this->topFrame . '.setSegment(\'"+nf[ai].id+"\');return true;\">";
row+="<img src=\"' . $this->tree_image_dir . '/"+nf[ai].icon+"\" style=\"width:100px;height:7px\" alt=\"\">";
row+="</a>";
row+="&nbsp;&nbsp;<br/>";'
		);
	}

	function getJSLoadTree($treeItems){
		$js = 'var attribs=new Array();';
		foreach($treeItems as $item){
			$js.='if(' . $this->topFrame . ".indexOfEntry('" . $item["id"] . "')<0){";
			foreach($item as $k => $v){
				$js.='attribs["' . strtolower($k) . '"]=\'' . addslashes($v) . '\';';
			}
			$js.=$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));
			}';
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSInfo(){
		return 'function info(text){}';
	}

	function setItemsCount($count){
		$this->default_segment = $count;
	}

	static function deleteTreeEntries($dontDeleteClassFolders = false){
		return '
var obj = top.treeData;
var cont = new top.container();
for(var i=1;i<=obj.len;i++){
	if(obj[i].checked!=1 ' . ($dontDeleteClassFolders ? ' || obj[i].parentid==0' : '') . '){
		if(obj[i].parentid != 0){
			if(!parentChecked(obj[i].parentid)){
				cont.add(obj[i]);
			}
		}else{
			cont.add(obj[i]);
		}
	}
}
top.treeData = cont;
top.drawTree();

function parentChecked(start){
	var obj = top.treeData;
	for(var i=1;i<=obj.len;i++){
		if(obj[i].id == start){
			if(obj[i].checked==1) return true;
			else if(obj[i].parentid != 0) parentChecked(obj[i].parentid);
		}
	}

	return false;
}';
	}

}

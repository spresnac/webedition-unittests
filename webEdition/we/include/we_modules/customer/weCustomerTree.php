<?php

/**
 * webEdition CMS
 *
 * $Rev: 5604 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 00:09:40 +0100 (Mon, 21 Jan 2013) $
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
class weCustomerTree extends weTree{

	function __construct($frameset = "", $topFrame = "", $treeFrame = "", $cmdFrame = ""){
		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);

		$this->setStyles(array(
			'.item {color: black; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . ';}',
			'.item a { text-decoration:none;}',
			'.group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . ';}',
			'.group a { text-decoration:none;}',
		));
	}

	function getJSCustomDraw(){
		$out = parent::getJSCustomDraw();

		$out["sort"] = '
var newAst = zweigEintrag;

var zusatz = (ai == nf.laenge) ? "end" : "";
var oc_img;
var oc_js;

oc_img="' . $this->tree_image_dir . '"+(nf[ai].open == 0?"auf":"zu")+zusatz+".gif";

oc_js=treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"";


row+="&nbsp;&nbsp;<a href=\"javascript:"+oc_js+" border=0><img src="+oc_img+" width=19 height=18 align=absmiddle border=0 Alt=\"\"></a>";


row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript://\" onClick=\""+oc_js+";return true;\" border=0>";
row+="<img src=' . $this->tree_image_dir . 'icons/"+nf[ai].icon+" width=16 height=18 align=absmiddle border=0 Alt=\"\">";
row+="</a>";

row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript://\" onClick=\""+oc_js+";return true;\">";
row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\" class=\""+treeData.node_layout[nf[ai].state]+"\">&nbsp;" + nf[ai].text+"</label>";
row+="</a>";

row+="&nbsp;&nbsp;<br>\n";

if (nf[ai].open){
	newAst = newAst + "<img src=' . $this->tree_image_dir . '"+(ai == nf.laenge?"leer.gif":"strich2.gif")+" width=19 height=18 align=absmiddle border=0>";
	row+=draw(nf[ai].id,newAst);
}';

		$out["group"] = '
var newAst = zweigEintrag;

var zusatz = (ai == nf.len) ? "end" : "";
var oc_img;
var oc_js;

oc_img="' . $this->tree_image_dir . '"+(nf[ai].open == 1?"zu":"auf")+zusatz+".gif";

if(nf[ai].disabled!=1) oc_js=treeData.topFrame+".setScrollY();"+treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"";
else oc_js="//";

oc_js=treeData.topFrame+".setScrollY();"+treeData.topFrame+".openClose(\'" + nf[ai].id + "\')\"";

row+="&nbsp;&nbsp;<a href=\"javascript:"+oc_js+" border=0><img src="+oc_img+" width=19 height=18 align=absmiddle border=0 Alt=\"\"></a>";

var folder_icon;
folder_icon="folder"+(nf[ai].open==1 ? "open" : "")+(nf[ai].disabled==1 ? "_disabled" : "")+".gif";

nf[ai].icon=folder_icon;

if(nf[ai].disabled!=1) row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript:"+oc_js+"\">";

row+="<img src=' . $this->tree_image_dir . 'icons/"+nf[ai].icon+" width=16 height=18 align=absmiddle border=0 alt=\"\">";

if(nf[ai].disabled!=1) row+="</a>";


if(nf[ai].disabled!=1) row+="<a name=\'_"+nf[ai].id+"\' href=\"javascript:"+oc_js+"\">";

row+="<label style=\"cursor:pointer\" id=\"lab_"+nf[ai].id+"\" class=\""+nf[ai].getlayout()+"\">&nbsp;" + nf[ai].text+"</label>";

if(nf[ai].disabled!=1) row+="</a>";

row+="&nbsp;&nbsp;<br>\n";

if (nf[ai].open==1){
	newAst = newAst + "<img src=' . $this->tree_image_dir . '"+(ai == nf.len?"leer.gif":"strich2.gif")+" width=19 height=18 align=absmiddle border=0>";
	row+=draw(nf[ai].id,newAst);
}';

		return $out;
	}

	function getJSOpenClose(){
		return '
	function openClose(id){
	var sort="";
	if(id=="") return;
	var eintragsIndex = indexOfEntry(id);
	var openstatus;

	if(treeData[eintragsIndex].typ=="group"){
		sort=' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.value;
	}

	openstatus=(treeData[eintragsIndex].open==0?1:0);

	treeData[eintragsIndex].open=openstatus;

	if(openstatus && treeData[eintragsIndex].loaded!=1){
		id = escape(id);
		sort = escape(sort);
		id = id.replace(/\+/g,"%2B");
		sort = sort.replace(/\+/g,"%2B");
		if(sort!=""){
			' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid="+id+"&sort="+sort;
		}else{
			' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid="+id;
		}
	}else{
		drawTree();
	}
	if(openstatus==1) treeData[eintragsIndex].loaded=1;
}';
	}

	function getJSUpdateItem(){
		return '
function updateEntry(id,text){
			var ai = 1;
			while (ai <= treeData.len) {
					if (treeData[ai].id==id) {
						text = text.replace(/</g,"&lt;");
			text = text.replace(/>/g,"&gt;");
							treeData[ai].text=text;
					}
					ai++;
			}
			drawTree();
}';
	}

	function getJSTreeFunctions(){
		return parent::getJSTreeFunctions() . '
function doClick(id,typ){
	var node=' . $this->topFrame . '.get(id);
		if(node.typ==\'item\')
		' . $this->topFrame . '.we_cmd(\'edit_customer\',node.id,node.typ,node.table);
}
' . $this->topFrame . '.loaded=1;';
	}

	function getJSStartTree(){
		return '
function startTree(){
	' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid=0";
	drawTree();
}';
	}

	function getJSIncludeFunctions(){
		return parent::getJSIncludeFunctions() .
			$this->getJSStartTree();
	}

	function getJSLoadTree($treeItems){
		$days = array(
			"Sunday" => 0,
			"Monday" => 1,
			"Tuesday" => 2,
			"Wednesday" => 3,
			"Thursday" => 4,
			"Friday" => 5,
			"Saturday" => 6
		);

		$months = array(
			"January" => 0,
			"February" => 1,
			"March" => 2,
			"April" => 3,
			"May" => 4,
			"June" => 5,
			"July" => 6,
			"August" => 7,
			"September" => 8,
			"October" => 9,
			"November" => 10,
			"December" => 11
		);

		$js = 'var attribs=new Array();';
		foreach($treeItems as $item){
			$js.='if(' . $this->topFrame . '.indexOfEntry(\'' . str_replace(array("\n","\r",'\''), '', $item["id"]) . '\')<0){';
			foreach($item as $k => $v){
				if($k == 'text'){
					if(in_array($v, array_keys($days))){
						$v = g_l('date', '[day][long][' . $days[$v] . ']');
					}
					if(in_array($v, array_keys($months))){
						$v = g_l('date', '[month][long][' . $months[$v] . ']');
					}
				}
				$js.='attribs["' . strtolower($k) . '"]=\'' . addslashes(stripslashes(str_replace(array("\n","\r",'\''), '', $v))) . '\';';
			}
			$js.=$this->topFrame . '.treeData.add(new ' . $this->topFrame . '.node(attribs));
				}';
		}
		$js.=$this->topFrame . '.drawTree();';

		return $js;
	}

	function getJSShowSegment(){
		return '
function showSegment(){
	var sort="";
	parentnode=' . $this->topFrame . '.get(this.parentid);
	parentnode.clear();
	sort=' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.value;
	we_cmd("load",parentnode.id,this.offset,sort);
}';
	}

}
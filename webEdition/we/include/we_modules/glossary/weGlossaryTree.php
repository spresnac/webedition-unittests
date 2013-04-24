<?php

/**
 * webEdition CMS
 *
 * $Rev: 5000 $
 * $Author: mokraemer $
 * $Date: 2012-10-18 23:35:06 +0200 (Thu, 18 Oct 2012) $
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
class weGlossaryTree extends weMainTree{

	function __construct($frameset = "", $topFrame = "", $treeFrame = "", $cmdFrame = ""){

		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);

		$styles = array();
		$styles[] = '.item {color: black; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . ';}';
		$styles[] = '.item a { text-decoration:none;}';

		$styles[] = '.group {color: black; font-weight: bold; font-size: ' . (((we_base_browserDetect::isUNIX()) ? "11px" : "9px")) . '; font-family: ' . g_l('css', '[font_family]') . ';}';
		$styles[] = '.group a { text-decoration:none;}';

		$this->setStyles($styles);
	}

	function getJSOpenClose(){

		return '
			function openClose(id){
			var sort="";
			if(id=="") return;
			var eintragsIndex = indexOfEntry(id);
			var openstatus;


			if(treeData[eintragsIndex].open==0) openstatus=1;
			else openstatus=0;

			treeData[eintragsIndex].open=openstatus;

			if(openstatus && treeData[eintragsIndex].loaded!=1){
				if(sort!="")
					' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid="+id+"&sort="+sort;
				else
					' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid="+id;
			}else{
				drawTree();
			}
			if(openstatus==1) treeData[eintragsIndex].loaded=1;
			}';
	}

	function getJSUpdateItem(){

		return '
				function updateEntry(id,text,pid,pub){
    			var ai = 1;
    			while (ai <= treeData.len) {
        			if (treeData[ai].id==id) {
             			treeData[ai].text=text;
             			treeData[ai].parentid=pid;
             			treeData[ai].published=pub;
         			}
        	 		ai++;
    			}
				drawTree();
				}
		';
	}

	function getJSTreeFunctions(){
		return weTree::getJSTreeFunctions() . '
			function doClick(id,typ){
					var cmd = "";
					if(top.content.hot == "1") {
						if(confirm("' . g_l('modules_glossary', "[save_changed_glossary]") . '")) {
							cmd = "save_export";
							top.content.we_cmd("save_glossary");
						} else {
							top.content.usetHot();
							var node=' . $this->topFrame . '.get(id);
							' . $this->topFrame . '.resize.right.editor.edbody.location="' . $this->frameset . '?pnt=edbody&cmd=" + node.cmd + "&cmdid="+node.id+"&tabnr="+' . $this->topFrame . '.activ_tab;
						}
					} else {
						var node=' . $this->topFrame . '.get(id);
						' . $this->topFrame . '.resize.right.editor.edbody.location="' . $this->frameset . '?pnt=edbody&cmd=" + node.cmd + "&cmdid="+node.id+"&tabnr="+' . $this->topFrame . '.activ_tab;
					}
			}' . $this->topFrame . '.loaded=1;';
	}

	function getJSStartTree(){

		return 'function startTree(){
			' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid=0";
			drawTree();
		}';
	}

	function getJSIncludeFunctions(){
		return weTree::getJSIncludeFunctions() .
			$this->getJSStartTree();
	}

	function getJSMakeNewEntry(){

		return '
		function makeNewEntry(icon,id,pid,txt,open,ct,tab,pub){
				if(treeData[indexOfEntry(pid)]){
					if(treeData[indexOfEntry(pid)].loaded){

 						if(ct=="folder") ct="group";
 						else ct="item";

						var attribs=new Array();

						attribs["id"]=id;
						attribs["icon"]=icon;
						attribs["text"]=txt;
						attribs["parentid"]=pid;
						attribs["open"]=open;

 						attribs["tooltip"]=id;
 						attribs["typ"]=ct;


						attribs["disabled"]=0;
						if(ct=="item") attribs["published"]=pub;
						else attribs["published"]=1;

						attribs["selected"]=0;

						treeData.addSort(new node(attribs));

						drawTree();
					}
				}
		}
		';
	}

	function getJSInfo(){

		return '
			function info(text) {
			}';
	}

	function getJSShowSegment(){

		return '
				function showSegment(){
				parentnode=' . $this->topFrame . '.get(this.parentid);
				parentnode.clear();
				' . $this->cmdFrame . '.location="' . $this->frameset . '?pnt=cmd&pid="+this.parentid+"&offset="+this.offset;
				drawTree();
			}';
	}

}
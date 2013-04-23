<?php

/**
 * webEdition CMS
 *
 * $Rev: 3750 $
 * $Author: mokraemer $
 * $Date: 2012-01-07 02:14:44 +0100 (Sat, 07 Jan 2012) $
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
class weNavigationTree extends weToolTree{

	function __construct($frameset = '', $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		parent::__construct($frameset, $topFrame, $treeFrame, $cmdFrame);
	}

	function getJSTreeFunctions(){

		$out = weTree::getJSTreeFunctions();

		$out .= '
				function doClick(id,typ){
					var node=' . $this->topFrame . '.get(id);
					' . $this->topFrame . '.resize.right.editor.edbody.we_cmd("tool_navigation_edit",node.id);
				}
				' . $this->topFrame . '.loaded=1;
			';
		return $out;
	}

	function getJSTreeCode(){
		return parent::getJSTreeCode() . we_html_element::jsElement(
				'
 					drawTree.selection_table="' . NAVIGATION_TABLE . '";
 				');
	}

}
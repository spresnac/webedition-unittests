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
function we_tag_path($attribs){
	$db = $GLOBALS['DB_WE'];
	$field = weTag_getAttribute("field", $attribs);
	$dirfield = weTag_getAttribute("dirfield", $attribs, $field);
	$index = weTag_getAttribute("index", $attribs);
	$oldHtmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true);
	$fieldforfolder = weTag_getAttribute("fieldforfolder", $attribs, false, true);
	$docAttr = weTag_getAttribute("doc", $attribs);
	$sep = weTag_getAttribute("separator", $attribs, "/");
	$home = weTag_getAttribute("home", $attribs, "home");
	$hidehome = weTag_getAttribute("hidehome", $attribs, false, true);
	$class = weTag_getAttribute("class", $attribs);
	$style = weTag_getAttribute("style", $attribs);

	$doc = we_getDocForTag($docAttr, true);
	$pID = $doc->ParentID;

	$indexArray = $index ? explode(',', $index) : array("index.html", "index.htm", "index.php", "default.htm", "default.html", "default.php");

	$class = $class ? ' class="' . $class . '"' : '';
	$style = $style ? ' style="' . $style . '"' : '';

	$path = '';
	$q = array();
	foreach($indexArray as $v){
		$q[] = ' Text="' . $v . '"';
	}
	$q = implode(' OR ', $q);
	$show = $doc->getElement($field);
	if(!in_array($doc->Text, $indexArray)){
		if(!$show){
			$show = $doc->Text;
		}
		$path = $oldHtmlspecialchars ? oldHtmlspecialchars($sep . $show) : $sep . $show;
	}
	while($pID) {
		$db->query('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ParentID=' . intval($pID) . ' AND IsFolder = 0 AND (' . $q . ') AND (Published > 0 AND IsSearchable = 1)');
		$db->next_record();
		$fileID = $db->f('ID');
		$filePath = $db->f('Path');
		if($fileID){
			$show = f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . LINK_TABLE . '.DID=' . intval($fileID) . ' AND ' . LINK_TABLE . ".Name='" . $db->escape($dirfield) . " ' AND " . CONTENT_TABLE . '.ID=' . LINK_TABLE . '.CID', 'Dat', $db);
			if(!$show && $fieldforfolder){
				$show = f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . LINK_TABLE . '.DID=' . intval($fileID) . ' AND ' . LINK_TABLE . ".Name='" . $db->escape($field) . " ' AND " . CONTENT_TABLE . ".ID=" . LINK_TABLE . '.CID', 'Dat', $db);
			}
			if(!$show){
				$show = f('SELECT Text FROM ' . FILE_TABLE . ' WHERE ID=' . intval($pID), 'Text', $db);
			}
			if($fileID != $doc->ID){
				$link_pre = '<a href="' . $filePath . '"' . $class . $style . '>';
				$link_post = '</a>';
			} else{
				$link_pre = $link_post = '';
			}
		} else{
			$link_pre = $link_post = '';
			$show = f('SELECT Text FROM ' . FILE_TABLE . ' WHERE ID=' . intval($pID), "Text", $db);
		}
		$pID = f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . intval($pID), "ParentID", $db);
		$path = (!$pID && $hidehome ? '' : $sep) . $link_pre . ($oldHtmlspecialchars ? oldHtmlspecialchars($show) : $show) . $link_post . $path;
	}

	if($hidehome){
		return $path;
	}

	list($fileID, $filePath) = getHash('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ParentID=0 AND IsFolder=0 AND (' . $q . ') AND (Published>0 AND IsSearchable=1)', $db);
	if($fileID){
		$show = f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . LINK_TABLE . '.DID=' . intval($fileID) . ' AND ' . LINK_TABLE . '.Name="' . $db->escape($field) . '" AND ' . CONTENT_TABLE . '.ID = ' . LINK_TABLE . '.CID', 'Dat', $db);
		if(!$show){
			$show = $home;
		}
		$link_pre = '<a href="' . $filePath . '"' . $class . $style . '>';
		$link_post = '</a>';
	} else{
		$link_pre = $link_post = '';
		$show = $home;
	}
	return $link_pre . ($oldHtmlspecialchars ? oldHtmlspecialchars($show) : $show) . $link_post . $path;
}

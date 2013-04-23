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
function we_tag_listdir($attribs, $content){
	$dirID = weTag_getAttribute('id', $attribs, $GLOBALS['we_doc']->ParentID);
	$index = explode(',', weTag_getAttribute('index', $attribs, 'index.html,index.htm,index.php,default.htm,default.html,default.php'));
	$name = weTag_getAttribute('field', $attribs);
	$dirfield = weTag_getAttribute('dirfield', $attribs, $name);
	$sort = weTag_getAttribute('order', $attribs, $name);
	$desc = weTag_getAttribute('desc', $attribs, false, true);

	$q = array();
	foreach($index as $i => $v){
		$q[] = " Text='$v'";
	}
	$q = implode(' OR ', $q);

	$files = array();

	$db = new DB_WE();
	$db2 = new DB_WE();

	$db->query("SELECT ID,Text,IsFolder,Path FROM " . FILE_TABLE . " WHERE ((Published > 0 AND IsSearchable = 1) OR (IsFolder = 1)) AND ParentID=" . intval($dirID));

	while($db->next_record()) {
		$sortfield = $namefield = '';

		if($db->f("IsFolder")){
			$id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE ParentID=' . intval($db->f("ID")) . ' AND IsFolder = 0 AND (' . $q . ') AND (Published > 0 AND IsSearchable = 1)', 'ID', $db2);
			if($id){
				if($sort){
					$dat = f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . $id
						. "' AND " . LINK_TABLE . ".Name='" . $db->escape($sort) . "' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID", 'Dat', $db2);
					$sortfield = $dat ? $dat : $db->f("Text");
				} else{
					$sortfield = $db->f("Text");
				}
				if($dirfield){
					$dat = f('SELECT ' . CONTENT_TABLE . '.Dat as Dat FROM ' . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID='" . $id
						. "' AND " . LINK_TABLE . ".Name='" . $db->escape($dirfield) . "' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID", 'Dat', $db2);
					$namefield = $dat ? $dat : $db->f("Text");
				} else{
					$namefield = $db->f("Text");
				}

				$files[] = array("properties" => $db->Record, "sort" => $sortfield, "name" => $namefield);
			}
		} else{
			if($sort){
				$dat = f('SELECT ' . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID=" . intval($db->f(
							"ID")) . " AND " . LINK_TABLE . ".Name='" . $db2->escape($sort) . "' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID", 'Dat', $db2);
				$sortfield = $dat ? $dat : $db->f("Text");
			} else{
				$sortfield = $db->f("Text");
			}
			if($name){
				$dat = f("SELECT " . CONTENT_TABLE . ".Dat as Dat FROM " . LINK_TABLE . "," . CONTENT_TABLE . " WHERE " . LINK_TABLE . ".DID=" . intval($db->f(
							"ID")) . " AND " . LINK_TABLE . ".Name='" . $db2->escape($name) . "' AND " . CONTENT_TABLE . ".ID = " . LINK_TABLE . ".CID", 'Dat', $db2);
				$namefield = $dat ? $dat : $db->f("Text");
			} else{
				$namefield = $db->f("Text");
			}
			array_push($files, array("properties" => $db->Record, "sort" => $sortfield, "name" => $namefield));
		}
	}

	if($sort){
		usort($files, ($desc ? 'we_cmpFieldDesc' : 'we_cmpField'));
	} else{
		usort($files, ($desc ? 'we_cmpTextDesc' : 'we_cmpText'));
	}
	$out = '';

	foreach($files as $i => $v){

		$field = $v["name"];
		$id = $v["properties"]["ID"];
		$path = $v["properties"]["Path"];
		$foo = preg_replace(array(
			'|we_tag\(\'field\',array\(\.*\)\)|s',
			'|we_tag\(\'id\',array\(\.*\)\)|s',
			'|(we_tag\(\'a\',array\()(\.*\).*\))|s',
			'|(we_tag\(\'ifSelf\',array\()(\.*\).*\))|s',
			'|(we_tag\(\'ifNotSelf\',array\()(\.*\).*\))|s',
			), array(
			'\'' . $field . '\'',
			'\'' . $id . '\'',
			'\'' . $path . '\'',
			'\1id=>' . $id . ',\2',
			'\1id=>' . $id . ',\2',
			'\1id=>' . $id . ',\2',
			), $content);

		//	parse we:ifPosition
		if(strpos($foo, 'setVar') || strpos($foo, 'position') || strpos($foo, 'ifPosition') || strpos(
				$foo, 'ifNotPosition')){
			$foo = '<?php $GLOBALS[\'we_position\'][\'listdir\'] = array(\'position\' => ' . ($i + 1) . ', \'size\' => ' . count(
					$files) . ', \'field\' => \'' . $field . '\', \'id\' => \'' . $id . '\', \'path\' => \'' . $path . '\'); ?>' . $foo . '<?php unset($GLOBALS[\'we_position\'][\'listdir\']); ?>';
		}

		$out .= $foo;
	}
	return $out;
}

function we_cmpText($a, $b){
	$x = strtolower(correctUml($a['properties']['Text']));
	$y = strtolower(correctUml($b['properties']['Text']));
	if($x == $y){
		return 0;
	}
	return ($x < $y) ? -1 : 1;
}

function we_cmpTextDesc($a, $b){
	$x = strtolower(correctUml($a['properties']['Text']));
	$y = strtolower(correctUml($b['properties']['Text']));
	if($x == $y){
		return 0;
	}
	return ($x > $y) ? -1 : 1;
}

function we_cmpField($a, $b){
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if($x == $y){
		return 0;
	}
	return ($x < $y) ? -1 : 1;
}

function we_cmpFieldDesc($a, $b){
	$x = strtolower(correctUml($a['sort']));
	$y = strtolower(correctUml($b['sort']));
	if($x == $y){
		return 0;
	}
	return ($x > $y) ? -1 : 1;
}
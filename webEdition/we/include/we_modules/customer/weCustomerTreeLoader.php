<?php

/**
 * webEdition CMS
 *
 * $Rev: 5639 $
 * $Author: mokraemer $
 * $Date: 2013-01-24 19:36:21 +0100 (Thu, 24 Jan 2013) $
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
class weCustomerTreeLoader{

	function getItems($pid, $offset = 0, $segment = 500, $sort = ""){
		return ($sort != "" ?
				weCustomerTreeLoader::getSortFromDB($pid, $sort, $offset, $segment) :
				weCustomerTreeLoader::getItemsFromDB($pid, $offset, $segment));
	}

	function getItemsFromDB($ParentID = 0, $offset = 0, $segment = 500, $elem = "ID,ParentID,Path,Text,Icon,IsFolder,Forename,Surname", $addWhere = "", $addOrderBy = ""){
		$db = new DB_WE();
		$table = CUSTOMER_TABLE;

		$items = array();

		$prevoffset = $offset - $segment;
		$prevoffset = ($prevoffset < 0) ? 0 : $prevoffset;
		if($offset && $segment){
			$items[] = array(
				"icon" => "arrowup.gif",
				"id" => "prev_" . $ParentID,
				"parentid" => $ParentID,
				"text" => "display (" . $prevoffset . "-" . $offset . ")",
				"contenttype" => "arrowup",
				"table" => CUSTOMER_TABLE,
				"typ" => "threedots",
				"open" => 0,
				"published" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $prevoffset
			);
		}

		$settings = new weCustomerSettings();
		$settings->load();


		$where = ' WHERE ParentID=' . intval($ParentID) . " " . $addWhere;

		$_formatFields = implode(',', $settings->formatFields);
		if($_formatFields != ''){
			$_formatFields.=',';
		}

		$_order = '';
		if($settings->getSettings('default_order') != ''){

			$_order = ($_formatFields != '' ?
					implode(' ' . $settings->getSettings('default_order') . ',', $settings->formatFields) . ' ' . $settings->getSettings('default_order') :
					'Text ' . $settings->getSettings('default_order'));
		}

		$db->query("SELECT $_formatFields $elem FROM $table $where " . (!empty($_order) ? "ORDER BY $_order" : '') . ($segment ? " LIMIT $offset,$segment;" : ";" ));

		while($db->next_record()) {

			$typ = array("typ" => ($db->f("IsFolder") == 1 ? "group" : "item"));

			$typ["disabled"] = 0;
			$typ["tooltip"] = $db->f("ID");
			$typ["offset"] = $offset;

			$ttrow = $db->Record;
			eval('$tt = "' . $settings->treeTextFormat . '";');

			$fileds = array();

			foreach($db->Record as $k => $v){
				if(!is_numeric($k))
					$fileds[strtolower($k)] = $v;
			}

			$fileds["text"] = oldHtmlspecialchars(trim($tt) != "" ? $tt : $db->f("Text"));
			$items[] = array_merge($fileds, $typ);
		}

		$total = f('SELECT COUNT(1) as total FROM ' . $table . ' ' . $where, 'total', $db);
		$nextoffset = $offset + $segment;
		if($segment && ($total > $nextoffset)){
			$items[] = array(
				"icon" => "arrowdown.gif",
				"id" => "next_" . $ParentID,
				"parentid" => 0,
				"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
				"contenttype" => "arrowdown",
				"table" => CUSTOMER_TABLE,
				"typ" => "threedots",
				"open" => 0,
				"disabled" => 0,
				"tooltip" => "",
				"offset" => $nextoffset
			);
		}

		return $items;
	}

	function getSortFromDB($pid, $sort, $offset = 0, $segment = 500){
		$db = new DB_WE();
		$table = CUSTOMER_TABLE;

		$fieldarr = array();

		$havingarr = array();
		$sort_defs = array();
		$pidarr = array();
		$check = array();
		$level = 0;

		$notroot = (preg_match('|\{.\}|', $pid)) ? true : false;

		$pid = str_replace(array('{', '}', '*****quot*****'), array('', '', "\\\\\'"), $pid);

		if($pid || $notroot){
			$pidarr = explode("-|-", $pid);
			$tmp = "";
		}

		$settings = new weCustomerSettings();
		$settings->load(false);

		if(isset($settings->SortView[$sort])){
			$sort_defs = $settings->SortView[$sort];
		}

		$c = 0;
		$select = array();
		$grouparr = array();
		$orderarr = array();

		foreach($sort_defs as $sortdef){
			if(isset($sortdef["function"]) && $sortdef["function"]){
				$select[] = ($settings->customer->isInfoDate($sortdef["field"]) ?
						sprintf($settings->FunctionTable[$sortdef["function"]], "FROM_UNIXTIME(" . $sortdef["field"] . ")") . " AS " . $sortdef["field"] . "_" . $sortdef["function"] :
						sprintf($settings->FunctionTable[$sortdef["function"]], $sortdef["field"]) . " AS " . $sortdef["field"] . "_" . $sortdef["function"]);

				$grouparr[] = $sortdef["field"] . '_' . $sortdef["function"];
				$orderarr[] = $sortdef["field"] . '_' . $sortdef["function"] . " " . $sortdef["order"];
				$orderarr[] = $sortdef["field"] . ' ' . $sortdef["order"];
				if(isset($pidarr[$c])){
					$havingarr[] = ($pidarr[$c] == g_l('modules_customer', '[no_value]') ?
							"(" . $sortdef["field"] . "_" . $sortdef["function"] . "='' OR " . $sortdef["field"] . "_" . $sortdef["function"] . " IS NULL)" :
							$sortdef["field"] . "_" . $sortdef["function"] . "='" . $pidarr[$c] . "'");
				}
			} else{
				$select[] = $sortdef["field"];
				$grouparr[] = $sortdef["field"];
				$orderarr[] = $sortdef["field"] . " " . $sortdef["order"];
				if(isset($pidarr[$c]) && $pidarr[$c])
					if($pidarr[$c] == g_l('modules_customer', '[no_value]'))
						$havingarr[] = "(" . $sortdef["field"] . "='' OR " . $sortdef["field"] . " IS NULL)";
					else
						$havingarr[] = $sortdef["field"] . "='" . $pidarr[$c] . "'";
			}
			$c++;
		}

		$level = count($pidarr);
		$levelcount = count($grouparr);


		$grp = isset($grouparr[0]) ? $grouparr[0] : null;

		if($level != 0){
			for($i = 1; $i < $level; $i++){
				$grp.="," . $grouparr[$i];
			}
		}

		$_formatFields = implode(',', $settings->formatFields);
		if($_formatFields != ''){
			$_formatFields.=',';
		}

		$items = array();

		if($settings->getSettings('default_order') != ''){
			$_order = ($_formatFields != '' ?
					implode(' ' . $settings->getSettings('default_order') . ',', $settings->formatFields) . ' ' . $settings->getSettings('default_order') :
					'Text ' . $settings->getSettings('default_order'));
		} else{
			$_order = '';
		}

		$db->query("SELECT $_formatFields ID,ParentID,Path,Text,Icon,IsFolder,Forename,Surname" . (count($select) ? "," . implode(",", $select) : "") . " FROM " . $table . " GROUP BY " . $grp . (count($grouparr) ? ($level != 0 ? ",ID" : "") : "ID") . (count($havingarr) ? " HAVING " . implode(" AND ", $havingarr) : "") . " ORDER BY " . implode(",", $orderarr) . (!empty($_order) ? (',' . $_order) : '' ) . (($level == $levelcount && $segment) ? " LIMIT $offset,$segment" : ''));

		$sortarr = array();
		$foo = array();
		$gname = "";
		$old = "0";
		$first = true;

		while($db->next_record()) {

			$old = 0;

			if($level == 0){
				$gname = $db->f($grouparr[0]) != "" ? $db->f($grouparr[0]) : g_l('modules_customer', '[no_value]');
				$gid = "{" . $gname . "}";

				$items[] = array(
					"id" => str_replace("\'", "*****quot*****", $gid),
					"parentid" => $old,
					"path" => "",
					"text" => $gname,
					"icon" => we_base_ContentTypes::FOLDER_ICON,
					"isfolder" => 1,
					"typ" => "group",
					"disabled" => "0",
					"open" => "0"
				);
				$check[$gname] = 1;
			} else{
				$foo = array();
				for($i = 0; $i < $levelcount; $i++){
					$foo[] = ($i == 0 ?
							("{" . ($db->f($grouparr[$i]) != "" ? $db->f($grouparr[$i]) : g_l('modules_customer', '[no_value]')) . "}") :
							($db->f($grouparr[$i]) != "" ? $db->f($grouparr[$i]) : g_l('modules_customer', '[no_value]')));
					$gname = implode("-|-", $foo);
					if($i >= $level){
						if(!isset($check[$gname])){
							$items[] = array(
								"id" => $gname,
								"parentid" => $old,
								"path" => "", "text" => ($db->f($grouparr[$i]) != "" ? $db->f($grouparr[$i]) : g_l('modules_customer', '[no_value]')),
								"icon" => we_base_ContentTypes::FOLDER_ICON,
								"isfolder" => 1,
								"typ" => "group",
								"disabled" => "0",
								"open" => "0"
							);
							$check[$gname] = 1;
						}
					}
					$old = $gname;
				}
				$gname = implode("-|-", $foo);
				if($level == $levelcount){
					$tt = "";
					$ttrow = $db->Record;
					eval('$tt = "' . $settings->treeTextFormat . '";');

					if($first){
						$prevoffset = $offset - $segment;
						$prevoffset = ($prevoffset < 0) ? 0 : $prevoffset;
						if($offset && $segment){
							$items[] = array(
								"icon" => "arrowup.gif",
								"id" => "prev_" . $gname,
								"parentid" => $gname,
								"text" => "display (" . $prevoffset . "-" . $offset . ")",
								"contenttype" => "arrowup",
								"table" => CUSTOMER_TABLE,
								"typ" => "threedots",
								"open" => 0,
								"published" => 0,
								"disabled" => 0,
								"tooltip" => "",
								"offset" => $prevoffset
							);
						}
						$first = false;
					}
					$items[] = array(
						"id" => $db->f("ID"),
						"parentid" => str_replace("\'", "*****quot*****", $gname),
						"path" => "",
						"text" => oldHtmlspecialchars(trim($tt) != "" ? $tt : $db->f("Text")),
						"icon" => $db->f("Icon"),
						"isfolder" => $db->f("IsFolder"),
						"typ" => "item",
						"disabled" => "0",
						"tooltip" => $db->f("ID")
					);
				}
			}
		}

		if($level == $levelcount){
			$total = f("SELECT COUNT(ID) as total " . (count($select) ? "," . implode(",", $select) : "") . " FROM " . $db->escape($table) . " GROUP BY " . $grp . (count($grouparr) ? ($level != 0 ? ",ID" : "") : "ID") . (count($havingarr) ? " HAVING " . implode(" AND ", $havingarr) : "") . " ORDER BY " . implode(",", $orderarr), 'total', $db);

			$nextoffset = $offset + $segment;
			if($segment && ($total > $nextoffset)){
				$items[] = array(
					"icon" => "arrowdown.gif",
					"id" => "next_" . str_replace("\'", "*****quot*****", $old),
					"parentid" => str_replace("\'", "*****quot*****", $old),
					"text" => "display (" . $nextoffset . "-" . ($nextoffset + $segment) . ")",
					"contenttype" => "arrowdown",
					"table" => CUSTOMER_TABLE,
					"typ" => "threedots",
					"open" => 0,
					"disabled" => 0,
					"tooltip" => "",
					"offset" => $nextoffset
				);
			}
		}

		return $items;
	}

}

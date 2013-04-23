<?php

/**
 * webEdition CMS
 *
 * $Rev: 5927 $
 * $Author: lukasimhof $
 * $Date: 2013-03-08 16:14:51 +0100 (Fri, 08 Mar 2013) $
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

/**
 * class    we_listview_multiobject
 * @desc    class for tag <we:listview type="multiobject">
 *
 */
//FIXME: is this class not ~ listview_object? why is this not the base class???
class we_listview_multiobject extends listviewBase{

	var $classID = ""; /* ID of a class */
	var $objects = ""; /* Comma sepearated list of all objetcs to show in this listview */
	var $triggerID = 0; /* ID of a document which to use for displaying thr detail page */
	var $condition = ""; /* condition string (like SQL) */
	var $ClassName = __CLASS__;
	var $Path = ""; /* internal: Path of document which to use for displaying thr detail page */
	var $IDs = array();
	var $searchable = true;
	var $languages = ''; //string of Languages, separated by ,
	var $objectseourls = false;
	var $hidedirindex = false;

	/**
	 * @desc    constructor of class
	 *
	 * @param   name          string - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   cats          string - comma separated categories
	 * @param   catOr         boolean - set to true if it should be an "OR condition"
	 * @param   condition     string - condition string (like SQL)
	 * @param   triggerID     integer - ID of a document which to use for displaying the detail page
	 * @param   cols          ??
	 * @param   seeMode       boolean - value if objects shall be accessible in seeMode (default true)
	 * @param   searchable 	  boolean - if false then show also documents which are not marked as searchable
	 * @param	unknown_type  $calendar
	 * @param	unknown_type  $datefield
	 * @param	unknown_type  $date
	 * @param	unknown_type  $weekstart
	 * @param	string        $categoryids
	 *
	 */
	function __construct($name = "0", $rows = 9999999, $offset = 0, $order = "", $desc = false, $cats = "", $catOr = "", $condition = "", $triggerID = "", $cols = "", $seeMode = true, $searchable = true, $calendar = "", $datefield = "", $date = "", $weekstart = "", $categoryids = '', $customerFilterType = 'off', $docID = 0, $languages = '', $hidedirindex = false, $objectseourls = false){

		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, 0, $cols, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType);

		$data = 0;
		if(isset($GLOBALS['we_lv_array']) && count($GLOBALS['we_lv_array']) > 1){
			$parent_lv = $GLOBALS['we_lv_array'][(count($GLOBALS['we_lv_array']) - 1)];
			if(isset($parent_lv->DB_WE->Record['we_' . $name]) && $parent_lv->DB_WE->Record['we_' . $name]){
				$data = unserialize($parent_lv->DB_WE->Record['we_' . $name]);
			}
		} elseif(isset($GLOBALS["lv"])){
			if(isset($GLOBALS["lv"]->object)){
				if(isset($GLOBALS['lv']->object->DB_WE->Record['we_' . $name]) && $GLOBALS['lv']->object->DB_WE->Record['we_' . $name]){
					$data = unserialize($GLOBALS['lv']->object->DB_WE->Record['we_' . $name]);
				}
			} else{
				if($GLOBALS["lv"]->ClassName == 'we_listview_shoppingCart'){
					if(isset($GLOBALS['lv']->Record[$name]) && $GLOBALS['lv']->Record[$name]){
						$data = unserialize($GLOBALS['lv']->Record[$name]);
					}
				} else{
					if(isset($GLOBALS['lv']->DB_WE->Record['we_' . $name]) && $GLOBALS['lv']->DB_WE->Record['we_' . $name]){
						$data = unserialize($GLOBALS['lv']->DB_WE->Record['we_' . $name]);
					}
				}
			}
		} else{
			if($GLOBALS['we_doc']->getElement($name)){
				$data = unserialize($GLOBALS['we_doc']->getElement($name));
			}
		}

		if(!$data){
			return;
		}
		// remove not set values
		$temp = $data['objects'];
		$empty = array_keys($temp, "");
		$objects = array();
		foreach($temp as $key => $val){
			if(!in_array($key, $empty)){
				$objects[] = $val;
			}
		}
		if(empty($objects)){
			return;
		}
		$this->objects = $objects;

		$this->classID = $data['class'];
		$this->triggerID = $triggerID;
		$this->condition = $condition;
		$this->searchable = $searchable;
		$this->docID = $docID; //Bug #3720

		$this->condition = $this->condition ? $this->condition : (isset($GLOBALS["we_lv_condition"]) ? $GLOBALS["we_lv_condition"] : '');
		$this->languages = $languages ? $languages : (isset($GLOBALS["we_lv_languages"]) ? $GLOBALS["we_lv_languages"] : '');
		$this->objectseourls = $objectseourls;
		$this->hidedirindex = $hidedirindex;

		$_obxTable = OBJECT_X_TABLE . $this->classID;

		$where_lang = '';

		if($this->languages != ''){
			$where_lang = array();
			$langArray = makeArrayFromCSV($this->languages);
			foreach($langArray as $lang){
				$where_lang [] = $_obxTable . '.OF_Language = "' . $lang . '"';
			}
			$where_lang = ' AND (' . implode(' OR ', $where_lang) . ' ) ';
		}

		if($this->desc && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		if($this->triggerID && show_SeoLinks()){
			$this->Path = id_to_path($this->triggerID, FILE_TABLE, $this->DB_WE);
		} else{
			$this->Path = (isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '');
		}

		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = (isset($_SESSION['weS']['last_webEdition_document']) ? $_SESSION['weS']['last_webEdition_document']['Path'] : '');

		$matrix = array();
		$join = $this->fillMatrix($matrix, $this->classID, $this->DB_WE);

		$calendar_select = '';
		$calendar_where = '';

		if($calendar != ''){
			$this->fetchCalendar($this->condition, $calendar_select, $calendar_where, $matrix);
		}
		$sqlParts = $this->makeSQLParts($matrix, $this->classID, $this->order, $this->condition);

		$pid_tail = 1;

		$cat_tail = ($this->cats || $this->categoryids ? we_category::getCatSQLTail($this->cats, $_obxTable, $this->catOr, $this->DB_WE, "OF_Category", true, $this->categoryids) : '');

		$weDocumentCustomerFilter_tail = ($this->customerFilterType != 'off' && defined("CUSTOMER_FILTER_TABLE") ?
				weDocumentCustomerFilter::getConditionForListviewQuery($this) :
				'');

		if($sqlParts["tables"]){
			$this->DB_WE->query('SELECT ' . $_obxTable . '.ID as ID ' . $calendar_select . ' FROM ' . $sqlParts['tables'] . ' WHERE ' . (!empty($this->objects) ? OBJECT_X_TABLE . $this->classID . ".OF_ID IN (" . implode(",", $this->objects) . ") AND " : '') . ($this->searchable ? " " . OBJECT_X_TABLE . $this->classID . ".OF_IsSearchable=1 AND" : "") . " " . $pid_tail . $where_lang . " AND " . OBJECT_X_TABLE . $this->classID . ".OF_ID != 0 " . ($join ? " AND ($join) " : "") . $cat_tail . " " . ($sqlParts["publ_cond"] ? (" AND " . $sqlParts["publ_cond"]) : "") . " " . ($sqlParts["cond"] ? (" AND (" . $sqlParts["cond"] . ") ") : "") . $calendar_where . $weDocumentCustomerFilter_tail . $sqlParts['groupBy']);
			$mapping = array(); // KEY = ID -> VALUE = ROWID
			$i = 0;
			while($this->DB_WE->next_record()) {
				$mapping[$this->DB_WE->Record["ID"]] = $i;
				$i++;
				$this->IDs[] = $this->DB_WE->f("ID");
				if($calendar != ""){
					$this->calendar_struct["storage"][$this->DB_WE->f("ID")] = (int) $this->DB_WE->f("Calendar");
				}
			}

			if($this->order == ''){
				$this->anz_all = count($this->objects);
			} else{
				$this->anz_all = 0;
				$count = array_count_values($this->objects);
				foreach($mapping as $objid => $rowid){
					if(isset($count[$objid])){
						for($i = 0; $i < $count[$objid]; $i++){
							$this->anz_all++;
						}
					}
				}
			}

			$this->DB_WE->query('SELECT ' . $sqlParts["fields"] . $calendar_select . ' FROM ' . $sqlParts["tables"] . ' WHERE  ' . (!empty($this->objects) ? OBJECT_X_TABLE . $this->classID . ".OF_ID IN (" . implode(",", $this->objects) . ") AND " : '') . ($this->searchable ? " " . OBJECT_X_TABLE . $this->classID . ".OF_IsSearchable=1 AND" : "") . " " . $pid_tail . $where_lang . " AND " . OBJECT_X_TABLE . $this->classID . ".OF_ID != 0 " . ($join ? " AND ($join) " : "") . $cat_tail . $weDocumentCustomerFilter_tail . " " . ($sqlParts["publ_cond"] ? (" AND " . $sqlParts["publ_cond"]) : "") . " " . ($sqlParts["cond"] ? (" AND (" . $sqlParts["cond"] . ") ") : "") . $calendar_where . $sqlParts['groupBy'] . $sqlParts["order"] . (($rows > 0 && $this->order != '') ? (' LIMIT ' . $this->start . "," . $this->rows) : ""));

			$mapping = array(); // KEY = ID -> VALUE = ROWID
			$i = 0;
			while($this->DB_WE->next_record()) {
				$mapping[$this->DB_WE->Record["OF_ID"]] = $i;
				$i++;
			}

			if($this->order == ""){
				for($i = $offset; $i < min($offset + $rows, count($this->objects)); $i++){
					if(in_array($this->objects[$i], array_keys($mapping))){
						$this->Record[] = $mapping[$this->objects[$i]];
					}
				}
			} else{
				$count = array_count_values($this->objects);
				foreach($mapping as $objid => $rowid){
					for($i = 0; $i < $count[$objid]; $i++){
						$this->Record[] = $rowid;
					}
				}
			}
			$this->anz = count($this->Record);
		} else{
			$this->anz_all = 0;
			$this->anz = 0;
		}
		if($calendar != ''){
			$this->postFetchCalendar();
		}
	}

	function tableInMatrix($matrix, $table){
		if(OBJECT_X_TABLE . $this->classID == $table){
			return true;
		}
		foreach($matrix as $foo){
			if($foo["table"] == $table){
				return true;
			}
		}
		return false;
	}

	function fillMatrix(&$matrix, $classID, $db = ''){
		$db = $db ? $db : new DB_WE();
		$table = OBJECT_X_TABLE . $classID;
		$joinWhere = array();
		$tableInfo = we_objectFile::getSortedTableInfo($classID, true, $db);
		foreach($tableInfo as $fieldInfo){
			if(preg_match('/(.+?)_(.*)/', $fieldInfo["name"], $regs)){
				$type = $regs[1];
				$name = $regs[2];
				if($type == "object" && $name != $this->classID){
					if(!isset($matrix["we_object_" . $name]["type"]) || !$matrix["we_object_" . $name]["type"]){
						$matrix["we_object_" . $name]["type"] = $type;
						$matrix["we_object_" . $name]["table"] = $table;
						$matrix["we_object_" . $name]["classID"] = $classID;
						$foo = $this->fillMatrix($matrix, $name, $db);
						$joinWhere[] = OBJECT_X_TABLE . $classID . '.object_' . $name . '=' . OBJECT_X_TABLE . $name . '.OF_ID';
						if($foo){
							$joinWhere[] = $foo;
						}
					}
				} else{
					$matrix[$name]["type"] = $type;
					$matrix[$name]["table"] = $table;
					$matrix[$name]["classID"] = $classID;
				}
			}
		}
		return implode(' AND ', $joinWhere);
	}

	function makeSQLParts($matrix, $classID, $order, $cond){
		//FIXME: order ist totaler nonsense - das geht deutlich einfacher
		$from = array();
		$orderArr = array();
		$descArr = array();
		$ordertmp = array();

		$cond = ' ' . preg_replace("/'([^']*)'/e", "we_listview_object::encodeEregString('\\1')", strtr($cond, array('&gt;' => '>', '&lt;' => '<'))) . ' ';

		if($order && ($order != 'random()')){
			$foo = makeArrayFromCSV($order);
			foreach($foo as $f){
				$g = explode(' ', trim($f));
				$orderArr[] = $g[0];
				$descArr[] = intval(isset($g[1]) && strtolower(trim($g[1])) == 'desc');
			}
		}

		//get Metadata for class (default title, etc.)
		//BugFix #4629
		$_fieldnames = getHash('SELECT DefaultDesc,DefaultTitle,DefaultKeywords,CreationDate,ModDate FROM ' . OBJECT_TABLE . ' WHERE ID=' . $classID, $this->DB_WE);
		$_selFields = '';
		foreach($_fieldnames as $_key => $_val){
			if(empty($_val) || $_val == '_'){ // bug #4657
				continue;
			}
			if(!is_numeric($_key) && $_val){
				switch($_key){
					case 'DefaultDesc':
						$_selFields .= OBJECT_X_TABLE . $classID . '.`' . $_val . '` AS we_Description,';
						break;
					case 'DefaultTitle':
						$_selFields .= OBJECT_X_TABLE . $classID . '.`' . $_val . '` AS we_Title,';
						break;
					case 'DefaultKeywords':
						$_selFields .= OBJECT_X_TABLE . $classID . '.`' . $_val . '` AS we_Keywords,';
						break;
				}
			}
		}
		$_selFields .= OBJECT_X_TABLE . $classID . '.OF_Published' . ' AS we_wedoc_Published,';
		$f = OBJECT_X_TABLE . $classID . '.ID as ID,' . OBJECT_X_TABLE . $classID . '.OF_Templates as OF_Templates,' . OBJECT_X_TABLE . $classID . ".OF_ID as OF_ID," . OBJECT_X_TABLE . $classID . ".OF_Category as OF_Category," . OBJECT_X_TABLE . $classID . ".OF_Text as OF_Text," . OBJECT_X_TABLE . $classID . ".OF_Url as OF_Url," . OBJECT_X_TABLE . $classID . ".OF_TriggerID as OF_TriggerID," . OBJECT_X_TABLE . $classID . ".OF_WebUserID as OF_WebUserID," . OBJECT_X_TABLE . $classID . ".OF_Language as OF_Language," . $_selFields;
		foreach($matrix as $n => $p){
			$n2 = $n;
			if(substr($n, 0, 10) == 'we_object_'){
				$n = substr($n, 10);
			}
			$f .= $p['table'] . '.`' . $p['type'] . '_' . $n . '` AS `we_' . $n2 . '`,';
			$from[] = $p['table'];
			if(in_array($n, $orderArr)){
				$pos = getArrayKey($n, $orderArr);
				$ordertmp[$pos] = $p['table'] . '.`' . $p['type'] . '_' . $n . '`' . ($descArr[$pos] ? ' DESC' : '');
			}
			$cond = preg_replace("/([\!\=%&\(\*\+\.\/<>|~ ])$n([\!\=%&\)\*\+\.\/<>|~ ])/", '$1' . $p["table"] . '.`' . $p['type'] . '_' . $n . '`$2', $cond);
		}

		$cond = preg_replace("/'([^']*)'/e", "we_listview_object::decodeEregString('\\1')", $cond);

		ksort($ordertmp);
		$_tmporder = trim(str_ireplace('desc', '', $order));
		switch($_tmporder){
			case 'we_id':
			case 'we_filename':
			case 'we_published':
				$_tmporder = str_replace(array(
					'we_id', 'we_filename', 'we_published',), array(
					OBJECT_X_TABLE . $classID . '.OF_ID', OBJECT_X_TABLE . $classID . '.OF_Text', OBJECT_X_TABLE . $classID . '.OF_Published'), $_tmporder);
				$order = ' ORDER BY ' . $_tmporder . ($this->desc ? ' DESC' : '');
				break;
			case 'random()':
				$order = ' ORDER BY RANDOM ';
				break;
			default:
				$order = makeCSVFromArray($ordertmp);
				if($order){
					$order = ' ORDER BY ' . $order;
				}
				break;
		}

		$tb = array();
		$from = array_unique($from);
		foreach($from as $val){
			$tb[] = $val;
		}

		$out = array(
			"order" => $order,
			"tables" => makeCSVFromArray($tb),
			"groupBy" => (count($tb) > 1) ? ' GROUP BY ' . OBJECT_X_TABLE . $classID . ".ID " : '',
			"cond" => trim($cond),
			"fields" => rtrim($f, ','),
			"publ_cond" => array(),
		);
		if($order == ' ORDER BY RANDOM '){
			$out['fields'] .= ', RAND() as RANDOM ';
		}
		foreach($tb as $t){
			$out["publ_cond"] [] = "( $t.OF_Published > 0 OR $t.OF_ID = 0)";
		}
		$out["publ_cond"] = implode(' AND ', $out["publ_cond"]);
		if($out["publ_cond"]){
			$out["publ_cond"] = " ( " . $out["publ_cond"] . " ) ";
		}
		return $out;
	}

	function next_record(){
		$fetch = false;
		if($this->calendar_struct["calendar"] != ''){
			if($this->count < $this->anz){
				parent::next_record();
				$fetch = $this->calendar_struct["forceFetch"];
				$this->DB_WE->Record = array();
			} else{
				return false;
			}
		}

		if($this->calendar_struct["calendar"] == "" || $fetch){

			if($this->count < count($this->Record)){
				$paramName = "we_objectID";
				$this->DB_WE->Record($this->Record[$this->count]);
				$this->DB_WE->Record["we_wedoc_Path"] = $this->Path . "?$paramName=" . $this->DB_WE->Record["OF_ID"];
				$path_parts = pathinfo($this->Path);
				if($this->objectseourls && $this->DB_WE->Record['OF_Url'] != '' && show_SeoLinks()){
					if(!$this->triggerID && $this->DB_WE->Record['OF_TriggerID'] != 0){
						$path_parts = pathinfo(id_to_path($this->DB_WE->f('OF_TriggerID')));
					}
					if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$this->DB_WE->Record["we_WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $this->DB_WE->Record['OF_Url'];
					} else{
						$this->DB_WE->Record["we_WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $this->DB_WE->Record['OF_Url'];
					}
				} else{
					if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$this->DB_WE->Record["we_WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . "?$paramName=" . $this->DB_WE->Record["OF_ID"];
					} else{
						$this->DB_WE->Record["we_WE_PATH"] = $this->Path . "?$paramName=" . $this->DB_WE->Record["OF_ID"];
					}
				}
				$this->DB_WE->Record["we_WE_TRIGGERID"] = ($this->triggerID ? $this->triggerID : intval($this->DB_WE->f("OF_TriggerID")));
				$this->DB_WE->Record["we_WE_URL"] = $this->DB_WE->f("OF_Url");
				$this->DB_WE->Record["we_WE_TEXT"] = $this->DB_WE->f("OF_Text");
				$this->DB_WE->Record["we_WE_ID"] = $this->DB_WE->f("OF_ID");
				$this->DB_WE->Record["we_wedoc_Category"] = $this->DB_WE->f("OF_Category");

				// for seeMode #5317
				$this->DB_WE->Record["we_wedoc_lastPath"] = $this->LastDocPath . "?$paramName=" . $this->DB_WE->Record["OF_ID"];
				$this->count++;
				return true;
			} else{
				$this->stop_next_row = $this->shouldPrintEndTR();
				if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
					$this->DB_WE->Record = array(
						"WE_PATH" => "",
						"WE_TEXT" => "",
						"WE_ID" => ""
					);
					$this->count++;
					return true;
				}
				return false;
			}
		}

		return ($this->calendar_struct["calendar"] != '');
	}

	function f($key){
		return $this->DB_WE->f('we_' . $key);
	}

}

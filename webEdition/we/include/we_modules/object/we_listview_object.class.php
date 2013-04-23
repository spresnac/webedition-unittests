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
 * class    we_listview_object
 * @desc    class for tag <we:listview type="object">
 *
 */
class we_listview_object extends listviewBase{

	var $classID = ""; /* ID of a class */
	var $triggerID = 0; /* ID of a document which to use for displaying thr detail page */
	var $condition = ""; /* condition string (like SQL) */
	var $ClassName = __CLASS__;
	var $Path = ""; /* internal: Path of document which to use for displaying thr detail page */
	var $IDs = array();
	var $searchable = true;
	var $customerFilterType = 'off';
	var $customers = "";
	var $we_predefinedSQL = "";
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
	function __construct($name = '0', $rows = 9999999, $offset = 0, $order = "", $desc = false, $classID = 0, $cats = "", $catOr = "", $condition = "", $triggerID = "", $cols = "", $seeMode = true, $searchable = true, $calendar = "", $datefield = "", $date = "", $weekstart = "", $categoryids = '', $workspaceID = '', $customerFilterType = 'off', $docID = 0, $customers = "", $id = "", $we_predefinedSQL = "", $languages = '', $hidedirindex = false, $objectseourls = false){
		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, $workspaceID, $cols, $calendar, $datefield, $date, $weekstart, $categoryids, $customerFilterType, $id);

		$this->classID = $classID;
		$this->triggerID = $triggerID;

		$this->seeMode = $seeMode; //	edit objects in seeMode
		$this->searchable = $searchable;
		$this->docID = $docID;
		$this->customers = $customers;
		$this->customerArray = array();

		$this->condition = $condition ? $condition : (isset($GLOBALS["we_lv_condition"]) ? $GLOBALS["we_lv_condition"] : '');
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

		$this->we_predefinedSQL = $we_predefinedSQL;

		$this->Path =
			($this->docID ?
				id_to_path($this->docID, FILE_TABLE, $this->DB_WE) :
				($this->triggerID && show_SeoLinks() ?
					id_to_path($this->triggerID, FILE_TABLE, $this->DB_WE) :
					(isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : '')
				)
			);


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

		$pid_tail = (isset($GLOBALS['we_doc']) ? makePIDTail($GLOBALS['we_doc']->ParentID, $this->classID, $this->DB_WE, $GLOBALS['we_doc']->Table) : '1');

		$cat_tail = ($this->cats || $this->categoryids ? we_category::getCatSQLTail($this->cats, $_obxTable, $this->catOr, $this->DB_WE, "OF_Category", true, $this->categoryids) : '');

		$weDocumentCustomerFilter_tail = "";
		if($this->customerFilterType != 'off' && defined("CUSTOMER_FILTER_TABLE")){
			$weDocumentCustomerFilter_tail = weDocumentCustomerFilter::getConditionForListviewQuery($this);
		}

		$webUserID_tail = '';
		if($this->customers && $this->customers !== "*"){

			$_wsql = ' ' . $_obxTable . '.OF_WebUserID IN(' . $this->customers . ') ';
			$this->DB_WE->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $this->customers . ')');
			while($this->DB_WE->next_record()) {
				$this->customerArray['cid_' . $this->DB_WE->f('ID')] = $this->DB_WE->getRecord();
			}

			$webUserID_tail = ' AND (' . $_wsql . ') ';
		}

		if(!empty($sqlParts["tables"]) || $we_predefinedSQL != ''){

			if($we_predefinedSQL != ""){
				$this->DB_WE->query($we_predefinedSQL);
				$this->anz_all = $this->DB_WE->num_rows();
				$q = $we_predefinedSQL . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : '');
			} else{
				$_idTail = $this->getIdQuery($_obxTable . '.OF_ID');

				if($this->workspaceID != ''){
					$workspaces = makeArrayFromCSV($this->workspaceID);
					$cond = array();
					foreach($workspaces as $id){
						$workspace = id_to_path($id, OBJECT_FILES_TABLE, $this->DB_WE);
						$cond[] = $_obxTable . '.OF_Path LIKE "' . $workspace . '/%"';
						$cond[] = $_obxTable . '.OF_Path="' . $workspace . '"';
					}
					$ws_tail = empty($cond) ? '' : ' AND (' . implode(' OR ', $cond) . ') ';
				} else{
					$ws_tail = '';
				}
				$this->DB_WE->query('SELECT ' . $_obxTable . '.ID AS ID ' . $calendar_select . ' FROM ' . $sqlParts["tables"] . ' WHERE ' . ($this->searchable ? " " . $_obxTable . ".OF_IsSearchable=1 AND" : "") . " " . $pid_tail . " AND " . $_obxTable . ".OF_ID != 0 " . $where_lang . ($join ? " AND ($join) " : "") . $cat_tail . " " . ($sqlParts["publ_cond"] ? (" AND " . $sqlParts["publ_cond"]) : "") . " " . ($sqlParts["cond"] ? (" AND (" . $sqlParts["cond"] . ") ") : "") . $calendar_where . $ws_tail . $weDocumentCustomerFilter_tail . $webUserID_tail . $_idTail . $sqlParts['groupBy']);
				$this->anz_all = $this->DB_WE->num_rows();
				if($calendar != ""){
					while($this->DB_WE->next_record()) {
						$this->IDs[] = $this->DB_WE->f('ID');
						if($calendar != ''){
							$this->calendar_struct["storage"][$this->DB_WE->f("ID")] = (int) $this->DB_WE->f("Calendar");
						}
					}
				}
				$q = 'SELECT ' . $sqlParts["fields"] . $calendar_select . ' FROM ' . $sqlParts['tables'] . ' WHERE ' . ($this->searchable ? ' ' . $_obxTable . '.OF_IsSearchable=1 AND' : '') . ' ' . $pid_tail . ' AND ' . $_obxTable . ".OF_ID != 0 " . $where_lang . ($join ? " AND ($join) " : "") . $cat_tail . " " . ($sqlParts["publ_cond"] ? (' AND ' . $sqlParts["publ_cond"]) : '') . ' ' . ($sqlParts["cond"] ? (' AND (' . $sqlParts['cond'] . ') ') : '') . $calendar_where . $ws_tail . $weDocumentCustomerFilter_tail . $webUserID_tail . $_idTail . $sqlParts['groupBy'] . $sqlParts["order"] . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : '');
			}
			$this->DB_WE->query($q);
			$this->anz = $this->DB_WE->num_rows();

			if($this->customers === '*'){
				$_idListArray = array();
				while($this->DB_WE->next_record()) {
					if(intval($this->DB_WE->f("OF_WebUserID")) > 0){
						$_idListArray[] = $this->DB_WE->f("OF_WebUserID");
					}
				}
				if(!empty($_idListArray)){
					$_idlist = implode(',', array_unique($_idListArray));
					$db = new DB_WE();
					$db->query('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID IN(' . $_idlist . ')');
					while($db->next_record()) {
						$this->customerArray["cid_" . $db->f("ID")] = $db->Record;
					}
				}
				unset($_idListArray);

				$this->DB_WE->seek(0);
			}
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
		$db = ($db ? $db : new DB_WE());
		$table = OBJECT_X_TABLE . $classID;
		$joinWhere = array();
		$tableInfo = we_objectFile::getSortedTableInfo($classID, true, $db, true);
		foreach($tableInfo as $fieldInfo){
			if(preg_match('/(.+?)_(.*)/', $fieldInfo["name"], $regs)){
				$type = $regs[1];
				$name = $regs[2];
				if($type == "object" && $name != $this->classID){
					if(!isset($matrix["we_object_" . $name]["type"]) || !$matrix["we_object_" . $name]["type"]){
						$matrix["we_object_" . $name]["type"] = $type;
						$matrix["we_object_" . $name]["table"] = $table;
						$matrix["we_object_" . $name]["table2"] = OBJECT_X_TABLE . $name;
						$matrix["we_object_" . $name]["classID"] = $classID;
						$foo = $this->fillMatrix($matrix, $name, $db);
						$joinWhere[] = OBJECT_X_TABLE . $classID . '.object_' . $name . '=' . OBJECT_X_TABLE . $name . '.OF_ID';
						if($foo){
							$joinWhere[] = $foo;
						}
					}
				} else{
					if(!isset($matrix[$name])){
						$matrix[$name]["type"] = $type;
						$matrix[$name]["table"] = $table;
						$matrix[$name]["classID"] = $classID;
						$matrix[$name]["table2"] = $table;
					}
				}
			}
		}
		return implode(' AND ', $joinWhere);
	}

	static function encodeEregString($in){
		$out = '';
		for($i = 0; $i < strlen($in); $i++){
			$out .= '&' . ord(substr($in, $i, 1)) . ';';
		}
		return "'" . $out . "'";
	}

	static function decodeEregString($in){
		return "'" . preg_replace("/&([^;]+);/e", "chr('\\1')", $in) . "'";
	}

	function makeSQLParts($matrix, $classID, $order, $cond){
		//FIXME: order ist totaler nonsense - das geht deutlich einfacher
		$from = array();
		$orderArr = array();
		$descArr = array();
		$ordertmp = array();

		$cond = str_replace(array('&gt;', '&lt;'), array('>', '<',), $cond);

		$cond = ' ' . preg_replace("/'([^']*)'/e", "we_listview_object::encodeEregString('\\1')", $cond) . ' ';


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
			if(empty($_val) || $_val == '_') // bug #4657
				continue;
			if(!is_numeric($_key) && $_val){
				switch($_key){
					case 'DefaultDesc':
						$_selFields .= OBJECT_X_TABLE . $classID . '.' . $_val . ' AS we_Description,';
						break;
					case 'DefaultTitle':
						$_selFields .= OBJECT_X_TABLE . $classID . '.' . $_val . ' AS we_Title,';
						break;
					case 'DefaultKeywords':
						$_selFields .= OBJECT_X_TABLE . $classID . '.' . $_val . ' AS we_Keywords,';
						break;
				}
			}
		}
		$_selFields .= OBJECT_X_TABLE . $classID . '.OF_Published' . ' AS we_wedoc_Published,';
		$f = OBJECT_X_TABLE . $classID . '.ID AS ID,' . OBJECT_X_TABLE . $classID . '.OF_Templates AS OF_Templates,' . OBJECT_X_TABLE . $classID . ".OF_ID AS OF_ID," . OBJECT_X_TABLE . $classID . ".OF_Category AS OF_Category," . OBJECT_X_TABLE . $classID . ".OF_Text AS OF_Text," . OBJECT_X_TABLE . $classID . ".OF_Url AS OF_Url," . OBJECT_X_TABLE . $classID . ".OF_TriggerID AS OF_TriggerID," . OBJECT_X_TABLE . $classID . ".OF_WebUserID AS OF_WebUserID," . OBJECT_X_TABLE . $classID . ".OF_Language AS OF_Language," . $_selFields;
		foreach($matrix as $n => $p){
			$n2 = $n;
			if(substr($n, 0, 10) == 'we_object_'){
				$n = substr($n, 10);
			}
			$f .= '`' . $p['table'] . '`.`' . $p['type'] . '_' . $n . '` AS `we_' . $n2 . '`,';
			$from[] = $p["table"];
			$from[] = $p["table2"];
			if(in_array($n, $orderArr)){
				$pos = getArrayKey($n, $orderArr);
				$ordertmp[$pos] = '`' . $p["table"] . '`.`' . $p["type"] . '_' . $n . '`' . ($descArr[$pos] ? ' DESC' : '');
			}
			$cond = preg_replace("/([\!\=%&\(\*\+\.\/<>|~ ])$n([\!\=%&\)\*\+\.\/<>|~ ])/", "$1" . $p["table"] . ".`" . $p["type"] . "_" . $n . "`$2", $cond);
		}

		$cond = preg_replace("/'([^']*)'/e", "we_listview_object::decodeEregString('\\1')", $cond);

		ksort($ordertmp);
		$_tmporder = trim(str_ireplace('desc', '', $order));
		switch($_tmporder){
			case 'we_id':
			case 'we_filename':
			case 'we_published':
				$_tmporder = str_replace(array(
					'we_id', 'we_filename', 'we_published'), array(
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

		$publ_cond = array();
		foreach($tb as $t){
			$publ_cond [] = "( $t.OF_Published > 0 OR $t.OF_ID = 0)";
		}

		return array(
			"fields" => rtrim($f, ',') . ($order == ' ORDER BY RANDOM ' ? ', RAND() AS RANDOM ' : ''),
			"order" => $order,
			"tables" => makeCSVFromArray($tb),
			"groupBy" => (count($tb) > 1) ? ' GROUP BY ' . OBJECT_X_TABLE . $classID . ".ID " : '',
			"publ_cond" => empty($publ_cond) ? '' : ' ( ' . implode(' AND ', $publ_cond) . ' ) ',
			"cond" => trim($cond)
		);
	}

	function next_record(){
		$count = $this->count;
		$fetch = false;
		if($this->calendar_struct["calendar"] != ""){
			if($this->count < $this->anz){
				listviewBase::next_record();
				$count = $this->calendar_struct["count"];
				$fetch = $this->calendar_struct["forceFetch"];
				$this->DB_WE->Record = array();
			} else{
				return false;
			}
		}

		if($this->calendar_struct["calendar"] == "" || $fetch){
			$ret = $this->DB_WE->next_record();

			if($ret){
				$paramName = $this->docID ? "we_oid" : "we_objectID";
				$this->DB_WE->Record["we_wedoc_Path"] = $this->Path . "?$paramName=" . $this->DB_WE->Record["OF_ID"];
				$this->DB_WE->Record["we_wedoc_WebUserID"] = isset($this->DB_WE->Record["OF_WebUserID"]) ? $this->DB_WE->Record["OF_WebUserID"] : 0; // needed for ifRegisteredUserCanChange tag
				$this->DB_WE->Record["we_WE_CUSTOMER_ID"] = $this->DB_WE->Record["we_wedoc_WebUserID"];
				$path_parts = pathinfo($this->Path);
				if($this->objectseourls && $this->DB_WE->Record['OF_Url'] != '' && show_SeoLinks()){
					if(!$this->triggerID && $this->DB_WE->Record['OF_TriggerID'] != 0){
						$path_parts = pathinfo(id_to_path($this->DB_WE->f('OF_TriggerID')));
					}
					$this->DB_WE->Record["we_WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' .
						(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
							'' :
							$path_parts['filename'] . '/' ) .
						$this->DB_WE->Record['OF_Url'];
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
				$this->DB_WE->Record["we_WE_SHOPVARIANTS"] = 0;
				if(isset($this->DB_WE->Record["we_weInternVariantElement"])){
					$ShopVariants = @unserialize($this->DB_WE->Record["we_weInternVariantElement"]);
					if(is_array($ShopVariants) && count($ShopVariants) > 0){
						$this->DB_WE->Record["we_WE_SHOPVARIANTS"] = count($ShopVariants);
					}
				}
				// for seeMode #5317
				$this->DB_WE->Record["we_wedoc_lastPath"] = $this->LastDocPath . "?$paramName=" . $this->DB_WE->Record["OF_ID"];
				if($this->customers && $this->DB_WE->Record["we_wedoc_WebUserID"]){
					if(isset($this->customerArray["cid_" . $this->DB_WE->Record["we_wedoc_WebUserID"]])){
						foreach($this->customerArray["cid_" . $this->DB_WE->Record["we_wedoc_WebUserID"]] as $key => $value){
							$this->DB_WE->Record["we_WE_CUSTOMER_$key"] = $value;
						}
					}
				}

				$this->count++;
				return true;
			} else{
				$this->stop_next_row = $this->shouldPrintEndTR();
				if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
					$this->DB_WE->Record = array(
						'WE_PATH' => '',
						'WE_TEXT' => '',
						'WE_ID' => '',
					);
					$this->count++;
					return true;
				}
			}
		}

		return ($this->calendar_struct["calendar"] != '');
	}

	function f($key){
		return $this->DB_WE->f('we_' . $key);
	}

}
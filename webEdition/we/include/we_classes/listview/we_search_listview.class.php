<?php

/**
 * webEdition CMS
 *
 * $Rev: 5931 $
 * $Author: mokraemer $
 * $Date: 2013-03-09 00:19:51 +0100 (Sat, 09 Mar 2013) $
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
 * @package    webEdition_listview
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * class    we_search_listview
 * @desc    class for tag <we:listview type="search">
 *          the difference to the normal listview is, that you can only
 *          display the fields from the index table (tblIndex) which are
 *          Title, Description we_text, we_path
 *
 */
class we_search_listview extends listviewBase{

	var $docType = ''; /* doctype string */
	var $class = 0; /* ID of a class. Search only in Objects of this class */
	var $triggerID = 0; /* ID of a document which to use for displaying thr detail page */
	var $casesensitive = false; /* set to true when a search should be case sensitive */
	var $ClassName = __CLASS__;
	var $languages = ''; //string of Languages, separated by ,
	var $objectseourls = false;
	var $hidedirindex = false;

	/**
	 * we_search_listview()
	 * @desc    constructor of class
	 *
	 * @param   name         string - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   docType       string - doctype
	 * @param   class         integer - ID of a class. Search only in Objects of this class
	 * @param   cats          string - comma separated categories
	 * @param   catOr         boolean - set to true if it should be an "OR condition"
	 * @param   casesensitive boolean - set to true when a search should be case sensitive
	 * @param   workspaceID   string - commaseperated list of id's of workspace
	 * @param   cols   		  integer - to display a table this is the number of cols
	 *
	 */
	function __construct($name = '0', $rows = 99999999, $offset = 0, $order = '', $desc = false, $docType = '', $class = 0, $cats = '', $catOr = false, $casesensitive = false, $workspaceID = 0, $triggerID = 0, $cols = '', $customerFilterType = 'off', $languages = '', $hidedirindex = false, $objectseourls = false){
		parent::__construct($name, $rows, $offset, $order, $desc, $cats, $catOr, $workspaceID, $cols);

		$this->customerFilterType = $customerFilterType;
		$this->triggerID = $triggerID;
		$this->objectseourls = $objectseourls;
		$this->hidedirindex = $hidedirindex;
		$this->languages = $languages ? $languages : (isset($GLOBALS["we_lv_languages"]) ? $GLOBALS["we_lv_languages"] : "");

		if($this->languages != ''){
			$where_lang = ' AND (';
			$langArray = makeArrayFromCSV($this->languages);
			$where_lang .= INDEX_TABLE . ".Language = '" . $langArray[0] . "' ";
			for($i = 1; $i < count($langArray); $i++){
				$where_lang .= "OR " . INDEX_TABLE . ".Language = '" . $langArray[$i] . "' ";
			}

			$where_lang .= ' ) ';
		} else{
			$where_lang = '';
		}

		// correct order
		$orderArr = array();
		$random = false;
		if($this->order){
			if($this->order == "we_id" || $this->order == "we_creationdate" || $this->order == 'we_filename'){

				$ord = str_replace('we_id', INDEX_TABLE . ".DID" . ($this->desc ? " DESC" : "") . ',' . INDEX_TABLE . ".OID" . ($this->desc ? " DESC" : ""), $this->order);
				//$ord = str_replace("we_creationdate",FILE_TABLE . ".CreationDate",$ord); // NOTE: this won't work, cause Indextable doesn't know this field & filetable is not used in this query
				$ord = str_replace('we_creationdate', '', $ord);
				$this->order = str_replace("we_filename", INDEX_TABLE . ".Path", $ord);
			} else{
				$orderArr1 = makeArrayFromCSV($this->order);
				if(in_array('random()', $orderArr1)){
					$random = true;
				} else{
					foreach($orderArr1 as $o){
						if(trim($o)){
							$foo = preg_split('/ +/', $o);
							$oname = $foo[0];
							$otype = isset($foo[1]) ? $foo[1] : "";
							$orderArr[] = array("oname" => $oname, "otype" => $otype);
						}
					}
					$this->order = "";
					foreach($orderArr as $o){
						switch($o["oname"]){
							case "Title":
							case "Path":
							case "Text":
							case "OID":
							case "DID":
							case "ID":
							case "Workspace":
							case "Description":
								$this->order .= $o["oname"] . ((trim(strtolower($o["otype"])) == "desc") ? " DESC" : "") . ",";
						}
					}
					$this->order = rtrim($this->order, ',');
				}
			}
		}

		if($this->order && $this->desc && (!preg_match('|.+ desc$|i', $this->order))){
			$this->order .= ' DESC';
		}

		$this->docType = trim($docType);
		$this->class = $class;
		$this->casesensitive = $casesensitive;


		$cat_tail = ($this->cats ? we_category::getCatSQLTail($this->cats, INDEX_TABLE, $this->catOr, $this->DB_WE) : '');

		$dt = ($this->docType) ? f('SELECT ID FROM ' . DOC_TYPES_TABLE . " WHERE DocType LIKE '" . $this->DB_WE->escape($this->docType) . "'", "ID", $this->DB_WE) : '';

		$cl = $this->class;

		if($dt && $cl){
			$dtcl_query = " AND (" . INDEX_TABLE . ".Doctype='" . $this->DB_WE->escape($dt) . "' OR " . INDEX_TABLE . ".ClassID=" . intval($cl) . ") ";
		} else if($dt){
			$dtcl_query = " AND " . INDEX_TABLE . ".Doctype='" . $this->DB_WE->escape($dt) . "' ";
		} else if($cl){
			$dtcl_query = " AND " . INDEX_TABLE . ".ClassID=" . intval($cl) . ' ';
		} else{
			$dtcl_query = '';
		}



		$bedingungen = preg_split('/ +/', $this->search);
		$ranking = "0";
		$spalten = array(($this->casesensitive ? 'BINARY ' : '') . INDEX_TABLE . '.Text');
		foreach($bedingungen as $v1){
			if(preg_match('|^[-\+]|', $v1)){
				$not = (preg_match('|^-|', $v1)) ? 'NOT ' : '';
				$bed = preg_replace('|^[-\+]|', '', $v1);
				$klammer = array();
				reset($spalten);
				foreach($spalten as $v){
					$klammer[] = sprintf("%s LIKE '%%%s%%'", $v, addslashes($bed));
				}
				if($not){
					$bedingungen3_sql[] = $not . '(' . implode($klammer, ' OR ') . ')';
				} else{
					$bedingungen_sql[] = '(' . implode($klammer, ' OR ') . ')';
				}
			} else{
				$klammer = array();
				reset($spalten);
				foreach($spalten as $v){
					$klammer[] = sprintf("%s LIKE '%%%s%%'", $v, addslashes($v1));
				}
				$bed2 = "(" . implode($klammer, " OR ") . ")";
				$ranking .= "-" . $bed2;
				$bedingungen2_sql[] = $bed2;
			}
		}

		if(isset($bedingungen_sql) && count($bedingungen_sql) > 0){
			$bedingung_sql1 = " ( " . implode($bedingungen_sql, " AND ") . (isset($bedingungen3_sql) && count($bedingungen3_sql) ? (" AND " . implode($bedingungen3_sql, " AND ")) : "") . " ) ";
		} else if(isset($bedingungen2_sql) && count($bedingungen2_sql) > 0){
			$bedingung_sql2 = " ( ( " . implode($bedingungen2_sql, " OR ") . (isset($bedingungen3_sql) && count($bedingungen3_sql) ? (" ) AND " . implode($bedingungen3_sql, " AND ")) : " ) ") . " ) ";
		} else if(isset($bedingungen3_sql) && count($bedingungen3_sql) > 0){
			$bedingung_sql2 = implode($bedingungen3_sql, " AND ");
		}

		if(isset($bedingung_sql1) && $bedingung_sql1){
			$bedingung_sql = $bedingung_sql1;
		} else{
			$bedingung_sql = $bedingung_sql2;
		}
		if($this->workspaceID != ""){
			$workspaces = makeArrayFromCSV($this->workspaceID);
			$cond = array();
			foreach($workspaces as $id){
				$workspace = id_to_path($id, FILE_TABLE, $this->DB_WE);
				array_push($cond, "(" . INDEX_TABLE . ".Workspace LIKE '" . $this->DB_WE->escape($workspace) . "/%' OR " . INDEX_TABLE . ".Workspace='" . $this->DB_WE->escape($workspace) . "')");
			}
			$ws_where = ' AND (' . implode(' OR ', $cond) . ')';
		} else{
			$ws_where = '';
		}

		$weDocumentCustomerFilter_tail = '';
		if($this->customerFilterType != 'off' && defined("CUSTOMER_FILTER_TABLE")){
			$weDocumentCustomerFilter_tail = weDocumentCustomerFilter::getConditionForListviewQuery($this);
		}

		$this->DB_WE->query('SELECT ID FROM ' . INDEX_TABLE . " WHERE $bedingung_sql $dtcl_query $cat_tail $ws_where $where_lang $weDocumentCustomerFilter_tail");
		$this->anz_all = $this->DB_WE->num_rows();

		$this->DB_WE->query('SELECT ' . INDEX_TABLE . ".Category as Category, " . INDEX_TABLE . ".DID as DID," . INDEX_TABLE . ".OID as OID," . INDEX_TABLE . ".ClassID as ClassID," . INDEX_TABLE . ".Text as Text," . INDEX_TABLE . ".Workspace as Workspace," . INDEX_TABLE . ".WorkspaceID as WorkspaceID," . INDEX_TABLE . ".Title as Title," . INDEX_TABLE . ".Description as Description," . INDEX_TABLE . ".Path as Path," . INDEX_TABLE . '.Language as Language, ' . ($random ? 'RAND() ' : $ranking) . ' AS ranking FROM ' . INDEX_TABLE . " WHERE $bedingung_sql $dtcl_query $cat_tail $ws_where $where_lang $weDocumentCustomerFilter_tail ORDER BY ranking" . ($this->order ? ("," . $this->order) : "") . (($this->maxItemsPerPage > 0) ? (" LIMIT " . intval($this->start) . ',' . intval($this->maxItemsPerPage)) : ""));
		$this->anz = $this->DB_WE->num_rows();
	}

	function next_record(){
		$ret = $this->DB_WE->next_record();
		if($ret){
			if($this->DB_WE->Record["OID"] && $this->objectseourls && show_SeoLinks()){
				$db = new DB_WE();
				$path_parts = pathinfo($_SERVER["SCRIPT_NAME"]);
				$objectdaten = getHash("SELECT  Url,TriggerID FROM " . OBJECT_FILES_TABLE . " WHERE ID=" . intval($this->DB_WE->Record["OID"]) . " LIMIT 1", $db);
				$objecturl = $objectdaten['Url'];
				$objecttriggerid = ($this->triggerID ? $this->triggerID : $objectdaten['TriggerID']);

				if($objecttriggerid){
					$path_parts = pathinfo(id_to_path($objecttriggerid));
				}
				$pidstr = ($this->DB_WE->Record["WorkspaceID"] ? '?pid=' . intval($this->DB_WE->Record["WorkspaceID"]) : '');

				if(NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$this->DB_WE->Record["WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') .
						($objecturl != '' ?
							'/' . $objecturl . $pidstr :
							'/?we_objectID=' . $this->DB_WE->Record["OID"] . str_replace('?', '&amp;', $pidstr));
				} else{
					$this->DB_WE->Record["WE_PATH"] = ($objecturl != '' ?
							($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $objecturl . $pidstr :
							$_SERVER["SCRIPT_NAME"] . '?we_objectID=' . $this->DB_WE->Record["OID"] . str_replace('?', '&amp;', $pidstr));
				}
				$this->DB_WE->Record["wedoc_Path"] = $this->DB_WE->Record["WE_PATH"];
				$this->DB_WE->Record["we_WE_URL"] = $objectdaten['Url'];
				$this->DB_WE->Record["we_WE_TRIGGERID"] = ($this->triggerID ? $this->triggerID : $objectdaten['TriggerID']);
			} else{
				$this->DB_WE->Record["wedoc_Path"] = $this->DB_WE->Record["Path"];
				$this->DB_WE->Record["WE_PATH"] = $this->DB_WE->Record["Path"];
			}
			$this->DB_WE->Record["WE_LANGUAGE"] = $this->DB_WE->Record["Language"];
			$this->DB_WE->Record["WE_TEXT"] = $this->DB_WE->Record["Text"];
			$this->DB_WE->Record["wedoc_Category"] = $this->DB_WE->Record["Category"];
			$this->DB_WE->Record["WE_ID"] = (isset($this->DB_WE->Record["DID"]) && $this->DB_WE->Record["DID"]) ? $this->DB_WE->Record["DID"] : (isset($this->DB_WE->Record["OID"]) ? $this->DB_WE->Record["OID"] : 0);
			$this->count++;
			return true;
		} else{
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
				$this->DB_WE->Record = array(
					"WE_LANGUAGE" => '',
					"WE_PATH" => '',
					"WE_TEXT" => '',
					"WE_ID" => '',
				);
				$this->count++;
				return true;
			}
		}
		return false;
	}

	function f($key){
		return $this->DB_WE->f($key);
	}

}
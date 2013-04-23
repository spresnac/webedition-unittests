<?php

/**
 * webEdition CMS
 *
 * $Rev: 4375 $
 * $Author: mokraemer $
 * $Date: 2012-03-30 00:01:55 +0200 (Fri, 30 Mar 2012) $
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
 * class    we_listview
 * @desc    class for tag <we:listview>
 *
 */
class we_catListview extends listviewBase{

	var $parentID = 0;
	var $catID = 0;
	var $variant = 'default';
	var $ClassName = __CLASS__;
	var $hidedirindex = false;

	/**
	 * we_listview()
	 * constructor of class
	 *
	 * @param   name          string  - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string  - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   parentID         integer - Id from Parent Category
	 * @param   variant       string - at the moment only "default" supported
	 * @param   cols   		  integer - to display a table this is the number of cols
	 *
	 */
	function __construct($name="0", $rows=999999999, $offset=0, $order="", $desc=false, $parentID=0, $catID=0, $variant="default", $cols="", $parentidname='we_parentid', $hidedirindex=false){
		parent::__construct($name, $rows, $offset, $order, $desc, "", false, "", $cols);
		$this->parentID = isset($_REQUEST[$parentidname]) ? intval($_REQUEST[$parentidname]) : intval($parentID);
		$this->catID = trim($catID);

		$this->variant = $variant;
		if(stripos($this->order, " desc") !== false){//was #3849
			$this->order = str_ireplace(" desc", "", $this->order);
			$this->desc = true;
		}

		$this->order = trim($this->order);

		$orderstring = $this->order ? (' ORDER BY ' . $this->order . ($this->desc ? ' DESC' : '')) : '';

		$this->hidedirindex = $hidedirindex;

		if($this->catID){
			$cids = explode(",", $this->catID);
			$tail = "";
			foreach($cids as $cid){
				$tail .= 'ID=' . intval($cid) . ' OR ';
			}
			$tail = preg_replace('/^(.+) OR /', '$1', $tail);
			$tail = '(' . $tail . ')';
		} else{
			$tail = ' ParentID=' . intval($this->parentID) . ' ';
		}

		$this->anz_all = f('SELECT COUNT(1) AS max FROM ' . CATEGORY_TABLE . ' WHERE ' . $tail, 'max', $this->DB_WE);

		$this->DB_WE->query('SELECT *' . ($this->order == 'random()' ? ', RAND() as RANDOM' : '') . ' FROM ' . CATEGORY_TABLE . ' WHERE ' . $tail . ' ' . ($this->order == 'random()' ? 'ORDER BY RANDOM' : $orderstring) . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : ''));
		$this->anz = $this->DB_WE->num_rows();

		$this->count = 0;
	}

	function next_record(){
		if($this->DB_WE->next_record()){
			$count = $this->count;
			$arr = $this->DB_WE->f("Catfields");
			$arr = $arr ? unserialize($arr) : array();

			$this->Record["WE_PATH"] = $this->DB_WE->f("Path");
			$this->Record["Path"] = $this->Record["WE_PATH"];
			$this->Record["WE_TITLE"] = isset($arr[$this->variant]["Title"]) ? $arr[$this->variant]["Title"] : '';
			$this->Record["WE_DESCRIPTION"] = isset($arr[$this->variant]["Description"]) ? $arr[$this->variant]["Description"] : '';
			$this->Record["WE_ID"] = $this->DB_WE->Record["ID"];
			$this->Record["Category"] = $this->DB_WE->f("Category");
			$this->Record["ParentID"] = $this->DB_WE->Record["ParentID"];
			$this->Record["ID"] = $this->Record["WE_ID"];
			$this->Record["Title"] = $this->Record["WE_TITLE"];
			$this->Record["Description"] = $this->Record["WE_DESCRIPTION"];

			$this->count++;
			return true;
		} else{
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
				$this->Record = array();
				$this->DB_WE->Record = array();
				$this->DB_WE->Record["WE_PATH"] = "";
				$this->DB_WE->Record["WE_TEXT"] = "";
				$this->DB_WE->Record["WE_ID"] = "";
				$this->count++;
				return true;
			}
		}
		return false;
	}

	function f($key){
		return isset($this->Record[$key]) ? $this->Record[$key] : '';
	}

}

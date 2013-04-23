<?php

/**
 * webEdition CMS
 *
 * $Rev: 5928 $
 * $Author: mokraemer $
 * $Date: 2013-03-08 23:23:10 +0100 (Fri, 08 Mar 2013) $
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
 * class    we_listview_customer
 * @desc    class for tag <we:listview type="banner">
 *
 */
class we_shop_listviewOrderitem extends listviewBase{

	var $ClassName = __CLASS__;
	var $condition = "";
	var $Path = "";
	var $docID = 0;
	var $orderID = 0;
	var $hidedirindex = false;

	/**
	 * @desc    constructor of class
	 *
	 * @param   $name          string - name of listview
	 * @param   $rows          integer - number of rows to display per page
	 * @param   $order         string - field name(s) to ORDER BY
	 * @param   $desc		   string - if desc order
	 * @param   $condition	   string - condition of listview
	 * @param   $cols		   string - number of cols (default = 1)
	 * @param   $docID	   	   string - id of a document where a we:customer tag is on
	 *
	 */
	function __construct($name="0", $rows=100000000, $offset=0, $order="", $desc=false, $condition="", $cols="", $docID=0, $orderID=0, $hidedirindex=false){

		parent::__construct($name, $rows, $offset, $order, $desc, "", false, 0, $cols);

		$this->docID = $docID;
		$this->orderID = $orderID;
		$this->condition = $condition ? $condition : (isset($GLOBALS["we_lv_condition"]) ? $GLOBALS["we_lv_condition"] : "");

		if(strpos($this->condition, 'ID') !== false && strpos($this->condition, 'IntID') === false){
			$this->condition = str_replace('ID', 'IntID', $this->condition);
		}
		// und nun sind alle anderen kaputt und werden repariert
		$this->condition = str_replace(array(
			'OrderIntID',
			'CustomerIntID',
			'ArticleIntID',
			'Quantity',
			'Payment_Type',
			), array(
			'IntOrderID',
			'IntCustomerID',
			'IntArticleID',
			'IntQuantity',
			'IntPayment_Type',
			), $this->condition);


		$this->Path = ($this->docID ? id_to_path($this->docID, FILE_TABLE, $this->DB_WE) : (isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : ''));

		$this->hidedirindex = $hidedirindex;
		// IMPORTANT for seeMode !!!! #5317
		$this->LastDocPath = '';
		if(isset($_SESSION['weS']['last_webEdition_document'])){
			$this->LastDocPath = $_SESSION['weS']['last_webEdition_document']['Path'];
		}



		if($this->desc && $this->order != '' && (!preg_match("|.+ desc$|i", $this->order))){
			$this->order .= " DESC";
		}

		if($this->order != ''){
			if(trim($this->order) == 'ID' || trim($this->order) == 'CustomerID' || trim($this->order) == 'ArticleID' || trim($this->order) == 'Quantity' || trim($this->order) == 'Payment_Type'){
				$this->order = 'Int' . $this->order;
			}

			$orderstring = " ORDER BY " . $this->order . " ";
		} else{
			$orderstring = '';
		}

		if($this->orderID != 0){
			$where = $this->condition ? (' WHERE IntOrderID=' . $this->name . ' AND ' . $this->condition ) : ' WHERE IntOrderID=' . $this->orderID . ' ';
		} else{
			$where = $this->condition ? (' WHERE ' . $this->condition ) : ' ';
		}

		$this->DB_WE->query('SELECT 1 FROM ' . SHOP_TABLE . $where);
		$this->anz_all = $this->DB_WE->num_rows();

		$this->DB_WE->query('SELECT IntID as ID,IntOrderID as OrderID, IntArticleID as ArticleID, IntQuantity as Quantity, Price, strSerial FROM ' . SHOP_TABLE . $where . ' ' . $orderstring . ' ' . (($this->maxItemsPerPage > 0) ? (' LIMIT ' . $this->start . ',' . $this->maxItemsPerPage) : ''));
		$this->anz = $this->DB_WE->num_rows();
	}

	function next_record(){
		$ret = $this->DB_WE->next_record();
		if($ret){
			$strSerial = @unserialize($this->DB_WE->Record["strSerial"]);
			unset($this->DB_WE->Record["strSerial"]);
			if(is_array($strSerial)){
				if(isset($strSerial['OF_ID'])){//Object based Article
					$this->DB_WE->Record['articleIsObject'] = 1;
					foreach($strSerial as $key => &$value){
						if(!is_numeric($key) && $key != 'we_sacf' && $key != 'WE_VARIANT' && (strpos($key, 'we_') !== false) && (strpos($key, 'we_wedoc') === false) && (strpos($key, 'we_WE') === false)){
							$this->DB_WE->Record[substr($key, 3)] = $value;
						}
					}
					unset($value);
					foreach($strSerial['we_sacf'] as $key => &$value){
						$this->DB_WE->Record[$key] = $value;
					}
					unset($value);
					$this->DB_WE->Record["shopvat"] = $strSerial["shopvat"];
				} else{//Document based Article
					$this->DB_WE->Record['articleIsObject'] = 0;
					foreach($strSerial as $key => &$value){
						if($key != 'we_sacf' && $key != 'Charset' && $key != 'WE_VARIANT' && strpos($key, 'wedoc_') === false){
							$this->DB_WE->Record[$key] = $value;
						}
					}
					unset($value);
					foreach($strSerial['we_sacf'] as $key => &$value){
						$this->DB_WE->Record[$key] = $value;
					}
					unset($value);
					$this->DB_WE->Record['VARIANT'] = $strSerial['WE_VARIANT'];
					$this->DB_WE->Record["shopvat"] = $strSerial["shopvat"];
				}
			}

			$this->DB_WE->Record["wedoc_Path"] = $this->Path . "?we_orderid=" . $this->DB_WE->Record["OrderID"] . "&we_orderitemid=" . $this->DB_WE->Record["ID"];
			$this->DB_WE->Record["WE_PATH"] = $this->Path . "?we_orderid=" . $this->DB_WE->Record["OrderID"] . "&we_orderitemid=" . $this->DB_WE->Record["ID"];
			$this->DB_WE->Record["WE_TEXT"] = $this->DB_WE->Record["ID"];
			$this->DB_WE->Record["WE_ID"] = $this->DB_WE->Record["ID"];
			$this->DB_WE->Record["we_wedoc_lastPath"] = $this->LastDocPath . "?we_orderid=" . $this->DB_WE->Record["OrderID"] . "&we_orderitemid=" . $this->DB_WE->Record["ID"];
			$this->count++;
			return true;
		} else{
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
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
		return $this->DB_WE->f($key);
	}

}
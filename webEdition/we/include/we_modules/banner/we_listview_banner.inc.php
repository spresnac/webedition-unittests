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
 * class    we_listview_banner
 * @desc    class for tag <we:listview type="banner">
 *
 */
class we_listview_banner extends listviewBase{

	var $ClassName = __CLASS__;
	var $allviews = 0;
	var $allclicks = 0;
	var $UseFilter = 0;
	var $FilterDate = 0;
	var $FilterDateEnd = 0;
	var $docs;

	/**
	 * @desc    constructor of class
	 *
	 * @param   name          string - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   order         string - field name(s) to order by
	 * @param   bannerID      integer - ID of banner
	 * @param   UseFilter     integer - 0 or 1
	 * @param   FilterDate    integer - Unix Timestamp
	 * @param   FilterDateEnd integer - Unix Timestamp
	 *
	 */
	function __construct($name = "0", $rows = 999999, $order = "views DESC", $bannerID = "", $UseFilter = false, $FilterDate = 0, $FilterDateEnd = 0){
		parent::__construct($name, $rows, 0, $order);

		$this->bannerID = $bannerID;
		$this->UseFilter = $UseFilter;
		$this->FilterDate = $FilterDate;
		$this->FilterDateEnd = $FilterDateEnd;
		$this->allviews = 0;
		$this->allclicks = 0;
		$this->count = $this->start;
		$this->docs = array();

		$tempArray = array();
		$tempArray2 = array();


		$ord = stripos($this->order, "views") === 0 ? "ORDER BY " . $this->order : "";
		$this->DB_WE->query('SELECT DID, COUNT( ID )  AS views FROM ' . BANNER_VIEWS_TABLE . " WHERE DID != 0 AND (Page='' OR page='0') AND ID=" . intval($this->bannerID) . " " . ($this->UseFilter ? " AND (Timestamp>='" . $this->FilterDate . "' AND Timestamp<'" . ($this->FilterDateEnd) . "')" : "") . " GROUP  BY DID");
		while($this->DB_WE->next_record()) {
			$tempArray[$this->DB_WE->f("DID")] = array(
				"views" => $this->DB_WE->f("views")
			);
			$this->allviews += intval($this->DB_WE->f("views"));
		}

		$this->DB_WE->query('SELECT DID, COUNT( ID )  AS clicks FROM ' . BANNER_CLICKS_TABLE . " WHERE DID != 0 AND (Page='' OR page='0') AND ID=" . intval($this->bannerID) . ' ' . ($this->UseFilter ? " AND (Timestamp>='" . $this->FilterDate . "' AND Timestamp<'" . ($this->FilterDateEnd) . "')" : "") . " GROUP  BY DID");
		while($this->DB_WE->next_record()) {
			$tempArray[$this->DB_WE->f("DID")]["clicks"] = $this->DB_WE->f("clicks");
			$this->allclicks += intval($this->DB_WE->f("clicks"));
		}

		$this->DB_WE->query('SELECT Page, COUNT( ID )  AS views FROM ' . BANNER_VIEWS_TABLE . " WHERE  Page != '' AND Page != '0' AND ID=" . intval($this->bannerID) . " " . ($this->UseFilter ? " AND (Timestamp>='" . $this->FilterDate . "' AND Timestamp<'" . ($this->FilterDateEnd) . "')" : "") . " GROUP  BY Page");
		while($this->DB_WE->next_record()) {

			$tempArray2[$this->DB_WE->f("Page")] = array(
				"views" => $this->DB_WE->f("views")
			);
			$this->allviews += intval($this->DB_WE->f("views"));
		}
		$this->DB_WE->query('SELECT Page, COUNT( ID )  AS clicks FROM ' . BANNER_CLICKS_TABLE . " WHERE  Page != '' AND Page != '0' AND ID=" . intval($this->bannerID) . " " . ($this->UseFilter ? " AND (Timestamp>='" . $this->FilterDate . "' AND Timestamp<'" . ($this->FilterDateEnd) . "')" : "") . " GROUP  BY Page");
		while($this->DB_WE->next_record()) {
			$tempArray2[$this->DB_WE->f("Page")]["clicks"] = $this->DB_WE->f("clicks");
			$this->allclicks += intval($this->DB_WE->f("clicks"));
		}

		// correct views entry on main banner table
		$allviews = f('SELECT COUNT(ID) AS views FROM ' . BANNER_VIEWS_TABLE . ' WHERE ID=' . intval($this->bannerID), "views", $this->DB_WE);
		$this->DB_WE->query('UPDATE ' . BANNER_TABLE . ' SET views=' . $allviews . ' WHERE ID=' . intval($this->bannerID));


		foreach($tempArray as $did => $vals){
			$this->docs[] = array("did" => $did, "views" => (isset($vals["views"]) ? $vals["views"] : 0), "clicks" => (isset($vals["clicks"]) ? $vals["clicks"] : 0), "page" => "");
		}

		foreach($tempArray2 as $page => $vals){
			$this->docs[] = array("did" => 0, "views" => isset($vals["views"]) ? $vals["views"] : 0, "clicks" => isset($vals["clicks"]) ? $vals["clicks"] : 0, "page" => $page);
		}

		if(stripos("path", $this->order) === 0){
			usort($this->docs, (preg_match("|^path +desc|i", $this->order) ? "we_sort_banners_path_desc" : "we_sort_banners_path"));
		} else if(stripos("clicks", $this->order) === 0){
			usort($this->docs, (preg_match("|^clicks +desc|i", $this->order) ? "we_sort_banners_clicks_desc" : "we_sort_banners_clicks"));
		} else if(stripos("views", $this->order) === 0){
			usort($this->docs, (preg_match("|^views +desc|i", $this->order) ? "we_sort_banners_views_desc" : "we_sort_banners_views"));
		} else if(stripos("rate", $this->order) === 0){
			usort($this->docs, (preg_match("|^rate +desc|i", $this->order) ? "we_sort_banners_rate_desc" : "we_sort_banners_rate"));
		}
		$this->anz_all = count($this->docs);
		$this->anz = min($this->rows, $this->anz_all - $this->start);
	}

	function next_record(){
		if($this->count >= min($this->start + $this->rows, $this->anz_all)){
			return false;
		}
		$id = intval($this->docs[$this->count]["did"]);
		$path = $id ? id_to_path($id, FILE_TABLE) : $this->docs[$this->count]["page"];
		$this->Record["WE_PATH"] = $this->Record["path"] = $path;
		$this->Record["WE_ID"] = $this->Record["id"] = $id;
		$this->Record["views"] = abs($this->docs[$this->count]["views"]);
		$this->Record["page"] = $this->docs[$this->count]["page"];
		$this->Record["clicks"] = abs($this->docs[$this->count]["clicks"]);
		$this->Record["rate"] = round($this->Record["views"] ? (100 * ($this->Record["clicks"] / $this->Record["views"])) : 0, 1);
		$this->count++;
		return true;
	}

	function f($key){
		return $this->Record[$key];
	}

	function getAllviews(){
		return intval($this->allviews);
	}

	function getAllclicks(){
		return intval($this->allclicks);
	}

	function getAllrate(){
		return round($this->getAllviews() ? (100 * ($this->getAllclicks() / $this->getAllviews())) : 0, 1);
	}

}

function we_sort_banners_path($a, $b){
	$aa = $a["did"] ? id_to_path($a["did"], FILE_TABLE) : $a["page"];
	$bb = $b["did"] ? id_to_path($b["did"], FILE_TABLE) : $b["page"];
	return strcmp($aa, $bb);
}

function we_sort_banners_path_desc($a, $b){
	return we_sort_banners_path($a, $b) * -1;
}

function we_sort_banners_clicks($a, $b){
	if(intval($a["clicks"]) == intval($b["clicks"])){
		return 0;
	}
	return (intval($a["clicks"]) > intval($b["clicks"])) ? 1 : -1;
}

function we_sort_banners_clicks_desc($a, $b){
	return we_sort_banners_clicks($a, $b) * -1;
}

function we_sort_banners_views($a, $b){
	if(intval($a["views"]) == intval($b["views"])){
		return 0;
	}
	return (intval($a["views"]) > intval($b["views"])) ? 1 : -1;
}

function we_sort_banners_views_desc($a, $b){
	return we_sort_banners_views($a, $b) * -1;
}

function we_sort_banners_rate($a, $b){
	$rate_a = round($a["views"] ? (100 * ($a["clicks"] / $a["views"])) : 0, 1);
	$rate_b = round($b["views"] ? (100 * ($b["clicks"] / $b["views"])) : 0, 1);
	if($rate_a == $rate_b){
		return 0;
	}
	return ($rate_a > $rate_b) ? 1 : -1;
}

function we_sort_banners_rate_desc($a, $b){
	return we_sort_banners_rate($a, $b) * -1;
}

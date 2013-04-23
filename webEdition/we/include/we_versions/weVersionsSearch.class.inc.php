<?php
/**
 * webEdition CMS
 *
 * $Rev: 3955 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 21:13:34 +0100 (Tue, 07 Feb 2012) $
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


class weVersionsSearch {

	public $db;

	public $searchFields = array();

	public $location = array();

	public $search = array();

	public $mode = 0;

	public $order = "version DESC";

	public $anzahl = 25;

	public $searchstart = 0;

	public $height = 1;

	public $where;

	public $version;


	/**
	*  Constructor for class 'weVersionsSearch'
	*/
	function __construct() {

	  $this->db = new DB_WE();
	  $this->version=new weVersions();

	}

	/**
	* @abstract initialize data from $_REQUEST
	*/
	function initData(){

		if (isset( $_REQUEST["mode"] ) ) {
			$this->mode = ($_REQUEST["mode"]);
		}
		if (isset( $_REQUEST["order"] ) ) {
			$this->order = ($_REQUEST["order"]);
		}
		if (isset( $_REQUEST["anzahl"] ) ) {
			$this->anzahl = ($_REQUEST["anzahl"]);
		}
		if (isset($_REQUEST["searchFields"])) {
			$this->searchFields = ($_REQUEST["searchFields"]);
			$this->height = count($this->searchFields);
		} elseif (!isset($_REQUEST["searchFields"]) && isset($_REQUEST["searchstart"])) {
			$this->height = 0;
		}
		if (isset( $_REQUEST["location"] ) ) {
			$this->location = ($_REQUEST["location"]);
		}
		if (isset($_REQUEST["search"])) {
			$this->search = ($_REQUEST["search"]);
		}

	}


	/**
	* @abstract make WHERE-Statement for mysql-SELECT
	*/
	function getWhere() {

		$where = "";
		$modConst = array();

		if(($this->mode!=0) || (isset($_REQUEST['we_cmd']["mode"]) && $_REQUEST['we_cmd']["mode"]!=0)) {

			foreach ($_REQUEST['we_cmd'] as $k => $v) {
				if (stristr($k, 'searchFields[')) {
				  $_REQUEST['searchFields'][] = $v;
				}
				if (stristr($k, 'location[')) {
				  $_REQUEST['location'][] = $v;
				}
				if (stristr($k, 'search[')) {
				  $_REQUEST['search'][] = $v;
				}
			}
			if(isset($_REQUEST['searchFields'])) {
				foreach($_REQUEST['searchFields'] as $k => $v) {
					if(isset($_REQUEST['searchFields'][$k])) {
						if($v=="modifierID") {
							if(isset($_REQUEST['search'][$k])) {
								$where .= " AND ".$v." = '".escape_sql_query($_REQUEST['search'][$k])."'";
							}
						}
						if($v=="status") {
							if(isset($_REQUEST['search'][$k])) {
								$where .= " AND ".$v." = '".escape_sql_query($_REQUEST['search'][$k])."'";
							}
						}
						if($v=="timestamp") {
							 if ($_REQUEST['location'][$k] && isset($_REQUEST['search'][$k]) && $_REQUEST['search'][$k]!="") {

							 	  $date = explode(".", $_REQUEST['search'][$k]);
                                  $day = $date[0];
                                  $month = $date[1];
                                  $year = $date[2];
                                  $timestampStart = mktime(0, 0, 0, $month, $day, $year);
                                  $timestampEnd = mktime(23, 59, 59, $month, $day, $year);

                                  switch ($_REQUEST['location'][$k]) {
										case "IS":
											$where .= " AND ".$v." BETWEEN " . intval($timestampStart) . " AND " . intval($timestampEnd);
										break;
										case "<":
											$where .= " AND ".$v . $_REQUEST['location'][$k] . ' ' . intval($timestampStart);
										break;
										case "<=":
											$where .= " AND ".$v . $_REQUEST['location'][$k] . ' ' . intval($timestampEnd);
										break;
										case ">":
											$where .= " AND ".$v . $_REQUEST['location'][$k] . ' ' . intval($timestampEnd);
										break;
										case ">=":
											$where .= " AND ".$v . $_REQUEST['location'][$k] . ' ' . intval($timestampStart);
										break;
									}
                             }
						}
						if($v=="allModsIn") {
							if(isset($_REQUEST['search'][$k])) {
								$modConst[] = $this->version->modFields[$_REQUEST['search'][$k]]['const'];
							}
						}
					}
				}
				if(!empty($modConst)) {
					$modifications = array();
					$ids = array();
					$_ids = array();
					$query = "SELECT ID, modifications FROM " . VERSIONS_TABLE . " WHERE modifications != '' ";
					$this->db->query($query);

					while ($this->db->next_record()) {
						$modifications[$this->db->f('ID')] = makeArrayFromCSV($this->db->f('modifications'));
					}
					$m = 0;
					foreach ($modConst as $k => $v) {
						foreach ($modifications as $key => $val) {
							if(in_array($v,$modifications[$key])) {
								$ids[$m][] = $key;

							}
						}$m++;
					}

					if(!empty($ids)) {
						foreach($ids as $key=>$val) {
							$_ids[] = $val;
						}
						$arr = array();
						if(!empty($_ids[0])) {
							//more then one field
							$mtof = false;
							foreach($_ids as $k=>$v) {
								if($k != 0) {
									$mtof = true;
									foreach($v as $key=>$val) {
										if(!in_array($val,$_ids[0])) {
											unset($_ids[0][$val]);
										}
										else {
											$arr[] = $val;
										}
									}
								}
							}
							if($mtof) {
								$where .= " AND ID IN (".makeCSVFromArray($arr).") ";
							}
							elseif(!empty($_ids[0])) {
								$where .= " AND ID IN (".makeCSVFromArray($_ids[0]).") ";
							}
							else {
								$where .= " AND 0";
							}
						}
					}
					else {
						$where .= " AND 0";
					}
				}
			}

		}

		return $where;

	}


	/**
	* @abstract get modification-fields for filter-SELECT
	* @return array of modification-fields
	*/
	function getModFields() {

		$modFields = array();

		foreach($this->version->modFields as $k => $v) {
			if($k!="status") {
				$modFields[$k] = g_l('versions','['.$k.']');
			}
		}

		return $modFields;

	}


	/**
	* @abstract get filter categories for filter-SELECT
	* @return array of filter categories
	*/
	function getFields() {

		$tableFields = array(
			'allModsIn' => g_l('versions','[allModsIn]'),
			'timestamp' => g_l('versions','[modTime]'),
			'modifierID' => g_l('versions','[modUser]'),
			'status' => g_l('versions','[status]')
		);

		return $tableFields;
	}


	/**
	* @abstract get location for filter-SELECT
	* @return array of filter locations
	*/
	function getLocation($whichFilterCategory = "")	{

		$locations = array(
				'CONTAIN' => g_l('searchtool','[CONTAIN]'),
				'IS' => g_l('searchtool','[IS]'),
				'START' => g_l('searchtool','[START]'),
				'END' => g_l('searchtool','[END]'),
				'<' => g_l('searchtool','[<]'),
				'<=' => g_l('searchtool','[<=]'),
				'>=' => g_l('searchtool','[>=]'),
				'>' => g_l('searchtool','[>]'));

		if ($whichFilterCategory == "date") {
			unset($locations["CONTAIN"]);
			unset($locations["START"]);
			unset($locations["END"]);
		}

		return $locations;

	}


	/**
	* @abstract get all user for filter-SELECT in category 'modifierID'
	* @return array of users
	*/
	function getUsers() {

		$_db = new DB_WE();
		$vals = array();

		$_db->query("SELECT ID, Text FROM " . USER_TABLE );
		while ($_db->next_record()) {
			$v = $_db->f("ID");
			$t = $_db->f("Text");
			$vals[$v] = $t;
		}

		return $vals;

	}

	/**
	* @abstract get status
	* @return array of stats
	*/
	function getStats() {

		$vals = array();

		$vals["published"] = g_l('versions','[published]');
		$vals["unpublished"] = g_l('versions','[unpublished]');
		$vals["saved"] = g_l('versions','[saved]');
		$vals["deleted"] = g_l('versions','[deleted]');

		return $vals;

	}


	/**
	* @abstract get code for calendar
	* @return html-code for calendar
	*/
	function getDateSelector($_label, $_name, $_btn, $value) {

		$btnDatePicker = we_button::create_button("image:date_picker", "javascript:", null, null, null, null, null, null, false, $_btn);

		$oSelector = new we_html_table(array("cellpadding" => "0", "cellspacing" => "0", "border" => "0", "id" => $_name . "_cell"), 1, 5);
		$oSelector->setCol(0, 2, null, we_html_tools::htmlTextInput($name = $_name, $size = 55, $value, $maxlength = 10, $attribs = 'id="' . $_name . '" class="wetextinput" readonly="1"', $type = "text", $width = 100));
		$oSelector->setCol(0, 3, null, "&nbsp;");
		$oSelector->setCol(0, 4, null, we_html_element::htmlA(array("href" => "#"), $btnDatePicker));

		return $oSelector->getHTML();

	}

}
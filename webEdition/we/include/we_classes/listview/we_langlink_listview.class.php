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
 * class    we_listview
 * @desc    class for tag <we:listview>
 *
 */
class we_langlink_listview extends listviewBase{

	var $docType = ""; /* doctype string */
	var $IDs = array(); /* array of ids with pages which are found */
	var $foundlinks = array();
	var $ClassName = __CLASS__;
	var $linkType = '';
	var $searchable = true;
	var $condition = ''; /* condition string (like SQL) */
	var $defaultCondition = '';
	var $customerFilterType = 'off'; // shall we control customer-filter?
	var $subfolders = true; // regard subfolders
	var $customers = "";
	var $languages = ""; //string of Languages, separated by ,
	var $numorder = false; // #3846
	var $objectseourls = false;
	var $hidedirindex = false;
	var $pagelanguage = ''; //assumed pagelanguage: choosen by using attribut pagelanguage
	var $ownlanguage = ''; // effective language of document/object
	var $dirsearchtable = '';
	var $showself = false;

	/**
	 * we_listview()
	 * constructor of class
	 *
	 * @param   name          string  - name of listview
	 * @param   rows          integer - number of rows to display per page
	 * @param   offset        integer - start offset of first page
	 * @param   order         string  - field name(s) to order by
	 * @param   desc          boolean - set to true, if order should be descendend
	 * @param   workspaceID   string - commaseperated list of id's of workspace
	 * @param   contentTypes  string  - contenttypes of documents (image,text ...)
	 * @param   cols   		  integer - to display a table this is the number of cols
	 * @param   searchable 	  boolean - if false then show also documents which are not marked as searchable
	 * @return we_listview
	 */
	function __construct($name = "0", $rows = 999999999, $offset = 0, $order = "", $desc = false, $linkType = 'tblFile', $cols = "", $seeMode = true, $searchable = true, $customerFilterType = 'off', $showself = false, $id = "", $pagelanguage = "", $ownlanguage = "", $hidedirindex = false, $objectseourls = false){
		$id = intval($id);
		parent::__construct($name, $rows, $offset, $order, $desc, '', false, '', $cols, '', '', '', '', '', 'off', $id);

		$this->showself = $showself;
		$this->objectseourls = $objectseourls;
		$this->hidedirindex = $hidedirindex;
		$this->id = $id;
		$this->pagelanguage = $pagelanguage;
		$this->ownlanguage = $ownlanguage;
		$this->linkType = $linkType;

		$_languages = getWeFrontendLanguagesForBackend();

		// if !$showself: remove the pagelanguage (choosen by using attribute pagelanguage) from the languages-list, so the link to this language won't be found in DB
		if(isset($_languages[$this->pagelanguage]) && !$showself){
			unset($_languages[$this->pagelanguage]);
		}

		if(stripos($this->order, " desc") !== false){//was #3849
			$this->order = str_ireplace(" desc", "", $this->order);
			$this->desc = true;
		}

		$this->order = trim($this->order);

		$orderstring = $this->order ? (' ORDER BY ' . LANGLINK_TABLE . '.' . $this->order . ($this->desc ? ' DESC' : '')) : '';
		if($this->order == 'Locale'){
			if($this->desc){
				krsort($_languages);
			} else{
				ksort($_languages);
			}
		}

		if($this->id && ($this->linkType == 'tblFile' || $this->linkType == 'tblObjectFile')){
			foreach($_languages as $langkey => $lang){
				if($this->linkType == 'tblFile'){
					$q = 'SELECT ' . LANGLINK_TABLE . '.DID as DID, ' . LANGLINK_TABLE . '.DLocale as DLocale, ' . LANGLINK_TABLE . '.LDID as LDID, ' . LANGLINK_TABLE . '.Locale as Locale, ' . LANGLINK_TABLE . '.IsFolder as IsFolder, ' . LANGLINK_TABLE . ".IsObject as IsObject, " . LANGLINK_TABLE . ".DocumentTable as DocumentTable, " . FILE_TABLE . ".Path as Path, " . FILE_TABLE . ".ParentID as ParentID  FROM " . LANGLINK_TABLE . "," . FILE_TABLE . " WHERE " . LANGLINK_TABLE . ".Locale='" . $langkey . "' AND " . LANGLINK_TABLE . ".LDID = " . FILE_TABLE . '.ID AND ' . FILE_TABLE . ".Published >0 AND " . LANGLINK_TABLE . ".DocumentTable='" . $this->linkType . "' AND " . LANGLINK_TABLE . '.DID=' . $this->id;
					$this->dirsearchtable = FILE_TABLE;
				} else{
					$q = "SELECT " . LANGLINK_TABLE . ".DID as DID, " . LANGLINK_TABLE . ".DLocale as DLocale, " . LANGLINK_TABLE . ".LDID as LDID, " . LANGLINK_TABLE . ".Locale as Locale, " . LANGLINK_TABLE . ".IsFolder as IsFolder, " . LANGLINK_TABLE . ".IsObject as IsObject, " . LANGLINK_TABLE . ".DocumentTable as DocumentTable, " . OBJECT_FILES_TABLE . ".Path as Path, " . OBJECT_FILES_TABLE . ".Url as Url, " . OBJECT_FILES_TABLE . ".TriggerID as TriggerID  FROM " . LANGLINK_TABLE . "," . OBJECT_FILES_TABLE . " WHERE " . LANGLINK_TABLE . ".Locale='" . $langkey . "' AND " . LANGLINK_TABLE . ".LDID = " . OBJECT_FILES_TABLE . ".ID AND " . OBJECT_FILES_TABLE . ".Published >0 AND " . LANGLINK_TABLE . ".DocumentTable='" . $this->linkType . "' AND " . LANGLINK_TABLE . '.DID=' . $this->id;
					$this->dirsearchtable = OBJECT_FILES_TABLE;
				}

				$this->DB_WE->query($q);
				$found = $this->DB_WE->num_rows();
				if($found){
					$this->DB_WE->next_record();
					$this->foundlinks[$this->DB_WE->Record['Locale']] = $this->DB_WE->Record;
				} else{
					$this->getParentData($this->id, $langkey);
				}
			}

			// Links to documents/objects themselves are not listet in tblLangLink.
			// In the following cases we must create them manually:
			// if($this->showself == true)
			// if($this->showself == false && $this->pagelanguage != $this->ownlanguage)
			if($this->showself || (!$this->showself && $this->pagelanguage != $this->ownlanguage)){
				$dt = array('DID' => $this->id, 'DLocale' => $this->ownlanguage, 'LDID' => $this->id, 'Locale' => $this->ownlanguage, 'DocumentTable' => (($this->linkType == 'tblFile') ? 'tblFile' : 'tblObjectFile'), 'IsObject' => (($this->linkType == 'tblFile') ? '0' : '1'), 'IsFolder' => 0);
				if($this->linkType == 'tblFile'){
					$dt['Path'] = id_to_path($this->id, FILE_TABLE);
				} else{
					$dt['Path'] = id_to_path($this->id, OBJECT_FILES_TABLE);
					$row = getHash("SELECT Url, TriggerID FROM " . OBJECT_FILES_TABLE . " WHERE ID=" . intval($this->id), $this->DB_WE);
					$dt['Url'] = $row['Url'];
					$dt['TriggerID'] = $row['TriggerID'];
				}
				$this->foundlinks[$this->ownlanguage] = $dt;
			}

			// sort array
			if($this->order == "random()"){
				shuffle($this->foundlinks);
			} else if($this->order == "Locale"){
				if($this->desc){
					krsort($this->foundlinks);
				} else{
					ksort($this->foundlinks);
				}
			} else if(strpos($this->order, '_') > 0){ //csv: ordered list of locales
				$orderArr = explode(',', trim($this->order));
				$orderedLinks = array();
				foreach($orderArr as $orderLocale){
					if(isset($this->foundlinks[$orderLocale])){
						$orderedLinks[$orderLocale] = $this->foundlinks[$orderLocale];
						unset($this->foundlinks[$orderLocale]);
					}
				}
				$sortFn = 'ksort';
				if($this->desc){
					$sortFn = 'krsort';
				}
				$sortFn($this->foundlinks);
				$this->foundlinks = array_merge($orderedLinks, $this->foundlinks);
			}

			// to go on with $this->foundlinks it must not be associative!
			$tmpFoundlinks = array();
			foreach($this->foundlinks as $foundlink){
				$tmpFoundlinks[] = $foundlink;
			}
			$this->foundlinks = $tmpFoundlinks;

			$this->anz_all = count($this->foundlinks);
			$this->anz = $this->anz_all;
			$this->count = 0;
		} else{
			$this->anz_all = 0;
			$this->anz = $this->anz_all;
			$this->count = 0;
		}
	}

	// Links to documents/objects themselves never use this method: so we do not need to fix the showself-problem in this section
	function getParentData($myid, $langkey){
		$pid = f('SELECT ParentID FROM ' . $this->dirsearchtable . ' WHERE ID=' . intval($myid), 'ParentID', $this->DB_WE);

		if($pid){
			//FIXME: this query is identical - what should that be good for?!
			$q = ($this->linkType == 'tblFile' ?
					"SELECT " . LANGLINK_TABLE . ".DID as DID, " . LANGLINK_TABLE . ".DLocale as DLocale, " . LANGLINK_TABLE . ".LDID as LDID, " . LANGLINK_TABLE . ".Locale as Locale, " . LANGLINK_TABLE . ".IsFolder as IsFolder, " . LANGLINK_TABLE . ".IsObject as IsObject, " . LANGLINK_TABLE . ".DocumentTable as DocumentTable, " . FILE_TABLE . ".Path as Path, " . FILE_TABLE . ".ParentID as ParentID  FROM " . LANGLINK_TABLE . "," . FILE_TABLE . " WHERE " . LANGLINK_TABLE . ".Locale='" . $langkey . "' AND " . LANGLINK_TABLE . ".LDID = " . FILE_TABLE . ".ID AND " . FILE_TABLE . ".Published >0 AND " . LANGLINK_TABLE . ".DocumentTable='tblFile' AND " . LANGLINK_TABLE . ".DID='" . $pid . "'" :
					"SELECT " . LANGLINK_TABLE . ".DID as DID, " . LANGLINK_TABLE . ".DLocale as DLocale, " . LANGLINK_TABLE . ".LDID as LDID, " . LANGLINK_TABLE . ".Locale as Locale, " . LANGLINK_TABLE . ".IsFolder as IsFolder, " . LANGLINK_TABLE . ".IsObject as IsObject, " . LANGLINK_TABLE . ".DocumentTable as DocumentTable, " . FILE_TABLE . ".Path as Path, " . FILE_TABLE . ".ParentID as ParentID  FROM " . LANGLINK_TABLE . "," . FILE_TABLE . " WHERE " . LANGLINK_TABLE . ".Locale='" . $langkey . "' AND " . LANGLINK_TABLE . ".LDID = " . FILE_TABLE . ".ID AND " . FILE_TABLE . ".Published >0 AND " . LANGLINK_TABLE . ".DocumentTable='tblFile' AND " . LANGLINK_TABLE . ".DID='" . $pid . "'");

			$this->DB_WE->query($q);
			$found = $this->DB_WE->num_rows();
			if($found){
				$this->DB_WE->next_record();
				$this->foundlinks[] = $this->DB_WE->Record;
			} else{
				$this->getParentData($pid, $langkey);
			}
		}
	}

	function next_record(){
		if($this->count < $this->anz_all){
			$count = $this->count;
			$this->Record["WE_LANG"] = $this->pagelanguage;
			$this->Record["WE_ID"] = $this->foundlinks[$count]["LDID"];
			$this->Record["WE_DOCUMENTLOCALE"] = $this->foundlinks[$count]["DLocale"];
			$dLocale = explode('_', $this->foundlinks[$count]["DLocale"]);
			$this->Record["WE_DOCUMENTCOUNTRY"] = isset($dLocale[1]) ? $dLocale[1] : '';
			$this->Record["WE_DOCUMENTLANGUAGE"] = $dLocale[0];
			$this->Record["WE_TARGETLOCALE"] = $this->foundlinks[$count]["Locale"];
			$Locale = explode('_', $this->foundlinks[$count]["Locale"]);
			$this->Record["WE_TARGETCOUNTRY"] = isset($Locale[1]) ? $Locale[1] : '';
			$this->Record["WE_TARGETLANGUAGE"] = isset($Locale[0]) ? $Locale[0] : '';

			//added for #7084
			$this->Record['WE_TARGETLANGUAGE_NAME'] = Zend_Locale::getTranslation($this->Record["WE_TARGETLANGUAGE"], 'language', $this->Record["WE_TARGETLANGUAGE"]);
			$this->Record['WE_TARGETCOUNTRY_NAME'] = Zend_Locale::getTranslation($this->Record["WE_TARGETCOUNTRY"], 'country', $this->Record["WE_TARGETCOUNTRY"]);

			if($this->foundlinks[$count]['DocumentTable'] == 'tblFile'){
				$this->Record["WE_PATH"] = $this->foundlinks[$count]["Path"];
			} else{

				$path_parts = pathinfo(
					(isset($this->foundlinks[$count]['TriggerID']) && $this->foundlinks[$count]['TriggerID'] ?
						id_to_path($this->foundlinks[$count]['TriggerID']) : $_SERVER['SCRIPT_NAME'])
				);

				$paramName = "we_object_ID";

				if($this->objectseourls && $this->foundlinks[$count]['Url'] != '' && show_SeoLinks()){
					if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$this->Record["WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $this->foundlinks[$count]['Url'];
					} else{
						$this->Record["WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . $path_parts['filename'] . '/' . $this->foundlinks[$count]['Url'];
					}
				} else{
					if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$this->Record["WE_PATH"] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . "?$paramName=" . $this->Record["WE_ID"];
					} else{
						$this->Record["WE_PATH"] = $_SERVER['SCRIPT_NAME'] . "?$paramName=" . $this->Record["WE_ID"];
					}
				}
			}
			$this->Record["Path"] = $this->Record["WE_PATH"];

			$this->Record["ID"] = $this->Record["WE_ID"];

			$this->count++;
			return true;
		} else{
			$this->stop_next_row = $this->shouldPrintEndTR();
			if($this->cols && ($this->count <= $this->maxItemsPerPage) && !$this->stop_next_row){
				$this->Record = array();
				$this->Record["WE_PATH"] = "";
				$this->Record["WE_TEXT"] = "";
				$this->Record["WE_ID"] = "";
				$this->count++;
				return true;
			}
		}
		return false;
	}

	function f($key){
		return isset($this->Record[$key]) ? $this->Record[$key] : "";
	}

}
<?php

/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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
 * General Definition of WebEdition Voting
 *
 */
class weVotingList{

	//properties
	var $Name;
	var $Version;
	var $Offset = 0;
	var $Start = 0;
	var $CountAll = 0;

	/**
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 */
	function weVotingList($name, $groupid, $version = 0, $rows = 0, $offset = 0, $desc = false, $order = 'PublishDate', $subgroup = false){

		$this->Name = $name;
		$this->Version = $version;
		$this->Offset = $offset;
		$this->Rows = $rows;
		$this->Start = (isset($_REQUEST["_we_vl_start_" . $this->Name]) && $_REQUEST["_we_vl_start_" . $this->Name]) ? abs($_REQUEST["_we_vl_start_" . $this->Name]) : 0;
		if($this->Start == 0)
			$this->Start += $offset;

		$childs_query = '';
		if($groupid != 0){
			$childs_query = '(ParentID=' . intval($groupid);
			if($subgroup){
				$childs = array();
				we_readChilds($groupid, $childs, VOTING_TABLE, true, '', 'IsFolder', 1);
				$childs_query .= ' OR ParentID=' . implode(' OR ParentID=', $childs);
			}
			$childs_query .= ')';
		}

		if($rows || $this->Start)
			$limit = ' LIMIT ' . $this->Start . ',' . ($rows == 0 ? 9999999 : $rows);
		else
			$limit = '';

		if($order != ""){
			$order_sql = ' ORDER BY ' . $order;
			if($desc){
				$order_sql .= ' DESC ';
			} else{
				$order_sql .= ' ASC ';
			}
		}

		$this->db = new DB_WE();


		$this->CountAll = f('SELECT count(ID) as CountAll FROM ' . VOTING_TABLE . ' WHERE IsFolder=0 ' . (!empty($childs_query) ? ' AND ' . $childs_query : '') . $order_sql . ';', 'CountAll', $this->db);
		$_we_voting_query = 'SELECT ID FROM ' . VOTING_TABLE . ' WHERE IsFolder=0 ' . (!empty($childs_query) ? ' AND ' . $childs_query : '') . $order_sql . $limit . ';';

		$this->db->query($_we_voting_query);
	}

	function getNext(){

		if($this->db->next_record()){
			$GLOBALS['_we_voting'] = new weVoting($this->db->f('ID'));
			$GLOBALS['_we_voting']->setDefVersion($this->Version);
			return true;
		}
		return false;
	}

	function getNextLink($attribs){
		if($this->hasNextPage()){
			$urlID = weTag_getAttribute("id", $attribs);
			$foo = $this->Start + $this->Rows;
			$attribs["href"] = we_tag('url', array('id' => ($urlID ? $urlID : 'self'), 'hidedirindex' => 'false')) . '?' . oldHtmlspecialchars(listviewBase::we_makeQueryString("_we_vl_start_" . $this->Name . "=$foo"));

			return getHtmlTag("a", $attribs, "", false, true);
		} else{
			return "";
		}
	}

	function hasNextPage(){
		return (($this->Start + $this->Rows) < $this->CountAll);
	}

	function getBackLink($attribs){
		if($this->hasPrevPage()){
			$urlID = weTag_getAttribute("id", $attribs);
			$foo = $this->Start - $this->Rows;
			$attribs["href"] = we_tag('url', array('id' => ($urlID ? $urlID : 'self'), 'hidedirindex' => 'false')) . '?' . oldHtmlspecialchars(listviewBase::we_makeQueryString("_we_vl_start_" . $this->Name . "=$foo"));

			return getHtmlTag("a", $attribs, "", false, true);
		} else{
			return "";
		}
	}

	function hasPrevPage(){
		return (abs($this->Start) != abs($this->Offset));
	}

}

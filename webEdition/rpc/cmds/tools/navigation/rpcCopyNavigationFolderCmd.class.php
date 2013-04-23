<?php

/**
 * webEdition CMS
 *
 * $Rev: 5158 $
 * $Author: mokraemer $
 * $Date: 2012-11-15 13:43:36 +0100 (Thu, 15 Nov 2012) $
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
 * @package    webEdition_rpc
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class rpcCopyNavigationFolderCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();
		if(isset($_REQUEST['we_cmd'][0]) && !empty($_REQUEST['we_cmd'][0]) &&
			isset($_REQUEST['we_cmd'][1]) && !empty($_REQUEST['we_cmd'][1]) &&
			isset($_REQUEST['we_cmd'][2]) && !empty($_REQUEST['we_cmd'][2]) &&
			isset($_REQUEST['we_cmd'][3]) && !empty($_REQUEST['we_cmd'][3]) &&
			(strpos($_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][0]) === false || strpos($_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][0]) > 0)
		){

			$db = new DB_WE();
			$query = "SELECT * FROM " . NAVIGATION_TABLE . " WHERE Path LIKE '" . $db->escape($_REQUEST['we_cmd'][2]) . "/%' ORDER BY Path";
			$db->query($query);
			$result = $db->getAll();
			$querySet = "";
			$query = "";
			$folders = array($_REQUEST['we_cmd'][1]);
			$mapedId = array($_REQUEST['we_cmd'][3] => $_REQUEST['we_cmd'][1]);
			foreach($result as $row){
				$querySet = '(';
				foreach($row as $key => $val){
					switch($key){
						case "ID" :
							$querySet .= "''";
							break;
						case "Path" :
							$path = str_replace($_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][0], $val);
							$querySet .= ", '" . $db->escape($path) . "'";
							break;
						case "ParentID" :
							$querySet .= ', ' . intval($mapedId[$val]);
							break;
						default :
							$querySet .= ", '$val'";
					}
				}
				$querySet .= ")";
				if($row['IsFolder']){
					if(!empty($query)){
						$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . $query);
					}
					$db->query('INSERT INTO ' . NAVIGATION_TABLE . ' VALUES ' . $querySet);
					$mapedId[$row['ID']] = $db->getInsertId();
					$folders[] = $mapedId[$row['ID']];
					$query = "";
				} else{
					if(!empty($query)){
						$query .= ', ';
					}
					$query .= $querySet;
				}
				$lastInserted = $row['IsFolder'];
			}
			if(!$lastInserted){
				$db->query("INSERT INTO " . NAVIGATION_TABLE . " VALUES " . $query);
			}
			foreach($folders as $folder){
				$newNavi = new weNavigation($folder);
				$newNavi->save();
			}
			$resp->setData("status", "ok");
			$resp->setData("folders", $folders);
		} else{
			$resp->setData("folders", "");
		}

		return $resp;
	}

}

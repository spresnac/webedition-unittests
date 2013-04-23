<?php

/**
 * webEdition CMS
 *
 * $Rev: 4798 $
 * $Author: mokraemer $
 * $Date: 2012-07-28 23:47:58 +0200 (Sat, 28 Jul 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");

class we_users_util{

	private static function getGroupList($id){
		$ret = array();
		if($id){
			$db_tmp = new DB_WE;
			$db_tmp->query("SELECT ID,username WHERE ParentID=" . intval($id) . " AND Type=1");
			while($db_tmp->next_record()) {
				$ret[$db_tmp->f("ID")] = $db_tmp->f("username");
				$section = array();
				$section = self::getGroupList($db_tmp->f("ID"));
				$ret = array_merge($ret, $section);
			}
		}
		return $ret;
	}

	private static function getUserTree($id){
		$ret = array();
		$db_tmp = new DB_WE;
		$db_tmp->query("SELECT ID,username,Type WHERE ParentID=" . intval($id));
		while($db_tmp->next_record()) {
			$ret[$db_tmp->f("ID")]["name"] = $db_tmp->f("username");
			$ret[$db_tmp->f("ID")]["ParentID"] = $id;
			$ret[$db_tmp->f("ID")]["Type"] = $db_tmp->f("Type");
			$section = array();
			$section = self::getUserTree($db_tmp->f("ID"));
			$ret = array_merge($ret, $section);
		}
		return $ret;
	}

	static function isUserInUsers($uid, $users){ // $users can be a csv string or an array
		if($_SESSION["perms"]["ADMINISTRATOR"])
			return true;
		if(!is_array($users)){
			$users = makeArrayFromCSV($users);
		}

		if(in_array($uid, $users)){
			return true;
		} else{
			$db = new DB_WE();

			$aliases = we_getAliases($uid, $db);
			foreach($aliases as $aid){
				if(in_array($aid, $users)){
					return true;
				}
			}

			foreach($users as $user){
				$isGroup = f("SELECT IsFolder FROM " . USER_TABLE . " WHERE ID=" . intval($user), "IsFolder", $db);
				if($isGroup){
					if(self::isUserInGroup($uid, $user)){
						return true;
					}
					foreach($aliases as $aid){
						if(self::isUserInGroup($aid, $user)){
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	static function isUserInGroup($uid, $groupID, $db = ""){
		if(!$db)
			$db = new DB_WE();
		$pid = f("SELECT ParentID FROM " . USER_TABLE . " WHERE ID=" . intval($uid), "ParentID", $db);
		if($pid == $groupID){
			return true;
		} else if($pid != 0){
			return self::isUserInGroup($pid, $groupID);
		} else{
			return false;
		}
	}

	static function addAllUsersAndGroups($uid, &$arr){
		$db = new DB_WE();
		$db->query("SELECT ID,IsFolder FROM " . USER_TABLE . " WHERE ParentID=" . intval($uid));
		while($db->next_record()) {
			array_push($arr, $db->f("ID"));
			if($db->f("IsFolder")){
				self::addAllUsersAndGroups($db->f("ID"), $arr);
			}
		}
	}

	static function removeNonAsociative(&$array){
		if(!is_array($array))
			return $array;

		reset($array);

		while(list($k) = each($array))
			if((string) (int) $k == $k)
				unset($array[$k]);

		return $array;
	}

	static function getUsersForDocWorkspace($id, $wsField = "workSpace"){

		$db = new DB_WE();
		if(is_array($id)){
			$ids = $id;
		} else{
			$ids = array($id);
		}

		$where = array();
		foreach($ids as $id){
			$where[] = $wsField . ' LIKE "%,' . $id . ',%"';
		}

		$out = array();

		$db->query('SELECT ID,username FROM ' . USER_TABLE . ' WHERE ' . implode(' OR ', $where));

		while($db->next_record()) {
			$out[$db->f('ID')] = $db->f('username');
		}

		return $out;
	}

}
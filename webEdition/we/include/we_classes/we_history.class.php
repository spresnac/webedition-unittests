<?php

/**
 * webEdition CMS
 *
 * $Rev: 5346 $
 * $Author: mokraemer $
 * $Date: 2012-12-12 14:02:37 +0100 (Wed, 12 Dec 2012) $
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
 * Class we_history
 *
 * Provides functions determined to handle a list of last modified files required by
 * the 'personalized desktop'.
 */
abstract class we_history{

	const MAX = 5;

	static function userHasPerms($creatorid, $owners, $restricted){
		if($_SESSION['perms']['ADMINISTRATOR']){
			return true;
		}
		if(!$restricted){
			return true;
		}
		if(we_isOwner($owners) || we_isOwner($creatorid)){
			return true;
		}
		return false;
	}

	static function insertIntoHistory(&$object, $action = 'save'){
		$db = new DB_WE();
		$table = $db->escape(stripTblPrefix($object->Table));
		$_username = isset($_SESSION['user']['Username']) ? $_SESSION['user']['Username'] : '';
		$cnt = f('SELECT COUNT(1) AS cnt FROM ' . HISTORY_TABLE . ' WHERE DID=' . intval($object->ID) . ' AND DocumentTable="' . $table . '"', 'cnt', $db);
		if($cnt > self::MAX){
			$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE DID=' . intval($object->ID) . ' AND DocumentTable="' . $table . '" ORDER BY ID LIMIT ' . ($cnt - self::MAX));
		}
		$db->query('INSERT INTO ' . HISTORY_TABLE . ' SET ' . we_database_base::arraySetter(array(
				'DID' => intval($object->ID),
				'DocumentTable' => $table,
				'ContentType' => $object->ContentType,
				'Act' => $action,
				'UserName' => $_username,
			)));
	}

	/**
	 * Deletes a model from navigation History
	 *
	 * @param array $modelIds
	 * @param string $table
	 */
	static function deleteFromHistory($modelIds, $table){
		$db = new DB_WE();
		$db->query('DELETE FROM ' . HISTORY_TABLE . ' WHERE DID IN (' . implode(', ', $modelIds) . ') AND DocumentTable = "' . stripTblPrefix($table) . '"');
	}

}

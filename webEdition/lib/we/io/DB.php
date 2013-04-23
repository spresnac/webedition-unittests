<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_io
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Base class for data base
 *
 * @category   we
 * @package    we_io
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_io_DB{

	/**
	 * dbInstance attribute
	 *
	 * @var NULL
	 */
	private static $dbInstance = NULL;

	/**
	 * create new adapter
	 *
	 * @return object
	 */
	static function newAdapter(){
		$DBpar = array('username' => DB_USER, 'password' => DB_PASSWORD, 'dbname' => DB_DATABASE);
		if(stripos(DB_HOST, ':') !== false){
			list($host, $port) = explode(':', DB_HOST);
			$DBpar['host'] = $host;
			$DBpar['port'] = $port;
		} else{
			$DBpar['host'] = DB_HOST;
		}
		$charset = we_database_base::getCharset();
		if($charset != ''){
			if(strpos(strtolower($charset), 'utf') !== false){// es gibt alte sites, da steht UTF-8 drin, was aber falsch ist
				$DBpar['charset'] = 'utf8';
			} else{
				$DBpar['charset'] = $charset;
			}
		} else{
			$DBpar['charset'] = 'utf8';
		}

		$db = Zend_Db::factory('Pdo_Mysql', $DBpar);
		return $db;
	}

	/**
	 * shared adapter
	 *
	 * @return object
	 */
	static function sharedAdapter(){
		if(self::$dbInstance === NULL){
			self::$dbInstance = self::newAdapter();
		}
		return self::$dbInstance;
	}

	/**
	 * checks if table exists in $tab
	 *
	 * @param string $tab
	 * @return boolean
	 */
	static function tableExists($tab){
		$_db = we_io_DB::sharedAdapter();
		if($_db->fetchAll("SHOW TABLES LIKE '$tab';"))
			return true;
		else
			return false;
	}

}

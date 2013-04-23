<?php

/**
 * webEdition CMS
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
 * Configuration file for webEdition
 * =================================
 *
 * Must be adjusted to the current environment!
 *
 * NOTE:
 * =====
 * Edit this file ONLY if you know exactly what you are doing!
 */

/*****************************************************************************
 * SERVER SETTINGS
 *****************************************************************************/

/**
 * When adding a password protection to the webEdition directory uncomment the
 * following lines and adjust the given values.
 *
 * For example "myUsername" should be changed to "whatever" if "whatever"
 * would be the username to access the directory.
 */

//define("HTTP_USERNAME", "myUsername");
//define("HTTP_PASSWORD", "myPassword");

if (isset($_SERVER["HTTP_HOST"])) {
	$matches = parse_url($_SERVER["HTTP_HOST"]);
	if(isset($matches["port"]) && !empty($matches["port"])) {
		$SERVER_NAME = $matches["host"];
		$SERVER_PORT = $matches["port"];
	} else {
		$SERVER_NAME = $_SERVER["HTTP_HOST"];
	}
}

if (isset($SERVER_PORT) && $SERVER_PORT != 80) {
	define("HTTP_PORT", $SERVER_PORT);
}

define("SERVER_NAME", $SERVER_NAME);

/*****************************************************************************
 * DATABASE SETTINGS
 *****************************************************************************/

// Domain or IP address of the database server
define("DB_HOST",'127.0.0.1');

// Name of database being used by webEdition
define("DB_DATABASE",'webedition');

// Username to access the database
define("DB_USER",base64_decode('cm9vdA=='));

// Password to access the database
define("DB_PASSWORD",base64_decode(''));

// Mode how to access the database
//
// "connect":  This mode lets webEdition establishing a connection to the
//             database server that will be closed as soon as the execution of
//             a script ends.
// "pconnect": Using this mode webEdition will first, when connecting to the
//             database, try to find a (persistent) link that's already open
//             with the same host. Second, the connection to the database server
//             will not be closed when execution of a script ends. Instead, the
//             link will remain open for future use.

// Don't change this line!!!
define("DB_CONNECT",'pconnect');

// Prefix of tables in database for this webEdition.
define("TBL_PREFIX",'');

// Charset of tables in database for this webEdition.
define("DB_CHARSET",'');

// Collation of tables in database for this webEdition.
define("DB_COLLATION",'');

// Database wrapper class of webEdition
//include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/"."db_mysql.inc.php");



/*****************************************************************************
 * GLOBAL WEBEDITION SETTINGS
 *****************************************************************************/

// Name of licensee
define("WE_LIZENZ",'GPL');

// Path to the templates directory
define("TEMPLATE_DIR",$_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/templates");

// Path to the temporary files directory
define("TMP_DIR",$_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/tmp");

// Original language of this version of webEdition, used for login-screen
define("WE_LANGUAGE",'Deutsch');

// Original backend charset of this version of webEdition, used for login-screen
define("WE_BACKENDCHARSET",'UTF-8');

if (!isset($GLOBALS["WE_LANGUAGE"])) {
	$GLOBALS["WE_LANGUAGE"] = WE_LANGUAGE;
}

if (!isset($GLOBALS["WE_LANGUAGE"])) {
	$GLOBALS["WE_BACKENDCHARSET"] = WE_BACKENDCHARSET;
}

// PHP 5.3 date init #4353
if (!date_default_timezone_set(@date_default_timezone_get())){
	date_default_timezone_set('Europe/Berlin');
}
define("DATETIME_INITIALIZED",'1'); // to prevent additional initialization in we_defines und autoload, this allows later to make that an settings-item

//define ("WE_SQL_DEBUG", 1);
define('LIVEUPDATE_INSTALLED_WITH_CONTENT', true);

?>

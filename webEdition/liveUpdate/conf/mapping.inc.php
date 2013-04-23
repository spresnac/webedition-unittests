<?php

/**
 * webEdition CMS
 *
 * $Rev: 5965 $
 * $Author: mokraemer $
 * $Date: 2013-03-16 17:28:12 +0100 (Sat, 16 Mar 2013) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/*
 * map needed variables for the program here, for example map version number
 */


$LU_Variables = array(
	// always needed variables
	'clientVersion' => WE_VERSION,
	'clientSubVersion' => WE_SVNREV,
	'clientVersionName' => (defined("WE_VERSION_NAME")) ? WE_VERSION_NAME : '',
	'clientVersionSupp' => (defined("WE_VERSION_SUPP")) ? WE_VERSION_SUPP : '',
	'clientVersionSuppVersion' => (defined("WE_VERSION_SUPP_VERSION")) ? WE_VERSION_SUPP_VERSION : '',
	'clientVersionBranch' => (defined("WE_VERSION_BRANCH")) ? WE_VERSION_BRANCH : '',
	'clientPhpVersion' => phpversion(),
	'clientPhpExtensions' => implode(',', get_loaded_extensions()),
	'clientPcreVersion' => (defined("PCRE_VERSION")) ? PCRE_VERSION : '',
	'clientMySQLVersion' => getMysqlVer(false),
	'clientDBcharset' => we_database_base::getCharset(),
	'clientDBcollation' => we_database_base::getCollation(),
	'clientServerSoftware' => $_SERVER["SERVER_SOFTWARE"],
	'clientUid' => (defined('UID') ? UID : false),
	'clientSyslng' => WE_LANGUAGE,
	'clientLng' => $GLOBALS['WE_LANGUAGE'],
	'clientExtension' => '.php',
	'clientDomain' => urlencode($_SERVER['SERVER_NAME']),
	'clientInstalledModules' => $GLOBALS['_we_active_integrated_modules'],
	'clientInstalledLanguages' => liveUpdateFunctions::getInstalledLanguages(),
	'clientInstalledAppMeta' => weToolLookup::getAllTools(true, false, true),
	'clientInstalledAppTOC' => we_app_Common::readAppTOCasString(),
	'clientUpdateUrl' => getServerUrl() . $_SERVER['SCRIPT_NAME'],
	'clientContent' => false,
	'clientEncoding' => 'none',
	'clientSessionName' => session_name(),
	'clientSessionID' => session_id()
);
if(defined('WE_VERSION_SUPP') && WE_VERSION_SUPP!='release'){
	$LU_Variables['testUpdate']=1;
}

// These request variables listed here are NOT submitted to the server - fill it
// to keep requests small
$LU_IgnoreRequestParameters = array(
	'we_mode',
	'cookie',
	'treewidth_main',
	session_name(),
	'we' . session_id()
);

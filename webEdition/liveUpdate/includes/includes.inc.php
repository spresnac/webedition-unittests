<?php
/**
 * webEdition CMS
 *
 * $Rev: 4072 $
 * $Author: mokraemer $
 * $Date: 2012-02-17 17:14:46 +0100 (Fri, 17 Feb 2012) $
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

define('LIVEUPDATE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/webEdition/liveUpdate/');

include_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_error_handler.inc.php');
if(function_exists('we_error_setHandleAll')){
	we_error_setHandleAll();
}
if(!defined('WE_ERROR_HANDLER_SET')){
	we_error_handler();
}

if(isset($_REQUEST['PHPSESSID'])){
	session_id($_REQUEST['PHPSESSID']);
	unset($_REQUEST['PHPSESSID']);
	unset($_GET['PHPSESSID']);
	unset($_POST['PHPSESSID']);
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateHttp.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateResponse.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateFrames.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateFunctions.class.php');
require_once(LIVEUPDATE_DIR . 'classes/liveUpdateTemplates.class.php');

require_once(LIVEUPDATE_DIR . 'conf/mapping.inc.php');
require_once(LIVEUPDATE_DIR . 'conf/conf.inc.php');
require_once(LIVEUPDATE_DIR . 'includes/define.inc.php');
include_once(LIVEUPDATE_LANGUAGE_DIR . 'liveUpdate.inc.php');

if(is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateFunctionsServer.class.php')) {
	require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateFunctionsServer.class.php');
}
if(is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateResponseServer.class.php')) {
	require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateResponseServer.class.php');
}
if(is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php')) {
	require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php');
}
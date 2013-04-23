<?php

/**
 * webEdition CMS
 *
 * $Rev: 5914 $
 * $Author: lukasimhof $
 * $Date: 2013-03-03 14:55:25 +0100 (Sun, 03 Mar 2013) $
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
/* * ***************************************************************************
 * INITIALIZATION
 * *************************************************************************** */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf.inc.php');

if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_language.inc.php')){
	require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');
	require_once ($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/core/autoload.php');
	require_once(WE_INCLUDES_PATH . 'we_global.inc.php');
	we_loadLanguageConfig();
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

//Check some critical PHP Setings #7243
//FIXME: implement class sysinfo.class, for not analysing the same php settings twice (here and in sysinfo.php)
if(isset($_SESSION['perms']['ADMINISTRATOR']) && $_SESSION['perms']['ADMINISTRATOR']){
	$suhosinMsg = (in_array('suhosin', get_loaded_extensions())
			&& !in_array(ini_get('suhosin.simulation'), array('1', 'on', 'yes', 'true', true))) ? 'suhosin=on\n' : '';

	$maxInputMsg = !ini_get('max_input_vars') ? 'max_input_vars = 1000 (PHP default value)' :
		(ini_get('max_input_vars') < 2000 ? 'max_input_vars = ' . ini_get('max_input_vars') : '');
	$maxInputMsg .= $maxInputMsg ? ': >= 2000 is recommended' : $maxInputMsg;

	$criticalPhpMsg = trim($maxInputMsg . $suhosinMsg);
	if($criticalPhpMsg){
		t_e('Critical PHP Settings found', $criticalPhpMsg);
	}
}

//FIXME: implement resave of config files
if(!defined('CONF_SAVED_VERSION') || (defined('CONF_SAVED_VERSION') && (intval(WE_SVNREV) > intval(CONF_SAVED_VERSION)))){
	//resave config file(s)
	we_base_preferences::check_global_config(true);
}
we_util_File::checkAndMakeFolder($_SERVER['DOCUMENT_ROOT'].WE_THUMBNAIL_DIRECTORY);

define('LOGIN_DENIED', 4);
define('LOGIN_OK', 2);
define('LOGIN_CREDENTIALS_INVALID', 1);
define('LOGIN_UNKNOWN', 0);


$ignore_browser = isset($_REQUEST['ignore_browser']) && ($_REQUEST['ignore_browser'] === 'true');

function getValueLoginMode($val){
	$mode = isset($_COOKIE['we_mode']) ? $_COOKIE['we_mode'] : 'normal';
	switch($val){
		case 'seem' :
			return ($mode == 'seem') ? ' checked="checked"' : '';
		case 'normal' :// start normal mode
			return ($mode != 'seem') ? ' checked="checked"' : '';
		case 'popup':
			return (!isset($_COOKIE['we_popup']) || $_COOKIE['we_popup'] == 1);
	}
}

function printHeader($login, $status = 200){
	header('Expires: ' . gmdate('D, d.m.Y H:i:s') . ' GMT');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Pragma: no-cache');
	we_html_tools::setHttpCode($status);
	we_html_tools::htmlTop('webEdition');

	print STYLESHEET .
		we_html_element::cssElement('html, body {height:100%;}') .
		we_html_element::jsScript(JS_DIR . 'windows.js');
	include(JS_PATH . 'weJsStrings.inc.php');

	if($login != LOGIN_OK){
		print we_html_element::linkElement(array('rel' => 'home', 'href' => WEBEDITION_DIR)) .
			we_html_element::linkElement(array('rel' => 'author', 'href' => g_l('start', '[we_homepage]')));
	}

	print we_html_element::linkElement(array('rel' => 'SHORTCUT ICON', 'href' => IMAGE_DIR . 'webedition.ico')) .
		we_html_element::jsElement('cookieBackup = document.cookie;
	document.cookie = "cookie=yep";
	cookieOk = document.cookie.indexOf("cookie=yep") > -1;
	document.cookie = cookieBackup;

	if (!cookieOk) {
		' . we_message_reporting::getShowMessageCall(g_l('alert', "[no_cookies]"), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
	var messageSettings = ' . (we_message_reporting::WE_MESSAGE_ERROR + we_message_reporting::WE_MESSAGE_WARNING + we_message_reporting::WE_MESSAGE_NOTICE) . ';

/**
 * setting is built like the unix file system privileges with the 3 options
 * see notices, see warnings, see errors
 *
 * 1 => see Errors
 * 2 => see Warnings
 * 4 => see Notices
 *
 * @param message string
 * @param prio integer one of the values 1,2,4
 * @param win object reference to the calling window
 */
function showMessage(message, prio, win){

	if (!win) {
		win = window;
	}
	if (!prio) { // default is error, to avoid missing messages
		prio = ' . we_message_reporting::WE_MESSAGE_ERROR . ';
	}

	if (prio & messageSettings) { // show it, if you should

		// the used vars are in file JS_DIR . "weJsStrings.php";
		switch (prio) {

			// Notice
			case 1:
				win.alert(we_string_message_reporting_notice + ":\n" + message);
				break;

			// Warning
			case 2:
				win.alert(we_string_message_reporting_warning + ":\n" + message);
				break;

			// Error
			case 4:
				win.alert(we_string_message_reporting_error + ":\n" + message);
				break;
		}
	}
}
') .
		'</head>';
}

/* * ***************************************************************************
 * CLEAN Temporary Data left over from last logout  bug #4240
 * *************************************************************************** */
if(is_dir(WEBEDITION_PATH . 'we/cache')){
	we_util_File::deleteLocalFolder(WEBEDITION_PATH . 'we/cache', true);
}

cleanTempFiles(true);
cleanWEZendCache();
weNavigationCache::clean();
we_updater::fixInconsistentTables();

//clean Error-Log-Table
$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE `Date` < DATE_SUB(NOW(), INTERVAL ' . ERROR_LOG_HOLDTIME . ' DAY)');
$cnt = f('SELECT COUNT(1) AS a FROM ' . ERROR_LOG_TABLE, 'a', $GLOBALS['DB_WE']);

if($cnt > ERROR_LOG_MAX_ITEM_COUNT){
	$GLOBALS['DB_WE']->query('DELETE  FROM ' . ERROR_LOG_TABLE . ' WHERE 1 ORDER BY Date LIMIT ' . ($cnt - ERROR_LOG_MAX_ITEM_THRESH));
}


//CHECK FOR FAILED LOGIN ATTEMPTS
$GLOBALS['DB_WE']->query('DELETE FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND LoginDate < DATE_SUB(NOW(), INTERVAL ' . LOGIN_FAILED_HOLDTIME . ' DAY)');

$count = f('SELECT COUNT(1) AS count FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND IP="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '" AND LoginDate > DATE_SUB(NOW(), INTERVAL ' . intval(LOGIN_FAILED_TIME) . ' MINUTE)', 'count', $GLOBALS['DB_WE']);

if($count >= LOGIN_FAILED_NR){
	we_html_tools::htmlTop('webEdition ');
	print we_html_element::jsElement(
			we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[3timesLoginError]'), LOGIN_FAILED_NR, LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR)
		);
	print '</html>';
	exit();
}

/* * ***************************************************************************
 * SWITCH MODE
 * *************************************************************************** */
//set denied as default
$login = LOGIN_DENIED;
if(isset($GLOBALS['userLoginDenied'])){
	$login = LOGIN_DENIED;
} else if(isset($_SESSION['user']['Username']) && isset($_POST['password']) && isset($_POST['username'])){
	$login = LOGIN_OK;
	if(isset($_REQUEST['mode'])){
		setcookie('we_mode', $_REQUEST['mode'], time() + 2592000); //	Cookie remembers the last selected mode, it will expire in one Month !!!
	}
	setcookie('we_popup', (isset($_REQUEST['popup']) ? 1 : 0), time() + 2592000);
} else if(isset($_POST['password']) && isset($_POST['username'])){
	$login = LOGIN_CREDENTIALS_INVALID;
} else{
	$login = LOGIN_UNKNOWN;
	if($ignore_browser){
		setcookie('ignore_browser', 'true', time() + 2592000); //	Cookie remembers that the incompatible mode has been selected, it will expire in one Month !!!
	}
}

function getError($reason, $cookie = false){
	$_error = we_html_element::htmlB($reason);
	$_error_count = 0;
	$tmp = ini_get('session.save_path');

	if(!(is_dir($tmp) || (is_link($tmp) && is_dir(readlink($tmp))))){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[tmp_path]'), ini_get('session.save_path')) . we_html_element::htmlBr();
	}

	if(!ini_get('session.use_cookies')){
		$_error .= $_error_count++ . ' - ' . g_l('start', '[use_cookies]') . we_html_element::htmlBr();
	}

	if(ini_get('session.cookie_path') != '/'){
		$_error .= $_error_count++ . ' - ' . sprintf(g_l('start', '[cookie_path]'), ini_get('session.cookie_path')) . we_html_element::htmlBr();
	}

	if($cookie && $_error_count == 0){
		$_error .=++$_error_count . ' - ' . g_l('start', '[login_session_terminated]') . we_html_element::htmlBr();
	}

	$_error .= we_html_element::htmlBr() . g_l('start', ($_error_count == 1 ? '[solution_one]' : '[solution_more]'));

	$_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);
	$_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 250, we_html_element::htmlP(array('class' => 'defaultfont'), $_error), g_l('alert', '[phpError]'))));
	return $_layout;
}

/* * ***************************************************************************
 * CHECK FOR PROBLEMS
 * *************************************************************************** */

if(isset($_POST['checkLogin']) && !count($_COOKIE)){
	$_layout = getError(g_l('start', '[cookies_disabled]'));

	printHeader($login, 400);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else if(!$GLOBALS['DB_WE']->isConnected() || $GLOBALS['DB_WE']->Error == 'No database selected'){
	$_layout = getError(g_l('start', '[no_db_connection]'));

	printHeader($login, 503);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else if(isset($_POST['checkLogin']) && $_POST['checkLogin'] != session_id()){
	$_layout = getError(sprintf(g_l('start', '[phpini_problems]'), (ini_get('cfg_file_path') ? ' (' . ini_get('cfg_file_path') . ')' : '')) . we_html_element::htmlBr() . we_html_element::htmlBr() .
		'Debug-Info:' . we_html_element::htmlBr() .
		'submitted session id: ' . $_POST['checkLogin'] . we_html_element::htmlBr() .
		'current session id:   ' . session_id() . we_html_element::htmlBr() .
		'login-page date:      ' . $_POST['indexDate'] .
		we_html_element::htmlBr() . we_html_element::htmlBr()
	);
	printHeader($login, 408);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else if(!$ignore_browser && !we_base_browserDetect::isSupported()){

	/*	 * *******************************************************************
	 * CHECK BROWSER
	 * ******************************************************************* */

	$supportedBrowserCnt = (we_base_browserDetect::isMAC() ? 3 : (we_base_browserDetect::isUNIX() ? 2 : 4));

	$_browser_table = new we_html_table(array('cellspacing' => 0, 'cellpadding' => 0, 'border' => 0, 'width' => '100%'), 12, $supportedBrowserCnt);

	$_browser_table->setCol(1, 0, array('align' => 'center', 'class' => 'defaultfont', 'colspan' => $supportedBrowserCnt), we_html_element::htmlB(g_l('start', '[browser_not_supported]')));
	$_browser_table->setCol(3, 0, array('align' => 'center', 'class' => 'defaultfont', 'colspan' => $supportedBrowserCnt), g_l('start', '[browser_supported]'));

	switch(we_base_browserDetect::inst()->getSystem()){
		case we_base_browserDetect::SYS_MAC:
			$_browser_table->setCol(5, 0, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_opera.png', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(5, 1, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_safari.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(5, 2, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_firefox.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(7, 0, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), g_l('start', '[browser_opera]'))));
			$_browser_table->setCol(7, 1, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), g_l('start', '[browser_safari]'))));
			$_browser_table->setCol(7, 2, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), g_l('start', '[browser_firefox]'))));

			$_browser_table->setCol(9, 0, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_safari_version]'));
			$_browser_table->setCol(9, 1, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_firefox_version]'));
			break;
		case we_base_browserDetect::SYS_UNIX:
			$_browser_table->setCol(5, 0, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_opera.png', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(5, 1, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_firefox.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(7, 0, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), g_l('start', '[browser_opera]'))));
			$_browser_table->setCol(7, 1, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), g_l('start', '[browser_firefox]'))));
			$_browser_table->setCol(9, 0, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_opera_version]'));
			$_browser_table->setCol(9, 1, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_firefox_version]'));
			break;
		default:
			$_browser_table->setCol(5, 0, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.microsoft.com/windows/ie/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_ie.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(5, 1, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_opera.png', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(5, 2, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_firefox.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(5, 3, array('align' => 'center'), we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/supported_browser_safari.gif', 'width' => 80, 'height' => 80, 'border' => 0))));
			$_browser_table->setCol(7, 0, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.microsoft.com/windows/ie/', 'target' => '_blank'), g_l('start', '[browser_ie]'))));
			$_browser_table->setCol(7, 1, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.opera.com/', 'target' => '_blank'), g_l('start', '[browser_opera]'))));
			$_browser_table->setCol(7, 2, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.mozilla.org/', 'target' => '_blank'), g_l('start', '[browser_firefox]'))));
			$_browser_table->setCol(7, 3, array('align' => 'center', 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlA(array('href' => 'http://www.apple.com/safari/', 'target' => '_blank'), g_l('start', '[browser_safari]'))));
			$_browser_table->setCol(9, 0, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_ie_version]'));
			$_browser_table->setCol(9, 1, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_opera_version]'));
			$_browser_table->setCol(9, 2, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_firefox_version]'));
			$_browser_table->setCol(9, 3, array('align' => 'center', 'valign' => 'top', 'class' => 'defaultfont'), g_l('start', '[browser_safari_version]'));
	}


	$_browser_table->setCol(0, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 20));
	$_browser_table->setCol(2, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 50));
	$_browser_table->setCol(4, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 30));
	$_browser_table->setCol(6, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 10));
	$_browser_table->setCol(8, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 5));
	$_browser_table->setCol(10, 0, array('colspan' => $supportedBrowserCnt), we_html_tools::getPixel(1, 50));

	$_browser_table->setCol(11, 0, array('align' => 'center', 'class' => 'defaultfont', 'colspan' => $supportedBrowserCnt), we_html_element::htmlA(array('href' => WEBEDITION_DIR . 'index.php?ignore_browser=true'), g_l('start', '[ignore_browser]')));

	$_layout = new we_html_table(array('style' => 'width: 100%; height: 75%;'), 1, 1);

	$_layout->setCol(0, 0, array('align' => 'center', 'valign' => 'middle'), we_html_element::htmlCenter(we_html_tools::htmlMessageBox(500, 380, $_browser_table->getHtml(), g_l('start', '[cannot_start_we]'))));

	printHeader($login, 400);
	print we_html_element::htmlBody(array('style' => 'background-color:#FFFFFF;'), $_layout->getHtml()) . '</html>';
} else{

	/*	 * ***************************************************************************
	 * GENERATE LOGIN
	 * *************************************************************************** */

	$_hidden_values = we_html_element::htmlHidden(array('name' => 'checkLogin', 'value' => session_id())) .
		we_html_element::htmlHidden(array('name' => 'indexDate', 'value' => date('d.m.Y, H:i:s')));

	if($ignore_browser){
		$_hidden_values .= we_html_element::htmlHidden(array('name' => 'ignore_browser', 'value' => 'true'));
	}




	/*	 * ***********************************************************************
	 * BUILD DIALOG
	 * *********************************************************************** */

	$GLOBALS['loginpage'] = ($login == LOGIN_OK) ? false : true;
	include(WE_INCLUDES_PATH . 'we_templates/we_info.inc.php');

	$dialogtable = '<noscript style="color:#fff;">Please activate Javascript!' . we_html_element::htmlBr() . we_html_element::htmlBr() . '</noscript>
<table cellpadding="0" cellspacing="0" border="0" style="margin-left: auto; margin-right: auto;text-align:left;">
	<tr>
		<td style="background-color:#386AAB;"></td>
		<td rowspan="2">' . $_loginTable . '</td>
		<td valign="top" style="background-image:url(' . IMAGE_DIR . 'login/right.jpg);background-repeat:repeat-y;">' . we_html_element::htmlImg(array('src' => IMAGE_DIR . 'login/top_r.jpg')) . '</td>

	</tr>
	<tr>
		<td  valign="bottom" style="background-color:#386AAB;"></td>

		<td valign="bottom" style="height:296px;background-image:url(' . IMAGE_DIR . 'login/right.jpg);background-repeat:repeat-y;">' . we_html_element::htmlImg(array('src' => IMAGE_DIR . 'login/bottom_r.jpg')) . '</td>

	</tr>
	<tr>
		<td></td>
		<td style="background-image:url(' . IMAGE_DIR . 'login/bottom.jpg);background-repeat:repeat-x;">' . we_html_element::htmlImg(array('src' => IMAGE_DIR . 'login/bottom_l.jpg')) . '</td>
		<td>' . we_html_element::htmlImg(array('src' => IMAGE_DIR . 'login/bottom_r2.jpg')) . '</td>
	</tr>

</table>';



	//	PHP-Table
	$_contenttable = 432;
	$_layoutLeft = 14;
	$_layoutLeft2 = 3;
	$_layoutMiddle = 406;
	$_layoutRight1 = 12;
	$_layoutRight2 = 10;
	$_layoutRight = ($_layoutRight1 + $_layoutRight2);

	$_layouttable = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0', 'width' => 440), 4, 5);

	$_layouttable->setCol(0, 0, null, we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/top_left2.gif', 'width' => $_layoutLeft2, 'height' => 21)));
	$_layouttable->setCol(0, 1, null, we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/top_left.gif', 'width' => $_layoutLeft, 'height' => 21)));
	$_layouttable->setCol(0, 2, array('background' => IMAGE_DIR . 'info/top.gif', 'width' => $_layoutMiddle, 'class' => 'small', 'align' => 'right'), '&nbsp;');
	$_layouttable->setCol(0, 3, array('colspan' => 2, 'width' => $_layoutRight), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/top_right.gif', 'width' => $_layoutRight, 'height' => 21)));

	//	Here is table to log in
	$GLOBALS['loginpage'] = ($login == LOGIN_OK) ? false : true;

	include(WE_INCLUDES_PATH . 'we_templates/we_info.inc.php');

	$_layouttable->setCol(1, 0, array('background' => IMAGE_DIR . 'info/left2.gif'), we_html_tools::getPixel($_layoutLeft2, 1));
	$_layouttable->setCol(1, 1, array('colspan' => 3, 'width' => $_contenttable), $_loginTable);
	$_layouttable->setCol(1, 4, array('width' => $_layoutRight2, 'background' => IMAGE_DIR . 'info/right.gif'), we_html_tools::getPixel($_layoutRight2, 1));

	$_layouttable->setCol(2, 0, array('width' => $_layoutLeft2), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/bottom_left2.gif', 'width' => $_layoutLeft2, 'height' => 16)));
	$_layouttable->setCol(2, 1, null, we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/bottom_left.gif', 'width' => $_layoutLeft, 'height' => 16)));
	$_layouttable->setCol(2, 2, array('background' => IMAGE_DIR . 'info/bottom.gif'), we_html_tools::getPixel(1, 16));
	$_layouttable->setCol(2, 3, array('colspan' => 2, 'width' => $_layoutRight), we_html_element::htmlImg(array('src' => IMAGE_DIR . 'info/bottom_right.gif', 'width' => $_layoutRight, 'height' => 16)));

	$_layouttable->setCol(3, 0, null, we_html_tools::getPixel($_layoutLeft2, 1));
	$_layouttable->setCol(3, 1, null, we_html_tools::getPixel($_layoutLeft, 1));
	$_layouttable->setCol(3, 2, null, we_html_tools::getPixel($_layoutMiddle, 1));
	$_layouttable->setCol(3, 3, null, we_html_tools::getPixel($_layoutRight1, 1));
	$_layouttable->setCol(3, 4, null, we_html_tools::getPixel($_layoutRight2, 1));

	/*	 * ***********************************************************************
	 * GENERATE NEEDED JAVASCRIPTS
	 * *********************************************************************** */

	switch($login){
		case LOGIN_OK:
			$httpCode = 200;
			$_body_javascript = '';

			//	Here the mode - SEEM or normal is saved in the SESSION!!!
			//	Perhaps this must move to another place later.
			//	Later we must check permissions as well!
			if(!isset($_REQUEST['mode']) || $_REQUEST['mode'] == '' || $_REQUEST['mode'] == 'normal'){
				if(permissionhandler::isUserAllowedForAction('work_mode', 'normal')){
					$_SESSION['weS']['we_mode'] = 'normal';
				} else{
					$_body_javascript = we_message_reporting::getShowMessageCall(g_l('SEEM', '[only_seem_mode_allowed]'), we_message_reporting::WE_MESSAGE_ERROR);
					$_SESSION['weS']['we_mode'] = 'seem';
				}
			} else{
				$_SESSION['weS']['we_mode'] = $_REQUEST['mode'];
			}

			if((WE_LOGIN_WEWINDOW == 2 || WE_LOGIN_WEWINDOW == 0 && (!isset($_REQUEST['popup'])))){
				if(empty($_body_javascript)){
					$httpCode = 303;
					header('Location: ' . WEBEDITION_DIR . 'webEdition.php');
					$_body_javascript = 'alert("automatic redirect disabled");';
				} else{
					$_body_javascript.='top.location="' . WEBEDITION_DIR . 'webEdition.php"';
				}
			} else{
				$_body_javascript .= 'function open_we() {
			var aw=' . (isset($_SESSION['prefs']['weWidth']) && $_SESSION['prefs']['weWidth'] > 0 ? $_SESSION['prefs']['weWidth'] : 8000) . ';
			var ah=' . (isset($_SESSION['prefs']['weHeight']) && $_SESSION['prefs']['weHeight'] > 0 ? $_SESSION['prefs']['weHeight'] : 6000) . ';
			win = new jsWindow(\'' . WEBEDITION_DIR . "webEdition.php?h='+ah+'&w='+aw+'&browser='+((document.all) ? 'ie' : 'nn'), '" . md5(uniqid(__FILE__, true)) . "', -1, -1, aw, ah, true, true, true, true, '" . g_l('alert', "[popupLoginError]") . "', '" . WEBEDITION_DIR . "index.php'); }";
			}
			break;
		case LOGIN_CREDENTIALS_INVALID:
			we_log_loginFailed('tblUser', $_POST['username']);

			//CHECK FOR FAILED LOGIN ATTEMPTS
			$cnt = f('SELECT COUNT(1) AS count FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblUser" AND IP="' . $GLOBALS['DB_WE']->escape($_SERVER['REMOTE_ADDR']) . '" AND LoginDate > DATE_SUB(NOW(), INTERVAL ' . intval(LOGIN_FAILED_TIME) . ' MINUTE)', 'count', $GLOBALS['DB_WE']);

			$_body_javascript = ($cnt >= LOGIN_FAILED_NR ?
					we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[3timesLoginError]"), LOGIN_FAILED_NR, LOGIN_FAILED_TIME), we_message_reporting::WE_MESSAGE_ERROR) :
					we_message_reporting::getShowMessageCall(g_l('alert', "[login_failed]"), we_message_reporting::WE_MESSAGE_ERROR));
			break;
		case 3:
			$_body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', "[login_failed_security]"), we_message_reporting::WE_MESSAGE_ERROR) . "document.location = '" . WEBEDITION_DIR . "index.php" . (($ignore_browser || (isset($_COOKIE["ignore_browser"]) && $_COOKIE["ignore_browser"] == "true")) ? "&ignore_browser=" . (isset($_COOKIE["ignore_browser"]) ? $_COOKIE["ignore_browser"] : ($ignore_browser ? "true" : "false")) : "") . "';";
			break;
		case LOGIN_DENIED:
			$_body_javascript = we_message_reporting::getShowMessageCall(g_l('alert', "[login_denied_for_user]"), we_message_reporting::WE_MESSAGE_ERROR);
			break;
		default:
			$httpCode = 200;
			break;
	}


	$_layout = we_html_element::htmlDiv(array('style' => 'float: left;height: 50%;width: 1px;')) . we_html_element::htmlDiv(array('style' => 'clear:left;position:relative;top:-25%;'), we_html_element::htmlForm(array("action" => WEBEDITION_DIR . 'index.php', 'method' => 'post', 'name' => 'loginForm'), $_hidden_values . $dialogtable));

	printHeader($login, (isset($httpCode) ? $httpCode : 401));
	print we_html_element::htmlBody(array('style' => 'background-color:#386AAB; height:100%;', "onload" => (($login == LOGIN_OK) ? "open_we();" : "document.loginForm.username.focus();document.loginForm.username.select();")), $_layout . ((isset($_body_javascript)) ? we_html_element::jsElement($_body_javascript) : '')) . '</html>';
}
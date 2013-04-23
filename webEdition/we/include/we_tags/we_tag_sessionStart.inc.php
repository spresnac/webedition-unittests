<?php

/**
 * webEdition CMS
 *
 * $Rev: 5883 $
 * $Author: mokraemer $
 * $Date: 2013-02-25 13:55:39 +0100 (Mon, 25 Feb 2013) $
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
function we_tag_sessionStart($attribs){
	$GLOBALS['WE_SESSION_START'] = true;
	$persistentlogins = weTag_getAttribute('persistentlogins', $attribs, false, true);
	$onlinemonitor = weTag_getAttribute('onlinemonitor', $attribs, false, true);

	if(!isset($_SESSION)){
		@session_start();
		//FIXME: remove in 6.4; due to upgrade!
		if(isset($_SESSION['we'])){
			unset($_SESSION['we']);
		}
	}

	if(!defined('CUSTOMER_TABLE')){
		return '';
	}

	$SessionAutologin = 0;
	if(isset($_REQUEST['we_webUser_logout']) && $_REQUEST['we_webUser_logout']){

		if(isset($_SESSION['webuser']['registered']) && $_SESSION['webuser']['registered'] && isset($_SESSION['webuser']['ID']) && $_SESSION['webuser']['ID'] && ( (isset($_REQUEST['s']['AutoLogin']) && !$_REQUEST['s']['AutoLogin']) || (isset($_SESSION['webuser']['AutoLogin']) && !$_SESSION['webuser']['AutoLogin'])) && isset($_SESSION['webuser']['AutoLoginID'])){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($_SESSION['webuser']['AutoLoginID'])) . '"');
			setcookie('_we_autologin', '', (time() - 3600), '/');
		}
		unset($_SESSION['webuser']);
		unset($_SESSION['s']);
		unset($_REQUEST['s']);
		$_SESSION['webuser'] = array('registered' => false);

		$GLOBALS['WE_LOGOUT'] = true;
	} else{
		if(isset($_REQUEST['we_set_registeredUser']) && $GLOBALS['we_doc']->InWebEdition){
			$_SESSION['weS']['we_set_registered'] = $_REQUEST['we_set_registeredUser'];
		}
		if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
			if(!isset($_SESSION['webuser'])){
				$_SESSION['webuser'] = array(
					'registered' => false
				);
			}
			if(isset($_REQUEST['s']['Username']) && isset($_REQUEST['s']['Password']) && !(isset($_REQUEST['s']['ID']))){
				$GLOBALS['DB_WE']->query('DELETE FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND LoginDate < DATE_SUB(NOW(), INTERVAL ' . LOGIN_FAILED_HOLDTIME . ' DAY)');
				if(!wetagsessionStartdoLogin($persistentlogins, $SessionAutologin)){
					wetagsessionHandleFailedLogin();
				}
			}
			if($persistentlogins && ((isset($_SESSION['webuser']['registered']) && !$_SESSION['webuser']['registered']) || !isset($_SESSION['webuser']['registered']) ) && isset($_COOKIE['_we_autologin'])){
				if(!wetagsessionStartdoAutoLogin()){
					wetagsessionHandleFailedLogin();
				}
				if(isset($_SESSION['webuser']['registered']) && isset($_SESSION['webuser']['ID']) && isset($_SESSION['webuser']['Username']) && $_SESSION['webuser']['registered'] && $_SESSION['webuser']['ID'] && $_SESSION['webuser']['Username'] != ''){
					$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET LastAccess=UNIX_TIMESTAMP() WHERE ID=' . intval($_SESSION['webuser']['ID']));
				}
			}
		}
		if($onlinemonitor && isset($_SESSION['webuser']['registered'])){
			$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_SESSION_TABLE . ' WHERE LastAccess < DATE_SUB(NOW(), INTERVAL 1 HOUR)');
			$monitorgroupfield = weTag_getAttribute('monitorgroupfield', $attribs);
			$docAttr = weTag_getAttribute('monitordoc', $attribs);
			$doc = we_getDocForTag($docAttr, false);
			$PageID = $doc->ID;
			$ObjectID = 0;
			$SessionID = session_id();
			$SessionIp = (!empty($_SERVER['REMOTE_ADDR'])) ? oldHtmlspecialchars((string) $_SERVER['REMOTE_ADDR']) : '';

			$Browser = (!empty($_SERVER['HTTP_USER_AGENT'])) ? oldHtmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';
			$Referrer = (!empty($_SERVER['HTTP_REFERER'])) ? oldHtmlspecialchars((string) $_SERVER['HTTP_REFERER']) : '';
			if($_SESSION['webuser']['registered']){
				$WebUserID = $_SESSION['webuser']['ID'];
				$WebUserGroup = ($monitorgroupfield != '' ? $_SESSION['webuser'][$monitorgroupfield] : 'we_guest');
			} else{
				$WebUserID = 0;
				$WebUserGroup = 'we_guest';
			}
			$WebUserDescription = '';

			$GLOBALS['DB_WE']->query('INSERT INTO ' . CUSTOMER_SESSION_TABLE . ' SET ' .
				we_database_base::arraySetter(array(
					'SessionID' => $SessionID,
					'SessionIp' => $SessionIp,
					'WebUserID' => $WebUserID,
					'WebUserGroup' => $WebUserGroup,
					'WebUserDescription' => $WebUserDescription,
					'Browser' => $Browser,
					'Referrer' => $Referrer,
					'LastLogin' => 'NOW()',
					'PageID' => $PageID,
					'ObjectID' => $ObjectID,
					'SessionAutologin' => $SessionAutologin
				)) . ' ON DUPLICATE KEY UPDATE ' . we_database_base::arraySetter(array(
					'PageID' => $PageID,
					'WebUserID' => intval($WebUserID),
					'WebUserGroup' => $WebUserGroup,
					'WebUserDescription' => $WebUserDescription,
			)));
		}
		return '';
	}
}

function wetagsessionHandleFailedLogin($locked=false){
	$_SESSION['webuser'] = array(
		'registered' => false, 'loginfailed' => true
	);
	if(!isset($GLOBALS['WE_LOGIN_DENIED'])){
		we_log_loginFailed('tblWebUser', $_REQUEST['s']['Username']);
	}
	sleep(SECURITY_DELAY_FAILED_LOGIN);

	if(
		intval(f('SELECT count(1) AS a FROM '.FAILED_LOGINS_TABLE.' WHERE UserTable="tblWebUser" AND Username="' . $GLOBALS['DB_WE']->escape($_REQUEST['s']['Username']) . '" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour)', 'a', $GLOBALS['DB_WE'])) >= intval(SECURITY_LIMIT_CUSTOMER_NAME) ||
		intval(f('SELECT count(1) AS a FROM '.FAILED_LOGINS_TABLE.' WHERE UserTable="tblWebUser" AND IP="' . $_SERVER['REMOTE_ADDR'] . '" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_IP_HOURS) . ' hour)', 'a', $GLOBALS['DB_WE'])) >= intval(SECURITY_LIMIT_CUSTOMER_IP)
	){
		//don't serve user
		if(SECURITY_LIMIT_CUSTOMER_REDIRECT){
			@include($_SERVER['DOCUMENT_ROOT'] . id_to_path(SECURITY_LIMIT_CUSTOMER_REDIRECT, FILE_TABLE));
		} else{
			echo CheckAndConvertISOfrontend('Dear customer, our service is currently not available. Please try again later. Thank you.<br/>' .
				'Sehr geehrter Kunde, aus Sicherheitsgründen ist ein Login derzeit nicht möglich! Bitte probieren Sie es später noch ein mal. Vielen Dank');
		}
		exit();
	}
}

function wetagsessionStartdoLogin($persistentlogins, &$SessionAutologin){
	if($_REQUEST['s']['Username'] != ''){
		if(
			intval(f('SELECT count(1) AS a FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND Username="' . $GLOBALS['DB_WE']->escape($_REQUEST['s']['Username']) . '" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour)', 'a', $GLOBALS['DB_WE'])) >= intval(SECURITY_LIMIT_CUSTOMER_NAME) ||
			intval(f('SELECT count(1) AS a FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND IP="' . $_SERVER['REMOTE_ADDR'] . '" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_IP_HOURS) . ' hour)', 'a', $GLOBALS['DB_WE'])) >= intval(SECURITY_LIMIT_CUSTOMER_IP)
		){
			$GLOBALS['WE_LOGIN_DENIED']=true;
			return false;
		}
		$u = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE Password!="" AND LoginDenied=0 AND Username="' . $GLOBALS['DB_WE']->escape(strtolower($_REQUEST['s']['Username'])) . '"', $GLOBALS['DB_WE']);
		if(!empty($u) && $_REQUEST['s']['Password'] == $u['Password']){
			$_SESSION['webuser'] = $u;
			$_SESSION['webuser']['registered'] = true;
			$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET LastLogin=UNIX_TIMESTAMP() WHERE ID=' . intval($_SESSION['webuser']['ID']));

			if($persistentlogins && isset($_REQUEST['s']['AutoLogin']) && $_REQUEST['s']['AutoLogin'] && $_SESSION['webuser']['AutoLoginDenied'] != 1){
				$_SESSION['webuser']['AutoLoginID'] = uniqid(hexdec(substr(session_id(), 0, 8)), true);
				$GLOBALS['DB_WE']->query('INSERT INTO ' . CUSTOMER_AUTOLOGIN_TABLE . ' SET AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($_SESSION['webuser']['AutoLoginID'])) . '", WebUserID=' . intval($_SESSION['webuser']['ID']) . ',LastIp="' . oldHtmlspecialchars((string) $_SERVER['REMOTE_ADDR']) . '"');
				setcookie('_we_autologin', $_SESSION['webuser']['AutoLoginID'], (time() + CUSTOMER_AUTOLOGIN_LIFETIME), '/');
				$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET AutoLogin=1 WHERE ID=' . intval($_SESSION['webuser']['ID']));
				$_SESSION['webuser']['AutoLogin'] = 1;
				$SessionAutologin = 1;
			}
			$GLOBALS['WE_LOGIN'] = true;
			return true;
		}
	}
	return false;
}

function wetagsessionStartdoAutoLogin(){
	$autologinSeek = $_COOKIE['_we_autologin'];
	if($autologinSeek != ''){
		$a = getHash('SELECT * FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($autologinSeek)) . '"', $GLOBALS['DB_WE']);
		if(isset($a['WebUserID']) && $a['WebUserID']){
			$u = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE LoginDenied=0 AND AutoLoginDenied=0 AND Password!="" AND ID=' . intval($a['WebUserID']), $GLOBALS['DB_WE']);
			if(!empty($u)){
				$_SESSION['webuser'] = $u;
				$_SESSION['webuser']['registered'] = true;
				$_SESSION['webuser']['AutoLoginID'] = uniqid(hexdec(substr(session_id(), 0, 8)), true);
				$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_AUTOLOGIN_TABLE . ' SET ' . we_database_base::arraySetter(array(
						'AutoLoginID' => sha1($_SESSION['webuser']['AutoLoginID']),
						'LastIp' => $_SERVER['REMOTE_ADDR'],
					)) . ' WHERE WebUserID=' . intval($_SESSION['webuser']['ID']) . ' AND AutoLoginID="' . $GLOBALS['DB_WE']->escape(sha1($autologinSeek)) . '"'
				);

				setcookie('_we_autologin', $_SESSION['webuser']['AutoLoginID'], (time() + CUSTOMER_AUTOLOGIN_LIFETIME), '/');
				$GLOBALS['WE_LOGIN'] = true;
				return true;
			}
		}
	}

	return false;
}

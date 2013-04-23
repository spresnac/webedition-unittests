<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

/**
 * base class for rpc commands
 *
 * @package we_rpc
 * @abstract
 */
class rpcCmd{

	const STATUS_OK = 0;
	const STATUS_NO_PERMISSION = 1;
	const STATUS_NOT_ALLOWED_VIEW = 2;
	const STATUS_LOGIN_FAILED = 3;
	const STATUS_REQUEST_MALFORMED = 4;
	const STATUS_NO_CMD = 5;
	const STATUS_NO_CIEW = 6;
	const STATUS_NO_SESSION = 7;
	const STATUS_NO_VIEW = 8;

	var $CmdShell;
	var $ExtraViews = array();
	var $Permissions = array();
	var $Status = self::STATUS_OK;
	var $Parameters = array();

	function rpcCmd($shell){

		if((get_magic_quotes_gpc() == 1)){
			if(!empty($_REQUEST)){
				rpcCmd::stripSlashes($_REQUEST);
			}
		}

		$this->startSession();

		$this->checkSession();

		$this->checkParameters();

		if(!empty($this->Permissions)){

			foreach($this->Permissions as $perm){
				if(!we_hasPerm($perm)){
					$this->Status = self::STATUS_NO_PERMISSION;
				}
			}
		}

		$this->CmdShell = $shell;
	}

	function stripSlashes(&$arr){
		foreach($arr as $n => $v){
			if(is_array($v)){
				rpcCmd::stripSlashes($arr[$n]);
			} else{
				$arr[$n] = stripslashes($v);
			}
		}
	}

	function execute(){

		return new rpcResponse();
	}

	function checkSession(){

		if(!isset($_SESSION['user']['ID'])){

			$this->Status = self::STATUS_NO_SESSION;
			return false;
		}

		if(empty($_SESSION['user']['ID'])){
			$this->Status = self::STATUS_NO_SESSION;
			return false;
		}

		return true;
	}

	//FIXME: remove this - session is already started by we.inc
	function startSession(){

		if(isset($_REQUEST["weSessionId"])){
			session_id($_REQUEST["weSessionId"]);
		}
		@session_start();
	}

	function checkParameters(){

		foreach($this->Parameters as $par){

			if(!isset($_REQUEST[$par])){
				$this->Status = self::STATUS_REQUEST_MALFORMED;
				return false;
			}
		}
		return true;
	}

}


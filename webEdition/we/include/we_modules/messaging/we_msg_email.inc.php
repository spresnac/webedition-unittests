<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
include_once(WE_MESSAGING_MODULE_PATH . "we_msg_proto.inc.php");
include_once(WE_MESSAGING_MODULE_PATH . "messaging_std.inc.php");

/* messaging email send class */

class we_msg_email extends we_msg_proto{
	const TYPE_SEND_RECEIVE=0;
	const TYPE_SEND_ONLY=1;

	var $msgclass_type = self::TYPE_SEND_ONLY;

	function __construct(){
		parent::__construct();
		$this->Name = 'msg_email_' . md5(uniqid(__FILE__, true));
	}

	function get_email_addr($userid){
		return f('SELECT Email FROM ' . USER_TABLE . ' WHERE ID=' . intval($userid), 'Email', new DB_WE());
	}

//FIXME: is this ever called???
	function rfc2047_encode($header){

		/* Quoted-Printable encoding (see RFC 2045) should be okay for iso-8859-1 */
		$charset = 'ISO-8859-1';
		$encoding = 'Q';

		$enc_header = "=?$charset?$encoding?";
		$chars = preg_split('//', $header, -1, PREG_SPLIT_NO_EMPTY);
		$pre_enc_len = strlen($enc_header);
		$ew_len = $pre_enc_len;
		foreach($chars as $c){
			if($ew_len >= 70){
				/* PHP converts \n and \t into space characters, */
				/* thus making multi-line headers impossible. */
				$enc_header .= "?=\n\t=?$charset?$encoding?";
				$ew_len = $pre_enc_len;
			}

			$oc = ord($c);
			if(($oc >= 33 && $oc <= 60) || ($oc >= 62 && $oc <= 126)){
				$enc_header .= $c;
				$ew_len++;
			} else{
				$enc_header .= sprintf("=%X", $oc);
				$ew_len += 3;
			}
		}

		$enc_header .= "?=";

		return $enc_header;
	}

	function &send(&$rcpts, &$data){
		$results = array();
		$results['err'] = array();
		$results['ok'] = array();
		$results['failed'] = array();

		$from = get_nameline($this->userid, 'email');
		$to = array_shift($rcpts);
		//$cc = join(',', $rcpts);

		if(we_mail($to, $data['subject'], $data['body'], $from)){
			$results['err'] = g_l('modules_messaging', '[error_occured]') . ': ' . g_l('modules_messaging', '[mail_not_sent]');
			$results['failed'] = $rcpts;
		} else{
			array_unshift($rcpts, $to);
			$results['ok'] = $rcpts;
		}

		return $results;
	}

}

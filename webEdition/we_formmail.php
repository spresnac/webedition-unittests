<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

define('WE_DEFAULT_EMAIL', 'mailserver@' . $_SERVER['SERVER_NAME']);
define('WE_DEFAULT_SUBJECT', 'webEdition mailform');


$_blocked = false;


// check to see if we need to lock or block the formmail request

if(FORMMAIL_LOG){
	$_ip = $_SERVER['REMOTE_ADDR'];
	$_now = time();

	// insert into log
	$GLOBALS['DB_WE']->query('INSERT INTO ' . FORMMAIL_LOG_TABLE . ' (ip, unixTime) VALUES("' . $GLOBALS['DB_WE']->escape($_ip) . '", UNIX_TIMESTAMP())');
	if(FORMMAIL_EMPTYLOG > -1){
		$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime < ' . intval($_now - FORMMAIL_EMPTYLOG));
	}

	if(FORMMAIL_BLOCK){
		$_num = 0;
		$_trials = FORMMAIL_TRIALS;
		$_blocktime = FORMMAIL_BLOCKTIME;

		// first delete all entries from blocktable which are older then now - blocktime
		$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE blockedUntil != -1 AND blockedUntil < UNIX_TIMESTAMP()');

		// check if ip is allready blocked
		if(f('SELECT id FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE ip="' . $GLOBALS['DB_WE']->escape($_ip) . '"', 'id', $GLOBALS['DB_WE'])){
			$_blocked = true;
		} else{

			// ip is not blocked, so see if we need to block it
			$GLOBALS['DB_WE']->query('SELECT * FROM ' . FORMMAIL_LOG_TABLE . ' WHERE unixTime > ' . intval($_now - FORMMAIL_SPAN) . ' AND ip="' . $GLOBALS['DB_WE']->escape($_ip) . '"');
			if($GLOBALS['DB_WE']->next_record()){
				$_num = $GLOBALS['DB_WE']->num_rows();
				if($_num > $_trials){
					$_blocked = true;
					// cleanup
					$GLOBALS['DB_WE']->query('DELETE FROM ' . FORMMAIL_BLOCK_TABLE . ' WHERE ip="' . $GLOBALS['DB_WE']->escape($_ip) . '"');
					// insert in block table
					$blockedUntil = ($_blocktime == -1) ? -1 : intval($_now + $_blocktime);
					$GLOBALS['DB_WE']->query('INSERT INTO ' . FORMMAIL_BLOCK_TABLE . " (ip, blockedUntil) VALUES('" . $GLOBALS['DB_WE']->escape($_ip) . "', " . $blockedUntil . ")");
				}
			}
		}
	}
}

if(FORMMAIL_VIAWEDOC){
	if($_SERVER['SCRIPT_NAME'] == WEBEDITION_DIR . 'we_formmail.php')
		$_blocked = true;
}

if($_blocked){
	print_error('Email dispatch blocked / Email Versand blockiert!');
}

function is_valid_email($email){
	return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false);
}

function contains_bad_str($str_to_test){
	$str_to_test = trim($str_to_test);
	$bad_strings = array(
		'content-type:',
		'mime-version:',
		'Content-Transfer-Encoding:',
		'bcc:',
		'cc:',
		'to:',
	);

	foreach($bad_strings as $bad_string){
		if(preg_match('|^' . preg_quote($bad_string, "|") . '|i', $str_to_test) || preg_match('|[\n\r]' . preg_quote($bad_string, "|") . '|i', $str_to_test)){
			print_error('Email dispatch blocked / Email Versand blockiert!');
		}
	}
	if(preg_match('|multipart/mixed|i', $str_to_test)){
		print_error('Email dispatch blocked / Email Versand blockiert!');
	}
}

function replace_bad_str($str_to_test){
	$out = $str_to_test;
	$bad_strings = array(
		'(content-type)(:)',
		'(mime-version)(:)',
		'(multipart/mixed)',
		'(Content-Transfer-Encoding)(:)',
		'(bcc)(:)',
		'(cc)(:)',
		'(to)(:)',
	);


	foreach($bad_strings as $bad_string){
		$out = preg_replace("#$bad_string#i", "($1)$2", $out);
	}
	return $out;
}

function contains_newlines($str_to_test){
	if(preg_match("/(\\n+|\\r+)/", $str_to_test) != 0){
		print_error("newline found in $str_to_test. Suspected injection attempt - mail not being sent.");
	}
}

function print_error($errortext){

	$headline = 'Fehler / Error';
	$content = g_l('global', '[formmailerror]') . getHtmlTag('br') . '&#8226; ' . $errortext;

	$css = array(
		'media' => 'screen',
		'rel' => 'stylesheet',
		'type' => 'text/css',
		'href' => WEBEDITION_DIR . 'css/global.php',
	);

	print we_html_tools::htmlTop() .
		getHtmlTag('link', $css) .
		'</head>' .
		getHtmlTag('body', array('class' => 'weEditorBody'), '', false, true) .
		we_html_tools::htmlDialogLayout(getHtmlTag('div', array('class' => 'defaultgray'), $content), $headline) .
		'</body></html>';

	exit;
}

function check_required($required){
	if($required){
		$we_requiredarray = explode(',', $required);
		for($i = 0; $i < count($we_requiredarray); $i++){
			if(!$_REQUEST[$we_requiredarray[$i]]){
				return false;
			}
		}
	}
	return true;
}

function error_page(){
	if($_REQUEST['error_page']){
		$errorpage = (get_magic_quotes_gpc() == 1) ? stripslashes($_REQUEST['error_page']) : $_REQUEST['error_page'];
		redirect($errorpage);
	} else{
		print_error(g_l('global', '[email_notallfields]'));
	}
}

function ok_page($_subject = ''){
	if($_REQUEST['ok_page']){
		$ok_page = (get_magic_quotes_gpc() == 1) ? stripslashes($_REQUEST['ok_page']) : $_REQUEST['ok_page'];
		if(defined('WE_ECONDA_STAT') && WE_ECONDA_STAT){
			redirect($ok_page, $_subject);
		} else{
			redirect($ok_page);
		}
	} else{
		print 'Vielen Dank, Ihre Formulardaten sind bei uns angekommen! / Thank you, we received your form data!';
		if(defined('WE_ECONDA_STAT') && WE_ECONDA_STAT){
			print "<a name='emos_name' title='scontact' rel='$_subject' rev=''></a>\n";
		}
		exit;
	}
}

function redirect($url, $_emosScontact = ''){
	if($_emosScontact != ''){
		$url = $url . (strpos($url, '?') ? '&' : '?') . 'emosScontact=' . urlencode($_emosScontact);
	}
	header('Location: ' . getServerUrl() . $url);
	exit;
}

function check_recipient($email){
	return (f('SELECT ID FROM ' . RECIPIENTS_TABLE . " WHERE Email='" . $GLOBALS['DB_WE']->escape($email) . "'", 'ID', $GLOBALS['DB_WE']) ? true : false);
}

function check_captcha(){
	$name = $_REQUEST['captchaname'];

	if(isset($_REQUEST[$name]) && !empty($_REQUEST[$name])){
		return Captcha::check($_REQUEST[$name]);
	} else{
		return false;
	}
}

$_req = isset($_REQUEST['required']) ? $_REQUEST['required'] : '';

if(!check_required($_req)){
	error_page();
}

if(isset($_REQUEST['email']) && $_REQUEST['email']){
	if(!we_check_email($_REQUEST['email'])){
		if($_REQUEST['mail_error_page']){
			$foo = (get_magic_quotes_gpc() == 1) ? stripslashes($_REQUEST['mail_error_page']) : $_REQUEST['mail_error_page'];
			redirect($foo);
		} else{
			print_error(g_l('global', '[email_invalid]'));
		}
	}
}

$output = array();

$we_reserved = array('from', 'we_remove', 'captchaname', 'we_mode', 'charset', 'required', 'order', 'ok_page', 'error_page', 'captcha_error_page', 'mail_error_page', 'recipient', 'subject', 'mimetype', 'confirm_mail', 'pre_confirm', 'post_confirm', 'MAX_FILE_SIZE', session_name(), 'cookie', 'recipient_error_page', 'forcefrom');

if(isset($_REQUEST['we_remove'])){
	$removeArr = makeArrayFromCSV($_REQUEST['we_remove']);
	foreach($removeArr as $val){
		array_push($we_reserved, $val);
	}
}

$we_txt = '';
$we_html = '<table>';

$_order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$we_orderarray = array();
if($_order){
	$we_orderarray = explode(',', $_order);
	for($i = 0; $i < count($we_orderarray); $i++){
		if(!in_array($we_orderarray[$i], $we_reserved)){
			$output[$we_orderarray[$i]] = $_REQUEST[$we_orderarray[$i]];
		}
	}
}

if(isset($_GET)){
	foreach($_GET as $n => $v){
		if((!in_array($n, $we_reserved)) && (!in_array($n, $we_orderarray)) && (!is_array($v))){
			$output[$n] = $v;
		}
	}
}

if(isset($_POST)){
	foreach($_POST as $n => $v){
		if((!in_array($n, $we_reserved)) && (!in_array($n, $we_orderarray)) && (!is_array($v))){
			$output[$n] = $v;
		}
	}
}

foreach($output as $n => $v){
	if(is_array($v)){
		foreach($v as $n2 => $v2){
			if(!is_array($v2)){
				$foo = (get_magic_quotes_gpc() == 1) ? stripslashes($v2) : $v2;
				$n = replace_bad_str($n);
				$n2 = replace_bad_str($n2);
				$foo = replace_bad_str($foo);
				$we_txt .= $n . '[' . $n2 . "]: $foo\n" . ($foo ? '' : "\n");
				$we_html .= '<tr><td align="right"><b>' . $n . '[' . $n2 . ']:</b></td><td>' . $foo . '</td></tr>
';
			}
		}
	} else{
		$foo = (get_magic_quotes_gpc() == 1) ? stripslashes($v) : $v;
		$n = replace_bad_str($n);
		$foo = replace_bad_str($foo);
		$we_txt .= "$n: $foo\n" . ($foo ? '' : "\n");
		if($n == 'email'){
			$we_html .= '<tr><td align="right"><b>' . $n . ':</b></td><td><a href="mailto:' . $foo . '">' . $foo . '</a></td></tr>';
		} else{
			$we_html .= '<tr><td align="right"><b>' . $n . ':</b></td><td>' . $foo . '</td></tr>';
		}
	}
}

$we_html .= '</table>';


$we_html_confirm = '';
$we_txt_confirm = '';

if(isset($_REQUEST['email']) && $_REQUEST['email']){
	if(isset($_REQUEST['confirm_mail']) && $_REQUEST['confirm_mail']){
		$we_html_confirm = $we_html;
		$we_txt_confirm = $we_txt;
		if(isset($_REQUEST['pre_confirm']) && $_REQUEST['pre_confirm']){
			contains_bad_str($_REQUEST['pre_confirm']);
			$we_html_confirm = $_REQUEST['pre_confirm'] . '<br>' . $we_html_confirm;
			$we_txt_confirm = $_REQUEST['pre_confirm'] . "\n\n" . $we_txt_confirm;
		}
		if(isset($_REQUEST['post_confirm']) && $_REQUEST['post_confirm']){
			contains_bad_str($_REQUEST['post_confirm']);
			$we_html_confirm = $we_html_confirm . '<br>' . $_REQUEST['post_confirm'];
			$we_txt_confirm = $we_txt_confirm . "\n\n" . $_REQUEST['post_confirm'];
		}
	}
}

$email = (isset($_REQUEST['email']) && $_REQUEST['email']) ?
	$_REQUEST['email'] :
	((isset($_REQUEST['from']) && $_REQUEST['from']) ?
		$_REQUEST['from'] :
		WE_DEFAULT_EMAIL);

$subject = (isset($_REQUEST['subject']) && $_REQUEST['subject']) ?
	$_REQUEST['subject'] :
	WE_DEFAULT_SUBJECT;

$subject = strip_tags($subject);

$charset = (isset($_REQUEST['charset']) && $_REQUEST['charset']) ?
	str_replace("\n", "", str_replace("\r", "", $_REQUEST['charset'])) :
	$GLOBALS['WE_BACKENDCHARSET'];
$recipient = (isset($_REQUEST['recipient']) && $_REQUEST['recipient']) ?
	$_REQUEST['recipient'] :
	'';
$from = (isset($_REQUEST['from']) && $_REQUEST['from']) ?
	$_REQUEST['from'] :
	WE_DEFAULT_EMAIL;

$mimetype = (isset($_REQUEST['mimetype']) && $_REQUEST['mimetype']) ? $_REQUEST['mimetype'] : '';

$wasSent = false;

if($recipient){
	$fromMail = (isset($_REQUEST['forcefrom']) && $_REQUEST['forcefrom'] == 'true' ? $from : $email);

	$subject = preg_replace("/(\\n+|\\r+)/", "", $subject);
	$charset = preg_replace("/(\\n+|\\r+)/", "", $charset);
	$fromMail = preg_replace("/(\\n+|\\r+)/", "", $fromMail);
	$email = preg_replace("/(\\n+|\\r+)/", "", $email);
	$from = preg_replace("/(\\n+|\\r+)/", "", $from);

	contains_bad_str($email);
	contains_bad_str($from);
	contains_bad_str($fromMail);
	contains_bad_str($subject);
	contains_bad_str($charset);

	if(!is_valid_email($fromMail)){
		print_error(g_l('global', '[email_invalid]'));
	}

	$recipients = makeArrayFromCSV($recipient);
	$senderForename = isset($_REQUEST['forename']) && $_REQUEST['forename'] != '' ? $_REQUEST['forename'] : '';
	$senderSurname = isset($_REQUEST['surname']) && $_REQUEST['surname'] != '' ? $_REQUEST['surname'] : '';
	$sender = ($senderForename != '' || $senderSurname != '' ?
			$senderForename . ' ' . $senderSurname . '<' . $fromMail . '>' :
			$fromMail);

	$phpmail = new we_util_Mailer('', $subject, $sender);
	$phpmail->setCharSet($charset);

	$recipientsList = array();

	foreach($recipients as $recipientID){

		$recipient = (is_numeric($recipientID) ?
				f('SELECT Email FROM ' . RECIPIENTS_TABLE . ' WHERE ID=' . intval($recipientID), 'Email', $GLOBALS['DB_WE']) :
				// backward compatible
				$recipientID);

		if(!$recipient){
			print_error(g_l('global', '[email_no_recipient]'));
		}
		if(!is_valid_email($recipient)){
			print_error(g_l('global', '[email_invalid]'));
		}

		$recipient = preg_replace("/(\\n+|\\r+)/", "", $recipient);

		if(we_check_email($recipient) && check_recipient($recipient)){
			$recipientsList[] = $recipient;
		} else{
			print_error(g_l('global', '[email_recipient_invalid]'));
		}
	}

	if(count($recipientsList) > 0){
		foreach($_FILES as $name => $file){
			if(isset($file['tmp_name']) && $file['tmp_name']){
				$tempName = TEMP_PATH . '/' . $file['name'];
				move_uploaded_file($file['tmp_name'], $tempName);
				$phpmail->doaddAttachment($tempName);
			}
		}
		$phpmail->addAddressList($recipientsList);
		if($mimetype == 'text/html'){
			$phpmail->addHTMLPart($we_html);
		} else{
			$phpmail->addTextPart($we_txt);
		}
		$phpmail->buildMessage();
		if($phpmail->Send()){
			$wasSent = true;
		}
	}



	if((isset($_REQUEST['confirm_mail']) && $_REQUEST['confirm_mail']) && FORMMAIL_CONFIRM){
		if($wasSent){
			// validation
			if(!is_valid_email($email)){
				print_error(g_l('global', '[email_invalid]'));
			}
			$phpmail = new we_util_Mailer($email, $subject, $from);
			$phpmail->setCharSet($charset);
			if($mimetype == 'text/html'){
				$phpmail->addHTMLPart($we_html_confirm);
			} else{
				$phpmail->addTextPart($we_txt_confirm);
			}
			$phpmail->buildMessage();
			$phpmail->Send();
		}
	}
} else{
	print_error(g_l('global', '[email_no_recipient]'));
}

ok_page($subject);

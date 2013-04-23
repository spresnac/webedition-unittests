<?php

/**
 * webEdition CMS
 *
 * $Rev: 5951 $
 * $Author: mokraemer $
 * $Date: 2013-03-13 13:24:47 +0100 (Wed, 13 Mar 2013) $
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
 * class forerror_reporting, uses the javascript function showmessage in
 * webEdition.php
 *
 */
class we_message_reporting{
// contansts for messaging!
// these are binray checked like permissions in unix, DON'T change indexes

	const WE_MESSAGE_INFO = -1;
	const WE_MESSAGE_FRONTEND = -2;
	const WE_MESSAGE_NOTICE = 1;
	const WE_MESSAGE_WARNING = 2;
	const WE_MESSAGE_ERROR = 4;

	/**
	 * returns js-call for the showMessage function
	 *
	 * @param string $message
	 * @param integer $priority
	 * @param boolean $isJsMsg
	 * @return string
	 */
	static function getShowMessageCall($message, $priority, $isJsMsg = false, $isOpener = false){
		switch($priority){
			case self::WE_MESSAGE_INFO:
			case self::WE_MESSAGE_FRONTEND:
				return ($isJsMsg ? // message is build from scripts, just print it!
						"alert( $message );" :
						'alert("' . str_replace(array('\n', '\\', '"', '##NL##', '`'), array('##NL##', '\\\\', '\\"', '\n', '\"'), $message) . '");');
				break;
			default:
				return ($isJsMsg ? // message is build from scripts, just print it!
						($isOpener ? 'top.opener.' : '') . 'top.we_showMessage(' . $message . ', ' . $priority . ', window);' :
						($isOpener ? 'top.opener.' : '') . 'top.we_showMessage("' . str_replace(array("\n", '\n', '\\', '"', '###NL###'), array('###NL###', '###NL###', '\\\\', '\\"', '\n'), $message) . '", ' . $priority . ', window);'
					);
		}
	}

}
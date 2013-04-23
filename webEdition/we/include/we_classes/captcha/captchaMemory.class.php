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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class CaptchaMemory{

	/**
	 * Save the Captcha Code to the Memory
	 *
	 * @param string $file
	 * @param string $captcha
	 * @return void
	 */
	function save($captcha, $file){
		$items = self::readData($file);

		// delete old items
		if(!empty($items)) {
			foreach($items as $code => $item){
				if(time() > $item['time']
					|| ($_SERVER['REMOTE_ADDR'] == $item['ip']
					&& $_SERVER['HTTP_USER_AGENT'] == $item['agent'])){
					unset($items[$code]);
				}
			}
		}

		$items[$captcha] = array(
			'time' => time() + 30 * 60,
			'ip' => $_SERVER['REMOTE_ADDR'],
			'agent' => $_SERVER['HTTP_USER_AGENT'],
		);
		self::writeData($file, $items);
	}

	/* end: save */

	/**
	 * checks if the Captcha Code is a valid Code
	 *
	 * @param string $file
	 * @param string $captcha
	 * @return boolean
	 */
	function isValid($captcha, $file){

		$returnValue = false;

		$items = self::readData($file);

		// check if code is valid
		if(isset($items[$captcha])
			&& is_array($items[$captcha])
			&& time() < $items[$captcha]['time']
			&& $_SERVER['REMOTE_ADDR'] == $items[$captcha]['ip']
			&& $_SERVER['HTTP_USER_AGENT'] == $items[$captcha]['agent']){
			unset($items[$captcha]);
			$returnValue = true;
		}

		// delete old items
		if(!empty($items)){
			foreach($items as $code => $item){
				if(time() > $item['time']
					|| ($_SERVER['REMOTE_ADDR'] == $item['ip']
					&& $_SERVER['HTTP_USER_AGENT'] == $item['agent'])){
					unset($items[$code]);
				}
			}
		}

		self::writeData($file, $items);

		return $returnValue;
	}

	/* end: isValid */

	/**
	 * read the data file
	 *
	 * @param string $file
	 * @return void
	 */
	static function readData($file){
		if(file_exists($file . ".php")){
			include($file . ".php");
			if(isset($data)){
				return unserialize($data);
			}
		}
		return array();
	}

	/**
	 * write the data file
	 *
	 * @param string $file
	 * @return void
	 */
	static function writeData($file, $data){
		if(count($data) < 1){
			if(file_exists($file . '.php')){
				weFile::delete($file . '.php');
			}
		} else{
			weFile::save($file . '.php', '<?php $data=\'' . serialize($data) . '\';', 'w+');
		}
	}

}

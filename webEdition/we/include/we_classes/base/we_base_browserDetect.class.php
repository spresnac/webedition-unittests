<?php

/**
 * webEdition CMS
 *
 * $Rev: 5987 $
 * $Author: lukasimhof $
 * $Date: 2013-03-22 12:08:45 +0100 (Fri, 22 Mar 2013) $
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
class we_base_browserDetect{

	const UNKNOWN = 'unknown';
	const OPERA = 'opera';
	const IE = 'ie';
	const FF = 'firefox';
	const LYNX = 'lynx';
	const JAVA = 'java';
	const KONQUEROR = 'konqueror';
	const NETSCAPE = 'nn';
	const MOZILLA = 'mozilla';
	const APPLE = 'appleWebKit';
	const SAFARI = 'safari';
	const CHROME = 'chrome';
	const SYS_MAC = 'mac';
	const SYS_WIN = 'win';
	const SYS_UNIX = 'unix';

	///Browser
	protected static $br = self::UNKNOWN;
	/// String of useragent
	protected static $ua = '';
	///Version
	protected static $v = 0;
	///Operating System
	protected static $sys = self::UNKNOWN;
	///determines, if browser already detected
	private static $detected = false;

	function __construct($ua = ''){
		//prevent from redetecting the same strings
		if(self::$detected && $ua == ''){
			return;
		}
		if($ua != ''){
			self::$br = self::UNKNOWN;
			self::$v = 0;
			self::$sys = self::UNKNOWN;
		}
		self::$detected = true;
		self::$ua = $ua ? $ua : (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$regs = array();
		if(preg_match('/^([^ ]+) ([^(]*)(\([^)]+\))(.*)$/', self::$ua, $regs)){
			$pre = $regs[1];
			//$mid = $regs[2];
			$bracket = str_replace(array('(', ')'), '', $regs[3]);
			$brArr = explode(';', $bracket);
			$post = $regs[4];

			list($bez, $prever) = explode('/', $pre);
			$bez = strtolower($bez);

			switch($bez){
				case 'lynx':
					self::$br = self::LYNX;
					break;
				case 'mozilla':
					$java = explode('/', trim($post));
					if($java[0] == 'Java'){
						self::$br = self::JAVA;
						self::$v = $java[1];
					} else
					if(preg_match('/msie (.*)$/i', trim(isset($brArr[1])?$brArr[1]:$brArr[0]), $regs) && (trim($post) == '' || preg_match('/\.net/i', $post))){ //if last condition matches this will produce a notice. $regs[1] won't be defined
						self::$br = self::IE;
						self::$v = $regs[1];
					} else
					if(preg_match('/konqueror\/(.*)$/i', trim(isset($brArr[1])?$brArr[1]:$brArr[0]), $regs)){
						self::$br = self::KONQUEROR;
						self::$v = $regs[1];
					} else
					if(preg_match('/galeon\/(.*)$/i', trim(isset($brArr[1])?$brArr[1]:$brArr[0]), $regs)){
						self::$br = self::UNKNOWN;
						self::$v = $regs[1];
					} else{
						if(stristr($post, 'netscape6')){
							self::$br = self::NETSCAPE;
							if(preg_match('/netscape6\/(.+)/i', $post, $regs)){
								self::$v = trim($regs[1]);
							} else{
								self::$v = 6;
							}
						} else
						if(stristr($post, 'netscape/7')){
							self::$br = self::NETSCAPE;
							if(preg_match('/netscape\/(7.+)/i', $post, $regs)){
								self::$v = trim($regs[1]);
							} else{
								self::$v = 7;
							}
						} else
						if(preg_match('/AppleWebKit\/([0-9.]+)/i', $post, $regs)){
							self::$v = $regs[1];
							self::$br = self::APPLE;

							if(stristr($post, 'chrome')){
								if(preg_match('/chrome\/([0-9]+\.[0-9]+)/i', $post, $regs)){
									self::$v = $regs[1];
								} else{
									self::$v = '1';
								}

								self::$br = self::CHROME;
							} else
							if(stristr($post, 'safari')){
								if(preg_match('/version\/([0-9]+\.[0-9]+)/i', $post, $regs)){
									self::$v = $regs[1];
								} else{
									self::$v = '1';
								}
								self::$br = self::SAFARI;
							}
						} else
						if(preg_match('/firefox\/([0-9]+.[0-9]+)/i', $post, $regs)){
							self::$v = $regs[1];
							self::$br = self::FF;
						} else
						if(stristr($post, 'gecko')){
							self::$br = self::MOZILLA;
							if(preg_match('/rv:([0-9.]*)/i', $bracket, $regs)){
								self::$v = $regs[1];
							}
						} else
						if(preg_match('/opera ([^ ]+)/i', $post, $regs)){
							self::$br = self::OPERA;
							$reg = array();
							if(preg_match('/version\/([^ ]+)/i', $post, $reg)){
								self::$v = $reg[1];
							} else{
								self::$v = $regs[1];
							}
						} else
						if($brArr[0] == 'compatible'){
							self::$br = self::UNKNOWN;
							break;
						} else
						if(!stristr($bracket, 'msie')){
							self::$br = self::NETSCAPE;
							self::$v = preg_replace('/[^0-9.]/', '', $prever);
						}
					}

					$this->_getSys($bracket);
					break;
				case 'opera':
					self::$br = self::OPERA;
					if(preg_match('/version\/([^ ]+)/i', $post, $reg)){
						self::$v = $reg[1];
					} else{
						self::$v = $prever;
					}
					$this->_getSys($bracket);
					break;
				case 'googlebot':
					self::$br = self::UNKNOWN;
					#self::$v=$prever;
					break;
				case 'nokia-communicator-www-Browser':
					self::$br = self::UNKNOWN;
					break;
			}
			if(self::$sys == self::UNKNOWN){
				if(stristr(self::$ua, 'webtv')){
					self::$sys = 'webtv';
				}
			}
		} else
		if(preg_match('/^lynx([^a-z]+)[a-z].*/i', $ua, $regs)){
			self::$br = self::LYNX;
			self::$v = str_replace('/', '', $regs[1]);
		} else{
			self::$br = self::UNKNOWN;
		}
	}

	private function _getSys($bracket){
		if(stristr($bracket, 'mac')){
			self::$sys = self::SYS_MAC;
		} else
		if(stristr($bracket, 'win')){
			self::$sys = self::SYS_WIN;
		} else
		if(stristr($bracket, 'linux') || stristr($bracket, 'x11') || stristr($bracket, 'sun')){
			self::$sys = self::SYS_UNIX;
		}
	}

	static function inst(){
		static $ref = 0;
		if(!is_object($ref)){
			$ref = new self();
		}
		return $ref;
	}

	function getBrowser(){
		return self::$br;
	}

	static function isIE(){
		return self::inst()->getBrowser() == self::IE;
	}

	static function isOpera(){
		return self::inst()->getBrowser() == self::OPERA;
	}

	static function isSafari(){
		return self::inst()->getBrowser() == self::SAFARI;
	}

	static function isNN(){
		switch(self::inst()->getBrowser()){
			case self::NETSCAPE:
			case self::MOZILLA:
			case self::FF:
				return true;
		}
		return false;
	}

	static function isFF(){
		return self::inst()->getBrowser() == self::FF;
	}

	static function isCHROME(){
		return self::inst()->getBrowser() == self::CHROME;
	}

	static function isMAC(){
		return self::inst()->getSystem() == self::SYS_MAC;
	}

	static function isUNIX(){
		return self::inst()->getSystem() == self::SYS_UNIX;
	}

	static function isWin(){
		return self::inst()->getSystem() == self::SYS_WIN;
	}

	static function getIEVersion(){
		return self::isIE() ? intval(trim(self::$v)) : -1;
	}

	function getBrowserVersion(){
		return trim(self::$v);
	}

	function getSystem(){
		return self::$sys;
	}

	function getUserAgent(){
		return self::$ua;
	}

	function getWebKitVersion(){
		$regs = array();
		if(preg_match('#AppleWebKit/([^ ]+)#i', self::$ua, $regs)){
			return intval($regs[1]);
		}
		return 0;
	}

	static function isGecko(){
		return stristr(self::inst()->getUserAgent(), 'gecko');
	}

	//todo: implement from we_browser_check
	static function isSupported(){
		if(self::isGecko()){
			return true;
		}
		$inst = self::inst();
		switch($inst->getSystem()){
			case self::SYS_WIN :
				switch($inst->getBrowser()){
					case self::IE:
					case self::OPERA:
					case self::SAFARI:
						return true;
				}
				break;

			case self::SYS_MAC:
				switch($inst->getBrowser()){
					case self::OPERA:
					case self::SAFARI:
						return true;
				}
				break;

			case self::SYS_UNIX:
				switch($inst->getBrowser()){
					case self::OPERA:
						return true;
				}

				break;

			case self::UNKNOWN:
				switch($inst->getBrowser()){
					case self::IE:
					case self::OPERA:
					case self::SAFARI:
						return true;
				}

				break;
		}
		return false;
	}

}
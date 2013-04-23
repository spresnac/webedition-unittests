<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * class for handling client information
 *
 * @category   we
 * @package    we_ui
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_Client{
	/**
	 * constant for IE Browser
	 */

	const kBrowserIE = 0;

	/**
	 * constant for Gecko Browser
	 */
	const kBrowserGecko = 1;

	/**
	 * constant for Webkit
	 */
	const kBrowserWebkit = 2;

	/**
	 * constant for other Browsers
	 */
	const kBrowserOther = 3;

	/**
	 * constant for Windows System
	 */
	const kSystemWindows = 0;

	/**
	 * constant for MacOs System
	 */
	const kSystemMacOS = 1;

	/**
	 * constant for other Systems
	 */
	const kSystemOther = 2;

	/**
	 * instance
	 */
	private static $instance;

	/**
	 * _system attribute
	 *
	 * @var string
	 */
	protected $_system;

	/**
	 * _browser attribute
	 *
	 * @var string
	 */
	protected $_browser;

	/**
	 * _version attribute
	 *
	 * @var string
	 */
	protected $_version;

	/**
	 * Constructor
	 *
	 * Set user agent properties
	 *
	 * @param string $userAgent
	 * @return void
	 */
	function __construct($userAgent = ''){
		$inst = we_base_browserDetect::inst();
		$this->_version = $inst->getBrowserVersion();

		switch($inst->getBrowser()){
			case we_base_browserDetect::IE:
				$this->_browser = self::kBrowserIE;
				break;
			case we_base_browserDetect::FF:
				$this->_browser = self::kBrowserGecko;
				break;
			case we_base_browserDetect::APPLE:
				$this->_browser = self::kBrowserWebkit;
				break;
			default:
				$this->_browser = self::kBrowserOther;
		}

		switch($inst->getSystem()){
			case we_base_browserDetect::SYS_MAC:
				$this->_system = self::kSystemMacOS;
				break;
			case we_base_browserDetect::SYS_WIN:
				$this->_system = self::kSystemWindows;
				break;
			default:
				$this->_system = self::kSystemOther;
		}
	}

	/**
	 * returns instance
	 *
	 * @param string $userAgent
	 * @return instance
	 */
	public static function getInstance($userAgent = ''){
		if(!self::$instance instanceof self){
			self::$instance = new self($userAgent);
		}
		return self::$instance;
	}

	/**
	 * retrieve browser
	 *
	 * @return string
	 */
	public function getBrowser(){
		return $this->_browser;
	}

	/**
	 * retrieve system
	 *
	 * @return string
	 */
	public function getSystem(){
		return $this->_system;
	}

	/**
	 * retrieve version
	 *
	 * @return version
	 */
	public function getVersion(){
		return $this->_version;
	}

}

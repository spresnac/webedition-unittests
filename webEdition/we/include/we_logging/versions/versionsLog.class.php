<?php

/**
 * webEdition CMS
 *
 * $Rev: 4258 $
 * $Author: mokraemer $
 * $Date: 2012-03-11 21:10:50 +0100 (Sun, 11 Mar 2012) $
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
class versionsLog extends logging{

	const VERSIONS_DELETE = 1;
	const VERSIONS_RESET = 2;
	const VERSIONS_PREFS = 3;

	public $action;
	public $data;

	function __construct(){
		parent::__construct(VERSIONS_TABLE_LOG);
	}

	function saveVersionsLog($logArray, $action = ""){

		$this->action = $action;
		$this->data = serialize($logArray);

		$this->saveLog();
	}

}

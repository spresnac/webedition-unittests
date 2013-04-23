<?php

/**
 * webEdition CMS
 *
 * $Rev: 4300 $
 * $Author: mokraemer $
 * $Date: 2012-03-18 16:36:04 +0100 (Sun, 18 Mar 2012) $
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
class rpcSelectorGetSelectedIdCmd extends rpcCmd{

	function execute(){
		$resp = new rpcResponse();

		if(!isset($_REQUEST['we_cmd'][1]) || !isset($_REQUEST['we_cmd'][2]))
			exit();

		$selectorSuggest = new weSelectorQuery();
		$contentTypes = isset($_REQUEST['we_cmd'][3]) ? explode(",", $_REQUEST['we_cmd'][3]) : null;
		$selectorSuggest->queryTable($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], $contentTypes);
		$resp->setData("data", $selectorSuggest->getResult());

		return $resp;
	}

}

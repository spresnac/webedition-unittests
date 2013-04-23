<?php

/**
 * webEdition CMS
 *
 * $Rev: 5075 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 02:04:23 +0100 (Tue, 06 Nov 2012) $
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
class rpcGetMouseOverDivsCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		we_html_tools::protect();

		$whichsearch = $_REQUEST['whichsearch'];
		$setView = $_REQUEST['we_cmd']['setView'];
		$anzahl = $_REQUEST['we_cmd']['anzahl'];
		$searchstart = $_REQUEST['we_cmd']['searchstart'];

		if(isset($_REQUEST["we_transaction"])){
			$_REQUEST['we_transaction'] = (preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0);

			$we_dt = isset($_SESSION['weS']['we_data'][$_REQUEST["we_transaction"]]) ? $_SESSION['weS']['we_data'][$_REQUEST["we_transaction"]] : "";
		}

		$_document = new $_REQUEST["classname"];
		$_document->we_initSessDat($we_dt);

		$_REQUEST['we_cmd']['obj'] = $_document;

		$code = "";
		if($setView == 1){
			$content = doclistView::searchProperties($whichsearch);

			$x = $searchstart + $anzahl;
			if($x > count($content)){
				$x = $x - ($x - count($content));
			}

			$code = searchtoolView::makeMouseOverDivs($x, $content, $whichsearch);
		}

		$resp->setData("data", $code);

		return $resp;
	}

}

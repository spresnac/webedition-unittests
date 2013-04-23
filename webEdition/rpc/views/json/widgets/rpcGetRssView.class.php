<?php

/**
 * webEdition CMS
 *
 * $Rev: 5179 $
 * $Author: mokraemer $
 * $Date: 2012-11-20 11:31:54 +0100 (Tue, 20 Nov 2012) $
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
class rpcGetRssView extends rpcJsonView{

	/**
	 * @param rpcResponse $response
	 * @return string
	 */
	function getResponse($response){
		return
			'weResponse = {
			"type":"' . ($response->Success ? "response" : "error") . '",
			"data":"' . addslashes(str_replace(array("\n", "\r"), " ", $response->getData("data"))) . '",
			"titel":"' . addslashes($response->getData("titel")) . '",
			"widgetType":"' . addslashes($response->getData("widgetType")) . '",
			"widgetId":"' . addslashes($response->getData("widgetId")) . '"
		};';
	}

}
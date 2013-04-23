<?php
/**
 * webEdition CMS
 *
 * $Rev: 3590 $
 * $Author: mokraemer $
 * $Date: 2011-12-19 12:38:55 +0100 (Mon, 19 Dec 2011) $
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


class rpcSelectorGetSelectedIdView extends rpcView {


	function getResponse($response) {

		header('Content-type: text/plain');
		$suggests = $response->getData("data");
		$html = "";
		if (is_array($suggests) && isset($suggests[0]['ID'])) {
			$status = "response";
			$html .= ' "id": "'.$_REQUEST['we_cmd'][4].'", "value": "'.$suggests[0]['ID'].'"';
			$html .= isset($suggests[0]['ContentType']) ? ', "contentType": "'.$suggests[0]['ContentType'].'"' : "";
		} else {
			$status = "error";
			if(strpos($_REQUEST['we_cmd'][3],',')) {
				switch ($_REQUEST['we_cmd'][2]) {
					case FILE_TABLE:
						$msg = g_l('weSelectorSuggest',"[no_document]");
						break;
					case TEMPLATES_TABLE:
						$msg = g_l('weSelectorSuggest',"[no_template]");
						break;
					case OBJECT_TABLE:
						$msg = g_l('weSelectorSuggest',"[no_class]");
						break;
					case OBJECT_FILES_TABLE:
						$msg = g_l('weSelectorSuggest',"[no_class]");
						break;
					default:
						$msg = g_l('weSelectorSuggest',"[no_result]");
						break;
				}
			} else  {
				$msg = g_l('weSelectorSuggest',"[no_folder]");
			}
			$html .= '"msg":"'.$msg.'","nr":"'.$_REQUEST['we_cmd'][2].'"';
		}
		return
			'var weResponse = {
			"type": "' . $status . '",
			"data": {' . $html . ' }
		};';
	}
}

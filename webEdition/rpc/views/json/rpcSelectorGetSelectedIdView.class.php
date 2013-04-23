<?php
/**
 * webEdition CMS
 *
 * $Rev: 3519 $
 * $Author: mokraemer $
 * $Date: 2011-12-06 20:07:38 +0100 (Tue, 06 Dec 2011) $
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
		if (is_array($suggests)) {
			$html .= $suggests[0]['ID'];
		}
		return $html;
	}
}

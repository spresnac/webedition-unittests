<?php
/**
 * webEdition CMS
 *
 * $Rev: 3655 $
 * $Author: mokraemer $
 * $Date: 2011-12-26 15:49:26 +0100 (Mon, 26 Dec 2011) $
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

class rpcLoginView extends rpcView {


	function getResponse($response) {

		if($response->getStatus()== rpcCmd::STATUS_OK) {

			$html = 'LOGIN OK<br>';


		} else {

			$html = 'LOGIN FAILED:' . $response->getReason();

		}

		return $html;

	}
}

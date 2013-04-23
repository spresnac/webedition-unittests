<?php

/**
 * webEdition CMS
 *
 * $Rev: 4303 $
 * $Author: mokraemer $
 * $Date: 2012-03-21 12:50:46 +0100 (Wed, 21 Mar 2012) $
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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class rpctoolfactoryView extends rpcView{

	function getResponse($response){

		$html = 'Hello World! My name is toolfactory and I am a webEdition-Application.';

		return $html;
	}

}
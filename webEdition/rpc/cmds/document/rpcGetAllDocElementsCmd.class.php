<?php

/**
 * webEdition CMS
 *
 * $Rev: 3955 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 21:13:34 +0100 (Tue, 07 Feb 2012) $
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
class rpcGetAllDocElementsCmd extends rpcCmd{

	var $Parameters = array('docid');

	function execute(){

		$resp = new rpcResponse();

		$_doc = new we_webEditionDocument();
		$_doc->initByID($_REQUEST['docid']);

		$resp->setData('elements', $_doc->elements);

		return $resp;
	}

}

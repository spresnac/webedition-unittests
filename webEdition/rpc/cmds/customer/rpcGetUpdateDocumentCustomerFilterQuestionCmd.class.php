<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
class rpcGetUpdateDocumentCustomerFilterQuestionCmd extends rpcCmd{

	function execute(){


		$resp = new rpcResponse();

		// compare filter of document with fitler of folder
		$_filterOfFolder = $this->getFilterOfFolder($_REQUEST["folderId"], $_REQUEST["table"]);

		if(isset($_REQUEST["we_transaction"])){
			$we_dt = isset($_SESSION['weS']['we_data'][$_REQUEST["we_transaction"]]) ? $_SESSION['weS']['we_data'][$_REQUEST["we_transaction"]] : "";
		}

		// filter of document
		$_document = new $_REQUEST["classname"];
		$_document->we_initSessDat($we_dt);
		$_filterOfDocument = $_document->documentCustomerFilter;

		$_ret = (weDocumentCustomerFilter::filterAreQual($_filterOfFolder, $_filterOfDocument, true) ? 'false' : 'true');

		$resp->setData('data', $_ret);

		return $resp;
	}

	function getFilterOfFolder($id, $table){
		if($id > 0){
			$folder = new we_folder();
			$folder->initByID($id, $table);
			return $folder->documentCustomerFilter;
		} else{
			return "";
		}
	}

}

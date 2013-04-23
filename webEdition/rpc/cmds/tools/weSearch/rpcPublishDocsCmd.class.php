<?php

/**
 * webEdition CMS
 *
 * $Rev: 5833 $
 * $Author: lukasimhof $
 * $Date: 2013-02-18 14:17:13 +0100 (Mon, 18 Feb 2013) $
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
class rpcPublishDocsCmd extends rpcCmd{

	function execute(){

		$db = new DB_WE();

		we_html_tools::protect();

		$docs = array();

		$arr = $_REQUEST['we_cmd'];
		if(!empty($arr)){
			$allDocs = explode(",", $arr[0]);
			foreach($allDocs as $k => $v){
				$teile = explode("_", $v, 2);
				$docs[$teile[1]][] = $teile[0];
			}
		}
		if(!empty($docs)){
			foreach($docs as $k => $v){
				if(!empty($v)){
					foreach($v as $key => $val){
						$ContentType = f("SELECT ContentType FROM `" . $db->escape($k) . "` WHERE ID=" . intval($val), "ContentType", $db);
						$object = weContentProvider::getInstance($ContentType, $val, $k);
						/* bugs #6189 & 4859
						we_temporaryDocument::delete($object->ID);
						$object->initByID($object->ID);
						$object->ModDate = $object->Published;
						*/
						$_SESSION['weS']['versions']['doPublish'] = true;
						$object->we_save();
						$object->we_publish();
						if(defined("WORKFLOW_TABLE") && $object->ContentType == "text/webedition"){
							if(we_workflow_utility::inWorkflow($object->ID, $object->Table)){
								we_workflow_utility::removeDocFromWorkflow($object->ID, $object->Table, $_SESSION["user"]["ID"], "");
							}
						}
						unset($_SESSION['weS']['versions']['doPublish']);
					}
				}
			}
		}
	}

}


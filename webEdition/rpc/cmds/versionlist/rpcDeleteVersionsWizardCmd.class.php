<?php

/**
 * webEdition CMS
 *
 * $Rev: 5070 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 23:52:42 +0100 (Sun, 04 Nov 2012) $
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
class rpcDeleteVersionsWizardCmd extends rpcCmd{

	function execute(){

		$resp = new rpcResponse();

		we_html_tools::protect();

		$db = new DB_WE();

		$query = "DELETE FROM `" . VERSIONS_TABLE . "` WHERE " . $_SESSION['weS']['versions']['deleteWizardWhere'];
		$db->query($query);

		unset($_SESSION['weS']['versions']['deleteWizardWhere']);

//		while($db->next_record()) {
//			weVersions::deleteVersion($db->f("ID"));
//		}
//		foreach($_SESSION['weS']['versions']['IDs'] as $k=>$v) {
//			weVersions::deleteVersion($v);
//		}
		if(isset($_SESSION['weS']['versions']['deleteWizardbinaryPath']) && is_array($_SESSION['weS']['versions']['deleteWizardbinaryPath']) && !empty($_SESSION['weS']['versions']['deleteWizardbinaryPath'])){
			foreach($_SESSION['weS']['versions']['deleteWizardbinaryPath'] as $k => $v){
				$binaryPath = $_SERVER['DOCUMENT_ROOT'] . $v;
				$binaryPathUsed = f("SELECT binaryPath FROM " . VERSIONS_TABLE . " WHERE binaryPath='" . $db->escape($v) . "' LIMIT 1", "binaryPath", $db);

				if(file_exists($binaryPath) && $binaryPathUsed == ""){
					@unlink($binaryPath);
				}
			}
			unset($_SESSION['weS']['versions']['deleteWizardbinaryPath']);
		}

		if(!empty($_SESSION['weS']['versions']['logDeleteIds'])){
			$versionslog = new versionsLog();
			$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logDeleteIds'], versionsLog::VERSIONS_DELETE);
		}
		unset($_SESSION['weS']['versions']['logDeleteIds']);


		$WE_PB = new we_progressBar(100, 0, true);
		$WE_PB->setStudLen(200);

		$WE_PB->addText(g_l('versions', '[deleteDateVersionsOK]'), 0, "pb1");
		$js = $WE_PB->getJSCode();
		$pb = $WE_PB->getHTML();


		$resp->setData("data", $pb);

		return $resp;
	}

}
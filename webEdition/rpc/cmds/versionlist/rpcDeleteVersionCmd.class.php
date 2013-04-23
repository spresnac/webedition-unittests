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

class rpcDeleteVersionCmd extends rpcCmd {

	function execute() {

		$db = new DB_WE();

		$ids = array();

		we_html_tools::protect();

		if(isset($_REQUEST['we_cmd']["deleteVersion"]) && $_REQUEST['we_cmd']["deleteVersion"]!="") {

			$ids = makeArrayFromCSV($_REQUEST['we_cmd']["deleteVersion"]);

		}

		if(!empty($ids)) {
			$_SESSION['weS']['versions']['logDeleteIds'] = array();
			foreach($ids as $k => $v) {
				weVersions::deleteVersion($v);
			}
			if(!empty($_SESSION['weS']['versions']['logDeleteIds'])) {
				$versionslog = new versionsLog();
				$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logDeleteIds'],versionsLog::VERSIONS_DELETE);
			}
			unset($_SESSION['weS']['versions']['logDeleteIds']);
		}
	}
}


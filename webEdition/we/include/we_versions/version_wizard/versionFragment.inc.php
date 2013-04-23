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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class versionFragment extends taskFragment{

	function __construct($name, $taskPerFragment, $pause = 0, $bodyAttributes = "", $initdata = ""){
		parent::__construct($name, $taskPerFragment, $pause, $bodyAttributes, $initdata);
	}

	function doTask(){
		we_version::todo($this->data);
		$this->updateProgressBar();
	}

	function updateProgressBar(){
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		print we_html_element::jsElement(
			'if(parent.wizbusy.document.getElementById("progr")){parent.wizbusy.document.getElementById("progr").style.display="";};parent.wizbusy.setProgressText("pb1",(parent.wizbusy.document.getElementById("progr") ? "' . addslashes(
				shortenPath(
					$this->data["path"] . " - " . g_l('versions', '[version]') . " " . $this->data["version"], 33)) . '" : "' . "test" . addslashes(
				shortenPath(
					$this->data["path"] . " - " . g_l('versions', '[version]') . " " . $this->data["version"], 60)) . '") );parent.wizbusy.setProgress(' . $percent . ');');
	}

	function finish(){
		if(!empty($_SESSION['weS']['versions']['logResetIds'])){
			$versionslog = new versionsLog();
			$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logResetIds'], versionsLog::VERSIONS_RESET);
		}
		unset($_SESSION['weS']['versions']['logResetIds']);
		$responseText = isset($_REQUEST["responseText"]) ? $_REQUEST["responseText"] : "";
		we_html_tools::htmlTop();
		if($_REQUEST['type'] == "delete_versions"){
			$responseText = g_l('versions', '[deleteDateVersionsOK]');
		}
		if($_REQUEST['type'] == "reset_versions"){
			$responseText = g_l('versions', '[resetAllVersionsOK]');
		}
		print we_html_element::jsElement(we_message_reporting::getShowMessageCall(
					addslashes($responseText ? $responseText : ""), we_message_reporting::WE_MESSAGE_NOTICE) . '

			// reload current document => reload all open Editors on demand

			var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
			for (frameId in _usedEditors) {

				if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);

				} else {
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			_multiEditorreload = true;

			//reload tree
			top.opener.we_cmd("load", top.opener.treeData.table ,0);

			top.close();
		') .
			'</head></html>';
	}

	function printHeader(){
		we_html_tools::protect();
		we_html_tools::htmlTop();
		echo '</head>';
	}

	function printBodyTag($attributes = ""){

	}

	function printFooter(){
		$this->printJSReload();
	}

}
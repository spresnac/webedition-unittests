<?php

/**
 * webEdition CMS
 *
 * $Rev: 4729 $
 * $Author: arminschulz $
 * $Date: 2012-07-22 12:11:33 +0200 (Sun, 22 Jul 2012) $
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
class rebuildFragment extends taskFragment{

	function __construct($name, $taskPerFragment, $pause = 0, $bodyAttributes = "", $initdata = ""){
		parent::__construct($name, $taskPerFragment, $pause, $bodyAttributes, $initdata);
	}

	function doTask(){
		$this->updateProgressBar();
		we_rebuild::rebuild($this->data);
	}

	function updateProgressBar(){
		$percent = round((100 / count($this->alldata)) * (1 + $this->currentTask));
		print we_html_element::jsElement('if(parent.wizbusy.document.getElementById("progr")){parent.wizbusy.document.getElementById("progr").style.display="";};parent.wizbusy.setProgressText("pb1",(parent.wizbusy.document.getElementById("progr") ? "' . addslashes(shortenPath($this->data["path"], 33)) . '" : "' . g_l('rebuild', "[savingDocument]") . addslashes(shortenPath($this->data["path"], 60)) . '") );parent.wizbusy.setProgress(' . $percent . ');');
		flush();
	}

	function finish(){
		$responseText = isset($_REQUEST["responseText"]) ? $_REQUEST["responseText"] : "";
		
		print we_html_element::jsElement(we_message_reporting::getShowMessageCall(addslashes($responseText ? $responseText : g_l('rebuild', "[finished]")), we_message_reporting::WE_MESSAGE_NOTICE) . '
			top.close();');
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

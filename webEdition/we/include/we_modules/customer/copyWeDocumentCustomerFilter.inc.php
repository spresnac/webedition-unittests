<?php

/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Tue, 06 Nov 2012) $
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
class copyWeDocumentCustomerFilterFrag extends taskFragment{

	function init(){

		// init the fragment
		// REQUEST[we_cmd][1] = id of folder
		// REQUEST[we_cmd][2] = table
		$_id = $_REQUEST['we_cmd'][1];
		$_table = $_REQUEST['we_cmd'][2];

		// if we_cmd 3 is set, take filters of that folder as parent!!
		$_idForFilter = (isset($_REQUEST['we_cmd'][3]) ? $_REQUEST['we_cmd'][3] : $_id);

		if($_id==0){
			t_e('called function with invalid id');
			die();
		}
		$_theFolder = new we_folder();
		$_theFolder->initByID($_id, $_table);

		$_db = new DB_WE();
		// now get all childs of this folder

		$_db->query('SELECT *, ID, ContentType FROM ' . $_db->escape($_table) . ' WHERE	( ContentType = "folder" OR ContentType = "text/webedition" OR ContentType="objectFile" )
			AND PATH LIKE "' . $_theFolder->Path . '/%"');

		$this->alldata = array();

		while($_db->next_record()) {
			array_push(
				$this->alldata, array(
				"folder_id" => $_id,
				"table" => $_table,
				"idForFilter" => $_idForFilter,
				"id" => $_db->f("ID"),
				"contenttype" => $_db->f("ContentType"),
				)
			);
		}
	}

	function doTask(){

		// getFilter of base-folder
		$_theFolder = new we_folder();
		$_theFolder->initByID($this->data["idForFilter"], $this->data["table"]);

		// getTarget-Document
		$_targetDoc = null;
		switch($this->data["contenttype"]){
			case "folder":
				$_targetDoc = new we_folder();
				break;
			case "text/webedition":
				$_targetDoc = new we_webEditionDocument();
				break;
			case "objectFile":
				$_targetDoc = new we_objectFile();
				break;
		}
		$_targetDoc->initById($this->data["id"], $this->data["table"]);

		$_targetDoc->documentCustomerFilter = ($_theFolder->documentCustomerFilter ?
				$_theFolder->documentCustomerFilter :
				weDocumentCustomerFilter::getEmptyDocumentCustomerFilter());

		// write filter to target document
		// save filter
		$_targetDoc->documentCustomerFilter->saveForModel($_targetDoc);
		$_targetDoc->rewriteNavigation();

		print we_html_element::jsElement("parent.setProgressText('copyWeDocumentCustomerFilterText', '" . shortenPath($_targetDoc->Path, 55) . "');
			parent.setProgress(" . number_format(( ( $this->currentTask ) / $this->numberOfTasks) * 100, 0) . ");");
	}

	function finish(){

		print we_html_element::jsElement("
			parent.setProgressText('copyWeDocumentCustomerFilterText', '" . g_l('modules_customerFilter', "[apply_filter_done]") . "');
			parent.setProgress(100);
			" . we_message_reporting::getShowMessageCall(g_l('modules_customerFilter', "[apply_filter_done]"), we_message_reporting::WE_MESSAGE_NOTICE) . "
			window.setTimeout('parent.top.close()', 2000);
		");
	}

}

if(isset($_REQUEST["startCopy"])){ // start the fragment
	$_theFrag = new copyWeDocumentCustomerFilterFrag("copyWeDocumentCustomerFilter", 1, 200);
} else{ // print the window
	// if any childs of the folder are open - bring message to close them
	// REQUEST[we_cmd][1] = id of folder
	// REQUEST[we_cmd][2] = table
	$_id = $_REQUEST['we_cmd'][1];
	$_table = $_REQUEST['we_cmd'][2];

	// if we_cmd 3 is set, take filters of that folder as parent!!
	$_idForFilter = (isset($_REQUEST['we_cmd'][3]) ? $_REQUEST['we_cmd'][3] : $_id);


	$_theFolder = new we_folder();
	$_theFolder->initByID($_id, $_table);

	// now get all childs of this folder
	$_db = new DB_WE();

	$_db->query('SELECT ID, ContentType FROM ' . $_db->escape($_table) . ' WHERE ( ContentType = "folder" OR ContentType = "text/webedition" OR ContentType="objectFile" )
			AND PATH LIKE "' . $_theFolder->Path . '/%"');

	$_allChildsJS = 'var _allChilds = new Object();';

	while($_db->next_record()) {
		$_allChildsJS .= "_allChilds['id_" . $_db->f("ID") . "'] = '" . $_db->f("ContentType") . "';";
	}
	$_js = 'var _openChilds = Array();
			var _usedEditors = top.opener.top.weEditorFrameController.getEditorsInUse();

			for (frameId in _usedEditors) {

				// table muss FILE_TABLE sein
				if ( _usedEditors[frameId].getEditorEditorTable() == "' . $_table . '" ) {
					if ( _allChilds["id_" + _usedEditors[frameId].getEditorDocumentId()] && _allChilds["id_" + _usedEditors[frameId].getEditorDocumentId()] == _usedEditors[frameId].getEditorContentType() ) {
						_openChilds.push( frameId );
					}
				}
			}';

	$pb = new we_progressBar(0, 0, true);
	$pb->addText("&nbsp;", 0, "copyWeDocumentCustomerFilterText");
	$pb->setStudWidth(10);
	$pb->setStudLen(300);
	$js = $pb->getJS() . $pb->getJSCode();

	// image and progressbar
	$content = $pb->getHTML();

	$buttonBar = we_button::create_button("cancel", "javascript:top.close();");

	$_iframeLocation = WEBEDITION_DIR.'we_cmd.php?we_cmd[0]=' . $_REQUEST['we_cmd'][0] . '&we_cmd[1]=' . $_REQUEST['we_cmd'][1] . "&we_cmd[2]=" . $_REQUEST['we_cmd'][2] . (isset($_REQUEST['we_cmd'][3]) ? "&we_cmd[3]=" . $_REQUEST['we_cmd'][3] : "" ) . '&startCopy=1';

	we_html_tools::htmlTop(g_l('modules_customerFilter', '[apply_filter]'));
	print STYLESHEET;
	print we_html_element::jsElement("
		function checkForOpenChilds() {

			$_allChildsJS
			$_js

			if (_openChilds.length) {
				if ( confirm(\"" . g_l('modules_customerFilter', "[apply_filter_cofirm_close]") . "\") ) {
					// close all
					for (i=0;i<_openChilds.length;i++) {
						_usedEditors[_openChilds[i]].setEditorIsHot(false);
						top.opener.top.weEditorFrameController.closeDocument(_openChilds[i]);

					}

				} else {
					window.close();
					return;
				}

			}
			document.getElementById(\"iframeCopyWeDocumentCustomerFilter\").src=\"" . $_iframeLocation . "\";
		}

	");
	print '</head><body class="weDialogBody" onload="checkForOpenChilds()">' .
		$js . we_html_tools::htmlDialogLayout($content, g_l('modules_customerFilter', "[apply_filter]"), $buttonBar) .
		'<div style="display: none;"> <!-- hidden -->
	<iframe style="position: absolute; top: 150; height: 1px; width: 1px;" name="iframeCopyWeDocumentCustomerFilter" id="iframeCopyWeDocumentCustomerFilter" src="about:blank"></iframe>
</div>
</html>';
}

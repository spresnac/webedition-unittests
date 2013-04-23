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
we_html_tools::protect();

$_REQUEST['we_cmd'] = isset($_REQUEST['we_cmd']) ? $_REQUEST['we_cmd'] : "";
$we_transaction = isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : (isset($_REQUEST["we_transaction"]) ? $_REQUEST["we_transaction"] : "");
$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $we_transaction) ? $we_transaction : '');

// init document
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

if(we_workflow_utility::approve($we_doc->ID, $we_doc->Table, $_SESSION["user"]["ID"], "", true)){
	if($we_doc->i_publInScheduleTable()){
		$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][autoschedule]'), date(g_l('date', '[format][default]'), $we_doc->From));
		$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
	} else{
		if($we_doc->we_publish()){
			$we_JavaScript = "_EditorFrame.setEditorDocumentId(" . $we_doc->ID . ");\n" . $we_doc->getUpdateTreeScript() . "\n";
			$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_ok]'), $we_doc->Path);
			$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
			if(($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == WE_EDITPAGE_INFO)){
				$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");'; // wird in Templ eingef�gt
			}
			$we_JavaScript .= "top.weEditorFrameController.getActiveDocumentReference().frames[3].location.reload();_EditorFrame.setEditorDocumentId(" . $we_doc->ID . ");\n" . $we_doc->getUpdateTreeScript() . "\n";
		} else{
			$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_notok]'), $we_doc->Path);
			$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
		}
	}
} else{
	$we_responseText = g_l('modules_workflow', '[' . stripTblPrefix($we_doc->Table) . '][pass_workflow_notok]');
	$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
	//$we_responseText = '';
}

include(WE_INCLUDES_PATH . 'we_templates/we_editor_save.inc.php');
<?php

/**
 * webEdition CMS
 *
 * $Rev: 5787 $
 * $Author: mokraemer $
 * $Date: 2013-02-10 03:02:29 +0100 (Sun, 10 Feb 2013) $
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
// Define needed JS
//$acErrorMsg = we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);
include_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

function getPreferencesFooterJS(){
	$tmp='';
	foreach(array_keys($GLOBALS['tabs']) as $key){
		$tmp.="document.getElementById('content').contentDocument.getElementById('setting_".$key."').style.display = 'none';";
	}
	$_javascript = <<< END_OF_SCRIPT
var countSaveTrys = 0;
function we_save() {
		$tmp
	// update setting for message_reporting
	top.opener.top.messageSettings = document.getElementById('content').contentDocument.getElementById("message_reporting").value;

	if(top.opener.top.weEditorFrameController.getActiveDocumentReference().quickstart){
		var oCockpit=top.opener.top.weEditorFrameController.getActiveDocumentReference();
		var _fo=document.getElementById('content').contentDocument.forms[0];
		var oSctCols=_fo.elements['newconf[cockpit_amount_columns]'];
		var iCols=oSctCols.options[oSctCols.selectedIndex].value;
//		if(iCols!=oCockpit._iLayoutCols){
//			oCockpit.modifyLayoutCols(iCols);
//		}
	}

	document.getElementById('content').contentDocument.getElementById('setting_save').style.display = '';
	document.getElementById('content').contentDocument.we_form.save_settings.value = 'true';

	document.getElementById('content').contentDocument.we_form.submit();
}

END_OF_SCRIPT;
	return we_html_element::jsElement($_javascript);
}

/* * ***************************************************************************
 * RENDER FILE
 * *************************************************************************** */

function getPreferencesFooter(){
	$okbut = we_button::create_button('save', 'javascript:we_save();');
	$cancelbut = we_button::create_button('cancel', 'javascript:top.close()');

	return we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%;'), we_button::position_yes_no_cancel($okbut, "", $cancelbut, 10, "", "", 0));
}
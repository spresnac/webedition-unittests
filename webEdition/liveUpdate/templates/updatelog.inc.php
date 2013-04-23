<?php
/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/*
 * This is the template for tab updatelog. It does not connect to the
 * updateserver
 *
 * This template need the following variables -> set in liveUpdateFunctions
 *
 * allEntries -> amount of all entries, not regarding the filter
 * amountEntries -> amount of all entries regarding the filter
 * amountPerPage -> amount of entries to be shwon on page
 * logEntries -> array of entries for this page
 * amountMessages -> amount of entries of type message
 * amountNotices -> amount of entries of type notice
 * amountErrors -> amount of entries of type notice
 *
 * and in addition the following request variables
 *
 * $_REQUEST['start']
 * $_REQUEST['messages']
 * $_REQUEST['notices']
 * $_REQUEST['errors']
 *
 */

$content = g_l('liveUpdate','[updatelog][logIsEmpty]');
$buttons = '';

/*
 * items found - show them in table
 */
if ( $this->Data['allEntries'] ) { // entries exist

	// there are entries available -> show them in table
	$content = '
<form name="we_form">
<input type="hidden" name="section" value="' . $_REQUEST['section'] .'" />
<input type="hidden" name="log_cmd" value="dummy" />
<input type="hidden" name="start" value="' . $_REQUEST['start'] .'" />
<table class="defaultfont" width="100%">
<tr>
	<td>' . g_l('liveUpdate','[updatelog][entriesTotal]') . ': ' . $this->Data['amountEntries'] . '</td>
	<td class="alignRight">' . g_l('liveUpdate','[updatelog][page]') . ' ' . (($_REQUEST['start'] / $this->Data['amountPerPage']) +1) . '/ ' . ((ceil($this->Data['amountEntries']/$this->Data['amountPerPage'])) ? ceil($this->Data['amountEntries']/$this->Data['amountPerPage']) : 1) . '</td>
</tr>
</table>
<div class="defaultfont">
	' .	we_forms::checkbox(1, isset($_REQUEST['messages']), 'messages', "<span class=\"logMessage\">" . g_l('liveUpdate','[updatelog][legendMessages]') . " (" . $this->Data['amountMessages'] . ")</span>", false, "small", "document.we_form.submit();") . '
	' .	we_forms::checkbox(1, isset($_REQUEST['notices']), 'notices', "<span class=\"logNotice\">" . g_l('liveUpdate','[updatelog][legendNotices]') . " (" . $this->Data['amountNotices'] . ")</span>", false, "small", "document.we_form.submit();") . '
	' .	we_forms::checkbox(1, isset($_REQUEST['errors']), 'errors', "<span class=\"logError\">" . g_l('liveUpdate','[updatelog][legendErrors]') . " (" . $this->Data['amountErrors'] . ")</span>", false, "small", "document.we_form.submit();") . '
</div>
<br />
';

	if (!empty($this->Data['logEntries'])) { // entries match filter

		$content .= '
<table width="100%" class="defaultfont updateContent" id="updateLogTable">
<tr>
	<th>' . g_l('liveUpdate','[updatelog][date]') . '</th>
	<th>' . g_l('liveUpdate','[updatelog][action]') . '</th>
	<th>' . g_l('liveUpdate','[updatelog][version]') . '</th>
</tr>';

		foreach ($this->Data['logEntries'] as $logEntry) {

			$classStr =  ' class="logMessage"';
			if ($logEntry['state'] == 1) {
				$classStr = ' class="logError"';
			} else if ($logEntry['state'] == 2) {
				$classStr = ' class="logNotice"';
			}

			$content .= "
	<tr$classStr>
		<td valign=\"top\">" . $logEntry['date'] . "</td>
		<td>" . $logEntry['action'] . "</td>
		<td valign=\"top\">" . $logEntry['version'] . "</td>
	</tr>";
		}

		/*
		 * Add buttons for next, back and delete
		 */

			if( $_REQUEST['start'] > 0 ){	//	backbutton
				$backButton = we_button::create_button("back", "javascript:lastEntries();");
			} else {
				$backButton = we_button::create_button("back", "#", true, 100, 22, "", "", true);
			}

			if( $this->Data['amountEntries'] <= $_REQUEST['start'] + $this->Data['amountPerPage'] ){	//	next_button
				$nextButton = we_button::create_button("next", "#", true, 100, 22, "", "", true);
			} else {
				$nextButton = we_button::create_button("next", "javascript:nextEntries();");
			}

			$deleteButton = we_button::create_button("delete", "javascript:confirmDelete();");

			$buttons = "<table><tr><td>$deleteButton</td><td>$backButton</td><td>$nextButton</td></tr></table>";

			$content .= '
</table>';

	} else {
		$content .= '
<table class="defaultfont">
<tr>
	<td><br />
		' . g_l('liveUpdate','[updatelog][noEntriesMatchFilter]') . '</td>
</tr>
</table>';
	}
	$content .= '
</form>';
}

$jsHead = we_html_element::jsElement('
	function confirmDelete() {
		if (confirm("' . g_l('liveUpdate','[updatelog][confirmDelete]') . '")) {
			deleteEntries();
		}
	}

	function deleteEntries() {
		document.we_form.log_cmd.value = "deleteEntries";
		document.we_form.submit();
	}

	function lastEntries() {
		document.we_form.log_cmd.value = "lastEntries";
		document.we_form.submit();
	}

	function nextEntries() {
		document.we_form.log_cmd.value = "nextEntries";
		document.we_form.submit();
	}');


print liveUpdateTemplates::getHtml(g_l('liveUpdate','[updatelog][headline]'), $content, $jsHead, $buttons);

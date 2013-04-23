<?php
/**
 * webEdition CMS
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
$ok = false;

if($_SESSION["perms"]["ADMINISTRATOR"]){
	$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : 0);
	// init document
	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];

	include (WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

	$ok = $we_doc->changeTriggerIDRecursive();
}

we_html_tools::htmlTop();

if($ok){
	print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('weClass', "[grant_tid_ok]"), we_message_reporting::WE_MESSAGE_NOTICE));
} else{
	print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('weClass', "[grant_tid_notok]"), we_message_reporting::WE_MESSAGE_ERROR));
}
?>
</head>

<body>
</body>

</html>
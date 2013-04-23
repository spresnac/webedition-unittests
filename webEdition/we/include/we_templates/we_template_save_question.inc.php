<?php
/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
we_html_tools::htmlTop(g_l('global', '[question]'));

$_we_cmd6 = "";
if(isset($_REQUEST['we_cmd'][6])){
	$_we_cmd6 = $_REQUEST['we_cmd'][6];
}

if($nrTemplatesUsedByThisTemplate){
	$alerttext = g_l('alert', "[template_save_warning2]");
} else{
	$alerttext = sprintf((($nrDocsUsedByThisTemplate == 1) ? g_l('alert', "[template_save_warning1]") : g_l('alert', "[template_save_warning]")), $nrDocsUsedByThisTemplate);
}
echo we_html_element::jsScript(JS_DIR . 'keyListener.js');
?>
<script type="text/javascript"><!--

	// functions for keyBoard Listener
	function applyOnEnter() {
		pressed_yes_button();

	}

	// functions for keyBoard Listener
	function closeOnEscape() {
		pressed_cancel_button();

	}

	function pressed_yes_button() {
		opener.top.we_cmd('save_document','<?php print $we_transaction; ?>',0,1,1,'<?php print str_replace("'", "\\'", $_REQUEST['we_cmd'][5]); ?>',"<?php print $_we_cmd6; ?>");
		opener.top.toggleBusy(1);
		self.close();

	}

	function pressed_no_button() {
		opener.top.we_cmd('save_document','<?php print $we_transaction; ?>',0,1,0,'<?php print str_replace("'", "\\'", $_REQUEST['we_cmd'][5]) ?>',"<?php print $_we_cmd6; ?>");
		opener.top.toggleBusy(1);
		self.close();

	}

	function pressed_cancel_button() {
		self.close();
		opener.top.toggleBusy(0);

	}
	self.focus();
//-->
</script>
<?php print STYLESHEET; ?>
</head>
<body class="weEditorBody" onBlur="self.focus()">
<?php print we_html_tools::htmlYesNoCancelDialog($alerttext, IMAGE_DIR . "alert.gif", true, true, true, "pressed_yes_button()", "pressed_no_button()", "pressed_cancel_button()"); ?>
</body>

</html>

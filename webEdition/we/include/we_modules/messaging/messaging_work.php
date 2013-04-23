<?php
/**
 * webEdition CMS
 *
 * $Rev: 4319 $
 * $Author: mokraemer $
 * $Date: 2012-03-22 19:22:48 +0100 (Thu, 22 Mar 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
	exit();
}

we_html_tools::protect();

we_html_tools::htmlTop();
?>
<script type="text/javascript"><!--
	do_mark_messages = 0;
	last_entry_selected = -1;
	entries_selected = new Array();
	//-->
</script>

</head>
<frameset rows="35,26,1,*" framespacing="0" border="0" frameborder="NO">
	<frame src="<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_search_frame.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>" name="messaging_search" scrolling="no" noresize/>
	<frame src="<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_fv_headers.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>" name="messaging_fv_headers" scrolling="no" noresize/>
	<frame src="<?php echo HTML_DIR ?>msg_white_fr.html" noresize scrolling="no"/>
	<frame src="<?php print WE_MESSAGING_MODULE_DIR; ?>messaging_mfv.php" name="msg_mfv" scrolling="no"/>
</frameset>
<noframes>
	<body background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
	</body>
</noframes>
</html>

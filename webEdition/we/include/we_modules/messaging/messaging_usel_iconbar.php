<?php
/**
 * webEdition CMS
 *
 * $Rev: 4040 $
 * $Author: mokraemer $
 * $Date: 2012-02-15 19:24:09 +0100 (Wed, 15 Feb 2012) $
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
we_html_tools::protect();
print STYLESHEET;
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript"><!--
	function get_selection() {
		parent.do_selupdate();
	}
	//-->
</script>
</head>
<body background="/webEdition/images/edit/editfooterback.gif" style="padding:10px;">
<?php
print we_button::position_yes_no_cancel(we_button::create_button("ok", "javascript:get_selection();"), "", we_button::create_button("cancel", "javascript:parent.close();")
	);
?>
</body>
</html>

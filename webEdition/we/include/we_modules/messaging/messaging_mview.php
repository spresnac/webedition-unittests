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

we_html_tools::htmlTop();

print STYLESHEET;
?>
</head>
<frameset rows="10,5,*" framespacing="0" border="0" frameborder="NO">
	<frame src="<?php echo HTML_DIR ?>aqua_nb.html" scrolling="no" noresize/>
	<frame src="<?php print HTML_DIR ?>msg_white_fr.html" noresize scrolling="no"/>
	<frame src="<?php print HTML_DIR ?>white_inc.html" name="messaging_msg_view" noresize/>
</frameset>
<body>
</body>
</html>

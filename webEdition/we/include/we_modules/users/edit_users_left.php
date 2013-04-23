<?php
/**
 * webEdition CMS
 *
 * $Rev: 4749 $
 * $Author: mokraemer $
 * $Date: 2012-07-23 00:02:51 +0200 (Mon, 23 Jul 2012) $
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
we_html_tools::htmlTop('', DEFAULT_CHARSET);
echo we_html_element::jsScript(JS_DIR . 'images.js');
print STYLESHEET;
?>
</head>
<frameset rows="1,*,40" framespacing="0" border="0" frameborder="NO">
	<frame src="<?php print HTML_DIR ?>whiteWithTopLine.html" scrolling="no" noresize/>
	<frame src="<?php print HTML_DIR ?>white.html" name="user_tree" scrolling="auto" noresize/>
	<frame src="<?php print WE_USERS_MODULE_DIR; ?>edit_users_search.php" name="user_search" scrolling="no" noresize/>
</frameset>
<noframes>
	<body background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
	</body>
</noframes>
</html>

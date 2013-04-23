<?php
/**
 * webEdition CMS
 *
 * $Rev: 4397 $
 * $Author: mokraemer $
 * $Date: 2012-04-09 00:06:42 +0200 (Mon, 09 Apr 2012) $
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
we_html_tools::htmlTop();
print STYLESHEET;
?>
</head>
<body>
	<span class="defaultfont">
		<?php
		print g_l('global', '[load_menu_info]');
		?>
	</span>
</body>
</html>
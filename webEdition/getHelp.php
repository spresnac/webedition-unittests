<?php
/**
 * webEdition CMS
 *
 * $Rev: 5343 $
 * $Author: mokraemer $
 * $Date: 2012-12-12 09:54:14 +0100 (Wed, 12 Dec 2012) $
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
we_html_tools::protect();

echo we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsElement('
	self.location="http://help.webedition.org/index.php?language=' . $GLOBALS["WE_LANGUAGE"] . '";');
?>
</head>

<body bgcolor="white">
</body>
</html>
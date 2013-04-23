<?php
/**
 * webEdition CMS
 *
 * $Rev: 5390 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 01:29:46 +0100 (Thu, 20 Dec 2012) $
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
if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_cmd'][1])){
	exit();
}

we_html_tools::protect();

we_html_tools::htmlTop();

echo we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsElement('url="' . WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(array(
		'we_cmd[0]' => 'save_document',
		'we_cmd[1]' => $_REQUEST['we_cmd'][1],
		'we_cmd[2]' => 1,
		'we_transaction' => $_REQUEST['we_cmd'][1],
		'we_cmd[5]' => $_REQUEST['we_cmd'][5],
		'we_cmd[6]' => (isset($_REQUEST['we_cmd'][6]) ? $_REQUEST['we_cmd'][6] : '')
		), null, '&') .
	'";
new jsWindow(url,"templateSaveQuestion",-1,-1,400,170,true,false,true);
');
?>
</head>
<body>
</body>
</html>

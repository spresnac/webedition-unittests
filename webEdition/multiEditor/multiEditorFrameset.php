<?php
/**
 * webEdition CMS
 *
 * $Rev: 5148 $
 * $Author: dreimamedia $
 * $Date: 2012-11-14 14:34:29 +0100 (Wed, 14 Nov 2012) $
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
we_html_tools::htmlTop();
echo we_html_element::jsElement(<<<EOF
			function we_cmd(){
				var args = "";
				for(var i = 0; i < arguments.length; i++){
					args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
				}
				eval('parent.we_cmd('+args+')');
			}
EOF
	);
?>
	</head>
	<body style="margin:0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;"><?php
$MULTIEDITOR_AMOUNT = (isset($_SESSION) && isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == 'seem') ? 1 : 16;

for($i = 0; $i < $MULTIEDITOR_AMOUNT; $i++){
    //'overflow:hidden;' removed to fix bug #6540
	echo '	<iframe frameBorder="0" style="' . ($i == 0 ? '' : 'display:none;') . 'margin:0px;border:0px;width:100%;height:100%;" src="' . HTML_DIR . 'blank_editor.html" name="multiEditFrame_' . $i . '" id="multiEditFrame_' . $i . '"  noresize ></iframe>';
}
?>
	</body>
</html>
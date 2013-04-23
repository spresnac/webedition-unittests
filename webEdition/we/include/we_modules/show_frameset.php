<?php
/**
 * webEdition CMS
 *
 * $Rev: 5085 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 21:31:15 +0100 (Tue, 06 Nov 2012) $
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
print we_html_element::jsElement('
	function toggleBusy(){
	}
	var makeNewEntry = 0;
	var publishWhenSave = 0;
	var weModuleWindow = true;

function we_cmd() {
		args = "";
		for(var i = 0; i < arguments.length; i++) {
					args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
		}
		eval("top.content.we_cmd("+args+")");
	}
');
print we_html_element::jsScript(JS_DIR . "keyListener.js");
if(isset($_REQUEST['mod']) && !isset($mod)){
	$mod = $_REQUEST['mod'];
}
?>
</head>
<frameset rows="26,*" border="0" framespacing="0" frameborder="no">
	<frame src="<?php print WE_MODULES_DIR; ?>navi.php?mod=<?php echo $mod ?>" name="navi" noresize scrolling="no"/>
	<frame src="<?php print WE_MODULES_DIR; ?>show.php?mod=<?php echo $mod . (empty($_REQUEST['we_cmd'][1]) ? '' : "&msg_param=" . $_REQUEST['we_cmd'][1]) . (isset($_REQUEST['sid']) ? '&sid=' . $_REQUEST['sid'] : '') . (isset($_REQUEST['bid']) ? '&bid=' . $_REQUEST['bid'] : ''); ?>" name="content" noresize scrolling="no"/>
</frameset><noframes></noframes>
<body bgcolor="#ffffff"></body>
</html>
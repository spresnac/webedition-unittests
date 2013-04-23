<?php
/**
 * webEdition CMS
 *
 * $Rev: 4320 $
 * $Author: mokraemer $
 * $Date: 2012-03-23 00:51:46 +0100 (Fri, 23 Mar 2012) $
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
we_html_tools::htmlTop('', '', 5);

$table = isset($table) ? $table : FILE_TABLE;
print STYLESHEET;
?>
<script type="text/javascript"><!--
	function we_cmd(){
		var args = "";
		for(var i = 0; i < arguments.length; i++){
			args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
		}
		eval('parent.we_cmd('+args+')');
	}
	//-->
</script>
</head>
<body style="margin:0px;">
	<div style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;">
		<div style="position:absolute;top:0px;bottom:0px;left:0px;width:24px;overflow: hidden;">
			<iframe frameBorder="0" src="<?php print WEBEDITION_DIR ?>we_vtabs.php" style="border:0px;width:100%;height:100%;overflow: hidden;" name="bm_vtabs"></iframe>
		</div>
		<div style="position:absolute;top:0px;bottom:0px;left:24px;right:0px;border:0px;overflow: hidden;" id="treeFrameDiv">
			<div style="position:absolute;top:0px;height:1px;left:0px;right:0px;overflow: hidden;" id="bm_treeheaderDiv">
				<iframe frameBorder="0" src="<?php print HTML_DIR ?>frameheader.html" name="treeheader" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
			</div>
			<div style="position:absolute;top:1px;bottom:40px;left:0px;right:0px;overflow: hidden;" id="bm_mainDiv">
				<iframe frameBorder="0" src="treeMain.php" name="bm_main" onload="top.start()" style="border:0px;width:100%;height:100%;"></iframe>
			</div>
			<div style="position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;background-repeat:repeat;margin:0px;background-image: url(<?php print EDIT_IMAGE_DIR ?>editfooterback.gif);">
				<?php
				include(WE_INCLUDES_PATH . 'treeInfo.inc.php');
				?>
			</div>

		</div>
	</div>
</body>
</html>
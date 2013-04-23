<?php
/**
 * webEdition CMS
 *
 * $Rev: 5452 $
 * $Author: mokraemer $
 * $Date: 2012-12-27 00:14:19 +0100 (Thu, 27 Dec 2012) $
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
$table = isset($table) ? $table : FILE_TABLE;
?>
<div style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;">
	<div style="position:absolute;top:0px;bottom:0px;left:0px;width:24px;overflow: hidden;background-image: url(<?php print IMAGE_DIR; ?>v-tabs/background.gif);background-repeat:repeat-y;border-top:1px solid black;">
		<?php include(WE_INCLUDES_PATH . 'we_vtabs.inc.php'); ?>
	</div>
	<div style="position:absolute;top:0px;bottom:0px;left:24px;right:0px;border:0px;overflow: hidden;" id="treeFrameDiv">
		<div style="position:absolute;top:0px;height:1px;left:0px;right:0px;overflow: hidden;" id="bm_treeheaderDiv">
			<iframe frameBorder="0" src="<?php print HTML_DIR ?>white.html" name="treeheader" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>
		</div>
		<div style="position:absolute;top:1px;bottom:40px;left:0px;right:0px;overflow: auto;background-color:#F3F7FF" id="bm_mainDiv">
			<?php
			$Tree = new weMainTree('webEdition.php', 'top', 'top.resize.left.tree', 'top.load');
			print $Tree->getHTMLContructX('if(top.treeResized){top.treeResized();}');
			?>
		</div>
		<div style="position:absolute;bottom:0px;height:40px;left:0px;right:0px;overflow: hidden;background-repeat:repeat;margin:0px;background-image: url(<?php print EDIT_IMAGE_DIR ?>editfooterback.gif);">
			<?php
			include(WE_INCLUDES_PATH . 'treeInfo.inc.php');
			?>
		</div>
	</div>
</div>
<?php
echo we_html_element::jsElement(
	we_base_browserDetect::isIE() ? 'window.setTimeout("top.start()", 1000);' :
		'top.start();'
);
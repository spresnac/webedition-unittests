<?php
/**
 * webEdition CMS
 *
 * $Rev: 4319 $
 * $Author: mokraemer $
 * $Date: 2012-03-22 19:22:48 +0100 (Thu, 22 Mar 2012) $
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
if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
	exit();
}
?>
</head>

<?php if(we_base_browserDetect::isGecko()){ ?>
	<frameset cols="180,*" border="1" frameborder="1" id="resizeframeid">
		<frameset rows="0,*" framespacing="0" border="1" frameborder="1">
			<frame src="<?php print HTML_DIR ?>whiteWithTopLine.html" scrolling="no"/>
			<frame src="<?php print HTML_DIR ?>white.html" name="messaging_tree"  scrolling="aoto"/>
		</frameset>
		<frame src="<?php print WE_MESSAGING_MODULE_DIR ?>messaging_right.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>" name="messaging_right"/>
	</frameset>
	<?php } elseif(we_base_browserDetect::isSafari()){
	?>
	<frameset cols="180,*" border="0" frameborder="0" id="resizeframeid">
		<frameset rows="1,*" framespacing="0" border="0" frameborder="NO">
			<frame src="<?php print HTML_DIR ?>whiteWithTopLine.html" scrolling="no" noresize/>
			<frame src="<?php print HTML_DIR ?>white.html" name="messaging_tree" scrolling="no"/>
		</frameset>
		<frame src="<?php print WE_MESSAGING_MODULE_DIR ?>messaging_right.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>" name="messaging_right"/>
	</frameset>
	<?php } else{//IE
	?>
	<frameset cols="180,*" border="0" frameborder="0" id="resizeframeid">
		<frameset rows="1,*" framespacing="0" border="0" frameborder="NO">
			<frame src="<?php print HTML_DIR ?>whiteWithTopLine.html" scrolling="no"/>
			<frame src="<?php print HTML_DIR ?>white.html" name="messaging_tree" scrolling="no" frameborder="0"/>
		</frameset>
		<frame src="<?php print WE_MESSAGING_MODULE_DIR ?>messaging_right.php?we_transaction=<?php echo $_REQUEST['we_transaction'] ?>" name="messaging_right"/>
	</frameset>
<?php } ?>

<noframes>
	<body background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
	</body>
</noframes>

</html>
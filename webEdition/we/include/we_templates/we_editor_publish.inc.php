<?php
/**
 * webEdition CMS
 *
 * $Rev: 5594 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:19:42 +0100 (Sat, 19 Jan 2013) $
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
?><script type="text/javascript"><!--
var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("<?php print $GLOBALS['we_transaction']; ?>");
var _EditorFrameDocumentRef = _EditorFrame.getDocumentReference();
<?php
print $we_JavaScript . ';';
if($we_responseText){ 
	?>top.toggleBusy(0);<?php
	print we_message_reporting::getShowMessageCall($we_responseText, $we_responseTextType);
}
we_html_tools::protect();
if(isset($_REQUEST['we_cmd'][5]) && $_REQUEST['we_cmd'][5] != ''){
	print $_REQUEST['we_cmd'][5];
}
?>
	top.toggleBusy(0);
	//-->
</script>

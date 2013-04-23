<?php
/**
 * webEdition CMS
 *
 * $Rev: 5625 $
 * $Author: mokraemer $
 * $Date: 2013-01-22 21:57:30 +0100 (Tue, 22 Jan 2013) $
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
/*
 * This file is opened by js-function dounload() which is only triggered
 * when webEdition.php is not closed regularily (by using menu -> quit): window.onbeforeunload()
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$GLOBALS['isIncluded'] = true;
include(WE_INCLUDES_PATH . 'we_logout.inc.php');

if(isset($_REQUEST['isopener']) && $_REQUEST['isopener']){
	header("location: " . WEBEDITION_DIR . "index.php");
}
?>

<html>
	<head>
		<script type="text/javascript"><!--
			function closeIt(){
				self.close();
			}
			//-->
		</script>
	</head>
	<body onLoad="self.setTimeout(closeIt,1000);" style="background-color:#386AAB;color:white">
		<?php echo g_l('global', "[irregular_logout]"); ?>
	</body>
</html>
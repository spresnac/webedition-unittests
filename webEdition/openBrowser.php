<?php
/**
 * webEdition CMS
 *
 * $Rev: 4624 $
 * $Author: mokraemer $
 * $Date: 2012-06-29 00:11:17 +0200 (Fri, 29 Jun 2012) $
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

if(is_numeric($_REQUEST["url"])){
	srand((double) microtime() * 1000000);
	$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE Published>0 AND ID=' . intval($_REQUEST["url"]), 'Path', $DB_WE);
	$loc = getServerUrl() . ($path ? $path . '?r=' . rand() : WEBEDITION_DIR . 'notPublished.php');
} else{
	$loc = filter_var($_REQUEST["url"], FILTER_VALIDATE_URL);
}
header('Location: ' . $loc);
we_html_tools::htmlTop();
?>
<meta HTTP-EQUIV="REFRESH" content="1; url=<?php echo $loc; ?>">
</head><body></body></html>

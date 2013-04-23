<?php

/**
 * webEdition CMS
 *
 * $Rev: 5936 $
 * $Author: lukasimhof $
 * $Date: 2013-03-09 21:19:46 +0100 (Sat, 09 Mar 2013) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/*
 * This is the template for tab update. It contains the information screen
 * before searching for an update
 *
 */
if(defined('WE_VERSION_SUPP') && WE_VERSION_SUPP!='release'){
	$alsoBeta='&setTestUpdate=1';			
} else {
	$alsoBeta='';
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");
$searchButton = we_button::create_button('search', $_SERVER['SCRIPT_NAME'] . '?section=update&update_cmd=update&detail=lookForUpdate'.$alsoBeta);
$clientSubVersion = (isset($GLOBALS['LU_Variables']['clientSubVersion']) && $GLOBALS['LU_Variables']['clientSubVersion'] != '0000') ? 
	', SVN-Revision: ' . $GLOBALS['LU_Variables']['clientSubVersion'] : '';

$clientVersionName = (isset($GLOBALS['LU_Variables']['clientVersionName']) && $GLOBALS['LU_Variables']['clientVersionName'] != '') ? 
	$GLOBALS['LU_Variables']['clientVersionName'] : $GLOBALS['LU_Variables']['clientVersion'];


$content = '
<table class="defaultfont" width="100%">
<tr>
	<td>' . g_l('liveUpdate', '[update][actualVersion]') . '</td>
	<td>' . $clientVersionName . ' ('. $GLOBALS['LU_Variables']['clientVersion'] . $clientSubVersion . ')</td>
</tr>
<tr>
	<td>' . g_l('liveUpdate', '[update][lastUpdate]') . '</td>
	<td>' . $this->Data['lastUpdate'] . '</td>
</tr>
<tr>
	<td>
		<br />
		<br />
	</td>
</tr>
<tr>
	<td>' . g_l('liveUpdate', '[update][lookForUpdate]') . '</td>
	<td>' . $searchButton . '</td>
</tr>
</table>
';

print liveUpdateTemplates::getHtml(g_l('liveUpdate', '[update][headline]'), $content);

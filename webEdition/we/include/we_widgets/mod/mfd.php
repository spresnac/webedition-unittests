<?php

/**
 * webEdition CMS
 *
 * $Rev: 5286 $
 * $Author: mokraemer $
 * $Date: 2012-12-03 23:43:10 +0100 (Mon, 03 Dec 2012) $
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
// widget LAST MODIFIED

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();
if(!isset($aCols)){
	$aCols = explode(';', $aProps[3]);
}
$sTypeBinary = $aCols[0];
$bTypeDoc = (bool) $sTypeBinary{0};
$bTypeTpl = (bool) $sTypeBinary{1};
$bTypeObj = (bool) $sTypeBinary{2};
$bTypeCls = (bool) $sTypeBinary{3};
$iDate = intval($aCols[1]);
switch($iDate){
	default:
	break;
	case 1 :
		$timestamp = 'CURDATE()';
		break;
	case 2 :
		$timestamp = '(CURDATE()-INTERVAL 1 WEEK)';
		break;
	case 3 :
		$timestamp = '(CURDATE()-INTERVAL 1 MONTH)';
		break;
	case 4 :
		$timestamp = '(CURDATE()-INTERVAL 1 YEAR)';
		break;
}
$iNumItems = $aCols[2];
switch($iNumItems){
	case 0 :
		$iMaxItems = 200;
		break;
	case 11 :
		$iMaxItems = 15;
		break;
	case 12 :
		$iMaxItems = 20;
		break;
	case 13 :
		$iMaxItems = 25;
		break;
	case 14 :
		$iMaxItems = 50;
		break;
	default :
		$iMaxItems = $iNumItems;
}
$sDisplayOpt = $aCols[3];
$bMfdBy = $sDisplayOpt{0};
$bDateLastMfd = $sDisplayOpt{1};
$aUsers = makeArrayFromCSV($aCols[4]);

$_where = array();
$_ws = array();
$_users_where = array();
foreach($aUsers as $uid){
	$_users_where[] = '"' . basename(id_to_path($uid, USER_TABLE)) . '"';
}

if($bTypeDoc && we_hasPerm('CAN_SEE_DOCUMENTS') && defined("FILE_TABLE")){
	$_where[] = '"' . stripTblPrefix(FILE_TABLE) . '"';
	$_ws[FILE_TABLE] = get_ws(FILE_TABLE);
}
if($bTypeObj && we_hasPerm('CAN_SEE_OBJECTFILES') && defined("OBJECT_FILES_TABLE")){
	$_where[] = '"' . stripTblPrefix(OBJECT_FILES_TABLE) . '"';
	$_ws[OBJECT_FILES_TABLE] = get_ws(OBJECT_FILES_TABLE);
}
if($bTypeTpl && we_hasPerm('CAN_SEE_TEMPLATES') && defined("TEMPLATES_TABLE") && $_SESSION['weS']['we_mode'] != "seem"){
	$_where[] = '"' . stripTblPrefix(TEMPLATES_TABLE) . '"';
}
if($bTypeCls && we_hasPerm('CAN_SEE_OBJECTS') && defined("OBJECT_TABLE") && $_SESSION['weS']['we_mode'] != "seem"){
	$_where[] = '"' . stripTblPrefix(OBJECT_TABLE) . '"';
}

$_whereSeem =($_SESSION['weS']['we_mode'] == "seem")? " AND ContentType!='folder' ":'';

$lastModified = '<table cellspacing="0" cellpadding="0" border="0">';
$_count = 10;
$i = $j = $k = 0;
$_db = new DB_WE();
while($j < $iMaxItems) {
	$DB_WE->query('SELECT DID,UserName,DocumentTable,MAX(ModDate) AS m FROM ' . HISTORY_TABLE . (!empty($_where) ? (' WHERE ' . ((count($_users_where) > 0) ? 'UserName IN (' . implode(',', $_users_where) . ') AND ' : '') . 'DocumentTable IN(' . implode(',', $_where) . ')') : '') . (isset($timestamp) ? ' AND ModDate >=' . $timestamp : '') . $_whereSeem . ' GROUP BY DID,DocumentTable  ORDER BY m DESC LIMIT ' . ($k++ * $_count) . ' , ' . ($_count));
	$num_rows = $DB_WE->num_rows();
	if($num_rows == 0){
		break;
	}
	while($DB_WE->next_record()) {
		$_table = TBL_PREFIX . $DB_WE->f('DocumentTable');
		$_paths = array();
		$_bool_ot = (defined('OBJECT_TABLE')) ? (($_table != OBJECT_TABLE) ? true : false) : true;
		if(!we_hasPerm('ADMINISTRATOR') || ($_table != TEMPLATES_TABLE && $_bool_ot)){
			if(isset($_ws[$_table])){
				$_wsa = makeArrayFromCSV($_ws[$_table]);
				foreach($_wsa as $_id){
					$_paths[] = 'Path LIKE ("' . $DB_WE->escape(id_to_path($_id, $_table)) . '%")';
				}
			}
		}
		$_hash = getHash('SELECT ID,Path,Icon,Text,ContentType,ModDate,CreatorID,Owners,RestrictOwners FROM ' . $DB_WE->escape($_table) . ' WHERE ID = ' . $DB_WE->f('DID') . (!empty($_paths) ? (' AND (' . implode(' OR ', $_paths) . ')') : '') . ' ORDER BY ModDate LIMIT 1', $_db);
		if(!empty($_hash)){
			$_show = true;
			$_bool_oft = (defined('OBJECT_FILES_TABLE')) ? (($_table == OBJECT_FILES_TABLE) ? true : false) : true;

			if($_table == FILE_TABLE || $_bool_oft){
				$_show = we_history::userHasPerms($_hash['CreatorID'], $_hash['Owners'], $_hash['RestrictOwners']);
			}
			if($_show){
				if($i + 1 <= $iMaxItems){
					++$i;
					++$j;
					$lastModified .= '<tr><td width="20" height="20" valign="middle" nowrap><img src="' . ICON_DIR . $_hash['Icon'] . '" />' . we_html_tools::getPixel(4, 1) . '</td>' .
						'<td valign="middle" class="middlefont">' .
						'<a href="javascript:top.weEditorFrameController.openDocument(\'' . $_table . '\',\'' . $_hash['ID'] . '\',\'' . $_hash['ContentType'] . '\');" title="' . $_hash['Path'] . '" style="color:#000000;text-decoration:none;">' . $_hash['Path'] . "</a></td>";
					if($bMfdBy){
						$lastModified .= '<td>' . we_html_tools::getPixel(5, 1) . '</td><td class="middlefont" nowrap>' . $DB_WE->f("UserName") . (($bDateLastMfd) ? ',' : '') . '</td>';
					}
					if($bDateLastMfd){
						$lastModified .= '<td>' . we_html_tools::getPixel(5, 1) . '</td><td class="middlefont" nowrap>' . date(g_l('date', '[format][default]'), $_hash['ModDate']) . '</td>';
					}
					$lastModified .= '</tr>';
				} else{
					break;
				}
			} else{
				$j++;
			}
		} else{
			break;
		}
	}
}

$lastModified .= '</table>';

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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
include_once ($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/core/autoload.php');

$isUTF8 = $GLOBALS['WE_BACKENDCHARSET'] == 'UTF-8';

$translate = we_core_Local::addTranslation('default.xml', 'toolfactory');

$perm_group_name = "toolfactory";
$perm_group_title[$perm_group_name] = $isUTF8 ? $translate->_('toolfactory') : utf8_decode($translate->_('toolfactory'));

$perm_values[$perm_group_name] = array(
	"USE_APP_TOOLFACTORY", "NEW_APP_TOOLFACTORY", "DELETE_APP_TOOLFACTORY", "EDIT_APP_TOOLFACTORY", "PUBLISH_APP_TOOLFACTORY", "GENTOC_APP_TOOLFACTORY"
);

$perm_titles[$perm_group_name] = array();

$translated = array(
	$translate->_('The user is allowed to use toolfactory'),
	$translate->_('The user is allowed to create new items in toolfactory'),
	$translate->_('The user is allowed to delete items from toolfactory'),
	$translate->_('The user is allowed to edit items toolfactory'),
	$translate->_('The user is allowed to publish items toolfactory'),
	$translate->_('The user is allowed to regenerate the application toc.xml')
);

foreach($translated as $i => $value){
	$perm_titles[$perm_group_name][$perm_values[$perm_group_name][$i]] = $isUTF8 ? $value : utf8_decode($value);
}

$perm_defaults[$perm_group_name] = array(
	"USE_APP_TOOLFACTORY" => 1, "NEW_APP_TOOLFACTORY" => 1, "DELETE_APP_TOOLFACTORY" => 0, "EDIT_APP_TOOLFACTORY" => 0, "PUBLISH_APP_TOOLFACTORY" => 0, "GENTOC_APP_TOOLFACTORY" => 0
);

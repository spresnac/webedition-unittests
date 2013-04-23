<?php

/**
 * webEdition CMS
 *
 * $Rev: 5830 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:49:00 +0100 (Sun, 17 Feb 2013) $
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
$perm_group_name = "customer";

$perm_group_title[$perm_group_name] = g_l('perms_customer', "[perm_group_title]");

$perm_values[$perm_group_name] = array(
	'NEW_CUSTOMER',
	'DELETE_CUSTOMER',
	'EDIT_CUSTOMER',
	'SHOW_CUSTOMER_ADMIN',
	'CUSTOMER_PASSWORD_VISIBLE',
	'CUSTOMER_AUTOLOGINID_VISIBLE',
	'CAN_EDIT_CUSTOMERFILTER',
	'CAN_CHANGE_DOCS_CUSTOMER');

//	Here the array of the permission-titles is set.
$perm_titles[$perm_group_name] = array();

foreach($perm_values[$perm_group_name] as $cur){
	$perm_titles[$perm_group_name][$cur] = g_l('perms_' . $perm_group_name, '[' . $cur . ']');
}

$perm_defaults[$perm_group_name] = array(
	'NEW_CUSTOMER' => 0,
	'DELETE_CUSTOMER' => 0,
	'EDIT_CUSTOMER' => 0,
	'SHOW_CUSTOMER_ADMIN' => 0,
	'CUSTOMER_PASSWORD_VISIBLE' => 0,
	'CUSTOMER_AUTOLOGINID_VISIBLE' => 0,
	'CAN_EDIT_CUSTOMERFILTER' => 1,
	'CAN_CHANGE_DOCS_CUSTOMER' => 1);

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software, you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_javamenu
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$we_menu_customer = array(
	'000100' => array(
		'text' => g_l('modules_customer', '[menu_customer]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('modules_customer', '[menu_new]'),
		'parent' => '000100',
		'cmd' => 'new_customer',
		'perm' => 'NEW_CUSTOMER || ADMINISTRATOR',
		'enabled' => '1',
	),
	'000400' => array(
		'text' => g_l('modules_customer', '[menu_save]'),
		'parent' => '000100',
		'cmd' => 'save_customer',
		'perm' => 'EDIT_CUSTOMER || NEW_CUSTOMER || ADMINISTRATOR',
		'enabled' => '1',
	),
	'000600' => array(
		'text' => g_l('modules_customer', '[menu_delete]'),
		'parent' => '000100',
		'cmd' => 'delete_customer',
		'perm' => 'DELETE_CUSTOMER || ADMINISTRATOR',
		'enabled' => '1',
	),
	'000700' => array('parent' => '000100',), // separator
	'000800' => array(
		'text' => g_l('modules_customer', '[menu_admin]'),
		'parent' => '000100',
		'enabled' => '0',
	),
	'000810' => array(
		'text' => g_l('modules_customer', '[field_admin]') . '&hellip;',
		'parent' => '000800',
		'cmd' => 'show_admin',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
		'enabled' => '1',),
	'000820' => array(
		'text' => g_l('modules_customer', '[sort_admin]') . '&hellip;',
		'parent' => '000800',
		'cmd' => 'show_sort_admin',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
		'enabled' => '1',),
	'000850' => array('parent' => '000100',), // separator
	'000860' => array(
		'text' => g_l('modules_customer', '[import]') . '&hellip;',
		'parent' => '000100',
		'cmd' => 'import_customer',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
		'enabled' => '1',
	),
	'000870' => array(
		'text' => g_l('modules_customer', '[export]') . '&hellip;',
		'parent' => '000100',
		'cmd' => 'export_customer',
		'perm' => 'SHOW_CUSTOMER_ADMIN || ADMINISTRATOR',
		'enabled' => '1',
	),
	'000900' => array('parent' => '000100',), // separator
	'000910' => array(
		'text' => g_l('modules_customer', '[search]') . '&hellip;',
		'parent' => '000100',
		'cmd' => 'show_search',
		'perm' => '',
		'enabled' => '1',
	),
	'000920' => array(
		'text' => g_l('modules_customer', '[settings]') . '&hellip;',
		'parent' => '000100',
		'cmd' => 'show_customer_settings',
		'perm' => '',
		'enabled' => '1',
	),
	'000950' => array('parent' => '000100',), // separator
	'001000' => array(
		'text' => g_l('modules_customer', '[menu_exit]'),
		'parent' => '000100',
		'cmd' => 'exit_customer',
		'perm' => '',
		'enabled' => '1',
	),
	'001100' => array(
		'text' => g_l('modules_customer', '[menu_help]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'001200' => array(
		'text' => g_l('modules_customer', '[menu_help]') . '&hellip;',
		'parent' => '001100',
		'cmd' => 'help_modules',
		'perm' => '',
		'enabled' => '1',
	),
	'001300' => array(
		'text' => g_l('modules_customer', '[menu_info]') . '&hellip;',
		'parent' => '001100',
		'cmd' => 'info_modules',
		'perm' => '',
		'enabled' => '1',
	),
);
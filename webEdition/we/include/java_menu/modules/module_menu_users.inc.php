<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
 * @package    webEdition_javamenu
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$we_menu_users = array(
	'000100' => array(
		'text' => g_l('javaMenu_users', '[menu_user]'),
		'parent' => '000000',
		'enabled' => '0',
	),
	'000150' => array(
		'text' => g_l('javaMenu_users', '[menu_new]'),
		'parent' => '000100',
		'enabled' => '0',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_user]'),
		'parent' => '000150',
		'cmd' => 'new_user',
		'perm' => 'NEW_USER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_save]'),
		'parent' => '000100',
		'cmd' => 'save_user',
		'perm' => 'NEW_GROUP || NEW_USER || SAVE_USER || SAVE_GROUP || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_delete]'),
		'parent' => '000100',
		'cmd' => 'delete_user',
		'perm' => 'DELETE_USER || DELETE_GROUP || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'parent' => '000100', // separator
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_exit]'),
		'parent' => '000100',
		'cmd' => 'exit_users',
		'enabled' => '1',
	),
	'001500' => array(
		'text' => g_l('javaMenu_users', '[menu_help]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_help]') . '&hellip;',
		'parent' => '001500',
		'cmd' => 'help_modules',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_info]') . '&hellip;',
		'parent' => '001500',
		'cmd' => 'info_modules',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_users', '[menu_alias]'),
		'parent' => '000150',
		'cmd' => 'new_alias',
		'perm' => 'NEW_USER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'parent' => '000150', // separator
	),
	array(
		'text' => g_l('javaMenu_users', '[group]'),
		'parent' => '000150',
		'cmd' => 'new_group',
		'perm' => 'NEW_GROUP || ADMINISTRATOR',
		'enabled' => '0',
	)
);
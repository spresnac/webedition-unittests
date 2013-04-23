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
$we_menu_voting = array(
	'000100' => array(
		'text' => g_l('modules_voting', '[voting]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('modules_voting', '[menu_new]'),
		'parent' => '000100',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_voting', '[voting]'),
		'parent' => '000200',
		'cmd' => 'new_voting',
		'perm' => 'NEW_VOTING || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_voting', '[group]'),
		'parent' => '000200',
		'cmd' => 'new_voting_group',
		'perm' => 'NEW_VOTING || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_voting', '[menu_save]'),
		'parent' => '000100',
		'cmd' => 'save_voting',
		'perm' => 'EDIT_VOTING || NEW_VOTING || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_voting', '[menu_delete]'),
		'parent' => '000100',
		'cmd' => 'delete_voting',
		'perm' => 'DELETE_VOTING || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'parent' => '000100', // separator
	),
	array(
		'text' => g_l('modules_voting', '[menu_exit]'),
		'parent' => '000100',
		'cmd' => 'exit_voting',
		'perm' => '',
		'enabled' => '1',
	),
	'001100' => array(
		'text' => g_l('modules_voting', '[menu_help]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_voting', '[menu_help]') . '&hellip;',
		'parent' => '001100',
		'cmd' => 'help_modules',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_voting', '[menu_info]') . '&hellip;',
		'parent' => '001100',
		'cmd' => 'info_modules',
		'perm' => '',
		'enabled' => '1',
	)
);
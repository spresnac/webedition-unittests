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
$we_menu_export = array(
	'000100' => array(
		'text' => g_l('export', '[export]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('export', '[new]'),
		'parent' => '000100',
		'enabled' => '0',
	),
	array(
		'text' => g_l('export', '[export]'),
		'cmd' => 'new_export',
		'perm' => 'NEW_EXPORT || ADMINISTRATOR',
		'parent' => '000200',
		'enabled' => '0',
	),
	array(
		'text' => g_l('export', '[group]'),
		'cmd' => 'new_export_group',
		'perm' => 'NEW_EXPORT || ADMINISTRATOR',
		'parent' => '000200',
		'enabled' => '0',
	),
	array(
		'text' => g_l('export', '[save]'),
		'parent' => '000100',
		'cmd' => 'save_export',
		'perm' => 'NEW_EXPORT || EDIT_EXPORT || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'text' => g_l('export', '[delete]'),
		'parent' => '000100',
		'cmd' => 'delete_export',
		'perm' => 'DELETE_EXPORT || ADMINISTRATOR',
		'enabled' => '0',
	),
	'000500' => array(
		'parent' => '000100', // separator
	),
	array(
		'text' => g_l('export', '[quit]'),
		'parent' => '000100',
		'cmd' => 'exit_export',
		'enabled' => '1',
	),
	'004000' => array(
		'text' => g_l('export', '[help]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('export', '[help]') . '&hellip;',
		'parent' => '004000',
		'cmd' => 'help_modules',
		'enabled' => '1',
	),
	array(
		'text' => g_l('export', '[info]') . '&hellip;',
		'parent' => '004000',
		'cmd' => 'info_modules',
		'enabled' => '1',
	),
);
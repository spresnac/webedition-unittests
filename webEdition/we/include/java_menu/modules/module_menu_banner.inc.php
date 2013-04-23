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
$we_menu_banner = array(
	'000100' => array(
		'text' => g_l('modules_banner', '[banner]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('modules_banner', '[new]'),
		'parent' => '000100',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_banner', '[banner]'),
		'cmd' => 'new_banner',
		'perm' => 'NEW_BANNER || ADMINISTRATOR',
		'parent' => '000200',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_banner', '[bannergroup]'),
		'cmd' => 'new_bannergroup',
		'perm' => 'NEW_BANNER || ADMINISTRATOR',
		'parent' => '000200',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_banner', '[save]'),
		'parent' => '000100',
		'cmd' => 'save_banner',
		'perm' => 'EDIT_BANNER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_banner', '[delete]'),
		'parent' => '000100',
		'cmd' => 'delete_banner',
		'perm' => 'DELETE_BANNER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'parent' => '000100', // separator
	),
	array(
		'text' => g_l('modules_banner', '[quit]'),
		'parent' => '000100',
		'cmd' => 'exit_banner',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_banner', '[options]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_banner', '[defaultbanner]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'default_banner',
		'perm' => 'EDIT_BANNER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_banner', '[bannercode]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'banner_code',
		'perm' => 'EDIT_BANNER || ADMINISTRATOR',
		'enabled' => '0',
	),
	'004000' => array(
		'text' => g_l('modules_banner', '[help]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_banner', '[help]') . '&hellip;',
		'parent' => '004000',
		'cmd' => 'help_modules',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_banner', '[info]') . '&hellip;',
		'parent' => '004000',
		'cmd' => 'info_modules',
		'enabled' => '1',
	),
);
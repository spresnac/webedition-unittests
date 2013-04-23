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
$we_menu_newsletter = array(
	'000100' => array(
		'text' => g_l('modules_newsletter', '[newsletter]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('modules_newsletter', '[new]'),
		'parent' => '000100',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_newsletter', '[newsletter]'),
		'cmd' => 'new_newsletter',
		'perm' => 'NEW_NEWSLETTER || ADMINISTRATOR',
		'parent' => '000200',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_newsletter', '[group]'),
		'cmd' => 'new_newsletter_group',
		'perm' => 'NEW_NEWSLETTER || ADMINISTRATOR',
		'parent' => '000200',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_newsletter', '[save]'),
		'parent' => '000100',
		'cmd' => 'save_newsletter',
		'perm' => 'NEW_NEWSLETTER || EDIT_NEWSLETTER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_newsletter', '[delete]'),
		'parent' => '000100',
		'cmd' => 'delete_newsletter',
		'perm' => 'DELETE_NEWSLETTER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'parent' => '000100', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[send]') . '&hellip;',
		'parent' => '000100',
		'cmd' => 'send_newsletter',
		'perm' => 'SEND_NEWSLETTER || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'parent' => '000100', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[quit]'),
		'parent' => '000100',
		'cmd' => 'exit_newsletter',
		'enabled' => '1',
	),
	'002000' => array(
		'text' => g_l('modules_newsletter', '[options]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[domain_check]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'domain_check',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[lists_overview_menu]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'print_lists',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[show_log]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'show_log',
		'enabled' => '1',
	),
	array(
		'parent' => '002000', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[newsletter_test]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'test_newsletter',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[preview]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'preview_newsletter',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[send_test]'),
		'parent' => '002000',
		'cmd' => 'send_test',
		'perm' => 'SEND_TEST_EMAIL || ADMINISTRATOR',
		'enabled' => '0',
	),
	array(
		'text' => g_l('modules_newsletter', '[search_email]'),
		'parent' => '002000',
		'cmd' => 'search_email',
		'enabled' => '1',
	),
	array(
		'parent' => '002000', // separator
	),
	array(
		'text' => g_l('modules_newsletter', '[edit_file]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'edit_file',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[black_list]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'black_list',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[clear_log]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'clear_log',
		'perm' => 'NEWSLETTER_SETTINGS || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[settings]') . '&hellip;',
		'parent' => '002000',
		'cmd' => 'newsletter_settings',
		'perm' => 'NEWSLETTER_SETTINGS || ADMINISTRATOR',
		'enabled' => '1',
	),
	'004000' => array(
		'text' => g_l('modules_newsletter', '[help]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[help]') . '&hellip;',
		'parent' => '004000',
		'cmd' => 'help_modules',
		'enableadd' => '1',
	),
	array(
		'text' => g_l('modules_newsletter', '[info]') . '&hellip;',
		'parent' => '004000',
		'cmd' => 'info_modules',
		'enabled' => '1',
	)
);
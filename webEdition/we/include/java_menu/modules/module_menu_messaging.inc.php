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
$we_menu_messaging = array(
	'000100' => array(
		'text' => g_l('javaMenu_messaging', '[file]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	'000110' => array(
		'text' => g_l('javaMenu_messaging', '[new]'),
		'parent' => '000100',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[message]') . '&hellip;',
		'cmd' => 'messaging_new_message',
		'parent' => '000110',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[todo]') . '&hellip;',
		'cmd' => 'messaging_new_todo',
		'parent' => '000110',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_new_folder',
		'parent' => '000110',
		'enabled' => '1',
	),
	'000120' => array(
		'text' => g_l('javaMenu_messaging', '[delete]'),
		'parent' => '000100',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_delete_mode_on',
		'parent' => '000120',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[quit]'),
		'cmd' => 'messaging_exit',
		'parent' => '000100',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('javaMenu_messaging', '[edit]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[folder]'),
		'cmd' => 'messaging_edit_folder',
		'parent' => '000200',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[settings]') . '&hellip;',
		'cmd' => 'messaging_settings',
		'parent' => '000200',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[copy]'),
		'cmd' => 'messaging_copy',
		'parent' => '000200',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[cut]'),
		'cmd' => 'messaging_cut',
		'parent' => '000200',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[paste]'),
		'cmd' => 'messaging_paste',
		'parent' => '000200',
		'enabled' => '1',
	),
	'000300' => array(
		'text' => g_l('javaMenu_messaging', '[help]'),
		'parent' => '000000',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[help]') . '&hellip;',
		'parent' => '000300',
		'cmd' => 'help_modules',
		'enabled' => '1',
	),
	array(
		'text' => g_l('javaMenu_messaging', '[info]') . '&hellip;',
		'parent' => '000300',
		'cmd' => 'info_modules',
		'enabled' => '1',
	)
);
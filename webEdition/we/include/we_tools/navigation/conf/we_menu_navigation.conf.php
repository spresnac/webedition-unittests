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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$we_menu_navigation = array(
	'000100' => array(
		'text' => g_l('navigation', '[navigation]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('navigation', '[menu_new]'),
		'parent' => '000100',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => g_l('navigation', '[entry]'),
		'parent' => '000200',
		'cmd' => 'tool_navigation_new',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => g_l('navigation', '[group]'),
		'parent' => '000200',
		'cmd' => 'tool_navigation_new_group',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => g_l('navigation', '[menu_save]'),
		'parent' => '000100',
		'cmd' => 'tool_navigation_save',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => g_l('navigation', '[menu_delete]'),
		'parent' => '000100',
		'cmd' => 'tool_navigation_delete',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'parent' => '000100', // separator
	),
	array(
		'text' => g_l('navigation', '[menu_exit]'),
		'parent' => '000100',
		'cmd' => 'tool_navigation_exit',
		'perm' => '',
		'enabled' => '1',
	),
	/*
	  '001500'=>array(
	  'text'=> g_l('navigation','[menu_options]'),
	  'parent'=> '000000',
	  'perm'=> '',
	  'enabled'=> '1',
	  ),
	  array(

	  'text'=> g_l('navigation','[menu_generate]').'...',
	  'parent'=> '001500',
	  'cmd'=> 'generate_navigation',
	  'perm'=> '',
	  'enabled'=> '1',
	  ),
	  array(

	  'text'=> g_l('navigation','[menu_settings]'),
	  'parent'=> '001500',
	  'cmd'=> 'settings_navigation',
	  'perm'=> '',
	  'enabled'=> '1',
	  ), */

	'002000' => array(
		'text' => g_l('navigation', '[menu_options]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => g_l('navigation', '[menu_highlight_rules]'),
		'parent' => '002000',
		'perm' => '',
		'cmd' => 'tool_navigation_rules',
		'enabled' => '1',
	));
if(defined('CUSTOMER_TABLE')){
	$we_menu_navigation['002200'] = array(
		'text' => g_l('navigation', '[reset_customer_filter]'),
		'parent' => '002000',
		'perm' => 'ADMINISTRATOR',
		'cmd' => 'tool_navigation_reset_customer_filter',
		'enabled' => '1',
	);
}

$we_menu_navigation['003000'] = array(
	'text' => g_l('navigation', '[menu_help]'),
	'parent' => '000000',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_navigation['003100'] = array(
	'text' => g_l('navigation', '[menu_help]') . '&hellip;',
	'parent' => '003000',
	'cmd' => 'help_tools',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_navigation['003200'] = array(
	'text' => g_l('navigation', '[menu_info]') . '&hellip;',
	'parent' => '003000',
	'cmd' => 'info_tools',
	'perm' => '',
	'enabled' => '1',
);
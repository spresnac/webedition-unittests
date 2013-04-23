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
// file
$we_menu_shop = array(
	'100000' => array(
		'text' => g_l('javaMenu_shop', '[menu_user]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'120000' => array(
		'text' => g_l('javaMenu_shop', '[year]'),
		'parent' => '100000',
		'perm' => '',
		'enabled' => '1',
	)
);

// first year
$yearshop = date('Y');

$z = 1;
while($yearshop >= 2002) {

	$menNr = '1' . (20000 + $z);
	$we_menu_shop[$menNr] = array(
		'text' => $yearshop,
		'parent' => '120000',
		'cmd' => 'year' . $yearshop,
		'perm' => '',
		'enabled' => '1',
	);
	$yearshop--;
	$z++;
}

$we_menu_shop['180000'] = array('parent' => '100000'); // separator

$we_menu_shop['190000'] = array(
	'text' => g_l('javaMenu_shop', '[menu_exit]'),
	'parent' => '100000',
	'cmd' => 'exit_shop',
	'perm' => '',
	'enabled' => '1',
);

// edit
$we_menu_shop['200000'] = array(
	'text' => g_l('javaMenu_shop', '[shop_edit]'),
	'parent' => '000000',
	'perm' => 'edit_shop',
	'enabled' => '1',
);

$we_menu_shop['210000'] = array(
	'text' => g_l('javaMenu_shop', '[shop_pref]') . '&hellip;',
	'parent' => '200000',
	'cmd' => 'pref_shop',
	'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['220000'] = array('parent' => '200000'); // separator

$we_menu_shop['230000'] = array(
	'text' => g_l('javaMenu_shop', '[shop_status]') . '&hellip;',
	'parent' => '200000',
	'cmd' => 'edit_shop_status',
	'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['240000'] = array(
	'text' => g_l('javaMenu_shop', '[country_vat]') . '&hellip;',
	'parent' => '200000',
	'cmd' => 'edit_shop_vat_country',
	'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['250000'] = array(
	'text' => g_l('javaMenu_shop', '[edit_vats]') . '&hellip;',
	'parent' => '200000',
	'cmd' => 'edit_shop_vats',
	'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['260000'] = array(
	'text' => g_l('modules_shop', '[shipping][shipping_package]') . '&hellip;',
	'parent' => '200000',
	'cmd' => 'edit_shop_shipping',
	'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['261000'] = array(
	'text' => g_l('modules_shop', '[shipping][payment_provider]') . '&hellip;',
	'parent' => '200000',
	'cmd' => 'payment_val',
	'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['261001'] = array('parent' => '200000'); // separator

$we_menu_shop['262000'] = array(
	'text' => g_l('modules_shop', '[shipping][revenue_view]'),
	'parent' => '200000',
	'cmd' => 'revenue_view',
	'perm' => 'EDIT_SHOP_PREFS || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['270000'] = array('parent' => '200000'); // separator

$we_menu_shop['280000'] = array(
	'text' => g_l('javaMenu_shop', '[order]'),
	'parent' => '200000',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_shop['281000'] = array(
	'text' => g_l('javaMenu_shop', '[add_article_to_order]'),
	'parent' => '280000',
	'cmd' => 'new_article',
	'perm' => 'NEW_SHOP_ARTICLE || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_shop['282000'] = array(
	'text' => g_l('javaMenu_shop', '[delete_order]'),
	'parent' => '280000',
	'cmd' => 'delete_shop',
	'perm' => 'DELETE_SHOP_ARTICLE || ADMINISTRATOR',
	'enabled' => '1',
);

// menu add
$we_menu_shop['300000'] = array(
	'text' => g_l('javaMenu_shop', '[menu_help]'),
	'parent' => '000000',
	'perm' => 'SHOW_HELP',
	'enabled' => '1',
);

$we_menu_shop['310000'] = array(
	'text' => g_l('javaMenu_shop', '[menu_help]') . '&hellip;',
	'parent' => '300000',
	'cmd' => 'help_modules',
	'perm' => 'SHOW_HELP',
	'enabled' => '1',
);

$we_menu_shop['320000'] = array(
	'text' => g_l('javaMenu_shop', '[menu_info]') . '&hellip;',
	'parent' => '300000',
	'cmd' => 'info_modules',
	'enabled' => '1',
);
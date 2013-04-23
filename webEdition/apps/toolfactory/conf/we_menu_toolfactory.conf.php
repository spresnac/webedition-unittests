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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', 'toolfactory');


include_once ('meta.conf.php');

$controller = Zend_Controller_Front::getInstance();
$appName = $controller->getParam('appName');

$we_menu_toolfactory = array(
	'000100' => array(
		'text' => $translate->_('toolfactory'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('New Entry'),
		'parent' => '000100',
		'cmd' => 'app_' . $appName . '_new',
		'perm' => 'NEW_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('Delete Entry/Group.'),
		'parent' => '000100',
		'cmd' => 'app_' . $appName . '_checkdelete',
		'perm' => 'DELETE_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('Generate TGZ-File from App'),
		'parent' => '000100',
		'cmd' => 'app_' . $appName . '_generateTGZ',
		'perm' => 'NEW_APP_TOOLFACTORY || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'parent' => '000100', // separator
	),
	array(
		'text' => $translate->_('Close'),
		'parent' => '000100',
		'cmd' => 'app_' . $appName . '_exit',
		'perm' => '',
		'enabled' => '1',
	),
	'003000' => array(
		'text' => $translate->_('Help'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('Help') . '&hellip;',
		'parent' => '003000',
		'cmd' => 'app_' . $appName . '_help',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('Info') . '&hellip;',
		'parent' => '003000',
		'cmd' => 'app_' . $appName . '_info',
		'perm' => '',
		'enabled' => '1',
	)
);

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once ('meta.conf.php');

$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', '<?php print $TOOLNAME; ?>');

$controller = Zend_Controller_Front::getInstance();
$appName = $controller->getParam('appName');

$_tool = weToolLookup::getToolProperties($appName);
$we_menu_<?php print $TOOLNAME; ?>= array(
	'000100' => array(
		'text' => we_util_Strings::shortenPath($_tool['text'], 40),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => $translate->_('New'),
		'parent' => '000100',
		'perm' => '',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('New Entry'),
		'parent' => '000200',
		'cmd' => 'app_' . $appName . '_new',
		'perm' => 'NEW_APP_<?php print strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('New Folder'),
		'parent' => '000200',
		'cmd' => 'app_' . $appName . '_new_folder',
		'perm' => 'NEW_APP_<?php print strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('Save'),
		'parent' => '000100',
		'cmd' => 'app_' . $appName . '_save',
		'perm' => 'EDIT_APP_<?php print strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
		'enabled' => '1',
	),
	array(
		'text' => $translate->_('Delete'),
		'parent' => '000100',
		'cmd' => 'app_' . $appName . '_delete',
		'perm' => 'DELETE_APP_<?php print strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
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
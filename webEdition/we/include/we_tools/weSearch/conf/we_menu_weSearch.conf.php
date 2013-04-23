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
include_once (WE_INCLUDES_PATH . 'we_tools/weSearch/conf/meta.conf.php');

$we_menu_weSearch = array(
	'000100' => array(
		'text' => g_l('searchtool', '[menu_suche]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'000200' => array(
		'text' => g_l('searchtool', '[menu_new]'),
		'parent' => '000100',
		'perm' => '',
		'enabled' => '1',
	)
);
if(we_hasPerm('CAN_SEE_DOCUMENTS')){
	$we_menu_weSearch['000300'] = array(
		'text' => g_l('searchtool', '[forDocuments]'),
		'parent' => '000200',
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forDocuments',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => '1',
	);
}
if($_SESSION['weS']['we_mode'] != 'seem' && we_hasPerm('CAN_SEE_TEMPLATES')){
	$we_menu_weSearch['000400'] = array(
		'text' => g_l('searchtool', '[forTemplates]'),
		'parent' => '000200',
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forTemplates',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => '1',
	);
}
if(defined('OBJECT_FILES_TABLE') && defined('OBJECT_TABLE') && we_hasPerm('CAN_SEE_OBJECTFILES')){
	$we_menu_weSearch['000500'] = array(
		'text' => g_l('searchtool', '[forObjects]'),
		'parent' => '000200',
		'cmd' => 'tool_' . $metaInfo['name'] . '_new_forObjects',
		'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
		'enabled' => '1',
	);
}
$we_menu_weSearch['000600'] = array(
	'text' => g_l('searchtool', '[menu_advSearch]'),
	'parent' => '000200',
	'cmd' => 'tool_' . $metaInfo['name'] . '_new_advSearch',
	'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
	'enabled' => '1',
);

//'text'=> g_l('searchtool','[menu_new_group]'),
//'parent'=> '000200',
//'cmd'=> 'tool_' . $metaInfo['name'] . '_new_group',
//'perm'=> 'ADMINISTRATOR',
//'enabled'=> '1',


$we_menu_weSearch['000800'] = array(
	'text' => g_l('searchtool', '[menu_save]'),
	'parent' => '000100',
	'cmd' => 'tool_' . $metaInfo['name'] . '_save',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_weSearch['000900'] = array(
	'text' => g_l('searchtool', '[menu_delete]'),
	'parent' => '000100',
	'cmd' => 'tool_' . $metaInfo['name'] . '_delete',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_weSearch['000950'] = array('parent' => '000100'); // separator


$we_menu_weSearch['001000'] = array(
	'text' => g_l('searchtool', '[menu_exit]'),
	'parent' => '000100',
	'cmd' => 'tool_' . $metaInfo['name'] . '_exit',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_weSearch['003000'] = array(
	'text' => g_l('searchtool', '[menu_help]'),
	'parent' => '000000',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_weSearch['003100'] = array(
	'text' => g_l('searchtool', '[menu_help]') . '&hellip;',
	'parent' => '003000',
	'cmd' => 'help_tools',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_weSearch['003200'] = array(
	'text' => g_l('searchtool', '[menu_info]') . '&hellip;',
	'parent' => '003000',
	'cmd' => 'info_tools',
	'perm' => '',
	'enabled' => '1',
);
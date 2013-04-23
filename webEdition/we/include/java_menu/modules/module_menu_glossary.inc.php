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
//
// ---> Menu File / Glossary
//
$we_menu_glossary = array(
	'001000' => array(
		'text' => g_l('modules_glossary', '[glossary]'),
		'parent' => '000000',
		'perm' => '',
		'enabled' => '1',
	),
	'002000' => array(
		'text' => g_l('modules_glossary', '[menu_new]'),
		'parent' => '001000',
		'perm' => '',
		'enabled' => '1',
	)
);
$nr = 300;
$langs = getWeFrontendLanguagesForBackend();
foreach($langs as $key => $language){

	$we_menu_glossary['00' . $nr . '0'] = array(
		'text' => $language,
		'parent' => '002000',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
		'enabled' => '0',
	);
	$parent = '00' . $nr . '0';

	$we_menu_glossary['00' . $nr . '1'] = array(
		'text' => g_l('modules_glossary', '[abbreviation]'),
		'parent' => $parent,
		'cmd' => 'GlossaryXYZnew_glossary_abbreviationXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
		'enabled' => '1',
	);

	$we_menu_glossary['00' . $nr . '2'] = array(
		'text' => g_l('modules_glossary', '[acronym]'),
		'parent' => $parent,
		'cmd' => 'GlossaryXYZnew_glossary_acronymXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
		'enabled' => '1',
	);

	$we_menu_glossary['00' . $nr . '3'] = array(
		'text' => g_l('modules_glossary', '[foreignword]'),
		'parent' => $parent,
		'cmd' => 'GlossaryXYZnew_glossary_foreignwordXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
		'enabled' => '1',
	);

	$we_menu_glossary['00' . $nr . '4'] = array(
		'text' => g_l('modules_glossary', '[link]'),
		'parent' => $parent,
		'cmd' => 'GlossaryXYZnew_glossary_linkXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
		'enabled' => '1',
	);

	$we_menu_glossary['00' . $nr . '5'] = array(
		'text' => g_l('modules_glossary', '[textreplacement]'),
		'parent' => $parent,
		'cmd' => 'GlossaryXYZnew_glossary_textreplacementXYZ$key',
		'perm' => 'NEW_GLOSSARY || ADMINISTRATOR',
		'enabled' => '1',
	);
	$nr++;
}

$we_menu_glossary['005000'] = array(
	'text' => g_l('modules_glossary', '[menu_save]'),
	'parent' => '001000',
	'cmd' => 'save_glossary',
	'perm' => 'EDIT_GLOSSARY || NEW_GLOSSARY || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_glossary['006000'] = array(
	'text' => g_l('modules_glossary', '[menu_delete]'),
	'parent' => '001000',
	'cmd' => 'delete_glossary',
	'perm' => 'DELETE_GLOSSARY || ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_glossary['009500'] = array('parent' => '001000'
); // separator

$we_menu_glossary['020000'] = array(
	'text' => g_l('modules_glossary', '[menu_exit]'),
	'parent' => '001000',
	'cmd' => 'exit_glossary',
	'perm' => '',
	'enabled' => '1',
);
//
// ---> Menu Options
//

$we_menu_glossary['010000'] = array(
	'text' => g_l('modules_glossary', '[menu_options]'),
	'parent' => '000000',
	'perm' => 'ADMINISTRATOR',
	'enabled' => '1',
);

$we_menu_glossary['012000'] = array(
	'text' => g_l('modules_glossary', '[menu_settings]'),
	'parent' => '010000',
	'cmd' => 'glossary_settings',
	'perm' => 'ADMINISTRATOR',
	'enabled' => '1',
);

//
// ---> Menu Help
//

$we_menu_glossary['021000'] = array(
	'text' => g_l('modules_glossary', '[menu_help]'),
	'parent' => '000000',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_glossary['022000'] = array(
	'text' => g_l('modules_glossary', '[menu_help]') . '&hellip;',
	'parent' => '021000',
	'cmd' => 'help_modules',
	'perm' => '',
	'enabled' => '1',
);

$we_menu_glossary['023000'] = array(
	'text' => g_l('modules_glossary', '[menu_info]') . '&hellip;',
	'parent' => '021000',
	'cmd' => 'info_modules',
	'perm' => '',
	'enabled' => '1',
);
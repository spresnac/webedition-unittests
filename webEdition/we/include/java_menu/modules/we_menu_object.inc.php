<?php

/**
 * webEdition CMS
 *
 * $Rev: 5970 $
 * $Author: mokraemer $
 * $Date: 2013-03-18 19:48:31 +0100 (Mon, 18 Mar 2013) $
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
if(defined('OBJECT_TABLE')){

	// File > unpublished objects
	$we_menu['1115000'] = array(
		'text' => g_l('javaMenu_object', '[unpublished_objects]') . '&hellip;',
		'parent' => '1000000',
		'cmd' => 'openUnpublishedObjects',
		'perm' => 'CAN_SEE_OBJECTFILES || ADMINISTRATOR',
		'enabled' => '1',
	);
//  File > open
	// File > open > Object
	$we_menu['1030300'] = array(
		'text' => g_l('javaMenu_object', '[open_object]') . '&hellip;',
		'parent' => '1030000',
		'cmd' => 'open_objectFile',
		'perm' => 'CAN_SEE_OBJECTFILES || ADMINISTRATOR',
		'enabled' => '1',
	);
	if($_SESSION['weS']['we_mode'] == 'normal'){

		// File > Open > Class
		$we_menu['1030400'] = array(
			'text' => g_l('javaMenu_object', '[open_class]') . '&hellip;',
			'parent' => '1030000',
			'cmd' => 'open_object',
			'perm' => 'CAN_SEE_OBJECTS || ADMINISTRATOR',
			'enabled' => '1',
		);

//  File > new
		// File > new > Class
		$we_menu['1010700'] = array(
			'text' => g_l('javaMenu_object', '[class]'),
			'parent' => '1010000',
			'cmd' => 'new_object',
			'perm' => 'NEW_OBJECT || ADMINISTRATOR',
			'enabled' => '1',
		);
		// File > new > directory > objectfolder
		$we_menu['1011003'] = array(
			'text' => g_l('javaMenu_object', '[object_directory]'),
			'parent' => '1011000',
			'cmd' => 'new_objectfile_folder',
			'perm' => 'NEW_OBJECTFILE_FOLDER || ADMINISTRATOR',
			'enabled' => '1',
		);
	}

	// File > new > Object
	$we_menu['1010800'] = array(
		'text' => g_l('javaMenu_object', '[object]'),
		'parent' => '1010000',
		'perm' => 'NEW_OBJECTFILE || ADMINISTRATOR',
		'enabled' => '0',
	);

	// object from which class
	$ac = makeCSVFromArray(getAllowedClasses($GLOBALS['DB_WE']));
	if($ac){
		$GLOBALS['DB_WE']->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . $ac . ') ' : '') . 'ORDER BY Text');
		$nr = 801;
		while($GLOBALS['DB_WE']->next_record()) {

			$we_menu['1010800']['enabled'] = '1';

			$foo = str_replace(array('"', '\''), '', $GLOBALS['DB_WE']->f('Text'));

			$we_menu['1010' . $nr] = array(
				'text' => $foo,
				'parent' => '1010800',
				'cmd' => 'new_ClObjectFile' . $GLOBALS['DB_WE']->f('ID'),
				'perm' => 'NEW_OBJECTFILE || ADMINISTRATOR',
				'enabled' => '1',
			);
			$nr++;
			if($nr == 999){
				break;
			}
		}
	}


	if($_SESSION['weS']['we_mode'] == 'normal'){
		// separator
		$we_menu['1010999'] = array('parent' => '1010000'); // separator
		// File > Delete
		// File > Delete > Objects
		$we_menu['1080300'] = array(
			'text' => g_l('javaMenu_object', '[objects]'),
			'parent' => '1080000',
			'cmd' => 'delete_objectfile',
			'perm' => 'DELETE_OBJECTFILE || ADMINISTRATOR',
			'enabled' => '1',
		);

		// File > Delete > Classes
		$we_menu['1080400'] = array(
			'text' => g_l('javaMenu_object', '[classes]'),
			'parent' => '1080000',
			'cmd' => 'delete_object',
			'perm' => 'DELETE_OBJECT || ADMINISTRATOR',
			'enabled' => '1',
		);

		// File > move
		$we_menu['1090300'] = array(
			'text' => g_l('javaMenu_object', '[objects]'),
			'parent' => '1090000',
			'cmd' => 'move_objectfile',
			'perm' => 'MOVE_OBJECTFILE || ADMINISTRATOR',
			'enabled' => '1',
		);
	}
}

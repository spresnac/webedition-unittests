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
$we_menu_workflow = array(
	"000100" => array(
		"text" => g_l('javaMenu_workflow', '[workflow]'),
		"parent" => "000000",
		"enabled" => "1",
	),
	array(
		"text" => g_l('javaMenu_workflow', '[new]'),
		"cmd" => "new_workflow",
		"perm" => "NEW_WORKFLOW || ADMINISTRATOR",
		"parent" => "000100",
		"enabled" => "0",
	),
	array(
		"text" => g_l('javaMenu_workflow', '[save]'),
		"parent" => "000100",
		"cmd" => "save_workflow",
		"perm" => "EDIT_WORKFLOW || ADMINISTRATOR",
		"enabled" => "0",
	),
	array(
		"text" => g_l('javaMenu_workflow', '[delete]'),
		"parent" => "000100",
		"cmd" => "delete_workflow",
		"perm" => "DELETE_WORKFLOW || ADMINISTRATOR",
		"enabled" => "0",
	),
	array(
		"parent" => "000100", // separator
	),
	/*
	  array(
	  "text"=> g_l('javaMenu_workflow','[reload]'),
	  "parent"=> "000100",
	  "cmd"=> "reload_workflow",
	  "enabled"=> "0",
	  ),
	  $we_menu_workflow["000880"]["parent"] = "000100"; // separator
	 */
	array(
		"text" => g_l('javaMenu_workflow', '[empty_log]') . "&hellip;",
		"parent" => "000100",
		"cmd" => "empty_log",
		"perm" => "EMPTY_LOG || ADMINISTRATOR",
		"enabled" => "0",
	),
	array(
		"parent" => "000100", // separator
	),
	array(
		"text" => g_l('javaMenu_workflow', '[quit]'),
		"parent" => "000100",
		"cmd" => "exit_workflow",
		"enabled" => "1",
	),
	'001500' => array(
		"text" => g_l('javaMenu_workflow', '[help]'),
		"parent" => "000000",
		"enabled" => "1",
	),
	array(
		"text" => g_l('javaMenu_workflow', '[help]') . "&hellip;",
		"parent" => "001500",
		"cmd" => "help_modules",
		"enabled" => "1",
	),
	array(
		"text" => g_l('javaMenu_workflow', '[info]') . "&hellip;",
		"parent" => "001500",
		"cmd" => "info_modules",
		"enabled" => "1",
	)
);
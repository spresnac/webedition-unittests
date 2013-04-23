<?php

/**
 * webEdition CMS
 *
 * $Rev: 5070 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 23:52:42 +0100 (Sun, 04 Nov 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

if(defined("MESSAGING_SYSTEM")){
	include_once(WE_MESSAGING_MODULE_PATH . "we_message.inc.php");

	$_SESSION['weS']['we_data'][$_transact] = array();

	$_we_messaging = new we_messaging($_SESSION['weS']['we_data'][$_transact]);
	$_we_messaging->add_msgobj('we_message');
	$_we_messaging->saveInSession($_SESSION['weS']['we_data'][$_transact]);
	$messaging_text = g_l('javaMenu_moduleInformation', '[messaging][text]') . ":";
	$new_messages = g_l('modules_messaging', "[new_messages]");
	$new_tasks = g_l('modules_messaging', "[new_tasks]");

	$messaging = new we_messaging($_SESSION['weS']['we_data']["we_transaction"]);
	$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
	$messaging->add_msgobj('we_message', 1);
	$messaging->add_msgobj('we_todo', 1);

	$newmsg_count = $messaging->used_msgobjs['we_message']->get_newmsg_count();
	$newtodo_count = $messaging->used_msgobjs['we_todo']->get_newmsg_count();

	$msg_cmd = "javascript:top.we_cmd('messaging_start','message');";
	$todo_cmd = "javascript:top.we_cmd('messaging_start','todo');";
	$msg_button = we_html_element::htmlA(array("href" => $msg_cmd), we_html_element::htmlImg(array("src" => IMAGE_DIR . 'pd/msg/message.gif', "width" => 34, "height" => 34, "border" => 0)));
	$todo_button = we_html_element::htmlA(array("href" => $todo_cmd), we_html_element::htmlImg(array("src" => IMAGE_DIR . 'pd/msg/todo.gif', "width" => 34, "height" => 34, "border" => 0)));
}

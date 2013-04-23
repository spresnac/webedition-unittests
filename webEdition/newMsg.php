<?php

/**
 * webEdition CMS
 *
 * $Rev: 4966 $
 * $Author: mokraemer $
 * $Date: 2012-09-14 23:40:01 +0200 (Fri, 14 Sep 2012) $
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
we_html_tools::protect();

$msg_cmd = "javascript:top.opener.we_cmd('messaging_start', 'message');self.close();";
$todo_cmd = "javascript:top.opener.we_cmd('messaging_start', 'todo');self.close();";

$text = '';
//msg="+newmsg_count+"&todo="+newtodo_count+"&omsg="+oldMsg+"otodo="+oldTodo
$msg = intval($_REQUEST['msg']) - intval($_REQUEST['omsg']);
$todo = intval($_REQUEST['todo']) - intval($_REQUEST['otodo']);

$text =
	($msg > 0 ? sprintf(g_l('modules_messaging', '[newHeaderMsg]'), '<a href="' . $msg_cmd . '">' . $msg, '</a>').'<br/>' : '') .
	($todo > 0 ? sprintf(g_l('modules_messaging', '[newHeaderTodo]'), '<a href="' . $todo_cmd . '">' . $todo, '</a>').'<br/>' : '');
$parts = array(
	array(
		"headline" => we_html_tools::htmlAlertAttentionBox($text, 2, 500, false),
		"html" => '',
		"space" => 10,
		"noline" => 1),
);

$buttons = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0, "class" => "defaultfont", "align" => "right"), 1, 1);
$buttons->setCol(0, 0, null, we_button::create_button("ok", "javascript:self.close();"));
print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			//FIXME: missing title
			we_html_tools::getHtmlInnerHead()
		) .
		STYLESHEET .
		we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlCenter(
				we_multiIconBox::getHTML("", "100%", $parts, 30, $buttons->getHtml())
			)
		)
	);

<?php

/**
 * webEdition CMS
 *
 * $Rev: 4404 $
 * $Author: mokraemer $
 * $Date: 2012-04-13 23:15:29 +0200 (Fri, 13 Apr 2012) $
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
$_messageTbl = new we_html_table(array("border" => 0,
		"cellpadding" => 0,
		"cellspacing" => 0),
		2,
		4);
//	spaceholder
$_messageTbl->setColContent(0, 0, we_html_tools::getPixel(20, 7));
$_messageTbl->setColContent(1, 1, we_html_element::htmlImg(array("src" => IMAGE_DIR . "alert.gif")));
$_messageTbl->setColContent(1, 2, we_html_tools::getPixel(5, 2));
$_messageTbl->setCol(1, 3, array("class" => "defaultfont"), g_l('alert', "[file_no_save_footer]"));


$_head = we_html_element::htmlHead(we_html_element::jsElement('top.toggleBusy(0);') . STYLESHEET);
$_body = we_html_element::htmlBody(array("background" => IMAGE_DIR . "edit/editfooterback.gif",
		"bgcolor" => "white"), $_messageTbl->getHtml());


print we_html_element::htmlDocType() . we_html_element::htmlHtml($_head . $_body);

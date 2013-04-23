<?php

/**
 * webEdition CMS
 *
 * $Rev: 5060 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 16:57:00 +0100 (Sun, 04 Nov 2012) $
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

if(isset($_SESSION['weS']['move_files_nok']) && is_array($_SESSION['weS']['move_files_nok'])){
	$i = 0;

	$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0, "class" => "defaultfont"), 1, 4);
	$i = 0;
	$table->setCol(0, 0, null, we_html_tools::getPixel(10, 10));
	foreach($_SESSION['weS']['move_files_nok'] as $data){
		$table->addRow();
		$i++;
		$table->setCol($i, 0, null, we_html_tools::getPixel(10, 2));
		$table->setCol($i, 1, null, (isset($data["icon"]) ? we_html_element::htmlImg(array("src" => ICON_DIR . $data["icon"])) : ""));
		$table->setCol($i, 2, null, we_html_tools::getPixel(10, 2));
		$table->setCol($i, 3, null, str_replace($_SERVER['DOCUMENT_ROOT'], "", $data["path"]));
	}
	$table->addRow();
	$i++;
	$table->setCol($i, 0, null, we_html_tools::getPixel(10, 10));
}


$parts = array(
	array(
		"headline" => we_html_tools::htmlAlertAttentionBox($_SESSION["move_files_info"], 1, 500),
		"html" => "",
		"space" => 10,
		"noline" => 1),
	array(
		"headline" => "",
		"html" => we_html_element::htmlDiv(array("class" => "blockwrapper", "style" => "width: 475px; height: 350px; border:1px #dce6f2 solid;"), $table->getHtml()),
		"space" => 10),
);

$buttons = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "align" => "right", "border" => 0, "class" => "defaultfont"), 1, 1);
$buttons->setCol(0, 0, null, we_button::create_button("close", "javascript:self.close();"));
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
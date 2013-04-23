<?php

/**
 * webEdition CMS
 *
 * $Rev: 4397 $
 * $Author: mokraemer $
 * $Date: 2012-04-09 00:06:42 +0200 (Mon, 09 Apr 2012) $
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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']) .
	print we_html_element::htmlhtml(
			we_html_element::htmlHead(
				we_html_tools::htmlMetaCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']) .
				we_html_element::jsElement('
					parent.we_wysiwyg_lng = {};
					parent.we_wysiwyg_lng["mozilla_paste"] = "' . g_l('wysiwyg', "[mozilla_paste]") . '";
					parent.we_wysiwyg_lng["cut"] = "' . g_l('wysiwyg', "[cut2]") . '";
					parent.we_wysiwyg_lng["copy"] = "' . g_l('wysiwyg', "[copy2]") . '";
					parent.we_wysiwyg_lng["paste"] = "' . g_l('wysiwyg', "[paste2]") . '";
					parent.we_wysiwyg_lng["inserttable"] = "' . g_l('wysiwyg', "[insert_table]") . '";
					parent.we_wysiwyg_lng["edit_hyperlink"] = "' . g_l('wysiwyg', "[edit_hyperlink]") . '";
					parent.we_wysiwyg_lng["insert_hyperlink"] = "' . g_l('wysiwyg', "[insert_hyperlink]") . '";
					parent.we_wysiwyg_lng["insert_image"] = "' . g_l('wysiwyg', "[insert_image]") . '";
					parent.we_wysiwyg_lng["edit_image"] = "' . g_l('wysiwyg', "[edit_image]") . '";
					parent.we_wysiwyg_lng["inserthorizontalrule"] = "' . g_l('wysiwyg', "[inserthorizontalrule]") . '";
					parent.we_wysiwyg_lng["insertspecialchar"] = "' . g_l('wysiwyg', "[insertspecialchar]") . '";
					parent.we_wysiwyg_lng["insert_table"] = "' . g_l('wysiwyg', "[insert_table]") . '";
					parent.we_wysiwyg_lng["edittable"] = "' . g_l('wysiwyg', "[edit_table]") . '";
					parent.we_wysiwyg_lng["editcell"] = "' . g_l('wysiwyg', "[edit_cell]") . '";
					parent.we_wysiwyg_lng["undo"] = "' . g_l('wysiwyg', "[undo]") . '";
					parent.we_wysiwyg_lng["redo"] = "' . g_l('wysiwyg', "[redo]") . '";
					parent.we_wysiwyg_lng["nothing_selected"] = "' . g_l('wysiwyg', "[nothing_selected]") . '";
					parent.we_wysiwyg_lng["selection_invalid"] = "' . g_l('wysiwyg', "[selection_invalid]") . '";
					parent.we_wysiwyg_lng["no_table_selected"] = "' . g_l('wysiwyg', "[no_table_selected]") . '";

					parent.we_wysiwyg_lng["insertcolumnright"] = "' . g_l('wysiwyg', "[insertcolumnright]") . '";
					parent.we_wysiwyg_lng["insertcolumnleft"] = "' . g_l('wysiwyg', "[insertcolumnleft]") . '";
					parent.we_wysiwyg_lng["insertrowabove"] = "' . g_l('wysiwyg', "[insertrowabove]") . '";
					parent.we_wysiwyg_lng["insertrowbelow"] = "' . g_l('wysiwyg', "[insertrowbelow]") . '";
					parent.we_wysiwyg_lng["deleterow"] = "' . g_l('wysiwyg', "[deleterow]") . '";
					parent.we_wysiwyg_lng["deletecol"] = "' . g_l('wysiwyg', "[deletecol]") . '";
					parent.we_wysiwyg_lng["increasecolspan"] = "' . g_l('wysiwyg', "[increasecolspan]") . '";
					parent.we_wysiwyg_lng["decreasecolspan"] = "' . g_l('wysiwyg', "[decreasecolspan]") . '";
					parent.we_wysiwyg_lng["caption"] = "' . g_l('wysiwyg', "[caption]") . '";
					parent.we_wysiwyg_lng["insert_edit_anchor"] = "' . g_l('wysiwyg', "[insert_edit_anchor]") . '";
					parent.we_wysiwyg_lng["anchor_name"] = "' . g_l('wysiwyg', "[anchor_name]") . '";
					parent.we_wysiwyg_lng["insert_anchor"] = "' . g_l('wysiwyg', "[insert_anchor]") . '";
					parent.we_wysiwyg_lng["edit_anchor"] = "' . g_l('wysiwyg', "[edit_anchor]") . '";

					parent.we_wysiwyg_lng["none"] = "' . g_l('wysiwyg', "[none]") . '";
					parent.we_wysiwyg_lng["hide_borders"] = "' . g_l('wysiwyg', "[hide_borders]") . '";
					parent.we_wysiwyg_lng["visible_borders"] = "' . g_l('wysiwyg', "[visible_borders]") . '";

					parent.we_wysiwyg_lng["formatblock"] = "' . g_l('wysiwyg', "[format2]") . '";
					parent.we_wysiwyg_lng["fontname"] = "' . g_l('wysiwyg', "[fontname2]") . '";
					parent.we_wysiwyg_lng["fontsize"] = "' . g_l('wysiwyg', "[fontsize]") . '";
					parent.we_wysiwyg_lng["applystyle"] = "' . g_l('wysiwyg', "[css_style2]") . '";
					parent.we_wysiwyg_lng["removeformat_warning"] = "' . g_l('wysiwyg', "[removeformat_warning]") . '";
					parent.we_wysiwyg_lng["removetags_warning"] = "' . g_l('wysiwyg', "[removetags_warning]") . '";
				')
			));

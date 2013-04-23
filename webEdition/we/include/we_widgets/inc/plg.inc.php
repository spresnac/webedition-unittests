<?php
/**
 * webEdition CMS
 *
 * $Rev: 3663 $
 * $Author: mokraemer $
 * $Date: 2011-12-27 15:20:42 +0100 (Tue, 27 Dec 2011) $
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

$splitPlg = explode(';', $aProps[3]);
$pLogUrl = base64_decode($splitPlg[1]);
$oTblCont = new we_html_table(array(
	"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
), 1, 1);
$oTblCont->setCol(
		0,
		0,
		null,
		we_html_element::htmlDiv(
				array(

						"id" => "m_" . $iCurrId . "_inline",
						"style" => "width:" . $iWidth . "px;height:" . ($aPrefs[$aProps[0]]["height"] - 25) . "px;overflow:auto;"
				),
				$_pLog->getHtml()));
$aLang = array(
	g_l('cockpit','[pagelogger]') . (($pLogUrl != "") ? " - " . $pLogUrl : ""), ""
);
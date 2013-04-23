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

function getPLogChart($vals)
{
	$_chart = new we_html_table(
			array(

					"width" => "100%",
					"border" => "0",
					"cellpadding" => "0",
					"cellspacing" => "0",
					"class" => "finelinebox"
			),
			count($vals) + 1,
			4);
	$_chart->setCol(
			0,
			0,
			array(
				"height" => 16, "colspan" => 3, "class" => "tablehead"
			),
			we_html_tools::getPixel(2, 5) . we_html_element::htmlImg(
					array(
						"src" => IMAGE_DIR . "pd/bullet_circle.gif", "class" => "bulletCircle"
					)) . we_html_tools::getPixel(2, 5) . g_l('cockpit','['.$vals[0].']'));
	$_chart->setCol(1, 0, array(
		"colspan" => 3
	), we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/blackdot.gif", "width" => "100%", "height" => 1
	)));
	for ($i = 2; $i < count($vals) + 1; $i++) {
		$_chart->setCol($i, 0, array(
			"width" => "53%", "height" => 19, "class" => "boxbg"
		), we_html_tools::getPixel(3, 5) . g_l('cockpit','['.($vals[$i - 1]).']'));
		$_chart->setCol($i, 1, array(
			"width" => "2%", "height" => 19, "class" => "boxbg"
		), ":");
		$_chart->setCol($i, 2, array(
			"width" => "45%", "height" => 19, "class" => "resbg"
		), showme($vals[$i - 1], $GLOBALS['_pLogUrl']));
	}
	return $_chart;
}

function getPLogGraph($gf){

	$_graph = new we_html_table(
			array(

					"width" => "100%",
					"border" => "0",
					"cellpadding" => "0",
					"cellspacing" => "0",
					"class" => "finelinebox"
			),
			1,
			1);
	$_gfDat = showme($gf, $GLOBALS['_pLogUrl']);
	$_graph->setCol(
			0,
			0,
			array(
				"colspan" => 3, "align" => "center", "style" => "background-color:#efefef;"
			),
			we_html_element::htmlImg(
					array(

							"src" => $GLOBALS['_url'] . "vertical-bar-graph.php?data=" . $GLOBALS['_url'] . "data.php%3Fdta=" . urlencode(
									serialize($_gfDat)) . "&config=" . $GLOBALS['_url'] . "config_" . $gf . ".php%3Fgfh=" . base64_encode(
									g_l('cockpit','['.$gf.']'))
					)));
	return $_graph;
}

?>
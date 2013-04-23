<?php

/**
 * webEdition CMS
 *
 * $Rev: 5079 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 16:54:18 +0100 (Tue, 06 Nov 2012) $
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
if(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(dirname(__FILE__), '', __FILE__)){
	exit();
}

list($pad_header_enc, $pad_csv) = explode(',', $aProps[3]);

$_iFrmPadAtts['src'] = WE_INCLUDES_DIR . 'we_widgets/mod/pad.php?' . http_build_query(array('we_cmd[0]' => $pad_csv, 'we_cmd[2]' => 'home', 'we_cmd[3]' => $aProps[1], 'we_cmd[4]' =>
		$pad_header_enc, 'we_cmd[5]' => $iCurrId, 'we_cmd[6]' => $aProps[1], 'we_cmd[7]' => 'home'));
$_iFrmPadAtts['id'] = 'm_' . $iCurrId . '_inline';
$_iFrmPadAtts['style'] = 'width:' . $iWidth . 'px;height:287px';
$_iFrmPadAtts['scrolling'] = 'no';
$_iFrmPadAtts['marginheight'] = '0';
$_iFrmPadAtts['marginwidth'] = '0';
$_iFrmPadAtts['frameborder'] = '0';

$_iFrmPad = str_replace('>', ' allowtransparency="true">', getHtmlTag('iframe', $_iFrmPadAtts, '', true));

$oTblCont = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 1, 1);
$oTblCont->setCol(0, 0, null, $_iFrmPad);
$aLang = array(
	g_l('cockpit', '[notes]') . " - " . base64_decode($pad_header_enc), ""
);

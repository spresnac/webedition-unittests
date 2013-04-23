<?php
/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
we_html_tools::htmlTop();
print STYLESHEET;

$da = ( $GLOBALS["WE_LANGUAGE"] == "Deutsch" ) ? "%d.%m.%y" : "%m/%d/%y";
if(isset($_REQUEST["cid"])){
	$foo = getHash("SELECT Forename,Surname FROM " . CUSTOMER_TABLE . " WHERE ID=" . intval($_REQUEST["cid"]) . "'", $DB_WE);
	$Kundenname = $foo["Forename"] . " " . $foo["Surname"];

//$DB_WE->query("SELECT IntOrderID, Price, IntQuantity, DateShipping,DatePayment FROM ".SHOP_TABLE." WHERE IntCustomerID=$cid GROUP BY IntOrderID ORDER BY IntOrderID");

	$Bestelldaten = '
<table border="0" cellpadding="2" cellspacing="6" width="300">
		<tr><td class="defaultfont" colspan="2"><b>' . g_l('modules_shop', '[bestellung]') . '</b></td>
		<td class="defaultfont"><b>' . g_l('modules_shop', '[datum]') . '</b></td>
		</tr>
		<tr><td colspan=3></tr>';

	$DB_WE->query("SELECT IntOrderID,DateShipping, DATE_FORMAT(DateOrder,'" . $da . "') as orddate, DATE_FORMAT(DateOrder,'%c%Y') as mdate FROM " . SHOP_TABLE . " WHERE IntCustomerID=" . intval($_REQUEST["cid"]) . " GROUP BY IntOrderID ORDER BY IntID DESC");
	while($DB_WE->next_record()) {
//echo "<br>".$DB_WE->f("Price");
		$Bestelldaten .= "<tr><td class='defaultfont'><a href='" . WE_SHOP_MODULE_DIR . "edit_shop_properties.php?bid=" . $DB_WE->f("IntOrderID") . "' class=\"defaultfont\"><b>" . $DB_WE->f("IntOrderID") . ".</b></a></td>".
			"<td class='defaultgray'>" . g_l('modules_shop', '[bestellungvom]') . "</td>".
			"<td class='defaultfont'><a href='" . WE_SHOP_MODULE_DIR . "edit_shop_editorFrameset.php?bid=" . $DB_WE->f("IntOrderID") . "' class=\"defaultfont\" target=\"shop_properties\"><b>" . $DB_WE->f("orddate") . "</b></a></td></tr>";
	}
	$Bestelldaten .= "</table>";
} else{
	$Bestelldaten = g_l('modules_shop', '[keinedaten]');
}
?>
</head>
<body class="weEditorBody" onUnload="doUnload()">
	<?php print we_html_tools::htmlDialogLayout($Bestelldaten, g_l('modules_shop', '[order_liste]') . "&nbsp;" . $Kundenname); ?>
</body></html>

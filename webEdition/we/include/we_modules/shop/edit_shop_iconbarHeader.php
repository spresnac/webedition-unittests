<?php
/**
 * webEdition CMS
 *
 * $Rev: 4040 $
 * $Author: mokraemer $
 * $Date: 2012-02-15 19:24:09 +0100 (Wed, 15 Feb 2012) $
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

print we_html_element::jsElement('

	function doUnload() {
		if (!!jsWindow_count) {
			for (i = 0; i < jsWindow_count; i++) {
				eval("jsWindow" + i + "Object.close()");
			}
		}
	}

	function we_cmd() {

		switch (arguments[0]) {

			case "openOrder":
				if(top.content.shop_tree.window.doClick) {
					top.content.shop_tree.window.doClick(arguments[1], arguments[2], arguments[3]);
				}
			break;

			default:
				// not needed yet
			break;
		}
	}

');

$bid = isset($_REQUEST["bid"]) ? intval($_REQUEST["bid"]) : 0;

$cid = f("SELECT IntCustomerID FROM " . SHOP_TABLE . " WHERE IntOrderID = " . $bid, "IntCustomerID", $DB_WE);
$DB_WE->query("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') as orddate FROM " . SHOP_TABLE . " GROUP BY IntOrderID ORDER BY IntID DESC");

if($DB_WE->next_record()){
	$headline = '<a style="text-decoration: none;" href="javascript:we_cmd(\'openOrder\', ' . $DB_WE->f("IntOrderID") . ',\'shop\',\'' . SHOP_TABLE . '\');">' . sprintf(g_l('modules_shop', '[lastOrder]'), $DB_WE->f("IntOrderID"), $DB_WE->f("orddate")) . '</a>';
} else{
	$headline = "";
}


// grep the last element from the year-set, wich is the current year
$DB_WE->query("SELECT DATE_FORMAT(DateOrder,'%Y') AS DateOrd FROM " . SHOP_TABLE . " ORDER BY DateOrd");
while($DB_WE->next_record()) {
	$strs = array($DB_WE->f("DateOrd"));
	$yearTrans = end($strs);
}
// print $yearTrans;
/// config
$DB_WE->query("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'shop_pref'");
$DB_WE->next_record();
$feldnamen = explode("|", $DB_WE->f("strFelder"));
for($i = 0; $i <= 3; $i++){
	$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
}
$fe = explode(",", $feldnamen[3]);
if(empty($classid)){
	$classid = $fe[0];
}

//$resultO = count($fe);
$resultO = array_shift($fe);


$dbTitlename = "shoptitle";
// wether the resultset ist empty?
$DB_WE->query("SELECT count(Name) as Anzahl FROM " . LINK_TABLE . " WHERE Name ='$dbTitlename'");
$DB_WE->next_record();
$resultD = $DB_WE->f("Anzahl");
?>

<body background="<?php print IMAGE_DIR ?>backgrounds/iconbarBack.gif" marginwidth="0" topmargin="5" marginheight="5" leftmargin="0">
	<table border="0" cellpadding="6" cellspacing="0" style="margin-left:8px">
		<tr>
			<?php echo "<td>" . we_button::create_button("image:btn_shop_extArt", "javascript:top.opener.top.we_cmd('new_article')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>

			<td>
				<?php echo we_button::create_button("image:btn_shop_delOrd", "javascript:top.opener.top.we_cmd('delete_shop')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>
			<?php
			if(($resultD > 0) && (!empty($resultO))){ //docs and objects
				echo "<td>" . we_button::create_button("image:btn_shop_sum", "javascript:top.content.shop_properties.location=' edit_shop_editorFramesetTop.php?typ=document '", true) . "</td>";
			} elseif(($resultD < 1) && (!empty($resultO))){ // no docs but objects
				echo "<td>" . we_button::create_button("image:btn_shop_sum", "javascript:top.content.shop_properties.location=' edit_shop_editorFramesetTop.php?typ=object&ViewClass=$classid '", true) . "</td>";
			} elseif(($resultD > 0) && (empty($resultO))){ // docs but no objects
				echo "<td>" . we_button::create_button("image:btn_shop_sum", "javascript:top.content.shop_properties.location=' edit_shop_editorFramesetTop.php?typ=document '", true) . "</td>";
			} else{
				echo " ";
			}
			?>
			<td>
				<?php echo we_button::create_button("image:btn_shop_pref", "javascript:top.opener.top.we_cmd('pref_shop')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>
			<td>
				<?php echo we_button::create_button("image:btn_payment_val", "javascript:top.opener.top.we_cmd('payment_val')", true, -1, -1, "", "", !we_hasPerm("NEW_USER")); ?></td>
			<?php
			if($headline){
				?>
				<td align="right" class="header_shop"><span style="margin-left:15px"><?php print @$headline; ?></span></td>
				<?php
			}
			?>
		</tr>
	</table>


</body></html>
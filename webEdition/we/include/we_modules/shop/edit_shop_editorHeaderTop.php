<?php
/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Tue, 06 Nov 2012) $
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
if(isset($_REQUEST["home"]) && $_REQUEST["home"]){
	print '<body bgcolor="#F0EFF0"></body></html>';
	exit;
}
include_once(WE_MODULES_PATH . 'shop/handle_shop_dbitemConnect.php');

we_html_tools::protect();

we_html_tools::htmlTop();

print STYLESHEET;

$yid = isset($_REQUEST["ViewYear"]) ? abs($_REQUEST["ViewYear"]) : date("Y");
$bid = isset($_REQUEST["bid"]) ? abs($_REQUEST["bid"]) : 0;
$cid = f("SELECT IntCustomerID FROM " . SHOP_TABLE . " WHERE IntOrderID = " . intval($bid), "IntCustomerID", $DB_WE);
$DB_WE->query("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') as orddate FROM " . SHOP_TABLE . " GROUP BY IntOrderID ORDER BY IntID DESC");
if($DB_WE->next_record()){
	$headline = sprintf(g_l('modules_shop', '[lastOrder]'), $DB_WE->f("IntOrderID"), $DB_WE->f("orddate"));
} else{
	$headline = "";
}

/// config
$DB_WE->query("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'shop_pref'");
$DB_WE->next_record();
$feldnamen = explode("|", $DB_WE->f("strFelder"));
if(isset($feldnamen[3])){
	$fe = explode(",", $feldnamen[3]);
} else{
	$fe = array(0);
}

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

// grep the last element from the year-set, wich is the current year
$DB_WE->query("SELECT DATE_FORMAT(DateOrder,'%Y') AS DateOrd FROM " . SHOP_TABLE . " ORDER BY DateOrd");
while($DB_WE->next_record()) {
	$strs = array($DB_WE->f("DateOrd"));
	$yearTrans = end($strs);
}

/*
  $DB_WE->query("SELECT COUNT(".SHOP_TABLE.".IntID) as db FROM ".SHOP_TABLE." WHERE YEAR(".SHOP_TABLE.".DateOrder) = $yid ");
  while($DB_WE->next_record()){
  $entries = $DB_WE->f("db");

  }
 */
//print $entries;
$we_tabs = new we_tabs();
if(isset($_REQUEST["mid"]) && $_REQUEST["mid"]){
	$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][overview]"), "TAB_ACTIVE", "//"));
} else{
	if(($resultD > 0) && (!empty($resultO))){ //docs and objects
		$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][admin_1]"), "TAB_ACTIVE", "setTab(0);"));
		$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][admin_2]"), "TAB_NORMAL", "setTab(1);"));
	} elseif(($resultD > 0) && (empty($resultO))){ // docs but no objects
		$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][admin_1]"), "TAB_NORMAL", "setTab(0);"));
	} elseif(($resultD < 1) && (!empty($resultO))){ // no docs but objects
		$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][admin_2]"), "TAB_NORMAL", "setTab(1);"));
	}
	if(isset($yearTrans) && $yearTrans != 0){
		$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][admin_3]"), "TAB_NORMAL", "setTab(2);"));
	}
}
$we_tabs->onResize();
$tab_head = $we_tabs->getHeader();
$tab_body = '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;" id="headrow">&nbsp;' . we_html_element::htmlB($headline) . '</div>' . we_html_tools::getPixel(100, 3) .
	$we_tabs->getHTML() .
	'</div>';
?>
<script type="text/javascript">
	<!--
	function setTab(tab){
		switch(tab){
			case 0:
				parent.edbody.document.location = 'edit_shop_article_extend.php?typ=document';
				break;
			case 1:
				parent.edbody.document.location = 'edit_shop_article_extend.php?typ=object&ViewClass=<?php print $classid; ?>';

				break;
<?php
if(isset($yearTrans)){
	?>
						case 2:
							parent.edbody.document.location = 'edit_shop_revenueTop.php?ViewYear=<?php print $yearTrans; ?>' // ' + top.yearshop

							break;
	<?php
}
?>

				}
			}
			top.content.hloaded=1;
			//-->
</script>
<?php
print $tab_head;
?>
<body bgcolor="white" background="<?php print IMAGE_DIR; ?>backgrounds/header_with_black_line.gif" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onLoad="setFrameSize()" onResize="setFrameSize()">
	<?php
	print $tab_body;
	?>
</body>
</html>
<?php
/**
 * webEdition CMS
 *
 * $Rev: 4067 $
 * $Author: mokraemer $
 * $Date: 2012-02-17 12:09:17 +0100 (Fri, 17 Feb 2012) $
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

/**
 * ECONDA implementation
 *
 * In /webEdition/we/include/weClasses/we_template.inc.php is checked if ECONDA is activated and if the ECONDA-JS file is integrated.
 * If it is done the code to include this file in each template before the body-tag will be executed.
 */
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/weTracking/econda/weEmos.class.inc.php");
$emos = new weEmos();
$emosJsCode = "";
//if (isset($_REQUEST["we_oid"]) ||) ) {
if ( isset($_REQUEST["we_oid"]) ||  (!isset($GLOBALS["WE_MAIN_DOC"]) && isset($_REQUEST["we_objectID"])) || (isset($_REQUEST["type"]) && $_REQUEST["type"] == "o") ) {
	// object
	$emosType = "obj";

} else {
	// document
	$emosType = "doc";
}

switch (true){

	case isset($GLOBALS["we_".$emosType]->elements["price"]) :
		// view article
		$emos->weViewArticle($emosType);
		break;

	case isset($_REQUEST["shop_artikelid"]) :
		// add/remove article quanitity to/from shopping cart
		$emos->weAddRemoveArticle($emosType);
		break;

	case isset($_REQUEST["del_shop_artikelid"]) :
		// remove complete article from shopping cart
		$emos->weAddRemoveArticle($emosType, true);
		break;

	case isset($_SESSION['webuser']) && isset($_SESSION['webuser']['MemberSince']) && $_SESSION['webuser']['MemberSince']<1 :
		// user registration
		//$emos->emosRegister();
		break;

	case isset($_REQUEST['s']) && isset($_REQUEST['s']['Username']) :
		// login
		$emos->emosLogin();
		break;
	case isset($GLOBALS['weEconda']) && isset($GLOBALS['weEconda']['emosBasket']):
		// shoping basket
		$emos->emosShopingBasket();
		break;


	/**
	 * @todo billing
	 */
}


echo $emos->getEmosHTMLFooter();
?>
<script type="text/javascript">
//<!--
var emosPageId = "<?php echo $GLOBALS["WE_DOC_ID"]; ?>";
<?php
echo $emos->getEmosJsFooter();
?>
//-->
</script>
<?php
echo we_html_element::jsScript(id_to_path(WE_ECONDA_ID));

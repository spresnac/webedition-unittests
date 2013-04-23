<?php
/**
 * webEdition CMS
 *
 * $Rev: 5051 $
 * $Author: mokraemer $
 * $Date: 2012-11-02 21:40:23 +0100 (Fri, 02 Nov 2012) $
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


$we_tabs = new we_tabs();

$name = array();

//	First sort the array after the text.
function order_available_modules($a, $b){

	return (strcmp($a["text"], $b["text"]));
}

uasort($_we_available_modules, "order_available_modules");


foreach($_we_available_modules as $_menuItem){
	if((isset($_menuItem["inModuleMenu"]) && $_menuItem["inModuleMenu"]) || (isset($_menuItem["inModuleWindow"]) && $_menuItem["inModuleWindow"])){
		if(in_array($_menuItem["name"], $GLOBALS['_we_active_integrated_modules'])){ //	MODULE INSTALLED
			if(we_userCanEditModule($_menuItem["name"])){
				$we_tabs->addTab(new we_tab("#", $_menuItem["text"], ( isset($_REQUEST["mod"]) && $_REQUEST["mod"] == $_menuItem["name"] ? "TAB_ACTIVE" : "TAB_NORMAL"), "openModule('" . $_menuItem["name"] . "');", array("id" => $_menuItem["name"])));
			}
		}
	}
}

$we_tabs->onResize('navi');
$tab_header = $we_tabs->getHeader('_modules', 1);
$tab_js = $we_tabs->getJS();

print $tab_header;
?>


<script type="text/javascript"><!--
	var current = "<?php echo $_REQUEST["mod"]; ?>";
	function openModule(module) {
		if(top.content.hot =="1") {
			if(confirm("<?php print g_l('alert', '[discard_changed_data]') ?>")) {
				if(typeof "top.content.usetHot" == "function") {top.content.usetHot();}
				current = module;
				top.content.location.replace('show.php?mod=' + module);
			} else {
				setActiveTab(current);
			}
		} else {
			if(typeof "top.content.usetHot" == "function") {top.content.usetHot();}
			current = module;
			top.content.location.replace('show.php?mod=' + module);

		}

	}
	//-->
</script>
</head>
<body bgcolor="white" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" background="<?php print IMAGE_DIR; ?>backgrounds/header.gif" link="black" alink="#1559b0" vlink="black" onLoad="setFrameSize()" onResize="setFrameSize()">
	<div id="main" ><?php echo $we_tabs->getHTML(); ?> </div>
</body>
</html>
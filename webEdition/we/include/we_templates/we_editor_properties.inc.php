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
$yuiSuggest = & weSuggest::getInstance();

if($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES){
	//	send charset, if one is set:
	$charset = $we_doc->getElement('Charset');
	$charset = $charset ? $charset : DEFAULT_CHARSET;
} else{
	$charset = $GLOBALS['WE_BACKENDCHARSET'];
}
we_html_tools::headerCtCharset('text/html', $charset);
we_html_tools::htmlTop('', $charset);

echo we_html_element::jsScript(JS_DIR . 'windows.js');
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print STYLESHEET;
?>
</head>
<body class="weEditorBody" onUnload="doUnload()">
	<form name="we_form" method="post" onSubmit="return false;"><?php
echo we_class::hiddenTrans();
$implementYuiAC = false;
switch($we_doc->ContentType){
	case "folder":
		include(WE_INCLUDES_PATH . 'we_templates/we_folder_properties.inc.php');
		$implementYuiAC = true;
		break;
	case "text/webedition":
		include(WE_INCLUDES_PATH . 'we_templates/we_webedition_properties.inc.php');
		break;
	case "text/xml":
	case "text/css":
	case "text/js":
	case "text/htaccess":
	case "text/plain":
		include(WE_INCLUDES_PATH . 'we_templates/we_textfile_properties.inc.php');
		break;
	case "text/html":
		include(WE_INCLUDES_PATH . 'we_templates/we_htmlfile_properties.inc.php');
		break;
	case "text/weTmpl":
		include(WE_INCLUDES_PATH . 'we_templates/we_template_properties.inc.php');
		break;
	case "image/*":
		include(WE_INCLUDES_PATH . 'we_templates/we_image_properties.inc.php');
		break;
	case "application/x-shockwave-flash":
		include(WE_INCLUDES_PATH . 'we_templates/we_flash_properties.inc.php');
		break;
	case "video/quicktime":
		include(WE_INCLUDES_PATH . 'we_templates/we_quicktime_properties.inc.php');
		break;
	case "application/*":
		include(WE_INCLUDES_PATH . 'we_templates/we_other_properties.inc.php');
		break;
	default:

		$moduleDir = we_getModuleNameByContentType($we_doc->ContentType);

		if($moduleDir != ""){
			$moduleDir .= "/";
		}

		if(file_exists(WE_MODULES_PATH . $moduleDir . "we_" . $we_doc->ContentType . "_properties.inc.php")){
			include(WE_MODULES_PATH . $moduleDir . "we_" . $we_doc->ContentType . "_properties.inc.php");
		} else{
			exit("Can NOT include property File");
		}
}
?>
	</form>
	<?php
	echo $yuiSuggest->getYuiCssFiles();
	echo $yuiSuggest->getYuiCss();

	echo $yuiSuggest->getYuiJsFiles();
	echo $yuiSuggest->getYuiJs();
	?>
</body>
</html>
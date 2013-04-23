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
//	send charset, if one is set:
if(isset($we_doc->elements["Charset"]["dat"]) && $we_doc->elements["Charset"]["dat"] && $we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES){
	we_html_tools::headerCtCharset('text/html', $we_doc->elements["Charset"]["dat"]);
}

we_html_tools::htmlTop();
echo we_html_element::jsScript(JS_DIR . 'windows.js');
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print STYLESHEET;
?>
</head>
<body class="weEditorBody" onUnload="doUnload()">
	<form name="we_form" method="post" onsubmit="return false;"><?php
echo we_class::hiddenTrans();

switch($we_doc->ContentType){
	case 'text/webedition':
		include(WE_MODULES_PATH . 'shop/we_editor_variants_webEditionDocument.inc.php');
		break;

	case 'objectFile':
		include(WE_MODULES_PATH . 'shop/we_editor_variants_objectFile.inc.php');
		break;

	case 'text/weTmpl':
		include(WE_MODULES_PATH . 'shop/we_template_variant.inc.php');
		break;

	default:
		print $we_doc->ContentType . ' not available (' . __FILE__ . ' ) ';
		break;
}
?>
	</form>
</body>
</html>
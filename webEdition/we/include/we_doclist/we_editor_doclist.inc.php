<?php

/**
 * webEdition CMS
 *
 * $Rev: 5070 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 23:52:42 +0100 (Sun, 04 Nov 2012) $
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
we_html_tools::protect();

we_html_tools::htmlTop();

echo we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js');

include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

$headCal = we_html_element::cssLink(JS_DIR . "jscalendar/skins/aqua/theme.css") .
	we_html_element::jsScript(JS_DIR . "jscalendar/calendar.js") .
	we_html_element::jsScript(WE_INCLUDES_DIR . "we_language/" . $GLOBALS ["WE_LANGUAGE"] . "/calendar.js") .
	we_html_element::jsScript(JS_DIR . "jscalendar/calendar-setup.js");

$_view = new doclistView ( );

print $headCal .
	$_view->getSearchJS() .
	STYLESHEET .
	'</head>

<body class="weEditorBody" onUnload="doUnload()" onkeypress="javascript:if(event.keyCode==\'13\' || event.keyCode==\'3\') search(true);" onLoad="setTimeout(\'init();\',200)" onresize="sizeScrollContent();">
<div id="mouseOverDivs_doclist"></div>
<form name="we_form" onSubmit="return false;" style="padding:0px;margin:0px;">';

$content = $_view->searchProperties();
$headline = $_view->makeHeadLines();
$foundItems = (isset($_SESSION['weS']['weSearch']['foundItems'])) ? $_SESSION['weS']['weSearch']['foundItems'] : 0;
$_parts = array(
	array("html" => $_view->getSearchDialog()),
	array("html" => "<div id='parametersTop'>" . $_view->getSearchParameterTop($foundItems) . "</div>" . searchtoolView::tblList($content, $headline, "doclist") . "<div id='parametersBottom'>" . $_view->getSearchParameterBottom($foundItems) . "</div>"),
);

echo $_view->getHTMLforDoclist($_parts) . '
</form>
</body>
</html>';

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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

// include autoload function
include_once($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/core/autoload.php');

Zend_Loader::loadClass('we_core_Local');



$title = 'webEdition ';
if(isset($_REQUEST['tool'])){
	switch($_REQUEST['tool']){
		case 'weSearch':
			$title .= g_l('tools', '[tools]') . ' - ' . g_l('searchtool', '[weSearch]');
			break;
		case 'navigation':
			$title .= g_l('tools', '[tools]') . ' - ' . g_l('navigation', '[navigation]');
			break;
		default:
			$translate = we_core_Local::addTranslation('apps.xml');
			we_core_Local::addTranslation('default.xml', $_REQUEST['tool']);
			$title .= $translate->_('Applications') . ' - ' . $translate->_($_REQUEST['tool']);
			break;
	}
}

we_html_tools::htmlTop($title);

print we_html_element::jsElement('
	top.weToolWindow = true;

	function toggleBusy(){
	}
	var makeNewEntry = 0;
	var publishWhenSave = 0;


	function we_cmd() {
		args = "";
		for(var i = 0; i < arguments.length; i++) {
					args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
		}
		eval("top.content.we_cmd("+args+")");
	}
');

if($_REQUEST['tool'] == "weSearch"){
	if(isset($_REQUEST['we_cmd'][1])){
		$_SESSION['weS']['weSearch']["keyword"] = $_REQUEST['we_cmd'][1];
	}
	//look which search is activ
	if(isset($_REQUEST['we_cmd'][2])){
		$table = $_REQUEST['we_cmd'][2];
		switch($table){
			case FILE_TABLE:
				$tab = 1;
				$_SESSION['weS']['weSearch']["checkWhich"] = 1;
				break;
			case TEMPLATES_TABLE:
				$tab = 2;
				$_SESSION['weS']['weSearch']["checkWhich"] = 2;
				break;
			case (defined("OBJECT_FILES_TABLE") ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$tab = 3;
				$_SESSION['weS']['weSearch']["checkWhich"] = 3;
				break;
			case (defined("OBJECT_TABLE") ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$tab = 3;
				$_SESSION['weS']['weSearch']["checkWhich"] = 4;
				break;

			default:
				$tab = $_REQUEST['we_cmd'][2];
		}
	}

	if(isset($_REQUEST['we_cmd'][3])){
		$modelid = $_REQUEST['we_cmd'][3];
	}
}

print we_html_element::jsScript(JS_DIR . "keyListener.js") .
	we_html_element::jsScript(JS_DIR . "libs/yui/yahoo-min.js") .
	we_html_element::jsScript(JS_DIR . "libs/yui/event-min.js") .
	we_html_element::jsScript(JS_DIR . "libs/yui/connection-min.js");
?>
</head>
<frameset rows="26,*" border="0" framespacing="0" frameborder="no">');
	<frame src="/webEdition/we/include/we_tools/tools_header.php?tool=<?php echo $_REQUEST['tool']; ?>" name="navi" noresize scrolling="no"/>
	<frame src="/webEdition/we/include/we_tools/tools_content.php?tool=<?php
echo $_REQUEST['tool'] . (isset($modelid) ? ('&modelid=' . $modelid) : '') . (isset($tab) ? ('&tab=' . $tab) : '');
?>" name="content" noresize scrolling="no"/>
</frameset>
<body bgcolor="#ffffff"></body>
</html>
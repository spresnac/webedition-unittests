<?php

/**
 * webEdition CMS
 *
 * $Rev: 4320 $
 * $Author: mokraemer $
 * $Date: 2012-03-23 00:51:46 +0100 (Fri, 23 Mar 2012) $
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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$appDir = Zend_Controller_Front::getInstance()->getParam('appDir');
$appName = Zend_Controller_Front::getInstance()->getParam('appName');

$frameset = new we_ui_layout_Frameset();
$frameset->setRows(((isset($_SESSION['prefs']['debug_normal']) && $_SESSION['prefs']['debug_normal'] != 0) ? '32,*,100' : '32,*,0'));

// parameter for each frame
$param = ($this->tab ?
		'/tab/' . $this->tab :
		'') .
	($this->sid ?
		'/sid/' . $this->sid :
		'') .
	($this->modelId ?
		'/modelId/' . $this->modelId :
		'');

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/header/index',
	'name' => 'header',
	'scrolling' => 'no',
	'noresize' => 'noresize'
));

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/frameset/resize' . $param,
	'name' => 'resize',
	'scrolling' => 'no'
));

$frameset->addFrame(array(
	'src' => 'about:blank',
	'name' => 'cmd_' . $appName,
	'scrolling' => 'no',
	'noresize' => 'noresize'
));

$page = we_ui_layout_HTMLPage::getInstance();
$page->setIsTopFrame(true);
$page->setFrameset($frameset);
$page->addJSFile(JS_DIR . 'windows.js');
$page->addJSFile(JS_DIR . 'we_showMessage.js');
$page->addJSFile(JS_DIR . 'images.js');
$page->addJSFile(JS_DIR . 'libs/yui/yahoo-min.js');
$page->addJSFile(JS_DIR . 'libs/yui/event-min.js');
$page->addJSFile(JS_DIR . 'libs/yui/connection-min.js');
$page->addJSFile(JS_DIR . 'libs/yui/json-min.js');
$page->addJSFile(LIB_DIR . 'we/core/JsonRpc.js');

$page->addInlineJS($this->getJSTop());

echo $page->getHTML();

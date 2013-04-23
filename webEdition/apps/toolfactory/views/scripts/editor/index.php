<?php
/**
 * webEdition CMS
 *
 * $Rev: 2633 $
 * $Author: mokraemer $
 * $Date: 2011-03-08 01:16:50 +0100 (Tue, 08 Mar 2011) $
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

$frameset = new we_ui_layout_Frameset(array('rows' => '40,*,40'));

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/editor/header' . $this->paramString,
	'name' => 'edheader', 
	'noresize' => 'noresize', 
	'scrolling' => 'no'
));

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/editor/body' . $this->paramString,
	'name' => 'edbody', 
	'scrolling' => 'auto'
));

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/editor/footer' . $this->paramString,
	'name' => 'edfooter', 
	'scrolling' => 'no'
));

// set and return html code		
$page = we_ui_layout_HTMLPage::getInstance();
$page->setFrameset($frameset);


echo $page->getHTML();

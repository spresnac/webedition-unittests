<?php
/**
 * webEdition CMS
 *
 * $Rev: 5427 $
 * $Author: mokraemer $
 * $Date: 2012-12-24 14:59:59 +0100 (Mon, 24 Dec 2012) $
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

$frameset = new we_ui_layout_Frameset(array('rows' => '1,*'));
$frameset->addFrame(array(
	'src' => '/webEdition/html/white.html',
	'name' => 'treeheader',
	'noresize' => 'noresize',
	'scrolling' => 'no'
));

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/tree/index' .
		($this->modelId ?
			'/modelId/' . $this->modelId :
			''),
	'name' => 'tree',
	'noresize' => 'noresize',
	'scrolling' => 'auto'
));


$page = we_ui_layout_HTMLPage::getInstance();
$page->setFrameset($frameset);
echo $page->getHTML();

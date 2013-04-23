<?php
/**
 * webEdition CMS
 *
 * $Rev: 3695 $
 * $Author: mokraemer $
 * $Date: 2012-01-01 18:33:26 +0100 (Sun, 01 Jan 2012) $
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
require_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we.inc.php");
$appName = Zend_Controller_Front::getInstance()->getParam('appName');
$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', 'toolfactory');

$page = we_ui_layout_HTMLPage::getInstance();


$saveButton = new we_ui_controls_Button(
	array(
		'text'		=> $translate->_('Save'),
		'onClick'	=> 'weCmdController.fire({cmdName: "app_toolfactory_save"});',
		'type'		=> 'onClick',
		'width'		=> 110,
		'disabled'	=> !we_core_Permissions::hasPerm('EDIT_APP_TOOLFACTORY'),
		'style'		=> 'margin:9px 0 0 15px;'
	)
);
$unpublishButton = new we_ui_controls_Button(
	array(
		'text'		=> $translate->_('Unpublish'),
		'onClick'	=> 'weCmdController.fire({cmdName: "app_toolfactory_unpublish", ignoreHot: "1", followCmd : {cmdName: "app_toolfactory_open",id: "'.$this->model->classname.'", ignoreHot: "1"}})',
		'type'		=> 'onClick',
		'width'		=> 110,
		'disabled'	=> !we_core_Permissions::hasPerm('PUBLISH_APP_TOOLFACTORY'),
		'style'		=> 'margin:9px 0 0 15px;'
	)
);
$publishButton = new we_ui_controls_Button(
	array(
		'text'		=> $translate->_('Publish'),
		'onClick'	=> 'weCmdController.fire({cmdName: "app_toolfactory_publish", ignoreHot: "1", followCmd : {cmdName: "app_toolfactory_open",id: "'.$this->model->classname.'", ignoreHot: "1"}})',
		'type'		=> 'onClick',
		'width'		=> 110,
		'disabled'	=> !we_core_Permissions::hasPerm('PUBLISH_APP_TOOLFACTORY'),
		'style'		=> 'margin:9px 0 0 15px;'
	)
);
$page->setBodyAttributes(array('class'=>'weEditorFooter'));

$table = new we_ui_layout_Table;
if(empty($this->model->ID)) {
	$table->addElement($saveButton, 1, 0);
} else {

	if(we_app_Common::isDeactivatable($this->model->classname)){
		if (we_app_Common::isActive($this->model->classname)){
			$table->addElement($unpublishButton,1,0);
		} else {
			$table->addElement($publishButton,1,0);
		}
	}
}
$page->addElement($table);
echo $page->getHTML();

<?php
/**
 * webEdition CMS
 *
 * $Rev: 5422 $
 * $Author: arminschulz $
 * $Date: 2012-12-23 19:23:47 +0100 (Sun, 23 Dec 2012) $
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
$appName = Zend_Controller_Front::getInstance()->getParam('appName');
$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', 'toolfactory');
if(we_app_Common::isDeinstallable($this->model->classname)){
	$dialog = new we_ui_dialog_YesNoCancelDialog();
	$dialog->setMessage($translate->_('The application will be deleted.') . "\n" . $translate->_('Are you sure?'));
	$dialog->setYesAction("weCmdController.fire(dialog.args.yesCmd);");
	$dialog->setNoAction("weCmdController.fire(dialog.args.noCmd);");
	echo $dialog->getHTML();
} else {
	if(we_app_Common::isInstalled($this->model->classname)){
		$dialog = new we_ui_dialog_OkDialog();
		$dialog->setMessage($translate->_('The application can not be deleted!'));
		$dialog->setOkAction("weCmdController.fire(dialog.args.noCmd);");
		echo $dialog->getHTML();
	} else {
		$dialog = new we_ui_dialog_OkDialog();
		$dialog->setMessage($translate->_('The application was succesfully deleted!'));
		$dialog->setOkAction("weCmdController.fire(dialog.args.noCmd);");
		echo $dialog->getHTML();
	}
}



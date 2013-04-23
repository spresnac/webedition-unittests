<?php

/**
 * webEdition CMS
 *
 * $Rev: 4303 $
 * $Author: mokraemer $
 * $Date: 2012-03-21 12:50:46 +0100 (Wed, 21 Mar 2012) $
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
/*
 * @see we_app_controller_FramesetAction
 */
Zend_Loader::loadClass('we_app_controller_FramesetAction');

/**
 * Base Frameset Controller
 *
 * @category   webEdition
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class FramesetController extends we_app_controller_FramesetAction{

	/**
	 * The default action - show the home page
	 */
	public function indexAction(){
		$this->view = new toolfactory_views_TopFrameView();
		$this->view->appDir = $this->getFrontController()->getParam('appDir');
		$this->view->appName = $this->getFrontController()->getParam('appName');
		$this->view->modelId = $this->getRequest()->getParam('modelId', 0);
		$this->view->tab = $this->getRequest()->getParam('tab', 0);
		$this->view->sid = $this->getRequest()->getParam('sid', 0);
		$this->view->setScriptPath('views/scripts');
		echo $this->view->render('frameset/index.php');
	}

}
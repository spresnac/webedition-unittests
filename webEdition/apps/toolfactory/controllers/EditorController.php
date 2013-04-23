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
 * @see we_app_controller_EditorAction
 */
Zend_Loader::loadClass('we_app_controller_EditorAction');

/**
 * Base Editor Controller
 *
 * @category   webEdition
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class EditorController extends we_app_controller_EditorAction{

	public function deletedocquestionAction(){
		$this->_setupModel();
		$this->_processPostVars();
		$this->_renderDefaultView('editor/deleteDocQuestion.php');
		/*
		  $this->view = new Zend_View();
		  $this->view->setScriptPath('views/scripts');
		  $this->_setupModel();
		  $this->view->model= $this->model;
		  $this->view->cmdstack = $this->getRequest()->getParam('cmdstack');
		  echo $this->view->render('editor/deleteDocQuestion.php');
		 */
	}

}
<?php

/**
 * webEdition CMS
 *
 * $Rev: 5988 $
 * $Author: arminschulz $
 * $Date: 2013-03-23 06:43:12 +0100 (Sat, 23 Mar 2013) $
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
/**
 * class for Services
 *
 * @category   app
 * @package    app_service
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

class toolfactory_service_Cmd extends we_app_service_AbstractCmd{

	/**
	 * check arguments and save the model
	 * @param array $args
	 *
	 * @return void
	 */
	public function save($args){

		$utf8_decode = true;

		$translate = we_core_Local::addTranslation('apps.xml');

		if(!isset($args[0])){
			throw new we_service_Exception(
				'Form data not set (first argument) at save cmd!',
				we_service_ErrorCodes::kModelFormDataNotSet);
		}
		$formData = $args[0];

		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if(!isset($session->model)){
			throw new we_service_Exception(
				'Model is not set in session!',
				we_service_ErrorCodes::kModelNotSetInSession);
		}
		$model = $session->model;
		$model->setFields($formData);

		$newBeforeSaving = $model->ID == 0;
		// check if user has the permissions to create new entries
		if($model->ID == 0 && !we_core_Permissions::hasPerm('NEW_APP_' . strtoupper($appName))){
			$ex = new we_service_Exception(
					$translate->_(
						'You do not have the permission to create new entries or folders!'));
			$ex->setType('warning');
			throw $ex;
		}

		// check if name is empty
		if($model->Text === ''){
			$ex = new we_service_Exception(
					$translate->_('The name must not be empty!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}

		// check if name is empty after converting text name for internal use
		if($model->classname === ''){
			$ex = new we_service_Exception(
					$translate->_('The name of the model class could not be empty!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}

		// check if it is a folder saving in itself
		if($model->IsFolder && $model->ID > 0 && $model->ParentID == $model->ID){
			$ex = new we_service_Exception(
					$translate->_('The folder cannot be saved in the chosen folder!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}
		// check all required fields
		$_miss = array();
		if(!$model->hasRequiredFields($_miss)){
			$ex = new we_service_Exception(
					$translate->_('Required fields are empty!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}

		// check if maintable name is valid
		if($model->maintablenameNotValid()){
			$ex = new we_service_Exception(
					$translate->_('The name of the maintable is not valid!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}

		// check if maintable exists
		if((isset($model->maintable) && $model->maintable != "")){
			if(we_io_DB::tableExists($model->maintable)){
				$ex = new we_service_Exception(
						$translate->_('The maintable exists!'),
						we_service_ErrorCodes::kModelTextEmpty);
				$ex->setType('warning');
				throw $ex;
			}
		}

		if($model->textNotValid()){
			$ex = new we_service_Exception(
					$translate->_('The name is not valid!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}

		if($model->classnameNotValid()){
			$ex = new we_service_Exception(
					$translate->_('The name of the model class is not valid!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}

		if($model->modelclassExists($model->classname)){
			$ex = new we_service_Exception(
					$translate->_('The model class exists!'),
					we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}

		try{
			$model->save();
		} catch (we_core_ModelException $e){
			switch($e->getCode()){
				case we_service_ErrorCodes::kPathExists :
					$ex = new we_service_Exception(
							$translate->_('The name already exists! Please choose another name or folder.'),
							$e->getCode());
					$ex->setType('warning');
					throw $ex;
					break;
				default :
					throw new we_service_Exception($e->getMessage(), $e->getCode());
			}
		}
		$model->ID = $model->classname;
		we_app_Common::rebuildAppTOC();
		return array(
			'model' => $model, 'newBeforeSaving' => $newBeforeSaving
		);
	}

	/**
	 * check arguments and delete the model
	 * @param array $args
	 *
	 * @return array
	 */
	public function delete($args){
		if(!isset($args[0])){
			throw new we_service_Exception(
				'ID not set (first argument) at delete cmd!',
				we_service_ErrorCodes::kModelIdNotSet);
		}
		we_app_Common::rebuildAppTOC();
		$IdToDel = $args[0];
		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if(!isset($session->model)){
			throw new we_service_Exception(
				'Model is not set in session!',
				we_service_ErrorCodes::kModelNotSetInSession);
		}
		$model = $session->model;

		if($model->ID != $IdToDel){
			throw new we_service_Exception(
				'Security Error: Model Ids are not the same! Id must fit the id of the model stored in the session!',
				we_service_ErrorCodes::kModelIdsNotTheSame);
		}

		try{
			if($model->maintable != ''){
				$db = we_io_DB::sharedAdapter();
				$result = $db->getConnection()->exec('DROP TABLE ' . $model->maintable);
			}
		} catch (we_core_ModelException $e){
			throw new we_service_Exception($GLOBALS['__WE_APP_PATH__'] . '/' . $model->classname . $e->getMessage());
		}
		//delete the directoy
		we_util_File::rmdirr($GLOBALS['__WE_APP_PATH__'] . DIRECTORY_SEPARATOR . $model->classname);
		we_app_Common::rebuildAppTOC();
		//return deleted model
		return array(
			'model' => $model
		);
	}

	/**
	 * regenerate toc.xml
	 *
	 *
	 */
	public function regeneratetoc($args){
		$translate = we_core_Local::addTranslation('apps.xml');
		we_core_Local::addTranslation('default.xml', 'toolfactory');
		we_app_Common::rebuildAppTOC();
		$ex = new we_service_Exception(
				$translate->_(
					'The application toc.xml was succesfully rebuild!'));
		$ex->setType('notice');
		throw $ex;
		return $args;
	}

	/**
	 * check arguments and unpublish the model
	 * @param array $args
	 *
	 * @return array
	 */
	public function unpublish($args){

		$utf8_decode = true;

		$translate = we_core_Local::addTranslation('apps.xml');

		if(!isset($args[0])){
			throw new we_service_Exception('Form data not set (first argument) at save cmd!', we_service_ErrorCodes::kModelFormDataNotSet);
		}
		$formData = $args[0];

		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if(!isset($session->model)){
			throw new we_service_Exception('Model is not set in session!', we_service_ErrorCodes::kModelNotSetInSession);
		}

		$model = $session->model;
		//return $this->save($args);


		we_app_Common::deactivate($model->classname);
		return array(
			'model' => $model
		);
	}

	/**
	 * check arguments and publish the model
	 * @param array $args
	 *
	 * @return array
	 */
	public function publish($args){

		$utf8_decode = true;

		$translate = we_core_Local::addTranslation('apps.xml');

		if(!isset($args[0])){
			throw new we_service_Exception('Form data not set (first argument) at save cmd!', we_service_ErrorCodes::kModelFormDataNotSet);
		}
		$formData = $args[0];

		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if(!isset($session->model)){
			throw new we_service_Exception('Model is not set in session!', we_service_ErrorCodes::kModelNotSetInSession);
		}
		$session->model->Published = 1;
		$model = $session->model;


		we_app_Common::activate($model->classname); // eintrag im TOC, in define und meta
		return array(
			'model' => $model
		);
	}

	public function localInstallFromTGZ($args){

		$utf8_decode = true;
		$translate = we_core_Local::addTranslation('apps.xml');

		if(!isset($args[0])){
			throw new we_service_Exception('App data not set (first argument) at localInstallFromTGZ cmd!', we_service_ErrorCodes::kModelFormDataNotSet);
		}

		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		$inst = new toolfactory_service_Install();
		$appdataArray = $inst->getApplist();
		$appdata = $appdataArray[$args[0]];
		we_util_File::decompressDirectoy($appdata['source'], $_app_directory_string = $GLOBALS['__WE_APP_PATH__'] . '/' . $appdata['classname']);

		we_util_File::delete($appdata['source']);
		$model = $session->model;

		$model->ID = $appdata['classname'];
		$newBeforeSaving = 0;
		we_app_Common::rebuildAppTOC();
		return array(
			'model' => $model, 'newBeforeSaving' => $newBeforeSaving
		);
	}

	public function generateTGZ($args){
		if(!isset($args[0])){
			throw new we_service_Exception(
				'ID not set (first argument) at generateTGZ cmd!',
				we_service_ErrorCodes::kModelIdNotSet);
		}

		$IdToTGZ = $args[0];
		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if(!isset($session->model)){
			throw new we_service_Exception(
				'Model is not set in session!',
				we_service_ErrorCodes::kModelNotSetInSession);
		}
		$model = $session->model;
		if($model->ID != $IdToTGZ){
			throw new we_service_Exception(
				'Security Error: Model Ids are not the same! Id must fit the id of the model stored in the session!',
				we_service_ErrorCodes::kModelIdsNotTheSame);
		}
		we_util_File::compressDirectoy($_SERVER['DOCUMENT_ROOT'] . "/webEdition/apps/" . $model->classname, $_SERVER['DOCUMENT_ROOT'] . "/webEdition/apps/" . $model->classname . "_" . $model->appconfig->info->version . ".tgz");

		return true;
	}

	/**
	 * Accessibility for services
	 * @param string $method
	 * @param string $accessibility
	 *
	 * @return string
	 */
	public function getAccessibility($method, $accessibility){
		switch($method){
			case 'save':
				return we_net_rpc_JsonRpc::kDefaultAccessibility;
				break;
			case 'delete':
				return we_net_rpc_JsonRpc::kDefaultAccessibility;
				break;
			default:
				return we_net_rpc_JsonRpc::kDefaultAccessibility;
		}
		return we_net_rpc_JsonRpc::kDefaultAccessibility;
	}

}
<?php
/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile 
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_app
 * @subpackage we_app_service
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * @see we_service_AbstractService
 */
Zend_Loader::loadClass('we_service_AbstractService');

/**
 * Abstract Class for all App Services
 * 
 * @category   we
 * @package    we_app
 * @subpackage we_app_service
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
abstract class we_app_service_AbstractCmd extends we_service_AbstractService
{

	/**
	 * check arguments and save the model
	 * 
	 * @param array $args
	 * @return array
	 */
	public function save($args)
	{
		$utf8_decode = true;
		
		$translate = we_core_Local::addTranslation('apps.xml');
		
		if (!isset($args[0])) {
			throw new we_service_Exception('Form data not set (first argument) at save cmd!', we_service_ErrorCodes::kModelFormDataNotSet);
		}
		$formData = $args[0];
		
		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if (!isset($session->model)) {
			throw new we_service_Exception('Model is not set in session!', we_service_ErrorCodes::kModelNotSetInSession);
		}
		$model = $session->model;
		$model->setFields($formData);
		
		if(isset($formData['ParentPath']) && $model->pathExists($formData['ParentPath'])){
			$model->ParentID = we_util_Path::path2Id($formData['ParentPath'], $model->getTable());
			$model->setPath();
		}
		
		$newBeforeSaving = $model->ID == 0;
		// check if user has the permissions to create new entries
		if ($model->ID == 0 && !we_core_Permissions::hasPerm('NEW_APP_' . strtoupper($appName))) {
			$ex = new we_service_Exception($translate->_('You do not have the permission to create new entries or folders!'));
			$ex->setType('warning');
			throw $ex;
		}
		
		// check if name is empty
		if ($model->Text === '') {
			$ex = new we_service_Exception($translate->_('The name must not be empty!'), we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}
		
		// check if name is valid
		if ($model->textNotValid()) {
			$ex = new we_service_Exception($translate->_('Invalid entry name!'), we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}
		
		// check if it is a folder saving in itself
		if ($model->IsFolder && $model->ID > 0 && $model->ParentID == $model->ID) {
			$ex = new we_service_Exception($translate->_('The folder cannot be saved in the chosen folder!'), we_service_ErrorCodes::kModelTextEmpty);
			$ex->setType('warning');
			throw $ex;
		}
		
		if (isset($args['skipHook'])){
			$skipHook = $args['skipHook'];
		} else {
			$skipHook = 0;
		}
		try {
			$model->save($skipHook);
		} catch (we_core_ModelException $e) {
			switch ($e->getCode()) {
				case we_service_ErrorCodes::kPathExists :
					$ex = new we_service_Exception($translate->_('The name already exists! Please choose another name or folder.'), $e->getCode());
					$ex->setType('warning');
					throw $ex;
					break;
				default :
					throw new we_service_Exception($e->getMessage(), $e->getCode());
			}
		}
		return array('model' => $model, 'newBeforeSaving' => $newBeforeSaving);
	}

	public function unpublish($args)
	{
	
		$utf8_decode = true;
		
		$translate = we_core_Local::addTranslation('apps.xml');
		
		if (!isset($args[0])) {
			throw new we_service_Exception('Form data not set (first argument) at save cmd!', we_service_ErrorCodes::kModelFormDataNotSet);
		}
		$formData = $args[0];
		
		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if (!isset($session->model)) {
			throw new we_service_Exception('Model is not set in session!', we_service_ErrorCodes::kModelNotSetInSession);
		}
		$session->model->Published=0;
		$model = $session->model;
		$dieargs = $args;
		$dieargs['skipHook'] = 1;
		$hook = new weHook('unpublish', $appName, array($model));
		$hook->executeHook();
		return $this->save($dieargs);
	}


	public function publish($args)
	{
	
		$utf8_decode = true;
		
		$translate = we_core_Local::addTranslation('apps.xml');
		
		if (!isset($args[0])) {
			throw new we_service_Exception('Form data not set (first argument) at save cmd!', we_service_ErrorCodes::kModelFormDataNotSet);
		}
		$formData = $args[0];
		
		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if (!isset($session->model)) {
			throw new we_service_Exception('Model is not set in session!', we_service_ErrorCodes::kModelNotSetInSession);
		}
		$session->model->Published=time();
		$model = $session->model;
		$dieargs = $args;
		$dieargs['skipHook'] = 1;
		$hook = new weHook('publish', $appName, array($model));
		$hook->executeHook();
		return $this->save($dieargs);
	}


	/**
	 * check arguments and delete the model
	 * 
	 * @param array $args
	 * @return array
	 */
	public function delete($args)
	{
		if (!isset($args[0])) {
			throw new we_service_Exception('ID not set (first argument) at delete cmd!', we_service_ErrorCodes::kModelIdNotSet);
		}
		$IdToDel = $args[0];
		$controller = Zend_Controller_Front::getInstance();
		$appName = $controller->getParam('appName');
		$session = new Zend_Session_Namespace($appName);
		if (!isset($session->model)) {
			throw new we_service_Exception('Model is not set in session!', we_service_ErrorCodes::kModelNotSetInSession);
		}
		$model = $session->model;
		
		if ($model->ID != $IdToDel) {
			throw new we_service_Exception('Security Error: Model Ids are not the same! Id must fit the id of the model stored in the session!', we_service_ErrorCodes::kModelIdsNotTheSame);
		}
		
		try {
			$model->delete();
		} catch (we_core_ModelException $e) {
			throw new we_service_Exception($e->getMessage());
		}
		
		//return deleted model
		return array('model' => $model);
	}
}
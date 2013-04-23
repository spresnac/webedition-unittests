<?php

/**
 * webEdition CMS
 *
 * $Rev: 4836 $
 * $Author: mokraemer $
 * $Date: 2012-08-09 00:10:37 +0200 (Thu, 09 Aug 2012) $
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
abstract class validation{

	static function getAllCategories(){
		$cats = array(
			'xhtml' => g_l('validation', '[category_xhtml]'),
			'links' => g_l('validation', '[category_links]'),
			'css' => g_l('validation', '[category_css]'),
			'accessibility' => g_l('validation', '[category_accessibility]')
		);
		return $cats;
	}

	static function saveService($validationService){
		// before saving check if another validationservice has this name
		$exist = f('SELECT 1 as a FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE name="' . $GLOBALS['DB_WE']->escape($validationService->name) . '"
					AND PK_tblvalidationservices != ' . intval($validationService->id), 'a', $GLOBALS['DB_WE']);

		if($exist === '1'){
			$GLOBALS['errorMessage'] = g_l('validation', '[edit_service][servicename_already_exists]');
			return false;
		}

		$qSet = we_database_base::arraySetter(array(
			'category' => $validationService->category,
			'name' => $validationService->name,
			'host' => $validationService->host,
			'path' => $validationService->path,
			'method' => $validationService->method,
			'varname' => $validationService->varname,
			'checkvia' => $validationService->checkvia,
			'additionalVars' => $validationService->additionalVars,
			'ctype' => $validationService->ctype,
			'fileEndings' => $validationService->fileEndings,
			'active' => $validationService->active
		));
		if($validationService->id != 0){
			$query = 'UPDATE ' . VALIDATION_SERVICES_TABLE . ' SET ' . $qSet .
				' WHERE PK_tblvalidationservices = ' . intval($validationService->id);
		} else{
			$query = 'INSERT INTO ' . VALIDATION_SERVICES_TABLE . ' SET ' . $qSet;
		}

		if($GLOBALS['DB_WE']->query($query)){
			if($validationService->id == 0){
				$validationService->id = $GLOBALS['DB_WE']->getInsertId();
			}
			return $validationService;
		} else{
			return false;
		}
	}

	static function deleteService($validationService){
		if($validationService->id != 0){

			if($GLOBALS['DB_WE']->query('DELETE FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE PK_tblvalidationservices = ' . intval($validationService->id))){
				return true;
			}
		} else{
			//  not saved entry - must not be deleted from db
			return true;
		}
		return false;
	}

	static function getValidationServices($mode = 'edit'){
		$_ret = array();

		switch($mode){
			case 'edit':
				$query = 'SELECT * FROM ' . VALIDATION_SERVICES_TABLE;
				break;
			case 'use':
				$query = 'SELECT * FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE fileEndings LIKE "%' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->Extension) . '%" AND active=1';
				break;
		}

		$GLOBALS['DB_WE']->query($query);
		while($GLOBALS['DB_WE']->next_record()) {
			$_ret[] = new validationService($GLOBALS['DB_WE']->f('PK_tblvalidationservices'), 'custom', $GLOBALS['DB_WE']->f('category'), $GLOBALS['DB_WE']->f('name'), $GLOBALS['DB_WE']->f('host'), $GLOBALS['DB_WE']->f('path'), $GLOBALS['DB_WE']->f('method'), $GLOBALS['DB_WE']->f('varname'), $GLOBALS['DB_WE']->f('checkvia'), $GLOBALS['DB_WE']->f('ctype'), $GLOBALS['DB_WE']->f('additionalVars'), $GLOBALS['DB_WE']->f('fileEndings'), $GLOBALS['DB_WE']->f('active'));
		}
		return $_ret;
	}

}

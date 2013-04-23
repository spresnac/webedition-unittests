<?php
/**
 * webEdition CMS
 *
 * $Rev: 5463 $
 * $Author: arminschulz $
 * $Date: 2012-12-27 19:43:53 +0100 (Thu, 27 Dec 2012) $
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
 * @see we_app_HomePage
 */
Zend_Loader::loadClass('we_app_HomePage');

/**
 * Class for Home Page View of toolfactory
 * 
 * @category   toolfactory
 * @package    toolfactory_app
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class toolfactory_app_HomePage extends we_app_HomePage
{
	/**
	 * Returns string with HTML of the body div
	 *
	 * @return string
	 */
	protected function _getBodyDiv() {
		
		$translate = we_core_Local::addTranslation('apps.xml');
		
		$appName = Zend_Controller_Front::getInstance()->getParam('appName');
		$this->_boxHeight=300;
		$bodyDiv = new we_ui_layout_Div(array(
			'width'=>206,
			'height'=>$this->_boxHeight-(38+22),
			'top'=>20,
			'left'=>0,
			'position'=>'absolute',
			'class' => self::kClassBoxBody
		));
		$perm = 'NEW_APP_'.strtoupper($appName);
		$newItemButton = new we_ui_controls_Button(array(
			'text'=>$translate->_('New Entry'), 
			'onClick'=>'weCmdController.fire({cmdName: "app_'.$appName.'_new"})', 
			'type'=>'onClick', 
			'disabled' => we_core_Permissions::hasPerm($perm) ? false : true,
			'width'=>200
		));
		$perm = 'GENTOC_APP_'.strtoupper($appName);
		$regenerateTocButton = new we_ui_controls_Button(array(
			'text'=>$translate->_('Regenetrate TOC'), 
			'onClick'=>'weCmdController.fire({cmdName: "app_'.$appName.'_gentoc"})', 
			'type'=>'onClick', 
			'disabled' => we_core_Permissions::hasPerm($perm) ? false : true,
			'width'=>200,
			'top'=>'10px;',
			'style'=>'margin-bottom:10px;'
		));
		$bodyDiv->addElement($newItemButton);
		$bodyDiv->addElement($regenerateTocButton);
		$perm = 'NEW_APP_'.strtoupper($appName);
		$inst = new toolfactory_service_Install();
		$appdata= $inst->getApplist();
		$i=0;
		foreach($appdata as $dieApp){
			$localInstallButton = new we_ui_controls_Button(array(
				'text'=>$translate->_('Install').' '.$dieApp['classname'].' '.$dieApp['version'], 
				'onClick'=>'weCmdController.fire({cmdName: "app_'.$appName.'_localInstall'.$i.'"})', 
				'type'=>'onClick', 
				'disabled' => we_core_Permissions::hasPerm($perm) ? false : true,
				'width'=>200,
				'top'=>'10px;',
				'style'=>'margin-top:10px;'
			));
			$bodyDiv->addElement($localInstallButton);
			
			if($i>=5){
				break;	
			}
			$i++;
		}
		
		
		
		return $bodyDiv;
	}
	
}

?>

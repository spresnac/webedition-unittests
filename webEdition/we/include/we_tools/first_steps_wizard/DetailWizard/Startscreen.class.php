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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

class Startscreen extends leWizardStepBase
{

	var $EnabledButtons = array(
		'next'
	);

	function execute(&$Template)
	{
		
		return LE_WIZARDSTEP_NEXT;
	
	}

	function check(&$Template)
	{
		
		$Parameters = array(
			"update_cmd" => "checkConnection"
		);
		
		$Response = liveUpdateHttpWizard::getHttpResponse(LIVEUPDATE_SERVER, LIVEUPDATE_SERVER_SCRIPT, $Parameters);
		$LiveUpdateResponse = new liveUpdateResponse();
		
		if ($LiveUpdateResponse->initByHttpResponse($Response)) {
			if ($LiveUpdateResponse->isError()) {
				$Template->addError($this->Language['error'] . ":<br />" . $LiveUpdateResponse->getField('Message'));
				return false;
			
			} else {
				return true;
			
			}
		
		} else {
			$Template->addError($this->Language['no_connection']);
			return false;
		
		}
	
	}

}

?>
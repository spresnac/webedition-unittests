<?php
/**
 * webEdition CMS
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



	/**
	 * if hook execution is enabled this function will be executed
	 * when publishing a document, template, object or class
	 * The array $param has all information about the respective document.
	 *
	 * IMPORTANT!
	 * Copy this file to the custom_hooks folder when doing any changes
	 * Files in the sample_hooks folder are not executed and are not update-safe and will be overwritten by the next webEdition update
	 *
	 * When using the WE-APP WE:Hookmanagement, this is done automatically by the WE-APP
	 *
	 * @param array $param
	 * contents of $param depends on 'from'
	 * if from='management' hook is called from object method save, 
	 * in this case customer ist the customer object
	 * and type is either 'new' or 'existing'
	 * if from='tag', it is called from a we-tag
	 * in this case, customer is NOT the customer object but an array,
	 * the arraykey is the fieldname, the arrayvalue gives the value
	 * the array is passed by reference
	 * 'tagname holds the name of the tag ('saveRegisteredUser' or addDelNewsletterEmail)
	 * in this case, type is either 'new' or 'modify'
	 * for addDelNewsletterEmail additional parameters are isSubscribe' and 'isUnsubscribe'
	 */
	function weCustomHook_customer_preSave($param) {
		$hookHandler=$param['hookHandler'];
		$data=$param['customer'];
		$from=$param['from']; //tag,management
		switch($param['type']){
			case 'new':
			case 'modify':
		}
	}

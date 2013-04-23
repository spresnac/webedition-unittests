<?php

/**
 * webEdition CMS
 *
 * $Rev: 5039 $
 * $Author: mokraemer $
 * $Date: 2012-10-31 01:13:32 +0100 (Wed, 31 Oct 2012) $
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
 * @package    webEdition_listview
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class metadatatag{

	var $DB_WE;
	var $ClassName = __CLASS__;
	var $object = '';
	var $avail = false;
	var $id = 0;

	function __construct($name){
		$this->DB_WE = new DB_WE;

		if($name){
			$unique = md5(uniqid(__FILE__, true));
			if(!isset($GLOBALS["lv"])){
				// determine the id of the element
				$_value = $GLOBALS['we_doc']->getElement($name, "bdid");
				if(!$_value){
					$_value = $GLOBALS['we_doc']->getElement($name);
				}
			} else{
				$_value = $GLOBALS["lv"]->f($name);
			}
			$this->id = 0;
			if(is_numeric($_value)){
				// it is an id
				$this->id = $_value;
			} else if($_value){
				// is this possible
				//TODO: check if this can happen
			}
			if($this->id){
				$this->object = new we_listview($unique, 1, 0, "", false, "", "", false, false, 0, "", "", false, "", "", "", "", "", "", "off", true, "", $this->id);
				if($this->object->next_record()){
					$this->avail = true;
				}
			}
		}
	}

	function f($key){
		return ($this->id ? $this->object->f($key) : '');
	}

}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5929 $
 * $Author: lukasimhof $
 * $Date: 2013-03-08 23:48:03 +0100 (Fri, 08 Mar 2013) $
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
class we_objecttag{

	var $DB_WE;
	var $class = '';
	var $id = 0;
	var $triggerID = 0;
	var $ClassName = __CLASS__;
	var $object = "";
	var $avail = false;
	var $hidedirindex = false;
	var $objectseourls = false;

	function __construct($class = '', $id = 0, $triggerID = 0, $searchable = true, $condition = '', $hidedirindex = false, $objectseourls = false){
		$this->DB_WE = new DB_WE();
		$this->id = $id;
		if(!$this->id && isset($_REQUEST['we_objectID']) && $_REQUEST['we_objectID']){
			!$this->id = $_REQUEST['we_objectID'];
		}
		$this->class = $class;
		$this->hidedirindex = $hidedirindex;
		$this->objectseourls = $objectseourls;

		$this->triggerID = $triggerID;
		$unique = md5(uniqid(__FUNCTION__, true));

		if($this->id){
			$foo = getHash('SELECT TableID,ObjectID FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->id), $this->DB_WE);
			if(!empty($foo)){
				//FIXME: fix regex in listview_object and listview_multiobject, then restore type int for ID
				//$this->object = new we_listview_object($unique, 1, 0, '', 0, $foo['TableID'], '', '', '(' . OBJECT_X_TABLE . $foo['TableID'] . '.ID=' . intval($foo['ObjectID']) . ')' . ($condition ? ' AND '.$condition : ''), $this->triggerID, '', '', $searchable, '', '', '', '', '', '', '', 0, '', '', '', '', $hidedirindex, $objectseourls);
				$this->object = new we_listview_object($unique, 1, 0, '', 0, $foo['TableID'], '', '', '(' . OBJECT_X_TABLE . $foo['TableID'] . '.ID="' . intval($foo['ObjectID']) . '")' . ($condition ? ' AND '.$condition : ''), $this->triggerID, '', '', $searchable, '', '', '', '', '', '', '', 0, '', '', '', '', $hidedirindex, $objectseourls);
				if($this->object->next_record()){
					$this->avail = true;
				}
			}
		}
	}

	public function getDBf($key){
		return $this->DB_WE->f($key);
	}

	function f($key){
		return ($this->id ?
				$this->object->f($key) : '');
	}

}
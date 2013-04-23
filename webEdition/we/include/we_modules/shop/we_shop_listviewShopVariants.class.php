<?php

/**
 * webEdition CMS
 *
 * $Rev: 5928 $
 * $Author: mokraemer $
 * $Date: 2013-03-08 23:23:10 +0100 (Fri, 08 Mar 2013) $
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
 * @desc    class for tag <we:listview type="shopVariants">
 *
 */
class we_shop_listviewShopVariants extends listviewBase{

	var $Record = array();
	var $ClassName = __CLASS__;
	var $VariantData = array();
	var $Position = 0;
	var $Id = 0;
	var $ObjectId = 0;
	var $DefaultName = 'default';
	var $Model = null;
	var $IsObjectFile = false;
	var $hidedirindex = false;
	var $objectseourls = false;

	function __construct($name, $rows, $defaultname = 'default', $documentid = '', $objectid = '', $offset = 0, $hidedirindex = false, $objectseourls = false, $triggerID = ""){

		parent::__construct($name, $rows, $offset);

		// we have to init a new document and look for the given field
		// get id of given document and check if it is a document or an objectfile
		if($documentid || ($objectid && defined('OBJECT_TABLE'))){

			if($documentid){

				$this->Id = $documentid;

				$doc = new we_webEditionDocument();
				$doc->initByID($this->Id);
			} else if($objectid){

				$this->IsObjectFile = true;

				$this->Id = $objectid;

				$doc = new we_objectFile();
				$doc->initByID($this->Id, OBJECT_FILES_TABLE);
			}
		} else{

			// check if its a document or a objectFile
			if(isset($GLOBALS['we_doc']->ObjectID)){ // is an objectFile
				$this->Id = isset($GLOBALS['we_doc']->OF_ID) ? $GLOBALS['we_doc']->OF_ID : $GLOBALS['we_doc']->ID;
				$this->IsObjectFile = true;

				$doc = new we_objectFile();
				$doc->initByID($this->Id, OBJECT_FILES_TABLE);
			} else{

				$this->Id = $GLOBALS['we_doc']->ID;

				$doc = new we_webEditionDocument();
				$doc->initByID($this->Id);
			}
		}

		// store model in listview object
		$this->Model = $doc;

		$this->DefaultName = $defaultname;

		$variantData = weShopVariants::getVariantData($this->Model, $this->DefaultName);

		$this->VariantData['Record'] = $variantData;

		$this->anz_all = count($this->VariantData['Record']);
		$this->anz = min($this->rows, $this->anz_all);
		$this->hidedirindex = $hidedirindex;
		$this->objectseourls = $objectseourls;
		$this->triggerID = $triggerID;
	}

	function next_record(){
		$this->Position = ($this->count + $this->start);
		if(isset($this->VariantData['Record'][$this->Position])){
			$ret = $this->VariantData['Record'][$this->Position];

			list($key, $vardata) = each($ret);
			foreach($vardata as $name => $value){

				$ret[$name] = (isset($value['type']) && $value['type'] == 'img' ?
						// there is a difference between objects and webEdition Documents
						isset($value['bdid']) ? $value['bdid'] : $value['dat'] :
						(isset($value['dat']) ? $value['dat'] : '')
					);
			}

			$varUrl = '';
			$ret['WE_VARIANT_NAME'] = $key;
			$ret['WE_VARIANT'] = '';

			if($key != $this->DefaultName){
				$varUrl = WE_SHOP_VARIANT_REQUEST . '=' . $key;
				$ret['WE_VARIANT'] = $key;
			}

			$ret['WE_ID'] = $this->Id;

			if($this->IsObjectFile){ // objectFile
				$path_parts = pathinfo($GLOBALS['we_doc']->Path);
				if($this->objectseourls && show_SeoLinks()){
					$Url = f("SELECT Url from " . OBJECT_FILES_TABLE . " WHERE ID=" . $this->Id, 'Url', $this->DB_WE);
					if($Url != ''){

						$ret['WE_PATH'] =
							($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') .
							( show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
								'' : '/' . $path_parts['filename']
							) . '/' . $Url . ($varUrl ? "?$varUrl" : '');
					} else{
						$ret['WE_PATH'] = (show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
								($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . "?we_objectID=" . $this->Id . ($varUrl ? "&amp;$varUrl" : '') :
								$GLOBALS['we_doc']->Path . "?we_objectID=" . $this->Id . ($varUrl ? "&amp;$varUrl" : '')
							);
					}
				} else{
					if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$ret['WE_PATH'] =
							$GLOBALS['we_doc']->Path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . ($varUrl ? "?$varUrl" : '');
					} else{
						$ret['WE_PATH'] = $GLOBALS['we_doc']->Path . "?we_objectID=" . $this->Id . ($varUrl ? "&amp;$varUrl" : '');
					}
				}
			} else{ // webEdition Document
				$path_parts = pathinfo($this->Model->Path);
				if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$ret['WE_PATH'] =
						$this->Model->Path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . ($varUrl ? "?$varUrl" : '');
				} else{
					$ret['WE_PATH'] = $this->Model->Path . ($varUrl ? "?$varUrl" : '');
				}
			}

			$this->Record = $ret;
			$this->count++;
			return true;
		}
		return false;
	}

	function f($key){
		return (isset($this->Record[$key]) ? $this->Record[$key] : '');
	}

}


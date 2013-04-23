<?php

/**
 * webEdition CMS
 *
 * $Rev: 5819 $
 * $Author: mokraemer $
 * $Date: 2013-02-15 23:45:18 +0100 (Fri, 15 Feb 2013) $
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
 * Filename:    we_baseCollection.inc.php
 *
 * Function:    Utility class that implements basic html collection operations
 *
 * Description: Provides functions for creating html tags
 */
class we_baseCollection extends we_baseElement{

	var $childs = array();

	/**
	 * Constructor
	 *
	 * @param		$tagname								string
	 * @param		$need_end_tag							bool
	 * @param		$attribs								array
	 * @param		$childs_num								int					(optional)
	 *
	 * @return		we_baseCollection
	 */
	function __construct($tagname = "", $need_end_tag = true, array $attribs = array(), $childs_num = 0){
		parent::__construct($tagname, $need_end_tag, $attribs);

		for($i = 0; $i < $childs_num; $i++){
			$this->addChild(new we_baseElement());
		}
	}

	/**
	 * Function adds new element to the collection
	 *
	 * @param		$child									we_baseElement
	 *
	 * @return		void
	 */
	function addChild($child){
		$this->childs[] = $child;
	}

	/**
	 * Function dels element from the collection
	 * Parameter $childid is a sequence number in the childs array
	 *
	 * @param		$childid									int
	 *
	 * @return		void
	 */
	function delChild($childid){
		array_splice($this->childs, $childid, 1);
	}

	/**
	 * Function sets child wit specified childid with given child object
	 *
	 * @param		$childid									int
	 * @param		$child										we_baseElement
	 *
	 * @return		void
	 */
	function setChild($childId, $child){
		$this->childs[$childId] = $child;
	}

	/**
	 * Function gives reference on child object back
	 *
	 * @param		$childid									int
	 *
	 * @return		we_baseElement
	 */
	function &getChild($childId){
		return $this->childs[$childId];
	}

	/**
	 * Function generate collection HTML code
	 *
	 * @param		$object										we_baseElement
	 *
	 * @return		string
	 */
	static function getHtmlCode($object){
		return $object->getHtml();
	}

	function getHtml(){
		$childs_content = '';
		foreach($this->childs as $vc){
			$childs_content.=$vc->getHtml();
		}
		$this->setContent($childs_content);
		return parent::getHtml();
	}

}

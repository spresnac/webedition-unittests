<?php

/**
 * webEdition CMS
 *
 * $Rev: 5044 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 17:59:55 +0100 (Thu, 01 Nov 2012) $
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
 * Filename:    we_html_table.inc.php
 *
 * Function:    Utility class that implements operations on tables
 *
 * Description: Provides functions for creating html tags used in forms.
 */
class we_html_frameset extends we_baseCollection{

	/**
	 * Constructor
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$frames_num								int				(optional)
	 *
	 * @return		we_html_frameset
	 */
	function __construct($attribs=array(), $frames_num=0){
		parent::__construct("frameset", true, $attribs);
		for($i = 0; $i < $frames_num; $i++){
			$this->addFrame();
		}
	}

	/**
	 * Function adds new frame to frameset
	 *
	 * Description: Constructor
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		void
	 */
	function addFrame($attribs=array()){
		$this->childs[] = new we_baseElement("frame", false, $attribs);
	}

	/**
	 * Function adds new frameset to frameset
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		void
	 */
	function addFrameset($attribs=array()){
		$this->childs[] = new self($attribs);
	}

	/**
	 * Function sets frame's attributes
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		void
	 */
	function setFrameAttributes($childid, $attribs=array()){

		$frame = & $this->getChild($childid);
		$frame->setAttributes($attribs);
	}

}
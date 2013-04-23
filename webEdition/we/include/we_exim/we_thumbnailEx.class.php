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

class we_thumbnailEx extends weModelBase{

	var $ClassName = __CLASS__;
	var $Table = THUMBNAILS_TABLE;
	var $ContentType = 'weThumbnail';

	function __construct(){
		parent::__construct(THUMBNAILS_TABLE);
	}

	function we_load($id){
		parent::load($id);
		$this->ContentType = 'weThumbnail';
	}

	function we_save(){
		parent::save($this->ID ? false : true);
	}

}

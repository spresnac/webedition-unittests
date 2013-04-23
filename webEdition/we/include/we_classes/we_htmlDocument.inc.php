<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_htmlDocument extends we_textContentDocument{

	function __construct(){
		parent::__construct();
		$this->ContentType = 'text/html';
	}

	function i_saveContentDataInDB(){
		if(is_array($this->elements['data']) && isset($this->elements['data']['dat'])){
			$code = $this->elements['data']['dat'];
			$metas = $this->getMetas($code);
			if(isset($metas['title']) && $metas['title']){
				$this->setElement('Title', $metas['title']);
			}
			if(isset($metas['description']) && $metas['description']){
				$this->setElement('Description', $metas['description']);
			}
			if(isset($metas['keywords']) && $metas['keywords']){
				$this->setElement('Keywords', $metas['keywords']);
			}
			if(isset($metas['charset']) && $metas['charset']){
				$this->setElement('Charset', $metas['charset']);
			}
		}
		return parent::i_saveContentDataInDB();
	}

	function makeSameNew(){
		parent::makeSameNew();
		$this->Icon = 'prog.gif';
	}

	function i_publInScheduleTable(){
		return (defined('SCHEDULE_TABLE') ?
				we_schedpro::publInScheduleTable($this, $this->DB_WE) :
				false);
	}

	function getDocumentCode(){
		$code = $this->getElement('data');

		if(isset($this->elements['Charset']['dat']) && $this->elements['Charset']['dat']){
			$code = preg_replace('|<meta http-equiv="Content-Type" content=".*>|i', we_html_tools::htmlMetaCtCharset('text/html', $this->elements['Charset']['dat']), $code);
		}
		return $code;
	}

}


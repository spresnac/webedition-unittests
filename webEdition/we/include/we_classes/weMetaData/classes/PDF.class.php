<?php

/**
 * webEdition CMS
 *
 * $Rev: 5661 $
 * $Author: mokraemer $
 * $Date: 2013-01-29 22:17:38 +0100 (Tue, 29 Jan 2013) $
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
 * @abstract implementation class of metadata reader for PDF metadata
 */
class weMetaData_PDF extends weMetaData{

	public function __construct($filetype){
		$this->filetype = $filetype;
		$this->accesstypes = array('read');
	}

	protected function _getMetaData($selection = ''){
		if(!$this->_valid){
			return false;
		}
		if(is_array($selection)){
			// fetch some
		} else{
			$pdf= new we_helpers_pdf2text($this->datasource);
			$this->metadata = $pdf->getInfo();
		}
		return $this->metadata;
	}

}

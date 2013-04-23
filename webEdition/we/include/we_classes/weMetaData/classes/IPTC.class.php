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
 * @abstract implementation class of metadata reader for IPTC data
 * @author Alexander Lindenstruth
 * @since 5.1.0.0 - 27.09.2007
 * @uses IPTC PEAR_IPTC Package for reading IPTC data. See link below for more information
 * @link http://pear.php.net/package/Image_IPTC/ PEAR IPTC Package
 */
class weMetaData_IPTC extends weMetaData{

	const usedFields = 'byline_title,byline,caption_writer,caption,category,city,copyright_string,country_code,country,created_date,credit,edit_status,fixture_identifier,headline,keywords,local_caption,object_cycle,object_name,original_transmission_reference,originating_program,priority,program_version,province_state,reference_date,reference_number,reference_service,release_date,release_time,source,special_instructions,supplementary_category';

	var $accesstypes = array("read");

	public function __construct($filetype){
		$this->filetype = $filetype;
		$this->accesstypes = array("read");
	}

	function getUsedFields(){
		return explode(',', self::usedFields);
	}

	protected function _getMetaData($selection = ""){
		if(!$this->_valid){
			return false;
		}

		// seems not to work correctly so only an empty array is returned to caller:
		$this->metadata = array();
		//$this->metadata = array("Copyright" => "/me","Make" => "Fuji");

		$_iptcData = new Image_IPTC($this->datasource);
		if($_iptcData->isValid()){
			if(is_array($selection)){
				// fetch some tags
				foreach($selection as $value){
					$this->metadata[] = $_iptcData->getTag($value);
				}
			} else{
				foreach(explode(',', self::usedFields) as $fieldName){
					$_data = $_iptcData->getTag($fieldName);
					if(!is_null($_data)){
						$this->metadata[$fieldName] = $_data;
					}
				}
			}
		}

		return $this->metadata;
	}

}

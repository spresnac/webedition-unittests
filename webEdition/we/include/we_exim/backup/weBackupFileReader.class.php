<?php

/**
 * webEdition CMS
 *
 * $Rev: 5230 $
 * $Author: mokraemer $
 * $Date: 2012-11-26 00:38:44 +0100 (Mon, 26 Nov 2012) $
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
abstract class weBackupFileReader extends weXMLFileReader{

	static function preParse(&$content){
		$match = array();

		if(preg_match('|<we:table(item)?([^>]*)|i', $content, $match)){

			$attributes = explode('=', $match[2]);
			$attributes[0] = trim($attributes[0]);

			if($attributes[0] == 'name' || $attributes[0] == 'table'){
				$attributes[1] = trim(str_replace(array('"', '\''), '', $attributes[1]));

				// if the table should't be imported
				if(weBackupUtil::getRealTableName($attributes[1]) === false){
					return true;
				}
			}
		}

		if((preg_match('|<we:binary><ID>([^<]*)</ID>(.*)<Path>([^<]*)</Path>|i', $content, $match) && !weBackupUtil::canImportBinary($match[1], $match[3])) ||
			(preg_match('|<we:version><ID>([^<]*)</ID>(.*)<Path>([^<]*)</Path>|i', $content, $match) && !weBackupUtil::canImportVersion($match[1], $match[3]))){
			return true;
		}

		return false;
	}

}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5136 $
 * $Author: mokraemer $
 * $Date: 2012-11-13 00:10:47 +0100 (Tue, 13 Nov 2012) $
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
function we_tag_ifRegisteredUser($attribs){

	$permission = weTag_getAttribute('permission', $attribs);
	$match = weTag_getAttribute('match', $attribs);
	$match = makeArrayFromCSV($match);
	$cfilter = weTag_getAttribute('cfilter', $attribs, false, true);
	$allowNoFilter = weTag_getAttribute('allowNoFilter', $attribs, false, true);
	$userid = weTag_getAttribute('userid', $attribs);
	$userid = makeArrayFromCSV($userid);
	$matchType = weTag_getAttribute('matchType', $attribs, 'one');

	if($GLOBALS['we_doc']->InWebEdition || $GLOBALS['WE_MAIN_DOC']->InWebEdition){
		return isset($_SESSION['weS']['we_set_registered']) && $_SESSION['weS']['we_set_registered'];
	}

	//return true only on registered users - or if cfilter is set to "no filter"
	if(isset($_SESSION['webuser']['registered']) && $_SESSION['webuser']['registered']){
		$ret = true;

		if($ret && !empty($userid)){
			if(!isset($_SESSION['webuser']['ID'])){
				return false;
			} else{
				$ret &= ( in_array($_SESSION['webuser']['ID'], $userid));
			}
		}

		if($ret && $permission){
			$ret &= isset($_SESSION['webuser']['registered']) && isset($_SESSION['webuser'][$permission]) && $_SESSION['webuser']['registered'];
			if(!$ret){
				return false;
			}
			if(!empty($match)){
				$perm = explode(',', $_SESSION['webuser'][$permission]);
				switch($matchType){
					case 'one':
						$tmp = array_intersect($perm, $match);
						$ret &= count($tmp) > 0;
						break;
					case 'contains':
						$tmp = array_intersect($perm, $match);
						$ret &= count($tmp) == count($match);
						break;
					default:
					case 'exact':
						$ret &= count($perm) == count($match);
						if($ret){
							$tmp = array_intersect($perm, $match);
							$ret &= count($tmp) == count($perm);
						}
						break;
				}
			} else{
				$ret &= (bool) $_SESSION['webuser'][$permission];
			}
		}

		if($ret && $cfilter && defined('CUSTOMER_TABLE')){
			if(isset($GLOBALS['we_doc']->documentCustomerFilter) && $GLOBALS['we_doc']->documentCustomerFilter){
				$ret &= ( $GLOBALS['we_doc']->documentCustomerFilter->accessForVisitor($GLOBALS['we_doc'], array(), true) == weDocumentCustomerFilter::ACCESS);
			} else{
				//access depends on $allowNoFilter
				return $allowNoFilter;
			}
		}

		return $ret;
	} else{
		//we are not logged in!
		if($cfilter && defined('CUSTOMER_TABLE')){
			if(isset($GLOBALS['we_doc']->documentCustomerFilter) && $GLOBALS['we_doc']->documentCustomerFilter){
				//not logged in - no filter can match
				return false;
			} else{
				//not logged in - but "allow all users" is set - return depends on allowNoFilter
				return $allowNoFilter;
			}
		}
	}
	return false;
}

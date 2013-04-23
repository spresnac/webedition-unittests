<?php

/**
 * webEdition CMS
 *
 * $Rev: 5886 $
 * $Author: mokraemer $
 * $Date: 2013-02-25 16:39:49 +0100 (Mon, 25 Feb 2013) $
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
function we_tag_ifEqual($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		print($foo);
		return false;
	}
	$name = weTag_getAttribute("name", $attribs);
	$eqname = weTag_getAttribute("eqname", $attribs);
	$value = weTag_getAttribute("value", $attribs);

	if(!$eqname){
		if(($foo = attributFehltError($attribs, "value", __FUNCTION__))){
			print($foo);
			return false;
		}
		return ($GLOBALS['we_doc']->getElement($name) == $value);
	}

	if(($foo = attributFehltError($attribs, "eqname", __FUNCTION__))){
		print($foo);
		return false;
	}
	$elem=$GLOBALS['we_doc']->getElement($name);
	$blockeq=we_tag_getPostName($eqname);
	if($GLOBALS["WE_MAIN_DOC"]->getElement($blockeq)){//check if eqname is present in block
		return ($elem == $GLOBALS["WE_MAIN_DOC"]->getElement($blockeq));
	} elseif($GLOBALS["WE_MAIN_DOC"]->getElement($eqname)){//check if eqname is present in document
		return ($elem == $GLOBALS["WE_MAIN_DOC"]->getElement($eqname));
	}else{//check if eqname is present in GLOBALS
		return (isset($GLOBALS[$eqname])) && ($GLOBALS[$eqname] == $elem);
	}
	return false;
}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 4243 $
 * $Author: mokraemer $
 * $Date: 2012-03-10 04:10:59 +0100 (Sat, 10 Mar 2012) $
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
function we_tag_ifNotVote($attribs){
	if(($foo = attributFehltError($attribs, "type", __FUNCTION__)))
		return $foo;
	$type = weTag_getAttribute("type", $attribs, "error");

	if(isset($GLOBALS["_we_voting_status"])){
		switch($type){
			case "error":
				return ($GLOBALS["_we_voting_status"] == weVoting::ERROR);
			case "revote":
				return ($GLOBALS["_we_voting_status"] == weVoting::ERROR_REVOTE);
			case "active":
				return ($GLOBALS["_we_voting_status"] == weVoting::ERROR_ACTIVE);
			case "forbidden":
				return ($GLOBALS["_we_voting_status"] == weVoting::ERROR_BLACKIP);
			default:
				return ($GLOBALS["_we_voting_status"] > 0);
		}
	}
	return false;
}

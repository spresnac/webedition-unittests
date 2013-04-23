<?php

/**
 * webEdition CMS
 *
 * $Rev: 5039 $
 * $Author: mokraemer $
 * $Date: 2012-10-31 01:13:32 +0100 (Wed, 31 Oct 2012) $
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
function we_tag_votingSession(){
	if(!$GLOBALS['we_editmode']){
		$_SESSION['_we_voting_sessionID'] = md5(uniqid(__FUNCTION__, true));
	}
}

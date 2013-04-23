<?php

/**
 * webEdition CMS
 *
 * $Rev: 5612 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 22:46:14 +0100 (Mon, 21 Jan 2013) $
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
function we_tag_writeVoting($attribs){

	$id = weTag_getAttribute('id', $attribs, 0);
	$additionalFields = weTag_getAttribute('additionalfields', $attribs, 0);
	$allowredirect = weTag_getAttribute("allowredirect", $attribs, false, true);
	$deletesessiondata = weTag_getAttribute("deletesessiondata", $attribs, false, true);
	$writeto = weTag_getAttribute("writeto", $attribs, "voting");

	$pattern = '/_we_voting_answer_(' . ($id ? $id : '[0-9]+') . ')_?([0-9]+)?/';

	$vars = implode(',', array_keys($_REQUEST));
	$_voting = array();

	$matches = array();
	if(preg_match_all($pattern, $vars, $matches)){
		foreach($matches[0] as $key => $value){
			$id = $matches[1][$key];
			if(!isset($_voting[$id]) || !is_array($_voting[$id])){
				$_voting[$id] = array();
			}
			if (isset($_REQUEST[$value]) && $_REQUEST[$value]!='') {// Bug #6118: !empty geht hier nicht, da es die 0 nicht durch lässt
				$_voting[$id][] = filterXss($_REQUEST[$value]);
			}
		}
	}
	$additionalFieldsArray = makeArrayFromCSV($additionalFields);
	$addFields = array();
	foreach($additionalFieldsArray as $field){
		if(isset($_REQUEST[$field])){
			$addFields[$field] = filterXss($_REQUEST[$field]);
		}
	}


	if($deletesessiondata){
		unset($_SESSION['_we_voting_sessionData']);
	}


	foreach($_voting as $id => $value){
		if($writeto == 'voting'){
			$voting = new weVoting($id);
			if($voting->IsRequired && implode('', $value) == ''){

				$GLOBALS['_we_voting_status'] = weVoting::ERROR;
				if(isset($_SESSION['_we_voting_sessionID'])){
					$votingsession = $_SESSION['_we_voting_sessionID'];
				} else{
					$votingsession = 0;
				}
				if($voting->Log)
					$voting->logVoting(weVoting::ERROR, $votingsession, '', '', '');
				break;
			}

			$GLOBALS['_we_voting_status'] = $voting->vote($value, $addFields);
			if($GLOBALS['_we_voting_status'] != weVoting::SUCCESS){
				break;
			}
		} else{
			$voting = new weVoting($id);
			if($voting->IsRequired && implode('', $value) == ''){

				$GLOBALS['_we_voting_status'] = weVoting::ERROR;
				if(isset($_SESSION['_we_voting_sessionID'])){
					$votingsession = $_SESSION['_we_voting_sessionID'];
				} else{
					$votingsession = 0;
				}
				if($voting->Log)
					$voting->logVoting(weVoting::ERROR, $votingsession, '', '', '');
				break;
			}

			$GLOBALS['_we_voting_status'] = $voting->setSuccessor($value);
			if($GLOBALS['_we_voting_status'] != weVoting::SUCCESS){
				break;
			}
			$_SESSION['_we_voting_sessionData'][$id] = array('value' => $value, 'addFields' => $addFields);
		}
	}
	if($allowredirect && !$GLOBALS["WE_MAIN_DOC"]->InWebEdition && isset($GLOBALS['_we_voting_SuccessorID']) && $GLOBALS['_we_voting_SuccessorID'] > 0){
		$mypath = id_to_path($GLOBALS['_we_voting_SuccessorID']);
		if($mypath != $_SERVER['SCRIPT_NAME']){
			header("Location: " . $mypath); /* Redirect browser */

			/* Make sure that code below does not get executed when we redirect. */
			exit;
		}
	}
}
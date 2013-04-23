<?php

/**
 * webEdition CMS
 *
 * $Rev: 5101 $
 * $Author: mokraemer $
 * $Date: 2012-11-08 16:19:49 +0100 (Thu, 08 Nov 2012) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/webEdition/liveUpdate/includes/proxysettings.inc.php")){
	include_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/liveUpdate/includes/proxysettings.inc.php");
}

/*
 * Include all needed files
 */
require_once('includes/includes.inc.php');
we_html_tools::protect();

/*
 * Deal with update_cmd
 */
if(isset($_REQUEST['update_cmd'])){

	/*
	 * Gather all needed Variables for the update-Request
	 */
	$parameters = array();

	foreach($LU_ParameterNames as $parameterName){

		if(isset($_REQUEST[$parameterName])){
			$parameters[$parameterName] = $_REQUEST[$parameterName];
		}
	}

	// this is flag to check if a response was received!
	$response = false;

	/*
	 * For command checkConnection, it is not needed to create a session on the
	 * server. Therefore treat this command in a special way.
	 */
	if($_REQUEST['update_cmd'] == 'checkConnection'){

		$response = liveUpdateHttp::getHttpResponse(LIVEUPDATE_SERVER, LIVEUPDATE_SERVER_SCRIPT, $parameters);
		$liveUpdateResponse = new liveUpdateResponse();

		if($liveUpdateResponse->initByHttpResponse($response)){

			if($liveUpdateResponse->isError()){
				print liveUpdateFrames::htmlConnectionSuccess($liveUpdateResponse->getField('Message'));
			} else{
				print liveUpdateFrames::htmlConnectionSuccess();
			}
		} else{
			print liveUpdateFrames::htmlConnectionError();
		}
		exit();
	}
	/*
	 * Before an update_cmd is submitted to the server, there must be an
	 * existing session on the server. $_REQUEST[liveUpdateSession] contains
	 * the session_id of the server. If this id is missing, create a new
	 * session on the server.
	 */
	if(!isset($_REQUEST['liveUpdateSession'])){

		/*
		 * exit after submitting the form
		 */
		print liveUpdateHttp::getServerSessionForm();
		exit;
	} else{
		/*
		 * $_REQUEST['liveUpdateSession'] exists => Session on server is up
		 * prepare all needed variables to submit to the updateServer
		 * These are stored in $LU_ParameterNames
		 */

		// add all other request parameters to the request
		$reqVars = array();
		foreach($_REQUEST as $key => $value){
			if(!isset($parameters[$key]) && !in_array($key, $LU_IgnoreRequestParameters) && !array_key_exists($key, $_COOKIE)){
				$reqVars[$key] = $value;
			}
		}
		$parameters['reqArray'] = base64_encode(serialize($reqVars));

		$response = liveUpdateHttp::getHttpResponse(LIVEUPDATE_SERVER, LIVEUPDATE_SERVER_SCRIPT, $parameters);
	}

	/*
	 * There is a response from the Update-Server.
	 */
	if($response){

		$liveUpdateResponse = new liveUpdateResponse();

		print ($liveUpdateResponse->initByHttpResponse($response) ?
				$liveUpdateResponse->getOutput() :
				liveUpdateFrames::htmlConnectionError());
	} else{
		/*
		 * No response from the update-server. Error message
		 */
		print liveUpdateFrames::htmlConnectionError();
	}
} else{
	/*
	 * No update_cmd exists, show normal frameset
	 */
	$updateFrames = new liveUpdateFrames();
	print $updateFrames->getFrame();
}

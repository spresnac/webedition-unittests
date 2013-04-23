<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
we_html_tools::protect();

if(isset($_REQUEST['we_transaction'])){ //  initialise Document
	if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction'])){
		exit();
	}

	$we_transaction = $_REQUEST['we_transaction'];

	$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : "";
	include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

	$GLOBALS['we_doc']->InWebEdition = false;

	$content = $GLOBALS['we_doc']->getDocument();


	$allowedHosts = array('validator.w3.org');

	$GLOBALS['DB_WE']->query('SELECT host FROM ' . VALIDATION_SERVICES_TABLE);
	while($GLOBALS['DB_WE']->next_record()) {
		$allowedHosts[] = $GLOBALS['DB_WE']->f('host');
	}


	$host = $_REQUEST['host'];

	if(!in_array($host, $allowedHosts)){
		exit($host . ' not in allowed hosts!');
	}


	$path = $_REQUEST['path'];
	$s_method = $_REQUEST['s_method'];
	$varname = $_REQUEST['varname'];
	$contentType = $_REQUEST['ctype'];

	$http_request = new HttpRequest($path, $host, $s_method);

	//  add additional parameters to the request
	if($_REQUEST['additionalVars']){
		$args = explode('&', $_REQUEST['additionalVars']);
		foreach($args as $pair){
			$keyValue = explode('=', $pair);
			$http_request->addVar($keyValue[0], $keyValue[1]);
		}
	}

	//  generate name of file.  - must be .html because of <?xml and short-open tags
	$extension = $GLOBALS['we_doc']->Extension;
	$filename = '/' . $we_transaction . $extension;

	//  check what should happen with document
	if($_REQUEST['checkvia'] == 'fileupload'){ //  submit via fileupload
		$http_request->addFileByContent($varname, $content, $contentType, $filename);
	} else{	//  submit via onlinecheck - site must be available online
		// when it is a dynamic document, remove <?xml when short_open_tags are allowed.
		if(ini_get("short_open_tag") == 1 && $GLOBALS['we_doc']->IsDynamic && $contentType == "text/html"){
			$content = str_replace("<?xml", '<?php print "<?xml"; ?>', $content);
		}

		//  save file - submit URL to service
		$tmpFile = $_SERVER['DOCUMENT_ROOT'] . $filename;
		we_util_File::saveFile($tmpFile, $content);
		we_util_File::insertIntoCleanUp($tmpFile, time());

		$url = getServerUrl() . $filename;
		$http_request->addVar($varname, $url);
	}

	$http_request->executeHttpRequest();

	//  check if all worked well..
	if(!$http_request->error){

		$http_response = new HttpResponse($http_request->getHttpResponseStr());

		if($http_response->getHttp_answer('code') == 200){
			//  change base href -> css of included page is loaded correctly
			print str_replace('<head>', '<head><base href="http://' . $host . '" />', $http_response->http_body);
		} else{	//  no correct answer
			we_html_tools::htmlTop();
			print STYLESHEET;
			print '</head>
                <body>';
			print we_html_tools::htmlAlertAttentionBox(sprintf(g_l('validation', '[connection_problems]'), $http_response->getHttp_answer()), 1, 0, false);
			print '</body></html>';
		}
	} else{
		print $http_request->errno . ": " . $http_request->errstr . "<br>";
	}
} else{
	print ' &hellip; ';
}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
 * This class can create a HTTP Request. It is possible to submit data to a certain web-site
 * via POST or GET.
 */
class HttpRequest{

	var $http_path = '';
	var $http_host = '';
	var $http_method = '';
	var $http_protocol = '';
	var $http_port = 80;
	var $http_headers = array();
	var $http_body = '';
	var $http_response = '';
	//  datas to submit
	var $files = array(); //  files array
	var $vars = array(); //  array with variables
	// in case something went wrong with the connection
	var $error = false;
	var $errno = 0;
	var $errstr = 0;

	function __construct($path, $host, $method = 'POST', $protocol = 'HTTP/1.0'){

		$this->http_path = $path;
		$this->http_host = $host;
		$this->http_method = (strtoupper($method) == 'GET' ? 'GET' : 'POST'); //  only get or post is allowed

		$this->http_protocol = $protocol;
		$this->http_headers['Host'] = $host;
	}

	/**
	 * @return void
	 * @param string $varname
	 * @param string $content
	 * @param string $contentType
	 * @param string $filename
	 * @desc This function adds a file to the HTTP-Request, by given varname (of form), content, content-type and filename
	 */
	function addFileByContent($varname, $content, $contentType = 'text/html', $filename = 'foo.html'){

		$this->files[] = array(
			'varname' => $varname,
			'filename' => $filename,
			'contentType' => $contentType,
			'content' => $content
		);
	}

	/**
	 * @return bool
	 * @param string $path
	 * @param string $varname
	 * @param string $contentType
	 * @param string $filename
	 * @desc This function adds a file by path (could be a web-path) and adds it to the HttpRequest.
	 *       returns false, when file does not exist.
	 */
	function addFileByPath($path, $varname, $contentType = 'text/html', $filename = 'foo.html'){
		if(file_exists($path)){

			$fileArr = file($path);
			$content = implode("\r\n", $fileArr);

			$this->addFileByContent($varname, $content, $contentType, $filename);
			return true;
		}
		return false;
	}

	/**
	 * @return void
	 * @param string $name
	 * @param string $value
	 * @desc Adds a header to the HTTP-Request
	 */
	function addHeader($name, $value){

		$this->http_headers[trim($name)] = trim($value);
	}

	/**
	 * @return void
	 * @param array $headers
	 * @desc Adds associative (name => value) to the headers of the Request
	 */
	function addHeaders($headers){

		foreach($headers as $k => $v){
			$this->http_headers[trim($k)] = trim($v);
		}
	}

	/**
	 * @return void
	 * @param string $name
	 * @param string $value
	 * @desc Adds variable by name and value to the Variables
	 */
	function addVar($name, $value){
		$this->vars[] = array(
			'name' => $name,
			'value' => $value
		);
	}

	/**
	 * @return void
	 * @param array $varsAr
	 * @desc Adds associative array (name => value) to the variables of the form
	 */
	function addVars($varsAr){
		foreach($varsAr as $k => $v){
			$this->addVar($k, $v);
		}
	}

	/**
	 * @return void
	 * @desc executes the HTTP Request via curl, saves errors in $error - or response in var $http_response
	 */
	function executeCurlHttpRequest(){

		$tmp = array();
		foreach($this->vars as $var){
			$tmp [] = $var['name'] . '=' . $var['value'];
		}
		$path = $this->http_path . (!empty($tmp) ? '?' . implode('&', $tmp) : '');

		$this->getHttpRequest();

		$_header[] = $this->http_method . ' ' . $path . ' ' . $this->http_protocol;
		foreach($this->http_headers as $k => $v){
			$_header[] = "$k: $v";
		}
		$_header[] = $this->http_body;

		$_session = curl_init();
		curl_setopt($_session, CURLOPT_URL, 'http://' . $this->http_host . ($this->http_method == 'GET' ? $path : $this->http_path));
		curl_setopt($_session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($_session, CURLOPT_CUSTOMREQUEST, $this->http_method);
		curl_setopt($_session, CURLOPT_HEADER, 1);
		curl_setopt($_session, CURLOPT_HTTPHEADER, $_header);
		curl_setopt($_session, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($_session, CURLOPT_MAXREDIRS, 5);

		$_data = curl_exec($_session);

		if(curl_errno($_session)){
			$this->error = true;
			$this->errno = 1;
			$this->errstr = curl_error($_session);
		} else{
			$this->http_response = $_data;
			$this->error = false;
			curl_close($_session);
		}
	}

	/**
	 * @return void
	 * @desc executes the HTTP Request, saves errors in $error - or response in var $http_response
	 */
	function executeHttpRequest(){

		$_http_opt = getHttpOption();

		switch($_http_opt){
			case 'fopen':
				$req = $this->getHttpRequest();
				$socket = @fsockopen($this->http_host, $this->http_port, $errno, $errstr, 1);

				if($socket){ //  connection etablished
					fwrite($socket, $req);
					$response = '';
					while(!feof($socket)) {
						$response .= fgets($socket, 1024);
					}
					fclose($socket);

					$this->http_response = $response;
				} else{ //  something wrong happened
					$this->error = true;
					$this->errno = $errno;
					$this->errstr = $errstr;
				}
				break;
			case 'curl':

				$this->executeCurlHttpRequest();
				break;
			default:
				$this->error = true;
				$this->errno = 1;
				$this->errstr = 'Server error: Unable to open URL (php configuration directive allow_url_fopen=Off)';
				break;
		}
	}

	/**
	 * @return string
	 * @desc Builds and returns the Http-Request from the given values
	 */
	function getHttpRequest(){

		//  first build body of request, then headers
		$body = '';

		$_sizeFiles = count($this->files);
		$_sizeVars = count($this->vars);

		$path = $this->http_path;

		if($_sizeFiles || $_sizeVars){

			//  it is necessary to differ from POST/GET requests
			if($this->http_method == 'POST'){ //  method 'POST'
				//  boundary to seperate between different content blocks
				$boundary = 'accessibility_webEdition' . str_replace('.', '', uniqid('', true));

				foreach($this->files as $file){
					//  important not ot forget the leading '--'
					$body .= '--' . $boundary . "\r\n" .
						'Content-Disposition: form-data; name="' . $file['varname'] . '"; filename="' . $file["filename"] . '"' . "\r\n" .
						'Content-Type: ' . $file['contentType'] . "\r\n" .
						"\r\n" . $file['content'] . "\r\n";
				}

				foreach($this->vars as $var){

					$body .= '--' . $boundary . "\r\n" .
						'Content-Disposition: form-data; name="' . $var['name'] . "\"\r\n" .
						"\r\n" . $var['value'] . "\r\n";
				}
				//  at last boundary we must attach '--'
				$body .= '--' . $boundary . "--\r\n";

				//  add 2 more headers for this request
				$this->http_headers['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
				$this->http_headers['Content-Length'] = strlen($body);
			} else{ //  method 'GET'
				//  all variables are joined to the path
				$tmp = array();
				foreach($this->vars as $var){
					$tmp[] = $var['name'] . '=' . $var['value'];
				}
				$path.=(!empty($tmp) ? '?' . implode('&', $tmp) : '');
			}
		} else{ //  no files or vars to submit
		}

		$this->http_body = $body;

		/*
		  Build header for this Request
		 */
		$head = $this->http_method . ' ' . $path . ' ' . $this->http_protocol . "\r\n";
		foreach($this->http_headers as $k => $v){
			$head .= "$k: $v\r\n";
		}
		$head .= "\r\n";

		return $head . $body;
	}

	/**
	 * @return string
	 * @desc returns the raw - httpResponse including header and content
	 */
	function getHttpResponseStr(){
		return($this->error ? '' : $this->http_response);
	}

	/**
	 * @return void
	 * @param int $port
	 * @desc sets the port for this http Request
	 */
	function setPort($port = 80){
		$this->http_port = $port;
	}

}

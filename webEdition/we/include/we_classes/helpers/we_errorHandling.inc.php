<?php

/**
 * webEdition CMS
 *
 * $Rev: 4397 $
 * $Author: mokraemer $
 * $Date: 2012-04-09 00:06:42 +0200 (Mon, 09 Apr 2012) $
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
 * Class we_errorHandling
 *
 * This is the error handling class of webEdition.
 */
class we_errorHandling{

	/**
	 * This function builds the error message that will be send to the browser.
	 *
	 * @param      $statuscode string
	 * @param      $message string
	 *
	 * @return     string
	 */
	function errormessage($statuscode = "403 Forbidden", $message = "You don't have permission to access / on this server."){
		// Build the message
		return we_html_tools::getHtmlTop($statuscode) .
			'</head>' . we_html_element::htmlBody(
				"<h1>" . substr($statuscode, 4) . "</h1>
		<p>" . $message . "</p>
		<hr />
		<address>" . $_SERVER["SERVER_SOFTWARE"] . " at " . $_SERVER["SERVER_NAME"] . " Port " . $_SERVER["SERVER_PORT"] . "</address>
		") . '</html>';
	}

	/**
	 * This function sends a error status to the webserver.
	 *
	 * @param      $statuscode string
	 *
	 * @return     void
	 */
	function status_header($statuscode = "403"){
		// First off we check the way PHP is being used by the webserver
		if(preg_match("/IIS/", $_SERVER["SERVER_SOFTWARE"]) || php_sapi_name() == "cgi"){
			// PHP is being used by the IIS webserver of Microsoft and/or as a CGI module
			$_headerstatus = "Status: ";
		} else{
			// PHP is neither being used by the IIS webserver of Microsoft and/or as a CGI module
			$_headerstatus = "HTTP/1.0 ";
		}

		// Get the requested filename
		$_requested_filename = $_SERVER["REQUEST_URI"];

		// Check what status code to send
		switch($statuscode){
			case "404":
				// We will add the status code to the header later being given to the webserver
				$_headerstatus .= "404 Not Found";

				// Now we generate the code to display to the user as the webserver will show no result
				$_errormessage = $this->errormessage("404 Not Found", "The requested URL " . $_requested_filename . " was not found on this server.");

				// As we are ready, we can leave this switch statement
				break;

			case "403":
			default:
				// We will add the status code to the header later being given to the webserver
				$_headerstatus .= "403 Forbidden";

				// Now we generate the code to display to the user as the webserver will show no result
				$_errormessage = $this->errormessage("403 Forbidden", "You don't have permission to access " . $_requested_filename . " on this server.");

				// As we are ready, we can leave this switch statement
				break;
		}

		// Now we are ready to send the code to the webserver
		header($_headerstatus);

		// Last thing to do is to display the error to the user
		echo($_errormessage);
	}

}
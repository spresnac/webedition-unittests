<?php
/**
 * webEdition CMS
 *
 * $Rev: 4462 $
 * $Author: mokraemer $
 * $Date: 2012-04-25 14:14:31 +0200 (Wed, 25 Apr 2012) $
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
include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_button.inc.php");


class liveUpdateResponse {

	var $Type;
	var $Headline;
	var $Content;
	var $Header;
	var $Code;
	var $EncodedCode;
	var $Encoding = false;

	function initByArray($respArray) {

		foreach ($respArray as $key => $value) {

			$this->$key = $value;
		}

		if ($this->Encoding && $this->EncodedCode) {
			$this->Code = base64_decode($this->EncodedCode);
		}
	}

	/**
	 * init the object with the response from the update-server
	 *
	 * @param string $response
	 * @return boolean
	 */
	function initByHttpResponse($response) {

		if ($respArr = liveUpdateResponse::responseToArray($response)) {

			$this->initByArray($respArr);
			return true;
		} else {
			return false;
		}
	}

	function isError() {

		if ($this->Type == 'state' && $this->State == 'error') {
			return true;
		}
		return false;
	}

	function getField($fieldname) {
		if (isset($this->$fieldname)) {
			return $this->$fieldname;
		}
		return '';
	}

	function responseToArray($response) {

		$respArray = @unserialize(base64_decode($response));

		if (is_array($respArray)) {
			return $respArray;
		} else {
			return false;
		}
	}

	function getOutput() {

		switch ($this->Type) {

			case 'template':
				return liveUpdateTemplates::getHtml(
					$this->Headline,
					$this->Content,
					$this->Header
				);
			break;

			case 'executePatches':
				return  liveUpdateFunctionsServer::executeAllPatches();
			case 'eval':
				return eval('?>' . $this->Code);
			break;

			case 'state':
				return liveUpdateFrames::htmlStateMessage();
				return 'Meldung vom Server:<br />Status: ' . $this->State . '<br />Meldung: ' . $this->Message;
			break;

			default:
				return $this->Type . ' is not implemented yet';
			break;
		}
	}
}
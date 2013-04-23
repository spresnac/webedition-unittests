<?php

/**
 * webEdition CMS
 *
 * $Rev: 4598 $
 * $Author: mokraemer $
 * $Date: 2012-06-17 02:02:35 +0200 (Sun, 17 Jun 2012) $
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
class weOrderContainer{

	// DEBUG
	var $debug = false;
	// private Target Frame
	var $targetFrame = "";
	// private containerId
	var $containerId = "";
	// private containerType
	var $containerType = "";

	function __construct($targetFrame, $id, $type = "div"){
		$this->targetFrame = $targetFrame;
		$this->containerId = $id;
		$this->containerType = $type;
	}

	function getJS($jsPath){

		$src = '';
		if(!defined("weOrderContainer_JS_loaded")){
			$src = we_html_element::jsScript($jsPath . '/weOrderContainer.js?t=' . time());
			define("weOrderContainer_JS_loaded", true);
		}
		$src .= we_html_element::jsElement('var ' . $this->containerId . ' = new weOrderContainer("' . $this->containerId . '");');

		return $src;
	}

// end: getJs

	function getContainer($attribs = array()){

		if($this->debug){
			$style = ' style="display: block; border: 1px #ff0000 solid;"';
		} else{
			$style = '';
		}

		$attrib = "";
		foreach($attribs as $name => $value){
			$attrib .= " " . $name . "=\"" . $value . "\"";
		}

		$src = '<' . $this->containerType . ' id="' . $this->containerId . '"' . $style . $attrib . '>'
			. '</' . $this->containerType . '>';

		return $src;
	}

// end: getContainer

	function getCmd($mode, $uniqueid = false, $afterid = false){

		$prefix = $this->targetFrame . "." . $this->containerId;

		if($afterid){
			$afterid = "'" . $afterid . "'";
		} else{
			$afterid = "null";
		}

		switch(strtolower($mode)){
			case 'add':
				$cmd = $prefix . ".add(document, '" . $uniqueid . "', $afterid);";
				break;
			case 'reload':
				$cmd = $prefix . ".reload(document, '" . $uniqueid . "');";
				break;
			case 'delete':
			case 'del':
				$cmd = $prefix . ".del('" . $uniqueid . "');";
				break;
			case 'moveup':
			case 'up':
				$cmd = $prefix . ".up('" . $uniqueid . "');";
				break;
			case 'movedown':
			case 'down':
				$cmd = $prefix . ".down('" . $uniqueid . "');";
				break;
			default:
				$cmd = "";
				break;
		}

		return $cmd;
	}

// end: getCmd

	function getResponse($mode, $uniqueid, $string = "", $afterid = false){

		$cmd = $this->getCmd($mode, $uniqueid, $afterid);
		if($cmd == ""){
			return "";
		}

		if($this->debug){
			$style = ' style="display: block; width: 90%; height: 90%; overflow: auto; border: 1px #ff0000 solid; font-family: verdana, arial; font-size: 11px; color: #000000; padding: 5px;"';
		} else{
			$style = ' style="display: none;"';
		}

		$src = "";
		if($string != "" || $this->debug){
			$src .= '<' . $this->containerType . ' id="' . $this->containerId . '"' . $style . '>'
				. $string
				. '</' . $this->containerType . '>' . "\n";
		}

		$src .= we_html_element::jsElement($cmd);

		$src .= $this->getDisableButtonJS();
		return $src;
	}

// end: getResponse

	function getDisableButtonJS(){

		return we_html_element::jsElement(
				'for(i = 0; i < ' . $this->targetFrame . '.' . $this->containerId . '.position.length; i++) {' . "\n"
				. '	id = ' . $this->targetFrame . '.' . $this->containerId . '.position[i];' . "\n"
				. '	id = id.replace(/entry_/, "");' . "\n"
				. '	' . $this->targetFrame . '.weButton.enable("btn_direction_up_" + id);' . "\n"
				. '	' . $this->targetFrame . '.weButton.enable("btn_direction_down_" + id);' . "\n"
				. '	if(i == 0) {' . "\n"
				. '		' . $this->targetFrame . '.weButton.disable("btn_direction_up_" + id);' . "\n"
				. '	}' . "\n"
				. '	if(i+1 == ' . $this->targetFrame . '.' . $this->containerId . '.position.length) {' . "\n"
				. '		' . $this->targetFrame . '.weButton.disable("btn_direction_down_" + id);' . "\n"
				. '	}' . "\n"
				. '}');
	}

}

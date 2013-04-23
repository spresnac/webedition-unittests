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
class weCodeWizard{

	/**
	 * Directory where the snippets are located
	 *
	 * @var string
	 */
	var $SnippetPath = "";

	/**
	 * PHP 5 constructor
	 *
	 */
	function __construct(){
		$this->SnippetPath = WE_INCLUDES_PATH . 'weCodeWizard/data/';
	}

	/**
	 * PHP 4 constructor
	 *
	 * @return weCodeWizard
	 */
	function weCodeWizard(){
		$this->__construct();
	}

	/**
	 * get all custom specific snippets
	 *
	 * @return array
	 */
	function _getCustomSnippets(){


		$SnippetDir = $this->SnippetPath . 'custom';
		if(!is_dir($SnippetDir)){
			return array();
		} else{
			return $this->_getSnippetsByDir('custom');
		}
	}

	/**
	 * get all standard snippets
	 *
	 * @return array
	 */
	function _getStandardSnippets(){

		$SnippetDir = $this->SnippetPath . 'default';

		if(!is_dir($SnippetDir)){
			return array();
		} else{
			return $this->_getSnippetsByDir('default');
		}
	}

	/**
	 * get snippets by directory name
	 *
	 * @return array
	 */
	function _getSnippetsByDir($SnippetDir, $Depth = 0){

		$Snippets = array();

		$Depth++;
		$_dir = dir($this->SnippetPath . $SnippetDir);
		while(false !== ($_entry = $_dir->read())) {

			// ignore files . and ..
			if($_entry == "." || $_entry == ".."){
				// ignore these
				// get the snippets by file if extension is xml
			} else
			if(!is_dir($this->SnippetPath . $SnippetDir . "/" . $_entry) && substr_compare($_entry, ".xml", -4, 4, true) == 0){
				// get the snippet
				$_snippet = weCodeWizardSnippet::initByXmlFile(
						$this->SnippetPath . $SnippetDir . "/" . $_entry);
				$_item = array(
					'type' => 'option',
					'name' => $_snippet->getName(),
					'value' => $SnippetDir . "/" . $_entry
				);
				$Snippets[] = $_item;

				// enter subdirectory only if depth is smaller than 2
			} else
			if(is_dir($this->SnippetPath . $SnippetDir . "/" . $_entry) && $Depth < 2){

				$information = array();
				$_infoFile = $this->SnippetPath . $SnippetDir . "/" . $_entry . "/" . "_information.php";
				if(file_exists($_infoFile) && is_file($_infoFile)){
					include ($_infoFile);
				}

				$_foldername = $_entry;
				if(isset($information['foldername'])){
					$_foldername = $information['foldername'];
				}

				$_folder = array(
					'type' => 'optgroup',
					'name' => $_foldername,
					'value' => $this->_getSnippetsByDir($SnippetDir . "/" . $_entry, $Depth)
				);
				$Snippets[] = $_folder;
			}
		}
		$_dir->close();

		$Depth--;

		return $Snippets;
	}

	/**
	 * create the select box to select a snippet
	 *
	 * @param string $type
	 * @return string
	 */
	function getSelect($type = 'standard'){

		$_options = array();

		switch($type){
			case 'custom' :
				$_options = $this->_getCustomSnippets();
				break;

			default :
				$_options = $this->_getStandardSnippets();
				break;
		}

		$_select = "<select id=\"codesnippet_" . $type . "\" name=\"codesnippet_" . $type . "\"  size=\"7\" style=\"width:250px; height: 100px; display: none;\" ondblclick=\"YUIdoAjax(this.value);\" onchange=\"weButton.enable('btn_direction_right_applyCode')\">\n";
		foreach($_options as $option){
			if($option['type'] == 'optgroup' && count($option['value']) > 0){
				$_select .= "<optgroup label=\"" . $option['name'] . "\">\n";

				foreach($option['value'] as $optgroupoption){

					if($optgroupoption['type'] == 'option'){
						$_select .= "<option value=\"" . $optgroupoption['value'] . "\">" . $optgroupoption['name'] . "</option>\n";
					}
				}
				$_select .= "</optgroup>\n";
			} else
			if($option['type'] == 'option'){
				$_select .= "<option value=\"" . $option['value'] . "\">" . $option['name'] . "</option>\n";
			}
		}
		$_select .= "</select>\n";

		return $_select;
	}

	/**
	 * get the needed javascript for the codewizard
	 *
	 * @return string
	 */
	function getJavascript(){
		return we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js') .
			<<<JS

<script type="text/javascript"><!--

var ajaxURL = "/webEdition/rpc/rpc.php";
var ajaxCallback = {
	success: function(o) {
		if(typeof(o.responseText) != 'undefined' && o.responseText != '') {
			document.getElementById('tag_edit_area').value = o.responseText;
		}
	},
	failure: function(o) {
		alert("Failure");
	}
}

function YUIdoAjax(value) {
	YAHOO.util.Connect.asyncRequest('POST', ajaxURL, ajaxCallback, 'protocol=text&cmd=GetSnippetCode&we_cmd[1]=' + value);
}
//-->
</script>
JS;
	}

}

/**
 * Code Sample
 *
 * $CodeWizard = new weCodeWizard();
 *
 * echo $CodeWizard->buildDialog();
 *
 */

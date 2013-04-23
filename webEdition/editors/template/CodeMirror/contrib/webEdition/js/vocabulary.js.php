<?php

/**
 * webEdition CMS
 *
 * $Rev: 4957 $
 * $Author: mokraemer $
 * $Date: 2012-09-14 02:17:42 +0200 (Fri, 14 Sep 2012) $
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
 *
 * This File creates in the top window (DOM) a javascript array of object containing we:tag descriptions and attributes.
 * It is beeing saven in the top window, so it is cached and there's no need to load this file more than once in a session.
 * Before including this file, check if top.we_tags is defined:
 *
 * if(top.we_tags==undefined) { //this is our tag cache
 *    document.write("<scr"+"ipt src=\"/webEdition/editors/template/CodeMirror/contrib/webEdition/js/vocabulary.js.php\" type=\"text/javascript\"></sc"+"ript>");
 * };
 *
 * Tag description and attributes are taken right from the tag descriptor files.
 *
 * 2 examples of array elements:
 *
 *   top.we_tags["we:tag"] = {
 *      "desc": "This is the tags description",
 *      "attributes": {
 *         "attributeName": {
 *            "value 1": 3, // 3 indicates this is an default option
 *            "value 2": 3,
 *            "value 3": 3
 *         },
 *         "anotherAttribute": 2 // 2 indicates, this attribute has no default options
 *      }
 *   };
 *
 *   top.we_tags["we:anotherTag"] = {
 *      "desc": "This is the tags description",
 *      "attributes": 1 // 1 indicates this tag has no default attributes
 *   };
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 * @author     Daniel Schroeder  <deemes79 at googlemail.com>
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

echo 'top.we_tags=new Array();';
$allWeTags = weTagWizard::getExistingWeTags();
foreach($allWeTags as $tagName){
	$GLOBALS['TagRefURLName'] = strtolower($tagName);
	if(isset($weTag)){
		unset($weTag);
	}
	$weTag = weTagData::getTagData($tagName);
	echo sprintf('top.we_tags["we:%s"]= {', $tagName) .
		sprintf('"desc": "%s",', addslashes($weTag->getDescription())) .
		'"attributes":';
	$attr = $weTag->getAllAttributes();

	if(count($attr)){
		echo '{';
		$attributes = array();
		foreach($attr as $attribute){
			$attributeString = sprintf('"%s":', $attribute);
			$options = $weTag->getTypeAttributeOptions();
			if($options){
				$attributeString.='{';
				$optionsJS = array();
				foreach($options as $option){
					if($option->Value != '-'){
						$optionsJS[] = sprintf('"%s": 3', $option->Value);
					}
				}
				$attributeString.=implode(',', $optionsJS);
				$attributeString.='}';
			} else{
				$attributeString.='2';
			}
			$attributes[] = $attributeString;
		}
		echo implode(',', $attributes);
		echo '}';
	} else{
		echo '1';
	}
	echo '};';
}
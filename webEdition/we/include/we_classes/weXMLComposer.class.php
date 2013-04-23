<?php

/**
 * webEdition CMS
 *
 * $Rev: 3695 $
 * $Author: mokraemer $
 * $Date: 2012-01-01 18:33:26 +0100 (Sun, 01 Jan 2012) $
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
abstract class weXMLComposer{

	static function we_xmlElement($name, $content="", $attributes=null){
		$element = new we_baseElement($name, true, (isset($attributes) && is_array($attributes) ? $attributes : null), $content);
		return $element->getHTML();
	}

	/* Function creates new xml element.
	 *
	 * element - [name] - element name
	 * 				 [attributes] - atributes array in form arry["attribute_name"]=attribute_value
	 * 				 [content] - if array childs otherwise some content
	 *
	 */

	static function buildXMLElements($elements){
		$out = "";
		$content = "";
		foreach($elements as $element){
			if(is_array($element["content"])){
				$content = weXMLComposer::buildXMLElements($element["content"]);
			}
			else
				$content = $element["content"];
			$element = new we_baseElement($element["name"], true, $element["attributes"], $content);
			$out.=$element->getHTML();
		}
		return $out;
	}

	static function buildAttributesFromArray($attribs){

		if(!is_array($attribs)){
			return '';
		}
		$out = '';
		foreach($attribs as $k => $v){
			if($v == null && $v != ""){
				$out.=' ' . $k;
			} else{
				$out.=' ' . $k . '="' . $v . '"';
			}
		}

		return $out;
	}

}

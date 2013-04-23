<?php

/**
 * webEdition CMS
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
 * shows a comment in the defined language
 * @param type $attribs
 * @param type $content
 * @return type
 */
function we_parse_tag_comment($attribs, $content){
	eval('$arr = ' . $attribs . ';');
	$type = weTag_getParserAttribute('type', $arr);
	//remove we: parts since this will confuse the tag parser and it will parse these tags
	$content = str_replace('we:', 'we', $content);
	switch($type){
		case 'xml':
		case 'html':
			return '<!-- ' . $content . ' -->';
		case 'js':
			return '/* ' . $content . ' */';
		case 'php':
			return '<?php /*' . str_replace('*/', '', $content) . '*/ ?>';
		default:
			return '';
	}
}

/**
 * shows a comment in the defined language
 * @param type $attribs
 * @param type $content
 * @return type
 */
function we_tag_comment(){
	/**
	 * Dummy tag - only parser part needed
	 */
}

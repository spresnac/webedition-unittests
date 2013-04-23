<?php

/**
 * webEdition CMS
 *
 * $Rev: 5889 $
 * $Author: mokraemer $
 * $Date: 2013-02-25 21:53:09 +0100 (Mon, 25 Feb 2013) $
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
 * @package    webEdition_wysiwyg
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
if(!(isset($_REQUEST['we_dialog_args']) && isset($_REQUEST['we_dialog_args']['outsideWE']) && $_REQUEST['we_dialog_args']['outsideWE'] == 1)){
	we_html_tools::protect();
}
$dialog = new weHyperlinkDialog();
$dialog->initByHttp();
$dialog->registerCmdFn('weDoLinkCmd');
print $dialog->getHTML();

function weDoLinkCmd($args){
	if((!isset($args['href'])) || $args['href'] == 'http://'){
		$args['href'] = '';
	}
	$param = trim($args['param'], '?& ');
	$anchor = trim($args['anchor'], '# ');
	if(!empty($param)){
		$tmp = array();
		parse_str($param, $tmp);
		$param = '?' . http_build_query($tmp, null, '&');
	}

	// TODO: $args['href'] comes from weHyperlinkDialog with params and anchor: strip these elements there, not here!
	$href = (strpos($args['href'], '?') !== false ? substr($args['href'], 0, strpos($args['href'], '?')) :
			(strpos($args['href'], '#') === false ? $args['href'] : substr($args['href'], 0, strpos($args['href'], '#')))) . $param . ($anchor ? '#' . $anchor : '');

	if(!(isset($_REQUEST['we_dialog_args']['editor']) && $_REQUEST['we_dialog_args']['editor'] == 'tinyMce')){
		return we_html_element::jsElement(
				'top.opener.weWysiwygObject_' . $args['editname'] . '.createLink("' . $href . '","' . $args['target'] . '","' . $args['class'] . '","' . $args['lang'] . '","' . $args['hreflang'] . '","' . $args['title'] . '","' . $args['accesskey'] . '","' . $args['tabindex'] . '","' . $args['rel'] . '","' . $args['rev'] . '");
top.close();
');
	} else{
		if(strpos($href, 'mailto:') === 0){
			$href = $args['href'] . (empty($param) ? '' : $param);
			$tmpClass = $args['class'];
			foreach($args as &$val){
				$val = '';
			}
			$args['class'] = $tmpClass;
		}

		return weDialog::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/welink/js/welink_insert.js') .
			'<form name="tiny_form">
			<input type="hidden" name="href" value="' . $href . '">
			<input type="hidden" name="target" value="' . $args["target"] . '">
			<input type="hidden" name="class" value="' . $args["class"] . '">
			<input type="hidden" name="lang" value="' . $args["lang"] . '">
			<input type="hidden" name="hreflang" value="' . $args["hreflang"] . '">
			<input type="hidden" name="title" value="' . $args["title"] . '">
			<input type="hidden" name="accesskey" value="' . $args["accesskey"] . '">
			<input type="hidden" name="tabindex" value="' . $args["tabindex"] . '">
			<input type="hidden" name="rel" value="' . $args["rel"] . '">
			<input type="hidden" name="rev" value="' . $args["rev"] . '">
			</form>';
	}
}

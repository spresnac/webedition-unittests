<?php

/**
 * webEdition CMS
 *
 * $Rev: 5185 $
 * $Author: mokraemer $
 * $Date: 2012-11-20 22:07:11 +0100 (Tue, 20 Nov 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/we.inc.php");
if(!(isset($_REQUEST['we_dialog_args']) && isset($_REQUEST['we_dialog_args']['outsideWE']) && $_REQUEST['we_dialog_args']['outsideWE'] == 1)){
	we_html_tools::protect();
}

$dialog = new weImageDialog();
$dialog->initByHttp();
$dialog->registerCmdFn("weDoImgCmd");
//$yuiSuggest =& weSuggest::getInstance();
print $dialog->getHTML();

function weDoImgCmd($args){
	if($args["thumbnail"] && $args["fileID"]){
		$thumbObj = new we_thumbnail();
		$thumbObj->initByImageIDAndThumbID($args["fileID"], $args["thumbnail"]);
		if(!file_exists($thumbObj->getOutputPath(true))){
			$thumbObj->createThumb();
		}
	}

	if(!(isset($_REQUEST['we_dialog_args']['editor']) && $_REQUEST['we_dialog_args']['editor'] == "tinyMce")){

		return we_html_element::jsElement('top.opener.weWysiwygObject_' . $args["editname"] . '.insertImage("' . $args["src"] . '","' . $args["width"] . '","' . $args["height"] . '","' . $args["hspace"] . '","' . $args["vspace"] . '","' . $args["border"] . '","' . addslashes($args["alt"]) . '","' . $args["align"] . '","' . $args["name"] . '","' . $args["class"] . '","' . addslashes($args["title"]) . '","' . $args["longdesc"] . '");
top.close();
');
	} else{
		return weDialog::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/weimage/js/image_insert.js') .
			'<form name="tiny_form">
					<input type="hidden" name="src" value="' . $args["src"] . '">
					<input type="hidden" name="width" value="' . $args["width"] . '">
					<input type="hidden" name="height" value="' . $args["height"] . '">
					<input type="hidden" name="hspace" value="' . $args["hspace"] . '">
					<input type="hidden" name="vspace" value="' . $args["vspace"] . '">
					<input type="hidden" name="border" value="' . $args["border"] . '">
					<input type="hidden" name="alt" value="' . addslashes($args["alt"]) . '">
					<input type="hidden" name="align" value="' . $args["align"] . '">
					<input type="hidden" name="name" value="' . $args["name"] . '">
					<input type="hidden" name="class" value="' . $args["class"] . '">
					<input type="hidden" name="title" value="' . addslashes($args["title"]) . '">
					<input type="hidden" name="longdesc" value="' . (intval($args["longdescid"]) ? $args["longdescsrc"] . '?id=' . intval($args["longdescid"]) : '') . '">
				</form>';
	}
}
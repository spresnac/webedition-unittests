<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
//make sure we know which browser is used
if(!(isset($_REQUEST['we_dialog_args']) && isset($_REQUEST['we_dialog_args']['outsideWE']) && $_REQUEST['we_dialog_args']['outsideWE']==1) ){
	we_html_tools::protect();
}
$dialog = new weSpecialCharDialog();
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoRuleJS");
print $dialog->getHTML();

function weDoRuleJS(){
	return '
eval("var editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
var ch = document.we_form.elements["we_dialog_args[char]"].value;
var isSafari = ' . (we_base_browserDetect::isSafari() ? 'true' : 'false') . ';

if (isSafari) {
	ch = ch.replace(/^&/,"_xx_WE_AMP_xx_");
}

editorObj.replaceText(ch);

if (isSafari) {
	editorObj.editContainer.innerHTML = editorObj.editContainer.innerHTML.replace(/_xx_WE_AMP_xx_/,"&");
}

top.close();
';
}
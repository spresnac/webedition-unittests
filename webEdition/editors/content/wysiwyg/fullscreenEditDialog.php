<?php

/**
 * webEdition CMS
 *
 * $Rev: 4083 $
 * $Author: mokraemer $
 * $Date: 2012-02-19 04:30:27 +0100 (Sun, 19 Feb 2012) $
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

$dialog = new weFullscreenEditDialog();
$dialog->initByHttp();
$dialog->registerOkJsFN("weDoFullscreenJS");
print $dialog->getHTML();

function weDoFullscreenJS(){
	return '
if(weWysiwygSetHiddenText){
	weWysiwygSetHiddenText();
}

eval("editorObj = top.opener.weWysiwygObject_"+document.we_form.elements["we_dialog_args[editname]"].value);
var src = document.we_form.elements["we_dialog_args[src]"].value;
editorObj.setText(src);
top.close();
';
}

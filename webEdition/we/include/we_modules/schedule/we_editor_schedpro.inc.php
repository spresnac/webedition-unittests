<?php
/**
 * webEdition CMS
 *
 * $Rev: 5489 $
 * $Author: mokraemer $
 * $Date: 2012-12-31 18:31:25 +0100 (Mon, 31 Dec 2012) $
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
if(defined("SCHEDULE_TABLE")){
	we_schedpro::trigger_schedule();
}

we_html_tools::htmlTop();

include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print STYLESHEET .
	we_html_element::jsScript(JS_DIR . 'windows.js');
?>
</head>
<body  class="weEditorBody" onload="checkFooter()" onunload="doUnload()">
	<form name="we_form" onsubmit="return false"><?php
echo we_class::hiddenTrans();

$parts = array();

foreach($we_doc->schedArr as $i => $sched){
	$schedObj = new we_schedpro($sched, $i);

	$ofT = defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : '';

	$parts[] = array(
		'headline' => '',
		'html' => $schedObj->getHTML($GLOBALS['we_doc']->Table == $ofT),
		'space' => 0
	);
}
$parts[] = array(
	'headline' => '',
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_schedule', '[descriptiontext]'), 2, 700) . '<br><br>' . we_button::create_button('image:btn_add_schedule', "javascript:we_cmd('add_schedule')"),
	'space' => 0
);
print we_multiIconBox::getJS() .
	we_schedpro::getMainJS($we_doc) .
	we_multiIconBox::getHTML('', '100%', $parts, 20, '', -1, '', '', false);
?>
	</form>
</body>
</html>
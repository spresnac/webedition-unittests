<?php
/**
 * webEdition CMS
 *
 * $Rev: 4397 $
 * $Author: mokraemer $
 * $Date: 2012-04-09 00:06:42 +0200 (Mon, 09 Apr 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();


$_parts = array();
if(we_hasPerm("BACKUPLOG")){
	$_parts[] = array(
		'headline' => g_l('backup', "[view_log]"),
		'html' => '',
		'space' => 10
	);
	if(!file_exists($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'data/lastlog.php')){
		$_parts[] = array(
			'headline' => '',
			'html' => '<p>' . g_l('backup', "[view_log_not_found]") . '</p>',
			'space' => 10
		);
	} else{
		$log = file_get_contents($_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'data/lastlog.php');
		$_parts[] = array(
			'headline' => '',
			'html' => '<pre>' . $log . '</pre>',
			'space' => 10
		);
	}
} else{
	$_parts[] = array(
		'headline' => '',
		'html' => '<p>' . g_l('backup', "[view_log_no_perm]") . '</p>',
		'space' => 10
	);
}
echo we_html_tools::htmlTop(g_l('backup', "[view_log]")) .
 we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsElement('
	function closeOnEscape() {
		return true;
	}
') .
 STYLESHEET;
?>

</head>

<body class="weDialogBody" style="overflow:hidden;" onLoad="self.focus();">
	<div id="info" style="display: block;">
		<?php
		$buttons = we_button::position_yes_no_cancel(
				we_button::create_button("close", "javascript:self.close()"), '', ''
		);

		print we_multiIconBox::getJS();
		print we_multiIconBox::getHTML('', 500, $_parts, 30, $buttons, -1, '', '', false, "", "", 620, "auto");
		?>
	</div>

</body>
</html>
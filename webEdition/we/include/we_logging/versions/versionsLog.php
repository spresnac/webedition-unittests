<?php
/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Tue, 06 Nov 2012) $
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

$versionsLogView = new versionsLogView();
$out = $versionsLogView->printContent();

we_html_tools::htmlTop(g_l('versions', '[versions_log]'));

print STYLESHEET;

echo we_html_element::jsScript(JS_DIR . 'windows.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js');

print $versionsLogView->getJS();

$closeButton = we_button::create_button("close", "javascript:window.close();");
?>
<style type="text/css">

	#headlineDiv {
		height				: 40px;
	}
	#headlineDiv div {
		padding				: 10px 0 0 15px;
	}

	#versionsDiv {
		background			: #fff;
		overflow			: auto;
		height				: 420px ! important;
		margin				: 0px ! important;
	}

	.dialogButtonDiv {
		left				: 0;
		height				: 40px;
		background-image	: url(<?php echo IMAGE_DIR; ?>edit/editfooterback.gif);
		position			: absolute;
		bottom				: 0;
		width				: 100%;
	}


</style>

</head>

<body class="weDialogBody">

	<div id="headlineDiv">
		<div class="weDialogHeadline">
			<?php print g_l('versions', '[versions_log]') ?>
		</div>
	</div>
	<div id="versionsDiv">
		<?php
		print $out;
		?>

	</div>
	<div class="dialogButtonDiv">
		<div style="position:absolute;top:10px;right:20px;">
			<?php print $closeButton; ?>
		</div>
	</div>
</body>
</html>
<?php
/**
 * webEdition CMS
 *
 * $Rev: 5906 $
 * $Author: lukasimhof $
 * $Date: 2013-03-01 14:08:18 +0100 (Fri, 01 Mar 2013) $
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
$defaultCharset = "ISO-8859-1";

$_charsetHandler = new charsetHandler();
$_charsets = array();
$whiteList = array();
$_charsets = $_charsetHandler->charsets;

if(!empty($_charsets) && is_array($_charsets)){
	foreach($_charsets as $k => $v){
		if(isset($v['charset']) && $v['charset'] != ''){
			$whiteList[] = strtolower($v['charset']);
		}
	}
}

if(isset($_REQUEST['we_cmd'][15])){
	if($_REQUEST['we_cmd'][15] == ''){
		$_REQUEST['we_cmd'][15] = $defaultCharset;
	} else{
		if(!in_array(strtolower($_REQUEST['we_cmd'][15]), $whiteList)){
			exit();
		}
	}
}


@we_html_tools::headerCtCharset('text/html', ($_REQUEST['we_cmd'][15] ? $_REQUEST['we_cmd'][15] : $defaultCharset));

we_html_tools::protect();

$we_dt = isset($_SESSION['weS']['we_data'][$_REQUEST['we_cmd'][4]]) ? $_SESSION['weS']['we_data'][$_REQUEST['we_cmd'][4]] : '';
include (WE_INCLUDES_PATH . "we_editors/we_init_doc.inc.php");

if(preg_match('%^.+_te?xt\[.+\]$%i', $_REQUEST['we_cmd'][1])){
	$fieldName = preg_replace('/^.+_te?xt\[(.+)\]$/', '\1', $_REQUEST['we_cmd'][1]);
} else
if(preg_match('|^.+_input\[.+\]$|i', $_REQUEST['we_cmd'][1])){
	$fieldName = preg_replace('/^.+_input\[(.+)\]$/', '\1', $_REQUEST['we_cmd'][1]);
}

we_html_tools::htmlTop(sprintf("sali", $fieldName), ($_REQUEST['we_cmd'][15] ? $_REQUEST['we_cmd'][15] : $defaultCharset));

if(isset($fieldName) && isset($_REQUEST["we_okpressed"]) && $_REQUEST["we_okpressed"]){
	if(preg_match('%^(.+_te?xt)\[.+\]$%i', $_REQUEST['we_cmd'][1])){
		$reqName = preg_replace('/^(.+_te?xt)\[.+\]$/', '\1', $_REQUEST['we_cmd'][1]);
	} else if(preg_match('|^(.+_input)\[.+\]$|i', $_REQUEST['we_cmd'][1])){
		$reqName = preg_replace('/^(.+_input)\[.+\]$/', '\1', $_REQUEST['we_cmd'][1]);
	}
	$we_doc->setElement($fieldName, $_REQUEST[$reqName][$fieldName], "input");
	$we_doc->saveInSession($_SESSION['weS']['we_data'][$_REQUEST['we_cmd'][4]]);
	$newHTML = $we_doc->getField(array("name" => $fieldName));
	echo we_html_element::jsElement(
		'if(top.opener && top.opener.top.weEditorFrameController.getVisibleEditorFrame()){
			if(top.opener.top.weEditorFrameController.getVisibleEditorFrame().document.getElementById("div_wysiwyg_' . $_REQUEST['we_cmd'][1] . '")){
				top.opener.top.weEditorFrameController.getVisibleEditorFrame().document.getElementById("div_wysiwyg_' . $_REQUEST['we_cmd'][1] . '").innerHTML = "' .
		preg_replace(
			'|script|i', 'scr"+"ipt', str_replace("\"", "\\\"", str_replace("\r", "\\r", str_replace("\n", "\\n", $newHTML)))) . '";
			}

			//top.opener.we_cmd("reload_editpage");
		}
		top.close();');
	?>

	</head>
	<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
		<?php
	} else{
		$cancelBut = we_button::create_button('cancel', "javascript:top.close()");
		$okBut = we_button::create_button('ok', "javascript:weWysiwygSetHiddenText();document.we_form.submit();");

		print STYLESHEET;
		echo we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsElement('top.focus();');
		?>
	</head>
	<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" background="<?php print IMAGE_DIR . 'backgrounds/aquaBackground.gif'; ?>">
		<form action="<?php print $_SERVER['SCRIPT_NAME']; ?>" name="we_form" method="post">
			<input type="hidden" name="we_okpressed" value="1" />
			<?php
			foreach($_REQUEST['we_cmd'] as $i => $v){
				print '<input type="hidden" name="we_cmd[' . $i . ']" value="' . $_REQUEST['we_cmd'][$i] . '" />' . "\n";
			}

			/*
			  1 = name
			  2 = width
			  3 = height
			  4 = transaction
			  5 = propstring
			  6 = classname
			  7 = fontnames
			  8 = outsidewe
			  9 = tbwidth (toolbar width)
			  10 = tbheight
			  11 = xml
			  12 = remove first paragraph
			  13 = bgcolor
			  14 = baseHref
			  15 = charset
			  16 = cssClasses
			  17 = Language
			  18 = documentCss
			  19 = origName
			  20 = tinyParams
			 */

			$e = new we_wysiwyg(
					$_REQUEST['we_cmd'][1],
					$_REQUEST['we_cmd'][2],
					$_REQUEST['we_cmd'][3],
					$we_doc->getElement($fieldName),
					$_REQUEST['we_cmd'][5],
					$_REQUEST['we_cmd'][13],
					'',
					$_REQUEST['we_cmd'][6],
					$_REQUEST['we_cmd'][7],
					$_REQUEST['we_cmd'][8],
					$_REQUEST['we_cmd'][11],
					$_REQUEST['we_cmd'][12],
					true,
					$_REQUEST['we_cmd'][14],
					$_REQUEST['we_cmd'][15],
					$_REQUEST['we_cmd'][16],
					$_REQUEST['we_cmd'][17],
					'',
					true,
					false,
					'top',
					true,
					we_cmd_dec(18),
					we_cmd_dec(19),
					we_cmd_dec(20));

			print we_wysiwyg::getHeaderHTML() . $e->getHTML() .
				'<div style="height:8px"></div>' . we_button::position_yes_no_cancel($okBut, $cancelBut);
			?>
		</form>
		<?php
	}
	?>
</body>
</html>

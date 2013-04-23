<?php
/**
 * webEdition CMS
 *
 * $Rev: 3953 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 19:12:45 +0100 (Tue, 07 Feb 2012) $
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
we_html_tools::protect();
we_html_tools::htmlTop(g_l('global', '[select_color]'));

print STYLESHEET;

echo we_html_element::jsScript(JS_DIR . 'we_colors2.js');
?>
<script  type="text/javascript">

	function selectColor(c){
		document.we_form.colorvalue.value = c;
	}
	function setColor(){
<?php if($_REQUEST['we_cmd'][0]){ ?>
			opener.document.we_form.elements["<?php print $_REQUEST['we_cmd'][1]; ?>"].value = document.we_form.colorvalue.value;

	<?php if(isset($_REQUEST['we_cmd'][3]) && $_REQUEST['we_cmd'][3]){ ?>

		<?php print $_REQUEST['we_cmd'][3]; ?>

	<?php } else{ ?>

					opener._EditorFrame.setEditorIsHot(true);
					opener.we_cmd("reload_editpage");

	<?php }
} else{ ?>
				window.returnValue = document.we_form.colorvalue.value;
<?php } ?>
		window.close();
	}
	function init(){
		top.focus();
<?php if($_REQUEST['we_cmd'][0]){ ?>
			document.we_form.colorvalue.value = "<?php print $_REQUEST['we_cmd'][2]; ?>";
<?php } else{ ?>
			document.we_form.colorvalue.value = window.dialogArguments["bgcolor"];
<?php } ?>
	}
</script>
</head>


<body class="weDialogBody"<?php if($_REQUEST['we_cmd'][0]){ ?> onLoad="init()"<?php } ?>>
	<form name="we_form" onSubmit="<?php if(!$_REQUEST['we_cmd'][0]){ ?>setColor();<?php } ?>return false">
<?php
$colortable = '<table border="1" bordercolor="SILVER" bordercolorlight="WHITE" bordercolordark="BLACK" cellspacing="0" cellpadding="0">
<script  type="text/javascript">
var z=0;
for ( col in we_color2 ){
	if(z == 0){
		document.writeln(\'<tr>\');
	}

document.writeln(\'<td bgcolor="\'+col+\'"><a href="#" onClick="selectColor(\\\'\'+col+\'\\\');"><span style="display:inline-block;width:15px;height:15px" title="\'+we_color2[col]+\'" ></span></a></td>\');

if(z==17){
		document.writeln(\'</tr>\');
		z = 0;
	}else{
		z++;
	}
}
if(z != 0){
	for(var i=z;i<18;i++){
		document.writeln(\'<td></td>\');
	}
	document.writeln(\'</tr>\');
}
</script>
		</table>
	';

$foo = '<input type="text" size="20" name="colorvalue" class="defaultfont" style="width:150px" />';
$color = we_html_tools::htmlFormElementTable($foo, g_l('wysiwyg', "[color]"));

if($_REQUEST['we_cmd'][0]){
	$buttons =
		we_button::position_yes_no_cancel(we_button::create_button("ok", "javascript:setColor();"), "", we_button::create_button("cancel", "javascript:window.close()")
	);
} else{
	$buttons =
		we_button::position_yes_no_cancel(we_button::create_button("ok", "form:submit:we_form"), "", we_button::create_button("cancel", "javascript:window.close()")
	);
}
$table = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . $colortable . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(2, 10) . '</td>
	</tr>
	<tr>
		<td>' . $color . '</td>
	</tr>
</table>
';

print we_html_tools::htmlDialogLayout($table, g_l('global', '[select_color]'), $buttons);
?>
	</form>
</body>

</html>
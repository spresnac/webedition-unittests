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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//
//	---> Includes
//

include_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

we_html_tools::protect();

//
//	---> Setting the Content-Type
//

if(isset($we_doc->elements["Charset"]["dat"]) && $we_doc->elements["Charset"]["dat"]){ //	send charset which might be determined in template
	$charset = $we_doc->elements["Charset"]["dat"];
} else{
	$charset = DEFAULT_CHARSET;
}
we_html_tools::headerCtCharset('text/html', $charset);

//
//	---> initialize some vars
//

$jsGUI = new weOrderContainer("_EditorFrame.getContentEditor()", "classEntry");
$parts = array();


//
//	---> Output the HTML Header
//

we_html_tools::htmlTop('', $charset, 5);

//
//	---> Loading the Stylesheets
//

if($we_doc->CSS){
	$cssArr = makeArrayFromCSV($we_doc->CSS);
	foreach($cssArr as $cs){
		$path = id_to_path($cs);
		if($path){
			print we_html_element::cssLink($path);
		}
	}
}
print STYLESHEET;


//
//	---> Loading some Javascript
//

echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script  type="text/javascript">
	<!--
	function we_checkObjFieldname(i){
		if(i.value.search(/^([a-zA-Z0-9_+-])*$/)){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_object', '[fieldNameNotValid]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			i.focus();
			i.select();
			i.value = i.getAttribute("oldValue");
		}else if(i.value=='Title' || i.value=='Description'){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_object', '[fieldNameNotTitleDesc]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			i.focus();
			i.select();
			i.value = i.getAttribute("oldValue");
		}else if(i.value.length==0){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_object', '[fieldNameEmpty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			//		i.focus(); # 1052
			//		i.select();
			i.value = i.getAttribute("oldValue");
		} else {
			i.setAttribute("oldValue", i.value);
		}
	}
	//-->
</script>
<?php
echo $jsGUI->getJS(WEBEDITION_DIR . "js");
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
?>

</head>

<body onUnload="doUnload()" class="weEditorBody">
	<form name="we_form" method="post"><?php
echo we_class::hiddenTrans();

if($we_doc->ID){
	$ctable = OBJECT_X_TABLE . $we_doc->ID;
	$tableInfo = $DB_WE->metadata($ctable);
}

$sort = $we_doc->getElement("we_sort");
$count = $we_doc->getElement("Sortgesamt");

$uniquename = md5(uniqid(__FILE__, true));
$width = 800;

$we_transaction = (preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0);

echo we_multiIconBox::_getBoxStart("100%", $uniquename) .
 $jsGUI->getContainer(array()) .
 '<div id="' . $uniquename . '_div">
 <table style="margin-left:30px;" cellpadding="0" cellspacing="0" border="0">
 <tr>
 <td valign="top"></td>
 <td class="defaultfont">' .
 we_button::create_button("image:btn_add_field", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_class','" . $_REQUEST['we_transaction'] . "');") .
 '</td>
 </tr>
 <tr>
 <td>' . we_html_tools::getPixel(0, 15) . '</td>
 <td></td>
 </tr>
 </table>
 </div>';

echo we_multiIconBox::_getBoxEnd("100%");

for($i = 0; $i <= $count && !empty($sort); $i++){
	$identifier = $we_doc->getSortIndex($i);
	$uniqid = "entry_" . $identifier;

	$upbut = we_button::create_button("image:btn_direction_up", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('up_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier);
	$downbut = we_button::create_button("image:btn_direction_down", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('down_entry_at_class','" . $we_transaction . "','" . $uniqid . "');", true, 22, 22, "", "", false, false, "_" . $identifier);
	$plusbut = we_button::create_button("image:btn_add_field", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_class','" . $we_transaction . "','" . $uniqid . "');");
	$trashbut = we_button::create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('delete_entry_at_class','" . $we_transaction . "','" . $uniqid . "');");

	echo '
<div id="' . $uniqid . '">
<a name="f' . $uniqid . '"></a>
<table style="margin-left:30px;" cellpadding="0" cellspacing="0" border="0">
<tr>
<td class="defaultfont" width="600">
<table cellpadding="6" cellspacing="0" border="0">' .
	$we_doc->getFieldHTML($we_doc->getElement("wholename" . $identifier), $uniqid) .
	'</table>
		</td>
		<td width="150" class = "defaultfont" valign="top">' .
	we_button::create_button_table(array($plusbut, $upbut, $downbut, $trashbut), 5) .
	'</td>
		</tr>
		</table>
		<div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;">' . we_html_tools::getPixel(1, 1) . '</div>' . we_html_tools::getPixel(2, 10) .
	'</div>' .
	we_html_element::jsElement('classEntry.add(document, \'' . $uniqid . '\', null);') .
	$jsGUI->getDisableButtonJS();
}
?>
	</form>
</body>

</html>
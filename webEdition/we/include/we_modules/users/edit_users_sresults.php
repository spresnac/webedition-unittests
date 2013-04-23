<?php
/**
 * webEdition CMS
 *
 * $Rev: 4746 $
 * $Author: mokraemer $
 * $Date: 2012-07-22 23:49:07 +0200 (Sun, 22 Jul 2012) $
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
we_html_tools::htmlTop(g_l('modules_users', "[search_result]"), $GLOBALS['WE_BACKENDCHARSET']);
print STYLESHEET;

$_kwd = isset($_REQUEST["kwd"]) ? $_REQUEST["kwd"] : "";
$arr = explode(" ", strToLower($_kwd));
$sWhere = "";
$ranking = "0";

$first = "";
$array_and = array();
$array_or = array();
$array_not = array();
$array_and[0] = $arr[0];

for($i = 1; $i < count($arr); $i++){
	switch($arr[$i]){
		case 'not':
			$i++;
			$array_not[count($array_not)] = $arr[$i];
			break;
		case 'and':
			$i++;
			$array_and[count($array_and)] = $arr[$i];
			break;
		case 'or':
			$i++;
			$array_or[count($array_or)] = $arr[$i];
			break;
		default:
			$array_and[count($array_and)] = $arr[$i];
			break;
	}
}
$condition = "";
foreach($array_and as $k => $value){
	$value = $DB_WE->escape($value);
	if($condition != "")
		$condition.=" AND (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')";
	else
		$condition.=" (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')";
}
foreach($array_or as $k => $value){
	$value = $DB_WE->escape($value);
	if($condition != "")
		$condition.=" OR (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')";
	else
		$condition.=" (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')";
}
foreach($array_not as $k => $value){
	$value = $DB_WE->escape($value);
	if($condition != "")
		$condition.=" AND NOT (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')";
	else
		$condition.=" (First LIKE '%$value%' OR Second LIKE '%$value%' OR username LIKE '%$value%' OR Address LIKE '%$value%' OR City LIKE '%$value%' OR State LIKE '%$value%' OR Country LIKE '%$value%' OR Tel_preselection LIKE '%$value%' OR Fax_preselection LIKE '%$value%' OR Telephone LIKE '%$value%' OR Fax LIKE '%$value%' OR Description LIKE '%$value%')";
}

if($condition != ""){
	$condition = ' WHERE ' . $condition . ' ORDER BY Text';
}
$DB_WE->query("SELECT ID,Text FROM " . USER_TABLE . $condition);

$_select = '<div style="background-color:white;width:520px;height:220px;"/>';
if($DB_WE->num_rows()){
	$_select = '<select name="search_results" size="20" style="width:520px;height:220px;" ondblclick="opener.top.content.we_cmd(\'check_user_display\',document.we_form.search_results.value)">';
	while($DB_WE->next_record()) {
		$_select.='<option value="' . $DB_WE->f("ID") . '">' . $DB_WE->f("Text") . '</option>';
	}
	$_select.='</select>';
}

$_buttons = we_button::position_yes_no_cancel(
		we_button::create_button("edit", "javascript:opener.top.content.we_cmd('check_user_display',document.we_form.search_results.value)"), null, we_button::create_button("cancel", "javascript:self.close();")
);



$_content = we_html_tools::htmlFormElementTable(
		we_html_tools::htmlTextInput('kwd', 24, $_kwd, "", "", "text", 485), g_l('modules_users', "[search_for]"), "left", "defaultfont", we_html_tools::getPixel(10, 1), we_button::create_button("image:btn_function_search", "javascript:document.we_form.submit();")
	) . '<div style="height:20px;"></div>' .
	we_html_tools::htmlFormElementTable(
		$_select, g_l('modules_users', "[search_result]")
);
?>
</head>
<body class="weEditorBody" style="margin:10px 20px;">
	<form name="we_form" method="post">
		<?php print we_html_tools::htmlDialogLayout($_content, g_l('modules_users', "[search]"), $_buttons); ?>
	</form>
</body>

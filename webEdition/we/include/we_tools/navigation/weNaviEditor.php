<?php

/**
 * webEdition CMS
 *
 * $Rev: 5828 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 14:17:22 +0100 (Sun, 17 Feb 2013) $
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


$_margin_top = 5;
$_space_size = 100;
$_input_size = 440;

$_path = isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : '';

$_id = (!empty($_path)) ? path_to_id($_path, NAVIGATION_TABLE) : 0;

$_cmd = 'opener.we_cmd("add_navi",' . $_id . ',encodeURIComponent(document.we_form.Text.value),dir.options[dir.selectedIndex].value,document.we_form.Ordn.value);';

$_navi = new weNavigation($_id);

$_wrkNavi = array();
$_db = new DB_WE();

if(!we_hasPerm('ADMINISTRATOR')){
	if(($_ws = get_ws(NAVIGATION_TABLE))){ // #5836: Use function get_ws()
		$_wrkNavi = makeArrayFromCSV($_ws);
	}
	$_condition = array();
	foreach($_wrkNavi as $_value){
		$_condition[] = 'Path LIKE "' . $_db->escape(id_to_path($_value, NAVIGATION_TABLE)) . '/%"';
	}
	$_dirs = array();
	$_def = null;
} else{
	$_dirs = array(
		'0' => '/'
	);
	$_def = 0;
}

if($_id){
	$_def = $_navi->ParentID;
}

$_db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE IsFolder=1 ' . (!empty($_wrkNavi) ? ' AND (ID IN (' . implode(',', $_wrkNavi) . ') OR (' . implode(' OR ', $_condition) . '))' : '') . ' ORDER BY Path;');
while($_db->next_record()) {
	if($_def === null){
		$_def = $_db->f('ID');
	}
	$_dirs[$_db->f('ID')] = $_db->f('Path');
}

$_parts = array(
	array(
		'headline' => g_l('navigation', '[name]'),
		'html' => we_html_tools::htmlTextInput(
			'Text', 24, $_navi->Text, '', 'style="width: ' . $_input_size . 'px;" onblur="if(document.we_form.Text.value!=\'\') switch_button_state(\'save\', \'save_enabled\', \'enabled\'); else switch_button_state(\'save\', \'save_disabled\', \'disabled\');" onkeyup="if(document.we_form.Text.value!=\'\') switch_button_state(\'save\', \'save_enabled\', \'enabled\'); else switch_button_state(\'save\', \'save_disabled\', \'disabled\');"'),
		'space' => $_space_size,
		'noline' => 1
	),
	array(
		'headline' => g_l('navigation', '[group]'),
		'html' => we_html_tools::htmlSelect(
			'ParentID', $_dirs, 1, $_navi->ParentID, false, (we_base_browserDetect::isIE() ? '' : 'style="width: ' . $_input_size . 'px;" ') . 'onChange="queryEntries(this.value)"'),
		'space' => $_space_size,
		'noline' => 1
	),
	array(
		'headline' => '',
		'html' => '<div id="details" class="blockWrapper" style="width: ' . $_input_size . 'px;height: 100px;"></div>',
		'space' => $_space_size,
		'noline' => 1
	),
	array(
		'headline' => g_l('navigation', '[order]'),
		'html' => we_html_tools::hidden('Ordn', $_navi->Ordn) . we_html_tools::htmlTextInput(
			'OrdnTxt', 8, ($_navi->Ordn + 1), '', 'onchange="document.we_form.Ordn.value=(document.we_form.OrdnTxt.value-1);"', 'text', 117) . we_html_tools::getPixel(6, 5) . we_html_tools::htmlSelect(
			'OrdnSelect', array(
			'begin' => g_l('navigation', '[begin]'), 'end' => g_l('navigation', '[end]')
			), 1, '', false, 'onchange="document.we_form.OrdnTxt.value=document.we_form.OrdnSelect.options[document.we_form.OrdnSelect.selectedIndex].text;document.we_form.Ordn.value=this.value;"', "value", 317),
		'space' => $_space_size,
		'noline' => 1
	)
);

$_js = we_button::create_state_changer(false) . '
function save() {
	var dir = document.we_form.ParentID;
	' . $_cmd . '
	self.close();

}

var ajaxObj = {
		handleSuccess:function(o){
				this.processResult(o);
				if(o["responseText"]) {
					document.getElementById("details").innerHTML = "";
					eval(o["responseText"]);

					var items = weResponse.data.split(",");
					var i = 0;

					for(s in items) {
						i++;
						var row = items[s].split(":");
						if(row.length>1) {
							document.getElementById("details").innerHTML += "<div style=\"width: 40px; float: left;\">"+i+"</div><div style=\"width: 220px;\">"+row[1]+"</div>";
						}
					}
				}
		},

		handleFailure:function(o){
				// Failure handler
		},

		processResult:function(o){
				// This member is called by handleSuccess
		},

		startRequest:function(id) {
			 YAHOO.util.Connect.asyncRequest("POST", "' . WEBEDITION_DIR . 'rpc/rpc.php", callback, "cmd=GetNaviItems&nid="+id);
		}

};


var callback = {
		success:ajaxObj.handleSuccess,
		failure:ajaxObj.handleFailure,
		scope: ajaxObj
};


function queryEntries(id) {
	ajaxObj.startRequest(id);
}';
$buttonsBottom = '<div style="float:right">' . we_button::position_yes_no_cancel(
		we_button::create_button('save', 'javascript:save();', true, 100, 22, '', '', ($_id ? false : true), false), null, we_button::create_button('close', 'javascript:self.close();')) . '</div>';

$_body = we_html_element::htmlBody(
		array(
		"class" => "weDialogBody", "onLoad" => "loaded=1;queryEntries(" . $_def . ")"
		), we_html_element::htmlForm(
			array(
			"name" => "we_form", "onsubmit" => "return false"
			), we_multiIconBox::getHTML(
				'', '100%', $_parts, 30, $buttonsBottom, -1, '', '', false, g_l('navigation', '[add_navigation]'), "", 311)));

$_head = //FIXME: missing title
	we_html_tools::getHtmlInnerHead() . STYLESHEET .
	we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
	we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
	we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js') .
	we_html_element::jsElement($_js);

print we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead($_head) . $_body);
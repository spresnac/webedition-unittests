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

/**
 * Class which contains all functions for the
 * rebuild dialog and the rebuild function
 * @static
 */
abstract class we_versions_wizard{

	/**
	 * returns HTML for the Body Frame
	 *
	 * @return string
	 */
	static function getBody(){
		$step = 'getStep' . (isset($_REQUEST["step"]) ? $_REQUEST["step"] : "0");
		return self::getPage(self::$step());
	}

	/**
	 * returns HTML for the Frame with the progress bar
	 *
	 * @return string
	 */
	static function getBusy(){
		$dc = isset($_REQUEST["dc"]) ? $_REQUEST["dc"] : 0;

		$WE_PB = new we_progressBar(0, 0, true);
		$WE_PB->setStudLen($dc ? 490 : 200);
		$WE_PB->addText("", 0, "pb1");
		$js = $WE_PB->getJSCode();
		$pb = $WE_PB->getHTML();

		$js .= we_html_element::jsElement('function showRefreshButton() {  prevBut = document.getElementById(\'prev\');  nextBut = document.getElementById(\'nextCell\');  refrBut = document.getElementById(\'refresh\');  prevBut.style.display = \'none\';  nextBut.style.display = \'none\';  refrBut.style.display = \'\';} function showPrevNextButton() {  prevBut = document.getElementById(\'prev\');  nextBut = document.getElementById(\'next\');  refrBut = document.getElementById(\'refresh\');  refrBut.style.display = \'none\';  prevBut.style.display = \'\';  nextBut.style.display = \'\';}');

		$cancelButton = we_button::create_button("cancel", "javascript:top.close();");
		$refreshButton = we_button::create_button("refresh", "javascript:parent.wizcmd.location.reload();", true, -1, -1, "", "", false, false);

		$nextbutdisabled = !(we_hasPerm("REBUILD_ALL") || we_hasPerm("REBUILD_FILTERD") || we_hasPerm(
				"REBUILD_OBJECTS") || we_hasPerm("REBUILD_INDEX") || we_hasPerm("REBUILD_THUMBS") || we_hasPerm(
				"REBUILD_META"));

		if($dc){
			$buttons = we_button::create_button_table(array(
					$refreshButton, $cancelButton
					), 10);
			$pb = we_html_tools::htmlDialogLayout($pb, g_l('rebuild', "[rebuild]"), $buttons);
		} else{
			$prevButton = we_button::create_button("back", "javascript:parent.wizbody.handle_event('previous');", true, -1, -1, "", "", true, false);
			$nextButton = we_button::create_button("next", "javascript:parent.wizbody.handle_event('next');", true, -1, -1, "", "", $nextbutdisabled, false);

			$content2 = new we_html_table(array(
					"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
					), 1, 4);
			$content2->setCol(
				0, 0, array(
				"id" => "prev",
				"style" => "display:table-cell; padding-left:10px;",
				"align" => "right"
				), $prevButton);
			$content2->setCol(
				0, 1, array(
				"id" => "nextCell",
				"style" => "display:table-cell; padding-left:10px;",
				"align" => "right"
				), $nextButton);
			$content2->setCol(
				0, 2, array(
				"id" => "refresh", "style" => "display:none; padding-left:10px;", "align" => "right"
				), $refreshButton);
			$content2->setCol(
				0, 3, array(
				"id" => "cancel",
				"style" => "display:table-cell; padding-left:10px;",
				"align" => "right"
				), $cancelButton);

			$content = new we_html_table(
					array(
						"border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "100%"
					),
					1,
					2);
			$content->setCol(0, 0, array(
				"id" => "progr", "style" => "display:none", "align" => "left"
				), $pb);
			$content->setCol(0, 1, array(
				"align" => "right"
				), $content2->getHtml());
		}

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					STYLESHEET . ($dc ? "" : we_button::create_state_changer()) . $js) . we_html_element::htmlBody(
					array(
					"class" => ($dc ? "weDialogBody" : "weDialogButtonsBody")
					), ($dc ? $pb : $content->getHtml())));
	}

	/**
	 * returns HTML for the Cmd Frame
	 *
	 * @return string for now it is an empty page
	 */
	static function getCmd(){
		return self::getPage(array('', ''));
	}

	/**
	 * returns the HTML for the First Step (0) of the wizard
	 *
	 * @return string
	 */
	static function getStep0(){

		$version = new weVersions();

		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "delete_versions";

		$version_delete = array();
		$version_reset = array();

		foreach($version->contentTypes as $k){
			$version_delete[$k] = isset($_REQUEST["version_delete_" . $k]) ? 1 : 0;
			$version_reset[$k] = isset($_REQUEST["version_reset_" . $k]) ? 1 : 0;
		}

		$version_delete['delete_date'] = isset($_REQUEST["delete_date"]) ? $_REQUEST["delete_date"] : "";
		$version_delete['delete_hours'] = isset($_REQUEST["delete_hours"]) ? $_REQUEST["delete_hours"] : 0;
		$version_delete['delete_minutes'] = isset($_REQUEST["delete_minutes"]) ? $_REQUEST["delete_minutes"] : 0;
		$version_delete['delete_seconds'] = isset($_REQUEST["delete_seconds"]) ? $_REQUEST["delete_seconds"] : 0;

		$version_reset['reset_date'] = isset($_REQUEST["reset_date"]) ? $_REQUEST["reset_date"] : "";
		$version_reset['reset_hours'] = isset($_REQUEST["reset_hours"]) ? $_REQUEST["reset_hours"] : 0;
		$version_reset['reset_minutes'] = isset($_REQUEST["reset_minutes"]) ? $_REQUEST["reset_minutes"] : 0;
		$version_reset['reset_seconds'] = isset($_REQUEST["reset_seconds"]) ? $_REQUEST["reset_seconds"] : 0;

		if(isset($_REQUEST["reset_doPublish"])){
			$version_reset['reset_doPublish'] = 1;
		} elseif(isset($_REQUEST["type"]) && $_REQUEST["type"] == "reset_versions"){
			$version_reset['reset_doPublish'] = 0;
		} else{
			$version_reset['reset_doPublish'] = 1;
		}

		$parts = array(
			array(
				"headline" => "",
				"html" => we_forms::radiobutton(
					"delete_versions", ($type == "delete_versions"), "type", g_l('versions', '[delete_versions]'), true, "defaultfont", "", false, g_l('versions', '[txt_delete_versions]'), 0, 495),
				"space" => 0
			),
			array(
				"headline" => "",
				"html" => we_forms::radiobutton(
					"reset_versions", ($type == "reset_versions"), "type", g_l('versions', '[reset_versions]'), true, "defaultfont", "", false, g_l('versions', '[txt_reset_versions]'), 0, 495),
				"space" => 0
			));

		$js = '
window.onload = function(){
	top.focus();
}
function handle_event(what){
	f = document.we_form;
	switch(what){
	case "previous":
		break;
	case "next":
		selectedValue="";
		for(var i=0;i<f.type.length;i++){
			if(f.type[i].checked){
				selectedValue = f.type[i].value;
			}
		}
		goTo(selectedValue);
		break;
	}
}
function goTo(where){
	f = document.we_form;
	switch(where){
	case "rebuild_thumbnails":
	case "delete_versions":
		f.target="wizbody";
		break;
	case "rebuild_objects":
	case "rebuild_index":
	case "rebuild_navigation":
		set_button_state(1);
		f.target="wizcmd";
		f.step.value="2";
		break;
	}
	f.submit();
}
function set_button_state(alldis) {
	if(top.frames["wizbusy"] && top.frames["wizbusy"].switch_button_state){
		top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "disabled");
		if(alldis){
			top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "disabled");
			top.frames["wizbusy"].showRefreshButton();
		}else{
			top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
		}
	}else{
		setTimeout("set_button_state("+(alldis ? 1 : 0)+")",300);
	}
}
set_button_state(false);';

		$hiddenFields = "";
		foreach($version_delete as $k => $v){
			$hiddenFields .= we_html_element::htmlHidden(array("name" => $k, "value" => $v));
		}

		foreach($version_reset as $k => $v){
			$hiddenFields .= we_html_element::htmlHidden(array("name" => $k, "value" => $v));
		}

		return array(
			$js,
			we_multiIconBox::getHTML("", "100%", $parts, 40, "", -1, "", "", false, g_l('versions', '[versioning]')) .
			$hiddenFields .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "versions_wizard")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "1"))
		);
	}

	/**
	 * returns the HTML for the Second Step (1) of the wizard
	 *
	 * @return string
	 */
	static function getStep1(){
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "delete_versions";

		switch($type){
			case "delete_versions" :
				return self::getDelete1();
			case "reset_versions" :
				return self::getReset1();
		}
	}

	static function getDelete1(){
		$version = new weVersions();
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "delete_versions";

		$versions_delete_all = isset($_REQUEST["version_delete_all"]) ? 1 : 0;
		$version_delete_date = isset($_REQUEST["delete_date"]) ? $_REQUEST["delete_date"] : "";
		$version_delete_hours = isset($_REQUEST["delete_hours"]) ? $_REQUEST["delete_hours"] : 0;
		$version_delete_minutes = isset($_REQUEST["delete_minutes"]) ? $_REQUEST["delete_minutes"] : 0;
		$version_delete_seconds = isset($_REQUEST["delete_seconds"]) ? $_REQUEST["delete_seconds"] : 0;


		$content = "";
		foreach($version->contentTypes as $k){
			$txt = $k;
			$name = "version_delete_" . $k;
			$val = "version_delete_" . $k;
			$checked = isset($_REQUEST[$k]) ? $_REQUEST[$k] : 0;
			if($k == "all"){
				$jvs = "checkAll(this);";
				$content .= we_forms::checkbox($val, $checked, $name, g_l('versions', '[versions_all]'), false, "defaultfont", $jvs) . "<br/>";
			} else{
				$jvs = "checkAllRevert(this);";
				$content .= we_forms::checkbox($val, $checked, $name, g_l('contentTypes', '[' . $txt . ']'), false, "defaultfont", $jvs) . "<br/>";
			}
		}
		$parts = array(
			array(
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[ct_delete_text]'), 2, 520),
				'noline' => 1,
				'space' => 0
			),
			array(
				'headline' => g_l('versions', '[ContentType]'),
				'space' => 170,
				'html' => $content,
				'noline' => 1
			)
		);

		$versions_delete_date = weVersionsSearch::getDateSelector("", "delete_date", "_1", $version_delete_date);

		$reset_hours = new we_html_select(
				array(
					"id" => "delete_hours",
					"name" => "delete_hours",
					"style" => "",
					"class" => "weSelect",
					"onChange" => ""
			));

		for($x = 0; $x <= 23; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_hours->addOption($x, $txt);
		}

		$reset_hours->selectOption($version_delete_hours);

		$reset_minutes = new we_html_select(
				array(
					"id" => "delete_minutes",
					"name" => "delete_minutes",
					"style" => "",
					"class" => "weSelect",
					"onChange" => ""
			));

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_minutes->addOption($x, $txt);
		}

		$reset_minutes->selectOption($version_delete_minutes);

		$reset_seconds = new we_html_select(
				array(
					"id" => "delete_seconds",
					"name" => "delete_seconds",
					"style" => "",
					"class" => "weSelect",
					"onChange" => ""
			));

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_seconds->addOption($x, $txt);
		}

		$reset_seconds->selectOption($version_delete_seconds);

		$parts[] = array(
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[date_delete_text]'), 2, 520),
			'noline' => 1,
			'space' => 0
		);

		$clearDate = we_button::create_button("reset", "javascript:document.getElementById('delete_date').value='';", true, -1, -1, "", "", "", false);

		$parts[] = array(
			'headline' => g_l('versions', '[time]'),
			'html' => "<div style='padding-bottom:3px;'>" . g_l('versions', '[day]') . ":</div><div style='float:left;'>" . $versions_delete_date . "</div><div style='float:left;margin: 0px 0px 10px 10px;'>" . $clearDate . "</div><br style='clear:left;' /><div style='padding-bottom:3px;'>" . g_l('versions', '[clocktime]') . ":</div>" . $reset_hours->getHtml() . " h : " . $reset_minutes->getHtml() . " m: " . $reset_seconds->getHtml() . " s",
			'noline' => 1,
			'space' => 170
		);

		//js
		$jsCheckboxCheckAll = '';
		$jsCheckboxCtIf = '';

		$jsCheckboxArgs = '';
		foreach($version->contentTypes as $k){
			if($k != "all"){
				$jsCheckboxCheckAll .= 'document.getElementById("version_delete_' . $k . '").checked = checked;';
			}
			$jsCheckboxCtIf .= (empty($jsCheckboxCtIf) ? '' : ' && ') . 'document.getElementById("version_delete_' . $k . '").checked==0';
			$jsCheckboxArgs .= 'args += "&ct[' . $k . ']="+escape(document.getElementById("version_delete_' . $k . '").checked);';
		}

		$nextButton = we_button::create_button("next", "javascript:parent.wizbody.handle_event(\"next\");", true, -1, -1, "", "", "", false);

		$js = '
window.onload = function(){
	top.focus();
}
function handle_event(what){
	f = document.we_form;
	switch(what){
		case "previous":
			f.step.value=0
			f.target="wizbody";
			f.submit();
			break;
		case "next":
			var date = document.getElementById("delete_date").value;
			var hour = document.getElementById("delete_hours").value;
			var minutes = document.getElementById("delete_minutes").value;
			var seconds = document.getElementById("delete_seconds").value;
			if(' . $jsCheckboxCtIf . ') {
				' . we_message_reporting::getShowMessageCall(
				g_l('versions', '[notCheckedContentType]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
			}else {
				selectedValue="";
				for(var i=0;i<f.type.length;i++){
					if(f.type[i].checked){;
						selectedValue = f.type[i].value;
					}
				}
				goTo(selectedValue);
			}
		break;
	}
}

function checkAll(val) {
	if(val.checked) {
		checked = 1;
	}else {
		checked = 0;
	}' . $jsCheckboxCheckAll . ';

}

function checkAllRevert() {
	var checkbox = document.getElementById("version_delete_all");
	checkbox.checked = false;
}

function calendarSetup(){
	if(document.getElementById("date_picker_1") != null) {
		Calendar.setup({inputField:"delete_date",ifFormat:"%d.%m.%Y",button:"date_picker_1",align:"Tl",singleClick:true});
	}
}

function goTo(where){
	f = document.we_form;
	switch(where){
		case "delete_versions":
			f.target="wizbody";
			break;
	}
	f.submit();
}



function set_button_state(alldis) {
	if(top.frames["wizbusy"] && top.frames["wizbusy"].switch_button_state){
		top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "enabled");
		if(alldis){
			top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
			top.frames["wizbusy"].showRefreshButton();
		}else{
			top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
			var nextBut = top.frames["wizbusy"].document.getElementById(\'nextCell\');
				nextBut.innerHTML = \'' . $nextButton . '\';
		}
	}else{
		setTimeout("set_button_state("+(alldis ? 1 : 0)+")",300);
	}
}
set_button_state(false);';

		$calendar = we_html_element::jsElement("calendarSetup();");

		$parts[] = array(
			'html' => $calendar, 'noline' => 0, 'space' => 0
		);

		return array(
			$js,
			we_multiIconBox::getHTML(
				"", "100%", $parts, 40, "", -1, "", "", false, g_l('versions', '[delete_versions]') . " - " . g_l('versions', '[step]') . " 1 " . g_l('versions', '[of]') . " 2") .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "versions_wizard")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "2"))
		);
	}

	static function getReset1(){
		$version = new weVersions();
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "reset_versions";

		$versions_reset_all = isset($_REQUEST["version_reset_all"]) ? 1 : 0;
		$version_reset_date = isset($_REQUEST["reset_date"]) ? $_REQUEST["reset_date"] : "";
		$version_reset_hours = isset($_REQUEST["reset_hours"]) ? $_REQUEST["reset_hours"] : 0;
		$version_reset_minutes = isset($_REQUEST["reset_minutes"]) ? $_REQUEST["reset_minutes"] : 0;
		$version_reset_seconds = isset($_REQUEST["reset_seconds"]) ? $_REQUEST["reset_seconds"] : 0;
		$version_reset_doPublish = isset($_REQUEST["reset_doPublish"]) && $_REQUEST["reset_doPublish"] ? 1 : 0;


		$content = "";
		foreach($version->contentTypes as $k){

			$txt = $k;
			$name = "version_reset_" . $k;
			$val = "version_reset_" . $k;
			$checked = isset($_REQUEST[$k]) ? $_REQUEST[$k] : 0;
			if($k == "all"){
				$jvs = "checkAll(this);";
				$content .= we_forms::checkbox($val, $checked, $name, g_l('versions', '[versions_all]'), false, "defaultfont", $jvs) . "<br/>";
			} else{
				$jvs = "checkAllRevert(this);";
				$content .= we_forms::checkbox($val, $checked, $name, g_l('contentTypes', '[' . $txt . ']'), false, "defaultfont", $jvs) . "<br/>";
			}
		}

		$versions_reset_date = weVersionsSearch::getDateSelector("", "reset_date", "_1", $version_reset_date);
		$doPublish = we_forms::checkbox($version_reset_doPublish, $version_reset_doPublish, "reset_doPublish", g_l('versions', '[publishIfReset]'), false, "defaultfont", "");

		$parts = array(
			array(
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[ct_reset_text]'), 2, 520),
				'noline' => 1,
				'space' => 0
			),
			array(
				'headline' => g_l('versions', '[ContentType]'),
				'space' => 170,
				'html' => $content,
				'noline' => 1
			),
			array(
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[doPublish_text]'), 2, 520),
				'noline' => 1,
				'space' => 0
			),
			array(
				'headline' => "", 'html' => $doPublish, 'noline' => 1, 'space' => 1
			),
		);

		$reset_hours = new we_html_select(
				array(
					"id" => "reset_hours",
					"name" => "reset_hours",
					"style" => "",
					"class" => "weSelect",
					"onChange" => ""
			));

		for($x = 0; $x <= 23; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_hours->addOption($x, $txt);
		}

		$reset_hours->selectOption($version_reset_hours);

		$reset_minutes = new we_html_select(
				array(
					"id" => "reset_minutes",
					"name" => "reset_minutes",
					"style" => "",
					"class" => "weSelect",
					"onChange" => ""
			));

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_minutes->addOption($x, $txt);
		}

		$reset_minutes->selectOption($version_reset_minutes);

		$reset_seconds = new we_html_select(
				array(
					"id" => "reset_seconds",
					"name" => "reset_seconds",
					"style" => "",
					"class" => "weSelect",
					"onChange" => ""
			));

		for($x = 0; $x <= 59; $x++){
			$txt = $x;
			if($x <= 9){
				$txt = "0" . $x;
			}
			$reset_seconds->addOption($x, $txt);
		}

		$reset_seconds->selectOption($version_reset_seconds);

		$parts[] = array(
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('versions', '[date_reset_text]'), 2, 520),
			'noline' => 1,
			'space' => 0
		);

		$clearDate = we_button::create_button(
				"reset", "javascript:document.getElementById('reset_date').value='';", true, -1, -1, "", "", "", false);

		$parts[] = array(
			'headline' => g_l('versions', '[time]'),
			'html' => "<div style='padding-bottom:3px;'>Tag:</div><div style='float:left;'>" . $versions_reset_date . "</div><div style='float:left;margin:0px 0px 10px 10px;'>" . $clearDate . "</div><br style='clear:left;' /><div style='padding-bottom:3px;'>Uhrzeit:</div>" . $reset_hours->getHtml() . " h : " . $reset_minutes->getHtml() . " m: " . $reset_seconds->getHtml() . " s ",
			'noline' => 1,
			'space' => 170
		);

		//js
		$jsCheckboxCheckAll = '';
		$jsCheckboxCtIf = '';

		$jsCheckboxArgs = '';
		foreach($version->contentTypes as $k){
			if($k != "all"){
				$jsCheckboxCheckAll .= 'document.getElementById("version_reset_' . $k . '").checked = checked;';
			}
			if($jsCheckboxCtIf != "")
				$jsCheckboxCtIf .= " && ";
			$jsCheckboxCtIf .= 'document.getElementById("version_reset_' . $k . '").checked==0';
			$jsCheckboxArgs .= 'args += "&ct[' . $k . ']="+escape(document.getElementById("version_reset_' . $k . '").checked);';
		}

		$nextButton = we_button::create_button(
				"next", "javascript:parent.wizbody.handle_event(\"next\");", true, -1, -1, "", "", "", false);

		$js = 'window.onload = function(){
					top.focus();
				}
				function handle_event(what){
					f = document.we_form;
					switch(what){
						case "previous":
							f.step.value=0
							f.target="wizbody";
							f.submit();
							break;
						case "next":
							var date = document.getElementById("reset_date").value;
							var hour = document.getElementById("reset_hours").value;
							var minutes = document.getElementById("reset_minutes").value;
							var seconds = document.getElementById("reset_seconds").value;
							if(' . $jsCheckboxCtIf . ') {
								' . we_message_reporting::getShowMessageCall(
				g_l('versions', '[notCheckedContentType]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
							}
							else if(date=="") {
								' . we_message_reporting::getShowMessageCall(
				g_l('versions', '[notCheckedDate]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
							}
							else {
								selectedValue="";
								for(var i=0;i<f.type.length;i++){
									if(f.type[i].checked){;
										selectedValue = f.type[i].value;
									}
								}
								goTo(selectedValue);
							}
						break;
					}
				}

				function checkAll(val) {

		            	if(val.checked) {
		            		checked = 1;
		            	}
		            	else {
		            		checked = 0;
		            	}
						' . $jsCheckboxCheckAll . ';

					}

	            	function checkAllRevert() {

	            		var checkbox = document.getElementById("version_reset_all");
						checkbox.checked = false;
	            	}

		            function calendarSetup(){

		            	if(document.getElementById("date_picker_1") != null) {
							Calendar.setup({inputField:"reset_date",ifFormat:"%d.%m.%Y",button:"date_picker_1",align:"Tl",singleClick:true});
						}

					}

				function goTo(where){
					f = document.we_form;
					switch(where){
						case "reset_versions":
							f.target="wizbody";
							break;
					}
					f.submit();
				}

				function set_button_state(alldis) {
					if(top.frames["wizbusy"] && top.frames["wizbusy"].switch_button_state){
						top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "enabled");
						if(alldis){
							top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
							top.frames["wizbusy"].showRefreshButton();
						}else{
							top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
							var nextBut = top.frames["wizbusy"].document.getElementById(\'nextCell\');
				  			nextBut.innerHTML = \'' . $nextButton . '\';
						}
					}else{
						setTimeout("set_button_state("+(alldis ? 1 : 0)+")",300);
					}
				}
				set_button_state(false);';

		$calendar = we_html_element::jsElement("calendarSetup();");

		$parts[] = array(
			'html' => $calendar, 'noline' => 0, 'space' => 0
		);

		return array(
			$js,
			we_multiIconBox::getHTML(
				"", "100%", $parts, 40, "", -1, "", "", false, g_l('versions', '[reset_versions]') . " - " . g_l('versions', '[step]') . " 1 " . g_l('versions', '[of]') . " 2") .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "versions_wizard")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "2"))
		);
	}

	/**
	 * returns the HTML for the Third Step (2) of the wizard. - Here the real work (loop) is done - it should be displayed in the cmd frame
	 *
	 * @return string
	 */
	static function getStep2(){
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "delete_versions";

		switch($type){
			case "delete_versions" :
				return self::getDelete2();
			case "reset_versions" :
				return self::getReset2();
		}
	}

	static function getStep3(){
		$version = new weVersions();

		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "delete_versions";

		$version_delete = array();
		$version_reset = array();

		foreach($version->contentTypes as $k){
			$version_delete[$k] = isset($_REQUEST["version_delete_" . $k]) ? 1 : 0;
			$version_reset[$k] = isset($_REQUEST["version_reset_" . $k]) ? 1 : 0;
		}

		$version_delete['delete_date'] = isset($_REQUEST["delete_date"]) ? $_REQUEST["delete_date"] : "";
		$version_delete['delete_hours'] = isset($_REQUEST["delete_hours"]) ? $_REQUEST["delete_hours"] : 0;
		$version_delete['delete_minutes'] = isset($_REQUEST["delete_minutes"]) ? $_REQUEST["delete_minutes"] : 0;
		$version_delete['delete_seconds'] = isset($_REQUEST["delete_seconds"]) ? $_REQUEST["delete_seconds"] : 0;

		$version_reset['reset_date'] = isset($_REQUEST["reset_date"]) ? $_REQUEST["reset_date"] : "";
		$version_reset['reset_hours'] = isset($_REQUEST["reset_hours"]) ? $_REQUEST["reset_hours"] : 0;
		$version_reset['reset_minutes'] = isset($_REQUEST["reset_minutes"]) ? $_REQUEST["reset_minutes"] : 0;
		$version_reset['reset_seconds'] = isset($_REQUEST["reset_seconds"]) ? $_REQUEST["reset_seconds"] : 0;
		if(isset($_REQUEST["reset_doPublish"])){
			$version_reset['reset_doPublish'] = 1;
		} elseif(isset($_REQUEST["type"]) && $_REQUEST["type"] == "reset_versions"){
			$version_reset['reset_doPublish'] = 0;
		} else{
			$version_reset['reset_doPublish'] = 1;
		}

		$taskname = md5(session_id() . "_version_wizard");
		$currentTask = isset($_GET["fr_" . $taskname . "_ct"]) ? $_GET["fr_" . $taskname . "_ct"] : 0;
		$taskFilename = FRAGMENT_LOCATION . $taskname;

		$js = "";
		if(!(file_exists($taskFilename) && $currentTask)){
			switch($type){
				case "delete_versions" :
					$data = we_version::getDocuments($type, $version_delete);
					break;
				case "reset_versions" :
					$data = we_version::getDocuments($type, $version_reset);
					break;
			}
			if(count($data)){
				$fr = new versionFragment($taskname, 1, 0, array(), $data);

				return array();
			} else{
				return array(
					$js . we_message_reporting::getShowMessageCall(g_l('versions', '[deleteNothingFound]'), 1) . 'top.wizbusy.showPrevNextButton();',
					""
				);
			}
		} else{
			$fr = new versionFragment($taskname, 1, 0, array());
			return array();
		}
	}

	/**
	 * returns Array with javascript (array[0]) and HTML Content (array[1]) for the rebuild document page
	 *
	 * @return array
	 */
	static function getDelete2(){
		$version = new weVersions();

		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "delete_versions";

		$version_delete = array();

		foreach($version->contentTypes as $k){
			$version_delete[$k] = isset($_REQUEST["version_delete_" . $k]) ? 1 : 0;
		}

		$version_delete['delete_date'] = isset($_REQUEST["delete_date"]) ? $_REQUEST["delete_date"] : "";
		$version_delete['delete_hours'] = isset($_REQUEST["delete_hours"]) ? $_REQUEST["delete_hours"] : 0;
		$version_delete['delete_minutes'] = isset($_REQUEST["delete_minutes"]) ? $_REQUEST["delete_minutes"] : 0;
		$version_delete['delete_seconds'] = isset($_REQUEST["delete_seconds"]) ? $_REQUEST["delete_seconds"] : 0;

		$timestamp = "";
		$timestampWhere = 1;
		if($version_delete['delete_date'] != ""){
			$date = explode(".", $_REQUEST["delete_date"]);
			$day = $date[0];
			$month = $date[1];
			$year = $date[2];
			$hour = $version_delete['delete_hours'];
			$minutes = $version_delete['delete_minutes'];
			$seconds = $version_delete['delete_seconds'];
			$timestamp = mktime($hour, $minutes, $seconds, $month, $day, $year);

			$timestampWhere = " timestamp< '" . $timestamp . "' ";
		}

		$parts = array();

		$whereCt = "";
		foreach($version_delete as $k => $v){
			if($k != "all" && $k != "delete_date" && $k != "delete_hours" && $k != "delete_minutes" && $k != "delete_seconds"){
				if($v){
					if($whereCt != "")
						$whereCt .= ",";
					$whereCt .= "'" . $k . "'";
				}
			}
		}
		$whereCt = (empty($whereCt) ? '1' : " ContentType IN (" . $whereCt . ")");

		$cont = array();
		$docIds = array();
		$query = "SELECT ID,documentID,documentTable,Text,Path,ContentType,binaryPath,timestamp,version FROM " . VERSIONS_TABLE . " WHERE " . $whereCt . " AND " . $timestampWhere . " ORDER BY ID";
		$_SESSION['weS']['versions']['deleteWizardWhere'] = $whereCt . " AND " . $timestampWhere;
		$GLOBALS['DB_WE']->query($query);
		$_SESSION['weS']['versions']['logDeleteIds'] = array();
		while($GLOBALS['DB_WE']->next_record()) {
			if(!in_array($GLOBALS['DB_WE']->f("documentID"), $docIds)){
				$docIds[$GLOBALS['DB_WE']->f("documentID")]["Path"] = $GLOBALS['DB_WE']->f("Path");
				$docIds[$GLOBALS['DB_WE']->f("documentID")]["ContentType"] = $GLOBALS['DB_WE']->f("ContentType");
			}
			$cont[] = array(
				"ID" => $GLOBALS['DB_WE']->f("ID"),
				"documentID" => $GLOBALS['DB_WE']->f("documentID"),
				"version" => $GLOBALS['DB_WE']->f("version"),
				"text" => $GLOBALS['DB_WE']->f("Text"),
				"path" => $GLOBALS['DB_WE']->f("Path"),
				"table" => $GLOBALS['DB_WE']->f("documentTable"),
				"contentType" => $GLOBALS['DB_WE']->f("ContentType"),
				"timestamp" => $GLOBALS['DB_WE']->f("timestamp")
			);
			$_SESSION['weS']['versions']['logDeleteIds'][$GLOBALS['DB_WE']->f('ID')] = array(
				'Text' => $GLOBALS['DB_WE']->f('Text'),
				'ContentType' => $GLOBALS['DB_WE']->f('ContentType'),
				'Path' => $GLOBALS['DB_WE']->f('Path'),
				'Version' => $GLOBALS['DB_WE']->f('version'),
				'documentID' => $GLOBALS['DB_WE']->f('documentID'),
			);
			if($GLOBALS['DB_WE']->f("binaryPath") != ""){
				$_SESSION['weS']['versions']['deleteWizardbinaryPath'][] = $GLOBALS['DB_WE']->f("binaryPath");
			}
		}

		$out = '<div style="width:520px;">' .
			g_l('versions', '[step2_txt1]');

		if($timestamp != ""){
			$date = date("d.m.y - H:i:s", $timestamp);
			$out .= sprintf(g_l('versions', '[step2_txt2_delete]'), $date);
		}
		$out .= g_l('versions', '[step2_txt3]') .
			'</div>
<div style="background-color:#fff;width:520px;margin-top:20px;">
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
		<tr class="defaultfont" style="height:30px;">
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[_id]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[path]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[ContentType]') . '</th>
		</tr>';

		foreach($docIds as $k => $v){
			$out .= '
<tr class="defaultfont">
	<td align="center">' . $k . '</td>
	<td align="center">' . shortenPath($v['Path'], 55) . '</td>
	<td align="center">' . $v['ContentType'] . '</td>
</tr>';
		}
		$out .= '</table>
			</div>';

		$parts[] = array(
			"headline" => "", "html" => $out, "space" => 0
		);

		$hiddenFields = "";
		foreach($version_delete as $k => $v){
			$hiddenFields .= we_html_element::htmlHidden(array(
					"name" => $k, "value" => $v
				));
		}

		return array(
			self::getPage2Js(empty($cont), "delete"),
			we_multiIconBox::getHTML("", "100%", $parts, 40, "", -1, "", "", false, g_l('versions', '[delete_versions]') . " - " . g_l('versions', '[step]') . " 2 " . g_l('versions', '[of]') . " 2") .
			$hiddenFields .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "versions_wizard")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "3"))
		);
	}

	static function getReset2(){
		$version = new weVersions();

		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "reset_versions";

		$_SESSION['weS']['versions']['logResetIds'] = array();

		$version_reset = array();

		foreach($version->contentTypes as $k){
			$version_reset[$k] = isset($_REQUEST["version_reset_" . $k]) ? 1 : 0;
		}

		$version_reset['reset_date'] = isset($_REQUEST["reset_date"]) ? $_REQUEST["reset_date"] : "";
		$version_reset['reset_hours'] = isset($_REQUEST["reset_hours"]) ? $_REQUEST["reset_hours"] : 0;
		$version_reset['reset_minutes'] = isset($_REQUEST["reset_minutes"]) ? $_REQUEST["reset_minutes"] : 0;
		$version_reset['reset_seconds'] = isset($_REQUEST["reset_seconds"]) ? $_REQUEST["reset_seconds"] : 0;
		if(isset($_REQUEST["reset_doPublish"])){
			$version_reset['reset_doPublish'] = 1;
		} elseif(isset($_REQUEST["type"]) && $_REQUEST["type"] == "reset_versions"){
			$version_reset['reset_doPublish'] = 0;
		} else{
			$version_reset['reset_doPublish'] = 1;
		}
		$timestamp = "";
		$timestampWhere = 1;
		if($version_reset['reset_date'] != ""){
			$date = explode(".", $_REQUEST["reset_date"]);
			$day = $date[0];
			$month = $date[1];
			$year = $date[2];
			$hour = $version_reset['reset_hours'];
			$minutes = $version_reset['reset_minutes'];
			$seconds = $version_reset['reset_seconds'];
			$timestamp = mktime($hour, $minutes, $seconds, $month, $day, $year);

			$timestampWhere = " timestamp< '" . intval($timestamp) . "' ";
		}


		$w = "";
		foreach($version_reset as $k => $v){

			if($k != "all" && $k != "reset_date" && $k != "reset_hours" && $k != "reset_minutes" && $k != "reset_seconds" && $k != "reset_doPublish"){
				if($v){
					if($w != "")
						$w .= " || ";
					$wHelp = " ContentType = '" . $k . "' ";
					if($k == "text/webedition" || $k == "text/html" || $k == "objectFile"){
						$wHelp = $wHelp . " AND status='published' ";
					}
					$w .= "(" . $wHelp . ")";
				}
			}
		}
		$w = (empty($w) ? '1' : "(" . $w . ") ");

		$cont = array();
		$docIds = array();
		$query = "SELECT ID,documentID,documentTable,Text,Path,ContentType,timestamp,MAX(version) as version FROM " . VERSIONS_TABLE . " WHERE timestamp<=" . intval($timestamp) . "   AND " . $w . "  GROUP BY  documentTable,documentID  ORDER BY version DESC ";

		$_SESSION['weS']['versions']['query'] = $query;
		$GLOBALS['DB_WE']->query($query);
		while($GLOBALS['DB_WE']->next_record()) {
			if(!in_array($GLOBALS['DB_WE']->f("documentID"), $docIds)){
				$docIds[$GLOBALS['DB_WE']->f("documentID")]["Path"] = $GLOBALS['DB_WE']->f("Path");
				$docIds[$GLOBALS['DB_WE']->f("documentID")]["ContentType"] = $GLOBALS['DB_WE']->f("ContentType");
			}
			array_push(
				$cont, array(
				"ID" => $GLOBALS['DB_WE']->f("ID"),
				"documentID" => $GLOBALS['DB_WE']->f("documentID"),
				"version" => $GLOBALS['DB_WE']->f("version"),
				"text" => $GLOBALS['DB_WE']->f("Text"),
				"path" => $GLOBALS['DB_WE']->f("Path"),
				"table" => $GLOBALS['DB_WE']->f("documentTable"),
				"contentType" => $GLOBALS['DB_WE']->f("ContentType"),
				"timestamp" => $GLOBALS['DB_WE']->f("timestamp")
			));
		}

		$date = date("d.m.y - H:i:s", $timestamp);
		$out = '
<div style="width:520px;">' . sprintf(g_l('versions', '[step2_txt_reset]'), $date) . '</div>
<div style="background-color:#fff;width:520px;margin-top:20px;">
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<tr class="defaultfont" style="height:30px;">
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[_id]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[path]') . '</th>
		<th style="border-bottom:1px solid #B7B5B6;">' . g_l('versions', '[ContentType]') . '</th>
	</tr>';

		foreach($docIds as $k => $v){
			$out .= '<tr class="defaultfont">
				<td align="center">' . $k . '</td>
				<td align="center">' . shortenPath($v['Path'], 55) . '</td>
				<td align="center">' . $v['ContentType'] . '</td>
			</tr>';
		}
		$out .= '</table>
			</div>';

		$parts = array(
			array(
				"headline" => "", "html" => $out, "space" => 0
			)
		);

		$hiddenFields = "";
		foreach($version_reset as $k => $v){
			$hiddenFields .= we_html_element::htmlHidden(array("name" => $k, "value" => $v));
		}

		return array(
			self::getPage2Js(empty($cont), "reset"),
			we_multiIconBox::getHTML("", "100%", $parts, 40, "", -1, "", "", false, g_l('versions', '[reset_versions]') . " - " . g_l('versions', '[step]') . " 2 " . g_l('versions', '[of]') . " 2") .
			$hiddenFields .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "versions_wizard")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "3"))
		);
	}

	/**
	 * returns HTML for the frameset
	 *
	 * @return string
	 */
	static function getFrameset(){

		$query = array();
		if(isset($_REQUEST["btype"])){
			$query['btype'] = $_REQUEST["btype"];
		}
		if(isset($_REQUEST["type"])){
			$query['type'] = $_REQUEST["type"];
		}
		if(isset($_REQUEST["templateID"])){
			$query['templateID'] = $_REQUEST["templateID"];
		}
		if(isset($_REQUEST["step"])){
			$query['step'] = $_REQUEST["step"];
		}
		if(isset($_REQUEST["responseText"])){
			$query['responseText'] = $_REQUEST["responseText"];
		}

		$taskname = md5(session_id() . "_version_wizard");
		$taskFilename = WE_FRAGMENT_PATH . $taskname;
		if(file_exists($taskFilename)){
			@unlink($taskFilename);
		}

		$cmdFrameHeight = (isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0) ? 30 : 0;

		if(!empty($query)){
			$query['we_cmd[0]'] = 'versions_wizard';
			$query['fr'] = 'body';
			$fst = new we_html_frameset(
					array(
						"rows" => "*,$cmdFrameHeight",
						"framespacing" => 0,
						"border" => 0,
						"frameborder" => "no"
				));

			$fst->addFrame(
				array(
					"src" => WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(array('we_cmd[0]' => 'versions_wizard', 'fr' => 'busy', 'dc' => 1)),
					"name" => "wizbusy"
			));
			$fst->setFrameAttributes(
				0, array(
				"scrolling" => "no",
				"onload" => "wizcmd.location='" . WEBEDITION_DIR . 'we_cmd.php?' . http_build_query($query) . "';"
			));

			$fst->addFrame(array(
				"src" => HTML_DIR . "white.html", "name" => "wizcmd"
			));
			$fst->setFrameAttributes(1, array(
				"scrolling" => "no"
			));
		} else{
			$fst = new we_html_frameset(
					array(
						"rows" => "*,40,$cmdFrameHeight",
						"framespacing" => 0,
						"border" => 0,
						"frameborder" => "no"
				));

			$fst->addFrame(
				array(
					"src" => WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(array('we_cmd[0]' => 'versions_wizard', 'fr' => 'body')),
					"name" => "wizbody"
			));
			$fst->setFrameAttributes(0, array(
				"scrolling" => "auto"
			));

			$fst->addFrame(
				array(
					"src" => WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(array('we_cmd[0]' => 'versions_wizard', 'fr' => 'busy')),
					"name" => "wizbusy"
			));
			$fst->setFrameAttributes(1, array(
				"scrolling" => "no"
			));

			$fst->addFrame(
				array(
					"src" => WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(array('we_cmd[0]' => 'versions_wizard', 'fr' => 'cmd')),
					"name" => "wizcmd"
			));
			$fst->setFrameAttributes(2, array(
				"scrolling" => "no"
			));
		}

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
					we_html_tools::getHtmlInnerHead(g_l('versions', '[versions_wizard]'))) . $fst->getHtml());
	}

	/**
	 * returns Javascript for step 2 (1)
	 *
	 * @return string
	 * @param string $folders csv value with directory IDs
	 */
	static function getPage2Js($cont, $action, $folders = "folders"){
		$disabled = false;
		if($cont){
			$disabled = true;
		}
		//reset
		$act = 1;
		if($action == "delete"){
			//delete
			$act = 0;
		}

		$nextButton = we_button::create_button(
				"go", "javascript:parent.wizbody.handle_event(\"next\");", true, -1, -1, "", "", $disabled, false);
		$publish = isset($_REQUEST['reset_doPublish']) && $_REQUEST['reset_doPublish'] ? 1 : 0;
		$we_transaction = $GLOBALS['we_transaction'];
		return '
window.onload = function(){
	top.focus();
}
function handle_event(what){
	f = document.we_form;
	switch(what){
		case "previous":
			f.step.value=1
			f.target="wizbody";
			f.submit();
			break;
		case "next":

				selectedValue="";
				for(var i=0;i<f.type.length;i++){
					if(f.type[i].checked){;
						selectedValue = f.type[i].value;
					}
				}
				goTo(selectedValue);

		break;
	}
}

var ajaxURL = "' . WEBEDITION_DIR . 'rpc/rpc.php";

var ajaxCallbackDeleteVersionsWizard = {
	success: function(o) {
	if(typeof(o.responseText) != "undefined" && o.responseText != "") {
		parent.wizbusy.document.getElementById("progr").innerHTML = o.responseText;
		' . we_message_reporting::getShowMessageCall(
				addslashes(
					g_l('versions', '[deleteDateVersionsOK]') ? g_l('versions', '[deleteDateVersionsOK]') : ""), we_message_reporting::WE_MESSAGE_NOTICE) . '
		// reload current document => reload all open Editors on demand

		var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
		for (frameId in _usedEditors) {

			if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
				_usedEditors[frameId].setEditorReloadAllNeeded(true);
				_usedEditors[frameId].setEditorIsActive(true);

			} else {
				_usedEditors[frameId].setEditorReloadAllNeeded(true);
			}
		}
		_multiEditorreload = true;

		//reload tree
		top.opener.we_cmd("load", top.opener.treeData.table ,0);
		top.close();
	}
},
	failure: function(o) {
	}
}

var ajaxCallbackResetVersionsWizard = {
	success: function(o) {
	if(typeof(o.responseText) != "undefined" && o.responseText != "") {
		parent.wizbusy.document.getElementById("progr").innerHTML = o.responseText;
		' . we_message_reporting::getShowMessageCall(
				addslashes(
					g_l('versions', '[resetAllVersionsOK]') ? g_l('versions', '[resetAllVersionsOK]') : ""), we_message_reporting::WE_MESSAGE_NOTICE) . '

		top.close();
	}
},
	failure: function(o) {
	}
}

function goTo(where){

	if(' . $act . ') {
		f = document.we_form;
		switch(where){
			case "delete_versions":
				f.target="wizbody";
				break;
		}
		f.submit();

//						parent.wizbusy.document.getElementById("progr").style.display = "block";
//
//						YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackResetVersionsWizard, "protocol=json&publish=' . $publish . '&we_transaction=' . $we_transaction . '&cns=versionlist&cmd=ResetVersionsWizard");
//
	}
	else {
		parent.wizbusy.document.getElementById("progr").style.display = "block";
		//parent.wizbusy.document.getElementById("progr").innerHTML = "<table border=\'0\' width=\'100%\' height=\'100%\'><tr><td align=\'center\'><img src=\'' . IMAGE_DIR . 'busy2.gif\' /></td></tr></table>";

		YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackDeleteVersionsWizard, "protocol=json&cns=versionlist&cmd=DeleteVersionsWizard");
	}
}

function set_button_state(alldis) {
	if(top.frames["wizbusy"] && top.frames["wizbusy"].switch_button_state){
		top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "enabled");
		if(alldis){
			top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
			top.frames["wizbusy"].showRefreshButton();
		}else{
			top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
			var nextBut = top.frames["wizbusy"].document.getElementById(\'nextCell\');
				nextBut.innerHTML = \'' . $nextButton . '\';
		}
	}else{
		setTimeout("set_button_state("+(alldis ? 1 : 0)+")",300);
	}
}
set_button_state(false);';
	}

	/**
	 * returns Javascript for step 2 (1)
	 *
	 * @return string
	 * @param array first element (array[0]) must be a javascript, second element (array[1]) must be the Body HTML
	 */
	static function getPage($contents){
		if(!count($contents)){
			return "";
		}
		$headCal =
			we_html_element::linkElement(
				array(
					"rel" => "stylesheet",
					"type" => "text/css",
					"href" => JS_DIR . "jscalendar/skins/aqua/theme.css",
					"title" => "Aqua"
			)) .
			we_html_element::jsScript(JS_DIR . 'jscalendar/calendar.js') .
			we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS['WE_LANGUAGE'] . '/calendar.js') .
			we_html_element::jsScript(JS_DIR . 'jscalendar/calendar-setup.js');

		$headCal .=
			we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js');

		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					$headCal . STYLESHEET . we_html_element::jsScript(JS_DIR . 'windows.js') . ($contents[0] ? we_html_element::jsElement(
							$contents[0]) : "")) . we_html_element::htmlBody(
					array(
					"class" => "weDialogBody"
					)
					, we_html_element::htmlForm(
						array(
						"name" => "we_form",
						"method" => "post",
						"action" => WEBEDITION_DIR . "we_cmd.php"
						), $contents[1])));
	}

}
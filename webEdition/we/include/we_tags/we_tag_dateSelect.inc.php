<?php

/**
 * webEdition CMS
 *
 * $Rev: 5612 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 22:46:14 +0100 (Mon, 21 Jan 2013) $
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
function we_tag_dateSelect($attribs){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__))){
		return $foo;
	}
	$name = weTag_getAttribute("name", $attribs);
	$class = weTag_getAttribute("class", $attribs);
	$style = weTag_getAttribute("style", $attribs);

	$tmp_from = weTag_getAttribute("start", $attribs);
	$tmp_to = weTag_getAttribute("end", $attribs);

	$from = array();
	$to = array();
	$js = $checkDate = $minyear = $maxyear = '';
	if(!empty($tmp_from) && !empty($tmp_to)){
		$from = array(
			'year' => substr($tmp_from, 0, 4),
			'month' => (substr($tmp_from, 5, 2)) - 1,
			'day' => substr($tmp_from, 8, 2),
			'hour' => strlen($tmp_from) == 16 ? substr($tmp_from, 11, 2) : 0,
			'minute' => strlen($tmp_from) == 16 ? substr($tmp_from, 14, 2) : 0
		);
		$to = array(
			'year' => substr($tmp_to, 0, 4),
			'month' => (substr($tmp_to, 5, 2)) - 1,
			'day' => substr($tmp_to, 8, 2),
			'hour' => strlen($tmp_to) == 16 ? substr($tmp_to, 11, 2) : 0,
			'minute' => strlen($tmp_to) == 16 ? substr($tmp_to, 14, 2) : 0
		);
		$minyear = $from['year'];
		$maxyear = $to['year'];

		$js = we_html_element::jsElement('
function WE_checkDate_' . $name . '() {

	var name = \'' . $name . '\';

	var from = new Date(' . $from['year'] . ', ' . $from['month'] . ', ' . $from['day'] . ', ' . $from['hour'] . ', ' . $from['minute'] . ', 0);
	var to   = new Date(' . $to['year'] . ', ' . $to['month'] . ', ' . $to['day'] . ', ' . $to['hour'] . ', ' . $to['minute'] . ', 59);

	var now = new Date();

	var year = now.getFullYear();
	var month = now.getMonth();
	var day = now.getDate();
	var hour = now.getHours();
	var minute = now.getMinutes();
	var second = 30;

	for (i = 0; i < document.getElementById(name + \'_month\').length; ++i) {
		if (document.getElementById(name + \'_month\').options[i].selected == true) {
			month = document.getElementById(name + \'_month\').options[i].value-1;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_year\').length; ++i) {
		if (document.getElementById(name + \'_year\').options[i].selected == true) {
			year = document.getElementById(name + \'_year\').options[i].value;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_day\').length; ++i) {
		if (document.getElementById(name + \'_day\').options[i].selected == true) {
			day = document.getElementById(name + \'_day\').options[i].value;
		}
	}
	if(document.getElementById(name + \'_hour\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_hour\').length; ++i) {
			if (document.getElementById(name + \'_hour\').options[i].selected == true) {
				hour = document.getElementById(name + \'_hour\').options[i].value;
			}
		}
	}
	if(document.getElementById(name + \'_minute\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_minute\').length; ++i) {
			if (document.getElementById(name + \'_minute\').options[i].selected == true) {
				minute = document.getElementById(name + \'_minute\').options[i].value;
			}
		}
	}

	var test = new Date(year, month, day, hour, minute, second);

	if(!(test.getTime() >= from.getTime() && test.getTime() < to.getTime())) {
		if(test.getTime() < from.getTime()) {
			correct = from;
		} else {
			correct = to;
		}
	} else {
		correct = test;
		while(correct.getMonth() != month) {
			correct.setDate(correct.getDate()-1);
		}
	}
	for (i = 0; i < document.getElementById(name + \'_year\').length; ++i) {
		if (document.getElementById(name + \'_year\').options[i].value == correct.getFullYear()) {
			document.getElementById(name + \'_year\').options[i].selected = true;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_month\').length; ++i) {
		if (document.getElementById(name + \'_month\').options[i].value == correct.getMonth()+1) {
			document.getElementById(name + \'_month\').options[i].selected = true;
		}
	}
	for (i = 0; i < document.getElementById(name + \'_day\').length; ++i) {
		if (document.getElementById(name + \'_day\').options[i].value == correct.getDate()) {
			document.getElementById(name + \'_day\').options[i].selected = true;
		}
	}
	if(document.getElementById(name + \'_hour\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_hour\').length; ++i) {
			if (document.getElementById(name + \'_hour\').options[i].value == correct.getHours()) {
				document.getElementById(name + \'_hour\').options[i].selected = true;
			}
		}
	}
	if(document.getElementById(name + \'_minute\').type == \'select-one\') {
		for (i = 0; i < document.getElementById(name + \'_minute\').length; ++i) {
			if (document.getElementById(name + \'_minute\').options[i].value == correct.getMinutes()) {
				document.getElementById(name + \'_minute\').options[i].selected = true;
			}
		}
	}

}
WE_checkDate_' . $name . '();');


		$checkDate = 'WE_checkDate_' . $name . '();';
	}

	$submitonchange = weTag_getAttribute("submitonchange", $attribs, false, true);
	return we_html_tools::getDateInput2(
			"$name%s", (((!isset($_REQUEST[$name])) || $_REQUEST[$name] == -1) ? time() : $_REQUEST[$name]), false, "dmy", $submitonchange ? $checkDate . "we_submitForm();" : $checkDate, $class, "", $minyear, $maxyear, $style) . $js;
}

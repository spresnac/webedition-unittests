<?php

/**
 * webEdition CMS
 *
 * $Rev: 4538 $
 * $Author: lukasimhof $
 * $Date: 2012-05-16 15:31:57 +0200 (Wed, 16 May 2012) $
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
function we_tag_date($attribs){
	$type = weTag_getAttribute("type", $attribs);
	$format = weTag_getAttribute("format", $attribs, g_l('date', '[format][default]'));

	switch(strtolower($type)){
		case 'js':
			$monthsLong = g_l('date', '[month][long]');
			ksort($monthsLong);
			$monthsShort = g_l('date', '[month][short]');
			ksort($monthsShort);
			$js = 'heute = new Date();
		function getDateS(d){
			switch(d){
				case 1:
				case 21:
				case 31:
					return "st";
				case 2:
				case 22:
					return "nd";
				case 3:
				case 23:
					return "rd";
				default:
					return "th";
			}
		}
		function getDateWord(f,dateObj){
			var l_day_Short = new Array("' . implode('","', g_l('date', '[day][short]')) . '");
			var l_monthLong = new Array("' . implode('","', $monthsLong) . '");
			var l_dayLong = new Array("' . implode('","', g_l('date', '[day][long]')) . '");
			var l_monthShort = new Array("' . implode('","', $monthsShort) . '");
			switch(f){
				case "D":
					return l_day_Short[dateObj.getDay()];
				case "F":
					return l_monthLong[dateObj.getMonth()];
				case "l":
					return l_dayLong[dateObj.getDay()];
				case "M":
					return l_monthShort[dateObj.getMonth()];
			}
		}';

			$js_arr = array();
			$f = str_split($format, 1);
			$ret = array();
			for($i = 0; $i < count($f); $i++){
				$skip = false;
				switch($f[$i]){
					case '\\'://skip next char
						$i++;
					default:
						$ret[] = '"' . $f[$i] . '"';
						$skip = true;
						break;
					case 'Y':
						$js_arr['Y'] = 'var Y = heute.getYear();Y = (Y < 1900) ? (Y + 1900) : Y;';
						break;
					case 'y':
						$js_arr['y'] = 'var y = heute.getYear();y = (y < 1900) ? (y + 1900) : y; y=String(y).substr(2,2);';
						break;
					case 'a':
						$js_arr['a'] = "var a = (heute.getHours() > 11) ? 'pm' : 'am';";
						break;
					case 'A':
						$js_arr['A'] = "var A = (heute.getHours() > 11) ? 'PM' : 'AM';";
						break;
					case 's':
						$js_arr['s'] = "var s = heute.getSeconds();";
						break;
					case 'm':
						$js_arr['m'] = "var m = heute.getMonth()+1;m = '00'+m;m=m.substring(m.length-2,m.length);";
						break;
					case 'n':
						$js_arr['n'] = "var n = heute.getMonth()+1;";
						break;
					case 'd':
						$js_arr['d'] = "var d = heute.getDate();d = '00'+d;d=d.substring(d.length-2,d.length);";
						break;
					case 'd':
						$js_arr['j'] = "var j = heute.getDate();";
						break;
					case 'h':
						$js_arr['h'] = "var h = heute.getHours();if(h > 12){h -= 12;};h = '00'+h;h=h.substring(h.length-2,h.length);";
						break;
					case 'H':
						$js_arr['H'] = "var H = heute.getHours();H = '00'+H;H=H.substring(H.length-2,H.length);";
						break;
					case 'g':
						$js_arr['g'] = "var g = heute.getHours();if(g > 12){ g -= 12;};";
						break;
					case 'G':
						$js_arr['G'] = "var G = heute.getHours();";
						break;
					case 'i':
						$js_arr['i'] = "var i = heute.getMinutes();i = '00'+i;i=i.substring(i.length-2,i.length);";
						break;
					case 'S':
						$js_arr['S'] = "var S = getDateS(heute.getDate());";
						break;
					case 'D':
						$js_arr['D'] = "var D = getDateWord('D',heute);";
						break;
					case 'F':
						$js_arr['F'] = "var F = getDateWord('F',heute);";
						break;
					case 'l':
						$js_arr['l'] = "var l = getDateWord('l',heute);";
						break;
					case 'M':
						$js_arr['M'] = "var M = getDateWord('M',heute);";
						break;
				}
				if(!$skip){
					$ret[] = $f[$i];
				}
			}
			$js.=implode('', $js_arr);

			$js .= 'document.write(' . stripslashes(implode('+', $ret)) . ');';

			return we_html_element::jsElement($js);
		case 'php':
		default:
			return date(correctDateFormat($format));
	}
}

function correctDateFormat($format, $t = 0){
	if(!$t){
		$t = time();
	}
	$dt = is_object($t) ? $t : new DateTime((is_numeric($t) ? '@' : '') . $t);

	$escapes = array('d' => '\\d', 'D' => '\\D', 'j' => '\\j', 'l' => '\\l', 'N' => '\\N', 'S' => '\\S', 'w' => '\\w', 'z' => '\\z',
		'W' => '\\W', 'F' => '\\F', 'M' => '\\M', 'm' => '\\m', 'n' => '\\n', 't' => '\\t', 'L' => '\\L', 'o' => '\\o', 'Y' => '\\Y',
		'y' => '\\y', 'a' => '\\a', 'A' => '\\A', 'B' => '\\B', 'g' => '\\g', 'G' => '\\G', 'h' => '\\h', 'H' => '\\H', 'i' => '\\i', 's' => '\\s',
		'u' => '\\u', 'e' => '\\e', 'I' => '\\I', 'O' => '\\O', 'P' => '\\P', 'T' => '\\T', 'Z' => '\\Z', 'c' => '\\c', 'r' => '\\r', 'U' => '\\U');

	$evals = array_values($escapes);
	//skip escaped
	foreach($evals as $k => $e){
		$format = str_replace($e, '%%' . $k . '%%', $format);
	}

	$rep = array(
		'##1##' => str_replace(array_keys($escapes), array_values($escapes), g_l('date', '[day][short][' . $dt->format('w') . ']')),
		'##2##' => str_replace(array_keys($escapes), array_values($escapes), g_l('date', '[month][long][' . ($dt->format('n') - 1) . ']')),
		'##3##' => str_replace(array_keys($escapes), array_values($escapes), g_l('date', '[day][long][' . $dt->format('w') . ']')),
		'##4##' => str_replace(array_keys($escapes), array_values($escapes), g_l('date', '[month][short][' . ($dt->format('n') - 1) . ']'))
	);

	$format = str_replace(array_keys($rep), array_values($rep), //make sure we don't replace chars in dayname strings
		str_replace(array('D', 'F', 'l', 'M'), array_keys($rep), $format));

	//reset escaped
	foreach($evals as $k => $e){
		$format = str_replace('%%' . $k . '%%', $e, $format);
	}

	return $format;
}

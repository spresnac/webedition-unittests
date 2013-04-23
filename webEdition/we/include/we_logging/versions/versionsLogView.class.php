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
class versionsLogView{

	public $actionView;
	public $versionPerPage = 10;
	private $Model;

	function __construct(){
		$this->Model = new versionsLog();
	}

	function getJS(){

		$js = we_html_element::jsElement('
			var ajaxURL = "' . WEBEDITION_DIR . 'rpc/rpc.php";

			var currentId = 0;

			var ajaxCallbackDetails = {
				success: function(o) {
				if(typeof(o.responseText) != "undefined" && o.responseText != "") {
					document.getElementById("dataContent_"+currentId+"").innerHTML = o.responseText;
				}
			},
				failure: function(o) {
				}
			}

			function openDetails(id) {
				currentId = id;
				var dataContent = document.getElementById("dataContent_"+id+"");
				dataContent.innerHTML = "<table border=\'0\' width=\'100%\' height=\'100%\'><tr><td align=\'center\'><img src=' . IMAGE_DIR . 'logo-busy.gif /></td></tr></table>";
				var otherdataContents = document.getElementsByName("dataContent");
				for(var i=0;i<otherdataContents.length;i++) {
					if(otherdataContents[i].id != "dataContent_"+id+""){
						otherdataContents[i].innerHTML = "";
					}
				}


				YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackDetails, "protocol=json&cns=logging/versions&cmd=GetLogVersionDetails&id="+id+"");

			}

			function showAll(id) {
				var Elements = document.getElementsByName(id+"_list");
				for(var i=0;i<Elements.length;i++) {
					Elements[i].style.display = "";
				}

				var newstartNumber = 1;
				document.getElementById("startNumber_"+id).innerHTML = newstartNumber;

				var newshowNumber = Elements.length;
				document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

				document.getElementById("showAll_"+id).innerHTML = "' . g_l('logging', '[defaultView]') . '";
				document.getElementById("showAll_"+id).onclick = function(){
					showDefault(id);
				};
				document.getElementById("back_"+id).style.display = "none";
				document.getElementById("next_"+id).style.display = "none";

			}

			function showDefault(id) {
				var Elements = document.getElementsByName(id+"_list");
				for(var i=0;i<Elements.length;i++) {
					if(i>=' . $this->versionPerPage . ') {
						Elements[i].style.display = "none";
					}
					else {
						Elements[i].style.display = "";
					}
				}

				var newstartNumber = 1;
				document.getElementById("startNumber_"+id).innerHTML = newstartNumber;

				var newshowNumber = ' . $this->versionPerPage . ';
				document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

				document.getElementById("back_"+id).style.display = "none";
				document.getElementById("next_"+id).style.display = "inline";

				document.getElementById("showAll_"+id).innerHTML = "' . g_l('logging', '[all]') . '";
				document.getElementById("showAll_"+id).onclick = function(){
					showAll(id);
				};

				document.getElementsByName("start_"+id)[0].value = 0;

			}

			function next(id) {
				var start = document.getElementsByName("start_"+id)[0].value;
				var newStart = parseInt(start) + ' . $this->versionPerPage . ';

				var Elements = document.getElementsByName(id+"_list");
				for(var i=0;i<Elements.length;i++) {
					if(i>=newStart && i<(newStart + ' . $this->versionPerPage . ')) {
						Elements[i].style.display = "";
					}
					else {
						Elements[i].style.display = "none";
					}

				}

				if(newStart>(Elements.length-' . $this->versionPerPage . ')) {
					document.getElementById("next_"+id).style.display = "none";
				}
				else {
					document.getElementById("next_"+id).style.display = "inline";
				}
				document.getElementById("back_"+id).style.display = "inline";

				var newstartNumber = newStart+1;
				document.getElementById("startNumber_"+id).innerHTML = newstartNumber;

				var newshowNumber = Elements.length;
				if(Elements.length>(newStart+' . $this->versionPerPage . ')) {
					newshowNumber = (newStart+' . $this->versionPerPage . ');
				}

				document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

				document.getElementsByName("start_"+id)[0].value = parseInt(newStart);


			}

			function back(id) {
				var start = document.getElementsByName("start_"+id)[0].value;
				var newStart = parseInt(start) - ' . $this->versionPerPage . ';

				var Elements = document.getElementsByName(id+"_list");
				for(var i=0;i<Elements.length;i++) {
					if(i>=newStart && i<(newStart + ' . $this->versionPerPage . ')) {
						Elements[i].style.display = "";
					}
					else {
						Elements[i].style.display = "none";
					}

				}


				if(newStart==0) {
					document.getElementById("back_"+id).style.display = "none";
				}
				else {
					document.getElementById("back_"+id).style.display = "inline";
				}
				document.getElementById("next_"+id).style.display = "inline";

				var newstartNumber = newStart+1;
				document.getElementById("startNumber_"+id).innerHTML = newstartNumber;


				newshowNumber = (newstartNumber+' . $this->versionPerPage . ');
				document.getElementById("showNumber_"+id).innerHTML = newshowNumber;

				document.getElementsByName("start_"+id)[0].value = parseInt(newStart);

			}


		');

		return $js;
	}

	function getContent(){

		$content = $this->Model->load();

		return $content;
	}

	function printContent(){
		$content = $this->getContent();
		$out = '';
		$anz = count($content);
		if($anz){
			$out .= '<div align="center" width="100%"><table border="0" width="100%" cellpadding="0" cellspacing="0" class="middlefont">';
//		$out .= '<thead>';
//		$out .= '<tr>';
//		$out .= '<th style="width:150px;">';
//		$out .= g_l('logging','[date]');
//		$out .= '</th>';
//		$out .= '<th style="width:100px;">';
//		$out .= g_l('logging','[user]');
//		$out .= '</th>';
//		$out .= '<th style="width:350px;">';
//		$out .= g_l('logging','[logEntry]');
//		$out .= '</th>';
//		$out .= '</tr>';
//		$out .= '</thead>';


			for($i = 0; $i < $anz; $i++){
				$out .= '<tr><td style="font-weight:bold;width:100px;padding:5px 15px 5px 15px;">' .
					g_l('logging', '[date]') . ':</td><td width="200">' .
					date("d.m.y - H:i:s", $content[$i]['timestamp']) . '</td>' .
					'<td width="auto">' . we_html_tools::getPixel(1, 1) . '</td></tr>' .
					'<tr><td style="font-weight:bold;width:100px;padding:5px 15px 5px 15px;">' .
					g_l('logging', '[user]') . ':</td><td width="auto">' .
					f("SELECT Text FROM `" . USER_TABLE . "` WHERE ID=" . intval($content[$i]['userID']), "Text", new DB_WE()) .
					'</td></tr>' .
					'<tr><td style="font-weight:bold;width:100px;padding:5px 15px 5px 15px;">' .
					g_l('logging', '[logEntry]') . ':</td><td width="auto">' .
					$this->showLog($content[$i]['action'], $content[$i]['ID']) . '</td></tr>' .
					'<tr><td colspan="3" style="padding:5px 15px 5px 15px;"><div id="dataContent_' . $content[$i]['ID'] . '" name="dataContent">' .
					$this->handleData($content[$i]['ID'], 0, $this->versionPerPage) . '</div>' .
					'<div style="border-top:1px solid #000;margin-top:20px;margin-bottom:20px;">' . we_html_tools::getPixel(1, 1) .
					'</div></td></tr>';
			}

			$out .= '</table></div>';
		} else{
			$out = '<div align="center" width="100%">' . g_l('logging', '[notfound]') . '</div>';
		}

		return $out;
	}

	function showLog($action, $logID){
		switch($action){
			case versionsLog::VERSIONS_DELETE:
				$title = g_l('logging', '[versions]') . " " . g_l('logging', '[deleted]');
				break;
			case versionsLog::VERSIONS_RESET:
				$title = g_l('logging', '[versions]') . " " . g_l('logging', '[reset]');
				break;
			case versionsLog::VERSIONS_PREFS:
				$title = g_l('logging', '[prefsVersionChanged]');
				break;
		}

		return $title . '.';
	}

	function handleData($logId, $start, $anzahl){
		list($data, $action) = getHash('SELECT data,action FROM `' . VERSIONS_TABLE_LOG . '` WHERE ID=' . intval($logId), new DB_WE());

		$data = unserialize($data);

		$out = '';

		if($action == versionsLog::VERSIONS_DELETE || $action == versionsLog::VERSIONS_RESET){

			$out .= '<table cellpadding="3" cellspacing="0" border="0" style="width:100%;border:1px solid #BBBAB9;" class="middlefont">' .
				'<thead><tr style="background-color:#dddddd;font-weight:bold;"><td>' .
				we_html_tools::getPixel(1, 1) . '</td><td>' .
				g_l('logging', '[ID]') . '</td><td>' .
				g_l('logging', '[name]') . '</td><td>' .
				g_l('logging', '[path]') . '</td><td>' .
				g_l('logging', '[version]') . '</td><td>' .
				g_l('logging', '[contenttype]') .
				'</td></tr></thead>';

			$anzGesamt = count($data);

			$orderedArray = array();
			foreach($data as $k => $v){
				$orderedArray[] = $v;
			}

			$showNumber = 0;
			//for($i=$start;$i<$anzahl;$i++) {
			foreach($orderedArray as $k => $v){

				$display = "none";
				$m = $k + 1;
				$name = $logId . '_list';
				if($k >= $start && $k < $anzahl){
					$display = "";
					$showNumber++;
				}
				$out .= '<tr id="' . $name . '" name="' . $name . '" style="display:' . $display . ';">
					<td align="left">' . $m . '.</td><td align="left">' .
					$v['documentID'] . '</td><td align="left">' .
					shortenPath($v['Text'], 18) . '</td><td align="left">' .
					shortenPath($v['Path'], 40) . '</td><td align="left">' .
					$v['Version'] . '</td><td align="left">' .
					$v['ContentType'] . '</td></tr>';
			}
			$out .= '<tr style="background-color:#dddddd;">
				<td style="border-top:1px solid #BBBAB9;padding:3px 5px 3px 3px;" align="right" colspan="6">
				<span id="startNumber_' . $logId . '">' . ($start + 1) . '</span> - <span id="showNumber_' . $logId . '">' . $showNumber . '</span> <span>' . g_l('logging', '[of]') . '</span> <span style="margin-right:20px;">' . $anzGesamt . '</span>' .
				(($anzGesamt > $this->versionPerPage) ? '<span style="margin-right:20px;"><a id="showAll_' . $logId . '" href="#" onclick="showAll(' . $logId . ');">' . g_l('logging', '[all]') . '</a></span>' : '') .
				'<span style="margin-right:5px;"><a title="' . g_l('logging', '[back]') . '" href="#" onclick="back(' . $logId . ');"><img src=\'' . IMAGE_DIR . 'navigation/button_arrow_left.gif\' id="back_' . $logId . '" style="display:none;border:2px solid #DDD;"  /></a></span>' .
				(($anzGesamt > $this->versionPerPage) ? '<span style="margin-right:5px;"><a title="' . g_l('logging', '[next]') . '" href="#" onclick="next(' . $logId . ');"><img src=\'' . IMAGE_DIR . 'navigation/button_arrow_right.gif\' id="next_' . $logId . '" style="border:2px solid #DDD;" /></a></span>' : '') .
				we_html_tools::hidden("start_" . $logId, $start) . '</td></tr>
				</table>';
		} elseif($action == versionsLog::VERSIONS_PREFS){

			$secondsDay = 86400;
			$secondsWeek = 604800;
			$secondsYear = 31449600;

			foreach($data as $k => $v){

				switch($k){
					case "version_image/*":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[image/*]') . ": " . $val;
						break;
					case "version_text/html":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/html]') . ": " . $val;
						break;
					case "version_text/webedition":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/webedition]') . ": " . $val;
						break;
					case "version_text/js":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/js]') . ": " . $val;
						break;
					case "version_text/css":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/css]') . ": " . $val;
						break;
					case "version_text/plain":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/plain]') . ": " . $val;
						break;
					case "version_text/htaccess":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/htaccess]') . ": " . $val;
						break;
					case "version_text/weTmpl"://#4120
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/weTmpl]') . ": " . $val;
						break;
					case "version_application/x-shockwave-flash":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[application/x-shockwave-flash]') . ": " . $val;
						break;
					case "version_video/quicktime":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[video/quicktime]') . ": " . $val;
						break;
					case "version_application/*":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[application/*]') . ": " . $val;
						break;
					case "version_text/xml":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[text/xml]') . ": " . $val;
						break;
					case "version_objectFile":
						$val = (isset($v) && $v) ? g_l('logging', '[activated]') : g_l('logging', '[deactivated]');
						$out .= '-> ' . g_l('logging', '[contenttype]') . " " . g_l('contentTypes', '[objectFile]') . ": " . $val;
						break;
					case "versions_time_days":
						$val = (isset($v) && $v != "" && $v != -1) ? ($v / $secondsDay) : "";
						$out .= '-> ' . g_l('logging', '[zeitraum]') . " " . g_l('logging', '[days]') . ": " . $val;
						break;
					case "versions_time_weeks":
						$val = (isset($v) && $v != "" && $v != -1) ? ($v / $secondsWeek) : "";
						$out .= '-> ' . g_l('logging', '[zeitraum]') . " " .
							g_l('logging', '[weeks]') . ": " . $val;
						break;
					case "versions_time_years":
						$val = (isset($v) && $v != "" && $v != -1) ? ($v / $secondsYear) : "";
						$out .= '-> ' . g_l('logging', '[zeitraum]') . " " .
							g_l('logging', '[years]') . ": " . $val;
						break;
					case "versions_anzahl":
						$val = (isset($v) && $v != "") ? $v : "";
						$out .= '-> ' . g_l('logging', '[anzahlVersions]') . ": " . $val;
						break;
				}
				$out .= we_html_element::htmlBr();
			}


			$out .= we_html_element::htmlBr();
		}

		return $out;
	}

}
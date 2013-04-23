<?php

/**
 * webEdition CMS
 *
 * $Rev: 5075 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 02:04:23 +0100 (Tue, 06 Nov 2012) $
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
 * @package    webEdition_update
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$errorMessage = "";
if(isset($Response)){
	$errorMessage .= str_replace("</body></html>", "", stristr($Response, "<body>"));
}
$errorMessage .= "<div id=\"contentHeadlineDiv\" style=\"height: 30px; margin-top:30px; \">
			<b>" . g_l('liveUpdate', '[connect][connectionInfo]') . "<hr /></b>
			</div><br />";
$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][availableConnectionTypes]') . ": ";
$errorMessage .= "<ul>";
if(ini_get("allow_url_fopen") == "1"){
	$errorMessage .= "<li>fopen</li>";
}
if(is_callable("curl_exec")){
	$errorMessage .= "<li>curl</li>";
}
$errorMessage .= "</ul>";
$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][connectionType]') . ": ";
if(isset($_SESSION['le_proxy_use']) && $_SESSION['le_proxy_use'] == "1"){
	$errorMessage .= "Proxy (fsockopen)" .
		"<ul>" .
		"<li>" . g_l('liveUpdate', '[connect][proxyHost]') . ": " . $_SESSION["le_proxy_host"] . "</li>" .
		"<li>" . g_l('liveUpdate', '[connect][proxyPort]') . ": " . $_SESSION["le_proxy_port"] . "</li>";
	if(is_callable("gethostbynamel") && is_callable("gethostbyaddr")){
		if(preg_match("/(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/", $_SESSION["le_proxy_host"])){
			$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][ipResolutionTest]') . " (IPv4 only): ";
			$hostName = gethostbyaddr((string) $_SESSION["le_proxy_host"]);
			if($hostName != $_SESSION["le_proxy_host"]){
				$errorMessage .= "" . g_l('liveUpdate', '[connect][succeeded]') . ".</li>" .
					"<li>" . g_l('liveUpdate', '[connect][hostName]') . ": " . $hostName . "</li>";
			} else{
				$errorMessage .= "" . g_l('liveUpdate', '[connect][failed]') . ".</li>";
			}
		}
		// gethostbyaddr currently does not support ipv6 address resolution
		/*
		  else if(preg_match("/^([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4}$/",$_SESSION["le_proxy_host"])) {
		  $errorMessage .= "<li>".$GLOBALS['l_liveUpdate']['connect']["ipResolutionTest"]." (IPv6): ";
		  $hostName = gethostbyaddr($_SESSION["le_proxy_host"],DNS_AAAA);
		  if($hostName != $_SESSION["le_proxy_host"]) {
		  $errorMessage .= "".$GLOBALS['l_liveUpdate']['connect']["succeeded"].".</li>".
		  "<li>".$GLOBALS['l_liveUpdate']['connect']["ipAddresses"].": ".$hostName."</li>";
		  } else {
		  $errorMessage .= "".$GLOBALS['l_liveUpdate']['connect']["failed"].".</li>";
		  }
		  }
		 */ else{
			$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][dnsResolutionTest]') . ": ";
			if($ipAddr = gethostbynamel($_SESSION["le_proxy_host"])){
				$errorMessage .= "" . g_l('liveUpdate', '[connect][succeeded]') . ".</li>" .
					"<li>" . g_l('liveUpdate', '[connect][ipAddresses]') . ": " . implode(",", $ipAddr) . "</li>";
			} else{
				$errorMessage .= "" . g_l('liveUpdate', '[connect][failed]') . ".</li>";
			}
		}
	}
	$errorMessage .= "</ul>";
} else{
	$errorMessage .= liveUpdateHttp::getHttpOption();
}
$errorMessage .= "</li>" .
	"<li>" . g_l('liveUpdate', '[connect][addressResolution]') . " " . g_l('liveUpdate', '[connect][updateServer]') . ":</li>" .
	"<ul>" .
	"<li>" . g_l('liveUpdate', '[connect][hostName]') . ": " . LIVEUPDATE_SERVER . "</li>";
if(is_callable("gethostbynamel")){
	$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][dnsResolutionTest]') . ": ";
	if($ipAddr = gethostbynamel(LIVEUPDATE_SERVER)){
		$errorMessage .= "" . g_l('liveUpdate', '[connect][succeeded]') . ".</li>" .
			"<li>" . g_l('liveUpdate', '[connect][ipAddresses]') . ": " . implode(",", $ipAddr) . "</li>";
	} else{
		$errorMessage .= "" . g_l('liveUpdate', '[connect][failed]') . ".</li>";
	}
	$errorMessage .= "</ul>";
}

$content = '
<div class="defaultfont">
	' . g_l('liveUpdate', '[connect][connectionError]') . '
</div>' . we_html_element::jsElement('alert("' . g_l('liveUpdate', '[connect][connectionErrorJs]') . '");') . $errorMessage;

print liveUpdateTemplates::getHtml(g_l('liveUpdate', '[connect][headline]'), $content);

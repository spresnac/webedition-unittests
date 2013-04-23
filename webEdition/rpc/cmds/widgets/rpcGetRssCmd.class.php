<?php

/**
 * webEdition CMS
 *
 * $Rev: 5938 $
 * $Author: mokraemer $
 * $Date: 2013-03-10 20:22:31 +0100 (Sun, 10 Mar 2013) $
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
 * @package    webEdition_rpc
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

class rpcGetRssCmd extends rpcCmd{

	function execute(){

		$sRssUri = $_REQUEST['we_cmd'][0];
		$sCfgBinary = $_REQUEST['we_cmd'][1];
		$bCfgTitle = (bool) $sCfgBinary{0};
		$bCfgLink = (bool) $sCfgBinary{1};
		$bCfgDesc = (bool) $sCfgBinary{2};
		$bCfgContEnc = (bool) $sCfgBinary{3};
		$bCfgPubDate = (bool) $sCfgBinary{4};
		$bCfgCategory = (bool) $sCfgBinary{5};
		$iNumItems = $_REQUEST['we_cmd'][2];
		switch($iNumItems){
			case 11:
				$iNumItems = 15;
				break;
			case 12:
				$iNumItems = 20;
				break;
			case 13:
				$iNumItems = 25;
				break;
			case 14:
				$iNumItems = 50;
				break;
		}
		$sTbBinary = $_REQUEST['we_cmd'][3];
		$bTbLabel = (bool) $sTbBinary{0};
		$bTbTitel = (bool) $sTbBinary{1};
		$bTbDesc = (bool) $sTbBinary{2};
		$bTbLink = (bool) $sTbBinary{3};
		$bTbPubDate = (bool) $sTbBinary{4};
		$bTbCopyright = (bool) $sTbBinary{5};

		//Bug 6119: Keine Unterstützung für curl in der XML_RSS Klasse
		//daher Umstellung den Inhalt des Feeds selbst zu holen
		$parsedurl = parse_url($sRssUri);
		$http_request = new HttpRequest($parsedurl['path'], $parsedurl['host'], 'GET');
		$http_request->executeHttpRequest();
		$http_response = new HttpResponse($http_request->getHttpResponseStr());
		if(isset($http_response->http_headers['Location'])){//eine Weiterleitung ist aktiv
			$parsedurl = parse_url($http_response->http_headers['Location']);
			$http_request = new HttpRequest($parsedurl['path'], $parsedurl['host'], 'GET');
			$http_request->executeHttpRequest();
			$http_response = new HttpResponse($http_request->getHttpResponseStr());
		}
		$feeddata = $http_response->http_body;
		$oRssParser = new XML_RSS($feeddata, null, $GLOBALS['WE_BACKENDCHARSET']); // Umstellung in der XML_RSS-Klasse: den string, und nicht die url weiterzugeben
		$tmp = $oRssParser->parse();
		$sRssOut = "";

		$iCurrItem = 0;
		foreach($oRssParser->getItems() as $item){
			$bShowTitle = ($bCfgTitle && isset($item['title'])) ? true : false;
			$bShowLink = ($bCfgLink && isset($item['link'])) ? true : false;
			$bShowDesc = ($bCfgDesc && isset($item['description'])) ? true : false;
			$bShowContEnc = ($bCfgContEnc && isset($item['content:encoded'])) ? true : false;
			$bShowPubdate = ($bCfgPubDate && isset($item['pubdate'])) ? true : false;
			$bShowCategory = ($bCfgCategory && isset($item['category'])) ? true : false;
			if($bShowTitle){
				$sRssOut .= ($bShowLink) ? we_html_element::htmlA(array("href" => $item['link'], "target" => "_blank"), we_html_element::htmlB($item['title'])) :
					we_html_element::htmlB($item['title']) .
					we_html_element::htmlBr() . we_html_tools::getPixel(1, 5) . (($bShowDesc || $bShowContEnc) ? we_html_element::htmlBr() : '');
			}
			if($bShowPubdate){
				$sRssOut .= g_l('cockpit', "[published]") . ': ' . date(g_l('date', '[format][default]'), strtotime($item['pubdate']));
			}
			if($bShowCategory){
				$sRssOut .= ($bShowPubdate ? we_html_element::htmlBr() . we_html_tools::getPixel(1, 2) . we_html_element::htmlBr() : "") .
					g_l('cockpit', "[category]") . ": " . $item['category'];
			}
			if($bShowPubdate || $bShowCategory){
				$sRssOut .= we_html_element::htmlBr() . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr();
			}
			$sLink = (($bCfgLink && isset($item['link'])) && !$bShowTitle) ? " &nbsp;" .
				we_html_element::htmlA(array("href" => $item['link'], "target" => "_blank", "style" => "text-decoration:underline;"), g_l('cockpit', '[more]')) : "";
			$sRssOut .= ($bShowDesc) ? $item['description'] . $sLink . we_html_element::htmlBr() : "";
			if($bShowContEnc){
				$contEnc = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 1, 1);
				$contEnc->setCol(0, 0, null, $item['content:encoded'] . ((!$bCfgDesc) ? $sLink : ""));
				$sRssOut .= $contEnc->getHTML();
			} else if(!$bShowDesc){
				$sRssOut .= $sLink . we_html_element::htmlBr();
			}
			$sRssOut .= ($bShowDesc || $bShowContEnc) ? we_html_tools::getPixel(1, 10) . we_html_element::htmlBr() : "";
			if($iNumItems){
				$iCurrItem++;
				if($iCurrItem == $iNumItems){
					break;
				}
			}
		}

		$aTb = array();
		if($bTbLabel)
			$aTb[] = g_l('cockpit', '[rss_feed]');
		if($bTbTitel){
			$aTb[] = (isset($_REQUEST['we_cmd'][4]) && $_REQUEST['we_cmd'][4] != "") ?
				$_REQUEST['we_cmd'][4] :
				((isset($oRssParser->channel["title"])) ? $oRssParser->channel["title"] : "");
		}
		if($bTbDesc){
			$aTb[] = (isset($oRssParser->channel["description"])) ? str_replace(array("\n", "\r"), '', $oRssParser->channel["description"]) : '';
		}
		if($bTbLink){
			$aTb[] = (isset($oRssParser->channel["link"])) ? $oRssParser->channel["link"] : '';
		}
		if($bTbPubDate){
			$aTb[] = (isset($oRssParser->channel["pubdate"])) ? (date(g_l('date', '[format][default]'), strtotime($oRssParser->channel["pubdate"]))) : "";
		}
		if($bTbCopyright){
			$aTb[] = (isset($oRssParser->channel["copyright"])) ? $oRssParser->channel["copyright"] : "";
		}
		$resp = new rpcResponse();
		$resp->setData('data', $sRssOut);

		// title
		$_title = implode(' - ', $aTb);
		if(strlen($_title) > 50){
			$_title = substr($_title, 0, 50) . '&hellip;';
		}
		$resp->setData('titel', $_title);
		$resp->setData('widgetType', "rss");
		$resp->setData('widgetId', $_REQUEST['we_cmd'][5]);

		return $resp;
	}

}
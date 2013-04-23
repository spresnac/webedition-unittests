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
define('NO_SESS', 1);
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');


$id = intval(isset($_GET["id"]) ? $_GET["id"] : 0);
$bid = intval(isset($_GET["bid"]) ? $_GET["bid"] : 0);
$did = intval(isset($_GET["did"]) ? $_GET["did"] : 0);
$paths = isset($_GET["paths"]) ? $_GET["paths"] : "";
$target = isset($_GET["target"]) ? $_GET["target"] : "";
$height = intval(isset($_GET["height"]) ? $_GET["height"] : 0);
$width = intval(isset($_GET["width"]) ? $_GET["width"] : 0);
$bannerclick = isset($_GET["bannerclick"]) ? $_GET["bannerclick"] : WEBEDITION_DIR . 'bannerclick.php';
$referer = isset($_GET["referer"]) ? $_GET["referer"] : "";
$type = isset($_GET["type"]) ? $_GET["type"] : "";
$cats = isset($_GET["cats"]) ? $_GET["cats"] : "";
$dt = isset($_GET["dt"]) ? $_GET["dt"] : "";
$link = isset($_GET["link"]) ? $_GET["link"] : 1;
$pixel = isset($_GET["link"]) ? $_GET["link"] : 0;
$bannername = isset($_GET["bannername"]) ? $_GET["bannername"] : "";
$page = isset($_GET["page"]) ? $_GET["page"] : "";
$nocount = isset($_GET["nocount"]) ? $_GET["nocount"] : false;
$xml = (isset($_GET["xml"]) && $_GET["xml"]) ? true : false;
$c = isset($_GET["c"]) ? $_GET["c"] : 0;

if($type && $type != "pixel"){
	$code = weBanner::getBannerCode($did, $paths, $target, $width, $height, $dt, $cats, $bannername, $link, $referer, $bannerclick, getServerUrl() . $_SERVER['SCRIPT_NAME'], $type, $page, $nocount, $xml);
}
switch($type){
	case "js":
		$jsarr = explode("\n", str_replace(array("\r", "'"), array("\n", "\\'"), $code));
		header("Content-type: application/x-javascript");

		foreach($jsarr as $line){
			print "document.writeln('" . $line . "');\n";
		}
		break;
	case "iframe":
		print $code;
		break;
	default:
		if(!$id){
			$bannerData = weBanner::getBannerData($did, $paths, $dt, $cats, $bannername, $GLOBALS['DB_WE']);
			$id = $bannerData["ID"];
			$bid = $bannerData["bannerID"];
		}
		if(!$bid){
			$id = f("SELECT pref_value FROM " . BANNER_PREFS_TABLE . " WHERE pref_name='DefaultBannerID'", "pref_value", $GLOBALS['DB_WE']);
			$bid = f('SELECT bannerID FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($id), "bannerID", $GLOBALS['DB_WE']);
		}

		$bannerpath = f("SELECT Path FROM " . FILE_TABLE . " WHERE ID=" . intval($bid), "Path", $GLOBALS['DB_WE']);

		if(($type == 'pixel' || (!$nocount) && $id && $c)){
			$GLOBALS['DB_WE']->query('INSERT INTO ' . BANNER_VIEWS_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => intval($id),
					'Timestamp' => 'UNIX_TIMESTAMP()',
					'IP' => $_SERVER["REMOTE_ADDR"],
					'Referer' => ($referer ? $referer : (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "")),
					'DID' => intval($did),
					'Page' => $page)));

			$GLOBALS['DB_WE']->query('UPDATE ' . BANNER_TABLE . ' SET views=views+1 WHERE ID=' . intval($id));
			setcookie("webid_$bannername", intval($id));
		}

		if($bannerpath){
			if(!$c){
				header("Location: $bannerpath");
				exit();
			}
			$ext = preg_replace('/.*\.(.+)$/', '\1', $bannerpath);
			switch($ext){
				case "jpg":
				case "jpeg":
					$contenttype = "image/jpeg";
					break;
				case "png":
					$contenttype = "image/png";
					break;
				default:
					$contenttype = "image/gif";
			}


			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
			header("Expires: -1");
			header("Content-disposition: filename=" . basename($bannerpath));
			header("Content-Type: $contenttype");

			readfile($_SERVER['DOCUMENT_ROOT'] . $bannerpath);
		} else{
			header("Content-type: image/gif");
			print chr(0x47) . chr(0x49) . chr(0x46) . chr(0x38) . chr(0x39) . chr(0x61) . chr(0x01) . chr(0x00) .
				chr(0x01) . chr(0x00) . chr(0x80) . chr(0x00) . chr(0x00) . chr(0x04) . chr(0x02) . chr(0x04) .
				chr(0x00) . chr(0x00) . chr(0x00) . chr(0x21) . chr(0xF9) . chr(0x04) . chr(0x01) . chr(0x00) .
				chr(0x00) . chr(0x00) . chr(0x00) . chr(0x2C) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) .
				chr(0x01) . chr(0x00) . chr(0x01) . chr(0x00) . chr(0x00) . chr(0x02) . chr(0x02) . chr(0x44) .
				chr(0x01) . chr(0x00) . chr(0x3B);
		}
}
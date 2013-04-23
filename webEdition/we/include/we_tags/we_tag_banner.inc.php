<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
function we_tag_banner($attribs, $content){
	if(($foo = attributFehltError($attribs, "name", __FUNCTION__)))
		return $foo;

	$bannername = weTag_getAttribute("name", $attribs);
	$paths = weTag_getAttribute("paths", $attribs);
	$type = weTag_getAttribute("type", $attribs, "js");
	$target = weTag_getAttribute("target", $attribs);
	$width = weTag_getAttribute("width", $attribs, ($type == "pixel") ? "1" : "");
	$height = weTag_getAttribute("height", $attribs, ($type == "pixel") ? "1" : "");
	$link = weTag_getAttribute("link", $attribs, true, true);
	$page = weTag_getAttribute("page", $attribs);
	$bannerclick = weTag_getAttribute("clickscript", $attribs, WEBEDITION_DIR . "bannerclick.php");
	$getbanner = weTag_getAttribute("getscript", $attribs, WEBEDITION_DIR . "getBanner.php");
	$xml = weTag_getAttribute('xml', $attribs, false, true);

	$nocount = $GLOBALS["WE_MAIN_DOC"]->InWebEdition;

	if($type == "pixel"){

		$newAttribs['src'] = $getbanner . '?' . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . 'type=pixel&amp;paths=' . rawurlencode($paths) . '&amp;bannername=' . rawurlencode($bannername) . '&amp;cats=' . rawurlencode($GLOBALS["WE_MAIN_DOC"]->Category) . '&amp;dt=' . (isset($GLOBALS["WE_MAIN_DOC"]->DocType) ? rawurlencode($GLOBALS["WE_MAIN_DOC"]->DocType) : "") . ($page ? ('&amp;page=' . rawurlencode($page)) : ('&amp;did=' . $GLOBALS["WE_MAIN_DOC"]->ID)) . '&amp;xml=' . ($xml ? "1" : "0");
		$newAttribs['border'] = 0;
		$newAttribs['alt'] = '';
		$newAttribs['width'] = 1;
		$newAttribs['height'] = 1;

		return getHtmlTag('img', $newAttribs);
	}

	$uniq = md5(uniqid(__FUNCTION__, true));

	// building noscript
	// here build image with link(opt)
	$imgAtts['src'] = $getbanner . '?c=1&amp;bannername=' . rawurlencode($bannername) . '&amp;cats=' . rawurlencode(isset($GLOBALS["WE_MAIN_DOC"]->Category) ? $GLOBALS["WE_MAIN_DOC"]->Category : "") . '&amp;dt=' . rawurlencode(isset($GLOBALS["WE_MAIN_DOC"]->DocType) ? $GLOBALS["WE_MAIN_DOC"]->DocType : "") . '&amp;paths=' . rawurlencode($paths) . ($page ? ('&amp;page=' . rawurlencode($page)) : ('&amp;did=' . $GLOBALS["WE_MAIN_DOC"]->ID)) . '&amp;bannerclick=' . rawurlencode($bannerclick) . '&amp;xml=' . ($xml ? "1" : "0");
	$imgAtts['alt'] = '';
	$imgAtts['border'] = 0;
	if($width){
		$imgAtts['width'] = $width;
	}
	if($height){
		$imgAtts['height'] = $height;
	}
	$img = getHtmlTag('img', $imgAtts);

	if($link){ //  with link
		$linkAtts['href'] = $bannerclick . '?' . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . 'u=' . $uniq . '&amp;bannername=' . rawurlencode($bannername) . ($page ? ('&amp;page=' . rawurlencode($page)) : ('&amp;did=' . $GLOBALS["WE_MAIN_DOC"]->ID));
		if($target){
			$linkAtts['target'] = $target;
		}
		$noscript = getHtmlTag('a', $linkAtts, $img);
	} else{ //  only img
		$noscript = $img;
	}


	if($type == "iframe"){

		// stuff for iframe  and ilayer 
		$newAttribs = removeAttribs($attribs, array('name', 'paths', 'type', 'target', 'link', 'clickscript', 'getscript', 'page'));
		$newAttribs['xml'] = $xml ? "true" : "false";
		$newAttribs['width'] = $width ? $width : 468;
		$newAttribs['height'] = $height ? $height : 60;
		$newAttribs['src'] = $getbanner . '?' . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . 'bannername=' . rawurlencode($bannername) . '&amp;cats=' . rawurlencode($GLOBALS["WE_MAIN_DOC"]->Category) . '&amp;link=' . ($link ? 1 : 0) . '&amp;type=iframe' . ($page ? ('&amp;page=' . rawurlencode($page)) : ('&amp;did=' . $GLOBALS["WE_MAIN_DOC"]->ID . '&amp;paths=' . rawurlencode($paths))) . '&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($bannerclick) . '&amp;width=' . rawurlencode($width) . '&amp;height=' . rawurlencode($height) . '&amp;xml=' . ($xml ? "1" : "0");

		// content
		//$content = getHtmlTag('ilayer',$newAttribs, '',true) . getHtmlTag('nolayer', array(),$noscript);    // WITH ilayer not conform !!!
		//$content = getHtmlTag('nolayer', array(),$noscript);    //  nolayer does not exist
		$content = $noscript;

		//    some more attribs for the iframe
		$newAttribs['marginwidth'] = 0;
		$newAttribs['marginheight'] = 0;
		//$newAttribs['vspace'] = 0;
		//$newAttribs['hspace'] = 0;
		$newAttribs['frameborder'] = 0;
		$newAttribs['scrolling'] = 'no';

		return getHtmlTag('iframe', $newAttribs, $content);
	} else{
		return ($GLOBALS["WE_MAIN_DOC"]->IsDynamic ?
				weBanner::getBannerCode($GLOBALS["WE_MAIN_DOC"]->ID, $paths, $target, $width, $height, $GLOBALS["WE_MAIN_DOC"]->DocType, $GLOBALS["WE_MAIN_DOC"]->Category, $bannername, $link, "", $bannerclick, $getbanner, "", $page, $GLOBALS["WE_MAIN_DOC"]->InWebEdition, $xml) :
				($type == "cookie" ?
					$noscript :
					we_html_element::jsElement('r = Math.random();document.write ("<" + "script type=\"text/javascript\"src=\"' . $getbanner . '?' . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . 'r="+r+"&amp;link=' . ($link ? 1 : 0) . '&amp;bannername=' . rawurlencode($bannername) . '&amp;type=js' . ($page ? ('&amp;page=' . rawurlencode($page)) : ('&amp;did=' . $GLOBALS["WE_MAIN_DOC"]->ID . '&amp;paths=' . rawurlencode($paths))) . '&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($bannerclick) . '&amp;height=' . rawurlencode($height) . '&amp;width=' . rawurlencode($width) . '"+(document.referer ? ("&amp;referer="+escape(document.referer)) : "")+"\"><" + "/script>");') . '<noscript>' . $noscript . '</noscript>'
				)
			);
	}
}

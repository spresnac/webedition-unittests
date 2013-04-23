<?php

/**
 * webEdition CMS
 *
 * $Rev: 5117 $
 * $Author: mokraemer $
 * $Date: 2012-11-10 17:18:00 +0100 (Sat, 10 Nov 2012) $
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
function we_parse_tag_captcha($attribs){
	eval('$attribs = ' . $attribs . ';');
	if(($foo = attributFehltError($attribs, 'width', __FUNCTION__)) ||
		($foo = attributFehltError($attribs, 'height', __FUNCTION__))){
		return $foo;
	}

	$width = weTag_getParserAttribute('width', $attribs, 100);
	$height = weTag_getParserAttribute('height', $attribs, 25);
	$path = weTag_getParserAttribute('path', $attribs, '/');

	$maxlength = weTag_getParserAttribute('maxlength', $attribs, 5);
	$type = weTag_getParserAttribute('type', $attribs, 'gif');

	$font = weTag_getParserAttribute('font', $attribs);
	$fontpath = weTag_getParserAttribute('fontpath', $attribs, '');
	$fontsize = weTag_getParserAttribute('fontsize', $attribs, '14');
	$fontcolor = weTag_getParserAttribute('fontcolor', $attribs, '#000000');

	$angle = weTag_getParserAttribute('angle', $attribs, '0');

	$subset = weTag_getParserAttribute('subset', $attribs, 'alphanum');
	$case = weTag_getParserAttribute('case', $attribs, 'mix');
	$skip = weTag_getParserAttribute('skip', $attribs, 'i,I,l,L,0,o,O,1,g,9');

	$valign = weTag_getParserAttribute('valign', $attribs, 'random');
	$align = weTag_getParserAttribute('align', $attribs, 'random');

	$bgcolor = weTag_getParserAttribute('bgcolor', $attribs, '#ffffff');
	$transparent = weTag_getParserAttribute('transparent', $attribs, false, true);

	$style = weTag_getParserAttribute('style', $attribs, '');
	$stylecolor = weTag_getParserAttribute('stylecolor', $attribs, '#cccccc');
	$stylenumber = weTag_getParserAttribute('stylenumber', $attribs, '5,10');

	// writing the temporary document
	$file = 'we_captcha_' . $GLOBALS['we_doc']->ID . ".php";
	$realPath = rtrim(realpath($_SERVER['DOCUMENT_ROOT'] . $path), '/') . '/' . $file;
	/* 	if(strpos($realPath, $_SERVER['DOCUMENT_ROOT']) === FALSE){
	  t_e('warning', 'Acess outside document_root forbidden!', $realPath);
	  } */


	$php = '<?php
require_once($_SERVER[\'DOCUMENT_ROOT\'].\'' . WE_INCLUDES_DIR . 'we.inc.php\');
$image = new CaptchaImage(' . $width . ', ' . $height . ', ' . $maxlength . ');' .
		($fontpath != '' ? '$image->setFontPath(\'' . $fontpath . '\');' : '') . '
$image->setFont(\'' . $font . '\', \'' . $fontsize . '\', \'' . $fontcolor . '\');
$image->setCharacterSubset(\'' . $subset . '\', \'' . $case . '\', \'' . $skip . '\');
$image->setAlign(\'' . $align . '\');
$image->setVerticalAlign(\'' . $valign . '\');
$image->setBackground(\'' . $bgcolor . '\'' . (isset($bgcolor) && $transparent ? ', true' : '') . ');' . '
$image->setStyle(\'' . $style . '\', \'' . $stylecolor . '\', \'' . $stylenumber . '\');
$image->setAngleRange(\'' . $angle . '\');
Captcha::display($image, \'' . ((isset($bgcolor) && $transparent) ? 'gif' : $type) . '\');';

	weFile::save($realPath, $php, 'w+');

	// clean attribs
	$attribs = removeAttribs($attribs, array(
		'path',
		'maxlength',
		'type',
		'font',
		'fontpath',
		'fontsize',
		'fontcolor',
		'angle',
		'subset',
		'case',
		'skip',
		'align',
		'valign',
		'bgcolor',
		'transparent',
		'style',
		'stylecolor',
		'stylenumber'
		));

	$attribs['src'] = rtrim($path, '/') . '/' . $file;
	return '<?php printElement(' . we_tag_tagParser::printTag('captcha', $attribs) . ');?>';
}

function we_tag_captcha($attribs){
	$attribs['src'] .= "?r=" . md5(md5(time()) . session_id());
	return getHtmlTag("img", $attribs);
}
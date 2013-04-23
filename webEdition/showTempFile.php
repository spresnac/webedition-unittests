<?php

/**
 * webEdition CMS
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

$filename = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['file'];
$mimetype = '';
if(file_exists($filename)){
	$isCompressed=weFile::isCompressed($filename);
	if(function_exists('finfo_open')){
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_buffer($finfo,weFile::loadPart($filename, 0, 8192, $isCompressed));
	} else{
		if(function_exists('getimagesizefromstring')){
			$mysize = getimagesizefromstring(weFile::load($filename, 0, 8192, $isCompressed));
			if(isset($mysize['mime'])){
				$mimetype = $mysize['mime'];
			}
		}
	}
	if($mimetype && $mimetype!='text/plain'){ //let the browser decide
		header('Content-Type: ' . $mimetype);
	}

	if($isCompressed){
		readgzfile($filename);
	}else{
		readfile($filename);
	}
}

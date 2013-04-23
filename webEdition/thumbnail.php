<?php

/**
 * webEdition CMS
 *
 * $Rev: 5321 $
 * $Author: mokraemer $
 * $Date: 2012-12-05 19:24:10 +0100 (Wed, 05 Dec 2012) $
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
//FIXME: send no perms img; but better an invalid picture, than access to unallowed images


if(!isset($_REQUEST['id']) || $_REQUEST['id'] == '' ||
	!isset($_REQUEST['path']) || $_REQUEST['path'] == '' ||
	!isset($_REQUEST['size']) || $_REQUEST['size'] == '' ||
	!isset($_REQUEST['extension']) || $_REQUEST['extension'] == ''){
	exit();
}

$imageId = $_REQUEST['id'];
$imagePath = $_REQUEST['path'];
$imageSizeW = $_REQUEST['size'];
$imageSizeH = (isset($_REQUEST['size2']) ? $_REQUEST['size2'] : $imageSizeW);


$whiteList = we_base_ContentTypes::inst()->getExtension('image/*');

if(!in_array(strtolower($_REQUEST['extension']), $whiteList)){
	exit();
}

$imageExt = substr($_REQUEST['extension'], 1, strlen($_REQUEST['extension']));

$thumbpath = we_image_edit::createPreviewThumb($imagePath, $imageId, $imageSizeW, $imageSizeH, substr($_REQUEST['extension'], 1));
header("Content-type: image/" . $imageExt);
readfile($_SERVER['DOCUMENT_ROOT'] . $thumbpath);

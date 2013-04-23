<?php

/**
 * webEdition CMS
 *
 * $Rev: 5661 $
 * $Author: mokraemer $
 * $Date: 2013-01-29 22:17:38 +0100 (Tue, 29 Jan 2013) $
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
/**
 * @abstract mapping for filetypes and implementations of metadata reader/writer
 * 			uses fileextensions for deciding, wich implementation class has to be used
 */
/**
 * @var array mapping array
 */
$dataTypeMapping = array(
	'jpe' => array('Exif', 'IPTC'), // iptc support is currently broken, will be fixed later
	'jpg' => array('Exif', 'IPTC'),
	'jpeg' => array('Exif', 'IPTC'),
	'wbmp' => array('Exif'),
	'pdf' => array('PDF'),
);

/**
 * @var mapping of image type constants (int) to file extensions (i.e. '1' => 'gif')
 * @link http://de.php.net/manual/de/function.exif-imagetype.php php reference manual
 */
$imageTypeMap = array(
	'', // image type 0 not defined
	'gif', // IMAGETYPE_GIF
	'jpg', // IMAGETYPE_JPEG
	'png', // IMAGETYPE_PNG
	'swf', // IMAGETYPE_SWF
	'psd', // IMAGETYPE_PSD
	'bmp', // IMAGETYPE_BMP
	'tif', // IMAGETYPE_TIFF_II intel-Bytefolge
	'tif', // IMAGETYPE_TIFF_MM motorola-Bytefolge
	'jpc', // IMAGETYPE_JPC
	'jp2', // IMAGETYPE_JP2
	'jpx', // IMAGETYPE_JPX
	'jb2', // IMAGETYPE_JB2
	'swc', // IMAGETYPE_SWC
	'iff', // IMAGETYPE_IFF
	'wbmp', // IMAGETYPE_WBMP
	'xbm', // IMAGETYPE_XBM
);

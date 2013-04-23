<?php

/**
 * webEdition CMS
 *
 * $Rev: 4085 $
 * $Author: mokraemer $
 * $Date: 2012-02-19 13:00:54 +0100 (Sun, 19 Feb 2012) $
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
we_html_tools::protect();


if(!isset($_REQUEST['csid'])){
	$yuiSuggest = & weSuggest::getInstance();

	$import_object = new we_import_files();

	print $import_object->getHTML();
}
<?php

/**
 * webEdition CMS
 *
 * $Rev: 4939 $
 * $Author: mokraemer $
 * $Date: 2012-09-04 23:14:06 +0200 (Tue, 04 Sep 2012) $
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

$what = isset($_REQUEST['pnt']) ? $_REQUEST['pnt'] : 'frameset';

$weFrame = new weSideBarFrames();
$weFrame->getHTML($what);

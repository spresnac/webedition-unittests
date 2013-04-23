<?php
/**
 * webEdition CMS
 *
 * $Rev: 4981 $
 * $Author: lukasimhof $
 * $Date: 2012-10-10 12:08:17 +0200 (Wed, 10 Oct 2012) $
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

// Live Update Server
define("LIVEUPDATE_SERVER", "update.webedition.org");

// Live Update Server Script
define("LIVEUPDATE_SERVER_SCRIPT", "/we5/snippets.p" . "hp");

// Css
define("LIVEUPDATE_CSS", "");

// Temp Dir for downloaded files
define("LIVEUPDATE_CLIENT_DOCUMENT_DIR", $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . "liveUpdate");

?>
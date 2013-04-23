<?php
/**
 * webEdition CMS
 *
 * $Rev: 3999 $
 * $Author: mokraemer $
 * $Date: 2012-02-12 17:26:23 +0100 (Sun, 12 Feb 2012) $
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

define('RPC_DIR', str_replace("\\", "/",dirname(__FILE__)) . '/');
define('RPC_URL', str_replace($_SERVER['DOCUMENT_ROOT'],'',RPC_DIR));

ini_set('include_path',	ini_get('include_path') . PATH_SEPARATOR . RPC_DIR);

//define('NO_SESS',1);
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

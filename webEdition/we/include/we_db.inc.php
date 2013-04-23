<?php
/**
 * webEdition CMS
 *
 * $Rev: 4375 $
 * $Author: mokraemer $
 * $Date: 2012-03-30 00:01:55 +0200 (Fri, 30 Mar 2012) $
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

/* Create a global instance of the concrete DB-Class (mysql/mysqli) */

//FIXME: remove in 6.4

if(!isset($GLOBALS['DB_WE'])){
	$GLOBALS['DB_WE'] = new DB_WE();
}

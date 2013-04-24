<?php
/**
 * webEdition CMS
 *
 * $Rev: 3574 $
 * $Author: mokraemer $
 * $Date: 2011-12-14 16:02:25 +0100 (Wed, 14 Dec 2011) $
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
 * @package    webEdition_cms
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

// include autoload function
include_once('../lib/we/core/autoload.php');

// get controller instabce
$controller = Zend_Controller_Front::getInstance();

// set path fpr controller directory
$controller->setControllerDirectory('./controllers');

// disables automatic view rendering
$controller->setParam('noViewRenderer', true);

$controller->throwExceptions(true);

// alerts a message and exits when a user is not logged in or when the session is expired
we_core_Permissions::protect();

// run!
$controller->dispatch();

/**
 * webEdition CMS
 *
 * $Rev: 4944 $
 * $Author: lukasimhof $
 * $Date: 2012-09-06 11:49:41 +0200 (Thu, 06 Sep 2012) $
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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
 
// include autoload function
include_once('../../lib/we/core/autoload.php');

// include configuration
include_once('<?php print $TOOLNAME;?>/conf/meta.conf.php');

// get controller instabce
$controller = Zend_Controller_Front::getInstance();

// set path fpr controller directory
$controller->setControllerDirectory('./controllers');

// turn on exceptions
$controller->throwExceptions(true); // should be turned off in production server 

// disables automatic view rendering
$controller->setParam('noViewRenderer', true);

// set some app specific parameter
$controller->setParam('appDir',dirname($_SERVER['SCRIPT_NAME']));
$controller->setParam('appPath',dirname($_SERVER['SCRIPT_FILENAME']));
$controller->setParam('appName', '<?php print $TOOLNAME;?>');

// alerts a message and exits when a user is not logged in or when the session is expired
we_core_Permissions::protect();

// run!
$controller->dispatch();
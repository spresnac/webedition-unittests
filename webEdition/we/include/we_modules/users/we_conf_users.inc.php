<?php

/**
 * webEdition CMS
 *
 * $Rev: 4319 $
 * $Author: mokraemer $
 * $Date: 2012-03-22 19:22:48 +0100 (Thu, 22 Mar 2012) $
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
define('USER_TABLE', TBL_PREFIX . 'tblUser');
define('LOCK_TABLE', TBL_PREFIX . 'tblLock');

define('PING_TIME', 30); // 30 sec
define('PING_TOLERANZ', 3 * PING_TIME); // 40 sec - allows 1 Ping missing

define('WE_USERS_MODULE_PATH', WE_MODULES_PATH . 'users/');
define('WE_USERS_MODULE_DIR', WE_MODULES_DIR . 'users/');

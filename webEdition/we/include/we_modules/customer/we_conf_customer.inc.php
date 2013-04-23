<?php

/**
 * webEdition CMS
 *
 * $Rev: 4696 $
 * $Author: arminschulz $
 * $Date: 2012-07-14 06:46:35 +0200 (Sat, 14 Jul 2012) $
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
define('CUSTOMER_TABLE', TBL_PREFIX . 'tblWebUser');
define('CUSTOMER_ADMIN_TABLE', TBL_PREFIX . 'tblWebAdmin');
define('CUSTOMER_FILTER_TABLE', TBL_PREFIX . 'tblcustomerfilter');
define('CUSTOMER_AUTOLOGIN_TABLE', TBL_PREFIX . 'tblWebUserAutoLogin');
define('CUSTOMER_SESSION_TABLE', TBL_PREFIX . 'tblWebUserSessions');
define('CUSTOMER_AUTOLOGIN_LIFETIME',  '31536000');
define('CUSTOMER_SESSION_LIFETIME',  '300');
define('WE_CUSTOMER_MODULE_PATH', WE_MODULES_PATH . 'customer/');
define('WE_CUSTOMER_MODULE_DIR', WE_MODULES_DIR . 'customer/');

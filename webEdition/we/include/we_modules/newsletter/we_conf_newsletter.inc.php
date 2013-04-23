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
define("NEWSLETTER_TABLE", TBL_PREFIX . "tblNewsletter");
define("NEWSLETTER_BLOCK_TABLE", TBL_PREFIX . "tblNewsletterBlock");
define("NEWSLETTER_CONFIRM_TABLE", TBL_PREFIX . "tblNewsletterConfirm");
define("NEWSLETTER_GROUP_TABLE", TBL_PREFIX . "tblNewsletterGroup");
define("NEWSLETTER_LOG_TABLE", TBL_PREFIX . "tblNewsletterLog");
define("NEWSLETTER_PREFS_TABLE", TBL_PREFIX . "tblNewsletterPrefs");

define("WE_NEWSLETTER_MODULE_DIR", WE_MODULES_DIR . "newsletter/");
define("WE_NEWSLETTER_CACHE_DIR", $_SERVER['DOCUMENT_ROOT'] . WE_NEWSLETTER_MODULE_DIR . '/cache/');

<?php

/**
 * webEdition CMS
 *
 * $Rev: 4219 $
 * $Author: mokraemer $
 * $Date: 2012-03-08 17:13:51 +0100 (Thu, 08 Mar 2012) $
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
define("SCHEDULE_TABLE", TBL_PREFIX . "tblSchedule");
define('SCHEDULER_TRIGGER_PREDOC', 0);
define('SCHEDULER_TRIGGER_POSTDOC', 1);
define('SCHEDULER_TRIGGER_CRON', 2);
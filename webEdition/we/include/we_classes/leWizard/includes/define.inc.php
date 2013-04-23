<?php
/**
 * webEdition CMS
 *
 * $Rev: 2633 $
 * $Author: mokraemer $
 * $Date: 2011-03-08 01:16:50 +0100 (Tue, 08 Mar 2011) $
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

// call next step
define("LE_WIZARDSTEP_NEXT", 1);

// repeat step, when an error occured
define("LE_WIZARDSTEP_ERROR", 2);

// exit installer, no more action possible
define("LE_WIZARDSTEP_FATAL_ERROR", 3);

// repeat step - download files
define("LE_WIZARDSTEP_ITERATE", 4);

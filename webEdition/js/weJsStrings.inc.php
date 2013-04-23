<?php

/**
 * webEdition CMS
 *
 * $Rev: 4197 $
 * $Author: mokraemer $
 * $Date: 2012-03-05 19:49:05 +0100 (Mon, 05 Mar 2012) $
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

print we_html_element::jsElement('
var we_string_message_reporting_notice = "' . g_l('alert', '[notice]') . '";
var we_string_message_reporting_warning = "' . g_l('alert', '[warning]') . '";
var we_string_message_reporting_error = "' . g_l('alert', '[error]') . '";
');
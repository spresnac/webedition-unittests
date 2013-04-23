<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
define('CSS_DIR', '/webEdition/css/');
define('SCRIPT_BUTTONS_ONLY', we_html_element::jsScript(JS_DIR . 'weButton.js'));
define('STYLESHEET_BUTTONS_ONLY', we_html_element::cssLink(CSS_DIR . 'we_button.css'));
define('STYLESHEET_SCRIPT', we_html_element::cssLink(CSS_DIR . 'global.php') . STYLESHEET_BUTTONS_ONLY);
define('STYLESHEET', STYLESHEET_SCRIPT . SCRIPT_BUTTONS_ONLY);

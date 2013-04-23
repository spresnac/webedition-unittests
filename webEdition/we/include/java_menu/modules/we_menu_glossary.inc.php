<?php

/**
 * webEdition CMS
 *
 * $Rev: 5046 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 22:54:01 +0100 (Thu, 01 Nov 2012) $
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
 * @package    webEdition_javamenu
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
// File > Glossary Check
$we_menu['1099000'] = array(
	'text' => g_l('javaMenu_glossary', '[glossary_check]'),
	'parent' => '1000000',
	'cmd' => 'check_glossary',
	'perm' => '',
	'enabled' => '1',
);
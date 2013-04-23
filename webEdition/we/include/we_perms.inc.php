<?php

/**
 * webEdition CMS
 *
 * $Rev: 4373 $
 * $Author: mokraemer $
 * $Date: 2012-03-29 18:52:41 +0200 (Thu, 29 Mar 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

//FIXME: remove in 6.4
function protect(){
	t_e('deprecated', 'old protect called! remove this!');
	we_html_tools::protect();
}
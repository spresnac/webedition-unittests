<?php

/**
 * webEdition CMS
 *
 * $Rev: 4058 $
 * $Author: mokraemer $
 * $Date: 2012-02-16 19:20:06 +0100 (Thu, 16 Feb 2012) $
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
switch($_REQUEST['we_cmd'][0]){
	case 'add_schedule':
	case 'del_schedule':
	case 'add_schedcat':
	case 'delete_all_schedcats':
	case 'delete_schedcat':
		$INCLUDE = 'we_editors/we_editor.inc.php';
		break;
}

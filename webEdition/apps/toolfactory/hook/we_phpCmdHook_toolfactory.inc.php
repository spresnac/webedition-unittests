<?php

/**
 * webEdition CMS
 *
 * $Rev: 5014 $
 * $Author: mokraemer $
 * $Date: 2012-10-24 22:08:13 +0200 (Wed, 24 Oct 2012) $
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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
switch($_REQUEST['we_cmd'][0]){
	case 'tool_toolfactory_edit':
		include(WE_INCLUDES_PATH . 'we_tools/tools_frameset.php');
		break;
}

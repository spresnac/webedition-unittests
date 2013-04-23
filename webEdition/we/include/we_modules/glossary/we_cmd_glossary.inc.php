<?php

/**
 * webEdition CMS
 *
 * $Rev: 3915 $
 * $Author: mokraemer $
 * $Date: 2012-01-30 17:34:27 +0100 (Mon, 30 Jan 2012) $
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

	case 'edit_glossary_acronym':
	case 'edit_glossary_abbreviation':
	case 'edit_glossary_foreignword':
	case 'edit_glossary_link':
	case 'edit_glossary_textreplacement':
	case 'edit_glossary_ifthere':
	case 'view_folder':
	case 'view_type':
	case 'view_exception':
		$mod = 'glossary';
		$INCLUDE = 'we_modules/show_frameset.php';
		break;

	case 'glossary_settings':
		$INCLUDE = 'we_modules/glossary/edit_glossary_settings_frameset.php';
		break;

	case 'glossary_dictionaries':
		$INCLUDE = 'we_modules/glossary/edit_glossary_dictionaries_frameset.php';
		break;

	case 'check_glossary':
		$mod = 'glossary';
		$INCLUDE = 'we_modules/glossary/add_items.inc.php';
		break;
}

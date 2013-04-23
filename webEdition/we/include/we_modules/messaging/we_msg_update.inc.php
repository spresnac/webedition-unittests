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
/* clipboard object class */
class we_msg_update extends we_class{
	/* Flag which is set when the file is not new */

	var $userid = -1;
	var $username = '';

	function __construct(){
		parent::__construct();
		$this->Name = 'msg_clipboard_' . md5(uniqid(__FUNCTION__, true));
		$this->persistent_slots = array('ClassName', 'Name', 'ID', 'Folder_ID', 'selected_message', 'selected_set', 'sort_order', 'last_sortfield', 'search_ids', 'available_folders', 'search_folders', 'search_fields');
	}

}


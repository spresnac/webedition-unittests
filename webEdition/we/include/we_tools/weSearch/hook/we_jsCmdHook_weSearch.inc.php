<?php
/**
 * webEdition CMS
 *
 * $Rev: 5044 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 17:59:55 +0100 (Thu, 01 Nov 2012) $
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

include (WE_INCLUDES_PATH . 'we_tools/weSearch/conf/meta.conf.php');

?>
    case "tool_weSearch_edit":
		new jsWindow(url,"tool_window_weSearch",-1,-1,970,760,true,true,true,true);
	break;
	case "tool_weSearch_new_forDocuments":
    case "tool_weSearch_new_forTemplates":
    case "tool_weSearch_new_forObjects":
    case "tool_weSearch_new_advSearch":
	case "tool_weSearch_delete":
	case "tool_weSearch_save":
	case "tool_weSearch_exit":
		var fo=false;
					if(jsWindow_count){
		for(var k=jsWindow_count-1;k>-1;k--){
			eval("if(jsWindow"+k+"Object.ref=='tool_window_weSearch'){ jsWindow"+k+"Object.wind.we_cmd('"+arguments[0]+"');fo=true;wind=jsWindow"+k+"Object.wind}");
			if(fo) break;
		}
		wind.focus();
		}
	break;
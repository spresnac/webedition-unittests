
switch($_REQUEST['we_cmd'][0]){
	case 'tool_<?php print $TOOLNAME; ?>_edit':
		include(WE_INCLUDES_PATH.'we_tools/tools_frameset.php');
	break;
}
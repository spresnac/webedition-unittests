<?php
/**
 * webEdition CMS
 *
 * $Rev: 5084 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 20:32:30 +0100 (Tue, 06 Nov 2012) $
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
we_html_tools::protect();
we_html_tools::htmlTop('command-bridge', '', 5);
?>
<script  type="text/javascript">
	// bugfix WE-356
	self.focus();
<?php
if(isset($_REQUEST['wecmd0'])){ // when calling from applet (we can not call directly we_cmd[0] with the applet =>  Safari OSX doesn't support live connect)
	$_REQUEST['we_cmd'][0] = $_REQUEST['wecmd0'];
}
foreach($_REQUEST['we_cmd'] as &$cmdvalue){
	$cmdvalue = preg_replace('/[^a-z0-9_-]/i', '', strip_tags($cmdvalue));
}

switch($_REQUEST['we_cmd'][0]){
	case "trigger_save_document":
		print 'if(top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames[3] && top.weEditorFrameController.getActiveDocumentReference().frames[3].weCanSave){
	top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(false);
	top.weEditorFrameController.getActiveDocumentReference().frames[3].we_save_document();
}else{
	' . we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_save]"), we_message_reporting::WE_MESSAGE_ERROR) . '
}
';
		break;

	case "trigger_publish_document":
		print 'if(top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().frames[3] && top.weEditorFrameController.getActiveDocumentReference().frames[3].weCanSave){
	top.weEditorFrameController.getActiveEditorFrame().setEditorPublishWhenSave(true);
	top.weEditorFrameController.getActiveDocumentReference().frames[3].we_save_document();
}else{
	' . we_message_reporting::getShowMessageCall(g_l('alert', "[nothing_to_publish]"), we_message_reporting::WE_MESSAGE_ERROR) . '
}
';
		break;
	case "new_webEditionPage":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","text/webedition");' . "\n";
		break;
	case "new_image":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","image/*");' . "\n";
		break;
	case "new_html_page":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","text/html");' . "\n";
		break;
	case "new_flash_movie":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","application/x-shockwave-flash");' . "\n";
		break;
	case "new_quicktime_movie":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","video/quicktime");' . "\n";
		break;
	case "new_javascript":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","text/js");' . "\n";
		break;
	case "new_text_plain":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","text/plain");' . "\n";
		break;
	case "new_text_xml":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","text/xml");' . "\n";
		break;
	case "new_text_htaccess":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","text/htaccess");' . "\n";
		break;
	case "new_css_stylesheet":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","text/css");' . "\n";
		break;
	case "new_binary_document":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","application/*");' . "\n";
		break;
	case "new_template":
		print 'top.we_cmd("new","' . TEMPLATES_TABLE . '","","text/weTmpl");' . "\n";
		break;
	case "new_document_folder":
		print 'top.we_cmd("new","' . FILE_TABLE . '","","folder");' . "\n";
		break;
	case "new_template_folder":
		print 'top.we_cmd("new","' . TEMPLATES_TABLE . '","","folder");' . "\n";
		break;
	case "delete_documents":
		print 'top.we_cmd("del",1,"' . FILE_TABLE . '");' . "\n";
		break;
	case "delete_templates":
		print 'top.we_cmd("del",1,"' . TEMPLATES_TABLE . '");' . "\n";
		break;
	case "delete_documents_cache":
		print 'top.we_cmd("del",1,"' . FILE_TABLE . '_cache");' . "\n";
		break;
	case "move_documents":
		print 'top.we_cmd("mv",1,"' . FILE_TABLE . '");' . "\n";
		break;
	case "move_templates":
		print 'top.we_cmd("mv",1,"' . TEMPLATES_TABLE . '");' . "\n";
		break;

	case "openDelSelector":
		$openTable = FILE_TABLE;
		if(isset($_SESSION['weS']['seemForOpenDelSelector']['Table'])){
			$openTable = $_SESSION['weS']['seemForOpenDelSelector']['Table'];
			unset($_SESSION['weS']['seemForOpenDelSelector']['Table']);
		}
		$_cmd = 'top.we_cmd("openDelSelector","","' . $openTable . '","","","","","","",1);';
		print "setTimeout('$_cmd',50)";
		break;

	case "export_documents":
		$_tbl = FILE_TABLE;
	case "export_templates":
		$_tbl = (!isset($_tbl) ? $_tbl : TEMPLATES_TABLE);
	case "export_objects":
		$_tbl = (!isset($_tbl) ? $_tbl : OBJECT_FILES_TABLE);



	default:
		$regs = array();
		if(preg_match('/^new_dtPage(.+)$/', $_REQUEST['we_cmd'][0], $regs)){
			$dt = $regs[1];
			print 'top.we_cmd("new","' . FILE_TABLE . '","","text/webedition","' . $dt . '");';
			break;
		} else if(preg_match('/^new_ClObjectFile(.+)$/', $_REQUEST['we_cmd'][0], $regs)){
			$clID = $regs[1];
			print 'top.we_cmd("new","' . OBJECT_FILES_TABLE . '","","objectFile","' . $clID . '");';
			break;
		}
		$arr = array();
		foreach($_REQUEST['we_cmd'] as $cur){
			$arr[] = '\'' . str_replace(array('\'', '"'), array('\\\'', '\\"'), $cur) . '\'';
		}
		print 'setTimeout("top.we_cmd(' . implode(',', $arr) . ')",50);';
}
?>
</script>
</head><body></body></html>
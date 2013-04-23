<?php
/**
 * webEdition CMS
 *
 * $Rev: 5748 $
 * $Author: mokraemer $
 * $Date: 2013-02-07 11:06:27 +0100 (Thu, 07 Feb 2013) $
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

we_html_tools::htmlTop();

print STYLESHEET;

if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == "save_last"){
	$_SESSION["user"]["LastDir"] = $last;
}
if(!isset($_REQUEST["cmd"]) || (isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] != "save_last")){
	?>
	<script type="text/javascript"><!--

		function drawNewFolder() {
			for(var i=0; i<top.allentries.length;i++){
				if(elem = top.fsbody.document.getElementById(top.allentries[i])){
					elem.style.backgroundColor = 'white';
				}
			}
			drawDir(top.currentDir,"new_folder");
		}

		function setFilter(filter) {
			top.currentFilter=filter;
			drawDir(top.currentDir);
		}

		function setDir(dir) {
			var a=top.fsheader.document.forms["we_form"].elements["lookin"].options;
			if(a.length-2>-1) {
				for(j=0;j<a.length;j++) {
					if(a[j].value==dir) {
						a.length=j+1;a[j].selected=true;
					}
				}
	<?php if(isset($_REQUEST["filter"]) && ($_REQUEST["filter"] == "folder" || $_REQUEST["filter"] == "filefolder")){ ?>
					selectFile(dir);
	<?php } ?>
				top.currentDir=dir;
				selectDir();
			}
			else {
	<?php print we_message_reporting::getShowMessageCall(g_l('fileselector', "[already_root]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
			}
		}

		function goUp() {
			var a=top.fsheader.document.forms["we_form"].elements["lookin"].options;
			if(a.length-2>-1)
				setDir(a[a.length-2].value);
			else
	<?php print we_message_reporting::getShowMessageCall(g_l('fileselector', "[already_root]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
			}

			function selectFile(fid) {
				if(fid != "/") {
					top.currentID=top.sitepath+top.rootDir+top.currentDir+((top.currentDir != "/") ? "/" : "")+fid;
					top.currentName=fid;
					top.fsfooter.document.forms["we_form"].elements["fname"].value=fid;
					if(top.fsbody.document.getElementById(fid)) {
						for(var i=0; i<top.allentries.length;i++){
							if(top.fsbody.document.getElementById(top.allentries[i])) top.fsbody.document.getElementById(top.allentries[i]).style.backgroundColor = 'white';
						}
						top.fsbody.document.getElementById(fid).style.backgroundColor = '#DFE9F5';
					}
				} else {
					top.currentID=top.sitepath;
					top.currentName=fid;
					top.fsfooter.document.forms["we_form"].elements["fname"].value=fid;
					if(top.fsbody.document.getElementById(fid)) {
						for(var i=0; i<top.allentries.length;i++){
							if(top.fsbody.document.getElementById(top.allentries[i])) top.fsbody.document.getElementById(top.allentries[i]).style.backgroundColor = 'white';
						}
						top.fsbody.document.getElementById(fid).style.backgroundColor = '#DFE9F5';
					}
				}
			}

			function selectDir() {
				if(arguments[0]) {
	<?php if(isset($_REQUEST["filter"]) && $_REQUEST["filter"] == "folder"){ ?>
						//selectFile(arguments[0],true);
	<?php } ?>
					if(top.currentDir=="/")
						top.currentDir=top.currentDir+arguments[0];
					else
						top.currentDir=top.currentDir+"/"+arguments[0];
					top.fsheader.addOption(arguments[0],top.currentDir);
				}

				if (top.currentDir.substring(0,12) == "<?php echo WEBEDITION_DIR; ?>" || top.currentDir=="<?php echo rtrim(WEBEDITION_DIR, '/'); ?>") {
					top.fsheader.weButton.disable("btn_new_dir_ss");
					top.fsheader.weButton.disable("btn_add_file_ss");
					top.fsheader.weButton.disable("btn_function_trash_ss");
				} else {
					top.fsheader.weButton.enable("btn_new_dir_ss");
					top.fsheader.weButton.enable("btn_add_file_ss");
					top.fsheader.weButton.enable("btn_function_trash_ss");
				}

				drawDir(top.currentDir);

			}

			function reorderDir(dir,order) {
				setTimeout('top.fsbody.location="we_sselector_body.php?dir='+dir+'&ord='+order+'&file='+top.currentFilter+'&curID='+escape(top.currentID)+'"',100);
			}

			function drawDir(dir) {
				switch(arguments[1]){
					case "new_folder":
						top.fsbody.location="we_sselector_body.php?dir="+escape(top.rootDir+dir)+"&nf=new_folder&file="+top.currentFilter+"&curID="+escape(top.currentID);
						break;
					case "rename_folder":
						if(arguments[2]) {
							top.fsbody.location="we_sselector_body.php?dir="+escape(top.rootDir+dir)+"&nf=rename_folder&sid="+escape(arguments[2])+"&file="+top.currentFilter+"&curID="+escape(top.currentID);
						}
						break;
					case "rename_file":
						if(arguments[2]) {
							top.fsbody.location="we_sselector_body.php?dir="+escape(top.rootDir+dir)+"&nf=rename_file&sid="+escape(arguments[2])+"&file="+top.currentFilter+"&curID="+escape(top.currentID);
						}
						break;
					default:
						setTimeout('top.fsbody.location="we_sselector_body.php?dir='+escape(top.rootDir+dir)+'&file='+top.currentFilter+'&curID='+escape(top.currentID)+'"',100);
				}
			}

			function delFile() {
				if((top.currentID!="")&&(top.fsfooter.document.forms["we_form"].elements["fname"].value!="")){
					top.fscmd.location="we_sselector_cmd.php?cmd=delete_file&fid="+top.currentID+"&ask="+arguments[0];
				}else{
	<?php print we_message_reporting::getShowMessageCall(g_l('fileselector', "[edit_file_nok]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
				}
			}

	<?php

	function delDir($dir){
		$d = dir($dir);
		while(false !== ($entry = $d->read())) {
			if($entry != "." && $entry != ".."){
				if(is_dir($dir . "/" . $entry)){
					delDir($dir . "/" . $entry);
				} else if(is_file($dir . "/" . $entry)){
					if(!@unlink($dir . "/" . $entry))
						print we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_file]"), $entry), we_message_reporting::WE_MESSAGE_ERROR);
				}
				else
					print we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_noexist]"), $entry), we_message_reporting::WE_MESSAGE_ERROR);
			}
		}
		if(!@rmdir($dir)){
			print we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_folder]"), $dir), we_message_reporting::WE_MESSAGE_ERROR);
		}
	}

	if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == "new_folder"){
		print 'drawDir(top.currentDir);';
		if($_REQUEST["txt"] == ""){
			print we_message_reporting::getShowMessageCall(g_l('alert', "[we_filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else if(preg_match('|[\'"<>/]|', $_REQUEST["txt"])){
			print we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"] . "/" . $_REQUEST["txt"]);
			if(!@is_dir($path)){
				$oldumask = @umask(0000);

				$mod = octdec(intval(WE_NEW_FOLDER_MOD));

				if(!we_util_File::createLocalFolder($path)){
					print we_message_reporting::getShowMessageCall(g_l('alert', "[create_folder_nok]"), we_message_reporting::WE_MESSAGE_ERROR);
				} else{
					print 'selectFile("' . $_REQUEST["txt"] . '");top.currentID="' . $path . '";';
				}
				@umask($oldumask);
			} else{
				$we_responseText = sprintf(g_l('alert', "[path_exists]"), str_replace($_SERVER['DOCUMENT_ROOT'], '', $path));
				print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR) . "\n";
			}
		}
	}

	if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == "rename_folder"){
		if($_REQUEST["txt"] == ""){
			print we_message_reporting::getShowMessageCall(g_l('alert', "[we_filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR).
				"drawDir(top.currentDir);\n";
		} else if(preg_match('|[\'"<>/]|', $_REQUEST["txt"])){
			print we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR).
				"drawDir(top.currentDir);\n";
		} else{
			$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"] . "/" . $_REQUEST["sid"]);
			$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"] . "/" . $_REQUEST["txt"]);
			if($old != $new){
				if(!@is_dir($new)){
					if(!rename($old, $new)){
						print we_message_reporting::getShowMessageCall(g_l('alert', "[rename_folder_nok]"), we_message_reporting::WE_MESSAGE_ERROR);
					} else{
						print 'selectFile("' . $_REQUEST["txt"] . '");' . "\n";
					}
				} else{
					$we_responseText = sprintf(g_l('alert', "[path_exists]"), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new));
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
			print "drawDir(top.currentDir);\n";
		}
	} else if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == "rename_file"){
		if($_REQUEST["txt"] == ""){
			print we_message_reporting::getShowMessageCall(g_l('alert', "[we_filename_empty]"), we_message_reporting::WE_MESSAGE_ERROR).
				"drawDir(top.currentDir);\n";
		} else if(preg_match('|[\'"<>/]|', $_REQUEST["txt"])){
			print we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR).
				"drawDir(top.currentDir);\n";
		} else{
			$old = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"] . '/' . $_REQUEST["sid"]);
			$new = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["pat"] . '/' . $_REQUEST["txt"]);
			if($old != $new){
				if(!@file_exists($new)){
					if(!rename($old, $new)){
						print we_message_reporting::getShowMessageCall(g_l('alert', "[rename_file_nok]"), we_message_reporting::WE_MESSAGE_ERROR);
					} else{
						print 'selectFile("' . $_REQUEST["txt"] . '");' . "\n";
					}
				} else{
					$we_responseText = sprintf(g_l('alert', "[path_exists]"), str_replace($_SERVER['DOCUMENT_ROOT'], '', $new));
					print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_ERROR);
				}
			}
			print "drawDir(top.currentDir);selectFile(top.currentName);\n";
		}
	} else if(isset($_REQUEST["cmd"]) && $_REQUEST["cmd"] == "delete_file"){
		if(isset($_REQUEST["fid"])){
			$foo = f("SELECT ID FROM " . FILE_TABLE . " WHERE Path='" . $DB_WE->escape($_REQUEST["fid"]) . "'", 'ID', $DB_WE);
			if(preg_match('|' . WEBEDITION_PATH . '|', $_REQUEST["fid"]) || ($_REQUEST["fid"] == rtrim(WEBEDITION_PATH, '/')) || strpos("..", $_REQUEST["fid"]) || $foo || $_REQUEST["fid"] == $_SERVER['DOCUMENT_ROOT'] || $_REQUEST["fid"] . "/" == $_SERVER['DOCUMENT_ROOT']){
				print we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR);
			} else{
				if(is_dir($_REQUEST["fid"]) && ($_REQUEST["ask"])){
					print "if (confirm(\"" . g_l('alert', "[delete_folder]") . "\")){delFile(0);}";
				} else if(is_file($_REQUEST["fid"]) && ($_REQUEST["ask"])){
					print "if (confirm(\"" . g_l('alert', "[delete]") . "\")){delFile(0);}";
				} else if(is_dir($_REQUEST["fid"])){
					delDir($_REQUEST["fid"]);
				} else if(!@unlink($_REQUEST["fid"])){
					print we_message_reporting::getShowMessageCall(sprintf(g_l('alert', "[delete_nok_error]"), $_REQUEST["fid"]), we_message_reporting::WE_MESSAGE_ERROR);
				}
				print "selectFile('');drawDir(top.currentDir);";
			}
		}
	}
	?>
			//-->
	</script>
<?php } ?>
</head>

<body>
</body>
</html>

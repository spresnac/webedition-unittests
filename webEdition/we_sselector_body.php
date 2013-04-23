<?php
/**
 * webEdition CMS
 *
 * $Rev: 5924 $
 * $Author: mokraemer $
 * $Date: 2013-03-06 21:02:50 +0100 (Wed, 06 Mar 2013) $
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

we_html_tools::protect(array('BROWSE_SERVER', 'ADMINISTRATOR'));

$supportDebuggingFile = WEBEDITION_PATH . 'we_sselector_inc.php';
$supportDebugging = false;
if(file_exists($supportDebuggingFile)){

	include($supportDebuggingFile);
	if(defined('SUPPORT_IP') && defined('SUPPORT_DURATION') && defined('SUPPORT_START')){
		if(SUPPORT_IP == $_SERVER['REMOTE_ADDR'] && (time() - SUPPORT_DURATION) < SUPPORT_START){
			$supportDebugging = true;
		}
	}
}

we_html_tools::htmlTop();
print STYLESHEET;

function _cutText($text, $l){
	if(strlen($text) > $l){
		return substr($text, 0, $l - 8) . '...' . substr($text, strlen($text) - 5, 5);
	} else{
		return $text;
	}
}
?>
<script type="text/javascript"><!--
	var clickCount=0;
	var wasdblclick=0;
	var tout=null;
	var mk=null;
	var old=0;

	function doClick(id,ct,indb){
    if(ct==1){
			if(wasdblclick){
				top.fscmd.selectDir(id);
				if(top.filter != "folder" && top.filter !="filefolder") top.fscmd.selectFile("");
				setTimeout('wasdblclick=0;',400);
			}else{
				if((top.filter == "folder" || top.filter =="filefolder") && (!indb)){
         	top.fscmd.selectFile(id);
				}
			}
			if((old==id)&&(!wasdblclick)){
				clickEdit(id);
			}
    }
    else{
			top.fscmd.selectFile(id);
			top.dirsel=0;
    }
    old=id;
	}

	function doSelectFolder(entry,indb){
		switch(top.filter){
		case "all_Types":
			if(!top.browseServer){
				break;
			}
			//no break;
		case "folder":
		case "filefolder":
			if(!indb){
				top.fscmd.selectFile(entry);
			}
			top.dirsel=1;
		}
	}

	function clickEdit(dir){
		switch(top.filter){
		case "folder":
		case "filefolder":
			break;
		default:
			setScrollTo();
			top.fscmd.drawDir(top.currentDir,"rename_folder",dir);
		}
	}

	function clickEditFile(file){
		setScrollTo();
		top.fscmd.drawDir(top.currentDir,"rename_file",file);
	}

	function doScrollTo(){
		if(parent.scrollToVal){
			window.scrollTo(0,parent.scrollToVal);
			parent.scrollToVal=0;
		}
	}

	function setScrollTo(){
		parent.scrollToVal=<?php if(we_base_browserDetect::isIE()){?>document.body.scrollTop;<?php } else{ ?>pageYOffset;<?php } ?>
	}

	function keypressed(e) {
		if (e.keyCode == 13) { // RETURN KEY => valid for all Browsers
			setTimeout('document.we_form.txt.blur()',30);
			//document.we_form
		}
	}

	//-->
</script>

</head>
<body bgcolor="white" LINK="#000000" ALINK="#000000" VLINK="#000000" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" onLoad="doScrollTo();">
	<form name="we_form" target="fscmd" action="we_sselector_cmd.php" method="post" onSubmit="return false;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">

			<?php

			function getDataType($dat){
				$ct = getContentTypeFromFile($dat);
				if(g_l('contentTypes', '[' . $ct . ']') !== false){
					return g_l('contentTypes', '[' . $ct . ']');
				}
				return "";
			}

			$arDir = array();
			$arFile = array();
			$ordDir = array();
			$ordFile = array();
			$final = array();

			if($_REQUEST["dir"] == ""){
				$org = "/";
			} else{
				$org = $_REQUEST["dir"];
			}

			$dir = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST["dir"];
			if($dir != "/")
				$dir = rtrim($dir, '/');
			if(!isset($_REQUEST["ord"]))
				$_REQUEST["ord"] = 10;
			@chdir($dir);
			$dir_obj = @dir($dir);

			if($dir_obj){
				while(false !== ($entry = $dir_obj->read())) {
					if($entry != '.' && $entry != '..'){
						if(is_dir($dir . '/' . $entry)){
							$arDir[] = $entry;
							switch($_REQUEST["ord"]){
								case 10:
								case 11:
									$ordDir[] = $entry;
									break;
								case 20:
								case 21:
									$ordDir[]= getDataType($dir . '/' . $entry);
									break;
								case 30:
								case 31:
									$ordDir[]= filectime($dir . "/" . $entry);
									break;
								case 40:
								case 41:
									$ordDir[]= filesize($dir . "/" . $entry);
									break;
							}
						} else{
							$arFile[]= $entry;
							switch($_REQUEST["ord"]){
								case 10:
								case 11:
									$ordFile[]= $entry;
									break;
								case 20:
								case 21:
									$ordFile[]= getDataType($dir . "/" . $entry);
									break;
								case 30:
								case 31:
									$ordFile[]= filectime($dir . "/" . $entry);
									break;
								case 40:
								case 41:
									$ordFile[]= filesize($dir . "/" . $entry);
									break;
							}
						}
					}
				}
				$dir_obj->close();
			} else{
				print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[access_denied]"), we_message_reporting::WE_MESSAGE_ERROR)) . '<br><br><div class="middlefontgray" align="center">-- ' . g_l('alert', "[access_denied]") . ' --</div>';
			}

			switch($_REQUEST["ord"]){
				case 10:
				case 20:
				case 30:
				case 40:
					asort($ordDir);
					asort($ordFile);
					break;
				case 11:
				case 21:
				case 31:
				case 41:
					arsort($ordDir);
					arsort($ordFile);
					break;
			}



			foreach($ordDir as $key => $value){
				array_push($final, $arDir[$key]);
			}
			foreach($ordFile as $key => $value){
				array_push($final, $arFile[$key]);
			}

			print '<script type="text/javascript"><!--
top.allentries = new Array();
var i = 0;
';
			foreach($final as $key => $entry){
				print 'top.allentries[i++] = "' . $entry . '";';
			}
			print '//--></script>';
			$set_rename = false;

			if(isset($_REQUEST["nf"]) && $_REQUEST["nf"] == "new_folder"){
				?>
				<tr style="background-color:#DFE9F5;">
					<td align="center" width="25"><img src="<?php print ICON_DIR . we_base_ContentTypes::FOLDER_ICON; ?>" width="16" height="18" border="0"></td>
					<td class="selector" width="200"><?php print we_html_tools::htmlTextInput("txt", 20, g_l('fileselector', "[new_folder_name]"), "", 'id="txt" onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%"); ?></td>
					<td class="selector" width="150"><?php print g_l('fileselector', "[folder]") ?></td>
					<td class="selector"><?php print date("d-m-Y H:i:s") ?></td>
					<td class="selector"></td>
				</tr>
				<?php
			}

			foreach($final as $key => $entry){
				$name = str_replace('//', '/', $org . '/' . $entry);
				$DB_WE->query("SELECT ID FROM " . FILE_TABLE . " WHERE Path='" . $DB_WE->escape($name) . "'");

				$isfolder = is_dir($dir . "/" . $entry) ? true : false;

				$type = $isfolder ? g_l('contentTypes', '[folder]') : getDataType($dir . "/" . $entry);

				$indb = $DB_WE->next_record() ? true : false;
				if($entry == "webEdition" || (preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition/|', $dir) || preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition$|', $dir)) && (!preg_match('|^' . $_SERVER['DOCUMENT_ROOT'] . '/?webEdition/we_backup|', $dir) || $entry == "download" || $entry == "tmp")){
					$indb = true;
				}
				if($supportDebugging){
					$indb = false;
				}
				$show = ($entry != ".") && ($entry != "..") && (($_REQUEST["file"] == g_l('contentTypes', '[all_Types]')) || ($type == g_l('contentTypes', '[folder]')) || ($type == $_REQUEST["file"] || $_REQUEST["file"] == ""));
				$bgcol = ($_REQUEST["curID"] == ($dir . "/" . $entry) && (!( isset($_REQUEST["nf"]) && $_REQUEST["nf"] == "new_folder"))) ? "#DFE9F5" : "white";
				$onclick = "";
				$ondblclick = "";
				$_cursor = "cursor:default;";
				if(!(( isset($_REQUEST["nf"]) && ($_REQUEST["nf"] == "rename_folder" || $_REQUEST["nf"] == "rename_file")) && ($entry == $_REQUEST["sid"]) && ($isfolder))){
					if($indb && $isfolder){
						$onclick = ' onClick="tout=setTimeout(\'if(wasdblclick==0){doClick(\\\'' . $entry . '\\\',1,' . ($indb ? "1" : "0") . ');}else{wasdblclick=0;}\',300);return true;"';
						$ondblclick = ' onDblClick="wasdblclick=1;clearTimeout(tout);doClick(\'' . $entry . '\',1,' . ($indb ? "1" : "0") . ');return true;"';
						$_cursor = "cursor:pointer;";
					} else if(!$indb){
						if($isfolder){
							$onclick = ' onClick="if(old==\'' . $entry . '\') mk=setTimeout(\'if(!wasdblclick) clickEdit(old);\',500); old=\'' . $entry . '\';doSelectFolder(\'' . $entry . '\',' . ($indb ? "1" : "0") . ');"';
							$ondblclick = ' onDblClick="wasdblclick=1;clearTimeout(tout);clearTimeout(mk);doClick(\'' . $entry . '\',1,0);return true;"';
						} else{
							$onclick = ' onClick="if(old==\'' . $entry . '\') mk=setTimeout(\'if(!wasdblclick) clickEditFile(old);\',500); old=\'' . $entry . '\';doClick(\'' . $entry . '\',0,0);return true;"';
						}
						$_cursor = "cursor:pointer;";
					}
				}

				$icon = $isfolder ? we_base_ContentTypes::FOLDER_ICON : we_base_ContentTypes::LINK_ICON;
				$filesize = filesize($dir . "/" . $entry);
				$_size = "";
				if(!$isfolder){
					if($filesize >= 1024 && $filesize < (1024 * 1024)){
						$_size = round($filesize / 1024, 1) . " KB";
					} else if($filesize >= (1024 * 1024)){
						$_size = round($filesize / (1024 * 1024), 1) . " MB";
					} else{
						$_size = $filesize . " Byte";
					}
					$_size = '<span' . ($indb ? ' style="color:#006699"' : '') . ' title="' . oldHtmlspecialchars($_size) . '">' . $_size . '</span>';
				}
				if(( isset($_REQUEST["nf"]) && $_REQUEST["nf"] == "rename_folder") && ($entry == $_REQUEST["sid"]) && ($isfolder) && (!$indb)){
					$_text_to_show = we_html_tools::htmlTextInput("txt", 20, $entry, "", 'onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%");
					$set_rename = true;
					$_type = g_l('contentTypes', '[folder]');
					$_date = date("d-m-Y H:i:s");
				} else if(( isset($_REQUEST["nf"]) && $_REQUEST["nf"] == "rename_file") && ($entry == $_REQUEST["sid"]) && (!$indb)){
					$_text_to_show = we_html_tools::htmlTextInput("txt", 20, $entry, "", 'onblur="setScrollTo();we_form.submit();" onkeypress="keypressed(event)"', "text", "100%");
					$set_rename = true;
					$_type = '<span' . ($indb ? ' style="color:#006699"' : '') . ' title="' . oldHtmlspecialchars($type) . '">' . oldHtmlspecialchars(_cutText($type, 17)) . '</span>';
					$_date = date("d-m-Y H:i:s");
				} else{
					$_text_to_show = '<span' . ($indb ? ' style="color:#006699"' : '') . ' title="' . oldHtmlspecialchars($entry) . '">' .
						((strlen($entry) > 24) ? oldHtmlspecialchars(_cutText($entry, 24)) : oldHtmlspecialchars($entry)) .
						'</span>';
					$_type = '<span' . ($indb ? ' style="color:#006699"' : '') . ' title="' . oldHtmlspecialchars($type) . '">' . oldHtmlspecialchars(_cutText($type, 17)) . '</span>';
					$_date = '<span' . ($indb ? ' style="color:#006699"' : '') . '>' . date("d-m-Y H:i:s", filectime($dir . "/" . $entry)) . '<span>';
				}

				if($show){
					print '<tr id="' . oldHtmlspecialchars($entry) . '"' . $ondblclick . $onclick . ' style="background-color:' . $bgcol . ';' . $_cursor . ($set_rename ? "" : "") . '"' . ($set_rename ? '' : '') . '>
	<td class="selector" align="center" width="25"><img src="' . ICON_DIR . $icon . '" width="16" height="18" border="0"></td>
	<td class="selector" width="200">' . $_text_to_show . '</td>
	<td class="selector" width="150">' . $_type . '</td>
	<td class="selector" width="200">' . $_date . '</td>
	<td class="selector">' . $_size . '</td>
 </tr>';
					?>
					<tr>
						<td width="25"><?php print we_html_tools::getPixel(25, 1) ?></td>
						<td width="200"><?php print we_html_tools::getPixel(200, 1) ?></td>
						<td width="150"><?php print we_html_tools::getPixel(150, 1) ?></td>
						<td width="200"><?php print we_html_tools::getPixel(200, 1) ?></td>
						<td><?php print we_html_tools::getPixel(10, 1) ?></td>
					</tr>
					<?php
				}
			}
			?>

		</table>
		<?php if(( isset($_REQUEST["nf"]) && $_REQUEST["nf"] == "new_folder") || (( isset($_REQUEST["nf"]) && ($_REQUEST["nf"] == "rename_folder" || $_REQUEST["nf"] == "rename_file")) && ($set_rename))){ ?>
			<input type="hidden" name="cmd" value="<?php print $_REQUEST["nf"]; ?>" />
			<?php if($_REQUEST["nf"] == "rename_folder" || $_REQUEST["nf"] == "rename_file"){ ?><input type="hidden" name="sid" value="<?php print $_REQUEST["sid"] ?>" />
				<input type="hidden" name="oldtxt" value="" /><?php } ?>
			<input type="hidden" name="pat" value="<?php print isset($_REQUEST["pat"]) ? $_REQUEST["pat"] : ""  ?>" />
		<?php } ?>
	</form>

	<?php if(( isset($_REQUEST["nf"]) && $_REQUEST["nf"] == "new_folder") || (( isset($_REQUEST["nf"]) && ($_REQUEST["nf"] == "rename_folder" || $_REQUEST["nf"] == "rename_file")) && ($set_rename))){ ?>
		<script  type="text/javascript">
			document.forms["we_form"].elements["txt"].focus();
			document.forms["we_form"].elements["txt"].select();
	<?php if($_REQUEST["nf"] == "rename_folder" || $_REQUEST["nf"] == "rename_file"){ ?>
			document.forms["we_form"].elements["oldtxt"].value=document.forms["we_form"].elements["txt"].value;
	<?php } ?>
		document.forms["we_form"].elements["pat"].value=top.currentDir;
		</script>
		<?php
	}
	if(( isset($_REQUEST["nf"]) && $_REQUEST["nf"] == "new_folder") || (( isset($_REQUEST["nf"]) && ($_REQUEST["nf"] == "rename_folder" || $_REQUEST["nf"] == "rename_file")) && ($set_rename))){

	}
	?>
</body>
</html>

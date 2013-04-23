<?php
/**
 * webEdition CMS
 *
 * $Rev: 5338 $
 * $Author: mokraemer $
 * $Date: 2012-12-11 14:26:12 +0100 (Tue, 11 Dec 2012) $
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


$showDocuments = we_hasPerm("CAN_SEE_DOCUMENTS") || we_hasPerm("ADMINISTRATOR");
$showTemplates = we_hasPerm("CAN_SEE_TEMPLATES") || we_hasPerm("ADMINISTRATOR");
if(defined("OBJECT_TABLE")){
	$showObjects = we_hasPerm("CAN_SEE_OBJECTFILES") || we_hasPerm("ADMINISTRATOR");
	$showClasses = we_hasPerm("CAN_SEE_OBJECTS") || we_hasPerm("ADMINISTRATOR");
} else{
	$showObjects = false;
	$showClasses = false;
}

$_treewidth = isset($_COOKIE["treewidth_main"]) && ($_COOKIE["treewidth_main"] >= weTree::MinWidth) ? $_COOKIE["treewidth_main"] : weTree::DefaultWidth;

echo we_html_element::jsScript(JS_DIR . 'images.js') .
 we_html_element::jsScript(JS_DIR . 'we_tabs.js');
?>
<script  type="text/javascript"><!--
	function we_cmd(){
		var args = "";
		var url = "<?php print WEBEDITION_DIR; ?>we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
		switch(arguments[0]){
			case "load":
				var op = top.makeFoldersOpenString();
				parent.we_cmd("load",arguments[1],0,op,top.treeData.table);
				break;
			default:
				for(var i = 0; i < arguments.length; i++){
					args += 'arguments['+i+']' + ( (i < (arguments.length-1)) ? ',' : '');
				}
				eval('parent.we_cmd('+args+')');
		}
	}

	function setTab(table){
		if(we_tabs == null){
			setTimeout("setTab('"+table+"')",500);
			return;
		}
		switch(table){
			case "<?php print FILE_TABLE; ?>":
					we_tabs[0].setState(TAB_ACTIVE,false,we_tabs);
				break;
			case "<?php print TEMPLATES_TABLE; ?>":
					we_tabs[1].setState(TAB_ACTIVE,false,we_tabs);
				break;
<?php
if(defined("OBJECT_FILES_TABLE")){
	?>
						case "<?php print OBJECT_FILES_TABLE; ?>":
								we_tabs[2].setState(TAB_ACTIVE,false,we_tabs);
							break;
						case "<?php print OBJECT_TABLE; ?>":
								we_tabs[3].setState(TAB_ACTIVE,false,we_tabs);
							break;

	<?php
}
?>
				}
			}

<?php
/**
 * GET WIDTH AND HEIGHT OF VERTICAL TABS
 */
// Documents
if(file_exists(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/documents_normal.gif")){
	$_v_tab_documents = getimagesize(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/documents_normal.gif");
}

// Templates
if(file_exists(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/templates_normal.gif")){
	$_v_tab_templates = getimagesize(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/templates_normal.gif");
}

// Check for other tabs if Object module installed
if(defined("OBJECT_TABLE")){
	// Objects
	if(file_exists(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/objects_normal.gif")){
		$_v_tab_objects = getimagesize(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/objects_normal.gif");
	}

	// Classes
	if(file_exists(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/classes_normal.gif")){
		$_v_tab_classes = getimagesize(WE_INCLUDES_PATH . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/v-tabs/classes_normal.gif");
	}
}
?>

			var we_tabs = new Array(
			new we_tab('#','<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/documents_normal.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/documents_active.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/documents_disabled.gif', <?php isset($_v_tab_documents[0]) ? (print $_v_tab_documents[0]) : (print "19") ?>, <?php isset($_v_tab_documents[1]) ? (print $_v_tab_documents[1]) : (print "83") ?>, <?php print ($showDocuments ? "TAB_ACTIVE" : "TAB_DISABLED"); ?>, "if(top.deleteMode){we_cmd('exit_delete', '<?php print FILE_TABLE; ?>');};treeOut();we_cmd('load', '<?php print FILE_TABLE; ?>' ,0)"),
			new we_tab('#','<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/templates_normal.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/templates_active.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/templates_disabled.gif', <?php isset($_v_tab_templates[0]) ? (print $_v_tab_templates[0]) : (print "19") ?>, <?php isset($_v_tab_templates[1]) ? (print $_v_tab_templates[1]) : (print "83") ?>, <?php print ($showTemplates ? "TAB_ACTIVE" : "TAB_DISABLED"); ?>, "if(top.deleteMode){we_cmd('exit_delete', '<?php print TEMPLATES_TABLE; ?>');};treeOut();we_cmd('load', '<?php print TEMPLATES_TABLE; ?>', 0)")
<?php if(defined("OBJECT_TABLE")){ ?>,
				new we_tab('#','<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/objects_normal.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/objects_active.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/objects_disabled.gif', <?php isset($_v_tab_objects[0]) ? (print $_v_tab_objects[0]) : (print "19") ?>, <?php isset($_v_tab_objects[1]) ? (print $_v_tab_objects[1]) : (print "83") ?>, <?php print ($showObjects ? "TAB_ACTIVE" : "TAB_DISABLED"); ?>, "if(top.deleteMode){we_cmd('exit_delete', '<?php print OBJECT_FILES_TABLE; ?>');};treeOut();we_cmd('load', '<?php print OBJECT_FILES_TABLE; ?>', 0)"),
				new we_tab('#','<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/classes_normal.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/classes_active.gif', '<?php print WE_INCLUDES_DIR; ?>we_language/<?php print $GLOBALS["WE_LANGUAGE"]; ?>/v-tabs/classes_disabled.gif', <?php isset($_v_tab_classes[0]) ? (print $_v_tab_classes[0]) : (print "19") ?>, <?php isset($_v_tab_classes[1]) ? (print $_v_tab_classes[1]) : (print "83") ?>, <?php print ($showClasses ? "TAB_ACTIVE" : "TAB_DISABLED"); ?>, "if(top.deleteMode){we_cmd('exit_delete', '<?php print OBJECT_TABLE; ?>');};treeOut();we_cmd('load', '<?php print OBJECT_TABLE; ?>', 0)")
<?php } ?>
		);

			var oldWidth = <?php print weTree::DefaultWidth; ?>;

			function toggleTree() {
				top.toggleTree();
			}

			function incTree(){
				var w = parseInt(top.getTreeWidth());
				if((100<w) && (w<1000)){
					w+=20;
					top.setTreeWidth(w);
				}
				if(w>=1000){
					w=1000;
					self.document.getElementById("incBaum").style.backgroundColor="grey";
				}
			}

			function decTree(){
				var w = parseInt(top.getTreeWidth());
				w-=20;
				if(w>200){
					top.setTreeWidth(w);
					self.document.getElementById("incBaum").style.backgroundColor="";
				}
				if(w<=200 && ((w+20)>=200)){
					toggleTree();
				}
			}


			function treeOut() {
				if (top.getTreeWidth() <= 30) {
					toggleTree();
				}
			}
			//-->
</script>
</head>
<body bgcolor="#ffffff" style="background-image: url(<?php print IMAGE_DIR; ?>v-tabs/background.gif);background-repeat:repeat-y;border-top:1px solid black;margin-top:0px;margin-bottom:0px;margin-left:0px;margin-right:0px;">
	<div style="position:absolute;top:8px;left:5px;z-index:10;border-top:1px solid black;">
		<script  type="text/javascript"><!--
					for (var i=0; i<we_tabs.length;i++) {
						we_tabs[i].write();
						document.writeln('<br/>');
					}
<?php
if(isset($_REQUEST["table"]) && $_REQUEST["table"]){
	print "var defTab = '" . $_REQUEST["table"] . "';";
} else{
	if($showDocuments){
		print "var defTab = '" . FILE_TABLE . "';";
	} else if($showTemplates){
		print "var defTab = '" . TEMPLATES_TABLE . "';";
	} else if($showObjects){
		print "var defTab = '" . OBJECT_FILES_TABLE . "';";
	} else if($showClasses){
		print "var defTab = '" . OBJECT_TABLE . "';";
	} else{
		print "var defTab = '';";
	}
}
?>
			setTab(defTab);
			//-->
		</script>
	</div>
	<img id="incBaum" src="<?php print BUTTONS_DIR ?>icons/function_plus.gif" width="9" height="12" style="position:absolute;bottom:53px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;<?php print ($_treewidth <= 100) ? 'bgcolor:grey;' : ''; ?>" onClick="incTree();">
	<img id="decBaum" src="<?php print BUTTONS_DIR ?>icons/function_minus.gif" width="9" height="12" style="position:absolute;bottom:33px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;<?php print ($_treewidth <= 100) ? 'bgcolor:grey;' : ''; ?>" onClick="decTree();">
	<img id="arrowImg" src="<?php print BUTTONS_DIR ?>icons/direction_<?php print ($_treewidth <= 100) ? "right" : "left"; ?>.gif" width="9" height="12" style="position:absolute;bottom:13px;left:5px;border:1px solid grey;padding:0 1px;cursor: pointer;" onClick="toggleTree();">
</body>
</html>

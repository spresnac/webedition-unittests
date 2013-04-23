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
we_html_tools::protect();
$parts = array();

$we_doc = new we_docTypes();

// Initialize variables
$we_show_response = 0;

switch($_REQUEST['we_cmd'][0]){
	case "save_docType":
		if(!we_hasPerm("EDIT_DOCTYPE")){
			$we_responseText = g_l('weClass', "[no_perms]");
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			break;
		}
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		if(preg_match('|[\'",]|', $we_doc->DocType)){
			$we_responseText = g_l('alert', "[doctype_hochkomma]");
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			$we_JavaScript = "";
			$we_show_response = 1;
		} else if(strlen($we_doc->DocType) == 0){
			$we_responseText = g_l('alert', "[doctype_empty]");
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			$we_JavaScript = "";
			$we_show_response = 1;
		} else{
			$GLOBALS['DB_WE']->query('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType="' . $GLOBALS['DB_WE']->escape($we_doc->DocType) . '"');
			if(($GLOBALS['DB_WE']->next_record()) && ($we_doc->ID != $GLOBALS['DB_WE']->f("ID"))){
				$we_responseText = sprintf(g_l('weClass', "[doctype_save_nok_exist]"), $we_doc->DocType);
				$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
				$we_JavaScript = "";
				$we_show_response = 1;
			} else{
				$we_JavaScript = 'opener.top.makefocus = self;' .
					we_main_headermenu::getMenuReloadCode();

				//$we_JavaScript .= "opener.top.header.document.location.reload();\n";
				if($we_doc->we_save()){
					$we_responseText = sprintf(g_l('weClass', "[doctype_save_ok]"), $we_doc->DocType);
					$we_response_type = we_message_reporting::WE_MESSAGE_NOTICE;
					$we_show_response = 1;
				} else{
					print "ERROR";
				}
			}
		}
		break;
	case "newDocType":
		if($_REQUEST['we_cmd'][1]){
			$we_doc->DocType = urldecode($_REQUEST['we_cmd'][1]);
			$we_doc->we_save();
		}
		break;
	case "deleteDocTypeok":
		if(!we_hasPerm("EDIT_DOCTYPE")){
			$we_responseText = g_l('alert', "[no_perms]");
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			break;
		}
		$name = f("SELECT DocType FROM " . DOC_TYPES_TABLE . " WHERE ID=" . intval($_REQUEST['we_cmd'][1]), 'DocType', $GLOBALS['DB_WE']);
		$del = false;
		if($name){
			$GLOBALS['DB_WE']->query("SELECT 1 FROM " . FILE_TABLE . " WHERE DocType=" . intval($_REQUEST['we_cmd'][1]) . " OR temp_doc_type=" . $GLOBALS['DB_WE']->escape($_REQUEST['we_cmd'][1]));
			if(!$GLOBALS['DB_WE']->next_record()){
				$GLOBALS['DB_WE']->query("DELETE FROM " . DOC_TYPES_TABLE . " WHERE ID=" . intval($_REQUEST['we_cmd'][1]));

				// Fast Fix for deleting entries from tblLangLink: #5840
				$GLOBALS['DB_WE']->query("DELETE FROM " . LANGLINK_TABLE . " WHERE DocumentTable='tblDocTypes' AND (DID=" . intval($_REQUEST["we_cmd"][1]) . ' OR LDID=' . intval($_REQUEST["we_cmd"][1]) . ')');

				$we_responseText = g_l('weClass', "[doctype_delete_ok]");
				$we_response_type = we_message_reporting::WE_MESSAGE_NOTICE;
				$we_responseText = sprintf($we_responseText, $name);
				unset($_REQUEST['we_cmd'][1]);
				$del = true;
			} else{
				$we_responseText = g_l('weClass', "[doctype_delete_nok]");
				$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
				$we_responseText = sprintf($we_responseText, $name);
			}
			if($del){
				$id = f('SELECT ID FROM ' . DOC_TYPES_TABLE . " ORDER BY DocType", 'ID', $GLOBALS['DB_WE']);
				if($id){
					$we_doc->initByID($id, DOC_TYPES_TABLE);
				}
			} else{
				$we_doc->initByID($_REQUEST['we_cmd'][1], DOC_TYPES_TABLE);
			}
		}
		break;
	case "add_dt_template":
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		$foo = makeArrayFromCSV($we_doc->Templates);
		$ids = makeArrayFromCSV($_REQUEST['we_cmd'][1]);
		foreach($ids as $id){
			if(!in_array($id, $foo)){
				array_push($foo, $id);
			}
		}
		$we_doc->Templates = makeCSVFromArray($foo);
		break;
	case "delete_dt_template":
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		$foo = makeArrayFromCSV($we_doc->Templates);
		if($_REQUEST['we_cmd'][1] && (in_array($_REQUEST['we_cmd'][1], $foo))){
			$pos = getArrayKey($_REQUEST['we_cmd'][1], $foo);
			if($pos != "" || $pos == "0"){
				array_splice($foo, $pos, 1);
			}
		}
		if($we_doc->TemplateID == $_REQUEST['we_cmd'][1]){
			if(count($foo)){
				$we_doc->TemplateID = $foo[0];
			} else{
				$we_doc->TemplateID = 0;
			}
		}
		$we_doc->Templates = makeCSVFromArray($foo);
		break;
	case "dt_add_cat":
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		if($_REQUEST['we_cmd'][1])
			$we_doc->addCat($_REQUEST['we_cmd'][1]);
		break;
	case "dt_delete_cat":
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		if($_REQUEST['we_cmd'][1]){
			$we_doc->delCat($_REQUEST['we_cmd'][1]);
		}
		break;
	default:
		if(isset($_REQUEST['we_cmd'][1])){
			$id = $_REQUEST['we_cmd'][1];
		} else{
			$q = getDoctypeQuery($GLOBALS['DB_WE']);
			$q = "SELECT ID FROM " . DOC_TYPES_TABLE . " $q";
			$id = f($q, "ID", $GLOBALS['DB_WE']);
		}
		if($id){
			$we_doc->initByID($id, DOC_TYPES_TABLE);
		}
}

we_html_tools::htmlTop(g_l('weClass', "[doctypes]"));
$yuiSuggest = & weSuggest::getInstance();
echo $yuiSuggest->getYuiCssFiles();
echo $yuiSuggest->getYuiJsFiles();

print we_html_element::jsScript(JS_DIR . "keyListener.js");
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript"><!--
<?php
if($we_show_response){
	print $we_JavaScript . ';';
	if($we_responseText){
		?>
					opener.top.toggleBusy(0);
		<?php
		print we_message_reporting::getShowMessageCall($we_responseText, $we_response_type);
	}
}
if($_REQUEST['we_cmd'][0] == "deleteDocType"){
	if(!we_hasPerm("EDIT_DOCTYPE")){
		print we_message_reporting::getShowMessageCall(g_l('alert', "[no_perms]"), we_message_reporting::WE_MESSAGE_ERROR);
	} else{
		?>
					if(confirm("<?php printf(g_l('weClass', "[doctype_delete_prompt]"), $we_doc->DocType); ?>")) {
						we_cmd("deleteDocTypeok","<?php print $_REQUEST['we_cmd'][1]; ?>");
					}
		<?php
	}
}
if($_REQUEST['we_cmd'][0] == "deleteDocTypeok"){
	echo 'opener.top.makefocus = self;' .
	we_main_headermenu::getMenuReloadCode();
//							opener.top.header.document.location.reload();

	print we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_NOTICE);
}
?>

	var countSaveLoop = 0;

	function we_save_docType(doc,url) {
		acStatus = '';
		invalidAcFields = false;
		if(YAHOO && YAHOO.autocoml) {
			acStatus = YAHOO.autocoml.checkACFields();
		} else {
			we_submitForm(doc,url);
			return;
		}
		acStatusType = typeof acStatus;
		if (countSaveLoop > 10) {
<?php print we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) ?>;
			countSaveLoop = 0;
		} else if(acStatusType.toLowerCase() == 'object') {
			if(acStatus.running) {
				countSaveLoop++;
				setTimeout('we_save_docType(doc,url)',100);
			} else if(!acStatus.valid) {
<?php print we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) ?>;
				countSaveLoop=0;
			} else {
				countSaveLoop=0;
				we_submitForm(doc,url);
			}
		} else {
<?php print we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) ?>;
		}
	}

	function we_cmd() {
		var args = "";
		var url = "<?php print WEBEDITION_DIR; ?>we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURIComponent(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
		switch (arguments[0]) {
			case "openDocselector":
			case "openDirselector":
				new jsWindow(url,"we_fileselector",-1,-1,<?php echo WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT; ?>,true,true,true,true);
				break;
			case "openCatselector":
				new jsWindow(url,"we_catselector",-1,-1,<?php echo WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT; ?>,true,true,true,true);
				break;
			case "add_dt_template":
			case "delete_dt_template":
			case "dt_add_cat":
			case "dt_delete_cat":
			case "save_docType":
				we_save_docType(self.name,url)
				break;
			case "newDocType":
<?php
$dtNames = "";
$GLOBALS['DB_WE']->query('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' ORDER BY DocType');
while($GLOBALS['DB_WE']->next_record()) {
	$dtNames .= '\'' . str_replace('\'', '\\\'', $GLOBALS['DB_WE']->f("DocType")) . '\',';
}
$dtNames = rtrim($dtNames, ',');
print 'var docTypeNames = new Array(' . $dtNames . ');';
?>

				var name = prompt("<?php print g_l('weClass', "[newDocTypeName]"); ?>","");
				if(name != null) {
					if((name.indexOf("<") != -1) || (name.indexOf(">") != -1)) {
<?php print we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
						return;
					}
					if(name.indexOf("'") != -1 || name.indexOf('"') != -1 || name.indexOf(',') != -1) {
<?php print we_message_reporting::getShowMessageCall(g_l('alert', "[doctype_hochkomma]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
					}
					else if(name=="") {
<?php print we_message_reporting::getShowMessageCall(g_l('alert', "[doctype_empty]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
					}
					else if(in_array(docTypeNames,name)) {
<?php print we_message_reporting::getShowMessageCall(g_l('alert', "[doctype_exists]"), we_message_reporting::WE_MESSAGE_ERROR); ?>
					}
					else {
<?php
echo we_main_headermenu::getMenuReloadCode();
?>
						/*						if (top.opener.top.header) {
							top.opener.top.header.location.reload();
						}*/
						self.location = "<?php print WEBEDITION_DIR; ?>we_cmd.php?we_cmd[0]=newDocType&we_cmd[1]="+encodeURIComponent(name);
					}
				}
				break;
			case "change_docType":
			case "deleteDocType":
			case "deleteDocTypeok":
				self.location = url;
				break;
			default:
				for(var i = 0; i < arguments.length; i++) {
					args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
				}
				eval('opener.top.we_cmd('+args+')');
			}
		}


		function we_submitForm(target,url) {
			var f = self.document.we_form;
			f.target = target;
			f.action = url;
			f.method = "post";
			f.submit();
		}

		function doUnload() {
			if(jsWindow_count) {
				for(i=0;i<jsWindow_count;i++) {
					eval("jsWindow"+i+"Object.close()");
				}
			}
			opener.top.dc_win_open=false;
		}

		function in_array(haystack, needle) {
			for(var i=0;i<haystack.length;i++) {
				if(haystack[i] == needle)
					return true;
			}
			return false;
		}

		function makeNewEntry(icon,id,pid,txt,offen,ct,tab) {
			opener.top.makeNewEntry(icon,id,pid,txt,offen,ct,tab);
		}

		function updateEntry(id,text,pid,tab) {
			opener.top.updateEntry(id,text,pid,tab);
		}

		function disableLangDefault(allnames,allvalues,deselect){
			var arr = allvalues.split(",");

			for(var v in arr){
				w=allnames+'['+arr[v]+']';
				e = document.getElementById(w);
				e.disabled=false;
			}
			w=allnames+'['+deselect+']';
			e = document.getElementById(w);
			e.disabled=true;


		}
		//-->
</script>
<?php print STYLESHEET; ?>
</head>

<body class="weDialogBody" style="overflow:hidden;" onUnload="doUnload()" onLoad="self.focus();">
	<form name="we_form" method="post" onSubmit="return false">
		<?php
		echo we_class::hiddenTrans();

		if($we_doc->ID){

			array_push($parts, array("headline" => g_l('weClass', "[doctypes]"),
				"html" => $GLOBALS['we_doc']->formDocTypeHeader(),
				"space" => 120
				)
			);

			array_push($parts, array("headline" => g_l('weClass', "[name]"),
				"html" => $GLOBALS['we_doc']->formName(),
				"space" => 120
				)
			);

			array_push($parts, array("headline" => g_l('global', "[templates]"),
				"html" => $GLOBALS['we_doc']->formDocTypeTemplates(),
				"space" => 120
				)
			);

			array_push($parts, array("headline" => g_l('weClass', "[defaults]"),
				"html" => $GLOBALS['we_doc']->formDocTypeDefaults(),
				"space" => 120
				)
			);
		} else{
			array_push($parts, array("headline" => "",
				"html" => $GLOBALS['we_doc']->formNewDocType(),
				"space" => 0
				)
			);
		}

		$cancelbut = we_button::create_button("close", "javascript:self.close();if(top.opener.we_cmd){top.opener.we_cmd('switch_edit_page',0);}");

		if($we_doc->ID){
			$buttons = we_button::position_yes_no_cancel(we_button::create_button("save", "javascript:we_cmd('save_docType', '$we_transaction')"), "", $cancelbut);
		} else{
			$buttons = '<div align="right">' . $cancelbut . '</div>';
		}

		print we_multiIconBox::getJS() .
			we_multiIconBox::getHTML("", "100%", $parts, 30, $buttons, -1, "", "", false, "", "", 630) .
			$yuiSuggest->getYuiCss() .
			$yuiSuggest->getYuiJs();
		?>
	</form>
</body>

</html>

<?php
$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);


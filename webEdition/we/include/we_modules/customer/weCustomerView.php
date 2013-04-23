<?php

/**
 * webEdition CMS
 *
 * $Rev: 5607 $
 * $Author: arminschulz $
 * $Date: 2013-01-21 13:40:43 +0100 (Mon, 21 Jan 2013) $
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
/* the parent class of storagable webEdition classes */

class weCustomerView{

	private $db;
	var $frameset;
	var $topFrame;
	var $customer;
	var $settings;

	const ERR_SAVE_BRANCH = -10;
	const ERR_SAVE_FIELD_INVALID = -7;
	const ERR_SAVE_PROPERTY = -5;
	const ERR_SAVE_FIELD_EXISTS = -4;
	const ERR_SAVE_FIELD_NOT_EMPTY = -3;

	function __construct($frameset = '', $topframe = 'top.content'){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
		$this->customer = new weCustomer();
		$this->settings = new weCustomerSettings();
		$this->settings->customer = & $this->customer;
		$this->settings->load();
	}

	//----------- Utility functions ------------------

	function htmlHidden($name, $value = ''){
		return we_html_element::htmlHidden(array('name' => trim($name), 'value' => oldHtmlspecialchars($value)));
	}

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
	}

	function getCommonHiddens($cmds = array()){
		return $this->htmlHidden('cmd', (isset($cmds['cmd']) ? $cmds['cmd'] : '')) .
			$this->htmlHidden('pnt', (isset($cmds['pnt']) ? $cmds['pnt'] : '')) .
			$this->htmlHidden('cmdid', (isset($cmds['cmdid']) ? $cmds['cmdid'] : '')) .
			$this->htmlHidden('activ_sort', (isset($cmds['activ_sort']) ? $cmds['activ_sort'] : '')) .
			$this->htmlHidden('branch', (isset($_REQUEST['branch']) ? $_REQUEST['branch'] : g_l('modules_customer', '[common]')));
	}

	function getJSTop(){
		$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
		$title = '';
		foreach($GLOBALS['_we_available_modules'] as $modData){
			if($modData['name'] == $mod){
				$title = 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'];
				break;
			}
		}

		$js = '
			var get_focus = 1;
			var activ_tab = 0;
			var hot= 0;
			var scrollToVal=0;

			function setHot() {
				hot = "1";
			}

			parent.document.title = "' . $title . '";

			function usetHot() {
				hot = "0";
			}

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd() {
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				if(hot == "1" && arguments[0] != "save_customer") {
					if(confirm("' . g_l('modules_customer', '[save_changed_customer]') . '")) {
						arguments[0] = "save_customer";
					} else {
						top.content.usetHot();
					}
				}
				switch (arguments[0]) {
					case "exit_customer":
						if(hot != "1") {
							eval(\'top.opener.top.we_cmd("exit_modules")\');
						}
						break;
					case "new_customer":
						if(' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value = arguments[0];
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value = arguments[1];
							' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
						} else {
							setTimeout(\'we_cmd("new_customer");\', 10);
						}
						break;

					case "delete_customer":
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;
						' . (!we_hasPerm("DELETE_CUSTOMER") ?
				('
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						') :
				('
								if (' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
									if (confirm("' . g_l('modules_customer', '[delete_alert]') . '")) {
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
										' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
									}
								} else {
									' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								}

						')) . '
						break;

					case "save_customer":
						if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;
						' . ((!we_hasPerm("EDIT_CUSTOMER") && !we_hasPerm("NEW_CUSTOMER")) ?
				('
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						') :
				('

								if (' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
										' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
										' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
								} else {
									' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								}
						')) . '
						top.content.usetHot();
						break;

					case "edit_customer":
						' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
						' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value=arguments[1];
						' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
					break;
					case "show_admin":
					case "show_sort_admin":
						if(' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=="home") ' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.home.value="1";
						' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
						' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value=arguments[1];
						' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
					break;
					case "show_search":
						' . $this->topFrame . '.resize.left.treefooter.document.we_form.cmd.value=arguments[0];
						' . $this->topFrame . '.resize.left.treefooter.submitForm();
					break;
					case "show_customer_settings":
						' . $this->topFrame . '.resize.left.treefooter.document.we_form.cmd.value=arguments[0];
						' . $this->topFrame . '.resize.left.treefooter.submitForm();
					break;
					case "export_customer":
						' . $this->topFrame . '.resize.left.treefooter.document.we_form.cmd.value=arguments[0];
						' . $this->topFrame . '.resize.left.treefooter.submitForm();
					break;
					case "import_customer":
						' . $this->topFrame . '.resize.left.treefooter.document.we_form.cmd.value=arguments[0];
						' . $this->topFrame . '.resize.left.treefooter.submitForm();
					break;
					case "load":
						' . $this->topFrame . '.cmd.location="' . $this->frameset . '?pnt=cmd&pid="+arguments[1]+"&offset="+arguments[2]+"&sort="+arguments[3];
					break;
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.opener.top.we_cmd(" + args + ")");
				}
			}';

		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement($js);
	}

	function getJSProperty(){
		$out = we_html_element::jsScript(JS_DIR . 'windows.js');

		$js = '
			var loaded=0;

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd() {
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

				switch (arguments[0]) {
					case "browse_users":
						new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
					break;
					case "openDocselector":
						new jsWindow(url,"we_fileselector",-1,-1,' . WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT . ',true,true,true,true);
					break;
					case "switchPage":
						document.we_form.cmd.value=arguments[0];
						document.we_form.branch.value=arguments[1];
						submitForm();
					break;
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
						}
						eval("top.content.we_cmd("+args+")");
				}
			}

			function setMultiSelectData(name,max){
				var tmp="";
				for (var i=0; i<max; ++i){
					var val=document.getElementsByName(name+"_multi_"+i)[0];
					if(val.checked){
						tmp+=val.value+",";
					}
				}
				tmp=tmp.substr(0,tmp.length-1);
				document.getElementsByName(name)[0].value=tmp;
				top.content.setHot();
			}

			function formatDate(date,format){

				var daynum=date.getDate();
				var day=daynum.toString();
				if(format.search("d")!=-1)
					if(daynum<10) day="0"+day;

				format=format.replace("d",day);
				format=format.replace("j",day);

				var monthnum=date.getMonth()+1;
				var month=monthnum.toString();
				if(format.search("m")!=-1)
					if(monthnum<10) month="0"+month;

				format=format.replace("m",month);
				format=format.replace("n",month);

				format=format.replace("Y",date.getFullYear());
				var yearnum=date.getYear();
				var year=yearnum.toString();
				format=format.replace("y",year.substr(2,2));

				var hournum=date.getHours();
				var hour=hournum.toString();
				if(format.search("H")!=-1)
					if(hournum<10) hour="0"+hour;

				format=format.replace("H",hour);
				format=format.replace("G",hour);

				var minnum=date.getMinutes();
				var min=minnum.toString();
				if(minnum<10) min="0"+min;

				format=format.replace("i",min);


				/*var secnum=date.getSeconds();
				var sec=secnum.toString();
				if(secnum<10) sec="0"+sec;*/

				var sec="00";

				format=format.replace("s",sec);

				format=format.replace(/\\\/g,"");

				return format;
			}

			function refreshForm(){
				if(document.we_form.cmd.value!="home"){
					we_cmd("switchPage",' . $this->topFrame . '.activ_tab);
					' . $this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->customer->Text) . '";
				}
			}
			' . $this->getJSSubmitFunction();

		$out.=we_html_element::jsElement($js);
		return $out;
	}

	function getJSSortAdmin(){
		return weCustomerAdd::getJSSortAdmin($this);
	}

	function getJSAdmin(){
		return
			'
			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd(){
				var args = "";
				var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

				switch (arguments[0]) {
					case "open_add_field":
						var branch=document.we_form.branch.value;
						url = "' . $this->frameset . '?pnt=field_editor&art=add&branch="+branch;
						new jsWindow(url,"field_editor",-1,-1,380,250,true,false,true);
					break;
					case "open_edit_field":
						var field=document.we_form.fields_select.value;
						var branch=document.we_form.branch.value;
						if(field=="") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[no_field]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else{
								url = "' . $this->frameset . '?pnt=field_editor&art=edit&field="+field+"&branch="+branch;
								new jsWindow(url,"field_editor",-1,-1,380,250,true,false,true);
						}
					break;
					case "delete_field":
						var field=document.we_form.fields_select.value;
						if(field=="") {
						 ' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[no_field]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else{
							if(confirm("' . g_l('modules_customer', '[del_fild_question]') . '")){
								document.we_form.cmd.value=arguments[0];
								submitForm();
							}
						}
					break;
					case "reset_edit_order":
						var field=document.we_form.fields_select.value;
						var branch=document.we_form.branch.value;
							if(confirm("' . g_l('modules_customer', '[reset_edit_order_question]') . '")){
								document.we_form.cmd.value=arguments[0];
								submitForm();
							}
					break;
					case "move_field_up":
						var field=document.we_form.fields_select.value;
						var branch=document.we_form.branch.value;
						if(field=="") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[no_field]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else{
								document.we_form.cmd.value=arguments[0];
								submitForm();
						}
					break;
					case "move_field_down":
						var field=document.we_form.fields_select.value;
						var branch=document.we_form.branch.value;
						if(field=="") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[no_field]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else{
								document.we_form.cmd.value=arguments[0];
								submitForm();
						}
					break;
					case "open_edit_branch":
						var branch=document.we_form.branch_select.options[document.we_form.branch_select.selectedIndex].text;
						if(branch=="") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[no_branch]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else if(branch=="' . g_l('modules_customer', '[other]') . '") {
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[branch_no_edit]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						} else{
								url = "' . $this->frameset . '?pnt=branch_editor&art=edit&&branch="+branch;
								new jsWindow(url,"field_editor",-1,-1,380,250,true,false,true);
						}
					break;
					case "save_branch":
					case "save_field":
						var field_name=document.we_form.name.value;
						if(field_name=="" || field_name.match(/[^a-zA-Z0-9\_]/)!=null){
							' . we_message_reporting::getShowMessageCall(g_l('modules_customer', '[we_fieldname_notValid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
						else{
							document.we_form.cmd.value=arguments[0];
							submitForm("field_editor");
						}
					break;
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
						}
						eval(\'top.content.we_cmd(\'+args+\')\');
				}
			}

			function selectBranch(){
				var f=document.we_form;
				var txt=f.branch;
				var sel=f.branch_select.options[f.branch_select.selectedIndex].text;

				f.cmd.value="switchBranch";
				txt.value=sel;
				submitForm();

			}

			function saveField(){
				document.we_form.cmd.value="save_field";
				submitForm();
			}' .
			$this->getJSSubmitFunction("customer_admin");
	}

	function getJSTreeHeader(){
		return '
			function applySort(){
				document.we_form.pnt.value="cmd";
				document.we_form.cmd.value="applySort";
				submitForm();
			}

			function addSorting(sortname) {
				var found=false;
				len = document.we_form.sort.options.length;
				for(i=0;i<len;i++) {
					if(document.we_form.sort.options[i].value==sortname){
						found = true;
					}
				}
				if(!found){
					document.we_form.sort.options[len] = new Option(sortname,sortname);
				}

			}

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd(){
				var args = "";
				var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]) {
					default:
						for (var i = 0; i < arguments.length; i++) {
							args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
						}
						eval(\'top.content.we_cmd(\'+args+\')\');
				}
			}' .
			$this->getJSSubmitFunction('cmd');
	}

	function getJSSearch(){
		return weCustomerAdd::getJSSearch($this);
	}

	function getJSSettings(){
		return '
			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			function we_cmd(){
				var args = "";
				var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]) {
					case "save_settings":
						document.we_form.cmd.value=arguments[0];
						submitForm();
					break;
					default:
				}
		}

		self.focus();
	' . $this->getJSSubmitFunction("customer_settings");
	}

	function getJSSubmitFunction($def_target = 'edbody', $def_method = 'post'){
		return '
			function submitForm() {
				var f = self.document.we_form;

				if (arguments[0]) {
					f.target = arguments[0];
				} else {
					f.target = "' . $def_target . '";
				}

				if (arguments[1]) {
					f.action = arguments[1];
				} else {
					f.action = "' . $this->frameset . '";
				}

				if (arguments[2]) {
					f.method = arguments[2];
				} else {
					f.method = "' . $def_method . '";
				}

				f.submit();
			}';
	}

	function processCommands(){
		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){
				case 'new_customer':
					$this->customer = new weCustomer();
					$this->settings->initCustomerWithDefaults($this->customer);
					print we_html_element::jsElement(
							$this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->customer->Text) . '";' .
							$this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
						);
					break;
				case 'edit_customer':
					$this->customer = new weCustomer($_REQUEST["cmdid"]);
					print we_html_element::jsElement(
							$this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->customer->Text) . '";' .
							$this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
						);
					break;
				case 'save_customer':
					$js = '';
					$this->customer->Username = trim($this->customer->Username);
					if($this->customer->Username == ''){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_customer', '[username_empty]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}

					if($this->customer->filenameNotValid()){
						print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_customer', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}

					$newone = true;
					if($this->customer->ID){
						$newone = false;
					}

					$exists = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $this->db->escape($this->customer->Username) . '"' . ($newone ? '' : ' AND ID!=' . $this->customer->ID), 'ID', $this->db);
					if($exists){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[username_exists]'), $this->customer->Username), we_message_reporting::WE_MESSAGE_ERROR)
							);
						break;
					}
					$c = unserialize($_SESSION['weS']['customer_session']);
					if($c->Password != $this->customer->Password || $this->customer->LoginDenied || $this->customer->AutoLoginDenied){//bei Password�nderungen die persistentlogins l�schen
						$this->db->query('DELETE FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE WebUserID=' . intval($this->customer->ID));
					}
					//FIXME: what if save failes (e.g. Hooks?)
					$this->customer->save();

					$tt = '';
					$ttrow = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($this->customer->ID), $this->db);
					//needed, because format is php code
					eval('$tt="' . $this->settings->treeTextFormat . '";');
					$tt = addslashes($tt != '' ? $tt : $this->customer->Text);
					if($newone){
						$js = '
									var attribs = new Array();
									attribs["icon"]=\'' . $this->customer->Icon . '\';
									attribs["id"]=\'' . $this->customer->ID . '\';
									attribs["typ"]=\'item\';
									attribs["parentid"]=\'0\';
									attribs["text"]=\'' . $tt . '\';
									attribs["disable"]=\'0\';
									attribs["tooltip"]=\'' . (($this->customer->Forename != "" || $this->customer->Surname != "") ? $this->customer->Forename . "&nbsp;" . $this->customer->Surname : "") . '\';' .
							$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));';
						$js .= $this->topFrame . '.resize.left.treeheader.applySort();';
					} else{
						$js = $this->topFrame . '.updateEntry(' . $this->customer->ID . ',"' . $tt . '");' . "\n";
					}


					print we_html_element::jsElement(
							$js .
							we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[customer_saved_ok]'), addslashes($this->customer->Text)), we_message_reporting::WE_MESSAGE_NOTICE)
						);
					break;
				case 'delete_customer':
					$oldid = $this->customer->ID;
					$this->customer->delete();
					$this->customer = new weCustomer();

					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_customer', '[customer_deleted]'), we_message_reporting::WE_MESSAGE_NOTICE) .
							$this->topFrame . '.deleteEntry("' . $oldid . '"); ' .
							$this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?home=1&pnt=edheader"; ' .
							$this->topFrame . '.resize.right.editor.edbody.location="' . $this->frameset . '?home=1&pnt=edbody"; ' .
							$this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";'
						);

					break;
				case 'switchPage':

					break;

				case 'show_admin':
					$js = '
						url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=customer_admin";
						new jsWindow(url,"customer_admin",-1,-1,600,420,true,true,true,false);
					';
					print we_html_element::jsScript(JS_DIR . "windows.js") .
						we_html_element::jsElement($js);
					break;
				case 'save_field':
					$branch = $_REQUEST['branch'];
					$field = $_REQUEST['field'];
					$field_name = $_REQUEST['name'];
					$field_type = $_REQUEST['field_type'];
					$field_default = $_REQUEST['field_default'];

					$saveret = $this->saveField($field, $branch, $field_name, $field_type, $field_default);

					switch($saveret){
						case self::ERR_SAVE_BRANCH:
							$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[branch_no_edit]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						case self::ERR_SAVE_FIELD_INVALID:
							$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[we_fieldname_notValid]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						case self::ERR_SAVE_PROPERTY:
							$js = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[cannot_save_property]'), $field_name), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						case self::ERR_SAVE_FIELD_EXISTS:
							$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[fieldname_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						case self::ERR_SAVE_FIELD_NOT_EMPTY:
							$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[field_not_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						default:
							$this->customer->loadPresistents();
							$sort = $this->settings->getEditSort();
							$sortarray = makeArrayFromCSV($sort);
							$orderedarray = $this->customer->persistent_slots;

							$cs = count($sortarray);
							$co = count($orderedarray);
							if(count($sortarray) != count($orderedarray)){
								if(count($sortarray) < count($orderedarray)){
									$sortarray[] = max($sortarray) + 1;
								}
								if(count($sortarray) < count($orderedarray)){
									$sortarray[] = max($sortarray) + 1;
								}
								if(count($sortarray) < count($orderedarray)){
									$sortarray[] = max($sortarray) + 1;
								}
								if(count($sortarray) != count($orderedarray)){
									$sortarray = range(0, count($orderedarray) - 1);
								}
							}
							$this->settings->setEditSort(makeCSVFromArray($sortarray, true));
							$this->settings->save();

							$js = '
							opener.submitForm();
							opener.opener.refreshForm();
							close();';
					}
					print we_html_element::jsElement($js);

					break;
				case 'delete_field':
					$field = $_REQUEST['fields_select'];

					$sort = $this->settings->getEditSort();
					$sortarray = makeArrayFromCSV($sort);
					$orderedarray = $this->customer->persistent_slots;
					if(count($sortarray) != count($orderedarray)){
						$sortarray = range(0, count($orderedarray) - 1);
					}
					$orderedarray = array_combine($sortarray, $orderedarray);
					ksort($orderedarray);
					$curpos = array_search($field, $orderedarray);
					$curposS = array_search($curpos, $sortarray);
					unset($sortarray[$curposS]);
					foreach($sortarray as &$val){
						if($val >= $curpos){
							$val--;
						}
					}
					if($sortarray[count($sortarray) - 1] == ''){
						array_pop($sortarray);
					}
					$this->settings->setEditSort(makeCSVFromArray($sortarray, true));
					$this->settings->save();

					$ber = '';
					$fname = $this->customer->transFieldName($field, $ber);

					if($ber == '' && preg_match('%' . g_l('modules_customer', '[other]') . '%i', $field))
						$this->deleteField($fname);
					else
						$this->deleteField($field);

					$this->customer->loadPresistents();
					$js =
						we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[field_deleted]'), $fname, $ber), we_message_reporting::WE_MESSAGE_NOTICE) .
						'opener.refreshForm();';
					print we_html_element::jsElement($js);

					break;
				case 'reset_edit_order':
					$orderedarray = $this->customer->persistent_slots;
					$sortarray = range(0, count($orderedarray) - 1);
					$this->settings->setEditSort(makeCSVFromArray($sortarray, true));
					$this->settings->save();
					break;
				case 'move_field_up':
					$field = $_REQUEST['fields_select'];
					$sort = $this->settings->getEditSort();
					$sortarray = makeArrayFromCSV($sort);
					$orderedarray = $this->customer->persistent_slots;
					if(count($sortarray) != count($orderedarray)){
						if(count($sortarray) < count($orderedarray)){
							$sortarray[] = max($sortarray) + 1;
						}
						if(count($sortarray) != count($orderedarray)){
							$sortarray = range(0, count($orderedarray) - 1);
						}
					}
					$orderedarray = array_combine($sortarray, $orderedarray);
					ksort($orderedarray);

					$curpos = array_search($field, $orderedarray);
					$curpos1 = $curpos - 1;
					if($curpos != 0){
						$sort = str_replace(array(',' . $curpos . ',', ',' . $curpos1 . ','), array(',XX,', ',YY,'), $sort);
						$sort = str_replace(array(',XX,', ',YY,'), array(',' . $curpos1 . ',', ',' . $curpos . ','), $sort);

						$this->settings->setEditSort($sort);
						$this->settings->save();
						$this->customer->loadPresistents();
					}
					$js = 'opener.refreshForm();';
					print we_html_element::jsElement($js);

					break;
				case 'move_field_down':
					$field = $_REQUEST['fields_select'];
					$sort = $this->settings->getEditSort();
					$sortarray = makeArrayFromCSV($sort);
					$orderedarray = $this->customer->persistent_slots;
					if(count($sortarray) != count($orderedarray)){
						if(count($sortarray) < count($orderedarray)){
							$sortarray[] = max($sortarray) + 1;
						}
						if(count($sortarray) != count($orderedarray)){
							$sortarray = range(0, count($orderedarray) - 1);
						}
					}
					$orderedarray = array_combine($sortarray, $orderedarray);
					ksort($orderedarray);

					$curpos = array_search($field, $orderedarray);
					$curpos1 = $curpos + 1;
					if($curpos != count($orderedarray) - 1){
						$sort = str_replace(array(',' . $curpos . ',', ',' . $curpos1 . ','), array(',XX,', ',YY,'), $sort);
						$sort = str_replace(array(',XX,', ',YY,'), array(',' . $curpos1 . ',', ',' . $curpos . ','), $sort);
						$this->settings->setEditSort($sort);
						$this->settings->save();
						$this->customer->loadPresistents();
					}
					$js =						'opener.refreshForm();';
					print we_html_element::jsElement($js);

					break;
				case 'save_branch':
					$branch_new = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
					$branch_old = isset($_REQUEST['branch']) ? $_REQUEST['branch'] : '';

					if($branch_new == g_l('modules_customer', '[common]') || $branch_new == g_l('modules_customer', '[other]') || $branch_new == g_l('modules_customer', '[all]')){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_customer', '[branch_no_edit]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
						return;
					}

					if($branch_new != $branch_old){
						$arr = array();
						$arr = $this->customer->getBranchesNames();

						if(in_array($branch_new, $arr)){
							print we_html_element::jsElement(
									we_message_reporting::getShowMessageCall(g_l('modules_customer', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR)
								);
							return;
						}
					}

					if($this->saveBranch($branch_old, $branch_new) == -5)
						we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[cannot_save_property]'), $field), we_message_reporting::WE_MESSAGE_ERROR);
					else{
						$this->customer->loadPresistents();
						$js = '
							opener.document.we_form.branch.value="' . g_l('modules_customer', '[other]') . '";
							opener.submitForm();
							opener.opener.document.we_form.branch.value="' . g_l('modules_customer', '[common]') . '";
							opener.opener.refreshForm();
							close();';
					}
					print we_html_element::jsElement($js);

					break;
				case 'show_sort_admin':
					$js = '
						url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=sort_admin";
						new jsWindow(url,"sort_admin",-1,-1,750,500,true,true,true,true);
					';
					print we_html_element::jsScript(JS_DIR . 'windows.js');
					print we_html_element::jsElement($js);

					break;
				case 'add_sort':
					$cout = 0;
					$found = false;
					while(!$found) {
						$cname = g_l('modules_customer', '[sort_name]') . $cout;
						if(!in_array($cname, array_keys($this->settings->SortView)))
							$found = true;
						$cout++;
					}
					$this->settings->SortView[$cname] = array();

					break;
				case 'del_sort':
					if(isset($_REQUEST['sortindex']))
						$this->new_array_splice($this->settings->SortView, $_REQUEST['sortindex'], 1);
					break;
				case 'add_sort_field':
					if(isset($_REQUEST['sortindex'])){
						$this->settings->SortView[$_REQUEST['sortindex']][] = array('branch' => '', 'field' => '', 'order' => '');
					}
					break;
				case 'del_sort_field':
					if(isset($_REQUEST['sortindex']))
						if(isset($_REQUEST['fieldindex']))
							array_splice($this->settings->SortView[$_REQUEST['sortindex']], $_REQUEST['fieldindex'], 1);
					break;
				case 'save_sort':

					$this->settings->save();
					$_sorting = 'opener.' . $this->topFrame . '.resize.left.treeheader.addSorting("' . g_l('modules_customer', '[no_sort]') . '");' . "\n";
					foreach(array_keys($this->settings->SortView) as $_sort){
						$_sorting .= 'opener.' . $this->topFrame . '.resize.left.treeheader.addSorting("' . $_sort . '");' . "\n";
					}

					$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[sort_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
							var selected = opener.' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.selectedIndex;
							opener.' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.options.length=0;
							' . $_sorting . '

							if(selected<opener.' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.options.length){
								opener.' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.selectedIndex = selected;
							} else {
								opener.' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.selectedIndex = opener.' . $this->topFrame . '.resize.left.treeheader.document.we_form.sort.options.length-1;
							}

							opener.' . $this->topFrame . '.resize.left.treeheader.applySort();
							self.close();';
					print we_html_element::jsScript(JS_DIR . "we_showMessage.js").
					 we_html_element::jsElement($js);
					break;
				case 'applySort':
					$js = $this->topFrame . '.clearTree();';
					print we_html_element::jsElement($js);
					break;
				case 'show_search':
					$js = '
						url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=search&search=1&keyword=' . $_REQUEST["keyword"] . '";
						new jsWindow(url,"search",-1,-1,650,600,true,true,true,false);';
					print we_html_element::jsScript(JS_DIR . "windows.js") .
						we_html_element::jsElement($js);
					break;
				case 'show_customer_settings':
					$js = '
						url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=settings";
						new jsWindow(url,"customer_settings",-1,-1,550,250,true,true,true,false);
					';
					print we_html_element::jsScript(JS_DIR . "windows.js") .
						we_html_element::jsElement($js);
					break;
				case 'import_customer':
					$js =
						'url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=import";
						new jsWindow(url,"import_customer",-1,-1,640,600,true,true,true,false);';
					print we_html_element::jsScript(JS_DIR . "windows.js") .
						we_html_element::jsElement($js);
					break;
				case 'export_customer':
					$js =
						'url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=export";
						new jsWindow(url,"export_customer",-1,-1,640,600,true,true,true,false);';
					print we_html_element::jsScript(JS_DIR . "windows.js") .
						we_html_element::jsElement($js);
					break;
				case 'save_settings':

					foreach($this->settings->getAllSettings() as $k => $v){
						if(isset($_REQUEST[$k]))
							$this->settings->setSettings($k, $_REQUEST[$k]);
					}
					foreach($this->settings->properties as $k => $v){
						if(isset($_REQUEST[$k]))
							$this->settings->properties[$k] = $_REQUEST[$k];
					}

					if($this->settings->save()){
						$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[settings_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) .
							'self.close();';
					} else{
						$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[settings_not_saved]'), we_message_reporting::WE_MESSAGE_NOTICE);
					}
					print we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
						we_html_element::jsElement($js);
					break;
				default:
			}
		}

		$_SESSION['weS']['customer_session'] = serialize($this->customer);
	}

	function processVariables(){
		if(isset($_SESSION['weS']['customer_session'])){

			$this->customer = unserialize($_SESSION['weS']['customer_session']);
		}

		if(isset($_REQUEST['sid'])){
			$this->customer = new weCustomer(addslashes($_REQUEST['sid']));
			$_SESSION['weS']['customer_session'] = serialize($this->customer);
		}
		if(is_array($this->customer->persistent_slots)){
			foreach($this->customer->persistent_slots as $key => $val){
				$varname = $val;
				if($varname == 'LoginDenied'){
					if(isset($_REQUEST[$varname])){
						$this->customer->{$val} = '1';
					} elseif(isset($_REQUEST['Username'])){
						$this->customer->{$val} = '0';
					}
				} elseif($varname == 'Password' && isset($_REQUEST[$varname]) && $_REQUEST[$varname] == weCustomer::NOPWD_CHANGE){
					//keep old pwd
				} elseif(isset($_REQUEST[$varname])){
					$this->customer->{$val} = $_REQUEST[$varname];
				}
			}
		}

		if(isset($_REQUEST['page'])){
			$this->page = $_REQUEST['page'];
		}

		if(isset($_REQUEST['pnt']))
			if($_REQUEST['pnt'] == 'sort_admin'){

				$counter = (isset($_REQUEST['counter']) ? $_REQUEST['counter'] : -1);

				if($counter > -1){
					$this->settings->SortView = array();
				}

				for($i = 0; $i < $counter; $i++){

					if(isset($_REQUEST['sort_' . $i]) && $_REQUEST['sort_' . $i] != ''){
						$sort_name = $_REQUEST['sort_' . $i];
					} else{
						$sort_name = g_l('modules_customer', '[sort_name]') . '_' . $i;
					}

					$fcounter = (isset($_REQUEST['fcounter_' . $i]) ? $_REQUEST['fcounter_' . $i] : 1);

					if($fcounter > -1){
						$this->settings->SortView[$sort_name] = array();
					}
					for($j = 0; $j < $fcounter; $j++){
						$new = array();
						if(isset($_REQUEST['branch_' . $i . '_' . $j])){
							$new['branch'] = $_REQUEST['branch_' . $i . '_' . $j];
						}
						if(isset($_REQUEST['field_' . $i . '_' . $j])){
							$new['field'] = ($new['branch'] == g_l('modules_customer', '[common]') ?
									str_replace(g_l('modules_customer', '[common]') . '_', '', $_REQUEST['field_' . $i . '_' . $j]) :
									$_REQUEST['field_' . $i . '_' . $j]);
						}
						if(isset($_REQUEST['function_' . $i . '_' . $j])){
							$new['function'] = $_REQUEST['function_' . $i . '_' . $j];
						}
						if(isset($_REQUEST['order_' . $i . '_' . $j])){
							$new['order'] = $_REQUEST['order_' . $i . '_' . $j];
						}
						$this->settings->SortView[$sort_name][$j] = $new;
					}
				}
			}
	}

	function getFieldProperties($field){
		$ret = array();

		//if(preg_match(g_l('modules_customer','[other]'),$field)) $field=str_replace(g_l('modules_customer','[other]')."_","",$field);

		$props = $this->customer->getFieldDbProperties($field);

		if(isset($props['Field'])){
			$ret['name'] = $this->customer->transFieldName($props['Field'], $branch);
		}
		if(isset($props['Type'])){
			$ret['type'] = $this->settings->getFieldType($props['Field']);
		}

		$ret['default'] = (isset($this->settings->FieldAdds[$field]['default']) ? $this->settings->FieldAdds[$field]['default'] : '');

		return $ret;
	}

	// field - contains full field name with branche
	// branch - branch name
	// field_name - field name without branch name
	// field_default - predefined values

	function saveField($field, $branch, $field_name, $field_type, $field_default){
		if($branch == g_l('modules_customer', '[common]')){
			return self::ERR_SAVE_BRANCH;
		}

		if($branch == g_l('modules_customer', '[other]')){
			$field = str_replace(g_l('modules_customer', '[other]') . '_', '', $field);
		}
		if($field_name == ''){
			return self::ERR_SAVE_FIELD_NOT_EMPTY;
		}

		$h = $this->customer->getFieldDbProperties($field);

		$new_field_name = (($branch && $branch != g_l('modules_customer', '[other]')) ? $branch . '_' : '') . $field_name;

		if(preg_match('|[^a-z0-9\_]|i', $new_field_name)){
			return self::ERR_SAVE_FIELD_INVALID;
		}

		if($field != $new_field_name && count($this->customer->getFieldDbProperties($new_field_name))){
			return self::ERR_SAVE_FIELD_EXISTS;
		}

		if($this->customer->isProperty($field) || $this->customer->isProtected($field) || $this->customer->isProperty($new_field_name) || $this->customer->isProtected($new_field_name)){
			return self::ERR_SAVE_PROPERTY;
		}
		if($branch == g_l('modules_customer', '[other]')){
			if($this->settings->isReserved($new_field_name)){
				return self::ERR_SAVE_PROPERTY;
			}
		}

		if(count($h)){
			$this->settings->removeFieldAdd($field);
		}
		$this->settings->storeFieldAdd($new_field_name, 'default', $field_default);
		$this->settings->storeFieldAdd($new_field_name, 'type', $field_type);


		$this->db->query('ALTER TABLE ' . CUSTOMER_TABLE . ' ' . ((count($h)) ? 'CHANGE ' . $field : 'ADD') . ' ' . $new_field_name . ' ' . $this->settings->getDbType($field_type, $new_field_name) . ' NOT NULL;');

		$this->settings->save();
	}

	function deleteField($field){
		$h = $this->customer->getFieldDbProperties($field);

		if(count($h)){
			$this->db->query('ALTER TABLE ' . $this->customer->table . ' DROP ' . $field);
		}

		$this->settings->removeFieldAdd($field);

		$this->settings->save();
	}

	function saveBranch($old_branch, $new_branch){
		$h = $this->customer->getFieldsDbProperties();
		foreach($h as $k => $v){
			if(strpos($k, $old_branch) !== false){
				$banche = '';
				$fieldname = $this->customer->transFieldName($k, $banche);
				if($banche == $old_branch && $fieldname != ''){
					$this->db->query('ALTER TABLE ' . $this->customer->table . ' CHANGE ' . $k . ' ' . $new_branch . '_' . $fieldname . ' ' . $v['Type'] . (!empty($v["Default"])? " DEFAULT '" . $v["Default"]."'" :''). " NOT NULL;");
				}
			}
		}

		$this->settings->renameFieldAdds($old_branch, $new_branch);
		$this->settings->save();
	}

	function new_array_splice(&$a, $start, $len = 1){
		$ks = array_keys($a);
		$k = array_search($start, $ks);
		if($k !== false){
			$ks = array_splice($ks, $k, $len);
			foreach($ks as $k){
				unset($a[$k]);
			}
		}
	}

	function getSearchResults($keyword, $res_num = 0){
		if(!$res_num){
			$res_num = $this->settings->getMaxSearchResults();
		}

		$arr = explode(' ', strToLower($keyword));
		$sWhere = '';
		$ranking = '0';

		$first = '';
		$array = array();

		$array['AND'] = array();
		$array['OR'] = array();
		$array['AND NOT'] = array();

		$array['AND'][] = $arr[0];

		for($i = 1; $i < count($arr); $i++){
			if(($arr[$i] == 'and') || ($arr[$i] == 'or') || ($arr[$i] == 'not')){
				switch($arr[$i]){
					case 'not':
						$i++;
						$array['AND NOT'][count($array['NOT'])] = $arr[$i];
						break;
					case 'and':
						$i++;
						$array['AND'][count($array['AND'])] = $arr[$i];
						break;
					case'or':
						$i++;
						$array['OR'][count($array['OR'])] = $arr[$i];
						break;
					default:
						$array['AND'][count($array['AND'])] = $arr[$i];
				}
			}
		}
		$main_condition = '';
		$condition = '';
		$op = false;

		foreach($array as $ak => $av){
			foreach($av as $value){
				$conditionarr = array();
				foreach($this->customer->persistent_slots as $field){
					if(!$this->customer->isProtected($field) && $field != "Password")
						$conditionarr[] = "$field LIKE '%$value%'";
				}
				if($condition != ''){
					$condition.=" $ak (" . implode(' OR ', $conditionarr) . ')';
				} else{
					$condition.=' (' . implode(' OR ', $conditionarr) . ')';
				}
			}
		}

		if($condition != ''){
			$condition = ' WHERE ' . $condition;
		}
		$condition.=' ORDER BY Username';

		$this->db->query('SELECT ID, CONCAT(Username, " (",Forename," ",Surname,")") AS user FROM ' . $this->db->escape($this->customer->table) . $condition . " LIMIT 0,$res_num");

		$result = array();
		while($this->db->next_record()) {
			$result[$this->db->f('ID')] = oldHtmlspecialchars($this->db->f('user'));
		}

		return $result;
	}

}

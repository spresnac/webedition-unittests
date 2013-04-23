<?php

/**
 * webEdition CMS
 *
 * $Rev: 5872 $
 * $Author: mokraemer $
 * $Date: 2013-02-23 12:01:09 +0100 (Sat, 23 Feb 2013) $
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
class weVotingFrames extends weModuleFrames{

	var $View;
	var $_space_size = 150;
	var $_text_size = 75;
	var $_width_size = 535;

	function __construct(){
		parent::__construct(WE_VOTING_MODULE_DIR . "edit_voting_frameset.php");
		$this->Tree = new weVotingTree();
		$this->View = new weVotingView(WE_VOTING_MODULE_DIR . "edit_voting_frameset.php", "top.content");
		$this->setupTree(VOTING_TABLE, "top.content", "top.content.resize.left.tree", "top.content.cmd");
		$this->module = "voting";
	}

	function getHTML($what){
		switch($what){
			case "frameset":
				print $this->getHTMLFrameset();
				break;
			case "header":
				print $this->getHTMLHeader();
				break;
			case "resize":
				print $this->getHTMLResize();
				break;
			case "left":
				print $this->getHTMLLeft();
				break;
			case "right":
				print $this->getHTMLRight();
				break;
			case "editor":
				print $this->getHTMLEditor();
				break;
			case "edheader":
				print $this->getHTMLEditorHeader();
				break;
			case "edbody":
				print $this->getHTMLEditorBody();
				break;
			case "edfooter":
				print $this->getHTMLEditorFooter();
				break;
			case "cmd":
				print $this->getHTMLCmd();
				break;
			case "treeheader":
				print $this->getHTMLTreeHeader();
				break;
			case "treefooter":
				print $this->getHTMLTreeFooter();
				break;
			case "export_csv":
				print $this->getHTMLExportCsvMessage();
				break;
			case "exportGroup_csv":
				print $this->getHTMLExportGroupCsvMessage();
				break;
			case "reset_ipdata":
				print $this->getHTMLResetIPData();
				break;
			case "reset_logdata":
				print $this->getHTMLResetLogData();
				break;
			case "show_log": if($this->View->voting->LogDB){
					print $this->getHTMLShowLogNew();
				} else{
					print $this->getHTMLShowLogOld();
				}break;
			case "delete_log":
				print $this->getHTMLDeleteLog();
				break;
			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset(){
		$this->View->voting->clearSessionVars();
		return weModuleFrames::getHTMLFrameset();
	}

	function getJSCmdCode(){
		return $this->View->getJSTop() . we_html_element::jsElement($this->Tree->getJSMakeNewEntry());
	}

	function getHTMLEditorHeader(){
		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$we_tabs = new we_tabs();

		$we_tabs->addTab(new we_tab("#", g_l('modules_voting', '[property]'), '((' . $this->topFrame . '.activ_tab==1) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('1');", array("id" => "tab_1")));
		if(!$this->View->voting->IsFolder){
			$we_tabs->addTab(new we_tab("#", g_l('modules_voting', '[inquiry]'), '((' . $this->topFrame . '.activ_tab==2) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('2');", array("id" => "tab_2")));
			$we_tabs->addTab(new we_tab("#", g_l('modules_voting', '[options]'), '((' . $this->topFrame . '.activ_tab==3) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('3');", array("id" => "tab_3")));

			if($this->View->voting->ID)
				$we_tabs->addTab(new we_tab("#", g_l('modules_voting', '[result]'), '((' . $this->topFrame . '.activ_tab==4) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('4');", array("id" => "tab_4")));
		}

		$we_tabs->onResize();
		$tabsBody = $we_tabs->getJS();
		$tabsHead = $we_tabs->getHeader('', 22) .
			we_html_element::jsElement('
				function setTab(tab) {
					parent.edbody.toggle("tab"+' . $this->topFrame . '.activ_tab);
					parent.edbody.toggle("tab"+tab);
					' . $this->topFrame . '.activ_tab=tab;
					self.focus();
				}
				' . ($this->View->voting->ID ? '' : $this->topFrame . '.activ_tab=1;')
		);


		$table = new we_html_table(array("width" => "3000", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 3, 1);

		$table->setCol(0, 0, array(), we_html_tools::getPixel(1, 3));

		$table->setCol(1, 0, array("valign" => "top", "class" => "small"), we_html_tools::getPixel(15, 2) .
			we_html_element::htmlB(
				($this->View->voting->IsFolder ? g_l('modules_voting', '[group]') : g_l('modules_voting', '[voting]')) . ':&nbsp;' . $this->View->voting->Text .
				we_html_tools::getPixel(1600, 19)
			)
		);

		$extraJS = 'document.getElementById("tab_"+top.content.activ_tab).className="tabActive";';
		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0", "onload" => "setFrameSize()", "onresize" => "setFrameSize()"), '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", ($this->View->voting->IsFolder ? g_l('modules_voting', '[group]') : g_l('modules_voting', '[voting]'))) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $this->View->voting->Path) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) .
				$we_tabs->getHTML() .
				'</div>' . we_html_element::jsElement($extraJS)
		);

		return $this->getHTMLDocument($body, $tabsHead);
	}

	function getHTMLEditorBody(){

		$hiddens = array('cmd' => 'edit_voting', 'pnt' => 'edbody', 'vernr' => (isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0));

		if(isset($_REQUEST["home"]) && $_REQUEST["home"]){
			$hiddens["cmd"] = "home";
			$GLOBALS["we_print_not_htmltop"] = true;
			$GLOBALS["we_head_insert"] = $this->View->getJSProperty();
			$GLOBALS["we_body_insert"] = we_html_element::htmlForm(array("name" => "we_form"), $this->View->getCommonHiddens($hiddens) . we_html_element::htmlHidden(array("name" => "home", "value" => "0"))
			);
			$GLOBALS["mod"] = "voting";
			ob_start();
			include(WE_MODULES_PATH . 'home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
			return
				we_html_element::jsElement('
			' . $this->topFrame . '.resize.right.editor.edheader.location = "' . $this->frameset . '?pnt=edheader&home=1";
			' . $this->topFrame . '.resize.right.editor.edfooter.location = "' . $this->frameset . '?pnt=edfooter&home=1";
			') . $out;
		}

		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onLoad" => "loaded=1;setMultiEdits();", "onunload" => "doUnload()"), we_html_element::htmlForm(array("name" => "we_form", "onsubmit" => "return false"), $this->View->getCommonHiddens($hiddens) . $this->getHTMLProperties()));

		return $this->getHTMLDocument($body, $this->View->getJSProperty());
	}

	function getHTMLEditorFooter(){

		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		$table1 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "3000"), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1600, 10));

		$table2 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "300"), 1, 2);
		$table2->setRow(0, array("valign" => "middle"));
		$table2->setCol(0, 0, array("nowrap" => null), we_html_tools::getPixel(5, 5));
		$table2->setCol(0, 1, array("nowrap" => null), we_button::create_button("save", "javascript:we_save()", true, 100, 22, '', '', (!we_hasPerm('NEW_VOTING') && !we_hasPerm('EDIT_VOTING')))
		);


		return $this->getHTMLDocument(
				we_html_element::jsElement('
					function we_save() {
						top.content.we_cmd("save_voting");
					}') .
				we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0"), we_html_element::htmlForm(array(), $table1->getHtml() . $table2->getHtml())
				)
		);
	}

	function getPercent($total, $value, $precision = 0){
		$result = ($total ? round(($value * 100) / $total, $precision) : 0);
		return we_util_Strings::formatNumber($result, strtolower($GLOBALS['WE_LANGUAGE']));
	}

	function getHTMLVariant(){
		$prefix = '';
		$del_but = addslashes(we_html_element::htmlImg(array('src' => BUTTONS_DIR . 'btn_function_trash.gif', 'onclick' => 'javascript:top . content . setHot(); #####placeHolder#####', 'style' => 'cursor: pointer; width: 27px;')));
		$del_but1 = addslashes(we_html_element::htmlImg(array('src' => BUTTONS_DIR . 'btn_function_trash.gif', 'onclick' => 'javascript:top.content.setHot();if(answers_edit.itemCount>answers_edit.minCount) #####placeHolder#####; else callAnswerLimit();', 'style' => 'cursor: pointer; width: 27px;')));

		$_Imagecmd = addslashes("we_cmd('openDocselector',document.we_form.elements['" . $prefix . "UrlID'].value,'" . FILE_TABLE . "','document.we_form.elements[\\'" . $prefix . "UrlID\\'].value','document.we_form.elements[\\'" . $prefix . "UrlIDPath\\'].value','opener." . $this->topFrame . ".mark()','" . session_id() . "',0,'text/webedition'," .
			(we_hasPerm('CAN_SELECT_OTHER_USERS_FILES') ? 0 : 1) . ')');

		$sel_but = addslashes(we_html_element::htmlImg(array('src' => BUTTONS_DIR . 'btn_function_trash.gif', 'onclick' => 'javascript:top.content.setHot();', 'style' => 'cursor: pointer; width: 27px;')));

		$js = we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js?' . time()) .
			we_html_element::jsScript(JS_DIR . 'utils/multi_editMulti.js?' . time());

		$variant_js =
			' function callAnswerLimit() {
				' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[answer_limit]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}

			function setMultiEdits() {';

		if($this->View->voting->IsFolder == 0){
			$variant_js .=
				'question_edit = new multi_edit("question",document.we_form,1,"",' . ($this->_width_size) . ',true);
				answers_edit = new multi_editMulti("answers",document.we_form,0,"' . $del_but1 . '",' . ($this->_width_size - 32) . ',true);
				answers_edit.SetImageIDText("' . g_l('modules_voting', '[imageID_text]') . '");
				answers_edit.SetMediaIDText("' . g_l('modules_voting', '[mediaID_text]') . '");
				answers_edit.SetSuccessorIDText("' . g_l('modules_voting', '[successorID_text]') . '");';

			for($j = 0; $j < count($this->View->voting->QASet[0]['answers']); $j++){
				$variant_js .= 'answers_edit.addItem("2");';
			}

			foreach($this->View->voting->QASet as $variant => $value){
				$variant_js .=
					'question_edit.addVariant();
				   answers_edit.addVariant();';
				foreach($value as $k => $v){
					switch($k){
						case 'question':
							$variant_js .= 'question_edit.setItem("' . $variant . '",0,"' . $v . '");';
							break;
						case 'answers':
							foreach($v as $akey => $aval){
								if((isset($this->View->voting->QASetAdditions[$variant]) && isset($this->View->voting->QASetAdditions[$variant]['imageID'][$akey]))){
									$aval2 = $this->View->voting->QASetAdditions[$variant]['imageID'][$akey];
									$aval3 = $this->View->voting->QASetAdditions[$variant]['mediaID'][$akey];
									$aval4 = $this->View->voting->QASetAdditions[$variant]['successorID'][$akey];
								} else{
									$aval2 = $aval3 = $aval4 = '';
								}
								$variant_js .=
									'answers_edit.setItem("' . $variant . '","' . $akey . '","' . $aval . '");
								answers_edit.setItemImageID("' . $variant . '","' . $akey . '","' . $aval2 . '");
								answers_edit.setItemMediaID("' . $variant . '","' . $akey . '","' . $aval3 . '");
								answers_edit.setItemSuccessorID("' . $variant . '","' . $akey . '","' . $aval4 . '");';
							}
							break;
					}
				}
			}

			$variant_js .=
				'answers_edit.delRelatedItems=true;
				question_edit.showVariant(0);
				answers_edit.showVariant(0);
				question_edit.showVariant(' . (isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0) . ');
				answers_edit.showVariant(' . (isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0) . ');
				answers_edit.SetMinCount(' . ($this->View->voting->AllowFreeText ? 1 : 2) . ');
				answers_edit.' . ($this->View->voting->AllowImages ? 'show' : 'hide') . 'Images();
				answers_edit.' . ($this->View->voting->AllowMedia ? 'show' : 'hide') . 'Media();
			  answers_edit.' . ($this->View->voting->AllowSuccessors ? 'show' : 'hide') . 'Successors();';
		}


		$variant_js .=
			' owners_label = new multi_edit("owners",document.we_form,0,"' . $del_but . '",' . ($this->_width_size - 10) . ',false);
			owners_label.addVariant();';
		if(is_array($this->View->voting->Owners)){
			foreach($this->View->voting->Owners as $owner){
				$foo = f('SELECT IsFolder FROM ' . USER_TABLE . ' WHERE ID=' . $owner, 'IsFolder', $this->db);

				$variant_js .=
					' owners_label.addItem();
					owners_label.setItem(0,(owners_label.itemCount-1),"' . ($foo ? $this->View->group_pattern : $this->View->item_pattern) . id_to_path($owner, USER_TABLE) . '");';
			}
		}
		$variant_js .=
			' owners_label.showVariant(0);
			iptable_label = new multi_edit("iptable",document.we_form,0,"' . $del_but . '",' . ($this->_width_size - 10) . ',false);
			iptable_label.addVariant();';

		if(is_array($this->View->voting->BlackList)){
			foreach($this->View->voting->BlackList as $ip){

				$variant_js .=
					'top.content.setHot();
					iptable_label.addItem();
					iptable_label.setItem(0,(iptable_label.itemCount-1),"' . $ip . '");';
			}
		}
		$variant_js .=
			'iptable_label.showVariant(0);
	}';

		return $js . we_html_element::jsElement($variant_js);
	}

	function getHTMLTab1(){
		$parts = array();
		$yuiSuggest = & weSuggest::getInstance();
		array_push($parts, array(
			'headline' => g_l('modules_voting', '[property]'),
			'html' => we_html_element::htmlHidden(array('name' => 'owners_name', 'value' => '')) .
			we_html_element::htmlHidden(array('name' => 'owners_count', 'value' => '0')) .
			we_html_element::htmlHidden(array('name' => 'newone', 'value' => ($this->View->voting->ID == 0 ? 1 : 0))) .
			we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Text', '', $this->View->voting->Text, '', 'style="width: ' . $this->_width_size . '" id="yuiAcInputPathName" onchange="top.content.setHot();" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()"'), g_l('modules_voting', '[headline_name]')) .
			we_html_element::htmlBr() .
			$this->getHTMLDirChooser() .
			$yuiSuggest->getYuiJsFiles() . $yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs() .
			we_html_element::htmlBr() .
			(!$this->View->voting->IsFolder ? we_html_tools::htmlFormElementTable(we_html_tools::getDateInput2('PublishDate%s', $this->View->voting->PublishDate, false, '', 'top.content.setHot();'), g_l('modules_voting', '[headline_publish_date]')) : ''),
			'space' => $this->_space_size,
			'noline' => 1)
		);

		array_push($parts, array(
			'headline' => '',
			'html' => we_forms::checkboxWithHidden($this->View->voting->RestrictOwners ? true : false, 'RestrictOwners', g_l('modules_voting', '[limit_access]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'ownersTable\')'),
			'space' => $this->_space_size,
			'noline' => 1
			)
		);

		$table = new we_html_table(array('id' => 'ownersTable', 'style' => 'display: ' . ($this->View->voting->RestrictOwners ? 'block' : 'none') . ';', 'cellpadding' => 2, 'cellspacing' => 2, "border" => 0), 3, 2);
		$table->setColContent(0, 0, we_html_tools::getPixel(10, 5));
		$table->setCol(0, 1, array('colspan' => '2', 'class' => 'defaultfont'), g_l('modules_voting', '[limit_access_text]'));
		$table->setColContent(1, 1, we_html_element::htmlDiv(array('id' => 'owners', 'class' => 'blockWrapper', 'style' => 'width: ' . ($this->_width_size - 10) . 'px; height: 60px; border: #AAAAAA solid 1px;')));
		$idname = 'owner_id';
		$textname = 'owner_text';
		//javascript:top.content.setHot(); we_cmd('browse_users','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','',document.forms[0].elements['$idname'].value,'fillIDs();opener.we_cmd(\\'add_owner\\',top.allPaths,top.allIsFolder)','','',1);
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
		$wecmdenc5 = we_cmd_enc("fillIDs();opener.we_cmd('add_owner',top.allPaths,top.allIsFolder);");
		$table->setCol(2, 0, array('colspan' => '2', 'align' => 'right'), we_html_element::htmlHidden(array('name' => $idname, 'value' => '')) .
			we_html_element::htmlHidden(array('name' => $textname, 'value' => '')) .
			we_button::create_button("add", "javascript:top.content.setHot(); we_cmd('browse_users','" . $wecmdenc1 . "','" . $wecmdenc2 . "','',document.forms[0].elements['$idname'].value,'" . $wecmdenc5 . "','','',1);")
		);

		array_push($parts, array(
			'headline' => '',
			'html' => $table->getHtml(),
			'space' => $this->_space_size)
		);

		if($this->View->voting->IsFolder){

			$table = new we_html_table(array('id' => 'LogGroupData', 'cellpadding' => 2, 'cellspacing' => 2, "border" => 0), 1, 2);
			$table->setColContent(0, 0, we_html_tools::getPixel(10, 5));
			$table->setColContent(0, 1, we_button::position_yes_no_cancel(
					we_button::create_button('logbook', 'javascript:we_cmd(\'show_log\')'), we_button::create_button('delete', 'javascript:we_cmd(\'delete_log\')'), null
				)
			);

			array_push($parts, array(
				'headline' => g_l('modules_voting', '[control]'),
				'html' => $table->getHtml(),
				'space' => $this->_space_size,
				'noline' => 1
				)
			);


			$ok = we_button::create_button("export", "javascript:we_cmd('exportGroup_csv')");

			$export_box = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 12, 1);

			$export_box->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
			$export_box->setCol(1, 0, array(), we_html_tools::htmlFormElementTable($this->formFileChooser($this->_width_size - 130, 'csv_dir', '/', '', 'folder'), g_l('export', '[dir]')));
			$export_box->setCol(2, 0, array(), we_html_tools::getPixel(5, 5));

			$lineend = new we_html_select(array('name' => 'csv_lineend', 'size' => '1', 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
			$lineend->addOption('windows', g_l('export', '[windows]'));
			$lineend->addOption('unix', g_l('export', "[unix]"));
			$lineend->addOption('mac', g_l('export', "[mac]"));

			$_charsetHandler = new charsetHandler();
			$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
			$charset = $GLOBALS['WE_BACKENDCHARSET'];
			//$GLOBALS['weDefaultCharset'] = get_value("default_charset");
			$_importCharset = we_html_tools::htmlTextInput('the_charset', 8, '', 255, "", "text", 200);
			$_importCharsetChooser = we_html_tools::htmlSelect("ImportCharsetSelect", $_charsets, 1, '', false, "onChange=\"document.forms[0].elements['the_charset'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;\"", "value", 325, "defaultfont", false);
			$import_Charset = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_importCharset . '</td><td>' . $_importCharsetChooser . '</td></tr></table>';



			$delimiter = new we_html_select(array('name' => 'csv_delimiter', 'size' => '1', 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
			$delimiter->addOption(';', g_l('export', '[semicolon]'));
			$delimiter->addOption(',', g_l('export', '[comma]'));
			$delimiter->addOption(':', g_l('export', '[colon]'));
			$delimiter->addOption('\t', g_l('export', '[tab]'));
			$delimiter->addOption(' ', g_l('export', '[space]'));

			$enclose = new we_html_select(array('name' => 'csv_enclose', 'size' => '1', 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
			$enclose->addOption(0, g_l('export', '[double_quote]'));
			$enclose->addOption(1, g_l('export', '[single_quote]'));

			$export_box->setCol(3, 0, array("class" => "defaultfont"), we_html_tools::htmlFormElementTable($lineend->getHtml(), g_l('export', '[csv_lineend]')));
			$export_box->setColContent(4, 0, we_html_tools::getPixel(5, 5));
			$export_box->setCol(5, 0, array("class" => "defaultfont"), we_html_tools::htmlFormElementTable($import_Charset, g_l('modules_voting', '[csv_charset]')));
			$export_box->setColContent(6, 0, we_html_tools::getPixel(5, 5));
			$export_box->setColContent(7, 0, we_html_tools::htmlFormElementTable($delimiter->getHtml(), g_l('export', '[csv_delimiter]')));
			$export_box->setColContent(8, 0, we_html_tools::getPixel(5, 5));
			$export_box->setColContent(9, 0, we_html_tools::htmlFormElementTable($enclose->getHtml(), g_l('export', '[csv_enclose]')));
			$export_box->setColContent(10, 0, we_html_tools::getPixel(5, 15));
			$export_box->setCol(11, 0, array("nowrap" => null), we_button::create_button_table(array($ok))
			);



			array_push($parts, array(
				"headline" => g_l('modules_voting', '[export]'),
				"html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[export_txt]'), 2, $this->_width_size) .
				$export_box->getHtml(),
				"space" => $this->_space_size)
			);


			return $parts;
		}

		$activeTime = new we_html_select(array('name' => 'ActiveTime', 'class' => 'weSelect', 'size' => '1', 'style' => 'width:200', 'onchange' => 'top.content.setHot(); if(this.value!=0) setVisible(\'valid\',true); else setVisible(\'valid\',false);'));
		$activeTime->addOption((0), g_l('modules_voting', '[always]'));
		$activeTime->addOption((1), g_l('modules_voting', '[until]'));
		$activeTime->selectOption($this->View->voting->ActiveTime);

		$table = new we_html_table(array('cellpadding' => 2, 'cellspacing' => 2, "border" => 0), 4, 2);
		$table->setCol(0, 0, array('colspan' => '2'), we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[valid_txt]'), 2, $this->_width_size, false, 133));
		$table->setCol(1, 0, array('colspan' => '2'), we_forms::checkboxWithHidden($this->View->voting->Active ? true : false, 'Active', g_l('modules_voting', '[active_till]'), false, 'defaultfont', 'toggle(\'activetime\');if(!this.checked) setVisible(\'valid\',false); else if(document.we_form.ActiveTime.value==1) setVisible(\'valid\',true); else setVisible(\'valid\',false);'));

		$table->setColContent(2, 1, we_html_element::htmlDiv(array('id' => 'activetime', 'style' => 'display: ' . ($this->View->voting->Active ? 'block' : 'none') . ';'), $activeTime->getHtml()
			)
		);
		$table->setColContent(3, 1, we_html_element::htmlDiv(array('id' => 'valid', 'style' => 'display: ' . ($this->View->voting->Active && $this->View->voting->ActiveTime ? 'block' : 'none') . ';'), we_html_tools::htmlFormElementTable(we_html_tools::getDateInput2('Valid%s', $this->View->voting->Valid, false, '', 'top.content.setHot();'), "")
			)
		);

		array_push($parts, array(
			'headline' => g_l('modules_voting', '[valid]'),
			'html' => $table->getHtml(),
			'space' => $this->_space_size,
			'noline' => 1)
		);


		return $parts;
	}

	function getHTMLTab2(){
		$parts = array();

		$successor_box = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 2, 1);

		$successor_box->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
		$successor_box->setCol(1, 0, array(), we_html_tools::htmlFormElementTable($this->formFileChooser($this->_width_size - 130, 'Successor', '/', '', ''), g_l('modules_voting', '[voting-successor]')));


		if($this->View->voting->AllowSuccessor){
			$displaySuccessor = 'block';
		} else{
			$displaySuccessor = 'none';
		}


		array_push($parts, array(
			'headline' => g_l('modules_voting', '[headline_datatype]'),
			'html' =>
			we_forms::checkboxWithHidden($this->View->voting->IsRequired ? true : false, 'IsRequired', g_l('modules_voting', '[IsRequired]'), false, 'defaultfont', 'top.content.setHot();') .
			we_forms::checkboxWithHidden($this->View->voting->AllowFreeText ? true : false, 'AllowFreeText', g_l('modules_voting', '[AllowFreeText]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleMinCount();') .
			we_forms::checkboxWithHidden($this->View->voting->AllowImages ? true : false, 'AllowImages', g_l('modules_voting', '[AllowImages]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleImages();') .
			we_forms::checkboxWithHidden($this->View->voting->AllowMedia ? true : false, 'AllowMedia', g_l('modules_voting', '[AllowMedia]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleMedia();') .
			we_forms::checkboxWithHidden($this->View->voting->AllowSuccessor ? true : false, 'AllowSuccessor', g_l('modules_voting', '[AllowSuccessor]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'Successor\')') .
			we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('Successor', '', $this->View->voting->Successor, '', 'style="width: ' . $this->_width_size . ';display:' . $displaySuccessor . '" id="Successor" onchange="top.content.setHot();" '), '') .
			we_forms::checkboxWithHidden($this->View->voting->AllowSuccessors ? true : false, 'AllowSuccessors', g_l('modules_voting', '[AllowSuccessors]'), false, 'defaultfont', 'top.content.setHot();answers_edit.toggleSuccessors();')
			,
			'space' => $this->_space_size
			)
		);


		$select = new we_html_select(array('name' => 'selectVar', 'class' => 'weSelect', 'onchange' => 'top.content.setHot();question_edit.showVariant(this.value);answers_edit.showVariant(this.value);document.we_form.vernr.value=this.value;refreshTexts();', 'style' => 'width:' . ($this->_width_size - 64)));
		foreach($this->View->voting->QASet as $variant => $value){
			$select->addOption($variant, g_l('modules_voting', '[variant]') . ' ' . ($variant + 1));
		}
		$select->selectOption(isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0);

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 1, 3);
		$table->setColContent(0, 0, $select->getHtml());
		$table->setColContent(0, 1, we_button::create_button("image:btn_function_plus", "javascript:top.content.setHot();question_edit.addVariant();answers_edit.addVariant();question_edit.showVariant(question_edit.variantCount-1);answers_edit.showVariant(answers_edit.variantCount-1);document.we_form.selectVar.options[document.we_form.selectVar.options.length] = new Option('" . g_l('modules_voting', '[variant]') . " '+question_edit.variantCount,question_edit.variantCount-1,false,true);"));
		$table->setColContent(0, 2, we_button::create_button("image:btn_function_trash", "javascript:top.content.setHot();if(question_edit.variantCount>1){ question_edit.deleteVariant(document.we_form.selectVar.selectedIndex);answers_edit.deleteVariant(document.we_form.selectVar.selectedIndex);document.we_form.selectVar.options.length--;document.we_form.selectVar.selectedIndex=question_edit.currentVariant;refreshTexts();} else {" . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[variant_limit]'), we_message_reporting::WE_MESSAGE_ERROR) . "}"));
		$table->setColAttributes(0, 1, array("style" => "padding:0 5px;"));
		$selectCode = $table->getHtml();

		$table = new we_html_table(array("cellpadding" => 0, "cellspacing" => 0, "border" => 0), 5, 1);

		$table->setColContent(0, 0, $selectCode);
		$table->setColContent(1, 0, we_html_tools::getPixel(10, 7));
		$table->setColContent(2, 0, we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('id' => 'question')), g_l('modules_voting', '[inquiry_question]')));
		$table->setColContent(3, 0, we_html_tools::getPixel(10, 7));
		$table->setColContent(4, 0, we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('id' => 'answers')), g_l('modules_voting', '[inquiry_answers]')));

		array_push($parts, array(
			'headline' => g_l('modules_voting', '[headline_data]'),
			'html' => we_html_element::htmlHidden(array('name' => 'question_name', 'value' => '')) .
			we_html_element::htmlHidden(array('name' => 'variant_count', 'value' => '0')) .
			we_html_element::htmlHidden(array('name' => 'answers_name', 'value' => '')) .
			we_html_element::htmlHidden(array('name' => 'item_count', 'value' => '0')) .
			we_html_element::htmlHidden(array('name' => 'iptable_name', 'value' => '')) .
			we_html_element::htmlHidden(array('name' => 'iptable_count', 'value' => '0')) .
			$table->getHtml() .
			we_button::create_button("image:btn_function_plus", "javascript:top.content.setHot();answers_edit.addItem()")
			,
			'space' => $this->_space_size
			)
		);

		return $parts;
	}

	function getHTMLTab3(){
		$parts = array();


		$selectTime = new we_html_select(array('name' => 'RevoteTime', 'class' => 'weSelect', 'size' => '1', 'style' => 'width:200', 'onchange' => 'top.content.setHot(); if(this.value==0) setVisible(\'method_table\',false); else setVisible(\'method_table\',true);'));
		$selectTime->addOption((-1), g_l('modules_voting', '[never]'));
		$selectTime->addOption((86400), g_l('modules_voting', '[one_day]'));
		$selectTime->addOption((3600), g_l('modules_voting', '[one_hour]'));
		$selectTime->addOption((1800), g_l('modules_voting', '[thirthty_minutes]'));
		$selectTime->addOption((900), g_l('modules_voting', '[feethteen_minutes]'));
		$selectTime->addOption((0), g_l('modules_voting', '[always]'));
		$selectTime->selectOption($this->View->voting->RevoteTime);

		$table = new we_html_table(array('id' => 'method_table', 'style' => 'display: ' . ($this->View->voting->RevoteTime == 0 ? 'none' : 'block'), 'cellpadding' => 2, 'cellspacing' => 1, 'border' => 0), 10, 2);
		$table->setCol(0, 0, array('colspan' => 2), we_html_tools::htmlAlertAttentionBox(
				we_html_element::htmlB(g_l('modules_voting', '[cookie_method]')) . we_html_element::htmlBr() .
				g_l('modules_voting', '[cookie_method_help]') .
				we_html_element::htmlBr() . we_html_element::htmlB(g_l('modules_voting', '[ip_method]')) . we_html_element::htmlBr() .
				g_l('modules_voting', '[ip_method_help]'), 2, ($this->_width_size - 3), false, 100
			)
		);


		$table->setCol(2, 0, array('colspan' => 2), we_forms::radiobutton(1, ($this->View->voting->RevoteControl == 1 ? true : false), 'RevoteControl', g_l('modules_voting', '[cookie_method]'), true, "defaultfont", "top.content.setHot();"));

		$table->setColContent(3, 0, we_html_tools::getPixel(10, 5));
		$table->setColContent(3, 1, we_forms::checkboxWithHidden($this->View->voting->FallbackIp ? true : false, 'FallbackIp', g_l('modules_voting', '[fallback]'), false, "defaultfont", "top.content.setHot();"));

		$table->setColContent(4, 0, we_html_tools::getPixel(10, 10));

		$table->setCol(5, 0, array('colspan' => 2), we_forms::radiobutton(0, ($this->View->voting->RevoteControl == 0 ? true : false), 'RevoteControl', g_l('modules_voting', '[ip_method]'), true, "defaultfont", "top.content.setHot();"));

		$datasize = f('SELECT (LENGTH(Revote)+LENGTH(RevoteUserAgent)) AS Size FROM ' . VOTING_TABLE, 'Size', $this->db);

		$table->setColContent(6, 1, we_forms::checkboxWithHidden($this->View->voting->UserAgent ? true : false, 'UserAgent', g_l('modules_voting', '[save_user_agent]'), false, "defaultfont", "top.content.setHot();"));

		$table->setCol(7, 1, array('id' => 'delete_ip_data', 'style' => 'display: ' . ($datasize > 0 ? 'block' : 'none')), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('modules_voting', '[delete_ipdata_text]'), we_html_element::htmlSpan(array('id' => 'ip_mem_size'), $datasize)), 2, ($this->_width_size - 20), false, 100) .
			we_button::create_button('delete', 'javascript:we_cmd(\'reset_ipdata\')')
		);
		$table->setColContent(8, 0, we_html_tools::getPixel(10, 5));
		$table->setCol(9, 0, array('colspan' => 2), we_forms::radiobutton(2, ($this->View->voting->RevoteControl == 2 ? true : false), 'RevoteControl', g_l('modules_voting', '[userid_method]'), true, "defaultfont", "top.content.setHot();"));


		array_push($parts, array(
			'headline' => g_l('modules_voting', '[headline_revote]'),
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[time_after_voting_again_help]'), 2, $this->_width_size, false, 100) .
			we_html_element::htmlBr() .
			we_html_tools::htmlFormElementTable($selectTime->getHtml(), g_l('modules_voting', '[time_after_voting_again]')) .
			we_html_element::htmlBr() .
			$table->getHtml(),
			'space' => $this->_space_size
			)
		);

		$table = new we_html_table(array('id' => 'LogData', 'style' => 'display: ' . ($this->View->voting->Log ? 'block' : 'none') . ';', 'cellpadding' => 2, 'cellspacing' => 2, "border" => 0), 1, 2);
		$table->setColContent(0, 0, we_html_tools::getPixel(10, 5));
		$table->setColContent(0, 1, we_button::position_yes_no_cancel(
				we_button::create_button('logbook', 'javascript:we_cmd(\'show_log\')'), we_button::create_button('delete', 'javascript:we_cmd(\'delete_log\')'), null
			)
		);

		array_push($parts, array(
			'headline' => g_l('modules_voting', '[control]'),
			'html' => we_forms::checkboxWithHidden($this->View->voting->Log ? true : false, 'Log', g_l('modules_voting', '[voting_log]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'LogData\')') .
			$table->getHtml(),
			'space' => $this->_space_size,
			'noline' => 1
			)
		);

		array_push($parts, array(
			'headline' => '',
			'html' => we_forms::checkboxWithHidden($this->View->voting->RestrictIP ? true : false, 'RestrictIP', g_l('modules_voting', '[forbid_ip]'), false, 'defaultfont', 'top.content.setHot(); toggle(\'RestrictIPDiv\')'),
			'space' => $this->_space_size,
			'noline' => 1
			)
		);


		$table = new we_html_table(array('id' => 'RestrictIPDiv', 'style' => 'display: ' . ($this->View->voting->RestrictIP ? 'block' : 'none') . ';', 'cellpadding' => 2, 'cellspacing' => 2, "border" => 0), 2, 2);
		$table->setColContent(0, 0, we_html_tools::getPixel(10, 5));
		$table->setColContent(0, 1, we_html_element::htmlDiv(array('id' => 'iptable', 'class' => 'blockWrapper', 'style' => 'width: ' . ($this->_width_size - 10) . 'px; height: 60px; border: #AAAAAA solid 1px;padding: 5px;')));

		$table->setCol(1, 0, array('colspan' => '2', 'align' => 'right'), we_button::create_button_table(array(
				we_button::create_button("delete_all", "javascript:top.content.setHot(); removeAll()"),
				we_button::create_button("add", "javascript:top.content.setHot(); newIp()")
				)
			)
		);


		array_push($parts, array(
			'headline' => '',
			'html' => we_html_element::jsElement('

							function removeAll(){
								for(var i=0;i<iptable_label.itemCount+1;i++){
									iptable_label.delItem(i);
								}
							}

							function newIp(){
								var ip = prompt("' . g_l('modules_voting', '[new_ip_add]') . '","");


								var re = new RegExp("[a-zA-Z|,]");
								var m = ip.match(re);
								if(m != null){
									' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[not_valid_ip]'), we_message_reporting::WE_MESSAGE_ERROR) . '
									return;
								}

								var re = new RegExp("^(([0-2|\*]?[0-9|\*]{1,2}\.){3}[0-2|\*]?[0-9|\*]{1,2})");

								var m = ip.match(re);

								if(m != null){

									var p = ip.split(".");
									for (var i = 0; i < p.length; i++) {
								      var t = p[i];
								      t.replace("*","");
								      if(parseInt(t)>255) {
								      	' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[not_valid_ip]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								      	return false;
								      }
								    }

									iptable_label.addItem();
									iptable_label.setItem(0,(iptable_label.itemCount-1),ip);
									iptable_label.showVariant(0);
								} else {
									' . we_message_reporting::getShowMessageCall(g_l('modules_voting', '[not_valid_ip]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								}
							}
					') . $table->getHtml(),
			'space' => $this->_space_size)
		);

		return $parts;
	}

	function getHTMLTab4(){
		$parts = array();
		$content = "";

		$total_score = array_sum($this->View->voting->Scores);

		$version = isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0;

		$table = new we_html_table(array('cellpadding' => 3, 'cellspacing' => 0, 'border' => 0, 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'), 1, 5);
		if(isset($this->View->voting->QASet[$version])){
			$table->setCol(0, 0, array('colspan' => 5, 'class' => 'defaultfont'), we_html_element::htmlB(we_html_element::htmlSpan(array('id' => 'question_score'), oldHtmlspecialchars(stripslashes($this->View->voting->QASet[$version]['question'])))));
		}
		$i = 1;
		if(isset($this->View->voting->QASet[$version])){
			foreach($this->View->voting->QASet[$version]['answers'] as $key => $value){
				if(!isset($this->View->voting->Scores[$key]))
					$this->View->voting->Scores[$key] = 0;

				$percent = weVotingFrames::getPercent($total_score, $this->View->voting->Scores[$key], 2);

				$pb = new we_progressBar($percent);
				$pb->setName('item' . $key);
				$pb->setStudWidth(10);
				$pb->setStudLen(150);

				$table->addRow();
				$table->setRow($key + 1, array("id" => "row_scores_$key"));
				$table->setCol($i, 0, array('style' => 'width: ' . ($this->_width_size - 150) . 'px'), we_html_element::htmlSpan(array('id' => 'answers_score_' . $key), oldHtmlspecialchars(stripslashes($value))));
				$table->setColContent($i, 1, $pb->getJS() . $pb->getHTML());
				$table->setColContent($i, 2, '&nbsp;');
				$table->setColContent($i, 3, we_html_tools::htmlTextInput('scores_' . $key, 4, $this->View->voting->Scores[$key], '', 'id="scores_' . $key . '" onKeyUp="var r=parseInt(this.value);if(isNaN(r)) this.value=' . $this->View->voting->Scores[$key] . '; else{ this.value=r;document.we_form.scores_changed.value=1;}refreshTotal();"'));
				$i++;
			}
		}
		$table->addRow();
		$table->setColContent($i, 0, we_html_element::htmlB(g_l('modules_voting', '[total_voting]') . ':') . we_html_tools::hidden("updateScores", "false", array("id" => 'updateScores')));
		$table->setCol($i, 3, array('colspan' => 3), we_html_element::htmlB(we_html_element::htmlSpan(array('id' => 'total'), $total_score)));

		$butt = we_button::create_button("reset_score", "javascript:top.content.setHot();resetScores();");

		$js = we_html_element::jsElement('
			function resetScores(){
				if(confirm("' . g_l('modules_voting', '[result_delete_alert]') . '")) {
					for(var i=0;i<' . ($i - 1) . ';i++){
						document.we_form.elements["scores_"+i].value = 0;
					}
					document.we_form.scores_changed.value=1;
					refreshTotal();
				} else {}
			}

			function refreshTotal(){
				var total=0;
				for(var i=0;i<' . ($i - 1) . ';i++){
					total += parseInt(document.we_form.elements["scores_"+i].value);
				}

				var t = document.getElementById("total");
				t.innerHTML = total;

				for(var i=0;i<' . ($i - 1) . ';i++){
					if(total!=0){
						percent = Math.round((parseInt(document.we_form.elements["scores_"+i].value)/total) * 100);
					}
					else percent = 0;
					eval("setProgressitem"+i+"("+percent+");");
				}

			}

			function refreshTexts(){
				var t = document.getElementById("question_score");
				eval("t.innerHTML = document.we_form."+question_edit.name+"_item0.value");
				for(i=0;i<answers_edit.itemCount;i++){
					var t = document.getElementById("answers_score_"+i);
					eval("t.innerHTML = document.we_form."+answers_edit.name+"_item"+i+".value");
				}
			}

		');

		array_push($parts, array(
			"headline" => g_l('modules_voting', '[inquiry]'),
			"html" => $js .
			we_html_element::htmlHidden(array('name' => 'scores_changed', 'value' => '0')) .
			$table->getHTML() .
			we_html_element::htmlBr() . $butt,
			"space" => $this->_space_size)
		);


		$ok = we_button::create_button("export", "javascript:we_cmd('export_csv')");

		$export_box = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 10, 1);

		$export_box->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
		$export_box->setCol(1, 0, array(), we_html_tools::htmlFormElementTable($this->formFileChooser($this->_width_size - 130, 'csv_dir', '/', '', 'folder'), g_l('export', '[dir]')));
		$export_box->setCol(2, 0, array(), we_html_tools::getPixel(5, 5));

		$lineend = new we_html_select(array('name' => 'csv_lineend', 'size' => '1', 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
		$lineend->addOption('windows', g_l('export', '[windows]'));
		$lineend->addOption('unix', g_l('export', "[unix]"));
		$lineend->addOption('mac', g_l('export', "[mac]"));

		$delimiter = new we_html_select(array('name' => 'csv_delimiter', 'size' => '1', 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
		$delimiter->addOption(';', g_l('export', '[semicolon]'));
		$delimiter->addOption(',', g_l('export', '[comma]'));
		$delimiter->addOption(':', g_l('export', '[colon]'));
		$delimiter->addOption('\t', g_l('export', '[tab]'));
		$delimiter->addOption(' ', g_l('export', '[space]'));

		$enclose = new we_html_select(array('name' => 'csv_enclose', 'size' => '1', 'class' => 'defaultfont', 'style' => 'width: ' . $this->_width_size . 'px'));
		$enclose->addOption(0, g_l('export', '[double_quote]'));
		$enclose->addOption(1, g_l('export', '[single_quote]'));

		$export_box->setCol(3, 0, array("class" => "defaultfont"), we_html_tools::htmlFormElementTable($lineend->getHtml(), g_l('export', '[csv_lineend]')));
		$export_box->setColContent(4, 0, we_html_tools::getPixel(5, 5));
		$export_box->setColContent(5, 0, we_html_tools::htmlFormElementTable($delimiter->getHtml(), g_l('export', '[csv_delimiter]')));
		$export_box->setColContent(6, 0, we_html_tools::getPixel(5, 5));
		$export_box->setColContent(7, 0, we_html_tools::htmlFormElementTable($enclose->getHtml(), g_l('export', '[csv_enclose]')));
		$export_box->setColContent(8, 0, we_html_tools::getPixel(5, 15));
		$export_box->setCol(9, 0, array("nowrap" => null), we_button::create_button_table(array($ok))
		);



		array_push($parts, array(
			"headline" => g_l('modules_voting', '[export]'),
			"html" => we_html_tools::htmlAlertAttentionBox(g_l('modules_voting', '[export_txt]'), 2, $this->_width_size) .
			$export_box->getHtml(),
			"space" => $this->_space_size)
		);

		return $parts;
	}

	function getHTMLProperties($preselect = ""){

		$tabNr = isset($_REQUEST["tabnr"]) ? (($this->View->voting->IsFolder && $_REQUEST["tabnr"] != 1) ? 1 : $_REQUEST["tabnr"]) : 1;

		$out = we_html_element::jsElement('

			var table = "' . FILE_TABLE . '";
			var log_counter=0;
			function toggle(id){
				var elem = document.getElementById(id);
				if(elem.style.display == "none") elem.style.display = "block";
				else elem.style.display = "none";
			}
			function setVisible(id,visible){
				var elem = document.getElementById(id);
				if(visible==true) elem.style.display = "block";
				else elem.style.display = "none";
			}

		');

		$out .= we_html_element::htmlDiv(array('id' => 'tab1', 'style' => ($tabNr == 1 ? '' : 'display: none')), we_multiIconBox::getHTML('', "100%", $this->getHTMLTab1(), 30, '', -1, '', '', false, $preselect)) .
			(!$this->View->voting->IsFolder ?
				(
				we_html_element::htmlDiv(array('id' => 'tab2', 'style' => ($tabNr == 2 ? '' : 'display: none')), we_multiIconBox::getHTML('', "100%", $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect)) .
				we_html_element::htmlDiv(array('id' => 'tab3', 'style' => ($tabNr == 3 ? '' : 'display: none')), we_multiIconBox::getHTML('', "100%", $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect)) .
				we_html_element::htmlDiv(array('id' => 'tab4', 'style' => ($tabNr == 4 ? '' : 'display: none')), we_multiIconBox::getHTML('', "100%", $this->getHTMLTab4(), 30, '', -1, '', '', false, $preselect))
				) : '') .
			$this->getHTMLVariant();

		return $out;
	}

	function getHTMLDirChooser(){
		$path = id_to_path($this->View->voting->ParentID, VOTING_TABLE);
		//javascript:top.content.setHot(); we_cmd('openVotingDirselector',document.we_form.elements['ParentID'].value,'document.we_form.elements[\'ParentID\'].value','document.we_form.elements[\'ParentPath\'].value','')"
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['ParentID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['ParentPath'].value");
		$wecmdenc3 = we_cmd_enc("top.opener._EditorFrame.setEditorIsHot(true);");
		$button = we_button::create_button('select', "javascript:top.content.setHot(); we_cmd('openVotingDirselector',document.we_form.elements['ParentID'].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','')");
		$width = "416";

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("PathGroup");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("ParentPath", $path, 'onchange=top.content.setHot();');
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult("ParentID", (empty($this->View->voting->ParentID) ? 0 : $this->View->voting->ParentID));
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setTable(VOTING_TABLE);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setLabel(g_l('modules_voting', '[group]'));

		return $yuiSuggest->getHTML();
	}

	function getHTMLLeft(){

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "1,*,0"));
		$frameset->addFrame(array("src" => HTML_DIR . "whiteWithTopLine.html", "name" => "treeheader", "noresize" => null, "scrolling" => "no"));

		$frameset->addFrame(array("src" => WEBEDITION_DIR . "treeMain.php", "name" => "tree", "noresize" => null, "scrolling" => "auto"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=treefooter", "name" => "treefooter", "noresize" => null, "scrolling" => "no"));

		// set and return html code
		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body);
	}

	function getHTMLTreeHeader(){
		return "";
	}

	function getHTMLTreeFooter(){

		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => "5", "marginheight" => "0", "leftmargin" => "5", "topmargin" => "0"), ""
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLCmd(){
		$out = "";

		if(isset($_REQUEST["pid"])){
			$pid = $_REQUEST["pid"];
		}
		else
			exit;

		if(isset($_REQUEST["offset"])){
			$offset = $_REQUEST["offset"];
		}
		else
			$offset = 0;

		$rootjs = "";
		if(!$pid)
			$rootjs.='
		' . $this->Tree->topFrame . '.treeData.clear();
		' . $this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));
		';

		$hiddens = we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "no_cmd"));

		$out.=we_html_element::htmlBody(array("bgcolor" => "white", "marginwidth" => "10", "marginheight" => "10", "leftmargin" => "10", "topmargin" => "10"), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
					we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree(weVotingTreeLoader::getItems($pid, $offset, $this->Tree->default_segment, "")))
				)
		);

		return $this->getHTMLDocument($out);
	}

	function getHTMLExportCsvMessage($mode = 0){
		if(isset($_REQUEST["lnk"])){
			$link = $_REQUEST["lnk"];
		}

		if(isset($link)){
			$port = defined("HTTP_PORT") ? HTTP_PORT : 80;
			$down = getServerUrl() . $link;

			$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 7, 1);

			$table->setCol(0, 0, array(), we_html_tools::getPixel(5, 5));

			$table->setCol(1, 0, array("class" => "defaultfont"), sprintf(g_l('modules_voting', '[csv_export]'), $link));

			$table->setCol(2, 0, array(), we_html_tools::getPixel(5, 10));

			$table->setCol(3, 0, array("class" => "defaultfont"), weBackupWizard::getDownloadLinkText());
			$table->setCol(4, 0, array(), we_html_tools::getPixel(5, 10));
			$table->setCol(5, 0, array("class" => "defaultfont"), we_html_element::htmlA(array("href" => $down), g_l('modules_voting', '[csv_download]')
				)
			);
			$table->setCol(6, 0, array(), we_html_tools::getPixel(100, 10));


			$close = we_button::create_button("close", "javascript:self.close();");


			$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "group", "value" => (isset($group) ? $group : ""))) .
						we_html_tools::htmlDialogLayout(
							$table->getHtml(), g_l('modules_voting', '[csv_download]'), we_button::position_yes_no_cancel(null, $close, null), "100%", "30", 350)
						.
						we_html_element::jsElement("self.focus();")
					)
			);

			return $this->getHTMLDocument($body);
		}
	}

	function getHTMLExportGroupCsvMessage($mode = 0){
		if(isset($_REQUEST["lnk"])){
			$link = $_REQUEST["lnk"];
		}

		if(isset($link)){
			$down = getServerUrl() . $link;

			$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 7, 1);

			$table->setCol(0, 0, array(), we_html_tools::getPixel(5, 5));

			$table->setCol(1, 0, array("class" => "defaultfont"), sprintf(g_l('modules_voting', '[csv_export]'), $link));

			$table->setCol(2, 0, array(), we_html_tools::getPixel(5, 10));

			$table->setCol(3, 0, array("class" => "defaultfont"), weBackupWizard::getDownloadLinkText());
			$table->setCol(4, 0, array(), we_html_tools::getPixel(5, 10));
			$table->setCol(5, 0, array("class" => "defaultfont"), we_html_element::htmlA(array("href" => $down), g_l('modules_voting', '[csv_download]')
				)
			);
			$table->setCol(6, 0, array(), we_html_tools::getPixel(100, 10));


			$close = we_button::create_button("close", "javascript:self.close();");


			$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "group", "value" => (isset($group) ? $group : ""))) .
						we_html_tools::htmlDialogLayout(
							$table->getHtml(), g_l('modules_voting', '[csv_download]'), we_button::position_yes_no_cancel(null, $close, null), "100%", "30", 350)
						.
						we_html_element::jsElement("self.focus();")
					)
			);

			return $this->getHTMLDocument($body);
		}
	}

	function formFileChooser($width = "", $IDName = "ParentID", $IDValue = "/", $cmd = "", $filter = ""){
		//javascript:we_cmd('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value);
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$button = we_button::create_button("select", "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','$filter',document.we_form.elements['$IDName'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, "", 'readonly onchange="top.content.setHot();"', "text", $width, 0), "", "left", "defaultfont", "", we_html_tools::getPixel(20, 4), we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $button : "");
	}

	function getHTMLResetIPData(){
		$this->View->voting->resetIpData();

		$close = we_button::create_button("close", "javascript:self.close();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout(
					we_html_element::htmlSpan(array('class' => 'defaultfont'), g_l('modules_voting', '[data_deleted_info]')), g_l('modules_voting', '[voting]'), we_button::position_yes_no_cancel(null, $close, null)) .
				we_html_element::jsElement("self.focus();")
		);
		return $this->getHTMLDocument($body);
	}

	function getHTMLDeleteLog(){
		$this->View->voting->deleteLogData();

		$close = we_button::create_button("close", "javascript:self.close();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout(
					we_html_element::htmlSpan(array('class' => 'defaultfont'), g_l('modules_voting', '[data_deleted_info]')), g_l('modules_voting', '[voting]'), we_button::position_yes_no_cancel(null, $close, null)) .
				we_html_element::jsElement("self.focus();")
		);
		return $this->getHTMLDocument($body);
	}

	function getHTMLShowLogOld(){
		$close = we_button::create_button("close", "javascript:self.close();");
		$refresh = we_button::create_button("refresh", "javascript:location.reload();");

		$voting = new weVoting();
		$voting->load($this->View->voting->ID);
		$log = array();
		if(!is_array($voting->LogData)){
			$log = unserialize($voting->LogData);
			if(empty($log)){
				$log = array();
			}
		}

		$headline = array();

		$headline[0] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[time]')));
		$headline[1] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[ip]')));
		$headline[2] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[user_agent]')));
		$headline[3] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[cookie]')));
		$headline[4] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[log_fallback]')));
		$headline[5] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[status]')));


		$content = array();

		$count = 15;
		$size = count($log);

		$nextprev = "";

		if($size > 0){
			$size--;
			$start = (isset($_REQUEST['start']) ? $_REQUEST['start'] : $size);
			$start = $start < 0 ? 0 : $start;
			$start = $start > $size ? $size : $start;

			$back = $start + $count;
			$back = $back > $size ? $size : $back;

			$next = $start - $count;
			$next = $next < 0 ? -1 : $next;

			$ind = 0;
			for($i = $start; $i > $next; $i--){
				if($i < 0)
					break;
				$data = $log[$i];
				$content[$ind] = array();
				$content[$ind][0]['dat'] = date(g_l('weEditorInfo', "[date_format]"), $data['time']);
				$content[$ind][1]['dat'] = $data['ip'];
				$content[$ind][2]['dat'] = $data['agent'];
				$content[$ind][3]['dat'] = $data['cookie'] ? g_l('modules_voting', '[enabled]') : g_l('modules_voting', '[disabled]');
				$content[$ind][4]['dat'] = $data['fallback'] ? g_l('global', '[yes]') : g_l('global', '[no]');

				$mess = g_l('modules_voting', '[log_success]');
				if($data['status'] != weVoting::SUCCESS){
					switch($data['status']){
						case weVoting::ERROR :
							$mess = g_l('modules_voting', '[log_error]');
							break;
						case weVoting::ERROR_ACTIVE :
							$mess = g_l('modules_voting', '[log_error_active]');
							break;
						case weVoting::ERROR_REVOTE :
							$mess = g_l('modules_voting', '[log_error_revote]');
							break;
						case weVoting::ERROR_BLACKIP :
							$mess = g_l('modules_voting', '[log_error_blackip]');
							break;
						default:
							$mess = g_l('modules_voting', '[log_error]');
					}
					$mess = we_html_element::htmlSpan(array('style' => 'color: red;'), $mess);
				}

				$content[$ind][5]['dat'] = $mess;
				$ind++;
			}

			$nextprev = '<table style="margin-top: 10px;" border="0" cellpadding="0" cellspacing="0"><tr><td>';
			if($start < $size){
				$nextprev .= we_button::create_button("back", $this->frameset . "?pnt=show_log&start=" . $back); //bt_back
			} else{
				$nextprev .= we_button::create_button("back", "", false, 100, 22, "", "", true);
			}

			$nextprev .= we_html_tools::getPixel(23, 1) . "</td><td align='center' class='defaultfont' width='120'><b>" . ($size - $start + 1) . "&nbsp;-&nbsp;";

			$nextprev .= ($size - $next);

			$nextprev .= "&nbsp;" . g_l('global', "[from]") . " " . ($size + 1) . "</b></td><td>" . we_html_tools::getPixel(23, 1);

			if($next > 0){
				$nextprev .= we_button::create_button("next", $this->frameset . "?pnt=show_log&start=" . $next); //bt_next
			} else{
				$nextprev .= we_button::create_button("next", "", "", 100, 22, "", "", true);
			}
			$nextprev .= "</td></tr></table>";

			$parts = array();

			$parts[] = array(
				'headline' => '',
				'html' => we_html_tools::htmlDialogBorder3(730, 300, $content, $headline) . $nextprev,
				'space' => 0,
				'noline' => 1
			);
		} else{
			$parts[] = array(
				'headline' => '',
				'html' => we_html_element::htmlSpan(array('class' => 'middlefontgray'), g_l('modules_voting', '[log_is_empty]')) .
				we_html_element::htmlBr() .
				we_html_element::htmlBr(),
				'space' => 0,
				'noline' => 1
			);
		}

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_multiIconBox::getHTML("show_log_data", "100%", $parts, 30, we_button::position_yes_no_cancel($refresh, $close, null), -1, '', '', false, g_l('modules_voting', '[voting]'), "", 558) .
				we_html_element::jsElement("self.focus();")
		);
		return $this->getHTMLDocument($body);
	}

	function getHTMLShowLogNew(){
		$close = we_button::create_button("close", "javascript:self.close();");
		$refresh = we_button::create_button("refresh", "javascript:location.reload();");

		$voting = new weVoting();
		$voting->load($this->View->voting->ID);
		$log = array();
		$log = $voting->loadDB($voting->ID);


		$headline = array();

		$headline[0] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-session]')));
		$headline[1] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-id]')));
		$headline[2] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[time]')));
		$headline[3] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[ip]')));
		$headline[4] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[user_agent]')));
		$headline[5] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[cookie]')));
		$headline[6] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[log_fallback]')));
		$headline[7] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[status]')));
		$headline[8] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerID]')));
		$headline[9] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerText]')));
		$headline[10] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-successor]')));
		$headline[11] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[voting-additionalfields]')));

		$content = array();

		$count = 15;
		$size = count($log);

		$nextprev = "";

		if($size > 0){
			$size--;
			$start = (isset($_REQUEST['start']) ? $_REQUEST['start'] : $size);
			$start = $start < 0 ? 0 : $start;
			$start = $start > $size ? $size : $start;

			$back = $start + $count;
			$back = $back > $size ? $size : $back;

			$next = $start - $count;
			$next = $next < 0 ? -1 : $next;

			$ind = 0;
			for($i = $start; $i > $next; $i--){
				if($i < 0)
					break;
				$data = $log[$i];
				$content[$ind] = array();
				$content[$ind][0]['dat'] = $data['votingsession'];
				$content[$ind][1]['dat'] = $data['voting'];
				$content[$ind][2]['dat'] = date(g_l('weEditorInfo', "[date_format]"), $data['time']);
				$content[$ind][3]['dat'] = $data['ip'];
				$content[$ind][4]['dat'] = $data['agent'];
				$content[$ind][5]['dat'] = $data['cookie'] ? g_l('modules_voting', '[enabled]') : g_l('modules_voting', '[disabled]');
				$content[$ind][6]['dat'] = $data['fallback'] ? g_l('global', '[yes]') : g_l('global', '[no]');

				$mess = g_l('modules_voting', '[log_success]');
				if($data['status'] != weVoting::SUCCESS){
					switch($data['status']){
						case weVoting::ERROR :
							$mess = g_l('modules_voting', '[log_error]');
							break;
						case weVoting::ERROR_ACTIVE :
							$mess = g_l('modules_voting', '[log_error_active]');
							break;
						case weVoting::ERROR_REVOTE :
							$mess = g_l('modules_voting', '[log_error_revote]');
							break;
						case weVoting::ERROR_BLACKIP :
							$mess = g_l('modules_voting', '[log_error_blackip]');
							break;
						default:
							$mess = g_l('modules_voting', '[log_error]');
					}
					$mess = we_html_element::htmlSpan(array('style' => 'color: red;'), $mess);
				}

				$content[$ind][7]['dat'] = $mess;

				$content[$ind][8]['dat'] = $data['answer'];
				$content[$ind][9]['dat'] = $data['answertext'];
				$content[$ind][10]['dat'] = $data['successor'];
				$addData = unserialize($data['additionalfields']);
				$addDataString = "";
				if(is_array($addData) && !empty($addData)){
					foreach($addData as $key => $value){
						$addDataString .= $key . ': ' . $value . '<br />';
					}
				}
				$content[$ind][11]['dat'] = $addDataString;

				$ind++;
			}

			$nextprev = '<table style="margin-top: 10px;" border="0" cellpadding="0" cellspacing="0"><tr><td>';
			if($start < $size){
				$nextprev .= we_button::create_button("back", $this->frameset . "?pnt=show_log&start=" . $back); //bt_back
			} else{
				$nextprev .= we_button::create_button("back", "", false, 100, 22, "", "", true);
			}

			$nextprev .= we_html_tools::getPixel(23, 1) . "</td><td align='center' class='defaultfont' width='120'><b>" . ($size - $start + 1) . "&nbsp;-&nbsp;";

			$nextprev .= ($size - $next);

			$nextprev .= "&nbsp;" . g_l('global', "[from]") . " " . ($size + 1) . "</b></td><td>" . we_html_tools::getPixel(23, 1);

			if($next > 0){
				$nextprev .= we_button::create_button("next", $this->frameset . "?pnt=show_log&start=" . $next); //bt_next
			} else{
				$nextprev .= we_button::create_button("next", "", "", 100, 22, "", "", true);
			}
			$nextprev .= "</td></tr></table>";

			$parts = array();

			$parts[] = array(
				'headline' => '',
				'html' => we_html_tools::htmlDialogBorder4(1000, 300, $content, $headline) . $nextprev,
				'space' => 0,
				'noline' => 1
			);
		} else{
			$parts[] = array(
				'headline' => '',
				'html' => we_html_element::htmlSpan(array('class' => 'middlefontgray'), g_l('modules_voting', '[log_is_empty]')) .
				we_html_element::htmlBr() .
				we_html_element::htmlBr(),
				'space' => 0,
				'noline' => 1
			);
		}

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_multiIconBox::getHTML("show_log_data", "100%", $parts, 30, we_button::position_yes_no_cancel($refresh, $close, null), -1, '', '', false, g_l('modules_voting', '[voting]'), "", 558) .
				we_html_element::jsElement("self.focus();")
		);
		return $this->getHTMLDocument($body);
	}

	function getHTMLShowGroupLog(){
		$close = we_button::create_button("close", "javascript:self.close();");
		$refresh = we_button::create_button("refresh", "javascript:location.reload();");

		$voting = new weVoting();
		$voting->load($this->View->voting->ID);
		$log = array();
		$log = $voting->loadDB($voting->ID);


		$headline = array();

		$headline[0] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[time]')));
		$headline[1] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[ip]')));
		$headline[2] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[user_agent]')));
		$headline[3] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[cookie]')));
		$headline[4] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[log_fallback]')));
		$headline[5] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[status]')));
		$headline[6] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerID]')));
		$headline[7] = array('dat' => we_html_element::htmlB(g_l('modules_voting', '[answerText]')));

		$content = array();

		$count = 15;
		$size = count($log);

		$nextprev = "";

		if($size > 0){
			$size--;
			$start = (isset($_REQUEST['start']) ? $_REQUEST['start'] : $size);
			$start = $start < 0 ? 0 : $start;
			$start = $start > $size ? $size : $start;

			$back = $start + $count;
			$back = $back > $size ? $size : $back;

			$next = $start - $count;
			$next = $next < 0 ? -1 : $next;

			$ind = 0;
			for($i = $start; $i > $next; $i--){
				if($i < 0)
					break;
				$data = $log[$i];
				$content[$ind] = array();
				$content[$ind][0]['dat'] = date(g_l('weEditorInfo', "[date_format]"), $data['time']);
				$content[$ind][1]['dat'] = $data['ip'];
				$content[$ind][2]['dat'] = $data['agent'];
				$content[$ind][3]['dat'] = $data['cookie'] ? g_l('modules_voting', '[enabled]') : g_l('modules_voting', '[disabled]');
				$content[$ind][4]['dat'] = $data['fallback'] ? g_l('global', '[yes]') : g_l('global', '[no]');

				$mess = g_l('modules_voting', '[log_success]');
				if($data['status'] != weVoting::SUCCESS){
					switch($data['status']){
						case weVoting::ERROR :
							$mess = g_l('modules_voting', '[log_error]');
							break;
						case weVoting::ERROR_ACTIVE :
							$mess = g_l('modules_voting', '[log_error_active]');
							break;
						case weVoting::ERROR_REVOTE :
							$mess = g_l('modules_voting', '[log_error_revote]');
							break;
						case weVoting::ERROR_BLACKIP :
							$mess = g_l('modules_voting', '[log_error_blackip]');
							break;
						default:
							$mess = g_l('modules_voting', '[log_error]');
					}
					$mess = we_html_element::htmlSpan(array('style' => 'color: red;'), $mess);
				}

				$content[$ind][5]['dat'] = $mess;

				$content[$ind][6]['dat'] = $data['answer'];
				$content[$ind][7]['dat'] = $data['answertext'];

				$ind++;
			}

			$nextprev = '<table style="margin-top: 10px;" border="0" cellpadding="0" cellspacing="0"><tr><td>';
			if($start < $size){
				$nextprev .= we_button::create_button("back", $this->frameset . "?pnt=show_log&start=" . $back); //bt_back
			} else{
				$nextprev .= we_button::create_button("back", "", false, 100, 22, "", "", true);
			}

			$nextprev .= we_html_tools::getPixel(23, 1) . "</td><td align='center' class='defaultfont' width='120'><b>" . ($size - $start + 1) . "&nbsp;-&nbsp;";

			$nextprev .= ($size - $next);

			$nextprev .= "&nbsp;" . g_l('global', "[from]") . " " . ($size + 1) . "</b></td><td>" . we_html_tools::getPixel(23, 1);

			if($next > 0){
				$nextprev .= we_button::create_button("next", $this->frameset . "?pnt=show_log&start=" . $next); //bt_next
			} else{
				$nextprev .= we_button::create_button("next", "", "", 100, 22, "", "", true);
			}
			$nextprev .= "</td></tr></table>";

			$parts = array();

			$parts[] = array(
				'headline' => '',
				'html' => we_html_tools::htmlDialogBorder3(730, 300, $content, $headline) . $nextprev,
				'space' => 0,
				'noline' => 1
			);
		} else{
			$parts[] = array(
				'headline' => '',
				'html' => we_html_element::htmlSpan(array('class' => 'middlefontgray'), g_l('modules_voting', '[log_is_empty]')) .
				we_html_element::htmlBr() .
				we_html_element::htmlBr(),
				'space' => 0,
				'noline' => 1
			);
		}

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_multiIconBox::getHTML("show_log_data", "100%", $parts, 30, we_button::position_yes_no_cancel($refresh, $close, null), -1, '', '', false, g_l('modules_voting', '[voting]'), "", 558) .
				we_html_element::jsElement("self.focus();")
		);
		return $this->getHTMLDocument($body);
	}

	function getHTMLDeleteGroupLog(){
		$this->View->voting->deleteGroupLogData();

		$close = we_button::create_button("close", "javascript:self.close();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_tools::htmlDialogLayout(
					we_html_element::htmlSpan(array('class' => 'defaultfont'), g_l('modules_voting', '[data_deleted_info]')), g_l('modules_voting', '[voting]'), we_button::position_yes_no_cancel(null, $close, null)) .
				we_html_element::jsElement("self.focus();")
		);
		return $this->getHTMLDocument($body);
	}

}


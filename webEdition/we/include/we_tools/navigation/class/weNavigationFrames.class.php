<?php

/**
 * webEdition CMS
 *
 * $Rev: 5828 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 14:17:22 +0100 (Sun, 17 Feb 2013) $
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
class weNavigationFrames extends weToolFrames{

	function __construct(){
		$this->toolName = 'navigation';
		$this->toolClassName = 'weNavigation';
		$this->toolUrl = WE_INCLUDES_DIR . 'we_tools/' . $this->toolName . '/';
		$this->toolDir = $_SERVER['DOCUMENT_ROOT'] . $this->toolUrl;
		$_frameset = $this->toolUrl . 'edit_' . $this->toolName . '_frameset.php';
		parent::__construct($_frameset);

		$this->Table = NAVIGATION_TABLE;
		$this->TreeSource = 'table:' . $this->Table;

		$this->Tree = new weNavigationTree();
		$this->View = new weNavigationView($_frameset, 'top.content');
		$this->Model = &$this->View->Model;

		$this->setupTree(NAVIGATION_TABLE, 'top.content', 'top.content.resize.left.tree', 'top.content.cmd');
	}

	function getHTML($what){
		switch($what){
			case 'preview' :
				print $this->getHTMLEditorBody();
				break;
			case 'previewIframe' :
				print $this->getHTMLEditorPreviewIframe();
				break;
			case 'fields' :
				print $this->getHTMLFieldSelector();
				break;
			case 'dyn_preview' :
				print $this->getHTMLDynPreview();
				break;
			default :
				parent::getHTML($what);
		}
	}

	/**
	 * Frame for tabs
	 *
	 * @return string
	 */
	function getHTMLEditorHeader(){

		if(!empty($this->Model->Charset)){
			we_html_tools::headerCtCharset('text/html', $this->Model->Charset);
		}

		if(isset($_REQUEST['home'])){
			return $this->getHTMLDocument(
					we_html_element::htmlBody(
						array(
						'bgcolor' => '#F0EFF0',
						), ''));
		}

		$we_tabs = new we_tabs();

		$we_tabs->addTab(new we_tab('#', g_l('navigation', '[property]'), '((' . $this->topFrame . '.activ_tab==1) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('1');", array("id" => "tab_1")));
		if($this->Model->IsFolder){
			$we_tabs->addTab(new we_tab("#", g_l('navigation', '[content]'), '((' . $this->topFrame . '.activ_tab==2) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('2');", array("id" => "tab_2")));
		}

		if(defined('CUSTOMER_TABLE') && we_hasPerm("CAN_EDIT_CUSTOMERFILTER")){
			$we_tabs->addTab(new we_tab("#", g_l('navigation', '[customers]'), '((' . $this->topFrame . '.activ_tab==3) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('3');", array("id" => "tab_3")));
		}

		if($this->Model->IsFolder){
			$we_tabs->addTab(new we_tab("#", g_l('navigation', '[preview]'), '((' . $this->topFrame . '.activ_tab=="preview") ? TAB_ACTIVE : TAB_NORMAL)', "setTab('preview');", array("id" => "tab_preview")));
		}

		$we_tabs->onResize();
		//$tabsBody = $we_tabs->getJS();
		$tabsHead = $we_tabs->getHeader() .
			we_html_element::jsElement(
				($this->Model->IsFolder == 0 ? '
				if(' . $this->View->topFrame . '.activ_tab!=1 && ' . $this->View->topFrame . '.activ_tab!=3) {
					' . $this->View->topFrame . '.activ_tab=1;
				}' : ''
				) . '
				function mark() {
					var elem = document.getElementById("mark");
					elem.style.display = "inline";

				}

				function unmark() {
					var elem = document.getElementById("mark");
					elem.style.display = "none";
				}

				function setTab(tab) {
					switch (tab) {
						case "preview":	// submit the information to preview screen
							parent.edbody.document.we_form.cmd.value="";
							if (' . $this->topFrame . '.activ_tab != tab || (' . $this->topFrame . '.activ_tab=="preview" && tab=="preview")) {
								parent.edbody.document.we_form.pnt.value = "preview";
								parent.edbody.document.we_form.tabnr.value = "preview";
								parent.edbody.submitForm();
							}
						break;

						default: // just toggle content to show
							if (' . $this->topFrame . '.activ_tab!="preview") {
								parent.edbody.toggle("tab"+' . $this->topFrame . '.activ_tab);
								parent.edbody.toggle("tab"+tab);
								' . $this->topFrame . '.activ_tab=tab;
								self.focus();
							} else {

								parent.edbody.document.we_form.pnt.value = "edbody";
								parent.edbody.document.we_form.tabnr.value = tab;
								parent.edbody.submitForm();
							}
						break;
					}
					self.focus();
					' . $this->topFrame . '.activ_tab=tab;
				}

				' . ($this->Model->ID ? '' : $this->topFrame . '.activ_tab=1;')
		);


		$table = new we_html_table(array("width" => "3000", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"), 3, 1);

		$table->setCol(0, 0, array(), we_html_tools::getPixel(1, 3));

		$table->setCol(1, 0, array("valign" => "top", "class" => "small"), we_html_tools::getPixel(15, 2) . we_html_element::htmlB(
				($this->Model->IsFolder ? g_l('navigation', '[group]') : g_l('navigation', '[entry]')) . ':&nbsp;' . str_replace(
					'&amp;', '&', $this->Model->Text) . '<div id="mark" style="display: none;">*</div>' . we_html_tools::getPixel(1600, 19)
		));

		$extraJS = 'document.getElementById("tab_"+' . $this->topFrame . '.activ_tab).className="tabActive";';
		$body = we_html_element::htmlBody(
				array(
				"bgcolor" => "white",
				"background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif",
				"marginwidth" => "0",
				"marginheight" => "0",
				"leftmargin" => "0",
				"topmargin" => "0",
				"onload" => "setFrameSize()",
				"onresize" => "setFrameSize()"
				), we_html_element::htmlDiv(array('id' => "main"), we_html_tools::getPixel(100, 3) . we_html_element::htmlDiv(array('id' => 'headrow', 'style' => "margin:0px;"), '&nbsp;' .
						we_html_element::htmlB(($this->Model->IsFolder ? g_l('navigation', '[group]') : g_l('navigation', '[entry]')) . ':&nbsp;' .
							str_replace('&amp;', '&', $this->Model->Text) .
							we_html_element::htmlDiv(array('id' => 'mark', 'style' => 'display: none;'), '*'))) .
					we_html_tools::getPixel(100, 3) . $we_tabs->getHTML() . '</div>' . we_html_element::jsElement($extraJS))//			$js.
				//			$table->getHtml() .
				//			$tabsBody
			)
		;

		return $this->getHTMLDocument($body, $tabsHead);
	}

	function getHTMLGeneral(){
		$_table = new we_html_table(
			array(
			'border' => '0',
			'cellpadding' => '0',
			'cellspacing' => '0',
			'width' => '300',
			'style' => 'margin-top:' . $this->_margin_top . ';'
			), 1, 3);

		$_table->setCol(0, 0, array(
			'class' => 'defaultfont'
			), g_l('navigation', '[order]') . ':');

		$_table->setColContent(
			0, 1, we_html_tools::htmlTextInput(
				'Ordn', '', ($this->Model->Ordn + 1), '', 'disabled="true" readonly style="width: 35px" onBlur="var r=parseInt(this.value);if(isNaN(r)) this.value=' . $this->Model->Ordn . '; else{ this.value=r; ' . $this->topFrame . '.mark();}"'));

		unset($inp);

		$_parentid = (isset($this->Model->Text) && $this->Model->Text != '' && isset($this->Model->ID) && $this->Model->ID != '' ?
				f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'ParentID', $this->db) :
				(isset($_REQUEST['presetFolder']) && $_REQUEST['presetFolder'] ?
					$this->Model->ParentID :
					'0'));


		$_num = $_parentid ? f(
				'SELECT COUNT(ID) as OrdCount FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($_parentid), 'OrdCount', new DB_WE()) : 0;

		$_table->setColContent(
			0, 2, we_button::create_button_table(
				array(
				we_button::create_button('image:direction_up', 'javascript:' . $this->topFrame . '.we_cmd("move_up");', true, 100, 22, '', '', (($this->Model->Ordn > 0) ? false : true), false),
				we_button::create_button('image:direction_down', 'javascript:' . $this->topFrame . '.we_cmd("move_down");', true, 100, 22, '', '', (($this->Model->Ordn < ($_num - 1)) ? false : true), false)
				), 10, array(
				'style' => 'margin-left: 15px'
		)));
		// name and folder block
		// icen selector block
		$uniqname = 'weIconNaviAttrib';
		$wepos = weGetCookieVariable("but_weIconNaviAttrib");
		$wepos = ($wepos == 'right' || $wepos == 'down') ? $wepos : 'right';

		return
			array(
				array(
					'headline' => g_l('navigation', '[general]'),
					'html' => we_html_element::htmlHidden(array('name' => 'newone', 'value' => ($this->Model->ID == 0 ? 1 : 0))) .
					we_html_tools::htmlFormElementTable(
						we_html_tools::htmlTextInput(
							'Text', '', strtr(
								$this->Model->Text, array_flip(get_html_translation_table(HTML_SPECIALCHARS))), '', 'style="width: ' . $this->_width_size . '" onchange="' . $this->topFrame . '.mark();"'), g_l('navigation', '[name]')) . we_html_tools::htmlFormElementTable(
						we_html_tools::htmlTextInput(
							'Display', '', $this->Model->Display, '', 'style="width: ' . $this->_width_size . '" onChange="' . $this->topFrame . '.mark();"'), g_l('navigation', '[display]')) . $this->getHTMLChooser(
						g_l('navigation', '[group]'), NAVIGATION_TABLE, 0, 'ParentID', $_parentid, 'ParentPath', 'opener.' . $this->topFrame . '.mark()', 'folder', ($this->Model->IsFolder == 0 && $this->Model->Depended == 1)),
					'space' => $this->_space_size,
					'noline' => 1
				),
				array(
					'headline' => '',
					'html' => $_table->getHtml(),
					'space' => $this->_space_size,
					'noline' => 1
				),
				array(
					'headline' => '',
					'html' => $this->getHTMLChooser(
						g_l('navigation', '[icon]'), FILE_TABLE, 0, 'IconID', $this->Model->IconID, 'IconPath', 'opener.' . $this->topFrame . '.mark()', 'image/*', false, true) . we_html_tools::getPixel($this->_width_size, 10) . '<table><tr><td>' . we_multiIconBox::getJS() . we_multiIconBox::_getButton(
						$uniqname, "weToggleBox('$uniqname','" . addslashes(g_l('navigation', '[icon_properties_out]')) . "','" . addslashes(
							g_l('navigation', '[icon_properties]')) . "')", $wepos, g_l('global', "[openCloseBox]")) . '</td><td><span style="cursor: pointer;" class="defaultfont" id="text_' . $uniqname . '" onClick="weToggleBox(\'' . $uniqname . '\',\'' . addslashes(
						g_l('navigation', '[icon_properties_out]')) . '\',\'' . addslashes(
						g_l('navigation', '[icon_properties]')) . '\');" >' . ($wepos == 'down' ? g_l('navigation', '[icon_properties_out]') : g_l('navigation', '[icon_properties]')) . '</span></td></tr></table>',
					'space' => $this->_space_size,
					'noline' => 1
				),
				array(
					'headline' => '',
					'html' => '<div id="table_' . $uniqname . '" style="display: ' . ($wepos == 'down' ? 'block' : 'none') . ';">' . $this->getHTMLImageAttributes() . '</div>',
					'space' => $this->_space_size + 50,
					'noline' => 1
				),
		);
	}

	function getHTMLPropertiesItem(){
		if($this->Model->Selection == weNavigation::SELECTION_DYNAMIC){
			$_seltype = array(
				weNavigation::STPYE_DOCTYPE => g_l('navigation', '[documents]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[weNavigation::STPYE_CLASS] = g_l('navigation', '[objects]');
			}
			$_seltype[weNavigation::STPYE_CATEGORY] = g_l('navigation', '[categories]');
		} else{
			$_seltype = array(
				weNavigation::STPYE_DOCLINK => g_l('navigation', '[docLink]'),
				weNavigation::STYPE_URLLINK => g_l('navigation', '[urlLink]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[weNavigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
			}
			$_seltype[weNavigation::STPYE_CATLINK] = g_l('navigation', '[catLink]');
		}
		if(defined("OBJECT_TABLE")){
			$_onCahngeJS = "if(document.we_form.Selection.value=='" . weNavigation::SELECTION_STATIC . "'){YAHOO.autocoml.modifySetById('yuiAcInputLinkPath','table',(this.value=='" . weNavigation::STPYE_DOCLINK . "'?'" . FILE_TABLE . "':(this.value=='" . weNavigation::STPYE_OBJLINK . "'?'" . OBJECT_TABLE . "':'" . CATEGORY_TABLE . "')));}else{};";
		} else{
			$_onCahngeJS = "if(document.we_form.Selection.value=='" . weNavigation::SELECTION_STATIC . "'){YAHOO.autocoml.modifySetById('yuiAcInputLinkPath','table',(this.value=='" . weNavigation::STPYE_DOCLINK . "'?'" . FILE_TABLE . "':'" . CATEGORY_TABLE . "'));}else{};";
		}
		$_selection_block = $this->Model->Depended == 1 ? $this->getHTMLDependedProfile() : $this->View->htmlHidden(
				'CategoriesControl', (isset($_REQUEST['CategoriesCount']) ? $_REQUEST['CategoriesCount'] : 0)) . $this->View->htmlHidden(
				'SortControl', (isset($_REQUEST['SortCount']) ? $_REQUEST['SortCount'] : 0)) . $this->View->htmlHidden(
				'CategoriesCount', (isset($this->Model->Categories) ? count($this->Model->Categories) : '0')) . $this->View->htmlHidden(
				'SortCount', (isset($this->Model->Sort) ? count($this->Model->Sort) : '0')) . '
			<div style="display: block;">
			' . we_html_tools::htmlSelect(
				'Selection', array(
				weNavigation::SELECTION_DYNAMIC => g_l('navigation', '[dyn_selection]'),
				weNavigation::SELECTION_STATIC => g_l('navigation', '[stat_selection]')
				), 1, $this->Model->Selection, false, 'onChange="closeAllSelection();toggle(this.value);setPresentation(this.value);setWorkspaces(\'\');' . $this->topFrame . '.mark();setCustomerFilter(this);"', 'value', $this->_width_size) . '<br />' . we_html_tools::htmlSelect(
				'SelectionType', $_seltype, 1, $this->Model->SelectionType, false, 'onChange="onSelectionTypeChangeJS(this); closeAllType();clearFields();closeAllStats();toggle(this.value);setWorkspaces(this.value);setStaticSelection(this.value);' . $this->topFrame . '.mark();" style="width: ' . $this->_width_size . 'px; margin-top: ' . $this->_margin_top . ';"', 'value', $this->_width_size) .
			'
			<div id="dynamic" style="' . ($this->Model->Selection == weNavigation::SELECTION_DYNAMIC ? 'display: block;' : 'display: none;') . '">
				' . $this->getHTMLDynamic() . '
			</div>
			<div id="static" style="' . ($this->Model->Selection == weNavigation::SELECTION_STATIC ? 'display: block;' : 'display: none;') . '">

				<div id="staticSelect" style="' . ($this->Model->SelectionType != weNavigation::STYPE_URLLINK ? 'display: block;' : 'display: none;') . '">
				' . $this->getHTMLStatic() . '
				</div>
				<div id="staticUrl" style="' . (($this->Model->SelectionType == weNavigation::STPYE_CATLINK || $this->Model->SelectionType == weNavigation::STYPE_URLLINK) ? 'display: block;' : 'display: none;') . ';margin-top:' . $this->_margin_top . ';">
				' . $this->getHTMLLink() . '
				</div>
				<div style="margin-top:' . $this->_margin_top . ';">
				' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Parameter', 58, $this->Model->Parameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0), g_l('navigation', '[parameter]')) . '
				</div>
			</div>
			</div>
		';

		return array(
			array(
				'headline' => g_l('navigation', '[selection]'),
				'html' => $_selection_block,
				'space' => $this->_space_size,
				'noline' => 1
		));
	}

	function getHTMLPropertiesGroup(){
		$yuiSuggest = & weSuggest::getInstance();

		$rootDirID = 0;
		//javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . FILE_TABLE . "','document.we_form.elements[\\'LinkID\\'].value','document.we_form.elements[\\'LinkPath\\'].value','','" . session_id() . "','$rootDirID',''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['LinkPath'].value");
		$_cmd_doc = "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','" . session_id() . "','$rootDirID',''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";
		//javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . OBJECT_FILES_TABLE . "','document.we_form.elements[\\'LinkID\\'].value','document.we_form.elements[\\'LinkPath\\'].value','opener." . $this->topFrame . ".we_cmd(\"populateFolderWs\");','" . session_id() . "','$rootDirID','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : ''
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['LinkPath'].value");
		$wecmdenc3 = we_cmd_enc("opener." . $this->topFrame . ".we_cmd('populateFolderWs');");
		$_cmd_obj = defined('OBJECT_TABLE') ? "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : '';

		$_button_doc = we_button::create_button('select', $_cmd_doc, true, 100, 22, '', '', false);
		$_button_obj = we_button::create_button('select', $_cmd_obj, true, 100, 22, '', '', false);

		$_buttons = '<div id="docFolderLink" style="display: ' . ((empty($this->Model->FolderSelection) || $this->Model->FolderSelection == weNavigation::STPYE_DOCLINK) ? 'inline' : 'none') . '">' . $_button_doc . '</div><div id="objFolderLink" style="display: ' . ($this->Model->FolderSelection == weNavigation::STPYE_OBJLINK ? 'inline' : 'none') . '">' . $_button_obj . '</div>';
		$_path = ($this->Model->LinkID == 0 ?
				'' :
				id_to_path($this->Model->LinkID, ($this->Model->FolderSelection == weNavigation::STPYE_DOCLINK ? FILE_TABLE : (defined('OBJECT_TABLE') && $this->Model->FolderSelection == weNavigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE))));

		$_seltype = array(
			weNavigation::STPYE_DOCLINK => g_l('navigation', '[docLink]'),
			weNavigation::STYPE_URLLINK => g_l('navigation', '[urlLink]')
		);
		if(defined('OBJECT_TABLE')){
			$_seltype[weNavigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
		}

		$yuiSuggest->setAcId("LinkPath");
		$yuiSuggest->setContentType(
			$this->Model->FolderSelection == weNavigation::STPYE_DOCLINK ? "folder,text/xml,text/webedition,image/*,text/html,application/*,application/x-shockwave-flash,video/quicktime" : (defined(
					'OBJECT_TABLE') && $this->Model->FolderSelection == weNavigation::STPYE_OBJLINK ? 'folder,objectFile' : "folder,text/xml,text/webedition,image/*,text/html,application/*,application/x-shockwave-flash,video/quicktime"));
		$yuiSuggest->setInput('LinkPath', $_path, array(
			"onChange" => $this->topFrame . ".mark();"
		));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('LinkID', $this->Model->LinkID);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setTable(
			$this->Model->FolderSelection == weNavigation::STPYE_DOCLINK ? FILE_TABLE : (defined('OBJECT_TABLE') && $this->Model->FolderSelection == weNavigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE));
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton($_buttons);

		$weAcSelector = $yuiSuggest->getHTML();

		$_selection = '
		<div style="display: block;">
		' . we_html_tools::htmlSelect(
				'FolderSelection', $_seltype, 1, $this->Model->FolderSelection, false, 'onChange="onFolderSelectionChangeJS(this);setFolderSelection(this.value);' . $this->topFrame . '.mark();" style="width: ' . $this->_width_size . 'px; margin-top: ' . $this->_margin_top . ';"', 'value', $this->_width_size) . '

		<div id="folderSelectionDiv" style="display: ' . ($this->Model->FolderSelection != weNavigation::STYPE_URLLINK ? 'block' : 'none') . ';margin-top:' . $this->_margin_top . '">' . $weAcSelector . '</div>

		</div>
		<div id="folderUrlDiv" style="display: ' . ($this->Model->FolderSelection == weNavigation::STYPE_URLLINK ? 'block' : 'none') . '; margin-top:' . $this->_margin_top . '">
			' . we_html_tools::htmlTextInput(
				'FolderUrl', 58, $this->Model->FolderUrl, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0) . '
		</div>
		' . $this->getHTMLWorkspace('object', 0, 'FolderWsID') . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'FolderParameter', 58, $this->Model->FolderParameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0) . '<br/>' . we_forms::checkboxWithHidden(
					$this->Model->CurrentOnUrlPar, 'CurrentOnUrlPar', g_l('navigation', '[current_on_urlpar]'), false, "defaultfont", $this->topFrame . '.mark();"'), g_l('navigation', '[parameter]'));

		$parts = array(
			array(
				'headline' => g_l('navigation', '[selection]'),
				'html' => $_selection,
				'space' => $this->_space_size,
				'noline' => 1
		));

		if(function_exists('mb_convert_encoding')){
			$parts[] = array(
				'headline' => g_l('navigation', '[charset]'),
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('navigation', '[charset_desc]'), 2, $this->_width_size) . $this->getHTMLCharsetTable(),
				'space' => $this->_space_size,
				'noline' => 1
			);
		}

		// COPY FOLDER
		//$cmd = 'opener.'.$this->topFrame.'.mark()';
		if($this->Model->isnew){
			$_disabled = true;
			$_disabledNote = " " . g_l('weClass', "[availableAfterSave]");
			$_padding = "15";
		} else{
			$_disabled = false;
			$_disabledNote = "";
			$_padding = "10";
		}

		$cmd = 'opener.we_cmd("copyNaviFolder")';
		$_cmd = "javascript:we_cmd('openNavigationDirselector',document.we_form.elements['CopyFolderID'].value,'document.we_form.CopyFolderID.value','document.we_form.CopyFolderPath.value','" . $cmd . "')";
		$_button_copyFolder = we_button::create_button('select', $_cmd, true, 100, 22, '', '', $_disabled);
		$parts[] = array(
			'headline' => g_l('weClass', "[copyFolder]"),
			'html' => we_html_element::jsElement("var selfNaviPath ='" . addslashes(
					$this->Model->Path) . "';\nvar selfNaviId = '" . $this->Model->ID . "';") . "<div style='float:left; margin-right:20px'>" . we_html_tools::htmlAlertAttentionBox(
				g_l('weClass', "[copy_owners_expl]") . $_disabledNote, 2, ($this->_width_size - 120), true, 0) . "</div>" . "<div style='padding-top:{$_padding}px'>" . $_button_copyFolder . "</div>" . we_html_element::htmlHidden(
				array(
					'name' => 'CopyFolderID', "value" => ''
			)) . we_html_element::htmlHidden(array(
				'name' => 'CopyFolderPath', "value" => ''
			)),
			'space' => $this->_space_size,
			'noline' => 1
		);

		return $parts;
	}

	function getHTMLDependedProfile(){
		if($this->Model->Selection == weNavigation::SELECTION_DYNAMIC){
			$_seltype = array(
				weNavigation::STPYE_DOCTYPE => g_l('navigation', '[documents]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[weNavigation::STPYE_CLASS] = g_l('navigation', '[objects]');
			}
		} else{
			$_seltype = array(
				weNavigation::STPYE_DOCLINK => g_l('navigation', '[docLink]')
			);
			if(defined('OBJECT_TABLE')){
				$_seltype[weNavigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
			}
		}

		$_table = new we_html_table(
			array(
			'width' => $this->_width_size,
			'cellpadding' => '0',
			'cellspacing' => '2',
			'border' => '0',
			'class' => 'defaultfont'
			), 5, 2);

		$_table->setColContent(0, 0, g_l('navigation', '[stat_selection]'));

		$_table->setColContent(
			1, 0, ($this->Model->SelectionType == weNavigation::STPYE_CATLINK ? g_l('navigation', '[catLink]') : ($this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? g_l('navigation', '[docLink]') : g_l('navigation', '[objLink]'))) . ':');

		$_table->setColContent(
			1, 1, id_to_path(
				$this->Model->LinkID, ($this->Model->SelectionType == weNavigation::STPYE_CATLINK ? CATEGORY_TABLE : ($this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? FILE_TABLE : OBJECT_FILES_TABLE))));

		if(!empty($this->Model->Url) && $this->Model->Url != 'http://'){
			$_table->setColContent(2, 0, g_l('navigation', '[linkSelection]') . ':');
			$_table->setColContent(2, 1, $this->Model->Url);
		} elseif(!empty($this->Model->UrlID) && $this->Model->UrlID){
			$_table->setColContent(2, 0, g_l('navigation', '[linkSelection]') . ':');
			$_table->setColContent(2, 1, id_to_path($this->Model->UrlID));
		}

		if($this->Model->SelectionType == weNavigation::STPYE_CATLINK){
			$_table->setColContent(3, 0, g_l('navigation', '[catParameter]') . ':');
			$_table->setColContent(3, 1, $this->Model->CatParameter);
		}

		if(!empty($this->Model->Parameter)){
			$_table->setColContent(4, 0, g_l('navigation', '[parameter]') . ':');

			$_table->setColContent(4, 1, $this->Model->Parameter);
		}

		return $_table->getHtml();

		return '
<div style="display: block;">' . g_l('navigation', '[stat_selection]') . '<br />' . ($this->Model->SelectionType == weNavigation::STPYE_CATLINK ? g_l('navigation', '[catLink]') : ($this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? g_l('navigation', '[docLink]') : g_l('navigation', '[objLink]'))) . ':' . id_to_path(
				$this->Model->LinkID, ($this->Model->SelectionType == weNavigation::STPYE_CATLINK ? CATEGORY_TABLE : ($this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? FILE_TABLE : OBJECT_FILES_TABLE))) . '
</div>
<div>' .
			$this->Model->Parameter . '
</div>';
	}

	function getHTMLDynamic(){
		$docTypes = array(
			g_l('navigation', '[no_entry]')
		);
		$q = getDoctypeQuery($this->db);
		$this->db->query('SELECT ID,DocType FROM ' . DOC_TYPES_TABLE . ' ' . $q);
		while($this->db->next_record()) {
			$docTypes[$this->db->f("ID")] = $this->db->f('DocType');
		}

		$classNames = array();
		$objectDirs = array();
		$objectPaths = array();
		$allowedClasses = getAllowedClasses($this->db);

		if(defined('OBJECT_TABLE')){
			$_firstClass = 0;
			$this->db->query('SELECT DISTINCT  ' . OBJECT_TABLE . ".ID," . OBJECT_TABLE . ".Text," . OBJECT_FILES_TABLE . ".ParentID FROM " . OBJECT_TABLE . " LEFT JOIN " . OBJECT_FILES_TABLE . " ON (" . OBJECT_TABLE . ".ID=" . OBJECT_FILES_TABLE . ".TableID)");
			while($this->db->next_record()) {
				if(in_array($this->db->f('ID'), $allowedClasses)){
					if(!$_firstClass){
						$_firstClass = $this->db->f('ID');
					}
					$classNames[$this->db->f('ID')] = $this->db->f('Text');
					$objectDirs[] = $this->db->f('ID') . ':' . intval($this->db->f('ParentID'));
					$objectPaths[] = $this->db->f('ID') . ':"' . f('SELECT * FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->db->f('ParentID')), 'Path', new DB_WE()) . '"';
				}
			}
		}

		$_wsid = array();
		if($this->Model->SelectionType == weNavigation::STPYE_OBJLINK && $this->Model->LinkID){
			$_wsid = weDynList::getWorkspacesForObject($this->Model->LinkID);
		}

		$_sortVal = isset($this->Model->Sort[0]['field']) ? $this->Model->Sort[0]['field'] : "";
		$_sortOrder = isset($this->Model->Sort[0]['order']) ? $this->Model->Sort[0]['order'] : "ASC";

		$_sortSelect = we_html_tools::htmlSelect(
				"SortOrder", array(
				"ASC" => g_l('navigation', '[ascending]'), "DESC" => g_l('navigation', '[descending]')
				), 1, $_sortOrder, false, 'onchange="' . $this->topFrame . '.mark();"', "value", 120);

		return we_html_element::jsElement('
objectDirs = {' . implode(',', $objectDirs) . '};
objectPaths = {' . implode(',', $objectPaths) . '};') . '
<div style="display: block;">
	<div id="doctype" style="' . ($this->Model->SelectionType == weNavigation::STPYE_DOCTYPE ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . ';margin-top:' . $this->_margin_top . '">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect(
					'DocTypeID', $docTypes, 1, $this->Model->DocTypeID, false, 'onChange="clearFields();' . $this->topFrame . '.mark();"', 'value', $this->_width_size), g_l('navigation', '[doctype]')) . '
	</div>
	<div id="classname" style="' . ($this->Model->SelectionType == weNavigation::STPYE_CLASS ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . ';margin-top:' . $this->_margin_top . ';">' .
			(defined('OBJECT_TABLE') ? we_html_tools::htmlFormElementTable(
					we_html_tools::htmlSelect(
						'ClassID', $classNames, 1, $this->Model->ClassID, false, 'onChange="clearFields();document.we_form.elements[\'FolderID\'].value=objectDirs[document.we_form.elements[\'ClassID\'].options[document.we_form.elements[\'ClassID\'].selectedIndex].value];document.we_form.elements[\'FolderPath\'].value=objectPaths[document.we_form.elements[\'ClassID\'].options[document.we_form.elements[\'ClassID\'].selectedIndex].value];' . $this->topFrame . '.we_cmd(\'populateWorkspaces\');' . $this->topFrame . '.mark();"', 'value', $this->_width_size), g_l('navigation', '[class]')) . $this->getHTMLWorkspace('class', $_firstClass) : '') . '
	</div>
	<div id="fieldChooser" style="' . ($this->Model->SelectionType != weNavigation::STPYE_CATEGORY ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . ';margin-top: ' . $this->_margin_top . ';">' .
			$this->getHTMLFieldChooser(g_l('navigation', '[title_field]'), 'TitleField', $this->Model->TitleField, 'putTitleField', $this->Model->SelectionType, ($this->Model->SelectionType == weNavigation::STPYE_CLASS ? $this->Model->ClassID : $this->Model->DocTypeID)) . '
	</div>' .
			$this->getHTMLDirSelector() . '
	<div id="catSort" style="' . ($this->Model->SelectionType != weNavigation::STPYE_CATEGORY ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . ';">' .
			$this->getHTMLCategory() .
			$this->getHTMLFieldChooser(g_l('navigation', '[sort]'), 'SortField', $_sortVal, 'putSortField', $this->Model->SelectionType, ($this->Model->SelectionType == weNavigation::STPYE_CLASS ? $this->Model->ClassID : $this->Model->DocTypeID), $_sortSelect, 120) . '
	</div>
	<div id="dynUrl" style="' . ($this->Model->SelectionType == weNavigation::STPYE_CATEGORY ? 'display: block' : 'display: none') . '; width: ' . $this->_width_size . ';">' .
			$this->getHTMLLink('dynamic_') . '
	</div>
	<div style="width: ' . $this->_width_size . ';margin-top: ' . $this->_margin_top . ';">' .
			we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput('dynamic_Parameter', 58, $this->Model->Parameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size, 0), g_l('navigation', '[parameter]')) . '
	</div>' .
			$this->getHTMLCount() .
			we_button::create_button_table(
				array(
				we_button::create_button(
					'preview', 'javascript:' . $this->topFrame . '.we_cmd("dyn_preview");'),
				($this->Model->hasDynChilds() ? we_button::create_button(
						'delete_all', 'javascript:' . $this->topFrame . '.we_cmd("depopulate");') : '')
				)
				, 10, array('style' => 'margin-top:20px;'
			)) . '
</div>';
	}

	function getHTMLStatic($disabled = false){
		$seltype = array(
			weNavigation::STPYE_DOCLINK => g_l('navigation', '[docLink]')
		);
		if(defined('OBJECT_TABLE')){
			$seltype[weNavigation::STPYE_OBJLINK] = g_l('navigation', '[objLink]');
		}

		$rootDirID = 0;

		//javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . FILE_TABLE . "','document.we_form.elements[\\'LinkID\\'].value','document.we_form.elements[\\'LinkPath\\'].value','','" . session_id() . "','$rootDirID',''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['LinkPath'].value");

		$_cmd_doc = "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','" . session_id() . "','$rootDirID',''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";

		//javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . OBJECT_FILES_TABLE . "','document.we_form.elements[\\'LinkID\\'].value','document.we_form.elements[\\'LinkPath\\'].value','opener." . $this->topFrame . ".we_cmd(\"populateWorkspaces\");','" . session_id() . "','$rootDirID',''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : ''
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['LinkID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['LinkPath'].value");
		$wecmdenc3 = we_cmd_enc("opener." . $this->topFrame . ".we_cmd('populateWorkspaces');");
		$_cmd_obj = defined('OBJECT_TABLE') ? "javascript:we_cmd('openDocselector',document.we_form.elements['LinkID'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID',''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")" : '';
		$_cmd_cat = "javascript:we_cmd('openCatselector',document.we_form.elements['LinkID'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'LinkID\\'].value','document.we_form.elements[\\'LinkPath\\'].value','opener." . $this->topFrame . ".we_cmd(\"populateText\");opener." . $this->topFrame . ".mark();','" . session_id() . "','$rootDirID')";

		$_button_doc = we_button::create_button('select', $_cmd_doc, true, 100, 22, '', '', $disabled);
		$_button_obj = we_button::create_button('select', $_cmd_obj, true, 100, 22, '', '', $disabled);
		$_button_cat = we_button::create_button('select', $_cmd_cat, true, 100, 22, '', '', $disabled);

		$_buttons = '<div id="docLink" style="display: ' . ($this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? 'inline' : 'none') . '">' . $_button_doc . '</div><div id="objLink" style="display: ' . ($this->Model->SelectionType == weNavigation::STPYE_OBJLINK ? 'inline' : 'none') . '">' . $_button_obj . '</div><div id="catLink" style="display: ' . ($this->Model->SelectionType == weNavigation::STPYE_CATLINK ? 'inline' : 'none') . '">' . $_button_cat . '</div>';
		$_path = ($this->Model->LinkID == 0 ?
				'' :
				id_to_path($this->Model->LinkID, ($this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? FILE_TABLE : ($this->Model->SelectionType == weNavigation::STPYE_CATLINK ? CATEGORY_TABLE : (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : ''))))
			);

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("LinkPath");
		$yuiSuggest->setContentType(
			$this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? "folder,text/xml,text/webedition,image/*,text/html,application/*,application/x-shockwave-flash,video/quicktime" : ($this->Model->SelectionType == 'folder,objectFile' ? OBJECT_FILES_TABLE : ''));
		$yuiSuggest->setInput('LinkPath', $_path, array(
			"onChange" => $this->topFrame . ".mark();"
		));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('LinkID', $this->Model->LinkID);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setTable(
			$this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? FILE_TABLE : ($this->Model->SelectionType == weNavigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : CATEGORY_TABLE));
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton($_buttons);

		return '<div style="margin-top:' . $this->_margin_top . '">' . $yuiSuggest->getHTML() . '</div>' . $this->getHTMLWorkspace();
	}

	function getHTMLTab2(){
		/* if($this->Model->hasDynChilds()) {
		  return $this->getHTMLContentInfo();
		  } */
		$_seltype = array(
			weNavigation::STPYE_DOCTYPE => g_l('navigation', '[documents]')
		);

		if(defined("OBJECT_TABLE")){
			$_seltype[weNavigation::STPYE_CLASS] = g_l('navigation', '[objects]');
			$_onCahngeJS = "if(document.we_form.Selection.value=='" . weNavigation::SELECTION_STATIC . "'){YAHOO.autocoml.modifySetById('yuiAcInputLinkPath','table',(this.value=='" . weNavigation::STPYE_DOCLINK . "'?'" . FILE_TABLE . "':(this.value=='" . weNavigation::STPYE_OBJLINK . "'?'" . OBJECT_TABLE . "':'" . CATEGORY_TABLE . "')));}else{};";
		} else{
			$_onCahngeJS = "if(document.we_form.Selection.value=='" . weNavigation::SELECTION_STATIC . "'){YAHOO.autocoml.modifySetById('yuiAcInputLinkPath','table',(this.value=='" . weNavigation::STPYE_DOCLINK . "'?'" . FILE_TABLE . "':'" . CATEGORY_TABLE . "'));}else{};";
		}

		$_seltype[weNavigation::STPYE_CATEGORY] = g_l('navigation', '[categories]');

		$_selection_block = $this->View->htmlHidden(
				'CategoriesControl', (isset($_REQUEST['CategoriesCount']) ? $_REQUEST['CategoriesCount'] : 0)) . $this->View->htmlHidden(
				'SortControl', (isset($_REQUEST['SortCount']) ? $_REQUEST['SortCount'] : 0)) . $this->View->htmlHidden(
				'CategoriesCount', (isset($this->Model->Categories) ? count($this->Model->Categories) : '0')) . $this->View->htmlHidden(
				'SortCount', (isset($this->Model->Sort) ? count($this->Model->Sort) : '0')) . '
			<div style="display: block;">
				<div style="display:block;">
				' . we_html_tools::htmlSelect(
				'Selection', array(
				weNavigation::SELECTION_NODYNAMIC => g_l('navigation', '[no_dyn_content]'),
				weNavigation::SELECTION_DYNAMIC => g_l('navigation', '[dyn_content]')
				), 1, $this->Model->Selection, false, 'style="width: ' . $this->_width_size . 'px;" onChange="toggle(\'dynamic\');setPresentation(\'dynamic\');setWorkspaces(\'\');' . $this->topFrame . '.mark();setCustomerFilter(this);"', 'value', $this->_width_size) . '
				</div>
				<div id="dynamic" style="' . ($this->Model->Selection == weNavigation::SELECTION_DYNAMIC ? 'display: block;' : 'display: none;') . ';margin-top:' . $this->_margin_top . '">
				' . we_html_tools::htmlSelect(
				'SelectionType', $_seltype, 1, $this->Model->SelectionType, false, 'onChange="onSelectionTypeChangeJS(this); closeAllType();clearFields();toggle(this.value);setWorkspaces(this.value);setStaticSelection(this.value);' . $this->topFrame . '.mark();"', 'value', $this->_width_size) . $this->getHTMLDynamic() . '</div>
			</div>';


		return array(
			array(
				'headline' => g_l('navigation', '[content]'),
				'html' => $_selection_block,
				'space' => $this->_space_size
		));
	}

	function getHTMLContentInfo(){
		$_sort = array();
		foreach($this->Model->Sort as $_i){
			$_sort[] = $_i['field'] . '&nbsp;(' . ($_i['order'] == 'DESC' ? g_l('navigation', '[descending]') : g_l('navigation', '[ascending]')) . ')';
		}

		$_table = new we_html_table(
			array(
			'width' => $this->_width_size,
			'cellpadding' => '0',
			'cellspacing' => '0',
			'border' => '0',
			'class' => 'defaultfont'
			), 9, 2);

		if($this->Model->SelectionType == weNavigation::STPYE_DOCTYPE){

			$_table->setCol(0, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[documents]'));

			if(!empty($this->Model->DocTypeID)){
				$_dt = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($this->Model->DocTypeID), 'DocType', new DB_WE());
				$_table->setCol(1, 0, array(
					'style' => 'font-weight: bold;'
					), g_l('navigation', '[doctype]') . ':');
				$_table->setColContent(1, 1, $_dt);
			}
		} else
		if($this->Model->SelectionType == weNavigation::STPYE_CATEGORY){
			$_table->setCol(0, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[categories]'));
		} else{
			$_table->setCol(0, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[objects]'));
			$_cn = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->Model->ClassID), 'Text', new DB_WE());
			$_table->setCol(1, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[class]') . ':');
			$_table->setColContent(1, 1, $_cn);

			$_table->setCol(2, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[workspace]') . ':');
			$_table->setColContent(2, 1, id_to_path($this->Model->WorkspaceID));
		}

		if($this->Model->SelectionType != weNavigation::STPYE_CATEGORY && !empty($this->Model->TitleField)){
			$_table->setCol(3, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[title_field]') . ':');
			$_table->setColContent(3, 1, $this->Model->TitleField);
		}

		$_table->setCol(4, 0, array(
			'style' => 'font-weight: bold;'
			), g_l('navigation', '[dir]') . ':');
		switch($this->Model->SelectionType){
			case weNavigation::STPYE_DOCTYPE:
				$_table->setColContent(4, 1, id_to_path($this->Model->FolderID, FILE_TABLE));
				break;
			case weNavigation::STPYE_CATEGORY:
				$_table->setColContent(4, 1, id_to_path($this->Model->FolderID, CATEGORY_TABLE));
				break;
			default:
				if(defined('OBJECT_FILES_TABLE')){
					$_table->setColContent(4, 1, id_to_path($this->Model->FolderID, OBJECT_FILES_TABLE));
				}
		}

		if($this->Model->SelectionType != weNavigation::STPYE_CATEGORY){
			if(!empty($this->Model->Categories)){
				$_table->setCol(5, 0, array(
					'style' => 'font-weight: bold;'
					), g_l('navigation', '[categories]') . ':');
				$_table->setColContent(5, 1, implode('<br />', $this->Model->Categories));
			}

			if(!empty($_sort)){
				$_table->setCol(6, 0, array(
					'style' => 'font-weight: bold;'
					), g_l('navigation', '[sort]') . ':');
				$_table->setColContent(6, 1, implode('<br />', $_sort));
			}
		}

		if(!empty($this->Model->Url) && $this->Model->Url != 'http://'){
			$_table->setCol(7, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[urlLink]') . ':');
			$_table->setColContent(7, 1, $this->Model->Url);
		}

		if(!empty($this->Model->Paramter)){
			$_table->setCol(8, 0, array(
				'style' => 'font-weight: bold;'
				), g_l('navigation', '[parameter]') . ':');
			$_table->setColContent(8, 1, $this->Model->Parameter);
		}

		$_table->setCol(8, 0, array(
			'style' => 'font-weight: bold;'
			), g_l('navigation', '[show_count]') . ':');
		$_table->setColContent(8, 1, $this->Model->ShowCount);

		return array(
			array(
				'headline' => g_l('navigation', '[entries]'),
				'html' => we_html_tools::htmlSelect('dynContent', $this->View->getItems($this->Model->ID), 20, '', false, 'style="width: ' . $this->_width_size . 'px; height: 200px;  margin: 0 0 ' . $this->_margin_bottom . ' 0;"'),
				'space' => $this->_space_size,
				'noline' => 0
			),
			array(
				'headline' => '',
				'html' => we_button::create_button_table(
					array(
						we_button::create_button('preview', 'javascript:' . $this->topFrame . '.we_cmd("dyn_preview");'),
						we_button::create_button('refresh', 'javascript:' . $this->topFrame . '.we_cmd("populate");'),
						we_button::create_button('delete_all', 'javascript:' . $this->topFrame . '.we_cmd("depopulate");')
				)),
				'space' => $this->_space_size
			),
			array(
				'headline' => g_l('navigation', '[content]'),
				'html' => $_table->getHTML(),
				'space' => $this->_space_size
			),
		);
	}

	function getHTMLEditorPreview(){

		$defaultPreviewCode = str_replace(array("\r\n", "\n"), '\n', addslashes($this->Model->defaultPreviewCode));
		// build the page
		$out = '<table border="0" class="defaultfont" cellpadding="0" cellspacing="0">
		<tr>
			<td><iframe name="preview" style="background: white; border: 1px solid black; width: 640px; height: 150px" src="edit_navigation_frameset.php?pnt=previewIframe"></iframe></td>
		</tr>
		<tr>
			<td height="30"><label for="previewCode">' . g_l('navigation', '[preview_code]') . '</label><br /></td>
		<tr>
			<td>' . we_html_element::htmlTextArea(
				array(
				'name' => 'previewCode',
				'id' => 'previewCode',
				'style' => 'width: 640px; height: 200px;',
				'class' => 'defaultfont'
				), $this->Model->previewCode) . '</td>
		</tr>
		<tr>
			<td height="10"></td>
		<tr>
		<tr>
			<td align="right">' . we_button::create_button_table(
				array(
					we_button::create_button('refresh', 'javascript: showPreview();'),
					we_button::create_button(
						'reset', 'javascript: document.getElementById("previewCode").value = "' . $defaultPreviewCode . '"; showPreview();')
				)//,we_button::create_button('new_template', 'javascript: '.$this->topFrame.'.we_cmd("create_template");')
			) . '</td>
		</tr>
		</table>';

		return we_html_element::jsElement(
				'
					function showPreview() {
						document.we_form.pnt.value="previewIframe";
						submitForm("preview");
					}
				') . $this->View->getCommonHiddens(array(
				'pnt' => 'preview', 'tabnr' => 'preview'
			)) . we_html_tools::htmlDialogLayout($out, g_l('navigation', '[preview]'));
	}

	function getHTMLEditorPreviewIframe(){

		require_once (WE_INCLUDES_PATH . 'we_tag.inc.php');

		$templateCode = $this->Model->previewCode;

		// if id in template is same as id in session_navigation object,
		// use dynamic entries
		$pattern = '/parentid="(.*)"/';
		if(preg_match($pattern, $templateCode, $matches)){

			if($matches[1] == $this->Model->ID){
				$GLOBALS['initNavigationFromSession'] = true;
			}
		}

		// initialize a document (only for caching needed)
		$GLOBALS['we_doc'] = new we_webEditionDocument();
		$GLOBALS['weNoCache'] = true;

		$tp = new we_tag_tagParser($templateCode);

		$tp->parseTags($templateCode);

		eval('?>' . $templateCode);
	}

	function getHTMLProperties($preselect = ''){
		$tabNr = isset($_REQUEST['tabnr']) ? ($_REQUEST['tabnr']) : 1;

		if($this->Model->IsFolder == 0 && $tabNr != 1 && $tabNr != 3){
			$tabNr = 1;
		}

		$_onSelectionTypeChangeJS = '
<script type="text/javascript">
function onSelectionTypeChangeJS(elem) {
	if(document.we_form.Selection.value=="' . weNavigation::SELECTION_STATIC . '"){
		';
		if(defined("OBJECT_TABLE")){
			$_onSelectionTypeChangeJS .= 'YAHOO.autocoml.modifySetById("yuiAcInputLinkPath","table",(elem.value=="' . weNavigation::STPYE_DOCLINK . '" || elem.value=="' . weNavigation::STPYE_DOCLINK . '" ? "' . FILE_TABLE . '":(elem.value=="' . weNavigation::STPYE_OBJLINK . '" || elem.value=="' . weNavigation::STPYE_CLASS . '" ? "' . OBJECT_TABLE . '":"' . CATEGORY_TABLE . '")));';
		} else{
			$_onSelectionTypeChangeJS .= 'YAHOO.autocoml.modifySetById("yuiAcInputLinkPath","table",(elem.value=="' . weNavigation::STPYE_DOCLINK . '" || elem.value=="' . weNavigation::STPYE_DOCLINK . '"? "' . FILE_TABLE . '":"' . CATEGORY_TABLE . '"));';
		}
		$_onSelectionTypeChangeJS .= '
	} else {
		';
		$_onSelectionTypeChangeJS .= (defined("OBJECT_FILES_TABLE") ?
				'YAHOO.autocoml.modifySetById("yuiAcInputFolderPath","table",(elem.value=="' . weNavigation::STPYE_DOCTYPE . '"?"' . FILE_TABLE . '":(elem.value=="' . weNavigation::STPYE_CLASS . '"?"' . OBJECT_FILES_TABLE . '":"' . CATEGORY_TABLE . '")));' :
				'YAHOO.autocoml.modifySetById("yuiAcInputFolderPath","table",(elem.value=="' . weNavigation::STPYE_DOCTYPE . '"?"' . FILE_TABLE . '":"' . CATEGORY_TABLE . '"));'
			) . '
	}
}
function onFolderSelectionChangeJS(elem) {
	YAHOO.autocoml.modifySetById("yuiAcInputLinkPath","table",(elem.value=="' . weNavigation::STPYE_DOCLINK . '"?"' . FILE_TABLE . '":"' . (defined(
				'OBJECT_TABLE') ? OBJECT_FILES_TABLE : FILE_TABLE) . '"));
}
</script>';

		$out = $_onSelectionTypeChangeJS;

		$hiddens = array(
			'cmd' => '',
			'pnt' => 'edbody',
			'tabnr' => $tabNr,
			'vernr' => (isset($_REQUEST['vernr']) ? $_REQUEST['vernr'] : 0),
			'delayParam' => (isset($_REQUEST['delayParam']) ? $_REQUEST['delayParam'] : '')
		);

		$yuiSuggest = & weSuggest::getInstance();
		if($tabNr == 'preview'){
			$out .= $this->getHTMLEditorPreview();
		} else{
			// Property tab content
			$out .= $yuiSuggest->getYuiJsFiles() .
				we_html_element::htmlDiv(
					array(
					'id' => 'tab1', 'style' => ($tabNr == 1 ? 'display: block;' : 'display: none')
					), $this->View->getCommonHiddens($hiddens) .
					$this->View->htmlHidden(
						'IsFolder', (isset($this->Model->IsFolder) ? $this->Model->IsFolder : '0')) . $this->View->htmlHidden(
						'presetFolder', (isset($_REQUEST['presetFolder']) ? $_REQUEST['presetFolder'] : '0')) .
					we_multiIconBox::getHTML(
						'', '100%', $this->getHTMLGeneral(), 30, '', -1, '', '', false, $preselect) .
					($this->Model->IsFolder ? we_multiIconBox::getHTML(
							'', '100%', $this->getHTMLPropertiesGroup(), 30, '', -1, '', '', false, $preselect) : we_multiIconBox::getHTML(
							'', '100%', $this->getHTMLPropertiesItem(), 30, '', -1, '', '', false, $preselect)) . (($this->Model->Selection == weNavigation::SELECTION_STATIC || $this->Model->IsFolder) ? $this->getHTMLAttributes() : '')) . ($this->Model->IsFolder ? (we_html_element::htmlDiv(
						array(
						'id' => 'tab2', 'style' => ($tabNr == 2 ? 'display: block;' : 'display: none')
						), we_multiIconBox::getHTML(
							'', '100%', $this->getHTMLTab2(), 30, '', -1, '', '', false, $preselect))) :
					'') . ((defined('CUSTOMER_TABLE')) ? we_html_element::htmlDiv(
						array(
						'id' => 'tab3', 'style' => ($tabNr == 3 ? 'display: block;' : 'display: none')
						), we_multiIconBox::getHTML(
							'', '100%', $this->getHTMLTab3(), 30, '', -1, '', '', false, $preselect)) : '');
		}
		$out .= $yuiSuggest->getYuiCss() .
			$yuiSuggest->getYuiJs();
		return $out;
	}

	function htmlTextInput($name, $size = 24, $value = "", $maxlength = "", $attribs = "", $type = "text", $width = "0", $height = "0", $markHot = "", $disabled = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . ((strpos($width, "px") || strpos(
					$width, "%")) ? "" : "px") . ';') : '') . ($height ? ('height: ' . $height . ((strpos($height, "px") || strpos(
					$height, "%")) ? "" : "px") . ';') : '') . '"') : '';
		return '<input' . ($markHot ? ' onchange="if(_EditorFrame){_EditorFrame.setEditorIsHot(true);}' . $markHot . '.hot=1;"' : '') . (strstr(
				$attribs, "class=") ? "" : ' class="wetextinput"') . ' type="' . trim($type) . '" name="' . trim($name) . '" size="' . abs(
				$size) . '" value="' . oldHtmlspecialchars($value) . '"' . ($maxlength ? (' maxlength="' . abs(
					$maxlength) . '"') : '') . ($attribs ? " $attribs" : '') . $style . ' onblur="weInputAppendClass(this, \'wetextinput\'); weInputRemoveClass(this, \'wetextinputselected\');" onfocus="weInputAppendClass(this, \'wetextinputselected\'); weInputRemoveClass(this, \'wetextinput\');" ' . ($disabled ? (' disabled="true"') : '') . ' />';
	}

	function getHTMLFieldChooser($title, $name, $value, $cmd, $type, $selection, $extraField = "", $extraFieldWidth = 0){

		$_disabled = !(($this->Model->SelectionType == weNavigation::STPYE_CLASS && $this->Model->ClassID != 0) || ($this->Model->SelectionType == weNavigation::STPYE_DOCTYPE && $this->Model->DocTypeID != 0));
		$_cmd = "javascript:var st=document.we_form.SelectionType.options[document.we_form.SelectionType.selectedIndex].value; var s=(st=='" . weNavigation::STPYE_DOCTYPE . "' ? document.we_form.DocTypeID.options[document.we_form.DocTypeID.selectedIndex].value : document.we_form.ClassID.options[document.we_form.ClassID.selectedIndex].value); we_cmd('openFieldSelector','$cmd',st,s,0)";
		$_button = we_button::create_button('select', $_cmd, true, 100, 22, '', '', $_disabled, false, "_$name");
		if($extraField == ""){
			$showValue = stristr($value, "_") ? substr($value, strpos($value, "_") + 1) : $value;
			return we_html_tools::htmlFormElementTable(
					we_html_tools::hidden($name, $value) . $this->htmlTextInput(
						"__" . $name, 58, $showValue, '', 'onchange="setFieldValue(\'' . $name . '\',this); ' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 120), 0), $title, 'left', 'defaultfont', '', we_html_tools::getPixel(20, 4), $_button);
		} else{
			$showValue = stristr($value, "_") ? substr($value, strpos($value, "_") + 1) : $value;
			return we_html_tools::htmlFormElementTable(
					we_html_tools::hidden($name, $value) . $this->htmlTextInput(
						"__" . $name, 58, $showValue, '', 'onchange="setFieldValue(\'' . $name . '\',this); ' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 120) - abs($extraFieldWidth) - 8, 0), $title, 'left', 'defaultfont', '', we_html_tools::getPixel(20, 4), $extraField, we_html_tools::getPixel(10, 4), $_button);
		}
	}

	function getHTMLChooser($title, $table = FILE_TABLE, $rootDirID = 0, $IDName = 'ID', $IDValue = '', $PathName = 'Path', $cmd = '', $filter = 'text/webedition', $disabled = false, $showtrash = false){
		if($IDValue == '0'){
			$_path = '/';
		} elseif(isset($this->Model->$IDName) && !empty($this->Model->$IDName)){
			$_path = id_to_path($this->Model->$IDName, $table);
		} else{
			$acQuery = new weSelectorQuery();
			if(isset($IDValue) && $IDValue !== ""){
				$acResponse = $acQuery->getItemById($IDValue, $table, array(
					"IsFolder", "Path"
				));
				if($acResponse && $acResponse[0]['IsFolder']){
					$_path = $acResponse[0]['IsFolder'];
				} else{
					// return with errormessage
				}
			} else{
				$_path = "";
			}
		}

		if($table == NAVIGATION_TABLE){
			$_cmd = "javascript:we_cmd('openNavigationDirselector',document.we_form.elements['" . $IDName . "'].value,'document.we_form." . $IDName . ".value','document.we_form." . $PathName . ".value','" . $cmd . "')";
			$_selector = "dirSelector";
		} else{
			if($filter == 'folder'){
				$_cmd = "javascript:we_cmd('openSelector',document.we_form.elements['$IDName'].value,'$table','document.we_form.$IDName.value','document.we_form.$PathName.value','" . $cmd . "','" . session_id() . "','$rootDirID')";
				$_selector = "dirSelector";
			} else{
				//javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'$table','document.we_form.$IDName.value','document.we_form.$PathName.value','" . $cmd . "','" . session_id() . "','$rootDirID','$filter'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")
				$wecmdenc1 = we_cmd_enc("document.we_form.$IDName.value");
				$wecmdenc2 = we_cmd_enc("document.we_form.$PathName.value");
				$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
				$_cmd = "javascript:we_cmd('openDocselector',document.we_form.elements['$IDName'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID','$filter'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";
				$_selector = "docSelector";
			}
		}
		$mayBeEmpty = 0;
		if($_selector == "docSelector"){
			$_path = $_path == '/' ? "" : $_path;
			$mayBeEmpty = 1;
		}

		if($showtrash){
			$_button = we_button::create_button_table(
					array(
					we_button::create_button('select', $_cmd, true, 100, 22, '', '', $disabled),
					we_button::create_button(
						'image:btn_function_trash', 'javascript:document.we_form.elements["' . $IDName . '"].value=0;document.we_form.elements["' . $PathName . '"].value="/";', true, 27, 22)
					), 10);
			$_width = 157;
		} else{
			$_width = 120;
			$_button = we_button::create_button('select', $_cmd, true, 100, 22, '', '', $disabled);
		}

		//$_input  = we_html_tools::htmlTextInput($PathName,58,$_path,'','id="yuiAcInput'.$PathName.'"','text',($this->_width_size-$_width),0);
		$_result = we_html_element::htmlHidden(
				array(
					'name' => $IDName, 'value' => $IDValue, 'id' => 'yuiAcResult' . $PathName
		));
		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId($PathName);
		$yuiSuggest->setContentType($filter);
		$yuiSuggest->setInput($PathName, $_path, array(
			"onChange" => $this->topFrame . ".mark();"
		));
		$yuiSuggest->setLabel($title);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty($mayBeEmpty);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector($_selector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth($this->_width_size - $_width);
		$yuiSuggest->setSelectButton($_button);

		$weAcSelector = $yuiSuggest->getHTML();
		if(isset($weAcSelector)){
			return $weAcSelector;
		} else{
			return we_html_tools::htmlFormElementTable(
					we_html_tools::htmlTextInput($PathName, 58, $_path, '', 'readonly', 'text', ($this->_width_size - $_width), 0), $title, 'left', 'defaultfont', we_html_element::htmlHidden(array(
						'name' => $IDName, 'value' => $IDValue
					)), we_html_tools::getPixel(20, 4), $_button);
		}
	}

	function getHTMLCategory(){
		$addbut = we_button::create_button("add", "javascript:we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths);opener." . $this->topFrame . ".mark();')");
		$del_but = addslashes(
			we_html_element::htmlImg(
				array(
					'src' => BUTTONS_DIR . 'btn_function_trash.gif',
					'onclick' => 'javascript:#####placeHolder#####;' . $this->topFrame . '.mark();',
					'style' => 'cursor: pointer; width: 27px;'
		)));

		$variant_js = '
var categories_edit = new multi_edit("categories",document.we_form,0,"' . $del_but . '",' . ($this->_width_size - 10) . ',false);
categories_edit.addVariant();

document.we_form.CategoriesControl.value = categories_edit.name;';

		if(is_array($this->Model->Categories)){
			foreach($this->Model->Categories as $cat){

				$variant_js .= '
categories_edit.addItem();
categories_edit.setItem(0,(categories_edit.itemCount-1),"' . $cat . '");';
			}
		}

		$variant_js .= 'categories_edit.showVariant(0);';

		$js = we_html_element::jsElement($variant_js);

		$table = new we_html_table(
			array(
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'cellpadding' => 0,
			'cellspacing' => 0,
			'border' => 0
			), 5, 1);

		$table->setColContent(0, 0, we_html_tools::getPixel(5, 5));
		$table->setCol(1, 0, array(
			'class' => 'defaultfont'
			), g_l('navigation', '[categories]'));
		$table->setColContent(
			2, 0, we_html_element::htmlDiv(
				array(
					'id' => 'categories',
					'class' => 'blockWrapper',
					'style' => 'width: ' . ($this->_width_size) . 'px; height: 60px; border: #AAAAAA solid 1px;'
		)));

		$table->setColContent(3, 0, we_html_tools::getPixel(5, 5));

		$table->setCol(
			4, 0, array(
			'colspan' => '2', 'align' => 'right'
			), we_button::create_button_table(
				array(
					we_button::create_button("delete_all", "javascript:removeAllCats()"), $addbut
		)));

		return $table->getHtml() . $js . we_html_element::jsElement('
function removeAllCats(){
	' . $this->topFrame . '.mark();
	if(categories_edit.itemCount>0){
		while(categories_edit.itemCount>0){
			categories_edit.delItem(categories_edit.itemCount);
		}
	}
}

function addCat(paths){
	' . $this->topFrame . '.mark();
	var path = paths.split(",");
	var found = false;
	var j = 0;
	for (var i = 0; i < path.length; i++) {
		if(path[i]!="") {
			found = false;
			for(j=0;j<categories_edit.itemCount;j++){
				if(categories_edit.form.elements[categories_edit.name+"_variant0_"+categories_edit.name+"_item"+j].value == path[i]) {
					found = true;
				}
			}
			if(!found) {
				categories_edit.addItem();
				categories_edit.setItem(0,(categories_edit.itemCount-1),path[i]);
			}
		}
	}
	categories_edit.showVariant(0);
}');
	}

	function getHTMLFieldSelector(){
		$_type = $_REQUEST['type']; // doctype || class
		$_selection = $_REQUEST['selection']; // templateid or classid
		$_cmd = $_REQUEST['cmd']; // js command
		$_multi = $_REQUEST['multi']; // js command
		$_js = we_button::create_state_changer(false) . '
function setFields() {
	var list = document.we_form.fields.options;

	var fields = new Array();
	for(i=0;i<list.length;i++){
		if(list[i].selected){
			fields.push(list[i].value);
		}
	}
	opener.' . $_cmd . '(fields.join(","));
	self.close();

}

function selectItem() {
	if(document.we_form.fields.selectedIndex>-1){
		switch_button_state("save", "save_enabled", "enabled");
	}
}';

		$__fields = array();

		if($_type == weNavigation::STPYE_DOCTYPE){

			$_db = new DB_WE();
			$_fields = array();
			$_templates = f('SELECT Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($_selection), 'Templates', $_db);
			$_ids = makeArrayFromCSV($_templates);

			foreach($_ids as $_templateID){
				$_template = new we_template();
				$_template->initByID($_templateID, TEMPLATES_TABLE);
				$_fields = array_merge($_fields, $_template->readAllVariantFields(true));
			}
			$__fields = array_keys($_fields);
			$__tmp = array();
			foreach($__fields as $val){
				$__tmp[$val] = $val;
			}
			$__fields = $__tmp;
		} else{
			if(defined('OBJECT_TABLE')){

				$_class = new we_object();
				$_class->initByID($_selection, OBJECT_TABLE);
				$_fields = $_class->getAllVariantFields();
				foreach($_fields as $_key => $val){
					$__fields[$_key] = substr($_key, strpos($_key, "_") + 1);
				}
			}
		}
		$_parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlSelect(
					'fields', $__fields, 20, '', ($_multi ? true : false), 'style="width: 300px; height: 200px; margin: ' . $this->_margin_bottom . ' 0px ' . $this->_margin_bottom . ' 0px;" onClick="setTimeout(\'selectItem();\',100);"'),
				'space' => 0
			)
		);
		$button = we_button::position_yes_no_cancel(
				we_button::create_button('save', 'javascript:setFields();', true, 100, 22, '', '', true, false), null, we_button::create_button('close', 'javascript:self.close();'));

		we_button::create_button_table(
			array(
				we_button::create_button(
					'save', 'javascript:setFields();', true, 100, 22, '', '', true, false), we_button::create_button('close', 'javascript:self.close();')
		));

		$_body = we_html_element::htmlBody(
				array(
				"class" => "weDialogBody", "onLoad" => "loaded=1;"
				), we_html_element::htmlForm(
					array(
					"name" => "we_form", "onsubmit" => "return false"
					), we_multiIconBox::getHTML(
						'', '100%', $_parts, 30, $button, -1, '', '', false, g_l('navigation', '[select_field_txt]'))));

		return $this->getHTMLDocument($_body, we_html_element::jsElement($_js));
	}

	function getHTMLCount(){
		return '<div style="width: ' . $this->_width_size . '; margin-top: ' . $this->_margin_top . '">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'ShowCount', 30, $this->Model->ShowCount, '', 'onBlur="var r=parseInt(this.value);if(isNaN(r)) this.value=' . $this->Model->ShowCount . '; else{ this.value=r; ' . $this->topFrame . '.mark();}"', 'text', $this->_width_size, 0), g_l('navigation', '[show_count]')) . '</div>';
	}

	function getHTMLDirSelector(){
		$rootDirID = 0;

		//javascript:we_cmd('openDirselector',document.we_form.elements['FolderID'].value,'" . FILE_TABLE . "','document.we_form.elements[\\'FolderID\\'].value','document.we_form.elements[\\'FolderPath\\'].value','opener." . $this->topFrame . ".mark();','" . session_id() . "','$rootDirID')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['FolderID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['FolderPath'].value");
		$wecmdenc3 = we_cmd_enc("opener." . $this->topFrame . ".mark();");
		$_button_doc = we_button::create_button(
				'select', "javascript:we_cmd('openDirselector',document.we_form.elements['FolderID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID')");
		$_countClasses = 1;
		if(defined('OBJECT_FILES_TABLE') && $this->Model->SelectionType == weNavigation::STPYE_CLASS){
			$_countClasses = f('SELECT COUNT(1) AS Count FROM ' . OBJECT_FILES_TABLE, 'Count', $GLOBALS['DB_WE']);
		}
		//javascript:we_cmd('openDirselector',document.we_form.elements['FolderID'].value,'" . OBJECT_FILES_TABLE . "','document.we_form.elements[\\'FolderID\\'].value','document.we_form.elements[\\'FolderPath\\'].value','opener." . $this->topFrame . ".mark();','" . session_id() . "',objectDirs[document.we_form.elements['ClassID'].options[document.we_form.elements['ClassID'].selectedIndex].value])
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['FolderID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['FolderPath'].value");
		$wecmdenc3 = we_cmd_enc("opener." . $this->topFrame . ".mark();");
		$_button_obj = defined('OBJECT_TABLE') ? we_button::create_button(
				'select', "javascript:we_cmd('openDirselector',document.we_form.elements['FolderID'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "',objectDirs[document.we_form.elements['ClassID'].options[document.we_form.elements['ClassID'].selectedIndex].value])", true, 100, 22, "", "", ($_countClasses ? false : true), false, "_XFolder") : '';

		$_button_cat = we_button::create_button('select', "javascript:we_cmd('openCatselector',document.we_form.elements['FolderID'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'FolderID\\'].value','document.we_form.elements[\\'FolderPath\\'].value','opener." . $this->topFrame . ".mark();','" . session_id() . "','$rootDirID')");

		$_buttons = '<div id="docFolder" style="display: ' . (($this->Model->SelectionType == weNavigation::STPYE_DOCTYPE) ? 'inline' : 'none') . '">' . $_button_doc . '</div><div id="objFolder" style="display: ' . ($this->Model->SelectionType == weNavigation::STPYE_CLASS ? 'inline' : 'none') . '">' . $_button_obj . '</div><div id="catFolder" style="display: ' . ($this->Model->SelectionType == weNavigation::STPYE_CATEGORY ? 'inline' : 'none') . '">' . $_button_cat . '</div>';

		$_path = id_to_path(
			$this->Model->FolderID, (($this->Model->SelectionType == weNavigation::STPYE_DOCTYPE) ? FILE_TABLE : ($this->Model->SelectionType == weNavigation::STPYE_CATEGORY ? CATEGORY_TABLE : (defined(
						'OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : ''))));

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("FolderPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput(
			'FolderPath', $_path, array(
			"onChange" => $this->topFrame . ".mark();", "disabled" => ($_countClasses ? "false" : "true")
		));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('FolderID', $this->Model->FolderID);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setLabel(g_l('navigation', '[dir]'));
		$yuiSuggest->setTable($this->Model->SelectionType == weNavigation::STPYE_DOCLINK ? FILE_TABLE : ($this->Model->SelectionType == weNavigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE));
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton($_buttons);

		$weAcSelector = $yuiSuggest->getHTML();

		return we_html_element::htmlDiv(array(
				'style' => 'margin-top:' . $this->_margin_top . ';'
				), $weAcSelector);
	}

	function getHTMLDynPreview(){
		$_select = new we_html_select(
			array(
			'size' => '20',
			'style' => 'width: 420px; height: 200; margin: ' . $this->_margin_bottom . ' 0 ' . $this->_margin_bottom . ' 0;'
		));

		$_items = $this->Model->getDynamicEntries();

		foreach($_items as $_k => $_item){
			$_txt = id_to_path(
				$_item['id'], ($this->Model->SelectionType == weNavigation::STPYE_DOCTYPE) ? FILE_TABLE : ($this->Model->SelectionType == weNavigation::STPYE_CATEGORY ? CATEGORY_TABLE : OBJECT_FILES_TABLE));
			if(!empty($_item['field'])){
				$_opt = we_html_select::getNewOptionGroup(
						array(
							'style' => 'font-weight: bold; font-style: normal; color: darkblue;',
							'label' => $_item['field']
				));
				$_opt->addChild(we_html_select::getNewOption($_k, $_txt));
				$_select->addChild($_opt);
			} else{
				$_select->addOption($_k, $_txt);
			}
		}

		$_parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlFormElementTable(
					$_select->getHtml(), $this->Model->SelectionType == weNavigation::STPYE_CATEGORY ? g_l('navigation', '[categories]') : ($this->Model->SelectionType == weNavigation::STPYE_CLASS ? g_l('navigation', '[objects]') : g_l('navigation', '[documents]'))),
				'space' => 0
		));

		$_body = we_html_element::htmlBody(
				array(
				"class" => "weDialogBody"
				), we_html_element::htmlForm(
					array(
					'name' => 'we_form', 'onsubmit' => 'return false'
					), we_multiIconBox::getHTML(
						'', '100%', $_parts, 30, '<div style="float:right;">' . we_button::create_button(
							'close', 'javascript:self.close();') . '</div>', -1, '', '', false, g_l('navigation', '[dyn_selection]'))));

		return $this->getHTMLDocument($_body, '');
	}

	function getHTMLWorkspace($type = 'object', $defClassID = 0, $field = 'WorkspaceID'){
		$_wsid = array();

		if($type == 'class'){

			if($this->Model->SelectionType == weNavigation::STPYE_CLASS && $this->Model->ClassID){
				$_wsid = weDynList::getWorkspacesForClass($this->Model->ClassID);
			} elseif($defClassID){
				$_wsid = weDynList::getWorkspacesForClass($defClassID);
			}

			return '<div id="objLinkWorkspaceClass" style="display: ' . (($this->Model->Selection == weNavigation::SELECTION_DYNAMIC) ? 'block' : 'none') . ';margin-top: ' . $this->_margin_top . ';">' . we_html_tools::htmlFormElementTable(
					we_html_tools::htmlSelect(
						'WorkspaceIDClass', $_wsid, 1, $this->Model->WorkspaceID, false, 'style="width: ' . $this->_width_size . 'px;" onChange="' . $this->topFrame . '.mark();"', 'value'), g_l('navigation', '[workspace]')) . '</div>';
		} else{

			if($field == 'WorkspaceID'){
				if($this->Model->SelectionType == weNavigation::STPYE_OBJLINK && $this->Model->LinkID){
					$_wsid = weDynList::getWorkspacesForObject($this->Model->LinkID);
				}

				return '<div id="objLinkWorkspace" style="display: ' . (($this->Model->SelectionType == weNavigation::STPYE_OBJLINK && ($this->Model->WorkspaceID > -1)) ? 'block' : 'none') . ';margin-top: ' . $this->_margin_top . ';">' . we_html_tools::htmlFormElementTable(
						we_html_tools::htmlSelect(
							'WorkspaceID', $_wsid, 1, $this->Model->WorkspaceID, false, 'style="width: ' . $this->_width_size . 'px;" onChange="' . $this->topFrame . '.mark();"', 'value'), g_l('navigation', '[workspace]')) . '</div>';
			} else{
				if($this->Model->FolderSelection == weNavigation::STPYE_OBJLINK && $this->Model->LinkID){
					$_wsid = weDynList::getWorkspacesForObject($this->Model->LinkID);
				}

				return '<div id="objLinkFolderWorkspace" style="display: ' . (($this->Model->FolderSelection == weNavigation::STPYE_OBJLINK && ($this->Model->FolderWsID > -1)) ? 'block' : 'none') . ';margin-top: ' . $this->_margin_top . ';">' . we_html_tools::htmlFormElementTable(
						we_html_tools::htmlSelect(
							'FolderWsID', $_wsid, 1, $this->Model->FolderWsID, false, 'style="width: ' . $this->_width_size . 'px;" onChange="' . $this->topFrame . '.mark();"', 'value'), g_l('navigation', '[workspace]')) . '</div>';
			}
		}
	}

	function getHTMLLink($prefix = ''){
		//javascript:we_cmd('openDocselector',document.we_form.elements['" . $prefix . "UrlID'].value,'" . FILE_TABLE . "','document.we_form.elements[\\'" . $prefix . "UrlID\\'].value','document.we_form.elements[\\'" . $prefix . "UrlIDPath\\'].value','opener." . $this->topFrame . ".mark()','" . session_id() . "',0,'text/webedition'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['" . $prefix . "UrlID'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['" . $prefix . "UrlIDPath'].value");
		$wecmdenc3 = we_cmd_enc("opener." . $this->topFrame . ".mark()");
		$_cmd = "javascript:we_cmd('openDocselector',document.we_form.elements['" . $prefix . "UrlID'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "',0,'text/webedition'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")";

		$_path = id_to_path($this->Model->UrlID);

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("UrlIDPath");
		$yuiSuggest->setContentType(
			"folder,text/xml,text/webedition,image/*,text/html,application/*,application/x-shockwave-flash,video/quicktime");
		$yuiSuggest->setInput($prefix . 'UrlIDPath', $_path, array(
			"onChange" => $this->topFrame . ".mark();"
		));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($prefix . 'UrlID', $this->Model->UrlID);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setWidth($this->_width_size - 120);
		$yuiSuggest->setSelectButton(we_button::create_button('select', $_cmd));

		$weAcSelector = $yuiSuggest->getHTML();

		return we_html_element::jsElement('
					function ' . $prefix . 'setLinkSelection(value){
						if(value=="intern"){
							setVisible("' . $prefix . 'intern",true);
							setVisible("' . $prefix . 'extern",false);
						} else {
							setVisible("' . $prefix . 'intern",false);
							setVisible("' . $prefix . 'extern",true);
						}

					}
				') . '<div id="' . $prefix . 'LinkSelectionDiv" style="display: ' . (($this->Model->SelectionType == weNavigation::STPYE_CATLINK || $this->Model->SelectionType == weNavigation::STPYE_CATEGORY) ? 'block' : 'none') . ';margin-top: ' . $this->_margin_top . ';">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect(
					$prefix . 'LinkSelection', array(
					'intern' => g_l('navigation', '[intern]'),
					'extern' => g_l('navigation', '[extern]')
					), 1, $this->Model->LinkSelection, false, 'style="width: ' . $this->_width_size . 'px;" onChange="' . $prefix . 'setLinkSelection(this.value);' . $this->topFrame . '.mark();"', 'value'), g_l('navigation', '[linkSelection]')) . '</div>
				<div id="' . $prefix . 'intern" style="display: ' . (($this->Model->LinkSelection == 'intern' && $this->Model->SelectionType != weNavigation::STYPE_URLLINK) ? 'block' : 'none') . ';margin-top: ' . $this->_margin_top . ';">
				' . $weAcSelector . '
				</div>
				<div id="' . $prefix . 'extern" style="display: ' . (($this->Model->LinkSelection == 'extern' || $this->Model->SelectionType == weNavigation::STYPE_URLLINK) ? 'block' : 'none') . ';margin-top: ' . $this->_margin_top . ';">' . we_html_tools::htmlTextInput(
				$prefix . 'Url', 58, $this->Model->Url, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size) . '</div>
				<div style="margin-top: ' . $this->_margin_top . ';">' . we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					$prefix . 'CatParameter', 58, $this->Model->CatParameter, '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[catParameter]')) . '</div>';
	}

	function getHTMLCharsetTable(){
		$value = ((isset($this->Model->Charset) && $this->Model->Charset) ? $this->Model->Charset : $GLOBALS['WE_BACKENDCHARSET']);

		$charsetHandler = new charsetHandler();

		$charsets = $charsetHandler->getCharsetsForTagWizzard();
		asort($charsets);
		reset($charsets);

		$table = new we_html_table(array(
			"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
			), 1, 3);
		$table->setCol(0, 0, null, we_html_tools::htmlTextInput("Charset", 15, $value, '', '', 'text', 120));
		$table->setCol(0, 1, null, we_html_tools::getPixel(2, 10, 0));
		$table->setCol(
			0, 2, null, we_html_tools::htmlSelect(
				"CharsetSelect", $charsets, 1, $value, false, "onblur='document.forms[0].elements[\"Charset\"].value=this.options[this.selectedIndex].value;' onchange='document.forms[0].elements[\"Charset\"].value=this.options[this.selectedIndex].value;document.we_form.submit();'", 'value', ($this->_width_size - 122), "defaultfont", false));

		return $table->getHtml();
	}

	function getLangField($name, $value, $title, $width){
		$input = we_html_tools::htmlTextInput("Attributes[$name]", 15, $value, "", 'onchange=' . $this->topFrame . '.mark(); ', "text", $width - 100);
		$select = '
<select style="width:100px;" class="weSelect" name="' . $name . '_select" size="1" onchange="' . $this->topFrame . '.mark(); this.form.elements[\'Attributes[' . $name . ']\'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;">
	<option value=""></option>
	<option value="en">en</option>
	<option value="de">de</option>
	<option value="es">es</option>
	<option value="fi">fi</option>
	<option value="ru">ru</option>
	<option value="fr">fr</option>
	<option value="nl">nl</option>
	<option value="pl">pl</option>
</select>';
		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	function getRevRelSelect($type, $value, $title){
		$input = we_html_tools::htmlTextInput(
				"Attributes[$type]", 15, $value, "", 'onchange=' . $this->topFrame . '.mark(); ', "text", $this->_width_size - 100);
		$select = '<select name="' . $type . '_sel" class="weSelect" size="1" style="width:100px;" onchange="' . $this->topFrame . '.mark(); this.form.elements[\'Attributes[' . $type . ']\'].value=this.options[this.selectedIndex].text;this.selectedIndex=0;">
	<option></option>
	<option>contents</option>
	<option>chapter</option>
	<option>section</option>
	<option>subsection</option>
	<option>index</option>
	<option>glossary</option>
	<option>appendix</option>
	<option>copyright</option>
	<option>next</option>
	<option>prev</option>
	<option>start</option>
	<option>help</option>
	<option>bookmark</option>
	<option>alternate</option>
	<option>nofollow</option>
</select>';
		return we_html_tools::htmlFormElementTable($input, $title, "left", "defaultfont", $select);
	}

	function getHTMLAttributes(){
		$_parts = array(
			array(
				'headline' => '',
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('navigation', '[linkprops_desc]'), 2, $this->_width_size),
				'space' => $this->_space_size,
				'noline' => 1
			)
		);

		$_title = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[title]', 30, $this->Model->getAttribute('title'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[title]'));

		$_anchor = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[anchor]', 30, $this->Model->getAttribute('anchor'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size) . '<br/>' . we_forms::checkboxWithHidden(
					$this->Model->CurrentOnAnker, 'CurrentOnAnker', g_l('navigation', '[current_on_anker]'), false, "defaultfont", $this->topFrame . '.mark();"'), g_l('navigation', '[anchor]'));

		$_target = we_html_tools::htmlFormElementTable(
				we_html_tools::targetBox('Attributes[target]', 30, ($this->_width_size - 100), '', $this->Model->getAttribute('target'), '' . $this->topFrame . '.mark();', 8, 100), g_l('navigation', '[target]'));

		$_link = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[link_attribute]', 30, $this->Model->getAttribute('link_attribute'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[link_attribute]'));

		$_parts[] = array(
			'headline' => g_l('navigation', '[attributes]'),
			'html' => $_title . $_anchor . $_link . $_target,
			'space' => $this->_space_size,
			'noline' => 1
		);

		$_lang = $this->getLangField(
			'lang', $this->Model->getAttribute('lang'), g_l('navigation', '[link_language]'), $this->_width_size);
		$_hreflang = $this->getLangField(
			'hreflang', $this->Model->getAttribute('hreflang'), g_l('navigation', '[href_language]'), $this->_width_size);

		$_parts[] = array(
			'headline' => g_l('navigation', '[language]'),
			'html' => $_lang . $_hreflang,
			'space' => $this->_space_size,
			'noline' => 1
		);

		$_accesskey = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[accesskey]', 30, $this->Model->getAttribute('accesskey'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[accesskey]'));

		$_tabindex = we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[tabindex]', 30, $this->Model->getAttribute('tabindex'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $this->_width_size), g_l('navigation', '[tabindex]'));

		$_parts[] = array(
			'headline' => g_l('navigation', '[keyboard]'),
			'html' => $_accesskey . $_tabindex,
			'space' => $this->_space_size,
			'noline' => 1
		);

		$_relfield = $this->getRevRelSelect('rel', $this->Model->getAttribute('rel'), 'rel');
		$_revfield = $this->getRevRelSelect('rev', $this->Model->getAttribute('rev'), 'rev');

		$_parts[] = array(
			'headline' => g_l('navigation', '[relation]'),
			'html' => $_relfield . $_revfield,
			'space' => $this->_space_size,
			'noline' => 1
		);

		$_input_width = 70;

		$_popup = new we_html_table(array('cellpadding' => '5', 'cellspacing' => '0'), 4, 4);

		$_popup->setCol(0, 0, array('colspan' => '2'), we_forms::checkboxWithHidden(
				$this->Model->getAttribute('popup_open'), 'Attributes[popup_open]', g_l('navigation', '[popup_open]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(0, 2, array('colspan' => '2'), we_forms::checkboxWithHidden(
				$this->Model->getAttribute('popup_center'), 'Attributes[popup_center]', g_l('navigation', '[popup_center]'), false, "defaultfont", $this->topFrame . '.mark();"'));

		$_popup->setCol(1, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput(
					'Attributes[popup_xposition]', 5, $this->Model->getAttribute('popup_xposition'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_x]')));
		$_popup->setCol(1, 1, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput(
					'Attributes[popup_yposition]', 5, $this->Model->getAttribute('popup_yposition'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_y]')));
		$_popup->setCol(1, 2, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput(
					'Attributes[popup_width]', 5, $this->Model->getAttribute('popup_width'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_width]')));

		$_popup->setCol(1, 3, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput(
					'Attributes[popup_height]', 5, $this->Model->getAttribute('popup_height'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[popup_height]')));

		$_popup->setCol(2, 0, array(), we_forms::checkboxWithHidden($this->Model->getAttribute('popup_status'), 'Attributes[popup_status]', g_l('navigation', '[popup_status]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(2, 1, array(), we_forms::checkboxWithHidden($this->Model->getAttribute('popup_scrollbars'), 'Attributes[popup_scrollbars]', g_l('navigation', '[popup_scrollbars]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(2, 2, array(), we_forms::checkboxWithHidden($this->Model->getAttribute('popup_menubar'), 'Attributes[popup_menubar]', g_l('navigation', '[popup_menubar]'), false, "defaultfont", $this->topFrame . '.mark();"'));

		$_popup->setCol(3, 0, array(), we_forms::checkboxWithHidden($this->Model->getAttribute('popup_resizable'), 'Attributes[popup_resizable]', g_l('navigation', '[popup_resizable]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(3, 1, array(), we_forms::checkboxWithHidden($this->Model->getAttribute('popup_location'), 'Attributes[popup_location]', g_l('navigation', '[popup_location]'), false, "defaultfont", $this->topFrame . '.mark();"'));
		$_popup->setCol(3, 2, array(), we_forms::checkboxWithHidden($this->Model->getAttribute('popup_toolbar'), 'Attributes[popup_toolbar]', g_l('navigation', '[popup_toolbar]'), false, "defaultfont", $this->topFrame . '.mark();"'));

		$_parts[] = array(
			'headline' => g_l('navigation', '[popup]'),
			'html' => $_popup->getHTML(),
			'space' => $this->_space_size,
			'noline' => 1
		);

		$wepos = weGetCookieVariable("but_weNaviAttrib");
		return we_multiIconBox::getHTML(
				'weNaviAttrib', '100%', $_parts, 30, '', 0, g_l('navigation', '[more_attributes]'), g_l('navigation', '[less_attributes]'), ($wepos == 'down'));
	}

	function getHTMLImageAttributes(){
		$_input_width = 70;
		$_img_props = new we_html_table(array(
			'cellpadding' => '5', 'cellspacing' => '0', 'border' => '0'
			), 4, 5);

		$_img_props->setCol(
			0, 0, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_width]', 5, $this->Model->getAttribute('icon_width'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_width]')));
		$_img_props->setCol(
			0, 1, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_height]', 5, $this->Model->getAttribute('icon_height'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_height]')));
		$_img_props->setCol(
			0, 2, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_border]', 5, $this->Model->getAttribute('icon_border'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_border]')));
		$_img_props->setCol(
			0, 3, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_hspace]', 5, $this->Model->getAttribute('icon_hspace'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_hspace]')));
		$_img_props->setCol(
			0, 4, array(), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_vspace]', 5, $this->Model->getAttribute('icon_vspace'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', $_input_width), g_l('navigation', '[icon_vspace]')));
		$_img_props->setCol(
			1, 0, array(
			'colspan' => '5'
			), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlSelect(
					'Attributes[icon_align]', array(
					'' => 'Default',
					'top' => 'Top',
					'middle' => 'Middle',
					'bottom' => 'Bottom',
					'left' => 'left',
					'right' => 'Right',
					'texttop' => 'Text Top',
					'absmiddle' => 'Abd Middle',
					'baseline' => 'Baseline',
					'absbottom' => 'Abs Bottom'
					), 1, $this->Model->getAttribute('icon_align'), false, 'style="width: ' . ($this->_width_size - 50) . 'px;"'), g_l('navigation', '[icon_align]')));
		$_img_props->setCol(
			2, 0, array(
			'colspan' => '5'
			), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_alt]', 5, $this->Model->getAttribute('icon_alt'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 50)), g_l('navigation', '[icon_alt]')));
		$_img_props->setCol(
			3, 0, array(
			'colspan' => '5'
			), we_html_tools::htmlFormElementTable(
				we_html_tools::htmlTextInput(
					'Attributes[icon_title]', 5, $this->Model->getAttribute('icon_title'), '', 'onchange="' . $this->topFrame . '.mark();"', 'text', ($this->_width_size - 50)), g_l('navigation', '[icon_title]')));

		return $_img_props->getHTML();
	}

	function getHTMLTab3(){
		$_space_size = 50;

		$_filter = new weNavigationCustomerFilter();
		$_filter->initByNavModel($this->Model);

		$_view = new weNavigationCustomerFilterView($_filter, $this->topFrame . '.mark()', $this->_width_size);

		return array(
			array(
				'headline' => '',
				'html' => $_view->getFilterHTML(
					$this->Model->IsFolder == 0 && $this->Model->Selection == weNavigation::SELECTION_DYNAMIC),
				'space' => $_space_size,
				'noline' => 1
		));
	}

	function getHTMLEditorFooter(){

		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array(
						"bgcolor" => "#F0EFF0"
						), ""));
		}

		$table1 = new we_html_table(array(
			"border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "3000"
			), 1, 1);
		$table1->setCol(0, 0, array(
			"nowrap" => null, "valign" => "top"
			), we_html_tools::getPixel(1600, 10));

		$table2 = new we_html_table(array(
			"border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "400"
			), 1, 2);
		$table2->setColContent(
			0, 0, we_button::create_button_table(
				array(
				we_button::create_button(
					"save", "javascript:we_save();", true, 100, 22, '', '', (!we_hasPerm('EDIT_NAVIGATION')))
				), 10, array(
				'style' => 'margin-left: 15px'
		)));

		$table2->setColContent(
			0, 1, we_forms::checkbox(
				"makeNewDoc", false, "makeNewDoc", ($this->View->Model->IsFolder ? g_l('global', "[we_new_folder_after_save]") : g_l('global', "[we_new_entry_after_save]")), false, "defaultfont", ""));

		return $this->getHTMLDocument(
				we_html_element::jsScript(JS_DIR . "attachKeyListener.js") .
				we_html_element::jsElement('
					function we_save() {
						' . $this->topFrame . '.makeNewDoc = document.we_form.makeNewDoc.checked;
						' . $this->topFrame . '.we_cmd("tool_' . $this->toolName . '_save");
					}
					') . we_html_element::htmlBody(
					array(
					"bgcolor" => "white",
					"background" => IMAGE_DIR . "edit/editfooterback.gif",
					"marginwidth" => "0",
					"marginheight" => "0",
					"leftmargin" => "0",
					"topmargin" => "0",
					"onLoad" => "document.we_form.makeNewDoc.checked=" . $this->topFrame . ".makeNewDoc;"
					), we_html_element::htmlForm(array(), $table1->getHtml() . $table2->getHtml())));
	}

}
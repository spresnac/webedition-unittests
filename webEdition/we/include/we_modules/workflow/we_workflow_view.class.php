<?php

/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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
class we_workflow_view extends we_workflow_base{

	// workflow array; format workflow[workflowID]=workflow_name
	var $workflows = array();
	//default workflow
	var $workflowDef;
	//default document
	var $documentDef;
	//what is current display 0-workflow(default);1-document;
	var $show = 0;
	//wat page is currentlly displed 0-properties(default);1-overview;
	var $page = 0;
	var $hiddens = array();

	function __construct(){
		parent::__construct();
		$this->workflowDef = new we_workflow_workflow();
		$this->documentDef = new we_workflow_document();
		array_push($this->hiddens, 'ID', 'Type', 'Status', 'Folders', 'ObjectFileFolders', 'Categories', 'ObjCategories', 'DocType', 'Objects');
		//$this->hiddens[]='EmailPath';
		//$this->hiddens[]='LastStepAutoPublish';
	}

	function getHiddens(){
		return $this->htmlHidden('home', '0') .
			$this->htmlHidden('wcmd', 'new_workflow') .
			$this->htmlHidden('wid', $this->workflowDef->ID) .
			$this->htmlHidden('pnt', 'edit') .
			$this->htmlHidden('wname', $this->uid) .
			$this->htmlHidden('page', $this->page);
	}

	function getHiddensFormPropertyPage(){
		return $this->htmlHidden($this->uid . '_Text', $this->workflowDef->Text) .
			$this->htmlHidden($this->uid . '_Type', $this->workflowDef->Type) .
			$this->htmlHidden($this->uid . '_FolderPath', $this->workflowDef->FolderPath) .
			$this->htmlHidden($this->uid . '_Folders', $this->workflowDef->Folders) .
			$this->htmlHidden($this->uid . '_ObjectFileFolders', $this->workflowDef->ObjectFileFolders) .
			$this->htmlHidden($this->uid . '_DocType', $this->workflowDef->DocType) .
			$this->htmlHidden($this->uid . '_Categories', $this->workflowDef->Categories) .
			$this->htmlHidden($this->uid . '_ObjCategories', $this->workflowDef->ObjCategories) .
			$this->htmlHidden($this->uid . '_Objects', $this->workflowDef->Objects) .
			$this->htmlHidden($this->uid . '_EmailPath', $this->workflowDef->EmailPath) .
			$this->htmlHidden($this->uid . '_LastStepAutoPublish', $this->workflowDef->LastStepAutoPublish);
	}

	function getHiddensFormOverviewPage(){
		$out = $this->htmlHidden('wcat', '0') .
			$this->htmlHidden('wocat', '0') .
			$this->htmlHidden('wfolder', '0') .
			$this->htmlHidden('woffolder', '0') .
			$this->htmlHidden('wobject', '0');

		$counter = 0;
		$counter1 = 0;
		foreach($this->workflowDef->steps as $sk => $sv){
			$out.=$this->htmlHidden($this->uid . '_step' . $counter . '_sid', $sv->ID) .
				$this->htmlHidden($this->uid . '_step' . $counter . '_and', $sv->stepCondition) .
				$this->htmlHidden($this->uid . '_step' . $counter . '_Worktime', $sv->Worktime) .
				$this->htmlHidden($this->uid . '_step' . $counter . '_timeAction', $sv->timeAction);
			$counter1 = 0;
			foreach($sv->tasks as $tk => $tv){
				$out.=$this->htmlHidden($this->uid . '_task' . $counter . $counter1 . '_tid', $tv->ID) .
					$this->htmlHidden($this->uid . '_task_' . $counter . '_' . $counter1 . '_userid', $tv->userID) .
					$this->htmlHidden($this->uid . '_task_' . $counter . '_' . $counter1 . '_Edit', ($tv->Edit ? 1 : 0)) .
					$this->htmlHidden($this->uid . '_task_' . $counter . '_' . $counter1 . '_Mail', ($tv->Mail ? 1 : 0));
				++$counter1;
			}
			++$counter;
		}
		$out.=$this->htmlHidden('wsteps', $counter) .
			$this->htmlHidden('wtasks', $counter1);

		return $out;
	}

	function workflowHiddens(){
		$out = '';
		foreach($this->hiddens as $key => $val){
			$out.=$this->htmlHidden($this->uid . '_' . $val, (in_array($val, $this->workflowDef->persistents) ? $this->workflowDef->$val : $this->$val));
		}
		return $out;
	}

	function getProperties(){
		if(isset($_REQUEST['home']) && $_REQUEST['home']){
			$GLOBALS['we_print_not_htmltop'] = true;
			$GLOBALS['we_head_insert'] = $this->getPropertyJS(); //this this is bullshit since getPropertyJS writes directly to screen
			$GLOBALS['we_body_insert'] = '<form name="we_form">';
			$GLOBALS['we_body_insert'] .= $this->getHiddens() . '</form>';
			$GLOBALS['mod'] = 'workflow';
			ob_start();
			include(WE_MODULES_PATH . 'home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
		} else{
			//$out=$this->getPropertyJS(); this is bullshit since getPropertyJS writes directly to screen
			$this->getPropertyJS();

			$out = '</head><body class="weEditorBody" onload="loaded=1;" onunload="doUnload()"><form name="we_form" onsubmit="return false">' . "\r\n";
			$out.=$this->getHiddens();
			if($this->show){
				$out.=$this->getDocumentInfo();
			} else{
				$out.=$this->workflowHiddens();
				$parts = array();

				if($this->page == 0){

					$_space = 143;

					$out .= $this->getHiddensFormOverviewPage();

					$parts[] = $this->getWorkflowHeaderMultiboxParts($_space);
					$parts[] = array(
						'headline' => g_l('modules_workflow', '[type]'),
						'space' => $_space - 25,
						'html' => $this->getWorkflowTypeHTML());
					$myout = '<br/>';
					$myout .= we_forms::checkboxWithHidden($this->workflowDef->EmailPath, $this->uid . '_EmailPath', g_l('modules_workflow', '[EmailPath]'), false, 'defaultfont', '', false);

					$myout .= we_forms::checkboxWithHidden($this->workflowDef->LastStepAutoPublish, $this->uid . '_LastStepAutoPublish', g_l('modules_workflow', '[LastStepAutoPublish]'), false, 'defaultfont', '', false);
					array_push($parts, array(
						'headline' => g_l('modules_workflow', '[specials]'),
						'space' => $_space - 25,
						'html' => $myout));
					//	Workflow-Type
					$out .= we_multiIconBox::getHTML('workflowProperties', '100%', $parts, 30);
				} else{
					$out .= $this->getHiddensFormPropertyPage();
					$out .= we_html_tools::htmlDialogLayout($this->getStepsHTML(), '');
				}
			}
			$out.='</form></body></html>';
		}
		return $out;
	}

	/**
	 * @return array		can be used by class we_multiIconBox.class.inc.php as $content-array
	 * @desc Enter description here...
	 */
	function getWorkflowHeaderMultiboxParts($space){
		return array(
			'headline' => g_l('modules_workflow', '[name]'),
			'html' => we_html_tools::htmlTextInput($this->uid . '_Text', 37, stripslashes($this->workflowDef->Text), '', ' id="yuiAcInputPathName" onchange="top.content.setHot();" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()"', "text", 498),
			'space' => $space
		);
	}

	function getWorkflowSelectHTML(){
		$vals = $this->workflowDef->getAllWorkflowsInfo();
		return we_html_tools::htmlSelect('wid', $vals, 4, $this->workflowDef->ID, false, "onclick='we_cmd(\"edit_workflow\")'", "value", 200);
	}

	function getWorkflowTypeHTML(){
		$vals = array(
			we_html_tools::getPixel(2, 10),
			$this->getFoldersHTML(),
		);
		$out = $this->getTypeTableHTML(we_forms::radiobutton(we_workflow_workflow::FOLDER, ($this->workflowDef->Type == we_workflow_workflow::FOLDER ? 1 : 0), $this->uid . '_Type', g_l('modules_workflow', '[type_dir]'), true, 'defaultfont', 'onclick=top.content.setHot();'), $vals, 25);
		$vals = array(
			we_html_tools::getPixel(2, 10),
			$this->getDocTypeHTML(),
			we_html_tools::getPixel(2, 10),
			$this->getCategoryHTML(),
		);
		$out .= $this->getTypeTableHTML(we_forms::radiobutton(we_workflow_workflow::DOCTYPE_CATEGORY, ($this->workflowDef->Type == we_workflow_workflow::DOCTYPE_CATEGORY ? 1 : 0), $this->uid . '_Type', g_l('modules_workflow', '[type_doctype]'), true, 'defaultfont', 'onclick=top.content.setHot();'), $vals, 25);

		if(defined('OBJECT_TABLE')){
			$vals = array(
				we_html_tools::getPixel(2, 10),
				$this->getObjectHTML(),
				we_html_tools::getPixel(2, 10),
				$this->getObjCategoryHTML(),
				we_html_tools::getPixel(2, 10),
				$this->getObjectFileFoldersHTML(),
			);
			$out .= $this->getTypeTableHTML(we_forms::radiobutton(we_workflow_workflow::OBJECT, ($this->workflowDef->Type == we_workflow_workflow::OBJECT ? 1 : 0), $this->uid . '_Type', g_l('modules_workflow', '[type_object]'), true, 'defaultfont', 'onclick=top.content.setHot();'), $vals, 25);
		}

		return $out;
	}

	function getFoldersHTML(){
		$delallbut = we_button::create_button('delete_all', "javascript:top.content.setHot();we_cmd('del_all_folders');");
		//javascript:top.content.setHot();we_cmd('openDirselector','','".FILE_TABLE."','','','fillIDs();opener.we_cmd(\\'add_folder\\',top.allIDs);','','','',true)
		$wecmdenc3 = we_cmd_enc("fillIDs();opener.we_cmd('add_folder',top.allIDs);");
		$addbut = we_button::create_button("add", "javascript:top.content.setHot();we_cmd('openDirselector','','" . FILE_TABLE . "','','','" . $wecmdenc3 . "','','','',true)");


		$dirs = new MultiDirChooser(495, $this->workflowDef->Folders, 'del_folder', we_button::create_button_table(array($delallbut, $addbut)), '', 'Icon,Path', FILE_TABLE, 'defaultfont', '', "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($dirs->get(), g_l('modules_workflow', '[dirs]'));
	}

	function getCategoryHTML(){
		$delallbut = we_button::create_button('delete_all', "javascript:top.content.setHot();we_cmd('del_all_cats')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));
		$addbut = we_button::create_button('add', "javascript:top.content.setHot();we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_cat\\',top.allIDs);')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));

		$cats = new MultiDirChooser(495, $this->workflowDef->Categories, 'del_cat', we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CATEGORY_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($cats->get(), g_l('modules_workflow', '[categories]'));
	}

	function getObjCategoryHTML(){
		$delallbut = we_button::create_button('delete_all', "javascript:top.content.setHot();we_cmd('del_all_objcats')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));
		$addbut = we_button::create_button('add', "javascript:top.content.setHot();we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_objcat\\',top.allIDs);')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));

		$cats = new MultiDirChooser(495, $this->workflowDef->ObjCategories, "del_objcat", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CATEGORY_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($cats->get(), g_l('modules_workflow', '[categories]'));
	}

	function getObjectHTML(){
		$delallbut = we_button::create_button('delete_all', "javascript:top.content.setHot();we_cmd('del_all_objects')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));
		//javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$idname'].value,'$table','document.forms[\\'we_form\\'].elements[\\'$idname\\'].value','document.forms[\\'we_form\\'].elements[\\'$textname\\'].value','top.opener._EditorFrame.setEditorIsHot(true);','".session_id()."','$rootDir','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")
		$wecmdenc3 = we_cmd_enc("opener.we_cmd('add_object',top.currentID);");
		$addbut = we_button::create_button('add', "javascript:top.content.setHot();we_cmd('openObjselector','','" . OBJECT_TABLE . "','','','" . $wecmdenc3 . "')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));

		$cats = new MultiDirChooser(495, $this->workflowDef->Objects, "del_object", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", OBJECT_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($cats->get(), g_l('modules_workflow', '[classes]'));
	}

	function getObjectFileFoldersHTML(){
		$delallbut = we_button::create_button('delete_all', "javascript:top.content.setHot();we_cmd('del_all_object_file_folders');");
		//avascript:top.content.setHot();we_cmd('openDirselector','','".OBJECT_FILES_TABLE."','','','fillIDs();opener.we_cmd(\\'add_object_file_folder\\',top.allIDs);','','','',true)
		$wecmdenc3 = we_cmd_enc("fillIDs();opener.we_cmd('add_object_file_folder',top.allIDs);");
		$addbut = we_button::create_button('add', "javascript:top.content.setHot();we_cmd('openDirselector','','" . OBJECT_FILES_TABLE . "','','','" . $wecmdenc3 . "','','','',true)");

		$dirs = new MultiDirChooser(495, $this->workflowDef->ObjectFileFolders, "del_object_file_folder", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", OBJECT_FILES_TABLE, "defaultfont", "", "top.content.setHot();");

		return we_html_tools::htmlFormElementTable($dirs->get(), g_l('modules_workflow', '[dirs]'));
	}

	function getStatusHTML(){
		return we_forms::checkboxWithHidden('1', 'status_workflow', g_l('modules_workflow', '[active]'), false, 'defaultfont', 'top.content.setHot();');
	}

	function getStepsHTML(){
		$headline = array();
		$content = array();

		$ids = '';

		$headline = array(
			array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[step]') . '</div>'),
			array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[and_or]') . '</div>'),
			array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[worktime]') . '</div>')
		);

		$counter = 0;
		$counter1 = 0;

		$yuiSuggest = & weSuggest::getInstance();

		/*		 * *** BROWSER DEPENDENCIES **** */
		switch(we_base_browserDetect::inst()->getBrowser()){
			case we_base_browserDetect::IE:
				$_spacer_1_height = 13;
				$_spacer_2_height = 5;
				break;
			default:
				$_spacer_1_height = 7;
				$_spacer_2_height = 5;
		}

		/*		 * *** WORKFLOWSTEPS **** */
		foreach($this->workflowDef->steps as $sk => $sv){
			$ids.=$this->htmlHidden($this->uid . '_step' . $counter . '_sid', $sv->ID);
			$content[$counter] = array(
				array(
					'dat' => $counter + 1,
					'height' => '',
					'align' => 'center',
				),
				array(
					'dat' => '<table><tr valign="top"><td>' . we_forms::radiobutton("1", $sv->stepCondition ? 1 : 0, $this->uid . "_step" . $counter . "_and", "", false, "defaultfont", "top.content.setHot();") . '</td><td>' . we_html_tools::getPixel(5, 5) . '</td><td>' . we_forms::radiobutton("0", $sv->stepCondition ? 0 : 1, $this->uid . "_step" . $counter . "_and", "", false, "defaultfont", "top.content.setHot();") . '</td></tr></table>',
					'height' => '',
					'align' => '',
				),
				array(
					'dat' => '<table cellpadding="0" cellspacing="0"><tr><td>' . we_html_tools::getPixel(5, 7) . '</td></tr><tr valign="middle"><td class="middlefont">' . we_html_tools::htmlTextInput($this->uid . "_step" . $counter . "_Worktime", "15", $sv->Worktime, "", 'onChange="top.content.setHot();"') . '</td></tr>' .
					'<tr valign="middle"><td>' . we_html_tools::getPixel(5, $_spacer_1_height) . '</td><tr>' .
					'<tr valign="top">' .
					'<td class="middlefont">' . we_forms::checkboxWithHidden($sv->timeAction == 1, $this->uid . "_step" . $counter . "_timeAction", g_l('modules_workflow', '[go_next]'), false, "middlefont", "top.content.setHot();") . '</td>' .
					'</tr></table>',
					'height' => '',
					'align' => '',
				)
			);


			$counter1 = 0;
			foreach($sv->tasks as $tk => $tv){
				$ids.=$this->htmlHidden($this->uid . '_task' . $counter . '_' . $counter1 . '_tid', $tv->ID);
				$headline[$counter1 + 3] = array('dat' => g_l('modules_workflow', '[user]') . (string) ($counter1 + 1));

				$foo = f('SELECT Path FROM ' . USER_TABLE . ' WHERE ID=' . intval($tv->userID), 'Path', $this->db);
				//javascript:top.content.setHot();we_cmd('browse_users','document.we_form.".$this->uid."_task_".$counter."_".$counter1."_userid.value','document.we_form.".$this->uid."_task_".$counter."_".$counter1."_usertext.value','',document.we_form.".$this->uid."_task_".$counter."_".$counter1."_userid.value);
				$wecmdenc1 = we_cmd_enc("document.we_form." . $this->uid . "_task_" . $counter . "_" . $counter1 . "_userid.value");
				$wecmdenc2 = we_cmd_enc("document.we_form." . $this->uid . "_task_" . $counter . "_" . $counter1 . "_usertext.value");
				$wecmdenc5 = we_cmd_enc("document.we_form." . $this->uid . "_task_" . $counter . "_" . $counter1 . "_userid.value");
				$button = we_button::create_button("select", "javascript:top.content.setHot();we_cmd('browse_users','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','" . $wecmdenc5 . "');");

				$yuiSuggest->setAcId('User_' . $counter . '_' . $counter1);
				$yuiSuggest->setContentType('0,1');
				$yuiSuggest->setInput($this->uid . '_task_' . $counter . '_' . $counter1 . '_usertext', $foo, array('onChange' => 'top.content.setHot();'));
				$yuiSuggest->setMaxResults(10);
				$yuiSuggest->setMayBeEmpty(false);
				$yuiSuggest->setResult($this->uid . '_task_' . $counter . '_' . $counter1 . '_userid', $tv->userID);
				$yuiSuggest->setSelector('Docselector');
				$yuiSuggest->setTable(USER_TABLE);
				$yuiSuggest->setWidth(200);
				$yuiSuggest->setContainerWidth(305);
				$yuiSuggest->setSelectButton($button, 6);

				$content[$counter][$counter1 + 3] = array(
					'dat' => '<table cellpadding="0" cellspacing="0">
						<tr valign="middle"><td colspan="4">' . we_html_tools::getPixel(5, $_spacer_2_height) . '</td><tr>
						<tr valign="middle"><td>' . $yuiSuggest->getHTML() . '</td>
						</tr></table>
						<table cellpadding="0" cellspacing="0">
						<tr valign="middle"><td colspan="3">' . we_html_tools::getPixel(5, 0) . '</td><tr>
						<tr valign="top">
						<td class="middlefont" align="right">' . we_forms::checkboxWithHidden($tv->Mail, $this->uid . "_task_" . $counter . "_" . $counter1 . "_Mail", g_l('modules_workflow', '[send_mail]'), false, "middlefont", "top.content.setHot();") . '</td>
						<td>' . we_html_tools::getPixel(20, 1) . '</td>
						<td class="middlefont">' . we_forms::checkboxWithHidden($tv->Edit, $this->uid . "_task_" . $counter . "_" . $counter1 . "_Edit", g_l('modules_workflow', '[edit]'), false, "middlefont", "top.content.setHot();") . '</td>
						</tr></table>',
					'height' => '',
					'align' => ''
				);
				$counter1++;
			}
			++$counter;
		}
		return $ids .
			we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/dom-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/datasource-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/animation-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/json-min.js') .
			we_html_element::jsScript(JS_DIR . 'libs/yui/autocomplete-min.js') .
			$yuiSuggest->getYuiFiles() .
			'	<table style="margin-right:30px;">
				<tr valign="top">
					<td>' . we_html_tools::htmlDialogBorder3(400, 300, $content, $headline) . '</td>
					<td><table cellpadding="0" cellspacing="0">
						<tr><td>' . we_html_tools::getPixel(5, 3) . '</td></tr>
						<tr><td>' . we_button::create_button_table(array(we_button::create_button("image:btn_function_plus", "javascript:top.content.setHot();addTask()", true, 30), we_button::create_button("image:btn_function_trash", "javascript:top.content.setHot();delTask()", true, 30))) . '</td>
						</tr>
						</table></td>
				</tr>
				<tr valign="top">
					<td colspan="2" nowrap>' . we_button::create_button_table(array(we_button::create_button("image:btn_function_plus", "javascript:top.content.setHot();addStep()", true, 30), we_button::create_button("image:btn_function_trash", "javascript:top.content.setHot();delStep()", true, 30))) . '</td></tr>
				</table>' .
			$yuiSuggest->getYuiCode() .
			$this->htmlHidden("wsteps", $counter) .
			$this->htmlHidden("wtasks", $counter1);
	}

	function getTypeTableHTML($head, $values, $ident = 0, $textalign = "left", $textclass = "defaultfont"){
		$out = '<table cellpadding="0" cellspacing="0" border="0">' . ($head ? '<tr><td class="' . trim($textclass) . '" align="' . trim($textalign) . '" colspan="2">' . $head . '</td></tr>' : '');
		foreach($values as $key => $val){
			$out.='<tr><td>' . we_html_tools::getPixel($ident, 5) . '</td><td class="' . trim($textclass) . '">' . $val . '</td></tr>';
		}
		$out.='</table>';
		return $out;
	}

	function getBoxHTML($w, $h, $content, $headline = "", $width = 120){
		$headline = str_replace(' ', '&nbsp;', $headline);
		if($headline){
			return '<table cellpadding="0" cellspacing="0" border="0">
			<tr>' . we_html_tools::getPixel(24, 15) . '</td>
				<td>' . we_html_tools::getPixel($width, 15) . '</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td valign="top" class="defaultgray">' . $headline . '</td>
				<td>' . $content . '</td>
			</tr>
			<tr>
				<td>' . we_html_tools::getPixel(24, 15) . '</td>
				<td>' . we_html_tools::getPixel($width, 15) . '</td>
				<td></td>
			</tr></table>';
		} else{
			return '<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td>' . we_html_tools::getPixel(24, 15) . '</td><td></td>
			</tr>
			<tr>
				<td></td><td>' . $content . '</td>
			</tr>
			<tr><td>' . we_html_tools::getPixel(24, 15) . '</td><td></td>
			</tr></table>';
		}
	}

	function getDocTypeHTML($width = 498){

		$pop = '';

		$vals = array();
		$q = getDoctypeQuery($this->db);
		$this->db->query('SELECT ID,DocType FROM ' . DOC_TYPES_TABLE . ' ' . $q);
		while($this->db->next_record()) {
			$v = $this->db->f('ID');
			$t = $this->db->f('DocType');
			$vals[$v] = $t;
		}
		$pop = we_html_tools::htmlSelect($this->uid . '_MYDocType[]', $vals, 6, $this->workflowDef->DocType, true, 'onChange="top.content.setHot();"', "value", $width, "defaultfont");

		return we_html_tools::htmlFormElementTable($pop, g_l('modules_workflow', '[doctype]'));
	}

	function htmlHidden($name, $value = ''){
		return '<input type="hidden" name="' . trim($name) . '" value="' . oldHtmlspecialchars($value) . '" />';
	}

	/* creates the DirectoryChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = '', $rootDirID = 0, $table = FILE_TABLE, $Pathname = 'ParentPath', $Pathvalue = '', $IDName = 'ParentID', $IDValue = '', $cmd = ''){
		$table = FILE_TABLE;

		//javascript:we_cmd('openDirselector',document.we_form.elements['$IDName'].value,'$table','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','".session_id()."','$rootDirID')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));

		$button = we_button::create_button('select', "javascript:we_cmd('openDirselector',document.we_form.elements['$IDName'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','$rootDirID')");
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'onChange="top.content.setHot();" readonly', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($IDName, $IDValue), we_html_tools::getPixel(20, 4), $button);
	}

	function getJSTopCode(){
		$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
		$title = '';
		foreach($GLOBALS['_we_available_modules'] as $modData){
			if($modData['name'] == $mod){
				$title = 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'];
				break;
			}
		}
		?>
		<script type="text/javascript"><!--

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}

			parent.document.title = "<?php print $title; ?>";

			function we_cmd(){
				var args = "";
				var url = "<?php print WEBEDITION_DIR; ?>we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				if(hot == "1" && arguments[0]!="save_workflow") {
					var hotConfirmMsg = confirm("<?php print g_l('modules_workflow', '[save_changed_workflow]') ?>");
					if(hotConfirmMsg==true) {
						arguments[0] = "save_workflow";
						top.content.usetHot();
					} else {
						top.content.setHot();
					}
				}
				switch (arguments[0]){
					case "exit_workflow":
						if(hot != "1") {
							eval('top.opener.top.we_cmd(\'exit_modules\')');
						}
						break;
					case "new_workflow":
						top.content.resize.right.editor.edbody.document.we_form.wcmd.value=arguments[0];
						top.content.resize.right.editor.edbody.document.we_form.wid.value=arguments[1];
						top.content.resize.right.editor.edbody.submitForm();
						break;
					case "delete_workflow":
		<?php
		if(!we_hasPerm("DELETE_WORKFLOW")){
			print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			?>
								if(top.content.resize.right.editor.edbody.loaded){
									if(!confirm("<?php print g_l('modules_workflow', '[delete_question]') ?>")) return;
								}
								else { <?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR); ?> }

								top.content.resize.right.editor.edbody.document.we_form.wcmd.value=arguments[0];
								top.content.resize.right.editor.edbody.submitForm();
		<?php } ?>
						break;
					case "save_workflow":
		<?php
		if(!we_hasPerm("EDIT_WORKFLOW") && !we_hasPerm("NEW_WORKFLOW")){
			print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			?>
								if(top.content.resize.right.editor.edbody.loaded){
									top.content.resize.right.editor.edbody.setStatus(top.content.resize.right.editor.edfooter.document.we_form.status_workflow.value);
									chk=top.content.resize.right.editor.edbody.checkData();
									if(!chk) return;
									num=top.content.resize.right.editor.edbody.getNumOfDocs();
									if(num>0)
										if(!confirm("<?php print g_l('modules_workflow', '[save_question]') ?>")) return;
								}
								else { <?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR); ?> }
								top.content.resize.right.editor.edbody.document.we_form.wcmd.value=arguments[0];
								top.content.resize.right.editor.edbody.submitForm();
								top.content.usetHot();
		<?php } ?>
						break;
					case "edit_workflow":
					case "show_document":
						top.content.resize.right.editor.edbody.document.we_form.wcmd.value=arguments[0];
						top.content.resize.right.editor.edbody.document.we_form.wid.value=arguments[1];
						top.content.resize.right.editor.edbody.submitForm();
						break;
					case "reload_workflow":
						top.content.resize.left.tree.location.reload(true);
						break;
					case "empty_log":
						new jsWindow("<?php print WE_WORKFLOW_MODULE_DIR ?>edit_workflow_frameset.php?pnt=qlog","log_question",-1,-1,360,230,true,false,true);
						break;
					default:
						for(var i = 0; i < arguments.length; i++){
							args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
						}
						eval('top.opener.top.we_cmd('+args+')');
					}
				}
				//-->
		</script>
		<?php
	}

	function getCmdJS(){
		?>
		<script type="text/javascript"><!--
				function submitForm(){
					var f = self.document.we_form;
					f.target = "cmd";
					f.method = "post";
					f.submit();
				}
				//-->
		</script>
		<?php
	}

	function getPropertyJS(){
		echo we_html_element::jsScript(JS_DIR . 'windows.js');
		?>
		<script type="text/javascript"><!--
				var loaded;

				function doUnload() {
					if (!!jsWindow_count) {
						for (i = 0; i < jsWindow_count; i++) {
							eval("jsWindow" + i + "Object.close()");
						}
					}
				}

				function we_cmd(){
					var args = "";
					var url = "<?php print WEBEDITION_DIR; ?>we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
					switch (arguments[0]){
						case "browse_users":
							new jsWindow(url,"browse_users",-1,-1,500,300,true,false,true);
							break;
						case "openDirselector":
							new jsWindow(url,"we_fileselector",-1,-1,<?php echo WINDOW_DIRSELECTOR_WIDTH . ',' . WINDOW_DIRSELECTOR_HEIGHT; ?>,true,true,true,true);
							break;
						case "openCatselector":
							new jsWindow(url,"we_catselector",-1,-1,<?php echo WINDOW_CATSELECTOR_WIDTH . ',' . WINDOW_CATSELECTOR_HEIGHT; ?>,true,true,true,true);
							break;
						case "openObjselector":
							url = "<?php echo WEBEDITION_DIR; ?>we_cmd.php?we_cmd[0]=openDocselector&we_cmd[8]=object&we_cmd[1]=&we_cmd[2]=<?php print (defined("OBJECT_TABLE") ? OBJECT_TABLE : ""); ?>&we_cmd[5]="+arguments[5]+"&we_cmd[9]=1";
							new jsWindow(url,"we_objectselector",-1,-1,<?php echo WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT; ?>,true,true,true);
							break;
						case "add_cat":
						case "del_cat":
						case "del_all_cats":
							document.we_form.wcmd.value=arguments[0];
							document.we_form.wcat.value=arguments[1];
							submitForm();
							break;
						case "add_objcat":
						case "del_objcat":
						case "del_all_objcats":
							document.we_form.wcmd.value=arguments[0];
							document.we_form.wocat.value=arguments[1];
							submitForm();
							break;
						case "add_folder":
						case "del_folder":
						case "del_all_folders":
							document.we_form.wcmd.value=arguments[0];
							document.we_form.wfolder.value=arguments[1];
							submitForm();
							break;
						case "add_object_file_folder":
						case "del_object_file_folder":
						case "del_all_object_file_folders":
							document.we_form.wcmd.value=arguments[0];
							document.we_form.woffolder.value=arguments[1];
							submitForm();
							break;
						case "add_object":
						case "del_object":
						case "del_all_objects":
							document.we_form.wcmd.value=arguments[0];
							document.we_form.wobject.value=arguments[1];
							submitForm();
							break;
						case "switchPage":
							document.we_form.wcmd.value=arguments[0];
							document.we_form.page.value=arguments[1];
							submitForm();
							break;
						default:
							for(var i = 0; i < arguments.length; i++){
								args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
							}
							eval('top.content.we_cmd('+args+')');
						}
					}


					function submitForm(){
						var f = self.document.we_form;
						f.target = "edbody";
						f.method = "post";
						f.submit();
					}
		<?php if(!$this->show){ ?>


						function clickCheck(a){
							if(a.checked) a.value=1;
							else a.value=0;
						}

						function addStep(){
							document.we_form.wsteps.value++;
							document.we_form.wcmd.value="reload_table";
							submitForm();

						}

						function addTask(){
							document.we_form.wtasks.value++;
							document.we_form.wcmd.value="reload_table";
							submitForm();

						}

						function delStep(){
							if(document.we_form.wsteps.value>1){
								document.we_form.wsteps.value--;
								document.we_form.wcmd.value="reload_table";
								submitForm();
							}
							else{
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[del_last_step]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
								}
							}

							function setStatus(val){
								document.we_form.<?php print $this->uid; ?>_Status.value=val;

							}

							function getStatusContol(){
								return document.we_form.<?php print $this->uid; ?>_Status.value;
							}

							function delTask(){
								if(document.we_form.wtasks.value>1){
									document.we_form.wtasks.value--;
									document.we_form.wcmd.value="reload_table";
									submitForm();
								}
								else{
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[del_last_task]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
								}
							}

							function getNumOfDocs(){
								return <?php
			$this->workflowDef->loadDocuments();
			print count($this->workflowDef->documents)
			?>;
							}

							function sprintf(){
								if (!arguments || arguments.length < 1) return;

								var argum = arguments[0];
								var regex = /([^%]*)%(%|d|s)(.*)/;
								var arr = new Array();
								var iterator = 0;
								var matches = 0;

								while (arr=regex.exec(argum)){
									var left = arr[1];
									var type = arr[2];
									var right = arr[3];

									matches++;
									iterator++;

									var replace = arguments[iterator];

									if (type=='d') replace = parseInt(param) ? parseInt(param) : 0;
									else if (type=='s') replace = arguments[iterator];
									argum = left + replace + right;
								}
								return argum;
							}


							function checkData(){
								var nsteps=document.we_form.wsteps;
								var ntasks=document.we_form.wtasks;
								ret=false;
								if(document.we_form.<?php print $this->uid ?>_Text.value=="") ret=true;
								if(ret){
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
									return false;
								}

								ret=false;
								if(document.we_form.<?php print $this->uid ?>_Folders.value=="" && document.we_form.<?php print $this->uid ?>_Type.value==1) ret=true;
								if(ret){
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[folders_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
									return false;
								}

								ret=false;
								if(document.we_form.<?php print $this->uid ?>_ObjectFileFolders.value=="" && document.we_form.<?php print $this->uid ?>_Type.value==2) ret=true;
								if(ret){
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[folders_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
									return false;
								}

								ret=false;
								if((document.we_form.<?php print $this->uid ?>_DocType.value==0 && document.we_form.<?php print $this->uid ?>_Categories.value=="") && document.we_form.<?php print $this->uid ?>_Type.value==0) ret=true;
								if(ret){
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[doctype_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
									return false;
								}

								ret=false;
								if(document.we_form.<?php print $this->uid ?>_Objects.value=="" && document.we_form.<?php print $this->uid ?>_Type.value==2) ret=true;
								if(ret){
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[objects_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
									return false;
								}

								ret=false;
								for(i=0;i<nsteps.value;i++){
									eval('if(document.we_form.<?php print $this->uid ?>_step'+i+'_Worktime.value=="") ret=true;');
									if(ret){
										var _txt = "<?php print addslashes(g_l('modules_workflow', '[worktime_empty]')); ?>";
			<?php print we_message_reporting::getShowMessageCall("_txt.replace(/%s/,i+1)", we_message_reporting::WE_MESSAGE_ERROR, true); ?>
										return false;
									}
									userempty=true;
									for(j=0;j<ntasks.value;j++){
										eval('if(document.we_form.<?php print $this->uid ?>_task_'+i+'_'+j+'_userid.value!=0) userempty=false;');
									}
									if(userempty){
										var _txt = "<?php print addslashes(g_l('modules_workflow', '[user_empty]')); ?>";

			<?php print we_message_reporting::getShowMessageCall("_txt.replace(/%s/,i+1)", we_message_reporting::WE_MESSAGE_ERROR, true); ?>
										return false;
									}

								}
								return true;
							}

		<?php } ?>
					//-->
		</script>
		<?php
	}

	function processCommands(){
		if(isset($_REQUEST['wcmd']))
			switch($_REQUEST['wcmd']){
				case 'new_workflow':
					$this->workflowDef = new we_workflow_workflow();
					$this->page = 0;
					print we_html_element::jsElement('
					top.content.resize.right.editor.edheader.location="' . WE_WORKFLOW_MODULE_DIR . 'edit_workflow_frameset.php?pnt=edheader";
					top.content.resize.right.editor.edfooter.location="' . WE_WORKFLOW_MODULE_DIR . 'edit_workflow_frameset.php?pnt=edfooter";
					');
					break;
				case 'add_cat':
					$arr = makeArrayFromCSV($this->workflowDef->Categories);
					if(isset($_REQUEST['wcat'])){
						$ids = makeArrayFromCSV($_REQUEST['wcat']);
						foreach($ids as $id){
							if(strlen($id) && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->workflowDef->Categories = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_cat':
					$arr = makeArrayFromCSV($this->workflowDef->Categories);
					if(isset($_REQUEST['wcat'])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST['wcat'])
								array_splice($arr, $k, 1);
						}
						$this->workflowDef->Categories = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_all_cats':
					$this->workflowDef->Categories = '';
					break;
				case 'add_objcat':
					$arr = makeArrayFromCSV($this->workflowDef->ObjCategories);
					if(isset($_REQUEST['wocat'])){
						$ids = makeArrayFromCSV($_REQUEST['wocat']);
						foreach($ids as $id){
							if(strlen($id) && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->workflowDef->ObjCategories = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_objcat':
					$arr = array();
					$arr = makeArrayFromCSV($this->workflowDef->ObjCategories);
					if(isset($_REQUEST['wocat'])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST['wocat'])
								array_splice($arr, $k, 1);
						}
						$this->workflowDef->ObjCategories = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_all_objcats':
					$this->workflowDef->ObjCategories = '';
					break;
				case 'add_folder':
					$arr = makeArrayFromCSV($this->workflowDef->Folders);
					if(isset($_REQUEST['wfolder'])){
						$ids = makeArrayFromCSV($_REQUEST['wfolder']);
						foreach($ids as $id){
							if(strlen($id) && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->workflowDef->Folders = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_folder':
					$arr = array();
					$arr = makeArrayFromCSV($this->workflowDef->Folders);
					if(isset($_REQUEST['wfolder'])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST['wfolder'])
								array_splice($arr, $k, 1);
						}
						$this->workflowDef->Folders = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_all_folders':
					$this->workflowDef->Folders = '';
					break;
				case 'add_object_file_folder':
					$arr = makeArrayFromCSV($this->workflowDef->ObjectFileFolders);
					if(isset($_REQUEST['woffolder'])){
						$ids = makeArrayFromCSV($_REQUEST['woffolder']);
						foreach($ids as $id){
							if(strlen($id) && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->workflowDef->ObjectFileFolders = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_object_file_folder':
					$arr = array();
					$arr = makeArrayFromCSV($this->workflowDef->ObjectFileFolders);
					if(isset($_REQUEST['woffolder'])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST['woffolder'])
								array_splice($arr, $k, 1);
						}
						$this->workflowDef->ObjectFileFolders = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_all_object_file_folders':
					$this->workflowDef->ObjectFileFolders = '';
					break;
				case 'add_object':
					$arr = array();
					$arr = makeArrayFromCSV($this->workflowDef->Objects);
					if(isset($_REQUEST['wobject'])){
						$arr[] = $_REQUEST['wobject'];
						$this->workflowDef->Objects = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_object':
					$arr = array();
					$arr = makeArrayFromCSV($this->workflowDef->Objects);
					if(isset($_REQUEST['wobject'])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST['wobject'])
								array_splice($arr, $k, 1);
						}
						$this->workflowDef->Objects = makeCSVFromArray($arr, true);
					}
					break;
				case 'del_all_objects':
					$this->workflowDef->Objects = '';
					break;
				case 'reload':
					print we_html_element::jsElement('
					top.content.resize.right.editor.edheader.location="' . WE_WORKFLOW_MODULE_DIR . 'edit_workflow_frameset.php?pnt=edheader&page=' . $this->page . '&txt=' . $this->workflowDef->Text . '";
					top.content.resize.right.editor.edfooter.location="' . WE_WORKFLOW_MODULE_DIR . 'edit_workflow_frameset.php?pnt=edfooter";
					');
					break;
				case 'edit_workflow':
					$this->show = 0;
					if(isset($_REQUEST['wid'])){
						$this->workflowDef = new we_workflow_workflow($_REQUEST['wid']);
					}

					$_REQUEST['wcmd'] = 'reload';
					$this->processCommands();
					break;
				case 'switchPage':
					if(isset($_REQUEST['page'])){
						$this->page = $_REQUEST['page'];
					}
					break;
				case 'save_workflow':
					if(isset($_REQUEST['wid'])){
						$newone = false;
						if(!$this->workflowDef->ID)
							$newone = true;
						$exist = false;
						$double = intval(f('SELECT COUNT(1) AS Count FROM ' . WORKFLOW_TABLE . " WHERE Text='" . $this->db->escape($this->workflowDef->Text) . "'" . ($newone ? '' : ' AND ID!=' . intval($this->workflowDef->ID)), 'Count', $this->db));

						if(!we_hasPerm('EDIT_WORKFLOW') && !we_hasPerm('NEW_WORKFLOW')){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} else if($newone && !we_hasPerm('NEW_WORKFLOW')){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} else{
							if($double){
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[double_name]'), we_message_reporting::WE_MESSAGE_ERROR));
								return;
							}
							$childs = '';
							$this->workflowDef->loadDocuments();
							foreach($this->workflowDef->documents as $k => $v)
								$childs.="top.content.deleteEntry(" . $v["ID"] . ",'file');\n";
							if(is_array($_REQUEST[$this->uid . '_MYDocType']) && !empty($_REQUEST[$this->uid . '_MYDocType'])){
								$this->workflowDef->DocType = makeCSVFromArray($_REQUEST[$this->uid . '_MYDocType'], true);
							}

							$this->workflowDef->save();
							print '<script type="text/javascript"><!--';
							if($newone){
								print 'top.content.makeNewEntry("workflow_folder",' . $this->workflowDef->ID . ',0,"' . $this->workflowDef->Text . '",true,"folder","we_workflow_workflowDef","' . $this->workflowDef->Status . '");';
							} else{
								print 'top.content.updateEntry(' . $this->workflowDef->ID . ',0,"' . $this->workflowDef->Text . '","' . $this->workflowDef->Status . '");';
							}
							print $childs;
							print 'top.content.resize.right.editor.edheader.document.getElementById("headrow").innerHTML="' . we_html_element::htmlB(g_l('modules_workflow', '[workflow]') . ': ' . oldHtmlspecialchars($this->workflowDef->Text)) . '";';
							print we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[save_ok]'), we_message_reporting::WE_MESSAGE_NOTICE);
							print '//--></script>';
						}
					}
					break;
				case 'show_document':
					if(isset($_REQUEST['wid'])){
						$this->show = 1;
						$this->page = 0;
						$this->documentDef->load($_REQUEST['wid']);
						print we_html_element::jsElement('
					top.content.resize.right.editor.edheader.location="' . WE_WORKFLOW_MODULE_DIR . 'edit_workflow_frameset.php?pnt=edheader&art=1&txt=' . $this->documentDef->document->Text . '";
					top.content.resize.right.editor.edfooter.location="' . WE_WORKFLOW_MODULE_DIR . 'edit_workflow_frameset.php?pnt=edfooter&art=1";
					');
					}
					break;
				case 'delete_workflow':
					if(isset($_REQUEST['wid'])){
						if(!we_hasPerm('DELETE_WORKFLOW')){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} else{

							$this->workflowDef = new we_workflow_workflow($_REQUEST['wid']);
							if($this->workflowDef->delete()){
								$this->workflowDef = new we_workflow_workflow();
								print we_html_element::jsElement('
							top.content.deleteEntry(' . $_REQUEST['wid'] . ',"folder");
							' . we_message_reporting::getShowMessageCall($lg_l('modules_workflow', '[delete_ok]'), we_message_reporting::WE_MESSAGE_NOTICE));
							} else{
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[delete_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
							}
						}
					}
					break;
				case 'reload_table':
					$this->page = 1;
					break;
				case 'empty_log':
					$stamp = 0;
					if(isset($_REQUEST['wopt']) && $_REQUEST['wopt']){
						$t = explode(',', $_REQUEST['wopt']);
						$stamp = mktime($t[3], $t[4], 0, $t[1], $t[0], $t[2]);
					}
					$this->Log->clearLog($stamp);
					print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_workflow', '[empty_log_ok]'), we_message_reporting::WE_MESSAGE_NOTICE));
					break;
				default:
			}
	}

	function processVariables(){
		if(isset($_REQUEST['wname']))
			$this->uid = $_REQUEST['wname'];
		foreach($this->workflowDef->persistents as $key => $val){
			$varname = $this->uid . '_' . $val;
			if(isset($_REQUEST[$varname])){
				$_REQUEST[$varname] = escape_sql_query($_REQUEST[$varname]);
				$this->workflowDef->$val = $_REQUEST[$varname];
			}
		}

		$wsteps = 0;
		$wtasks = 0;

		if(isset($_REQUEST['wsteps']))
			$wsteps = $_REQUEST['wsteps'];
		if(isset($_REQUEST['wtasks']))
			$wtasks = $_REQUEST['wtasks'];
		if(isset($_REQUEST['page']))
			$this->page = $_REQUEST['page'];


		$this->workflowDef->steps = array();
		if($wsteps == 0){
			$this->workflowDef->addNewStep();
			$this->workflowDef->addNewTask();
		}

		for($i = 0; $i < $wsteps; $i++){
			$this->workflowDef->addNewStep();
		}
		for($j = 0; $j < $wtasks; $j++){
			$this->workflowDef->addNewTask();
		}

		foreach($this->workflowDef->steps as $skey => $sval){
			$this->workflowDef->steps[$skey]->workflowID = $this->workflowDef->ID;

			$varname = $this->uid . '_step' . $skey . '_sid';
			if(isset($_REQUEST[$varname])){
				if($_REQUEST[$varname])
					$this->workflowDef->steps[$skey]->ID = $_REQUEST[$varname];
			}

			$varname = $this->uid . '_step' . $skey . '_and';
			if(isset($_REQUEST[$varname])){
				$this->workflowDef->steps[$skey]->stepCondition = $_REQUEST[$varname] ? 1 : 0;
			}

			$varname = $this->uid . '_step' . $skey . '_Worktime';
			if(isset($_REQUEST[$varname])){
				$this->workflowDef->steps[$skey]->Worktime = $_REQUEST[$varname];
			}

			$varname = $this->uid . '_step' . $skey . '_timeAction';
			if(isset($_REQUEST[$varname])){
				$this->workflowDef->steps[$skey]->timeAction = $_REQUEST[$varname];
			}

			foreach($this->workflowDef->steps[$skey]->tasks as $tkey => $tval){

				$this->workflowDef->steps[$skey]->tasks[$tkey]->stepID = $this->workflowDef->steps[$skey]->ID;

				$varname = $this->uid . '_task_' . $skey . '_' . $tkey . '_tid';
				if(isset($_REQUEST[$varname])){
					$this->workflowDef->steps[$skey]->tasks[$tkey]->ID = $_REQUEST[$varname];
				}

				$varname = $this->uid . '_task_' . $skey . '_' . $tkey . '_userid';
				if(isset($_REQUEST[$varname])){
					$this->workflowDef->steps[$skey]->tasks[$tkey]->userID = $_REQUEST[$varname];
				}

				$varname = $this->uid . '_task_' . $skey . '_' . $tkey . '_usertext';
				if(isset($_REQUEST[$varname])){
					$this->workflowDef->steps[$skey]->tasks[$tkey]->username = $_REQUEST[$varname];
				}

				$varname = $this->uid . '_task_' . $skey . '_' . $tkey . '_Edit';
				if(isset($_REQUEST[$varname])){
					$this->workflowDef->steps[$skey]->tasks[$tkey]->Edit = $_REQUEST[$varname];
				}

				$varname = $this->uid . '_task_' . $skey . '_' . $tkey . '_Mail';
				if(isset($_REQUEST[$varname])){
					$this->workflowDef->steps[$skey]->tasks[$tkey]->Mail = $_REQUEST[$varname];
				}
			}
		}
	}

	function getDocumentInfo(){
		if($this->documentDef->workflow->Type == we_workflow_workflow::OBJECT){
			return $this->getObjectInfo();
		}

		$_space = 100;

		$out = we_html_element::jsScript(JS_DIR . 'tooltip.js') .
			we_html_element::jsElement('function openToEdit(tab,id,contentType){
		if(top.opener && top.opener.top.weEditorFrameController) {
			top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		}
	}');

		//	Part - file-information
		$_parts = array(
			array(
				'headline' => g_l('weEditorInfo', '[content_type]'),
				'html' => g_l('weEditorInfo', '[' . $this->documentDef->document->ContentType . ']'),
				'space' => $_space,
				'noline' => (($this->documentDef->document->ContentType != 'folder' && $this->documentDef->workflow->Type != we_workflow_workflow::OBJECT) ? 1 : 0)
			)
		);
		if($this->documentDef->document->ContentType != 'folder' && $this->documentDef->workflow->Type != we_workflow_workflow::OBJECT){
			$GLOBALS['we_doc'] = $this->documentDef->document;
			$fs = $this->documentDef->document->getFilesize($this->documentDef->document->Path);
			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[file_size]'),
				'html' => round(($fs / 1024), 2) . '&nbsp;KB&nbsp;(' . number_format($fs, 0, ',', '.') . '&nbsp;Byte)',
				'space' => $_space
			);
		}

		//	Part - publish-information

		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[creation_date]'),
			'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->CreationDate),
			'space' => $_space,
			'noline' => 1
		);

		if($this->documentDef->document->CreatorID){
			$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->CreatorID);
			if($this->db->next_record())
				$_parts[] = array(
					'headline' => g_l('modules_users', '[created_by]'),
					'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
					'space' => $_space,
					'noline' => 1
				);
		}

		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[changed_date]'),
			'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->ModDate),
			'space' => $_space,
			'noline' => 1
		);

		if($this->documentDef->document->ModifierID){
			$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->ModifierID);
			if($this->db->next_record())
				$_parts[] = array(
					'headline' => g_l('modules_users', '[changed_by]'),
					'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
					'space' => $_space,
					'noline' => 1
				);
		}

		if($this->documentDef->document->ContentType == 'text/html' || $this->documentDef->document->ContentType == 'text/webedition'){
			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[lastLive]'),
				'html' => ($this->documentDef->document->Published ? date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->Published) : '-'),
				'space' => $_space
			);
		}


		//	Part - Path-information

		if($this->documentDef->document->Table != TEMPLATES_TABLE && $this->documentDef->workflow->Type != we_workflow_workflow::OBJECT){
			$rp = $this->documentDef->document->getRealPath();
			$http = $this->documentDef->document->getHttpPath();
			$showlink = ($this->documentDef->document->ContentType == 'text/html' ||
				$this->documentDef->document->ContentType == 'text/webedition' ||
				$this->documentDef->document->ContentType == 'image/*' ||
				$this->documentDef->document->ContentType == 'application/x-shockwave-flash');

			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[local_path]'),
				'html' => '<a href="#" style="text-decoration:none;cursor:text" class="defaultfont" onMouseOver="showtip(this,event,\'' . $rp . '\')" onMouseOut="hidetip()"  onclick="openToEdit(\'' . $this->documentDef->document->Table . '\',\'' . $this->documentDef->document->ID . '\',\'' . $this->documentDef->document->ContentType . '\')" >' . shortenPath($rp, 74) . '</a>',
				'space' => $_space,
				'noline' => 1
			);

			$_parts[] = array(
				'headline' => g_l('weEditorInfo', '[http_path]'),
				'html' => ($showlink ? '<a href="' . $http . '" target="_blank" onMouseOver="showtip(this,event,\'' . $http . '\')" onMouseOut="hidetip()">' : '') . shortenPath($http, 74) . ($showlink ? '</a>' : ''),
				'space' => $_space
			);
			$_parts[] = array(
				'headline' => '',
				'html' => '<a href="#" onclick="openToEdit(\'' . $this->documentDef->document->Table . '\',\'' . $this->documentDef->document->ID . '\',\'' . $this->documentDef->document->ContentType . '\')" >' . g_l('weEditorInfo', "[openDocument]") . '</a>',
				'space' => $_space
			);
		}



		//	Logbook
		$_parts[] = array(
			'headline' => '',
			'html' => self::getDocumentStatus($this->documentDef->ID),
			'space' => 0
		);
		$out .= we_multiIconBox::getHTML('', '100%', $_parts, 30);
		return $out;
	}

	function getObjectInfo(){
		$_space = 150;

		//	Dokument properties
		$_parts = array(
			array(
				'headline' => 'ID',
				'html' => $this->documentDef->document->ID,
				'space' => $_space,
				'noline' => 1
			),
			array(
				'headline' => g_l('weEditorInfo', '[content_type]'),
				'html' => g_l('weEditorInfo', '[' . $this->documentDef->document->ContentType . ']'),
				'space' => $_space,
			),
			// publish information
			array(
				'headline' => g_l('weEditorInfo', '[creation_date]'),
				'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->CreationDate),
				'space' => $_space,
				'noline' => 1
			)
		);

		$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->CreatorID);
		if($this->db->next_record())
			$_parts[] = array(
				'headline' => g_l('modules_users', '[created_by]'),
				'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
				'space' => $_space,
				'noline' => 1
			);

		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[changed_date]'),
			'html' => date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->ModDate),
			'space' => $_space,
			'noline' => 1
		);

		$this->db->query('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . $this->documentDef->document->ModifierID);
		if($this->db->next_record())
			$_parts[] = array(
				'headline' => g_l('modules_users', '[changed_by]'),
				'html' => $this->db->f('First') . ' ' . $this->db->f('Second') . ' (' . $this->db->f('username') . ')',
				'space' => $_space,
				'noline' => 1
			);

		$_parts[] = array(
			'headline' => g_l('weEditorInfo', '[lastLive]'),
			'html' => ($this->documentDef->document->Published ? date(g_l('weEditorInfo', '[date_format]'), $this->documentDef->document->Published) : '-'),
			'space' => $_space,
		);

		$_parts[] = array(
			'headline' => '',
			'html' => '<a href="#" onclick="openToEdit(\'' . $this->documentDef->document->Table . '\',\'' . $this->documentDef->document->ID . '\',\'' . $this->documentDef->document->ContentType . '\')" >' . g_l('weEditorInfo', "[openDocument]") . '</a>',
			'space' => $_space
		);

		$_parts[] = array(
			'headline' => '',
			'html' => self::getDocumentStatus($this->documentDef->ID),
			'space' => 0,
		);


		include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');

		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
		function openToEdit(tab,id,contentType){
		if(top.opener && top.opener.top.weEditorFrameController) {
			top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		} else if(top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
			top.opener.top.opener.top.opener.top.weEditorFrameController.openDocument(tab,id,contentType);
		}
	}') .
			'</head>
		<body class="weEditorBody" onunload="doUnload()">
				<form name="we_form">' . we_class::hiddenTrans() . '<table cellpadding="6" cellspacing="0" border="0">' .
			we_multiIconBox::getHTML('', '100%', $_parts, 30) .
			'</form></body></html>';
	}

	function getTime($seconds){
		$min = floor($seconds / 60);
		$ret = array(
			'hour' => floor($min / 60),
			'min' => $min,
			'sec' => $seconds - ($min * 60));
		$ret['min'] -= ($ret['hour'] * 60);
		return $ret;
	}

	static function getDocumentStatus($workflowDocID){
		$db = new DB_WE;
		$headline = array(array('dat' => '<div class="middlefont">' . g_l('modules_workflow', '[step]') . '</div>'));

		$workflowDocument = new we_workflow_document($workflowDocID);

		$counter = 0;
		$counter1 = 0;
		$current = $workflowDocument->findLastActiveStep();
		if($current < 0){
			return g_l('modules_workflow', '[cannot_find_active_step]');
		}
		foreach($workflowDocument->steps as $sk => $sv){

			$workflowStep = new we_workflow_step($sv->workflowStepID);

			/* $now = date(g_l('weEditorInfo', "[date_format]"), time());
			  $start = date(g_l('weEditorInfo', "[date_format]"), $sv->startDate);
			 */
			$secs = time() - $sv->startDate;
			$elapsed = self::getTime($secs);

			$secs = ($sv->startDate + round($workflowStep->Worktime * 3600)) - time();
			$remained = self::getTime($secs);

			if($remained['hour'] < 0){
				if($sk > $current){
					$finished_font = 'middlefont';
					$notfinished_font = 'middlefontgray';
				} else{
					$finished_font = 'middlefontred';
					$notfinished_font = 'middlefontred';
				}
			} else{
				$finished_font = 'middlefont';
				$notfinished_font = 'middlefontgray';
			}

			$end = date(g_l('weEditorInfo', '[date_format]'), $sv->startDate + round($workflowStep->Worktime * 3600));

			$content[$counter] = array(
				array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . ($counter + 1) . "</div>",
					'height' => '',
					'align' => 'center'
				)
			);

			$counter1 = 0;
			foreach($sv->tasks as $tk => $tv){

				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[user]') . (string) ($counter1);

				$workflowTask = new we_workflow_task($tv->workflowTaskID);

				$foo = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($workflowTask->userID), 'username', $db);

				if($sk == $current){
					$out = ($tv->Status == we_workflow_documentTask::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $foo . "</div>";
				} else if($sk < $current){
					$out = '<div class="' . $finished_font . '">' . $foo . '</div>';
				} else{
					$out = '<div class="' . $notfinished_font . '">' . $foo . '</div>';
				}

				$content[$counter][$counter1] = array(
					'dat' => $out,
					'height' => '',
					'align' => '',
				);
			}


			$headline[++$counter1] = array('dat' => g_l('modules_workflow', '[worktime]'));

			$content[$counter][$counter1] = array(
				'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $workflowStep->Worktime . '</div>',
				'height' => '',
				'align' => 'right');

			if($sk <= $current){
				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[time_elapsed]');


				$content[$counter][$counter1] = array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $elapsed["hour"] . ":" . $elapsed["min"] . ":" . $elapsed["sec"] . "</div>",
					'height' => '',
					'align' => 'right',
				);

				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[time_remained]');

				$content[$counter][$counter1] = array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $remained["hour"] . ":" . $remained["min"] . ":" . $remained["sec"] . "</div>",
					'height' => '',
					'align' => 'right',
				);

				$headline[++$counter1]['dat'] = g_l('modules_workflow', '[step_plan]');

				$content[$counter][$counter1] = array(
					'dat' => ($sv->Status == we_workflow_documentStep::STATUS_UNKNOWN ? '<div class="' . $notfinished_font . '">' : '<div class="' . $finished_font . '">') . $end . "</div>",
					'height' => '',
					'align' => 'right',
				);
			}
			++$counter;
		}

		$wfType = f('SELECT ' . WORKFLOW_TABLE . '.Type as Type FROM ' . WORKFLOW_TABLE . ',' . WORKFLOW_DOC_TABLE . ' WHERE ' . WORKFLOW_DOC_TABLE . '.workflowID=' . WORKFLOW_TABLE . '.ID AND ' . WORKFLOW_DOC_TABLE . '.ID=' . intval($workflowDocument->ID), 'Type', $db);
		return '<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td></td><td>' . we_html_tools::htmlDialogBorder3(730, 300, $content, $headline) . '</td><td>' . we_html_tools::getPixel(15, 10) . '</td>
		</tr>
			<td></td><td>' . we_html_tools::getPixel(10, 10) . '</td><td>' . we_html_tools::getPixel(15, 10) . '</td>
		<tr>
			<td></td><td>' . we_button::create_button('logbook', "javascript:new jsWindow('" . WE_WORKFLOW_MODULE_DIR . "edit_workflow_frameset.php?pnt=log&art=" . $workflowDocument->document->ID . "&type=" . $wfType . "','workflow_history',-1,-1,640,480,true,false,true);") . '</td><td>' . we_html_tools::getPixel(15, 10) . '</td>
		</tr>
		</table>';
	}

	static function getLogForDocument($docID, $type = 0){
		$db = new DB_WE;

		$content = array();

		$headlines = array(
			array('dat' => g_l('modules_workflow', '[action]')),
			array('dat' => g_l('modules_workflow', '[description]')),
			array('dat' => g_l('modules_workflow', '[time]')),
			array('dat' => g_l('modules_workflow', '[user]')),
		);

		$logs = array();
		$logs = we_workflow_log::getLogForDocument($docID, 'DESC', $type);
		$counter = 0;

		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$art = isset($_REQUEST['art']) ? $_REQUEST['art'] : '';
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
		$numRows = we_workflow_log::NUMBER_LOGS;
		$anz = $GLOBALS['ANZ_LOGS'];

		foreach($logs as $v){
			$foo = getHash('SELECT First,Second,username FROM ' . USER_TABLE . ' WHERE ID=' . intval($v['userID']), $db);
			$content[$counter] = array(
				array(
					'dat' => '<div class="middlefont">' . $v['Type'] . '</div>',
					'height' => '',
					'align' => '',
				),
				array(
					'dat' => '<div class="middlefont">' . $v['Description'] . '</div>',
					'height' => '',
					'align' => '',
				),
				array(
					'dat' => '<div class="middlefont"><nobr>' . date(g_l('weEditorInfo', '[date_format]'), $v['logDate']) . '</nobr></div>',
					'height' => '',
					'align' => 'right',
				),
				array(
					'dat' => '<div class="middlefont">' . ((isset($foo['First']) && $foo['First']) ? $foo['First'] : '-') . ' ' . ((isset($foo['Second']) && $foo['Second']) ? $foo['Second'] : '-') . ((isset($foo['username']) && $foo['username']) ? ' (' . $foo['username'] . ')' : '') . '</div>',
					'height' => '',
					'align' => 'left',
				),
			);

			++$counter;
		}

		$nextprev = '<table cellpadding="0" cellspacing="0" border="0"><tr><td>' .
			($offset ?
				we_button::create_button('back', WE_WORKFLOW_MODULE_DIR . "edit_workflow_frameset.php?pnt=log&art=$art&type=$type&offset=" . ($offset - $numRows)) :
				we_button::create_button('back', '', false, 100, 22, '', '', true)
			) .
			we_html_tools::getPixel(23, 1) . "</td><td class='defaultfont' style=\"padding: 0 10px 0 10px;\"><b>" . (($anz) ? $offset + 1 : 0) . "-" .
			(($anz - $offset) < $numRows ? $anz : $offset + $numRows) .
			we_html_tools::getPixel(5, 1) . ' ' . g_l('global', '[from]') . ' ' . we_html_tools::getPixel(5, 1) . $anz . '</b></td><td>' . we_html_tools::getPixel(23, 1) .
			((($offset + $numRows) < $anz) ?
				we_button::create_button('next', WE_WORKFLOW_MODULE_DIR . "edit_workflow_frameset.php?pnt=log&art=$art&type=$type&offset=" . ($offset + $numRows) . "&order=$order") :
				we_button::create_button('next', '', '', 100, 22, '', '', true)
			) .
			'</td><td></tr></table>';

		$buttonsTable = '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td>' . $nextprev . '</td><td align="right">' . we_button::create_button("close", "javascript:self.close();") . '</td></tr></table>';


		if(count($logs)){
			return we_html_tools::htmlDialogLayout(we_html_tools::htmlDialogBorder3(580, 300, $content, $headlines), '', $buttonsTable);
		} else{
			return we_html_tools::htmlDialogLayout('<div style="width:500px" class="middlefontgray" align="center"><center>-- ' . g_l('modules_workflow', '[log_is_empty]') . ' --</center></div>', '', we_button::create_button("close", "javascript:self.close();"));
		}
	}

	function getLogQuestion(){
		$vals = array('<table cellpading="0" cellspacing="0"><tr><td>' . we_html_tools::getPixel(22, 5) . '</td><td>' . we_html_tools::getDateInput2("log_time%s", (time() - (336 * 3600))) . '</td></tr></table>');

		return we_html_element::jsElement('
			function clear(){
				opener.top.content.cmd.document.we_form.wcmd.value="empty_log";
				if(document.we_form.clear_opt.value==1){
					var day=document.we_form.log_time_day.options[document.we_form.log_time_day.selectedIndex].text;
					var month=document.we_form.log_time_month.options[document.we_form.log_time_month.selectedIndex].text;
					var year=document.we_form.log_time_year.options[document.we_form.log_time_year.selectedIndex].text;
					var hour=document.we_form.log_time_hour.options[document.we_form.log_time_hour.selectedIndex].text;
					var min=document.we_form.log_time_minute.options[document.we_form.log_time_minute.selectedIndex].text;

					var timearr=[day,month,year,hour,min];
					opener.top.content.cmd.document.we_form.wopt.value=timearr.join();
				}
				else{
					if(!confirm("' . g_l('modules_workflow', '[emty_log_question]') . '")) return;
				}
				opener.top.content.cmd.submitForm();
				close();
			}
			self.focus();
		') .
			we_html_tools::htmlDialogLayout(
				$this->htmlHidden('clear_opt', '1') .
				'<form name="we_form">' .
				'<table cellpading="0" cellspacing="0">' .
				'<tr>' .
				'<td class="defaultfont">' . g_l('modules_workflow', '[log_question_text]') . '</td>' .
				'</tr>' .
				'<tr>' .
				'<td>' . we_html_tools::getPixel(10, 10) . '</td>' .
				'</tr>' .
				'<tr>' .
				'<td>' . $this->getTypeTableHTML(we_forms::radiobutton('1', true, 'clear_time', g_l('modules_workflow', '[log_question_time]'), true, 'defaultfont', "javascript:document.we_form.clear_opt.value=1;"), $vals) . '</td>' .
				'</tr>' .
				'<tr>' .
				'<td>' . we_html_tools::getPixel(22, 10) . '<br/>' . we_forms::radiobutton('0', false, 'clear_time', g_l('modules_workflow', '[log_question_all]'), true, 'defaultfont', "javascript:document.we_form.clear_opt.value=0;") . '</td>' .
				'</tr>' .
				'</tr>' .
				'</table>'
				, g_l('modules_workflow', '[empty_log]'), we_button::position_yes_no_cancel(we_button::create_button('ok', 'javascript:self.clear();'), '', we_button::create_button('cancel', 'javascript:self.close();')
				)
			) . '</form>';
	}

}
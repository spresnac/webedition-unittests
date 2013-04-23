<?php

/**
 * webEdition CMS
 *
 * $Rev: 5956 $
 * $Author: mokraemer $
 * $Date: 2013-03-14 11:53:57 +0100 (Thu, 14 Mar 2013) $
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

/**
 * Class which contains all functions for the
 * rebuild dialog and the rebuild function
 * @static
 */
abstract class we_rebuild_wizard{

	/**
	 * returns HTML for the Body Frame
	 *
	 * @return string
	 */
	static function getBody(){
		$step = 'getStep' . (isset($_REQUEST["step"]) ? $_REQUEST["step"] : '0');
		return self::getPage(self::$step());
	}

	/**
	 * returns HTML for the Frame with the progress bar
	 *
	 * @return string
	 */
	static function getBusy(){
		$dc = isset($_REQUEST["dc"]) ? $_REQUEST["dc"] : 0;

		$WE_PB = new we_progressBar(0, 0, true);
		$WE_PB->setStudLen($dc ? 490 : 200);
		$WE_PB->addText("", 0, "pb1");
		$js = $WE_PB->getJSCode();
		$pb = $WE_PB->getHTML();

		$js .= we_html_element::jsElement(
				'function showRefreshButton() {
				  prevBut = document.getElementById("prev");
				  nextBut = document.getElementById("next");
				  refrBut = document.getElementById("refresh");
				  prevBut.style.display = "none";
				  nextBut.style.display = "none";
				  refrBut.style.display = "";
				}
				function showPrevNextButton() {
				  prevBut = document.getElementById("prev");
				  nextBut = document.getElementById("next");
				  refrBut = document.getElementById("refresh");
				  refrBut.style.display = "none";
				  prevBut.style.display = "";
				  nextBut.style.display = "";
				}');

		$cancelButton = we_button::create_button("cancel", "javascript:top.close();");
		$refreshButton = we_button::create_button("refresh", "javascript:parent.wizcmd.location.reload();", true, -1, -1, "", "", false, false);

		$nextbutdisabled = !(we_hasPerm("REBUILD_ALL") || we_hasPerm("REBUILD_FILTERD") || we_hasPerm("REBUILD_OBJECTS") || we_hasPerm("REBUILD_INDEX") || we_hasPerm("REBUILD_THUMBS") || we_hasPerm("REBUILD_META"));

		if($dc){
			$buttons = we_button::create_button_table(array($refreshButton, $cancelButton), 10);
			$pb = we_html_tools::htmlDialogLayout($pb, g_l('rebuild', "[rebuild]"), $buttons);
		} else{
			$prevButton = we_button::create_button("back", "javascript:parent.wizbody.handle_event('previous');", true, -1, -1, "", "", true, false);
			$nextButton = we_button::create_button("next", "javascript:parent.wizbody.handle_event('next');", true, -1, -1, "", "", $nextbutdisabled, false);

			$content2 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 1, 4);
			$content2->setCol(0, 0, array("id" => "prev", "style" => "display:table-cell; padding-left:10px;", "align" => "right"), $prevButton);
			$content2->setCol(0, 1, array("id" => "next", "style" => "display:table-cell; padding-left:10px;", "align" => "right"), $nextButton);
			$content2->setCol(0, 2, array("id" => "refresh", "style" => "display:none; padding-left:10px;", "align" => "right"), $refreshButton);
			$content2->setCol(0, 3, array("id" => "cancel", "style" => "display:table-cell; padding-left:10px;", "align" => "right"), $cancelButton);

			$content = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "100%"), 1, 2);
			$content->setCol(0, 0, array("id" => "progr", "style" => "display:none", "align" => "left"), $pb);
			$content->setCol(0, 1, array("align" => "right"), $content2->getHtml());
		}


		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead(g_l('rebuild', "[rebuild]")) .
					STYLESHEET .
					($dc ? '' : we_button::create_state_changer()) . $js) .
				we_html_element::htmlBody(array("class" => ($dc ? "weDialogBody" : "weDialogButtonsBody")), ($dc ? $pb : $content->getHtml())
				)
		);
	}

	/**
	 * returns HTML for the Cmd Frame
	 *
	 * @return string for now it is an empty page
	 */
	static function getCmd(){
		return self::getPage(array('', ''));
	}

	/**
	 * returns the HTML for the First Step (0) of the wizard
	 *
	 * @return string
	 */
	static function getStep0(){
		$btype = isset($_REQUEST["btype"]) ? $_REQUEST["btype"] : "rebuild_all";
		$categories = isset($_REQUEST["categories"]) ? $_REQUEST["categories"] : "";
		$doctypes = (isset($_REQUEST["doctypes"]) && is_array($_REQUEST["doctypes"])) ? makeCSVFromArray($_REQUEST["doctypes"], true) : "";
		$folders = isset($_REQUEST["folders"]) ? $_REQUEST["folders"] : (($dws = get_def_ws()) ? $dws : "");
		$maintable = isset($_REQUEST["maintable"]) ? $_REQUEST["maintable"] : 0;
		$tmptable = isset($_REQUEST["tmptable"]) ? $_REQUEST["tmptable"] : 0;
		$thumbsFolders = isset($_REQUEST["thumbsFolders"]) ? $_REQUEST["thumbsFolders"] : (($dws = get_def_ws()) ? $dws : "");
		$thumbs = (isset($_REQUEST["thumbs"]) && is_array($_REQUEST["thumbs"])) ? makeCSVFromArray($_REQUEST["thumbs"], true) : "";
		$catAnd = isset($_REQUEST["catAnd"]) ? $_REQUEST["catAnd"] : 0;
		$metaFolders = isset($_REQUEST["metaFolders"]) ? $_REQUEST["metaFolders"] : (($dws = get_def_ws()) ? $dws : "");
		$metaFields = isset($_REQUEST["_field"]) ? $_REQUEST["_field"] : array();
		$onlyEmpty = isset($_REQUEST["onlyEmpty"]) ? $_REQUEST["onlyEmpty"] : 0;

		if(isset($_REQUEST["type"])){
			$type = $_REQUEST["type"];
		} else{
			if(we_hasPerm("REBUILD_ALL") || we_hasPerm("REBUILD_FILTERD")){
				$type = "rebuild_documents";
			} else if(defined("OBJECT_FILES_TABLE") && we_hasPerm("REBUILD_OBJECTS")){
				$type = "rebuild_objects";
			} else if(we_hasPerm("REBUILD_INDEX")){
				$type = "rebuild_index";
			} else if(we_hasPerm("REBUILD_THUMBS")){
				$type = "rebuild_thumbnails";
			} else if(we_hasPerm("REBUILD_NAVIGATION")){
				$type = "rebuild_navigation";
			} else if(we_hasPerm("REBUILD_META")){
				$type = "rebuild_metadata";
			} else{
				$type = "";
			}
		}


		$parts = array(
			array(
				"headline" => "",
				"html" => we_forms::radiobutton("rebuild_documents", ($type == "rebuild_documents" && (we_hasPerm("REBUILD_ALL") || we_hasPerm("REBUILD_FILTERD"))), "type", g_l('rebuild', "[documents]"), true, "defaultfont", "setNavStatDocDisabled()", (!(we_hasPerm("REBUILD_ALL") || we_hasPerm("REBUILD_FILTERD"))), g_l('rebuild', "[txt_rebuild_documents]"), 0, 495),
				"space" => 0
			)
		);

		if(defined("OBJECT_FILES_TABLE")){

			$parts[] = array(
				"headline" => "",
				"html" => we_forms::radiobutton("rebuild_objects", ($type == "rebuild_objects" && we_hasPerm("REBUILD_OBJECTS")), "type", g_l('rebuild', "[rebuild_objects]"), true, "defaultfont", "setNavStatDocDisabled()", (!we_hasPerm("REBUILD_OBJECTS")), g_l('rebuild', "[txt_rebuild_objects]"), 0, 495),
				"space" => 0
			);
		}

		$parts[] = array(
			"headline" => "",
			"html" => we_forms::radiobutton("rebuild_index", ($type == "rebuild_index" && we_hasPerm("REBUILD_INDEX")), "type", g_l('rebuild', "[rebuild_index]"), true, "defaultfont", "setNavStatDocDisabled()", (!we_hasPerm("REBUILD_INDEX")), g_l('rebuild', "[txt_rebuild_index]"), 0, 495),
			"space" => 0
		);

		$parts[] = array(
			"headline" => "",
			"html" => we_forms::radiobutton("rebuild_thumbnails", ($type == "rebuild_thumbnails" && we_hasPerm("REBUILD_THUMBS")), "type", g_l('rebuild', "[thumbnails]"), true, "defaultfont", "setNavStatDocDisabled()", (we_image_edit::gd_version() == 0 || (!we_hasPerm("REBUILD_THUMBS"))), g_l('rebuild', "[txt_rebuild_thumbnails]"), 0, 495),
			"space" => 0
		);

		$_navRebuildHTML = '<div>' .
			we_forms::radiobutton("rebuild_navigation", ($type == "rebuild_navigation" && we_hasPerm("REBUILD_NAVIGATION")), "type", g_l('rebuild', "[navigation]"), false, "defaultfont", "setNavStatDocDisabled()", !we_hasPerm("REBUILD_NAVIGATION"), g_l('rebuild', "[txt_rebuild_navigation]"), 0, 495) .
			'</div><div style="padding:10px 20px;">' .
			we_forms::checkbox(1, false, 'rebuildStaticAfterNavi', g_l('rebuild', "[rebuildStaticAfterNaviCheck]"), false, 'defaultfont', '', true, g_l('rebuild', "[rebuildStaticAfterNaviHint]"), 0, 475) .
			'</div>';

		$parts[] = array(
			"headline" => "",
			"html" => $_navRebuildHTML,
			"space" => 0
		);

		$metaDataFields = weMetaData::getDefinedMetaDataFields();

		$_rebuildMetaDisabled = true;
		foreach($metaDataFields as $md){
			if($md['importFrom'] !== ""){
				$_rebuildMetaDisabled = false;
				break;
			}
		}

		$parts[] = array(
			"headline" => "",
			"html" => we_forms::radiobutton("rebuild_metadata", ($type == "rebuild_metadata" && we_hasPerm("REBUILD_META")), "type", g_l('rebuild', "[metadata]"), true, "defaultfont", "setNavStatDocDisabled()", (!we_hasPerm("REBUILD_META")) || $_rebuildMetaDisabled, g_l('rebuild', "[txt_rebuild_metadata]"), 0, 495),
			"space" => 0
		);

		$allbutdisabled = !(we_hasPerm("REBUILD_ALL") || we_hasPerm("REBUILD_FILTERD") || we_hasPerm("REBUILD_OBJECTS") || we_hasPerm("REBUILD_INDEX") || we_hasPerm("REBUILD_THUMBS") || we_hasPerm("REBUILD_META"));


		$js =
			'window.onload = function(){top.focus();}
			function handle_event(what){
				f = document.we_form;
				switch(what){
					case "previous":
						break;
					case "next":
						selectedValue="";
						for(var i=0;i<f.type.length;i++){
							if(f.type[i].checked){;
								selectedValue = f.type[i].value;
					}
						}
						goTo(selectedValue)
						break;
				}
			}
			function goTo(where){
				f = document.we_form;
				switch(where){
					case "rebuild_thumbnails":
					case "rebuild_documents":
						f.target="wizbody";
						break;
					case "rebuild_objects":
					case "rebuild_index":
					case "rebuild_navigation":
						set_button_state(1);
						f.target="wizcmd";
						f.step.value="2";
						break;
				}
				f.submit();
			}
			function set_button_state(alldis) {
				if(top.frames["wizbusy"] && top.frames["wizbusy"].switch_button_state){
					top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "disabled");
					if(alldis){
						top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "disabled");
						top.frames["wizbusy"].showRefreshButton();
					}else{
						top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
					}
				}else{
					setTimeout("set_button_state("+(alldis ? 1 : 0)+")",300);
				}
			}
			set_button_state(' . ($allbutdisabled ? 1 : 0) . ');';

		$js .=
			'function setNavStatDocDisabled() {
		var radio = document.getElementById("type");
		var check = document.getElementById("rebuildStaticAfterNavi");
		var checkLabel = document.getElementById("label_rebuildStaticAfterNavi");
		check.disabled=(!radio.checked);
		checkLabel.style.color = radio.checked ? "" : "grey";
	}';

		$dthidden = "";
		$doctypesArray = makeArrayFromCSV($doctypes);
		foreach($doctypesArray as $k => $v){
			$dthidden .= we_html_element::htmlHidden(array("name" => "doctypes[$k]", "value" => $v));
		}

		$thumbsHidden = "";
		$thumbsArray = makeArrayFromCSV($thumbs);
		foreach($thumbsArray as $k => $v){
			$thumbsHidden .= we_html_element::htmlHidden(array("name" => "thumbs[$k]", "value" => $v));
		}

		$metaFieldsHidden = "";
		foreach($metaFields as $_key => $_val){
			$metaFieldsHidden .= we_html_element::htmlHidden(array("name" => "_field[$_key]", "value" => $_val));
		}

		return array($js, we_multiIconBox::getHTML("", "100%", $parts, 40, "", -1, "", "", false, g_l('rebuild', "[rebuild]")) .
			$dthidden .
			$thumbsHidden .
			$metaFieldsHidden .
			we_html_element::htmlHidden(array("name" => "catAnd", "value" => $catAnd)) .
			we_html_element::htmlHidden(array("name" => "thumbsFolders", "value" => $thumbsFolders)) .
			we_html_element::htmlHidden(array("name" => "metaFolders", "value" => $metaFolders)) .
			we_html_element::htmlHidden(array("name" => "maintable", "value" => $maintable)) .
			we_html_element::htmlHidden(array("name" => "tmptable", "value" => $tmptable)) .
			we_html_element::htmlHidden(array("name" => "categories", "value" => $categories)) .
			we_html_element::htmlHidden(array("name" => "folders", "value" => $folders)) .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "btype", "value" => $btype)) .
			we_html_element::htmlHidden(array("name" => "onlyEmpty", "value" => $onlyEmpty)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "rebuild")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "1")));
	}

	/**
	 * returns the HTML for the Second Step (1) of the wizard
	 *
	 * @return string
	 */
	static function getStep1(){
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "rebuild_documents";

		switch($type){
			case "rebuild_documents":
				return we_rebuild_wizard::getRebuildDocuments();
			case "rebuild_thumbnails":
				return we_rebuild_wizard::getRebuildThumbnails();
			case "rebuild_metadata":
				return we_rebuild_wizard::getRebuildMetadata();
		}
	}

	/**
	 * returns the HTML for the Third Step (2) of the wizard. - Here the real work (loop) is done - it should be displayed in the cmd frame
	 *
	 * @return string
	 */
	static function getStep2(){
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "rebuild_documents";
		$btype = isset($_REQUEST["btype"]) ? $_REQUEST["btype"] : "rebuild_all";
		$categories = isset($_REQUEST["categories"]) ? $_REQUEST["categories"] : "";
		$doctypes = (isset($_REQUEST["doctypes"]) && is_array($_REQUEST["doctypes"])) ? makeCSVFromArray($_REQUEST["doctypes"], true) : "";
		$folders = isset($_REQUEST["folders"]) ? $_REQUEST["folders"] : "";
		$maintable = isset($_REQUEST["maintable"]) ? $_REQUEST["maintable"] : 0;
		$tmptable = isset($_REQUEST["tmptable"]) ? $_REQUEST["tmptable"] : 0;
		$thumbsFolders = isset($_REQUEST["thumbsFolders"]) ? $_REQUEST["thumbsFolders"] : "";
		$thumbs = (isset($_REQUEST["thumbs"]) && is_array($_REQUEST["thumbs"])) ? makeCSVFromArray($_REQUEST["thumbs"], true) : "";
		$catAnd = isset($_REQUEST["catAnd"]) ? $_REQUEST["catAnd"] : 0;
		$templateID = isset($_REQUEST["templateID"]) ? $_REQUEST["templateID"] : 0;
		$metaFolders = isset($_REQUEST["metaFolders"]) ? $_REQUEST["metaFolders"] : (($dws = get_def_ws()) ? $dws : "");
		$metaFields = isset($_REQUEST["_field"]) ? $_REQUEST["_field"] : array();
		$onlyEmpty = isset($_REQUEST["onlyEmpty"]) ? $_REQUEST["onlyEmpty"] : 0;

		$taskname = md5(session_id() . "_rebuild");
		$currentTask = isset($_GET["fr_" . $taskname . "_ct"]) ? $_GET["fr_" . $taskname . "_ct"] : 0;
		$taskFilename = WE_FRAGMENT_PATH . $taskname;


		$js = 'function set_button_state() {
				if(top.frames["wizbusy"] && top.frames["wizbusy"].switch_button_state){
					top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "enabled");
					top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
				}else{
					setTimeout("set_button_state()",300);
				}
			}
			set_button_state();';
		if(!(file_exists($taskFilename) && $currentTask)){
			switch($type){
				case 'rebuild_documents':
					$data = we_rebuild::getDocuments($btype, $categories, $catAnd, $doctypes, $folders, $maintable, $tmptable, $templateID);
					break;
				case 'rebuild_thumbnails':
					if(!$thumbs){
						return array($js . ";top.frames[\"wizbusy\"].showPrevNextButton();" . we_message_reporting::getShowMessageCall(g_l('rebuild', '[no_thumbs_selected]'), we_message_reporting::WE_MESSAGE_ERROR), '');
					}
					$data = we_rebuild::getThumbnails($thumbs, $thumbsFolders);
					break;
				case 'rebuild_index':
					$data = we_rebuild::getIndex();
					break;
				case 'rebuild_objects':
					$data = we_rebuild::getObjects();
					break;
				case 'rebuild_navigation':
					$data = we_rebuild::getNavigation();
					break;
				case 'rebuild_metadata':
					$data = we_rebuild::getMetadata($metaFields, $onlyEmpty, $metaFolders);
					break;
			}
			if(count($data)){
				$fr = new rebuildFragment($taskname, 1, 0, array(), $data);

				return array();
			} else{
				return array($js . we_message_reporting::getShowMessageCall(g_l('rebuild', "[nothing_to_rebuild]"), we_message_reporting::WE_MESSAGE_ERROR) . 'top.wizbusy.showPrevNextButton();', "");
			}
		} else{
			$fr = new rebuildFragment($taskname, 1, 0, array());

			return array();
		}
	}

	/**
	 * returns HTML for the category form
	 *
	 * @return string
	 * @param string $categories csv value with category IDs
	 * @param boolean $catAnd if the categories should be connected with AND
	 */
	static function formCategory($categories, $catAnd){
		$catAndCheck = we_forms::checkbox("1", $catAnd, "catAnd", g_l('rebuild', "[catAnd]"), false, "defaultfont", "document.we_form.btype[2].checked=true;");
		$delallbut = we_button::create_button("delete_all", "javascript:document.we_form.btype[2].checked=true;we_cmd('del_all_cats')");
		$addbut = we_button::create_button("add", "javascript:document.we_form.btype[2].checked=true;we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_cat\\',top.allIDs);')", false, 100, 22, "", "", (!we_hasPerm("EDIT_KATEGORIE")));
		$butTable = we_button::create_button_table(array($delallbut, $addbut));
		$upperTable = '<table border="0" cellpadding="0" cellspacing="0" width="495"><tr><td align="left">' . $catAndCheck . '</td><td align="right">' . $butTable . '</td></tr></table>';

		$cats = new MultiDirChooser(495, $categories, "del_cat", $upperTable, "", "Icon,Path", CATEGORY_TABLE);
		return g_l('global', "[categorys]") . "<br>" . we_html_tools::getPixel(1, 3) . "<br>" . $cats->get();
	}

	/**
	 * returns HTML for the doctypes form
	 *
	 * @return string
	 * @param string $doctypes csv value with doctype IDs
	 */
	static function formDoctypes($doctypes){

		$GLOBALS['DB_WE']->query("SELECT ID,DocType FROM " . DOC_TYPES_TABLE . " Order By DocType");
		$DTselect = g_l('global', "[doctypes]") . "<br>" . we_html_tools::getPixel(1, 3) . "<br>" . '<select class="defaultfont" name="doctypes[]" size="5" multiple style="width: 495px" onchange="document.we_form.btype[2].checked=true;">' . "\n";

		$doctypesArray = makeArrayFromCSV($doctypes);
		while($GLOBALS['DB_WE']->next_record()) {
			$DTselect .= '<option value="' . $GLOBALS['DB_WE']->f("ID") . '"' . (in_array($GLOBALS['DB_WE']->f("ID"), $doctypesArray) ? " selected" : "") . '>' . $GLOBALS['DB_WE']->f("DocType") . "</option>\n";
		}
		$DTselect .= "</select>\n";
		return $DTselect;
	}

	/**
	 * returns HTML for the directories form
	 *
	 * @return string
	 * @param string $folders csv value with directory IDs
	 * @param boolean $thumnailpage if it should displayed in the thumbnails page or on an other page
	 */
	static function formFolders($folders, $thumnailpage = false, $width = "495"){
		$delallbut = we_button::create_button("delete_all", "javascript:" . ($thumnailpage ? "" : "document.we_form.btype[2].checked=true;") . "we_cmd('del_all_folders')");
		$wecmdenc3 = we_cmd_enc("fillIDs();opener.we_cmd('add_folder',top.allIDs);");
		$addbut = we_button::create_button("add", "javascript:" . ($thumnailpage ? "" : "document.we_form.btype[2].checked=true;") . "we_cmd('openDirselector','','" . FILE_TABLE . "','','','" . $wecmdenc3 . "','','','',1)");

		$dirs = new MultiDirChooser($width, $folders, "del_folder", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", FILE_TABLE);

		return ($thumnailpage ? g_l('rebuild', "[thumbdirs]") : g_l('rebuild', "[dirs]")) . "<br>" . we_html_tools::getPixel(1, 3) . "<br>" . $dirs->get();
	}

	/**
	 * returns HTML for the thumbnails form
	 *
	 * @return string
	 * @param string $thumbs csv value with thumb IDs
	 */
	static function formThumbs($thumbs){
		$GLOBALS['DB_WE']->query("SELECT ID,Name FROM " . THUMBNAILS_TABLE . " Order By Name");
		$Thselect = g_l('rebuild', "[thumbnails]") . "<br>" . we_html_tools::getPixel(1, 3) . "<br>" .
			'<select class="defaultfont" name="thumbs[]" size="10" multiple style="width: 520px">' . "\n";

		$thumbsArray = makeArrayFromCSV($thumbs);
		while($GLOBALS['DB_WE']->next_record()) {
			$Thselect .= '<option value="' . $GLOBALS['DB_WE']->f("ID") . '"' . (in_array($GLOBALS['DB_WE']->f("ID"), $thumbsArray) ? " selected" : "") . '>' . $GLOBALS['DB_WE']->f("Name") . "</option>\n";
		}
		$Thselect .= "</select>\n";
		return $Thselect;
	}

	static function formMetadata($metaFields, $onlyEmpty){
		$metaDataFields = weMetaData::getDefinedMetaDataFields();

		$_html = we_html_element::jsElement('document._errorMessage=' . (!empty($metaFields) ? '""' : '"' . addslashes(g_l('rebuild', "[noFieldsChecked]")) . '"'));
		$_html .= we_html_tools::htmlAlertAttentionBox(g_l('rebuild', "[expl_rebuild_metadata]"), 2, 520);
		$_html .= '<div class="defaultfont" style="margin:10px 0 5px 0;">' . g_l('rebuild', "[metadata]") . ':</div>' . "\n";

		$selAllBut = we_button::create_button("selectAll", "javascript:we_cmd('select_all_fields');");
		$deselAllBut = we_button::create_button("deselectAll", "javascript:we_cmd('deselect_all_fields');");

		foreach($metaDataFields as $md){
			if($md['importFrom']){
				$checked = isset($metaFields[$md['tag']]) && $metaFields[$md['tag']];
				$_html .= we_forms::checkbox(1, $checked, "_field[" . $md['tag'] . "]", $md['tag'], false, "defaultfont", "checkForError()");
			}
		}

		$_html .= we_button::create_button_table(
				array(
				$selAllBut,
				$deselAllBut
				), 10, array('style' => 'margin:10px 0 20px 0;')
		);

		$_html .= we_forms::checkbox(1, $onlyEmpty, 'onlyEmpty', g_l('rebuild', "[onlyEmpty]"));


		return $_html;
	}

	/**
	 * returns Array with javascript (array[0]) and HTML Content (array[1]) for the rebuild document page
	 *
	 * @return array
	 */
	static function getRebuildDocuments(){

		$thumbsFolders = isset($_REQUEST["thumbsFolders"]) ? $_REQUEST["thumbsFolders"] : "";
		$metaFolders = isset($_REQUEST["metaFolders"]) ? $_REQUEST["metaFolders"] : "";
		$metaFields = isset($_REQUEST["_field"]) ? $_REQUEST["_field"] : '';
		$thumbs = (isset($_REQUEST["thumbs"]) && is_array($_REQUEST["thumbs"])) ? makeCSVFromArray($_REQUEST["thumbs"], true) : "";
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "rebuild_documents";
		$btype = isset($_REQUEST["btype"]) ? $_REQUEST["btype"] : "rebuild_all";
		$categories = isset($_REQUEST["categories"]) ? $_REQUEST["categories"] : "";
		$doctypes = (isset($_REQUEST["doctypes"]) && is_array($_REQUEST["doctypes"])) ? makeCSVFromArray($_REQUEST["doctypes"], true) : "";
		$folders = isset($_REQUEST["folders"]) ? $_REQUEST["folders"] : "";
		$maintable = isset($_REQUEST["maintable"]) ? $_REQUEST["maintable"] : 0;
		$tmptable = isset($_REQUEST["tmptable"]) ? $_REQUEST["tmptable"] : 0;
		$catAnd = isset($_REQUEST["catAnd"]) ? $_REQUEST["catAnd"] : 0;
		$onlyEmpty = isset($_REQUEST["onlyEmpty"]) ? $_REQUEST["onlyEmpty"] : 0;


		$ws = get_ws(FILE_TABLE, true);
		if($ws && strpos($ws, (",0,")) !== true && ($folders == "" || $folders == "0")){
			$folders = get_def_ws(FILE_TABLE);
		}

		$all_content = ($_SESSION["perms"]["ADMINISTRATOR"] ?
				we_forms::checkbox("1", $maintable, "maintable", g_l('rebuild', "[rebuild_maintable]"), false, "defaultfont", "document.we_form.btype[0].checked=true;") .
				we_forms::checkbox("1", $tmptable, "tmptable", g_l('rebuild', "[rebuild_tmptable]"), false, "defaultfont", "document.we_form.btype[0].checked=true;") :
				'');

		$filter_content = we_rebuild_wizard::formCategory($categories, $catAnd) . '<br>' . we_html_tools::getPixel(2, 5) . "<br>" .
			we_rebuild_wizard::formDoctypes($doctypes) . '<br>' . we_html_tools::getPixel(2, 10) . "<br>" .
			we_rebuild_wizard::formFolders($folders);

		$filter_content = we_forms::radiobutton("rebuild_filter", ($btype == "rebuild_filter" && we_hasPerm("REBUILD_FILTERD") || ($btype == "rebuild_all" && (!we_hasPerm("REBUILD_ALL")) && we_hasPerm("REBUILD_FILTERD"))), "btype", g_l('rebuild', "[rebuild_filter]"), true, "defaultfont", "", (!we_hasPerm("REBUILD_FILTERD")), g_l('rebuild', "[txt_rebuild_filter]"), 0, 495, "", $filter_content);


		$parts = array(
			array(
				"headline" => "",
				"html" => we_forms::radiobutton("rebuild_all", ($btype == "rebuild_all" && we_hasPerm("REBUILD_ALL")), "btype", g_l('rebuild', "[rebuild_all]"), true, "defaultfont", "", (!we_hasPerm("REBUILD_ALL")), g_l('rebuild', "[txt_rebuild_all]"), 0, 495, "", $all_content),
				"space" => 0
			),
			array(
				"headline" => "",
				"html" => we_forms::radiobutton("rebuild_templates", ($btype == "rebuild_templates" && we_hasPerm("REBUILD_TEMPLATES")), "btype", g_l('rebuild', "[rebuild_templates]"), true, "defaultfont", "", (!we_hasPerm("REBUILD_TEMPLATES")), g_l('rebuild', "[txt_rebuild_templates]"), 0, 495),
				"space" => 0
			),
			array(
				"headline" => "",
				"html" => $filter_content,
				"space" => 0
			)
		);

		$thumbsHidden = '';
		$thumbsArray = makeArrayFromCSV($thumbs);
		foreach($thumbsArray as $i => $cur){
			$thumbsHidden .= we_html_element::htmlHidden(array("name" => 'thumbs[' . $i . ']', "value" => $cur));
		}

		$metaFieldsHidden = '';
		if(!empty($metaFields)){
			foreach($metaFields as $_key => $_val){
				$metaFieldsHidden .= we_html_element::htmlHidden(array("name" => '_field[' . $_key . ']', "value" => $_val));
			}
		}
		return array(we_rebuild_wizard::getPage2Js(), we_multiIconBox::getHTML("", "100%", $parts, 40, "", -1, "", "", false, g_l('rebuild', "[rebuild_documents]")) .
			$thumbsHidden .
			$metaFieldsHidden .
			we_html_element::htmlHidden(array("name" => "thumbsFolders", "value" => $thumbsFolders)) .
			we_html_element::htmlHidden(array("name" => "metaFolders", "value" => $metaFolders)) .
			we_html_element::htmlHidden(array("name" => "metaFields", "value" => $metaFields)) .
			we_html_element::htmlHidden(array("name" => "onlyEmpty", "value" => $onlyEmpty)) .
			we_html_element::htmlHidden(array("name" => "folders", "value" => $folders)) .
			we_html_element::htmlHidden(array("name" => "categories", "value" => $categories)) .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "rebuild")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "2")));
	}

	/**
	 * returns Array with javascript (array[0]) and HTML Content (array[1]) for the rebuild metadata page
	 *
	 * @return array
	 */
	static function getRebuildThumbnails(){

		$thumbsFolders = isset($_REQUEST["thumbsFolders"]) ? $_REQUEST["thumbsFolders"] : "";
		$metaFolders = isset($_REQUEST["metaFolders"]) ? $_REQUEST["metaFolders"] : "";
		$metaFields = isset($_REQUEST["_field"]) ? $_REQUEST["_field"] : array();
		$thumbs = (isset($_REQUEST["thumbs"]) && is_array($_REQUEST["thumbs"])) ? makeCSVFromArray($_REQUEST["thumbs"], true) : "";
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "rebuild_documents";
		$categories = isset($_REQUEST["categories"]) ? $_REQUEST["categories"] : "";
		$doctypes = (isset($_REQUEST["doctypes"]) && is_array($_REQUEST["doctypes"])) ? makeCSVFromArray($_REQUEST["doctypes"], true) : "";
		$folders = isset($_REQUEST["folders"]) ? $_REQUEST["folders"] : "";
		$catAnd = isset($_REQUEST["catAnd"]) ? $_REQUEST["catAnd"] : 0;
		$onlyEmpty = isset($_REQUEST["onlyEmpty"]) ? $_REQUEST["onlyEmpty"] : 0;

		$ws = get_ws(FILE_TABLE, true);

		// check if folers are in Workspace of User

		if($ws && $folders){
			$newFolders = array();
			$wsArray = makeArrayFromCSV($ws);
			$foldersArray = makeArrayFromCSV($folders);
			for($i = 0; $i < count($foldersArray); $i++){
				if(in_workspace($foldersArray[$i], $ws)){
					array_push($newFolders, $foldersArray[$i]);
				}
			}
			$folders = makeCSVFromArray($newFolders);
		}

		if($ws && strpos($ws, (",0,")) !== true && ($thumbsFolders == "" || $thumbsFolders == "0")){
			$thumbsFolders = get_def_ws(FILE_TABLE);
		}
		$parts = array();

		$content = we_rebuild_wizard::formThumbs($thumbs) .
			'<br>' . we_html_tools::getPixel(2, 15) . "<br>" .
			we_rebuild_wizard::formFolders($thumbsFolders, true, 520);



		array_push($parts, array(
			"headline" => "",
			"html" => $content,
			"space" => 0)
		);


		$dthidden = "";
		$doctypesArray = makeArrayFromCSV($doctypes);
		for($i = 0; $i < count($doctypesArray); $i++){
			$dthidden .= we_html_element::htmlHidden(array("name" => "doctypes[$i]", "value" => $doctypesArray[$i]));
		}
		$metaFieldsHidden = "";
		foreach($metaFields as $_key => $_val){
			$metaFieldsHidden .= we_html_element::htmlHidden(array("name" => "_field[$_key]", "value" => $_val));
		}
		return array(we_rebuild_wizard::getPage2Js("thumbsFolders"), we_multiIconBox::getHTML("", "100%", $parts, 40, "", -1, "", "", false, g_l('rebuild', "[rebuild_thumbnails]")) .
			$dthidden .
			$metaFieldsHidden .
			we_html_element::htmlHidden(array("name" => "catAnd", "value" => $catAnd)) .
			we_html_element::htmlHidden(array("name" => "thumbsFolders", "value" => $thumbsFolders)) .
			we_html_element::htmlHidden(array("name" => "metaFolders", "value" => $metaFolders)) .
			we_html_element::htmlHidden(array("name" => "metaFields", "value" => $metaFields)) .
			we_html_element::htmlHidden(array("name" => "onlyEmpty", "value" => $onlyEmpty)) .
			we_html_element::htmlHidden(array("name" => "folders", "value" => $folders)) .
			we_html_element::htmlHidden(array("name" => "categories", "value" => $categories)) .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "rebuild")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "2")));
	}

	static function getRebuildMetadata(){

		$thumbsFolders = isset($_REQUEST["thumbsFolders"]) ? $_REQUEST["thumbsFolders"] : "";
		$metaFolders = isset($_REQUEST["metaFolders"]) ? $_REQUEST["metaFolders"] : "";
		$onlyEmpty = isset($_REQUEST["onlyEmpty"]) ? $_REQUEST["onlyEmpty"] : 0;
		$metaFields = isset($_REQUEST["_field"]) ? $_REQUEST["_field"] : array();
		$thumbs = (isset($_REQUEST["thumbs"]) && is_array($_REQUEST["thumbs"])) ? makeCSVFromArray($_REQUEST["thumbs"], true) : "";
		$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "rebuild_documents";
		$categories = isset($_REQUEST["categories"]) ? $_REQUEST["categories"] : "";
		$doctypes = (isset($_REQUEST["doctypes"]) && is_array($_REQUEST["doctypes"])) ? makeCSVFromArray($_REQUEST["doctypes"], true) : "";
		$folders = isset($_REQUEST["folders"]) ? $_REQUEST["folders"] : "";
		$maintable = isset($_REQUEST["maintable"]) ? $_REQUEST["maintable"] : 0;
		$catAnd = isset($_REQUEST["catAnd"]) ? $_REQUEST["catAnd"] : 0;

		$ws = get_ws(FILE_TABLE, true);

		// check if folers are in Workspace of User

		if($ws && $folders){
			$newFolders = array();
			$wsArray = makeArrayFromCSV($ws);
			$foldersArray = makeArrayFromCSV($folders);
			for($i = 0; $i < count($foldersArray); $i++){
				if(in_workspace($foldersArray[$i], $ws)){
					array_push($newFolders, $foldersArray[$i]);
				}
			}
			$folders = makeCSVFromArray($newFolders);
		}

		if($ws && strpos($ws, (",0,")) !== true && ($metaFolders == "" || $metaFolders == "0")){
			$metaFolders = get_def_ws(FILE_TABLE);
		}
		$parts = array();

		$content = we_rebuild_wizard::formMetadata($metaFields, $onlyEmpty) .
			'<br>' . we_html_tools::getPixel(2, 15) . "<br>" .
			we_rebuild_wizard::formFolders($metaFolders, true, 520);



		array_push($parts, array(
			"headline" => "",
			"html" => $content,
			"space" => 0)
		);


		$dthidden = "";
		$doctypesArray = makeArrayFromCSV($doctypes);
		for($i = 0; $i < count($doctypesArray); $i++){
			$dthidden .= we_html_element::htmlHidden(array("name" => "doctypes[$i]", "value" => $doctypesArray[$i]));
		}
		$thumbsHidden = "";
		$thumbsArray = makeArrayFromCSV($thumbs);
		for($i = 0; $i < count($thumbsArray); $i++){
			$thumbsHidden .= we_html_element::htmlHidden(array("name" => "thumbs[$i]", "value" => $thumbsArray[$i]));
		}
		return array(we_rebuild_wizard::getPage2Js("metaFolders"), we_multiIconBox::getHTML("", "100%", $parts, 40, "", -1, "", "", false, g_l('rebuild', "[rebuild_metadata]")) .
			$dthidden .
			$thumbsHidden .
			we_html_element::htmlHidden(array("name" => "catAnd", "value" => $catAnd)) .
			we_html_element::htmlHidden(array("name" => "metaFolders", "value" => $metaFolders)) .
			we_html_element::htmlHidden(array("name" => "thumbsFolders", "value" => $thumbsFolders)) .
			we_html_element::htmlHidden(array("name" => "folders", "value" => $folders)) .
			we_html_element::htmlHidden(array("name" => "categories", "value" => $categories)) .
			we_html_element::htmlHidden(array("name" => "fr", "value" => "body")) .
			we_html_element::htmlHidden(array("name" => "type", "value" => $type)) .
			we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "rebuild")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "2")));
	}

	/**
	 * returns HTML for the frameset
	 *
	 * @return string
	 */
	static function getFrameset(){
		$tail = "";
		if(isset($_REQUEST["btype"])){
			$tail .= "&amp;btype=" . rawurlencode($_REQUEST["btype"]);
		}
		if(isset($_REQUEST["type"])){
			$tail .= "&amp;type=" . rawurlencode($_REQUEST["type"]);
		}
		if(isset($_REQUEST["templateID"])){
			$tail .= "&amp;templateID=" . rawurlencode($_REQUEST["templateID"]);
		}
		if(isset($_REQUEST["step"])){
			$tail .= "&amp;step=" . rawurlencode($_REQUEST["step"]);
		}
		if(isset($_REQUEST["responseText"])){
			$tail .= "&amp;responseText=" . rawurlencode($_REQUEST["responseText"]);
		}



		$taskname = md5(session_id() . "_rebuild");
		$taskFilename = WE_FRAGMENT_PATH . $taskname;
		if(file_exists($taskFilename)){
			@unlink($taskFilename);
		}

		$cmdFrameHeight = (isset($_SESSION["prefs"]["debug_normal"]) && $_SESSION["prefs"]["debug_normal"] != 0) ? 30 : 0;

		if($tail){
			$fst = new we_html_frameset(array(
					"rows" => "*,$cmdFrameHeight",
					"framespacing" => 0,
					"border" => 0,
					"frameborder" => "no")
			);

			$fst->addFrame(array("src" => WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=busy&amp;dc=1", "name" => "wizbusy"));
			$fst->setFrameAttributes(0, array("scrolling" => "no", "onload" => "wizcmd.location='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=body" . $tail . "';"));

			$fst->addFrame(array("src" => HTML_DIR . "white.html", "name" => "wizcmd"));
			$fst->setFrameAttributes(1, array("scrolling" => "no"));
		} else{
			$fst = new we_html_frameset(array(
					"rows" => '*,' . (we_base_browserDetect::isFF() ? 60 : 40) . ',' . $cmdFrameHeight,
					"framespacing" => 0,
					"border" => 0,
					"frameborder" => "no")
			);

			$fst->addFrame(array("src" => WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=body", "name" => "wizbody"));
			$fst->setFrameAttributes(0, array("scrolling" => "auto"));

			$fst->addFrame(array("src" => WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=busy", "name" => "wizbusy"));
			$fst->setFrameAttributes(1, array("scrolling" => "no"));

			$fst->addFrame(array("src" => WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=rebuild&amp;fr=cmd", "name" => "wizcmd"));
			$fst->setFrameAttributes(2, array("scrolling" => "no"));
		}


		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead(g_l('rebuild', "[rebuild]"))
				) . $fst->getHtml());
	}

	/**
	 * returns Javascript for step 2 (1)
	 *
	 * @return string
	 * @param string $folders csv value with directory IDs
	 */
	static function getPage2Js($folders = "folders"){
		return
			'function handle_event(what){
				f = document.we_form;
				switch(what){
					case "previous":
						f.step.value=0;
						f.target="wizbody";
						break;
					case "next":
						if (typeof(document._errorMessage) != "undefined" && document._errorMessage !== ""){
							' . we_message_reporting::getShowMessageCall(g_l('rebuild', "[noFieldsChecked]"), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						} else {
							top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "disabled");
							top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "disabled");
							top.frames["wizbusy"].showRefreshButton();
							f.step.value=2;
							f.target="wizcmd";
						}
						break;
				}
				f.submit();
			}
			function we_cmd() {
				f = document.we_form;
				var args = "";
				var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				switch (arguments[0]) {
				case "openDirselector":
					new jsWindow(url,"we_fileselector",-1,-1,' . WINDOW_DIRSELECTOR_WIDTH . ',' . WINDOW_DIRSELECTOR_HEIGHT . ',true,true,true,true);
					break;
				case "openCatselector":
					new jsWindow(url,"we_catselector",-1,-1,' . WINDOW_CATSELECTOR_WIDTH . ',' . WINDOW_CATSELECTOR_HEIGHT . ',true,true,true,true);
					break;
				case "add_cat":
					var catsToAdd = makeArrayFromCSV(arguments[1]);
					var cats = makeArrayFromCSV(f.categories.value);
					for(var i=0;i<catsToAdd.length;i++){
						if(!inArray(catsToAdd[i],cats)){
							cats.push(catsToAdd[i]);
						};
					};
					f.categories.value = makeCSVFromArray(cats);
					f.step.value=1;
					f.submit();
					break;
				case "del_cat":
					var catToDel = arguments[1];
					var cats = makeArrayFromCSV(f.categories.value);
					var newcats = new Array();
					for(var i=0;i<cats.length;i++){
						if(cats[i] != catToDel){
							newcats.push(cats[i]);
						};
					};
					f.categories.value = makeCSVFromArray(newcats);
					f.step.value=1;
					f.submit();
					break;
				case "del_all_cats":
					f.categories.value = "";
					f.step.value=1;
					f.submit();
					break;
				case "add_folder":
					var foldersToAdd = makeArrayFromCSV(arguments[1]);
					var folders = makeArrayFromCSV(f.' . $folders . '.value);
					for(var i=0;i<foldersToAdd.length;i++){
						if(!inArray(foldersToAdd[i],folders)){
							folders.push(foldersToAdd[i]);
						};
					};
					f.' . $folders . '.value = makeCSVFromArray(folders);
					f.step.value=1;
					f.submit();
					break;
				case "del_folder":
					var folderToDel = arguments[1];
					var folders = makeArrayFromCSV(f.' . $folders . '.value);
					var newfolders = new Array();
					for(var i=0;i<folders.length;i++){
						if(folders[i] != folderToDel){
							newfolders.push(folders[i]);
						};
					};
					f.' . $folders . '.value = makeCSVFromArray(newfolders);
					f.step.value=1;
					f.submit();
					break;
				case "del_all_folders":
					f.' . $folders . '.value = "";
					f.step.value=1;
					f.submit();
					break;
				case "deselect_all_fields":
					var _elem = document.we_form.elements;
					var _elemLength = _elem.length;
					for (var i=0; i<_elemLength; i++) {
						if (_elem[i].name.substring(0,7) == "_field[") {
							_elem[i].checked = false;
						}
					}
					document._errorMessage = "' . addslashes(g_l('rebuild', "[noFieldsChecked]")) . '";
					break;
				case "select_all_fields":
					var _elem = document.we_form.elements;
					var _elemLength = _elem.length;
					for (var i=0; i<_elemLength; i++) {
						if (_elem[i].name.substring(0,7) == "_field[") {
							_elem[i].checked = true;
						}
					}
					document._errorMessage = "";
					break;
				default:
					for(var i = 0; i < arguments.length; i++) {
						args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
					}
					eval("opener.top.we_cmd("+args+")");
				}
			}
			function checkForError() {
				var _elem = document.we_form.elements;
				var _elemLength = _elem.length;
				var _fieldsChecked = false;
				for (var i=0; i<_elemLength; i++) {
					if (_elem[i].name.substring(0,7) == "_field[") {
						if(_elem[i].checked){
							_fieldsChecked=true;break;
						}
					}
				}
				if (_fieldsChecked === false) {
					document._errorMessage = "' . addslashes(g_l('rebuild', "[noFieldsChecked]")) . '";
				} else {
					document._errorMessage = "";
				}
			}
			function makeArrayFromCSV(csv) {
				if(csv.length && csv.substring(0,1)==","){csv=csv.substring(1,csv.length);}
				if(csv.length && csv.substring(csv.length-1,csv.length)==","){csv=csv.substring(0,csv.length-1);}
				if(csv.length==0){return new Array();}else{return csv.split(/,/);};
			}
			function inArray(needle,haystack){
				for(var i=0;i<haystack.length;i++){
					if(haystack[i] == needle){return true;}
				}
				return false;
			}
			function makeCSVFromArray(arr) {
				if(arr.length == 0){return "";};
				return ","+arr.join(",")+",";
			}
			function set_button_state() {
				if(top.frames["wizbusy"] && top.frames["wizbusy"].switch_button_state){
					top.frames["wizbusy"].back_enabled = top.frames["wizbusy"].switch_button_state("back", "back_enabled", "enabled");
					top.frames["wizbusy"].next_enabled = top.frames["wizbusy"].switch_button_state("next", "next_enabled", "enabled");
				}else{
					setTimeout("set_button_state()",300);
				}
			}
			set_button_state();';
	}

	/**
	 * returns Javascript for step 2 (1)
	 *
	 * @return string
	 * @param array first element (array[0]) must be a javascript, second element (array[1]) must be the Body HTML
	 */
	static function getPage($contents){
		if(empty($contents)){
			return '';
		}
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(
				we_html_element::htmlHead(
					we_html_tools::getHtmlInnerHead(g_l('rebuild', "[rebuild]")) .
					STYLESHEET .
					($contents[0] ?
						we_html_element::jsScript(JS_DIR . 'windows.js') .
						we_html_element::jsElement($contents[0]) : '')) .
				we_html_element::htmlBody(array(
					"class" => "weDialogBody"
					), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "action" => WEBEDITION_DIR . "we_cmd.php"), $contents[1])
				)
		);
	}

}


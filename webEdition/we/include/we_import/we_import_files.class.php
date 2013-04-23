<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
class we_import_files{

	var $importToID = 0;
	var $step = 0;
	var $sameName = "overwrite";
	var $importMetadata = true;
	var $cmd = "";
	var $thumbs = "";
	var $width = "";
	var $height = "";
	var $widthSelect = "pixel";
	var $heightSelect = "pixel";
	var $keepRatio = 1;
	var $quality = 8;
	var $degrees = 0;
	var $categories = '';

	function __construct(){

		if(isset($_REQUEST['categories'])){
			$_catarray = makeArrayFromCSV($_REQUEST['categories']);
			$_cats = array();
			foreach($_catarray as $_cat){
				// bugfix Workarround #700
				if(is_numeric($_cat)){
					$_cats[] = $_cat;
				} else{
					$_cats[] = path_to_id($_cat, CATEGORY_TABLE);
				}
			}
			$_REQUEST['categories'] = makeCSVFromArray($_cats);
		}

		$this->categories = isset($_REQUEST["categories"]) ? $_REQUEST["categories"] : $this->categories;
		$this->importToID = isset($_REQUEST["importToID"]) ? $_REQUEST["importToID"] : $this->importToID;
		$this->sameName = isset($_REQUEST["sameName"]) ? $_REQUEST["sameName"] : $this->sameName;
		$this->importMetadata = isset($_REQUEST["importMetadata"]) ? $_REQUEST["importMetadata"] : $this->importMetadata;
		$this->step = isset($_REQUEST["step"]) ? $_REQUEST["step"] : $this->step;
		$this->cmd = isset($_REQUEST["cmd"]) ? $_REQUEST["cmd"] : $this->cmd;
		$this->thumbs = isset($_REQUEST["thumbs"]) ? $_REQUEST["thumbs"] : $this->thumbs;
		$this->width = isset($_REQUEST["width"]) ? $_REQUEST["width"] : $this->width;
		$this->height = isset($_REQUEST["height"]) ? $_REQUEST["height"] : $this->height;
		$this->widthSelect = isset($_REQUEST["widthSelect"]) ? $_REQUEST["widthSelect"] : $this->widthSelect;
		$this->heightSelect = isset($_REQUEST["heightSelect"]) ? $_REQUEST["heightSelect"] : $this->heightSelect;
		$this->keepRatio = isset($_REQUEST["keepRatio"]) ? $_REQUEST["keepRatio"] : $this->keepRatio;
		$this->quality = isset($_REQUEST["quality"]) ? $_REQUEST["quality"] : $this->quality;
		$this->degrees = isset($_REQUEST["degrees"]) ? $_REQUEST["degrees"] : $this->degrees;
	}

	function getHTML(){
		switch($this->cmd){
			case "content" :
				return $this->_getContent();
				break;
			case "buttons" :
				return $this->_getButtons();
				break;
			default :
				return $this->_getFrameset();
		}
	}

	function _getJS($fileinput){
		return we_html_element::jsElement("
				function makeArrayFromCSV(csv) {
					if(csv.length && csv.substring(0,1)==\",\"){csv=csv.substring(1,csv.length);}
					if(csv.length && csv.substring(csv.length-1,csv.length)==\",\"){csv=csv.substring(0,csv.length-1);}
					if(csv.length==0){return new Array();}else{return csv.split(/,/);};
				}

				function inArray(needle,haystack){
					for(var i=0;i<haystack.length;i++){
						if(haystack[i] == needle){return true;}
					}
					return false;
				}

				function makeCSVFromArray(arr) {
					if(arr.length == 0){return \"\";};
					return \",\"+arr.join(\",\")+\",\";
				}
				function we_cmd(){
					var args = '';
					var url = '" . WEBEDITION_DIR . "we_cmd.php?'; for(var i = 0; i < arguments.length; i++){ url += 'we_cmd['+i+']='+escape(arguments[i]); if(i < (arguments.length - 1)){ url += '&'; }}

					switch (arguments[0]){
						case 'openDirselector':
							new jsWindow(url,'we_fileselector',-1,-1," . WINDOW_DIRSELECTOR_WIDTH . "," . WINDOW_DIRSELECTOR_HEIGHT . ",true,true,true,true);
							break;
						case 'openCatselector':
							new jsWindow(url,'we_catselector',-1,-1," . WINDOW_CATSELECTOR_WIDTH . "," . WINDOW_CATSELECTOR_HEIGHT . ",true,true,true,true);
						break;
					}
				}" . 'var we_fileinput = \'<form name="we_upload_form_WEFORMNUM" method="post" action="' . WEBEDITION_DIR . 'we_cmd.php" enctype="multipart/form-data" target="imgimportbuttons">' . str_replace("\n", " ", str_replace("\r", " ", $this->_getHiddens("buttons", $this->step + 1))) . $fileinput . '</form>\';
				function checkFileinput(){
					var prefix =  "trash_";
					var imgs = document.getElementsByTagName("IMG");
					if(document.forms[document.forms.length-1].name.substring(0,14) == "we_upload_form" && document.forms[document.forms.length-1].elements["we_File"].value){
						for(var i = 0; i<imgs.length; i++){
							if(imgs[i].id.length > prefix.length && imgs[i].id.substring(0,prefix.length) == prefix){
									imgs[i].style.display="";
							}
						}
						//weAppendMultiboxRow(we_fileinput.replace(/WEFORMNUM/g,weGetLastMultiboxNr()),\'' . g_l('importFiles', "[file]") . '\' + \' \' + (parseInt(weGetMultiboxLength())),80,1);
						var fi = we_fileinput.replace(/WEFORMNUM/g,weGetLastMultiboxNr());
						fi = fi.replace(/WE_FORM_NUM/g,(document.forms.length));
						weAppendMultiboxRow(fi,"",0,1);
						window.scrollTo(0,1000000);
					}
				}

				function we_trashButDown(but){
					if(but.src.indexOf("disabled") == -1){
						but.src = "' . BUTTONS_DIR . 'btn_function_trash_down.gif";
					}
				}
				function we_trashButUp(but){
					if(but.src.indexOf("disabled") == -1){
						but.src = "' . BUTTONS_DIR . 'btn_function_trash.gif";
					}
				}

				function wedelRow(nr,but){
					if(but.src.indexOf("disabled") == -1){
						var prefix =  "div_uploadFiles_";
						var num = -1;
						var z = 0;
						weDelMultiboxRow(nr);
						var divs = document.getElementsByTagName("DIV");
						for(var i = 0; i<divs.length; i++){
							if(divs[i].id.length > prefix.length && divs[i].id.substring(0,prefix.length) == prefix){
								num = divs[i].id.substring(prefix.length,divs[i].id.length);
								if(parseInt(num)){
									var sp = document.getElementById("headline_uploadFiles_"+(num-1));
									if(sp){
										sp.innerHTML = z;
									}
								}
								z++;
							}
						}
					}
				}

function checkButtons(){
	try{
		if(typeof(document.JUpload)=="undefined"||(typeof(document.JUpload.isActive)!="function")||document.JUpload.isActive()==false){
			checkFileinput();
			window.setTimeout("checkButtons()",1000);
			//recheck
		}else{
			setApplet();
		}
	}catch(e){
		checkFileinput();
		window.setTimeout("checkButtons()",1000);
	}
}

				function setApplet() {

					var descDiv = document.getElementById("desc");
					if(descDiv.style.display!="none"){
						var descJUDiv = document.getElementById("descJupload");
						var buttDiv = top.imgimportbuttons.document.getElementById("normButton");
						var buttJUDiv = top.imgimportbuttons.document.getElementById("juButton");

						descDiv.style.display="none";
						buttDiv.style.display="none";
						descJUDiv.style.display="block";
						buttJUDiv.style.display="block";
					}

					//setTimeout("document.JUpload.jsRegisterUploaded(\"refreshTree\");",3000);
				}

				function refreshTree() {
					//FIXME: this won\'t work in current version
					top.opener.top.we_cmd("load","' . FILE_TABLE . '");
				}

				function uploadFinished() {
					refreshTree();
					' . we_message_reporting::getShowMessageCall(
					g_l('importFiles', "[finished]"), we_message_reporting::WE_MESSAGE_NOTICE) . '
				}') . we_html_element::jsScript(JS_DIR . "windows.js");
	}

	function _getContent(){
		$_funct = isset($_REQUEST['step']) ? $_REQUEST['step'] : 1;
		$_funct = 'getStep' . $_funct;

		return $this->$_funct();
	}

	function getStep1(){
		$yuiSuggest = & weSuggest::getInstance();
		$this->loadPropsFromSession();
		unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);

		// create Start Screen ##############################################################################

		$wsA = makeArrayFromCSV(get_def_ws());
		$ws = empty($wsA) ? 0 : $wsA[0];
		$store_id = $this->importToID ? $this->importToID : $ws;

		$path = id_to_path($store_id);
		$wecmdenc1 = we_cmd_enc("document.we_startform.importToID.value");
		$wecmdenc2 = we_cmd_enc("document.we_startform.egal.value");
		$button = we_button::create_button("select", "javascript:we_cmd('openDirselector',document.we_startform.importToID.value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','0')");

		$yuiSuggest->setAcId("Dir");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("egal", $path);
		$yuiSuggest->setLabel(g_l('weClass', "[path]"));
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult("importToID", $store_id);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setWidth(260);
		$yuiSuggest->setSelectButton($button);

		$content = we_html_tools::hidden('we_cmd[0]', 'import_files') . we_html_tools::hidden('cmd', 'content') . we_html_tools::hidden('step', '2') . // fix for categories require reload!
			we_html_element::htmlHidden(array('name' => 'categories', 'value' => '')) .
			$yuiSuggest->getHTML();

		$parts = array(array(
				"headline" => g_l('importFiles', "[destination_dir]"),
				"html" => $content,
				"space" => 150
		));

		$content = we_html_tools::htmlAlertAttentionBox(g_l('importFiles', "[sameName_expl]"), 2, 380) .
			we_html_tools::getPixel(200, 10) .
			we_forms::radiobutton("overwrite", ($this->sameName == "overwrite"), "sameName", g_l('importFiles', "[sameName_overwrite]")) .
			we_forms::radiobutton("rename", ($this->sameName == "rename"), "sameName", g_l('importFiles', "[sameName_rename]")) .
			we_forms::radiobutton("nothing", ($this->sameName == "nothing"), "sameName", g_l('importFiles', "[sameName_nothing]"));

		array_push(
			$parts, array(
			"headline" => g_l('importFiles', "[sameName_headline]"),
			"html" => $content,
			"space" => 150
		));

		// categoryselector


		if(we_hasPerm("EDIT_KATEGORIE")){

			array_push(
				$parts, array(
				'headline' => g_l('global', "[categorys]") . '',
				'html' => $this->getHTMLCategory(),
				'space' => 150
			));
		}

		if(we_hasPerm("NEW_GRAFIK")){
			$parts[] = array(
				'headline' => g_l('importFiles', "[metadata]") . '',
				'html' => we_forms::checkboxWithHidden(
					$this->importMetadata == true, 'importMetadata', g_l('importFiles', "[import_metadata]")),
				'space' => 150
			);

			if(we_image_edit::gd_version() > 0){
				$GLOBALS['DB_WE']->query("SELECT ID,Name FROM " . THUMBNAILS_TABLE . " Order By Name");
				$Thselect = g_l('importFiles', "[thumbnails]") . "<br>" . we_html_tools::getPixel(1, 3) . "<br>" . '<select class="defaultfont" name="thumbs_tmp" size="5" multiple style="width: 260px" onchange="this.form.thumbs.value=\'\';for(var i=0;i<this.options.length;i++){if(this.options[i].selected){this.form.thumbs.value +=(this.options[i].value+\',\');}};this.form.thumbs.value=this.form.thumbs.value.replace(/^(.+),$/,\'$1\');">' . "\n";

				$thumbsArray = makeArrayFromCSV($this->thumbs);
				while($GLOBALS['DB_WE']->next_record()) {
					$Thselect .= '<option value="' . $GLOBALS['DB_WE']->f("ID") . '"' . (in_array(
							$GLOBALS['DB_WE']->f("ID"), $thumbsArray) ? " selected" : "") . '>' . $GLOBALS['DB_WE']->f("Name") . "</option>\n";
				}
				$Thselect .= "</select>\n" . '<input type="hidden" name="thumbs" value="' . $this->thumbs . '" />' . "\n";

				$parts[] = array(
					"headline" => g_l('importFiles', "[make_thumbs]"),
					"html" => $Thselect,
					"space" => 150
				);

				$widthInput = we_html_tools::htmlTextInput("width", "10", $this->width, "", '', "text", 60);
				$heightInput = we_html_tools::htmlTextInput("height", "10", $this->height, "", '', "text", 60);

				$widthSelect = '<select size="1" class="weSelect" name="widthSelect"><option value="pixel"' . (($this->widthSelect == "pixel") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[pixel]") . '</option><option value="percent"' . (($this->widthSelect == "percent") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[percent]") . '</option></select>';
				$heightSelect = '<select size="1" class="weSelect" name="heightSelect"><option value="pixel"' . (($this->heightSelect == "pixel") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[pixel]") . '</option><option value="percent"' . (($this->heightSelect == "percent") ? ' selected="selected"' : '') . '>' . g_l('weClass', "[percent]") . '</option></select>';

				$ratio_checkbox = we_forms::checkbox(
						"1", $this->keepRatio, "keepRatio", g_l('thumbnails', "[ratio]"));

				$_resize = '<table border="0" cellpadding="2" cellspacing="0">
				<tr>
					<td class="defaultfont">' . g_l('weClass', "[width]") . ':</td>
					<td>' . $widthInput . '</td>
					<td>' . $widthSelect . '</td>
				</tr>
				<tr>
					<td class="defaultfont">' . g_l('weClass', "[height]") . ':</td>
					<td>' . $heightInput . '</td>
					<td>' . $heightSelect . '</td>
				</tr>
				<tr>
					<td colspan="3">' . $ratio_checkbox . '</td>
				</tr>
			</table>';

				$parts[] = array("headline" => g_l('weClass', "[resize]"), "html" => $_resize, "space" => 150);

				$_radio0 = we_forms::radiobutton(
						"0", $this->degrees == 0, "degrees", g_l('weClass', "[rotate0]"));
				$_radio180 = we_forms::radiobutton(
						"180", $this->degrees == 180, "degrees", g_l('weClass', "[rotate180]"));
				$_radio90l = we_forms::radiobutton(
						"90", $this->degrees == 90, "degrees", g_l('weClass', "[rotate90l]"));
				$_radio90r = we_forms::radiobutton(
						"270", $this->degrees == 270, "degrees", g_l('weClass', "[rotate90r]"));

				$parts[] = array(
					"headline" => g_l('weClass', "[rotate]"),
					"html" => $_radio0 . $_radio180 . $_radio90l . $_radio90r,
					"space" => 150
				);

				$parts[] = array(
					"headline" => g_l('weClass', "[quality]"),
					"html" => we_image_edit::qualitySelect("quality", $this->quality),
					"space" => 150
				);
			} else{
				$parts[] = array(
					"headline" => "",
					"html" => we_html_tools::htmlAlertAttentionBox(
						g_l('importFiles', "[add_description_nogdlib]"), 2, ""),
					"space" => 0
				);
			}
			$foldAt = 3;
		} else{
			$foldAt = -1;
		}
		$wepos = weGetCookieVariable("but_weimportfiles");
		$content = we_multiIconBox::getJS() .
			we_multiIconBox::getHTML(
				"weimportfiles", "99%", $parts, 30, "", $foldAt, g_l('importFiles', "[image_options_open]"), g_l('importFiles', "[image_options_close]"), ($wepos == "down"), g_l('importFiles', "[step1]"));
		$startsrceen = we_html_element::htmlDiv(
				array(
				"id" => "start"
				), we_html_element::htmlForm(
					array(
					"action" => WEBEDITION_DIR . "we_cmd.php",
					//"action"=>WEBEDITION_DIR."we/include/we_import_files.inc.php",
					"name" => "we_startform",
					"method" => "post"
					), $content));

		$body = we_html_element::htmlBody(array(
				"class" => "weDialogBody"
				), $startsrceen . $yuiSuggest->getYuiCss() . $yuiSuggest->getYuiJs());

		return $this->_getHtmlPage($body, $this->_getJS(''));
	}

	function getStep2(){

		$this->savePropsInSession();

		// create Second Screen ##############################################################################
		$but = we_html_tools::getPixel(10, 22) .
			we_html_element::htmlImg(
				array(
					"src" => IMAGE_DIR . 'button/btn_function_trash.gif',
					"width" => "27",
					"height" => "22",
					"border" => "0",
					"align" => "absmiddle",
					"onMouseDown" => "we_trashButDown(this)",
					"onMouseUp" => "we_trashButUp(this)",
					"onMouseOut" => "we_trashButUp(this)",
					"style" => "display: none;cursor:pointer;",
					"id" => "trash_WEFORMNUM",
					"onclick" => "wedelRow(WEFORMNUM + 1,this)"
		));
		$but = str_replace("\n", " ", str_replace("\r", " ", $but));

		$maxsize = getUploadMaxFilesize(false, $GLOBALS['DB_WE']);
		$maxsize = round($maxsize / (1024 * 1024), 3) . 'MB';

		$content = we_html_tools::hidden('we_cmd[0]', 'import_files') .
			we_html_tools::hidden('cmd', 'content') . we_html_tools::hidden('step', 2) .
			we_html_element::htmlDiv(array('id' => 'desc'), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('importFiles', "[import_expl]"), $maxsize), 2, 520, false)) .
			we_html_element::htmlDiv(array('id' => 'descJupload', 'style' => 'display:none;'), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('importFiles', "[import_expl_jupload]"), $maxsize), 2, 520, false));

		$parts = array(
			array("headline" => "", "html" => $content, "space" => 0)
		);

		$fileinput = we_html_element::htmlInput(
				array(
					"name" => "we_File",
					"type" => "file",
					"size" => "40",
					"onclick" => "checkFileinput();",
					"onchange" => "checkFileinput();"
			)) . $but;

		$fileinput = '<table><tr><td valign="top" class="weMultiIconBoxHeadline">' . g_l('importFiles', "[file]") . '&nbsp;<span id="headline_uploadFiles_WEFORMNUM">WE_FORM_NUM</span></td><td>' . we_html_tools::getPixel(
				35, 5) . '</td><td>' . $fileinput . '</td></tr></table>';

		$form_content = str_replace("WEFORMNUM", "0", $this->_getHiddens("buttons", $this->step) . str_replace("WE_FORM_NUM", "1", $fileinput));
		$formhtml = we_html_element::htmlForm(
				array(
				"action" => WEBEDITION_DIR . "we_cmd.php",
				"name" => "we_upload_form_0",
				"method" => "post",
				"enctype" => "multipart/form-data",
				"target" => "imgimportbuttons"
				), $form_content);

		// JUpload part0


		if(getPref('use_jupload') && file_exists(WEBEDITION_PATH . 'jupload/jupload.jar')){
			$_weju = new weJUpload();
			$formhtml = $_weju->getAppletTag($formhtml, 530, 300);
		}


		$parts[] = array(
			"headline" => '', "html" => $formhtml, "space" => 0
		);

		$content = we_html_element::htmlDiv(
				array("id" => "forms", "style" => "display:block"), (getPref('use_jupload') && file_exists(WEBEDITION_PATH . 'jupload/jupload.jar') ? we_html_element::htmlForm(array(
						"name" => "JUploadForm"
						), '') : '') . we_html_element::htmlForm(
					array(
					"action" => WEBEDITION_DIR . "we_cmd.php",
					"name" => "we_startform",
					"method" => "post"
					), $this->_getHiddens()) .
				we_multiIconBox::getHTML("uploadFiles", "100%", $parts, 30, "", -1, "", "", "", g_l('importFiles', "[step2]"))
		);

		$body = we_html_element::htmlBody(
				array(
				"class" => "weDialogBody",
				//"onMouseMove" => "checkButtons();",
				"onload" => "checkButtons();"
				), $content);

		$js = $this->_getJS($fileinput) . we_multiIconBox::getDynJS("uploadFiles", "30");

		return $this->_getHtmlPage($body, $js);
	}

	function getStep3(){

		// create Second Screen ##############################################################################

		$parts = array();

		if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){

			$filelist = "";
			foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
				$filelist .= '- ' . $err["filename"] . ' => ' . $err['error'] . we_html_element::htmlBr();
			}
			unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);

			$parts[] = array(
				'html' => we_html_tools::htmlAlertAttentionBox(
					sprintf(str_replace('\n', '<br>', g_l('importFiles', '[error]')), $filelist), 1, "520", false)
			);
		} else{

			$parts[] = array(
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[finished]'), 2, "520", false)
			);
		}

		$content = we_html_element::htmlForm(
				array(
				"action" => WEBEDITION_DIR . "we_cmd.php", "name" => "we_startform", "method" => "post"
				), we_html_element::htmlHidden(array(
					'name' => 'step', 'value' => '3'
				)) . we_multiIconBox::getHTML(
					"uploadFiles", "100%", $parts, 30, "", -1, "", "", "", g_l('importFiles', "[step3]")))// bugfix 1001
		;

		$body = we_html_element::htmlBody(array(
				"class" => "weDialogBody"
				), $content);
		return $this->_getHtmlPage($body);
	}

	function _getButtons(){

		$bodyAttribs = array("class" => "weDialogButtonsBody");

		if($this->step == 1){
			$bodyAttribs["onload"] = "next();";
			$error = $this->importFile();
			if(!empty($error)){
				if(!isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'])){
					$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] = array();
				}
				$_SESSION['weS']['WE_IMPORT_FILES_ERRORs'][] = $error;
			}
		}

		$cancelButton = we_button::create_button("cancel", "javascript:top.close()");
		$closeButton = we_button::create_button("close", "javascript:top.close()");

		$progressbar = "";
		$formnum = (isset($_REQUEST["weFormNum"]) ? $_REQUEST["weFormNum"] : 0);
		$formcount = (isset($_REQUEST["weFormCount"]) ? $_REQUEST["weFormCount"] : 0);
		$js = we_button::create_state_changer(false) . '

var weFormNum = ' . $formnum . ';
var weFormCount = ' . $formcount . ';

function back() {
	if(top.imgimportcontent.document.we_startform.step.value=="2") {
		top.location.href="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=import&we_cmd[1]=import_files";
	} else {
		top.location.href="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=import_files";
	}

}

function weCheckAC(j){
	if(top.imgimportcontent.YAHOO.autocoml){
		feld = top.imgimportcontent.YAHOO.autocoml.checkACFields();
		if(j<30){
			if(feld.running) {
				setTimeout("weCheckAC(j++)",100);
			} else {
				return feld.valid
			}
		} else {
			return false;
		}
	} else {
		return true;
	}
}

function next() {
	if(!weCheckAC(1)) return false;
	if (top.imgimportcontent.document.getElementById("start") && top.imgimportcontent.document.getElementById("start").style.display != "none") {
		' . (we_hasPerm('EDIT_KATEGORIE') ? 'top.imgimportcontent.selectCategories();' : '') . '
		top.imgimportcontent.document.we_startform.submit();
	} else {
		if(weFormNum == weFormCount && weFormNum != 0){
			document.getElementById("progressbar").style.display = "none";
';

		if(isset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']) && $formnum == $formcount && $formnum != 0){

			$filelist = '';
			foreach($_SESSION['weS']['WE_IMPORT_FILES_ERRORs'] as $err){
				$filelist .= '- ' . $err["filename"] . ' => ' . g_l('importFiles', '[' . $err["error"] . ']') . '\n';
			}
			unset($_SESSION['weS']['WE_IMPORT_FILES_ERRORs']);
			$js .= we_message_reporting::getShowMessageCall(sprintf(g_l('importFiles', "[error]"), $filelist), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			$js .= we_message_reporting::getShowMessageCall(g_l('importFiles', "[finished]"), we_message_reporting::WE_MESSAGE_NOTICE);
		}

		$js .= "
			top.opener.top.we_cmd('load','" . FILE_TABLE . "');
			top.close();
			return;
		}
		forms = top.imgimportcontent.document.forms;
		var z=0;
		var sameName=top.imgimportcontent.document.we_startform.sameName.value;
		var prefix =  'trash_';
		var imgs = top.imgimportcontent.document.getElementsByTagName('IMG');
		for(var i = 0; i<imgs.length; i++){
			if(imgs[i].id.length > prefix.length && imgs[i].id.substring(0,prefix.length) == prefix){
				imgs[i].src='" . BUTTONS_DIR . "btn_function_trash_dis.gif';
				imgs[i].style.cursor='default';
			}
		}
		for(var i=0; i<forms.length;i++){
			if(forms[i].name.substring(0,14) == 'we_upload_form') {
				if(z == weFormNum && forms[i].we_File.value != ''){
					forms[i].importToID.value = top.imgimportcontent.document.we_startform.importToID.value;" . ((we_image_edit::gd_version() > 0) ? ("
					forms[i].thumbs.value = top.imgimportcontent.document.we_startform.thumbs.value;
					forms[i].width.value = top.imgimportcontent.document.we_startform.width.value;
					forms[i].height.value = top.imgimportcontent.document.we_startform.height.value;
					forms[i].widthSelect.value = top.imgimportcontent.document.we_startform.widthSelect.value;
					forms[i].heightSelect.value = top.imgimportcontent.document.we_startform.heightSelect.value;
					forms[i].keepRatio.value = top.imgimportcontent.document.we_startform.keepRatio.checked ? 1 : 0;
					forms[i].quality.value = top.imgimportcontent.document.we_startform.quality.value;
					for(var n=0;n<top.imgimportcontent.document.we_startform.degrees.length;n++){
						if(top.imgimportcontent.document.we_startform.degrees[n].checked){
							forms[i].degrees.value = top.imgimportcontent.document.we_startform.degrees[n].value;
							break;
						}
					}") : "") . "
					forms[i].sameName.value = sameName;
					forms[i].weFormNum.value = weFormNum + 1;
					forms[i].weFormCount.value = forms.length - 2;
					back_enabled = switch_button_state('back', 'back_enabled', 'disabled');
					next_enabled = switch_button_state('next', 'next_enabled', 'disabled');
					document.getElementById('progressbar').style.display = '';
					forms[i].submit();
					return;
				}
				z++;
			}
		}
	}
}";

		$js = we_html_element::jsElement($js);

		$prevButton = we_button::create_button("back", "javascript:back();", true, -1, -1, "", "", false);
		$prevButton2 = we_button::create_button("back", "javascript:back();", true, -1, -1, "", "", false, false);
		$nextButton = we_button::create_button("next", "javascript:next();", true, -1, -1, "", "", $this->step > 0, false);

		$prog = ($formcount == 0) ? 0 : (($this->step == 0) ? 0 : ((int) ((100 / $formcount) * ($formnum + 1))));
		$pb = new we_progressBar($prog);
		$pb->setStudLen(200);
		$pb->addText(sprintf(g_l('importFiles', "[import_file]"), $formnum + 1), 0, "title");
		$progressbar = '<span id="progressbar"' . (($this->step == 0) ? 'style="display:none' : '') . '">' . $pb->getHTML() . '</span>';
		$js .= $pb->getJSCode();

		$prevNextButtons = $prevButton ? we_button::create_button_table(array($prevButton, $nextButton)) : null;

		$table = new we_html_table(array(
			"border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "100%"
			), 1, 2);
		$table->setCol(0, 0, null, $progressbar);
		$table->setCol(0, 1, array(
			"align" => "right"
			), we_html_element::htmlDiv(array(
				'id' => 'normButton'
				), we_button::position_yes_no_cancel($prevNextButtons, null, $cancelButton, 10, '', array(), 10)) .
			we_html_element::htmlDiv(
				array(
				'id' => 'juButton', 'style' => 'display:none;'
				), we_button::position_yes_no_cancel($prevButton2, null, $closeButton, 10, '', array(), 10)));

		if($this->step == 3){
			$table->setCol(0, 0, null, '');
			$table->setCol(0, 1, array("align" => "right"), we_html_element::htmlDiv(array(
					'id' => 'normButton'
					), we_button::position_yes_no_cancel($prevButton2, null, $closeButton, 10, '', array(), 10)));
		}

		$content = $table->getHtml();
		$body = we_html_element::htmlBody($bodyAttribs, $content);
		return $this->_getHtmlPage($body, $js);
	}

	function importFile(){
		if(isset($_FILES['we_File']) && strlen($_FILES['we_File']["tmp_name"])){
			if(!we_hasPerm(we_base_ContentTypes::inst()->getPermission(getContentTypeFromFile($_FILES['we_File']["name"])))){
				return array(
					"filename" => $_FILES['we_File']["name"], "error" => "no_perms"
				);
			}
			$we_ContentType = getContentTypeFromFile($_FILES['we_File']["name"]);
			// initializing $we_doc
			include (WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
			$tempName = TEMP_PATH . "/" . weFile::getUniqueId();
			if(!@move_uploaded_file($_FILES['we_File']["tmp_name"], $tempName)){
				return array(
					"filename" => $_FILES['we_File']["name"], "error" => "move_file_error"
				);
			}

			// setting Filename, Path ...
			$_fn = importFunctions::correctFilename($_FILES['we_File']["name"]);

			$we_doc->Filename = preg_replace('/^(.+)\..+$/', "\\1", $_fn);
			$we_doc->Extension = (stristr($_fn, ".") ? strtolower(preg_replace('/^.+(\..+)$/', "\\1", $_fn)) : '');
			$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
			$we_doc->setParentID($this->importToID);
			$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != "/") ? "/" : "") . $we_doc->Text;

			// if file exists we have to see if we should create a new one or overwrite it!
			if(($file_id = f('SELECT ID FROM ' . FILE_TABLE . ' WHERE Path="' . $GLOBALS['DB_WE']->escape($we_doc->Path) . '"', 'ID', $GLOBALS['DB_WE']))){
				if($this->sameName == 'overwrite'){
					$tmp = $we_doc->ClassName;
					$we_doc = new $tmp();
					$we_doc->initByID($file_id, FILE_TABLE);
				} else
				if($this->sameName == "rename"){
					$z = 0;
					$footext = $we_doc->Filename . "_" . $z . $we_doc->Extension;
					while(f("SELECT ID FROM " . FILE_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($footext) . "' AND ParentID=" . intval($this->importToID), "ID", $GLOBALS['DB_WE'])) {
						$z++;
						$footext = $we_doc->Filename . "_" . $z . $we_doc->Extension;
					}
					$we_doc->Text = $footext;
					$we_doc->Filename = $we_doc->Filename . "_" . $z;
					$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != "/") ? "/" : "") . $we_doc->Text;
				} else{
					return array("filename" => $_FILES['we_File']["name"], 'error' => g_l('importFiles', '[same_name]'));
				}
			}
			// now change the category
			$we_doc->Category = $this->categories;
			if($we_ContentType == "image/*" || $we_ContentType == "application/x-shockwave-flash"){
				$we_size = $we_doc->getimagesize($tempName);
				if(is_array($we_size) && count($we_size) >= 2){
					$we_doc->setElement("width", $we_size[0], "attrib");
					$we_doc->setElement("height", $we_size[1], "attrib");
					$we_doc->setElement("origwidth", $we_size[0]);
					$we_doc->setElement("origheight", $we_size[1]);
				}
			}
			if($we_doc->Extension == '.pdf'){
				$we_doc->setMetaDataFromFile($tempName);
			}

			$we_doc->setElement("type", $we_ContentType, "attrib");
			$fh = @fopen($tempName, "rb");
			if($_FILES['we_File']["size"] <= 0){
				$_FILES['we_File']["size"] = 1;
			}
			if($fh){
				if(!$we_doc->isBinary()){
					$we_fileData = fread($fh, $_FILES['we_File']["size"]);
				}
				fclose($fh);
			} else{
				return array("filename" => $_FILES['we_File']["name"], 'error' => g_l('importFiles', '[read_file_error]'));
			}
			$foo = explode('/', $_FILES["we_File"]["type"]);
			if($we_doc->isBinary()){
				$we_doc->setElement("data", $tempName);
			} else{
				$we_doc->setElement("data", $we_fileData, $foo[0]);
			}

			$we_doc->setElement("filesize", $_FILES['we_File']["size"], "attrib");
			$we_doc->Table = FILE_TABLE;
			$we_doc->Published = time();
			if($we_ContentType == "image/*"){
				$we_doc->Thumbs = $this->thumbs;

				$newWidth = 0;
				$newHeight = 0;
				if($this->width){
					$newWidth = ($this->widthSelect == "percent" ?
							round(($we_doc->getElement("origwidth") / 100) * $this->width) :
							$this->width);
				}
				if($this->height){
					$newHeight = ($this->widthSelect == "percent" ?
							round(($we_doc->getElement("origheight") / 100) * $this->height) :
							$this->height);
				}
				if(($newWidth && ($newWidth != $we_doc->getElement("origwidth"))) || ($newHeight && ($newHeight != $we_doc->getElement("origheight")))){

					if($we_doc->resizeImage($newWidth, $newHeight, $this->quality, $this->keepRatio)){
						$this->width = $newWidth;
						$this->height = $newHeight;
					}
				}

				if($this->degrees){
					$we_doc->rotateImage(
						($this->degrees % 180 == 0) ? $we_doc->getElement("origwidth") : $we_doc->getElement(
								"origheight"), ($this->degrees % 180 == 0) ? $we_doc->getElement("origheight") : $we_doc->getElement(
								"origwidth"), $this->degrees, $this->quality);
				}
				$we_doc->DocChanged = true;
			}
			if(!$we_doc->we_save()){
				return array("filename" => $_FILES['we_File']["name"], "error" => g_l('importFiles', '[save_error]'));
			}
			if($we_ContentType == "image/*" && $this->importMetadata){
				$we_doc->importMetaData();
				$we_doc->we_save();
			}
			if(!$we_doc->we_publish()){
				return array("filename" => $_FILES['we_File']["name"], "error" => "publish_error"
				);
			}
			if($we_ContentType == "image/*" && $this->importMetadata){
				$we_doc->importMetaData();
			}
			return array();
		} else{
			return array("filename" => $_FILES['we_File']["name"], "error" => g_l('importFiles', '[php_error]'));
		}
	}

	function _getHiddens(){
		return we_html_element::htmlHidden(array("name" => "we_cmd[0]", "value" => "import_files")) .
			we_html_element::htmlHidden(array("name" => "cmd", "value" => "buttons")) .
			we_html_element::htmlHidden(array("name" => "step", "value" => "1")) .
			we_html_element::htmlHidden(array("name" => "weFormNum", "value" => "0")) .
			we_html_element::htmlHidden(array("name" => "weFormCount", "value" => "0")) .
			we_html_element::htmlHidden(array("name" => "importToID", "value" => $this->importToID)) .
			we_html_element::htmlHidden(array("name" => "sameName", "value" => $this->sameName)) .
			we_html_element::htmlHidden(array("name" => "thumbs", "value" => $this->thumbs)) .
			we_html_element::htmlHidden(array("name" => "width", "value" => $this->width)) .
			we_html_element::htmlHidden(array("name" => "height", "value" => $this->height)) .
			we_html_element::htmlHidden(array("name" => "widthSelect", "value" => $this->widthSelect)) .
			we_html_element::htmlHidden(array("name" => "heightSelect", "value" => $this->heightSelect)) .
			we_html_element::htmlHidden(array("name" => "keepRatio", "value" => $this->keepRatio)) .
			we_html_element::htmlHidden(array("name" => "degrees", "value" => $this->degrees)) .
			we_html_element::htmlHidden(array("name" => "quality", "value" => $this->quality)) .
			we_html_element::htmlHidden(array("name" => "categories", "value" => $this->categories));
	}

	function _getFrameset(){
		$_step = isset($_REQUEST['step']) ? $_REQUEST['step'] : -1;

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));

		$frameset->setAttributes(array("rows" => "*,40"));
		$frameset->addFrame(
			array(
				"src" => WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files&cmd=content" . ($_step > -1 ? '&step=' . $_step : ''),
				"name" => "imgimportcontent",
				"scrolling" => "auto",
				"noresize" => null
		));
		$frameset->addFrame(
			array(
				"src" => WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files&cmd=buttons" . ($_step > -1 ? '&step=' . $_step : ''),
				"name" => "imgimportbuttons",
				"scrolling" => "no"
		));

		// set and return html code
		$body = $frameset->getHtml() . "\n" . we_baseElement::getHtmlCode(new we_baseElement("noframes"));

		return $this->_getHtmlPage($body);
	}

	function _getHtmlPage($body, $js = ""){
		$yuiSuggest = & weSuggest::getInstance();
		$head = we_html_tools::getHtmlInnerHead(g_l('import', '[title]')) . STYLESHEET . $js .
			$yuiSuggest->getYuiCssFiles() . $yuiSuggest->getYuiJsFiles();
		return we_html_element::htmlDocType() . we_html_element::htmlHtml(we_html_element::htmlHead($head) . $body);
	}

	function getHTMLCategory(){
		$_width_size = 300;

		$addbut = we_button::create_button(
				"add", "javascript:we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.addCat(top.allPaths);')");
		$del_but = addslashes(
			we_html_element::htmlImg(
				array(
					'src' => BUTTONS_DIR . 'btn_function_trash.gif',
					'onclick' => 'javascript:#####placeHolder#####;',
					'style' => 'cursor: pointer; width: 27px;'
		)));

		$js = we_html_element::jsScript(JS_DIR . 'utils/multi_edit.js');

		$variant_js = '
			var categories_edit = new multi_edit("categoriesDiv",document.we_startform,0,"' . $del_but . '",' . ($_width_size - 10) . ',false);
			categories_edit.addVariant();';

		$_cats = makeArrayFromCSV($this->categories);
		if(is_array($_cats)){
			foreach($_cats as $cat){
				$variant_js .='
categories_edit.addItem();
categories_edit.setItem(0,(categories_edit.itemCount-1),"' . id_to_path($cat, CATEGORY_TABLE) . '");';
			}
		}

		$variant_js .= 'categories_edit.showVariant(0);';

		$js .= we_html_element::jsElement($variant_js);

		$table = new we_html_table(
			array(
			'id' => 'CategoriesBlock',
			'style' => 'display: block;',
			'cellpadding' => 0,
			'cellspacing' => 0,
			'border' => 0
			), 4, 1);

		$table->setColContent(0, 0, we_html_tools::getPixel(5, 5));
		$table->setColContent(
			1, 0, we_html_element::htmlDiv(
				array(
					'id' => 'categoriesDiv',
					'class' => 'blockWrapper',
					'style' => 'width: ' . ($_width_size) . 'px; height: 60px; border: #AAAAAA solid 1px;'
		)));
		$table->setColContent(2, 0, we_html_tools::getPixel(5, 5));
		$table->setCol(
			3, 0, array(
			'colspan' => '2', 'align' => 'right'
			), we_button::create_button_table(
				array(
					we_button::create_button("delete_all", "javascript:removeAllCats()"), $addbut
		)));

		return $table->getHtml() . $js . we_html_element::jsElement('
function removeAllCats(){
	if(categories_edit.itemCount>0){
		while(categories_edit.itemCount>0){
			categories_edit.delItem(categories_edit.itemCount);
		}
		categories_edit.showVariant(0);
	}
}

function addCat(paths){
	var path = paths.split(",");
	for (var i = 0; i < path.length; i++) {
		if(path[i]!="") {
			categories_edit.addItem();
			categories_edit.setItem(0,(categories_edit.itemCount-1),path[i]);
		}
	}
	categories_edit.showVariant(0);
}

function selectCategories() {
	var cats = new Array();
	for(var i=0;i<categories_edit.itemCount;i++){
		cats.push(categories_edit.form.elements[categories_edit.name+"_variant0_"+categories_edit.name+"_item"+i].value);
	}
	categories_edit.form.categories.value=makeCSVFromArray(cats);
}');
	}

	function savePropsInSession(){
		$_SESSION['weS']['_we_import_files'] = array();
		$_vars = get_object_vars($this);
		foreach($_vars as $_name => $_value){
			$_SESSION['weS']['_we_import_files'][$_name] = $_value;
		}
	}

	function loadPropsFromSession(){
		if(isset($_SESSION['weS']['_we_import_files'])){
			foreach($_SESSION['weS']['_we_import_files'] as $_name => $_var){
				$this->$_name = $_var;
			}
		}
	}

}

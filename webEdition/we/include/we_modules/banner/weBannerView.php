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
/* the parent class of storagable webEdition classes */

class weBannerView extends weBannerBase{

	// settings array; format settings[setting_name]=settings_value
	var $settings = array();
	//default banner
	var $banner;
	//wat page is currentlly displed 0-properties(default);1-stat;
	var $page = 0;
	var $UseFilter = 0;
	var $FilterDate = -1;
	var $FilterDateEnd = -1;
	var $Order = "views";
	var $pageFields = array();

	public function __construct(){
		parent::__construct();
		$this->banner = new weBanner();
		$this->page = 0;
		$this->settings = $this->getSettings();
		$this->pageFields[weBanner::PAGE_PROPERTY] = array("Text", "ParentID", "bannerID", "bannerUrl", "bannerIntID", "IntHref", "IsDefault", "IsActive", "StartOk", "EndOk", "StartDate", "EndDate");
		$this->pageFields[weBanner::PAGE_PLACEMENT] = array("DoctypeIDs", "TagName");
		$this->pageFields[weBanner::PAGE_STATISTICS] = array();
		$yuiSuggest = & weSuggest::getInstance();
	}

	function getHiddens(){
		$out = $this->htmlHidden("home", "0") .
			$this->htmlHidden("ncmd", "new_banner") .
			$this->htmlHidden("ncmdvalue", "") .
			$this->htmlHidden("bid", $this->banner->ID) .
			$this->htmlHidden("pnt", $_REQUEST["pnt"]) .
			$this->htmlHidden("page", $this->page) .
			$this->htmlHidden("bname", $this->uid) .
			$this->htmlHidden("order", $this->Order) .
			$this->htmlHidden($this->uid . "_IsFolder", $this->banner->IsFolder);
		foreach($this->banner->persistents as $p){
			if(!in_array($p, $this->pageFields[$this->page])){
				$v = $this->banner->{$p};
				$out.=$this->htmlHidden($this->uid . "_$p", $v);
			}
		}
		return $out;
	}

	function htmlHidden($name, $value = "", $id = ""){
		return '<input type="hidden" name="' . trim($name) . '" value="' . oldHtmlspecialchars($value) . '"' . (empty($id) ? "" : ' id="' . $id . '"') . ' />';
	}

	function getProperties(){
		$yuiSuggest = & weSuggest::getInstance();
		if(isset($_REQUEST["home"]) && $_REQUEST["home"]){
			$GLOBALS["we_print_not_htmltop"] = true;
			$GLOBALS["we_head_insert"] = $this->getJSProperty();
			$GLOBALS["we_body_insert"] = '<form name="we_form">' . "\n";
			$GLOBALS["we_body_insert"] .= $this->getHiddens() . '</form>' . "\n";
			$GLOBALS["mod"] = "banner";
			ob_start();

			include(WE_MODULES_PATH . 'home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
		} else{
			$out = $this->getJSProperty() . $yuiSuggest->getYuiJsFiles() . '
				</head>
				<body class="weEditorBody" onload="loaded=1;" onunload="doUnload()">
				<form name="we_form" onsubmit="return false;">' . "\n";
			$out.=$this->getHiddens();
			$parts = array();
			$headline = "";
			$znr = -1;
			$openText = "";
			$closeText = "";
			$wepos = "";
			$itsname = "";
			switch($this->page){
				case weBanner::PAGE_PROPERTY:
					$out .= $this->htmlHidden("UseFilter", $this->UseFilter) .
						$this->htmlHidden("FilterDate", $this->FilterDate) .
						$this->htmlHidden("FilterDateEnd", $this->FilterDateEnd);
					$parts = array(
						array(
							"headline" => g_l('modules_banner', '[path]'),
							"html" => $this->formPath(),
							"space" => 120
						));
					$znr = -1;
					if(!$this->banner->IsFolder){
						$parts[] = array(
							"headline" => g_l('modules_banner', '[banner]'),
							"html" => $this->formBanner(),
							"space" => 120
						);
						$parts[] = array(
							"headline" => g_l('modules_banner', '[period]'),
							"html" => $this->formPeriod(),
							"space" => 120
						);
						$znr = 2;
					}
					if(defined("CUSTOMER_TABLE")){
						$parts[] = array(
							"headline" => g_l('modules_banner', '[customers]'),
							"html" => $this->formCustomer(),
							"space" => 120
						);
					}
					$headline = g_l('tabs', "[module][properties]");
					$itsname = "weBannerProp";
					$openText = g_l('weClass', "[moreProps]");
					$closeText = g_l('weClass', "[lessProps]");
					$wepos = weGetCookieVariable("but_weBannerProp");
					break;
				case weBanner::PAGE_PLACEMENT:
					$out .= $this->htmlHidden("UseFilter", $this->UseFilter) .
						$this->htmlHidden("FilterDate", $this->FilterDate) .
						$this->htmlHidden("FilterDateEnd", $this->FilterDateEnd);
					$parts = array(array(
							"headline" => g_l('modules_banner', '[tagname]'),
							"html" => $this->formTagName(),
							"space" => 120
						),
						array(
							"headline" => g_l('modules_banner', '[pages]'),
							"html" => $this->formFiles(),
							"space" => 120
						),
						array(
							"headline" => g_l('modules_banner', '[dirs]'),
							"html" => $this->formFolders(),
							"space" => 120
						),
						array(
							"headline" => g_l('modules_banner', '[categories]'),
							"html" => $this->formCategories(),
							"space" => 120
						),
						array(
							"headline" => g_l('modules_banner', '[doctypes]'),
							"html" => $this->formDoctypes(),
							"space" => 120)
					);
					$headline = g_l('tabs', "[module][placement]");
					$znr = 3;
					$itsname = "weBannerPlace";
					$openText = g_l('weClass', "[moreProps]");
					$closeText = g_l('weClass', "[lessProps]");
					$wepos = weGetCookieVariable("but_$itsname");
					break;
				case weBanner::PAGE_STATISTICS:
					$headline = g_l('tabs', "[module][statistics]");
					$parts = array(
						array(
							"headline" => "",
							"html" => $this->formStat(),
							"space" => 0)
					);
					break;
			}

			$out.= we_multiIconBox::getJS() .
				we_multiIconBox::getHTML($itsname, "100%", $parts, 30, "", $znr, $openText, $closeText, ($wepos == "down")) .
				'</form>' .
				$yuiSuggest->getYuiCss() .
				$yuiSuggest->getYuiJs() .
				'</body></html>';
		}
		return $out;
	}

	function previewBanner(){
		$ID = $this->banner->bannerID;
		if($ID){
			$ct = f('SELECT ContentType FROM ' . FILE_TABLE . " WHERE ID=" . intval($ID), "ContentType", $this->db);
			switch($ct){
				case "image/*";
					$img = new we_imageDocument();
					$img->initByID($ID, FILE_TABLE);
					return $img->getHTML();
			}
		}


		return '';
	}

	function getJSTopCode(){
		?>
		<script type="text/javascript">

			var hot = 0;

			function doUnload() {
				if (!!jsWindow_count) {
					for (i = 0; i < jsWindow_count; i++) {
						eval("jsWindow" + i + "Object.close()");
					}
				}
			}
		<?php
		$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
		$title = '';
		foreach($GLOBALS["_we_available_modules"] as $modData){
			if($modData["name"] == $mod){
				$title = $modData["text"];
				break;
			}
		}
		?>

			parent.document.title = "webEdition <?php print g_l('global', "[modules]") . ' - ' . $title; ?>";

			function setHot() {
				hot = "1";
			}

			function usetHot() {
				hot = "0";
			}

			function we_cmd(){
				var args = "";
				var url = "<?php print WEBEDITION_DIR; ?>we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
				if(hot == "1" && arguments[0] != "save_banner") {
					if(confirm("<?php print g_l('modules_banner', '[save_changed_banner]') ?>")) {
						arguments[0] = "save_banner";
					} else {
						top.content.usetHot();
					}
				}
				switch (arguments[0]){
					case "exit_banner":
						if(hot != "1") {
							eval('top.opener.top.we_cmd(\'exit_modules\')');
						}
						break;
					case "new_banner":
						if(top.content.resize.right.editor.edbody.loaded){
							top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
							top.content.resize.right.editor.edbody.submitForm();
						}
						else setTimeout('we_cmd("new_banner");',10);
						break;
					case "new_bannergroup":
						if(top.content.resize.right.editor.edbody.loaded){
							top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
							top.content.resize.right.editor.edbody.submitForm();
						}
						else setTimeout('we_cmd("new_bannergroup");',10);
						break;
					case "delete_banner":
		<?php
		if(!we_hasPerm("DELETE_BANNER")){
			print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else{
			?>
								if(top.content.resize.right.editor.edbody.loaded && top.content.resize.right.editor.edbody.we_is_home==undefined){
									if(!confirm("<?php print g_l('modules_banner', '[delete_question]') ?>")) return;
								}
								else {
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_WARNING); ?>
									return;
								}
								top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
								top.content.resize.right.editor.edbody.submitForm();
		<?php } ?>
						break;
					case "save_banner":
		<?php if(we_hasPerm("EDIT_BANNER") || we_hasPerm("NEW_BANNER")){ ?>
							if(top.content.resize.right.editor.edbody.loaded && top.content.resize.right.editor.edbody.we_is_home==undefined){
								if(!top.content.resize.right.editor.edbody.checkData()){
									return;
								}
							}else{
			<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_WARNING); ?>
									return;
								}

								top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
								top.content.resize.right.editor.edbody.submitForm();
		<?php } ?>
						top.content.usetHot();
						break;
					case "edit_banner":
						top.content.resize.right.editor.edbody.document.we_form.ncmd.value=arguments[0];
						top.content.resize.right.editor.edbody.document.we_form.bid.value=arguments[1];
						top.content.resize.right.editor.edbody.submitForm();
						break;
					default:
						for(var i = 0; i < arguments.length; i++) {
							args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
						}
						eval('top.opener.top.we_cmd('+args+')');
					}
				}
		</script>
		<?php
	}

	function getJSFooterCode(){
		?>
		<script type="text/javascript">

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
						case "empty_log":
							break;
						default:
							for(var i = 0; i < arguments.length; i++){
								args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
							}
							eval('parent.edbody.we_cmd('+args+')');
					}
				}
		</script>
		<?php
	}

	function getJSCmd(){
		?>
		<script type="text/javascript">
				function submitForm(){
					var f = self.document.we_form;
					f.target = "cmd";
					f.method = "post";
					f.submit();
				}
		</script>
		<?php
	}

	function getJSProperty(){
		echo we_html_element::jsScript(JS_DIR . 'windows.js');
		?>
		<script type="text/javascript">
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
						case "openSelector":
							new jsWindow(url,"we_selector",-1,-1,<?php echo WINDOW_SELECTOR_WIDTH . "," . WINDOW_SELECTOR_HEIGHT; ?>,true,true,true,true);
							break;
						case "openCatselector":
							new jsWindow(url,"we_catselector",-1,-1,<?php echo WINDOW_CATSELECTOR_WIDTH . "," . WINDOW_CATSELECTOR_HEIGHT; ?>,true,true,true,true);
							break;
						case "openDocselector":
							new jsWindow(url,"we_docselector",-1,-1,<?php echo WINDOW_DOCSELECTOR_WIDTH . "," . WINDOW_DOCSELECTOR_HEIGHT; ?>,true,true,true,true);
							break;
						case "openDirselector":
							new jsWindow(url,"we_dirselector",-1,-1,<?php echo WINDOW_DIRSELECTOR_WIDTH . "," . WINDOW_DIRSELECTOR_HEIGHT; ?>,true,true,true,true);
							break;
						case "openBannerDirselector":
							new jsWindow(url,"we_bannerselector",-1,-1,600,350,true,true,true);
							break;
						case "switchPage":
							document.we_form.ncmd.value=arguments[0];
							document.we_form.page.value=arguments[1];
							submitForm();
							break;
						case "add_cat":
						case "del_cat":
						case "del_all_cats":
						case "add_file":
						case "del_file":
						case "del_all_files":
						case "add_folder":
						case "del_folder":
						case "del_customer":
						case "del_all_customers":
						case "del_all_folders":
						case "add_customer":
							document.we_form.ncmd.value=arguments[0];
							document.we_form.ncmdvalue.value=arguments[1];
							submitForm();
							break;
						case "delete_stat":
							if(confirm("<?php print g_l('modules_banner', '[deleteStatConfirm]'); ?>")){
								document.we_form.ncmd.value=arguments[0];
								submitForm();
							}
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
						if(arguments[0]) f.target = arguments[0];
						else f.target = "edbody";
						if(arguments[1]) f.action = arguments[1];
						else f.action = "";
						if(arguments[2]) f.method = arguments[2];
						else f.method = "post";

						f.submit();
					}
					function checkData(){

						return true;
					}

					self.focus();
		</script>
		<?php
	}

	function processCommands(){
		if(isset($_REQUEST["ncmd"]))
			switch($_REQUEST["ncmd"]){
				case "delete_stat":
					$this->banner->views = 0;
					$this->banner->clicks = 0;
					$this->db->query('UPDATE ' . BANNER_TABLE . ' SET views=0,clicks=0 WHERE ID=' . intval($this->banner->ID));
					$this->db->query('DELETE FROM ' . BANNER_CLICKS_TABLE . ' WHERE ID=' . intval($this->banner->ID));
					$this->db->query('DELETE FROM ' . BANNER_VIEWS_TABLE . ' WHERE ID=' . intval($this->banner->ID));
					break;
				case "new_banner":
					$this->page = 0;
					$this->banner = new weBanner();
					print we_html_element::jsElement('
					top.content.resize.right.editor.edheader.location="edit_banner_frameset.php?pnt=edheader&page=' . $this->page . '&txt=' . $this->banner->Path . '&isFolder=' . $this->banner->IsFolder . '";
					top.content.resize.right.editor.edfooter.location="edit_banner_frameset.php?pnt=edfooter";
					');
					break;
				case "new_bannergroup":
					$this->page = 0;
					$this->banner = new weBanner(0, 1);
					print we_html_element::jsElement('
					top.content.resize.right.editor.edheader.location="edit_banner_frameset.php?pnt=edheader&page=' . $this->page . '&txt=' . $this->banner->Path . '&isFolder=' . $this->banner->IsFolder . '";
					top.content.resize.right.editor.edfooter.location="edit_banner_frameset.php?pnt=edfooter";
					');
					break;
				case "reload":
					print we_html_element::jsElement('
					top.content.resize.right.editor.edheader.location="edit_banner_frameset.php?pnt=edheader&page=' . $this->page . '&txt=' . $this->banner->Path . '&isFolder=' . $this->banner->IsFolder . '";
					top.content.resize.right.editor.edfooter.location="edit_banner_frameset.php?pnt=edfooter";') . '
					</head>
					<body></body>
					</html>';
					break;
				case "edit_banner":
					if(isset($_REQUEST["bid"])){
						$this->banner = new weBanner($_REQUEST["bid"]);
					}
					if($this->banner->IsFolder){
						$this->page = 0;
					}
					$_REQUEST["ncmd"] = "reload";
					$this->processCommands();

					break;
				case "add_cat":
					$arr = makeArrayFromCSV($this->banner->CategoryIDs);
					if(isset($_REQUEST["ncmdvalue"])){
						$ids = makeArrayFromCSV($_REQUEST["ncmdvalue"]);
						foreach($ids as $id){
							if($id && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->banner->CategoryIDs = makeCSVFromArray($arr, true);
					}
					break;
				case "del_cat":
					$arr = makeArrayFromCSV($this->banner->CategoryIDs);
					if(isset($_REQUEST["ncmdvalue"])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST["ncmdvalue"])
								array_splice($arr, $k, 1);
						}
						$this->banner->CategoryIDs = makeCSVFromArray($arr, true);
					}
					break;
				case "del_all_cats":
					$this->banner->CategoryIDs = "";
					break;
				case "add_file":
					$arr = makeArrayFromCSV($this->banner->FileIDs);
					if(isset($_REQUEST["ncmdvalue"])){
						$ids = makeArrayFromCSV($_REQUEST["ncmdvalue"]);
						foreach($ids as $id){
							if($id && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->banner->FileIDs = makeCSVFromArray($arr, true);
					}
					break;
				case "del_file":
					$arr = makeArrayFromCSV($this->banner->FileIDs);
					if(isset($_REQUEST["ncmdvalue"])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST["ncmdvalue"])
								array_splice($arr, $k, 1);
						}
						$this->banner->FileIDs = makeCSVFromArray($arr, true);
					}
					break;
				case "del_all_files":
					$this->banner->FileIDs = "";
					break;
				case "add_folder":
					$arr = makeArrayFromCSV($this->banner->FolderIDs);
					if(isset($_REQUEST["ncmdvalue"])){
						$ids = makeArrayFromCSV($_REQUEST["ncmdvalue"]);
						foreach($ids as $id){
							if(strlen($id) && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->banner->FolderIDs = makeCSVFromArray($arr, true);
					}
					break;
				case "add_customer":
					$arr = makeArrayFromCSV($this->banner->Customers);
					if(isset($_REQUEST["ncmdvalue"])){
						$ids = makeArrayFromCSV($_REQUEST["ncmdvalue"]);
						foreach($ids as $id){
							if($id && (!in_array($id, $arr))){
								array_push($arr, $id);
							}
						}
						$this->banner->Customers = makeCSVFromArray($arr, true);
					}
					break;
				case "del_customer":
					$arr = makeArrayFromCSV($this->banner->Customers);
					if(isset($_REQUEST["ncmdvalue"])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST["ncmdvalue"])
								array_splice($arr, $k, 1);
						}
						$this->banner->Customers = makeCSVFromArray($arr, true);
					}
					break;
				case "del_all_customers":
					$this->banner->Customers = "";
					break;
				case "del_folder":
					$arr = makeArrayFromCSV($this->banner->FolderIDs);
					if(isset($_REQUEST["ncmdvalue"])){
						foreach($arr as $k => $v){
							if($v == $_REQUEST["ncmdvalue"])
								array_splice($arr, $k, 1);
						}
						$this->banner->FolderIDs = makeCSVFromArray($arr, true);
					}
					break;
				case "del_all_folders":
					$this->banner->FolderIDs = "";
					break;
				case "switchPage":
					if(isset($_REQUEST["page"])){
						$this->page = $_REQUEST["page"];
					}
					break;
				case "save_banner":
					if(isset($_REQUEST["bid"])){
						$newone = ($this->banner->ID == 0);
						$exist = false;
						$double = f('SELECT COUNT(1) AS Count FROM ' . BANNER_TABLE . " WHERE Text='" . $this->db->escape($this->banner->Text) . "' AND ParentID=" . intval($this->banner->ParentID) . ($newone ? '' : ' AND ID!=' . intval($this->banner->ID)), 'Count', $this->db);
						$acQuery = new weSelectorQuery();
						if(!we_hasPerm("EDIT_BANNER") && !we_hasPerm("NEW_BANNER")){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} elseif($newone && !we_hasPerm("NEW_BANNER")){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} elseif($this->banner->Text == ""){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[no_text]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} elseif(preg_match('|[%/\\\"\']|', $this->banner->Text)){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} elseif(!$this->banner->bannerID && !$this->banner->IsFolder){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[no_bannerid]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} elseif($this->banner->ID && ($this->banner->ID == $this->banner->ParentID)){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[no_group_in_group]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} elseif($double){
							if($double){
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[double_name]'), we_message_reporting::WE_MESSAGE_ERROR));
								return;
							}
						}

						if($this->banner->ParentID > 0){
							$acResult = $acQuery->getItemById($this->banner->ParentID, BANNER_TABLE, "IsFolder");
							if(!$acResult || (isset($acResult[0]['IsFolder']) && $acResult[0]['IsFolder'] == 0)){
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_ac_field]'), we_message_reporting::WE_MESSAGE_ERROR));
								return;
							}
						}
						if($this->banner->IntHref){
							$acResult = $acQuery->getItemById($this->banner->bannerIntID, FILE_TABLE, array("IsFolder"));
							if(!$acResult || $acResult[0]['IsFolder'] == 1){
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_ac_field]'), we_message_reporting::WE_MESSAGE_ERROR));
								return;
							}
						}
						if($this->banner->bannerID > 0){
							$acResult = $acQuery->getItemById($this->banner->bannerID, FILE_TABLE, array("ContentType"));
							if(!$acResult || $acResult[0]['ContentType'] != 'image/*'){
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_ac_field]'), we_message_reporting::WE_MESSAGE_ERROR));
								return;
							}
						}


						$childs = "";
						$message = "";
						$this->banner->save($message);
						echo we_html_element::jsElement(
							($newone ?
								'top.content.makeNewEntry("' . $this->banner->Icon . '",' . $this->banner->ID . ',' . $this->banner->ParentID . ',"' . $this->banner->Text . '",true,"' . ($this->banner->IsFolder ? 'folder' : 'file') . '","weBanner",1);' :
								'top.content.updateEntry(' . $this->banner->ID . ',' . $this->banner->ParentID . ',"' . $this->banner->Text . '",1);') .
							$childs .
							we_message_reporting::getShowMessageCall(($this->banner->IsFolder ? g_l('modules_banner', '[save_group_ok]') : g_l('modules_banner', '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE));
					}
					break;
				case "delete_banner":
					if(isset($_REQUEST["bid"])){
						if(!we_hasPerm("DELETE_BANNER")){
							print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_banner', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
							return;
						} else{

							$this->banner = new weBanner($_REQUEST["bid"]);
							if($this->banner->delete()){
								$this->banner = new weBanner(0, $this->banner->IsFolder);
								print we_html_element::jsElement('
							top.content.deleteEntry(' . $_REQUEST["bid"] . ',"' . ($this->banner->IsFolder ? 'folder' : 'file') . '");
							' . we_message_reporting::getShowMessageCall(($this->banner->IsFolder ? g_l('modules_banner', '[delete_group_ok]') : g_l('modules_banner', '[delete_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) . '
							top.content.we_cmd("new_banner");
							');
							} else{
								print we_html_element::jsElement(we_message_reporting::getShowMessageCall(($this->banner->IsFolder ? g_l('modules_banner', '[delete_group_nok]') : g_l('modules_banner', '[delete_nok]')), we_message_reporting::WE_MESSAGE_ERROR));
							}
						}
					}
					break;
				case "reload_table":
					$this->page = 1;
					break;
				default:
			}
	}

	function processVariables(){
		if(isset($_REQUEST["bname"])){
			$this->uid = $_REQUEST["bname"];
		}
		if(isset($_REQUEST["bid"])){
			$this->banner->ID = $_REQUEST["bid"];
		}
		if(isset($_REQUEST["page"])){
			$this->page = $_REQUEST["page"];
		}
		if(isset($_REQUEST["order"])){
			$this->Order = $_REQUEST["order"];
		}
		if(isset($_REQUEST["DoctypeIDs"]) && is_array($_REQUEST["DoctypeIDs"])){
			$this->banner->DoctypeIDs = makeCSVFromArray($_REQUEST["DoctypeIDs"], true);
		}
		if(isset($_REQUEST["UseFilter"])){
			$this->UseFilter = $_REQUEST["UseFilter"];
		}
		if(isset($_REQUEST["Customers"])){
			$this->banner->Customers = $_REQUEST["Customers"];
		}
		if(is_array($this->banner->persistents)){
			foreach($this->banner->persistents as $key => $val){
				$varname = $this->uid . "_" . $val;
				if(isset($_REQUEST[$varname])){
					$this->banner->$val = $_REQUEST[$varname];
				}
			}
		}

		if(isset($_REQUEST["dateFilter_day"])){
			$this->FilterDate = mktime(0, 0, 0, $_REQUEST["dateFilter_month"], $_REQUEST["dateFilter_day"], $_REQUEST["dateFilter_year"]);
		} else if(isset($_REQUEST["FilterDate"])){
			$this->FilterDate = $_REQUEST["FilterDate"];
		}
		if(isset($_REQUEST["dateFilter2_day"])){
			$this->FilterDateEnd = mktime(0, 0, 0, $_REQUEST["dateFilter2_month"], $_REQUEST["dateFilter2_day"], $_REQUEST["dateFilter2_year"]);
		} else if(isset($_REQUEST["FilterDateEnd"])){
			$this->FilterDateEnd = $_REQUEST["FilterDateEnd"];
		}

		if(isset($_REQUEST["we__From_day"])){
			$this->banner->StartDate = mktime($_REQUEST["we__From_hour"], $_REQUEST["we__From_minute"], 0, $_REQUEST["we__From_month"], $_REQUEST["we__From_day"], $_REQUEST["we__From_year"]);
			$this->banner->EndDate = mktime($_REQUEST["we__To_hour"], $_REQUEST["we__To_minute"], 0, $_REQUEST["we__To_month"], $_REQUEST["we__To_day"], $_REQUEST["we__To_year"]);
		}
	}

	// Static function - Settings

	function getSettings(){
		$db = new DB_WE();
		$ret = array();
		$db->query('SELECT * FROM ' . BANNER_PREFS_TABLE);
		while($db->next_record()) {
			$ret[$db->f('pref_name')] = $db->f('pref_value');
		}
		return $ret;
	}

	function saveSettings($settings){
		$db = new DB_WE();
		$db->query('TRUNCATE ' . BANNER_PREFS_TABLE);
		foreach($settings as $key => $value){
			$db->query('INSERT INTO ' . BANNER_PREFS_TABLE . '(pref_name,pref_value) VALUES "' . $key . '","' . $value . '"');
		}
	}

	############### form functions #################

	function formTagName(){

		$tagnames = array();
		$query = 'SELECT ' . CONTENT_TABLE . '.Dat AS templateCode, ' . LINK_TABLE . '.DID AS DID FROM ' . CONTENT_TABLE . "," . LINK_TABLE . " WHERE " . LINK_TABLE . ".DocumentTable='" . stripTblPrefix(TEMPLATES_TABLE) . "' AND " . LINK_TABLE . ".CID=" . CONTENT_TABLE . ".ID AND " . CONTENT_TABLE . ".Dat LIKE '%<we:banner %'  ";
		$this->db->query($query);
		$foo = array();
		while($this->db->next_record()) {
			preg_match_all("|(<we:banner [^>]+>)|U", $this->db->f('templateCode'), $foo, PREG_SET_ORDER);
			foreach($foo as $cur){
				$wholeTag = $cur[1];
				$name = preg_replace('|.+name="([^"]+)".*|i', '\1', $wholeTag);
				if($name && (!in_array($name, $tagnames))){
					$tagnames[] = $name;
				}
			}
		}
		sort($tagnames);

		$code = '<table border="0" cellpadding="0" cellspacing="0"><tr><td class="defaultfont">' .
			we_html_tools::htmlTextInput($this->uid . "_TagName", 50, $this->banner->TagName, "", 'style="width:250px" onchange="top.content.setHot();"') .
			'</td>
<td class="defaultfont">' . we_html_tools::getPixel(10, 2) . '</td>
<td class="defaultfont"><select style="width:240px" class="weSelect" name="' . $this->uid . '_TagName_tmp" size="1" onchange="top.content.setHot(); this.form.elements[\'' . $this->uid . '_TagName\'].value=this.options[this.selectedIndex].value;this.selectedIndex=0">' .
			'<option value=""></option>';
		foreach($tagnames as $tagname){
			$code .= '<option value="' . $tagname . '">' . $tagname . '</option>' . "\n";
		}
		$code .= '</select></td></tr></table>';
		return $code;
	}

	function formFiles(){
		$delallbut = we_button::create_button("delete_all", "javascript:top.content.setHot(); we_cmd('del_all_files')");
		$wecmdenc3 = we_cmd_enc("fillIDs();opener.we_cmd('add_file',top.allIDs);");
		$addbut = we_button::create_button("add", "javascript:top.content.setHot(); we_cmd('openDocselector','','" . FILE_TABLE . "','','','" . $wecmdenc3 . "','','','text/webedition','',1)");

		$dirs = new MultiDirChooser(495, $this->banner->FileIDs, "del_file", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", FILE_TABLE);

		return $dirs->get();
	}

	function formFolders(){
		$delallbut = we_button::create_button("delete_all", "javascript:top.content.setHot();we_cmd('del_all_folders')");
		$wecmdenc3 = we_cmd_enc("fillIDs();opener.we_cmd('add_folder',top.allIDs);");
		$addbut = we_button::create_button("add", "javascript:top.content.setHot();we_cmd('openDirselector','','" . FILE_TABLE . "','','','" . $wecmdenc3 . "','','','',1)");

		$dirs = new MultiDirChooser(495, $this->banner->FolderIDs, "del_folder", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", FILE_TABLE);

		return $dirs->get();
	}

	function formCategories(){
		$delallbut = we_button::create_button("delete_all", "javascript:top.content.setHot();we_cmd('del_all_cats')");
		$addbut = we_button::create_button("add", "javascript:top.content.setHot();we_cmd('openCatselector','','" . CATEGORY_TABLE . "','','','fillIDs();opener.we_cmd(\'add_cat\',top.allIDs);')");

		$cats = new MultiDirChooser(495, $this->banner->CategoryIDs, "del_cat", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CATEGORY_TABLE);

		return $cats->get();
	}

	function formDoctypes(){

		$dt = '<select name="DoctypeIDs[]" size="10" multiple="multiple" style="width:495" onchange="top.content.setHot();">';
		$this->db->query("SELECT DocType,ID FROM " . DOC_TYPES_TABLE . " ORDER BY DocType");

		$doctypesArr = makeArrayFromCSV($this->banner->DoctypeIDs);
		while($this->db->next_record()) {
			$dt .= '<option value="' . $this->db->f("ID") . '"' . (in_array($this->db->f("ID"), $doctypesArr) ? ' selected' : '') . '>' . $this->db->f("DocType") . '</option>' . "\n";
		}
		$dt .= '</select>';


		return $dt;
	}

	function formStat($class = "middlefont"){
		$datefilterCheck = we_forms::checkboxWithHidden($this->UseFilter, "UseFilter", g_l('modules_banner', '[datefilter]'), false, "defaultfont", "top.content.setHot(); we_cmd('switchPage','" . $this->page . "')");
		$datefilter = we_html_tools::getDateInput2("dateFilter%s", ($this->FilterDate == -1 ? time() : $this->FilterDate), false, "dmy", "top.content.setHot(); we_cmd('switchPage','" . $this->page . "');", $class);
		$datefilter2 = we_html_tools::getDateInput2("dateFilter2%s", ($this->FilterDateEnd == -1 ? time() : $this->FilterDateEnd), false, "dmy", "top.content.setHot(); we_cmd('switchPage','" . $this->page . "');", $class);

		$content = '
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2">' . $datefilterCheck . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(20, 5) . '</td><td>' . we_html_tools::getPixel(500, 2) . '</td>
	</tr>
	<tr>
		<td colspan="2"><table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="defaultfont">' . g_l('global', "[from]") . ':&nbsp;</td>
		<td>' . $datefilter . '</td>
		<td class="defaultfont">' . g_l('global', "[to]") . ':&nbsp;</td>
		<td>' . $datefilter2 . '</td>
	</tr>
</table></td>
	</tr>
</table>';

		$GLOBALS["lv"] = new we_listview_banner("0", 99999999, $this->Order, $this->banner->ID, $this->UseFilter, $this->FilterDate, $this->FilterDateEnd + 86399);
		$pathlink = '<a href="javascript:top.content.setHot();if(this.document.we_form.elements[\'order\'].value==\'path\'){this.document.we_form.elements[\'order\'].value=\'path desc\';}else{this.document.we_form.elements[\'order\'].value=\'path\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[page]') . '</a>';
		$viewslink = '<a href="javascript:top.content.setHot();if(this.document.we_form.elements[\'order\'].value==\'views desc\'){this.document.we_form.elements[\'order\'].value=\'views\';}else{this.document.we_form.elements[\'order\'].value=\'views desc\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[views]') . '</a>';
		$clickslink = '<a href="javascript:top.content.setHot();if(this.document.we_form.elements[\'order\'].value==\'clicks desc\'){this.document.we_form.elements[\'order\'].value=\'clicks\';}else{this.document.we_form.elements[\'order\'].value=\'clicks desc\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[clicks]') . '</a>';
		$ratelink = '<a href="javascript:top.content.setHot();if(this.document.we_form.elements[\'order\'].value==\'rate desc\'){this.document.we_form.elements[\'order\'].value=\'rate\';}else{this.document.we_form.elements[\'order\'].value=\'rate desc\';}we_cmd(\'switchPage\',\'' . $this->page . '\');">' . g_l('modules_banner', '[rate]') . '</a>';
		$headline = array(
			array("dat" => $pathlink),
			array("dat" => $viewslink),
			array("dat" => $clickslink),
			array("dat" => $ratelink)
		);
		$rows = array(
			array(
				array("dat" => g_l('modules_banner', '[all]')),
				array("dat" => $GLOBALS["lv"]->getAllviews()),
				array("dat" => $GLOBALS["lv"]->getAllclicks()),
				array("dat" => $GLOBALS["lv"]->getAllrate() . "%", "align" => "right")
			)
		);
		while($GLOBALS["lv"]->next_record()) {
			$rows[] = array(
				array("dat" => ($GLOBALS["lv"]->f("page") ? '' : '<a href="' . $GLOBALS["lv"]->f("WE_PATH") . '" target="_blank">') . $GLOBALS["lv"]->f("WE_PATH") . ($GLOBALS["lv"]->f("page") ? '' : '</a>'), FILE_TABLE),
				array("dat" => $GLOBALS["lv"]->f("views")),
				array("dat" => $GLOBALS["lv"]->f("clicks")),
				array("dat" => $GLOBALS["lv"]->f("rate") . "%", "align" => "right")
			);
		}

		$table = we_html_tools::htmlDialogBorder3(650, 0, $rows, $headline, $class);
		$delbut = we_button::create_button("delete", "javascript:top.content.setHot();we_cmd('delete_stat')");

		return $content . we_html_tools::getPixel(2, 10) . $table . we_html_tools::getPixel(2, 10) . "<br>" . $delbut;
	}

	function formBanner($leftsize = 120){
		return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . $this->formBannerChooser(388, $this->uid . "_bannerID", $this->banner->bannerID, g_l('modules_banner', '[imagepath]'), "opener.we_cmd(\\'switchPage\\',\\'" . $this->page . "\\')") . '</td>
	</tr>
' . ($this->banner->bannerID ?
				'	<tr>
		<td>' . we_html_tools::getPixel(20, 10) . '</td>
	</tr>
	<tr>
		<td>' . $this->previewBanner() . '</td>
	</tr>
' : '') .
			'	<tr>
		<td>' . we_html_tools::getPixel(20, 10) . '</td>
	</tr>
	<tr>
		<td>' . $this->formBannerHref() . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(20, 10) . '</td>
	</tr>
	<tr>
		<td>' . $this->formBannerNumbers() . '</td>
	</tr>
</table>';
	}

	function formPeriod(){

		$now = time();
		$from = $this->banner->StartOk ? $this->banner->StartDate : $now;
		$to = $this->banner->EndOk ? $this->banner->EndDate : $now + 3600;

		$checkStart = we_forms::checkboxWithHidden($this->banner->StartOk, $this->uid . '_StartOk', g_l('modules_banner', '[from]'), false, "defaultfont", "top.content.setHot();");
		$checkEnd = we_forms::checkboxWithHidden($this->banner->EndOk, $this->uid . '_EndOk', g_l('modules_banner', '[to]'), false, "defaultfont", "top.content.setHot();");


		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . $checkStart . '</td>
		<td></td>
		<td>' . $checkEnd . '</td>
	</tr>
	<tr>
		<td></td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td></td>
	</tr>
	<tr>
		<td>' . we_html_tools::getDateInput2("we__From%s", $from, false, "", "top.content.setHot();") . '</td>
		<td></td>
		<td>' . we_html_tools::getDateInput2("we__To%s", $to, false, "", "top.content.setHot();") . '</td>
	</tr>
</table>';
	}

	function formCustomer(){
		$delallbut = we_button::create_button("delete_all", "javascript:top.content.setHot();we_cmd('del_all_customers')");
		$addbut = we_button::create_button("add", "javascript:top.content.setHot();we_cmd('openSelector','','" . CUSTOMER_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_customer\\',top.allIDs);','','','',1)");
		$obj = new MultiDirChooser(508, $this->banner->Customers, "del_customer", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CUSTOMER_TABLE);
		return $obj->get();
	}

	function formPath($leftsize = 120){
		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_Text", 37, $this->banner->Text, "", 'style="width:388px" id="yuiAcInputPathName" onchange="top.content.setHot();" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()"'), g_l('modules_banner', '[name]')) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(20, 10) . '</td>
	</tr>
	<tr>
		<td>' . $this->formDirChooser(388, BANNER_TABLE, $this->banner->ParentID, $this->uid . "_ParentID", g_l('modules_banner', '[group]'), "", "PathGroup") . '</td>
	</tr>
</table>';
	}

	function getHTMLParentPath(){
		$IDName = "ParentID";
		$Pathname = "ParentPath";

		return $this->htmlHidden($IDName, 0) .
			$this->htmlHidden($Pathname, "") .
			we_button::create_button("select", "javascript:top.content.setHot();we_cmd('openSelector',document.we_form.elements['$IDName'].value,'" . BANNER_TABLE . "','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','opener.we_cmd(\\'copy_banner\\');','" . session_id() . "','$rootDirID')");
	}

	/* creates the DocumentChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	function formBannerChooser($width = "", $IDName = "bannerID", $IDValue = "0", $title = "", $cmd = ""){
		$yuiSuggest = & weSuggest::getInstance();
		$Pathvalue = $IDValue ? id_to_path($IDValue, FILE_TABLE, $this->db) : '';
		$Pathname = md5(uniqid(__FUNCTION__, true));
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:top.content.setHot();we_cmd('openDocselector',((document.we_form.elements['$IDName'].value != 0) ? document.we_form.elements['$IDName'].value : ''),'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','',0,'image/*')");

		$yuiSuggest->setAcId("Image");
		$yuiSuggest->setLabel($title);
		$yuiSuggest->setContentType('folder,image/*,application/*,application/x-shockwave-flash,video/quicktime');
		$yuiSuggest->setInput($Pathname, $Pathvalue, "onchange=\"top.content.setHot();\"", true);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($IDName, $IDValue);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	function formDirChooser($width = "", $table = FILE_TABLE, $idvalue, $idname, $title = "", $cmd = "", $acID = ""){
		$yuiSuggest = & weSuggest::getInstance();
		$path = id_to_path($idvalue, $table, $this->db);
		$textname = md5(uniqid(__FUNCTION__, true));
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:top.content.setHot();we_cmd('openBannerDirselector',document.we_form.elements['$idname'].value,'" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "')");

		$yuiSuggest->setAcId($acID);
		$yuiSuggest->setLabel($title);
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($textname, $path, "onchange=\"top.content.setHot();\"", true);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(false);
		$yuiSuggest->setResult($idname, $idvalue);
		$yuiSuggest->setSelector("Dirselector");
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	function formBannerNumbers(){
		$cn = md5(uniqid(__FUNCTION__, true));
		$activeCheckbox = we_forms::checkboxWithHidden($this->banner->IsActive, $this->uid . '_IsActive', g_l('modules_banner', '[active]'), false, "defaultfont", "top.content.setHot();");
		$maxShow = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_maxShow", 10, $this->banner->maxShow, "", "onchange=\"top.content.setHot();\"", "text", "100", 0), g_l('modules_banner', '[max_show]'), "left", "defaultfont");
		$maxClicks = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_maxClicks", 10, $this->banner->maxClicks, "", "onchange=\"top.content.setHot();\"", "text", "100", 0), g_l('modules_banner', '[max_clicks]'), "left", "defaultfont");
		$weight = we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect($this->uid . "_weight", array("8" => "1 (" . g_l('modules_banner', '[infrequent]') . ")", "7" => "2", "6" => "3", "5" => "4", "4" => "5 (" . g_l('modules_banner', '[normal]') . ")", "3" => "6", "2" => "7", "1" => "8", "0" => "9 (" . g_l('modules_banner', '[frequent]') . ")"), 1, $this->banner->weight), g_l('modules_banner', '[weight]'), "left", "defaultfont");

		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . $activeCheckbox . '</td>
		<td>' . we_html_tools::getPixel(40, 2) . '</td>
		<td>' . $maxShow . '</td>
		<td>' . we_html_tools::getPixel(40, 2) . '</td>
		<td>' . $maxClicks . '</td>
		<td>' . we_html_tools::getPixel(40, 2) . '</td>
		<td>' . $weight . '</td>
	</tr>
</table>';
	}

	function formBannerHref(){
		$idvalue = $this->banner->bannerIntID;
		$idname = $this->uid . "_bannerIntID";

		$Pathvalue = $idvalue ? id_to_path($idvalue, FILE_TABLE, $this->db) : "";
		$Pathname = md5(uniqid(__FUNCTION__, true));

		$cmd = "opener.document.we_form.elements[\\'" . $this->uid . "_IntHref\\'][1].checked=true";

		$onkeydown = "self.document.we_form.elements['" . $this->uid . "_IntHref'][0].checked=true; YAHOO.autocoml.setValidById('yuiAcInputInternalURL'); document.getElementById('yuiAcInputInternalURL').value=''; document.getElementById('yuiAcResultInternalURL').value=''";
		$onkeydown2 = "self.document.we_form.elements['" . $this->uid . "_IntHref'][1].checked=true; document.getElementById('" . $this->uid . "_bannerUrl" . "').value='';";
		$width = 388;

		$title1 = g_l('modules_banner', '[ext_url]');
		$title2 = g_l('modules_banner', '[int_url]');

		$title1 = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td><input type="radio" name="' . $this->uid . '_IntHref" id="' . $this->uid . '_IntHref0" value="0"' . ($this->banner->IntHref ? "" : " checked") . ' /></td>
		<td class="defaultfont">&nbsp;<label for="' . $this->uid . '_IntHref0">' . $title1 . '</label></td>
	</tr>
</table>';

		$title2 = '<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td><input type="radio" name="' . $this->uid . '_IntHref" id="' . $this->uid . '_IntHref1" value="1"' . ($this->banner->IntHref ? " checked" : "") . ' /></td>
		<td class="defaultfont">&nbsp;<label for="' . $this->uid . '_IntHref1">' . $title2 . '</label></td>
	</tr>
</table>';

		//javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'".FILE_TABLE."','document.we_form.elements[\\'$idname\\'].value','document.we_form.elements[\\'$Pathname\\'].value','".$cmd."','',0,'')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc(str_replace('\\', '', $cmd));
		$button = we_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','',0,'')");
		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId("InternalURL");
		$yuiSuggest->setContentType("folder,text/xml,text/webedition,image/*,text/html,application/*,application/x-shockwave-flash,video/quicktime");
		$yuiSuggest->setInput($Pathname, $Pathvalue, "onchange=\"top.content.setHot();\"", true);
		$yuiSuggest->setLabel($title2);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($idname, $idvalue);
		$yuiSuggest->setSelector("Docselector");
		$yuiSuggest->setWidth($width);
		$yuiSuggest->setSelectButton($button);

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($this->uid . "_bannerUrl", 30, $this->banner->bannerUrl, "", 'id="' . $this->uid . '_bannerUrl" onkeydown="' . $onkeydown . '"', "text", $width, 0), $title1, "left", "defaultfont", "", "", "", "", "", 0) . we_html_tools::getPixel(10, 5) . $yuiSuggest->getHTML();
	}

}
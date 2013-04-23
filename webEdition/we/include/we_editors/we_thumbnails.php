<?php

/**
 * webEdition CMS
 *
 * $Rev: 5044 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 17:59:55 +0100 (Thu, 01 Nov 2012) $
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
// Check if we need to create a new thumbnail
if(isset($_GET["newthumbnail"]) && $_GET["newthumbnail"] != ""){
	if(we_hasPerm("ADMINISTRATOR")){
		$DB_WE->query("INSERT INTO " . THUMBNAILS_TABLE . " (Name) VALUES ('" . $DB_WE->escape($_GET["newthumbnail"]) . "')");
		header('Location: ' . getServerUrl(true) . WE_INCLUDES_DIR . 'we_editors/we_thumbnails.php?id=' . f("SELECT ID FROM " . THUMBNAILS_TABLE . " WHERE Name = '" . $DB_WE->escape($_GET["newthumbnail"]) . "'", "ID", $DB_WE));
		exit();
	}
}

// Check if we need to delete a thumbnail
if(isset($_GET["deletethumbnail"]) && $_GET["deletethumbnail"] != ""){
	if(we_hasPerm("ADMINISTRATOR")){
		// Delete thumbnails in filesystem
		we_thumbnail::deleteByThumbID($_GET["deletethumbnail"]);

		// Delete entry in database
		$DB_WE->query("DELETE FROM " . THUMBNAILS_TABLE . " WHERE ID = " . intval($_GET["deletethumbnail"]));

		header("Location: " . getServerUrl() . WE_INCLUDES_DIR . "we_editors/we_thumbnails.php");
		exit();
	}
}

// Check which thumbnail to work with
if(!isset($_GET["id"]) || $_GET["id"] == ""){

	$tmpid = f("SELECT ID FROM " . THUMBNAILS_TABLE . " ORDER BY Name LIMIT 1", "ID", $DB_WE);

	$_GET["id"] = $tmpid ? $tmpid : -1;
}

$save_javascript = "";

/* * ***************************************************************************
 * FUNCTIONS
 * *************************************************************************** */

/**
 * This function returns the HTML code of a dialog.
 *
 * @param          string                                  $name
 * @param          string                                  $title
 * @param          array                                   $content
 * @param          int                                     $expand             (optional)
 * @param          string                                  $show_text          (optional)
 * @param          string                                  $hide_text          (optional)
 * @param          bool                                    $cookie             (optional)
 * @param          string                                  $JS                 (optional)
 *
 * @return         string
 */
function create_dialog($name, $title, $content, $expand = -1, $show_text = "", $hide_text = "", $cookie = false, $JS = ""){
	$_output = "";

	// Check, if we need to write some JavaScripts
	if($JS != ""){
		$_output .= $JS;
	}

	if($expand != -1){
		$_output .= we_multiIconBox::getJS();
	}

	// Return HTML code of dialog
	return $_output . we_multiIconBox::getHTML($name, "100%", $content, 30, "", $expand, $show_text, $hide_text, $cookie != false ? ($cookie == "down") : $cookie, $title);
}

/**
 * This functions saves an option in the current session.
 *
 * @param          string                                  $settingvalue
 * @param          string                                  $settingname
 *
 * @see            save_all_values
 *
 * @return         bool
 */
function remember_value($settingvalue, $settingname){
	global $DB_WE;

	$_update_prefs = false;

	if(isset($settingvalue) && ($settingvalue != null)){
		switch($settingname){
			case '$_REQUEST["thumbnail_name"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Name = '" . $DB_WE->escape($settingvalue) . "' WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["thumbnail_width"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Width = " . abs($settingvalue) . " WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["thumbnail_height"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Height = " . abs($settingvalue) . " WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["thumbnail_quality"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Quality = " . abs($settingvalue) . " WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Ratio"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Ratio = " . abs($settingvalue) . " WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Maxsize"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Maxsize = " . abs($settingvalue) . " WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Interlace"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Interlace = " . abs($settingvalue) . " WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Fitinside"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Fitinside = " . abs($settingvalue) . " WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Format"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Format = '" . (($settingvalue == "none") ? "" : $DB_WE->escape($settingvalue)) . "' WHERE ID =" . intval($_REQUEST["edited_id"]));
				break;
			default:
				break;
		}
	} else{
		switch($settingname){
			case '$_REQUEST["thumbnail_width"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Width = 0 WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["thumbnail_height"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Height = 0 WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["thumbnail_quality"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET quality = 0 WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Ratio"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Ratio = 0 WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Maxsize"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Maxsize = 0 WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Interlace"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Interlace = 0 WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Fitinside"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Fitinside = 0 WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			case '$_REQUEST["Format"]':
				$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Format = 'jpg' WHERE ID = " . intval($_REQUEST["edited_id"]));
				break;
			default:
				$_update_prefs = false;
				break;
		}
	}

	// Return if the preferences need to be written to the database
	return $_update_prefs;
}

/**
 * This functions saves all options.
 *
 * @see            remember_value()
 *
 * @return         void
 */
function save_all_values(){
	global $DB_WE;

	/*	 * ***********************************************************************
	 * THUMBNAILS
	 * *********************************************************************** */

	if(we_hasPerm("ADMINISTRATOR")){
		// Update settings
		$_update_prefs = remember_value(isset($_REQUEST["thumbnail_name"]) ? $_REQUEST["thumbnail_name"] : null, '$_REQUEST["thumbnail_name"]');
		$_update_prefs = remember_value(isset($_REQUEST["thumbnail_width"]) ? $_REQUEST["thumbnail_width"] : null, '$_REQUEST["thumbnail_width"]');
		$_update_prefs = remember_value(isset($_REQUEST["thumbnail_height"]) ? $_REQUEST["thumbnail_height"] : null, '$_REQUEST["thumbnail_height"]');
		$_update_prefs = remember_value(isset($_REQUEST["thumbnail_quality"]) ? $_REQUEST["thumbnail_quality"] : null, '$_REQUEST["thumbnail_quality"]');
		$_update_prefs = remember_value(isset($_REQUEST["Ratio"]) ? $_REQUEST["Ratio"] : null, '$_REQUEST["Ratio"]');
		$_update_prefs = remember_value(isset($_REQUEST["Maxsize"]) ? $_REQUEST["Maxsize"] : null, '$_REQUEST["Maxsize"]');
		$_update_prefs = remember_value(isset($_REQUEST["Interlace"]) ? $_REQUEST["Interlace"] : null, '$_REQUEST["Interlace"]');
		$_update_prefs = remember_value(isset($_REQUEST["Fitinside"]) ? $_REQUEST["Fitinside"] : null, '$_REQUEST["Fitinside"]');
		$_update_prefs = remember_value(isset($_REQUEST["Format"]) ? $_REQUEST["Format"] : null, '$_REQUEST["Format"]');

		// Update saving timestamp
		$DB_WE->query("UPDATE " . THUMBNAILS_TABLE . " SET Date = UNIX_TIMESTAMP()  WHERE ID = " . intval($_REQUEST["edited_id"]));
	}
}

function build_dialog($selected_setting = "ui"){
	global $DB_WE;


	switch($selected_setting){
		case "save":
			/*			 * ***************************************************************
			 * SAVE DIALOG
			 * *************************************************************** */

			$_settings = array();

			/**
			 * Saving
			 */
			// Build dialog
			array_push($_settings, array("headline" => "", "html" => g_l('thumbnails', "[save]"), "space" => 0));

			/**
			 * BUILD FINAL DIALOG
			 */
			// Build dialog element if user has permission
			$_dialog = create_dialog("", g_l('thumbnails', "[save_wait]"), $_settings);

			break;

		case "saved":
			/*			 * ***************************************************************
			 * SAVED SUCCESSFULLY DIALOG
			 * *************************************************************** */

			$_thumbs = array();

			/**
			 * Saved
			 */
			// Build dialog
			$_thumbs[] = array("headline" => "", "html" => g_l('thumbnails', "[saved]"), "space" => 0);

			/**
			 * BUILD FINAL DIALOG
			 */
			// Build dialog element if user has permission
			$_dialog = create_dialog("", g_l('thumbnails', "[saved_successfully]"), $_thumbs);

			break;

		case "dialog":
			/*			 * ***************************************************************
			 * THUMBNAILS
			 * *************************************************************** */

			$_thumbs = array();

			// Generate needed JS
			$_needed_JavaScript_Source = "
					function in_array(haystack, needle) {
						for (var i = 0; i < haystack.length; i++) {
							if (haystack[i] == needle) {
								return true;
							}
						}

						return false;
					}

					function add_thumbnail() {";

			// Detect thumbnail names
			$_thumbnail_names = "";

			$DB_WE->query("SELECT Name FROM " . THUMBNAILS_TABLE);

			while($DB_WE->next_record()) {
				$_thumbnail_names .= "'" . str_replace("'", "\'", $DB_WE->f("Name")) . "',";
			}

			$_thumbnail_names = rtrim($_thumbnail_names, ',');

			$_needed_JavaScript_Source .= "
						var thumbnail_names = new Array(" . $_thumbnail_names . ");
						var name = prompt('" . g_l('thumbnails', "[new]") . "', '');

						if (name != null) {
							if((name.indexOf('<') != -1) || (name.indexOf('>') != -1)) {
								" . we_message_reporting::getShowMessageCall(g_l('alert', "[name_nok]"), we_message_reporting::WE_MESSAGE_ERROR) . "
								return;
							}

							if (name.indexOf(\"'\") != -1 || name.indexOf(\",\") != -1) {
								" . we_message_reporting::getShowMessageCall(g_l('alert', "[thumbnail_hochkomma]"), we_message_reporting::WE_MESSAGE_ERROR) . "
							} else if (name == '') {
								" . we_message_reporting::getShowMessageCall(g_l('alert', "[thumbnail_empty]"), we_message_reporting::WE_MESSAGE_ERROR) . "
							} else if (in_array(thumbnail_names, name)) {
								" . we_message_reporting::getShowMessageCall(g_l('alert', "[thumbnail_exists]"), we_message_reporting::WE_MESSAGE_ERROR) . "
							} else {
								self.location = '" . WE_INCLUDES_DIR . "we_editors/we_thumbnails.php?newthumbnail=' + escape(name);
							}
						}
					}

					function delete_thumbnail() {" .
				((we_hasPerm("ADMINISTRATOR")) ?
					"var deletion = confirm('" . sprintf(g_l('thumbnails', "[delete_prompt]"), f("SELECT Name FROM " . THUMBNAILS_TABLE . " WHERE ID='" . $_GET["id"] . "'", "Name", $DB_WE)) . "');

							if (deletion == true) {
								self.location = '" . WE_INCLUDES_DIR . "we_editors/we_thumbnails.php?deletethumbnail=" . $_GET["id"] . "';
							}" :
					"") . "
					}

					function change_thumbnail() {
						var url = '" . WE_INCLUDES_DIR. "we_editors/we_thumbnails.php?id=' + arguments[0];

						self.location = url;
					}

					function changeFormat() {
						if(document.getElementById('Format').value == 'jpg' || document.getElementById('Format').value == 'none') {
							document.getElementById('thumbnail_quality_text_cell').style.display='';
							document.getElementById('thumbnail_quality_value_cell').style.display='';
						} else {
							document.getElementById('thumbnail_quality_text_cell').style.display='none';
							document.getElementById('thumbnail_quality_value_cell').style.display='none';
						}
					}

					function init() {
						changeFormat();
					}";

			$_needed_JavaScript = we_html_element::jsElement($_needed_JavaScript_Source) .
				we_html_element::jsScript(JS_DIR . "keyListener.js");

			/**
			 * Thumbnails
			 */
			$_enabled_buttons = false;

			// Build language select box
			$_thumbnails = new we_html_select(array("name" => "Thumbnails", "class" => "weSelect", "size" => "10", "style" => "width: 314px;", "onchange" => "if(this.selectedIndex > -1){change_thumbnail(this.options[this.selectedIndex].value);}"));

			$DB_WE->query("SELECT ID, Name FROM " . THUMBNAILS_TABLE . " ORDER BY Name");

			$_thumbnail_counter_firsttime = true;

			while($DB_WE->next_record()) {
				$_enabled_buttons = true;
				$_thumbnail_counter = $DB_WE->f("ID");

				$_thumbnails->addOption($DB_WE->f("ID"), $DB_WE->f("Name"));

				if($_thumbnail_counter_firsttime && ($_GET["id"] == -1)){
					$_GET["id"] = $DB_WE->f("ID");

					$_thumbnails->selectOption($DB_WE->f("ID"));
				} else if($_GET["id"] == $DB_WE->f("ID")){
					$_thumbnails->selectOption($DB_WE->f("ID"));
				}

				$_thumbnail_counter_firsttime = false;
			}

			// Create thumbnails list
			$_thumbnails_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 2, 3);

			$_thumbnails_table->setCol(0, 0, null, we_html_element::htmlHidden(array("name" => "edited_id", "value" => $_GET["id"])) . $_thumbnails->getHtml());
			$_thumbnails_table->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
			$_thumbnails_table->setCol(0, 2, array("valign" => "top"), we_button::create_button("add", "javascript:add_thumbnail();") . we_html_tools::getPixel(1, 10) . we_button::create_button("delete", "javascript:delete_thumbnail();", true, 100, 22, "", "", !$_enabled_buttons, false));

			// Build dialog
			array_push($_thumbs, array("headline" => "", "html" => $_thumbnails_table->getHtml(), "space" => 0));

			/*			 * ***************************************************************
			 * NAME
			 * *************************************************************** */

			$_thumbnail_name = ($_GET["id"] != -1) ? f("SELECT Name FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Name", $DB_WE) : -1;

			$_thumbnail_name_input = we_html_tools::htmlTextInput("thumbnail_name", 22, ($_thumbnail_name != -1 ? $_thumbnail_name : ""), 255, ($_thumbnail_name == -1 ? "disabled=\"true\"" : ""), "text", 225);

			// Build dialog
			array_push($_thumbs, array("headline" => g_l('thumbnails', "[name]"), "html" => $_thumbnail_name_input, "space" => 200));

			/*			 * ***************************************************************
			 * PROPERTIES
			 * *************************************************************** */

			// Create specify thumbnail dimension input
			$_thumbnail_width = ($_GET["id"] != -1) ? f("SELECT Width FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Width", $DB_WE) : -1;
			$_thumbnail_height = ($_GET["id"] != -1) ? f("SELECT Height FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Height", $DB_WE) : -1;
			$_thumbnail_quality = ($_GET["id"] != -1) ? f("SELECT Quality FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Quality", $DB_WE) : -1;

			$_thumbnail_specify_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 5, 3);

			$_thumbnail_specify_table->setCol(1, 0, array("width" => "60"), we_html_tools::getPixel(1, 5));
			$_thumbnail_specify_table->setCol(3, 0, array("colspan" => "3"), we_html_tools::getPixel(1, 5));

			$_thumbnail_specify_table->setCol(0, 0, array("class" => "defaultfont"), g_l('thumbnails', "[width]") . ":");
			$_thumbnail_specify_table->setCol(2, 0, array("class" => "defaultfont"), g_l('thumbnails', "[height]") . ":");
			$_thumbnail_specify_table->setCol(4, 0, array("class" => "defaultfont", "id" => "thumbnail_quality_text_cell"), g_l('thumbnails', "[quality]") . ":");

			$_thumbnail_specify_table->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
			$_thumbnail_specify_table->setCol(2, 1, null, we_html_tools::getPixel(10, 1));
			$_thumbnail_specify_table->setCol(4, 1, null, we_html_tools::getPixel(10, 22));

			$_thumbnail_specify_table->setCol(0, 2, null, we_html_tools::htmlTextInput("thumbnail_width", 6, ($_thumbnail_width != -1 ? $_thumbnail_width : ""), 4, ($_thumbnail_width == -1 ? "disabled=\"disabled\"" : ""), "text", 60));
			$_thumbnail_specify_table->setCol(2, 2, null, we_html_tools::htmlTextInput("thumbnail_height", 6, ($_thumbnail_height != -1 ? $_thumbnail_height : ""), 4, ($_thumbnail_height == -1 ? "disabled=\"disabled\"" : ""), "text", 60));
			$_thumbnail_specify_table->setCol(4, 2, array("class" => "defaultfont", "id" => "thumbnail_quality_value_cell"), we_image_edit::qualitySelect("thumbnail_quality", $_thumbnail_quality));

			// Create checkboxes for options for thumbnails
			$_thumbnail_ratio = ($_GET["id"] != -1) ? f("SELECT Ratio FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Ratio", $DB_WE) : -1;
			$_thumbnail_maximize = ($_GET["id"] != -1) ? f("SELECT Maxsize FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Maxsize", $DB_WE) : -1;
			$_thumbnail_interlace = ($_GET["id"] != -1) ? f("SELECT Interlace FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Interlace", $DB_WE) : -1;
			$_thumbnail_fitinside = ($_GET["id"] != -1) ? f("SELECT Fitinside FROM " . THUMBNAILS_TABLE . " WHERE ID='" . abs($_GET["id"]) . "'", "Fitinside", $DB_WE) : -1;

			$_thumbnail_option_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 7, 1);

			$_thumbnail_option_table->setCol(0, 0, null, we_forms::checkbox(1, (($_thumbnail_ratio == -1 || $_thumbnail_ratio == 0) ? false : true), "Ratio", g_l('thumbnails', "[ratio]"), false, "defaultfont", "", ($_thumbnail_ratio == -1)));
			$_thumbnail_option_table->setCol(1, 0, null, we_html_tools::getPixel(1, 5));
			$_thumbnail_option_table->setCol(2, 0, null, we_forms::checkbox(1, (($_thumbnail_maximize == -1 || $_thumbnail_maximize == 0) ? false : true), "Maxsize", g_l('thumbnails', "[maximize]"), false, "defaultfont", "", ($_thumbnail_maximize == -1)));
			$_thumbnail_option_table->setCol(3, 0, null, we_html_tools::getPixel(1, 5));
			$_thumbnail_option_table->setCol(4, 0, null, we_forms::checkbox(1, (($_thumbnail_interlace == -1 || $_thumbnail_interlace == 0) ? false : true), "Interlace", g_l('thumbnails', "[interlace]"), false, "defaultfont", "", ($_thumbnail_interlace == -1)));
			$_thumbnail_option_table->setCol(5, 0, null, we_html_tools::getPixel(1, 5));
			$_thumbnail_option_table->setCol(6, 0, null, we_forms::checkbox(1, (($_thumbnail_fitinside == -1 || $_thumbnail_fitinside == 0) ? false : true), "Fitinside", 'Fit inside', false, "defaultfont", "", ($_thumbnail_fitinside == -1)));

			// Build final HTML code
			$_window_html = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 3, 1);
			$_window_html->setCol(0, 0, null, $_thumbnail_specify_table->getHtml());
			$_window_html->setCol(1, 0, null, we_html_tools::getPixel(1, 10));
			$_window_html->setCol(2, 0, null, $_thumbnail_option_table->getHtml());

			// Build dialog
			array_push($_thumbs, array("headline" => g_l('thumbnails', "[properties]"), "html" => $_window_html->getHtml(), "space" => 200));

			/*			 * ***************************************************************
			 * OUTPUT FORMAT
			 * *************************************************************** */

			$_thumbnail_format = ($_GET["id"] != -1) ? f("SELECT Format FROM " . THUMBNAILS_TABLE . " WHERE ID=" . intval($_GET["id"]), "Format", $DB_WE) : -1;

			// Define available formats
			$_thumbnails_formats = array("none" => g_l('thumbnails', "[format_original]"), "gif" => g_l('thumbnails', "[format_gif]"), "jpg" => g_l('thumbnails', "[format_jpg]"), "png" => g_l('thumbnails', "[format_png]"));

			$_thumbnail_format_select_attribs = array("name" => "Format", "id" => "Format", "class" => "weSelect", "style" => "width: 225px;", "onchange" => "changeFormat()");

			if($_thumbnail_format == -1){
				$_thumbnail_format_select_attribs["disabled"] = "true"; //#6027
			}

			$_thumbnail_format_select = new we_html_select($_thumbnail_format_select_attribs);

			foreach($_thumbnails_formats as $_k => $_v){
				if(in_array($_k, we_image_edit::supported_image_types()) || $_k == "none"){
					$_thumbnail_format_select->addOption($_k, $_v);

					// Check if added option is selected
					if($_thumbnail_format == $_k || (($_thumbnail_format == "") && ($_k == "none"))){
						$_thumbnail_format_select->selectOption($_k);
					}
				}
			}

			// Build dialog
			$_thumbs[] = array("headline" => g_l('thumbnails', "[format]"), "html" => $_thumbnail_format_select->getHtml(), "space" => 200);

			/**
			 * BUILD FINAL DIALOG
			 */
			$_dialog = create_dialog("settings_predefined", g_l('thumbnails', "[thumbnails]"), $_thumbs, -1, "", "", false, $_needed_JavaScript);

			break;
	}

	if(isset($_dialog)){
		return $_dialog;
	} else{
		return "";
	}
}

/**
 * This functions renders the complete dialog.
 *
 * @return         string
 */
function render_dialog(){
	// Render setting groups
	return we_html_element::htmlDiv(array("id" => "thumbnails_dialog"), build_dialog("dialog")) .
		// Render save screen
		we_html_element::htmlDiv(array("id" => "thumbnails_save", "style" => "display: none;"), build_dialog("save"));
}

/* * ***************************************************************************
 * RENDER FILE
 * *************************************************************************** */

we_html_tools::htmlTop();

// Check if we need to save settings
if(isset($_REQUEST["save_thumbnails"]) && $_REQUEST["save_thumbnails"] == "true"){

	if(isset($_REQUEST["thumbnail_name"]) && (strpos($_REQUEST["thumbnail_name"], "'") !== false || strpos($_REQUEST["thumbnail_name"], ",") !== false)){
		$save_javascript = we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', "[thumbnail_hochkomma]"), we_message_reporting::WE_MESSAGE_ERROR) .
				'history.back()');
	} else{
		save_all_values();


		$save_javascript = we_html_element::jsElement(
				$save_javascript . we_message_reporting::getShowMessageCall(g_l('thumbnails', "[saved]"), we_message_reporting::WE_MESSAGE_NOTICE) .
				"self.location = '" . WE_INCLUDES_DIR . "we_editors/we_thumbnails.php?id=" . $_REQUEST["edited_id"] . "';");
	}

	print STYLESHEET . $save_javascript . '</head>' .
		we_html_element::htmlBody(array("class" => "weDialogBody"), build_dialog("saved")) . "</html>";
} else{
	$_form = we_html_element::htmlForm(array("name" => "we_form", "method" => "get", "action" => $_SERVER["SCRIPT_NAME"]), we_html_element::htmlHidden(array("name" => "save_thumbnails", "value" => "false")) . render_dialog());

	print STYLESHEET . '</head>' .
		we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "init()"), $_form) . "</html>";
}

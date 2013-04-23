<?php

/**
 * webEdition CMS
 *
 * $Rev: 5920 $
 * $Author: lukasimhof $
 * $Date: 2013-03-04 13:56:51 +0100 (Mon, 04 Mar 2013) $
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
include_once(WE_INCLUDES_PATH . 'we_editors/we_preferences_config.inc.php');

//NOTE: only add "newConf" to entries set in $GLOBALS['configs']. All "temporary" entries should remain in main-Request-Scope

we_html_tools::protect();

$yuiSuggest = &weSuggest::getInstance();

define('secondsDay', 86400);
define('secondsWeek', 604800);
define('secondsYear', 31449600);


// Check which group of settings to work with
if(!isset($_REQUEST['setting']) || $_REQUEST['setting'] == ''){
	$_REQUEST['setting'] = 'ui';
}

$save_javascript = '';
$GLOBALS['editor_reloaded'] = false;
$email_saved = true;
$tabname = isset($_REQUEST['tabname']) && $_REQUEST['tabname'] != '' ? $_REQUEST['tabname'] : 'setting_ui';

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
function create_dialog($name, $title, $content, $expand = -1, $show_text = '', $hide_text = '', $cookie = false, $JS = ''){
	$_output = ($JS != '' ? $JS : '') .
		($expand != -1 ? we_multiIconBox::getJS() : '');

	// Return HTML code of dialog
	return $_output . we_multiIconBox::getHTML($name, '100%', $content, 30, '', $expand, $show_text, $hide_text, $cookie != false ? ($cookie == 'down') : $cookie);
}

function getColorInput($name, $value, $disabled = false, $width = 20, $height = 20){
	return we_html_tools::hidden($name, $value) . '<table cellpadding="0" cellspacing="0" style="border:1px solid grey;margin:2px 0;"><tr><td' . ($disabled ? ' class="disabled"' : '') . ' id="color_' . $name . '" ' . ($value ? (' style="background-color:' . $value . ';"') : '') . '><a style="cursor:' . ($disabled ? "default" : "pointer") . ';" href="javascript:if(document.getElementById(&quot;color_' . $name . '&quot;).getAttribute(&quot;class&quot;)!=&quot;disabled&quot;) {we_cmd(\'openColorChooser\',\'' . $name . '\',document.we_form.elements[\'' . $name . '\'].value,&quot;opener.setColorField(\'' . $name . '\');&quot;);}">' . we_html_tools::getPixel($width, $height) . '</a></td></tr></table>';
}

/**
 * This functions return either the saved option or the changed one.
 *
 * @param          string                                  $settingvalue
 *
 * @see            return_value()
 *
 * @return         unknown
 */
function get_value($settingname){
	$all = explode('-', $settingname);
	$settingname = $all[0];
	switch($settingname){
		case 'use_jupload':
		case 'specify_jeditor_colors':
			return (isset($_SESSION['prefs'][$settingname]) ? $_SESSION['prefs'][$settingname] : 1);

		case 'seem_start_type':
			if(($_SESSION['prefs']['seem_start_type'] == 'document' || $_SESSION['prefs']['seem_start_type'] == 'object') && $_SESSION['prefs']['seem_start_file'] == 0){
				return 'cockpit';
			}
			return $_SESSION['prefs']['seem_start_type'];

		case 'locale_locales':
			we_loadLanguageConfig();
			return getWeFrontendLanguagesForBackend();

		case 'locale_default':
			we_loadLanguageConfig();
			return $GLOBALS['weDefaultFrontendLanguage'];

		case 'proxy_proxy':
			// Check for settings file
			if(file_exists(WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php')){
				include_once(WEBEDITION_PATH . 'liveUpdate/includes/proxysettings.inc.php');
			}
			return defined('WE_PROXYHOST');

		case 'message_reporting':
			return (isset($_SESSION['prefs']['message_reporting']) && $_SESSION['prefs']['message_reporting']) ? $_SESSION['prefs']['message_reporting'] : (we_message_reporting::WE_MESSAGE_ERROR + we_message_reporting::WE_MESSAGE_WARNING + we_message_reporting::WE_MESSAGE_NOTICE);

		default:
			if(isset($GLOBALS['configs']['user'][$settingname])){
				if(isset($all[1])){
					//handle subkey
					$tmp = @unserialize(isset($_SESSION['prefs'][$settingname]) ? $_SESSION['prefs'][$settingname] : $GLOBALS['configs']['user'][$settingname][0]);
					return isset($tmp[$all[1]]) ? $tmp[$all[1]] : 0;
				} else{
					return (isset($_SESSION['prefs'][$settingname]) ? $_SESSION['prefs'][$settingname] : $GLOBALS['configs']['user'][$settingname][0]);
				}
			}

			//if not found in global_config or other config - simply return '' - this should not happen - should we return something more error-specific?
			return defined($settingname) ?
				constant($settingname) :
				(isset($GLOBALS['configs']['global'][$settingname]) ? $GLOBALS['configs']['global'][$settingname][1] : (
					isset($GLOBALS['configs']['other'][$settingname]) ? $GLOBALS['configs']['other'][$settingname][0] : '')
				);
	}
}

/**
 * This functions saves an option in the current session.
 *
 * @param          string                                  $settingvalue
 * @param          string                                  $settingname
 *
 * @see            save_all_values
 * @see            we_base_preferences::changeSourceCode()
 *
 * @return         bool
 */
function remember_value($settingvalue, $settingname, $comment = ''){
	global $save_javascript, $email_saved, $DB_WE;

	if(isset($GLOBALS['configs']['user'][$settingname]) && $settingvalue == null){ //checkboxes -> unchecked - all other values are set by the form
		$settingvalue = 0;
	}

	//check for user-setting
	switch($settingname){
		default:
			if(isset($GLOBALS['configs']['user'][$settingname])){
				$_SESSION['prefs'][$settingname] = ($settingvalue == null ? 0 : $settingvalue);
			} else{
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;
		case 'seem_start_file'://don't do anything here
			return;
		case 'seem_start_type':
			switch($settingvalue){
				case 'document':
					$tmp = $_SESSION['prefs']['seem_start_file'] = $_REQUEST['seem_start_document'];
					$_SESSION['prefs'][$settingname] = ($tmp ? $settingvalue : 'cockpit');
					break;
				case 'object':
					$tmp = $_SESSION['prefs']['seem_start_file'] = $_REQUEST['seem_start_object'];
					$_SESSION['prefs'][$settingname] = ($tmp ? $settingvalue : 'cockpit');
					break;
				default:
					$_SESSION['prefs'][$settingname] = $settingvalue;
					break;
			}
			return;
		case 'sizeOpt':
			if($settingvalue == 0){
				$_SESSION['prefs']['weWidth'] = 0;
				$_SESSION['prefs']['weHeight'] = 0;
				$_SESSION['prefs']['sizeOpt'] = 0;
			} else if(($settingvalue == 1) && (isset($_REQUEST['newconf']['weWidth']) && is_numeric($_REQUEST['newconf']['weWidth'])) && (isset($_REQUEST['newconf']['weHeight']) && is_numeric($_REQUEST['newconf']['weHeight']))){
				$_SESSION['prefs']['sizeOpt'] = 1;
			}

			return;

		case 'weWidth':
			if($_SESSION['prefs']['sizeOpt'] == 1){
				$_generate_java_script = ($_SESSION['prefs']['weWidth'] != $settingvalue);

				$_SESSION['prefs']['weWidth'] = $settingvalue;

				if($_generate_java_script){
					$save_javascript .= "
							parent.opener.top.resizeTo(" . $settingvalue . ", " . $_REQUEST['newconf']["weHeight"] . ");
							parent.opener.top.moveTo((screen.width / 2) - " . ($settingvalue / 2) . ", (screen.height / 2) - " . ($_REQUEST['newconf']["weHeight"] / 2) . ");
						";
				}
			}
			return;

		case 'weHeight':
			if($_SESSION['prefs']['sizeOpt'] == 1){
				$_SESSION['prefs'][$settingname] = $settingvalue;
			}
			return;

		case 'editorFont':
			if(intval($settingvalue) == 0){
				$_SESSION['prefs']['editorFontname'] = 'none';
				$_SESSION['prefs']['editorFontsize'] = -1;
				$_SESSION['prefs']['editorFont'] = 0;
			} else if(($settingvalue == 1) && isset($_REQUEST['newconf']['editorFontname']) && isset($_REQUEST['newconf']['editorFontsize'])){
				$_SESSION['prefs']['editorFont'] = 1;
			}

			if(!$GLOBALS['editor_reloaded']){
				$GLOBALS['editor_reloaded'] = true;

				// editor font has changed - mark all editors to reload!
				$save_javascript .= '
					if (!_multiEditorreload) {
						var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
							for (frameId in _usedEditors) {

								if ( (_usedEditors[frameId].getEditorEditorTable() == "' . TEMPLATES_TABLE . '" || _usedEditors[frameId].getEditorEditorTable() == "' . FILE_TABLE . '") &&
									_usedEditors[frameId].getEditorEditPageNr() == ' . WE_EDITPAGE_CONTENT . ' ) {

									if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
										_usedEditors[frameId].setEditorReloadNeeded(true);
										_usedEditors[frameId].setEditorIsActive(true);

									} else {
										_usedEditors[frameId].setEditorReloadNeeded(true);
									}
								}
							}
					}
					_multiEditorreload = true;';
			}
			return;

		case 'editorCodecompletion':
			$_SESSION['prefs'][$settingname] = is_array($settingvalue) ? serialize($settingvalue) : '';
			return;
		case 'editorFontname':
		case 'editorFontsize':
			if($_SESSION['prefs']['editorFont'] == 1){
				$_SESSION['prefs'][$settingname] = $settingvalue;
			}

			return;

		case 'editorTooltipFont':
			if(intval($settingvalue) == 0){
				$_SESSION['prefs']['editorTooltipFontname'] = 'none';
				$_SESSION['prefs']['editorTooltipFontsize'] = -1;
				$_SESSION['prefs']['editorTooltipFont'] = 0;
			} else if(($settingvalue == 1) && isset($_REQUEST['newconf']['editorTooltipFontname']) && isset($_REQUEST['newconf']['editorTooltipFontsize'])){
				$_SESSION['prefs']['editorTooltipFont'] = 1;
			}

			if(!$GLOBALS['editor_reloaded']){
				$GLOBALS['editor_reloaded'] = true;

				// editor tooltip font has changed - mark all editors to reload!
				$save_javascript .= '
					if (!_multiEditorreload) {
						var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
							for (frameId in _usedEditors) {

								if ( (_usedEditors[frameId].getEditorEditorTable() == "' . TEMPLATES_TABLE . '" || _usedEditors[frameId].getEditorEditorTable() == "' . FILE_TABLE . '") &&
									_usedEditors[frameId].getEditorEditPageNr() == ' . WE_EDITPAGE_CONTENT . ' ) {

									if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
										_usedEditors[frameId].setEditorReloadNeeded(true);
										_usedEditors[frameId].setEditorIsActive(true);

									} else {
										_usedEditors[frameId].setEditorReloadNeeded(true);
									}
								}
							}
					}
					_multiEditorreload = true;
					';
			}

			return;

		case 'Language': //Handle both
			$_SESSION['prefs'][$settingname] = $settingvalue;
			$_SESSION['prefs']['BackendCharset'] = $_REQUEST['newconf']['BackendCharset'];


			if($settingvalue != $GLOBALS['WE_LANGUAGE'] || $_REQUEST['newconf']['BackendCharset'] != $GLOBALS['WE_BACKENDCHARSET']){

				// complete webEdition reload: anpassen nach Wegfall der Frames
				$save_javascript .= "
						// reload current document => reload all open Editors on demand
						var _usedEditors =  top.opener.weEditorFrameController.getEditorsInUse();
						for (frameId in _usedEditors) {

							if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
								_usedEditors[frameId].setEditorReloadAllNeeded(true);
								_usedEditors[frameId].setEditorIsActive(true);

							} else {
								_usedEditors[frameId].setEditorReloadAllNeeded(true);
							}
						}
						_multiEditorreload = true;";
			}

		case 'locale_locales':
			return;
		case 'locale_default':
			if(isset($_REQUEST['newconf']['locale_locales']) && isset($_REQUEST['newconf']['locale_default'])){
				we_writeLanguageConfig($_REQUEST['newconf']['locale_default'], explode(",", $_REQUEST['newconf']['locale_locales']));
			}
			return;

		case 'WE_COUNTRIES_TOP':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, implode(',', array_keys($_REQUEST['newconf']['countries'], 2)), true, $comment);
			return;

		case 'WE_COUNTRIES_SHOWN':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, implode(',', array_keys($_REQUEST['newconf']['countries'], 1)), true, $comment);
			return;

		case 'WE_SEEM':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			if(intval($settingvalue) == constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, ($settingvalue == 1 ? 0 : 1), true, $comment);
			}
			return;

		case 'WE_LOGIN_HIDEWESTATUS':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			if($settingvalue != constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;
		case 'WE_LOGIN_WEWINDOW':
			if(constant($settingname) != $settingvalue){
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'SIDEBAR_DISABLED':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			if($settingvalue != SIDEBAR_DISABLED){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'SIDEBAR_DISABLED', $settingvalue, true, $comment);
			}

			$_sidebar_show_on_startup = ((isset($_REQUEST['newconf']['SIDEBAR_SHOW_ON_STARTUP']) && $_REQUEST['newconf']['SIDEBAR_SHOW_ON_STARTUP'] != null) ? $_REQUEST['newconf']['SIDEBAR_SHOW_ON_STARTUP'] : 0);
			if(SIDEBAR_SHOW_ON_STARTUP != $_sidebar_show_on_startup){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'newconf[SIDEBAR_SHOW_ON_STARTUP]', $_sidebar_show_on_startup);
			}

			$_sidebar_document = ((isset($_REQUEST['newconf']['newconf[SIDEBAR_DEFAULT_DOCUMENT]']) && $_REQUEST['newconf']['SIDEBAR_DEFAULT_DOCUMENT'] != null) ? $_REQUEST['newconf']['SIDEBAR_DEFAULT_DOCUMENT'] : 0);
			if(SIDEBAR_DEFAULT_DOCUMENT != $_sidebar_document){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'newconf[SIDEBAR_DEFAULT_DOCUMENT]', $_sidebar_document);
			}

			$_sidebar_width = ((isset($_REQUEST['newconf']['SIDEBAR_DEFAULT_WIDTH']) && $_REQUEST['newconf']['SIDEBAR_DEFAULT_WIDTH'] != null) ? $_REQUEST['newconf']['SIDEBAR_DEFAULT_WIDTH'] : 0);
			if(SIDEBAR_DEFAULT_WIDTH != $_sidebar_width){
				$_file = we_base_preferences::changeSourceCode('define', $_file, 'newconf[SIDEBAR_DEFAULT_WIDTH]', $_sidebar_width);
			}

			return;

		case 'DEFAULT_STATIC_EXT':
		case 'DEFAULT_DYNAMIC_EXT':
		case 'DEFAULT_HTML_EXT':
			if(constant($settingname) != $settingvalue){
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		//FORMMAIL RECIPIENTS
		case 'formmail_values':
			if((isset($_REQUEST['newconf']['formmail_values']) && $_REQUEST['newconf']['formmail_values'] != '') || (isset($_REQUEST['newconf']['formmail_deleted']) && $_REQUEST['newconf']['formmail_deleted'] != '')){
				$_recipients = explode('<##>', $_REQUEST['newconf']['formmail_values']);
				if(!empty($_recipients)){
					foreach($_recipients as $i => $_recipient){
						$_single_recipient = explode('<#>', $_recipient);

						if(isset($_single_recipient[0]) && ($_single_recipient[0] == '#')){
							if(isset($_single_recipient[1]) && $_single_recipient[1]){
								$DB_WE->query('INSERT INTO ' . RECIPIENTS_TABLE . ' (Email) VALUES("' . $DB_WE->escape($_single_recipient[1]) . '")');
							}
						} else{
							if(isset($_single_recipient[1]) && isset($_single_recipient[0]) && $_single_recipient[1] && $_single_recipient[0]){
								$DB_WE->query('UPDATE ' . RECIPIENTS_TABLE . ' SET Email="' . $DB_WE->escape($_single_recipient[1]) . '" WHERE ID=' . intval($_single_recipient[0]));
							}
						}
					}
				}
			}

			return;

		case 'formmail_deleted':
			if(isset($_REQUEST['newconf'][$settingname]) && $_REQUEST['newconf'][$settingname] != ''){
				$_formmail_deleted = explode(',', $_REQUEST['newconf'][$settingname]);
				foreach($_formmail_deleted as $del){
					$DB_WE->query('DELETE FROM ' . RECIPIENTS_TABLE . ' WHERE ID=' . intval($del));
				}
			}
			return;

		case 'active_integrated_modules':
			$GLOBALS['config_files']['active_integrated_modules']['content'] = '<?php
$GLOBALS[\'_we_active_integrated_modules\'] = array(
\'' . implode("',\n'", $_REQUEST['newconf']['active_integrated_modules']) . '\'
);';
			return;

		case 'useproxy':
			if($settingvalue == 1){
				// Create/overwrite proxy settings file
				we_base_preferences::setConfigContent('proxysettings', '<?php
	define(\'WE_PROXYHOST\', "' . ((isset($_REQUEST['newconf']["proxyhost"]) && $_REQUEST['newconf']["proxyhost"] != null) ? $_REQUEST['newconf']["proxyhost"] : '') . '");
	define(\'WE_PROXYPORT\', "' . ((isset($_REQUEST['newconf']["proxyport"]) && $_REQUEST['newconf']["proxyport"] != null) ? $_REQUEST['newconf']["proxyport"] : '') . '");
	define(\'WE_PROXYUSER\', "' . ((isset($_REQUEST['newconf']["proxyuser"]) && $_REQUEST['newconf']["proxyuser"] != null) ? $_REQUEST['newconf']["proxyuser"] : '') . '");
	define(\'WE_PROXYPASSWORD\', "' . ((isset($_REQUEST['newconf']["proxypass"]) && $_REQUEST['newconf']["proxypass"] != null) ? $_REQUEST['newconf']["proxypass"] : '') . '");'
				);
			} else{
				// Delete proxy settings file
				if(file_exists(WEBEDITION_PATH . "liveUpdate/includes/proxysettings.inc.php")){
					unlink(WEBEDITION_PATH . "liveUpdate/includes/proxysettings.inc.php");
				}
				we_base_preferences::unsetConfig('proxysettings');
			}
			return;

		case 'proxyhost':
		case 'proxyport':
		case 'proxyuser':
		case 'proxypass':
			return;

		// ADVANCED
		case 'DB_CONNECT':
			$_file = &$GLOBALS['config_files']['conf']['content'];
			$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue);
			return;

		case 'DB_SET_CHARSET':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];

			if(!defined($settingname) || $settingvalue != constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'useauth':
			$_file = &$GLOBALS['config_files']['conf']['content'];
			if($settingvalue == 1){
				// enable
				if(!(defined("HTTP_USERNAME")) || !(defined("HTTP_PASSWORD"))){
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_USERNAME', 'myUsername', false);
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_PASSWORD', 'myPassword', false);
				}

				$un = defined("HTTP_USERNAME") ? HTTP_USERNAME : "";
				$pw = defined("HTTP_PASSWORD") ? HTTP_PASSWORD : "";
				if($un != $_REQUEST['newconf']["HTTP_USERNAME"] || $pw != $_REQUEST['newconf']["HTTP_PASSWORD"]){

					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_USERNAME', ((isset($_REQUEST['newconf']["HTTP_USERNAME"]) && $_REQUEST['newconf']["HTTP_USERNAME"] != null) ? $_REQUEST['newconf']["HTTP_USERNAME"] : ''));
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_PASSWORD', ((isset($_REQUEST['newconf']["HTTP_PASSWORD"]) && $_REQUEST['newconf']["HTTP_PASSWORD"] != null) ? $_REQUEST['newconf']["HTTP_PASSWORD"] : ''));
				}
			} else{
				// disable
				if(defined("HTTP_USERNAME") || defined("HTTP_PASSWORD")){
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_USERNAME', 'myUsername', false);
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'HTTP_PASSWORD', 'myPassword', false);
				}
			}

			return;

		case 'HTTP_USERNAME':
		case 'HTTP_PASSWORD':
			return;

		//ERROR HANDLING
		case 'WE_ERROR_HANDLER':
		case 'WE_ERROR_NOTICES':
		case 'WE_ERROR_DEPRECATED':
		case 'WE_ERROR_WARNINGS':
		case 'WE_ERROR_ERRORS':
		case 'WE_ERROR_SHOW':
		case 'WE_ERROR_LOG':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];

			if($settingvalue != constant($settingname)){
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;

		case 'WE_ERROR_MAIL':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];

			if($settingvalue == 0 && WE_ERROR_MAIL == 1){
				$_file = we_base_preferences::changeSourceCode('define', $_file, "WE_ERROR_MAIL", 0, true, $comment);
			} else if($settingvalue == 1 && WE_ERROR_MAIL == 0){
				$_file = we_base_preferences::changeSourceCode('define', $_file, "WE_ERROR_MAIL", 1, true, $comment);
			}

			return;

		case 'WE_ERROR_MAIL_ADDRESS':
			$_file = &$GLOBALS['config_files']['conf_global']['content'];
			
			if(WE_ERROR_MAIL_ADDRESS != $settingvalue){
				$_file = we_base_preferences::changeSourceCode('define', $_file, "WE_ERROR_MAIL_ADDRESS", $settingvalue, true, $comment);
			}

			return;

		case 'ERROR_DOCUMENT_NO_OBJECTFILE':
			if(!defined($settingname) || constant($settingname) != $settingvalue){
				$_file = &$GLOBALS['config_files']['conf_global']['content'];
				$_file = we_base_preferences::changeSourceCode('define', $_file, $settingname, $settingvalue, true, $comment);
			}
			return;
	}
}

/**
 * This functions saves all options.
 *
 * @see            remember_value()
 *
 * @return         void
 */
function save_all_values(){
	we_base_preferences::loadConfigs();
	//set config to latest version
	$_REQUEST['newconf']['CONF_SAVED_VERSION'] = WE_SVNREV;
	// Second, change sourcecodes of the configfiles
	foreach($GLOBALS['configs'] as $name => $conf){
		foreach($conf as $key => $default){
			switch($name){
				case 'global'://no settings in session
					if(we_base_preferences::userIsAllowed($key)){
						remember_value(isset($_REQUEST['newconf'][$key]) ? $_REQUEST['newconf'][$key] : null, $key, $default[0]);
					}
					break;
				case 'user':
					remember_value(isset($_REQUEST['newconf'][$key]) ? $_REQUEST['newconf'][$key] : null, $key);
					break;
				default:
					if(we_base_preferences::userIsAllowed($key)){
						remember_value(isset($_REQUEST['newconf'][$key]) ? $_REQUEST['newconf'][$key] : null, $key);
					}
					break;
			}
		}
	}
	if(isset($_SESSION['weS']['versions']) && isset($_SESSION['weS']['versions']['logPrefs'])){
		$_SESSION['weS']['versions']['logPrefsChanged'] = array();
		foreach($_SESSION['weS']['versions']['logPrefs'] as $k => $v){
			if(isset($_REQUEST['newconf'][$k])){
				if($_SESSION['weS']['versions']['logPrefs'][$k] != $_REQUEST['newconf'][$k]){
					$_SESSION['weS']['versions']['logPrefsChanged'][$k] = $_REQUEST['newconf'][$k];
				}
			} elseif($_SESSION['weS']['versions']['logPrefs'][$k] != ""){
				$_SESSION['weS']['versions']['logPrefsChanged'][$k] = "";
			}
		}

		if(!empty($_SESSION['weS']['versions']['logPrefsChanged'])){
			$versionslog = new versionsLog();
			$versionslog->saveVersionsLog($_SESSION['weS']['versions']['logPrefsChanged'], versionsLog::VERSIONS_PREFS);
		}
		unset($_SESSION['weS']['versions']['logPrefs']);
		unset($_SESSION['weS']['versions']['logPrefsChanged']);
	}

	//SAVE CHANGES
	// Third save all changes of the config files
	we_base_preferences::saveConfigs();
}

/**
 * This builds every single dialog (of a tab).
 *
 * @param          $selected_setting                       string              (optional)
 *
 * @see            render_dialog()
 *
 * @return         string
 */
function build_dialog($selected_setting = 'ui'){
	global $DB_WE;
	$yuiSuggest = & weSuggest::getInstance();
	$trueFalseArray = array('true' => 'true', 'false' => 'false');

	switch($selected_setting){
		case 'save':

			return create_dialog('', g_l('prefs', '[save_wait]'), array(
				array('headline' => '', 'html' => g_l('prefs', '[save]'), 'space' => 0)
			));

		case 'saved'://SAVED SUCCESSFULLY DIALOG
			return create_dialog('', g_l('prefs', '[saved_successfully]'), array(
				array('headline' => '', 'html' => g_l('prefs', '[saved]'), 'space' => 0)
			));

		case 'ui':
			//LANGUAGE

			$_settings = array();

			//	Look which languages are installed ...
			$_language_directory = dir(WE_INCLUDES_PATH . 'we_language');

			while(false !== ($entry = $_language_directory->read())) {
				if($entry != '.' && $entry != '..'){
					if(is_dir(WE_INCLUDES_PATH . 'we_language/' . $entry)){
						$_language[$entry] = $entry;
					}
				}
			}
			global $_languages;

			if(!empty($_language)){ // Build language select box
				$_languages = new we_html_select(array('name' => 'newconf[Language]', 'class' => 'weSelect', 'onChange' => "document.getElementById('langnote').style.display='block'"));
				foreach($_language as $key => $value){
					$_languages->addOption($key, $value);
				}
				$_languages->selectOption(get_value('Language'));
				// Lang notice
				$langNote = '<div id="langnote" style="padding: 5px; background-color: rgb(221, 221, 221); width: 190px; display:none">
<table border="0" cellpadding="2" width="100%">
<tbody>
<tr>
<td style="padding-right: 10px;" valign="top">
  <img src="' . IMAGE_DIR . 'info_small.gif" height="22" width="20" />
</td>
<td class="middlefont">' . g_l('prefs', '[language_notice]') . '
</td>
</tr>
</tbody>
</table>
</div>';
				// Build dialog
				$_settings[] = array('headline' => g_l('prefs', '[choose_language]'), 'html' => $_languages->getHtml() . '<br><br>' . $langNote, 'space' => 200, 'noline' => 1);
			} else{ // Just one Language Installed, no select box needed
				foreach($_language as $key => $value){
					$_languages = $value;
				}
				// Build dialog
				$_settings[] = array('headline' => g_l('prefs', '[choose_language]'), 'html' => $_languages, 'space' => 200, 'noline' => 1);
			}

			$BackendCharset = new we_html_select(array('name' => 'newconf[BackendCharset]', 'class' => 'weSelect', 'onChange' => "document.getElementById('langnote').style.display='block'"));
			$c = charsetHandler::getAvailCharsets();
			foreach($c as $char){
				$BackendCharset->addOption($char, $char);
			}
			$BackendCharset->selectOption(get_value('BackendCharset'));
			$_settings[] = array('headline' => g_l('prefs', '[choose_backendcharset]'), 'html' => $BackendCharset->getHtml() . '<br><br>' . $langNote, 'space' => 200);


			// DEFAULT CHARSET
			if(we_base_preferences::userIsAllowed('DEFAULT_CHARSET')){
				$_charsetHandler = new charsetHandler();
				$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
				$charset = $GLOBALS['WE_BACKENDCHARSET'];
				$GLOBALS['weDefaultCharset'] = get_value('DEFAULT_CHARSET');
				$_defaultCharset = we_html_tools::htmlTextInput('newconf[DEFAULT_CHARSET]', 8, $GLOBALS['weDefaultCharset'], 255, '', 'text', 100);
				$_defaultCharsetChooser = we_html_tools::htmlSelect('DefaultCharsetSelect', $_charsets, 1, $GLOBALS['weDefaultCharset'], false, "onChange=\"document.forms[0].elements['newconf[DEFAULT_CHARSET]'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;\"", "value", 100, "defaultfont", false);
				$DEFAULT_CHARSET = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_defaultCharset . '</td><td>' . $_defaultCharsetChooser . '</td></tr></table>';

				$_settings[] = array(
					'headline' => g_l('prefs', '[default_charset]'),
					'space' => 200,
					'html' => $DEFAULT_CHARSET
				);
			}

			//AMOUNT COLUMNS IN COCKPIT
			$cockpit_amount_columns = new we_html_select(array('name' => 'newconf[cockpit_amount_columns]', 'class' => 'weSelect'));
			for($i = 1; $i <= 10; $i++){
				$cockpit_amount_columns->addOption($i, $i);
			}
			$cockpit_amount_columns->selectOption(get_value('cockpit_amount_columns'));
			$_settings[] = array('headline' => g_l('prefs', '[cockpit_amount_columns]'), 'html' => $cockpit_amount_columns->getHtml(), 'space' => 200);

			/*			 * ***************************************************************
			 * SEEM
			 * *************************************************************** */

			// Generate needed JS
			$_needed_JavaScript = we_button::create_state_changer();

			if(we_base_preferences::userIsAllowed('WE_SEEM')){
				// Build maximize window
				$_seem_disabler = we_forms::checkbox(1, get_value('WE_SEEM') == 0 ? 1 : 0, 'newconf[WE_SEEM]', g_l('prefs', '[seem_deactivate]'));

				// Build dialog if user has permission
				$_settings[] = array('headline' => g_l('prefs', '[seem]'), 'html' => $_seem_disabler, 'space' => 200);
			}

			// SEEM start document
			if(we_base_preferences::userIsAllowed('seem_start_type')){
				// Generate needed JS
				$_needed_JavaScript .= we_html_element::jsElement("
							function selectSidebarDoc() {
								myWind = false;

								for (k = parent.opener.top.jsWindow_count; k > -1; k--) {
									eval('if (parent.opener.top.jsWindow' + k + 'Object) {' +
										 ' if (parent.opener.top.jsWindow' + k + \"Object.ref == 'preferences') {\" +
										 '     myWind = parent.opener.top.jsWindow' + k + \"Object.wind;\" +
										 \"     myWindStr = 'top.jsWindow\" + k + \"Object.wind';\" +
										 ' }' +
										 '}');

									if (myWind) {
										break;
									}
								}
								parent.opener.top.we_cmd('openDocselector',document.getElementsByName('newconf[SIDEBAR_DEFAULT_DOCUMENT]').value,'" . FILE_TABLE . "',myWindStr + '.content.document.getElementsByName(\'newconf[SIDEBAR_DEFAULT_DOCUMENT]\')[0].value',myWindStr + '.content.document.getElementsByName(\'ui_sidebar_file_name\')[0].value','','" . session_id() . "', '', 'text/webedition'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");
							}

							function select_seem_start() {
								myWind = false;

								for (k = parent.opener.top.jsWindow_count; k > -1; k--) {
									eval('if (parent.opener.top.jsWindow' + k + 'Object) {' +
										 '	if (parent.opener.top.jsWindow' + k + \"Object.ref == 'preferences') {\" +
										 '		myWind = parent.opener.top.jsWindow' + k + \"Object.wind;\" +
										 \"		myWindStr = 'top.jsWindow\" + k + \"Object.wind';\" +
										 '	}' +
										 '}');

									if (myWind) {
										break;
									}
								}
								if(document.getElementById('seem_start_type').value == 'object') {
								" .
						//FIXME frames['content'] will probably not work here
						(defined("OBJECT_FILES_TABLE") ?
							"parent.opener.top.we_cmd('openDocselector', document.getElementsByName('seem_start_object')[0].value, '" . OBJECT_FILES_TABLE . "', myWindStr + '.content.document.getElementsByName(\'seem_start_object\')[0].value', myWindStr + '.content.document.getElementsByName(\'seem_start_object_name\')[0].value', '', '" . session_id() . "', '', 'objectFile',1);" : '') .
						"} else {
									parent.opener.top.we_cmd('openDocselector', document.getElementsByName('seem_start_document')[0].value, '" . FILE_TABLE . "', myWindStr + '.content.document.getElementsByName(\'seem_start_document\')[0].value', myWindStr + '.content.document.getElementsByName(\'seem_start_document_name\')[0].value', '', '" . session_id() . "', '', 'text/webedition'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");
								}
							}
							function show_seem_chooser(val) {
								if(val == 'document') {
									if(!!document.getElementById('selectordummy')) {
										document.getElementById('selectordummy').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_object')) {
										document.getElementById('seem_start_object').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_weapp')) {
										document.getElementById('seem_start_weapp').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_document')) {
										document.getElementById('seem_start_document').style.display = 'block';
									}

							" .
						(defined("OBJECT_FILES_TABLE") ?
							"} else if(val == 'object') {
									if(!!document.getElementById('selectordummy')) {
										document.getElementById('selectordummy').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_weapp')) {
										document.getElementById('seem_start_weapp').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_document')) {
										document.getElementById('seem_start_document').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_object')) {
										document.getElementById('seem_start_object').style.display = 'block';
									}
							" : '') . "
								} else if(val == 'weapp'){
									if(!!document.getElementById('selectordummy')) {
										document.getElementById('selectordummy').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_document')) {
										document.getElementById('seem_start_document').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_weapp')) {
										document.getElementById('seem_start_weapp').style.display = 'block';
									}
									if(!!document.getElementById('seem_start_object')) {
										document.getElementById('seem_start_object').style.display = 'none';
									}
								} else {
									if(!!document.getElementById('selectordummy')) {
										document.getElementById('selectordummy').style.display = 'block';
									}
									if(!!document.getElementById('seem_start_document')) {
										document.getElementById('seem_start_document').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_weapp')) {
										document.getElementById('seem_start_weapp').style.display = 'none';
									}
									if(!!document.getElementById('seem_start_object')) {
										document.getElementById('seem_start_object').style.display = 'none';
									}

								}
							}");

				// Cockpit
				$_document_path = $_object_path = '';
				$_document_id = $_object_id = 0;

				switch(get_value('seem_start_type')){
					default:
						$_seem_start_type = '0';
						break;
					case 'cockpit':
						$_SESSION['prefs']['seem_start_file'] = 0;
						$_SESSION['prefs']['seem_start_weapp'] = '';
						$_seem_start_type = 'cockpit';
						break;

					// Object
					case 'object':
						$_seem_start_type = 'object';
						if(get_value('seem_start_file') != 0){
							$_object_id = get_value('seem_start_file');
							$_get_object_paths = getPathsFromTable(OBJECT_FILES_TABLE, '', FILE_ONLY, $_object_id);

							if(isset($_get_object_paths[$_object_id])){ //	seeMode start file exists
								$_object_path = $_get_object_paths[$_object_id];
							}
						}
						break;
					case 'weapp':
						$_seem_start_type = 'weapp';
						if(get_value('seem_start_weapp') != ''){
							$_seem_start_weapp = get_value('seem_start_weapp');
						}

						break;
					// Document
					case 'document':
						$_seem_start_type = 'document';
						if(get_value('seem_start_file') != 0){
							$_document_id = get_value('seem_start_file');
							$_get_document_paths = getPathsFromTable(FILE_TABLE, '', FILE_ONLY, $_document_id);

							if(isset($_get_document_paths[$_document_id])){ //	seeMode start file exists
								$_document_path = $_get_document_paths[$_document_id];
							}
						}
						break;
				}

				$_start_type = new we_html_select(array('name' => 'newconf[seem_start_type]', 'class' => 'weSelect', 'id' => 'seem_start_type', 'onchange' => "show_seem_chooser(this.value);"));

				$showStartType = false;
				$permitedStartTypes = array('');
				$_start_type->addOption('0', '-');
				$_seem_cockpit_selectordummy = "<div id='selectordummy' style='height:" . (we_base_browserDetect::isIE() ? '33px' : '24px') . ";'>&nbsp;</div>";
				if(we_hasPerm('CAN_SEE_QUICKSTART')){
					$_start_type->addOption('cockpit', g_l('prefs', '[seem_start_type_cockpit]'));
					$showStartType = true;
					$permitedStartTypes[] = 'cockpit';
				}

				$_seem_document_chooser = '';
				if(we_hasPerm('CAN_SEE_DOCUMENTS')){
					$_start_type->addOption('document', g_l('prefs', '[seem_start_type_document]'));
					$showStartType = true;
					// Build SEEM select start document chooser

					$yuiSuggest->setAcId('Doc');
					$yuiSuggest->setContentType('folder,text/webEdition,text/html,image/*');
					$yuiSuggest->setInput('seem_start_document_name', $_document_path, '', get_value('seem_start_file'));
					$yuiSuggest->setMaxResults(20);
					$yuiSuggest->setMayBeEmpty(false);
					$yuiSuggest->setResult('seem_start_document', $_document_id);
					$yuiSuggest->setSelector('Docselector');
					$yuiSuggest->setWidth(150);
					$yuiSuggest->setSelectButton(we_button::create_button('select', 'javascript:select_seem_start()', true, 100, 22, '', '', get_value('WE_SEEM'), false), 10);
					$yuiSuggest->setContainerWidth(259);

					$_seem_document_chooser = we_button::create_button_table(array($yuiSuggest->getHTML()), 0, array('id' => 'seem_start_document', 'style' => 'display:none'));
					$permitedStartTypes[] = 'document';
				}
				$_seem_object_chooser = '';
				if(defined('OBJECT_FILES_TABLE') && we_hasPerm('CAN_SEE_OBJECTFILES')){
					$_start_type->addOption('object', g_l('prefs', '[seem_start_type_object]'));
					$showStartType = true;
					// Build SEEM select start object chooser

					$yuiSuggest->setAcId('Obj');
					$yuiSuggest->setContentType('folder,objectFile');
					$yuiSuggest->setInput('seem_start_object_name', $_object_path, '', get_value('seem_start_file'));
					$yuiSuggest->setMaxResults(20);
					$yuiSuggest->setMayBeEmpty(false);
					$yuiSuggest->setResult('seem_start_object', $_object_id);
					$yuiSuggest->setSelector('Docselector');
					$yuiSuggest->setTable(OBJECT_FILES_TABLE);
					$yuiSuggest->setWidth(150);
					$yuiSuggest->setSelectButton(we_button::create_button('select', 'javascript:select_seem_start()', true, 100, 22, '', '', get_value('WE_SEEM'), false), 10);
					$yuiSuggest->setContainerWidth(259);

					$_seem_object_chooser = we_button::create_button_table(array($yuiSuggest->getHTML()), 0, array('id' => 'seem_start_object', 'style' => 'display:none'));
					$permitedStartTypes[] = 'object';
				}
				$_start_weapp = new we_html_select(array('name' => 'newconf[seem_start_weapp]', 'class' => 'weSelect', 'id' => 'seem_start_weapp', 'onchange' => 'top.content.setHot();'));
				$_tools = weToolLookup::getAllTools(true, false);
				foreach($_tools as $_tool){
					if(!$_tool['appdisabled'] && we_hasPerm($_tool['startpermission'])){
						$_start_weapp->addOption($_tool['name'], $_tool['text']);
					}
				}
				$_seem_weapp_chooser = '';
				if($_start_weapp->getOptionNum()){
					$_start_type->addOption('weapp', g_l('prefs', '[seem_start_type_weapp]'));
					if(isset($_seem_start_weapp) && $_seem_start_weapp != ''){
						$_start_weapp->selectOption($_seem_start_weapp);
					}
					$weAPPSelector = $_start_weapp->getHtml();
					$_seem_weapp_chooser = we_button::create_button_table(array($weAPPSelector), 10, array('id' => 'seem_start_weapp', 'style' => 'display:none'));
					$permitedStartTypes[] = 'weapp';
				}

				// Build final HTML code
				if($showStartType){
					if(in_array($_seem_start_type, $permitedStartTypes)){
						$_start_type->selectOption($_seem_start_type);
					} else{
						$_seem_start_type = $permitedStartTypes[0];
					}
					$_seem_html = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 2, 1);
					$_seem_html->setCol(0, 0, array('class' => 'defaultfont'), $_start_type->getHtml());
					$_seem_html->setCol(1, 0, array('style' => 'padding-top:5px;'), $_seem_cockpit_selectordummy . $_seem_document_chooser . $_seem_object_chooser . $_seem_weapp_chooser);
					$_settings[] = array('headline' => g_l('prefs', '[seem_startdocument]'), 'html' => $_seem_html->getHtml() . we_html_element::jsElement('show_seem_chooser("' . $_seem_start_type . '");'), "space" => 200);
				}

				// Build dialog if user has permission
			}

			/*			 * *******************************************************
			 * Sidebar
			 * ******************************************************* */
			if(we_base_preferences::userIsAllowed('SIDEBAR_DISABLED')){
				// Settings
				$_sidebar_disable = get_value('SIDEBAR_DISABLED');
				$_sidebar_show = ($_sidebar_disable) ? 'none' : 'block';

				$_sidebar_id = get_value('SIDEBAR_DEFAULT_DOCUMENT');
				$_sidebar_paths = getPathsFromTable(FILE_TABLE, '', FILE_ONLY, $_sidebar_id);
				$_sidebar_path = '';
				if(isset($_sidebar_paths[$_sidebar_id])){
					$_sidebar_path = $_sidebar_paths[$_sidebar_id];
				}

				// Enable / disable sidebar
				$_sidebar_disabler = we_forms::checkbox(1, $_sidebar_disable, 'newconf[SIDEBAR_DISABLED]', g_l('prefs', '[sidebar_deactivate]'), false, 'defaultfont', "document.getElementById('sidebar_options').style.display=(this.checked?'none':'block');");

				// Show on Startup
				$_sidebar_show_on_startup = we_forms::checkbox(1, get_value('SIDEBAR_SHOW_ON_STARTUP'), 'newconf[SIDEBAR_SHOW_ON_STARTUP]', g_l('prefs', '[sidebar_show_on_startup]'), false, 'defaultfont', '');

				// Sidebar width
				$_sidebar_width = we_html_tools::htmlTextInput('newconf[SIDEBAR_DEFAULT_WIDTH]', 8, get_value('SIDEBAR_DEFAULT_WIDTH'), 255, "onchange=\"if ( isNaN( this.value ) ||  parseInt(this.value) < 100 ) { this.value=100; };\"", 'text', 150);
				$_sidebar_width_chooser = we_html_tools::htmlSelect('tmp_sidebar_width', array('' => '', 100 => 100, 150 => 150, 200 => 200, 250 => 250, 300 => 300, 350 => 350, 400 => 400), 1, '', false, "onChange=\"document.forms[0].elements['newconf[SIDEBAR_DEFAULT_WIDTH]'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;\"", "value", 100, "defaultfont");

				// Sidebar document
				$_sidebar_document_button = we_button::create_button('select', 'javascript:selectSidebarDoc()');

				$yuiSuggest->setAcId('SidebarDoc');
				$yuiSuggest->setContentType('folder,text/webEdition');
				$yuiSuggest->setInput('ui_sidebar_file_name', $_sidebar_path);
				$yuiSuggest->setMaxResults(20);
				$yuiSuggest->setMayBeEmpty(true);
				$yuiSuggest->setResult('newconf[SIDEBAR_DEFAULT_DOCUMENT]', $_sidebar_id);
				$yuiSuggest->setSelector('Docselector');
				$yuiSuggest->setWidth(150);
				$yuiSuggest->setSelectButton($_sidebar_document_button, 10);
				$yuiSuggest->setContainerWidth(259);

				// build html
				$_sidebar_html1 = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 1, 1);

				$_sidebar_html1->setCol(0, 0, null, $_sidebar_disabler);

				// build html
				$_sidebar_html2 = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0', 'id' => 'sidebar_options', 'style' => 'display:' . $_sidebar_show), 8, 3);

				$_sidebar_html2->setCol(0, 0, array('colspan' => 3, 'height' => 10), '');
				$_sidebar_html2->setCol(1, 0, array('colspan' => 3, 'height' => 10), $_sidebar_show_on_startup);
				$_sidebar_html2->setCol(2, 0, array('colspan' => 3, 'height' => 10), '');
				$_sidebar_html2->setCol(3, 0, array('colspan' => 3, 'class' => 'defaultfont'), g_l('prefs', '[sidebar_width]'));
				$_sidebar_html2->setCol(4, 0, null, $_sidebar_width);
				$_sidebar_html2->setCol(4, 1, null, we_html_tools::getPixel(10, 1));
				$_sidebar_html2->setCol(4, 2, null, $_sidebar_width_chooser);
				$_sidebar_html2->setCol(5, 0, array('colspan' => 3, 'height' => 10), '');
				$_sidebar_html2->setCol(6, 0, array('colspan' => 3, 'class' => 'defaultfont'), g_l('prefs', '[sidebar_document]'));
				$_sidebar_html2->setCol(7, 0, array('colspan' => 3), $yuiSuggest->getHTML());

				// Build dialog if user has permission
				$_settings[] = array('headline' => g_l('prefs', '[sidebar]'), 'html' => $_sidebar_html1->getHtml() . $_sidebar_html2->getHtml(), 'space' => 200);
			}

			$_settings[] = array('headline' => g_l('prefs', '[use_jupload]'), 'html' => we_html_tools::htmlSelect('newconf[use_jupload]', array(g_l('prefs', '[no]'), g_l('prefs', '[yes]')), 1, get_value('use_jupload'), false, ''), 'space' => 200);


			// TREE

			$_tree_count = get_value('default_tree_count');
			$_file_tree_count = new we_html_select(array('name' => 'newconf[default_tree_count]', 'class' => 'weSelect'));
			$_file_tree_count->addOption(0, g_l('prefs', '[all]'));


			for($i = 10; $i < 51; $i+=10){
				$_file_tree_count->addOption($i, $i);
			}

			for($i = 100; $i < 501; $i+=100){
				$_file_tree_count->addOption($i, $i);
			}

			if(!$_file_tree_count->selectOption($_tree_count)){
				$_file_tree_count->addOption($_tree_count, $_tree_count);
				// Set selected extension
				$_file_tree_count->selectOption($_tree_count);
			}

			$_settings[] = array('headline' => g_l('prefs', '[tree_title]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[tree_count_description]'), 2, 200) . '<br>' . $_file_tree_count->getHtml(), 'space' => 200);


			//WINDOW DIMENSIONS

			if(get_value('sizeOpt') == 0){
				$_window_specify = false;
				$_window_max = true;
			} else{
				$_window_specify = true;
				$_window_max = false;
			}

			// Build maximize window
			$_window_max_code = we_forms::radiobutton(0, get_value('sizeOpt') == 0, 'newconf[sizeOpt]', g_l('prefs', '[maximize]'), true, 'defaultfont', "document.getElementsByName('newconf[weWidth]')[0].disabled = true;document.getElementsByName('newconf[weHeight]')[0].disabled = true;");

			// Build specify window dimension
			$_window_specify_code = we_forms::radiobutton(1, !(get_value('sizeOpt') == 0), 'newconf[sizeOpt]', g_l('prefs', '[specify]'), true, 'defaultfont', "document.getElementsByName('newconf[weWidth]')[0].disabled = false;document.getElementsByName('newconf[weHeight]')[0].disabled = false;");

			// Create specify window dimension input
			$_window_specify_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 4, 4);

			$_window_specify_table->setCol(0, 0, null, we_html_tools::getPixel(1, 10));
			$_window_specify_table->setCol(1, 0, null, we_html_tools::getPixel(50, 1));
			$_window_specify_table->setCol(2, 0, null, we_html_tools::getPixel(1, 5));
			$_window_specify_table->setCol(3, 0, null, we_html_tools::getPixel(50, 1));

			$_window_specify_table->setCol(1, 1, array('class' => 'defaultfont'), g_l('prefs', '[width]') . ':');
			$_window_specify_table->setCol(3, 1, array('class' => 'defaultfont'), g_l('prefs', '[height]') . ':');

			$_window_specify_table->setCol(1, 2, null, we_html_tools::getPixel(10, 1));
			$_window_specify_table->setCol(3, 2, null, we_html_tools::getPixel(10, 1));

			$_window_specify_table->setCol(1, 3, null, we_html_tools::htmlTextInput('newconf[weWidth]', 6, (get_value('sizeOpt') != 0 ? get_value('weWidth') : ''), 4, (get_value('sizeOpt') == 0 ? "disabled=\"disabled\"" : ""), "text", 60));
			$_window_specify_table->setCol(3, 3, null, we_html_tools::htmlTextInput('newconf[weHeight]', 6, (get_value('sizeOpt') != 0 ? get_value('weHeight') : ''), 4, (get_value('sizeOpt') == 0 ? "disabled=\"disabled\"" : ""), "text", 60));

			// Build apply current window dimension
			$_window_current_dimension_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 1, 2);

			$_window_current_dimension_table->setCol(0, 0, null, we_html_tools::getPixel(50, 1));
			$_window_current_dimension_table->setCol(0, 1, null, we_button::create_button('apply_current_dimension', "javascript:document.getElementsByName('sizeOpt')[1].checked = true;document.getElementsByName('newconf[weWidth]')[0].disabled = false;document.getElementsByName('newconf[weHeight]')[0].disabled = false;document.getElementsByName('newconf[weWidth]')[0].value = " . (we_base_browserDetect::isIE() ? "parent.opener.top.document.body.clientWidth" : "parent.opener.top.window.outerWidth") . ";document.getElementsByName('newconf[weHeight]')[0].value = " . (we_base_browserDetect::isIE() ? "parent.opener.top.document.body.clientHeight;" : "parent.opener.top.window.outerHeight;"), true));

			// Build final HTML code
			$_window_html = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 5, 1);
			$_window_html->setCol(0, 0, null, $_window_max_code);
			$_window_html->setCol(1, 0, null, we_html_tools::getPixel(1, 10));
			$_window_html->setCol(2, 0, null, $_window_specify_code . $_window_specify_table->getHtml());
			$_window_html->setCol(3, 0, null, we_html_tools::getPixel(1, 10));
			$_window_html->setCol(4, 0, null, $_window_current_dimension_table->getHtml());

			// Build dialog
			$_settings[] = array('headline' => g_l('prefs', '[dimension]'), 'html' => $_window_html->getHtml(), 'space' => 200);
			return create_dialog('', g_l('prefs', '[tab][ui]'), $_settings, -1, '', '', '', (isset($_needed_JavaScript) ? $_needed_JavaScript : ''));

		case 'defaultAttribs':
			if(!we_hasPerm("ADMINISTRATOR")){
				break;
			}

			$settings = array();

			$_needed_JavaScript = "";
			/**
			 * PHP setting
			 */
			// Build select box
			$WE_PHP_DEFAULT = new we_html_select(array("name" => "newconf[WE_PHP_DEFAULT]", "class" => "weSelect"));
			$WE_PHP_DEFAULT->addOption(0, "false");
			$WE_PHP_DEFAULT->addOption(1, "true");
			$WE_PHP_DEFAULT->selectOption(get_value("WE_PHP_DEFAULT") ? 1 : 0);

			/**
			 * inlineedit setting
			 */
			// Build select box
			$INLINEEDIT_DEFAULT = new we_html_select(array("name" => "newconf[INLINEEDIT_DEFAULT]", "class" => "weSelect"));
			$INLINEEDIT_DEFAULT->addOption(0, "false");
			$INLINEEDIT_DEFAULT->addOption(1, "true");
			$INLINEEDIT_DEFAULT->selectOption(get_value("INLINEEDIT_DEFAULT") ? 1 : 0);

			$REMOVEFIRSTPARAGRAPH_DEFAULT = new we_html_select(array("name" => "newconf[REMOVEFIRSTPARAGRAPH_DEFAULT]", "class" => "weSelect"));
			$REMOVEFIRSTPARAGRAPH_DEFAULT->addOption(0, "false");
			$REMOVEFIRSTPARAGRAPH_DEFAULT->addOption(1, "true");
			$REMOVEFIRSTPARAGRAPH_DEFAULT->selectOption(get_value("REMOVEFIRSTPARAGRAPH_DEFAULT") ? 1 : 0);

			$SHOWINPUTS_DEFAULT = new we_html_select(array("name" => "newconf[SHOWINPUTS_DEFAULT]", "class" => "weSelect"));
			$SHOWINPUTS_DEFAULT->addOption(0, "false");
			$SHOWINPUTS_DEFAULT->addOption(1, "true");
			$SHOWINPUTS_DEFAULT->selectOption(get_value("SHOWINPUTS_DEFAULT") ? 1 : 0);

			$HIDENAMEATTRIBINWEIMG_DEFAULT = new we_html_select(array("name" => "newconf[HIDENAMEATTRIBINWEIMG_DEFAULT]", "class" => "weSelect"));
			$HIDENAMEATTRIBINWEIMG_DEFAULT->addOption(0, g_l('prefs', '[no]'));
			$HIDENAMEATTRIBINWEIMG_DEFAULT->addOption(1, g_l('prefs', '[yes]'));
			$HIDENAMEATTRIBINWEIMG_DEFAULT->selectOption(get_value("HIDENAMEATTRIBINWEIMG_DEFAULT") ? 1 : 0);

			$HIDENAMEATTRIBINWEFORM_DEFAULT = new we_html_select(array("name" => "newconf[HIDENAMEATTRIBINWEFORM_DEFAULT]", "class" => "weSelect"));
			$HIDENAMEATTRIBINWEFORM_DEFAULT->addOption(0, g_l('prefs', '[no]'));
			$HIDENAMEATTRIBINWEFORM_DEFAULT->addOption(1, g_l('prefs', '[yes]'));
			$HIDENAMEATTRIBINWEFORM_DEFAULT->selectOption(get_value("HIDENAMEATTRIBINWEFORM_DEFAULT") ? 1 : 0);

			$CSSAPPLYTO_DEFAULT = new we_html_select(array("name" => "newconf[CSSAPPLYTO_DEFAULT]", "class" => "weSelect"));
			$CSSAPPLYTO_DEFAULT->addOption("all", "all");
			$CSSAPPLYTO_DEFAULT->addOption("around", "around");
			$CSSAPPLYTO_DEFAULT->addOption("wysiwyg", "wysiwyg");
			$CSSAPPLYTO_DEFAULT->selectOption(get_value("CSSAPPLYTO_DEFAULT") ? get_value("CSSAPPLYTO_DEFAULT") : "around");

			$BASE_IMG = we_html_tools::htmlTextInput("newconf[BASE_IMG]", 22, get_value('BASE_IMG'), "", 'placeholder="http://example.org"', "url", 225, 0, "");
			$BASE_CSS = we_html_tools::htmlTextInput("newconf[BASE_CSS]", 22, get_value('BASE_CSS'), "", 'placeholder="http://example.org"', "url", 225, 0, "");
			$BASE_JS = we_html_tools::htmlTextInput("newconf[BASE_JS]", 22, get_value('BASE_JS'), "", 'placeholder="http://example.org"', "url", 225, 0, "");

			$_settings = array(
				array("headline" => g_l('prefs', '[default_php_setting]'), "html" => $WE_PHP_DEFAULT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[inlineedit_default]'), "html" => $INLINEEDIT_DEFAULT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[removefirstparagraph_default]'), "html" => $REMOVEFIRSTPARAGRAPH_DEFAULT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[showinputs_default]'), "html" => $SHOWINPUTS_DEFAULT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[hidenameattribinweimg_default]'), "html" => $HIDENAMEATTRIBINWEIMG_DEFAULT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[hidenameattribinweform_default]'), "html" => $HIDENAMEATTRIBINWEFORM_DEFAULT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[cssapplyto_default]'), "html" => $CSSAPPLYTO_DEFAULT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[base][img]'), "html" => $BASE_IMG, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[base][css]'), "html" => $BASE_CSS, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[base][js]'), "html" => $BASE_JS, "space" => 200),
			);


			return create_dialog('', 'we:tag Standards'/* g_l('prefs', '[tab][defaultAttribs]') */, $_settings, -1, '', '', '', (isset($_needed_JavaScript) ? $_needed_JavaScript : ''));

		case 'countries':
			if(!we_base_preferences::userIsAllowed('WE_COUNTRIES_DEFAULT')){
				break;
			}
			if(!Zend_Locale::hasCache()){
				Zend_Locale::setCache(getWEZendCache());
			}

			$_countries_default = we_html_tools::htmlTextInput('newconf[WE_COUNTRIES_DEFAULT]', 22, get_value('WE_COUNTRIES_DEFAULT'), '', '', 'text', 225);

			$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
			$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
			$countrycode = array_search($langcode, $GLOBALS['WE_LANGS_COUNTRIES']);
			$zendsupported = Zend_Locale::getTranslationList('territory', $langcode, 2);
			$oldLocale = setlocale(LC_ALL, NULL);
			setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
			asort($zendsupported, SORT_LOCALE_STRING);
			setlocale(LC_ALL, $oldLocale);
			$countries_top = explode(',', get_value('WE_COUNTRIES_TOP'));
			$countries_shown = explode(',', get_value('WE_COUNTRIES_SHOWN'));
			$tabC = new we_html_table(array('border' => '1', 'cellpadding' => '2', 'cellspacing' => '0'), $rows_num = 1, $cols_num = 4);
			$i = 0;
			$tabC->setCol($i, 0, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_country]'));
			$tabC->setCol($i, 1, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_top]'));
			$tabC->setCol($i, 2, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_show]'));
			$tabC->setCol($i, 3, array('class' => 'defaultfont', 'style' => 'font-weight:bold', 'nowrap' => 'nowrap'), g_l('prefs', '[countries_noshow]'));
			foreach($zendsupported as $countrycode => $country){
				$i++;
				$tabC->addRow();
				$tabC->setCol($i, 0, array('class' => 'defaultfont'), CheckAndConvertISObackend($country));
				$tabC->setCol($i, 1, array('class' => 'defaultfont'), '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="2" ' . (in_array($countrycode, $countries_top) ? 'checked' : '') . ' > ');
				$tabC->setCol($i, 2, array('class' => 'defaultfont'), '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="1" ' . (in_array($countrycode, $countries_shown) ? 'checked' : '') . ' > ');
				$tabC->setCol($i, 3, array('class' => 'defaultfont'), '<input type="radio" name="newconf[countries][' . $countrycode . ']" value="0" ' . (!in_array($countrycode, $countries_top) && !in_array($countrycode, $countries_shown) ? 'checked' : '') . ' > ');
			}

			$_settings = array(
				array('headline' => g_l('prefs', '[countries_headline]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[countries_information]'), 2, 450, false), 'space' => 0, 'noline' => 1),
				array('headline' => g_l('prefs', '[countries_default]'), 'html' => $_countries_default, 'space' => 200, 'noline' => 1),
				array('headline' => '', 'html' => $tabC->getHtml(), 'space' => 0, 'noline' => 1),
			);
			// Build dialog element if user has permission
			return create_dialog('', g_l('prefs', '[tab][countries]'), $_settings);

		case 'language':
			if(!we_base_preferences::userIsAllowed('locale_default') && we_base_preferences::userIsAllowed('locale_locales')){
				break;
			}
			$default = get_value('locale_default');
			$locales = get_value('locale_locales');

			$preJs = we_html_element::jsElement("
	function addLocale() {
		var LanguageIndex = document.getElementById('locale_language').selectedIndex;
		var LanguageValue = document.getElementById('locale_language').options[LanguageIndex].value;
		var LanguageText = document.getElementById('locale_language').options[LanguageIndex].text;

		var CountryIndex = document.getElementById('locale_country').selectedIndex;
		var CountryValue = document.getElementById('locale_country').options[CountryIndex].value;
		var CountryText = document.getElementById('locale_country').options[CountryIndex].text;

		if(LanguageValue.substr(0, 1) == \"~\") {
			LanguageValue = LanguageValue.substr(1);
		}
		if(LanguageValue == \"\") {
			return;
		}

		if(CountryValue.substr(0, 1) == \"~\") {
			CountryValue = CountryValue.substr(1);
		}
		if(CountryValue != \"\") {
			var LocaleValue = LanguageValue + '_' + CountryValue;
			var LocaleText = LanguageText + ' (' + CountryText + ')';
		} else {
			var LocaleValue = LanguageValue;
			var LocaleText = LanguageText;
		}

		var found = false;
		for(i = 0; i < document.getElementById('locale_temp_locales').options.length; i++) {
			if(document.getElementById('locale_temp_locales').options[i].value == LocaleValue) {
				found = true;
			}
		}

		if(found == true) {
			" . we_message_reporting::getShowMessageCall(g_l('prefs', '[language_already_exists]'), we_message_reporting::WE_MESSAGE_ERROR) . "
		} else {
			if (CountryValue == \"\") {
				" . we_message_reporting::getShowMessageCall(g_l('prefs', '[language_country_missing]'), we_message_reporting::WE_MESSAGE_ERROR) . "
			} else {

				var option = new Option(LocaleText, LocaleValue, false, false);
				document.getElementById('locale_temp_locales').options[document.getElementById('locale_temp_locales').options.length] = option

				if(document.getElementById('locale_temp_locales').options.length == 1) {
					setDefaultLocale(LocaleValue);
				}
" . (defined('SPELLCHECKER') ?
						"

				// Wörterbuch hinzufügen
				if(confirm('{" . g_l('prefs', '[add_dictionary_question]') . "}')) {
					top.opener.top.we_cmd('edit_spellchecker_ifthere');
				}
" : '') . "

			}
		}
		resetLocales();

	}

	function deleteLocale() {

		if(document.getElementById('locale_temp_locales').selectedIndex > -1) {
			var LocaleIndex = document.getElementById('locale_temp_locales').selectedIndex;
			var LocaleValue =  document.getElementById('locale_temp_locales').options[LocaleIndex].value;

			if(LocaleValue == document.getElementById('locale_default').value) {
				" . we_message_reporting::getShowMessageCall(g_l('prefs', '[cannot_delete_default_language]'), we_message_reporting::WE_MESSAGE_ERROR) . "
			} else {
				document.getElementById('locale_temp_locales').options[LocaleIndex] = null;
			}
			resetLocales();
		}

	}

	function defaultLocale() {

		if(document.getElementById('locale_temp_locales').selectedIndex > -1) {
			var LocaleIndex = document.getElementById('locale_temp_locales').selectedIndex;
			var LocaleValue =  document.getElementById('locale_temp_locales').options[LocaleIndex].value;

			setDefaultLocale(LocaleValue);
		}

	}

	function setDefaultLocale(Value) {

		if(document.getElementById('locale_temp_locales').options.length > 0) {
			Index = 0;
			for(i = 0; i < document.getElementById('locale_temp_locales').options.length; i++) {
				if(document.getElementById('locale_temp_locales').options[i].value == Value) {
					Index = i;
				}
				document.getElementById('locale_temp_locales').options[i].style.background = '#ffffff';
			}
			document.getElementById('locale_temp_locales').options[Index].style.background = '#cccccc';
			document.getElementById('locale_temp_locales').options[Index].selected = false;
			document.getElementById('locale_default').value = Value;
		}

	}

	function resetLocales() {

		if(document.getElementById('locale_temp_locales').options.length > 0) {
			var temp = new Array(document.getElementById('locale_temp_locales').options.length);
			for(i = 0; i < document.getElementById('locale_temp_locales').options.length; i++) {
				temp[i] = document.getElementById('locale_temp_locales').options[i].value;
			}
			document.getElementById('locale_locales').value = temp.join(\",\");
		}

	}

	function initLocale(Locale) {
		if(Locale != \"\") {
			setDefaultLocale(Locale);
		}
		resetLocales();
	}

	Array.prototype.contains = function(obj) {
		var i, listed = false;
		for (i=0; i<this.length; i++) {
			if (this[i] === obj) {
				listed = true;
				break;
			}
		}
		return listed;
	}
");

			$postJs = we_html_element::jsElement('initLocale("' . $default . '");');

			$_hidden_fields = we_html_element::htmlHidden(array('name' => 'newconf[locale_default]', 'value' => $default, 'id' => 'locale_default')) .
				we_html_element::htmlHidden(array('name' => 'newconf[locale_locales]', 'value' => implode(',', array_keys($locales)), 'id' => 'locale_locales'));

			//Locales
			$_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'locale_temp_locales', 'size' => '10', 'id' => 'locale_temp_locales', 'style' => 'width: 340px'));
			$_select_box->addOptions(count($locales), array_keys($locales), array_values($locales));

			$_enabled_buttons = (count($locales) > 0);


			// Create edit list
			$_editlist_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 2, 3);

			// Buttons
			$default = we_button::create_button('default', 'javascript:defaultLocale()', true, 100, 22, '', '', !$_enabled_buttons);
			$delete = we_button::create_button('delete', 'javascript:deleteLocale()', true, 100);

			$_html = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 1, 3);
			$_html->setCol(0, 0, array('class' => 'defaultfont'), $default);
			$_html->setCol(0, 1, null, we_html_tools::getPixel(25, 2));
			$_html->setCol(0, 2, array('class' => 'defaultfont'), $delete);

			$_editlist_table->setCol(0, 0, null, $_hidden_fields . $_select_box->getHtml());
			$_editlist_table->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
			$_editlist_table->setCol(0, 2, array('valign' => 'top'), $default . we_html_tools::getPixel(1, 10) . $delete);

			// Add Locales
			// Languages
			$Languages = g_l('languages', '');
			$TopLanguages = array(
				'~de' => $Languages['de'],
				'~nl' => $Languages['nl'],
				'~en' => $Languages['en'],
				'~fi' => $Languages['fi'],
				'~fr' => $Languages['fr'],
				'~pl' => $Languages['pl'],
				'~ru' => $Languages['ru'],
				'~es' => $Languages['es'],
			);
			asort($Languages);
			asort($TopLanguages);
			$TopLanguages[''] = '---';
			$Languages = array_merge($TopLanguages, $Languages);

			$_languages = new we_html_select(array('name' => 'newconf[locale_language]', 'id' => 'locale_language', 'style' => 'width: 139px', 'class' => 'weSelect'));
			$_languages->addOptions(count($Languages), array_keys($Languages), array_values($Languages));

			// Countries
			$Countries = g_l('countries', '');
			$TopCountries = array(
				'~DE' => $Countries['DE'],
				'~CH' => $Countries['CH'],
				'~AT' => $Countries['AT'],
				'~NL' => $Countries['NL'],
				'~GB' => $Countries['GB'],
				'~US' => $Countries['US'],
				'~FI' => $Countries['FI'],
				'~FR' => $Countries['FR'],
				'~PL' => $Countries['PL'],
				'~RU' => $Countries['RU'],
				'~ES' => $Countries['ES'],
			);
			asort($Countries);
			asort($TopCountries);
			$TopCountries['~'] = '---';
			$Countries = array_merge(array('' => ''), $TopCountries, $Countries);

			$_countries = new we_html_select(array('name' => 'newconf[locale_country]', 'id' => 'locale_country', 'style' => 'width: 139px', 'class' => 'weSelect'));
			$_countries->addOptions(count($Countries), array_keys($Countries), array_values($Countries));

			// Button
			$_add_button = we_button::create_button('add', 'javascript:addLocale()', true, 139);

			// Build final HTML code
			$_add_html = g_l('prefs', '[locale_languages]') . '<br />' .
				$_languages->getHtml() . '<br /><br />' .
				g_l('prefs', '[locale_countries]') . '<br />' .
				$_countries->getHtml() . '<br /><br />' .
				$_add_button;

			$LANGLINK_SUPPORT = new we_html_select(array('name' => 'newconf[LANGLINK_SUPPORT]', 'class' => 'weSelect'));
			$LANGLINK_SUPPORT->addOption(0, 'false');
			$LANGLINK_SUPPORT->addOption(1, 'true');
			$LANGLINK_SUPPORT->selectOption(get_value('LANGLINK_SUPPORT'));

			//Todo: remove: g_l('prefs', '[langlink_support_backlinks_information]'), g_l('prefs', '[langlink_support_backlinks]'),g_l('prefs', '[langlink_support_recursive_information]'),g_l('prefs', '[langlink_support_recursive]')
			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[locale_information]'), 2, 450, false), 'space' => 0),
				array('headline' => '', 'html' => $_editlist_table->getHtml(), 'space' => 0),
				array('headline' => g_l('prefs', '[locale_add]'), 'html' => $_add_html, 'space' => 200),
				array('headline' => g_l('prefs', '[langlink_headline]'), 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[langlink_information]'), 2, 450, false), 'space' => 0, 'noline' => 1),
				array('headline' => g_l('prefs', '[langlink_support]'), 'html' => $LANGLINK_SUPPORT->getHtml(), 'space' => 200, 'noline' => 1),
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[langlink_abandoned_options]'), 2, 450, false), 'space' => 0, 'noline' => 1),
			);

			return $preJs . create_dialog('', g_l('prefs', '[tab][language]'), $_settings) . $postJs;

		case 'extensions':
			//FILE EXTENSIONS
			if(!we_base_preferences::userIsAllowed('DEFAULT_HTML_EXT')){
				break;
			}

			// Get webEdition extensions
			$_we_extensions = we_base_ContentTypes::inst()->getExtension('text/webedition');

			// Build static webEdition extensions select box
			$_static_we_extensions = new we_html_select(array('name' => 'newconf[DEFAULT_STATIC_EXT]', 'class' => 'weSelect'));
			$_dynamic_we_extensions = new we_html_select(array('name' => 'newconf[DEFAULT_DYNAMIC_EXT]', 'class' => 'weSelect'));
			foreach($_we_extensions as $value){
				$_static_we_extensions->addOption($value, $value);
				$_dynamic_we_extensions->addOption($value, $value);
			}
			$_static_we_extensions->selectOption(get_value('DEFAULT_STATIC_EXT'));
			$_dynamic_we_extensions->selectOption(get_value('DEFAULT_DYNAMIC_EXT'));

			$_we_extensions_html = g_l('prefs', '[static]') . we_html_element::htmlBr() . $_static_we_extensions->getHtml() . we_html_element::htmlBr() . we_html_element::htmlBr() . g_l('prefs', '[dynamic]') . we_html_element::htmlBr() . $_dynamic_we_extensions->getHtml();

			// HTML extensions
			$_html_extensions = we_base_ContentTypes::inst()->getExtension('text/html');

			// Build static webEdition extensions select box
			$_static_html_extensions = new we_html_select(array('name' => 'newconf[DEFAULT_HTML_EXT]', 'class' => 'weSelect'));
			foreach($_html_extensions as $value){
				$_static_html_extensions->addOption($value, $value);
			}
			$_static_html_extensions->selectOption(get_value('DEFAULT_HTML_EXT'));

			$_html_extensions_html = g_l('prefs', '[html]') . '<br>' . $_static_html_extensions->getHtml();

			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[extensions_information]'), 2, 450, false), 'space' => 0),
				array('headline' => g_l('prefs', '[we_extensions]'), 'html' => $_we_extensions_html, 'space' => 200),
				array('headline' => g_l('prefs', '[html_extensions]'), 'html' => $_html_extensions_html, 'space' => 200, 'noline' => 1)
			);

			return create_dialog('', g_l('prefs', '[tab][extensions]'), $_settings);

		case 'editor':
			//EDITOR PLUGIN

			$_needed_JavaScript = we_html_element::jsElement('

function setJavaEditorDisabled(disabled) {
	document.getElementById("_newconf[specify_jeditor_colors]").disabled = disabled;
	document.getElementById("label__newconf[specify_jeditor_colors]").style.color = (disabled ? "grey" : "");
	document.getElementById("label__newconf[specify_jeditor_colors]").style.cursor = (disabled ? "default" : "pointer");
	if (document.getElementById("_newconf[specify_jeditor_colors]").checked) {
		setEditorColorsDisabled(disabled);
	} else {
		setEditorColorsDisabled(true);
	}
}

function setEditorColorsDisabled(disabled) {
	setColorChooserDisabled("editorFontcolor", disabled);
	setColorChooserDisabled("editorWeTagFontcolor", disabled);
	setColorChooserDisabled("editorWeAttributeFontcolor", disabled);
	setColorChooserDisabled("editorHTMLTagFontcolor", disabled);
	setColorChooserDisabled("editorHTMLAttributeFontcolor", disabled);
	setColorChooserDisabled("editorPiTagFontcolor", disabled);
	setColorChooserDisabled("editorCommentFontcolor", disabled);
}

function setColorChooserDisabled(id, disabled) {
	var td = document.getElementById("color_newconf["+ id+"]");
	td.setAttribute("class", disabled ? "disabled" : "");
	td.firstChild.style.cursor = disabled ? "default" : "pointer";
	document.getElementById("label_"+id).style.color=disabled ? "grey" : "";
}

function displayEditorOptions(editor) {
	tmp=document.getElementsByClassName("editor");
	for( var k=0; k<tmp .length; k++ ) {
		tmp[k].style.display="none";
	}

	tmp=document.getElementsByClassName("editor_"+editor);
	for( var k=0; k<tmp .length; k++ ) {
		tmp[k].style.display="block";
	}
}

function initEditorMode() {
	displayEditorOptions(document.getElementsByName("newconf[editorMode]")[0].options[document.getElementsByName("newconf[editorMode]")[0].options.selectedIndex].value);
}

if(window.onload) {
	var previousOnload = window.onload;
	window.onload = function(e) {
		previousOnload(e);
		initEditorMode();
	};
}else {
	window.onload = function(e) {
		initEditorMode();
	};
}');

			$_attr = ' class="defaultfont" style="width:150px;"';
			$_attr_dis = ' class="defaultfont" style="width:150px;color:grey;"';

			$_template_editor_mode = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorMode]', 'size' => '1', 'onchange' => 'displayEditorOptions(this.options[this.options.selectedIndex].value);'));
			$_template_editor_mode->addOption('textarea', g_l('prefs', '[editor_plaintext]'));
			$_template_editor_mode->addOption('codemirror2', g_l('prefs', '[editor_javascript2]'));
			$_template_editor_mode->addOption('java', g_l('prefs', '[editor_java]'));
			$_template_editor_mode->selectOption(get_value('editorMode'));

			/**
			 * Editor font settings
			 */
			$_template_fonts = array('Arial', 'Courier', 'Courier New', 'Helvetica', 'Monaco', 'Mono', 'Tahoma', 'Verdana', 'serif', 'sans-serif', 'none');
			$_template_font_sizes = array(8, 9, 10, 11, 12, 14, 16, 18, 24, 32, 48, 72, -1);

			$_template_editor_font_specify = (get_value('editorFontname') != '' && get_value('editorFontname') != 'none');
			$_template_editor_font_size_specify = (get_value('editorFontsize') != '' && get_value('editorFontsize') != -1);

			// Build specify font
			$_template_editor_font_specify_code = we_forms::checkbox(1, $_template_editor_font_specify, 'newconf[editorFont]', g_l('prefs', '[specify]'), true, 'defaultfont', "if (document.getElementsByName('newconf[editorFont]')[0].checked) { document.getElementsByName('newconf[editorFontname]')[0].disabled = false;document.getElementsByName('newconf[editorFontsize]')[0].disabled = false; } else { document.getElementsByName('newconf[editorFontname]')[0].disabled = true;document.getElementsByName('newconf[editorFontsize]')[0].disabled = true; }");

			$_template_editor_font_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorFontname]', 'size' => '1', 'style' => 'width: 135px;', ($_template_editor_font_specify ? 'enabled' : 'disabled') => ($_template_editor_font_specify ? 'enabled' : 'disabled')));

			$_colorsDisabled = get_value('specify_jeditor_colors') == 0 || (get_value('editorMode') != 'java');

			$_template_editor_fontcolor_selector = getColorInput('newconf[editorFontcolor]', get_value('editorFontcolor'), $_colorsDisabled);
			$_template_editor_we_tag_fontcolor_selector = getColorInput('newconf[editorWeTagFontcolor]', get_value('editorWeTagFontcolor'), $_colorsDisabled);
			$_template_editor_we_attribute_fontcolor_selector = getColorInput('newconf[editorWeAttributeFontcolor]', get_value('editorWeAttributeFontcolor'), $_colorsDisabled);
			$_template_editor_html_tag_fontcolor_selector = getColorInput('newconf[editorHTMLTagFontcolor]', get_value('editorHTMLTagFontcolor'), $_colorsDisabled);
			$_template_editor_html_attribute_fontcolor_selector = getColorInput('newconf[editorHTMLAttributeFontcolor]', get_value('editorHTMLAttributeFontcolor'), $_colorsDisabled);
			$_template_editor_pi_tag_fontcolor_selector = getColorInput('newconf[editorPiTagFontcolor]', get_value('editorPiTagFontcolor'), $_colorsDisabled);
			$_template_editor_comment_fontcolor_selector = getColorInput('newconf[editorCommentFontcolor]', get_value('editorCommentFontcolor'), $_colorsDisabled);

			foreach($_template_fonts as $font){
				$_template_editor_font_select_box->addOption($font, $font);
			}
			$_template_editor_font_select_box->selectOption($_template_editor_font_specify ? get_value('editorFontname') : 'Courier New');

			$_template_editor_font_sizes_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorFontsize]', 'size' => '1', 'style' => 'width: 135px;', ($_template_editor_font_size_specify ? 'enabled' : 'disabled') => ($_template_editor_font_size_specify ? 'enabled' : 'disabled')));
			foreach($_template_font_sizes as $key => $sz){
				$_template_editor_font_sizes_select_box->addOption($sz, $sz);
			}
			$_template_editor_font_sizes_select_box->selectOption($_template_editor_font_specify ? $_template_font_sizes[$key] : 11);

			$_template_editor_font_sizes_select_box->selectOption(get_value('editorFontsize'));


			$_template_editor_font_specify_table = '<table style="margin:0 0 20px 50px;" border="0" cellpadding="0" cellspacing="0">
	<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontname]') . '</td><td>' . $_template_editor_font_select_box->getHtml() . '</td></tr>
	<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontsize]') . '</td><td>' . $_template_editor_font_sizes_select_box->getHtml() . '</td></tr>
</table>';

			$_template_editor_font_color_checkbox = we_forms::checkboxWithHidden(get_value('specify_jeditor_colors'), "newconf[specify_jeditor_colors]", g_l('prefs', '[editor_font_colors]'), false, "defaultfont", "setEditorColorsDisabled(!this.checked);");
			$attr = ($_colorsDisabled ? $_attr_dis : $_attr);
			$_template_editor_font_color_table = '<table id="editorColorTable" style="margin: 10px 0 0 50px;" border="0" cellpadding="0" cellspacing="0">
	<tr><td id="label_editorFontcolor" ' . $attr . '>' . g_l('prefs', '[editor_normal_font_color]') . '</td><td>' . $_template_editor_fontcolor_selector . '</td></tr>
	<tr><td id="label_editorWeTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_we_tag_font_color]') . '</td><td>' . $_template_editor_we_tag_fontcolor_selector . '</td></tr>
	<tr><td id="label_editorWeAttributeFontcolor"' . $attr . '>' . g_l('prefs', '[editor_we_attribute_font_color]') . '</td><td>' . $_template_editor_we_attribute_fontcolor_selector . '</td></tr>
	<tr><td id="label_editorHTMLTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_html_tag_font_color]') . '</td><td>' . $_template_editor_html_tag_fontcolor_selector . '</td></tr>
	<tr><td id="label_editorHTMLAttributeFontcolor"' . $attr . '>' . g_l('prefs', '[editor_html_attribute_font_color]') . '</td><td>' . $_template_editor_html_attribute_fontcolor_selector . '</td></tr>
	<tr><td id="label_editorPiTagFontcolor"' . $attr . '>' . g_l('prefs', '[editor_pi_tag_font_color]') . '</td><td>' . $_template_editor_pi_tag_fontcolor_selector . '</td></tr>
	<tr><td id="label_editorCommentFontcolor"' . $attr . '>' . g_l('prefs', '[editor_comment_font_color]') . '</td><td>' . $_template_editor_comment_fontcolor_selector . '</td></tr>
</table>';


			//Build activation of line numbers
			$_template_editor_linenumbers_code = we_forms::checkbox(1, get_value('editorLinenumbers'), 'newconf[editorLinenumbers]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '');

			$tmp2 = we_forms::checkbox(1, get_value('FAST_RESTORE'), 'FAST_RESTORE', 'new fast Restore', false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[FAST_RESTORE]\');') .
				we_html_tools::hidden('newconf[FAST_RESTORE]', get_value('FAST_RESTORE'));

			//Build activation of code completion
			$_template_editor_codecompletion_code =
				we_forms::checkbox(1, get_value('editorCodecompletion-WE'), 'editorCodecompletion0', 'WE-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][WE]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][WE]', get_value('editorCodecompletion-WE')) .
				we_forms::checkbox(1, get_value('editorCodecompletion-htmlTag'), 'editorCodecompletion1', 'HTML-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlTag]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlTag]', get_value('editorCodecompletion-htmlTag')) .
				we_forms::checkbox(1, get_value('editorCodecompletion-htmlDefAttr'), 'editorCodecompletion2', 'HTML-Default-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlDefAttr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlDefAttr]', get_value('editorCodecompletion-htmlDefAttr')) .
				we_forms::checkbox(1, get_value('editorCodecompletion-htmlAttr'), 'editorCodecompletion3', 'HTML-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlAttr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlAttr]', get_value('editorCodecompletion-htmlAttr')) .
				we_forms::checkbox(1, get_value('editorCodecompletion-htmlJSAttr'), 'editorCodecompletion4', 'HTML-JS-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][htmlJSAttr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][htmlJSAttr]', get_value('editorCodecompletion-htmlJSAttr')) .
				we_forms::checkbox(1, get_value('editorCodecompletion-html5Tag'), 'editorCodecompletion5', 'HTML5-Tags', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][html5Tag]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][html5Tag]', get_value('editorCodecompletion-html5Tag')) .
				we_forms::checkbox(1, get_value('editorCodecompletion-html5Attr'), 'editorCodecompletion6', 'HTML5-Attribs', true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorCodecompletion][html5Attr]\');') .
				we_html_tools::hidden('newconf[editorCodecompletion][html5Attr]', get_value('editorCodecompletion-html5Attr'));


			$_template_editor_tabstop_code =
				we_forms::checkbox(1, get_value('editorShowTab'), 'editorShowTab', g_l('prefs', '[show]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[editorShowTab]\');') .
				we_html_tools::hidden('newconf[editorShowTab]', get_value('editorShowTab')) .
				'<table border="0" cellpadding="0" cellspacing="0">
				<tr><td class="defaultfont" style="width:200px;">' . g_l('prefs', '[editor_tabSize]') . '</td><td>' . we_html_tools::htmlTextInput("newconf[editorTabSize]", 2, get_value("editorTabSize"), "", "", "int", 135) . '</td></tr>
			</table>';

//Build activation of tooltips
			$_template_editor_tooltips_code = we_forms::checkbox(1, get_value('editorTooltips'), 'newconf[editorTooltips]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '');

			$_template_editor_tooltip_font_specify = (get_value('editorTooltipFontname') != '' && get_value('editorTooltipFontname') != 'none');
			$_template_editor_tooltip_font_size_specify = (get_value('editorTooltipFontsize') != '' && get_value('editorTooltipFontsize') != -1);

			// Build specify font
			$_template_editor_tooltip_font_specify_code = we_forms::checkbox(1, $_template_editor_tooltip_font_specify, 'newconf[editorTooltipFont]', g_l('prefs', '[specify]'), true, 'defaultfont', 'if (document.getElementsByName(\'newconf[editorTooltipFont]\')[0].checked) { document.getElementsByName(\'newconf[editorTooltipFontname]\')[0].disabled = false;document.getElementsByName(\'newconf[editorTooltipFontsize]\')[0].disabled = false; } else { document.getElementsByName(\'newconf[editorTooltipFontname]\')[0].disabled = true;document.getElementsByName(\'newconf[editorTooltipFontsize]\')[0].disabled = true; }');

			$_template_editor_tooltip_font_select_box = new we_html_select(array('class' => 'weSelect', 'name' => 'newconf[editorTooltipFontname]', 'size' => '1', 'style' => 'width: 135px;', ($_template_editor_tooltip_font_specify ? 'enabled' : 'disabled') => ($_template_editor_tooltip_font_specify ? 'enabled' : 'disabled')));

			foreach($_template_fonts AS $font){
				$_template_editor_tooltip_font_select_box->addOption($font, $font);
			}
			$_template_editor_tooltip_font_select_box->selectOption($_template_editor_tooltip_font_specify ? get_value('editorTooltipFontname') : 'Tahoma');

			$_template_editor_tooltip_font_sizes_select_box = new we_html_select(array('class' => 'weSelect editor editor_codemirror2', 'name' => 'newconf[editorTooltipFontsize]', 'size' => '1', 'style' => 'width: 135px;', ($_template_editor_tooltip_font_size_specify ? 'enabled' : 'disabled') => ($_template_editor_tooltip_font_size_specify ? 'enabled' : 'disabled')));

			foreach($_template_font_sizes as $sz){
				$_template_editor_tooltip_font_sizes_select_box->addOption($sz, $sz);
			}
			$_template_editor_tooltip_font_sizes_select_box->selectOption($_template_editor_tooltip_font_specify ? get_value("editor_tooltip_font_size") : 11);
			$_template_editor_tooltip_font_specify_table = '<table style="margin:0 0 20px 50px;" border="0" cellpadding="0" cellspacing="0">
				<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontname]') . '</td><td>' . $_template_editor_tooltip_font_select_box->getHtml() . '</td></tr>
				<tr><td' . $_attr . '>' . g_l('prefs', '[editor_fontsize]') . '</td><td>' . $_template_editor_tooltip_font_sizes_select_box->getHtml() . '</td></tr>
			</table>';

			//Build activation of integration of documentation
			$_template_editor_autoClose = we_forms::checkbox(1, get_value('editorDocuintegration'), 'newconf[editorDocuintegration]', g_l('prefs', '[editor_enable]'), true, 'defaultfont', '');

			//FIXME:remove editor_javascript_information
			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[editor_information]'), 2, 480, false), 'space' => 0),
				array('headline' => g_l('prefs', '[editor_mode]'), 'html' => $_template_editor_mode->getHtml(), 'space' => 150),
				array('class' => 'editor editor_codemirror2 editor_textarea', 'headline' => g_l('prefs', '[editor_font]'), 'html' => $_template_editor_font_specify_code . $_template_editor_font_specify_table, 'space' => 150),
				array('class' => 'editor editor_java', 'headline' => g_l('prefs', '[editor_highlight_colors]'), 'html' => $_template_editor_font_color_checkbox . $_template_editor_font_color_table, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_linenumbers]'), 'html' => $_template_editor_linenumbers_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_tabstop]'), 'html' => $_template_editor_tabstop_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_completion]'), 'html' => $_template_editor_codecompletion_code, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => g_l('prefs', '[editor_tooltips]'), 'html' => $_template_editor_tooltips_code . $_template_editor_tooltip_font_specify_code . $_template_editor_tooltip_font_specify_table, 'space' => 150),
				array('class' => 'editor editor_codemirror2', 'headline' => 'Autoclose Tags'/* g_l('prefs', '[editor_docuclick]') */, 'html' => $_template_editor_autoClose, 'space' => 150),
				//array('class'=>'editor editor_codemirror2','headline' => g_l('prefs', '[editor_docuclick]'), 'html' => $_template_editor_docuintegration_code, 'space' => 150),
			);

			$_settings_cookie = weGetCookieVariable("but_settings_editor_predefined");

			return create_dialog("settings_editor_predefined", g_l('prefs', '[tab][editor]'), $_settings, count($_settings), g_l('prefs', '[show_predefined]'), g_l('prefs', '[hide_predefined]'), $_settings_cookie, $_needed_JavaScript);

		case "recipients":
			if(!we_base_preferences::userIsAllowed('FORMMAIL_VIAWEDOC')){
				break;
			}
			$_settings = array();
			//FORMMAIL RECIPIENTS
			if(we_base_preferences::userIsAllowed('FORMMAIL_BLOCK')){

				// Generate needed JS
				$_needed_JavaScript = we_html_element::jsElement("
							var hot = false;

							" . (!we_hasPerm("CHANGE_START_DOCUMENT") ? we_button::create_state_changer(false) : "") . "
							function set_state_edit_delete_recipient() {
								var p = document.forms[0].elements[\"we_recipient\"];
								var i = p.length;

								if (i == 0) {
									edit_enabled = switch_button_state('edit', 'edit_enabled', 'disabled');
									delete_enabled = switch_button_state('delete', 'delete_enabled', 'disabled');
								} else {
									edit_enabled = switch_button_state('edit', 'edit_enabled', 'enabled');
									delete_enabled = switch_button_state('delete', 'delete_enabled', 'enabled');
								}
							}

							function inSelectBox(val) {
								var p = document.forms[0].elements[\"we_recipient\"];

								for (var i = 0; i < p.options.length; i++) {
									if (p.options[i].text == val) {
										return true;
									}
								}
								return false;
							}

							function addElement(value, text, sel) {
								var p = document.forms[0].elements[\"we_recipient\"];
								var i = p.length;

								p.options[i] =  new Option(text, value);

								if (sel) {
									p.selectedIndex = i;
								}
							}

							function in_array(n, h) {
								for (var i = 0; i < h.length; i++) {
									if (h[i] == n) {
										return true;
									}
								}
								return false;
							}

							function add_recipient() {
								var newRecipient = prompt(\"" . g_l('alert', "[input_name]") . "\", \"\");
								var p = document.forms[0].elements[\"we_recipient\"];

								if (newRecipient != null) {
									if (newRecipient.length > 0) {
										if (newRecipient.length > 255 ) {
											" . we_message_reporting::getShowMessageCall(g_l('alert', "[max_name_recipient]"), we_message_reporting::WE_MESSAGE_ERROR) . "
											return;
										}

										if (!inSelectBox(newRecipient)) {
											addElement(\"#\", newRecipient, true);
											hot = true;

											set_state_edit_delete_recipient();
											send_recipients();
										} else {
											" . we_message_reporting::getShowMessageCall(g_l('alert', "[recipient_exists]"), we_message_reporting::WE_MESSAGE_ERROR) . "
										}
									} else {
										" . we_message_reporting::getShowMessageCall(g_l('alert', "[not_entered_recipient]"), we_message_reporting::WE_MESSAGE_ERROR) . "
									}
								}
							}

							function delete_recipient() {
								var p = document.forms[0].elements[\"we_recipient\"];

								if (p.selectedIndex >= 0) {
									if (confirm(\"" . g_l('alert', "[delete_recipient]") . "\")) {
										hot = true;

										var d = document.forms[0].elements[\"newconf[formmail_deleted]\"];

										d.value += ((d.value)  ? \",\" : \"\") + p.options[p.selectedIndex].value;
										p.options[p.selectedIndex] = null;

										set_state_edit_delete_recipient();
									}
								}
							}

							function edit_recipient() {
								var p = document.forms[0].elements[\"we_recipient\"];

								if (p.selectedIndex >= 0) {
									var editRecipient = p.options[p.selectedIndex].text;

									editRecipient = prompt(\"" . g_l('alert', "[recipient_new_name]") . "\", editRecipient);
								}

								if (p.selectedIndex >= 0 && editRecipient != null) {
									if (editRecipient != \"\") {
										if (p.options[p.selectedIndex].text == editRecipient) {
											return;
										}

										if (editRecipient.length > 255 ) {
											" . we_message_reporting::getShowMessageCall(g_l('alert', "[max_name_recipient]"), we_message_reporting::WE_MESSAGE_ERROR) . "
											return;
										}

										if (!inSelectBox(editRecipient)) {
											p.options[p.selectedIndex].text = editRecipient;
											hot = true;
											send_recipients();
										} else {
											" . we_message_reporting::getShowMessageCall(g_l('alert', "[recipient_exists]"), we_message_reporting::WE_MESSAGE_ERROR) . "
										}
									} else {
										" . we_message_reporting::getShowMessageCall(g_l('alert', "[not_entered_recipient]"), we_message_reporting::WE_MESSAGE_ERROR) . "
									}
								}
							}

							function send_recipients() {
								if (hot) {
									var p = document.forms[0].elements[\"we_recipient\"];
									var v = document.forms[0].elements[\"newconf[formmail_values]\"];

									v.value = \"\";

									for (var i = 0; i < p.options.length; i++) {
										v.value += p.options[i].value + \"<#>\" + p.options[i].text + ( (i < (p.options.length -1 )) ? \"<##>\" : \"\");
									}
								}
							}

							function formmailLogOnOff() {
								var formmail_log = document.forms[0].elements[\"newconf[FORMMAIL_LOG]\"];
								var formmail_block = document.forms[0].elements[\"newconf[FORMMAIL_BLOCK]\"];
								var formmail_emptylog = document.forms[0].elements[\"newconf[FORMMAIL_EMPTYLOG]\"];
								var formmail_span = document.forms[0].elements[\"newconf[FORMMAIL_SPAN]\"];
								var formmail_trials = document.forms[0].elements[\"newconf[FORMMAIL_TRIALS]\"];
								var formmail_blocktime = document.forms[0].elements[\"newconf[FORMMAIL_BLOCKTIME]\"];

								var flag = formmail_log.options[formmail_log.selectedIndex].value == 1;

								formmail_emptylog.disabled = !flag;

								formmail_block.disabled = !flag;
								if (formmail_block.options[formmail_block.selectedIndex].value == 1) {
									formmail_span.disabled = !flag;
									formmail_trials.disabled = !flag;
									formmail_blocktime.disabled = !flag;
								}
							}
							function formmailBlockOnOff() {
								var formmail_block = document.forms[0].elements[\"newconf[FORMMAIL_BLOCK]\"];
								var formmail_span = document.forms[0].elements[\"newconf[FORMMAIL_SPAN]\"];
								var formmail_trials = document.forms[0].elements[\"newconf[FORMMAIL_TRIALS]\"];
								var formmail_blocktime = document.forms[0].elements[\"newconf[FORMMAIL_BLOCKTIME]\"];

								var flag = formmail_block.options[formmail_block.selectedIndex].value == 1;

								formmail_span.disabled = !flag;
								formmail_trials.disabled = !flag;
								formmail_blocktime.disabled = !flag;
							}");

				// Build dialog if user has permission
				$_settings[] = array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[formmail_information]'), 2, 450, false), "space" => 0);

				/**
				 * Recipients list
				 */
				$_select_box = new we_html_select(array("class" => "weSelect", "name" => "we_recipient", "size" => "10", "style" => "width: 340px;height:100px", "ondblclick" => "edit_recipient();"));

				$_enabled_buttons = false;

				$DB_WE->query('SELECT ID, Email FROM ' . RECIPIENTS_TABLE . ' ORDER BY Email');

				while($DB_WE->next_record()) {
					$_enabled_buttons = true;
					$_select_box->addOption($DB_WE->f("ID"), $DB_WE->f("Email"));
				}

				// Create needed hidden fields
				$_hidden_fields = we_html_element::htmlHidden(array("name" => "newconf[formmail_values]", "value" => "")) .
					we_html_element::htmlHidden(array("name" => "newconf[formmail_deleted]", "value" => ""));

				// Create edit list
				$_editlist_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 2, 3);

				$_editlist_table->setCol(0, 0, null, $_hidden_fields . $_select_box->getHtml());
				$_editlist_table->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
				$_editlist_table->setCol(0, 2, array("valign" => "top"), we_button::create_button('add', "javascript:add_recipient();") . we_html_tools::getPixel(1, 10) . we_button::create_button("edit", "javascript:edit_recipient();", true, 100, 22, "", "", !$_enabled_buttons, false) . we_html_tools::getPixel(1, 10) . we_button::create_button("delete", "javascript:delete_recipient();", true, 100, 22, "", "", !$_enabled_buttons, false));

				// Build dialog if user has permission
				$_settings[] = array("headline" => "", "html" => $_editlist_table->getHtml(), "space" => 0);
			}

			// formmail stuff


			if(we_base_preferences::userIsAllowed("FORMMAIL_CONFIRM")){
				$_formmail_confirm = new we_html_select(array("name" => "newconf[FORMMAIL_CONFIRM]", "style" => "width:88px;", "class" => "weSelect"));
				$_formmail_confirm->addOption(1, g_l('prefs', '[on]'));
				$_formmail_confirm->addOption(0, g_l('prefs', '[off]'));
				$_formmail_confirm->selectOption(get_value("FORMMAIL_CONFIRM") ? 1 : 0);

				$_settings[] = array('html' => $_formmail_confirm->getHtml(), "space" => 250, "headline" => g_l('prefs', '[formmailConfirm]'));

				$_formmail_log = new we_html_select(array("name" => "newconf[FORMMAIL_LOG]", "onchange" => "formmailLogOnOff()", "style" => "width:88px;", "class" => "weSelect"));
				$_formmail_log->addOption(1, g_l('prefs', '[yes]'));
				$_formmail_log->addOption(0, g_l('prefs', '[no]'));
				$_formmail_log->selectOption(get_value("FORMMAIL_LOG") ? 1 : 0);

				$_html = '<table border="0" cellpading="0" cellspacing="0">
							<tr>
								<td>' . $_formmail_log->getHtml() . '</td>
								<td style="padding-left:10px;">' . we_button::create_button("logbook", 'javascript:we_cmd(\'show_formmail_log\')') . '</td>
							</tr>
						</table>';
				$_settings[] = array('html' => $_html, "space" => 250, "headline" => g_l('prefs', '[logFormmailRequests]'), "noline" => 1);

				$_isDisabled = (get_value("FORMMAIL_LOG") == 0);


				$_formmail_emptylog = new we_html_select(array("name" => "newconf[FORMMAIL_EMPTYLOG]", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_emptylog->setAttribute("disabled", "disabled");
				}
				$_formmail_emptylog->addOption(-1, g_l('prefs', '[never]'));
				$_formmail_emptylog->addOption(86400, g_l('prefs', '[1_day]'));
				$_formmail_emptylog->addOption(172800, sprintf(g_l('prefs', '[more_days]'), 2));
				$_formmail_emptylog->addOption(345600, sprintf(g_l('prefs', '[more_days]'), 4));
				$_formmail_emptylog->addOption(604800, g_l('prefs', '[1_week]'));
				$_formmail_emptylog->addOption(1209600, sprintf(g_l('prefs', '[more_weeks]'), 2));
				$_formmail_emptylog->addOption(2419200, sprintf(g_l('prefs', '[more_weeks]'), 4));
				$_formmail_emptylog->addOption(4838400, sprintf(g_l('prefs', '[more_weeks]'), 8));
				$_formmail_emptylog->addOption(9676800, sprintf(g_l('prefs', '[more_weeks]'), 16));
				$_formmail_emptylog->addOption(19353600, sprintf(g_l('prefs', '[more_weeks]'), 32));

				$_formmail_emptylog->selectOption(get_value("FORMMAIL_EMPTYLOG"));


				$_settings[] = array('html' => $_formmail_emptylog->getHtml(), "space" => 250, "headline" => g_l('prefs', '[deleteEntriesOlder]'));

				// formmail only via we doc //
				$_formmail_ViaWeDoc = new we_html_select(array("name" => "newconf[FORMMAIL_VIAWEDOC]", "style" => "width:88px;", "class" => "weSelect"));
				$_formmail_ViaWeDoc->addOption(1, g_l('prefs', '[yes]'));
				$_formmail_ViaWeDoc->addOption(0, g_l('prefs', '[no]'));
				$_formmail_ViaWeDoc->selectOption((get_value("FORMMAIL_VIAWEDOC") ? 1 : 0));

				$_settings[] = array('html' => $_formmail_ViaWeDoc->getHtml(), "space" => 250, "headline" => g_l('prefs', '[formmailViaWeDoc]'));

				// limit formmail requests //
				$_formmail_block = new we_html_select(array("name" => "newconf[FORMMAIL_BLOCK]", "onchange" => "formmailBlockOnOff()", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_block->setAttribute("disabled", "disabled");
				}
				$_formmail_block->addOption(1, g_l('prefs', '[yes]'));
				$_formmail_block->addOption(0, g_l('prefs', '[no]'));
				$_formmail_block->selectOption(get_value("FORMMAIL_BLOCK") ? 1 : 0);

				$_html = '<table border="0" cellpading="0" cellspacing="0">
							<tr>
								<td>' . $_formmail_block->getHtml() . '</td>
								<td style="padding-left:10px;">' . we_button::create_button("logbook", 'javascript:we_cmd(\'show_formmail_block_log\')') . '</td>
							</tr>
						</table>';

				$_settings[] = array('html' => $_html, "space" => 250, "headline" => g_l('prefs', '[blockFormmail]'), "noline" => 1);

				$_isDisabled = $_isDisabled || (get_value("FORMMAIL_BLOCK") == 0);

				// table is IE fix. Without table IE has a gap on the left of the input
				$_formmail_trials = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' .
					we_html_tools::htmlTextInput("newconf[FORMMAIL_TRIALS]", 24, get_value("FORMMAIL_TRIALS"), "", "", "text", 88, 0, "", $_isDisabled) .
					'</td></tr></table>';

				$_settings[] = array('html' => $_formmail_trials, "space" => 250, "headline" => g_l('prefs', '[formmailTrials]'), "noline" => 1);

				if(!$_isDisabled){
					$_isDisabled = (get_value("FORMMAIL_BLOCK") == 0);
				}

				$_formmail_span = new we_html_select(array("name" => "newconf[FORMMAIL_SPAN]", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_span->setAttribute("disabled", "disabled");
				}
				$_formmail_span->addOption(60, g_l('prefs', '[1_minute]'));
				$_formmail_span->addOption(120, sprintf(g_l('prefs', '[more_minutes]'), 2));
				$_formmail_span->addOption(180, sprintf(g_l('prefs', '[more_minutes]'), 3));
				$_formmail_span->addOption(300, sprintf(g_l('prefs', '[more_minutes]'), 5));
				$_formmail_span->addOption(600, sprintf(g_l('prefs', '[more_minutes]'), 10));
				$_formmail_span->addOption(1200, sprintf(g_l('prefs', '[more_minutes]'), 20));
				$_formmail_span->addOption(1800, sprintf(g_l('prefs', '[more_minutes]'), 30));
				$_formmail_span->addOption(2700, sprintf(g_l('prefs', '[more_minutes]'), 45));
				$_formmail_span->addOption(3600, g_l('prefs', '[1_hour]'));
				$_formmail_span->addOption(7200, sprintf(g_l('prefs', '[more_hours]'), 2));
				$_formmail_span->addOption(14400, sprintf(g_l('prefs', '[more_hours]'), 4));
				$_formmail_span->addOption(28800, sprintf(g_l('prefs', '[more_hours]'), 8));
				$_formmail_span->addOption(86400, sprintf(g_l('prefs', '[more_hours]'), 24));

				$_formmail_span->selectOption(get_value("FORMMAIL_SPAN"));


				$_settings[] = array('html' => $_formmail_span->getHtml(), "space" => 250, "headline" => g_l('prefs', '[formmailSpan]'), "noline" => 1);
				$_formmail_blocktime = new we_html_select(array("name" => "newconf[FORMMAIL_BLOCKTIME]", "style" => "width:88px;", "class" => "weSelect"));
				if($_isDisabled){
					$_formmail_blocktime->setAttribute("disabled", "disabled");
				}
				$_formmail_blocktime->addOption(60, g_l('prefs', '[1_minute]'));
				$_formmail_blocktime->addOption(120, sprintf(g_l('prefs', '[more_minutes]'), 2));
				$_formmail_blocktime->addOption(180, sprintf(g_l('prefs', '[more_minutes]'), 3));
				$_formmail_blocktime->addOption(300, sprintf(g_l('prefs', '[more_minutes]'), 5));
				$_formmail_blocktime->addOption(600, sprintf(g_l('prefs', '[more_minutes]'), 10));
				$_formmail_blocktime->addOption(1200, sprintf(g_l('prefs', '[more_minutes]'), 20));
				$_formmail_blocktime->addOption(1800, sprintf(g_l('prefs', '[more_minutes]'), 30));
				$_formmail_blocktime->addOption(2700, sprintf(g_l('prefs', '[more_minutes]'), 45));
				$_formmail_blocktime->addOption(3600, g_l('prefs', '[1_hour]'));
				$_formmail_blocktime->addOption(7200, sprintf(g_l('prefs', '[more_hours]'), 2));
				$_formmail_blocktime->addOption(14400, sprintf(g_l('prefs', '[more_hours]'), 4));
				$_formmail_blocktime->addOption(28800, sprintf(g_l('prefs', '[more_hours]'), 8));
				$_formmail_blocktime->addOption(86400, sprintf(g_l('prefs', '[more_hours]'), 24));
				$_formmail_blocktime->addOption(-1, g_l('prefs', '[ever]'));

				$_formmail_blocktime->selectOption(get_value("FORMMAIL_BLOCKTIME"));


				$_settings[] = array('html' => $_formmail_blocktime->getHtml(), "space" => 250, "headline" => g_l('prefs', '[blockFor]'), "noline" => 1);
			}

			return create_dialog("", g_l('prefs', '[formmail_recipients]'), $_settings, -1, "", "", false, $_needed_JavaScript);

		case "modules":
			if(!we_base_preferences::userIsAllowed('active_integrated_modules')){
				break;
			}
			$_modInfos = weModuleInfo::getIntegratedModules();

			$_html = "";

			foreach($_modInfos as $_modKey => $_modInfo){
				if(!isset($_modInfo["alwaysActive"])){
					$_modInfo["alwaysActive"] = null;
				}
				$onclick = "";
				if($_modInfo["childmodule"] != ""){
					$onclick = "if(!this.checked){document.getElementById('newconf[active_integrated_modules][" . $_modInfo["childmodule"] . "]').checked=false;}";
				}
				if($_modInfo["dependson"] != ""){
					$onclick = "if(this.checked){document.getElementById('newconf[active_integrated_modules][" . $_modInfo["dependson"] . "]').checked=true;}";
				}
				$_html .= we_forms::checkbox($_modKey, $_modInfo["alwaysActive"] || in_array($_modKey, $GLOBALS["_we_active_integrated_modules"]), "newconf[active_integrated_modules][$_modKey]", $_modInfo["text"], false, "defaultfont", $onclick, $_modInfo["alwaysActive"]) . ($_modInfo["alwaysActive"] ? "<input type=\"hidden\" name=\"newconf[active_integrated_modules][$_modKey]\" value=\"$_modKey\" />" : "" ) . "<br />";
			}

			$_settings = array(
				array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[module_activation][information]'), 2, 450, false), "space" => 0),
				array("headline" => g_l('prefs', '[module_activation][headline]'), "html" => $_html, "space" => 200)
			);

			return create_dialog("", g_l('prefs', '[module_activation][headline]'), $_settings, -1);

		case "proxy":
			if(!we_base_preferences::userIsAllowed('useproxy')){
				break;
			}
			/**
			 * Proxy server
			 */
			// Generate needed JS
			$_needed_JavaScript = we_html_element::jsElement("
							function set_state() {
								if (document.getElementsByName('newconf[useproxy]')[0].checked == true) {
									_new_state = false;
								} else {
									_new_state = true;
								}

								document.getElementsByName('newconf[proxyhost]')[0].disabled = _new_state;
								document.getElementsByName('newconf[proxyport]')[0].disabled = _new_state;
								document.getElementsByName('newconf[proxyuser]')[0].disabled = _new_state;
								document.getElementsByName('newconf[proxypass]')[0].disabled = _new_state;
							}");


			// Check Proxy settings  ...
			$_proxy = get_value("proxy_proxy");

			$_use_proxy = we_forms::checkbox(1, $_proxy, "newconf[useproxy]", g_l('prefs', '[useproxy]'), false, "defaultfont", "set_state();");
			$_proxyaddr = we_html_tools::htmlTextInput("newconf[proxyhost]", 22, get_value("WE_PROXYHOST"), "", "", "text", 225, 0, "", !$_proxy);
			$_proxyport = we_html_tools::htmlTextInput("newconf[proxyport]", 22, get_value("WE_PROXYPORT"), "", "", "text", 225, 0, "", !$_proxy);
			$_proxyuser = we_html_tools::htmlTextInput("newconf[proxyuser]", 22, get_value("WE_PROXYUSER"), "", "", "text", 225, 0, "", !$_proxy);
			$_proxypass = we_html_tools::htmlTextInput("newconf[proxypass]", 22, get_value("WE_PROXYPASSWORD"), "", "", "password", 225, 0, "", !$_proxy);

			// Build dialog if user has permission

			$_settings = array(
				array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[proxy_information]'), 2, 450, false), "space" => 0),
				array("headline" => g_l('prefs', '[tab][proxy]'), "html" => $_use_proxy, "space" => 200),
				array("headline" => g_l('prefs', '[proxyaddr]'), "html" => $_proxyaddr, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[proxyport]'), "html" => $_proxyport, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[proxyuser]'), "html" => $_proxyuser, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[proxypass]'), "html" => $_proxypass, "space" => 200, "noline" => 1),
			);
			// Build dialog element if user has permission
			return create_dialog("", g_l('prefs', '[tab][proxy]'), $_settings, -1, "", "", false, $_needed_JavaScript);


		case "advanced":
			/*			 * *******************************************************************
			 * ATTRIBS
			 * ******************************************************************* */
			if(!we_hasPerm("ADMINISTRATOR")){
				break;
			}


			// Build select box
			$WYSIWYG_TYPE = new we_html_select(array("name" => "newconf[WYSIWYG_TYPE]", "class" => "weSelect"));
			$_options = array('tinyMCE' => 'tinyMCE', 'default' => 'webEdition Editor (deprecated))');
			foreach($_options as $key => $val){
				$WYSIWYG_TYPE->addOption($key, $val);
			}
			$WYSIWYG_TYPE->selectOption(get_value("WYSIWYG_TYPE"));
			$_settings[] = array("headline" => g_l('prefs', '[wysiwyg_type]'), "html" => $WYSIWYG_TYPE->getHtml(), "space" => 200);

			$WYSIWYG_TYPE_FRONTEND = new we_html_select(array("name" => "newconf[WYSIWYG_TYPE_FRONTEND]", "class" => "weSelect"));
			$_options = array('tinyMCE' => 'tinyMCE', 'default' => 'webEdition Editor (deprecated))');
			foreach($_options as $key => $val){
				$WYSIWYG_TYPE_FRONTEND->addOption($key, $val);
			}
			$WYSIWYG_TYPE_FRONTEND->selectOption(get_value("WYSIWYG_TYPE_FRONTEND"));
			$_settings[] = array("headline" => "Editor für textareas im Frontend", "html" => $WYSIWYG_TYPE_FRONTEND->getHtml(), "space" => 200);

			$_we_doctype_workspace_behavior = abs(get_value("WE_DOCTYPE_WORKSPACE_BEHAVIOR"));
			$_we_doctype_workspace_behavior_table = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' .
				we_forms::radiobutton("0", ($_we_doctype_workspace_behavior == "0"), "newconf[WE_DOCTYPE_WORKSPACE_BEHAVIOR]", g_l('prefs', '[we_doctype_workspace_behavior_0]'), true, "defaultfont", "", false, g_l('prefs', '[we_doctype_workspace_behavior_hint0]'), 0, 430) .
				'</td></tr><tr><td style="padding-top:10px;">' .
				we_forms::radiobutton("1", ($_we_doctype_workspace_behavior == "1"), "newconf[WE_DOCTYPE_WORKSPACE_BEHAVIOR]", g_l('prefs', '[we_doctype_workspace_behavior_1]'), true, "defaultfont", "", false, g_l('prefs', '[we_doctype_workspace_behavior_hint1]'), 0, 430) .
				'</td></tr></table>';

			$_settings[] = array("headline" => g_l('prefs', '[we_doctype_workspace_behavior]'), "html" => $_we_doctype_workspace_behavior_table, "space" => 200);

			if(we_base_preferences::userIsAllowed('WE_LOGIN_HIDEWESTATUS')){
				$_loginWEst_disabler = we_forms::checkbox(1, get_value('WE_LOGIN_HIDEWESTATUS') == 1 ? 1 : 0, 'newconf[WE_LOGIN_HIDEWESTATUS]', g_l('prefs', '[login][deactivateWEstatus]'));

				$_we_windowtypes = array('0' => g_l('prefs', '[login][windowtypeboth]'), '1' => g_l('prefs', '[login][windowtypepopup]'), '2' => g_l('prefs', '[login][windowtypesame]'));
				$_we_windowtypeselect = new we_html_select(array('name' => 'newconf[WE_LOGIN_WEWINDOW]', 'class' => 'weSelect'));
				foreach($_we_windowtypes as $key => $value){
					$_we_windowtypeselect->addOption($key, $value);
				}
				$_we_windowtypeselect->selectOption(get_value('WE_LOGIN_WEWINDOW'));
				// Build dialog if user has permission
				$_settings[] = array('headline' => g_l('prefs', '[login][login]'), 'html' => $_loginWEst_disabler . we_html_element::htmlBr() . g_l('prefs', '[login][windowtypes]') . we_html_element::htmlBr() . $_we_windowtypeselect->getHtml(), 'space' => 200);
			}

			if(defined('SCHEDULE_TABLE')){
				$_Schedtrigger_setting = new we_html_select(array("name" => "newconf[SCHEDULER_TRIGGER]", "class" => "weSelect"));
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_PREDOC, g_l('prefs', '[we_scheduler_trigger][preDoc]')); //pre
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_POSTDOC, g_l('prefs', '[we_scheduler_trigger][postDoc]')); //post
				$_Schedtrigger_setting->addOption(SCHEDULER_TRIGGER_CRON, g_l('prefs', '[we_scheduler_trigger][cron]')); //cron
				$_Schedtrigger_setting->selectOption(get_value("SCHEDULER_TRIGGER"));
				$tmp = '<div>' . $_Schedtrigger_setting->getHtml() . '<br/>' . we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[we_scheduler_trigger][description]'), 2, 430, false) . '</div>';
				$_settings[] = array("headline" => g_l('prefs', '[we_scheduler_trigger][head]'), "html" => $tmp, "space" => 200);
			}

			return create_dialog("", g_l('prefs', '[tab][advanced]'), $_settings, -1, '', '', null, isset($_needed_JavaScript) ? $_needed_JavaScript : '');

		case "system":
			if(!we_hasPerm("ADMINISTRATOR")){
				break;
			}

			$_we_max_upload_size = abs(get_value("WE_MAX_UPLOAD_SIZE"));
			$_we_max_upload_size = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[WE_MAX_UPLOAD_SIZE]", 22, $_we_max_upload_size, "", ' onkeypress="return IsDigit(event);"', "text", 60) . '</td><td style="padding-left:20px;" class="small">' .
				g_l('prefs', '[we_max_upload_size_hint]') .
				'</td></tr></table>';
			$_needed_JavaScript = we_html_element::jsElement('function IsDigit(e) {
					var key;

					if (e != null && e.charCode) {
						key = e.charCode;
					} else {
						key = event.keyCode;
					}

					return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13));
				}');

			$_we_new_folder_mod = get_value("WE_NEW_FOLDER_MOD");
			$_we_new_folder_mod = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' .
				we_html_tools::htmlTextInput("newconf[WE_NEW_FOLDER_MOD]", 22, $_we_new_folder_mod, 3, ' onkeypress="return IsDigit(event);"', "text", 60) . '</td><td style="padding-left:20px;" class="small">' .
				g_l('prefs', '[we_new_folder_mod_hint]') .
				'</td></tr></table>';

			// Build db select box
			$_db_connect = new we_html_select(array("name" => "newconf[DB_CONNECT]", "class" => "weSelect"));
			if(function_exists('mysql_connect')){
				$_db_connect->addOption('connect', "connect");
				$_db_connect->addOption('pconnect', "pconnect");
			}
			if(class_exists('mysqli', false)){
				$_db_connect->addOption('mysqli_connect', "mysqli_connect");
				$_db_connect->addOption('mysqli_pconnect', "mysqli_pconnect");
			}
			$_db_connect->selectOption(DB_CONNECT);

			// Build db charset select box
			$html_db_charset_information = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[db_set_charset_information]'), 2, 240, false, 40) . "<br/>";
			$html_db_charset_warning = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[db_set_charset_warning]'), 1, 240, false, 40) . "<br/>";

			$_db_set_charset = new we_html_select(array("name" => "newconf[DB_SET_CHARSET]", "class" => "weSelect"));

			$GLOBALS['DB_WE']->query('SHOW CHARACTER SET');

			$charsets = array('');
			while($GLOBALS['DB_WE']->next_record()) {
				$charsets[] = $GLOBALS['DB_WE']->f('Charset');
			}
			sort($charsets);
			foreach($charsets as $charset){
				$_db_set_charset->addOption($charset, $charset);
			}

			if(defined('DB_SET_CHARSET') && DB_SET_CHARSET != ''){
				$_db_set_charset->selectOption(DB_SET_CHARSET);
			} else{
				$tmp = $GLOBALS['DB_WE']->getCurrentCharset();
				if($tmp){
					$_db_set_charset->selectOption($tmp);
					$_file = &$GLOBALS['config_files']['conf_global']['content'];
					$_file = we_base_preferences::changeSourceCode('define', $_file, 'DB_SET_CHARSET', $tmp);
				}
			}

			// Generate needed JS
			$_needed_JavaScript .= we_html_element::jsElement("
							function set_state_auth() {
								if (document.getElementsByName('useauthEnabler')[0].checked == true) {
                                    document.getElementsByName('newconf[useauth]')[0].value = 1;
									_new_state = false;
								} else {
                                    document.getElementsByName('newconf[useauth]')[0].value = 0;
									_new_state = true;
								}

								document.getElementsByName('newconf[HTTP_USERNAME]')[0].disabled = _new_state;
								document.getElementsByName('newconf[HTTP_PASSWORD]')[0].disabled = _new_state;
							}");

			// Check authentication settings  ...
			$_auth = get_value("HTTP_USERNAME");
			$_auth_user = get_value("HTTP_USERNAME");
			$_auth_pass = get_value("HTTP_PASSWORD");

			// Build dialog if user has permission
			$_use_auth = we_html_tools::hidden('newconf[useauth]', $_auth) .
				we_forms::checkbox(1, $_auth, "useauthEnabler", g_l('prefs', '[useauth]'), false, "defaultfont", "set_state_auth();");

			/**
			 * User name
			 */
			$_authuser = we_html_tools::htmlTextInput("newconf[HTTP_USERNAME]", 22, $_auth_user, "", "", "text", 225, 0, "", !$_auth);
			$_authpass = we_html_tools::htmlTextInput("newconf[HTTP_PASSWORD]", 22, $_auth_pass, "", "", "password", 225, 0, "", !$_auth);


			if(we_image_edit::gd_version() > 0){ //  gd lib ist installiert
				$wecmdenc1 = we_cmd_enc("document.forms[0].elements['newconf[WE_THUMBNAIL_DIRECTORY]'].value");
				$wecmdenc4 = '';
				$_but = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_button::create_button("select", "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', 'folder', document.forms[0].elements['newconf[WE_THUMBNAIL_DIRECTORY]'].value, '')") : "";
				$_inp = we_html_tools::htmlTextInput("newconf[WE_THUMBNAIL_DIRECTORY]", 12, get_value("WE_THUMBNAIL_DIRECTORY"), "", "", "text", 125);
				$_thumbnail_dir = we_button::create_button_table(array($_inp, $_but));
			} else{ //  gd lib ist nicht installiert
				$_but = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_button::create_button("select", "#", true, 100, 22, '', '', true) : "";
				$_inp = we_html_tools::htmlTextInput("newconf[WE_THUMBNAIL_DIRECTORY]", 12, get_value("WE_THUMBNAIL_DIRECTORY"), "", "", "text", 125, '0', '', true);
				$_thumbnail_dir = we_button::create_button_table(array($_inp, $_but)) . '<br/>' . g_l('thumbnails', "[add_description_nogdlib]");
			}

			/**
			 * set pageLogger dir
			 */
			$wecmdenc1 = we_cmd_enc("document.forms[0].elements['newconf[WE_TRACKER_DIR]'].value");
			$wecmdenc4 = '';
			$_but = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_button::create_button("select", "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', 'folder', document.forms[0].elements['newconf[WE_TRACKER_DIR]'].value, '')") : "";
			$_inp = we_html_tools::htmlTextInput("newconf[WE_TRACKER_DIR]", 12, get_value("WE_TRACKER_DIR"), "", "", "text", 125);
			$_we_tracker_dir = we_button::create_button_table(array($_inp, $_but));

			// Build select box
			$NAVIGATION_ENTRIES_FROM_DOCUMENT = new we_html_select(array("name" => "newconf[NAVIGATION_ENTRIES_FROM_DOCUMENT]", "class" => "weSelect"));
			for($i = 0; $i < 2; $i++){
				$NAVIGATION_ENTRIES_FROM_DOCUMENT->addOption($i, $i == 0 ? g_l('prefs', '[navigation_entries_from_document_folder]') : g_l('prefs', '[navigation_entries_from_document_item]'));
			}
			$NAVIGATION_ENTRIES_FROM_DOCUMENT->selectOption(get_value("NAVIGATION_ENTRIES_FROM_DOCUMENT") ? 1 : 0);


			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH = new we_html_select(array("name" => "newconf[NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH]", "class" => "weSelect"));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->addOption(0, g_l('prefs', '[no]'));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->addOption(1, g_l('prefs', '[yes]'));
			$NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->selectOption(get_value("NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH") ? 1 : 0);

			//  select if hooks can be executed
			$EXECUTE_HOOKS = new we_html_select(array("name" => "newconf[EXECUTE_HOOKS]", "class" => "weSelect"));
			$EXECUTE_HOOKS->addOption(0, g_l('prefs', '[no]'));
			$EXECUTE_HOOKS->addOption(1, g_l('prefs', '[yes]'));

			$EXECUTE_HOOKS->selectOption(get_value("EXECUTE_HOOKS") ? 1 : 0);

			$hooksHtml = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[hooks_information]'), 2, 240, false) . "<br/>" .
				$EXECUTE_HOOKS->getHtml();

			//  select how php is parsed
			$PHPLOCALSCOPE = new we_html_select(array("name" => "newconf[PHPLOCALSCOPE]", "class" => "weSelect"));
			$PHPLOCALSCOPE->addOption(0, g_l('prefs', '[no]'));
			$PHPLOCALSCOPE->addOption(1, g_l('prefs', '[yes]'));

			$PHPLOCALSCOPE->selectOption(get_value("PHPLOCALSCOPE") ? 1 : 0);

			$phpLocalScopeHtml = we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[phpLocalScope_information]'), 2, 240, false) . "<br/>" .
				$PHPLOCALSCOPE->getHtml();


			$_settings = array(
				array("headline" => g_l('prefs', '[we_max_upload_size]'), "html" => $_we_max_upload_size, "space" => 200),
				array("headline" => g_l('prefs', '[we_new_folder_mod]'), "html" => $_we_new_folder_mod, "space" => 200),
				array("headline" => g_l('prefs', '[db_connect]'), "html" => $_db_connect->getHtml(), "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[db_set_charset]'), "html" => $html_db_charset_information . $_db_set_charset->getHtml() . $html_db_charset_warning, "space" => 200),
				array("headline" => g_l('prefs', '[auth]'), "html" => $_use_auth, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[authuser]'), "html" => $_authuser, "space" => 200, "noline" => 1),
				array("headline" => g_l('prefs', '[authpass]'), "html" => $_authpass, "space" => 200),
				array("headline" => g_l('prefs', '[thumbnail_dir]'), "html" => $_thumbnail_dir, "space" => 200),
				array("headline" => g_l('prefs', '[pagelogger_dir]'), "html" => $_we_tracker_dir, "space" => 200),
				array("headline" => g_l('prefs', '[navigation_entries_from_document]'), "html" => $NAVIGATION_ENTRIES_FROM_DOCUMENT->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[navigation_rules_continue]'), "html" => $NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[hooks]'), "html" => $hooksHtml, "space" => 200),
				array("headline" => g_l('prefs', '[phpLocalScope]'), "html" => $phpLocalScopeHtml, "space" => 200),
			);
			// Build dialog element if user has permission
			return create_dialog("", g_l('prefs', '[tab][system]'), $_settings, -1, "", "", null, $_needed_JavaScript);

		case "seolinks":
			/*			 * *******************************************************************
			 * ATTRIBS
			 * ******************************************************************* */
			if(!we_hasPerm("ADMINISTRATOR")){
				break;
			}
			$_needed_JavaScript = "";
			// Build dialog if user has permission

			$_settings = array(
				array("headline" => g_l('prefs', '[general_directoryindex_hide]'), "html" => "", "space" => 480, "noline" => 1),
				array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[navigation_directoryindex_description]'), 2, 480), "noline" => 1),
			);

			$NAVIGATION_DIRECTORYINDEX_HIDE = new we_html_select(array("name" => "newconf[NAVIGATION_DIRECTORYINDEX_HIDE]", "class" => "weSelect"));
			$NAVIGATION_DIRECTORYINDEX_HIDE->addOption(0, "false");
			$NAVIGATION_DIRECTORYINDEX_HIDE->addOption(1, "true");
			$NAVIGATION_DIRECTORYINDEX_HIDE->selectOption(get_value("NAVIGATION_DIRECTORYINDEX_HIDE") ? 1 : 0);
			$_settings[] = array("headline" => g_l('prefs', '[navigation_directoryindex_hide]'), "html" => $NAVIGATION_DIRECTORYINDEX_HIDE->getHtml(), "space" => 200, "noline" => 1);

			$WYSIWYGLINKS_DIRECTORYINDEX_HIDE = new we_html_select(array("name" => "newconf[WYSIWYGLINKS_DIRECTORYINDEX_HIDE]", "class" => "weSelect"));
			$WYSIWYGLINKS_DIRECTORYINDEX_HIDE->addOption(0, "false");
			$WYSIWYGLINKS_DIRECTORYINDEX_HIDE->addOption(1, "true");
			$WYSIWYGLINKS_DIRECTORYINDEX_HIDE->selectOption(get_value("WYSIWYGLINKS_DIRECTORYINDEX_HIDE") ? 1 : 0);
			$_settings[] = array("headline" => g_l('prefs', '[wysiwyglinks_directoryindex_hide]'), "html" => $WYSIWYGLINKS_DIRECTORYINDEX_HIDE->getHtml(), "space" => 200, "noline" => 1);

			$_navigation_directoryindex_names = we_html_tools::htmlTextInput("newconf[NAVIGATION_DIRECTORYINDEX_NAMES]", 22, get_value("NAVIGATION_DIRECTORYINDEX_NAMES"), "", "", "text", 225);
			$_settings[] = array("headline" => g_l('prefs', '[navigation_directoryindex_names]'), "html" => $_navigation_directoryindex_names, "space" => 200, "noline" => 1);

			$_settings[] = array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_directoryindex_hide_description]'), 2, 480), "noline" => 1);

			$TAGLINKS_DIRECTORYINDEX_HIDE = new we_html_select(array("name" => "newconf[TAGLINKS_DIRECTORYINDEX_HIDE]", "class" => "weSelect"));
			$TAGLINKS_DIRECTORYINDEX_HIDE->addOption(0, "false");
			$TAGLINKS_DIRECTORYINDEX_HIDE->addOption(1, "true");
			$TAGLINKS_DIRECTORYINDEX_HIDE->selectOption(get_value("TAGLINKS_DIRECTORYINDEX_HIDE") ? 1 : 0);
			$_settings[] = array("headline" => g_l('prefs', '[taglinks_directoryindex_hide]'), "html" => $TAGLINKS_DIRECTORYINDEX_HIDE->getHtml(), "space" => 200);


			$_settings[] = array("headline" => g_l('prefs', '[general_objectseourls]'), "noline" => 1);
			$NAVIGATION_OBJECTSEOURLS = new we_html_select(array("name" => "newconf[NAVIGATION_OBJECTSEOURLS]", "class" => "weSelect"));
			$NAVIGATION_OBJECTSEOURLS->addOption(0, "false");
			$NAVIGATION_OBJECTSEOURLS->addOption(1, "true");
			$NAVIGATION_OBJECTSEOURLS->selectOption(get_value("NAVIGATION_OBJECTSEOURLS"));

			$_settings[] = array("headline" => g_l('prefs', '[navigation_objectseourls]'), "html" => $NAVIGATION_OBJECTSEOURLS->getHtml(), "space" => 200, "noline" => 1);

			$WYSIWYGLINKS_OBJECTSEOURLS = new we_html_select(array("name" => "newconf[WYSIWYGLINKS_OBJECTSEOURLS]", "class" => "weSelect"));
			$WYSIWYGLINKS_OBJECTSEOURLS->addOption(0, "false");
			$WYSIWYGLINKS_OBJECTSEOURLS->addOption(1, "true");
			$WYSIWYGLINKS_OBJECTSEOURLS->selectOption(get_value("WYSIWYGLINKS_OBJECTSEOURLS"));

			$_settings[] = array("headline" => g_l('prefs', '[wysiwyglinks_objectseourls]'), "html" => $WYSIWYGLINKS_OBJECTSEOURLS->getHtml(), "space" => 200, "noline" => 1);
			$_settings[] = array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_objectseourls_description]'), 2, 480), "noline" => 1);

			$TAGLINKS_OBJECTSEOURLS = new we_html_select(array("name" => "newconf[TAGLINKS_OBJECTSEOURLS]", "class" => "weSelect"));
			$TAGLINKS_OBJECTSEOURLS->addOption(0, "false");
			$TAGLINKS_OBJECTSEOURLS->addOption(1, "true");
			$TAGLINKS_OBJECTSEOURLS->selectOption(get_value("TAGLINKS_OBJECTSEOURLS"));

			$_settings[] = array("headline" => g_l('prefs', '[taglinks_objectseourls]'), "html" => $TAGLINKS_OBJECTSEOURLS->getHtml(), "space" => 200, "noline" => 1);

			$URLENCODE_OBJECTSEOURLS = new we_html_select(array("name" => "newconf[URLENCODE_OBJECTSEOURLS]", "class" => "weSelect"));
			$URLENCODE_OBJECTSEOURLS->addOption(0, "false");
			$URLENCODE_OBJECTSEOURLS->addOption(1, "true");
			$URLENCODE_OBJECTSEOURLS->selectOption(get_value("URLENCODE_OBJECTSEOURLS"));

			$_settings[] = array("headline" => g_l('prefs', '[urlencode_objectseourls]'), "html" => $URLENCODE_OBJECTSEOURLS->getHtml(), "space" => 200);
			$_settings[] = array("headline" => g_l('prefs', '[general_seoinside]'), "noline" => 1);
			$_settings[] = array("html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[general_seoinside_description]'), 2, 480), "noline" => 1);

			$SEOINSIDE_HIDEINEDITMODE = new we_html_select(array("name" => "newconf[SEOINSIDE_HIDEINEDITMODE]", "class" => "weSelect"));
			$SEOINSIDE_HIDEINEDITMODE->addOption(0, "false");
			$SEOINSIDE_HIDEINEDITMODE->addOption(1, "true");
			$SEOINSIDE_HIDEINEDITMODE->selectOption(get_value("SEOINSIDE_HIDEINEDITMODE"));
			$_settings[] = array("headline" => g_l('prefs', '[seoinside_hideineditmode]'), "html" => $SEOINSIDE_HIDEINEDITMODE->getHtml(), "space" => 200, "noline" => 1);

			$SEOINSIDE_HIDEINWEBEDITION = new we_html_select(array("name" => "newconf[SEOINSIDE_HIDEINWEBEDITION]", "class" => "weSelect"));
			$SEOINSIDE_HIDEINWEBEDITION->addOption(0, "false");
			$SEOINSIDE_HIDEINWEBEDITION->addOption(1, "true");
			$SEOINSIDE_HIDEINWEBEDITION->selectOption(get_value("SEOINSIDE_HIDEINWEBEDITION"));
			$_settings[] = array("headline" => g_l('prefs', '[seoinside_hideinwebedition]'), "html" => $SEOINSIDE_HIDEINWEBEDITION->getHtml(), "space" => 200);

			$wecmdenc1 = we_cmd_enc("document.forms[0].elements['newconf[ERROR_DOCUMENT_NO_OBJECTFILE]'].value");
			$wecmdenc2 = we_cmd_enc("document.forms[0].elements['error_document_no_objectfile_text'].value");
			$_acButton1 = we_button::create_button('select', "javascript:we_cmd('openDocselector', document.forms[0].elements['newconf[ERROR_DOCUMENT_NO_OBJECTFILE]'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "','','" . session_id() . "','', 'text/webEdition,text/html', 1)");
			$_acButton2 = we_button::create_button('image:btn_function_trash', 'javascript:document.forms[0].elements[\'newconf[ERROR_DOCUMENT_NO_OBJECTFILE]\'].value = 0;document.forms[0].elements[\'error_document_no_objectfile_text\'].value = \'\'');

			$yuiSuggest->setAcId("doc2");
			$yuiSuggest->setContentType("folder,text/webEdition,text/html");
			$yuiSuggest->setInput('error_document_no_objectfile_text', ( ERROR_DOCUMENT_NO_OBJECTFILE ? id_to_path(ERROR_DOCUMENT_NO_OBJECTFILE) : ''));
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult('newconf[ERROR_DOCUMENT_NO_OBJECTFILE]', ( ERROR_DOCUMENT_NO_OBJECTFILE ? ERROR_DOCUMENT_NO_OBJECTFILE : 0));
			$yuiSuggest->setSelector("Docselector");
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton($_acButton1, 10);
			$yuiSuggest->setTrashButton($_acButton2, 4);

			$_settings[] = array('headline' => g_l('prefs', '[error_no_object_found]'), 'html' => $yuiSuggest->getHTML(), 'space' => 200, "noline" => 1);

			$SUPPRESS404CODE = new we_html_select(array("name" => "newconf[SUPPRESS404CODE]", "class" => "weSelect"));
			$SUPPRESS404CODE->addOption(0, "false");
			$SUPPRESS404CODE->addOption(1, "true");
			$SUPPRESS404CODE->selectOption(get_value("SUPPRESS404CODE"));
			$_settings[] = array("headline" => g_l('prefs', '[suppress404code]'), "html" => $SUPPRESS404CODE->getHtml(), "space" => 200, "noline" => 0);

			return create_dialog("", g_l('prefs', '[tab][seolinks]'), $_settings, -1, "", "", null, $_needed_JavaScript);

		case "error_handling":
			/*			 * *******************************************************************
			 * ERROR TYPES
			 * ******************************************************************* */
			if(!we_hasPerm("ADMINISTRATOR")){
				break;
			}

			// Generate needed JS
			$_needed_JavaScript = we_html_element::jsElement("
							function set_state_error_handler() {
								if (document.getElementsByName('newconf[WE_ERROR_HANDLER]')[0].checked == true) {
									_new_state = false;
									_new_style = 'black';
									_new_cursor = document.all ? 'hand' : 'pointer';
								} else {
									_new_state = true;
									_new_style = 'gray';
									_new_cursor = '';
								}

								document.getElementsByName('newconf[WE_ERROR_NOTICES]')[0].disabled = _new_state;
								document.getElementsByName('newconf[WE_ERROR_WARNINGS]')[0].disabled = _new_state;
								document.getElementsByName('newconf[WE_ERROR_ERRORS]')[0].disabled = _new_state;
								document.getElementsByName('newconf[WE_ERROR_DEPRECATED]')[0].disabled = _new_state;

								document.getElementById('label_newconf[WE_ERROR_NOTICES]').style.color = _new_style;
								document.getElementById('label_newconf[WE_ERROR_WARNINGS]').style.color = _new_style;
								document.getElementById('label_newconf[WE_ERROR_ERRORS]').style.color = _new_style;
								document.getElementById('label_newconf[WE_ERROR_DEPRECATED]').style.color = _new_style;

								document.getElementById('label_newconf[WE_ERROR_NOTICES]').style.cursor = _new_cursor;
								document.getElementById('label_newconf[WE_ERROR_WARNINGS]').style.cursor = _new_cursor;
								document.getElementById('label_newconf[WE_ERROR_ERRORS]').style.cursor = _new_cursor;
								document.getElementById('label_newconf[WE_ERROR_DEPRECATED]').style.cursor = _new_cursor;

								document.getElementsByName('newconf[WE_ERROR_SHOW]')[0].disabled = _new_state;
								document.getElementsByName('newconf[WE_ERROR_LOG]')[0].disabled = _new_state;
								document.getElementsByName('newconf[WE_ERROR_MAIL]')[0].disabled = _new_state;

								document.getElementById('label_newconf[WE_ERROR_SHOW]').style.color = _new_style;
								document.getElementById('label_newconf[WE_ERROR_LOG]').style.color = _new_style;
								document.getElementById('label_newconf[WE_ERROR_MAIL]').style.color = _new_style;

								document.getElementById('label_newconf[WE_ERROR_SHOW]').style.cursor = _new_cursor;
								document.getElementById('label_newconf[WE_ERROR_LOG]').style.cursor = _new_cursor;
								document.getElementById('label_newconf[WE_ERROR_MAIL]').style.cursor = _new_cursor;
							}");

			/**
			 * Error handler
			 */
			$_foldAt = 4;

			// Create checkboxes
			$_template_error_handling_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 8, 1);
			$_template_error_handling_table->setCol(0, 0, null, we_forms::checkbox(1, get_value('DISABLE_TEMPLATE_CODE_CHECK'), 'DISABLE_TEMPLATE_CODE_CHECK', g_l('prefs', '[disable_template_code_check]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[DISABLE_TEMPLATE_CODE_CHECK]\');') .
				we_html_tools::hidden('newconf[DISABLE_TEMPLATE_CODE_CHECK]', get_value('DISABLE_TEMPLATE_CODE_CHECK')));

			/* $_template_error_handling_table->setCol(1, 0, null, we_forms::checkbox(1, get_value('DISABLE_TEMPLATE_PARSER'), 'DISABLE_TEMPLATE_PARSER', g_l('prefs', '[disable_template_parser]'), true, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[DISABLE_TEMPLATE_PARSER]\');') .
			  we_html_tools::hidden('newconf[DISABLE_TEMPLATE_PARSER]', get_value('DISABLE_TEMPLATE_PARSER')));
			 */


			// Create checkboxes
			$_we_error_handler = we_forms::checkbox(1, get_value("WE_ERROR_HANDLER"), "newconf[WE_ERROR_HANDLER]", g_l('prefs', '[error_use_handler]'), false, "defaultfont", "set_state_error_handler();");

			// Error types
			// Create checkboxes
			$_error_handling_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 8, 1);

			$_error_handling_table->setCol(0, 0, null, we_forms::checkbox(1, get_value("WE_ERROR_ERRORS"), "newconf[WE_ERROR_ERRORS]", g_l('prefs', '[error_errors]'), false, "defaultfont", "", !get_value("WE_ERROR_HANDLER")));
			$_error_handling_table->setCol(1, 0, null, we_html_tools::getPixel(1, 5));
			$_error_handling_table->setCol(2, 0, null, we_forms::checkbox(1, get_value("WE_ERROR_WARNINGS"), "newconf[WE_ERROR_WARNINGS]", g_l('prefs', '[error_warnings]'), false, "defaultfont", "", !get_value("WE_ERROR_HANDLER")));
			$_error_handling_table->setCol(3, 0, null, we_html_tools::getPixel(1, 5));
			$_error_handling_table->setCol(4, 0, null, we_forms::checkbox(1, get_value("WE_ERROR_NOTICES"), "newconf[WE_ERROR_NOTICES]", g_l('prefs', '[error_notices]'), false, "defaultfont", "", !get_value("WE_ERROR_HANDLER")));
			$_error_handling_table->setCol(5, 0, null, we_html_tools::getPixel(1, 5));
			$_error_handling_table->setCol(6, 0, null, we_forms::checkbox(1, get_value("WE_ERROR_DEPRECATED"), "newconf[WE_ERROR_DEPRECATED]", g_l('prefs', '[error_deprecated]'), false, "defaultfont", "", !get_value("WE_ERROR_HANDLER")));
			$_error_handling_table->setCol(7, 0, null, we_html_tools::getPixel(1, 5));

			// Create checkboxes
			$_error_display_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 8, 1);
			$_error_display_table->setCol(0, 0, array('class' => 'defaultfont', 'style' => 'padding-left: 25px;'), we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[error_notices_warning]'), 1, 260));

			$_error_display_table->setCol(1, 0, null, we_forms::checkbox(1, get_value("WE_ERROR_SHOW"), "newconf[WE_ERROR_SHOW]", g_l('prefs', '[error_display]'), false, "defaultfont", "", !get_value("WE_ERROR_HANDLER")));
			$_error_display_table->setCol(2, 0, null, we_html_tools::getPixel(1, 5));
			$_error_display_table->setCol(3, 0, null, we_forms::checkbox(1, get_value("WE_ERROR_LOG"), "newconf[WE_ERROR_LOG]", g_l('prefs', '[error_log]'), false, "defaultfont", "", !get_value("WE_ERROR_HANDLER")));
			$_error_display_table->setCol(4, 0, null, we_html_tools::getPixel(1, 5));
			$_error_display_table->setCol(5, 0, null, we_forms::checkbox(1, get_value("WE_ERROR_MAIL"), "newconf[WE_ERROR_MAIL]", g_l('prefs', '[error_mail]'), false, "defaultfont", "", !get_value("WE_ERROR_HANDLER")));

			// Create specify mail address input
			$_error_mail_specify_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 1, 4);

			$_error_mail_specify_table->setCol(0, 0, null, we_html_tools::getPixel(25, 1));
			$_error_mail_specify_table->setCol(0, 1, array("class" => "defaultfont"), g_l('prefs', '[error_mail_address]') . ":");
			$_error_mail_specify_table->setCol(0, 2, null, we_html_tools::getPixel(6, 1));
			$_error_mail_specify_table->setCol(0, 3, array("align" => "left"), we_html_tools::htmlTextInput("newconf[WE_ERROR_MAIL_ADDRESS]", 6, (get_value("WE_ERROR_MAIL_ADDRESS")), 100, "", "text", 195));

			$_error_display_table->setCol(6, 0, null, we_html_tools::getPixel(1, 10));
			$_error_display_table->setCol(7, 0, null, $_error_mail_specify_table->getHtml());

			$_settings = array(
				array("headline" => g_l('prefs', '[templates]'), "html" => $_template_error_handling_table->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[tab][error_handling]'), "html" => $_we_error_handler, "space" => 200),
				array("headline" => g_l('prefs', '[error_types]'), "html" => $_error_handling_table->getHtml(), "space" => 200),
				array("headline" => g_l('prefs', '[error_displaying]'), "html" => $_error_display_table->getHtml(), "space" => 200),
			);

			$_settings_cookie = weGetCookieVariable("but_settings_error_expert");

			return create_dialog("settings_error_expert", g_l('prefs', '[tab][error_handling]'), $_settings, $_foldAt, g_l('prefs', '[show_expert]'), g_l('prefs', '[hide_expert]'), $_settings_cookie, $_needed_JavaScript);

		/*		 * *******************************************************************
		 * Validation (XHTML)
		 * ******************************************************************* */
		case 'message_reporting':

			$_val = get_value('message_reporting');

			$_js = we_html_element::jsElement('
			function handle_message_reporting_click() {
				val = 0;
				var fields = new Array("message_reporting_notices", "message_reporting_warnings", "message_reporting_errors");
				for (i=0;i<fields.length;i++) {

					if (document.getElementById(fields[i]).checked) {
						val += parseInt(document.getElementById(fields[i]).value);
					}
				}
				document.getElementById("message_reporting").value = val;

			}');


			$_html =
				"<input type=\"hidden\" id=\"message_reporting\" name=\"newconf[message_reporting]\" value=\"$_val\" />" . we_forms::checkbox(we_message_reporting::WE_MESSAGE_ERROR, 1, "message_reporting_errors", g_l('prefs', '[message_reporting][show_errors]'), false, "defaultfont", "handle_message_reporting_click();", true) . "<br />" .
				we_forms::checkbox(we_message_reporting::WE_MESSAGE_WARNING, $_val & we_message_reporting::WE_MESSAGE_WARNING, "message_reporting_warnings", g_l('prefs', '[message_reporting][show_warnings]'), false, "defaultfont", "handle_message_reporting_click();") . "<br />" .
				we_forms::checkbox(we_message_reporting::WE_MESSAGE_NOTICE, $_val & we_message_reporting::WE_MESSAGE_NOTICE, "message_reporting_notices", g_l('prefs', '[message_reporting][show_notices]'), false, "defaultfont", "handle_message_reporting_click();");

			$_settings = array(
				array("headline" => "", "html" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[message_reporting][information]'), 2, 450, false), "space" => 0),
				array("headline" => g_l('prefs', '[message_reporting][headline]'), "html" => $_html, "space" => 200),
			);

			return create_dialog("settings_message_reporting", g_l('prefs', '[tab][message_reporting]'), $_settings, -1, "", "", false, $_js);

		/*		 * *******************************************************************
		 * Validation (XHTML)
		 * ******************************************************************* */
		case 'validation':
			if(!we_hasPerm("ADMINISTRATOR")){
				break;
			}

			$js = we_html_element::jsElement('
            mainXhtmlFields  = Array("setXhtml_remove_wrong","setXhtml_show_wrong");
            showXhtmlFields = Array("setXhtml_show_wrong_text","setXhtml_show_wrong_js","setXhtml_show_wrong_error_log");

            function disable_xhtml_fields(val,fields){
                for(i=0;i<fields.length;i++){
                    elem = document.forms[0][fields[i]];
                    label = document.getElementById("label_" + fields[i]);
                    if(val == 1){
                        elem.disabled = false;
                        label.style.color = "black";
                        label.style.cursor = document.all ? "hand" : "pointer";
                    } else {
                        elem.disabled = true;
                        label.style.color = "grey";
                        label.style.cursor = "";
                    }
                }
            }

            function set_xhtml_field(val, field){
                document.forms[0][field].value = (val ? 1 : 0);
            }');

			//   select xhtml_default in we:tags
			$_xhtml_setting = new we_html_select(array("name" => "newconf[XHTML_DEFAULT]", "class" => "weSelect"));
			$_xhtml_setting->addOption(0, 'false');
			$_xhtml_setting->addOption(1, 'true');

			$_xhtml_setting->selectOption(get_value("XHTML_DEFAULT") ? 1 : 0);

			//  activate xhtml_debug
			$_xhtml_debug = we_forms::checkbox(1, get_value("XHTML_DEBUG"), "setXhtml_debug", g_l('prefs', '[xhtml_debug_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[XHTML_DEBUG]\');disable_xhtml_fields(this.checked, mainXhtmlFields);disable_xhtml_fields((document.forms[0][\'setXhtml_show_wrong\'].checked && this.checked), showXhtmlFields);') .
				we_html_tools::hidden('newconf[XHTML_DEBUG]', get_value("XHTML_DEBUG"));

			//  activate xhtml_remove_wrong
			$_xhtml_remove_wrong = we_forms::checkbox(1, get_value("XHTML_REMOVE_WRONG"), "setXhtml_remove_wrong", g_l('prefs', '[xhtml_remove_wrong]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'xhtml_remove_wrong\');', !get_value('XHTML_DEBUG')) .
				we_html_tools::hidden('newconf[XHTML_REMOVE_WRONG]', get_value("XHTML_REMOVE_WRONG"));

			//  activate xhtml_show_wrong
			$_xhtml_show_wrong = we_forms::checkbox(1, get_value("xhtml_show_wrong"), "setXhtml_show_wrong", g_l('prefs', '[xhtml_show_wrong_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong]\');disable_xhtml_fields(this.checked,showXhtmlFields);', !get_value('XHTML_DEBUG')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong]', get_value("xhtml_show_wrong"));

			//  activate xhtml_show_wrong_text
			$_xhtml_show_wrong_text = we_forms::checkbox(1, get_value("xhtml_show_wrong_text"), 'setXhtml_show_wrong_text', g_l('prefs', '[xhtml_show_wrong_text_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong_text]\');', !get_value('xhtml_show_wrong')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong_text]', get_value("xhtml_show_wrong_text"));

			//  activate xhtml_show_wrong_text
			$_xhtml_show_wrong_js = we_forms::checkbox(1, get_value("xhtml_show_wrong_js"), 'setXhtml_show_wrong_js', g_l('prefs', '[xhtml_show_wrong_js_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong_js]\');', !get_value('xhtml_show_wrong')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong_js]', get_value("xhtml_show_wrong_js"));

			//  activate xhtml_show_wrong_text
			$_xhtml_show_wrong_error_log = we_forms::checkbox(1, get_value("xhtml_show_wrong_error_log"), "setXhtml_show_wrong_error_log", g_l('prefs', '[xhtml_show_wrong_error_log_html]'), false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[xhtml_show_wrong_error_log]\');', !get_value('xhtml_show_wrong')) .
				we_html_tools::hidden('newconf[xhtml_show_wrong_error_log]', get_value("xhtml_show_wrong_error_log"));

			$_settings = array(
				array('html' => g_l('prefs', '[xhtml_default]'), 'space' => 0, 'noline' => 1),
				array('html' => $_xhtml_setting->getHtml(), "space" => 200),
				array('html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[xhtml_debug_explanation]'), 2, 450), 'space' => 0, 'noline' => 1),
				array('headline' => g_l('prefs', '[xhtml_debug_headline]'), 'html' => $_xhtml_debug, 'space' => 200, 'noline' => 1),
				array('html' => $_xhtml_remove_wrong, 'space' => 200),
				array('headline' => g_l('prefs', '[xhtml_show_wrong_headline]'), 'html' => '', 'space' => 400, 'noline' => 1),
				array('html' => $_xhtml_show_wrong, 'space' => 200, 'noline' => 1),
				array('html' => $_xhtml_show_wrong_text, 'space' => 220, 'noline' => 1),
				array('html' => $_xhtml_show_wrong_js, 'space' => 220, 'noline' => 1),
				array('html' => $_xhtml_show_wrong_error_log, 'space' => 220, 'noline' => 1),
			);

			return create_dialog("", g_l('prefs', '[tab][validation]'), $_settings, -1, "", "", null, $js);

		/*		 * *******************************************************************
		 * BACKUP
		 * ******************************************************************* */
		case "backup":
			return;
		/* if(!we_hasPerm("ADMINISTRATOR")){
		  break;
		  }
		  $perf = new we_html_table(array("width" => "420", "border" => "0", "cellpadding" => "2", "cellspacing" => "0"), 3, 5);
		  $perf->setCol(0, 0, array("class" => "header_small"), g_l('prefs', '[backup_slow]'));
		  $perf->setCol(0, 1, array(), we_html_tools::getPixel(5, 2));
		  $perf->setCol(0, 2, array("class" => "header_small", "align" => "right"), g_l('prefs', '[backup_fast]'));



		  $steps = explode(',', weBackup::backupSteps);
		  $backup_steps = get_value("BACKUP_STEPS");
		  $steps_code = '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom:10px;"><tr>';
		  foreach($steps as $step){
		  $steps_code.=($step == $backup_steps ?
		  '<td>' . we_html_element::htmlInput(array("type" => "radio", "value" => "$step", "name" => "newconf[BACKUP_STEPS]", "checked" => true)) . '</td>' :
		  '<td>' . we_html_element::htmlInput(array("type" => "radio", "value" => "$step", "name" => "newconf[BACKUP_STEPS]")) . '</td>');
		  }
		  $steps_code.= '</tr></table>';
		  $perf->setCol(1, 0, array("class" => "defaultfont", "colspan" => 3), $steps_code);

		  $steps_code = ($backup_steps == 0 ?
		  we_html_element::htmlInput(array("type" => "radio", "value" => "0", "name" => "newconf[BACKUP_STEPS]", "checked" => true)) :
		  we_html_element::htmlInput(array("type" => "radio", "value" => "0", "name" => "newconf[BACKUP_STEPS]"))) .
		  g_l('prefs', '[backup_auto]');
		  $perf->setCol(2, 0, array("class" => "header_small", "colspan" => 3), $steps_code);

		  $tmp = we_forms::checkbox(1, get_value('FAST_BACKUP'), 'FAST_BACKUP', 'new fast Backup', false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[FAST_BACKUP]\');') .
		  we_html_tools::hidden('newconf[FAST_BACKUP]', get_value('FAST_BACKUP'));

		  $tmp2 = we_forms::checkbox(1, get_value('FAST_RESTORE'), 'setXhtml_show_wrong_js', 'new fast Restore', false, 'defaultfont', 'set_xhtml_field(this.checked,\'newconf[FAST_RESTORE]\');') .
		  we_html_tools::hidden('newconf[FAST_RESTORE]', get_value('FAST_RESTORE'));

		  $_settings = array(
		  array("headline" => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[performance]'), 2, 450), "html" => "", "space" => 200),
		  array("headline" => '', "html" => $perf->getHtml(), "space" => 15),
		  array("headline" => 'Fast Backup', 'html' => $tmp, 'space' => 50, 'noline' => 1),
		  array("headline" => 'Fast Restore (testing)', 'html' => $tmp2, 'space' => 50, 'noline' => 1),
		  );

		  $_settings_cookie = weGetCookieVariable("but_settings_predefined");

		  return create_dialog("settings_backup", g_l('prefs', '[tab][backup]'), $_settings);
		 */
		case 'security':
			if(!we_hasPerm('ADMINISTRATOR')){
				return;
			}
			$customer_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0', 'id' => 'customer_table'), 9, 10);
			$customer_table->setCol(0, 0, array('class' => 'defaultfont', 'width' => '20px'), '');
			$customer_table->setCol(0, 1, array('class' => 'defaultfont', 'colspan' => 5), g_l('prefs', '[security][customer][disableLogins]') . ':');
			$customer_table->setCol(0, 6, array('width' => 300));
			$customer_table->setCol(1, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][sameIP]'));
			$customer_table->setCol(1, 2, array('width' => '20px'));
			$customer_table->setCol(1, 3, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_IP]', 3, get_value('SECURITY_LIMIT_CUSTOMER_IP'), 3, '', 'number', 50));
			$customer_table->setCol(1, 4, array('class' => 'defaultfont', 'style' => 'width:2em;text-align:center'), '/');
			$customer_table->setCol(1, 5, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_IP_HOURS]', 3, get_value('SECURITY_LIMIT_CUSTOMER_IP_HOURS'), 3, '', 'number', 50));
			$customer_table->setCol(1, 6, array('class' => 'defaultfont'), 'h');

			$customer_table->setCol(2, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][sameUser]'));
			$customer_table->setCol(2, 3, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_NAME]', 3, get_value('SECURITY_LIMIT_CUSTOMER_NAME'), 3, '', 'number', 50));
			$customer_table->setCol(2, 4, array('class' => 'defaultfont', 'style' => 'text-align:center;'), '/');
			$customer_table->setCol(2, 5, array(), we_html_tools::htmlTextInput('newconf[SECURITY_LIMIT_CUSTOMER_NAME_HOURS]', 3, get_value('SECURITY_LIMIT_CUSTOMER_NAME_HOURS'), 3, '', 'number', 50));
			$customer_table->setCol(2, 6, array('class' => 'defaultfont'), 'h');

			$customer_table->setCol(4, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][errorPage]'));

			$wecmdenc1 = we_cmd_enc("document.forms[0].elements['newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]'].value");
			$wecmdenc2 = we_cmd_enc("document.forms[0].elements['SECURITY_LIMIT_CUSTOMER_REDIRECT_text'].value");

			$yuiSuggest->setAcId("SECURITY_LIMIT_CUSTOMER_REDIRECT_doc");
			$yuiSuggest->setContentType('folder,text/webEdition,text/html');
			$yuiSuggest->setInput('SECURITY_LIMIT_CUSTOMER_REDIRECT_text', (SECURITY_LIMIT_CUSTOMER_REDIRECT ? id_to_path(SECURITY_LIMIT_CUSTOMER_REDIRECT) : ''));
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult('newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]', ( SECURITY_LIMIT_CUSTOMER_REDIRECT ? SECURITY_LIMIT_CUSTOMER_REDIRECT : 0));
			$yuiSuggest->setSelector('Docselector');
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton(we_button::create_button('select', "javascript:we_cmd('openDocselector', document.forms[0].elements['newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "','','" . session_id() . "','', 'text/webEdition,text/html', 1)"), 10);
			$yuiSuggest->setTrashButton(we_button::create_button('image:btn_function_trash', 'javascript:document.forms[0].elements[\'newconf[SECURITY_LIMIT_CUSTOMER_REDIRECT]\'].value = 0;document.forms[0].elements[\'SECURITY_LIMIT_CUSTOMER_REDIRECT_text\'].value = \'\''), 4);

			$customer_table->setCol(4, 3, array('class' => 'defaultfont', 'colspan' => 5), $yuiSuggest->getHTML());



			$customer_table->setCol(5, 1, array('class' => 'defaultfont'), g_l('prefs', '[security][customer][slowDownLogin]'));
			$customer_table->setCol(5, 3, array(), we_html_tools::htmlTextInput('newconf[SECURITY_DELAY_FAILED_LOGIN]', 3, get_value('SECURITY_DELAY_FAILED_LOGIN'), 3, '', 'number', 50));
			$customer_table->setCol(5, 4, array(), 's');


			$settings = array(
				array('headline' => g_l('perms_customer', '[perm_group_title]'), 'html' => $customer_table->getHtml(), 'space' => 120, 'noline' => 1),
				//array('headline' => '', 'html' => '', 'space' => 120, 'noline' => 1),
			);
			return create_dialog('settings_security', g_l('prefs', '[tab][security]'), $settings);

		case 'email':
			/**
			 * Information
			 */
			$_settings = array(
				array('headline' => '', 'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[mailer_information]'), 2, 450, false), "space" => 0)
			);

			if(we_hasPerm('ADMINISTRATOR')){
				$_emailSelect = we_html_tools::htmlSelect('newconf[WE_MAILER]', array('php' => g_l('prefs', '[mailer_php]'), 'smtp' => g_l('prefs', '[mailer_smtp]')), 1, get_value('WE_MAILER'), false, "onchange=\"var el = document.getElementById('smtp_table').style; if(this.value=='smtp') el.display='block'; else el.display='none';\"", 'value', 300, 'defaultfont');

				$_smtp_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0', 'id' => 'smtp_table', 'width' => 300, 'style' => 'display: ' . ((get_value('WE_MAILER') == 'php') ? 'none' : 'block') . ';'), 9, 3);
				$_smtp_table->setCol(0, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_server]'));
				$_smtp_table->setCol(0, 1, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 5));
				$_smtp_table->setCol(0, 2, array('align' => 'right'), we_html_tools::htmlTextInput('newconf[SMTP_SERVER]', 24, get_value('SMTP_SERVER'), 180, '', 'text', 180));
				$_smtp_table->setCol(1, 0, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 10));
				$_smtp_table->setCol(2, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_port]'));
				$_smtp_table->setCol(2, 2, array('align' => 'right'), we_html_tools::htmlTextInput('newconf[SMTP_PORT]', 24, get_value('SMTP_PORT'), 180, '', 'text', 180));


				$_encryptSelect = we_html_tools::htmlSelect('newconf[SMTP_ENCRYPTION]', array('0' => g_l('prefs', '[smtp_encryption_none]'), 'ssl' => g_l('prefs', '[smtp_encryption_ssl]'), 'tls' => g_l('prefs', '[smtp_encryption_tls]')), 1, get_value('SMTP_ENCRYPTION'), false, "", 'value', 180, 'defaultfont');

				$_smtp_table->setCol(3, 0, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 10));
				$_smtp_table->setCol(4, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_encryption]'));
				$_smtp_table->setCol(4, 2, array('align' => 'left'), $_encryptSelect);


				$_auth_table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0', 'id' => 'auth_table', 'width' => 200, 'style' => 'display: ' . ((get_value('SMTP_AUTH') == 1) ? 'block' : 'none') . ';'), 4, 3);
				$_auth_table->setCol(0, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_username]'));
				$_auth_table->setCol(0, 1, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 10));
				$_auth_table->setCol(0, 2, array('align' => 'right'), we_html_tools::htmlTextInput('newconf[SMTP_USERNAME]', 14, get_value('SMTP_USERNAME'), 105, '', 'text', 105));
				$_auth_table->setCol(1, 0, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 10));
				$_auth_table->setCol(2, 0, array('class' => 'defaultfont'), g_l('prefs', '[smtp_password]'));
				$_auth_table->setCol(2, 2, array('align' => 'right'), we_html_tools::htmlTextInput('newconf[SMTP_PASSWORD]', 14, get_value('SMTP_PASSWORD'), 105, '', 'password', 105));
				$_auth_table->setCol(3, 0, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 10));
				$_smtp_table->setCol(5, 0, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 20));
				$_smtp_table->setCol(6, 0, array('class' => 'defaultfont', 'colspan' => 3), we_forms::checkbox(1, get_value('SMTP_AUTH'), 'newconf[SMTP_AUTH]', g_l('prefs', '[smtp_auth]'), false, 'defaultfont', "var el2 = document.getElementById('auth_table').style; if(this.checked) el2.display='block'; else el2.display='none';"));
				$_smtp_table->setCol(7, 0, array('class' => 'defaultfont'), we_html_tools::getPixel(10, 10));
				$_smtp_table->setCol(8, 0, array('align' => 'right', 'colspan' => 3), we_html_tools::getPixel(5, 5) . $_auth_table->getHtml());

				$_settings[] = array('headline' => g_l('prefs', '[mailer_type]'), 'html' => $_emailSelect, 'space' => 120, 'noline' => 1);
				$_settings[] = array('headline' => '', 'html' => $_smtp_table->getHtml(), 'space' => 120, 'noline' => 1);
			}

			return create_dialog('settings_email', g_l('prefs', '[email]'), $_settings);

		case 'versions':
			if(!we_hasPerm("ADMINISTRATOR")){
				break;
			}

			$versionsPrefs = array(
				'ctypes' => array(
					"image/*" => 'VERSIONING_IMAGE',
					"text/html" => 'VERSIONING_TEXT_HTML',
					"text/webedition" => 'VERSIONING_TEXT_WEBEDITION',
					"text/js" => 'VERSIONING_TEXT_JS',
					"text/css" => 'VERSIONING_TEXT_CSS',
					"text/plain" => 'VERSIONING_TEXT_PLAIN',
					"text/htaccess" => 'VERSIONING_TEXT_HTACCESS',
					"text/weTmpl" => 'VERSIONING_TEXT_WETMPL',
					"application/x-shockwave-flash" => 'VERSIONING_FLASH',
					"video/quicktime" => 'VERSIONING_QUICKTIME',
					"application/*" => 'VERSIONING_SONSTIGE',
					"text/xml" => 'VERSIONING_TEXT_XML',
					"objectFile" => 'VERSIONING_OBJECT',
				),
				'other' => array(
					"VERSIONS_TIME_DAYS" => 'VERSIONS_TIME_DAYS',
					"VERSIONS_TIME_WEEKS" => 'VERSIONS_TIME_WEEKS',
					"VERSIONS_TIME_YEARS" => 'VERSIONS_TIME_YEARS',
					"VERSIONS_ANZAHL" => 'VERSIONS_ANZAHL',
					"VERSIONS_CREATE" => 'VERSIONS_CREATE',
					"VERSIONS_CREATE_TMPL" => 'VERSIONS_CREATE_TMPL',
					"VERSIONS_TIME_DAYS_TMPL" => 'VERSIONS_TIME_DAYS_TMPL',
					"VERSIONS_TIME_WEEKS_TMPL" => 'VERSIONS_TIME_WEEKS_TMPL',
					"VERSIONS_TIME_YEARS_TMPL" => 'VERSIONS_TIME_YEARS_TMPL',
					"VERSIONS_ANZAHL_TMPL" => 'VERSIONS_ANZAHL_TMPL'
				)
			);

			//js
			$jsCheckboxCheckAll = '';

			foreach($versionsPrefs['ctypes'] as $v){
				$jsCheckboxCheckAll .= 'document.getElementById("newconf[' . $v . ']").checked = checked;';
			}

			$js = we_html_element::jsElement('
function checkAll(val) {
	checked=(val.checked)?1:0;
	' . $jsCheckboxCheckAll . ';
}

function checkAllRevert() {
	var checkbox = document.getElementById("version_all");
	checkbox.checked = false;
}

function openVersionWizard() {
	parent.opener.top.we_cmd("versions_wizard");

}');

			$_SESSION['weS']['versions']['logPrefs'] = array();
			foreach($versionsPrefs as $v){
				foreach($v as $val){
					$_SESSION['weS']['versions']['logPrefs'][$val] = get_value($val);
				}
			}

			$checkboxes = we_forms::checkbox(1, false, 'version_all', g_l('prefs', '[version_all]'), false, "defaultfont", 'checkAll(this);') . '<br/>';

			foreach($versionsPrefs['ctypes'] as $k => $v){
				$checkboxes .= we_forms::checkbox(1, get_value($v), 'newconf[' . $v . ']', g_l('contentTypes', '[' . $k . ']'), false, "defaultfont", 'checkAllRevert(this);') . '<br/>';
			}

			$_versions_time_days = new we_html_select(array(
				"name" => "newconf[VERSIONS_TIME_DAYS]",
				"style" => "",
				"class" => "weSelect"
				)
			);

			$_versions_time_days->addOption(-1, "");
			$_versions_time_days->addOption(secondsDay, g_l('prefs', '[1_day]'));
			for($x = 2; $x <= 31; $x++){
				$_versions_time_days->addOption(($x * secondsDay), sprintf(g_l('prefs', '[more_days]'), $x));
			}
			$_versions_time_days->selectOption(get_value("VERSIONS_TIME_DAYS"));


			$_versions_time_weeks = new we_html_select(array(
				"name" => "newconf[VERSIONS_TIME_WEEKS]",
				"style" => "",
				"class" => "weSelect")
			);
			$_versions_time_weeks->addOption(-1, "");
			$_versions_time_weeks->addOption(secondsWeek, g_l('prefs', '[1_week]'));
			for($x = 2; $x <= 52; $x++){
				$_versions_time_weeks->addOption(($x * secondsWeek), sprintf(g_l('prefs', '[more_weeks]'), $x));
			}
			$_versions_time_weeks->selectOption(get_value("VERSIONS_TIME_WEEKS"));


			$_versions_time_years = new we_html_select(array(
				"name" => "newconf[VERSIONS_TIME_YEARS]",
				"style" => "",
				"class" => "weSelect"
				)
			);
			$_versions_time_years->addOption(-1, "");
			$_versions_time_years->addOption(secondsYear, g_l('prefs', '[1_year]'));
			for($x = 2; $x <= 10; $x++){
				$_versions_time_years->addOption(($x * secondsYear), sprintf(g_l('prefs', '[more_years]'), $x));
			}
			$_versions_time_years->selectOption(get_value("VERSIONS_TIME_YEARS"));
			$_versions_anzahl = we_html_tools::htmlTextInput("newconf[VERSIONS_ANZAHL]", 24, get_value("VERSIONS_ANZAHL"), 5, "", "text", 50, 0, "");

			$_versions_create_publishing = we_forms::radiobutton("1", (get_value("VERSIONS_CREATE") == "1"), "newconf[VERSIONS_CREATE]", g_l('prefs', '[versions_create_publishing]'), true, "defaultfont", "", false, "");
			$_versions_create_always = we_forms::radiobutton("0", (get_value("VERSIONS_CREATE") == 0), "newconf[VERSIONS_CREATE]", g_l('prefs', '[versions_create_always]'), true, "defaultfont", "", false, "");

			$_versions_time_days_tmpl = new we_html_select(array(
				"name" => "newconf[VERSIONS_TIME_DAYS_TMPL]",
				"style" => "",
				"class" => "weSelect"
				)
			);

			$_versions_time_days_tmpl->addOption(-1, '');
			$_versions_time_days_tmpl->addOption(secondsDay, g_l('prefs', '[1_day]'));
			for($x = 2; $x <= 31; $x++){
				$_versions_time_days_tmpl->addOption(($x * secondsDay), sprintf(g_l('prefs', '[more_days]'), $x));
			}
			$_versions_time_days_tmpl->selectOption(get_value("VERSIONS_TIME_DAYS_TMPL"));


			$_versions_time_weeks_tmpl = new we_html_select(array(
				"name" => "newconf[VERSIONS_TIME_WEEKS_TMPL]",
				"style" => "",
				"class" => "weSelect")
			);
			$_versions_time_weeks_tmpl->addOption(-1, "");
			$_versions_time_weeks_tmpl->addOption(secondsWeek, g_l('prefs', '[1_week]'));
			for($x = 2; $x <= 52; $x++){
				$_versions_time_weeks_tmpl->addOption(($x * secondsWeek), sprintf(g_l('prefs', '[more_weeks]'), $x));
			}
			$_versions_time_weeks_tmpl->selectOption(get_value("VERSIONS_TIME_WEEKS_TMPL"));

			$_versions_time_years_tmpl = new we_html_select(array(
				"name" => "newconf[VERSIONS_TIME_YEARS_TMPL]",
				"style" => "",
				"class" => "weSelect"
				)
			);
			$_versions_time_years_tmpl->addOption(-1, "");
			$_versions_time_years_tmpl->addOption(secondsYear, g_l('prefs', '[1_year]'));
			for($x = 2; $x <= 10; $x++){
				$_versions_time_years_tmpl->addOption(($x * secondsYear), sprintf(g_l('prefs', '[more_years]'), $x));
			}
			$_versions_time_years_tmpl->selectOption(get_value("VERSIONS_TIME_YEARS_TMPL"));
			$_versions_anzahl_tmpl = we_html_tools::htmlTextInput("newconf[VERSIONS_ANZAHL_TMPL]", 24, get_value("VERSIONS_ANZAHL_TMPL"), 5, "", "text", 50, 0, "");
			$_versions_create_tmpl_publishing = we_forms::radiobutton("1", (get_value("VERSIONS_CREATE_TMPL") == 1), "newconf[VERSIONS_CREATE_TMPL]", g_l('prefs', '[versions_create_tmpl_publishing]'), true, "defaultfont", "", false, "");
			$_versions_create_tmpl_always = we_forms::radiobutton("0", (get_value("VERSIONS_CREATE_TMPL") == 0), "newconf[VERSIONS_CREATE_TMPL]", g_l('prefs', '[versions_create_tmpl_always]'), true, "defaultfont", "", false, "");
			$_versions_wizard = "<div style='float:left;'>" . we_button::create_button("openVersionWizard", "javascript:openVersionWizard()", true, 100, 22, "", "") . "</div>";


			$_settings = array(
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_activate_text]'), 2, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[ContentType]'),
					'space' => 170,
					'html' => $checkboxes
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_time_text]'), 2, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'html' => $_versions_time_days->getHtml() . " " . $_versions_time_weeks->getHtml() . " " . $_versions_time_years->getHtml(),
					"space" => 170,
					"headline" => g_l('prefs', '[versioning_time]')
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_anzahl_text]'), 2, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[versioning_anzahl]'),
					'html' => $_versions_anzahl,
					'space' => 170
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_create_text]'), 2, 470, false),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[versioning_create]'),
					'html' => $_versions_create_publishing . "<br/>" . $_versions_create_always,
					'space' => 170
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_templates_text]'), 2, 470, false),
					'noline' => 0,
					'space' => 0
				),
				array(
					'html' => $_versions_time_days_tmpl->getHtml() . " " . $_versions_time_weeks_tmpl->getHtml() . " " . $_versions_time_years_tmpl->getHtml(),
					"space" => 170,
					'noline' => 1,
					"headline" => g_l('prefs', '[versioning_time]')
				),
				array(
					'headline' => g_l('prefs', '[versioning_anzahl]'),
					'html' => $_versions_anzahl_tmpl,
					'noline' => 1,
					'space' => 170
				),
				array(
					'headline' => g_l('prefs', '[versioning_create]'),
					'html' => $_versions_create_tmpl_publishing . "<br/>" . $_versions_create_tmpl_always,
					'space' => 170
				),
				array(
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('prefs', '[versioning_wizard_text]'), 2, 470),
					'noline' => 1,
					'space' => 0
				),
				array(
					'headline' => g_l('prefs', '[versioning_wizard]'),
					'html' => $_versions_wizard,
					'space' => 170
				),
			);

			return create_dialog("", g_l('prefs', '[tab][validation]'), $_settings, -1, "", "", null, $js);
	}

	return 'No rights.';
}

/**
 * This functions renders the complete dialog.
 *
 * @return         string
 */
function render_dialog(){
	// Check configuration file for all needed variables => since included in startup, nothing should change
	we_base_preferences::check_global_config();
	$tabs = array_keys($GLOBALS['tabs']);
	$tabs[] = 'save';
	$ret = '';

	foreach($tabs as $tab){
		$ret .= we_html_element::htmlDiv(array("id" => 'setting_' . $tab, 'style' => ($GLOBALS['tabname'] == 'setting_' . $tab ? '' : 'display: none;')), build_dialog($tab));
	}
	return $ret;
	// Hide preload screen
	//we_html_element::jsElement("setTimeout(\"top.we_cmd('show_tabs');\", 50);");
}

we_html_tools::htmlTop();

$doSave = false;
$acError = false;
$acErrorMsg = "";
// Check if we need to save settings
if(isset($_REQUEST["save_settings"]) && $_REQUEST["save_settings"] == "true"){
	$acQuery = new weSelectorQuery();

	// check seemode start document | object
	switch($_REQUEST['newconf']['seem_start_type']){
		case "document":
			if(empty($_REQUEST['seem_start_document'])){
				$acError = true;
				$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
			} else{
				$acResponse = $acQuery->getItemById($_REQUEST['seem_start_document'], FILE_TABLE, array("IsFolder"));
				if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
					$acError = true;
					$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
				}
			}
			break;
		case "weapp":
			if(empty($_REQUEST['newconf']['seem_start_weapp'])){
				$acError = true;
				$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
			}
			break;
		case "object":
			if(empty($_REQUEST['seem_start_object'])){
				$acError = true;
				$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
			} else{
				$acResponse = $acQuery->getItemById($_REQUEST['seem_start_object'], OBJECT_FILES_TABLE, array("IsFolder"));
				if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
					$acError = true;
					$acErrorMsg = sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[seem_startdocument]'), g_l('prefs', '[tab][ui]')) . "\\n";
				}
			}
			break;
	}
	// check sidebar document
	if((isset($_REQUEST['newconf']['SIDEBAR_DISABLED']) && !$_REQUEST['newconf']['SIDEBAR_DISABLED'] && $_REQUEST['ui_sidebar_file_name']) != ""){
		$acResponse = $acQuery->getItemById($_REQUEST['newconf']['newconf[SIDEBAR_DEFAULT_DOCUMENT]'], FILE_TABLE, array("IsFolder"));
		if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
			$acError = true;
			$acErrorMsg .= sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[sidebar]') . " / " . g_l('prefs', '[sidebar_document]'), g_l('prefs', '[tab][ui]')) . "\\n";
		}
	}
	// check doc for error on none existing objects
	if(isset($_REQUEST['error_document_no_objectfile_text']) && $_REQUEST['error_document_no_objectfile_text'] != ""){
		$acResponse = $acQuery->getItemById($_REQUEST['newconf']['ERROR_DOCUMENT_NO_OBJECTFILE'], FILE_TABLE, array("IsFolder"));
		if(!$acResponse || $acResponse[0]['IsFolder'] == 1){
			$acError = true;
			$acErrorMsg .= sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[error_no_object_found]'), g_l('prefs', '[tab][error_handling]')) . "\\n";
		}
	}
	// check if versioning number is correct
	if(isset($_REQUEST['newconf']['VERSIONS_ANZAHL']) && $_REQUEST['newconf']['VERSIONS_ANZAHL'] != ''){
		if(!pos_number($_REQUEST['newconf']['VERSIONS_ANZAHL'])){
			$acError = true;
			$acErrorMsg .= sprintf(g_l('alert', '[field_in_tab_notvalid]'), g_l('prefs', '[versioning_anzahl]'), g_l('prefs', '[tab][versions]')) . "\\n";
		}
	}
	$doSave = true;
}

if($doSave && !$acError){
	save_all_values();

	print STYLESHEET .
		we_html_element::jsElement('
							function doClose() {

								var _multiEditorreload = false;
							   ' . $save_javascript .
			(!$email_saved ? we_message_reporting::getShowMessageCall(g_l('prefs', '[error_mail_not_saved]'), we_message_reporting::WE_MESSAGE_ERROR) : we_message_reporting::getShowMessageCall(g_l('prefs', '[saved]'), we_message_reporting::WE_MESSAGE_NOTICE)) . '
							   //top.opener.top.frames[0].location.reload();
							   top.close();
							}
					   ') .
		'</head>' .
		we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "doClose()"), build_dialog("saved")) . "</html>";
} else{
	$_form = we_html_element::htmlForm(array("onSubmit" => "return false;", "name" => "we_form", "method" => "post", "action" => $_SERVER["SCRIPT_NAME"]), we_html_element::htmlHidden(array("name" => "save_settings", "value" => "false")) . render_dialog());

	$_we_cmd_js = we_html_element::jsElement('function we_cmd(){

	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){
		case "browse_server":
            new jsWindow(url,"browse_server",-1,-1,840,400,true,false,true);
			break;
		case "openDocselector":
			new jsWindow(url,"openDocselector",-1,-1,' . WINDOW_DOCSELECTOR_WIDTH . ',' . WINDOW_DOCSELECTOR_HEIGHT . ',true,false,true,true);
			break;
		case "show_formmail_log":
			url = "' . WE_INCLUDES_DIR . 'we_editors/weFormmailLog.php"
			new jsWindow(url,"openDocselector",-1,-1,840,400,true,false,true);
			break;
		case "show_formmail_block_log":
			url = "' . WE_INCLUDES_DIR . 'we_editors/weFormmailBlockLog.php"
			new jsWindow(url,"openDocselector",-1,-1,840,400,true,false,true);
			break;
		case "openColorChooser":
			new jsWindow(url,"we_colorChooser",-1,-1,430,370,true,true,true);
			break;

		default:
			for(var i = 0; i < arguments.length; i++){
				args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
			}
			eval(\'parent.we_cmd(\'+args+\')\');
	}
}

function setColorField(name) {
	document.getElementById("color_" + name).style.backgroundColor=document.we_form.elements[name].value;
}' . ($acError ? we_message_reporting::getShowMessageCall(g_l('alert', '[field_in_tab_notvalid_pre]') . "\\n\\n" . $acErrorMsg . "\\n" . g_l('alert', '[field_in_tab_notvalid_post]'), we_message_reporting::WE_MESSAGE_ERROR) : ""));


	print STYLESHEET .
		$_we_cmd_js . we_html_element::jsScript(JS_DIR . 'windows.js') . $yuiSuggest->getYuiCssFiles() . $yuiSuggest->getYuiJsFiles() . '</head>' .
		we_html_element::htmlBody(array("class" => "weDialogBody"), $_form) .
		$yuiSuggest->getYuiCss() .
		$yuiSuggest->getYuiJs() .
		'</html>';
}

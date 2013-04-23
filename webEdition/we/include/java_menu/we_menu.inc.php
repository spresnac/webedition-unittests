<?php

/**
 * webEdition CMS
 *
 * $Rev: 5971 $
 * $Author: mokraemer $
 * $Date: 2013-03-18 19:55:13 +0100 (Mon, 18 Mar 2013) $
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
 * @package    webEdition_javamenu
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$seeMode = !(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == 'normal');
$we_menu = array(
	'1000000' => array(
// File
		'text' => g_l('javaMenu_global', '[file]'),
		'parent' => '0000000',
		'enabled' => 1,
	),
// File > New
	'1010000' => array(
		'text' => g_l('javaMenu_global', '[new]'),
		'parent' => '1000000',
		'enabled' => 1,
	),
	'1010100' => array(
// File > New > webEdition Document
		'text' => g_l('javaMenu_global', '[webEdition_page]'),
		'parent' => '1010000',
		'perm' => 'NEW_WEBEDITIONSITE || ADMINISTRATOR',
		'enabled' => 1,
	)
);
// File > New > webEdition Document > empty page
if(we_hasPerm('NO_DOCTYPE')){
	$we_menu['1010101'] = array(
		'text' => g_l('javaMenu_global', '[empty_page]'),
		'parent' => '1010100',
		'cmd' => 'new_webEditionPage',
		'perm' => 'NEW_WEBEDITIONSITE || ADMINISTRATOR',
	);
}

$GLOBALS['DB_WE']->query('SELECT ID,DocType FROM ' . DOC_TYPES_TABLE . ' ' . getDoctypeQuery($GLOBALS['DB_WE']));
if($GLOBALS['DB_WE']->num_rows() && we_hasPerm('NO_DOCTYPE')){
	$we_menu['1010102'] = array('parent' => '1010100'); // separator
}
// File > New > webEdition Document > Doctypes*
$nr = 103;
while($GLOBALS['DB_WE']->next_record()) {
	$we_menu['1010' . $nr++] = array(
		'text' => str_replace(array(',', '"', '\'',), array(' ', ''), $GLOBALS['DB_WE']->f('DocType')),
		'parent' => '1010100',
		'cmd' => 'new_dtPage' . $GLOBALS['DB_WE']->f('ID'),
		'perm' => 'NEW_WEBEDITIONSITE || ADMINISTRATOR',
		'enabled' => 1,
	);
	if($nr == 197){
		break;
	}
}

if($seeMode && we_hasPerm('NO_DOCTYPE')){
	$we_menu['1010198'] = array('parent' => '1010100'); // separator
	// File > New > Others (Import)
	$we_menu['1010199'] = array(
		'text' => g_l('javaMenu_global', '[other]'),
		'parent' => '1010100',
		'cmd' => 'openFirstStepsWizardDetailTemplates',
		'perm' => 'ADMINISTRATOR',
		'enabled' => 1,
	);
}

// File > Image
$we_menu['1010200'] = array(
	'text' => g_l('javaMenu_global', '[image]'),
	'parent' => '1010000',
	'cmd' => 'new_image',
	'perm' => 'NEW_GRAFIK || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other
$we_menu['1010300'] = array(
	'text' => g_l('javaMenu_global', '[other]'),
	'parent' => '1010000',
	'enabled' => 1,
);

// File > New > Other > html
$we_menu['1010301'] = array(
	'text' => g_l('javaMenu_global', '[html_page]'),
	'parent' => '1010300',
	'cmd' => 'new_html_page',
	'perm' => 'NEW_HTML || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other > Flash
$we_menu['1010302'] = array(
	'text' => g_l('javaMenu_global', '[flash_movie]'),
	'parent' => '1010300',
	'cmd' => 'new_flash_movie',
	'perm' => 'NEW_FLASH || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New Other > quicktime
$we_menu['1010303'] = array(
	'text' => g_l('javaMenu_global', '[quicktime_movie]'),
	'parent' => '1010300',
	'cmd' => 'new_quicktime_movie',
	'perm' => 'NEW_QUICKTIME || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other > Javascript
$we_menu['1010304'] = array(
	'text' => g_l('javaMenu_global', '[javascript]'),
	'parent' => '1010300',
	'cmd' => 'new_javascript',
	'perm' => 'NEW_JS || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other > CSS
$we_menu['1010305'] = array(
	'text' => g_l('javaMenu_global', '[css_stylesheet]'),
	'parent' => '1010300',
	'cmd' => 'new_css_stylesheet',
	'perm' => 'NEW_CSS || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other > Text
$we_menu['1010306'] = array(
	'text' => g_l('javaMenu_global', '[text_plain]'),
	'parent' => '1010300',
	'cmd' => 'new_text_plain',
	'perm' => 'NEW_TEXT || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other > XML
$we_menu['1010307'] = array(
	'text' => g_l('javaMenu_global', '[text_xml]'),
	'parent' => '1010300',
	'cmd' => 'new_text_xml',
	'perm' => 'NEW_TEXT || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other > htaccess
$we_menu['1010308'] = array(
	'text' => g_l('javaMenu_global', '[htaccess]'),
	'parent' => '1010300',
	'cmd' => 'new_text_htaccess',
	'perm' => 'NEW_HTACCESS || ADMINISTRATOR',
	'enabled' => 1,
);

// File > New > Other > Other (Binary)
$we_menu['1010309'] = array(
	'text' => g_l('javaMenu_global', '[other_files]'),
	'parent' => '1010300',
	'cmd' => 'new_binary_document',
	'perm' => 'NEW_SONSTIGE || ADMINISTRATOR',
	'enabled' => 1,
);

if(!$seeMode){
	$we_menu['1010400'] = array(
		'parent' => '1010000',
	); // separator
// File > New > Template
	$we_menu['1010500'] = array(
		'text' => g_l('javaMenu_global', '[template]'),
		'parent' => '1010000',
		'cmd' => 'new_template',
		'perm' => 'NEW_TEMPLATE || ADMINISTRATOR',
		'enabled' => 1,
	);

	$we_menu['1010600'] = array(
		'parent' => '1010000',
		'perm' => 'NEW_TEMPLATE || ADMINISTRATOR',
	); // separator
// File > New > Directory
	$we_menu['1011000'] = array(
		'text' => g_l('javaMenu_global', '[directory]'),
		'parent' => '1010000',
		'enabled' => 1,
	);

// File > New > Directory > Document
	$we_menu['1011001'] = array(
		'text' => g_l('javaMenu_global', '[document_directory]'),
		'parent' => '1011000',
		'cmd' => 'new_document_folder',
		'perm' => 'NEW_DOC_FOLDER || ADMINISTRATOR',
		'enabled' => 1,
	);

// File > New > Directory > Template
	$we_menu['1011002'] = array(
		'text' => g_l('javaMenu_global', '[template_directory]'),
		'parent' => '1011000',
		'cmd' => 'new_template_folder',
		'perm' => 'NEW_TEMP_FOLDER || ADMINISTRATOR',
		'enabled' => 1,
	);

// File > New > Directory > Object

	/* 	$we_menu['1011100']['parent'] = '1010000'; // separator
	  // File > New > Wizards
	  'text'=> g_l('javaMenu_global', '[wizards]') . '&hellip;',
	  'parent'=> '1010000',
	  'enabled'=> 1,

	  // File > New > Wizard > First Steps Wizard
	  'text'=> g_l('javaMenu_global', '[first_steps_wizard]'),
	  'parent'=> '1011200',
	  'cmd'=> 'openFirstStepsWizardMasterTemplate',
	  'perm'=> 'ADMINISTRATOR',
	  'enabled'=> 1,

	  $we_menu['1020000']['parent'] = '1000000'; // separator
	 */
}
// File > Open
$we_menu['1030000'] = array(
	'text' => g_l('javaMenu_global', '[open]'),
	'parent' => '1000000',
	'enabled' => 1,
);

// File > Open > Document
$we_menu['1030100'] = array(
	'text' => g_l('javaMenu_global', '[open_document]') . '&hellip;',
	'parent' => '1030000',
	'cmd' => 'open_document',
	'perm' => 'CAN_SEE_DOCUMENTS || ADMINISTRATOR',
	'enabled' => 1,
);

// File > Open > Template
if(!$seeMode){
	$we_menu['1030200'] = array(
		'text' => g_l('javaMenu_global', '[open_template]') . '&hellip;',
		'parent' => '1030000',
		'cmd' => 'open_template',
		'perm' => 'CAN_SEE_TEMPLATES || ADMINISTRATOR',
		'enabled' => 1,
	);
}

// File > Open > Object
// File > Open > Class
// File > Close
$we_menu['1040000'] = array(
	'text' => g_l('javaMenu_global', '[close_single_document]'),
	'parent' => '1000000',
	'cmd' => 'close_document',
	'perm' => '',
	'enabled' => 1,
);

if(!$seeMode){

// File > Close All
	$we_menu['1050000'] = array(
		'text' => g_l('javaMenu_global', '[close_all_documents]'),
		'parent' => '1000000',
		'cmd' => 'close_all_documents',
		'perm' => '',
		'enabled' => 1,
	);

// File > Close All But this
	$we_menu['1050100'] = array(
		'text' => g_l('javaMenu_global', '[close_all_but_active_document]'),
		'parent' => '1000000',
		'cmd' => 'close_all_but_active_document',
		'perm' => '',
		'enabled' => 1,
	);

// File > Delete Active Document
	$we_menu['1050200'] = array(
		'text' => g_l('javaMenu_global', '[delete_active_document]'),
		'parent' => '1000000',
		'cmd' => 'delete_single_document_question',
		'perm' => '',
		'enabled' => 1,
	);
}

$we_menu['1060000'] = array(
	'parent' => '1000000'
); // separator
// File > Save
$we_menu['1070000'] = array(
	'text' => g_l('javaMenu_global', '[save]'),
	'parent' => '1000000',
	'cmd' => 'trigger_save_document',
	'perm' => 'SAVE_DOCUMENT_TEMPLATE || ADMINISTRATOR',
	'enabled' => 1,
);

// File > Publish
$we_menu['1070001'] = array(
	'text' => g_l('javaMenu_global', '[publish]'),
	'parent' => '1000000',
	'cmd' => 'trigger_publish_document',
	'perm' => 'PUBLISH || ADMINISTRATOR',
	'enabled' => 1,
);

// File > Delete
$we_menu['1080000'] = array(
	'text' => g_l('javaMenu_global', '[delete]'),
	'parent' => '1000000',
	'enabled' => 1,
);

if($seeMode){
// File > Delete
	$we_menu['1080000'] = array(
		'text' => g_l('javaMenu_global', '[delete]') . '&hellip;',
		'parent' => '1000000',
		'cmd' => 'openDelSelector',
		'perm' => 'DELETE_DOCUMENT || ADMINISTRATOR',
		'enabled' => 1,
	);
} else{
// File > Delete > Documents
	$we_menu['1080100'] = array(
		'text' => g_l('javaMenu_global', '[documents]'),
		'parent' => '1080000',
		'cmd' => 'delete_documents',
		'perm' => 'DELETE_DOCUMENT || ADMINISTRATOR',
		'enabled' => 1,
	);

// File > Delete > Templates
	$we_menu['1080200'] = array(
		'text' => g_l('javaMenu_global', '[templates]'),
		'parent' => '1080000',
		'cmd' => 'delete_templates',
		'perm' => 'DELETE_TEMPLATE || ADMINISTRATOR',
		'enabled' => 1,
	);

// File > Delete > Classes
// File > Delete > Objects
	/* if (we_hasPerm('ADMINISTRATOR')) {
	  'text'=> g_l('javaMenu_global', '[cache]') . ' (' . g_l('javaMenu_global', '[documents]') . ')',
	  'parent'=> '1080000',
	  'cmd'=> 'delete_documents_cache',
	  'perm'=> 'ADMINISTRATOR',
	  'enabled'=> 1,
	  } */
// File > Move
	$we_menu['1090000'] = array(
		'text' => g_l('javaMenu_global', '[move]'),
		'parent' => '1000000',
		'enabled' => 1,
	);

// File > Move > Documents
	$we_menu['1090100'] = array(
		'text' => g_l('javaMenu_global', '[documents]'),
		'parent' => '1090000',
		'cmd' => 'move_documents',
		'perm' => 'MOVE_DOCUMENT || ADMINISTRATOR',
		'enabled' => 1,
	);

// File > Move > Templates
	$we_menu['1090200'] = array(
		'text' => g_l('javaMenu_global', '[templates]'),
		'parent' => '1090000',
		'cmd' => 'move_templates',
		'perm' => 'MOVE_TEMPLATE || ADMINISTRATOR',
		'enabled' => 1,
	);

// File > Move > Objects
}
$we_menu['1100000'] = array(
	'parent' => '1000000'
);
// separator
// File > unpublished pages
$we_menu['1110000'] = array(
	'text' => g_l('javaMenu_global', '[unpublished_pages]') . '&hellip;',
	'parent' => '1000000',
	'cmd' => 'openUnpublishedPages',
	'perm' => 'CAN_SEE_DOCUMENTS || ADMINISTRATOR',
	'enabled' => 1,
);

// File > unpublished objects, comes here !
$we_menu['1120000'] = array(
	'parent' => '1000000',
); // separator
// File > Search
$we_menu['1130000'] = array(
	'text' => g_l('javaMenu_global', '[search]') . '&hellip;',
	'parent' => '1000000',
	'cmd' => 'tool_weSearch_edit',
	'perm' => '',
	'enabled' => 1,
);

$we_menu['1140000'] = array(
	'parent' => '1000000',
); // separator
// File > Import/Export
$we_menu['1150000'] = array(
	'text' => g_l('javaMenu_global', '[import_export]'),
	'parent' => '1000000',
	'enabled' => 1,
);

// File > Import/Export > Import
$we_menu['1150100'] = array(
	'text' => g_l('javaMenu_global', '[import]') . '&hellip;',
	'cmd' => 'import',
	'parent' => '1150000',
	'perm' => (we_hasPerm('FILE_IMPORT') || we_hasPerm('SITE_IMPORT') || we_hasPerm('GENERICXML_IMPORT') || we_hasPerm('CSV_IMPORT') || we_hasPerm('WXML_IMPORT') ?
		'NEW_GRAFIK || NEW_WEBEDITIONSITE || NEW_HTML || NEW_FLASH || NEW_QUICKTIME || NEW_JS || NEW_CSS || NEW_TEXT || NEW_HTACCESS || NEW_SONSTIGE || ADMINISTRATOR' :
		'ADMINISTRATOR'),
	'enabled' => 1,
);

// File > Import/Export > Export
$we_menu['1150200'] = array(
	'text' => g_l('javaMenu_global', '[export]') . '&hellip;',
	'cmd' => 'export',
	'parent' => '1150000',
	'perm' => 'GENERICXML_EXPORT || CSV_EXPORT || ADMINISTRATOR',
	'enabled' => 1,
);

if(!$seeMode){
// File > Backup
	$we_menu['1160000'] = array(
		'text' => g_l('javaMenu_global', '[backup]'),
		'parent' => '1000000',
		'perm' => 'BACKUPLOG ||IMPORT ||EXPORT || EXPORTNODOWNLOAD || ADMINISTRATOR',
		'enabled' => 1,
	);
}

// File > Backup > make
$we_menu['1160100'] = array(
	'text' => g_l('javaMenu_global', '[make_backup]') . '&hellip;',
	'parent' => $seeMode ? '1000000' : '1160000',
	'cmd' => 'make_backup',
	'perm' => 'EXPORT || EXPORTNODOWNLOAD || ADMINISTRATOR',
	'enabled' => 1,
);

if(!$seeMode){
// File > Backup > recover
	$we_menu['1160200'] = array(
		'text' => g_l('javaMenu_global', '[recover_backup]') . '&hellip;',
		'parent' => '1160000',
		'cmd' => 'recover_backup',
		'perm' => 'IMPORT || ADMINISTRATOR',
		'enabled' => 1,
	);
}
// File > Backup > view Log
$we_menu['1160300'] = array(
	'text' => g_l('javaMenu_global', '[view_backuplog]') . '&hellip;',
	'parent' => $seeMode ? '1000000' : '1160000',
	'cmd' => 'view_backuplog',
	'perm' => 'BACKUPLOG || ADMINISTRATOR',
	'enabled' => 1,
);

// File > rebuild
$we_menu['1180000'] = array(
	'text' => g_l('javaMenu_global', '[rebuild]') . '&hellip;',
	'parent' => '1000000',
	'cmd' => 'rebuild',
	'perm' => 'REBUILD || ADMINISTRATOR',
	'enabled' => 1,
);

$we_menu['1200000'] = array(
	'parent' => '1000000',
); // separator

if(!$seeMode){
// File > Browse server
	$we_menu['1210000'] = array(
		'text' => g_l('javaMenu_global', '[browse_server]') . '&hellip;',
		'parent' => '1000000',
		'cmd' => 'browse_server',
		'perm' => 'BROWSE_SERVER || ADMINISTRATOR',
		'enabled' => 1,
	);

	$we_menu['1220000'] = array(
		'parent' => '1000000',
		'perm' => 'BROWSE_SERVER || ADMINISTRATOR',
	); // separator
}
// File > Quit
$we_menu['1230000'] = array(
	'text' => g_l('javaMenu_global', '[quit]'),
	'parent' => '1000000',
	'cmd' => 'dologout',
	'enabled' => 1,
);

// Cockpit
$we_menu['2000000'] = array(
	'text' => g_l('global', '[cockpit]'),
	'parent' => '0000000',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > Display
$we_menu['2010000'] = array(
	'text' => g_l('javaMenu_global', '[display]'),
	'parent' => '2000000',
	'cmd' => 'home',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget
$we_menu['2020000'] = array(
	'text' => g_l('javaMenu_global', '[new_widget]'),
	'parent' => '2000000',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > shortcuts
$we_menu['2020100'] = array(
	'text' => g_l('javaMenu_global', '[shortcuts]'),
	'parent' => '2020000',
	'cmd' => 'new_widget_sct',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > RSS
$we_menu['2020200'] = array(
	'text' => g_l('javaMenu_global', '[rss_reader]'),
	'parent' => '2020000',
	'cmd' => 'new_widget_rss',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > messaging
if(defined('MESSAGING_SYSTEM')){
	$we_menu['2020300'] = array(
		'text' => g_l('javaMenu_global', '[todo_messaging]'),
		'parent' => '2020000',
		'cmd' => 'new_widget_msg',
		'perm' => '',
		'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
	);
}

// Cockpit > new Widget > online users
$we_menu['2020400'] = array(
	'text' => g_l('javaMenu_global', '[users_online]'),
	'parent' => '2020000',
	'cmd' => 'new_widget_usr',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > lastmodified
$we_menu['2020500'] = array(
	'text' => g_l('javaMenu_global', '[last_modified]'),
	'parent' => '2020000',
	'cmd' => 'new_widget_mfd',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > unpublished
$we_menu['2020600'] = array(
	'text' => g_l('javaMenu_global', '[unpublished]'),
	'parent' => '2020000',
	'cmd' => 'new_widget_upb',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > my Documents
$we_menu['2020700'] = array(
	'text' => g_l('javaMenu_global', '[my_documents]'),
	'parent' => '2020000',
	'cmd' => 'new_widget_mdc',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > Notepad
$we_menu['2020800'] = array(
	'text' => g_l('javaMenu_global', '[notepad]'),
	'parent' => '2020000',
	'cmd' => 'new_widget_pad',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Cockpit > new Widget > pageLogger
if(WE_TRACKER_DIR &&
	file_exists($_SERVER['DOCUMENT_ROOT'] . WE_TRACKER_DIR . '/includes/showme.inc.php')){
	$we_menu['2020900'] = array(
		'text' => g_l('javaMenu_global', '[pagelogger]'),
		'parent' => '2020000',
		'cmd' => 'new_widget_plg',
		'perm' => '',
		'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
	);
}

// Cockpit > new Widget > default settings
$we_menu['2030000'] = array(
	'text' => g_l('javaMenu_global', '[default_settings]'),
	'parent' => '2000000',
	'cmd' => 'reset_home',
	'perm' => '',
	'enabled' => we_hasPerm('CAN_SEE_QUICKSTART'),
);

// Modules
$we_menu['3000000'] = array(
	'text' => g_l('javaMenu_global', '[modules]'),
	'parent' => '0000000',
);

$z = 100;

// order all modules
$buyableModules = weModuleInfo::getAllModules();
weModuleInfo::orderModuleArray($buyableModules);

$moduleList = 'schedpro|';

if(!empty($GLOBALS['_we_active_integrated_modules'])){

	foreach($buyableModules as $m){

		if(weModuleInfo::showModuleInMenu($m['name'])){
			// workarround (old module names) for not installed Modules WIndow
			if($m['name'] == 'customer'){
				$moduleList .= 'customerpro|';
			}
			$moduleList .= $m['name'] . '|';
			$we_menu['3000' . $z++] = array(
				'text' => $m['text'] . '&hellip;',
				'parent' => '3000000',
				'cmd' => 'edit_' . $m['name'] . '_ifthere',
				'perm' => isset($m['perm']) ? $m['perm'] : '',
				'enabled' => 1,
			);
		} else if(in_array($m['name'], $GLOBALS['_we_active_integrated_modules'])){
			$moduleList .= $m['name'] . '|';
		}
	}
}

foreach($GLOBALS['_we_available_modules'] as $key => $val){
	//if ($val['integrated']) {
	$moduleList .= $key . '|';
	//}
}
//$_SESSION['we_module_list'] = rtrim($moduleList, '|');
// Modules > pagelogger
if(WE_TRACKER_DIR){
	$we_menu['3020000'] = array(
		'text' => 'pageLogger',
		'parent' => '3000000',
		'cmd' => 'we_tracker',
		'perm' => '',
		'enabled' => 1,
	);
}

// Extras
$we_menu['4000000'] = array(
	'text' => g_l('javaMenu_global', '[extras]'),
	'parent' => '0000000',
	'enabled' => 1,
);

// Extras > Navigation
$we_menu['4031000'] = array(
	'text' => g_l('javaMenu_global', '[navigation]') . '&hellip;',
	'parent' => '4000000',
	'cmd' => 'tool_navigation_edit',
	'perm' => 'EDIT_NAVIGATION || ADMINISTRATOR',
	'enabled' => 1,
);

// Extras > Dokument-Typen
$we_menu['4032000'] = array(
	'text' => g_l('javaMenu_global', '[document_types]') . '&hellip;',
	'parent' => '4000000',
	'cmd' => 'doctypes',
	'perm' => 'EDIT_DOCTYPE || ADMINISTRATOR',
	'enabled' => 1,
);

// Extras > Kategorien
$we_menu['4033000'] = array(
	'text' => g_l('javaMenu_global', '[categories]') . '&hellip;',
	'parent' => '4000000',
	'cmd' => 'editCat',
	'perm' => 'EDIT_KATEGORIE || ADMINISTRATOR',
	'enabled' => 1,
);

$we_menu['4033300'] = array(
	'parent' => '4000000'
); // separator
// Extras > Tools > Custom tools
$_tools = weToolLookup::getAllTools(true, false);

foreach($_tools as $_k => $_tool){
	$we_menu[($_tool['name'] == 'toolfactory' ? '404' : '405') . sprintf('%04d', $_k)] = array(
		'text' => $_tool['text'] . '&hellip;',
		'parent' => '4000000',
		'cmd' => 'tool_' . $_tool['name'] . '_edit',
		'perm' => $_tool['startpermission'] . ' || ADMINISTRATOR',
		'enabled' => 1,
	);
}


$we_menu['4125000'] = array(
	'parent' => '4000000',
); // separator
// Extras > Thumbnails
$we_menu['4130000'] = array(
	'text' => g_l('javaMenu_global', '[thumbnails]') . '&hellip;',
	'parent' => '4000000',
	'cmd' => 'editThumbs',
	'perm' => 'EDIT_THUMBS || ADMINISTRATOR',
	'enabled' => 1,
);
if(!$seeMode){

// Extras > Metadata fields
	$we_menu['4140000'] = array(
		'text' => g_l('javaMenu_global', '[metadata]') . '&hellip;',
		'parent' => '4000000',
		'cmd' => 'editMetadataFields',
		'perm' => 'ADMINISTRATOR',
		'enabled' => 1,
	);
}

// Extras > change password
$we_menu['4160000'] = array(
	'text' => g_l('javaMenu_global', '[change_password]') . '&hellip;',
	'parent' => '4000000',
	'cmd' => 'change_passwd',
	'perm' => 'EDIT_PASSWD || ADMINISTRATOR',
	'enabled' => 1,
);

if(we_hasPerm('ADMINISTRATOR')){
	// Extras > versioning
	$we_menu['4161000'] = array(
		'text' => g_l('javaMenu_global', '[versioning]') . '&hellip;',
		'parent' => '4000000',
		'cmd' => 'versions_wizard',
		'perm' => 'ADMINISTRATOR',
		'enabled' => 1,
	);

	// Extras > versioning-log
	$we_menu['4162000'] = array(
		'text' => g_l('javaMenu_global', '[versioning_log]') . '&hellip;',
		'parent' => '4000000',
		'cmd' => 'versioning_log',
		'perm' => 'ADMINISTRATOR',
		'enabled' => 1,
	);
}

$we_menu['4170000'] = array(
	'parent' => '4000000',
); // separator
// Extras > Einstellungen

$we_menu['4180000'] = array(
	'text' => g_l('javaMenu_global', '[preferences]'),
	'parent' => '4000000',
	'enabled' => 1,
);

$we_menu['4181000'] = array(
	'text' => g_l('javaMenu_global', '[common]') . '&hellip;',
	'parent' => '4180000',
	'cmd' => 'openPreferences',
	'perm' => 'EDIT_SETTINGS || ADMINISTRATOR',
	'enabled' => 1,
);

$we_menu['4183000'] = array(
	'parent' => '4180000',
); // separator

$_activeIntModules = weModuleInfo::getIntegratedModules(true);
weModuleInfo::orderModuleArray($_activeIntModules);

if(!empty($_activeIntModules)){

	$z = 100;

	foreach($_activeIntModules as $key => $modInfo){
		if($modInfo['hasSettings']){
			$we_menu['4184' . $z++] = array(
				'text' => $modInfo['text'] . '&hellip;',
				'parent' => '4180000',
				'cmd' => 'edit_settings_' . $modInfo['name'],
				'perm' => isset($modInfo['perm']) ? $modInfo['perm'] : '',
				'enabled' => 1,
			);
		}
	}
}

// Help
$we_menu['5000000'] = array(
	'text' => g_l('javaMenu_global', '[help]'),
	'parent' => '0000000',
	'enabled' => 1,
);

if(!$seeMode){
	$we_menu['5010000'] = array(
		'text' => g_l('javaMenu_global', '[onlinehelp]'),
		'parent' => '5000000',
		'enabled' => 1,
	);
}

$we_menu['5010001'] = array(
	'text' => g_l('javaMenu_global', '[onlinehelp]') . '&hellip;',
	'parent' => $seeMode ? '5000000' : '5010000',
	'cmd' => 'help',
	'perm' => '',
	'enabled' => 1,
);
if(!$seeMode){
	$we_menu['5010002'] = array(
		'parent' => '5010000'
	); // separator

	$we_menu['5010003'] = array(
		'text' => g_l('javaMenu_global', '[onlinehelp_documentation]') . '&hellip;',
		'parent' => '5010000',
		'cmd' => 'help_documentation',
		'perm' => '',
		'enabled' => 1,
	);

	$we_menu['5010004'] = array(
		'text' => g_l('javaMenu_global', '[onlinehelp_tagreference]') . '&hellip;',
		'parent' => '5010000',
		'cmd' => 'help_tagreference',
		'perm' => '',
		'enabled' => 1,
	);

	$we_menu['5010005'] = array(
		'text' => g_l('javaMenu_global', '[onlinehelp_forum]') . '&hellip;',
		'parent' => '5010000',
		'cmd' => 'help_forum',
		'perm' => '',
		'enabled' => 1,
	);

	$we_menu['5010006'] = array(
		'text' => g_l('javaMenu_global', '[onlinehelp_bugtracker]') . '&hellip;',
		'parent' => '5010000',
		'cmd' => 'help_bugtracker',
		'perm' => '',
		'enabled' => 1,
	);

	$we_menu['5010008'] = array(
		'parent' => '5010000'
	); // separator

	$we_menu['5010009'] = array(
		'text' => g_l('javaMenu_global', '[onlinehelp_changelog]') . '&hellip;',
		'parent' => '5010000',
		'cmd' => 'help_changelog',
		'perm' => '',
		'enabled' => 1,
	);
}
if(SIDEBAR_DISABLED == 0){
	$we_menu['5015000'] = array(
		'text' => g_l('javaMenu_global', '[sidebar]') . '&hellip;',
		'parent' => '5000000',
		'cmd' => 'openSidebar',
		'perm' => '',
		'enabled' => 1,
	);
}

$we_menu['5020000'] = array(
	'text' => g_l('javaMenu_global', '[webEdition_online]') . '&hellip;',
	'parent' => '5000000',
	'cmd' => 'webEdition_online',
	'perm' => '',
	'enabled' => 1,
);

$we_menu['5040000'] = array(
	'parent' => '5000000',
	'perm' => 'ADMINISTRATOR',
); // separator

$we_menu['5050000'] = array(
	'text' => g_l('javaMenu_global', '[update]') . '&hellip;',
	'parent' => '5000000',
	'cmd' => 'update',
	'perm' => 'ADMINISTRATOR',
	'enabled' => 1,
);

$we_menu['5060000'] = array(
	'parent' => '5000000'
); // separator

$we_menu['5090000'] = array(
	'text' => g_l('javaMenu_global', '[sysinfo]') . '&hellip;',
	'parent' => '5000000',
	'cmd' => 'sysinfo',
	'perm' => 'ADMINISTRATOR',
	'enabled' => 1,
);

$we_menu['5095000'] = array(
	'text' => g_l('javaMenu_global', '[showerrorlog]') . '&hellip;',
	'parent' => '5000000',
	'cmd' => 'showerrorlog',
	'perm' => 'ADMINISTRATOR',
	'enabled' => 1,
);

$we_menu['5100000'] = array(
	'text' => g_l('javaMenu_global', '[info]') . '&hellip;',
	'parent' => '5000000',
	'cmd' => 'info',
	'perm' => '',
	'enabled' => 1,
);

reset($GLOBALS['_we_available_modules']);
while((list($key, $val) = each($GLOBALS['_we_available_modules']))) {

	if(!isset($val['integrated']) || ( in_array($val['name'], $GLOBALS['_we_active_integrated_modules']) )){

		if(file_exists(WE_INCLUDES_PATH . 'java_menu/modules/we_menu_' . $val['name'] . '.inc.php')){
			include_once(WE_INCLUDES_PATH . 'java_menu/modules/we_menu_' . $val['name'] . '.inc.php');
		}
	}
}
<?php

/**
 * webEdition CMS
 *
 * $Rev: 5338 $
 * $Author: mokraemer $
 * $Date: 2012-12-11 14:26:12 +0100 (Tue, 11 Dec 2012) $
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
function we_stripslashes(&$arr){
	foreach($arr as $n => $v){
		if(is_array($v)){
			we_stripslashes($arr[$n]);
		} else{
			$arr[$n] = stripslashes($v);
		}
	}
}

if((get_magic_quotes_gpc() == 1)){
	if(!empty($_REQUEST)){
		foreach($_REQUEST as $n => $v){
			if(is_array($v)){
				we_stripslashes($v);
				$_REQUEST[$n] = $v;
			} else{
				$_REQUEST[$n] = stripslashes($v);
			}
		}
	}
}

// bugfix for php bug id #37276
if(version_compare(phpversion(), '5.1.3', '=')){
	if(isset($_REQUEST['we_cmd'])){
		foreach($_REQUEST['we_cmd'] as $key => $value){
			if(is_array($value)){
				$value = array_shift($value);
			}
			$_REQUEST['we_cmd'][$key] = $value;
		}
	}
}


define('WEBEDITION_DIR', '/webEdition/');

define('WE_INCLUDES_DIR', WEBEDITION_DIR . 'we/include/');
define('TEMPLATES_DIR', WEBEDITION_DIR . 'we/templates');
define('TEMP_DIR', WEBEDITION_DIR . 'we/tmp/');
define('WE_MODULES_DIR', WE_INCLUDES_DIR . 'we_modules/');


define('WE_APPS_DIR', WEBEDITION_DIR . 'apps/');
define('SITE_DIR', WEBEDITION_DIR . 'site/');
define('IMAGE_DIR', WEBEDITION_DIR . 'images/');
define('HTML_DIR', WEBEDITION_DIR . 'html/');
define('JS_DIR', WEBEDITION_DIR . 'js/');
define('BACKUP_DIR', WEBEDITION_DIR . 'we_backup/');
define('VERSION_DIR', WEBEDITION_DIR . 'we/versions/');
define('LIB_DIR', WEBEDITION_DIR . 'lib/');
define('WE_THUMB_PREVIEW_DIR', WEBEDITION_DIR . 'preview/');
define('TINYMCE_JS_DIR', WEBEDITION_DIR . 'editors/content/tinymce/jscripts/tiny_mce/');

define('TREE_IMAGE_DIR', IMAGE_DIR . 'tree/');
define('ICON_DIR', TREE_IMAGE_DIR . 'icons/');
define('EDIT_IMAGE_DIR', IMAGE_DIR . 'edit/');
define('BUTTONS_DIR', IMAGE_DIR . 'button/');


//all paths
define('WEBEDITION_PATH', $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR);
define('TEMPLATES_PATH', $_SERVER['DOCUMENT_ROOT'] . TEMPLATES_DIR);
define('TEMP_PATH', $_SERVER['DOCUMENT_ROOT'] . TEMP_DIR);
define('WE_APPS_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_APPS_DIR);
define('WE_INCLUDES_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_INCLUDES_DIR);
define('JS_PATH', $_SERVER['DOCUMENT_ROOT'] . JS_DIR);
define('WE_MODULES_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_MODULES_DIR);
define('WE_THUMB_PREVIEW_PATH', $_SERVER['DOCUMENT_ROOT'] . WE_THUMB_PREVIEW_DIR);

//paths without "DIRS"
define('WE_FRAGMENT_PATH', WEBEDITION_PATH . 'fragments/');
define('ZENDCACHE_PATH', WEBEDITION_PATH . 'we/zendcache/');


// Activate the webEdition error handler
include_once (WE_INCLUDES_PATH . 'we_error_handler.inc.php');
if(!defined('WE_ERROR_HANDLER_SET')){
	we_error_handler();
}

include_once (WE_INCLUDES_PATH . 'we_version.php');


define('WE_EDITPAGE_PROPERTIES', 0);
define('WE_EDITPAGE_CONTENT', 1);
define('WE_EDITPAGE_INFO', 2);
define('WE_EDITPAGE_PREVIEW', 3);
define('WE_EDITPAGE_WORKSPACE', 4);
define('WE_EDITPAGE_METAINFO', 5);
define('WE_EDITPAGE_FIELDS', 6);
define('WE_EDITPAGE_SEARCH', 7);
define('WE_EDITPAGE_SCHEDULER', 8);
define('WE_EDITPAGE_THUMBNAILS', 9);
define('WE_EDITPAGE_VALIDATION', 10);
define('WE_EDITPAGE_VARIANTS', 11);
define('WE_EDITPAGE_PREVIEW_TEMPLATE', 12);
define('WE_EDITPAGE_CFWORKSPACE', 13);
define('WE_EDITPAGE_WEBUSER', 14);
define('WE_EDITPAGE_IMAGEEDIT', 15);
define('WE_EDITPAGE_DOCLIST', 16);
define('WE_EDITPAGE_VERSIONS', 17);

define('FILE_ONLY', 0);
define('FOLDER_ONLY', 1);

define('CATEGORY_TABLE', TBL_PREFIX . 'tblCategorys');
define('CLEAN_UP_TABLE', TBL_PREFIX . 'tblCleanUp');
define('CONTENT_TABLE', TBL_PREFIX . 'tblContent');
define('DOC_TYPES_TABLE', TBL_PREFIX . 'tblDocTypes');
define('ERROR_LOG_TABLE', TBL_PREFIX . 'tblErrorLog');
define('FAILED_LOGINS_TABLE', TBL_PREFIX . 'tblFailedLogins');
define('FILE_TABLE', TBL_PREFIX . 'tblFile');
define('INDEX_TABLE', TBL_PREFIX . 'tblIndex');
define('LINK_TABLE', TBL_PREFIX . 'tblLink');
define('LANGLINK_TABLE', TBL_PREFIX . 'tblLangLink');
define('PREFS_TABLE', TBL_PREFIX . 'tblPrefs');
define('RECIPIENTS_TABLE', TBL_PREFIX . 'tblRecipients');
define('TEMPLATES_TABLE', TBL_PREFIX . 'tblTemplates');
define('TEMPORARY_DOC_TABLE', TBL_PREFIX . 'tblTemporaryDoc');
define('UPDATE_LOG_TABLE', TBL_PREFIX . 'tblUpdateLog');
define('THUMBNAILS_TABLE', TBL_PREFIX . 'tblthumbnails');
define('VALIDATION_SERVICES_TABLE', TBL_PREFIX . 'tblvalidationservices');
define('HISTORY_TABLE', TBL_PREFIX . 'tblhistory');
define('FORMMAIL_LOG_TABLE', TBL_PREFIX . 'tblformmaillog');
define('FORMMAIL_BLOCK_TABLE', TBL_PREFIX . 'tblformmailblock');
define('METADATA_TABLE', TBL_PREFIX . 'tblMetadata');
define('NOTEPAD_TABLE', TBL_PREFIX . 'tblwidgetnotepad');
define('VERSIONS_TABLE', TBL_PREFIX . 'tblversions');
define('VERSIONS_TABLE_LOG', TBL_PREFIX . 'tblversionslog');

define('NAVIGATION_TABLE', TBL_PREFIX . 'tblnavigation');
define('NAVIGATION_RULE_TABLE', TBL_PREFIX . 'tblnavigationrules');

define('SESSION_NAME', 'WESESSION');

(!defined('LOGIN_FAILED_TIME')) && define('LOGIN_FAILED_TIME', 2); // in minutes

(!defined('LOGIN_FAILED_NR')) && define('LOGIN_FAILED_NR', 3);

(!defined('LOGIN_FAILED_HOLDTIME')) && define('LOGIN_FAILED_HOLDTIME', 30); // in days
//define how long Errors hold in DB
define('ERROR_LOG_HOLDTIME', 30); // in days
define('ERROR_LOG_MAX_ITEM_COUNT', 10000);
define('ERROR_LOG_MAX_ITEM_THRESH', 9800);



define('WE_WYSIWYG_COMMANDS', 'formatblock,fontname,fontsize,applystyle,bold,italic,underline,subscript,superscript,strikethrough,removeformat,removetags,forecolor,backcolor,justifyleft,justifycenter,justifyright,justifyfull,insertunorderedlist,insertorderedlist,indent,outdent,createlink,unlink,anchor,insertimage,inserthorizontalrule,insertspecialchar,inserttable,edittable,editcell,insertcolumnright,insertcolumnleft,insertrowabove,insertrowbelow,deletecol,deleterow,increasecolspan,decreasecolspan,caption,removecaption,importrtf,fullscreen,cut,copy,paste,undo,redo,visibleborders,editsource,prop,justify,list,link,color,copypaste,table,insertbreak,acronym,lang,spellcheck');

/**
 * Fix the none existing $_SERVER['REQUEST_URI'] on IIS
 */
if(!isset($_SERVER['REQUEST_URI'])){
	if(!isset($_SERVER['HTTP_REQUEST_URI'])){
		if(isset($_SERVER['SCRIPT_NAME'])){
			$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		} else{
			$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['PHP_SELF'];
		}

		if(isset($_SERVER['QUERY_STRING'])){
			$_SERVER['HTTP_REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
	}
}

define('WINDOW_SELECTOR_WIDTH', 900);
define('WINDOW_SELECTOR_HEIGHT', 685);
define('WINDOW_DIRSELECTOR_WIDTH', 900);
define('WINDOW_DIRSELECTOR_HEIGHT', 600);
define('WINDOW_DOCSELECTOR_WIDTH', 900);
define('WINDOW_DOCSELECTOR_HEIGHT', 685);
define('WINDOW_CATSELECTOR_WIDTH', 900);
define('WINDOW_CATSELECTOR_HEIGHT', 600);
define('WINDOW_DELSELECTOR_WIDTH', 900);
define('WINDOW_DELSELECTOR_HEIGHT', 600);

$GLOBALS['WE_LANGS'] = array(
	'de' => 'Deutsch',
	'en' => 'English',
	'nl' => 'Dutch',
	'fi' => 'Finnish',
	'ru' => 'Russian',
	'es' => 'Spanish',
	'pl' => 'Polish',
	'fr' => 'French'
);
$GLOBALS['WE_LANGS_COUNTRIES'] = array(
	'DE' => 'de',
	'GB' => 'en',
	'NL' => 'nl',
	'FI' => 'fi',
	'RU' => 'ru',
	'ES' => 'es',
	'PL' => 'pl',
	'FR' => 'fr'
);
if(!defined('DATETIME_INITIALIZED')){// to prevent additional initialization if set somewhere else, i.e in autoload, this also allows later to make that an settings-item
	if(!date_default_timezone_set(@date_default_timezone_get())){
		date_default_timezone_set('Europe/Berlin');
	}
	define('DATETIME_INITIALIZED', '1');
}

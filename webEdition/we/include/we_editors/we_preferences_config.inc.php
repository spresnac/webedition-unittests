<?php

/**
 * webEdition CMS
 *
 * $Rev: 5908 $
 * $Author: lukasimhof $
 * $Date: 2013-03-01 21:25:12 +0100 (Fri, 01 Mar 2013) $
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
//NOTE: there is no need to add any variables to we_conf_global.inc.php.default anymore.
$GLOBALS['tabs'] = array(
	'ui' => '',
	'extensions' => 'EDIT_SETTINGS_DEF_EXT',
	'editor' => '',
	'proxy' => 'ADMINISTRATOR',
	'defaultAttribs' => 'ADMINISTRATOR',
	'advanced' => 'ADMINISTRATOR',
	'system' => 'ADMINISTRATOR',
	'security' => 'ADMINISTRATOR',
	'seolinks' => 'ADMINISTRATOR',
	'modules' => 'ADMINISTRATOR',
	'language' => 'ADMINISTRATOR',
	'countries' => 'ADMINISTRATOR',
	'error_handling' => 'ADMINISTRATOR',
//	'backup' => 'ADMINISTRATOR',
	'validation' => 'ADMINISTRATOR',
	'email' => 'ADMINISTRATOR',
	'message_reporting' => '',
	'recipients' => 'FORMMAIL',
	'versions' => 'ADMINISTRATOR',
);

$GLOBALS['configs'] = array(
// Create array for needed configuration variables
	'global' => array(
//key => comment, default, changed right (default Admin)
// Variables for SEEM
		'WE_SEEM' => array('Enable seeMode', 1),
// Variables for LogIn
		'WE_LOGIN_HIDEWESTATUS' => array('Hide if webEdition is Nightly or Alpha or.. Release Version', 1),
		'WE_LOGIN_WEWINDOW' => array('Decide how WE opens: 0 allow both, 1 POPUP only, 2 same Window only', 0),
// Variables for thumbnails
		'WE_THUMBNAIL_DIRECTORY' => array('Directory in which to save thumbnails', '/__we_thumbs__'),
// Variables for error handling
		'WE_ERROR_HANDLER' => array('Show errors that occur in webEdition', true),
		'WE_ERROR_NOTICES' => array('Handle notices', false),
		'WE_ERROR_DEPRECATED' => array('Handle deprecated warnings', true),
		'WE_ERROR_WARNINGS' => array('Handle warnings', true),
		'WE_ERROR_ERRORS' => array('Handle errors', true),
		'WE_ERROR_SHOW' => array('Show errors', false),
		'WE_ERROR_LOG' => array('Log errors', true),
		'WE_ERROR_MAIL' => array('Mail errors', false),
		'WE_ERROR_MAIL_ADDRESS' => array('E-Mail address to which to mail errors', "mail@www.example"),
		'ERROR_DOCUMENT_NO_OBJECTFILE' => array('Document to open when trying to open non-existing object', 0),
		'DISABLE_TEMPLATE_CODE_CHECK' => array('Disable the check for php-errors in templates', 0),
		'DISABLE_TEMPLATE_PARSER' => array('Disable run of parser - only report errors', 0),
// Backup variable
		/* 		'BACKUP_STEPS' => array('Number of entries per batch', 100),
		  'FAST_BACKUP' => array('New Test for a faster Backup', 0),
		  'FAST_RESTORE' => array('New Test for a faster Restore', 0), */
// inlineedit default value
		'INLINEEDIT_DEFAULT' => array('Default setting for inlineedit attribute', true),
		'WE_PHP_DEFAULT' => array('Default setting for php attribute', false),
		'REMOVEFIRSTPARAGRAPH_DEFAULT' => array('Default setting for removeparagraph attribute', false),
		'HIDENAMEATTRIBINWEIMG_DEFAULT' => array('Default setting for hide name attribute in weimg output', false),
		'HIDENAMEATTRIBINWEFORM_DEFAULT' => array('Default setting for hide name attribute in weform output', false),
// we_css
		'CSSAPPLYTO_DEFAULT' => array('Default setting for we:css attribute applyto', 'around'),
// hooks
		'EXECUTE_HOOKS' => array('Default setting for hook execution', false),
// php local scope == global scope
		'PHPLOCALSCOPE' => array('Default setting for assuming php local scope == global scope ', false),
		'BASE_IMG' => array('url used prior all internal we:img tags', ''),
		'BASE_CSS' => array('url used prior all we:css tags', ''),
		'BASE_JS' => array('url used prio all we:js tags', ''),
// xhtml
		'XHTML_DEFAULT' => array('Default setting for xml attribute', false),
		'XHTML_DEBUG' => array('Enable XHTML debug', false),
		'XHTML_REMOVE_WRONG' => array('Remove wrong xhtml attributes from we:tags', false),
		'WE_MAX_UPLOAD_SIZE' => array('Maximal possible uploadsize', 0),
		'WE_DOCTYPE_WORKSPACE_BEHAVIOR' => array('Which Doctypes should be shown for which workspace 0=normal behaviour , 1=new behaviour', 0),
		'SCHEDULER_TRIGGER' => array('decide how the scheduler works', 1), //postdoc
// accessibility
		'SHOWINPUTS_DEFAULT' => array('Default setting for showinputs attribute', true),
		'WE_NEW_FOLDER_MOD' => array('File permissions when creating a new directory', "755"),
// pageLogger Dir
		'WE_TRACKER_DIR' => array('Directory in which pageLogger is installed', '/pageLogger'),
		'DB_SET_CHARSET' => array('connection charset to db', 'utf8'),
		'WYSIWYG_TYPE' => array('define used wysiwyg editor', 'default'),
		'WYSIWYG_TYPE_FRONTEND' => array('define used wysiwyg editor in frontend', 'default'),
		'WE_MAILER' => array('mailer type; possible values are php and smtp', 'php'),
		'SMTP_SERVER' => array('SMTP_SERVER', 'localhost'),
		'SMTP_PORT' => array('SMTP server port', 25),
		'SMTP_AUTH' => array('SMTP authentication', ''),
		'SMTP_USERNAME' => array('SMTP username', ''),
		'SMTP_PASSWORD' => array('SMTP password', ''),
		'SMTP_ENCRYPTION' => array('SMTP encryption', 0),
//formmail stuff
		'FORMMAIL_CONFIRM' => array('Flag if formmail confirm function should be work', true), //this is restricted to admin
		'FORMMAIL_VIAWEDOC' => array('Flag if formmail should be send only via a webEdition document', false, 'FORMMAIL'),
		'FORMMAIL_LOG' => array('Flag if formmail calls should be logged', true, 'FORMMAIL'),
		'FORMMAIL_EMPTYLOG' => array('Time how long formmail calls should be logged', 604800, 'FORMMAIL'),
		'FORMMAIL_BLOCK' => array('Flag if formmail calls should be blocked after a time', true, 'FORMMAIL'),
		'FORMMAIL_SPAN' => array('Time span in seconds', 300, 'FORMMAIL'),
		'FORMMAIL_TRIALS' => array('Num of trials sending formmail with same ip address in span', 3, 'FORMMAIL'),
		'FORMMAIL_BLOCKTIME' => array('Time to block ip', 86400, 'FORMMAIL'),
// sidebar stuff
		'SIDEBAR_DISABLED' => array('Sidebar is disabled', 1),
		'SIDEBAR_SHOW_ON_STARTUP' => array('Show Sidebar on startup', 1),
		'SIDEBAR_DEFAULT_DOCUMENT' => array('Default document id of the sidebar', 0),
		'SIDEBAR_DEFAULT_WIDTH' => array('Default width of the sidebar', 300),
// extension stuff
		'DEFAULT_STATIC_EXT' => array('Default static extension', ".html", 'EDIT_SETTINGS_DEF_EXT'),
		'DEFAULT_DYNAMIC_EXT' => array('Default dynamic extension', ".php", 'EDIT_SETTINGS_DEF_EXT'),
		'DEFAULT_HTML_EXT' => array('Default html extension', ".html", 'EDIT_SETTINGS_DEF_EXT'),
//naviagtion stuff
		'NAVIGATION_ENTRIES_FROM_DOCUMENT' => array('Flag if new NAV- entries added from Dokument should be items or folders', false),
		'NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH' => array('Flag if NAV- rules should be evaluated even after a first match', false),
		'NAVIGATION_DIRECTORYINDEX_HIDE' => array('Flag if directoy-index files should be hidden in Nav-output', false),
		'NAVIGATION_DIRECTORYINDEX_NAMES' => array('Comma seperated list such as index.php,index.html', 'index.php,index.html'),
		'WYSIWYGLINKS_DIRECTORYINDEX_HIDE' => array('Flag if directoy-index files should be hidden in Wysiwyg-editor output', false),
		'TAGLINKS_DIRECTORYINDEX_HIDE' => array('Flag if directoy-index files should be hidden in tag output', false),
		'NAVIGATION_OBJECTSEOURLS' => array('Flag if we_objectID should be hidden from output of navigation', false),
		'WYSIWYGLINKS_OBJECTSEOURLS' => array('Flag if we_objectID should be hidden from output of wysiwyg editior', false),
		'TAGLINKS_OBJECTSEOURLS' => array('Flag if we_objectID should be hidden from output of tags', false),
		'URLENCODE_OBJECTSEOURLS' => array('Flag if seo-urls should be urlencoded', false),
		'SUPPRESS404CODE' => array('Flag if 404 not found should be suppressd', false),
		'SEOINSIDE_HIDEINWEBEDITION' => array('Flag if should be displayed in webEdition ', false),
		'SEOINSIDE_HIDEINEDITMODE' => array('Flag if should be displayed in Editmode ', false),
		'LANGLINK_SUPPORT' => array('Flag if automatic LanguageLinks should be supported ', true),
//default charset
		'DEFAULT_CHARSET' => array('Default Charset', "UTF-8"),
//countries
		'WE_COUNTRIES_TOP' => array('top countries', "DE,AT,CH"),
		'WE_COUNTRIES_SHOWN' => array('other shown countries', "BE,DK,FI,FR,GR,IE,IT,LU,NL,PT,SE,ES,GB,EE,LT,MT,PL,SK,SI,CZ,HU,CY"),
		'WE_COUNTRIES_DEFAULT' => array('shown if no coutry was choosen', ""),
//versions
		'VERSIONING_IMAGE' => array('Versioning status for ContentType image', 0),
		'VERSIONING_TEXT_HTML' => array('Versioning status for ContentType text/html', 0),
		'VERSIONING_TEXT_WEBEDITION' => array('Versioning status for ContentType text/webedition', 1),
		'VERSIONING_TEXT_HTACCESS' => array('Versioning status for ContentType text/htaccess', 0),
		'VERSIONING_TEXT_WETMPL' => array('Versioning status for ContentType text/weTmpl', 0),
		'VERSIONING_TEXT_JS' => array('Versioning status for ContentType text/js', 0),
		'VERSIONING_TEXT_CSS' => array('Versioning status for ContentType text/css', 0),
		'VERSIONING_TEXT_PLAIN' => array('Versioning status for ContentType text/plain', 0),
		'VERSIONING_FLASH' => array('Versioning status for ContentType application/x-shockwave-flash', 0),
		'VERSIONING_QUICKTIME' => array('Versioning status for ContentType video/quicktime', 0),
		'VERSIONING_SONSTIGE' => array('Versioning status for ContentType application/*', 0),
		'VERSIONING_TEXT_XML' => array('Versioning status for ContentType text/xml', 0),
		'VERSIONING_OBJECT' => array('Versioning status for ContentType objectFile', 0),
		'VERSIONS_TIME_DAYS' => array('Versioning Number of Days', -1),
		'VERSIONS_TIME_WEEKS' => array('Versioning Number of Weeks', -1),
		'VERSIONS_TIME_YEARS' => array('Versioning Number of Years', -1),
		'VERSIONS_ANZAHL' => array('Versioning Number of Versions', ''),
		'VERSIONS_CREATE' => array('Versioning Save version only if publishing', 0),
		'VERSIONS_CREATE_TMPL' => array('Versioning Save template version only on special request', 1),
		'VERSIONS_TIME_DAYS_TMPL' => array('Versioning Number of Days', -1),
		'VERSIONS_TIME_WEEKS_TMPL' => array('Versioning Number of Weeks', -1),
		'VERSIONS_TIME_YEARS_TMPL' => array('Versioning Number of Years', -1),
		'VERSIONS_ANZAHL_TMPL' => array('Versioning Number of Versions', 5),
//security
		'SECURITY_LIMIT_CUSTOMER_IP' => array('Limit # of failed logins comming from the same IP', 10),
		'SECURITY_LIMIT_CUSTOMER_IP_HOURS' => array('Limit failed logins comming from same IP connections per # hours', 3),
		'SECURITY_LIMIT_CUSTOMER_NAME' => array('Limit # of failed logins with same username', 4),
		'SECURITY_LIMIT_CUSTOMER_NAME_HOURS' => array('Limit failed logins with same usernames per # hours', 1),
		'SECURITY_LIMIT_CUSTOMER_REDIRECT' => array('If limit reached, redirect to page', ''),
		'SECURITY_DELAY_FAILED_LOGIN' => array('Delay a failed login by # seconds', 3),
//internal
		'CONF_SAVED_VERSION' => array('config file version', WE_SVNREV),
	),
	'user' => array(//FIXME: most defaults (currently null) are handled by remember_value! change this!
//key => default-val, permission. default true
		'Language' => array('Deutsch'),
		'BackendCharset' => array('UTF-8'),
		'default_tree_count' => array(0),
		'sizeOpt' => array(null),
		'weWidth' => array(0),
		'weHeight' => array(0),
		'cockpit_amount_columns' => array(5),
		'cockpit_amount_last_documents' => array(5),
		'cockpit_dat' => array(''),
		'cockpit_rss_feed_url' => array(''),
		'editorMode' => array('codemirror2'),
		'editorFont' => array(null),
		'editorFontname' => array(null),
		'editorFontsize' => array(null),
		'editorFontcolor' => array(null),
		'editorWeTagFontcolor' => array(null),
		'editorWeAttributeFontcolor' => array(null),
		'editorHTMLTagFontcolor' => array(null),
		'editorHTMLAttributeFontcolor' => array(null),
		'editorPiTagFontcolor' => array(null),
		'editorCommentFontcolor' => array(null),
		'editorLinenumbers' => array(0),
		'editorCodecompletion' => array(serialize(array())),
		'editorTooltips' => array(0),
		'editorDocuintegration' => array(0),
		'editorTooltipFont' => array(null),
		'editorTooltipFontname' => array(null),
		'editorTooltipFontsize' => array(null),
		'message_reporting' => array(7),
		'xhtml_show_wrong' => array(null),
		'xhtml_show_wrong_text' => array(null),
		'xhtml_show_wrong_js' => array(null),
		'xhtml_show_wrong_error_log' => array(null),
		'use_jupload' => array(null),
		'specify_jeditor_colors' => array(null),
		'seem_start_type' => array('cockpit', 'CHANGE_START_DOCUMENT'),
		'seem_start_file' => array(0),
		'seem_start_weapp' => array(0),
		'autostartPlugin' => array(0),
		'DefaultTemplateID' => array(0),
		'editorHeight' => array(0),
		'editorSizeOpt' => array(0),
		'editorWidth' => array(0),
		'FileFilter' => array(0),
		'force_glossary_action' => array(0),
		'force_glossary_check' => array(0),
		'import_from' => array(''),
		'juploadPath' => array(0),
		'openFolders_tblFile' => array(''),
		'openFolders_tblObject' => array(''),
		'openFolders_tblObjectFiles' => array(''),
		'openFolders_tblTemplates' => array(''),
		'promptPlugin' => array(0),
		'siteImportPrefs' => array(''),
		'usePlugin' => array(0),
		'editorShowTab' => array(1),
		'editorTabSize' => array(2),
	),
	'other' => array(
		'formmail_values' => array('', 'FORMMAIL'),
		'formmail_deleted' => array('', 'FORMMAIL'),
		'useproxy' => array(''),
		'proxyhost' => array(''),
		'proxyport' => array(''),
		'proxyuser' => array(''),
		'proxypass' => array(''),
		'active_integrated_modules' => array(''),
		'DB_CONNECT' => array(''),
		'useauth' => array(''), //pseudo element
		'HTTP_USERNAME' => array(''),
		'HTTP_PASSWORD' => array(''),
		'locale_default' => array(''),
		'locale_locales' => array(''),
	),
);

<?php
/**
 * webEdition CMS
 *
 * $Rev: 5946 $
 * $Author: mokraemer $
 * $Date: 2013-03-13 01:22:40 +0100 (Wed, 13 Mar 2013) $
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

@include_once('Text/Diff.php');

function getInfoTable($_infoArr, $name){

	$_table = new we_html_table(array("width" => "500", "style" => "width: 500px;", "spellspacing" => "2"), 1, 2);
	$_i = 0;

	foreach($_infoArr as $_k => $_v){

		$_style = ($_i % 2 ? '' : "background: #D4DBFA;");

		$_table->addRow(1);
		$_table->setRow($_i, array("class" => "defaultfont", "style" => $_style . "height:20px;"));
		$_table->setCol($_i, 0, array("style" => "width: 200px; height: 20px;font-weight: bold; padding-left: 10px;"), $_k);
		$_table->setCol($_i, 1, array("style" => "width: 250px; height: 20px; padding-left: 10px;"), parseValue($_k, $_v));
		$_i++;

		// highlight some values:
		if($name == "PHP"){
			if($_i == 3 && ini_get_bool('register_globals')){
				$_table->setColAttributes(2, 1, array("style" => "border:1px solid red;"));
			}
			if($_i == 6 && ini_get_bool('short_open_tag')){
				$_table->setColAttributes(5, 1, array("style" => "border:1px solid red;"));
			}
			if($_i == 9 && ini_get_bool('safe_mode'))
				$_table->setColAttributes(8, 1, array("style" => "border:1px solid grey;"));
		}
	}
	return $_table->getHtml();
}

function ini_get_bool($val){
	$bool = ini_get($val);
	if($val == "1"){
		return true;
	}
	if($val == "0"){
		return false;
	}
	switch(strtolower($bool)){
		case '1':
		case 'on':
		case 'yes':
		case 'true':
			return true;
		default:
			return false;
	}
	return false;
}

function ini_get_message($val){
	global $_sysinfo;
	$bool = ini_get($val);
	if($val == "1"){
		return 'on';
	}
	if($val == "0"){
		return 'off';
	}
	switch(strtolower($bool)){
		case '1':
		case 'on':
		case 'yes':
		case 'true':
			return 'on';
		case '0':
		case 'off':
		case 'no':
		case 'false':
			return 'off';
		case '':
			return g_l('sysinfo', '[not_set]');
		default:
			return $bool;
	}
	return 'off';
}

function parseValue($name, $value){
	if(in_array($name, array_keys($GLOBALS['_types']))){
		if($GLOBALS['_types'][$name] == 'bytes' && $value){

			$value = we_convertIniSizes($value);
			return convertToMb($value) . ' (' . $value . ' Bytes)';
		}
	}

	return $value;
}

function convertToMb($value){
	return round($value / (1024 * 1024), 3) . ' MB';
}

function getConnectionTypes(){
	$_connectionTypes = array();
	if(ini_get('allow_url_fopen') == "1"){
		$_connectionTypes[] = "fopen";
		$_connectionTypeUsed = "fopen";
	}
	if(is_callable("curl_exec")){
		$_connectionTypes[] = "curl";
		if(count($_connectionTypes) == 1){
			$_connectionTypeUsed = "curl";
		}
	}
	foreach($_connectionTypes as &$con){
		if($con == $_connectionTypeUsed){
			$con = "<u>" . $con . "</u>";
		}
	}
	return $_connectionTypes;
}

function getWarning($message, $value){
	return '<div style="cursor:pointer; padding-right:20px; padding-left:8px; background:url(' . IMAGE_DIR . 'alert_tiny.gif) center right no-repeat;" title="' . $message . '">' . $value . '</div>';
}

function getInfo($message, $value){
	return '<div style="cursor:pointer; padding-right:20px; padding-left:8px; background:url(' . IMAGE_DIR . 'info_tiny.gif) center right no-repeat;" title="' . $message . '">' . $value . '</div>';
}

function getOK($message, $value){
	return '<div style="cursor:pointer; padding-right:20px; padding-left:0px; background:url(' . IMAGE_DIR . 'valid.gif) center right no-repeat;" title="' . $message . '">' . $value . '</div>';
}

$_install_dir = $_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR;

if(strlen($_install_dir) > 35){
	$_install_dir = substr($_install_dir, 0, 25) . '<acronym title="' . $_install_dir . '">&hellip;</acronym>' . substr($_install_dir, -10);
}

$weVersion = WE_VERSION;
if(defined("WE_SVNREV") && WE_SVNREV != '0000'){

	$weVersion .= ' (SVN-Revision: ' . WE_SVNREV . ((defined("WE_VERSION_BRANCH") && WE_VERSION_BRANCH != 'trunk') ? '|' . WE_VERSION_BRANCH : '') . ')';
}
if(defined("WE_VERSION_SUPP") && WE_VERSION_SUPP != '')
	$weVersion .= ' ' . g_l('global', '[' . WE_VERSION_SUPP . ']');
if(defined("WE_VERSION_SUPP_VERSION") && WE_VERSION_SUPP_VERSION != '0')
	$weVersion .= WE_VERSION_SUPP_VERSION;

// GD_VERSION is more precise but only available in PHP 5.2.4 or newer
if(is_callable("gd_info")){
	if(defined("GD_VERSION")){
		$gdVersion = GD_VERSION;
	} else{
		$gdinfoArray = gd_info();
		$gdVersion = $gdinfoArray["GD Version"];
		unset($gdinfoArray);
	}
} else{
	$gdVersion = "";
}

$phpExtensionsDetectable = true;

$phpextensions = get_loaded_extensions();
foreach($phpextensions as &$extens){
	$extens = strtolower($extens);
}
$phpextensionsMissing = array();
$phpextensionsMin = array('ctype', 'date', 'dom', 'filter', 'iconv', 'libxml', 'mysql', 'pcre', 'Reflection', 'session', 'SimpleXML', 'SPL', 'standard', 'tokenizer', 'xml', 'zlib');

if(count($phpextensions) > 3){
	foreach($phpextensionsMin as $exten){
		if(!in_array(strtolower($exten), $phpextensions, true)){
			$phpextensionsMissing[] = $exten;
		}
	}

	if(in_array(strtolower('PDO'), $phpextensions) && in_array(strtolower('pdo_mysql'), $phpextensions)){//sp�ter ODER mysqli
		$phpextensionsSDK_DB = 'PDO &amp; PDO_mysql';
	} else{
		$phpextensionsSDK_DB = getWarning(g_l('sysinfo', "[sdk_db warning]"), '-');
	}
} else{
	$phpExtensionsDetectable = false;
	$phpextensionsSDK_DB = 'unkown';
}
if(in_array('suhosin', get_loaded_extensions())){
	if(ini_get_bool('suhosin.simulation')){
		$SuhosinText = getOK('', g_l('sysinfo', '[suhosin simulation]'));
	} else{
		if(ini_get_bool('suhosin.cookie.encrypt')){
			$SuhosinText = getWarning(g_l('sysinfo', '[suhosin warning]'), 'on' . ' (suhosin.cookie.encrypt=on)');
		} else{
			$SuhosinText = getWarning(g_l('sysinfo', '[suhosin warning]'), 'on');
		}
	}
} else{
	$SuhosinText = getOK('', ini_get_message('suhosin'));
}

$lockTables = $GLOBALS['DB_WE']->hasLock();
$allowTempTables = searchtoolsearch::checkRightTempTable() == '0';

$_info = array(
	'webEdition' => array(
		g_l('sysinfo', '[we_version]') => $weVersion,
		g_l('sysinfo', '[server_name]') => $_SERVER['SERVER_NAME'],
		g_l('sysinfo', '[port]') => $_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : 80,
		g_l('sysinfo', '[protocol]') => getServerProtocol(),
		g_l('sysinfo', '[installation_folder]') => $_install_dir,
		g_l('sysinfo', '[we_max_upload_size]') => getUploadMaxFilesize()
	),
	'<a href="javascript:showPhpInfo();">PHP</a>' => array(
		g_l('sysinfo', '[php_version]') => phpversion(),
		g_l('sysinfo', '[zendframework_version]') => (Zend_Version::VERSION != WE_ZFVERSION) ? getWarning(sprintf(g_l('sysinfo', "[zend_framework warning]"), WE_ZFVERSION), Zend_Version::VERSION) : Zend_Version::VERSION,
		'register_globals' => (ini_get_bool('register_globals')) ? getWarning(g_l('sysinfo', "[register_globals warning]"), ini_get('register_globals')) : getOK('', ini_get_message('register_globals')),
		'max_execution_time' => ini_get('max_execution_time'),
		'memory_limit' => we_convertIniSizes(ini_get('memory_limit')),
		'short_open_tag' => (ini_get_bool('short_open_tag')) ? getWarning(g_l('sysinfo', "[short_open_tag warning]"), ini_get('short_open_tag')) : ini_get_message('short_open_tag'),
		'allow_url_fopen' => ini_get_message('allow_url_fopen'),
		'open_basedir' => ini_get_message('open_basedir'),
		'safe_mode' => (ini_get_bool('safe_mode')) ? getInfo(g_l('sysinfo', "[safe_mode warning]"), ini_get('safe_mode')) : getOK('', ini_get_message('safe_mode')),
		'safe_mode_exec_dir' => ini_get_message('safe_mode_exec_dir'),
		'safe_mode_gid' => ini_get_message('safe_mode_gid'),
		'safe_mode_include_dir' => ini_get_message('safe_mode_include_dir'),
		'upload_max_filesize' => we_convertIniSizes(ini_get('upload_max_filesize')),
		'post_max_size' => we_convertIniSizes(ini_get('post_max_size')),
		'max_input_vars' => (ini_get('max_input_vars') < 2000 ? getWarning('<2000', ini_get('max_input_vars')) : getOK('>=2000', ini_get_message('max_input_vars'))),
		'session.auto_start' => (ini_get_bool('session.auto_start')) ? getWarning(g_l('sysinfo', "[session.auto_start warning]"), ini_get('session.auto_start')) : getOK('', ini_get_message('session.auto_start')),
		'Suhosin' => $SuhosinText,
	),
	'MySql' => array(
		g_l('sysinfo', '[mysql_version]') => (version_compare("5.0.0", getMysqlVer(false)) > 1) ? getWarning(sprintf(g_l('sysinfo', "[dbversion warning]"), getMysqlVer(false)), getMysqlVer(false)) : getOK('', getMysqlVer(false)),
		'max_allowed_packet' => getMaxAllowedPacket(),
		'lock tables' => ($lockTables ? getOK('', g_l('sysinfo', '[available]')) : getWarning('', '-')),
		'create temporary tables' => ($allowTempTables ? getOK('', g_l('sysinfo', '[available]')) : getWarning('', '-')),
		'Info' => $GLOBALS['DB_WE']->getInfo(),
	),
	'System' => array(
		g_l('sysinfo', '[connection_types]') => implode(", ", getConnectionTypes()),
		g_l('sysinfo', '[mbstring]') => (is_callable("mb_get_info") ? g_l('sysinfo', '[available]') : "-"),
		g_l('sysinfo', '[gdlib]') => (!empty($gdVersion) ? g_l('sysinfo', '[version]') . " " . $gdVersion : "-"),
		g_l('sysinfo', '[exif]') => (is_callable("exif_imagetype") ? g_l('sysinfo', '[available]') : getWarning(g_l('sysinfo', "[exif warning]"), '-')),
		g_l('sysinfo', '[pcre]') => ((defined("PCRE_VERSION")) ? ( (substr(PCRE_VERSION, 0, 1) < 7) ? getWarning(g_l('sysinfo', "[pcre warning]"), g_l('sysinfo', '[version]') . ' ' . PCRE_VERSION) : g_l('sysinfo', '[version]') . ' ' . PCRE_VERSION ) : getWarning(g_l('sysinfo', '[available]'), g_l('sysinfo', "[pcre_unkown]"))),
		g_l('sysinfo', '[sdk_db]') => $phpextensionsSDK_DB,
		g_l('sysinfo', '[phpext]') => (!empty($phpextensionsMissing) ? getWarning(g_l('sysinfo', "[phpext warning2]"), g_l('sysinfo', "[phpext warning]") . implode(', ', $phpextensionsMissing)) : ($phpExtensionsDetectable ? g_l('sysinfo', '[available]') : g_l('sysinfo', '[detectable warning]')) ),
		'pear Text_Diff' => class_exists('Text_Diff', false) ? getOk('http://pear.php.net/package/Text_Diff/redirected', g_l('sysinfo', '[available]')) : getWarning('http://pear.php.net/package/Text_Diff/redirected', '-'),
	),
	'Deprecated' => array(
		g_l('prefs', '[backwardcompatibility_tagloading]') => (defined('INCLUDE_ALL_WE_TAGS') && INCLUDE_ALL_WE_TAGS) ? getWarning('Deprecated', '1') : getOk('', '0'),
		'we:saveRegisteredUser register=' => (defined('CUSTOMER_TABLE') && f('SELECT Value FROM ' . CUSTOMER_ADMIN_TABLE . ' WHERE Name="default_saveRegisteredUser_register"', 'Value', $GLOBALS['DB_WE']) == 'true' ? getWarning('Deprecated', 'true') : getOk('', defined('CUSTOMER_TABLE') ? 'false' : '?')),
	),
);


$_types = array(
	'upload_max_filesize' => 'bytes',
	'memory_limit' => 'bytes',
	'max_allowed_packet' => 'bytes',
	g_l('sysinfo', '[we_max_upload_size]') => 'bytes'
);

$buttons = we_button::position_yes_no_cancel(
		we_button::create_button("close", "javascript:self.close()"), '', ''
);


$_space_size = 150;
$_parts = array();
foreach($_info as $_k => $_v){
	$_parts[] = array(
		'headline' => $_k,
		'html' => getInfoTable($_v, strip_tags($_k)),
		'space' => $_space_size
	);
}

$_parts[] = array(
	'headline' => '',
	'html' => '<a href="javascript:showPhpInfo();">' . g_l('sysinfo', '[more_info]') . '&hellip;</a>',
	'space' => 10
);
we_html_tools::htmlTop(g_l('sysinfo', '[sysinfo]'));
?>
<script type="text/javascript"><!--
	function closeOnEscape() {
		return true;
	}

	function showPhpInfo() {
		document.getElementById("info").style.display = "none";
		document.getElementById("more").style.display = "block";
		document.getElementById("phpinfo").src = "phpinfo.php";
	}

	function showInfoTable() {
		document.getElementById("info").style.display = "block";
		document.getElementById("more").style.display = "none";
	}
	//-->
</script>

<?php
print STYLESHEET;
?>

</head>

<body class="weDialogBody" style="overflow:hidden;" onLoad="self.focus();">
	<div id="info" style="display: block;">
		<?php
		print we_multiIconBox::getJS();
		print we_multiIconBox::getHTML('', 700, $_parts, 30, $buttons, -1, '', '', false, "", "", 620, "auto");
		?>
	</div>
	<div id="more" style="display:none;">
		<?php
		$_parts = array(
			array(
				'headline' => '',
				'html' => '<iframe id="phpinfo" style="width:1280px;height:530px;">' . g_l('sysinfo', '[more_info]') . ' &hellip;</iframe>',
				'space' => $_space_size
			),
			array(
				'headline' => '',
				'html' => '<a href="javascript:showInfoTable();">' . g_l('sysinfo', '[back]') . '</a>',
				'space' => 10
			),
		);

		print we_multiIconBox::getHTML('', '100%', $_parts, 30, $buttons, -1, '', '', false);
		?>
	</div>
</body>
</html>
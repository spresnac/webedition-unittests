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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
@include_once('Text/Diff.php');
@include_once('Text/Diff/Renderer/inline.php');

we_html_tools::protect();

$_db = new DB_WE();

$ID = $_REQUEST['we_cmd'][1];

$newDoc = weVersions::loadVersion(' WHERE ID=' . intval($ID));

$compareID = "";
if(isset($_REQUEST['we_cmd'][2])){
	$compareID = $_REQUEST['we_cmd'][2];
	$oldDoc = weVersions::loadVersion(' WHERE ID=' . intval($compareID));
} else{
	$oldDoc = weVersions::loadVersion(' WHERE version < ' . intval($newDoc['version']) . ' AND documentTable="' . $_db->escape($newDoc['documentTable']) . '" AND documentID=' . intval($newDoc['documentID']) . ' ORDER BY version DESC LIMIT 1');
}

$isObj = false;
$isTempl = false;
if($newDoc['ContentType'] == "text/weTmpl"){
	$isTempl = true;
}
if($newDoc['ContentType'] == "objectFile"){
	$isObj = true;
}
if(!($isObj OR $isTempl)){
	//get path of preview-file
	$binaryPathNew = $newDoc['binaryPath'];
	if($binaryPathNew == ""){
		$binaryPathNew = f("SELECT binaryPath FROM " . VERSIONS_TABLE . " WHERE binaryPath!='' AND version<" . intval($newDoc['version']) . " AND documentTable='" . $_db->escape($newDoc['documentTable']) . "' AND documentID=" . intval($newDoc['documentID']) . "  ORDER BY version DESC LIMIT 1 ", "binaryPath", $_db);
	}

	if(!empty($oldDoc)){
		$binaryPathOld = $oldDoc['binaryPath'];
		if($binaryPathOld == ''){
			$binaryPathOld = f('SELECT binaryPath FROM ' . VERSIONS_TABLE . " WHERE binaryPath!='' AND version<" . intval($oldDoc['version']) . " AND documentTable='" . $_db->escape($oldDoc['documentTable']) . "' AND documentID=" . intval($oldDoc['documentID']) . " ORDER BY version DESC LIMIT 1 ", "binaryPath", $_db);
		}
	}

	$filePathNew = $_SERVER['DOCUMENT_ROOT'] . $binaryPathNew;
	$fileNew = $binaryPathNew;
	if(!empty($oldDoc)){
		$fileOld = $binaryPathOld;
	}

	if(!file_exists($filePathNew) && isset($fileOld)){
		$fileNew = $fileOld;
	}
}

//close button
$_button = we_button::create_button("close", "javascript:self.close();");

$we_tabs = new we_tabs();

$we_tabs->addTab(new we_tab("#", g_l('versions', '[versionDiffs]'), '((activ_tab==1) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('1');", array("id" => "tab_1")));

if(!$isObj){
	$we_tabs->addTab(new we_tab("#", g_l('versions', '[previewVersionNew]'), '((activ_tab==2) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('2');", array("id" => "tab_2")));
}
if(!empty($oldDoc) && !$isObj){
	$we_tabs->addTab(new we_tab("#", g_l('versions', '[previewVersionOld]'), '((activ_tab==3) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('3');", array("id" => "tab_3")));
}

$js = $we_tabs->getHeader() . we_html_element::jsElement('
function setTab(tab) {
	toggle("tab"+activ_tab);
	toggle("tab"+tab);
	activ_tab=tab;
}');

function doNotShowFields($k){

	$notshow = array(
		'ID',
		'documentElements',
		'documentScheduler',
		'documentCustomFilter',
		'documentTable',
		'binaryPath',
		'ContentType',
		'modifications',
		'IP',
		'Browser',
		'Icon',
		'CreationDate',
		'Path',
		'ClassName',
		'TableID',
		'ObjectID',
		'IsClassFolder',
		'IsNotEditable',
		'active'
	);

	return !(in_array($k, $notshow));
}

function doNotMarkFields($k){

	$notmark = array(
		'timestamp',
		'version'
	);

	return !(in_array($k, $notmark));
}

$pathLength = 40;

$tabsBody = $we_tabs->getHTML() . we_html_element::jsElement('
						if(!activ_tab) activ_tab = 1;
						document.getElementById("tab_"+activ_tab).className="tabActive";
					');

$contentNew = $contentOld = $contentDiff = '';

if(!($isObj || $isTempl)){
	$contentNew = '<iframe frameBorder="0" name="previewNew" src="' . WEBEDITION_DIR . 'showTempFile.php?file=' . $fileNew . '" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>';
}
if($isTempl){
	if($newDoc['documentElements']){
		$nDocElements = unserialize((substr_compare($newDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($newDoc['documentElements']), ENT_QUOTES) :
				gzuncompress($newDoc['documentElements']))
		);
	} else{
		$nDocElements = array();
	}
	$contentNew = '<textarea style="width:99%;height:99%">' . $nDocElements['data']['dat'] . '</textarea>';
}
if(!empty($oldDoc) && !($isObj || $isTempl)){
	$contentOld = '<iframe frameBorder="0" name="previewOld" src="' . WEBEDITION_DIR . 'showTempFile.php?file=' . $fileOld . '" style="border:0px;width:100%;height:100%;overflow: hidden;"></iframe>';
}
if(!empty($oldDoc) && $isTempl){
	if($oldDoc['documentElements']){
		$oDocElements = unserialize((substr_compare($oldDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($oldDoc['documentElements']), ENT_QUOTES) :
				gzuncompress($oldDoc['documentElements']))
		);
	} else{
		$oDocElements = array();
	}
	$contentOld = '<textarea style="width:99%;height:99%">' . $oDocElements['data']['dat'] . '</textarea>';
}
$_versions_time_days = new we_html_select(array(
		'name' => 'versions_time_days',
		'style' => '',
		'class' => 'weSelect',
		'onchange' => 'previewVersion(' . $ID . ', this.value);'
		)
);

$versionOld = '';
if(!empty($oldDoc)){
	$versionOld = ' AND version!=' . intval($oldDoc['version']);
}
$versions = array();
$_db->query('SELECT ID,version, timestamp FROM ' . VERSIONS_TABLE . ' WHERE documentID=' . intval($newDoc['documentID']) . " AND documentTable='" . $_db->escape($newDoc['documentTable']) . "' AND version!=" . intval($newDoc['version']) . ' ' . $versionOld . "  ORDER BY version ASC");
while($_db->next_record()) {
	$versions[$_db->f("ID")]['version'] = $_db->f("version");
	$versions[$_db->f("ID")]['timestamp'] = date("d.m.y - H:i:s", $_db->f("timestamp"));
}

$_versions_time_days->addOption("", g_l('versions', '[pleaseChoose]'));
foreach($versions as $k => $v){
	$txt = g_l('versions', '[version]') . " " . $v['version'] . " " . g_l('versions', '[from]') . " " . $v['timestamp'];
	$_versions_time_days->addOption($k, $txt);
}

$contentDiff = '<div style="margin-left:25px;" id="top">' . g_l('versions', '[VersionChangeTxt]') . '<br/><br/>' .
	g_l('versions', '[VersionNumber]') . " " . $_versions_time_days->getHtml() . '
			<div style="margin:20px 0px 0px 0px;" class="defaultfont"><a href="javascript:window.print()">' . g_l('versions', '[printPage]') . '</a></div>
			</div>
			<div style="margin:0px 0px 0px 25px;" id="topPrint">
					<strong>' . g_l('versions', '[versionDiffs]') . ':</strong><br/>
					<br/><strong>' . g_l('versions', '[Text]') . ':</strong> ' . $newDoc["Text"] . '
					<br/><strong>' . g_l('versions', '[documentID]') . ':</strong> ' . $newDoc["documentID"] . '
					<br/><strong>' . g_l('versions', '[path]') . ':</strong> ' . $newDoc["Path"] . '
			</div>
			<table cellpadding="5" cellspacing="0" border="0" width="95%" style="background-color:#F5F5F5;margin:15px 15px 15px 25px;border-left:1px solid #B8B8B7;border-right:1px solid #B8B8B7;">
			<tr>
			<td style="border-bottom:1px solid #B8B8B7;background-color:#BCBBBB;">' . we_html_tools::getPixel(30, 15) . '
			</td>
	  		<td class="defaultfont" align="left" style="border-bottom:1px solid #B8B8B7;background-color:#BCBBBB;"><strong>' . g_l('versions', '[VersionNew]') . '</strong></td>' .
	(empty($oldDoc) ? '' :
		'<td class="defaultfont" align="left" style="border-left:1px solid #B8B8B7;background-color:#BCBBBB;border-bottom:1px solid #B8B8B7;"><strong>' . g_l('versions', '[VersionOld]') . '</strong></td>') .
	'</tr>';

foreach($newDoc as $k => $v){
	if(doNotShowFields($k)){
		$name = g_l('versions', '[' . $k . ']');

		$oldVersion = true;
		$newVal = ($k == "ParentID" ?
				$newDoc['Path'] :
				weVersions::showValue($k, $newDoc[$k], $newDoc['documentTable'])
			);

		if($k == "Owners" && $newDoc[$k] == ""){
			$newVal = g_l('versions', '[CreatorID]');
		}

		$mark = "border-bottom:1px solid #B8B8B7; ";
		if(!empty($oldDoc)){
			$oldVal = ($k == "ParentID" ?
					$oldDoc['Path'] :
					weVersions::showValue($k, $oldDoc[$k], $oldDoc['documentTable']));

			if($k == "Owners" && $oldDoc[$k] == ""){
				$oldVal = g_l('versions', '[CreatorID]');
			}
			if(doNotMarkFields($k)){
				if($newVal != $oldVal){
					$mark .= "background-color:#BFD5FF;";
				}
			}
		} else{
			$oldVersion = false;
		}

		$contentDiff .= '<tr>
<td width="33%" style="' . $mark . '"><strong>' . $name . '</strong></td>
<td width="33%" style="' . $mark . '">' . $newVal . '</td>' .
			($oldVersion ? '<td width="33%" style="' . $mark . 'border-left:1px solid #B8B8B7;">' . $oldVal . '</td>' : '') .
			'</tr>';
	}
}

$contentDiff .= '</table>';

//elements

$contentDiff .= '<table cellpadding="5" cellspacing="0" border="0" width="95%" style="background-color:#F5F5F5;margin:15px 15px 15px 25px;border-left:1px solid #B8B8B7;border-right:1px solid #B8B8B7;">
		<tr>
		<td align="left" colspan="3" style="padding:5px;background-color:#BCBBBB;" class="defaultfont"><strong>' . g_l('versions', '[contentElementsMod]') . '</strong>' .
	(class_exists('Text_Diff', false) ? '' : '<br/><b>PHP-Pear-Text_Diff not installed - Quirks mode.</b>') .
	'</td></tr>';
if($newDoc['documentElements']){
	$newDocElements = unserialize((substr_compare($newDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
			html_entity_decode(urldecode($newDoc['documentElements']), ENT_QUOTES) :
			gzuncompress($newDoc['documentElements']))
	);
} else{
	$newDocElements = array();
}

if(isset($oldDoc['documentElements'])){
	if($oldDoc['documentElements']){
		$oldDocElements = unserialize((substr_compare($oldDoc['documentElements'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($oldDoc['documentElements']), ENT_QUOTES) :
				gzuncompress($oldDoc['documentElements']))
		);
	} else{
		$oldDocElements = array();
	}
}
if(!empty($newDocElements)){
	foreach($newDocElements as $k => $v){
		$name = ($k != "") ? $k : we_html_tools::getPixel(1, 1);
		$oldVersion = true;
		//skip this value - it is of no interest; everything is in data
		if($isTempl && $name == 'completeData'){
			continue;
		}

		if($k == 'weInternVariantElement'){
			$newVal = weVersions::showValue($k, $newDocElements[$k]['dat']);
		} else{
			$newVal = (isset($v['dat']) && $v['dat'] != "") ? $v['dat'] : we_html_tools::getPixel(1, 1);
		}

		$mark = "border-bottom:1px solid #B8B8B7; ";
		if(!empty($oldDoc)){

			if($k == 'weInternVariantElement' && isset($oldDocElements[$k]['dat'])){
				$oldVal = weVersions::showValue($k, $oldDocElements[$k]['dat']);
			} elseif(isset($oldDocElements[$k]['dat']) && $oldDocElements[$k]['dat'] != ""){
				$oldVal = $oldDocElements[$k]['dat'];
			} else{
				$oldVal = we_html_tools::getPixel(1, 1);
			}

			if($newVal != $oldVal){
				$mark .= "background-color:#BFD5FF;";
			}
		} else{
			$oldVersion = false;
			$oldVal = '';
		}

		/*
		  $newVal = shortenPathSpace($newVal, $pathLength);
		  if($oldVersion) {
		  $oldVal = shortenPathSpace($oldVal, $pathLength);
		  }
		 */
		//make sure none of them is an array
		if(is_array($newVal)){
			$newVal = print_r($newVal, true);
		}
		if(is_array($oldVal)){
			$oldVal = print_r($oldVal, true);
		}

		//if one of them contains newlines, format it as pre-block
		if(true || $isTempl){
			if(preg_match("/(%0A|%0D|\\n+|\\r+)/i", $newVal) || preg_match("/(%0A|%0D|\\n+|\\r+)/i", $oldVal)){
				$pre = '<pre style="font-size:0.9em;' . (class_exists('Text_Diff') ? '' : 'width:400px;') . 'overflow:auto;">';
				$div = '';
			} else{
				$pre = '';
				$div = '<div style="width:400px;overflow:auto">';
			}
		} else{
			$pre = $div = '';
		}

		$contentDiff .= '<tr>
<td width="33%" style="' . $mark . '"><strong>' . $name . '</strong></td>';
		if(class_exists('Text_Diff', false) && $pre != ''){
			$oldVal = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $oldVal)));
			$newVal = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $newVal)));
			$diff = new Text_Diff('native', array(($oldVersion ? $oldVal : array()), is_array($newVal) ? $newVal : array()));
			$renderer = new Text_Diff_Renderer_inline(array('ins_prefix' => '###INS_START###', 'ins_suffix' => '###INS_END###',
					'del_prefix' => '###DEL_START###', 'del_suffix' => '###DEL_END###',));

			$text = str_replace('###INS_START###', '<span style="color:blue;">+<span style="font-weight:bold;text-decoration:underline;">', str_replace('###INS_END###', '</span>+</span>', str_replace('###DEL_END###', '</span>-</span>', str_replace('###DEL_START###', '<span style="color:red;">-<span style="font-weight:bold;text-decoration: line-through;">-', $renderer->render($diff)))));

			$contentDiff .= '<td colspan="2" style="' . $mark . '">' . $pre . $text . '</pre></td>';
		} else{
			if($newVal != we_html_tools::getPixel(1, 1) && $k != 'weInternVariantElement'){
				$newVal = oldHtmlspecialchars($newVal);
			}

			$contentDiff .= '<td width="33%" style="' . $mark . '">' . $div . $pre . $newVal . ($pre == '' ? '' : '</pre>') . ($div == '' ? '' : '</div>') . '</td>';
			if($oldVersion){
				if($oldVal != we_html_tools::getPixel(1, 1) && $k != 'weInternVariantElement'){
					$oldVal = oldHtmlspecialchars($oldVal);
				}
				$contentDiff .= '<td width="33%" style="' . $mark . 'border-left:1px solid #B8B8B7;">' . $div . $pre . $oldVal . ($pre == '' ? '' : '</pre>') . ($div == '' ? '' : '</div>') . '</td>';
			}
		}
		$contentDiff .= '</tr>';
	}
}

$contentDiff .= '</table>' .
//scheduler
	'<table cellpadding="5" cellspacing="0" border="0" width="95%" style="background-color:#F5F5F5;margin:15px 15px 15px 25px;border-left:1px solid #B8B8B7;border-right:1px solid #B8B8B7;">
<tr>
	<td align="left" colspan="3" style="padding:5px;background-color:#BCBBBB;" class="defaultfont"><strong>' . g_l('versions', '[schedulerMod]') . '</strong></td>
</tr>';

if($newDoc['documentScheduler']){
	$newDocScheduler = unserialize((substr_compare($newDoc['documentScheduler'], 'a%3A', 0, 4) == 0 ?
			html_entity_decode(urldecode($newDoc['documentScheduler']), ENT_QUOTES) :
			gzuncompress($newDoc['documentScheduler']))
	);
} else{
	$newDocScheduler = array();
}
if(isset($oldDoc['documentScheduler'])){
	if($oldDoc['documentScheduler']){
		$oldDocScheduler = unserialize((substr_compare($oldDoc['documentScheduler'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($oldDoc['documentScheduler']), ENT_QUOTES) :
				gzuncompress($oldDoc['documentScheduler']))
		);
	} else{
		$oldDocScheduler = array();
	}
}

$mark = "border-bottom:1px solid #B8B8B7; ";

if(empty($newDocScheduler) && empty($oldDocScheduler)){
	$contentDiff .= '<tr><td style="border-bottom:1px solid #B8B8B7;">-</td></tr>';
} elseif(empty($newDocScheduler) && !empty($oldDocScheduler)){

	foreach($oldDocScheduler as $k => $v){
		$number = $k + 1;
		$contentDiff .= '<tr>
	<td width="33%" style="background-color:#FFF; "><strong>' . g_l('versions', '[scheduleTask]') . ' ' . $number . '</strong></td>
	<td width="33%" style="background-color:#FFF;">' . we_html_tools::getPixel(1, 1) . '</td>
	<td width="33%" style="background-color:#FFF;">' . we_html_tools::getPixel(1, 1) . '</td>
</tr>';

		foreach($v as $key => $val){
			$name = g_l('versions', '[' . $key . ']');
			$newVal = we_html_tools::getPixel(1, 1);
			if(!is_array($val)){
				$oldVal = weVersions::showValue($key, $val, $oldDoc['documentTable']);
			} else{
				$oldVal = (is_array($val) ?
						weVersions::showValue($key, $val, $oldDoc['documentTable']) :
						we_html_tools::getPixel(1, 1));
			}


			$contentDiff .= '<tr>
	<td width="33%" style="' . $mark . '"><strong>' . $name . '</strong></td>
	<td width="33%" style="' . $mark . '">' . $newVal . '</td>
	<td width="33%" style="border-left:1px solid #B8B8B7;' . $mark . '">' . $oldVal . '</td>
</tr>';
		}
	}
} else{
	foreach($newDocScheduler as $k => $v){
		$number = $k + 1;

		$contentDiff .= '<tr>
	<td width="33%" style="background-color:#FFF; "><strong>' . g_l('versions', '[scheduleTask]') . ' ' . $number . '</strong></td>
	<td width="33%" style="background-color:#FFF;">' . we_html_tools::getPixel(1, 1) . '</td>' .
			(empty($oldDoc) ? '' : '<td width="33%" style="background-color:#FFF;">' . we_html_tools::getPixel(1, 1) . '</td>') . '
</tr>';


		foreach($v as $key => $val){
			$mark = "border-bottom:1px solid #B8B8B7; ";
			$name = g_l('versions', '[' . $key . ']');

			if(!is_array($val)){
				$newVal = weVersions::showValue($key, $val, $newDoc['documentTable']);

				if(!empty($oldDocScheduler)){
					$oldVal = (isset($oldDocScheduler[$k][$key]) && !is_array($oldDocScheduler[$k][$key]) ?
							weVersions::showValue($key, $oldDocScheduler[$k][$key], $oldDoc['documentTable']) :
							we_html_tools::getPixel(1, 1));

					if($newVal != $oldVal){
						$mark .= "background-color:#BFD5FF;";
					}
				} else{
					$oldVal = we_html_tools::getPixel(1, 1);
				}
			} else{
				$newVal = weVersions::showValue($key, $val, $newDoc['documentTable']);
				if(!empty($oldDocScheduler)){
					$oldVal = (isset($oldDocScheduler[$k][$key]) && is_array($oldDocScheduler[$k][$key]) ?
							weVersions::showValue($key, $oldDocScheduler[$k][$key], $oldDoc['documentTable']) :
							we_html_tools::getPixel(1, 1));

					if($newVal != $oldVal){
						$mark .= "background-color:#BFD5FF;";
					}
				} else{
					$oldVal = we_html_tools::getPixel(1, 1);
				}
			}

			$contentDiff .= '<tr>
	<td width="33%" style="' . $mark . '"><strong>' . $name . '</strong></td>
	<td width="33%" style="' . $mark . '">' . $newVal . '</td>' .
				(empty($oldDoc) ? '' : '<td width="33%" style="border-left:1px solid #B8B8B7;' . $mark . '">' . $oldVal . '</td>') . '
</tr>';
		}
	}
}

$contentDiff .= '</table>' .
//customfilter
	'<table cellpadding="5" cellspacing="0" border="0" width="95%" style="background-color:#F5F5F5;margin:15px 15px 15px 25px;border-left:1px solid #B8B8B7;border-right:1px solid #B8B8B7;">
<tr>
	<td align="left" colspan="3" style="padding:5px;background-color:#BCBBBB;" class="defaultfont"><strong>' . g_l('versions', '[customerMod]') . '</strong></td>
</tr>';

if($newDoc['documentCustomFilter']){
	$newCustomFilter = unserialize((substr_compare($newDoc['documentCustomFilter'], 'a%3A', 0, 4) == 0 ?
			html_entity_decode(urldecode($newDoc['documentCustomFilter']), ENT_QUOTES) :
			gzuncompress($newDoc['documentCustomFilter']))
	);
} else{
	$newCustomFilter = array();
}
if(isset($oldDoc['documentCustomFilter'])){
	if($oldDoc['documentCustomFilter']){
		$oldCustomFilter = unserialize((substr_compare($oldDoc['documentCustomFilter'], 'a%3A', 0, 4) == 0 ?
				html_entity_decode(urldecode($oldDoc['documentCustomFilter']), ENT_QUOTES) :
				gzuncompress($oldDoc['documentCustomFilter']))
		);
	} else{
		$oldCustomFilter = array();
	}
}

$mark = "border-bottom:1px solid #B8B8B7; ";

if(empty($newCustomFilter) && empty($oldCustomFilter)){
	$contentDiff .= '<tr><td style="border-bottom:1px solid #B8B8B7;">-</td></tr>';
} elseif(empty($newCustomFilter) && !empty($oldCustomFilter)){

	foreach($oldCustomFilter as $key => $val){

		$name = g_l('versions', '[' . $key . ']');
		$newVal = we_html_tools::getPixel(1, 1);
		if(!is_array($val)){
			$oldVal = weVersions::showValue($key, $val, $oldDoc['documentTable']);
		} else{
			$oldVal = (is_array($val) ?
					weVersions::showValue($key, $val, $oldDoc['documentTable']) :
					we_html_tools::getPixel(1, 1));
		}

		$contentDiff .= '<tr>
	<td width="33%" style="' . $mark . '"><strong>' . $name . '</strong></td>
	<td width="33%" style="' . $mark . 'border-right:1px solid #000;">' . $newVal . '</td>' .
			(empty($oldDoc) ? '' : '<td width="33%" style="' . $mark . '">' . $oldVal . '</td>') . '
</tr>';
	}
} else{
	foreach($newCustomFilter as $key => $val){

		$name = g_l('versions', '[' . $key . ']');

		$mark = "border-bottom:1px solid #B8B8B7; ";

		if(!is_array($val)){
			$newVal = weVersions::showValue($key, $val, $newDoc['documentTable']);
			if(!empty($oldCustomFilter)){
				$oldVal = (!is_array($oldCustomFilter[$key]) ?
						weVersions::showValue($key, $oldCustomFilter[$key], $oldDoc['documentTable']) :
						we_html_tools::getPixel(1, 1));

				if($newVal != $oldVal){
					$mark .= "background-color:#BFD5FF;";
				}
			} else{
				$oldVal = we_html_tools::getPixel(1, 1);
			}
		} else{
			$newVal = weVersions::showValue($key, $val, $newDoc['documentTable']);
			if(!empty($oldCustomFilter)){
				$oldVal = (isset($oldCustomFilter[$key]) && is_array($oldCustomFilter[$key]) ?
						weVersions::showValue($key, $oldCustomFilter[$key], $oldDoc['documentTable']) :
						we_html_tools::getPixel(1, 1));

				if($newVal != $oldVal){
					$mark .= "background-color:#BFD5FF;";
				}
			} else{
				$oldVal = we_html_tools::getPixel(1, 1);
			}
		}

		$contentDiff .= '<tr>
	<td width="33%" style="' . $mark . '"><strong>' . $name . '</strong></td>
	<td width="33%" style="' . $mark . '">' . $newVal . '</td>' .
			(empty($oldDoc) ? '' : '<td width="33%" style="' . $mark . '">' . $oldVal . '</td>') . '
</tr>';
	}
}

$contentDiff .= '</table>';

if(!$isObj){
	$_tab_1 = $contentDiff;
	$_tab_2 = $contentNew;
	$_tab_3 = $contentOld;
	$activTab = 1;
} else{
	$_tab_1 = $contentDiff;
	$_tab_2 = "";
	$_tab_3 = "";
	$activTab = 1;
}



we_html_tools::htmlTop("webEdition - " . g_l('versions', '[versioning]'), ($newDoc['Charset'] ? $newDoc['Charset'] : DEFAULT_CHARSET));

print STYLESHEET;
?>

<script type="text/javascript">
	<!--
	var activ_tab = <?php print $activTab; ?>;

	function toggle(id){
		var elem = document.getElementById(id);
		if(elem.style.display == "none") elem.style.display = "block";
		else elem.style.display = "none";
	}

	function previewVersion(ID, newID) {
		top.opener.top.we_cmd("versions_preview", ID, newID);
		//new jsWindow("<?php print WEBEDITION_DIR; ?>we/include/we_versions/weVersionsPreview.php?ID="+ID+"&newCompareID="+newID+"", "version_preview",-1,-1,1000,750,true,true,true,true);

	}
	//-->
</script>
<?php print we_html_element::jsScript(JS_DIR . 'windows.js') . $js; ?>
<style type="text/css" media="screen">
	body {margin: 0;padding: 0;}
	td {font-size:11px;vertical-align:top;}
	#tab1 {position:absolute;overflow:auto; }
	#topPrint {display: none;}
</style>

<style type="text/css" media="print">
	body {margin: 0;padding: 0;}
	td {font-size:9px;vertical-align:top;}
	#tab1 {position:relative;overflow: visible;font-size:12px; }
	#tab2 {display: none}
	#tab3 {display: none}
	#mytabs {display: none}
	#top {display: none}
	#topPrint {display: block}
</style>
</head>

<body>
	<div id="mytabs">
<?php print $tabsBody; ?>
	</div>
	<div id="content" style="position:absolute;margin: 0px; top:30px;bottom:40px;left:0px;right:0px;overflow:auto;">
		<div id="tab1" style="display:block;">
<?php print $_tab_1 ?>
		</div>
		<div id="tab2" style="display:none;height:100%;width:100%">
<?php print $_tab_2 ?>
		</div>
		<div id="tab3" style="display:none;height:100%;width:100%">
<?php print $_tab_3 ?>
		</div>
	</div>

	<div style="left:0px;height:40px;background-image: url(<?php echo IMAGE_DIR; ?>edit/editfooterback.gif);position:fixed;bottom:0px;width:100%">
		<div align="right" style="padding: 10px 10px 0 0;"><?php echo $_button; ?></div>
	</div>
</body>
</html>
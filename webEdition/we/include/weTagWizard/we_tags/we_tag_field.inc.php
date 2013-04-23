<?php
	//NOTE you are inside the constructor of weTagData.class.php

	$this->NeedsEndTag = false;
	//$this->Groups[] = 'input_tags';
	//$this->Module = '';
	$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

	$name = new weTagData_choiceAttribute('name', array(new weTagDataOption('WE_PATH', false, ''), new weTagDataOption('WE_ID', false, ''), new weTagDataOption('WE_TEXT', false, ''), new weTagDataOption('wedoc_CreationDate', false, ''), new weTagDataOption('wedoc_ModDate', false, ''), new weTagDataOption('wedoc_Published', false, ''), new weTagDataOption('wedoc_ParentID', false, ''), new weTagDataOption('wedoc_Text', false, ''), new weTagDataOption('WE_SHOPVARIANTS', false, '')), false,false, '');
	$classid = (defined("OBJECT_TABLE") ? new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, ''): null);
	$hyperlink = new weTagData_selectAttribute('hyperlink', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
	$tid = (defined("TEMPLATES_TABLE") ? new weTagData_selectorAttribute('tid',TEMPLATES_TABLE, 'text/weTmpl', false, ''): null);
	$href = new weTagData_textAttribute('href', false, '');
	$target = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false,false, '');
	$class = new weTagData_textAttribute('class', false, '');
	$style = new weTagData_textAttribute('style', false, '');
	$format = new weTagData_textAttribute('format', false, '');
	$num_format = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('swiss', false, '')), false,false, '');
	$thumbnail = new weTagData_sqlRowAttribute('thumbnail',THUMBNAILS_TABLE, false, 'Name', '', '', '');
	$id = (defined("FILE_TABLE") ? new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, ''):null);
	$parentidname = new weTagData_textAttribute('parentidname', false, '');
	$winprops = new weTagData_textAttribute('winprops', false, '');
	$alt = new weTagData_textAttribute('alt', false, '');
	$max = new weTagData_textAttribute('max', false, '');
	$src = new weTagData_textAttribute('src', false, '');
	$width = new weTagData_textAttribute('width', false, '');
	$height = new weTagData_textAttribute('height', false, '');
	$border = new weTagData_textAttribute('border', false, '');
	$hspace = new weTagData_textAttribute('hspace', false, '');
	$vspace = new weTagData_textAttribute('vspace', false, '');
	$align = new weTagData_selectAttribute('align', array(new weTagDataOption('left', false, ''), new weTagDataOption('right', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('bottom', false, ''), new weTagDataOption('absmiddle', false, ''), new weTagDataOption('middle', false, ''), new weTagDataOption('texttop', false, ''), new weTagDataOption('baseline', false, ''), new weTagDataOption('absbottom', false, '')), false, '');
	//$only = new weTagData_textAttribute('only', false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$seeMode = new weTagData_selectAttribute('seeMode', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$win2iso = new weTagData_selectAttribute('win2iso', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$listviewname = new weTagData_textAttribute('listviewname', false, '');
$striphtml = new weTagData_selectAttribute('striphtml', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$only = new weTagData_selectAttribute('only', array(new weTagDataOption('name', false, ''), new weTagDataOption('src', false, ''), new weTagDataOption('parentpath', false, ''), new weTagDataOption('filename', false, ''), new weTagDataOption('extension', false, ''), new weTagDataOption('filesize', false, '')), false, '');
$onlyImg = new weTagData_selectAttribute('only', array(new weTagDataOption('name', false, ''), new weTagDataOption('src', false, ''), new weTagDataOption('parentpath', false, ''), new weTagDataOption('filename', false, ''), new weTagDataOption('extension', false, ''), new weTagDataOption('filesize', false, ''),new weTagDataOption('width', false, ''),new weTagDataOption('height', false, ''),new weTagDataOption('alt', false, '')), false, '');
$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$triggerid = (defined("FILE_TABLE") ? new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, ''): null);
$usekey = new weTagData_selectAttribute('usekey', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

$to = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$nameto = new weTagData_textAttribute('nameto', false, '');


$this->TypeAttribute = new weTagData_typeAttribute('type', array(
new weTagDataOption('-', false, '', array(), array()),
new weTagDataOption('text', false, '', array($name,$hyperlink,$href,$target,$num_format,$alt,$max,$striphtml,$htmlspecialchars,$triggerid,$to,$nameto), array($name)),
new weTagDataOption('date', false, '', array($name,$hyperlink,$href,$target,$format,$alt,$max,$htmlspecialchars,$triggerid,$to,$nameto), array($name)),
new weTagDataOption('img', false, '', array($name,$hyperlink,$href,$target,$thumbnail,$src,$width,$height,$border,$hspace,$vspace,$align,$onlyImg,$triggerid,$to,$nameto), array($name)),
new weTagDataOption('flashmovie', false, '', array($name,$width,$height,$triggerid,$to,$nameto), array($name)),
new weTagDataOption('href', false, '', array($name,$to,$nameto), array($name)),
new weTagDataOption('link', false, '', array($name,$to,$nameto), array($name)),
new weTagDataOption('day', false, '', array($to,$nameto), array()),
new weTagDataOption('dayname', false, '', array($to,$nameto), array()),
new weTagDataOption('week', false, '', array($to,$nameto), array()),
new weTagDataOption('month', false, '', array($to,$nameto), array()),
new weTagDataOption('monthname', false, '', array($to,$nameto), array()),
new weTagDataOption('year', false, '', array($to,$nameto), array()),
new weTagDataOption('select', false, 'object', array($name,$usekey,$htmlspecialchars,$triggerid,$to,$nameto), array($name)),
new weTagDataOption('binary', false, 'object', array($name,$hyperlink,$href,$target,$only,$to,$nameto), array($name)),
new weTagDataOption('float', false, '', array($name,$hyperlink,$href,$target,$num_format,$triggerid,$to,$nameto), array($name)),
new weTagDataOption('int', false, 'object', array($name,$hyperlink,$href,$target,$triggerid,$to,$nameto), array($name)),
new weTagDataOption('shopVat', false, '', array($to,$nameto), array()),
new weTagDataOption('checkbox', false, '', array($to,$nameto), array()),
new weTagDataOption('country', false, '', array($outputlanguage,$doc,$to,$nameto), array()),
new weTagDataOption('language', false, '', array($outputlanguage,$doc,$to,$nameto), array())
), false, '');

$this->Attributes=array($name,$classid,$hyperlink,$tid,$href,$target,$class,$style,$format,$num_format,$thumbnail,$id,$parentidname,$winprops,$alt,$max,$src,
$width,$height,$border,$hspace,$vspace,$align,$only,$onlyImg,$htmlspecialchars,$seeMode,$xml,$win2iso,$listviewname,$striphtml,$outputlanguage,$doc,$triggerid,$usekey,
$to,$nameto);

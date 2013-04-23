<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$size = new weTagData_textAttribute('size', false, '');
$maxlength = new weTagData_textAttribute('maxlength', false, '');
$format = new weTagData_textAttribute('format', false, '');
$mode = new weTagData_selectAttribute('mode', array(new weTagDataOption('add', false, ''), new weTagDataOption('replace', false, '')), false, '');
$value = new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$html = new weTagData_selectAttribute('html', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$php = new weTagData_selectAttribute('php', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$num_format = new weTagData_selectAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('swiss', false, '')), false, '');
$precision = new weTagData_textAttribute('precision', false, '');
$win2iso = new weTagData_selectAttribute('win2iso', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$reload = new weTagData_selectAttribute('reload', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$seperator = new weTagData_textAttribute('seperator', false, '');
$user = new weTagData_multiSelectorAttribute('user',USER_TABLE, 'user,folder', 'Text', false, 'users');
$spellcheck = new weTagData_selectAttribute('spellcheck', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'spellchecker');
$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$to = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$nameto = new weTagData_textAttribute('nameto', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('text', false, '', array($name,$size,$maxlength,$value,$html,$php,$num_format,$precision,$user,$htmlspecialchars,$spellcheck,$to,$nameto,), array($name)),
	new weTagDataOption('checkbox', false, '', array($name,$value,$reload,$user,$htmlspecialchars,$to,$nameto,), array($name)),
	new weTagDataOption('date', false, '', array($name,$format,$user,$htmlspecialchars,$to,$nameto,), array($name)),
	new weTagDataOption('choice', false, '', array($name,$size,$maxlength,$mode,$values,$reload,$seperator,$user,$htmlspecialchars,$to,$nameto,), array($name)),
	new weTagDataOption('select', false, '', array($name,$values,$htmlspecialchars,$to,$nameto,), array($name)),
	new weTagDataOption('country', false, '', array($name,$outputlanguage,$doc,$to,$nameto,), array($name)),
	new weTagDataOption('language', false, '', array($name,$outputlanguage,$doc,$to,$nameto), array($name))), true, '');

$this->Attributes=array($name,$size,$maxlength,$format,$mode,$value,$values,$html,$htmlspecialchars,$php,$num_format,$precision,$win2iso,$reload,
	$seperator,$user,$spellcheck,$outputlanguage,$doc,$to,$nameto);

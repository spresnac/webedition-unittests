<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$id_document = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition,image/*,text/css,text/js,application/*', true, '');
$id_object = (defined("OBJECT_FILES_TABLE")?new weTagData_selectorAttribute('id',OBJECT_FILES_TABLE, 'objectFile', true, ''):null);
$triggerid = new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, '');
$hidedirindex = new weTagData_selectAttribute('hidedirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$objectseourls = new weTagData_selectAttribute('objectseourls', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$to = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$nameto = new weTagData_textAttribute('nameto', false, '');
$this->Attributes=array();
$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('document', false, '', array($id_document,$hidedirindex,$to,$nameto), array($id_document)),
	new weTagDataOption('object', false, 'object', array($id_object,$triggerid,$hidedirindex,$objectseourls,$to,$nameto), array($id_object))), false, '');

$this->Attributes = array($id_document,$id_object,$triggerid,$to,$nameto,$hidedirindex,$objectseourls);
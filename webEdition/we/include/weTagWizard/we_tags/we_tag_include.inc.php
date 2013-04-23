<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$included = new weTagData_selectAttribute('included', array(), false, '');
$id = (defined("FILE_TABLE") ? new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, ''): null);
$path = new weTagData_textAttribute('path', false, '');
$gethttp = new weTagData_selectAttribute('gethttp', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$seeMode = new weTagData_selectAttribute('seeMode', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$kind = new weTagData_selectAttribute('kind', array(new weTagDataOption('all', false, ''), new weTagDataOption('int', false, ''), new weTagDataOption('ext', false, '')), false, '');
$name = new weTagData_textAttribute('name', false, '');
$id_temp = (defined("TEMPLATES_TABLE") ? new weTagData_selectorAttribute('id',TEMPLATES_TABLE, 'text/weTmpl', false, ''):null);
$rootdir = new weTagData_textAttribute('rootdir', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
new weTagDataOption('document', false, '', array($id,$path,$gethttp,$seeMode,$kind,$name,$rootdir), array()),
new weTagDataOption('template', false, '', array($path,$id_temp), array())), false, '');

$this->Attributes=array($included,$id,$path,$gethttp,$seeMode,$kind,$name,$id_temp,$rootdir);

<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$doctype = new weTagData_sqlRowAttribute('doctype',DOC_TYPES_TABLE, false, 'DocType', 'DocType', 'DocType', '');
$classid = (defined("OBJECT_TABLE") ? new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, ''):null);
$pid = new weTagData_selectorAttribute('pid',FILE_TABLE, 'folder', false, '');
$pidO = (defined("OBJECT_FILES_TABLE") ? new weTagData_selectorAttribute('pid',OBJECT_FILES_TABLE, 'folder', false, ''):null);
$protected = new weTagData_selectAttribute('protected', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$admin = new weTagData_textAttribute('admin', false, '');
$forceedit = new weTagData_selectAttribute('forceedit', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$mail = new weTagData_textAttribute('mail', false, '');
$mailfrom = new weTagData_textAttribute('mailfrom', false, '');
$charset = new weTagData_textAttribute('charset', false, '');
$userid = new weTagData_textAttribute('userid', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
new weTagDataOption('document', false, '', array($doctype,$pid,$userid,$admin,$forceedit,$mail,$mailfrom,$charset,$protected), array()),
new weTagDataOption('object', false, '', array($classid,$userid,$admin,$forceedit,$mail,$mailfrom,$charset,$pidO,$protected), array())), false, '');

$this->Attributes=array($doctype,$classid,$pid,$pidO,$protected,$admin,$forceedit,$mail,$mailfrom,$charset,$userid);

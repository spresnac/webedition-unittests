<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$id = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, '');
$name = new weTagData_textAttribute('name', false, '');
$nameid = new weTagData_textAttribute('nameid', false, '');
$method = new weTagData_selectAttribute('method', array(new weTagDataOption('get', false, ''), new weTagDataOption('post', false, '')), false, '');
$target = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false,false, '');
$recipient = new weTagData_textAttribute('recipient', true, '');
$onsuccess = new weTagData_selectorAttribute('onsuccess',FILE_TABLE, 'text/webedition', false, '');
$onerror = new weTagData_selectorAttribute('onerror',FILE_TABLE, 'text/webedition', false, '');
$onmailerror = new weTagData_selectorAttribute('onmailerror',FILE_TABLE, 'text/webedition', false, '');
$onrecipienterror = new weTagData_selectorAttribute('onrecipienterror',FILE_TABLE, 'text/webedition', false, '');
$from = new weTagData_textAttribute('from', false, '');
$subject = new weTagData_textAttribute('subject', false, '');
$charset = new weTagData_textAttribute('charset', false, '');
$order = new weTagData_textAttribute('order', false, '');
$required = new weTagData_textAttribute('required', false, '');
$remove = new weTagData_textAttribute('remove', false, '');
$mimetype = new weTagData_selectAttribute('mimetype', array(new weTagDataOption('text/plain', false, ''), new weTagDataOption('text/html', false, '')), false, '');
$confirmmail = new weTagData_selectAttribute('confirmmail', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$forcefrom = new weTagData_selectAttribute('forcefrom', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$preconfirm = new weTagData_textAttribute('preconfirm', false, '');
$postconfirm = new weTagData_textAttribute('postconfirm', false, '');
$doctype = new weTagData_sqlRowAttribute('doctype',DOC_TYPES_TABLE, true, 'DocType', '', '', '');
$categories = new weTagData_multiSelectorAttribute('categories',CATEGORY_TABLE, '', 'Path', false, '');
$tid = (defined("TEMPLATES_TABLE") ? new weTagData_selectorAttribute('tid',TEMPLATES_TABLE, 'text/weTmpl', false, ''):null);
$classid = (defined("OBJECT_TABLE") ? new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, 'object'):null);
$parentid = (defined("OBJECT_FILES_TABLE") ? new weTagData_selectorAttribute('parentid',OBJECT_FILES_TABLE, 'folder', false, ''):null);
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$enctype = new weTagData_selectAttribute('enctype', array(new weTagDataOption('application/x-www-form-urlencoded', false, ''), new weTagDataOption('multipart/form-data', false, '')), false, '');

if (defined('FORMMAIL_VIAWEDOC') && FORMMAIL_VIAWEDOC ==1){
$this->TypeAttribute = new weTagData_typeAttribute('type', array(
new weTagDataOption('-', false, '', array($id,$name,$nameid,$method,$target,$enctype), array()),
new weTagDataOption('document', false, '', array($id,$name,$nameid,$method,$target,$doctype,$tid,$enctype), array($doctype)),
new weTagDataOption('formmail', false, '', array($id,$name,$nameid,$method,$target,$recipient,$onsuccess,$onerror,$onmailerror,$onrecipienterror,$from,$subject,$charset,$order,$required,$remove,$mimetype,$confirmmail,$forcefrom,$preconfirm,$postconfirm), array($recipient)),
new weTagDataOption('object', false, 'object', array($id,$name,$nameid,$method,$target,$categories,$classid,$parentid,$enctype), array($classid)),
new weTagDataOption('search', false, '', array($id,$name,$nameid,$method,$target), array()),
new weTagDataOption('shopliste', false, '', array($id,$nameid,$method,$target), array())
), false, '');
} else {
$this->TypeAttribute = new weTagData_typeAttribute('type', array(
new weTagDataOption('-', false, '', array($id,$name,$nameid,$method,$target,$enctype), array()),
new weTagDataOption('document', false, '', array($id,$name,$nameid,$method,$target,$doctype,$tid,$enctype), array($doctype)),
new weTagDataOption('formmail', false, '', array($name,$nameid,$method,$target,$recipient,$onsuccess,$onerror,$onmailerror,$onrecipienterror,$from,$subject,$charset,$order,$required,$remove,$mimetype,$confirmmail,$forcefrom,$preconfirm,$postconfirm), array($recipient)),
new weTagDataOption('object', false, 'object', array($id,$name,$nameid,$method,$target,$categories,$classid,$parentid,$enctype), array($classid)),
new weTagDataOption('search', false, '', array($id,$name,$nameid,$method,$target), array()),
new weTagDataOption('shopliste', false, '', array($id,$nameid,$method,$target), array())
), false, '');
}


$this->Attributes=array($id,$name,$nameid,$method,$target,$recipient,$onsuccess,$onerror,$onmailerror,$from,$subject,$charset,$order,$required,$remove,$mimetype,
$confirmmail,$forcefrom,$preconfirm,$postconfirm,$doctype,$categories,$tid,$classid,$parentid,$xml,$enctype);

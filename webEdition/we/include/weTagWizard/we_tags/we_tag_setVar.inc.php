<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('object', false, ''), new weTagDataOption('document', false, ''), new weTagDataOption('sessionfield', false, '')), true, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', true, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_selectAttribute('from', array(new weTagDataOption('request', false, ''),new weTagDataOption('post', false, ''),new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('object', false, ''), new weTagDataOption('document', false, ''), new weTagDataOption('sessionfield', false, ''), new weTagDataOption('calendar', false, ''), new weTagDataOption('listview', false, ''), new weTagDataOption('block', false, ''), new weTagDataOption('listdir', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('namefrom', false, '');
$this->Attributes[] = new weTagData_selectAttribute('typefrom', array(new weTagDataOption('text', false, ''), new weTagDataOption('date', false, ''), new weTagDataOption('img', false, ''), new weTagDataOption('flashmovie', false, ''), new weTagDataOption('href', false, ''), new weTagDataOption('link', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('binary', false, ''), new weTagDataOption('float', false, ''), new weTagDataOption('int', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('propertyto', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('propertyfrom', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('formnameto', false, '');
$this->Attributes[] = new weTagData_textAttribute('formnamefrom', false, '');
$this->Attributes[] = new weTagData_selectAttribute('striptags', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

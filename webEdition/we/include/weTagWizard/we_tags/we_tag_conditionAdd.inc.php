<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('field', true, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('compare', array(new weTagDataOption('=', false, ''), new weTagDataOption('!=', false, ''), new weTagDataOption('&lt;', false, ''), new weTagDataOption('&gt;', false, ''), new weTagDataOption('&lt;=', false, ''), new weTagDataOption('&gt;=', false, ''), new weTagDataOption('LIKE', false, '')), false,false, '');
$this->Attributes[] = new weTagData_textAttribute('var', false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('global', false, ''), new weTagDataOption('request', false, ''), new weTagDataOption('sessionfield', false, ''), new weTagDataOption('document', false, ''), new weTagDataOption('now', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('property', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('exactmatch', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');

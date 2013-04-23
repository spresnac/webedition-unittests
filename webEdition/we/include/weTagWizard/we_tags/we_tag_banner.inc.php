<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->Module = 'banner';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);


$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('width', false, '');
$this->Attributes[] = new weTagData_textAttribute('height', false, '');
$this->Attributes[] = new weTagData_textAttribute('paths', false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('js', false, ''), new weTagDataOption('iframe', false, ''), new weTagDataOption('cookie', false, ''), new weTagDataOption('pixel', false, '')), false, '');
$this->Attributes[] = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false,false, '');
$this->Attributes[] = new weTagData_selectAttribute('link', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('clickscript', false, '');
$this->Attributes[] = new weTagData_textAttribute('getscript', false, '');
$this->Attributes[] = new weTagData_textAttribute('page', false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

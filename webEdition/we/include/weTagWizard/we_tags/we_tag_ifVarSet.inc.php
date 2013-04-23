<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('href', false, ''), new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('sessionfield', false, ''), new weTagDataOption('sum', false, ''), new weTagDataOption('shopField', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('object', false, ''), new weTagDataOption('document', false, ''), new weTagDataOption('top', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('property', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('formname', false, '');
$this->Attributes[] = new weTagData_textAttribute('shopname', false, 'shop');

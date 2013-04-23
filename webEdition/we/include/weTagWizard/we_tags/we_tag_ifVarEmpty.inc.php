<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('document', false, ''), new weTagDataOption('object', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''),new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('sessionfield', false, ''), new weTagDataOption('href', false, ''), new weTagDataOption('multiobject', false, 'object')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('property', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('formname', false, '');

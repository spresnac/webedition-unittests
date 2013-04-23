<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('global', false, ''), new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('document', false, ''), new weTagDataOption('property', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('operator', array(new weTagDataOption('equal', false, ''), new weTagDataOption('less', false, ''), new weTagDataOption('less|equal', false, ''), new weTagDataOption('greater', false, ''), new weTagDataOption('greater|equal', false, ''), new weTagDataOption('contains', false, '')), false, '');

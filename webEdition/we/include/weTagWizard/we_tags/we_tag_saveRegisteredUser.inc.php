<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('userexists', false, '');
$this->Attributes[] = new weTagData_textAttribute('userempty', false, '');
$this->Attributes[] = new weTagData_textAttribute('passempty', false, '');
$this->Attributes[] = new weTagData_selectAttribute('register', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('protected', false, '');
$this->Attributes[] = new weTagData_selectAttribute('changesessiondata', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->Module = 'banner';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_selectAttribute('showpath', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('rootdir', false, '');
$this->Attributes[] = new weTagData_textAttribute('firstentry', false, '');
$this->Attributes[] = new weTagData_selectAttribute('submitonchange', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('customer', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');

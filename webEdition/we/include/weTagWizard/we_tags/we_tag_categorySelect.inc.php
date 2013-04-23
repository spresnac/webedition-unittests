<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('request', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showpath', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('rootdir', false, '');
$this->Attributes[] = new weTagData_textAttribute('firstentry', false, '');
$this->Attributes[] = new weTagData_selectAttribute('multiple', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('indent', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

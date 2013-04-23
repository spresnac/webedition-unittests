<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('id', false, ''), new weTagDataOption('name', false, '')), true, '');
$this->Attributes[] = new weTagData_textAttribute('mandatory', false, '');
$this->Attributes[] = new weTagData_textAttribute('email', false, '');
$this->Attributes[] = new weTagData_textAttribute('password', false, '');
$this->Attributes[] = new weTagData_textAttribute('onError', false, '');
$this->Attributes[] = new weTagData_textAttribute('jsIncludePath', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

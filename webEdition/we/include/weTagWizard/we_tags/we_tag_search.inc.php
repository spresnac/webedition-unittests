<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('textinput', false, ''), new weTagDataOption('textarea', false, ''), new weTagDataOption('print', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_textAttribute('size', false, '');
$this->Attributes[] = new weTagData_textAttribute('maxlength', false, '');
$this->Attributes[] = new weTagData_textAttribute('cols', false, '');
$this->Attributes[] = new weTagData_textAttribute('rows', false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

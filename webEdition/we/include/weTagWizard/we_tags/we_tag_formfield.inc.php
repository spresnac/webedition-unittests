<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('textinput', false, ''), new weTagDataOption('textarea', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('radio', false, ''), new weTagDataOption('checkbox', false, ''), new weTagDataOption('country', false, ''), new weTagDataOption('language', false, ''), new weTagDataOption('file', false, '')), false,true, '');
$this->Attributes[] = new weTagData_textAttribute('attribs', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

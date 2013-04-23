<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('prefix', false, '');
$this->Attributes[] = new weTagData_textAttribute('suffix', false, '');
$this->Attributes[] = new weTagData_textAttribute('delimiter', false, '');

//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

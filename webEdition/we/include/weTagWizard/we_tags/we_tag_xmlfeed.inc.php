<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_textAttribute('url', true, '');
$this->Attributes[] = new weTagData_textAttribute('refresh', false, '');
$this->Attributes[] = new weTagData_textAttribute('timeout', false, '');

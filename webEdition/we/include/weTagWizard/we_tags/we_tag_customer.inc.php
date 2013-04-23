<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('id', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('size', false, 'customer');
$this->Attributes[] = new weTagData_textAttribute('condition', false, 'customer');

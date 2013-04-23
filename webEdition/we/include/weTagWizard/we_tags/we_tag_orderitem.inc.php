<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';


$this->Attributes[] = new weTagData_textAttribute('id', false, '');

$this->Attributes[] = new weTagData_textAttribute('orderid', false, '');
$this->Attributes[] = new weTagData_textAttribute('condition', false, '');

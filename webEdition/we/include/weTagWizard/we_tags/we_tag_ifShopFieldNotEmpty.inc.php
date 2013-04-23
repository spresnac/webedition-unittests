<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_selectAttribute('reference', array(new weTagDataOption('article', false, ''), new weTagDataOption('cart', false, '')), true, '');
$this->Attributes[] = new weTagData_textAttribute('shopname', true, '');

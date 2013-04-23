<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('shopname', true, '');
$this->Attributes[] = new weTagData_selectAttribute('deleteshoponlogout', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('deleteshop', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

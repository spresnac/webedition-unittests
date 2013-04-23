<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('id', true, '');
$this->Attributes[] = new weTagData_textAttribute('show', false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top', false, ''), new weTagDataOption('self', false, '')), false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

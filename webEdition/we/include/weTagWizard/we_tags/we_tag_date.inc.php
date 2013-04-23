<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('js', false, ''), new weTagDataOption('php', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('format', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

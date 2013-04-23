<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'folder', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('index', false, '');
$this->Attributes[] = new weTagData_textAttribute('field', false, '');
$this->Attributes[] = new weTagData_textAttribute('dirfield', false, '');
$this->Attributes[] = new weTagData_textAttribute('order', false, '');
$this->Attributes[] = new weTagData_selectAttribute('desc', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

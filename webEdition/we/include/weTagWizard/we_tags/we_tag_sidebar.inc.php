<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = g_l('weTag','[' . $tagName . '][defaultvalue]', true);

if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, '', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('file', false, '');
$this->Attributes[] = new weTagData_textAttribute('url', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('width', array(new weTagDataOption('100', false, ''), new weTagDataOption('150', false, ''), new weTagDataOption('200', false, ''), new weTagDataOption('250', false, ''), new weTagDataOption('300', false, ''), new weTagDataOption('350', false, ''), new weTagDataOption('400', false, '')), false,true, '');

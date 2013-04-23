<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('defined', array(new weTagDataOption('UTF-8', false, ''), new weTagDataOption('ISO-8859-1', false, ''), new weTagDataOption('ISO-8859-2', false, ''), new weTagDataOption('ISO-8859-3', false, ''), new weTagDataOption('ISO-8859-4', false, ''), new weTagDataOption('ISO-8859-5', false, ''), new weTagDataOption('ISO-8859-6', false, ''), new weTagDataOption('ISO-8859-7', false, ''), new weTagDataOption('ISO-8859-8', false, ''), new weTagDataOption('ISO-8859-9', false, ''), new weTagDataOption('ISO-8859-10', false, ''), new weTagDataOption('ISO-8859-11', false, ''), new weTagDataOption('ISO-8859-13', false, ''), new weTagDataOption('ISO-8859-14', false, ''), new weTagDataOption('ISO-8859-15', false, ''), new weTagDataOption('Windows-1251', false, ''), new weTagDataOption('Windows-1252', false, '')), false,true, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

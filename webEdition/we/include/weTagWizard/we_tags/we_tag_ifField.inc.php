<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('text', false, ''), new weTagDataOption('date', false, ''), new weTagDataOption('img', false, ''), new weTagDataOption('flashmovie', false, ''), new weTagDataOption('href', false, ''), new weTagDataOption('link', false, ''), new weTagDataOption('day', false, ''), new weTagDataOption('dayname', false, ''), new weTagDataOption('month', false, ''), new weTagDataOption('monthname', false, ''), new weTagDataOption('year', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('binary', false, ''), new weTagDataOption('float', false, ''), new weTagDataOption('int', false, ''), new weTagDataOption('shopVat', false, ''), new weTagDataOption('checkbox', false, '')), true, '');
$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('operator', array(new weTagDataOption('equal', false, ''), new weTagDataOption('less', false, ''), new weTagDataOption('less|equal', false, ''), new weTagDataOption('greater', false, ''), new weTagDataOption('greater|equal', false, ''), new weTagDataOption('contains', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('striphtml', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('usekey', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

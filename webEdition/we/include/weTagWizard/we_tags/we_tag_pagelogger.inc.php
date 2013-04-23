<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('standard', false, ''), new weTagDataOption('robot', false, ''), new weTagDataOption('fileserver', false, ''), new weTagDataOption('downloads', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('ssl', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('websitename', false, '');
$this->Attributes[] = new weTagData_selectAttribute('trackname', array(new weTagDataOption('WE_PATH', false, ''), new weTagDataOption('WE_TITLE', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('category', false, '');
$this->Attributes[] = new weTagData_selectAttribute('order', array(new weTagDataOption('FILENAME', false, ''), new weTagDataOption('FILETITLE', false, ''), new weTagDataOption('FILESIZE', false, ''), new weTagDataOption('DOWNLOADS', false, ''), new weTagDataOption('LASTDOWNLOAD', false, ''), new weTagDataOption('SHORTDESC', false, ''), new weTagDataOption('LONGDESC', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('desc', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('rows', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

<?php
$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module='voting';

$this->Attributes[] = new weTagData_textAttribute('id', false, '');
$this->Attributes[] = new weTagData_selectAttribute('allowredirect', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('writeto', array(new weTagDataOption('voting', false, ''), new weTagDataOption('session', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('deletesessiondata', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('additionalfields', false, '');

<?php
$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module='voting';

$this->Attributes[] = new weTagData_textAttribute('firstentry', false, '');
$this->Attributes[] = new weTagData_selectAttribute('submitonchange', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('reload', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');

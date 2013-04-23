<?php
$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Groups[] = 'if_tags';
$this->Module='voting';

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('error', false, ''), new weTagDataOption('revote', false, ''), new weTagDataOption('active', false, ''), new weTagDataOption('forbidden', false, '')), false, '');

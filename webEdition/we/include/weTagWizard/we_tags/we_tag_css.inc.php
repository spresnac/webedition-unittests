<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/css', true, ''); }
$this->Attributes[] = new weTagData_selectAttribute('rel', array(new weTagDataOption('stylesheet', false, ''), new weTagDataOption('alternate stylesheet', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('title', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('media', array(new weTagDataOption('all', false, ''), new weTagDataOption('braille', false, ''), new weTagDataOption('embossed', false, ''), new weTagDataOption('handheld', false, ''), new weTagDataOption('print', false, ''), new weTagDataOption('projection', false, ''), new weTagDataOption('screen', false, ''), new weTagDataOption('speech', false, ''), new weTagDataOption('tty', false, ''), new weTagDataOption('tv', false, '')), false,false, '');
$this->Attributes[] = new weTagData_selectAttribute('applyto', array(new weTagDataOption('all', false, ''), new weTagDataOption('wysiwyg', false, ''), new weTagDataOption('around', false, '')),false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

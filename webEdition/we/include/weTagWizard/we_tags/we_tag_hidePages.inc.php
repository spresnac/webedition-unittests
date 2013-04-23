<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('pages', array(new weTagDataOption('all', false, ''), new weTagDataOption('properties', false, ''), new weTagDataOption('edit', false, ''), new weTagDataOption('information', false, ''), new weTagDataOption('preview', false, ''), new weTagDataOption('validation', false, ''), new weTagDataOption('customer', false, ''), new weTagDataOption('versions', false, ''), new weTagDataOption('schedpro', false, ''), new weTagDataOption('variants', false, '')), false,true, '');

<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_selectAttribute('reference', array(new weTagDataOption('article', false, ''), new weTagDataOption('cart', false, '')), true, '');
$this->Attributes[] = new weTagData_textAttribute('shopname', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('checkbox', false, ''), new weTagDataOption('choice', false, ''), new weTagDataOption('hidden', false, ''), new weTagDataOption('print', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('textarea', false, ''), new weTagDataOption('textinput', false, ''), new weTagDataOption('radio', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('value', false, '');
$this->Attributes[] = new weTagData_textAttribute('values', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('checked', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false,false, '');
$this->Attributes[] = new weTagData_choiceAttribute('mode', array(new weTagDataOption('add', false, '')), false,false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');

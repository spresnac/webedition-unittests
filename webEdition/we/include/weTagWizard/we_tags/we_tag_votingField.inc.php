<?php
$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module='voting';

$this->Attributes[] = new weTagData_selectAttribute('name', array(new weTagDataOption('question', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('result', false, ''), new weTagDataOption('id', false, ''), new weTagDataOption('date', false, '')), true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('text', false, ''), new weTagDataOption('radio', false, ''), new weTagDataOption('checkbox', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('count', false, ''), new weTagDataOption('percent', false, ''), new weTagDataOption('total', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('voting', false, ''),new weTagDataOption('textinput', false, ''),new weTagDataOption('textarea', false, ''), new weTagDataOption('image', false, ''),new weTagDataOption('media', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('format', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('num_format', array(new weTagDataOption('german', false, ''), new weTagDataOption('french', false, ''), new weTagDataOption('english', false, ''), new weTagDataOption('swiss', false, '')), false,false, '');
$this->Attributes[] = new weTagData_textAttribute('precision', false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');

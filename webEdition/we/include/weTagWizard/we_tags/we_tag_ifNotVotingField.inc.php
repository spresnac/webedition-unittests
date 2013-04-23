<?php
$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Groups[] = 'if_tags';
$this->Module='voting';

$this->Attributes[] = new weTagData_selectAttribute('name', array(new weTagDataOption('question', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('result', false, ''), new weTagDataOption('id', false, ''), new weTagDataOption('date', false, '')), true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('text', false, ''), new weTagDataOption('radio', false, ''), new weTagDataOption('checkbox', false, ''), new weTagDataOption('select', false, ''), new weTagDataOption('count', false, ''), new weTagDataOption('percent', false, ''), new weTagDataOption('total', false, ''), new weTagDataOption('answer', false, ''), new weTagDataOption('voting', false, ''),new weTagDataOption('textinput', false, ''),new weTagDataOption('textarea', false, ''), new weTagDataOption('image', false, ''),new weTagDataOption('media', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('match', false, '');
$this->Attributes[] = new weTagData_selectAttribute('operator', array(new weTagDataOption('equal', false, ''), new weTagDataOption('less', false, ''), new weTagDataOption('less|equal', false, ''), new weTagDataOption('greater', false, ''), new weTagDataOption('greater|equal', false, ''), new weTagDataOption('contains', false, '')), false, '');

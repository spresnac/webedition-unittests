<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'navigation_tags';
$this->Module = 'navigation';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<a href="<we:navigationField name="href" />"><we:navigationField name="text" /></a><br />';

$this->Attributes[] = new weTagData_textAttribute('navigationname', false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('folder', false, ''), new weTagDataOption('item', false, '')), true, '');
$this->Attributes[] = new weTagData_textAttribute('level', false, '');
$this->Attributes[] = new weTagData_selectAttribute('current', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_choiceAttribute('position', array(new weTagDataOption('first', false, ''), new weTagDataOption('odd', false, ''), new weTagDataOption('even', false, ''), new weTagDataOption('last', false, '')), false,false, '');

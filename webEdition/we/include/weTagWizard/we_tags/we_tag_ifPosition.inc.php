<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('type', array(new weTagDataOption('block', false, ''), new weTagDataOption('linklist', false, ''), new weTagDataOption('listdir', false, ''), new weTagDataOption('listview', false, '')), true, false, '');
$this->Attributes[] = new weTagData_choiceAttribute('position', array(
		new weTagDataOption('first', false, ''),
		new weTagDataOption('last', false, ''),
		new weTagDataOption('odd', false, ''),
		new weTagDataOption('even', false, '')
		), true, true, '');
$this->Attributes[] = new weTagData_textAttribute('reference', false, '');
$this->Attributes[] = new weTagData_selectAttribute('operator', array(
		new weTagDataOption('equal', false, ''),
		new weTagDataOption('less', false, ''),
		new weTagDataOption('less|equal', false, ''),
		new weTagDataOption('greater', false, ''),
		new weTagDataOption('greater|equal', false, ''),
		new weTagDataOption('every', false, ''),
		), false, '');

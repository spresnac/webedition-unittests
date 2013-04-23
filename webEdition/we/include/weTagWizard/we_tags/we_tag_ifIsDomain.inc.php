<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('domain', true, '');
$this->Attributes[] = new weTagData_selectAttribute('matchType', array(
		new weTagDataOption('exact'),
		new weTagDataOption('contains'),
		new weTagDataOption('front'),
		new weTagDataOption('back'),
		), false, '');
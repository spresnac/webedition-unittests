<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('img', false, ''), new weTagDataOption('flashmovie', false, ''), new weTagDataOption('quicktime', false, ''), new weTagDataOption('binary', false, ''), new weTagDataOption('href', false, ''), new weTagDataOption('object', false, ''), new weTagDataOption('multiobject', false, ''), new weTagDataOption('calendar', false, ''), new weTagDataOption('checkbox', false, ''),
 new weTagDataOption('int', false, ''), new weTagDataOption('float', false, '')
), false, '');

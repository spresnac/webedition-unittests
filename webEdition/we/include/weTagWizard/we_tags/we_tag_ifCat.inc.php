<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_multiSelectorAttribute('categories',CATEGORY_TABLE, '', 'Path', true, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('listview', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('parent', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

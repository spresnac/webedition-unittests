<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined("TEMPLATES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',TEMPLATES_TABLE, '', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('path', false, '');
if(defined("TEMPLATES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('parentid',TEMPLATES_TABLE, 'folder', false, ''); }

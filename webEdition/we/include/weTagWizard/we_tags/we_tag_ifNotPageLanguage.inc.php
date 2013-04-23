<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$locales = array();
foreach($GLOBALS["weFrontendLanguages"] as $lv){
	$locales[] = new weTagDataOption($lv, false, '');
}

$this->Attributes[] = new weTagData_choiceAttribute('match',$locales, false,true, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top', false, ''), new weTagDataOption('self', false, '')), false, '');

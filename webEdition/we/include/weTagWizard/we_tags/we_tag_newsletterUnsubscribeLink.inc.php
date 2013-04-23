<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'if_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined("FILE_TABLE")) {
$this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', true, '');
$this->Attributes[] = new weTagData_selectAttribute('plain', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

}

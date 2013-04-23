<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'if_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = g_l('weTag','[' . $tagName . '][defaultvalue]', true);

$this->Attributes[] = new weTagData_selectAttribute('plain', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'newsletter');

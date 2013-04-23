<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined("TEMPLATES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('tid',TEMPLATES_TABLE, 'text/weTmpl', true, ''); }
$this->Attributes[] = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false,false, '');
$this->Attributes[] = new weTagData_selectAttribute('link', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top', false, ''), new weTagDataOption('self', false, '')), false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, ''); }
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

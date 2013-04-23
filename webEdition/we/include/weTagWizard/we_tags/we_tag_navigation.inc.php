<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'navigation_tags';
$this->Module = 'navigation';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('navigationname', false, '');
if(defined("NAVIGATION_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('parentid',NAVIGATION_TABLE, 'weNavigation', false, ''); }
if(defined("NAVIGATION_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',NAVIGATION_TABLE, 'weNavigation', false, ''); }
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

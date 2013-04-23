<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

if(defined('WE_SHOP_VAT_TABLE')){
	$this->Attributes[] = new weTagData_sqlRowAttribute('id',WE_SHOP_VAT_TABLE, true, 'id', 'text', 'text', '');
}

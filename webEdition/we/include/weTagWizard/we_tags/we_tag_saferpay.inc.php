<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_textAttribute('shopname', true, '');
$this->Attributes[] = new weTagData_textAttribute('pricename', true, '');
$this->Attributes[] = new weTagData_selectAttribute('netprices', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('usevat', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

$this->Attributes[] = new weTagData_textAttribute('languagecode', false, '');
$this->Attributes[] = new weTagData_textAttribute('shipping', false, '');
$this->Attributes[] = new weTagData_textAttribute('shippingisnet', false, '');
$this->Attributes[] = new weTagData_textAttribute('shippingvatrate', false, '');

if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('434', 'onsuccess',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('435', 'onfailure',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('436', 'onabortion',FILE_TABLE, 'text/webedition', false, ''); }

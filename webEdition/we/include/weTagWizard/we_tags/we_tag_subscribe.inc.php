<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'if_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$size = new weTagData_textAttribute('size', false, '');
$maxlength = new weTagData_textAttribute('maxlength', false, '');
$value= new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$class = new weTagData_textAttribute('class', false, '');
$style = new weTagData_textAttribute('style', false, '');
$onchange = new weTagData_textAttribute('onchange', false, '');
$checked = new weTagData_selectAttribute('checked', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
		new weTagDataOption('email', false, 'newsletter', array($size, $maxlength, $value, $class, $style, $onchange), array()),
		new weTagDataOption('htmlCheckbox', false, 'newsletter', array($class, $style, $checked), array()),
		new weTagDataOption('htmlSelect', false, 'newsletter', array($value, $values, $class, $style), array()),
		new weTagDataOption('firstname', false, 'newsletter', array($size, $maxlength, $value, $class, $style, $onchange), array()),
		new weTagDataOption('lastname', false, 'newsletter', array($size, $maxlength, $value, $class, $style, $onchange), array()),
		new weTagDataOption('salutation', false, 'newsletter', array($size, $maxlength, $value, $values, $class, $style, $onchange), array()),
		new weTagDataOption('title', false, 'newsletter', array($size, $maxlength, $value, $values, $class, $style, $onchange), array()),
		new weTagDataOption('listCheckbox', false, 'newsletter', array($class, $style, $checked), array()),
		new weTagDataOption('listSelect', false, 'newsletter', array($size, $values, $class, $style), array())), false, '');

$this->Attributes = array($size,$maxlength,$value,$values,$class,$style,$onchange,$checked,$xml);
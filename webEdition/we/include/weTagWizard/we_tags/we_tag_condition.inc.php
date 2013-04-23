<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<we:conditionAdd field="Type" var="type" compare="="/>';

$this->Attributes[] = new weTagData_textAttribute('name', false, '');

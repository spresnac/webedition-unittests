<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('width', true, '');
$this->Attributes[] = new weTagData_textAttribute('height', true, '');
$this->Attributes[] = new weTagData_textAttribute('maxlength', false, '');
$this->Attributes[] = new weTagData_textAttribute('path', false, '');
$this->Attributes[] = new weTagData_selectAttribute('subset', array(new weTagDataOption('alphanum', false, ''), new weTagDataOption('alpha', false, ''), new weTagDataOption('num', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('skip', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('fontcolor', array(new weTagDataOption('#000000', false, ''), new weTagDataOption('#ffffff', false, ''), new weTagDataOption('#ff0000', false, ''), new weTagDataOption('#00ff00', false, ''), new weTagDataOption('#0000ff', false, ''), new weTagDataOption('#ffff00', false, ''), new weTagDataOption('#ff00ff', false, ''), new weTagDataOption('#00ffff', false, '')), false,true, '');
$this->Attributes[] = new weTagData_textAttribute('fontsize', false, '');
$this->Attributes[] = new weTagData_choiceAttribute('bgcolor', array(new weTagDataOption('#ffffff', false, ''), new weTagDataOption('#cccccc', false, ''), new weTagDataOption('#888888', false, '')), false,false, '');
$this->Attributes[] = new weTagData_selectAttribute('transparent', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, '')), false, '');
$this->Attributes[] = new weTagData_choiceAttribute('style', array(new weTagDataOption('strikeout', false, ''), new weTagDataOption('fullcircle', false, ''), new weTagDataOption('fullrectangle', false, ''), new weTagDataOption('outlinecircle', false, ''), new weTagDataOption('outlinerectangle', false, '')), false,true, '');
$this->Attributes[] = new weTagData_choiceAttribute('stylecolor', array(new weTagDataOption('#cccccc', false, ''), new weTagDataOption('#ff0000', false, ''), new weTagDataOption('#00ff00', false, ''), new weTagDataOption('#0000ff', false, ''), new weTagDataOption('#00ffff', false, ''), new weTagDataOption('#ff00ff', false, ''), new weTagDataOption('#ffff00', false, '')), false,true, '');
$this->Attributes[] = new weTagData_textAttribute('angle', false, '');
$this->Attributes[] = new weTagData_selectAttribute('align', array(new weTagDataOption('random', false, ''), new weTagDataOption('center', false, ''), new weTagDataOption('left', false, ''), new weTagDataOption('right', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('valign', array(new weTagDataOption('random', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('middle', false, ''), new weTagDataOption('bottom', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('font', false, '');
$this->Attributes[] = new weTagData_textAttribute('fontpath', false, '');
$this->Attributes[] = new weTagData_selectAttribute('case', array(new weTagDataOption('mix', false, ''), new weTagDataOption('upper', false, ''), new weTagDataOption('lower', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('gif', false, ''), new weTagDataOption('jpg', false, ''), new weTagDataOption('png', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('stylenumber', false, '');
$this->Attributes[] = new weTagData_textAttribute('alt', false, '');

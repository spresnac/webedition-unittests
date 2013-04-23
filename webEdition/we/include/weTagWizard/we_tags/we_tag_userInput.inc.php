<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$property = new weTagData_selectAttribute('property', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$checked = new weTagData_selectAttribute('checked', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$editable = new weTagData_selectAttribute('editable', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$removefirstparagraph = new weTagData_selectAttribute('removefirstparagraph', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$size = new weTagData_textAttribute('size', false, '');
$maxlength = new weTagData_textAttribute('maxlength', false, '');
$format = new weTagData_textAttribute('format', false, '');
$value = new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$hidden = new weTagData_selectAttribute('hidden', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$currentdate = new weTagData_selectAttribute('currentdate', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$cols = new weTagData_textAttribute('cols', false, '');
$rows = new weTagData_textAttribute('rows', false, '');
$pure = new weTagData_selectAttribute('pure', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$autobr = new weTagData_selectAttribute('autobr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$width = new weTagData_textAttribute('width', false, '');
$height = new weTagData_textAttribute('height', false, '');
$bgcolor = new weTagData_textAttribute('bgcolor', false, '');
$class = new weTagData_textAttribute('class', false, '');
$style = new weTagData_textAttribute('style', false, '');
$classes = new weTagData_textAttribute('classes', false, '');
$hideautobr = new weTagData_selectAttribute('hideautobr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$wysiwyg = new weTagData_selectAttribute('wysiwyg', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$buttonpos = new weTagData_choiceAttribute('buttonpos', array(new weTagDataOption('top', false, ''), new weTagDataOption('bottom', false, '')), false,false, '');
$commands = new weTagData_choiceAttribute('commands', array(new weTagDataOption('absolute', false, ''), new weTagDataOption('acronym', false, ''), new weTagDataOption('anchor', false, ''), new weTagDataOption('applystyle', false, ''), new weTagDataOption('backcolor', false, ''), new weTagDataOption('blockquote', false, ''), new weTagDataOption('bold', false, ''), new weTagDataOption('caption', false, ''), new weTagDataOption('cite', false, ''), new weTagDataOption('color', false, ''), new weTagDataOption('copy', false, ''), new weTagDataOption('copypaste', false, ''), new weTagDataOption('createlink', false, ''), new weTagDataOption('cut', false, ''), new weTagDataOption('decreasecolspan', false, ''), new weTagDataOption('del', false, ''), new weTagDataOption('deletecol', false, ''), new weTagDataOption('deleterow', false, ''), new weTagDataOption('editcell', false, ''), new weTagDataOption('editsource', false, ''), new weTagDataOption('edittable', false, ''), new weTagDataOption('fontname', false, ''), new weTagDataOption('fontsize', false, ''), new weTagDataOption('forecolor', false, ''), new weTagDataOption('formatblock', false, ''), new weTagDataOption('fullscreen', false, ''), new weTagDataOption('hr', false, ''), new weTagDataOption('importrtf', false, ''), new weTagDataOption('increasecolspan', false, ''), new weTagDataOption('indent', false, ''), new weTagDataOption('ins', false, ''), new weTagDataOption('insertbreak', false, ''), new weTagDataOption('insertcolumnleft', false, ''), new weTagDataOption('insertcolumnright', false, ''), new weTagDataOption('insertdate', false, ''), new weTagDataOption('inserthorizontalrule', false, ''), new weTagDataOption('insertlayer', false, ''), new weTagDataOption('insertimage', false, ''), new weTagDataOption('insertorderedlist', false, ''), new weTagDataOption('insertrowabove', false, ''), new weTagDataOption('insertrowbelow', false, ''), new weTagDataOption('insertspecialchar', false, ''), new weTagDataOption('inserttable', false, ''), new weTagDataOption('inserttime', false, ''), new weTagDataOption('insertunorderedlist', false, ''), new weTagDataOption('italic', false, ''), new weTagDataOption('justify', false, ''), new weTagDataOption('justifycenter', false, ''), new weTagDataOption('justifyfull', false, ''), new weTagDataOption('justifyleft', false, ''), new weTagDataOption('justifyright', false, ''), new weTagDataOption('lang', false, ''), new weTagDataOption('link', false, ''), new weTagDataOption('list', false, ''), new weTagDataOption('ltr', false, ''), new weTagDataOption('movebackward', false, ''), new weTagDataOption('moveforward', false, ''), new weTagDataOption('nonbreaking', false, ''), new weTagDataOption('outdent', false, ''), new weTagDataOption('paste', false, ''), new weTagDataOption('pastetext', false, ''), new weTagDataOption('pasteword', false, ''), new weTagDataOption('prop', false, ''), new weTagDataOption('redo', false, ''), new weTagDataOption('removecaption', false, ''), new weTagDataOption('removeformat', false, ''), new weTagDataOption('removetags', false, ''), new weTagDataOption('replace', false, ''), new weTagDataOption('rtl', false, ''), new weTagDataOption('search', false, ''), new weTagDataOption('spellcheck', false, ''), new weTagDataOption('strikethrough', false, ''), new weTagDataOption('styleprops', false, ''), new weTagDataOption('subscript', false, ''), new weTagDataOption('superscript', false, ''), new weTagDataOption('underline', false, ''), new weTagDataOption('table', false, ''), new weTagDataOption('undo', false, ''), new weTagDataOption('unlink', false, ''), new weTagDataOption('visibleborders', false, '')), false,true, '');
if(defined("FILE_TABLE")) {$editorcss = new weTagData_selectorAttribute('editorcss',FILE_TABLE, 'text/css', false, '');}
$ignoredocumentcss = new weTagData_selectAttribute('ignoredocumentcss', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$fontnames = new weTagData_choiceAttribute('fontnames', array(new weTagDataOption('arial', false, ''), new weTagDataOption('courier', false, ''), new weTagDataOption('tahoma', false, ''), new weTagDataOption('times', false, ''), new weTagDataOption('verdana', false, ''), new weTagDataOption('wingdings', false, '')), false,true, '');
$parentid = new weTagData_selectorAttribute('parentid', FILE_TABLE, 'folder', true, 'customer');
$quality = new weTagData_selectAttribute('quality', array(new weTagDataOption('0', false, ''), new weTagDataOption('1', false, ''), new weTagDataOption('2', false, ''), new weTagDataOption('3', false, ''), new weTagDataOption('4', false, ''), new weTagDataOption('5', false, ''), new weTagDataOption('6', false, ''), new weTagDataOption('7', false, ''), new weTagDataOption('8', false, ''), new weTagDataOption('9', false, ''), new weTagDataOption('10', false, '')), false, 'customer');
$keepratio = new weTagData_selectAttribute('keepratio', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$maximize = new weTagData_selectAttribute('maximize', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$bordercolor = new weTagData_textAttribute('bordercolor', false, 'customer');
$checkboxstyle = new weTagData_textAttribute('checkboxstyle', false, 'customer');
$checkboxclass = new weTagData_textAttribute('checkboxclass', false, 'customer');
$inputstyle = new weTagData_textAttribute('inputstyle', false, 'customer');
$inputclass = new weTagData_textAttribute('inputclass', false, 'customer');
$checkboxtext = new weTagData_textAttribute('checkboxtext', false, 'customer');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$minyear = new weTagData_textAttribute('minyear', false, '');
$maxyear = new weTagData_textAttribute('maxyear', false, '');
$thumbnail = new weTagData_sqlRowAttribute('thumbnail',THUMBNAILS_TABLE, false, 'Name', '', '', '');

$to = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$nameto = new weTagData_textAttribute('nameto', false, '');


$this->TypeAttribute = new weTagData_typeAttribute('type', array(
new weTagDataOption('textinput', false, '', array($name, $property, $editable, $size, $maxlength, $value, $class, $style), array($name)),
 new weTagDataOption('textarea', false, '', array($name, $property, $editable, $value, $cols, $rows, $autobr, $width, $height, $bgcolor, $class, $style, $hideautobr, $wysiwyg, $wysiwyg, $buttonpos, $ignoredocumentcss, $editorcss, $commands, $fontnames, $classes), array($name)),
 new weTagDataOption('checkbox', false, '', array($name, $property, $checked, $editable), array($name)),
	new weTagDataOption('radio', false, '', array($name, $property, $checked, $editable, $value), array($name)),
 new weTagDataOption('choice', false, '', array($name, $property, $editable, $size, $maxlength, $value, $values, $class, $style), array($name)),
 new weTagDataOption('select', false, '', array($name, $property, $editable, $size, $value, $values, $class, $style), array($name)),
 new weTagDataOption('hidden', false, '', array($name, $property), array($name)),
 new weTagDataOption('print', false, '', array($name, $property, $to,$nameto), array($name)),
	new weTagDataOption('date', false, '', array($name, $property, $editable, $format, $value, $minyear, $maxyear, $hidden), array($name)),
 new weTagDataOption('password', false, '', array(array())),
 new weTagDataOption('img', false, 'customer', array($name, $editable, $size, $value, $width, $height, $thumbnail, $parentid, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('flashmovie', false, 'customer', array($name, $editable, $size, $value, $width, $height, $parentid, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('quicktime', false, 'customer', array($name, $editable, $size, $value, $width, $height, $parentid, $quality, $keepratio, $maximize, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('binary', false, 'customer', array($name, $editable, $size, $value, $parentid, $bordercolor, $checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext), array($name, $parentid)),
 new weTagDataOption('country', false, '', array($name, $size, $class, $style, $doc, $value), array($name)),
 new weTagDataOption('language', false, '', array($name, $size, $class, $style, $doc, $value), array($name))),
 true, '');

$this->Attributes = array($name, $property, $checked, $editable, $xml, $removefirstparagraph, $size, $maxlength, $format, $value, $values, $hidden, $currentdate, $cols,
	$rows, $pure, $autobr, $width, $bgcolor, $class, $style, $wysiwyg, $buttonpos, $ignoredocumentcss, $editorcss, $commands, $classes, $fontnames, $parentid, $quality, $keepratio, $maximize, $bordercolor,
	$checkboxstyle, $checkboxclass, $inputstyle, $inputclass, $checkboxtext, $doc, $minyear, $maxyear,$to,$nameto);

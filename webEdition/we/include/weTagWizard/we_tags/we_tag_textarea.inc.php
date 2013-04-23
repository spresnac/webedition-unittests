<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$name = new weTagData_textAttribute('name', true, '');
$cols = new weTagData_textAttribute('cols', false, '');
$rows = new weTagData_textAttribute('rows', false, '');
$autobr = new weTagData_selectAttribute('autobr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$importrtf = new weTagData_selectAttribute('importrtf', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$width = new weTagData_textAttribute('width', false, '');
$height = new weTagData_textAttribute('height', false, '');
$bgcolor = new weTagData_textAttribute('bgcolor', false, '');
$class = new weTagData_textAttribute('class', false, '');
if(defined("FILE_TABLE")) { $editorcss = new weTagData_selectorAttribute('editorcss',FILE_TABLE, 'text/css', false, ''); }
$ignoredocumentcss = new weTagData_selectAttribute('ignoredocumentcss', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$html = new weTagData_selectAttribute('html', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$php = new weTagData_selectAttribute('php', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$commands = new weTagData_choiceAttribute('commands', array(new weTagDataOption('absolute', false, ''), new weTagDataOption('acronym', false, ''), new weTagDataOption('anchor', false, ''), new weTagDataOption('applystyle', false, ''), new weTagDataOption('backcolor', false, ''), new weTagDataOption('blockquote', false, ''), new weTagDataOption('bold', false, ''), new weTagDataOption('caption', false, ''), new weTagDataOption('cite', false, ''), new weTagDataOption('color', false, ''), new weTagDataOption('copy', false, ''), new weTagDataOption('copypaste', false, ''), new weTagDataOption('createlink', false, ''), new weTagDataOption('cut', false, ''), new weTagDataOption('decreasecolspan', false, ''), new weTagDataOption('del', false, ''), new weTagDataOption('deletecol', false, ''), new weTagDataOption('deleterow', false, ''), new weTagDataOption('editcell', false, ''), new weTagDataOption('editsource', false, ''), new weTagDataOption('edittable', false, ''), new weTagDataOption('fontname', false, ''), new weTagDataOption('fontsize', false, ''), new weTagDataOption('forecolor', false, ''), new weTagDataOption('formatblock', false, ''), new weTagDataOption('fullscreen', false, ''), new weTagDataOption('hr', false, ''), new weTagDataOption('importrtf', false, ''), new weTagDataOption('increasecolspan', false, ''), new weTagDataOption('indent', false, ''), new weTagDataOption('ins', false, ''), new weTagDataOption('insertbreak', false, ''), new weTagDataOption('insertcolumnleft', false, ''), new weTagDataOption('insertcolumnright', false, ''), new weTagDataOption('insertdate', false, ''), new weTagDataOption('inserthorizontalrule', false, ''), new weTagDataOption('insertlayer', false, ''), new weTagDataOption('insertimage', false, ''), new weTagDataOption('insertorderedlist', false, ''), new weTagDataOption('insertrowabove', false, ''), new weTagDataOption('insertrowbelow', false, ''), new weTagDataOption('insertspecialchar', false, ''), new weTagDataOption('inserttable', false, ''), new weTagDataOption('inserttime', false, ''), new weTagDataOption('insertunorderedlist', false, ''), new weTagDataOption('italic', false, ''), new weTagDataOption('justify', false, ''), new weTagDataOption('justifycenter', false, ''), new weTagDataOption('justifyfull', false, ''), new weTagDataOption('justifyleft', false, ''), new weTagDataOption('justifyright', false, ''), new weTagDataOption('lang', false, ''), new weTagDataOption('link', false, ''), new weTagDataOption('list', false, ''), new weTagDataOption('ltr', false, ''), new weTagDataOption('movebackward', false, ''), new weTagDataOption('moveforward', false, ''), new weTagDataOption('nonbreaking', false, ''), new weTagDataOption('outdent', false, ''), new weTagDataOption('paste', false, ''), new weTagDataOption('pastetext', false, ''), new weTagDataOption('pasteword', false, ''), new weTagDataOption('prop', false, ''), new weTagDataOption('redo', false, ''), new weTagDataOption('removecaption', false, ''), new weTagDataOption('removeformat', false, ''), new weTagDataOption('removetags', false, ''), new weTagDataOption('replace', false, ''), new weTagDataOption('rtl', false, ''), new weTagDataOption('search', false, ''), new weTagDataOption('spellcheck', false, ''), new weTagDataOption('strikethrough', false, ''), new weTagDataOption('styleprops', false, ''), new weTagDataOption('subscript', false, ''), new weTagDataOption('superscript', false, ''), new weTagDataOption('underline', false, ''), new weTagDataOption('table', false, ''), new weTagDataOption('undo', false, ''), new weTagDataOption('unlink', false, ''), new weTagDataOption('visibleborders', false, '')), false,true, '');
$fontnames = new weTagData_choiceAttribute('fontnames', array(new weTagDataOption('arial', false, ''), new weTagDataOption('courier', false, ''), new weTagDataOption('tahoma', false, ''), new weTagDataOption('times', false, ''), new weTagDataOption('verdana', false, ''), new weTagDataOption('wingdings', false, '')), false,true, '');
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$abbr = new weTagData_selectAttribute('abbr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$removefirstparagraph = new weTagData_selectAttribute('removefirstparagraph',array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$inlineedit = new weTagData_selectAttribute('inlineedit', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$buttonpos = new weTagData_choiceAttribute('buttonpos', array(new weTagDataOption('top', false, ''), new weTagDataOption('bottom', false, '')), false,false, '');
$win2iso = new weTagData_selectAttribute('win2iso', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$classes = new weTagData_textAttribute('classes', false, '');
$spellcheck = new weTagData_selectAttribute('spellcheck', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'spellchecker');
$tinyparams = new weTagData_textAttribute('tinyparams', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

$this->TypeAttribute = new weTagData_typeAttribute('wysiwyg', array(
	new weTagDataOption('true', false, '', array($name,$cols,$rows,$autobr,$width,$height,$class,$bgcolor,$editorcss,$ignoredocumentcss,$htmlspecialchars,$commands,$fontnames,$abbr,$removefirstparagraph,$inlineedit,$buttonpos,$win2iso,$classes,$spellcheck,$tinyparams), array($name)),
	new weTagDataOption('false', false, '', array($name,$cols,$rows,$class,$autobr,$html,$htmlspecialchars,$php,$abbr,$spellcheck), array($name))), false, '');

$this->Attributes=array($name,$cols,$rows,$class,$autobr,$importrtf,$width,$height,$bgcolor,$editorcss,$ignoredocumentcss,$html,$htmlspecialchars,$php,$commands,$fontnames,$xml,$abbr,
	$removefirstparagraph,$inlineedit,$buttonpos,$win2iso,$classes,$spellcheck,$tinyparams);
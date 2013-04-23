<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
if(defined('CUSTOMER_TABLE')){
$name = new weTagData_sqlColAttribute('name', CUSTOMER_TABLE, true, array(), '');
$size = new weTagData_textAttribute('size', false, '');
$maxlength = new weTagData_textAttribute('maxlength', false, '');
$rows = new weTagData_textAttribute('rows', false, '');
$cols = new weTagData_textAttribute('cols', false, '');
$onchange = new weTagData_textAttribute('onchange', false, '');
$choice = new weTagData_choiceAttribute('choice', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false,false, '');
$checked = new weTagData_choiceAttribute('checked', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false,false, '');
$value = new weTagData_textAttribute('value', false, '');
$values = new weTagData_textAttribute('values', false, '');
$dateformat = new weTagData_textAttribute('dateformat', false, '');
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$id = new weTagData_textAttribute('id', false, '');
$removefirstparagraph = new weTagData_selectAttribute('removefirstparagraph', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$autofill = new weTagData_selectAttribute('autofill', array(new weTagDataOption('true', false, '')), false, '');
$parentid = new weTagData_selectorAttribute('parentid',FILE_TABLE, 'folder', false, 'customer');
$width = new weTagData_textAttribute('width', false, 'customer');
$height = new weTagData_textAttribute('height', false, 'customer');
$quality = new weTagData_selectAttribute('quality', array(new weTagDataOption('0', false, ''), new weTagDataOption('1', false, ''), new weTagDataOption('2', false, ''), new weTagDataOption('3', false, ''), new weTagDataOption('4', false, ''), new weTagDataOption('5', false, ''), new weTagDataOption('6', false, ''), new weTagDataOption('7', false, ''), new weTagDataOption('8', false, ''), new weTagDataOption('9', false, ''), new weTagDataOption('10', false, '')), false, 'customer');
$keepratio = new weTagData_selectAttribute('keepratio', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$maximize = new weTagData_selectAttribute('maximize', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$bordercolor = new weTagData_textAttribute('bordercolor', false, 'customer');
$checkboxstyle = new weTagData_textAttribute('checkboxstyle', false, 'customer');
$inputstyle = new weTagData_textAttribute('inputstyle', false, 'customer');
$checkboxclass = new weTagData_textAttribute('checkboxclass', false, 'customer');
$inputclass = new weTagData_textAttribute('inputclass', false, 'customer');
$checkboxtext = new weTagData_textAttribute('checkboxtext', false, 'customer');
$showcontrol = new weTagData_selectAttribute('showcontrol', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer');
$thumbnail = new weTagData_sqlRowAttribute('thumbnail',THUMBNAILS_TABLE, false, 'Name', '', '', '');
$ascountry = new weTagData_selectAttribute('ascountry', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$aslanguage = new weTagData_selectAttribute('aslanguage', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$outputlanguage = new weTagData_textAttribute('outputlanguage', false, '');
$languageautofill = new weTagData_selectAttribute('languageautofill', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$to = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$nameto = new weTagData_textAttribute('nameto', false, '');
$usevalue = new weTagData_selectAttribute('usevalue', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$minyear = new weTagData_textAttribute('minyear', false, '');
$maxyear = new weTagData_textAttribute('maxyear', false, '');
$pure = new weTagData_selectAttribute('pure', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$wysiwyg = new weTagData_selectAttribute('wysiwyg', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), true, '');
$autobr = new weTagData_selectAttribute('autobr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$html = new weTagData_selectAttribute('html', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$htmlspecialchars = new weTagData_selectAttribute('htmlspecialchars', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$php = new weTagData_selectAttribute('php', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$abbr = new weTagData_selectAttribute('abbr', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$spellcheck = new weTagData_selectAttribute('spellcheck', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'spellchecker');
$commands = new weTagData_choiceAttribute('commands', array(new weTagDataOption('acronym', false, ''), new weTagDataOption('anchor', false, ''), new weTagDataOption('applystyle', false, ''), new weTagDataOption('backcolor', false, ''), new weTagDataOption('bold', false, ''), new weTagDataOption('caption', false, ''), new weTagDataOption('color', false, ''), new weTagDataOption('copy', false, ''), new weTagDataOption('copypaste', false, ''), new weTagDataOption('createlink', false, ''), new weTagDataOption('cut', false, ''), new weTagDataOption('decreasecolspan', false, ''), new weTagDataOption('deletecol', false, ''), new weTagDataOption('deleterow', false, ''), new weTagDataOption('editcell', false, ''), new weTagDataOption('editsource', false, ''), new weTagDataOption('edittable', false, ''), new weTagDataOption('fontname', false, ''), new weTagDataOption('fontsize', false, ''), new weTagDataOption('forecolor', false, ''), new weTagDataOption('formatblock', false, ''), new weTagDataOption('fullscreen', false, ''), new weTagDataOption('importrtf', false, ''), new weTagDataOption('increasecolspan', false, ''), new weTagDataOption('indent', false, ''), new weTagDataOption('insertbreak', false, ''), new weTagDataOption('insertcolumnleft', false, ''), new weTagDataOption('insertcolumnright', false, ''), new weTagDataOption('inserthorizontalrule', false, ''), new weTagDataOption('insertimage', false, ''), new weTagDataOption('insertorderedlist', false, ''), new weTagDataOption('insertrowabove', false, ''), new weTagDataOption('insertrowbelow', false, ''), new weTagDataOption('insertspecialchar', false, ''), new weTagDataOption('inserttable', false, ''), new weTagDataOption('insertunorderedlist', false, ''), new weTagDataOption('italic', false, ''), new weTagDataOption('justify', false, ''), new weTagDataOption('justifycenter', false, ''), new weTagDataOption('justifyfull', false, ''), new weTagDataOption('justifyleft', false, ''), new weTagDataOption('justifyright', false, ''), new weTagDataOption('lang', false, ''), new weTagDataOption('link', false, ''), new weTagDataOption('list', false, ''), new weTagDataOption('outdent', false, ''), new weTagDataOption('paste', false, ''), new weTagDataOption('prop', false, ''), new weTagDataOption('redo', false, ''), new weTagDataOption('removecaption', false, ''), new weTagDataOption('removeformat', false, ''), new weTagDataOption('removetags', false, ''), new weTagDataOption('spellcheck', false, ''), new weTagDataOption('strikethrough', false, ''), new weTagDataOption('subscript', false, ''), new weTagDataOption('superscript', false, ''), new weTagDataOption('underline', false, ''), new weTagDataOption('table', false, ''), new weTagDataOption('undo', false, ''), new weTagDataOption('unlink', false, ''), new weTagDataOption('visibleborders', false, '')), false,true, '');
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');




$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('textinput', false, '', array($name,$size,$maxlength,$value), array($name)),
	new weTagDataOption('textarea', false, '', array($name,$rows,$cols,$value,$pure,$wysiwyg,$autobr,$html,$htmlspecialchars,$php,$abbr,$spellcheck,$commands), array($name)),
	new weTagDataOption('checkbox', false, '', array($name,$checked), array($name)),
	new weTagDataOption('radio', false, '', array($name,$checked,$value), array($name)),
	new weTagDataOption('password', false, '', array($name,$size,$maxlength,$value), array($name)),
	new weTagDataOption('hidden', false, 'customer', array($name,$value,$autofill,$languageautofill,$doc,$usevalue), array($name)),
	new weTagDataOption('print', false, '', array($name,$dateformat,$ascountry,$aslanguage,$outputlanguage,$doc,$to,$nameto), array($name)),
	new weTagDataOption('select', false, '', array($name,$size,$value,$values), array($name)),
	new weTagDataOption('choice', false, '', array($name,$size,$maxlength,$value,$values), array($name)),
	new weTagDataOption('img', false, 'customer', array($name,$value,$id,$xml,$parentid,$width,$height,$quality,$keepratio,$maximize,$bordercolor,$checkboxstyle,$inputstyle,$checkboxclass,$inputclass,$checkboxtext,$showcontrol,$thumbnail), array($name,$parentid)),
	new weTagDataOption('date', false, '', array($name,$dateformat,$minyear,$maxyear,$value), array($name)),
	new weTagDataOption('country', false, '', array($name,$size,$doc,$value), array($name)),
	new weTagDataOption('language', false, '', array($name,$size,$doc,$value), array($name))), true, '');

$this->Attributes=array($name,$size,$maxlength,$rows,$cols,$onchange,$choice,$checked,$value,$values,$dateformat,$xml,$id,$removefirstparagraph,$autofill,
$parentid,$width,$height,$quality,$keepratio,$maximize,$bordercolor,$checkboxstyle,$inputstyle,$checkboxclass,$inputclass,$checkboxtext,$showcontrol,
$thumbnail,$ascountry,$aslanguage,$outputlanguage,$languageautofill,$doc,$to,$nameto,$usevalue,$minyear,$maxyear,$pure,$wysiwyg,$autobr,$html,$htmlspecialchars,$php,$abbr,$spellcheck,$commands);
}

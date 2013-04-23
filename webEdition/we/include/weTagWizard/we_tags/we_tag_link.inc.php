<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_selectAttribute('only', array(new weTagDataOption('href', false, ''), new weTagDataOption('jsstatus', false, ''), new weTagDataOption('jsscrollbars', false, ''), new weTagDataOption('jsmenubar', false, ''), new weTagDataOption('jstoolbar', false, ''), new weTagDataOption('jsresizable', false, ''), new weTagDataOption('jslocation', false, ''), new weTagDataOption('img_id', false, ''), new weTagDataOption('type', false, ''), new weTagDataOption('ctype', false, ''), new weTagDataOption('border', false, ''), new weTagDataOption('hspace', false, ''), new weTagDataOption('vspace', false, ''), new weTagDataOption('align', false, ''), new weTagDataOption('alt', false, ''), new weTagDataOption('jsheight', false, ''), new weTagDataOption('jswidth', false, ''), new weTagDataOption('jsposx', false, ''), new weTagDataOption('id', false, ''), new weTagDataOption('text', false, ''), new weTagDataOption('title', false, ''), new weTagDataOption('accesskey', false, ''), new weTagDataOption('tabindex', false, ''), new weTagDataOption('lang', false, ''), new weTagDataOption('rel', false, ''), new weTagDataOption('obj_id', false, ''), new weTagDataOption('anchor', false, ''), new weTagDataOption('params', false, ''), new weTagDataOption('target', false, ''), new weTagDataOption('jswin', false, ''), new weTagDataOption('jscenter', false, ''), new weTagDataOption('jsposy', false, ''), new weTagDataOption('img_title', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('class', false, '');
$this->Attributes[] = new weTagData_textAttribute('style', false, '');
$this->Attributes[] = new weTagData_textAttribute('text', false, '');
$this->Attributes[] = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('imageid',FILE_TABLE, 'image/*', false, ''); }
$this->Attributes[] = new weTagData_selectAttribute('hidedirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('objectseourls', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_selectAttribute('to', array(new weTagDataOption('screen', false, ''),new weTagDataOption('request', false, ''), new weTagDataOption('post', false, ''), new weTagDataOption('get', false, ''), new weTagDataOption('global', false, ''), new weTagDataOption('session', false, ''), new weTagDataOption('top', false, ''), new weTagDataOption('self', false, ''), new weTagDataOption('sessionfield', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('nameto', false, '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');

<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$path = new weTagData_textAttribute('path', false, '');
$mailingList = new weTagData_textAttribute('mailingList', false, '');
$doubleoptin = new weTagData_selectAttribute('doubleoptin', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$expiredoubleoptin = new weTagData_textAttribute('expiredoubleoptin', false, '');
$mailid = new weTagData_selectorAttribute('mailid',FILE_TABLE, 'text/webedition', false, '');
$adminmailid = new weTagData_selectorAttribute('adminmailid',FILE_TABLE, 'text/webedition', false, '');
$subject = new weTagData_textAttribute('subject', false, '');
$adminsubject = new weTagData_textAttribute('adminsubject', false, '');
$adminemail = new weTagData_textAttribute('adminemail', false, '');
$from = new weTagData_textAttribute('from', false, '');
$id = new weTagData_selectorAttribute('id',FILE_TABLE, 'text/webedition', false, '');
$fieldGroup = new weTagData_textAttribute('fieldGroup', false, '');
$recipientCC = new weTagData_textAttribute('recipientCC', false, '');
$recipientBCC = new weTagData_textAttribute('recipientBCC', false, '');
$includeimages = new weTagData_selectAttribute('includeimages', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

$this->TypeAttribute = new weTagData_typeAttribute('type', array(
	new weTagDataOption('csv', false, '', array($path,$doubleoptin,$expiredoubleoptin,$mailid,$subject,$from,$id,$mailingList,$recipientCC,$recipientBCC,$adminmailid,$adminsubject,$adminemail,$includeimages), array($path)),
	new weTagDataOption('customer', false, 'customer', array($doubleoptin,$expiredoubleoptin,$mailid,$subject,$from,$id,$fieldGroup,$mailingList,$recipientCC,$recipientBCC,$adminmailid,$adminsubject,$adminemail,$includeimages), array()),
	new weTagDataOption('emailonly', false, '', array($doubleoptin,$expiredoubleoptin,$mailid,$subject,$from,$id,$adminmailid,$adminsubject,$adminemail,$includeimages), array($adminmailid,$adminsubject,$adminemail)) ), false, 'newsletter');


$this->Attributes=array($path,$mailingList,$doubleoptin,$expiredoubleoptin,$mailid,$subject,$adminmailid,$adminsubject,$adminemail,
	$from,$id,$fieldGroup,$recipientCC,$recipientBCC,$includeimages);

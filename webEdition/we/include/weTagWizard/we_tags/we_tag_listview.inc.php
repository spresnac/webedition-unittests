<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<we:repeat>
<we:field name="Title" alt="we_path" hyperlink="true"/>
</we:repeat>';

$MultiSelector = new weTagData_multiSelectorAttribute('MultiSelector',FILE_TABLE, '', '', false, '');
$name = new weTagData_textAttribute('name', false, '');
$doctype = new weTagData_sqlRowAttribute('doctype',DOC_TYPES_TABLE, false, 'DocType', '', '', '');
$categories = new weTagData_multiSelectorAttribute('categories',CATEGORY_TABLE, '', 'Path', false, '');
$catOr = new weTagData_selectAttribute('catOr', array(new weTagDataOption('true', false, '')), false, '');
$rows = new weTagData_textAttribute('rows', false, '');
$cols = new weTagData_textAttribute('cols', false, '');
$order_document = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('we_id', false, ''), new weTagDataOption('we_filename', false, ''), new weTagDataOption('we_creationdate', false, ''), new weTagDataOption('we_moddate', false, ''), new weTagDataOption('we_published', false, '')), false,false, '');
$order_object = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('we_id', false, ''), new weTagDataOption('we_filename', false, ''), new weTagDataOption('we_published', false, '')), false,false, '');
$order_search = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('we_id', false, ''), new weTagDataOption('we_filename', false, ''), new weTagDataOption('we_creationdate', false, ''), new weTagDataOption('Title', false, ''), new weTagDataOption('Description', false, ''), new weTagDataOption('Path', false, ''), new weTagDataOption('Text', false, ''), new weTagDataOption('DID', false, ''), new weTagDataOption('OID', false, ''), new weTagDataOption('ID', false, '')), false,false, '');
$order_category = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('ID', false, ''), new weTagDataOption('Category', false, ''), new weTagDataOption('Text', false, ''), new weTagDataOption('Path', false, '')), false,false, '');
$order_banner = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('Path', false, ''), new weTagDataOption('Clicks', false, ''), new weTagDataOption('Views', false, ''), new weTagDataOption('Rate', false, '')), false,false, '');
$order_customer = (defined('CUSTOMER_TABLE')?new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('ID', false, ''), new weTagDataOption('Username', false, ''), new weTagDataOption('Forename', false, ''), new weTagDataOption('Surname', false, '')), false,false, 'customer'):null);
$order_onlinemonitor = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('WebUserID', false, ''), new weTagDataOption('WebUserGroup', false, ''), new weTagDataOption('WebUserDescription', false, ''),new weTagDataOption('PageID', false, ''),new weTagDataOption('ObjectID', false, ''),new weTagDataOption('LastLogin', false, ''),new weTagDataOption('LastAccess', false, '')), false,false, '');
$order_languagelink = new weTagData_choiceAttribute('order', array(new weTagDataOption('random()', false, ''), new weTagDataOption('Locale', false, '')), false,false, '');
$desc = new weTagData_selectAttribute('desc', array(new weTagDataOption('true', false, '')), false, '');
$offset = new weTagData_textAttribute('offset', false, '');
$casesensitive = new weTagData_selectAttribute('casesensitive', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$classid = (defined("OBJECT_TABLE")?new weTagData_selectorAttribute('classid',OBJECT_TABLE, 'object', false, ''):null);
$condition = new weTagData_textAttribute('condition', false, '');
$triggerid = new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, '');
$seeMode = new weTagData_selectAttribute('seeMode', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$custBanner = (defined('CUSTOMER_TABLE')?new weTagData_selectAttribute('customer', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'customer'):null);
$workspaceID_document = new weTagData_multiSelectorAttribute('workspaceID',FILE_TABLE, 'folder', 'ID', false, '');
$workspaceID_object = defined('OBJECT_FILES_TABLE')?new weTagData_multiSelectorAttribute('workspaceID',OBJECT_FILES_TABLE, 'folder', 'ID', false, ''):null;
$categoryids = new weTagData_multiSelectorAttribute('categoryids',CATEGORY_TABLE, '', 'ID', false, '');
$parentid = new weTagData_selectorAttribute('parentid',CATEGORY_TABLE, '', false, '');
$parentidname = new weTagData_textAttribute('parentidname', false, '');
$contenttypes = new weTagData_choiceAttribute('contenttypes', array(new weTagDataOption('text/webedition', false, ''), new weTagDataOption('image/*', false, ''), new weTagDataOption('text/html', false, ''), new weTagDataOption('text/plain', false, ''), new weTagDataOption('text/xml', false, ''), new weTagDataOption('text/js', false, ''), new weTagDataOption('text/css', false, ''), new weTagDataOption('application/*', false, ''), new weTagDataOption('application/x-shockwave-flash', false, ''), new weTagDataOption('video/quicktime', false, '')), false,true, '');
$searchable = new weTagData_selectAttribute('searchable', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$defaultname = new weTagData_textAttribute('defaultname', false, '');
$documentid = new weTagData_selectorAttribute('documentid',FILE_TABLE, 'text/webedition', false, '');
$objectid = (defined("OBJECT_FILES_TABLE") ? new weTagData_selectorAttribute('objectid',OBJECT_FILES_TABLE, 'objectFile', false, '') : null);
$calendar = new weTagData_selectAttribute('calendar', array(new weTagDataOption('year', false, ''), new weTagDataOption('month', false, ''), new weTagDataOption('month_table', false, ''), new weTagDataOption('day', false, '')), false, '');
$datefield = new weTagData_textAttribute('datefield', false, '');
$date = new weTagData_textAttribute('date', false, '');
$weekstart = new weTagData_selectAttribute('weekstart', array(new weTagDataOption('sunday', false, ''), new weTagDataOption('monday', false, ''), new weTagDataOption('tuesday', false, ''), new weTagDataOption('wednesday', false, ''), new weTagDataOption('thursday', false, ''), new weTagDataOption('friday', false, ''), new weTagDataOption('saturday', false, '')), false, '');
$cfilter = (defined('CUSTOMER_TABLE')?new weTagData_selectAttribute('cfilter', array(new weTagDataOption('false', false, ''), new weTagDataOption('true', false, ''), new weTagDataOption('auto', false, '')), false, 'customer'):null);
$recursive = new weTagData_selectAttribute('recursive', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$docid = new weTagData_multiSelectorAttribute('docid',FILE_TABLE, 'text/webedition', 'ID', false, '');
$customer = (defined('CUSTOMER_TABLE')?new weTagData_textAttribute('customer', false, 'customer'):null);
$customers = (defined('CUSTOMER_TABLE')?new weTagData_textAttribute('customers', false, 'customer'):null);
$id = new weTagData_textAttribute('id', false, '');
$predefinedSQL = new weTagData_textAttribute('predefinedSQL', false, '');
$numorder = new weTagData_selectAttribute('numorder', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$locales = array();
foreach($GLOBALS["weFrontendLanguages"] as $lv){
	$locales[] = new weTagDataOption($lv, false, '');
}
$locales[] = new weTagDataOption('self', false, '');
$locales[] = new weTagDataOption('top', false, '');
$languages = new weTagData_choiceAttribute('languages',$locales, false,true, '');
$lastaccesslimit = new weTagData_textAttribute('lastaccesslimit', false, '');
$lastloginlimit = new weTagData_textAttribute('lastloginlimit', false, '');
$objectseourls = new weTagData_selectAttribute('objectseourls', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$hidedirindex = new weTagData_selectAttribute('hidedirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$pagelanguage = new weTagData_choiceAttribute('pagelanguage',$locales, false,true, '');
$doc = new weTagData_selectAttribute('doc', array(new weTagDataOption('self', false, ''), new weTagDataOption('top', false, '')), false, '');
$showself = new weTagData_selectAttribute('showself', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');

$this->TypeAttribute =new weTagData_typeAttribute('type', array(
	new weTagDataOption('-', false, '', array(), array()),
	new weTagDataOption('document', false, '', array($name,$doctype,$categories,$catOr,$rows,$order_document,$desc,$offset,$languages,$searchable,$workspaceID_document,$cfilter,$recursive,$customers,$contenttypes,$id,$calendar, $numorder,$categoryids,$condition,$hidedirindex), array()),
	new weTagDataOption('search', false, '', array($name,$doctype,$categories,$catOr,$languages,$rows,$order_search,$desc,$casesensitive,$classid,$workspaceID_document,$cfilter,$numorder,$triggerid,$objectseourls,$hidedirindex), array()),
	new weTagDataOption('category', false, '', array($name,$categories,$rows,$order_category,$desc,$offset,$parentid,$parentidname,$categoryids), array()),
	new weTagDataOption('object', false, '', array($name,$categories,$catOr,$rows,$order_object,$desc,$offset,$classid,$condition,$triggerid,$languages,$searchable,$workspaceID_object,$cfilter,$docid,$customers,$id,$calendar,$predefinedSQL,$categoryids,$objectseourls,$hidedirindex), array()),
	new weTagDataOption('multiobject', false, '', array($name,$categories,$catOr,$rows,$order_object,$desc,$offset,$classid,$condition,$triggerid,$languages,$searchable,$cfilter,$calendar,$objectseourls,$hidedirindex), array()),
	new weTagDataOption('banner', false, 'banner', array($name,$rows,$order_banner,$custBanner), array()),
	new weTagDataOption('shopVariant', false, '', array($name,$defaultname,$documentid,$objectid,$objectseourls,$hidedirindex), array()),
	new weTagDataOption('customer', false, 'customer', array($name,$rows,$cols,$order_customer,$desc,$offset,$condition,$docid), array()),
	new weTagDataOption('onlinemonitor', false, 'customer', array($name,$rows,$cols,$order_onlinemonitor,$desc,$offset,$condition,$docid,$lastaccesslimit,$lastloginlimit), array()),
	new weTagDataOption('languagelink', false, '', array($name,$rows,$cols,$order_languagelink,$desc,$offset,$pagelanguage,$showself,$objectseourls,$hidedirindex), array()),
	new weTagDataOption('order', false, '', array($name,$rows,$cols,$order_document,$desc,$offset,$condition,$docid), array()),
	new weTagDataOption('orderitem', false, 'shop', array($name,$rows,$cols,$order_document,$desc,$offset,$condition,$docid), array())), false, '');

$this->Attributes=array($MultiSelector,$name,$doctype,$categories,$catOr,$rows,$cols,$order_document,$order_object,$order_search,$order_category,
	$order_banner,$order_customer,$order_onlinemonitor,$order_languagelink,$desc,$offset,$casesensitive,$classid,$condition,$triggerid,$seeMode,
	$workspaceID_document,$workspaceID_object,$categoryids,$parentid,$parentidname,$contenttypes,$searchable,$defaultname,$documentid,$objectid,
	$datefield,$date,$weekstart,$cfilter,$recursive,$docid,$customer,$customers,$custBanner,$id,$calendar,$predefinedSQL,$numorder,$languages,$lastaccesslimit,
	$lastloginlimit,$objectseourls,$hidedirindex,$pagelanguage,$doc,$showself);

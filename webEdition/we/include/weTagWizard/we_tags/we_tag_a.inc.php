<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$id = new weTagData_selectorAttribute('id', FILE_TABLE, 'text/webedition', true, '');
$target = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top', false, ''), new weTagDataOption('_parent', false, ''), new weTagDataOption('_self', false, ''), new weTagDataOption('_blank', false, '')), false, false, '');
$confirm = new weTagData_textAttribute('confirm', false, '');
$button = new weTagData_selectAttribute('button', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$hrefonly = new weTagData_selectAttribute('hrefonly', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$class = new weTagData_textAttribute('class', false, '');
$style = new weTagData_textAttribute('style', false, '');
$params = new weTagData_textAttribute('params', false, '');
$hidedirindex = new weTagData_selectAttribute('hidedirindex', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$amount = new weTagData_textAttribute('amount', false, 'shop');
$delarticle = new weTagData_selectAttribute('delarticle', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$delshop = new weTagData_selectAttribute('delshop', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, 'shop');
$shopname = new weTagData_textAttribute('shopname', false, 'shop');
$editself = new weTagData_selectAttribute('editself', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$delete = new weTagData_selectAttribute('delete', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$xml = new weTagData_selectAttribute('xml', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
// = new weTagData_textAttribute( 'cachelifetime', false, '');



$this->TypeAttribute = new weTagData_typeAttribute('edit', array(
		new weTagDataOption('', false, '', array($id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex), array($id)),
		new weTagDataOption('document', false, '', array($id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex, $editself, $delete), array($id)),
		new weTagDataOption('object', false, 'object', array($id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex, $editself, $delete), array($id)),
		new weTagDataOption('shop', false, 'shop', array($id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex, $amount, $delarticle, $delshop, $shopname, ), array($id)))
	, false, '');


$this->Attributes = array($id,$target,$confirm,$button,$hrefonly,$class,$style,$params,$hidedirindex,$amount,$delarticle,$delshop,$shopname,$editself,$delete,$xml);

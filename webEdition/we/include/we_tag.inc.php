<?php

/**
 * webEdition CMS
 *
 * $Rev: 5992 $
 * $Author: mokraemer $
 * $Date: 2013-03-24 19:12:46 +0100 (Sun, 24 Mar 2013) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

function we_include_tag_file($name){
	$fn = 'we_tag_' . $name;

	// as default: all tag_functions are in this file.
	if(function_exists($fn) || $fn == 'we_tag_noCache'){
		// do noting
		return true;
	}
	if(file_exists(WE_INCLUDES_PATH . 'we_tags/' . $fn . '.inc.php')){
		include_once (WE_INCLUDES_PATH . 'we_tags/' . $fn . '.inc.php');
		return true;
	}
	if(file_exists(WE_INCLUDES_PATH . 'we_tags/custom_tags/' . $fn . '.inc.php')){
		include_once (WE_INCLUDES_PATH . 'we_tags/custom_tags/' . $fn . '.inc.php');
		return true;
	}

	$toolinc = '';
	if(weToolLookup::getToolTag($name, $toolinc, true)){
		include_once ($toolinc);
		return true;
	}
	if(strpos(trim($name), 'if') === 0){ // this ifTag does not exist
		print parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
		return false;
	}
	return parseError(sprintf(g_l('parser', '[tag_not_known]'), trim($name)));
}

/**
 * get the full name of an Attribute with applied postTagName if set
 * @param type $var
 * @return type
 */
function we_tag_getPostName($var){
	if($var && isset($GLOBALS['postTagName'])){
		return $var . $GLOBALS['postTagName'];
	}
	return $var;
}

function we_tag($name, $attribs = array(), $content = ''){
	//keep track of editmode
	$edMerk = isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : '';
	$user = weTag_getAttribute('user', $attribs);

	//make sure comment attribute is never shown
	if($name == 'setVar' || $name == 'xmlnode'){//special handling inside tag setVar and xmlnode
		$attribs = removeAttribs($attribs, array('cachelifetime', 'comment', 'user'));
		$nameTo = '';
		$to = 'screen';
	} else{
		$nameTo = weTag_getAttribute("nameto", $attribs);
		$to = weTag_getAttribute("to", $attribs, 'screen');
		$attribs = removeAttribs($attribs, array('cachelifetime', 'comment', 'to', 'nameto', 'user'));
		/* if to attribute is set, output of the tag is redirected to a variable
		 * this makes only sense if tag output is equal to non-editmode */
		if($to != 'screen'){
			$GLOBALS['we_editmode'] = false;
		}
	}

	//make a copy of the name - this copy is never touched even not inside blocks/listviews etc.
	if(isset($attribs['name'])){
		$attribs['_name_orig'] = $attribs['name'];
		$attribs['name'] = we_tag_getPostName($attribs['name']);
	}

	if($edMerk && $user && (!$_SESSION['perms']['ADMINISTRATOR'])){
		if(!in_array($_SESSION['user']['Username'], makeArrayFromCSV($user))){
			$GLOBALS['we_editmode'] = false;
		}
	}

	if(($foo = we_include_tag_file($name)) !== true){
		return $foo;
	}

	$fn = 'we_tag_' . $name;
	$foo = '';
	switch($fn){
		case 'we_tag_setVar':
			$fn($attribs, $content);
			//nothing more to do don't waste time
			return;
		default:
			$foo = $fn($attribs, $content);
	}

	$GLOBALS['we_editmode'] = $edMerk;
	return we_redirect_tagoutput($foo, $nameTo, $to);
}

### tag utility functions ###

function we_redirect_tagoutput($returnvalue, $nameTo, $to = 'screen'){
	if(isset($GLOBALS['calculate'])){
		$to = 'calculate';
	}
	switch($to){
		case 'request' :
			$_REQUEST[$nameTo] = $returnvalue;
			return null;
		case 'post' :
			$_POST[$nameTo] = $returnvalue;
			return null;
		case 'get' :
			$_GET[$nameTo] = $returnvalue;
			return null;
		case 'global' :
			$GLOBALS[$nameTo] = $returnvalue;
			return null;
		case 'session' :
			$_SESSION[$nameTo] = $returnvalue;
			return null;
		case 'top' :
			$GLOBALS['WE_MAIN_DOC_REF']->setElement($nameTo, $returnvalue);
			return null;
		case 'block' :
		case 'self' :
			$GLOBALS['we_doc']->setElement($nameTo, $returnvalue);
			return null;
		case 'sessionfield' :
			if(isset($_SESSION['webuser'][$nameTo])){
				$_SESSION['webuser'][$nameTo] = $returnvalue;
			}
			return null;
		case 'calculate':
			return we_util::std_numberformat($returnvalue);
			break;
		case 'screen':
		default:
			return $returnvalue;
	}
	return null;
}

function mta($hash, $key){
	return (isset($hash[$key]) && ($hash[$key] != '' || $key == 'alt')) ? (' ' . $key . '="' . $hash[$key] . '"') : '';
}

function printElement($code){
	if(isset($code)){
		if(strpos($code, '<?') !== FALSE){
			eval('?>' . str_replace(array('<?php', '?>'), array('<?php ', ' ?>'), $code));
		} else{
			echo $code;
		}
	}
}

/**
 * get an attribute from $attribs, and return its value according to default
 * @param string $name attributes name
 * @param array $attribs array containg the attributes
 * @param mixed $default default value
 * @param bool $isFlag determines if this is a flag (true/false -value)
 * @return mixed returns the attributes value or default if not set
 */
function weTag_getParserAttribute($name, $attribs, $default = '', $isFlag = false){
	return weTag_getAttribute($name, $attribs, $default, $isFlag, false);
}

/**
 * get an attribute from $attribs, and return its value according to default
 * @param string $name attributes name
 * @param array $attribs array containg the attributes
 * @param mixed $default default value
 * @param bool $isFlag determines if this is a flag (true/false -value)
 * @param bool $useGlobal check if attribute value is a php-variable and is found in $GLOBALS
 * @return mixed returns the attributes value or default if not set
 */
function weTag_getAttribute($name, $attribs, $default = '', $isFlag = false, $useGlobal = true){
	$value = isset($attribs[$name]) ? $attribs[$name] : '';
	$regs = array();
	if($useGlobal && !is_array($value) && preg_match('|^\\\\?\$(.+)$|', $value, $regs)){
		$value = isset($GLOBALS[$regs[1]]) ? $GLOBALS[$regs[1]] : '';
	}
	if($isFlag){
		$val = (is_string($value) ? strtolower(trim($value)) : $value);
		$ret = (bool) $default;
		$ret = $ret && !($val === 'false' || $val === 'off' || $val === '0' || $val === 0 || $val === false);
		$ret = $ret || ($val === 'true' || $val === 'on' || $val === '1' || $value === $name || $val === 1 || $val === true);
		return $ret;
	}
	$value = is_array($value) || strlen($value) ? $value : $default;

	return is_array($value) ? $value : htmlspecialchars_decode($value);
}

/*
 * @deprecated
 */

function we_getTagAttributeTagParser($name, $attribs, $default = '', $isFlag = false, $checkForFalse = false){
	t_e('deprecated', 'you use an old tag, which still uses function we_getTagAttributeTagParser, use weTag_getParserAttribute instead!');
	return weTag_getAttribute($name, $attribs, ($isFlag ? $checkForFalse : $default), $isFlag, false);
}

/*
 * @deprecated
 */

function we_getTagAttribute($name, $attribs, $default = '', $isFlag = false, $checkForFalse = false, $useGlobal = true){
	t_e('deprecated', 'you use an old tag, which still uses function we_getTagAttribute, use weTag_getAttribute instead!');
	return weTag_getAttribute($name, $attribs, ($isFlag ? $checkForFalse : $default), $isFlag, $useGlobal);
}

function makeEmptyTable($in){
	preg_match_all('/<[^>]+>/i', $in, $result, PREG_SET_ORDER);

	$out = '';
	foreach($result as $res){
		$tag = $res[0];

		if(preg_match('-< ?/? ?(td|tr|table|tbody)-i', $tag)){
			$out .= $tag;
		}
	}
	return $out;
}

function we_tag_path_hasIndex($path, $indexArray){
	foreach($indexArray as $index){
		if(file_exists($path . $index)){
			return true;
		}
	}
	return false;
}

function makeArrayFromAttribs($attr){
	$arr = array();
	@eval('$arr = array(' . we_tag_tagParser::parseAttribs($attr) . ');');
	return $arr;
}

function cutSimpleText($text, $len){
	$text = substr($text, 0, $len);
	$pos = array(
		0,
		strrpos($text, ' '),
		strrpos($text, "\n"),
		strrpos($text, "\t"),
	);
	//cut to last whitespace
	return substr($text, 0, max($pos));
}

function cutText($text, $max = 0){
	if((!$max) || (strlen($text) <= $max)){
		return $text;
	}
	//no tags, simple cut off
	if(strstr($text, '<') == FALSE){
		return cutSimpleText($text, $max) . ' &hellip;';
	}

	$ret = '';
	$tags = $foo = array();
	//split text on tags, entities and "rest"
	preg_match_all('%(&#?[[:alnum:]]+;)|([^<&]*)|<(/?)([[:alnum:]]+)([ \t\r\n]+[[:alnum:]]+[ \t\r\n]*=[ \t\r\n]*"[^"]*")*[ \t\r\n]*(/?)>%sm', $text, $foo, PREG_SET_ORDER);

	foreach($foo as $cur){
		switch(count($cur)){
			case 2://entity
				if($max > 0){
					$ret.=$cur[0];
					$max-=1;
				}
				break;
			case 3://text
				if($max > 0){
					$len = strlen($cur[0]);
					$ret.=($len > $max ? cutSimpleText($cur[0], $max) : $cur[0]);
					$max-=$len;
					if($max <= 0){
						$ret.=' &hellip;';
					}
				}
				break;
			case 7://tags
				if($max > 0){
					$ret.=$cur[0];
					if(!$cur[6]){//!selfclosing
						if($cur[3]){//close
							array_pop($tags);
						} else{
							array_push($tags, $cur[4]);
						}
					}
				}
				break;
		}
	}

//close open tags
	while(!empty($tags)) {
		$ret.='</' . array_pop($tags) . '>';
	}

	return $ret;
}

function we_getDocForTag($docAttr, $maindefault = false){
	if($maindefault){
		switch($docAttr){
			case 'self' :
				return $GLOBALS['we_doc'];
			default :
				return $GLOBALS['WE_MAIN_DOC'];
		}
	} else{
		switch($docAttr){
			case 'top' :
				return $GLOBALS['WE_MAIN_DOC'];
			default :
				return $GLOBALS['we_doc'];
		}
	}
}

function modulFehltError($modul, $tag){
	$tag = str_replace(array('we_tag_', 'we_parse_tag_'), '', $tag);
	return parseError(sprintf(g_l('parser', '[module_missing]'), $modul, $tag));
}

function parseError($text){
	t_e('warning', html_entity_decode($text, ENT_QUOTES, $GLOBALS['WE_BACKENDCHARSET']), g_l('weClass', '[template]') . ': ' . we_tag_tagParser::$curFile);
	return "<b>" . g_l('parser', '[error_in_template]') . ":</b>$text<br/>\n" . g_l('weClass', '[template]') . ': ' . we_tag_tagParser::$curFile; /* .'<?php trigger_error(\''.str_replace('\'', '"', $text).'\',E_USER_WARNING);?>'; */
}

function attributFehltError($attribs, $attr, $tag, $canBeEmpty = false){
	$tag = str_replace(array('we_tag_', 'we_parse_tag_'), '', $tag);
	if($canBeEmpty){
		if(!isset($attribs[$attr]))
			return parseError(sprintf(g_l('parser', '[attrib_missing2]'), $attr, $tag));
	} else{
		if(!isset($attribs[$attr]) || $attribs[$attr] == '')
			return parseError(sprintf(g_l('parser', '[attrib_missing]'), $attr, $tag));
	}
	return '';
}

function we_getInputRadioField($name, $value, $itsValue, $atts){
	//  This function replaced fnc: we_getRadioField
	$atts['type'] = 'radio';
	$atts['name'] = $name;
	$atts['value'] = oldHtmlspecialchars($itsValue);
	if($value == $itsValue){
		$atts['checked'] = 'checked';
	}
	return getHtmlTag('input', $atts);
}

function we_getTextareaField($name, $value, $atts){
	$atts['name'] = $name;
	$atts['rows'] = isset($atts['rows']) ? $atts['rows'] : 5;
	$atts['cols'] = isset($atts['cols']) ? $atts['cols'] : 20;

	return getHtmlTag('textarea', $atts, oldHtmlspecialchars($value), true);
}

function we_getInputTextInputField($name, $value, $atts){
	$atts['type'] = 'text';
	$atts['name'] = $name;
	$atts['value'] = oldHtmlspecialchars($value);

	return getHtmlTag('input', $atts);
}

function we_getInputPasswordField($name, $value, $atts){
	$atts['type'] = 'password';
	$atts['name'] = $name;
	$atts['value'] = oldHtmlspecialchars($value);

	return getHtmlTag('input', $atts);
}

function we_getHiddenField($name, $value, $xml = false){
	return '<input type="hidden" name="' . $name . '" value="' . oldHtmlspecialchars($value) . '" ' . ($xml ? ' /' : '') . '>';
}

//function we_getInputChoiceField($name, $value, $values, $atts, $mode, $valuesIsHash = false){}
//=> moved as statical function htmlInputChoiceField() to we_html_tools

function we_getInputCheckboxField($name, $value, $attr){
	//  returns a checkbox with associated hidden-field

	$tmpname = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(time())
	if($value){
		$attr['checked'] = 'checked';
	}
	$attr['type'] = 'checkbox';
	$attr['value'] = 1;
	$attr['name'] = $tmpname;
	$attr['onclick'] = 'this.form.elements[\'' . $name . '\'].value=(this.checked) ? 1 : 0';
	$_attsHidden = array();

	// hiddenField
	if(isset($attr['xml'])){
		$_attsHidden['xml'] = $attr['xml'];
	}
	$_attsHidden['type'] = 'hidden';
	$_attsHidden['name'] = $name;
	$_attsHidden['value'] = oldHtmlspecialchars($value);

	return getHtmlTag('input', $attr) . getHtmlTag('input', $_attsHidden);
}

function we_getSelectField($name, $value, $values, $attribs = array(), $addMissing = true){

	$options = makeArrayFromCSV($values);
	$attribs['name'] = $name;
	$content = '';
	$isin = 0;
	foreach($options as $option){
		if($option == $value){
			$content .= getHtmlTag('option', array('value' => $option, 'selected' => 'selected'), $option, true);
			$isin = 1;
		} else{
			$content .= getHtmlTag('option', array('value' => $option), $option, true);
		}
	}
	if((!$isin) && $addMissing && $value != ''){
		$content .= getHtmlTag('option', array(
			'value' => oldHtmlspecialchars($value), 'selected' => 'selected'
			), oldHtmlspecialchars($value), true);
	}
	return getHtmlTag('select', $attribs, $content, true);
}

/* * *************************************************
 * 	we:tags										 *
  /*  ************************************************* */

function we_tag_ifSidebar(){
	return defined('WE_SIDEBAR');
}

function we_tag_ifNotSidebar(){
	return !we_tag_ifSidebar();
}

function we_tag_ifDemo(){
	return !defined('UID');
}

function we_tag_ifSeeMode($attribs, $content){
	return (we_tag('ifWebEdition', $attribs, $content)) && (isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == 'seem');
}

function we_tag_ifNotSeeMode($attribs, $content){
	return (we_tag('ifWebEdition', $attribs, $content)) || !(we_tag('ifSeeMode', $attribs, $content));
}

function we_tag_ifTdEmpty(){
	return $GLOBALS['lv']->tdEmpty();
}

function we_tag_ifTdNotEmpty(){
	return !we_tag('ifTdEmpty');
}

function we_tag_ifTop(){
	return ($GLOBALS['WE_MAIN_DOC'] == $GLOBALS['we_doc']);
}

function we_tag_ifFieldNotEmpty($attribs, $content){
	return !we_tag('ifFieldEmpty', $attribs, $content);
}

function we_tag_ifNotField($attribs, $content){
	return !we_tag('ifField', $attribs, $content);
}

function we_tag_ifFound(){
	return isset($GLOBALS['lv']) && $GLOBALS['lv']->anz;
}

function we_tag_ifIsNotDomain($attribs){
	return (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) || !we_tag('ifIsDomain', $attribs);
}

function we_tag_ifLastCol(){
	return (isset($GLOBALS['lv'])) && $GLOBALS['lv']->shouldPrintEndTR();
}

function we_tag_ifNew($attribs){
	$type = weTag_getAttribute('type', $attribs);
	return !(isset($_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']) && $_REQUEST['we_edit' . (($type == 'object') ? 'Object' : 'Document') . '_ID']);
}

function we_tag_ifNotNew($attribs, $content){
	return !we_tag('ifNew', $attribs, $content);
}

function we_tag_ifNotCat($attribs, $content){
	return !we_tag('ifCat', $attribs, $content);
}

function we_tag_ifNotCaptcha($attribs, $content){
	return !we_tag('ifCaptcha', $attribs, $content);
}

function we_tag_ifNotDeleted($attribs, $content){
	return !we_tag('ifDeleted', $attribs, $content);
}

function we_tag_ifNotDoctype($attribs, $content){
	return !we_tag('ifDoctype', $attribs, $content);
}

function we_tag_ifNotEditmode($attribs){
	return !we_tag('ifEditmode', $attribs);
}

function we_tag_ifNotEmpty($attribs){
	return (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) || !we_tag('ifEmpty', $attribs);
}

function we_tag_ifNotEqual($attribs){
	if(isset($attribs['_name_orig'])){$attribs['name']=$attribs['_name_orig'];}
	return !we_tag('ifEqual', $attribs);
}

function we_tag_ifNotFound($attribs){
	return !we_tag('ifFound', $attribs);
}

function we_tag_ifNotObject($attribs){
	return !we_tag('ifObject', $attribs);
}

function we_tag_ifNotObjectLanguage($attribs){
	return !we_tag('ifObjectLanguage', $attribs);
}

function we_tag_ifNotPageLanguage($attribs){
	return !(we_tag('ifPageLanguage', $attribs));
}

function we_tag_ifNotHasShopVariants($attribs){
	return !we_tag('ifHasShopVariants', $attribs);
}

function we_tag_ifNotSendMail($attribs){
	return !(we_tag('ifSendMail', $attribs));
}

function we_tag_ifNotVoteActive($attribs){
	return !we_tag('ifVoteActive', $attribs);
}

function we_tag_ifNotVoteIsRequired($attribs){
	return !we_tag('ifVoteIsRequired', $attribs);
}

function we_tag_ifNotHasChildren($attribs){
	return !we_tag('ifHasChildren', $attribs);
}

function we_tag_ifNotHasEntries($attribs){
	return !we_tag('ifHasEntries', $attribs);
}

function we_tag_ifNotHasCurrentEntry($attribs){
	return !we_tag('ifHasCurrentEntry', $attribs);
}

function we_tag_ifNotRegisteredUser($attribs){
	return !we_tag('ifRegisteredUser', $attribs);
}

function we_tag_ifNotNewsletterSalutation($attribs){
	return !we_tag('ifNewsletterSalutation', $attribs);
}

function we_tag_ifNotReturnPage($attribs){
	return !we_tag('ifReturnPage', $attribs);
}

function we_tag_ifNotSearch($attribs){
	return !we_tag('ifSearch', $attribs);
}

function we_tag_ifNotSelf($attribs){
	return !we_tag('ifSelf', $attribs);
}

function we_tag_ifNotTop($attribs){
	return !we_tag('ifTop', $attribs);
}

function we_tag_ifNotTemplate($attribs){
	return !we_tag('ifTemplate', $attribs);
}

function we_tag_ifNotVar($attribs){
	if(isset($attribs['_name_orig'])){$attribs['name']=$attribs['_name_orig'];}
	return !we_tag('ifVar', $attribs);
}

function we_tag_ifNotVarSet($attribs){
	if(isset($attribs['_name_orig'])){$attribs['name']=$attribs['_name_orig'];}
	return !we_tag('ifVarSet', $attribs);
}

function we_tag_ifNotVotingField($attribs){
	return !we_tag('ifVotingField', $attribs);
}

function we_tag_ifShopFieldNotEmpty($attribs){
	return !we_tag('ifShopFieldEmpty', $attribs);
}

function we_tag_ifVotingFieldNotEmpty($attribs){
	return !we_tag('ifVotingFieldEmpty', $attribs);
}

function we_tag_ifNotWebEdition($attribs){
	return !we_tag('ifWebEdition', $attribs);
}

function we_tag_ifNotWorkspace($attribs){
	return !we_tag('ifWorkspace', $attribs);
}

function we_tag_ifNotPosition($attribs){
	return !we_tag('ifPosition', $attribs);
}

function we_tag_pagelogger($attribs, $content){
	return we_tag('tracker', $attribs, $content);
}

function we_tag_ifReturnPage(){
	return isset($_REQUEST['we_returnpage']) && ($_REQUEST['we_returnpage']);
}

function we_tag_ifUserInputNotEmpty($attribs){
	return !we_tag('ifUserInputEmpty', $attribs);
}

function we_tag_ifVarNotEmpty($attribs){
	if(isset($attribs['_name_orig'])){$attribs['name']=$attribs['_name_orig'];}
	return !we_tag('ifVarEmpty', $attribs);
}

function we_tag_ifWebEdition(){
	return $GLOBALS['WE_MAIN_DOC']->InWebEdition;
}

function we_tag_ifWritten($attribs){
	$type = weTag_getAttribute('type', $attribs);
	$type = $type ? $type : weTag_getAttribute('var', $attribs, 'document');
	$type = $type ? $type : weTag_getAttribute('doc', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_write_ok']) && ($GLOBALS['we_' . $type . '_write_ok'] == true);
}

function we_tag_ifNotWritten($attribs){
	return !we_tag('ifWritten', $attribs);
}

function we_tag_linkToSEEM($attribs, $content){
	return we_tag('linkToSeeMode', $attribs, $content);
}

function we_tag_listviewPageNr(){
	return $GLOBALS['lv']->rows ? (((abs($GLOBALS['lv']->start) - abs($GLOBALS['lv']->offset)) / $GLOBALS['lv']->maxItemsPerPage) + 1) : 1;
}

function we_tag_listviewPages(){
	return $GLOBALS['lv']->rows ? ceil(
			((float) $GLOBALS['lv']->anz_all - abs($GLOBALS['lv']->offset)) / ((float) $GLOBALS['lv']->maxItemsPerPage )) : 1;
}

function we_tag_listviewRows(){
	return $GLOBALS['lv']->anz_all - abs($GLOBALS['lv']->offset);
}

function we_tag_listviewStart(){
	return $GLOBALS['lv']->start + 1 - abs($GLOBALS['lv']->offset);
}

function we_tag_makeMail(){
	t_e('deprecated', 'makeMail');
	return '';
}

function we_tag_ifshopexists(){
	return defined('SHOP_TABLE');
}

function we_tag_ifobjektexists(){
	return defined('OBJECT_TABLE');
}

function we_tag_ifnewsletterexists(){
	return defined('NEWSLETTER_TABLE');
}

function we_tag_ifcustomerexists(){
	return defined('CUSTOMER_TABLE');
}

function we_tag_ifbannerexists(){
	return defined('BANNER_TABLE');
}

function we_tag_ifvotingexists(){
	return defined('VOTING_TABLE');
}

//this function is used by all tags adding elements to we_lv_array
function we_post_tag_listview(){
	if(isset($GLOBALS['we_lv_array'])){
		array_pop($GLOBALS['we_lv_array']);
		if(count($GLOBALS['we_lv_array'])){
			$GLOBALS['lv'] = clone($GLOBALS['we_lv_array'][count($GLOBALS['we_lv_array']) - 1]);
		} else{
			unset($GLOBALS['lv']);
			unset($GLOBALS['we_lv_array']);
		}
	}
}

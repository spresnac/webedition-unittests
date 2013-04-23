<?php

/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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
function we_tag_formfield($attribs){

	if(($foo = attributFehltError($attribs, "name", __FUNCTION__)))
		return $foo;
	$name = weTag_getAttribute("name", $attribs);

	$types = makeArrayFromCSV(weTag_getAttribute("type", $attribs, "textinput"));
	$attrs = makeArrayFromCSV(weTag_getAttribute("attribs", $attribs));

	$type_sel = $GLOBALS['we_doc']->getElement($name, 'fftype');
	$ffname = $GLOBALS['we_doc']->getElement($name, 'ffname');

	$type_sel = $type_sel ? $type_sel : (!empty($types) ? $types[0] : "textinput");

	$nameprefix = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . '#';

	$xml = weTag_getAttribute("xml", $attribs, XHTML_DEFAULT, true);
	$ff = array();

	$ret = "";

	// here add some mandatory fields
	$mandatoryFields = array();
	if($xml){
		$mandatoryFields = array(
			'textarea_cols', 'textarea_rows'
		);
	}

	foreach($attribs as $k => $v){
		if(preg_match("/^([^_]+)_([^_]+)$/", $k, $m) && ($m[1] == $type_sel)){
			if(in_array($k, $attrs)){
				if(isset($GLOBALS['we_doc']->elements[$name]['ff_' . $type_sel . '_' . $m[2]])){
					$ff[$m[2]]['value'] = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $m[2]);
				}
				$ff[$m[2]]['default'] = $attribs[$k];
			} else{
				/* $ff[$m[2]] = array('change' => 0, 'default' => $attribs[$k]); */
				$ff[$m[2]]['change'] = 0;
				$ff[$m[2]]['default'] = $attribs[$k];
			}
			if(in_array($m[0], $mandatoryFields)){
				for($i = (count($mandatoryFields) - 1); $i >= 0; $i--){
					if($mandatoryFields[$i] == $m[0]){
						unset($mandatoryFields[$i]);
					}
				}
			}
		}
	}

	$attrs = array_merge($attrs, $mandatoryFields);

	foreach($attrs as $a){
		if(preg_match("/^([^_]+)_([^_]+)$/", $a, $m) && ($m[1] == $type_sel)){

			//$ff[$m[2]] = array('change' => 1);
			$ff[$m[2]]['change'] = 1;

			if(isset($GLOBALS['we_doc']->elements[$name]['ff_' . $type_sel . '_' . $m[2]])){
				$t = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $m[2]);
				if(!empty($t)){
					$ff[$m[2]]['value'] = $t;
				}
			}
		}
	}

	if(isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"]){
		$tmp_select = '<select name="' . $nameprefix . 'fftype]" onchange="setScrollTo();we_cmd(\'reload_editpage\');">' . "\n";
		foreach($types as $k){
			$tmp_select .= '<option value="' . $k . '"' . (($k == $type_sel) ? ' selected="selected"' : '') . '>' . $k . '</option>' . "\n";
		}
		$tmp_select .= '</select>';
		$tbl = '<table width="223" border="0" cellspacing="0" cellpadding="4" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif">
	<tr>
		<td class=\"weEditmodeStyle\" width="62" style="color: black; font-size: 12px; font-family: Verdana, sans-serif" align="right"><nobr>' . g_l('global', "[name]") . ':&nbsp;</nobr></td>
		<td class=\"weEditmodeStyle\" width="161"><input type="text" name="' . $nameprefix . 'ffname]" value="' . $ffname . '" size="24" /></td>
	</tr>
	<tr>
		<td class=\"weEditmodeStyle\" width="62" style="color: black; font-size: 12px; font-family: Verdana, sans-serif" align="right"><nobr>' . g_l('global', "[type]") . ':&nbsp;</nobr></td>
		<td class=\"weEditmodeStyle\" width="161">' . $tmp_select . '</td>
	</tr>
';

		if(!empty($ff)){
			$tbl .= '	<tr>
		<td class=\"weEditmodeStyle\" width="62" style="color: black; font-size: 12px; font-family: Verdana, sans-serif" align="right"><nobr>' . g_l('global', "[attributes]") . ':&nbsp;</nobr></td>
		<td class=\"weEditmodeStyle\" width="161">
			<table border="0" cellspacing="0">
				<tr>
';

			foreach($ff as $f => $m){
				$tbl .= '<td class=\"weEditmodeStyle\" style="color: black; font-size: 10px; font-family: Verdana, sans-serif"><nobr><b>' . $f . ':</b><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif">&nbsp;';
				$val = isset($m['value']) ? $m['value'] : '';

				$default = isset($m['default']) ? makeArrayFromCSV($m['default']) : array();

				if($m['change'] == 1){
					$valselect = "";
					if(count($default) > 1){
						$valselect = '<select name="' . $name . 'tmp" size="1" onchange="this.form.elements[\'' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']\'].value=this.options[this.selectedIndex].value;">' . "\n";
						$valselect .= '<option value=""></option>' . "\n";
						foreach($default as $v){
							$valselect .= '<option value="' . $v . '">' . $v . '</option>' . "\n";
						}
						$valselect .= '</select>' . "\n";
					}
					if((!isset($m['value'])) && count($default) == 1){
						$val = $default[0];
					}
					$tbl .= '<input type="text" name="' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']" size="7" border="0"' . ($val ? ' value="' . $val . '"' : '') . ' />' . $valselect;
				} else{
					if(count($default) > 1){
						$val = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $f);
						$valselect = "";
						if(count($default) > 1){
							$valselect = '<select name="' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']" size="1">' . "\n";
							foreach($default as $v){
								$valselect .= '<option value="' . $v . '"' . (($v == $val) ? " selected" : "") . '>' . $v . '</option>' . "\n";
							}
							$valselect .= '</select>' . "\n";
						}
						$tbl .= $valselect;
					} else{
						$foo = !empty($default) ? $default[0] : "";
						$tbl .= $foo . '<input type="hidden" name="' . $nameprefix . 'ff_' . $type_sel . '_' . $f . ']" value="' . $foo . '" />';
					}
				}
				$tbl .= '</span></nobr></td><td class=\"weEditmodeStyle\">' . we_html_tools::getPixel(5, 2) . "</td>\n";
			}
			$tbl .= '				</tr>
			</table>
		</td>
	</tr>
';
		}
		if($type_sel == "select"){
			$tbl .= '	<tr>
		<td class=\"weEditmodeStyle\" width="62" style="color: black; font-size: 12px; font-family: Verdana, sans-serif" align="right"><nobr>' . g_l('global', "[values]") . ':</nobr></td>
		<td class=\"weEditmodeStyle\" width="161"><textarea name="' . $nameprefix . 'ffvalues]" cols="30" rows="5">' . $GLOBALS['we_doc']->getElement(
					$name, 'ffvalues') . '</textarea></td>
	</tr>
	<tr>
		<td class=\"weEditmodeStyle\" width="62" style="color: black; font-size: 12px; font-family: Verdana, sans-serif" align="right"><nobr>' . g_l('global', "[default]") . ':</nobr></td>
		<td class=\"weEditmodeStyle\" width="161"><input type="text" name="' . $nameprefix . 'ffdefault]" size="24" value="' . $GLOBALS['we_doc']->getElement(
					$name, 'ffdefault') . '" /></td>
	</tr>
';
		} else
		if($type_sel == 'file'){
			$tbl .= '	<tr>
		<td class=\"weEditmodeStyle\" width="62" style="color: black; font-size: 12px; font-family: Verdana, sans-serif" align="right"><nobr>' . g_l('global', "[max_file_size]") . ':</nobr></td>
		<td class=\"weEditmodeStyle\" width="161"><input type="text" name="' . $nameprefix . 'ffmaxfilesize]" size="24" value="' . $GLOBALS['we_doc']->getElement(
					$name, 'ffmaxfilesize') . '" /></td>
	</tr>
';
		} else
		if($type_sel == 'radio' || $type_sel == 'checkbox'){
			$tbl .= '	<tr>
		<td class=\"weEditmodeStyle\" width="62" style="color: black; font-size: 12px; font-family: Verdana, sans-serif" align="right"><nobr>' . g_l('global', "[checked]") . ':</nobr></td>
		<td class=\"weEditmodeStyle\" width="161"><select name="' . $nameprefix . 'ffchecked]" size="1"><option value="0"' . ($GLOBALS['we_doc']->getElement(
					$name, 'ffchecked') ? "" : " selected") . '>' . g_l('global', "[no]") . '</option><option value="1"' . ($GLOBALS['we_doc']->getElement(
					$name, 'ffchecked') ? " selected" : "") . '>' . g_l('global', "[yes]") . '</option></select></td>
	</tr>
';
		}
		$tbl .= '</table>
';
		$ret .= $tbl;
	} else{

		$tagEndTag = false;

		$tagName = '';
		$tagAtts = array(
			'xml' => $xml, 'name' => oldHtmlspecialchars($GLOBALS['we_doc']->getElement($attribs["name"], 'ffname'))
		);
		$tagContent = '';

		switch($type_sel){
			case "textarea" :
			case "select" :
				$tagName = $type_sel;
				break;
			default :
				$tagName = 'input';
				$tagAtts['type'] = $type_sel;
		}

		foreach($ff as $f => $arr){

			if(!((($f == 'value') && ($type_sel == 'textarea')) || $f == 'type')){

				$val = $GLOBALS['we_doc']->getElement($name, 'ff_' . $type_sel . '_' . $f);
				if($val){
					$tagAtts[$f] = oldHtmlspecialchars($val);
				}
			}
		}

		switch($type_sel){
			case 'checkbox':
			case 'radio':
				if($GLOBALS['we_doc']->getElement($name, 'ffchecked')){
					$tagAtts['checked'] = 'checked';
				}
				break;
			case 'textinput': // correct input type="text"
				$tagAtts['type'] = 'text';
				break;
			case 'textarea':
				$tagEndTag = true;
				if(isset($ff['value'])){
					$tagContent = oldHtmlspecialchars($ff['value']['value']);
				}
				if(!array_key_exists('cols', $tagAtts)){
					$tagAtts['cols'] = 20;
				}
				if(!array_key_exists('rows', $tagAtts)){
					$tagAtts['rows'] = 5;
				}
				break;
			case 'select':
				$selected = $GLOBALS['we_doc']->getElement($name, 'ffdefault');
				$foo = $GLOBALS['we_doc']->getElement($name, 'ffvalues');
				$foo = str_replace("\r\n", "<_BR_>", $foo);
				$foo = str_replace("\r", "<_BR_>", $foo);
				$foo = str_replace("\n", "<_BR_>", $foo);
				$foo = explode("<_BR_>", $foo);
				foreach($foo as $v){
					$_atts = array(
						'value' => oldHtmlspecialchars($v)
					);
					if($selected == $v){
						$_atts['selected'] = 'selected';
					}
					$tagContent .= getHtmlTag('option', $_atts, oldHtmlspecialchars($v));
				}
				break;
			case 'country':
				$orgVal = $GLOBALS['we_doc']->getElement($name, 'ffdefault');
				$docAttr = weTag_getAttribute("doc", $attribs, "self");
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				if($lang != ''){
					$langcode = substr($lang, 0, 2);
				} else{
					$langcode = we_core_Local::weLangToLocale($GLOBALS["WE_LANGUAGE"]);
				}
				if(!Zend_Locale::hasCache()){
					Zend_Locale::setCache(getWEZendCache());
				}

				$zendsupported = Zend_Locale::getTranslationList('territory', $langcode, 2);
				$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));
				foreach($topCountries as $countrykey => &$countryvalue){
					$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
				}
				unset($countryvalue);
				$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
				foreach($shownCountries as $countrykey => &$countryvalue){
					$countryvalue = Zend_Locale::getTranslation($countrykey, 'territory', $langcode);
				}
				unset($countryvalue);
				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $lang . '.UTF-8');
				asort($topCountries, SORT_LOCALE_STRING);
				asort($shownCountries, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);

				$tagContent = '';
				if(WE_COUNTRIES_DEFAULT != ''){
					$tagContent.='<option value="--" ' . ($orgVal == '--' ? ' selected="selected">' : '>') . WE_COUNTRIES_DEFAULT . '</option>' . "\n";
				}
				foreach($topCountries as $countrykey => &$countryvalue){
					$tagContent.='<option value="' . $countrykey . '" ' . ($orgVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>' . "\n";
				}
				unset($countryvalue);
				$tagContent.='<option value="-" disabled="disabled">----</option>' . "\n";
				foreach($shownCountries as $countrykey2 => &$countryvalue2){
					$tagContent.='<option value="' . $countrykey2 . '" ' . ($orgVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>' . "\n";
				}
				unset($countryvalue);
				$newAtts['size'] = (isset($atts['size']) ? $atts['size'] : 1);
				$newAtts['name'] = $fieldname;
				$tagName = "select";
				break;
			case 'language':
				$orgVal = $GLOBALS['we_doc']->getElement($name, 'ffdefault');
				$docAttr = weTag_getAttribute("doc", $attribs, "self");
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				if($lang != ''){
					$langcode = substr($lang, 0, 2);
				} else{
					$langcode = we_core_Local::weLangToLocale($GLOBALS["WE_LANGUAGE"]);
				}
				$frontendL = $GLOBALS["weFrontendLanguages"];
				foreach($frontendL as $lc => &$lcvalue){
					$lccode = explode('_', $lcvalue);
					$lcvalue = $lccode[0];
				}
				unset($lcvalue);
				if(!Zend_Locale::hasCache()){
					Zend_Locale::setCache(getWEZendCache());
				}

				foreach($frontendL as &$lcvalue){
					$frontendLL[$lcvalue] = Zend_Locale::getTranslation($lcvalue, 'language', $langcode);
				}

				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $lang . '.UTF-8');
				asort($frontendLL, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);

				$tagContent = '';
				foreach($frontendLL as $langkey => &$langvalue){
					$tagContent.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>' . "\n";
				}
				unset($langvalue);
				$tagAtts['size'] = (isset($atts['size']) ? $atts['size'] : 1);

				$tagName = "select";
				break;
			case 'file':
				$ret .= getHtmlTag(
					'input', array(
					'type' => 'hidden',
					'name' => 'MAX_FILE_SIZE',
					'value' => oldHtmlspecialchars(
						$GLOBALS['we_doc']->getElement($name, 'ffmaxfilesize')),
					'xml' => $xml
					));
				break;
		}
		return getHtmlTag($tagName, $tagAtts, $tagContent, $tagEndTag) . $ret;
	}
	return $ret;
}

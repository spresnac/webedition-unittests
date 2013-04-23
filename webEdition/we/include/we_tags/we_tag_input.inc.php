<?php

/**
 * webEdition CMS
 *
 * $Rev: 5770 $
 * $Author: mokraemer $
 * $Date: 2013-02-09 14:10:42 +0100 (Sat, 09 Feb 2013) $
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
function we_tag_input($attribs, $content){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$name = weTag_getAttribute('name', $attribs);
	$value = weTag_getAttribute('value', $attribs);
	$values = weTag_getAttribute('values', $attribs);
	$mode = weTag_getAttribute('mode', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$format = weTag_getAttribute('format', $attribs);

	$seperator = weTag_getAttribute('seperator', $attribs, '|');
	$reload = weTag_getAttribute('reload', $attribs, false, true);

	$spellcheck = weTag_getAttribute('spellcheck', $attribs, 'false');

	$val = oldHtmlspecialchars($GLOBALS['we_doc']->issetElement($name) ? $GLOBALS['we_doc']->getElement($name) : $value);

	if($GLOBALS['we_editmode']){
		//all edit-specific things
		switch($type){
			case 'date':
				$d = abs($GLOBALS['we_doc']->getElement($name));
				return we_html_tools::getDateInput2(
						'we_' . $GLOBALS['we_doc']->Name . '_date[' . $name . ']', ($d ? $d : time()), true, $format);
			case 'checkbox':
				$attr = we_make_attribs($attribs, 'name,value,type,_name_orig');
				return '<input onclick="_EditorFrame.setEditorIsHot(true);this.form.elements[\'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']\'].value=(this.checked ? 1 : \'\');' . ($reload ? (';setScrollTo();top.we_cmd(\'reload_editpage\');') : '') . '" type="checkbox" name="we_' . $GLOBALS['we_doc']->Name . '_attrib_' . $name . '" value="1"' . ($attr ? " $attr" : "") . ($val ? " checked" : "") . ' /><input type="hidden" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $val . '" />';
			case 'country':
				$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
				$newAtts['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$newAtts['onclick'] = '_EditorFrame.setEditorIsHot(true);';
				$docAttr = weTag_getAttribute('doc', $attribs, 'self');
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				if($lang != ''){
					$langcode = substr($lang, 0, 2);
				} else{
					$langcode = we_core_Local::weLangToLocale($GLOBALS["WE_LANGUAGE"]);
				}
				$orgVal = $GLOBALS['we_doc']->getElement($name);
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
				$orgVal = $GLOBALS['we_doc']->getElement($name);
				$content = '';
				if(WE_COUNTRIES_DEFAULT != ''){
					$content.='<option value="--" ' . ($orgVal == '--' ? ' selected="selected">' : '>') . WE_COUNTRIES_DEFAULT . '</option>' . "\n";
				}
				foreach($topCountries as $countrykey => &$countryvalue){
					$content.='<option value="' . $countrykey . '" ' . ($orgVal == $countrykey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue) . '</option>' . "\n";
				}
				unset($countryvalue);
				$content.='<option value="-" disabled="disabled">----</option>' . "\n";
				foreach($shownCountries as $countrykey2 => &$countryvalue2){
					$content.='<option value="' . $countrykey2 . '" ' . ($orgVal == $countrykey2 ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($countryvalue2) . '</option>' . "\n";
				}
				unset($countryvalue2);

				return getHtmlTag('select', $newAtts, $content, true);
			case 'language':
				$newAtts = removeAttribs($attribs, array('checked', 'type', 'options', 'selected', 'onchange', 'onChange', 'name', 'value', 'values', 'onclick', 'onClick', 'mode', 'choice', 'pure', 'rows', 'cols', 'maxlength', 'wysiwyg'));
				$newAtts['name'] = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
				$newAtts['onclick'] = '_EditorFrame.setEditorIsHot(true);';
				$docAttr = weTag_getAttribute('doc', $attribs, 'self');
				$doc = we_getDocForTag($docAttr);
				$lang = $doc->Language;
				$langcode = substr($lang, 0, 2);
				$langcode = ($lang != '' ? substr($lang, 0, 2) : we_core_Local::weLangToLocale($GLOBALS["WE_LANGUAGE"]));

				$frontendL = $GLOBALS['weFrontendLanguages'];
				foreach($frontendL as &$lcvalue){
					$lccode = explode('_', $lcvalue);
					$lcvalue = $lccode[0];
				}
				if(!Zend_Locale::hasCache()){
					Zend_Locale::setCache(getWEZendCache());
				}
				$frontendLL = array();
				foreach($frontendL as &$lcvalue){
					$frontendLL[$lcvalue] = Zend_Locale::getTranslation($lcvalue, 'language', $langcode);
				}

				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $lang . '.UTF-8');
				asort($frontendLL, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);
				$content = '';
				$orgVal = $GLOBALS['we_doc']->getElement($name);
				foreach($frontendLL as $langkey => &$langvalue){
					$content.='<option value="' . $langkey . '" ' . ($orgVal == $langkey ? ' selected="selected">' : '>') . CheckAndConvertISOfrontend($langvalue) . '</option>' . "\n";
				}
				unset($langvalue);
				return getHtmlTag('select', $newAtts, $content, true);
			case 'choice':
				if($values){
					$tagname = 'we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']';
					$vals = explode($seperator, $values);

					if($mode == 'add'){
						$onChange = "this.form.elements['$tagname'].value += ((this.form.elements['$tagname'].value ? ' ' : '')+this.options[this.selectedIndex].text);";
					} else{
						$onChange = "this.form.elements['$tagname'].value = this.options[this.selectedIndex].text;";
					}
					if($reload){
						$onChange .= 'setScrollTo();top.we_cmd(\'reload_editpage\');';
					}
					$sel = '<select  class="defaultfont" name="we_choice_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" size="1" onchange="' . $onChange . ';this.selectedIndex=0;_EditorFrame.setEditorIsHot(true);"><option></option>' .
						(!empty($vals) ? '<option>' . implode("</option>\n<option>", $vals) . "</option>\n" : '') .
						'</select>';
				}
				$attr = we_make_attribs($attribs, 'name,value,type,onchange,mode,values,_name_orig');

				return '<input onchange="_EditorFrame.setEditorIsHot(true);" type="text" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' />' . "&nbsp;" . (isset(
						$sel) ? $sel : '');
			case 'select':
				//NOTE: this tag is for objects only
				return $GLOBALS['we_doc']->getField($attribs, 'select');
			case 'print':
				return $val;
			case 'text':
			default:
				$attr = we_make_attribs($attribs, 'name,value,type,html,_name_orig');

				if(defined('SPELLCHECKER') && $spellcheck == 'true'){
					return '<table border="0" cellpadding="0" cellspacing="0" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif">
	<tr>
			<td class="weEditmodeStyle"><input onchange="_EditorFrame.setEditorIsHot(true);" class="wetextinput" type="text" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' /></td>
			<td class="weEditmodeStyle">' . we_html_tools::getPixel(6, 4) . '</td>
			<td class="weEditmodeStyle">' . we_button::create_button(
							'image:spellcheck', 'javascript:we_cmd("spellcheck","we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']")') . '</td>
	</tr>
</table>';
				} else{
					return '<input onchange="_EditorFrame.setEditorIsHot(true);" class="wetextinput" type="text" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' />';
				}
		}
	} else{
		//not-editmode
		switch($type){
			case 'date':
				return $GLOBALS['we_doc']->getField($attribs, 'date');
			case 'checkbox':
				return $GLOBALS['we_doc']->getElement($name);
			case 'country':
				$lang = weTag_getAttribute('outputlanguage', $attribs);
				if($lang == ''){
					$docAttr = weTag_getAttribute('doc', $attribs, 'self');
					$doc = we_getDocForTag($docAttr);
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if($lang == ''){
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
				}
				if($GLOBALS['we_doc']->getElement($name) == '--'){
					return '';
				} else{
					if(!Zend_Locale::hasCache()){
						Zend_Locale::setCache(getWEZendCache());
					}
					return CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS['we_doc']->getElement($name), 'territory', $langcode));
				}
			case 'language':
				$lang = weTag_getAttribute('outputlanguage', $attribs);
				if($lang == ''){
					$docAttr = weTag_getAttribute('doc', $attribs, 'self');
					$doc = we_getDocForTag($docAttr);
					$lang = $doc->Language;
				}
				$langcode = substr($lang, 0, 2);
				if($lang == ''){
					$lang = explode('_', $GLOBALS['WE_LANGUAGE']);
					$langcode = array_search($lang[0], $GLOBALS['WE_LANGS']);
				}
				if(!Zend_Locale::hasCache()){
					Zend_Locale::setCache(getWEZendCache());
				}
				return CheckAndConvertISOfrontend(Zend_Locale::getTranslation($GLOBALS['we_doc']->getElement($name), 'language', $langcode));
			case 'choice':
				return $GLOBALS['we_doc']->getElement($name);
			case 'select':
				return $GLOBALS['we_doc']->getField($attribs, 'select');
			case 'text':
			default:
				return $GLOBALS['we_doc']->getField($attribs);
		}
	}
}

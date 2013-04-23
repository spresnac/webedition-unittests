<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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

/**
 * Filename:    we_html_element.inc.php
 *
 * Function:    Class to create html tags
 *
 * Description: Provides functions for creating html tags
 */
abstract class we_html_element{

	/**
	 * Function generates html code for html form
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlForm($attribs = array(), $content = ''){

		if(!isset($attribs['name']))
			$attribs['name'] = 'we_form';
		return we_baseElement::getHtmlCode(new we_baseElement('form', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html input element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlInput($attribs = array()){

		if(!isset($attribs['class']))
			$attribs['class'] = 'defaultfont';
		return we_baseElement::getHtmlCode(new we_baseElement('input', 'selfclose', $attribs));
	}

	/**
	 * Function generates html code for html radio-checkbox input element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlRadioCheckbox($attribs = array()){
		$attribs['type'] = 'checkbox';

		$table = new we_html_table(array('cellpadding' => '0', 'cellspacing' => '0', 'border' => '0'), 1, 3);
		$table->setColContent(0, 0, self::htmlInput($attribs));
		$table->setColContent(0, 1, we_html_tools::getPixel(4, 2));
		$table->setColContent(0, 2, self::htmlLabel(array('for' => '$name', 'title' => sprintf(g_l('htmlForms', '[click_here]'), $attribs['title']), $attribs['title'])));

		return $table->getHtml();
	}

	/**
	 * Function generates css code
	 *
	 * @param		$content								string			(optional)
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function cssElement($content = '', $attribs = array()){
		$attribs['type'] = 'text/css';
		return we_baseElement::getHtmlCode(new we_baseElement('style', true, $attribs, $content));
	}

	static function jsScript($name){
		$attribs = array(
			'src' => self::getUnCache($name),
			'type' => 'text/javascript',
		);
		return we_baseElement::getHtmlCode(new we_baseElement('script', true, $attribs));
	}

	/**
	 * Function generates js code
	 *
	 * @param		$content								string			(optional)
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function jsElement($content = '', $attribs = array()){
		$attribs['type'] = 'text/javascript';
		if(strpos($content, '<!--') === FALSE){
			$content = "<!--\n" . trim($content, " \n") . "\n//-->\n";
		}
		return we_baseElement::getHtmlCode(new we_baseElement('script', true, $attribs, $content));
	}

	static function cssLink($url){
		return we_baseElement::getHtmlCode(new we_baseElement('link', false,
					array('href' => self::getUnCache($url), 'rel' => 'styleSheet', 'type' => 'text/css')
			));
	}

	/**
	 * Function generates link code
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function linkElement($attribs = array()){
		return we_baseElement::getHtmlCode(new we_baseElement('link', 'selfclose', $attribs));
	}

	/**
	 * Function generates html code for html font element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlFont($attribs = array(), $content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('font', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html div elements
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlSpan($attribs = array(), $content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('span', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html div elements
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlDiv($attribs = array(), $content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('div', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html b element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	static function htmlB($content){
		return we_baseElement::getHtmlCode(new we_baseElement('b', true, array(), $content));
	}

	/**
	 * Function generates html code for html i element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	static function htmlI($content){
		return we_baseElement::getHtmlCode(new we_baseElement('i', true, array(), $content));
	}

	/**
	 * Function generates html code for html u element
	 *
	 * @param		$content								string
	 *
	 * @return		string
	 */
	static function htmlU($content){
		return we_baseElement::getHtmlCode(new we_baseElement('u', true, array(), $content));
	}

	/**
	 * Function generates html code for html image element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlImg($attribs = array()){
		//if no alt is set, set dummy alt
		if(!isset($attribs['alt'])){
			$attribs['alt'] = '-';
		}
		return we_baseElement::getHtmlCode(new we_baseElement('img', 'selfclose', $attribs));
	}

	/**
	 * Function generates html code for html body element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlBody($attribs = array(), $content = ''){
		$body = new we_baseElement('body', true, $attribs, $content);
		$body->setStyle('margin', '0px 0px 0px 0px');
		return $body->getHTML();
	}

	/**
	 * Function generates html code for html label element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlLabel($attribs = array(), $content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('label', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html hidden element
	 *
	 * @param		$attribs								array			(optional)
	 *
	 * @return		string
	 */
	static function htmlHidden($attribs = array()){
		$attribs['type'] = 'hidden';
		return we_baseElement::getHtmlCode(new we_baseElement('input', 'selfclose', $attribs));
	}

	/**
	 * Function generates html code for html a element
	 *
	 * @param		$attribs								array			(optional)
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlA($attribs = array(), $content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('a', true, $attribs, $content));
	}

	/**
	 * Function generates html code for html br element
	 *
	 * @return		string
	 */
	static function htmlBr(){
		static $br = 0;
		$br = ($br ? $br : we_baseElement::getHtmlCode(new we_baseElement('br', 'selfclose')));
		return $br;
	}

	/**
	 * Function generates html code for html nobr element
	 *
	 * @return		string
	 */
	static function htmlNobr($content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('nobr', true, array(), $content));
	}

	/**
	 * Function generates html code for html br element
	 *
	 * @param		$content								string			(optional)
	 *
	 * @return		string
	 */
	static function htmlComment($content){
		return we_baseElement::getHtmlCode(new we_baseElement('!-- ' . $content . ' --', false));
	}

	/**
	 *
	 */
	static function htmlDocType($version = '4Trans'){
		switch($version){
			case 5:
			case '5':
				return '<!DOCTYPE html>';
			case '4Trans':
			default:
				return '<!DOCTYPE  HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
		}
	}

	/**
	 * Function generates html code for html document
	 *
	 * @return		string
	 */
	static function htmlHtml($content, $close = true){
		return we_baseElement::getHtmlCode(new we_baseElement('html', $close, array(), $content));
	}

	/**
	 * Function generates html code for document head
	 *
	 * @return		string
	 */
	static function htmlHead($content, $close = true){
		return we_baseElement::getHtmlCode(new we_baseElement('head', $close, array(), $content));
	}

	static function htmlMeta($attribs = array()){
		return we_baseElement::getHtmlCode(new we_baseElement('meta', 'selfclose', $attribs));
	}

	static function htmlTitle($content){
		return we_baseElement::getHtmlCode(new we_baseElement('title', true, array(), $content));
	}

	/**
	 * Function generates html code for textarea tag
	 *
	 * @return		string
	 */
	static function htmlTextArea($attribs = array(), $content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('textarea', true, $attribs, $content));
	}

	/**
	 * Function generates html code for p tag
	 *
	 * @return		string
	 */
	static function htmlP($attribs = array(), $content = ''){
		return we_baseElement::getHtmlCode(new we_baseElement('p', true, $attribs, $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	static function htmlCenter($content){
		return we_baseElement::getHtmlCode(new we_baseElement('center', true, array(), $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	static function htmlApplet(array $attribs = array(), $content = '', array $params = array()){
		//$params['cache_archive'] = $attribs['archive']; // Applet seams not to like this param
		$params['cache_version'] = WE_VERSION;
		$params['type'] = 'application/x-java-applet;jpi-version=1.6.0';
		$params['scriptable'] = 'true';
		$params['mayscript'] = 'true';
		$tmp = '';
		foreach($params as $key => $value){
			$tmp.='<param name="' . $key . '" value="' . $value . '"/>';
		}
		$content = $tmp . $content;
		$attribs['MAYSCRIPT'] = '';
		$attribs['SCRIPTABLE'] = '';


		return we_baseElement::getHtmlCode(new we_baseElement('applet', true, $attribs, $content));
	}

	/**
	 * Function generates html code for center tag
	 *
	 * @return		string
	 */
	static function htmlParam($attribs = array()){
		return we_baseElement::getHtmlCode(new we_baseElement('param', 'selfclose', $attribs));
	}

	/**
	 * this func is used to get a parameter variing in each version, to get the latest file (browser cache)
	 * but don't offer information about the current installed version!
	 * @staticvar string $cache saves current cached path
	 * @param string $url url to add the version-unique param
	 * @return string resulting url
	 */
	static function getUnCache($url){
		static $cache = -1;
		if($cache == -1){
			$cache = md5(WE_VERSION . filemtime(WE_INCLUDES_PATH . 'we_version.php') . __FILE__);
		}
		return $url . (strstr($url, '?') ? '&amp;' : '?') . $cache;
	}

	static function htmlIFrame($name, $src, $style, $iframestyle = 'border:0px;width:100%;height:100%;overflow: hidden;'){
		return self::htmlDiv(array('style' => $style, 'name' => $name . 'Div', 'id' => $name . 'Div')
				, we_baseElement::getHtmlCode(
					new we_baseElement('iframe', true, array('name' => $name, 'id' => $name, 'frameBorder' => 0, 'src' => $src, 'style' => $iframestyle))
				));
	}

	static function htmlExIFrame($__name, $__src, $__style){
		if(strlen($__src) > 100){
			$tmp = $__src;
		} else{
			ob_start();
			include $__src;
			$tmp = ob_get_contents();
			ob_end_clean();
		}
		return self::htmlDiv(array('style' => $__style, 'name' => $__name . 'Div', 'id' => $__name . 'Div')
				, $tmp);
	}

}
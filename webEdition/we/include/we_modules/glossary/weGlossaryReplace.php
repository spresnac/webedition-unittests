<?php

/**
 * webEdition CMS
 *
 * $Rev: 5812 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 23:25:28 +0100 (Wed, 13 Feb 2013) $
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
abstract class weGlossaryReplace{

	const configFile = 'we_conf_glossary_settings.inc.php';

	/**
	 * defines the start of the content which have to be replaced
	 *
	 */
	public static function start(){
		$configFile = WE_GLOSSARY_MODULE_PATH . self::configFile;
		if(!file_exists($configFile) || !is_file($configFile)){
			weGlossarySettingControl::saveSettings(true);
		}
		include_once($configFile);

		if(isset($GLOBALS['weGlossaryAutomaticReplacement']) && $GLOBALS['weGlossaryAutomaticReplacement']){
			ob_start();
		}
	}

	/**
	 * finish the output buffering and do the replacements
	 *
	 * @param unknown_type $language
	 */
	public static function end($language){
		include_once(WE_GLOSSARY_MODULE_PATH . self::configFile);

		if(isset($GLOBALS['weGlossaryAutomaticReplacement']) && $GLOBALS['weGlossaryAutomaticReplacement']){
			$content = ob_get_contents();
			ob_end_clean();
			echo self::doReplace($content, $language);
		}
	}

	/**
	 * replace the content
	 *
	 * @param unknown_type $content
	 * @param unknown_type $language
	 */
	public static function replace($content, $language){
		$configFile = WE_GLOSSARY_MODULE_PATH . self::configFile;
		if(!file_exists($configFile) || !is_file($configFile)){
			weGlossarySettingControl::saveSettings(true);
		}
		include_once($configFile);

		if(isset($GLOBALS['weGlossaryAutomaticReplacement']) && $GLOBALS['weGlossaryAutomaticReplacement']){
			return self::doReplace($content, $language);
		}
		return $content;
	}

	/**
	 * replace all glossary items for the requested language in the
	 * given source code
	 *
	 * @param string $src
	 * @param string $language
	 * @return string
	 */
	private static function doReplace($src, $language){
		if($language == ''){
			we_loadLanguageConfig();
			$language = $GLOBALS['weDefaultFrontendLanguage'];
		}
		$matches = array();
		// get the words to replace
		$cache = new weGlossaryCache($language);
		$replace = array(
			'<span ' => $cache->get('foreignword'),
			'<abbr ' => $cache->get('abbreviation'),
			'<acronym ' => $cache->get('acronym'),
			'<a ' => $cache->get('link'),
			'' => $cache->get('textreplacement')
		);
		unset($cache);

		//forbid self-reference links
		foreach($replace['<a '] as $k => $rep){
			if(stripos($rep, $GLOBALS['we_doc']->Path) !== FALSE){
				unset($replace['<a '][$k]);
			}
		}
		//remove empty elements
		foreach($replace as $tag => $words){
			if(empty($words)){
				unset($replace[$tag]);
			}
		}

		// first check if there is a body tag inside the sourcecode
		preg_match('|<body[^>]*>(.*)</body>|si', $src, $matches);

		$srcBody = $replBody = (isset($matches[1]) ? $matches[1] : $src);

		/*
		  This is the fastest variant
		 */
		// split the source into tag and non-tag pieces
		$pieces = preg_split('|(<[^>]*>)|', $replBody, -1, PREG_SPLIT_DELIM_CAPTURE);
		// replace words in non-tag pieces
		$replBody = '';
		$before = '';
		foreach($pieces as $piece){
			if(strpos($piece, '<') === FALSE && stripos($before, '<script') === FALSE){
				//this will generate invalid code: $piece = str_replace('&quot;', '"', $piece);
				foreach($replace as $tag => $words){
					if($tag == '' || stripos($before, $tag) === FALSE){
						$piece = self::doReplaceWords($piece, $words);
					}
				}
			}
			$replBody .= $piece;
			$before = $piece;
		}

		$replBody = str_replace('@@@we@@@', '\'', $replBody);
		if(isset($matches[1])){
			return str_replace($srcBody, $replBody, $src);
		} else{
			return $replBody;
		}
	}

	/**
	 * replace just the given replacements in the given source
	 *
	 * @param string $src
	 * @param array $replacements
	 * @return string
	 */
	private static function doReplaceWords($src, $replacements){
		if($src === '' || count($replacements) == 0){
			return $src;
		}
		@set_time_limit(0);
		$src2 = preg_replace(array_keys($replacements), $replacements, ' ' . $src . ' ', 1);

		if(trim($src, ' ') != trim($src2, ' ') && trim($src2, ' ') != ''){
			$len = strlen($src);
			$spaceStr = '';
			for($i = $len - 1; $i >= 0; $i--){
				if($src{$i} == ' '){
					$spaceStr .=' ';
				} else{
					break;
				}
			}

			// add spaces before and after and replace the words
			$src = preg_replace(array_keys($replacements), $replacements, ' ' . $src . ' ', 1);
			// remove added spaces
			//$return = (preg_replace('/^ (.+) $/', '$1', $src));
			$return = substr($src, 1, -1);
			// remove added slashes
			return stripslashes($return);
		}

		return $src;
	}

}
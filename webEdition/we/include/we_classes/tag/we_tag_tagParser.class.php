<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
class we_tag_tagParser{

	private $lastpos = 0;
	private $tags = array();
	private static $CloseTags = 0;
	private static $AllKnownTags = 0;
	public static $curFile = '';

	//private $AppListviewItemsTags = array();

	public function __construct($content = '', $curFile = ''){
		self::$curFile = $curFile;
		//init Tags
		if($content != ''){
			$this->setAllTags($content);
		}
		if(!is_array(self::$CloseTags)){
			self::$CloseTags = weTagWizard::getTagsWithEndTag();
			self::$AllKnownTags = weTagWizard::getExistingWeTags();
		}
	}

	/* 	private function parseAppListviewItemsTags($tagname, $tag, $code, $attribs = "", $postName = ""){
	  return $this->replaceTag($tag, $code, $php);
	  }
	 */

	public function getAllTags(){
		return $this->tags;
	}

	private function setAllTags($code){
		$this->tags = array();
		$foo = array();
		preg_match_all('%</?we:([[:alnum:]_-]+)([ \t\n\r]*[[:alnum:]_-]+[ \t]*=[ \t]*"[^"]*")*[ \t\n\r]*/?>?%i', $code, $foo, PREG_SET_ORDER);
		foreach($foo as $f){
			$this->tags[] = $f[0];
		}
	}

	/**
	 * @return	array
	 * @param	string $tagname
	 * @param	string $code
	 * @param	bool   $hasEndtag
	 * @desc		function separates a complete XML tag in several pieces
	 * 			returns array with this information
	 * 			tagname without <> .. for example "we:hidePages"
	 * 			[0][x] = complete Tag
	 * 			[1][x] = start tag
	 * 			[2][x] = parameter as string
	 */
	public static function itemize_we_tag($tagname, $code){
		$_matches = array();
		preg_match_all('/(<' . $tagname . '([^>]*)>)/U', $code, $_matches);
		return $_matches;
	}

	/**
	 * @return string of code with all required tags
	 * @param $code Src Code
	 * @desc Searches for all meta-tags in a given template (title, keyword, description, charset)
	 */
	public static function getMetaTags($code){
		$_tmpTags = array();
		$foo = array();
		$_rettags = array();

		preg_match_all('%</?we:([[:alnum:]_-]+)([ \t\n\r]*[[:alnum:]_-]+[ \t]*=[ \t]*"[^"]*")*[ \t\n\r]*/?>?%i', $code, $foo, PREG_SET_ORDER);

		foreach($foo as $f){
			/* 			if(substr($f[1], -1) == '<'){
			  $f[1] = substr($f[1], 0, strlen($f[1]) - 1);
			  } */
			$_tmpTags[] = $f[0];
		}

		//	only Meta-tags, description, keywords, title and charset
		$_tags = array();
		foreach($_tmpTags as $t){
			if(strpos($t, 'we:title') || strpos($t, 'we:description') || strpos(
					$t, 'we:keywords') || strpos($t, 'we:charset')){
				$_tags[] = $t;
			}
		}

		//	now we need all between these tags - beware of selfclosing tags
		for($i = 0; $i < count($_tags);){

			if(preg_match("|<we:(.*)/>|i", $_tags[$i])){ //  selfclosing xhtml-we:tag
				$_start = strpos($code, $_tags[$i]);
				$_starttag = $_tags[$i];

				$_endtag = '';
				$i++;
			} else{ //  "normal" we:tag
				$_start = strpos($code, $_tags[$i]);
				$_starttag = $_tags[$i];
				$i++;

				$_end = strpos($code, $_tags[$i]) - $_start + strlen($_tags[$i]);
				$_endtag = isset($_tags[$i]) ? $_tags[$i] : '';
				$i++;
			}
			$_rettags[] = array(
				array(
					$_starttag, $_endtag
				), $_endtag ? substr($code, $_start, $_end) : ''
			);
			if($_endtag){
				// on behalf of constructions like:
				// <we:ifTemplate><we:title prefix="pref1>title</we:title><we:else/><we:title prefix="pref2>title</we:title><we:ifTemplate>
				// we need to cut after Endtag for the second pair of <title>-Tags to be correctly computeted
				$code = substr($code, $_start + $_end);
			}
		}
		return $_rettags;
	}

	public function parseSpecificTags($tags, &$code, $postName = '', $ignore = array()){
		$this->tags = $tags;
		return $this->parseTags($code, ($postName == '' ? 0 : $postName), $ignore);
	}

	public function parseTags(&$code, $start = 0, $ende = FALSE){
		if(is_string($start)){//old call
			$start = 0;
			$ende = FALSE;
			t_e('Tagparser called with old API - please Update your tag!');
		}
		if($start == 0 && ($tmp = $this->checkOpenCloseTags($code)) !== true){
			return $tmp;
		}
		$this->lastpos = 0;
		$ende = $ende ? $ende : count($this->tags);
		for($ipos = $start; $ipos < $ende;){
			//t_e($ipos,$this->tags[$ipos],$ende);
			if($this->tags[$ipos]){
				$tmp = $this->parseTag($code, $ipos);
				if(!is_numeric($tmp)){
					//parser-error:
					return $tmp;
				}
				$this->tags[$ipos] = '';
				$ipos+=$tmp;
			} else{
				$ipos++;
			}
		}
		$this->lastpos = 0;
		return true;
	}

	private function checkOpenCloseTags(&$code){
		if(!is_array(self::$CloseTags)){
			self::$AllKnownTags = weTagWizard::getExistingWeTags();
			self::$CloseTags = weTagWizard::getTagsWithEndTag();
		}

		$Counter = array();

		foreach($this->tags as $_tag){
			$_matches = array();
			if(preg_match_all('|<(/?)we:([[:alnum:]_-]+)([ \t\n\r]*[[:alnum:]_-]+[ \t]*=[ \t]*"[^"]*")*[ \t\n\r]*(/)?>?|smi', $_tag, $_matches)){
				if(!is_null($_matches[2][0]) && in_array($_matches[2][0], self::$CloseTags)){
					if(!isset($Counter[$_matches[2][0]])){
						$Counter[$_matches[2][0]] = 0;
					}

					if($_matches[1][0] == '/'){
						$Counter[$_matches[2][0]]--;
					} else{
						//selfclosing-Tag
						if($_matches[4][0] == '/'){
							continue;
						}
						$Counter[$_matches[2][0]]++;
					}
				}
			}
		}

		$ErrorMsg = '';
		$err = '';
		$isError = false;
		foreach($Counter as $_tag => $_counter){
			if($_counter < 0){
				$err.=sprintf(g_l('parser', '[missing_open_tag]'), 'we:' . $_tag);
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_open_tag]') . ' (' . abs($_counter) . ')', 'we:' . $_tag));

				$isError = true;
			} else
			if($_counter > 0){
				$err.=sprintf(g_l('parser', '[missing_close_tag]'), 'we:' . $_tag);
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_close_tag]') . ' (' . abs($_counter) . ')', 'we:' . $_tag));
				$isError = true;
			}
		}
		if($isError){
			$code = $ErrorMsg;
		}
		return (!$isError ? true : $err);
	}

	private function searchEndtag($tagname, $code, $tagPos, $ipos){
		$tagcount = 0;
		$endtags = array();

		$endtagpos = $tagPos;
		$regs = array();
		for($i = $ipos + 1; $i < count($this->tags); $i++){
			if(preg_match('|(< ?/ ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i], $regs)){
				array_push($endtags, $regs[1]);
				if($tagcount){
					$tagcount--;
				} else{
					// found endtag
					for($n = 0; $n < count($endtags); $n++){
						$endtagpos = strpos($code, $endtags[$n], $endtagpos + 1);
					}
					$this->tags[$i] = '';
					return array($endtagpos, $i);
				}
			} else{
				if(preg_match('|(< ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i])){
					$tagcount++;
				}
			}
		}
		return array(FALSE, FALSE);
	}

	public static function parseAttribs($attr){
		//remove comment-attribute (should never be seen), and obsolete cachelifetime
		$removeAttribs = array('cachelifetime', 'comment');

		$attribs = '';
		$regs = array();
		preg_match_all('/([^=]+)=[ \t]*("[^"]*")/', $attr, $regs, PREG_SET_ORDER);

		if(!empty($regs)){
			foreach($regs as $f){
				if(!in_array($f[1], $removeAttribs)){
					$attribs .= '"' . trim($f[1]) . '"=>' . trim($f[2]) . ',';
				}
			}
		}
		return rtrim($attribs, ',');
	}

	private function parseTag(&$code, $ipos){
		$tag = $this->tags[$ipos];
		$regs = array();
		//$endTag = false;
		preg_match('%<(/?)we:([[:alnum:]_-]+)([ \t\n\r]*[[:alnum:]_-]+[ \t]*=[ \t]*"[^"]*")*[ \t\n\r]*(/?)(>?)%mi', $tag, $regs);
		$endTag = ($regs[1] === '/');
		if($endTag){
			//there should not be any endtags
			$code = str_replace($tag, '', $code);
			return 1;
		}

		$selfclose = ($regs[4] === '/');
		$gt = $regs[5];
		$tagname = $regs[2];

		//@Lukas: =>384
		if(!$gt){
			return parseError(sprintf(g_l('parser', '[incompleteTag]'), $tagname));
		}
		//tags which need an endtag are not allowed to be selfclosing
		//FIXME: ok or not?
		//$selfclose&=!in_array($tagname, self::$CloseTags);
		preg_match('%</?we:[[:alnum:]_-]+[ \t\n\r]*(.*)' . $regs[4] . $regs[5] . '%msi', $regs[0], $regs);
		$attr = trim($regs[1]);

		//FIXME: remove?!
		if(preg_match('|name[ \t]*=[ \t]*"([^"]*)"|i', $attr, $regs)){
			if(!$regs[1]){
				print parseError(sprintf(g_l('parser', '[name_empty]'), $tagname));
			} else
			if(strlen($regs[1]) > 255){
				print parseError(sprintf(g_l('parser', '[name_to_long]'), $tagname));
			}
		}

		$attribs = self::parseAttribs($attr);

		if(!function_exists('we_tag_' . $tagname)){
			$ret = we_include_tag_file($tagname);
			if($ret !== true){
				return $ret;
			}
		}
		$tagPos = strpos($code, $tag, $this->lastpos);
		$this->lastpos = $tagPos;
		$endeStartTag = $tagPos + strlen($tag);
		if($selfclose){
			$content = '';
		} else{
			list($endTagPos, $endTagNo) = $this->searchEndtag($tagname, $code, $tagPos, $ipos);
			if($endTagPos !== FALSE){
				$endeEndTagPos = strpos($code, '>', $endTagPos) + 1;
				$content = substr($code, $endeStartTag, ($endTagPos - $endeStartTag));
				//only 1 exception: comment tag should be able to contain partly invalid code (e.g. missing attributes etc)
				if(($tagname != 'comment') && (($ipos + 1) < $endTagNo)){
					$tmp = $this->parseTags($content, ($ipos + 1), $endTagNo);
					if(is_string($tmp)){
						//parser-error:
						return $tmp;
					}
				}
			} else{
				//FIXME: remove in 6.4
				//if it is not a selfclosing tag (<we:xx/>),
				//there exists a tagWizzard file, and in this file this tag is stated to be selfclosing
				//NOTE: most custom tags don't have a wizzard file - so this tag must be selfclosing, or have a corresponding closing-tag!
				if(!$selfclose && in_array($tagname, self::$AllKnownTags) && !in_array($tagname, self::$CloseTags)){
					//for now we'll correct this error and keep parsing
					$selfclose = true;
					$content = '';
					unset($endeEndTagPos);
					unset($endTagPos);
					unset($endTagNo);
					//don't break for now.
					parseError(sprintf('Compatibility MODE of parser - Note this will soon be removed!' . "\n" . g_l('parser', '[start_endtag_missing]'), $tagname));
				}else
					return parseError(sprintf(g_l('parser', '[start_endtag_missing]'), $tagname));
			}
		}

		if(PHPLOCALSCOPE){
			$attribs = str_replace('\$', '$', 'array(' . rtrim($attribs, ',') . ')'); //#6330
			//t_e($tag, $tagPos, $endeStartTag, $endTagPos, $ipos, $content,$this->tags);
		} else{
			$attribs = 'array(' . rtrim($attribs, ',') . ')';
		}
		$parseFn = 'we_parse_tag_' . $tagname;
		if(function_exists($parseFn)){
			/* call specific function for parsing this tag
			 * $attribs is the attribs string, $content is content of this tag
			 * return value is parsed again and inserted
			 */
			$content = $parseFn($attribs, $content);
			$code = substr($code, 0, $tagPos) .
				$content .
				substr($code, (isset($endeEndTagPos) ? $endeEndTagPos : $endeStartTag));
		} else
		if(substr($tagname, 0, 2) == "if" && $tagname != "ifNoJavaScript"){
			if(!isset($endeEndTagPos)){
				return parseError(sprintf(g_l('parser', '[selfclosingIf]'), $tagname));
			}
			$code = substr($code, 0, $tagPos) .
				'<?php if(' . self::printTag($tagname, $attribs) . '){ ?>' .
				$content .
				'<?php } ?>' .
				substr($code, $endeEndTagPos);
		} else{
			// Tag besitzt Endtag
			if($content){
				$code = substr($code, 0, $tagPos) . '<?php printElement(' . self::printTag($tagname, $attribs, $content, true) . '); ?>' . substr(
						$code, $endeEndTagPos);
			} else{
				$code = substr($code, 0, $tagPos) . '<?php printElement(' . self::printTag($tagname, $attribs) . '); ?>' . substr(
						$code, (isset($endeEndTagPos) ? $endeEndTagPos : $endeStartTag));
			}
		}
		return (isset($endTagNo) ? ($endTagNo - $ipos) : 1);
	}

	public static function printTag($name, $attribs = '', $content = '', $cslash = false){
		$attr = (is_array($attribs) ? self::printArray($attribs) : $attribs);
		return 'we_tag(\'' . $name . '\'' .
			($attr != '' ? ',' . $attr : ($content != '' ? ',array()' : '')) .
			($content != '' ? ',"' . ($cslash ? addcslashes($content, '"') : $content) . '"' : '') . ')';
	}

	public static function printArray($array){
		$ret = '';
		foreach($array as $key => $val){
			$ret.='\'' . $key . '\'=>\'' . $val . '\',';
		}
		return 'array(' . $ret . ')';
	}

}

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
/*
 * due to parser limits this does not work now
  function we_parse_tag_blockControls($attribs,$content){
  eval('$arr = ' . str_replace('$','\$',$attribs) . ';');
  if (($foo = attributFehltError($arr, 'name', __FUNCTION__)))	return $foo;
  $name = weTag_getParserAttribute("name", $arr);
  return '<?php if(we_tag(\'ifEditmode\')){echo we_tag_blockControls('.$name.'));}?>';
  }
 */

function we_parse_tag_block($attribs, $content){
	$GLOBALS['blkCnt'] = (isset($GLOBALS['blkCnt']) ? $GLOBALS['blkCnt'] + 1 : 0);
	$arr = array();
	eval('$arr = ' . (PHPLOCALSCOPE ? str_replace('$', '\$', $attribs) : $attribs) . ';'); //Bug #6516
	if(($foo = attributFehltError($arr, 'name', __FUNCTION__)))
		return $foo;

	//cleanup content
	while(strpos($content, '\$') !== false) {
		$content = str_replace('\$', '$', $content);
	}
	$blockName = weTag_getParserAttribute('name', $arr);
	$name = str_replace(array('$', '.', '/', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9), '', md5($blockName)) . $GLOBALS['blkCnt'];
	$ctlPre = '<?php if($GLOBALS[\'we_editmode\']){echo we_tag_blockControls(';
	$ctlPost = ');}?>';

	//if(strpos($content,'blockControls')===false){
	if(preg_match('/^< ?(tr|td)/i', trim($content))){
		$content = str_replace(array('=>', '?>'), array('#####PHPCALSSARROW####', '#####PHPENDBRACKET####'), $content);
		$content = preg_replace('|(< ?td[^>]*>)(.*< ?/ ?td[^>]*>)|si', '$1' . $ctlPre . '$block_' . $name . $ctlPost . '$2', $content, 1);
		$content = str_replace(array('#####PHPCALSSARROW####', '#####PHPENDBRACKET####'), array('=>', '?>'), $content);
	} else{
		$content = $ctlPre . '$block_' . $name . $ctlPost . $content;
	}
//	}
	//here postTagName is explicitly needed, because the control-element is not "inside" the block-tag (no block defined/first element) but controls its elements
	return '<?php if(($block_' . $name . '=' . we_tag_tagParser::printTag('block', $attribs) . ')!==false){' . "\n\t" .
		'while(we_condition_tag_block($block_' . $name . ')){?>' . $content . '<?php }}else{?>' .
		$ctlPre . 'array(\'name\'=>we_tag_getPostName("' . $blockName . '"),\'pos\'=>0,\'listSize\'=>0,' .
		'\'ctlShowSelect\'=>' . (weTag_getParserAttribute('showselect', $arr, true, true) ? 'true' : 'false') . ',' .
		'\'ctlShow\'=>' . (int) weTag_getParserAttribute('limit', $arr, 10) . ')' . $ctlPost .
		'<?php }unset($block_' . $name . ');?>';
}

function we_condition_tag_block(&$block){
	//go to next element
	++$block['pos'];
	if($block['pos'] >= $block['listSize']){
		//end of list
		//we need a last add button in editmode
		if($GLOBALS['we_editmode']){
			print printElement(we_tag_blockControls($block));
		}
		//reset data
		unset($GLOBALS['we_position']['block'][$block['name']]);
		$GLOBALS['postTagName'] = $block['lastPostName'];
		return false;
	}
	$blkPreName = 'blk_' . $block['name'] . '_';
	$GLOBALS['postTagName'] = $blkPreName . $block['list'][$block['pos']];

	$GLOBALS['we_position']['block'][$block['name']] = array(
		'position' => $block['pos'] + 1,
		'size' => $block['listSize']);
	return true;
}

function we_tag_block($attribs){
	$name = weTag_getAttribute('name', $attribs);
	$showselect = weTag_getAttribute('showselect', $attribs, true, true);
	$start = weTag_getAttribute('start', $attribs);
	$limit = weTag_getAttribute('limit', $attribs);

	$list = (isset($GLOBALS['lv'])) ? $GLOBALS['lv']->f($name) : $GLOBALS['we_doc']->getElement($name);

	if($list){
		$list = unserialize($list);
		if(is_array($list) && count($list) && ((count($list) - 1) != max(array_keys($list)))){
			//reorder list!
			$list = array_values($list);
			$GLOBALS['we_doc']->setElement($name, serialize($list));
		}
	} else if($start){
		$list = array();
		if($limit && $limit > 0 && $limit < $start){
			$start = $limit;
		}
		for($i = 1; $i <= $start; $i++){
			$list[] = '_' . $i;
		}
	}

	$listlen = count($list);
	if(!$list || $listlen == 0){
		return false;
	}

	$show = 10;
	if(!$GLOBALS['we_editmode']){
		if($limit > 0 && $listlen > $limit){
			$listlen = $limit;
		}
	} else{
		if($limit && $limit > 0){
			$diff = $limit - $listlen;
			if($diff > 0){
				$show = min($show, $diff);
			} else{
				$show = 0;
			}
		}
	}

	return array(
		'name' => $name,
		'list' => $list,
		'listSize' => $listlen,
		'ctlShow' => $show,
		'ctlShowSelect' => $showselect,
		'pos' => -1,
		'lastPostName' => isset($GLOBALS['postTagName']) ? $GLOBALS['postTagName'] : '',
	);
}

function we_tag_blockControls($attribs, $content = ''){
	//if in listview no Buttons are shown!
	if(!$GLOBALS['we_editmode'] || isset($GLOBALS['lv'])){
		return '';
	}
	if(!isset($attribs['ctlName'])){
		$attribs['ctlName'] = md5(str_replace('.', '', uniqid('', true))); // #6590, changed from: uniqid(time())
	}

	if($attribs['pos'] < $attribs['listSize']){
		$tabArray = array();
		if($attribs['ctlShowSelect'] && $attribs['ctlShow'] > 0){
			$jsSelector = $attribs['pos'] . ",document.we_form.elements['" . $attribs['ctlName'] . "_" . $attribs['pos'] . "'].options[document.we_form.elements['" . $attribs['ctlName'] . "_" . $attribs['pos'] . "'].selectedIndex].text";
			$tabArray[] = we_button::create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','" . $attribs['name'] . "'," . $jsSelector . ")", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));

			$selectb = '<select name="' . $attribs['ctlName'] . '_' . $attribs['pos'] . '">';
			for($j = 0; $j < $attribs['ctlShow']; $j++){
				$selectb .= '<option value="' . ($j + 1) . '">' . ($j + 1) . '</option>';
			}
			$selectb .= '</select>';
			$tabArray[] = $selectb;
		} else{
			$tabArray[] = we_button::create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "',1)", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));
			$jsSelector = '1';
		}
		$tabArray[] = (($attribs['pos'] > 0) ?
				//enabled upBtn
				we_button::create_button('image:btn_direction_up', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('up_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "'," . $jsSelector . ")") :
				//disabled upBtn
				we_button::create_button('image:btn_direction_up', '', true, -1, -1, '', '', true));
		$tabArray[] = (($attribs['pos'] == $attribs['listSize'] - 1) ?
				//disabled downBtn
				we_button::create_button('image:btn_direction_down', '', true, -1, -1, '', '', true) :
				//enabled downBtn
				we_button::create_button('image:btn_direction_down', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('down_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "'," . $jsSelector . ")"));
		$tabArray[] = we_button::create_button('image:btn_function_trash', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','" . $attribs['name'] . "','" . $attribs['pos'] . "','" . $GLOBALS['postTagName'] . "',1)");

		return we_button::create_button_table($tabArray, 5);
	} else{

		if($attribs['ctlShowSelect'] && $attribs['ctlShow'] > 0){
			$selectb = '<select name="' . $attribs['ctlName'] . '_00">';
			for($j = 1; $j <= $attribs['ctlShow']; $j++){
				$selectb .= '<option value="' . $j . '">' . $j . '</option>';
			}
			$selectb .= '</select>';
			$plusbut = we_button::create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','" . $attribs['name'] . "',document.we_form.elements['" . $attribs['ctlName'] . "_00'].options[document.we_form.elements['" . $attribs['ctlName'] . "_00'].selectedIndex].text);", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));

			$plusbut = we_button::create_button_table(array($plusbut, $selectb));
		} else{
			$plusbut = we_button::create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','" . $attribs['name'] . "',1)", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));
		}

		return '<input type="hidden" name="we_' . $GLOBALS['we_doc']->Name . '_list[' . $attribs['name'] . ']" value="' . htmlentities(
				serialize(isset($attribs['list']) ? $attribs['list'] : array())) . '"><input type="hidden" name="we_' . $GLOBALS['we_doc']->Name . '_list[' . $attribs['name'] . '#content]" value="' .
			$content . '" />' . $plusbut;
	}
}

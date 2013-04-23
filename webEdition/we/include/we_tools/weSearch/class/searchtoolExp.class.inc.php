<?php

/**
 * webEdition CMS
 *
 * $Rev: 5039 $
 * $Author: mokraemer $
 * $Date: 2012-10-31 01:13:32 +0100 (Wed, 31 Oct 2012) $
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
  / only used for direct expression input ( exp: ) from old search of slavko
 */

class searchtoolExp extends we_search{

	var $Operators = array(
		'!=' => '<>', '=' => '=', '<>' => '<>', '<' => '<', '>' => '>'
	);
	var $FieldMap = array(
		'id' => 'ID',
		'path' => 'Path',
		'text' => 'Text',
		'doctype' => 'DocType',
		'category' => 'Category',
		'contenttype' => 'ContentType',
		'isfolder' => 'IsFolder',
		'templateid' => 'TemplateID',
		'parentid' => 'ParentID',
		'tableid' => 'TableID',
		'mastertemplateid' => 'MasterTemplateID'
	);

	function getSearchResults($keyword, $options, $res_num = 0){

		$_exp_pos = strpos($keyword, 'exp:');
		$_items = array();

		$keyword = trim($keyword);

		if($_exp_pos !== false){
			$_items = $this->evaluateExp(substr($keyword, $_exp_pos + 4), $options, $res_num);
			$keyword = trim(substr($keyword, 0, $_exp_pos));
		}

		return $_items;
	}

	function evaluateExp($keyword, $options, $res_num = 0){

		$keyword = $this->normalize($keyword);
		$_tokens = $this->tokenize($keyword);
		$_tokens = $this->translateOperators($_tokens);
		$_tables = $options;
		$_condition = $this->constructCondition($_tokens);

		$_result = array();

		foreach($_tables as $_table){

			$_query = 'SELECT * FROM ' . $_table . ' ' . $_condition;
			$this->db->query($_query);

			while($this->next_record()) {
				$_result[] = array_merge(array(
					'Table' => $_table
					), $this->db->Record);
			}
		}

		return $_result;
	}

	function fixFieldNames($name){

		foreach($this->FieldMap as $_k => $_v){
			if(preg_match('%^' . $_k . '$%i', $name)){
				return $_v;
			}
		}

		return $name;
	}

	function normalize($keyword){
		foreach($this->Operators as $_operator){
			$keyword = preg_replace('/[ ]*' . $_operator . '[ ]*/i', $_operator, $keyword);
		}
		return $keyword;
	}

	function tokenize($keyword){

		$array = array();

		$array['AND'] = array();
		$array['OR'] = array();
		$array['AND NOT'] = array();

		$ident = 'AND';
		$array[$ident][0] = '';

		$word = '';

		for($i = 0; $i < strlen($keyword); $i++){

			$word .= $keyword[$i];

			if($keyword[$i] == ' '){

				switch(strtolower(trim($word))){
					case 'and' :
						$ident = 'AND';
						$array[$ident][] = '';
						break;

					case 'or' :
						$ident = 'OR';
						$array[$ident][] = '';
						break;

					case 'not' :
						$ident = 'AND NOT';
						$array[$ident][] = '';
						break;
					default :
						$_count = count($array[$ident]) - 1;
						$array[$ident][$_count] .= $word;
				}
				$word = '';
			}
		}

		$array[$ident][count($array[$ident]) - 1] .= $word;

		return $array;
	}

	function translateOperators($tokens){

		$_tokens = $tokens;
		foreach($_tokens as $_lop => $_slots){
			foreach($_slots as $_key => $_value){

				$tokens[$_lop][$_key] = $this->getExpression($_value);
			}
		}
		return $tokens;
	}

	function replaceSpecChars($string){
		$string = trim($string);
		$string = str_replace('*', '%', $string);
		$string = trim($string, '"');
		$string = trim($string, "'");
		return $string;
	}

	function getExpression($string){
		$_arr = array();

		foreach($this->Operators as $_k => $_v){
			if(preg_match('_' . $_k . '_', $string)){
				$_arr = explode($_k, $string);
				$_expr = array(
					'operand1' => trim($this->fixFieldNames($_arr[0])),
					'operator' => trim($_v),
					'operand2' => trim($this->replaceSpecChars(stripslashes($_arr[1])))
				);

				if($_expr['operator'] == '=' && strpos($_expr['operand2'], '%') !== false){
					$_expr['operator'] = 'LIKE';
				}

				if(($_expr['operator'] == '!=' || $_expr['operator'] == '<>') && strpos($_expr['operand2'], '%') !== false){
					$_expr['operator'] = 'NOT LIKE';
				}

				$this->getTransaltedExpression($_expr);

				if(!$this->isField($_expr['operand1'])){
					$_expr['operand1'] = implode('', $_expr);
					unset($_expr['operator']);
					unset($_expr['operand2']);
				}

				break;
			}
		}

		if(!isset($_expr)){
			$_expr['operand1'] = $string;
		}

		return $_expr;
	}

	function getTransaltedExpression(&$_expr){

		if(($_expr['operand1'] == 'DocType')){
			if(strpos($_expr['operand2'], '\*') !== false){
				$_expr['operand2'] = f(
					'SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . str_replace(
						"*", "%", $_expr['operand2']) . '";', 'ID', new DB_WE());
			} else{
				$_expr['operand2'] = f(
					'SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . $_expr['operand2'] . '";', 'ID', new DB_WE());
			}
			// if operand2 is empty make some impossible condition
			if(empty($_expr['operand2']) && ($_expr['operator'] == 'LIKE' || $_expr['operator'] == '=')){
				$_expr['operand2'] = md5(uniqid(__FUNCTION__, true));
			}
		}

		if(($_expr['operand1'] == 'Category')){
			$_expr['operand2'] = ',' . f(
					'SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE Text="' . $_expr['operand2'] . '";', 'ID', new DB_WE()) . ',';
			if($_expr['operator'] == '='){
				$_expr['operator'] = 'LIKE';
			}
			if($_expr['operator'] == '!='){
				$_expr['operator'] = 'NOT LIKE';
			}
			// if operand2 is empty make some impossible condition
			if(empty($_expr['operand2']) && $_expr['operator'] == 'LIKE'){
				$_expr['operand2'] = md5(uniqid(__FUNCTION__, true));
			}
		}

		if(strpos($_expr['operand2'], '\*') !== false){
			$_expr['operator'] = 'LIKE';
			$_expr['operand2'] = str_replace("*", "%", $_expr['operand2']);
		}
	}

	function isField($field){
		return in_array($field, $this->FieldMap);
	}

	function getTables($options){
		$_tables = array();
		foreach($options as $option => $value){
			if($value && $option == FILE_TABLE && we_hasPerm('CAN_SEE_DOCUMENTS')){
				$_tables[] = FILE_TABLE;
			}
			if($value && $option == TEMPLATES_TABLE && we_hasPerm('CAN_SEE_TEMPLATES')){
				$_tables[] = TEMPLATES_TABLE;
			}
			if(defined('OBJECT_FILES_TABLE') && $value && $option == OBJECT_FILES_TABLE && we_hasPerm(
					'CAN_SEE_OBJECTFILES')){
				$_tables[] = OBJECT_FILES_TABLE;
			}
			if(defined('OBJECT_TABLE') && $value && $option == OBJECT_TABLE && we_hasPerm('CAN_SEE_OBJECTS')){
				$_tables[] = OBJECT_TABLE;
			}
		}
		return $_tables;
	}

	function constructCondition(&$_tokens){

		$_condition = '';
		foreach($_tokens as $_log => $_token){
			$_word = array();
			$_conditions = array();
			foreach($_token as $_op){
				if(count($_op) < 3){
					$_word[] = ' ' . $_op['operand1'] . ' ';
				} else{
					$_word[] = $_op['operand1'] . ' ' . $_op['operator'] . ' "' . addslashes($_op['operand2']) . '"';
				}
			}
			if(!empty($_word)){
				$_conditions[] = implode(" $_log ", $_word);
			}

			if(count($_conditions)){
				if(empty($_condition)){
					$_condition .= implode(" $_log ", $_conditions);
				} else{
					$_condition .= " $_log " . implode(" $_log ", $_conditions);
				}
			}
		}

		if($_condition != ''){
			$_condition = " WHERE $_condition ";
		}

		return $_condition;
	}

}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5473 $
 * $Author: arminschulz $
 * $Date: 2012-12-29 17:40:27 +0100 (Sat, 29 Dec 2012) $
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
 * @package    webEdition_model
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Class we_category
 *
 * Provides functions for handling webEdition category.
 */
class we_category extends weModelBase{

	var $ClassName = __CLASS__;
	var $ContentType = "category";

	function __construct(){
		parent::__construct(CATEGORY_TABLE);
	}

	function we_save(){
		if(isset($this->Catfields) && is_array($this->Catfields)){
			$this->Catfields = serialize($this->Catfields);
		}

		weModelBase::save();
	}

	static function getCatSQLTail($catCSV = '', $table = FILE_TABLE, $catOr = false, $db = '', $fieldName = 'Category', $getParentCats = true, $categoryids = ''){
		$db = $db ? $db : new DB_WE();
		$catCSV = trim($catCSV, ' ,');
		$pre = ' FIND_IN_SET("';
		$post = '",' . $table . '.' . $fieldName . ') ';

		$idarray = array();
		$folders = array();
		if($categoryids){
			$idarray2 = array_unique(array_map('trim', explode(',', trim($categoryids, ','))));
			$db->query('SELECT ID,IsFolder,Path FROM ' . CATEGORY_TABLE . ' WHERE ID IN(' . implode(',', $idarray2) . ')');
			while($db->next_record()) {
				if($db->f('IsFolder')){
					//all folders need to be searched in deep
					$catCSV.=',' . $db->f('Path');
				} else{
					$idarray[] = $db->f('ID');
				}
			}
		}

		if($catCSV){
			$idarray1 = array_unique(array_map('trim', explode(',', trim($catCSV, ','))));
			foreach($idarray1 as $cat){
				$cat = '/' . trim($cat, '/ ');
				$isFolder = 0;
				$tmp = array();
				$db->query('SELECT ID, IsFolder FROM ' . CATEGORY_TABLE . ' WHERE Path LIKE "' . $db->escape($cat) . '/%" OR Path="' . $db->escape($cat) . '"');
				while($db->next_record()) {
					$tmp[] = $db->f('ID');
					$isFolder|=$db->f('IsFolder');
				}
				if($isFolder){
					$folders[] = $tmp;
				} else{
					$idarray = array_merge($idarray, $tmp);
				}
			}
		}
		if(empty($idarray) && empty($folders)){
			return '';
		}

		$where = array();
		if(!empty($idarray)){
			$where[] = $pre . implode($post . ($catOr ? 'OR' : 'AND') . $pre, array_unique($idarray)) . $post;
		}
		if(!empty($folders)){
			foreach($folders as &$cur){
			$where[] = '('.$pre . implode($post . 'OR' . $pre, $cur) . $post.')';
			}
			unset($cur);
		}

		return /*(empty($where) ?
				' AND ' . $table . '.' . $fieldName . ' = "-1" ' :*/
				' AND (' . implode(($catOr ? ' OR ' : ' AND '), $where) . ' )';
	}

}

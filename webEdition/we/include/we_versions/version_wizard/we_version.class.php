<?php

/**
 * webEdition CMS
 *
 * $Rev: 5587 $
 * $Author: mokraemer $
 * $Date: 2013-01-17 23:44:20 +0100 (Thu, 17 Jan 2013) $
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
class we_version{

	function todo($data, $printIt = true){

		$db = new DB_WE();

		if($printIt){
			$_newLine = count($_SERVER['argv']) ? "\n" : "<br>\n";
		}

		//		if($data["type"] == "version_delete"){
		//
		//			weVersions::deleteVersion($data["ID"]);
		//			$_SESSION['weS']['versions']['logDeleteIds'][$data["ID"]]['Version'] = $data["version"];
		//			$_SESSION['weS']['versions']['logDeleteIds'][$data["ID"]]['Text'] = $data["text"];
		//			$_SESSION['weS']['versions']['logDeleteIds'][$data["ID"]]['ContentType'] = $data["contenttype"];
		//			$_SESSION['weS']['versions']['logDeleteIds'][$data["ID"]]['Path'] = $data["path"];
		//			$_SESSION['weS']['versions']['logDeleteIds'][$data["ID"]]['documentID'] = $data["documentID"];
		//
		//		}
		//
		//		else{


		switch($data["type"]){
			case "version_reset" :
				$publish = isset($_REQUEST['reset_doPublish']) && $_REQUEST['reset_doPublish'] ? 1 : 0;
				weVersions::resetVersion($data["ID"], $data["version"], $publish);

				$_SESSION['weS']['versions']['logResetIds'][$data["ID"]]['Text'] = $data["text"];
				$_SESSION['weS']['versions']['logResetIds'][$data["ID"]]['ContentType'] = $data["contenttype"];
				$_SESSION['weS']['versions']['logResetIds'][$data["ID"]]['Path'] = $data["path"];
				$_SESSION['weS']['versions']['logResetIds'][$data["ID"]]['Version'] = $data["version"];
				$_SESSION['weS']['versions']['logResetIds'][$data["ID"]]['documentID'] = $data["documentID"];

				break;

			default :
				return false;
		}

		//}
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding documents
	 *
	 * @return array
	 * @param string $btype "rebuild_all" or "rebuild_filter"
	 * @param string $categories csv value of category IDs
	 * @param boolean $catAnd true if AND should be used for more than one categories (default=OR)
	 * @param string $doctypes csv value of doctypeIDs
	 * @param string $folders csv value of directory IDs
	 * @param boolean $maintable if the main table should be rebuilded
	 * @param boolean $tmptable if the tmp table should be rebuilded
	 * @param int $templateID ID of a template (All documents of this template should be rebuilded)
	 */
	function getDocuments($type = 'delete_versions', $version){
		switch($type){
			case "delete_versions" :
				return we_version::getDocumentsDelete($version);
				break;
			case "reset_versions" :
				return we_version::getDocumentsReset($version);
				break;
		}
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding all documents and templates (Called from getDocuments())
	 *
	 * @return array
	 * @param boolean $maintable if the main table should be rebuilded
	 * @param boolean $tmptable if the tmp table should be rebuilded
	 */
	function getDocumentsDelete($version){
		$data = array();
		if(we_hasPerm("ADMINISTRATOR")){

			$GLOBALS['DB_WE']->query($_SESSION['weS']['versions']['query']);
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"ID" => $GLOBALS['DB_WE']->f("ID"),
					"documentID" => $GLOBALS['DB_WE']->f("documentID"),
					"type" => "version_delete",
					"version" => $GLOBALS['DB_WE']->f("version"),
					"timestamp" => $GLOBALS['DB_WE']->f("timestamp"),
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"table" => $GLOBALS['DB_WE']->f("documentTable"),
					"contenttype" => $GLOBALS['DB_WE']->f("ContentType"),
					"text" => $GLOBALS['DB_WE']->f("Text")
				));
			}
			unset($_SESSION['weS']['versions']['query']);
		}
		return $data;
	}

	function getDocumentsReset($version){
		$data = array();
		if(we_hasPerm("ADMINISTRATOR")){

			$GLOBALS['DB_WE']->query($_SESSION['weS']['versions']['query']);
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"ID" => $GLOBALS['DB_WE']->f("ID"),
					"documentID" => $GLOBALS['DB_WE']->f("documentID"),
					"type" => "version_reset",
					"version" => $GLOBALS['DB_WE']->f("version"),
					"timestamp" => $GLOBALS['DB_WE']->f("timestamp"),
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"table" => $GLOBALS['DB_WE']->f("documentTable"),
					"contenttype" => $GLOBALS['DB_WE']->f("ContentType"),
					"text" => $GLOBALS['DB_WE']->f("Text")
				));
			}
			unset($_SESSION['weS']['versions']['query']);
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding metadata
	 *
	 * @return array
	 * @param array $metaFields array which meta fields should be rebuilt
	 * @param boolean $onlyEmpty if this is true, only empty fields will be imported
	 * @param array $metaFolders array with folder Ids
	 */
	function getMetadata($metaFields, $onlyEmpty, $metaFolders){

		if(!is_array($metaFolders)){
			$metaFolders = makeArrayFromCSV($metaFolders);
		}
		$data = array();
		if(we_hasPerm("REBUILD_META")){
			$foldersQuery = count($metaFolders) ? ' AND ParentId IN(' . implode(",", $metaFolders) . ') ' : '';
			$GLOBALS['DB_WE']->query(
				"SELECT ID,path FROM " . FILE_TABLE . " WHERE ContentType='image/*' AND (Extension='.jpg' OR Extension='jpeg' OR Extension='wbmp') $foldersQuery");
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"id" => $GLOBALS['DB_WE']->f("ID"),
					"type" => "metadata",
					"onlyEmpty" => $onlyEmpty,
					"path" => $GLOBALS['DB_WE']->f("path"),
					"metaFields" => $metaFields
				));
			}
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding all documents and templates (Called from getDocuments())
	 *
	 * @return array
	 */
	function getTemplates(){
		$data = array();
		if(we_hasPerm("REBUILD_TEMPLATES")){
			$GLOBALS['DB_WE']->query("SELECT ID,ClassName,Path FROM " . TEMPLATES_TABLE . " ORDER BY ID");
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"id" => $GLOBALS['DB_WE']->f("ID"),
					"type" => "template",
					"cn" => $GLOBALS['DB_WE']->f("ClassName"),
					"mt" => 0,
					"tt" => 0,
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"it" => 0
				));
			}
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding filtered documents (Called from getDocuments())
	 *
	 * @return array
	 * @param string $categories csv value of category IDs
	 * @param boolean $catAnd true if AND should be used for more than one categories (default=OR)
	 * @param string $doctypes csv value of doctypeIDs
	 * @param string $folders csv value of directory IDs
	 * @param int $templateID ID of a template (All documents of this template should be rebuilded)
	 */
	function getFilteredDocuments($categories, $catAnd, $doctypes, $folders, $templateID){
		$data = array();
		if(we_hasPerm("REBUILD_FILTERD")){
			$_cat_query = "";
			$_doctype_query = "";
			$_folders_query = "";
			$_template_query = "";

			if($categories){
				$_foo = makeArrayFromCSV($categories);
				$tmp = array();
				foreach($_foo as $catID){
					$tmp[] = " Category LIKE '%," . intval($catID) . ",%'";
				}
				$_cat_query = '(' . implode(' ' . ($catAnd ? 'AND' : 'OR') . ' ', $tmp) . ')';
			}
			if($doctypes){
				$_foo = makeArrayFromCSV($doctypes);
				$tmp = array();
				foreach($_foo as $doctypeID){
					$tmp .= " Doctype = '" . $GLOBALS['DB_WE']->escape($doctypeID) . "'";
				}
				$_doctype_query = '(' . implode(' OR ', $tmp) . ')';
			}
			if($folders){
				$_foo = makeArrayFromCSV($folders);
				$_foldersList = array();
				foreach($_foo as $folderID){
					$_foldersList[] = makeCSVFromArray(we_util::getFoldersInFolder($folderID));
				}
				$_folders_query = '( ParentID IN(' . implode(',', $_foldersList) . ') )';
			}

			if($templateID){

				$arr = we_rebuild::getTemplAndDocIDsOfTemplate($templateID);

				if(count($arr["templateIDs"])){
					$where = "";
					foreach($arr["templateIDs"] as $tid){
						$where .= " ID=" . intval($tid) . " OR ";
					}
					$where = substr($where, 0, strlen($where) - 3);
					$where = '(' . $where . ')';

					$GLOBALS['DB_WE']->query(
						"SELECT ID,ClassName,Path FROM " . TEMPLATES_TABLE . " WHERE $where ORDER BY ID");
					while($GLOBALS['DB_WE']->next_record()) {
						array_push(
							$data, array(
							"id" => $GLOBALS['DB_WE']->f("ID"),
							"type" => "template",
							"cn" => $GLOBALS['DB_WE']->f("ClassName"),
							"mt" => 0,
							"tt" => 0,
							"path" => $GLOBALS['DB_WE']->f("Path"),
							"it" => 0
						));
					}

					$_template_query = " TemplateID=" . intval($templateID) . " OR ";
					foreach($arr["templateIDs"] as $tid){
						$_template_query .= " TemplateID=" . intval($tid) . " OR ";
					}
					// remove last OR
					$_template_query = substr(
						$_template_query, 0, strlen($_template_query) - 3);
					$_template_query = '(' . $_template_query . ')';
				} else{
					$_template_query = "( TemplateID='$templateID' )";
				}
			}

			$query = ($_cat_query ? " AND $_cat_query " : "") . ($_doctype_query ? " AND $_doctype_query " : "") . ($_folders_query ? " AND $_folders_query " : "") . ($_template_query ? " AND $_template_query " : "");

			$GLOBALS['DB_WE']->query(
				"SELECT ID,ClassName,Path FROM " . FILE_TABLE . " WHERE IsDynamic=0 AND Published > 0 AND ContentType='text/webedition' $query ORDER BY ID");
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"id" => $GLOBALS['DB_WE']->f("ID"),
					"type" => "document",
					"cn" => $GLOBALS['DB_WE']->f("ClassName"),
					"mt" => 0,
					"tt" => 0,
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"it" => 0
				));
			}
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding the OBJECTFILES_TABLE
	 *
	 * @return array
	 */
	function getObjects(){
		$data = array();
		if(we_hasPerm("REBUILD_OBJECTS")){
			$GLOBALS['DB_WE']->query(
				"SELECT ID,ClassName,Path FROM " . OBJECT_FILES_TABLE . " WHERE Published > 0 ORDER BY ID");
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"id" => $GLOBALS['DB_WE']->f("ID"),
					"type" => "object",
					"cn" => $GLOBALS['DB_WE']->f("ClassName"),
					"mt" => 0,
					"tt" => 0,
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"it" => 0
				));
			}
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding the INDEX_TABLE
	 *
	 * @return array
	 */
	function getNavigation(){
		$data = array();
		if(we_hasPerm("REBUILD_NAVIGATION")){
			$GLOBALS['DB_WE']->query("SELECT ID,Path FROM " . NAVIGATION_TABLE . " WHERE IsFolder=0 ORDER BY ID");
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"id" => $GLOBALS['DB_WE']->f("ID"),
					"type" => "navigation",
					"cn" => "weNavigation",
					"mt" => 0,
					"tt" => 0,
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"it" => 0
				));
			}
			array_push(
				$data, array(
				"id" => 0,
				"type" => "navigation",
				"cn" => "weNavigation",
				"mt" => 0,
				"tt" => 0,
				"path" => $GLOBALS['DB_WE']->f("Path"),
				"it" => 0
			));
		}

		if(isset($_REQUEST['rebuildStaticAfterNavi']) && $_REQUEST['rebuildStaticAfterNavi'] == 1){
			$data2 = we_version::getFilteredDocuments('', '', '', '', '');
			$data = array_merge($data, $data2);
		}

		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding the INDEX_TABLE
	 *
	 * @return array
	 */
	function getIndex(){
		$data = array();
		if(we_hasPerm("REBUILD_INDEX")){
			$GLOBALS['DB_WE']->query(
				"SELECT ID,ClassName,Path FROM " . FILE_TABLE . " WHERE Published > 0 AND IsSearchable='1' ORDER BY ID");
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"id" => $GLOBALS['DB_WE']->f("ID"),
					"type" => "document",
					"cn" => $GLOBALS['DB_WE']->f("ClassName"),
					"mt" => 0,
					"tt" => 0,
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"it" => 1
				));
			}
			if(defined("OBJECT_FILES_TABLE")){
				$GLOBALS['DB_WE']->query(
					"SELECT ID,ClassName,Path FROM " . OBJECT_FILES_TABLE . " WHERE Published > 0 ORDER BY ID");
				while($GLOBALS['DB_WE']->next_record()) {
					array_push(
						$data, array(
						"id" => $GLOBALS['DB_WE']->f("ID"),
						"type" => "object",
						"cn" => $GLOBALS['DB_WE']->f("ClassName"),
						"mt" => 0,
						"tt" => 0,
						"path" => $GLOBALS['DB_WE']->f("Path"),
						"it" => 1
					));
				}
			}
			$GLOBALS['DB_WE']->query("DELETE FROM " . INDEX_TABLE);
		}
		return $data;
	}

	/**
	 * Create and returns data Array with IDs and other information for the fragmment class for rebuilding thumbnails
	 *
	 * @return array
	 * @param string $thumbs csv value of IDs which thumbs to create
	 * @param string $thumbsFolders csv value of directory IDs => Create Thumbs for images in these directories.
	 */
	function getThumbnails($thumbs = "", $thumbsFolders = ""){
		$data = array();
		if(we_hasPerm("REBUILD_THUMBS")){
			$_folders_query = "";
			if($thumbsFolders){
				$_foo = makeArrayFromCSV($thumbsFolders);
				$_foldersList = array();
				foreach($_foo as $folderID){
					$_foldersList[] = makeCSVFromArray(we_util::getFoldersInFolder($folderID));
				}
				$_folders_query = '( ParentID IN(' . implode(',', $_foldersList) . ') )';
			}
			$GLOBALS['DB_WE']->query(
				"SELECT ID,ClassName,Path,Extension FROM " . FILE_TABLE . " WHERE ContentType='image/*'" . ($_folders_query ? " AND $_folders_query " : "") . " ORDER BY ID");
			while($GLOBALS['DB_WE']->next_record()) {
				array_push(
					$data, array(
					"id" => $GLOBALS['DB_WE']->f("ID"),
					"type" => "thumbnail",
					"cn" => $GLOBALS['DB_WE']->f("ClassName"),
					"thumbs" => $thumbs,
					"extension" => $GLOBALS['DB_WE']->f("Extension"),
					"mt" => 0,
					"tt" => 0,
					"path" => $GLOBALS['DB_WE']->f("Path"),
					"it" => 0
				));
			}
		}
		return $data;
	}

}

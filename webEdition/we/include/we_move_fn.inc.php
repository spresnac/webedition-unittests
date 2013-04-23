<?php

/**
 * webEdition CMS
 *
 * $Rev: 5782 $
 * $Author: mokraemer $
 * $Date: 2013-02-09 22:19:57 +0100 (Sat, 09 Feb 2013) $
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
$notprotect = isset($GLOBALS["NOT_PROTECT"]) && $GLOBALS["NOT_PROTECT"] && (!isset($_REQUEST["NOT_PROTECT"]));

if(!$notprotect){
	we_html_tools::protect();
}

function moveTreeEntries($dontMoveClassFolders = false){
	return 'var obj = top.treeData;
		var cont = new top.container();
		for(var i=1;i<=obj.len;i++){
		  if(obj[i].checked!=1 ' . ($dontMoveClassFolders ? ' || obj[i].parentid==0' : '') . '){
				if(obj[i].parentid != 0){
					if(!parentChecked(obj[i].parentid)){
						cont.add(obj[i]);
					}
				}else{
					cont.add(obj[i]);
				}
			}
		}
		top.treeData = cont;
		top.drawTree();
		function parentChecked(start){
			var obj = top.treeData;
			for(var i=1;i<=obj.len;i++){
				if(obj[i].id == start){
					if(obj[i].checked==1){
						return true;
					} else if(obj[i].parentid != 0){
						parentChecked(obj[i].parentid);
					}
				}
			}
			return false;
		}';
}

function checkMoveItem($targetDirectoryID, $id, $table, &$items2move){
	$DB_WE = new DB_WE();
	// check if entry is a folder
	$row = getHash('SELECT Path, Text, IsFolder FROM ' . $DB_WE->escape($table) . ' WHERE  ID=' . intval($id), $DB_WE);
	if(empty($row) || $row["IsFolder"]){
		return -1;
	}

	$text = $row["Text"];
	$temp = explode("/", $row["Path"]);
	$rootdir = (count($temp) < 2 ? '/' : '/' . $temp[1]);

	// add the item to the item names which could be moved
	$items2move[] = $text;

	$DB_WE->query('SELECT Text,Path FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($targetDirectoryID));
	while($DB_WE->next_record()) {
		// check if there is a item with the same name in the target directory
		if(in_array($DB_WE->f('Text'), $items2move)){
			return -2;
		}

		if(defined("OBJECT_TABLE") && $table == OBJECT_FILES_TABLE){
			// check if class directory is the same
			if(substr($DB_WE->f('Path'), 0, strlen($rootdir) + 1) != $rootdir . '/'){
				return -3;
			}
		}
	}
	return 1;
}

function moveItem($targetDirectoryID, $id, $table, &$notMovedItems){

	$DB_WE = new DB_WE();

	if(!$id){
		return false;
	}

	$table = addslashes($table);

	// get information about the target directory
	if(defined("OBJECT_TABLE") && $table == OBJECT_TABLE && !$targetDirectoryID){
		return false;
	} elseif($targetDirectoryID){
		$row = getHash('SELECT IsFolder,Path,ID FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($targetDirectoryID), $DB_WE);
		if(empty($row)|| !$row["IsFolder"]){
			return false;
		}
		$newPath = $row['Path'];
		$parentID = $row['ID'];
	} else{
		$newPath = "";
		$parentID = 0;
	}

	@set_time_limit(30);

	// move Templates
	switch($table){
		case TEMPLATES_TABLE:
			/*
			  // get information about the template which has to be moved
			  $row = getHash("SELECT Text,Path,IsFolder,Icon FROM ".$table." WHERE ID=".intval($id),$DB_WE);
			  $fileName = $row['Text'];
			  $oldPath = $row['Path'];
			  $isFolder = ($row["IsFolder"] == 1 ? true : false);
			  $icon = $row['Icon'];
			  $item = array(
			  'ID'	=> $id,
			  'Text'	=> $fileName,
			  'Path'	=> $oldPath,
			  'Icon'	=> $icon,
			  );

			  if(sizeof($row) == 0 || $isFolder) {
			  array_push($notMovedItems, $item);
			  return false;
			  }

			  // move template file
			  if(!file_exists(TEMPLATES_PATH.$oldPath)) {
			  array_push($notMovedItems, $item);
			  return false;
			  }

			  if(!copy(TEMPLATES_PATH.$oldPath, TEMPLATES_PATH.$newPath."/".$fileName)) {
			  array_push($notMovedItems, $item);
			  return false;
			  }

			  if(!unlink(TEMPLATES_PATH.$oldPath)) {
			  array_push($notMovedItems, $item);
			  return false;
			  }

			  // update table
			  $q = "UPDATE ".$table." SET ParentID=".$parentID.", Path='".$newPath."/".$fileName."' WHERE ID=".intval($id);
			  $DB_WE->query($q);

			  return true;
			 */
			// bugfix 0001643
			$_template = new we_template();
			$_template->initByID($id, $_template->Table);
			$_template->ParentID = $targetDirectoryID;
			if(!$_template->save()){
				$notMovedItems[] = array(
					'ID' => $_template->ID,
					'Text' => $_template->Text,
					'Path' => $_template->Path,
					'Icon' => $_template->Icon
				);
				return false;
			}
			return true;
		// move documents
		case FILE_TABLE:

			// get information about the document which has to be moved
			$row = getHash('SELECT Text,Path,Published,IsFolder,Icon,ContentType FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
			$fileName = $row['Text'];
			$oldPath = $row['Path'];
			$isPublished = ($row['Published'] > 0 ? true : false);
			$isFolder = ($row["IsFolder"] == 1 ? true : false);
			$icon = $row['Icon'];
			$item = array('ID' => $id, 'Text' => $fileName, 'Path' => $oldPath, 'Icon' => $icon);
			if(empty($row)|| $isFolder){
				$notMovedItems[] = $item;
				return false;
			}

			// move document file
			if(!file_exists($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $oldPath)){
				array_push($notMovedItems, $item);
				return false;
			}
			if(!copy(
					$_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $oldPath, $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $newPath . "/" . $fileName)){
				$notMovedItems[] = $item;
				return false;
			}
			if(!unlink($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . $oldPath)){
				$notMovedItems[] = $item;
				return false;
			}

			// move published document file
			if($isPublished){
				if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $oldPath)){
					$notMovedItems[] = $item;
					return false;
				}
				if(!copy($_SERVER['DOCUMENT_ROOT'] . $oldPath, $_SERVER['DOCUMENT_ROOT'] . $newPath . "/" . $fileName)){
					$notMovedItems[] = $item;
					return false;
				}
				if(!unlink($_SERVER['DOCUMENT_ROOT'] . $oldPath)){
					$notMovedItems[] = $item;
					return false;
				}
			}

			$version = new weVersions();
			if(in_array($row['ContentType'], $version->contentTypes)){
				$object = weContentProvider::getInstance($row['ContentType'], $id, $table);
				$version_exists = $version->getLastEntry($id, $table);
				$tempOldParentID = $object->ParentID;
				$tempNewParentID = $parentID;
				$tempOldPath = $object->Path;
				$tempNewPath = "" . $newPath . "/" . $fileName;
				$object->Path = $tempNewPath;
				$object->ParentID = $tempNewParentID;
				if(empty($version_exists)){
					$object->Path = $tempOldPath;
					$object->ParentID = $tempOldParentID;
					$version->saveVersion($object);
					$object->Path = $tempNewPath;
					$object->ParentID = $tempNewParentID;
				}
				$version->saveVersion($object);
			}

			// update table
			$DB_WE->query('UPDATE ' . $DB_WE->escape($table) . ' SET ParentID=' . intval($parentID) . ", Path='" . $DB_WE->escape($newPath) . "/" . $DB_WE->escape($fileName) . "' WHERE ID=" . intval($id));

			return true;

		// move Objects
		case (defined("OBJECT_TABLE") ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):

			// get information about the object which has to be moved
			$row = getHash('SELECT TableID,Path,Text,IsFolder,Icon,ContentType FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);
			$tableID = $row['TableID'];
			$oldPath = $row['Path'];
			$fileName = $row['Text'];
			$isFolder = ($row["IsFolder"] == 1 ? true : false);
			$icon = $row['Icon'];
			$item = array(
				'ID' => $id, 'Text' => $fileName, 'Path' => $oldPath, 'Icon' => $icon
			);
			if(empty($row) || $isFolder){
				$notMovedItems[] = $item;
				return false;
			}

			$version = new weVersions();
			if(in_array($row['ContentType'], $version->contentTypes)){
				$object = weContentProvider::getInstance($row['ContentType'], $id, $table);
				$version_exists = $version->getLastEntry($id, $table);
				$tempOldParentID = $object->ParentID;
				$tempNewParentID = $parentID;
				$tempOldPath = $object->Path;
				$tempNewPath = $newPath . '/' . $fileName;
				$object->Path = $tempNewPath;
				$object->ParentID = $tempNewParentID;
				if(empty($version_exists)){
					$object->Path = $tempOldPath;
					$object->ParentID = $tempOldParentID;
					$version->saveVersion($object);
					$object->Path = $tempNewPath;
					$object->ParentID = $tempNewParentID;
				}
				$version->saveVersion($object);
			}

			// update table
			$DB_WE->query("UPDATE " . $DB_WE->escape($table) . " SET ParentID=" . intval($parentID) . ", Path='" . $DB_WE->escape($newPath) . "/" . $DB_WE->escape($fileName) . "' WHERE ID=" . intval($id));
			$DB_WE->query("UPDATE " . OBJECT_X_TABLE . $tableID . " SET OF_ParentID=" . intval($parentID) . ", OF_Path='" . $DB_WE->escape($newPath) . "/" . $DB_WE->escape($fileName) . "' WHERE OF_ID=" . intval($id));

			return true;
	}

	return false;
}

function checkIfRestrictUserIsAllowed($id, $table = FILE_TABLE, $DB_WE = ''){

	$DB_WE = $DB_WE ? $DB_WE : new DB_WE();
	$row = getHash('SELECT CreatorID,RestrictOwners,Owners,OwnersReadOnly FROM ' . $DB_WE->escape($table) . ' WHERE ID=' . intval($id), $DB_WE);

	if(($_SESSION["user"]["ID"] == $row["CreatorID"]) || $_SESSION["perms"]["ADMINISTRATOR"]){ //	Owner or admin
		return true;
	}

	if($row["RestrictOwners"]){ //	check which user - group has permission
		$userArray = makeArrayFromCSV($row["Owners"]);

		$_allowedGroup = false;

		//	check if usergroup is allowed
		foreach($_SESSION['user']['groups'] as $nr => $_userGroup){
			if(in_array($_userGroup, $userArray)){
				$_allowedGroup = true;
				break;
			}
		}
		if(!in_array($_SESSION["user"]["ID"], $userArray) && !$_allowedGroup){ //	user is no allowed user.
			return false;
		}

		//	user belongs to owners of document, check if he has only read access !!!


		if($row["OwnersReadOnly"]){

			$arr = unserialize($row["OwnersReadOnly"]);
			if(is_array($arr)){

				if(isset($arr[$_SESSION["user"]["ID"]]) && $arr[$_SESSION["user"]["ID"]]){ //	if user is readonly user -> no delete
					return false;
				} elseif(in_array($_SESSION["user"]["ID"], $userArray)){ //	user NOT readonly and in restricted -> delete allowed
					return true;
				}

				//	check if group has rights to delete
				foreach($_SESSION['user']['groups'] as $nr => $_userGroup){ //	user is directly in first group
					if(isset($arr[$_userGroup]) && $arr[$_userGroup]){ //	group not allowed
						return false;
					} elseif(in_array($_userGroup, $userArray)){ //	group is NOT readonly and in restricted -> delete allowed
						return true;
					}
				}
			}
		}
	}
	return true;
}
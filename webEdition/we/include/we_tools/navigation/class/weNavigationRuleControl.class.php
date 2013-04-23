<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
class weNavigationRuleControl{

	var $NavigationRule;

	function __construct(){
		$this->NavigationRule = new weNavigationRule();
	}

	function processCommands(){
		$js = '';
		$html = '';

		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){

				case "save_navigation_rule" :
					$isNew = $this->NavigationRule->isnew; // navigationID = 0
					$save = true;

					$this->NavigationRule->NavigationName = trim($this->NavigationRule->NavigationName);

					// 1st check if name is allowed
					//FIXME: is this correct on UTF-8??
					/* 					if(!preg_match(
					  '%^[äöüßa-z0-9_-]+$%i', $this->NavigationRule->NavigationName)){
					  $js = we_message_reporting::getShowMessageCall(
					  g_l('navigation', '[rules][invalid_name]'), we_message_reporting::WE_MESSAGE_ERROR);
					  $save = false;
					  } */

					// 2ns check if another element has same name
					$db = new DB_WE();

					if(f('SELECT 1 AS a FROM ' . NAVIGATION_RULE_TABLE . ' WHERE NavigationName = "' . $db->escape($this->NavigationRule->NavigationName) . '" AND ID != ' . intval($this->NavigationRule->ID), 'a', $db)){
						$js = we_message_reporting::getShowMessageCall(
								sprintf(
									g_l('navigation', '[rules][name_exists]'), $this->NavigationRule->NavigationName), we_message_reporting::WE_MESSAGE_ERROR);
						$save = false;
					}

					if($save && $this->NavigationRule->save()){

						$js = "doc = top.frames['content'];
							doc.weSelect." . ($isNew ? 'addOption' : 'updateOption') . "('navigationRules', " . $this->NavigationRule->ID . ", '" . $this->NavigationRule->NavigationName . "');
							doc.weSelect.selectOption('navigationRules', " . $this->NavigationRule->ID . ");
							doc.weInput.setValue('ID', " . $this->NavigationRule->ID . ");" .
							we_message_reporting::getShowMessageCall(sprintf(g_l('navigation', '[rules][saved_successful]'), $this->NavigationRule->NavigationName), we_message_reporting::WE_MESSAGE_NOTICE);
					}
					break;

				case "delete_navigation_rule" :
					if($this->NavigationRule->delete()){

						$js = "doc = top.frames['content'];
						doc.weSelect.removeOption('navigationRules', " . $this->NavigationRule->ID . ", '" . $this->NavigationRule->NavigationName . "');
						doc.weInput.setValue('ID', 0);";
					}
					break;

				case "edit_navigation_rule" :

					$this->NavigationRule = new weNavigationRule();
					$this->NavigationRule->initByID($_REQUEST['ID']);

					$FolderIDPath = ($this->NavigationRule->FolderID ? id_to_path($this->NavigationRule->FolderID, FILE_TABLE) : '');
					$ClassIDPath = (defined('OBJECT_TABLE') && $this->NavigationRule->ClassID ? id_to_path($this->NavigationRule->ClassID, OBJECT_TABLE) : '');
					$NavigationIDPath = htmlspecialchars_decode($this->NavigationRule->NavigationID ? id_to_path($this->NavigationRule->NavigationID, NAVIGATION_TABLE) : '', ENT_NOQUOTES);

					// workspaces:
					$_workspaceList = 'optionList.push({"text":"' . g_l('navigation', '[no_entry]') . '","value":"0"});';
					$_selectWorkspace = '';
					if(defined('OBJECT_TABLE') && $this->NavigationRule->ClassID){
						$_workspaces = $this->getWorkspacesByClassID($this->NavigationRule->ClassID);

						foreach($_workspaces as $key => $value){
							$_workspaceList .= 'optionList.push({"text":"' . $value . '","value":"' . $key . '"});';
						}
						$_selectWorkspace = 'doc.weSelect.selectOption("WorkspaceID", "' . $this->NavigationRule->WorkspaceID . '" );';
					}

					// categories
					$catJs = '';
					if($this->NavigationRule->Categories){

						$catIds = makeArrayFromCSV($this->NavigationRule->Categories);

						foreach($catIds as $catId){
							if(($path = id_to_path($catId, CATEGORY_TABLE))){
								$catJs .= 'doc.categories_edit.addItem();doc.categories_edit.setItem(0,(doc.categories_edit.itemCount-1),"' . $path . '");
							';
							}
						}
					}

					$js = "
						doc = top.frames['content'];
						doc.clearNavigationForm();

						doc.weInput.setValue('ID', " . $this->NavigationRule->ID . ");
						doc.weInput.setValue('NavigationName', '" . $this->NavigationRule->NavigationName . "');

						doc.weInput.setValue('NavigationID', '" . $this->NavigationRule->NavigationID . "');
						doc.weInput.setValue('NavigationIDPath', '" . $NavigationIDPath . "');

						doc.weInput.setValue('FolderID', '" . $this->NavigationRule->FolderID . "');
						doc.weInput.setValue('FolderIDPath', '" . $FolderIDPath . "');


						doc.weSelect.selectOption('SelectionType', '" . $this->NavigationRule->SelectionType . "');
						doc.switchType('" . $this->NavigationRule->SelectionType . "');

						doc.weInput.setValue('DoctypeID', '" . $this->NavigationRule->DoctypeID . "');

						doc.weInput.setValue('ClassID', '" . $this->NavigationRule->ClassID . "');
						doc.weInput.setValue('ClassIDPath', '" . $ClassIDPath . "');

						doc.removeAllCats();
						$catJs
						doc.categories_edit.showVariant(0);
						doc.weInput.setValue('CategoriesCount', doc.categories_edit.itemCount);


						var optionList = new Array();
						$_workspaceList
						doc.weSelect.setOptions('WorkspaceID', optionList);
						$_selectWorkspace
						";
					break;

				case "get_workspaces" :

					if(defined('OBJECT_TABLE') && $_REQUEST['ClassID']){
						$_workspaces = $this->getWorkspacesByClassID($_REQUEST['ClassID']);
						$optionList = 'optionList.push({"text":"' . g_l('navigation', '[no_entry]') . '","value":"0"});';

						foreach($_workspaces as $key => $value){
							$optionList .= 'optionList.push({"text":"' . $value . '","value":"' . $key . '"});';
						}

						$js = "
							doc = top.frames['content'];
							var optionList = new Array();
							$optionList
							doc.weSelect.setOptions('WorkspaceID', optionList);
						";
					}

					break;
			}

			print we_html_tools::htmlTop() .
				we_html_element::jsElement($js) .
				'</head><body>' . $html . '</body></html>';
			exit();
		}
	}

	function getWorkspacesByClassID($classId){
		$_workspaces = array();

		if($classId){
			$_workspaces = weDynList::getWorkspacesForClass($classId);
			asort($_workspaces);
		}
		return $_workspaces;
	}

	function processVariables(){
		if(isset($_REQUEST['CategoriesControl']) && isset($_REQUEST['CategoriesCount'])){
			$_categories = array();

			for($i = 0; $i < $_REQUEST['CategoriesCount']; $i++){
				if(isset(
						$_REQUEST[$_REQUEST['CategoriesControl'] . '_variant0_' . $_REQUEST['CategoriesControl'] . '_item' . $i])){

					$_categories[] = $_REQUEST[$_REQUEST['CategoriesControl'] . '_variant0_' . $_REQUEST['CategoriesControl'] . '_item' . $i];
				}
			}

			$categoryIds = array();

			for($i = 0; $i < count($_categories); $i++){
				if(($path = path_to_id($_categories[$i], CATEGORY_TABLE))){
					$categoryIds[] = path_to_id($_categories[$i], CATEGORY_TABLE);
				}
			}
			$categoryIds = array_unique($categoryIds);
			$_catString = '';

			if(!empty($categoryIds)){
				$_catString = ',';

				foreach($categoryIds as $catId){
					$_catString .= "$catId,";
				}
			}

			$this->NavigationRule->Categories = $_catString;
		}

		if(is_array($this->NavigationRule->persistent_slots)){
			foreach($this->NavigationRule->persistent_slots as $val){
				if(isset($_REQUEST[$val])){
					$this->NavigationRule->$val = $_REQUEST[$val];
				}
			}
		}

		$this->NavigationRule->isnew = ($this->NavigationRule->ID == 0);
	}

	static function getAllNavigationRules(){
		$db = new DB_WE();
		$db->query('SELECT * FROM ' . NAVIGATION_RULE_TABLE);

		$navigationRules = array();

		while($db->next_record()) {
			$navigationRules[] = new weNavigationRule(false, $db->Record);
		}
		return $navigationRules;
	}

}
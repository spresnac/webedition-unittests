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

/**
 * collection of the navigation items
 */
class weNavigationItems{

	private static $cache = array();
	var $items;
	var $templates;
	var $rootItem = 0;
	var $hasCurrent = false;
	var $currentRules = array();

	function getCustomerData($navi){
		$_customer = array(
			'id' => '', 'filter' => '', 'blacklist' => '', 'whitelist' => '', 'usedocumentfilter' => 1
		);

		if(!is_array($navi->Customers)){
			$navi->Customers = makeArrayFromCSV($navi->Customers);
		}

		if(!is_array($navi->BlackList)){
			$navi->BlackList = makeArrayFromCSV($navi->BlackList);
		}

		if(!is_array($navi->WhiteList)){
			$navi->WhiteList = makeArrayFromCSV($navi->WhiteList);
		}

		if(!is_array($navi->CustomerFilter)){
			$navi->CustomerFilter = @unserialize($navi->CustomerFilter);
		}

		if($navi->LimitAccess){
			$_customer['id'] = $navi->AllCustomers == 0 ? $navi->Customers : array();
			$_customer['filter'] = $navi->ApplyFilter == 1 ? $navi->CustomerFilter : array();
			$_customer['blacklist'] = $navi->ApplyFilter == 1 ? $navi->BlackList : array();
			$_customer['whitelist'] = $navi->ApplyFilter == 1 ? $navi->WhiteList : array();
			$_customer['usedocumentfilter'] = $navi->UseDocumentFilter ? 1 : 0;
			return $_customer;
		}

		return $_customer;
	}

	function initByNavigationObject($showRoot = true){
		$this->items = array();
		$_navigation = unserialize($_SESSION['weS']['navigation_session']);

		$this->rootItem = $_navigation->ID;

		// set defaultTemplates
		$this->setDefaultTemplates();

		$this->readItemsFromDb($this->rootItem);

		$this->items['id' . $_navigation->ID] = new weNavigationItem(
			$_navigation->ID, $_navigation->LinkID, ($_navigation->IsFolder ? ($_navigation->FolderSelection == weNavigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE) : (($_navigation->SelectionType == weNavigation::STPYE_CLASS || $_navigation->SelectionType == weNavigation::STPYE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE)), $_navigation->Text, $_navigation->Display, $_navigation->getHref($_navigation->SelectionType, $_navigation->LinkID, $_navigation->Url, $_navigation->Parameter, $_navigation->WorkspaceID), $showRoot ? 'folder' : 'root', $this->id2path($_navigation->IconID), $_navigation->Attributes, $_navigation->LimitAccess, $this->getCustomerData($_navigation), $_navigation->CurrentOnUrlPar, $_navigation->CurrentOnAnker);

		$_items = $_navigation->getDynamicPreview($this->Storage);

		$_new_items = self::getStaticSavedDynamicItems($_navigation);

		// fetch the new items in item array
		$_depended = array();
		foreach($_items as $k => $v){
			if($v['depended'] == 1 && $v['parentid'] == $_navigation->ID){
				$_depended[] = $k;
			}
		}

		$i = 0;
		foreach($_new_items as $_new){
			if(isset($_depended[$i])){
				$_items[$_depended[$i]] = $_new;
			} else{
				$_items[] = $_new;
			}
			$i++;
		}

		$_all = count($_items) - count($_depended) + count($_new_items);
		$_items = array_splice($_items, 0, $_all);
		foreach($_items as $_item){
			$this->items['id' . $_item['id']] = new weNavigationItem(
				$_item['id'], $_item['docid'], $_item['table'], $_item['text'], $_item['display'], $_item['href'], $_item['type'], $_item['icon'], $_item['attributes'], $_item['limitaccess'], $_item['customers'], $_item['currentonurlpar'], $_item['currentonanker']);
			if(isset($this->items['id' . $_item['parentid']])){
				$this->items['id' . $_item['parentid']]->addItem($this->items['id' . $_item['id']]);
			}
		}
	}

	function getStaticSavedDynamicItems($_nav, $rules = false){
		$_items = array();
		$_dyn_items = $_nav->getDynamicEntries();
		if(is_array($_dyn_items)){
			foreach($_dyn_items as $_dyn){

				$_href = id_to_path($_dyn['id']);
				$_items[] = array(
					'id' => $_dyn['id'],
					'text' => isset($_dyn['field']) && !empty($_dyn['field']) ? $_dyn['field'] : $_dyn['text'],
					'display' => isset($_dyn['display']) && !empty($_dyn['display']) ? $_dyn['display'] : '',
					'name' => !empty($_dyn['field']) ? $_dyn['field'] : (isset($_dyn['name']) && !empty(
							$_dyn['name']) ? $_dyn['name'] : $_dyn['text']),
					'docid' => $_dyn['id'],
					'table' => (($_nav->SelectionType == weNavigation::STPYE_CLASS || $_nav->SelectionType == weNavigation::STPYE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE),
					'href' => $_href,
					'type' => 'item',
					'parentid' => $_nav->ID,
					'workspaceid' => $_nav->WorkspaceID,
					'icon' => isset($this->Storage['ids'][$_nav->IconID]) ? $this->Storage['ids'][$_nav->IconID] : id_to_path(
							$_nav->IconID),
					'attributes' => $_nav->Attributes,
					'limitaccess' => $_nav->LimitAccess,
					'customers' => self::getCustomerData($_nav),
					'depended' => 1
				);

				if($rules){
					$_items[(count($_items) - 1)]['currentRule'] = weNavigationRule::getWeNavigationRule(
							'defined_' . (!empty($_dyn['field']) ? $_dyn['field'] : $_dyn['text']), $_nav->ID, $_nav->SelectionType, $_nav->FolderID, $_nav->DocTypeID, $_nav->ClassID, $_nav->CategoryIDs, $_nav->WorkspaceID, $_href, false);
				}
			}
		}
		return $_items;
	}

	function loopAllRules($id){
		if(!$this->hasCurrent){
			// add defined rules
			$newRules = weNavigationRuleControl::getAllNavigationRules();

			foreach($newRules as $_rule){
				$this->currentRules[] = $_rule;
			}

			$this->checkCurrent($this->items['id' . $id]->items);
		}
	}

	function initFromCache($parentid = 0, $showRoot = true){
		$this->items = array();
		$this->rootItem = $parentid;
		$this->setDefaultTemplates();

		if(isset(self::$cache[$parentid])){
			$this->items = self::$cache[$parentid];
		} else{
			$this->items = weNavigationCache::getCacheFromFile($parentid);
			if($this->items === false){
				$this->items = array();
				return false;
			}
			self::$cache[$parentid] = $this->items;
		}

		$this->items['id' . $parentid]->type = $showRoot ? ($_parent == 0 ? 'root' : $this->items['id' . $parentid]->type) : 'root';

		$navigationRulesStorage = weNavigationCache::getCachedRule();
		if($navigationRulesStorage !== false){
			$this->currentRules = unserialize($navigationRulesStorage);
			foreach($this->currentRules as &$rule){ //#Bug 4142
				$rule->renewDB();
			}
		}
		unset($navigationRulesStorage);

		foreach($this->items as $_k => &$_item){
			if(strtolower(get_class($_item)) == 'wenavigationitem'){
				$this->hasCurrent = ($_item->isCurrent($this));
			}
		}
		unset($_item);
		$this->loopAllRules($parentid);
		return true;
	}

	function initById($parentid = 0, $depth = false, $showRoot = true){
		$this->items = array();
		$this->rootItem = intval($parentid);

		$_navigation = new weNavigation();

		$this->readItemsFromDb($this->rootItem);

		$_item = $this->getItemFromPool($parentid);

		$_navigation->initByRawData($_item ? $_item : array(
				'ID' => 0, 'Path' => '/'
		));

		// set defaultTemplates
		$this->setDefaultTemplates();

		$this->items['id' . $_navigation->ID] = new weNavigationItem(
			$_navigation->ID, $_navigation->LinkID, ($_navigation->IsFolder ? ($_navigation->FolderSelection == weNavigation::STPYE_OBJLINK ? OBJECT_FILES_TABLE : FILE_TABLE) : (($_navigation->SelectionType == weNavigation::STPYE_CLASS || $_navigation->SelectionType == weNavigation::STPYE_OBJLINK) ? OBJECT_FILES_TABLE : FILE_TABLE)), $_navigation->Text, $_navigation->Display, $_navigation->getHref($this->Storage['ids']), $showRoot ? ($_navigation->ID == 0 ? 'root' : ($_navigation->IsFolder ? 'folder' : 'item')) : 'root', $this->id2path($_navigation->IconID), $_navigation->Attributes, $_navigation->LimitAccess, $this->getCustomerData($_navigation), $_navigation->CurrentOnUrlPar, $_navigation->CurrentOnAnker);

		$_items = $_navigation->getDynamicPreview($this->Storage, true);

		foreach($_items as $_item){

			if(!empty($_item['id'])){
				if(isset($_item['name']) && !empty($_item['name'])){
					$_item['text'] = $_item['name'];
				}
				$this->items['id' . $_item['id']] = new weNavigationItem(
					$_item['id'], $_item['docid'], $_item['table'], $_item['text'], $_item['display'], $_item['href'], $_item['type'], $_item['icon'], $_item['attributes'], $_item['limitaccess'], $_item['customers'], isset($_item['currentonurlpar']) ? $_item['currentonurlpar'] : '', isset($_item['currentonanker']) ? $_item['currentonanker'] : '');

				if(isset($this->items['id' . $_item['parentid']])){
					$this->items['id' . $_item['parentid']]->addItem($this->items['id' . $_item['id']]);
				}

				if($this->items['id' . $_item['id']]->isCurrent($this)){
					$this->hasCurrent = true;
				}

				// add currentRules
				if(isset($_item['currentRule'])){
					$this->currentRules[] = $_item['currentRule'];
				}
			}
		}

		$this->loopAllRules($_navigation->ID);

		//make avail in cache
		self::$cache[$parentid] = $this->items;

		//reduce Memory consumption!
		$this->Storage = array();
	}

	function checkCategories($idRule, $idDoc){
		$idsRule = makeArrayFromCSV($idRule);

		if(!empty($idsRule)){
			foreach($idsRule as $rule){
				if(strpos($idDoc, ",$rule,") !== false){
					return true;
				}
			}
		} else{
			return true;
		}
		return false;
	}

	function setCurrent($navigationID, $current){
		if(isset($this->items["id$navigationID"])){
			$this->items["id$navigationID"]->setCurrent($this, true);
		}
	}

	function checkCurrent(&$items){

		$_candidate = 0;
		$_score = 3;
		$_len = 0;
		$_curr_len = 0;
		$_ponder = 0;

		$_isObject = (isset($GLOBALS['we_obj']) && isset($GLOBALS["WE_MAIN_DOC"]->TableID) && $GLOBALS["WE_MAIN_DOC"]->TableID);

		if(isset($GLOBALS['WE_MAIN_DOC'])){

			for($i = 0; $i < count($this->currentRules); $i++){

				$_rule = $this->currentRules[$i];

				$_ponder = 4;

				if($_rule->SelectionType == weNavigation::STPYE_DOCTYPE && $_rule->DoctypeID){
					if(isset($GLOBALS['WE_MAIN_DOC']->DocType) && ($_rule->DoctypeID == $GLOBALS['WE_MAIN_DOC']->DocType)){
						$_ponder--;
					} else{
						$_ponder = 999; // remove from selection
					}
				}

				if($_rule->SelectionType == weNavigation::STPYE_CLASS && $_rule->ClassID){
					if(isset($GLOBALS["WE_MAIN_DOC"]->TableID) && ($GLOBALS["WE_MAIN_DOC"]->TableID == $_rule->ClassID)){
						$_ponder--;
					} else{
						$_ponder = 999; // remove from selection
					}
				}

				$parentPath = '';
				if($_rule->SelectionType == weNavigation::STPYE_CLASS && $_isObject){

					//$parentPath = id_to_path($_rule->WorkspaceID, FILE_TABLE);
					$parentPath = $this->id2path($_rule->WorkspaceID);

					if(!empty($wPath) && $parentPath != '/'){
						$parentPath .= '/';
					}
				}

				if($_rule->SelectionType == weNavigation::STPYE_DOCTYPE && !$_isObject){

					//$parentPath = id_to_path($_rule->FolderID, FILE_TABLE);
					$parentPath = $this->id2path($_rule->FolderID);

					if(!empty($parentPath) && $parentPath != '/'){
						$parentPath .= '/';
					}
				}

				if(!empty($parentPath)){

					if(strpos($GLOBALS['WE_MAIN_DOC']->Path, $parentPath) === 0){

						$_ponder--;
						$_curr_len = strlen($parentPath);
						if($_curr_len > $_len){
							$_len = $_curr_len;
							$_ponder--;
						}
					}
				}

				$_cats = makeArrayFromCSV($_rule->Categories);
				if(!empty($_cats)){
					if($this->checkCategories($_rule->Categories, $GLOBALS['WE_MAIN_DOC']->Category)){
						$_ponder--;
					} else{
						$_ponder = 999; // remove from selection
					}
				}

				if($_ponder == 0){
					$this->setCurrent($_rule->NavigationID, $_rule->SelfCurrent);
					return true;
				} elseif($_ponder <= $_score){
					if(NAVIGATION_RULES_CONTINUE_AFTER_FIRST_MATCH){
						$this->setCurrent($_rule->NavigationID, null);
					} else{
						$_score = $_ponder;
						$_candidate = $_rule->NavigationID;
					}
				}
			}
			if($_candidate != 0){
				$this->setCurrent($_candidate, null);
				return true;
			}
		}
		return false;
	}

	function getItemIds($id){
		$_items = array($id);

		foreach($this->items[$id]->items as $key => $val){
			if($val->type == 'folder'){
				$_items = array_merge($_items, $this->getItemIds($key));
			} else{
				$_items[] = $key;
			}
		}

		return $_items;
	}

	function getItems($id = false){
		return ($id ?
				$this->getItemIds($id) :
				array_keys($this->items));
	}

	function getItem($id){
		return isset($this->items[$id]) ? $this->items[$id] : false;
	}

	function getTemplate($item){
		if(!isset($this->templates[$item->type])){
			return $this->getDefaultTemplate($item);
		}

		// get correct Level
		$useTemplate = $this->templates[$item->type][(isset($this->templates[$item->type][$item->level]) ? $item->level : 'defaultLevel')];
		// get correct position
		if(isset($useTemplate[$item->current])){
			$useTemplate = $useTemplate[$item->current];
		} elseif(isset($useTemplate['defaultCurrent'])){
			$useTemplate = $useTemplate['defaultCurrent'];
		}

		// is last entry??
		if(isset($useTemplate['last'])){
			// check if item is last
			if((count($this->items['id' . $item->parentid]->items)) == $item->position){
				return $useTemplate['last'];
			}
		}

		if(isset($useTemplate[$item->position])){
			return $useTemplate[$item->position];
		} else{
			if($item->position % 2 === 1){
				if(isset($useTemplate['odd'])){
					return $useTemplate['odd'];
				}
			} elseif(isset($useTemplate['even'])){
				return $useTemplate['even'];
			}
		}

		if(isset($useTemplate['defaultPosition'])){
			return $useTemplate['defaultPosition'];
		}

		return $this->getDefaultTemplate($item);
	}

	function setDefaultTemplates(){
		// the default templates should look like this
		//			$folderTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a><ul><we:navigationEntries /></ul></li>';
		//			$itemTemplate = '<li><a href="<we:navigationField name="href">"><we:navigationField name="text"></a></li>';
		//			$rootTemplate = '<we:navigationEntries />';


		$folderTemplate = '<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "href")) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "text")) . '); ?></a><?php if(' . we_tag_tagParser::printTag('ifHasEntries') . '){ ?><ul><?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?></ul><?php } ?></li>';
		$itemTemplate = '<li><a href="<?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "href")) . '); ?>"><?php printElement( ' . we_tag_tagParser::printTag('navigationField', array("name" => "text")) . '); ?></a></li>';
		$rootTemplate = '<?php printElement( ' . we_tag_tagParser::printTag('navigationEntries') . '); ?>';

		$this->setTemplate($folderTemplate, 'folder', 'defaultLevel', 'defaultCurrent', 'defaultPosition');
		$this->setTemplate($itemTemplate, 'item', 'defaultLevel', 'defaultCurrent', 'defaultPosition');
		$this->setTemplate($rootTemplate, 'root', 'defaultLevel', 'defaultCurrent', 'defaultPosition');
	}

	function getDefaultTemplate($item){
		return $this->templates[$item->type]['defaultLevel']['defaultCurrent']['defaultPosition'];
	}

	function writeNavigation($depth = false){
		$GLOBALS['weNavigationObject'] = & $this;

		if(isset($this->items['id' . $this->rootItem]) && (get_class($this->items['id' . $this->rootItem]) == 'weNavigationItem')){
			if($this->items['id' . $this->rootItem]->type == 'folder' && $depth !== false){
				// if initialised by id => root item is on lvl0 -> therefore decrease depth
				// this is to make it equal init by id, parentid
				$depth--;
			}
			return $this->items['id' . $this->rootItem]->writeItem($this, $depth);
		}

		return '';
	}

	function setTemplate($content, $type, $level, $current, $position){
		$this->templates[$type][$level][$current][$position] = $content;
	}

	function readItemsFromDb($id){
		$this->Storage['items'] = array();
		$this->Storage['ids'] = array();

		$_pathArr = id_to_path($id, NAVIGATION_TABLE, "", false, true);
		$_path = isset($_pathArr[0]) ? $_pathArr[0] : "";

		$_db = new DB_WE();

		$_path = clearPath($_path . '/%');

		$_ids = array();

		$_db->query('SELECT * FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE "' . $_db->escape($_path) . '" ' . ($id != 0 ? ' OR ID=' . intval($id) : '') . ' ORDER BY Ordn');
		while($_db->next_record()) {
			$_tmpItem = $_db->getRecord();
			$_tmpItem["Name"] = $_tmpItem["Text"];
			$this->Storage['items'][] = $_tmpItem;
			unset($_tmpItem);

			if($_db->Record['IsFolder'] == '1' && ($_db->Record['FolderSelection'] == '' || $_db->Record['FolderSelection'] == weNavigation::STPYE_DOCLINK)){
				$_ids[] = $_db->Record['LinkID'];
			} elseif($_db->Record['Selection'] == weNavigation::SELECTION_STATIC && $_db->Record['SelectionType'] == weNavigation::STPYE_DOCLINK){
				$_ids[] = $_db->Record['LinkID'];
			} else
			if(($_db->Record['SelectionType'] == weNavigation::STPYE_CATEGORY || $_db->Record['SelectionType'] == weNavigation::STPYE_CATLINK) && $_db->Record['LinkSelection'] != 'extern'){
				$_ids[] = $_db->Record['UrlID'];
			}

			if(!empty($_db->Record['IconID'])){
				$_ids[] = $_db->Record['IconID'];
			}
		}

		if(count($_ids)){
			array_unique($_ids);
			$_db->query('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $_ids) . ') ORDER BY ID');
			while($_db->next_record()) {
				$this->Storage['ids'][$_db->f('ID')] = $_db->f('Path');
			}
		}
	}

	function getItemFromPool($id){
		foreach($this->Storage['items'] as $item){
			if($item['ID'] == $id){
				return $item;
			}
		}

		return null;
	}

	function id2path($id){
		if(isset($this->Storage['ids'][$id])){
			return $this->Storage['ids'][$id];
		} else{
			$_path = id_to_path($id, FILE_TABLE);
			$this->Storage['ids'][$id] = $_path;
			return $_path;
		}
	}

}
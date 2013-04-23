<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
function we_parse_tag_listview($attribs, $content){
	$arr = array();
	eval('$arr = ' . (PHPLOCALSCOPE ? str_replace('$', '\$', $attribs) : $attribs) . ';'); //Bug #6516
	switch(weTag_getParserAttribute('type', $arr)){
		default:
		case 'document':
		case 'search':
		case 'languagelink':
		case 'customer':
		case 'onlinemonitor':
		case 'order':
		case 'orderitem':
		case 'shopVariant':
		case 'category':
			break;
		case 'object':
			if(defined('OBJECT_TABLE')){
				$predefinedSQL = weTag_getParserAttribute('predefinedSQL', $arr, '');

				if(($foo = attributFehltError($arr, 'classid', __FUNCTION__)) && $predefinedSQL == '')
					return $foo;
			}
			break;
		case 'orderitem':
			if(defined('SHOP_TABLE')){
				if(($foo = attributFehltError($arr, 'orderid', __FUNCTION__)))
					return $foo;
			}
			break;
		case 'multiobject':
			if(defined('OBJECT_TABLE')){
				if(($foo = attributFehltError($arr, 'name', __FUNCTION__)))
					return $foo;
			}
			break;
		case 'banner':
			if(defined('BANNER_TABLE')){
				if(($foo = attributFehltError($arr, 'path', __FUNCTION__)))
					return $foo;
			}
			break;
	}
	//setting global $lv is for backward compatibility
	return '<?php global $lv;' . we_tag_tagParser::printTag('listview', $attribs) . ';?>' . $content . '<?php we_post_tag_listview();?>';
}

function we_tag_listview($attribs){
	$name = weTag_getAttribute('name', $attribs, 0);
	$doctype = weTag_getAttribute('doctype', $attribs);
	$class = weTag_getAttribute('classid', $attribs, 0);
	$we_lv_cats = isset($_REQUEST['we_lv_cats_' . $name]) ? filterXss($_REQUEST['we_lv_cats_' . $name]) : weTag_getAttribute('categories', $attribs);
	$categoryids = weTag_getAttribute('categoryids', $attribs);
	$we_lv_categoryids = isset($_REQUEST['we_lv_categoryids_' . $name]) ? filterXss($_REQUEST['we_lv_categoryids_' . $name]) : $categoryids;
	$we_lv_catOr = (isset($_REQUEST['we_lv_catOr_' . $name]) ? $_REQUEST['we_lv_catOr_' . $name] : weTag_getAttribute('catOr', $attribs, false, true) ) ? true : false;

	$rows = weTag_getAttribute('rows', $attribs, 100000000);
	$order = weTag_getAttribute('order', $attribs);
	//FIXME: XSS
	$we_lv_order = isset($_REQUEST['we_lv_order_' . $name]) ? filterXss($_REQUEST['we_lv_order_' . $name]) : $order;

	$we_lv_numorder = (isset($_REQUEST['we_lv_numorder_' . $name]) ? $_REQUEST['we_lv_numorder_' . $name] : weTag_getAttribute('numorder', $attribs, false, true) ) ? true : false;

	$id = weTag_getAttribute('id', $attribs);
	$cond = weTag_getAttribute('condition', $attribs);
	$type = weTag_getAttribute('type', $attribs, 'document');
	$desc = weTag_getAttribute('desc', $attribs, false, true);
	$we_lv_desc = (isset($_REQUEST['we_lv_desc_' . $name]) ? $_REQUEST['we_lv_desc_' . $name] : $desc ) ? true : false;

	$predefinedSQL = weTag_getAttribute('predefinedSQL', $attribs);
	$offset = weTag_getAttribute('offset', $attribs);
	$workspaceID = weTag_getAttribute('workspaceID', $attribs, weTag_getAttribute('workspaceid', $attribs));
	$we_lv_ws = isset($_REQUEST['we_lv_ws_' . $name]) ? filterIntVals($_REQUEST['we_lv_ws_' . $name]) : $workspaceID;

	$orderid = weTag_getAttribute('orderid', $attribs, 0);

	$we_lv_languages = isset($_REQUEST['we_lv_languages_' . $name]) ? $_REQUEST['we_lv_languages_' . $name] : weTag_getAttribute('languages', $attribs);
	$we_lv_pagelanguage = isset($_REQUEST['we_lv_pagelanguage_' . $name]) ? $_REQUEST['we_lv_pagelanguage_' . $name] : weTag_getAttribute('pagelanguage', $attribs);
	$showself = weTag_getAttribute('showself', $attribs, false, true);

	$triggerid = weTag_getAttribute('triggerid', $attribs, 0);
	$docid = weTag_getAttribute('docid', $attribs, 0);
	$customers = filterIntVals(weTag_getAttribute('customers', $attribs)); // csv value of Ids
	$casesensitive = weTag_getAttribute('casesensitive', $attribs, false, true);
	$customer = weTag_getAttribute('customer', $attribs, false, true);
	$we_lv_ct = isset($_REQUEST['we_lv_ct_' . $name]) ? $_REQUEST['we_lv_ct_' . $name] : weTag_getAttribute('contenttypes', $attribs);

	$cols = weTag_getAttribute('cols', $attribs);
	$we_lv_se = (isset($_REQUEST['we_lv_se_' . $name]) ? $_REQUEST['we_lv_se_' . $name] : weTag_getAttribute('searchable', $attribs, true, true)) ? true : false;

	$seeMode = (isset($attribs['seem'])) ?
		weTag_getAttribute('seem', $attribs, true, true) : //	backwards compatibility
		weTag_getAttribute('seeMode', $attribs, true, true);

	$calendar = weTag_getAttribute('calendar', $attribs);
	$datefield = weTag_getAttribute('datefield', $attribs);
	$date = weTag_getAttribute('date', $attribs);
	$weekstart = weTag_getAttribute('weekstart', $attribs, 'monday');
	$lastaccesslimit = weTag_getAttribute('lastaccesslimit', $attribs, 300);
	$lastloginlimit = weTag_getAttribute('lastloginlimit', $attribs);

	// deprecated, because subfolders acts the other way arround as it should
	$subfolders = (isset($attribs['subfolders'])) ?
		!weTag_getAttribute('subfolders', $attribs, false, true) :
		weTag_getAttribute('recursive', $attribs, true, true);

	$we_lv_subfolders = isset($_REQUEST['we_lv_subfolders_' . $name]) ? (bool) $_REQUEST['we_lv_subfolders_' . $name] : $subfolders;

	$cfilter = weTag_getAttribute('cfilter', $attribs, 'off');
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);
	$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, true);
	$docAttr = weTag_getAttribute('doc', $attribs, 'self');

	if(!isset($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = array();
	}

	if($we_lv_languages == 'self' || $we_lv_languages == 'top'){
		$we_lv_langguagesdoc = we_getDocForTag($we_lv_languages);
		$we_lv_languages = $we_lv_langguagesdoc->Language;
		unset($we_lv_langguagesdoc);
	}
	//FIXME: XSS -> what type is we_lv_calendar
	$we_lv_calendar = isset($_REQUEST['we_lv_calendar_' . $name]) ? $_REQUEST['we_lv_calendar_' . $name] : $calendar;
	$we_lv_datefield = isset($_REQUEST['we_lv_datefield_' . $name]) ? $_REQUEST['we_lv_datefield_' . $name] : $datefield;
	$we_lv_date = isset($_REQUEST['we_lv_date_' . $name]) ? $_REQUEST['we_lv_date_' . $name] : ($date != '' ? $date : date('Y-m-d'));
	$we_lv_weekstart = isset($_REQUEST['we_lv_weekstart_' . $name]) ? $_REQUEST['we_lv_weekstart_' . $name] : $weekstart;

	if($we_lv_cats == 'we_doc'){
		$we_lv_cats = we_getCatsFromDoc($GLOBALS['we_doc'], ',', true, $GLOBALS['DB_WE']);
	}
	$we_predefinedSQL = $predefinedSQL;
	$we_offset = intval($offset);
	$we_rows = intval($rows);


	if($type == 'document' || $type == 'search'){
		$we_lv_doctype = $doctype;
		if($we_lv_doctype == 'we_doc' && $GLOBALS['we_doc']->DocType){
			$we_lv_doctype = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID="' . $GLOBALS['we_doc']->DocType . '"', 'DocType', $GLOBALS['DB_WE']);
		}
	}

	switch($type){
		case 'document':
			$GLOBALS['lv'] = new we_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $doctype, $we_lv_cats, $we_lv_catOr, $casesensitive, $we_lv_ws, $we_lv_ct, $cols, $we_lv_se, $cond, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $cfilter, $we_lv_subfolders, $customers, $id, $we_lv_languages, $we_lv_numorder, $hidedirindex);
			break;
		case 'search':
			$GLOBALS['lv'] = new we_search_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $doctype, $class, $we_lv_cats, $we_lv_catOr, $casesensitive, $we_lv_ws, $triggerid, $cols, $cfilter, $we_lv_languages, $hidedirindex, $objectseourls);
			if(!isset($GLOBALS['weEconda'])){
				$GLOBALS['weEconda'] = '';
			}
			if(!isset($GLOBALS['weEconda']['HTML'])){
				$GLOBALS['weEconda']['HTML'] = '';
			}

			$GLOBALS['weEconda']['HTML'] .= '<a name="emos_name" title="search" rel="' . $GLOBALS["lv"]->search . '" rev="' . $GLOBALS["lv"]->anz_all . '" >';
			break;
		case 'object':
			if(!defined('OBJECT_TABLE')){
				print modulFehltError('Object/DB', __FUNCTION__ . ' type="object"');
				return;
			}
			if(f('SELECT 1 AS a FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($class), 'a', $GLOBALS['DB_WE']) == '1'){
				$GLOBALS['lv'] = new we_listview_object($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $class, $we_lv_cats, $we_lv_catOr, $cond, $triggerid, $cols, $seeMode, $we_lv_se, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $we_lv_ws, $cfilter, $docid, $customers, $id, $we_predefinedSQL, $we_lv_languages, $hidedirindex, $objectseourls);
			} else{
				t_e('warning', 'Class with id=' . intval($class) . ' does not exist');
				return false;
			}
			break;
		case 'languagelink':
			$we_lv_langguagesdoc = we_getDocForTag($we_lv_pagelanguage);
			$we_lv_ownlanguage = $we_lv_langguagesdoc->Language; // we do need real document/objectlanguage in both cases!
			if($we_lv_pagelanguage == 'self' || $we_lv_pagelanguage == 'top'){
				$we_lv_pagelanguage = $we_lv_ownlanguage;
				if(isset($we_lv_langguagesdoc->TableID) && $we_lv_langguagesdoc->TableID){
					$we_lv_pageID = $we_lv_langguagesdoc->OF_ID;
					$we_lv_linktype = 'tblObjectFile';
				} else{
					$we_lv_pageID = $we_lv_langguagesdoc->ID;
					$we_lv_linktype = 'tblFile';
				}
				unset($we_lv_langguagesdoc);
			} else{
				$we_lv_DocAttr = $docAttr;
				$we_lv_langguagesdoc = we_getDocForTag($we_lv_DocAttr);
				if(isset($we_lv_langguagesdoc->TableID) && $we_lv_langguagesdoc->TableID){
					//$we_lv_pagelanguage = $we_lv_langguagesdoc->Language;
					$we_lv_pageID = $we_lv_langguagesdoc->OF_ID;
					$we_lv_linktype = 'tblObjectFile';
				} else{
					//$we_lv_pagelanguage = $we_lv_langguagesdoc->Language;
					$we_lv_pageID = $we_lv_langguagesdoc->ID;
					$we_lv_linktype = 'tblFile';
				}
				unset($we_lv_langguagesdoc);
			}
			$GLOBALS['lv'] = new we_langlink_listview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $we_lv_linktype, $cols, $seeMode, $we_lv_se, $cfilter, $showself, $we_lv_pageID, $we_lv_pagelanguage, $we_lv_ownlanguage, $hidedirindex, $objectseourls);
			break;
		case 'customer':
			if(!defined('CUSTOMER_TABLE')){
				print modulFehltError('Customer', __FUNCTION__ . ' type="customer"');
				return;
			}
			$GLOBALS['lv'] = new we_listview_customer($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $hidedirindex);
			break;
		case 'onlinemonitor':
			if(defined('CUSTOMER_SESSION_TABLE')){
				$GLOBALS['lv'] = new we_listview_onlinemonitor($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $lastaccesslimit, $lastloginlimit, $hidedirindex);
				break;
			}
			print modulFehltError('Customer', __FUNCTION__ . ' type="onlinemonitor"');
			return;
		case 'order':
			if(!defined('SHOP_TABLE')){
				print modulFehltError('Shop', __FUNCTION__ . ' type="order"');
				return;
			}
			$GLOBALS['lv'] = new we_shop_listviewOrder($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $hidedirindex);
			break;
		case 'orderitem':
			if(!defined('SHOP_TABLE')){
				print modulFehltError('Shop', __FUNCTION__ . ' type="orderitem"');
				return;
			}
			$GLOBALS['lv'] = new we_shop_listviewOrderitem($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $cond, $cols, $docid, $orderid, $hidedirindex);
			break;
		case 'multiobject':
			if(!defined('OBJECT_TABLE')){
				print modulFehltError('Object/DB', __FUNCTION__ . ' type="multiobject"');
				return;
			}
			$name = weTag_getAttribute('_name_orig', $attribs);
			$GLOBALS['lv'] = new we_listview_multiobject($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $we_lv_cats, $we_lv_catOr, $cond, $triggerid, $cols, $seeMode, $we_lv_se, $we_lv_calendar, $we_lv_datefield, $we_lv_date, $we_lv_weekstart, $we_lv_categoryids, $cfilter, $docid, $we_lv_languages, $hidedirindex, $objectseourls);
			break;
		case 'banner':
			if(!defined('BANNER_TABLE')){
				print modulFehltError('Banner', __FUNCTION__ . ' type="banner"');
				return;
			}
			$usefilter = weTag_getAttribute('usefilter', $attribs);
			$path = weTag_getAttribute('path', $attribs);
			$filterdatestart = weTag_getAttribute('filterdatestart', $attribs, '-1');
			$filterdateend = weTag_getAttribute('filterdateend', $attribs, '-1');
			$bannerid = f('SELECT ID FROM ' . BANNER_TABLE . ' WHERE PATH="' . $GLOBALS[DB_WE]->escape($path) . '"', 'ID', new DB_WE());
			if($customer && defined('CUSTOMER_TABLE') && (!weBanner::customerOwnsBanner($_SESSION['webuser']['ID'], $bannerid))){
				$bannerid = 0;
			}
			$GLOBALS['lv'] = new we_listview_banner($name, $we_rows, $order, $bannerid, $usefilter, $filterdatestart, $filterdateend);
			break;
		case 'shopVariant':
			if(!defined('SHOP_TABLE')){
				print modulFehltError('Shop', __FUNCTION__ . ' type="shopVariant"');
				return;
			}
			$defaultname = weTag_getAttribute('defaultname', $attribs);
			$docId = weTag_getAttribute('documentid', $attribs);
			$objectId = weTag_getAttribute('objectid', $attribs);
			if($objectId == ''){
				if(isset($GLOBALS['lv']->ClassName) && $GLOBALS['lv']->ClassName == 'we_objecttag'){
					$objectId = $GLOBALS['lv']->object->getDBf('OF_ID');
				}
				if(isset($GLOBALS['lv']->ClassName) && $GLOBALS['lv']->ClassName == 'we_listview_object'){
					$objectId = $GLOBALS['lv']->getDBf('OF_ID');
				}
			}
			$GLOBALS['lv'] = new we_shop_listviewShopVariants($name, $we_rows, $defaultname, $docId, $objectId, $we_offset, $hidedirindex, $objectseourls);
			break;
		case 'category':
			$parentid = weTag_getAttribute('parentid', $attribs, 0);
			$parentidname = weTag_getAttribute('parentidname', $attribs);
//$categoryids="' . $categoryids . '";
//$parentid="' . $parentid . '";
			$GLOBALS['lv'] = new we_catListview($name, $we_rows, $we_offset, $we_lv_order, $we_lv_desc, $parentid, $categoryids, 'default', $cols, ($parentidname ? $parentidname : ''), $hidedirindex);
			break;
		default:
	}
//prevent error if $GLOBALS["we_lv_array"] is no array
	if(!isset($GLOBALS['we_lv_array']) || !is_array($GLOBALS['we_lv_array'])){
		$GLOBALS['we_lv_array'] = array();
	}

	$GLOBALS['we_lv_array'][] = clone($GLOBALS['lv']);
}

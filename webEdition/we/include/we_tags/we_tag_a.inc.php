<?php

/**
 * webEdition CMS
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
function we_parse_tag_a($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('a', $attribs, $content, true) . ');?>';
}

function we_tag_a($attribs, $content){
	// check for id attribute
	if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
		return $foo;
	}

	// get attributes

	$id = weTag_getAttribute('id', $attribs);
	if($id == 'self' && !defined('WE_REDIRECTED_SEO')){
		$id = $GLOBALS['WE_MAIN_DOC']->ID;
	}
	$confirm = weTag_getAttribute('confirm', $attribs);
	$button = weTag_getAttribute('button', $attribs, false, true);
	$hrefonly = weTag_getAttribute('hrefonly', $attribs, false, true);
	$return = weTag_getAttribute('return', $attribs, false, true);
	$target = weTag_getAttribute('target', $attribs);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);
	$shop = weTag_getAttribute('shop', $attribs, false, true);
	$amount = weTag_getAttribute('amount', $attribs, 1);
	$delarticle = weTag_getAttribute('delarticle', $attribs, false, true);
	$delshop = weTag_getAttribute('delshop', $attribs, false, true);
	$urladd = weTag_getAttribute('params', $attribs);
	if($urladd && strpos($urladd, '?') !== 0){
		$urladd = '?' . $urladd;
	}

	$edit = weTag_getAttribute('edit', $attribs);

	if(!$edit && ($shop || $delarticle || $delshop)){
		$edit = 'shop';
	}

	if($edit){
		$delete = weTag_getAttribute('delete', $attribs, false, true);
		$editself = weTag_getAttribute('editself', $attribs, false, true);
		$listview = isset($GLOBALS['lv']);
	}

	if($id == 'self' && defined('WE_REDIRECTED_SEO')){
		$url = WE_REDIRECTED_SEO;
	} else{
		// init variables
		$db = new DB_WE();
		$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $db);
		$url = (isset($row['Path']) ? $row['Path'] : '') . ((isset($row['IsFolder']) && $row['IsFolder']) ? '/' : '');
		$path_parts = pathinfo($url);
		if($hidedirindex && show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES != '' && TAGLINKS_DIRECTORYINDEX_HIDE && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
			$url = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
		}
	}

	if((!$url) && ($GLOBALS['WE_MAIN_DOC']->ClassName != 'we_template')){
		return ($GLOBALS['we_editmode'] ? parseError('in we:a attribute id not exists!') : '');
	}

	switch($edit){
		case 'shop':
			$amount = weTag_getAttribute('amount', $attribs, 1);

			$foo = (isset($GLOBALS['lv']) && $GLOBALS['lv']->ClassName != 'we_listview_multiobject' ? $GLOBALS['lv']->count - 1 : -1);

			// get ID of element
			$customReq = '';
			if(isset($GLOBALS['lv']) && get_class($GLOBALS['lv']) == 'we_shop_shop'){
				$idd = $GLOBALS['lv']->ActItem['id'];
				$type = $GLOBALS['lv']->ActItem['type'];
				$customReq = $GLOBALS['lv']->getCustomFieldsAsRequest();
			} else{
				//Zwei Faelle werden abgedeckt, bei denen die Objekt-ID nicht gefunden wird: (a) bei einer listview ueber shop-objekte, darin eine listview über shop-varianten, hierin der we:a-link und (b) Objekt wird ueber den objekt-tag geladen #3538
				if((isset($GLOBALS['lv']) && get_class($GLOBALS['lv']) == 'we_shop_listviewShopVariants' && isset($GLOBALS['lv']->Model) && $GLOBALS['lv']->Model->ClassName == 'we_objectFile') || isset($GLOBALS['lv']) && get_class($GLOBALS['lv']) == 'we_objecttag'){
					$type = 'o';
					$idd = (get_class($GLOBALS['lv']) == 'we_shop_listviewShopVariants' ? $GLOBALS['lv']->Id : $GLOBALS['lv']->id);
				} else{

					$idd = ((isset($GLOBALS['lv']) && isset($GLOBALS['lv']->IDs[$foo])) && $GLOBALS['lv']->IDs[$foo] != '') ?
						$GLOBALS['lv']->IDs[$foo] :
						((isset($GLOBALS['lv']->classID)) ?
							$GLOBALS['lv']->DB_WE->Record['OF_ID'] :
							((isset($GLOBALS['we_obj']->ID)) ?
								$GLOBALS['we_obj']->ID :
								$GLOBALS['WE_MAIN_DOC']->ID));
					$type = (isset($GLOBALS['lv']) && isset($GLOBALS['lv']->IDs[$foo]) && $GLOBALS['lv']->IDs[$foo] != '') ?
						(
						(isset($GLOBALS['lv']->classID) || isset($GLOBALS['lv']->Record['OF_ID'])) ? 'o' : 'w') :
						((isset($GLOBALS['lv']->classID)) ?
							'o' :
							((isset($GLOBALS['we_obj']->ID)) ? 'o' : 'w')
						);
				}
			}

			// is it a shopVariant ????
			$variant = '';
			// normal variant on document
			if(isset($GLOBALS['we_doc']->Variant)){ // normal listView or document
				$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS['we_doc']->Variant;
			}
			// variant inside shoplistview!
			if(isset($GLOBALS['lv']) && $GLOBALS['lv']->f('WE_VARIANT')){
				$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS['lv']->f('WE_VARIANT');
			}

			//	preview mode in seem
			if(isset($_REQUEST['we_transaction']) && isset(
					$_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]['0']['ClassName']) && $_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]['0']['ClassName'] == 'we_objectFile'){
				$type = 'o';
			}

			$shopname = weTag_getAttribute('shopname', $attribs);
			$ifShopname = ($shopname == '' ? '' : '&shopname=' . $shopname);
			if($delarticle){ // delarticle
				// is it a shopVariant ????
				$variant = '';
				// normal variant on document
				if(isset($GLOBALS['we_doc']->Variant)){ // normal listView or document
					$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS['we_doc']->Variant;
				}
				// variant inside shoplistview!
				if(isset($GLOBALS['lv']) && $GLOBALS['lv']->f('WE_VARIANT')){
					$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS['lv']->f('WE_VARIANT');
				}

				$foo = $GLOBALS['lv']->count - 1;

				$customReq = '';
				if(isset($GLOBALS['lv']) && get_class($GLOBALS['lv']) == 'we_shop_shop'){

					$idd = $GLOBALS['lv']->ActItem['id'];
					$type = $GLOBALS['lv']->ActItem['type'];
					$customReq = $GLOBALS['lv']->getCustomFieldsAsRequest();
				} else{
					$idd = (isset($GLOBALS['lv']->IDs[$foo]) && $GLOBALS['lv']->IDs[$foo] != '') ?
						$GLOBALS['lv']->IDs[$foo] :
						((isset($GLOBALS['lv']->classID)) ?
							$GLOBALS['lv']->DB_WE->Record['OF_ID'] :
							((isset($GLOBALS['we_obj']->ID)) ?
								$GLOBALS['we_obj']->ID :
								$GLOBALS['WE_MAIN_DOC']->ID));
					$type = (isset($GLOBALS['lv']) && isset($GLOBALS['lv']->IDs[$foo]) && $GLOBALS['lv']->IDs[$foo] != '') ?
						((isset($GLOBALS['lv']->classID) || isset($GLOBALS['lv']->Record['OF_ID'])) ?
							'o' :
							'w') :
						((isset($GLOBALS['lv']->classID)) ?
							'o' :
							((isset($GLOBALS['we_obj']->ID)) ?
								'o' :
								'w'));
				}
				//	preview mode in seem
				if(isset($_REQUEST['we_transaction']) && isset(
						$_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]['0']['ClassName']) && $_SESSION['weS']['we_data'][$_REQUEST['we_transaction']]['0']['ClassName'] == 'we_objectFile'){
					$type = 'o';
				}
				$urladd = ($urladd ? $urladd . '&' : '?') . 'del_shop_artikelid=' . $idd . '&type=' . $type . '&t=' . time() . $variant . $customReq . $ifShopname;
			} else
			if($delshop){ // emptyshop
				if(($foo = attributFehltError($attribs, 'shopname', __FUNCTION__))){
					return $foo;
				}
				$urladd = ($urladd ? $urladd . '&' : '?') . 'deleteshop=1' . $ifShopname . '&t=' . time();
			} else{ // increase/decrease amount of articles
				$urladd = ($urladd ? $urladd . '&' : '?') . 'shop_artikelid=' . $idd . '&shop_anzahl=' . $amount . '&type=' . $type . '&t=' . time() . $variant . ($customReq ? $customReq : '') . $ifShopname;
			}
			break;

		case 'object':
			$oid = ($listview ?
					(isset($GLOBALS['lv']) && $GLOBALS['lv']->f('WE_ID') ? $GLOBALS['lv']->f('WE_ID') : 0) :
					(isset($GLOBALS['we_obj']) && isset($GLOBALS['we_obj']->ID) && $editself ? $GLOBALS['we_obj']->ID : 0));

			if($delete){
				if($oid){
					$urladd = ($urladd ? $urladd . '&' : '?') . 'we_delObject_ID=' . $oid;
				}
			} else{
				$urladd = ($urladd ? $urladd . '&' : '?') . ($oid ? 'we_editObject_ID=' . $oid : 'edit_object=1');
			}
			break;
		case 'document':
			$did = ($listview ?
					(isset($GLOBALS['lv']) && $GLOBALS['lv']->f('WE_ID') ? $GLOBALS['lv']->f('WE_ID') : 0) :
					(isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ID) && $editself ? $GLOBALS['we_doc']->ID : 0));

			if($delete){//FIXME: make sure only the selected object can be deleted - sth unique not user-known has to be added to prevent denial of service
				if($did){
					$urladd = ($urladd ? $urladd . '&' : '?') . 'we_delDocument_ID=' . $did;
				}
			} else{
				$urladd = ($urladd ? $urladd . '&' : '?') . ($did ? 'we_editDocument_ID=' . $did : 'edit_document=1');
			}
			break;
	}

	if($return){
		$urladd = ($urladd ? $urladd . '&' : '?') . 'we_returnpage=' . rawurlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']);
	}

	if($hrefonly){
		return $url . $urladd;
	}

	//	remove unneeded attributes from array
	$attribs = removeAttribs($attribs, array(
		'id',
		'shop',
		'amount',
		'delshop',
		'delarticle',
		'shopname',
		'return',
		'edit',
		'type',
		'button',
		'hrefonly',
		'confirm',
		'editself',
		'delete',
		'params'
	));

	if($button){ //	show button
		$attribs['type'] = 'button';
		$attribs['value'] = oldHtmlspecialchars($content);
		$attribs['onclick'] = ($target ? ("var wind=window.open('','$target');wind") : 'self') . ".document.location='$url" . oldHtmlspecialchars($urladd) . "';";

		$attribs = removeAttribs($attribs, array('target')); //	not html - valid


		if($confirm){
			$confirm = str_replace("'", "\\'", $confirm);
			$attribs['onclick'] = 'if(confirm(\'' . $confirm . '\')){' . $attribs['onclick'] . '}';
		}
		return getHtmlTag('input', $attribs);
	} else{ //	show normal link
		$attribs['href'] = $url . ($urladd ? oldHtmlspecialchars($urladd) : '');

		if($confirm){
			$attribs['onclick'] = 'if(confirm(\'' . $confirm . '\')){return true;}else{return false;}';
		}

		return getHtmlTag('a', $attribs, $content, true);
	}
}
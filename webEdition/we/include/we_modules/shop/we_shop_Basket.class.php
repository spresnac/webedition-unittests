<?php

/**
 * webEdition CMS
 *
 * $Rev: 5873 $
 * $Author: mokraemer $
 * $Date: 2013-02-23 15:00:13 +0100 (Sat, 23 Feb 2013) $
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
class we_shop_Basket{

	//FIXME: this is set back to public due to some shop restrictions, see #6530, #6954
	/**
	 * 	this array contains all shopping items
	 * 	a shopping item is an associated array containining
	 * 	'id'       => integer
	 * 	'type'     => w | o
	 * 	'variant'  => string
	 * 	'quantity' => integer //fr�her - jetzt umstellung auf float, feature #4875
	 * 	'serial'   => string
	 *  'customFields' => array
	 *
	 * @var array
	 */
	public $ShoppingItems = array();

	/**
	 * user can define custom fields saved with the order.
	 *
	 * @var array
	 */
	public $CartFields = array();
	public $orderID = 0;

	function initCartFields(){

		if(isset($_REQUEST[WE_SHOP_CART_CUSTOM_FIELD]) && is_array($_REQUEST[WE_SHOP_CART_CUSTOM_FIELD])){
			foreach($_REQUEST[WE_SHOP_CART_CUSTOM_FIELD] as $key => $value){
				$this->CartFields[$key] = $value;
			}
		}
	}

	/**
	 * returns array of shoppingItems
	 *
	 * @return array
	 */
	function getShoppingItems(){
		return $this->ShoppingItems;
	}

	/**
	 * returns array of all shopping cartfields
	 *
	 * @return array
	 */
	function getCartFields(){
		return $this->CartFields;
	}

	function hasCartField($name){
		return isset($this->CartFields[$name]);
	}

	function getCartField($name){
		return isset($this->CartFields[$name]) ? $this->CartFields[$name] : '';
	}

	/**
	 * returns the items in the shopping cart and all custom cart fields
	 * former getProperties
	 *
	 * @return array
	 */
	function getCartProperties(){

		return array(
			'shoppingItems' => $this->getShoppingItems(),
			'cartFields' => $this->getCartFields()
		);
	}

	/**
	 * initialies the shoppingCart
	 * former name setProperties
	 *
	 * @param array $array
	 */
	function setCartProperties($array){

		if(isset($array['shoppingItems']) && isset($array['cartFields'])){
			$this->ShoppingItems = $array['shoppingItems'];
			$this->CartFields = $array['cartFields'];
		} else{
			$this->ShoppingItems = array();
			$this->CartFields = array();
		}
	}

	/**
	 * add an item to the array
	 *
	 * @param integer $id
	 * @param integer $quantity
	 * @param string $type
	 * @param string $variant
	 */
	function Add_Item($id, $quantity = 1, $type = 'w', $variant = '', $customFields = array()){

		// check if this item is already in the shoppingCart
		if(($key = $this->getShoppingItemIndex($id, $type, $variant, $customFields))){ // item already exists
			if($this->ShoppingItems[$key]['quantity'] + $quantity > 0){
				$this->ShoppingItems[$key]['quantity'] += $quantity;
			} else{
				$this->Del_Item($id, $type, $variant, $customFields);
			}
		} else{ // add the item
			$key = str_replace('.', '', uniqid('we_cart_', true));

			if($quantity > 0){ // only add new item with positive number
				$item = array(
					'id' => $id,
					'type' => $type,
					'variant' => $variant,
					'quantity' => $quantity,
					'serial' => $this->getserial($id, $type, $variant, $customFields),
					'customFields' => $customFields
				);

				$this->ShoppingItems[$key] = $item;
			}
		}
	}

	/**
	 * returns size of shoppingCart
	 *
	 * @return integer
	 */
	function Get_Basket_Count(){
		return count($this->ShoppingItems);
	}

	/**
	 * returns shoppingItems
	 *
	 * @return array
	 */
	function Get_All_Data(){
		return $this->getCartProperties();
	}

	/**
	 * returns shoppingItem - serial
	 *
	 * @param integer $id
	 * @param string $type
	 * @param string $variant
	 * @return string
	 */
	function getserial($id, $type, $variant = false, $customFields = array()){
		$DB_WE = new DB_WE;
		$Record = array();

		switch($type){
			case 'w':
				// unfortunately this is not made with initDocById,
				// but its much faster -> so we use it
				$DB_WE->query('SELECT ' . CONTENT_TABLE . '.BDID as BDID, ' . CONTENT_TABLE . '.Dat as Dat, ' . LINK_TABLE . '.Name as Name FROM ' . LINK_TABLE . ',' . CONTENT_TABLE . ' WHERE ' . LINK_TABLE . '.DID=' . intval($id) . ' AND ' . LINK_TABLE . '.CID=' . CONTENT_TABLE . '.ID AND ' . LINK_TABLE . '.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '"');
				while($DB_WE->next_record()) {
					$tmp = ($DB_WE->f('BDID'));
					$Record[$DB_WE->f('Name')] = $tmp ? $tmp : $DB_WE->f('Dat');
				}

				if($variant){
					weShopVariants::useVariantForShop($Record, $variant);
				}

				$hash = getHash('SELECT * FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), $DB_WE);
				if(!empty($hash)){
					foreach($hash as $key => $val){
						$Record['wedoc_' . $key] = $val;
					}
				}

				$Record['WE_PATH'] = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'Path', $DB_WE) . ($variant ? '?' . WE_SHOP_VARIANT_REQUEST . '=' . $variant : '');
				$Record['WE_TEXT'] = f('SELECT Text FROM ' . INDEX_TABLE . ' WHERE DID=' . intval($id), 'Text', $DB_WE);
				$Record['WE_VARIANT'] = $variant;
				$Record['WE_ID'] = intval($id);

				// at last add custom fields to record and to path
				if(!empty($customFields)){
					$Record['WE_PATH'] .= ($variant ? '&amp;' : '?');

					foreach($customFields as $name => $value){
						$Record[$name] = $value;
						$Record['WE_PATH'] .= WE_SHOP_ARTICLE_CUSTOM_FIELD . '[' . $name . ']=' . $value . '&amp;';
					}
				}
				break;
			case 'o':
				$classArray = getHash('SELECT * FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), $DB_WE);

				$olv = new we_listview_object('0', 1, 0, '', 0, $classArray['TableID'], '', '', ' ' . OBJECT_X_TABLE . $classArray["TableID"] . '.ID=' . $classArray['ObjectID']);
				$olv->next_record();

				$Record = $olv->DB_WE->Record;

				if($variant){
					// init model to detect variants
					// :TODO: change this to match above version
					$obj = new we_objectFile();
					$obj->initByID($id, OBJECT_FILES_TABLE);

					weShopVariants::useVariantForShopObject($Record, $variant, $obj);

					// add variant to path ...
					$Record['we_WE_PATH'] = $Record['we_WE_PATH'] . '&amp;' . WE_SHOP_VARIANT_REQUEST . '=' . $variant;
				}
				$Record['WE_VARIANT'] = $variant;
				$Record['we_obj'] = $id;

				// at last add custom fields to record and to path
				if(!empty($customFields)){
					foreach($customFields as $name => $value){
						$Record[$name] = $value;
						$Record['we_WE_PATH'] .= '&amp;' . WE_SHOP_ARTICLE_CUSTOM_FIELD . "[$name]=$value";
					}
				}

				// when using objects all fields have 'we_' as prename
				if(isset($Record['we_' . WE_SHOP_VAT_FIELD_NAME])){
					$Record[WE_SHOP_VAT_FIELD_NAME] = $Record['we_' . WE_SHOP_VAT_FIELD_NAME];
					unset($Record['we_' . WE_SHOP_VAT_FIELD_NAME]);
				}
				break;
		}

		// at last add custom fields and vat to shopping card
		$Record[WE_SHOP_ARTICLE_CUSTOM_FIELD] = $customFields;

		return $Record;
	}

	/**
	 * returns amount of shopping items by key
	 *
	 * @param string $key
	 * @return integer
	 */
	function Get_Item_Quantity($key){
		return $this->ShoppingItems[$key]['quantity'];
	}

	/**
	 * remove item from shop
	 *
	 * @param integer $id
	 * @param string $type
	 * @param string $variant
	 */
	function Del_Item($id, $type, $variant = '', $customFields = array()){
		if(($key = $this->getShoppingItemIndex($id, $type, $variant, $customFields))){
			unset($this->ShoppingItems[$key]);
		}
	}

	/**
	 * resets the shoppingCart
	 *
	 */
	function Empty_Basket(){
		$this->ShoppingItems = array();
		$this->CartFields = array();
	}

	/**
	 * changes abilities of item in the shoppingCart
	 *
	 * @param integer $id
	 * @param integer $quantity
	 * @param string $type
	 * @param string $variant
	 */
	function Set_Item($id, $quantity = 1, $type = "w", $variant = '', $customFields = array()){

		if(($key = $this->getShoppingItemIndex($id, $type, $variant, $customFields))){ // item already in cart
			if($quantity > 0){
				$this->ShoppingItems[$key]['quantity'] = $quantity;
			} else{
				$this->Del_Item($id, $type, $variant, $customFields);
			}
		} else{ // new item
			$this->Add_Item($id, $quantity, $type, $variant, $customFields);
		}
	}

	/**
	 * set cart item by the assoc array
	 *
	 * @param string $cart_id
	 * @param integer $cart_amount
	 */
	function Set_Cart_Item($cart_id, $cart_amount){

		if(isset($this->ShoppingItems[$cart_id])){

			$item = $this->ShoppingItems[$cart_id];
			$this->Set_Item($item['id'], $cart_amount, $item['type'], $item['variant'], $item['customFields']);
		}
	}

	/**
	 * returns key for shoppingItem or false
	 *
	 * @param integer $id
	 * @param string $type
	 * @param string $variant
	 * @return mixed
	 */
	function getShoppingItemIndex($id, $type = 'w', $variant = '', $customFields = array()){

		foreach($this->ShoppingItems as $index => $item){
			if($item['id'] == $id && $item['type'] == $type && $item['variant'] == $variant && $customFields == $item['customFields']){
				return $index;
			}
		}
		return false;
	}

	function getOrderID(){
		return $this->orderID;
	}

	function setOrderID($id){
		$this->orderID = $id;
	}

}

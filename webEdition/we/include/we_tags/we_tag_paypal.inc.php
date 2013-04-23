<?php

/**
 * webEdition CMS
 *
 * $Rev: 5993 $
 * $Author: mokraemer $
 * $Date: 2013-03-24 19:20:25 +0100 (Sun, 24 Mar 2013) $
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
 * @category	webEdition
 * @package	 webEdition_base
 * @license	 http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * This function writes the shop data (order) to the database and send values to paypal
 *
 * @param			 $attribs array
 *
 * @return			void
 */
function we_tag_paypal($attribs){
	global $DB_WE;
	$name = weTag_getAttribute('name', $attribs);
	if(($foo = attributFehltError($attribs, 'pricename', __FUNCTION__))){
		return $foo;
	}

	$shopname = weTag_getAttribute('shopname', $attribs, $name);
	$pricename = weTag_getAttribute('pricename', $attribs);

	$countrycode = weTag_getAttribute('countrycode', $attribs);
	$languagecode = weTag_getAttribute('languagecode', $attribs);
	$shipping = weTag_getAttribute('shipping', $attribs);
	$shippingIsNet = weTag_getAttribute('shippingisnet', $attribs, false, true);
	$shippingVatRate = weTag_getAttribute('shippingvatrate', $attribs);
	$messageRedirectAuto = weTag_getAttribute('messageredirectAuto', $attribs);
	if($messageRedirectAuto == ''){
		$messageRedirectAuto = weTag_getAttribute('messageredirectauto', $attribs);
	}
	$messageRedirectMan = weTag_getAttribute('messageredirectMan', $attribs);
	if($messageRedirectMan == ''){
		$messageRedirectMan = weTag_getAttribute('messageredirectman', $attribs);
	}
	$formTagOnly = weTag_getAttribute('formtagonly', $attribs, false, true);
	$charset = weTag_getAttribute('charset', $attribs);

	$netprices = weTag_getAttribute('netprices', $attribs, true, true);
	$useVat = weTag_getAttribute('usevat', $attribs, true, true);
	$currency = weTag_getAttribute('currency', $attribs);

	if($useVat){
		$_customer = (isset($_SESSION['webuser']) ? $_SESSION['webuser'] : false);

		$weShopVatRule = weShopVatRule::getShopVatRule();

		//FIX: it was meant to write since now we know if a costumer needs to pay tax or the default is true
		$useVat = $weShopVatRule->executeVatRule($_customer);
	}



	if(isset($GLOBALS[$shopname])){
		$basket = $GLOBALS[$shopname];

		$shoppingItems = $basket->getShoppingItems();
		$cartFields = $basket->getCartFields();

		if(empty($shoppingItems)){
			return;
		}

		/*  PHP Paypal IPN Integration Class Demonstration File
		 *
		 *  This file demonstrates the usage of paypal.class.php, a class designed
		 *  to aid in the interfacing between your website, paypal, and the instant
		 *  payment notification (IPN) interface.  This single file serves as 4
		 *  virtual pages depending on the "action" varialble passed in the URL. It's
		 *  the processing page which processes form data being submitted to paypal, it
		 *  is the page paypal returns a user to upon success, it's the page paypal
		 *  returns a user to upon canceling an order, and finally, it's the page that
		 *  handles the IPN request from Paypal.
		 */


		$DB_WE = !isset($DB_WE) ? new DB_WE : $DB_WE;

		//	NumberFormat - currency and taxes
		if($currency == ''){
			$feldnamen = explode('|', f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname = "shop_pref"', 'strFelder', $DB_WE));
			if(!isset($feldnamen[0])){ // determine the currency
				$feldnamen[0] = -1;
			}

			switch($feldnamen[0]){
				case '$':
				case 'USD':
					$currency = 'USD';
					break;
				case '�':
				case '£':
				case 'GBP':
					$currency = 'GBP';
					break;
				case 'AUD':
					$currency = 'AUD';
					break;
				case '?':
				case 'EUR':
				default:
					$currency = 'EUR';
					break;
			}
		}

		$formField = explode('|', f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname = "payment_details"', 'strFelder', $DB_WE));
		if(isset($formField[0])){ // determine the Forename
			$sendForename = $_SESSION['webuser'][$formField[0]];
		}
		if(isset($formField[1])){ // determine the Surename
			$sendSurname = $_SESSION['webuser'][$formField[1]];
		}
		if(isset($formField[2])){ // determine the Street
			$sendStreet = $_SESSION['webuser'][$formField[2]];
		}
		if(isset($formField[3])){ // determine the Zip
			$sendZip = $_SESSION['webuser'][$formField[3]];
		}
		if(isset($formField[4])){ // determine the City
			$sendCity = $_SESSION['webuser'][$formField[4]];
		}
		if(isset($formField[18]) && $formField[18]){ // determine the City
			$sSendEmail = $_SESSION['webuser'][$formField[18]];
		}

		if(isset($formField[5])){ // determine the country code
			$lc = $formField[5];
		}

		if(isset($formField[6])){ // determine the paypal business email
			$paypalEmail = $formField[6];
		}
		if(isset($formField[7])){ // todo
			if($formField[7] == 'default'){
				$paypalURL = 'https://www.paypal.com/cgi-bin/webscr';
			} else{
				$paypalURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			}
		} else{
			$paypalURL = 'https://www.paypal.com/cgi-bin/webscr';
		}

// Setup class
		$p = new paypal_class; // initiate an instance of the class
		$p->paypal_url = $paypalURL; // testing paypal url
//$p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';	  // paypal url
// setup a variable for this script (ie: 'http://www.webedition.org/shop/paypal.php')
		$this_script = getServerUrl() . $_SERVER['SCRIPT_NAME'];

// if there is not action variable, set the default action of 'process'
		if(empty($_GET['action']))
			$_GET['action'] = 'process';

		switch($_GET['action']){

			case 'process': // Process and order
				// There should be no output at this point.  To process the POST data,
				// the submit_paypal_post() function will output all the HTML tags which
				// contains a FORM which is submited instantaneously using the BODY onload
				// attribute.  In other words, don't echo or printf anything when you're
				// going to be calling the submit_paypal_post() function.
				// This is where you would have your form validation  and all that jazz.
				// You would take your POST vars and load them into the class like below,
				// only using the POST values instead of constant string expressions.
				// For example, after ensureing all the POST variables from your custom
				// order form are valid, you might have:
				//
		// $p->add_field('first_name', $_POST['first_name']);
				// $p->add_field('last_name', $_POST['last_name']);


				$i = 0;
				$summit = 0;
				foreach($shoppingItems as $key => $item){
					$i++; //  loop through basket

					$p->add_field('business', $paypalEmail);
					$p->add_field('return', $this_script . '?action=success');
					$p->add_field('cancel_return', $this_script . '?action=cancel');
					$p->add_field('notify_url', $this_script . '?action=ipn');
					$p->add_field('currency_code', $currency);
					if($languagecode == ''){
						$p->add_field('lc', $lc);
					} else{
						$p->add_field('lc', $languagecode);
					}
					// get user details
					$p->add_field('first_name', $sendForename);
					$p->add_field('last_name', $sendSurname);
					$p->add_field('address1', $sendStreet);
					$p->add_field('zip', $sendZip);
					$p->add_field('city', $sendCity);
					//#4615, don't set country code if not specified.
					if($countrycode != ''){
						$p->add_field('country', $countrycode);
					}

					if(isset($sSendEmail) && we_check_email($sSendEmail)){
						$p->add_field('email', $sSendEmail);
						$p->add_field('receiver_email', $sSendEmail);
					}
					if($charset != ''){
						$p->add_field('charset', $charset);
					}
					//  determine the basket data
					$p->add_field('item_name_' . $i, $itemTitle = (isset($item['serial']['we_shoptitle']) ? $item['serial']['we_shoptitle'] : $item['serial']['shoptitle']));
					$p->add_field('quantity_' . $i, $item['quantity']);

					$itemPrice = (isset($item['serial']['we_' . $pricename]) ? $item['serial']['we_' . $pricename] : $item['serial'][$pricename]);

					// correct price, if it has more than one "."
					// bug #8717
					$itemPrice = we_util::std_numberformat($itemPrice);

					//seems to be gros product prices and customer do not need pay tax
					//so we have to calculate the correct net article price
					//bug #5701
					if(!$useVat && !$netprices){
						$vatId = isset($item['serial'][WE_SHOP_VAT_FIELD_NAME]) ? $item['serial'][WE_SHOP_VAT_FIELD_NAME] : 0;
						$shopVat = weShopVats::getVatRateForSite($vatId, true, false);
						$shopVat = (1 + ($shopVat / 100));
						//paypal allows only two decimal places
						$itemPrice = round(($itemPrice / $shopVat), 2);
					} else{
						//paypal allows only two decimal places
						$itemPrice = round($itemPrice, 2); //#6546
					}

					$p->add_field('amount_' . $i, $itemPrice);

					// foreach article we must determine the correct tax-rate
					$vatId = isset($item['serial'][WE_SHOP_VAT_FIELD_NAME]) ? $item['serial'][WE_SHOP_VAT_FIELD_NAME] : 0;
					$shopVat = weShopVats::getVatRateForSite($vatId, true, false);
					if($shopVat){ // has selected or standard shop rate
						$item['serial'][WE_SHOP_VAT_FIELD_NAME] = $shopVat;
					} else{ // could not find any shoprates, remove field if necessary
						if(isset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME])){
							unset($shoppingItem['serial'][WE_SHOP_VAT_FIELD_NAME]);
						}
					}


					if($netprices && $useVat){ //Bug 4549
						$totalVat = $itemPrice / 100 * $shopVat;
						$totalVats = number_format($totalVat, 2);
						// add the polychronic taxes
						$p->add_field('tax_' . $i, $totalVats);
					}

					// determine the shipping cost by accumulating the total
					$summit += ( $itemPrice * $item['quantity']);
				}

				//get the shipping costs
				$weShippingControl = weShippingControl::getShippingControl();

				if(we_tag('ifRegisteredUser')){ // check if user is registered
					$customer = $_SESSION['webuser'];
				} else{
					$customer = false;
				}

				if($shipping == ''){
					$cartField[WE_SHOP_SHIPPING] = array(
						'costs' => $weShippingControl->getShippingCostByOrderValue($summit, $customer),
						'isNet' => $weShippingControl->isNet,
						'vatRate' => $weShippingControl->vatRate
					);
				} else{
					$cartField[WE_SHOP_SHIPPING] = array(
						'costs' => $shipping,
						'isNet' => $shippingIsNet,
						'vatRate' => $shippingVatRate
					);
				}
				$shippingCosts = $cartField[WE_SHOP_SHIPPING]['costs'];
				$isNet = $cartField[WE_SHOP_SHIPPING]['isNet'];
				$vatRate = $cartField[WE_SHOP_SHIPPING]['vatRate'];
				$shippingCostVat = $shippingCosts / 100 * $vatRate;
				$shippingFee = $shippingCosts + $shippingCostVat;

				//Bug 4549
				if($isNet && $useVat){ // net prices
					$shippingCostVat = $shippingCosts / 100 * $vatRate;
					$shippingFee = $shippingCosts + $shippingCostVat;
				} elseif(!$useVat && !$isNet){// Bug #5701
					//seems to be gros vat rate
					$vatRate = (1 + ($vatRate / 100));
					$shippingFee = ($shippingCosts / $vatRate);
				} else{
					$shippingFee = $shippingCosts;
				}


				/*
				  if($isNet != 0){
				  $p->add_field('shipping_1', $shippingCosts);
				  }else{
				  print " null ";
				  }
				 */
				$p->add_field('shipping_1', round($shippingFee, 2));
				$p->add_field('upload', 1);


				//p_r($p);
				// exit;

				$p->submit_paypal_post($formTagOnly, $messageRedirectAuto, $messageRedirectMan); // submit the fields to paypal
				break;

			case 'success': // Order was successful
				// This is where you would probably want to thank the user for their order
				// or what have you.  The order information at this point is in POST
				// variables.  However, you don't want to "process" the order until you
				// get validation from the IPN.  That's where you would have the code to
				// email an admin, update the database with payment status, activate a
				// membership, etc.
				we_tag('writeShopData', $attribs);

				// You could also simply re-direct them to another page, or your own
				// order status page which presents the user with the status of their
				// order based on a database (which can be modified with the IPN code
				// below).



				break;

			case 'cancel': // Order was canceled
				// The order was canceled before being completed.



				break;

			case 'ipn': // Paypal is calling page for IPN validation
				// It's important to remember that paypal calling this script.  There
				// is no output here.  This is where you validate the IPN data and if it's
				// valid, update your database to signify that the user has payed.  If
				// you try and use an echo or printf function here it's not going to do you
				// a bit of good.  This is on the "backend".  That is why, by default, the
				// class logs all IPN data to a text file.

				if($p->validate_ipn()){

					// Payment has been recieved and IPN is verified.  This is where you
					// update your database to activate or process the order, or setup
					// the database with the user's order details, email an administrator,
					// etc.  You can access a slew of information via the ipn_data() array.
					// Check the paypal documentation for specifics on what information
					// is available in the IPN POST variables.  Basically, all the POST vars
					// which paypal sends, which we send back for validation, are now stored
					// in the ipn_data() array.
					// For this example, we'll just email ourselves ALL the data.
					/* $subject = 'Instant Payment Notification - Recieved Payment';
					  $to = 'jan.gorba@webedition.de';	 //  your email
					  $body =  "An instant payment notification was successfully recieved\n";
					  $body .= "from ".$p->ipn_data['payer_email']." on ".date('m/d/Y');
					  $body .= " at ".date('g:i A')."\n\nDetails:\n";
					  foreach ($p->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
					  mail($to, $subject, $body); */
				}
				break;
		}
	}
	return;
}

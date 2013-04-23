<?php

/**
 * webEdition CMS
 *
 * $Rev: 5872 $
 * $Author: mokraemer $
 * $Date: 2013-02-23 12:01:09 +0100 (Sat, 23 Feb 2013) $
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
function we_tag_shipping($attribs){
	if(($foo = attributFehltError($attribs, "sum", __FUNCTION__))){
		return $foo;
	}

	$sumName = weTag_getAttribute('sum', $attribs);
	$num_format = weTag_getAttribute('num_format', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$shippingCost = 0;

	// shipping depends on total value of basket
	if(isset($GLOBALS['summe'][$sumName])){
		$orderVal = $GLOBALS['summe'][$sumName];
		$weShippingControl = weShippingControl::getShippingControl();

		// check if user is registered
		$customer = (we_tag('ifRegisteredUser') ? $_SESSION['webuser'] : false);

		$shippingCost = $weShippingControl->getShippingCostByOrderValue($orderVal, $customer);

		// get calculated value if needed
		if($type){
			// if user must NOT pay vat always return net prices
			$mustPayVat = we_tag('ifShopPayVat'); // alayways return net prices

			if($mustPayVat){
				switch($type){
					case 'net':
						if(!$weShippingControl->isNet){
							// y = x * (100/116)
							$shippingCost = $shippingCost * (100 / ((1 + ($weShippingControl->vatRate / 100)) * 100) );
						}
						break;
					case 'gros':
						if($weShippingControl->isNet){
							// y = x * (1.16)
							$shippingCost = $shippingCost * (1 + ($weShippingControl->vatRate / 100));
						}
						break;
					case 'vat':
						$shippingCost = ($weShippingControl->isNet ?
								// y = x * 0.16
								$shippingCost * ($weShippingControl->vatRate / 100) :
								// y = x /116 * 16
								$shippingCost / ( ((1 + ($weShippingControl->vatRate / 100)) * 100) ) * $weShippingControl->vatRate);
						break;
				}
			} else{ // always return net prices
				switch($type){
					case 'gros':
					case 'net':
						if(!$weShippingControl->isNet){
							// y = x * (100/116)
							$shippingCost = $shippingCost * (100 / ((1 + ($weShippingControl->vatRate / 100)) * 100) );
						}
						break;
					case 'vat':
						$shippingCost = 0;
						break;
				}
			}
		}
		return we_util::std_numberformat($shippingCost, $num_format);
	}
	return 0;
}
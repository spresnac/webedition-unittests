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
define('SHOP_TABLE', TBL_PREFIX . 'tblOrders');
define('ANZEIGE_PREFS_TABLE', TBL_PREFIX . 'tblAnzeigePrefs');
define('WE_SHOP_VAT_TABLE', TBL_PREFIX . 'tblshopvats');
define('WE_SHOP_MODULE_DIR', WE_MODULES_DIR . 'shop/');
define('WE_SHOP_MODULE_PATH', WE_MODULES_PATH . 'shop/');

define('WE_SHOP_VARIANTS_PREFIX', 'we__intern_variant___');
define('WE_SHOP_VARIANTS_ELEMENT_NAME', 'weInternVariantElement');
define('WE_SHOP_VARIANT_REQUEST', 'we_variant');

// name of request array for shopping items
define('WE_SHOP_ARTICLE_CUSTOM_FIELD', 'we_sacf');
define('WE_SHOP_CART_CUSTOM_FIELD', 'we_sscf');
define('WE_SHOP_CART_CUSTOMER_FIELD', 'we_shopCustomer');
define('WE_SHOP_VAT_FIELD_NAME', 'shopvat'); // due to the names of old fields (shoptitle, shopdescription) - we must name shopvat
define('WE_SHOP_PRICE_IS_NET_NAME', 'we_shopPriceIsNet');
define('WE_SHOP_PRICENAME', 'we_shopPricename');
define('WE_SHOP_SHIPPING', 'we_shopPriceShipping');
define('WE_SHOP_CALC_VAT', 'we_shopCalcVat');
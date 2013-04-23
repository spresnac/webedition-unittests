<?php

/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Tue, 06 Nov 2012) $
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
// check if there are variation fields defined
$variationFields = $we_doc->getVariantFields();

// need many buttons here
// if editing data the class weShopVariants must do some stuff
// add, move, delete
// :TODO: decide where to put this
switch($_REQUEST['we_cmd'][0]){
	case 'shop_insert_variant':
		weShopVariants::insertVariant($we_doc, $_REQUEST['we_cmd'][1]);
		break;
	case "shop_move_variant_up":
		weShopVariants::moveVariant($we_doc, $_REQUEST['we_cmd'][1], 'up');
		break;
	case "shop_move_variant_down":
		weShopVariants::moveVariant($we_doc, $_REQUEST['we_cmd'][1], 'down');
		break;
	case "shop_remove_variant":
		weShopVariants::removeVariant($we_doc, $_REQUEST['we_cmd'][1]);
		break;
	case "shop_preview_variant":
		weShopVariants::correctModelFields($we_doc, false);
		require(WE_MODULES_PATH . 'shop/show_variant.inc.php');
		exit;
		break;
}


$we_editmode = true;
$parts = weShopVariants::getVariantsEditorMultiBoxArray($we_doc);

print we_multiIconBox::getHTML("", "100%", $parts, 30, "", -1, "", "", false);

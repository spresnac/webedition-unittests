<?php

/**
 * webEdition CMS
 *
 * $Rev: 5000 $
 * $Author: mokraemer $
 * $Date: 2012-10-18 23:35:06 +0200 (Thu, 18 Oct 2012) $
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
$variantFields = $we_doc->getVariantFields();

// if editing data the class weShopVariants must do some stuff
// add, move, delete
// :TODO: decide WHERE to put this
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

$parts = weShopVariants::getVariantsEditorMultiBoxArrayObjectFile($we_doc);
print we_multiIconBox::getHTML('', '100%', $parts, 30, '', -1, '', '', false);

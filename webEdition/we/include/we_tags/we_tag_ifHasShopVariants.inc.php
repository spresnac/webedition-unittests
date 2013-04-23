<?php

/**
 * webEdition CMS
 *
 * $Rev: 5603 $
 * $Author: mokraemer $
 * $Date: 2013-01-20 19:29:17 +0100 (Sun, 20 Jan 2013) $
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
 * This function returns if an article has variants
 *
 * @param	$attribs array
 *
 * @return	boolean
 */
function we_tag_ifHasShopVariants(){
	return (defined('SHOP_TABLE') && weShopVariants::getNumberOfVariants($GLOBALS['we_doc']) > 0);
}

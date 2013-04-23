<?php

/**
 * webEdition CMS
 *
 * $Rev: 5609 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 17:15:55 +0100 (Mon, 21 Jan 2013) $
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
if(isset($_REQUEST['code'])){
	exit('REQUEST[\'code\'] is forbidden!');
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

$Tree = new weMainTree('webEdition.php', 'top', 'top.resize.left.tree', 'top.load');

print $Tree->getHTMLContruct('if(top.treeResized){top.treeResized();}');

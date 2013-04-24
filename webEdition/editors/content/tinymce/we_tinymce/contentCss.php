<?php
/**
 * webEdition CMS
 *
 * $Rev: 5320 $
 * $Author: lukasimhof $
 * $Date: 2012-12-05 17:20:59 +0100 (Wed, 05 Dec 2012) $
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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
header("Content-type: text/css");

$bgcol = preg_match('/^[a-f0-9]{6}$/i', $_REQUEST['tinyMceBackgroundColor']) ? '#' . $_REQUEST['tinyMceBackgroundColor'] : $_REQUEST['tinyMceBackgroundColor'];
$bgcol = $bgcol ? $bgcol : 'white';
?>

/* css editor body */

body {
font-size: <?php print (we_base_browserDetect::isMAC()) ? "12px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px"); ?>;
background-color: <?php echo $bgcol; ?> !important;
}


/* css for plugin wevisialborders */

acronym.mceItemWeAcronym{
border: 1px dotted gray;
}

abbr.mceItemWeAbbr{
border: 1px dotted gray;
}

span.mceItemWeLang{
border: 1px dotted gray;
}
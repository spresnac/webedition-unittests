<?php

/**
 * webEdition CMS
 *
 * $Rev: 5612 $
 * $Author: mokraemer $
 * $Date: 2013-01-21 22:46:14 +0100 (Mon, 21 Jan 2013) $
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
function we_tag_tracker($attribs){
	if($GLOBALS['we_doc']->InWebEdition){
		return "";
	}
	if(!is_dir($_SERVER['DOCUMENT_ROOT'] . WE_TRACKER_DIR)){
		t_e('pagelogger not installed, but we:pagelogger called');
		return '';
	}

	$type = weTag_getAttribute("type", $attribs, "standard");
	$ssl = weTag_getAttribute("ssl", $attribs, false, true);
	$websitename = weTag_getAttribute("websitename", $attribs, $_SERVER['SERVER_NAME']);
	$trackname = weTag_getAttribute("trackname", $attribs);

	if($trackname == "WE_PATH"){
		if(isset($_REQUEST['we_objectID'])){
			$trackname = "/object" . id_to_path(intval($_REQUEST['we_objectID']), OBJECT_FILES_TABLE);
		} else{
			$trackname = $GLOBALS["WE_MAIN_DOC"]->Path;
		}
	} else
	if($trackname == "WE_TITLE"){
		$trackname = $GLOBALS["WE_MAIN_DOC"]->getElement("Title");
	}

	$trackerurl = getServerUrl() . WE_TRACKER_DIR;

	if($type == 'standard'){
		return '<!-- pageLogger Code BEGIN -->' . we_html_element::jsScript($trackerurl . '/scripts/picmodejs.js') . '
<script type="text/javascript">
<!--
_my_stat_write(\'' . $websitename . '\',\'' . $trackerurl . '\'' . ($trackname ? (",'" . addslashes(
					$trackname) . "'") : "") . ');
//-->
</script>
<noscript>
<img width="1" height="1"  alt="" src="' . $trackerurl . '/connector.php?' . $websitename . '&amp;mode=NOSCRIPT' . ($trackname ? ("&amp;trackname=" . rawurlencode(
					$trackname)) : "") . '" />
</noscript>
<!-- pageLogger Code END -->
';
	} else
	if($type == 'robot'){
		include ($_SERVER['DOCUMENT_ROOT'] . WE_TRACKER_DIR . "/spidertracker.php");
		@logspider($websitename);
	} else
	if($type == 'fileserver'){
		@include_once ($_SERVER['DOCUMENT_ROOT'] . WE_TRACKER_DIR . "/service/fileserver.php");
	} else
	if($type == 'downloads'){
		@include_once ($_SERVER['DOCUMENT_ROOT'] . WE_TRACKER_DIR . "/includes/showcat.inc.php");
		$category = weTag_getAttribute("category", $attribs);
		$order = weTag_getAttribute("order", $attribs, "FILETITLE");
		$desc = weTag_getAttribute("desc", $attribs, true, true);
		$rows = weTag_getAttribute("rows", $attribs, "10");
		showcat($category, $order, $desc ? "DESC" : "ASC", $rows, $websitename);
	}
}

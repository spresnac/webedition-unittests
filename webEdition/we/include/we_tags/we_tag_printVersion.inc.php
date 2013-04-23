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
function we_tag_printVersion($attribs, $content){
	if(($foo = attributFehltError($attribs, "tid", __FUNCTION__))){
		return $foo;
	}

	$tid = weTag_getAttribute("tid", $attribs);
	$triggerID = weTag_getAttribute("triggerid", $attribs, weTag_getAttribute("triggerID", $attribs));

	$docAttr = weTag_getAttribute("doc", $attribs);
	if(!$docAttr){
		$docAttr = weTag_getAttribute("type", $attribs);
	}

	$link = isset($attribs["Link"]) ? $attribs["Link"] : "";
	if(!$link){
		$link = isset($attribs["link"]) ? $attribs["link"] : "";
	}

	$doc = we_getDocForTag($docAttr);

	$id = isset($doc->OF_ID) ? $doc->OF_ID : $doc->ID;

	$_query_string = array();

	$hideQuery = array("we_objectID", "tid", "id", "pv_tid", "pv_id", 'we_cmd', "responseText", "we_mode", "btype");
	if(isset($_SESSION)){
		$hideQuery[] = session_name();
	}
	if(isset($_REQUEST)){
		$tmp = filterXss($_REQUEST);
		foreach($tmp as $k => $v){
			if((!is_array($v)) && (!in_array($k, $hideQuery))){
				$_query_string[$k] = $v;
			}
		}
	}

	if(isset($doc->TableID)){
		if($triggerID){
			$_query_string['we_objectID'] = $id;
			$_query_string['tid'] = $tid;
			$url = id_to_path($triggerID);
		} else{
			$_query_string['we_cmd[0]'] = 'preview_objectFile';
			$_query_string['we_objectID'] = $id;
			$_query_string['we_cmd[2]'] = $tid;
			$url = WEBEDITION_DIR . 'we_cmd.php';
		}
	} else{
		if($triggerID){
			$_query_string['pv_id'] = $id;
			$_query_string['pv_tid'] = $tid;
			$url = id_to_path($triggerID);
		} else{
			$_query_string['we_cmd[0]'] = 'show';
			$_query_string['we_cmd[1]'] = $id;
			$_query_string['we_cmd[4]'] = $tid;
			$url = WEBEDITION_DIR . 'we_cmd.php';
		}
	}

	if($link == "off" || $link == "false"){
		return $url . '?' . http_build_query($_query_string);
	} else{
		$attribs = removeAttribs($attribs, array('tid', 'triggerID', 'triggerid', 'doc', 'type', 'link', 'Link')); //	not html - valid
		$attribs['href'] = $url . '?' . http_build_query($_query_string);
		return getHtmlTag('a', $attribs, $content, true);
	}
}

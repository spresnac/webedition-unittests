<?php

/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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
function we_tag_econda($attribs){

	$type = weTag_getAttribute("type", $attribs);
	if($type == "exclude" && !$GLOBALS['we_doc']->InWebEdition){
		return '<script type="text/javascript">
//<!--
	var emosTrackClicks=false;
//-->
</script>' . "\n";
	} else if($type == "content"){
		$retEdit = "";
		$contentType = weTag_getAttribute("labelFrom", $attribs, "path");
		$retView = '<?php $GLOBALS["weEconda"]["content"]["from"] = "' . $contentType . '"; ?>';
		switch($contentType){
			case "input":
				$name = "econda_content";
				$value = weTag_getAttribute("value", $attribs);
				$contentLabel = oldHtmlspecialchars(isset($GLOBALS['we_doc']->elements["econda_content"]["dat"]) ? $GLOBALS['we_doc']->getElement("econda_content") : $value);
				$retEdit = '<input onchange="_EditorFrame.setEditorIsHot(true);" class="wetextinput" type="text" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' />';
				$retView .= '<a name="emos_name" title="content" rel="' . $contentLabel . '" rev=""></a>';
				break;
			case "hidden":
				$name = "econda_content";
				$value = weTag_getAttribute("value", $attribs);
				$contentLabel = oldHtmlspecialchars(isset($GLOBALS['we_doc']->elements["econda_content"]["dat"]) ? $GLOBALS['we_doc']->getElement("econda_content") : $value);
				$retEdit = '<input onchange="_EditorFrame.setEditorIsHot(true);" type="hidden" name="we_' . $GLOBALS['we_doc']->Name . '_txt[' . $name . ']" value="' . $val . '"' . ($attr ? " $attr" : "") . ' />';
				$retView .= '<a name="emos_name" title="content" rel="' . $contentLabel . '" rev=""></a>';
				break;
		}

		if($GLOBALS['we_editmode']){
			return $retEdit;
		} else if(!$GLOBALS['we_doc']->InWebEdition){
			return $retView;
			//return '<a name="emos_name" title="content" rel="'.$contentLabel.'" rev=""></a>';
		}
	} else if($type == "orderProcess"){
		if(!$GLOBALS['we_doc']->InWebEdition){
			$step = weTag_getAttribute("step", $attribs);
			$pageName = weTag_getAttribute("pageName", $attribs);
			return '<a name="emos_name" title="orderProcess" rel="' . $step . '_' . $pageName . '" rev=""></a>';
		}
	} else{

	}
}
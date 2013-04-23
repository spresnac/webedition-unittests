<?php

/**
 * webEdition CMS
 *
 * $Rev: 5009 $
 * $Author: mokraemer $
 * $Date: 2012-10-24 00:36:50 +0200 (Wed, 24 Oct 2012) $
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
function we_parse_tag_navigationEntry($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('navigationEntry', $attribs, str_replace('global $lv;', '', $content), true) . ');?>';
}

function we_tag_navigationEntry($attribs, $content){
	if(($foo = attributFehltError($attribs, 'type', __FUNCTION__))){
		echo $foo;
		return;
	}

	$navigationName = weTag_getAttribute('navigationname', $attribs, "default");
	$type = weTag_getAttribute('type', $attribs);
	$level = weTag_getAttribute('level', $attribs, 'defaultLevel');
	$current = weTag_getAttribute('current', $attribs, 'defaultCurrent');
	$positions = makeArrayFromCSV(weTag_getAttribute('position', $attribs, 'defaultPosition'));

	foreach($positions as $position){
		if($position == 'first'){
			$position = 1;
		}
		$GLOBALS['we_navigation'][$navigationName]->setTemplate($content, $type, $level, $current, $position);
	}
}

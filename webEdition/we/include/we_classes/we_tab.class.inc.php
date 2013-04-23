<?php

/**
 * webEdition CMS
 *
 * $Rev: 4686 $
 * $Author: mokraemer $
 * $Date: 2012-07-08 02:15:42 +0200 (Sun, 08 Jul 2012) $
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
class we_tab{

	private $tab;

	function __construct($href, $text, $status = 'TAB_NORMAL', $jscmd = '', $attribs = array()){
		$class = ($status == 'TAB_ACTIVE' ? 'tabActive' : 'tabNormal');
		$att = '';
		if(isset($attribs) && is_array($attribs)){
			foreach($attribs as $key => $val){
				$att .= $key . '="' . $val . '" ';
			}
		}

		$this->tab = '<div ' . $att . ' onclick="if ( allowed_change_edit_page() ){ setTabClass(this); ' . $jscmd . '}" class="' . $class . '"><nobr><span class="spacer">&nbsp;&nbsp;</span><span class="text">' . $text . '</span>&nbsp;&nbsp;<img src="' . IMAGE_DIR . 'multiTabs/tabBorder.gif" height="21" style="vertical-align:bottom;" /></nobr>' . (we_base_browserDetect::isSafari() ? '<span><img src="' . IMAGE_DIR . 'pixel.gif" height="0" /></span>' : '') . '</div>';
	}

	function getHTML(){
		return $this->tab;
	}

}

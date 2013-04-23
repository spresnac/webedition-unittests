<?php

/**
 * webEdition CMS
 *
 * $Rev: 3750 $
 * $Author: mokraemer $
 * $Date: 2012-01-07 02:14:44 +0100 (Sat, 07 Jan 2012) $
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
class weTagData_linkAttribute extends weTagDataAttribute{

	/**
	 * @param string $name
	 * @param boolean $required
	 */
	function __construct($name, $required = false, $module = '', $value='', $description='', $deprecated=false){

		parent::__construct($name, $required, $module, $description, $deprecated);
		$this->Value = $value;
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){
		return '<table class="attribute"><tr>
						<td class="attributeName defaultfont">&nbsp;</td><td class="attributeField">' . we_html_element::htmlSpan(
				array(
				'name' => $this->Name,
				'id' => $this->getIdName(),
				'value' => '',
				'class' => 'defaultfont'
				), '<a href="http://' . $this->Value . '" target="TagRef">' . g_l('taged', '[tagreference_linktext]') . '</a>') . '</td>
					</tr></table>';
	}

}
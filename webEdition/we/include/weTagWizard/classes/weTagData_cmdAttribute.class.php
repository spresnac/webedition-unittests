<?php

/**
 * webEdition CMS
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
class weTagData_cmdAttribute extends weTagDataAttribute{

	/**
	 * @var array
	 */
	var $Options;

	/**
	 * @var string
	 */
	var $Text;

	/**
	 * @param string $name
	 * @param boolean $required
	 */
	function __construct($name, $required = false, $module = '', $Options, $Text, $description='', $deprecated=false){
		parent::__construct($name, $required, $module, $description, $deprecated);
		$this->Options = $Options;
		$this->Text = $Text;
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){
		return sprintf('<table class="attribute"><tr><td class="attributeName defaultfont">&nbsp;</td><td class="attributeField">%s</td></tr>
			</table>', we_html_element::htmlSpan(
					array(
					'name' => $this->Name,
					'id' => $this->getIdName(),
					'value' => '',
					'class' => 'defaultfont',
					), sprintf('<a href="#" onclick="we_cmd(%s);">%s</a>', '\'' . implode('\',\'', $this->Options) . '\'', $this->Text)
				)
		);
	}

}

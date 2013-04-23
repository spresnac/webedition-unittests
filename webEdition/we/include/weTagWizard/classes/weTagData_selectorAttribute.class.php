<?php

/**
 * webEdition CMS
 *
 * $Rev: 3700 $
 * $Author: mokraemer $
 * $Date: 2012-01-02 02:21:39 +0100 (Mon, 02 Jan 2012) $
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
class weTagData_selectorAttribute extends weTagDataAttribute{

	/**
	 * @var string
	 */
	var $Table;

	/**
	 * @var string
	 */
	var $Selectable;

	/**
	 * @param string $name
	 * @param string $table
	 * @param string $selectable
	 * @param boolean $required
	 */
	function __construct($name, $table, $selectable, $required = false, $module = '', $description='', $deprecated=false){
		$this->Table = $table;
		$this->Selectable = $selectable;

		parent::__construct($name, $required, $module, $description, $deprecated);
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){

		$weCmd = 'openDocselector';

		if($this->Selectable == 'folder'){
			$weCmd = 'openDirselector';
		}

		if($this->Table == CATEGORY_TABLE){
			$weCmd = 'openCatselector';
			$this->Selectable = '';
		}

		if($this->Table == NAVIGATION_TABLE){
			$weCmd = 'openSelector';
		}

		$input = we_html_element::htmlInput(
				array(
					'name' => $this->Name,
					'value' => $this->Value,
					'id' => $this->getIdName(),
					'class' => 'wetextinput'
			));
		$wecmdenc1 = we_cmd_enc("document.getElementById('" . $this->getIdName() . "').value");
		$button = we_button::create_button(
				"select", "javascript:we_cmd('" . $weCmd . "', document.getElementById('" . $this->getIdName() . "').value, '" . $this->Table . "','" . $wecmdenc1 . "', '', '', '" . session_id() . "', '', '" . $this->Selectable . "')");

		return '<table class="attribute"><tr>
						<td class="attributeName">' . $this->getLabelCodeForTagWizard() . '</td>
						<td class="attributeField">' . $input . '</td>
						<td class="attributeButton">' . $button . '</td>
					</tr></table>';
	}

}


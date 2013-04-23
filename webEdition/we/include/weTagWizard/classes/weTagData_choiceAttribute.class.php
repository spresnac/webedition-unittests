<?php

/**
 * webEdition CMS
 *
 * $Rev: 5594 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:19:42 +0100 (Sat, 19 Jan 2013) $
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
class weTagData_choiceAttribute extends weTagDataAttribute{

	/**
	 * @var array
	 */
	var $Options;

	/**
	 * @var boolean
	 */
	var $Multiple;

	/**
	 * @param string $name
	 * @param array $options
	 * @param boolean $required
	 */
	function __construct($name, $options = array(), $required = false, $multiple = true, $module = '', $description='', $deprecated=false){

		parent::__construct($name, $required, $module, $description, $deprecated);
		$this->Options = parent::getUseOptions($options);
		$this->Multiple = $multiple;
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){

		$texts = array();
		$values = array();

		$texts[] = '----';
		$values[] = '';

		foreach($this->Options as $option){

			$texts[] = $option->getName();
			$values[] = htmlentities($option->Value);
		}

		// get html for choice box


		if($this->Multiple){
			$jsSelect = 'var valSel=this.options[this.selectedIndex].value; var valTa = document.getElementById(\'' . $this->getIdName() . '\').value; document.getElementById(\'' . $this->getIdName() . '\').value=((valTa==\'\' || (valSel==\'\')) ? valSel : (valTa+\',\'+valSel));';
		} else{
			$jsSelect = 'document.getElementById(\'' . $this->getIdName() . '\').value=this.options[this.selectedIndex].value;';
		}

		$select = new we_html_select(array(
				'onchange' => $jsSelect, 'class' => 'defaultfont selectinput'
			));
		$select->addOptions(count($texts), $values, $texts);

		return '
					<table class="attribute">
					<tr>
						<td class="attributeName">' . $this->getLabelCodeForTagWizard() . '</td>
						<td class="attributeField">' . we_html_element::htmlInput(
				array(
					'name' => $this->Name,
					'value' => $this->Value,
					'id' => $this->getIdName(),
					'class' => 'wetextinput'
			)) . '</td>
						<td class="attributeButton">' . $select->getHtml() . '</td>
					</tr>
					</table>';
	}

}

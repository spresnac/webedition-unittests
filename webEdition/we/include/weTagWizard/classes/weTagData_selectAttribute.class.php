<?php

/**
 * webEdition CMS
 *
 * $Rev: 4882 $
 * $Author: mokraemer $
 * $Date: 2012-08-16 20:30:09 +0200 (Thu, 16 Aug 2012) $
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
class weTagData_selectAttribute extends weTagDataAttribute{

	/**
	 * @var array
	 */
	var $Options;

	/**
	 * @param string $name
	 * @param array $options
	 * @param boolean $required
	 */
	function __construct($name, $options = array(), $required = false, $module = '', $description = '', $deprecated = false){
		parent::__construct($name, $required, $module, $description, $deprecated);
		$this->Options = parent::getUseOptions($options);
	}

	/**
	 * @return string
	 */
	function getCodeForTagWizard(){

		$keys = array();
		$values = array();

		if(!$this->Required){
			$keys[] = '';
			$values[] = '';
		}

		foreach($this->Options as $option){

			$keys[] = $option->Value;
			$values[] = $option->getName();
		}

		$select = new we_html_select(
				array(
					'name' => $this->getName(), 'id' => $this->getIdName(), 'class' => 'defaultfont selectinput'
			));
		$select->addOptions(count($values), $keys, $values);

		$select->selectOption($this->Value);

		return '<table class="attribute"><tr>
						<td class="attributeName">' . $this->getLabelCodeForTagWizard() . '</td>
						<td class="attributeField">' . $select->getHtml() . '</td>
					</tr></table>';
	}

}
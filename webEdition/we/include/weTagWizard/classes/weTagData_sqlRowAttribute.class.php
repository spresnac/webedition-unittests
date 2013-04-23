<?php

/**
 * webEdition CMS
 *
 * $Rev: 5744 $
 * $Author: mokraemer $
 * $Date: 2013-02-07 00:43:30 +0100 (Thu, 07 Feb 2013) $
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
class weTagData_sqlRowAttribute extends weTagData_selectAttribute{

	/**
	 * @var string
	 */
	var $Table;

	/**
	 * @var string
	 */
	var $ValueName;

	/**
	 * @var string
	 */
	var $TextName;

	/**
	 * @param string $name
	 * @param string $table
	 * @param boolean $required
	 * @param string $valueName
	 * @param string $textName
	 * @param string $order
	 */
	function __construct($name, $table, $required = false, $valueName = 'ID', $textName = 'Text', $order = 'Text', $module = '', $description='', $deprecated=false){

		global $DB_WE;
		$this->Table = $table;
		$this->ValueName = $valueName;
		$this->TextName = $textName ? $textName : $valueName;

		$options = array();

		// get options from choosen table
		$items = array();

		$DB_WE->query('SELECT ' . $DB_WE->escape($this->ValueName) . ',' . $DB_WE->escape($this->TextName) . ' FROM ' . $DB_WE->escape($this->Table) . ' ' . ($order ? 'ORDER BY '.$order : ''));

		while($DB_WE->next_record()) {

			$options[] = new weTagDataOption($DB_WE->f($this->TextName), $DB_WE->f($this->ValueName));
		}
		parent::__construct($name, $options, $required, $module, $description, $deprecated);
	}

}

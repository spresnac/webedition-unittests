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
class weTagData_sqlColAttribute extends weTagData_selectAttribute{

	/**
	 * @var string
	 */
	var $Table;

	/**
	 * @param string $name
	 * @param string $table
	 * @param boolean $required
	 * @param array $filter
	 */
	function __construct($name, $table, $required = false, $filter = array(), $module = '', $description='', $deprecated=false){

		$this->Table = $table;

		$options = array();

		// get options from choosen table
		$items = array();
		$tableInfo = $GLOBALS['DB_WE']->metadata($this->Table);
		sort($tableInfo); // #3490

		for($i = 0; $i < count($tableInfo); $i++){

			if(!in_array($tableInfo[$i]['name'], $filter)){
				$options[] = new weTagDataOption($tableInfo[$i]['name']);
			}
		}
		parent::__construct($name, $options, $required, $module, $description, $deprecated);
	}

}

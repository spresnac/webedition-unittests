<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
/**
 * @see we_ui_abstract_AbstractFormElement
 */
Zend_Loader::loadClass('we_ui_controls_Select');

/**
 * Class to display a Select
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_controls_SelectObjectclass extends we_ui_controls_Select{
	/**
	 * Default class name for Select
	 */

	const kSelectClass = 'we_ui_controls_Select';

	/**
	 * class name for disabled Select
	 */
	const kSelectClassDisabled = 'we_ui_controls_Select_disabled';

	/**
	 * Constructor
	 *
	 * Sets object properties if set in $properties array
	 *
	 * @param array $properties associative array containing named object properties
	 * @return void
	 */
	public function __construct($properties = null){
		parent::__construct($properties);
		include ($_SERVER['DOCUMENT_ROOT'] . "/webEdition/we/include/conf/we_active_integrated_modules.inc.php");
		if(in_array('object', $GLOBALS['_we_active_integrated_modules'])){
			if(file_exists(WE_MODULES_PATH . "object/we_conf_object.inc.php")){
				include_once (WE_MODULES_PATH . "object/we_conf_object.inc.php");
				$db = new DB_WE();
				$db->query("SELECT ID,Text FROM " . OBJECT_TABLE);
				$this->addOption(0, '-');
				while($db->next_record()) {
					$this->addOption($db->f("ID"), $db->f("Text"));
				}
			}
		}
		// add needed CSS files
		$this->addCSSFile(we_ui_layout_Themes::computeCSSURL(__CLASS__));

		// add needed JS Files
		$this->addJSFile(we_ui_abstract_AbstractElement::computeJSURL(__CLASS__));
	}

}
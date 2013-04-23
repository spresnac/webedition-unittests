<?php
/**
 * webEdition CMS
 *
 * $Rev: 3029 $
 * $Author: mokraemer $
 * $Date: 2011-07-06 22:39:56 +0200 (Wed, 06 Jul 2011) $
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
 * @package    webEdition_toolfactory
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


		
	/**
	 * if hook execution is enabled this function will be executed 
	 * when saving an entry or folder in the application toolfactory
	 * Files in the sample_hooks folder are not executed and are not update-safe and will be overwritten by the next webEdition update
	 * 
	 * @param array $param
	 */	
	function weCustomHook_toolfactory_save($param) { 
	
		/**
		 * e.g.:
		 * 
		 * ob_start("error_log");
		 * print_r($param);
		 * ob_end_clean();
		 */

	}

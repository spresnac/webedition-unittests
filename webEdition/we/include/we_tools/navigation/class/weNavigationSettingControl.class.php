<?php

/**
 * webEdition CMS
 *
 * $Rev: 5044 $
 * $Author: mokraemer $
 * $Date: 2012-11-01 17:59:55 +0100 (Thu, 01 Nov 2012) $
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
class weNavigationSettingControl{

	function saveSettings($default = false){
		if($_SESSION["perms"]["ADMINISTRATOR"]){
			if($default){
				$CacheLifeTime = '';
				$Add = 'true';
				$Edit = 'true';
				$Delete = 'true';
			} else{
				$CacheLifeTime = (int) str_replace("'", "", $_REQUEST['CacheLifeTime']);

				$Add = 'false';
				if($_REQUEST['NavigationCacheAdd'] == 1){
					$Add = 'true';
				}

				$Edit = 'false';
				if($_REQUEST['NavigationCacheEdit'] == 1){
					$Edit = 'true';
				}

				$Delete = 'false';
				if($_REQUEST['NavigationCacheDelete'] == 1){
					$Delete = 'true';
				}
			}

			$code = <<<EOF
<?php

\$GLOBALS['weDefaultNavigationCacheLifetime'] = '{$CacheLifeTime}';

\$GLOBALS['weNavigationCacheDeleteAfterAdd'] = {$Add};
\$GLOBALS['weNavigationCacheDeleteAfterEdit'] = {$Edit};
\$GLOBALS['weNavigationCacheDeleteAfterDelete'] = {$Delete};

EOF;

			$languageFile = WE_INCLUDES_PATH . 'we_tools/navigation/conf/we_conf_navigation.inc.php';
			return weFile::save($languageFile, $code, "w+");
		}
	}

}
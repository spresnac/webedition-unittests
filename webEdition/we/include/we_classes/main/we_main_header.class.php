<?php
/**
 * webEdition CMS
 *
 * $Rev: 4220 $
 * $Author: mokraemer $
 * $Date: 2012-03-08 17:59:01 +0100 (Thu, 08 Mar 2012) $
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

class we_main_header{

	static function pCSS(){
		we_main_headermenu::pCSS();
		if(self::hasMsg()){
			we_messaging_headerMsg::pCSS();
		}
	}

	static function pJS(){
		we_main_headermenu::pJS();
		if(self::hasMsg()){
			we_messaging_headerMsg::pJS();
		}
	}

	private static function hasMsg(){
		return (defined("MESSAGING_SYSTEM") && (!isset($_REQUEST["SEEM_edit_include"]) || !$_REQUEST["SEEM_edit_include"] ));
	}

	static function pbody(){
		$msg=self::hasMsg();
		?>
		<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;border:0px;background-color:#efefef;background-image: url(<?php print IMAGE_DIR ?>java_menu/background.gif); background-repeat: repeat-x;">
			<div style="position:absolute;top:0px;bottom:0px;left:0px;right:<?php echo $msg?'60':'0'?>px;"><?php
		we_main_headermenu::pbody();
		?>
			</div>
		<?php if($msg){ ?>
				<div style="position:absolute;top:0px;bottom:0px;right:5px;width:60px;">
					<?php we_messaging_headerMsg::pbody();
					?>
				</div>
		<?php } ?>
		</div>
		<?php
	}

}
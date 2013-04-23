<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
class we_main_headermenu{

	static function pCSS(){
		echo self::css();
	}

	static function css(){
		$ret = we_html_element::cssLink(WEBEDITION_DIR . 'css/menu/pro_drop_1.css');
		switch(we_base_browserDetect::inst()->getBrowser()){
			case we_base_browserDetect::CHROME:
			case we_base_browserDetect::SAFARI:
				$ret.=we_html_element::cssLink(WEBEDITION_DIR . 'css/menu/pro_drop_safari.css');
				break;
		}
		if(we_base_browserDetect::inst()->isMAC()){
			$ret.=we_html_element::cssLink(WEBEDITION_DIR . 'css/menu/pro_drop_mac.css');
		}
		if(we_base_browserDetect::isIE() && intval(we_base_browserDetect::inst()->getBrowserVersion()) < 9){
			$ret.=we_html_element::jsScript(WEBEDITION_DIR . 'css/menu/clickMenu_IE8.js');
		} else{
			$ret.=we_html_element::jsScript(WEBEDITION_DIR . 'css/menu/clickMenu.js');
		}
		return $ret;
	}

	static function pJS(){
		$jmenu = self::getMenu();

		echo we_html_element::jsScript(JS_DIR . 'images.js') .
		we_html_element::jsScript(JS_DIR . 'weSidebar.php') .
		($jmenu ? $jmenu->getJS() : '');
		we_html_element::jsElement('
top.weSidebar = weSidebar;

	preload("busy_icon","' . IMAGE_DIR . 'logo-busy.gif");
	preload("empty_icon","' . IMAGE_DIR . 'pixel.gif");
	function toggleBusy(foo){
		if(!document.images["busy"]){
			setTimeout("toggleBusy("+foo+")",200);
		}else{
			changeImage(null,"busy",(foo ? "busy_icon" : "empty_icon"));
		}
	}
');
	}

	static function getMenuReloadCode($location = 'top.opener.'){
		$menu = self::getMenu();
		$menu = str_replace("\n", '"+"', addslashes($menu->getHTML(false)));
		return $location . 'document.getElementById("nav").parentNode.innerHTML="' . $menu . '";';
	}

	static function getMenu(){
		if(isset($_REQUEST["SEEM_edit_include"])){ // there is only a menu when not in seem_edit_include!
			return null;
		}
		include(WE_INCLUDES_PATH . "java_menu/we_menu.inc.php");
		ksort($we_menu);
		if(// menu for normalmode
			isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == "normal"){

			$jmenu = new weJavaMenu($we_menu, "top.load");
		} else{ // menu for seemode
			if(permissionhandler::isUserAllowedForAction("header", "with_java")){
				$jmenu = new weJavaMenu($we_menu, "top.load");
			} else{
				return null;
			}
		}

		return $jmenu;
	}

	static function pbody(){

// all available elements
		$jmenu = self::getMenu();
		$navigationButtons = array();

		if(!isset($_REQUEST["SEEM_edit_include"])){ // there is only a menu when not in seem_edit_include!
			if(// menu for normalmode
				isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == "normal"){

			} else{ // menu for seemode
				if(permissionhandler::isUserAllowedForAction("header", "with_java")){

				} else{
//  no menu in this case !
					$navigationButtons[] = array(
						"onclick" => "top.we_cmd('dologout');",
						"imagepath" => "/navigation/close.gif",
						"text" => g_l('javaMenu_global', "[close]")
					);
				}
			}
			$navigationButtons = array_merge($navigationButtons, array(
				array("onclick" => "top.we_cmd('start_multi_editor');", "imagepath" => "/navigation/home.gif", "text" => g_l('javaMenu_global', "[home]")),
				array("onclick" => "top.weNavigationHistory.navigateReload();", "imagepath" => "/navigation/reload.gif", "text" => g_l('javaMenu_global', "[reload]")),
				array("onclick" => "top.weNavigationHistory.navigateBack();", "imagepath" => "/navigation/back.gif", "text" => g_l('javaMenu_global', "[back]")),
				array("onclick" => "top.weNavigationHistory.navigateNext();", "imagepath" => "/navigation/next.gif", "text" => g_l('javaMenu_global', "[next]")),
				)
			);
		}
		?>
		<div style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;border:0px;">
			<div style="position:relative;border:0px;float:left;" >
				<?php
				if($jmenu){
					print $jmenu->getCode(false);
				}
				?>
			</div>
			<div style="position:relative;bottom:0px;border:0px;padding-left: 10px;float:left;" >
				<?php
				if(!empty($navigationButtons)){
					foreach($navigationButtons as $button){
						print '<div style = "float:left;margin-top:5px;" class = "navigation_normal" onclick = "' . $button['onclick'] . '" onmouseover = "this.className=\'navigation_hover\'" onmouseout = "this.className=\'navigation_normal\'"><img border = "0" hspace = "2" src = "' . IMAGE_DIR . $button['imagepath'] . '" width = "17" height = "18" alt = "' . $button['text'] . '" title = "' . $button['text'] . '"></div>';
					}
				}
				?></div>
			<div style="position:absolute;top:0px;bottom:0px;right:10px;border:0px;" >


				<?php
				include_once(WE_INCLUDES_PATH . "jsMessageConsole/messageConsole.inc.php" );
				print createMessageConsole("mainWindow");
				?>
				<img src="<?php print IMAGE_DIR ?>pixel.gif" alt="" name="busy" width="20" height="19">
				<img src="<?php print IMAGE_DIR ?>webedition.gif" alt="" style="width:78px;height:25px;padding-left: 10px;padding-right: 5px;padding-top:3px;">
			</div>
		</div>
		<?php
	}

}
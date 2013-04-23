/**
 * webEdition CMS
 *
 * $Rev: 5365 $
 * $Author: mokraemer $
 * $Date: 2012-12-15 15:12:58 +0100 (Sat, 15 Dec 2012) $
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
var isRegisterDialogHere = true;
var tinyMceDialog = null;
var tinyMceSecondaryDialog = null;
var tinyMceFullscreenDialog = null;
var blocked = false;

function weRegisterTinyMcePopup(win,action){
		win = typeof(win) != "undefined" ? win : null;
		switch (action) {
			case "registerDialog":
				if(!blocked){
					try{
						tinyMceDialog.close();
					} catch(err){}
					tinyMceDialog = win;
				} else {
					blocked = false;
				}
				if(tinyMceSecondaryDialog !== null){
					try{
						tinyMceSecondaryDialog.close();
					}catch(err){}
				}
				break;
			case "registerSecondaryDialog":
				if(tinyMceSecondaryDialog !== null){
					try{
						tinyMceSecondaryDialog.close();
					}catch(err){}
				}
				tinyMceSecondaryDialog = win;
				break;
			case "registerFullscreenDialog":
				if(tinyMceDialog !== null){
					try{
						tinyMceDialog.close();
					}catch(err){}
				}
				if(tinyMceSecondaryDialog !== null){
					try{
						tinyMceSecondaryDialog.close();
					}catch(err){}
				}
				if(tinyMceFullscreenDialog !== null){
					try{
						tinyMceFullscreenDialog.close();
					}catch(err){}
				}
				tinyMceFullscreenDialog = win;
				break;
			case "block":
				blocked = true;
				break;
			case "skip":
				// do nothing!
				break;
			case "unregisterDialog":
				try{
					tinyMceDialog.close();
				}catch(err){}
			case "unregisterSecondaryDialog":
				try{
					tinyMceSecondaryDialog.close();
				}catch(err){}
				break;
		}

}
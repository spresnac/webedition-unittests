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
 * Class for handling we_ui_controls_ACFileSelector Element
 * 
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

we_ui_controls_WeWysiwygEditor = new Object();
/**
 * enables / disables TextInput and Button of AC element
 *
 *@static
 *@param {object|string} idOrObject id or reference of input element
 *@param {boolean} disabled flag that indicates if text field is disabled or not
 *@return void
 */

we_ui_controls_WeWysiwygEditor.setDisabled = function(idOrObject, disabled) 
{
	if (document.getElementById('yuiWysiwigButton_'+idOrObject)) {
		we_ui_controls_Button.setDisabled('yuiWysiwigButton_'+idOrObject, disabled);
	}
}

we_ui_controls_WeWysiwygEditor.setData = function(idOrObject, data) 
{
	if (document.getElementById(idOrObject)) {
		document.getElementById(idOrObject).innerHTML=base64_decode(data);
	}
}
we_ui_controls_WeWysiwygEditor.setDataView = function(idOrObject, data) 
{
	if (document.getElementById(idOrObject+'_View')) {
		document.getElementById(idOrObject+'_View').innerHTML=base64_decode(data);
	}
}

/**
 * opens the wysiwyg window for a tool
 *
 *@static
 *@return void
 */

we_ui_controls_WeWysiwygEditor.openWeWysiwyg = function() 
{
	var args = "";
	var url = "/webEdition/editors/content/wysiwyg/WeWysiwygEditorWindow.php?"; 
	url += "we_cmd[0]="+escape(arguments[0])+"&";
	url += "we_cmd[1]="+escape(arguments[1])+"&";
	url += "we_cmd[2]="+escape(arguments[2]-50)+"&";
	url += "we_cmd[3]="+escape(arguments[3]-100)+"&";
	url += "we_cmd[4]=&";
	url += "we_cmd[5]="+escape(arguments[5])+"&";
	url += "we_cmd[6]=&";
	url += "we_cmd[7]="+escape(arguments[7])+"&"
	url += "we_cmd[8]=&we_cmd[9]=337&we_cmd[10]=94&we_cmd[11]=1&we_cmd[12]=1&we_cmd[13]=&we_cmd[14]=&we_cmd[15]=UTF-8&";
	url += "we_cmd[16]="+escape(arguments[6])+"&";
	url += "we_cmd[17]=";
	new jsWindow(url,"we_"+arguments[4]+"_wysiwyg",-1,-1,arguments[2]-30,arguments[3]+70,true,true,true);
}



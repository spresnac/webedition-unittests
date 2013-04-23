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

 * Class for handling we_ui_controls_TextField Element

 * 

 * @category   we

 * @package    we_ui

 * @subpackage we_ui_controls

 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL

 */

we_ui_controls_DateTime = new Object();



/**

 * enables / disables TextInput element

 *

 *@static

 *@param {object|string} idOrObject id or reference of input element

 *@param {boolean} disabled flag that indicates if text field is disabled or not

 *@return void

 */

we_ui_controls_DateTime.setDisabled = function(idOrObject, disabled) 

{

	var element = idOrObject;

	if (typeof(element) != "object") {

		element = document.getElementById(idOrObject);

	}

	element.disabled = disabled;

}

we_ui_controls_DateTime.setDateTimeValueOnChange = function(idOrObject) 

{

	var element = idOrObject;
	var datumMS = new Date(document.getElementById(idOrObject+'_years').value, document.getElementById(idOrObject+'_months').value - 1, document.getElementById(idOrObject+'_days').value, document.getElementById(idOrObject+'_hours').value, document.getElementById(idOrObject+'_minutes').value,document.getElementById(idOrObject+'_seconds').value);
	if (typeof(element) != "object") {

		element = document.getElementById(idOrObject);

	}

	element.value = datumMS.getTime()/1000;

}
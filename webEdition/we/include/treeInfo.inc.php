<?php
/**
 * webEdition CMS
 *
 * $Rev: 5359 $
 * $Author: mokraemer $
 * $Date: 2012-12-14 14:18:19 +0100 (Fri, 14 Dec 2012) $
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
?>


	<div id="infoField" style="margin:5px; display: none;" class="defaultfont"></div>
	<form name="we_form" onsubmit="top.we_cmd('tool_weSearch_edit',document.we_form.keyword.value, top.treeData.table); return false;">
		<div id="search" style="margin: 10px 0 0 10px;">
			<?php
			print we_button::create_button_table(
					array(
						we_html_tools::htmlTextInput('keyword',10,(isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : ''),'','','search','120px'),
						we_button::create_button('image:btn_function_search', "javascript:top.we_cmd('tool_weSearch_edit',document.we_form.keyword.value, top.treeData.table);",true,40)
					)
			);?>
		</div>
	</form>

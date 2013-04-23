<?php
/**
 * webEdition CMS
 *
 * $Rev: 4396 $
 * $Author: mokraemer $
 * $Date: 2012-04-08 21:04:10 +0200 (Sun, 08 Apr 2012) $
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

abstract class liveUpdateTemplates {

	/**
	 * returns standard html container for output
	 *
	 * @param string $headline
	 * @param string $content
	 * @param integer $width
	 * @param integer $height
	 * @return string
	 */
	function getContainer($headline, $content) {


		return "<div id=\"leWizardContent\" class=\"defaultfont\">
			<h1>{$headline}</h1>
			<p>
				{$content}
			</p>
			{$buttonDiv}
		</div>";

	}

	/**
	 * returns header of template
	 *
	 * @return string
	 */
	function getHtmlHead() {

		return we_html_tools::htmlMetaCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']);
	}

	/**
	 * Returns a html page as response
	 *
	 * @param string $headline
	 * @param string $content
	 * @param string $header
	 * @param string $buttons
	 * @param integer $contentWidth
	 * @param integer $contentHeight
	 * @return string
	 */
	function getHtml($headline, $content, $header='', $append = false) {

/*		if($appendContent) {
			$PushJs = 'parent.leWizardContent.appendElement(document.getElementById("leWizardContent"));';

		} else {*/
			$PushJs = 'parent.leWizardContent.replaceElement(document.getElementById("leWizardContent"));';

		//}

		return we_html_tools::headerCtCharset('text/html',$GLOBALS['WE_BACKENDCHARSET']).we_html_element::htmlDocType().'<html><head>' . liveUpdateTemplates::getHtmlHead() . '
	' . $header . '
	</head><body>' . liveUpdateTemplates::getContainer($headline, $content) . we_html_element::jsElement($PushJs) . '
	</body>
</html>';
	}
}

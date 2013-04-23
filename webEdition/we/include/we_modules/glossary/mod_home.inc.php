<?php
/**
 * webEdition CMS
 *
 * $Rev: 3464 $
 * $Author: mokraemer $
 * $Date: 2011-11-20 19:01:05 +0100 (Sun, 20 Nov 2011) $
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

$createAbbreviation = we_button::create_button("new_glossary_abbreviation", "javascript:top.opener.top.we_cmd('new_glossary_abbreviation');", true, -1, -1, "", "", !we_hasPerm("NEW_GLOSSARY"));
$createAcronym = we_button::create_button("new_glossary_acronym", "javascript:top.opener.top.we_cmd('new_glossary_acronym');", true, -1, -1, "", "", !we_hasPerm("NEW_GLOSSARY"));
$createForeignWord = we_button::create_button("new_glossary_foreignword", "javascript:top.opener.top.we_cmd('new_glossary_foreignword');", true, -1, -1, "", "", !we_hasPerm("NEW_GLOSSARY"));
$createLink = we_button::create_button("new_glossary_link", "javascript:top.opener.top.we_cmd('new_glossary_link');", true, -1, -1, "", "", !we_hasPerm("NEW_GLOSSARY"));
$createTextReplacement = we_button::create_button("new_glossary_textreplacement", "javascript:top.opener.top.we_cmd('new_glossary_textreplacement');", true, -1, -1, "", "", !we_hasPerm("NEW_GLOSSARY"));

$content	=		$createAbbreviation
				.	we_html_tools::getPixel(2,10)
				.	$createAcronym
				.	we_html_tools::getPixel(2,10)
				.	$createForeignWord
				.	we_html_tools::getPixel(2,10)
				.	$createLink
				.	we_html_tools::getPixel(2,10)
				.	$createTextReplacement;

$modimage = "glossary.gif";

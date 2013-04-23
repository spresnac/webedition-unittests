<?php

/**
 * webEdition CMS
 *
 * $Rev: 4592 $
 * $Author: lukasimhof $
 * $Date: 2012-06-14 15:42:57 +0200 (Thu, 14 Jun 2012) $
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

/**
 * Code Snipptes are used in templates inside webEdition
 *
 * @see Parser.php
 * @see dtd:http://docs.oasis-open.org/dita/v1.0.1/dtd/topic.dtd
 *
 */
class weCodeWizardSnippet{

	/**
	 * Name of the Snippet
	 *
	 * @var string
	 */
	var $Name = "";

	/**
	 * Description of the snippet
	 *
	 * @var string
	 */
	var $Description = "";

	/**
	 * Author of the snippet
	 *
	 * @var string
	 */
	var $Author = "";

	/**
	 * Snippet code
	 *
	 * @var string
	 */
	var $Code = "";

	/**
	 * initialize the snippet from an xml file
	 *
	 * @param string $file
	 */
	function initByXmlFile($file){

		$Snippet = new weCodeWizardSnippet();
		$Parser = new we_xml_parser($file);

		// set the title
		if($Parser->execMethod_count("/topic[1]/title[1]", "codeblock") > 0){
			$Snippet->Name = $Parser->getData("/topic[1]/title[1]/codeblock[1]");
			if(isset($GLOBALS['we_doc']->elements["Charset"]['dat']) && $GLOBALS['we_doc']->elements["Charset"]['dat'] != "UTF-8"){
				$Snippet->Name = $Snippet->Name;
			}
			ob_start();
			eval('?>' . $Snippet->Name);
			$Snippet->Name = ob_get_contents();
			ob_end_clean();
		}

		// set the short description
		if($Parser->execMethod_count("/topic[1]/shortdesc[1]", "codeblock") > 0){
			$Snippet->Description = $Parser->getData("/topic[1]/shortdesc[1]/codeblock[1]");
			if(isset($GLOBALS['we_doc']->elements["Charset"]['dat']) && $GLOBALS['we_doc']->elements["Charset"]['dat'] != "UTF-8"){
				$Snippet->Description = $Snippet->Description;
			}
			ob_start();
			eval('?>' . $Snippet->Description);
			$Snippet->Description = ob_get_contents();
			ob_end_clean();
		}

		// set the author
		if($Parser->execMethod_count("/topic[1]/prolog[1]", "author") > 0){
			$Snippet->Author = $Parser->getData("/topic[1]/prolog[1]/author[1]");
			if(isset($GLOBALS['we_doc']->elements["Charset"]['dat']) && $GLOBALS['we_doc']->elements["Charset"]['dat'] != "UTF-8"){
				$Snippet->Author = $Snippet->Author;
			}
		}

		// set the code
		if($Parser->execMethod_count("/topic[1]/body[1]/p[1]", "codeblock") > 0){
			$Snippet->Code = $Parser->getData("/topic[1]/body[1]/p[1]/codeblock[1]");
			if(isset($GLOBALS['we_doc']->elements["Charset"]['dat']) && $GLOBALS['we_doc']->elements["Charset"]['dat'] != "UTF-8"){
				$Snippet->Code = $Snippet->Code;
			}
			ob_start();
			eval('?>' . $Snippet->Code);
			$Snippet->Code = ob_get_contents();
			ob_end_clean();
		}

		return $Snippet;
	}

	function changeCharset($string, $charset = ""){

		if($charset == ""){
			$charset = $GLOBALS['we_doc']->getElement('Charset');
			if($charset == ""){
				$charset = $GLOBALS['WE_BACKENDCHARSET'];
			}
		}

		if($charset != "UTF-8" && $charset != ""){

			if(function_exists("iconv")){
				$string = iconv("UTF-8", $charset, $string);
			} elseif($charset == "ISO-8859-1"){
				$string = utf8_decode($string);
			}
		}

		return $string;
	}

	/**
	 * get the snippet name
	 *
	 * @return string
	 */
	function getName($charset = ""){
		return weCodeWizardSnippet::changeCharset($this->Name, $charset);
	}

	/**
	 * get the snippet description
	 *
	 * @return string
	 */
	function getDescription($charset = ""){
		return weCodeWizardSnippet::changeCharset($this->Description, $charset);
	}

	/**
	 * get the snippet author
	 *
	 * @return string
	 */
	function getAuthor($charset = ""){
		return weCodeWizardSnippet::changeCharset($this->Author, $charset);
	}

	/**
	 * get the snippet code
	 *
	 * @return string
	 */
	function getCode($charset = ""){
		return weCodeWizardSnippet::changeCharset($this->Code, $charset);
	}

}

/**
 * Code Sample
 *
 * $Snippet = weCodeWizardSnippet::initByXmlFile('Contact.xml');
 *
 * echo $Snippet->getName();
 *
 */


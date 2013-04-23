<?php

/**
 * webEdition CMS
 *
 * $Rev: 3750 $
 * $Author: mokraemer $
 * $Date: 2012-01-07 02:14:44 +0100 (Sat, 07 Jan 2012) $
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
 * Definition of WebEdition Newsletter Block
 *
 */
class weNewsletterBlock extends weNewsletterBase{
// Document based Newsletter Block
	const DOCUMENT=0;
// Document field based Newsletter Block
	const DOCUMENT_FIELD=1;
// Object based Newsletter Block
	const OBJECT=2;
// Object field based Newsletter Block
	const OBJECT_FIELD=3;
// File based Newsletter Block
	const FILE=4;
//  Text based Newsletter Block
	const TEXT=5;
//  Newsletter attachment
	const ATTACHMENT=6;
//  URL based newsletter
	const URL=7;

	//properties
	var $ID;
	var $NewsletterID;
	var $Groups;
	var $Type;
	var $LinkID;
	var $Field;
	var $Source;
	var $Html;

	/*	 * *****************************************************
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 * ****************************************************** */

	function __construct($newsletterID = 0){

		parent::__construct();
		$this->table = NEWSLETTER_BLOCK_TABLE;

		//$this->persistents[]="ID";
		$this->persistents[] = "NewsletterID";
		$this->persistents[] = "Groups";
		$this->persistents[] = "Type";
		$this->persistents[] = "LinkID";
		$this->persistents[] = "Field";
		$this->persistents[] = "Source";
		$this->persistents[] = "Html";
		$this->persistents[] = "Pack";

		$this->ID = 0;
		$this->NewsletterID = 0;
		$this->Groups = "";
		$this->Type = self::DOCUMENT;
		$this->LinkID = 0;
		$this->Field = "";
		$this->Source = "";
		$this->Html = "";
		$this->Pack = "";


		if($newsletterID){
			$this->ID = $newsletterID;
			$this->load($newsletterID);
		}
	}

	/*	 * **************************************
	 * saves newsletter blocks in database
	 *
	 * *************************************** */

	function save(){

		$this->Groups = makeCSVFromArray(makeArrayFromCSV($this->Groups), true);
		parent::save();
		return true;
	}

	/*	 * **************************************
	 * deletes newsletter blocks from database
	 *
	 * *************************************** */

	function delete(){

		parent::delete();
		return true;
	}

	//---------------------------------- STATIC FUNCTIONS -------------------------------

	/*	 * ****************************************************
	 * return all newsletter blocks for given newsletter id
	 *
	 * ***************************************************** */
	function __getAllBlocks($newsletterID){

		$db = new DB_WE();

		$db->query("SELECT ID FROM " . NEWSLETTER_BLOCK_TABLE . " WHERE NewsletterID=" . intval($newsletterID) . " ORDER BY ID");
		$ret = array();
		while($db->next_record()) {
			$ret[] = new weNewsletterBlock($db->f("ID"));
		}
		return $ret;
	}

}
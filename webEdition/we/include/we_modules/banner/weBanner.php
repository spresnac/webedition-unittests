<?php

/**
 * webEdition CMS
 *
 * $Rev: 5575 $
 * $Author: mokraemer $
 * $Date: 2013-01-15 22:36:59 +0100 (Tue, 15 Jan 2013) $
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
 * General Definition of WebEdition Banner
 *
 */
class weBanner extends weBannerBase{

	const PAGE_PROPERTY = 0;
	const PAGE_PLACEMENT = 1;
	const PAGE_STATISTICS = 2;

//properties
	var $ID = 0;
	var $Text;
	var $ParentID = 0;
	var $bannerID = 0;
	var $bannerUrl = '';
	var $bannerIntID = 0;
	var $maxShow = 10000;
	var $maxClicks = 1000;
	var $IsDefault = 0;
	var $clickPrice = 0;
	var $showPrice = 0;
	var $IsFolder = 0;
	var $Icon = "banner.gif";
	var $Path = "";
	var $IntHref = 0;
	var $FileIDs;
	var $FolderIDs;
	var $CategoryIDs;
	var $DoctypeIDs;
	var $IsActive = 1;
	var $StartDate = 0;
	var $EndDate = 0;
	var $StartOk = 0;
	var $EndOk = 0;
	var $clicks = 0;
	var $views = 0;
	var $Customers = '';
	var $TagName = '';
	var $weight = 4;

	/**
	 * steps for WorkFlow Definition
	 * this is array of weBannerStep objects
	 */
	var $steps = array();
	// default document object
	var $documentDef;
	// documents array; format document[documentID]=document_name
	// don't create array of objects 'cos whant to save some memory
	var $documents = array();

	/**
	 * Default Constructor
	 * Can load or create new Banner Definition depends of parameter
	 */
	public function __construct($bannerID = 0, $IsFolder = 0){
		parent::__construct();
		$this->table = BANNER_TABLE;

		$this->persistents = array("ID",
			"Text",
			"ParentID",
			"bannerID",
			"bannerUrl",
			"bannerIntID",
			"maxShow",
			"maxClicks",
			"IsDefault",
			"clickPrice",
			"showPrice",
			"IsFolder",
			"Icon",
			"Path",
			"IntHref",
			"FileIDs",
			"FolderIDs",
			"CategoryIDs",
			"DoctypeIDs",
			"StartDate",
			"EndDate",
			"StartOk",
			"EndOk",
			"IsActive",
			"clicks",
			"views",
			"Customers",
			"TagName",
			"weight"
		);

		$this->IsFolder = $IsFolder;
		$this->Text = g_l('modules_banner', ($this->IsFolder ? '[newbannergroup]' : '[newbanner]'));
		$this->Path = '/' . g_l('modules_banner', ($this->IsFolder ? '[newbannergroup]' : '[newbanner]'));

		if($this->IsFolder){
			$this->Icon = "banner_folder.gif";
		}

		if($bannerID){
			$this->ID = $bannerID;
			$this->load($bannerID);
		}
	}

	/**
	 * Load banner definition from database
	 */
	public function load($id = 0){
		if($id){
			$this->ID = $id;
		}
		if(!$this->ID){
			return false;
		}
		parent::load();
		$ppath = id_to_path($this->ParentID, BANNER_TABLE);
		$this->Path = ($ppath == '/') ? $ppath . $this->Text : $ppath . '/' . $this->Text;
		return true;
	}

	/**
	 * get all banners from database (STATIC)
	 */
	function getAllBanners(){
		//FIXME: check for e.g. group by, having, ..
		$this->db->query('SELECT ID,abs(text) AS Nr, (text REGEXP "^[0-9]") AS isNr FROM ' . $this->table . ' ORDER BY isNr DESC,Nr,Text');

		$out = array();
		while($this->db->next_record()) {
			$out[] = new weBanner($this->db->f("ID"));
		}
		return $out;
	}

	/**
	 * save complete banner definition in database
	 */
	public function save(){
		$ppath = id_to_path($this->ParentID, BANNER_TABLE);
		$this->Path = ($ppath == "/") ? $ppath . $this->Text : $ppath . '/' . $this->Text;
		parent::save();
	}

	/**
	 * delete banner from database
	 */
	public function delete(){
		if(!$this->ID){
			return false;
		}

		parent::delete();
		$this->db->query('DELETE FROM ' . BANNER_VIEWS_TABLE . ' WHERE ID=' . intval($this->ID));
		$this->db->query('DELETE FROM ' . BANNER_CLICKS_TABLE . ' WHERE ID=' . intval($this->ID));
		if($this->IsFolder){
			$path = (substr($this->Path, -1) == "/") ? $this->Path : $this->Path . "/";
			$this->db->query('SELECT ID FROM ' . BANNER_TABLE . ' WHERE Path LIKE "' . $this->db->escape($path) . '%"');
			$ids = array();
			while($this->db->next_record()) {
				$ids[] = $this->db->f("ID");
			}
			foreach($ids as $id){
				if($id){
					$this->db->query('DELETE FROM ' . BANNER_VIEWS_TABLE . ' WHERE ID=' . intval($id));
					$this->db->query('DELETE FROM ' . BANNER_CLICKS_TABLE . ' WHERE ID=' . intval($id));
					$this->db->query('DELETE FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($id));
				}
			}
		}

		return true;
	}

	static function getBannerData($did, $paths, $dt, $cats, $bannername, $db){
		$parents = array();

		we_readParents($did, $parents, FILE_TABLE);

		$where = 'IsActive=1 AND IsFolder=0 AND ( FileIDs LIKE "%,' . intval($did) . ',%" OR FileIDs="" )';
		$foo = '';
		foreach($parents as $p){
			$foo .= ' FolderIDs LIKE "%,' . intval($p) . ',%" OR ';
		}
		$where .= ' AND (' . $foo . ' FolderIDs="" ) ';

		$dtArr = makeArrayFromCSV($dt);

		$foo = '';
		foreach($dtArr as $d){
			$foo .= ' DoctypeIDs LIKE "%,' . intval($d) . ',%" OR ';
		}
		$where .= ' AND (' . $foo . ' DoctypeIDs="" ) ';

		$catArr = makeArrayFromCSV($cats);

		$foo = '';
		foreach($catArr as $c){
			$foo .= ' CategoryIDs LIKE "%,' . intval($c) . ',%" OR ';
		}
		$where .= ' AND (' . $foo . ' CategoryIDs="" ) ';

		if($paths){
			$foo=array();
			$pathsArray = makeArrayFromCsv($paths);
			foreach($pathsArray as $p){
				$foo []= 'Path LIKE "' . $db->escape($p) . '/%" OR Path = "' . $db->escape($p) . '"';
			}
			$where .= ' AND ('. implode(' OR ',$foo) .') ';
		}

		$where .= ' AND ( (StartOk=0 OR StartDate <= UNIX_TIMESTAMP() ) AND (EndOk=0 OR EndDate > UNIX_TIMESTAMP()) ) AND (maxShow=0 OR views<maxShow) AND (maxClicks=0 OR clicks<=maxClicks) ';

		$maxweight = f('SELECT MAX(weight) as maxweight FROM ' . BANNER_TABLE, 'maxweight', $db);

		srand((double) microtime() * 1000000);
		$weight = rand(0, intval($maxweight));
		$anz = 0;

		while($anz == 0 && $weight <= $maxweight) {
			$db->query('SELECT ID, bannerID FROM ' . BANNER_TABLE . " WHERE $where AND weight <= $weight AND (TagName='' OR TagName='" . $db->escape($bannername) . "')");
			$anz = $db->num_rows();
			if($anz == 0){
				++$weight;
			}
		}

		if($anz > 0){
			if($anz > 1){
				srand((double) microtime() * 1000000);
				$offset = rand(0, $anz - 1);
				$db->seek($offset);
			}
			if($db->next_record()){
				return $db->getRecord();
			}
		}

		return array("ID" => 0, "bannerID" => 0);
	}

	private static function getImageInfos($fileID){
		$imgAttr = array();
		$db = new DB_WE();
		$db->query('SELECT l.Name AS Name, c.Dat AS Dat FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' AS c ON l.CID=c.ID WHERE l.Type="attrib" AND l.DID=' . intval($fileID));
		while($db->next_record(MYSQL_ASSOC)) {
			$imgAttr[$db->f('Name')] = $db->f("Dat");
		}
		$db->free();
		return ($imgAttr);
	}

	public static function getBannerCode($did, $paths, $target, $width, $height, $dt, $cats, $bannername, $link = true, $referer = "", $bannerclick = "/webEdition/bannerclick.php", $getbanner = "/webEdition/getBanner.php", $type = "", $page = "", $nocount = false, $xml = false){
		$db = new DB_WE();
		$bannerData = self::getBannerData($did, $paths, $dt, $cats, $bannername, $db);
		$uniq = md5(uniqid(__FUNCTION__, true));
		$showlink = true;
		$attsImage['border'] = 0;
		$attsImage['alt'] = '';

		if($bannerData["ID"]){
			$id = $bannerData["ID"];
			if($bannerData["bannerID"]){
				$bannersrc = getServerUrl() . id_to_path($bannerData["bannerID"]);
				$attsImage = array_merge($attsImage, self::getImageInfos($bannerData["bannerID"]));
				if(isset($attsImage['longdescid'])){
					unset($attsImage['longdescid']);
				}
			} else{
				$bannersrc = $getbanner . "?" . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . "u=$uniq&amp;bannername=" . rawurlencode($bannername) . "&amp;id=" . $bannerData["ID"] . "&amp;bid=" . $bannerData["bannerID"] . "&amp;did=" . $did . "&amp;page=" . rawurlencode($page);
			}
			$bannerlink = $bannerclick . "?" . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . "u=$uniq&amp;bannername=" . rawurlencode($bannername) . "&amp;id=" . $bannerData["ID"] . "&amp;did=" . $did . "&amp;page=" . rawurlencode($page);
		} else{
			$id = f('SELECT pref_value FROM ' . BANNER_PREFS_TABLE . ' WHERE pref_name="DefaultBannerID"', 'pref_value', $db);

			$bannerID = f('SELECT bannerID FROM ' . BANNER_TABLE . ' WHERE ID=' . intval($id), "bannerID", $db);
			if($bannerID){
				$bannersrc = getServerUrl() . id_to_path($bannerID);
				$attsImage = array_merge($attsImage, self::getImageInfos($bannerID));
				if(isset($attsImage['longdescid']))
					unset($attsImage['longdescid']);
			}else{
				$bannersrc = $getbanner . "?" . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . "u=$uniq&amp;bannername=" . rawurlencode($bannername) . "&amp;id=" . $id . "&amp;bid=" . $bannerID . "&amp;did=" . $did;
				$showlink = false;
			}
			$bannerlink = $bannerclick . "?" . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . "u=$uniq&amp;bannername=" . rawurlencode($bannername) . "&amp;id=" . $id . "&amp;did=" . $did . "&amp;page=" . rawurlencode($page);
		}
		if(!$nocount){
			$db->query('INSERT INTO ' . BANNER_VIEWS_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => intval($id),
					'Timestamp' => 'UNIX_TIMESTAMP()',
					'IP' => $_SERVER['REMOTE_ADDR'],
					'Referer' => $referer ? $referer : (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : ""),
					'DID' => intval($did),
					'Page' => $page
				)));
			$db->query('UPDATE ' . BANNER_TABLE . ' SET views=views+1 WHERE ID=' . intval($id));
		}

		$attsImage['xml'] = $xml ? "true" : "false";

		$attsImage['src'] = $bannersrc;
		if($width){
			$attsImage['width'] = $width;
		}
		if($height){
			$attsImage['height'] = $height;
		}
		if(isset($attsImage['type'])){
			unset($attsImage['type']);
		}
		if(isset($attsImage['filesize'])){
			unset($attsImage['filesize']);
		}
		$img = getHtmlTag('img', $attsImage);

		if($showlink){

			$linkAtts['href'] = $bannerlink;
			if($target){
				$linkAtts['target'] = $target;
			} else if($type == 'iframe'){
				$linkAtts['target'] = '_parent';
			}

			return getHtmlTag('a', $linkAtts, $img);
		} else{
			return $img;
		}
	}

	public static function getBannerURL($bid){
		$h = getHash("SELECT IntHref,bannerIntID,bannerURL FROM " . BANNER_TABLE . " WHERE ID=" . intval($bid), $GLOBALS['DB_WE']);
		return $h["IntHref"] ? getServerUrl() . id_to_path($h["bannerIntID"], FILE_TABLE) : $h["bannerURL"];
	}

	public static function customerOwnsBanner($customerID, $bannerID){
		$res = getHash("SELECT Customers,ParentID FROM " . BANNER_TABLE . " WHERE ID=" . intval($bannerID), new DB_WE());
		if(strstr($res["Customers"], "," . $customerID . ",") != false){
			return true;
		} elseif($res["ParentID"] != 0){
			return self::customerOwnsBanner($customerID, $res["ParentID"]);
		}
		return false;
	}

}

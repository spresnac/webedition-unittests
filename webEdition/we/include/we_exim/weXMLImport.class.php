<?php

/**
 * webEdition CMS
 *
 * $Rev: 5934 $
 * $Author: mokraemer $
 * $Date: 2013-03-09 20:06:43 +0100 (Sat, 09 Mar 2013) $
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
class weXMLImport extends weXMLExIm{

	var $nodehierarchy = array();

	function __construct(){
		$this->RefTable = new RefTable();
		$this->destination[strtolower(FILE_TABLE)] = 0;
		$this->destination[strtolower(TEMPLATES_TABLE)] = 0;
		$this->destination[strtolower(DOC_TYPES_TABLE)] = 0;
		if(defined("OBJECT_TABLE")){
			$this->destination[strtolower(OBJECT_TABLE)] = 0;
		}
		if(defined("OBJECT_FILES_TABLE")){
			$this->destination[strtolower(OBJECT_FILES_TABLE)] = 0;
		}
	}

	function import($chunk_file){
		$db = new DB_WE();
		@set_time_limit(0);

		$objects = array();

		$data = weFile::load($chunk_file);
		$this->xmlBrowser = new weXMLParser();
//			$this->xmlBrowser->parse($data,$_SESSION['weXMLimportCharset']); Original , �nderung f�r Zeichensatz Wandlung
		$this->xmlBrowser->parse($data, $this->options['xml_encoding']);
		unset($data);
		$this->xmlBrowser->normalize();

		if($this->xmlBrowser->getChildren(0, $node_set)){

			foreach($node_set as $node){
				$this->xmlBrowser->seek($node);

				if($this->handleTag($this->xmlBrowser->getNodeName($node))){
					$objects[] = $this->importNodeSet($node);
				}
			}
		}

		$save = false;
		foreach($objects as $object){

			$extra = array();
			if(!empty($object)){

				$save = true;
				$extra = array(
					"OldID" => isset($object->ID) ? $object->ID : 0,
					"OldParentID" => isset($object->ParentID) ? $object->ParentID : 0,
					"OldPath" => isset($object->Path) ? $object->Path : "",
					"OldTemplatePath" => isset($object->TemplatePath) ? $object->TemplatePath : "",
					"Eximed" => 1,
				);

				if(isset($object->elements)){
					$extra["elements"] = $object->elements;
				}

				$object->ID = 0;
				$object->Table = $this->getTable($object->ClassName);

				switch($object->ClassName){
					case "weModelBase":
						$extra["ContentType"] = "category";
						break;
					case "we_docTypes":
						$extra["ContentType"] = "doctype";
						$dtid = f("SELECT ID FROM " . DOC_TYPES_TABLE . " WHERE DocType='" . $db->escape($object->DocType) . "'", "ID", $db);
						if($dtid){
							if($this->options["handle_collision"] == "replace"){
								$object->ID = $dtid;
							} else if($this->options["handle_collision"] == "rename"){
								$this->getNewName($object, $dtid, "DocType");
							} else{
								$save = false;
								continue;
							}
						}
						break;
					case "weNavigationRule":
						$nid = f("SELECT ID FROM " . NAVIGATION_RULE_TABLE . " WHERE NavigationName='" . $db->escape($object->NavigationName) . "'", "ID", $db);
						if($nid){
							if($this->options["handle_collision"] == "replace"){
								$object->ID = $nid;
							} else if($this->options["handle_collision"] == "rename"){
								$this->getNewName($object, $nid, "NavigationName");
							} else{
								$save = false;
								continue;
							}
						}
						break;
					case "we_thumbnailEx":
						$nid = f("SELECT ID FROM " . THUMBNAILS_TABLE . " WHERE Name='" . $db->escape($object->Name) . "'", "ID", $db);
						if($nid){
							if($this->options["handle_collision"] == "replace"){
								$object->ID = $nid;
							} else if($this->options["handle_collision"] == "rename"){
								$this->getNewName($object, $nid, "Name");
							} else{
								$save = false;
								continue;
							}
						}
						break;
				}

				if(isset($object->Path)){
					if(isset($object->Table) && !empty($object->Table)){
						$prefix = '/';
						switch($object->Table){
							case FILE_TABLE:
								if($this->options["document_path"]){
									$prefix = id_to_path($this->options["document_path"], FILE_TABLE, $db);
								}
								$object->Path = $prefix . ($this->options["restore_doc_path"] ?
										$object->Path :
										"/" . $object->Text);
								break;
							case TEMPLATES_TABLE:
								if($this->options["template_path"]){
									$prefix = id_to_path($this->options["template_path"], TEMPLATES_TABLE, $db);
								}
								$object->Path = $prefix . ($this->options["restore_tpl_path"] ?
										$object->Path :
										"/" . $object->Text);
								break;
							case NAVIGATION_TABLE:
								if($this->options["navigation_path"]){
									$prefix = id_to_path($this->options["navigation_path"], NAVIGATION_TABLE, $db);
								}
								$object->Path = $prefix . $object->Path;
								break;
						}


						$object->Path = clearPath($object->Path);

						//fix Path if there is a conflict
						$id = path_to_id($object->Path, $object->Table);

						if($id){
							if($this->options["handle_collision"] == "replace" ||
								($object->ClassName == "we_folder" && $this->RefTable->exists(array("OldID" => $object->ID, "Table" => $object->Table)))
							){
								$object->ID = $id;
								if(isset($object->isnew)){
									$object->isnew = 0;
								}
							} else if($this->options["handle_collision"] == "rename"){
								$this->getNewName($object, $id, "Path");
							} else{
								$save = false;
								continue;
							}
						}
					}
					//fix Path ends
					// set OldPath
					if(isset($object->OldPath))
						$object->OldPath = $object->Path;

					// assign ParentID and ParentPath based on Path
					if(isset($object->Table)){
						$pathids = array();
						$_old_pid = $object->ParentID;
						$owner = ($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']) ? $this->options['owners_overwrite_id'] : 0;
						$object->ParentID = makePath(dirname($object->Path), $object->Table, $pathids, $owner);
						if(isset($object->ParentPath))
							$object->ParentPath = id_to_path($object->ParentID, $object->Table);

						// insert new created folders in ref table
						foreach($pathids as $pid){

							$h = getHash("SELECT ParentID,Path FROM " . $db->escape($object->Table) . " WHERE ID=" . intval($pid), $db);
							if(!$this->RefTable->exists(array("ID" => $pid, "ContentType" => "folder"))){
								$this->RefTable->add2(
									array_merge(array(
									"ID" => $pid,
									"ParentID" => $h["ParentID"],
									"Path" => $h["Path"],
									"Table" => $object->Table,
									"ContentType" => "folder"
										), array(
									"OldID" => ($pid == $object->ParentID) ? $_old_pid : null,
									"OldParentID" => null,
									"OldPath" => null,
									"OldTemplatePath" => null,
									"Eximed" => 0,
										)
									)
								);
							}
						}
					}

					if($object->ClassName == 'weBinary'){
						if(is_file($_SERVER['DOCUMENT_ROOT'] . $object->Path)){
							if($this->options['handle_collision'] == 'replace'){
								$save = true;
							} else if($this->options['handle_collision'] == 'rename'){
								$_c = 1;
								do{
									$_path = $object->Path . '_' . $_c;
									$_c++;
								} while(is_file($_SERVER['DOCUMENT_ROOT'] . $_path));
								$object->Path = $_path;
								unset($_path);
								unset($_c);
							} else{
								$save = false;
							}
						}

						if($save && !$this->RefTable->exists(array('ID' => $object->ID, 'Path' => $object->Path, 'ContentType' => 'weBinary'))){
							$this->RefTable->add2(
								array('ID' => $object->ID,
									'ParentID' => 0,
									'Path' => $object->Path,
									'Table' => $object->Table,
									'ContentType' => 'weBinary'
								)
							);
						}
					}
				}

				if(defined("OBJECT_TABLE") && $object->ClassName == 'we_objectFile'){
					$ref = $this->RefTable->getRef(
						array(
							'OldID' => $object->TableID,
							'ContentType' => "object"
						)
					);
					if($ref){
						// assign TableID and ParentID from reference
						$object->TableID = $ref->ID;
					} else{
						//assign TableID based on Path
						// evaluate root dir for object
						$match = array();
						preg_match('|(/+[a-zA-Z0-9_+-\.]*)|', $object->Path, $match);
						if(isset($match[0])){
							$object->TableID = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path=\'' . $db->escape($match[0]) . '\'', 'ID', $db);
						}
					}
				}

				// update owners data
				$this->refreshOwners($object);

				if($save){
					$this->saveObject($object);
				}
				$this->RefTable->add($object, $extra);
			}
		}
		return $save;
	}

	function getNewName(&$object, $id, $prop){
		$c = 0;
		$newid = $id;
		do{
			$c++;

			if($object->ClassName == "we_docTypes" || $object->ClassName == "weNavigationRule" || $object->ClassName == "we_thumbnailEx")
				$newname = $object->$prop;
			else
				$newname = basename($object->$prop);

			if($newid)
				$newname = $c . "_" . $newname;
			switch($object->ClassName){
				case "we_docTypes":
					$newid = f("SELECT ID FROM " . DOC_TYPES_TABLE . " WHERE DocType='" . escape_sql_query($newname) . "'", "ID", new DB_WE());
					break;
				case 'weNavigationRule':
					$newid = f("SELECT ID FROM " . NAVIGATION_RULE_TABLE . " WHERE NavigationName='" . escape_sql_query($newname) . "'", "ID", new DB_WE());
					break;
				case 'we_thumbnailEx':
					$newid = f("SELECT ID FROM " . THUMBNAILS_TABLE . " WHERE Name='" . escape_sql_query($newname) . "'", "ID", new DB_WE());
					break;
				default:
					$newid = path_to_id(clearPath(dirname($object->Path) . "/" . $newname), $object->Table);
			}
		} while($newid);
		$this->renameObject($object, $newname);
	}

	function renameObject(&$object, $new_name){
		if($object->ClassName == "we_docTypes"){
			$object->DocType = $new_name;
			return;
		}
		if($object->ClassName == "weNavigationRule"){
			$object->NavigationName = $new_name;
			return;
		}
		if($object->ClassName == "we_thumbnailEx"){
			$object->Name = $new_name;
			return;
		}
		if(isset($object->Path)){
			$_path = dirname($object->Path);
			$_ref = $this->RefTable->getRef(
				array(
					'OldID' => $object->ParentID,
					'ContentType' => 'weNavigation'
				)
			);
			if($_ref){
				$object->ParentID = $_ref->ID;
				$object->Path = $_ref->Path . '/' . $new_name;
			} else{
				$object->Path = clearPath(dirname($object->Path) . '/' . $new_name);
			}
		}
		if(isset($object->Text))
			$object->Text = $new_name;
		if(isset($object->Filename))
			$object->Filename = str_replace($object->Extension, "", $new_name);
	}

	function importNodeSet($node_id){
		$i = 0;
		$object = '';
		$node_props = array();
		$node_data = array();
		$node_coding = array();

		if($this->xmlBrowser->getChildren($node_id, $node_props)){

			foreach($node_props as $node){
				$this->xmlBrowser->seek($node);
				$nodname = $this->xmlBrowser->getNodeName();
				$noddata = $this->xmlBrowser->getNodeData();
				$attributes = $this->xmlBrowser->getNodeAttributes();


				if($nodname == "we:info"){
					$this->importNodeSet($node);
				} else if($nodname == "we:map"){
					$this->RefTable->Users[$attributes['user']] = $attributes;
				} else if($nodname == "we:content"){
					$i++;
					$this->xmlBrowser->addMark('we:content');
					$content = $this->importNodeSet($node);
					$this->xmlBrowser->gotoMark('we:content');
					$object->elements = array_merge($object->elements, $content->getElement());
				} else{
					if($nodname == "ClassName"){
						array_push($this->nodehierarchy, $noddata);
						switch($noddata){
							case "we_object":
								if(defined("OBJECT_TABLE")){
									$object = new we_objectEx();
								}
								break;
							case "we_objectFile":
								if(defined("OBJECT_FILES_TABLE")){
									$object = new we_objectFile();
								}
								break;
							case "we_class_folder": //Bug 3857 sonderbehandlung hinzugef�gt, da es sonst hier beim letzten else zum Absturz kommt, es wird nichts geladen, da eigentlich alles geladen ist
								break;
							case "weNavigation":
							case 'weNavigationRule':
							case 'we_thumbnailEx':
							case "weBinary":
							default:
								$object = new $noddata();
								break;
						}
					}
					$node_data[$nodname] = $noddata;
					$node_coding[$nodname] = (isset($attributes[weContentProvider::CODING_ATTRIBUTE]) ? $attributes[weContentProvider::CODING_ATTRIBUTE] : weContentProvider::CODING_NONE);
				}
			}
		}

		if(!empty($object)){
			$reflect = new ReflectionClass($object);
			$props = $reflect->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);
			foreach($props as $prop){
				unset($node_data[$prop->getName()]);
			}

			weContentProvider::populateInstance($object, $node_data);

			foreach($node_data as $k => $v){
				$v = weContentProvider::getDecodedData($node_coding[$k], $v);

				if($v != $object->$k)
					$object->$k = $v;
			}
		}

		return $object;
	}

	function isSerialized($str){
		return ($str == serialize(false) || @unserialize($str) !== false);
	}

	function changeEncoding($value){
		if($this->options['change_encoding']){
			if($this->options['target_encoding'] != '' && $this->options['xml_encoding'] != '' && $this->options['target_encoding'] != $this->options['xml_encoding']){

				if($value == $this->options['xml_encoding']){
					return $this->options['target_encoding'];
				} else{
					if($this->isSerialized($value)){
						$usv = unserialize($value);
						if(is_array($usv)){
							foreach($usv as &$av){
								if($this->options['xml_encoding'] == 'ISO-8859-1'){
									$av = utf8_encode($av);
								} else{
									$av = utf8_decode($av);
								}
							}
							$sv = serialize($usv);
							return $sv;
						} else{
							return $value;
						}
					} else{
						if($this->options['xml_encoding'] == 'ISO-8859-1'){
							return utf8_encode($value);
						} else{
							return utf8_decode($value);
						}
					}
				}
			} else{
				return $value;
			}
		} else{
			return $value;
		}
	}

	function refreshOwners(&$object){
		if(isset($object->CreatorID) && ($this->options['handle_owners'] || $this->options['owners_overwrite'])){
			$userid = $object->CreatorID;
			if($this->options['handle_owners']){
				$userid = $this->RefTable->getNewOwnerID($userid);
				if($userid == 0 && $this->options['owners_overwrite'] && $this->options['owners_overwrite_id'])
					$userid = $this->options['owners_overwrite_id'];
			} else if($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
				$userid = $this->options['owners_overwrite_id'];
			} else{
				$userid = 0;
			}
			$object->CreatorID = $userid;
			if(isset($object->ModifierID))
				$object->ModifierID = $userid;
		}else{
			if(isset($object->CreatorID))
				$object->CreatorID = 0;
			if(isset($object->ModifierID))
				$object->ModifierID = 0;
		}

		if(isset($object->Owners) && ($this->options['handle_owners'] || $this->options['owners_overwrite'])){
			$owners = makeArrayFromCSV($object->Owners);
			$newowners = array();
			foreach($owners as $owner){
				if($this->options['handle_owners']){
					$own = $this->RefTable->getNewOwnerID($owner);
					if($own == 0 && $this->options['owners_overwrite'] && $this->options['owners_overwrite_id'])
						$own = $this->options['owners_overwrite_id'];
				} else if($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
					$own = $this->options['owners_overwrite_id'];
				}
				if(isset($own) && $own && !in_array($own, $newowners)){
					if(!$object->CreatorID)
						$object->CreatorID = $own;
					if(!$object->ModifierID)
						$object->ModifierID = $own;
					$newowners[] = $own;
				}
			}
			$object->Owners = makeCSVFromArray($newowners);
			if(isset($object->OwnersReadOnly)){
				$readonly = unserialize($object->OwnersReadOnly);
				$readonly_new = array();
				if(is_array($readonly)){
					foreach($readonly as $key => $value){
						if($this->options['handle_owners']){
							$newkey = $this->RefTable->getNewOwnerID($key);
							if($newkey == 0 && $this->options['owners_overwrite'] && $this->options['owners_overwrite_id'])
								$newkey = $this->options['owners_overwrite_id'];
						} else if($this->options['owners_overwrite'] && $this->options['owners_overwrite_id']){
							$newkey = $this->options['owners_overwrite_id'];
						}
						if($newkey)
							$readonly_new[$newkey] = $value;
					}
					$object->OwnersReadOnly = serialize($readonly_new);
				}
			}
		} else{
			if(isset($object->Owners))
				$object->Owners = '';
			if(isset($object->RestrictOwners))
				$object->RestrictOwners = 0;
			if(isset($object->OwnersReadOnly))
				$object->OwnersReadOnly = serialize(array());
		}
	}

	function splitFile($filename, $tmppath, $count){
		global $_language;

		if($filename == "")
			return -1;

		$path = $tmppath;
		$marker = weBackup::backupMarker;
		$marker2 = "<!--webackup -->"; //Bug 5089
		$pattern = basename($filename) . "_%s";

		$compress = (weFile::isCompressed($filename) ? "gzip" : "none");
		$head = weFile::loadPart($filename, 0, 256, $compress == 'gzip');

		$encoding = we_xml_parser::getEncoding('', $head);
		$_SESSION['weXMLimportCharset'] = $encoding;
		$header = weXMLExIm::getHeader($encoding);
		$footer = weXMLExIm::getFooter();

		$buff = "";
		$filename_tmp = "";
		$fh = ($compress != "none" ? @gzopen($filename, "rb") : @fopen($filename, "rb"));

		$num = -1;
		$open_new = true;
		$fsize = 0;

		$elnum = 0;

		$marker_size = strlen($marker);
		$marker2_size = strlen($marker2); //Backup 5089

		if($fh){
			while(!@feof($fh)) {
				@set_time_limit(240);
				$line = "";
				$findline = false;

				while($findline == false && !@feof($fh)) {
					$line .= ($compress != "none" ? @gzgets($fh, 4096) : @fgets($fh, 4096));
					if(substr($line, -1) == "\n"){
						$findline = true;
					}
				}

				if($open_new && !empty($line) && trim($line) != "</webEdition>"){
					$num++;
					$filename_tmp = sprintf($path . $pattern, $num);
					$fh_temp = fopen($filename_tmp, "wb");
					fwrite($fh_temp, $header);
					if($num == 0)
						$header = "";
					$open_new = false;
				}

				if(isset($fh_temp) && $fh_temp){
					if((substr($line, 0, 2) != "<?") && (substr($line, 0, 11) != "<webEdition") && (substr($line, 0, 12) != "</webEdition")){

						$buff.=$line;
						$write = false;
						if($marker_size){
							$write = ((substr($buff, (0 - ($marker_size + 1))) == $marker . "\n") || (substr($buff, (0 - ($marker_size + 2))) == $marker . "\r\n") || (substr($buff, (0 - ($marker2_size + 1))) == $marker2 . "\n") || (substr($buff, (0 - ($marker2_size + 2))) == $marker2 . "\r\n" ));
						} else{
							$write = true;
						}

						if($write){
							$fsize+=strlen($buff);
							fwrite($fh_temp, $buff);
							if($marker_size){
								$elnum++;
								if($elnum >= $count){
									$elnum = 0;
									$open_new = true;
									fwrite($fh_temp, $footer);
									@fclose($fh_temp);
								}
								$fsize = 0;
							}
							$buff = "";
						}
					} else{
						if(((substr($line, 0, 2) == "<?") || (substr($line, 0, 11) == "<webEdition")) && $num == 0){
							$header.=$line;
						}
					}
				} else{
					return -1;
				}
			}
		} else{
			return -1;
		}
		if($fh_temp && trim($line) != "</webEdition>"){
			if($buff){
				@fwrite($fh_temp, $buff);
			}
			@fwrite($fh_temp, $footer);
			@fclose($fh_temp);
		}
		if($compress != "none")
			@gzclose($fh);
		else
			@fclose($fh);

		return $num + 1;
	}

}

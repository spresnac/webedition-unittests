<?php

/**
 * webEdition CMS
 *
 * $Rev: 5716 $
 * $Author: mokraemer $
 * $Date: 2013-02-04 23:01:15 +0100 (Mon, 04 Feb 2013) $
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
abstract class weBackupExport{

	public static function export($_fh, &$offset, &$row_count, $lines = 1, $export_binarys = 0, $log = 0, $export_version_binarys = 0){

		if(!$_fh){
			return false;
		}
		static $_db = 0;
		$_db = $_db ? $_db : new DB_WE();

		if($offset == 0){

			$_table = weBackupUtil::getNextTable();

			// export table
			if($log){
				weBackupUtil::addLog(sprintf('Exporting table %s', $_table));
			}

			$_object = new weTableAdv($_table, true);

			$_attributes = array(
				'name' => weBackupUtil::getDefaultTableName($_table),
				'type' => 'create'
			);

			weContentProvider::object2xml($_object, $_fh, $_attributes);

			fwrite($_fh, weBackup::backupMarker . "\n");
		}


		$_table = weBackupUtil::getCurrentTable();

		//sppedup for some tables
		if(isset($_table)){
			switch($_table){
				case LANGLINK_TABLE:
				case RECIPIENTS_TABLE:
				case HISTORY_TABLE:
				case LINK_TABLE:
				case CONTENT_TABLE:
				case PREFS_TABLE:
				case (defined('BANNER_CLICKS_TABLE') ? BANNER_CLICKS_TABLE : 'BANNER_CLICKS_TABLE'):
					$lines = intval($lines) * 5;
					break;
			}
		}
		if(empty($_table)){
			return false;
		}

		// export table item

		$_keys = weTableItem::getTableKey($_table);
		$_keys_str = '`'.implode('`,`', $_keys).'`';

		$_db->query('SELECT ' . $_db->escape($_keys_str) . ' FROM  ' . $_db->escape($_table) . ' ORDER BY ' . $_keys_str . ' LIMIT ' . intval($offset) . ' ,' . intval($lines), true);
		$_def_table = weBackupUtil::getDefaultTableName($_table);
		$_attributes = array(
			'table' => $_def_table
		);

		while($_db->next_record()) {
			$_keyvalue = array();
			foreach($_keys as $_key){
				$_keyvalue[$_key] = $_db->f($_key);
			}
			$_ids = implode(',', $_keyvalue);

			if($log){
				weBackupUtil::addLog(sprintf('Exporting item %s:%s', $_table, $_ids));
			}

			$_object = new weTableItem($_table);
			$_object->load($_keyvalue);


			weContentProvider::object2xml($_object, $_fh, $_attributes);
			fwrite($_fh, weBackup::backupMarker . "\n");


			if($export_binarys || $export_version_binarys){
				switch($_def_table){
					case 'tblfile':
						if(($_object->ContentType == "image/*" || stripos($_object->ContentType, "application/") !== false)){
							if($log){
								weBackupUtil::addLog(sprintf('Exporting binary data for item %s:%s', $_table, $_object->ID));
							}

							$bin = weContentProvider::getInstance('weBinary', $_object->ID);

							weContentProvider::binary2file($bin, $_fh);
						}
						break;

					case 'tblversions':
						if($log){
							weBackupUtil::addLog(sprintf('Exporting version data for item %s:%s', $_table, $_object->ID));
						}

						$bin = weContentProvider::getInstance('weVersion', $_object->ID);

						weContentProvider::version2file($bin, $_fh);
						break;
				}
			}
			$offset++;
			$row_count++;
		}

		$_table_end = f('SELECT COUNT(1) AS Count FROM ' . $_db->escape($_table), 'Count', $_db);
		if($offset >= $_table_end){
			$offset = 0;
		}
		fflush($_fh);

		return true;
	}

}


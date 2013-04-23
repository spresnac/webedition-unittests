<?php

/**
 * webEdition CMS
 *
 * $Rev: 5601 $
 * $Author: mokraemer $
 * $Date: 2013-01-20 19:16:46 +0100 (Sun, 20 Jan 2013) $
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
abstract class weCustomerEI{

	public static function exportCustomers($options = array()){
		$code = '';
		if($options['format'] == 'gxml'){
			$code = self::exportXML($options);
		}
		if($options['format'] == 'csv'){
			$code = self::exportCSV($options);
		}
		// write to file
		if($code != ''){
			self::save2File($options['filename'], $code);
		}
	}

	public static function getDataset($type, $filename, $arrgs = array()){
		switch($type){
			case 'gxml':
				return self::getXMLDataset($filename, $arrgs['dataset']);
			case 'csv':
				return self::getCSVDataset($filename, $arrgs['delimiter'], $arrgs['enclose'], $arrgs['lineend'], $arrgs['fieldnames'], $arrgs['charset']);
		}
	}

	public static function save2File($filename, $code = '', $flags = 'ab'){
		return weFile::save($filename, $code, $flags);
	}

	public static function getCustomersFieldset(){
		$customer = new weCustomer();
		return $customer->getFieldset();
	}

	public static function exportXML($options = array()){
		if(isset($options['customers']) && is_array($options['customers'])){

			$customer = new weCustomer();
			$fields = $customer->getFieldsDbProperties();

			if(isset($options['firstexec']) && $options['firstexec'] == -999){
				$xml_out = '<?xml version="1.0" encoding="' . $GLOBALS['WE_BACKENDCHARSET'] . '" standalone="yes" ?>' . "\n";
				$xml_out.='<webEdition>' . "\n";
			} else{
				$xml_out = '';
			}

			foreach($options['customers'] as $cid){
				if($cid){
					$customer_xml = new we_baseCollection('customer');
					$customer = new weCustomer($cid);
					if($customer->ID){
						foreach($fields as $k => $v){
							if(!$customer->isProtected($k)){
								$value = $customer->{$k};
								if($value != '')
									$value = ($options['cdata'] ? '<![CDATA[' . $value . ']]>' : htmlentities($value));
								$customer_xml->addChild(new we_baseElement($k, true, null, $value));
							}
						}
					}
					$xml_out.=$customer_xml->getHtml() . weBackup::backupMarker . "\n";
				}
			}
			return $xml_out;
		}
		return '';
	}

	/* Function creates new xml element.
	 *
	 * element - [name] - element name
	 * 				 [attributes] - atributes array in form arry["attribute_name"]=attribute_value
	 * 				 [content] - if array childs otherwise some content
	 *
	 */

	function buildXMLElement($elements){
		$out = '';
		$content = '';
		foreach($elements as $element){
			if(is_array($element['content'])){
				$content = self::buildXMLElement($element['content']);
			}
			else
				$content = $element['content'];
			$element = new we_baseElement($element['name'], true, $element['attributes'], $content);
			$out.=$element->getHTML();
		}
		return $out;
	}

	function getXMLDataset($filename, $dataset){
		$xp = new we_xml_parser($_SERVER['DOCUMENT_ROOT'] . $filename);
		$nodeSet = $xp->evaluate($xp->root . '/' . $dataset . '[1]/child::*');
		$nodes = array();
		$attrs = array();

		foreach($nodeSet as $node){
			$nodeName = $xp->nodeName($node);
			$nodeattribs = array();
			if($xp->hasAttributes($node)){
				$attrs = $attrs + array('@n:' => g_l('modules_customer', '[none]'));
				$attributes = $xp->getAttributes($node);
				foreach($attributes as $name => $value){
					$nodeattribs[$name] = $value;
				}
			}
			$nodes[$nodeName] = $nodeattribs;
		}
		return $nodes;
	}

	function exportCSV($options = array()){
		if(isset($options['customers']) && is_array($options['customers'])){
			$customer_csv = array();
			$customer = new weCustomer();
			$fields = $customer->getFieldsDbProperties();
			foreach($options['customers'] as $cid){
				if($cid){
					$customer = new weCustomer($cid);
					if($customer->ID){
						$customer_csv[$cid] = array();
						foreach($fields as $k => $v){
							if(!$customer->isProtected($k)){
								$value = $customer->{$k};
								$customer_csv[$cid][] = $value;
							}
						}
					}
				}
			}

			$field_names = array();
			foreach($fields as $k => $v){
				if(!$customer->isProtected($k))
					$field_names[] = $k;
			}

			$csv_out = '';
			$enclose = trim($options['csv_enclose']);
			$lineend = trim($options['csv_lineend']);
			$delimiter = $enclose . ($options['csv_delimiter'] == '\t' ? "\t" : trim($options['csv_delimiter'])) . $enclose;

			if($options['csv_fieldnames']){
				$csv_out.=$enclose . implode($delimiter, $field_names) . $enclose . ($lineend == g_l('modules_customer', '[unix]') ? "\n" : ($lineend == g_l('modules_customer', '[mac]') ? "\r" : "\r\n"));
			}

			foreach($customer_csv as $ck => $cv){
				$csv_out.=$enclose . implode($delimiter, $cv) . $enclose . ($lineend == g_l('modules_customer', '[unix]') ? "\n" : ($lineend == g_l('modules_customer', '[mac]') ? "\r" : "\r\n"));
			}

			return $csv_out;
		}
		return '';
	}

	function getCSVDataset($filename, $delimiter, $enclose, $lineend, $fieldnames, $charset){
		if($charset == ''){
			$charset = DEFAULT_CHARSET;
		}
		if($delimiter == '\t'){
			$delimiter = "\t";
		}
		$csvFile = $_SERVER['DOCUMENT_ROOT'] . $filename;
		$nodes = array();

		if(file_exists($csvFile) && is_readable($csvFile)){
			$recs = array();

			if($lineend == 'mac'){
				self::massReplace("\r", "\n", $csvFile);
			}

			$cp = new we_import_CSV;
			$cp->setFile($csvFile);
			$cp->setDelim($delimiter);
			$cp->setFromCharset($charset);
			$cp->setEnclosure(($enclose == '') ? '"' : $enclose);
			$cp->parseCSV();
			$num = count($cp->FieldNames);
			$recs = array();
			for($c = 0; $c < $num; $c++){
				$recs[$c] = $cp->CSVFieldName($c);
			}
			for($i = 0; $i < count($recs); $i++){
				if($fieldnames)
					$nodes[$recs[$i]] = array();
				else
					$nodes[g_l('modules_customer', '[record_field]') . ' ' . ($i + 1)] = array();
			}
		}

		return $nodes;
	}

	function massReplace($string1, $string2, $file){
		$contents = weFile::load($file, 'r');
		$replacement = preg_replace('/' . preg_quote($string1, '/') . '/i', $string2, $contents);
		weFile::save($file, $contents, 'w');
	}

	function getUniqueId(){
		// md5 encrypted hash with the start value microtime(). The function
		// uniqid() prevents from simultanious access, within a microsecond.
		return md5(uniqid(__FILE__, true)); // #6590, changed from: uniqid(microtime())
	}

	function prepareImport($options){
		$ret = array(
			'tmp_dir' => '',
			'file_count' => '',
		);

		$type = $options['type'];
		$filename = $options['filename'];

		switch($type){
			case 'gxml':
				$dataset = $options['dataset'];
				$xml_from = $options['xml_from'];
				$xml_to = $options['xml_to'];

				$parse = new XML_SplitFile($_SERVER['DOCUMENT_ROOT'] . $filename);
				$parse->splitFile('*/' . $dataset, $xml_from, $xml_to);

				$ret['tmp_dir'] = str_replace(TEMP_PATH . '/', '', $parse->path);
				$ret['file_count'] = $parse->fileId;
				break;

			case 'csv':
				$csv_delimiter = $options['csv_delimiter'];
				$csv_enclose = $options['csv_enclose'];
				$csv_fields = $options['csv_fieldnames'];
				$csv_charset = $options['the_charset'];
				$exim = $options['exim'];

				$csvFile = $_SERVER['DOCUMENT_ROOT'] . $filename;

				if(file_exists($csvFile) && is_readable($csvFile)){

					// create temp dir
					$unique = weCustomerEI::getUniqueId();
					$path = TEMP_PATH . '/' . $unique;

					we_util_File::createLocalFolder($path);
					$path.='/';

					$fcount = 0;
					$rootnode = array(
						'name' => 'customer',
						'attributes' => null,
						'content' => array()
					);

					$csv = new we_customer_CSVImport();

					$csv->setDelim($csv_delimiter);
					$csv->setEnclosure($csv_enclose);
					$csv->setHeader($csv_fields);
					$csv->setFile($csvFile);
					$csv->setFromCharset($csv_charset);
					$csv->setToCharset('UTF-8');
					$csv->parseCSV();
					$data = $csv->CSVFetchRow();
					while($data != FALSE) {
						$value = array();
						foreach($data as $kdat => $vdat){
							$value[] = array(
								'name' => ($csv_fields ? $csv->FieldNames[$kdat] : (str_replace(' ', '', g_l('modules_customer', '[record_field]')) . ($kdat + 1))),
								'attributes' => null,
								'content' => '<![CDATA[' . $vdat . ']]>'
							);
						}
						$rootnode['content'] = $value;
						$code = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>' . "\n";
						$code.=weCustomerEI::buildXMLElement(array($rootnode));
						weCustomerEI::save2File($path . 'temp_' . $fcount . '.xml', $code, 'wb');
						$fcount++;

						$data = $csv->CSVFetchRow();
					}
					$ret['tmp_dir'] = $unique;
					$ret['file_count'] = $fcount;
				}
				break;
		}

		return $ret;
	}

	function importCustomers($options = array()){
		$ret = false;
		$xmlfile = isset($options['xmlfile']) ? $options['xmlfile'] : '';
		$field_mappings = isset($options['field_mappings']) ? $options['field_mappings'] : array();
		$attrib_mappings = isset($options['attrib_mappings']) ? $options['attrib_mappings'] : array();

		$same = isset($options['same']) ? $options['same'] : '';
		$logfile = isset($options['logfile']) ? $options['logfile'] : '';

		$db = new DB_WE();

		$customer = new weCustomer();
		$xp = new we_xml_parser($xmlfile);

		$fields = array_flip($field_mappings);
		$nodeSet = $xp->evaluate($xp->root . '/*');
		foreach($nodeSet as $node){
			$node_name = $xp->nodeName($node);
			$node_value = $xp->getData($node);
			if(isset($fields[$node_name]))
				$customer->{$fields[$node_name]} = iconv('UTF-8', DEFAULT_CHARSET, $node_value);
		}

		$existid = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $db->escape($customer->Username) . '" AND ID!=' . intval($customer->ID), 'ID', $db);
		if($existid){
			switch($same){
				case 'rename':
					$exists = true;
					$count = 0;
					$oldname = $customer->Username;
					while($exists) {
						$count++;
						$new_name = $customer->Username . $count;
						$exists = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $db->escape($new_name) . '" AND ID!=' . intval($customer->ID), 'ID', $db);
					}
					$customer->Username = $new_name;
					$customer->save();
					weCustomerEI::save2File($logfile, sprintf(g_l('modules_customer', '[rename_customer]'), $oldname, $customer->Username) . "\n");
					$ret = true;
					break;
				case 'overwrite':
					$customer->overwrite($existid);
					weCustomerEI::save2File($logfile, sprintf(g_l('modules_customer', '[overwrite_customer]'), $customer->Username) . "\n");
					$ret = true;
					break;
				default:
				case 'skip':
					weCustomerEI::save2File($logfile, sprintf(g_l('modules_customer', '[skip_customer]'), $customer->Username) . "\n");
					break;
			}
		} else{
			$ret = true;
			$customer->save();
		}

		unlink($xmlfile);
		return $ret;
	}

}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 5656 $
 * $Author: mokraemer $
 * $Date: 2013-01-29 00:36:45 +0100 (Di, 29. Jan 2013) $
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
 * @author		 Marc Krämer
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 * Filters from "tcpdf_filters.php", Nicola Asuni - Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com
 */
//define('DEBUG', 'fontout|page|tree|line'); //line|fontout|page|tree
//define('DEBUG', 'tree'); //line|fontout|page|tree
//define('DEBUG_MEM', 1);
//ini_set('memory_limit', '21M');

class we_helpers_pdf2text{

	const READPORTION = 512000;
	const NL = "\n";
	const SPACE = ' ';
	const FILTER_GZ = '/FlateDecode';
	const OBJ = 'obj';
	const ENDOBJ = 'endobj';
	const TRIM_STRING = ' ()';
	const TRIM_LIST = ' []';
	const TRIM_REF = ' R';
	const TRIM_NAME = ' /';
	const DEFLATE_ALL = false;

	private static $space = 0;
	private static $encodings = array();
	private static $mapping = array();
	private $root = '';
	private $data = array();
	private $fonts = array();
	private $unset = array();
	private $objects = array();
	private $currentFontRessource = array();
	private $text = '';
	private $file = '';

	public function __construct($file){
		if(!file_exists($file)){
			t_e('file not found', $file);
		} else{
			$this->file = $file;
		}
	}

	public function processText(){
		$this->setupFont();
		defined('DEBUG') && $this->mem();
		$this->fillData($this->file);
		defined('DEBUG') && $this->mem();
		$this->unset = array();
		if(defined('DEBUG') && strstr(DEBUG, 'tree')){
			print_r($this->data);
		}
		$this->setFontTables();
		defined('DEBUG') && $this->mem();
		$this->getAllPageObjects(trim($this->data[$this->root]['Pages'], self::TRIM_LIST));
		defined('DEBUG') && $this->mem();
		$this->unsetElem();
		$this->getText();
		unset($this->data);
		/* echo $this->root;
		  print_r($this->fonts);
		  print_r($this->objects); */

		return $this->text;
	}

	public function getInfo(){
		$offset = filesize($this->file) - 1024;
		$file = fopen($this->file, 'r');
		$data = fread($file, 1024);
		fseek($file, $offset);
		$data .= fread($file, 1024);
		fclose($file);
		$match = array();
		if(preg_match('#trailer[\r\n ]*<<(.*)>>#s', $data, $match)){
			preg_match_all('#/(\w+)[ \r\n]{0,2}(\d+ \d+) R[\r\n]*#s', $match[1], $match, PREG_SET_ORDER);

			foreach($match as $cur){
				if($cur[1] == 'Info'){
					$info = $cur[2];
					break;
				}
			}
			for($data = $this->readPortion($this->file); !empty($data); $data = $this->readPortion()){
				if(preg_match('#[\r\n ]+(' . $info . ' ' . self::OBJ . '.*' . self::ENDOBJ . ')#Us', $data, $match)){
					$this->readPortion(-1);
					$this->parsePDF($match[0]);
					break;
				}
			}
			$info = $this->data[$info];
			$this->data = array();
			foreach($info as $key => &$cur){
				$cur = self::getStringContent($cur);
				if(strstr($key, 'Date') && method_exists('DateTime', 'createFromFormat')){
					if(($cur = DateTime::createFromFormat('YmdHis', substr($cur, 2, 14)))){
						$cur = $cur->format(g_l('date', '[format][default]'));
					}
				}
			}
			return $info;
		}
		return array();
	}

	private static function getStringContent($str){
		$str = trim($str);
		switch(substr($str, 0, 1)){
			case '('://string
				return CheckAndConvertISOfrontend(preg_replace_callback('#\\\\(\d{3})#', 'we_helpers_pdf2text::setOctChar', trim($str, self::TRIM_STRING)));
			case '<'://hex
				$str = trim($str, '<>');
				$str = str_split($str, 2);
				$out = '';
				foreach($str as $cur){
					$out.=chr(hexdec($cur));
				}
				return CheckAndConvertISOfrontend(mb_convert_encoding(preg_replace_callback('#\\\\(\d{3})#', 'we_helpers_pdf2text::setOctChar', $out), 'UTF-8', 'UTF-16'));
		}
	}

	private function setFontTables(){
		foreach($this->fonts as $cur){
			$elem = &$this->data[$cur];
			$elem['charMap'] = array();
			$encoding = (isset($elem['Encoding']) ? $elem['Encoding'] : '');
			if(substr($encoding, -1) == 'R'){
				$id = rtrim($encoding, self::TRIM_REF);
				$this->unset[] = rtrim($encoding, self::TRIM_REF);
				$this->processFontDictionary($this->data[$id], $elem);
			} else{
				$this->setDefaultFontTable($encoding, $elem);
			}

			if(isset($elem['ToUnicode'])){
				$id = rtrim($elem['ToUnicode'], self::TRIM_REF);
				$this->unset[] = $id;

				self::applyToUnicode(self::getStream($this->data[$id]), $elem['charMap']);
			}
		}
	}

	private function processFontDictionary($dict, &$elem){
		$this->setDefaultFontTable(isset($dict['BaseEncoding']) ? $dict['BaseEncoding'] : '', $elem);
		//print_r($elem);
		if(isset($dict['Differences'])){
			$matches = array();
			$diff = $dict['Differences'];
			preg_match_all('#(\d+)(([\r\n ]*\/\w+)*)#s', $diff, $matches, PREG_SET_ORDER);
			foreach($matches as $m){
				$start = $m[1];
				$replace = explode(' ', trim(strtr($m[2], array("\n" => ' ', "\r" => ' ', '/' => ' ', '_' => '', '   ' => ' ', '  ' => ' '))));
				foreach($replace as $cur){
					$cur = trim($cur);
					if(empty($cur)){
						continue;
					}
					$from = $this->unichr($start++);
					if(!isset($this->mapping[$cur])){
						continue;
					}
					$to = $this->mapping[trim($cur)];
					if($from != $to){
						$elem['charMap'][$from] = $to;
					}
				}
			}
		}
		//print_r($elem);
	}

	private function setDefaultFontTable($encoding, &$elem){
		switch($encoding){
			default:
				$encoding = ltrim($encoding, '/');
				if(isset($this->encodings[$encoding])){
					$elem['charMap'] = $this->encodings[$encoding];
					return;
				} else{
					//print_r($this->encodings);
			if(defined('DEBUG') && strstr(DEBUG, 'fontout')){
				echo 'not found:' . $encoding;
			}
				}
			case '':
			case '/Identity':
			case '/Identity-h':
			case '/Identity-v':
				$elem['charMap'] = $this->encodings['standardEncoding'];
		}
	}

	private static function getStream($elem){
		/* if(!isset($elem['stream'])){
		  print_r($elem);
		  } */
		switch($elem['Filter']){

			case '/Fl':
			case '/FlateDecode':
				return @gzuncompress($elem['stream']);
			case 'ASCIIHexDecode':
			case 'AHx':
				return self::decodeFilterASCIIHexDecode($elem['stream']);
			case 'ASCII85Decode':
			case 'A85':
				return self::decodeFilterASCII85Decode($elem['stream']);
			case 'LZWDecode':
			case 'LZW':
				return self::decodeFilterLZWDecode($elem['stream']);
			case 'RunLengthDecode':
			case 'RL':
				return self::decodeFilterRunLengthDecode($elem['stream']);
			case 'CCITTFaxDecode':
			case 'CCF':
			//can be ignored - used for images only
			case 'DCTDecode':
			case 'DCT':
			//can be ignored - used for images only

			default:
				return $elem['stream'];
		}
	}

	/**
	 * ASCIIHexDecode
	 * Decodes data encoded in an ASCII hexadecimal representation, reproducing the original binary data.
	 * @param $data (string) Data to decode.
	 * @return Decoded data string.
	 * @public
	 * @since 1.0.000 (2011-05-23)
	 */
	private static function decodeFilterASCIIHexDecode($data){
		// intialize string to return
		$decoded = '';
		// all white-space characters shall be ignored
		$data = preg_replace('/[\s]/', '', $data);
		// check for EOD character: GREATER-THAN SIGN (3Eh)
		$eod = strpos($data, '>');
		if($eod !== false){
			// remove EOD and extra data (if any)
			$data = substr($data, 0, $eod);
			$eod = true;
		}
		// get data length
		$data_length = strlen($data);
		if(($data_length % 2) != 0){
			// odd number of hexadecimal digits
			if($eod){
				// EOD shall behave as if a 0 (zero) followed the last digit
				$data = substr($data, 0, -1) . '0' . substr($data, -1);
			} else{
				$this->Error('decodeASCIIHex: invalid code');
			}
		}
		// check for invalid characters
		if(preg_match('/[^a-fA-F\d]/', $data) > 0){
			$this->Error('decodeASCIIHex: invalid code');
		}
		// get one byte of binary data for each pair of ASCII hexadecimal digits
		$decoded = pack('H*', $data);
		return $decoded;
	}

	/**
	 * ASCII85Decode
	 * Decodes data encoded in an ASCII base-85 representation, reproducing the original binary data.
	 * @param $data (string) Data to decode.
	 * @return Decoded data string.
	 * @since 1.0.000 (2011-05-23)
	 */
	private static function decodeFilterASCII85Decode($data){
		// intialize string to return
		$decoded = '';
		// all white-space characters shall be ignored
		$data = preg_replace('/[\s]/', '', $data);
		// remove start sequence 2-character sequence <~ (3Ch)(7Eh)
		if(strpos($data, '<~') !== false){
			// remove EOD and extra data (if any)
			$data = substr($data, 2);
		}
		// check for EOD: 2-character sequence ~> (7Eh)(3Eh)
		$eod = strpos($data, '~>');
		if($eod !== false){
			// remove EOD and extra data (if any)
			$data = substr($data, 0, $eod);
		}
		// data length
		$data_length = strlen($data);
		// check for invalid characters
		if(preg_match('/[^\x21-\x75,\x74]/', $data) > 0){
			$this->Error('decodeASCII85: invalid code');
		}
		// z sequence
		$zseq = chr(0) . chr(0) . chr(0) . chr(0);
		// position inside a group of 4 bytes (0-3)
		$group_pos = 0;
		$tuple = 0;
		$pow85 = array((85 * 85 * 85 * 85), (85 * 85 * 85), (85 * 85), 85, 1);
		$last_pos = ($data_length - 1);
		// for each byte
		for($i = 0; $i < $data_length; ++$i){
			// get char value
			$char = ord($data[$i]);
			if($char == 122){ // 'z'
				if($group_pos == 0){
					$decoded .= $zseq;
				} else{
					$this->Error('decodeASCII85: invalid code');
				}
			} else{
				// the value represented by a group of 5 characters should never be greater than 2^32 - 1
				$tuple += (($char - 33) * $pow85[$group_pos]);
				if($group_pos == 4){
					$decoded .= chr($tuple >> 24) . chr($tuple >> 16) . chr($tuple >> 8) . chr($tuple);
					$tuple = 0;
					$group_pos = 0;
				} else{
					++$group_pos;
				}
			}
		}
		if($group_pos > 1){
			$tuple += $pow85[($group_pos - 1)];
		}
		// last tuple (if any)
		switch($group_pos){
			case 4:{
					$decoded .= chr($tuple >> 24) . chr($tuple >> 16) . chr($tuple >> 8);
					break;
				}
			case 3:{
					$decoded .= chr($tuple >> 24) . chr($tuple >> 16);
					break;
				}
			case 2:{
					$decoded .= chr($tuple >> 24);
					break;
				}
			case 1:{
					$this->Error('decodeASCII85: invalid code');
					break;
				}
		}
		return $decoded;
	}

	/**
	 * LZWDecode
	 * Decompresses data encoded using the LZW (Lempel-Ziv-Welch) adaptive compression method, reproducing the original text or binary data.
	 * @param $data (string) Data to decode.
	 * @return Decoded data string.
	 * @public
	 * @since 1.0.000 (2011-05-23)
	 */
	private static function decodeFilterLZWDecode($data){
		// intialize string to return
		$decoded = '';
		// data length
		$data_length = strlen($data);
		// convert string to binary string
		$bitstring = '';
		for($i = 0; $i < $data_length; ++$i){
			$bitstring .= sprintf('%08b', ord($data{$i}));
		}
		// get the number of bits
		$data_length = strlen($bitstring);
		// initialize code length in bits
		$bitlen = 9;
		// initialize dictionary index
		$dix = 258;
		// initialize the dictionary (with the first 256 entries).
		$dictionary = array();
		for($i = 0; $i < 256; ++$i){
			$dictionary[$i] = chr($i);
		}
		// previous val
		$prev_index = 0;
		// while we encounter EOD marker (257), read code_length bits
		while(($data_length > 0) AND (($index = bindec(substr($bitstring, 0, $bitlen))) != 257)) {
			// remove read bits from string
			$bitstring = substr($bitstring, $bitlen);
			// update number of bits
			$data_length -= $bitlen;
			if($index == 256){ // clear-table marker
				// reset code length in bits
				$bitlen = 9;
				// reset dictionary index
				$dix = 258;
				$prev_index = 256;
				// reset the dictionary (with the first 256 entries).
				$dictionary = array();
				for($i = 0; $i < 256; ++$i){
					$dictionary[$i] = chr($i);
				}
			} elseif($prev_index == 256){
				// first entry
				$decoded .= $dictionary[$index];
				$prev_index = $index;
			} else{
				// check if index exist in the dictionary
				if($index < $dix){
					// index exist on dictionary
					$decoded .= $dictionary[$index];
					$dic_val = $dictionary[$prev_index] . $dictionary[$index]{0};
					// store current index
					$prev_index = $index;
				} else{
					// index do not exist on dictionary
					$dic_val = $dictionary[$prev_index] . $dictionary[$prev_index]{0};
					$decoded .= $dic_val;
				}
				// update dictionary
				$dictionary[$dix] = $dic_val;
				++$dix;
				// change bit length by case
				if($dix == 2047){
					$bitlen = 12;
				} elseif($dix == 1023){
					$bitlen = 11;
				} elseif($dix == 511){
					$bitlen = 10;
				}
			}
		}
		return $decoded;
	}

	private static function decodeFilterRunLengthDecode($data){
		// intialize string to return
		$decoded = '';
		// data length
		$data_length = strlen($data);
		$i = 0;
		while($i < $data_length) {
			// get current byte value
			$byte = ord($data{$i});
			if($byte == 128){
				// a length value of 128 denote EOD
				break;
			} elseif($byte < 128){
				// if the length byte is in the range 0 to 127
				// the following length + 1 (1 to 128) bytes shall be copied literally during decompression
				$decoded .= substr($data, ($i + 1), ($byte + 1));
				// move to next block
				$i += ($byte + 2);
			} else{
				// if length is in the range 129 to 255,
				// the following single byte shall be copied 257 - length (2 to 128) times during decompression
				$decoded .= str_repeat($data{($i + 1)}, (257 - $byte));
				// move to next block
				$i += 2;
			}
		}
		return $decoded;
	}

	private static function applyToUnicode($data, &$table){
		$match = array();
		if(preg_match('#beginbfchar(.*)endbfchar#s', $data, $match)){
			preg_match_all('#<([[:alnum:]]*)>[ ]*<([[:alnum:]]*)>#s', $match[1], $match);
			//print_r($match);
			foreach($match[1] as $key => $cur){
				if($cur == $match[2][$key]){
					continue;
				}
				$table[chr(hexdec($cur))] = self::unichr($match[2][$key], true);
			}
			//print_r($table);
		}
		if(preg_match('#beginbfrange(.*)endbfrange#s', $data, $match)){
			preg_match_all('#<([[:alnum:]]{2,4})>[ ]*<([[:alnum:]]{2,4})>[ ]*\[*([ ]*<[[:alnum:]]{2,4}>)+\]*#s', $match[1], $match);
			foreach($match[1] as $key => $cur){
				$start = hexdec($cur);
				$end = hexdec($match[2][$key]);
				$values = trim($match[3][$key], '<> ');
				if(strlen($values) <= 4){
					//single value incremented
					$value = hexdec($values);
					if($start == $value){
						//equal maps
						continue;
					}
					for(; $start < $end; ++$start){
						$table[self::unichr($start)] = self::unichr($value++);
					}
				} else{
					$values = explode('>', strtr($values, array(' ' => '', '<' => '')));
					foreach($values as $cur){
						$table[self::unichr($start++)] = self::unichr($cur, true);
					}
				}
			}
		}
	}

	private function fillData($fname){
		for($data = $this->readPortion($fname); !empty($data); $data = $this->readPortion()){
			$this->parsePDF($data);
		}
		defined('DEBUG') && $this->mem();
	}

	/**
	 * Return unicode char by its code
	 *
	 * @param int $u
	 * @return char
	 */
	private static function unichr($u, $hex = false){
		if($hex){
			$ret = '';
			foreach(str_split($u, 4) as $cur){
				$ret.=mb_convert_encoding('&#x' . $cur . ';', 'UTF-8', 'HTML-ENTITIES');
			}
			return $ret;
		} else{
			return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
		}
	}

	private function setupFont(){
		if(!empty($this->encodings)){
			return;
		}
		require('we_helpers_pdfmapping.inc.php');
		require('we_helpers_pdfencodings.inc.php');
		foreach($nameToUnicodeTab as &$cur){
			$cur = self::unichr($cur);
		}
		unset($cur);

		$newEnc = array();
		foreach($encodings as $type => $myenc){
			$newEnc[$type] = array();
			foreach($myenc as $key => $char){
				if($char != NULL){
					$char = $nameToUnicodeTab[$char];
					$key = chr($key);
					if($char != $key){
						$newEnc[$type][$key] = $char;
					}
				} else{
					$newEnc[$type][chr($key)] = '';
				}
			}
			unset($char);
		}
		unset($myenc);
		$this->encodings = $newEnc;
		$this->mapping = $nameToUnicodeTab;
	}

	private function readPortion($fname = ''){
		static $file = 0;
		static $lastPos = 0;
		if($fname == -1){
			if($file){
				fclose($file);
				$file = 0;
			}
			return'';
		}

		$file = $file ? $file : fopen($fname, 'r');
		$data = '';
		while(($read = fread($file, self::READPORTION))) {
			$data.=$read;
			if(strrpos($read, self::ENDOBJ) !== FALSE){
				break;
			}
		}
		if(!$data || (strlen($data) < self::READPORTION && (strrpos($data, self::ENDOBJ) === FALSE))){
			fclose($file);
			return '';
		}

		$pos = (strrpos($data, self::ENDOBJ) + 7) - strlen($data);
		fseek($file, $pos, SEEK_CUR);
		$lastPos+=strlen($data) + $pos;
		return substr($data, 0, $pos);
	}

	private function parsePDF($data){
		$matches = $matches2 = $matches3 = array();
		preg_match_all('#(\d+ \d+) obj[\r\n]+(.*)endobj#Us', $data, $matches, PREG_SET_ORDER);
		defined('DEBUG') && $this->mem();
		unset($data);
		defined('DEBUG') && $this->mem();
		foreach($matches as $key => $m){
			unset($matches[$key]);
			if(in_array($m[1], $this->unset)){
				continue;
			}
			$values = array();
			if(!preg_match('#(xxxx\d+xxx)|<<(.*)>>[\r\n ]*stream[\r\n]+(.*)endstream#s', $m[2], $matches2)){
				if(!preg_match('#(\d+)|<<(.*)>>#s', $m[2], $matches2)){
					continue;
				}
			}
			defined('DEBUG') && $this->mem();
			unset($m[2]);
			if(isset($matches2[2])){
				preg_match_all('#/(\w+)[ \r\n]{0,2}(\d+ \d+ R|/\w+|\[[^\]]*\]|\([^)]*\)|<[a-fA-F\d]*>)[\r\n]*#s', $matches2[2], $matches3, PREG_SET_ORDER);
				defined('DEBUG') && $this->mem();
				foreach($matches3 as $cur){
					$values[$cur[1]] = $cur[2];
				}
				if(isset($values['Type'])){
					switch($values['Type']){
						case '/FontDescriptor':
							$set = isset($values['FontFile']) ? $values['FontFile'] : (isset($values['FontFile2']) ? $values['FontFile2'] : (isset($values['FontFile3']) ? $values['FontFile3'] : ''));
							if($set){
								$this->unset[] = rtrim($set, self::TRIM_REF);
							}
							continue 2;
						case '/Catalog':
							$this->root = $m[1];
							break;
						case '/XObject':
							continue 2;
						case '/Font':
							$this->fonts[] = $m[1];
							break;
					}
				}

				if(isset($values['Subtype'])){
					switch($values['Subtype']){
						case '/Image':
						/* 						case '/TrueType':
						  case '/Type1':
						  case '/Type2':
						  case '/Type3': */
						case '/XML':
						case '/Link': //no need for links
						case '/Type1C': //Filter font files
							continue 2;
					}
				}
			}
			if($matches2[1]){
				$values['value'] = $matches2[1];
			}
			if(isset($matches2[3])){
				$values['stream'] = $matches2[3];
				if(self::DEFLATE_ALL){
					$values['stream'] = self::getStream($values);
					$values['Filter'].='-done';
				}
			}
			/* if(isset($values['Filter'])&&!isset($values['stream'])){
			  print_r($matches2);
			  print_r( $m[2]);
			  } */
			$this->data[$m[1]] = $values;
		}
		defined('DEBUG') && $this->mem();
	}

	private function getAllPageObjects($id){
		$id = array_map('trim', array_filter(explode(self::TRIM_REF, $id)));
		foreach($id as $cur){
			if(empty($cur)){
				continue;
			}
			$elem = $this->data[$cur];
			switch($elem['Type']){
				case '/Pages':
					$this->unset[] = $cur;
					$this->getAllPageObjects(trim($elem['Kids'], self::TRIM_LIST));
					break;
				case '/Page':
					if(defined('DEBUG') && strstr(DEBUG, 'page')){
						print_r($elem);
					}
					$fonts = array();
					$this->getPageFonts($fonts, $elem);
					if(isset($elem['Font'])){
						$tmp=rtrim($elem['Font'], self::TRIM_REF);
						$data = isset($this->data[$tmp])?$this->data[$tmp]:'';
						if(!empty($data)){
							$this->getPageFonts($fonts, $data);
						}
					}
					if(isset($elem['Resources'])){
						$this->getPageFonts($fonts, $this->data[rtrim($elem['Resources'], self::TRIM_REF)]);
					}
					if(!empty($fonts)){
						$fonts['Type'] = '/FontRessource';
						$this->data[$cur] = $fonts;
						$this->objects[] = $cur;
					}
					$x = array_filter(explode(self::TRIM_REF, trim($elem['Contents'], self::TRIM_LIST)));
					$x = array_map('trim', $x);
					$this->objects = array_merge($this->objects, $x);
			}
		}
	}

	private function getPageFonts(array &$fonts, array $elem){
		foreach($elem as $key => $cur){
			if($key == 'stream'){
				continue;
			}
			$cur = rtrim($cur, self::TRIM_REF);
			if(in_array($cur, $this->fonts)){
				$fonts[$key] = $this->data[$cur]['charMap'];
			}
		}
	}

	private function getText(){
		$texts = $lines = array();
		foreach($this->objects as $cur){
			$elem = $this->data[$cur];
			unset($this->data[$cur]);
			if(isset($elem['Type']) && $elem['Type'] == '/FontRessource'){
				$this->currentFontRessource = $elem;
				continue;
			}
			$stream = self::getStream($elem);
			preg_match_all('#BT[\r\n]+(.*)ET#Us', $stream, $texts, PREG_SET_ORDER);
			unset($stream);
			foreach($texts as $m){
				$this->setTextLines($m[1]);
			}
		}
	}

	private function setTextLines($text){
		static $selectedFont = '';
		$tmpText = '';
		$fs = 10;
		$hasData = false;
		$lines = array();
		preg_match_all('#([^\r\n]*)?[ \r\n]{0,2}(T.|rg|RG|"|\')#Us', $text, $lines, PREG_SET_ORDER);
		/* print_r(str_replace("\r","\n",$text));
		  print_r($lines);
		  return; */
		foreach($lines as $line){
			if(defined('DEBUG') && strstr(DEBUG, 'line')){
				print_r($line);
			}
			switch($line[2]){
				case 'Tf'://fontsize
					if($hasData){
						$this->applyTextChars($tmpText, $selectedFont);
						$tmpText = '';
					}
					$hasData = false;
					list($selectedFont, $fs) = explode(' ', trim($line[1], ' '));
					$fs = floatval($fs);
					$selectedFont = trim($selectedFont, self::TRIM_NAME);
					break;
				case 'T*'://newline
					$tmpText .= self::NL;
					break;
				case 'Td'://potential newline
					list(, $tmp) = explode(' ', $line[1]);
					if($tmp){
						$tmpText .= self::NL;
					}
					break;
				case 'TD'://newline
					$tmpText .= self::SPACE;
					break;
				case '\'':
				case '"':
					$tmpText .= self::NL;
//no break
				case 'TJ':
				case 'Tj':
					$hasData = true;
					$tmpText.=$this->extractPSTextElement($line[1], $fs);
					break;
			}
		}
		$tmpText.=self::SPACE;
		$this->applyTextChars($tmpText, $selectedFont);
	}

	private function applyTextChars($text, $selectedFont){
		$text = str_replace(array('\\\\', '\(', '\)'), array('\\\\', '(', ')'), $text);

		if($selectedFont == ''){
			$this->text.=$text;
			return;
		}

		if(isset($this->currentFontRessource[$selectedFont])){
			if(defined('DEBUG') && strstr(DEBUG, 'fontout')){
				print_r($this->currentFontRessource[$selectedFont]);
			}
			$res = $this->currentFontRessource[$selectedFont];
			$tmp = '';
			for($i = 0; $i < strlen($text); ++$i){
				$x = $text{$i};
				$tmp.=isset($res[$x]) ? $res[$x] : $x;
			}
			//		$tmp = str_replace(array_keys($this->currentFontRessource[$selectedFont]), $this->currentFontRessource[$selectedFont], $text);
			if(defined('DEBUG') && strstr(DEBUG, 'fontout')){
				echo 'Font:' . $selectedFont . ' ' . $text . ' post: ' . $tmp . "\n";
			}
			$this->text.=$tmp;
		} else{
			if(defined('DEBUG') && strstr(DEBUG, 'fontout')){
				echo 'Error-text: ' . $selectedFont;
			}
		}
	}

	private static function setOctChar($char){
		return chr(octdec($char[1]));
	}

	private function extractPSTextElement($string, $fs){
		self::$space = -4 * $fs;
		$parts = array();
		preg_match_all('#\(((?:\\\\.|[^\\\\\\)])+)\)(-?\d+\.\d{1,7})?#', $string, $parts);

		//add spaces only if size is bigger than a certain amount
		$parts[2] = array_filter($parts[2], 'we_helpers_pdf2text::lower');
		foreach(array_keys($parts[2]) as $key){
			$parts[1][$key].=self::SPACE;
		}
		$tmp = implode('', $parts[1]);

		return preg_replace_callback('#\\\\(\d{3})#', 'we_helpers_pdf2text::setOctChar', $tmp);
	}

	private static function lower($val){
		return $val < self::$space;
	}

	private function unsetElem(){
		foreach($this->unset as $cur){
			unset($this->data[$cur]);
		}
		$this->unset = array();
	}

	private function mem($last = false){
		static $max = 0;
		if(defined('DEBUG_MEM')){
			if($max < memory_get_usage()){
				$max = memory_get_usage();
			}
		}
		if($last)
			print('Mem usage ' . round((($max / 1024) / 1024), 3) . ' MiB' . "\n");
	}

}

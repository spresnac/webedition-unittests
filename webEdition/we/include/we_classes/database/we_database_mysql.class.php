<?php

/**
 * webEdition CMS
 *
 * $Rev: 5116 $
 * $Author: mokraemer $
 * $Date: 2012-11-10 14:38:12 +0100 (Sat, 10 Nov 2012) $
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
class DB_WE extends we_database_base{

	private $conType = '';

	protected function ping(){
		return @mysql_ping($this->Link_ID);
	}

	/* public: connection management */

	protected function connect($Database = DB_DATABASE, $Host = DB_HOST, $User = DB_USER, $Password = DB_PASSWORD){
		/* establish connection, select database */
		if(!$this->isConnected()){
			switch(DB_CONNECT){
				case 'pconnect':
					$this->Link_ID = @mysql_pconnect($Host, $User, $Password);
					if($this->Link_ID){
						$this->conType = 'pconnect';
						break;
					}
				//intentionally no break
				case 'connect':
					$this->Link_ID = @mysql_connect($Host, $User, $Password);
					if(!$this->Link_ID){
						$this->halt("(p)connect($Host, $User) failed.");
						return false;
					}
					$this->conType = 'connect';
					break;
				default:
					$this->halt('Error in DB connect');
					exit('Error in DB connect');
			}
			if(!@mysql_select_db($Database, $this->Link_ID) &&
				!@mysql_select_db($Database, $this->Link_ID) &&
				!@mysql_select_db($Database, $this->Link_ID) &&
				!@mysql_select_db($Database, $this->Link_ID)){
				$this->halt('cannot use database ' . $this->Database);
				return false;
			}
			if($this->Link_ID){
				$this->_setup();
			}
		}
		return ($this->Link_ID > 0);
	}

	protected function _setCharset($charset){
		@mysql_set_charset($charset);
	}

	/* public: discard the query result */

	protected function _free(){
		@mysql_free_result($this->Query_ID);
	}

	protected function _query($Query_String, $unbuffered = false){
		return ($unbuffered ?
				@mysql_unbuffered_query($Query_String, $this->Link_ID) :
				@mysql_query($Query_String, $this->Link_ID));
	}

	public function close(){
		if($this->Link_ID){
			@mysql_close($this->Link_ID);
		}
		$this->Link_ID = 0;
	}

	protected function fetch_array($resultType){
		return @mysql_fetch_array($this->Query_ID, $resultType);
	}

	/* public: position in result set */

	protected function _seek($pos = 0){
		return @mysql_data_seek($this->Query_ID, $pos);
	}

	/* public: evaluate the result (size, width) */

	public function affected_rows(){
		return @mysql_affected_rows($this->Link_ID);
	}

	public function num_rows(){
		return @mysql_num_rows($this->Query_ID);
	}

	public function num_fields(){
		return @mysql_num_fields($this->Query_ID);
	}

	public function field_name($no){
		return @mysql_field_name($this->Query_ID, $no);
	}

	public function field_type($no){
		return @mysql_field_type($this->Query_ID, $no);
	}

	public function field_table($no){
		return @mysql_field_table($this->Query_ID, $no);
	}

	public function field_len($no){
		//fix faulty lenght on text-types with connection in utf-8
		$len = intval(@mysql_field_len($this->Query_ID, $no));
		$type = $this->field_type($no);
		if(DB_SET_CHARSET == 'utf8' && strtolower($type) == 'string'){
			$len/=3;
		}
		return $len;
	}

	public function field_flags($no){
		return @mysql_field_flags($this->Query_ID, $no);
	}

	public function getInsertId(){
		return mysql_insert_id($this->Link_ID);
	}

	public function getCurrentCharset(){
		return mysql_client_encoding();
	}

	public function getInfo(){
		return '<table class="defaultfont"><tr><td>type:</td><td>' . $this->conType .
			'</td></tr><tr><td>protocol:</td><td>' . mysql_get_proto_info() .
			'</td></tr><tr><td>client:</td><td>' . mysql_get_client_info() .
			'</td></tr><tr><td>host:</td><td>' . mysql_get_host_info() .
			'</td></tr><tr><td>server:</td><td>' . mysql_get_server_info() .
			'</td></tr><tr><td>encoding:</td><td>' . mysql_client_encoding() . '</td></tr></table>';
	}

	protected function errno(){
		return $this->Link_ID ? mysql_errno($this->Link_ID) : 2006;
	}

	protected function error(){
		return $this->Link_ID ? mysql_error($this->Link_ID) : 'no Link to DB';
	}

	protected function info(){
		return $this->Link_ID ? mysql_info($this->Link_ID) : 'no Link to DB';
	}

}

<?php

/**
 * webEdition CMS
 *
 * $Rev: 4966 $
 * $Author: mokraemer $
 * $Date: 2012-09-14 23:40:01 +0200 (Fri, 14 Sep 2012) $
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
				case 'msconnect':
					$this->Link_ID = @sqlsrv_connect($Host, array('Database' => $Database, 'UID' => $User, 'PWD' => $Password, 'CharacterSet' => 'UTF-8'));
					if(!$this->Link_ID){
						$this->halt("sqlsrv($Host, $User, $Database) failed.");
						return false;
					}
					$this->conType = 'msconnect';
					break;
				default:
					$this->halt('Error in DB connect');
					exit('Error in DB connect');
			}
			if($this->Link_ID){
				$this->_setup();
			}
		}
		return ($this->Link_ID > 0);
	}

	protected function _setCharset($charset){
		//@ mysql_set_charset($charset);  bereits beim connect
	}

	/* public: discard the query result */

	protected function _free(){
		//@mysql_free_result($this->Query_ID);
	}

	protected function _query($Query_String, $unbuffered = false){//unbuffered wird wohl nicht unterstützt
		return @sqlsrv_query($this->Link_ID, $Query_String);
	}

	public function close(){
		if($this->Link_ID){
			@sqlsrv_close($this->Link_ID);
		}
		$this->Link_ID = 0;
	}

	protected function fetch_array($resultType){
		return @sqlsrv_fetch_array($this->Query_ID, $resultType);
	}

	/* public: position in result set */

	protected function _seek($pos = 0){
		return @mysql_data_seek($this->Query_ID, $pos);
	}

	/* public: evaluate the result (size, width) */

	public function affected_rows(){
		return @sqlsrv_rows_affected($this->Link_ID);
	}

	public function num_rows(){
		return @sqlsrv_num_rows($this->Query_ID);
	}

	public function num_fields(){
		return @sqlsrv_num_fields($this->Query_ID);
	}

	public function field_name($no){
		$metadata = sqlsrv_field_metadata($this->Query_ID);
		return $metadata[$no]['Name'];
	}

	public function field_type($no){
		$metadata = sqlsrv_field_metadata($this->Query_ID);
		return $metadata[$no]['Type']; //liefert den Numerischen Wert
	}

	public function field_table($no){
		//return @mysql_field_table($this->Query_ID, $no);
	}

	public function field_len($no){
		$metadata = sqlsrv_field_metadata($this->Query_ID);
		return $metadata[$no]['Size'];
	}

	public function field_flags($no){
		//return @mysql_field_flags($this->Query_ID, $no);
	}

	public function getInsertId(){
		//return mysql_insert_id($this->Link_ID);
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

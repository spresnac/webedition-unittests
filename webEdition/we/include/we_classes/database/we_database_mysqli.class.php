<?php

/**
 * webEdition CMS
 *
 * $Rev: 5554 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 10:14:54 +0100 (Fri, 11 Jan 2013) $
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
ini_set('mysqli.reconnect', 1);

class DB_WE extends we_database_base{

	protected function _free(){
		if(is_object($this->Query_ID)){
//			print_r(debug_backtrace());
			@$this->Query_ID->free();
		}
	}

	protected function _query($Query_String, $unbuffered = false){
		$this->_free();
		$tmp = $this->Link_ID->query($Query_String, ($unbuffered ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT));
		if($tmp === false){
			return 0;
		} else if($tmp === true){
			return -1; //!=0 what is tested.
		}
		return $tmp;
	}

	protected function _setCharset($charset){
		$this->Link_ID->set_charset($charset);
	}

	protected function _seek($pos = 0){
		return (is_object($this->Query_ID)) && $this->Query_ID->data_seek($pos);
	}

	protected function errno(){
		return ($this->Link_ID ? $this->Link_ID->errno : 2006);
	}

	protected function error(){
		return ($this->Link_ID ? $this->Link_ID->error : '');
	}

	protected function info(){
		return ($this->Link_ID ? $this->Link_ID->info : '');
	}

	protected function fetch_array($resultType){
		return (is_object($this->Query_ID)) ?
			$this->Query_ID->fetch_array($resultType) :
			false;
	}

	public function affected_rows(){
		return $this->Link_ID->affected_rows;
	}

	public function close(){
		if($this->Link_ID){
			$this->Link_ID->close();
			$this->Link_ID = null;
			$this->Query_ID = null;
		}
	}

	protected function connect($Database = DB_DATABASE, $Host = DB_HOST, $User = DB_USER, $Password = DB_PASSWORD){
		if(!$this->isConnected()){
			switch(DB_CONNECT){
				case 'mysqli_pconnect':
					$Host = 'p:' . $Host;
				case 'mysqli_connect':
					$this->Query_ID = null;
					$this->Link_ID = new mysqli($Host, $User, $Password, $Database);
					if($this->Link_ID->connect_error){
						$this->Link_ID = null;
						$this->halt("mysqli_(p)connect($Host, $User) failed.");
						return false;
					}
					break;
				default:
					$this->halt('Error in DB connect');
					exit('Error in DB connect');
			}
		}
		$this->_setup();
		return true;
	}

	public function field_flags($no){
		return (is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->flags : '');
	}

	public function field_len($no){
		if(is_object($this->Query_ID)){
			$len = $this->Query_ID->fetch_field_direct($no)->length;
			//fix faulty lenght on text-types with connection in utf-8
			$type = $this->field_type($no);
			if(DB_SET_CHARSET == 'utf8' && $type >= 252 && $type <= 254){
				$len/=3;
			}
			return $len;
		} else{
			return 0;
		}
	}

	public function field_name($no){
		return (is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->name : '');
	}

	public function field_table($no){
		return (is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->table : '');
	}

	public function field_type($no){
		return (is_object($this->Query_ID) ? $this->Query_ID->fetch_field_direct($no)->type : '');
	}

	public function getInsertId(){
		return $this->Link_ID->insert_id;
	}

	public function num_fields(){
		return $this->Link_ID->field_count;
	}

	public function num_rows(){
		return is_object($this->Query_ID) ? $this->Query_ID->num_rows : 0;
	}

	public function getCurrentCharset(){
		$charset = mysqli_get_charset($this->Link_ID);
		return $charset->charset;
	}

	public function getInfo(){
		$charset = mysqli_get_charset($this->Link_ID);
		return '<table class="defaultfont"><tr><td>type:</td><td>' . DB_CONNECT .
			'</td></tr><tr><td>protocol:</td><td>' . $this->Link_ID->protocol_version .
			'</td></tr><tr><td>client:</td><td>' . $this->Link_ID->client_info .
			'</td></tr><tr><td>host:</td><td>' . $this->Link_ID->host_info .
			'</td></tr><tr><td>server:</td><td>' . $this->Link_ID->server_info .
			'</td></tr><tr><td>encoding:</td><td>' . $charset->charset . '</td></tr></table>';
	}

	protected function ping(){
		return $this->Link_ID->ping();
	}

}

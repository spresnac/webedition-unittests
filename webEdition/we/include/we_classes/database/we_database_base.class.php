<?php

/**
 * webEdition CMS
 *
 * $Rev: 5965 $
 * $Author: mokraemer $
 * $Date: 2013-03-16 17:28:12 +0100 (Sat, 16 Mar 2013) $
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
/* * this is the abstract super class for DB connections */

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf.inc.php');

abstract class we_database_base{

	private static $pool = array();
	private static $conCount = 0;
	private static $linkCount = 0;
	//states if we have lost connection and try again
	private $retry = false;
	/* link handles */
	protected $Link_ID = 0;
	/* query handles */
	protected $Query_ID = 0;
	/*	 * true, if first query failed due to some server conditions */

	/** result array */
	public $Record = array();

	/** current row number */
	public $Row = 0;

	/** current error number */
	public $Errno = 0;
	/*	 * current Error text */
	public $Error = "";

	/** public: connection parameters */
	public $Database = DB_DATABASE;
	private static $Trigger_cnt = 0;

	/** Connects to the database, which this is done by the constructor
	 *
	 */
	abstract protected function connect($Database = DB_DATABASE, $Host = DB_HOST, $User = DB_USER, $Password = DB_PASSWORD);

	/** internal query
	 * @param $Query_String string the sql statement
	 * @param $unbuffered bool if this query is executed buffered or unbuffered
	 * @return int a query_id
	 */
	abstract protected function _query($Query_String, $unbuffered = false);

	/** set charset for the session
	 */
	abstract protected function _setCharset($charset);

	/** internal free function */
	abstract protected function _free();

	/** internal get last error
	 * @return string error
	 */
	abstract protected function error();

	/** internal get last errorno
	 * @return int number
	 */
	abstract protected function errno();

	/** get info of latest executed query
	 * @return String
	 */
	abstract protected function info();

	/** internal seek to a specific position in result-set
	 * @return bool if seek was successfull
	 */
	abstract protected function _seek($pos = 0);

	/** internal fetch the data from DB
	 * @param $resultType int (MYSQL_BOTH,MYSQL_ASSOC,MYSQL_NUM)
	 * @return array depending on resulttype
	 */
	abstract protected function fetch_array($resultType);

	/** internal check if Database is still responding
	 * @return true if DB is still present
	 */
	abstract protected function ping();

	/** get the last inserted ID
	 * @return int last generated id of the insert-statement
	 */
	abstract function getInsertId();

	/** get the no of rows in the resultset
	 * @return int row count
	 */
	abstract function num_rows();

	/** get the count of fields in the result-set
	 * @return int no of fields
	 */
	abstract function num_fields();

	/** get the name of the field no
	 * @param $no int field no
	 * @return string orignal name of the field
	 */
	abstract function field_name($no);

	/** get the type of the field no
	 * @param $no int field no
	 * @return string type of the field
	 */
	abstract function field_type($no);

	/** get the name of the table field no is located
	 * @param $no int field no
	 * @return string orignal table location of the field
	 */
	abstract function field_table($no);

	/** get the lenght of the field no
	 * @param $no int field no
	 * @return int length inside the tabledef. of the field
	 */
	abstract function field_len($no);

	/** get the flags of the field no
	 * @param $no int field no
	 * @return int field flags as int
	 */
	abstract public function field_flags($no);

	/** close the DB connection
	 *
	 */
	abstract public function close();

	/** get the no of rows that were affected by update/delete/replace
	 * @return int count of rows
	 */
	abstract public function affected_rows();

	/** get Information about the used driver etc.
	 * @return string containing all information
	 */
	abstract public function getInfo();

	/*	 * returns the charset of the current connection */

	abstract public function getCurrentCharset();

	/** Constructor, establishes the connection to the DB
	 *
	 */
	public function __construct(){
		//make lazy connections, only the first one is executed instantly
		if(!self::$linkCount){
			$this->connect();
			self::$linkCount++;
		}
		self::$conCount++;
	}

	/**
	 * Fill connection pool
	 */
	public function __destruct(){
		if($this->Link_ID && $this->Database == DB_DATABASE){
			$this->free();
			self::$pool[] = $this->Link_ID;
		}
	}

	/**
	 * called on serialize of this class, closes db connection
	 * @return type empty array
	 */
	public function __sleep(){
		$this->close();
		return array();
	}

	/**
	 * called if this class is unserialized
	 */
	public function __wakeup(){
		$this->_connect();
	}

	/**
	 * internal connect which uses Connection Pools
	 * @return type
	 */
	protected function _connect(){
		$this->Link_ID = array_pop(self::$pool);
		if(!$this->Link_ID || !$this->ping()){
			self::$linkCount++;
			$this->connect();
		}
		return $this->Link_ID;
	}

	/**
	 * Only for debug
	 */
	static function showStat(){
		echo 'tried connections: ' . self::$conCount . '<br>' . 'real connections: ' . self::$linkCount;
	}

	/**
	 * call this function to make sure the connection is setup correctly
	 */
	protected function _setup(){
// deactivate MySQL strict mode; don't use query function (error logging)
		$this->_query('SET SESSION sql_mode=""');
		if(defined('DB_SET_CHARSET') && DB_SET_CHARSET != ''){
			$this->_setCharset(DB_SET_CHARSET);
		}
	}

	/**
	 * This function is a replacement for mysql_real_escape_string, which sends the string to mysql to escape it
	 * @deprecated NOTE: this function will be removed; in future there will be a function for prepared statements
	 * @param array/string $inp value to escape for sql-query
	 * @return array/string
	 */
	function escape($inp){
		return escape_sql_query($inp);
	}

	/** check the connection
	 *
	 * @return bool true, if the DB is connected
	 */
	function isConnected(){
		return ($this->Link_ID) && $this->ping();
	}

	/**
	 * make an sql-Query to the DB
	 * @param string $Query_String the sql-query
	 * @param bool $allowUnion this parameter is deprecated; it determines if the query is allowed to have unions
	 * @return bool true, if the query was successfull
	 */
	function query($Query_String, $allowUnion = false, $unbuffered = false){
		if(!$this->retry){
			$this->Errno = 0;
			$this->Error = '';
			$this->Row = 0;
			if(self::$Trigger_cnt){
				--self::$Trigger_cnt;
				t_e($Query_String);
			}
		}
		/* No empty queries, please, since PHP4 chokes on them. */
		if($Query_String == ''){
			/* The empty query string is passed on from the constructor,
			 * when calling the class without a query, e.g. in situations
			 * like these: '$db = new DB_Sql_Subclass;'
			 */
			return true;
		}
		if(!$this->isConnected() && !$this->_connect())
			return false;
		/* we already complained in connect() about that. */

// check for union This is the fastest check
// if union is found in query, then take a closer look
		if($allowUnion == false && stristr($Query_String, 'union')){
			if(preg_match('/[\s\(`=\)\/]union[\s\(`\/]/i', $Query_String)){
				$queryToCheck = str_replace(array("\\\"", "\\'"), '', $Query_String);

				$singleQuote = false;
				$doubleQuote = false;

				$queryWithoutStrings = '';

				for($i = 0; $i < strlen($queryToCheck); $i++){
					$char = $queryToCheck[$i];
					if($char == '"' && $doubleQuote == false && $singleQuote == false){
						$doubleQuote = true;
					} else if($char == '\'' && $doubleQuote == false && $singleQuote == false){
						$singleQuote = true;
					} else if($char == '"' && $doubleQuote == true){
						$doubleQuote = false;
					} else if($char == '\'' && $singleQuote == true){
						$singleQuote = false;
					}
					if($doubleQuote == false && $singleQuote == false && $char !== '\'' && $char !== '"'){
						$queryWithoutStrings .= $char;
					}
				}

				if(preg_match('/[\s\(`"\'\\/)]union[\s\(`\/]/i', $queryWithoutStrings)){
					exit('Bad SQL statement! For security reasons, the UNION operator is not allowed within SQL statements per default! You need to set the second parameter of the query function to true if you want to use the UNION operator!');
				}
			}
		}

# New query, discard previous result.
		if($this->Query_ID){
			$this->free();
		}
		$this->Query_ID = $this->_query($Query_String, $unbuffered);

		if(!$this->Query_ID && preg_match('/alter table|drop table/i', $Query_String)){
			$this->_query('FLUSH TABLES');
			$this->Query_ID = $this->_query($Query_String);
		} else
		if(preg_match('/insert |update|replace /i', $Query_String)){
// delete getHash DB Cache
			getHash('', $this);
		}
		if(preg_match('/^[[:space:]]*alter[[:space:]]*table[[:space:]]*(`?([[:alpha:]]|[[:punct:]])+`?)[[:space:]]*(add|change|modify|drop)/i', $Query_String, $matches)){
			$this->_query('ANALYZE TABLE `' . $matches[1] . '`');
		}
		$this->Errno = $this->errno();
		$this->Error = $this->error();
		$this->Row = 0;
		if(!$this->Query_ID){
			switch($this->Errno){
				case 2006://SERVER_GONE_ERROR
				case 2013://SERVER_LOST
					if(!$this->retry){
						$this->retry = true;
						$this->Link_ID = 0;
						$tmp = $this->query($Query_String, $allowUnion, $unbuffered);
						$this->retry = false;
						return $tmp;
					}
				case 0:
					//don't know why, but ignore this
					return true;
				default:
					trigger_error('MYSQL-ERROR' . "\nFehler: " . $this->Errno . "\nDetail: " . $this->Error . "\nInfo:" . $this->info() . "\nQuery: " . $Query_String, E_USER_WARNING);
					if(defined('WE_SQL_DEBUG') && WE_SQL_DEBUG == 1){
						error_log('MYSQL-ERROR - Fehler: ' . $this->Errno . ' Detail: ' . $this->Error . ' Query: ' . $Query_String);
					}
			}
		}

//(bool) entfernt um Kompatibilität mit alten weDevEdge Beispiel herzustellen
		return $this->Query_ID;
	}

	/* discard the query result */

	public function free(){
		$this->_free();
		$this->Query_ID = 0;
		$this->Record = array();
	}

	/** shorthand notation for num_rows */
	public function nf(){
		return $this->num_fields();
	}

	/** shorthand for print num_rows
	 */
	public function np(){
		print $this->num_rows();
	}

	/**
	 * get a value from a field, queried by a prequel query+next_record
	 * @param mixed $Name name/number of the field, depending on query-type
	 * @return mixed returns the value or '' if not present
	 */
	public function f($Name){
		return isset($this->Record[$Name]) ? $this->Record[$Name] : "";
	}

	/**
	 * directly print the value from a field, queried by a prequel query+next_record
	 * @param type $Name name/number of the field, depending on query-type
	 *
	 */
	public function p($Name){
		print $this->Record[$Name];
	}

	/**
	 * get an array with all field names from a previous query
	 * @return array in order of the query, all fields are added with their name, having one name per row
	 */
	function fieldNames(){
		$res = array();
		if(!($this->Query_ID)){
			return $res;
		}
		$count = $this->num_fields();
		for($i = 0; $i < $count; $i++){
			$res[$i] = $this->field_name($i);
		}
		return $res;
	}

	/**
	 * get all tables named (like)
	 * @param string $like if given, make a like query, without any %
	 * @return array all tables (named like $like)
	 */
	function table_names($like = ''){
		$this->query('SHOW TABLES' . (($like != '') ? ' LIKE "' . $like . '"' : ''));
		$return = array();
		while($this->next_record()) {
			$return[] = array(
				"table_name" => $this->f(0),
				"tablespace_name" => $this->Database,
				"database" => $this->Database);
		}
		return $return;
	}

	/** walk result set
	 * @param $resultType int
	 * @return bool true, if rows was successfully fetched
	 */
	public function next_record($resultType = MYSQL_BOTH){
		if(!($this->Query_ID)){
			$this->halt("next_record called with no query pending.");
			return false;
		}
		$this->Record = $this->fetch_array($resultType);
		$this->Row++;
		$this->Errno = $this->errno();
		$this->Error = $this->error();

		$stat = is_array($this->Record);
		return $stat;
	}

	/** get result at positionset
	 * @param $pos int position in the result set
	 * @param $resultType int
	 * @return bool true, if rows was successfully fetched
	 */
	public function record($pos = 0, $resultType = MYSQL_BOTH){
		if(!$this->seek($pos)){
			return false;
		}

		return $this->next_record($resultType);
	}

	/** set the position in result set
	 * @param $pos int seek to pos in result set
	 * @return bool true, if seek was successfull
	 */
	public function seek($pos = 0){
		if(!$this->Query_ID){
			$this->halt("seek called with no query pending.");
			return false;
		}
		if($this->_seek($pos)){
			$this->Row = $pos;
			return true;
		} else{
			$this->halt("seek($pos) failed: result has " . $this->num_rows() . " rows");

			/* half assed attempt to save the day,
			 * but do not consider this documented or even
			 * desireable behaviour.
			 */
			$this->_seek($this->num_rows());
			$this->Row = $this->num_rows();
			return false;
		}
	}

	/**
	 * Get complete result as array
	 * @return array
	 */
	public function getAll($single = false, $resultType = MYSQL_ASSOC){
		$ret = array();
		while($this->next_record($resultType)) {
			$ret[] = ($single ? current($this->Record) : $this->Record);
		}
		return $ret;
	}

	/**
	 * is a handy setter, for executing `a`="\"b\"" set from an assoc array
	 * @param type $arr
	 */
	static function arraySetter(array $arr, $imp = ','){
		$ret = array();
		foreach($arr as $key => $val){
			if(is_object($val) || is_array($val)){
				t_e('warning', 'data error: db-field cannot contain objects / arrays', 'Key: ' . $key, $arr);
			}
			//current hack: don't escape some used mysql functions
			//FIXME: make this more robust to use internal mysql functions - e.g. functions object?
			$escape = !(is_int($val) || is_float($val));
			if($escape){
				switch(strtoupper($val)){
					case 'NOW()':
					case 'UNIX_TIMESTAMP()':
					case 'UNIX_TIMESTAMP(NOW())':
					case 'CURDATE()':
					case 'CURRENT_DATE()':
					case 'CURRENT_TIME()':
					case 'CURRENT_TIMESTAMP()':
					case 'CURTIME()':
					case 'NULL':
						$escape = false;
				}
			}
			$ret[] = '`' . $key . '`=' . ($escape ? '"' . escape_sql_query($val) . '"' : $val);
		}
		return implode($imp, $ret);
	}

	/* public: return table metadata */

	public function metadata($table = '', $full = false){
		$res = array();

		/*
		 * Due to compatibility problems with Table we changed the behavior
		 * of metadata();
		 * depending on $full, metadata returns the following values:
		 *
		 * - full is false (default):
		 * $result[]:
		 *   [0]["table"]  table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"]  field flags
		 *
		 * - full is true
		 * $result[]:
		 *   ["num_fields"] number of metadata records
		 *   [0]["table"]  table name
		 *   [0]["name"]   field name
		 *   [0]["type"]   field type
		 *   [0]["len"]    field length
		 *   [0]["flags"]  field flags
		 *   ["meta"][field name]  index of field named "field name"
		 *   The last one is used, if you have a field name, but no index.
		 *   Test:  if (isset($result['meta']['myfield'])) {
		 */

// if no $table specified, assume that we are working with a query
// result
		if($table){
			if(!$this->query('SELECT * FROM `' . $table . '` LIMIT 1')){
				$this->halt("Metadata query failed.");
			}
		} else{
			if(!($this->Query_ID))
				$this->halt("No query specified.");
		}
		$count = $this->num_fields();
		if(!$count){
			trigger_error('MYSQL-ERROR' . "\n" . 'Fehler: Metadata-Query on table ' . $table . ' failed' . "\n", E_USER_WARNING);
		}

		for($i = 0; $i < $count; $i++){
			$res[$i] = array(
				"table" => $this->field_table($i),
				"name" => $this->field_name($i),
				"type" => $this->field_type($i),
				"len" => $this->field_len($i),
				"flags" => $this->field_flags($i),
			);
		}
		if($full){
			$res["num_fields"] = $count;
			for($i = 0; $i < $count; $i++){
				$res["meta"][$res[$i]["name"]] = $i;
			}
		}

		$this->free();
		return $res;
	}

	/*	 * checks if this DB connection with this user is allowed to lock a table */

	public function hasLock(){
//lock table
		$this->lock(VALIDATION_SERVICES_TABLE, 'read');
//select from an not locked table - must fail
		$this->_query('SELECT 1 FROM ' . FILE_TABLE);
		$ret = ($this->errno() > 0);
		$this->unlock();
		return $ret;
	}

	/**
	 * @param $table string,array specify the tables to lock; use numeric array to lock all tables with mode; use named array with [table]=mode to lock specific modes
	 * @param $mode string name the locking mode
	 * @return bool true, on success
	 */
	public function lock($table, $mode = 'write'){
		if(!$this->_connect()){
			return false;
		}
		if(is_array($table)){
			$query = array();
			foreach($table as $key => $value){
				$query[] = (is_numeric($key) ?
						$value . ' ' . $mode :
						$key . ' ' . $value);
			}
			$query = implode(',', $query);
		} else{
			$query = $table . ' ' . $mode;
		}
//always lock Errlog-Table
		if(strpos($query, ERROR_LOG_TABLE) === FALSE){
			$query.=',' . ERROR_LOG_TABLE . ' write';
		}

		return $this->_query('lock tables ' . $query);
	}

	/** Unlock all locked tables
	 * @return bool true, on success
	 */
	function unlock(){
		if(!$this->_connect()){
			return false;
		}
		return $this->_query('unlock tables');
	}

	/**
	 * get full query result
	 * @return array
	 */
	public function getRecord(){
		return $this->Record;
	}

	/** print the message and stop further execution
	 * @param string $msg message to be printed
	 */
	protected function halt($msg){
		$this->Error = $this->error();
		$this->Errno = $this->errno();
		/* this doesn't work, since LiveUpdate tries to create tables
		  $this->haltmsg($msg);
		  die("Session halted.");
		 *
		 */
	}

	/**
	 * print a message with the query error
	 * @param string $msg message to be printed
	 */
	protected function haltmsg($msg){
		printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
		printf("<b>MySQL Error</b>: %s (%s)<br>\n", $this->Errno, $this->Error);
	}

	public function isColExist($tab, $col){
		if($tab == '' || $col == ''){
			return false;
		}
		$col = trim($col, '`');
		return (bool) count(getHash('SHOW COLUMNS FROM ' . $this->escape($tab) . ' LIKE "' . $col . '"', $this));
	}

	public function isTabExist($tab){
		if($tab == ''){
			return false;
		}
		$this->query('SHOW TABLES LIKE "' . $this->escape($tab) . '"');
		return ($this->next_record());
	}

	public function addTable($tab, $cols, $keys = array()){
		if(!is_array($cols) || empty($cols)){
			return;
		}
		$cols_sql = array();
		foreach($cols as $name => $type){
			$cols_sql[] = "`" . $name . "` " . $type;
		}
		if(!empty($keys)){
			foreach($keys as $key){
				$cols_sql[] = $key;
			}
		}

		return $this->query('CREATE TABLE ' . $this->escape($tab) . ' (' . implode(',', $cols_sql) . ') ENGINE = MYISAM ' . we_database_base::getCharsetCollation() . ';');
	}

	public function delTable($tab){
		$this->query('DROP TABLE IF EXISTS ' . $this->escape($tab));
	}

	public function addCol($tab, $col, $typ, $pos = ''){
		$col = trim($col, '`');
		if($this->isColExist($tab, $col)){
			return false;
		}
		return $this->query('ALTER TABLE ' . $this->escape($tab) . ' ADD `' . $col . '` ' . $typ . (($pos != '') ? ' ' . $pos : ''));
	}

	public function changeColType($tab, $col, $newtyp){
		$col = trim($col, '`');
		if(!$this->isColExist($tab, $col)){
			return false;
		}

		return $this->query('ALTER TABLE ' . $this->escape($tab) . ' CHANGE `' . $col . '` `' . $col . '` ' . $newtyp);
	}

	public function getColTyp($tab, $col){
		return f('SHOW COLUMNS FROM ' . $this->escape($tab) . ' LIKE "' . $col . '"', 'Type', $this);
	}

	public function delCol($tab, $col){
		if(!$this->isColExist($tab, $col)){
			return;
		}
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' DROP `' . trim($col, '`') . '`');
	}

	function getTableCreateArray($tab){
		$this->query('SHOW CREATE TABLE ' . $this->escape($tab));
		return ($this->next_record()) ?
			explode("\n", $this->f("Create Table")) :
			false;
	}

	public function getTableKeyArray($tab){
		$myarray = array();
		$zw = $this->getTableCreateArray($tab);
		if(!$zw){
			return false;
		}
		foreach($zw as $v){
			$vv = trim($v);
			$posP = strpos($vv, 'PRIMARY KEY');
			$posU = strpos($vv, 'UNIQUE KEY');
			$posK = strpos($vv, 'KEY');
			if(($posP !== false && $posP == 0) || ($posU !== false && $posU == 0) || ($posK !== false && $posK == 0)){
				$myarray[] = trim(rtrim($v, ','));
			}
		}
		return $myarray;
	}

	/**
	 * checks if a key with a full key definition exists
	 * @param type $tab table to check
	 * @param string $key full key definition what is used in a create statement
	 * @return string|boolean extracted key name
	 */
	public function isKeyExistAtAll($tab, $key){
		$matches = array();
		preg_match('|.*KEY *`?([^( `]*)`? \(|', $key, $matches);
		$key = $matches[1];

		$zw = $this->getTableCreateArray($tab);
		if($zw){
			foreach($zw as $v){
				if(preg_match('|.*KEY *`?' . $key . '`? \(|', $v)){
					return $key;
				}
			}
		}
		return false;
	}

	/**
	 * check if a key matches the EXACT definition given by $key
	 * @param string $tab tablename
	 * @param string $key full key definition what is used in a create statement
	 * @return boolean true, if the exact definition is met, false otherwise
	 */
	public function isKeyExist($tab, $key){
		$zw = $this->getTableCreateArray($tab);
		if($zw){
			foreach($zw as $v){
				if(trim(rtrim($v, ',')) == $key)
					return true;
			}
		}
		return false;
	}

	/**
	 * add a key/index to a table
	 * @param string $tab tablename
	 * @param string $fullKey full key definition what is used in a create statement
	 */
	public function addKey($tab, $fullKey){
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' ADD ' . $fullKey);
	}

	/**
	 * delete a key/index from a table
	 * @param string $tab tablename
	 * @param string $keyname ONLY the keyname is wanted here
	 */
	public function delKey($tab, $keyname){
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' DROP ' . ($keyname == 'PRIMARY' ? 'PRIMARY KEY' : 'INDEX `' . $keyname . '`'));
	}

	/**
	 * rename a Col to a new Name
	 * @param string $tab tablename
	 * @param string $oldcol old col-name
	 * @param string $newcol new col-name
	 */
	public function renameCol($tab, $oldcol, $newcol){
		$this->query('ALTER TABLE ' . $this->escape($tab) . ' CHANGE `' . $oldcol . '` `' . $newcol . '`');
	}

	/**
	 * move a column to a new position inside the table
	 * @param string $tab tablename
	 * @param string $colName the name of the col to move
	 * @param string $newPos the new position (possible: FIRST, AFTER colname)
	 */
	public function moveCol($tab, $colName, $newPos){
		//get the old col def, use for alter table.
		$zw = $this->getTableCreateArray($tab);
		if(!$zw){
			return false;
		}
		$colName = '`' . trim($colName, '`') . '`';
		unset($zw[0]);
		$found = false;
		foreach($zw as $def){
			if(strpos($def, $colName) !== FALSE){
				$found = $def;
				break;
			}
		}
		if($found){
			return $this->query('ALTER TABLE ' . $tab . ' MODIFY ' . $found . ' ' . ($newPos == 'FIRST' ? 'FIRST' : 'AFTER `' . trim($newPos, '`') . '`'));
		}
		return false;
	}

	public static function getCharset(){
		return defined('DB_CHARSET') ? DB_CHARSET : '';
	}

	public static function getCollation(){
		return defined('DB_COLLATION') ? DB_COLLATION : '';
	}

	public static function getCharsetCollation(){
		$Charset = self::getCharset();
		$Collation = self::getCollation();
		return ($Charset != '' && $Collation != '' ? ' CHARACTER SET ' . $Charset . ' COLLATE ' . $Collation : '');
	}

	public function t_e_query($cnt = 1){
		self::$Trigger_cnt = $cnt;
	}

}

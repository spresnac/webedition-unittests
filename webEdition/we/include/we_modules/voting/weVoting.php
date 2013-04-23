<?php

/**
 * webEdition CMS
 *
 * $Rev: 5899 $
 * $Author: mokraemer $
 * $Date: 2013-02-28 14:24:54 +0100 (Thu, 28 Feb 2013) $
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
 * General Definition of WebEdition Voting
 *
 */
class weVoting extends weModelBase{
//voting status codes

	const SUCCESS = 1;
	const ERROR = 2;
	const ERROR_REVOTE = 3;
	const ERROR_ACTIVE = 4;
	const ERROR_BLACKIP = 5;
	const ERROR_REQUIRED = 6;

	//properties
	var $ID;
	var $Text;
	var $ParentID;
	var $Icon;
	var $IsFolder;
	var $Path;
	var $QASet = array();
	var $QASetAdditions = array();
	var $IsRequired = false;
	var $AllowFreeText = false;
	var $AllowImages = false;
	var $AllowMedia = false;
	var $AllowSuccessor = false;
	var $AllowSuccessors = false;
	var $Successor = 0;
	var $Scores;
	var $PublishDate = 0;
	var $RestrictOwners = 0;
	var $Owners = array();
	var $Active = 1;
	var $Valid = 0;
	var $RevoteControl = 1;
	var $RevoteTime = -1;
	// it is not automatically (un)serialized
	var $Revote = '';
	var $RevoteUserAgent = '';
	var $answerCount = -1;
	var $defVersion = 0;
	var $FallbackIp = 0;
	var $UserAgent = 0;
	var $FallbackUserID = 0;
	var $Log = 0;
	var $LogData = array();
	var $LogDB = 0;
	var $RestrictIP = 0;
	var $BlackList = array();
	var $ActiveTime = 0;
	var $protected = array('ID', 'ParentID', 'Icon', 'IsFolder', 'Path', 'Text');
	var $FallbackActive = 0;

	/**
	 * Default Constructor
	 * Can load or create new Newsletter depends of parameter
	 */
	function __construct($votingID = 0){
		parent::__construct(VOTING_TABLE);

		if($votingID){
			$this->ID = $votingID;
			$this->load($votingID);
		}

		if(empty($this->QASet)){
			$this->QASet = array(
				0 => array(
					'question' => '',
					'answers' => array(
						0 => '',
						1 => ''
					)
				)
			);
		}
		if(empty($this->QASetAdditions)){
			$this->QASetAdditions = array(
				0 => array('imageID' => '', 'mediaID' => '', 'successorID' => '')
			);
		}

		if($this->Valid == 0){
			$this->Valid = time() + 31536000; //365 days
		}

		if($this->Valid < time() && $this->ActiveTime == 1){
			$this->Active = 0;
		}

		if(empty($this->Scores)){
			$this->Scores = array();
		}

		if($this->PublishDate == 0){
			$this->PublishDate = time();
		}
	}

	function load($id = 0){
		if(parent::load($id)){
			$this->QASet = unserialize($this->QASet);
			$this->QASetAdditions = unserialize($this->QASetAdditions);
			$this->Scores = unserialize($this->Scores);
			$this->Owners = makeArrayFromCSV($this->Owners);
			$this->BlackList = makeArrayFromCSV($this->BlackList);
			if(empty($this->LogData)){
				$this->LogDB = true;
			} else{
				if($this->LogData == 'a:0:{}'){
					$this->LogDB = true;
				} else{
					$this->switchToLogDataDB();
				}
			}
		}
	}

	function save($with_scores = true){

		$this->Icon = ($this->IsFolder == 1 ? we_base_ContentTypes::FOLDER_ICON : we_base_ContentTypes::LINK_ICON);

		if($this->ActiveTime && $this->Valid < time()){
			$this->Active = 0;
		}

		if(!$this->IsFolder && ($with_scores || $this->ID == 0)){
			$qaset_count = count($this->QASet[$this->defVersion]['answers']);
			$scores_count = count($this->Scores);

			if($qaset_count != $scores_count){
				$diff = $qaset_count - $scores_count;
				if($diff > 0){
					for($i = 0; $i < $diff; $i++){
						$this->Scores[] = 0;
					}
				}
				if($diff < 0){
					$diff = abs($diff);
					for($i = 0; $i < $diff; $i++){
						array_splice($this->Scores, (count($this->Scores) - 1), 1);
					}
				}
			}
		}

		$this->ParentID = !empty($this->ParentID) ? $this->ParentID : 0;
		if(isset($_SESSION['user']['ID']) && ($this->RestrictOwners && empty($this->Owners) || !in_array($_SESSION['user']['ID'], $this->Owners)))
			$this->Owners[] = $_SESSION['user']['ID'];

		$_old_QASet = f('SELECT QASet FROM ' . VOTING_TABLE . " WHERE Text='" . $GLOBALS['DB_WE']->escape($this->Text) . "'", "QASet", $GLOBALS['DB_WE']);
		$_new_QASet = $this->QASet;

		$this->QASet = serialize($this->QASet);
		if($with_scores || $this->ID == 0){
			$this->Scores = serialize($this->Scores);
		} elseif(isset($_REQUEST['updateScores']) && $_REQUEST['updateScores']){
			for($_xcount = 0; $_xcount < $_REQUEST['item_count']; $_xcount++){
				if(isset($_REQUEST['scores_' . $_xcount])){
					$temp[$_xcount] = $_REQUEST['scores_' . $_xcount];
				}
				$this->Scores = serialize($temp);
			}
		} else{
			$temp = $this->Scores;
			unset($this->Scores);
		}


		$this->QASetAdditions = serialize($this->QASetAdditions);

		$logdata = $this->LogData;
		unset($this->LogData);
		$oldid = $this->ID;

		$this->Owners = array_unique($this->Owners);
		$this->Owners = makeCSVFromArray($this->Owners, true);

		$this->BlackList = array_unique($this->BlackList);
		$this->BlackList = makeCSVFromArray($this->BlackList, true);

		parent::save();

		$this->QASet = unserialize($this->QASet);
		$this->Scores = ($with_scores || $oldid == 0 ? unserialize($this->Scores) : $temp);

		$this->QASetAdditions = unserialize($this->QASetAdditions);
		$this->Owners = makeArrayFromCSV($this->Owners);
		$this->BlackList = makeArrayFromCSV($this->BlackList);
		$this->LogData = $logdata;
	}

	function saveField($name, $serialize = false){
		$field = ($serialize ? serialize($this->$name) : $this->$name);
		return $this->db->query('UPDATE ' . $this->table . ' SET ' . $this->db->escape($name) . '="' . $this->db->escape($field) . '" WHERE ID=' . intval($this->ID) . ';');
	}

	function delete(){
		if(!$this->ID){
			return false;
		}
		if($this->IsFolder){
			$this->deleteChilds();
		}
		parent::delete();
		return true;
	}

	function deleteChilds(){
		$this->db->query('SELECT ID FROM ' . VOTING_TABLE . ' WHERE ParentID=' . intval($this->ID));
		while($this->db->next_record()) {
			$child = new weVoting($this->db->f('ID'));
			$child->delete();
		}
	}

	function setPath(){
		$this->Path = f('SELECT Path FROM ' . VOTING_TABLE . ' WHERE ID=' . intval($this->ParentID), 'Path', $this->db) . '/' . $this->Text;
	}

	function pathExists($path){
		return f('SELECT 1 AS a FROM ' . $this->db->escape($this->table) . ' WHERE Path = "' . $this->db->escape($path) . '" AND ID != ' . intval($this->ID), 'a', $this->db) == '1';
	}

	function isSelf(){
		return (strpos(clearPath(dirname($this->Path) . '/'), '/' . $this->Text . '/') !== false);
	}

	function evalPath($id = 0){
		$db_tmp = new DB_WE();
		$path = '';
		if($id == 0){
			$id = $this->ParentID;
			$path = $this->Text;
		}

		$foo = getHash('SELECT Text,ParentID FROM ' . VOTING_TABLE . ' WHERE ID=' . intval($id), $db_tmp);
		$path = '/' . (isset($foo['Text']) ? $foo['Text'] : '') . $path;

		$pid = isset($foo['ParentID']) ? $foo['ParentID'] : '';
		while($pid > 0) {
			$db_tmp->query('SELECT Text,ParentID FROM ' . VOTING_TABLE . ' WHERE ID=' . intval($pid));
			while($db_tmp->next_record()) {
				$path = '/' . $db_tmp->f('Text') . $path;
				$pid = $db_tmp->f('ParentID');
			}
		}
		return $path;
	}

	function initByName($name){
		$id = f('SELECT ID FROM ' . VOTING_TABLE . ' WHERE Text="' . $this->db->escape($name) . '"', 'ID', $this->db);
		return $this->load($id);
	}

	function clearSessionVars(){
		if(isset($_SESSION['weS']['voting_session']))
			unset($_SESSION['weS']['voting_session']);
	}

	function filenameNotValid($text){
		return preg_match('%[^a-z0-9äüöß\._\@\ \-]%i', $text);
	}

	function getNext(){
		$this->answerCount++;
//	if($this->AllowFreeText){
		$addOne = 0;
		return ($this->answerCount < count($this->QASet[$this->defVersion]['answers']) + $addOne);
	}

	function resetSets(){
		$this->answerCount = -1;
	}

	function getAnswer($ansn = -1){
		return $this->QASet[$this->defVersion]['answers'][$this->answerCount];
	}

	function getResult($type = 'count', $num_format = '', $precision = we_util_Strings::PRECISION){
		switch($type){
			case 'percent':
				$total = $this->getResult('total');
				if($total <= 0)
					return 0;
				$_scores = isset($this->Scores[$this->answerCount]) ? $this->Scores[$this->answerCount] : 0;
				if($total > 0 && $this->answerCount >= 0)
					$result = round((($_scores / $total) * 100), $precision);
				else
					$result = 100;
				break;
			case 'total':
				$result = array_sum($this->Scores);
				break;
			case 'count':
			default:
				if($this->answerCount >= 0 && isset($this->Scores[$this->answerCount]))
					$result = $this->Scores[$this->answerCount];
				break;
		}

		if(!empty($num_format)){
			$result = we_util_Strings::formatNumber($result, $num_format, $precision);
		}
		return $result;
	}

	function isLastSet(){
		return ($this->answerCount >= count($this->QASet[$this->defVersion]['answers']) - 1);
	}

	function setDefVersion($version){
		if($version < 0){
			$this->defVersion = 0;
		}
		$this->defVersion = ($version < count($this->QASet) ? $version : count($this->QASet) - 1);
	}

	function isAllowedForUser(){

		if($this->RestrictOwners == 0 || we_hasPerm('ADMINISTRATOR') || in_array($_SESSION['user']['ID'], $this->Owners)){
			return true;
		}

		$userids = array();

		we_readParents($_SESSION['user']['ID'], $userids, USER_TABLE, 'IsFolder', 1);

		foreach($userids as $uid){
			if(in_array($uid, $this->Owners)){
				return true;
			}
		}

		return false;
	}

	function getOwnersSql(){
		$owners_sql = '';
		if(!we_hasPerm('ADMINISTRATOR')){
			$userids = array(
				$_SESSION['user']['ID']
			);
			we_readParents($_SESSION['user']['ID'], $userids, USER_TABLE, 'IsFolder', 1);

			$sqlarr = array();
			foreach($userids as $uid){
				$sqlarr[] = 'Owners LIKE ("%,' . $uid . ',%")';
			}
			$owners_sql = ' AND (RestrictOwners=0 OR (' . implode(' OR ', $sqlarr) . ')) ';
		}

		return $owners_sql;
	}

	function getSuccessor($answers){
		$answerID = $answers[0];
		$mySuccessorID = stripslashes($this->QASetAdditions[$this->defVersion]['successorID'][$answerID]);
		return $mySuccessorID;
	}

	function setSuccessor($answers){
		foreach($answers as &$answer){
			if($this->AllowSuccessors){
				$mySuccessorID = stripslashes($this->QASetAdditions[$this->defVersion]['successorID'][$answer]);
				if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
					$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
				}
			}
		}
		if($this->AllowSuccessor){
			$mySuccessorID = $this->Successor;
			if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
				$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
			}
		}
		return self::SUCCESS;
	}

	function vote($answers, $addfields = NULL){
		$votingsession = (isset($_SESSION['_we_voting_sessionID']) ? $_SESSION['_we_voting_sessionID'] : 0);
		$answerID = makeCSVFromArray($answers);
		$answertext = '';
		$successor = 0;
		if(!is_array($answers) || !(count($answers) > 0)){
			if($this->Log){
				$this->logVoting(self::ERROR, $votingsession, $answerID, $answertext, $successor);
			}
			return self::ERROR;
		}

		$ret = $this->canVote();

		if($ret != self::SUCCESS){
			if($this->Log){
				$this->logVoting($ret, $votingsession, $answerID, $answertext, $successor);
			}
			return $ret;
		}

		$countanswers = count($this->QASet[$this->defVersion]['answers']);
		$mySuccessorID = -1;
		foreach($answers as &$answer){
			if(is_numeric($answer) && $answer < $countanswers){
				if($answer > -1 && $answer < count($this->Scores)){
					$this->Scores[$answer]++;
				}
			} else{
				$answertext = $answer;
				$answer = $countanswers - 1;
				$this->Scores[$answer]++;
			}
			if($this->AllowSuccessors){
				$mySuccessorID = stripslashes($this->QASetAdditions[$this->defVersion]['successorID'][$answer]);
				if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
					$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
				}
			}
		}
		if($this->AllowSuccessor){
			$mySuccessorID = $this->Successor;
			if(is_numeric($mySuccessorID) && $mySuccessorID > 0){
				$GLOBALS['_we_voting_SuccessorID'] = $mySuccessorID;
			}
		}
		$answerID = makeCSVFromArray($answers);
		$this->saveField('Scores', true);
		if($this->RevoteTime != 0){
			if($this->RevoteControl == 1){
				if($this->RevoteTime < 0)
					$revotetime = 630720000; //20 years
				else
					$revotetime = $this->RevoteTime;
				setcookie(md5('_we_voting_' . $this->ID), time(), time() + $revotetime);
			} else{
				if(!is_array($this->Revote)){
					$this->Revote = array();
				}
				$this->Revote[$_SERVER['REMOTE_ADDR']] = time();
				$this->saveField('Revote', true);
				if($this->UserAgent){
					$this->RevoteUserAgent = unserialize($this->RevoteUserAgent);
					if(!is_array($this->RevoteUserAgent)){
						$this->RevoteUserAgent = array();
					}
					if(!isset($this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']]) || !is_array($this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']])){
						$this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']] = array();
					}
					$this->RevoteUserAgent[$_SERVER['REMOTE_ADDR']][] = $_SERVER['HTTP_USER_AGENT'];
					$this->saveField('RevoteUserAgent', true);
				}
			}
		}
		if($mySuccessorID <= 0){
			$mySuccessorID = '';
		}
		if(is_array($addfields) && !empty($addfields)){
			$addfieldsdata = serialize($addfields);
		} else{
			$addfieldsdata = '';
		}
		if($this->Log)
			$this->logVoting($ret, $votingsession, $answerID, $answertext, $mySuccessorID, $addfieldsdata);

		return $ret;
	}

	function canVote(){
		if(!$this->isActive()){
			return self::ERROR_ACTIVE;
		}
		if($this->isBlackIP()){
			return self::ERROR_BLACKIP;
		}
		if($this->RevoteTime == 0){
			return self::SUCCESS;
		}

		switch($this->RevoteControl){
			case 2:
				return $this->canVoteUserID();
			case 1:
				return $this->canVoteCookie();
			default:
				return $this->canVoteIP();
		}
	}

	function canVoteCookie(){
		if($this->cookieDisabled() && $this->FallbackIp){
			$this->RevoteControl = 0;
			$this->FallbackActive = 1;
			return $this->canVoteIP();
		}

		if(isset($_COOKIE[md5('_we_voting_' . $this->ID)])){
			return self::ERROR_REVOTE;
		} else{
			if($this->FallbackUserID){
				return $this->canVoteUserID();
			} else
				return self::SUCCESS;
		}
	}

	function canVoteIP(){

		$this->Revote = unserialize($this->Revote);
		if(!is_array($this->Revote))
			$this->Revote = array();

		$revotetime = ($this->RevoteTime < 0 ? time() + 5 : $this->RevoteTime);

		if(in_array($_SERVER['REMOTE_ADDR'], array_keys($this->Revote))){
			if(($revotetime + $this->Revote[$_SERVER['REMOTE_ADDR']]) > time()){
				$revoteua = unserialize($this->RevoteUserAgent);
				if($this->UserAgent){
					if(!is_array($revoteua)){
						return self::ERROR_REVOTE;
					}
					if(isset($revoteua[$_SERVER['REMOTE_ADDR']]) && is_array($revoteua[$_SERVER['REMOTE_ADDR']])){
						if(in_array($_SERVER['HTTP_USER_AGENT'], $revoteua[$_SERVER['REMOTE_ADDR']])){
							return self::ERROR_REVOTE;
						} else{
							if($this->FallbackUserID){
								return $this->canVoteUserID();
							} else
								return self::SUCCESS;
						}
					}
				}

				return self::ERROR_REVOTE;
			}
			else{
				if($this->FallbackUserID){
					return $this->canVoteUserID();
				} else
					return self::SUCCESS;
			}
		} else{
			if($this->FallbackUserID){
				return $this->canVoteUserID();
			} else
				return self::SUCCESS;
		}
	}

	function canVoteUserID(){
		$userid = (defined('CUSTOMER_TABLE') && isset($_SESSION['webuser']['registered']) && isset($_SESSION['webuser']['ID']) && $_SESSION['webuser']['registered'] && $_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : -1;

		if(!$this->LogDB || ($userid <= 0)){
			return self::SUCCESS;
		} else{
			$testtime = ($this->RevoteTime < 0 ? 0 : time() - $this->RevoteTime);

			if(f('SELECT 1 AS a FROM `' . VOTING_LOG_TABLE . '` WHERE `' . VOTING_LOG_TABLE . '`.`voting` = ' . $this->ID . ' AND `' . VOTING_LOG_TABLE . '`.`userid` = ' . $userid . ' AND `' . VOTING_LOG_TABLE . '`.`time` > ' . $testtime, 'a', $this->db) == '1'){
				return self::ERROR_REVOTE;
			}
			return self::SUCCESS;
		}
	}

	function isActive(){
		if(!$this->Active)
			return false;
		if($this->ActiveTime == 0)
			return true;
		if(time() > $this->Valid)
			return false;
		return true;
	}

	function cookieDisabled(){
		return !(isset($_SESSION['_we_cookie_']));
	}

	function isBlackIP(){
		if(!$this->RestrictIP){
			return false;
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		foreach($this->BlackList as $fip){
			$reg = str_replace('*', '[0-9]+', $fip);
			if(preg_match('/' . $reg . '/', $ip)){
				return true;
			}
		}
		return false;
	}

	function resetIpData(){
		$this->Revote = '';
		$this->RevoteUserAgent = '';
		$this->saveField('Revote');
		$this->saveField('RevoteUserAgent');
	}

	function logVoting($status, $votingsession, $answer, $answertext, $successor, $additionalfields = ''){
		if($this->LogDB){
			$this->logVotingDB($status, $votingsession, $answer, $answertext, $successor, $additionalfields);
		} else{
			$this->LogData = unserialize($this->LogData);
			if(!is_array($this->LogData)){
				$this->LogData = array();
			}
			$this->LogData[] = array(
				'time' => time(),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'agent' => $_SERVER['HTTP_USER_AGENT'],
				'cookie' => $this->cookieDisabled() ? 0 : 1,
				'fallback' => $this->FallbackActive,
				'status' => $status
			);
			$this->saveField('LogData', true);
		}
	}

	function deleteLogData(){
		if($this->IsFolder){
			$this->deleteGroupLogData();
			return true;
		} else{
			if($this->LogDB){
				$this->deleteLogDataDB();
				$this->LogData = '';
				$this->saveField('LogData', false);
			} else{
				$this->LogData = '';
				$this->saveField('LogData', false);
				$this->LogDB = true;
			}
			return true;
		}
	}

	function deleteGroupLogData(){
		$this->db->query('SELECT ID FROM ' . VOTING_TABLE . " WHERE `Path` LIKE '" . $this->Path . "%'");
		while($this->db->next_record()) {
			$child = new weVoting($this->db->f('ID'));
			$child->deleteLogDataDB();
		}
		return true;
	}

	/* ================================= NEW FUNCTIONS FOR LOGGING TO VOTING_LOG_TABLE ================================= */
	/**
	 * @abstract new functions for logging votings to a separate database table
	 * @internal new logging mode logs votings to a new table called "tblvotinglog" instead of saving them
	 * 			to the field "LogData" as a serialized array. So $this->LogData contains an array if the new mode
	 * 			is used (for newly created votings). For existing votings using the old logging style nothing has changed
	 * 			and the variable will still contain a string with the serialized logging data array from tblvoting.LogData
	 * 			In the first case the variable $this->LogDB will be set to true, else it will stay false
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 05.05.2008
	 */

	/**
	 * fetches all log entries for current voting from database
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 02.05.2008
	 */
	function loadDB($id = '0'){

		if($this->IsFolder){
			$logQuery = 'SELECT A.*, B.* FROM `' . VOTING_TABLE . '` A, `' . VOTING_LOG_TABLE . "` B WHERE A.Path LIKE '" . $this->Path . "%' AND A.IsFolder = '0' AND A.ID = B.voting ORDER BY B.time";
		} else{
			$logQuery = 'SELECT * FROM `' . VOTING_LOG_TABLE . '` WHERE `' . VOTING_LOG_TABLE . '`.`voting` = ' . $id . ' ORDER BY time';
		}

		$this->db->query($logQuery);
		$this->LogData = array();
		while($this->db->next_record()) {
			$this->LogData[] = array('votingsession' => $this->db->f('votingsession'), 'voting' => $this->db->f('voting'),
				'time' => $this->db->f('time'),
				'ip' => $this->db->f('ip'),
				'agent' => $this->db->f('agent'),
				'userid' => $this->db->f('userid'),
				'cookie' => $this->db->f('cookie'),
				'fallback' => $this->db->f('fallback'),
				'status' => $this->db->f('status'),
				'answer' => $this->db->f('answer'),
				'answertext' => $this->db->f('answertext'),
				'successor' => $this->db->f('successor'),
				'additionalfields' => $this->db->f('additionalfields'),
			);
		}
		return $this->LogData;
	}

	/**
	 * writes a single entry to the voting log table VOTING_LOG_TABLE
	 * @return boolean success or failure of this operation
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 02.05.2008
	 */
	function logVotingDB($status = NULL, $votingsession = NULL, $answer = NULL, $answertext = NULL, $successor = NULL, $additionalfields = NULL){
		if(is_null($status))
			$status = 0;
		if(is_null($votingsession))
			$votingsession = 0;
		if(is_null($answer))
			$answer = -1;
		if(is_null($answertext))
			$answertext = '';
		if(is_null($successor))
			$successor = '';
		if(is_null($additionalfields))
			$additionalfields = '';
		$_cookieStatus = $this->cookieDisabled() ? 0 : 1;
		$userid = (defined("CUSTOMER_TABLE") && isset($_SESSION["webuser"]["registered"]) && isset($_SESSION["webuser"]["ID"]) && $_SESSION["webuser"]["registered"] && $_SESSION["webuser"]["ID"] ?
				$_SESSION["webuser"]["ID"] : 0);
		$this->db->query('INSERT INTO `' . VOTING_LOG_TABLE . '` SET ' . we_database_base::arraySetter(array(
				'votingsession' => $votingsession,
				'voting' => $this->ID,
				'time' => time(),
				'ip' => $_SERVER['REMOTE_ADDR'],
				'agent' => $_SERVER['HTTP_USER_AGENT'],
				'userid' => $userid,
				'cookie' => $_cookieStatus,
				'fallback' => $this->FallbackActive,
				'answer' => $answer,
				'answertext' => $answertext,
				'successor' => $successor,
				'additionalfields' => $additionalfields,
				'status' => $status,
			)));

		return true;
	}

	/**
	 * deletes all log entries for a given voting
	 * @return boolean success or failure of this operation
	 * @author Alexander Lindenstruth
	 * @since 5.1.1.2 - 02.05.2008
	 */
	function deleteLogDataDB(){
		$this->db->query('DELETE FROM `' . VOTING_LOG_TABLE . '` WHERE `' . VOTING_LOG_TABLE . '`.`voting` = ' . $this->ID);
		return true;
	}

	/**
	 * switches from storing LogData in table VOTING_TABLE to table VOTING_LOG_TABLE
	 * @return boolean success or failure of this operation
	 * @author Dr. Armin Schulz
	 * @since 6.1.0.2 - 019.09.2010
	 */
	function switchToLogDataDB(){

		$LogData = @unserialize($this->LogData);
		if(is_array($LogData) && !empty($LogData)){
			foreach($LogData as $ld){
				$this->db->query('INSERT INTO `' . VOTING_LOG_TABLE . '` SET ' . we_database_base::arraySetter(array(
						'votingsession' => '',
						'voting' => $this->ID,
						'time' => $ld['time'],
						'ip' => $ld['ip'],
						'agent' => $ld['agent'],
						'userid' => 0,
						'cookie' => $ld['cookie'],
						'fallback' => $ld['fallback'],
						'answer' => '',
						'answertext' => '',
						'successor' => '',
						'additionalfields' => '',
						'status' . $ld['status'],
					)));
			}
			$this->LogData = '';
			$this->saveField('LogData', false);
			$this->LogDB = true;
			return true;
		}
	}

}

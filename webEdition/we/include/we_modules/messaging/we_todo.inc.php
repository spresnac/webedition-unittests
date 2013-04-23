<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
include_once(WE_MESSAGING_MODULE_PATH . "messaging_std.inc.php");
include_once(WE_MESSAGING_MODULE_PATH . 'we_conf_messaging.inc.php');
/* todo object class */

class we_todo extends we_msg_proto{

	/* Flag which is set when the file is not new */
	var $selected_message = array();
	var $selected_set = array();
	var $search_fields = array('m.headerSubject', 'm.headerCreator', 'm.MessageText');
	var $search_folder_ids = array();
	var $sortfield = 'm.headerDeadline';
	var $last_sortfield = '';
	var $sortorder = 'desc';
	var $ids_selected = array();
	var $available_folders = array();
	var $sql_class_nr = 2;
	var $Short_Description = 'webEdition TODO';
	var $table = MSG_TODO_TABLE;
	var $view_class = 'todo';
	var $sf2sqlfields = array(
		'm.headerSubject' => array('hdrs', 'Subject'),
		'm.headerDate' => array('hdrs', 'Date'),
		'm.headerDeadline' => array('hdrs', 'Deadline'),
		'm.headerCreator' => array('hdrs', 'Creator'),
		'm.seenStatus' => array('hdrs', 'seenStatus'),
		'm.MessageText' => array('body', 'MessageText')
	);
	var $so2sqlso = array(
		'desc' => 'asc',
		'asc' => 'desc');

	function __construct(){
		parent::__construct();
		$this->Short_Description = g_l('modules_messaging', "[we_todo]");
		$this->Name = 'todo_' . md5(uniqid(__FILE__, true));
		$this->persistent_slots = array('ClassName', 'Name', 'ID', 'Folder_ID', 'selected_message', 'sortorder', 'last_sortfield', 'available_folders', 'search_folder_ids', 'search_fields', 'default_folders');
	}

	function init($sessDat = ''){
		$init_folders = array();

		if($sessDat){
			$this->initSessionDat($sessDat);
		}

		foreach($this->default_folders as $id => $fid)
			if($fid == -1){
				$init_folders[] = $id;
			}

		if(!empty($init_folders)){
			$this->DB_WE->query('SELECT ID, obj_type FROM ' . MSG_FOLDERS_TABLE . ' WHERE UserID=' . intval($this->userid) . ' AND msg_type=' . $this->sql_class_nr . ' AND (obj_type=' . $this->DB_WE->escape(implode(' OR obj_type=', $init_folders)) . ')');
			while($this->DB_WE->next_record()) {
				$this->default_folders[$this->DB_WE->f('obj_type')] = $this->DB_WE->f('ID');
			}
		}
	}

	function initSessionDat($sessDat){
		if($sessDat){
			/* move sizeof out of loop */
			foreach($this->persistent_slots as $pers){
				if(isset($sessDat[0][$pers])){
					$this->{$pers} = $sessDat[0][$pers];
				}
			}

			if(isset($sessDat[1])){
				$this->elements = $sessDat[1];
			}
		}
	}

	function saveInSession(&$save){
		$save = array();
		$save[0] = array();
		foreach($this->persistent_slots as $pers){
			$save[0][$pers] = $this->{$pers};
		}
		$save[1] = isset($this->elements) ? $this->elements : "";
	}

	/* Methods dealing with USER_TABLE and other userstuff */

	function userid_to_username($id, $db = ''){
		$db = $db ? $db : new DB_WE();
		$user = f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($id), 'username', $db);
		return $user ? $user : g_l('modules_messaging', '[userid_not_found]');
	}

	function username_to_userid($username, $db = ''){
		$db = $db ? $db : new DB_WE();
		$id = f('SELECT ID FROM ' . USER_TABLE . ' WHERE username="' . $db->escape($username) . '"', 'ID', $db);
		return $id ? $id : -1;
	}

	/* Getters And Setters */

	function get_newmsg_count(){
		return intval(f('SELECT COUNT(1) AS c FROM ' . $this->table . ' WHERE (seenStatus & ' . we_msg_proto::STATUS_READ . '=0) AND obj_type=' . we_msg_proto::TODO_NR . ' AND msg_type=' . $this->sql_class_nr . ' AND ParentID=' . $this->default_folders[we_msg_proto::FOLDER_INBOX] . ' AND UserID=' . intval($this->userid), 'c', $this->DB_WE));
	}

	function get_count($folder_id){
		$cnt = f('SELECT COUNT(1) AS c FROM ' . $this->DB_WE->escape($this->table) . ' WHERE ParentID=' . intval($folder_id) . ' AND obj_type=' . we_msg_proto::TODO_NR . ' AND msg_type=' . $this->sql_class_nr . ' AND UserID=' . intval($this->userid), 'c', $this->DB_WE);
		return $cnt === '' ? -1 : $cnt;
	}

	/* 	function get_userids_by_nick($nick){
	  $ret_ids = array();

	  $db2 = new DB_WE();
	  $db2->query('SELECT ID FROM ' . USER_TABLE . ' WHERE username LIKE "%' . $db2->escape($nick) . '%" OR First LIKE "%' . $db2->escape($nick) . '%" OR Second LIKE "%' . $db2->escape($nick) . '%"');
	  while($db2->next_record())
	  $ret_ids[] = $db2->f('ID');

	  return $ret_ids;
	  } */

	function format_from_line($userid){
		$tmp = getHash('SELECT First, Second, username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userid), new DB_WE());
		return $tmp['First'] . ' ' . $tmp['Second'] . ' (' . $tmp['username'] . ')';
	}

	function create_folder($name, $parent, $aid = -1){
		return parent::create_folder($name, $parent, $aid);
	}

	/* get subtree starting with node $id */

	function &get_f_children($id){
		$fids = array();

		$this->DB_WE->query('SELECT ID FROM ' . $this->folder_tbl . ' WHERE ParentID=' . intval($id) . ' AND UserID=' . $this->userid);
		while($this->DB_WE->next_record()) {
			$fids[] = $this->DB_WE->f('ID');
		}

		foreach($fids as $fid){
			$fids = array_merge($fids, $this->get_f_children($fid));
		}

		return $fids;
	}

	function delete_items(&$i_headers){
		if(empty($i_headers)){
			return -1;
		}

		$cond = '';
		foreach($i_headers as $ih){
			$cond .= 'ID=' . intval($ih['_ID']) . ' OR ';
		}

		$cond = substr($cond, 0, -4);

		$this->DB_WE->query('DELETE FROM ' . $this->DB_WE->escape($this->table) . ' WHERE (' . $this->DB_WE->escape($cond) . ') AND obj_type=' . we_msg_proto::TODO_NR . " AND UserID=" . $this->userid);

		return 1;
	}

	function history_update($id, $userid, $fromuserid, $comment, $action, $status = 'NULL'){
		return $this->DB_WE->query('INSERT INTO ' . MSG_TODOHISTORY_TABLE . ' (ParentID, UserID, fromUserID, Comment, Created, action, status) VALUES (' . intval($id) . ', ' . intval($userid) . ', ' . $this->DB_WE->escape($fromuserid) . ', "' . $this->DB_WE->escape($comment) . '", UNIX_TIMESTAMP(), ' . $this->DB_WE->escape($action) . ', ' . $this->DB_WE->escape($status) . ')');
	}

	function add_comment(){
		return ($this->history_update($id, $this->userid, $this->userid, $comment, we_msg_proto::ACTION_COMMENT) == 1);
	}

	function &update_status(&$data, &$msg, $userid = ''){
		$ret = array();
		$ret['changed'] = 0;
		$set_query = array();

		if($userid == ''){
			$userid = $this->userid;
		}

		if(empty($data)){
			$ret['msg'] = g_l('modules_messaging', '[todo_no_changes]');
			return $ret;
		}

		if(empty($msg)){
			$ret['msg'] = g_l('modules_messaging', '[todo_none_selected]');
			return $ret;
		}

		if(isset($data['todo_comment'])){
			//use current assignee instead of userid
			if($this->history_update($msg['_ID'], $userid, $userid, $data['todo_comment'], we_msg_proto::ACTION_COMMENT)){
				$ret['msg'] = g_l('modules_messaging', '[update_successful]');
				$ret['changed'] = 1;
			} else{
				$ret['msg'] = g_l('modules_messaging', '[error_occured]');
				$ret['err'] = 1;
			}
		}

		if(isset($data['todo_status'])){
			if(!is_numeric($data['todo_status']) || ($data['todo_status'] < 0)){
				$ret['msg'] = g_l('modules_messaging', '[todo_status_inv_input]');
				$ret['err'] = 1;
				return $ret;
			}

			$set_query['headerStatus'] = $data['todo_status'];
			if($data['todo_status'] >= 100){
				if($this->default_folders[we_msg_proto::FOLDER_DONE] < 0){
					$ret['msg'] = g_l('modules_messaging', '[todo_move_error]') . ': ' . g_l('modules_messaging', '[no_done_folder]');
					return $ret;
				} else{
					$set_query['ParentID'] = $this->default_folders[we_msg_proto::FOLDER_DONE];
				}
			} else{
				if(f('SELECT ParentID FROM ' . $this->table . ' WHERE ID=' . $msg['_ID'], 'ParentID', $this->DB_WE) == $this->default_folders[we_msg_proto::FOLDER_DONE]){
					$set_query['ParentID'] = $this->default_folders[we_msg_proto::FOLDER_INBOX];
				}
			}
		}

		if(isset($data['deadline'])){
			$set_query['headerDeadline'] = $data['deadline'];
		}

		if(isset($data['todo_priority'])){
			$set_query['Priority'] = $data['todo_priority'];
		}

		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET ' . we_database_base::arraySetter($set_query) . ' WHERE ID=' . intval($msg['_ID']));
		$ret['msg'] = g_l('modules_messaging', '[update_successful]');
		$ret['changed'] = 1;
		$ret['err'] = 0;

		return $ret;
	}

	/* Forward is actually "reassign", so no copy is made */

	function forward(&$rcpts, &$data, &$msg){
		$results = array();
		$results['err'] = array();
		$results['ok'] = array();
		$results['failed'] = array();
		$in_folder = '';

		$rcpt = $rcpts[0];

		if(($userid = $this->username_to_userid($rcpt, $this->DB_WE)) == -1){
			$results['err'][] = g_l('modules_messaging', '[username_not_found]');
			$results['failed'][] = $rcpt;
			return $results;
		}

		$id = f('SELECT ID FROM ' . $this->DB_WE->escape($this->table) . ' WHERE Properties=' . we_msg_proto::TODO_PROP_IMMOVABLE . ' AND ID=' . intval($msg['int_hdrs']['_ID']), 'ID', $this->DB_WE);
		if($id == $msg['int_hdrs']['_ID']){
			$results['err'][] = g_l('modules_messaging', '[todo_no_forward]');
			$results['failed'][] = $this->userid;
			return $results;
		}

		$in_folder = f('SELECT ID FROM ' . $this->folder_tbl . ' WHERE obj_type=' . we_msg_proto::FOLDER_INBOX . ' AND msg_type=' . $this->sql_class_nr . ' AND UserID=' . intval($userid), 'ID', $this->DB_WE);
		if($in_folder == ''){
			$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
			$results['failed'][] = $rcpt;
			return $results;
		}

		if($this->history_update($msg['int_hdrs']['_ID'], $userid, $this->userid, $data['body'], we_msg_proto::ACTION_FORWARD) == 1){
			$this->DB_WE->query('UPDATE ' . $this->table . " SET ParentID=$in_folder, UserID=" . intval($userid) . ', seenStatus=0, headerAssigner=' . intval($this->userid) . " WHERE ID=" . intval($msg['int_hdrs']['_ID']) . ' AND UserID=' . intval($this->userid));
			$results['ok'][] = $rcpt;
		} else{
			$results['err'][] = g_l('modules_messaging', '[todo_err_history_update]');
			$results['failed'][] = $rcpt;
		}

		return $results;
	}

	function reject(&$msg, &$data){
		$results = array();
		$results['err'] = array();
		$results['ok'] = array();
		$results['failed'] = array();


		$rej_folder = f('SELECT ID FROM ' . MSG_FOLDERS_TABLE . ' WHERE obj_type=' . we_msg_proto::FOLDER_REJECT . ' AND UserID=' . intval($msg['int_hdrs']['_from_userid']), 'ID', $this->DB_WE);
		if(empty($rej_folder)){
			$results['err'][] = g_l('modules_messaging', '[no_reject_folder]');
			$results['failed'][] = $this->userid_to_username($msg['int_hdrs']['_from_userid'], $this->DB_WE);
			return $results;
		}

		$tmpId = f('SELECT ID FROM ' . $this->DB_WE->escape($this->table) . ' WHERE Properties=' . we_msg_proto::TODO_PROP_IMMOVABLE . ' AND ID=' . intval($msg['int_hdrs']['_ID']), 'ID', $this->DB_WE);
		if($tmpId == $msg['int_hdrs']['_ID']){
			$results['err'][] = g_l('modules_messaging', '[todo_no_reject]');
			$results['failed'][] = $this->userid_to_username($msg['int_hdrs']['_from_userid'], $this->DB_WE);
			return $results;
		}

		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET UserID=' . intval($msg['int_hdrs']['_from_userid']) . ', ParentID=' . intval($rej_folder) . ' WHERE ID=' . intval($msg['int_hdrs']['_ID']));
		$this->history_update($msg['int_hdrs']['_ID'], $msg['int_hdrs']['_from_userid'], $this->userid, $data['body'], we_msg_proto::ACTION_REJECT);

		$results['err'][] = '';
		$results['ok'][] = $this->userid_to_username($msg['int_hdrs']['_from_userid'], $this->DB_WE);

		return $results;
	}

	function clipboard_cut($items, $target_fid){
		if(empty($items)){
			return;
		}

		$id_str = 'ID=' . implode(', ID=', $items);
		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET ParentID=' . intval($target_fid) . ' WHERE (' . $this->DB_WE->escape($id_str) . ') AND UserID=' . intval($this->userid));

		return 1;
	}

	function clipboard_copy($items, $target_fid){
		$tmp_msgs = array();

		if(empty($items)){
			return;
		}

		foreach($items as $item){
			$tmp = array();
			$row = getHash('SELECT msg_type, obj_type, headerDate, headerSubject, headerCreator, headerAssigner, headerStatus, headerDeadline, Priority, Content_Type, MessageText, seenStatus, tag FROM ' . $this->DB_WE->escape($this->table) . " WHERE ID=" . intval($item) . " AND UserID=" . intval($this->userid), $this->DB_WE);
			$tmp['ParentID'] = $target_fid;
			$tmp['UserID'] = $this->userid;
			$tmp['msg_type'] = $row('msg_type');
			$tmp['obj_type'] = $row('obj_type');
			$tmp['headerDate'] = $row['headerDate'] != '' ? $row['headerDate'] : 'NULL';
			$tmp['headerSubject'] = $row['headerSubject'] != '' ? $row['headerSubject'] : 'NULL';
			$tmp['headerCreator'] = $row['headerCreator'] != '' ? $row['headerCreator'] : 'NULL';
			$tmp['headerAssigner'] = $row['headerAssigner'] != '' ? $row['headerAssigner'] : 'NULL';
			$tmp['headerStatus'] = $row['headerStatus'] != '' ? $row['headerStatus'] : 'NULL';
			$tmp['headerDeadline'] = $row['headerDeadline'] != '' ? $row['headerDeadline'] : 'NULL';
			$tmp['Priority'] = $row['Priority'] != '' ? $row['Priority'] : 'NULL';
			$tmp['MessageText'] = $row['MessageText'];
			$tmp['Content_Type'] = $row['Content_Type'];
			$tmp['seenStatus'] = intval($row['seenStatus']);
			$tmp['tag'] = $row['tag'] != '' ? $row['tag'] : '';

			$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->table) . ' ' . we_database_base::arraySetter($tmp));
		}

		return 1;
	}

	function &send(&$rcpts, &$data){
		$results = array(
			'err' => array(),
			'ok' => array(),
			'failed' => array(),
		);
		$db = new DB_WE();

		foreach($rcpts as $rcpt){
			$in_folder = '';
			//FIXME: Put this out of the loop (the select statement)
			if(($userid = $this->username_to_userid($rcpt, $db)) == -1){
				$results['err'][] = "Username '$rcpt' existiert nicht'";
				$results['failed'][] = $rcpt;
				continue;
			}

			$in_folder = f('SELECT ID FROM ' . $this->folder_tbl . ' WHERE obj_type=' . we_msg_proto::FOLDER_INBOX . ' AND msg_type=' . $this->sql_class_nr . ' AND UserID=' . intval($userid), 'ID', $this->DB_WE);
			if($in_folder == ''){
				/* Create default Folders for target user */
				include_once(WE_MESSAGING_MODULE_PATH . 'messaging_interfaces.inc.php');
				if(msg_create_folders($userid) == 1){
					$in_folder = f('SELECT ID FROM ' . $this->folder_tbl . ' WHERE obj_type=' . we_msg_proto::FOLDER_INBOX . ' AND msg_type=' . $this->sql_class_nr . ' AND UserID=' . intval($userid), 'ID', $this->DB_WE);
					if($in_folder == ''){
						$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
						$results['failed'][] = $rcpt;
						continue;
					}
				} else{
					$results['err'][] = g_l('modules_messaging', '[no_inbox_folder]');
					$results['failed'][] = $rcpt;
					continue;
				}
			}

			$this->DB_WE->query('INSERT INTO ' . $this->table . ' SET ' . we_database_base::arraySetter(array(
					'ParentID' => intval($in_folder),
					'UserID' => intval($userid),
					'msg_type' => $this->sql_class_nr,
					'obj_type' => we_msg_proto::TODO_NR,
					'headerDate' => 'UNIX_TIMESTAMP()',
					'headerSubject' => $data['subject'],
					'headerCreator' => intval(intval($this->userid) ? $this->userid : $userid),
					'headerStatus' => 0,
					'headerDeadline' => $data['deadline'],
					'Properties' => we_msg_proto::TODO_PROP_NONE,
					'MessageText' => $data['body'],
					'seenStatus' => 0,
					'Priority' => empty($data['priority']) ? 'NULL' : $data['priority'],
					'Content_Type' => empty($data['Content_Type']) ? 'NULL' : $data['Content_Type']
				)));

			$results['id'] = $this->DB_WE->getInsertId();
			$results['ok'][] = $rcpt;
		}

		return $results;
	}

	function get_msg_set(&$criteria){
		$sfield_cond = '';

		if(isset($criteria['search_fields'])){

			$arr = array('hdrs', 'From');
			$sf_uoff = arr_offset_arraysearch($arr, $criteria['search_fields']);

			if($sf_uoff > -1){
				$sfield_cond .= 'u.username LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR
				u.First LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR
				u.Second LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR ';

				array_splice($criteria['search_fields'], $sf_uoff, 1);
			}

			foreach($criteria['search_fields'] as $sf){
				$sfield_cond .= array_key_by_val($sf, $this->sf2sqlfields) . ' LIKE "%' . $this->DB_WE->escape($criteria['searchterm']) . '%" OR ';
			}

			$sfield_cond = substr($sfield_cond, 0, -4);

			$folders_cond = implode(' OR m.ParentID=', $criteria['search_folder_ids']);
		} else if(isset($criteria['folder_id'])){
			$folders_cond = $criteria['folder_id'];

			if($this->cached['sortfield'] != 1 || $this->cached['sortorder'] != 1){
				$this->init_sortstuff($criteria['folder_id']);
			}

			$this->Folder_ID = $criteria['folder_id'];
		}

		if(isset($criteria['message_ids'])){
			$message_ids_cond = implode(' OR m.ID=', $criteria['message_ids']);
		}

		$this->selected_set = array();
		$query = 'SELECT m.ID, m.ParentID, m.headerDeadline, m.headerSubject, m.headerCreator, m.Priority, m.seenStatus, m.headerStatus, u.username
		FROM ' . $this->table . ' as m, ' . USER_TABLE . ' as u
		WHERE ((m.msg_type=' . $this->sql_class_nr . ' AND m.obj_type=' . we_msg_proto::TODO_NR . ') ' . ($sfield_cond == '' ? '' : " AND ($sfield_cond)") . ($folders_cond == '' ? '' : " AND (m.ParentID=$folders_cond)") . ( (!isset($message_ids_cond) || $message_ids_cond == '') ? '' : " AND (m.ID=$message_ids_cond)") . ") AND m.UserID=" . $this->userid . " AND m.headerCreator=u.ID
		ORDER BY " . $this->sortfield . ' ' . $this->so2sqlso[$this->sortorder];
		$this->DB_WE->query($query);

		$i = isset($criteria['start_id']) ? $criteria['start_id'] + 1 : 0;

		$seen_ids = array();

		while($this->DB_WE->next_record()) {
			if(!($this->DB_WE->f('seenStatus') & we_msg_proto::STATUS_SEEN)){
				$seen_ids[] = $this->DB_WE->f('ID');
			}

			$this->selected_set[] =
				array('ID' => $i++,
					'hdrs' => array('Deadline' => $this->DB_WE->f('headerDeadline'),
						'Subject' => $this->DB_WE->f('headerSubject'),
						'Creator' => $this->DB_WE->f('username'),
						'Priority' => $this->DB_WE->f('Priority'),
						'seenStatus' => $this->DB_WE->f('seenStatus'),
						'status' => $this->DB_WE->f('headerStatus'),
						'ClassName' => $this->ClassName),
					'int_hdrs' => array('_from_userid' => $this->DB_WE->f('headerCreator'),
						'_ParentID' => $this->DB_WE->f('ParentID'),
						'_ID' => $this->DB_WE->f('ID')));
		}

		/* mark selected_set messages as seen */
		if(!empty($seen_ids)){
			$query = 'UPDATE ' . $this->DB_WE->escape($this->table) . ' SET seenStatus=(seenStatus | ' . we_msg_proto::STATUS_SEEN . ') WHERE ID IN (' . implode(',', $seen_ids) . ') AND UserID=' . intval($this->userid);
			$this->DB_WE->query($query);
		}

		return $this->selected_set;
	}

	function &retrieve_items(&$int_hdrs){
		$ret = array();
		$i = 0;

		if(empty($int_hdrs)){
			return $ret;
		}

		foreach($int_hdrs as $ih){
			if(!isset($id_str)){
				$id_str = "";
			}
			$id_str .= 'm.ID=' . intval($ih['_ID']);
		}

		$this->DB_WE->query('SELECT m.ID, m.headerDate, m.headerSubject, m.headerCreator, m.headerAssigner, m.headerStatus, m.headerDeadline, m.Priority, m.MessageText, m.Content_Type, m.seenStatus, u.username, u.First, u.Second FROM ' . $this->table . " as m, " . USER_TABLE . " as u WHERE ($id_str) AND u.ID=m.headerCreator AND m.UserID=" . intval($this->userid));

		$db2 = new DB_WE();

		$read_ids = array();

		while($this->DB_WE->next_record()) {
			if(!($this->DB_WE->f('seenStatus') && we_msg_proto::STATUS_READ)){
				$read_ids[] = $this->DB_WE->f('ID');
			}

			$history = array();
			/* FIXME: get the ids; use one query outside of the loop; */
			$db2->query('SELECT u.username, t.Comment, t.Created, t.action, t.fromUserID FROM ' . MSG_TODOHISTORY_TABLE . ' as t, ' . USER_TABLE . ' as u WHERE t.ParentID=' . $this->DB_WE->f('ID') . ' AND t.UserID=u.ID ORDER BY Created');
			while($db2->next_record()) {
				$history[] = array(
					'username' => $db2->f('username'),
					'from_userid' => $db2->f('fromUserID'),
					'date' => $db2->f('Created'),
					'action' => $db2->f('action'),
					'comment' => $db2->f('Comment'));
			}

			$from = $this->DB_WE->f('First') . ' ' . $this->DB_WE->f('Second') . ' (' . $this->DB_WE->f('username') . ')';
			$ret[] = array(
				'ID' => $i++,
				'hdrs' => array(
					'Date' => $this->DB_WE->f('headerDate'),
					'Deadline' => $this->DB_WE->f('headerDeadline'),
					'Subject' => $this->DB_WE->f('headerSubject'),
					'From' => $from,
					'Assigner' => empty($this->DB_WE->Record['headerAssigner']) ? $from : $this->format_from_line($this->DB_WE->Record['headerAssigner']),
					'status' => $this->DB_WE->f('headerStatus'),
					'Priority' => $this->DB_WE->f('Priority'),
					'seenStatus' => $this->DB_WE->f('seenStatus'),
					'Content_Type' => $this->DB_WE->f('Content_Type'),
					'ClassName' => $this->ClassName),
				'int_hdrs' => array(
					'_from_userid' => $this->DB_WE->f('headerCreator'),
					'_ID' => $this->DB_WE->f('ID'),
					'_reply_to' => $this->DB_WE->f('username')),
				'body' => array(
					'MessageText' => $this->DB_WE->f('MessageText'),
					'History' => $history));
		}

		if(!empty($read_ids)){
			$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->table) . ' SET seenStatus=(seenStatus | ' . we_msg_proto::STATUS_READ . ') WHERE ID IN (' . implode(',', $read_ids) . ') AND UserID=' . $this->userid);
		}

		return $ret;
	}

}
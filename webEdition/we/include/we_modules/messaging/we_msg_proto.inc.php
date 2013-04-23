<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
/* message protocol root class */
class we_msg_proto extends we_class{

	const FOLDER_DONE = 13;
	const FOLDER_REJECT = 11;
	const FOLDER_TRASH = 9;
	const FOLDER_SENT = 5;
	const FOLDER_INBOX = 3;
	const FOLDER_NR = 1;
	const MESSAGE_NR = 2;
	const TODO_NR = 4;
	const STATUS_SEEN = 1;
	const STATUS_READ = 2;
	const CLIPBOARD_COPY = 0;
	const CLIPBOARD_CUT = 1;

	/* History action codes */
	const ACTION_COMMENT = 1;
	const ACTION_FORWARD = 2;
	const ACTION_REJECT = 3;
	const ACTION_DONE = 4;

	/* ToDO properties */
	const TODO_PROP_NONE = 0;
	const TODO_PROP_IMMOVABLE = 1;


	/* Flag which is set when the file is not new */
	var $Folder_ID = -1;
	var $userid = -1;
	var $username = '';
	var $selected_message = array();
	var $selected_set = array();
	var $search_ids = array();
	var $search_fields = array('headerSubject', 'headerFrom', 'MessageText');
	var $search_folder_ids = array();
	var $sortfield = 'headerDate';
	var $last_sortfield = '';
	var $sortorder = 'desc';
	var $ids_selected = array();
	var $available_folders = array();
	var $cached = array(
		'sortorder' => 0,
		'sortfield' => 0);
//    var $got_sortstuff_from_db = 0;
	var $update_interval = 10;
	var $default_folders = array(
		self::FOLDER_DONE => -1,
		self::FOLDER_REJECT => -1,
		self::FOLDER_TRASH => -1,
		self::FOLDER_SENT => -1,
		self::FOLDER_INBOX => -1);
	var $table = MESSAGES_TABLE;
	var $folder_tbl = MSG_FOLDERS_TABLE;

	function __construct(){
		parent::__construct();
		$this->Name = 'msg_proto_' . md5(uniqid(__FILE__, true));
		$this->persistent_slots = array('ClassName', 'Name', 'ID', 'Table', 'Folder_ID', 'selected_message', 'sortorder', 'last_sortfield', 'search_ids', 'available_folders', 'search_folder_ids', 'search_fields', 'cached');
	}

	/* Getters And Setters */

	function get_sortitem(){
		if(empty($this->sortfield)){
			$this->init_sortstuff($this->Folder_ID);
		}

		return $this->sf2si[$this->sortfield];
	}

	function get_entries_selected(){
		if(empty($this->ids_selected)){
			return '';
		}

		return '"' . implode('","', $this->ids_selected) . '"';
	}

	function set_entries_selected($entrsel){
		$this->ids_selected = explode(',', $entrsel);
	}

	function reset_entries_selected(){
		$this->ids_selected = array();
	}

	function set_login_data($userid, $username){
		$this->userid = $userid;
		$this->username = $username;
	}

	function get_sortfield(){
		if($this->cached['sortfield'] != 1){
			$this->init_sortstuff($this->Folder_ID);
		}

		return $this->sortfield;
	}

	function get_sortorder(){
		if($this->cached['sortorder'] != 1){
			$this->init_sortstuff($this->Folder_ID);
		}

		return $this->sortorder;
	}

	/* Get all values for $key in an array of hashes */
	/* params: key, hash */
	/* returns: array of the values for the key */

	function array_get_kvals($key, $hash){
		$ret_arr = array();

		foreach($hash as $elem){
			$ret_arr[] = $elem[$key];
		}

		return $ret_arr;
	}

	function get_subfolder_count($id){
		$this->DB_WE->query('SELECT count(ID) as c FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ParentID=' . intval($id) . ' AND UserID=' . intval($this->userid));

		if($this->DB_WE->next_record() && $this->DB_WE->f('c') > 0){
			return $this->DB_WE->f('c');
		}

		return -1;
	}

	function set_search_settings($search_fields, $search_folder_ids){
		$this->search_fields = array();
		$this->search_folder_ids = array();

		if(isset($search_fields)){
			foreach($search_fields as $elem){
				if(!empty($this->si2sf[$elem])){
					$this->search_fields[] = $this->si2sf[$elem];
				}
			}
		}

		if(isset($search_folder_ids)){
			foreach($search_folder_ids as $elem){
				if(in_array($elem, $this->array_get_kvals('ID', $this->available_folders))){
					$this->search_folder_ids[] = $elem;
				}
			}
		}
	}

	/* Intialize the class. If $sessDat (array) is set, the class will be initialized from this array */

	function init($sessDat){
		if($sessDat){
			$this->initSessionDat($sessDat);
		}

		/* 	if (empty($this->available_folders))
		  $this->get_available_folders(); */
	}

	function initSessionDat($sessDat){
		if($sessDat){
			foreach($this->persistent_slots as $cur){
				if(isset($sessDat[0][$cur])){
					$this->{$cur} = $sessDat[0][$cur];
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

		foreach($this->persistent_slots as $cur){
			$save[0][$cur] = $this->{$cur};
		}

		$save[1] = isset($this->elements) ? $this->elements : array();
	}

	function get_available_folders(){
		$this->available_folders = array();

		$this->DB_WE->query('SELECT ID, ParentID, account_id, Name, obj_type FROM  ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE msg_type=' . intval($this->sql_class_nr) . ' AND UserID=' . intval($this->userid));
		//$this->DB_WE->query('SELECT ID, ParentID, account_id, Name, obj_type FROM  ' . $this->folder_tbl . ' WHERE msg_type=' . $this->sql_class_nr  . ' AND UserID=' . $this->userid);
		while($this->DB_WE->next_record()) {
			$this->available_folders[] = array('ID' => $this->DB_WE->f('ID'),
				'ParentID' => $this->DB_WE->f('ParentID'),
				'ClassName' => $this->ClassName,
				'account_id' => $this->DB_WE->f('account_id'),
				'obj_type' => $this->DB_WE->f('obj_type'),
				'view_class' => $this->view_class,
				'Name' => $this->DB_WE->f('Name'));
		}

		return $this->available_folders;
	}

	function create_folder($name, $parent){
		$this->DB_WE->query('INSERT INTO ' . $this->DB_WE->escape($this->folder_tbl) . ' (ID, ParentID, UserID, account_id, msg_type, obj_type, Name) VALUES (NULL, ' . intval($parent) . ', ' . intval($this->userid) . ', -1, ' . $this->sql_class_nr . ', ' . we_msg_proto::FOLDER_NR . ', "' . $this->DB_WE->escape($name) . '")');
		$this->DB_WE->query('SELECT LAST_INSERT_ID() as l');
		$this->DB_WE->next_record();

		return $this->DB_WE->f('l');
	}

	function modify_folder($fid, $folder_name, $parent_folder){
		if(!is_numeric($fid) || !is_numeric($parent_folder)){
			return -1;
		}

		$query = 'UPDATE ' . $this->DB_WE->escape($this->folder_tbl) . ' SET Name="' . $this->DB_WE->escape($folder_name) . '", ParentID=' . intval($parent_folder) . ' WHERE ID=' . intval($fid) . ' AND UserID=' . intval($this->userid);
		$this->DB_WE->query($query);
		return 1;
	}

	/* get subtree starting with node $id (only the folders) */

	function &get_f_children($id){
		$fids = array();

		$this->DB_WE->query('SELECT ID FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ParentID=' . intval($id) . ' AND UserID=' . intval($this->userid));
		while($this->DB_WE->next_record())
			$fids[] = $this->DB_WE->f('ID');

		foreach($fids as $fid)
			$fids = array_merge($fids, $this->get_f_children($fid));

		return $fids;
	}

	function delete_folders($f_arr){

		$ret = array();
		$ret["res"] = 0;
		$ret["ids"] = array();

		if(empty($f_arr)){
			return $ret;
		}

		$rm_folders = array();
		$rm_fids = $f_arr;
		$norm_folderds = array();

		foreach($f_arr as $id){
			$rm_fids = array_merge($rm_fids, $this->get_f_children($id));
		}

		$cond = '';
		foreach($rm_fids as $rf){
			$cond .= 'ID=' . intval($rf) . ' OR ';
		}
		$cond = substr($cond, 0, -4);

		$query = 'SELECT ID, Name, (Properties & ' . we_msg_proto::FOLDER_NR . ') as norm FROM ' . $this->DB_WE->escape($this->folder_tbl) . " WHERE ($cond) AND UserID=" . intval($this->userid);
		$this->DB_WE->query($query);
		while($this->DB_WE->next_record()) {
			if($this->DB_WE->f('norm') == 1){
				$norm_folders[] = $this->DB_WE->f('Name') . ' (ID=' . $this->DB_WE->f('ID') . ')';
			} else{
				$rm_folders[] = $this->DB_WE->f('ID');
			}
		}

		if(empty($rm_folders)){
			return $ret;
		} else{
			$query = 'DELETE FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE (ID=' . join(' OR ID=', $rm_folders) . ') AND UserID=' . intval($this->userid);
			$this->DB_WE->query($query);
		}

		$ret["res"] = 1;
		$ret["ids"] = $rm_folders;

		return $ret;
	}

	function cmp_asc($a, $b){
		if($a[$this->sortfield] == $b[$this->sortfield]){
			return 0;
		}

		return ($a[$this->sortfield] > $b[$this->sortfield] ? 1 : -1);
	}

	function cmp_desc($a, $b){
		if($a[$this->sortfield] == $b[$this->sortfield]){
			return 0;
		}

		return ($a[$this->sortfield] > $b[$this->sortfield] ? -1 : 1);
	}

	function sort_set(){
		if(!empty($this->selected_set)){
			if(($this->last_sortfield != $this->sortfield) || $this->sortorder != 'asc'){
				usort($this->selected_set, array($this, 'cmp_asc'));
				$this->sortorder = 'asc';
			} else{
				usort($this->selected_set, array($this, 'cmp_desc'));
				$this->sortorder = 'desc';
			}

			$this->last_sortfield = $this->sortfield;
		}
	}

	function save_sortstuff($id, $sortfield, $sortorder){
		$sortorder = $sortorder == 'asc' ? 'desc' : 'asc';

		$this->DB_WE->query('UPDATE ' . $this->DB_WE->escape($this->folder_tbl) . ' SET sortItem="' . $this->DB_WE->escape($sortfield) . '", sortOrder="' . $this->DB_WE->escape($sortorder) . '" WHERE ID=' . intval($id) . ' AND UserID=' . intval($this->userid));
	}

	function init_sortstuff($id){
		$this->DB_WE->query('SELECT sortItem, sortOrder FROM ' . $this->DB_WE->escape($this->folder_tbl) . ' WHERE ID=' . intval($id) . ' AND UserID=' . intval($this->userid));
		$this->DB_WE->next_record();

		if(($this->DB_WE->f('sortItem'))){
			$this->sortfield = $this->DB_WE->f('sortItem');
		}

		if(($this->DB_WE->f('sortOrder'))){
			$this->sortorder = ($this->DB_WE->f('sortOrder') == 'asc') ? 'desc' : 'asc';
		}

		$this->cached[] = 'sortfield';
		$this->cached[] = 'sortorder';
		//	$this->got_sortstuff_from_db = 1;
	}

}

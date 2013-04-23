CREATE TABLE ###TBLPREFIX###tblMsgFolders (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned default NULL,
  UserID int(11) unsigned NOT NULL default '0',
  account_id int(11) unsigned default NULL,
  msg_type tinyint(4) unsigned NOT NULL default '0',
  obj_type tinyint(4) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  sortItem varchar(255) default NULL,
  sortOrder varchar(5) default NULL,
  Properties int(10) unsigned default NULL,
  tag tinyint(4) unsigned default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders VALUES (1,0,1,NULL,1,3,'Messages',NULL,NULL,1,NULL);
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders VALUES (2,1,1,NULL,1,5,'Sent',NULL,NULL,1,NULL);
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders VALUES (3,0,1,NULL,2,3,'Task',NULL,NULL,1,NULL);
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders VALUES (4,3,1,NULL,2,13,'Done',NULL,NULL,1,NULL);
/* query separator */
INSERT IGNORE INTO ###TBLPREFIX###tblMsgFolders VALUES (5,3,1,NULL,2,11,'rejected',NULL,NULL,1,NULL);

CREATE TABLE ###TBLPREFIX###tblMsgAccounts (
  ID int(11) unsigned NOT NULL auto_increment,
  UserID int(11) unsigned default NULL,
  name varchar(255) NOT NULL default '',
  msg_type int(11) unsigned default NULL,
  deletable tinyint(4) unsigned NOT NULL default '0',
  uri varchar(255) default NULL,
  `user` varchar(255) default NULL,
  pass varchar(255) default NULL,
  update_interval smallint(5) unsigned NOT NULL default '0',
  ext varchar(255) default NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM;

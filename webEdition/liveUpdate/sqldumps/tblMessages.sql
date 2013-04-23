CREATE TABLE ###TBLPREFIX###tblMessages (
  ID int(11) unsigned NOT NULL auto_increment,
  ParentID int(11) unsigned default NULL,
  UserID int(11) unsigned default NULL,
  msg_type tinyint(4) unsigned NOT NULL default '0',
  obj_type tinyint(4) unsigned NOT NULL default '0',
  headerDate int(11) unsigned default NULL,
  headerSubject varchar(255) default NULL,
  headerUserID int(11) unsigned default NULL,
  headerFrom varchar(255) default NULL,
  headerTo varchar(255) default NULL,
  Priority tinyint(4) unsigned default NULL,
  seenStatus tinyint(4) unsigned NOT NULL default '0',
  MessageText text,
  tag tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (ID),
  KEY UserID (UserID),
  KEY `query` (`obj_type`,`msg_type`,`ParentID`,`UserID`)
) ENGINE=MyISAM;

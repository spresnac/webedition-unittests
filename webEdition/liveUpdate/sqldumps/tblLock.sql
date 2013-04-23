CREATE TABLE ###TBLPREFIX###tblLock (
  ID bigint(20) unsigned NOT NULL default '0',
  UserID int(11) unsigned NOT NULL default '0',
  sessionID varchar(64) NOT NULL default '',
  lockTime datetime NOT NULL,
  tbl varchar(32) NOT NULL default '',
  PRIMARY KEY (ID,tbl),
  KEY UserID (UserID,sessionID),
  KEY lockTime (lockTime)
) ENGINE=MyISAM;

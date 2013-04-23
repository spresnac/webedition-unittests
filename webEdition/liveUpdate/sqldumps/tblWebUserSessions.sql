CREATE TABLE ###TBLPREFIX###tblWebUserSessions (
  SessionID varchar(32) NOT NULL default '',
  SessionIp varchar(40)NOT NULL DEFAULT '',
  WebUserID bigint(20) unsigned NOT NULL default '0',
  WebUserGroup varchar(255) NOT NULL DEFAULT '',
  WebUserDescription varchar(255) NOT NULL DEFAULT '',
  Browser varchar(255) NOT NULL DEFAULT '',
  Referrer varchar(255) NOT NULL DEFAULT '',
  LastLogin timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  LastAccess timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PageID bigint(20) unsigned NOT NULL default '0',
  ObjectID bigint(20) unsigned NOT NULL DEFAULT '0',
  SessionAutologin tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (SessionID),
  KEY `WebUserID` (`WebUserID`),
  KEY `LastAccess` (`LastAccess`)
) ENGINE=MyISAM;

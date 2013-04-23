CREATE TABLE ###TBLPREFIX###tblWebUserAutoLogin (
  AutoLoginID varchar(64) NOT NULL default '',
  WebUserID bigint(20) unsigned NOT NULL default '0',
  LastIp varchar(40)NOT NULL DEFAULT '',
  LastLogin timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (AutoLoginID,WebUserID),
  KEY `LastLogin` (`LastLogin`)
) ENGINE=MyISAM;

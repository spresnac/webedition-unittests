CREATE TABLE ###TBLPREFIX###tblFailedLogins (
  ID  bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  Username varchar(64) NOT NULL default '',
  IP varchar(40) NOT NULL default '',
  LoginDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UserTable enum('tblUser','tblWebUser') NOT NULL,
  Servername varchar(150) NOT NULL,
  Port mediumint(8) NOT NULL,
  Script varchar(150) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY IP (LoginDate,UserTable,IP),
  KEY user (UserTable,Username,LoginDate)
) ENGINE=MyISAM;

CREATE TABLE ###TBLPREFIX###tblSchedule (
  DID bigint(20) unsigned NOT NULL default '0',
  Wann int(11) unsigned NOT NULL default '0',
  `lockedUntil` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Was tinyint(1) unsigned NOT NULL default '0',
  ClassName enum('we_htmlDocument','we_webEditionDocument','we_objectFile') NOT NULL,
  SerializedData longblob,
  Schedpro longtext,
  `Type` tinyint(3) unsigned NOT NULL default '0',
  Active tinyint(1) unsigned default NULL,
  PRIMARY KEY (DID,ClassName,Active,Wann,Was,`Type`),
  KEY Wann (Wann,`lockedUntil`,Active)
) ENGINE=MyISAM;

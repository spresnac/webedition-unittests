CREATE TABLE ###TBLPREFIX###tblNewsletterConfirm (
  confirmID varchar(96) NOT NULL default '',
  subscribe_mail varchar(255) NOT NULL default '',
  subscribe_html tinyint(1) unsigned NOT NULL default '0',
  subscribe_salutation varchar(255) NOT NULL default '',
  subscribe_title varchar(255) NOT NULL default '',
  subscribe_firstname varchar(255) NOT NULL default '',
  subscribe_lastname varchar(255) NOT NULL default '',
  lists text NOT NULL,
  expires bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY confirmID(confirmID(30)),
  KEY expires (expires),
  KEY subscribe (subscribe_mail(50),confirmID(30))
) ENGINE=MyISAM;

CREATE TABLE ###TBLPREFIX###tblhistory (
  ID bigint(20) unsigned NOT NULL auto_increment,
  DID bigint(20) unsigned NOT NULL default '0',
  DocumentTable varchar(64) NOT NULL default '',
  ContentType enum('image/*','text/html','text/webedition','text/weTmpl','text/js','text/css','text/htaccess','text/plain','folder','class_folder','application/x-shockwave-flash','video/quicktime','application/*','text/xml','object','objectFile') NOT NULL,
  ModDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  Act enum('save') NOT NULL default 'save',
  UserName varchar(64) NOT NULL default '',
  PRIMARY KEY  (ID),
  KEY UserName (UserName,DocumentTable),
  KEY DID (DID,DocumentTable,ModDate)
) ENGINE=MyISAM;

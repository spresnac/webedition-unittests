CREATE TABLE ###TBLPREFIX###tblErrorLog (
  ID int(11) unsigned NOT NULL auto_increment,
  `Type` enum('Error','Warning','Parse error','Notice','Core error','Core warning','Compile error','Compile warning','User error','User warning','User notice','Deprecated notice','User deprecated notice','Strict Error','unknown Error','SQL Error') NOT NULL,
  `Function` varchar(255) NOT NULL DEFAULT '',
  `File` varchar(255) NOT NULL DEFAULT '',
  `Line` mediumint(8) unsigned NOT NULL,
  `Text` text NOT NULL,
  `Backtrace` text NOT NULL,
  `Request` text NOT NULL,
  `Session` text NOT NULL,
  `Global` text NOT NULL DEFAULT '',
  `Server` text NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  KEY `Date` (`Date`)
) ENGINE=MyISAM; 

CREATE TABLE ###TBLPREFIX###tblOrders (
  IntID int(11) unsigned NOT NULL auto_increment,
  IntOrderID int(11) unsigned default NULL,
  IntCustomerID int(11) unsigned default NULL,
  IntArticleID int(11) unsigned default NULL,
  IntQuantity float default NULL,
  DateOrder datetime default NULL,
  DateConfirmation datetime default NULL,
  DateCustomA datetime default NULL,
  DateCustomB datetime default NULL,
  DateCustomC datetime default NULL,
  DateShipping datetime default NULL,
  DateCustomD datetime default NULL,
  DateCustomE datetime default NULL,
  DatePayment datetime default NULL,
  DateCustomF datetime default NULL,
  DateCustomG datetime default NULL,
  DateCancellation datetime default NULL,
  DateCustomH datetime default NULL,
  DateCustomI datetime default NULL,
  DateCustomJ datetime default NULL,
  DateFinished datetime default NULL,
  MailOrder DATETIME NULL,
  MailConfirmation DATETIME NULL,
  MailCustomA DATETIME NULL,
  MailCustomB DATETIME NULL,
  MailCustomC DATETIME NULL,
  MailShipping DATETIME NULL,
  MailCustomD DATETIME NULL,
  MailCustomE DATETIME NULL,
  MailPayment DATETIME NULL,
  MailCustomF DATETIME NULL,
  MailCustomG DATETIME NULL,
  MailCancellation DATETIME NULL,
  MailCustomH DATETIME NULL,
  MailCustomI DATETIME NULL,
  MailCustomJ DATETIME NULL,
  MailFinished DATETIME NULL,
  Price varchar(20) default NULL,
  IntPayment_Type tinyint(4) unsigned default NULL,
  strSerial longtext NOT NULL,
  strSerialOrder longtext NOT NULL,
  PRIMARY KEY  (IntID)
) ENGINE=MyISAM;

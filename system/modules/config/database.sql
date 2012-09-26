-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_calendar_registrations`
-- 

CREATE TABLE `tl_calendar_registrations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `regNr` varchar(255) NOT NULL default '',
  `registrationTime` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `phone` varchar(255) NULL default '',
  `street` varchar(255) NOT NULL default '',
  `plz_city` varchar(255) NOT NULL default '',
  `message` text NULL,
  `manual` char(1) NOT NULL default '',
  `manual_storno` char(1) NOT NULL default '',
  `isSend` char(1) NOT NULL default '',
  `sendTime` int(10) unsigned NOT NULL default '0',  
  `orderId` varchar(27) NOT NULL default '',
  `productId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table `tl_calendar_places`
-- 

CREATE TABLE `tl_calendar_places` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `street` varchar(255) NOT NULL default '',
  `zip` varchar(5) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_calendar_events`
--

CREATE TABLE `tl_calendar_events` (
  `evr_register` char(1) NOT NULL default '',
  `evr_place` int(10) NULL default NULL,
  `evr_lecturer` int(10) NULL default NULL,
  `evr_deadline` int(20) NULL default NULL,
  `evr_slots` int(10) NULL default NULL,
  `evr_price` decimal(20,2) NULL default NULL,
  `evr_scope` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_module`
--

CREATE TABLE `tl_module` (
  `evr_register_mail` text NULL,
 `evr_register_mail_cc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table `tl_iso_config`
--

CREATE TABLE `tl_iso_config` (
 `evr_product_types_registration` blob NULL,
 `evr_product_types_send_ticket` blob NULL,
 `evr_ticketmail_subject` text NULL,
 `evr_ticketmail_text` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

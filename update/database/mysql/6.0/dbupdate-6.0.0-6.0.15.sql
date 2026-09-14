--
-- Exponential 6.0.0 to 6.0.15, MySQL.
--
-- Note: no SET storage_engine here. That variable was removed in MySQL 5.7.6
-- and errors on anything newer, and nothing below creates a table anyway.
--

UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

--
-- The pdf export carries its own footer wording.
--
-- Every page of every export used to read "Exponential PDF export", because
-- those words were written into content/pdf/footer.tpl and there was no way to
-- change them short of overriding the template. The wording now belongs to the
-- export and is set on its own edit page: show_footer says whether the footer
-- carries a line of text at all, footer_text says what it reads. Left empty
-- with the box still ticked, the wording that shipped is used, so an export
-- that predates this change looks exactly as it did.
--

ALTER TABLE ezpdf_export ADD COLUMN show_footer int(11) NOT NULL DEFAULT 1;
ALTER TABLE ezpdf_export ADD COLUMN footer_text varchar(255) NOT NULL DEFAULT '';

--
-- OPML exports.
--
-- An RSS export can now be written as OPML, which is a list of feeds rather
-- than a list of articles. Such an export has no content source and no class
-- mapping; what it has instead is a set of outlines, one per row below, each
-- naming another export on this installation or a content node. The address is
-- worked out when the document is written, so renaming a feed cannot leave a
-- dead entry behind.
--
-- opml_head carries the OPML head fields - owner, docs, expansion and window
-- state - as json. One column rather than a dozen, because they are written
-- once, read once and never searched on, and an installation with no OPML
-- export never fills it in.
--

ALTER TABLE ezrss_export ADD COLUMN opml_head longtext;

CREATE TABLE ezrss_export_opml_item (
  id int(11) NOT NULL auto_increment,
  rssexport_id int(11) NOT NULL default 0,
  parent_id int(11) NOT NULL default 0,
  priority int(11) NOT NULL default 0,
  target_export_id int(11) NOT NULL default 0,
  source_node_id int(11) NOT NULL default 0,
  subnodes int(11) NOT NULL default 0,
  outline_type varchar(50) default 'rss',
  outline_text varchar(255) default NULL,
  title varchar(255) default NULL,
  description varchar(255) default NULL,
  category varchar(255) default NULL,
  language varchar(50) default NULL,
  xml_url varchar(255) default NULL,
  html_url varchar(255) default NULL,
  url varchar(255) default NULL,
  is_comment int(11) NOT NULL default 0,
  is_breakpoint int(11) NOT NULL default 0,
  created int(11) NOT NULL default 0,
  status int(11) NOT NULL default 0,
  PRIMARY KEY  (id,status),
  KEY ezrss_export_opml_rsseid (rssexport_id)
) ENGINE=InnoDB;

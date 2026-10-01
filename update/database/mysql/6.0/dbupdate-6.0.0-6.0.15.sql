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

--
-- podcast_head carries the channel level fields an Apple Podcasts feed needs
-- and no other format has anywhere to put: author, owner name and email,
-- artwork, category and subcategory, explicit, and whether the show is
-- episodic or serial. Json in one column, for the same reason opml_head is:
-- written once, read once, never searched on, and empty on any installation
-- with no podcast.
--

ALTER TABLE ezrss_export ADD COLUMN podcast_head longtext;

--
-- The order statuses of a whole order lifecycle.
--
-- An installation shipped with three statuses, Pending, Processing and
-- Delivered, so a shop that takes payment, ships parcels or handles returns
-- had to create its own before it could say where an order stood. These are
-- the steps most shops need, as internal statuses (below 1000) that cannot be
-- removed by mistake: payment (Awaiting payment, Paid, Payment failed),
-- fulfilment (On hold, Backordered, Packed, Shipped, Ready for pickup), the
-- end of an order (Completed, Cancelled) and what can follow it (Return
-- requested, Returned, Partially refunded, Refunded).
--
-- A status is only added when no row has its status_id yet, and its id is left
-- to the database, so a custom status that already uses one of those ids keeps
-- it.
--

INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Awaiting payment', 4 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 4);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Paid', 5 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 5);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Payment failed', 6 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 6);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'On hold', 7 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 7);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Backordered', 8 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 8);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Packed', 9 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 9);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Shipped', 10 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 10);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Ready for pickup', 11 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 11);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Completed', 12 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 12);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Cancelled', 13 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 13);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Return requested', 14 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 14);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Returned', 15 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 15);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Partially refunded', 16 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 16);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Refunded', 17 FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 17);

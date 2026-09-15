--
-- Exponential 6.0.0 to 6.0.15, SQLite.
--
-- Each ALTER adds one column: SQLite takes only one per statement, and cannot
-- drop one again, so run these once.
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

ALTER TABLE ezpdf_export ADD COLUMN show_footer integer NOT NULL DEFAULT 1;
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

CREATE TABLE `ezrss_export_opml_item` (
  `id` integer NOT NULL PRIMARY KEY AUTOINCREMENT
,  `rssexport_id` integer NOT NULL DEFAULT '0'
,  `parent_id` integer NOT NULL DEFAULT '0'
,  `priority` integer NOT NULL DEFAULT '0'
,  `target_export_id` integer NOT NULL DEFAULT '0'
,  `source_node_id` integer NOT NULL DEFAULT '0'
,  `subnodes` integer NOT NULL DEFAULT '0'
,  `outline_type` varchar(50) DEFAULT 'rss'
,  `outline_text` varchar(255) DEFAULT NULL
,  `title` varchar(255) DEFAULT NULL
,  `description` varchar(255) DEFAULT NULL
,  `category` varchar(255) DEFAULT NULL
,  `language` varchar(50) DEFAULT NULL
,  `xml_url` varchar(255) DEFAULT NULL
,  `html_url` varchar(255) DEFAULT NULL
,  `url` varchar(255) DEFAULT NULL
,  `is_comment` integer NOT NULL DEFAULT '0'
,  `is_breakpoint` integer NOT NULL DEFAULT '0'
,  `created` integer NOT NULL DEFAULT '0'
,  `status` integer NOT NULL DEFAULT '0'
);

CREATE INDEX "idx_ezrss_export_opml_item_ezrss_export_opml_rsseid" ON "ezrss_export_opml_item" (`rssexport_id`);

--
-- podcast_head carries the channel level fields an Apple Podcasts feed needs
-- and no other format has anywhere to put: author, owner name and email,
-- artwork, category and subcategory, explicit, and whether the show is
-- episodic or serial. Json in one column, for the same reason opml_head is:
-- written once, read once, never searched on, and empty on any installation
-- with no podcast.
--

ALTER TABLE ezrss_export ADD COLUMN podcast_head longtext;

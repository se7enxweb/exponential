--
-- Exponential 6.0.0 to 6.0.15, PostgreSQL.
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
ALTER TABLE ezpdf_export ADD COLUMN footer_text character varying(255) NOT NULL DEFAULT '';

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

ALTER TABLE ezrss_export ADD COLUMN opml_head text;

CREATE SEQUENCE ezrss_export_opml_item_id_seq
    START 1
    INCREMENT 1
    MAXVALUE 9223372036854775807
    MINVALUE 1
    CACHE 1;

CREATE TABLE ezrss_export_opml_item (
    id integer DEFAULT nextval('ezrss_export_opml_item_id_seq'::text) NOT NULL,
    rssexport_id integer DEFAULT 0 NOT NULL,
    parent_id integer DEFAULT 0 NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    target_export_id integer DEFAULT 0 NOT NULL,
    source_node_id integer DEFAULT 0 NOT NULL,
    subnodes integer DEFAULT 0 NOT NULL,
    outline_type character varying(50) DEFAULT 'rss'::character varying,
    outline_text character varying(255),
    title character varying(255),
    description character varying(255),
    category character varying(255),
    language character varying(50),
    xml_url character varying(255),
    html_url character varying(255),
    url character varying(255),
    is_comment integer DEFAULT 0 NOT NULL,
    is_breakpoint integer DEFAULT 0 NOT NULL,
    created integer DEFAULT 0 NOT NULL,
    status integer DEFAULT 0 NOT NULL
);

ALTER TABLE ONLY ezrss_export_opml_item
    ADD CONSTRAINT ezrss_export_opml_item_pkey PRIMARY KEY (id, status);

CREATE INDEX ezrss_export_opml_rsseid ON ezrss_export_opml_item USING btree (rssexport_id);

ALTER TABLE ezrss_export ADD COLUMN podcast_head text;

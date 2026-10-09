--
-- Exponential 6.0.0 to 6.0.15, PostgreSQL.
--

UPDATE ezsite_data SET value='6.0.15stable' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

--
-- ezuser.password_hash takes the hashes Exponential writes.
--
-- A database that came from 5.x has password_hash character varying(50), too
-- narrow for the php_default (bcrypt, 60 characters) hash every user is given
-- at the first sign-in. dbupdate-5.4-to-6.0.sql widens it since October 2026;
-- this statement is here for a site that reached 6.0 with an older copy of
-- that file. On a column that is already 255 wide it changes nothing. It
-- comes first because the statements after it add columns and stop on a
-- second run.
--

ALTER TABLE ezuser ALTER COLUMN password_hash TYPE VARCHAR(255);

--
-- ezcontentobject_trash.trashed: the time an object was moved to the trash.
--
-- The 6.0 kernel writes it when content goes to the trash and sorts and
-- filters the trash by it, so without the column moving content to the trash
-- fails. A 5.4 database does not have it: upstream added it in its 7.3 update
-- file (EZP-28881), which is not on this path. Same definition as there and in
-- the kernel schema, no backfill. Added only when it is missing, so the block
-- can run again.
--

DO $$
BEGIN
    IF NOT EXISTS ( SELECT 1 FROM information_schema.columns
                    WHERE table_schema = current_schema()
                      AND table_name = 'ezcontentobject_trash'
                      AND column_name = 'trashed' ) THEN
        ALTER TABLE ezcontentobject_trash ADD trashed integer DEFAULT 0 NOT NULL;
    END IF;
END
$$;

--
-- ezcontentobject_trash.trashed_by and trashed_via: who moved an object to
-- the trash (the user's content object id, 0 when not known) and from where
-- ("web <siteaccess>" or "cli <script>").
--
-- The kernel writes both when content goes to the trash, in the same row and
-- transaction as the rest of it, so without the columns moving content to the
-- trash fails. They replace <VarDir>/trash/trashed.json, which was local to
-- one web server and outside the database's transactions and backups.
-- update/common/scripts/6.0/movetrashrecords.php copies what that file holds
-- into the columns afterwards.
--
-- Added only when missing, so the block can run again.
--

DO $$
BEGIN
    IF NOT EXISTS ( SELECT 1 FROM information_schema.columns
                    WHERE table_schema = current_schema()
                      AND table_name = 'ezcontentobject_trash'
                      AND column_name = 'trashed_by' ) THEN
        ALTER TABLE ezcontentobject_trash ADD trashed_by integer DEFAULT 0 NOT NULL;
    END IF;
    IF NOT EXISTS ( SELECT 1 FROM information_schema.columns
                    WHERE table_schema = current_schema()
                      AND table_name = 'ezcontentobject_trash'
                      AND column_name = 'trashed_via' ) THEN
        ALTER TABLE ezcontentobject_trash ADD trashed_via character varying(100) DEFAULT ''::character varying NOT NULL;
    END IF;
END
$$;

--
-- Sequence names: <table>_s becomes <table>_<column>_seq.
--
-- The 6.0 kernel reads the id of the row it just inserted from
-- <table>_<column>_seq, the name the SERIAL type gives a sequence, and its
-- schema creates the sequences under those names. A 5.4 database has the old
-- <table>_s names, so every insert that needs its new id fails there. Upstream
-- renamed them in its 7.2 update file (EZP-28706), which is not on this path;
-- these are the same 88 sequences.
--
-- For each one: the old sequence is renamed only when it exists and the new
-- name is free, and the column default is pointed at the new name only when
-- the table and the new sequence exist and the default does not name it yet.
-- A database that already has the new names (installed by 6.0, or one that
-- ran this before) is left as it is.
--

DO $$
DECLARE
    r record;
    new_name text;
BEGIN
    FOR r IN SELECT * FROM ( VALUES
        ( 'ezapprove_items', 'id', 'ezapprove_items_s' ),
        ( 'ezbasket', 'id', 'ezbasket_s' ),
        ( 'ezcobj_state', 'id', 'ezcobj_state_s' ),
        ( 'ezcobj_state_group', 'id', 'ezcobj_state_group_s' ),
        ( 'ezcollab_group', 'id', 'ezcollab_group_s' ),
        ( 'ezcollab_item', 'id', 'ezcollab_item_s' ),
        ( 'ezcollab_item_message_link', 'id', 'ezcollab_item_message_link_s' ),
        ( 'ezcollab_notification_rule', 'id', 'ezcollab_notification_rule_s' ),
        ( 'ezcollab_profile', 'id', 'ezcollab_profile_s' ),
        ( 'ezcollab_simple_message', 'id', 'ezcollab_simple_message_s' ),
        ( 'ezcontentbrowsebookmark', 'id', 'ezcontentbrowsebookmark_s' ),
        ( 'ezcontentbrowserecent', 'id', 'ezcontentbrowserecent_s' ),
        ( 'ezcontentclass', 'id', 'ezcontentclass_s' ),
        ( 'ezcontentclass_attribute', 'id', 'ezcontentclass_attribute_s' ),
        ( 'ezcontentclassgroup', 'id', 'ezcontentclassgroup_s' ),
        ( 'ezcontentobject', 'id', 'ezcontentobject_s' ),
        ( 'ezcontentobject_attribute', 'id', 'ezcontentobject_attribute_s' ),
        ( 'ezvattype', 'id', 'ezvattype_s' ),
        ( 'ezcontentobject_link', 'id', 'ezcontentobject_link_s' ),
        ( 'ezcontentobject_tree', 'node_id', 'ezcontentobject_tree_s' ),
        ( 'ezcontentobject_version', 'id', 'ezcontentobject_version_s' ),
        ( 'ezcurrencydata', 'id', 'ezcurrencydata_s' ),
        ( 'ezdiscountrule', 'id', 'ezdiscountrule_s' ),
        ( 'ezdiscountsubrule', 'id', 'ezdiscountsubrule_s' ),
        ( 'ezenumvalue', 'id', 'ezenumvalue_s' ),
        ( 'ezforgot_password', 'id', 'ezforgot_password_s' ),
        ( 'ezgeneral_digest_user_settings', 'id', 'ezgeneral_digest_user_settings_s' ),
        ( 'ezimagefile', 'id', 'ezimagefile_s' ),
        ( 'ezinfocollection', 'id', 'ezinfocollection_s' ),
        ( 'ezinfocollection_attribute', 'id', 'ezinfocollection_attribute_s' ),
        ( 'ezisbn_group', 'id', 'ezisbn_group_s' ),
        ( 'ezisbn_group_range', 'id', 'ezisbn_group_range_s' ),
        ( 'ezisbn_registrant_range', 'id', 'ezisbn_registrant_range_s' ),
        ( 'ezkeyword', 'id', 'ezkeyword_s' ),
        ( 'ezkeyword_attribute_link', 'id', 'ezkeyword_attribute_link_s' ),
        ( 'ezmessage', 'id', 'ezmessage_s' ),
        ( 'ezmodule_run', 'id', 'ezmodule_run_s' ),
        ( 'ezmultipricedata', 'id', 'ezmultipricedata_s' ),
        ( 'eznode_assignment', 'id', 'eznode_assignment_s' ),
        ( 'eznotificationcollection', 'id', 'eznotificationcollection_s' ),
        ( 'eznotificationcollection_item', 'id', 'eznotificationcollection_item_s' ),
        ( 'eznotificationevent', 'id', 'eznotificationevent_s' ),
        ( 'ezoperation_memento', 'id', 'ezoperation_memento_s' ),
        ( 'ezorder', 'id', 'ezorder_s' ),
        ( 'ezorder_nr_incr', 'id', 'ezorder_nr_incr_s' ),
        ( 'ezorder_item', 'id', 'ezorder_item_s' ),
        ( 'ezorder_status', 'id', 'ezorder_status_s' ),
        ( 'ezorder_status_history', 'id', 'ezorder_status_history_s' ),
        ( 'ezpackage', 'id', 'ezpackage_s' ),
        ( 'ezpaymentobject', 'id', 'ezpaymentobject_s' ),
        ( 'ezpdf_export', 'id', 'ezpdf_export_s' ),
        ( 'ezpending_actions', 'id', 'ezpending_actions_s' ),
        ( 'ezpolicy', 'id', 'ezpolicy_s' ),
        ( 'ezpolicy_limitation', 'id', 'ezpolicy_limitation_s' ),
        ( 'ezpolicy_limitation_value', 'id', 'ezpolicy_limitation_value_s' ),
        ( 'ezpreferences', 'id', 'ezpreferences_s' ),
        ( 'ezprest_authorized_clients', 'id', 'ezprest_authorized_clients_s' ),
        ( 'ezprest_clients', 'id', 'ezprest_clients_s' ),
        ( 'ezproductcategory', 'id', 'ezproductcategory_s' ),
        ( 'ezproductcollection', 'id', 'ezproductcollection_s' ),
        ( 'ezproductcollection_item', 'id', 'ezproductcollection_item_s' ),
        ( 'ezproductcollection_item_opt', 'id', 'ezproductcollection_item_opt_s' ),
        ( 'ezrole', 'id', 'ezrole_s' ),
        ( 'ezrss_export', 'id', 'ezrss_export_s' ),
        ( 'ezrss_export_item', 'id', 'ezrss_export_item_s' ),
        ( 'ezrss_import', 'id', 'ezrss_import_s' ),
        ( 'ezscheduled_script', 'id', 'ezscheduled_script_s' ),
        ( 'ezsearch_object_word_link', 'id', 'ezsearch_object_word_link_s' ),
        ( 'ezsearch_search_phrase', 'id', 'ezsearch_search_phrase_s' ),
        ( 'ezsearch_word', 'id', 'ezsearch_word_s' ),
        ( 'ezsection', 'id', 'ezsection_s' ),
        ( 'ezsubtree_notification_rule', 'id', 'ezsubtree_notification_rule_s' ),
        ( 'eztrigger', 'id', 'eztrigger_s' ),
        ( 'ezurl', 'id', 'ezurl_s' ),
        ( 'ezurlalias', 'id', 'ezurlalias_s' ),
        ( 'ezurlalias_ml_incr', 'id', 'ezurlalias_ml_incr_s' ),
        ( 'ezurlwildcard', 'id', 'ezurlwildcard_s' ),
        ( 'ezuser_accountkey', 'id', 'ezuser_accountkey_s' ),
        ( 'ezuser_discountrule', 'id', 'ezuser_discountrule_s' ),
        ( 'ezuser_role', 'id', 'ezuser_role_s' ),
        ( 'ezvatrule', 'id', 'ezvatrule_s' ),
        ( 'ezwaituntildatevalue', 'id', 'ezwaituntildatevalue_s' ),
        ( 'ezwishlist', 'id', 'ezwishlist_s' ),
        ( 'ezworkflow', 'id', 'ezworkflow_s' ),
        ( 'ezworkflow_assign', 'id', 'ezworkflow_assign_s' ),
        ( 'ezworkflow_event', 'id', 'ezworkflow_event_s' ),
        ( 'ezworkflow_group', 'id', 'ezworkflow_group_s' ),
        ( 'ezworkflow_process', 'id', 'ezworkflow_process_s' )
    ) AS s ( table_name, column_name, old_name )
    LOOP
        new_name := r.table_name || '_' || r.column_name || '_seq';
        IF EXISTS ( SELECT 1 FROM pg_class
                    WHERE relkind = 'S' AND relname = r.old_name AND pg_table_is_visible( oid ) )
           AND NOT EXISTS ( SELECT 1 FROM pg_class
                            WHERE relname = new_name AND pg_table_is_visible( oid ) ) THEN
            EXECUTE 'ALTER SEQUENCE ' || quote_ident( r.old_name ) || ' RENAME TO ' || quote_ident( new_name );
        END IF;
        IF EXISTS ( SELECT 1 FROM pg_class
                    WHERE relkind = 'S' AND relname = new_name AND pg_table_is_visible( oid ) )
           AND EXISTS ( SELECT 1 FROM information_schema.columns
                        WHERE table_schema = current_schema()
                          AND table_name = r.table_name
                          AND column_name = r.column_name
                          AND ( column_default IS NULL
                                OR position( quote_literal( new_name ) in column_default ) = 0 ) ) THEN
            EXECUTE 'ALTER TABLE ' || quote_ident( r.table_name ) || ' ALTER COLUMN ' || quote_ident( r.column_name )
                 || ' SET DEFAULT nextval(' || quote_literal( new_name ) || '::regclass)';
        END IF;
    END LOOP;
END
$$;

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
  SELECT 1, 'Awaiting payment', 4 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 4);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Paid', 5 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 5);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Payment failed', 6 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 6);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'On hold', 7 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 7);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Backordered', 8 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 8);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Packed', 9 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 9);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Shipped', 10 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 10);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Ready for pickup', 11 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 11);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Completed', 12 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 12);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Cancelled', 13 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 13);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Return requested', 14 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 14);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Returned', 15 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 15);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Partially refunded', 16 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 16);
INSERT INTO ezorder_status (is_active, name, status_id)
  SELECT 1, 'Refunded', 17 WHERE NOT EXISTS (SELECT 1 FROM ezorder_status WHERE status_id = 17);


--
-- The audit index (doc/bc/6.0/audit.md, "The index"): one row per audit record (expaudit_event), how far
-- each live channel file has been indexed (expaudit_cursor) and each file's verification state (expaudit_file).
-- The JSON lines files under var/<site>/log/audit/ are the record; these tables are a copy for the console,
-- filled by the auditindex cronjob part and rebuilt from the files at any time. New in 6.0.15.
-- update/common/scripts/6.0/createaudittables.php does the same on any engine (Oracle and MongoDB too)
-- and skips tables that exist.
--
CREATE TABLE IF NOT EXISTS expaudit_cursor (
  byte_offset bigint DEFAULT '0' NOT NULL,
  channel character varying(32) DEFAULT ''::character varying NOT NULL,
  file_name character varying(64) DEFAULT ''::character varying NOT NULL,
  last_hash character varying(80) DEFAULT NULL,
  last_seq integer DEFAULT 0 NOT NULL,
  updated_ms bigint DEFAULT '0' NOT NULL
);
ALTER TABLE ONLY expaudit_cursor ADD CONSTRAINT expaudit_cursor_pkey PRIMARY KEY ( channel, file_name );
CREATE TABLE IF NOT EXISTS expaudit_event (
  channel character varying(32) DEFAULT ''::character varying NOT NULL,
  depth integer DEFAULT 0 NOT NULL,
  domain_name character varying(16) DEFAULT ''::character varying NOT NULL,
  engine character varying(16) DEFAULT NULL,
  file_name character varying(64) DEFAULT ''::character varying NOT NULL,
  id character(26) DEFAULT ''::bpchar NOT NULL,
  imported integer DEFAULT 0 NOT NULL,
  ip character varying(64) DEFAULT NULL,
  job_id character varying(32) DEFAULT NULL,
  login character varying(150) DEFAULT NULL,
  module_view character varying(128) DEFAULT NULL,
  name character varying(128) DEFAULT ''::character varying NOT NULL,
  object_id character varying(64) DEFAULT NULL,
  object_name character varying(255) DEFAULT NULL,
  object_type character varying(32) DEFAULT NULL,
  parent_id character(26) DEFAULT NULL,
  pseudonymised integer DEFAULT 0 NOT NULL,
  reason character varying(32) DEFAULT NULL,
  record text DEFAULT NULL,
  request_id character varying(40) DEFAULT NULL,
  result character varying(8) DEFAULT NULL,
  run_id character varying(40) DEFAULT NULL,
  search_text text DEFAULT NULL,
  seq integer DEFAULT 0 NOT NULL,
  session_h character varying(24) DEFAULT NULL,
  severity integer DEFAULT 0 NOT NULL,
  siteaccess character varying(64) DEFAULT NULL,
  target_id character varying(64) DEFAULT NULL,
  target_type character varying(32) DEFAULT NULL,
  time_ms bigint DEFAULT '0' NOT NULL,
  ua character varying(128) DEFAULT NULL,
  user_id integer DEFAULT NULL,
  verb character varying(32) DEFAULT NULL
);
CREATE INDEX expaudit_event_domain ON expaudit_event USING btree ( domain_name, severity, time_ms );
CREATE UNIQUE INDEX expaudit_event_file_seq ON expaudit_event USING btree ( channel, file_name, seq );
CREATE INDEX expaudit_event_ip ON expaudit_event USING btree ( ip, time_ms );
CREATE INDEX expaudit_event_job ON expaudit_event USING btree ( job_id );
CREATE INDEX expaudit_event_name ON expaudit_event USING btree ( name, time_ms );
CREATE INDEX expaudit_event_object ON expaudit_event USING btree ( object_type, object_id, time_ms );
CREATE INDEX expaudit_event_parent ON expaudit_event USING btree ( parent_id );
CREATE INDEX expaudit_event_request ON expaudit_event USING btree ( request_id );
CREATE INDEX expaudit_event_result ON expaudit_event USING btree ( result, time_ms );
CREATE INDEX expaudit_event_time ON expaudit_event USING btree ( time_ms );
CREATE INDEX expaudit_event_user ON expaudit_event USING btree ( user_id, time_ms );
ALTER TABLE ONLY expaudit_event ADD CONSTRAINT expaudit_event_pkey PRIMARY KEY ( id );
CREATE TABLE IF NOT EXISTS expaudit_file (
  archive_path character varying(255) DEFAULT NULL,
  break_line integer DEFAULT 0 NOT NULL,
  channel character varying(32) DEFAULT ''::character varying NOT NULL,
  file_name character varying(64) DEFAULT ''::character varying NOT NULL,
  records integer DEFAULT 0 NOT NULL,
  state character varying(16) DEFAULT 'live'::character varying NOT NULL,
  verified character varying(16) DEFAULT 'unchecked'::character varying NOT NULL,
  verified_ms bigint DEFAULT '0' NOT NULL
);
ALTER TABLE ONLY expaudit_file ADD CONSTRAINT expaudit_file_pkey PRIMARY KEY ( channel, file_name );

-- Full-text search: a generated tsvector column with a GIN index (PostgreSQL 12+, the 'simple' configuration:
-- names and ids are not stemmed). On an older server leave the two statements out: search uses LIKE.
ALTER TABLE expaudit_event ADD COLUMN search_tsv tsvector GENERATED ALWAYS AS (to_tsvector('simple', coalesce(search_text, ''))) STORED;
CREATE INDEX expaudit_event_tsv ON expaudit_event USING GIN (search_tsv);

-- Bookmark folders.
--
-- A user can organise bookmarks in a tree of virtual folders. folder_id is the folder
-- of a bookmark (0 is the top level, so every existing bookmark stays where it was
-- and nothing needs to be migrated), priority is the order within the folder.
-- expbookmark_folder holds the folders: parent_id 0 is the top level.
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN folder_id integer DEFAULT 0 NOT NULL;
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN priority integer DEFAULT 0 NOT NULL;
CREATE INDEX ezcontentbrowsebookmark_folder ON ezcontentbrowsebookmark USING btree ( user_id, folder_id );
CREATE SEQUENCE expbookmark_folder_id_seq START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;
CREATE TABLE expbookmark_folder (
  created integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('expbookmark_folder_id_seq'::text) NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  parent_id integer DEFAULT 0 NOT NULL,
  priority integer DEFAULT 0 NOT NULL,
  user_id integer DEFAULT 0 NOT NULL
);
ALTER TABLE ONLY expbookmark_folder ADD CONSTRAINT expbookmark_folder_pkey PRIMARY KEY ( id );
CREATE INDEX expbookmark_folder_user ON expbookmark_folder USING btree ( user_id, parent_id );

-- E-mail preferences and consent.
--
-- New tables only; nothing existing changes. Mail without a category is sent as before, so the update can run
-- before or after the code. The site secret of the links is generated on first use into
-- settings/override/mailpreferences.ini.append.php.
CREATE SEQUENCE expmail_category_id_seq START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;
CREATE TABLE expmail_category (
  created integer DEFAULT 0 NOT NULL,
  default_on integer DEFAULT 0 NOT NULL,
  description text,
  double_opt_in integer DEFAULT 0 NOT NULL,
  essential integer DEFAULT 0 NOT NULL,
  frequencies character varying(100) DEFAULT ''::character varying NOT NULL,
  handler_class character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_category_id_seq'::text) NOT NULL,
  identifier character varying(100) DEFAULT ''::character varying NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  name character varying(255) DEFAULT ''::character varying NOT NULL,
  priority integer DEFAULT 0 NOT NULL
);
ALTER TABLE ONLY expmail_category ADD CONSTRAINT expmail_category_pkey PRIMARY KEY ( id );
CREATE UNIQUE INDEX expmail_category_identifier ON expmail_category USING btree ( identifier );
CREATE SEQUENCE expmail_consent_log_id_seq START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;
CREATE TABLE expmail_consent_log (
  action character varying(30) DEFAULT ''::character varying NOT NULL,
  actor_user_id integer DEFAULT 0 NOT NULL,
  anonymised integer DEFAULT 0 NOT NULL,
  category character varying(100) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  email character varying(255) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_consent_log_id_seq'::text) NOT NULL,
  ip character varying(64) DEFAULT ''::character varying NOT NULL,
  new_value character varying(100) DEFAULT ''::character varying NOT NULL,
  old_value character varying(100) DEFAULT ''::character varying NOT NULL,
  recipient_key character varying(80) DEFAULT ''::character varying NOT NULL,
  siteaccess character varying(100) DEFAULT ''::character varying NOT NULL,
  source character varying(20) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  wording text
);
ALTER TABLE ONLY expmail_consent_log ADD CONSTRAINT expmail_consent_log_pkey PRIMARY KEY ( id );
CREATE INDEX expmail_consent_log_created ON expmail_consent_log USING btree ( created );
CREATE INDEX expmail_consent_log_recipient ON expmail_consent_log USING btree ( recipient_key, created );
CREATE INDEX expmail_consent_log_user ON expmail_consent_log USING btree ( user_id );
CREATE SEQUENCE expmail_pending_id_seq START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;
CREATE TABLE expmail_pending (
  category character varying(100) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  data text,
  expires integer DEFAULT 0 NOT NULL,
  id integer DEFAULT nextval('expmail_pending_id_seq'::text) NOT NULL,
  kind character varying(20) DEFAULT ''::character varying NOT NULL,
  recipient_key character varying(80) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL
);
ALTER TABLE ONLY expmail_pending ADD CONSTRAINT expmail_pending_pkey PRIMARY KEY ( id );
CREATE INDEX expmail_pending_expires ON expmail_pending USING btree ( expires );
CREATE INDEX expmail_pending_recipient ON expmail_pending USING btree ( recipient_key, kind );
CREATE SEQUENCE expmail_preference_id_seq START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;
CREATE TABLE expmail_preference (
  category character varying(100) DEFAULT ''::character varying NOT NULL,
  created integer DEFAULT 0 NOT NULL,
  frequency character varying(20) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_preference_id_seq'::text) NOT NULL,
  modified integer DEFAULT 0 NOT NULL,
  recipient_key character varying(80) DEFAULT ''::character varying NOT NULL,
  state character varying(20) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL
);
ALTER TABLE ONLY expmail_preference ADD CONSTRAINT expmail_preference_pkey PRIMARY KEY ( id );
CREATE INDEX expmail_preference_category ON expmail_preference USING btree ( category, state );
CREATE UNIQUE INDEX expmail_preference_recipient ON expmail_preference USING btree ( recipient_key, category );
CREATE INDEX expmail_preference_user ON expmail_preference USING btree ( user_id );
CREATE SEQUENCE expmail_suppression_id_seq START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;
CREATE TABLE expmail_suppression (
  created integer DEFAULT 0 NOT NULL,
  created_by integer DEFAULT 0 NOT NULL,
  email_hash character varying(64) DEFAULT ''::character varying NOT NULL,
  id integer DEFAULT nextval('expmail_suppression_id_seq'::text) NOT NULL,
  note text,
  reason character varying(30) DEFAULT ''::character varying NOT NULL
);
ALTER TABLE ONLY expmail_suppression ADD CONSTRAINT expmail_suppression_pkey PRIMARY KEY ( id );
CREATE UNIQUE INDEX expmail_suppression_hash ON expmail_suppression USING btree ( email_hash );

-- Personal API keys (doc/guides/api-keys.md).
--
-- One row per key a user made on the API access page (apikey/list). key_prefix is the
-- public part of the key (expk_<id>); the secret is never stored, only secret_hash,
-- HMAC-SHA-256 of the secret keyed with the row's own salt. scopes is a space separated
-- list of the scope ids of rest.ini [ApiKeySettings]. expires, last_used and revoked are
-- timestamps (0 = never / not yet). Created only when the table is missing (a DO block,
-- PostgreSQL 9.0 or newer), so running this block again changes nothing.
DO $$
BEGIN
    IF NOT EXISTS ( SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema() AND table_name = 'expapikey' ) THEN
        IF NOT EXISTS ( SELECT 1 FROM information_schema.sequences WHERE sequence_schema = current_schema() AND sequence_name = 'expapikey_id_seq' ) THEN
            CREATE SEQUENCE expapikey_id_seq START 1 INCREMENT 1 MAXVALUE 9223372036854775807 MINVALUE 1 CACHE 1;
        END IF;
        CREATE TABLE expapikey (
          created integer DEFAULT 0 NOT NULL,
          created_by integer DEFAULT 0 NOT NULL,
          expires integer DEFAULT 0 NOT NULL,
          id integer DEFAULT nextval('expapikey_id_seq'::text) NOT NULL,
          key_prefix character varying(40) DEFAULT ''::character varying NOT NULL,
          last_ip character varying(64) DEFAULT ''::character varying NOT NULL,
          last_used integer DEFAULT 0 NOT NULL,
          name character varying(255) DEFAULT ''::character varying NOT NULL,
          revoked integer DEFAULT 0 NOT NULL,
          revoked_by integer DEFAULT 0 NOT NULL,
          salt character varying(64) DEFAULT ''::character varying NOT NULL,
          scopes character varying(255) DEFAULT ''::character varying NOT NULL,
          secret_hash character varying(128) DEFAULT ''::character varying NOT NULL,
          user_id integer DEFAULT 0 NOT NULL
        );
        ALTER TABLE ONLY expapikey ADD CONSTRAINT expapikey_pkey PRIMARY KEY ( id );
        CREATE UNIQUE INDEX expapikey_prefix ON expapikey USING btree ( key_prefix );
        CREATE INDEX expapikey_user ON expapikey USING btree ( user_id );
    END IF;
END
$$;

UPDATE ezsite_data SET value='6.0.0' WHERE name='ezpublish-version';
UPDATE ezsite_data SET value='1' WHERE name='ezpublish-release';

--
-- ezuser.password_hash takes the hashes Exponential writes.
--
-- Every 5.x schema up to 2017.08 has password_hash character varying(50).
-- Exponential stores php_default (bcrypt, 60 characters) hashes and, with
-- [UserSettings] UpdateHash=true, rewrites a user's hash at the first sign-in,
-- which does not fit: the sign-in fails. 255 is the width of the kernel
-- schema. On a column that is already 255 wide the statement changes nothing,
-- so it can run again.
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

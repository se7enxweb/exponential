<?php
/**
 * File containing the expSchemaConsistencyReport class
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Compares the database with the schema that ships with Exponential and its active extensions (share/db_schema.dba
 * of the root and of each extension), and says what differs, table by table, with the SQL the schema handler of the
 * database engine would write to bring the database in line. The upgrade check of the administration
 * (setup/systemupgrade) shows it.
 *
 * It only reads. The SQL is text to show and to download; nothing here runs it.
 *
 * What it finds, per table:
 * - missing_table: shipped, not in the database
 * - extra_table:   in the database, in no shipped schema (often a table of a custom extension that ships no schema
 *                  file); its SQL drops it, so it is marked destructive
 * - inactive_table: in the database and in no active schema, but named by the schema of an extension that is
 *                  installed and not active (its share/db_schema.dba, or a CREATE TABLE in its sql/<engine>/*.sql).
 *                  It is listed with the extension's name and no SQL: dropping it would remove that extension's data,
 *                  so no DROP statement is written for it, neither for the table nor in the whole SQL
 * - changed_table: in both, with fields or indexes that differ:
 *   missing_field, extra_field, changed_field, missing_index, extra_index, changed_index
 *
 * The comparison itself is eZDbSchemaChecker::diff(); fromDifferences() takes its result, so the grouping and the
 * counts can be tested without a database. collect() reads the schema files and the database.
 *
 * Guide: doc/eng-us/guides/upgrade-check.md
 *
 * @package kernel
 */
class expSchemaConsistencyReport
{
    const STATUS_OK = 'ok';
    const STATUS_DIFFERENCES = 'differences';
    const STATUS_FAILED = 'failed';

    const TABLE_MISSING = 'missing_table';
    const TABLE_EXTRA = 'extra_table';
    const TABLE_CHANGED = 'changed_table';
    const TABLE_INACTIVE = 'inactive_table';

    /** The kinds of a finding inside a changed table, in the order they are shown */
    const ITEM_KINDS = array( 'missing_field', 'extra_field', 'changed_field', 'missing_index', 'extra_index', 'changed_index' );

    /** eZDbSchemaChecker's keys for the kinds above */
    const DIFF_KEYS = array( 'added_fields' => 'missing_field', 'removed_fields' => 'extra_field', 'changed_fields' => 'changed_field',
                             'added_indexes' => 'missing_index', 'removed_indexes' => 'extra_index', 'changed_indexes' => 'changed_index' );

    /** The kinds whose SQL removes something from the database */
    const DESTRUCTIVE_KINDS = array( 'extra_field', 'extra_index', 'changed_field', 'changed_index' );

    /** @var array the result, see result() */
    private $result;

    /**
     * @param array|false $differences What eZDbSchemaChecker::diff( $database, $shipped ) returned; false when the
     *                                 database schema could not be read
     * @param object|callable|null $generator The schema handler of the engine (generateUpgradeFile()), or a function
     *                                        of a differences array that returns SQL; null for no SQL
     * @param string $engine The database engine (mysql, postgresql, sqlite ...)
     * @param array $current The database's schema, to say what a changed field is now; optional
     * @param array $sources table => where it is shipped ('exponential' or the extension's name); optional
     * @param array $inactive table => the installed, not active extension whose schema names it
     *                        (inactiveExtensionTables()); optional
     * @return expSchemaConsistencyReport
     */
    public static function fromDifferences( $differences, $generator = null, $engine = '', array $current = array(), array $sources = array(), array $inactive = array() )
    {
        $report = new self();
        $report->result = $report->build( $differences, $generator, (string)$engine, $current, $sources, $inactive );
        return $report;
    }

    /**
     * The tables named by the schema files of the extensions that are installed and not active: each directory
     * under $extensionRoot that is not in $active, its share/db_schema.dba and the CREATE TABLE statements of its
     * sql/<engine>/*.sql. Only says to which extension a table belongs; nothing is compared with these schemas.
     *
     * @param string $extensionRoot The extension directory (eZExtension::baseDirectory())
     * @param string[] $active The names of the active extensions
     * @param string $engine The database engine (mysql, postgresql, sqlite ...): which sql/ directory to read
     * @return array table => extension name; the first extension in name order wins
     */
    public static function inactiveExtensionTables( $extensionRoot, array $active, $engine = '' )
    {
        $tables = array();
        $entries = is_dir( $extensionRoot ) ? scandir( $extensionRoot ) : false;
        if ( !is_array( $entries ) )
            return $tables;
        $engineDirs = array_unique( array_filter( array( (string)$engine, $engine === 'postgresql' ? 'pgsql' : '', $engine === 'sqlite' ? 'sqlite3' : '' ) ) );
        foreach ( $entries as $extension )
        {
            $path = $extensionRoot . '/' . $extension;
            if ( $extension === '' || $extension[0] === '.' || !is_dir( $path ) || in_array( $extension, $active, true ) )
                continue;
            $names = self::tableNames( self::readSchemaFile( $path . '/share/db_schema.dba' ) );
            foreach ( $engineDirs as $engineDir )
            {
                foreach ( glob( $path . '/sql/' . $engineDir . '/*.sql' ) ?: array() as $file )
                    $names = array_merge( $names, self::createdTables( (string)@file_get_contents( $file ) ) );
            }
            foreach ( $names as $name )
            {
                if ( !isset( $tables[$name] ) )
                    $tables[$name] = $extension;
            }
        }
        return $tables;
    }

    /**
     * The names of the tables an SQL file creates (CREATE TABLE [IF NOT EXISTS] name), unquoted.
     *
     * @param string $sql
     * @return string[]
     */
    public static function createdTables( $sql )
    {
        if ( !preg_match_all( '/\bCREATE\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?[`"\[]?([A-Za-z0-9_]+)[`"\]]?/i', (string)$sql, $matches ) )
            return array();
        return array_values( array_unique( $matches[1] ) );
    }

    /**
     * A schema file as eZDbSchema::read() reads it (a PHP file that sets $schema, or a serialized array), without
     * the kernel; false when it is missing or in another format.
     *
     * @param string $file
     * @return array|false
     */
    private static function readSchemaFile( $file )
    {
        if ( !is_file( $file ) )
            return false;
        $head = (string)@file_get_contents( $file, false, null, 0, 100 );
        if ( strpos( $head, '<?php' ) === 0 )
        {
            $schema = ( static function ( $__file ) { $schema = false; include $__file; return $schema; } )( $file );
            return is_array( $schema ) ? $schema : false;
        }
        if ( preg_match( '#a:[0-9]+:{#', $head ) )
        {
            $schema = @unserialize( (string)file_get_contents( $file ), array( 'allowed_classes' => false ) );
            return is_array( $schema ) ? $schema : false;
        }
        return false;
    }

    /**
     * Reads the shipped schema files and the database, and compares them.
     *
     * @param eZDBInterface|null $db
     * @return expSchemaConsistencyReport
     */
    public static function collect( $db = null )
    {
        $started = microtime( true );
        $db = $db ?: eZDB::instance();
        $original = eZDbSchema::read( 'share/db_schema.dba' );
        $original = is_array( $original ) ? $original : array();
        $sources = array();
        $files = array( array( 'name' => 'exponential', 'file' => 'share/db_schema.dba', 'tables' => count( self::tableNames( $original ) ) ) );
        foreach ( self::tableNames( $original ) as $table )
            $sources[$table] = 'exponential';

        foreach ( eZExtension::activeExtensions() as $extension )
        {
            $path = eZExtension::extensionPath( $extension );
            if ( $path === false || !file_exists( $path . '/share/db_schema.dba' ) )
                continue;
            $schema = eZDbSchema::read( $path . '/share/db_schema.dba' );
            if ( !$schema )
                continue;
            $original = eZDbSchema::merge( $original, $schema );
            $files[] = array( 'name' => $extension, 'file' => $path . '/share/db_schema.dba', 'tables' => count( self::tableNames( $schema ) ) );
            foreach ( self::tableNames( $schema ) as $table )
            {
                if ( !isset( $sources[$table] ) )
                    $sources[$table] = $extension;
            }
        }

        $engine = method_exists( $db, 'databaseName' ) ? (string)$db->databaseName() : '';
        // tables of extensions that are installed and not active: said to be theirs, never dropped
        $inactive = array_diff_key( self::inactiveExtensionTables( eZExtension::baseDirectory(), eZExtension::activeExtensions(), $engine ),
                                    $sources );
        $dbSchema = eZDbSchema::instance( $db );
        if ( !is_object( $dbSchema ) )
        {
            $report = new self();
            if ( method_exists( $db, 'listCollectionNames' ) )
            {
                $report->result = self::mongoResult( self::tableNames( $original ), (array)$db->listCollectionNames(), $engine );
            }
            else
            {
                $report->result = $report->build( false, null, $engine, array(), $sources );
                $report->result['failure'] = 'no_schema_handler';
            }
        }
        else
        {
            // compare in the engine's own format: the generated SQL is written in it
            $dbSchema->transformSchema( $original, true );
            $current = $dbSchema->schema( array( 'format' => 'local', 'force_autoincrement_rebuild' => true ) );
            if ( is_array( $current ) && method_exists( $dbSchema, 'normalizeForComparison' ) )
            {
                // The engine compares only what it can tell apart (SQLite: text = longtext); the SQL is still
                // written from the shipped definitions
                $differences = self::restoreDefinitions(
                    eZDbSchemaChecker::diff( $dbSchema->normalizeForComparison( $current ), $dbSchema->normalizeForComparison( $original ) ),
                    $original );
            }
            else
            {
                $differences = eZDbSchemaChecker::diff( $current, $original );
            }
            $report = self::fromDifferences( $differences, $dbSchema, $engine, is_array( $current ) ? $current : array(), $sources, $inactive );
        }
        $report->result['schema_files'] = $files;
        $report->result['seconds'] = round( microtime( true ) - $started, 3 );
        return $report;
    }

    /**
     * The result:
     * - status:   'ok', 'differences' or 'failed'
     * - failure:  '', 'unreadable_database' or 'no_schema_handler'
     * - engine:   the database engine
     * - relational: false for a document store (MongoDB), which has collections and no fields to compare
     * - tables:   list of array( name, kind, source, destructive, noise, items, counts, sql ); items is a list of
     *             array( kind, name, detail, expected, actual )
     * - counts:   missing_table, extra_table, changed_table, inactive_table (tables of an installed extension that is
     *             not active: listed, no SQL, they do not fail the check), tables (with SQL to run), noise (engine
     *             notes: changed tables the engine writes no SQL for), and each item kind
     * - sql:      the whole upgrade SQL, as eZDbSchemaInterface::generateUpgradeFile() writes it ('' when equal)
     * - mongo:    for a document store: missing, extra, groups, create_command
     * - schema_files: list of array( name, file, tables ) that were compared (collect() only)
     * - seconds:  how long it took (collect() only)
     *
     * @return array
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * The SQL as a file to download: every table's statements under a comment with what it changes.
     *
     * @param string $title
     * @return string
     */
    public function sqlText( $title = 'Database consistency check' )
    {
        $result = $this->result;
        $out = '-- ' . $title . "\n-- Engine: " . $result['engine'] . "\n-- Result: " . $result['status'] . "\n";
        $out .= "-- Review every statement before running it. Statements marked DESTRUCTIVE remove tables, columns or\n";
        $out .= "-- indexes that the shipped schema does not have; they may hold data of your own.\n";
        foreach ( $result['tables'] as $table )
        {
            if ( $table['kind'] === self::TABLE_INACTIVE )
            {
                $out .= "\n-- " . $table['name'] . ': belongs to ' . $table['source'] . " (not active), nothing to run\n"
                      . "--   activate the extension, or leave it; dropping the table removes its data\n";
                continue;
            }
            $out .= "\n-- " . $table['name'] . ': ' . ( !empty( $table['noise'] ) ? 'engine note, nothing to run' : str_replace( '_', ' ', $table['kind'] ) ) . ( $table['source'] !== '' ? ' (' . $table['source'] . ')' : '' )
                  . ( $table['destructive'] ? ' DESTRUCTIVE' : '' ) . "\n";
            foreach ( $table['items'] as $item )
                $out .= '--   ' . str_replace( '_', ' ', $item['kind'] ) . ' ' . $item['name'] . ( $item['detail'] !== '' ? ': ' . $item['detail'] : '' ) . "\n";
            $out .= rtrim( $table['sql'] ) . "\n";
        }
        return $out;
    }

    /**
     * Puts the shipped definitions back into differences found between normalized schemas, so the SQL written
     * from them creates what the schema file says (a missing table, an added or changed field).
     *
     * @param array|false $differences eZDbSchemaChecker::diff() of the normalized schemas
     * @param array $shipped The shipped schema, not normalized
     * @return array|false
     */
    public static function restoreDefinitions( $differences, array $shipped )
    {
        if ( !is_array( $differences ) )
            return $differences;
        foreach ( isset( $differences['new_tables'] ) ? $differences['new_tables'] : array() as $table => $def )
        {
            if ( isset( $shipped[$table] ) )
                $differences['new_tables'][$table] = $shipped[$table];
        }
        foreach ( isset( $differences['table_changes'] ) ? $differences['table_changes'] : array() as $table => $diff )
        {
            foreach ( isset( $diff['added_fields'] ) ? $diff['added_fields'] : array() as $field => $def )
            {
                if ( isset( $shipped[$table]['fields'][$field] ) )
                    $differences['table_changes'][$table]['added_fields'][$field] = $shipped[$table]['fields'][$field];
            }
            foreach ( isset( $diff['changed_fields'] ) ? $diff['changed_fields'] : array() as $field => $change )
            {
                if ( isset( $shipped[$table]['fields'][$field] ) )
                    $differences['table_changes'][$table]['changed_fields'][$field]['field-def'] = $shipped[$table]['fields'][$field];
            }
        }
        return $differences;
    }

    /**
     * A field definition in one line: "int(11) NOT NULL DEFAULT 0".
     *
     * @param array|mixed $def
     * @return string
     */
    public static function describeField( $def )
    {
        if ( !is_array( $def ) || !isset( $def['type'] ) )
            return '';
        $text = (string)$def['type'];
        if ( isset( $def['length'] ) && $def['length'] !== '' && $def['length'] !== false )
            $text .= '(' . $def['length'] . ')';
        if ( !empty( $def['not_null'] ) )
            $text .= ' NOT NULL';
        if ( array_key_exists( 'default', $def ) && $def['default'] !== false && $def['default'] !== null )
            $text .= ' DEFAULT ' . ( is_string( $def['default'] ) ? "'" . $def['default'] . "'" : var_export( $def['default'], true ) );
        return $text;
    }

    /**
     * An index definition in one line: "unique (a, b)".
     *
     * @param array|mixed $def
     * @return string
     */
    public static function describeIndex( $def )
    {
        if ( !is_array( $def ) )
            return '';
        $fields = array();
        foreach ( isset( $def['fields'] ) ? (array)$def['fields'] : array() as $field )
            $fields[] = is_array( $field ) ? ( isset( $field['name'] ) ? $field['name'] : '' ) : (string)$field;
        return ( isset( $def['type'] ) ? $def['type'] : 'index' ) . ' (' . implode( ', ', $fields ) . ')';
    }

    /**
     * The table names of a schema array, without its '_info'.
     *
     * @param array|mixed $schema
     * @return string[]
     */
    public static function tableNames( $schema )
    {
        if ( !is_array( $schema ) )
            return array();
        return array_values( array_filter( array_keys( $schema ), function ( $name ) { return $name !== '_info'; } ) );
    }

    /**
     * A document store (MongoDB) has collections, not tables with fields: missing and extra collections, grouped by
     * what they are for, and the mongosh command that creates the missing ones. Only kernel collections (ez*) are
     * expected.
     *
     * @param string[] $expectedTables
     * @param string[] $existingCollections
     * @param string $engine
     * @return array the result, see result()
     */
    public static function mongoResult( array $expectedTables, array $existingCollections, $engine = 'mongo' )
    {
        $expected = array_values( array_filter( $expectedTables, function ( $name ) { return strpos( $name, 'ez' ) === 0; } ) );
        $missing = array_values( array_diff( $expected, $existingCollections ) );
        $extra = array_values( array_diff( $existingCollections, $expected ) );

        $featureGroups = array(
            'Collaboration'     => array( 'ezcollab_group', 'ezcollab_item', 'ezcollab_item_group_link', 'ezcollab_item_message_link', 'ezcollab_item_participant_link', 'ezcollab_item_status', 'ezcollab_notification_rule', 'ezcollab_profile', 'ezcollab_simple_message' ),
            'Shop / Commerce'   => array( 'ezcurrencydata', 'ezdiscountrule', 'ezdiscountsubrule', 'ezdiscountsubrule_value', 'ezmultipricedata', 'ezorder', 'ezorder_nr_incr', 'ezorder_item', 'ezorder_status_history', 'ezpaymentobject', 'ezproductcategory', 'ezproductcollection_item_opt', 'ezvatrule', 'ezvatrule_product_category', 'ezuser_discountrule', 'ezwishlist' ),
            'Workflow'          => array( 'ezapprove_items', 'ezmodule_run', 'ezoperation_memento', 'ezpending_actions', 'ezpublishingqueueprocesses', 'ezscheduled_script', 'eztrigger', 'ezwaituntildatevalue', 'ezworkflow', 'ezworkflow_assign', 'ezworkflow_event', 'ezworkflow_group_link', 'ezworkflow_process' ),
            'Notifications'     => array( 'eznotificationcollection', 'eznotificationcollection_item', 'ezmessage', 'ezsubtree_notification_rule' ),
            'Media / Binary'    => array( 'ezbinaryfile', 'ezmedia' ),
            'Sessions / Auth'   => array( 'ezsession', 'ezforgot_password', 'ezuser_accountkey' ),
            'REST / OAuth'      => array( 'ezprest_authcode', 'ezprest_authorized_clients', 'ezprest_clients', 'ezprest_token' ),
            'Content'           => array( 'ezcontentobject_trash', 'ezenumobjectvalue', 'ezenumvalue', 'ezview_counter' ),
            'Search'            => array( 'ezsearch_search_phrase' ),
            'RSS'               => array( 'ezrss_import' ),
            'PDF Export'        => array( 'ezpdf_export' ),
            'Tip-a-Friend'      => array( 'eztipafriend_counter', 'eztipafriend_request' ),
            'Geo / Map'         => array( 'ezgmaplocation' ),
        );
        $groups = array();
        $assigned = array();
        foreach ( $featureGroups as $name => $collections )
        {
            $in = array_values( array_intersect( $missing, $collections ) );
            if ( $in )
            {
                $groups[] = array( 'name' => $name, 'count' => count( $in ), 'collections' => $in );
                $assigned = array_merge( $assigned, $in );
            }
        }
        $other = array_values( array_diff( $missing, $assigned ) );
        if ( $other )
            $groups[] = array( 'name' => 'Other', 'count' => count( $other ), 'collections' => $other );

        $create = array_values( array_diff( $missing, array( 'ezsequence', 'nxc_datalist_filters', 'init' ) ) );
        $command = $create ? "[\n  '" . implode( "',\n  '", $create ) . "'\n].forEach(function(c) {\n  db.createCollection(c);\n  print('Created: ' + c);\n});" : '';

        $counts = array_fill_keys( self::ITEM_KINDS, 0 ) + array( self::TABLE_MISSING => count( $missing ), self::TABLE_EXTRA => count( $extra ),
                                                                   self::TABLE_CHANGED => 0, self::TABLE_INACTIVE => 0, 'tables' => count( $missing ), 'noise' => 0 );
        return array( 'status' => $missing ? self::STATUS_DIFFERENCES : self::STATUS_OK, 'failure' => '', 'engine' => (string)$engine,
                      'relational' => false, 'tables' => array(), 'counts' => $counts, 'sql' => '',
                      'mongo' => array( 'missing' => $missing, 'extra' => $extra, 'groups' => $groups, 'create_command' => $command ),
                      'schema_files' => array(), 'seconds' => 0.0 );
    }

    private function build( $differences, $generator, $engine, array $current, array $sources, array $inactive = array() )
    {
        $counts = array_fill_keys( self::ITEM_KINDS, 0 ) + array( self::TABLE_MISSING => 0, self::TABLE_EXTRA => 0, self::TABLE_CHANGED => 0,
                                                                   self::TABLE_INACTIVE => 0, 'tables' => 0, 'noise' => 0 );
        $result = array( 'status' => self::STATUS_OK, 'failure' => '', 'engine' => $engine, 'relational' => true, 'tables' => array(),
                         'counts' => $counts, 'sql' => '', 'mongo' => null, 'schema_files' => array(), 'seconds' => 0.0 );
        if ( !is_array( $differences ) )
        {
            $result['status'] = self::STATUS_FAILED;
            $result['failure'] = 'unreadable_database';
            return $result;
        }

        $tables = array();
        if ( isset( $differences['new_tables'] ) )
        {
            foreach ( $differences['new_tables'] as $name => $def )
            {
                $tables[] = $this->table( $name, self::TABLE_MISSING, $sources, array(),
                                          $this->sql( $generator, array( 'new_tables' => array( $name => $def ) ) ), false );
                $counts[self::TABLE_MISSING]++;
            }
        }
        if ( isset( $differences['table_changes'] ) )
        {
            foreach ( $differences['table_changes'] as $name => $diff )
            {
                $items = array();
                $destructive = false;
                foreach ( self::DIFF_KEYS as $key => $kind )
                {
                    if ( empty( $diff[$key] ) )
                        continue;
                    foreach ( $diff[$key] as $itemName => $def )
                    {
                        $items[] = $this->item( $kind, $itemName, $def, $name, $current );
                        $counts[$kind]++;
                        if ( in_array( $kind, self::DESTRUCTIVE_KINDS, true ) )
                            $destructive = true;
                    }
                }
                $tables[] = $this->table( $name, self::TABLE_CHANGED, $sources, $items,
                                          $this->sql( $generator, array( 'table_changes' => array( $name => $diff ) ) ), $destructive );
                $counts[self::TABLE_CHANGED]++;
            }
        }
        if ( isset( $differences['removed_tables'] ) )
        {
            foreach ( $differences['removed_tables'] as $name => $def )
            {
                if ( isset( $inactive[$name] ) )
                {
                    // An installed extension that is not active names it: its data, not something to drop. No SQL
                    // for it, here or in the whole SQL below.
                    $tables[] = $this->table( $name, self::TABLE_INACTIVE, array( $name => $inactive[$name] ), array(), '', false );
                    $counts[self::TABLE_INACTIVE]++;
                    unset( $differences['removed_tables'][$name] );
                    continue;
                }
                $tables[] = $this->table( $name, self::TABLE_EXTRA, $sources, array(),
                                          $this->sql( $generator, array( 'removed_tables' => array( $name => $def ) ) ), true );
                $counts[self::TABLE_EXTRA]++;
            }
        }
        // Engine notes: a changed table the engine's schema handler writes no SQL for. The engine reports a type in
        // its own words (SQLite: text for longtext, a primary key it does not see as auto_increment), and nothing
        // in the database needs to change. They are listed, and they do not fail the check.
        foreach ( $tables as $index => $table )
        {
            if ( $generator !== null && $table['kind'] === self::TABLE_CHANGED && trim( $table['sql'] ) === '' )
            {
                $tables[$index]['noise'] = true;
                $tables[$index]['destructive'] = false;
                $counts['noise']++;
            }
        }
        if ( isset( $differences['removed_tables'] ) && !$differences['removed_tables'] )
            unset( $differences['removed_tables'] );
        // the tables to act on first, then the tables of extensions that are not active, then the engine notes
        usort( $tables, function ( $a, $b ) {
            $rank = function ( $t ) { return $t['noise'] ? 2 : ( $t['kind'] === self::TABLE_INACTIVE ? 1 : 0 ); };
            if ( $rank( $a ) !== $rank( $b ) )
                return $rank( $a ) - $rank( $b );
            return strcmp( $a['name'], $b['name'] );
        } );
        $counts['tables'] = count( $tables ) - $counts['noise'] - $counts[self::TABLE_INACTIVE];

        $result['tables'] = $tables;
        $result['counts'] = $counts;
        $result['sql'] = count( $tables ) > $counts[self::TABLE_INACTIVE] ? $this->sql( $generator, $differences ) : '';
        $result['status'] = $counts['tables'] > 0 ? self::STATUS_DIFFERENCES : self::STATUS_OK;
        return $result;
    }

    private function table( $name, $kind, array $sources, array $items, $sql, $destructive )
    {
        $counts = array_fill_keys( self::ITEM_KINDS, 0 );
        foreach ( $items as $item )
            $counts[$item['kind']]++;
        return array( 'name' => (string)$name, 'kind' => $kind, 'source' => isset( $sources[$name] ) ? (string)$sources[$name] : '',
                      'destructive' => (bool)$destructive, 'noise' => false, 'items' => $items, 'counts' => $counts, 'sql' => (string)$sql );
    }

    private function item( $kind, $name, $def, $table, array $current )
    {
        $expected = '';
        $actual = '';
        $detail = '';
        switch ( $kind )
        {
            case 'missing_field':
                $expected = self::describeField( $def );
                break;
            case 'extra_field':
                $actual = isset( $current[$table]['fields'][$name] ) ? self::describeField( $current[$table]['fields'][$name] ) : '';
                break;
            case 'changed_field':
                $expected = self::describeField( isset( $def['field-def'] ) ? $def['field-def'] : array() );
                $actual = isset( $current[$table]['fields'][$name] ) ? self::describeField( $current[$table]['fields'][$name] ) : '';
                $detail = isset( $def['different-options'] ) ? implode( ', ', (array)$def['different-options'] ) : '';
                break;
            case 'missing_index':
            case 'changed_index':
                $expected = self::describeIndex( $def );
                $actual = isset( $current[$table]['indexes'][$name] ) ? self::describeIndex( $current[$table]['indexes'][$name] ) : '';
                break;
            case 'extra_index':
                $actual = self::describeIndex( $def );
                break;
        }
        return array( 'kind' => $kind, 'name' => (string)$name, 'detail' => $detail, 'expected' => $expected, 'actual' => $actual );
    }

    private function sql( $generator, array $differences )
    {
        if ( $generator === null )
            return '';
        if ( is_object( $generator ) && method_exists( $generator, 'generateUpgradeFile' ) )
            return (string)$generator->generateUpgradeFile( $differences );
        if ( is_callable( $generator ) )
            return (string)call_user_func( $generator, $differences );
        return '';
    }
}

<?php
/**
 * The two tables of help maintained as content (doc/eng-us/features/6.0/help-content.md) on any engine: whether they
 * exist, and creating them from their definitions in share/db_schema.dba through the engine's schema handler
 * (MySQL/MariaDB, PostgreSQL, SQLite, Oracle with ezoracle, MongoDB).
 *
 * - exphelp_help_id: every help_id that was Released, with the object it belongs to (expHelpContentHelpIds). The
 *   validator reports a released help_id that was renamed or given to another topic (style guide section 7).
 * - exphelp_feedback: the answers to "Was this helpful?" (expHelpContentFeedback): topic, yes or no, a reason from a
 *   fixed list, the day. Nothing about who answered.
 *
 * New installations get both from the kernel schemas; an installation made before them gets them from the 6.0.0 to
 * 6.0.15 update files or from "./console exp:help:content install" (step tables). Without them both features are off
 * and everything else works as before.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpContentTables
{
    const HELP_ID = 'exphelp_help_id';
    const FEEDBACK = 'exphelp_feedback';

    /** @var array table => true: only a table found is remembered (a persistent worker sees one created later) */
    protected static $exists = array();

    /** @return string[] */
    public static function tables()
    {
        return array( self::HELP_ID, self::FEEDBACK );
    }

    /**
     * The tables' definitions, from share/db_schema.dba
     *
     * @param string[]|null $tables default tables()
     * @return array table name => definition
     */
    public static function definition( ?array $tables = null )
    {
        $schema = array();
        $file = eZSys::rootDir() . '/share/db_schema.dba';
        if ( is_readable( $file ) )
            $schema = eZDbSchema::read( $file, true );
        if ( isset( $schema['schema'] ) )
            $schema = $schema['schema'];
        $result = array();
        foreach ( $tables === null ? self::tables() : $tables as $table )
            if ( isset( $schema[$table] ) )
                $result[$table] = $schema[$table];
        return $result;
    }

    /**
     * Whether a table exists
     *
     * @param string $table
     * @param eZDBInterface|null $db
     * @return bool|null null when it cannot be told
     */
    public static function exists( $table, $db = null )
    {
        if ( !in_array( $table, self::tables(), true ) )
            return false;
        $db = $db ?: eZDB::instance();
        switch ( expAuditIndexSchema::type( $db ) )
        {
            case 'sqlite':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE type = 'table' AND name = '$table'" );
                break;
            case 'mysql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '$table'" );
                break;
            case 'postgresql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT relname AS name FROM pg_class WHERE relkind = 'r' AND relname = '$table'" );
                break;
            case 'oracle':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM user_tables WHERE table_name = '" . strtoupper( $table ) . "'" );
                break;
            case 'mongodb':
                if ( !method_exists( $db, 'listCollectionNames' ) )
                    return null;
                try
                {
                    return in_array( $table, (array)$db->listCollectionNames(), true );
                }
                catch ( Throwable $e )
                {
                    return null;
                }
            default:
                return null;
        }
        return $rows === null ? null : count( $rows ) > 0;
    }

    /**
     * Whether a table can be used in this request (exists; remembered for the request)
     *
     * @param string $table
     * @return bool
     */
    public static function available( $table )
    {
        if ( isset( self::$exists[$table] ) )
            return true;
        try
        {
            $found = self::exists( $table ) === true;
        }
        catch ( Throwable $e )
        {
            $found = false;
        }
        if ( $found )
            self::$exists[$table] = true;
        return $found;
    }

    /** Forgets what available() found (a persistent worker, a test, after install()) */
    public static function resetCache()
    {
        self::$exists = array();
    }

    /**
     * Creates the tables that are missing
     *
     * @param eZDBInterface|null $db
     * @param bool $dryRun only report what is missing
     * @param string[] $messages what was done (or would be)
     * @return bool whether every table exists afterwards (with $dryRun: whether none is missing)
     */
    public static function install( $db = null, $dryRun = false, &$messages = array() )
    {
        $db = $db ?: eZDB::instance();
        $ok = true;
        foreach ( self::tables() as $table )
        {
            $exists = self::exists( $table, $db );
            if ( $exists === true )
                continue;
            if ( $exists === null )
            {
                $messages[] = "cannot tell whether the table $table exists on this database";
                $ok = false;
                continue;
            }
            if ( $dryRun )
            {
                $messages[] = "create table $table";
                $ok = false;
                continue;
            }
            $schema = self::definition( array( $table ) );
            if ( !$schema )
            {
                $messages[] = "share/db_schema.dba has no table $table";
                $ok = false;
                continue;
            }
            $type = expAuditIndexSchema::type( $db );
            $handler = eZDbSchema::instance( array( 'type' => $type === 'mongodb' ? $db->databaseName() : $type,
                                                    'instance' => $db, 'schema' => $schema ) );
            if ( !$handler )
            {
                $messages[] = "no schema handler for $type";
                $ok = false;
                continue;
            }
            $created = $handler->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => $type === 'mysql' ? 'innodb' : '' ) );
            $created = $created && self::exists( $table, $db ) === true;
            $messages[] = $created ? "created table $table" : "could not create table $table";
            $ok = $ok && $created;
        }
        self::resetCache();
        return $ok;
    }
}

?>

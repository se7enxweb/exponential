<?php
/**
 * File containing the expPasskeySchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The exppasskey table on any engine: whether it exists, and creating it from its definition in share/db_schema.dba
 * through the engine's schema handler (MySQL/MariaDB, PostgreSQL, SQLite, Oracle with ezoracle, MongoDB). Used by
 * update/common/scripts/6.0/createpasskeytable.php for installations made before the table existed (the SQL update
 * files of MySQL, PostgreSQL and SQLite create the same table), and by the passkey pages, which stay hidden while
 * the table is missing instead of failing.
 */
class expPasskeySchema
{
    const TABLE = 'exppasskey';

    /**
     * @return array table name => definition, from share/db_schema.dba
     */
    public static function definition()
    {
        $schema = array();
        $file = eZSys::rootDir() . '/share/db_schema.dba';
        if ( is_readable( $file ) )
            $schema = eZDbSchema::read( $file, true );
        if ( isset( $schema['schema'] ) )
            $schema = $schema['schema'];
        return isset( $schema[self::TABLE] ) ? array( self::TABLE => $schema[self::TABLE] ) : array();
    }

    /**
     * Whether the table exists. "Yes" is kept for the database connection; reset() forgets it.
     *
     * @param eZDBInterface|null $db
     * @return bool|null null when it cannot be told
     */
    public static function exists( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $key = 'expPasskeySchemaExists_' . spl_object_id( $db );
        if ( array_key_exists( $key, $GLOBALS ) )
            return $GLOBALS[$key];
        $type = expAuditIndexSchema::type( $db );
        switch ( $type )
        {
            case 'sqlite':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'exppasskey'" );
                break;
            case 'mysql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'exppasskey'" );
                break;
            case 'postgresql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT relname AS name FROM pg_class WHERE relkind = 'r' AND relname = 'exppasskey'" );
                break;
            case 'oracle':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM user_tables WHERE table_name = 'EXPPASSKEY'" );
                break;
            case 'mongodb':
                $rows = null;
                if ( method_exists( $db, 'listCollectionNames' ) )
                {
                    try
                    {
                        $rows = in_array( self::TABLE, (array)$db->listCollectionNames(), true ) ? array( 1 ) : array();
                    }
                    catch ( Throwable $e )
                    {
                        $rows = null;
                    }
                }
                break;
            default:
                $rows = null;
        }
        $result = $rows === null ? null : count( $rows ) > 0;
        // only "exists" is kept (a table is not dropped while the site runs): a missing table or a database that could
        // not answer is asked again, so the table created by the update script is seen by a persistent worker at once
        if ( $result === true )
            $GLOBALS[$key] = $result;
        return $result;
    }

    /** Forgets what exists() found (after install(), and at the start of a request of a persistent worker). */
    public static function reset()
    {
        foreach ( array_keys( $GLOBALS ) as $k )
            if ( strpos( (string)$k, 'expPasskeySchemaExists_' ) === 0 )
                unset( $GLOBALS[$k] );
    }

    /**
     * Creates the table when it is missing.
     *
     * @param eZDBInterface|null $db
     * @param string[] $messages what was done, for the command line
     * @return bool whether the table exists afterwards
     */
    public static function install( $db = null, &$messages = array() )
    {
        $db = $db ?: eZDB::instance();
        self::reset();
        $exists = self::exists( $db );
        if ( $exists === true )
        {
            $messages[] = 'the table exppasskey exists already';
            return true;
        }
        if ( $exists === null )
        {
            $messages[] = 'cannot tell whether the table exppasskey exists on this database';
            return false;
        }
        $schema = self::definition();
        if ( !$schema )
        {
            $messages[] = 'share/db_schema.dba has no exppasskey table';
            return false;
        }
        $type = expAuditIndexSchema::type( $db );
        $handler = eZDbSchema::instance( array( 'type' => $type === 'mongodb' ? $db->databaseName() : $type,
                                                'instance' => $db, 'schema' => $schema ) );
        if ( !$handler )
        {
            $messages[] = "no schema handler for $type";
            return false;
        }
        // SQLite: inside a transaction the statement waits for the database's writer (the site, the audit) where a lone
        // statement fails at once with "database is locked"
        $wrap = $type === 'sqlite';
        if ( $wrap )
            $db->begin();
        $ok = $handler->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => $type === 'mysql' ? 'innodb' : '' ) );
        if ( $wrap )
            $ok = $db->commit() && $ok;
        $messages[] = $ok ? 'created exppasskey' : 'could not create exppasskey';
        self::reset();
        return $ok && self::exists( $db ) === true;
    }
}

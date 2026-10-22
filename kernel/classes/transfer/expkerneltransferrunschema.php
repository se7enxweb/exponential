<?php
/**
 * File containing the expKernelTransferRunSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The table exp_transfer_run on any engine: whether it exists, and creating it from its definition in
 * share/db_schema.dba through the engine's schema handler (MySQL/MariaDB, PostgreSQL, SQLite, Oracle with ezoracle,
 * MongoDB). Used by update/common/scripts/6.0/createtransferruntable.php for installations made before the table
 * existed; the SQL update files of MySQL, PostgreSQL and SQLite create the same table. The history
 * (expKernelTransferHistory) asks exists() once per process and writes nothing while the table is missing, so no
 * tool breaks on an installation that has not been updated yet.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferRunSchema
{
    const TABLE = 'exp_transfer_run';

    /** @var array database key => bool|null */
    protected static $known = array();

    /**
     * The table's definition, from share/db_schema.dba.
     *
     * @param string|null $file
     * @return array table name => definition
     */
    public static function definition( $file = null )
    {
        $schema = array();
        $file = $file ?: eZSys::rootDir() . '/share/db_schema.dba';
        if ( is_readable( $file ) )
            $schema = eZDbSchema::read( $file, true );
        if ( isset( $schema['schema'] ) )
            $schema = $schema['schema'];
        return isset( $schema[self::TABLE] ) ? array( self::TABLE => $schema[self::TABLE] ) : array();
    }

    /**
     * Whether the table exists (asked once per database connection and process).
     *
     * @param eZDBInterface|null $db
     * @param bool $fresh ask the database again
     * @return bool|null null when it cannot be told
     */
    public static function exists( $db = null, $fresh = false )
    {
        $db = $db ?: eZDB::instance();
        $key = spl_object_id( $db );
        if ( !$fresh && array_key_exists( $key, self::$known ) )
            return self::$known[$key];
        switch ( expAuditIndexSchema::type( $db ) )
        {
            case 'sqlite':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'exp_transfer_run'" );
                break;
            case 'mysql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'exp_transfer_run'" );
                break;
            case 'postgresql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT relname AS name FROM pg_class WHERE relkind = 'r' AND relname = 'exp_transfer_run'" );
                break;
            case 'oracle':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM user_tables WHERE table_name = 'EXP_TRANSFER_RUN'" );
                break;
            case 'mongodb':
                $rows = null;
                if ( method_exists( $db, 'listCollectionNames' ) )
                {
                    try
                    {
                        return self::$known[$key] = in_array( self::TABLE, (array)$db->listCollectionNames(), true );
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
        return self::$known[$key] = ( $rows === null ? null : count( $rows ) > 0 );
    }

    /** Forgets what exists() found (tests, after install()). */
    public static function reset()
    {
        self::$known = array();
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
        $exists = self::exists( $db, true );
        if ( $exists === true )
        {
            $messages[] = 'the table exp_transfer_run exists already';
            return true;
        }
        if ( $exists === null )
        {
            $messages[] = 'cannot tell whether the table exp_transfer_run exists on this database';
            return false;
        }
        $schema = self::definition();
        if ( !$schema )
        {
            $messages[] = 'share/db_schema.dba has no exp_transfer_run table';
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
        $ok = $handler->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => $type === 'mysql' ? 'innodb' : '' ) );
        $messages[] = $ok ? 'created exp_transfer_run' : 'could not create exp_transfer_run';
        return $ok && self::exists( $db, true ) === true;
    }
}

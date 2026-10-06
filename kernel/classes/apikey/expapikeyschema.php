<?php
/**
 * File containing the expApiKeySchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The expapikey table on any engine: whether it exists, and creating it from its definition in share/db_schema.dba
 * through the engine's schema handler (MySQL/MariaDB, PostgreSQL, SQLite, Oracle with ezoracle, MongoDB). Used by
 * update/common/scripts/6.0/createapikeytable.php, for installations made before the table existed; the SQL update
 * files of MySQL, PostgreSQL and SQLite create the same table.
 */
class expApiKeySchema
{
    const TABLE = 'expapikey';

    /**
     * The table's definition, from share/db_schema.dba.
     *
     * @return array table name => definition
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
     * Whether the table exists.
     *
     * @param eZDBInterface|null $db
     * @return bool|null null when it cannot be told
     */
    public static function exists( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $type = expAuditIndexSchema::type( $db );
        switch ( $type )
        {
            case 'sqlite':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'expapikey'" );
                break;
            case 'mysql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'expapikey'" );
                break;
            case 'postgresql':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT relname AS name FROM pg_class WHERE relkind = 'r' AND relname = 'expapikey'" );
                break;
            case 'oracle':
                $rows = expAuditIndexSchema::tryArrayQuery( $db, "SELECT table_name AS name FROM user_tables WHERE table_name = 'EXPAPIKEY'" );
                break;
            case 'mongodb':
                if ( !method_exists( $db, 'listCollectionNames' ) )
                    return null;
                try
                {
                    return in_array( self::TABLE, (array)$db->listCollectionNames(), true );
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
     * Creates the table when it is missing.
     *
     * @param eZDBInterface|null $db
     * @param string[] $messages what was done, for the command line
     * @return bool whether the table exists afterwards
     */
    public static function install( $db = null, &$messages = array() )
    {
        $db = $db ?: eZDB::instance();
        $exists = self::exists( $db );
        if ( $exists === true )
        {
            $messages[] = 'the table expapikey exists already';
            return true;
        }
        if ( $exists === null )
        {
            $messages[] = 'cannot tell whether the table expapikey exists on this database';
            return false;
        }
        $schema = self::definition();
        if ( !$schema )
        {
            $messages[] = 'share/db_schema.dba has no expapikey table';
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
        $messages[] = $ok ? 'created expapikey' : 'could not create expapikey';
        return $ok && self::exists( $db ) === true;
    }
}
?>

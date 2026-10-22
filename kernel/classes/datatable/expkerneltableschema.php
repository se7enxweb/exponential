<?php
/**
 * File containing the expKernelTableSchema class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Tables of a .dba schema on any engine: whether a table exists (asked of the engine's own catalogue, never inferred
 * from a query result: MongoDB answers an empty list for a collection that does not exist, MySQL raises), its columns
 * as the engine reports them, and creating the missing ones through the engine's schema handler (MySQL/MariaDB,
 * PostgreSQL, SQLite, Oracle with ezoracle, MongoDB).
 *
 *   expKernelTableSchema::exists( 'exp_secret' );                         // true, false, null (cannot tell)
 *   expKernelTableSchema::install( array( 'exp_secret', 'exp_data_connection' ), null, null, $messages );
 *
 * The data table layer creates its kernel tables with it from the administration (no command needed), and a data
 * table's schema is installed on a named connection the same way (expDataTableSchema).
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTableSchema
{
    /** @var array "<db key>:<table>" => bool|null */
    protected static $known = array();

    /**
     * The engine of a connection: mysql, postgresql, sqlite, oracle, mongodb, or the driver's own name (pdo_sqlsrv).
     *
     * @param eZDBInterface|null $db
     * @return string
     */
    public static function type( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $type = strtolower( (string)$db->databaseName() );
        if ( strpos( $type, 'mongo' ) === 0 )
            return 'mongodb';
        if ( $type === 'mysqli' )
            return 'mysql';
        if ( strpos( $type, 'pdo_' ) === 0 && method_exists( $db, 'sqlDialect' ) )
        {
            $dialect = $db->sqlDialect();
            return $dialect === 'generic' ? $type : $dialect;
        }
        return $type;
    }

    /**
     * The tables of a .dba file (share/db_schema.dba when none is given).
     *
     * @param string|null $file
     * @return array name => definition
     */
    public static function read( $file = null )
    {
        $file = $file ?: 'share/db_schema.dba';
        if ( $file[0] !== '/' )
            $file = rtrim( eZSys::rootDir(), '/' ) . '/' . $file;
        if ( !is_readable( $file ) )
            return array();
        $schema = eZDbSchema::read( $file, true );
        if ( isset( $schema['schema'] ) )
            $schema = $schema['schema'];
        unset( $schema['_info'] );
        return is_array( $schema ) ? $schema : array();
    }

    /**
     * Whether a table exists (asked once per connection, table and process unless $fresh).
     *
     * @param string $table
     * @param eZDBInterface|null $db
     * @param bool $fresh
     * @return bool|null null when it cannot be told
     */
    public static function exists( $table, $db = null, $fresh = false )
    {
        $db = $db ?: eZDB::instance();
        $table = (string)$table;
        if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_]{0,127}$/', $table ) )
            return false;
        $key = spl_object_id( $db ) . ':' . $table;
        if ( !$fresh && array_key_exists( $key, self::$known ) )
            return self::$known[$key];
        $names = self::tables( $db );
        if ( $names === null )
            return self::$known[$key] = null;
        $lower = array_map( 'strtolower', $names );
        return self::$known[$key] = in_array( strtolower( $table ), $lower, true );
    }

    /**
     * Every table (collection) of a connection, from the engine's catalogue.
     *
     * @param eZDBInterface|null $db
     * @return string[]|null null when the catalogue cannot be read
     */
    public static function tables( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $type = self::type( $db );
        $sql = null;
        switch ( $type )
        {
            case 'sqlite':
                $sql = "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'";
                break;
            case 'mysql':
                $sql = "SELECT table_name AS name FROM information_schema.tables WHERE table_schema = DATABASE()";
                break;
            case 'postgresql':
                $sql = "SELECT tablename AS name FROM pg_tables WHERE schemaname = current_schema()";
                break;
            case 'oracle':
                $sql = "SELECT table_name AS name FROM user_tables";
                break;
            case 'sqlserver':
                $sql = "SELECT table_name AS name FROM information_schema.tables WHERE table_type = 'BASE TABLE'";
                break;
            case 'mongodb':
                if ( !method_exists( $db, 'listCollectionNames' ) )
                    return null;
                try
                {
                    return array_values( array_map( 'strval', (array)$db->listCollectionNames() ) );
                }
                catch ( Throwable $e )
                {
                    return null;
                }
            default:
                if ( method_exists( $db, 'eZTableList' ) )
                {
                    try
                    {
                        $list = $db->eZTableList();
                        return is_array( $list ) ? array_values( array_map( 'strval', array_keys( $list ) ) ) : null;
                    }
                    catch ( Throwable $e )
                    {
                        return null;
                    }
                }
                return null;
        }
        $rows = self::tryArrayQuery( $db, $sql );
        if ( $rows === null )
            return null;
        $names = array();
        foreach ( $rows as $row )
        {
            $row = array_change_key_case( (array)$row, CASE_LOWER );
            if ( isset( $row['name'] ) )
                $names[] = (string)$row['name'];
        }
        return $names;
    }

    /** Forgets what exists() found (tests, after install()). */
    public static function reset()
    {
        self::$known = array();
    }

    /**
     * Creates the tables of a schema that are missing on a connection.
     *
     * @param string[] $tables the tables to create (all of the schema when empty)
     * @param eZDBInterface|null $db
     * @param array|string|null $schema the schema (name => definition) or a .dba file (share/db_schema.dba when null)
     * @param string[] $messages what was done
     * @return bool whether every table exists afterwards
     */
    public static function install( array $tables, $db = null, $schema = null, &$messages = array() )
    {
        $db = $db ?: eZDB::instance();
        if ( !is_array( $schema ) )
            $schema = self::read( $schema );
        if ( !$tables )
            $tables = array_keys( $schema );
        $missing = array();
        foreach ( $tables as $table )
        {
            if ( !isset( $schema[$table] ) )
            {
                $messages[] = "the schema has no table $table";
                return false;
            }
            $exists = self::exists( $table, $db, true );
            if ( $exists === true )
            {
                $messages[] = "the table $table exists already";
                continue;
            }
            if ( $exists === null )
            {
                $messages[] = "cannot tell whether the table $table exists on this database";
                return false;
            }
            $missing[$table] = $schema[$table];
        }
        if ( !$missing )
            return true;
        $handler = self::handler( $db, $missing );
        if ( !$handler )
        {
            $messages[] = 'no schema handler for ' . self::type( $db );
            return false;
        }
        $type = self::type( $db );
        $before = expAuditIndexSchema::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $ok = $handler->insertSchema( array( 'schema' => true, 'data' => false, 'table_type' => $type === 'mysql' ? 'innodb' : '' ) );
        }
        catch ( Throwable $e )
        {
            $ok = false;
            $messages[] = $e->getMessage();
        }
        $db->setErrorHandling( $before );
        $all = true;
        foreach ( array_keys( $missing ) as $table )
        {
            $now = self::exists( $table, $db, true ) === true;
            $messages[] = $now ? "created $table" : "could not create $table";
            $all = $all && $now;
        }
        return $ok !== false && $all;
    }

    /**
     * The SQL that install() would run for the given tables, for a preview (empty for MongoDB, which has none).
     *
     * @param array $schema name => definition
     * @param string $type mysql, postgresql, sqlite, oracle
     * @return string
     */
    public static function sql( array $schema, $type )
    {
        $handler = eZDbSchema::instance( array( 'type' => $type === 'mongodb' ? 'mongo' : $type, 'instance' => new eZNullDB( array() ), 'schema' => $schema ) );
        if ( !$handler )
            return '';
        $options = array( 'schema' => true, 'data' => false, 'allow_multi_insert' => false, 'diff_friendly' => false,
                          'table_type' => $type === 'mysql' ? 'innodb' : '' );
        try
        {
            $handler->transformSchema( $schema, true );
            return (string)$handler->generateSchemaFile( $schema, $options );
        }
        catch ( Throwable $e )
        {
            return '';
        }
    }

    /**
     * The schema handler of a connection.
     *
     * @param eZDBInterface $db
     * @param array $schema
     * @return eZDbSchemaInterface|false
     */
    public static function handler( $db, array $schema )
    {
        $type = self::type( $db );
        // no schema: the handler reads the database's own (schema() reads it only while its schema is false)
        return eZDbSchema::instance( array( 'type' => $type === 'mongodb' ? $db->databaseName() : $type, 'instance' => $db, 'schema' => $schema ? $schema : false ) );
    }

    /**
     * A query that returns null instead of failing or raising.
     *
     * @param eZDBInterface $db
     * @param string $sql
     * @return array|null
     */
    public static function tryArrayQuery( $db, $sql )
    {
        return expAuditIndexSchema::tryArrayQuery( $db, $sql );
    }
}
?>

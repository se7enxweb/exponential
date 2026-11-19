<?php
/**
 * File containing the expDBInList class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package lib
 */

/**
 * Queries whose condition is an IN list of a set of values of any size: the values are asked in lists of at most
 * chunkSize() values, one statement per list, so a set of tens of thousands of ids never becomes one statement
 * that the database refuses (Oracle: at most 1,000 values per IN list, ORA-01795/ORA-00913 past it; MySQL and
 * MariaDB: max_allowed_packet; SQLite: SQLITE_MAX_SQL_LENGTH, 1 MB by default; PostgreSQL and the MongoDB SQL
 * fallback: memory and planning time grow with the list).
 *
 * Every method returns false when one of its statements fails, never a part of the rows or a count of a failed
 * result: arrayQuery() returns false on an error, and (array)false is a row of its own.
 *
 * <code>
 * $rows = expDBInList::rows( $db, 'SELECT contentobject_id FROM ezuser WHERE', $objectIDs, 'contentobject_id' );
 * if ( $rows === false )
 *     return $unknown;
 * $count = expDBInList::count( $db, 'FROM ezcontentobject_tree WHERE', $objectIDs, 'contentobject_id', 'int', 'AND depth > 1' );
 * expDBInList::query( $db, 'DELETE FROM ezuser_role WHERE', $objectIDs, 'contentobject_id' );
 * </code>
 *
 * The statement is "$select <column> IN ( ... ) $tail". Because each list is its own statement, ORDER BY, LIMIT,
 * GROUP BY and aggregates in $tail apply to each list on its own: sort the rows afterwards, and use count() (a sum of
 * COUNT(*) per list) rather than COUNT( DISTINCT ... ). The values are made unique before they are split, so a row
 * matched by its value is returned once. A write through query() is not atomic across lists: run it inside the
 * caller's transaction when that matters.
 *
 * The chunk size comes from site.ini [DatabaseSettings] InListChunkSize, per engine InListChunkSizes[<engine>];
 * Oracle never takes more than 1,000.
 */
class expDBInList
{
    /** The chunk size when site.ini says nothing */
    const DEFAULT_CHUNK_SIZE = 1000;

    /** Oracle refuses an IN list of more values (ORA-01795) */
    const ORACLE_MAX_CHUNK_SIZE = 1000;

    /** @var int[] chunk size by engine name, read once per process */
    private static $chunkSizes = array();

    /** @var int|null a chunk size set by setChunkSize(), over the settings */
    private static $fixedChunkSize = null;

    /**
     * The number of values that go into one IN list for the given database.
     *
     * @param eZDBInterface|null $db the database the statements go to (default eZDB::instance())
     * @return int at least 1
     */
    public static function chunkSize( $db = null )
    {
        $engine = self::engine( $db );
        if ( self::$fixedChunkSize !== null )
            $size = self::$fixedChunkSize;
        else
        {
            if ( !isset( self::$chunkSizes[$engine] ) )
                self::$chunkSizes[$engine] = self::configuredChunkSize( $engine );
            $size = self::$chunkSizes[$engine];
        }
        if ( ( $engine === 'oracle' || $engine === 'pdo_oci' ) && $size > self::ORACLE_MAX_CHUNK_SIZE )
            $size = self::ORACLE_MAX_CHUNK_SIZE;
        return max( 1, (int)$size );
    }

    /**
     * Sets the chunk size for every engine over the settings (Oracle still at most 1,000), or with null goes back to
     * the settings and reads them again. For tests and scripts.
     *
     * @param int|null $size
     */
    public static function setChunkSize( $size )
    {
        self::$fixedChunkSize = $size === null ? null : max( 1, (int)$size );
        self::$chunkSizes = array();
    }

    /**
     * The values made unique and cast to the type, in lists of at most chunkSize() values.
     *
     * @param array $values
     * @param string $type 'int' (default) or 'string'
     * @param eZDBInterface|null $db
     * @return array[] no list when $values is empty
     */
    public static function chunks( array $values, $type = 'int', $db = null )
    {
        if ( !$values )
            return array();
        $unique = array();
        foreach ( $values as $value )
        {
            if ( $type === 'int' )
                $value = (int)$value;
            else
                $value = (string)$value;
            $unique[$type === 'int' ? $value : 's' . $value] = $value;
        }
        return array_chunk( array_values( $unique ), self::chunkSize( $db ) );
    }

    /**
     * The IN condition of one list, as the database's generateSQLINStatement() writes it; strings are escaped and
     * quoted.
     *
     * @param eZDBInterface $db
     * @param array $chunk at most chunkSize() values (see chunks())
     * @param string $column
     * @param string $type 'int' or 'string'
     * @param bool $not NOT IN
     * @return string
     */
    public static function condition( $db, array $chunk, $column, $type = 'int', $not = false )
    {
        if ( $type === 'int' )
            return $db->generateSQLINStatement( array_map( 'intval', $chunk ), $column, $not, false, 'int' );
        $quoted = array();
        foreach ( $chunk as $value )
            $quoted[] = "'" . $db->escapeString( (string)$value ) . "'";
        return $db->generateSQLINStatement( $quoted, $column, $not, false, false );
    }

    /**
     * The rows of "$select <column> IN ( ... ) $tail" for all the values, one statement per list.
     *
     * @param eZDBInterface|null $db
     * @param string $select the statement up to and including WHERE (or AND)
     * @param array $values
     * @param string $column the column the values are looked up in
     * @param string $type 'int' (default) or 'string'
     * @param string $tail what follows the IN list (AND ..., GROUP BY ..., ORDER BY ...: per list)
     * @return array|false the rows of all lists in list order; an empty array for no values; false when a statement
     *                     fails
     */
    public static function rows( $db, $select, array $values, $column, $type = 'int', $tail = '' )
    {
        $db = $db ?: eZDB::instance();
        $rows = array();
        foreach ( self::chunks( $values, $type, $db ) as $chunk )
        {
            $chunkRows = $db->arrayQuery( self::statement( $select, self::condition( $db, $chunk, $column, $type ), $tail ) );
            if ( !is_array( $chunkRows ) )
                return false;
            foreach ( $chunkRows as $row )
                $rows[] = $row;
        }
        return $rows;
    }

    /**
     * One field of the rows of rows(), for example the ids a query selects.
     *
     * @param eZDBInterface|null $db
     * @param string $select
     * @param array $values
     * @param string $column
     * @param string $field the field of each row to return
     * @param string $type
     * @param string $tail
     * @return array|false
     */
    public static function column( $db, $select, array $values, $column, $field, $type = 'int', $tail = '' )
    {
        $rows = self::rows( $db, $select, $values, $column, $type, $tail );
        if ( $rows === false )
            return false;
        $result = array();
        foreach ( $rows as $row )
        {
            if ( is_array( $row ) && array_key_exists( $field, $row ) )
                $result[] = $row[$field];
        }
        return $result;
    }

    /**
     * The number of rows of "SELECT COUNT(*) AS count $from <column> IN ( ... ) $tail" for all the values: the sum of
     * the counts of the lists.
     *
     * @param eZDBInterface|null $db
     * @param string $from the statement after SELECT COUNT(*), up to and including WHERE (or AND): "FROM t WHERE"
     * @param array $values
     * @param string $column
     * @param string $type
     * @param string $tail AND ... (no GROUP BY: the counts are summed)
     * @return int|false 0 for no values; false when a statement fails
     */
    public static function count( $db, $from, array $values, $column, $type = 'int', $tail = '' )
    {
        $db = $db ?: eZDB::instance();
        $total = 0;
        foreach ( self::chunks( $values, $type, $db ) as $chunk )
        {
            $rows = $db->arrayQuery( self::statement( 'SELECT COUNT(*) AS count ' . $from, self::condition( $db, $chunk, $column, $type ), $tail ) );
            if ( !is_array( $rows ) || !isset( $rows[0] ) || !is_array( $rows[0] ) )
                return false;
            $row = array_change_key_case( $rows[0], CASE_LOWER );
            $total += (int)( isset( $row['count'] ) ? $row['count'] : reset( $row ) );
        }
        return $total;
    }

    /**
     * Runs a statement that returns no rows (UPDATE, DELETE) for all the values, one statement per list, and stops
     * at the first that fails. Not atomic across lists: run it in the caller's transaction when that matters.
     *
     * @param eZDBInterface|null $db
     * @param string $statement up to and including WHERE (or AND): "DELETE FROM t WHERE"
     * @param array $values
     * @param string $column
     * @param string $type
     * @param string $tail
     * @return bool true for no values
     */
    public static function query( $db, $statement, array $values, $column, $type = 'int', $tail = '' )
    {
        $db = $db ?: eZDB::instance();
        foreach ( self::chunks( $values, $type, $db ) as $chunk )
        {
            if ( $db->query( self::statement( $statement, self::condition( $db, $chunk, $column, $type ), $tail ) ) === false )
                return false;
        }
        return true;
    }

    /**
     * @param string $select
     * @param string $condition
     * @param string $tail
     * @return string
     */
    private static function statement( $select, $condition, $tail )
    {
        $tail = trim( (string)$tail );
        return rtrim( $select ) . ' ' . $condition . ( $tail !== '' ? ' ' . $tail : '' );
    }

    /**
     * @param eZDBInterface|null $db
     * @return string the engine name of databaseName() (mysql, postgresql, sqlite, oracle, mongo, pdo_<driver>)
     */
    private static function engine( $db )
    {
        if ( $db === null )
            $db = eZDB::instance();
        return is_object( $db ) && method_exists( $db, 'databaseName' ) ? strtolower( (string)$db->databaseName() ) : '';
    }

    /**
     * @param string $engine
     * @return int the chunk size site.ini gives the engine, DEFAULT_CHUNK_SIZE without a valid setting
     */
    private static function configuredChunkSize( $engine )
    {
        if ( !class_exists( 'eZINI' ) )
            return self::DEFAULT_CHUNK_SIZE;
        $ini = eZINI::instance( 'site.ini' );
        $size = self::DEFAULT_CHUNK_SIZE;
        if ( $ini->hasVariable( 'DatabaseSettings', 'InListChunkSize' ) && (int)$ini->variable( 'DatabaseSettings', 'InListChunkSize' ) > 0 )
            $size = (int)$ini->variable( 'DatabaseSettings', 'InListChunkSize' );
        if ( $ini->hasVariable( 'DatabaseSettings', 'InListChunkSizes' ) )
        {
            $sizes = $ini->variable( 'DatabaseSettings', 'InListChunkSizes' );
            if ( is_array( $sizes ) && isset( $sizes[$engine] ) && (int)$sizes[$engine] > 0 )
                $size = (int)$sizes[$engine];
        }
        return $size;
    }
}

<?php
/**
 * File containing the expUserLoginIntegrity class
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 826e1d4647), Copyright (C) Mugo Web, GPL v2
 */

/**
 * One account per login: finds the logins more than one ezuser row has, adds the unique index on
 * ezuser.login once there are none, and stores an account without letting a taken login stop the request.
 *
 * Two kinds of duplicate are found, on every engine:
 *
 * - rows the unique index would refuse, compared the way the engine compares the column (GROUP BY login):
 *   on MySQL and MariaDB with the column's collation, which as shipped (utf8mb4_general_ci and the like)
 *   ignores case and trailing spaces; on PostgreSQL, SQLite and Oracle byte for byte; on MongoDB byte for
 *   byte as well. A login Oracle stores as NULL (an empty string) never blocks the index.
 * - logins that differ only in case (GROUP BY LOWER( login )). eZUser::fetchByName() and signing in compare
 *   LOWER( login ), so one of those accounts can never be reached by its login. On MySQL with a case
 *   insensitive collation these are the same groups as the first kind; elsewhere the index would accept
 *   them, and the report still lists them, because they are the same login to Exponential.
 *
 * A new installation gets the unique index from the kernel schema. An existing one gets it only through
 * addUniqueIndex() (./console exp:user-login-check --fix-index), which refuses while duplicates() lists any
 * group: the duplicates are resolved by hand first (one account renamed or removed), never automatically.
 *
 * Holds no state: every call asks the database again, so a persistent worker carries nothing over.
 *
 * @package kernel
 */
class expUserLoginIntegrity
{
    /** The name of the index in the kernel schema, on every engine */
    const INDEX_NAME = 'ezuser_login';

    /**
     * The logins more than one account has.
     *
     * @param eZDBInterface|null $db the database (eZDB::instance() when null)
     * @return array<int, array{login: string, user_ids: int[], logins: string[], blocks_index: bool}> one entry
     *         per group: a login of the group, the content object ids of its accounts, the spellings the group
     *         has, and whether the unique index on this engine refuses the group (false: the logins differ only
     *         in case, which the index of this engine accepts). Sorted by login.
     */
    public static function duplicates( $db = null )
    {
        $db = $db ?: eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
            return self::groupRows( self::allRows( $db ), true );

        $groups = array();
        // logins equal to LOWER( login ), the comparison of fetchByName()
        $rows = $db->arrayQuery( 'SELECT LOWER( login ) AS login_key, COUNT(*) AS row_count FROM ezuser WHERE login IS NOT NULL ' .
                                 'GROUP BY LOWER( login ) HAVING COUNT(*) > 1' );
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $members = $db->arrayQuery( "SELECT contentobject_id, login FROM ezuser WHERE LOWER( login ) = '" .
                                        $db->escapeString( (string)$row['login_key'] ) . "' ORDER BY contentobject_id" );
            self::mergeGroup( $groups, $members, false );
        }
        // logins equal by the column's own comparison: what the unique index refuses
        $rows = $db->arrayQuery( 'SELECT login AS login_key, COUNT(*) AS row_count FROM ezuser WHERE login IS NOT NULL ' .
                                 'GROUP BY login HAVING COUNT(*) > 1' );
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $members = $db->arrayQuery( "SELECT contentobject_id, login FROM ezuser WHERE login = '" .
                                        $db->escapeString( (string)$row['login_key'] ) . "' ORDER BY contentobject_id" );
            self::mergeGroup( $groups, $members, true );
        }
        return self::sortGroups( $groups );
    }

    /**
     * The groups of $rows (contentobject_id, login): byte-equal logins block the index, logins equal in lower
     * case are reported. Used for MongoDB, whose unique index compares bytes, and by the tests.
     *
     * @param array[] $rows
     * @param bool $sorted
     * @return array[] as duplicates() returns
     */
    public static function groupRows( array $rows, $sorted = true )
    {
        $byExact = array();
        $byLower = array();
        foreach ( $rows as $row )
        {
            if ( !isset( $row['login'] ) || $row['login'] === null )
                continue;
            $login = (string)$row['login'];
            $byExact[$login][] = $row;
            $byLower[function_exists( 'mb_strtolower' ) ? mb_strtolower( $login, 'UTF-8' ) : strtolower( $login )][] = $row;
        }
        $groups = array();
        foreach ( $byLower as $members )
            if ( count( $members ) > 1 )
                self::mergeGroup( $groups, $members, false );
        foreach ( $byExact as $members )
            if ( count( $members ) > 1 )
                self::mergeGroup( $groups, $members, true );
        return $sorted ? self::sortGroups( $groups ) : $groups;
    }

    /**
     * The index on ezuser.login as the database has it.
     *
     * @param eZDBInterface|null $db
     * @return array{engine: string, exists: bool, unique: bool, names: string[]} names of the indexes on the
     *         column login alone; unique when one of them is unique
     */
    public static function indexStatus( $db = null )
    {
        $db = $db ?: eZDB::instance();
        $status = array( 'engine' => (string)$db->databaseName(), 'exists' => false, 'unique' => false, 'names' => array() );
        foreach ( self::loginIndexes( $db ) as $name => $unique )
        {
            $status['exists'] = true;
            $status['names'][] = $name;
            if ( $unique )
                $status['unique'] = true;
        }
        return $status;
    }

    /**
     * Adds the unique index on ezuser.login, replacing the plain index of that column. Refuses while
     * duplicates() lists any group, and changes nothing then.
     *
     * @param eZDBInterface|null $db
     * @param string|null $message set to what was done, or why nothing was done
     * @return bool the unique index is there (added now, or already)
     */
    public static function addUniqueIndex( $db = null, &$message = null )
    {
        $db = $db ?: eZDB::instance();
        $duplicates = self::duplicates( $db );
        if ( $duplicates )
        {
            $message = 'refused: ' . count( $duplicates ) . ' login(s) belong to more than one account; resolve them first';
            return false;
        }
        $indexes = self::loginIndexes( $db );
        if ( in_array( true, $indexes, true ) )
        {
            $message = 'the unique index is there already (' . implode( ', ', array_keys( array_filter( $indexes ) ) ) . ')';
            return true;
        }

        $engine = (string)$db->databaseName();
        try
        {
            switch ( $engine )
            {
                case 'mysql':
                    // one statement: the column is never without an index, and a failure leaves the old one
                    $sql = 'ALTER TABLE ezuser';
                    $parts = array();
                    foreach ( array_keys( $indexes ) as $name )
                        $parts[] = ' DROP INDEX ' . self::identifier( $name );
                    $parts[] = ' ADD UNIQUE KEY ' . self::INDEX_NAME . ' ( login )';
                    self::run( $db, $sql . implode( ',', $parts ) );
                    break;
                case 'postgresql':
                case 'sqlite':
                    // DDL is transactional on both: the old index comes back when the new one fails
                    $name = $engine === 'sqlite' && $indexes ? (string)key( $indexes ) : self::INDEX_NAME;
                    self::run( $db, 'BEGIN' );
                    try
                    {
                        foreach ( array_keys( $indexes ) as $old )
                            self::run( $db, 'DROP INDEX ' . self::quoted( $old ) );
                        self::run( $db, 'CREATE UNIQUE INDEX ' . self::quoted( $name ) . ' ON ezuser ( login )' );
                        self::run( $db, 'COMMIT' );
                    }
                    catch ( Exception $e )
                    {
                        self::run( $db, 'ROLLBACK', false );
                        throw $e;
                    }
                    break;
                case 'oracle':
                    // DDL commits by itself, and Oracle refuses a second index on the same column list: the old
                    // index goes first. The duplicate check above is what makes the CREATE succeed; should it
                    // fail all the same, the message says so and the plain index is to be created again by hand
                    foreach ( array_keys( $indexes ) as $old )
                        self::run( $db, 'DROP INDEX ' . $old );
                    self::run( $db, 'CREATE UNIQUE INDEX ' . self::INDEX_NAME . ' ON ezuser ( login )' );
                    break;
                case 'mongo':
                    $collection = $db->getClient()->selectCollection( $db->DB, 'ezuser' );
                    foreach ( array_keys( $indexes ) as $old )
                        $collection->dropIndex( $old );
                    $collection->createIndex( array( 'login' => 1 ), array( 'unique' => true, 'name' => self::INDEX_NAME ) );
                    break;
                default:
                    $message = "refused: the database engine '$engine' is not known to this command";
                    return false;
            }
        }
        catch ( Exception $e )
        {
            $message = 'the index was not added: ' . $e->getMessage();
            return false;
        }

        if ( !in_array( true, self::loginIndexes( $db ), true ) )
        {
            $message = 'the index was not added: the database shows no unique index on ezuser.login afterwards';
            return false;
        }
        $message = 'added the unique index ' . self::INDEX_NAME . ' on ezuser.login' .
                   ( $indexes ? ' (replacing ' . implode( ', ', array_keys( $indexes ) ) . ')' : '' );
        return true;
    }

    /**
     * The content object id of the account other than $userID that has $login, compared as signing in does
     * (LOWER( login )), or null.
     *
     * @param string $login
     * @param int $userID
     * @return int|null
     */
    public static function loginTakenBy( $login, $userID )
    {
        $login = (string)$login;
        if ( trim( $login ) === '' )
            return null;
        $other = eZUser::fetchByName( $login );
        if ( $other instanceof eZUser && (int)$other->attribute( 'contentobject_id' ) !== (int)$userID )
            return (int)$other->attribute( 'contentobject_id' );
        return null;
    }

    /**
     * Stores the account unless another account has its login. A login that another account takes between the
     * check and the write (two requests creating the same account at once) is refused by the unique index;
     * outside a transaction that is caught here, logged and answered with false instead of the fatal error of
     * a failed query. Inside a transaction the database has rolled the transaction back already, and the
     * error takes its usual course (the error page with its transaction id).
     *
     * @param eZUser $user
     * @return bool the account was stored
     */
    public static function storeUser( eZUser $user )
    {
        $userID = (int)$user->attribute( 'contentobject_id' );
        $takenBy = self::loginTakenBy( $user->attribute( 'login' ), $userID );
        if ( $takenBy !== null )
        {
            eZDebug::writeError( "The account of user object $userID is not stored: the login is taken by user $takenBy", __METHOD__ );
            return false;
        }

        $db = eZDB::instance();
        if ( $db->transactionCounter() > 0 || !method_exists( $db, 'setErrorHandling' ) )
        {
            $user->store();
            return true;
        }

        $previous = self::errorHandling( $db );
        $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        $db->ErrorNumber = 0;
        try
        {
            $user->store();
            $stored = true;
            // SQLite reports a failed statement by returning false, in either mode
            if ( (int)$db->errorNumber() !== 0 && self::isDuplicateKeyError( $db, $db->errorNumber(), $db->errorMessage() ) )
                $stored = false;
        }
        catch ( eZDBException $e )
        {
            if ( !self::isDuplicateKeyError( $db, $e->getCode(), isset( $e->originalMessage ) ? $e->originalMessage : $e->getMessage() ) )
            {
                $db->setErrorHandling( $previous );
                throw $e;
            }
            $stored = false;
        }
        $db->setErrorHandling( $previous );
        if ( !$stored )
            eZDebug::writeError( "The account of user object $userID is not stored: another account took its login at the same time", __METHOD__ );
        return $stored;
    }

    /**
     * Whether a database error is the refusal of a unique index.
     *
     * @param eZDBInterface $db
     * @param int|string $code
     * @param string $message
     * @return bool
     */
    public static function isDuplicateKeyError( $db, $code, $message )
    {
        $message = (string)$message;
        switch ( (string)$db->databaseName() )
        {
            case 'mysql':
                return (int)$code === 1062 || stripos( $message, 'Duplicate entry' ) !== false;
            case 'postgresql':
                return (string)$code === '23505' || stripos( $message, 'duplicate key value' ) !== false;
            case 'sqlite':
                return stripos( $message, 'UNIQUE constraint failed' ) !== false || stripos( $message, 'is not unique' ) !== false;
            case 'oracle':
                return (int)$code === 1 || stripos( $message, 'ORA-00001' ) !== false;
            case 'mongo':
                return (int)$code === 11000 || stripos( $message, 'E11000' ) !== false;
        }
        return false;
    }

    /**
     * The indexes on the column login alone, name => unique.
     *
     * @param eZDBInterface $db
     * @return array<string, bool>
     */
    private static function loginIndexes( $db )
    {
        $indexes = array();
        switch ( (string)$db->databaseName() )
        {
            case 'mysql':
                $byName = array();
                foreach ( (array)$db->arrayQuery( 'SHOW INDEX FROM ezuser' ) as $row )
                {
                    $row = array_change_key_case( $row, CASE_LOWER );
                    $byName[$row['key_name']]['columns'][] = strtolower( (string)$row['column_name'] );
                    $byName[$row['key_name']]['unique'] = !(int)$row['non_unique'];
                }
                foreach ( $byName as $name => $index )
                    if ( $name !== 'PRIMARY' && $index['columns'] === array( 'login' ) )
                        $indexes[$name] = $index['unique'];
                break;
            case 'postgresql':
                foreach ( (array)$db->arrayQuery( "SELECT indexname, indexdef FROM pg_indexes WHERE tablename = 'ezuser'" ) as $row )
                    if ( preg_match( '/\(\s*"?login"?\s*\)\s*$/i', (string)$row['indexdef'] ) )
                        $indexes[$row['indexname']] = stripos( (string)$row['indexdef'], 'CREATE UNIQUE' ) === 0;
                break;
            case 'sqlite':
                foreach ( (array)$db->arrayQuery( 'PRAGMA index_list(ezuser)' ) as $row )
                {
                    $columns = array();
                    foreach ( (array)$db->arrayQuery( 'PRAGMA index_info(' . self::quoted( $row['name'] ) . ')' ) as $column )
                        $columns[] = strtolower( (string)$column['name'] );
                    if ( $columns === array( 'login' ) && strncmp( $row['name'], 'sqlite_autoindex_', 17 ) !== 0 )
                        $indexes[$row['name']] = (int)$row['unique'] === 1;
                }
                break;
            case 'oracle':
                $rows = $db->arrayQuery( "SELECT i.index_name, i.uniqueness FROM user_indexes i WHERE i.table_name = 'EZUSER' " .
                                         "AND ( SELECT COUNT(*) FROM user_ind_columns c WHERE c.index_name = i.index_name ) = 1 " .
                                         "AND EXISTS ( SELECT 1 FROM user_ind_columns c WHERE c.index_name = i.index_name AND c.column_name = 'LOGIN' )" );
                foreach ( (array)$rows as $row )
                {
                    $row = array_change_key_case( $row, CASE_LOWER );
                    $indexes[strtolower( $row['index_name'] )] = strtoupper( (string)$row['uniqueness'] ) === 'UNIQUE';
                }
                break;
            case 'mongo':
                foreach ( $db->getClient()->selectCollection( $db->DB, 'ezuser' )->listIndexes() as $index )
                    if ( array_keys( (array)$index->getKey() ) === array( 'login' ) )
                        $indexes[$index->getName()] = (bool)$index->isUnique();
                break;
        }
        return $indexes;
    }

    /** @return array[] contentobject_id and login of every account (MongoDB) */
    private static function allRows( $db )
    {
        $rows = $db->arrayQuery( 'SELECT contentobject_id, login FROM ezuser' );
        return is_array( $rows ) ? $rows : array();
    }

    /**
     * Adds the accounts of $members to the group that has one of them already, or as a new group.
     */
    private static function mergeGroup( array &$groups, $members, $blocksIndex )
    {
        $ids = array();
        $logins = array();
        foreach ( is_array( $members ) ? $members : array() as $member )
        {
            $ids[] = (int)$member['contentobject_id'];
            $logins[] = (string)$member['login'];
        }
        if ( count( $ids ) < 2 )
            return;
        foreach ( $groups as $key => $group )
        {
            if ( array_intersect( $group['user_ids'], $ids ) )
            {
                $groups[$key]['user_ids'] = array_values( array_unique( array_merge( $group['user_ids'], $ids ) ) );
                sort( $groups[$key]['user_ids'] );
                $groups[$key]['logins'] = array_values( array_unique( array_merge( $group['logins'], $logins ) ) );
                $groups[$key]['blocks_index'] = $group['blocks_index'] || $blocksIndex;
                return;
            }
        }
        sort( $ids );
        $groups[] = array( 'login' => $logins[0], 'user_ids' => $ids, 'logins' => array_values( array_unique( $logins ) ),
                           'blocks_index' => (bool)$blocksIndex );
    }

    private static function sortGroups( array $groups )
    {
        usort( $groups, function ( $a, $b ) { return strcmp( strtolower( $a['login'] ), strtolower( $b['login'] ) ) ?: $a['user_ids'][0] - $b['user_ids'][0]; } );
        return $groups;
    }

    /** Runs one statement and turns a failure into an exception, whatever the error handling mode is */
    private static function run( $db, $sql, $throw = true )
    {
        $previous = method_exists( $db, 'setErrorHandling' ) ? self::errorHandling( $db ) : null;
        if ( $previous !== null )
            $db->setErrorHandling( eZDB::ERROR_HANDLING_EXCEPTIONS );
        try
        {
            $result = $db->query( $sql );
        }
        catch ( Exception $e )
        {
            $result = false;
            $error = $e->getMessage();
        }
        if ( $previous !== null )
            $db->setErrorHandling( $previous );
        if ( $result === false && $throw )
            throw new RuntimeException( isset( $error ) ? $error : "$sql: " . $db->errorMessage() );
        return $result;
    }

    /** The error handling mode of $db, which eZDBInterface keeps protected */
    private static function errorHandling( $db )
    {
        if ( !$db instanceof eZDBInterface || !property_exists( $db, 'errorHandling' ) )
            return eZDB::ERROR_HANDLING_STANDARD;
        $property = new ReflectionProperty( 'eZDBInterface', 'errorHandling' );
        return $property->getValue( $db );
    }

    private static function identifier( $name )
    {
        return '`' . str_replace( '`', '``', (string)$name ) . '`';
    }

    private static function quoted( $name )
    {
        return '"' . str_replace( '"', '""', (string)$name ) . '"';
    }
}

<?php
/**
 * Clean up of the REST live tests (ApiKeyLiveTest, RestContentPermissionLiveTest): what a run made is removed again
 * whether its tests pass, fail or its setUpBeforeClass() stops halfway, and what an earlier run left behind (a crash,
 * a killed process) is swept away at the start of the next one.
 *
 * Every throwaway thing of a run carries the run id, made by runID(): the start time (8 hex digits) and 4 random hex
 * digits. A sweep matches by name prefix only (roles, user groups and REST applications by their name, users by their
 * login, the throwaway root folder by "k1c <test class> ") and removes only what is older than STALE_AFTER seconds,
 * so a run of another process that is still going is left alone. Age comes from the run id in the name, else from the
 * object's publication or the row's creation time. A role of an older run id format (10 hex digits) is as old as its
 * user group of the same name, and stale when that group is gone (a run makes the group before the role).
 *
 * Removal goes through the kernel APIs: eZRole::removeThis() (with its policies, their limitations and the editing
 * copies), the content tree removal and purge (users with their accounts), the persistent session for the REST
 * applications, eZPersistentObject for the keys.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expRestLiveTestCleanup
{
    /** seconds after which a throwaway thing of another run counts as left behind */
    const STALE_AFTER = 3600;

    /**
     * A run id: the start time in 8 hex digits and 4 random ones, e.g. "6a1f3c2b9e04".
     */
    public static function runID()
    {
        return sprintf( '%08x', time() ) . substr( md5( uniqid( '', true ) ), 0, 4 );
    }

    /**
     * The start time a run id carries, or null for an id without one (the older 10 digit format).
     */
    public static function runTime( $runID )
    {
        if ( !is_string( $runID ) || !preg_match( '/^[0-9a-f]{12}$/', $runID ) )
            return null;
        $time = (int)hexdec( substr( $runID, 0, 8 ) );
        // 2026-01-01 .. tomorrow: anything else is not a time this helper wrote
        return $time >= 1767225600 && $time <= time() + 86400 ? $time : null;
    }

    /**
     * Finds the throwaway things of the REST live tests.
     *
     * @param array $spec 'name' => prefix of roles, user groups and REST application names,
     *                    'login' => prefix of user logins, 'root' => prefix of the throwaway root folder names,
     *                    'client' => prefix of REST application client ids (optional),
     *                    'keys' => prefixes of key names whose owner no longer exists (optional),
     *                    'run' => a run id: only the things of that run, at any age (optional; $olderThan is ignored)
     * @param int $olderThan only what is older than this many seconds; 0 for everything
     * @return array 'roles' => id => name, 'objects' => id => name, 'users' => id[], 'clients' => id => client id,
     *               'keys' => id => name
     */
    public static function find( array $spec, $olderThan = self::STALE_AFTER )
    {
        $db = eZDB::instance();
        $cutoff = $olderThan > 0 ? time() - (int)$olderThan : PHP_INT_MAX;
        $like = function ( $prefix ) use ( $db ) {
            return "'" . $db->escapeString( $prefix ) . "%'";
        };
        $found = array( 'roles' => array(), 'objects' => array(), 'users' => array(), 'clients' => array(), 'keys' => array() );
        // one run ('run' => its id): exactly its names, whatever their age; else what is older than the cutoff
        $run = isset( $spec['run'] ) ? (string)$spec['run'] : null;
        $take = function ( $runID, $time ) use ( $run, $cutoff ) {
            if ( $run !== null )
                return $runID === $run;
            $runTime = static::runTime( $runID );
            return ( $runTime !== null ? $runTime : (int)$time ) < $cutoff;
        };

        // content: the user groups (name prefix) and the throwaway root folders, in every status (drafts, trash too)
        foreach ( array( 'name', 'root' ) as $part )
        {
            if ( empty( $spec[$part] ) )
                continue;
            foreach ( $db->arrayQuery( 'SELECT id, name, published, modified FROM ezcontentobject WHERE name LIKE ' . $like( $spec[$part] ) ) as $row )
            {
                if ( strpos( $row['name'], $spec[$part] ) !== 0 )
                    continue;
                $time = (int)$row['published'] ?: (int)$row['modified'];
                if ( $take( substr( $row['name'], strlen( $spec[$part] ) ), $time ) )
                    $found['objects'][(int)$row['id']] = $row['name'];
            }
        }

        // users by login: the prefix and a run id, nothing else
        if ( !empty( $spec['login'] ) )
        {
            foreach ( $db->arrayQuery( 'SELECT u.contentobject_id, u.login, o.published FROM ezuser u LEFT JOIN ezcontentobject o ON o.id = u.contentobject_id'
                                       . ' WHERE u.login LIKE ' . $like( $spec['login'] ) ) as $row )
            {
                $runID = substr( $row['login'], strlen( $spec['login'] ) );
                if ( strpos( $row['login'], $spec['login'] ) !== 0 || !preg_match( '/^[0-9a-f]{10,12}$/', $runID ) )
                    continue;
                if ( $take( $runID, $row['published'] ) )
                {
                    $found['users'][] = (int)$row['contentobject_id'];
                    $found['objects'][(int)$row['contentobject_id']] = 'user ' . (int)$row['contentobject_id'];
                }
            }
        }

        // roles, with their editing copies (the same name)
        if ( !empty( $spec['name'] ) )
        {
            foreach ( $db->arrayQuery( 'SELECT id, name FROM ezrole WHERE name LIKE ' . $like( $spec['name'] ) ) as $row )
            {
                if ( strpos( $row['name'], $spec['name'] ) !== 0 )
                    continue;
                $runID = substr( $row['name'], strlen( $spec['name'] ) );
                $time = 0;
                if ( $run === null && static::runTime( $runID ) === null )
                {
                    $group = $db->arrayQuery( "SELECT published FROM ezcontentobject WHERE name = '" . $db->escapeString( $row['name'] ) . "'" );
                    $time = $group ? (int)$group[0]['published'] : 0;
                }
                if ( $take( $runID, $time ) )
                    $found['roles'][(int)$row['id']] = $row['name'];
            }
        }

        // REST applications (by name or client id) with their tokens and authorizations
        if ( static::hasTable( 'ezprest_clients' ) && ( !empty( $spec['name'] ) || !empty( $spec['client'] ) ) )
        {
            $conds = array();
            if ( !empty( $spec['name'] ) )
                $conds[] = 'name LIKE ' . $like( $spec['name'] );
            if ( !empty( $spec['client'] ) )
                $conds[] = 'client_id LIKE ' . $like( $spec['client'] );
            foreach ( $db->arrayQuery( 'SELECT id, client_id, name, created FROM ezprest_clients WHERE ' . implode( ' OR ', $conds ) ) as $row )
            {
                $byName = !empty( $spec['name'] ) && strpos( (string)$row['name'], $spec['name'] ) === 0;
                $byClient = !empty( $spec['client'] ) && preg_match( '/^' . preg_quote( $spec['client'], '/' ) . '[0-9a-f]{10,12}$/', (string)$row['client_id'] );
                if ( !$byName && !$byClient )
                    continue;
                $runID = $byClient ? substr( $row['client_id'], strlen( $spec['client'] ) ) : substr( (string)$row['name'], strlen( $spec['name'] ) );
                if ( $take( $runID, $row['created'] ) )
                    $found['clients'][(int)$row['id']] = (string)$row['client_id'];
            }
        }

        // keys: every key of a found user, and keys of a test name whose owner is gone
        if ( static::hasTable( 'expapikey' ) )
        {
            if ( $found['users'] )
                foreach ( $db->arrayQuery( 'SELECT id, name FROM expapikey WHERE ' . $db->generateSQLINStatement( $found['users'], 'user_id', false, true, 'int' ) ) as $row )
                    $found['keys'][(int)$row['id']] = $row['name'];
            foreach ( isset( $spec['keys'] ) && $run === null ? (array)$spec['keys'] : array() as $prefix )
                foreach ( $db->arrayQuery( 'SELECT k.id, k.name, k.created FROM expapikey k LEFT JOIN ezuser u ON u.contentobject_id = k.user_id'
                                           . ' WHERE u.contentobject_id IS NULL AND k.name LIKE ' . $like( $prefix ) ) as $row )
                    if ( strpos( $row['name'], $prefix ) === 0 && (int)$row['created'] < $cutoff )
                        $found['keys'][(int)$row['id']] = $row['name'];
        }
        return $found;
    }

    /**
     * Removes what find() found, or what a run tracked. Each step runs on its own: one that fails does not keep the
     * others from running.
     *
     * @param array $what 'roles' => id[] or id => name, 'objects' => (as roles), 'users' => id[], 'clients' => id[] or
     *                    id => client id, 'keys' => (as roles)
     * @return string[] what could not be removed
     */
    public static function remove( array $what )
    {
        $errors = array();
        $ids = function ( $key, $byKey = true ) use ( $what ) {
            if ( empty( $what[$key] ) )
                return array();
            $list = $what[$key];
            return array_values( array_unique( array_map( 'intval', $byKey && array_keys( $list ) !== range( 0, count( $list ) - 1 ) ? array_keys( $list ) : $list ) ) );
        };
        $step = function ( $label, callable $fn ) use ( &$errors ) {
            $db = eZDB::instance();
            try
            {
                $fn();
            }
            catch ( Throwable $e )
            {
                $errors[] = $label . ': ' . $e->getMessage();
            }
            // a step that failed inside a transaction must not take the following steps down with it
            while ( $db->transactionCounter() > 0 )
                $db->rollback();
        };
        $db = eZDB::instance();

        $users = $ids( 'users', false );
        $step( 'keys', function () use ( $db, $ids, $users ) {
            if ( !static::hasTable( 'expapikey' ) )
                return;
            $keyIDs = $ids( 'keys' );
            if ( $users )
                foreach ( $db->arrayQuery( 'SELECT id FROM expapikey WHERE ' . $db->generateSQLINStatement( $users, 'user_id', false, true, 'int' ) ) as $row )
                    $keyIDs[] = (int)$row['id'];
            foreach ( array_unique( $keyIDs ) as $keyID )
                eZPersistentObject::removeObject( expApiKey::definition(), array( 'id' => (int)$keyID ) );
        } );

        $step( 'REST applications', function () use ( $db, $ids ) {
            $clientRowIDs = $ids( 'clients' );
            if ( !$clientRowIDs || !static::hasTable( 'ezprest_clients' ) )
                return;
            ezpRestDbConfig::registerCallbacks();
            $session = ezcPersistentSessionInstance::get();
            foreach ( $clientRowIDs as $rowID )
            {
                $row = $db->arrayQuery( 'SELECT client_id FROM ezprest_clients WHERE id = ' . (int)$rowID );
                if ( !$row )
                    continue;
                $clientID = $db->escapeString( (string)$row[0]['client_id'] );
                $db->begin();
                $db->query( 'DELETE FROM ezprest_authorized_clients WHERE rest_client_id = ' . (int)$rowID );
                $db->query( "DELETE FROM ezprest_token WHERE client_id = '$clientID'" );
                if ( static::hasTable( 'ezprest_authcode' ) )
                    $db->query( "DELETE FROM ezprest_authcode WHERE client_id = '$clientID'" );
                $db->commit();
                $session->delete( $session->load( 'ezpRestClient', (int)$rowID ) );
            }
        } );

        $step( 'roles', function () use ( $ids ) {
            foreach ( $ids( 'roles' ) as $roleID )
            {
                $role = eZRole::fetch( $roleID );
                if ( $role instanceof eZRole )
                    $role->removeThis();
            }
            eZRole::expireCache();
        } );

        $step( 'sessions', function () use ( $users ) {
            foreach ( $users as $userID )
            {
                eZUser::removeSessionData( $userID );
                eZUser::purgeUserCacheByUserId( $userID );
            }
        } );

        $step( 'content', function () use ( $ids ) {
            static::removeObjects( $ids( 'objects' ) );
        } );
        return $errors;
    }

    /**
     * Removes what earlier runs left behind (older than $olderThan seconds); returns what it removed, as find() does.
     */
    public static function sweep( array $spec, $olderThan = self::STALE_AFTER )
    {
        $found = static::find( $spec, $olderThan );
        if ( array_filter( $found ) )
        {
            $errors = static::remove( $found );
            if ( $errors )
                fwrite( STDERR, 'the sweep of earlier REST live test runs left something: ' . implode( '; ', $errors ) . "\n" );
        }
        return $found;
    }

    /**
     * Removes content objects with every location, and from the trash (as expContentModelLiveTestCase does).
     */
    public static function removeObjects( array $ids )
    {
        $ids = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
        if ( !$ids )
            return;
        $db = eZDB::instance();
        $roots = array();
        foreach ( $db->arrayQuery( 'SELECT node_id FROM ezcontentobject_tree WHERE ' . $db->generateSQLINStatement( $ids, 'contentobject_id', false, true, 'int' ) ) as $r )
            $roots[] = (int)$r['node_id'];
        if ( $roots )
            eZContentObjectTreeNode::removeSubtrees( $roots, false );
        eZContentObject::clearCache();
        foreach ( $db->arrayQuery( 'SELECT id, status FROM ezcontentobject WHERE ' . $db->generateSQLINStatement( $ids, 'id', false, true, 'int' ) ) as $r )
        {
            $db->begin();
            if ( (int)$r['status'] === eZContentObject::STATUS_ARCHIVED )
                \Exponential\Service\Trash::purgeObjects( array( (int)$r['id'] ) );
            else if ( $object = eZContentObject::fetch( (int)$r['id'] ) )
                $object->purge();
            $db->commit();
        }
        // a user whose object is gone but whose account row is not (an interrupted removal)
        foreach ( $ids as $id )
            if ( !eZContentObject::fetch( $id ) && eZUser::fetch( $id ) )
                eZUser::removeUser( $id );
        eZContentObject::clearCache();
    }

    protected static function hasTable( $table )
    {
        // eZTableList() of some engines lists only the ez* tables; the key table has a check of its own
        if ( $table === 'expapikey' )
            return class_exists( 'expApiKeySchema' ) && expApiKeySchema::exists() === true;
        static $tables = null;
        if ( $tables === null )
            $tables = array_change_key_case( (array)eZDB::instance()->eZTableList(), CASE_LOWER );
        return isset( $tables[$table] );
    }
}

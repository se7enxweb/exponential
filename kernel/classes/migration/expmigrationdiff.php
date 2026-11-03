<?php
/**
 * File containing the expMigrationDiff class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * exp:migration:generate --from-diff=<caption|address|dump>: a YAML migration of what changed between a saved state
 * and the installation now. Run on the saved state, it makes the saved state like this installation.
 *
 * Both sides are read as a snapshot (snapshot()): per kind of thing a fingerprint (a SHA-1 of what a step would set)
 * by a stable key: classes, sections and object state groups by identifier, roles by name, languages by locale,
 * content of a subtree by the remote id of the object, settings of settings/override as "<file> [<block>] <variable>".
 * compare() tells what was added, changed and removed. What was added or changed is generated as the generators of
 * what is there generate it (expMigrationRecordingGenerator::fromExisting(): mode ensure, the same resolver, no
 * personal data); what was removed becomes a delete step (settings: remove). What no step type repeats goes in the
 * header as "to be done by hand".
 *
 * The saved state is one of:
 *  - a caption (Sicherungspunkt) by id (exp:migration:caption --list, exp:upgrade:captions): its database dump and its
 *    copy of settings/override
 *  - a database dump of the same schema: a SQLite file, or an SQL dump of this installation's engine (SQLite, MySQL or
 *    MariaDB, PostgreSQL), loaded into a throwaway database that is dropped afterwards
 *  - another installation's address (https://...): its snapshot read once through the read-only REST route
 *    /api/migration/v1/snapshot (policy migration/read, API key scope migration:read). Nothing is ever written there.
 *
 * Secret settings are left out of every snapshot (their fingerprint would say something about their value): set them
 * by hand. Role assignments are not compared (they name users).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationDiff
{
    /** The format of a snapshot. */
    const FORMAT = 'exp-migration-snapshot/1';

    /** What can be compared. */
    const KINDS = array( 'classes', 'roles', 'sections', 'states', 'languages', 'content', 'settings' );

    /** What is compared when --compare is not given (content with --subtree). */
    const DEFAULT_KINDS = array( 'classes', 'roles', 'sections', 'states', 'languages', 'settings' );

    /** The REST route of a snapshot below an installation's address. */
    const REST_PATH = '/api/migration/v1/snapshot';

    /** How many objects of a subtree are compared at most when no limit is given. */
    const DEFAULT_LIMIT = 500;

    /**
     * The kinds of --compare. Pure.
     *
     * @param string $value comma list; '' or all: the default kinds (and content when a subtree is given)
     * @param bool $subtree whether a subtree is given
     * @param string|null $error set when the list names something else
     * @return string[]
     */
    public static function kinds( $value, $subtree, &$error = null )
    {
        $error = null;
        $value = trim( (string)$value );
        if ( $value === '' || $value === 'all' )
            return $subtree ? array_merge( self::DEFAULT_KINDS, array( 'content' ) ) : self::DEFAULT_KINDS;
        $kinds = array();
        foreach ( array_filter( array_map( 'trim', explode( ',', $value ) ), 'strlen' ) as $kind )
        {
            if ( !in_array( $kind, self::KINDS, true ) )
            {
                $error = "--compare takes " . implode( ', ', self::KINDS ) . ", not $kind";
                return array();
            }
            $kinds[$kind] = true;
        }
        if ( isset( $kinds['content'] ) && !$subtree )
        {
            $error = '--compare=content needs --subtree=<node>: the content of which subtree';
            return array();
        }
        return array_values( array_intersect( self::KINDS, array_keys( $kinds ) ) );
    }

    // ------------------------------------------------------------------ snapshot

    /**
     * A query function of a connection: SQL => rows with lower-case keys.
     *
     * @param eZDBInterface|SQLite3 $db
     * @return callable
     */
    public static function queryOf( $db )
    {
        if ( $db instanceof SQLite3 )
        {
            return function ( $sql ) use ( $db ) {
                $result = @$db->query( $sql );
                if ( !$result )
                    throw new RuntimeException( 'the saved database cannot be read: ' . $db->lastErrorMsg() );
                $rows = array();
                while ( ( $row = $result->fetchArray( SQLITE3_ASSOC ) ) !== false )
                    $rows[] = array_change_key_case( $row );
                $result->finalize();
                return $rows;
            };
        }
        return function ( $sql ) use ( $db ) {
            $rows = $db->arrayQuery( $sql );
            if ( !is_array( $rows ) )
                throw new RuntimeException( 'the database cannot be read: ' . $sql );
            return array_map( 'array_change_key_case', $rows );
        };
    }

    /**
     * The snapshot of one side.
     *
     * @param callable $query SQL => rows (queryOf())
     * @param string[] $kinds
     * @param array $options subtree (the remote id of the node whose subtree is compared), limit (objects, default
     *                       DEFAULT_LIMIT), settings_dir (the folder of the settings/override files; null: no settings)
     * @return array format, kinds (kind => key => fingerprint), parents (object remote id => parent object remote id),
     *               depths (object remote id => depth), missing (kinds this side cannot tell), errors
     */
    public static function snapshot( $query, array $kinds, array $options = array() )
    {
        $out = array( 'format' => self::FORMAT, 'kinds' => array(), 'parents' => array(), 'depths' => array(), 'missing' => array(), 'errors' => array() );
        foreach ( $kinds as $kind )
        {
            try
            {
                switch ( $kind )
                {
                    case 'classes':
                        $out['kinds'][$kind] = self::classes( $query );
                        break;
                    case 'roles':
                        $out['kinds'][$kind] = self::roles( $query );
                        break;
                    case 'sections':
                        $out['kinds'][$kind] = self::sections( $query );
                        break;
                    case 'states':
                        $out['kinds'][$kind] = self::states( $query );
                        break;
                    case 'languages':
                        $out['kinds'][$kind] = self::languages( $query );
                        break;
                    case 'content':
                        $subtree = isset( $options['subtree'] ) ? (string)$options['subtree'] : '';
                        $limit = isset( $options['limit'] ) ? max( 1, (int)$options['limit'] ) : self::DEFAULT_LIMIT;
                        $content = self::content( $query, $subtree, $limit );
                        if ( $content === null )
                            $out['missing'][] = 'content';
                        else
                        {
                            $out['kinds'][$kind] = $content['hashes'];
                            $out['parents'] = $content['parents'];
                            $out['depths'] = $content['depths'];
                        }
                        break;
                    case 'settings':
                        $dir = isset( $options['settings_dir'] ) ? $options['settings_dir'] : null;
                        if ( $dir === null || !is_dir( (string)$dir ) )
                            $out['missing'][] = 'settings';
                        else
                            $out['kinds'][$kind] = self::settings( (string)$dir );
                        break;
                }
            }
            catch ( Throwable $e )
            {
                $out['errors'][] = "$kind: " . $e->getMessage();
            }
        }
        return $out;
    }

    /** A fingerprint of a value. Pure. */
    public static function hash( $value )
    {
        return sha1( json_encode( self::normalised( $value ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
    }

    /** Numbers as numbers whatever the engine gives (1, "1", "1.0"), keys sorted. Pure. */
    protected static function normalised( $value )
    {
        if ( is_array( $value ) )
        {
            $out = array();
            foreach ( $value as $k => $v )
                $out[$k] = self::normalised( $v );
            ksort( $out );
            return $out;
        }
        if ( $value === null )
            return '';
        if ( is_bool( $value ) )
            return $value ? '1' : '0';
        $value = (string)$value;
        if ( is_numeric( $value ) && preg_match( '/^-?\d+(\.\d+)?([eE][-+]?\d+)?$/', $value ) )
            return (string)( 0 + $value );
        return $value;
    }

    /** classes: identifier => fingerprint */
    protected static function classes( $query )
    {
        $classes = array();
        foreach ( $query( 'SELECT id, identifier, contentobject_name, url_alias_name, is_container, always_available, sort_field, sort_order, '
                          . 'serialized_name_list, serialized_description_list FROM ezcontentclass WHERE version = 0' ) as $row )
            if ( (string)$row['identifier'] !== '' )
                $classes[(int)$row['id']] = array( 'class' => array_diff_key( $row, array( 'id' => 1 ) ), 'attributes' => array(), 'groups' => array() );
        foreach ( $query( 'SELECT contentclass_id, identifier, data_type_string, placement, is_required, is_searchable, can_translate, '
                          . 'is_information_collector, category, serialized_name_list, serialized_description_list, serialized_data_text, '
                          . 'data_int1, data_int2, data_int3, data_int4, data_float1, data_float2, data_float3, data_float4, '
                          . 'data_text1, data_text2, data_text3, data_text4, data_text5 FROM ezcontentclass_attribute WHERE version = 0' ) as $row )
            if ( isset( $classes[(int)$row['contentclass_id']] ) )
                $classes[(int)$row['contentclass_id']]['attributes'][(string)$row['identifier']] = array_diff_key( $row, array( 'contentclass_id' => 1 ) );
        foreach ( $query( 'SELECT contentclass_id, group_name FROM ezcontentclass_classgroup WHERE contentclass_version = 0' ) as $row )
            if ( isset( $classes[(int)$row['contentclass_id']] ) )
                $classes[(int)$row['contentclass_id']]['groups'][] = (string)$row['group_name'];
        $out = array();
        foreach ( $classes as $class )
        {
            sort( $class['groups'] );
            $out[(string)$class['class']['identifier']] = self::hash( $class );
        }
        ksort( $out );
        return $out;
    }

    /** roles: name => fingerprint of the policies and their limitations */
    protected static function roles( $query )
    {
        $roles = array();
        foreach ( $query( 'SELECT id, name FROM ezrole WHERE version = 0' ) as $row )
            $roles[(int)$row['id']] = array( 'name' => (string)$row['name'], 'policies' => array() );
        $policies = array();
        foreach ( $query( 'SELECT id, role_id, module_name, function_name FROM ezpolicy' ) as $row )
            if ( isset( $roles[(int)$row['role_id']] ) )
                $policies[(int)$row['id']] = array( 'role' => (int)$row['role_id'], 'what' => $row['module_name'] . '/' . $row['function_name'], 'limitations' => array() );
        $limitations = array();
        foreach ( $query( 'SELECT id, policy_id, identifier FROM ezpolicy_limitation' ) as $row )
            if ( isset( $policies[(int)$row['policy_id']] ) )
                $limitations[(int)$row['id']] = array( 'policy' => (int)$row['policy_id'], 'identifier' => (string)$row['identifier'] );
        foreach ( $query( 'SELECT limitation_id, value FROM ezpolicy_limitation_value' ) as $row )
        {
            $l = isset( $limitations[(int)$row['limitation_id']] ) ? $limitations[(int)$row['limitation_id']] : null;
            if ( $l !== null )
                $policies[$l['policy']]['limitations'][$l['identifier']][] = (string)$row['value'];
        }
        foreach ( $limitations as $l )
            if ( !isset( $policies[$l['policy']]['limitations'][$l['identifier']] ) )
                $policies[$l['policy']]['limitations'][$l['identifier']] = array();
        foreach ( $policies as $policy )
        {
            foreach ( $policy['limitations'] as &$values )
                sort( $values );
            unset( $values );
            ksort( $policy['limitations'] );
            $roles[$policy['role']]['policies'][] = $policy['what'] . ' ' . json_encode( $policy['limitations'] );
        }
        $out = array();
        foreach ( $roles as $role )
        {
            sort( $role['policies'] );
            $out[$role['name']] = self::hash( $role['policies'] );
        }
        ksort( $out );
        return $out;
    }

    /** sections: identifier => fingerprint */
    protected static function sections( $query )
    {
        $out = array();
        foreach ( $query( 'SELECT identifier, name, navigation_part_identifier FROM ezsection' ) as $row )
            if ( (string)$row['identifier'] !== '' )
                $out[(string)$row['identifier']] = self::hash( $row );
        ksort( $out );
        return $out;
    }

    /** object state groups: identifier => fingerprint of the states and the names */
    protected static function states( $query )
    {
        $groups = array();
        foreach ( $query( 'SELECT id, identifier FROM ezcobj_state_group' ) as $row )
            $groups[(int)$row['id']] = array( 'identifier' => (string)$row['identifier'], 'names' => array(), 'states' => array() );
        foreach ( $query( 'SELECT contentobject_state_group_id, name, description FROM ezcobj_state_group_language' ) as $row )
            if ( isset( $groups[(int)$row['contentobject_state_group_id']] ) )
                $groups[(int)$row['contentobject_state_group_id']]['names'][] = $row['name'] . "\n" . $row['description'];
        $states = array();
        foreach ( $query( 'SELECT id, group_id, identifier, priority FROM ezcobj_state' ) as $row )
            if ( isset( $groups[(int)$row['group_id']] ) )
                $states[(int)$row['id']] = array( 'group' => (int)$row['group_id'], 'identifier' => (string)$row['identifier'], 'priority' => $row['priority'], 'names' => array() );
        foreach ( $query( 'SELECT contentobject_state_id, name, description FROM ezcobj_state_language' ) as $row )
            if ( isset( $states[(int)$row['contentobject_state_id']] ) )
                $states[(int)$row['contentobject_state_id']]['names'][] = $row['name'] . "\n" . $row['description'];
        foreach ( $states as $state )
        {
            sort( $state['names'] );
            $groups[$state['group']]['states'][$state['identifier']] = array( $state['priority'], $state['names'] );
        }
        $out = array();
        foreach ( $groups as $group )
        {
            sort( $group['names'] );
            $out[$group['identifier']] = self::hash( array( $group['names'], $group['states'] ) );
        }
        ksort( $out );
        return $out;
    }

    /** languages: locale => fingerprint of the name */
    protected static function languages( $query )
    {
        $out = array();
        foreach ( $query( 'SELECT locale, name FROM ezcontent_language' ) as $row )
            $out[(string)$row['locale']] = self::hash( (string)$row['name'] );
        ksort( $out );
        return $out;
    }

    /**
     * content of a subtree: object remote id => fingerprint (modified, version, section, parent); null when the
     * subtree's node is not there.
     */
    protected static function content( $query, $nodeRemoteId, $limit )
    {
        if ( !preg_match( '/^[A-Za-z0-9_.:-]{1,100}$/', $nodeRemoteId ) )
            throw new RuntimeException( "not a remote id of a node: $nodeRemoteId" );
        $root = $query( "SELECT node_id, path_string FROM ezcontentobject_tree WHERE remote_id = '$nodeRemoteId'" );
        if ( !$root )
            return null;
        $path = (string)$root[0]['path_string'];
        if ( !preg_match( '#^[0-9/]+$#', $path ) )
            throw new RuntimeException( 'an unexpected path of the node' );
        $nodes = array();
        foreach ( $query( "SELECT node_id, parent_node_id, contentobject_id, depth, main_node_id FROM ezcontentobject_tree WHERE path_string LIKE '$path%'" ) as $row )
            $nodes[(int)$row['node_id']] = $row;
        $objects = array();
        foreach ( $nodes as $node )
            $objects[(int)$node['contentobject_id']] = true;
        if ( count( $objects ) > $limit )
            throw new RuntimeException( 'the subtree has ' . count( $objects ) . " objects, more than $limit (--max-objects)" );
        $rows = array();
        foreach ( array_chunk( array_keys( $objects ), 200 ) as $chunk )
            foreach ( $query( 'SELECT id, remote_id, modified, current_version, section_id FROM ezcontentobject WHERE status = 1 AND id IN (' . implode( ',', array_map( 'intval', $chunk ) ) . ')' ) as $row )
                $rows[(int)$row['id']] = $row;
        $remote = array();
        foreach ( $rows as $id => $row )
            $remote[$id] = (string)$row['remote_id'];
        $hashes = $parents = $depths = array();
        $rootNode = (int)$root[0]['node_id'];
        foreach ( $nodes as $nodeId => $node )
        {
            $objectId = (int)$node['contentobject_id'];
            if ( !isset( $rows[$objectId] ) || ( (int)$node['main_node_id'] !== $nodeId && (int)$node['main_node_id'] > 0 ) )
                continue;
            $parent = $nodeId === $rootNode || !isset( $nodes[(int)$node['parent_node_id']] ) ? '' : (string)( isset( $remote[(int)$nodes[(int)$node['parent_node_id']]['contentobject_id']] )
                                                                                                          ? $remote[(int)$nodes[(int)$node['parent_node_id']]['contentobject_id']] : '' );
            $rid = $remote[$objectId];
            $parents[$rid] = $parent;
            $depths[$rid] = (int)$node['depth'];
            $hashes[$rid] = self::hash( array( $rows[$objectId]['modified'], $rows[$objectId]['current_version'], $rows[$objectId]['section_id'], $parent ) );
        }
        ksort( $hashes );
        return array( 'hashes' => $hashes, 'parents' => $parents, 'depths' => $depths );
    }

    /**
     * settings of a settings/override folder: "<file> [<block>] <variable>" => fingerprint of the value; secrets left
     * out.
     *
     * @param string $dir
     * @return string[]
     */
    public static function settings( $dir )
    {
        $out = array();
        foreach ( self::settingValues( $dir ) as $key => $value )
            $out[$key] = self::hash( $value );
        ksort( $out );
        return $out;
    }

    /**
     * The values of a settings/override folder: "<file> [<block>] <variable>" => value; secrets left out.
     *
     * @param string $dir
     * @return array
     */
    public static function settingValues( $dir )
    {
        $out = array();
        foreach ( glob( rtrim( (string)$dir, '/' ) . '/*.ini.append*' ) ?: array() as $path )
        {
            if ( !preg_match( '/^([A-Za-z0-9_.-]+\.ini)\.append(\.php)?$/', basename( $path ), $m ) || !is_file( $path ) )
                continue;
            // read from that path alone (fetchFromFile() reads below the installation's root only)
            $ini = new eZINI( basename( $path ), false, null, false, false, true, false, false );
            $ini->parse( array( $path ), $path );
            foreach ( (array)$ini->groups() as $block => $variables )
                foreach ( (array)$variables as $variable => $value )
                {
                    if ( class_exists( 'expAuditPrivacy' ) && expAuditPrivacy::isSecretName( (string)$variable ) )
                        continue;
                    $out[$m[1] . ' [' . $block . '] ' . $variable] = $value;
                }
        }
        return $out;
    }

    // ------------------------------------------------------------------ compare

    /**
     * What differs between two snapshots, per kind both can tell. Pure.
     *
     * @param array $saved snapshot()
     * @param array $now snapshot()
     * @return array kind => array( added => keys, changed => keys, removed => keys )
     */
    public static function compare( array $saved, array $now )
    {
        $out = array();
        foreach ( self::KINDS as $kind )
        {
            if ( !isset( $saved['kinds'][$kind], $now['kinds'][$kind] ) )
                continue;
            $a = (array)$saved['kinds'][$kind];
            $b = (array)$now['kinds'][$kind];
            $changed = array();
            foreach ( array_intersect_key( $b, $a ) as $key => $hash )
                if ( (string)$a[$key] !== (string)$hash )
                    $changed[] = (string)$key;
            $out[$kind] = array( 'added' => array_map( 'strval', array_keys( array_diff_key( $b, $a ) ) ), 'changed' => $changed,
                                 'removed' => array_map( 'strval', array_keys( array_diff_key( $a, $b ) ) ) );
        }
        return $out;
    }

    /**
     * The things of a difference in the order a migration makes them: what is made or changed (languages, sections,
     * states, classes, roles, content parents first, settings), then what is removed (content, roles, classes, states,
     * sections, languages, settings). Content removed below another removed object is left to that removal. Pure.
     *
     * @param array $diff compare()
     * @param array $saved snapshot() (parents of the removed content)
     * @param array $now snapshot() (depths of the content made or changed)
     * @return array[] each: kind, key, change (added, changed, removed)
     */
    public static function order( array $diff, array $saved, array $now )
    {
        $out = array();
        foreach ( array( 'languages', 'sections', 'states', 'classes', 'roles', 'content', 'settings' ) as $kind )
        {
            if ( !isset( $diff[$kind] ) )
                continue;
            $keys = array();
            foreach ( array( 'added', 'changed' ) as $change )
                foreach ( $diff[$kind][$change] as $key )
                    $keys[$key] = $change;
            if ( $kind === 'content' )
                uksort( $keys, function ( $a, $b ) use ( $now ) {
                    $da = isset( $now['depths'][$a] ) ? $now['depths'][$a] : 0;
                    $db = isset( $now['depths'][$b] ) ? $now['depths'][$b] : 0;
                    return $da <=> $db ?: strcmp( $a, $b );
                } );
            foreach ( $keys as $key => $change )
                $out[] = array( 'kind' => $kind, 'key' => (string)$key, 'change' => $change );
        }
        foreach ( array( 'content', 'roles', 'classes', 'states', 'sections', 'languages', 'settings' ) as $kind )
        {
            if ( !isset( $diff[$kind] ) )
                continue;
            $removed = array_fill_keys( $diff[$kind]['removed'], true );
            foreach ( $diff[$kind]['removed'] as $key )
            {
                if ( $kind === 'content' )
                {
                    // below another removed object: removed with it
                    $up = isset( $saved['parents'][$key] ) ? (string)$saved['parents'][$key] : '';
                    $seen = array();
                    while ( $up !== '' && !isset( $seen[$up] ) )
                    {
                        if ( isset( $removed[$up] ) )
                            continue 2;
                        $seen[$up] = true;
                        $up = isset( $saved['parents'][$up] ) ? (string)$saved['parents'][$up] : '';
                    }
                }
                $out[] = array( 'kind' => $kind, 'key' => (string)$key, 'change' => 'removed' );
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ the migration

    /**
     * The report of exp:migration:generate --from-diff.
     *
     * @param string $from a caption id, an address (https://...) or the path of a database dump
     * @param array $options as expMigrationRecordingGenerator::fromRecording(), and compare (kinds, comma list),
     *                       subtree (a node of this installation), limit (objects), token (the API key of the other
     *                       installation: a secret name or env:VARIABLE), backupDir (of the captions), root,
     *                       recordingSource (tests), fetch (callable url, headers => body, tests)
     * @return array as fromRecording(), and source (what was compared with), diff (kind => counts), compared (kinds)
     */
    public static function fromDiff( $from, array $options = array() )
    {
        $report = array( 'mode' => 'generate-from-diff', 'recording' => null, 'source' => null, 'compared' => array(), 'diff' => array(),
                         'steps' => array(), 'plan' => array(), 'manual' => array(), 'missing_step_types' => array(), 'omitted' => array(),
                         'generated' => null, 'written' => false, 'errors' => array(), 'warnings' => array(), 'exit' => expUpgrade::EXIT_OK );
        $usage = function ( $message ) use ( &$report ) {
            $report['errors'][] = $message;
            $report['exit'] = expUpgrade::EXIT_USAGE;
            return $report;
        };
        $from = trim( (string)$from );
        if ( $from === '' )
            return $usage( '--from-diff takes a caption id, the address of another installation (https://...) or the path of a database dump' );
        $subtreeNode = isset( $options['subtree'] ) ? trim( (string)$options['subtree'] ) : '';
        $error = null;
        $kinds = self::kinds( isset( $options['compare'] ) ? (string)$options['compare'] : '', $subtreeNode !== '', $error );
        if ( $error !== null )
            return $usage( $error );
        $snapshotOptions = array( 'limit' => isset( $options['limit'] ) ? max( 1, (int)$options['limit'] ) : self::DEFAULT_LIMIT );
        if ( in_array( 'content', $kinds, true ) )
        {
            $nodeId = expMigrationIdentifier::find( 'node', $subtreeNode );
            $node = $nodeId === null ? null : eZContentObjectTreeNode::fetch( (int)$nodeId );
            if ( !$node instanceof eZContentObjectTreeNode )
                return $usage( "the node $subtreeNode: not found" );
            $snapshotOptions['subtree'] = (string)$node->attribute( 'remote_id' );
        }
        $root = isset( $options['root'] ) ? rtrim( (string)$options['root'], '/' ) : rtrim( eZSys::rootDir(), '/' );

        $now = self::snapshot( self::queryOf( eZDB::instance() ), $kinds, $snapshotOptions + array( 'settings_dir' => "$root/settings/override" ) );
        $saved = self::saved( $from, $kinds, $snapshotOptions, $options + array( 'root' => $root ), $report );
        if ( $saved === null )
        {
            if ( $report['exit'] === expUpgrade::EXIT_OK )
                $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        foreach ( array_merge( $now['errors'], $saved['errors'] ) as $problem )
            $report['errors'][] = $problem;
        if ( $report['errors'] )
        {
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        foreach ( array_unique( array_merge( (array)$saved['missing'], (array)$now['missing'] ) ) as $kind )
            $report['warnings'][] = $kind === 'settings' ? 'settings not compared: ' . $report['source']['kind'] . ' holds no copy of settings/override'
                                                        : "$kind not compared: the subtree's node is not on both sides";
        $diff = self::compare( $saved, $now );
        $report['compared'] = array_keys( $diff );
        foreach ( $diff as $kind => $lists )
            $report['diff'][$kind] = array_map( 'count', $lists );

        $made = self::events( self::order( $diff, $saved, $now ), $saved, $now, $options, $root, $report );
        $events = $made['events'];
        $manual = $made['manual'];
        if ( $report['errors'] )
        {
            $report['exit'] = expUpgrade::EXIT_FAILED;
            return $report;
        }
        if ( !$events )
        {
            $report['manual'] = $manual;
            if ( !$manual )
                $report['warnings'][] = 'no differences: nothing to generate';
            return $report;
        }
        $label = $report['source']['kind'] . ' ' . $report['source']['name'];
        if ( !isset( $options['name'] ) || trim( (string)$options['name'] ) === '' )
            $options['name'] = 'diff';
        if ( !isset( $options['description'] ) || trim( (string)$options['description'] ) === '' )
            $options['description'] = 'What changed since the ' . $label;
        $counts = array();
        foreach ( $report['diff'] as $kind => $c )
            if ( $c['added'] + $c['changed'] + $c['removed'] > 0 )
                $counts[] = "$kind {$c['added']} added, {$c['changed']} changed, {$c['removed']} removed";
        $options['comments'] = array( 'Generated from the difference to the ' . $label . ' (' . implode( '; ', $counts ) . ').',
                                      'Run on that state, it makes it like this installation.' );
        $options['ensure'] = true;
        $options['manual'] = $manual;
        return expMigrationRecordingGenerator::migration( $events, $options, $report );
    }

    /**
     * The events of the ordered things of a difference.
     *
     * @return array events, manual
     */
    protected static function events( array $things, array $saved, array $now, array $options, $root, array &$report )
    {
        $source = isset( $options['recordingSource'] ) ? $options['recordingSource'] : new expMigrationRecordingResolver();
        $keep = isset( $options['keepUserAttributes'] ) ? (array)$options['keepUserAttributes'] : expMigrationRecorder::settings()['keepUserAttributes'];
        $events = array();
        $manual = array();
        $add = function ( $name, array $data, $label ) use ( &$events, &$report, $source, $keep )
        {
            $event = expMigrationRecordingEvent::normalise( $name, $data, $source, array( 'keepUserAttributes' => $keep ) );
            if ( $event === null || isset( $event['missing'] ) )
                $report['errors'][] = "$label: not found";
            else
                $events[] = $event;
        };
        $direct = function ( $audit, $subject, array $step ) use ( &$events )
        {
            $events[] = array( 'v' => 1, 'audit' => $audit, 'subject' => $subject, 'step' => $step, 'omitted' => array(), 'notes' => array() );
        };
        $settings = null;
        foreach ( $things as $thing )
        {
            $key = $thing['key'];
            $gone = $thing['change'] === 'removed';
            switch ( $thing['kind'] )
            {
                case 'classes':
                    $add( $gone ? 'content.class.remove' : 'content.class.create', array( 'object' => array( 'identifier' => $key ) ), "the class $key" );
                    break;
                case 'roles':
                    $add( $gone ? 'access.role.remove' : 'access.role.create', array( 'object' => array( 'name' => $key ) ), "the role $key" );
                    break;
                case 'sections':
                    if ( $gone )
                        $add( 'content.section.remove', array( 'object' => array( 'identifier' => $key ) ), "the section $key" );
                    else
                    {
                        $section = eZSection::fetchByIdentifier( $key, false );
                        if ( !is_array( $section ) )
                            $report['errors'][] = "the section $key: not found";
                        else
                            $add( 'content.section.change', array( 'object' => array( 'id' => (int)$section['id'], 'identifier' => $key ) ), "the section $key" );
                    }
                    break;
                case 'states':
                    $add( $gone ? 'content.state.remove' : 'content.state.change', array( 'object' => array( 'identifier' => $key ) ), "the state group $key" );
                    break;
                case 'languages':
                    if ( $gone )
                        $direct( 'content.language.remove', "language $key", array( 'type' => 'language', 'mode' => 'delete', 'language' => $key ) );
                    else
                    {
                        $language = eZContentLanguage::fetchByLocale( $key );
                        if ( !$language instanceof eZContentLanguage )
                            $report['errors'][] = "the language $key: not found";
                        else
                            $direct( 'content.language.change', "language $key", array( 'type' => 'language', 'mode' => 'ensure', 'locale' => $key,
                                                                                         'name' => (string)$language->attribute( 'name' ) ) );
                    }
                    break;
                case 'content':
                    if ( $gone )
                    {
                        $direct( 'content.object.remove', "object remote:$key", array( 'type' => 'content', 'mode' => 'delete', 'match' => "remote:$key" ) );
                        break;
                    }
                    $object = eZContentObject::fetchByRemoteID( $key, false );
                    if ( !is_array( $object ) )
                    {
                        $report['errors'][] = "the object remote:$key: not found";
                        break;
                    }
                    $add( 'content.object.create', array( 'object' => array( 'id' => (int)$object['id'] ) ), "the object remote:$key" );
                    if ( $thing['change'] === 'changed' && isset( $saved['parents'][$key], $now['parents'][$key] ) && $saved['parents'][$key] !== $now['parents'][$key] )
                        $manual[] = "object remote:$key was moved (below " . ( $now['parents'][$key] !== '' ? 'remote:' . $now['parents'][$key] : 'the subtree' )
                                  . '): move its location with a location step (mode move)';
                    break;
                case 'settings':
                    if ( !preg_match( '/^(\S+\.ini) \[(.*)\] (.+)$/', $key, $m ) )
                        break;
                    $path = "$root/settings/override/{$m[1]}.append.php";
                    if ( !is_file( $path ) )
                        $path = "$root/settings/override/{$m[1]}.append";
                    $object = array( 'file' => $m[1], 'block' => $m[2], 'variable' => $m[3], 'path' => 'settings/override/' . basename( $path ), 'scope' => 'override' );
                    if ( $gone )
                        $add( 'system.setting.write', array( 'object' => $object, 'after' => array( 'set' => false ) ), "the setting $key" );
                    else
                    {
                        if ( $settings === null )
                            $settings = self::settingValues( "$root/settings/override" );
                        $add( 'system.setting.write', array( 'object' => $object, 'after' => array( 'set' => true, 'value' => $settings[$key] ) ), "the setting $key" );
                    }
                    break;
            }
        }
        return array( 'events' => $events, 'manual' => array_values( array_unique( $manual ) ) );
    }

    // ------------------------------------------------------------------ the saved state

    /**
     * The snapshot of the saved state, or null (the report says why).
     *
     * @param string $from
     * @param string[] $kinds
     * @param array $snapshotOptions
     * @param array $options root, backupDir, token, fetch
     * @param array $report source and errors are set
     * @return array|null
     */
    protected static function saved( $from, array $kinds, array $snapshotOptions, array $options, array &$report )
    {
        if ( preg_match( '#^https?://#i', $from ) )
        {
            $report['source'] = array( 'kind' => 'installation', 'name' => self::redactUrl( $from ) );
            return self::remote( $from, $kinds, $snapshotOptions, $options, $report );
        }
        $root = $options['root'];
        $tmp = "$root/var/tmp/migration-diff-" . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        try
        {
            $captionDir = expUpgradeCaption::find( $from, isset( $options['backupDir'] ) ? $options['backupDir'] : null );
            if ( $captionDir !== null )
            {
                $report['source'] = array( 'kind' => 'caption', 'name' => $from );
                $dump = expUpgradeCaption::extractDump( $captionDir, $tmp );
                $settingsDir = null;
                foreach ( array( "$captionDir/upgrade/settings/override", "$captionDir/upgrade/settings" ) as $dir )
                    if ( $settingsDir === null && is_dir( $dir ) && glob( "$dir/*.ini.append*" ) )
                        $settingsDir = $dir;
                return self::fromDump( $dump, $kinds, $snapshotOptions + array( 'settings_dir' => $settingsDir ), $tmp, $report );
            }
            $path = $from[0] === '/' ? $from : "$root/$from";
            if ( !is_file( $path ) )
            {
                $report['errors'][] = "no caption, address or dump $from (./console exp:migration:caption --list lists the captions)";
                $report['exit'] = expUpgrade::EXIT_USAGE;
                return null;
            }
            $report['source'] = array( 'kind' => 'dump', 'name' => basename( $path ) );
            return self::fromDump( array( 'engine' => self::dumpEngine( $path ), 'path' => $path ), $kinds, $snapshotOptions, $tmp, $report );
        }
        catch ( Throwable $e )
        {
            $report['errors'][] = 'the saved state cannot be read: ' . $e->getMessage();
            return null;
        }
        finally
        {
            if ( is_dir( $tmp ) )
                eZDir::recursiveDelete( $tmp, false );
        }
    }

    /**
     * The engine of a dump file by its first bytes: sqlite (a database file), sqlite-sql (the SQL of sqlite3 .dump),
     * mysql, postgresql, or '' (not known). Pure but for reading the file.
     *
     * @param string $path
     * @return string
     */
    public static function dumpEngine( $path )
    {
        $head = (string)@file_get_contents( $path, false, null, 0, 4096 );
        if ( strncmp( $head, "SQLite format 3\0", 16 ) === 0 )
            return 'sqlite';
        if ( stripos( $head, 'PostgreSQL database dump' ) !== false )
            return 'postgresql';
        if ( preg_match( '/MySQL dump|MariaDB dump|ENGINE=|`[a-z_]+`/i', $head ) )
            return 'mysql';
        if ( preg_match( '/PRAGMA foreign_keys|BEGIN TRANSACTION;/i', $head ) )
            return 'sqlite-sql';
        return '';
    }

    /**
     * The snapshot of a dump: read in place (a SQLite file, opened read-only), or loaded into a throwaway database
     * of this installation's engine that is dropped afterwards.
     */
    protected static function fromDump( array $dump, array $kinds, array $snapshotOptions, $tmp, array &$report )
    {
        $engine = (string)$dump['engine'];
        $path = (string)$dump['path'];
        $live = eZDB::instance();
        $liveEngine = expKernelTableSchema::type( $live );
        if ( $engine === 'sqlite' )
        {
            $sqlite = new SQLite3( $path, SQLITE3_OPEN_READONLY );
            $sqlite->busyTimeout( 5000 );
            try
            {
                return self::snapshot( self::queryOf( $sqlite ), $kinds, $snapshotOptions );
            }
            finally
            {
                $sqlite->close();
            }
        }
        if ( $engine === 'sqlite-sql' || ( $engine === '' && $liveEngine === 'sqlite' ) )
        {
            @mkdir( $tmp, eZDir::dirMode( 0700 ), true );
            $file = "$tmp/saved.sqlite";
            $sqlite = new SQLite3( $file );
            try
            {
                if ( !@$sqlite->exec( (string)file_get_contents( $path ) ) )
                    throw new RuntimeException( 'the SQL dump does not load into SQLite: ' . $sqlite->lastErrorMsg() );
                return self::snapshot( self::queryOf( $sqlite ), $kinds, $snapshotOptions );
            }
            finally
            {
                $sqlite->close();
            }
        }
        if ( $engine === 'mongodb' || is_dir( $path ) )
            throw new RuntimeException( 'a MongoDB dump is not compared here: restore it with mongorestore into a database of its own, then compare with that installation by its address' );
        if ( !in_array( $engine, array( 'mysql', 'postgresql' ), true ) )
            throw new RuntimeException( 'not a dump this command reads (a SQLite file, or an SQL dump of SQLite, MySQL/MariaDB or PostgreSQL)' );
        if ( $engine !== $liveEngine )
            throw new RuntimeException( "a $engine dump is compared only on a $engine installation (this one is $liveEngine)" );
        return self::fromServerDump( $engine, $path, $live, $kinds, $snapshotOptions, $tmp );
    }

    /** A MySQL or PostgreSQL dump loaded into <database>_migdiff_<pid> with the installation's account, read, dropped. */
    protected static function fromServerDump( $engine, $path, $live, array $kinds, array $snapshotOptions, $tmp )
    {
        $name = preg_replace( '/[^a-z0-9_]/', '', strtolower( (string)$live->DB ) ) . '_migdiff_' . getmypid();
        if ( !$live->query( "CREATE DATABASE $name" ) )
            throw new RuntimeException( 'the account may not create a throwaway database for the dump' );
        $db = null;
        try
        {
            $err = '';
            @mkdir( $tmp, eZDir::dirMode( 0700 ), true );
            if ( $engine === 'mysql' )
            {
                $clean = expUpgradeCaption::withoutDatabaseStatements( $path );
                $cnf = expUpgradeBackup::mysqlDefaultsFile( $live, "$tmp/.diff.cnf" );
                $code = expUpgradeBackup::runWithEnv( 'mysql --defaults-extra-file=' . escapeshellarg( $cnf ) . ' ' . escapeshellarg( $name ) . ' < ' . escapeshellarg( $clean ), array(), $err );
                @unlink( $cnf );
                if ( $clean !== $path )
                    @unlink( $clean );
            }
            else
                $code = expUpgradeBackup::runWithEnv( 'psql -X -q -v ON_ERROR_STOP=0 -h ' . escapeshellarg( $live->Server ?: 'localhost' ) . ( $live->Port ? ' -p ' . (int)$live->Port : '' )
                                                      . ' -U ' . escapeshellarg( (string)$live->User ) . ' -d ' . escapeshellarg( $name ) . ' -f ' . escapeshellarg( $path ),
                                                      array( 'PGPASSWORD' => (string)$live->Password ), $err );
            if ( $code !== 0 )
                throw new RuntimeException( 'the dump does not load: ' . mb_substr( trim( $err ), 0, 300 ) );
            $db = eZDB::instance( false, array( 'database' => $name, 'show_errors' => false ), true );
            return self::snapshot( self::queryOf( $db ), $kinds, $snapshotOptions );
        }
        finally
        {
            if ( $db instanceof eZDBInterface )
                $db->close();
            $live->query( "DROP DATABASE $name" );
        }
    }

    /**
     * The snapshot of another installation through its REST route (GET only).
     */
    protected static function remote( $url, array $kinds, array $snapshotOptions, array $options, array &$report )
    {
        $token = isset( $options['token'] ) ? trim( (string)$options['token'] ) : '';
        $value = '';
        if ( $token !== '' )
        {
            $value = strpos( $token, 'env:' ) === 0 ? getenv( substr( $token, 4 ) ) : ( class_exists( 'expSecrets' ) ? expSecrets::get( $token ) : null );
            if ( $value === false || $value === null || $value === '' )
            {
                $report['errors'][] = "the API key $token is not set (Setup > Secrets, or env:VARIABLE)";
                $report['exit'] = expUpgrade::EXIT_USAGE;
                return null;
            }
        }
        $base = rtrim( preg_replace( '#[?\#].*$#', '', $url ), '/' );
        $endpoint = strpos( $base, self::REST_PATH ) !== false ? $base : $base . self::REST_PATH;
        $query = array( 'compare' => implode( ',', $kinds ) );
        if ( isset( $snapshotOptions['subtree'] ) )
            $query['subtree'] = $snapshotOptions['subtree'];
        $query['limit'] = $snapshotOptions['limit'];
        $endpoint .= '?' . http_build_query( $query );
        $headers = array( 'Accept: application/json' );
        if ( $value !== '' )
            $headers[] = 'Authorization: Bearer ' . $value;
        $fetch = isset( $options['fetch'] ) && is_callable( $options['fetch'] ) ? $options['fetch'] : array( __CLASS__, 'httpGet' );
        try
        {
            list( $status, $body ) = call_user_func( $fetch, $endpoint, $headers );
        }
        catch ( Throwable $e )
        {
            $report['errors'][] = 'the other installation cannot be reached: ' . $e->getMessage();
            $report['exit'] = expUpgrade::EXIT_UNREACHABLE;
            return null;
        }
        $data = json_decode( (string)$body, true );
        if ( (int)$status !== 200 || !is_array( $data ) || !isset( $data['format'] ) || $data['format'] !== self::FORMAT )
        {
            $report['errors'][] = 'the other installation gave no snapshot (HTTP ' . (int)$status . ')'
                                . ( (int)$status === 403 || (int)$status === 401 ? ': an API key with the scope migration:read is needed (--token)' : '' )
                                . ( (int)$status === 404 ? ': it has no route ' . self::REST_PATH . ' (an older version?)' : '' );
            $report['exit'] = (int)$status === 401 || (int)$status === 403 ? expUpgrade::EXIT_USAGE : expUpgrade::EXIT_FAILED;
            return null;
        }
        return self::cleanSnapshot( $data );
    }

    /** A snapshot as another installation gave it, with only the keys and types a snapshot has. Pure. */
    public static function cleanSnapshot( array $data )
    {
        $out = array( 'format' => self::FORMAT, 'kinds' => array(), 'parents' => array(), 'depths' => array(), 'missing' => array(), 'errors' => array() );
        foreach ( self::KINDS as $kind )
            if ( isset( $data['kinds'][$kind] ) && is_array( $data['kinds'][$kind] ) )
                foreach ( $data['kinds'][$kind] as $key => $hash )
                    if ( is_scalar( $hash ) )
                        $out['kinds'][$kind][(string)$key] = (string)$hash;
        foreach ( isset( $data['kinds'] ) && is_array( $data['kinds'] ) ? array_keys( $data['kinds'] ) : array() as $kind )
            if ( in_array( $kind, self::KINDS, true ) && !isset( $out['kinds'][$kind] ) )
                $out['kinds'][$kind] = array();
        foreach ( array( 'parents', 'depths' ) as $key )
            foreach ( isset( $data[$key] ) && is_array( $data[$key] ) ? $data[$key] : array() as $k => $v )
                if ( is_scalar( $v ) )
                    $out[$key][(string)$k] = $key === 'depths' ? (int)$v : (string)$v;
        foreach ( isset( $data['missing'] ) ? (array)$data['missing'] : array() as $kind )
            if ( in_array( $kind, self::KINDS, true ) )
                $out['missing'][] = $kind;
        foreach ( isset( $data['errors'] ) ? (array)$data['errors'] : array() as $error )
            if ( is_scalar( $error ) )
                $out['errors'][] = 'the other installation: ' . mb_substr( (string)$error, 0, 300 );
        return $out;
    }

    /**
     * GET of a URL: array( status, body ).
     *
     * @param string $url
     * @param string[] $headers
     * @return array
     */
    public static function httpGet( $url, array $headers )
    {
        if ( !function_exists( 'curl_init' ) )
            throw new RuntimeException( 'the curl extension is not loaded' );
        $curl = curl_init( $url );
        curl_setopt_array( $curl, array( CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPGET => true, CURLOPT_HTTPHEADER => $headers, CURLOPT_TIMEOUT => 120,
                                         CURLOPT_CONNECTTIMEOUT => 15, CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS ) );
        $body = curl_exec( $curl );
        if ( $body === false )
        {
            $error = curl_error( $curl );
            throw new RuntimeException( $error );
        }
        return array( (int)curl_getinfo( $curl, CURLINFO_HTTP_CODE ), (string)$body );
    }

    /** An address without user, password and query, as reports show it. Pure. */
    public static function redactUrl( $url )
    {
        $parts = parse_url( (string)$url );
        if ( !is_array( $parts ) || !isset( $parts['host'] ) )
            return '(an address)';
        return ( isset( $parts['scheme'] ) ? $parts['scheme'] : 'https' ) . '://' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . (int)$parts['port'] : '' )
             . ( isset( $parts['path'] ) ? $parts['path'] : '' );
    }

    /**
     * The snapshot of this installation the REST route gives another one: fingerprints only, never a value.
     *
     * @param array $get compare, subtree (a node remote id), limit
     * @return array snapshot()
     */
    public static function localSnapshot( array $get )
    {
        $subtree = isset( $get['subtree'] ) ? trim( (string)$get['subtree'] ) : '';
        $error = null;
        $kinds = self::kinds( isset( $get['compare'] ) ? (string)$get['compare'] : '', $subtree !== '', $error );
        if ( $error !== null )
            return array( 'format' => self::FORMAT, 'kinds' => array(), 'parents' => array(), 'depths' => array(), 'missing' => array(), 'errors' => array( $error ) );
        $options = array( 'settings_dir' => rtrim( eZSys::rootDir(), '/' ) . '/settings/override',
                          'limit' => isset( $get['limit'] ) && ctype_digit( (string)$get['limit'] ) ? min( 100000, max( 1, (int)$get['limit'] ) ) : self::DEFAULT_LIMIT );
        if ( $subtree !== '' )
            $options['subtree'] = $subtree;
        return self::snapshot( self::queryOf( eZDB::instance() ), $kinds, $options );
    }
}

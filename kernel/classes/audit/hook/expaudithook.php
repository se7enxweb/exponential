<?php
/**
 * The kernel's audit call sites (doc/bc/6.0/audit.md, "The event catalogue", stage 3): one guarded way to record
 * an event from a hook point, and the descriptions of the things events are about (node, object, user, role ...).
 *
 *   if ( class_exists( 'expAuditHook' ) )
 *       expAuditHook::emit( 'content.node.move', function () use ( $node, $old, $new ) {
 *           return array( 'object' => expAuditHook::node( $node ), 'target' => array( 'type' => 'node', 'id' => $new ),
 *                         'before' => array( 'parent' => $old ), 'after' => array( 'parent' => $new ) );
 *       } );
 *
 * Everything here is guarded: the data is only built when the name is recorded (one lookup when it is off), and
 * nothing a call site hands in or this class does can throw into the request; a failure is a notice in the debug
 * output and the caller's work goes on. Call sites test class_exists( 'expAuditHook' ) first, so a persistent
 * Velocity worker that started before this class existed simply records nothing until it is restarted.
 *
 * mute() keeps an inner hook point quiet while an outer one records the action as a whole (the object removal
 * inside a subtree remove is recorded by the subtree, not again by eZContentObject::purge()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditHook
{
    /** @var array name => depth of mute() calls in progress */
    protected static $muted = array();

    /** @var array node id => true: the roots of subtree removals in progress (recorded as the parent) */
    protected static $subtreeRoots = array();

    /**
     * The verbs of the catalogue (doc/bc/6.0/audit.md, "Actor / verb / object") where they are not the name's
     * third rank, the record's default. A call site's own verb for a kernel name goes to after.action.
     *
     * @var array
     */
    public static $verbs = array(
        'access.permission.refused' => 'access',
        'access.token.refused' => 'post',
        'access.user.email.change' => 'change',
        'access.user.login.change' => 'change',
        'access.user.password.change' => 'change',
        'access.user.password.change.failed' => 'change',
        'access.user.password.reset' => 'reset',
        'access.user.password.reset.failed' => 'reset',
        'access.user.password.reset.request' => 'request',
        'access.view.sensitive' => 'read',
        'commerce.order.delete' => 'remove',
        'commerce.order.item.remove' => 'remove',
        'commerce.order.status' => 'change',
        'content.node.main' => 'assign',
        'content.node.priority' => 'sort',
        'content.node.section' => 'assign',
        'content.node.view' => 'read',
        'content.object.always_available' => 'change',
        'content.object.create' => 'publish',
        'content.object.download' => 'read',
        'content.object.initial_language' => 'change',
        'content.object.state' => 'assign',
        'content.object.translation.remove' => 'remove',
        'content.search.query' => 'read',
        'content.trash.empty' => 'purge',
        'data.export.csv' => 'export',
        'data.export.package' => 'export',
        'data.export.pdf' => 'export',
        'data.import.csv' => 'import',
        'data.import.dba' => 'import',
        'data.import.rss' => 'import',
        'data.infocollection.view' => 'read',
        'system.cronjob.fail' => 'run',
        'system.install.run' => 'install',
        'system.repair.queue' => 'change',
        'system.workflow.trigger.change' => 'change',
    );

    /**
     * The catalogue's verb on the data of a kernel name; a different verb the call site gave (create, remove,
     * restart ...) is kept as after.action.
     *
     * @param string $name
     * @param array $data
     * @return array
     */
    public static function withVerb( $name, array $data )
    {
        $ranks = explode( '.', (string)$name );
        $verb = isset( self::$verbs[$name] ) ? self::$verbs[$name] : ( isset( $ranks[2] ) ? $ranks[2] : ( isset( $ranks[1] ) ? $ranks[1] : null ) );
        $kernel = class_exists( 'expAuditTaxonomy' ) && array_key_exists( $name, (array)expAuditTaxonomy::catalogue() );
        if ( !$kernel )
            return $data;
        if ( isset( $data['verb'] ) && $data['verb'] !== $verb )
        {
            $after = isset( $data['after'] ) && is_array( $data['after'] ) ? $data['after'] : array();
            $data['after'] = array( 'action' => (string)$data['verb'] ) + $after;
        }
        $data['verb'] = $verb;
        return $data;
    }

    /**
     * Records one event when it is on.
     *
     * @param string $name a taxonomy name
     * @param array|callable|null $data the data of expAudit::event(), or a function returning it (called only
     *                                  when the name is recorded)
     * @return string|null the event id
     */
    public static function emit( $name, $data = null )
    {
        try
        {
            if ( !self::on( $name ) )
                return null;
            $data = self::data( $data );
            if ( $data === null )
                return null;
            return expAudit::event( $name, self::withVerb( $name, $data ) );
        }
        catch ( Throwable $e )
        {
            self::failed( $e, $name );
            return null;
        }
    }

    /**
     * Whether a name is recorded now (audit on, the name on, not muted).
     *
     * @param string $name
     * @return bool
     */
    public static function on( $name )
    {
        try
        {
            if ( !empty( self::$muted[$name] ) || !class_exists( 'expAudit' ) )
                return false;
            return expAudit::isOn( $name );
        }
        catch ( Throwable $e )
        {
            return false;
        }
    }

    /**
     * Starts a parent event (expAudit::begin()), guarded.
     *
     * @param string $name
     * @param array|callable|null $data
     * @return string|null
     */
    public static function begin( $name, $data = null )
    {
        try
        {
            if ( !self::on( $name ) )
                return null;
            $data = self::data( $data );
            if ( $data === null )
                return null;
            return expAudit::begin( $name, self::withVerb( $name, $data ) );
        }
        catch ( Throwable $e )
        {
            self::failed( $e, $name );
            return null;
        }
    }

    /**
     * Ends a parent event (expAudit::end()), guarded.
     *
     * @param string|null $id
     * @param array|callable|null $data
     * @return string|null
     */
    public static function end( $id, $data = null )
    {
        if ( $id === null )
            return null;
        try
        {
            $data = self::data( $data );
            return expAudit::end( $id, $data === null ? array() : $data );
        }
        catch ( Throwable $e )
        {
            self::failed( $e, 'end' );
            return null;
        }
    }

    /**
     * Runs $code with $id as the implicit parent of every event inside (expAudit::withParent()); the code runs
     * whatever happens to the audit.
     *
     * @param string|null $id
     * @param callable $code
     * @return mixed
     */
    public static function withParent( $id, $code )
    {
        if ( $id === null || !class_exists( 'expAudit' ) )
            return call_user_func( $code );
        return expAudit::withParent( $id, $code );
    }

    /**
     * Keeps $names quiet while $code runs (an outer hook point records the action as a whole).
     *
     * @param string|string[] $names
     * @param callable $code
     * @return mixed what $code returns
     */
    public static function muted( $names, $code )
    {
        $names = (array)$names;
        foreach ( $names as $n )
            self::$muted[$n] = ( isset( self::$muted[$n] ) ? self::$muted[$n] : 0 ) + 1;
        try
        {
            return call_user_func( $code );
        }
        finally
        {
            foreach ( $names as $n )
            {
                if ( --self::$muted[$n] <= 0 )
                    unset( self::$muted[$n] );
            }
        }
    }

    /**
     * Marks a node as the root of a subtree removal in progress: removeNodeFromTree() does not record it again,
     * the subtree's parent event is its record.
     *
     * @param int $nodeID
     * @param bool $set
     */
    public static function subtreeRoot( $nodeID, $set = true )
    {
        if ( $set )
            self::$subtreeRoots[(int)$nodeID] = true;
        else
            unset( self::$subtreeRoots[(int)$nodeID] );
    }

    /** @return bool */
    public static function isSubtreeRoot( $nodeID )
    {
        return isset( self::$subtreeRoots[(int)$nodeID] );
    }

    /** Forgets mutes and subtree roots (a new request in a persistent worker; tests). */
    public static function reset()
    {
        self::$muted = array();
        self::$subtreeRoots = array();
    }

    /**
     * A sampled read (Z6: content.node.view, content.search.query, content.object.download): recorded only with
     * [AuditReadSettings] Reads=enabled, at SampleRate, and only for the sections and classes in Sections[] and
     * Classes[] when those are set.
     *
     * @param string $name
     * @param int|null $nodeID the node read, for the section and class filters
     * @param array|callable $data
     * @return string|null
     */
    public static function read( $name, $nodeID, $data )
    {
        if ( !self::on( $name ) )
            return null;
        return self::emit( $name, function () use ( $nodeID, $data ) {
            $ini = eZINI::instance( 'audit.ini' );
            $sections = $ini->hasVariable( 'AuditReadSettings', 'Sections' ) ? array_filter( (array)$ini->variable( 'AuditReadSettings', 'Sections' ) ) : array();
            $classes = $ini->hasVariable( 'AuditReadSettings', 'Classes' ) ? array_filter( (array)$ini->variable( 'AuditReadSettings', 'Classes' ) ) : array();
            if ( ( $sections || $classes ) && $nodeID )
            {
                $node = eZContentObjectTreeNode::fetch( (int)$nodeID );
                $object = $node ? $node->object() : null;
                if ( !$object )
                    return false;
                if ( $classes && !in_array( (string)$object->attribute( 'class_identifier' ), $classes, true ) )
                    return false;
                if ( $sections )
                {
                    $section = eZSection::fetch( (int)$object->attribute( 'section_id' ) );
                    if ( !$section || !in_array( (string)$section->attribute( 'identifier' ), $sections, true ) )
                        return false;
                }
            }
            return $data instanceof Closure ? call_user_func( $data ) : $data;
        } );
    }

    /**
     * The stored row of a persistent object before a store, for rowChanged(): false when it has no row yet, null
     * when $name is not recorded (nothing to compare then).
     *
     * @param string $name
     * @param eZPersistentObject $object
     * @return array|false|null
     */
    public static function rowBefore( $name, $object )
    {
        if ( !self::on( $name ) || !$object instanceof eZPersistentObject )
            return null;
        $row = self::safe( function () use ( $object ) {
            $def = $object->definition();
            $conds = array();
            foreach ( (array)$def['keys'] as $key )
            {
                $value = $object->attribute( $key );
                if ( $value === null || $value === '' )
                    return false;
                $conds[$key] = $value;
            }
            return eZPersistentObject::fetchObject( $def, null, $conds, false );
        } );
        return is_array( $row ) ? $row : false;
    }

    /**
     * Records what a store changed in the given fields: nothing when no field changed (a list view stores every
     * row it shows), verb create for a new row.
     *
     * @param string $name
     * @param eZPersistentObject $object
     * @param array|false|null $before rowBefore()
     * @param string[] $fields
     * @param array $desc the audit object
     * @param array $data more fields of the record (target ...)
     * @return string|null
     */
    public static function rowChanged( $name, $object, $before, array $fields, array $desc, array $data = array() )
    {
        if ( $before === null )
            return null;
        $b = array();
        $a = array();
        foreach ( $fields as $field )
        {
            $new = self::safe( function () use ( $object, $field ) { return $object->attribute( $field ); } );
            $new = $new === null ? null : (string)$new;
            $old = $before === false || !array_key_exists( $field, $before ) ? null : ( $before[$field] === null ? null : (string)$before[$field] );
            if ( $old === $new || ( is_numeric( $old ) && is_numeric( $new ) && (float)$old == (float)$new ) )
                continue;
            $b[$field] = $old;
            $a[$field] = $new;
        }
        if ( !$a )
            return null;
        return self::emit( $name, $data + array( 'object' => $desc, 'verb' => $before === false ? 'create' : 'change',
                                                 'before' => $before === false ? null : $b, 'after' => $a ) );
    }

    // ------------------------------------------------------------------ descriptions

    /**
     * A node: id, object id, name, class, parent, path.
     *
     * @param eZContentObjectTreeNode|int|null $node
     * @return array|null
     */
    public static function node( $node )
    {
        if ( is_numeric( $node ) && class_exists( 'eZContentObjectTreeNode' ) )
        {
            $id = (int)$node;
            $node = eZContentObjectTreeNode::fetch( $id );
            if ( !$node instanceof eZContentObjectTreeNode )
                return array( 'type' => 'node', 'id' => $id );
        }
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;
        $d = array( 'type' => 'node', 'id' => (int)$node->attribute( 'node_id' ),
                    'object_id' => (int)$node->attribute( 'contentobject_id' ) );
        $name = self::safe( function () use ( $node ) { return $node->attribute( 'name' ); } );
        if ( $name === null || $name === '' )
            $name = self::safe( function () use ( $node ) { $o = $node->object(); return $o ? $o->attribute( 'name' ) : null; } );
        if ( $name !== null && $name !== '' )
            $d['name'] = (string)$name;
        $class = self::safe( function () use ( $node ) { return $node->attribute( 'class_identifier' ); } );
        if ( $class )
            $d['class'] = (string)$class;
        $d['parent'] = (int)$node->attribute( 'parent_node_id' );
        $d['path'] = (string)$node->attribute( 'path_string' );
        return $d;
    }

    /**
     * An object: id, name, class, main node.
     *
     * @param eZContentObject|int|null $object
     * @return array|null
     */
    public static function object( $object )
    {
        if ( is_numeric( $object ) && class_exists( 'eZContentObject' ) )
        {
            $id = (int)$object;
            $object = eZContentObject::fetch( $id );
            if ( !$object instanceof eZContentObject )
                return array( 'type' => 'object', 'id' => $id );
        }
        if ( !$object instanceof eZContentObject )
            return null;
        $d = array( 'type' => 'object', 'id' => (int)$object->attribute( 'id' ) );
        $name = self::safe( function () use ( $object ) { return $object->attribute( 'name' ); } );
        if ( $name !== null && $name !== '' )
            $d['name'] = (string)$name;
        $class = self::safe( function () use ( $object ) { return $object->attribute( 'class_identifier' ); } );
        if ( $class )
            $d['class'] = (string)$class;
        $main = self::safe( function () use ( $object ) { return $object->attribute( 'main_node_id' ); } );
        if ( $main )
            $d['main_node'] = (int)$main;
        return $d;
    }

    /**
     * A user: content object id and login (the e-mail address only when asked, under the privacy rule "email").
     *
     * @param eZUser|int|null $user
     * @param bool $withEmail
     * @return array|null
     */
    public static function user( $user, $withEmail = false )
    {
        if ( is_numeric( $user ) && class_exists( 'eZUser' ) )
        {
            $id = (int)$user;
            $user = eZUser::fetch( $id );
            if ( !$user instanceof eZUser )
                return array( 'type' => 'user', 'id' => $id );
        }
        if ( !$user instanceof eZUser )
            return null;
        $d = array( 'type' => 'user', 'id' => (int)$user->attribute( 'contentobject_id' ), 'login' => (string)$user->attribute( 'login' ) );
        if ( $withEmail )
            $d['email'] = (string)$user->attribute( 'email' );
        return $d;
    }

    /**
     * A user or a user group (the target of a role assignment), by content object id.
     *
     * @param int $objectID
     * @return array
     */
    public static function userOrGroup( $objectID )
    {
        $objectID = (int)$objectID;
        $d = array( 'type' => 'user', 'id' => $objectID );
        $object = class_exists( 'eZContentObject' ) ? eZContentObject::fetch( $objectID ) : null;
        if ( $object instanceof eZContentObject )
        {
            $user = eZUser::fetch( $objectID );
            if ( $user instanceof eZUser && $user->attribute( 'login' ) !== '' )
                $d['login'] = (string)$user->attribute( 'login' );
            else
                $d['type'] = 'group';
            $d['name'] = (string)$object->attribute( 'name' );
        }
        return $d;
    }

    /**
     * A role: id and name.
     *
     * @param eZRole|int|null $role
     * @return array|null
     */
    public static function role( $role )
    {
        if ( is_numeric( $role ) && class_exists( 'eZRole' ) )
        {
            $id = (int)$role;
            $role = eZRole::fetch( $id );
            if ( !$role instanceof eZRole )
                return array( 'type' => 'role', 'id' => $id );
        }
        if ( !$role instanceof eZRole )
            return null;
        return array( 'type' => 'role', 'id' => (int)$role->attribute( 'id' ), 'name' => (string)$role->attribute( 'name' ) );
    }

    /**
     * The policies of a role as "module/function" strings with their limitations, for before/after.
     *
     * @param eZRole|int $role
     * @return array
     */
    public static function policies( $role )
    {
        if ( is_numeric( $role ) )
            $role = eZRole::fetch( (int)$role );
        if ( !$role instanceof eZRole )
            return array();
        $list = array();
        foreach ( (array)$role->policyList() as $policy )
            $list[] = self::policy( $policy );
        return $list;
    }

    /**
     * One policy: module, function, limitations (identifier => values).
     *
     * @param eZPolicy $policy
     * @return array
     */
    public static function policy( $policy )
    {
        if ( !$policy instanceof eZPolicy )
            return array();
        $d = array( 'id' => (int)$policy->attribute( 'id' ), 'module' => (string)$policy->attribute( 'module_name' ),
                    'function' => (string)$policy->attribute( 'function_name' ) );
        $limits = array();
        foreach ( (array)$policy->limitationList() as $limitation )
        {
            $values = array();
            foreach ( (array)$limitation->attribute( 'values' ) as $v )
                $values[] = is_object( $v ) ? (string)$v->attribute( 'value' ) : (string)$v;
            $limits[(string)$limitation->attribute( 'identifier' )] = $values;
        }
        if ( $limits )
            $d['limitations'] = $limits;
        return $d;
    }

    /**
     * A section: id, name, identifier.
     *
     * @param eZSection|int|null $section
     * @return array|null
     */
    public static function section( $section )
    {
        if ( is_numeric( $section ) && class_exists( 'eZSection' ) )
        {
            $id = (int)$section;
            $section = eZSection::fetch( $id );
            if ( !$section instanceof eZSection )
                return array( 'type' => 'section', 'id' => $id );
        }
        if ( !$section instanceof eZSection )
            return null;
        return array( 'type' => 'section', 'id' => (int)$section->attribute( 'id' ), 'name' => (string)$section->attribute( 'name' ),
                      'identifier' => (string)$section->attribute( 'identifier' ) );
    }

    /**
     * A content class: id, identifier, name, attribute identifiers with datatype, required and searchable.
     *
     * @param eZContentClass|int|null $class
     * @param bool $withAttributes
     * @param int|null $version eZContentClass::VERSION_STATUS_* to read the attributes of (default: the class's own)
     * @return array|null
     */
    public static function contentClass( $class, $withAttributes = false, $version = null )
    {
        if ( is_numeric( $class ) && class_exists( 'eZContentClass' ) )
        {
            $id = (int)$class;
            $class = eZContentClass::fetch( $id );
            if ( !$class instanceof eZContentClass )
                return array( 'type' => 'class', 'id' => $id );
        }
        if ( !$class instanceof eZContentClass )
            return null;
        $d = array( 'type' => 'class', 'id' => (int)$class->attribute( 'id' ), 'identifier' => (string)$class->attribute( 'identifier' ) );
        $name = self::safe( function () use ( $class ) { return $class->attribute( 'name' ); } );
        if ( $name !== null && $name !== '' )
            $d['name'] = (string)$name;
        if ( $withAttributes )
            $d['attributes'] = self::classAttributes( $class, $version );
        return $d;
    }

    /**
     * The attributes of a class version: identifier => datatype, required, searchable.
     *
     * @param eZContentClass $class
     * @param int|null $version
     * @return array
     */
    public static function classAttributes( $class, $version = null )
    {
        $version = $version === null ? (int)$class->attribute( 'version' ) : (int)$version;
        $list = array();
        foreach ( (array)eZContentClassAttribute::fetchFilteredList( array( 'contentclass_id' => (int)$class->attribute( 'id' ),
                                                                            'version' => $version ) ) as $a )
        {
            $list[(string)$a->attribute( 'identifier' )] = array( 'datatype' => (string)$a->attribute( 'data_type_string' ),
                                                                  'required' => (bool)$a->attribute( 'is_required' ),
                                                                  'searchable' => (bool)$a->attribute( 'is_searchable' ) );
        }
        ksort( $list );
        return $list;
    }

    /**
     * The languages of a language mask, as locale codes.
     *
     * @param int $mask
     * @return string[]
     */
    public static function languages( $mask )
    {
        $list = array();
        foreach ( (array)eZContentLanguage::languagesByMask( (int)$mask ) as $language )
            $list[] = (string)$language->attribute( 'locale' );
        sort( $list );
        return $list;
    }

    /**
     * The state ids of an object, by group identifier ("ez_lock" => "not_locked").
     *
     * @param eZContentObject $object
     * @return array
     */
    public static function states( $object )
    {
        $list = array();
        if ( !$object instanceof eZContentObject )
            return $list;
        foreach ( (array)$object->attribute( 'state_id_array' ) as $stateID )
        {
            $state = eZContentObjectState::fetchById( $stateID );
            if ( $state instanceof eZContentObjectState )
            {
                $group = $state->attribute( 'group' );
                $list[$group ? (string)$group->attribute( 'identifier' ) : (string)$state->attribute( 'group_id' )] = (string)$state->attribute( 'identifier' );
            }
        }
        ksort( $list );
        return $list;
    }

    /**
     * Ids from a list that may hold numbers, strings and objects.
     *
     * @param array $list
     * @return int[]
     */
    public static function ids( $list )
    {
        $out = array();
        foreach ( (array)$list as $v )
        {
            if ( is_numeric( $v ) )
                $out[] = (int)$v;
        }
        return $out;
    }

    // ------------------------------------------------------------------ internals

    /**
     * @param array|callable|null $data
     * @return array|null
     */
    protected static function data( $data )
    {
        if ( $data instanceof Closure || ( is_array( $data ) && is_callable( $data ) && !isset( $data['object'] ) ) )
            $data = call_user_func( $data );
        if ( $data === null )
            return array();
        if ( $data === false )
            return null;
        return is_array( $data ) ? $data : array();
    }

    /**
     * Calls $code; null when it throws.
     *
     * @param callable $code
     * @return mixed
     */
    public static function safe( $code )
    {
        try
        {
            return call_user_func( $code );
        }
        catch ( Throwable $e )
        {
            return null;
        }
    }

    protected static function failed( Throwable $e, $name )
    {
        try
        {
            if ( class_exists( 'eZDebug' ) )
                eZDebug::writeNotice( 'Audit event ' . (string)$name . ' not recorded: ' . $e->getMessage(), __METHOD__ );
        }
        catch ( Throwable $e2 )
        {
        }
    }
}

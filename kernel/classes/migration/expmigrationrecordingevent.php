<?php
/**
 * File containing the expMigrationRecordingEvent class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One recorded change: an audit event of the kernel (the operation level: content published, a node moved, a class
 * stored, a role changed, a setting written ...) turned into the YAML migration step that would make the same change
 * on another installation. The recording (expMigrationRecorder) keeps these; the generator turns a recording into a
 * YAML migration (exp:migration:generate --from-recording).
 *
 * The format, exp-migration-recording-event/1:
 *
 *   v        1
 *   audit    the audit event's name (content.object.publish ...), the kernel's taxonomy
 *   subject  what it is about, in stable names: "object remote:<remote id>", "class <identifier>", "role <name>" ...
 *   step     the step: type and mode (STEPS) and the keys the step takes, every id in a stable form: nodes and
 *            objects as remote:<remote id> (expMigrationIdentifier), classes, sections, states and languages by
 *            identifier, roles by name; the values are the state after the change (a content step's fields, a
 *            class's definition, a role's policies), so the last event of a subject is the whole of it
 *   omitted  what was left out on purpose: personal fields of users (open question 16: never recorded), secrets
 *   notes    what the generator must know (a value kept as the kernel gave it, a thing that no longer existed)
 *
 * normalise() is pure: everything it reads from the installation comes through an expMigrationRecordingSource.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRecordingEvent
{
    /** The format of a recorded event. */
    const FORMAT = 'exp-migration-recording-event/1';

    /**
     * The audit events a recording keeps and the step each becomes: audit name => array( step type, mode ). The step
     * types are those of the YAML steps v1 (work package 10); a step type the installation does not have yet makes
     * exp:migration:generate --from-recording report it instead of writing a migration.
     */
    const STEPS = array(
        'content.object.create' => array( 'content', 'create' ),
        'content.object.publish' => array( 'content', 'update' ),
        'content.object.translate' => array( 'content', 'update' ),
        'content.object.translation.remove' => array( 'content', 'update' ),
        'content.object.always_available' => array( 'content', 'update' ),
        'content.object.initial_language' => array( 'content', 'update' ),
        'content.object.state' => array( 'content', 'update' ),
        'content.object.remove' => array( 'content', 'delete' ),
        'content.object.purge' => array( 'content', 'delete' ),
        'content.object.restore' => array( 'trash', 'restore' ),
        'content.node.add' => array( 'location', 'create' ),
        'content.node.move' => array( 'location', 'move' ),
        'content.node.copy' => array( 'content', 'copy' ),
        'content.node.hide' => array( 'location', 'hide' ),
        'content.node.reveal' => array( 'location', 'reveal' ),
        'content.node.remove' => array( 'location', 'delete' ),
        'content.node.remove.trash' => array( 'trash', 'move' ),
        'content.node.section' => array( 'section', 'assign' ),
        'content.node.sort' => array( 'location', 'update' ),
        'content.node.priority' => array( 'location', 'update' ),
        'content.node.main' => array( 'location', 'update' ),
        'content.node.swap' => array( 'location', 'swap' ),
        'content.class.create' => array( 'class', 'create' ),
        'content.class.change' => array( 'class', 'update' ),
        'content.class.copy' => array( 'class', 'create' ),
        'content.class.remove' => array( 'class', 'delete' ),
        'content.section.change' => array( 'section', 'ensure' ),
        'content.section.remove' => array( 'section', 'delete' ),
        'content.state.change' => array( 'object_state_group', 'ensure' ),
        'content.state.remove' => array( 'object_state_group', 'delete' ),
        'content.urlalias.change' => array( 'url_alias', 'ensure' ),
        'access.role.create' => array( 'role', 'create' ),
        'access.role.change' => array( 'role', 'update' ),
        'access.role.copy' => array( 'role', 'create' ),
        'access.role.remove' => array( 'role', 'delete' ),
        'access.policy.add' => array( 'role', 'update' ),
        'access.policy.remove' => array( 'role', 'update' ),
        'access.role.assign' => array( 'role', 'assign' ),
        'access.role.unassign' => array( 'role', 'unassign' ),
        'access.user.create' => array( 'user', 'create' ),
        'access.user.change' => array( 'user', 'update' ),
        'access.user.move' => array( 'user', 'update' ),
        'access.user.activate' => array( 'user', 'update' ),
        'access.user.lock' => array( 'user', 'update' ),
        'access.user.unlock' => array( 'user', 'update' ),
        'access.user.remove' => array( 'user', 'delete' ),
        'system.setting.write' => array( 'ini', 'set' ),
        'system.workflow.trigger.change' => array( 'workflow_trigger', 'ensure' ),
    );

    /** The datatypes whose value is the account of a user: never recorded. */
    const ACCOUNT_DATATYPES = array( 'ezuser' );

    /** What a secret value is replaced with in the audit (expAuditPrivacy::SECRET). */
    const SECRET_MARK = '***';

    /**
     * The audit names a recording keeps.
     *
     * @return string[]
     */
    public static function names()
    {
        return array_keys( self::STEPS );
    }

    /**
     * Whether an audit name is recorded (it is in STEPS, and in $only when that is given).
     *
     * @param string $name
     * @param string[]|null $only [RecordingSettings] EventList[] (null or empty: all of STEPS)
     * @return bool
     */
    public static function recorded( $name, $only = null )
    {
        if ( !isset( self::STEPS[(string)$name] ) )
            return false;
        return !$only || in_array( (string)$name, $only, true );
    }

    /**
     * The step type and mode of an audit name, or null.
     *
     * @param string $name
     * @return array|null array( type, mode )
     */
    public static function stepOf( $name )
    {
        return isset( self::STEPS[(string)$name] ) ? self::STEPS[(string)$name] : null;
    }

    /**
     * The step types a list of recorded events needs. Pure.
     *
     * @param array[] $events normalised events (or rows with a payload already decoded)
     * @return string[] sorted
     */
    public static function stepTypes( array $events )
    {
        $types = array();
        foreach ( $events as $e )
            if ( isset( $e['step']['type'] ) )
                $types[(string)$e['step']['type']] = true;
        $types = array_keys( $types );
        sort( $types );
        return $types;
    }

    /**
     * Turns an audit event into a recorded event.
     *
     * @param string $name the audit name
     * @param array $data the data of the audit event (object, target, before, after, verb ...)
     * @param expMigrationRecordingSource $source
     * @param array $options keepUserAttributes (attribute identifiers of user objects that are not personal data)
     * @return array|null the event (see the class), null when the name is not recorded or the event says nothing
     *                    that could be repeated
     */
    public static function normalise( $name, array $data, expMigrationRecordingSource $source, array $options = array() )
    {
        $name = (string)$name;
        $step = self::stepOf( $name );
        if ( $step === null )
            return null;
        $event = array( 'v' => 1, 'audit' => $name, 'subject' => '', 'step' => array( 'type' => $step[0], 'mode' => $step[1] ),
                        'omitted' => array(), 'notes' => array() );
        $object = isset( $data['object'] ) && is_array( $data['object'] ) ? $data['object'] : array();
        $target = isset( $data['target'] ) && is_array( $data['target'] ) ? $data['target'] : array();
        $before = isset( $data['before'] ) && is_array( $data['before'] ) ? $data['before'] : array();
        $after = isset( $data['after'] ) && is_array( $data['after'] ) ? $data['after'] : array();
        $keep = isset( $options['keepUserAttributes'] ) ? array_values( (array)$options['keepUserAttributes'] ) : array();

        $family = substr( $name, 0, strpos( $name, '.', strpos( $name, '.' ) + 1 ) ?: strlen( $name ) );
        switch ( $family )
        {
            case 'content.object':
                $done = self::contentObject( $name, $object, $target, $before, $after, $source, $keep, $event );
                break;
            case 'content.node':
                $done = self::contentNode( $name, $object, $target, $before, $after, $source, $keep, $event );
                break;
            case 'content.class':
                $done = self::contentClass( $name, $object, $target, $source, $event );
                break;
            case 'content.section':
                $done = self::section( $name, $object, $after, $source, $event );
                break;
            case 'content.state':
                $done = self::stateGroup( $name, $object, $after, $source, $event );
                break;
            case 'content.urlalias':
                $done = self::urlAlias( $data, $object, $target, $before, $after, $source, $event );
                break;
            case 'access.role':
            case 'access.policy':
                $done = self::role( $name, $object, $target, $before, $after, $source, $event );
                break;
            case 'access.user':
                $done = self::user( $name, $object, $target, $before, $after, $source, $keep, $event );
                break;
            case 'system.setting':
                $done = self::setting( $object, $before, $after, $event );
                break;
            case 'system.workflow':
                $done = self::trigger( $before, $after, $event );
                break;
            default:
                $done = false;
        }
        if ( !$done )
            return null;
        $event['omitted'] = array_values( array_unique( $event['omitted'] ) );
        $event['notes'] = array_values( array_unique( $event['notes'] ) );
        return $event;
    }

    // ------------------------------------------------------------------ content

    /** content.object.* */
    protected static function contentObject( $name, array $object, array $target, array $before, array $after, expMigrationRecordingSource $source,
                                             array $keep, array &$event )
    {
        $objectId = isset( $object['id'] ) ? (int)$object['id'] : 0;
        if ( $objectId <= 0 )
            return false;
        $ref = $source->objectRef( $objectId );
        if ( $name === 'content.object.remove' || $name === 'content.object.purge' )
        {
            if ( $ref === null )
                return self::missing( $event, 'object', $objectId );
            $event['subject'] = 'object ' . $ref;
            $event['step']['match'] = $ref;
            return true;
        }
        if ( $name === 'content.object.restore' )
        {
            if ( $ref === null )
                return self::missing( $event, 'object', $objectId );
            $event['subject'] = 'object ' . $ref;
            $event['step']['object'] = $ref;
            $node = $source->object( $objectId );
            if ( $node && $node['parent'] !== null )
                $event['step']['parent'] = $node['parent'];
            return true;
        }
        $snapshot = $source->object( $objectId );
        if ( $snapshot === null )
            return self::missing( $event, 'object', $objectId );
        $event['subject'] = 'object remote:' . $snapshot['remote_id'];
        if ( !empty( $snapshot['is_user'] ) )
        {
            // a user is published as content too: the user step, without the personal fields
            $event['step']['type'] = 'user';
            self::userBody( $snapshot, $keep, $event );
            if ( $name === 'content.object.create' )
                $event['step']['mode'] = 'create';
            return true;
        }
        self::contentBody( $snapshot, $event );
        if ( $name === 'content.object.translate' && isset( $after['languages'], $before['languages'] ) )
            $event['notes'][] = 'translations added: ' . implode( ', ', array_diff( (array)$after['languages'], (array)$before['languages'] ) );
        if ( $name === 'content.object.translation.remove' )
            $event['step']['remove_translations'] = isset( $before['languages'], $after['languages'] )
                ? array_values( array_diff( (array)$before['languages'], (array)$after['languages'] ) )
                : ( isset( $target['id'] ) ? array_values( array_filter( explode( ',', (string)$target['id'] ) ) ) : array() );
        if ( $name === 'content.object.publish' && isset( $after['changed_attributes'] ) )
            $event['step']['changed'] = array_values( (array)$after['changed_attributes'] );
        return true;
    }

    /** The keys of a content step from an object snapshot. */
    protected static function contentBody( array $snapshot, array &$event )
    {
        $mode = $event['step']['mode'];
        $step = array( 'type' => 'content', 'mode' => $mode );
        if ( $mode === 'create' )
        {
            $step['class'] = $snapshot['class'];
            $step['parent'] = $snapshot['parent'];
            $step['remote_id'] = $snapshot['remote_id'];
        }
        else
            $step['match'] = 'remote:' . $snapshot['remote_id'];
        foreach ( array( 'section', 'always_available', 'initial_language' ) as $key )
            if ( isset( $snapshot[$key] ) && $snapshot[$key] !== null && $snapshot[$key] !== '' )
                $step[$key] = $snapshot[$key];
        if ( !empty( $snapshot['states'] ) )
            $step['states'] = $snapshot['states'];
        $step['fields'] = array();
        foreach ( (array)$snapshot['languages'] as $locale => $fields )
        {
            $step['fields'][$locale] = array();
            foreach ( (array)$fields as $identifier => $field )
            {
                if ( in_array( $field['datatype'], self::ACCOUNT_DATATYPES, true ) )
                {
                    $event['omitted'][] = "$identifier (a user account)";
                    continue;
                }
                $step['fields'][$locale][$identifier] = $field['value'];
                if ( !empty( $field['note'] ) )
                    $event['notes'][] = "$identifier: " . $field['note'];
            }
        }
        if ( !empty( $snapshot['locations'] ) && count( $snapshot['locations'] ) > 1 )
        {
            $step['locations'] = array();
            foreach ( $snapshot['locations'] as $location )
                if ( empty( $location['main'] ) )
                    $step['locations'][] = array( 'parent' => $location['parent'], 'remote_id' => $location['remote_id'] );
        }
        if ( isset( $event['step']['changed'] ) )
            $step['changed'] = $event['step']['changed'];
        $event['step'] = $step;
    }

    /**
     * The keys of a user step from an object snapshot: the groups, the attributes listed as not personal, and
     * nothing of the account (login, e-mail, password hash). What is left out is named in omitted.
     */
    protected static function userBody( array $snapshot, array $keep, array &$event )
    {
        $step = array( 'type' => 'user', 'mode' => $event['step']['mode'] );
        if ( $step['mode'] === 'create' )
            $step['remote_id'] = $snapshot['remote_id'];
        else
            $step['match'] = 'remote:' . $snapshot['remote_id'];
        $step['class'] = $snapshot['class'];
        $groups = isset( $snapshot['user_groups'] ) ? (array)$snapshot['user_groups'] : array();
        if ( !$groups && $snapshot['parent'] !== null )
            $groups = array( $snapshot['parent'] );
        $step['groups'] = array_values( $groups );
        if ( array_key_exists( 'enabled', $snapshot ) )
            $step['enabled'] = (bool)$snapshot['enabled'];
        $kept = array();
        foreach ( (array)$snapshot['languages'] as $locale => $fields )
            foreach ( (array)$fields as $identifier => $field )
            {
                if ( in_array( $field['datatype'], self::ACCOUNT_DATATYPES, true ) )
                {
                    $event['omitted'][] = 'login';
                    $event['omitted'][] = 'email';
                    $event['omitted'][] = 'password';
                    continue;
                }
                if ( !in_array( (string)$identifier, $keep, true ) )
                {
                    $event['omitted'][] = (string)$identifier;
                    continue;
                }
                $kept[$locale][$identifier] = $field['value'];
            }
        if ( $kept )
            $step['fields'] = $kept;
        $event['notes'][] = 'personal data left out: a generated migration names the account by secret references';
        $event['step'] = $step;
    }

    /** content.node.* */
    protected static function contentNode( $name, array $object, array $target, array $before, array $after, expMigrationRecordingSource $source,
                                           array $keep, array &$event )
    {
        $nodeId = isset( $object['type'], $object['id'] ) && $object['type'] === 'node' ? (int)$object['id'] : 0;
        switch ( $name )
        {
            case 'content.node.add':
                $objectId = isset( $object['id'] ) ? (int)$object['id'] : 0;
                $newNode = isset( $after['node'] ) ? (int)$after['node'] : ( isset( $target['id'] ) ? (int)$target['id'] : 0 );
                $objectRef = $source->objectRef( $objectId );
                $location = $newNode ? $source->node( $newNode ) : null;
                if ( $objectRef === null || $location === null )
                    return self::missing( $event, 'location', $newNode );
                $event['subject'] = 'location remote:' . $location['remote_id'];
                $event['step'] += array( 'object' => $objectRef, 'parent' => $location['parent'], 'remote_id' => $location['remote_id'] );
                return true;
            case 'content.node.move':
                $location = $source->node( $nodeId );
                $parent = isset( $after['parent'] ) ? $source->nodeRef( (int)$after['parent'] ) : null;
                if ( $location === null || $parent === null )
                    return self::missing( $event, 'node', $nodeId );
                $event['subject'] = 'location remote:' . $location['remote_id'];
                $event['step'] += array( 'match' => 'remote:' . $location['remote_id'], 'parent' => $parent );
                return true;
            case 'content.node.copy':
                $newNode = isset( $after['node'] ) ? (int)$after['node'] : 0;
                $from = $source->nodeRef( $nodeId );
                $copy = $newNode ? $source->node( $newNode ) : null;
                if ( $from === null || $copy === null )
                    return self::missing( $event, 'node', $nodeId );
                $copied = $source->object( (int)$copy['object_id'] );
                $event['subject'] = 'location remote:' . $copy['remote_id'];
                $event['step'] += array( 'node' => $from, 'parent' => $copy['parent'],
                                         'remote_id' => $copied ? $copied['remote_id'] : null, 'location_remote_id' => $copy['remote_id'] );
                if ( isset( $after['nodes'] ) && (int)$after['nodes'] > 1 )
                    $event['step']['subtree'] = true;
                return true;
            case 'content.node.hide':
            case 'content.node.reveal':
            case 'content.node.sort':
            case 'content.node.priority':
                $location = $source->node( $nodeId );
                if ( $location === null )
                    return self::missing( $event, 'node', $nodeId );
                $event['subject'] = 'location remote:' . $location['remote_id'];
                $event['step']['match'] = 'remote:' . $location['remote_id'];
                // the sort is recorded before it is stored: the values come from the event
                if ( $name === 'content.node.sort' )
                    $event['step'] += array( 'sort_field' => isset( $after['field'] ) ? self::sortFieldName( (int)$after['field'] ) : $location['sort_field'],
                                             'sort_order' => isset( $after['order'] ) ? ( (int)$after['order'] === 1 ? 'asc' : 'desc' ) : $location['sort_order'] );
                if ( $name === 'content.node.priority' )
                {
                    $priorities = array();
                    foreach ( isset( $after['priorities'] ) ? (array)$after['priorities'] : array() as $childId => $priority )
                    {
                        $child = $source->nodeRef( (int)$childId );
                        if ( $child !== null )
                            $priorities[$child] = (int)$priority;
                    }
                    $event['step']['child_priorities'] = $priorities;
                }
                return true;
            case 'content.node.main':
                $mainId = isset( $after['main_node'] ) ? (int)$after['main_node'] : ( isset( $target['id'] ) ? (int)$target['id'] : 0 );
                $location = $mainId ? $source->node( $mainId ) : null;
                if ( $location === null )
                    return self::missing( $event, 'node', $mainId );
                $event['subject'] = 'location remote:' . $location['remote_id'];
                $event['step'] += array( 'match' => 'remote:' . $location['remote_id'], 'main' => true );
                return true;
            case 'content.node.swap':
                $one = $source->nodeRef( $nodeId );
                $two = isset( $target['id'] ) ? $source->nodeRef( (int)$target['id'] ) : null;
                if ( $one === null || $two === null )
                    return self::missing( $event, 'node', $nodeId );
                $event['subject'] = 'location ' . $one;
                $event['step'] += array( 'match' => $one, 'with' => $two );
                return true;
            case 'content.node.section':
                $location = $source->nodeRef( $nodeId );
                $section = isset( $target['id'] ) ? $source->sectionIdentifier( (int)$target['id'] )
                         : ( isset( $after['section'] ) ? $source->sectionIdentifier( (int)$after['section'] ) : null );
                if ( $location === null || $section === null )
                    return self::missing( $event, 'node', $nodeId );
                $event['subject'] = 'location ' . $location;
                $event['step'] += array( 'match' => $section, 'subtree' => $location );
                return true;
            case 'content.node.remove':
            case 'content.node.remove.trash':
                $ref = $source->nodeRef( $nodeId );
                if ( $ref === null )
                    return self::missing( $event, 'node', $nodeId );
                $event['subject'] = 'location ' . $ref;
                $event['step']['match'] = $ref;
                return true;
        }
        return false;
    }

    /** content.class.* */
    protected static function contentClass( $name, array $object, array $target, expMigrationRecordingSource $source, array &$event )
    {
        $identifier = isset( $object['identifier'] ) ? (string)$object['identifier'] : '';
        if ( $name === 'content.class.remove' )
        {
            if ( $identifier === '' )
                return self::missing( $event, 'class', isset( $object['id'] ) ? (int)$object['id'] : 0 );
            $event['subject'] = 'class ' . $identifier;
            $event['step']['match'] = $identifier;
            return true;
        }
        // a copy: the new class is the target
        $id = $name === 'content.class.copy' && isset( $target['id'] ) ? (int)$target['id'] : ( isset( $object['id'] ) ? (int)$object['id'] : $identifier );
        $definition = $source->contentClass( $id );
        if ( $definition === null && $identifier !== '' )
            $definition = $source->contentClass( $identifier );
        if ( $definition === null )
            return self::missing( $event, 'class', is_int( $id ) ? $id : 0 );
        $event['subject'] = 'class ' . $definition['identifier'];
        $step = array( 'type' => 'class', 'mode' => $event['step']['mode'] );
        if ( $step['mode'] === 'create' )
            $step['identifier'] = $definition['identifier'];
        else
            $step['match'] = $definition['identifier'];
        unset( $definition['identifier'] );
        $event['step'] = $step + $definition;
        return true;
    }

    /** content.section.* */
    protected static function section( $name, array $object, array $after, expMigrationRecordingSource $source, array &$event )
    {
        $identifier = isset( $object['identifier'] ) && $object['identifier'] !== '' ? (string)$object['identifier']
                    : ( isset( $after['identifier'] ) ? (string)$after['identifier'] : '' );
        if ( $name === 'content.section.remove' )
        {
            if ( $identifier === '' )
                return self::missing( $event, 'section', isset( $object['id'] ) ? (int)$object['id'] : 0 );
            $event['subject'] = 'section ' . $identifier;
            $event['step']['match'] = $identifier;
            return true;
        }
        $section = isset( $object['id'] ) ? $source->section( (int)$object['id'] ) : null;
        if ( $section === null )
            return self::missing( $event, 'section', isset( $object['id'] ) ? (int)$object['id'] : 0 );
        $event['subject'] = 'section ' . $section['identifier'];
        $event['step'] += array( 'match' => $section['identifier'] ) + $section;
        return true;
    }

    /** content.state.* */
    protected static function stateGroup( $name, array $object, array $after, expMigrationRecordingSource $source, array &$event )
    {
        $identifier = isset( $object['identifier'] ) ? (string)$object['identifier'] : ( isset( $after['identifier'] ) ? (string)$after['identifier'] : '' );
        if ( $name === 'content.state.remove' )
        {
            if ( $identifier === '' )
                return self::missing( $event, 'state group', isset( $object['id'] ) ? (int)$object['id'] : 0 );
            $event['subject'] = 'state_group ' . $identifier;
            $event['step']['match'] = $identifier;
            return true;
        }
        $group = $source->stateGroup( isset( $object['id'] ) && (int)$object['id'] > 0 ? (int)$object['id'] : $identifier );
        if ( $group === null )
            return self::missing( $event, 'state group', isset( $object['id'] ) ? (int)$object['id'] : 0 );
        $event['subject'] = 'state_group ' . $group['identifier'];
        $event['step'] += array( 'match' => $group['identifier'] ) + $group;
        return true;
    }

    /** content.urlalias.change */
    protected static function urlAlias( array $data, array $object, array $target, array $before, array $after, expMigrationRecordingSource $source,
                                        array &$event )
    {
        $removed = isset( $data['verb'] ) && $data['verb'] === 'remove';
        $alias = isset( $after['alias'] ) ? (string)$after['alias'] : ( isset( $object['id'] ) ? (string)$object['id'] : '' );
        $node = isset( $target['type'], $target['id'] ) && $target['type'] === 'node' ? $source->nodeRef( (int)$target['id'] ) : null;
        if ( $removed )
        {
            $event['step']['mode'] = 'delete';
            $event['subject'] = 'url_alias ' . ( $node !== null ? $node : 'global' );
            $event['step']['match'] = $node !== null ? $node : 'global';
            $event['step']['which'] = isset( $before['removed'] ) ? (string)$before['removed'] : 'selected';
            $event['notes'][] = 'the aliases removed are not named by the kernel: check the generated step';
            return true;
        }
        if ( $alias === '' )
            return false;
        $event['subject'] = 'url_alias ' . $alias;
        $event['step']['path'] = $alias;
        if ( $node !== null )
            $event['step']['node'] = $node;
        return true;
    }

    // ------------------------------------------------------------------ access

    /** access.role.* and access.policy.* */
    protected static function role( $name, array $object, array $target, array $before, array $after, expMigrationRecordingSource $source,
                                    array &$event )
    {
        // the role is the object, or the target of a policy event
        $roleDesc = $name === 'access.policy.add' || $name === 'access.policy.remove' ? $target : $object;
        if ( $name === 'access.role.copy' && isset( $target['type'] ) && $target['type'] === 'role' )
            $roleDesc = $target;
        $roleName = isset( $roleDesc['name'] ) ? (string)$roleDesc['name'] : '';
        if ( $name === 'access.role.remove' )
        {
            if ( $roleName === '' )
                return self::missing( $event, 'role', isset( $roleDesc['id'] ) ? (int)$roleDesc['id'] : 0 );
            $event['subject'] = 'role ' . $roleName;
            $event['step']['match'] = $roleName;
            return true;
        }
        if ( $name === 'access.role.assign' || $name === 'access.role.unassign' )
        {
            $who = isset( $target['id'] ) ? $source->objectRef( (int)$target['id'] ) : null;
            if ( $roleName === '' || $who === null )
                return self::missing( $event, 'role assignment', isset( $target['id'] ) ? (int)$target['id'] : 0 );
            $event['subject'] = 'role ' . $roleName;
            $event['step']['match'] = $roleName;
            $event['step'][isset( $target['type'] ) && $target['type'] === 'group' ? 'group' : 'user'] = $who;
            $limitation = $name === 'access.role.assign' ? ( isset( $after['limitation'] ) ? $after['limitation'] : null )
                                                         : ( isset( $before['limitation'] ) ? $before['limitation'] : null );
            if ( is_array( $limitation ) && isset( $limitation['identifier'] ) && $limitation['identifier'] !== '' )
            {
                $identifier = strtolower( (string)$limitation['identifier'] );
                if ( $identifier === 'subtree' )
                {
                    $nodeId = isset( $limitation['node'] ) ? (int)$limitation['node'] : 0;
                    $ref = $nodeId ? $source->nodeRef( $nodeId ) : null;
                    if ( $ref === null )
                        $event['notes'][] = 'the subtree of the limitation is kept as its path: ' . (string)$limitation['value'];
                    $event['step']['limitation'] = array( 'subtree' => $ref !== null ? $ref : (string)$limitation['value'] );
                }
                else if ( $identifier === 'section' )
                {
                    $section = $source->sectionIdentifier( (int)$limitation['value'] );
                    $event['step']['limitation'] = array( 'section' => $section !== null ? $section : (string)$limitation['value'] );
                }
                else
                    $event['step']['limitation'] = array( $identifier => (string)$limitation['value'] );
            }
            return true;
        }
        $role = $source->role( isset( $roleDesc['id'] ) && (int)$roleDesc['id'] > 0 ? (int)$roleDesc['id'] : $roleName );
        if ( $role === null )
            return self::missing( $event, 'role', isset( $roleDesc['id'] ) ? (int)$roleDesc['id'] : 0 );
        $event['subject'] = 'role ' . $role['name'];
        $step = array( 'type' => 'role', 'mode' => $event['step']['mode'] );
        if ( $step['mode'] === 'create' )
            $step['name'] = $role['name'];
        else
            $step['match'] = $role['name'];
        $step['policies'] = $role['policies'];
        foreach ( isset( $role['notes'] ) ? (array)$role['notes'] : array() as $note )
            $event['notes'][] = $note;
        $event['step'] = $step;
        return true;
    }

    /** access.user.* */
    protected static function user( $name, array $object, array $target, array $before, array $after, expMigrationRecordingSource $source,
                                    array $keep, array &$event )
    {
        $userId = isset( $object['id'] ) ? (int)$object['id'] : 0;
        if ( $userId <= 0 )
            return false;
        if ( $name === 'access.user.remove' )
        {
            $ref = $source->objectRef( $userId );
            if ( $ref === null )
                return self::missing( $event, 'user', $userId );
            $event['subject'] = 'user ' . $ref;
            $event['step']['match'] = $ref;
            return true;
        }
        $snapshot = $source->object( $userId );
        if ( $snapshot === null )
            return self::missing( $event, 'user', $userId );
        $event['subject'] = 'user remote:' . $snapshot['remote_id'];
        self::userBody( $snapshot, $keep, $event );
        return true;
    }

    // ------------------------------------------------------------------ system

    /** system.setting.write */
    protected static function setting( array $object, array $before, array $after, array &$event )
    {
        foreach ( array( 'file', 'block', 'variable' ) as $key )
            if ( !isset( $object[$key] ) || (string)$object[$key] === '' )
                return false;
        $path = isset( $object['path'] ) ? (string)$object['path'] : '';
        $event['subject'] = 'setting ' . $object['file'] . ' [' . $object['block'] . '] ' . $object['variable'];
        $step = array( 'type' => 'ini', 'mode' => 'set', 'file' => (string)$object['file'], 'scope' => self::settingScope( $path, isset( $object['scope'] ) ? (string)$object['scope'] : '' ),
                       'block' => (string)$object['block'], 'variable' => (string)$object['variable'] );
        $set = !isset( $after['set'] ) || $after['set'];
        if ( !$set )
            $step['mode'] = 'remove';
        else
        {
            $value = array_key_exists( 'value', $after ) ? $after['value'] : null;
            $secret = $value === self::SECRET_MARK || ( is_array( $value ) && in_array( self::SECRET_MARK, $value, true ) )
                   || ( class_exists( 'expAuditPrivacy' ) && expAuditPrivacy::isSecretName( (string)$object['variable'] ) );
            if ( $secret )
            {
                $event['omitted'][] = 'value (a secret)';
                $event['notes'][] = 'a secret: the generated step names it by a secret reference, never by its value';
                $step['secret'] = true;
            }
            else
                $step['value'] = $value;
        }
        $event['step'] = $step;
        return true;
    }

    /**
     * The scope of a settings file by its path: override, siteaccess:<name> or extension:<name>. Pure.
     *
     * @param string $path settings/override/site.ini.append.php ...
     * @param string $given the scope the audit gave
     * @return string
     */
    public static function settingScope( $path, $given = '' )
    {
        $path = str_replace( '\\', '/', (string)$path );
        if ( preg_match( '#^settings/siteaccess/([^/]+)/#', $path, $m ) )
            return 'siteaccess:' . $m[1];
        if ( preg_match( '#^extension/([^/]+)/settings/#', $path, $m ) )
            return 'extension:' . $m[1];
        if ( strpos( $path, 'settings/override/' ) === 0 )
            return 'override';
        return $given !== '' ? $given : 'override';
    }

    /** system.workflow.trigger.change */
    protected static function trigger( array $before, array $after, array &$event )
    {
        $workflows = isset( $after['workflows'] ) ? (array)$after['workflows'] : array();
        if ( !$workflows )
            return false;
        $triggers = array();
        foreach ( $workflows as $key => $workflowId )
        {
            $parts = explode( '/', (string)$key );
            if ( count( $parts ) !== 3 )
                continue;
            $triggers[] = array( 'module' => $parts[0], 'function' => $parts[1], 'connect' => $parts[2],
                                 'workflow' => $workflowId === null ? null : (int)$workflowId );
        }
        $event['subject'] = 'workflow_trigger ' . implode( ',', array_keys( $workflows ) );
        $event['step']['triggers'] = $triggers;
        $event['notes'][] = 'workflows are named by id: workflows have no identifier yet';
        return true;
    }

    // ------------------------------------------------------------------ helpers

    /** The names of the kernel's sort fields (eZContentObjectTreeNode::sortFieldName()), by id. */
    const SORT_FIELDS = array( 1 => 'path', 2 => 'published', 3 => 'modified', 4 => 'section', 5 => 'depth', 6 => 'class_identifier',
                               7 => 'class_name', 8 => 'priority', 9 => 'name', 10 => 'modified_subnode', 11 => 'node_id',
                               12 => 'contentobject_id', 13 => 'is_invisible' );

    /**
     * The name of a sort field id, as a content step names it ("path" for an unknown one, as the kernel does). Pure.
     *
     * @param int $id
     * @return string
     */
    public static function sortFieldName( $id )
    {
        return isset( self::SORT_FIELDS[(int)$id] ) ? self::SORT_FIELDS[(int)$id] : 'path';
    }

    /** A thing an event names that is gone: nothing to record. */
    protected static function missing( array &$event, $kind, $id )
    {
        return false;
    }

    /**
     * The value of a recorded event's payload as JSON (stable key order as built, no escaped slashes). Pure.
     *
     * @param array $event
     * @return string
     */
    public static function encode( array $event )
    {
        return json_encode( $event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
    }

    /**
     * A payload back as an event, or null when it is not one.
     *
     * @param string $json
     * @return array|null
     */
    public static function decode( $json )
    {
        $event = json_decode( (string)$json, true );
        return is_array( $event ) && isset( $event['audit'], $event['step'] ) ? $event : null;
    }

    /**
     * One line for a recorded event: "<step type>/<mode> <subject>". Pure.
     *
     * @param array $event
     * @return string
     */
    public static function line( array $event )
    {
        $step = isset( $event['step'] ) ? (array)$event['step'] : array();
        return ( isset( $step['type'] ) ? $step['type'] : '?' ) . '/' . ( isset( $step['mode'] ) ? $step['mode'] : '?' )
             . ( isset( $event['subject'] ) && $event['subject'] !== '' ? ' ' . $event['subject'] : '' );
    }
}

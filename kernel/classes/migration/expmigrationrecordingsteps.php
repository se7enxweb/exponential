<?php
/**
 * File containing the expMigrationRecordingSteps class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The steps of a YAML migration made from recorded steps (expMigrationRecordingEvent, folded by
 * expMigrationRecordingGenerator::plan()): each recorded step in the form of the step type that repeats it (content,
 * location, content_class, role, user, section, object_state_group, url_alias, trash, settings), with the keys that
 * step type takes. Pure.
 *
 * A recorded change no step type can repeat (a swap of two locations, a workflow trigger, a setting of an extension, a
 * secret setting) becomes no step: it is listed as manual, with the reason, and the generated file names it in a
 * comment. What a step cannot carry (the remote id of a further location, object states) is said in the step's
 * comment.
 *
 * Personal data (open question 16, answer a): a user step names the login, the e-mail address and the password by
 * secrets (secrets: login, email, password => a name of the secret store), never by value; the recording has none of
 * them anyway. Recorded texts that contain "${" are written as "$${" so that the engine does not read them as
 * placeholders.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationRecordingSteps
{
    /** The sort fields a location and a class step take (expMigrationStepLocation::SORT_FIELDS). */
    const SORT_FIELDS = array( 'path', 'published', 'modified', 'section', 'depth', 'class_identifier', 'class_name', 'priority', 'name' );

    /** The step types whose create becomes an ensure with fromPlan( ..., array( 'ensure' => true ) ). */
    const ENSURE_TYPES = array( 'content', 'content_class', 'role', 'section', 'object_state_group' );

    /** The step type of each recorded type; the recorded types not in it have no step type. */
    const TYPES = array( 'content' => 'content', 'location' => 'location', 'trash' => 'trash', 'class' => 'content_class', 'role' => 'role',
                         'user' => 'user', 'section' => 'section', 'object_state_group' => 'object_state_group', 'url_alias' => 'url_alias',
                         'ini' => 'settings', 'workflow_trigger' => null );

    /**
     * The step types the migration of a recorded type and mode is made of (empty: none repeats it). Pure.
     *
     * @param string $type
     * @param string $mode
     * @return string[]
     */
    public static function typesOf( $type, $mode )
    {
        if ( $type === 'content' && $mode === 'copy' )
            return array( 'location' );
        if ( !array_key_exists( $type, self::TYPES ) )
            return $type === '' ? array() : array( $type );
        return self::TYPES[$type] === null ? array() : array( self::TYPES[$type] );
    }

    /**
     * The migration steps of a plan.
     *
     * @param array[] $plan expMigrationRecordingGenerator::plan(): each subject, step, audit, omitted, notes
     * @param array $options ensure: true makes a create of content, a class, a role, a section or an object state
     *                       group an ensure (what is there already is changed to match instead of failing)
     * @return array steps (the YAML steps), manual (string[]: what no step repeats, with the reason)
     */
    public static function fromPlan( array $plan, array $options = array() )
    {
        $ensure = !empty( $options['ensure'] );
        $steps = array();
        $manual = array();
        foreach ( $plan as $entry )
        {
            $notes = isset( $entry['notes'] ) ? (array)$entry['notes'] : array();
            $audit = isset( $entry['audit'] ) ? (array)$entry['audit'] : array();
            $reason = null;
            $made = self::step( (array)$entry['step'], $audit, $notes, $reason );
            if ( $reason !== null )
            {
                $manual[] = expMigrationRecordingEvent::line( array( 'step' => $entry['step'], 'subject' => isset( $entry['subject'] ) ? $entry['subject'] : '' ) )
                          . ': ' . $reason;
                continue;
            }
            $omitted = isset( $entry['omitted'] ) ? array_values( array_unique( (array)$entry['omitted'] ) ) : array();
            if ( $omitted )
                $notes[] = 'left out: ' . implode( ', ', $omitted );
            foreach ( $made as $i => $step )
            {
                if ( $ensure && isset( $step['mode'] ) && $step['mode'] === 'create' && in_array( $step['type'], self::ENSURE_TYPES, true ) )
                    $step['mode'] = 'ensure';
                if ( $i === 0 && $notes )
                    $step['comment'] = implode( '; ', array_values( array_unique( $notes ) ) );
                $steps[] = self::escape( $step );
            }
        }
        return array( 'steps' => $steps, 'manual' => $manual );
    }

    /**
     * The migration steps of one recorded step.
     *
     * @param array $r the recorded step (type, mode and its keys)
     * @param string[] $audit the audit names folded into it
     * @param string[] $notes notes for the comment of the first step (added to)
     * @param string|null $reason set when no step type repeats it
     * @return array[] the steps (empty when $reason is set)
     */
    public static function step( array $r, array $audit, array &$notes, &$reason )
    {
        $reason = null;
        $type = isset( $r['type'] ) ? (string)$r['type'] : '';
        $mode = isset( $r['mode'] ) ? (string)$r['mode'] : '';
        switch ( $type )
        {
            case 'content':
                return self::content( $r, $mode, $notes, $reason );
            case 'location':
                return self::location( $r, $mode, $notes, $reason );
            case 'trash':
                if ( $mode === 'move' )
                    return array( array( 'type' => 'trash', 'mode' => 'move', 'node' => $r['match'] ) );
                $step = array( 'type' => 'trash', 'mode' => 'restore', 'object' => $r['object'] );
                if ( isset( $r['parent'] ) && $r['parent'] !== null )
                    $step['parent'] = $r['parent'];
                return array( $step );
            case 'class':
                return self::contentClass( $r, $mode, $notes );
            case 'role':
                return self::role( $r, $mode, $audit, $notes );
            case 'user':
                return self::user( $r, $mode, $notes );
            case 'section':
                if ( $mode === 'assign' )
                    return array( array( 'type' => 'section', 'mode' => 'assign', 'section' => $r['match'], 'node' => $r['subtree'], 'scope' => 'subtree' ) );
                if ( $mode === 'delete' )
                    return array( array( 'type' => 'section', 'mode' => 'delete', 'section' => $r['match'] ) );
                $step = array( 'type' => 'section', 'mode' => 'ensure', 'identifier' => $r['match'] );
                foreach ( array( 'name', 'navigation_part' ) as $key )
                    if ( isset( $r[$key] ) && $r[$key] !== '' )
                        $step[$key] = (string)$r[$key];
                if ( !empty( $r['component'] ) )
                    $notes[] = 'the component of the section (' . $r['component'] . ') is not set by the step';
                return array( $step );
            case 'object_state_group':
                return self::stateGroup( $r, $mode );
            case 'url_alias':
                return self::urlAlias( $r, $mode, $notes, $reason );
            case 'ini':
                return self::setting( $r, $mode, $reason );
            case 'workflow_trigger':
                $reason = 'workflow triggers have no step type (workflows are named by id): set them in Setup > Triggers';
                return array();
        }
        // a step type of the migration already (a recording of a later kernel, an extension's own event)
        if ( $type !== '' && isset( $r['type'] ) )
            return array( $r );
        $reason = 'no step type repeats it';
        return array();
    }

    /** content */
    protected static function content( array $r, $mode, array &$notes, &$reason )
    {
        if ( $mode === 'delete' )
            return array( array( 'type' => 'content', 'mode' => 'delete', 'object' => $r['match'] ) );
        if ( $mode === 'copy' )
        {
            $step = array( 'type' => 'location', 'mode' => 'copy', 'node' => $r['node'], 'parent' => $r['parent'] );
            $notes[] = 'the copy gets remote ids of its own' . ( !empty( $r['subtree'] ) ? '; the step copies the object of the node, not the nodes below it' : '' );
            return array( $step );
        }
        $step = array( 'type' => 'content', 'mode' => $mode === 'create' ? 'create' : 'update' );
        if ( $mode === 'create' )
        {
            $step['class'] = $r['class'];
            $step['parent'] = $r['parent'];
            $step['remote_id'] = $r['remote_id'];
            if ( isset( $r['section'] ) )
                $step['section'] = $r['section'];
            if ( isset( $r['always_available'] ) )
                $step['always_available'] = (bool)$r['always_available'];
        }
        else
            $step['object'] = $r['match'];
        $fields = isset( $r['fields'] ) && is_array( $r['fields'] ) ? $r['fields'] : array();
        $language = isset( $r['initial_language'] ) && isset( $fields[$r['initial_language']] ) ? (string)$r['initial_language'] : (string)key( $fields );
        if ( $fields )
        {
            $step['language'] = $language;
            $step['fields'] = (array)$fields[$language];
            unset( $fields[$language] );
            if ( $fields )
                $step['translations'] = $fields;
        }
        if ( $mode !== 'create' && empty( $step['fields'] ) && empty( $step['translations'] ) )
        {
            $reason = 'nothing of the object\'s fields was recorded';
            return array();
        }
        if ( !empty( $r['remove_translations'] ) )
            $notes[] = 'translations removed: ' . implode( ', ', (array)$r['remove_translations'] ) . ' (no step removes a translation: remove it by hand)';
        if ( !empty( $r['states'] ) )
        {
            $states = array();
            foreach ( (array)$r['states'] as $group => $state )
                $states[] = "$group/$state";
            $notes[] = 'object states not set by the step: ' . implode( ', ', $states );
        }
        $steps = array( $step );
        if ( $mode === 'create' && !empty( $r['locations'] ) )
        {
            foreach ( (array)$r['locations'] as $location )
                $steps[] = array( 'type' => 'location', 'mode' => 'create', 'object' => 'remote:' . $r['remote_id'], 'parent' => $location['parent'] );
            $notes[] = 'further locations get remote ids of their own';
        }
        return $steps;
    }

    /** location */
    protected static function location( array $r, $mode, array &$notes, &$reason )
    {
        switch ( $mode )
        {
            case 'create':
                $notes[] = 'the new location gets a remote id of its own (recorded: ' . $r['remote_id'] . ')';
                return array( array( 'type' => 'location', 'mode' => 'create', 'object' => $r['object'], 'parent' => $r['parent'] ) );
            case 'move':
                return array( array( 'type' => 'location', 'mode' => 'move', 'node' => $r['match'], 'parent' => $r['parent'] ) );
            case 'hide':
                return array( array( 'type' => 'location', 'mode' => 'hide', 'node' => $r['match'] ) );
            case 'reveal':
                return array( array( 'type' => 'location', 'mode' => 'unhide', 'node' => $r['match'] ) );
            case 'delete':
                return array( array( 'type' => 'location', 'mode' => 'delete', 'node' => $r['match'] ) );
            case 'swap':
                $reason = 'a swap of two locations has no step: swap ' . $r['match'] . ' and ' . $r['with'] . ' by hand';
                return array();
        }
        $steps = array();
        $step = array( 'type' => 'location', 'mode' => 'update', 'node' => $r['match'] );
        if ( isset( $r['sort_field'] ) )
        {
            if ( in_array( $r['sort_field'], self::SORT_FIELDS, true ) )
                $step['sort_field'] = $r['sort_field'];
            else
                $notes[] = 'the sort field ' . $r['sort_field'] . ' is not one a step sets';
        }
        if ( isset( $r['sort_order'] ) )
            $step['sort_order'] = $r['sort_order'];
        if ( !empty( $r['main'] ) )
            $step['main'] = true;
        if ( count( $step ) > 3 )
            $steps[] = $step;
        foreach ( isset( $r['child_priorities'] ) ? (array)$r['child_priorities'] : array() as $child => $priority )
            $steps[] = array( 'type' => 'location', 'mode' => 'update', 'node' => (string)$child, 'priority' => (int)$priority );
        if ( !$steps )
            $reason = 'nothing of the location was recorded';
        return $steps;
    }

    /** class -> content_class */
    protected static function contentClass( array $r, $mode, array &$notes )
    {
        if ( $mode === 'delete' )
            return array( array( 'type' => 'content_class', 'mode' => 'delete', 'class' => $r['match'] ) );
        $identifier = $mode === 'create' ? $r['identifier'] : $r['match'];
        // a change is the whole definition after it: ensure makes the class so (create when missing)
        $step = array( 'type' => 'content_class', 'mode' => $mode === 'create' ? 'create' : 'ensure', 'identifier' => $identifier );
        if ( $mode === 'create' && !empty( $r['remote_id'] ) )
            $step['remote_id'] = (string)$r['remote_id'];
        $map = array( 'names' => 'names', 'descriptions' => 'descriptions', 'contentobject_name' => 'name_pattern', 'url_alias_name' => 'url_alias_pattern',
                      'is_container' => 'container', 'always_available' => 'always_available', 'sort_order' => 'sort_order', 'groups' => 'groups' );
        foreach ( $map as $from => $to )
            if ( isset( $r[$from] ) && $r[$from] !== '' && $r[$from] !== array() )
                $step[$to] = $r[$from];
        if ( isset( $r['sort_field'] ) )
        {
            if ( in_array( $r['sort_field'], self::SORT_FIELDS, true ) )
                $step['sort_field'] = $r['sort_field'];
            else
                $notes[] = 'the sort field ' . $r['sort_field'] . ' is not one a step sets';
        }
        $attributes = array();
        foreach ( isset( $r['attributes'] ) ? (array)$r['attributes'] : array() as $id => $a )
        {
            $attribute = array( 'identifier' => (string)$id, 'type' => (string)$a['datatype'] );
            foreach ( array( 'names', 'descriptions', 'required', 'searchable', 'translatable', 'information_collector', 'category', 'placement', 'settings' ) as $key )
                if ( isset( $a[$key] ) && $a[$key] !== array() )
                    $attribute[$key] = $a[$key];
            $attributes[] = $attribute;
        }
        if ( $attributes )
            $step['attributes'] = $attributes;
        if ( $step['mode'] === 'ensure' )
            $notes[] = 'ensure adds the attributes the class lacks and changes those that differ; it removes none';
        return array( $step );
    }

    /** role */
    protected static function role( array $r, $mode, array $audit, array &$notes )
    {
        switch ( $mode )
        {
            case 'delete':
                return array( array( 'type' => 'role', 'mode' => 'delete', 'role' => $r['match'] ) );
            case 'assign':
            case 'unassign':
                $step = array( 'type' => 'role', 'mode' => $mode, 'role' => $r['match'] );
                if ( isset( $r['user'] ) )
                    $step['users'] = array( $r['user'] );
                if ( isset( $r['group'] ) )
                    $step['groups'] = array( $r['group'] );
                foreach ( isset( $r['limitation'] ) ? (array)$r['limitation'] : array() as $key => $value )
                {
                    if ( $key === 'subtree' || $key === 'section' )
                        $step[$key] = $value;
                    else
                        $notes[] = "the limitation $key ($value) of the assignment is not set by the step";
                }
                return array( $step );
        }
        $step = array( 'type' => 'role', 'mode' => $mode === 'create' ? 'create' : 'ensure', 'name' => $mode === 'create' ? $r['name'] : $r['match'] );
        $step['policies'] = isset( $r['policies'] ) ? array_values( (array)$r['policies'] ) : array();
        if ( $step['mode'] === 'ensure' && in_array( 'access.policy.remove', $audit, true ) )
            $notes[] = 'a policy was removed: ensure adds the policies the role lacks and removes none (remove_policies: of mode update does)';
        return array( $step );
    }

    /** user: the account by secrets */
    protected static function user( array $r, $mode, array &$notes )
    {
        if ( $mode === 'delete' )
            return array( array( 'type' => 'user', 'mode' => 'delete', 'user' => $r['match'] ) );
        $step = array( 'type' => 'user', 'mode' => $mode === 'create' ? 'create' : 'update' );
        if ( $mode === 'create' )
        {
            $name = self::secretName( isset( $r['remote_id'] ) ? (string)$r['remote_id'] : '' );
            $step['secrets'] = array( 'login' => $name . '_login', 'email' => $name . '_email', 'password' => $name . '_password' );
            $step['remote_id'] = $r['remote_id'];
            if ( isset( $r['class'] ) && $r['class'] !== 'user' )
                $step['class'] = $r['class'];
            $notes[] = 'set the secrets ' . implode( ', ', $step['secrets'] ) . ' (Setup > Secrets, or env:<variable>) before the run';
        }
        else
            $step['user'] = $r['match'];
        if ( !empty( $r['groups'] ) )
            $step['groups'] = array_values( (array)$r['groups'] );
        if ( isset( $r['enabled'] ) )
            $step['enabled'] = (bool)$r['enabled'];
        if ( !empty( $r['fields'] ) && is_array( $r['fields'] ) )
        {
            $language = (string)key( $r['fields'] );
            $step['language'] = $language;
            $step['fields'] = (array)$r['fields'][$language];
        }
        return array( $step );
    }

    /** object_state_group */
    protected static function stateGroup( array $r, $mode )
    {
        if ( $mode === 'delete' )
            return array( array( 'type' => 'object_state_group', 'mode' => 'delete', 'group' => $r['match'] ) );
        $step = array( 'type' => 'object_state_group', 'mode' => 'ensure', 'identifier' => $r['match'] );
        if ( !empty( $r['names'] ) )
            $step['names'] = $r['names'];
        if ( !empty( $r['descriptions'] ) )
            $step['descriptions'] = $r['descriptions'];
        $states = array();
        foreach ( isset( $r['states'] ) ? (array)$r['states'] : array() as $identifier => $names )
        {
            $state = array( 'identifier' => (string)$identifier );
            if ( is_array( $names ) && $names )
                $state['names'] = isset( $names['names'] ) && is_array( $names['names'] ) ? $names['names'] : $names;
            $states[] = $state;
        }
        if ( $states )
            $step['states'] = $states;
        return array( $step );
    }

    /** url_alias */
    protected static function urlAlias( array $r, $mode, array &$notes, &$reason )
    {
        if ( $mode === 'delete' )
        {
            if ( isset( $r['which'] ) && $r['which'] === 'all' && $r['match'] !== 'global' )
                return array( array( 'type' => 'url_alias', 'mode' => 'delete', 'node' => $r['match'], 'which' => 'all' ) );
            $reason = 'the kernel does not name the addresses that were removed: remove them by hand or write the step with path:';
            return array();
        }
        if ( !isset( $r['node'] ) )
        {
            $reason = 'a global address without its destination: write the step with destination:';
            return array();
        }
        return array( array( 'type' => 'url_alias', 'mode' => 'ensure', 'path' => $r['path'], 'node' => $r['node'] ) );
    }

    /** ini -> settings */
    protected static function setting( array $r, $mode, &$reason )
    {
        $scope = isset( $r['scope'] ) ? (string)$r['scope'] : 'override';
        if ( strpos( $scope, 'extension:' ) === 0 )
        {
            $reason = 'settings of an extension are not written by migrations: change them in the extension';
            return array();
        }
        if ( !empty( $r['secret'] ) )
        {
            $reason = 'a secret: set it on each installation by hand (or keep it in the secret store), never in a migration';
            return array();
        }
        $file = (string)$r['file'];
        if ( substr( $file, -4 ) !== '.ini' )
            $file = preg_replace( '/\.ini\..*$/', '.ini', $file );
        $step = array( 'type' => 'settings', 'file' => $file );
        if ( strpos( $scope, 'siteaccess:' ) === 0 )
            $step['siteaccess'] = substr( $scope, 11 );
        if ( $mode === 'remove' )
            $step['remove'] = array( (string)$r['block'] => array( (string)$r['variable'] ) );
        else
            $step['set'] = array( (string)$r['block'] => array( (string)$r['variable'] => isset( $r['value'] ) ? $r['value'] : '' ) );
        return array( $step );
    }

    /**
     * The name of the secrets of a user from its remote id. Pure.
     *
     * @param string $remoteId
     * @return string user_<12 characters>
     */
    public static function secretName( $remoteId )
    {
        $clean = strtolower( preg_replace( '/[^A-Za-z0-9]+/', '', (string)$remoteId ) );
        return 'user_' . ( $clean !== '' ? substr( $clean, 0, 12 ) : 'new' );
    }

    /**
     * A step with "${" in its values written as "$${" (a recorded text is no placeholder). Keys stay. Pure.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function escape( $value )
    {
        if ( is_array( $value ) )
        {
            foreach ( $value as $key => $item )
                $value[$key] = self::escape( $item );
            return $value;
        }
        return is_string( $value ) ? str_replace( '${', '$${', $value ) : $value;
    }
}

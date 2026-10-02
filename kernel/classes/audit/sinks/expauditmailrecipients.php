<?php
/**
 * Who gets the audit's alert mail (doc/bc/6.0/audit.md, "E-mail"; owner decision 2026-10-02).
 *
 * Recipients are named in the settings only, never taken from event data. A recipient is one of:
 *
 *   admin                       the site's site.ini [MailSettings] AdminEmail
 *   address:ops@example.com     an e-mail address (a bare address with "@" is the same)
 *   group:security              the named list [AlertRecipients_security] Addresses[] and Recipients[]
 *                               (Recipients[] may name any kind here, other groups too; loops are cut)
 *   user:14                     the user with that content object id
 *   login:editor1               the user with that login
 *   usergroup:12                every user under the user group with that node id, sub-groups included
 *   usergroup:<remote id>       the same, by the node's or the object's remote id
 *   role:Administrator          every user the role is assigned to, directly or through a user group
 *                               (role:<id> works too); a limited assignment counts as well
 *
 * Users are resolved when the mail is sent, with their current e-mail address; disabled users and users without a
 * valid address are left out. Addresses are compared case-insensitively and each one gets one mail.
 *
 * Which list applies: an alert uses its rule's [AlertRule_<rule>] Recipients[] when it has any; otherwise, and for
 * any other record mailed, [AuditAlertSettings] Recipients[]; then [AuditSink_mail] Receivers[] (the older name,
 * same syntax); then "admin".
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditMailRecipients
{
    /** Kinds of recipient */
    const KINDS = array( 'admin', 'address', 'group', 'user', 'login', 'usergroup', 'role' );

    /**
     * The recipient specifications that apply to a record (from the settings only).
     *
     * @param array|null $record a mailed record; for system.audit.alert its rule decides
     * @return array specs, from (rule:<name>, AuditAlertSettings, AuditSink_mail, default)
     */
    public static function specsFor( ?array $record = null )
    {
        if ( $record !== null && isset( $record['name'], $record['after']['rule'] ) && $record['name'] === 'system.audit.alert' )
        {
            $rule = (string)$record['after']['rule'];
            // only a rule that is configured: the name in the record is a key, never a recipient
            if ( in_array( $rule, expAuditConfig::lists( 'AuditAlertSettings', 'Rules' ), true ) )
            {
                $specs = expAuditConfig::lists( 'AlertRule_' . $rule, 'Recipients' );
                if ( $specs )
                    return array( 'specs' => $specs, 'from' => 'rule:' . $rule );
            }
        }
        return self::defaultSpecs();
    }

    /** @return array specs, from: the global default */
    public static function defaultSpecs()
    {
        $specs = expAuditConfig::lists( 'AuditAlertSettings', 'Recipients' );
        if ( $specs )
            return array( 'specs' => $specs, 'from' => 'AuditAlertSettings' );
        $specs = expAuditConfig::lists( 'AuditSink_mail', 'Receivers' );
        if ( $specs )
            return array( 'specs' => $specs, 'from' => 'AuditSink_mail' );
        return array( 'specs' => array( 'admin' ), 'from' => 'default' );
    }

    /**
     * Resolves specifications to addresses.
     *
     * @param string[] $specs
     * @return array addresses (lower-cased address => array( address, sources[] )), problems (spec => why)
     */
    public static function resolve( array $specs )
    {
        $out = array( 'addresses' => array(), 'problems' => array() );
        $seenGroups = array();
        foreach ( $specs as $spec )
            self::resolveOne( trim( (string)$spec ), $out, $seenGroups, (string)$spec );
        return $out;
    }

    /** @return string[] The addresses only, deduplicated, in first-seen order */
    public static function addresses( array $specs )
    {
        return array_values( array_map( function ( $a ) { return $a['address']; }, self::resolve( $specs )['addresses'] ) );
    }

    protected static function add( array &$out, $address, $source )
    {
        $address = trim( (string)$address );
        if ( !self::isAddress( $address ) )
        {
            $out['problems'][$source] = "'$address' is not a valid e-mail address";
            return;
        }
        $key = strtolower( $address );
        if ( !isset( $out['addresses'][$key] ) )
            $out['addresses'][$key] = array( 'address' => $address, 'sources' => array() );
        if ( !in_array( $source, $out['addresses'][$key]['sources'], true ) )
            $out['addresses'][$key]['sources'][] = $source;
    }

    /** @return bool A single plain address: no header injection, no list */
    public static function isAddress( $a )
    {
        if ( $a === '' || preg_match( '/[\r\n,;<>"\s]/', $a ) )
            return false;
        return filter_var( $a, FILTER_VALIDATE_EMAIL ) !== false;
    }

    protected static function resolveOne( $spec, array &$out, array &$seenGroups, $source )
    {
        if ( $spec === '' )
            return;
        if ( strpos( $spec, ':' ) === false )
        {
            if ( strtolower( $spec ) === 'admin' )
            {
                $admin = self::adminEmail();
                if ( $admin === '' )
                    $out['problems'][$source] = 'site.ini [MailSettings] AdminEmail is empty';
                else
                    self::add( $out, $admin, $source );
                return;
            }
            if ( strpos( $spec, '@' ) !== false )
            {
                self::add( $out, $spec, $source );
                return;
            }
            $out['problems'][$source] = "unknown recipient '$spec' (" . implode( ', ', self::KINDS ) . ')';
            return;
        }
        list( $kind, $value ) = explode( ':', $spec, 2 );
        $kind = strtolower( trim( $kind ) );
        $value = trim( $value );
        switch ( $kind )
        {
            case 'address':
                self::add( $out, $value, $source );
                return;
            case 'group':
                if ( !preg_match( '/^[A-Za-z0-9_-]+$/', $value ) )
                {
                    $out['problems'][$source] = "malformed group name '$value'";
                    return;
                }
                if ( isset( $seenGroups[$value] ) )
                    return;
                $seenGroups[$value] = true;
                if ( !expAuditConfig::hasBlock( 'AlertRecipients_' . $value ) )
                {
                    $out['problems'][$source] = "no [AlertRecipients_$value] block";
                    return;
                }
                foreach ( expAuditConfig::lists( 'AlertRecipients_' . $value, 'Addresses' ) as $a )
                    self::add( $out, $a, $source );
                foreach ( expAuditConfig::lists( 'AlertRecipients_' . $value, 'Recipients' ) as $s )
                    self::resolveOne( $s, $out, $seenGroups, $source );
                return;
            case 'user':
            case 'login':
                $user = null;
                if ( class_exists( 'eZUser' ) )
                    $user = $kind === 'user' ? ( ctype_digit( $value ) ? eZUser::fetch( (int)$value ) : null ) : eZUser::fetchByName( $value );
                if ( !$user )
                {
                    $out['problems'][$source] = "no user $kind $value";
                    return;
                }
                self::addUser( $out, $user, $source, true );
                return;
            case 'usergroup':
                $nodes = self::groupNodes( $value );
                if ( !$nodes )
                {
                    $out['problems'][$source] = "no user group with node id or remote id '$value'";
                    return;
                }
                foreach ( $nodes as $nodeID )
                    foreach ( self::usersUnder( $nodeID ) as $userID )
                        if ( $u = eZUser::fetch( $userID ) )
                            self::addUser( $out, $u, $source, false );
                return;
            case 'role':
                $role = null;
                if ( class_exists( 'eZRole' ) )
                    $role = ctype_digit( $value ) ? eZRole::fetch( (int)$value ) : eZRole::fetchByName( $value );
                if ( !$role )
                {
                    $out['problems'][$source] = "no role '$value'";
                    return;
                }
                foreach ( $role->fetchUserByRole() as $assignment )
                {
                    $object = $assignment['user_object'];
                    if ( !$object )
                        continue;
                    $id = (int)$object->attribute( 'id' );
                    $user = eZUser::fetch( $id );
                    if ( $user )
                    {
                        self::addUser( $out, $user, $source, false );
                        continue;
                    }
                    // a user group: every user below each of its locations
                    foreach ( (array)$object->attribute( 'assigned_nodes' ) as $node )
                        foreach ( self::usersUnder( (int)$node->attribute( 'node_id' ) ) as $userID )
                            if ( $u = eZUser::fetch( $userID ) )
                                self::addUser( $out, $u, $source, false );
                }
                return;
        }
        $out['problems'][$source] = "unknown recipient kind '$kind' (" . implode( ', ', self::KINDS ) . ')';
    }

    /** Adds an enabled user's current address. */
    protected static function addUser( array &$out, eZUser $user, $source, $report )
    {
        $id = (int)$user->attribute( 'contentobject_id' );
        if ( !self::isEnabled( $id ) )
        {
            if ( $report )
                $out['problems'][$source] = "user $id is disabled";
            return;
        }
        $email = trim( (string)$user->attribute( 'email' ) );
        if ( !self::isAddress( $email ) )
        {
            if ( $report )
                $out['problems'][$source] = "user $id has no valid e-mail address";
            return;
        }
        self::add( $out, $email, $source );
    }

    /** @return bool The account is enabled (ezuser_setting.is_enabled) */
    public static function isEnabled( $userID )
    {
        if ( !class_exists( 'eZUserSetting' ) )
            return true;
        $s = eZUserSetting::fetch( (int)$userID );
        return $s ? (bool)$s->attribute( 'is_enabled' ) : false;
    }

    /**
     * The node ids of a user group given as a node id or a remote id (of the node or of the object).
     *
     * @return int[]
     */
    protected static function groupNodes( $value )
    {
        if ( !class_exists( 'eZContentObjectTreeNode' ) )
            return array();
        if ( ctype_digit( $value ) )
        {
            $node = eZContentObjectTreeNode::fetch( (int)$value );
            return $node ? array( (int)$value ) : array();
        }
        $node = eZContentObjectTreeNode::fetchByRemoteID( $value );
        if ( $node )
            return array( (int)$node->attribute( 'node_id' ) );
        $object = eZContentObject::fetchByRemoteID( $value );
        if ( !$object )
            return array();
        $out = array();
        foreach ( (array)$object->attribute( 'assigned_nodes' ) as $n )
            $out[] = (int)$n->attribute( 'node_id' );
        return $out;
    }

    /**
     * The users (content object ids) anywhere below a node, whatever the current user may read.
     *
     * @param int $nodeID
     * @return int[]
     */
    public static function usersUnder( $nodeID )
    {
        $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'Limitation' => array(), 'IgnoreVisibility' => true, 'AsObject' => false ), $nodeID );
        $out = array();
        foreach ( (array)$nodes as $n )
        {
            // a row of AsObject=false names the object as id
            $id = (int)( is_array( $n ) ? ( isset( $n['contentobject_id'] ) ? $n['contentobject_id'] : $n['id'] ) : $n->attribute( 'contentobject_id' ) );
            if ( $id && !isset( $out[$id] ) && eZUser::fetch( $id ) )
                $out[$id] = $id;
        }
        return array_values( $out );
    }

    /** @return string The site's AdminEmail */
    public static function adminEmail()
    {
        $v = expAuditConfig::value( 'MailSettings', 'AdminEmail', null );
        if ( $v !== null )
            return trim( (string)$v ); // tests: 'MailSettings/AdminEmail' in the override
        try
        {
            if ( class_exists( 'eZINI' ) && !expAuditConfig::isOverridden() )
                return trim( (string)eZINI::instance()->variable( 'MailSettings', 'AdminEmail' ) );
        }
        catch ( Throwable $e )
        {
        }
        return '';
    }

    /**
     * Who would get mail, per rule and for the default (exp:audit alerts recipients, the console's settings view).
     *
     * @param string|null $only one rule
     * @return array name (rule name or "(default)") => array( specs, from, addresses, problems )
     */
    public static function overview( $only = null )
    {
        $out = array();
        if ( $only === null )
        {
            $d = self::defaultSpecs();
            $r = self::resolve( $d['specs'] );
            $out['(default)'] = $d + array( 'addresses' => array_values( $r['addresses'] ), 'problems' => $r['problems'] );
        }
        foreach ( expAuditConfig::lists( 'AuditAlertSettings', 'Rules' ) as $rule )
        {
            if ( $only !== null && $rule !== $only )
                continue;
            $sinks = expAuditConfig::lists( 'AlertRule_' . $rule, 'Sinks' );
            $s = self::specsFor( array( 'name' => 'system.audit.alert', 'after' => array( 'rule' => $rule ) ) );
            $r = self::resolve( $s['specs'] );
            $out[$rule] = $s + array( 'mailed' => in_array( 'mail', $sinks, true ), 'addresses' => array_values( $r['addresses'] ),
                                      'problems' => $r['problems'] );
        }
        return $out;
    }
}

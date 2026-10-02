<?php
/**
 * File containing the built-in request rule conditions.
 *
 * Every condition here reads facts of the ezpRequestContext and nothing else.
 * The table in doc/bc/6.0/view_full_security.md lists them with the facts
 * they read and examples.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Base for the conditions that compare one fact with the values: the fact
 * matches when it equals any value (case-insensitive), or, when the fact is a
 * list, when any item does. Subclasses name the fact, or override value().
 * Extensions can extend it too: a class with protected $fact = 'my_fact' is
 * a complete condition.
 */
class ezpRequestFactCondition implements ezpRequestRuleCondition
{
    /** @var string the fact compared */
    protected $fact = '';
    /** @var bool whether the values are shell patterns (fnmatch: *, ?, [a-z]) */
    protected $patterns = false;

    public function matches( ezpRequestContext $context, array $values, $argument )
    {
        $actual = $this->value( $context, $argument );
        if ( $actual === null )
            return false;
        foreach ( is_array( $actual ) ? $actual : array( $actual ) as $item )
        {
            if ( !is_scalar( $item ) )
                continue;
            foreach ( $values as $value )
            {
                if ( self::same( (string)$item, $value, $this->patterns ) )
                    return true;
            }
        }
        return false;
    }

    /**
     * @param ezpRequestContext $context
     * @param string|null $argument
     * @return mixed the fact, a list of them, or null when unknown
     */
    protected function value( ezpRequestContext $context, $argument )
    {
        return $context->get( $this->fact );
    }

    /**
     * @param string $actual
     * @param string $value
     * @param bool $pattern
     * @return bool
     */
    public static function same( $actual, $value, $pattern )
    {
        if ( $pattern )
            return fnmatch( strtolower( $value ), strtolower( $actual ), FNM_NOESCAPE );
        return strcasecmp( $actual, $value ) === 0;
    }
}

/** requested_via: how the visitor reached the page -- system, alias, wildcard or index. */
class ezpRequestConditionRequestedVia extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'requested_via'; }

/** uri: the address as typed (without siteaccess, leading slash or query), shell patterns: content/view/*, Fit-Healthy/* */
class ezpRequestConditionUri extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'typed_uri'; protected $patterns = true; }

/** resolved_uri: the system address the kernel runs, shell patterns: content/view/full/* */
class ezpRequestConditionResolvedUri extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'resolved_uri'; protected $patterns = true; }

/** module: the module that runs, after URL translation: content, user, shop */
class ezpRequestConditionModule extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'module'; }

/** module_view: module/view, shell patterns: content/view, content/*, user/login */
class ezpRequestConditionModuleView extends ezpRequestFactCondition implements ezpRequestRuleStableCondition
{
    protected $patterns = true;
    protected function value( ezpRequestContext $context, $argument )
    {
        return $context->get( 'module' ) . '/' . $context->get( 'view' );
    }
}

/** view_mode: the ViewMode parameter of content/view: full, line, embed */
class ezpRequestConditionViewMode extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'view_mode'; }

/** param:<Name>: a view parameter by its name in module.php: Conditions[param:NodeID]=2,43 */
class ezpRequestConditionParam extends ezpRequestFactCondition implements ezpRequestRuleStableCondition
{
    protected $patterns = true;
    protected function value( ezpRequestContext $context, $argument )
    {
        return $argument === null || $argument === '' ? null : $context->param( $argument );
    }
}

/** node: the node shown, by id: Conditions[node]=2,43 */
class ezpRequestConditionNode extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'node_id'; }

/** subtree: the node shown is one of these nodes or below them: Conditions[subtree]=864 */
class ezpRequestConditionSubtree extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'path_ids'; }

/** class: the content class identifier of the node shown: Conditions[class]=article,blog_post */
class ezpRequestConditionClass extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'class_identifier'; }

/** section: the section of the node shown, by id or identifier: Conditions[section]=standard,6 */
class ezpRequestConditionSection extends ezpRequestFactCondition implements ezpRequestRuleStableCondition
{
    protected function value( ezpRequestContext $context, $argument )
    {
        $id = $context->get( 'section_id' );
        if ( $id === null )
            return null;
        return array( (string)$id, (string)$context->get( 'section_identifier', '' ) );
    }
}

/** state: an object state of the node shown, group/state identifiers: Conditions[state]=ez_lock/locked */
class ezpRequestConditionState extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'state_identifiers'; protected $patterns = true; }

/** user: anonymous or logged_in */
class ezpRequestConditionUser implements ezpRequestRuleCondition, ezpRequestRuleStableCondition
{
    public function matches( ezpRequestContext $context, array $values, $argument )
    {
        $state = $context->get( 'is_anonymous', true ) ? 'anonymous' : 'logged_in';
        foreach ( $values as $value )
        {
            if ( strcasecmp( $value, $state ) === 0 )
                return true;
        }
        return false;
    }
}

/** user_id: the current user's content object id */
class ezpRequestConditionUserId extends ezpRequestFactCondition { protected $fact = 'user_id'; }

/** user_login: the current user's login, shell patterns */
class ezpRequestConditionUserLogin extends ezpRequestFactCondition { protected $fact = 'user_login'; protected $patterns = true; }

/** role: the name of a role assigned to the user, directly or through a group: Conditions[role]=Editor */
class ezpRequestConditionRole extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'role_names'; }

/** group: a user group (content object id) the user belongs to: Conditions[group]=12,13 */
class ezpRequestConditionGroup extends ezpRequestFactCondition { protected $fact = 'group_ids'; }

/**
 * policy: the user has access to any of these policy functions, module/function:
 * Conditions[policy]=!content/view_system_url. "Limited" access counts as access.
 */
class ezpRequestConditionPolicy implements ezpRequestRuleCondition, ezpRequestRuleStableCondition
{
    public function matches( ezpRequestContext $context, array $values, $argument )
    {
        foreach ( $values as $value )
        {
            $parts = explode( '/', $value, 2 );
            if ( count( $parts ) === 2 && $context->hasAccess( $parts[0], $parts[1] ) )
                return true;
        }
        return false;
    }
}

/** siteaccess: the current siteaccess name: Conditions[siteaccess]=site,bold */
class ezpRequestConditionSiteAccess extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'siteaccess'; protected $patterns = true; }

/** method: the HTTP method: GET, POST, HEAD */
class ezpRequestConditionMethod extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'method'; }

/** scheme: http or https */
class ezpRequestConditionScheme extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'scheme'; }

/** host: the host name the visitor used, shell patterns: *.example.com */
class ezpRequestConditionHost extends ezpRequestFactCondition implements ezpRequestRuleStableCondition { protected $fact = 'host'; protected $patterns = true; }

/**
 * header:<Name>: a request header, shell patterns; "*" means "sent at all":
 * Conditions[header:X-Requested-With]=XMLHttpRequest
 */
class ezpRequestConditionHeader extends ezpRequestFactCondition
{
    protected $patterns = true;
    protected function value( ezpRequestContext $context, $argument )
    {
        return $argument === null || $argument === '' ? null : $context->header( $argument );
    }
}

/** fact:<name>: any fact, including the ones an extension defines: Conditions[fact:customer_tier]=gold */
class ezpRequestConditionFact extends ezpRequestFactCondition
{
    protected $patterns = true;
    protected function value( ezpRequestContext $context, $argument )
    {
        return $argument === null || $argument === '' ? null : $context->get( $argument );
    }
}

/**
 * ip: the client address is one of these addresses or networks, IPv4 or
 * IPv6, CIDR: Conditions[ip]=10.0.0.0/8,192.168.1.20,2001:db8::/32
 */
class ezpRequestConditionIp implements ezpRequestRuleCondition
{
    public function matches( ezpRequestContext $context, array $values, $argument )
    {
        $ip = (string)$context->get( 'client_ip', '' );
        $address = @inet_pton( $ip );
        if ( $address === false )
            return false;
        foreach ( $values as $value )
        {
            if ( self::inNetwork( $address, $value ) )
                return true;
        }
        return false;
    }

    /**
     * @param string $address packed address (inet_pton)
     * @param string $network '10.0.0.0/8' or '192.168.1.20'
     * @return bool
     */
    public static function inNetwork( $address, $network )
    {
        $parts = explode( '/', $network, 2 );
        $base = @inet_pton( trim( $parts[0] ) );
        if ( $base === false || strlen( $base ) !== strlen( $address ) )
            return false;
        $bits = isset( $parts[1] ) ? (int)$parts[1] : strlen( $base ) * 8;
        if ( $bits < 0 || $bits > strlen( $base ) * 8 )
            return false;
        $bytes = intdiv( $bits, 8 );
        if ( substr( $address, 0, $bytes ) !== substr( $base, 0, $bytes ) )
            return false;
        $rest = $bits % 8;
        if ( $rest === 0 )
            return true;
        $mask = ( 0xff << ( 8 - $rest ) ) & 0xff;
        return ( ord( $address[$bytes] ) & $mask ) === ( ord( $base[$bytes] ) & $mask );
    }
}

?>

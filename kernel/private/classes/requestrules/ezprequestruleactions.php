<?php
/**
 * File containing the built-in request rule actions.
 *
 * An action decides; it never sends anything. It returns an
 * ezpRequestRuleResult and the kernel answers the request from it
 * (ezpRequestRuleKernel::moduleResult()), so actions are as testable as
 * conditions.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/** allow: the request runs as usual; no later rule is asked. For exceptions placed before a wider rule. */
class ezpRequestActionAllow implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        return ezpRequestRuleResult::allow();
    }
}

/** notfound: the "not found" page, HTTP 404. Says nothing about whether the page exists. */
class ezpRequestActionNotFound implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        return ezpRequestRuleResult::notFound();
    }
}

/** forbidden: the "access denied" page, HTTP 403 -- the same page a missing policy gives. */
class ezpRequestActionForbidden implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        return ezpRequestRuleResult::forbidden();
    }
}

/**
 * login: an anonymous visitor is sent to user/login and back to the page after signing in.
 * Never acts on the views a visitor needs to sign in (self::$signInViews), so a
 * rule that matches them too cannot send anyone round in a loop.
 */
class ezpRequestActionLogin implements ezpRequestRuleAction
{
    /** @var string[] module/view the login action leaves alone */
    public static $signInViews = array( 'user/login', 'user/logout', 'user/register', 'user/activate',
                                        'user/success', 'user/forgotpassword', 'user/password' );

    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        if ( in_array( $context->get( 'module' ) . '/' . $context->get( 'view' ), self::$signInViews, true ) )
            return null;
        return ezpRequestRuleResult::login();
    }
}

/**
 * redirect: HTTP redirect.
 *   ActionArgs[to]=/about/{node_id}   target; placeholders are facts ({node_id}, {param:NodeID}, {typed_uri})
 *   ActionArgs[status]=301            301, 302 (default), 303, 307 or 308
 * A system URI as the target (content/view/full/2) is sent as that node's URL alias.
 * Without a target it cannot act (the FallbackAction runs, or the next rule).
 */
class ezpRequestActionRedirect implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        $to = isset( $arguments['to'] ) ? trim( $context->expand( $arguments['to'] ) ) : '';
        if ( $to === '' )
            return null;
        return ezpRequestRuleResult::redirect( $to, isset( $arguments['status'] ) ? $arguments['status'] : 302 );
    }
}

/**
 * redirect_to_alias: HTTP redirect to the URL alias of the node shown, with the
 * view's user parameters ((offset)/10); the query string is kept by the kernel.
 *   ActionArgs[status]=301            default 301
 * Cannot act (FallbackAction, or the next rule) when the request is not about a
 * node, or the node has no URL alias of its own.
 */
class ezpRequestActionRedirectToAlias implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        $alias = trim( (string)$context->get( 'url_alias', '' ), '/' );
        if ( $alias === '' || preg_match( '#^content/view/#i', $alias ) )
            return null;
        if ( strcasecmp( $alias, trim( (string)$context->get( 'typed_uri', '' ), '/' ) ) === 0 )
            return null;
        $location = '/' . $alias . (string)$context->get( 'user_parameters_uri', '' );
        return ezpRequestRuleResult::redirect( $location, isset( $arguments['status'] ) ? $arguments['status'] : 301 );
    }
}

/**
 * rewrite: the kernel runs another URI instead, without a redirect; the visitor
 * keeps the address they typed. Rules are asked again for the new URI, at most
 * [RequestRuleSettings] MaxRewrites times per request.
 *   ActionArgs[to]=content/view/full/{node_id}
 */
class ezpRequestActionRewrite implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        $to = isset( $arguments['to'] ) ? trim( $context->expand( $arguments['to'] ) ) : '';
        return $to === '' ? null : ezpRequestRuleResult::rewrite( $to );
    }
}

/**
 * log: writes a line to var/log/requestrules.log and lets the next rule decide.
 * For auditing a rule before it is switched to a real action.
 *   ActionArgs[message]=system URL {typed_uri} by {user_login}
 */
class ezpRequestActionLog implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        $message = isset( $arguments['message'] ) ? $context->expand( $arguments['message'] )
                                                  : (string)$context->get( 'typed_uri', '' );
        $line = '[' . $rule->name . '] ' . (string)$context->get( 'method', '' ) . ' ' . $message
              . ' ip=' . (string)$context->get( 'client_ip', '' );
        if ( class_exists( 'eZLog' ) )
            eZLog::write( str_replace( array( "\r", "\n" ), ' ', $line ), 'requestrules.log' );
        return null;
    }
}

?>

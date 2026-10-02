<?php
/**
 * File containing the ezpRequestRuleKernel class.
 *
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Where the request rules meet the kernel: the facts of a real request
 * (context()) and the response for a decision (moduleResult()). ezpKernelWeb
 * calls decide() right before it runs a module view, after the URL alias was
 * translated and the user's policies allowed the view. Everything eZ-specific
 * about the rules is in this class; the engine, conditions and actions only
 * see the context.
 */
class ezpRequestRuleKernel
{
    /**
     * Asks the rules about the module view the kernel is about to run.
     *
     * @param eZModule $module the module about to run
     * @param string $view its view
     * @param array $params the URI elements after module/view, as fetchModule() gives them
     * @param array $route array( 'typed_uri' => what the visitor asked for, 'via' => system|alias|wildcard|index,
     *                     'user_parameters' => array( name => value ), 'rewrites' => int )
     * @return array|null the module result to use instead of running the view, or null to run it
     */
    public static function decide( eZModule $module, $view, array $params, array $route )
    {
        $engine = ezpRequestRuleEngine::instance();
        if ( !$engine->isActive() )
            return null;

        $context = self::context( $module->attribute( 'name' ), $view, $params, $route, eZUser::currentUser(), $module );
        $result = $engine->evaluate( $context );
        // Another request answered by the same stored page could be decided
        // otherwise: keep this one out of every shared cache
        if ( $context->get( 'rules_vary_by_request' ) )
            self::keepOutOfSharedCaches();
        if ( $result === null )
            return null;

        eZDebug::writeNotice( "Request rule '{$result->rule}': " . $result->describe() . ' for ' . $context->get( 'typed_uri' ), __METHOD__ );
        if ( $result->type === ezpRequestRuleResult::REWRITE
             && ( isset( $route['rewrites'] ) ? $route['rewrites'] : 0 ) >= $engine->maxRewrites() )
        {
            eZDebug::writeError( "Request rule '{$result->rule}' rewrote more than MaxRewrites=" . $engine->maxRewrites()
                               . ' times; answering not found instead', __METHOD__ );
            $result = ezpRequestRuleResult::notFound();
        }
        return self::moduleResult( $result, $module, $context );
    }

    /**
     * The context of a request: every built-in fact, most of them resolved
     * only when a rule reads them, plus the facts of the FactProviders.
     *
     * @param string $moduleName
     * @param string $view
     * @param array $params URI elements after module/view
     * @param array $route see decide()
     * @param eZUser $user the user the rules are about
     * @param eZModule|null $module to name the view parameters; looked up when null
     * @return ezpRequestContext
     */
    public static function context( $moduleName, $view, array $params, array $route, eZUser $user, ?eZModule $module = null )
    {
        if ( $module === null )
            $module = eZModule::exists( $moduleName );
        $named = array();
        $views = $module instanceof eZModule ? $module->attribute( 'views' ) : array();
        if ( isset( $views[$view]['params'] ) && is_array( $views[$view]['params'] ) )
        {
            foreach ( array_values( $views[$view]['params'] ) as $i => $name )
            {
                if ( isset( $params[$i] ) )
                    $named[$name] = $params[$i];
            }
        }
        $userParameters = isset( $route['user_parameters'] ) && is_array( $route['user_parameters'] ) ? $route['user_parameters'] : array();
        $userParametersUri = '';
        foreach ( $userParameters as $name => $value )
            $userParametersUri .= '/(' . $name . ')/' . $value;

        $resolvedUri = trim( $moduleName . '/' . $view . ( $params ? '/' . implode( '/', $params ) : '' ), '/' );
        $nodeId = $moduleName === 'content' && isset( $named['NodeID'] ) && ctype_digit( (string)$named['NodeID'] ) ? (int)$named['NodeID'] : null;
        $access = eZSiteAccess::current();

        $context = new ezpRequestContext( array(
            'typed_uri' => trim( isset( $route['typed_uri'] ) ? (string)$route['typed_uri'] : $resolvedUri, '/' ),
            'requested_via' => isset( $route['via'] ) ? $route['via'] : 'system',
            'resolved_uri' => $resolvedUri,
            'module' => $moduleName,
            'view' => $view,
            'params' => $named,
            'view_mode' => isset( $named['ViewMode'] ) ? $named['ViewMode'] : null,
            'node_id' => $nodeId,
            'user_parameters' => $userParameters,
            'user_parameters_uri' => $userParametersUri,
            'rewrites' => isset( $route['rewrites'] ) ? (int)$route['rewrites'] : 0,
            'siteaccess' => isset( $access['name'] ) ? $access['name'] : '',
            'method' => isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : 'GET',
            'scheme' => function () { return eZSys::isSSLNow() ? 'https' : 'http'; },
            'host' => function () { return preg_replace( '/:\d+$/', '', (string)eZSys::hostname() ); },
            'client_ip' => function () { return (string)eZSys::clientIP(); },
            'headers' => function () { return self::requestHeaders(); },

            'user' => $user,
            'user_id' => (int)$user->attribute( 'contentobject_id' ),
            'is_anonymous' => function () use ( $user ) { return $user->isAnonymous(); },
            'user_login' => function () use ( $user ) { return (string)$user->attribute( 'login' ); },
            'group_ids' => function () use ( $user ) { return array_map( 'intval', (array)$user->groups() ); },
            'role_names' => function () use ( $user )
            {
                $names = array();
                foreach ( (array)$user->roles() as $role )
                    $names[] = (string)$role->attribute( 'name' );
                return array_values( array_unique( $names ) );
            },
            'has_access' => function () use ( $user )
            {
                return function ( $module, $function ) use ( $user )
                {
                    $access = $user->hasAccessTo( $module, $function );
                    return isset( $access['accessWord'] ) && $access['accessWord'] !== 'no';
                };
            },

            'node' => function () use ( $nodeId ) { return $nodeId ? eZContentObjectTreeNode::fetch( $nodeId ) : null; },
            'url_alias' => function ( ezpRequestContext $c )
            {
                $node = $c->get( 'node' );
                return $node instanceof eZContentObjectTreeNode ? (string)$node->urlAlias() : null;
            },
            'path_ids' => function ( ezpRequestContext $c )
            {
                $node = $c->get( 'node' );
                return $node instanceof eZContentObjectTreeNode ? array_map( 'intval', $node->pathArray() ) : null;
            },
            'class_identifier' => function ( ezpRequestContext $c )
            {
                $node = $c->get( 'node' );
                return $node instanceof eZContentObjectTreeNode ? (string)$node->attribute( 'class_identifier' ) : null;
            },
            'section_id' => function ( ezpRequestContext $c )
            {
                $object = $c->get( 'object' );
                return $object instanceof eZContentObject ? (int)$object->attribute( 'section_id' ) : null;
            },
            'section_identifier' => function ( ezpRequestContext $c )
            {
                $id = $c->get( 'section_id' );
                $section = $id ? eZSection::fetch( $id ) : null;
                return $section instanceof eZSection ? (string)$section->attribute( 'identifier' ) : null;
            },
            'state_identifiers' => function ( ezpRequestContext $c )
            {
                $object = $c->get( 'object' );
                return $object instanceof eZContentObject ? array_values( (array)$object->stateIdentifierArray() ) : null;
            },
            'object' => function ( ezpRequestContext $c )
            {
                $node = $c->get( 'node' );
                return $node instanceof eZContentObjectTreeNode ? $node->object() : null;
            },
        ) );

        foreach ( ezpRequestRuleEngine::instance()->factProviders() as $class )
        {
            if ( class_exists( $class ) && in_array( 'ezpRequestFactProvider', class_implements( $class ) ) )
            {
                $provider = new $class();
                $provider->defineFacts( $context );
            }
            else
                eZDebug::writeError( "FactProviders: $class is not a class implementing ezpRequestFactProvider", __METHOD__ );
        }
        return $context;
    }

    /**
     * Turns a decision into what the kernel does: a module result to show, a
     * redirect (through the module's redirect status), or a rerun.
     *
     * Every answer but allow is sent with Cache-Control: private, no-store --
     * it depends on who asked, so no shared cache and no browser may keep it
     * (a remembered 301 would still send a visitor away after they signed in).
     *
     * @param ezpRequestRuleResult $result
     * @param eZModule $module
     * @param ezpRequestContext $context
     * @return array|null
     */
    public static function moduleResult( ezpRequestRuleResult $result, eZModule $module, ezpRequestContext $context )
    {
        if ( $result->type === ezpRequestRuleResult::ALLOW )
            return null;
        self::keepOutOfSharedCaches();
        $GLOBALS['ezpRequestRuleDecision'] = $result;

        switch ( $result->type )
        {
            case ezpRequestRuleResult::NOT_FOUND:
                return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

            case ezpRequestRuleResult::FORBIDDEN:
                return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

            case ezpRequestRuleResult::LOGIN:
                if ( !$context->get( 'is_anonymous', true ) )
                    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
                eZHTTPTool::instance()->setSessionVariable( 'RedirectAfterLogin', '/' . $context->get( 'typed_uri' ) . $context->get( 'user_parameters_uri', '' ) );
                $module->redirectTo( '/user/login' );
                $module->setRedirectStatus( ezpRequestRuleResult::$statusText[302] );
                return array( 'content' => '' );

            case ezpRequestRuleResult::REDIRECT:
                $module->redirectTo( $result->location );
                $module->setRedirectStatus( $result->statusLine() );
                return array( 'content' => '' );

            case ezpRequestRuleResult::REWRITE:
                return array( 'content' => '', 'rerun_uri' => $result->location, 'request_rule_rewrite' => true );

            case ezpRequestRuleResult::MODULE_RESULT:
                return $result->moduleResult;
        }
        return null;
    }

    /**
     * Sends Cache-Control: private, no-store (replacing the one the kernel
     * sent first), which Velocity's response cache and every proxy honour, and
     * sets $GLOBALS['ezpRequestRuleNoStore'], which the role-aware HTTP cache
     * (ezpHttpCacheListener) honours. The kernel resets the flag for each request.
     */
    public static function keepOutOfSharedCaches()
    {
        $GLOBALS['ezpRequestRuleNoStore'] = true;
        if ( !headers_sent() )
        {
            header( 'Cache-Control: private, no-store' );
            header_remove( 'Expires' );
            header( 'Pragma: no-cache' );
        }
    }

    /**
     * @return array lower-case header name => value
     */
    public static function requestHeaders()
    {
        $headers = array();
        foreach ( $_SERVER as $key => $value )
        {
            if ( strncmp( $key, 'HTTP_', 5 ) === 0 && is_scalar( $value ) )
                $headers[strtolower( str_replace( '_', '-', substr( $key, 5 ) ) )] = (string)$value;
        }
        foreach ( array( 'CONTENT_TYPE' => 'content-type', 'CONTENT_LENGTH' => 'content-length' ) as $key => $name )
        {
            if ( isset( $_SERVER[$key] ) && is_scalar( $_SERVER[$key] ) )
                $headers[$name] = (string)$_SERVER[$key];
        }
        return $headers;
    }
}

?>

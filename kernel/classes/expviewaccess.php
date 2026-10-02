<?php
/**
 * File containing the expViewAccess class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Whether a user can open an address of this siteaccess, decided the way the kernel decides a request
 * (ezpKernelWeb): the URL alias (and wildcard) translation, the module, its view, the siteaccess's
 * [SiteAccessRules], [SiteAccessSettings] RequireUserLogin with its AnonymousAccessList, the
 * [RoleSettings] PolicyOmitList, the user/login policy's SiteAccess limitation,
 * and the view's policy functions through eZUser::hasAccessToView() (a view without functions needs
 * access to the module). For the views that name a node or an object (content/view/<mode>/<node>,
 * content/edit/<object>, user/edit) the node's or object's own permission is asked as well, since those limitations
 * (Section, Subtree, Class, Owner ...) are only known once the node is. That last step is made for the
 * current user, and for another user only on the command line.
 *
 * Used by the admin dashboard, its menus and the top tabs to show a link only to a user who can follow it.
 * Templates: fetch( 'user', 'can_open', hash( 'uri', 'setup/cache' ) ). Guide: doc/bc/6.0/audit.md
 * ("Dashboard defect").
 */
class expViewAccess
{
    /**
     * @param string $uri an address of this siteaccess: "module/view/params", a URL alias, with or without
     *                    a leading slash, a query string or a fragment
     * @param eZUser|null $user the current user when null
     * @return bool
     */
    public static function canOpen( $uri, $user = null )
    {
        $check = self::check( $uri, $user );
        return $check['result'];
    }

    /**
     * Like canOpen(), with the reason: array( 'result' => bool, 'module' => string, 'view' => string,
     * 'reason' => string ). Reasons: open, empty, external, no-module, no-view, siteaccess-rules,
     * omitted (open: PolicyOmitList), login (the user may not use this siteaccess), policy, node, object.
     *
     * @param string $uri
     * @param eZUser|null $user
     * @param int $redirects internal: moved aliases followed so far
     * @return array
     */
    public static function check( $uri, $user = null, $redirects = 0 )
    {
        $result = array( 'result' => false, 'module' => '', 'view' => '', 'reason' => 'empty' );
        $uri = trim( (string)$uri );
        if ( preg_match( '#^[a-z][a-z0-9+.-]*:#i', $uri ) || strpos( $uri, '//' ) === 0 )
        {
            $result['reason'] = 'external';
            return $result;
        }
        $uri = preg_replace( '/[?#].*$/', '', $uri );
        $uri = trim( $uri, '/' );
        $ini = eZINI::instance();
        if ( $uri === '' )
            $uri = trim( $ini->variable( 'SiteSettings', 'IndexPage' ), '/' );
        if ( $uri === '' )
            return $result;

        if ( !$user instanceof eZUser )
            $user = eZUser::currentUser();
        if ( !$user instanceof eZUser )
            return $result;

        // a new object: translation changes it, and eZURI::instance() would hand out the request's own
        $uriObject = new eZURI( $uri );
        if ( $ini->variable( 'URLTranslator', 'Translation' ) === 'enabled' && eZURLAliasML::urlTranslationEnabledByUri( $uriObject ) )
        {
            $translated = eZURLAliasML::translate( $uriObject );
            if ( !is_string( $translated ) && $translated !== true && $ini->variable( 'URLTranslator', 'WildcardTranslation' ) === 'enabled' )
                $translated = eZURLWildcard::translate( $uriObject );
            // a moved alias or a redirecting wildcard: the kernel sends the visitor on to the new address
            if ( is_string( $translated ) && $translated !== '' )
            {
                if ( $redirects >= 3 )
                {
                    $result['reason'] = 'no-module';
                    return $result;
                }
                return self::check( $translated, $user, $redirects + 1 );
            }
        }

        $moduleName = $uriObject->element( 0 );
        $result['module'] = (string)$moduleName;
        $module = $moduleName ? eZModule::exists( $moduleName ) : null;
        if ( !$module instanceof eZModule )
        {
            $result['reason'] = 'no-module';
            return $result;
        }
        $viewName = $module->singleFunction() ? '' : (string)$uriObject->element( 1 );
        $result['view'] = $viewName;
        $params = $uriObject->elements( false );
        $params = array_slice( $params, $module->singleFunction() ? 1 : 2 );

        if ( !$module->singleFunction() )
        {
            $views = $module->attribute( 'views' );
            if ( !isset( $views[$viewName] ) && !isset( $module->Module['function']['script'] ) )
            {
                $result['reason'] = 'no-view';
                return $result;
            }
        }

        $siteAccessCheck = eZModule::accessAllowed( new eZURI( $moduleName . '/' . $viewName ) );
        if ( !$siteAccessCheck['result'] )
        {
            $result['reason'] = 'siteaccess-rules';
            return $result;
        }

        // [SiteAccessSettings] RequireUserLogin: an anonymous visitor gets the sign-in form for everything
        // but AnonymousAccessList (eZUser::checkUser())
        if ( $ini->variable( 'SiteAccessSettings', 'RequireUserLogin' ) === 'true' && $user->isAnonymous() )
        {
            // user/login is where the kernel sends the visitor, so it opens
            $listed = ( $moduleName === 'user' && $viewName === 'login' );
            foreach ( (array)$ini->variable( 'SiteAccessSettings', 'AnonymousAccessList' ) as $entry )
            {
                $e = explode( '/', $entry );
                if ( $e[0] === $moduleName && ( !isset( $e[1] ) || $e[1] === $viewName ) )
                {
                    $listed = true;
                    break;
                }
            }
            if ( !$listed )
            {
                $result['reason'] = 'login';
                return $result;
            }
        }

        $omit = (array)$ini->variable( 'RoleSettings', 'PolicyOmitList' );
        if ( in_array( $moduleName, $omit ) || in_array( $moduleName . '/' . $viewName, $omit ) )
        {
            $result['result'] = true;
            $result['reason'] = 'omitted';
            return $result;
        }

        if ( !self::mayUseSiteAccess( $user ) )
        {
            $result['reason'] = 'login';
            return $result;
        }

        $accessParams = array();
        if ( !$user->hasAccessToView( $module, $viewName, $accessParams ) )
        {
            $result['reason'] = 'policy';
            return $result;
        }

        if ( $moduleName === 'content' || ( $moduleName === 'user' && $viewName === 'edit' ) )
        {
            $reason = self::contentTarget( $moduleName, $viewName, $params, $user );
            if ( $reason !== true )
            {
                $result['reason'] = $reason;
                return $result;
            }
        }

        $result['result'] = true;
        $result['reason'] = 'open';
        return $result;
    }

    /**
     * The user/login policy as the kernel reads it: a SiteAccess limitation must name the current siteaccess.
     *
     * @param eZUser $user
     * @return bool
     */
    protected static function mayUseSiteAccess( eZUser $user )
    {
        $login = $user->hasAccessTo( 'user', 'login' );
        if ( $login['accessWord'] === 'yes' )
            return true;
        if ( $login['accessWord'] !== 'limited' )
            return false;
        $access = eZSiteAccess::current();
        $crc = eZSys::ezcrc32( isset( $access['name'] ) ? $access['name'] : '' );
        $checked = false;
        foreach ( $login['policies'] as $policy )
        {
            if ( isset( $policy['SiteAccess'] ) )
            {
                $checked = true;
                if ( in_array( $crc, $policy['SiteAccess'] ) )
                    return true;
            }
        }
        return !$checked;
    }

    /**
     * The node or object a content view names, asked for its own permission: content/view/<mode>/<node>
     * (read), content/edit/<object> (edit), user/edit[/<user id>] (edit of that user's object, the current
     * user's own without an id: the view checks it and edits through content/edit).
     *
     * @return true|string true, or the reason it cannot be opened
     */
    protected static function contentTarget( $moduleName, $viewName, array $params, eZUser $user )
    {
        $current = eZUser::currentUser();
        $switched = $current->attribute( 'contentobject_id' ) != $user->attribute( 'contentobject_id' );
        // eZContentObjectTreeNode::canRead() and eZContentObject::canEdit() ask the current user. Switching
        // it writes the session, so another user's node and object permissions are only asked on the
        // command line (tests, commands); on the web the step is left out for anyone but the current user.
        if ( $switched && !eZSys::isShellExecution() )
            return true;
        if ( $switched )
            eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        try
        {
            if ( $moduleName === 'user' )
            {
                $objectId = ( isset( $params[0] ) && ctype_digit( (string)$params[0] ) ) ? (int)$params[0] : (int)$user->attribute( 'contentobject_id' );
                $object = eZContentObject::fetch( $objectId );
                return ( $object instanceof eZContentObject && $object->canEdit() ) ? true : 'object';
            }
            if ( $viewName === 'view' && isset( $params[1] ) && ctype_digit( (string)$params[1] ) )
            {
                $node = eZContentObjectTreeNode::fetch( (int)$params[1] );
                return ( $node instanceof eZContentObjectTreeNode && $node->canRead() ) ? true : 'node';
            }
            if ( $viewName === 'edit' && isset( $params[0] ) && ctype_digit( (string)$params[0] ) )
            {
                $object = eZContentObject::fetch( (int)$params[0] );
                return ( $object instanceof eZContentObject && $object->canEdit() ) ? true : 'object';
            }
            return true;
        }
        finally
        {
            if ( $switched )
                eZUser::setCurrentlyLoggedInUser( $current, $current->attribute( 'contentobject_id' ), eZUser::NO_SESSION_REGENERATE );
        }
    }
}

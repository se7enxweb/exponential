<?php
/**
 * The code of kernel/content/hide.php, moved into a class (#207 stage 1). The file kernel/content/hide.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/hide.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Content
{

class Hide extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $NodeID = $Params['NodeID'];

        $curNode = \eZContentObjectTreeNode::fetch( $NodeID );
        if ( !$curNode )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( !$curNode->attribute( 'can_hide' ) )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' ) );

        // Content jobs (doc/bc/6.0/content-jobs.md): a large subtree (or the user's last choice) gets a
        // confirmation with the now-or-background choice; a small one is hidden or revealed at once, as before.
        if ( class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
        {
            try
            {
                $reveal = (bool) $curNode->attribute( 'is_hidden' );
                $jobResult = Job::interstitial( $Module, 'hide', $reveal ? 'reveal' : 'hide', array( 'node_id' => (int) $NodeID ),
                                                '/content/hide/' . (int) $NodeID, '/content/view/full/' . (int) $NodeID,
                                                \ezpI18n::tr( 'design/admin/content/job', $reveal ? 'Reveal %name' : 'Hide %name', null,
                                                              array( '%name' => $curNode->attribute( 'name' ) ) ),
                                                \ezpI18n::tr( 'design/admin/content/job', $reveal
                                                              ? 'The node and everything below it become visible again, unless hidden on their own.'
                                                              : 'The node and everything below it are hidden from the site.' ),
                                                array( 'RedirectURI' => self::confirmationReturnURI( $Module, (int) $NodeID ) ) );
                if ( $jobResult )
                    return $jobResult;
            }
            catch ( \Throwable $e )
            {
                \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            }
        }

        // Hiding and revealing change the site, so they take a POST (with the form token where ezformtoken is
        // active); a link (GET) shows the confirmation form instead of acting.
        if ( \eZSys::serverVariable( 'REQUEST_METHOD', true ) !== 'POST' )
        {
            $reveal = (bool) $curNode->attribute( 'is_hidden' );
            $tpl = \eZTemplate::factory();
            $tpl->setVariable( 'title', \ezpI18n::tr( 'design/admin/content/job', $reveal ? 'Reveal %name' : 'Hide %name', null,
                                                     array( '%name' => $curNode->attribute( 'name' ) ) ) );
            $tpl->setVariable( 'text', \ezpI18n::tr( 'design/admin/content/job', $reveal
                                                    ? 'The node and everything below it become visible again, unless hidden on their own.'
                                                    : 'The node and everything below it are hidden from the site.' ) );
            $tpl->setVariable( 'action_url', '/content/hide/' . (int) $NodeID );
            $tpl->setVariable( 'back_url', '/content/view/full/' . (int) $NodeID );
            $tpl->setVariable( 'hidden', array( 'RedirectURI' => self::confirmationReturnURI( $Module, (int) $NodeID ) ) );
            $tpl->setVariable( 'operation', 'hide' );
            $tpl->setVariable( 'job_mode', false );
            $tpl->setVariable( 'job_summary', null );
            $Result = array( 'content' => $tpl->fetch( 'design:content/job_confirm.tpl' ),
                             'path' => array( array( 'text' => $tpl->variable( 'title' ), 'url' => false ) ) );
            return $this->viewResult( $Result, null );
        }

        if ( \eZOperationHandler::operationIsAvailable( 'content_hide' ) )
        {
            $operationResult = \eZOperationHandler::execute( 'content',
                                                            'hide',
                                                             array( 'node_id' => $NodeID ),
                                                             null, true );
        }
        else
        {
            \eZContentOperationCollection::changeHideStatus( $NodeID );
        }


        // From the confirmation form: back to the page it was opened from, as the form names it. The page last
        // viewed in the session may be one of another tab by now.
        $postedReturn = self::postedReturnURI( (int) $NodeID );
        if ( $postedReturn !== false )
            return $this->viewResult( isset( $Result ) ? $Result : null, $Module->redirectTo( $postedReturn ) );

        $hasRedirect = \eZRedirectManager::redirectTo( $Module, false );
        if ( !$hasRedirect )
        {
            // redirect to the parent node
            if( ( $parentNodeID = $curNode->attribute( 'parent_node_id' ) ) == 1 )
                $redirectNodeID = $NodeID;
            else
                $redirectNodeID = $parentNodeID;
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'view', array( 'full', $redirectNodeID ) ) );
        }

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /** The views a return address must not lead to: the usual ones and content/hide itself */
    protected static function disallowedReturnViews()
    {
        return array_merge( \eZRedirectManager::disallowedReturnViews(), array( 'content/hide' ) );
    }

    /**
     * The page the confirmation form returns to after OK, fixed when the form is shown: the page the user came
     * from (the last viewed one, which is this tab's while the form is being opened), else the node's view.
     *
     * @param \eZModule|null $module
     * @param int $nodeID
     * @return string
     */
    public static function confirmationReturnURI( $module, $nodeID )
    {
        return (string) \eZRedirectManager::returnURI( $module, '/content/view/full/' . (int) $nodeID, false,
                                                       array( 'disallowed_views' => self::disallowedReturnViews() ) );
    }

    /**
     * Where a POST from the confirmation form goes: its RedirectURI when that is a local address of a page to
     * return to (eZRedirectManager::returnURI(), without the session), else the node's view. False when the POST
     * names no RedirectURI (the popup menu and the Locations tab post directly).
     *
     * @param int $nodeID
     * @return string|false
     */
    public static function postedReturnURI( $nodeID )
    {
        if ( !\eZHTTPTool::instance()->hasPostVariable( 'RedirectURI' ) )
            return false;
        return (string) \eZRedirectManager::returnURI( null, '/content/view/full/' . (int) $nodeID,
                                                       \eZRedirectManager::formReturnURIs( array( 'RedirectURI' ) ),
                                                       array( 'session' => false, 'disallowed_views' => self::disallowedReturnViews() ) );
    }
}

}

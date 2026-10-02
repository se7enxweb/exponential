<?php
/**
 * The code of kernel/content/removeobject.php, moved into a class (#207 stage 1). The file kernel/content/removeobject.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/content/removeobject.php:
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

class Removeobject extends \Exponential\Runnable\ModuleView
{

    /**
     * Calls the content jobs GUI helper (Exponential\View\Kernel\Content\Job) when it can be used. Without it
     * (a Velocity worker started before it existed, the engine not installed or failing) the view takes the
     * old synchronous path: runAsJob gives false, everything else null. doc/bc/6.0/content-jobs.md
     *
     * @param string $method runAsJob, lockRefusal or startJob
     * @return mixed
     */
    protected static function contentJob( $method, ...$args )
    {
        $default = $method === 'runAsJob' ? false : null;
        if ( !class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
            return $default;
        try
        {
            return call_user_func_array( array( 'Exponential\\View\\Kernel\\Content\\Job', $method ), $args );
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            return $default;
        }
    }
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        $http = \eZHTTPTool::instance();

        $viewMode = $http->sessionVariable( "CurrentViewMode" );
        $deleteIDArray = $http->sessionVariable( "DeleteIDArray" );
        $contentNodeID = $http->sessionVariable( 'ContentNodeID' );

        $requestedURI = '';
        $userRedirectURI = '';
        $requestedURI = $GLOBALS['eZRequestedURI'];
        if ( $requestedURI instanceof \eZURI )
        {
            $userRedirectURI = $requestedURI->uriString( true );
        }
        $http->setSessionVariable( 'userRedirectURIReverseRelatedList', $userRedirectURI );

        if ( $http->hasSessionVariable( 'ContentLanguage' ) )
        {
            $contentLanguage = $http->sessionVariable( 'ContentLanguage' );
        }
        else
        {
            $contentLanguage = false;
        }
        if ( is_array( $deleteIDArray ) && count( $deleteIDArray ) <= 0 )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'view', array( $viewMode, $contentNodeID, $contentLanguage ) ) );

        // Cleanup and redirect back when cancel is clicked
        if ( $http->hasPostVariable( "CancelButton" ) )
        {
            $http->removeSessionVariable( "CurrentViewMode" );
            $http->removeSessionVariable( "DeleteIDArray" );
            $http->removeSessionVariable( 'ContentNodeID' );
            $http->removeSessionVariable( 'userRedirectURIReverseRelatedList' );
            $http->removeSessionVariable( 'HideRemoveConfirmation' );
            $http->removeSessionVariable( 'RedirectURIAfterRemove' );

            if ( $http->hasSessionVariable( 'RedirectIfCancel' )
              && $http->sessionVariable( 'RedirectIfCancel' ) )
            {
                $Module->redirectTo( $http->sessionVariable( 'RedirectIfCancel' ) );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $http->removeSessionVariable( 'RedirectIfCancel' ) );
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'view', array( $viewMode, $contentNodeID, $contentLanguage ) ) );
            }
        }

        $contentINI = \eZINI::instance( 'content.ini' );

        $RemoveAction = $contentINI->hasVariable( 'RemoveSettings', 'DefaultRemoveAction' ) ?
                           $contentINI->variable( 'RemoveSettings', 'DefaultRemoveAction' ) : 'trash';
        if ( $RemoveAction != 'trash' and $RemoveAction != 'delete' )
            $RemoveAction = 'trash';

        $moveToTrash = ( $RemoveAction == 'trash' ) ? true : false;
        if ( $http->hasPostVariable( 'SupportsMoveToTrash' ) )
        {
            if ( $http->hasPostVariable( 'MoveToTrash' ) )
                $moveToTrash = $http->postVariable( 'MoveToTrash' ) ? true : false;
            else
                $moveToTrash = false;
        }

        $hideRemoveConfirm = $contentINI->hasVariable( 'RemoveSettings', 'HideRemoveConfirmation' ) ?
                             (( $contentINI->variable( 'RemoveSettings', 'HideRemoveConfirmation' ) == 'true' ) ? true : false ) : false;
        if ( $http->hasSessionVariable( 'HideRemoveConfirmation' ) )
            $hideRemoveConfirm = $http->sessionVariable( 'HideRemoveConfirmation' );

        // Content jobs (doc/bc/6.0/content-jobs.md): a removal inside a subtree a background job is working on
        // is refused; a large removal (content.ini [ContentJobSettings] SynchronousLimit, or more than
        // MaxNodesRemoveSubtree) runs as a job in the background. Small removals are done here as before.
        $jobBackURL = '/content/view/full/' . (int) $contentNodeID;
        $jobRefused = is_array( $deleteIDArray ) ? self::contentJob( 'lockRefusal', $deleteIDArray, $jobBackURL ) : null;
        if ( $jobRefused )
            return $jobRefused;
        $jobParams = array( 'node_ids' => is_array( $deleteIDArray ) ? array_values( array_map( 'intval', $deleteIDArray ) ) : array(),
                            'move_to_trash' => $moveToTrash );
        $jobOldLimit = $contentINI->hasVariable( 'RemoveSettings', 'MaxNodesRemoveSubtree' ) ?
                       $contentINI->variable( 'RemoveSettings', 'MaxNodesRemoveSubtree' ) : 100;
        $runAsJob = $jobParams['node_ids'] && self::contentJob( 'chosenAsJob', 'remove', 'remove', $jobParams, $jobOldLimit );

        if ( $runAsJob and ( $http->hasPostVariable( "ConfirmButton" ) or $hideRemoveConfirm ) )
            return self::contentJob( 'startJob', $Module, 'remove', $jobParams, $jobBackURL,
                                  in_array( (int) $contentNodeID, $jobParams['node_ids'] ) ? 0 : (int) $contentNodeID );

        if ( $http->hasPostVariable( "ConfirmButton" ) or
             $hideRemoveConfirm )
        {
            if ( \eZOperationHandler::operationIsAvailable( 'content_delete' ) )
            {
                $operationResult = \eZOperationHandler::execute( 'content',
                                                                'delete',
                                                                 array( 'node_id_list' => $deleteIDArray,
                                                                        'move_to_trash' => $moveToTrash ),
                                                                  null, true );
            }
            else
            {
                \eZContentOperationCollection::deleteObject( $deleteIDArray, $moveToTrash );
            }

            if ( $http->hasSessionVariable( 'RedirectURIAfterRemove' )
              && $http->sessionVariable( 'RedirectURIAfterRemove' ) )
            {
                $Module->redirectTo( $http->sessionVariable( 'RedirectURIAfterRemove' ) );
                $http->removeSessionVariable( 'RedirectURIAfterRemove' );
                return $this->viewResult( isset( $Result ) ? $Result : null,  $http->removeSessionVariable( 'RedirectIfCancel' ) );
            }
            else
            {
                return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'view', array( $viewMode, $contentNodeID, $contentLanguage ) ) );
            }
        }

        $showCheck = $contentINI->hasVariable( 'RemoveSettings', 'ShowRemoveToTrashCheck' ) ?
                     (( $contentINI->variable( 'RemoveSettings', 'ShowRemoveToTrashCheck' ) == 'false' ) ? false : true ) : true;

        $info               = \eZContentObjectTreeNode::subtreeRemovalInformation( $deleteIDArray );
        $deleteResult       = $info['delete_list'];
        $moveToTrashAllowed = $info['move_to_trash'];
        $totalChildCount    = $info['total_child_count'];
        $hasPendingObject   = $info['has_pending_object'];
        $exceededLimit      = false;
        $deleteNodeIdArray  = array();

        // Check if number of nodes being removed not more then MaxNodesRemoveSubtree setting.
        $maxNodesRemoveSubtree = $contentINI->hasVariable( 'RemoveSettings', 'MaxNodesRemoveSubtree' ) ?
                                    $contentINI->variable( 'RemoveSettings', 'MaxNodesRemoveSubtree' ) : 100;

        $deleteItemsExist = true; // If false, we should disable 'OK' button if count of each deletion items more then MaxNodesRemoveSubtree setting.

        foreach ( array_keys( $deleteResult ) as $removeItemKey )
        {
            $removeItem =& $deleteResult[$removeItemKey];
            $deleteNodeIdArray[$removeItem['node']->attribute( 'node_id' )] = 1;
            if ( !$runAsJob && $removeItem['child_count'] > $maxNodesRemoveSubtree )
            {
                $removeItem['exceeded_limit_of_subitems'] = true;
                $exceededLimit = true;
                $nodeObj = $removeItem['node'];
                if ( !$nodeObj )
                    continue;

                $nodeID = $nodeObj->attribute( 'node_id' );
                $deleteIDArrayNew = array();
                foreach ( $deleteIDArray as $deleteID )
                {
                    if ( $deleteID != $nodeID )
                        $deleteIDArrayNew[] = $deleteID;
                }
                $deleteItemsExist = count( $deleteIDArrayNew ) != 0;
                $http->setSessionVariable( "DeleteIDArray", $deleteIDArrayNew );
            }
        }

        // We check if we can remove the nodes without confirmation
        // to do this the following must be true:
        // - The total child count must be zero
        // - There must be no object removal (i.e. it is the only node for the object)
        if ( $totalChildCount == 0 && !$runAsJob )
        {
            $canRemove = true;
            foreach ( $deleteResult as $item )
            {
                if ( $item['object_node_count'] <= 1 )
                {
                    $canRemove = false;
                    break;
                }
            }
            if ( $canRemove )
            {
                if ( \eZOperationHandler::operationIsAvailable( 'content_removelocation' ) )
                {
                    $operationResult = \eZOperationHandler::execute( 'content',
                                                                    'removelocation',
                                                                     array( 'node_list' => array_keys( $deleteNodeIdArray ),
                                                                            'move_to_trash' => $moveToTrash ),
                                                                      null, true );
                }
                else
                {
                    \eZContentOperationCollection::removeNodes( array_keys( $deleteNodeIdArray ) );
                }

                if ( $http->hasSessionVariable( 'RedirectURIAfterRemove' )
                  && $http->sessionVariable( 'RedirectURIAfterRemove' ) )
                {
                    $Module->redirectTo( $http->sessionVariable( 'RedirectURIAfterRemove' ) );
                    $http->removeSessionVariable( 'RedirectURIAfterRemove' );
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $http->removeSessionVariable( 'RedirectIfCancel' ) );
                }
                else
                {
                    return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->redirectToView( 'view', array( $viewMode, $contentNodeID, $contentLanguage ) ) );
                }
            }
        }

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( 'reverse_related'        , $info['reverse_related_count'] );
        $tpl->setVariable( 'module'                 , $Module );
        $tpl->setVariable( 'moveToTrashAllowed'     , $moveToTrashAllowed ); // Backwards compatibility
        $tpl->setVariable( 'ChildObjectsCount'      , $totalChildCount ); // Backwards compatibility
        $tpl->setVariable( 'DeleteResult'           , $deleteResult ); // Backwards compatibility
        $tpl->setVariable( 'move_to_trash_allowed'  , ( $moveToTrashAllowed and $showCheck ) );
        $tpl->setVariable( 'remove_list'            , $deleteResult );
        $tpl->setVariable( 'total_child_count'      , $totalChildCount );
        $tpl->setVariable( 'remove_info'            , $info );
        $tpl->setVariable( 'exceeded_limit'         , $exceededLimit );
        $tpl->setVariable( 'delete_items_exist'     , $deleteItemsExist );
        $tpl->setVariable( 'move_to_trash'          , $moveToTrash );
        $tpl->setVariable( 'has_pending_object'     , $hasPendingObject );
        $tpl->setVariable( 'run_as_job'             , $runAsJob );
        // content jobs: the now-or-background choice and what the removal touches (null without the engine)
        $tpl->setVariable( 'job_mode'               , $jobParams['node_ids'] ? self::contentJob( 'modeChoice', 'remove', 'remove', $jobParams, $jobOldLimit, '[RemoveSettings] MaxNodesRemoveSubtree' ) : null );
        $tpl->setVariable( 'job_summary'            , $jobParams['node_ids'] && class_exists( 'Exponential\\Service\\ContentJobDetails' ) ? \Exponential\Service\ContentJobDetails::subtreeSummary( $jobParams['node_ids'] ) : null );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:node/removeobject.tpl" );
        $Result['path'] = array( array( 'url' => false,
                                        'text' => \ezpI18n::tr( 'kernel/content', 'Remove object' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}

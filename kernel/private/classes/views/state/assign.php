<?php
/**
 * The code of kernel/state/assign.php, moved into a class (#207 stage 1). The file kernel/state/assign.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/state/assign.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\State
{

class Assign extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $Result = array();
        $Result['content'] = '';

        // Identify whether the input data was submitted through URL parameters or through POST
        if ( $Module->isCurrentAction( 'Assign' )                 and
             $Module->hasActionParameter( 'SelectedStateIDList' ) and
             $Module->hasActionParameter( 'ObjectID' ) )
        {
            $selectedStateIDList = $Module->actionParameter( 'SelectedStateIDList' );
            $objectID = $Module->actionParameter( 'ObjectID' );
        }
        else
        {
            $objectID = isset( $Params['ObjectID'] ) ? $Params['ObjectID'] : false;
            $selectedStateIDList = isset( $Params['SelectedStateID'] ) ? array( $Params['SelectedStateID'] ) : false ;
        }

        // Content jobs (doc/bc/6.0/content-jobs.md): "also for everything below this node" sets the states on the
        // whole subtree, now or as a background job; without it the object's states are set as before
        $http = \eZHTTPTool::instance();
        if ( $objectID and $selectedStateIDList and $Module->isCurrentAction( 'Assign' )
             and $http->hasPostVariable( 'StateApplyToSubtree' ) and $http->hasPostVariable( 'NodeID' ) )
        {
            $subtreeResult = $this->assignToSubtree( $Module, (int) $objectID, (int) $http->postVariable( 'NodeID' ),
                                                     array_values( array_map( 'intval', (array) $selectedStateIDList ) ) );
            if ( $subtreeResult )
                return $this->viewResult( null, $subtreeResult );
        }

        // Change object's state
        if ( $objectID and $selectedStateIDList )
        {
            if ( \eZOperationHandler::operationIsAvailable( 'content_updateobjectstate' ) )
            {
                $operationResult = \eZOperationHandler::execute( 'content', 'updateobjectstate',
                                                                array( 'object_id'     => $objectID,
                                                                       'state_id_list' => $selectedStateIDList ) );
            }
            else
            {
                \eZContentOperationCollection::updateObjectState( $objectID, $selectedStateIDList );
            }

            // Redirect to the provided URI, or to the root if not provided.
            // @TODO : in case this view is called through Ajax, make sure the module ends another way.
            $Module->hasActionParameter( 'RedirectRelativeURI' ) ? $Module->redirectTo( $Module->actionParameter( 'RedirectRelativeURI' ) ) : $Module->redirectTo( '/' );
        }
        elseif ( $objectID )
        {
            // Propose an interface. The end-user probably accessed this view through a simple URL like
            // '/state/assign/<object_id>'
            if ( ( $object = \eZContentObject::fetch( $objectID ) ) !== null  )
            {
                $tpl = \eZTemplate::factory();
                $tpl->setVariable( 'node', $object->attribute( 'main_node' ) );
                $Result['content'] = $tpl->fetch( 'design:state/assign.tpl' );
            }
        }
        else
            $Module->hasActionParameter( 'RedirectRelativeURI' ) ? $Module->redirectTo( $Module->actionParameter( 'RedirectRelativeURI' ) ) : $Module->redirectTo( '/' );

        $Result['path'] = array(
                            array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ),
                            array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'Assign' ) )
                           );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The states for a whole subtree. A small subtree (and the user's choice "now") is done in this request, per
     * object through the kernel's operation, which leaves out states the user may not assign to an object; a
     * large one (or the choice "background") becomes a content job of the type 'state' after a confirmation with
     * what it touches. A background job sets one state, so it needs one changed state group.
     *
     * Without the content jobs GUI (a Velocity worker started before it existed, the engine missing or failing)
     * the subtree is done now, as far as the page allows.
     *
     * @param \eZModule $module
     * @param int $objectID the object of the form
     * @param int $nodeID the node whose subtree gets the states
     * @param int[] $stateIDs the selected states (one per group)
     * @return array|null the view's result, or null to go on with the old code (one object)
     */
    protected function assignToSubtree( $module, $objectID, $nodeID, array $stateIDs )
    {
        $node = \eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node instanceof \eZContentObjectTreeNode || (int) $node->attribute( 'contentobject_id' ) !== $objectID )
            return null;
        $http = \eZHTTPTool::instance();
        $redirect = $http->hasPostVariable( 'RedirectRelativeURI' ) ? (string) $http->postVariable( 'RedirectRelativeURI' ) : '/content/view/full/' . $nodeID;
        $backURL = '/content/view/full/' . $nodeID;
        $object = $node->object();
        $tr = function ( $text, $args = array() ) { return \ezpI18n::tr( 'design/admin/content/job', $text, null, $args ); };

        // the user must be allowed to set the chosen states on the top object, as the job checks when it is created
        $allowed = array_map( 'intval', (array) $object->attribute( 'allowed_assign_state_id_list' ) );
        foreach ( $stateIDs as $stateID )
        {
            if ( !in_array( $stateID, $allowed, true ) )
            {
                $state = \eZContentObjectState::fetchById( $stateID );
                return self::refused( $tr( 'You may not set the state "%state" on %name, so it is not set on the subtree either.',
                                           array( '%state' => $state ? $state->attribute( 'identifier' ) : $stateID,
                                                  '%name' => $node->attribute( 'name' ) ) ), $backURL );
            }
        }

        // the state a job would set: the only group, or the one group whose state was changed on the form
        $changed = array_values( array_diff( $stateIDs, array_map( 'intval', (array) $object->attribute( 'state_id_array' ) ) ) );
        $jobStateID = count( $stateIDs ) === 1 ? $stateIDs[0] : ( count( $changed ) === 1 ? $changed[0] : 0 );

        if ( $jobStateID && class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
        {
            try
            {
                $jobState = \eZContentObjectState::fetchById( $jobStateID );
                $jobStateName = $jobStateID;
                if ( $jobState )
                    $jobStateName = $jobState->attribute( 'current_translation' )
                                    ? $jobState->attribute( 'current_translation' )->attribute( 'name' ) : $jobState->attribute( 'identifier' );
                $hidden = array( 'ObjectID' => $objectID, 'NodeID' => $nodeID, 'SelectedStateIDList[]' => $stateIDs,
                                 'StateApplyToSubtree' => 1, 'AssignButton' => 1, 'RedirectRelativeURI' => $redirect );
                $jobResult = \Exponential\View\Kernel\Content\Job::interstitial( $module, 'state', 'state',
                    array( 'node_id' => $nodeID, 'state_id' => $jobStateID ), '/state/assign', $backURL,
                    $tr( 'Set the state %state on %name and everything below it',
                         array( '%state' => $jobStateName,
                                '%name' => $node->attribute( 'name' ) ) ),
                    $tr( 'Every object in the subtree gets the state; objects you may not give it are skipped.' ),
                    $hidden, $nodeID );
                if ( $jobResult )
                    return $jobResult;
            }
            catch ( \Throwable $e )
            {
                \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            }
        }
        else if ( !$jobStateID )
        {
            // several groups changed at once: only now, and only as far as the request allows
            $ini = \eZINI::instance( 'content.ini' );
            $nowLimit = $ini->hasVariable( 'ContentJobSettings', 'NowLimit' ) ? (int) $ini->variable( 'ContentJobSettings', 'NowLimit' ) : 0;
            if ( ( $http->hasPostVariable( 'ContentJobMode' ) && $http->postVariable( 'ContentJobMode' ) === 'job' )
                 || ( $nowLimit > 0 && (int) $node->subTreeCount( array( 'Limitation' => array() ) ) + 1 > $nowLimit ) )
                return self::refused( $tr( 'A background job sets one state at a time: change one state group, or run it now.' ), $backURL );
        }

        // a subtree a background job is working on is refused, also now
        if ( class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
        {
            try
            {
                $lockResult = \Exponential\View\Kernel\Content\Job::lockRefusal( array( $nodeID ), $backURL );
                if ( $lockResult )
                    return $lockResult;
            }
            catch ( \Throwable $e )
            {
                \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            }
        }

        // now: every object of the subtree, through the kernel's operation (it leaves out what is there already
        // and what the user may not assign)
        $this->assignStatesNow( $node, $stateIDs );
        $module->redirectTo( $redirect );
        return array( 'content' => '', 'path' => array() );
    }

    /**
     * Sets the states on every object of the node's subtree, the node's own object included, in this request.
     *
     * @param \eZContentObjectTreeNode $node
     * @param int[] $stateIDs
     * @return int the number of objects handed to the operation
     */
    protected function assignStatesNow( \eZContentObjectTreeNode $node, array $stateIDs )
    {
        $db = \eZDB::instance();
        $rows = $db->arrayQuery( "SELECT DISTINCT contentobject_id FROM ezcontentobject_tree WHERE path_string LIKE '"
                                 . $db->escapeString( $node->attribute( 'path_string' ) ) . "%' ORDER BY contentobject_id" );
        $useOperation = \eZOperationHandler::operationIsAvailable( 'content_updateobjectstate' );
        $count = 0;
        foreach ( $rows as $row )
        {
            $id = (int) $row['contentobject_id'];
            if ( $useOperation )
                \eZOperationHandler::execute( 'content', 'updateobjectstate', array( 'object_id' => $id, 'state_id_list' => $stateIDs ) );
            else
                \eZContentOperationCollection::updateObjectState( $id, $stateIDs );
            \eZContentObject::clearCache( array( $id ) );
            $count++;
        }
        return $count;
    }

    /**
     * The refusal page of the content jobs (or a plain one without them).
     *
     * @param string $message
     * @param string $backURL
     * @return array
     */
    protected static function refused( $message, $backURL )
    {
        if ( class_exists( 'Exponential\\View\\Kernel\\Content\\Job' ) )
        {
            try
            {
                return \Exponential\View\Kernel\Content\Job::refusedResult( $message, null, $backURL );
            }
            catch ( \Throwable $e )
            {
                \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            }
        }
        return array( 'content' => '<div class="message-warning"><h2>' . htmlspecialchars( $message ) . '</h2></div>',
                      'path' => array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/state', 'State' ) ) ) );
    }
}

}

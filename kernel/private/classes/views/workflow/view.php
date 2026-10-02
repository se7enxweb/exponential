<?php
/**
 * The code of kernel/workflow/view.php, moved into a class (#207 stage 1). The file kernel/workflow/view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/workflow/view.php:
 *
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Workflow
{

class View extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $validation = array( 'processed' => false,
                             'groups' => array() );

        $WorkflowID = $Params["WorkflowID"];
        $WorkflowID = (int) $WorkflowID;
        if ( !is_int( $WorkflowID ) )
            $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );

        $workflow = \eZWorkflow::fetch( $WorkflowID );
        if ( !$workflow )
            return $this->viewResult( isset( $Result ) ? $Result : null,  $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );

        if ( $http->hasPostVariable( "AddGroupButton" ) && $http->hasPostVariable( "Workflow_group") )
        {
            $selectedGroup = $http->postVariable( "Workflow_group" );
            \eZWorkflowFunctions::addGroup( $WorkflowID, 0, $selectedGroup );
        }
        if ( $http->hasPostVariable( "DeleteGroupButton" ) && $http->hasPostVariable( "group_id_checked" ) )
        {
            $selectedGroup = $http->postVariable( "group_id_checked" );
            if ( !\eZWorkflowFunctions::removeGroup( $WorkflowID, 0, $selectedGroup ) )
            {
                $validation['groups'][] = array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'You have to have at least one group that the workflow belongs to!' ) );
                $validation['processed'] = true;
            }
        }

        $event_list = $workflow->fetchEvents();

        $tpl = \eZTemplate::factory();
        $res = \eZTemplateDesignResource::instance();
        $res->setKeys( array( array( "workflow", $workflow->attribute( "id" ) ) ) );

        $tpl->setVariable( "workflow", $workflow );
        $tpl->setVariable( "event_list", $event_list );
        $tpl->setVariable( 'validation', $validation );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:workflow/view.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Workflow' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'View' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}

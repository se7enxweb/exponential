<?php
/**
 * The code of kernel/workflow/run.php, moved into a class (#207 stage 1). The file kernel/workflow/run.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/workflow/run.php:
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

class Run extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $Module = $Params['Module'];

        $WorkflowProcessID = null;
        if ( !isset( $Params["WorkflowProcessID"] ) )
        {
            $Module->redirectTo( $Module->functionURI( "list" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $WorkflowProcessID = $Params["WorkflowProcessID"];

        $process = \eZWorkflowProcess::fetch( $WorkflowProcessID );
        if ( $process === null )
        {
            $Module->redirectTo( $Module->functionURI( "list" ) );
            return $this->viewResult( isset( $Result ) ? $Result : null, null );
        }

        $http = \eZHTTPTool::instance();

        // $execStack = eZExecutionStack::instance();
        // $execStack->addEntry( $Module->functionURI( "run" ) . "/" . $WorkflowProcessID,
        //                       $Module->attribute( "name" ), "run" );

        // Template handling

        $tpl = \eZTemplate::factory();

        $workflow = \eZWorkflow::fetch( $process->attribute( "workflow_id" ) );

        $workflowEvent = null;
        if ( $process->attribute( "event_id" ) != 0 )
            $workflowEvent = \eZWorkflowEvent::fetch( $process->attribute( "event_id" ) );

        $process->run( $workflow, $workflowEvent, $eventLog );
        // Store changes to process
        if ( $process->attribute( 'status' ) != \eZWorkflow::STATUS_DONE )
        {
            $process->store();
        }
        if ( $process->attribute( 'status' ) == \eZWorkflow::STATUS_DONE )
        {
        //    list ( $module, $function, $parameters ) = $process->getModuleInfo();
        }
        $tpl->setVariable( "event_log", $eventLog );
        $tpl->setVariable( "current_workflow", $workflow );

        $Module->setTitle( "Workflow run" );

        $tpl->setVariable( "process", $process );
        $tpl->setVariable( "module", $Module );
        $tpl->setVariable( "http", $http );

        $Result = array();
        $Result['content'] = $tpl->fetch( "design:workflow/run.tpl" );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}

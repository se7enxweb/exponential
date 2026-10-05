<?php
/**
 * The code of kernel/workflow/processlist.php, moved into a class (#207 stage 1). The file kernel/workflow/processlist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/workflow/processlist.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Workflow
{

class Processlist extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $Module = $Params['Module'];

        //////////////////////
        //$userID = eZUser::currentUserID();
        $conds = array();
        //$conds['user_id'] =  $userID;
        $conds['status'] = array( array( \eZWorkflow::STATUS_DEFERRED_TO_CRON,
                                         \eZWorkflow::STATUS_FETCH_TEMPLATE,
                                         \eZWorkflow::STATUS_REDIRECT,
                                         \eZWorkflow::STATUS_WAITING_PARENT ) );
        $db = \eZDB::instance();
        if ( $db->databaseName() == 'oracle' )
            $conds['LENGTH(memento_key)'] = array( '!=', 0 );
        else
            $conds['memento_key'] = array( '!=', '' );


        $offset = $Params['Offset'];
        if ( !is_numeric( $offset ) )
        {
            $offset = 0;
        }

        // The sizes on offer are configured, not written here; the preference holds
        // the position in that list, which is what it has always held.
        list( $limit, $limitChoice, $limitChoices ) =
            \expAdminPagination::chosen( 'workflow/processlist', 'admin_workflow_processlist_limit',
                                        array( 10, 25, 50, 100 ) );

        $viewParameters = array( 'offset' => $offset );

        $plist = \eZWorkflowProcess::fetchList( $conds, true, $offset, $limit );
        $plistCount = \eZWorkflowProcess::count( \eZWorkflowProcess::definition(), $conds );

        list( $outList2, $totalProcessCount ) = self::processesByTrigger( is_array( $plist ) ? $plist : array() );

        // Template handling

        $tpl = \eZTemplate::factory();

        $tpl->setVariable( "module", $Module );
        $tpl->setVariable( "trigger_list", $outList2 );
        $tpl->setVariable( "total_process_count", $totalProcessCount );
        $tpl->setVariable( 'page_limit', $limit );
        $tpl->setVariable( 'limit_choices', $limitChoices );
        $tpl->setVariable( 'limit_choice', $limitChoice );
        $tpl->setVariable( 'list_count', $plistCount );
        $tpl->setVariable( 'view_parameters', $viewParameters );

        $Module->setTitle( "Workflow processes list" );
        $Result = array();
        $Result['content'] = $tpl->fetch( "design:workflow/processlist.tpl" );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Workflow' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/workflow', 'Process list' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * Groups the workflow processes by the trigger they wait in.
     *
     * The trigger is read from the child memento of the process. The main memento is not needed for that, and a
     * process without one (for example one deferred to cron) used to be left out, so the list was empty although
     * workflows were waiting.
     *
     * @param \eZWorkflowProcess[] $processList
     * @return array array( array( '<module>/<function>/<name>' => array( 'trigger' => eZTrigger, 'process_list' => eZWorkflowProcess[] ) ), number of processes listed )
     */
    public static function processesByTrigger( array $processList )
    {
        $totalProcessCount = 0;
        $outList = array();
        foreach ( $processList as $p )
        {
            $mementoChild = \eZOperationMemento::fetchChild( $p->attribute( 'memento_key' ) );
            if ( !$mementoChild )
                continue;

            $mementoChildData = $mementoChild->data();

            $triggers = \eZTrigger::fetchList( array( 'module_name' => $mementoChildData['module_name'],
                                                     'function_name' => $mementoChildData['operation_name'],
                                                     'name' => $mementoChildData['name'] ) );
            if ( count( $triggers ) > 0 )
            {
                $trigger = $triggers[0];
                if ( is_object( $trigger ) )
                {
                    $nkey = $trigger->attribute( 'module_name' ) . '/' . $trigger->attribute( 'function_name' ) . '/' . $trigger->attribute( 'name' );

                    if ( !isset( $outList[ $nkey ] ) )
                    {
                        $outList[ $nkey ] = array( 'trigger' => $trigger,
                                                   'process_list' => array() );
                    }
                    $outList[ $nkey ][ 'process_list' ][] = $p;
                    $totalProcessCount++;
                }
            }
        }
        return array( $outList, $totalProcessCount );
    }
}

}

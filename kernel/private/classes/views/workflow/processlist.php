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
     * @param callable|null $triggerOf function( process ) returning the eZTrigger the process waits in, or null; the
     *                                 default reads it from the child memento (triggerOfProcess())
     * @return array array( array( '<module>/<function>/<name>' => array( 'trigger' => eZTrigger, 'process_list' => eZWorkflowProcess[] ) ), number of processes listed )
     */
    public static function processesByTrigger( array $processList, $triggerOf = null )
    {
        if ( !is_callable( $triggerOf ) )
        {
            $triggers = array();
            $triggerOf = function ( $process ) use ( &$triggers )
            {
                return Processlist::triggerOfProcess( $process, $triggers );
            };
        }
        $totalProcessCount = 0;
        $outList = array();
        foreach ( $processList as $p )
        {
            if ( !is_object( $p ) )
                continue;
            $trigger = call_user_func( $triggerOf, $p );
            if ( !is_object( $trigger ) )
                continue;

            $nkey = $trigger->attribute( 'module_name' ) . '/' . $trigger->attribute( 'function_name' ) . '/' . $trigger->attribute( 'name' );
            if ( !isset( $outList[ $nkey ] ) )
            {
                $outList[ $nkey ] = array( 'trigger' => $trigger,
                                           'process_list' => array() );
            }
            $outList[ $nkey ][ 'process_list' ][] = $p;
            $totalProcessCount++;
        }
        return array( $outList, $totalProcessCount );
    }

    /**
     * Returns the trigger a workflow process waits in, read from its child memento: the first trigger of the module,
     * operation and name the memento names. Null when the process has no child memento, the memento does not name
     * them, or no such trigger exists.
     *
     * @param \eZWorkflowProcess $process
     * @param array $cache triggers already looked up in this listing, by module/operation/name
     * @return \eZTrigger|null
     */
    public static function triggerOfProcess( $process, &$cache = null )
    {
        $mementoChild = \eZOperationMemento::fetchChild( $process->attribute( 'memento_key' ) );
        if ( !$mementoChild )
            return null;

        $data = $mementoChild->data();
        if ( !isset( $data['module_name'], $data['operation_name'], $data['name'] ) )
            return null;

        $key = $data['module_name'] . '/' . $data['operation_name'] . '/' . $data['name'];
        if ( !is_array( $cache ) )
            $cache = array();
        if ( !array_key_exists( $key, $cache ) )
        {
            $triggers = \eZTrigger::fetchList( array( 'module_name' => $data['module_name'],
                                                     'function_name' => $data['operation_name'],
                                                     'name' => $data['name'] ) );
            $cache[$key] = ( is_array( $triggers ) && isset( $triggers[0] ) && is_object( $triggers[0] ) ) ? $triggers[0] : null;
        }
        return $cache[$key];
    }
}

}

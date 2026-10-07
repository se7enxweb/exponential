<?php
/**
 * The code of kernel/trigger/list.php, moved into a class (#207 stage 1). The file kernel/trigger/list.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of ./kernel/trigger/list.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'makeTriggerArray' ) ) {
function makeTriggerArray( $triggerList )
{
    $triggerArray = array();
    foreach ( $triggerList as $trigger )
    {
        $newKey = $trigger->attribute( 'module_name' ) . '_' . $trigger->attribute( 'function_name' ) . '_' . $trigger->attribute( 'connect_type' );
        $triggerArray[$newKey] = $trigger;
    }
    return $triggerArray;
}
}
}

namespace Exponential\View\Kernel\Trigger
{

class ListView extends \Exponential\Runnable\ModuleView
{
    /** The session variable that carries the result of Apply changes over the redirect back to the list. */
    const FEEDBACK_KEY = 'eZTriggerListFeedback';

    /**
     * How many triggers differ between two maps of module/function/connect type => workflow id.
     *
     * @param array $before
     * @param array $after
     * @return int
     */
    public static function changedCount( array $before, array $after )
    {
        $changed = 0;
        foreach ( array_unique( array_merge( array_keys( $before ), array_keys( $after ) ) ) as $key )
        {
            $old = isset( $before[$key] ) ? $before[$key] : null;
            $new = isset( $after[$key] ) ? $after[$key] : null;
            if ( $old !== $new )
                $changed++;
        }
        return $changed;
    }

    /**
     * Every published workflow (enabled or not) with its number of events. One query.
     *
     * @return array workflow id => hash( id, name, enabled, events, modified )
     */
    public static function workflowFacts()
    {
        $db = \eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT w.id, w.name, w.is_enabled, w.modified, COUNT(e.id) AS event_count
                                    FROM ezworkflow w
                                    LEFT JOIN ezworkflow_event e ON e.workflow_id = w.id AND e.version = w.version
                                   WHERE w.version = 0
                                GROUP BY w.id, w.name, w.is_enabled, w.modified' );
        $facts = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $facts[(int)$row['id']] = array( 'id' => (int)$row['id'], 'name' => (string)$row['name'],
                                             'enabled' => (int)$row['is_enabled'] === 1,
                                             'events' => (int)$row['event_count'], 'modified' => (int)$row['modified'] );
        }
        return $facts;
    }

    /**
     * How many processes of each workflow wait (for the cronjob, a user or a parent). One query.
     *
     * @return array workflow id => count
     */
    public static function waitingByWorkflow()
    {
        $filters = \Exponential\View\Kernel\Workflow\Processlist::statusFilters();
        $statuses = implode( ', ', array_map( 'intval', $filters['waiting'] ) );
        $db = \eZDB::instance();
        $rows = $db->arrayQuery( "SELECT workflow_id, COUNT(*) AS process_count FROM ezworkflow_process
                                   WHERE status IN ( $statuses ) GROUP BY workflow_id" );
        $waiting = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $waiting[(int)$row['workflow_id']] = (int)$row['process_count'];
        }
        return $waiting;
    }

    /**
     * The possible triggers in words, with the workflow each one runs and what needs attention.
     *
     * "not_allowed": the stored workflow is not among those the select offers (it was disabled, or an event of
     * it does not allow this operation); the select keeps it as an extra choice, so Apply changes does not drop
     * it unnoticed. "missing": the trigger names a workflow that no longer exists.
     *
     * @param array $possibleTriggers what the view hands the page as possible_triggers
     * @param array $facts workflowFacts()
     * @param array $waiting waitingByWorkflow()
     * @return array
     */
    public static function rows( array $possibleTriggers, array $facts, array $waiting )
    {
        $rows = array();
        foreach ( $possibleTriggers as $trigger )
        {
            $workflowID = (int)$trigger['workflow_id'];
            $before = $trigger['connect_type'] === 'before';
            $allowedIDs = array();
            foreach ( (array)$trigger['allowed_workflows'] as $workflow )
                $allowedIDs[] = (int)( is_object( $workflow ) ? $workflow->attribute( 'id' ) : $workflow['id'] );
            $fact = $workflowID > 0 && isset( $facts[$workflowID] ) ? $facts[$workflowID] : false;
            $rows[] = array( 'key' => $trigger['key'],
                             'module' => $trigger['module'],
                             'operation' => $trigger['operation'],
                             'connect_type' => $trigger['connect_type'],
                             'label' => \Exponential\View\Kernel\Workflow\Processlist::triggerLabel(
                                            $trigger['module'], $trigger['operation'], ( $before ? 'pre_' : 'post_' ) . $trigger['operation'] ),
                             'workflow_id' => $workflowID,
                             'is_set' => $workflowID > 0,
                             'workflow' => $fact,
                             'missing' => $workflowID > 0 && $fact === false,
                             'not_allowed' => $workflowID > 0 && $fact !== false && !in_array( $workflowID, $allowedIDs, true ),
                             'allowed_count' => count( $allowedIDs ),
                             'waiting' => $workflowID > 0 && isset( $waiting[$workflowID] ) ? $waiting[$workflowID] : 0 );
        }
        return $rows;
    }

    /**
     * The rows by module, in the order they come.
     *
     * @param array $rows rows()
     * @return array of hash( module, rows, set )
     */
    public static function groupByModule( array $rows )
    {
        $groups = array();
        foreach ( $rows as $row )
        {
            if ( !isset( $groups[$row['module']] ) )
                $groups[$row['module']] = array( 'module' => $row['module'], 'rows' => array(), 'set' => 0 );
            $groups[$row['module']]['rows'][] = $row;
            if ( $row['is_set'] )
                $groups[$row['module']]['set']++;
        }
        return array_values( $groups );
    }

    /**
     * The stored triggers this list does not offer: their operation, or this connection type of it, is not in
     * workflow.ini [OperationSettings] AvailableOperationList (any more). They cannot be changed here, only
     * removed.
     *
     * @param array $possibleTriggers
     * @param array $triggers eZTrigger objects
     * @return array of hash( id, module, function, connect_type, label, workflow_id, available )
     */
    public static function orphans( array $possibleTriggers, array $triggers )
    {
        $offered = array();
        foreach ( $possibleTriggers as $trigger )
            $offered[$trigger['module'] . '_' . $trigger['operation'] . '_' . $trigger['connect_type'][0]] = true;
        $orphans = array();
        foreach ( $triggers as $trigger )
        {
            $module = (string)$trigger->attribute( 'module_name' );
            $function = (string)$trigger->attribute( 'function_name' );
            $connect = (string)$trigger->attribute( 'connect_type' );
            if ( isset( $offered[$module . '_' . $function . '_' . $connect] ) )
                continue;
            $before = $connect === 'b';
            $orphans[] = array( 'id' => (int)$trigger->attribute( 'id' ),
                                'module' => $module, 'function' => $function,
                                'connect_type' => $before ? 'before' : 'after',
                                'label' => \Exponential\View\Kernel\Workflow\Processlist::triggerLabel(
                                               $module, $function, ( $before ? 'pre_' : 'post_' ) . $function ),
                                'workflow_id' => (int)$trigger->attribute( 'workflow_id' ) );
        }
        return $orphans;
    }

    /**
     * @param array $rows rows()
     * @param array $orphans orphans()
     * @return array possible, set, unset, attention, orphans, waiting
     */
    public static function summary( array $rows, array $orphans )
    {
        $summary = array( 'possible' => count( $rows ), 'set' => 0, 'unset' => 0, 'attention' => 0,
                          'orphans' => count( $orphans ), 'waiting' => 0 );
        $counted = array();
        foreach ( $rows as $row )
        {
            $summary[$row['is_set'] ? 'set' : 'unset']++;
            if ( $row['missing'] || $row['not_allowed'] )
                $summary['attention']++;
            if ( $row['is_set'] && !isset( $counted[$row['workflow_id']] ) )
            {
                $summary['waiting'] += $row['waiting'];
                $counted[$row['workflow_id']] = true;
            }
        }
        return $summary;
    }

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();

        $Module = $Params['Module'];

        $possibleTriggers = array();

        $triggers = makeTriggerArray( \eZTrigger::fetchList() );

        foreach ( array_unique( \eZINI::instance( 'workflow.ini' )->variable( 'OperationSettings', 'AvailableOperationList' ) ) as $operation )
        {
            if ( $operation == '' )
            {
                continue;
            }
            $trigger = array();

            // the operation string has either two or three underscore characters.
            // Eg: shop_checkout, before_shop_checkout, after_shop_checkout.
            // Only the strings before and after are allowed in front of the module.
            $explodedOperation = explode ('_', $operation);
            $i = 0;

            if (sizeof ($explodedOperation) >= 3)
            {
                if (strcmp($explodedOperation[$i], "before") == 0 || strcmp($explodedOperation[$i], "after") == 0)
                    $moduleParts = array ($explodedOperation[$i++]);
            }
            else
            {
                $moduleParts = array ("before", "after");
            }

            foreach ($moduleParts as $trigger['connect_type'])
            {
                $trigger['module'] = $explodedOperation[$i]; // $i is either 0 or 1
                $trigger['operation'] = $explodedOperation[$i + 1];
                $trigger['workflow_id'] = 0;
                $trigger['key'] = $trigger['module'] . '_' . $trigger['operation'] . '_' . $trigger['connect_type'][0];
                $trigger['allowed_workflows'] = \eZWorkflow::fetchLimited( $trigger['module'], $trigger['operation'], $trigger['connect_type'] );

                foreach ( $triggers as $existendTrigger )
                {
                    if ( $existendTrigger->attribute( 'module_name' ) == $trigger['module'] &&
                         $existendTrigger->attribute( 'function_name' ) == $trigger['operation'] &&
                         $existendTrigger->attribute( 'connect_type' ) == $trigger['connect_type'][0] )
                    {
                         $trigger['workflow_id'] = $existendTrigger->attribute( 'workflow_id' );
                    }
                }

                $possibleTriggers[] = $trigger;
            }
        }

        // Audit (doc/bc/6.0/audit.md, system.workflow.trigger.change): the triggers before a change
        $auditTriggers = function () {
            $map = array();
            foreach ( (array)\eZTrigger::fetchList() as $t )
                $map[$t->attribute( 'module_name' ) . '/' . $t->attribute( 'function_name' ) . '/' . $t->attribute( 'connect_type' )] = (int)$t->attribute( 'workflow_id' );
            ksort( $map );
            return $map;
        };
        $auditChanged = function ( array $before, array $after ) {
            if ( $before === $after || !class_exists( 'expAuditHook' ) )
                return;
            $b = array();
            $a = array();
            foreach ( array_unique( array_merge( array_keys( $before ), array_keys( $after ) ) ) as $k )
            {
                $old = isset( $before[$k] ) ? $before[$k] : null;
                $new = isset( $after[$k] ) ? $after[$k] : null;
                if ( $old !== $new )
                {
                    $b[$k] = $old;
                    $a[$k] = $new;
                }
            }
            \expAuditHook::emit( 'system.workflow.trigger.change', array( 'object' => array( 'type' => 'trigger', 'id' => implode( ',', array_keys( $a ) ) ),
                'verb' => 'change', 'before' => array( 'workflows' => $b ), 'after' => array( 'workflows' => $a ) ) );
        };

        if ( $http->hasPostVariable( 'StoreButton' )  )
        {
            $auditBefore = $auditTriggers();
            $db = \eZDB::instance();
            $db->begin();
            foreach ( $possibleTriggers as $trigger )
            {
                if ( $http->hasPostVariable( 'WorkflowID_' . $trigger['key'] ) )
                {
                    $workflowID = $http->postVariable( 'WorkflowID_' . $trigger['key'] );
                    if( $workflowID != -1 )
                    {
                        if ( !array_key_exists( $trigger['key'], $triggers ) )
                        {
                            //create trigger
                            if ( $trigger['connect_type'] == 'before' )
                            {
                                $connectType = 'b';
                            }
                            else
                            {
                                $connectType = 'a';
                            }
                            $newTrigger = \eZTrigger::createNew( $trigger['module'], $trigger['operation'], $connectType, $workflowID );
                        }
                        else
                        {
                            $existendTrigger = $triggers[$trigger['key']];
                            if ( $existendTrigger->attribute( 'workflow_id' ) != $workflowID )
                            {
                                $existendTrigger = $triggers[$trigger['key']];
                                $existendTrigger->setAttribute( 'workflow_id', $workflowID );
                                $existendTrigger->store();
                            }
                            // update trigger
                        }
                    }
                    else if ( array_key_exists( $trigger['key'], $triggers ) )
                    {
                        $existendTrigger = $triggers[$trigger['key']];
                        $existendTrigger->remove();
                        //remove trigger
                    }
                }
            }
            $db->commit();
            $auditAfter = $auditTriggers();
            $auditChanged( $auditBefore, $auditAfter );
            // What the saving did comes back after the redirect: "3 triggers changed" or "nothing changed".
            $http->setSessionVariable( self::FEEDBACK_KEY, array( 'type' => 'saved', 'changed' => self::changedCount( $auditBefore, $auditAfter ) ) );
            $Module->redirectToView( 'list' );
            // Nothing is drawn before the redirect: drawing the list here would read and drop the message above.
            return $this->viewResult( null, null );

        }

        $moduleName='*';
        $functionName='*';

        $feedback = $http->hasSessionVariable( self::FEEDBACK_KEY ) ? $http->sessionVariable( self::FEEDBACK_KEY ) : false;
        if ( $feedback !== false )
            $http->removeSessionVariable( self::FEEDBACK_KEY );

        if ( $http->hasPostVariable( 'RemoveButton' )  )
        {
            if ( $http->hasPostVariable( 'DeleteIDArray' ) )
            {
                $deleteIDArray = $http->postVariable( 'DeleteIDArray' );

                $auditBefore = $auditTriggers();
                $db = \eZDB::instance();
                $db->begin();
                foreach ( $deleteIDArray as $deleteID )
                {
                    \eZPersistentObject::removeObject( \eZTrigger::definition(), array( 'id' => (int)$deleteID ) );
                }
                $db->commit();
                $auditAfter = $auditTriggers();
                $auditChanged( $auditBefore, $auditAfter );
                $feedback = array( 'type' => 'removed', 'changed' => self::changedCount( $auditBefore, $auditAfter ) );
            }
            else
            {
                $feedback = array( 'type' => 'none_selected', 'changed' => 0 );
            }
        }

        $tpl = \eZTemplate::factory();

        $triggers = \eZTrigger::fetchList( array(
                                               'module' => $moduleName,
                                               'function' => $functionName
                                               ) );
        $showModuleList = false;
        $showFunctionList = false;
        $functionList = array();
        $moduleList = array();
        if ( $moduleName == '*' )
        {
            $showModuleList = true;
            $ini = \eZINI::instance( 'module.ini' );
            $moduleList = $ini->variable( 'ModuleSettings', 'ModuleList' );
        }
        elseif( $functionName == '*' )
        {
            $mod = \eZModule::exists( $moduleName );
            $functionList = array_keys( $mod->attribute( 'available_functions' ) );
            \eZDebug::writeNotice( $functionList, "functions" );
            $showFunctionList = true;
        }

        $tpl->setVariable( 'current_module', $moduleName );
        $tpl->setVariable( 'current_function', $functionName );
        $tpl->setVariable( 'show_functions', $showFunctionList );
        $tpl->setVariable( 'show_modules', $showModuleList );

        $tpl->setVariable( 'possible_triggers', $possibleTriggers );

        $tpl->setVariable( 'modules', $moduleList );
        $tpl->setVariable( 'functions', $functionList );

        $tpl->setVariable( 'triggers', $triggers );
        $tpl->setVariable( 'module', $Module );

        // The redesigned page: each possible trigger in words with its workflow, grouped by module, the triggers
        // stored for operations this list does not offer, an overview and what the last action did.
        $rows = self::rows( $possibleTriggers, self::workflowFacts(), self::waitingByWorkflow() );
        $orphans = self::orphans( $possibleTriggers, $triggers );
        $tpl->setVariable( 'trigger_groups', self::groupByModule( $rows ) );
        $tpl->setVariable( 'trigger_orphans', $orphans );
        $tpl->setVariable( 'trigger_summary', self::summary( $rows, $orphans ) );
        $tpl->setVariable( 'trigger_feedback', $feedback );

        $Result['content'] = $tpl->fetch( 'design:trigger/list.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'kernel/trigger', 'Trigger' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'kernel/trigger', 'List' ),
                                        'url' => false ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}

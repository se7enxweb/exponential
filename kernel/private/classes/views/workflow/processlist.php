<?php
/**
 * The code of kernel/workflow/processlist.php, moved into a class (#207 stage 1). The file kernel/workflow/processlist.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 * User guide of the page: doc/guides/workflows.md
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
    /** The translation context of every text this page shows. */
    const CONTEXT = 'design/admin/workflow/processlist';

    /** The session variable that carries the result of a cancel over the redirect back to the list. */
    const FEEDBACK_KEY = 'eZWorkflowProcessListFeedback';

    /** The cronjob script that resumes processes deferred to cron. */
    const CRONJOB_SCRIPT = 'workflow.php';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $http = \eZHTTPTool::instance();
        $Module = $Params['Module'];
        $db = \eZDB::instance();
        $now = time();

        // The status filter: "waiting" (the default, and all this page ever showed), "stopped" or "all".
        $statusFilter = self::statusFilter( isset( $Params['Status'] ) ? $Params['Status'] : null );

        // Cancelling, in two steps. The first button only asks: it shows the processes and what cancelling them
        // does. Only the second one, on that page, cancels; the result comes back after a redirect, so a reload
        // never cancels twice.
        $confirmIDs = array();
        if ( $http->hasPostVariable( 'ConfirmCancelProcessButton' ) )
            $confirmIDs = self::processIDList( $http->postVariable( 'ConfirmCancelProcessButton' ) );
        else if ( $http->hasPostVariable( 'ConfirmCancelSelectedButton' ) )
            $confirmIDs = self::processIDList( $http->hasPostVariable( 'SelectedProcessIDList' ) ? $http->postVariable( 'SelectedProcessIDList' ) : array() );

        $feedback = array();
        $redirectURI = $Module->functionURI( 'processlist' ) . ( $statusFilter !== 'waiting' ? '/(status)/' . $statusFilter : '' );
        if ( $http->hasPostVariable( 'CancelProcessesButton' ) )
        {
            $ids = self::processIDList( $http->hasPostVariable( 'ProcessIDList' ) ? $http->postVariable( 'ProcessIDList' ) : array() );
            $feedback = self::cancelProcesses( $ids );
            $http->setSessionVariable( self::FEEDBACK_KEY, $feedback );
            return $this->viewResult( null, $Module->redirectTo( $redirectURI ) );
        }
        if ( $http->hasPostVariable( 'KeepProcessesButton' ) )
            return $this->viewResult( null, $Module->redirectTo( $redirectURI ) );

        if ( $http->hasSessionVariable( self::FEEDBACK_KEY ) )
        {
            $feedback = (array)$http->sessionVariable( self::FEEDBACK_KEY );
            $http->removeSessionVariable( self::FEEDBACK_KEY );
        }

        $conds = self::statusConditions( $statusFilter, $db->databaseName() );

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
        if ( $statusFilter !== 'waiting' )
            $viewParameters['status'] = $statusFilter;

        $plist = \eZWorkflowProcess::fetchList( $conds, true, $offset, $limit );
        $plistCount = \eZWorkflowProcess::count( \eZWorkflowProcess::definition(), $conds );
        $plist = is_array( $plist ) ? $plist : array();

        list( $outList2, $totalProcessCount ) = self::processesByTrigger( $plist );

        // The page itself: every process with what it is waiting for, in words, grouped by its trigger.
        $groups = self::pageGroups( $outList2, $plist, $now );

        // The processes a cancel would apply to, for the confirmation step.
        $confirmList = array();
        if ( $confirmIDs )
        {
            foreach ( $confirmIDs as $id )
            {
                $process = \eZWorkflowProcess::fetch( $id );
                if ( $process instanceof \eZWorkflowProcess )
                    $confirmList[] = self::processView( $process, $now, self::approvalItems( array( $id ) ) );
            }
            if ( !$confirmList )
                $feedback[] = array( 'ok' => false, 'message' => \ezpI18n::tr( self::CONTEXT, 'Select at least one process to cancel.' ) );
        }

        $counts = self::statusCounts();
        $summary = self::summary( $counts['by_status'], $counts['oldest_waiting'], $now );

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

        $tpl->setVariable( 'wfp_groups', $groups );
        $tpl->setVariable( 'wfp_status_filter', $statusFilter );
        $tpl->setVariable( 'wfp_summary', $summary );
        $tpl->setVariable( 'wfp_feedback', $feedback );
        $tpl->setVariable( 'wfp_confirm_list', $confirmList );
        $tpl->setVariable( 'wfp_cronjob', self::workflowCronjob( \expCronjobRunner::parts(), \expCronjobRunner::scheduledParts(), $now ) );
        $tpl->setVariable( 'wfp_now', $now );

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

    /* ---- What the page shows: no database below this line until statusCounts() ------------------------------ */

    /**
     * The process statuses each filter of the page shows.
     *
     * "waiting" are the processes that will still move on: deferred to the workflow cronjob, showing a page to the
     * user or redirecting them, or waiting for a parent process. "stopped" are the ones that will not: failed,
     * cancelled, reset, done, or marked running (busy) - a process is only busy while a request runs it, so one
     * that stays busy was cut off. "all" is every process.
     *
     * @return array filter => list of eZWorkflow::STATUS_* values (null for every status)
     */
    public static function statusFilters()
    {
        return array( 'waiting' => array( \eZWorkflow::STATUS_DEFERRED_TO_CRON,
                                          \eZWorkflow::STATUS_FETCH_TEMPLATE,
                                          \eZWorkflow::STATUS_REDIRECT,
                                          \eZWorkflow::STATUS_WAITING_PARENT,
                                          \eZWorkflow::STATUS_FETCH_TEMPLATE_REPEAT ),
                      'stopped' => array( \eZWorkflow::STATUS_NONE,
                                          \eZWorkflow::STATUS_BUSY,
                                          \eZWorkflow::STATUS_DONE,
                                          \eZWorkflow::STATUS_FAILED,
                                          \eZWorkflow::STATUS_CANCELLED,
                                          \eZWorkflow::STATUS_RESET ),
                      'all' => null );
    }

    /**
     * The filter asked for in the address, or "waiting" when there is none or it is not one of them.
     *
     * @param mixed $value
     * @return string
     */
    public static function statusFilter( $value )
    {
        $value = is_string( $value ) ? strtolower( trim( $value ) ) : '';
        return array_key_exists( $value, self::statusFilters() ) ? $value : 'waiting';
    }

    /**
     * The conditions eZWorkflowProcess::fetchList() and count() take for a filter.
     *
     * "waiting" keeps the condition the page always had: a process without a memento key cannot be resumed and was
     * never listed. The other filters list every process of their statuses, memento or not, since a stopped
     * process has usually lost its mementos already.
     *
     * @param string $filter
     * @param string $databaseName eZDB::databaseName(); Oracle compares an empty string differently
     * @return array
     */
    public static function statusConditions( $filter, $databaseName = '' )
    {
        $filters = self::statusFilters();
        if ( !array_key_exists( $filter, $filters ) )
            $filter = 'waiting';
        $statuses = $filters[$filter];   // null for "all": not isset()

        $conds = array();
        if ( $statuses !== null )
            $conds['status'] = array( $statuses );
        if ( $filter === 'waiting' )
        {
            if ( $databaseName == 'oracle' )
                $conds['LENGTH(memento_key)'] = array( '!=', 0 );
            else
                $conds['memento_key'] = array( '!=', '' );
        }
        return $conds;
    }

    /**
     * A process status in words: what it means for the content and the person who started it.
     *
     * @param int $status eZWorkflow::STATUS_*
     * @return array key (cron, person, parent, failed, running, ended), label, explanation, tone (info, warn, bad, ok, muted), code, name
     */
    public static function statusInfo( $status )
    {
        $status = (int)$status;
        $c = self::CONTEXT;
        switch ( $status )
        {
            case \eZWorkflow::STATUS_DEFERRED_TO_CRON:
                $info = array( 'cron', \ezpI18n::tr( $c, 'Waiting for the workflow cronjob' ),
                               \ezpI18n::tr( $c, 'The workflow cronjob runs this step again each time it comes round, until the step is done - for example until an approver has decided.' ), 'info' );
                break;
            case \eZWorkflow::STATUS_FETCH_TEMPLATE:
            case \eZWorkflow::STATUS_FETCH_TEMPLATE_REPEAT:
                $info = array( 'person', \ezpI18n::tr( $c, 'Waiting for the user' ),
                               \ezpI18n::tr( $c, 'A step showed the user a page and waits for their answer. It moves on when they send it; if they left, it stays here.' ), 'warn' );
                break;
            case \eZWorkflow::STATUS_REDIRECT:
                $info = array( 'person', \ezpI18n::tr( $c, 'Waiting for the user to come back' ),
                               \ezpI18n::tr( $c, 'A step sent the user to another page (a payment provider, for example) and waits for them to return.' ), 'warn' );
                break;
            case \eZWorkflow::STATUS_WAITING_PARENT:
                $info = array( 'parent', \ezpI18n::tr( $c, 'Waiting for its parent workflow' ),
                               \ezpI18n::tr( $c, 'This process was started by another workflow (a multiplexer step) and moves on together with it.' ), 'info' );
                break;
            case \eZWorkflow::STATUS_FAILED:
                $info = array( 'failed', \ezpI18n::tr( $c, 'Failed' ),
                               \ezpI18n::tr( $c, 'A step rejected the content or went wrong. Nothing will run this process again.' ), 'bad' );
                break;
            case \eZWorkflow::STATUS_BUSY:
                $info = array( 'running', \ezpI18n::tr( $c, 'Marked as running' ),
                               \ezpI18n::tr( $c, 'A process is running only while a request or the cronjob runs it. One that stays marked running was cut off and will not move on by itself.' ), 'warn' );
                break;
            case \eZWorkflow::STATUS_CANCELLED:
                $info = array( 'ended', \ezpI18n::tr( $c, 'Cancelled' ),
                               \ezpI18n::tr( $c, 'A step cancelled the workflow. The workflow cronjob removes such processes when it meets them.' ), 'muted' );
                break;
            case \eZWorkflow::STATUS_DONE:
                $info = array( 'ended', \ezpI18n::tr( $c, 'Done' ),
                               \ezpI18n::tr( $c, 'Every step has run. A finished process is normally removed straight away.' ), 'ok' );
                break;
            case \eZWorkflow::STATUS_RESET:
                $info = array( 'ended', \ezpI18n::tr( $c, 'Reset' ),
                               \ezpI18n::tr( $c, 'A step reset the workflow for reuse. Nothing will run this process again.' ), 'muted' );
                break;
            default:
                $info = array( 'ended', \ezpI18n::tr( $c, 'Not started' ),
                               \ezpI18n::tr( $c, 'The process has no state yet. Nothing will run it.' ), 'muted' );
        }
        $name = \eZWorkflow::statusName( $status );
        return array( 'key' => $info[0], 'label' => $info[1], 'explanation' => $info[2], 'tone' => $info[3],
                      'code' => $status, 'name' => $name === false ? '' : $name );
    }

    /**
     * The name of an event status (eZWorkflowType::STATUS_*), with its number.
     *
     * @param int $status
     * @return array code, name (empty when the number is unknown)
     */
    public static function eventStatusInfo( $status )
    {
        $name = \eZWorkflowType::statusName( (int)$status );
        return array( 'code' => (int)$status, 'name' => $name === false ? '' : $name );
    }

    /**
     * A trigger in words: "Before publishing content", "After an order is confirmed". Triggers the page has no
     * words for are named by their module and operation.
     *
     * @param string $module
     * @param string $function
     * @param string $name pre_<function> or post_<function>
     * @return string
     */
    public static function triggerLabel( $module, $function, $name )
    {
        $c = self::CONTEXT;
        $before = strpos( (string)$name, 'post_' ) !== 0;
        $known = array(
            'content/publish'       => array( 'Before publishing content', 'After publishing content' ),
            'content/read'          => array( 'Before content is read', 'After content is read' ),
            'content/hide'          => array( 'Before content is hidden or shown', 'After content is hidden or shown' ),
            'content/delete'        => array( 'Before content is deleted', 'After content is deleted' ),
            'content/move'          => array( 'Before content is moved', 'After content is moved' ),
            'content/swap'          => array( 'Before two locations are swapped', 'After two locations are swapped' ),
            'content/addlocation'   => array( 'Before a location is added', 'After a location is added' ),
            'content/removelocation'=> array( 'Before a location is removed', 'After a location is removed' ),
            'content/updatesection' => array( 'Before the section changes', 'After the section changes' ),
            'content/updatepriority'=> array( 'Before priorities change', 'After priorities change' ),
            'content/updatemainassignment' => array( 'Before the main location changes', 'After the main location changes' ),
            'content/updateobjectstate' => array( 'Before an object state changes', 'After an object state changes' ),
            'content/sort'          => array( 'Before the sort order changes', 'After the sort order changes' ),
            'shop/confirmorder'     => array( 'Before an order is confirmed', 'After an order is confirmed' ),
            'shop/checkout'         => array( 'Before checkout', 'After checkout' ),
            'shop/addtobasket'      => array( 'Before something is added to the basket', 'After something is added to the basket' ),
            'shop/updatebasket'     => array( 'Before the basket is updated', 'After the basket is updated' ),
            'user/activation'       => array( 'Before a user account is activated', 'After a user account is activated' ),
            'user/register'         => array( 'Before a user registers', 'After a user registers' ),
            'user/sendpassword'     => array( 'Before a password is sent', 'After a password is sent' ),
        );
        $key = $module . '/' . $function;
        if ( isset( $known[$key] ) )
            return \ezpI18n::tr( $c, $known[$key][$before ? 0 : 1] );
        return $before ? \ezpI18n::tr( $c, 'Before %operation', null, array( '%operation' => $key ) )
                       : \ezpI18n::tr( $c, 'After %operation', null, array( '%operation' => $key ) );
    }

    /**
     * How long ago something was, in words: "less than a minute", "5 minutes", "3 hours", "2 days".
     *
     * @param int $seconds
     * @return string
     */
    public static function ageText( $seconds )
    {
        $c = self::CONTEXT;
        $seconds = max( 0, (int)$seconds );
        if ( $seconds < 60 )
            return \ezpI18n::tr( $c, 'less than a minute' );
        if ( $seconds < 3600 )
        {
            $n = (int)floor( $seconds / 60 );
            return $n == 1 ? \ezpI18n::tr( $c, '1 minute' ) : \ezpI18n::tr( $c, '%count minutes', null, array( '%count' => $n ) );
        }
        if ( $seconds < 86400 )
        {
            $n = (int)floor( $seconds / 3600 );
            return $n == 1 ? \ezpI18n::tr( $c, '1 hour' ) : \ezpI18n::tr( $c, '%count hours', null, array( '%count' => $n ) );
        }
        $n = (int)floor( $seconds / 86400 );
        return $n == 1 ? \ezpI18n::tr( $c, '1 day' ) : \ezpI18n::tr( $c, '%count days', null, array( '%count' => $n ) );
    }

    /**
     * A content version's status in words ("waiting to be published" for the pending one a publish workflow holds).
     *
     * @param int $status eZContentObjectVersion::STATUS_*, or -1 when the version was not found
     * @return string empty when the version was not found
     */
    public static function versionStatusText( $status )
    {
        $c = self::CONTEXT;
        switch ( (int)$status )
        {
            case 0: return \ezpI18n::tr( $c, 'draft' );
            case 1: return \ezpI18n::tr( $c, 'published' );
            case 2: return \ezpI18n::tr( $c, 'waiting to be published' );
            case 3: return \ezpI18n::tr( $c, 'archived' );
            case 4: return \ezpI18n::tr( $c, 'rejected' );
            case 5: return \ezpI18n::tr( $c, 'internal draft' );
            case 7: return \ezpI18n::tr( $c, 'queued for publishing' );
        }
        return '';
    }

    /**
     * The overview: how many processes wait and for what, how many have stopped, and the oldest waiting one.
     *
     * @param array $countsByStatus eZWorkflow::STATUS_* => number of processes
     * @param int|false $oldestWaiting created time of the oldest waiting process, false when none waits
     * @param int $now
     * @return array waiting, cron, person, parent, failed, stopped (all that will not move on, failed included),
     *               total, oldest (timestamp or 0), oldest_age (words or ''), stuck (waiting longer than a day)
     */
    public static function summary( array $countsByStatus, $oldestWaiting, $now )
    {
        $filters = self::statusFilters();
        $summary = array( 'waiting' => 0, 'cron' => 0, 'person' => 0, 'parent' => 0, 'failed' => 0, 'stopped' => 0,
                          'total' => 0, 'oldest' => 0, 'oldest_age' => '', 'stuck' => false );
        foreach ( $countsByStatus as $status => $count )
        {
            $status = (int)$status;
            $count = (int)$count;
            $summary['total'] += $count;
            if ( in_array( $status, $filters['waiting'], true ) )
            {
                $summary['waiting'] += $count;
                $info = self::statusInfo( $status );
                if ( isset( $summary[$info['key']] ) )
                    $summary[$info['key']] += $count;
            }
            else
            {
                $summary['stopped'] += $count;
                if ( $status === \eZWorkflow::STATUS_FAILED )
                    $summary['failed'] += $count;
            }
        }
        if ( $oldestWaiting !== false && $oldestWaiting !== null && $summary['waiting'] > 0 )
        {
            $summary['oldest'] = (int)$oldestWaiting;
            $summary['oldest_age'] = self::ageText( $now - (int)$oldestWaiting );
            $summary['stuck'] = ( $now - (int)$oldestWaiting ) > 86400;
        }
        return $summary;
    }

    /**
     * The process ids a form sent: one id or a list, as positive whole numbers, each once.
     *
     * @param mixed $value
     * @return int[]
     */
    public static function processIDList( $value )
    {
        $ids = array();
        foreach ( (array)$value as $id )
        {
            if ( is_int( $id ) || ( is_string( $id ) && ctype_digit( trim( $id ) ) ) )
            {
                $id = (int)$id;
                if ( $id > 0 && !in_array( $id, $ids, true ) )
                    $ids[] = $id;
            }
        }
        return $ids;
    }

    /**
     * Where the workflow cronjob is: the cronjob parts that run workflow.php, whether the crontab runs them, in words,
     * when next, and the command that runs the script alone.
     *
     * @param array $parts expCronjobRunner::parts()
     * @param array $scheduledParts expCronjobRunner::scheduledParts(): part => crontab line
     * @param int $now
     * @return array found (bool), part, label, anchor (the part's card on the cronjobs page), scheduled, schedule,
     *               schedule_text, next_run (0 when not scheduled), other_parts (more parts that run it)
     */
    public static function workflowCronjob( array $parts, array $scheduledParts, $now )
    {
        $found = array();
        foreach ( $parts as $part )
        {
            if ( !isset( $part['scripts'] ) || !is_array( $part['scripts'] ) )
                continue;
            foreach ( $part['scripts'] as $script )
            {
                if ( isset( $script['name'] ) && $script['name'] === self::CRONJOB_SCRIPT )
                {
                    $found[] = $part;
                    break;
                }
            }
        }
        $result = array( 'found' => false, 'part' => '', 'label' => '', 'anchor' => '', 'scheduled' => false,
                         'schedule' => '', 'schedule_text' => '', 'next_run' => 0, 'other_parts' => array() );
        if ( !$found )
            return $result;

        // A part the crontab runs is the one that matters; the first otherwise.
        $chosen = $found[0];
        foreach ( $found as $part )
        {
            if ( isset( $scheduledParts[$part['name']] ) )
            {
                $chosen = $part;
                break;
            }
        }
        $result['found'] = true;
        $result['part'] = $chosen['name'];
        $result['label'] = isset( $chosen['label'] ) ? $chosen['label'] : $chosen['name'];
        $result['anchor'] = 'cronjob-part-' . $chosen['name'];
        foreach ( $found as $part )
            if ( $part['name'] !== $chosen['name'] )
                $result['other_parts'][] = $part['name'];

        if ( isset( $scheduledParts[$chosen['name']] ) )
        {
            $schedule = \expCronjobRunner::scheduleOfLine( $scheduledParts[$chosen['name']] );
            $result['scheduled'] = true;
            if ( $schedule !== false )
            {
                $result['schedule'] = $schedule;
                $result['schedule_text'] = \expCronjobRunner::describeSchedule( $schedule );
                $result['next_run'] = (int)\expCronjobRunner::nextRun( $schedule, $now );
            }
        }
        return $result;
    }

    /**
     * Turns the trigger groups into what the page shows. Processes whose trigger cannot be found (a memento that is
     * gone, a trigger removed since) are not dropped from the page: they come last, in a group of their own, so the
     * list and its count agree.
     *
     * @param array $byTrigger the first element of processesByTrigger()
     * @param array $processList every process on this page of the list
     * @param int $now
     * @param array|null $approvals process id => collaboration item id; looked up when null
     * @return array of key, label, module, function, name, connect (before/after/''), count, processes (processView())
     */
    public static function pageGroups( array $byTrigger, array $processList, $now, $approvals = null )
    {
        $grouped = array();
        foreach ( $byTrigger as $entry )
            foreach ( $entry['process_list'] as $process )
                $grouped[spl_object_id( $process )] = true;
        $rest = array();
        foreach ( $processList as $process )
            if ( is_object( $process ) && !isset( $grouped[spl_object_id( $process )] ) )
                $rest[] = $process;

        if ( $approvals === null )
        {
            $ids = array();
            foreach ( $processList as $process )
                if ( is_object( $process ) )
                    $ids[] = (int)$process->attribute( 'id' );
            $approvals = self::approvalItems( $ids );
        }

        $groups = array();
        foreach ( $byTrigger as $key => $entry )
        {
            $trigger = $entry['trigger'];
            $module = (string)$trigger->attribute( 'module_name' );
            $function = (string)$trigger->attribute( 'function_name' );
            $name = (string)$trigger->attribute( 'name' );
            $views = array();
            foreach ( $entry['process_list'] as $process )
                $views[] = self::processView( $process, $now, $approvals );
            $groups[] = array( 'key' => $key, 'label' => self::triggerLabel( $module, $function, $name ),
                               'module' => $module, 'function' => $function, 'name' => $name,
                               'connect' => strpos( $name, 'post_' ) === 0 ? 'after' : 'before',
                               'count' => count( $views ), 'processes' => $views );
        }
        if ( $rest )
        {
            $views = array();
            foreach ( $rest as $process )
                $views[] = self::processView( $process, $now, $approvals );
            $groups[] = array( 'key' => '', 'label' => \ezpI18n::tr( self::CONTEXT, 'Trigger not recorded' ),
                               'module' => '', 'function' => '', 'name' => '', 'connect' => '',
                               'count' => count( $views ), 'processes' => $views );
        }
        return $groups;
    }

    /**
     * One process as the page shows it: the content and version it runs for, who started it, the workflow and the
     * step it is at, its status in words, its age, and the approval waiting in the collaboration inbox, if any.
     * Everything is read through the process's own attributes, so a stand-in object will do in a test.
     *
     * @param \eZWorkflowProcess|object $process
     * @param int $now
     * @param array $approvals process id => collaboration item id
     * @return array
     */
    public static function processView( $process, $now, array $approvals = array() )
    {
        $id = (int)$process->attribute( 'id' );
        $parameters = $process->attribute( 'parameter_list' );
        $parameters = is_array( $parameters ) ? $parameters : array();

        // The content: the process's own columns, else the operation's parameters (a content/publish process keeps
        // its object and version only there).
        $objectID = (int)$process->attribute( 'content_id' );
        $version = (int)$process->attribute( 'content_version' );
        if ( $objectID <= 0 && isset( $parameters['object_id'] ) )
            $objectID = (int)$parameters['object_id'];
        if ( $version <= 0 && isset( $parameters['version'] ) )
            $version = (int)$parameters['version'];

        $object = array( 'id' => $objectID, 'version' => $version, 'name' => '', 'exists' => false, 'version_status' => -1 );
        if ( $objectID > 0 && class_exists( 'eZContentObject' ) && $process instanceof \eZWorkflowProcess )
        {
            $contentObject = \eZContentObject::fetch( $objectID );
            if ( $contentObject instanceof \eZContentObject )
            {
                $object['exists'] = true;
                $object['name'] = (string)$contentObject->attribute( 'name' );
                $contentVersion = $version > 0 ? $contentObject->version( $version ) : null;
                if ( $contentVersion instanceof \eZContentObjectVersion )
                {
                    $object['version_status'] = (int)$contentVersion->attribute( 'status' );
                    $versionName = $contentVersion->attribute( 'name' );
                    if ( is_string( $versionName ) && $versionName !== '' )
                        $object['name'] = $versionName;
                }
            }
        }

        $object['version_status_text'] = self::versionStatusText( $object['version_status'] );

        // Who started it.
        $userID = (int)$process->attribute( 'user_id' );
        if ( $userID <= 0 && isset( $parameters['user_id'] ) )
            $userID = (int)$parameters['user_id'];
        $user = array( 'id' => $userID, 'name' => '', 'node_url' => '' );
        if ( $userID > 0 && $process instanceof \eZWorkflowProcess )
        {
            $userObject = \eZContentObject::fetch( $userID );
            if ( $userObject instanceof \eZContentObject )
            {
                $user['name'] = (string)$userObject->attribute( 'name' );
                $mainNode = $userObject->attribute( 'main_node' );
                if ( $mainNode instanceof \eZContentObjectTreeNode )
                    $user['node_url'] = (string)$mainNode->attribute( 'url_alias' );
            }
        }

        // The workflow and the step it is at.
        $workflowID = (int)$process->attribute( 'workflow_id' );
        $workflow = array( 'id' => $workflowID, 'name' => '', 'event_count' => 0 );
        $currentEvent = array( 'id' => (int)$process->attribute( 'event_id' ), 'position' => (int)$process->attribute( 'event_position' ),
                               'type' => '', 'type_name' => '', 'description' => '', 'information' => '',
                               'status' => self::eventStatusInfo( $process->attribute( 'event_status' ) ) );
        $lastEvent = array( 'id' => (int)$process->attribute( 'last_event_id' ), 'position' => (int)$process->attribute( 'last_event_position' ),
                            'type' => '', 'type_name' => '', 'description' => '', 'information' => '',
                            'status' => self::eventStatusInfo( $process->attribute( 'last_event_status' ) ) );
        if ( $process instanceof \eZWorkflowProcess )
        {
            $workflowObject = $process->attribute( 'workflow' );
            if ( $workflowObject instanceof \eZWorkflow )
            {
                $workflow['name'] = (string)$workflowObject->attribute( 'name' );
                $workflow['event_count'] = (int)$workflowObject->attribute( 'event_count' );
            }
            foreach ( array( 'workflow_event' => 'currentEvent', 'last_workflow_event' => 'lastEvent' ) as $attribute => $variable )
            {
                $event = $process->attribute( $attribute );
                if ( !( $event instanceof \eZWorkflowEvent ) )
                    continue;
                ${$variable}['description'] = (string)$event->attribute( 'description' );
                $type = $event->attribute( 'workflow_type' );
                if ( is_object( $type ) )
                {
                    ${$variable}['type'] = (string)$type->attribute( 'type' );
                    ${$variable}['type_name'] = (string)$type->attribute( 'name' );
                    ${$variable}['information'] = (string)$type->attribute( 'information' );
                }
            }
        }

        $created = (int)$process->attribute( 'created' );
        $modified = (int)$process->attribute( 'modified' );
        $status = self::statusInfo( $process->attribute( 'status' ) );
        $collaborationID = isset( $approvals[$id] ) ? (int)$approvals[$id] : 0;

        // What it waits for, as one sentence a person can act on.
        $c = self::CONTEXT;
        if ( $collaborationID > 0 && $status['key'] === 'cron' )
            $waitingFor = \ezpI18n::tr( $c, 'An approver must approve or reject it in the collaboration inbox; the workflow cronjob then carries on.' );
        else
            $waitingFor = $status['explanation'];

        $search = array( '#' . $id, $object['name'], 'v' . $version, $user['name'], $workflow['name'], $status['label'],
                         $currentEvent['type_name'], $currentEvent['description'] );

        return array( 'id' => $id,
                      'status' => $status,
                      'object' => $object,
                      'user' => $user,
                      'workflow' => $workflow,
                      'current_event' => $currentEvent,
                      'last_event' => $lastEvent,
                      'created' => $created,
                      'modified' => $modified,
                      'age' => self::ageText( $now - $created ),
                      'idle' => self::ageText( $now - max( $created, $modified ) ),
                      'stuck' => $status['key'] !== 'ended' && ( $now - max( $created, $modified ) ) > 86400,
                      'collaboration_id' => $collaborationID,
                      'waiting_for' => $waitingFor,
                      'memento_key' => (string)$process->attribute( 'memento_key' ),
                      'search' => strtolower( implode( ' ', array_filter( $search, 'strlen' ) ) ) );
    }

    /* ---- Database ---------------------------------------------------------------------------------------------- */

    /**
     * The number of processes of each status, and when the oldest waiting one started. One query.
     *
     * @return array by_status (status => count), oldest_waiting (timestamp, or false when none waits)
     */
    public static function statusCounts()
    {
        $db = \eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT status, COUNT(*) AS process_count, MIN(created) AS oldest FROM ezworkflow_process GROUP BY status' );
        $filters = self::statusFilters();
        $byStatus = array();
        $oldest = false;
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $status = (int)$row['status'];
            $byStatus[$status] = (int)$row['process_count'];
            if ( in_array( $status, $filters['waiting'], true ) && ( $oldest === false || (int)$row['oldest'] < $oldest ) )
                $oldest = (int)$row['oldest'];
        }
        ksort( $byStatus );
        return array( 'by_status' => $byStatus, 'oldest_waiting' => $oldest );
    }

    /**
     * The approval collaboration items of processes waiting in an approve step (ezapprove_items).
     *
     * @param int[] $processIDs
     * @return array process id => collaboration item id
     */
    public static function approvalItems( array $processIDs )
    {
        $ids = array();
        foreach ( $processIDs as $id )
            if ( (int)$id > 0 )
                $ids[] = (int)$id;
        if ( !$ids )
            return array();
        $db = \eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT workflow_process_id, collaboration_id FROM ezapprove_items WHERE workflow_process_id IN (' . implode( ',', array_unique( $ids ) ) . ')' );
        $items = array();
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            $row = array_change_key_case( $row, CASE_LOWER );
            $items[(int)$row['workflow_process_id']] = (int)$row['collaboration_id'];
        }
        return $items;
    }

    /**
     * Cancels processes and reports, per process, what was done.
     *
     * @param int[] $ids
     * @return array of ok, message
     */
    public static function cancelProcesses( array $ids )
    {
        $c = self::CONTEXT;
        $feedback = array();
        if ( !$ids )
            return array( array( 'ok' => false, 'message' => \ezpI18n::tr( $c, 'Select at least one process to cancel.' ) ) );
        foreach ( $ids as $id )
        {
            $process = \eZWorkflowProcess::fetch( (int)$id );
            if ( !( $process instanceof \eZWorkflowProcess ) )
            {
                $feedback[] = array( 'ok' => false, 'message' => \ezpI18n::tr( $c, 'Process %id no longer exists; it may have finished in the meantime.', null, array( '%id' => (int)$id ) ) );
                continue;
            }
            $result = self::cancelProcess( $process );
            $feedback[] = array( 'ok' => $result['ok'], 'message' => $result['message'] );
        }
        return $feedback;
    }

    /**
     * Cancels one process the way the workflow cronjob ends a cancelled one, and puts back what the waiting process
     * held: its content version, if still pending, becomes a draft again (as a rejected approval leaves it), the
     * approval request leaves the approvers' inbox, and its mementos are removed with it. Nothing is published and
     * nothing is deleted from the content.
     *
     * @param \eZWorkflowProcess $process
     * @return array ok, message, version_to_draft (bool), approval_closed (int collaboration item id or 0)
     */
    public static function cancelProcess( \eZWorkflowProcess $process )
    {
        $c = self::CONTEXT;
        $db = \eZDB::instance();
        $id = (int)$process->attribute( 'id' );
        $parameters = $process->attribute( 'parameter_list' );
        $parameters = is_array( $parameters ) ? $parameters : array();
        $objectID = (int)$process->attribute( 'content_id' ) ?: ( isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0 );
        $versionNumber = (int)$process->attribute( 'content_version' ) ?: ( isset( $parameters['version'] ) ? (int)$parameters['version'] : 0 );
        $toDraft = false;
        $approvalClosed = 0;

        try
        {
            $db->begin();

            // The version waiting to be published goes back to the author as a draft.
            if ( $objectID > 0 && $versionNumber > 0 )
            {
                $version = \eZContentObjectVersion::fetchVersion( $versionNumber, $objectID );
                if ( $version instanceof \eZContentObjectVersion
                     && (int)$version->attribute( 'status' ) === \eZContentObjectVersion::STATUS_PENDING )
                {
                    $version->setAttribute( 'status', \eZContentObjectVersion::STATUS_DRAFT );
                    $version->store();
                    $toDraft = true;
                }
            }

            // The approval request, if this process is waiting in one, is closed in the approvers' inbox.
            $approvals = self::approvalItems( array( $id ) );
            if ( isset( $approvals[$id] ) )
            {
                $item = \eZCollaborationItem::fetch( $approvals[$id] );
                if ( $item instanceof \eZCollaborationItem )
                {
                    $item->setAttribute( 'status', \eZCollaborationItem::STATUS_INACTIVE );
                    $item->setAttribute( 'modified', time() );
                    $item->setIsActive( false );
                    $item->sync();
                    $approvalClosed = (int)$approvals[$id];
                }
                $db->query( 'DELETE FROM ezapprove_items WHERE workflow_process_id = ' . $id );
            }

            // The mementos that would resume the operation, then the process itself (its events' own cleanup runs).
            $key = (string)$process->attribute( 'memento_key' );
            if ( $key !== '' )
            {
                $main = \eZOperationMemento::fetchMain( $key );
                if ( $main )
                    $main->remove();
                foreach ( (array)\eZOperationMemento::fetchList( $key ) as $memento )
                    if ( is_object( $memento ) )
                        $memento->remove();
            }
            $process->removeThis();

            $db->commit();
        }
        catch ( \Exception $e )
        {
            $db->rollback();
            \eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return array( 'ok' => false, 'version_to_draft' => false, 'approval_closed' => 0,
                          'message' => \ezpI18n::tr( $c, 'Process %id could not be cancelled: %error', null, array( '%id' => $id, '%error' => $e->getMessage() ) ) );
        }

        if ( $toDraft )
            $message = \ezpI18n::tr( $c, 'Process %id was cancelled. Version %version of object %object is a draft again; nothing was published.', null,
                                     array( '%id' => $id, '%version' => $versionNumber, '%object' => $objectID ) );
        else
            $message = \ezpI18n::tr( $c, 'Process %id was cancelled.', null, array( '%id' => $id ) );
        return array( 'ok' => true, 'message' => $message, 'version_to_draft' => $toDraft, 'approval_closed' => $approvalClosed );
    }
}

}

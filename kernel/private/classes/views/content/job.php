<?php
/**
 * The content/job/<id> view: the progress page of a content job (a large subtree remove or copy that runs in
 * the background, see kernel/classes/contentjob/), its JSON status (?json=1, polled by the page every two
 * seconds) and its actions: Cancel, Resume, Remove the partial copy.
 *
 * Who may see a job: the user who started it; a user with the content/jobs policy; a user with unlimited
 * content/remove (a remove job) or content/create (a copy job).
 * Guide: doc/bc/6.0/content-jobs.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Content
{

class Job extends \Exponential\Runnable\ModuleView
{
    /** log lines on the page and in the status */
    const LOG_LINES = 40;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $user = \eZUser::currentUser();
        $json = $http->hasGetVariable( 'json' );

        $id = isset( $Params['JobID'] ) ? (string) $Params['JobID'] : '';
        $job = preg_match( '/^[0-9a-z-]{1,64}$/', $id ) ? \expContentJob::fetch( $id ) : null;
        if ( !$job )
            return $json ? self::sendJson( array( 'ok' => false, 'error' => 'not found' ), 404 )
                         : $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        if ( !self::canAccess( $job, $user ) )
            return $json ? self::sendJson( array( 'ok' => false, 'error' => 'access denied' ), 403 )
                         : $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );

        if ( $json )
        {
            $info = self::info( $job );
            $info['ok'] = true;
            $info['log'] = implode( "\n", $job->log( self::LOG_LINES ) );
            $info['redirect_url'] = self::absoluteURL( $info['redirect_url'] );
            return self::sendJson( $info, 200 );
        }

        $actionError = '';
        if ( $http->hasPostVariable( 'CancelJobButton' ) || $http->hasPostVariable( 'ResumeJobButton' )
             || $http->hasPostVariable( 'RemovePartialCopyButton' ) )
        {
            $redirect = '/content/job/' . $job->id();
            $actionError = self::act( $job, $http, $user, $redirect );
            if ( $actionError === '' )
                return $Module->redirectTo( $redirect );
        }

        if ( $http->hasGetVariable( 'log' ) )
            return self::sendLog( $job );

        $info = self::info( $job );
        $info['log'] = implode( "\n", $job->log( self::LOG_LINES ) );
        $info['details'] = array();
        $info['error_node_id'] = 0;
        if ( class_exists( 'Exponential\\Service\\ContentJobDetails' ) )
        {
            $info['details'] = \Exponential\Service\ContentJobDetails::groups( $job, $info );
            $info['error_node_id'] = \Exponential\Service\ContentJobDetails::errorNodeID( $job );
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'job', $info );
        $tpl->setVariable( 'action_error', $actionError );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:content/job.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'design/admin/content/job', 'Content jobs' ),
                                        'url' => 'content/jobs' ),
                                 array( 'text' => $info['operation_short'], 'url' => false ) );
        return $Result;
    }

    /**
     * Cancel, Resume or Remove the partial copy, from the progress page or the jobs list.
     *
     * @param \expContentJob $job
     * @param \eZHTTPTool $http
     * @param \eZUser $user
     * @param string &$redirect where to go afterwards (a new job's page for Remove the partial copy)
     * @return string '' when done, else the message to show
     */
    public static function act( $job, $http, $user, &$redirect )
    {
        try
        {
            if ( $http->hasPostVariable( 'CancelJobButton' ) )
            {
                if ( !$job->canCancel() )
                    return \ezpI18n::tr( 'design/admin/content/job', 'This job cannot be cancelled any more.' );
                $job->cancel();
                return '';
            }
            if ( $http->hasPostVariable( 'ResumeJobButton' ) )
            {
                if ( !self::canResume( $job ) || !$job->resume( false ) )
                    return \ezpI18n::tr( 'design/admin/content/job', 'This job cannot be resumed.' );
                self::spawnWithoutSession( $job );
                return '';
            }
            if ( $http->hasPostVariable( 'RemovePartialCopyButton' ) )
            {
                $root = self::partialCopyNodeID( $job );
                if ( !$root )
                    return \ezpI18n::tr( 'design/admin/content/job', 'There is no partial copy to remove.' );
                $removeJob = self::createJob( 'remove', array( 'node_ids' => array( $root ), 'move_to_trash' => false ),
                                              0, $error );
                if ( !$removeJob )
                    return $error;
                $redirect = '/content/job/' . $removeJob->id();
                return '';
            }
        }
        catch ( \expContentJobException $e )
        {
            return $e->getMessage();
        }
        return '';
    }

    /**
     * Should the operation run as a job? At or above content.ini [ContentJobSettings] SynchronousLimit (0:
     * always), and also whenever the old synchronous limit (MaxNodesRemoveSubtree, MaxNodesCopySubtree) would
     * have refused it: a job has no such limit. False when the engine is not installed.
     *
     * A small operation is recognised here, from the tree alone, without loading the engine: the synchronous
     * path of a small remove or copy runs exactly the code it ran before content jobs existed.
     *
     * @param string $type remove|copy
     * @param array $params the job's parameters
     * @param int|false $oldLimit the old synchronous limit, false for none
     * @return bool
     */
    public static function runAsJob( $type, array $params, $oldLimit = false )
    {
        $ids = self::nodeIDsOf( $type, $params );
        $total = 0;
        $largest = 0;
        foreach ( $ids as $id )
        {
            $node = \eZContentObjectTreeNode::fetch( (int) $id );
            if ( !$node )
                continue;
            $children = (int) $node->subTreeCount( array( 'Limitation' => array() ) );
            $total += $children + 1;
            $largest = max( $largest, $children );
        }
        $ini = \eZINI::instance( 'content.ini' );
        $limit = $ini->hasVariable( 'ContentJobSettings', 'SynchronousLimit' )
                 ? (int) $ini->variable( 'ContentJobSettings', 'SynchronousLimit' ) : 50;
        $overOldLimit = $oldLimit !== false && $largest > (int) $oldLimit;
        if ( $limit > 0 && $total < $limit && !$overOldLimit )
            return false;
        if ( !self::engineReady() )
            return false;
        try
        {
            return $overOldLimit || (bool) \expContentJob::shouldRunAsJob( $type, $params );
        }
        catch ( \Exception $e )
        {
            // the engine failing must not break the synchronous path: the view does what it did before
            \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            return false;
        }
    }

    /**
     * The "run now or in the background" choice of a confirmation page: how many items, which mode is preselected
     * (the user's last choice for this operation, else SynchronousLimit's), whether "now" is allowed and why not.
     * Null when the engine cannot be used: the page then shows no choice and works as it always did.
     *
     * @param string $op the operation's preference name (remove, copy, move, hide, section, state, addlocation, removelocation)
     * @param string $type the job type
     * @param array $params the job's parameters
     * @param int|false $oldLimit an older synchronous limit of the view (MaxNodesCopySubtree ...), false for none
     * @param string $oldSetting its name, for the reason shown
     * @return array|null
     */
    public static function modeChoice( $op, $type, array $params, $oldLimit = false, $oldSetting = '' )
    {
        if ( !self::engineReady() )
            return null;
        try
        {
            $count = (int) \expContentJob::countNodes( $type, $params );
            $nowAllowed = (bool) \expContentJob::nowAllowed( $type, $params );
            $default = (string) \expContentJob::defaultMode( $type, $params );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            return null;
        }
        $ini = \eZINI::instance( 'content.ini' );
        $syncLimit = (int) $ini->variable( 'ContentJobSettings', 'SynchronousLimit' );
        $nowLimit = $ini->hasVariable( 'ContentJobSettings', 'NowLimit' ) ? (int) $ini->variable( 'ContentJobSettings', 'NowLimit' ) : 0;
        $reason = '';
        if ( !$nowAllowed )
            $reason = \ezpI18n::tr( 'design/admin/content/job', 'Not for more than %limit items: the request would take too long (content.ini NowLimit).',
                                    null, array( '%limit' => $nowLimit ) );
        else if ( $oldLimit !== false && $count > (int) $oldLimit + 1 )
        {
            $nowAllowed = false;
            $reason = \ezpI18n::tr( 'design/admin/content/job', 'Not for more than %limit items here (content.ini %setting).',
                                    null, array( '%limit' => (int) $oldLimit, '%setting' => $oldSetting ) );
        }
        $preference = \eZPreferences::value( 'admin_content_job_mode_' . $op );
        if ( $preference === 'job' || $preference === 'now' )
            $default = $preference;
        if ( !$nowAllowed )
            $default = 'job';
        return array( 'op' => $op, 'count' => $count, 'default' => $default === 'job' ? 'job' : 'now',
                      'now_allowed' => $nowAllowed, 'now_reason' => $reason, 'sync_limit' => $syncLimit,
                      'now_limit' => $nowLimit, 'preference' => (string) $preference,
                      'recommended' => $count >= $syncLimit || $syncLimit === 0 ? 'job' : 'now' );
    }

    /**
     * Whether the user's choice (POST ContentJobMode: job|now) makes the operation a job, and remembers it as the
     * user's preference for this operation. Without a choice in the request (an older form, a script) it is the
     * old automatic decision.
     *
     * @param string $op
     * @param string $type
     * @param array $params
     * @param int|false $oldLimit
     * @return bool
     */
    public static function chosenAsJob( $op, $type, array $params, $oldLimit = false )
    {
        $http = \eZHTTPTool::instance();
        $mode = $http->hasPostVariable( 'ContentJobMode' ) ? (string) $http->postVariable( 'ContentJobMode' ) : '';
        if ( $mode !== 'job' && $mode !== 'now' )
            return self::runAsJob( $type, $params, $oldLimit );
        if ( !self::engineReady() )
            return false;
        \eZPreferences::setValue( 'admin_content_job_mode_' . $op, $mode );
        if ( $mode === 'job' )
            return true;
        $choice = self::modeChoice( $op, $type, $params, $oldLimit );
        // "now" is greyed out above the limits; a forged or stale form gets the job instead of a timeout
        return $choice && !$choice['now_allowed'];
    }

    /**
     * For an operation that had no confirmation page (hide, reveal, section ...): with the user's choice posted,
     * start the job or return null for the old code to run now; without one, show a confirmation page when the
     * background is preselected (a large operation, or the user's last choice), else null: a small operation
     * runs at once, as it always did.
     *
     * @param \eZModule $module
     * @param string $op the preference name
     * @param string $type the job type
     * @param array $params
     * @param string $actionURL where the confirmation posts to
     * @param string $backURL Cancel
     * @param string $title
     * @param string $text
     * @param array $hidden fields the form sends again
     * @param int $returnNodeID the node to open when the job is done
     * @return array|null the view's result, or null to go on with the old code
     */
    public static function interstitial( $module, $op, $type, array $params, $actionURL, $backURL, $title, $text,
                                         array $hidden = array(), $returnNodeID = 0 )
    {
        if ( !self::engineReady() )
            return null;
        $refused = self::lockRefusal( self::nodeIDsOf( $type, $params ), $backURL );
        if ( $refused )
            return $refused;
        $http = \eZHTTPTool::instance();
        if ( $http->hasPostVariable( 'ContentJobMode' ) )
            return self::chosenAsJob( $op, $type, $params ) ? self::startJob( $module, $type, $params, $backURL, $returnNodeID ) : null;
        $choice = self::modeChoice( $op, $type, $params );
        // the page for a large operation always (the last choice only preselects), for a small one only when the
        // user chose the background last time
        if ( !$choice || ( $choice['recommended'] !== 'job' && $choice['default'] !== 'job' ) )
            return null;
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'title', $title );
        $tpl->setVariable( 'text', $text );
        $tpl->setVariable( 'action_url', $actionURL );
        $tpl->setVariable( 'back_url', $backURL );
        $tpl->setVariable( 'hidden', $hidden );
        $tpl->setVariable( 'operation', $op );
        $tpl->setVariable( 'job_mode', $choice );
        $tpl->setVariable( 'job_summary', class_exists( 'Exponential\\Service\\ContentJobDetails' )
                                          ? \Exponential\Service\ContentJobDetails::subtreeSummary( self::nodeIDsOf( $type, $params ) ) : null );
        return array( 'content' => $tpl->fetch( 'design:content/job_confirm.tpl' ),
                      'path' => array( array( 'text' => $title, 'url' => false ) ) );
    }

    /**
     * The nodes an operation works on, from its parameters: copy source_node_id; move, hide, reveal, section
     * and state node_id; remove, addlocation and removelocation node_ids.
     *
     * @param string $type
     * @param array $params
     * @return int[]
     */
    public static function nodeIDsOf( $type, array $params )
    {
        if ( $type === 'copy' )
            return array( isset( $params['source_node_id'] ) ? (int) $params['source_node_id'] : 0 );
        if ( isset( $params['node_ids'] ) )
            return array_values( array_map( 'intval', (array) $params['node_ids'] ) );
        return isset( $params['node_id'] ) ? array( (int) $params['node_id'] ) : array();
    }

    /**
     * The target node of an operation: copy destination_node_id, move new_parent_node_id, addlocation
     * target_node_id; 0 for the others.
     *
     * @param array $params
     * @return int
     */
    public static function targetIDOf( array $params )
    {
        foreach ( array( 'destination_node_id', 'new_parent_node_id', 'target_node_id' ) as $key )
            if ( !empty( $params[$key] ) )
                return (int) $params[$key];
        return 0;
    }

    /**
     * Is the content jobs engine (kernel/classes/contentjob/) installed? Without it the views do what they
     * always did.
     *
     * @return bool
     */
    public static function engineReady()
    {
        return class_exists( 'expContentJob' ) && class_exists( 'expContentJobLock' );
    }

    /**
     * Whether any content job holds a lock, read from the lock index without loading the engine (so a
     * synchronous operation pays one stat and, at most, one small read).
     *
     * @return bool
     */
    protected static function anyLock()
    {
        $varDir = \eZSys::varDirectory();
        if ( $varDir === '' || $varDir === null )
            $varDir = 'var';
        if ( $varDir[0] !== '/' )
            $varDir = \eZSys::rootDir() . '/' . $varDir;
        $file = $varDir . '/jobs/content/locks.json';
        if ( !is_file( $file ) )
            return false;
        $data = @file_get_contents( $file );
        return $data === false || trim( $data, " \t\r\n{}[]" ) !== '';
    }

    /**
     * Creates and starts a job, or shows why it was refused.
     *
     * @param \eZModule $module
     * @param string $type
     * @param array $params
     * @param string $backURL where the refusal page's Back button leads
     * @return mixed the view's result (a redirect to the progress page, or the refusal page)
     */
    public static function startJob( $module, $type, array $params, $backURL, $returnNodeID = 0 )
    {
        $job = self::createJob( $type, $params, $returnNodeID, $error );
        if ( !$job )
            return self::refusedResult( $error, null, $backURL );
        $module->redirectTo( '/content/job/' . $job->id() );
        // not empty, so the calling view knows the job was started (the module's redirect is what is sent)
        return array( 'content' => '', 'path' => array() );
    }

    /**
     * Creates a job, remembers what the progress page shows once the nodes are gone (their names, the node
     * to open when it is done) and starts its worker.
     *
     * @param string $type
     * @param array $params
     * @param int $returnNodeID the node to open when a remove is done (0: the parent of the first node)
     * @param string &$error why it was refused
     * @return \expContentJob|null
     */
    public static function createJob( $type, array $params, $returnNodeID, &$error )
    {
        $error = '';
        try
        {
            $job = \expContentJob::create( $type, $params, \eZUser::currentUser() );
        }
        catch ( \expContentJobException $e )
        {
            $error = $e->getMessage();
            return null;
        }
        // before the worker starts, so nothing else writes the job meanwhile
        $ids = self::nodeIDsOf( $type, $params );
        $first = $ids ? \eZContentObjectTreeNode::fetch( (int) reset( $ids ) ) : null;
        if ( $first )
        {
            $job->set( 'gui_name', $first->attribute( 'name' ) );
            if ( class_exists( 'Exponential\\Service\\ContentJobDetails' ) )
                $job->set( 'gui_old_path', \Exponential\Service\ContentJobDetails::pathName( $first ) );
            if ( !$returnNodeID )
                $returnNodeID = (int) $first->attribute( 'parent_node_id' );
        }
        if ( self::targetIDOf( $params ) && ( $target = \eZContentObjectTreeNode::fetch( self::targetIDOf( $params ) ) ) )
            $job->set( 'gui_target_name', $target->attribute( 'name' ) );
        $job->set( 'gui_return_node_id', (int) $returnNodeID );
        $job->save();
        self::spawnWithoutSession( $job );
        return $job;
    }

    /**
     * Starts the job's worker with the PHP session closed. The worker inherits the request's open files; with
     * file sessions it would inherit the session file and its lock, and every further request of this user
     * (the progress page first) would wait until the worker ends. The session is opened again afterwards.
     *
     * @param \expContentJob $job
     * @return bool whether the worker was started
     */
    public static function spawnWithoutSession( $job )
    {
        $active = function_exists( 'session_status' ) && session_status() === PHP_SESSION_ACTIVE;
        if ( $active )
            session_write_close();
        $started = $job->spawn();
        if ( $active && session_status() !== PHP_SESSION_ACTIVE && !headers_sent() )
            @session_start();
        return $started;
    }

    /**
     * The refusal page when a synchronous remove, move or copy touches a subtree a job is working on, or null
     * when nothing is locked (or the engine is not installed).
     *
     * @param array $nodeIDs the nodes the operation touches (sources and the destination)
     * @param string $backURL
     * @return array|null
     */
    public static function lockRefusal( array $nodeIDs, $backURL )
    {
        if ( !self::anyLock() || !self::engineReady() )
            return null;
        $nodeIDs = array_values( array_filter( array_map( 'intval', $nodeIDs ) ) );
        try
        {
            $holder = $nodeIDs ? \expContentJobLock::checkNodes( $nodeIDs ) : false;
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeError( 'Content jobs: ' . $e->getMessage(), __METHOD__ );
            $holder = false;
        }
        if ( !$holder )
            return null;
        return self::refusedResult( \ezpI18n::tr( 'design/admin/content/job',
                                                  'This part of the content tree is being changed by a background job. Try again when the job is done.' ),
                                    $holder, $backURL );
    }

    /**
     * @param string $message
     * @param \expContentJob|null $holder the job that holds the lock
     * @param string $backURL
     * @return array the view's result
     */
    public static function refusedResult( $message, $holder, $backURL )
    {
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'message', $message );
        $tpl->setVariable( 'holder', $holder && self::canAccess( $holder, \eZUser::currentUser() ) ? self::info( $holder ) : false );
        $tpl->setVariable( 'back_url', $backURL );
        return array( 'content' => $tpl->fetch( 'design:content/job_refused.tpl' ),
                      'path' => array( array( 'text' => \ezpI18n::tr( 'design/admin/content/job', 'Content jobs' ),
                                              'url' => 'content/jobs' ),
                                       array( 'text' => \ezpI18n::tr( 'design/admin/content/job', 'Not started' ),
                                              'url' => false ) ) );
    }

    /**
     * May $user see and act on $job?
     *
     * @param \expContentJob $job
     * @param \eZUser $user
     * @return bool
     */
    public static function canAccess( $job, \eZUser $user )
    {
        if ( (int) $job->userID() === (int) $user->attribute( 'contentobject_id' ) && !$user->isAnonymous() )
            return true;
        if ( self::canSeeAll( $user ) )
            return true;
        $access = $user->hasAccessTo( 'content', $job->type() === 'copy' ? 'create' : 'remove' );
        return $access['accessWord'] === 'yes';
    }

    /**
     * Has $user the content/jobs policy (everybody's jobs)?
     *
     * @param \eZUser $user
     * @return bool
     */
    public static function canSeeAll( \eZUser $user )
    {
        $access = $user->hasAccessTo( 'content', 'jobs' );
        return $access['accessWord'] === 'yes';
    }

    /**
     * Resume is offered for a failed job and for a job whose worker died (a cancelled job is final).
     *
     * @param \expContentJob $job
     * @return bool
     */
    public static function canResume( $job )
    {
        // a queued job whose worker is still starting is not offered: only a failed one, or one whose worker
        // died (running without a worker, or queued longer than QueuedGrace)
        return $job->canResume() && ( $job->state() === 'failed' || $job->isStale() );
    }

    /**
     * The root node of a cancelled copy's partial copy, when it still exists; 0 otherwise.
     *
     * @param \expContentJob $job
     * @return int
     */
    public static function partialCopyNodeID( $job )
    {
        if ( $job->type() !== 'copy' || $job->state() !== 'cancelled' )
            return 0;
        $result = $job->result();
        $root = isset( $result['new_root_node_id'] ) ? (int) $result['new_root_node_id'] : 0;
        return ( $root && \eZContentObjectTreeNode::fetch( $root ) ) ? $root : 0;
    }

    /**
     * What the templates and the JSON status show of a job.
     *
     * @param \expContentJob $job
     * @return array
     */
    public static function info( $job )
    {
        $progress = $job->progress() + array( 'done' => 0, 'total' => 0, 'percent' => 0, 'batch' => 0,
                                              'batches' => 0, 'message' => '', 'phase' => '' );
        $params = $job->params();
        $result = (array) $job->result();
        $raw = array( 'name' => (string) $job->get( 'gui_name' ), 'target_name' => (string) $job->get( 'gui_target_name' ) );
        if ( $raw['name'] === '' )
            $raw['name'] = $job->description();
        $type = $job->type();
        $state = $job->state();

        $nodeIDs = self::nodeIDsOf( $type, $params );
        $firstID = $nodeIDs ? (int) $nodeIDs[0] : 0;
        $node = $firstID ? \eZContentObjectTreeNode::fetch( $firstID ) : null;
        $name = self::nodeName( $node, $firstID, $raw, 'name' );
        if ( count( $nodeIDs ) > 1 )
            $name = \ezpI18n::tr( 'design/admin/content/job', '%name and %count more', null,
                                  array( '%name' => $name, '%count' => count( $nodeIDs ) - 1 ) );

        $targetID = self::targetIDOf( $params );
        $target = $targetID ? \eZContentObjectTreeNode::fetch( $targetID ) : null;

        // where "done" leads: the new copy, or the parent of what was removed
        $redirectID = 0;
        if ( $type === 'copy' )
            $redirectID = !empty( $result['new_root_node_id'] ) ? (int) $result['new_root_node_id'] : $targetID;
        else
        {
            $redirectID = (int) $job->get( 'gui_return_node_id' );
            if ( !$redirectID && $node )
                $redirectID = (int) $node->attribute( 'parent_node_id' );
        }
        $redirectURL = $redirectID ? 'content/view/full/' . $redirectID : 'content/view/full/2';

        $owner = \eZContentObject::fetch( (int) $job->userID() );
        $batches = 0;
        $cancelRequested = $job->cancelRequested();

        // the words for the job's type; another registered type (JobTypes) is shown by its description
        $tr = function ( $text, $args = array() ) { return \ezpI18n::tr( 'design/admin/content/job', $text, null, $args ); };
        $trash = !empty( $params['move_to_trash'] );
        if ( $type === 'copy' )
            $labels = array( 'title' => $tr( 'Copying %name', array( '%name' => $name ) ),
                             'operation' => $tr( 'Copy subtree' ), 'operation_short' => $tr( 'Copy subtree' ),
                             'done_text' => $tr( 'The subtree has been copied.' ), 'open_text' => $tr( 'Open the copy' ) );
        else if ( $type === 'remove' )
            $labels = array( 'title' => $tr( 'Removing %name', array( '%name' => $name ) ),
                             'operation' => $trash ? $tr( 'Remove subtree, move to trash' ) : $tr( 'Remove subtree, delete' ),
                             'operation_short' => $trash ? $tr( 'Remove, to trash' ) : $tr( 'Remove, delete' ),
                             'done_text' => $tr( 'The subtree has been removed.' ), 'open_text' => $tr( 'Open the parent' ) );
        else if ( in_array( $type, array( 'move', 'hide', 'reveal', 'section', 'state' ), true ) )
        {
            $words = array( 'move' => array( 'Moving %name', 'Move subtree', 'The subtree has been moved.', 'Open it in its new place' ),
                            'hide' => array( 'Hiding %name', 'Hide subtree', 'The subtree is hidden.', 'Open the node' ),
                            'reveal' => array( 'Revealing %name', 'Reveal subtree', 'The subtree is visible again.', 'Open the node' ),
                            'section' => array( 'Assigning a section to %name', 'Assign section', 'The section is assigned.', 'Open the node' ),
                            'state' => array( 'Setting a state on %name', 'Set state', 'The state is set.', 'Open the node' ) );
            $w = $words[$type];
            $labels = array( 'title' => $tr( $w[0], array( '%name' => $name ) ), 'operation' => $tr( $w[1] ), 'operation_short' => $tr( $w[1] ),
                             'done_text' => $tr( $w[2] ), 'open_text' => $tr( $w[3] ) );
            // the node keeps its id: open it (in its new place after a move)
            if ( $firstID )
                $redirectURL = 'content/view/full/' . $firstID;
        }
        else
        {
            $description = $job->description() !== '' ? $job->description() : $type;
            $labels = array( 'title' => $description, 'operation' => $description, 'operation_short' => $description,
                             'done_text' => $tr( 'The job is done.' ), 'open_text' => $tr( 'Open the node' ) );
            if ( !$redirectID && $firstID )
                $redirectURL = 'content/view/full/' . $firstID;
        }

        return $labels + array(
            'id'                 => $job->id(),
            'type'               => $type,
            'state'              => $state,
            'phase'              => (string) $progress['phase'],
            'done'               => (int) $progress['done'],
            'total'              => (int) $progress['total'],
            'percent'            => max( 0, min( 100, (int) $progress['percent'] ) ),
            'batch'              => $batches ? $progress['batch'] . ' / ' . $batches : (string) $progress['batch'],
            'message'            => (string) $progress['message'],
            'error'              => (string) $job->error(),
            'created'            => (int) $job->created(),
            'started'            => (int) $job->started(),
            'finished'           => (int) $job->finished(),
            'just_finished'      => $state === 'done' && (int) $job->finished() >= time() - 30,
            'now'                => time(),
            'user_name'          => $owner ? $owner->attribute( 'name' ) : (string) $job->userID(),
            'name'               => $name,
            'node_id'            => $firstID,
            'node_exists'        => $node ? true : false,
            'target_node_id'     => $target ? $targetID : 0,
            'target_name'        => self::nodeName( $target, $targetID, $raw, 'target_name' ),
            'move_to_trash'      => !empty( $params['move_to_trash'] ),
            'redirect_url'       => $redirectURL,
            'partial_node_id'    => self::partialCopyNodeID( $job ),
            'cancel_requested'   => $cancelRequested,
            'can_cancel'         => $job->canCancel() && !$cancelRequested,
            'can_resume'         => self::canResume( $job ),
            'can_remove_partial' => self::partialCopyNodeID( $job ) > 0,
            'warnings'           => isset( $result['warnings'] ) ? array_values( (array) $result['warnings'] ) : array(),
        );
    }

    /**
     * A node's name, or what the job recorded, or "Node <id>".
     */
    protected static function nodeName( $node, $nodeID, array $raw, $key )
    {
        if ( $node )
            return $node->attribute( 'name' );
        if ( !empty( $raw[$key] ) && is_string( $raw[$key] ) )
            return $raw[$key];
        return $nodeID ? \ezpI18n::tr( 'design/admin/content/job', 'Node %id', null, array( '%id' => $nodeID ) ) : '';
    }

    /**
     * An eZURI path as the browser needs it (with the siteaccess prefix).
     */
    protected static function absoluteURL( $path )
    {
        \eZURI::transformURI( $path );
        return $path;
    }

    /**
     * Sends the job's whole log as a download (content/job/<id>?log=1) and ends the request.
     *
     * @param \expContentJob $job
     */
    public static function sendLog( $job )
    {
        header( 'Content-Type: text/plain; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="content-job-' . $job->id() . '.log"' );
        header( 'Cache-Control: no-store' );
        echo implode( "\n", $job->log( 1000000 ) ), "\n";
        \eZExecution::cleanExit();
    }

    /**
     * Sends a JSON answer and ends the request (eZExecution::cleanExit() is not caught on purpose: under
     * Velocity it ends the request by throwing).
     *
     * @param array $data
     * @param int $status
     */
    public static function sendJson( array $data, $status )
    {
        // as class/edit does: a status line, the headers, the body, cleanExit(). No ob_end_clean(): under
        // Velocity the output buffers are the server's own, and ending them leaves the request hanging.
        $protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        if ( $status === 404 )
            header( $protocol . ' 404 Not Found' );
        else if ( $status === 403 )
            header( $protocol . ' 403 Forbidden' );
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Cache-Control: no-store' );
        echo json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        \eZExecution::cleanExit();
    }
}

}

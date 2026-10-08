<?php
/**
 * File containing the expNotificationService class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The one place that knows how the notification system is run and looked at: the cronjob part, the
 * exp:notification:* commands, the status page and its "Run now" all go through it.
 *
 *  - run()            one pass: time event, every pending event through every handler, cleanup; with a lock,
 *                     a record of the run and an audit event
 *  - plan()           the same pass inside a transaction that is rolled back, with the mail only reported:
 *                     what would be sent, to how many, and nothing sent, marked or removed
 *  - status()         pending events, scheduled digest items, subscriptions, the last runs, problems
 *  - subscriptions()  the subtree subscriptions of a user (or of everyone), filtered and paged, with path,
 *                     class and the last change below the node
 *  - eventsReport() / cleanup() the events, and removing the old ones
 *
 * The runs are kept in var/<var dir>/notification/runs.jsonl (the newest 200), the lock is
 * var/<var dir>/notification/run.lock.
 */
class expNotificationService
{
    const KEEP_RUNS = 200;
    /** pending events older than this (seconds) with no run since are a problem */
    const STALE_SECONDS = 3600;

    /** @var resource|null the lock file of the run in progress */
    private static $lockHandle = null;

    public static function directory()
    {
        return eZSys::varDirectory() . '/notification';
    }

    private static function ensureDirectory()
    {
        $dir = self::directory();
        if ( !is_dir( $dir ) )
            eZDir::mkdir( $dir, false, true );
        return $dir;
    }

    // ------------------------------------------------------------------ small helpers

    /**
     * "90s", "15m", "12h", "30d", "2w", a plain number of days, or a date (2026-09-01) as a number of seconds back
     * from now; false when it is none of them.
     *
     * @return int|false
     */
    public static function parseAge( $spec )
    {
        $spec = trim( (string)$spec );
        if ( preg_match( '/^(\d+)\s*([smhdw]?)$/i', $spec, $m ) )
        {
            $units = array( 's' => 1, 'm' => 60, 'h' => 3600, 'd' => 86400, 'w' => 604800, '' => 86400 );
            return (int)$m[1] * $units[strtolower( $m[2] )];
        }
        if ( preg_match( '/^\d{4}-\d{2}-\d{2}/', $spec ) && ( $time = strtotime( $spec ) ) !== false )
            return max( 0, time() - $time );
        return false;
    }

    /** Hides most of an address: j***@example.com. */
    public static function maskAddress( $address )
    {
        $address = (string)$address;
        $at = strrpos( $address, '@' );
        if ( $at === false || $at < 1 )
            return $address === '' ? '' : substr( $address, 0, 1 ) . '***';
        return substr( $address, 0, 1 ) . '***' . substr( $address, $at );
    }

    private static function one( $sql )
    {
        $rows = eZDB::instance()->arrayQuery( $sql );
        return $rows ? (int)reset( $rows[0] ) : 0;
    }

    // ------------------------------------------------------------------ runs

    /**
     * The recorded runs, newest first.
     *
     * @return array each: time, source, dry, result, ms, events, removed, kept, failed, mails, recipients, error, user
     */
    public static function lastRuns( $limit = 10 )
    {
        $file = self::directory() . '/runs.jsonl';
        if ( !is_file( $file ) )
            return array();
        $lines = file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        $runs = array();
        foreach ( array_reverse( (array)$lines ) as $line )
        {
            $run = json_decode( $line, true );
            if ( is_array( $run ) )
                $runs[] = $run;
            if ( count( $runs ) >= $limit )
                break;
        }
        return $runs;
    }

    private static function record( array $run )
    {
        $dir = self::ensureDirectory();
        $file = $dir . '/runs.jsonl';
        $existed = is_file( $file );
        @file_put_contents( $file, json_encode( $run ) . "\n", FILE_APPEND | LOCK_EX );
        if ( !$existed )
            @chmod( $file, eZFile::fileMode( 0666 ) );
        $lines = @file( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        if ( is_array( $lines ) && count( $lines ) > self::KEEP_RUNS * 2 )
            @file_put_contents( $file, implode( "\n", array_slice( $lines, -self::KEEP_RUNS ) ) . "\n", LOCK_EX );
    }

    /**
     * Who holds the run lock, or false: array( pid, since ).
     */
    public static function runningNow()
    {
        $file = self::directory() . '/run.lock';
        if ( !is_file( $file ) )
            return false;
        $handle = @fopen( $file, 'c+' );
        if ( !$handle )
            return false;
        $free = flock( $handle, LOCK_EX | LOCK_NB );
        $info = false;
        if ( !$free )
        {
            $data = json_decode( (string)@file_get_contents( $file ), true );
            $info = is_array( $data ) ? $data : array( 'pid' => 0, 'since' => 0 );
        }
        else
        {
            flock( $handle, LOCK_UN );
        }
        fclose( $handle );
        return $info;
    }

    /** @var string why the last lock() failed: busy or the lock file cannot be opened */
    private static $lockError = '';

    private static function lock( $source )
    {
        $dir = self::ensureDirectory();
        $file = $dir . '/run.lock';
        $existed = is_file( $file );
        $handle = @fopen( $file, 'c+' );
        if ( !$handle )
        {
            self::$lockError = 'The lock file ' . $file . ' cannot be opened (permissions: the web server and the command line run as different users).';
            return false;
        }
        if ( !$existed )
            @chmod( $file, eZFile::fileMode( 0666 ) ); // the command line (root) and the web server (a site user) both take it
        if ( !flock( $handle, LOCK_EX | LOCK_NB ) )
        {
            fclose( $handle );
            self::$lockError = 'busy';
            return false;
        }
        ftruncate( $handle, 0 );
        fwrite( $handle, json_encode( array( 'pid' => getmypid(), 'since' => time(), 'source' => $source ) ) );
        fflush( $handle );
        self::$lockHandle = $handle;
        return true;
    }

    private static function unlock()
    {
        if ( self::$lockHandle )
        {
            ftruncate( self::$lockHandle, 0 );
            flock( self::$lockHandle, LOCK_UN );
            fclose( self::$lockHandle );
            self::$lockHandle = null;
        }
    }

    // ------------------------------------------------------------------ run and plan

    /**
     * One pass over the pending events.
     *
     * @param array $options source (cron, console, web, test), at (timestamp of the time event, default now),
     *        time_event (false: no time event, only what is pending), events (list of event ids: only those,
     *        and no time event), user (who started it)
     * @return array result (ok, busy, failed), events, removed, kept, failed, mails, recipients, ms, error
     */
    public static function run( array $options = array() )
    {
        $source = isset( $options['source'] ) ? (string)$options['source'] : 'console';
        $result = array( 'time' => time(), 'source' => $source, 'dry' => false, 'result' => 'ok', 'ms' => 0,
                         'events' => 0, 'removed' => 0, 'kept' => 0, 'failed' => 0, 'mails' => 0, 'recipients' => 0,
                         'send_failed' => 0, 'dropped' => 0, 'retried' => 0, 'error' => '', 'user' => isset( $options['user'] ) ? (string)$options['user'] : '' );
        if ( !self::lock( $source ) )
        {
            if ( self::$lockError === 'busy' )
            {
                $result['result'] = 'busy';
                $held = self::runningNow();
                $result['error'] = 'Another run holds the lock' . ( $held ? ' (process ' . (int)$held['pid'] . ')' : '' ) . '.';
            }
            else
            {
                $result['result'] = 'failed';
                $result['error'] = self::$lockError;
            }
            return $result;
        }
        $start = microtime( true );
        $mails = 0;
        $recipients = 0;
        eZMailNotificationTransport::observe( function ( $addresses, $subject, $body, $parameters, $sent = true ) use ( &$mails, &$recipients ) {
            if ( !$sent )
                return; // refused by the transport: not counted as sent
            ++$mails;
            $recipients += count( $addresses );
        }, false );
        try
        {
            $only = !empty( $options['events'] ) ? array_map( 'intval', (array)$options['events'] ) : null;
            if ( $only === null && ( !array_key_exists( 'time_event', $options ) || $options['time_event'] ) )
            {
                $params = isset( $options['at'] ) ? array( 'time' => (int)$options['at'] ) : array();
                $event = eZNotificationEvent::create( 'ezcurrenttime', $params );
                $event->store();
            }
            $stats = eZNotificationEventFilter::process( $only );
            unset( $stats['notes'] ); // short texts for the debug log; the record keeps the numbers
            $result = array_merge( $result, $stats );
        }
        catch ( Throwable $e )
        {
            $result['result'] = 'failed';
            $result['error'] = get_class( $e ) . ': ' . $e->getMessage();
        }
        eZMailNotificationTransport::observe( null );
        $result['mails'] = $mails;
        $result['recipients'] = $recipients;
        $result['ms'] = (int)round( ( microtime( true ) - $start ) * 1000 );
        self::record( $result );
        self::unlock();
        if ( $source !== 'cron' )
            self::audit( $result );
        return $result;
    }

    /**
     * What a run would do now: the same pass inside a transaction that is rolled back, the mail reported and
     * not sent. Refused (error set) where the database cannot roll back.
     *
     * @return array result (ok, failed), error, events, mails (list of subject, to (count), addresses (masked list)),
     *         removed, kept
     */
    public static function plan( array $options = array() )
    {
        $plan = array( 'result' => 'ok', 'error' => '', 'events' => 0, 'removed' => 0, 'kept' => 0, 'failed' => 0, 'mails' => array() );
        $db = eZDB::instance();
        if ( !self::canRollBack() )
        {
            $plan['result'] = 'failed';
            $plan['error'] = 'This database cannot roll back the notification tables (not InnoDB, or transactions are disabled), so a dry run is refused.';
            return $plan;
        }
        if ( !self::lock( 'dry-run' ) )
        {
            $plan['result'] = 'failed';
            $plan['error'] = self::$lockError === 'busy' ? 'A run is in progress.' : self::$lockError;
            return $plan;
        }
        $mails = array();
        eZMailNotificationTransport::observe( function ( $addresses, $subject ) use ( &$mails ) {
            $mails[] = array( 'subject' => (string)$subject, 'to' => count( $addresses ),
                              'addresses' => array_map( array( 'expNotificationService', 'maskAddress' ), $addresses ),
                              'raw' => $addresses );
        }, true );
        $db->begin();
        try
        {
            $only = !empty( $options['events'] ) ? array_map( 'intval', (array)$options['events'] ) : null;
            if ( $only === null && ( !array_key_exists( 'time_event', $options ) || $options['time_event'] ) )
            {
                $event = eZNotificationEvent::create( 'ezcurrenttime', isset( $options['at'] ) ? array( 'time' => (int)$options['at'] ) : array() );
                $event->store();
            }
            $plan = array_merge( $plan, eZNotificationEventFilter::process( $only ) );
        }
        catch ( Throwable $e )
        {
            $plan['result'] = 'failed';
            $plan['error'] = get_class( $e ) . ': ' . $e->getMessage();
        }
        $db->rollback();
        eZMailNotificationTransport::observe( null );
        self::unlock();
        $plan['mails'] = $mails;
        return $plan;
    }

    private static function canRollBack()
    {
        $db = eZDB::instance();
        if ( eZINI::instance()->variable( 'DatabaseSettings', 'Transactions' ) != 'enabled' )
            return false;
        if ( $db->databaseName() == 'mysql' )
        {
            $rows = $db->arrayQuery( "SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ( 'eznotificationevent', 'eznotificationcollection', 'eznotificationcollection_item' )" );
            if ( !$rows )
                return false;
            foreach ( $rows as $row )
                if ( strtolower( (string)$row['ENGINE'] ) !== 'innodb' )
                    return false;
        }
        return true;
    }

    private static function audit( array $run )
    {
        if ( !class_exists( 'expAuditHook' ) )
            return;
        expAuditHook::emit( 'system.command.run', function () use ( $run ) {
            return array( 'object' => array( 'type' => 'notification', 'id' => 'run' ),
                          'verb' => 'run',
                          'result' => $run['result'] === 'ok' ? 'success' : 'failed',
                          'after' => array( 'source' => $run['source'], 'events' => $run['events'], 'mails' => $run['mails'],
                                            'recipients' => $run['recipients'], 'failed' => $run['failed'], 'ms' => $run['ms'] ) );
        } );
    }

    // ------------------------------------------------------------------ status

    /**
     * Everything the status page and exp:notification:status show.
     *
     * @return array
     */
    public static function status()
    {
        $db = eZDB::instance();
        $now = time();
        $s = array( 'time' => $now );

        $s['pending'] = array();
        foreach ( $db->arrayQuery( 'SELECT event_type_string AS type, COUNT(*) AS n, MIN(id) AS first_id FROM eznotificationevent WHERE status = ' . eZNotificationEvent::STATUS_CREATED . ' GROUP BY event_type_string ORDER BY event_type_string' ) as $row )
            $s['pending'][$row['type']] = (int)$row['n'];
        $s['pending_total'] = array_sum( $s['pending'] );
        $s['oldest_pending'] = false;
        if ( $s['pending_total'] )
        {
            $first = eZNotificationEvent::fetchUnhandledList( array( 'offset' => 0, 'length' => 1 ) );
            if ( $first )
                $s['oldest_pending'] = $first[0]->createdAt();
        }
        $s['handled_kept'] = self::one( 'SELECT COUNT(*) FROM eznotificationevent WHERE status = ' . eZNotificationEvent::STATUS_HANDLED );
        $s['handled_orphans'] = eZNotificationEvent::cleanupHandled( true );
        // messages the transport did not take: their items have no send date, their event is handled
        $s['items_unsent'] = self::one( 'SELECT COUNT(*) FROM eznotificationcollection_item i, eznotificationevent e WHERE i.send_date = 0 AND e.id = i.event_id AND e.status = ' . eZNotificationEvent::STATUS_HANDLED );
        $s['retry_hours'] = eZNotificationEventFilter::retryHours();
        $s['collections'] = self::one( 'SELECT COUNT(*) FROM eznotificationcollection' );
        $s['items_total'] = self::one( 'SELECT COUNT(*) FROM eznotificationcollection_item' );
        $s['items_now'] = self::one( 'SELECT COUNT(*) FROM eznotificationcollection_item WHERE send_date = 0' );
        $s['items_digest'] = self::one( 'SELECT COUNT(*) FROM eznotificationcollection_item WHERE send_date <> 0' );
        $s['items_due'] = self::one( 'SELECT COUNT(*) FROM eznotificationcollection_item WHERE send_date <> 0 AND send_date < ' . $now );
        $s['subscriptions'] = self::one( 'SELECT COUNT(*) FROM ezsubtree_notification_rule' );
        $s['subscribers'] = self::one( 'SELECT COUNT(DISTINCT user_id) FROM ezsubtree_notification_rule' );
        $s['collab_rules'] = self::one( 'SELECT COUNT(*) FROM ezcollab_notification_rule' );
        $s['digest'] = array( 'daily' => 0, 'weekly' => 0, 'monthly' => 0 );
        foreach ( $db->arrayQuery( 'SELECT digest_type AS type, COUNT(*) AS n FROM ezgeneral_digest_user_settings WHERE receive_digest = 1 GROUP BY digest_type' ) as $row )
        {
            $key = array( eZGeneralDigestUserSettings::TYPE_DAILY => 'daily', eZGeneralDigestUserSettings::TYPE_WEEKLY => 'weekly',
                          eZGeneralDigestUserSettings::TYPE_MONTHLY => 'monthly' );
            if ( isset( $key[(int)$row['type']] ) )
                $s['digest'][$key[(int)$row['type']]] = (int)$row['n'];
        }
        $s['runs'] = self::lastRuns( 10 );
        $s['last_run'] = $s['runs'] ? $s['runs'][0] : false;
        $s['running'] = self::runningNow();
        $sent = array( 'mails' => 0, 'recipients' => 0, 'runs' => 0 );
        foreach ( $s['runs'] as $run )
        {
            if ( empty( $run['dry'] ) && $run['time'] > $now - 86400 )
            {
                $sent['mails'] += (int)$run['mails'];
                $sent['recipients'] += (int)$run['recipients'];
                ++$sent['runs'];
            }
        }
        $s['sent_24h'] = $sent;

        $ini = eZINI::instance();
        $s['transport'] = trim( $ini->variable( 'MailSettings', 'Transport' ) );
        $notificationINI = eZINI::instance( 'notification.ini' );
        $sender = $notificationINI->variable( 'MailSettings', 'EmailSender' );
        if ( !$sender )
            $sender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$sender )
            $sender = $ini->variable( 'MailSettings', 'AdminEmail' );
        $s['sender'] = $sender;
        $s['handlers'] = array_keys( eZNotificationEventFilter::availableHandlers() );
        $s['problems'] = self::problems( $s );
        return $s;
    }

    /**
     * What looks wrong, as array( level (error, warning, info), text ) rows.
     */
    public static function problems( $s = null )
    {
        if ( $s === null )
            $s = self::status();
        $now = time();
        $problems = array();
        $real = false;
        foreach ( $s['runs'] as $run )
            if ( empty( $run['dry'] ) && $run['result'] !== 'busy' )
            {
                $real = $run;
                break;
            }
        $lastRun = $real ? $real['time'] : false;
        if ( $s['pending_total'] > 0 )
        {
            if ( $lastRun === false )
                $problems[] = array( 'error', 'notification_never_ran', $s['pending_total'] );
            else if ( $lastRun < $now - self::STALE_SECONDS )
                $problems[] = array( 'error', 'notification_not_run_recently', $s['pending_total'], $lastRun );
        }
        else if ( $lastRun === false )
            $problems[] = array( 'warning', 'notification_no_run_recorded' );
        if ( $real && $real['result'] === 'failed' )
            $problems[] = array( 'error', 'notification_last_run_failed', $real['error'] );
        if ( $real && !empty( $real['failed'] ) )
            $problems[] = array( 'warning', 'notification_handler_failures', (int)$real['failed'] );
        if ( !empty( $s['items_unsent'] ) )
            $problems[] = array( 'error', 'notification_unsent', $s['items_unsent'], isset( $s['retry_hours'] ) ? $s['retry_hours'] : 72 );
        if ( $real && !empty( $real['send_failed'] ) )
            $problems[] = array( 'error', 'notification_send_failed', (int)$real['send_failed'] );
        if ( $real && !empty( $real['dropped'] ) )
            $problems[] = array( 'warning', 'notification_dropped', (int)$real['dropped'], isset( $s['retry_hours'] ) ? $s['retry_hours'] : 72 );
        if ( $s['items_due'] > 0 )
            $problems[] = array( 'warning', 'notification_digest_overdue', $s['items_due'] );
        if ( $s['handled_orphans'] > 0 )
            $problems[] = array( 'info', 'notification_handled_orphans', $s['handled_orphans'] );
        $orphanRules = self::one( 'SELECT COUNT(*) FROM ezsubtree_notification_rule r LEFT JOIN ezcontentobject_tree t ON t.node_id = r.node_id WHERE t.node_id IS NULL' );
        if ( $orphanRules > 0 )
            $problems[] = array( 'warning', 'notification_orphan_rules', $orphanRules );
        $strayRules = self::one( 'SELECT COUNT(*) FROM ezsubtree_notification_rule r LEFT JOIN ezuser u ON u.contentobject_id = r.user_id WHERE u.contentobject_id IS NULL' );
        if ( $strayRules > 0 )
            $problems[] = array( 'warning', 'notification_rules_without_user', $strayRules );
        if ( $s['transport'] === 'file' )
            $problems[] = array( 'info', 'notification_transport_file' );
        if ( $s['sender'] === '' || !eZMail::validate( $s['sender'] ) )
            $problems[] = array( 'error', 'notification_no_sender' );
        if ( !in_array( 'ezsubtree', $s['handlers'], true ) )
            $problems[] = array( 'warning', 'notification_no_subtree_handler' );
        return $problems;
    }

    /**
     * The sentence of a problem row (see problems()).
     */
    public static function problemText( array $p )
    {
        $d = 'kernel/notification';
        switch ( $p[1] )
        {
            case 'notification_never_ran':
                return ezpI18n::tr( $d, '%count events wait and no run of the notification cronjob is recorded. Add the cronjob part "frequent" to the crontab, or run exp:notification:run.', null, array( '%count' => $p[2] ) );
            case 'notification_not_run_recently':
                return ezpI18n::tr( $d, '%count events wait and the last run was more than an hour ago (%time). Is the notification cronjob running?', null, array( '%count' => $p[2], '%time' => date( 'Y-m-d H:i', $p[3] ) ) );
            case 'notification_no_run_recorded':
                return ezpI18n::tr( $d, 'No run is recorded yet. The notification cronjob (part "frequent") sends the notifications.' );
            case 'notification_last_run_failed':
                return ezpI18n::tr( $d, 'The last run failed: %error', null, array( '%error' => $p[2] ) );
            case 'notification_handler_failures':
                return ezpI18n::tr( $d, 'A handler failed on %count events in the last run; see the debug log.', null, array( '%count' => $p[2] ) );
            case 'notification_unsent':
                return ezpI18n::tr( $d, '%count messages could not be handed to the mail transport and wait for the next run; each is given up after %hours hours.', null, array( '%count' => $p[2], '%hours' => $p[3] ) );
            case 'notification_send_failed':
                return ezpI18n::tr( $d, 'The mail transport refused %count messages in the last run. Check the mail server and site.ini MailSettings.', null, array( '%count' => $p[2] ) );
            case 'notification_dropped':
                return ezpI18n::tr( $d, '%count messages were given up in the last run: older than %hours hours, or for an address that cannot be mailed.', null, array( '%count' => $p[2], '%hours' => $p[3] ) );
            case 'notification_digest_overdue':
                return ezpI18n::tr( $d, '%count digest messages are overdue; a run sends them.', null, array( '%count' => $p[2] ) );
            case 'notification_handled_orphans':
                return ezpI18n::tr( $d, '%count handled events have nothing left to send; Remove old events clears them.', null, array( '%count' => $p[2] ) );
            case 'notification_orphan_rules':
                return ezpI18n::tr( $d, '%count subscriptions point to content that no longer exists.', null, array( '%count' => $p[2] ) );
            case 'notification_rules_without_user':
                return ezpI18n::tr( $d, '%count subscriptions belong to users that no longer exist.', null, array( '%count' => $p[2] ) );
            case 'notification_transport_file':
                return ezpI18n::tr( $d, 'Mail is written to files, not sent (site.ini MailSettings Transport=file).' );
            case 'notification_no_sender':
                return ezpI18n::tr( $d, 'There is no valid sender address: set EmailSender in notification.ini or site.ini.' );
            case 'notification_no_subtree_handler':
                return ezpI18n::tr( $d, 'The subtree handler is not available, so no one is notified about published content.' );
        }
        return (string)$p[1];
    }

    // ------------------------------------------------------------------ subscriptions

    /**
     * The subtree subscriptions of one user, or of everyone, with what the lists show.
     *
     * @param int|null $userID the user's content object id, null for everyone
     * @param array $filter q (part of the name), class (class identifier), missing (true: only nodes that are gone), ids (rule ids)
     * @return array total, rows (id, user_id, login, node_id, name, path (array of names), class_identifier, class_name,
     *         section_id, last_change (timestamp or false), missing (bool), use_digest)
     */
    public static function subscriptions( $userID = null, array $filter = array(), $offset = 0, $limit = 25 )
    {
        $db = eZDB::instance();
        $where = array();
        if ( $userID !== null )
            $where[] = 'r.user_id = ' . (int)$userID;
        if ( !empty( $filter['q'] ) )
            $where[] = "o.name LIKE '%" . $db->escapeString( addcslashes( (string)$filter['q'], '%_' ) ) . "%'";
        if ( !empty( $filter['class'] ) )
            $where[] = "c.identifier = '" . $db->escapeString( (string)$filter['class'] ) . "'";
        if ( !empty( $filter['missing'] ) )
            $where[] = 't.node_id IS NULL';
        if ( !empty( $filter['ids'] ) )
            $where[] = 'r.id IN ( ' . implode( ',', array_map( 'intval', (array)$filter['ids'] ) ) . ' )';
        $whereSQL = $where ? 'WHERE ' . implode( ' AND ', $where ) : '';
        $from = 'FROM ezsubtree_notification_rule r
                 LEFT JOIN ezcontentobject_tree t ON t.node_id = r.node_id
                 LEFT JOIN ezcontentobject o ON o.id = t.contentobject_id
                 LEFT JOIN ezcontentclass c ON c.id = o.contentclass_id AND c.version = 0
                 LEFT JOIN ezuser u ON u.contentobject_id = r.user_id';
        $total = self::one( "SELECT COUNT(*) $from $whereSQL" );
        $rows = $db->arrayQuery( "SELECT r.id, r.user_id, r.node_id, r.use_digest, t.path_string, t.contentobject_id, o.name AS object_name,
                                         o.section_id, o.contentclass_id, c.identifier AS class_identifier, u.login
                                  $from $whereSQL ORDER BY r.id",
                                 array( 'offset' => max( 0, (int)$offset ), 'limit' => max( 1, (int)$limit ) ) );
        $nodeIDs = array();
        foreach ( $rows as $row )
            if ( $row['path_string'] )
                foreach ( explode( '/', trim( $row['path_string'], '/' ) ) as $id )
                    if ( $id !== '' )
                        $nodeIDs[(int)$id] = true;
        $names = array();
        if ( $nodeIDs )
        {
            $in = $db->generateSQLINStatement( array_keys( $nodeIDs ), 't.node_id', false, false, 'int' );
            foreach ( $db->arrayQuery( "SELECT t.node_id, o.name FROM ezcontentobject_tree t, ezcontentobject o WHERE o.id = t.contentobject_id AND $in" ) as $n )
                $names[(int)$n['node_id']] = $n['name'];
        }
        $out = array();
        foreach ( $rows as $row )
        {
            $missing = $row['path_string'] === null || $row['path_string'] === '';
            $path = array();
            if ( !$missing )
            {
                $ids = array_filter( explode( '/', trim( $row['path_string'], '/' ) ), 'strlen' );
                array_pop( $ids );
                array_shift( $ids ); // the tree root has no name worth showing
                foreach ( $ids as $id )
                    $path[] = isset( $names[(int)$id] ) ? $names[(int)$id] : '#' . (int)$id;
            }
            $last = false;
            if ( !$missing )
            {
                $like = $db->escapeString( $row['path_string'] ) . '%';
                $v = $db->arrayQuery( "SELECT MAX(o.modified) AS m FROM ezcontentobject_tree t, ezcontentobject o WHERE o.id = t.contentobject_id AND t.path_string LIKE '$like'" );
                $last = $v && (int)$v[0]['m'] > 0 ? (int)$v[0]['m'] : false;
            }
            $className = '';
            if ( !$missing && $row['contentclass_id'] )
            {
                $class = eZContentClass::fetch( (int)$row['contentclass_id'] );
                $className = $class ? $class->attribute( 'name' ) : (string)$row['class_identifier'];
            }
            $out[] = array( 'id' => (int)$row['id'], 'user_id' => (int)$row['user_id'], 'login' => (string)$row['login'],
                            'node_id' => (int)$row['node_id'], 'name' => $missing ? '' : (string)$row['object_name'], 'path' => $path,
                            'class_identifier' => (string)$row['class_identifier'], 'class_name' => $className,
                            'section_id' => (int)$row['section_id'], 'last_change' => $last, 'missing' => $missing,
                            'use_digest' => (int)$row['use_digest'] );
        }
        return array( 'total' => $total, 'rows' => $out );
    }

    /**
     * Classes of the nodes a user subscribed to, for the filter: array( identifier => name ).
     */
    public static function subscribedClasses( $userID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( 'SELECT DISTINCT c.identifier, o.contentclass_id
                                  FROM ezsubtree_notification_rule r, ezcontentobject_tree t, ezcontentobject o, ezcontentclass c
                                  WHERE r.user_id = ' . (int)$userID . ' AND t.node_id = r.node_id AND o.id = t.contentobject_id
                                        AND c.id = o.contentclass_id AND c.version = 0' );
        $classes = array();
        foreach ( $rows as $row )
        {
            $class = eZContentClass::fetch( (int)$row['contentclass_id'] );
            $classes[$row['identifier']] = $class ? $class->attribute( 'name' ) : $row['identifier'];
        }
        asort( $classes );
        return $classes;
    }

    // ------------------------------------------------------------------ events

    /**
     * The events, for exp:notification:events.
     *
     * @return array id, status (pending/handled), type, created (timestamp or false), items (collection items waiting)
     */
    public static function eventsReport( $status = null, $limit = 50 )
    {
        $conds = $status === null ? null : array( 'status' => (int)$status );
        $events = eZPersistentObject::fetchObjectList( eZNotificationEvent::definition(), null, $conds, array( 'id' => 'desc' ),
                                                       array( 'offset' => 0, 'length' => max( 1, (int)$limit ) ), true );
        $out = array();
        foreach ( $events as $event )
        {
            $out[] = array( 'id' => (int)$event->attribute( 'id' ),
                            'status' => (int)$event->attribute( 'status' ) == eZNotificationEvent::STATUS_HANDLED ? 'handled' : 'pending',
                            'type' => $event->attribute( 'event_type_string' ),
                            'created' => $event->createdAt(),
                            'items' => eZNotificationCollectionItem::fetchCountForEvent( $event->attribute( 'id' ) ) );
        }
        return $out;
    }

    /**
     * Removes events: the handled ones nothing waits for, and those older than $olderThan seconds.
     *
     * @param int|null $olderThan seconds
     * @param int|null $status eZNotificationEvent::STATUS_* the age rule applies to (null: both)
     * @return array handled (orphans removed), removed, kept, unknown
     */
    public static function cleanup( $olderThan = null, $status = null, $unknown = false, $dryRun = false )
    {
        $result = array( 'handled' => 0, 'removed' => 0, 'kept' => 0, 'unknown' => 0 );
        $result['handled'] = eZNotificationEvent::cleanupHandled( $dryRun );
        if ( $olderThan !== null )
        {
            $r = eZNotificationEvent::removeOlderThan( time() - (int)$olderThan, $status, $unknown, $dryRun );
            $result['removed'] = $r['removed'];
            $result['kept'] = $r['kept'];
            $result['unknown'] = $r['unknown'];
        }
        if ( !$dryRun )
            eZNotificationCollection::removeEmpty();
        return $result;
    }
}

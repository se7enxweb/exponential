<?php
/**
 * The audit/dashboard view (doc/bc/6.0/audit.md, "The console", "Views"): the summary that binds the audit's views
 * together, in cards that each link into their detailed view: health (audit on or off, the chain of each channel
 * as last verified, the signing key's age; "Verify now" for audit/manage), today and the last 7 days per channel
 * and family, security (failed logins by address and login, role grants, permission refusals), alerts (recent
 * firings, the mail recipients), activity (top actors and objects today, the latest warnings), operations
 * (archives, sinks, the index, the cronjob part) and quick links.
 *
 * Fast by design: it never walks the files (chain states are the stored ones, expAuditVerifier::loadState()), the
 * figures come from the index, and the index itself is brought up to date by a short bounded run. Policy
 * audit/read with its Channel limitation; manage actions only with audit/manage. Recorded as system.audit.read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Audit
{

class Dashboard extends \Exponential\Runnable\ModuleView
{
    /** The channels in their fixed colour order (as the charts) */
    const CHANNEL_ORDER = array( 'content', 'access', 'system', 'commerce', 'read' );

    /** Seconds after which the cronjob part's last run is called stale */
    const CRON_STALE = 3600;

    /** Days after which a signing key gets a rotation hint */
    const KEY_ROTATE_DAYS = 365;

    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
            return $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        $allowed = \expAuditConsole::allowedChannels();
        $manage = \expAuditConsole::canManage();
        if ( \eZHTTPTool::instance()->hasPostVariable( 'AuditVerifyNowButton' ) )
        {
            if ( !$manage )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $form = \expAuditReauth::gate( $Module, 'AuditVerifyNowButton', 'audit/dashboard', \ezpI18n::tr( 'design/admin/audit', 'Verify now' ) );
            if ( $form !== null )
                return $form;
            if ( class_exists( 'expAuditGuard' ) && !\expAuditGuard::allows( 'system.audit.verify' ) )
                return \expAuditGuard::refusedResult( 'audit', 'dashboard' );
            \expAuditConsole::chainStates( $allowed, true );
            return $Module->redirectTo( '/audit/dashboard' );
        }

        $start = microtime( true );
        $config = \expAuditConfig::get();
        // a short index run (the cronjob part does the rest), so the page stays fast
        $indexRun = \expAuditConsole::refreshIndex( 0.15 );
        $usable = \expAuditConsole::indexUsable();
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'can_manage', $manage );
        $tpl->setVariable( 'audit_enabled', $config['enabled'] );
        $tpl->setVariable( 'index_usable', $usable );
        $tpl->setVariable( 'health', $this->health( $allowed, $config ) );
        $tpl->setVariable( 'limited_to', $allowed === null ? array() : $allowed );
        if ( $usable )
        {
            $q = new \expAuditQuery();
            // the 7-day groupings scan many rows: kept for a minute per Channel limitation
            $tpl->setVariable( 'volume', $this->cached( 'volume', $allowed, function () use ( $q, $allowed ) { return $this->volume( $q, $allowed ); } ) );
            $tpl->setVariable( 'security', $this->security( $q, $allowed ) );
            $tpl->setVariable( 'alerts', $this->alerts( $q, $allowed ) );
            $tpl->setVariable( 'activity', $this->cached( 'activity', $allowed, function () use ( $q, $allowed ) { return $this->activity( $q, $allowed ); } ) );
        }
        $tpl->setVariable( 'operations', $this->operations( $config, $manage, $usable, $indexRun ) );
        $tpl->setVariable( 'ms', (int)round( ( microtime( true ) - $start ) * 1000 ) );

        \expAuditConsole::recordRead( 'audit/dashboard', array(), null );
        return \expAuditConsole::result( $tpl->fetch( 'design:audit/dashboard.tpl' ), \ezpI18n::tr( 'design/admin/audit', 'Dashboard' ) );
    }

    /** Seconds the 7-day figures are kept */
    const CACHE_SECONDS = 60;

    /**
     * A section's figures from a short-lived cache file (var/<site>/cache/audit/), computed when missing or older
     * than CACHE_SECONDS; keyed by the section, the Channel limitation and the day.
     *
     * @param string $section
     * @param string[]|null $allowed
     * @param callable $compute
     * @return array
     */
    protected function cached( $section, $allowed, $compute )
    {
        $dir = \eZSys::cacheDirectory() . '/audit';
        $file = $dir . '/dashboard-' . $section . '-' . md5( json_encode( $allowed ) . date( 'Ymd' ) ) . '.php';
        clearstatcache( true, $file );
        if ( is_file( $file ) && filemtime( $file ) > time() - self::CACHE_SECONDS )
        {
            $data = @include $file;
            if ( is_array( $data ) )
                return $data;
        }
        $data = $compute();
        // owned like the cache directory, also when written by a process running as root
        if ( is_dir( $dir ) || ( @mkdir( $dir, \eZDir::dirMode( 0770 ), true ) && ( \expAuditWriter::ownLikeParent( $dir, 0770 ) || true ) ) )
        {
            $tmp = $file . '.' . getmypid() . '.tmp';
            if ( @file_put_contents( $tmp, '<?php return ' . var_export( $data, true ) . ';' ) !== false )
            {
                @rename( $tmp, $file );
                \expAuditWriter::ownLikeParent( $file, 0660 );
            }
        }
        return $data;
    }

    /** @return int epoch ms of the start of today, local time */
    protected static function todayMs()
    {
        return mktime( 0, 0, 0 ) * 1000;
    }

    /**
     * Health: audit on or off, the chain of each channel as last verified, the signing key's age.
     */
    protected function health( $allowed, array $config )
    {
        $chains = \expAuditConsole::chainStates( $allowed, false );
        $key = array( 'id' => '', 'fingerprint' => '', 'age_days' => null, 'rotate' => false );
        try
        {
            $keys = new \expAuditKeys( $config );
            $id = $keys->activeKeyId();
            if ( $id )
            {
                $raw = $keys->signingKey( $id );
                $key['id'] = $id;
                $key['fingerprint'] = $raw ? \expAuditKeys::fingerprint( $raw ) : '';
                if ( preg_match( '/^k\d+-(\d{4})(\d{2})(\d{2})-/', $id, $m ) )
                {
                    $key['age_days'] = (int)floor( ( time() - gmmktime( 0, 0, 0, (int)$m[2], (int)$m[3], (int)$m[1] ) ) / 86400 );
                    $key['rotate'] = $key['age_days'] > self::KEY_ROTATE_DAYS;
                }
            }
        }
        catch ( \Throwable $e )
        {
        }
        $worst = 'intact';
        foreach ( $chains as $c )
            if ( $c['result'] === 'broken' )
                $worst = 'broken';
            elseif ( in_array( $c['result'], array( 'unchecked', 'repaired' ), true ) && $worst === 'intact' )
                $worst = $c['result'];
        return array( 'chains' => $chains, 'key' => $key, 'worst' => $chains ? $worst : 'empty' );
    }

    /**
     * Today and the last 7 days: per channel and family, per day, refused and failed.
     */
    protected function volume( \expAuditQuery $q, $allowed )
    {
        $today = array( 'from_ms' => self::todayMs() );
        $week = array( 'from_ms' => self::todayMs() - 6 * 86400000 );
        $offsetMs = (int)date( 'Z' ) * 1000;
        $perDay = $q->perDay( $week, $allowed, 'channel', $offsetMs );
        $days = array();
        $max = 1;
        for ( $i = 6; $i >= 0; $i-- )
        {
            $d = date( 'Y-m-d', mktime( 0, 0, 0, (int)date( 'n' ), (int)date( 'j' ) - $i ) );
            $n = isset( $perDay[$d] ) ? array_sum( $perDay[$d] ) : 0;
            $max = max( $max, $n );
            $days[] = array( 'day' => $d, 'label' => date( 'D', strtotime( $d ) ), 'n' => $n );
        }
        foreach ( $days as $i => $d )
            $days[$i]['pct'] = round( 100 * $d['n'] / $max, 1 );

        $channels = array();
        $todayBy = array_column( $q->groupCount( array( 'channel' ), $today, $allowed, 20 ), 'n', 'channel' );
        $weekBy = array_column( $q->groupCount( array( 'channel' ), $week, $allowed, 20 ), 'n', 'channel' );
        $names = array_unique( array_merge( array_keys( $weekBy ), array_keys( $todayBy ) ) );
        usort( $names, function ( $a, $b ) {
            $ia = array_search( $a, self::CHANNEL_ORDER, true );
            $ib = array_search( $b, self::CHANNEL_ORDER, true );
            return array( $ia === false ? 99 : $ia, $a ) <=> array( $ib === false ? 99 : $ib, $b );
        } );
        foreach ( $names as $c )
        {
            $slot = array_search( $c, self::CHANNEL_ORDER, true );
            $channels[] = array( 'channel' => $c, 'today' => isset( $todayBy[$c] ) ? (int)$todayBy[$c] : 0,
                                 'week' => isset( $weekBy[$c] ) ? (int)$weekBy[$c] : 0, 'slot' => $slot === false ? 6 : $slot + 1,
                                 'url' => \expAuditConsole::url( 'audit/console', array( 'channel' => $c ) ) );
        }
        $families = array();
        foreach ( $q->groupCount( array( 'domain_name' ), $week, $allowed, 10 ) as $r )
            $families[] = array( 'domain' => $r['domain_name'], 'n' => (int)$r['n'],
                                 'url' => \expAuditConsole::url( 'audit/console', array( 'name' => $r['domain_name'] . '.*' ) ) );
        $results = function ( array $f ) use ( $q, $allowed ) {
            $by = array_column( $q->groupCount( array( 'result' ), $f, $allowed, 5 ), 'n', 'result' );
            return array( 'refused' => isset( $by['refused'] ) ? (int)$by['refused'] : 0, 'failed' => isset( $by['failed'] ) ? (int)$by['failed'] : 0,
                          'total' => (int)array_sum( $by ) );
        };
        return array( 'days' => $days, 'channels' => $channels, 'families' => $families,
                      'today' => $results( $today ), 'week' => $results( $week ),
                      'refused_url' => \expAuditConsole::url( 'audit/console', array( 'result' => 'refused' ) ),
                      'failed_url' => \expAuditConsole::url( 'audit/console', array( 'result' => 'failed' ) ),
                      'charts_url' => \expAuditConsole::url( 'audit/charts', array(), array( 'days' => 7 ) ) );
    }

    /**
     * Security: failed logins in the last 24 hours by address and login, role grants, permission refusals.
     */
    protected function security( \expAuditQuery $q, $allowed )
    {
        $day = array( 'from_ms' => ( time() - 86400 ) * 1000 );
        $week = array( 'from_ms' => self::todayMs() - 6 * 86400000 );
        $failed = $day + array( 'name' => 'access.session.login.failed' );
        $out = array(
            'failed_logins' => (int)$q->count( $failed, $allowed ),
            'failed_url' => \expAuditConsole::url( 'audit/console', array( 'name' => 'access.session.login.failed' ) ),
            'by_ip' => array(), 'by_login' => array(), 'grants' => array(), 'refusals' => 0, 'refused_views' => array(),
        );
        foreach ( $q->groupCount( array( 'ip' ), $failed, $allowed, 5 ) as $r )
            $out['by_ip'][] = array( 'value' => (string)$r['ip'], 'n' => (int)$r['n'],
                                     'url' => \expAuditConsole::url( 'audit/console', array( 'name' => 'access.session.login.failed', 'ip' => (string)$r['ip'] ) ) );
        // the login that was typed: the account's name when it exists, else its hashed form (privacy rules)
        foreach ( $q->groupCount( array( 'object_name' ), $failed, $allowed, 5 ) as $r )
            $out['by_login'][] = array( 'value' => (string)$r['object_name'], 'n' => (int)$r['n'],
                                        'url' => \expAuditConsole::url( 'audit/console', array( 'name' => 'access.session.login.failed', 'q' => (string)$r['object_name'] ) ) );
        foreach ( (array)$q->fetch( $week + array( 'name' => 'access.role.assign' ), $allowed, 0, 5 ) as $r )
            $out['grants'][] = \expAuditConsole::view( $r );
        $out['grants_total'] = (int)$q->count( $week + array( 'name' => 'access.role.assign' ), $allowed );
        $out['grants_url'] = \expAuditConsole::url( 'audit/console', array( 'name' => 'access.role.assign' ) );
        $refused = $day + array( 'name' => 'access.permission.refused' );
        $out['refusals'] = (int)$q->count( $refused, $allowed );
        $out['refusals_url'] = \expAuditConsole::url( 'audit/console', array( 'name' => 'access.permission.refused' ) );
        foreach ( $q->groupCount( array( 'object_id' ), $refused, $allowed, 5 ) as $r )
            $out['refused_views'][] = array( 'value' => (string)$r['object_id'], 'n' => (int)$r['n'] );
        return $out;
    }

    /**
     * Alerts: the latest firings, the rules in use and who gets the alert mail.
     */
    protected function alerts( \expAuditQuery $q, $allowed )
    {
        $out = array( 'recent' => array(), 'day' => 0, 'rules' => 0, 'problems' => 0, 'recipients' => array(),
                      'enabled' => true );
        foreach ( (array)$q->fetch( array( 'name' => 'system.audit.alert' ), $allowed, 0, 5 ) as $r )
        {
            $v = \expAuditConsole::view( $r );
            $rec = json_decode( (string)$r['record'], true );
            $v['rule'] = isset( $rec['after']['rule'] ) ? (string)$rec['after']['rule'] : '';
            $v['count'] = isset( $rec['after']['count'] ) ? (int)$rec['after']['count'] : 0;
            $out['recent'][] = $v;
        }
        $out['day'] = (int)$q->count( array( 'name' => 'system.audit.alert', 'from_ms' => ( time() - 86400 ) * 1000 ), $allowed );
        foreach ( Alerts::rules() as $rule )
        {
            $out['rules']++;
            if ( $rule['problem'] )
                $out['problems']++;
        }
        if ( class_exists( 'expAuditAlertEvaluator' ) && method_exists( 'expAuditAlertEvaluator', 'isEnabled' ) )
            $out['enabled'] = (bool)\expAuditAlertEvaluator::isEnabled();
        if ( class_exists( 'expAuditMailRecipients' ) && method_exists( 'expAuditMailRecipients', 'overview' ) )
        {
            try
            {
                foreach ( \expAuditMailRecipients::overview() as $name => $o )
                    $out['recipients'][] = array( 'name' => (string)$name,
                                                  'count' => isset( $o['addresses'] ) ? count( (array)$o['addresses'] ) : 0,
                                                  'problems' => isset( $o['problems'] ) ? count( (array)$o['problems'] ) : 0 );
            }
            catch ( \Throwable $e )
            {
            }
        }
        return $out;
    }

    /**
     * Activity: top actors and objects today, the latest warnings and worse.
     */
    protected function activity( \expAuditQuery $q, $allowed )
    {
        $today = array( 'from_ms' => self::todayMs() );
        $out = array( 'actors' => array(), 'objects' => array(), 'latest' => array() );
        foreach ( $q->groupCount( array( 'login' ), $today, $allowed, 6 ) as $r )
            $out['actors'][] = array( 'value' => (string)$r['login'], 'n' => (int)$r['n'],
                                      'url' => (string)$r['login'] !== '' ? \expAuditConsole::url( 'audit/console', array( 'login' => (string)$r['login'] ) ) : '' );
        foreach ( $q->groupCount( array( 'object_type', 'object_id' ), $today + array(), $allowed, 6 ) as $r )
        {
            if ( (string)$r['object_type'] === '' )
                continue;
            $out['objects'][] = array( 'value' => $r['object_type'] . ' ' . $r['object_id'], 'n' => (int)$r['n'],
                                       'url' => \expAuditConsole::url( 'audit/console', array( 'object' => $r['object_type'] . ( (string)$r['object_id'] !== '' ? ':' . $r['object_id'] : '' ) ) ) );
        }
        foreach ( (array)$q->fetch( array( 'severity' => 4 ), $allowed, 0, 10 ) as $r )
            $out['latest'][] = \expAuditConsole::view( $r );
        $out['latest_url'] = \expAuditConsole::url( 'audit/console', array( 'severity' => 'warning' ) );
        return $out;
    }

    /**
     * Operations: archives, sinks, the index, the cronjob part.
     */
    protected function operations( array $config, $manage, $usable, $indexRun )
    {
        $out = array( 'archives' => array(), 'sinks' => array(), 'index' => array(), 'cron' => array() );
        if ( class_exists( 'expAuditArchiver' ) && method_exists( 'expAuditArchiver', 'status' ) )
        {
            try
            {
                $archiver = new \expAuditArchiver( $config );
                foreach ( $archiver->status() as $c => $s )
                {
                    $manifests = array_keys( $archiver->manifests( $c ) );
                    $out['archives'][] = array( 'channel' => $c, 'live_files' => (int)$s['live_files'], 'oldest_live' => (string)$s['oldest_live'],
                                                'last_archive' => $manifests ? end( $manifests ) : '', 'archives' => (int)$s['archives'],
                                                'live_days' => (int)$s['live_days'], 'archive_days' => (int)$s['archive_days'],
                                                'due' => $s['oldest_live'] && strtotime( $s['oldest_live'] ) < time() - ( (int)$s['live_days'] + 1 ) * 86400 );
                }
            }
            catch ( \Throwable $e )
            {
            }
        }
        if ( class_exists( 'expAuditSinkRegistry' ) && method_exists( 'expAuditSinkRegistry', 'status' ) )
        {
            try
            {
                foreach ( \expAuditSinkRegistry::status() as $name => $s )
                    $out['sinks'][] = array( 'name' => $name, 'problem' => (string)$s['problem'], 'spooled' => (int)$s['spooled'],
                                             'last_delivery' => $s['last_delivery'] ? date( 'Y-m-d H:i', strtotime( $s['last_delivery'] ) ) : '',
                                             'last_error' => (string)$s['last_error'] );
            }
            catch ( \Throwable $e )
            {
            }
        }
        $index = array( 'usable' => $usable, 'rows' => 0, 'lag' => 0, 'last_reindex' => '', 'fulltext' => '' );
        if ( $usable )
        {
            $q = new \expAuditQuery();
            $index['rows'] = (int)$q->count( array(), null );
            $index['lag'] = ( new \expAuditIndexer() )->lag()['bytes'];
            $index['fulltext'] = $q->fullTextKind();
            $last = $q->fetch( array( 'name' => 'system.audit.reindex' ), null, 0, 1, 'time_ms' );
            $index['last_reindex'] = $last ? date( 'Y-m-d H:i', (int)( $last[0]['time_ms'] / 1000 ) ) : '';
            // the cronjob part's last run: its system.cronjob.run record, else when its lock was taken
            $cron = $q->fetch( array( 'name' => 'system.cronjob.run', 'q' => 'audit' ), null, 0, 1, 'time_ms' );
            if ( $cron )
                $out['cron']['last'] = (int)( $cron[0]['time_ms'] / 1000 );
        }
        if ( empty( $out['cron']['last'] ) )
        {
            $m = @filemtime( $config['logDir'] . '/.cron-daily' );
            $out['cron']['last'] = $m ? (int)$m : 0;
            $out['cron']['source'] = 'daily';
        }
        $out['cron']['text'] = $out['cron']['last'] ? date( 'Y-m-d H:i', $out['cron']['last'] ) : '';
        $out['cron']['stale'] = !$out['cron']['last'] || time() - $out['cron']['last'] > self::CRON_STALE;
        $out['cron']['daily'] = trim( (string)@file_get_contents( $config['logDir'] . '/.cron-daily' ) );
        $out['index'] = $index;
        return $out;
    }
}

}

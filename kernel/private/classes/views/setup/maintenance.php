<?php
/**
 * The code of kernel/setup/maintenance.php, moved into a class (#207 stage 1). The file kernel/setup/maintenance.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of kernel/setup/maintenance.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 *
 */

namespace Exponential\View\Kernel\Setup
{

class Maintenance extends \Exponential\Runnable\ModuleView
{
    /** The longest window the page accepts, in minutes (a week) */
    const MAX_MINUTES = 10080;

    /** The durations offered as one-click choices, in minutes */
    const PRESETS = array( 15, 30, 60, 120, 240 );

    /** Changes of the maintenance mode shown from the audit */
    const HISTORY = 8;

    /** expMaintenance::page()'s own texts for a manual window, which the live preview falls back to */
    const DEFAULT_MESSAGE = 'We are working on the site and will be back shortly.';
    const DEFAULT_UNTIL = 'Please try again in a few minutes.';

    /** Stand-ins the live preview replaces with what is typed */
    const MESSAGE_SLOT = '@@EXP-MAINTENANCE-MESSAGE@@';
    const UNTIL_SLOT = '@@EXP-MAINTENANCE-UNTIL@@';

    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $root = \eZSys::rootDir();
        $feedback = false;
        $t = function ( $text, array $values = array() ) {
            return \ezpI18n::tr( 'design/admin/setup/maintenance', $text, null, $values );
        };

        if ( $module->isCurrentAction( 'SwitchOn' ) )
        {
            $until = 0;
            $minutes = (int)$http->postVariable( 'MaintenanceMinutes', 0 );
            if ( $minutes > 0 )
                $until = time() + min( $minutes, self::MAX_MINUTES ) * 60;
            list( $ips, $rejected ) = self::parseAddresses( (string)$http->postVariable( 'MaintenanceAllowIPs', '' ) );
            // Only when asked for: switched on here, the public site must show the
            // maintenance page to the one who switched it on too, or it looks as if
            // nothing happened. /admin stays reachable either way.
            if ( $http->hasPostVariable( 'MaintenanceAllowMe' ) )
                $ips[] = \eZSys::clientIP();
            $ok = \expMaintenance::enable( $root, array(
                'reason' => 'manual',
                'message' => trim( mb_substr( (string)$http->postVariable( 'MaintenanceMessage', '' ), 0, 500 ) ),
                'until' => $until,
                'allow_ips' => array_values( array_unique( array_filter( $ips ) ) ),
                'allow_paths' => array( '/admin' ),
                'by' => \eZUser::currentUser()->attribute( 'login' ),
            ) );
            $feedback = $ok ? array( true, $t( 'Maintenance is on: visitors see the maintenance page.' ) )
                            : array( false, $t( 'Maintenance could not be switched on: %file is not writable.', array( '%file' => \expMaintenance::MARKER ) ) );
            if ( $ok && $rejected )
                $feedback[2] = $t( 'These entries are not addresses and were left out: %list', array( '%list' => implode( ', ', $rejected ) ) );
            \eZDebug::writeNotice( $feedback[1], 'setup/maintenance' );
        }
        elseif ( $module->isCurrentAction( 'SwitchOff' ) )
        {
            $ok = \expMaintenance::disable( $root );
            if ( $ok )
                \expMaintenance::clearResponseCaches( $root );
            $feedback = $ok ? array( true, $t( 'Maintenance is off: the site answers again.' ) )
                            : array( false, $t( 'Maintenance was not on.' ) );
            \eZDebug::writeNotice( $feedback[1], 'setup/maintenance' );
        }

        $state = \expMaintenance::state( $root );
        $clientIP = (string)\eZSys::clientIP();
        $info = self::describe( $state, time(), $clientIP );
        $info['running_text'] = self::durationText( $info['running_minutes'] );
        $info['remaining_text'] = self::durationText( $info['remaining_minutes'] );
        $info['overdue_text'] = self::durationText( $info['overdue_minutes'] );

        // What visitors see: the page in force, or the one switching on would use
        $previewState = $state ? $state : array( 'reason' => 'manual', 'page' => \expMaintenance::themePage( $root ) );
        $preview = self::previewTemplate( $root, $previewState );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'maintenance_on', $state !== false );
        $tpl->setVariable( 'maintenance', $state ? $state : array() );
        $tpl->setVariable( 'feedback', $feedback );
        $tpl->setVariable( 'client_ip', $clientIP );
        $tpl->setVariable( 'maintenance_info', $info );
        $tpl->setVariable( 'maintenance_preview', $preview );
        $tpl->setVariable( 'maintenance_presets', self::presets() );
        $tpl->setVariable( 'maintenance_max_minutes', self::MAX_MINUTES );
        $tpl->setVariable( 'maintenance_history', self::history() );
        $tpl->setVariable( 'maintenance_commands', self::commands() );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:setup/maintenance.tpl' );
        $Result['path'] = array( array( 'url' => false, 'text' => \ezpI18n::tr( 'kernel/setup', 'Maintenance' ) ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }

    /**
     * The addresses of the allow-list field: whitespace or commas between them; what is not an IPv4 or IPv6
     * address is returned separately, so the page can say what it left out instead of dropping it silently.
     *
     * @param string $text
     * @return array array( valid addresses (unique, in order), rejected entries )
     */
    public static function parseAddresses( $text )
    {
        $valid = array();
        $rejected = array();
        foreach ( preg_split( '/[\s,;]+/', (string)$text, -1, PREG_SPLIT_NO_EMPTY ) as $entry )
        {
            if ( filter_var( $entry, FILTER_VALIDATE_IP ) )
                $valid[] = $entry;
            else
                $rejected[] = mb_substr( $entry, 0, 64 );
        }
        return array( array_values( array_unique( $valid ) ), array_values( array_unique( $rejected ) ) );
    }

    /**
     * What the page says about the state: on or off, why, who, since and until when, how long that is, whether
     * the expected end has passed, who still gets in and whether the viewer is one of them.
     *
     * @param array|false $state expMaintenance::state()
     * @param int $now
     * @param string $clientIP
     * @return array
     */
    public static function describe( $state, $now, $clientIP = '' )
    {
        $now = (int)$now;
        $info = array( 'on' => false, 'reason' => '', 'by' => '', 'since' => 0, 'until' => 0, 'lease' => 0,
                       'running_minutes' => 0, 'remaining_minutes' => 0, 'overdue' => false, 'overdue_minutes' => 0,
                       'message' => '', 'custom_message' => false, 'allow_ips' => array(), 'allow_paths' => array(),
                       'admin_open' => false, 'client_allowed' => false, 'run' => '', 'page' => '' );
        if ( !is_array( $state ) )
            return $info;

        $reason = isset( $state['reason'] ) ? (string)$state['reason'] : 'unknown';
        // A kickstarter run is a setup run that carries its run id but no wizard lease
        if ( $reason === 'setup' && empty( $state['lease'] ) && !empty( $state['run'] ) )
            $reason = 'install';
        if ( !in_array( $reason, array( 'manual', 'setup', 'install' ), true ) )
            $reason = 'unknown';

        $since = isset( $state['since'] ) ? (int)$state['since'] : 0;
        $until = isset( $state['until'] ) ? (int)$state['until'] : 0;
        $ips = array_values( array_filter( array_map( 'strval', (array)( $state['allow_ips'] ?? array() ) ) ) );
        $paths = array_values( array_filter( array_map( 'strval', (array)( $state['allow_paths'] ?? array() ) ) ) );
        $message = trim( (string)( $state['message'] ?? '' ) );

        $info['on'] = true;
        $info['reason'] = $reason;
        $info['by'] = isset( $state['by'] ) ? (string)$state['by'] : '';
        $info['since'] = $since;
        $info['until'] = $until;
        $info['lease'] = isset( $state['lease'] ) ? (int)$state['lease'] : 0;
        $info['run'] = isset( $state['run'] ) ? (string)$state['run'] : '';
        $info['running_minutes'] = $since > 0 && $since <= $now ? intdiv( $now - $since, 60 ) : 0;
        if ( $until > 0 )
        {
            if ( $until > $now )
                $info['remaining_minutes'] = (int)ceil( ( $until - $now ) / 60 );
            else
            {
                $info['overdue'] = true;
                $info['overdue_minutes'] = intdiv( $now - $until, 60 );
            }
        }
        $info['message'] = $message;
        $info['custom_message'] = $message !== '';
        $info['allow_ips'] = $ips;
        $info['allow_paths'] = $paths;
        foreach ( $paths as $path )
            if ( '/' . trim( $path, '/' ) === '/admin' )
                $info['admin_open'] = true;
        $info['client_allowed'] = $clientIP !== '' && in_array( (string)$clientIP, $ips, true );
        $info['page'] = !empty( $state['page'] ) ? (string)$state['page'] : \expMaintenance::DEFAULT_PAGE;
        return $info;
    }

    /**
     * The maintenance page as visitors get it, and the same page with stand-ins where the message and the expected
     * end go, which the page's script fills with what is typed in the form.
     *
     * @param string $root
     * @param array $state
     * @return array html, template, file, message_slot, until_slot, default_message, default_until
     */
    public static function previewTemplate( $root, array $state )
    {
        $template = \expMaintenance::page( $root, array( 'message' => self::MESSAGE_SLOT, 'until' => 0 ) + $state );
        $template = str_replace( htmlspecialchars( self::DEFAULT_UNTIL, ENT_QUOTES, 'UTF-8' ), self::UNTIL_SLOT, $template );
        return array(
            'html' => self::withoutScripts( \expMaintenance::page( $root, $state ) ),
            'template' => self::withoutScripts( $template ),
            'file' => !empty( $state['page'] ) ? (string)$state['page'] : \expMaintenance::DEFAULT_PAGE,
            'message_slot' => self::MESSAGE_SLOT,
            'until_slot' => self::UNTIL_SLOT,
            'default_message' => self::DEFAULT_MESSAGE,
            'default_until' => self::DEFAULT_UNTIL,
        );
    }

    /**
     * The page for the preview frame, which runs no scripts anyway: its script elements and inline handlers taken
     * out, so the browser has nothing to refuse and log.
     *
     * @param string $html
     * @return string
     */
    public static function withoutScripts( $html )
    {
        $html = preg_replace( '#<script\b[^>]*>.*?</script\s*>#is', '', (string)$html );
        return preg_replace( '#\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html );
    }

    /**
     * The shell commands that do what the page does, for the same window from a terminal. Switching on keeps
     * /admin reachable (--allow-admin), as the page does.
     *
     * @return array[] key (on|off|status), label, command
     */
    public static function commands()
    {
        $t = function ( $text ) { return \ezpI18n::tr( 'design/admin/setup/maintenance', $text ); };
        return array(
            array( 'key' => 'on', 'label' => $t( 'Switch on' ), 'command' => 'php bin/php/maintenance.php on --allow-admin' ),
            array( 'key' => 'off', 'label' => $t( 'Switch off' ), 'command' => 'php bin/php/maintenance.php off' ),
            array( 'key' => 'status', 'label' => $t( 'Show the state' ), 'command' => 'php bin/php/maintenance.php status' ),
        );
    }

    /**
     * A number of minutes in words: "45 min", "2 h", "1 h 30 min", "3 days 4 h".
     *
     * @param int $minutes
     * @return string '' for none
     */
    public static function durationText( $minutes )
    {
        $minutes = (int)$minutes;
        if ( $minutes <= 0 )
            return '';
        $t = function ( $text, $values ) { return \ezpI18n::tr( 'design/admin/setup/maintenance', $text, null, $values ); };
        $days = intdiv( $minutes, 1440 );
        $hours = intdiv( $minutes % 1440, 60 );
        $rest = $minutes % 60;
        $parts = array();
        if ( $days > 0 )
            $parts[] = $days === 1 ? $t( '1 day', array() ) : $t( '%count days', array( '%count' => $days ) );
        if ( $hours > 0 )
            $parts[] = $t( '%count h', array( '%count' => $hours ) );
        // minutes only below a day: "3 days 4 h" is precise enough
        if ( $rest > 0 && $days === 0 )
            $parts[] = $t( '%count min', array( '%count' => $rest ) );
        return implode( ' ', $parts );
    }

    /**
     * The one-click durations of the form.
     *
     * @return array[] minutes, label
     */
    public static function presets()
    {
        $out = array();
        foreach ( self::PRESETS as $minutes )
            $out[] = array( 'minutes' => $minutes, 'label' => self::durationText( $minutes ) );
        return $out;
    }

    /**
     * One audit index row of system.maintenance.change as the page lists it.
     *
     * @param array $row a row of the audit index (id, time_ms, login, record)
     * @return array|null id, time, login, mode (on|off|changed), reason, until
     */
    public static function historyRow( array $row )
    {
        $record = isset( $row['record'] ) ? json_decode( (string)$row['record'], true ) : null;
        if ( !is_array( $record ) )
            return null;
        $before = isset( $record['before']['mode'] ) ? (string)$record['before']['mode'] : 'off';
        $after = isset( $record['after']['mode'] ) ? (string)$record['after']['mode'] : 'off';
        $mode = $after === 'off' ? 'off' : ( $before === 'on' ? 'changed' : 'on' );
        $side = $after === 'off' ? ( $record['before'] ?? array() ) : ( $record['after'] ?? array() );
        $reason = isset( $side['reason'] ) ? (string)$side['reason'] : '';
        $login = isset( $row['login'] ) && $row['login'] !== null ? (string)$row['login']
               : ( isset( $record['actor']['login'] ) ? (string)$record['actor']['login'] : '' );
        return array(
            'id' => isset( $row['id'] ) ? (string)$row['id'] : (string)( $record['id'] ?? '' ),
            'time' => isset( $row['time_ms'] ) ? intdiv( (int)$row['time_ms'], 1000 ) : 0,
            'login' => $login,
            'cli' => isset( $record['actor']['cli'] ) || ( isset( $record['request']['engine'] ) && $record['request']['engine'] === 'cli' ),
            'mode' => $mode,
            'reason' => in_array( $reason, array( 'manual', 'setup' ), true ) ? $reason : ( $reason === '' ? '' : 'unknown' ),
            'until' => isset( $side['until'] ) ? (int)$side['until'] : 0,
        );
    }

    /**
     * The latest changes of the maintenance mode, from the audit index, for a user who may read the system
     * channel. Never fails the page: without the audit, its index or the right, it says so instead.
     *
     * @return array available (bool), why (no-audit|no-access|no-index|''), rows, console (url or '')
     */
    protected static function history()
    {
        $out = array( 'available' => false, 'why' => 'no-audit', 'rows' => array(), 'console' => '' );
        try
        {
            if ( !class_exists( 'expAuditConsole' ) || !\expAuditConsole::available() )
                return $out;
            $allowed = \expAuditConsole::allowedChannels();
            if ( !\expAuditConsole::channelAllowed( 'system', $allowed ) )
            {
                $out['why'] = 'no-access';
                return $out;
            }
            $out['console'] = 'audit/console/(name)/system.maintenance.change';
            if ( !\expAuditConsole::indexUsable() )
            {
                $out['why'] = 'no-index';
                return $out;
            }
            $query = new \expAuditQuery();
            $rows = $query->fetch( \expAuditQuery::normalise( array( 'name' => 'system.maintenance.change' ) ), $allowed, 0, self::HISTORY,
                                   'id, time_ms, login, record' );
            if ( $rows === null )
            {
                $out['why'] = 'no-index';
                return $out;
            }
            foreach ( $rows as $row )
                if ( ( $entry = self::historyRow( $row ) ) !== null )
                    $out['rows'][] = $entry;
            $out['available'] = true;
            $out['why'] = '';
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeWarning( 'The maintenance history could not be read: ' . $e->getMessage(), 'setup/maintenance' );
            $out['why'] = 'no-index';
        }
        return $out;
    }
}

}

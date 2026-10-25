<?php
/**
 * File containing the expSysCardCronProvider class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The cronjobs: when one last ran (the newest cronjob log or Setup > Cronjobs run) and, for exp:info, every part of
 * cronjob.ini with its scripts. No run for two days is a hint: the cleanup, notification and indexing jobs wait.
 */
class expSysCardCronProvider extends expSysCardBaseProvider
{
    protected $id = 'cron';
    protected $section = 'site';
    protected $priority = 35;
    protected $budget = 0.5;

    /** A run older than this (seconds) is a hint */
    const STALE = 172800;

    public function collect( expSysCardContext $context )
    {
        $last = 0;
        foreach ( array( 'var/log/cronjob*.log', 'var/*/log/cronjob*.log', 'var/cronjobs/*.log', 'var/*/cronjobs/*.log' ) as $pattern )
        {
            foreach ( $context->glob( $pattern ) as $file )
            {
                $time = (int)$context->get( 'cron.mtime:' . $file, function () use ( $file ) { return @filesize( $file ) > 0 ? (int)@filemtime( $file ) : 0; }, 0 );
                $last = max( $last, $time );
            }
        }
        foreach ( $context->glob( 'var/*/cronjobs/history.json' ) as $file )
        {
            $runs = json_decode( (string)$context->file( $file ), true );
            if ( is_array( $runs ) && isset( $runs[0]['started'] ) )
                $last = max( $last, (int)$runs[0]['started'] );
        }
        // the parts runcronjobs.php started (expCrontabSetup::recordRun()); the cron job's lines write no log while quiet
        foreach ( $context->glob( 'var/log/cronjob-lastrun.json' ) as $file )
        {
            $runs = json_decode( (string)$context->file( $file ), true );
            foreach ( is_array( $runs ) ? $runs : array() as $run )
                if ( is_array( $run ) && isset( $run['started'] ) )
                    $last = max( $last, (int)$run['started'] );
        }
        $parts = array();
        $global =(array)$context->ini( 'cronjob.ini', 'CronjobSettings', 'Scripts', array() );
        if ( !empty( $global ) )
            $parts['default'] = $global;
        foreach ( $context->iniGroups( 'cronjob.ini' ) as $group )
            if ( strpos( $group, 'CronjobPart-' ) === 0 )
                $parts[substr( $group, 12 )] = (array)$context->ini( 'cronjob.ini', $group, 'Scripts', array() );

        if ( $last === 0 )
            $fact = $this->fact( 'last', 'Cronjobs', 'no run found, ' . self::counted( count( $parts ), 'part' ), expSysCardFact::INFO,
                                 'No cronjob run was found: add runcronjobs.php to the crontab (doc/eng-us/guides/cronjobs.md), or the cleanup, notification and search index jobs never run.' );
        else
        {
            $old = $context->now - $last > self::STALE;
            $fact = $this->fact( 'last', 'Cronjobs', 'last run ' . self::ago( $last, $context->now ) . ', ' . self::counted( count( $parts ), 'part' ), $old ? expSysCardFact::WARN : expSysCardFact::OK,
                                 $old ? 'No cronjob has run for ' . self::elapsed( $last, $context->now ) . ': check the crontab and the cronjob logs.' : '' );
        }
        $facts = array( $fact );
        if ( $context->mode === expSysCardContext::MODE_INFO )
        {
            foreach ( $parts as $part => $scripts )
            {
                $scripts = array_values( array_filter( array_map( 'trim', $scripts ), 'strlen' ) );
                $facts[] = $this->fact( 'part:' . $part, $part, empty( $scripts ) ? '(none)' : implode( ', ', array_map( function ( $s ) { return basename( $s, '.php' ); }, $scripts ) ),
                                        expSysCardFact::INFO, '', 'cron' );
            }
        }
        return $facts;
    }
}

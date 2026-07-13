<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * expScriptStatus - lightweight CLI progress/status reporter.
 *
 * Shows a single status line that updates in place (\r) with:
 *   [command] description | percent (current/total) | elapsed | end | end @ time
 *
 * Respects --quiet and is auto-finalised at script shutdown.
 */
class expScriptStatus
{
    private static $instance = null;
    private $cli;
    private $startTime = 0;
    private $command = '';
    private $commandLabel = '';
    private $description = '';
    private $current = 0;
    private $total = 0;
    private $reportInterval = 1;
    private $lastReportCurrent = -1;
    private $lastDescription = '';
    private $lastTotal = 0;
    private $lineLength = 0;
    private $ended = false;

    private function __construct()
    {
        $this->cli = eZCLI::instance();
        register_shutdown_function( array( $this, 'end' ) );
    }

    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new expScriptStatus();
        return self::$instance;
    }

    public function start( $command, $description, $total = 0 )
    {
        $this->command = $command;
        $this->commandLabel = $description;
        $this->description = $description;
        $this->startTime = microtime( true );
        $this->current = 0;
        $this->total = (int)$total;
        $this->reportInterval = 1;
        $this->lastReportCurrent = -1;
        $this->lastDescription = '';
        $this->lastTotal = 0;
        $this->lineLength = 0;
        $this->ended = false;
        $this->maybeRender();
    }

    public function update( $description, $current = 0, $total = 0 )
    {
        $this->description = $description;
        $this->current = (int)$current;
        if ( $total > 0 )
            $this->total = (int)$total;
        if ( $this->startTime == 0 )
            $this->startTime = microtime( true );
        $this->maybeRender();
    }

    public function fail( $description = null )
    {
        if ( $this->ended || $this->startTime == 0 || $this->cli->isQuiet() )
            return;
        $this->ended = true;
        $text = $description !== null ? $description : $this->commandLabel . ' failed';
        $this->line( $this->prefix( $this->st( 'error', $text ) ) . ' | ' . $this->st( 'symbol', 'elapsed' ) . ' ' . $this->st( 'file', $this->formatElapsed() ), true );
        $this->startTime = 0;
        $this->current = 0;
        $this->total = 0;
        $this->lineLength = 0;
    }

    public function newline()
    {
        if ( $this->cli->isQuiet() || !is_resource( STDOUT ) )
            return;
        fwrite( STDOUT, "\n" );
        fflush( STDOUT );
        $this->lineLength = 0;
    }

    public function end( $description = null )
    {
        if ( $this->ended || $this->startTime == 0 || $this->cli->isQuiet() )
            return;
        $this->ended = true;
        $text = $description !== null ? $description : $this->commandLabel . ' complete';
        $this->line( $this->prefix( $this->st( 'success', $text ) ) . ' | ' . $this->st( 'symbol', 'elapsed' ) . ' ' . $this->st( 'file', $this->formatElapsed() ), true );
        $this->startTime = 0;
        $this->current = 0;
        $this->total = 0;
        $this->lineLength = 0;
    }

    private function maybeRender()
    {
        if ( $this->cli->isQuiet() )
            return;
        if ( $this->total > 0 && ( $this->total != $this->lastTotal || $this->lastReportCurrent == -1 ) )
        {
            $this->reportInterval = max( 1, (int)round( $this->total / 1000 ) );
            $this->lastTotal = $this->total;
            $this->lastReportCurrent = -1;
        }
        if ( $this->description !== $this->lastDescription )
            return $this->render();
        if ( $this->total > 0 )
        {
            if ( $this->current == $this->total && $this->current != $this->lastReportCurrent )
                return $this->render();
            if ( $this->current - $this->lastReportCurrent >= $this->reportInterval )
                return $this->render();
            return;
        }
        if ( $this->current != $this->lastReportCurrent )
            $this->render();
    }

    private function render()
    {
        $this->lastDescription = $this->description;
        $this->lastTotal = $this->total;
        $this->lastReportCurrent = $this->current;

        $parts = array( $this->prefix( $this->st( 'notice', $this->description ) ) );
        if ( $this->total > 0 && $this->current >= 0 )
        {
            $percent = $this->current > 0 ? round( ( $this->current / $this->total ) * 100, 1 ) : 0;
            $percentStyle = $percent >= 100 ? 'success' : ( $percent >= 50 ? 'timing' : 'warning' );
            $parts[] = $this->st( $percentStyle, $percent . '%' ) . ' ' . $this->st( 'symbol', '(' . $this->current . '/' . $this->total . ')' );
        }
        if ( $this->startTime > 0 )
        {
            $elapsed = microtime( true ) - $this->startTime;
            $parts[] = $this->st( 'symbol', 'elapsed' ) . ' ' . $this->st( 'file', $this->formatTime( $elapsed ) );
            if ( $this->total > 0 && $this->current > 0 && $elapsed > 0 )
            {
                $rate = $this->current / $elapsed;
                $remaining = ( $this->total - $this->current ) / $rate;
                if ( is_finite( $remaining ) && $remaining > 0 )
                {
                    $parts[] = $this->st( 'symbol', 'end' ) . ' ' . $this->st( 'warning', $this->formatTime( $remaining ) );
                    $parts[] = $this->st( 'symbol', 'end @' ) . ' ' . $this->st( 'timing', date( 'H:i:s', strtotime( '+' . ceil( $remaining ) . ' seconds' ) ) );
                }
            }
        }
        $this->line( implode( ' | ', $parts ), true );
    }

    private function prefix( $text )
    {
        if ( $this->command === '' )
            return $text;
        return $this->st( 'timing', '[' . $this->command . ']' ) . ' ' . $text;
    }

    private function line( $text, $newline = false )
    {
        if ( $this->cli->isQuiet() || !is_resource( STDOUT ) )
            return;
        $plain = preg_replace( '/\033\[[0-9;]*m/', '', $text );
        $plainLen = strlen( $plain );
        $width = max( $this->lineLength, $plainLen );
        $this->lineLength = $plainLen;
        $padded = $text . str_repeat( ' ', max( 0, $width - $plainLen ) );
        fwrite( STDOUT, "\r" . $padded );
        if ( $newline )
            fwrite( STDOUT, "\n" );
        fflush( STDOUT );
    }

    private function st( $style, $text )
    {
        return $this->cli->stylize( $style, $text );
    }

    private function formatElapsed()
    {
        return $this->formatTime( microtime( true ) - $this->startTime );
    }

    private function formatTime( $seconds )
    {
        $seconds = (int)$seconds;
        $h = floor( $seconds / 3600 );
        $m = floor( ( $seconds % 3600 ) / 60 );
        $s = $seconds % 60;
        return sprintf( '%02d:%02d:%02d', $h, $m, $s );
    }
}

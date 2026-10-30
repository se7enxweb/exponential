<?php
/**
 * The PHP, OPcache and APCu chips of the Exp Debug bar's summary (lib/ezutils/classes/expdebugbarreport.php,
 * expDebugBarReport::runtimeFigures(), runtimeChips(), versionsChip(), apcuStatus()):
 *   - nothing for a viewer without setup or cache rights, the versions for one with them
 *   - OPcache, APCu, then PHP and Exponential in one chip, the last chips of the summary, at its right end
 *   - OPcache and APCu only when they are on, each with the share of its memory in use and the details in the title
 *   - the level follows the defaults (OPcache 80/95 %, APCu 75/90 %); a level of the kernel's summary wins
 *   - the chips open the Velocity tab, and summaryHtml() draws them with their title escaped
 *   - apcuStatus() is null without APCu and else names the figures the chips read
 *
 * No database: the figures are passed in.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group lib
 * @group ezutils
 */

class expDebugBarReportRuntimeChipsTest extends PHPUnit\Framework\TestCase
{
    private static function runtime( array $over = array() )
    {
        return $over + array(
            'php' => '8.4.19', 'sapi' => 'fpm-fcgi', 'exponential' => '6.0.15',
            'opcache' => array( 'enabled' => true, 'used' => 64 * 1048576, 'free' => 64 * 1048576, 'hit_rate' => 99.5,
                                'scripts' => 1200, 'restarts' => 0 ),
            'apcu' => array( 'size' => 32 * 1048576, 'used' => 8 * 1048576, 'entries' => 450, 'hit_rate' => 97.25, 'expunges' => 0 ),
        );
    }

    /** @return array key => chip */
    private static function byKey( array $chips )
    {
        $keyed = array();
        foreach ( $chips as $chip )
            $keyed[$chip[1]] = $chip;
        return $keyed;
    }

    public function testNothingForAViewerWithoutRights()
    {
        $this->assertNull( expDebugBarReport::runtimeFigures( false ) );
        $this->assertSame( array(), expDebugBarReport::runtimeChips( null ) );
        $this->assertNull( expDebugBarReport::versionsChip( null ) );
    }

    public function testAViewerWithRightsGetsThePhpVersion()
    {
        $figures = expDebugBarReport::runtimeFigures( true );
        $this->assertSame( PHP_VERSION, $figures['php'] );
        $this->assertSame( PHP_SAPI, $figures['sapi'] );
        $this->assertSame( eZPublishSDK::version( true, false, false ), $figures['exponential'] );
        $this->assertArrayHasKey( 'opcache', $figures );
        $this->assertArrayHasKey( 'apcu', $figures );
    }

    public function testTheChipsShowTheShareOfMemoryInUse()
    {
        $chips = self::byKey( expDebugBarReport::runtimeChips( self::runtime() ) );
        $this->assertSame( array( 'opcache', 'apcu' ), array_keys( $chips ) );
        $this->assertSame( 'OPc 50 %', $chips['opcache'][3] );
        $this->assertSame( 'APCu 25 %', $chips['apcu'][3] );
        foreach ( $chips as $chip )
            $this->assertSame( 'velocity', $chip[0], 'a chip opens the Velocity tab' );
        $this->assertStringContainsString( '64.0 of 128 MB', $chips['opcache'][4] );
        $this->assertStringContainsString( '1200 scripts', $chips['opcache'][4] );
        $this->assertStringContainsString( '450 entries', $chips['apcu'][4] );
        $this->assertStringContainsString( 'hit rate 97.25 %', $chips['apcu'][4] );
    }

    public function testOpcacheAndApcuAppearOnlyWhenOn()
    {
        $chips = self::byKey( expDebugBarReport::runtimeChips( self::runtime( array( 'opcache' => null, 'apcu' => null ) ) ) );
        $this->assertSame( array(), $chips );
    }

    public function testTheVersionsAreOneChip()
    {
        $chip = expDebugBarReport::versionsChip( self::runtime() );
        $this->assertSame( array( 'velocity', 'versions', 'ok', 'PHP 8.4.19 | Exp 6.0.15' ), array_slice( $chip, 0, 4 ) );
        $this->assertStringContainsString( 'PHP 8.4.19 (fpm-fcgi), Exponential 6.0.15', $chip[4] );
        $this->assertSame( 'PHP 8.4.19', expDebugBarReport::versionsChip( self::runtime( array( 'exponential' => null ) ) )[3] );
    }

    public static function levels()
    {
        // used MB of 100 MB: OPcache warns from 80 %, high from 95 %; APCu from 75 % and 90 %
        return array(
            'both ok'     => array( 70, 70, 'ok', 'ok' ),
            'APCu warns'  => array( 79, 75, 'ok', 'warn' ),
            'both warn'   => array( 80, 89, 'warn', 'warn' ),
            'APCu high'   => array( 94, 90, 'warn', 'high' ),
            'both high'   => array( 95, 99, 'high', 'high' ),
        );
    }

    /**
     * @dataProvider levels
     */
    #[PHPUnit\Framework\Attributes\DataProvider('levels')]
    public function testTheLevelFollowsTheDefaults( $opcacheUsed, $apcuUsed, $opcacheLevel, $apcuLevel )
    {
        $mb = 1048576;
        $chips = self::byKey( expDebugBarReport::runtimeChips( self::runtime( array(
            'opcache' => array( 'enabled' => true, 'used' => $opcacheUsed * $mb, 'free' => ( 100 - $opcacheUsed ) * $mb, 'hit_rate' => 99,
                                'scripts' => 1, 'restarts' => 0 ),
            'apcu' => array( 'size' => 100 * $mb, 'used' => $apcuUsed * $mb, 'entries' => 1, 'hit_rate' => 99, 'expunges' => 0 ) ) ) ) );
        $this->assertSame( $opcacheLevel, $chips['opcache'][2] );
        $this->assertSame( $apcuLevel, $chips['apcu'][2] );
    }

    public function testALevelOfTheKernelsSummaryWins()
    {
        $chips = self::byKey( expDebugBarReport::runtimeChips( self::runtime(), array( 'opcache' => 'high', 'apcu' => 'warn' ) ) );
        $this->assertSame( 'high', $chips['opcache'][2] );
        $this->assertSame( 'warn', $chips['apcu'][2] );

        $chips = self::byKey( expDebugBarReport::runtimeChips( self::runtime(), array( 'opcache' => 'bogus' ) ) );
        $this->assertSame( 'ok', $chips['opcache'][2], 'an unknown level is ignored' );
    }

    public function testTheSummaryDrawsTheChipsEscaped()
    {
        $summary = new ReflectionMethod( 'expDebugBarReport', 'summaryHtml' );
        $html = $summary->invoke( null, array(
            'time' => 0.1, 'sql_count' => 1, 'sql_ms' => 1.0, 'memory' => 1048576, 'memory_limit' => 0, 'templates' => null,
            'warnings' => 0, 'errors' => 0, 'notices' => 0, 'deprecations' => 0, 'levels' => array(), 'querycache' => null,
            'runtime' => self::runtime( array( 'sapi' => '<script>' ) ) ) );
        $this->assertMatchesRegularExpression( '#<button type="button" class="exp-debug-chip exp-debug-end" data-tab="velocity" data-key="opcache"[^>]*>OPc 50 %</button>'
                                               . '<button type="button" class="exp-debug-chip" data-tab="velocity" data-key="apcu"[^>]*>APCu 25 %</button>'
                                               . '<button type="button" class="exp-debug-chip" data-tab="velocity" data-key="versions"[^>]*>PHP 8\.4\.19 \| Exp 6\.0\.15</button></span>$#', $html,
                                               'OPcache, APCu, then the versions, as the last chips; the first of them marked for the right end' );
        $this->assertStringNotContainsString( '<script>', $html );
        $this->assertStringContainsString( '&lt;script&gt;', $html );
    }

    public function testApcuStatusNamesItsFigures()
    {
        $status = expDebugBarReport::apcuStatus();
        if ( !function_exists( 'apcu_enabled' ) || !apcu_enabled() )
        {
            $this->assertNull( $status, 'without APCu (or with apc.enable_cli off) there is nothing to show' );
            return;
        }
        $this->assertSame( array( 'size', 'used', 'entries', 'hit_rate', 'expunges' ), array_keys( $status ) );
        $this->assertGreaterThan( 0, $status['size'] );
        $this->assertLessThanOrEqual( $status['size'], $status['used'] );
    }
}

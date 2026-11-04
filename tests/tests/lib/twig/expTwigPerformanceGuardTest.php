<?php
/**
 * Performance guard (twig.md 10b): a fixed set of templates rendered N times as .tpl and as the .twig the converter
 * writes, in the same process, through the template system (eZTemplate::fetch(), the stub, expTwigRenderer). The guard
 * is relative, so the machine does not matter: the MEDIAN of the per-round ratios twig/tpl may be at most BUDGET. A round
 * renders the .tpl and the .twig RENDERS times each, back to back, in an order that alternates from round to round, so a
 * burst of load on a shared machine falls on both sides of one round, or moves single rounds, which the median leaves
 * out. The times and the ratios are printed, so a change that makes Twig slower shows before it crosses the budget.
 *
 * Twig 3 from Composer, or TWIG_LIBRARY_PATH; without it the guard is skipped. No database and no siteaccess. With an
 * active Xdebug (any xdebug.mode but off) it is skipped too: Xdebug slows Twig's many small calls far more than the
 * compiled .tpl, so the ratio would measure Xdebug. CI runs it in a step of its own with php -d xdebug.mode=off.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group twig
 * @group performance
 */
#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('twig')]
#[\PHPUnit\Framework\Attributes\Group('performance')]
class expTwigPerformanceGuardTest extends PHPUnit\Framework\TestCase
{
    /** A .twig render may take at most this many times its .tpl. */
    const BUDGET = 2.0;

    /** Renders per side and round, and rounds (odd: the median is one round's ratio). */
    const RENDERS = 20;
    const ROUNDS = 15;

    private static $dir;

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-twig-performance-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir, 0777, true );
        if ( !class_exists( '\Twig\Environment' ) && getenv( 'TWIG_LIBRARY_PATH' ) )
            eZINI::instance( 'twig.ini' )->setVariable( 'TwigSettings', 'LibraryPath', getenv( 'TWIG_LIBRARY_PATH' ) );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( glob( self::$dir . '/*' ) as $file )
            unlink( $file );
        rmdir( self::$dir );
    }

    /** The templates: a list with a delimiter and operators, conditions, an include, a section, a while. */
    private static function templates()
    {
        return array(
            'list'    => "<ul>{foreach \$items as \$i => \$item}<li class=\"{cond( \$i|mod( 2 ), 'odd', 'even' )}\">{\$item.name|wash}"
                       . "{if \$item.tags|count|gt( 0 )} ({\$item.tags|implode( ', ' )}){/if}</li>{delimiter}\n{/delimiter}{/foreach}</ul>",
            'logic'   => "{def \$n = 0}{foreach \$items as \$item}{if and( \$item.price|gt( 10 ), \$item.tags|contains( 'b' ) )}{set \$n = \$n|inc}{/if}{/foreach}{\$n}"
                       . "{concat( 'a', 'b', \$title )|upcase}{undef \$n}",
            'section' => "{section var=v loop=\$items max=5}{\$v.index}:{\$v.item.name|shorten( 4 )};{/section}"
                       . "{def \$k = 0}{while \$k|lt( 20 )}{set \$k = \$k|inc}{/while}{\$k}{undef \$k}",
        );
    }

    private static function variables()
    {
        $items = array();
        for ( $i = 0; $i < 30; ++$i )
            $items[] = array( 'name' => "Item <$i> name", 'price' => $i * 1.5, 'tags' => $i % 3 ? array( 'a', 'b' ) : array() );
        return array( 'items' => $items, 'title' => 'Title' );
    }

    private function timeRenders( $file, array $variables )
    {
        // as on a production system: no debug output (the debug bar's records are not part of the comparison)
        $debug = isset( $GLOBALS['eZDebugEnabled'] ) ? $GLOBALS['eZDebugEnabled'] : null;
        $GLOBALS['eZDebugEnabled'] = false;
        try
        {
            return $this->timeRendersWithoutDebug( $file, $variables );
        }
        finally
        {
            $GLOBALS['eZDebugEnabled'] = $debug;
        }
    }

    private function timeRendersWithoutDebug( $file, array $variables )
    {
        $tpl = eZTemplate::factory();
        $saved = $tpl->Variables;
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        $start = hrtime( true );
        for ( $i = 0; $i < self::RENDERS; ++$i )
            $tpl->fetch( $file );
        $ns = hrtime( true ) - $start;
        $tpl->Variables = $saved;
        return array( $ns / self::RENDERS / 1e6, $output );
    }

    public function testTwigRendersWithinTheBudgetOfTpl()
    {
        if ( !expTwigRenderer::loadLibrary() )
            $this->markTestSkipped( 'Twig 3 is not installed (composer require twig/twig, or TWIG_LIBRARY_PATH)' );
        $xdebugModes = self::xdebugModes();
        if ( $xdebugModes )
            $this->markTestSkipped( 'Xdebug is active (xdebug.mode=' . implode( ',', $xdebugModes ) . '): it adds a cost to every PHP '
                                  . 'function call, and a Twig render makes several times as many calls as a .tpl render, so the ratio '
                                  . 'measures Xdebug, not Twig (PHP 8.5, 2026-11-01: 1.2-1.4 with xdebug.mode=off, 2.1 and more with '
                                  . 'xdebug.mode=coverage, the mode of every CI leg). Run it '
                                  . 'with php -d xdebug.mode=off, as the PHPUnit workflow does in its own step.' );
        $converter = new expTwigConverter( eZTemplate::factory() );
        $variables = self::variables();
        $report = array();
        foreach ( self::templates() as $name => $source )
        {
            $tplFile = self::$dir . "/$name.tpl";
            $twigFile = self::$dir . "/$name.twig";
            file_put_contents( $tplFile, $source );
            file_put_contents( $twigFile, $converter->convert( $source, "$name.tpl" ) );
            // a template over the budget is measured once more and the lower median counts: a shared CI runner put a
            // single series at 2.002 (2026-10-31), a real slowdown stays over the budget in both series
            for ( $series = 0; $series < 2; ++$series )
            {
                list( $median, $line ) = $this->measure( $name, $tplFile, $twigFile, $variables );
                if ( $series === 0 || $median < $best )
                    list( $best, $bestLine ) = array( $median, $line );
                if ( $best <= self::BUDGET )
                    break;
            }
            $report[] = $bestLine;
            $this->assertLessThanOrEqual( self::BUDGET, $best, "$name: a .twig render takes $best times its .tpl (median of the rounds, lower of two series; budget " . self::BUDGET . ")\n" . implode( "\n", $report ) );
        }
        fwrite( STDERR, "\n" . implode( "\n", $report ) . "\n" );
    }

    /**
     * The Xdebug modes that instrument code: none without Xdebug or with xdebug.mode=off.
     *
     * @return string[]
     */
    private static function xdebugModes()
    {
        if ( !extension_loaded( 'xdebug' ) )
            return array();
        if ( function_exists( 'xdebug_info' ) )
            return array_values( (array)xdebug_info( 'mode' ) );
        $mode = (string)ini_get( 'xdebug.mode' );
        return $mode === '' || $mode === 'off' ? array() : explode( ',', $mode );
    }

    /**
     * One series: ROUNDS interleaved rounds of the .tpl and the .twig, both outputs compared.
     *
     * @return array( median twig/tpl ratio, report line )
     */
    private function measure( $name, $tplFile, $twigFile, array $variables )
    {
        $ratios = array();
        $tplMs = $twigMs = array();
        for ( $round = 0; $round < self::ROUNDS; ++$round )
        {
            // interleaved: the side that goes first alternates
            if ( $round % 2 === 0 )
            {
                list( $tplTime, $tplOutput ) = $this->timeRenders( $tplFile, $variables );
                list( $twigTime, $twigOutput ) = $this->timeRenders( $twigFile, $variables );
            }
            else
            {
                list( $twigTime, $twigOutput ) = $this->timeRenders( $twigFile, $variables );
                list( $tplTime, $tplOutput ) = $this->timeRenders( $tplFile, $variables );
            }
            $this->assertSame( $tplOutput, $twigOutput, "$name renders the same" );
            $ratios[] = $twigTime / max( $tplTime, 1e-6 );
            $tplMs[] = $tplTime;
            $twigMs[] = $twigTime;
        }
        sort( $ratios );
        sort( $tplMs );
        sort( $twigMs );
        $middle = intdiv( self::ROUNDS, 2 );
        $median = $ratios[$middle];
        return array( $median, sprintf( '%-8s tpl %.3f ms  twig %.3f ms (medians)  twig/tpl median %.2f (lowest %.2f, highest %.2f, %d interleaved rounds)',
                                        $name, $tplMs[$middle], $twigMs[$middle], $median, $ratios[0], $ratios[self::ROUNDS - 1], self::ROUNDS ) );
    }
}

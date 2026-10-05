<?php
/**
 * More of the template engine through eZTemplate with string templates, interpreted and compiled:
 *   - eZTemplateUnitOperator (si): explicit decimal and binary prefixes, none, decimal and binary auto modes, the
 *     decimal count, decimal symbol and thousands separator parameters, negative values (prefixed by magnitude),
 *     small values, an unknown unit and an unknown prefix, with literal and variable input
 *   - eZTemplateLocaleOperator: gettime, maketime, makedate and currentdate against PHP's own date functions,
 *     l10n and datetime against eZLocale's formatting of the same value, the locale fetch, an unknown type
 *   - eZTemplateSequenceFunction and the sequence parameter of section
 *
 * No kernel bootstrap, no database. The locale is whatever site.ini sets; every locale dependent expectation is
 * computed with eZLocale itself. Template files and compiled templates go to a private directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

class eZTemplateUnitLocaleAndSequenceTest extends PHPUnit\Framework\TestCase
{
    private static $dir;
    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-eztemplate-unit-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
    }

    protected function tearDown(): void
    {
        foreach ( $this->savedGlobals as $name => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$name] );
            else
                $GLOBALS[$name] = $saved[0];
        }
    }

    private static function removeTree( $path )
    {
        if ( !$path || !file_exists( $path ) )
            return;
        if ( is_dir( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( $path . '/' . $entry );
            }
            rmdir( $path );
        }
        else
            unlink( $path );
    }

    private function render( $source, $variables, $compiled )
    {
        if ( $compiled )
        {
            unset( $GLOBALS['eZSiteBasics'] );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = self::$dir . '/compiled';
        }
        else
        {
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
            $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
        }
        $file = self::$dir . '/' . md5( $source ) . '.tpl';
        if ( !file_exists( $file ) )
            file_put_contents( $file, $source );
        $tpl = new eZTemplate();
        $tpl->autoload();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        return array( $output, $tpl->errorLog(), $tpl->warningLog() );
    }

    private static function binarySuffix()
    {
        return eZINI::instance()->variable( 'UnitSettings', 'UseSIUnits' ) == 'true' ? 'Mi' : 'M';
    }

    /**
     * Cases: name => array( input, parameters (after the unit), expected ).
     */
    public static function unitTable()
    {
        $p = ',".",","';
        return array(
            'kilo'                  => array( 1500, '"meter","kilo",1' . $p, '1.5 km' ),
            'mega'                  => array( 2500000, '"gram","mega",2' . $p, '2.50 Mg' ),
            'milli'                 => array( 0.25, '"meter","milli",0' . $p, '250 mm' ),
            'kibi'                  => array( 2048, '"byte","kibi",1' . $p, '2.0 KiB' ),
            'none with separators'  => array( 1234567, '"meter","none",2' . $p, '1,234,567.00 m' ),
            'none, other symbols'   => array( 1234567, '"meter","none",2,",","."', '1.234.567,00 m' ),
            'decimal auto prefix'   => array( 2500000, '"gram","decimal",2' . $p, '2.50 Mg' ),
            'decimal small value'   => array( 3, '"gram","decimal",2' . $p, '3 g' ),
            'decimal negative'      => array( -2000, '"meter","decimal",1' . $p, '-2.0 km' ),
            'decimal small negative' => array( -3, '"meter","decimal",1' . $p, '-3 m' ),
            'binary negative'       => array( -2048, '"bit","kibi",0' . $p, '-2 Kib' ),
        );
    }

    public static function unitProvider()
    {
        $rows = array();
        foreach ( self::unitTable() as $name => $case )
        {
            list( $input, $parameters, $expected ) = $case;
            foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
            {
                $rows["$name, literal, $mode"] = array( '{' . $input . '|si(' . $parameters . ')}', array(), $compiled, $expected );
                $rows["$name, variable, $mode"] = array( '{$v|si(' . $parameters . ')}', array( 'v' => $input ), $compiled, $expected );
            }
        }
        return $rows;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('unitProvider')]
    public function testSi( $source, $variables, $compiled, $expected )
    {
        list( $output, $errors ) = $this->render( $source, $variables, $compiled );
        $this->assertSame( array(), $errors );
        $this->assertSame( $expected, $output );
    }

    public static function modeProvider()
    {
        return array( 'interpreted' => array( false ), 'compiled' => array( true ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testSiBinaryAutoFollowsTheSiUnitsSetting( $compiled )
    {
        list( $output ) = $this->render( '{$v|si("byte","binary",0,".",",")}', array( 'v' => 3 * 1048576 ), $compiled );
        $this->assertSame( '3 ' . self::binarySuffix() . 'B', $output );
        list( $output ) = $this->render( '{$v|si("byte",auto,0,".",",")}', array( 'v' => 3 * 1048576 ), $compiled );
        $this->assertStringEndsWith( 'B', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testSiUnknownUnitWarnsAndKeepsTheValue( $compiled )
    {
        list( $output, $errors, $warnings ) = $this->render( '{$v|si("furlong")}', array( 'v' => 5 ), $compiled );
        $this->assertNotEmpty( $warnings );
        $this->assertSame( '5', $output );
    }

    public function testSiUnknownPrefixWarns()
    {
        list( $output, $errors, $warnings ) = $this->render( '{$v|si("meter","bogus",0,".",",")}', array( 'v' => 7 ), false );
        $this->assertNotEmpty( $warnings );
        $this->assertSame( '7 m', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testGettime( $compiled )
    {
        $t = mktime( 13, 14, 15, 7, 9, 2025 ); // a Wednesday
        list( $output, $errors ) = $this->render(
            '{def $g=gettime($t)}{$g.hours}:{$g.minutes}:{$g.seconds} {$g.day}.{$g.month}.{$g.year} {$g.weekday} {$g.yearday} {$g.weeknumber} {$g.epoch}{undef $g}',
            array( 't' => $t ), $compiled );
        $this->assertSame( array(), $errors );
        $info = getdate( $t );
        $this->assertSame( "13:14:15 9.7.2025 {$info['wday']} {$info['yday']} " . date( 'W', $t ) . " $t", $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testMaketimeAndMakedate( $compiled )
    {
        list( $output, $errors ) = $this->render( '{maketime(10,20,30,3,4,2024)}|{makedate(3,4,2024)}', array(), $compiled );
        $this->assertSame( array(), $errors );
        $this->assertSame( mktime( 10, 20, 30, 3, 4, 2024 ) . '|' . mktime( 0, 0, 0, 3, 4, 2024 ), $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testCurrentdate( $compiled )
    {
        $before = time();
        list( $output ) = $this->render( '{currentdate()}', array(), $compiled );
        $this->assertGreaterThanOrEqual( $before, (int)$output );
        $this->assertLessThanOrEqual( time(), (int)$output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testL10nUsesTheLocale( $compiled )
    {
        $locale = eZLocale::instance();
        $t = mktime( 8, 5, 0, 12, 24, 2025 );
        list( $output, $errors ) = $this->render(
            '{$t|l10n("date")}|{$t|l10n("shortdate")}|{$t|l10n("time")}|{$n|l10n("number")}|{$n|l10n("currency")}',
            array( 't' => $t, 'n' => 1234.5 ), $compiled );
        $this->assertSame( array(), $errors );
        $this->assertSame( implode( '|', array(
            $locale->formatDate( $t ), $locale->formatShortDate( $t ), $locale->formatTime( $t ),
            $locale->formatNumber( 1234.5 ), $locale->formatCurrency( 1234.5 ) ) ), $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testDatetimeCustomFormat( $compiled )
    {
        $t = mktime( 8, 5, 0, 12, 24, 2025 );
        list( $output, $errors ) = $this->render( '{$t|datetime("custom","%Y-%m-%d %H:%i")}', array( 't' => $t ), $compiled );
        $this->assertSame( array(), $errors );
        $this->assertSame( eZLocale::instance()->formatDateTimeType( '%Y-%m-%d %H:%i', $t ), $output );
        $this->assertSame( '2025-12-24 08:05', $output );
    }

    public function testUnknownDatetimeClassAndLocaleTypeAreErrors()
    {
        list( , $errors ) = $this->render( '{$t|datetime("no_such_class")}', array( 't' => 0 ), false );
        $this->assertNotEmpty( $errors );
        list( , $errors ) = $this->render( '{$t|l10n("no_such_type")}', array( 't' => 0 ), false );
        $this->assertNotEmpty( $errors );
    }

    public function testLocaleFetch()
    {
        list( $output, $errors ) = $this->render( '{locale("eng-GB").locale_code}', array(), false );
        $this->assertSame( array(), $errors );
        $this->assertSame( 'eng-GB', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('modeProvider')]
    public function testSequenceFunction( $compiled )
    {
        list( $output, $errors ) = $this->render(
            '{sequence name=row loop=array("odd","even")}{foreach array(1,2,3) as $i}{$i}{$row:item}{$row:iteration} {sequence name=row}{/foreach}',
            array(), $compiled );
        unset( $GLOBALS['eZTemplateSequence-row'] );
        $this->assertSame( array(), $errors );
        $this->assertSame( '1odd0 2even1 3odd2 ', $output );
    }

    public function testSectionSequence()
    {
        list( $output, $errors ) = $this->render(
            '{section name=S loop=$l sequence=array("a","b")}{$S:item}{$S:sequence};{/section}',
            array( 'l' => array( 1, 2, 3 ) ), false );
        $this->assertSame( array(), $errors );
        $this->assertSame( '1a;2b;3a;', $output );
    }
}

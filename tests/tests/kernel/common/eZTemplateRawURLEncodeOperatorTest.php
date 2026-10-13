<?php
/**
 * The template operators rawurlencode and rawurldecode (template.ini [PHP] PHPOperatorList): RFC 3986
 * encoding, where a space is %20 and not + as urlencode gives. Checked in the shipped settings, through the
 * PHP operator class and in a template rendered by eZTemplate. Numbers, the empty text and the RFC 3986 characters
 * give the same result interpreted and compiled, and a value that is not set gives an empty text without a PHP 8.1
 * null deprecation (the same for urlencode and urldecode, which share eZTemplatePHPOperator). No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/common/eZTemplateRawURLEncodeOperatorTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group templateoperators
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 627004e5c4), Copyright (C) Mugo Web, GPL v2
 */

class eZTemplateRawURLEncodeOperatorTest extends PHPUnit\Framework\TestCase
{
    const VALUE = 'a b+c/ä?x=1&y=~';

    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/rawurlencode_operator_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    public function testTheShippedSettingsListBothOperators()
    {
        $ini = new eZINI( 'template.ini', 'settings', null, false, null, true );
        $list = $ini->variable( 'PHP', 'PHPOperatorList' );
        $this->assertSame( 'rawurlencode', $list['rawurlencode'] );
        $this->assertSame( 'rawurldecode', $list['rawurldecode'] );
        $this->assertSame( 'urlencode', $list['urlencode'], 'the existing entries stay' );
        $this->assertSame( 'urldecode', $list['urldecode'] );
    }

    public function testThePHPOperatorEncodesAndDecodes()
    {
        $operator = new eZTemplatePHPOperator( array( 'rawurlencode' => 'rawurlencode', 'rawurldecode' => 'rawurldecode' ) );
        $this->assertContains( 'rawurlencode', $operator->operatorList() );

        $value = self::VALUE;
        $operator->modify( null, 'rawurlencode', array(), '', '', $value, array(), array() );
        $this->assertSame( 'a%20b%2Bc%2F%C3%A4%3Fx%3D1%26y%3D~', $value );

        $operator->modify( null, 'rawurldecode', array(), '', '', $value, array(), array() );
        $this->assertSame( self::VALUE, $value );
    }

    public function testATemplateRendersBothOperators()
    {
        $file = $this->dir . '/raw.tpl';
        file_put_contents( $file, '{$value|rawurlencode}|{$value|urlencode}|{$encoded|rawurldecode}' );

        // The process may hold template.ini from var/cache/ini, built before the entries were added (config.php
        // turns the INI file time check off), and the list is read only (site.ini [eZINISettings]
        // ReadonlySettingList), so it can not be set in-process: a new template instance registers the list of
        // the files themselves, as eZPHPOperatorInit() does with the cached one.
        $fresh = new eZINI( 'template.ini', 'settings', null, false );
        $list = $fresh->variable( 'PHP', 'PHPOperatorList' );
        eZTemplate::resetInstance();
        try
        {
            $tpl = eZTemplate::factory();
            $tpl->registerAutoloadOperators( array( 'class' => 'eZTemplatePHPOperator',
                                                    'class_parameter' => $list,
                                                    'operator_names' => array_keys( $list ) ) );
            $tpl->setVariable( 'value', 'a b' );
            $tpl->setVariable( 'encoded', 'x%20y%2Bz' );
            $this->assertSame( 'a%20b|a+b|x y+z', trim( $tpl->fetch( $file ) ) );
        }
        finally
        {
            eZTemplate::resetInstance();
        }
    }

    /**
     * Renders $source with $variables, interpreted and compiled, and gives array( mode => array( output, PHP
     * notices ) ). The operators are registered from the shipped list as in testATemplateRendersBothOperators().
     */
    private function renderBothWays( $source, array $variables )
    {
        $file = $this->dir . '/' . md5( $source ) . '.tpl';
        file_put_contents( $file, $source );
        $fresh = new eZINI( 'template.ini', 'settings', null, false );
        $list = $fresh->variable( 'PHP', 'PHPOperatorList' );
        $saved = array();
        foreach ( array( 'eZTemplateCompilerSettings', 'eZSiteBasics' ) as $name )
            $saved[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $results = array();
        try
        {
            foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
            {
                if ( $compiled )
                {
                    unset( $GLOBALS['eZSiteBasics'] );
                    $GLOBALS['eZTemplateCompilerSettings']['compile'] = true;
                    $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $this->dir;
                }
                else
                {
                    $GLOBALS['eZTemplateCompilerSettings']['compile'] = false;
                    $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
                }
                $notices = array();
                set_error_handler( function ( $no, $str, $f, $l ) use ( &$notices )
                {
                    $notices[] = $str . ' (' . basename( $f ) . ':' . $l . ')';
                    return true;
                } );
                eZTemplate::resetInstance();
                try
                {
                    $tpl = new eZTemplate();
                    $tpl->autoload();
                    $tpl->registerAutoloadOperators( array( 'class' => 'eZTemplatePHPOperator',
                                                            'class_parameter' => $list,
                                                            'operator_names' => array_keys( $list ) ) );
                    foreach ( $variables as $name => $value )
                        $tpl->setVariable( $name, $value );
                    $output = $tpl->fetch( $file );
                }
                finally
                {
                    restore_error_handler();
                }
                $results[$mode] = array( $output, $notices );
            }
        }
        finally
        {
            eZTemplate::resetInstance();
            foreach ( $saved as $name => $value )
            {
                if ( $value === null )
                    unset( $GLOBALS[$name] );
                else
                    $GLOBALS[$name] = $value[0];
            }
            foreach ( glob( $this->dir . '/*/' ) ?: array() as $sub )
                self::removeTree( $sub );
        }
        return $results;
    }

    private static function removeTree( $path )
    {
        if ( is_dir( $path ) && !is_link( $path ) )
        {
            foreach ( scandir( $path ) as $entry )
            {
                if ( $entry !== '.' && $entry !== '..' )
                    self::removeTree( rtrim( $path, '/' ) . '/' . $entry );
            }
            rmdir( $path );
        }
        else if ( file_exists( $path ) )
            unlink( $path );
    }

    /** Numbers, the empty text and the RFC 3986 characters, interpreted and compiled */
    public function testValuesOfEveryKindBothWays()
    {
        $source = '[{$n|rawurlencode}][{$e|rawurlencode}][{$u|rawurlencode}][{$p|rawurldecode}][{$q|rawurldecode}]';
        $variables = array( 'n' => 42, 'e' => '', 'u' => '-_.~ !*\'()', 'p' => 'a+b%20c', 'q' => '%E2%82%AC' );
        foreach ( $this->renderBothWays( $source, $variables ) as $mode => $result )
        {
            list( $output, $notices ) = $result;
            $this->assertSame( array(), $notices, "$mode: no PHP notices" );
            $this->assertSame( "[42][][-_.~%20%21%2A%27%28%29][a+b c][\u{20AC}]", $output, $mode );
        }
    }

    /** A value that is not set (null) gives an empty text, without a PHP 8.1 null deprecation */
    public function testAnUnsetValueGivesAnEmptyText()
    {
        $source = '[{$missing|rawurlencode}][{$nothing|rawurldecode}][{$nothing|urlencode}]';
        foreach ( $this->renderBothWays( $source, array( 'nothing' => null ) ) as $mode => $result )
        {
            list( $output, $notices ) = $result;
            $this->assertSame( array(), $notices, "$mode: no PHP notices" );
            $this->assertSame( '[][][]', $output, $mode );
        }
    }
}

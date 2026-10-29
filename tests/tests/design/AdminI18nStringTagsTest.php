<?php
/**
 * Every {'text'|i18n( 'context' )} tag of the RAD wizard pages renders as text. A '}' inside the quoted text closes
 * the tag early: Setup > RAD > Template extension printed "{$value|myoperator — takes the value on the left and gives
 * one back.'|i18n( 'design/admin/setup/rad/template' )}" as the help of three fields. Inside a quoted string the
 * closing brace is written \}.
 *
 * The tags are taken out of the templates of every admin design and rendered by the template engine, interpreted and
 * compiled, one template per file: none may leave "|i18n" or a quote followed by a pipe in the output, and a tag
 * whose text has a brace keeps that brace.
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group design
 */

class AdminI18nStringTagsTest extends PHPUnit\Framework\TestCase
{
    /** A complete i18n tag with a literal text and a literal context, as a template writes it. */
    const TAG = "/\\{'((?:[^'\\\\]|\\\\.)*)'\\|i18n\\(\\s*'[^']*'\\s*\\)\\}/";

    /** @var string */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 3 ) );
        self::$dir = 'var/tmp/phpunit-admin-i18n-tags-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        foreach ( array( self::$dir . '/compiled', self::$dir ) as $dir )
        {
            foreach ( glob( $dir . '/*' ) ?: array() as $file )
                is_file( $file ) && unlink( $file );
        }
        @rmdir( self::$dir . '/compiled' );
        @rmdir( self::$dir );
    }

    protected function setUp(): void
    {
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

    /**
     * The RAD wizard pages of every admin design.
     */
    public static function templateProvider()
    {
        $root = dirname( __DIR__, 3 );
        $cases = array();
        foreach ( glob( $root . '/design/admin*/templates/setup/*.tpl' ) as $file )
        {
            $path = substr( $file, strlen( $root ) + 1 );
            foreach ( array( false, true ) as $compiled )
                $cases[$path . ( $compiled ? ', compiled' : '' )] = array( $path, $compiled );
        }
        return $cases;
    }

    public function testTheTagPatternFindsTheFault()
    {
        $this->assertSame( 1, preg_match_all( self::TAG, "{'{\$value|op} takes it.'|i18n( 'design/admin/setup/rad/template' )}" ) );
        $this->assertSame( 1, preg_match_all( self::TAG, "{'{\$value|op\\} takes it.'|i18n( 'design/admin/setup/rad/template' )}" ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('templateProvider')]
    public function testI18nTagsRenderAsText( $path, $compiled )
    {
        preg_match_all( self::TAG, file_get_contents( $path ), $tags );
        if ( !count( $tags[0] ) )
        {
            $this->assertTrue( true );
            return;
        }

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

        // One tag per line, between markers, so each tag's output can be told apart
        $source = '';
        foreach ( $tags[0] as $i => $tag )
            $source .= "<<$i>>" . $tag . "<</$i>>\n";
        $file = self::$dir . '/' . md5( $path . $source ) . '.tpl';
        file_put_contents( $file, $source );

        $tpl = new eZTemplate();
        $tpl->autoload();
        $output = $tpl->fetch( $file );

        $this->assertSame( array(), $tpl->errorLog(), $path );
        foreach ( $tags[1] as $i => $text )
        {
            $this->assertSame( 1, preg_match( "#<<$i>>(.*?)<</$i>>#s", $output, $match ), "$path: tag $i left no output" );
            $rendered = $match[1];
            $this->assertStringNotContainsString( '|i18n', $rendered, "$path: " . $tags[0][$i] );
            $this->assertStringNotContainsString( "'|", $rendered, "$path: " . $tags[0][$i] );
            // an escaped brace is printed as a brace, without its backslash
            $this->assertStringNotContainsString( '\\}', $rendered, "$path: " . $tags[0][$i] );
            // a brace written in the text (escaped or not) is part of the text
            foreach ( array( '{', '}' ) as $brace )
            {
                if ( strpos( stripslashes( $text ), $brace ) !== false )
                    $this->assertStringContainsString( $brace, $rendered, "$path: " . $tags[0][$i] );
            }
        }
    }
}

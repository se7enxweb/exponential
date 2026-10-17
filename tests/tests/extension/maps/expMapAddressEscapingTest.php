<?php
/**
 * The address of a map location reaches the page escaped once and in the right order, in every template that prints
 * it (ezgmaplocation 6.0.8, ezflow 6.1.6, cjw_themes_jumper 0.1.4), and fromString() of the datatype reads its four
 * fields safely. No database.
 *
 *  MA-01 - Where a template puts the address (or the popup title) into a script, it is |wash|wash( 'javascript' ):
 *          the map libraries hand it to setContent(), which reads HTML. Never wash( 'javascript' ) alone.
 *  MA-02 - The view and edit templates of the datatype print the address with |wash, never raw: it is stored as typed
 *  MA-03 - The pipeline as the templates write it, rendered by a bare eZTemplate (interpreted and compiled): the script
 *          literal holds no quote, < or >, and what the browser reads from it is the HTML-escaped address
 *  MA-05 - The view and edit templates make the coordinates strings (concat) before explode(): a driver that returns
 *          floats (SQLite, PDO) stopped the edit page at implode()
 *  MA-04 - fromString(): four fields store the location; three keep the coordinates with an empty address; fewer, or
 *          coordinates that are not numbers, store nothing; an address with "|#" in it is kept whole
 *
 * Each extension is installed by Composer into extension/<name>; a test whose extension is missing is skipped.
 *
 * Run: php vendor/bin/phpunit --testsuite maps
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group eztemplate
 */

class expMapAddressEscapingTest extends PHPUnit\Framework\TestCase
{
    /** Templates that put an address into a script: file => the variables that go into HTML through the script */
    const SCRIPT_TEMPLATES = array(
        'extension/cjw_themes_jumper/design/starter/templates/content/parts/leaflet_map_single.tpl' => array( '$lm_loc.address', '$lm_title' ),
        'extension/ezflow/design/ezflow/override/templates/block/geo_located_content.tpl' => array( '$location.data_map[$attribute].content.address' ),
    );

    const VIEW = 'extension/ezgmaplocation/design/standard/templates/content/datatype/view/ezgmaplocation.tpl';

    const EDIT = 'extension/ezgmaplocation/design/standard/templates/content/datatype/edit/ezgmaplocation.tpl';

    const DATATYPE = 'extension/ezgmaplocation/datatypes/ezgmaplocation/ezgmaplocationtype.php';

    const HOSTILE = "Main St. 1 </script><img src=x onerror=alert(1)> \"quoted\" 'single' & M\u{00FC}ller";

    /** @var string private directory of the rendered templates */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-map-address-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
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
        if ( is_dir( $path ) && !is_link( $path ) )
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

    private function source( $file )
    {
        if ( !is_file( $file ) )
            $this->markTestSkipped( "$file is not installed" );
        return file_get_contents( $file );
    }

    /** Every {$variable|...} output of $variable in $source: array of the operator pipelines after the variable */
    private static function pipelines( $source, $variable )
    {
        preg_match_all( '/\{' . preg_quote( $variable, '/' ) . '((?:\|[^}]*)?)\}/', $source, $matches );
        return $matches[1];
    }

    /** The source without its template comments, so that a comment explaining the escaping is not taken for code */
    private static function code( $source )
    {
        return preg_replace( '/\{\*.*?\*\}/s', '', $source );
    }

    /** MA-01 */
    public function testScriptsEscapeForHtmlThenForJavascript()
    {
        foreach ( self::SCRIPT_TEMPLATES as $file => $variables )
        {
            $source = self::code( $this->source( $file ) );
            foreach ( $variables as $variable )
            {
                // only the outputs inside a <script> block
                $found = 0;
                preg_match_all( '#<script\b[^>]*>(.*?)</script>#s', $source, $scripts );
                foreach ( $scripts[1] as $script )
                {
                    foreach ( self::pipelines( $script, $variable ) as $pipeline )
                    {
                        ++$found;
                        $this->assertMatchesRegularExpression( '/^\|wash\|wash\(\s*["\']javascript["\']\s*\)$/', $pipeline,
                                                               "$file: $variable in a script is escaped for HTML, then for JavaScript" );
                    }
                }
                $this->assertGreaterThan( 0, $found, "$file writes $variable into a script" );
            }
        }
    }

    /** MA-02 */
    public function testTheDatatypeTemplatesEscapeTheAddress()
    {
        foreach ( array( self::VIEW, self::EDIT ) as $file )
        {
            $pipelines = self::pipelines( self::code( $this->source( $file ) ), '$attribute.content.address' );
            $this->assertNotEmpty( $pipelines, "$file prints the address" );
            foreach ( $pipelines as $pipeline )
                $this->assertSame( '|wash', $pipeline, "$file prints the address once escaped for HTML" );
        }
    }

    /** The template rendered with $a, interpreted and compiled; fails on a PHP notice */
    private function render( $template, $value )
    {
        $file = self::$dir . '/t' . md5( $template ) . '.tpl';
        file_put_contents( $file, $template );
        $outputs = array();
        foreach ( array( 'interpreted' => false, 'compiled' => true ) as $mode => $compiled )
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
            $notices = array();
            set_error_handler( function ( $no, $str, $f, $l ) use ( &$notices )
            {
                $notices[] = $str . ' (' . basename( $f ) . ':' . $l . ')';
                return true;
            } );
            try
            {
                $tpl = new eZTemplate();
                $tpl->autoload();
                $tpl->setVariable( 'a', $value );
                $outputs[$mode] = $tpl->fetch( $file );
            }
            finally
            {
                restore_error_handler();
            }
            $this->assertSame( array(), $notices, "$mode: PHP notices" );
        }
        $this->assertSame( $outputs['interpreted'], $outputs['compiled'], 'the same output interpreted and compiled' );
        return $outputs['compiled'];
    }

    /** What a JavaScript engine reads from the escapes that wash( 'javascript' ) writes */
    private static function javascriptValue( $literal )
    {
        return preg_replace_callback( '/\\\\(x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|[0-7]{3}|[\\\\rn])/', function ( $m )
        {
            $e = $m[1];
            if ( $e === '\\' ) return '\\';
            if ( $e === 'r' ) return "\r";
            if ( $e === 'n' ) return "\n";
            if ( $e[0] === 'x' ) return chr( hexdec( substr( $e, 1 ) ) );
            if ( $e[0] === 'u' ) return mb_chr( hexdec( substr( $e, 1 ) ), 'UTF-8' );
            return chr( octdec( $e ) );
        }, $literal );
    }

    /** MA-03 */
    public function testThePipelineGivesTheBrowserTheHtmlEscapedAddress()
    {
        $pipelines = array();
        foreach ( self::SCRIPT_TEMPLATES as $file => $variables )
        {
            if ( !is_file( $file ) )
                continue;
            $source = self::code( file_get_contents( $file ) );
            foreach ( $variables as $variable )
            {
                foreach ( self::pipelines( $source, $variable ) as $pipeline )
                {
                    if ( strpos( $pipeline, 'javascript' ) !== false )
                        $pipelines[$pipeline] = true;
                }
            }
        }
        if ( !$pipelines )
            $this->markTestSkipped( 'no map template is installed' );

        foreach ( array_keys( $pipelines ) as $pipeline )
        {
            $literal = $this->render( "{\$a$pipeline}", self::HOSTILE );
            foreach ( array( '<', '>', "'", '"', "\n" ) as $character )
                $this->assertStringNotContainsString( $character, $literal, "$pipeline: no raw " . json_encode( $character ) . ' in the script' );
            $this->assertSame( htmlspecialchars( self::HOSTILE, ENT_QUOTES, 'UTF-8' ), self::javascriptValue( $literal ),
                               "$pipeline: setContent() receives the address escaped for HTML once" );
        }
    }

    /** MA-05 */
    public function testTheCoordinatesAreStringsBeforeExplode()
    {
        foreach ( array( self::VIEW, self::EDIT ) as $file )
        {
            $code = self::code( $this->source( $file ) );
            preg_match_all( '/\$attribute\.content\.(?:latitude|longitude)\s*\|explode/', $code, $raw );
            $this->assertSame( array(), $raw[0], "$file: a coordinate goes through concat() before explode()" );
            $this->assertMatchesRegularExpression( '/concat\(\s*\$attribute\.content\.latitude,\s*\'\'\s*\)\|explode\(\s*\',\'\s*\)\|implode\(\s*\'\.\'\s*\)/', $code );
        }
        // a database driver that returns the coordinate as a float (SQLite, PDO)
        $this->assertSame( '51.3397', $this->render( "{concat( \$a, '' )|explode(',')|implode('.')}", 51.3397 ) );
        $this->assertSame( '51.3397', $this->render( "{concat( \$a, '' )|explode(',')|implode('.')}", '51,3397' ) );
    }

    private function datatype()
    {
        if ( !is_file( self::DATATYPE ) )
            $this->markTestSkipped( 'ezgmaplocation is not installed' );
        if ( !class_exists( 'eZGmapLocation' ) )
            require_once 'extension/ezgmaplocation/classes/ezgmaplocation.php';
        if ( !class_exists( 'eZGmapLocationType' ) )
            require_once self::DATATYPE;
        return new eZGmapLocationType();
    }

    /** fromString() on a fresh attribute: array( data_int, latitude, longitude, address ) or array( data_int ) */
    private function fromString( $string )
    {
        $attribute = new eZContentObjectAttribute( array( 'id' => 4711, 'version' => 1, 'data_int' => 0 ) );
        $this->datatype()->fromString( $attribute, $string );
        $location = $attribute->Content;
        if ( !$location instanceof eZGmapLocation )
            return array( (int)$attribute->attribute( 'data_int' ) );
        return array( (int)$attribute->attribute( 'data_int' ), $location->attribute( 'latitude' ),
                      $location->attribute( 'longitude' ), $location->attribute( 'address' ) );
    }

    /** MA-04 */
    public function testFromStringReadsItsFieldsSafely()
    {
        $this->assertSame( array( 1, 52.5, 13.4, 'M&uuml;llerstra&szlig;e & <5>' ), $this->fromString( '1|#52.5|#13.4|#M&uuml;llerstra&szlig;e & <5>' ),
                           'the address is stored as given, not decoded or encoded' );
        $this->assertSame( array( 1, 52.5, 13.4, '' ), $this->fromString( '1|#52.5|#13.4' ), 'no address' );
        $this->assertSame( array( 1, 52.5, 13.4, 'Hall A |# B' ), $this->fromString( '1|#52.5|#13.4|#Hall A |# B' ), 'an address with "|#" in it' );
        $this->assertSame( array( 0 ), $this->fromString( '1|#52.5' ), 'one coordinate' );
        $this->assertSame( array( 0 ), $this->fromString( '1|#52.5|#alert(1)|#x' ), 'a coordinate that is no number' );
        $this->assertSame( array( 0 ), $this->fromString( '' ) );
        $this->assertSame( array( 0 ), $this->fromString( '0|#52.5|#13.4|#Somewhere' ), 'has_content 0 stores nothing' );
    }
}

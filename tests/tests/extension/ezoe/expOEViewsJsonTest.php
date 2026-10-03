<?php
/**
 * The ezoe views answer what the editor's dialogs parse: JSON with nothing in front of it. A stray byte before
 * the JSON (an ?> followed by code in module.php once printed the source of the module in front of every answer)
 * makes the editor's JSON.parse fail, so these tests look at the raw output.
 */

require_once __DIR__ . '/expOETestCase.php';

class expOEViewsJsonTest extends expOETestCase
{
    protected function root()
    {
        return dirname( __DIR__, 4 );
    }

    public static function phpFiles()
    {
        $root = dirname( __DIR__, 4 ) . '/extension/ezoe';
        $files = array();
        foreach ( array( 'modules/ezoe', 'classes', 'ezxmltext/handlers/input', 'autoloads', 'settings' ) as $dir )
        {
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/' . $dir, FilesystemIterator::SKIP_DOTS ) ) as $f )
            {
                if ( substr( $f->getFilename(), -4 ) === '.php' )
                    $files[] = array( substr( $f->getPathname(), strlen( $root ) + 1 ) );
            }
        }
        return $files;
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'phpFiles' )]
    public function testNoOutputBeforeOrAfterThePhpCode( $file )
    {
        $source = file_get_contents( $this->root() . '/extension/ezoe/' . $file );
        $this->assertStringStartsWith( '<?php', $source, "$file starts with text" );
        $last = strrpos( $source, '?>' );
        if ( $last !== false )
        {
            $after = substr( $source, $last + 2 );
            // the ini files wrap their settings in a comment that closes just before the tag
            $this->assertSame( '', trim( $after ), "$file has output after its closing tag" );
        }
    }

    public function testTheModuleDefinitionPrintsNothing()
    {
        ob_start();
        ( function () { $Module = array(); $ViewList = array(); $FunctionList = array(); include dirname( __DIR__, 4 ) . '/extension/ezoe/modules/ezoe/module.php'; } )();
        $this->assertSame( '', ob_get_clean() );
    }

    public function testTheModuleDefinesTheEngineView()
    {
        $ViewList = array();
        $FunctionList = array();
        $Module = array();
        ob_start();
        include $this->root() . '/extension/ezoe/modules/ezoe/module.php';
        ob_end_clean();
        $this->assertArrayHasKey( 'engine', $ViewList );
        $this->assertSame( 'engine.php', $ViewList['engine']['script'] );
        $this->assertArrayHasKey( 'load', $ViewList );
        $this->assertArrayHasKey( 'upload', $ViewList );
    }

    public function testEveryViewScriptExists()
    {
        $ViewList = array();
        $FunctionList = array();
        $Module = array();
        ob_start();
        include $this->root() . '/extension/ezoe/modules/ezoe/module.php';
        ob_end_clean();
        foreach ( $ViewList as $name => $view )
            $this->assertFileExists( $this->root() . '/extension/ezoe/modules/ezoe/' . $view['script'], $name );
    }

    public function testLoadAnswersJsonForAnObject()
    {
        $out = $this->runView( 'load', array( '108' ) );
        $this->assertStringStartsWith( '{', $out );
        $json = json_decode( $out, true );
        $this->assertSame( JSON_ERROR_NONE, json_last_error(), substr( $out, 0, 200 ) );
        $this->assertSame( 108, $json['id'] );
    }

    public function testLoadAnswersJsonForANode()
    {
        $out = $this->runView( 'load', array( 'eznode_110' ) );
        $json = json_decode( $out, true );
        $this->assertSame( JSON_ERROR_NONE, json_last_error(), substr( $out, 0, 200 ) );
        $this->assertSame( 108, $json['contentobject_id'] );
    }

    public function testLoadAnswersJsonForAnObjectGivenAsEzobject()
    {
        $json = json_decode( $this->runView( 'load', array( 'ezobject_108' ) ), true );
        $this->assertSame( 108, $json['id'] );
    }

    public function testLoadAnswersFalseForAMissingObject()
    {
        $this->assertSame( 'false', trim( $this->runView( 'load', array( '99999999' ) ) ) );
    }

    public function testLoadHasNoTextInFrontOfTheJson()
    {
        $out = $this->runView( 'load', array( '108' ) );
        $this->assertSame( '{', $out[0] );
        $this->assertStringNotContainsString( '$ViewList', $out );
        $this->assertStringNotContainsString( '<?php', $out );
    }

    public function testLoadAnswersFalseToAnonymousForAnObjectItCannotRead()
    {
        $out = trim( $this->runView( 'load', array( '14' ), array(), 'anonymous' ) );
        $this->assertTrue( $out === 'false' || ( $out[0] === '{' && is_array( json_decode( $out, true ) ) ), substr( $out, 0, 100 ) );
    }

    public function testBrowseReturnsAnEncodableList()
    {
        $result = ezoeServerFunctions::browse( array( 43, 0, 5 ) );
        $json = json_encode( $result );
        $this->assertNotFalse( $json );
        $decoded = json_decode( $json, true );
        $this->assertArrayHasKey( 'list', $decoded );
        $this->assertArrayHasKey( 'count', $decoded );
    }

    public function testBrowseRefusesAMissingNode()
    {
        $this->expectException( ezcBaseFunctionalityNotSupportedException::class );
        ezoeServerFunctions::browse( array( 99999999 ) );
    }

    public function testBookmarksReturnsAnEncodableList()
    {
        $result = ezoeServerFunctions::bookmarks( array( 0, 5 ) );
        $this->assertNotFalse( json_encode( $result ) );
        $this->assertArrayHasKey( 'list', $result );
    }

    public function testTheEzjscoreFunctionsOfEzoeAreRegistered()
    {
        $ini = eZINI::instance( 'ezjscore.ini' );
        $this->assertSame( 'ezoeServerFunctions', $ini->variable( 'ezjscServer_ezoe', 'Class' ) );
    }

    public function testThePullRequestAddsNoServerFunction()
    {
        // the TinyMCE 8 plugins use the existing ezoe views and ezjscore functions only
        $methods = array();
        foreach ( ( new ReflectionClass( 'ezoeServerFunctions' ) )->getMethods( ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC ) as $m )
        {
            if ( $m->class === 'ezoeServerFunctions' )
                $methods[] = $m->name;
        }
        sort( $methods );
        $this->assertSame( array( 'bookmarks', 'browse', 'getCacheTime', 'i18n' ), $methods );
    }
}

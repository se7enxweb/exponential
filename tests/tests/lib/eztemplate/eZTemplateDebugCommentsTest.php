<?php
/**
 * The debug comments of the template engine and the templates that may not get them.
 *
 * [TemplateSettings]Debug and ShowTemplatePathComments wrap every included template in HTML comments. A template that
 * prints into a start tag, such as parts/exp_content_direction.tpl in <h1 class="title"{include ...}>, broke the tag
 * that way: the browser ended it at the first ">" of the comment and showed a stray ">" in the page.
 * [TemplateSettings]TemplatesWithoutDebugComments[] names such templates by their path below templates/; both kinds of
 * comments leave them out, interpreted and compiled.
 *
 * No kernel bootstrap, no database and no siteaccess: a bare eZTemplate with the lib/eztemplate autoloads.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group eztemplate
 */

class eZTemplateDebugCommentsTest extends PHPUnit\Framework\TestCase
{
    /** @var string directory of this class's template files and compiled templates */
    private static $dir;

    /** @var string working directory before this class, restored afterwards */
    private static $previousCwd;

    private $savedGlobals = array();

    private $savedSetting;

    private const GLOBAL_NAMES = array(
        'eZTemplateCompilerSettings', 'eZSiteBasics', 'eZTemplateDebugInternalsEnabled',
        'eZTemplateDebugXHTMLCodeEnabled', 'eZTemplateShowTemplatePathComments', 'eZTemplateTemplatesWithoutDebugComments',
        'eZCurrentAccess', 'eZTemplateDebugCommentsCompilationKey',
    );

    public static function setUpBeforeClass(): void
    {
        self::$previousCwd = getcwd();
        $root = dirname( __DIR__, 4 );
        chdir( $root );
        self::$dir = 'var/tmp/phpunit-eztemplate-debugcomments-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
        mkdir( self::$dir . '/design/test/templates/parts', 0777, true );
        file_put_contents( self::$dir . '/design/test/templates/parts/exp_content_direction.tpl', ' lang="he-IL" dir="rtl"' );
        file_put_contents( self::$dir . '/design/test/templates/parts/other.tpl', 'other' );
        // The override lookup of a path comment registers eZExecution's handlers once per process; done here, a run
        // of this class alone does not report the first test as installing an exception handler
        eZExecution::registerShutdownHandler();
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
        if ( self::$previousCwd )
            chdir( self::$previousCwd );
    }

    protected function setUp(): void
    {
        foreach ( self::GLOBAL_NAMES as $name )
            $this->savedGlobals[$name] = array_key_exists( $name, $GLOBALS ) ? array( $GLOBALS[$name] ) : null;
        $ini = eZINI::instance();
        $this->savedSetting = $ini->hasVariable( 'TemplateSettings', 'TemplatesWithoutDebugComments' )
            ? array( $ini->variable( 'TemplateSettings', 'TemplatesWithoutDebugComments' ) )
            : null;
        $ini->setVariable( 'TemplateSettings', 'TemplatesWithoutDebugComments', array( 'parts/exp_content_direction.tpl' ) );
        unset( $GLOBALS['eZTemplateTemplatesWithoutDebugComments'] );
        // the path comment of an include looks up the override source, which reads the current siteaccess
        if ( !isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $GLOBALS['eZCurrentAccess'] = array( 'name' => 'phpunit', 'type' => 1 );
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
        $ini = eZINI::instance();
        if ( $this->savedSetting === null )
            $ini->removeSetting( 'TemplateSettings', 'TemplatesWithoutDebugComments' );
        else
            $ini->setVariable( 'TemplateSettings', 'TemplatesWithoutDebugComments', $this->savedSetting[0] );
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

    /**
     * Renders a page that includes both parts, the direction part inside a start tag and captured by set-block,
     * with the given kind of debug comments switched on, compiled or interpreted.
     */
    private function render( $kind, $compiled )
    {
        $this->setUpRender( $kind, $compiled );

        $parts = self::$dir . '/design/test/templates/parts';
        $source = '<h1 class="title"{include uri="file:' . $parts . '/exp_content_direction.tpl"}>T</h1>'
                . '{include uri="file:' . $parts . '/other.tpl"}'
                . '{set-block variable=attrs}{include uri="file:' . $parts . '/exp_content_direction.tpl"}{/set-block}'
                . '[{$attrs}]';
        // one page file per kind and mode: the compiler keys its output on the file
        $file = self::$dir . '/page-' . $kind . '-' . ( $compiled ? 'compiled' : 'interpreted' ) . '.tpl';
        file_put_contents( $file, $source );

        $tpl = new eZTemplate();
        $tpl->autoload();
        $output = $tpl->fetch( $file );
        $this->assertSame( array(), $tpl->errorLog() );
        return $output;
    }

    public static function kindAndModeProvider()
    {
        return array(
            'Debug, interpreted'                    => array( 'debug', false ),
            'Debug, compiled'                       => array( 'debug', true ),
            'ShowTemplatePathComments, interpreted' => array( 'path-comments', false ),
            'ShowTemplatePathComments, compiled'    => array( 'path-comments', true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('kindAndModeProvider')]
    public function testAPartPrintedIntoAStartTagGetsNoComments( $kind, $compiled )
    {
        $output = $this->render( $kind, $compiled );
        $this->assertStringContainsString( '<h1 class="title" lang="he-IL" dir="rtl">T</h1>', $output );
        $this->assertStringNotContainsString( 'exp_content_direction.tpl', $output );
        // captured with set-block, the part is exactly its attributes, so a test of it for '' still works
        $this->assertStringContainsString( '[ lang="he-IL" dir="rtl"]', $output );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('kindAndModeProvider')]
    public function testOtherTemplatesKeepTheirComments( $kind, $compiled )
    {
        $output = $this->render( $kind, $compiled );
        $this->assertMatchesRegularExpression( '#<!-- START[^>]*parts/other\.tpl[^>]*-->\s*other\s*<!-- STOP[^>]*parts/other\.tpl#', $output );
    }

    public function testTheSettingMatchesThePathBelowTemplatesInAnyDesign()
    {
        $this->assertFalse( eZTemplate::templateAllowsDebugComments( 'design/standard/templates/parts/exp_content_direction.tpl' ) );
        $this->assertFalse( eZTemplate::templateAllowsDebugComments( 'extension/site/design/site/templates/parts/exp_content_direction.tpl' ) );
        $this->assertFalse( eZTemplate::templateAllowsDebugComments( 'design\\admin\\templates\\parts\\exp_content_direction.tpl' ) );
        $this->assertTrue( eZTemplate::templateAllowsDebugComments( 'design/standard/templates/parts/other.tpl' ) );
        $this->assertTrue( eZTemplate::templateAllowsDebugComments( 'design/standard/templates/myparts/exp_content_direction.tpl' ) );
        $this->assertTrue( eZTemplate::templateAllowsDebugComments( '' ) );
    }

    public function testWithoutTheSettingEveryTemplateMayHaveComments()
    {
        eZINI::instance()->removeSetting( 'TemplateSettings', 'TemplatesWithoutDebugComments' );
        unset( $GLOBALS['eZTemplateTemplatesWithoutDebugComments'] );
        $this->assertTrue( eZTemplate::templateAllowsDebugComments( 'design/standard/templates/parts/exp_content_direction.tpl' ) );
    }

    public function testTheVerdictIsAKeyLookupKeptPerPath()
    {
        $path = 'design/standard/templates/parts/exp_content_direction.tpl';
        $this->assertFalse( eZTemplate::templateAllowsDebugComments( $path ) );
        $lookup = $GLOBALS['eZTemplateTemplatesWithoutDebugComments'];
        $this->assertSame( array( 'parts/exp_content_direction.tpl' => true ), $lookup['names'] );
        $this->assertSame( false, $lookup['verdicts'][$path] );
        // a templates/ directory deeper in the path is matched from each /templates/ on
        $this->assertFalse( eZTemplate::templateAllowsDebugComments( 'extension/x/templates/design/y/templates/parts/exp_content_direction.tpl' ) );
    }

    /**
     * The default of site.ini: the direction part; the other in-tag and captured parts need no entry.
     */
    public function testTheDefaultOfSiteIniNamesTheDirectionPart()
    {
        $ini = file_get_contents( dirname( __DIR__, 4 ) . '/settings/site.ini' );
        $this->assertStringContainsString( "\nTemplatesWithoutDebugComments[]\nTemplatesWithoutDebugComments[]=parts/exp_content_direction.tpl\n", $ini );
    }

    private static function comment( $marker, $name )
    {
        return eZTemplate::templatePathComment( $marker, "design/test/templates/$name" );
    }

    public static function misplacedProvider()
    {
        return array(
            'between attributes'    => array( '<a href="x"', ' class="y">t</a>' ),
            'in an attribute value' => array( '<a title="', 'x">t</a>' ),
            'in a single quoted one' => array( "<a title='", "x'>t</a>" ),
            'in an end tag'          => array( '<p>t</p', '>' ),
            'in a script'            => array( '<p>t</p><script>var a = "', '";</script>' ),
            'in a style'             => array( '<style>.a { color: ', 'red }</style>' ),
            'in a title'             => array( '<title>', 'T</title>' ),
            'in a textarea'          => array( '<textarea name="a">', 'v</textarea>' ),
            'in another comment'     => array( '<p>t</p><!-- note ', ' -->' ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('misplacedProvider')]
    public function testAMisplacedCommentIsTakenOut( $before, $after )
    {
        $wrapped = '<div>' . self::comment( 'START', 'a.tpl' ) . 'a' . self::comment( 'STOP', 'a.tpl' ) . '</div>';
        $text = $wrapped . $before . self::comment( 'START', 'b.tpl' ) . 'b' . self::comment( 'STOP', 'b.tpl' ) . $after . $wrapped;
        $this->assertSame( $wrapped . $before . 'b' . $after . $wrapped, eZTemplate::removeMisplacedDebugComments( $text ) );
    }

    public function testCommentsBetweenElementsAndAfterAClosedScriptStay()
    {
        $text = '<script>if ( a > b ) x = "<p>";</script><p class="a>b">' . self::comment( 'START', 'a.tpl' ) . 'a' .
                self::comment( 'STOP', 'a.tpl' ) . '</p><textarea></textarea>' . self::comment( 'START', 'b.tpl' );
        $this->assertSame( $text, eZTemplate::removeMisplacedDebugComments( $text ) );
    }

    public static function notHTMLProvider()
    {
        return array(
            'XML'        => array( '<?xml version="1.0"?><rss><channel>', '</channel></rss>' ),
            'JSON'       => array( '{"a": "', '"}' ),
            'plain text' => array( "Hello,\n\nyour order: ", "\nhttps://example.com/\n" ),
            'iCal'       => array( "BEGIN:VCALENDAR\nSUMMARY:", "\nEND:VCALENDAR\n" ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('notHTMLProvider')]
    public function testOutputThatIsNotHTMLHasNoComments( $before, $after )
    {
        $text = self::comment( 'START', 'page.tpl' ) . $before . self::comment( 'START', 'b.tpl' ) . 'b' . self::comment( 'STOP', 'b.tpl' ) .
                $after . self::comment( 'STOP', 'page.tpl' );
        $this->assertSame( $before . 'b' . $after, eZTemplate::removeMisplacedDebugComments( $text ) );
    }

    public function testOutputWithoutDebugCommentsIsReturnedAsItIs()
    {
        $text = "<p>a</p>\n<!-- a comment of the template -->\n<p title=\"<!-- x -->\">b</p>";
        $this->assertSame( $text, eZTemplate::removeMisplacedDebugComments( $text ) );
    }

    /**
     * A part printed into a start tag that is not in the list: the comments are taken out of the rendered page.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('kindAndModeProvider')]
    public function testAnUnlistedPartInAStartTagLosesItsCommentsInThePage( $kind, $compiled )
    {
        $parts = self::$dir . '/design/test/templates/parts';
        file_put_contents( $parts . '/unlisted_attributes.tpl', ' data-x="1"' );
        $this->setUpRender( $kind, $compiled );
        $file = self::$dir . '/unlisted-' . $kind . '-' . ( $compiled ? 'compiled' : 'interpreted' ) . '.tpl';
        file_put_contents( $file, '<p{include uri="file:' . $parts . '/unlisted_attributes.tpl"}>T</p>'
                                . '<script>var a = "{include uri="file:' . $parts . '/other.tpl"}";</script>'
                                . '{include uri="file:' . $parts . '/other.tpl"}' );
        $tpl = new eZTemplate();
        $tpl->autoload();
        $output = $tpl->fetch( $file );
        $this->assertStringContainsString( '<p data-x="1">T</p><script>var a = "other";</script>', $output );
        $this->assertStringNotContainsString( 'unlisted_attributes.tpl', $output );
        $this->assertMatchesRegularExpression( '#</script>\s*<!-- START[^>]*parts/other\.tpl#', $output );
    }

    /**
     * A captured value (a name, a URL, nothing) loses its comments, so tests and uses of it work; captured HTML keeps
     * them. Unlisted parts only: no list is needed for set-block.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('kindAndModeProvider')]
    public function testACapturedValueHasNoCommentsAndCapturedHTMLKeepsThem( $kind, $compiled )
    {
        $parts = self::$dir . '/design/test/templates/parts';
        file_put_contents( $parts . '/value_name.tpl', 'ezcontentnavigationpart' );
        file_put_contents( $parts . '/value_empty.tpl', "{* nothing *}\n" );
        file_put_contents( $parts . '/value_html.tpl', '<li>item</li>' );
        $this->setUpRender( $kind, $compiled );
        $file = self::$dir . '/captured-' . $kind . '-' . ( $compiled ? 'compiled' : 'interpreted' ) . '.tpl';
        file_put_contents( $file, '{set-block variable=name}{include uri="file:' . $parts . '/value_name.tpl"}{/set-block}'
                                . '{set-block variable=empty}{include uri="file:' . $parts . '/value_empty.tpl"}{/set-block}'
                                . '{set-block variable=html}{include uri="file:' . $parts . '/value_html.tpl"}{/set-block}'
                                . '[{$name}][{if $empty|trim|eq( \'\' )}empty{else}not empty{/if}]<a href="/{$name|wash}">x</a>'
                                . '<ul>{$html}</ul>' );
        $tpl = new eZTemplate();
        $tpl->autoload();
        $output = $tpl->fetch( $file );
        $this->assertSame( array(), $tpl->errorLog() );
        $this->assertStringContainsString( '[ezcontentnavigationpart][empty]<a href="/ezcontentnavigationpart">x</a>', $output );
        $this->assertMatchesRegularExpression( '#<ul>\s*<!-- START[^>]*value_html\.tpl.*<li>item</li>#s', $output );
    }

    public function testCapturedTextWithoutCommentsIsUnchanged()
    {
        $this->assertSame( ' lang="he" ', eZTemplate::capturedText( ' lang="he" ' ) );
        $this->assertSame( ' lang="he" ', eZTemplate::capturedText( self::comment( 'START', 'a.tpl' ) . ' lang="he" ' . self::comment( 'STOP', 'a.tpl' ) ) );
        $this->assertSame( '', eZTemplate::capturedText( self::comment( 'START', 'a.tpl' ) . self::comment( 'STOP', 'a.tpl' ) ) );
    }

    public function testDebugCompilesIntoFilesOfItsOwn()
    {
        $GLOBALS['eZTemplateDebugInternalsEnabled'] = false;
        unset( $GLOBALS['eZTemplateDebugCommentsCompilationKey'] );
        $this->assertSame( '', eZTemplate::debugCommentsCompilationKey() );
        $GLOBALS['eZTemplateDebugInternalsEnabled'] = true;
        $GLOBALS['eZTemplateDebugXHTMLCodeEnabled'] = false;
        $key = eZTemplate::debugCommentsCompilationKey();
        $this->assertMatchesRegularExpression( '#^-debug-[0-9a-f]{8}$#', $key );
        unset( $GLOBALS['eZTemplateDebugCommentsCompilationKey'] );
        eZINI::instance()->setVariable( 'TemplateSettings', 'TemplatesWithoutDebugComments', array( 'parts/other.tpl' ) );
        $this->assertNotSame( $key, eZTemplate::debugCommentsCompilationKey() );
        unset( $GLOBALS['eZTemplateDebugCommentsCompilationKey'] );
    }

    private function setUpRender( $kind, $compiled )
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
        $GLOBALS['eZTemplateDebugInternalsEnabled'] = ( $kind === 'debug' );
        $GLOBALS['eZTemplateDebugXHTMLCodeEnabled'] = false;
        $GLOBALS['eZTemplateShowTemplatePathComments'] = ( $kind === 'path-comments' );
        unset( $GLOBALS['eZTemplateDebugCommentsCompilationKey'] );
    }
}

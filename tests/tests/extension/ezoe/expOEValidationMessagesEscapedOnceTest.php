<?php
/**
 * The messages of the validation log are plain text, and every design prints them escaped exactly once, without the
 * database: the parsers' messages, then the real edit_validation.tpl of each design rendered by the template engine.
 *
 *  VM-01 - The online editor's link messages carry the values as typed: no entity, nothing escaped
 *  VM-02 - The kernel parser's messages whose translations spell markup as entities (&lt;%1&gt;) are stored as
 *          plain text, once (a typed "&amp;" stays "&amp;" only when it came as such)
 *  VM-03 - Rendered through design/admin, design/admin4 and design/standard (and exp_adminui and the media theme when
 *          they are installed): a target name with markup, &, quotes and umlauts is text, escaped once, in the log, in
 *          an attribute's message, in a location message and in a custom rule's message; no element is created
 *  VM-04 - No template of a kernel design turns &lt; and &gt; back before washing, and every validation message is
 *          printed with |wash
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expOEValidationMessagesEscapedOnceTest extends PHPUnit\Framework\TestCase
{
    /** A trashed target's name as an editor may have typed it */
    const NAME = '<b>x</b> & Müller "Q" \'q\' <img src=x onerror=alert(1)>';

    /** The same, escaped exactly once (wash: htmlspecialchars, ENT_QUOTES, HTML 4.01) */
    const NAME_ONCE = '&lt;b&gt;x&lt;/b&gt; &amp; Müller &quot;Q&quot; &#039;q&#039; &lt;img src=x onerror=alert(1)&gt;';

    /** @var string private directory for the templates and their compiled copies */
    private static $dir;

    private $savedGlobals = array();

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        self::$dir = 'var/tmp/phpunit-ezoe-validation-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( self::$dir . '/compiled', 0777, true );
    }

    public static function tearDownAfterClass(): void
    {
        self::removeTree( self::$dir );
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        if ( !class_exists( 'eZOEInputParser' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
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

    /**
     * @return string the message for a link whose target is in the trash, named self::NAME
     */
    private static function deadLinkMessage()
    {
        return eZOEInputParser::deadLinkMessage( 'Report <i>2026</i>', 'eznode://42', array(
            'name' => self::NAME, 'class' => 'Article & Co', 'trashed' => 0, 'path' => '/news/<s>' ), true );
    }

    public function testTheLinkMessagesArePlainText()
    {
        $message = self::deadLinkMessage();
        $this->assertStringContainsString( self::NAME, $message, 'the name as typed' );
        $this->assertStringContainsString( '"Report <i>2026</i>"', $message );
        $this->assertStringContainsString( 'Article & Co', $message );
        $this->assertStringContainsString( '/news/<s>', $message );
        $this->assertDoesNotMatchRegularExpression( '/&(lt|gt|amp|quot|#0?39);/', $message, 'no entity in a plain message' );

        $parser = new eZOEInputParser();
        $parser->process( '<p><a href="javascript:x(&lt;b&gt;)">s</a></p>' );
        $this->assertStringContainsString( "Using scripts in links is not allowed, 'javascript:x(<b>)' has been removed",
                                           implode( "\n", $parser->getMessages() ) );
        // a translatable message now, with its German translation
        $ts = new DOMDocument();
        $ts->load( 'extension/ezoe/translations/ger-DE/translation.ts' );
        $german = ( new DOMXPath( $ts ) )->query( "//context[name='design/standard/ezoe/handler']/message[source=\"Using scripts in links is not allowed, '%1' has been removed\"]/translation" );
        $this->assertSame( 1, $german->length );
        $this->assertStringContainsString( '%1', $german->item( 0 )->textContent );
    }

    public function testTheKernelParsersMessagesArePlainText()
    {
        $this->assertSame( 'Unknown tag: <blink>.', eZXMLInputParser::plainText( 'Unknown tag: &lt;blink&gt;.' ) );
        $this->assertSame( 'Class \'a & "b"\' is not allowed for element <p> (check content.ini).',
                           eZXMLInputParser::plainText( 'Class &#039;a &amp; &quot;b&quot;&#039; is not allowed for element &lt;p&gt; (check content.ini).' ) );
        // once: an escaped entity becomes the entity's text, not the character
        $this->assertSame( '&lt;', eZXMLInputParser::plainText( '&amp;lt;' ) );
        $this->assertSame( 'Müller', eZXMLInputParser::plainText( 'Müller' ) );

        // what handleError() stores: a parser of the kernel's own input with every error reported
        $parser = new eZXMLInputParser( false, eZXMLInputParser::ERROR_ALL );
        $parser->handleError( eZXMLInputParser::ERROR_SYNTAX, ezpI18n::tr( 'kernel/classes/datatypes/ezxmltext', 'Unknown tag: &lt;%1&gt;.', false, array( 'blink' ) ) );
        $this->assertSame( array( 'Unknown tag: <blink>.' ), $parser->getMessages() );
    }

    /**
     * @return array design => path of its edit_validation.tpl (the extension designs only when installed)
     */
    public static function designs()
    {
        $designs = array();
        foreach ( array( 'admin' => 'design/admin/templates/content/edit_validation.tpl',
                         'admin4' => 'design/admin4/templates/content/edit_validation.tpl',
                         'standard' => 'design/standard/templates/content/edit_validation.tpl',
                         'exp_adminui' => 'extension/exp_adminui/design/adminui/templates/content/edit_validation.tpl',
                         'media' => 'extension/sevenx_themes_media/design/media/templates/content/edit_validation.tpl' ) as $design => $path )
        {
            $designs[$design] = array( $path );
        }
        return $designs;
    }

    /**
     * Renders the template file $path with $variables through the template engine, compiled or interpreted
     */
    private function render( $path, array $variables, $compiled )
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
        // a private copy: the compiler keys its output on the file
        $file = self::$dir . '/' . md5( $path . ( $compiled ? 'c' : 'i' ) ) . '.tpl';
        copy( $path, $file );
        $tpl = new eZTemplate();
        $tpl->autoload();
        foreach ( $variables as $name => $value )
            $tpl->setVariable( $name, $value );
        $output = $tpl->fetch( $file );
        $this->assertSame( array(), $tpl->errorLog(), $path );
        return (string)$output;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('designs')]
    public function testEveryDesignPrintsTheMessagesEscapedOnce( $path )
    {
        if ( !file_exists( $path ) )
            $this->markTestSkipped( "$path is not installed" );

        $message = self::deadLinkMessage();
        $kernel = eZXMLInputParser::plainText( ezpI18n::tr( 'kernel/classes/datatypes/ezxmltext', 'Unknown tag: &lt;%1&gt;.', false, array( 'blink' ) ) );
        $place = 'You are not allowed to place this object under: /' . self::NAME;
        $cases = array(
            // the draft was stored, with messages in the log
            'log' => array( 'validation' => array( 'processed' => true, 'attributes' => array(), 'placement' => array(), 'custom_rules' => array() ),
                            'validation_log' => array( array( 'name' => 'Body', 'description' => array( $message, $kernel ) ) ) ),
            // the draft was not stored: an attribute, a location and a custom rule
            'errors' => array( 'validation' => array( 'processed' => true,
                                                      'attributes' => array( array( 'id' => 7, 'identifier' => 'body', 'name' => 'Body', 'description' => $message . ' ' . $kernel ) ),
                                                      'placement' => array( array( 'text' => $place ) ),
                                                      'custom_rules' => array( array( 'text' => self::NAME ) ) ),
                               'validation_log' => array() ),
        );
        foreach ( $cases as $case => $variables )
        {
            foreach ( array( false, true ) as $compiled )
            {
                $label = $path . ' ' . $case . ( $compiled ? ' compiled' : ' interpreted' );
                $html = $this->render( $path, $variables, $compiled );
                $this->assertStringContainsString( self::NAME_ONCE, $html, "$label: the name escaped once" );
                $this->assertStringContainsString( 'Unknown tag: &lt;blink&gt;.', $html, "$label: the kernel message escaped once" );
                $this->assertStringContainsString( 'Article &amp; Co', $html, $label );
                $this->assertStringNotContainsString( '&amp;lt;', $html, "$label: escaped twice" );
                $this->assertStringNotContainsString( '&amp;amp;', $html, "$label: escaped twice" );
                $this->assertStringNotContainsString( '&amp;quot;', $html, "$label: escaped twice" );
                // nothing from the message became markup
                $doc = new DOMDocument();
                libxml_use_internal_errors( true );
                $doc->loadHTML( '<?xml encoding="utf-8"?><div>' . $html . '</div>' );
                libxml_clear_errors();
                foreach ( array( 's', 'img', 'blink' ) as $tag )
                {
                    $this->assertSame( 0, $doc->getElementsByTagName( $tag )->length, "$label: no <$tag> element" );
                }
                // a design may use <b> and <i> itself (the media theme's log heading): none of them holds the message's
                foreach ( array( 'b' => 'x', 'i' => '2026' ) as $tag => $text )
                {
                    foreach ( $doc->getElementsByTagName( $tag ) as $element )
                    {
                        $this->assertNotSame( $text, $element->textContent, "$label: no <$tag>$text</$tag> element" );
                    }
                }
                if ( $case === 'errors' )
                {
                    $this->assertStringContainsString( 'You are not allowed to place this object under: /' . self::NAME_ONCE, $html, "$label: the location message" );
                }
            }
        }
    }

    public function testNoKernelDesignUnescapesAMessageBeforeWashingIt()
    {
        $files = array();
        foreach ( array( 'design', 'extension/ezoe/design' ) as $dir )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                if ( substr( $file->getFilename(), -4 ) === '.tpl'
                     && strpos( (string)file_get_contents( $file->getPathname() ), "explode( '&lt;' )" ) !== false )
                {
                    $files[] = $file->getPathname();
                }
            }
        }
        $this->assertSame( array(), $files, "explode( '&lt;' ) turns an escaped message back into markup" );

        // every validation message of the kernel designs is printed with |wash
        foreach ( array( 'design/admin/templates/content/edit_validation.tpl', 'design/admin4/templates/content/edit_validation.tpl',
                         'design/standard/templates/content/edit_validation.tpl', 'design/admin/templates/pdf/edit_validation.tpl',
                         'design/admin/templates/infocollection_validation.tpl', 'design/admin4/templates/infocollection_validation.tpl',
                         'design/standard/templates/content/collectedinfo_validation.tpl',
                         'design/admin/templates/user/register.tpl', 'design/admin4/templates/user/register.tpl',
                         'design/standard/templates/user/register.tpl' ) as $path )
        {
            $source = (string)file_get_contents( $path );
            preg_match_all( '/\{\$[^{}]*(?:\.description|\.text|LogMessages\.item|\$:item)[^{}]*\}/', $source, $prints );
            $this->assertNotEmpty( $prints[0], $path );
            foreach ( $prints[0] as $print )
            {
                if ( strpos( $print, 'loop=' ) !== false || strpos( $print, '|wash' ) !== false )
                    continue;
                // {$x.name|wash}: {$x.description} and the like
                $this->fail( "$path prints $print without |wash" );
            }
        }
        $attribute = (string)file_get_contents( 'design/admin4/templates/content/edit_attribute.tpl' );
        $this->assertSame( 3, substr_count( $attribute, '{$attribute.validation_error|wash}' ), 'the field messages of admin4' );
    }
}

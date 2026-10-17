<?php
/**
 * The ezurl and ezroot operators with an absolute link (a scheme such as https:, mailto:, javascript: or data:,
 * or a protocol-relative //host one). eZURI::transformURI() gives such a link back untouched and unescaped;
 * when the operator puts it in quotes (no quote parameter, 'double' or 'single'), it is now escaped for the
 * HTML attribute as transformURI() escapes the links it builds. Quote 'no' gives it back as before.
 *
 * Every case is rendered with the real template engine, interpreted (modify()) and compiled
 * (urlTransformation(): a constant link worked out at compile time, a constant link with
 * ShareCompiledTemplates, a link in a variable, a quote parameter in a variable). Relative links must come
 * out exactly as before: the expected value for them is built with transformURI() itself.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group templateoperators
 */

class eZURLOperatorAbsoluteEscapeTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    private $settings;

    private $basics;

    private $share;

    /** ezroot asks the cluster handler, whose first instance installs eZExecution's exception handler */
    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        eZClusterFileHandler::instance();
    }

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = dirname( __DIR__, 4 ) . '/var/tmp/phpunit-ezurl-escape-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir . '/compiled', 0777, true );
        $this->settings = isset( $GLOBALS['eZTemplateCompilerSettings'] ) ? array( $GLOBALS['eZTemplateCompilerSettings'] ) : null;
        $this->basics = array_key_exists( 'eZSiteBasics', $GLOBALS ) ? array( $GLOBALS['eZSiteBasics'] ) : null;
        $ini = eZINI::instance();
        $this->share = $ini->hasVariable( 'TemplateSettings', 'ShareCompiledTemplates' )
            ? array( $ini->variable( 'TemplateSettings', 'ShareCompiledTemplates' ) ) : null;
    }

    protected function tearDown(): void
    {
        if ( $this->settings === null )
            unset( $GLOBALS['eZTemplateCompilerSettings'] );
        else
            $GLOBALS['eZTemplateCompilerSettings'] = $this->settings[0];
        if ( $this->basics === null )
            unset( $GLOBALS['eZSiteBasics'] );
        else
            $GLOBALS['eZSiteBasics'] = $this->basics[0];
        $ini = eZINI::instance();
        if ( $this->share === null )
            $ini->removeSetting( 'TemplateSettings', 'ShareCompiledTemplates' );
        else
            $ini->setVariable( 'TemplateSettings', 'ShareCompiledTemplates', $this->share[0] );
        eZDir::recursiveDelete( $this->dir );
    }

    /** The absolute links and how they must look between the quotes */
    public static function absoluteLinks()
    {
        return array(
            'https with & " < >' => array( 'https://a.example/p?x=1&y="2"<b>', 'https://a.example/p?x=1&amp;y=&quot;2&quot;&lt;b&gt;' ),
            'attribute break-out' => array( 'https://a.example/" onmouseover="alert(1)', 'https://a.example/&quot; onmouseover=&quot;alert(1)' ),
            'single quote' => array( "https://a.example/it's", 'https://a.example/it&#039;s' ),
            '&amp; already escaped' => array( 'https://a.example/?x=1&amp;y=2', 'https://a.example/?x=1&amp;y=2' ),
            'protocol-relative' => array( '//cdn.example/a?b=1&c=2', '//cdn.example/a?b=1&amp;c=2' ),
            'mailto' => array( 'mailto:a@example.invalid?subject=a&body=b', 'mailto:a@example.invalid?subject=a&amp;body=b' ),
            // Escaped, not removed: ezurl is not a filter for script links
            'javascript' => array( 'javascript:alert("x")', 'javascript:alert(&quot;x&quot;)' ),
            'data' => array( 'data:text/html,<script>alert(1)</script>', 'data:text/html,&lt;script&gt;alert(1)&lt;/script&gt;' ),
        );
    }

    /** The relative links: they keep what transformURI() made of them before */
    public static function relativeLinks()
    {
        return array(
            'path with & " <' => array( '/content/view?x=1&y="2"<b>' ),
            'path without slash' => array( 'content/view/full/2' ),
            'anchor' => array( '#top"x' ),
        );
    }

    /** The quote parameters: as written in the template, and the quote that goes round the link */
    private static function quoteModes()
    {
        return array(
            'none' => array( '', '"' ),
            'empty' => array( '()', '"' ),
            'double' => array( "('double')", '"' ),
            'single' => array( "('single')", "'" ),
            'no' => array( "('no')", '' ),
            'double, full' => array( "('double', 'full')", '"' ),
            'single, full' => array( "('single', 'full')", "'" ),
            'no, full' => array( "('no', 'full')", '' ),
        );
    }

    /**
     * Renders $source in every way the engine can: interpreted, compiled, compiled with shared templates.
     *
     * @return array( mode => output )
     */
    private function render( $source, array $variables )
    {
        static $number = 0;
        $out = array();
        foreach ( array( 'interpreted' => array( false, 'disabled' ), 'compiled' => array( true, 'disabled' ),
                         'compiled, shared' => array( true, 'enabled' ) ) as $mode => $how )
        {
            list( $compiled, $share ) = $how;
            eZINI::instance()->setVariable( 'TemplateSettings', 'ShareCompiledTemplates', $share );
            $GLOBALS['eZTemplateCompilerSettings']['compile'] = $compiled;
            $GLOBALS['eZTemplateCompilerSettings']['compilation-directory'] = $this->dir . '/compiled';
            if ( $compiled )
                unset( $GLOBALS['eZSiteBasics'] );
            else
                $GLOBALS['eZSiteBasics']['no-cache-adviced'] = true;
            // A file of its own for each render, so no compiled file of another mode is used
            $file = $this->dir . '/t' . ++$number . '.tpl';
            file_put_contents( $file, $source );
            $tpl = new eZTemplate();
            $tpl->setAutoloadPathList( array( 'lib/eztemplate/classes/', 'kernel/common/' ) );
            $tpl->autoload();
            foreach ( $variables as $name => $value )
                $tpl->setVariable( $name, $value );
            $out[$mode] = $tpl->fetch( $file );
            $this->assertSame( array(), $tpl->errorLog(), "$mode: $source" );
            if ( $compiled )
                $this->assertNotEmpty( glob( $this->dir . '/compiled/*.php' ), "$mode: the template was compiled" );
        }
        return $out;
    }

    /** The template literal for $value: in single quotes, with \' and \\ escaped */
    private static function literal( $value )
    {
        return "'" . str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), $value ) . "'";
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'absoluteLinks' )]
    public function testAnAbsoluteLinkInQuotesIsEscaped( $url, $escaped )
    {
        foreach ( self::quoteModes() as $name => $mode )
        {
            list( $parameters, $quote ) = $mode;
            $expected = $quote === '' ? $url : $quote . $escaped . $quote;
            $sources = array(
                'variable' => '{$url|ezurl' . $parameters . '}',
                'constant' => '{' . self::literal( $url ) . '|ezurl' . $parameters . '}',
            );
            foreach ( $sources as $kind => $source )
                foreach ( $this->render( $source, array( 'url' => $url ) ) as $how => $html )
                    $this->assertSame( $expected, $html, "$name, $kind, $how" );
        }
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'absoluteLinks' )]
    public function testAQuoteParameterInAVariableIsWorkedOutAtRenderTime( $url, $escaped )
    {
        foreach ( array( 'double' => '"' . $escaped . '"', 'single' => "'" . $escaped . "'", 'no' => $url,
                         'anything else' => '"' . $escaped . '"' ) as $quote => $expected )
        {
            foreach ( array( 'variable' => '{$url|ezurl( $quote )}', 'constant' => '{' . self::literal( $url ) . '|ezurl( $quote )}' ) as $kind => $source )
                foreach ( $this->render( $source, array( 'url' => $url, 'quote' => $quote ) ) as $how => $html )
                {
                    // A constant link is worked out at compile time, and applyQuotes() cannot read a quote
                    // parameter in a variable then: it always puts double quotes (as before, also for relative
                    // links). The link is escaped for them.
                    $this->assertSame( $kind === 'constant' && $how === 'compiled' ? '"' . $escaped . '"' : $expected,
                                       $html, "$quote, $kind, $how" );
                }
        }
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'relativeLinks' )]
    public function testARelativeLinkIsUnchanged( $url )
    {
        foreach ( self::quoteModes() as $name => $mode )
        {
            list( $parameters, $quote ) = $mode;
            $built = $url;
            eZURI::transformURI( $built, false, strpos( $parameters, 'full' ) !== false ? 'full' : 'relative' );
            $expected = $quote . $built . $quote;
            foreach ( array( 'variable' => '{$url|ezurl' . $parameters . '}', 'constant' => '{' . self::literal( $url ) . '|ezurl' . $parameters . '}' ) as $kind => $source )
                foreach ( $this->render( $source, array( 'url' => $url ) ) as $how => $html )
                    $this->assertSame( $expected, $html, "$name, $kind, $how" );
        }
    }

    /**
     * ezroot: interpreted, an absolute link is given back as it is (escaped in quotes); compiled, it has always
     * become '/'. Both ways nothing unescaped reaches the attribute.
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'absoluteLinks' )]
    public function testEzrootEscapesAnAbsoluteLinkInQuotes( $url, $escaped )
    {
        $root = '/';
        eZURI::transformURI( $root, true, 'relative' );
        foreach ( array( '' => '"', "('single')" => "'" ) as $parameters => $quote )
        {
            $out = $this->render( '{$url|ezroot' . $parameters . '}', array( 'url' => $url ) );
            $this->assertSame( $quote . $escaped . $quote, $out['interpreted'] );
            $this->assertSame( $quote . $root . $quote, $out['compiled'] );
            $this->assertSame( $quote . $root . $quote, $out['compiled, shared'] );
        }
        $out = $this->render( "{\$url|ezroot('no')}", array( 'url' => $url ) );
        $this->assertSame( $url, $out['interpreted'] );
    }

    public function testEscapeAbsoluteURLMatchesTheEscapingOfBuiltLinks()
    {
        $value = '/a?x=1&amp;y="2"&z=<3>\'';
        $built = $value;
        eZURI::transformURI( $built, false, 'relative' );
        $this->assertStringEndsWith( eZURLOperator::escapeAbsoluteURL( $value ), $built );
    }
}

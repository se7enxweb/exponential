<?php
/**
 * Documentation links of cronjob scripts (expCronjobRunner::scriptDocumentation()): the installation's
 * cronjob.ini [ScriptDocumentation] Link[] comes first, then the @link tag of the script's header; a path is put after
 * DocumentationBase; only http and https come out.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

class expCronjobScriptDocumentationTest extends PHPUnit\Framework\TestCase
{
    private $file;
    private $saved = array();

    protected function setUp(): void
    {
        if ( !class_exists( 'expCronjobRunner' ) || !class_exists( 'eZINI' ) )
            $this->markTestSkipped( 'expCronjobRunner and eZINI are not autoloadable here' );
        $this->file = sys_get_temp_dir() . '/exp_cronjob_doc_' . bin2hex( random_bytes( 6 ) ) . '.php';
        $ini = eZINI::instance( 'cronjob.ini' );
        foreach ( array( 'Link', 'DocumentationBase' ) as $name )
            $this->saved[$name] = $ini->hasVariable( 'ScriptDocumentation', $name ) ? $ini->variable( 'ScriptDocumentation', $name ) : null;
    }

    protected function tearDown(): void
    {
        if ( $this->file && is_file( $this->file ) )
            unlink( $this->file );
        $ini = eZINI::instance( 'cronjob.ini' );
        foreach ( $this->saved as $name => $value )
        {
            if ( $value === null )
                $ini->removeSetting( 'ScriptDocumentation', $name );
            else
                $ini->setVariable( 'ScriptDocumentation', $name, $value );
        }
    }

    private function script( $header )
    {
        file_put_contents( $this->file, "<?php\n/**\n" . $header . " */\n" );
        return $this->file;
    }

    private function links( array $links, $base = '' )
    {
        $ini = eZINI::instance( 'cronjob.ini' );
        $ini->setVariable( 'ScriptDocumentation', 'Link', $links );
        $ini->setVariable( 'ScriptDocumentation', 'DocumentationBase', $base );
    }

    public function testAUrlStaysAndAPathGoesAfterTheBase()
    {
        $base = expCronjobRunner::DOCUMENTATION_BASE;
        $this->assertSame( 'https://example.com/a?b=1#c', expCronjobRunner::documentationUrl( 'https://example.com/a?b=1#c' ) );
        $this->assertSame( $base . 'doc/guides/sessions.md', expCronjobRunner::documentationUrl( 'doc/guides/sessions.md' ) );
        $this->assertSame( $base . 'doc/guides/sessions.md#cleanup', expCronjobRunner::documentationUrl( ' doc/guides/sessions.md#cleanup ' ) );
        $this->assertSame( 'https://intranet.example.com/ops/doc/x.md',
                           expCronjobRunner::documentationUrl( 'doc/x.md', 'https://intranet.example.com/ops/' ) );
    }

    public function testOnlyHttpLinksComeOut()
    {
        foreach ( array( 'javascript:alert(1)', 'data:text/html,x', 'ftp://example.com/x', '//example.com/x', '/etc/passwd',
                         '../secret.md', 'doc/../../x.md', 'doc/a b.md', 'https://example.com/"onmouseover=x', '' ) as $value )
            $this->assertSame( '', expCronjobRunner::documentationUrl( $value ), $value );
        // A path needs a base that is a link itself.
        $this->assertSame( '', expCronjobRunner::documentationUrl( 'doc/x.md', 'javascript:' ) );
    }

    public function testTheHeaderNamesTheDocumentation()
    {
        $this->links( array() );
        $path = $this->script( " * @description Does a thing\n * @link doc/guides/sessions.md\n" );
        $this->assertSame( expCronjobRunner::DOCUMENTATION_BASE . 'doc/guides/sessions.md',
                           expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
        $this->assertSame( 'Does a thing', expCronjobRunner::scriptDescription( $path ) );
        // Without a tag, or without a file, there is no link.
        $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'thing.php', $this->script( " * @description Does a thing\n" ) ) );
        $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'gone.php', false ) );
    }

    public function testTheInstallationComesFirst()
    {
        $path = $this->script( " * @link doc/guides/sessions.md\n" );
        $this->links( array( 'thing.php' => 'https://intranet.example.com/ops/thing' ) );
        $this->assertSame( 'https://intranet.example.com/ops/thing', expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
        // By the file name, also when the part names the script with a directory.
        $this->assertSame( 'https://intranet.example.com/ops/thing', expCronjobRunner::scriptDocumentation( 'cronjobs/thing.php', $path ) );
        // none removes the header's link; an empty entry leaves it.
        $this->links( array( 'thing.php' => 'none' ) );
        $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
        $this->links( array( 'thing.php' => 'None' ) );
        $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
        // A value that is no link gives none either, rather than the header's.
        $this->links( array( 'thing.php' => 'javascript:alert(1)' ) );
        $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
        $this->links( array( 'thing.php' => array( 'x' ) ) );
        $this->assertSame( expCronjobRunner::DOCUMENTATION_BASE . 'doc/guides/sessions.md', expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
        $this->links( array( 'thing.php' => '' ) );
        $this->assertSame( expCronjobRunner::DOCUMENTATION_BASE . 'doc/guides/sessions.md', expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
        // The installation's base for paths.
        $this->links( array( 'thing.php' => 'betrieb/thing.md' ), 'https://intranet.example.com/doc/' );
        $this->assertSame( 'https://intranet.example.com/doc/betrieb/thing.md', expCronjobRunner::scriptDocumentation( 'thing.php', $path ) );
    }

    public function testTheKernelScriptsLinkToDocumentationThatExists()
    {
        $root = dirname( __DIR__, 4 );
        $found = 0;
        foreach ( glob( $root . '/cronjobs/*.php' ) as $file )
        {
            if ( !preg_match( '/^\s*\*\s*@link\s+(\S+)/m', (string)file_get_contents( $file, false, null, 0, 8192 ), $match ) )
                continue;
            $found++;
            $this->assertFileExists( $root . '/' . preg_replace( '/#.*$/', '', $match[1] ), basename( $file ) );
        }
        $this->assertGreaterThan( 0, $found );
    }

    /**
     * With Exponential's documentation as the base, a path is linked only when the installation ships that file: a
     * typo or an unpublished page never becomes a dead link.
     */
    public function testAPathThatIsNotShippedGivesNoLink()
    {
        $this->links( array() );
        $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'thing.php', $this->script( " * @link doc/guides/t471-no-such-page.md\n" ) ) );
        $this->links( array( 'thing.php' => 'doc/guides/t471-no-such-page.md' ) );
        $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'thing.php', $this->script( " * @description Does a thing\n" ) ) );
        // An installation's own base is its own to keep alive.
        $this->links( array( 'thing.php' => 'doc/t471-intranet.md' ), 'https://intranet.example.com/' );
        $this->assertSame( 'https://intranet.example.com/doc/t471-intranet.md', expCronjobRunner::scriptDocumentation( 'thing.php', $this->script( '' ) ) );
    }

    /**
     * The path in an extension's script names a file of that extension, never one of Exponential's documentation; its
     * URL is kept.
     */
    public function testAnExtensionScriptsPathIsNotLinkedToTheKernelDocumentation()
    {
        $this->links( array() );
        $dir = dirname( $this->file ) . '/t471-' . bin2hex( random_bytes( 4 ) ) . '/extension/demo/cronjobs';
        mkdir( $dir, 0700, true );
        $file = $dir . '/thing.php';
        try
        {
            file_put_contents( $file, "<?php\n/**\n * @link doc/guides/sessions.md\n */\n" );
            $this->assertSame( '', expCronjobRunner::scriptDocumentation( 'thing.php', $file ) );
            file_put_contents( $file, "<?php\n/**\n * @link https://example.com/demo/cronjobs\n */\n" );
            $this->assertSame( 'https://example.com/demo/cronjobs', expCronjobRunner::scriptDocumentation( 'thing.php', $file ) );
        }
        finally
        {
            @unlink( $file );
            for ( $d = $dir, $i = 0; $i < 4; $i++, $d = dirname( $d ) )
                @rmdir( $d );
        }
    }
}

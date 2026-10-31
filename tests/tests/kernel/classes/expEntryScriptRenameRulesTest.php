<?php
/**
 * The SOAP and WebDAV servers are index_soap.php and index_webdav.php since Exponential 6.0.15; soap.php and
 * webdav.php are shims. expSoapEntryScriptTest and expWebDAVEntryScriptTest test the scripts and the shims. This
 * test keeps what surrounds them in step: the shipped rewrite rules and example configurations name the new
 * scripts and no active rule names an old one, Velocity runs neither server, and the migration pages exist in
 * English and German.
 *
 * No database: it reads files and the constants of expVelocity.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expEntryScriptRenameRulesTest extends PHPUnit\Framework\TestCase
{
    /** The example configurations that carry the rules of .htaccess_root for a server. */
    const EXAMPLES = array(
        'doc/eng-us/examples/htaccess',
        'doc/eng-us/examples/ezpublish.conf',
        'doc/eng-us/examples/web-servers/nginx.conf',
        'doc/eng-us/examples/web-servers/openlitespeed-vhost.conf',
    );

    /** An old name, not part of index_soap.php or index_webdav.php. */
    const OLD_NAME = '/(?<![A-Za-z0-9_])(soap|webdav)\\\\?\.php/';

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    private static function read( $file )
    {
        $text = file_get_contents( self::root() . '/' . $file );
        if ( $text === false )
            throw new RuntimeException( "Cannot read $file" );
        return $text;
    }

    /**
     * The lines of a configuration file that are not comments.
     */
    private static function activeLines( $text )
    {
        $lines = array();
        foreach ( preg_split( '/\R/', $text ) as $number => $line )
        {
            if ( preg_match( '/^\s*(#|$)/', $line ) )
                continue;
            $lines[$number + 1] = $line;
        }
        return $lines;
    }

    public function testBothServersAndBothShimsAreShipped()
    {
        $manifest = self::read( 'share/filelist.md5' );
        foreach ( array( 'index_soap.php', 'soap.php', 'index_webdav.php', 'webdav.php' ) as $script )
        {
            $this->assertFileExists( self::root() . '/' . $script );
            $this->assertMatchesRegularExpression( '/^[0-9a-f]{32}  ' . preg_quote( $script, '/' ) . '$/m', $manifest, $script );
        }
    }

    public function testTheShippedRewriteRulesNameIndexSoap()
    {
        foreach ( array( '.htaccess_root', '.htaccess_root_static' ) as $file )
        {
            $rules = self::read( $file );
            $this->assertStringContainsString( '#RewriteRule ^index_soap\.php - [L]', $rules, $file );
            foreach ( self::activeLines( $rules ) as $number => $line )
                $this->assertDoesNotMatchRegularExpression( self::OLD_NAME, $line, "$file:$number names an old entry script" );
        }
    }

    public function testTheExampleConfigurationsNameBothNewScripts()
    {
        foreach ( self::EXAMPLES as $file )
        {
            $text = self::read( $file );
            $this->assertStringContainsString( 'index_soap', $text, $file );
            $this->assertStringContainsString( 'index_webdav', $text, $file );
            foreach ( self::activeLines( $text ) as $number => $line )
                $this->assertDoesNotMatchRegularExpression( self::OLD_NAME, $line, "$file:$number names an old entry script" );
        }
    }

    /**
     * Velocity's workers stay up between requests, and both servers declare global functions
     * (eZUpdateDebugSettings() and others): a second request through the same worker would end in
     * "Cannot redeclare". So neither runs under Velocity, under either name; they are served by Apache
     * or nginx with PHP-FPM, as doc/eng-us/examples/web-servers/velocity.md says.
     */
    public function testVelocityRunsNeitherServer()
    {
        foreach ( array( 'index_soap.php', 'index_webdav.php' ) as $script )
            $this->assertMatchesRegularExpression( '/^function eZUpdateDebugSettings\(/m', self::read( $script ), $script );

        $routed = implode( ' ', array_merge( expVelocity::ENTRY_SCRIPTS, array_values( expVelocity::FRONT_CONTROLLERS ) ) );
        $this->assertDoesNotMatchRegularExpression( '/(soap|webdav)\.php/', $routed );

        foreach ( array( 'kernel/private/classes/commands/velocity-router.php', 'kernel/classes/expvelocityfrankenphp.php' ) as $file )
            $this->assertDoesNotMatchRegularExpression( '/(soap|webdav)\\\\?\.php/', self::read( $file ), $file );
    }

    public function testTheMigrationPagesExistInBothLanguages()
    {
        $pages = array(
            'doc/eng-us/bc/6.0/soap-entry-script-index-soap.md',
            'doc/eng-us/bc/6.0/webdav-entry-script-index-webdav.md',
            'doc/ger-de/bc/6.0/soap-einstiegsskript-index-soap.md',
            'doc/ger-de/bc/6.0/webdav-einstiegsskript-index-webdav.md',
        );
        foreach ( $pages as $page )
        {
            $text = self::read( $page );
            foreach ( array( 'Apache', 'Plesk', 'nginx', 'Velocity', 'curl ' ) as $part )
                $this->assertStringContainsString( $part, $text, "$page: $part" );
        }

        // the pages the shims and their notice send an administrator to
        $this->assertStringContainsString( 'doc/eng-us/bc/6.0/soap-entry-script-index-soap.md', self::read( 'soap.php' ) );
        $this->assertStringContainsString( 'doc/eng-us/bc/6.0/webdav-entry-script-index-webdav.md', self::read( 'webdav.php' ) );

        $changelog = self::read( 'doc/changelogs/6.0/6.0.15.md' );
        $this->assertStringContainsString( '## 2026-10-29: the SOAP entry script is index_soap.php', $changelog );
        $this->assertStringContainsString( '## 2026-10-29: the WebDAV entry script is index_webdav.php', $changelog );
    }
}

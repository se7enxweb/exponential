<?php
/**
 * bin/php/console resolves the @alias tags of extension commands (extension/<ext>/bin/php/<name>.php) as it does
 * for the kernel's: "@alias exp:layouts" in explayouts_ui's layouts.php makes ./console exp:layouts run
 * ext:explayouts_ui:layouts. Before, the tags of extension scripts were declared but never read.
 *
 * Runs the console against a throwaway installation root holding only the console and one extension script.
 *
 * Run: php vendor/bin/phpunit tests/tests/bin/ConsoleExtensionAliasTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ConsoleExtensionAliasTest extends PHPUnit\Framework\TestCase
{
    protected $root;

    protected function setUp(): void
    {
        $base = dirname( __DIR__, 3 );
        $this->root = $base . '/var/tmp/console-alias-test-' . getmypid() . '-' . mt_rand();
        mkdir( $this->root . '/bin/php', 0700, true );
        mkdir( $this->root . '/extension/demoext/bin/php', 0700, true );
        copy( $base . '/bin/php/console', $this->root . '/bin/php/console' );
        // autoload.php is all the console loads before it dispatches; it only needs eZCLI and eZScript to exist
        file_put_contents( $this->root . '/autoload.php', "<?php\n"
            . "class eZCLI { static function instance() { return new self(); } function __call( \$n, \$a ) { if ( \$a ) echo \$a[0], \"\\n\"; } }\n"
            . "class eZScript { static function instance( \$s = array() ) { return new self(); } function __call( \$n, \$a ) {} }\n" );
        file_put_contents( $this->root . '/extension/demoext/bin/php/hello.php',
            "<?php\n/**\n * Says hello\n *\n * @alias exp:demo-hello, hi\n */\necho \"hello from demoext\\n\";\n" );
    }

    protected function tearDown(): void
    {
        foreach ( array( 'extension/demoext/bin/php/hello.php', 'bin/php/console', 'autoload.php' ) as $file )
            @unlink( $this->root . '/' . $file );
        foreach ( array( 'extension/demoext/bin/php', 'extension/demoext/bin', 'extension/demoext', 'extension', 'bin/php', 'bin', '' ) as $dir )
            @rmdir( $this->root . '/' . $dir );
    }

    protected function console( $command )
    {
        $output = array();
        exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $this->root . '/bin/php/console' ) . ' ' . $command . ' 2>&1', $output, $code );
        return array( $code, implode( "\n", $output ) );
    }

    public function testAFullAliasRunsTheExtensionCommand()
    {
        list( $code, $output ) = $this->console( 'exp:demo-hello' );
        $this->assertSame( 0, $code, $output );
        $this->assertStringContainsString( 'hello from demoext', $output );
    }

    public function testABareAliasStaysInTheExtensionsNamespace()
    {
        list( $code, $output ) = $this->console( 'ext:demoext:hi' );
        $this->assertSame( 0, $code, $output );
        $this->assertStringContainsString( 'hello from demoext', $output );
    }
}

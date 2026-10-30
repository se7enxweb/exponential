<?php
/**
 * The SOAP entry script is index_soap.php (Exponential 6.0.15), named like the other front controllers; soap.php
 * stays as a shim that only includes it and writes one deprecation notice a day to var/log/warning.log.
 * See doc/eng-us/bc/6.0/soap-entry-script-index-soap.md.
 *
 * No database. The shim is run in a PHP process of its own, in a copy beside a stand-in index_soap.php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expSoapEntryScriptTest extends PHPUnit\Framework\TestCase
{
    /** @var string a directory of its own under the installation's var/tmp */
    private $dir;

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null && is_dir( $this->dir ) )
            self::removeTree( $this->dir );
        $this->dir = null;
        unset( $_SERVER['SCRIPT_NAME'] );
    }

    private static function removeTree( $dir )
    {
        foreach ( array_diff( scandir( $dir ), array( '.', '..' ) ) as $entry )
        {
            $path = $dir . '/' . $entry;
            is_dir( $path ) ? self::removeTree( $path ) : unlink( $path );
        }
        rmdir( $dir );
    }

    public function testTheSoapServerIsIndexSoapPhp()
    {
        $script = file_get_contents( self::root() . '/index_soap.php' );
        $this->assertNotFalse( $script );
        $this->assertStringContainsString( '$server = new eZSOAPServer();', $script );
        $this->assertStringContainsString( '$server->processRequest();', $script );
        $this->assertStringContainsString( "\$soapINI->variable( 'GeneralSettings', 'EnableSOAP' )", $script );
        // the URIs are read relative to the script the request named, the shim's name included
        $this->assertStringContainsString( 'eZSys::init( $soapEntryScript,', $script );
        $this->assertStringNotContainsString( "eZSys::init( 'soap.php'", $script );
    }

    public function testSoapPhpOnlyForwards()
    {
        $shim = file_get_contents( self::root() . '/soap.php' );
        $this->assertNotFalse( $shim );
        $this->assertSame( "require __DIR__ . '/index_soap.php';", trim( substr( $shim, strrpos( $shim, 'require ' ) ) ),
                           'the shim ends by including index_soap.php' );
        $this->assertStringNotContainsString( 'eZSOAPServer', $shim );

        // nothing declared: a persistent worker may include it more than once
        foreach ( token_get_all( $shim ) as $token )
        {
            if ( is_array( $token ) )
                $this->assertNotContains( $token[0], array( T_FUNCTION, T_CLASS ), 'no declaration in the shim' );
        }
    }

    public function testTheShimRunsIndexSoapPhpAndWarnsOnceADay()
    {
        $this->dir = self::root() . '/var/tmp/expsoapentryscript-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir . '/var/log', 0700, true );
        copy( self::root() . '/soap.php', $this->dir . '/soap.php' );
        file_put_contents( $this->dir . '/index_soap.php', "<?php\necho 'index_soap.php ran';\n" );

        $run = function ()
        {
            $out = array();
            $status = null;
            exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $this->dir . '/soap.php' ) . ' 2>&1', $out, $status );
            $this->assertSame( 0, $status, implode( "\n", $out ) );
            return implode( "\n", $out );
        };

        $this->assertSame( 'index_soap.php ran', $run() );
        $log = $this->dir . '/var/log/warning.log';
        $this->assertFileExists( $log );
        $this->assertSame( 1, substr_count( file_get_contents( $log ), 'index_soap.php (Exponential 6.0.15)' ) );
        $this->assertFileExists( $this->dir . '/var/log/soap-php-deprecated.stamp' );

        // the same day: forwarded again, no second notice
        $this->assertSame( 'index_soap.php ran', $run() );
        $this->assertSame( 1, substr_count( file_get_contents( $log ), 'index_soap.php (Exponential 6.0.15)' ) );

        // a stamp from yesterday: one more notice
        touch( $this->dir . '/var/log/soap-php-deprecated.stamp', time() - 2 * 86400 );
        $this->assertSame( 'index_soap.php ran', $run() );
        $this->assertSame( 2, substr_count( file_get_contents( $log ), 'index_soap.php (Exponential 6.0.15)' ) );
    }

    public function testTheShimStillAnswersWithoutAVarLogDirectory()
    {
        $this->dir = self::root() . '/var/tmp/expsoapentryscript-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0700, true );
        copy( self::root() . '/soap.php', $this->dir . '/soap.php' );
        file_put_contents( $this->dir . '/index_soap.php', "<?php\necho 'index_soap.php ran';\n" );

        $out = array();
        exec( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $this->dir . '/soap.php' ) . ' 2>&1', $out, $status );
        $this->assertSame( 0, $status );
        $this->assertSame( 'index_soap.php ran', implode( "\n", $out ) );
        $this->assertFileDoesNotExist( $this->dir . '/var/log/warning.log' );
    }

    public function testASignInThroughEitherNameIsASoapSignIn()
    {
        foreach ( array( '/index_soap.php', '/soap.php' ) as $script )
        {
            $_SERVER['SCRIPT_NAME'] = $script;
            $this->assertSame( 'soap', expUserSessionTracker::detectMethod(), $script );
        }
    }
}

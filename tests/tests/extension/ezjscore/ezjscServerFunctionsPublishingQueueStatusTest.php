<?php
/**
 * ezpublishingqueue::status answers only a user who can read the object, or the registered user who created the
 * version and waits for it; the address of the published node only a user who can read the object. Anyone else gets
 * the answer of a version that does not exist, where an anonymous caller got the status of any version before
 * (and the node's address of a finished one).
 *
 * The server function runs in a PHP process of its own with stand-ins for the kernel classes it uses
 * (fixtures/publishingqueue_harness.php): object 7 with version 1. No kernel, database or siteaccess.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsPublishingQueueStatusTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezjscServerFunctionsPublishingQueueStatusTest extends PHPUnit\Framework\TestCase
{
    const NOT_FOUND = array( 'error' => 'status is not supported. Reason: Object version not found.' );
    const ANONYMOUS = array( 'registered' => false, 'id' => 10 );
    const CREATOR = array( 'registered' => true, 'id' => 14 );
    const OTHER = array( 'registered' => true, 'id' => 15 );

    /** @return array the JSON object the harness printed */
    private function callStatus( array $case )
    {
        $case += array( 'args' => array( '7', '1' ), 'version' => array( 'creator_id' => 14 ), 'canRead' => false,
                        'user' => self::ANONYMOUS, 'process' => null );
        $line = array( ...expTestBarePhpCommand(), '-d', 'display_errors=stderr', __DIR__ . '/fixtures/publishingqueue_harness.php',
                       json_encode( $case ) );
        $process = proc_open( $line, array( 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
        $this->assertIsResource( $process );
        $out = stream_get_contents( $pipes[1] );
        $err = stream_get_contents( $pipes[2] );
        fclose( $pipes[1] );
        fclose( $pipes[2] );
        $this->assertSame( 0, proc_close( $process ), $out . $err );
        $this->assertSame( '', $err );
        $result = json_decode( $out, true );
        $this->assertIsArray( $result, $out );
        return $result;
    }

    public static function refusedProvider()
    {
        return array(
            'anonymous, queued'               => array( array() ),
            'anonymous, finished'             => array( array( 'process' => 2 ) ),
            'anonymous, deferred'             => array( array( 'process' => 4 ) ),
            'anonymous creator of the version' => array( array( 'version' => array( 'creator_id' => 10 ) ) ),
            'another user, working'           => array( array( 'user' => self::OTHER, 'process' => 1 ) ),
            'another user, finished'          => array( array( 'user' => self::OTHER, 'process' => 2 ) ),
            'no such version'                 => array( array( 'version' => null, 'canRead' => true, 'user' => self::CREATOR ) ),
            'not an ID'                       => array( array( 'args' => array( '7 OR 1=1', '1' ), 'canRead' => true ) ),
            'version zero'                    => array( array( 'args' => array( '7', '0' ), 'canRead' => true ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('refusedProvider')]
    public function testTheStatusIsNotToldToEveryone( array $case )
    {
        $this->assertSame( self::NOT_FOUND, $this->callStatus( $case ) );
    }

    public function testAReaderGetsTheStatusAndTheNodeAddress()
    {
        $this->assertSame( array( 'return' => array( 'status' => 'queued' ) ), $this->callStatus( array( 'canRead' => true ) ) );
        $this->assertSame( array( 'return' => array( 'status' => 'working' ) ), $this->callStatus( array( 'canRead' => true, 'process' => 1 ) ) );
        $this->assertSame( array( 'return' => array( 'node_uri' => '/index.php/secret/draft-title', 'status' => 'finished' ) ),
                           $this->callStatus( array( 'canRead' => true, 'process' => 2 ) ) );
        $this->assertSame( array( 'return' => array( 'status' => 'pending' ) ), $this->callStatus( array( 'canRead' => true, 'process' => 3 ) ) );
    }

    public function testTheCreatorGetsTheStatusButNotTheNodeAddressItCannotRead()
    {
        $this->assertSame( array( 'return' => array( 'status' => 'queued' ) ), $this->callStatus( array( 'user' => self::CREATOR ) ) );
        $this->assertSame( array( 'return' => array( 'status' => 'finished' ) ), $this->callStatus( array( 'user' => self::CREATOR, 'process' => 2 ) ) );
        $this->assertSame( array( 'return' => array( 'versionview_uri' => '/index.php/content/versionview/7/1', 'status' => 'deferred' ) ),
                           $this->callStatus( array( 'user' => self::CREATOR, 'process' => 4 ) ) );
    }

    public function testAMissingArgumentIsRefused()
    {
        $this->assertSame( array( 'error' => 'status is not supported. Reason: Missing argument(s).' ),
                           $this->callStatus( array( 'args' => array( '7' ), 'canRead' => true ) ) );
    }

    public function testTheAutoloadGeneratorFindsNoClassInTheHarness()
    {
        // the stand-ins carry kernel class names; declared as classes, ezpgenerateautoloads.php -s would map them to
        // the harness, a script that runs when it is loaded
        $tokens = token_get_all( (string)file_get_contents( __DIR__ . '/fixtures/publishingqueue_harness.php' ) );
        $this->assertNotEmpty( $tokens );
        foreach ( $tokens as $token )
            $this->assertFalse( is_array( $token ) && in_array( $token[0], array( T_CLASS, T_INTERFACE, T_TRAIT ), true ), 'a class declaration in the harness' );
    }
}

<?php
/**
 * lib/version.php (ExponentialSDK and its former name eZPublishSDK) and lib/phpcompat.php:
 *   - version(): with and without the release, as the alias, with and without the state, built from the constants
 *   - majorVersion/minorVersion/release/state/alias/developmentVersion agree with the class constants
 *   - eZPublishSDK answers exactly as ExponentialSDK
 *   - the version is a plain dotted number and the alias is major.minor
 *   - phpcompat.php: array_is_list() behaves as PHP 8.1's, and the PHP 8.0 fallback body itself is run under another
 *     name on every PHP, so the code that PHP 8.0 executes is tested on the versions CI runs
 *
 * No kernel bootstrap, no database (databaseVersion() needs one and is not called).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 */

class ExponentialSDKAndPhpCompatTest extends PHPUnit\Framework\TestCase
{
    private static $fallback;

    public static function setUpBeforeClass(): void
    {
        $root = dirname( __DIR__, 4 );
        require_once $root . '/lib/version.php';
        require_once $root . '/lib/phpcompat.php';

        // The fallback of phpcompat.php, defined under a name of its own: on PHP 8.1 and later the file defines
        // nothing, since array_is_list() is built in.
        self::$fallback = 'exp_phpcompat_test_array_is_list_fallback';
        if ( !function_exists( self::$fallback ) )
        {
            $source = file_get_contents( $root . '/lib/phpcompat.php' );
            if ( !preg_match( '/function array_is_list\(.*?\n    \}\n/s', $source, $m ) )
                throw new RuntimeException( 'array_is_list() fallback not found in lib/phpcompat.php' );
            eval( str_replace( 'function array_is_list(', 'function ' . self::$fallback . '(', $m[0] ) );
        }
    }

    public function testVersionIsBuiltFromTheConstants()
    {
        $base = ExponentialSDK::VERSION_MAJOR . '.' . ExponentialSDK::VERSION_MINOR;
        $this->assertSame( $base . '.' . ExponentialSDK::VERSION_RELEASE . ExponentialSDK::VERSION_STATE, ExponentialSDK::version() );
        $this->assertSame( $base . '.' . ExponentialSDK::VERSION_RELEASE, ExponentialSDK::version( true, false, false ) );
        $this->assertSame( $base, ExponentialSDK::version( false, false, false ) );
        $this->assertSame( $base . ExponentialSDK::VERSION_STATE, ExponentialSDK::version( false ) );
        $this->assertSame( ExponentialSDK::VERSION_ALIAS . '-' . ExponentialSDK::VERSION_STATE, ExponentialSDK::version( true, true ) );
        $this->assertSame( ExponentialSDK::VERSION_ALIAS, ExponentialSDK::version( true, true, false ) );
    }

    public function testAccessorsReturnTheConstants()
    {
        $this->assertSame( ExponentialSDK::VERSION_MAJOR, ExponentialSDK::majorVersion() );
        $this->assertSame( ExponentialSDK::VERSION_MINOR, ExponentialSDK::minorVersion() );
        $this->assertSame( ExponentialSDK::VERSION_RELEASE, ExponentialSDK::release() );
        $this->assertSame( ExponentialSDK::VERSION_STATE, ExponentialSDK::state() );
        $this->assertSame( ExponentialSDK::VERSION_ALIAS, ExponentialSDK::alias() );
        $this->assertSame( ExponentialSDK::VERSION_DEVELOPMENT, ExponentialSDK::developmentVersion() );
        $this->assertSame( 'Exponential', ExponentialSDK::EDITION );
    }

    public function testVersionShape()
    {
        $this->assertIsInt( ExponentialSDK::VERSION_MAJOR );
        $this->assertIsInt( ExponentialSDK::VERSION_MINOR );
        $this->assertIsInt( ExponentialSDK::VERSION_RELEASE );
        $this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+$/', ExponentialSDK::version( true, false, false ) );
        $this->assertSame( ExponentialSDK::VERSION_MAJOR . '.' . ExponentialSDK::VERSION_MINOR, ExponentialSDK::VERSION_ALIAS );
    }

    public function testFormerNameAnswersTheSame()
    {
        $this->assertTrue( is_subclass_of( 'eZPublishSDK', 'ExponentialSDK' ) );
        $this->assertSame( ExponentialSDK::version(), eZPublishSDK::version() );
        $this->assertSame( ExponentialSDK::version( true, true ), eZPublishSDK::version( true, true ) );
        $this->assertSame( ExponentialSDK::release(), eZPublishSDK::release() );
        $this->assertSame( ExponentialSDK::VERSION_MAJOR, eZPublishSDK::VERSION_MAJOR );
    }

    public static function listProvider()
    {
        return array(
            'empty'                 => array( array(), true ),
            'list'                  => array( array( 'a', 'b', 'c' ), true ),
            'explicit keys 0..2'    => array( array( 0 => 'a', 1 => 'b', 2 => 'c' ), true ),
            'nulls'                 => array( array( null, null ), true ),
            'gap'                   => array( array( 0 => 'a', 2 => 'b' ), false ),
            'starts at 1'           => array( array( 1 => 'a', 2 => 'b' ), false ),
            'out of order'          => array( array( 1 => 'b', 0 => 'a' ), false ),
            'string key'            => array( array( 'x' => 1 ), false ),
            'numeric string key'    => array( array( '0' => 'a', '1' => 'b' ), true ),
            'mixed'                 => array( array( 0 => 'a', 'k' => 'b' ), false ),
            'after unset'           => array( ( function () { $a = array( 1, 2, 3 ); unset( $a[1] ); return $a; } )(), false ),
            'after array_values'    => array( array_values( array( 5 => 'a', 9 => 'b' ) ), true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('listProvider')]
    public function testArrayIsList( $array, $expected )
    {
        $this->assertSame( $expected, array_is_list( $array ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('listProvider')]
    public function testPhp80FallbackAgreesWithTheBuiltIn( $array, $expected )
    {
        $fallback = self::$fallback;
        $this->assertSame( $expected, $fallback( $array ) );
    }

    public function testFallbackIsOnlyDefinedWhenMissing()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/lib/phpcompat.php' );
        $this->assertStringContainsString( "if ( !function_exists( 'array_is_list' ) )", $source );
        $this->assertTrue( function_exists( 'array_is_list' ) );
    }
}

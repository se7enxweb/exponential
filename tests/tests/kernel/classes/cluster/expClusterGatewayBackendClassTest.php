<?php
/**
 * ezpClusterGateway::gatewayClassForBackend(): the class getGateway() falls back to when a persistent worker serves a
 * later request and the backend file's setGatewayClass() call (loaded once, require_once) did not run again. No database.
 *
 *  CG-01 - Every kernel/clustering/dfs*.php backend maps to exactly the class that file registers
 *  CG-02 - A backend the kernel does not ship maps to null
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../../../../../kernel/clustering/gateway.php';

class expClusterGatewayBackendClassTest extends PHPUnit\Framework\TestCase
{
    /** The class a backend file registers with setGatewayClass(), following a require_once of another backend */
    private static function registeredClass( $file )
    {
        $code = file_get_contents( $file );
        if ( preg_match( "/ezpClusterGateway::setGatewayClass\(\s*'([A-Za-z0-9_]+)'\s*\)/", $code, $m ) )
        {
            return $m[1];
        }
        if ( preg_match( "#require_once __DIR__ \. '/(dfs[a-z0-9]+\.php)'#", $code, $m ) )
        {
            return self::registeredClass( dirname( $file ) . '/' . $m[1] );
        }
        return null;
    }

    public function testEveryShippedBackendMapsToTheClassItRegisters()
    {
        $files = glob( __DIR__ . '/../../../../../kernel/clustering/dfs*.php' );
        $this->assertNotEmpty( $files );
        foreach ( $files as $file )
        {
            $backend = basename( $file, '.php' );
            $registered = self::registeredClass( $file );
            $this->assertNotNull( $registered, $backend . ': registers no gateway class' );
            $this->assertSame( $registered, ezpClusterGateway::gatewayClassForBackend( $backend ), $backend );
        }
    }

    public function testAnUnknownBackendMapsToNull()
    {
        $this->assertNull( ezpClusterGateway::gatewayClassForBackend( 'dfsnosuchengine' ) );
        $this->assertNull( ezpClusterGateway::gatewayClassForBackend( '' ) );
    }
}

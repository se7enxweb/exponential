<?php
/**
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expUsersTestCase.php';

class expUsersDeclarationsTest extends expUsersTestCase
{
    public static function classes()
    {
        return array( 'expUserServices', 'expUserGroupServices', 'expRoleServices', 'expPolicyServices', 'expSessionAdminServices',
                      'expPreferencesServices', 'expNotificationServices', 'expCollaborationServices', 'expAccountServices' );
    }

    public function testEveryServiceIsDeclaredAndCallable()
    {
        foreach ( self::classes() as $class )
        {
            $this->assertNotEmpty( $class::$services, $class );
            foreach ( $class::$services as $m => $d )
            {
                $this->assertTrue( is_callable( array( $class, $m ) ), "$class::$m is callable" );
                foreach ( array( 'summary', 'access', 'write', 'args', 'returns' ) as $k )
                    $this->assertArrayHasKey( $k, $d, "$class::$m $k" );
                $this->assertNotSame( '', $d['summary'], "$class::$m summary" );
                $this->assertNotSame( '', $d['returns'], "$class::$m returns" );
                $this->assertIsBool( $d['write'] );
                $this->assertTrue( $d['access'] === 'public' || $d['access'] === 'user' || ( is_array( $d['access'] ) && count( $d['access'] ) === 2 ), "$class::$m access" );
            }
        }
    }

    public function testEveryPublicStaticMethodIsADeclaredService()
    {
        foreach ( self::classes() as $class )
        {
            $r = new ReflectionClass( $class );
            foreach ( $r->getMethods( ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC ) as $m )
            {
                if ( !$m->isPublic() || $m->getDeclaringClass()->getName() !== $class )
                    continue;
                $this->assertArrayHasKey( $m->getName(), $class::$services, "$class::" . $m->getName() . ' is not declared' );
            }
        }
    }

    public function testAtLeastOneHundredFiftyServices()
    {
        $n = 0;
        foreach ( self::classes() as $class )
            $n += count( $class::$services );
        $this->assertGreaterThanOrEqual( 150, $n );
    }

    public function testWritesAreDeclaredAsPostWithAPolicyOrLogin()
    {
        foreach ( self::classes() as $class )
            foreach ( $class::$services as $m => $d )
                if ( $d['write'] )
                    $this->assertNotSame( 'public', $d['access'] === 'public' && !in_array( $m, array( 'register', 'activate', 'forgotRequest', 'reset' ), true ) ? 'public' : 'x', "$class::$m is a public write" );
    }

    public function testEveryWriteRefusesAGetRequest()
    {
        foreach ( self::classes() as $class )
            foreach ( $class::$services as $m => $d )
                if ( $d['write'] )
                {
                    $r = expServiceBase::invoke( $class, $m, array() );
                    $this->assertFalse( $r['ok'], "$class::$m" );
                    $this->assertSame( 403, $r['error']['code'], "$class::$m: " . $r['error']['message'] );
                }
    }

    public function testEveryServiceRefusesAnonymousOrAnswersPublicly()
    {
        $this->loginAnonymous();
        foreach ( self::classes() as $class )
            foreach ( $class::$services as $m => $d )
            {
                if ( $d['write'] )
                    continue;
                $r = expServiceBase::invoke( $class, $m, array() );
                if ( $d['access'] === 'public' )
                    $this->assertNotSame( 401, $r['ok'] ? 0 : $r['error']['code'], "$class::$m" );
                else
                    $this->assertFalse( $r['ok'] && !$r['ok'], "$class::$m" ) ;
                if ( $d['access'] !== 'public' )
                    $this->assertFalse( $r['ok'], "$class::$m must refuse anonymous" );
            }
    }

    public function testCatalogListsTheDomains()
    {
        $r = expServiceBase::invoke( 'expServicesCatalog', 'catalog', array() );
        $this->assertTrue( $r['ok'] );
        $domains = array();
        foreach ( $r['data'] as $row )
            if ( isset( $row['domain'] ) )
                $domains[$row['domain']] = true;
        foreach ( array( 'user', 'usergroup', 'role', 'policy', 'sessionadmin', 'preferences', 'notification', 'collaboration', 'account' ) as $d )
            $this->assertArrayHasKey( $d, $domains, $d );
    }
}

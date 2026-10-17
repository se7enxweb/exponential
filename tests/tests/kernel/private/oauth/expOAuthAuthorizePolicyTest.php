<?php
/**
 * The policy check of oauth/authorize (expOAuthAuthorizePolicy, #548): which results of
 * eZUser::hasAccessTo( 'oauth', 'authorize' ) allow a user to authorize a client in a siteaccess, the switch
 * rest.ini [OAuthSettings] AuthorizeRequiresPolicy, and that the policy function exists and new installations give
 * it to the Editor role. Before 6.0.15 the view checked "!$access['accessWord'] == 'yes'", which never denied.
 *
 * No database: the INI and the user are stand-ins.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/private/oauth/expOAuthAuthorizePolicyTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 * @group oauth
 */

class expOAuthAuthorizePolicyTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function ini( $value )
    {
        return new class( $value ) {
            private $value;
            public function __construct( $value ) { $this->value = $value; }
            public function hasVariable( $block, $name ) { return $this->value !== null; }
            public function variable( $block, $name ) { return $this->value; }
        };
    }

    private static function user( $access )
    {
        return new class( $access ) {
            private $access;
            public $asked = array();
            public function __construct( $access ) { $this->access = $access; }
            public function hasAccessTo( $module, $function ) { $this->asked[] = "$module/$function"; return $this->access; }
        };
    }

    public static function accessProvider()
    {
        $site = (string)crc32( 'site' );
        $siteUnsigned = sprintf( '%u', crc32( 'site' ) );
        $other = (string)crc32( 'other' );
        return array(
            'yes'                               => array( array( 'accessWord' => 'yes' ), true ),
            'no'                                => array( array( 'accessWord' => 'no' ), false ),
            'no access word'                    => array( array(), false ),
            'not an array'                      => array( false, false ),
            'unknown access word'               => array( array( 'accessWord' => 'maybe' ), false ),
            // the old check's mistake: the access word is a string, never compared loosely
            'access word true'                  => array( array( 'accessWord' => true ), false ),
            'limited without policies'          => array( array( 'accessWord' => 'limited' ), false ),
            'limited, other limitation only'    => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'Section' => array( '1' ) ) ) ), true ),
            'limited to this siteaccess'        => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'SiteAccess' => array( $site ) ) ) ), true ),
            'limited to it, unsigned checksum'  => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'SiteAccess' => array( $siteUnsigned ) ) ) ), true ),
            'limited to it, integer checksum'   => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'SiteAccess' => array( crc32( 'site' ) ) ) ) ), true ),
            'limited to another siteaccess'     => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'SiteAccess' => array( $other ) ) ) ), false ),
            'second policy has it'              => array( array( 'accessWord' => 'limited', 'policies' => array( array( 'SiteAccess' => array( $other ) ), array( 'SiteAccess' => array( $other, $site ) ) ) ), true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('accessProvider')]
    public function testAccessResultAllows( $access, $expected )
    {
        $this->assertSame( $expected, expOAuthAuthorizePolicy::accessResultAllows( $access, 'site' ) );
    }

    public function testTheOldCheckNeverDenied()
    {
        // what 6.0.14 did, kept here so the mistake stays visible: denied only when this is true
        foreach ( array( 'yes', 'no', 'limited' ) as $word )
        {
            $access = array( 'accessWord' => $word );
            $this->assertFalse( ( !$access['accessWord'] ) == 'yes' );
        }
        $this->assertFalse( expOAuthAuthorizePolicy::accessResultAllows( array( 'accessWord' => 'no' ), 'site' ) );
    }

    public static function enforcedProvider()
    {
        return array(
            'missing'  => array( null, true ),
            'enabled'  => array( 'enabled', true ),
            'disabled' => array( 'disabled', false ),
            'Disabled' => array( ' Disabled ', false ),
            'typo'     => array( 'disable', true ),
            'empty'    => array( '', true ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('enforcedProvider')]
    public function testIsEnforced( $value, $expected )
    {
        $this->assertSame( $expected, expOAuthAuthorizePolicy::isEnforced( self::ini( $value ) ) );
    }

    public function testUserMayAuthorize()
    {
        $denied = self::user( array( 'accessWord' => 'no' ) );
        $this->assertFalse( expOAuthAuthorizePolicy::userMayAuthorize( $denied, 'site', self::ini( 'enabled' ) ) );
        $this->assertSame( array( 'oauth/authorize' ), $denied->asked );

        $this->assertTrue( expOAuthAuthorizePolicy::userMayAuthorize( self::user( array( 'accessWord' => 'yes' ) ), 'site', self::ini( 'enabled' ) ) );

        $notAsked = self::user( array( 'accessWord' => 'no' ) );
        $this->assertTrue( expOAuthAuthorizePolicy::userMayAuthorize( $notAsked, 'site', self::ini( 'disabled' ) ) );
        $this->assertSame( array(), $notAsked->asked );
    }

    public function testTheShippedSettingEnforcesThePolicy()
    {
        $ini = (string)file_get_contents( self::root() . '/settings/rest.ini' );
        $this->assertMatchesRegularExpression( '/^\[OAuthSettings\][^\[]*^AuthorizeRequiresPolicy=enabled$/ms', $ini );
    }

    public function testTheModuleDefinesTheFunctionWithASiteAccessLimitation()
    {
        $source = (string)file_get_contents( self::root() . '/kernel/private/modules/oauth/module.php' );
        $this->assertStringContainsString( "\$FunctionList['authorize'] = array(", $source );
        $this->assertMatchesRegularExpression( "/'SiteAccess' => array\(\s*'name' => 'SiteAccess',.*'class' => 'eZSiteAccess',\s*'function' => 'siteAccessList'/s", $source );
    }

    public function testTheViewUsesThePolicyCheck()
    {
        $source = (string)file_get_contents( self::root() . '/kernel/private/modules/oauth/authorize.php' );
        $this->assertStringContainsString( 'expOAuthAuthorizePolicy::userMayAuthorize(', $source );
        $this->assertStringContainsString( "error( \$pRedirectUri, 'access_denied'", $source );
        $this->assertDoesNotMatchRegularExpression( '/!\s*\$access\s*\[[^\]]+\]\s*==/', $source );
    }

    public static function cleanDataProvider()
    {
        return array( 'common' => array( 'kernel/sql/common/cleandata.sql' ), 'sqlite' => array( 'kernel/sql/sqlite/cleandata.sql' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('cleanDataProvider')]
    public function testNewInstallationsGiveTheEditorRoleThePolicy( $file )
    {
        $sql = (string)file_get_contents( self::root() . '/' . $file );
        // role 3 is Editor
        $this->assertMatchesRegularExpression( "/\) VALUES \(\s*3,\s*0,\s*'Editor',/", $sql );
        $this->assertMatchesRegularExpression( "/INSERT INTO ezpolicy \([^)]*\) VALUES \(\s*'authorize',\s*\d+,\s*'oauth',\s*0,\s*3\s*\);/", $sql );
        preg_match_all( "/INSERT INTO ezpolicy \([^)]*\) VALUES \(\s*'[^']*',\s*(\d+),/", $sql, $ids );
        $this->assertSame( count( $ids[1] ), count( array_unique( $ids[1] ) ), 'policy ids are unique' );
    }
}

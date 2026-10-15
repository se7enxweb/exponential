<?php
/**
 * ezjscore/call is not open to anonymous users of a siteaccess that requires a login.
 *
 * [SiteAccessSettings] AnonymousAccessList only matters where RequireUserLogin=true, normally the admin
 * siteaccess, where ezjscore/call needs a signed-in user. An extension's settings rank above a
 * siteaccess's, so a siteaccess could not take the entry back if an extension listed it. The
 * shipped settings of ezjscore and of every other extension of the tree leave it out.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscoreAnonymousAccessTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezjscoreAnonymousAccessTest extends PHPUnit\Framework\TestCase
{
    /**
     * Returns the active (not commented) values of a list setting in one block of an INI file's text
     */
    public static function listValues( $ini, $block, $name )
    {
        if ( !preg_match( '/^\[' . preg_quote( $block, '/' ) . '\]\s*$(.*?)(?=^\[|\z)/ms', $ini, $m ) )
            return array();
        preg_match_all( '/^' . preg_quote( $name, '/' ) . '\[\]=(\S+)\s*$/m', $m[1], $values );
        return $values[1];
    }

    public function testReadsOnlyTheActiveValuesOfTheBlock()
    {
        $ini = "[SiteAccessSettings]\nAnonymousAccessList[]=a/b\n#AnonymousAccessList[]=c/d\n[Other]\nAnonymousAccessList[]=e/f\n";
        $this->assertSame( array( 'a/b' ), self::listValues( $ini, 'SiteAccessSettings', 'AnonymousAccessList' ) );
    }

    public function testEzjscoreCallIsNotOpenToAnonymousUsers()
    {
        $root = dirname( __DIR__, 4 );
        $ezjscore = (string)file_get_contents( "$root/extension/ezjscore/settings/site.ini.append.php" );
        $this->assertNotContains( 'ezjscore/call', self::listValues( $ezjscore, 'SiteAccessSettings', 'AnonymousAccessList' ),
            'ezjscore/call on AnonymousAccessList lets anonymous users of the admin read nodes and search' );
        // the reason it must stay off: the policy check of the view is skipped
        $this->assertContains( 'ezjscore/call', self::listValues( $ezjscore, 'RoleSettings', 'PolicyOmitList' ) );
        $this->assertContains( 'ezjscore/hello', self::listValues( $ezjscore, 'SiteAccessSettings', 'AnonymousAccessList' ) );
    }

    public function testNoSettingsFileOfTheTreeOpensItAgain()
    {
        $root = dirname( __DIR__, 4 );
        $found = array();
        foreach ( array_merge( glob( "$root/settings/{,*/,*/*/}site.ini*", GLOB_BRACE ) ?: array(),
                               glob( "$root/extension/*/settings/{,*/,*/*/}site.ini*", GLOB_BRACE ) ?: array() ) as $file )
        {
            if ( in_array( 'ezjscore/call', self::listValues( (string)file_get_contents( $file ), 'SiteAccessSettings', 'AnonymousAccessList' ), true ) )
                $found[] = substr( $file, strlen( $root ) + 1 );
        }
        $this->assertSame( array(), $found );
    }
}

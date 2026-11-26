<?php
/**
 * File containing the expUserCredentialChecksTest class
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The credential checks of the login handlers, without a database or a directory:
 * - eZLDAPUser escapes a filter value in one pass, and a DN that goes into a group filter is escaped as a value;
 * - eZTextFileUser compares login and password as strings, never an empty password, in constant time;
 * - eZUser::authenticateHash() compares the hash with hash_equals().
 */
class expUserCredentialChecksTest extends ezpTestCase
{
    public static function filterValues()
    {
        return array(
            'plain'            => array( 'jdoe', 'jdoe' ),
            'parentheses'      => array( 'a(b)', 'a\28b\29' ),
            'star'             => array( 'j*', 'j\2a' ),
            'backslash'        => array( 'a\b', 'a\5cb' ),
            'nul'              => array( "a\0b", 'a\00b' ),
            'every character'  => array( 'f*o(o)b\ar', 'f\2ao\28o\29b\5car' ),
        );
    }

    #[DataProvider( 'filterValues' )]
    public function testLdapFilterValueIsEscapedOnce( $value, $expected )
    {
        $this->assertSame( $expected, eZLDAPUser::ldap_escape( $value ) );
    }

    public function testLdapDnValueIsEscapedOnce()
    {
        $this->assertSame( 'cn\3dSmith\2c John\5c', eZLDAPUser::ldap_escape( 'cn=Smith, John\\', true ) );
    }

    public function testGroupMemberFilterEscapesTheDn()
    {
        $dn = 'cn=x)(objectClass=*,ou=people,dc=example,dc=org';
        $filter = eZLDAPUser::groupMemberFilter( 'groupOfNames', 'member', $dn );

        $this->assertSame( '(&(objectClass=groupOfNames)(member=cn=x\29\28objectClass=\2a,ou=people,dc=example,dc=org))', $filter );
        // the DN adds no filter of its own: two "(" of the frame, one of each component
        $this->assertSame( 3, substr_count( $filter, '(' ) );
        $this->assertSame( 3, substr_count( $filter, ')' ) );
    }

    public function testGroupMemberFilterKeepsAPlainDn()
    {
        $this->assertSame(
            '(&(objectClass=groupOfNames)(member=uid=jdoe,ou=people,dc=example,dc=org))',
            eZLDAPUser::groupMemberFilter( 'groupOfNames', 'member', 'uid=jdoe,ou=people,dc=example,dc=org' )
        );
    }

    public static function textFilePasswords()
    {
        return array(
            'same'                    => array( 'secret', 'secret', true ),
            'column has spaces'       => array( 'secret', " secret \n", true ),
            'other'                   => array( 'secret', 'Secret', false ),
            'numeric strings'         => array( '1000', '1e3', false ),
            'numeric strings 2'       => array( '0', '0.0', false ),
            'leading zero'            => array( '01', '1', false ),
            'empty password'          => array( '', '', false ),
            'empty column'            => array( 'secret', '', false ),
            'array posted'            => array( array( 'secret' ), 'secret', false ),
            'true'                    => array( true, 'secret', false ),
        );
    }

    #[DataProvider( 'textFilePasswords' )]
    public function testTextFilePasswordMatches( $password, $column, $expected )
    {
        $this->assertSame( $expected, eZTextFileUser::passwordMatches( $password, $column ) );
    }

    public static function textFileLogins()
    {
        return array(
            'same'            => array( 'jdoe', 'jdoe', true ),
            'other'           => array( 'jdoe', 'jdoe2', false ),
            'numeric strings' => array( '100', '1e2', false ),
            'empty'           => array( '', '', false ),
            'array posted'    => array( array( 'jdoe' ), 'jdoe', false ),
        );
    }

    #[DataProvider( 'textFileLogins' )]
    public function testTextFileLoginMatches( $login, $uid, $expected )
    {
        $this->assertSame( $expected, eZTextFileUser::lineMatchesLogin( $login, $uid ) );
    }

    public function testAuthenticateHashStillAuthenticates()
    {
        $hash = eZUser::createHash( 'jdoe', 'secret', 'site', eZUser::PASSWORD_HASH_MD5_USER );
        $this->assertTrue( eZUser::authenticateHash( 'jdoe', 'secret', 'site', eZUser::PASSWORD_HASH_MD5_USER, $hash ) );
        $this->assertFalse( eZUser::authenticateHash( 'jdoe', 'wrong', 'site', eZUser::PASSWORD_HASH_MD5_USER, $hash ) );

        $bcrypt = password_hash( 'secret', PASSWORD_BCRYPT );
        $this->assertTrue( eZUser::authenticateHash( 'jdoe', 'secret', 'site', eZUser::PASSWORD_HASH_BCRYPT, $bcrypt ) );
        $this->assertFalse( eZUser::authenticateHash( 'jdoe', 'wrong', 'site', eZUser::PASSWORD_HASH_BCRYPT, $bcrypt ) );
    }

    /**
     * Timing cannot be measured reliably in a unit test; this guards the comparison itself: the hash is compared
     * with hash_equals(), not with === (which stops at the first differing byte).
     */
    public function testAuthenticateHashComparesInConstantTime()
    {
        $method = new ReflectionMethod( 'eZUser', 'authenticateHash' );
        $lines = file( $method->getFileName() );
        $body = implode( '', array_slice( $lines, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1 ) );
        $code = preg_replace( '#//[^\n]*#', '', $body );

        $this->assertStringContainsString( 'hash_equals(', $code );
        $this->assertDoesNotMatchRegularExpression( '/\)\s*===\s*\$hash/', $code );
    }
}

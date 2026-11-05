<?php
/**
 * Generated passwords, tokens, keys and storage names come from the secure random source of PHP.
 *
 *  RND-01 - eZUser::createPassword() has the asked length, only characters of the table, and never repeats
 *  RND-02 - A seed passed to eZUser::createPassword() does not make the password reproducible
 *  RND-03 - eZUser::createPassword() draws every character with random_int(), never from time, mt_rand or md5
 *  RND-04 - REST/OAuth tokens (ezpRestToken, ezpRestTokenManager) are 40 hex characters and never repeat
 *  RND-05 - The forgot-password key is 32 hex characters from random_bytes(), with no weak fallback
 *  RND-06 - Stored files, uploads and the activation hash are named from random_bytes(), not time or mt_rand
 *  RND-07 - A user account the site installer creates gets no known password
 *
 * No database: the methods exercised are pure, and the rest is read from the source files.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group security
 */

#[\PHPUnit\Framework\Attributes\Group('security')]
class expRandomSourcesTest extends PHPUnit\Framework\TestCase
{
    /** @var mixed the character table cached by eZUser::passwordCharacterTable() before the test */
    private $savedTable;

    /** @var bool whether a table was cached before the test */
    private $hadTable = false;

    protected function setUp(): void
    {
        $this->hadTable = array_key_exists( 'eZUserPasswordCharacterTable', $GLOBALS );
        $this->savedTable = $this->hadTable ? $GLOBALS['eZUserPasswordCharacterTable'] : null;
        // The table eZUser::passwordCharacterTable() builds without special characters, so no settings are read.
        $GLOBALS['eZUserPasswordCharacterTable'] = array_values( array_diff(
            array_merge( range( 'a', 'z' ), range( 'A', 'Z' ), array_map( 'strval', range( 0, 9 ) ) ),
            array( 'I', 'l', 'o', 'O', '0' ) ) );
    }

    protected function tearDown(): void
    {
        if ( $this->hadTable )
            $GLOBALS['eZUserPasswordCharacterTable'] = $this->savedTable;
        else
            unset( $GLOBALS['eZUserPasswordCharacterTable'] );
    }

    private static function source( $relative )
    {
        $file = dirname( __DIR__, 5 ) . '/' . $relative;
        self::assertFileExists( $file );
        return (string)file_get_contents( $file );
    }

    private static function methodSource( $class, $method )
    {
        $reflection = new ReflectionMethod( $class, $method );
        $lines = file( $reflection->getFileName() );
        return implode( '', array_slice( $lines, $reflection->getStartLine() - 1,
                                         $reflection->getEndLine() - $reflection->getStartLine() + 1 ) );
    }

    // -- RND-01 ---------------------------------------------------------------------------------------------------

    public static function lengths()
    {
        return array( array( 1, 1 ), array( 8, 8 ), array( 16, 16 ), array( 17, 17 ), array( 40, 40 ),
                      array( 0, 1 ), array( -3, 1 ) );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'lengths' )]
    public function testRND01PasswordHasTheAskedLength( $asked, $expected )
    {
        self::assertSame( $expected, strlen( eZUser::createPassword( $asked ) ) );
    }

    public function testRND01PasswordUsesOnlyTheCharacterTable()
    {
        $table = array_flip( $GLOBALS['eZUserPasswordCharacterTable'] );
        $seen = array();
        for ( $i = 0; $i < 200; ++$i )
        {
            foreach ( str_split( eZUser::createPassword( 24 ) ) as $character )
            {
                self::assertArrayHasKey( $character, $table, "'$character' is not in the password character table" );
                $seen[$character] = true;
            }
        }
        // 4800 draws from 57 characters: every character turns up (missing one by chance is about 1e-34).
        self::assertCount( count( $table ), $seen, 'some characters of the table never appear' );
    }

    public function testRND01PasswordsNeverRepeat()
    {
        $passwords = array();
        for ( $i = 0; $i < 2000; ++$i )
            $passwords[] = eZUser::createPassword( 12 );
        self::assertCount( 2000, array_unique( $passwords ), 'two generated passwords were the same' );
    }

    // -- RND-02 ---------------------------------------------------------------------------------------------------

    public function testRND02SeedDoesNotMakeThePasswordReproducible()
    {
        $first = eZUser::createPassword( 16, 'the same seed' );
        $second = eZUser::createPassword( 16, 'the same seed' );
        self::assertNotSame( $first, $second, 'a known seed reproduces the password' );
    }

    // -- RND-03 ---------------------------------------------------------------------------------------------------

    public function testRND03PasswordGeneratorUsesRandomIntOnly()
    {
        $code = self::methodSource( 'eZUser', 'createPassword' );
        $body = substr( $code, strpos( $code, '{' ) );
        self::assertStringContainsString( 'random_int(', $body );
        foreach ( array( 'mt_rand', 'mt_srand', 'rand(', 'time(', 'microtime', 'uniqid', 'md5(', 'sha1(', 'lcg_value' ) as $weak )
            self::assertStringNotContainsString( $weak, $body, "createPassword() uses $weak" );
    }

    // -- RND-04 ---------------------------------------------------------------------------------------------------

    public function testRND04RestTokensAreLongAndNeverRepeat()
    {
        if ( !interface_exists( 'ezcPersistentObject' ) )
            self::markTestSkipped( 'ezcPersistentObject (expcomponents PersistentObject) is not installed' );

        foreach ( array( array( 'ezpRestToken', '' ), array( 'ezpRestTokenManager', 'scope' ) ) as $case )
        {
            list( $class, $argument ) = $case;
            $tokens = array();
            for ( $i = 0; $i < 1000; ++$i )
            {
                $token = $class::generateToken( $argument );
                self::assertMatchesRegularExpression( '/^[0-9a-f]{40}$/', $token, "$class token" );
                $tokens[] = $token;
            }
            self::assertCount( 1000, array_unique( $tokens ), "$class repeated a token" );
        }
    }

    public function testRND04TokenManagerIsNotAConstant()
    {
        $code = self::source( 'kernel/private/oauth/classes/tokenmanager.php' );
        self::assertStringNotContainsString( "md5( 'uniqid' )", $code );
        self::assertStringContainsString( 'ezpRestToken::generateToken(', $code );
    }

    // -- RND-05 ---------------------------------------------------------------------------------------------------

    public function testRND05ForgotPasswordKeyComesFromRandomBytes()
    {
        $code = self::source( 'kernel/private/classes/views/user/forgotpassword.php' );
        self::assertStringContainsString( '$hashKey = bin2hex( random_bytes( 16 ) );', $code );
        self::assertSame( 32, strlen( bin2hex( random_bytes( 16 ) ) ), 'the key fits ezforgot_password.hash_key' );
        self::assertStringNotContainsString( 'mt_rand', $code );
        self::assertStringNotContainsString( 'openssl_random_pseudo_bytes', $code );
        self::assertStringNotContainsString( 'microtime', $code );
    }

    // -- RND-06 ---------------------------------------------------------------------------------------------------

    public static function namedFiles()
    {
        return array(
            array( 'kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php', 2 ),
            array( 'kernel/classes/datatypes/ezmedia/ezmediatype.php', 2 ),
            array( 'lib/ezutils/classes/ezhttpfile.php', 1 ),
            array( 'kernel/classes/datatypes/ezbinaryfile/plugins/ezwordparser.php', 1 ),
            array( 'kernel/user/ezuseroperationcollection.php', 1 ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'namedFiles' )]
    public function testRND06NamesComeFromRandomBytes( $file, $atLeast )
    {
        $code = self::source( $file );
        self::assertGreaterThanOrEqual( $atLeast, substr_count( $code, 'bin2hex( random_bytes( 16 ) )' ) );
        self::assertSame( 0, preg_match( '/microtime\(\s*\)\s*\.\s*mt_rand\(/', $code ), 'a name from microtime() and mt_rand()' );
        self::assertSame( 0, preg_match( '/md5\(\s*(mt_rand|time)\(/', $code ), 'a name from md5( mt_rand() ) or md5( time() )' );
    }

    // -- RND-07 ---------------------------------------------------------------------------------------------------

    public function testRND07SiteInstallerUserGetsNoKnownPassword()
    {
        $code = self::source( 'kernel/classes/ezsiteinstaller.php' );
        $start = strpos( $code, '$user->setInformation( $objectID,' );
        self::assertNotFalse( $start );
        $call = substr( $code, $start, strpos( $code, ');', $start ) - $start );
        self::assertStringNotContainsString( "'publish'", $call );
        self::assertStringNotContainsString( 'mt_rand', $call );
        self::assertStringContainsString( 'random_bytes', $call );
        self::assertStringContainsString( 'eZUser::createPassword(', substr( $code, $start - 300, 300 ) );
    }
}

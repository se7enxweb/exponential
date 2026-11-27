<?php
/**
 * MonetDB's protocol MAPI in plain PHP (expMonetDBMapi), without a server: the answer to the login challenge, the
 * answers of the server read into results, row values unescaped, error lines.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expColumnarTestKit.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('columnar')]
class expMonetDBMapiTest extends PHPUnit\Framework\TestCase
{
    public function testAnswerToTheChallenge()
    {
        $challenge = "saltSALT:merovingian:9:RIPEMD160,SHA512,SHA384,SHA256,SHA224,SHA1:LIT:SHA512:sql=6:BINARY=1:\n";
        $expected = 'LIT:monetdb:{SHA512}' . hash( 'sha512', hash( 'sha512', 'secret' ) . 'saltSALT' ) . ':sql:exp:';
        $this->assertSame( $expected, expMonetDBMapi::answer( $challenge, 'monetdb', 'secret', 'exp' ) );
        // the best algorithm the server offers that PHP has
        $this->assertStringStartsWith( 'LIT:u:{SHA256}', expMonetDBMapi::answer( 's:mserver:9:SHA1,SHA256:LIT:SHA512:', 'u', 'p', 'd' ) );
        // another protocol, or a password hash PHP does not know: no answer
        $this->assertNull( expMonetDBMapi::answer( 's:mserver:8:SHA1:LIT:SHA512:', 'u', 'p', 'd' ) );
        $this->assertNull( expMonetDBMapi::answer( 's:mserver:9:SHA1:LIT:MD99:', 'u', 'p', 'd' ) );
        $this->assertNull( expMonetDBMapi::answer( 'nonsense', 'u', 'p', 'd' ) );
    }

    public function testResultSet()
    {
        $reply = "&1 3 2 3 2 47 1093 205 58\n"
               . "% sys.t,\tsys.t,\tsys.t # table_name\n"
               . "% id,\tname,\tok # name\n"
               . "% int,\tvarchar,\tboolean # type\n"
               . "% 1,\t5,\t5 # length\n"
               . "[ 1,\t\"a\\tb\",\ttrue\t]\n"
               . "[ NULL,\t\"NULL\",\tfalse\t]\n";
        $results = expMonetDBMapi::parse( $reply );
        $this->assertCount( 1, $results );
        $this->assertSame( 'table', $results[0]['type'] );
        $this->assertSame( 3, $results[0]['id'] );
        $this->assertSame( 2, $results[0]['count'] );
        $this->assertSame( array( 'id', 'name', 'ok' ), $results[0]['columns'] );
        $this->assertSame( array( 'int', 'varchar', 'boolean' ), $results[0]['types'] );
        $this->assertSame( array( array( '1', "a\tb", 'true' ), array( null, 'NULL', 'false' ) ), $results[0]['rows'],
                           'an unquoted NULL is null, the text "NULL" is text' );
    }

    public function testOtherAnswers()
    {
        $results = expMonetDBMapi::parse( "&2 1 42 -1\n&3 12 5\n&4 f\n&4 t\n" );
        $this->assertSame( array( 'type' => 'update', 'affected' => 1, 'lastid' => 42 ), $results[0] );
        $this->assertSame( array( 'type' => 'schema' ), $results[1] );
        $this->assertSame( array( 'type' => 'trans', 'autocommit' => false ), $results[2] );
        $this->assertSame( array( 'type' => 'trans', 'autocommit' => true ), $results[3] );

        $errors = expMonetDBMapi::parse( "!40002!INSERT INTO: PRIMARY KEY constraint 't.t_pkey' violated\n!continued here\n" );
        $this->assertCount( 1, $errors, 'an error of several lines is one error' );
        $this->assertSame( '40002', $errors[0]['sqlstate'] );
        $this->assertSame( "INSERT INTO: PRIMARY KEY constraint 't.t_pkey' violated\ncontinued here", $errors[0]['message'] );
        $this->assertSame( 'HY000', expMonetDBMapi::parse( "!no state here\n" )[0]['sqlstate'] );
        $this->assertSame( array(), expMonetDBMapi::parse( "#a comment\n" ) );
    }

    public static function rows()
    {
        return array(
            'numbers' => array( "[ 1,\t-2.5,\t3e10\t]", array( '1', '-2.5', '3e10' ) ),
            'escapes' => array( "[ \"tab\\there\",\t\"line\\nbreak\",\t\"back\\\\slash\",\t\"quote\\\"d\"\t]",
                                array( "tab\there", "line\nbreak", 'back\\slash', 'quote"d' ) ),
            'octal' => array( "[ \"\\303\\274\"\t]", array( 'ü' ) ),
            'unicode' => array( "[ \"ünïcödé\",\t\"\"\t]", array( 'ünïcödé', '' ) ),
            'separators inside text' => array( "[ \"a,\tb\",\t\"]\"\t]", array( "a,\tb", ']' ) ),
            'a timestamp' => array( "[ 2026-01-02 03:04:05.250000\t]", array( '2026-01-02 03:04:05.250000' ) ),
            'null' => array( "[ NULL\t]", array( null ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('rows')]
    public function testRowValues( $line, array $expected )
    {
        $this->assertSame( $expected, expMonetDBMapi::parseRow( $line ) );
    }

    public function testErrorLines()
    {
        $this->assertSame( "SELECT: no such table 'x'", expMonetDBMapi::errorText( "!42S02!SELECT: no such table 'x'" ) );
        $this->assertSame( '42S02', expMonetDBMapi::errorState( "!42S02!SELECT: no such table 'x'", 'HY000' ) );
        $this->assertSame( 'InvalidCredentialsException:checkCredentials:invalid credentials',
                           expMonetDBMapi::errorText( '!InvalidCredentialsException:checkCredentials:invalid credentials' ) );
        $this->assertSame( '28000', expMonetDBMapi::errorState( '!InvalidCredentialsException:checkCredentials', '28000' ) );
    }

    public function testNoServer()
    {
        $mapi = new expMonetDBMapi( 2 );
        // a port nothing listens on: refused at once
        $this->assertFalse( $mapi->connect( '127.0.0.1', 1, 'u', 'p', 'd', 2 ) );
        $this->assertSame( '08001', $mapi->SQLState );
        $this->assertFalse( $mapi->isConnected() );
        $this->assertFalse( $mapi->query( 'SELECT 1' ) );
        $this->assertSame( '08003', $mapi->SQLState );
    }
}

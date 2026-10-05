<?php
/**
 * eZPHPCreator (lib/ezutils), which writes the PHP of compiled templates and of many caches: what it writes must
 * read back as exactly the value it was given.
 *   - variableText() and thisVariableText() of a table of values evaluated back with ===: booleans, null, integers,
 *     floats (whole ones stay floats), strings with quotes, backslashes, dollar signs, new lines and multi-byte
 *     text, lists, hashes with keys that need escaping (a $ in a key), nested arrays, numeric and negative keys
 *   - fetch() of defines, variables (assignment, append text, append element), unsets, comments, raw text, code
 *     pieces, includes and method calls, evaluated as PHP
 *   - store() and restore(): every stored variable comes back, a variable stored as null too, optional variables
 *     with defaults, a missing required variable
 *   - prependSpacing() and variableNameText()
 *
 * Files go to a private directory under var/tmp that tearDown() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group ezutils
 */

class eZPHPCreatorRoundTripTest extends PHPUnit\Framework\TestCase
{
    private $dir;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/phpunit-ezphpcreator-' . getmypid() . '-' . substr( md5( uniqid( '', true ) ), 0, 8 );
        mkdir( $this->dir, 0777, true );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    public static function valueProvider()
    {
        return array(
            'true'                  => array( true ),
            'false'                 => array( false ),
            'null'                  => array( null ),
            'int'                   => array( 42 ),
            'negative int'          => array( -7 ),
            'zero'                  => array( 0 ),
            'float'                 => array( 1.25 ),
            'whole float'           => array( 2.0 ),
            'negative float'        => array( -0.5 ),
            'large float'           => array( 1.0e25 ),
            'string'                => array( 'plain' ),
            'empty string'          => array( '' ),
            'quotes'                => array( 'say "hi" and \'bye\'' ),
            'backslashes'           => array( 'C:\\path\\to\\n' ),
            'dollar'                => array( 'costs $5 and ${x}' ),
            'new lines'             => array( "line 1\nline 2\r\n\ttab" ),
            'multi-byte'            => array( 'grüße 日本' ),
            'numeric string'        => array( '0012' ),
            'list'                  => array( array( 1, 'two', 3.5, null, false ) ),
            'empty array'           => array( array() ),
            'hash'                  => array( array( 'a' => 1, 'b' => 'x' ) ),
            'key with a dollar'     => array( array( 'a$b' => 1, '$x' => 2 ) ),
            'key with quote and nl' => array( array( "k\"q" => 1, "n\nl" => 2, 'b\\s' => 3 ) ),
            'numeric keys'          => array( array( 5 => 'five', -1 => 'minus', 0 => 'zero' ) ),
            'nested'                => array( array( 'level1' => array( 'level2' => array( 'level3' => array( 'deep' ) ) ), 'list' => array( array( 1 ), array( 2 ) ) ) ),
        );
    }

    /**
     * Keys with a $ were written between double quotes without escaping, so reading the file back interpolated a
     * variable; whole floats were written as integers.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('valueProvider')]
    public function testVariableTextReadsBack( $value )
    {
        $text = eZPHPCreator::variableText( $value );
        $this->assertSame( $value, eval( 'return ' . $text . ';' ), $text );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('valueProvider')]
    public function testThisVariableTextReadsBack( $value )
    {
        $php = new eZPHPCreator( $this->dir, 'unused.php' );
        $text = $php->thisVariableText( $value, 0, 0, false );
        $this->assertSame( $value, eval( 'return ' . $text . ';' ), $text );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('valueProvider')]
    public function testFetchedVariableReadsBack( $value )
    {
        $php = new eZPHPCreator( $this->dir, 'unused.php' );
        $php->addVariable( 'value', $value );
        $code = $php->fetch( false );
        $result = ( function () use ( $code ) { eval( $code ); return $value; } )();
        $this->assertSame( $value, $result, $code );
    }

    public function testFetchedElementsRun()
    {
        $php = new eZPHPCreator( $this->dir, 'unused.php' );
        $php->addComment( "a comment\nover two lines" );
        $php->addDefine( 'X3_PHPCREATOR_TEST_CONSTANT_' . getmypid(), 'defined' );
        $php->addVariable( 'text', 'a' );
        $php->addVariable( 'text', 'b', eZPHPCreator::VARIABLE_APPEND_TEXT );
        $php->addVariable( 'list', array( 1 ) );
        $php->addVariable( 'list', 2, eZPHPCreator::VARIABLE_APPEND_ELEMENT );
        $php->addVariable( 'gone', 'x' );
        $php->addVariableUnset( 'gone' );
        $php->addVariable( 'g1', 1 );
        $php->addVariable( 'g2', 2 );
        $php->addVariableUnsetList( array( 'g1', 'g2' ) );
        $php->addRawVariable( 'raw', 'raw value' );
        $php->addSpace();
        $php->addCodePiece( "\$code = 'from code';\n" );
        $php->addMethodCall( 'helper', 'concat', array( array( 'x' ), array( 'y' ) ), array( 'joined' ) );
        $text = $php->fetch();
        $this->assertStringStartsWith( '<?php', $text );
        $this->assertStringContainsString( '// a comment', $text );

        $body = substr( $text, 5, -3 );
        $result = ( function () use ( $body ) {
            $helper = new class { function concat( $a, $b ) { return $a . $b; } };
            eval( $body );
            return array( $text, $list, isset( $gone ), isset( $g1 ) || isset( $g2 ), $raw, $code, $joined );
        } )();
        $this->assertSame( array( 'ab', array( 1, 2 ), false, false, 'raw value', 'from code', 'xy' ), $result );
        $this->assertSame( 'defined', constant( 'X3_PHPCREATOR_TEST_CONSTANT_' . getmypid() ) );
    }

    public function testStoreAndRestore()
    {
        $values = array( 'number' => 3, 'nothing' => null, 'list' => array( 'a', 'b' ), 'float' => 2.0 );
        $php = new eZPHPCreator( $this->dir, 'store.php' );
        foreach ( $values as $name => $value )
            $php->addVariable( $name, $value );
        $this->assertTrue( $php->store() );

        $restore = new eZPHPCreator( $this->dir, 'store.php' );
        $this->assertTrue( $restore->exists() );
        $this->assertSame( $values, $restore->restore( array( 'number' => 'number', 'nothing' => 'nothing', 'list' => 'list', 'float' => 'float' ) ),
                           'a variable stored as null comes back' );

        $restored = $restore->restore( array(
            'n' => 'number',
            'opt' => array( 'name' => 'missing', 'required' => false, 'default' => 'fallback' ),
            'opt2' => array( 'name' => 'missing2', 'required' => false ),
        ) );
        $this->assertSame( array( 'n' => 3, 'opt' => 'fallback', 'opt2' => false ), $restored );
        $this->assertSame( array(), @$restore->restore( array( 'x' => 'not_there' ) ), 'a required variable that is missing' );
    }

    public function testHelpers()
    {
        $this->assertSame( "  a\n\n  b", eZPHPCreator::prependSpacing( "a\n\nb", 2 ) );
        $this->assertSame( "--a\n--\n--b", eZPHPCreator::prependSpacing( "a\n\nb", 2, false, '-' ) );
        $this->assertSame( '$x = ', eZPHPCreator::variableNameText( 'x', eZPHPCreator::VARIABLE_ASSIGNMENT ) );
        $this->assertSame( '$x .= ', eZPHPCreator::variableNameText( 'x', eZPHPCreator::VARIABLE_APPEND_TEXT ) );
        $this->assertSame( '$x[] = ', eZPHPCreator::variableNameText( 'x', eZPHPCreator::VARIABLE_APPEND_ELEMENT ) );
    }
}

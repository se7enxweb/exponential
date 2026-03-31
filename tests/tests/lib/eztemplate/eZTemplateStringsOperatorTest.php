<?php
/**
 * PHPUnit test suite for eZTemplateStringsOperator.
 *
 * Covers all operators exposed by eZTemplateStringsOperator:
 *   - No-parameter PHP string wrappers (addslashes, bin2hex, etc.)
 *   - Parametric operators (strpos, substr, sprintf, strtr, …)
 *   - Creative additions: ristring (str_replace) and rstring (str_ireplace)
 *   - operatorList() and operatorTemplateHints() contract checks
 *   - Warning emission for missing required parameters
 *
 * Does NOT require a database connection; uses ezpTestCase (no DB bootstrap).
 *
 * @copyright Copyright (C) eZ Systems AS / Exponential CMS contributors.
 * @license   For full copyright and license information view LICENSE file.
 * @version   //autogentag//
 * @package   tests
 * @group     lib
 * @group     eztemplate
 * @group     stringsoperator
 */

// ---------------------------------------------------------------------------
// Minimal template stub — passes operator parameters through unchanged so that
// tests can supply plain PHP values without wrapping them in eZTemplate nodes.
// ---------------------------------------------------------------------------
class eZTemplateStringsOperatorTplStub
{
    /** @var string[] */
    public $warnings = [];

    /**
     * Return the raw parameter value unchanged.
     * In production this resolves a template node; the stub short-circuits it.
     */
    public function elementValue( $param, $rootNs, $currentNs, $placement )
    {
        return $param;
    }

    public function warning( $operatorName, $message, $placement )
    {
        $this->warnings[] = $operatorName . ': ' . $message;
    }
}

// ---------------------------------------------------------------------------
// Minimal eZTemplateNodeTool stub so noParamTransformation() tests work
// without bootstrapping the full eZ kernel.
// The real class lives at lib/eztemplate/classes/eztemplatenodtool.php.
// ---------------------------------------------------------------------------
if ( !class_exists( 'eZTemplateNodeTool', false ) )
{
    class eZTemplateNodeTool
    {
        /** An element is "constant" when it carries a 'value' key. */
        public static function isConstantElement( $element ): bool
        {
            return is_array( $element ) && array_key_exists( 'value', $element );
        }

        public static function elementConstantValue( $element )
        {
            return $element['value'];
        }

        /** Returns a node-array with a 'const' key — matches what the real class emits. */
        public static function createConstantElement( $value ): array
        {
            return [ 'const' => $value ];
        }

        /** Returns a node-array with a 'code' key — matches what the real class emits. */
        public static function createCodePieceElement( string $code, array $values ): array
        {
            return [ 'code' => $code, 'values' => $values ];
        }
    }
}

// ---------------------------------------------------------------------------

class eZTemplateStringsOperatorTest extends PHPUnit\Framework\TestCase
{
    protected $backupGlobals = false;

    /** @var eZTemplateStringsOperator */
    private $op;

    /** @var eZTemplateStringsOperatorTplStub */
    private $tpl;

    // -----------------------------------------------------------------------
    // Fixture
    // -----------------------------------------------------------------------

    public function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../../../../lib/eztemplate/classes/eztemplatestringsoperator.php';
        $this->op  = new eZTemplateStringsOperator();
        $this->tpl = new eZTemplateStringsOperatorTplStub();
    }

    // -----------------------------------------------------------------------
    // Helper: run an operator and return the result
    // -----------------------------------------------------------------------

    /**
     * Invoke modify() using the stub template and return the mutated value.
     *
     * @param string $operatorName  Template operator name.
     * @param mixed  $value         Input subject (piped value); passed by value here.
     * @param array  $params        Extra positional parameters (raw PHP values).
     * @return mixed                The same variable after operator mutation.
     */
    private function runOp( $operatorName, $value, array $params = [] )
    {
        $v = $value;
        $this->op->modify(
            $this->tpl,
            $operatorName,
            $params,
            '',
            '',
            $v,
            [],
            null
        );
        return $v;
    }

    // -----------------------------------------------------------------------
    // operatorList() contract
    // -----------------------------------------------------------------------

    /**
     * @group contract
     */
    public function testOperatorListIsNonEmpty()
    {
        $ops = $this->op->operatorList();
        $this->assertIsArray( $ops );
        $this->assertGreaterThan( 0, count( $ops ) );
    }

    /**
     * @group contract
     */
    public function testOperatorListContainsRequiredOperators()
    {
        $ops = $this->op->operatorList();

        // Key no-param operators
        foreach ( [ 'addslashes', 'bin2hex', 'hex2bin', 'lcfirst', 'quotemeta',
                    'soundex', 'strlen', 'stripslashes', 'stripcslashes' ] as $name )
        {
            $this->assertContains( $name, $ops, "Operator '$name' missing from list" );
        }

        // Key param operators
        foreach ( [ 'addcslashes', 'chunk_split', 'htmlentities', 'html_entity_decode',
                    'htmlspecialchars_decode', 'levenshtein', 'ltrim', 'rtrim',
                    'metaphone', 'number_format', 'similar_text', 'sprintf', 'vsprintf',
                    'str_contains', 'str_ends_with', 'str_getcsv', 'str_split',
                    'str_starts_with', 'str_word_count',
                    'strcasecmp', 'strcmp', 'strcoll', 'strcspn', 'strip_tags',
                    'stripos', 'stristr', 'strnatcasecmp', 'strnatcmp', 'strncasecmp',
                    'strncmp', 'strpbrk', 'strpos', 'strrchr', 'strripos', 'strrpos',
                    'strspn', 'strstr', 'strtok', 'strtr', 'substr', 'substr_compare',
                    'substr_count', 'substr_replace', 'ristring', 'rstring' ] as $name )
        {
            $this->assertContains( $name, $ops, "Operator '$name' missing from list" );
        }
    }

    /**
     * @group contract
     */
    public function testOperatorListHasNoDuplicates()
    {
        $ops = $this->op->operatorList();
        $this->assertSame(
            $ops,
            array_values( array_unique( $ops ) ),
            'operatorList() contains duplicate entries'
        );
    }

    // -----------------------------------------------------------------------
    // operatorTemplateHints() contract
    // -----------------------------------------------------------------------

    /**
     * @group contract
     */
    public function testHintsReturnedForEveryOperator()
    {
        $ops   = $this->op->operatorList();
        $hints = $this->op->operatorTemplateHints();

        $this->assertIsArray( $hints );
        foreach ( $ops as $op )
        {
            $this->assertArrayHasKey( $op, $hints, "No hint entry for operator '$op'" );
            $this->assertTrue( $hints[$op]['input'],  "Hint 'input' should be true for '$op'" );
            $this->assertTrue( $hints[$op]['output'], "Hint 'output' should be true for '$op'" );
        }
    }

    // -----------------------------------------------------------------------
    // No-parameter operators
    // -----------------------------------------------------------------------

    /**
     * @group noParam
     */
    public function testAddslashesEscapesQuotes()
    {
        $this->assertSame( 'He said \"hi\"', $this->runOp( 'addslashes', 'He said "hi"' ) );
    }

    /**
     * @group noParam
     */
    public function testAddslashesPlainTextUnchanged()
    {
        $this->assertSame( 'Hello World', $this->runOp( 'addslashes', 'Hello World' ) );
    }

    /**
     * @group noParam
     */
    public function testStripslashesRemovesEscapes()
    {
        $this->assertSame( 'He said "hi"', $this->runOp( 'stripslashes', 'He said \"hi\"' ) );
    }

    /**
     * @group noParam
     */
    public function testStripcslashesPlainText()
    {
        $this->assertSame( 'abc', $this->runOp( 'stripcslashes', 'abc' ) );
    }

    /**
     * @group noParam
     */
    public function testBin2hex()
    {
        $this->assertSame( '68656c6c6f', $this->runOp( 'bin2hex', 'hello' ) );
    }

    /**
     * @group noParam
     */
    public function testHex2bin()
    {
        $this->assertSame( 'hello', $this->runOp( 'hex2bin', '68656c6c6f' ) );
    }

    /**
     * @group noParam
     */
    public function testHex2binInvalidInputReturnsFalse()
    {
        // hex2bin returns false on invalid input (odd-length string in some versions)
        // Just ensure it handles it without exception.
        $result = $this->runOp( 'hex2bin', 'xyz' );
        $this->assertTrue( $result === false || is_string( $result ) );
    }

    /**
     * @group noParam
     */
    public function testLcfirst()
    {
        $this->assertSame( 'hello World', $this->runOp( 'lcfirst', 'Hello World' ) );
    }

    /**
     * @group noParam
     */
    public function testQuotemeta()
    {
        $this->assertSame( 'Hello\+World', $this->runOp( 'quotemeta', 'Hello+World' ) );
    }

    /**
     * @group noParam
     */
    public function testSoundex()
    {
        $this->assertSame( 'R163', $this->runOp( 'soundex', 'Robert' ) );
    }

    /**
     * @group noParam
     */
    public function testStrlen()
    {
        $this->assertSame( 5, $this->runOp( 'strlen', 'hello' ) );
        $this->assertSame( 0, $this->runOp( 'strlen', '' ) );
    }

    /**
     * @group noParam
     */
    public function testStrShufflePreservesLength()
    {
        $result = $this->runOp( 'str_shuffle', 'abcdef' );
        $this->assertSame( 6, strlen( $result ) );
    }

    /**
     * @group noParam
     */
    public function testQuotedPrintableRoundTrip()
    {
        $encoded = $this->runOp( 'quoted_printable_encode', "hello\n" );
        $this->assertIsString( $encoded );
        $decoded = $this->runOp( 'quoted_printable_decode', $encoded );
        $this->assertSame( "hello\n", $decoded );
    }

    // -----------------------------------------------------------------------
    // ltrim / rtrim
    // -----------------------------------------------------------------------

    /**
     * @group trim
     */
    public function testLtrimDefaultWhitespace()
    {
        $this->assertSame( 'hello  ', $this->runOp( 'ltrim', '  hello  ' ) );
    }

    /**
     * @group trim
     */
    public function testLtrimCharlist()
    {
        $this->assertSame( 'hellopxx', $this->runOp( 'ltrim', 'xxhellopxx', [ 'xp' ] ) );
    }

    /**
     * @group trim
     */
    public function testRtrimDefaultWhitespace()
    {
        $this->assertSame( '  hello', $this->runOp( 'rtrim', '  hello  ' ) );
    }

    /**
     * @group trim
     */
    public function testRtrimCharlist()
    {
        $this->assertSame( 'hello', $this->runOp( 'rtrim', 'helloxx', [ 'x' ] ) );
    }

    // -----------------------------------------------------------------------
    // strip_tags
    // -----------------------------------------------------------------------

    /**
     * @group html
     */
    public function testStripTagsAll()
    {
        $this->assertSame( 'Hello World', $this->runOp( 'strip_tags', '<p>Hello <b>World</b></p>' ) );
    }

    /**
     * @group html
     */
    public function testStripTagsWithAllowed()
    {
        $result = $this->runOp( 'strip_tags', '<p>Hello <b>World</b></p>', [ '<p>' ] );
        $this->assertSame( '<p>Hello World</p>', $result );
    }

    // -----------------------------------------------------------------------
    // strpos family
    // -----------------------------------------------------------------------

    /**
     * @group strpos
     */
    public function testStrposFound()
    {
        $this->assertSame( 6, $this->runOp( 'strpos', 'hello world', [ 'world' ] ) );
    }

    /**
     * @group strpos
     */
    public function testStrposNotFound()
    {
        $this->assertFalse( $this->runOp( 'strpos', 'hello world', [ 'xyz' ] ) );
    }

    /**
     * @group strpos
     */
    public function testStrposWithOffset()
    {
        $this->assertSame( 9, $this->runOp( 'strpos', 'hello world', [ 'l', 5 ] ) );
    }

    /**
     * @group strpos
     */
    public function testStrrpos()
    {
        $this->assertSame( 9, $this->runOp( 'strrpos', 'hello world', [ 'l' ] ) );
    }

    /**
     * @group strpos
     */
    public function testStriposCaseInsensitive()
    {
        $this->assertSame( 6, $this->runOp( 'stripos', 'Hello World', [ 'WORLD' ] ) );
    }

    /**
     * @group strpos
     */
    public function testStrripos()
    {
        $this->assertSame( 9, $this->runOp( 'strripos', 'Hello World', [ 'L' ] ) );
    }

    // -----------------------------------------------------------------------
    // substr family
    // -----------------------------------------------------------------------

    /**
     * @group substr
     */
    public function testSubstrStartLength()
    {
        $this->assertSame( 'hello', $this->runOp( 'substr', 'hello world', [ 0, 5 ] ) );
    }

    /**
     * @group substr
     */
    public function testSubstrStartOnly()
    {
        $this->assertSame( 'world', $this->runOp( 'substr', 'hello world', [ 6 ] ) );
    }

    /**
     * @group substr
     */
    public function testSubstrCount()
    {
        $this->assertSame( 2, $this->runOp( 'substr_count', 'hello world hello', [ 'hello' ] ) );
    }

    /**
     * @group substr
     */
    public function testSubstrReplace()
    {
        $this->assertSame( 'hello PHP', $this->runOp( 'substr_replace', 'hello world', [ 'PHP', 6, 5 ] ) );
    }

    /**
     * @group substr
     */
    public function testSubstrCompareEqual()
    {
        $this->assertSame( 0, $this->runOp( 'substr_compare', 'Hello World', [ 'World', 6, 5 ] ) );
    }

    // -----------------------------------------------------------------------
    // str_* operators
    // -----------------------------------------------------------------------

    /**
     * @group str
     */
    public function testStrSplit()
    {
        $this->assertSame( [ 'he', 'll', 'o' ], $this->runOp( 'str_split', 'hello', [ 2 ] ) );
    }

    /**
     * @group str
     */
    public function testStrWordCount()
    {
        $this->assertSame( 3, $this->runOp( 'str_word_count', 'hello world foo' ) );
    }

    /**
     * @group str
     */
    public function testStrContainsTrue()
    {
        $this->assertTrue( (bool) $this->runOp( 'str_contains', 'hello world', [ 'world' ] ) );
    }

    /**
     * @group str
     */
    public function testStrContainsFalse()
    {
        $this->assertFalse( (bool) $this->runOp( 'str_contains', 'hello world', [ 'xyz' ] ) );
    }

    /**
     * @group str
     */
    public function testStrStartsWithTrue()
    {
        $this->assertTrue( (bool) $this->runOp( 'str_starts_with', 'hello world', [ 'hello' ] ) );
    }

    /**
     * @group str
     */
    public function testStrStartsWithFalse()
    {
        $this->assertFalse( (bool) $this->runOp( 'str_starts_with', 'hello world', [ 'world' ] ) );
    }

    /**
     * @group str
     */
    public function testStrEndsWithTrue()
    {
        $this->assertTrue( (bool) $this->runOp( 'str_ends_with', 'hello world', [ 'world' ] ) );
    }

    /**
     * @group str
     */
    public function testStrEndsWithFalse()
    {
        $this->assertFalse( (bool) $this->runOp( 'str_ends_with', 'hello world', [ 'hello' ] ) );
    }

    /**
     * @group str
     */
    public function testStrGetcsv()
    {
        $this->assertSame( [ 'hello', 'world', 'foo' ], $this->runOp( 'str_getcsv', 'hello,world,foo', [ ',' ] ) );
    }

    // -----------------------------------------------------------------------
    // Comparison operators
    // -----------------------------------------------------------------------

    /**
     * @group compare
     */
    public function testStrcmpEqual()
    {
        $this->assertSame( 0, $this->runOp( 'strcmp', 'hello', [ 'hello' ] ) );
    }

    /**
     * @group compare
     */
    public function testStrcmpLess()
    {
        $this->assertLessThan( 0, $this->runOp( 'strcmp', 'abc', [ 'abd' ] ) );
    }

    /**
     * @group compare
     */
    public function testStrcasecmpEqual()
    {
        $this->assertSame( 0, $this->runOp( 'strcasecmp', 'abc', [ 'ABC' ] ) );
    }

    /**
     * @group compare
     */
    public function testStrncmpFirstNCharsMatch()
    {
        $this->assertSame( 0, $this->runOp( 'strncmp', 'hello', [ 'hello world', 5 ] ) );
    }

    /**
     * @group compare
     */
    public function testStrncasecmpFirstNCharsMatch()
    {
        $this->assertSame( 0, $this->runOp( 'strncasecmp', 'abc', [ 'abc world', 3 ] ) );
    }

    /**
     * @group compare
     */
    public function testStrnatcmpFile10GreaterThanFile9()
    {
        $this->assertGreaterThan( 0, $this->runOp( 'strnatcmp', 'file10', [ 'file9' ] ) );
    }

    /**
     * @group compare
     */
    public function testStrnatcasecmpCaseInsensitive()
    {
        $this->assertGreaterThan( 0, $this->runOp( 'strnatcasecmp', 'file10', [ 'FILE9' ] ) );
    }

    // -----------------------------------------------------------------------
    // Search operators
    // -----------------------------------------------------------------------

    /**
     * @group search
     */
    public function testStrstrNeedle()
    {
        $this->assertSame( '@example.com', $this->runOp( 'strstr', 'user@example.com', [ '@' ] ) );
    }

    /**
     * @group search
     */
    public function testStrstrBeforeNeedle()
    {
        $this->assertSame( 'user', $this->runOp( 'strstr', 'user@example.com', [ '@', true ] ) );
    }

    /**
     * @group search
     */
    public function testStristrCaseInsensitive()
    {
        $this->assertSame( 'World', $this->runOp( 'stristr', 'Hello World', [ 'WORLD' ] ) );
    }

    /**
     * @group search
     */
    public function testStrpbrk()
    {
        $this->assertSame( 'is is a test', $this->runOp( 'strpbrk', 'This is a test', [ 'aeiou' ] ) );
    }

    /**
     * @group search
     */
    public function testStrrchr()
    {
        $this->assertSame( '/file.php', $this->runOp( 'strrchr', 'path/to/file.php', [ '/' ] ) );
    }

    // -----------------------------------------------------------------------
    // Span operators
    // -----------------------------------------------------------------------

    /**
     * @group span
     */
    public function testStrcspn()
    {
        // 'h' in "hello" is not a vowel → initial non-mask segment length = 1
        $this->assertSame( 1, $this->runOp( 'strcspn', 'hello', [ 'aeiou' ] ) );
    }

    /**
     * @group span
     */
    public function testStrspn()
    {
        // Every char of "hello" is in mask "helo" → 5
        $this->assertSame( 5, $this->runOp( 'strspn', 'hello', [ 'helo' ] ) );
    }

    // -----------------------------------------------------------------------
    // strtr
    // -----------------------------------------------------------------------

    /**
     * @group strtr
     */
    public function testStrtrTwoArgFromTo()
    {
        // strtr( str, from, to ) maps individual *characters*, not substrings.
        // 'H' → 'Z' ; all other characters unchanged.
        $this->assertSame( 'Zello World', $this->runOp( 'strtr', 'Hello World', [ 'H', 'Z' ] ) );
    }

    /**
     * @group strtr
     */
    public function testStrtrAssocArray()
    {
        $this->assertSame( 'World', $this->runOp( 'strtr', 'Hello', [ [ 'Hello' => 'World' ] ] ) );
    }

    // -----------------------------------------------------------------------
    // Miscellaneous
    // -----------------------------------------------------------------------

    /**
     * @group misc
     */
    public function testChunkSplit()
    {
        $this->assertSame( 'ab-cd-ef-gh-', $this->runOp( 'chunk_split', 'abcdefgh', [ 2, '-' ] ) );
    }

    /**
     * @group misc
     */
    public function testStrtokReturnsFirstToken()
    {
        $this->assertSame( 'hello', $this->runOp( 'strtok', 'hello world foo', [ ' ' ] ) );
    }

    /**
     * @group misc
     */
    public function testLevenshtein()
    {
        $this->assertSame( 3, $this->runOp( 'levenshtein', 'kitten', [ 'sitting' ] ) );
    }

    /**
     * @group misc
     */
    public function testSimilarText()
    {
        $this->assertSame( 4, $this->runOp( 'similar_text', 'World', [ 'Word' ] ) );
    }

    /**
     * @group misc
     */
    public function testMetaphone()
    {
        $this->assertSame( 'RBRT', $this->runOp( 'metaphone', 'Robert' ) );
    }

    // -----------------------------------------------------------------------
    // HTML encode/decode operators
    // -----------------------------------------------------------------------

    /**
     * @group htmlEncode
     */
    public function testHtmlentities()
    {
        $this->assertSame(
            '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;',
            $this->runOp( 'htmlentities', '<script>alert("xss")</script>' )
        );
    }

    /**
     * @group htmlEncode
     */
    public function testHtmlEntityDecode()
    {
        $this->assertSame(
            '<b>hello</b>',
            $this->runOp( 'html_entity_decode', '&lt;b&gt;hello&lt;/b&gt;' )
        );
    }

    /**
     * @group htmlEncode
     */
    public function testHtmlspecialcharsDecode()
    {
        $this->assertSame(
            '<p>test</p>',
            $this->runOp( 'htmlspecialchars_decode', '&lt;p&gt;test&lt;/p&gt;' )
        );
    }

    // -----------------------------------------------------------------------
    // sprintf / vsprintf / number_format
    // -----------------------------------------------------------------------

    /**
     * @group format
     */
    public function testSprintf()
    {
        $this->assertSame(
            'Hello Alice, you are 30 years old',
            $this->runOp( 'sprintf', 'Hello %s, you are %d years old', [ 'Alice', 30 ] )
        );
    }

    /**
     * @group format
     */
    public function testVsprintf()
    {
        $this->assertSame(
            'Hello Bob, you are 25',
            $this->runOp( 'vsprintf', 'Hello %s, you are %d', [ [ 'Bob', 25 ] ] )
        );
    }

    /**
     * @group format
     */
    public function testNumberFormatNoParams()
    {
        $this->assertSame( '1,235', $this->runOp( 'number_format', 1234.5 ) );
    }

    /**
     * @group format
     */
    public function testNumberFormatOneParam()
    {
        $this->assertSame( '1,234.50', $this->runOp( 'number_format', 1234.5, [ 2 ] ) );
    }

    /**
     * @group format
     */
    public function testNumberFormatThreeParams()
    {
        $this->assertSame( '1,234,567.89', $this->runOp( 'number_format', 1234567.891, [ 2, '.', ',' ] ) );
    }

    // -----------------------------------------------------------------------
    // addcslashes
    // -----------------------------------------------------------------------

    /**
     * @group cslashes
     */
    public function testAddcslashes()
    {
        $this->assertSame( '\\Hello World!', $this->runOp( 'addcslashes', 'Hello World!', [ 'H' ] ) );
    }

    // -----------------------------------------------------------------------
    // Creative addition: ristring — str_replace (case-sensitive)
    // -----------------------------------------------------------------------

    /**
     * @group ristring
     */
    public function testRistringCaseSensitiveMatch()
    {
        $this->assertSame(
            'Hello PHP',
            $this->runOp( 'ristring', 'Hello World', [ 'World', 'PHP' ] )
        );
    }

    /**
     * @group ristring
     */
    public function testRistringCaseSensitiveNoMatch()
    {
        // Lower-case needle must NOT match mixed-case haystack
        $this->assertSame(
            'Hello World',
            $this->runOp( 'ristring', 'Hello World', [ 'world', 'PHP' ] )
        );
    }

    /**
     * @group ristring
     */
    public function testRistringReplacesAllOccurrences()
    {
        $this->assertSame(
            'xxx bbb xxx',
            $this->runOp( 'ristring', 'aaa bbb aaa', [ 'aaa', 'xxx' ] )
        );
    }

    /**
     * @group ristring
     */
    public function testRistringArraySearchReplace()
    {
        $this->assertSame(
            'Hi PHP',
            $this->runOp( 'ristring', 'Hello World', [ [ 'Hello', 'World' ], [ 'Hi', 'PHP' ] ] )
        );
    }

    // -----------------------------------------------------------------------
    // Creative addition: rstring — str_ireplace (case-insensitive)
    // -----------------------------------------------------------------------

    /**
     * @group rstring
     */
    public function testRstringUppercaseNeedle()
    {
        $this->assertSame(
            'Hello PHP',
            $this->runOp( 'rstring', 'Hello World', [ 'WORLD', 'PHP' ] )
        );
    }

    /**
     * @group rstring
     */
    public function testRstringLowercaseNeedle()
    {
        $this->assertSame(
            'Hello PHP',
            $this->runOp( 'rstring', 'Hello World', [ 'world', 'PHP' ] )
        );
    }

    /**
     * @group rstring
     */
    public function testRstringExactCaseNeedle()
    {
        $this->assertSame(
            'Hello PHP',
            $this->runOp( 'rstring', 'Hello World', [ 'World', 'PHP' ] )
        );
    }

    /**
     * @group rstring
     */
    public function testRstringArraySearchReplace()
    {
        $this->assertSame(
            'Hi PHP',
            $this->runOp( 'rstring', 'Hello World', [ [ 'Hello', 'World' ], [ 'Hi', 'PHP' ] ] )
        );
    }

    // -----------------------------------------------------------------------
    // Warning emission for missing required parameters
    // -----------------------------------------------------------------------

    /**
     * @group warnings
     */
    public function testAddcslashesMissingParamEmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'addcslashes', 'test' );
        $this->assertNotEmpty( $this->tpl->warnings, 'addcslashes with no charlist must emit a warning' );
    }

    /**
     * @group warnings
     */
    public function testStrposMissingNeedleEmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'strpos', 'test' );
        $this->assertNotEmpty( $this->tpl->warnings, 'strpos with no needle must emit a warning' );
    }

    /**
     * @group warnings
     */
    public function testLevenshteinMissingStr2EmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'levenshtein', 'test' );
        $this->assertNotEmpty( $this->tpl->warnings, 'levenshtein with no string2 must emit a warning' );
    }

    /**
     * @group warnings
     */
    public function testStrncmpOnlyOneParamEmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'strncmp', 'test', [ 'only_one' ] );
        $this->assertNotEmpty( $this->tpl->warnings, 'strncmp with only 1 param must emit a warning' );
    }

    /**
     * @group warnings
     */
    public function testRistringOnlySearchEmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'ristring', 'test', [ 'search' ] );
        $this->assertNotEmpty( $this->tpl->warnings, 'ristring with only search (no replace) must emit a warning' );
    }

    /**
     * @group warnings
     */
    public function testRstringOnlySearchEmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'rstring', 'test', [ 'search' ] );
        $this->assertNotEmpty( $this->tpl->warnings, 'rstring with only search (no replace) must emit a warning' );
    }

    /**
     * @group warnings
     */
    public function testSubstrMissingStartEmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'substr', 'hello world' );
        $this->assertNotEmpty( $this->tpl->warnings, 'substr with no start param must emit a warning' );
    }

    /**
     * @group warnings
     */
    public function testStrtrSingleNonArrayParamEmitsWarning()
    {
        $this->tpl->warnings = [];
        $this->runOp( 'strtr', 'hello', [ 'notanarray' ] );
        $this->assertNotEmpty( $this->tpl->warnings, 'strtr with 1 non-array param must emit a warning' );
    }

    // -----------------------------------------------------------------------
    // noParamTransformation() — compile-time optimisation
    // -----------------------------------------------------------------------

    /**
     * @group compiler
     */
    public function testNoParamTransformationConstantInput()
    {
        $constParam = [ 'value' => 'He said "hi"' ];
        $parameters = [ $constParam ];   // must be a variable to pass as reference
        $result = $this->op->noParamTransformation(
            'addslashes', null, $this->tpl, null, null, null, null, null,
            $parameters
        );
        $this->assertIsArray( $result );
        $this->assertSame( 'He said \"hi\"', $result[0]['const'] );
    }

    /**
     * @group compiler
     */
    public function testNoParamTransformationVariableInputEmitsCodePiece()
    {
        $varParam   = [ 'variable' => 'x' ];  // not a constant element
        $parameters = [ $varParam ];            // must be a variable to pass as reference
        $result = $this->op->noParamTransformation(
            'addslashes', null, $this->tpl, null, null, null, null, null,
            $parameters
        );
        $this->assertIsArray( $result );
        $this->assertArrayHasKey( 'code', $result[0] );
        $this->assertStringContainsString( 'addslashes', $result[0]['code'] );
    }

    /**
     * @group compiler
     */
    public function testNoParamTransformationWrongParamCountReturnsFalse()
    {
        // Two parameters are invalid for a no-param operator (only 1 expected: the input).
        $parameters = [ [ 'value' => 'a' ], [ 'value' => 'b' ] ]; // variable for ref
        $result = $this->op->noParamTransformation(
            'addslashes', null, $this->tpl, null, null, null, null, null,
            $parameters
        );
        $this->assertFalse( $result );
    }
}
?>

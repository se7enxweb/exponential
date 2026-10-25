<?php
/**
 * ezjscAjaxContent never encodes a JSON response as an empty string.
 *
 * json_encode() without flags returns false for a string that is not valid UTF-8, and ezjscore/call echoed that
 * false: a 200 with an empty body. Now invalid bytes become U+FFFD, and whatever still cannot be encoded (INF,
 * NAN) becomes the error object ezjscore/call uses for every other failure.
 *
 *  JSON-01  autoEncode() and nodeEncode() with invalid UTF-8: valid JSON, the bytes as U+FFFD, the rest unchanged
 *  JSON-02  a value json_encode() cannot encode at all: the error object, never false or ''
 *  JSON-03  ezjscore/call with a server function returning invalid UTF-8: a decodable response with its content
 *  JSON-04  valid values are encoded exactly as before (json_encode() without flags)
 *  JSON-05  the json_encode template operator with invalid UTF-8: valid JSON, never an empty value in the page
 *  JSON-06  the ajax uploader's own JSON answers (the error with the exception message, the location form) use
 *           jsonEncode(), so a file name that is not UTF-8 does not empty them
 *
 * The server function is a stand-in, so no kernel, database or siteaccess is needed.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscAjaxContentJsonTest.php
 *
 * @copyright Copyright CJW Network and Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$ezjscoreRoot = dirname( __DIR__, 4 ) . '/extension/ezjscore';
require_once $ezjscoreRoot . '/classes/ezjscserverrouter.php';
require_once $ezjscoreRoot . '/classes/ezjscajaxcontent.php';
require_once $ezjscoreRoot . '/classes/runnable/views/ezjscore/call.php';
require_once $ezjscoreRoot . '/autoloads/ezjscencodingtemplatefunctions.php';
unset( $ezjscoreRoot );

/**
 * A server function router that returns a fixed value.
 */
class ezjscAjaxContentJsonTestRouter extends ezjscServerRouter
{
    public $result;

    public function __construct( $result )
    {
        $this->result = $result;
    }

    public function call( &$environmentArguments = array(), $isPackeStage = false )
    {
        return $this->result;
    }
}

class ezjscAjaxContentJsonTest extends PHPUnit\Framework\TestCase
{
    const BROKEN = "Caf\xE9 \xC3"; // Latin-1 é and a truncated two-byte character

    /**
     * Runs $callable and puts back the PHP error handler found before: logging an error the first time sets
     * eZDebug up, which resets it (see ezjscoreCallThrowableTest).
     */
    private static function keepingTheErrorHandler( callable $callable )
    {
        $probe = function() { return false; };
        $handler = set_error_handler( $probe );
        restore_error_handler();
        $result = $callable();
        $current = set_error_handler( $probe );
        restore_error_handler();
        if ( $current !== $handler && $handler !== null )
            set_error_handler( $handler );
        return $result;
    }

    /** JSON-01 */
    public function testInvalidUTF8IsReplacedNotAnEmptyBody()
    {
        $this->assertFalse( json_encode( array( 'title' => self::BROKEN ) ), 'the old call gave false' );
        $json = ezjscAjaxContent::autoEncode( array( 'title' => self::BROKEN, 'id' => 7 ), 'json' );
        $this->assertIsString( $json );
        $this->assertSame( array( 'title' => "Caf\u{FFFD} \u{FFFD}", 'id' => 7 ), json_decode( $json, true ) );

        $json = ezjscAjaxContent::nodeEncode( array( array( 'name' => self::BROKEN ) ), array(), 'json' );
        $this->assertIsString( $json );
        $this->assertNotSame( '', $json );
        $this->assertNotNull( json_decode( $json, true ), $json );
    }

    /** JSON-02 */
    public function testWhatCannotBeEncodedIsTheErrorObject()
    {
        foreach ( array( INF, NAN, array( 'x' => -INF ) ) as $value )
        {
            $json = self::keepingTheErrorHandler( function () use ( $value ) { return ezjscAjaxContent::autoEncode( $value, 'json' ); } );
            $this->assertSame( array( 'error_text' => 'The response could not be encoded as JSON', 'content' => '' ), json_decode( $json, true ), var_export( $value, true ) );
            $this->assertSame( $json, ezjscAjaxContent::jsonEncode( $value ) );
        }
    }

    /** JSON-03 */
    public function testTheCallViewAnswersWithContent()
    {
        $responses = self::keepingTheErrorHandler( function () {
            return expModuleViewEzjscoreCallMultipleServerCalls( array( new ezjscAjaxContentJsonTestRouter( array( 'name' => self::BROKEN ) ) ), 'json' );
        } );
        $this->assertCount( 1, $responses );
        $response = json_decode( $responses[0], true );
        $this->assertIsArray( $response, var_export( $responses[0], true ) );
        $this->assertSame( '', $response['error_text'] );
        $this->assertSame( array( 'name' => "Caf\u{FFFD} \u{FFFD}" ), $response['content'] );
    }

    /** JSON-04 */
    public function testValidValuesAreUnchanged()
    {
        foreach ( array( array( 'a' => 'ä/ö "x"', 'b' => array( 1, 2.5, true, null ) ), 'text', 0, array(), '<b>&</b>' ) as $value )
            $this->assertSame( json_encode( $value ), ezjscAjaxContent::autoEncode( $value, 'json' ), var_export( $value, true ) );
    }

    /** JSON-05 */
    public function testTheTemplateOperatorAnswersWithValidJSON()
    {
        $operators = new ezjscEncodingTemplateFunctions();
        $value = null;
        $operators->modify( null, 'json_encode', array(), '', '', $value, array( 'hash' => array( 'title' => self::BROKEN ) ) );
        $this->assertIsString( $value );
        $this->assertSame( array( 'title' => "Caf\u{FFFD} \u{FFFD}" ), json_decode( $value, true ) );

        $value = null;
        $operators->modify( null, 'json_encode', array(), '', '', $value, array( 'hash' => array( 'a' => 'ä "x"', 'n' => 3 ) ) );
        $this->assertSame( json_encode( array( 'a' => 'ä "x"', 'n' => 3 ) ), $value, 'valid values as before' );
    }

    /** JSON-06 */
    public function testTheUploaderAnswersThroughJsonEncode()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezjscore/classes/ezjscserverfunctionsajaxuploader.php' );
        $this->assertSame( 2, substr_count( $source, 'return ezjscAjaxContent::jsonEncode(' ) );
        $this->assertStringNotContainsString( 'return json_encode(', $source );
    }
}

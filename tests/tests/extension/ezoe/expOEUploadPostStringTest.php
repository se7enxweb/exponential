<?php
/**
 * The text fields of the rich text editor's upload form (uploadUrl, location, objectName and the
 * ContentObjectAttribute_* fields) are read through Upload::postString(): a field sent as an array (name[]) is an
 * empty text, not a TypeError that answers HTTP 500.
 *
 *  PS-01 - A text, a number and a missing field
 *  PS-02 - An array is an empty text
 *  PS-03 - The view reads every text field through postString()
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/dialog.php';
require_once dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/upload.php';

use Exponential\View\Extension\Ezoe\Ezoe\Upload;

class expOEUploadPostStringTest extends PHPUnit\Framework\TestCase
{
    private $post;

    protected function setUp(): void
    {
        $this->post = $_POST;
    }

    protected function tearDown(): void
    {
        $_POST = $this->post;
    }

    /** PS-01 */
    public function testATextANumberAndAMissingField()
    {
        $_POST = array( 'objectName' => ' A name ', 'location' => 42 );
        $http = eZHTTPTool::instance();
        $this->assertSame( ' A name ', Upload::postString( $http, 'objectName' ) );
        $this->assertSame( '42', Upload::postString( $http, 'location' ) );
        $this->assertSame( '', Upload::postString( $http, 'uploadUrl' ) );
    }

    /** PS-02 */
    public function testAnArrayIsAnEmptyText()
    {
        $_POST = array( 'objectName' => array( 'x' ), 'ContentObjectAttribute_image' => array( 'a' => 'b' ) );
        $http = eZHTTPTool::instance();
        $this->assertSame( '', Upload::postString( $http, 'objectName' ) );
        $this->assertSame( '', Upload::postString( $http, 'ContentObjectAttribute_image' ) );
    }

    /** PS-03 */
    public function testTheViewReadsEveryTextFieldThroughPostString()
    {
        $source = file_get_contents( dirname( __DIR__, 4 ) . '/extension/ezoe/classes/runnable/views/ezoe/upload.php' );
        $this->assertSame( 1, preg_match_all( '/->postVariable\(/', $source ), 'only postString() itself reads a post variable' );
        foreach ( array( "'uploadUrl'", "'location'", "'objectName'", '$base' ) as $field )
            $this->assertStringContainsString( 'self::postString( $http, ' . $field . ' )', $source, $field );
    }
}

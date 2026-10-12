<?php
/**
 * PHP 8 regression cases of the datatypes, from the PHP 7.2-8.3 fixes LoveStack (mugoweb/ezpublish-legacy)
 * collected in 2024 and 2025: each case is the input a LoveStack fix guarded against, and the datatype must deal
 * with it without a TypeError or a PHP notice.
 *
 * LoveStack commits reproduced here: b11e85ca5a and fd839afd28 (ezmatrix with data_text NULL or ''), a5f16bd9f7
 * (ezmatrix validation of a posted value that is not an array), 1bd250d4c6 (ezdatetime fields posted as strings,
 * empty ones included; an ezauthor row without an e-mail address).
 *
 * No database: the attribute is an unsaved eZContentObjectAttribute whose required flag the test sets.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 * @group datatypes
 * @group php8
 */

class eZDatatypePHP8RegressionTestAttribute extends eZContentObjectAttribute
{
    public $required = false;

    public function validateIsRequired()
    {
        return $this->required;
    }
}

class eZDatatypePHP8RegressionTest extends PHPUnit\Framework\TestCase
{
    private $savedPost;
    private $notices = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->savedPost = $_POST;
        $this->notices = array();
        $notices =& $this->notices;
        set_error_handler( function ( $no, $str, $f, $l ) use ( &$notices )
        {
            $notices[] = $str . ' (' . basename( $f ) . ':' . $l . ')';
            return true;
        } );
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        $_POST = $this->savedPost;
    }

    private function attribute( array $row = array() )
    {
        return new eZDatatypePHP8RegressionTestAttribute( $row + array( 'id' => 4711, 'version' => 1,
                                                                        'data_type_string' => 'ezstring' ) );
    }

    private function assertNoNotices()
    {
        $this->assertSame( array(), $this->notices, 'PHP notices' );
    }

    /** fd839afd28: data_text NULL (a class attribute added to a class that has objects) reads as '' */
    public function testDataTextNullReadsAsEmptyString()
    {
        $attribute = $this->attribute( array( 'data_text' => null ) );
        $this->assertSame( '', $attribute->attribute( 'data_text' ) );
        $this->assertNoNotices();
    }

    /** b11e85ca5a: a matrix decoded from NULL or '' is an empty matrix, without the PHP 8 DOMDocument error */
    public function testMatrixDecodeOfNullOrEmpty()
    {
        foreach ( array( null, '', ' ' ) as $xml )
        {
            $matrix = new eZMatrix( '' );
            $matrix->decodeXML( $xml );
            $this->assertSame( array(), $matrix->attribute( 'rows' )['sequential'] ?? array(), var_export( $xml, true ) );
        }
        $this->assertNoNotices();
    }

    /** a5f16bd9f7: the cells posted for a matrix are not an array (a string, or missing) */
    public function testMatrixValidationOfANonArray()
    {
        $type = new eZMatrixType();
        foreach ( array( 'text', '' ) as $posted )
        {
            $_POST = array( 'ContentObjectAttribute_ezmatrix_cell_4711' => $posted );
            $attribute = $this->attribute();
            $attribute->required = true;
            $this->assertSame( eZInputValidator::STATE_INVALID,
                               $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attribute ) );
        }
        $_POST = array();
        $attribute = $this->attribute();
        $this->assertSame( eZInputValidator::STATE_ACCEPTED,
                           $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attribute ) );
        $this->assertNoNotices();
    }

    /** 1bd250d4c6: date and time fields arrive as strings; an empty time is midnight, not a TypeError */
    public function testDateTimeFieldsPostedAsStrings()
    {
        $type = new eZDateTimeType();
        $stamp = $type->dateTimeInputStamp( '2026', '10', '12', '', '', '', false );
        $this->assertSame( mktime( 0, 0, 0, 10, 12, 2026 ), $stamp );
        $this->assertSame( mktime( 9, 5, 0, 10, 12, 2026 ), $type->dateTimeInputStamp( '2026', '10', '12', '9', '05', '', false ) );
        $this->assertNull( $type->dateTimeInputStamp( '2026', '13', '40', '', '', '', false ) );
        $this->assertNull( $type->dateTimeInputStamp( 'abc', '10', '12', '', '', '', false ) );

        $dateTime = new eZDateTime();
        $dateTime->setMDYHMS( '10', '12', '2026', '9', '5', '0' );
        $this->assertSame( mktime( 9, 5, 0, 10, 12, 2026 ), $dateTime->timeStamp() );
        $this->assertNoNotices();
    }

    /**
     * 1bd250d4c6 (ezauthor): an author row whose e-mail field is empty. Exponential refuses it, as every
     * release before did ("The email address is not valid."); LoveStack accepts it. This test records the
     * behaviour (refused, without a PHP notice); accepting an empty address is a behaviour change, not a PHP 8 fix.
     */
    public function testAuthorWithoutEmailAddress()
    {
        $type = new eZAuthorType();
        $_POST = array( 'ContentObjectAttribute_data_author_id_4711' => array( '0' ),
                        'ContentObjectAttribute_data_author_name_4711' => array( 'An Author' ),
                        'ContentObjectAttribute_data_author_email_4711' => array( '' ) );
        $attribute = $this->attribute();
        $this->assertSame( eZInputValidator::STATE_INVALID,
                           $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attribute ) );

        $_POST['ContentObjectAttribute_data_author_email_4711'] = array( 'author@example.com' );
        $attribute = $this->attribute();
        $this->assertSame( eZInputValidator::STATE_ACCEPTED,
                           $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attribute ) );

        // a missing e-mail field (null on PHP 8) and an empty author list
        unset( $_POST['ContentObjectAttribute_data_author_email_4711'] );
        $attribute = $this->attribute();
        $this->assertSame( eZInputValidator::STATE_INVALID,
                           $type->validateObjectAttributeHTTPInput( eZHTTPTool::instance(), 'ContentObjectAttribute', $attribute ) );
        $this->assertSame( 0, eZMail::validate( null ) );
        $this->assertNoNotices();
    }
}

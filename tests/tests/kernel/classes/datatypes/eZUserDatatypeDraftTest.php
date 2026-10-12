<?php
/**
 * eZUserType with an account that exists only as a draft: an object of a user class whose ezuser row is not
 * stored yet, with the account details of an edit in data_text. content() shows the draft on a new account
 * (login and e-mail address stay in the edit form), without a draft it stays null; storing the first version
 * and publishing such a draft create the account together with its ezuser_setting row, and an existing setting
 * row (of a stored account, or a disabled one) is left alone.
 *
 * No database: eZUserDatatypeDraftTestDB answers the selects on ezuser and ezuser_setting from rows the test
 * gives it and records what is written.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZUserDatatypeDraftTestDB extends eZDBInterface
{
    /** @var array table => rows returned for a select on it */
    public $rows = array( 'ezuser' => array(), 'ezuser_setting' => array() );

    /** @var string[] the statements that write */
    public $writes = array();

    public function __construct()
    {
        $this->DB = 'k1-user-draft';
    }

    function query( $sql, $server = false )
    {
        if ( preg_match( '/^\s*(INSERT|UPDATE|DELETE)\b/i', $sql ) )
            $this->writes[] = preg_replace( '/\s+/', ' ', trim( $sql ) );
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        if ( preg_match( '/\bFROM\s+(ezuser_setting|ezuser)\b/i', $sql, $m ) )
            return $this->rows[strtolower( $m[1] )];
        return array();
    }

    function isConnected()
    {
        return true;
    }

    function escapeString( $str )
    {
        return addslashes( (string)$str );
    }

    function databaseName()
    {
        return 'mysql';
    }

    function lastSerialID( $table = false, $column = false )
    {
        return 1;
    }

    /** @return string[] the INSERTs into the table */
    public function inserts( $table )
    {
        return array_values( array_filter( $this->writes, function ( $sql ) use ( $table ) {
            return preg_match( '/^INSERT INTO ' . $table . '\b/i', $sql );
        } ) );
    }
}

class eZUserDatatypeDraftTest extends eZDatatypeTestCase
{
    /** @var eZUserDatatypeDraftTestDB */
    private $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new eZUserDatatypeDraftTestDB();
        eZDB::setInstance( $this->db );
        unset( $GLOBALS['eZUserObject_812'] );
    }

    protected function tearDown(): void
    {
        unset( $GLOBALS['eZUserObject_812'] );
        parent::tearDown();
    }

    /**
     * The draft an edit leaves in data_text.
     */
    private function draft( $login, $email )
    {
        $user = eZUser::create( 812 );
        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $email );
        $user->setAttribute( 'password_hash', 'k1hash' );
        $user->setAttribute( 'password_hash_type', 7 );
        return eZUserType::serializeDraft( $user );
    }

    private function userAttribute( $dataText, $version = 2 )
    {
        return $this->objectAttribute( 'ezuser', array(), array( 'data_text' => $dataText, 'version' => $version ) );
    }

    public function testDraftOfAnAccountThatIsNotStoredYetIsShown()
    {
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ) );
        $user = $this->dataType( 'ezuser' )->objectAttributeContent( $attribute );
        $this->assertInstanceOf( 'eZUser', $user );
        $this->assertSame( 'k1new', $user->attribute( 'login' ) );
        $this->assertSame( 'k1new@example.org', $user->attribute( 'email' ) );
        $this->assertSame( 812, (int)$user->attribute( 'contentobject_id' ) );
        $this->assertSame( 'k1new', $this->dataType( 'ezuser' )->title( $attribute ) );
        $this->assertSame( array(), $this->db->writes, 'showing the draft writes nothing' );
    }

    public function testWithoutAValidDraftThereIsNoAccount()
    {
        $type = $this->dataType( 'ezuser' );
        $this->assertNull( $type->objectAttributeContent( $this->userAttribute( '' ) ) );
        $this->assertNull( $type->objectAttributeContent( $this->userAttribute( 'not a draft' ) ) );
        $this->assertSame( '', $type->title( $this->userAttribute( '' ) ) );
    }

    public function testDraftIsAppliedToAStoredAccount()
    {
        $this->db->rows['ezuser'] = array( array( 'contentobject_id' => 812, 'login' => 'k1old', 'email' => 'k1old@example.org',
                                                  'password_hash' => 'x', 'password_hash_type' => 7 ) );
        $user = $this->dataType( 'ezuser' )->objectAttributeContent( $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ) ) );
        $this->assertSame( 'k1new', $user->attribute( 'login' ) );
        $this->assertSame( 'k1new@example.org', $user->attribute( 'email' ) );
    }

    public function testStoringTheFirstVersionOfADraftAccountCreatesItsSetting()
    {
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ), 1 );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $attribute );
        $this->assertCount( 1, $this->db->inserts( 'ezuser' ), implode( "\n", $this->db->writes ) );
        $settings = $this->db->inserts( 'ezuser_setting' );
        $this->assertCount( 1, $settings, implode( "\n", $this->db->writes ) );
        $this->assertStringContainsString( '(user_id, is_enabled, max_login) VALUES(812, 1, 0)', $settings[0], 'enabled, for the account' );
    }

    public function testStoringAnAccountThatExistsKeepsItsSetting()
    {
        $this->db->rows['ezuser'] = array( array( 'contentobject_id' => 812, 'login' => 'k1old', 'email' => 'k1old@example.org',
                                                  'password_hash' => 'x', 'password_hash_type' => 7 ) );
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ), 1 );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $attribute );
        $this->assertSame( array(), $this->db->inserts( 'ezuser_setting' ) );
    }

    public function testPublishingADraftAccountCreatesTheAccountAndItsSetting()
    {
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $users = $this->db->inserts( 'ezuser' );
        $this->assertCount( 1, $users, implode( "\n", $this->db->writes ) );
        $this->assertStringContainsString( "'k1new'", $users[0] );
        $this->assertCount( 1, $this->db->inserts( 'ezuser_setting' ), implode( "\n", $this->db->writes ) );
        $this->assertSame( '', $attribute->attribute( 'data_text' ), 'the draft is cleared' );
    }

    public function testPublishingADraftOfAStoredAccountKeepsItsSetting()
    {
        $this->db->rows['ezuser'] = array( array( 'contentobject_id' => 812, 'login' => 'k1old', 'email' => 'k1new@example.org',
                                                  'password_hash' => 'x', 'password_hash_type' => 7 ) );
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $this->assertSame( array(), $this->db->inserts( 'ezuser_setting' ) );
    }

    public function testNewAccountKeepsAnExistingSetting()
    {
        // the default account of the object's creation stored the setting, but no ezuser row (it had no login)
        $this->db->rows['ezuser_setting'] = array( array( 'user_id' => 812, 'is_enabled' => 0 ) );
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ), 1 );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $attribute );
        $this->assertCount( 1, $this->db->inserts( 'ezuser' ) );
        $this->assertSame( array(), $this->db->inserts( 'ezuser_setting' ), 'a disabled account stays disabled' );
    }
}

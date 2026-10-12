<?php
/**
 * eZUserType: what the draft of an account (the login, e-mail address and password hash an edit keeps in
 * data_text) can and cannot do, next to eZUserDatatypeDraftTest.
 *
 *  - A setting row that exists is never changed: an account an administrator disabled stays disabled when its
 *    draft is published or its object is stored again, and so does one waiting for its activation (user/register
 *    with [UserSettings] VerifyUserType). A new setting row is enabled, or disabled when the account has an
 *    account key (pending activation). The draft has no is_enabled; one that names it changes nothing.
 *  - A draft becomes a new account only with a login and e-mail address no other account has (the checks of the
 *    edit form), also when it is stored as the first version (a copy of another user's object).
 *  - A copy of a user object into a new object does not take the draft along; a new version of the same does.
 *  - Anything in data_text that serializeDraft() did not write is no draft: no account, no TypeError, no write.
 *    The hash type is one eZUser knows, and a plaintext "hash" counts only on a site with HashType=plaintext.
 *
 * No database: eZUserDatatypeDraftSecurityTestDB answers the selects on ezuser, ezuser_setting and
 * ezuser_accountkey from the rows the test gives it, filtered by the WHERE of the select, and records the writes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

require_once __DIR__ . '/eZDatatypeTestFixtures.php';

class eZUserDatatypeDraftSecurityTestDB extends eZDBInterface
{
    /** @var array table => rows */
    public $rows = array( 'ezuser' => array(), 'ezuser_setting' => array(), 'ezuser_accountkey' => array() );

    /** @var string[] the statements that write */
    public $writes = array();

    public function __construct()
    {
        $this->DB = 'k1-user-draft-security';
    }

    function query( $sql, $server = false )
    {
        if ( preg_match( '/^\s*(INSERT|UPDATE|DELETE)\b/i', $sql ) )
            $this->writes[] = preg_replace( '/\s+/', ' ', trim( $sql ) );
        return true;
    }

    function arrayQuery( $sql, $params = array(), $server = false )
    {
        if ( !preg_match( '/\bFROM\s+(ezuser_accountkey|ezuser_setting|ezuser)\b/i', $sql, $m ) )
            return array();
        $rows = $this->rows[strtolower( $m[1] )];
        $where = preg_match( '/\bWHERE\b(.*)$/is', $sql, $w ) ? $w[1] : '';
        $filters = array(
            'contentobject_id' => '/\bcontentobject_id\s*=\s*\'?(\d+)\'?/i',
            'user_id' => '/\buser_id\s*=\s*\'?(\d+)\'?/i',
            'login' => '/LOWER\(\s*login\s*\)\s*=\s*\'([^\']*)\'/i',
            'email' => '/LOWER\(\s*email\s*\)\s*=\s*\'([^\']*)\'/i',
        );
        foreach ( $filters as $field => $pattern )
        {
            if ( !preg_match( $pattern, $where, $f ) )
                continue;
            $rows = array_values( array_filter( $rows, function ( $row ) use ( $field, $f ) {
                return isset( $row[$field] ) && strtolower( (string)$row[$field] ) === strtolower( $f[1] );
            } ) );
        }
        return $rows;
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

    /** @return string[] the writes (INSERT, UPDATE, DELETE) of the table */
    public function writesOf( $table )
    {
        return array_values( array_filter( $this->writes, function ( $sql ) use ( $table ) {
            return preg_match( '/^(INSERT INTO|UPDATE|DELETE FROM) ' . $table . '\b/i', $sql );
        } ) );
    }
}

class eZUserDatatypeDraftSecurityTest extends eZDatatypeTestCase
{
    /** @var eZUserDatatypeDraftSecurityTestDB */
    private $db;

    /** @var array [group, name, value or null when it was not set] */
    private $savedIni = array();

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new eZUserDatatypeDraftSecurityTestDB();
        eZDB::setInstance( $this->db );
        unset( $GLOBALS['eZUserObject_812'] );
        $this->setIni( 'UserSettings', 'HashType', 'php_default' );
        $this->setIni( 'UserSettings', 'RequireUniqueEmail', 'false' );
        $this->setIni( 'UserSettings', 'AuthenticateMatch', 'login;email' );
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance();
        foreach ( array_reverse( $this->savedIni ) as $saved )
        {
            if ( $saved[2] === null )
                $ini->removeSetting( $saved[0], $saved[1] );
            else
                $ini->setVariable( $saved[0], $saved[1], $saved[2][0] );
        }
        $this->savedIni = array();
        unset( $GLOBALS['eZUserObject_812'] );
        parent::tearDown();
    }

    private function setIni( $group, $name, $value )
    {
        $ini = eZINI::instance();
        $this->savedIni[] = array( $group, $name, $ini->hasVariable( $group, $name ) ? array( $ini->variable( $group, $name ) ) : null );
        $ini->setVariable( $group, $name, $value );
    }

    private function draft( $login, $email, $hashType = 7, $hash = 'k1hash' )
    {
        $user = eZUser::create( 812 );
        $user->setAttribute( 'login', $login );
        $user->setAttribute( 'email', $email );
        $user->setAttribute( 'password_hash', $hash );
        $user->setAttribute( 'password_hash_type', $hashType );
        return eZUserType::serializeDraft( $user );
    }

    private function userAttribute( $dataText, $version = 2, $objectID = 812 )
    {
        return $this->objectAttribute( 'ezuser', array(), array( 'data_text' => $dataText, 'version' => $version,
                                                                 'contentobject_id' => $objectID ) );
    }

    private function account( $id, $login, $email )
    {
        return array( 'contentobject_id' => $id, 'login' => $login, 'email' => $email,
                      'password_hash' => 'x', 'password_hash_type' => 7 );
    }

    private function writes()
    {
        return implode( "\n", $this->db->writes );
    }

    // The setting row

    public function testPublishingTheDraftOfANewAccountKeepsItsDisabledSetting()
    {
        $this->db->rows['ezuser_setting'] = array( array( 'user_id' => 812, 'is_enabled' => 0, 'max_login' => 0 ) );
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $this->assertCount( 1, $this->db->writesOf( 'ezuser' ), $this->writes() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_setting' ), 'a disabled account stays disabled' );
    }

    public function testADraftThatNamesIsEnabledChangesNoSetting()
    {
        $this->db->rows['ezuser'] = array( $this->account( 812, 'k1old', 'k1old@example.org' ) );
        $this->db->rows['ezuser_setting'] = array( array( 'user_id' => 812, 'is_enabled' => 0, 'max_login' => 0 ) );
        $draft = json_decode( $this->draft( 'k1old', 'k1old@example.org' ), true );
        $draft['is_enabled'] = 1;
        $draft['max_login'] = 99;
        $attribute = $this->userAttribute( json_encode( $draft ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $this->userAttribute( json_encode( $draft ), 1 ) );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_setting' ), $this->writes() );
    }

    public function testStoringAnObjectWithoutAccountKeepsItsDisabledSetting()
    {
        // the default account branch stored a new, enabled setting row over the existing one
        $this->db->rows['ezuser_setting'] = array( array( 'user_id' => 812, 'is_enabled' => 0, 'max_login' => 3 ) );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $this->userAttribute( '', 2 ) );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_setting' ), $this->writes() );
    }

    public function testTheDefaultAccountOfANewObjectGetsAnEnabledSetting()
    {
        $this->dataType( 'ezuser' )->storeObjectAttribute( $this->userAttribute( '', 1 ) );
        $settings = $this->db->writesOf( 'ezuser_setting' );
        $this->assertCount( 1, $settings, $this->writes() );
        $this->assertStringContainsString( 'VALUES(812, 1, 0)', $settings[0] );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser' ), 'an account without a login is not stored' );
    }

    public function testAnAccountWaitingForItsActivationGetsADisabledSetting()
    {
        $this->db->rows['ezuser_accountkey'] = array( array( 'id' => 1, 'user_id' => 812, 'hash_key' => 'k1', 'time' => 1 ) );
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $settings = $this->db->writesOf( 'ezuser_setting' );
        $this->assertCount( 1, $settings, $this->writes() );
        $this->assertStringContainsString( 'VALUES(812, 0, 0)', $settings[0], 'pending activation: disabled' );
    }

    public function testTheDefaultAccountOfAnObjectWaitingForActivationIsDisabled()
    {
        $this->db->rows['ezuser_accountkey'] = array( array( 'id' => 1, 'user_id' => 812, 'hash_key' => 'k1', 'time' => 1 ) );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $this->userAttribute( '', 1 ) );
        $settings = $this->db->writesOf( 'ezuser_setting' );
        $this->assertCount( 1, $settings, $this->writes() );
        $this->assertStringContainsString( 'VALUES(812, 0, 0)', $settings[0] );
    }

    public function testARegistrationWaitingForVerificationStaysDisabledUntilActivated()
    {
        // user/register with VerifyUserType=email: the account is stored, sendActivationEmail() disabled its
        // setting and stored the account key, the object waits unpublished. Storing the first version again
        // (the form sent once more) and publishing its draft leave the setting alone; only user/activate
        // (eZUserOperationCollection::activation()) enables it.
        $this->db->rows['ezuser'] = array( $this->account( 812, 'k1reg', 'k1reg@example.org' ) );
        $this->db->rows['ezuser_setting'] = array( array( 'user_id' => 812, 'is_enabled' => 0, 'max_login' => 0 ) );
        $this->db->rows['ezuser_accountkey'] = array( array( 'id' => 1, 'user_id' => 812, 'hash_key' => 'k1', 'time' => 1 ) );
        $draft = $this->draft( 'k1reg', 'k1reg@example.org' );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $this->userAttribute( $draft, 1 ) );
        $this->dataType( 'ezuser' )->onPublish( $this->userAttribute( $draft, 1 ), null, array() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_setting' ), $this->writes() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_accountkey' ), $this->writes() );
    }

    // Login and e-mail address of a new account

    public function testPublishingADraftWithATakenLoginCreatesNoAccount()
    {
        $this->db->rows['ezuser'] = array( $this->account( 900, 'K1Taken', 'k1other@example.org' ) );
        $attribute = $this->userAttribute( $this->draft( 'k1taken', 'k1new@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser' ), $this->writes() );
        $this->assertSame( '', $attribute->attribute( 'data_text' ), 'the draft is cleared' );
        // the attribute, stored again without its draft, is an object without account: it gets the default
        // setting row every new user object gets, and an account without a login, which is not stored
        $this->assertSame( '', (string)$attribute->content()->attribute( 'login' ), 'the account in hand has no login' );
    }

    public function testStoringAFirstVersionWithATakenLoginCreatesNoAccount()
    {
        // a copy of user 900's object, whose attribute held 900's draft (user/password writes one)
        $this->db->rows['ezuser'] = array( $this->account( 900, 'k1taken', 'k1taken@example.org' ) );
        $attribute = $this->userAttribute( $this->draft( 'k1taken', 'k1taken@example.org' ), 1 );
        $this->dataType( 'ezuser' )->storeObjectAttribute( $attribute );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser' ), $this->writes() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_setting' ), $this->writes() );
        $this->assertSame( '', $attribute->attribute( 'data_text' ) );
    }

    public function testATakenEmailAddressStopsTheNewAccountWhereAddressesAreUnique()
    {
        $this->setIni( 'UserSettings', 'RequireUniqueEmail', 'true' );
        $this->db->rows['ezuser'] = array( $this->account( 900, 'k1other', 'K1Shared@example.org' ) );
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1shared@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser' ), $this->writes() );
        $this->assertSame( '', $attribute->attribute( 'data_text' ) );
    }

    public function testATakenEmailAddressIsAllowedWhereAddressesNeedNotBeUnique()
    {
        $this->db->rows['ezuser'] = array( $this->account( 900, 'k1other', 'k1shared@example.org' ) );
        $attribute = $this->userAttribute( $this->draft( 'k1new', 'k1shared@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $this->assertCount( 1, $this->db->writesOf( 'ezuser' ), $this->writes() );
        $this->assertCount( 1, $this->db->writesOf( 'ezuser_setting' ), $this->writes() );
    }

    public function testTheAccountsOwnLoginIsNoConflict()
    {
        $this->db->rows['ezuser'] = array( $this->account( 812, 'k1own', 'k1own@example.org' ) );
        $attribute = $this->userAttribute( $this->draft( 'k1own', 'k1own@example.org' ) );
        $this->dataType( 'ezuser' )->onPublish( $attribute, null, array() );
        $this->assertCount( 1, $this->db->writesOf( 'ezuser' ), $this->writes() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_setting' ) );
    }

    // Copies

    public function testACopyIntoAnotherObjectDropsTheDraft()
    {
        $original = $this->userAttribute( $this->draft( 'k1orig', 'k1orig@example.org' ), 3, 900 );
        $copy = $this->userAttribute( $original->attribute( 'data_text' ), 1, 812 );
        $this->dataType( 'ezuser' )->initializeObjectAttribute( $copy, 3, $original );
        $this->assertSame( '', $copy->attribute( 'data_text' ) );
        $this->assertNull( $this->dataType( 'ezuser' )->objectAttributeContent( $copy ) );
    }

    public function testANewVersionOfTheSameObjectKeepsTheDraft()
    {
        $draft = $this->draft( 'k1same', 'k1same@example.org' );
        $previous = $this->userAttribute( $draft, 2 );
        $next = $this->userAttribute( $draft, 3 );
        $this->dataType( 'ezuser' )->initializeObjectAttribute( $next, 2, $previous );
        $this->assertSame( $draft, $next->attribute( 'data_text' ) );
    }

    // What is no draft

    public static function notADraft()
    {
        $valid = array( 'login' => 'k1new', 'password_hash' => 'k1hash', 'email' => 'k1new@example.org', 'password_hash_type' => 7 );
        $with = function ( array $change ) use ( $valid ) {
            return json_encode( array_merge( $valid, $change ) );
        };
        $without = function ( $field ) use ( $valid ) {
            unset( $valid[$field] );
            return json_encode( $valid );
        };
        return array(
            'invalid JSON' => array( '{"login":' ),
            'JSON null' => array( 'null' ),
            'JSON number' => array( '42' ),
            'JSON string' => array( '"k1new"' ),
            'JSON true' => array( 'true' ),
            'JSON list' => array( '["k1new","k1hash","k1new@example.org",7]' ),
            'empty object' => array( '{}' ),
            'no login' => array( $without( 'login' ) ),
            'no hash type' => array( $without( 'password_hash_type' ) ),
            'login is a list' => array( $with( array( 'login' => array( 'k1new' ) ) ) ),
            'login is an object' => array( $with( array( 'login' => array( 'a' => 'k1new' ) ) ) ),
            'login is true' => array( $with( array( 'login' => true ) ) ),
            'empty login' => array( $with( array( 'login' => '' ) ) ),
            'blank login' => array( $with( array( 'login' => '   ' ) ) ),
            'null login' => array( $with( array( 'login' => null ) ) ),
            'hash is false' => array( $with( array( 'password_hash' => false ) ) ),
            'unknown hash type' => array( $with( array( 'password_hash_type' => 99 ) ) ),
            'negative hash type' => array( $with( array( 'password_hash_type' => -1 ) ) ),
            'fractional hash type' => array( $with( array( 'password_hash_type' => 7.5 ) ) ),
            'hash type name' => array( $with( array( 'password_hash_type' => 'bcrypt' ) ) ),
            'plaintext hash on a hashing site' => array( $with( array( 'password_hash' => 'secret', 'password_hash_type' => 5 ) ) ),
            'serialized PHP' => array( serialize( $valid ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'notADraft' )]
    public function testWhatIsNoDraftMakesNoAccountAndWritesNothing( $dataText )
    {
        $type = $this->dataType( 'ezuser' );
        $this->assertNull( $type->objectAttributeContent( $this->userAttribute( $dataText ) ) );
        $this->assertSame( '', $type->title( $this->userAttribute( $dataText ) ) );
        $type->onPublish( $this->userAttribute( $dataText ), null, array() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser' ), $this->writes() );
        $this->assertSame( array(), $this->db->writesOf( 'ezuser_setting' ), $this->writes() );
    }

    public function testAMalformedDraftLeavesAStoredAccountAsItIs()
    {
        $this->db->rows['ezuser'] = array( $this->account( 812, 'k1old', 'k1old@example.org' ) );
        $user = $this->dataType( 'ezuser' )->objectAttributeContent(
            $this->userAttribute( json_encode( array( 'login' => 'k1new', 'password_hash' => 'secret', 'email' => 'k1new@example.org',
                                                      'password_hash_type' => 5 ) ) ) );
        $this->assertSame( 'k1old', $user->attribute( 'login' ) );
        $this->assertSame( 'x', $user->attribute( 'password_hash' ) );
        $this->assertSame( array(), $this->db->writes, 'showing an account writes nothing' );
    }

    public function testAPlaintextDraftCountsOnASiteThatStoresPlaintext()
    {
        $this->setIni( 'UserSettings', 'HashType', 'plaintext' );
        $user = $this->dataType( 'ezuser' )->objectAttributeContent( $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org', 5, 'secret' ) ) );
        $this->assertInstanceOf( 'eZUser', $user );
        $this->assertSame( 5, (int)$user->attribute( 'password_hash_type' ) );
    }

    public function testAHashTypeReadFromTheDatabaseAsAStringCounts()
    {
        $user = $this->dataType( 'ezuser' )->objectAttributeContent( $this->userAttribute( $this->draft( 'k1new', 'k1new@example.org', '6' ) ) );
        $this->assertInstanceOf( 'eZUser', $user );
        $this->assertSame( 'k1hash', $user->attribute( 'password_hash' ) );
    }

    public function testTheDraftHoldsTheHashNeverThePassword()
    {
        $user = eZUser::create( 812 );
        $user->setInformation( 812, 'k1new', 'k1new@example.org', 'K1-secret-pass', 'K1-secret-pass' );
        $draft = eZUserType::serializeDraft( $user );
        $this->assertStringNotContainsString( 'K1-secret-pass', $draft );
        $decoded = json_decode( $draft, true );
        $this->assertSame( eZUser::hashType(), (int)$decoded['password_hash_type'] );
        $this->assertTrue( eZUser::authenticateHash( 'k1new', 'K1-secret-pass', eZUser::site(), $decoded['password_hash_type'], $decoded['password_hash'] ) );
    }
}

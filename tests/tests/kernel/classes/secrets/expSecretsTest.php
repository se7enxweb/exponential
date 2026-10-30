<?php
/**
 * The kernel's secret store (expSecrets, expSecretKey, expSecret; doc/eng-us/features/6.0/secret-store.md). No
 * installation: the key file is made under var/tmp/ of this checkout, the table exp_secret is created from
 * share/db_schema.dba in a SQLite database in memory set as the global database for the test, and everything is
 * removed afterwards. The site's own database is never opened.
 *
 *  SC-01 - encrypt()/decrypt(): a round trip; the text starts with the version byte; a fresh nonce each time
 *  SC-02 - decrypt() fails (null) with another name (the name is bound in), another key, a changed text, another
 *          version byte, and text that is not base64
 *  SC-03 - expSecretKey::decode(): 32 bytes as base64, base64url, unpadded or hex; anything else null; id() is 16 hex
 *          characters, the same for the same key, another for another
 *  SC-04 - the key file is made on first use, mode 0600, with a .htaccess that denies the web; a second current()
 *          keeps it; current( false ) makes none
 *  SC-05 - source() is env while EXP_SECRET_KEY is set, and the key is that one; EXP_SECRET_KEY_PREVIOUS is the
 *          previous key; a key that is not 32 bytes is reported in info()
 *  SC-06 - prepareRotation()/commitRotation(): the new key is current, the old one is .previous, byID() finds both
 *  SC-07 - get(): env:VAR; a name that is not valid is null; validName(); mask() replaces values and their
 *          URL-encoded form, also values that are not stored
 *  SC-08 - set()/get()/names()/describe()/remove() through the table: the value is stored encrypted under the key's
 *          id, names() shows names and never values, a value is replaced, removed is gone
 *  SC-09 - rotate(): every secret encrypted again under the new key and still readable; reencryptAll() counts the
 *          ones already current; a secret of a key that is gone is unreadable and counted
 *  SC-10 - required() throws naming the secret, never a value; set() refuses env: names, empty values, bad scopes
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once dirname( __DIR__ ) . '/datatable/expDataTableTestLoader.php';

class expSecretsTest extends PHPUnit\Framework\TestCase
{
    /** @var string */
    private $dir;

    /** @var mixed */
    private $savedGlobal;

    /** @var callable|null the exception handler before the test */
    private $exceptionHandler = null;

    /** @var bool whether the global database was replaced */
    private $replacedGlobal = false;

    protected function setUp(): void
    {
        if ( !function_exists( 'sodium_crypto_aead_xchacha20poly1305_ietf_encrypt' ) )
            $this->markTestSkipped( 'The sodium extension is not loaded' );
        $this->exceptionHandler = self::exceptionHandler();
        putenv( 'EXP_SECRET_KEY' );
        putenv( 'EXP_SECRET_KEY_PREVIOUS' );
        $this->dir = expDataTableTestLoader::root() . '/var/tmp/t691-secrets-' . getmypid() . '/keys';
        expSecretKey::setFile( $this->dir . '/exp_secret.key' );
        expSecrets::reset();
    }

    /** @return callable|null the exception handler in place */
    private static function exceptionHandler()
    {
        $handler = set_exception_handler( null );
        restore_exception_handler();
        return $handler;
    }

    protected function tearDown(): void
    {
        // set() asks for the current user, whose first lookup in a process may install the kernel's exception handler
        for ( $i = 0; $i < 5 && self::exceptionHandler() !== $this->exceptionHandler; $i++ )
            restore_exception_handler();
        putenv( 'EXP_SECRET_KEY' );
        putenv( 'EXP_SECRET_KEY_PREVIOUS' );
        expSecretKey::setFile( null );
        expSecrets::reset();
        if ( $this->replacedGlobal )
        {
            if ( $this->savedGlobal === null )
                unset( $GLOBALS['eZDBGlobalInstance'] );
            else
                $GLOBALS['eZDBGlobalInstance'] = $this->savedGlobal[0];
            expKernelTableSchema::reset();
        }
        if ( $this->dir && is_dir( $this->dir ) )
        {
            foreach ( scandir( $this->dir ) as $file )
                if ( $file !== '.' && $file !== '..' )
                    unlink( $this->dir . '/' . $file );
            rmdir( $this->dir );
            rmdir( dirname( $this->dir ) );
        }
    }

    /** A SQLite database in memory with exp_secret, as the global database of the test */
    private function store()
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $this->savedGlobal = array_key_exists( 'eZDBGlobalInstance', $GLOBALS ) ? array( $GLOBALS['eZDBGlobalInstance'] ) : null;
        $this->replacedGlobal = true;
        $db = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => ':memory:',
                                      'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                      'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                      'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                      'use_persistent_connection' => false, 'show_errors' => false ) );
        eZDB::setInstance( $db );
        expKernelTableSchema::reset();
        $schema = expKernelTableSchema::read( expDataTableTestLoader::root() . '/share/db_schema.dba' );
        $messages = array();
        $this->assertTrue( expKernelTableSchema::install( array( 'exp_secret' ), $db, $schema, $messages ), implode( '; ', $messages ) );
        $this->assertTrue( expSecrets::installed( true ) );
        return $db;
    }

    /** SC-01 */
    public function testRoundTrip()
    {
        $key = random_bytes( 32 );
        $a = expSecrets::encrypt( 'p@ss:word/ü', 'db.archive', $key );
        $b = expSecrets::encrypt( 'p@ss:word/ü', 'db.archive', $key );
        $this->assertNotSame( $a, $b, 'a fresh nonce each time' );
        $this->assertSame( expSecrets::VERSION, base64_decode( $a )[0] );
        $this->assertStringNotContainsString( 'p@ss', base64_decode( $a ) );
        $this->assertSame( 'p@ss:word/ü', expSecrets::decrypt( $a, 'db.archive', $key ) );
        $this->assertSame( 'p@ss:word/ü', expSecrets::decrypt( $b, 'db.archive', $key ) );
    }

    /** SC-02 */
    public function testDecryptFailures()
    {
        $key = random_bytes( 32 );
        $text = expSecrets::encrypt( 'value', 'name', $key );
        $this->assertNull( expSecrets::decrypt( $text, 'other', $key ) );
        $this->assertNull( expSecrets::decrypt( $text, 'name', random_bytes( 32 ) ) );
        $raw = base64_decode( $text );
        $raw[strlen( $raw ) - 1] = chr( ord( $raw[strlen( $raw ) - 1] ) ^ 1 );
        $this->assertNull( expSecrets::decrypt( base64_encode( $raw ), 'name', $key ) );
        $raw = base64_decode( $text );
        $raw[0] = "\x02";
        $this->assertNull( expSecrets::decrypt( base64_encode( $raw ), 'name', $key ) );
        $this->assertNull( expSecrets::decrypt( '%%%not base64', 'name', $key ) );
        $this->assertNull( expSecrets::decrypt( base64_encode( "\x01short" ), 'name', $key ) );
        $this->assertNull( expSecrets::decrypt( '', 'name', $key ) );
    }

    /** SC-03 */
    public function testDecodeAndId()
    {
        $key = random_bytes( 32 );
        $this->assertSame( $key, expSecretKey::decode( base64_encode( $key ) ) );
        $this->assertSame( $key, expSecretKey::decode( "  " . base64_encode( $key ) . "\n" ) );
        $this->assertSame( $key, expSecretKey::decode( rtrim( strtr( base64_encode( $key ), '+/', '-_' ), '=' ) ) );
        $this->assertSame( $key, expSecretKey::decode( bin2hex( $key ) ) );
        foreach ( array( '', 'short', base64_encode( random_bytes( 16 ) ), base64_encode( random_bytes( 33 ) ), str_repeat( 'g', 64 ) ) as $bad )
            $this->assertNull( expSecretKey::decode( $bad ), $bad );
        $id = expSecretKey::id( $key );
        $this->assertMatchesRegularExpression( '/^[0-9a-f]{16}$/', $id );
        $this->assertSame( $id, expSecretKey::id( $key ) );
        $this->assertNotSame( $id, expSecretKey::id( random_bytes( 32 ) ) );
        $this->assertStringNotContainsString( $id, bin2hex( $key ), 'the id is not a piece of the key' );
    }

    /** SC-04 */
    public function testKeyFile()
    {
        $file = $this->dir . '/exp_secret.key';
        $this->assertNull( expSecretKey::current( false ) );
        $this->assertFileDoesNotExist( $file );
        $this->assertSame( 'file', expSecretKey::source() );
        $key = expSecretKey::current();
        $this->assertSame( 32, strlen( $key ) );
        $this->assertFileExists( $file );
        $this->assertSame( '0600', substr( sprintf( '%o', fileperms( $file ) ), -4 ) );
        $this->assertStringContainsString( 'Require all denied', file_get_contents( $this->dir . '/.htaccess' ) );
        expSecretKey::reset();
        $this->assertSame( $key, expSecretKey::current(), 'the file is kept' );
        $info = expSecretKey::info();
        $this->assertSame( 'file', $info['source'] );
        $this->assertTrue( $info['exists'] );
        $this->assertSame( expSecretKey::id( $key ), $info['id'] );
        $this->assertSame( $file, $info['file'] );
        $this->assertSame( '', $info['problem'] );
        $this->assertStringNotContainsString( base64_encode( $key ), json_encode( $info ) );
    }

    /** SC-05 */
    public function testEnvironmentKey()
    {
        $key = random_bytes( 32 );
        $old = random_bytes( 32 );
        putenv( 'EXP_SECRET_KEY=' . base64_encode( $key ) );
        putenv( 'EXP_SECRET_KEY_PREVIOUS=' . bin2hex( $old ) );
        expSecretKey::reset();
        $this->assertSame( 'env', expSecretKey::source() );
        $this->assertSame( $key, expSecretKey::current() );
        $this->assertFileDoesNotExist( $this->dir . '/exp_secret.key', 'no key file with an environment key' );
        $this->assertSame( $old, expSecretKey::previous() );
        $this->assertSame( 'env', expSecretKey::info()['previous_source'] );
        $this->assertSame( $old, expSecretKey::byID( expSecretKey::id( $old ) ) );
        $this->assertNull( expSecretKey::prepareRotation(), 'an environment key is not rotated here' );
        $this->assertFalse( expSecrets::rotate() );

        putenv( 'EXP_SECRET_KEY=tooshort' );
        expSecretKey::reset();
        $this->assertNull( expSecretKey::current() );
        $this->assertNotSame( '', expSecretKey::info()['problem'] );
    }

    /** SC-06 */
    public function testRotationOfTheFile()
    {
        $old = expSecretKey::current();
        $new = expSecretKey::prepareRotation();
        $this->assertSame( 32, strlen( $new ) );
        $this->assertNotSame( $old, $new );
        $this->assertSame( $new, expSecretKey::prepareRotation(), 'a rotation in progress keeps its new key' );
        $this->assertSame( $old, expSecretKey::current(), 'still the old key until committed' );
        $this->assertTrue( expSecretKey::commitRotation() );
        $this->assertSame( $new, expSecretKey::current() );
        $this->assertSame( $old, expSecretKey::previous() );
        $this->assertFileExists( $this->dir . '/exp_secret.key.previous' );
        $this->assertFileDoesNotExist( $this->dir . '/exp_secret.key.next' );
        $this->assertSame( $new, expSecretKey::byID( expSecretKey::id( $new ) ) );
        $this->assertSame( $old, expSecretKey::byID( expSecretKey::id( $old ) ) );
        $this->assertNull( expSecretKey::byID( 'ffffffffffffffff' ) );
        $this->assertFalse( expSecretKey::commitRotation(), 'nothing to commit' );
    }

    /** SC-07 */
    public function testGetValidNameAndMask()
    {
        putenv( 'T691_SECRET=from-the-env' );
        $this->assertSame( 'from-the-env', expSecrets::get( 'env:T691_SECRET' ) );
        putenv( 'T691_SECRET' );
        $this->assertNull( expSecrets::get( 'env:T691_SECRET' ) );
        $this->assertNull( expSecrets::get( 'not valid!' ) );
        $this->assertNull( expSecrets::get( '' ) );
        foreach ( array( 'db', 'datatable.archive', 'env:VAR', 'a:b-c_d.e' ) as $name )
            $this->assertTrue( expSecrets::validName( $name ), $name );
        foreach ( array( '', 'env:', 'a b', 'a/b', str_repeat( 'a', 129 ), 'ä', null, 5 ) as $name )
            $this->assertFalse( expSecrets::validName( $name ), var_export( $name, true ) );
        putenv( 'T691_SECRET=p@ss w/rd' );
        $masked = expSecrets::mask( 'login failed for p@ss w/rd and ' . rawurlencode( 'p@ss w/rd' ) . ', typed: hunter2',
                                    array( 'env:T691_SECRET' ), array( 'hunter2', 'ab' ) );
        putenv( 'T691_SECRET' );
        $this->assertStringNotContainsString( 'p@ss', $masked );
        $this->assertStringNotContainsString( 'hunter2', $masked );
        $this->assertStringContainsString( '[secret env:T691_SECRET]', $masked );
        $this->assertStringContainsString( '[secret]', $masked );
    }

    /** SC-08 */
    public function testStoreThroughTheTable()
    {
        $db = $this->store();
        $this->assertTrue( expSecrets::set( 'db.archive', 'first-value', 'datatable', 'The archive' ) );
        expSecrets::reset();
        $this->assertSame( 'first-value', expSecrets::get( 'db.archive' ) );
        $rows = $db->arrayQuery( 'SELECT * FROM exp_secret' );
        $this->assertCount( 1, $rows );
        $this->assertStringNotContainsString( 'first-value', implode( ' ', $rows[0] ) );
        $this->assertStringNotContainsString( 'first-value', base64_decode( $rows[0]['ciphertext'] ) );
        $this->assertSame( expSecretKey::id( expSecretKey::current() ), $rows[0]['key_id'] );
        $this->assertSame( 'datatable', $rows[0]['scope'] );

        $names = expSecrets::names();
        $this->assertSame( array( 'db.archive' ), array_keys( $names ) );
        $this->assertSame( 'store', $names['db.archive']['source'] );
        $this->assertTrue( $names['db.archive']['readable'] );
        $this->assertSame( 'The archive', $names['db.archive']['description'] );
        $this->assertStringNotContainsString( 'first-value', json_encode( $names ) );
        $this->assertSame( array(), expSecrets::names( 'sqliimport' ) );

        $this->assertTrue( expSecrets::set( 'db.archive', 'second-value', 'datatable' ) );
        expSecrets::reset();
        $this->assertSame( 'second-value', expSecrets::get( 'db.archive' ) );
        $this->assertSame( 'The archive', expSecrets::names()['db.archive']['description'], 'the description is kept when none is given' );
        $this->assertCount( 1, $db->arrayQuery( 'SELECT id FROM exp_secret' ) );

        $this->assertTrue( expSecrets::describe( 'db.archive', 'Another' ) );
        $this->assertSame( 'Another', expSecrets::names()['db.archive']['description'] );
        $this->assertFalse( expSecrets::describe( 'nosuch', 'x' ) );

        $this->assertTrue( expSecrets::remove( 'db.archive' ) );
        expSecrets::reset();
        $this->assertNull( expSecrets::get( 'db.archive' ) );
        $this->assertSame( array(), expSecrets::names() );
    }

    /** SC-09 */
    public function testRotateAndReencrypt()
    {
        $db = $this->store();
        expSecrets::set( 'one', 'value-one', 'datatable' );
        expSecrets::set( 'two', 'value-two', 'sqliimport' );
        $old = expSecretKey::current();
        $counts = expSecrets::rotate();
        $this->assertSame( array( 'reencrypted' => 2, 'current' => 0, 'unreadable' => 0 ), $counts );
        $new = expSecretKey::current();
        $this->assertNotSame( $old, $new );
        expSecrets::reset();
        $this->assertSame( 'value-one', expSecrets::get( 'one' ) );
        $this->assertSame( 'value-two', expSecrets::get( 'two' ) );
        foreach ( $db->arrayQuery( 'SELECT key_id FROM exp_secret' ) as $row )
            $this->assertSame( expSecretKey::id( $new ), $row['key_id'] );
        $this->assertSame( array( 'reencrypted' => 0, 'current' => 2, 'unreadable' => 0 ), expSecrets::reencryptAll() );

        // a row of a key that is gone: unreadable, counted, left as it is
        $foreign = expSecrets::encrypt( 'lost', 'three', random_bytes( 32 ) );
        $db->query( "INSERT INTO exp_secret ( name, scope, description, ciphertext, key_id ) VALUES ( 'three', '', '', '$foreign', 'ffffffffffffffff' )" );
        expSecrets::reset();
        $this->assertNull( expSecrets::get( 'three' ) );
        $this->assertFalse( expSecrets::names()['three']['readable'] );
        $this->assertSame( array( 'reencrypted' => 0, 'current' => 2, 'unreadable' => 1 ), expSecrets::reencryptAll() );
    }

    /** SC-10 */
    public function testRequiredAndRefusals()
    {
        try
        {
            expSecrets::required( 'env:T691_NOT_SET', 'Archive database' );
            $this->fail( 'a missing secret throws' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'env:T691_NOT_SET', $e->getMessage() );
            $this->assertStringContainsString( 'Archive database', $e->getMessage() );
        }
        $this->store();
        $this->assertFalse( expSecrets::set( 'env:X', 'v' ) );
        $this->assertFalse( expSecrets::set( 'ok', '' ) );
        $this->assertFalse( expSecrets::set( 'ok', 'v', 'Bad Scope' ) );
        $this->assertFalse( expSecrets::set( 'bad name', 'v' ) );
        $this->assertTrue( expSecrets::set( 'ok', 'v', '' ) );
        $this->assertSame( 'v', expSecrets::required( 'ok', 'x' ) );
    }
}

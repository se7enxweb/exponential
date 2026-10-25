<?php
/**
 * "Your devices" (user/sessions): expUserAgent, expUserSession, expUserSessionTracker and the table expuser_session.
 * No installation: the parsing and the rules are plain functions; the table is checked in its schema files, in a
 * SQLite database built in var/tmp from the update file, and the tracker's writes run on a SQLite database in memory.
 *
 *  US-01 - A User-Agent header names the browser, the system and the kind of device (desktop, mobile, tablet, app, bot)
 *  US-02 - An address is shown as its network (IPv4 /24, IPv6 /48); a header is stored printable and cut short
 *  US-03 - A device is new when no earlier sign-in had the same browser, system and kind; the first one is not;
 *          the sign-in method comes from the view (user/login, user2fa/verify) or the entry script
 *  US-04 - The table is the same in share/db_schema.dba, its persistent object, the kernel schemas of MySQL,
 *          PostgreSQL and SQLite and the 6.0.0 to 6.0.15 update files; the SQLite update runs twice and keeps its rows
 *  US-05 - On a database: one session revoked, the others ended, all of a user ended (admin), each recorded with who
 *          and why; the retention ends expired sessions and removes old and orphaned rows; the page's list
 *  US-06 - The module declares user/sessions with the policy function sessions; the account overview links to it;
 *          eZUser checks the session on every request and ends its row at sign-out; the settings and the cronjob
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../datatypes/eZDatatypeTestFixtures.php';

class expUserSessionTest extends eZDatatypeTestCase
{
    const COLUMNS = array( 'browser', 'created', 'device_type', 'end_reason', 'ended', 'ended_by', 'first_ip', 'id', 'last_active', 'last_ip',
                           'method', 'os', 'remember_until', 'siteaccess', 'token_hash', 'user_agent', 'user_id' );

    const INDEXES = array( 'expuser_session_token' => array( 'unique', array( 'token_hash' ) ),
                           'expuser_session_user' => array( 'non-unique', array( 'user_id', 'ended' ) ),
                           'expuser_session_active' => array( 'non-unique', array( 'ended', 'last_active' ) ) );

    /** @var eZSQLite3DB|null */
    private $sqlite;

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function read( $file )
    {
        return file_get_contents( self::root() . '/' . $file );
    }

    protected function setUp(): void
    {
        parent::setUp();
        expUserSessionTracker::reset();
        if ( class_exists( 'expKernelTableSchema' ) )
            expKernelTableSchema::reset();
    }

    protected function tearDown(): void
    {
        expUserSessionTracker::reset();
        if ( class_exists( 'expKernelTableSchema' ) )
            expKernelTableSchema::reset();
        $this->sqlite = null;
        parent::tearDown();
    }

    /** US-01 */
    public static function agentProvider()
    {
        return array(
            'Chrome on Windows' => array( 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
                                          array( 'Chrome', '130', 'Windows', '10/11', 'desktop' ) ),
            'Firefox on Linux' => array( 'Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0',
                                         array( 'Firefox', '131', 'Linux', '', 'desktop' ) ),
            'Edge' => array( 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Edg/130.0.2849.56',
                             array( 'Edge', '130', 'Windows', '10/11', 'desktop' ) ),
            'Safari on macOS' => array( 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Safari/605.1.15',
                                        array( 'Safari', '17', 'macOS', '10.15', 'desktop' ) ),
            'Safari on iPhone' => array( 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
                                         array( 'Safari', '17', 'iOS', '17', 'mobile' ) ),
            'Chrome on iPad' => array( 'Mozilla/5.0 (iPad; CPU OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/126.0.6478.54 Mobile/15E148 Safari/604.1',
                                       array( 'Chrome', '126', 'iPadOS', '17', 'tablet' ) ),
            'Samsung on Android phone' => array( 'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/25.0 Chrome/121.0.0.0 Mobile Safari/537.36',
                                                 array( 'Samsung Internet', '25', 'Android', '14', 'mobile' ) ),
            'Chrome on Android tablet' => array( 'Mozilla/5.0 (Linux; Android 13; SM-X710) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36',
                                                 array( 'Chrome', '129', 'Android', '13', 'tablet' ) ),
            'curl' => array( 'curl/8.9.1', array( 'curl', '8', 'Unknown', '', 'app' ) ),
            'Windows WebDAV' => array( 'Microsoft-WebDAV-MiniRedir/10.0.19045', array( 'Windows WebDAV', '10', 'Unknown', '', 'app' ) ),
            'Googlebot' => array( 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', array( 'Googlebot', '', 'Unknown', '', 'bot' ) ),
            'empty' => array( '', array( 'Unknown', '', 'Unknown', '', 'unknown' ) ),
        );
    }

    #[PHPUnit\Framework\Attributes\DataProvider( 'agentProvider' )]
    public function testUserAgentIsParsed( $ua, array $want )
    {
        $p = expUserAgent::parse( $ua );
        $this->assertSame( $want, array( $p['browser'], $p['browser_version'], $p['os'], $p['os_version'], $p['device'] ) );
        $this->assertContains( $p['device'], expUserAgent::DEVICES );
    }

    /** US-01 */
    public function testLabel()
    {
        $this->assertSame( 'Firefox 131 on Linux', expUserAgent::label( expUserAgent::parse( 'Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0' ) ) );
        $this->assertSame( 'curl 8', expUserAgent::label( expUserAgent::parse( 'curl/8.9.1' ) ) );
    }

    /** US-02 */
    public function testApproximateAddressAndCleanHeader()
    {
        $this->assertSame( '203.0.113.x', expUserAgent::approximateIP( '203.0.113.57' ) );
        $this->assertSame( '198.51.100.x', expUserAgent::approximateIP( '::ffff:198.51.100.7' ) );
        $this->assertSame( '2001:db8:85a3::/48', expUserAgent::approximateIP( '2001:db8:85a3:8d3:1319:8a2e:370:7348' ) );
        $this->assertSame( '', expUserAgent::approximateIP( '' ) );
        $this->assertSame( 'unknown', expUserAgent::approximateIP( 'unknown' ) );
        $this->assertSame( 'a b', expUserAgent::clean( "a\r\nb" ) );
        $this->assertSame( expUserAgent::MAX_LENGTH, mb_strlen( expUserAgent::clean( str_repeat( 'x', 2000 ) ) ) );
    }

    /** US-03 */
    public function testNewDeviceRule()
    {
        $firefox = array( 'browser' => 'Firefox', 'os' => 'Windows', 'device_type' => 'desktop' );
        $this->assertFalse( expUserSessionTracker::isNewAmong( $firefox, array() ), 'the first sign-in has nothing to compare with' );
        $this->assertTrue( expUserSessionTracker::isNewAmong( $firefox, array(), true ), 'unless FirstSignInMail' );
        $this->assertFalse( expUserSessionTracker::isNewAmong( $firefox, array( array( 'browser' => 'firefox', 'os' => 'Windows', 'device_type' => 'desktop' ) ) ) );
        $this->assertTrue( expUserSessionTracker::isNewAmong( $firefox, array( array( 'browser' => 'Firefox', 'os' => 'Linux', 'device_type' => 'desktop' ),
                                                                              array( 'browser' => 'Chrome', 'os' => 'Windows', 'device_type' => 'desktop' ) ) ) );
    }

    /** US-03 */
    public function testSignInMethod()
    {
        $this->assertSame( 'password', expUserSessionTracker::methodFor( 'user', 'login' ) );
        $this->assertSame( 'two_factor', expUserSessionTracker::methodFor( 'user2fa', 'verify' ) );
        $this->assertSame( 'single_sign_on', expUserSessionTracker::methodFor( 'content', 'view' ) );
        $this->assertSame( 'unknown', expUserSessionTracker::methodFor( '', '' ) );
        expUserSessionTracker::setMethodHint( 'webdav' );
        $this->assertSame( 'webdav', expUserSessionTracker::detectMethod() );
        foreach ( array( 'password', 'two_factor', 'rest', 'soap', 'webdav', 'single_sign_on', 'unknown' ) as $m )
            $this->assertContains( $m, expUserSession::METHODS );
    }

    /** US-04 */
    public function testTheDbaAndThePersistentObject()
    {
        $schema = null;
        include self::root() . '/share/db_schema.dba';
        $this->assertArrayHasKey( 'expuser_session', $schema );
        $table = $schema['expuser_session'];
        $this->assertSame( self::COLUMNS, array_keys( $table['fields'] ) );
        $this->assertSame( 'auto_increment', $table['fields']['id']['type'] );
        $this->assertSame( array_merge( array( 'PRIMARY' ), array_keys( self::INDEXES ) ), array_keys( $table['indexes'] ) );
        foreach ( self::INDEXES as $name => $index )
            $this->assertSame( $index, array( $table['indexes'][$name]['type'], $table['indexes'][$name]['fields'] ), $name );
        $definition = expUserSession::definition();
        $columns = array_keys( $definition['fields'] );
        sort( $columns );
        $this->assertSame( self::COLUMNS, $columns );
        $this->assertSame( array( 'expuser_session', array( 'id' ), 'id' ), array( $definition['name'], $definition['keys'], $definition['increment_key'] ) );
        foreach ( array_merge( self::COLUMNS, array_keys( self::INDEXES ), array( 'expuser_session_id_seq' ) ) as $name )
            $this->assertLessThanOrEqual( 30, strlen( $name ), "Oracle: $name" );
    }

    /** US-04 */
    public function testTheSqlFilesCarryTheTable()
    {
        $pattern = '/CREATE TABLE (?:IF NOT EXISTS )?`?expuser_session`? \((.*?)\n\s*\)/s';
        foreach ( array( 'kernel/sql/mysql/kernel_schema.sql', 'kernel/sql/postgresql/kernel_schema.sql', 'kernel/sql/sqlite/schema.sql',
                         'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql', 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql',
                         'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' ) as $file )
        {
            $sql = self::read( $file );
            $this->assertMatchesRegularExpression( $pattern, $sql, $file );
            preg_match( $pattern, $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|varchar|character)/m', $m[1], $cols );
            $this->assertSame( self::COLUMNS, $cols[1], $file );
            foreach ( self::INDEXES as $name => $index )
                $this->assertMatchesRegularExpression( $index[0] === 'unique' ? '/(?:UNIQUE KEY|UNIQUE INDEX(?: IF NOT EXISTS)?) ' . $name . ' /'
                                                                               : '/(?<!UNIQUE )(?:KEY|INDEX(?: IF NOT EXISTS)?) ' . $name . ' /', $sql, "$file $name" );
        }
        $pg = self::read( 'kernel/sql/postgresql/kernel_schema.sql' );
        $this->assertStringContainsString( 'CREATE SEQUENCE expuser_session_id_seq', $pg );
        $this->assertStringContainsString( 'ADD CONSTRAINT expuser_session_pkey PRIMARY KEY (id)', $pg );
        $this->assertStringContainsString( "setval('expuser_session_id_seq'", self::read( 'kernel/sql/postgresql/setval.sql' ) );
        $mysql = self::read( 'update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertLessThan( strpos( $mysql, 'ALTER TABLE ezprest_authcode' ), strpos( $mysql, 'CREATE TABLE IF NOT EXISTS expuser_session' ),
                               'before the statement that stops a second run' );
        $this->assertMatchesRegularExpression( "/table_name = 'expuser_session' \)/", self::read( 'update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
        $this->assertFileExists( self::root() . '/update/common/scripts/6.0/createusersessiontable.php' );
    }

    /** US-04 */
    public function testSqliteUpdateRunsTwice()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::root() . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/user-session-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = self::read( 'update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $start = strpos( $sql, '-- The signed-in sessions of the users' );
            $this->assertNotFalse( $start );
            $section = substr( $sql, $start, strpos( $sql, "\n--\n-- ezuser.login", $start ) - $start );
            foreach ( preg_split( '/;\s*$/m', preg_replace( '/^--.*$/m', '', $section ) ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        try
        {
            $apply();
            $this->assertSame( self::COLUMNS, array_column( $pdo->query( 'PRAGMA table_info(expuser_session)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' ) );
            $pdo->exec( "INSERT INTO expuser_session (user_id, token_hash, created) VALUES (14, 'a', 1)" );
            $apply();
            $this->assertSame( 1, (int)$pdo->query( 'SELECT COUNT(*) FROM expuser_session' )->fetchColumn() );
            $this->expectException( PDOException::class );
            $pdo->exec( "INSERT INTO expuser_session (user_id, token_hash, created) VALUES (15, 'a', 2)" );
        }
        finally
        {
            $pdo = null;
            if ( is_file( $file ) )
                unlink( $file );
        }
    }

    /**
     * A SQLite database in memory with expuser_session from the kernel schema and the accounts 14 and 15, made the
     * current database.
     */
    private function sqlite()
    {
        if ( !extension_loaded( 'sqlite3' ) )
            $this->markTestSkipped( 'SQLite3 extension is not loaded' );
        $db = new eZSQLite3DB( array(
            'server' => '', 'port' => '', 'user' => '', 'password' => '', 'database' => ':memory:',
            'use_slave_server' => false, 'slave_server' => '', 'slave_port' => '', 'slave_user' => '', 'slave_password' => '',
            'slave_database' => '', 'socket' => false, 'charset' => 'utf-8', 'is_internal_charset' => true,
            'builtin_encoding' => false, 'connect_retries' => 0, 'use_persistent_connection' => 'disabled' ) );
        $schema = self::read( 'kernel/sql/sqlite/schema.sql' );
        preg_match( '/CREATE TABLE `expuser_session` \(.*?\n\);/s', $schema, $m );
        $db->query( $m[0] );
        preg_match_all( '/^CREATE (?:UNIQUE )?INDEX expuser_session_[a-z]+ ON expuser_session .*;$/m', $schema, $ix );
        foreach ( $ix[0] as $statement )
            $db->query( rtrim( $statement, ';' ) );
        $db->query( "CREATE TABLE ezuser ( contentobject_id INTEGER NOT NULL DEFAULT 0, email varchar(150) NOT NULL DEFAULT '',
                     login varchar(150) NOT NULL DEFAULT '', password_hash varchar(255) DEFAULT NULL,
                     password_hash_type INTEGER NOT NULL DEFAULT 1, PRIMARY KEY ( contentobject_id ) )" );
        foreach ( array( 14 => 'anna', 15 => 'bob' ) as $id => $login )
            $db->query( "INSERT INTO ezuser ( contentobject_id, email, login, password_hash, password_hash_type ) VALUES ( $id, '$login@example.invalid', '$login', 'x', 7 )" );
        eZDB::setInstance( $db );
        expKernelTableSchema::reset();
        expUserSessionTracker::reset();
        return $this->sqlite = $db;
    }

    private function row( $userID, $browser, $created, array $more = array() )
    {
        $row = new expUserSession( $more + array( 'user_id' => $userID, 'token_hash' => hash( 'sha256', uniqid( '', true ) . $browser . $created ),
            'created' => $created, 'last_active' => $created, 'first_ip' => '203.0.113.9', 'last_ip' => '203.0.113.9',
            'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 ' . $browser . '/131.0', 'browser' => $browser,
            'os' => 'Linux', 'device_type' => 'desktop', 'method' => 'password', 'remember_until' => 0, 'siteaccess' => 'site',
            'ended' => 0, 'ended_by' => 0, 'end_reason' => '' ) );
        $row->store();
        return $row;
    }

    /** US-05 */
    public function testRevokeOneOthersAndAll()
    {
        $this->sqlite();
        $this->assertTrue( expUserSessionTracker::available() );
        $now = time();
        $a = $this->row( 14, 'Firefox', $now - 300 );
        $b = $this->row( 14, 'Chrome', $now - 200 );
        $c = $this->row( 14, 'Safari', $now - 100 );
        $other = $this->row( 15, 'Firefox', $now - 100 );

        // one session, by the user: ended as revoked; a session of another user is never touched by its id
        $this->assertFalse( expUserSessionTracker::revoke( 14, $other->attribute( 'id' ), 14 ) );
        $this->assertTrue( expUserSessionTracker::revoke( 14, $a->attribute( 'id' ), 14 ) );
        $this->assertFalse( expUserSessionTracker::revoke( 14, $a->attribute( 'id' ), 14 ), 'an ended session is not ended again' );
        $a = expUserSession::fetchByID( $a->attribute( 'id' ) );
        $this->assertSame( array( 'revoked', 14 ), array( $a->attribute( 'end_reason' ), (int)$a->attribute( 'ended_by' ) ) );
        $this->assertFalse( $a->isActive() );

        // by an administrator: revoked_admin, with who did it
        $this->assertTrue( expUserSessionTracker::revoke( 14, $b->attribute( 'id' ), 1 ) );
        $this->assertSame( 'revoked_admin', expUserSession::fetchByID( $b->attribute( 'id' ) )->attribute( 'end_reason' ) );

        // all of a user (no session runs here, so none is kept)
        $this->assertSame( 1, expUserSessionTracker::revokeAll( 14, 1 ) );
        $this->assertSame( array(), expUserSession::fetchForUser( 14, true ) );
        $this->assertCount( 1, expUserSession::fetchForUser( 15, true ), 'the other user keeps the session' );
        $this->assertSame( array( 'password_change' ), array_values( array_unique( array_map( function ( $r ) { return $r->attribute( 'end_reason' ); },
            array_map( 'expUserSession::fetchByID', expUserSessionTracker::endOtherRows( 15, 'password_change', 15 ) ) ) ) ) );
        $this->assertNotNull( $c );
    }

    /** US-05 */
    public function testRetentionAndList()
    {
        $this->sqlite();
        $now = time();
        $timeout = expUserSessionTracker::sessionTimeout();
        $days = (int)expUserSessionTracker::setting( 'RetentionDays', 90 );
        $running = $this->row( 14, 'Firefox', $now - 60 );
        $idle = $this->row( 14, 'Chrome', $now - $timeout - 10 );
        $old = $this->row( 14, 'Safari', $now - ( $days + 5 ) * 86400, array( 'ended' => $now - ( $days + 1 ) * 86400, 'end_reason' => 'signout' ) );
        $recent = $this->row( 14, 'Edge', $now - 86400, array( 'ended' => $now - 3600, 'end_reason' => 'signout' ) );
        $orphan = $this->row( 99, 'Firefox', $now - 60 );

        $r = expUserSessionTracker::cleanup( $now );
        $this->assertSame( array( 'expired' => 1, 'removed' => 1, 'orphaned' => 1 ), $r );
        $this->assertSame( 'expired', expUserSession::fetchByID( $idle->attribute( 'id' ) )->attribute( 'end_reason' ) );
        $this->assertNull( expUserSession::fetchByID( $old->attribute( 'id' ) ) );
        $this->assertNull( expUserSession::fetchByID( $orphan->attribute( 'id' ) ) );
        $this->assertNotNull( expUserSession::fetchByID( $recent->attribute( 'id' ) ) );

        $list = expUserSessionTracker::listForUser( 14 );
        $this->assertSame( array( (int)$running->attribute( 'id' ) ), array_column( $list['active'], 'id' ) );
        $this->assertEqualsCanonicalizing( array( (int)$idle->attribute( 'id' ), (int)$recent->attribute( 'id' ) ), array_column( $list['ended'], 'id' ) );
        $item = $list['active'][0];
        $this->assertSame( array( 'Firefox 131 on Linux', '203.0.113.x', 'desktop', false ), array( $item['label'], $item['ip'], $item['device'], $item['current'] ) );

        // a device seen before is not new; a new browser is
        $again = $this->row( 14, 'Firefox', $now );
        $this->assertFalse( expUserSessionTracker::isNewDevice( 14, $again ) );
        $opera = $this->row( 14, 'Opera', $now );
        $this->assertTrue( expUserSessionTracker::isNewDevice( 14, $opera ) );
        $first = $this->row( 15, 'Firefox', $now );
        $this->assertFalse( expUserSessionTracker::isNewDevice( 15, $first ), 'the first sign-in of an account' );
    }

    /** US-06 */
    public function testWiring()
    {
        $Module = $ViewList = $FunctionList = null;
        include self::root() . '/kernel/user/module.php';
        $this->assertSame( array( 'login' ), $ViewList['sessions']['functions'] );
        $this->assertSame( 'sessions.php', $ViewList['sessions']['script'] );
        $this->assertSame( array( 'UserID' ), $ViewList['sessions']['params'] );
        $this->assertArrayHasKey( 'sessions', $FunctionList );
        $this->assertFileExists( self::root() . '/kernel/user/sessions.php' );
        foreach ( array( 'design/admin4/templates/user/sessions.tpl', 'design/admin/templates/user/sessions.tpl', 'design/standard/templates/user/sessions.tpl',
                         'design/standard/templates/user/new_signin_mail.tpl', 'design/standard/templates/user/new_signin_admin_mail.tpl' ) as $tpl )
            $this->assertFileExists( self::root() . '/' . $tpl );
        $this->assertSame( self::read( 'design/admin4/templates/user/sessions.tpl' ), self::read( 'design/admin/templates/user/sessions.tpl' ) );

        $user = self::read( 'kernel/classes/datatypes/ezuser/ezuser.php' );
        $this->assertStringContainsString( 'expUserSessionTracker::signedIn( $user )', $user );
        $this->assertStringContainsString( 'expUserSessionTracker::sessionIsCurrent( $user )', $user );
        $this->assertStringContainsString( 'expUserSessionTracker::endRevokedSession( $user )', $user );
        $this->assertStringContainsString( 'expUserSessionTracker::signedOut()', $user );

        $site = eZINI::instance();
        $this->assertSame( 'enabled', $site->variable( 'UserSessionSettings', 'Tracking' ) );
        $this->assertSame( 'enabled', $site->variable( 'UserSessionSettings', 'NewDeviceMail' ), 'the user alert is on by default' );
        $this->assertSame( 'disabled', $site->variable( 'UserSessionSettings', 'NewDeviceAdminMail' ) );
        $this->assertSame( '31104000', $site->variable( 'Session', 'RememberMeTimeout' ), 'Remember me stays 360 days' );
        $cron = self::read( 'settings/cronjob.ini' );
        $this->assertMatchesRegularExpression( '/\[CronjobPart-infrequent\](?:(?!\n\[)[\s\S])*Scripts\[\]=usersessions\.php/', $cron );
        $this->assertFileExists( self::root() . '/cronjobs/usersessions.php' );
        $this->assertArrayHasKey( expUserSessionTracker::AUDIT_NEW_DEVICE, expAuditTaxonomy::catalogue() );
        $this->assertArrayHasKey( expUserSessionTracker::AUDIT_REVOKE, expAuditTaxonomy::catalogue() );
        $this->assertContains( 'sessions', expUserAccountOverview::ACTIONS );
    }
}

<?php
/**
 * The user audit log (expUserAudit, expAuditUserAuditSink, user/audit, table expuser_audit). Needs no installation:
 * the rows are made from audit records built here, the SQL runs on an SQLite database made in var/tmp from the
 * update file, the rest are the source files.
 *
 *  UA-01 which records are kept: Events[] and a user account or group named (object user, else target user/group);
 *        a record about a node or a role alone is not
 *  UA-02 the row: who, when (ms), where (siteaccess, host given or the record's, address unless RecordIP=disabled),
 *        whom, result and reason of a refused attempt, before and after as JSON
 *  UA-03 never a password, a hash or a secret, at any depth; long values are cut
 *  UA-04 the filters of a request and their query string; the WHERE clause by id, by login, by action and its
 *        branch, by result and by day
 *  UA-05 the CSV cells that a spreadsheet would run are made text; flatten() for the table
 *  UA-06 the table in share/db_schema.dba, the kernel schemas of MySQL, PostgreSQL and SQLite (Oracle installs from
 *        the .dba through ezoracle), the update files guarded, the SQLite update twice, the event id stored once
 *  UA-07 the wiring: the sink is registered for the access channel, the view needs user/audit, the cronjob part,
 *        the menu link with its policy, the help map, no inline CSS in either template, the upgrade script
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expUserAuditTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    private static $columns = array( 'actor_id', 'actor_login', 'client_ip', 'created', 'event_id', 'event_name', 'host', 'id',
                                     'module_view', 'reason', 'request_id', 'result', 'siteaccess', 'target_id', 'target_login',
                                     'target_type', 'time_ms', 'value_after', 'value_before' );

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        require_once self::$root . '/kernel/classes/user/expuseraudit.php';
    }

    private function settings( array $over = array() )
    {
        return $over + array( 'enabled' => true, 'events' => expUserAudit::$defaultEvents, 'record_ip' => true,
                              'retention_days' => 730, 'max_value_length' => 2000 );
    }

    private function record( array $over = array() )
    {
        return $over + array(
            'id' => '01JABCDEFGHJKMNPQRSTVWXYZ0',
            'name' => 'access.user.change',
            'time' => '2026-10-28T10:11:12.345Z',
            'request' => array( 'id' => 'r1', 'siteaccess' => 'admin', 'host' => 'web1', 'module' => 'content/edit' ),
            'actor' => array( 'user_id' => 14, 'login' => 'editor1', 'ip' => '192.0.2.0' ),
            'object' => array( 'type' => 'user', 'id' => 120, 'login' => 'member1' ),
            'before' => array( 'name' => 'Old name' ),
            'after' => array( 'name' => 'New name' ),
            'result' => 'success',
        );
    }

    /** UA-01 */
    public function testWhichRecordsAreKept()
    {
        $s = $this->settings();
        $this->assertNotNull( expUserAudit::rowFromRecord( $this->record(), $s ) );
        // a role assigned to a group: the target
        $assign = $this->record( array( 'name' => 'access.role.assign', 'object' => array( 'type' => 'role', 'id' => 3 ),
                                        'target' => array( 'type' => 'group', 'id' => 12, 'name' => 'Members' ) ) );
        $row = expUserAudit::rowFromRecord( $assign, $s );
        $this->assertSame( 'group', $row['target_type'] );
        $this->assertSame( 12, $row['target_id'] );
        // a refusal about a node, not an account
        $this->assertNull( expUserAudit::rowFromRecord( $this->record( array( 'name' => 'access.permission.refused',
            'object' => array( 'type' => 'view', 'id' => 'content/edit' ), 'target' => array( 'type' => 'node', 'id' => 2 ) ) ), $s ) );
        // a content event, even about a user object, is not in Events[]
        $this->assertNull( expUserAudit::rowFromRecord( $this->record( array( 'name' => 'content.node.move' ) ), $s ) );
        // a group as the object is not a target of its own (only a role's target may be a group)
        $this->assertNull( expUserAudit::rowFromRecord( $this->record( array( 'object' => array( 'type' => 'group', 'id' => 12 ) ) ), $s ) );
        // a session event of a command (a cronjob signs in and out as a user) is not a person's session; a web one is
        $logout = array( 'name' => 'access.session.logout', 'object' => array( 'type' => 'user', 'id' => 14 ) );
        $this->assertNull( expUserAudit::rowFromRecord( $this->record( $logout + array( 'actor' => array( 'user_id' => 14, 'cli' => array( 'command' => 'runcronjobs.php' ) ) ) ), $s ) );
        $this->assertNotNull( expUserAudit::rowFromRecord( $this->record( $logout ), $s ) );
        // switched off
        $this->assertNull( expUserAudit::rowFromRecord( $this->record(), $this->settings( array( 'enabled' => false ) ) ) );
        $this->assertTrue( expUserAudit::wantsName( 'access.user.password.change', array( 'access.user.*' ) ) );
        $this->assertTrue( expUserAudit::wantsName( 'access.user', array( 'access.user.*' ) ) );
        $this->assertFalse( expUserAudit::wantsName( 'access.username', array( 'access.user.*' ) ) );
        $this->assertFalse( expUserAudit::wantsName( 'access.role.change', array( 'access.role.assign' ) ) );
    }

    /** UA-02 */
    public function testTheRow()
    {
        $row = expUserAudit::rowFromRecord( $this->record(), $this->settings(), 'www.example.com' );
        $this->assertSame( '01JABCDEFGHJKMNPQRSTVWXYZ0', $row['event_id'] );
        $this->assertSame( strtotime( '2026-10-28T10:11:12Z' ), $row['created'] );
        $this->assertSame( strtotime( '2026-10-28T10:11:12Z' ) * 1000 + 345, $row['time_ms'] );
        $this->assertSame( 14, $row['actor_id'] );
        $this->assertSame( 'editor1', $row['actor_login'] );
        $this->assertSame( 120, $row['target_id'] );
        $this->assertSame( 'member1', $row['target_login'] );
        $this->assertSame( 'user', $row['target_type'] );
        $this->assertSame( 'admin', $row['siteaccess'] );
        $this->assertSame( 'www.example.com', $row['host'] );
        $this->assertSame( '192.0.2.0', $row['client_ip'] );
        $this->assertSame( 'content/edit', $row['module_view'] );
        $this->assertSame( '{"name":"Old name"}', $row['value_before'] );
        $this->assertSame( '{"name":"New name"}', $row['value_after'] );
        $cols = array_keys( $row );
        sort( $cols );
        $this->assertSame( array_values( array_diff( self::$columns, array( 'id' ) ) ), $cols );

        // the record's host when none is given; no address with RecordIP=disabled
        $row = expUserAudit::rowFromRecord( $this->record(), $this->settings( array( 'record_ip' => false ) ) );
        $this->assertSame( 'web1', $row['host'] );
        $this->assertSame( '', $row['client_ip'] );

        // a refused attempt keeps result and reason; an unknown login has no id
        $failed = $this->record( array( 'name' => 'access.session.login.failed', 'result' => 'failed', 'reason' => 'not_found',
                                        'object' => array( 'type' => 'user', 'attempted_login' => 'sha256:ab' ),
                                        'before' => null, 'after' => null ) );
        $row = expUserAudit::rowFromRecord( $failed, $this->settings() );
        $this->assertSame( 'failed', $row['result'] );
        $this->assertSame( 'not_found', $row['reason'] );
        $this->assertSame( 0, $row['target_id'] );
        $this->assertSame( '', $row['value_before'] );
    }

    /** UA-03 */
    public function testNoSecrets()
    {
        $record = $this->record( array(
            'before' => array( 'password_hash' => 'x', 'Password' => 'y', 'login' => 'a', 'nested' => array( 'api_key' => 'k', 'token' => 't', 'ok' => 1 ) ),
            'after' => array( 'PasswordConfirm' => 'z', 'hash_type' => 5, 'secret' => 's', 'otp_secret' => 'o' ),
        ) );
        $row = expUserAudit::rowFromRecord( $record, $this->settings() );
        $this->assertSame( '{"login":"a","nested":{"ok":1}}', $row['value_before'] );
        $this->assertSame( '', $row['value_after'] );
        foreach ( array( 'password', 'hash', 'secret', 'token', 'api_key', 'otp' ) as $word )
            $this->assertStringNotContainsStringIgnoringCase( $word, $row['value_before'] . $row['value_after'] );

        $long = expUserAudit::encodeValue( array( 'text' => str_repeat( 'ä', 500 ) ), 120 );
        $this->assertLessThanOrEqual( 120, strlen( $long ) );
        $this->assertStringEndsWith( '...', $long );
        $this->assertTrue( mb_check_encoding( $long, 'UTF-8' ) );
    }

    /** UA-04 */
    public function testFiltersAndWhere()
    {
        $f = expUserAudit::normaliseFilters( array( 'user' => ' member1 ', 'action' => 'access.user.*', 'result' => 'nonsense',
                                                    'from' => '2026-02-30', 'to' => '2026-10-28', 'limit' => '7' ) );
        $this->assertSame( 'member1', $f['user'] );
        $this->assertSame( 'access.user', $f['action'] );
        $this->assertSame( '', $f['result'] );
        $this->assertSame( '', $f['from'], 'not a date' );
        $this->assertSame( '2026-10-28', $f['to'] );
        $this->assertSame( 50, $f['limit'] );
        $this->assertSame( '?user=member1&action=access.user&to=2026-10-28', expUserAudit::queryString( $f ) );
        $this->assertSame( '', expUserAudit::queryString( expUserAudit::normaliseFilters( array() ) ) );
        $f2 = expUserAudit::normaliseFilters( array( 'action' => "access.user'; DROP TABLE x;--" ) );
        $this->assertSame( 'access.userdroptablex', $f2['action'] );

        $esc = function ( $s ) { return str_replace( "'", "''", $s ); };
        $where = expUserAudit::whereClause( $f, $esc );
        $this->assertStringContainsString( "LOWER( target_login ) = 'member1' OR LOWER( actor_login ) = 'member1'", $where );
        $this->assertStringContainsString( "event_name = 'access.user' OR event_name LIKE 'access.user.%'", $where );
        $this->assertStringContainsString( 'created < ' . ( gmmktime( 0, 0, 0, 10, 28, 2026 ) + 86400 ), $where );
        $byID = expUserAudit::whereClause( expUserAudit::normaliseFilters( array( 'user' => '120', 'result' => 'refused', 'from' => '2026-10-01' ) ), $esc );
        $this->assertStringContainsString( 'target_id = 120 OR actor_id = 120', $byID );
        $this->assertStringContainsString( "result = 'refused'", $byID );
        $this->assertStringContainsString( 'created >= ' . gmmktime( 0, 0, 0, 10, 1, 2026 ), $byID );
        $this->assertSame( '', expUserAudit::whereClause( expUserAudit::normaliseFilters( array() ), $esc ) );
    }

    /** UA-05 */
    public function testCsvCellsAndFlatten()
    {
        foreach ( array( '=1+1', '+1', '-1', '@SUM(A1)', "\tx" ) as $v )
            $this->assertSame( "'" . $v, expUserAudit::csvCell( $v ) );
        $this->assertSame( 'member1', expUserAudit::csvCell( 'member1' ) );
        $this->assertSame( array( 'limitation.identifier' => 'Subtree', 'policies' => 'content/read, user/login', 'self' => 'true' ),
                           expUserAudit::flatten( array( 'limitation' => array( 'identifier' => 'Subtree' ),
                                                         'policies' => array( 'content/read', 'user/login' ), 'self' => true ) ) );
    }

    /** UA-06 */
    public function testSchemaEverywhere()
    {
        $schema = null;
        include self::$root . '/share/db_schema.dba';
        $this->assertArrayHasKey( 'expuser_audit', $schema );
        $fields = array_keys( $schema['expuser_audit']['fields'] );
        $this->assertSame( self::$columns, $fields );
        $this->assertSame( 'auto_increment', $schema['expuser_audit']['fields']['id']['type'] );
        $this->assertSame( 'unique', $schema['expuser_audit']['indexes']['expuser_audit_event']['type'] );
        foreach ( array_keys( $schema['expuser_audit']['indexes'] ) as $index )
            $this->assertLessThanOrEqual( 30, strlen( $index ), "Oracle: $index is too long" );
        // Oracle keeps identifiers to 30 characters and reserves some words
        foreach ( $fields as $f )
            $this->assertNotContains( strtoupper( $f ), array( 'ACCESS', 'COMMENT', 'DATE', 'FILE', 'LEVEL', 'MODE', 'SESSION', 'SIZE', 'UID', 'USER', 'BEFORE', 'AFTER' ) );

        foreach ( array( 'mysql' => 'kernel/sql/mysql/kernel_schema.sql', 'postgresql' => 'kernel/sql/postgresql/kernel_schema.sql',
                         'sqlite' => 'kernel/sql/sqlite/schema.sql' ) as $engine => $file )
        {
            $sql = file_get_contents( self::$root . '/' . $file );
            $this->assertMatchesRegularExpression( '/CREATE TABLE `?expuser_audit`? \((.*?)\n\)/s', $sql, $engine );
            preg_match( '/CREATE TABLE `?expuser_audit`? \((.*?)\n\)/s', $sql, $m );
            preg_match_all( '/^[\s,]*`?([a-z_]+)`? (?:int|integer|bigint|varchar|character|longtext|text)/m', $m[1], $cols );
            $this->assertSame( self::$columns, $cols[1], $engine );
            foreach ( array( 'expuser_audit_event', 'expuser_audit_target', 'expuser_audit_actor', 'expuser_audit_name', 'expuser_audit_created' ) as $index )
                $this->assertMatchesRegularExpression( '/(?:KEY|INDEX) ' . $index . '\b/', $sql, "$engine $index" );
        }
        $pg = file_get_contents( self::$root . '/kernel/sql/postgresql/kernel_schema.sql' );
        $this->assertStringContainsString( 'CREATE SEQUENCE expuser_audit_id_seq', $pg );
        $this->assertStringContainsString( 'ADD CONSTRAINT expuser_audit_pkey PRIMARY KEY (id)', $pg );
        $this->assertStringContainsString( "setval('expuser_audit_id_seq'", file_get_contents( self::$root . '/kernel/sql/postgresql/setval.sql' ) );
        $this->assertStringContainsString( "setval('expuser_audit_id_seq'", file_get_contents( self::$root . '/kernel/sql/postgresql/sequence.sql.template' ) );

        $this->assertStringContainsString( 'CREATE TABLE IF NOT EXISTS expuser_audit', file_get_contents( self::$root . '/update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql' ) );
        $pgUpdate = file_get_contents( self::$root . '/update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql' );
        $this->assertMatchesRegularExpression( "/IF NOT EXISTS \( SELECT 1 FROM information_schema.tables WHERE table_schema = current_schema\(\) AND table_name = 'expuser_audit' \)/", $pgUpdate );
    }

    /** UA-06 */
    public function testSqliteUpdateRunsTwiceAndKeepsAnEventOnce()
    {
        if ( !in_array( 'sqlite', PDO::getAvailableDrivers(), true ) )
            $this->markTestSkipped( 'no pdo_sqlite' );
        $dir = self::$root . '/var/tmp';
        if ( !is_dir( $dir ) )
            mkdir( $dir, 0777, true );
        $file = $dir . '/useraudit-update-test-' . getmypid() . '.db';
        $pdo = new PDO( 'sqlite:' . $file );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $apply = function () use ( $pdo ) {
            $sql = file_get_contents( self::$root . '/update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql' );
            $section = substr( $sql, strpos( $sql, '-- expuser_audit:' ) );
            $body = preg_replace( '/^--.*$/m', '', $section );
            foreach ( preg_split( '/;\s*$/m', $body ) as $statement )
                if ( trim( $statement ) !== '' )
                    $pdo->exec( $statement );
        };
        $apply();
        $this->assertSame( self::$columns, array_column( $pdo->query( 'PRAGMA table_info(expuser_audit)' )->fetchAll( PDO::FETCH_ASSOC ), 'name' ) );

        // the row of a record, inserted as storeRecords() does
        $row = expUserAudit::rowFromRecord( $this->record(), $this->settings() );
        $insert = 'INSERT INTO expuser_audit ( ' . implode( ', ', array_keys( $row ) ) . ' ) VALUES ( '
                . implode( ', ', array_map( function ( $v ) use ( $pdo ) { return is_int( $v ) ? (string)$v : $pdo->quote( $v ); }, $row ) ) . ' )';
        $pdo->exec( $insert );
        $apply();
        $this->assertSame( '1', (string)$pdo->query( 'SELECT COUNT(*) FROM expuser_audit' )->fetchColumn(), 'the second run kept the row' );
        $refused = false;
        try { $pdo->exec( $insert ); }
        catch ( PDOException $e ) { $refused = true; }
        $this->assertTrue( $refused, 'an event is stored once' );

        // the WHERE clause runs on it
        $where = expUserAudit::whereClause( expUserAudit::normaliseFilters( array( 'user' => 'MEMBER1', 'action' => 'access.user', 'to' => '2026-10-28' ) ),
                                            function ( $s ) { return str_replace( "'", "''", $s ); } );
        $this->assertSame( '1', (string)$pdo->query( 'SELECT COUNT(*) FROM expuser_audit' . $where )->fetchColumn() );
        $where = expUserAudit::whereClause( expUserAudit::normaliseFilters( array( 'user' => '999' ) ), function ( $s ) { return $s; } );
        $this->assertSame( '0', (string)$pdo->query( 'SELECT COUNT(*) FROM expuser_audit' . $where )->fetchColumn() );
        $pdo = null;
        @unlink( $file );
    }

    /** UA-07 */
    public function testWiring()
    {
        $audit = file_get_contents( self::$root . '/settings/audit.ini' );
        $this->assertStringContainsString( 'SinkClasses[useraudit]=expAuditUserAuditSink', $audit );
        $access = substr( $audit, strpos( $audit, '[AuditChannel_access]' ) );
        $access = substr( $access, 0, strpos( $access, "\n[", 1 ) );
        $this->assertStringContainsString( 'Sinks[]=useraudit', $access );
        $this->assertStringContainsString( '[UserAuditSettings]', $audit );
        $this->assertStringContainsString( "'useraudit' => 'expAuditUserAuditSink'", file_get_contents( self::$root . '/kernel/classes/audit/sinks/expauditsinkregistry.php' ) );

        $ViewList = $FunctionList = array();
        include self::$root . '/kernel/user/module.php';
        $this->assertSame( array( 'audit' ), $ViewList['audit']['functions'] );
        $this->assertArrayHasKey( 'audit', $FunctionList );

        $cron = file_get_contents( self::$root . '/settings/cronjob.ini' );
        $this->assertStringContainsString( "[CronjobPart-useraudit]\nScripts[]=useraudit.php", $cron );
        $infrequent = substr( $cron, strpos( $cron, '[CronjobPart-infrequent]' ) );
        $infrequent = substr( $infrequent, 0, strpos( $infrequent, "\n[", 1 ) );
        $this->assertStringContainsString( 'Scripts[]=useraudit.php', $infrequent );
        $this->assertFileExists( self::$root . '/cronjobs/useraudit.php' );
        $this->assertFileExists( self::$root . '/update/common/scripts/6.0/createuseraudittable.php' );

        $menu = file_get_contents( self::$root . '/settings/menu.ini' );
        $this->assertStringContainsString( 'Links[user_audit]=user/audit', $menu );
        $this->assertStringContainsString( 'PolicyList_user_audit[]=user/audit', $menu );
        $this->assertStringContainsString( 'Map[user/audit]=features/6.0/user-audit.md', file_get_contents( self::$root . '/settings/exphelp.ini' ) );
        $this->assertFileExists( self::$root . '/doc/eng-us/features/6.0/user-audit.md' );

        $admin4 = file_get_contents( self::$root . '/design/admin4/templates/user/audit.tpl' );
        $this->assertSame( $admin4, file_get_contents( self::$root . '/design/admin/templates/user/audit.tpl' ) );
        $this->assertStringNotContainsString( '<style', $admin4 );
        $this->assertDoesNotMatchRegularExpression( '/\sstyle\s*=/', $admin4 );
        $this->assertStringContainsString( 'name="ExportButton"', $admin4 );
        $this->assertStringContainsString( 'method="post"', $admin4 );
    }
}

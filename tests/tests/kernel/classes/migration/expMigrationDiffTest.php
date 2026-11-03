<?php
/**
 * expMigrationDiff without a database of the installation: the kinds of --compare, the fingerprints, the comparison,
 * the order of the things a migration makes, the snapshot of two SQLite files with known differences, the settings of
 * two folders, a dump told by its first bytes, and a snapshot from another installation cleaned.
 *
 *  DM-01 --compare: the default kinds, content only with a subtree, unknown kinds refused
 *  DM-02 fingerprints do not depend on how an engine gives a number, nor on the order of keys
 *  DM-03 compare(): added, changed, removed, and only the kinds both sides tell
 *  DM-04 order(): made before removed, content parents first, removed content below removed content left out
 *  DM-05 snapshot() of two SQLite files with known differences, every kind
 *  DM-06 settings of two folders: secrets left out
 *  DM-07 dumpEngine(), cleanSnapshot(), redactUrl()
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationDiffTest extends PHPUnit\Framework\TestCase
{
    /** @var string|null */
    private $dir;

    protected function tearDown(): void
    {
        if ( $this->dir !== null && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir, false );
    }

    private function dir()
    {
        if ( $this->dir === null )
        {
            $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-diff-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            mkdir( $this->dir, 0700, true );
        }
        return $this->dir;
    }

    /** DM-01 */
    public function testKinds()
    {
        $this->assertSame( expMigrationDiff::DEFAULT_KINDS, expMigrationDiff::kinds( '', false, $error ) );
        $this->assertNull( $error );
        $this->assertContains( 'content', expMigrationDiff::kinds( 'all', true ) );
        $this->assertSame( array( 'classes', 'roles' ), expMigrationDiff::kinds( 'roles, classes', false ) );
        $this->assertSame( array(), expMigrationDiff::kinds( 'content', false, $error ) );
        $this->assertStringContainsString( '--subtree', $error );
        $this->assertSame( array(), expMigrationDiff::kinds( 'classes,users', false, $error ) );
        $this->assertStringContainsString( 'not users', $error );
    }

    /** DM-02 */
    public function testFingerprints()
    {
        $this->assertSame( expMigrationDiff::hash( array( 'a' => 1, 'b' => '2.0' ) ), expMigrationDiff::hash( array( 'b' => 2, 'a' => '1' ) ) );
        $this->assertSame( expMigrationDiff::hash( array( 'x' => null ) ), expMigrationDiff::hash( array( 'x' => '' ) ) );
        $this->assertNotSame( expMigrationDiff::hash( array( 'x' => '01a' ) ), expMigrationDiff::hash( array( 'x' => '1a' ) ) );
    }

    /** DM-03 */
    public function testCompare()
    {
        $saved = array( 'kinds' => array( 'classes' => array( 'folder' => 'a', 'article' => 'b', 'gone' => 'c' ), 'settings' => array( 'x' => '1' ) ) );
        $now = array( 'kinds' => array( 'classes' => array( 'folder' => 'a', 'article' => 'B', 'new' => 'd' ), 'roles' => array( 'Editor' => 'e' ) ) );
        $this->assertSame( array( 'classes' => array( 'added' => array( 'new' ), 'changed' => array( 'article' ), 'removed' => array( 'gone' ) ) ),
                           expMigrationDiff::compare( $saved, $now ) );
    }

    /** DM-04 */
    public function testOrder()
    {
        $diff = array( 'classes' => array( 'added' => array( 'news' ), 'changed' => array(), 'removed' => array( 'old' ) ),
                       'content' => array( 'added' => array( 'child', 'parent' ), 'changed' => array(), 'removed' => array( 'r1', 'r2', 'r3' ) ),
                       'languages' => array( 'added' => array( 'nor-NO' ), 'changed' => array(), 'removed' => array() ) );
        $saved = array( 'parents' => array( 'r1' => '', 'r2' => 'r1', 'r3' => 'kept' ) );
        $now = array( 'depths' => array( 'child' => 3, 'parent' => 2 ) );
        $order = array_map( function ( $t ) { return $t['kind'] . ' ' . $t['key'] . ' ' . $t['change']; }, expMigrationDiff::order( $diff, $saved, $now ) );
        $this->assertSame( array( 'languages nor-NO added', 'classes news added', 'content parent added', 'content child added',
                                  'content r1 removed', 'content r3 removed', 'classes old removed' ), $order );
    }

    /** DM-05 */
    public function testSnapshotOfTwoSqliteFiles()
    {
        if ( !class_exists( 'SQLite3' ) )
            $this->markTestSkipped( 'The SQLite3 extension is not loaded' );
        $a = $this->database( 'a.sqlite', false );
        $b = $this->database( 'b.sqlite', true );
        $kinds = array( 'classes', 'roles', 'sections', 'states', 'languages', 'content' );
        $sa = expMigrationDiff::snapshot( expMigrationDiff::queryOf( $a ), $kinds, array( 'subtree' => 'node-root' ) );
        $sb = expMigrationDiff::snapshot( expMigrationDiff::queryOf( $b ), $kinds, array( 'subtree' => 'node-root' ) );
        $this->assertSame( array(), $sa['errors'] );
        $this->assertSame( array(), $sb['errors'] );
        $diff = expMigrationDiff::compare( $sa, $sb );
        $this->assertSame( array( 'added' => array(), 'changed' => array( 'folder' ), 'removed' => array() ), $diff['classes'] );
        $this->assertSame( array( 'added' => array(), 'changed' => array(), 'removed' => array( 'Editor' ) ), $diff['roles'] );
        $this->assertSame( array( 'added' => array( 'news' ), 'changed' => array( 'standard' ), 'removed' => array() ), $diff['sections'] );
        $this->assertSame( array( 'added' => array(), 'changed' => array( 'lock' ), 'removed' => array() ), $diff['states'] );
        $this->assertSame( array( 'added' => array( 'nor-NO' ), 'changed' => array(), 'removed' => array() ), $diff['languages'] );
        $this->assertSame( array( 'added' => array(), 'changed' => array( 'obj-child' ), 'removed' => array( 'obj-gone' ) ), $diff['content'] );
        $this->assertSame( 'obj-root', $sa['parents']['obj-gone'] );
        $this->assertSame( 3, $sb['depths']['obj-child'] );
        $this->assertSame( array( 'content' ), expMigrationDiff::snapshot( expMigrationDiff::queryOf( $a ), array( 'content' ), array( 'subtree' => 'no-such-node' ) )['missing'] );
        $few = expMigrationDiff::snapshot( expMigrationDiff::queryOf( $a ), array( 'content' ), array( 'subtree' => 'node-root', 'limit' => 2 ) );
        $this->assertStringContainsString( 'more than 2 (--max-objects)', $few['errors'][0] );
        $a->close();
        $b->close();
    }

    /** DM-06 */
    public function testSettings()
    {
        $dir = $this->dir();
        mkdir( "$dir/before" );
        mkdir( "$dir/after" );
        file_put_contents( "$dir/before/site.ini.append.php", "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=A\n\n[DatabaseSettings]\nPassword=one\n*/ ?>\n" );
        file_put_contents( "$dir/after/site.ini.append.php", "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=B\n\n[DatabaseSettings]\nPassword=two\n*/ ?>\n" );
        file_put_contents( "$dir/after/notes.txt", 'not a settings file' );
        $before = expMigrationDiff::settings( "$dir/before" );
        $after = expMigrationDiff::settings( "$dir/after" );
        $this->assertSame( array( 'site.ini [SiteSettings] SiteName' ), array_keys( $before ) );
        $this->assertNotSame( $before['site.ini [SiteSettings] SiteName'], $after['site.ini [SiteSettings] SiteName'] );
        $this->assertSame( array( 'site.ini [SiteSettings] SiteName' => 'B' ), expMigrationDiff::settingValues( "$dir/after" ) );
    }

    /** DM-07 */
    public function testDumpsAndRemoteSnapshots()
    {
        $dir = $this->dir();
        file_put_contents( "$dir/pg.sql", "--\n-- PostgreSQL database dump\n--\nCREATE TABLE ezsection (id integer);\n" );
        file_put_contents( "$dir/my.sql", "-- MySQL dump 10.13\nCREATE TABLE `ezsection` (`id` int) ENGINE=InnoDB;\n" );
        file_put_contents( "$dir/lite.sql", "PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\nCREATE TABLE ezsection (id integer);\nCOMMIT;\n" );
        file_put_contents( "$dir/other.txt", "hello\n" );
        $this->assertSame( 'postgresql', expMigrationDiff::dumpEngine( "$dir/pg.sql" ) );
        $this->assertSame( 'mysql', expMigrationDiff::dumpEngine( "$dir/my.sql" ) );
        $this->assertSame( 'sqlite-sql', expMigrationDiff::dumpEngine( "$dir/lite.sql" ) );
        $this->assertSame( '', expMigrationDiff::dumpEngine( "$dir/other.txt" ) );
        $clean = expMigrationDiff::cleanSnapshot( array( 'format' => expMigrationDiff::FORMAT, 'kinds' => array( 'classes' => array( 'folder' => 'abc', 'bad' => array( 1 ) ),
                                                                                                                'roles' => array(), 'evil' => array( 'x' => 'y' ) ),
                                                         'parents' => array( 'o1' => 'o0' ), 'depths' => array( 'o1' => '3' ), 'missing' => array( 'settings', 'nope' ),
                                                         'errors' => array( 'boom' ), 'extra' => 'dropped' ) );
        $this->assertSame( array( 'format' => expMigrationDiff::FORMAT, 'kinds' => array( 'classes' => array( 'folder' => 'abc' ), 'roles' => array() ),
                                  'parents' => array( 'o1' => 'o0' ), 'depths' => array( 'o1' => 3 ), 'missing' => array( 'settings' ),
                                  'errors' => array( 'the other installation: boom' ) ), $clean );
        $this->assertSame( 'https://staging.example.com:8443/base', expMigrationDiff::redactUrl( 'https://user:secret@staging.example.com:8443/base?token=x' ) );
        $this->assertSame( '(an address)', expMigrationDiff::redactUrl( 'not a url' ) );
    }

    /**
     * A SQLite file with the tables a snapshot reads and a little data; $changed makes the known differences.
     *
     * @return SQLite3
     */
    private function database( $name, $changed )
    {
        $db = new SQLite3( $this->dir() . '/' . $name );
        $db->exec( 'CREATE TABLE ezcontentclass (id INTEGER, version INTEGER, identifier TEXT, contentobject_name TEXT, url_alias_name TEXT, is_container INTEGER,
                        always_available INTEGER, sort_field INTEGER, sort_order INTEGER, serialized_name_list TEXT, serialized_description_list TEXT);
                    CREATE TABLE ezcontentclass_attribute (contentclass_id INTEGER, version INTEGER, identifier TEXT, data_type_string TEXT, placement INTEGER,
                        is_required INTEGER, is_searchable INTEGER, can_translate INTEGER, is_information_collector INTEGER, category TEXT,
                        serialized_name_list TEXT, serialized_description_list TEXT, serialized_data_text TEXT, data_int1 INTEGER, data_int2 INTEGER,
                        data_int3 INTEGER, data_int4 INTEGER, data_float1 REAL, data_float2 REAL, data_float3 REAL, data_float4 REAL, data_text1 TEXT,
                        data_text2 TEXT, data_text3 TEXT, data_text4 TEXT, data_text5 TEXT);
                    CREATE TABLE ezcontentclass_classgroup (contentclass_id INTEGER, contentclass_version INTEGER, group_id INTEGER, group_name TEXT);
                    CREATE TABLE ezrole (id INTEGER, name TEXT, version INTEGER);
                    CREATE TABLE ezpolicy (id INTEGER, role_id INTEGER, module_name TEXT, function_name TEXT);
                    CREATE TABLE ezpolicy_limitation (id INTEGER, policy_id INTEGER, identifier TEXT);
                    CREATE TABLE ezpolicy_limitation_value (id INTEGER, limitation_id INTEGER, value TEXT);
                    CREATE TABLE ezsection (id INTEGER, identifier TEXT, name TEXT, navigation_part_identifier TEXT);
                    CREATE TABLE ezcobj_state_group (id INTEGER, identifier TEXT);
                    CREATE TABLE ezcobj_state_group_language (contentobject_state_group_id INTEGER, name TEXT, description TEXT);
                    CREATE TABLE ezcobj_state (id INTEGER, group_id INTEGER, identifier TEXT, priority INTEGER);
                    CREATE TABLE ezcobj_state_language (contentobject_state_id INTEGER, name TEXT, description TEXT);
                    CREATE TABLE ezcontent_language (id INTEGER, locale TEXT, name TEXT);
                    CREATE TABLE ezcontentobject_tree (node_id INTEGER, parent_node_id INTEGER, contentobject_id INTEGER, path_string TEXT, depth INTEGER,
                        main_node_id INTEGER, remote_id TEXT);
                    CREATE TABLE ezcontentobject (id INTEGER, remote_id TEXT, modified INTEGER, current_version INTEGER, section_id INTEGER, status INTEGER);' );
        $db->exec( "INSERT INTO ezcontentclass VALUES (1, 0, 'folder', '" . ( $changed ? '<title> (changed)' : '<title>' ) . "', '', 1, 1, 1, 1, 'a:0:{}', 'a:0:{}');
                    INSERT INTO ezcontentclass VALUES (2, 0, 'article', '<title>', '', 0, 0, 1, 1, 'a:0:{}', 'a:0:{}');
                    INSERT INTO ezcontentclass VALUES (3, 1, 'folder', 'a draft', '', 1, 1, 1, 1, 'a:0:{}', 'a:0:{}');
                    INSERT INTO ezcontentclass_attribute (contentclass_id, version, identifier, data_type_string, placement, data_float1) VALUES (1, 0, 'title', 'ezstring', 1, " . ( $changed ? "'0.0'" : '0' ) . ");
                    INSERT INTO ezcontentclass_classgroup VALUES (1, 0, 1, 'Content');
                    INSERT INTO ezrole VALUES (1, 'Anonymous', 0);
                    INSERT INTO ezpolicy VALUES (" . ( $changed ? '11' : '1' ) . ", 1, 'content', 'read');
                    INSERT INTO ezpolicy_limitation VALUES (" . ( $changed ? '21, 11' : '2, 1' ) . ", 'Section');
                    INSERT INTO ezpolicy_limitation_value VALUES (3, " . ( $changed ? '21' : '2' ) . ", '1');
                    INSERT INTO ezsection VALUES (1, 'standard', '" . ( $changed ? 'Standard (changed)' : 'Standard' ) . "', 'ezcontentnavigationpart');
                    INSERT INTO ezsection VALUES (2, 'media', 'Media', 'ezmedianavigationpart');
                    INSERT INTO ezcobj_state_group VALUES (1, 'lock');
                    INSERT INTO ezcobj_state_group_language VALUES (1, 'Lock', '');
                    INSERT INTO ezcobj_state VALUES (1, 1, 'not_locked', 0);
                    INSERT INTO ezcobj_state VALUES (2, 1, 'locked', " . ( $changed ? '2' : '1' ) . ");
                    INSERT INTO ezcontent_language VALUES (2, 'eng-GB', 'English (United Kingdom)');
                    INSERT INTO ezcontentobject_tree VALUES (1, 1, 0, '/1/', 1, 1, 'top');
                    INSERT INTO ezcontentobject_tree VALUES (2, 1, 10, '/1/2/', 2, 2, 'node-root');
                    INSERT INTO ezcontentobject_tree VALUES (3, 2, 11, '/1/2/3/', 3, 3, 'node-child');
                    INSERT INTO ezcontentobject VALUES (10, 'obj-root', 100, 1, 1, 1);
                    INSERT INTO ezcontentobject VALUES (11, 'obj-child', " . ( $changed ? '200' : '100' ) . ", 1, 1, 1);" );
        if ( $changed )
            $db->exec( "INSERT INTO ezsection VALUES (3, 'news', 'News', 'ezcontentnavigationpart');
                        INSERT INTO ezcontent_language VALUES (4, 'nor-NO', 'Norsk');" );
        else
            $db->exec( "INSERT INTO ezrole VALUES (2, 'Editor', 0);
                        INSERT INTO ezcontentobject_tree VALUES (4, 2, 12, '/1/2/4/', 3, 4, 'node-gone');
                        INSERT INTO ezcontentobject VALUES (12, 'obj-gone', 100, 1, 1, 1);" );
        return $db;
    }
}

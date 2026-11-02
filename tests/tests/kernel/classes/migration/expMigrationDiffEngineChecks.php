<?php
/**
 * The checks of exp:migration:generate --from-diff on a real database, for every engine: the saved state is a SQLite
 * file with the kernel schema and the clean data (what the installation was), the installation is the test's
 * database with the clean data and known changes made through the kernel's classes.
 *
 *  DF-1 a dump (the SQLite file): a class changed, a section changed and one added, a role removed, a language added,
 *       an object of the subtree changed; the generated migration has a step for each and none for what is the same
 *  DF-2 a caption (Sicherungspunkt) holding the same file and a copy of settings/override: the settings set, added and
 *       removed since
 *  DF-3 no differences: nothing generated, exit 0; an unknown source: a usage error
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRecordingEngineChecks.php';

trait expMigrationDiffEngineChecks
{
    use expMigrationRecordingEngineChecks;

    /** The saved state: a SQLite file with the kernel schema and the clean data. */
    protected function savedDatabase()
    {
        $file = $this->runnerDir() . '/saved.sqlite';
        if ( is_file( $file ) )
            return $file;
        $saved = new eZSQLite3DB( array( 'server' => '', 'port' => false, 'user' => '', 'password' => '', 'database' => $file,
                                         'use_slave_server' => false, 'slave_server' => '', 'slave_port' => false, 'slave_user' => '',
                                         'slave_password' => '', 'slave_database' => '', 'socket' => false, 'charset' => 'utf-8',
                                         'is_internal_charset' => false, 'builtin_encoding' => false, 'connect_retries' => 1,
                                         'use_persistent_connection' => false, 'show_errors' => false ) );
        foreach ( array( 'schema.sql', 'cleandata.sql' ) as $sql )
            $this->assertNotFalse( $saved->insertFile( self::runnerRoot() . '/kernel/sql/sqlite', $sql, false ), "kernel/sql/sqlite/$sql loads into the saved state" );
        $saved->query( 'PRAGMA wal_checkpoint(TRUNCATE)' );
        $saved->close();
        return $file;
    }

    /** The known changes since the saved state. */
    protected function changeInstallation()
    {
        $class = eZContentClass::fetchByIdentifier( 'folder' );
        $class->setAttribute( 'contentobject_name', '<short_name|name> (changed)' );
        $class->store();
        $section = eZSection::fetchByIdentifier( 'standard' );
        $section->setAttribute( 'name', 'Standard (changed)' );
        $section->store();
        $added = new eZSection( array( 'name' => 'Diff news', 'identifier' => 'diff_news', 'navigation_part_identifier' => 'ezcontentnavigationpart' ) );
        $added->store();
        $role = eZRole::fetchByName( 'Editor' );
        $role->removeThis();
        eZContentLanguage::addLanguage( 'nor-NO', 'Norsk' );
        $node = eZContentObjectTreeNode::fetch( 2 );
        $object = $node->object();
        $object->setAttribute( 'modified', (int)$object->attribute( 'modified' ) + 60 );
        $object->store();
        eZContentObject::clearCache();
    }

    /** DF-1 */
    protected function checkDiffWithDump( $db )
    {
        $saved = $this->savedDatabase();
        $this->assertSame( 'sqlite', expMigrationDiff::dumpEngine( $saved ) );
        $this->changeInstallation();
        $report = expMigrationDiff::fromDiff( $saved, array( 'compare' => 'classes,roles,sections,languages,content', 'subtree' => '2', 'name' => 'diff_check',
                                                             'root' => $this->runnerDir() ) );
        $this->assertSame( expUpgrade::EXIT_OK, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'kind' => 'dump', 'name' => 'saved.sqlite' ), $report['source'] );
        $this->assertSame( array( 'classes', 'roles', 'sections', 'languages', 'content' ), $report['compared'] );
        $this->assertContains( 'class folder', array_column( $report['steps'], 'subject' ) );
        $this->assertSame( 1, $report['diff']['sections']['added'] );
        $this->assertSame( 1, $report['diff']['roles']['removed'] );
        $this->assertSame( 1, $report['diff']['languages']['added'] );
        $content = $report['generated']['content'];
        foreach ( array( "type: content_class\n      mode: ensure\n      identifier: folder\n", "type: section\n      mode: ensure\n      identifier: standard\n",
                         "type: section\n      mode: ensure\n      identifier: diff_news\n", "type: role\n      mode: delete\n      role: Editor\n",
                         "type: language\n      mode: ensure\n      locale: nor-NO\n", "type: content\n      mode: ensure\n" ) as $text )
            $this->assertStringContainsString( $text, $content );
        foreach ( array( 'identifier: media', 'name: Anonymous', 'identifier: user_group' ) as $same )
            $this->assertStringNotContainsString( $same, $content, "$same did not change" );
        $this->assertStringContainsString( 'Generated from the difference to the dump saved.sqlite', $content );
        $this->assertFalse( $report['written'] );
        // the first step the migration makes is the language (before classes and content), the removals come last
        $types = array_map( function ( $s ) { return $s['type'] . '/' . $s['mode']; }, $report['steps'] ? array_column( $report['steps'], 'step' ) : array() );
        $this->assertSame( 'language/ensure', $types[0] );
        $this->assertSame( 'role/delete', end( $types ) );
        $this->assertFileDoesNotExist( $this->runnerDir() . '/var/tmp', 'the throwaway files are gone' );
    }

    /** DF-2 */
    protected function checkDiffWithCaption( $db )
    {
        $saved = $this->savedDatabase();
        $root = $this->runnerDir();
        $caption = "$root/captions/20261102-101500";
        mkdir( "$caption/upgrade/settings/override", 0700, true );
        copy( $saved, "$caption/database.sqlite" );
        file_put_contents( "$caption/manifest.json", json_encode( array( 'engine' => 'sqlite', 'created' => date( 'c' ) ) ) );
        file_put_contents( "$caption/upgrade/settings/override/diffcheck.ini.append.php",
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=Before\nKept=same\n\n[GoneSettings]\nValue=1\n*/ ?>\n" );
        mkdir( "$root/settings/override", 0700, true );
        file_put_contents( "$root/settings/override/diffcheck.ini.append.php",
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=After\nKept=same\n\n[AddedSettings]\nList[]\nList[]=one\nPassword=never-in-a-migration\n*/ ?>\n" );
        $report = expMigrationDiff::fromDiff( '20261102-101500', array( 'compare' => 'settings,states', 'backupDir' => "$root/captions", 'root' => $root, 'name' => 'diff_caption' ) );
        $this->assertSame( expUpgrade::EXIT_OK, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'kind' => 'caption', 'name' => '20261102-101500' ), $report['source'] );
        $this->assertSame( array( 'added' => 1, 'changed' => 1, 'removed' => 1 ), $report['diff']['settings'] );
        $this->assertSame( array( 'added' => 0, 'changed' => 0, 'removed' => 0 ), $report['diff']['states'] );
        $content = $report['generated']['content'];
        $this->assertStringContainsString( "file: diffcheck.ini\n      set: { SiteSettings: { SiteName: After } }\n", $content );
        $this->assertStringContainsString( "AddedSettings: { List: [ one ] }\n", $content );
        $this->assertStringContainsString( "remove: { GoneSettings: [ Value ] }\n", $content );
        $this->assertStringNotContainsString( 'Kept', $content );
        $this->assertStringNotContainsString( 'never-in-a-migration', $content );
        $this->assertStringNotContainsString( 'Password', $content );
    }

    /** DF-3 */
    protected function checkNoDifference( $db )
    {
        $saved = $this->savedDatabase();
        $report = expMigrationDiff::fromDiff( $saved, array( 'compare' => 'states', 'root' => $this->runnerDir() ) );
        $this->assertSame( expUpgrade::EXIT_OK, $report['exit'], implode( '; ', $report['errors'] ) );
        $this->assertSame( array( 'added' => 0, 'changed' => 0, 'removed' => 0 ), $report['diff']['states'] );
        $this->assertNull( $report['generated'] );
        $this->assertContains( 'no differences: nothing to generate', $report['warnings'] );
        $missing = expMigrationDiff::fromDiff( 'no-such-caption-or-file', array( 'root' => $this->runnerDir() ) );
        $this->assertSame( expUpgrade::EXIT_USAGE, $missing['exit'] );
        $this->assertStringStartsWith( 'no caption, address or dump no-such-caption-or-file', $missing['errors'][0] );
    }
}

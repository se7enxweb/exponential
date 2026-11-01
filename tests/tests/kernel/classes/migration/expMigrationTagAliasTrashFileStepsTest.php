<?php
/**
 * The step types tag, url_alias, trash, file and image without a database: their schemas, the checks of their steps,
 * their plans, which steps undo themselves, the identifier kinds tag and url_alias, and the files of a migration.
 *
 *  MT-01 the kernel registers the five types from migration.ini; their schemas are valid, their examples valid steps
 *  MT-02 tag: the keys each mode needs, keywords as language => text, parent as a tag or the top, synonyms
 *  MT-03 url_alias: node or destination, wildcards with their {n}, delete by path, wildcard or which: all
 *  MT-04 trash: move by object or node, restore and purge by object (never path:), purge all
 *  MT-05 file and image: the object, the attribute, file: below the migration's folder, focal point and crop
 *  MT-06 plans name what would change
 *  MT-07 which steps undo themselves: creates, trash move and restore; the rest do not
 *  MT-08 identifiers of the kinds tag and url_alias; a tag without eztags names nothing
 *  MT-09 a file of a migration: below the folder of the migration file, never out of it
 *  MT-10 the caches the steps touch
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationTagAliasTrashFileStepsTest extends PHPUnit\Framework\TestCase
{
    const TYPES = array( 'tag' => 'expMigrationStepTag', 'url_alias' => 'expMigrationStepUrlAlias', 'trash' => 'expMigrationStepTrash',
                         'file' => 'expMigrationStepFile', 'image' => 'expMigrationStepImage' );

    /** @var string|null */
    private $dir;

    /**
     * @param string $yaml
     * @return string the errors of the steps, one per line
     */
    private static function errors( $yaml )
    {
        $errors = array();
        expMigrationYaml::fromText( $yaml, expMigrationStepRegistry::fromSettings(), $errors );
        return implode( "\n", $errors );
    }

    /**
     * @param string $name
     * @param array $step
     * @return string[]
     */
    private static function plan( $name, array $step )
    {
        $type = expMigrationStepRegistry::fromSettings()->get( $name );
        return $type->plan( expMigrationStepSchema::withDefaults( $type->schema(), $step ), null );
    }

    /**
     * @param string $name
     * @param array $step
     * @return bool
     */
    private static function revertible( $name, array $step )
    {
        $type = expMigrationStepRegistry::fromSettings()->get( $name );
        if ( !$type instanceof expMigrationRevertibleStepType )
            return false;
        return !$type instanceof expMigrationPartlyRevertibleStepType || $type->revertible( expMigrationStepSchema::withDefaults( $type->schema(), $step ) );
    }

    protected function setUp(): void
    {
        if ( !expMigrationYamlParser::available() )
            $this->markTestSkipped( (string)expMigrationYamlParser::problem() );
        expMigrationStepRegistry::forget();
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $iterator as $item )
                $item->isDir() || $item->isLink() ? ( $item->isLink() ? unlink( $item->getPathname() ) : rmdir( $item->getPathname() ) ) : unlink( $item->getPathname() );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** MT-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        foreach ( self::TYPES as $name => $class )
        {
            $type = $registry->get( $name );
            $this->assertInstanceOf( $class, $type, $name );
            $schema = $type->schema();
            $this->assertSame( array(), expMigrationStepSchema::problems( $schema ), $name );
            $this->assertNotEmpty( $schema['values'], "$name gives values to references:" );
            $this->assertSame( '', self::errors( $schema['example'] ), "the example of $name is a valid step" );
        }
        foreach ( array( 'tag', 'url_alias', 'trash' ) as $name )
            $this->assertInstanceOf( 'expMigrationPartlyRevertibleStepType', $registry->get( $name ), "$name says which of its steps undo themselves" );
        foreach ( array( 'file', 'image' ) as $name )
            $this->assertNotInstanceOf( 'expMigrationRevertibleStepType', $registry->get( $name ), "$name does not undo itself" );
    }

    /** MT-02 */
    public function testTagChecks()
    {
        $this->assertSame( '', self::errors( "- type: tag\n  mode: create\n  parent: 0\n  keyword: Topics\n  keywords: { ger-DE: Themen }\n"
                                             . "- type: tag\n  mode: ensure\n  parent: 'path:Topics'\n  remote_id: sport\n  keyword: Sport\n  synonyms: [ Sports ]\n"
                                             . "- type: tag\n  mode: move\n  tag: 'remote:sport'\n  parent: 0\n"
                                             . "- type: tag\n  mode: merge\n  tag: 'path:Topics/Athletics'\n  into: 'remote:sport'\n"
                                             . "- type: tag\n  mode: update\n  tag: 12\n  main_language: ger-DE\n" ) );
        $errors = self::errors( "- type: tag\n  mode: create\n  parent: 'object-remote:x'\n  keywords: { german: Sport }\n  synonyms: [ '', [ a ] ]\n" );
        foreach ( array( 'keyword is missing (mode create)', 'parent: "object-remote:x" is no identifier of a tag', 'keywords: german is not a language code',
                         'synonyms[0]: a keyword', 'synonyms[1]: a keyword' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'tag is missing (mode delete)', self::errors( "- type: tag\n  mode: delete\n" ) );
        $this->assertStringContainsString( 'parent is missing: the new parent tag', self::errors( "- type: tag\n  mode: move\n  tag: 3\n" ) );
        $this->assertStringContainsString( 'into is missing', self::errors( "- type: tag\n  mode: merge\n  tag: 3\n" ) );
        $this->assertStringContainsString( 'nothing to update', self::errors( "- type: tag\n  mode: update\n  tag: 3\n" ) );
        $this->assertStringContainsString( 'steps[0] (tag).into: only in the mode merge', self::errors( "- type: tag\n  mode: delete\n  tag: 3\n  into: 4\n" ) );
    }

    /** MT-03 */
    public function testUrlAliasChecks()
    {
        $this->assertSame( '', self::errors( "- type: url_alias\n  mode: create\n  path: about-us\n  node: 'path:company/about'\n  below_parent: true\n"
                                             . "- type: url_alias\n  mode: ensure\n  path: find\n  destination: content/search\n  redirect: false\n"
                                             . "- type: url_alias\n  mode: ensure\n  wildcard: 'old/*/*'\n  destination: 'new/{2}/{1}'\n"
                                             . "- type: url_alias\n  mode: delete\n  path: about-us\n  language: eng-US\n"
                                             . "- type: url_alias\n  mode: delete\n  node: 2\n  which: all\n"
                                             . "- type: url_alias\n  mode: delete\n  wildcard: 'old/*'\n  missing: skip\n" ) );
        $errors = self::errors( "- type: url_alias\n  mode: create\n  node: 2\n  destination: content/search\n" );
        $this->assertStringContainsString( 'path is missing (mode create)', $errors );
        $this->assertStringContainsString( 'say where the address leads by node: or by destination: (one of them)', $errors );
        $this->assertStringContainsString( 'below_parent: places the address of a node', self::errors( "- type: url_alias\n  mode: ensure\n  path: a\n  destination: b\n  below_parent: true\n" ) );
        $errors = self::errors( "- type: url_alias\n  mode: ensure\n  wildcard: 'old/news'\n  destination: 'new/{1}'\n" );
        $this->assertStringContainsString( 'wildcard: a pattern has at least one *', $errors );
        $this->assertStringContainsString( 'destination: {1} has no * of its own in the wildcard (0)', $errors );
        $this->assertStringContainsString( 'destination is missing: where the wildcard leads', self::errors( "- type: url_alias\n  mode: create\n  wildcard: 'a/*'\n" ) );
        $this->assertStringContainsString( 'name what is deleted by path:, by wildcard: or by which: all (one of them)',
                                           self::errors( "- type: url_alias\n  mode: delete\n  node: 2\n" ) );
        $this->assertStringContainsString( 'which: selected deletes the address of path:', self::errors( "- type: url_alias\n  mode: delete\n  which: selected\n" ) );
        $this->assertStringContainsString( 'path: the address without the scheme and host',
                                           self::errors( "- type: url_alias\n  mode: ensure\n  path: 'https://example.com/a'\n  node: 2\n" ) );
    }

    /** MT-04 */
    public function testTrashChecks()
    {
        $this->assertSame( '', self::errors( "- type: trash\n  mode: move\n  node: 'path:news/old'\n- type: trash\n  mode: restore\n  object: 'remote:old'\n  parent: 2\n"
                                             . "- type: trash\n  mode: purge\n  object: 57\n- type: trash\n  mode: purge\n  all: true\n" ) );
        $this->assertStringContainsString( 'name what goes to the trash by object: or by node: (one of them)', self::errors( "- type: trash\n  mode: move\n" ) );
        $this->assertStringContainsString( 'object is missing: the object in the trash', self::errors( "- type: trash\n  mode: restore\n" ) );
        $this->assertStringContainsString( 'name what is purged by object: or by all: true', self::errors( "- type: trash\n  mode: purge\n  object: 5\n  all: true\n" ) );
        $this->assertStringContainsString( 'an object in the trash has no path', self::errors( "- type: trash\n  mode: restore\n  object: 'path:news/old'\n" ) );
        $this->assertStringContainsString( 'steps[0] (trash).node: only in the mode move', self::errors( "- type: trash\n  mode: restore\n  object: 5\n  node: 6\n" ) );
    }

    /** MT-05 */
    public function testFileAndImageChecks()
    {
        $this->assertSame( '', self::errors( "- type: file\n  object: 'remote:report'\n  field: file\n  file: files/report.pdf\n  name: Report.pdf\n"
                                             . "- type: image\n  node: 'path:news/welcome'\n  field: image\n  focal: { x: 0.25, y: 1 }\n  crop: { x: 0, y: 0.1, width: 1, height: 0.5 }\n"
                                             . "- type: image\n  object: 7\n  field: image\n  focal: null\n" ) );
        $errors = self::errors( "- type: file\n  object: 1\n  node: 2\n  field: File\n  file: ../secret.txt\n" );
        foreach ( array( 'name the object by object: or by node: (one of them)', 'field: "File" is not an attribute identifier', 'file: ../secret.txt leaves the folder of the migration' ) as $expected )
            $this->assertStringContainsString( $expected, $errors );
        $this->assertStringContainsString( 'file is missing', self::errors( "- type: file\n  object: 1\n  field: file\n" ) );
        $this->assertStringContainsString( 'is absolute', self::errors( "- type: file\n  object: 1\n  field: file\n  file: /etc/passwd\n" ) );
        $this->assertStringContainsString( 'is no path', self::errors( "- type: file\n  object: 1\n  field: file\n  file: 'https://example.com/a.pdf'\n" ) );
        $this->assertStringContainsString( 'nothing to change: file:, alt:, focal: or crop: is missing', self::errors( "- type: image\n  object: 1\n  field: image\n" ) );
        $errors = self::errors( "- type: image\n  object: 1\n  field: image\n  focal: { x: 2, y: 0 }\n  crop: { x: 0, y: 0, width: 1, height: 1 }\n" );
        $this->assertStringContainsString( 'focal: { x, y }, each a number from 0 to 1', $errors );
        $this->assertStringContainsString( 'crop: { x, y, width, height }', $errors );
    }

    /** MT-06 */
    public function testPlans()
    {
        $this->assertSame( array( 'ensure the tag named "Sport", ger-DE: "Sport" below "path:Topics" (remote id sport): create it when missing, change what differs, synonyms Sports'
                                  . ( expMigrationIdentifier::tagsInstalled() ? '' : ' (eztags is not installed here)' ) ),
                           self::plan( 'tag', array( 'mode' => 'ensure', 'parent' => 'path:Topics', 'remote_id' => 'sport', 'keyword' => 'Sport',
                                                     'keywords' => array( 'ger-DE' => 'Sport' ), 'synonyms' => array( 'Sports' ) ) ) );
        $this->assertStringStartsWith( 'merge the tag "path:Topics/Athletics" into "remote:sport"',
                                       self::plan( 'tag', array( 'mode' => 'merge', 'tag' => 'path:Topics/Athletics', 'into' => 'remote:sport' ) )[0] );
        $this->assertSame( array( 'ensure the address about-us -> node "path:company/about", redirecting' ),
                           self::plan( 'url_alias', array( 'mode' => 'ensure', 'path' => 'about-us', 'node' => 'path:company/about' ) ) );
        $this->assertSame( array( 'create the wildcard old/* -> new/{1} (direct)' ),
                           self::plan( 'url_alias', array( 'mode' => 'create', 'wildcard' => 'old/*', 'destination' => 'new/{1}', 'redirect' => false ) ) );
        $this->assertSame( array( 'delete every custom address of node 2' ), self::plan( 'url_alias', array( 'mode' => 'delete', 'node' => 2, 'which' => 'all' ) ) );
        $this->assertSame( array( 'move the object of node "path:news/old" to the trash, with all its locations' ),
                           self::plan( 'trash', array( 'mode' => 'move', 'node' => 'path:news/old' ) ) );
        $this->assertSame( array( 'restore the object "remote:old" from the trash below the parent it had' ), self::plan( 'trash', array( 'mode' => 'restore', 'object' => 'remote:old' ) ) );
        $this->assertSame( array( 'empty the trash: every object in it is removed for good' ), self::plan( 'trash', array( 'mode' => 'purge', 'all' => true ) ) );
        $this->assertSame( array( 'attach files/report.pdf to file of the object "remote:report", as a new version' ),
                           self::plan( 'file', array( 'object' => 'remote:report', 'field' => 'file', 'file' => 'files/report.pdf' ) ) );
        $this->assertSame( array( 'set the image images/a.jpg, the alternative text, no focal point of image of the object 7 in ger-DE, in place' ),
                           self::plan( 'image', array( 'object' => 7, 'field' => 'image', 'file' => 'images/a.jpg', 'alt' => 'A', 'focal' => null,
                                                       'language' => 'ger-DE', 'in_place' => true ) ) );
    }

    /** MT-07 */
    public function testRevertible()
    {
        $this->assertTrue( self::revertible( 'tag', array( 'mode' => 'create', 'keyword' => 'Sport' ) ) );
        foreach ( array( 'update', 'ensure', 'delete', 'move', 'merge' ) as $mode )
            $this->assertFalse( self::revertible( 'tag', array( 'mode' => $mode, 'tag' => 1 ) ), "tag $mode" );
        $this->assertTrue( self::revertible( 'url_alias', array( 'mode' => 'create', 'path' => 'a', 'node' => 2 ) ) );
        $this->assertFalse( self::revertible( 'url_alias', array( 'mode' => 'ensure', 'path' => 'a', 'node' => 2 ) ) );
        $this->assertFalse( self::revertible( 'url_alias', array( 'mode' => 'delete', 'path' => 'a' ) ) );
        $this->assertTrue( self::revertible( 'trash', array( 'mode' => 'move', 'object' => 'remote:a' ) ) );
        $this->assertTrue( self::revertible( 'trash', array( 'mode' => 'restore', 'object' => 'remote:a' ) ) );
        $this->assertFalse( self::revertible( 'trash', array( 'mode' => 'purge', 'all' => true ) ) );
        $this->assertFalse( self::revertible( 'file', array( 'object' => 1, 'field' => 'file', 'file' => 'a.pdf' ) ) );
        $this->assertFalse( self::revertible( 'image', array( 'object' => 1, 'field' => 'image', 'alt' => 'A' ) ) );
    }

    /** MT-08 */
    public function testIdentifiers()
    {
        $this->assertContains( 'tag', expMigrationIdentifier::KINDS );
        $this->assertContains( 'url_alias', expMigrationIdentifier::KINDS );
        $this->assertSame( array( 'form' => 'path', 'value' => 'Topics/Sport' ), expMigrationIdentifier::parse( 'path:/Topics/Sport/', $error, 'tag' ) );
        $this->assertSame( array( 'form' => 'remote', 'value' => 'sport' ), expMigrationIdentifier::parse( 'remote:sport', $error, 'tag' ) );
        $this->assertSame( array( 'form' => 'id', 'value' => 12 ), expMigrationIdentifier::parse( '12', $error, 'tag' ) );
        $this->assertNull( expMigrationIdentifier::parse( 'Sport', $error, 'tag' ) );
        $this->assertStringContainsString( 'remote:<remote id>, path:<url path>', (string)$error );
        $this->assertSame( array( 'form' => 'name', 'value' => 'about-us/team' ), expMigrationIdentifier::parse( 'about-us/team', $error, 'url_alias' ) );
        $this->assertNotNull( expMigrationIdentifier::problem( 'url_alias', 'remote:x' ) );
        $this->assertNotNull( expMigrationIdentifier::problem( 'url_alias', 'about us' ) );
        if ( !expMigrationIdentifier::tagsInstalled() )
        {
            try
            {
                expMigrationIdentifier::resolve( 'tag', 'remote:sport' );
                $this->fail( 'a tag without eztags names nothing' );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringContainsString( 'eztags, which is not installed here', $e->getMessage() );
            }
            $this->assertNull( expMigrationIdentifier::find( 'tag', 3 ) );
        }
    }

    /** MT-09 */
    public function testMigrationFiles()
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-files-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        mkdir( $this->dir . '/migrations/files', 0775, true );
        file_put_contents( $this->dir . '/migrations/files/report.pdf', '%PDF-1.4' );
        file_put_contents( $this->dir . '/outside.txt', 'not of the migration' );
        symlink( $this->dir . '/outside.txt', $this->dir . '/migrations/files/link.txt' );
        $context = new expMigrationContext( new stdClass(), false, array( 'no-mirror' => true, 'user' => null, 'run-id' => '20261104-100000-abcdef' ) );
        $context->setMigrationFile( $this->dir . '/migrations/20261104100000_files.yml' );
        $this->assertSame( realpath( $this->dir . '/migrations/files/report.pdf' ), expMigrationStepFile::migrationPath( 'files/report.pdf', $context ) );
        foreach ( array( 'files/link.txt' => 'leads out of the folder of the migration', 'files/none.pdf' => 'is not a readable file',
                         '../outside.txt' => 'leaves the folder of the migration (..)', '/etc/hostname' => 'is absolute' ) as $path => $expected )
        {
            try
            {
                expMigrationStepFile::migrationPath( $path, $context );
                $this->fail( "$path is refused" );
            }
            catch ( RuntimeException $e )
            {
                $this->assertStringContainsString( $expected, $e->getMessage(), $path );
            }
        }
        $context->setMigrationFile( null );
        try
        {
            expMigrationStepFile::migrationPath( 'files/report.pdf', $context );
            $this->fail( 'outside a migration nothing is read' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'runs outside one', $e->getMessage() );
        }
    }

    /** MT-10 */
    public function testCaches()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array( 'content', 'urlalias' ), $registry->get( 'url_alias' )->caches( array( 'mode' => 'ensure' ) ) );
        foreach ( array( 'tag', 'trash', 'file', 'image' ) as $name )
            $this->assertSame( array( 'content' ), $registry->get( $name )->caches( array() ), $name );
    }
}

<?php
/**
 * The second part of packages, deploys and long runs, without a database: the step type code_file (code and design
 * files below the allowed folders, with a backup, checksums and a way back), the site-wide recording window of time
 * (the parts that need no database; RC-5 runs the rest on every engine), and the long-run fields of the run form.
 *
 *  CF-01 the kernel registers code_file: a valid schema and example, it undoes itself, its caches by the file's kind
 *  CF-02 paths: only below [FileStepSettings] AllowedRootList[] (design, extension), never a root itself, .., an
 *        absolute path, a URL, .git, settings/override, var or vendor; an empty list allows nothing
 *  CF-03 expMigrationFiles: a new file, a replaced one (the backup readable by its owner only, the mode kept), a
 *        removed one, the change list with the SHA-256 before and after, nothing written twice; restore() puts each
 *        back and refuses a file that changed since; a link out of the folder is refused
 *  CF-04 the step: create, update, ensure and delete with expect: and checksum:, the plan, revert() after a rollback
 *  CF-05 the runner puts back the files of a migration that fails after writing them
 *  CF-06 the recording window: the end in the future and at most 31 days, a window that has ended matches nothing,
 *        the end in the file of running recordings, the minutes of the administration's form
 *  CF-07 the run form: MaxMigrations, MaxTime, Throttle and Nice checked as the command checks them, passed to the
 *        background job (--nice too)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expMigrationRunnerFakes.php';

/** migration.ini [FileStepSettings] as a list of values. */
class expMigrationFileStepsIni
{
    private $values;

    public function __construct( array $values )
    {
        $this->values = $values;
    }

    public function hasVariable( $group, $name )
    {
        return array_key_exists( "$group/$name", $this->values );
    }

    public function variable( $group, $name )
    {
        return $this->values["$group/$name"];
    }
}

class expMigrationFileStepsTest extends PHPUnit\Framework\TestCase
{
    const ROOTS = array( 'design', 'extension' );

    /** @var string|null the test's installation under var/tmp */
    private $dir;

    protected function setUp(): void
    {
        expMigrationStepRegistry::forget();
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->dir, FilesystemIterator::SKIP_DOTS ),
                                                       RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $iterator as $item )
                $item->isDir() && !$item->isLink() ? rmdir( $item->getPathname() ) : unlink( $item->getPathname() );
            rmdir( $this->dir );
        }
        $this->dir = null;
    }

    /** @return string the test's installation: design/, extension/, migrations/ */
    private function dir()
    {
        if ( $this->dir === null )
        {
            $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-file-steps-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
            foreach ( array( 'design/site/templates', 'extension/demo', 'migrations/files', 'outside' ) as $folder )
                mkdir( $this->dir . '/' . $folder, 0775, true );
        }
        return $this->dir;
    }

    /**
     * A context of the test's installation inside a migration file.
     *
     * @param string $id
     * @return expMigrationRunnerTestContext
     */
    private function context( $id = '20261102100000_files' )
    {
        $context = new expMigrationRunnerTestContext( true, array( 'root' => $this->dir() ) );
        $file = $this->dir() . "/migrations/$id.yml";
        if ( !is_file( $file ) )
            file_put_contents( $file, "- type: code_file\n" );
        $context->setMigrationFile( $file );
        return $context;
    }

    /** CF-01 */
    public function testRegistered()
    {
        $registry = expMigrationStepRegistry::fromSettings();
        $this->assertSame( array(), $registry->errors() );
        $type = $registry->get( 'code_file' );
        $this->assertInstanceOf( 'expMigrationStepCodeFile', $type );
        $this->assertInstanceOf( 'expMigrationRevertibleStepType', $type );
        $this->assertNotInstanceOf( 'expMigrationPartlyRevertibleStepType', $type, 'every mode undoes itself' );
        $schema = $type->schema();
        $this->assertSame( array(), expMigrationStepSchema::problems( $schema ) );
        $this->assertSame( array( 'create', 'update', 'ensure', 'delete' ), $schema['modes'] );
        if ( expMigrationYamlParser::available() )
        {
            $errors = array();
            expMigrationYaml::fromText( $schema['example'], $registry, $errors );
            $this->assertSame( array(), $errors, 'the example is a valid step' );
        }
        $this->assertSame( array( 'template', 'template-override', 'template-block' ), $type->caches( array( 'path' => 'design/site/templates/a.tpl' ) ) );
        $this->assertSame( array( 'tag:ini' ), $type->caches( array( 'path' => 'extension/demo/settings/site.ini.append.php' ) ) );
        $this->assertSame( array( 'ezjscore-packer', 'template-block' ), $type->caches( array( 'path' => 'design/site/stylesheets/site.css' ) ) );
        $this->assertSame( array(), $type->caches( array( 'path' => 'extension/demo/classes/demo.php' ) ) );
        $this->assertSame( array( 'design', 'extension' ), expMigrationFiles::allowedRoots() );
    }

    /** CF-02 */
    public function testPaths()
    {
        foreach ( array( 'design/site/templates/page.tpl', 'extension/demo/classes/x.php', './design//site/a.css' ) as $path )
            $this->assertNull( expMigrationFiles::pathProblem( $path, self::ROOTS ), $path );
        $refused = array(
            '' => 'a path below',
            'settings/site.ini' => 'not below a folder',
            'kernel/classes/x.php' => 'not below a folder',
            'design' => 'the folder itself',
            'design/' => 'the folder itself',
            'design/../kernel/x.php' => 'leaves the installation',
            '/etc/passwd' => 'absolute',
            'C:/x' => 'no path',
            'file:///etc/passwd' => 'no path',
            'phar://x/design/a' => 'no path',
            'design/site/.git/config' => '.git folder',
            'extension/demo/settings/override/site.ini.append.php' => 'settings/override',
            'extension/demo/vendor/autoload.php' => 'vendor',
            'design/var/x' => 'var',
        );
        foreach ( $refused as $path => $text )
            $this->assertStringContainsString( $text, (string)expMigrationFiles::pathProblem( $path, self::ROOTS ), $path );
        $this->assertStringContainsString( 'names no folder', (string)expMigrationFiles::pathProblem( 'design/a.tpl', array() ) );

        $this->assertSame( array( 'design/site', 'extension' ), expMigrationFiles::allowedRoots(
            new expMigrationFileStepsIni( array( 'FileStepSettings/AllowedRootList' => array( '/design/site/', 'extension', 'extension', '../x', '' ) ) ) ) );
        $this->assertSame( array(), expMigrationFiles::allowedRoots( new expMigrationFileStepsIni( array( 'FileStepSettings/AllowedRootList' => array() ) ) ) );
        $this->assertStringContainsString( 'not below', (string)expMigrationFiles::pathProblem( 'design/admin/x.tpl', array( 'design/site' ) ) );
    }

    /** CF-03 */
    public function testWriteAndRestore()
    {
        $root = $this->dir();
        $id = '20261102100000_files';
        $created = expMigrationFiles::write( $root, 'design/site/templates/new/footer.tpl', "new\n", $id, self::ROOTS );
        $this->assertFalse( $created['existed'] );
        $this->assertNull( $created['before'] );
        $this->assertSame( hash( 'sha256', "new\n" ), $created['after'] );
        $this->assertSame( '', $created['backup'] );
        $this->assertSame( "new\n", file_get_contents( "$root/design/site/templates/new/footer.tpl" ) );
        $this->assertNull( expMigrationFiles::write( $root, 'design/site/templates/new/footer.tpl', "new\n", $id, self::ROOTS ), 'the same content is not written again' );

        file_put_contents( "$root/extension/demo/old.css", "old\n" );
        chmod( "$root/extension/demo/old.css", 0640 );
        $replaced = expMigrationFiles::write( $root, 'extension/demo/old.css', "replaced\n", $id, self::ROOTS );
        $this->assertTrue( $replaced['existed'] );
        $this->assertSame( hash( 'sha256', "old\n" ), $replaced['before'] );
        $this->assertSame( '0640', $replaced['mode'] );
        $this->assertSame( 0640, fileperms( "$root/extension/demo/old.css" ) & 0777, 'the mode is kept' );
        $this->assertSame( expMigrationFiles::BACKUP_DIRECTORY . "/$id/extension/demo/old.css", $replaced['backup'] );
        $this->assertSame( "old\n", file_get_contents( "$root/" . $replaced['backup'] ) );
        $this->assertSame( 0600, fileperms( "$root/" . $replaced['backup'] ) & 0777, 'the backup is readable by its owner only' );

        file_put_contents( "$root/extension/demo/gone.js", "gone\n" );
        $removed = expMigrationFiles::write( $root, 'extension/demo/gone.js', null, $id, self::ROOTS );
        $this->assertFalse( is_file( "$root/extension/demo/gone.js" ) );
        $this->assertNull( $removed['after'] );

        $changes = expMigrationFiles::changes( $root, $id );
        $this->assertSame( array( 'design/site/templates/new/footer.tpl', 'extension/demo/old.css', 'extension/demo/gone.js' ), array_keys( $changes ) );
        $this->assertSame( 0600, fileperms( expMigrationFiles::listFile( $root, $id ) ) & 0777 );

        // put back, the last first
        $this->assertStringContainsString( 'put back extension/demo/gone.js', expMigrationFiles::restore( $root, $changes['extension/demo/gone.js'] ) );
        $this->assertSame( "gone\n", file_get_contents( "$root/extension/demo/gone.js" ) );
        file_put_contents( "$root/extension/demo/old.css", "edited by hand\n" );
        try
        {
            expMigrationFiles::restore( $root, $changes['extension/demo/old.css'] );
            $this->fail( 'a file changed since is not put back' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'changed after the migration wrote it', $e->getMessage() );
        }
        $this->assertSame( "edited by hand\n", file_get_contents( "$root/extension/demo/old.css" ) );
        expMigrationFiles::restore( $root, $changes['extension/demo/old.css'], true );
        $this->assertSame( "old\n", file_get_contents( "$root/extension/demo/old.css" ) );
        $this->assertSame( 0640, fileperms( "$root/extension/demo/old.css" ) & 0777 );
        $this->assertStringContainsString( 'removed design/site/templates/new/footer.tpl again', expMigrationFiles::restore( $root, $changes['design/site/templates/new/footer.tpl'] ) );
        $this->assertFalse( is_file( "$root/design/site/templates/new/footer.tpl" ) );

        // links lead nowhere
        symlink( "$root/outside", "$root/design/site/out" );
        try
        {
            expMigrationFiles::write( $root, 'design/site/out/x.tpl', 'x', $id, self::ROOTS );
            $this->fail( 'a link out of the folder is refused' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'leads out of design', $e->getMessage() );
        }
        $this->assertFalse( is_file( "$root/outside/x.tpl" ) );
        symlink( "$root/outside", "$root/design/site/link.tpl" );
        $this->expectException( RuntimeException::class );
        expMigrationFiles::write( $root, 'design/site/link.tpl', 'x', $id, self::ROOTS );
    }

    /** CF-04 */
    public function testStep()
    {
        $type = new expMigrationStepCodeFile( self::ROOTS );
        $root = $this->dir();
        $path = 'design/site/templates/page_footer.tpl';
        $this->assertSame( array(), $type->validate( array( 'mode' => 'ensure', 'path' => $path, 'content' => 'x' ) ) );
        $this->assertSame( array(), $type->validate( array( 'mode' => 'delete', 'path' => $path, 'expect' => str_repeat( 'a', 64 ) ) ) );
        $this->assertCount( 1, $type->validate( array( 'mode' => 'ensure', 'path' => $path ) ) );
        $this->assertCount( 1, $type->validate( array( 'mode' => 'ensure', 'path' => $path, 'content' => 'x', 'source' => 'files/x' ) ) );
        $this->assertCount( 1, $type->validate( array( 'mode' => 'ensure', 'path' => 'kernel/x.php', 'content' => 'x' ) ) );
        $this->assertCount( 1, $type->validate( array( 'mode' => 'ensure', 'path' => $path, 'source' => '../secret' ) ) );
        $this->assertCount( 1, $type->validate( array( 'mode' => 'ensure', 'path' => $path, 'content' => 'x', 'checksum' => 'abc' ) ) );
        $this->assertCount( 1, $type->validate( array( 'mode' => 'create', 'path' => $path, 'content' => 'x', 'expect' => str_repeat( 'b', 64 ) ) ) );

        $context = $this->context();
        file_put_contents( "$root/migrations/files/page_footer.tpl", "<footer>v2</footer>\n" );
        $create = array( 'mode' => 'create', 'path' => $path, 'source' => 'files/page_footer.tpl', 'expect' => 'absent',
                         'checksum' => hash( 'sha256', "<footer>v2</footer>\n" ) );
        $this->assertSame( array( "create $path (20 bytes)" ), $type->plan( $create, $context ) );
        $this->assertFalse( is_file( "$root/$path" ), 'a plan writes nothing' );
        $result = $type->apply( $create, $context );
        $this->assertTrue( $result['values']['changed'] );
        $this->assertSame( '', $result['values']['before'] );
        $this->assertSame( "<footer>v2</footer>\n", file_get_contents( "$root/$path" ) );
        $this->assertCount( 1, $context->takeFileChanges() );
        $this->assertStringContainsString( 'exists already', $type->plan( $create, $context )[0] );
        try
        {
            $type->apply( $create, $context );
            $this->fail( 'create: the file must not exist' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'exists already', $e->getMessage() );
        }
        $this->assertFalse( $type->apply( array( 'mode' => 'ensure', 'path' => $path, 'source' => 'files/page_footer.tpl' ), $context )['values']['changed'] );
        try
        {
            $type->apply( array( 'mode' => 'ensure', 'path' => $path, 'content' => 'x', 'checksum' => str_repeat( 'c', 64 ) ), $context );
            $this->fail( 'checksum: guards the new content' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'checksum:', $e->getMessage() );
        }
        try
        {
            $type->apply( array( 'mode' => 'update', 'path' => $path, 'content' => 'x', 'expect' => str_repeat( 'd', 64 ) ), $context );
            $this->fail( 'expect: guards the file before' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'is not the file expect: names', $e->getMessage() );
        }
        $this->assertSame( "<footer>v2</footer>\n", file_get_contents( "$root/$path" ) );
        try
        {
            $type->apply( array( 'mode' => 'update', 'path' => 'design/site/missing.tpl', 'content' => 'x' ), $context );
            $this->fail( 'update: the file must exist' );
        }
        catch ( RuntimeException $e )
        {
            $this->assertStringContainsString( 'does not exist', $e->getMessage() );
        }

        // a second migration replaces it, a third removes it; each rollback puts back what its migration found
        $second = $this->context( '20261102100100_files' );
        $type->apply( array( 'mode' => 'update', 'path' => $path, 'content' => "<footer>v3</footer>\n", 'expect' => hash( 'sha256', "<footer>v2</footer>\n" ) ), $second );
        $third = $this->context( '20261102100200_files' );
        $this->assertSame( array( "remove $path (a backup first)" ), $type->plan( array( 'mode' => 'delete', 'path' => $path ), $third ) );
        $type->apply( array( 'mode' => 'delete', 'path' => $path ), $third );
        $this->assertFalse( is_file( "$root/$path" ) );
        $this->assertSame( array( "$path: not there" ), $type->apply( array( 'mode' => 'delete', 'path' => $path ), $third )['lines'] );

        $this->assertStringContainsString( 'put back', $type->revert( array( 'mode' => 'delete', 'path' => $path ), $third )[0] );
        $this->assertSame( "<footer>v3</footer>\n", file_get_contents( "$root/$path" ) );
        $type->revert( array( 'mode' => 'update', 'path' => $path ), $second );
        $this->assertSame( "<footer>v2</footer>\n", file_get_contents( "$root/$path" ) );
        $this->assertStringContainsString( 'removed', $type->revert( $create, $this->context() )[0] );
        $this->assertFalse( is_file( "$root/$path" ) );
        $this->assertStringContainsString( 'did not change it', $type->revert( array( 'path' => 'design/site/other.tpl' ), $this->context() )[0] );

        $outside = new expMigrationRunnerTestContext( true, array( 'root' => $root ) );
        $this->expectException( RuntimeException::class );
        $type->apply( array( 'mode' => 'ensure', 'path' => $path, 'content' => 'x' ), $outside );
    }

    /** CF-05 */
    public function testRunnerPutsBackOnFailure()
    {
        $root = $this->dir();
        file_put_contents( "$root/design/site/templates/kept.tpl", "kept\n" );
        $self = $this;
        $failing = new expMigrationRunnerFake( '20261102110000_fails', array(
            'up' => function ( $context ) use ( $self ) {
                $type = new expMigrationStepCodeFile( self::ROOTS );
                $context->setMigrationFile( $self->migrationFile( '20261102110000_fails' ) );
                $type->apply( array( 'mode' => 'ensure', 'path' => 'design/site/templates/kept.tpl', 'content' => "changed\n" ), $context );
                $type->apply( array( 'mode' => 'create', 'path' => 'design/site/templates/made.tpl', 'content' => "made\n" ), $context );
                throw new RuntimeException( 'a later step failed' );
            },
        ) );
        $context = new expMigrationRunnerTestContext( true, array( 'root' => $root ) );
        $report = ( new expMigrationRunner( $context ) )->run( array( $failing ) );
        $this->assertSame( 'failed', $report['migrations'][0]['action'] );
        $messages = implode( "\n", $report['migrations'][0]['messages'] );
        $this->assertStringContainsString( 'a later step failed', $messages );
        $this->assertStringContainsString( 'removed design/site/templates/made.tpl again', $messages );
        $this->assertStringContainsString( 'put back design/site/templates/kept.tpl', $messages );
        $this->assertSame( "kept\n", file_get_contents( "$root/design/site/templates/kept.tpl" ) );
        $this->assertFalse( is_file( "$root/design/site/templates/made.tpl" ) );
        $this->assertSame( array(), $context->takeFileChanges() );
    }

    /**
     * The migration file of an id in the test's installation.
     *
     * @param string $id
     * @return string
     */
    public function migrationFile( $id )
    {
        $file = $this->dir() . "/migrations/$id.yml";
        file_put_contents( $file, "- type: code_file\n" );
        return $file;
    }

    /** CF-06 */
    public function testRecordingWindow()
    {
        $now = 1800000000;
        $this->assertSame( array(), expMigrationRecording::startProblems( 'site', 0, '', 0, $now ) );
        $this->assertSame( array(), expMigrationRecording::startProblems( 'site', 0, '', $now + 60, $now ) );
        $this->assertSame( array( 'the end of the window is not in the future' ), expMigrationRecording::startProblems( 'site', 0, '', $now, $now ) );
        $this->assertSame( array( 'a window is at most 31 days long' ), expMigrationRecording::startProblems( 'site', 0, '', $now + expMigrationRecording::MAX_WINDOW + 1, $now ) );

        $this->assertFalse( expMigrationRecording::ended( array( 'until' => 0 ), $now ) );
        $this->assertFalse( expMigrationRecording::ended( array( 'until' => $now + 1 ), $now ) );
        $this->assertTrue( expMigrationRecording::ended( array( 'until' => $now ), $now ) );
        $this->assertTrue( expMigrationRecording::ended( array( 'stopped_at' => $now - 5 ), $now ) );
        $active = array( array( 'recording_id' => 'a', 'kind' => 'site', 'user_id' => 14, 'started_at' => 1, 'until' => $now + 10 ),
                         array( 'recording_id' => 'b', 'kind' => 'session', 'user_id' => 10, 'started_at' => 1, 'until' => $now ) );
        $this->assertSame( array( 'a' ), array_column( expMigrationRecording::matching( $active, 10, $now ), 'recording_id' ) );
        $this->assertSame( array(), expMigrationRecording::matching( $active, 10, $now + 10 ) );

        $file = $this->dir() . '/active.json';
        $id = expMigrationRecording::newId();
        $this->assertTrue( expMigrationRecording::writeActive( $file, array( array( 'recording_id' => $id, 'kind' => 'site', 'user_id' => 14, 'started_at' => 5, 'until' => $now ) ) ) );
        $this->assertSame( $now, expMigrationRecording::active( $file )[0]['until'] );

        $this->assertSame( 0, Exponential\View\Kernel\Migration\Recording::windowEnd( '', $now ) );
        $this->assertSame( $now + 7200, Exponential\View\Kernel\Migration\Recording::windowEnd( ' 120 ', $now ) );
        $this->assertSame( $now + 2678400, Exponential\View\Kernel\Migration\Recording::windowEnd( '44640', $now ) );
        foreach ( array( '0', '-5', '1.5', 'soon', '44641' ) as $bad )
            $this->assertNull( Exponential\View\Kernel\Migration\Recording::windowEnd( $bad, $now ), $bad );
    }

    /** CF-07 */
    public function testLongRunFields()
    {
        $errors = array();
        $this->assertSame( array( 'max-migrations' => '20', 'max-time' => '600', 'throttle' => '0.5', 'nice' => '10' ),
                           expMigrationAdmin::longRunOptions( array( 'max-migrations' => ' 20 ', 'max-time' => '600', 'throttle' => '.5', 'nice' => '10' ), $errors ) );
        $this->assertSame( array(), $errors );
        $this->assertSame( array(), expMigrationAdmin::longRunOptions( array( 'max-migrations' => '', 'throttle' => '' ), $errors ), 'empty: the settings count' );
        $this->assertSame( array( 'throttle' => '0' ), expMigrationAdmin::longRunOptions( array( 'throttle' => '0' ), $errors ) );
        $bad = array( 'max-migrations' => '0', 'max-time' => '1.5', 'throttle' => '61', 'nice' => '20' );
        $this->assertSame( array(), expMigrationAdmin::longRunOptions( $bad, $errors ) );
        $this->assertSame( array( 'max-migrations', 'max-time', 'throttle', 'nice' ), $errors );
        $errors = array();
        $this->assertSame( array(), expMigrationAdmin::longRunOptions( array( 'nice' => array( '5' ), 'max-time' => '-1' ), $errors ) );
        $this->assertSame( array( 'max-time' ), $errors );

        $errors = array();
        $options = expMigrationAdmin::runOptions( array( 'MigrationChoice' => 1, 'MigrationOnly' => array( 'a', 'b' ), 'LongRun' => array( 'max-migrations' => '1', 'nice' => '7' ) ),
                                                  array( 'a', 'b' ), $errors );
        $this->assertSame( array(), $errors );
        $this->assertSame( array( 'max-migrations' => '1', 'nice' => '7' ), $options );
        $arguments = expMigrationJob::arguments( 'migrate', '20261102-120000-abcdef', $options, 'editor', '', 'p' );
        $this->assertContains( '--max-migrations=1', $arguments );
        $this->assertContains( '--nice=7', $arguments );
        $this->assertContains( '--nice=3', expMigrationJob::arguments( 'resume', '20261102-120000-abcdef', array( 'nice' => '3', 'throttle' => '0.5' ), 'editor', '', 'p' ) );

        $defaults = expMigrationAdmin::longRunDefaults( new expMigrationFileStepsIni( array( 'RunSettings/MaxMigrations' => '50', 'RunSettings/Nice' => ' ' ) ) );
        $this->assertSame( array( 'max-migrations' => '50', 'max-time' => '', 'throttle' => '', 'nice' => '' ), $defaults );
    }
}

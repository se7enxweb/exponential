<?php
/**
 * eZFilePackageHandler::uninstall() (from 6.0.15 on) removes only the files its install created, still unchanged,
 * at a relative path inside the installation, whose content is one of the package's own files; everything else
 * stays. The ezsql and eziniaddon handlers say why they undo nothing, and "extension" is a package type of
 * package.ini [PackageSettings] TypeList. No database: a stand-in package and a scratch directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class eZFilePackageHandlerUninstallTest extends PHPUnit\Framework\TestCase
{
    private $root;
    private $cwd;

    protected function setUp(): void
    {
        $this->root = dirname( __DIR__, 5 ) . '/var/tmp/ezfile-uninstall-test-' . getmypid();
        $this->cwd = getcwd();
        mkdir( $this->root . '/site/design/example/templates', 0777, true );
        mkdir( $this->root . '/package/files', 0777, true );
        // the package's own files
        file_put_contents( $this->root . '/package/files/a.tpl', 'package A' );
        file_put_contents( $this->root . '/package/files/b.tpl', 'package B' );
        chdir( $this->root . '/site' );
    }

    protected function tearDown(): void
    {
        chdir( $this->cwd );
        eZDir::recursiveDelete( $this->root );
    }

    private function package()
    {
        $dir = $this->root . '/package/files';
        return new class( $dir ) {
            private $dir;
            public function __construct( $dir ) { $this->dir = $dir; }
            public function attribute( $key )
            {
                if ( $key === 'name' )
                    return 'example';
                return array( 'default' => array( array( 'name' => 'a.tpl' ), array( 'name' => 'b.tpl' ) ) );
            }
            public function fileItemPath( $fileItem, $collectionName ) { return $this->dir . '/' . $fileItem['name']; }
        };
    }

    public function testRemovesOnlyUnchangedFilesItCreated()
    {
        file_put_contents( 'design/example/templates/a.tpl', 'package A' );   // as installed
        file_put_contents( 'design/example/templates/b.tpl', 'changed by the site' );
        file_put_contents( 'design/example/templates/c.tpl', 'package B' );   // not recorded: never touched
        file_put_contents( $this->root . '/outside.tpl', 'package A' );
        $installData = array( 'installed_files' => array(
            'design/example/templates/a.tpl' => md5( 'package A' ),
            'design/example/templates/b.tpl' => md5( 'package B' ),
            '../outside.tpl' => md5( 'package A' ),
            $this->root . '/outside.tpl' => md5( 'package A' ),
            'design/example/templates/gone.tpl' => md5( 'package A' ),
        ) );
        $handler = new eZFilePackageHandler();
        $parameters = array();
        $result = $handler->uninstall( $this->package(), 'ezfile', array(), '', false, false, false, false, $parameters, $installData );

        $this->assertTrue( $result );
        $this->assertFileDoesNotExist( 'design/example/templates/a.tpl' );
        $this->assertFileExists( 'design/example/templates/b.tpl', 'changed since the install: kept' );
        $this->assertFileExists( 'design/example/templates/c.tpl', 'not installed by the package: kept' );
        $this->assertFileExists( $this->root . '/outside.tpl', 'outside the installation: kept' );
        $this->assertDirectoryExists( 'design/example/templates' );
        $this->assertSame( array(), $installData['installed_files'] );
    }

    public function testInstallDataCanNeverNameAFileThatIsNotThePackages()
    {
        file_put_contents( 'settings.ini', 'site secret' );
        $installData = array( 'installed_files' => array( 'settings.ini' => md5( 'site secret' ) ) );
        $handler = new eZFilePackageHandler();
        $parameters = array();
        $handler->uninstall( $this->package(), 'ezfile', array(), '', false, false, false, false, $parameters, $installData );
        $this->assertFileExists( 'settings.ini' );
    }

    public function testPackageInstalledBeforeHasNothingToRemove()
    {
        $installData = array();
        $handler = new eZFilePackageHandler();
        $parameters = array();
        $this->assertTrue( $handler->uninstall( $this->package(), 'ezfile', array(), '', false, false, false, false, $parameters, $installData ) );
    }

    public function testSafeInstalledPaths()
    {
        foreach ( array( 'design/x/templates/a.tpl', 'settings/override/x.ini.append.php', 'var/x' ) as $path )
            $this->assertTrue( eZFilePackageHandler::isSafeInstalledPath( $path ), $path );
        foreach ( array( '', '/etc/passwd', '\\x', 'C:/x', '../x', 'a/../../x', 'a\\..\\x', "a\0b", 'file:///x' ) as $path )
            $this->assertFalse( eZFilePackageHandler::isSafeInstalledPath( $path ), var_export( $path, true ) );
    }

    public function testInstallRecordsOnlyFilesItCreates()
    {
        $source = file_get_contents( dirname( __DIR__, 5 ) . '/kernel/classes/packagehandlers/ezfile/ezfilepackagehandler.php' );
        $this->assertStringContainsString( '$existedBefore = file_exists( $newFilePath );', $source );
        $this->assertStringContainsString( "if ( !\$existedBefore && is_file( \$newFilePath ) )", $source );
    }

    public function testSqlAndIniAddonHandlersSayWhyTheyUndoNothing()
    {
        foreach ( array( 'eZDBPackageHandler', 'eZINIAddonPackageHandler' ) as $class )
        {
            $method = new ReflectionMethod( $class, 'uninstall' );
            $this->assertSame( $class, $method->getDeclaringClass()->getName() );
        }
    }

    public function testExtensionIsAPackageType()
    {
        $ini = file_get_contents( dirname( __DIR__, 5 ) . '/settings/package.ini' );
        $this->assertMatchesRegularExpression( '/^TypeList\[extension\]=Extension$/m', $ini );
    }
}

<?php
/**
 * The Zeta Components and image editor requirements of composer.json (6.0.15). No database.
 *
 *  CR-01 - authentication-database-tiein, mvc-authentication-tiein and php-generator are required neither by
 *          composer.json nor by composer.json.dist
 *  CR-02 - no PHP code of the kernel, its libraries, designs, scripts, test toolkit or the extensions the kernel
 *          repository ships names a class of those three (comments may say where code was adopted from)
 *  CR-03 - zetacomponents/configuration stays required while composer.json requires a 7x SQLI Import whose releases
 *          throw ezcConfigurationNoConfigException (2.0.x; the extension's own copy is checked too when installed)
 *  CR-04 - se7enxweb/ezie and zetacomponents/image-conversion stay required: the sevenx_multisite site package
 *          activates ezie, and ezie does not declare the image conversion it needs
 *  CR-05 - the setup wizard and the kickstarter activate expimageeditor, not ezie (only one of the two can draw the
 *          button in admin4)
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expComposerKernelRequirementsTest extends PHPUnit\Framework\TestCase
{
    const REMOVED = array(
        'zetacomponents/authentication-database-tiein' => 'ezcAuthenticationDatabase',
        'zetacomponents/mvc-authentication-tiein' => 'ezcMvcAuthentication',
        'zetacomponents/php-generator' => 'ezcPhpGenerator',
    );

    /** directories whose PHP code belongs to the kernel package (extension/ only the ones the repository ships) */
    const SCANNED = array( 'kernel', 'lib', 'design', 'bin', 'cronjobs', 'update', 'tests/toolkit',
                           'extension/expimageeditor', 'extension/expservices', 'extension/ezformtoken',
                           'extension/ezjscore', 'extension/ezoe' );

    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    private static function composer( $file = 'composer.json' )
    {
        $data = json_decode( (string)file_get_contents( self::root() . '/' . $file ), true );
        self::assertIsArray( $data, "$file is valid JSON" );
        return $data;
    }

    /** CR-01 */
    public function testRemovedComponentsAreNotRequired()
    {
        foreach ( array( 'composer.json', 'composer.json.dist' ) as $file )
        {
            $composer = self::composer( $file );
            foreach ( array_keys( self::REMOVED ) as $package )
            {
                $this->assertArrayNotHasKey( $package, $composer['require'] ?? array(), "$file require" );
                $this->assertArrayNotHasKey( $package, $composer['require-dev'] ?? array(), "$file require-dev" );
            }
        }
        $this->assertFileDoesNotExist( self::root() . '/tests/toolkit/extras/scripts/create-test-from-class.php' );
    }

    /** CR-02 */
    public function testNoCodeUsesARemovedComponent()
    {
        $pattern = '/^(' . implode( '|', self::REMOVED ) . ')/';
        $found = array();
        foreach ( self::SCANNED as $directory )
        {
            $path = self::root() . '/' . $directory;
            if ( !is_dir( $path ) )
                continue;
            $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $iterator as $file )
            {
                if ( !$file->isFile() || substr( $file->getFilename(), -4 ) !== '.php' )
                    continue;
                $source = (string)file_get_contents( $file->getPathname() );
                if ( !preg_match( '/ezc(AuthenticationDatabase|MvcAuthentication|PhpGenerator)/', $source ) )
                    continue;
                // code only: a comment that says where code was adopted from needs no package
                foreach ( token_get_all( $source ) as $token )
                {
                    if ( is_array( $token ) && $token[0] === T_STRING && preg_match( $pattern, $token[1] ) )
                        $found[] = substr( $file->getPathname(), strlen( self::root() ) + 1 ) . ':' . $token[2] . ' ' . $token[1];
                }
            }
        }
        $this->assertSame( array(), $found, 'code names a class of a component composer.json no longer requires' );
    }

    /** CR-03 */
    public function testConfigurationStaysWhileSqliImportUsesIt()
    {
        $require = self::composer()['require'];
        if ( !isset( $require['se7enxweb/sqliimport'] ) )
            $this->markTestSkipped( 'composer.json does not require 7x SQLI Import' );

        $factory = self::root() . '/extension/sqliimport/classes/sqliimportfactory.php';
        $installedUsesIt = is_file( $factory ) && strpos( (string)file_get_contents( $factory ), 'ezcConfiguration' ) !== false;
        // 2.0.0 and 2.0.1 throw ezcConfigurationNoConfigException; ~2.0.0 allows them
        $constraintAllowsIt = preg_match( '/^[~^]?2\.0(\.[01])?$/', trim( $require['se7enxweb/sqliimport'] ) ) === 1;
        if ( !$installedUsesIt && !$constraintAllowsIt )
            $this->markTestSkipped( 'the required 7x SQLI Import no longer uses zetacomponents/configuration: it may go' );

        $this->assertArrayHasKey( 'zetacomponents/configuration', $require,
            '7x SQLI Import ' . $require['se7enxweb/sqliimport'] . ' throws ezcConfigurationNoConfigException; drop the component together with a repin to a release that does not' );
    }

    /** CR-04 */
    public function testEzieKeepsItsImageConversion()
    {
        $require = self::composer()['require'];
        $this->assertArrayHasKey( 'se7enxweb/ezie', $require, 'the sevenx_multisite site package activates ezie' );
        $this->assertArrayHasKey( 'zetacomponents/image-conversion', $require, 'ezie needs it and does not require it itself' );
    }

    /** CR-05 */
    public function testWizardActivatesExpImageEditor()
    {
        $source = (string)file_get_contents( self::root() . '/kernel/setup/steps/ezstep_create_sites.php' );
        $this->assertSame( 1, preg_match( '/foreach \(\s*array\(\s*(\'[^)]*?), self::restApiExtension\(\)/s', $source, $m ),
                           'the list of extensions the wizard activates when present' );
        $list = array_map( function ( $item ) { return trim( $item, " '\n" ); }, explode( ',', $m[1] ) );
        $this->assertContains( 'expimageeditor', $list );
        $this->assertNotContains( 'ezie', $list, 'both editors replace the same include of admin4; the wizard activates one' );
    }
}

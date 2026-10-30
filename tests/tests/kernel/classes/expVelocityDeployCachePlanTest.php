<?php
/**
 * File containing the expVelocityDeployCachePlanTest class.
 *
 * Which caches exp:velocity deploy clears (expVelocityDeploy::plan()): every cache without a record of an earlier
 * deploy, none of the INI caches and caches of rendered output after a deploy of PHP classes only, the right ones for
 * changed templates, settings, translations and template engine code, and all of them with --all-caches. And how
 * changed files are found (expVelocityDeploy::changedFiles()): by modification time, below the watched paths only.
 * No server and no database: files are written in a scratch directory under var/tmp.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class expVelocityDeployCachePlanTest extends ezpTestCase
{
    const CACHES = array( 'ini', 'template', 'template-override', 'translation', 'design_base', 'rendered' );

    /** @var string */
    protected $dir;

    public function setUp(): void
    {
        parent::setUp();
        $this->dir = eZSys::rootDir() . '/var/tmp/velocity-deploy-plan-test-' . getmypid();
        @mkdir( $this->dir, 0775, true );
    }

    public function tearDown(): void
    {
        $remove = function ( $path ) use ( &$remove ) {
            if ( is_dir( $path ) && !is_link( $path ) )
            {
                foreach ( (array)scandir( $path ) as $entry )
                    if ( $entry !== '.' && $entry !== '..' )
                        $remove( $path . '/' . $entry );
                @rmdir( $path );
            }
            else
                @unlink( $path );
        };
        $remove( $this->dir );
        parent::tearDown();
    }

    /** @return array cache => cleared (bool) */
    private static function cleared( array $changed, array $options = array(), $known = true )
    {
        $plan = expVelocityDeploy::plan( array( 'known' => $known, 'since' => $known ? 1700000000 : null, 'changed' => $changed ), $options );
        $result = array();
        foreach ( self::CACHES as $cache )
        {
            self::assertArrayHasKey( $cache, $plan );
            $result[$cache] = $plan[$cache][0];
        }
        return $result;
    }

    public function testWithoutARecordEveryCacheIsCleared()
    {
        $this->assertSame( array_fill_keys( self::CACHES, true ), self::cleared( array(), array(), false ) );
    }

    public function testADeployOfPhpClassesKeepsTheCaches()
    {
        $this->assertSame( array_fill_keys( self::CACHES, false ), self::cleared( array() ) );
        $plan = expVelocityDeploy::plan( array( 'known' => true, 'since' => 1700000000, 'changed' => array() ), array() );
        $this->assertStringStartsWith( 'kept: no template, design file, settings or translation changed', $plan['rendered'][1] );
    }

    public function testChangedTemplates()
    {
        $this->assertSame( array( 'ini' => false, 'template' => true, 'template-override' => true, 'translation' => false,
                                  'design_base' => true, 'rendered' => true ),
                           self::cleared( array( 'templates' => array( 'design/x/templates/a.tpl' ) ) ) );
    }

    public function testChangedSettings()
    {
        $this->assertSame( array( 'ini' => true, 'template' => false, 'template-override' => true, 'translation' => false,
                                  'design_base' => true, 'rendered' => true ),
                           self::cleared( array( 'settings' => array( 'settings/site.ini' ) ) ) );
    }

    public function testChangedTranslations()
    {
        $this->assertSame( array( 'ini' => false, 'template' => false, 'template-override' => false, 'translation' => true,
                                  'design_base' => false, 'rendered' => true ),
                           self::cleared( array( 'translations' => array( 'share/translations/ger-DE/translation.ts' ) ) ) );
    }

    /** The template engines' code: the compiled templates are made again, rendered output is kept. */
    public function testChangedTemplateEngineCode()
    {
        $this->assertSame( array( 'ini' => false, 'template' => true, 'template-override' => false, 'translation' => false,
                                  'design_base' => false, 'rendered' => false ),
                           self::cleared( array( 'template code' => array( 'lib/twig/classes/exptwigextension.php' ) ) ) );
    }

    public function testOptions()
    {
        $this->assertSame( array_fill_keys( self::CACHES, true ), self::cleared( array(), array( 'all-caches' => true ) ) );
        $this->assertSame( array( 'ini' => false, 'template' => false, 'template-override' => false, 'translation' => false,
                                  'design_base' => false, 'rendered' => true ),
                           self::cleared( array(), array( 'clear-content' => true ) ) );
        $kernel = self::cleared( array(), array( 'kernel' => true ) );
        $this->assertTrue( $kernel['ini'], '--kernel: a class added or renamed, the kernel services are compiled again' );
        $this->assertFalse( $kernel['rendered'] );
    }

    public function testTheReasonNamesTheChangedFiles()
    {
        $plan = expVelocityDeploy::plan( array( 'known' => true, 'since' => 1700000000,
                                                'changed' => array( 'settings' => array( 'settings/site.ini' ) ) ), array() );
        $this->assertSame( 'cleared: settings changed (settings/site.ini)', $plan['ini'][1] );
    }

    public function testChangedFilesByModificationTime()
    {
        $root = $this->dir;
        foreach ( array( 'design/a/templates/old.tpl', 'design/a/templates/new.tpl', 'extension/x/design/b/templates/new.twig',
                         'extension/x/classes/new.php', 'settings/old.ini' ) as $file )
        {
            @mkdir( dirname( "$root/$file" ), 0775, true );
            file_put_contents( "$root/$file", 'x' );
        }
        $since = time();
        touch( "$root/design/a/templates/old.tpl", $since - 100 );
        touch( "$root/settings/old.ini", $since - 100 );
        touch( "$root/design/a/templates/new.tpl", $since + 1 );
        touch( "$root/extension/x/design/b/templates/new.twig", $since + 1 );
        touch( "$root/extension/x/classes/new.php", $since + 1 );

        $templates = expVelocityDeploy::changedFiles( $root, expVelocityDeploy::WATCHED['templates'], $since );
        sort( $templates );
        $this->assertSame( array( 'design/a/templates/new.tpl', 'extension/x/design/b/templates/new.twig' ), $templates );
        $this->assertSame( array(), expVelocityDeploy::changedFiles( $root, expVelocityDeploy::WATCHED['settings'], $since ) );
        $this->assertCount( 1, expVelocityDeploy::changedFiles( $root, expVelocityDeploy::WATCHED['templates'], $since, 1 ), 'stops at the limit' );
    }
}

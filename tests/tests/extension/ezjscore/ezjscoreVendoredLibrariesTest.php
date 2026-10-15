<?php
/**
 * The jQuery libraries ezjscore ships: one release of each, and the one its settings name.
 *
 * extension/ezjscore/design/standard/javascript/ holds jQuery, jQuery Migrate and jQuery UI. Older
 * releases used to stay beside the current ones "for templates that name them directly", and nothing
 * named them; jQuery UI 1.10.3 among them has known vulnerabilities. This test keeps the folder at one
 * release per library (the minified and the full build of it may both be there), checks that every
 * LocalScripts[] file of ezjscore.ini exists, that the CDN entries name the same release as the local
 * files, and that extension.xml declares that release. It reads files only: no kernel, no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscoreVendoredLibrariesTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

use PHPUnit\Framework\TestCase;

class ezjscoreVendoredLibrariesTest extends TestCase
{
    /**
     * File name prefix of each library, longest first so jquery-ui- is not read as jquery-
     */
    private const LIBRARIES = array(
        'jquery-migrate' => 'jQuery Migrate',
        'jquery-ui' => 'jQuery UI',
        'jquery' => 'jQuery',
    );

    private static function root(): string
    {
        return dirname( __DIR__, 4 ) . '/extension/ezjscore';
    }

    private static function javascriptDir(): string
    {
        return self::root() . '/design/standard/javascript';
    }

    /**
     * Splits a library file name into library and release, or null when it is not one
     *
     * @return array{0:string,1:string}|null
     */
    private static function parse( string $fileName ): ?array
    {
        foreach ( self::LIBRARIES as $prefix => $name )
        {
            if ( preg_match( '/^' . preg_quote( $prefix, '/' ) . '-(\d+\.\d+\.\d+)(?:\.custom)?(?:\.min)?\.js$/', $fileName, $m ) )
            {
                return array( $prefix, $m[1] );
            }
        }
        return null;
    }

    /**
     * The releases of each library in the javascript folder
     *
     * @return array<string,string[]>
     */
    private static function releasesOnDisk(): array
    {
        $releases = array();
        foreach ( scandir( self::javascriptDir() ) as $fileName )
        {
            $parsed = self::parse( $fileName );
            if ( $parsed === null )
            {
                continue;
            }
            $releases[$parsed[0]][$parsed[1]] = $parsed[1];
        }
        return array_map( 'array_values', $releases );
    }

    /**
     * The values of one array setting of the [eZJSCore] block of the shipped ezjscore.ini
     *
     * @return array<string,string>
     */
    private static function iniArray( string $key ): array
    {
        $values = array();
        $inBlock = false;
        foreach ( file( self::root() . '/settings/ezjscore.ini' ) as $line )
        {
            $line = trim( $line );
            if ( preg_match( '/^\[(.+)\]$/', $line, $m ) )
            {
                $inBlock = ( $m[1] === 'eZJSCore' );
                continue;
            }
            if ( $inBlock && preg_match( '/^' . preg_quote( $key, '/' ) . '\[(\w+)\]=(.*)$/', $line, $m ) )
            {
                $values[$m[1]] = trim( $m[2] );
            }
        }
        return $values;
    }

    public function testParseKnowsTheFileNamesOfTheThreeLibraries(): void
    {
        $this->assertSame( array( 'jquery', '4.0.0' ), self::parse( 'jquery-4.0.0.min.js' ) );
        $this->assertSame( array( 'jquery-migrate', '4.0.2' ), self::parse( 'jquery-migrate-4.0.2.js' ) );
        $this->assertSame( array( 'jquery-ui', '1.10.3' ), self::parse( 'jquery-ui-1.10.3.custom.min.js' ) );
        $this->assertNull( self::parse( 'LICENSE-jquery.txt' ) );
    }

    public function testTheFolderShipsExactlyOneReleaseOfEachLibrary(): void
    {
        $releases = self::releasesOnDisk();
        $this->assertSame( array_keys( self::LIBRARIES ), array_intersect( array_keys( self::LIBRARIES ), array_keys( $releases ) ),
            'each of jQuery, jQuery Migrate and jQuery UI is in the folder' );
        foreach ( $releases as $library => $versions )
        {
            $this->assertCount( 1, $versions,
                self::LIBRARIES[$library] . ' is shipped in one release only, found: ' . implode( ', ', $versions ) );
        }
    }

    public function testEveryLocalScriptOfTheSettingsExists(): void
    {
        $localScripts = self::iniArray( 'LocalScripts' );
        $this->assertSame( array( 'jquery', 'jqueryMigrate', 'jqueryUI' ), array_keys( $localScripts ) );
        foreach ( $localScripts as $key => $fileName )
        {
            $path = $fileName[0] === '/'
                ? self::root() . '/design/standard' . $fileName
                : self::javascriptDir() . '/' . $fileName;
            $this->assertFileExists( $path, "LocalScripts[$key]" );
        }
    }

    public function testTheSettingsTheCdnAndExtensionXmlNameTheShippedRelease(): void
    {
        $releases = self::releasesOnDisk();
        $local = self::iniArray( 'LocalScripts' );
        $external = self::iniArray( 'ExternalScripts' );
        $xml = simplexml_load_file( self::root() . '/extension.xml' );
        $declared = array();
        foreach ( $xml->metadata->software->uses as $uses )
        {
            $declared[(string)$uses->name] = (string)$uses->version;
        }

        foreach ( array( 'jquery' => 'jquery', 'jqueryMigrate' => 'jquery-migrate', 'jqueryUI' => 'jquery-ui' ) as $key => $library )
        {
            $shipped = $releases[$library][0];
            $this->assertSame( array( $library, $shipped ), self::parse( $local[$key] ), "LocalScripts[$key]" );
            $this->assertStringContainsString( $shipped, $external[$key], "ExternalScripts[$key]" );
            $this->assertSame( $shipped, $declared[self::LIBRARIES[$library]] ?? null,
                'extension.xml declares ' . self::LIBRARIES[$library] . ' ' . $shipped );
        }
    }

    /**
     * An ezjscore.ini of another extension, a siteaccess or the override settings that names a jQuery file must name
     * one that exists in some design: an old override with the files ezjscore no longer ships loads nothing. Only
     * the settings files present are read, so a checkout without extensions passes.
     */
    public function testNoOtherSettingsNameAJqueryFileThatIsGone(): void
    {
        $base = dirname( __DIR__, 4 );
        $files = array_merge( glob( "$base/settings/{siteaccess/*,override}/ezjscore.ini*", GLOB_BRACE ) ?: array(),
                              glob( "$base/extension/*/settings/ezjscore.ini*" ) ?: array(),
                              glob( "$base/extension/*/settings/siteaccess/*/ezjscore.ini*" ) ?: array() );
        $missing = array();
        foreach ( $files as $file )
        {
            if ( strpos( $file, "$base/extension/ezjscore/" ) === 0 )
                continue;
            preg_match_all( '/^[ \t]*LocalScripts\[(jquery|jqueryMigrate|jqueryUI)\][ \t]*=[ \t]*(\S+)/m', (string)file_get_contents( $file ), $m, PREG_SET_ORDER );
            foreach ( $m as $match )
            {
                $name = $match[2];
                if ( self::parse( basename( $name ) ) === null )
                    continue;
                $sub = $name[0] === '/' ? $name : '/javascript/' . $name;
                if ( !glob( "$base/design/*$sub" ) && !glob( "$base/extension/*/design/*$sub" ) )
                    $missing[] = substr( $file, strlen( $base ) + 1 ) . ": LocalScripts[{$match[1]}]=$name";
            }
        }
        $this->assertSame( array(), $missing, 'jQuery files named in settings that no design holds' );
    }
}

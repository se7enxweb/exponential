<?php
/**
 * INI files in a PHP wrapper cannot be read over the web.
 *
 *  INI-01 - Every *.ini.append.php and *.ini.php of this repository (settings/ and the settings of the extensions it
 *           ships) begins with "<?php /*": without it a direct request serves the file as text, settings and all
 *           (an extension's file that began with "#?ini", or with the short open tag "<?/*", did)
 *  INI-02 - Every such file ends with "*\/ ?>": a comment left open makes PHP warn when the file is included
 *
 * The files are the ones git tracks, so extensions installed into extension/ by Composer are not checked here (they
 * carry their own tests). No database.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group security
 */

class expIniPhpWrapperTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 5 );
    }

    public static function iniFiles()
    {
        $files = array();
        $root = self::root();
        if ( !is_dir( $root . '/.git' ) && !is_file( $root . '/.git' ) )
            return array( 'no git checkout' => array( '' ) );
        exec( 'git -C ' . escapeshellarg( $root ) . ' ls-files -- "settings/*.ini.append.php" "settings/*.ini.php"'
              . ' "extension/*/settings/*.ini.append.php" "extension/*/settings/*.ini.php" 2>/dev/null', $files );
        $cases = array();
        foreach ( $files as $file )
            $cases[$file] = array( $file );
        return $cases ?: array( 'no files' => array( '' ) );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider( 'iniFiles' )]
    public function testIniFileIsWrappedInPhp( string $file ): void
    {
        if ( $file === '' )
            $this->markTestSkipped( 'needs a git checkout' );
        $text = str_replace( "\r", '', (string)file_get_contents( self::root() . '/' . $file ) );
        $text = preg_replace( '/^\xEF\xBB\xBF/', '', $text );
        $this->assertMatchesRegularExpression( '#^<\?php\s*/\*#', $text, "INI-01: $file does not begin with <?php /*" );
        $this->assertMatchesRegularExpression( '#\*/\s*\?>\s*$#', $text, "INI-02: $file does not end with */ ?>" );
    }
}

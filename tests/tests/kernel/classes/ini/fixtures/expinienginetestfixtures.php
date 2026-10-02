<?php
/**
 * Fixtures of the exp:ini engine tests: a throwaway installation root under var/tmp/ini-tests/ with a few settings
 * directories, so no test ever touches the real settings.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expIniEngineTestFixtures
{
    /** @var string|null One directory per test run */
    protected static $runDir = null;
    protected static $counter = 0;

    /** @return string The real installation root, with a trailing slash */
    public static function realRoot()
    {
        return dirname( __DIR__, 6 ) . '/';
    }

    /** @return string var/tmp/ini-tests/run-<time>-<pid>/ (created) */
    public static function runDir()
    {
        if ( self::$runDir === null )
        {
            self::$runDir = self::realRoot() . 'var/tmp/ini-tests/run-' . date( 'Ymd-His' ) . '-' . getmypid() . '/';
            if ( !is_dir( self::$runDir ) )
                mkdir( self::$runDir, 0755, true );
        }
        return self::$runDir;
    }

    /**
     * A fresh fixture installation root, and the editor pointed at it.
     *
     *   settings/site.ini                                     defaults
     *   settings/override/site.ini.append.php                 PHP-wrapped, comments, ## comments, arrays, hashes
     *   settings/siteaccess/eng/site.ini.append.php
     *   settings/siteaccess/admin/                            empty
     *   extension/exta/settings/site.ini.append.php           active
     *   extension/exta/settings/siteaccess/eng/               empty
     *   extension/extb/                                       inactive, no settings
     *   'plain' is a known siteaccess without a directory
     *
     * @param string $name
     * @return string The root, with a trailing slash
     */
    public static function makeRoot( $name = 'root' )
    {
        $root = self::runDir() . sprintf( '%03d-', ++self::$counter ) . preg_replace( '#[^\w-]#', '_', $name ) . '/';
        foreach ( array( 'settings/override', 'settings/siteaccess/eng', 'settings/siteaccess/admin',
                         'extension/exta/settings/siteaccess/eng', 'extension/extb', 'var' ) as $dir )
            mkdir( $root . $dir, 0755, true );
        file_put_contents( $root . 'settings/site.ini', self::defaultSiteIni() );
        file_put_contents( $root . 'settings/override/site.ini.append.php', self::overrideSiteIni() );
        file_put_contents( $root . 'settings/siteaccess/eng/site.ini.append.php',
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=English\n\n[SiteAccessSettings]\nRequireUserLogin=false\n\n*/ ?>" );
        file_put_contents( $root . 'extension/exta/settings/site.ini.append.php',
                           "<?php /* #?ini charset=\"utf-8\"?\n\n[ExtAs]\nFeature=enabled\n*/ ?>\n" );
        expIniEditor::setRoot( $root, array( 'exta' ), array( 'eng', 'admin', 'plain' ) );
        return $root;
    }

    public static function defaultSiteIni()
    {
        return "#?ini charset=\"utf-8\"?\n# The defaults\n\n[SiteSettings]\nSiteName=Default\n\n[DebugSettings]\nDebugOutput=disabled\n";
    }

    public static function overrideSiteIni()
    {
        return "<?php /* #?ini charset=\"utf-8\"?\n"
             . "\n"
             . "# Global overrides\n"
             . "[SiteSettings]\n"
             . "# the name\n"
             . "SiteName=Exponential\n"
             . "SiteURL=example.com ## comment after the value\n"
             . "\n"
             . "# comment about the next block\n"
             . "[ExtensionSettings]\n"
             . "ActiveExtensions[]\n"
             . "ActiveExtensions[]=exta\n"
             . "ActiveExtensions[]=ezjscore\n"
             . "\n"
             . "[DatabaseSettings]\n"
             . "Password=s3cret\n"
             . "Servers[main]=db1\n"
             . "Servers[replica]=db2\n"
             . "\n"
             . "*/ ?>";
    }

    /** @return expIniEditor For a scope spec and file */
    public static function editor( $spec, $file = 'site' )
    {
        return new expIniEditor( expIniEditor::scope( $spec ), $file );
    }

    /**
     * Every INI file of the real installation: settings/ and extension/<ext>/settings/, recursively.
     *
     * @return string[] Paths relative to the real root
     */
    public static function realIniFiles()
    {
        $root = self::realRoot();
        $files = array();
        $dirs = array( $root . 'settings' );
        foreach ( (array)glob( $root . 'extension/*/settings', GLOB_ONLYDIR ) as $d )
            $dirs[] = $d;
        foreach ( $dirs as $dir )
        {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $f )
            {
                if ( !$f->isFile() || $f->isLink() )
                    continue;
                if ( preg_match( '#\.ini(\.append)?(\.php)?$#', $f->getFilename() ) && strpos( $f->getFilename(), '#' ) === false )
                    $files[] = substr( $f->getPathname(), strlen( $root ) );
            }
        }
        sort( $files );
        return $files;
    }
}

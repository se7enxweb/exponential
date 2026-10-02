<?php
/**
 * The temporary installation root the exp:ini command tests work in (var/tmp/ini/b/...): defaults, an
 * override, two siteaccesses and one extension with a siteaccess directory.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class iniCommandTestRoot
{
    /**
     * A small settings tree: defaults, an override, two siteaccesses, one extension with a siteaccess directory.
     */
    public static function build( $root )
    {
        $files = array(
            'settings/site.ini' =>
                "#?ini charset=\"utf-8\"?\n# The defaults\n\n[SiteSettings]\nSiteName=Default site\nSiteURL=example.com\n\n" .
                "[SiteAccessSettings]\nAvailableSiteAccessList[]\nAvailableSiteAccessList[]=site\nAvailableSiteAccessList[]=admin\n\n" .
                "[ContentSettings]\nViewCaching=enabled\n\n[TemplateSettings]\nDebug=false\n\n[ExtensionSettings]\nActiveExtensions[]\n\n" .
                "[DatabaseSettings]\nUser=root\nPassword=defaultpw\n",
            'settings/override/site.ini.append.php' =>
                "<?php /* #?ini charset=\"utf-8\"?\n\n# Site settings of this installation\n[SiteSettings]\n# the name\nSiteName=Override site\n" .
                "SiteURL=override.example.com\n\n[ExtensionSettings]\nActiveExtensions[]\nActiveExtensions[]=fixtureext\n\n" .
                "[DatabaseSettings]\nPassword=s3cretpw\n\n[DebugSettings]\nDebugOutput=Enabled\nLevel=1\nMode=sometimes\n*/ ?>\n",
            'settings/siteaccess/site/site.ini.append.php' =>
                "<?php /* #?ini charset=\"utf-8\"?\n\n[SiteSettings]\nSiteName=Site siteaccess\n\n[SiteAccessSettings]\n" .
                "RelatedSiteAccessList[]\nRelatedSiteAccessList[]=site\nRelatedSiteAccessList[]=admin\n*/ ?>\n",
            'settings/siteaccess/admin/site.ini.append.php' =>
                "<?php /* #?ini charset=\"utf-8\"?\n\n[TemplateSettings]\nDebug=true\n*/ ?>\n",
            'extension/fixtureext/settings/site.ini.append.php' =>
                "<?php /* #?ini charset=\"utf-8\"?\n\n[FixtureSettings]\nList[]\nList[]=one\nList[]=two\nMap[a]=1\nMap[b]=2\n*/ ?>\n",
            'extension/fixtureext/settings/siteaccess/admin/.keep' => '',
        );
        foreach ( $files as $path => $content )
        {
            $full = $root . '/' . $path;
            if ( !is_dir( dirname( $full ) ) )
                mkdir( dirname( $full ), 0755, true );
            file_put_contents( $full, $content );
        }
        mkdir( $root . '/var', 0755, true );
    }

    public static function remove( $dir )
    {
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                                                 RecursiveIteratorIterator::CHILD_FIRST ) as $f )
            $f->isDir() && !$f->isLink() ? rmdir( $f->getPathname() ) : unlink( $f->getPathname() );
        rmdir( $dir );
    }

}

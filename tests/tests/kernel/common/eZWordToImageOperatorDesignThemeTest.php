<?php
/**
 * icon.ini [IconSettings] DesignTheme[<design>]: the icon theme follows the design. While admin4 or admin4l is the
 * SiteDesign, class_icon, mimetype_icon, classgroup_icon, icon and the content tree's icon_info take the
 * bootstrap-icons theme, whatever Theme the siteaccess sets; Theme is searched right after it. Another design (admin3
 * after the UI switch, a public site) keeps Theme. An empty value turns it off, a type's own Theme still wins.
 * Before, the theme was opt-in per admin siteaccess, and an admin4 siteaccess whose icon.ini.append.php said
 * Theme=crystal-admin drew the crystal PNGs in the tree, the node header and the sub items.
 *
 * Checked in the shipped settings, through eZWordToImageOperator::themeList() and in a template rendered by
 * eZTemplate. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/common/eZWordToImageOperatorDesignThemeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group templateoperators
 */

class eZWordToImageOperatorDesignThemeTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $saved = array();

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $this->dir = 'var/tmp/icon_design_theme_' . uniqid( '', true );
        mkdir( $this->dir, 0700, true );

        $icon = eZINI::instance( 'icon.ini' );
        $site = eZINI::instance();
        foreach ( array( array( $icon, 'IconSettings', 'Theme' ), array( $icon, 'IconSettings', 'AdditionalThemeList' ),
                         array( $icon, 'IconSettings', 'StandardTheme' ), array( $icon, 'IconSettings', 'DesignTheme' ),
                         array( $icon, 'ClassIcons', 'Theme' ), array( $site, 'DesignSettings', 'SiteDesign' ) ) as $setting )
            $this->saved[] = array( $setting, $setting[0]->hasVariable( $setting[1], $setting[2] ) ? array( $setting[0]->variable( $setting[1], $setting[2] ) ) : null );

        // An admin siteaccess as it was set up before the theme followed the design.
        $icon->setVariable( 'IconSettings', 'Theme', 'crystal-admin' );
        $icon->setVariable( 'IconSettings', 'StandardTheme', 'crystal' );
        $icon->setVariable( 'IconSettings', 'AdditionalThemeList', array() );
        $icon->setVariable( 'IconSettings', 'DesignTheme', array( 'admin4' => 'bootstrap-icons', 'admin4l' => 'bootstrap-icons' ) );
        $this->removeVariable( $icon, 'ClassIcons', 'Theme' );
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $saved )
        {
            list( $setting, $value ) = $saved;
            if ( $value === null )
                $this->removeVariable( $setting[0], $setting[1], $setting[2] );
            else
                $setting[0]->setVariable( $setting[1], $setting[2], $value[0] );
        }
        eZTemplate::resetInstance();

        foreach ( glob( $this->dir . '/*' ) ?: array() as $file )
            unlink( $file );
        rmdir( $this->dir );
    }

    public function testTheShippedSettingsGiveAdmin4AndAdmin4lTheBootstrapIcons()
    {
        $ini = new eZINI( 'icon.ini', 'settings', null, false, null, true );
        $this->assertSame( array( 'admin4' => 'bootstrap-icons', 'admin4l' => 'bootstrap-icons' ),
                           $ini->variable( 'IconSettings', 'DesignTheme' ) );
        $this->assertSame( 'crystal', $ini->variable( 'IconSettings', 'Theme' ), 'Theme is unchanged' );
        $this->assertDirectoryExists( 'share/icons/bootstrap-icons' );
    }

    public function testAdmin4SearchesItsThemeFirstAndTheSiteaccessThemeRightAfter()
    {
        $this->design( 'admin4' );
        $this->assertSame( array( 'bootstrap-icons', 'crystal-admin', 'crystal' ), $this->themes() );

        $this->design( 'admin4l' );
        $this->assertSame( array( 'bootstrap-icons', 'crystal-admin', 'crystal' ), $this->themes() );
    }

    public function testTheSiteaccessThemeComesBeforeTheAdditionalThemes()
    {
        eZINI::instance( 'icon.ini' )->setVariable( 'IconSettings', 'AdditionalThemeList', array( 'mytheme' ) );
        $this->design( 'admin4' );
        $this->assertSame( array( 'bootstrap-icons', 'crystal-admin', 'mytheme', 'crystal' ), $this->themes() );
    }

    public function testAnotherDesignKeepsTheme()
    {
        $this->design( 'admin3' );
        $this->assertSame( array( 'crystal-admin', 'crystal' ), $this->themes() );
    }

    public function testAnEmptyValueTurnsItOff()
    {
        eZINI::instance( 'icon.ini' )->setVariable( 'IconSettings', 'DesignTheme', array( 'admin4' => '' ) );
        $this->design( 'admin4' );
        $this->assertSame( array( 'crystal-admin', 'crystal' ), $this->themes() );

        eZINI::instance( 'icon.ini' )->setVariable( 'IconSettings', 'DesignTheme', array() );
        $this->assertSame( array( 'crystal-admin', 'crystal' ), $this->themes() );
    }

    public function testTheSameThemeIsNotSearchedTwice()
    {
        eZINI::instance( 'icon.ini' )->setVariable( 'IconSettings', 'Theme', 'bootstrap-icons' );
        eZINI::instance( 'icon.ini' )->setVariable( 'IconSettings', 'AdditionalThemeList', array( 'crystal-admin' ) );
        $this->design( 'admin4' );
        $this->assertSame( array( 'bootstrap-icons', 'crystal-admin', 'crystal' ), $this->themes(),
                           'the opt-in setting of 6.0.15 gives the same list' );
    }

    public function testATypesOwnThemeStillWins()
    {
        eZINI::instance( 'icon.ini' )->setVariable( 'ClassIcons', 'Theme', 'mytheme' );
        $this->design( 'admin4' );
        $this->assertSame( array( 'mytheme', 'crystal-admin', 'crystal' ), $this->themes( 'ClassIcons' ) );
        $this->assertSame( array( 'bootstrap-icons', 'crystal-admin', 'crystal' ), $this->themes( 'MimeIcons' ) );
    }

    public function testTheOperatorsDrawTheDesignsTheme()
    {
        $this->design( 'admin4' );
        $this->assertSame( '/share/icons/bootstrap-icons/svg/folder.svg|/share/icons/bootstrap-icons/svg/folder.svg|bootstrap-icons',
                           $this->render() );

        $this->design( 'admin3' );
        $this->assertSame( '/share/icons/crystal-admin/16x16_indexed/filesystems/folder.png|/share/icons/crystal-admin/32x32/filesystems/folder.png|crystal-admin',
                           $this->render() );
    }

    /**
     * icon_info keeps its answer per operator instance. A persistent worker (Velocity) keeps the template, and so the
     * operator, from one request to the next, and the UI switch changes the design within a session: the kept answer
     * must be the one of the current theme list, not of the design the first request had.
     */
    public function testIconInfoFollowsADesignSwitchInTheSameTemplate()
    {
        $file = $this->dir . '/icon_info.tpl';
        file_put_contents( $file, "{icon_info( 'class' ).theme}" );
        eZTemplate::resetInstance();
        $tpl = eZTemplate::factory();

        $this->design( 'admin4' );
        $this->assertSame( 'bootstrap-icons', $tpl->fetch( $file ) );

        $this->design( 'admin3' );
        $this->assertSame( 'crystal-admin', $tpl->fetch( $file ) );

        $this->design( 'admin4' );
        $this->assertSame( 'bootstrap-icons', $tpl->fetch( $file ) );
    }

    /**
     * An extension's ClassMap[] override names a crystal path (cjw_newsletter: filesystems/folder_txt.png). The content
     * tree builds its icon URLs from icon_info's map and theme path, so under bootstrap-icons that entry would be a
     * 404; it is left out there, and kept where the theme has the file.
     */
    public function testIconInfoLeavesOutAnOverrideTheThemeHasNoFileFor()
    {
        $icon = eZINI::instance( 'icon.ini' );
        $hadMap = $icon->hasVariable( 'ClassIcons', 'ClassMap' );
        $savedMap = $hadMap ? $icon->variable( 'ClassIcons', 'ClassMap' ) : null;
        $icon->setVariable( 'ClassIcons', 'ClassMap', array( 'w567_crystal_only' => 'filesystems/folder_txt.png' ) );
        try
        {
            $file = $this->dir . '/icon_info_map.tpl';
            file_put_contents( $file, "{def \$i = icon_info( 'class' )}{\$i.theme}:{cond( is_set( \$i.icons.w567_crystal_only ), \$i.icons.w567_crystal_only, 'none' )}" );

            $this->design( 'admin4' );
            eZTemplate::resetInstance();
            $this->assertSame( 'bootstrap-icons:none', eZTemplate::factory()->fetch( $file ) );

            $this->design( 'admin3' );
            eZTemplate::resetInstance();
            $this->assertSame( 'crystal-admin:filesystems/folder_txt.png', eZTemplate::factory()->fetch( $file ) );
        }
        finally
        {
            if ( $hadMap )
                $icon->setVariable( 'ClassIcons', 'ClassMap', $savedMap );
            else
                $this->removeVariable( $icon, 'ClassIcons', 'ClassMap' );
        }
    }

    private function design( $design )
    {
        eZINI::instance()->setVariable( 'DesignSettings', 'SiteDesign', $design );
    }

    private function themes( $configGroup = 'ClassIcons' )
    {
        return eZWordToImageOperator::themeList( eZINI::instance( 'icon.ini' ), $configGroup );
    }

    private function render()
    {
        $file = $this->dir . '/icons.tpl';
        file_put_contents( $file, "{'folder'|class_icon( 'small', '', true() )}|{'folder'|class_icon( 'normal', '', true() )}|{icon_info( 'class' ).theme}" );

        // icon_info keeps its answer per operator instance, that is per template instance.
        eZTemplate::resetInstance();
        return eZTemplate::factory()->fetch( $file );
    }

    private function removeVariable( $ini, $group, $variable )
    {
        if ( $ini->hasVariable( $group, $variable ) )
            $ini->removeSetting( $group, $variable );
    }
}

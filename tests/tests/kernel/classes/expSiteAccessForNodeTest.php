<?php
/**
 * The siteaccess a node belongs to (expSiteAccessForNode), as the version preview and the "View on site" link choose
 * it: content root containment, the most specific root, the language, the fallback to DefaultAccess, path strings,
 * the host comparison that decides between a preview on the siteaccess's host and one under /<siteaccess>/, and the
 * switches of site.ini [SiteAccessSettings].
 *
 * No database, no files except the settings and code read by the last tests.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/expSiteAccessForNodeTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

#[\PHPUnit\Framework\Attributes\Group('kernel')]
class expSiteAccessForNodeTest extends PHPUnit\Framework\TestCase
{
    /**
     * One installation, three sites: the main site (root 2) in German and English, a wedding site under node 60 in
     * German, a forum under node 70 in English, and a German forum site under node 70 too.
     */
    private static function candidates()
    {
        return array(
            'main_ger' => array( 'root_path' => array( 1, 2 ), 'locale' => 'ger-DE', 'languages' => array( 'ger-DE', 'eng-GB' ) ),
            'main_eng' => array( 'root_path' => array( 1, 2 ), 'locale' => 'eng-GB', 'languages' => array( 'eng-GB', 'ger-DE' ) ),
            'wedding_ger' => array( 'root_path' => array( 1, 2, 60 ), 'locale' => 'ger-DE', 'languages' => array( 'ger-DE' ) ),
            'forum_eng' => array( 'root_path' => array( 1, 2, 70 ), 'locale' => 'eng-GB', 'languages' => array( 'eng-GB' ) ),
            'forum_ger' => array( 'root_path' => array( 1, 2, 70 ), 'locale' => 'ger-DE', 'languages' => array( 'ger-DE', 'eng-GB' ) ),
        );
    }

    public function testANodeUnderAMicrositeGetsTheMicrositeNotTheMainSite()
    {
        $chosen = expSiteAccessForNode::resolve( array( 1, 2, 60, 61, 62 ), 'ger-DE', self::candidates(), 'main_ger' );
        $this->assertSame( 'wedding_ger', $chosen['siteaccess'] );
        $this->assertSame( expSiteAccessForNode::REASON_TREE, $chosen['reason'] );
        $this->assertSame( 3, $chosen['depth'] );
    }

    public function testTheMostSpecificRootWinsWhateverTheOrderOfTheList()
    {
        $reversed = array_reverse( self::candidates(), true );
        $this->assertSame( 'wedding_ger', expSiteAccessForNode::resolve( array( 1, 2, 60, 61 ), 'ger-DE', $reversed, 'main_ger' )['siteaccess'] );
    }

    public function testTheRootNodeItselfBelongsToItsSite()
    {
        $this->assertSame( 'wedding_ger', expSiteAccessForNode::resolve( array( 1, 2, 60 ), 'ger-DE', self::candidates(), 'main_ger' )['siteaccess'] );
    }

    public function testBetweenEquallySpecificRootsTheLanguageDecides()
    {
        $this->assertSame( 'forum_eng', expSiteAccessForNode::resolve( array( 1, 2, 70, 71 ), 'eng-GB', self::candidates(), 'main_ger' )['siteaccess'] );
        $this->assertSame( 'forum_ger', expSiteAccessForNode::resolve( array( 1, 2, 70, 71 ), 'ger-DE', self::candidates(), 'main_ger' )['siteaccess'] );
        $this->assertSame( 'main_eng', expSiteAccessForNode::resolve( array( 1, 2, 80 ), 'eng-GB', self::candidates(), 'main_ger' )['siteaccess'] );
        $this->assertSame( 'main_ger', expSiteAccessForNode::resolve( array( 1, 2, 80 ), 'ger-DE', self::candidates(), 'main_eng' )['siteaccess'] );
    }

    public function testTheLocaleComesBeforeTheLanguageListAndTheListComesBeforeNothing()
    {
        $candidates = array(
            'listed_second' => array( 'root_path' => array( 1, 2 ), 'locale' => 'eng-GB', 'languages' => array( 'eng-GB', 'fre-FR' ) ),
            'listed_first' => array( 'root_path' => array( 1, 2 ), 'locale' => 'eng-GB', 'languages' => array( 'fre-FR' ) ),
            'locale' => array( 'root_path' => array( 1, 2 ), 'locale' => 'fre-FR', 'languages' => array() ),
        );
        $this->assertSame( 'locale', expSiteAccessForNode::resolve( array( 1, 2, 5 ), 'fre-FR', $candidates, 'x' )['siteaccess'] );
        unset( $candidates['locale'] );
        $this->assertSame( 'listed_first', expSiteAccessForNode::resolve( array( 1, 2, 5 ), 'fre-FR', $candidates, 'x' )['siteaccess'] );
        $this->assertSame( 0, expSiteAccessForNode::languageRank( 'fre-FR', array( 'locale' => 'fre-FR' ) ) );
        $this->assertSame( 2, expSiteAccessForNode::languageRank( 'fre-FR', array( 'locale' => 'eng-GB', 'languages' => array( 'eng-GB', 'fre-FR' ) ) ) );
        $this->assertSame( 1000, expSiteAccessForNode::languageRank( 'nor-NO', array( 'locale' => 'eng-GB', 'languages' => array( 'eng-GB' ) ) ) );
    }

    public function testAMicrositeWinsEvenWithoutTheLanguageOverTheMainSiteWithIt()
    {
        $this->assertSame( 'wedding_ger', expSiteAccessForNode::resolve( array( 1, 2, 60, 61 ), 'eng-GB', self::candidates(), 'main_ger' )['siteaccess'] );
    }

    public function testANodeNoSiteaccessContainsGetsDefaultAccess()
    {
        $chosen = expSiteAccessForNode::resolve( array( 1, 43, 51 ), 'ger-DE', self::candidates(), 'main_ger' );
        $this->assertSame( array( 'siteaccess' => 'main_ger', 'reason' => expSiteAccessForNode::REASON_DEFAULT, 'depth' => 0 ), $chosen );
        $this->assertSame( 'main_ger', expSiteAccessForNode::resolve( array( 1, 2, 60 ), 'ger-DE', array(), 'main_ger' )['siteaccess'] );
    }

    public function testARootIsMatchedByItsWholePathNotByAnIdThatAppearsElsewhere()
    {
        // node 60 under 1/5 is another node path than 1/2/60; a root without a path (its node is gone) never matches
        $candidates = array( 'wedding' => array( 'root_path' => array( 1, 2, 60 ) ), 'gone' => array( 'root_path' => array() ) );
        $this->assertSame( expSiteAccessForNode::REASON_DEFAULT, expSiteAccessForNode::resolve( array( 1, 5, 60, 61 ), 'ger-DE', $candidates, 'd' )['reason'] );
        $this->assertSame( expSiteAccessForNode::REASON_DEFAULT, expSiteAccessForNode::resolve( array( 1, 2 ), 'ger-DE', $candidates, 'd' )['reason'] );
    }

    public function testAPathStringGivesItsIdsAndTheEmptyEndOfAnUnpublishedNodeIsDropped()
    {
        $this->assertSame( array( 1, 2, 60, 9316 ), expSiteAccessForNode::pathOf( '/1/2/60/9316/' ) );
        $this->assertSame( array( 1, 2, 60 ), expSiteAccessForNode::pathOf( '/1/2/60//' ) );
        $this->assertSame( array(), expSiteAccessForNode::pathOf( '' ) );
        $this->assertSame( array( 1, 2 ), expSiteAccessForNode::pathOf( '/1/x/2/0/' ) );
    }

    public function testAnotherHostIsAPreviewOnThatHostTheSameHostIsAPreviewUnderTheSiteaccessPath()
    {
        $this->assertTrue( expSiteAccessForNode::isOtherHost( 'https://wedding.example.com/', 'admin.example.com' ) );
        $this->assertFalse( expSiteAccessForNode::isOtherHost( 'https://admin.example.com/main_ger/', 'admin.example.com' ) );
        $this->assertFalse( expSiteAccessForNode::isOtherHost( 'https://ADMIN.example.com:8443/eng/', 'admin.example.com:8443' ) );
        $this->assertFalse( expSiteAccessForNode::isOtherHost( 'https://admin.example.com:8080/', 'admin.example.com' ) );
        $this->assertFalse( expSiteAccessForNode::isOtherHost( '/main_ger/', 'admin.example.com' ) );
        $this->assertFalse( expSiteAccessForNode::isOtherHost( 'https://wedding.example.com/', '' ) );
    }

    public function testTheSwitchesAreOnUnlessTurnedOff()
    {
        foreach ( array( null, 'enabled', 'true', '1', '' ) as $value )
            $this->assertTrue( expSiteAccessForNode::switchOn( $value ), var_export( $value, true ) );
        foreach ( array( 'disabled', 'false', '0', 'Disabled', 'no' ) as $value )
            $this->assertFalse( expSiteAccessForNode::switchOn( $value ), $value );
    }

    public function testTheSettingsAreDocumentedWithTheirDefaults()
    {
        $site = file_get_contents( __DIR__ . '/../../../../settings/site.ini' );
        foreach ( array( "\nPreviewInOwningSiteAccess=enabled\n", "\nPreviewOnOwningHost=enabled\n", "\nPreviewTokenLifetime=300\n" ) as $line )
            $this->assertStringContainsString( $line, $site );
        $this->assertStringContainsString( "\nPolicyOmitList[]=content/hostpreview\n", $site );
    }

    public function testThePreviewFrameAndTheViewOnSiteLinkUseTheResolver()
    {
        $root = __DIR__ . '/../../../../';
        $frame = file_get_contents( $root . 'kernel/content/versionviewframe.php' );
        $this->assertStringContainsString( 'expSiteAccessForNode::forNode(', $frame );
        // an explicit choice in the form is kept: the resolver runs only when the form chose nothing
        $this->assertMatchesRegularExpression( '/if \( !\$siteaccess && isset\( \$node \) && class_exists\( \'expSiteAccessForNode\' \)/', $frame );
        $view = file_get_contents( $root . 'kernel/private/classes/views/content/versionview.php' );
        $this->assertStringContainsString( 'self::hostPreviewURL(', $view );
        $this->assertStringContainsString( "'/content/hostpreview/'", $view );
        $this->assertStringContainsString( '$site_url = exp_node_site_url( $node )}', file_get_contents( $root . 'design/admin4/templates/node/view/a4_full_body.tpl' ) );
        $this->assertStringContainsString( "'operator_names' => array( 'exp_node_site_url' )", file_get_contents( $root . 'kernel/common/eztemplateautoload.php' ) );
    }
}

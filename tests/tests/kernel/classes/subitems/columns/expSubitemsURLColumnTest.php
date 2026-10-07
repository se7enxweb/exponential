<?php
/**
 * The URLs, Location and SEO columns: aliases, the public address, the page title in the browser.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expSubitemsColumnsTestCase.php';

class expSubitemsURLColumnTest extends expSubitemsColumnsTestCase
{
    /** The node that owns the root element of ezurlalias_ml (parent 0, text ''), or null. */
    protected function rootElementNode()
    {
        $rows = eZPersistentObject::fetchObjectList( eZURLAliasML::definition(), null,
                                                     array( 'parent' => 0, 'text' => '', 'is_original' => 1, 'is_alias' => 0 ), null, null, false );
        foreach ( is_array( $rows ) ? $rows : array() as $row )
        {
            if ( preg_match( '/^eznode:(\d+)$/', $row['action'], $m ) )
                return eZContentObjectTreeNode::fetch( (int)$m[1] );
        }
        return null;
    }

    public function testSystemURL()
    {
        $this->assertSame( 'content/view/full/2', $this->value( 'systemurl', $this->contentRoot() ) );
        $user = $this->adminUserNode();
        $this->assertSame( 'content/view/full/' . $user->attribute( 'node_id' ), $this->value( 'systemurl', $user ) );
    }

    public function testURLAliasOfOrdinaryNodes()
    {
        $user = $this->adminUserNode();
        $this->assertSame( eZURLAliasML::cleanURL( $user->attribute( 'url_alias' ) ), $this->value( 'urlalias', $user ) );
        $this->assertNotSame( '', $this->value( 'urlalias', $user ) );
        $parts = explode( '/', $this->value( 'urlalias', $user ) );
        $this->assertSame( end( $parts ), $this->value( 'urlslug', $user ) );
        $media = $this->mediaRoot();
        $this->assertSame( eZURLAliasML::cleanURL( $media->attribute( 'url_alias' ) ), $this->value( 'urlalias', $media ) );
    }

    /**
     * Every node has an alias in the column, never an empty cell: the node at the site root (it owns
     * the root element) shows "/", the content root its own alias when the root element is another's.
     * On alpha that is node 89, "Fit & Healthy": its kernel url_alias is '' (reported empty by the list).
     */
    public function testURLAliasIsNeverEmpty()
    {
        $offset = 0;
        do
        {
            $nodes = eZContentObjectTreeNode::subTreeByNodeID( array( 'Limit' => 100, 'Offset' => $offset, 'IgnoreVisibility' => true,
                                                                     'SortBy' => array( 'node_id', true ) ), 1 );
            foreach ( $nodes as $node )
            {
                $alias = $this->value( 'urlalias', $node );
                $this->assertIsString( $alias );
                $this->assertNotSame( '', $alias, 'node ' . $node->attribute( 'node_id' ) . ' has an alias' );
            }
            $offset += 100;
            expSubitemsFieldColumn::resetMemo();
        } while ( count( $nodes ) === 100 && $offset < 5000 );
    }

    public function testTheNodeAtTheSiteRootShowsSlash()
    {
        $node = $this->rootElementNode();
        if ( !$node )
            $this->markTestSkipped( 'no node owns the root URL element' );
        $this->assertSame( '/', $this->value( 'urlalias', $node ) );
        $this->assertNull( $this->value( 'urlslug', $node ) );
        $this->assertContains( '/', $this->value( 'allaliases', $node ) );
        $this->assertSame( '/', $this->value( 'publicpath', $node ) );
    }

    /**
     * Node 89 of the alpha database, "Fit & Healthy": an "&" in the name, and a named alias at the top level that
     * its children hang below (the root element belongs to the content root, node 2).
     */
    public function testFitAndHealthyNode()
    {
        $node = eZContentObjectTreeNode::fetch( 89 );
        if ( !$node || $node->getName() !== 'Fit & Healthy' )
            $this->markTestSkipped( 'not the alpha database' );
        $this->assertSame( 'fit-healthy', (string)$node->attribute( 'url_alias' ) );
        $this->assertSame( eZURLAliasML::cleanURL( $node->attribute( 'url_alias' ) ), $this->value( 'urlalias', $node ) );
        // the named alias its children hang below (more appear when the node is translated or was moved,
        // so the list is checked for it, not for being only it)
        $aliases = $this->value( 'allaliases', $node );
        $this->assertContains( 'fit-healthy', $aliases );
        $this->assertSame( count( $aliases ), $this->value( 'aliascount', $node ) );
        // the public title: the page title equals SiteName ("Fit & Healthy"), so it stands alone
        $this->assertSame( 'Fit & Healthy', $this->value( 'pagetitle', $node ) );
    }

    public function testContentRootAliasWhenTheRootElementIsAnothers()
    {
        $owner = $this->rootElementNode();
        $root = $this->contentRoot();
        if ( !$owner || (int)$owner->attribute( 'node_id' ) === (int)$root->attribute( 'node_id' ) )
        {
            $this->assertSame( '/', $this->value( 'urlalias', $root ), 'the content root owns the root element' );
            return;
        }
        $own = $this->value( 'allaliases', $root );
        $this->assertNotEmpty( $own );
        $this->assertSame( $own[0], $this->value( 'urlalias', $root ) );
    }

    public function testPublicPathAndURL()
    {
        $ini = expSubitemsURLColumn::publicSiteIni();
        if ( !$ini )
            $this->markTestSkipped( 'no public siteaccess' );
        $prefix = $ini->hasVariable( 'SiteAccessSettings', 'PathPrefix' ) ? eZURLAliasML::cleanURL( $ini->variable( 'SiteAccessSettings', 'PathPrefix' ) ) : '';
        $siteURL = trim( $ini->variable( 'SiteSettings', 'SiteURL' ), '/' );

        $user = $this->adminUserNode();
        $full = eZURLAliasML::cleanURL( $user->pathWithNames() );
        $path = $this->value( 'publicpath', $user );
        if ( $prefix === '' )
            $this->assertSame( $full, $path );
        else
            $this->assertStringEndsWith( $path, $full );
        $url = $this->value( 'publicurl', $user );
        $this->assertStringEndsWith( '/' . $path, $url );
        $this->assertStringContainsString( $siteURL, $url );
        $this->assertMatchesRegularExpression( '#^https?://#', $url );

        // a path below the prefix loses it, the prefix itself becomes the root
        if ( $prefix !== '' )
        {
            $this->assertSame( 'x/y', expSubitemsURLColumn::publicPath( $user, $ini, $prefix . '/x/y' ) );
            $this->assertSame( '', expSubitemsURLColumn::publicPath( $user, $ini, $prefix ) );
            $this->assertSame( 'other/x', expSubitemsURLColumn::publicPath( $user, $ini, 'other/x' ) );
        }
    }

    public function testUnknownSiteAccessGivesNull()
    {
        $this->assertNull( expSubitemsURLColumn::publicSiteIni( 'no_such_siteaccess_x' ) );
        $this->assertNull( expSubitemsURLColumn::publicSiteIni( '../etc' ) );
        $column = new expSubitemsURLColumn( 'x', array( 'Field' => 'public_url', 'Type' => 'link', 'SiteAccess' => 'no_such_siteaccess_x' ) );
        $this->assertNull( $column->value( $this->contentRoot() ) );
    }

    public function testAliasCounts()
    {
        $user = $this->adminUserNode();
        $rows = eZURLAliasML::fetchByAction( 'eznode', $user->attribute( 'node_id' ), false, false, true );
        $originals = $history = $custom = 0;
        foreach ( $rows as $row )
        {
            if ( $row->attribute( 'is_original' ) == 1 && $row->attribute( 'is_alias' ) == 0 ) $originals++;
            if ( $row->attribute( 'is_original' ) == 0 ) $history++;
            if ( $row->attribute( 'is_original' ) == 1 && $row->attribute( 'is_alias' ) == 1 ) $custom++;
        }
        $this->assertGreaterThanOrEqual( 1, $originals );
        $this->assertSame( $originals, $this->value( 'aliascount', $user ) );
        $this->assertSame( $history, $this->value( 'historycount', $user ) );
        $this->assertSame( $custom, $this->value( 'customaliascount', $user ) );
        $this->assertCount( $custom, $this->value( 'customaliases', $user ) );
    }

    public function testLocations()
    {
        $user = $this->adminUserNode();
        $count = count( eZContentObjectTreeNode::fetchByContentObjectID( $user->attribute( 'contentobject_id' ) ) );
        $this->assertSame( $count, $this->value( 'locationcount', $user ) );
        $this->assertCount( $count - 1, $this->value( 'otherlocations', $user ) );
        $this->assertNull( $this->value( 'mainlocation', $user ), 'the main location has no other main location' );
    }

    public function testPageTitleNameFormat()
    {
        $ini = expSubitemsURLColumn::publicSiteIni();
        $siteName = $ini ? $ini->variable( 'SiteSettings', 'SiteName' ) : eZINI::instance()->variable( 'SiteSettings', 'SiteName' );
        $media = $this->mediaRoot();
        $this->assertSame( $media->getName() . ' - ' . $siteName, $this->value( 'pagetitle', $media ) );
        $this->assertSame( mb_strlen( $media->getName() . ' - ' . $siteName ), $this->value( 'titlelength', $media ) );
        $this->assertSame( 'name', $this->column( 'pagetitle' )->setting( 'TitleFormat' ) );
    }

    public function testPageTitlePathFormat()
    {
        $ini = expSubitemsURLColumn::publicSiteIni();
        $siteName = $ini ? $ini->variable( 'SiteSettings', 'SiteName' ) : eZINI::instance()->variable( 'SiteSettings', 'SiteName' );
        $user = $this->adminUserNode();
        $names = array( $user->getName() );
        foreach ( array_reverse( $user->fetchPath() ) as $ancestor )
            $names[] = $ancestor->getName();
        $this->assertSame( implode( ' / ', $names ) . ' - ' . $siteName, expSubitemsSEOColumn::pageTitle( $user, 'path' ) );
        $column = new expSubitemsSEOColumn( 'x', array( 'Field' => 'page_title', 'TitleFormat' => 'path', 'Type' => 'text' ) );
        $this->assertSame( implode( ' / ', $names ) . ' - ' . $siteName, $column->value( $user ) );
    }

    public function testMetaColumnsWithoutMetadata()
    {
        // the media root (a folder) has no xrowmetadata attribute
        $this->assertNull( $this->value( 'metatitle', $this->mediaRoot() ) );
        $this->assertNull( $this->value( 'metadescription', $this->mediaRoot() ) );
        $this->assertNull( $this->value( 'metakeywords', $this->mediaRoot() ) );
    }
}

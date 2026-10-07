<?php
/**
 * The history step of eZURLAliasML::storePath() on the SQL databases, against the installation's own database:
 * when a node gets another name, its old URL alias entry becomes a history entry (is_original = 0) that links to
 * the new one, so the old address answers with a redirect to the new address instead of staying an original
 * address of its own.
 *
 *  - Renaming twice (A, B, C): A and B both link to C and redirect to it.
 *  - Renaming back (C to A): A is the original again, B and C link to it, and no text has two rows.
 *  - Two translations: renaming one keeps the other translation's alias as its original address.
 *  - A custom alias of the node and a global alias of it are not touched by a rename.
 *
 * Runs on the admin siteaccess through ezpLiveInstallation (skipped where there is no installation, as on CI);
 * every object lives below a throwaway folder that tearDownAfterClass() removes.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/contentmodel/expContentModelLiveTestCase.php';

class eZURLAliasMLHistoryLiveTest extends expContentModelLiveTestCase
{
    /** @var string[] ids of the global alias rows made by a test, removed in tearDown() */
    private $globalAliases = array();

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if ( eZDB::instance()->databaseName() === 'mongo' )
            self::markTestSkipped( 'the SQL history step; MongoDB has its own branch' );
    }

    protected function tearDown(): void
    {
        $db = eZDB::instance();
        foreach ( $this->globalAliases as $action )
            $db->query( "DELETE FROM ezurlalias_ml WHERE parent = 0 AND is_alias = 1 AND action = '" . $db->escapeString( $action ) . "'" );
        $this->globalAliases = array();
        parent::tearDown();
    }

    private static function rows( $nodeID )
    {
        $rows = eZDB::instance()->arrayQuery( "SELECT * FROM ezurlalias_ml WHERE action = 'eznode:" . (int)$nodeID . "' ORDER BY id" );
        $byText = array();
        foreach ( $rows as $row )
        {
            foreach ( array( 'id', 'link', 'parent', 'lang_mask', 'is_original', 'is_alias', 'alias_redirects' ) as $int )
                $row[$int] = (int)$row[$int];
            if ( isset( $byText[$row['text']] ) )
                throw new RuntimeException( "two alias rows with the text '{$row['text']}' for node $nodeID" );
            $byText[$row['text']] = $row;
        }
        return $byText;
    }

    private static function rename( $nodeID, $name, $language = false )
    {
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        $params = array( 'attributes' => array( 'name' => $name ) );
        if ( $language )
            $params['language'] = $language;
        if ( !eZContentFunctions::updateAndPublishObject( $node->object(), $params ) )
            throw new RuntimeException( "could not rename node $nodeID to '$name'" );
        eZContentObject::clearCache();
    }

    private static function parentPath( $nodeID )
    {
        $parent = eZURLAliasML::fetchByAction( 'eznode', (int)$nodeID );
        return $parent[0]->getPath();
    }

    /**
     * Translates $path as a request would and returns what translate() gave: the target of a redirect, true for
     * an address that is served as it is, false for one that is unknown.
     */
    private static function translate( $path )
    {
        $uri = new eZURI( $path );
        $result = eZURLAliasML::translate( $uri );
        return $result === true || $result === false ? $result : array( (string)$result, $uri->uriString() );
    }

    private function assertHistoryOf( array $row, array $original )
    {
        $this->assertSame( 0, $row['is_original'], "'{$row['text']}' is a history entry" );
        $this->assertSame( 0, $row['is_alias'] );
        $this->assertSame( $original['id'], $row['link'], "'{$row['text']}' links to '{$original['text']}'" );
        $this->assertNotSame( $original['id'], $row['id'], "'{$row['text']}' has an id of its own" );
    }

    public function testRenamingTwiceRedirectsBothOldAddressesToTheNewOne()
    {
        $folder = static::folder( static::$root['node'], 'Hist A' );
        static::rename( $folder['node'], 'Hist B' );
        $rows = static::rows( $folder['node'] );
        $this->assertSame( array( 'hist-a', 'hist-b' ), static::sortedKeys( $rows ) );
        $this->assertSame( 1, $rows['hist-b']['is_original'] );
        $this->assertHistoryOf( $rows['hist-a'], $rows['hist-b'] );

        static::rename( $folder['node'], 'Hist C' );
        $rows = static::rows( $folder['node'] );
        $this->assertSame( array( 'hist-a', 'hist-b', 'hist-c' ), static::sortedKeys( $rows ) );
        $this->assertSame( 1, $rows['hist-c']['is_original'] );
        $this->assertSame( 0, $rows['hist-c']['is_alias'] );
        $this->assertHistoryOf( $rows['hist-a'], $rows['hist-c'] );
        $this->assertHistoryOf( $rows['hist-b'], $rows['hist-c'] );

        $parentPath = static::parentPath( static::$root['node'] );
        foreach ( array( 'hist-a', 'hist-b' ) as $old )
            $this->assertSame( array( "$parentPath/hist-c", 'error/301' ), static::translate( "$parentPath/$old" ), "/$old redirects to /hist-c" );
        $this->assertTrue( static::translate( "$parentPath/hist-c" ) );
        $this->assertSame( "$parentPath/hist-c", eZContentObjectTreeNode::fetch( $folder['node'] )->urlAlias() );
    }

    public function testRenamingBackMakesTheFirstNameTheOriginalAgain()
    {
        $folder = static::folder( static::$root['node'], 'Back A' );
        static::rename( $folder['node'], 'Back B' );
        static::rename( $folder['node'], 'Back C' );
        static::rename( $folder['node'], 'Back A' );

        $rows = static::rows( $folder['node'] ); // throws on a duplicate text
        $this->assertSame( array( 'back-a', 'back-b', 'back-c' ), static::sortedKeys( $rows ) );
        $this->assertSame( 1, $rows['back-a']['is_original'] );
        $this->assertSame( $rows['back-a']['id'], $rows['back-a']['link'] );
        $this->assertHistoryOf( $rows['back-b'], $rows['back-a'] );
        $this->assertHistoryOf( $rows['back-c'], $rows['back-a'] );
        $originals = array_filter( $rows, function ( $r ) { return $r['is_original'] === 1 && $r['is_alias'] === 0; } );
        $this->assertCount( 1, $originals );

        $parentPath = static::parentPath( static::$root['node'] );
        $this->assertTrue( static::translate( "$parentPath/back-a" ) );
        $this->assertSame( array( "$parentPath/back-a", 'error/301' ), static::translate( "$parentPath/back-c" ) );
    }

    public function testRenamingOneTranslationKeepsTheOtherTranslationsAlias()
    {
        $object = eZContentObject::fetch( static::$root['object'] );
        $first = eZContentLanguage::fetch( $object->attribute( 'initial_language_id' ) );
        $second = null;
        foreach ( eZContentLanguage::fetchList() as $language )
        {
            if ( (int)$language->attribute( 'id' ) !== (int)$first->attribute( 'id' ) )
            {
                $second = $language;
                break;
            }
        }
        if ( !$second )
            $this->markTestSkipped( 'needs a second content language' );
        $firstID = (int)$first->attribute( 'id' );
        $secondID = (int)$second->attribute( 'id' );

        // Both translations named alike share one composite row
        $folder = static::folder( static::$root['node'], 'Lang Same' );
        static::rename( $folder['node'], 'Lang Same', $second->attribute( 'locale' ) );
        $rows = static::rows( $folder['node'] );
        $this->assertSame( array( 'lang-same' ), array_keys( $rows ) );
        $this->assertSame( $firstID | $secondID, $rows['lang-same']['lang_mask'] & ~1 );

        // Renaming the second translation takes only its language bit off the shared row
        static::rename( $folder['node'], 'Lang Second', $second->attribute( 'locale' ) );
        $rows = static::rows( $folder['node'] );
        $this->assertSame( array( 'lang-same', 'lang-second' ), static::sortedKeys( $rows ) );
        $this->assertSame( 1, $rows['lang-same']['is_original'], 'the first translation keeps its address' );
        $this->assertSame( $firstID, $rows['lang-same']['lang_mask'] & ~1 );
        $this->assertSame( 1, $rows['lang-second']['is_original'] );
        $this->assertSame( $secondID, $rows['lang-second']['lang_mask'] & ~1 );

        // Renaming the second translation again makes its own old row history; the first is untouched
        static::rename( $folder['node'], 'Lang Second Again', $second->attribute( 'locale' ) );
        $rows = static::rows( $folder['node'] );
        $this->assertSame( array( 'lang-same', 'lang-second', 'lang-second-again' ), static::sortedKeys( $rows ) );
        $this->assertSame( 1, $rows['lang-same']['is_original'] );
        $this->assertSame( $firstID, $rows['lang-same']['lang_mask'] & ~1 );
        $this->assertSame( 1, $rows['lang-second-again']['is_original'] );
        $this->assertHistoryOf( $rows['lang-second'], $rows['lang-second-again'] );

        // Renaming the first translation leaves the second translation's address alone
        static::rename( $folder['node'], 'Lang First', $first->attribute( 'locale' ) );
        $rows = static::rows( $folder['node'] );
        $this->assertSame( 1, $rows['lang-first']['is_original'] );
        $this->assertHistoryOf( $rows['lang-same'], $rows['lang-first'] );
        $this->assertSame( 1, $rows['lang-second-again']['is_original'] );
        $this->assertSame( $secondID, $rows['lang-second-again']['lang_mask'] & ~1 );
    }

    public function testCustomAndGlobalAliasesAreNotTouchedByARename()
    {
        $folder = static::folder( static::$root['node'], 'Alias Owner' );
        $action = 'eznode:' . $folder['node'];
        $object = eZContentObject::fetch( $folder['object'] );
        $language = eZContentLanguage::fetch( $object->attribute( 'initial_language_id' ) );
        // As content/urlalias and content/urlalias_global store them: always available like the object
        $always = (int)$object->attribute( 'language_mask' ) & 1;
        $system = static::rows( $folder['node'] )['alias-owner'];

        // A custom alias next to the node (as content/urlalias stores it) and a global alias at the top
        $custom = eZURLAliasML::storePath( 'alias-owner-custom', $action, $language, $system['id'], $always, $system['parent'], true, false, false, true );
        $this->assertTrue( $custom['status'] );
        $global = 'k1c-urlhist-global-' . $folder['node'];
        $this->globalAliases[] = $action;
        $result = eZURLAliasML::storePath( $global, $action, $language, true, $always, 0, true, false, false, true );
        $this->assertTrue( $result['status'] );

        $before = static::rows( $folder['node'] );
        $this->assertSame( 1, $before['alias-owner-custom']['is_alias'] );
        $this->assertSame( 1, $before[$global]['is_alias'] );

        static::rename( $folder['node'], 'Alias Owner Renamed' );
        $after = static::rows( $folder['node'] );
        $this->assertSame( $before['alias-owner-custom'], $after['alias-owner-custom'], 'the custom alias row is unchanged' );
        $this->assertSame( $before[$global], $after[$global], 'the global alias row is unchanged' );
        $this->assertSame( 1, $after['alias-owner-renamed']['is_original'] );
        $this->assertHistoryOf( $after['alias-owner'], $after['alias-owner-renamed'] );
    }

    private static function sortedKeys( array $rows )
    {
        $keys = array_keys( $rows );
        sort( $keys );
        return $keys;
    }
}

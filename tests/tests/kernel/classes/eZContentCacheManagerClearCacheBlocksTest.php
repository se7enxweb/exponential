<?php
/**
 * viewcache.ini [<class>] ClearCacheBlocks=disabled (6.0.15): a change of objects that are all of such classes no
 * longer expires every ordinary cache-block of the installation, as the setting has always said; before, only the
 * subtree_expiry blocks were kept. Found on se7enx.com, where each centre comment (a content object shown only by a
 * module view) expired every cache-block of the installation, every 10-40 s.
 *
 * No database: the pure decision and the class lookup against a stand-in for viewcache.ini.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/../../../../kernel/classes/ezcontentcachemanager.php';

class eZContentCacheManagerClearCacheBlocksTestIni
{
    private $values;
    public function __construct( array $values ) { $this->values = $values; }
    public function hasVariable( $group, $name ) { return isset( $this->values[$group][$name] ); }
    public function variable( $group, $name ) { return $this->values[$group][$name]; }
}

class eZContentCacheManagerClearCacheBlocksTest extends PHPUnit\Framework\TestCase
{
    private function keeps()
    {
        $ini = new eZContentCacheManagerClearCacheBlocksTestIni( array( 'centre_comment' => array( 'ClearCacheBlocks' => 'disabled' ),
                                                                        'centre_card' => array( 'ClearCacheBlocks' => 'disabled' ),
                                                                        'article' => array( 'ClearCacheBlocks' => 'enabled' ) ) );
        return function ( $c ) use ( $ini ) { return eZContentCacheManager::classKeepsCacheBlocks( $ini, $c ); };
    }

    public function testAChangeOfOnlyClassesThatKeepTheBlocksExpiresNone()
    {
        $this->assertFalse( eZContentCacheManager::changeExpiresAllCacheBlocks( array( 'centre_comment' ), $this->keeps() ) );
        $this->assertFalse( eZContentCacheManager::changeExpiresAllCacheBlocks( array( 'centre_comment', 'centre_card' ), $this->keeps() ) );
    }

    public function testOneObjectOfAnotherClassExpiresAllAsBefore()
    {
        $this->assertTrue( eZContentCacheManager::changeExpiresAllCacheBlocks( array( 'centre_comment', 'folder' ), $this->keeps() ) );
        $this->assertTrue( eZContentCacheManager::changeExpiresAllCacheBlocks( array( 'article' ), $this->keeps() ), 'enabled is not disabled' );
    }

    public function testAnUnknownObjectOrNothingKnownExpiresAll()
    {
        $this->assertTrue( eZContentCacheManager::changeExpiresAllCacheBlocks( array( 'centre_comment', null ), $this->keeps() ) );
        $this->assertTrue( eZContentCacheManager::changeExpiresAllCacheBlocks( array(), $this->keeps() ) );
    }

    public function testTheClassLookupNeedsTheExactValueDisabled()
    {
        $ini = new eZContentCacheManagerClearCacheBlocksTestIni( array( 'a' => array( 'ClearCacheBlocks' => 'disabled' ), 'b' => array( 'ClearCacheBlocks' => 'Disabled ' ) ) );
        $this->assertTrue( eZContentCacheManager::classKeepsCacheBlocks( $ini, 'a' ) );
        $this->assertFalse( eZContentCacheManager::classKeepsCacheBlocks( $ini, 'b' ) );
        $this->assertFalse( eZContentCacheManager::classKeepsCacheBlocks( $ini, 'c' ) );
        $this->assertFalse( eZContentCacheManager::classKeepsCacheBlocks( $ini, null ) );
        $this->assertFalse( eZContentCacheManager::classKeepsCacheBlocks( $ini, '' ) );
    }
}

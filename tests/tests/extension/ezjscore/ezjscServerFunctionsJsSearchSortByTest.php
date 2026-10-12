<?php
/**
 * The sort order of the ezjscore search for eZ Find: ezfind.ini [SearchSettings] SortBy[] when it is set, newest
 * first (the order the search always used) without it, invalid entries left out. ezjscore ships the setting with
 * SortBy[published]=desc. The settings are set in-process and put back afterwards. No database.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezjscore/ezjscServerFunctionsJsSearchSortByTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commits 3d2d337b0d and 3af1ec3aee), Copyright (C) Mugo Web, GPL v2
 */

class ezjscServerFunctionsJsSearchSortByTest extends PHPUnit\Framework\TestCase
{
    private $saved;
    private $had;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
        $ini = eZINI::instance( 'ezfind.ini' );
        $this->had = $ini->hasVariable( 'SearchSettings', 'SortBy' );
        $this->saved = $this->had ? $ini->variable( 'SearchSettings', 'SortBy' ) : null;
    }

    protected function tearDown(): void
    {
        $ini = eZINI::instance( 'ezfind.ini' );
        if ( $this->had )
            $ini->setVariable( 'SearchSettings', 'SortBy', $this->saved );
        else if ( $ini->hasGroup( 'SearchSettings' ) )
            $ini->removeSetting( 'SearchSettings', 'SortBy' );
    }

    private function sortBy()
    {
        $method = new ReflectionMethod( 'ezjscServerFunctionsJs', 'searchSortBy' );
        return $method->invoke( null );
    }

    public function testTheShippedSettingKeepsNewestFirst()
    {
        $ini = new eZINI( 'ezfind.ini.append.php', 'extension/ezjscore/settings', null, false, null, true );
        $this->assertSame( array( 'published' => 'desc' ), $ini->variable( 'SearchSettings', 'SortBy' ) );
    }

    public function testTheSettingGivesTheOrder()
    {
        eZINI::instance( 'ezfind.ini' )->setVariable( 'SearchSettings', 'SortBy', array( 'score' => 'desc', 'name' => 'ASC' ) );
        $this->assertSame( array( 'score' => 'desc', 'name' => 'asc' ), $this->sortBy() );
    }

    public function testWithoutTheSettingTheOrderIsNewestFirst()
    {
        $ini = eZINI::instance( 'ezfind.ini' );
        if ( $ini->hasGroup( 'SearchSettings' ) )
            $ini->removeSetting( 'SearchSettings', 'SortBy' );
        $this->assertSame( array( 'published' => 'desc' ), $this->sortBy() );
    }

    public function testAnEmptyListGivesNewestFirst()
    {
        eZINI::instance( 'ezfind.ini' )->setVariable( 'SearchSettings', 'SortBy', array() );
        $this->assertSame( array( 'published' => 'desc' ), $this->sortBy() );
    }

    public function testInvalidEntriesAreLeftOut()
    {
        eZINI::instance( 'ezfind.ini' )->setVariable( 'SearchSettings', 'SortBy', array(
            'article/title' => 'asc', 'bad field' => 'desc', 'modified' => 'sideways', 'score' => 'desc' ) );
        $this->assertSame( array( 'article/title' => 'asc', 'score' => 'desc' ), @$this->sortBy() );
    }
}

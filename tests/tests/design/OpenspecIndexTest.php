<?php
/**
 * The OpenSpec index: the table "Planned and finished changes" of doc/eng-us/standards/planned-changes-with-openspec.md
 * (and of its German page) names every directory of openspec/changes/ and openspec/changes/archive/, and nothing
 * else. Files only; no database.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/OpenspecIndexTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class OpenspecIndexTest extends PHPUnit\Framework\TestCase
{
    const PAGES = array(
        'doc/eng-us/standards/planned-changes-with-openspec.md' => '## Planned and finished changes',
        'doc/ger-de/standards/geplante-aenderungen-mit-openspec.md' => '## Geplante und fertige Änderungen',
    );

    /**
     * Returns the change names of the directories: "<name>" for a change, "archive/<name>" for a finished one
     */
    public static function changeDirectories( $root )
    {
        $names = array();
        foreach ( (array)glob( $root . '/openspec/changes/*', GLOB_ONLYDIR ) as $dir )
        {
            if ( basename( $dir ) !== 'archive' )
            {
                $names[] = basename( $dir );
            }
        }
        foreach ( (array)glob( $root . '/openspec/changes/archive/*', GLOB_ONLYDIR ) as $dir )
        {
            $names[] = 'archive/' . basename( $dir );
        }
        sort( $names );
        return $names;
    }

    /**
     * Returns the change names in the first column of the index table that follows $heading
     */
    public static function indexedChanges( $markdown, $heading )
    {
        $start = strpos( $markdown, $heading );
        if ( $start === false )
        {
            return null;
        }
        $section = substr( $markdown, $start + strlen( $heading ) );
        $end = strpos( $section, "\n## " );
        $section = $end === false ? $section : substr( $section, 0, $end );
        $names = array();
        foreach ( explode( "\n", $section ) as $line )
        {
            if ( !preg_match( '/^\|([^|]*)\|/', $line, $cell ) || preg_match( '/^\|\s*-/', $line ) )
            {
                continue;
            }
            preg_match_all( '/`([a-z0-9][a-z0-9\/\-]*)`/', $cell[1], $m );
            foreach ( $m[1] as $name )
            {
                $names[] = $name;
            }
        }
        sort( $names );
        return $names;
    }

    public function testReadsTheTable()
    {
        $md = "## Planned and finished changes\n\n| Change | State |\n|---|---|\n| `a-b`, `c` | x `not-me` |\n| `archive/2026-01-01-d` | y |\n\n## Related pages\n| `e` | z |\n";
        $this->assertSame( array( 'a-b', 'archive/2026-01-01-d', 'c' ), self::indexedChanges( $md, '## Planned and finished changes' ) );
    }

    public function testEveryChangeHasItsRowAndEveryRowItsChange()
    {
        $root = dirname( __DIR__, 3 );
        $dirs = self::changeDirectories( $root );
        $this->assertNotEmpty( $dirs, 'openspec/changes/ has no change' );
        foreach ( self::PAGES as $page => $heading )
        {
            $indexed = self::indexedChanges( (string)file_get_contents( "$root/$page" ), $heading );
            $this->assertNotNull( $indexed, "$page has no section \"$heading\"" );
            $this->assertSame( array(), array_values( array_diff( $dirs, $indexed ) ),
                "$page: these change directories have no row in the index (add one in the pull request that adds the change)" );
            $this->assertSame( array(), array_values( array_diff( $indexed, $dirs ) ),
                "$page: these rows name no directory of openspec/changes/ (moved to archive/? update the row)" );
        }
    }
}

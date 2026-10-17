<?php
/**
 * eZContentObjectTreeNode::pathWithNames() falls back to content/view/full/<id> when the URL path cannot be built in
 * the siteaccess's languages. That fallback is logged:
 *   - as debug output when every node of the path has a URL alias, in other languages only (a German-only site asked
 *     for from an English siteaccess, as the sitemap cronjobs do: 554 errors per run on alpha before)
 *   - as an error naming the nodes without any alias when aliases are really missing (updateniceurls.php)
 * No database: hasURLAlias() is replaced, and the node is not stored.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class eZContentObjectTreeNodeMissingPathLogTestNode extends eZContentObjectTreeNode
{
    public $withAlias = array();

    protected function hasURLAlias( $nodeID )
    {
        return in_array( (int)$nodeID, $this->withAlias, true );
    }

    public function report( array $path )
    {
        $this->reportMissingPath( $path );
    }
}

class eZContentObjectTreeNodeMissingPathLogTest extends PHPUnit\Framework\TestCase
{
    const GLOBAL_KEYS = array( 'eZDebugGlobalInstance', 'eZDebugEnabled', 'eZDebugLogOnly', 'eZDebugLogFileEnabled' );
    private $saved = array();

    protected function setUp(): void
    {
        foreach ( self::GLOBAL_KEYS as $key )
            $this->saved[$key] = array_key_exists( $key, $GLOBALS ) ? array( $GLOBALS[$key] ) : null;
        $debug = new eZDebug();
        $debug->ShowTypes = eZDebug::SHOW_ALL;
        $GLOBALS['eZDebugGlobalInstance'] = $debug;
        $GLOBALS['eZDebugEnabled'] = true;
        $GLOBALS['eZDebugLogOnly'] = false;
        eZDebug::setLogFileEnabled( false );
    }

    protected function tearDown(): void
    {
        foreach ( $this->saved as $key => $saved )
        {
            if ( $saved === null )
                unset( $GLOBALS[$key] );
            else
                $GLOBALS[$key] = $saved[0];
        }
    }

    private function messages( $level )
    {
        $out = array();
        foreach ( eZDebug::instance()->DebugStrings as $entry )
            if ( $entry['Level'] === $level )
                $out[] = $entry['String'];
        return $out;
    }

    private function node()
    {
        $node = new eZContentObjectTreeNodeMissingPathLogTestNode( array( 'node_id' => 32284 ) );
        return $node;
    }

    public function testAPathWithAliasesInOtherLanguagesIsDebugOutput()
    {
        $node = $this->node();
        $node->withAlias = array( 2, 31927, 32284 );
        $node->report( array( 2, 31927, 32284 ) );
        $this->assertSame( array(), $this->messages( eZDebug::LEVEL_ERROR ) );
        $this->assertStringContainsString( 'Node 32284 has no URL alias path in the languages of this siteaccess', implode( "\n", $this->messages( eZDebug::LEVEL_DEBUG ) ) );
    }

    public function testMissingAliasesAreAnErrorNamingTheNodes()
    {
        $node = $this->node();
        $node->withAlias = array( 2 );
        $node->report( array( 2, 31927, 32284 ) );
        $errors = $this->messages( eZDebug::LEVEL_ERROR );
        $this->assertCount( 1, $errors );
        $this->assertStringContainsString( 'no URL alias for node(s) 31927, 32284', $errors[0] );
        $this->assertStringContainsString( 'updateniceurls.php', $errors[0] );
    }
}

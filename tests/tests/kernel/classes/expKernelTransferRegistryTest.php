<?php
/**
 * The transfer registry (expKernelTransferRegistry, expKernelTransferTool, expKernelTransferOperator;
 * doc/eng-us/features/6.0/transfer-registry.md). No database: tools come from settings blocks given here or from the
 * shipped settings/kernelservices.ini; modules and policies are callables.
 *
 *  TR-01 - The shipped kernel tools: each block validates as a service, carries the tag (declared in [Tags]), has a
 *          name, a description, a direction, scopes, an entry URL, a permission, and a help page that exists
 *  TR-02 - A block becomes a tool: direction defaults to both, unknown scopes are dropped, Permission alternatives,
 *          the entry module joins Modules[], actions are parsed and wrong ones dropped
 *  TR-03 - available(): a tool whose module does not exist, or whose policy the user lacks, is never listed; an
 *          Enabled=false block is not read
 *  TR-04 - select(): direction (both matches import and export), scope, format without case, module, exclude, query
 *  TR-05 - actions(): placeholders replaced; an action whose placeholder is not given is left out; a placeholder given
 *          as itself stays for the menu's script; an action's own permission
 *  TR-06 - related(): the round-trip partners first, both ways; itself and tools of the same page left out; by module
 *  TR-07 - search() and words(): ranking by name, keyword or format, description; stop words; German words
 *  TR-08 - The Help menu of a tool's module lists "Which import or export tool do I need?" and the tool's help
 *  TR-09 - The help page (English and German) names every tool the kernel ships, and every round-trip partner exists
 *  TR-10 - The operators: tool hashes, translated actions, related with partner, transfer_tool with active
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expKernelTransferRegistryTest extends PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        expKernelTransferOperator::$registry = null;
        expKernelTransferOperator::$access = null;
    }

    private static function root()
    {
        return dirname( __DIR__, 4 );
    }

    private function ini( array $blocks )
    {
        $ini = new eZINI( 'kernelservices.ini', 'settings', null, false, null, false, false, false );
        $ini->BlockValues = $blocks;
        return $ini;
    }

    private static function block( array $values )
    {
        return $values + array( 'Class' => 'expKernelTransferTool', 'Factory' => 'expKernelTransferTool::fromSettings',
                                'Scope' => 'worker', 'Tags' => array( 'transfer.tool' ) );
    }

    /**
     * Three tools: an exporter (exp), an importer (imp) that reads exp's files, a site copier (copy) without access.
     */
    private function registry( array $modules = array( 'exp', 'imp', 'copy' ) )
    {
        $ini = $this->ini( array(
            'Service_transfer.exp' => self::block( array( 'Name' => 'Exporter', 'Description' => 'Download items as a spreadsheet.',
                'Direction' => 'export', 'Scopes' => array( 'subtree', 'class' ), 'Formats' => array( 'CSV', 'XML' ),
                'EntryURL' => 'exp/list', 'Permission' => 'exp/read', 'HelpPage' => 'help/exp.md',
                'Keywords' => array( 'spreadsheet', 'Tabelle' ), 'Weight' => '20',
                'Actions' => array( 'node|export|Export this subtree…|exp/list/(node)/%nodeID%',
                                    'class|export|Export this class…|exp/list/(class)/%classID%',
                                    'container|import|Admin only…|exp/admin/%nodeID%|exp/admin' ) ) ),
            'Service_transfer.imp' => self::block( array( 'Name' => 'Importer', 'Description' => 'Bring rows in as content.',
                'Direction' => 'import', 'Scopes' => array( 'subtree' ), 'Formats' => array( 'CSV', 'XLSX' ),
                'EntryURL' => 'imp/list', 'Permission' => 'imp/manage|imp/run', 'RoundTrip' => array( 'transfer.exp' ),
                'Keywords' => array( 'import csv' ), 'Weight' => '10',
                'Actions' => array( 'container|import|Import into this folder…|imp/edit/new/(parent)/%nodeID%' ) ) ),
            'Service_transfer.copy' => self::block( array( 'Name' => 'Site copier', 'Direction' => 'both',
                'Scopes' => array( 'site' ), 'EntryURL' => 'copy/menu', 'Permission' => 'copy/menu' ) ),
            'Service_transfer.off' => self::block( array( 'Name' => 'Off', 'EntryURL' => 'exp/list', 'Enabled' => 'false' ) ),
            'Service_other' => array( 'Class' => 'stdClass' ),
        ) );
        return expKernelTransferRegistry::fromSettings( $ini, function ( $module ) use ( $modules ) {
            return in_array( $module, $modules, true );
        } );
    }

    private static function access( array $granted = array( 'exp/read', 'exp/admin', 'imp/run' ) )
    {
        return function ( $module, $function ) use ( $granted ) {
            return in_array( $module . '/' . ( $function === false ? '*' : $function ), $granted, true );
        };
    }

    /** TR-01 */
    public function testShippedKernelToolsAreComplete()
    {
        $ini = new eZINI( 'kernelservices.ini', 'settings', null, false, null, true );
        $read = expKernelServiceCompiler::definitionsFromINI( $ini );
        $this->assertContains( 'transfer.tool', $read['declared_tags'] );
        $this->assertArrayHasKey( 'transfer.registry', $read['definitions'] );

        $ids = isset( $read['tags']['transfer.tool'] ) ? $read['tags']['transfer.tool'] : array();
        foreach ( array( 'transfer.package', 'transfer.rss_import', 'transfer.rss_export', 'transfer.infocollector', 'transfer.audit', 'transfer.pdf' ) as $id )
            $this->assertContains( $id, $ids );

        foreach ( expKernelServiceCompiler::validate( $read ) as $problem )
        {
            if ( strncmp( $problem[1], 'transfer.', 9 ) === 0 )
                $this->fail( "{$problem[1]}: {$problem[2]}" );
        }

        $registry = expKernelTransferRegistry::fromSettings( $ini );
        foreach ( $ids as $id )
        {
            $tool = $registry->tool( $id );
            $this->assertNotNull( $tool, $id );
            $this->assertNotSame( $id, $tool->get( 'name' ), "$id: Name" );
            $this->assertNotSame( '', $tool->get( 'description' ), "$id: Description" );
            $this->assertNotEmpty( $tool->get( 'scopes' ), "$id: Scopes[]" );
            $this->assertNotSame( '', $tool->get( 'entry_url' ), "$id: EntryURL" );
            $this->assertNotEmpty( $tool->get( 'permissions' ), "$id: Permission" );
            $page = strtok( $tool->get( 'help_page' ), '#' );
            $this->assertFileExists( self::root() . '/doc/eng-us/' . $page, "$id: HelpPage" );
            $this->assertSame( $id, expKernelTransferTool::fromSettings( $id, $ini )->id() );
        }
        $this->assertFileExists( self::root() . '/doc/eng-us/' . expKernelTransferRegistry::HELP_PAGE );

        // "…", "|" and "/" survive the settings parser
        $actions = $registry->tool( 'transfer.package' )->get( 'actions' );
        $this->assertSame( array( 'Export classes as a package…', 'Upload and install a package…' ), array_column( $actions, 'label' ) );
        $this->assertSame( array( array( array( 'package', 'list' ), array( 'package', 'read' ) ) ), array( $registry->tool( 'package' )->get( 'permissions' ) ) );
    }

    /** TR-02 */
    public function testBlockParsing()
    {
        $tool = expKernelTransferTool::fromBlock( 'transfer.x', array( 'Direction' => 'sideways', 'Scopes' => array( 'Subtree', 'galaxy' ),
            'EntryURL' => '/x/list/', 'Permission' => array( 'x/read|x/write', 'y/*' ), 'Modules' => array( 'xadmin' ),
            'Actions' => array( 'node|export|Go…|x/go/%nodeID%|x/go', 'nowhere|export|Bad|x', 'node|sideways|Bad|x', 'node|import||x' ) ) );
        $this->assertSame( 'both', $tool->get( 'direction' ) );
        $this->assertSame( array( 'subtree' ), $tool->get( 'scopes' ) );
        $this->assertSame( 'x/list', $tool->get( 'entry_url' ) );
        $this->assertSame( 'x', $tool->get( 'module' ) );
        $this->assertSame( array( 'x', 'xadmin' ), $tool->get( 'modules' ) );
        $this->assertSame( array( array( 'x', 'read' ), array( 'x', 'write' ), array( 'y', false ) ), $tool->get( 'permissions' ) );
        $this->assertSame( 'transfer.x', $tool->get( 'name' ), 'the id when there is no name' );
        $actions = $tool->get( 'actions' );
        $this->assertCount( 1, $actions );
        $this->assertSame( array( array( 'x', 'go' ) ), $actions[0]['permissions'] );
        $this->assertTrue( $tool->imports() && $tool->exports() );
    }

    /** TR-03 */
    public function testAvailableLeavesOutInactiveAndRefusedTools()
    {
        $registry = $this->registry();
        $this->assertSame( array( 'transfer.imp', 'transfer.exp', 'transfer.copy' ), array_keys( $registry->tools() ), 'weight, then name; Enabled=false and untagged blocks not read' );
        $this->assertSame( array( 'transfer.imp', 'transfer.exp' ), array_keys( $registry->available( self::access() ) ) );
        $this->assertSame( array(), array_keys( $registry->available( self::access( array() ) ) ) );

        $withoutImp = $this->registry( array( 'exp', 'copy' ) );
        $this->assertSame( array( 'transfer.exp' ), array_keys( $withoutImp->available( self::access() ) ) );
    }

    /** TR-04 */
    public function testSelect()
    {
        $registry = $this->registry();
        $all = self::access( array( 'exp/read', 'imp/run', 'copy/menu' ) );
        $this->assertSame( array( 'transfer.imp', 'transfer.copy' ), array_keys( $registry->select( array( 'direction' => 'import' ), $all ) ) );
        $this->assertSame( array( 'transfer.exp', 'transfer.copy' ), array_keys( $registry->select( array( 'direction' => 'export' ), $all ) ) );
        $this->assertSame( array( 'transfer.exp' ), array_keys( $registry->select( array( 'scope' => 'class' ), $all ) ) );
        $this->assertSame( array( 'transfer.imp' ), array_keys( $registry->select( array( 'format' => 'xlsx' ), $all ) ) );
        $this->assertSame( array( 'transfer.imp' ), array_keys( $registry->select( array( 'module' => 'imp' ), $all ) ) );
        $this->assertSame( array( 'transfer.imp', 'transfer.copy' ), array_keys( $registry->select( array( 'exclude' => 'exp' ), $all ) ) );
        $this->assertSame( array( 'transfer.exp' ), array_keys( $registry->select( array( 'query' => 'Tabelle' ), $all ) ) );
    }

    /** TR-05 */
    public function testActions()
    {
        $registry = $this->registry();
        $access = self::access();
        $node = $registry->actions( 'node', array( '%nodeID%' => 42 ), $access );
        $this->assertSame( array( array( 'tool' => 'transfer.exp', 'context' => 'node', 'direction' => 'export',
                                         'label' => 'Export this subtree…', 'url' => 'exp/list/(node)/42', 'name' => 'Exporter' ) ), $node );

        $container = $registry->actions( 'container', array( '%nodeID%' => 42 ), $access );
        $this->assertSame( array( 'imp/edit/new/(parent)/42', 'exp/admin/42' ), array_column( $container, 'url' ) );
        $this->assertSame( array( 'imp/edit/new/(parent)/42' ), array_column( $registry->actions( 'container', array( '%nodeID%' => 42 ), self::access( array( 'exp/read', 'imp/run' ) ) ), 'url' ), 'the action\'s own permission' );
        $this->assertSame( array( 'exp/list/(node)/%nodeID%' ), array_column( $registry->actions( 'node', array( '%nodeID%' => '%nodeID%' ), $access ), 'url' ), 'left for the menu\'s script' );
        $this->assertSame( array(), $registry->actions( 'class', array( '%nodeID%' => 42 ), $access ), 'no %classID%: left out' );
        $this->assertSame( array( 'imp/edit/new/(parent)/7', 'exp/admin/7' ), array_column( $registry->actions( 'container', array( '%nodeID%' => 7 ), $access, 'import' ), 'url' ) );
        $this->assertCount( 0, $registry->actions( 'container', array( '%nodeID%' => 7 ), $access, 'export' ) );
    }

    /** TR-06 */
    public function testRelatedPutsPartnersFirst()
    {
        $registry = $this->registry();
        $all = self::access( array( 'exp/read', 'imp/run', 'copy/menu' ) );
        $related = $registry->related( 'transfer.copy', $all );
        $this->assertSame( array( 'transfer.imp', 'transfer.exp' ), array_map( function ( $e ) { return $e['tool']->id(); }, $related ) );
        $this->assertFalse( $related[0]['partner'] );

        // exp names no partner, imp names exp: the partnership counts both ways
        $related = $registry->related( 'exp', $all );
        $this->assertSame( 'transfer.imp', $related[0]['tool']->id() );
        $this->assertTrue( $related[0]['partner'] );
        $this->assertSame( 'transfer.copy', $related[1]['tool']->id() );
        $this->assertFalse( $related[1]['partner'] );

        $this->assertSame( 'transfer.imp', $registry->related( 'exp', $all )[0]['tool']->id(), 'by module' );
        $this->assertSame( 'transfer.exp', $registry->toolOfModule( 'exp' )->id() );
        $this->assertNull( $registry->toolOfModule( 'content' ) );
    }

    /** TR-07 */
    public function testSearch()
    {
        $registry = $this->registry();
        $all = self::access( array( 'exp/read', 'imp/run', 'copy/menu' ) );
        $this->assertSame( array( 'import', 'csv' ), expKernelTransferRegistry::words( 'I want to import a CSV' ) );
        $this->assertSame( array( 'transfer.imp', 'transfer.exp' ), array_keys( $registry->search( 'import csv', $all ) ), 'keyword and format beat format alone' );
        $this->assertSame( array( 'transfer.copy' ), array_keys( $registry->search( 'site copier', $all ) ) );
        $this->assertSame( array( 'transfer.exp' ), array_keys( $registry->search( 'eine Tabelle', $all ) ) );
        $this->assertSame( array(), $registry->search( 'the and', $all ), 'stop words only' );
        $this->assertSame( array(), $registry->search( 'banana', $all ) );
    }

    /** TR-08 */
    public function testHelpMenuOfAToolsModule()
    {
        $registry = $this->registry();
        $this->assertSame( array( expKernelTransferRegistry::HELP_PAGE, 'help/exp.md' ), $registry->helpTopicsForModule( 'exp' ) );
        $this->assertSame( array( expKernelTransferRegistry::HELP_PAGE ), $registry->helpTopicsForModule( 'imp' ) );
        $this->assertSame( array(), $registry->helpTopicsForModule( 'content' ) );

        $pages = array( expKernelTransferRegistry::HELP_PAGE => 'Which tool', 'help/exp.md' => 'Exporter', 'guides/exp.md' => 'Guide' );
        $info = function ( $id ) use ( $pages ) {
            return isset( $pages[$id] ) ? array( 'title' => $pages[$id] ) : null;
        };
        $help = new expHelpContextRegistry( array( 'exp' => 'guides/exp.md' ), array(), $info,
            array( 'transfer' => array( $registry, 'helpTopicsForModule' ) ) );
        $menu = $help->menu( 'exp', 'list' );
        $this->assertSame( array( expKernelTransferRegistry::HELP_PAGE, 'help/exp.md' ), array_column( $menu['topics'], 'id' ) );
        $this->assertSame( array(), array_column( $help->menu( 'content', 'view' )['topics'], 'id' ) );

        $off = new expHelpContextRegistry( array( 'exp' => 'guides/exp.md' ), array(), $info, array( 'transfer' => false ) );
        $this->assertSame( array(), array_column( $off->menu( 'exp', 'list' )['topics'], 'id' ) );
    }

    /** TR-09 */
    public function testHelpPageNamesEveryKernelTool()
    {
        $ini = new eZINI( 'kernelservices.ini', 'settings', null, false, null, true );
        $registry = expKernelTransferRegistry::fromSettings( $ini );
        $english = file_get_contents( self::root() . '/doc/eng-us/' . expKernelTransferRegistry::HELP_PAGE );
        $german = file_get_contents( self::root() . '/doc/ger-de/hilfe/import-oder-export-werkzeug.md' );
        $paths = json_decode( file_get_contents( self::root() . '/doc/ger-de/.paths.json' ), true );
        $this->assertSame( expKernelTransferRegistry::HELP_PAGE, $paths['hilfe/import-oder-export-werkzeug.md'] );
        foreach ( $registry->tools() as $id => $tool )
        {
            $this->assertStringContainsString( '**' . $tool->get( 'name' ) . '**', $english, "$id in the English page" );
            $this->assertStringContainsString( '**' . $tool->get( 'name' ) . '**', $german, "$id in the German page" );
            foreach ( $tool->get( 'round_trip' ) as $partner )
            {
                // a partner may be an extension's tool, not declared here; a kernel id must exist
                if ( in_array( $partner, array( 'transfer.package', 'transfer.rss_import', 'transfer.rss_export', 'transfer.infocollector', 'transfer.audit', 'transfer.pdf' ), true ) )
                    $this->assertNotNull( $registry->tool( $partner ), "$id: RoundTrip[]=$partner" );
            }
        }
    }

    /** TR-10 */
    public function testOperators()
    {
        expKernelTransferOperator::$registry = $this->registry();
        expKernelTransferOperator::$access = self::access();

        $tools = expKernelTransferOperator::run( 'transfer_tools', array( 'filter' => array( 'direction' => 'export' ) ) );
        $this->assertSame( array( 'transfer.exp' ), array_column( $tools, 'id' ) );
        $this->assertSame( 'exp/list', $tools[0]['url'] );
        $this->assertSame( 'exphelp/viewer/help/exp.md', $tools[0]['help_url'] );

        $actions = expKernelTransferOperator::run( 'transfer_actions', array( 'context' => 'node', 'values' => array( '%nodeID%' => 'a/b' ), 'direction' => '' ) );
        $this->assertSame( 'exp/list/(node)/a%2Fb', $actions[0]['url'], 'values are encoded' );

        $related = expKernelTransferOperator::run( 'transfer_related', array( 'tool' => 'exp' ) );
        $this->assertSame( array( 'transfer.imp' ), array_column( $related, 'id' ) );
        $this->assertTrue( $related[0]['partner'] );

        $this->assertTrue( expKernelTransferOperator::run( 'transfer_tool', array( 'tool' => 'exp' ) )['active'] );
        $this->assertFalse( expKernelTransferOperator::run( 'transfer_tool', array( 'tool' => 'copy' ) )['active'] );
        $this->assertSame( 'transfer.imp', expKernelTransferOperator::run( 'transfer_tool', array( 'tool' => 'imp' ) )['id'] );
        $this->assertFalse( expKernelTransferOperator::run( 'transfer_tool', array( 'tool' => 'nothing' ) ) );

        $operator = new expKernelTransferOperator();
        $this->assertSame( array( 'transfer_tools', 'transfer_actions', 'transfer_related', 'transfer_tool' ), $operator->operatorList() );
    }
}
?>

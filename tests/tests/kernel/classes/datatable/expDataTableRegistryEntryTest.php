<?php
/**
 * The data tables in the transfer registry (settings/kernelservices.ini [Service_transfer.datatable];
 * doc/eng-us/features/6.0/data-tables.md, "In the registry"), read from this checkout's settings file.
 *
 *  DG-01 - the block is a transfer tool: direction both, scopes data, class and subtree, entry datatable/dashboard,
 *          permission datatable/read, the secrets module belongs to it, round trip with transfer.sqliimport
 *  DG-02 - both actions are parsed (setup context, import with its own permission, export)
 *  DG-03 - the help page exists, and the "which tool" help pages in English and German name the tool
 *  DG-04 - the content view's tab: admininterface.ini has [AdditionalTab_datatable] with existing templates in admin
 *          and admin4, and datatable.ini switches the inferred relations off by default
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expDataTableTestLoader.php';

class expDataTableRegistryEntryTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return expDataTableTestLoader::root();
    }

    /**
     * A settings file of this checkout. eZINI reads a directory relative to its own installation root (the checkout
     * its class came from), so the path is made relative to that root.
     */
    private static function ini( $file )
    {
        $method = new ReflectionMethod( 'eZINI', 'installationRoot' );
        $from = explode( '/', trim( (string)realpath( $method->invoke( null ) ), '/' ) );
        $to = explode( '/', trim( (string)realpath( self::root() . '/settings' ), '/' ) );
        while ( $from && $to && $from[0] === $to[0] )
        {
            array_shift( $from );
            array_shift( $to );
        }
        $dir = implode( '/', array_merge( array_fill( 0, count( $from ), '..' ), $to ) );
        return new eZINI( $file, $dir === '' ? '.' : $dir, null, false, null, true );
    }

    private static function tool()
    {
        $ini = self::ini( 'kernelservices.ini' );
        self::assertTrue( $ini->hasGroup( 'Service_transfer.datatable' ), 'the block is in ' . self::root() . '/settings/kernelservices.ini' );
        $group = $ini->group( 'Service_transfer.datatable' );
        self::assertSame( 'expKernelTransferTool', $group['Class'] );
        self::assertSame( 'worker', $group['Scope'] );
        self::assertContains( 'transfer.tool', (array)$group['Tags'] );
        return expKernelTransferTool::fromBlock( 'transfer.datatable', $group );
    }

    /** DG-01 */
    public function testTheBlockIsATool()
    {
        $tool = self::tool();
        $this->assertSame( 'transfer.datatable', $tool->id() );
        $this->assertSame( 'Data tables', $tool->get( 'name' ) );
        $this->assertNotSame( '', $tool->get( 'description' ) );
        $this->assertSame( 'both', $tool->get( 'direction' ) );
        $this->assertSame( array( 'data', 'class', 'subtree' ), $tool->get( 'scopes' ) );
        $this->assertSame( 'datatable/dashboard', $tool->get( 'entry_url' ) );
        $this->assertSame( array( array( array( 'datatable', 'read' ) ) ), array( $tool->get( 'permissions' ) ) );
        $this->assertContains( 'datatable', $tool->get( 'modules' ) );
        $this->assertContains( 'secrets', $tool->get( 'modules' ) );
        $this->assertSame( array( 'transfer.sqliimport' ), $tool->get( 'round_trip' ) );
        $this->assertNotEmpty( $tool->get( 'keywords' ) );
    }

    /** DG-02 */
    public function testBothActionsAreParsed()
    {
        $actions = self::tool()->get( 'actions' );
        $this->assertCount( 2, $actions );
        $this->assertSame( array( 'setup', 'setup' ), array_column( $actions, 'context' ) );
        $this->assertSame( array( 'import', 'export' ), array_column( $actions, 'direction' ) );
        $this->assertSame( array( 'datatable/schema', 'datatable/dashboard' ), array_column( $actions, 'url' ) );
        $this->assertSame( array( array( 'datatable', 'manage' ) ), $actions[0]['permissions'] );
        $this->assertSame( array(), $actions[1]['permissions'] );
        foreach ( $actions as $action )
            $this->assertStringEndsWith( '…', $action['label'] );
    }

    /** DG-03 */
    public function testHelpPages()
    {
        $tool = self::tool();
        $this->assertFileExists( self::root() . '/doc/eng-us/' . strtok( $tool->get( 'help_page' ), '#' ) );
        $this->assertStringContainsString( '**Data tables**', file_get_contents( self::root() . '/doc/eng-us/' . expKernelTransferRegistry::HELP_PAGE ) );
        $this->assertStringContainsString( '**Data tables**', file_get_contents( self::root() . '/doc/ger-de/hilfe/import-oder-export-werkzeug.md' ) );
    }

    /** DG-04 */
    public function testTheContentViewTab()
    {
        $admin = self::ini( 'admininterface.ini' );
        $this->assertContains( 'datatable', (array)$admin->variable( 'WindowControlsSettings', 'AdditionalTabs' ) );
        $this->assertSame( 'datatable/node_header.tpl', $admin->variable( 'AdditionalTab_datatable', 'HeaderTemplate' ) );
        $this->assertSame( 'datatable/node.tpl', $admin->variable( 'AdditionalTab_datatable', 'Template' ) );
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            foreach ( array( 'node_header.tpl', 'node.tpl' ) as $file )
                $this->assertFileExists( self::root() . "/design/$design/templates/tabs/datatable/$file" );
        }
        $this->assertFileEquals( self::root() . '/design/admin4/templates/tabs/datatable/node.tpl', self::root() . '/design/admin/templates/tabs/datatable/node.tpl' );
        $this->assertSame( 'disabled', self::ini( 'datatable.ini' )->variable( 'DataTableSettings', 'ContentTabInferred' ) );
    }
}

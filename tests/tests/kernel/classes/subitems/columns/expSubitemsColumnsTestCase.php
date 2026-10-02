<?php
/**
 * The common base of the subitems column catalogue tests.
 *
 * These tests run the shipped columns against the installation's own database: the kernel is
 * started once (eZScript, the admin siteaccess, the configured database), the admin user is
 * logged in, and every column is read through the registry from settings/subitemscolumns.ini,
 * exactly as the list does. The nodes used are the stable ones every installation has: the
 * content root, the media root and the users root ([NodeSettings] in content.ini), and the
 * admin user's node. Nodes particular to this database are looked up by what they contain
 * (a class, a datatype), and a test is skipped when the database has none.
 *
 * Run: php vendor/bin/phpunit tests/tests/kernel/classes/subitems/columns/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

abstract class expSubitemsColumnsTestCase extends PHPUnit\Framework\TestCase
{
    /** @var eZScript|null */
    protected static $script = null;

    /** @var string|null why the kernel could not be started */
    protected static $bootError = null;

    /** @var expSubitemsColumnRegistry */
    protected static $registry;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::boot();
    }

    public function setUp(): void
    {
        parent::setUp();
        if ( self::$bootError !== null )
            $this->markTestSkipped( 'Kernel not available: ' . self::$bootError );
        expSubitemsFieldColumn::resetMemo();
        expSubitemsURLColumn::resetMemo();
        self::loginAdmin();
    }

    /** Starts the kernel on the admin siteaccess once for the whole run. */
    protected static function boot()
    {
        if ( self::$script !== null || self::$bootError !== null )
            return;
        try
        {
            $root = dirname( __DIR__, 6 );
            chdir( $root );
            if ( !class_exists( 'eZScript' ) )
                require_once $root . '/autoload.php';

            $script = eZScript::instance( array( 'use-session' => false, 'use-modules' => true, 'use-extensions' => true ) );
            $script->startup();
            $script->setUseSiteAccess( self::adminSiteAccess() );
            $script->initialize();
            eZExecution::setCleanExit();

            if ( !eZDB::instance()->isConnected() )
                throw new RuntimeException( 'no database connection' );
            self::$script = $script;
            self::$registry = new expSubitemsColumnRegistry();
        }
        catch ( Throwable $e )
        {
            self::$bootError = $e->getMessage();
        }
    }

    /** The admin siteaccess: the first of AvailableSiteAccessList[] named "admin", else "admin". */
    protected static function adminSiteAccess()
    {
        return 'admin';
    }

    protected static function loginAdmin()
    {
        $admin = eZUser::fetchByName( 'admin' );
        if ( $admin instanceof eZUser )
            eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ) );
    }

    /** A node by id; the test fails when it does not exist. */
    protected function node( $nodeID )
    {
        $node = eZContentObjectTreeNode::fetch( (int)$nodeID );
        $this->assertInstanceOf( 'eZContentObjectTreeNode', $node, "node $nodeID exists" );
        return $node;
    }

    protected function contentRoot()
    {
        return $this->node( eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) );
    }

    protected function mediaRoot()
    {
        return $this->node( eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'MediaRootNode' ) );
    }

    protected function usersRoot()
    {
        return $this->node( eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'UserRootNode' ) );
    }

    /** The node of the user "admin". */
    protected function adminUserNode()
    {
        $admin = eZUser::fetchByName( 'admin' );
        $this->assertInstanceOf( 'eZUser', $admin, 'the admin user exists' );
        $object = eZContentObject::fetch( $admin->attribute( 'contentobject_id' ) );
        return $object->attribute( 'main_node' );
    }

    /**
     * The main node of the first published object with an attribute of $dataType that has content,
     * or a skipped test.
     */
    protected function nodeWithDataType( $dataType )
    {
        $db = eZDB::instance();
        $type = $db->escapeString( $dataType );
        $rows = $db->arrayQuery(
            "SELECT ezcontentobject.id AS id FROM ezcontentobject, ezcontentobject_attribute
              WHERE ezcontentobject_attribute.contentobject_id = ezcontentobject.id
                AND ezcontentobject_attribute.version = ezcontentobject.current_version
                AND ezcontentobject_attribute.data_type_string = '$type'
                AND ezcontentobject.status = 1
              ORDER BY ezcontentobject.id", array( 'limit' => 200 ) );
        foreach ( $rows as $row )
        {
            $object = eZContentObject::fetch( (int)$row['id'] );
            $node = $object ? $object->attribute( 'main_node' ) : null;
            if ( !$node instanceof eZContentObjectTreeNode )
                continue;
            foreach ( $node->attribute( 'data_map' ) as $attribute )
            {
                if ( $attribute->attribute( 'data_type_string' ) === $dataType && $attribute->hasContent() )
                    return $node;
            }
        }
        $this->markTestSkipped( "no object with a filled $dataType attribute in this database" );
    }

    /** The column for $key from the shipped INI, through the registry (no policy check). */
    protected function column( $key )
    {
        $column = self::$registry->definedColumn( $key );
        $this->assertInstanceOf( 'expSubitemsColumn', $column, "column $key is defined and builds" );
        return $column;
    }

    /** The value of column $key for $node. */
    protected function value( $key, eZContentObjectTreeNode $node )
    {
        return $this->column( $key )->value( $node );
    }
}

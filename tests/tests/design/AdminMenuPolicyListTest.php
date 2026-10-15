<?php
/**
 * The policies of the admin menus (settings/menu.ini PolicyList[] and PolicyList_<link>[]) name something that
 * exists: a node id, or <module>/<function> with a function of the module's FunctionList, or <module>/* for a
 * module. A view's name in place of a function ("visual/toolbarlist") is checked by eZUser::hasAccessTo() as a
 * function nobody can be given, so the entry passes only for a user with the whole module and hides the link from
 * everyone with a limited policy. The design tab points at the design top node by its id, not at a URL alias.
 *
 * Run: php vendor/bin/phpunit tests/tests/design/AdminMenuPolicyListTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class AdminMenuPolicyListTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return dirname( __DIR__, 3 );
    }

    /**
     * Returns the policy functions of a kernel module (the keys of its FunctionList), or null when there is no such
     * module
     */
    public static function moduleFunctions( $module )
    {
        $file = self::root() . "/kernel/$module/module.php";
        if ( !preg_match( '/^\w+$/', $module ) || !is_file( $file ) )
            return null;
        // read, not included: some modules build their limitation values from the database
        preg_match_all( '/^\$FunctionList\[\s*[\'"](\w+)[\'"]\s*\]\s*=/m', (string)file_get_contents( $file ), $m );
        return array_values( array_unique( $m[1] ) );
    }

    /**
     * Returns the problems of one PolicyList value, an empty array when it is fine
     */
    public static function problems( $value )
    {
        if ( ctype_digit( $value ) )
            return array();
        $parts = explode( '/', $value );
        if ( count( $parts ) !== 2 )
            return array( "$value: neither a node id nor <module>/<function>" );
        list( $module, $function ) = $parts;
        $functions = self::moduleFunctions( $module );
        if ( $functions === null )
            return array( "$value: there is no module $module" );
        if ( $function !== '*' && !in_array( $function, $functions, true ) )
            return array( "$value: $module has no policy function $function (" . ( $functions ? implode( ', ', $functions ) : "use $module/*" ) . ')' );
        return array();
    }

    public function testTellsAFunctionFromAView()
    {
        $this->assertSame( array(), self::problems( '58' ) );
        $this->assertSame( array(), self::problems( 'setup/managecache' ) );
        $this->assertSame( array(), self::problems( 'visual/*' ) );
        $this->assertCount( 1, self::problems( 'visual/toolbarlist' ) );
        $this->assertCount( 1, self::problems( 'setup/cache' ) );
        $this->assertCount( 1, self::problems( 'nosuchmodule/read' ) );
    }

    public function testEveryPolicyOfTheMenusExists()
    {
        $ini = (string)file_get_contents( self::root() . '/settings/menu.ini' );
        // the commented examples too: they are what an administrator uncomments
        preg_match_all( '/^#?\s*PolicyList(?:_\w+)?\[\]=(\S+)\s*$/m', $ini, $m );
        $values = array_values( array_diff( array_unique( $m[1] ), array( '<value>' ) ) );
        $this->assertGreaterThan( 40, count( $values ) );
        $problems = array();
        foreach ( $values as $value )
            $problems = array_merge( $problems, self::problems( $value ) );
        $this->assertSame( array(), $problems, 'menu.ini names policies that do not exist' );
    }

    public function testTheDesignTabOpensTheDesignTopNodeById()
    {
        $menu = (string)file_get_contents( self::root() . '/settings/menu.ini' );
        $content = (string)file_get_contents( self::root() . '/settings/content.ini' );
        $this->assertMatchesRegularExpression( '/^DesignRootNode=58$/m', $content );
        $this->assertSame( 1, preg_match( '/^\[Topmenu_design\]$(.*?)^\[/ms', $menu, $block ) );
        $this->assertMatchesRegularExpression( '/^URL\[default\]=content\/view\/full\/58$/m', $block[1] );
        $this->assertMatchesRegularExpression( '/^PolicyList\[\]=58$/m', $block[1] );
    }
}

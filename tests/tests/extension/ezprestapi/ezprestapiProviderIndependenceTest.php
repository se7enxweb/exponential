<?php
/**
 * The ezprestapi extension serves REST API v1 and v2 on its own: it must not reach for a class that only the
 * deprecated ezprestapiprovider extension defines. A new installation activates ezprestapi alone, and the
 * children list answered 500 there (Class "ezpRestContentController" not found) because the model looked up
 * the Fields response group on the provider's controller.
 *
 *  RA-01 - no PHP code of ezprestapi names a class of the deprecated provider
 *  RA-02 - every constant read from ezp7xRestContentController is declared there
 *  RA-03 - the provider's routes name controllers the extension declares, and every action has its do-method
 *  RA-04 - rest.ini maps the prefixes ezp and ezpl to a provider class the extension declares
 *  RA-05 - the deprecated provider, where it is still present, only extends the classes of ezprestapi
 *
 * Reads the extension's files only. No kernel, database or siteaccess.
 *
 * Run: php vendor/bin/phpunit --testsuite ezprestapi
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class ezprestapiProviderIndependenceTest extends PHPUnit\Framework\TestCase
{
    /** the classes the deprecated ezprestapiprovider defines */
    private const PROVIDER_CLASSES = array( 'ezpRestApiProvider', 'ezpRestContentController' );

    private static function root(): string
    {
        return dirname( __DIR__, 4 ) . '/extension/ezprestapi';
    }

    /** @return string[] the PHP files under classes/ */
    private static function classFiles(): array
    {
        $files = array();
        $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( self::root() . '/classes', FilesystemIterator::SKIP_DOTS ) );
        foreach ( $iterator as $file )
        {
            if ( $file->getExtension() === 'php' )
                $files[] = $file->getPathname();
        }
        sort( $files );
        self::assertNotEmpty( $files, 'extension/ezprestapi/classes holds no PHP file' );
        return $files;
    }

    /**
     * the PHP code of a file without comments, so a docblock naming a class is no reference; strings stay
     * unless $withoutStrings, because a class named as a string ('Class', class_exists) is a reference too
     */
    private static function code( string $file, bool $withoutStrings = false ): string
    {
        $source = file_get_contents( $file );
        self::assertNotFalse( $source, "$file is not readable" );
        $code = '';
        foreach ( token_get_all( $source ) as $token )
        {
            if ( is_array( $token ) )
            {
                $skip = array( T_COMMENT, T_DOC_COMMENT );
                if ( $withoutStrings )
                    $skip[] = T_CONSTANT_ENCAPSED_STRING;
                if ( in_array( $token[0], $skip, true ) )
                    continue;
                $code .= $token[1];
            }
            else
                $code .= $token;
        }
        return $code;
    }

    /** @return string[] the classes declared in the extension's classes/, name => file */
    private static function declaredClasses(): array
    {
        $classes = array();
        foreach ( self::classFiles() as $file )
        {
            if ( preg_match_all( '/^\s*(?:(?:abstract|final|readonly)\s+)*class\s+(\w+)/m', self::code( $file ), $m ) )
            {
                foreach ( $m[1] as $name )
                    $classes[$name] = $file;
            }
        }
        return $classes;
    }

    public function testNoCodeNamesAClassOfTheDeprecatedProvider()
    {
        foreach ( self::classFiles() as $file )
        {
            $code = self::code( $file );
            foreach ( self::PROVIDER_CLASSES as $class )
            {
                self::assertSame(
                    0,
                    preg_match( '/\b' . $class . '\b/', $code ),
                    basename( $file ) . " names $class, which only the deprecated ezprestapiprovider defines"
                );
            }
        }
    }

    public function testEveryConstantReadFromTheContentControllerIsDeclared()
    {
        $controller = self::code( self::root() . '/classes/controllers/content.php', true );
        preg_match( '/class\s+ezp7xRestContentController\b.*/s', $controller, $body );
        self::assertNotEmpty( $body, 'ezp7xRestContentController is not declared in controllers/content.php' );

        $declared = array();
        // const A = 'x', B = 'y';  — the names before each "=" of a const statement
        if ( preg_match_all( '/\bconst\b([^;]*);/s', $body[0], $statements ) )
        {
            foreach ( $statements[1] as $statement )
            {
                if ( preg_match_all( '/(\w+)\s*=/', $statement, $names ) )
                    $declared = array_merge( $declared, $names[1] );
            }
        }
        self::assertNotEmpty( $declared, 'ezp7xRestContentController declares no constant' );

        $read = array();
        foreach ( self::classFiles() as $file )
        {
            if ( preg_match_all( '/\bezp7xRestContentController::([A-Z_0-9]+)/', file_get_contents( $file ), $m ) )
            {
                foreach ( $m[1] as $name )
                    $read[$name] = basename( $file );
            }
        }
        self::assertArrayHasKey( 'VIEWLIST_RESPONSEGROUP_FIELDS', $read, 'the model no longer reads the Fields response group from the extension\'s own controller' );
        foreach ( $read as $name => $file )
            self::assertContains( $name, $declared, "$file reads ezp7xRestContentController::$name, which the controller does not declare" );
    }

    public function testRoutesNameDeclaredControllersAndActions()
    {
        $declared = self::declaredClasses();
        $source = self::code( self::root() . '/classes/rest_provider.php' );

        // 'ezp7xRestContentController', array( 'http-get' => 'list' )   /   new ezpMvcRailsRoute( '/path', 'Class', array( ... ) )
        preg_match_all( "/'(ezp\w*Controller)'\s*,\s*array\(([^)]*)\)/s", $source, $routes, PREG_SET_ORDER );
        self::assertNotEmpty( $routes, 'rest_provider.php defines no route' );
        // a route written another way (short array, ::class) would be skipped without a word: count them
        self::assertSame(
            preg_match_all( '/new\s+ezpMvc(?:Rails|Regexp)Route\s*\(/', $source ),
            count( $routes ),
            'rest_provider.php has a route this test cannot read; extend the pattern'
        );

        foreach ( $routes as $route )
        {
            self::assertArrayHasKey( $route[1], $declared, "a route names {$route[1]}, which ezprestapi does not declare" );
            self::assertNotSame( 0, preg_match_all( "/'http-\w+'\s*=>\s*'(\w+)'/", $route[2], $actions ), "a route of {$route[1]} maps no action" );
            $controller = self::code( $declared[$route[1]], true );
            foreach ( $actions[1] as $action )
            {
                self::assertSame(
                    1,
                    preg_match( '/function\s+do' . preg_quote( $action, '/' ) . '\s*\(/i', $controller ),
                    "{$route[1]} has no do" . ucfirst( $action ) . '() for the route action ' . $action
                );
            }
        }
    }

    public function testRestIniMapsBothPrefixesToADeclaredProvider()
    {
        $ini = file_get_contents( self::root() . '/settings/rest.ini.append.php' );
        foreach ( array( 'ezp', 'ezpl' ) as $prefix )
        {
            self::assertSame( 1, preg_match( '/^ProviderClass\[' . $prefix . '\]=(\w+)\s*$/m', $ini, $m ), "rest.ini does not map the prefix $prefix" );
            self::assertSame( 'ezp7xRestApiProvider', $m[1] );
            self::assertArrayHasKey( $m[1], self::declaredClasses(), "{$m[1]} is not declared in ezprestapi" );
        }
    }

    public function testTheDeprecatedProviderOnlyExtendsTheClassesOfEzprestapi()
    {
        $dir = dirname( __DIR__, 4 ) . '/extension/ezprestapiprovider';
        if ( !is_dir( $dir . '/classes' ) )
            $this->markTestSkipped( 'ezprestapiprovider is not installed' );

        $expected = array(
            'rest_provider.php' => array( 'ezpRestApiProvider', 'ezp7xRestApiProvider' ),
            'controllers/content.php' => array( 'ezpRestContentController', 'ezp7xRestContentController' ),
        );
        foreach ( $expected as $file => $pair )
        {
            self::assertFileExists( "$dir/classes/$file" );
            self::assertSame(
                1,
                preg_match( '/class\s+' . $pair[0] . '\s+extends\s+' . $pair[1] . '\b/', self::code( "$dir/classes/$file" ) ),
                "{$pair[0]} is no shim on {$pair[1]}: the provider is older than 6.0.4, and ezprestapi works without it only from 1.2.7"
            );
        }
    }
}

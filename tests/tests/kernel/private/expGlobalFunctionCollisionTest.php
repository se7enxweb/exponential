<?php
/**
 * No two request-time files of the kernel and the bundled extensions define the same global function.
 *
 * Module views and the files they include define their helper functions as global functions, most of them behind
 * `if ( !function_exists( 'name' ) )`. A persistent-worker server (Velocity with ForkPerRequest=disabled) keeps every
 * function a worker ever defined, so when two files define the same name, the second view silently runs the first
 * view's function: content/edit and user/register both defined checkContentActions, and a Publish after a
 * registration in the same worker ran the registration code. Generic names (error, response, validate, browse ...)
 * collide just as easily with an extension's code, so a module view gives its functions a prefixed name of its own.
 *
 *  GF-01 - the scanner sees global functions and only those (not methods, closures or namespaced functions)
 *  GF-02 - no global function name is defined in two request-time files (any body, guarded or not)
 *  GF-03 - module views define no generic global name and only names with an exp or ez prefix
 *  GF-04 - the renamed view functions are not defined under their old names any more
 *
 * Request-time files: kernel/, lib/, design/ and the bundled extensions, without the command line commands
 * (kernel/private/classes/commands, bin/, update/), which run in a process of their own, and without tests.
 * Cronjob parts share the runcronjobs.php process with the libraries, so they count as request-time too.
 *
 * No database. Run: php vendor/bin/phpunit tests/tests/kernel/private/expGlobalFunctionCollisionTest.php
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group kernel
 */

class expGlobalFunctionCollisionTest extends PHPUnit\Framework\TestCase
{
    /** extensions shipped in the kernel repository */
    const BUNDLED_EXTENSIONS = array( 'expservices', 'ezformtoken', 'ezjscore', 'ezoe' );

    /** names that collide easily with other code */
    const GENERIC_NAMES = array( 'error', 'response', 'validate', 'browse', 'helper', 'help', 'render', 'process', 'run',
                                 'execute', 'redirect', 'output', 'debug', 'log', 'init', 'check', 'update', 'store',
                                 'getHTTPVariable', 'pageLimit', 'applyChanges', 'copyObject', 'checkContentActions',
                                 'exitWithInternalError', 'computeRedirect', 'showError', 'showMessage' );

    /** directories whose PHP files are module views */
    const VIEW_PATTERN = '#^(kernel/private/classes/views/|kernel/private/modules/|extension/[^/]+/modules/|extension/[^/]+/classes/runnable/views/)#';

    /** polyfills of PHP functions: guarded definitions of a built-in name are expected */
    const POLYFILLS = array( 'mb_substr' );

    private static $definitions;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    /**
     * Global functions of one PHP source, by token.
     *
     * @return array of array( 'name' => string, 'line' => int, 'hash' => md5 of the body's tokens )
     */
    public static function globalFunctions( $source )
    {
        $tokens = token_get_all( $source );
        $count = count( $tokens );
        $found = array();
        $depth = 0;
        $classDepths = array();
        $pendingClass = false;
        $namespace = '';
        $namespaceDepth = null;
        $skip = array( T_WHITESPACE, T_COMMENT, T_DOC_COMMENT );
        for ( $i = 0; $i < $count; $i++ )
        {
            $token = $tokens[$i];
            if ( is_string( $token ) )
            {
                if ( $token === '{' )
                {
                    if ( $pendingClass )
                    {
                        $classDepths[] = $depth;
                        $pendingClass = false;
                    }
                    $depth++;
                }
                elseif ( $token === '}' )
                {
                    $depth--;
                    if ( $classDepths && end( $classDepths ) === $depth )
                        array_pop( $classDepths );
                    if ( $namespaceDepth === $depth )
                    {
                        $namespace = '';
                        $namespaceDepth = null;
                    }
                }
                continue;
            }
            $id = $token[0];
            if ( $id === T_CURLY_OPEN || $id === T_DOLLAR_OPEN_CURLY_BRACES )
            {
                $depth++;
            }
            elseif ( $id === T_NAMESPACE )
            {
                $name = '';
                for ( $j = $i + 1; $j < $count && is_array( $tokens[$j] ); $j++ )
                {
                    if ( in_array( $tokens[$j][0], $skip, true ) )
                        continue;
                    if ( $tokens[$j][0] === T_NS_SEPARATOR )
                        break; // namespace\foo(): an expression, not a declaration
                    $name .= $tokens[$j][1];
                }
                if ( $j < $count && ( $tokens[$j] === '{' || $tokens[$j] === ';' ) )
                {
                    $namespace = $name;
                    if ( $tokens[$j] === '{' )
                    {
                        $namespaceDepth = $depth;
                        $depth++;
                    }
                    $i = $j;
                }
            }
            elseif ( in_array( $id, array( T_CLASS, T_INTERFACE, T_TRAIT ), true ) || ( defined( 'T_ENUM' ) && $id === T_ENUM ) )
            {
                for ( $p = $i - 1; $p >= 0 && is_array( $tokens[$p] ) && in_array( $tokens[$p][0], $skip, true ); $p-- );
                if ( !( $p >= 0 && is_array( $tokens[$p] ) && $tokens[$p][0] === T_DOUBLE_COLON ) )
                    $pendingClass = true;
            }
            elseif ( $id === T_FUNCTION && !$classDepths && $namespace === '' )
            {
                for ( $j = $i + 1; $j < $count && ( $tokens[$j] === '&' || ( is_array( $tokens[$j] ) && $tokens[$j][1] === '&' ) || ( is_array( $tokens[$j] ) && in_array( $tokens[$j][0], $skip, true ) ) ); $j++ );
                if ( !( $j < $count && is_array( $tokens[$j] ) && $tokens[$j][0] === T_STRING ) )
                    continue; // a closure
                $body = '';
                $level = 0;
                for ( $k = $j; $k < $count; $k++ )
                {
                    $part = $tokens[$k];
                    if ( is_array( $part ) && in_array( $part[0], $skip, true ) )
                        continue;
                    $text = is_array( $part ) ? $part[1] : $part;
                    $body .= $text . ' ';
                    if ( $text === '{' || ( is_array( $part ) && in_array( $part[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) )
                        $level++;
                    elseif ( $text === '}' && --$level === 0 )
                        break;
                }
                $found[] = array( 'name' => $tokens[$j][1], 'line' => $token[2], 'hash' => md5( $body ) );
            }
        }
        return $found;
    }

    /**
     * @return array lower-case name => list of array( 'file', 'name', 'line', 'hash' )
     */
    private static function definitions()
    {
        if ( self::$definitions !== null )
            return self::$definitions;
        $dirs = array( 'kernel', 'lib', 'design' );
        foreach ( self::BUNDLED_EXTENSIONS as $extension )
            if ( is_dir( 'extension/' . $extension ) )
                $dirs[] = 'extension/' . $extension;
        self::$definitions = array();
        foreach ( $dirs as $dir )
        {
            $files = new RecursiveIteratorIterator( new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ),
                function ( $file )
                {
                    if ( $file->isDir() )
                        return !in_array( $file->getFilename(), array( 'vendor', 'node_modules', 'tests', 'bin', 'update' ), true );
                    return substr( $file->getFilename(), -4 ) === '.php';
                } ) );
            foreach ( $files as $file )
            {
                $path = str_replace( '\\', '/', $file->getPathname() );
                if ( strpos( $path, 'kernel/private/classes/commands/' ) === 0 )
                    continue; // command line commands: a process of their own
                $source = file_get_contents( $path );
                if ( strpos( $source, 'function' ) === false )
                    continue;
                foreach ( self::globalFunctions( $source ) as $definition )
                {
                    $definition['file'] = $path;
                    self::$definitions[strtolower( $definition['name'] )][] = $definition;
                }
            }
        }
        return self::$definitions;
    }

    /** GF-01 */
    public function testScannerSeesGlobalFunctionsOnly()
    {
        $source = <<<'PHP'
<?php
namespace
{
if ( !function_exists( 'one' ) ) { function one( $a ) { return "{$a}"; } }
function &two() { static $x; return $x; }
$f = function ( $b ) { return $b; };
class Foo { function three() {} public static function four() { return Foo::class; } }
interface Bar { function five(); }
trait Baz { function six() { function seven() {} } }
echo Foo::class;
function eight() {}
}
namespace Some\Space
{
function nine() {}
class Qux { function ten() {} }
}
namespace
{
function eleven() {}
}
PHP;
        $names = array_column( self::globalFunctions( $source ), 'name' );
        $this->assertSame( array( 'one', 'two', 'eight', 'eleven' ), $names );
    }

    /** GF-02 */
    public function testNoNameIsDefinedInTwoRequestTimeFiles()
    {
        $collisions = array();
        foreach ( self::definitions() as $list )
        {
            $files = array_unique( array_column( $list, 'file' ) );
            if ( count( $files ) < 2 )
                continue;
            $bodies = count( array_unique( array_column( $list, 'hash' ) ) ) > 1 ? 'different bodies' : 'same body';
            $where = array();
            foreach ( $list as $definition )
                $where[] = $definition['file'] . ':' . $definition['line'];
            $collisions[] = $list[0]['name'] . ' (' . $bodies . '): ' . implode( ', ', $where );
        }
        $this->assertSame( array(), $collisions,
            "A global function defined in two request-time files: under a persistent-worker server the first definition " .
            "a worker loads serves both. Give each file's function a name of its own (expModuleView<Module><View><Name>)." );
    }

    /** GF-03 */
    public function testModuleViewsUsePrefixedNames()
    {
        $generic = array_map( 'strtolower', self::GENERIC_NAMES );
        $found = array();
        foreach ( self::definitions() as $lower => $list )
        {
            foreach ( $list as $definition )
            {
                $isView = preg_match( self::VIEW_PATTERN, $definition['file'] );
                if ( in_array( $lower, self::POLYFILLS, true ) )
                    continue;
                if ( in_array( $lower, $generic, true ) || ( $isView && !preg_match( '/^(exp|ez)/i', $definition['name'] ) ) )
                    $found[] = $definition['name'] . ' in ' . $definition['file'] . ':' . $definition['line'];
            }
        }
        $this->assertSame( array(), $found, 'A generic or unprefixed global function in request-time code' );
    }

    /** GF-04 */
    public function testRenamedViewFunctionsKeepTheirNewNames()
    {
        $expected = array(
            'kernel/private/modules/oauth/authorize.php' => array( 'expModuleViewOauthAuthorizeError', 'expModuleViewOauthAuthorizeResponse', 'expModuleViewOauthAuthorizeGetHTTPVariable' ),
            'kernel/private/classes/views/shop/productcategories.php' => array( 'expModuleViewShopProductCategoriesApplyChanges' ),
            'kernel/private/classes/views/shop/vattype.php' => array( 'expModuleViewShopVatTypeApplyChanges' ),
            'kernel/private/classes/views/content/copy.php' => array( 'expModuleViewContentCopyBrowse' ),
            'kernel/private/classes/views/content/copysubtree.php' => array( 'expModuleViewContentCopySubtreeBrowse' ),
            'kernel/settings/validation.php' => array( 'expSettingsValidate' ),
        );
        foreach ( $expected as $file => $names )
        {
            $defined = array_column( self::globalFunctions( file_get_contents( $file ) ), 'name' );
            foreach ( $names as $name )
            {
                $this->assertContains( $name, $defined, $file );
                // each definition is guarded by its own name, and every use calls the new name
                $this->assertStringContainsString( "function_exists( '" . $name . "' )", file_get_contents( $file ), $file );
            }
        }
    }
}

<?php
/**
 * Boot smoke test: needs no database and no PHPUnit, so it runs on every supported PHP,
 * the oldest (8.0) included. The Quality workflow runs it with PHP 8.0.
 *
 *   php bin/php/ezexec.php tests/bin/bootsmoke.php
 *
 * Checks, with the PHP that runs it:
 *  - the kernel boots (eZScript, INI, the autoload arrays);
 *  - every class of autoload/ezp_kernel.php and of var/autoload/ezp_extension.php loads,
 *    which compiles each class file and checks its parents and interfaces on this PHP;
 *  - the functions of lib/phpcompat.php exist;
 *  - the wash operator escapes single quotes (the htmlspecialchars() default of PHP 8.1,
 *    which PHP 8.0 does not have) and a template renders.
 * Classes that need an optional PHP extension or library that is not installed are listed
 * and skipped, not failed. Prints PASS or FAIL; the script's exit code follows.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 */

$cli = eZCLI::instance();
$failures = array();
$cli->output( 'PHP ' . PHP_VERSION );

// 1. the classes
$maps = array( 'kernel' => 'autoload/ezp_kernel.php', 'extension' => 'var/autoload/ezp_extension.php' );
$skipped = array();
$notes = array();
foreach ( $maps as $label => $file )
{
    if ( !is_file( $file ) )
    {
        $cli->output( "  $label: $file is missing (run bin/php/ezpgenerateautoloads.php)" );
        if ( $label === 'kernel' )
            $failures[] = "$file missing";
        continue;
    }
    $classes = include $file;
    $loaded = 0;
    foreach ( $classes as $class => $path )
    {
        // the code of a CLI script, a module view or a cronjob: it brings the global helper
        // functions of its script along, so two of them cannot share a process (syntax is linted)
        // and the tests of an extension, which need PHPUnit or the test toolkit
        if ( preg_match( '/^Exponential\\\\(Command|View|Cronjob)\\\\/', $class ) || strpos( $path, '/tests/' ) !== false )
        {
            $loaded++;
            continue;
        }
        if ( class_exists( $class, false ) || interface_exists( $class, false ) || trait_exists( $class, false ) )
        {
            $loaded++;
            continue;
        }
        try
        {
            if ( class_exists( $class ) || interface_exists( $class ) || trait_exists( $class ) )
                $loaded++;
            else
                $notes[] = "$class is not defined by $path (defined conditionally?)";
        }
        catch ( Throwable $e )
        {
            // a parent or interface of an optional dependency (MongoDB, Symfony, ...) is not installed
            if ( preg_match( '/^(Class|Interface|Trait) "?([^" ]+)"? not found/', $e->getMessage(), $m )
                 && !isset( $classes[$m[2]] ) )
            {
                $skipped[] = "$class (needs {$m[2]})";
                continue;
            }
            $failures[] = "$class: " . get_class( $e ) . ': ' . $e->getMessage();
        }
    }
    $cli->output( sprintf( '  %s classes: %d of %d loaded', $label, $loaded, count( $classes ) ) );
}
if ( $skipped )
    $cli->output( '  skipped (optional dependency not installed): ' . count( $skipped ) . "\n    " . implode( "\n    ", array_slice( $skipped, 0, 30 ) ) );
if ( $notes )
    $cli->output( '  notes: ' . count( $notes ) . "\n    " . implode( "\n    ", array_slice( $notes, 0, 30 ) ) );

// 2. the compat functions
foreach ( array( 'array_is_list' ) as $function )
{
    if ( !function_exists( $function ) )
        $failures[] = "function $function() is missing";
}
if ( function_exists( 'array_is_list' ) && ( !array_is_list( array( 1, 2 ) ) || array_is_list( array( 1 => 1 ) ) ) )
    $failures[] = 'array_is_list() answers wrongly';

// 3. a template, and the wash operator
$tpl = eZTemplate::factory();
$tpl->setVariable( 'text', "It's <b>" );
$file = eZSys::cacheDirectory() . '/bootsmoke.tpl';
eZFile::create( basename( $file ), dirname( $file ), "{\$text|wash}|{'a'|upcase}|{array(1,2)|count}" );
$out = $tpl->fetch( 'file:' . $file );
$expected = 'It&#039;s &lt;b&gt;|A|2';
if ( $out !== $expected )
    $failures[] = "template output '$out', expected '$expected'";
else
    $cli->output( '  template and wash: ' . $out );
@unlink( $file );

foreach ( $failures as $f )
    $cli->output( '  FAIL ' . $f );
$cli->output( $failures ? 'FAIL: ' . count( $failures ) . ' problem(s)' : 'PASS' );
if ( $failures )
    $script->setExitCode( 1 );
return 1;

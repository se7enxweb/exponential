#!/usr/bin/env php
<?php
/**
 * Checks changed PHP files against the code style in phpcs.xml.dist, and fails
 * only for new violations.
 *
 * Much of the old code does not follow every rule, so a whole-file check would
 * refuse every edit of an old file. Instead the number of violations of each
 * changed file is compared with its previous version: the check fails when a
 * change adds violations, and shows the report of that file.
 *
 *   php bin/php/checkcodestyle.php --staged          staged files against HEAD (git hook)
 *   php bin/php/checkcodestyle.php --base=origin/main files changed since the merge base
 *                                                    with that ref (make phpcs, CI)
 *
 * Without the development tools (make devtools) the check is skipped with a hint.
 * Exit code 0 when no change adds violations, 1 otherwise.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package bin
 */

if ( PHP_SAPI !== 'cli' )
{
    exit( 1 );
}

chdir( dirname( __DIR__, 2 ) );

$phpcs = '.devtools/vendor/bin/phpcs';
if ( !is_file( $phpcs ) )
{
    echo "checkcodestyle: skipped, the development tools are not installed (run: make devtools)\n";
    exit( 0 );
}

$mode = isset( $argv[1] ) ? $argv[1] : '';
if ( $mode === '--staged' )
{
    $oldRef = 'HEAD';
    $newRef = ':';
    $names = shell_exec( 'git diff --cached --name-only --diff-filter=ACMR -z 2>/dev/null' );
}
else if ( strpos( $mode, '--base=' ) === 0 )
{
    $base = trim( (string)shell_exec( 'git merge-base ' . escapeshellarg( substr( $mode, 7 ) ) . ' HEAD 2>/dev/null' ) );
    if ( $base === '' )
    {
        fwrite( STDERR, "checkcodestyle: no merge base with " . substr( $mode, 7 ) . "\n" );
        exit( 1 );
    }
    $oldRef = $base;
    $newRef = false;
    $names = shell_exec( 'git diff --name-only --diff-filter=ACMR -z ' . escapeshellarg( $base ) . ' 2>/dev/null' );
}
else
{
    fwrite( STDERR, "Usage: php bin/php/checkcodestyle.php --staged|--base=REF\n" );
    exit( 1 );
}

/**
 * Returns the content of $path at $ref (':' is the index), the working tree file
 * when $ref is false, or false when the file does not exist there
 *
 * @param string $path
 * @param string|false $ref
 * @return string|false
 */
function fileAt( $path, $ref )
{
    if ( $ref === false )
    {
        return is_file( $path ) ? file_get_contents( $path ) : false;
    }
    $spec = $ref === ':' ? ':' . $path : $ref . ':' . $path;
    exec( 'git cat-file -e ' . escapeshellarg( $spec ) . ' 2>/dev/null', $output, $status );
    if ( $status !== 0 )
    {
        return false;
    }
    return (string)shell_exec( 'git show ' . escapeshellarg( $spec ) );
}

/**
 * Runs phpcs on $content as $path and returns array( count, report text )
 *
 * @param string $phpcs
 * @param string $path
 * @param string $content
 * @param string $report json or full
 * @return array
 */
function runPhpcs( $phpcs, $path, $content, $report )
{
    $command = 'php ' . escapeshellarg( $phpcs ) . ' -q --no-colors --report=' . $report . ' --stdin-path=' . escapeshellarg( $path ) . ' -';
    $process = proc_open( $command, array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
    if ( !is_resource( $process ) )
    {
        return array( 0, '' );
    }
    fwrite( $pipes[0], $content );
    fclose( $pipes[0] );
    $output = stream_get_contents( $pipes[1] );
    fclose( $pipes[1] );
    fclose( $pipes[2] );
    proc_close( $process );

    if ( $report !== 'json' )
    {
        return array( 0, $output );
    }
    $data = json_decode( $output, true );
    $count = 0;
    if ( is_array( $data ) && isset( $data['totals'] ) )
    {
        $count = (int)$data['totals']['errors'] + (int)$data['totals']['warnings'];
    }
    return array( $count, '' );
}

$failed = 0;
$checked = 0;
foreach ( array_filter( explode( "\0", (string)$names ), 'strlen' ) as $path )
{
    if ( substr( $path, -4 ) !== '.php' )
    {
        continue;
    }
    $new = fileAt( $path, $newRef );
    if ( $new === false )
    {
        continue;
    }
    $old = fileAt( $path, $oldRef );
    $checked++;

    list( $newCount ) = runPhpcs( $phpcs, $path, $new, 'json' );
    list( $oldCount ) = $old === false ? array( 0 ) : runPhpcs( $phpcs, $path, $old, 'json' );
    if ( $newCount > $oldCount )
    {
        $failed++;
        echo "$path: $newCount style violation(s), " . ( $old === false ? 'new file' : "$oldCount before this change" ) . "\n";
        list( , $text ) = runPhpcs( $phpcs, $path, $new, 'full' );
        echo $text;
    }
}

if ( $failed )
{
    echo "checkcodestyle: $failed of $checked changed PHP file(s) add style violations (rules: phpcs.xml.dist)\n";
    exit( 1 );
}
echo "checkcodestyle: $checked changed PHP file(s), no new style violations\n";
exit( 0 );

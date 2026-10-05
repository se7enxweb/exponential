#!/usr/bin/env php
<?php
/**
 * Checks share/filelist.md5 against the files of the installation.
 *
 * The file manifest lists "md5  path" for every file of the distribution; the
 * system upgrade check of the admin interface compares the files against it, so
 * a stale entry shows up there as a modified file. This script needs no kernel
 * and no database, so the git hooks and CI can run it on every commit.
 *
 *   php bin/php/checkmanifest.php --all     every entry exists and has its checksum;
 *                                           git files missing from the manifest are warnings
 *   php bin/php/checkmanifest.php --staged  the files staged for the next commit are listed
 *                                           with the checksum of their staged content
 *   php bin/php/checkmanifest.php --fix     rewrites stale checksums, adds missing files at
 *                                           their sorted place and drops entries of removed
 *                                           files; the order of the other lines is kept
 *
 * Exit code 0 when everything matches, 1 otherwise.
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

/**
 * The manifest file, relative to the root of the installation
 */
const EXP_MANIFEST_FILE = 'share/filelist.md5';

/**
 * Paths outside the manifest: the manifest itself, runtime data and the files
 * of extensions that ship their own checksum list
 */
$manifestExcludes = array( 'share/filelist.md5', 'var/', 'extension/ezoe/' );

/**
 * Returns true when $path belongs into the manifest
 *
 * @param string $path
 * @param array $excludes
 * @return bool
 */
function manifestCovers( $path, $excludes )
{
    foreach ( $excludes as $exclude )
    {
        if ( $path === $exclude || ( substr( $exclude, -1 ) === '/' && strpos( $path, $exclude ) === 0 ) )
        {
            return false;
        }
    }
    if ( strpos( $path, '/__pycache__/' ) !== false || substr( $path, -4 ) === '.pyc' )
    {
        return false;
    }
    return true;
}

/**
 * Runs a git command and returns its output split by NUL, or false on failure
 *
 * @param string $arguments
 * @return array|false
 */
function gitList( $arguments )
{
    $output = shell_exec( 'git ' . $arguments . ' 2>/dev/null' );
    if ( !is_string( $output ) )
    {
        return false;
    }
    return array_values( array_filter( explode( "\0", $output ), 'strlen' ) );
}

/**
 * Parses manifest text into an ordered list of array( md5, path )
 *
 * @param string $text
 * @return array
 */
function parseManifest( $text )
{
    $entries = array();
    foreach ( explode( "\n", $text ) as $line )
    {
        if ( isset( $line[34] ) && substr( $line, 32, 2 ) === '  ' )
        {
            $entries[] = array( substr( $line, 0, 32 ), substr( $line, 34 ) );
        }
    }
    return $entries;
}

$mode = isset( $argv[1] ) ? $argv[1] : '';
if ( !in_array( $mode, array( '--all', '--staged', '--fix' ), true ) )
{
    fwrite( STDERR, "Usage: php bin/php/checkmanifest.php --all|--staged|--fix\n" );
    exit( 1 );
}

$errors = array();
$warnings = array();

if ( $mode === '--staged' )
{
    // Compare with the manifest as it is staged, so a commit that updates both passes
    $staged = shell_exec( 'git show :' . escapeshellarg( EXP_MANIFEST_FILE ) . ' 2>/dev/null' );
    $entries = parseManifest( is_string( $staged ) ? $staged : (string)@file_get_contents( EXP_MANIFEST_FILE ) );
    $sums = array();
    foreach ( $entries as $entry )
    {
        $sums[$entry[1]] = $entry[0];
    }

    $changes = gitList( 'diff --cached --name-status --no-renames -z' );
    if ( $changes === false )
    {
        fwrite( STDERR, "checkmanifest: git is not available\n" );
        exit( 1 );
    }
    for ( $i = 0; $i + 1 < count( $changes ); $i += 2 )
    {
        $status = $changes[$i];
        $path = $changes[$i + 1];
        if ( !manifestCovers( $path, $manifestExcludes ) )
        {
            continue;
        }
        if ( $status === 'D' )
        {
            if ( isset( $sums[$path] ) )
            {
                $errors[] = "$path is removed but still listed";
            }
            continue;
        }
        $content = shell_exec( 'git show :' . escapeshellarg( $path ) );
        $md5 = md5( is_string( $content ) ? $content : '' );
        if ( !isset( $sums[$path] ) )
        {
            $errors[] = "$path is not listed";
        }
        else if ( $sums[$path] !== $md5 )
        {
            $errors[] = "$path has checksum {$sums[$path]} in the manifest, the staged file has $md5";
        }
    }
}
else
{
    $entries = parseManifest( (string)@file_get_contents( EXP_MANIFEST_FILE ) );
    if ( !$entries )
    {
        fwrite( STDERR, "checkmanifest: " . EXP_MANIFEST_FILE . " is missing or empty\n" );
        exit( 1 );
    }

    $tracked = gitList( 'ls-files -z' );
    if ( $tracked === false )
    {
        fwrite( STDERR, "checkmanifest: git is not available\n" );
        exit( 1 );
    }
    $tracked = array_flip( $tracked );

    $listed = array();
    $kept = array();
    $previous = '';
    foreach ( $entries as $entry )
    {
        list( $md5, $path ) = $entry;
        $listed[$path] = true;
        if ( !is_file( $path ) )
        {
            $errors[] = "$path is listed but does not exist";
            continue;
        }
        $current = md5_file( $path );
        if ( $current !== $md5 )
        {
            $errors[] = "$path has checksum $md5 in the manifest, the file has $current";
        }
        if ( strcmp( $previous, $path ) > 0 )
        {
            $warnings[] = "$path is out of order (after $previous)";
        }
        $previous = $path;
        $kept[] = array( $current, $path );
    }

    $missing = array();
    foreach ( array_keys( $tracked ) as $path )
    {
        if ( !isset( $listed[$path] ) && manifestCovers( $path, $manifestExcludes ) && is_file( $path ) )
        {
            $missing[] = $path;
            $warnings[] = "$path is in git but not listed";
        }
    }

    if ( $mode === '--fix' )
    {
        // Insert each missing file before the first entry that sorts after it
        sort( $missing, SORT_STRING );
        foreach ( $missing as $path )
        {
            $line = array( md5_file( $path ), $path );
            $position = count( $kept );
            foreach ( $kept as $index => $entry )
            {
                if ( strcmp( $entry[1], $path ) > 0 )
                {
                    $position = $index;
                    break;
                }
            }
            array_splice( $kept, $position, 0, array( $line ) );
        }
        $text = '';
        foreach ( $kept as $entry )
        {
            $text .= $entry[0] . '  ' . $entry[1] . "\n";
        }
        file_put_contents( EXP_MANIFEST_FILE, $text );
        echo "checkmanifest: " . EXP_MANIFEST_FILE . " rewritten (" . count( $errors ) . " corrected, " . count( $missing ) . " added)\n";
        exit( 0 );
    }
}

foreach ( $warnings as $warning )
{
    echo "warning: $warning\n";
}
foreach ( $errors as $error )
{
    echo "error: $error\n";
}
if ( $errors )
{
    echo "checkmanifest: " . count( $errors ) . " problem(s) in " . EXP_MANIFEST_FILE . "; run: make manifest-fix\n";
    exit( 1 );
}
echo "checkmanifest: " . EXP_MANIFEST_FILE . " matches" . ( $warnings ? ' (' . count( $warnings ) . ' warning(s))' : '' ) . "\n";
exit( 0 );

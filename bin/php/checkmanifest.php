#!/usr/bin/env php
<?php
/**
 * Checks share/filelist.md5 against the files of the installation.
 *
 * The file manifest lists "md5  path" for every file of the distribution; the
 * system upgrade check of the admin interface compares the files against it, so
 * a stale entry shows up there as a modified file. This script needs no kernel
 * and no database, so the git hooks and CI can run it on every commit. It reads
 * the manifests with the same class as the upgrade check
 * (kernel/private/classes/expfileconsistencyreport.php), so both always agree.
 *
 *   php bin/php/checkmanifest.php --all     every entry exists and has its checksum;
 *                                           git files missing from the manifest, lines out
 *                                           of order and malformed lines are warnings
 *   php bin/php/checkmanifest.php --staged  the files staged for the next commit are listed
 *                                           with the checksum of their staged content
 *   php bin/php/checkmanifest.php --fix     rewrites stale checksums, adds missing files at
 *                                           their sorted place and drops entries of removed
 *                                           files; the order of the other lines is kept
 *
 * With --all:
 *   --extensions   also checks the share/filelist.md5 every extension may carry of its own
 *                  (every extension directory that has one, active or not)
 *   --csv          prints the findings as CSV, the same columns as the download of the
 *                  upgrade check, instead of the lines below
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

require_once 'kernel/private/classes/expfileconsistencyreport.php';

/**
 * The manifest file, relative to the root of the installation
 */
const EXP_MANIFEST_FILE = expFileConsistencyReport::MANIFEST_FILE;

/**
 * Paths outside the manifest: the manifest itself, runtime data and the files
 * of extensions that ship their own checksum list
 */
$manifestExcludes = expFileConsistencyReport::ROOT_EXCLUDES;

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

$arguments = array_slice( $argv, 1 );
$mode = '';
$withExtensions = false;
$csv = false;
foreach ( $arguments as $argument )
{
    if ( in_array( $argument, array( '--all', '--staged', '--fix' ), true ) && $mode === '' )
    {
        $mode = $argument;
    }
    else if ( $argument === '--extensions' )
    {
        $withExtensions = true;
    }
    else if ( $argument === '--csv' )
    {
        $csv = true;
    }
    else
    {
        $mode = '';
        break;
    }
}
if ( $mode === '' || ( ( $withExtensions || $csv ) && $mode !== '--all' ) )
{
    fwrite( STDERR, "Usage: php bin/php/checkmanifest.php --all [--extensions] [--csv] | --staged | --fix\n" );
    exit( 1 );
}

$errors = array();
$warnings = array();

if ( $mode === '--staged' )
{
    // Compare with the manifest as it is staged, so a commit that updates both passes
    $staged = shell_exec( 'git show :' . escapeshellarg( EXP_MANIFEST_FILE ) . ' 2>/dev/null' );
    $parsed = expFileConsistencyReport::parseManifest( is_string( $staged ) ? $staged : (string)@file_get_contents( EXP_MANIFEST_FILE ) );
    $sums = array();
    foreach ( $parsed['entries'] as $entry )
    {
        $sums[$entry['path']] = $entry['md5'];
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
        if ( !expFileConsistencyReport::covers( $path, $manifestExcludes ) )
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
    $parsed = expFileConsistencyReport::parseManifest( (string)@file_get_contents( EXP_MANIFEST_FILE ) );
    if ( !$parsed['entries'] )
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

    $extensions = $withExtensions ? expFileConsistencyReport::extensionDirectoriesWithManifest( '.' ) : array();
    $report = expFileConsistencyReport::forInstallation( '.', $extensions );
    $report->setTrackedFiles( 'exponential', $tracked, $manifestExcludes );
    foreach ( $extensions as $name => $directory )
    {
        // an extension that is a git checkout of its own: its tracked files belong in its own list
        if ( ( $own = expFileConsistencyReport::gitTrackedFiles( $directory ) ) !== false )
        {
            $report->setTrackedFiles( $name, $own, array( EXP_MANIFEST_FILE ) );
        }
    }
    $result = $report->run();

    if ( $mode === '--fix' )
    {
        // Every listed file that still exists keeps its place with its current checksum; each file git tracks that
        // is not listed goes in before the first entry that sorts after it. Malformed lines are dropped.
        $missing = array();
        $gone = array();
        foreach ( $result['items'] as $item )
        {
            if ( $item['manifest'] !== 'exponential' )
                continue;
            if ( $item['state'] === expFileConsistencyReport::STATE_UNLISTED )
                $missing[] = $item['path'];
            else if ( $item['state'] === expFileConsistencyReport::STATE_MISSING || $item['state'] === expFileConsistencyReport::STATE_UNREADABLE )
                $gone[$item['path']] = true;
        }
        $kept = array();
        foreach ( $parsed['entries'] as $entry )
        {
            if ( !isset( $gone[$entry['path']] ) )
                $kept[] = array( md5_file( $entry['path'] ), $entry['path'] );
        }
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
        echo "checkmanifest: " . EXP_MANIFEST_FILE . " rewritten (" . $result['problems'] . " corrected, " . count( $missing ) . " added)\n";
        exit( 0 );
    }

    if ( $csv )
    {
        $out = fopen( 'php://stdout', 'w' );
        $report->writeCsv( $out, false );
        exit( $result['status'] === expFileConsistencyReport::STATUS_OK ? 0 : 1 );
    }

    foreach ( $result['items'] as $item )
    {
        if ( in_array( $item['state'], expFileConsistencyReport::PROBLEM_STATES, true ) )
        {
            $errors[] = expFileConsistencyReport::describe( $item );
        }
        else
        {
            $warnings[] = expFileConsistencyReport::describe( $item );
        }
    }
    foreach ( $result['manifests'] as $manifest )
    {
        if ( !$manifest['readable'] )
        {
            $errors[] = $manifest['file'] . ' cannot be read';
        }
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
    echo "checkmanifest: " . count( $errors ) . " problem(s) in " . EXP_MANIFEST_FILE . ( $withExtensions ? ' and the extension manifests' : '' ) . "; run: make manifest-fix\n";
    exit( 1 );
}
echo "checkmanifest: " . EXP_MANIFEST_FILE . ( $withExtensions ? ' and the extension manifests' : '' ) . " matches" . ( $warnings ? ' (' . count( $warnings ) . ' warning(s))' : '' ) . "\n";
exit( 0 );

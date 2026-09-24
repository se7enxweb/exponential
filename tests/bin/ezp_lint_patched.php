#!/usr/bin/env php
<?php
/**
 * ezp_lint_patched.php — Reusable PHP lint tester for patched files.
 *
 * Usage:  php -n -l tests/bin/ezp_lint_patched.php           (lint this script itself)
 *         php tests/bin/ezp_lint_patched.php                  (lint all files in MANIFEST)
 *         php tests/bin/ezp_lint_patched.php <file> [file…]  (lint specific files only)
 *
 * Reports:
 *   - PASS / FAIL per file
 *   - Summary counts
 *   - Exit code 0 = all pass, 1 = one or more failures
 *
 * NOTE: Uses `php -n -l` (no php.ini, syntax-check only) to avoid the
 *       known server extension-load bug.
 */

define( 'ROOT',     __DIR__ . '/../../' );
define( 'MANIFEST', ROOT . 'tests/bin/lint_manifest.txt' );
define( 'PHP_BIN',  PHP_BINARY );

// ── Collect file list ──────────────────────────────────────────────────────
if ( $argc > 1 ) {
    // Files passed on the command line (relative to ROOT or absolute)
    $files = array_slice( $argv, 1 );
    foreach ( $files as &$f ) {
        if ( $f[0] !== '/' ) {
            $f = ROOT . ltrim( $f, '/' );
        }
    }
    unset( $f );
} elseif ( file_exists( MANIFEST ) ) {
    $lines = file( MANIFEST, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
    $files = [];
    foreach ( $lines as $line ) {
        $line = trim( $line );
        if ( $line === '' || $line[0] === '#' ) continue;
        $abs = ( $line[0] === '/' ) ? $line : ROOT . $line;
        $files[] = $abs;
    }
} else {
    echo "ERROR: No files supplied and no manifest found at " . MANIFEST . "\n";
    echo "       Create tests/bin/lint_manifest.txt or pass file paths as arguments.\n";
    exit( 1 );
}

// ── Run lint ───────────────────────────────────────────────────────────────
$pass   = 0;
$fail   = 0;
$skip   = 0;
$errors = [];

$colWidth = 60;

echo str_repeat( '─', 80 ) . "\n";
printf( "%-{$colWidth}s  %s\n", 'FILE', 'RESULT' );
echo str_repeat( '─', 80 ) . "\n";

foreach ( $files as $absPath ) {
    $rel = str_replace( ROOT, '', $absPath );

    if ( !file_exists( $absPath ) ) {
        printf( "%-{$colWidth}s  [SKIP — not found]\n", $rel );
        $skip++;
        continue;
    }

    $cmd    = escapeshellarg( PHP_BIN ) . ' -n -l ' . escapeshellarg( $absPath ) . ' 2>&1';
    $output = [];
    $code   = 0;
    exec( $cmd, $output, $code );

    if ( $code === 0 ) {
        printf( "%-{$colWidth}s  ✓ PASS\n", $rel );
        $pass++;
    } else {
        printf( "%-{$colWidth}s  ✗ FAIL\n", $rel );
        foreach ( $output as $line ) {
            if ( trim( $line ) !== '' ) {
                echo "     → " . trim( $line ) . "\n";
            }
        }
        $fail++;
        $errors[] = $rel;
    }
}

// ── Summary ────────────────────────────────────────────────────────────────
echo str_repeat( '─', 80 ) . "\n";
printf( "TOTAL: %d files   ✓ %d PASS   ✗ %d FAIL   ~ %d SKIP\n",
        count( $files ), $pass, $fail, $skip );

if ( $fail > 0 ) {
    echo "\nFailed files:\n";
    foreach ( $errors as $e ) {
        echo "  • $e\n";
    }
}

echo str_repeat( '─', 80 ) . "\n";
exit( $fail > 0 ? 1 : 0 );

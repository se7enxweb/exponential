#!/usr/bin/env php
<?php
/**
 * eZScript-style combined test runner.
 *
 * Runs both the lint suite and the functional flow test suite, then produces
 * a consolidated PASS/FAIL report and (optionally) writes a dated report file.
 *
 * Usage:
 *   php tests/bin/ezptestrunner_all_tests.php                  # all tests, all categories
 *   php tests/bin/ezptestrunner_all_tests.php security          # functional: security only, full lint
 *   php tests/bin/ezptestrunner_all_tests.php php84             # functional: php84 only, full lint
 *   php tests/bin/ezptestrunner_all_tests.php --report          # all tests + write dated .txt report
 *   php tests/bin/ezptestrunner_all_tests.php security --report # filtered + report
 *   php tests/bin/ezptestrunner_all_tests.php --list-categories # list available functional categories
 *
 * Categories (functional tests):
 *   security  soap  preg  dom  null  array  php84  setup  image
 *
 * Exit codes:
 *   0  All tests pass
 *   1  One or more failures
 *   2  Script environment error
 */

// ─── bootstrap ───────────────────────────────────────────────────────────────
$ROOT      = dirname( __DIR__, 2 );
$phpBin    = PHP_BINARY;
$selfDir   = __DIR__;
$timestamp = date( 'Y-m-d H:i:s' );
$dateSlug  = date( 'Y-m-d' );

// ─── argument parsing ─────────────────────────────────────────────────────────
$args          = array_slice( $argv, 1 );
$writeReport   = in_array( '--report', $args, true );
$listCats      = in_array( '--list-categories', $args, true );
$filterArgs    = array_filter( $args, fn( $a ) => $a[0] !== '-' );
$category      = array_values( $filterArgs )[0] ?? 'all';

$knownCategories = [ 'security', 'soap', 'preg', 'dom', 'null', 'array', 'php84', 'setup', 'image' ];

if ( $listCats ) {
    echo "Available functional test categories:\n";
    foreach ( $knownCategories as $cat ) {
        echo "  $cat\n";
    }
    echo "  all  (default — runs every category)\n";
    exit( 0 );
}

// ─── helpers ─────────────────────────────────────────────────────────────────
function hr( string $char = '─', int $width = 80 ): string
{
    return str_repeat( $char, $width ) . "\n";
}

function runScript( string $phpBin, string $script, string $extraArg = '' ): array
{
    $cmd     = escapeshellarg( $phpBin ) . ' ' . escapeshellarg( $script );
    if ( $extraArg !== '' ) $cmd .= ' ' . escapeshellarg( $extraArg );
    $output  = [];
    $retCode = 0;
    exec( $cmd . ' 2>&1', $output, $retCode );
    return [ 'output' => implode( "\n", $output ), 'code' => $retCode ];
}

function extractCounts( string $output, string $passWord, string $failWord ): array
{
    $pass = 0; $fail = 0; $skip = 0;
    // TOTAL line: "TOTAL: N tests   ✓ P PASS   ✗ F FAIL"
    if ( preg_match( '/TOTAL:\s*(\d+)\s+tests\s+.*?(\d+)\s+PASS\s+.*?(\d+)\s+FAIL/', $output, $m ) ) {
        $pass = (int)$m[2]; $fail = (int)$m[3];
    }
    // fallback: count individual lines
    if ( $pass === 0 && $fail === 0 ) {
        preg_match_all( '/✓\s+PASS/', $output, $pm );
        preg_match_all( '/✗\s+FAIL/', $output, $fm );
        preg_match_all( '/⊘\s+SKIP/', $output, $sm );
        $pass = count( $pm[0] ); $fail = count( $fm[0] ); $skip = count( $sm[0] );
    }
    return compact( 'pass', 'fail', 'skip' );
}

// ─── paths ───────────────────────────────────────────────────────────────────
$lintScript       = $selfDir . '/ezp_lint_patched.php';
$functionalScript = $selfDir . '/tests/functional_tests.php';
$reportPath       = $selfDir . '/test_report_' . $dateSlug . '.txt';

foreach ( [ $lintScript, $functionalScript ] as $req ) {
    if ( !file_exists( $req ) ) {
        echo "ERROR: Required script not found: $req\n";
        exit( 2 );
    }
}

// ─── header ──────────────────────────────────────────────────────────────────
$header  = hr();
$header .= "  eZScript Combined Test Runner — $timestamp\n";
$header .= "  PHP: " . PHP_VERSION . " | Root: $ROOT\n";
$header .= "  Functional filter: $category\n";
$header .= hr();
echo $header;

// ─── PHASE 1: Lint ───────────────────────────────────────────────────────────
echo "\n── Phase 1: Syntax Lint (" . basename( $lintScript ) . ") ──\n\n";
$lintResult  = runScript( $phpBin, $lintScript );
$lintCounts  = extractCounts( $lintResult['output'], 'PASS', 'FAIL' );

// patch extractCounts for lint (uses different format)
if ( preg_match( '/(\d+)\/(\d+)\s+PASS/', $lintResult['output'], $lm ) ) {
    $lintCounts['pass'] = (int)$lm[1];
    $lintCounts['fail'] = (int)$lm[2] - (int)$lm[1];
}
if ( preg_match( '/(\d+)\s+SKIP/', $lintResult['output'], $sm2 ) ) {
    $lintCounts['skip'] = (int)$sm2[1];
}

echo $lintResult['output'] . "\n";

// ─── PHASE 2: Functional Tests ───────────────────────────────────────────────
echo "\n── Phase 2: Functional Flow Tests (" . basename( $functionalScript ) . ") ──\n";
if ( $category !== 'all' ) {
    echo "   Category filter: $category\n";
}
echo "\n";

$funcResult = runScript( $phpBin, $functionalScript, $category );
$funcCounts = extractCounts( $funcResult['output'], 'PASS', 'FAIL' );
echo $funcResult['output'] . "\n";

// ─── PHASE 3: Combined Summary ───────────────────────────────────────────────
$totalPass = $lintCounts['pass'] + $funcCounts['pass'];
$totalFail = $lintCounts['fail'] + $funcCounts['fail'];
$totalSkip = ( $lintCounts['skip'] ?? 0 ) + ( $funcCounts['skip'] ?? 0 );
$overallOk = $totalFail === 0;

$summary  = "\n" . hr( '═' );
$summary .= "  COMBINED RESULTS — $timestamp\n";
$summary .= hr( '═' );
$summary .= sprintf(
    "  %-28s  PASS: %3d   FAIL: %3d   SKIP: %3d\n",
    'Lint (php -n -l)',
    $lintCounts['pass'],
    $lintCounts['fail'],
    $lintCounts['skip'] ?? 0
);
$summary .= sprintf(
    "  %-28s  PASS: %3d   FAIL: %3d   SKIP: %3d\n",
    "Functional [$category]",
    $funcCounts['pass'],
    $funcCounts['fail'],
    $funcCounts['skip'] ?? 0
);
$summary .= hr( '─' );
$summary .= sprintf(
    "  %-28s  PASS: %3d   FAIL: %3d   SKIP: %3d\n",
    'TOTAL',
    $totalPass,
    $totalFail,
    $totalSkip
);
$summary .= hr( '─' );
$summary .= "  Overall status: " . ( $overallOk ? "✓ ALL PASS" : "✗ FAILURES DETECTED" ) . "\n";
$summary .= hr( '═' );

echo $summary;

// ─── PHASE 4: Failure detail ─────────────────────────────────────────────────
if ( $totalFail > 0 ) {
    echo "\nFailed items:\n";
    // extract FAIL lines from both outputs
    foreach ( explode( "\n", $lintResult['output'] . "\n" . $funcResult['output'] ) as $line ) {
        if ( strpos( $line, '✗ FAIL' ) !== false || strpos( $line, 'FAIL' ) !== false && strpos( $line, '✓' ) === false ) {
            if ( trim( $line ) !== '' ) echo "  " . trim( $line ) . "\n";
        }
    }
    echo "\n";
}

// ─── PHASE 5: Write report ───────────────────────────────────────────────────
if ( $writeReport ) {
    $reportBody  = $header;
    $reportBody .= "\n=== Phase 1: Lint Output ===\n\n";
    $reportBody .= $lintResult['output'] . "\n";
    $reportBody .= "\n=== Phase 2: Functional Output (filter: $category) ===\n\n";
    $reportBody .= $funcResult['output'] . "\n";
    $reportBody .= $summary;

    $reportBody .= "\n=== File Manifest (patched files) ===\n";
    $manifestFile = $selfDir . '/lint_manifest.txt';
    if ( file_exists( $manifestFile ) ) {
        foreach ( file( $manifestFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
            if ( $line[0] === '#' ) continue;
            $abs = $ROOT . '/' . trim( $line );
            $exists = file_exists( $abs ) ? '✓' : '✗';
            $reportBody .= "  $exists  $line\n";
        }
    }

    $reportBody .= "\n=== Generated by ezptestrunner_all_tests.php on $timestamp ===\n";

    file_put_contents( $reportPath, $reportBody );
    echo "Report written: $reportPath\n";
}

exit( $overallOk ? 0 : 1 );

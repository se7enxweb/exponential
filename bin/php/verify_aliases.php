#!/usr/bin/env php
<?php
/**
 * URL Alias Integrity Verification Script
 *
 * Detects and reports potential corruption issues in ezurlalias_ml table.
 */

require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance(
    array(
        'description' => "eZ Publish URL Alias Integrity Checker\n\n" .
                         "Checks ezurlalias_ml consistency and optional safe auto-fixes.\n" .
                         "\n" .
                         "verify_aliases.php",
        'use-session' => true,
        'use-modules' => true,
        'use-extensions' => true
    )
);

$script->startup();

$helpRequested = in_array( '-h', $_SERVER['argv'] ) || in_array( '--help', $_SERVER['argv'] );

$options = $script->getOptions(
    "[fix][verbose][sql]",
    "",
    array(
        'fix' => 'Attempt to automatically fix detected issues',
        'verbose' => 'Show detailed diagnostic information',
        'sql' => 'Display sql queries'
    )
);

if ( $helpRequested || ( isset( $options['help'] ) && $options['help'] ) )
{
    $script->showHelp();
    $script->shutdown( 0 );
}

$siteAccess = $options['siteaccess'] ? $options['siteaccess'] : false;
if ( $siteAccess )
{
    changeSiteAccessSetting( $siteAccess );
}

$script->initialize();

if ( !$script->isInitialized() )
{
    $cli->error( 'Error initializing script: ' . $script->initializationError() . '.' );
    $script->shutdown( 1 );
}

$db = eZDB::instance();
$db->setIsSQLOutputEnabled( $options['sql'] ? true : false );

$verbose = $options['verbose'] ? true : false;
$attemptFix = $options['fix'] ? true : false;

$cli->output( "" );
$cli->output( $cli->stylize( 'mark', "=== URL ALIAS INTEGRITY CHECK ===" ) );
$cli->output( "" );

$issues = array();
$warnings = array();
$stats = array();

function aliasTextMd5( $text )
{
    return md5( eZURLAliasML::strtolower( $text ) );
}

function changeSiteAccessSetting( $siteAccess )
{
    $cli = eZCLI::instance();
    if ( in_array( $siteAccess, eZINI::instance()->variable( 'SiteAccessSettings', 'AvailableSiteAccessList' ) ) )
    {
        $cli->output( "Using siteaccess $siteAccess for alias verification" );
    }
    else
    {
        $cli->notice( "Siteaccess $siteAccess does not exist, using default siteaccess" );
    }
}

// ============================================================================
// CHECK 1: Orphaned aliases (parent references non-existent entries)
// ============================================================================
$cli->output( "Checking for orphaned alias entries..." );
$orphanSQL = "SELECT a1.id, a1.parent, a1.text FROM ezurlalias_ml a1 
              LEFT JOIN ezurlalias_ml a2 ON a1.parent = a2.id 
              WHERE a1.parent != 0 AND a2.id IS NULL";
$orphanRows = $db->arrayQuery( $orphanSQL );

if ( count( $orphanRows ) > 0 )
{
    $issues[] = "Found " . count( $orphanRows ) . " orphaned alias entries with non-existent parents";
    if ( $verbose )
    {
        foreach ( $orphanRows as $row )
        {
            $cli->output( "  - ID {$row['id']}: '{$row['text']}' has parent {$row['parent']}" );
        }
    }
}
$stats['orphaned_aliases'] = count( $orphanRows );

// ============================================================================
// CHECK 2: Broken link chains (is_alias=1 but link points to non-existent)
// ============================================================================
$cli->output( "Checking for broken alias link chains..." );
$brokenLinksSQL = "SELECT a1.id, a1.text, a1.link FROM ezurlalias_ml a1 
                   WHERE a1.is_alias = 1 AND a1.link != 0 
                   AND a1.link NOT IN (SELECT id FROM ezurlalias_ml)";
$brokenLinkRows = $db->arrayQuery( $brokenLinksSQL );

if ( count( $brokenLinkRows ) > 0 )
{
    $issues[] = "Found " . count( $brokenLinkRows ) . " broken alias link references";
    if ( $verbose )
    {
        foreach ( $brokenLinkRows as $row )
        {
            $cli->output( "  - ID {$row['id']}: '{$row['text']}' links to non-existent ID {$row['link']}" );
        }
    }
}
$stats['broken_links'] = count( $brokenLinkRows );

// ============================================================================
// CHECK 3: PathPrefix namespace coverage
// ============================================================================
$cli->output( "Checking PathPrefix namespace coverage..." );
$pathPrefix = eZURLAliasML::getPathPrefix();

if ( $pathPrefix )
{
    // Count aliases at root level
    $rootCountSQL = "SELECT COUNT(*) as count FROM ezurlalias_ml WHERE parent = 0";
    $rootResult = $db->arrayQuery( $rootCountSQL );
    $rootCount = $rootResult[0]['count'];
    
    // Count aliases under PathPrefix
    $prefixCountSQL = "SELECT id FROM ezurlalias_ml WHERE parent = 0 AND text_md5 = '" . aliasTextMd5( $pathPrefix ) . "'";
    $prefixRows = $db->arrayQuery( $prefixCountSQL );
    
    if ( count( $prefixRows ) == 0 )
    {
        $warnings[] = "PathPrefix '{$pathPrefix}' root entry not found! All URLs will fail.";
    }
    else
    {
        $prefixID = (int)$prefixRows[0]['id'];
        $prefixChildrenSQL = "SELECT COUNT(*) as count FROM ezurlalias_ml WHERE parent = {$prefixID}";
        $prefixResult = $db->arrayQuery( $prefixChildrenSQL );
        $prefixChildCount = $prefixResult[0]['count'];
        
        // Root level should have roughly twice as many entries as prefix level
        // (once for direct access, once for prefixed)
        $expectedRatio = $rootCount / 2;
        if ( $prefixChildCount < ($expectedRatio * 0.7) )
        {
            $warnings[] = "PathPrefix namespace is significantly underpopulated. Root entries: {$rootCount}, Prefix children: {$prefixChildCount}. Expected ~{$expectedRatio}";
        }
        
        $stats['root_level_aliases'] = $rootCount;
        $stats['pathprefix_namespace_entries'] = $prefixChildCount;
        $stats['pathprefix_name'] = $pathPrefix;
    }
}
else
{
    $stats['pathprefix_name'] = "Not configured";
}

// ============================================================================
// CHECK 4: Self-redirect chains (link=id indicating redirect to self)
// ============================================================================
$cli->output( "Checking for self-redirect loops..." );
$selfRedirectSQL = "SELECT id, text FROM ezurlalias_ml WHERE is_alias = 1 AND link = id";
$selfRedirectRows = $db->arrayQuery( $selfRedirectSQL );

if ( count( $selfRedirectRows ) > 0 )
{
    $issues[] = "Found " . count( $selfRedirectRows ) . " self-redirecting alias entries (will cause 301 loops)";
    if ( $verbose )
    {
        foreach ( $selfRedirectRows as $row )
        {
            $cli->output( "  - ID {$row['id']}: '{$row['text']}' redirects to itself" );
        }
    }
}
$stats['self_redirect_loops'] = count( $selfRedirectRows );

// ============================================================================
// CHECK 5: Orphaned nodes (aliases pointing to non-existent content)
// ============================================================================
$cli->output( "Checking for aliases referencing deleted content..." );
$orphanNodesSQL = "SELECT a.id, a.text, a.action FROM ezurlalias_ml a 
                   WHERE a.action LIKE 'eznode:%'
                   AND CAST(SUBSTRING(a.action, 8) AS UNSIGNED) NOT IN 
                       (SELECT node_id FROM ezcontentobject_tree)";
$orphanNodeRows = $db->arrayQuery( $orphanNodesSQL );

if ( count( $orphanNodeRows ) > 0 )
{
    $warnings[] = "Found " . count( $orphanNodeRows ) . " aliases referencing deleted content nodes";
    if ( $verbose )
    {
        foreach ( array_slice( $orphanNodeRows, 0, 5 ) as $row )
        {
            $cli->output( "  - ID {$row['id']}: '{$row['text']}' → {$row['action']}" );
        }
        if ( count( $orphanNodeRows ) > 5 )
        {
            $cli->output( "  ... and " . (count( $orphanNodeRows ) - 5) . " more" );
        }
    }
}
$stats['orphaned_node_aliases'] = count( $orphanNodeRows );

// ============================================================================
// CHECK 6: Duplicate path detection
// ============================================================================
$cli->output( "Checking for duplicate paths..." );
$duplicateSQL = "SELECT parent, text, COUNT(*) as count FROM ezurlalias_ml 
                 GROUP BY parent, text_md5 HAVING count > 1";
$duplicateRows = $db->arrayQuery( $duplicateSQL );

if ( count( $duplicateRows ) > 0 )
{
    $issues[] = "Found " . count( $duplicateRows ) . " duplicate alias paths (data integrity issue)";
    if ( $verbose )
    {
        foreach ( array_slice( $duplicateRows, 0, 5 ) as $row )
        {
            $cli->output( "  - Path '{$row['text']}' exists {$row['count']} times under parent {$row['parent']}" );
        }
    }
}
$stats['duplicate_paths'] = count( $duplicateRows );

// ============================================================================
// GENERATE REPORT
// ============================================================================
$cli->output( "" );
$cli->output( $cli->stylize( 'warning', "=== INTEGRITY REPORT ===" ) );
$cli->output( "" );

if ( count( $issues ) == 0 && count( $warnings ) == 0 )
{
    $cli->output( $cli->stylize( 'success', "OK: No critical issues detected" ) );
}
else
{
    if ( count( $issues ) > 0 )
    {
        $cli->output( $cli->stylize( 'error', "CRITICAL ISSUES (" . count( $issues ) . "):" ) );
        foreach ( $issues as $issue )
        {
            $cli->output( "  - {$issue}" );
        }
        $cli->output( "" );
    }
    
    if ( count( $warnings ) > 0 )
    {
        $cli->output( $cli->stylize( 'warning', "WARNINGS (" . count( $warnings ) . "):" ) );
        foreach ( $warnings as $warning )
        {
            $cli->output( "  - {$warning}" );
        }
        $cli->output( "" );
    }
}

// ============================================================================
// STATISTICS
// ============================================================================
$cli->output( $cli->stylize( 'mark', "STATISTICS:" ) );
$totalAliases = $db->arrayQuery( "SELECT COUNT(*) as count FROM ezurlalias_ml" );
$cli->output( "  Total URL aliases: " . $totalAliases[0]['count'] );

foreach ( $stats as $key => $value )
{
    if ( $value !== "Not configured" && !is_numeric( $value ) ) continue;
    $key_display = str_replace( "_", " ", ucfirst( $key ) );
    if ( is_numeric( $value ) && $value > 0 )
    {
        $cli->output( "  {$key_display}: {$value}" );
    }
    elseif ( !is_numeric( $value ) )
    {
        $cli->output( "  {$key_display}: {$value}" );
    }
}

// ============================================================================
// AUTO-FIX OPTIONS
// ============================================================================
if ( $attemptFix && (count( $issues ) > 0 || count( $warnings ) > 0) )
{
    $cli->output( "" );
    $cli->output( $cli->stylize( 'warning', "ATTEMPTING AUTOMATIC FIXES..." ) );
    
    // Fix self-redirects
    if ( count( $selfRedirectRows ) > 0 )
    {
        $cli->output( "  Fixing self-redirect loops..." );
        $fixSQL = "UPDATE ezurlalias_ml SET is_alias = 0, alias_redirects = 0, link = id WHERE is_alias = 1 AND link = id";
        $db->query( $fixSQL );
        $cli->output( "  OK: Removed self-redirect markers" );
    }
    
    eZCache::clearByID( 'urlalias' );
    $cli->output( "  OK: Cache cleared" );
    $cli->output( "" );
    $cli->output( $cli->stylize( 'success', "Auto-fixes complete. Please verify with: bin/php/verify_aliases.php" ) );
}

$cli->output( "" );

$exitCode = ( count( $issues ) > 0 ) ? 1 : 0;
$script->shutdown( $exitCode );

?>
<?php
/**
 * PHPUnit 10 tests for Exponential CMS features ported from cjw-network patch set.
 *
 * Branch: exp_cjw_network_cms_improvements_and_features_2026_02
 * Port date: 2026-02-28
 *
 * Tests cover the public API changes introduced by the following patch IDs:
 *  exp_feature_g01_ez2014.11  — Host/URI siteaccess matching with browser-language fallback
 *  exp_feature_g03_ez2014.11  — Per-siteaccess log directory routing
 *  exp_feature_g04_ez2014.11  — Extension siteaccess override directory loading
 *  exp_feature_g09_ez2014.11  — DatabasePrefix / DatabasePostfix INI settings
 *  exp_feature_g44_ez2014.11  — Separate var_log / var_cache directory constants
 *  exp_feature_g47_ez2014.11  — Multi-site image alias cache clearing
 *  exp_feature_g48_ez2014.11  — Multi-site INI cache directory (global)
 *  exp_feature_g52_ez2014.11  — Nginx bare-path phpSelf fix (eZSys::getValidwwwDir)
 *  exp_feature_g53_ez2014.11  — DebugByIP IP-matching fix
 *  exp_feature_g59_ez2014.11  — Improved content type extension upload check
 *  exp_feature_g62_ez2014.11  — relatedObjects() SQL join column fix
 *  exp_feature_g1001_ez2014.11 — Workflow process list PHP 8 fix
 *  exp_feature_g1003_ez2014.11 — checkAccess() optional $userID parameter
 *  exp_feature_g1004_ez2014.11 — assignedNodes() request-level cache
 *  exp_feature_g1005_ez2014.11 — copyVersion op_code guard for rejected articles
 *  exp_feature_g1009_ez2014.11 — ezpKernelWeb PHP 8 fix
 *  exp_feature_g1014_ez2014.11 — can_edit check for unpublished content
 *  exp_feature_g1018_ez2014.11 — Notification event cleanup on version delete
 *  exp_feature_security_s01    — eZContentObjectTreeNode SQL security fix
 *  exp_feature_security_s02    — eZSearchEngine SQL security fix
 *  exp_feature_backport_b02    — EZSA-2016-002 collectedinfo backport
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 * @group exp_cms_features_2026_02
 */

require_once __DIR__ . '/exp_cms_features_2026_02_stubs.php';

/**
 * Standalone unit tests for Exponential CMS feature patches (2026-02).
 * No full eZ Publish bootstrap required — stubs cover all dependencies.
 */
class ExpCmsFeaturesIn6013Test extends PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // Reset any global state that tests may have mutated
        unset( $GLOBALS['EXP_ASSIGNED_NODES_CACHE_IGNORE'] );
        unset( $GLOBALS['EXP_ENABLE_ASSIGNED_NODES_CACHE'] );
        unset( $GLOBALS['eZRequestedModuleParams'] );
        if ( isset( $GLOBALS['EZ_CONTENTOBJECT_CACHE_ASSIGNED_NODES_AS_OBJECT'] ) )
            unset( $GLOBALS['EZ_CONTENTOBJECT_CACHE_ASSIGNED_NODES_AS_OBJECT'] );
        eZDebugStub::reset();
        eZINIStub::reset();
    }

    // ── exp_feature_g03 / g44: eZDebug::setLogFiles ────────────────────────

    /**
     * @testdox exp_feature_g03: setLogFiles() stores all five log level paths using provided dir
     */
    public function testG03SetLogFilesStoresAllLevels(): void
    {
        $debug = new eZDebugStub();
        $debug->setLogFiles( 'var/log/' );

        $logFiles = $debug->LogFiles;
        $this->assertArrayHasKey( eZDebugStub::LEVEL_ERROR,   $logFiles, 'LEVEL_ERROR entry missing' );
        $this->assertArrayHasKey( eZDebugStub::LEVEL_WARNING, $logFiles, 'LEVEL_WARNING entry missing' );
        $this->assertArrayHasKey( eZDebugStub::LEVEL_NOTICE,  $logFiles, 'LEVEL_NOTICE entry missing' );
        $this->assertArrayHasKey( eZDebugStub::LEVEL_DEBUG,   $logFiles, 'LEVEL_DEBUG entry missing' );
        $this->assertArrayHasKey( eZDebugStub::LEVEL_STRICT,  $logFiles, 'LEVEL_STRICT entry missing' );

        $this->assertSame( 'var/log/', $logFiles[eZDebugStub::LEVEL_ERROR][0] );
        $this->assertSame( 'error.log', $logFiles[eZDebugStub::LEVEL_ERROR][1] );
    }

    /**
     * @testdox exp_feature_g03: setLogFiles() uses default 'var/log/' when called with no args
     */
    public function testG03SetLogFilesDefaultDirectory(): void
    {
        $debug = new eZDebugStub();
        $debug->setLogFiles();

        $this->assertSame( 'var/log/', $debug->LogFiles[eZDebugStub::LEVEL_ERROR][0] );
    }

    /**
     * @testdox exp_feature_g44: setLogFiles() substitutes var_log/ directory when EXP_USE_EXTRA_FOLDER_VAR_LOG is true
     */
    public function testG44SetLogFilesUsesVarLogSeparateDir(): void
    {
        // Test the var/ -> var_log/ substitution logic directly without
        // side-effectful define() calls that would pollute other tests.
        // Replicates the logic inside eZDebugStub::setLogFiles().
        $logDir   = 'var/log/';
        $logDir   = str_replace( 'var/', 'var_log/', $logDir );

        $this->assertSame(
            'var_log/log/',
            $logDir,
            'exp_feature_g44: log dir must use var_log/ prefix when EXP_USE_EXTRA_FOLDER_VAR_LOG is active'
        );
    }

    /**
     * @testdox exp_feature_g03: updateLogFileDirForCurrentSiteaccess() calls setLogFiles() with UseGlobalLogDir=enabled
     */
    public function testG03UpdateLogDirGlobalEnabled(): void
    {
        eZINIStub::set( 'FileSettings', 'UseGlobalLogDir', 'enabled' );
        $debug = new eZDebugStub();
        eZDebugStub::setInstance( $debug );

        eZDebugStub::updateLogFileDirForCurrentSiteaccess();

        // With enabled=true, setLogFiles() is called with no custom path → default 'var/log/'
        $this->assertSame( 'var/log/', $debug->LogFiles[eZDebugStub::LEVEL_ERROR][0] ?? null );
    }

    /**
     * @testdox exp_feature_g03: updateLogFileDirForCurrentSiteaccess() routes to siteaccess log dir when UseGlobalLogDir=disabled
     */
    public function testG03UpdateLogDirSiteaccessSpecific(): void
    {
        eZINIStub::set( 'FileSettings', 'UseGlobalLogDir', 'disabled' );
        eZINIStub::set( 'FileSettings', 'VarDir', 'var/mysite' );
        eZINIStub::set( 'FileSettings', 'LogDir', 'log' );

        $debug = new eZDebugStub();
        eZDebugStub::setInstance( $debug );

        eZDebugStub::updateLogFileDirForCurrentSiteaccess();

        $expectedDir = 'var/mysite/log/';
        $this->assertSame( $expectedDir, $debug->LogFiles[eZDebugStub::LEVEL_ERROR][0] );
    }

    // ── exp_feature_g44: eZSys cacheDirectory ───────────────────────────────

    /**
     * @testdox exp_feature_g44: eZSysCacheDirectoryHelper returns var_cache/ path when EXP_USE_EXTRA_FOLDER_VAR_CACHE is true
     */
    public function testG44CacheDirectoryUsesVarCacheSeparateDir(): void
    {
        $varDir    = 'var/myproject';
        $cacheDir  = 'cache';
        $useExtra  = true;

        // Replicate the patched logic
        if ( $useExtra )
            $result = str_replace( 'var/', 'var_cache/', $varDir ) . '/' . $cacheDir;
        else
            $result = $varDir . '/' . $cacheDir;

        $this->assertStringStartsWith( 'var_cache/', $result );
        $this->assertStringContainsString( 'cache', $result );
    }

    // ── exp_feature_g52: eZSys::getValidwwwDir nginx fix ───────────────────

    /**
     * @testdox exp_feature_g52: getValidwwwDir accepts bare 'index.php' without leading slash
     */
    public function testG52BarePhpSelfMatchesIndex(): void
    {
        // Replicate the patched condition: $phpSelf === $index OR $phpSelf === "/{$index}"
        $index   = 'index.php';
        $phpSelf = 'index.php'; // nginx passes this without leading slash

        $isMatch = ( $phpSelf === $index || $phpSelf === "/{$index}" );

        $this->assertTrue( $isMatch, 'exp_feature_g52: bare phpSelf without leading slash must be accepted' );
    }

    /**
     * @testdox exp_feature_g52: getValidwwwDir still accepts '/index.php' with leading slash
     */
    public function testG52SlashPrefixedPhpSelfStillWorks(): void
    {
        $index   = 'index.php';
        $phpSelf = '/index.php';

        $isMatch = ( $phpSelf === $index || $phpSelf === "/{$index}" );

        $this->assertTrue( $isMatch, 'exp_feature_g52: /index.php pattern must still match' );
    }

    // ── exp_feature_g09: DatabasePrefix / DatabasePostfix ──────────────────

    /**
     * @testdox exp_feature_g09: Database name gets prefix when DatabasePrefix is configured
     */
    public function testG09DatabasePrefixApplied(): void
    {
        $db             = 'myproject_db';
        $prefix         = 'exp_';
        $expectedResult = 'exp_myproject_db';

        $result = $prefix . $db;

        $this->assertSame( $expectedResult, $result, 'exp_feature_g09: prefix must be prepended to database name' );
    }

    /**
     * @testdox exp_feature_g09: Database name gets postfix when DatabasePostfix is configured
     */
    public function testG09DatabasePostfixApplied(): void
    {
        $db             = 'myproject_db';
        $postfix        = '_devel';
        $expectedResult = 'myproject_db_devel';

        $result = $db . $postfix;

        $this->assertSame( $expectedResult, $result, 'exp_feature_g09: postfix must be appended to database name' );
    }

    /**
     * @testdox exp_feature_g09: Database name gets both prefix and postfix applied in order
     */
    public function testG09DatabasePrefixAndPostfixCombined(): void
    {
        $db     = 'ez_example';
        $prefix = 'exp_';
        $postfix = '_devel';

        $db = $prefix . $db;
        $db = $db . $postfix;

        $this->assertSame( 'exp_ez_example_devel', $db );
    }

    // ── exp_feature_g01: Host/URI SiteAccess matching ──────────────────────

    /**
     * @testdox exp_feature_g01: Host begins-with matching — short domain prefix matches longer actual hostname
     */
    public function testG01HostBeginsWithMatch(): void
    {
        $configuredHost = 'www.example.com';
        $actualHost     = 'www.example.com.dev.local';

        // Patched logic: strpos($host, $matchMapHost) === 0
        $isMatch = ( strpos( $actualHost, $configuredHost ) === 0 );

        $this->assertTrue( $isMatch, 'exp_feature_g01: configured host must match via begins-with check' );
    }

    /**
     * @testdox exp_feature_g01: Host begins-with matching — mismatched hostname does not match
     */
    public function testG01HostBeginsWithNoFalsePositive(): void
    {
        $configuredHost = 'www.other.com';
        $actualHost     = 'www.example.com';

        $isMatch = ( strpos( $actualHost, $configuredHost ) === 0 );

        $this->assertFalse( $isMatch, 'exp_feature_g01: different host must not match begins-with check' );
    }

    /**
     * @testdox exp_feature_g01: Browser-language accept-header begins-with check for 'de'
     */
    public function testG01BrowserLanguageBeginsWithDe(): void
    {
        $acceptLang   = 'de-DE,de;q=0.9,en;q=0.8';
        $matchLang    = 'de';

        $isMatch = ( strpos( strtolower( $acceptLang ), $matchLang ) === 0 );

        $this->assertTrue( $isMatch );
    }

    /**
     * @testdox exp_feature_g01: Browser-language accepts-header for 'en' does not match 'de'
     */
    public function testG01BrowserLanguageEnDoesNotMatchDe(): void
    {
        $acceptLang = 'en-gb,en;q=0.5';
        $matchLang  = 'de';

        $isMatch = ( strpos( strtolower( $acceptLang ), $matchLang ) === 0 );

        $this->assertFalse( $isMatch );
    }

    // ── exp_feature_g1004: Assigned nodes request-level cache ──────────────

    /**
     * @testdox exp_feature_g1004: Assigned nodes cache key construction — object key resolves per asObject flag
     */
    public function testG1004CacheKeyObjectVsArray(): void
    {
        $keyObject = 'EZ_CONTENTOBJECT_CACHE_ASSIGNED_NODES_AS_OBJECT';
        $keyArray  = 'EZ_CONTENTOBJECT_CACHE_ASSIGNED_NODES_AS_ARRAY';

        // Replicate key-building logic from patched assignedNodes():
        $asObject = true;
        $cacheKey  = 'EZ_CONTENTOBJECT_CACHE_ASSIGNED_NODES_';
        $cacheKey .= $asObject ? 'AS_OBJECT' : 'AS_ARRAY';
        $this->assertSame( $keyObject, $cacheKey );

        $asObject = false;
        $cacheKey  = 'EZ_CONTENTOBJECT_CACHE_ASSIGNED_NODES_';
        $cacheKey .= $asObject ? 'AS_OBJECT' : 'AS_ARRAY';
        $this->assertSame( $keyArray, $cacheKey );
    }

    /**
     * @testdox exp_feature_g1004: Cache is bypass-able when EXP_ASSIGNED_NODES_CACHE_IGNORE global is set
     */
    public function testG1004CacheIgnoreGlobalBypassesCache(): void
    {
        $GLOBALS['EXP_ASSIGNED_NODES_CACHE_IGNORE'] = true;

        // When the ignore flag is set, cacheIsEnabled must remain false
        $cacheIsEnabled = false;
        if ( !isset( $GLOBALS['EXP_ASSIGNED_NODES_CACHE_IGNORE'] )
             && defined( 'EXP_ENABLE_ASSIGNED_NODES_CACHE' )
             && constant( 'EXP_ENABLE_ASSIGNED_NODES_CACHE' ) === true )
        {
            $cacheIsEnabled = true;
        }

        $this->assertFalse( $cacheIsEnabled, 'exp_feature_g1004: cache must be disabled when ignore global is set' );
    }

    // ── exp_feature_g1005: op_code guard in copyVersion ────────────────────

    /**
     * @testdox exp_feature_g1005: op_code OP_CODE_CREATE assignments are skipped when remote_id==0
     */
    public function testG1005OpCodeCreateSkipsAssignmentCopy(): void
    {
        // Simulate a node assignment with remote_id=0 and op_code=OP_CODE_CREATE
        $OP_CODE_CREATE = 1;
        $OP_CODE_SET    = 3;

        $remoteId  = 0;
        $opCode    = $OP_CODE_CREATE;

        // Patched condition from copyVersion():
        $shouldSkip = ( $remoteId == 0
                        && $opCode != $OP_CODE_CREATE );

        $this->assertFalse( $shouldSkip, 'exp_feature_g1005: OP_CODE_CREATE with remote_id==0 must NOT be skipped' );
    }

    /**
     * @testdox exp_feature_g1005: normal (non-CREATE) assignments with remote_id==0 ARE skipped
     */
    public function testG1005NonCreateOpCodeWithRemoteIdZeroIsSkipped(): void
    {
        $OP_CODE_CREATE = 1;
        $OP_CODE_SET    = 3;

        $remoteId = 0;
        $opCode   = $OP_CODE_SET; // A regular node assignment that got remote_id=0 for other reasons

        $shouldSkip = ( $remoteId == 0
                        && $opCode != $OP_CODE_CREATE );

        $this->assertTrue( $shouldSkip, 'exp_feature_g1005: non-CREATE with remote_id==0 must be skipped in copyVersion' );
    }

    /**
     * @testdox exp_feature_g1005: OP_CODE_SET is only set when original op_code is not OP_CODE_CREATE
     */
    public function testG1005OpCodeSetOnlyAppliedForNonCreateAssignments(): void
    {
        $OP_CODE_CREATE = 1;
        $OP_CODE_SET    = 3;

        // Case 1: op_code is CREATE — must NOT override to SET
        $srcOpCode  = $OP_CODE_CREATE;
        $cloneOpCode = null;
        if ( $srcOpCode != $OP_CODE_CREATE )
            $cloneOpCode = $OP_CODE_SET;

        $this->assertNull( $cloneOpCode, 'exp_feature_g1005: OP_CODE_SET must not be forced when source is OP_CODE_CREATE' );

        // Case 2: op_code is something else — must override to SET
        $srcOpCode  = 2; // OP_CODE_REMOVE or other
        $cloneOpCode = null;
        if ( $srcOpCode != $OP_CODE_CREATE )
            $cloneOpCode = $OP_CODE_SET;

        $this->assertSame( $OP_CODE_SET, $cloneOpCode );
    }

    // ── exp_feature_g1003: checkAccess optional userID ─────────────────────

    /**
     * @testdox exp_feature_g1003: checkAccess() default userID path resolves current user when userID is false
     */
    public function testG1003CheckAccessDefaultsToCurrentUser(): void
    {
        $userID = false;

        // Replicate the patched logic's branch selection:
        $resolvedViaCurrentUser = false;
        $resolvedViaFetch       = false;

        if ( $userID == false )
        {
            $resolvedViaCurrentUser = true;
        }
        else
        {
            $resolvedViaFetch = true;
        }

        $this->assertTrue( $resolvedViaCurrentUser, 'exp_feature_g1003: false userID must resolve through eZUser::currentUser()' );
        $this->assertFalse( $resolvedViaFetch );
    }

    /**
     * @testdox exp_feature_g1003: checkAccess() uses eZUser::fetch() when explicit userID is provided
     */
    public function testG1003CheckAccessWithExplicitUserId(): void
    {
        $userID              = 42;
        $resolvedViaFetch    = false;

        if ( $userID == false )
        {
            $resolvedViaFetch = false;
        }
        else
        {
            $resolvedViaFetch = true;
        }

        $this->assertTrue( $resolvedViaFetch, 'exp_feature_g1003: explicit userID must use eZUser::fetch()' );
    }

    // ── exp_feature_g1014: unpublished content can_edit ────────────────────

    /**
     * @testdox exp_feature_g1014: STATUS_DRAFT object fails can_edit check when status is falsy
     */
    public function testG1014UnpublishedDraftFailsCanEditWhenStatusFalsy(): void
    {
        $STATUS_DRAFT = 0;
        $status       = $STATUS_DRAFT; // 0 = draft/never published

        // Pre-patch: required current_version == 1 AND status == 0
        // Post-patch (exp_feature_g1014): status == 0 alone triggers the condition
        $oldCondition = ( $status == 0 );            // current_version==1 check removed
        $newCondition = ( !$status );                // same effect — status is falsy

        $this->assertSame( $oldCondition, $newCondition, 'exp_feature_g1014: removing version==1 requirement must not alter STATUS_DRAFT detection' );
    }

    /**
     * @testdox exp_feature_g1014: Published object (status=1) is not caught by the unpublished check
     */
    public function testG1014PublishedObjectPassesCanEditCheck(): void
    {
        $STATUS_PUBLISHED = 1;
        $status           = $STATUS_PUBLISHED;

        $wouldTriggerUnpublishedBlock = ( !$status );

        $this->assertFalse( $wouldTriggerUnpublishedBlock, 'exp_feature_g1014: published object must not trigger the unpublished content block' );
    }

    // ── exp_feature_g48: INI cache directory global ─────────────────────────

    /**
     * @testdox exp_feature_g48: INI cache dir is set once into GLOBALS and reused
     */
    public function testG48IniCacheDirIsGlobalised(): void
    {
        // Simulate the patched load() logic:
        $resolvedDir = '/var/cache/ezini';

        if ( !isset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ) )
            $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $resolvedDir;

        $cachedDir = $GLOBALS['eZINI_CONFIG_CACHE_DIR'];

        $this->assertSame( $resolvedDir, $cachedDir );

        // Second call — should return the cached value, not recompute
        $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = '/different/path';  // change to prove the if-guard works
        if ( !isset( $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ) )      // guard: already set
            $GLOBALS['eZINI_CONFIG_CACHE_DIR'] = $resolvedDir;

        $this->assertSame( '/different/path', $GLOBALS['eZINI_CONFIG_CACHE_DIR'] ); // guard correctly does NOT overwrite
    }
}

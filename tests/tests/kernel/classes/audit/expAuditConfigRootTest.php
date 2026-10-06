<?php
/**
 * expAuditConfig::root() read out of the engine archive (EXP_ENGINE_PHAR): it named the path inside
 * the archive, so the audit directory became phar://.../engine.phar/var/site/log/audit, every
 * record failed with "The audit directory ... cannot be created" and was spilled to the error log
 * instead (seen on the FrankenPHP server, which runs from the archive). It uses EXP_ROOT_DIR now.
 *
 * The class is loaded from a small archive built for the test, in a separate PHP process.
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 * @group audit
 */

require_once __DIR__ . '/../../../lib/ezutils/EngineArchiveInstallationRootTest.php';

class expAuditConfigRootTest extends PHPUnit\Framework\TestCase
{
    public function testRootIsTheInstallationWhenLoadedFromTheEngineArchive()
    {
        if ( !extension_loaded( 'phar' ) || !function_exists( 'exec' ) )
            $this->markTestSkipped( 'Needs the phar extension and exec()' );

        $r = EngineArchiveInstallationRootTest::rootsFromArchive( array( 'kernel/classes/audit/expauditconfig.php' ) );

        $this->assertStringStartsWith( 'phar://', $r['audit.source'], 'the class really came from the archive' );
        $this->assertSame( $r['_root'] . '/', $r['audit.root'] );
    }

    public function testAnOverriddenRootStillWins()
    {
        expAuditConfig::setOverride( array( 'root' => '/some/where/else/' ) );
        try
        {
            $this->assertSame( '/some/where/else/', expAuditConfig::root() );
        }
        finally
        {
            expAuditConfig::setOverride( null );
        }
    }
}

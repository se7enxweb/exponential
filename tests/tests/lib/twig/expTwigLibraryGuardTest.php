<?php
/**
 * The Twig template engine runs only on Twig 3.15 and later (Twig\Node\Nodes): with no Twig, or with Twig 2 or an early
 * Twig 3 (the Symfony 3.4 platforms install se7enxweb/twig 2.16), expTwigRenderer::libraryProblem() names the reason
 * and loadLibrary() switches the engine off instead of loading node classes that would end the request with a fatal
 * error.
 *
 * No database, no siteaccess and no Twig needed.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 * @group twig
 */
class expTwigLibraryGuardTest extends PHPUnit\Framework\TestCase
{
    public function testNoTwigIsReportedAsMissing()
    {
        $problem = expTwigRenderer::libraryProblem( false, false, null );
        $this->assertNotNull( $problem );
        $this->assertStringContainsString( 'Twig 3 not found', $problem );
    }

    public function testTwig2SwitchesTheEngineOffAndSaysWhy()
    {
        $problem = expTwigRenderer::libraryProblem( true, false, '2.16.5' );
        $this->assertNotNull( $problem );
        $this->assertStringContainsString( 'Twig 2.16.5 found', $problem );
        $this->assertStringContainsString( 'needs Twig ' . expTwigRenderer::MINIMUM_TWIG . ' or later', $problem );
        $this->assertStringContainsString( 'switched off', $problem );
        $this->assertStringContainsString( '.tpl and .stpl templates are not affected', $problem );
    }

    public function testEarlyTwig3WithoutNodesIsSwitchedOff()
    {
        $this->assertNotNull( expTwigRenderer::libraryProblem( true, false, '3.11.4' ) );
    }

    public function testUnknownVersionIsStillExplained()
    {
        $this->assertStringContainsString( '(unknown version)', expTwigRenderer::libraryProblem( true, false, null ) );
    }

    public function testTwig315AndLaterRunTheEngine()
    {
        $this->assertNull( expTwigRenderer::libraryProblem( true, true, '3.15.0' ) );
        $this->assertNull( expTwigRenderer::libraryProblem( true, true, '3.27.0' ) );
    }

    public function testTheMinimumIsTheReleaseThatAddedNodes()
    {
        $this->assertSame( '3.15', expTwigRenderer::MINIMUM_TWIG );
    }

    public function testTheInstalledTwigMatchesLoadLibrary()
    {
        $found = class_exists( '\Twig\Environment' );
        $expected = expTwigRenderer::libraryProblem( $found, $found && class_exists( '\Twig\Node\Nodes' ), $found ? \Twig\Environment::VERSION : null ) === null;
        $this->assertSame( $expected, expTwigRenderer::loadLibrary() );
        if ( !$expected )
            $this->assertNotNull( expTwigRenderer::libraryProblemMessage() );
    }
}

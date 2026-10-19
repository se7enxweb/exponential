<?php
/**
 * A Setup > RAD wizard page shows its problems once the form has been sent, not on a first visit: the datatype,
 * workflow event, template and design extension wizards (and the others) opened on a list of errors about fields
 * nobody had filled in yet.
 *
 * No database: the views are read as files, and the settings of an empty form are checked directly.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expRadWizardFirstVisitProblemsTest extends PHPUnit\Framework\TestCase
{
    const VIEWS = array( 'datatype' => 'expDatatypeWizard', 'workflowevent' => 'expWorkflowEventWizard',
                         'templateoperator' => 'expTemplateExtensionWizard', 'designextension' => 'expDesignExtensionWizard',
                         'contentextension' => 'expContentExtensionWizard', 'handlerextension' => 'expHandlerWizard',
                         'moduleextension' => 'expModuleExtensionWizard', 'modulewizard' => 'expModuleWizard',
                         'settingsextension' => 'expSettingsExtensionWizard', 'kerneloverride' => 'expKernelOverrideWizard' );

    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        expRadWizardTestHelper::useEmptyDatabase();
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreDatabase();
    }

    public function testProblemsAreShownOnlyOnceTheFormWasSent()
    {
        $problems = array( 'The extension needs a name.' );
        $this->assertSame( array(), expExtensionWizard::shownProblems( $problems, false ) );
        $this->assertSame( $problems, expExtensionWizard::shownProblems( $problems, true ) );
    }

    public static function viewProvider()
    {
        $cases = array();
        foreach ( self::VIEWS as $view => $class )
            $cases[$view] = array( $view, $class );
        return $cases;
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('viewProvider')]
    public function testTheViewHidesTheProblemsOfAFirstVisit( $view, $class )
    {
        $source = file_get_contents( expRadWizardTestHelper::root() . '/kernel/private/classes/views/setup/' . $view . '.php' );

        $this->assertStringContainsString( "\\expExtensionWizard::shownProblems( \$problems, \$http->hasPostVariable( 'Submitted' ) )", $source );
        $this->assertStringNotContainsString( "setVariable( 'wizard_problems', \$problems )", $source );

        // an empty form has problems (no name), which the first visit does not show
        $this->assertNotEmpty( $class::problems( $class::settings( array() ) ), $class );
    }

    /**
     * Every wizard form posts the Submitted field the views test for.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('viewProvider')]
    public function testTheFormPostsSubmitted( $view, $class )
    {
        foreach ( array( 'admin', 'admin4' ) as $design )
        {
            $path = expRadWizardTestHelper::root() . '/design/' . $design . '/templates/setup/' . $view . '.tpl';
            $this->assertFileExists( $path );
            $this->assertStringContainsString( 'name="Submitted"', file_get_contents( $path ), "$design/$view" );
        }
    }
}

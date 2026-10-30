<?php
/**
 * The shared bridge of the template engines (lib/templatebridge): operator input modes and the operators that ignore
 * their input, the parameter-only operators and their settings, value wrapping with the per-page pairs, write-back,
 * the render context, resolving a design: URI, the loop helpers and the suffix registry. No engine library needed,
 * no database and no siteaccess.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group lib
 */

class expTemplateBridgeTestObject
{
    public $values;

    public function __construct( array $values )
    {
        $this->values = $values;
    }

    public function hasAttribute( $name )
    {
        return array_key_exists( $name, $this->values );
    }

    public function attribute( $name )
    {
        return $this->values[$name];
    }
}

if ( !class_exists( 'Q_WebServer_ExitSignal', false ) )
{
    /** Stands in for Velocity's exit signal (vendor/se7enxweb/exponential-velocity) in a test run without it. */
    class Q_WebServer_ExitSignal extends Exception
    {
    }
}

class expTemplateBridgeTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 4 ) );
    }

    protected function tearDown(): void
    {
        expTemplateBridge::resetSettings();
        expTemplateValues::reset();
    }

    public function testInputModes()
    {
        $this->assertSame( 'param', expTemplateBridge::operatorInputMode( 'eq' ), 'input-as-parameter true' );
        $this->assertSame( 'param', expTemplateBridge::operatorInputMode( 'cond' ), 'a control operator' );
        $this->assertSame( 'param', expTemplateBridge::operatorInputMode( 'ezhttp' ), 'a parameter-only operator' );
        $this->assertSame( 'input', expTemplateBridge::operatorInputMode( 'wash' ) );
        $this->assertSame( 'input', expTemplateBridge::operatorInputMode( 'nl2br' ), 'INPUT_AT_RUN_TIME' );
        $this->assertTrue( expTemplateBridge::ignoresInput( 'first_set' ) );
        $this->assertTrue( expTemplateBridge::ignoresInput( 'ezhttp' ) );
        $this->assertFalse( expTemplateBridge::ignoresInput( 'eq' ) );
        $this->assertContains( 'exp_twig_render', expTemplateBridge::PARAMETER_OPERATORS );
        $this->assertSame( expTemplateBridge::PARAMETER_OPERATORS, expSmartyRenderer::PARAMETER_OPERATORS, 'Smarty reads the shared list' );
    }

    public function testTheSharedParameterOperatorList()
    {
        $ini = eZINI::instance();
        $ini->setVariable( 'TemplateBridgeSettings', 'ParameterOperatorList', array( 'my_parameter_operator' ) );
        expTemplateBridge::resetSettings();
        try
        {
            $this->assertTrue( expTemplateBridge::isParameterOperator( 'my_parameter_operator' ) );
            $this->assertSame( 'param', expTemplateBridge::operatorInputMode( 'my_parameter_operator' ) );
        }
        finally
        {
            $ini->setVariable( 'TemplateBridgeSettings', 'ParameterOperatorList', array() );
        }
    }

    public function testCallingOperatorsAsATemplateWritesThem()
    {
        $this->assertSame( 3, expTemplateBridge::callOperator( 'sum', 1, array( 2 ) ), '1|sum(2): the input is the first parameter' );
        $this->assertSame( 'p', expTemplateBridge::callOperatorWithInput( 'first_set', 'in', array( 'p' ) ), "'in'|first_set('p') is 'p', as in a .tpl" );
        $this->assertSame( 'ABC', expTemplateBridge::callOperatorWithInput( 'upcase', 'abc', array() ) );
        $this->assertSame( 'ab', expTemplateBridge::callOperatorDirect( 'concat', false, null, array( 'a', 'b' ) ) );
        $this->assertSame( array( 'a', 'c' ), expTemplateBridge::callOperator( 'append', array( 'a' ), array( 'c' ) ) );
    }

    /**
     * Two operator classes register strip_tags, and the one loaded last holds the name: eZTemplatePHPOperator takes
     * the input as a parameter, eZTemplateStringsOperator (loaded with ristring) as its input. The mode must follow
     * the class that holds the name when the operator runs: a cached mode of the other class made strip_tags return
     * '' after a ristring (an empty label on the contact form).
     */
    public function testInputModeFollowsTheClassThatHoldsTheName()
    {
        $tpl = eZTemplate::factory();
        $saved = isset( $tpl->Operators['strip_tags'] ) ? $tpl->Operators['strip_tags'] : null;
        $text = '<section><paragraph>I consent &amp; agree.</paragraph></section>';
        try
        {
            $php = new eZTemplatePHPOperator( array( 'strip_tags' => 'strip_tags' ) );
            foreach ( array( $php, new eZTemplateStringsOperator(), $php ) as $holder )
            {
                $tpl->Operators['strip_tags'] = $holder;
                $this->assertSame( 'I consent &amp; agree.', (string)expTemplateBridge::callOperatorWithInput( 'strip_tags', $text, array() ), get_class( $holder ) );
            }
        }
        finally
        {
            $tpl->Operators['strip_tags'] = $saved;
        }
    }

    public function testWrapPairsAndWriteBack()
    {
        $object = new expTemplateBridgeTestObject( array( 'name' => 'n', 'child' => new expTemplateBridgeTestObject( array( 'name' => 'c' ) ) ) );
        $wrapped = expTemplateValues::wrap( array( 'o' => $object ), 'expTwigObjectProxy' );
        $this->assertInstanceOf( 'expTwigObjectProxy', $wrapped['o'] );
        $this->assertSame( 'c', $wrapped['o']['child']['name'] );
        $this->assertSame( $object, expTemplateValues::unwrap( $wrapped['o'] ) );
        // the same value again is the same wrapped value (the pairs of the page)
        $this->assertSame( expTemplateValues::wrapNamed( 'o', $object, 'expTwigObjectProxy' ), expTemplateValues::wrapNamed( 'o', $object, 'expTwigObjectProxy' ) );
        $smarty = expTemplateValues::wrap( $object, 'expSmartyObjectProxy' );
        $this->assertFalse( isset( $smarty['missing'] ) );
        $this->assertTrue( isset( ( expTemplateValues::wrap( $object, 'expTwigObjectProxy' ) )['missing'] ), 'Twig: every key exists, a missing one is null' );

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'kept', 'same' );
        $tpl->setVariable( 'removed', 'x' );
        expTemplateValues::writeBack( array( 'kept' => 'same', 'removed' => null, 'added' => 'a', '__helper' => 1 ), $tpl, '', array( 'kept' => 'same', 'removed' => 'x' ) );
        $this->assertSame( 'a', $tpl->variable( 'added' ) );
        $this->assertFalse( $tpl->hasVariable( 'removed' ) );
        $this->assertFalse( $tpl->hasVariable( '__helper' ) );
        $tpl->unsetVariable( 'added' );
        $tpl->unsetVariable( 'kept' );
    }

    public function testContextAndHelpers()
    {
        $this->assertNull( expTemplateBridge::context() );
        $tpl = eZTemplate::factory();
        expTemplateBridge::enter( $tpl, 'Root', 'Root:Sub' );
        $this->assertSame( 'Root:Sub:ns', expTemplateBridge::scopedNamespace( 'relative', 'ns' ) );
        $this->assertSame( 'Root', expTemplateBridge::scopedNamespace( 'root', '' ) );
        $this->assertSame( 'x', expTemplateBridge::scopedNamespace( 'global', 'x' ) );
        $this->assertTrue( expTemplateBridge::leave() );
        $this->assertSame( array( 1 => 'b', 2 => 'c' ), expTemplateBridge::loopSlice( array( 'a', 'b', 'c' ), 1 ) );
        $this->assertSame( array( 2 => 'c', 1 => 'b' ), expTemplateBridge::loopSlice( array( 'a', 'b', 'c' ), 0, 2, true ) );
        $this->assertSame( array( 0 => 1, 1 => 2, 2 => 3 ), expTemplateBridge::sectionLoop( 3 ) );
        $this->assertSame( 'b', expTemplateBridge::sequence( array( 'a', 'b' ), 3 ) );
        $this->assertSame( array( 3, 2, 1 ), expTemplateBridge::range( 3, 1 ) );
    }

    public function testResolveURI()
    {
        $this->assertNull( expTemplateBridge::resolveURI( eZTemplate::factory(), 'design:no/such/template.tpl' ) );
        $this->assertSame( 'design/standard/templates/smarty/demo_full.stpl',
                           expTemplateBridge::resolveURI( eZTemplate::factory(), 'file:design/standard/templates/smarty/demo_full.stpl' ) );
    }

    public function testExitSignalIsRethrown()
    {
        $signal = new Q_WebServer_ExitSignal( 'exit' );
        expTemplateBridge::rethrowExit( new RuntimeException( 'other' ) );
        try
        {
            expTemplateBridge::rethrowExit( new RuntimeException( 'wrapped', 0, $signal ) );
            $this->fail( 'the exit signal goes through' );
        }
        catch ( Q_WebServer_ExitSignal $e )
        {
            $this->assertSame( $signal, $e );
        }
    }
}

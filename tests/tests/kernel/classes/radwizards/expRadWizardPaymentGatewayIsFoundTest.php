<?php
/**
 * A payment gateway made by the RAD handler wizard is found by the kernel.
 *
 * The wizard used to write the class to paymentgateways/<alias>gateway.php, while
 * eZPaymentGatewayType::loadAndRegisterBuiltInGateways() looks for
 * <GatewaysDirectories>/<AvailableGateways>/classes/<AvailableGateways>gateway.php - so a generated
 * gateway was never loaded, never registered, and never offered in the Payment Gateway workflow event.
 * It also registered the class under '<alias>gateway', which is not the class name whenever the
 * extension name has an underscore in it, so createGateway() would have failed even once found.
 *
 * Both gateway kinds are generated into a scratch directory, registered with exactly the settings
 * the wizard writes (the extension path pointed at the scratch copy), and the loader is asked for them.
 *
 * No database.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once __DIR__ . '/expRadWizardTestHelper.php';

class expRadWizardPaymentGatewayIsFoundTest extends PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        expRadWizardTestHelper::boot();
    }

    private $scratch;
    private $injected;
    private $gateways;

    protected function setUp(): void
    {
        expRadWizardTestHelper::boot();
        $this->scratch = expRadWizardTestHelper::scratch( 'gateway' );
        $this->injected = expRadWizardTestINI::injected();
        $this->gateways = $GLOBALS['eZPaymentGateways'] ?? null;
    }

    protected function tearDown(): void
    {
        expRadWizardTestHelper::restoreInjected( $this->injected );
        eZINI::resetInstance( 'paymentgateways.ini' );
        expRadWizardTestHelper::removeTree( $this->scratch );
        $GLOBALS['eZPaymentGateways'] = $this->gateways;
    }

    public static function kinds()
    {
        return array( 'redirect' => array( 'paymentgateway' ), 'transparent' => array( 'paymentgatewaydirect' ) );
    }

    /**
     * @dataProvider kinds
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'kinds' )]
    public function testTheLoaderFindsAndRegistersTheGeneratedGateway( $kind )
    {
        // An underscore in the name: the alias keeps it, the default class name drops it.
        $suffix = substr( md5( $kind . getmypid() . mt_rand() ), 0, 8 );
        $name = 'k1e_pay_' . $suffix;
        $settings = expHandlerWizard::settings( array( 'kind' => $kind, 'name' => $name ) );
        $this->assertSame( array(), expHandlerWizard::problems( $settings ) );
        $alias = $settings['alias'];
        $class = $settings['class'];
        $this->assertNotSame( $alias . 'gateway', strtolower( $class ), 'the name is chosen so alias and class differ' );

        $files = expHandlerWizard::files( $settings );
        $classFile = 'paymentgateways/' . $alias . '/classes/' . $alias . 'gateway.php';
        $this->assertArrayHasKey( $classFile, $files, 'the class is written where the loader looks' );
        $this->assertArrayHasKey( 'settings/paymentgateways.ini.append.php', $files );

        // The extension, written out under the scratch directory
        $root = $this->scratch . '/' . $name;
        foreach ( $files as $path => $content )
        {
            if ( !is_dir( dirname( "$root/$path" ) ) )
                mkdir( dirname( "$root/$path" ), 0777, true );
            file_put_contents( "$root/$path", $content );
        }

        // The settings exactly as generated, with extension/<name> standing for the scratch copy
        $ini = $files['settings/paymentgateways.ini.append.php'];
        $this->assertSame( 1, preg_match_all( '/^AvailableGateways\[\]=(.*)$/m', $ini, $available ) );
        $this->assertSame( 1, preg_match_all( '/^GatewaysDirectories\[\]=(.*)$/m', $ini, $directories ) );
        $this->assertSame( $alias, $available[1][0] );
        $this->assertSame( 'extension/' . $name . '/paymentgateways', $directories[1][0] );
        $directory = str_replace( 'extension/' . $name, $root, $directories[1][0] );

        expRadWizardTestHelper::injectIni( 'paymentgateways.ini', array( 'GatewaysSettings' => array(
            'AvailableGateways' => array( $available[1][0] ),
            'GatewaysDirectories' => array( $directory ) ) ) );
        eZINI::resetInstance( 'paymentgateways.ini' );

        $this->assertFalse( class_exists( $class, false ) );
        $type = new eZPaymentGatewayType();   // loads and registers every gateway the settings name
        $log = $this->scratch . '/payment.log';
        $type->logger = eZPaymentLogger::CreateForAdd( $log );

        $this->assertTrue( class_exists( $class, false ), 'the loader included the generated file' );
        $this->assertArrayHasKey( $alias, $GLOBALS['eZPaymentGateways'], 'the gateway registered itself under its type' );
        $this->assertSame( $class, $GLOBALS['eZPaymentGateways'][$alias]['class_name'] );
        $this->assertContains( $alias, array_column( $type->attribute( 'available_gateways' ), 'value' ) );
        $this->assertInstanceOf( $class, $type->createGateway( $alias ), 'the workflow event can build it' );
    }
}

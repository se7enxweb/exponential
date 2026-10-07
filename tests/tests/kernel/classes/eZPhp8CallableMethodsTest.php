<?php
/**
 * Methods the kernel calls as Class::method() are static, signatures carry no
 * optional parameter before a required one, and classes do not create
 * properties they never declared.
 *
 * Since PHP 8.0 calling a non-static method statically is an Error, so each of
 * the calls below stopped the request (removing a trigger, removing a discount
 * rule, purging all orders, collecting baskets, ...). An optional parameter
 * before a required one is deprecated since PHP 8.0, a dynamic property since
 * PHP 8.2, and a tentative return type without #[\ReturnTypeWillChange] since
 * PHP 8.1.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

$root = __DIR__ . '/../../../..';
require_once $root . '/lib/ezi18n/classes/ezcodepage.php';
require_once $root . '/lib/eztemplate/classes/eztemplateexpinfooperator.php';
require_once $root . '/lib/ezpdf/classes/class.pdf.php';
require_once $root . '/kernel/private/classes/clusterfilehandlers/dfsbackends/dfs_filter_iterator.php';

class eZPhp8CallableMethodsTest extends PHPUnit\Framework\TestCase
{
    private static function root()
    {
        return realpath( __DIR__ . '/../../../..' );
    }

    public static function staticMethods()
    {
        return array(
            array( 'kernel/classes/ezcontentupload.php', 'eZContentUpload', 'nodeAliasID' ),
            array( 'kernel/classes/ezorderitem.php', 'eZOrderItem', 'cleanup' ),
            array( 'kernel/classes/ezworkflowprocess.php', 'eZWorkflowProcess', 'cleanup' ),
            array( 'kernel/classes/ezcontentclass.php', 'eZContentClass', 'canInstantiateClasses' ),
            array( 'kernel/classes/ezdbgarbagecollector.php', 'eZDBGarbageCollector', 'collectBaskets' ),
            array( 'kernel/classes/ezdbgarbagecollector.php', 'eZDBGarbageCollector', 'collectProductCollections' ),
            array( 'kernel/classes/ezdbgarbagecollector.php', 'eZDBGarbageCollector', 'collectProductCollectionItems' ),
            array( 'kernel/classes/ezdbgarbagecollector.php', 'eZDBGarbageCollector', 'collectProductCollectionItemOptions' ),
            array( 'kernel/classes/ezsiteinstaller.php', 'eZSiteInstaller', 'createSiteAccess' ),
            array( 'kernel/classes/ezsiteinstaller.php', 'eZSiteInstaller', 'languageNameFromLocale' ),
            array( 'lib/ezi18n/classes/ezcodepage.php', 'eZCodePage', 'fileModification' ),
        );
    }

    /**
     * Read from the source, so the test does not need the classes' parents loaded.
     *
     * @dataProvider staticMethods
     */
    #[PHPUnit\Framework\Attributes\DataProvider( 'staticMethods' )]
    public function testMethodCalledStaticallyIsStatic( $file, $class, $method )
    {
        $source = file_get_contents( self::root() . '/' . $file );
        $this->assertMatchesRegularExpression( '/\bstatic\s+(public\s+)?function\s+' . $method . '\s*\(/i', $source,
            "$class::$method() is called statically and must be declared static" );
    }

    public function testCodePageFileModificationCallsStatically()
    {
        $this->assertTrue( ( new ReflectionMethod( 'eZCodePage', 'fileModification' ) )->isStatic() );
        $this->assertFalse( eZCodePage::fileModification( 'no-such-charset-' . uniqid() ) );
    }

    public function testSiteInstallerLanguageName()
    {
        require_once self::root() . '/kernel/classes/ezsiteinstaller.php';
        $this->assertSame( 'eng', eZSiteInstaller::languageNameFromLocale( 'eng-GB' ) );
        $this->assertSame( 'eng_us', eZSiteInstaller::languageNameFromLocale( 'eng-US', array( 'eng-GB', 'eng-US' ) ) );
    }

    public function testNoOptionalParameterBeforeRequired()
    {
        $method = new ReflectionMethod( 'Cpdf', 'addJpegImage_common' );
        $this->assertSame( 7, $method->getNumberOfRequiredParameters() );
        foreach ( array( 'kernel/classes/ezcontentupload.php' => 'static function upload( $parameters, $module )',
                         'kernel/private/classes/clusterfilehandlers/dfsbackends/mysqli.php' =>
                             'function _selectOne( $query, $fname, $error, $debug, $fetchCall )' ) as $file => $signature )
            $this->assertStringContainsString( $signature, file_get_contents( self::root() . '/' . $file ) );
    }

    public function testExpInfoOperatorDeclaresItsProperties()
    {
        $class = new ReflectionClass( 'eZTemplateExpInfoOperator' );
        $this->assertTrue( $class->hasProperty( 'ExpInfoName' ) );
        $this->assertTrue( $class->hasProperty( 'Operators' ) );
        $operator = new eZTemplateExpInfoOperator( 'myinfo' );
        $this->assertSame( array( 'myinfo' ), $operator->operatorList() );
    }

    public function testDfsFilterIteratorKeepsItsReturnTypesQuiet()
    {
        foreach ( array( 'accept', 'current' ) as $name )
            $this->assertNotEmpty( ( new ReflectionMethod( 'eZDFSFileHandlerDFSBackendFilterIterator', $name ) )
                ->getAttributes( 'ReturnTypeWillChange' ), "$name() carries #[\\ReturnTypeWillChange]" );
    }

    public function testDfsHandlerResetsMetaDataInsteadOfCreatingAProperty()
    {
        $source = file_get_contents( self::root() . '/kernel/private/classes/clusterfilehandlers/ezdfsfilehandler.php' );
        $this->assertStringNotContainsString( '$this->metaData = ', $source,
            'metaData is served by __get(); assigning it creates a public property that hides the reload' );
        $this->assertMatchesRegularExpression( '/public \$uniqueName\b/', $source );
    }

    public function testViewsDoNotRemovePersistentObjectsStatically()
    {
        $views = array( 'kernel/private/classes/views/trigger/list.php' => '\eZTrigger::remove(',
                        'kernel/private/classes/views/shop/discountgroupmembershipview.php' => '\eZDiscountSubRule::remove(' );
        foreach ( $views as $file => $call )
            $this->assertStringNotContainsString( $call, file_get_contents( self::root() . '/' . $file ) );

        $this->assertStringNotContainsString( 'eZCollaborationItemHandler::notificationParticipantTemplate(',
            file_get_contents( self::root() . '/kernel/classes/ezcollaborationitemhandler.php' ) );
    }
}

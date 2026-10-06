<?php
/**
 * Tests of the steps every package creator shares (eZPackageCreationHandler), without the database: the step
 * map (previous and next steps, method maps, steps a check leaves out), step templates, the checks of the package
 * information, changelog and maintainer forms, the changelog text split into entries, the thumbnail upload check
 * and the licence document a package gets.
 *
 * POST values come from a stand-in for eZHTTPTool; files are written under var/tmp and removed in tearDown().
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

require_once dirname( __DIR__ ) . '/workflowevents/fixtures/k1workflowtesthttp.php';

class eZPackageCreationHandlerTestCreator extends eZPackageCreationHandler
{
    public $skip = true;

    function checkOptional( $package, &$persistentData )
    {
        return !$this->skip;
    }
}

class eZPackageCreationHandlerTest extends PHPUnit\Framework\TestCase
{
    private $dir;
    private $files;

    protected function setUp(): void
    {
        chdir( dirname( __DIR__, 5 ) );
        $this->dir = 'var/tmp/phpunit-packagecreation-' . getmypid() . '-' . mt_rand();
        mkdir( $this->dir, 0775, true );
        $this->files = $_FILES;
    }

    protected function tearDown(): void
    {
        $_FILES = $this->files;
        if ( $this->dir && is_dir( $this->dir ) )
            eZDir::recursiveDelete( $this->dir );
    }

    private function creator( $skip = true )
    {
        $creator = new eZPackageCreationHandlerTestCreator( 'k1creator', 'K1 creator', array(
            array( 'id' => 'first', 'name' => 'First', 'template' => 'first.tpl', 'methods' => array( 'initialize' => 'initFirst', 'validate' => 'valFirst' ) ),
            array( 'id' => 'optional', 'name' => 'Optional', 'template' => 'optional.tpl', 'methods' => array( 'check' => 'checkOptional', 'commit' => 'commitOptional' ) ),
            array( 'id' => 'last', 'name' => 'Last', 'template' => 'last.tpl', 'use_standard_template' => true, 'methods' => array( 'load' => 'loadLast' ) ),
        ) );
        $creator->skip = $skip;
        $data = array();
        $creator->generateStepMap( false, $data );
        return $creator;
    }

    public function testStepMapLinksTheSteps()
    {
        $creator = $this->creator( false );
        $map = $creator->stepMap();
        $this->assertSame( 'first', $map['first']['id'] );
        $this->assertSame( array( 'first', 'optional', 'last' ), array_keys( $map['map'] ) );
        $this->assertFalse( $map['map']['first']['previous_step'] );
        $this->assertSame( 'optional', $map['map']['first']['next_step'] );
        $this->assertSame( 'first', $map['map']['optional']['previous_step'] );
        $this->assertFalse( $map['map']['last']['next_step'] );
        $this->assertSame( array( 'first' => 'initFirst' ), $creator->initializeStepMethodMap() );
        $this->assertSame( array( 'first' => 'valFirst' ), $creator->validateStepMethodMap() );
        $this->assertSame( array( 'optional' => 'commitOptional' ), $creator->commitStepMethodMap() );
        $this->assertSame( array( 'last' => 'loadLast' ), $creator->loadStepMethodMap() );
        $this->assertCount( 3, $creator->attribute( 'current_steps' ) );
    }

    public function testAStepItsCheckLeavesOutIsSkipped()
    {
        $creator = $this->creator( true );
        $map = $creator->stepMap();
        $this->assertSame( array( 'first', 'last' ), array_keys( $map['map'] ) );
        $this->assertSame( array( 'first', 'last' ), array_column( $creator->attribute( 'current_steps' ), 'id' ) );
        $this->assertSame( 'first', $map['map']['last']['previous_step'], 'the step before a skipped one is the previous step' );
    }

    public function testStepTemplates()
    {
        $creator = $this->creator();
        $map = $creator->stepMap();
        $this->assertSame( array( 'name' => 'first.tpl', 'dir' => 'creators/k1creator' ), $creator->stepTemplate( $map['map']['first'] ) );
        $this->assertSame( array( 'name' => 'last.tpl', 'dir' => 'create' ), $creator->stepTemplate( $map['map']['last'] ) );
    }

    public function testAttributes()
    {
        $creator = $this->creator();
        $this->assertSame( 'k1creator', $creator->attribute( 'id' ) );
        $this->assertSame( 'K1 creator', $creator->attribute( 'name' ) );
        $this->assertTrue( $creator->hasAttribute( 'step_map' ) );
        $this->assertFalse( $creator->hasAttribute( 'nosuch' ) );
        $this->assertNull( $creator->attribute( 'nosuch' ) );
    }

    public static function informationProvider()
    {
        return array(
            'nothing posted' => array( array(), array( 'License', 'Package name', 'Summary', 'Version' ) ),
            'unknown licence' => array( array( 'PackageLicence' => 'k1-not-a-licence', 'PackageVersion' => '1.0.0' ), array( 'License', 'Package name', 'Summary' ) ),
            'old style version' => array( array( 'PackageLicence' => 'GPL-2.0-or-later', 'PackageVersion' => '1.0' ), array( 'Package name', 'Summary', 'Version' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('informationProvider')]
    public function testPackageInformationChecks( array $post, array $fields )
    {
        $data = array( 'licence' => 'GPL-3.0-only' );
        $errors = array();
        $map = array();
        $this->assertFalse( $this->creator()->validatePackageInformation( false, new k1WorkflowTestHTTP( $post ), 'info', $map, $data, $errors ) );
        $this->assertSame( $fields, array_column( $errors, 'field' ) );
        if ( !isset( $post['PackageLicence'] ) || $post['PackageLicence'] !== 'GPL-2.0-or-later' )
            $this->assertSame( 'GPL-3.0-only', $data['licence'], 'a refused licence keeps the previous choice' );
    }

    public function testPackageInformationIsKeptForTheForm()
    {
        $data = array();
        $errors = array();
        $map = array();
        $http = new k1WorkflowTestHTTP( array( 'PackageSummary' => 'Sum', 'PackageDescription' => 'Desc', 'PackageVersion' => ' 2.1.0 ',
                                               'PackageLicence' => 'GPL-2.0-or-later', 'PackageHost' => 'h', 'PackagePackager' => 'p' ) );
        $this->creator()->validatePackageInformation( false, $http, 'info', $map, $data, $errors );
        $this->assertSame( array( 'Package name' ), array_column( $errors, 'field' ) );
        $this->assertSame( 'Sum', $data['summary'] );
        $this->assertSame( '2.1.0', $data['version'] );
        $this->assertSame( 'GPL-2.0-or-later', $data['licence'] );
        $this->assertSame( 'h', $data['host'] );
        $this->assertSame( 'p', $data['packager'] );
    }

    public function testChangelogChecks()
    {
        $data = array();
        $errors = array();
        $map = array();
        $this->assertFalse( $this->creator()->validatePackageChangelog( false, new k1WorkflowTestHTTP( array( 'PackageChangelogPerson' => '  ' ) ), 's', $map, $data, $errors ) );
        $this->assertSame( array( 'Name', 'Email', 'Changelog' ), array_column( $errors, 'field' ) );

        $errors = array();
        $http = new k1WorkflowTestHTTP( array( 'PackageChangelogPerson' => ' Ada ', 'PackageChangelogEmail' => 'ada@k1.example.invalid', 'PackageChangelogText' => '- one' ) );
        $this->assertTrue( $this->creator()->validatePackageChangelog( false, $http, 's', $map, $data, $errors ) );
        $this->assertSame( array(), $errors );
        $this->assertSame( 'Ada', $data['changelog_person'] );
    }

    public function testMaintainerChecks()
    {
        $data = array();
        $errors = array();
        $map = array();
        $this->assertFalse( $this->creator()->validatePackageMaintainer( false, new k1WorkflowTestHTTP(), 's', $map, $data, $errors ) );
        $this->assertSame( array( 'Name', 'Email' ), array_column( $errors, 'field' ) );

        $errors = array();
        $http = new k1WorkflowTestHTTP( array( 'PackageMaintainerPerson' => 'Bob', 'PackageMaintainerEmail' => 'bob@k1.example.invalid', 'PackageMaintainerRole' => 'lead' ) );
        $this->assertTrue( $this->creator()->validatePackageMaintainer( false, $http, 's', $map, $data, $errors ) );
        $this->assertSame( array( 'Bob', 'bob@k1.example.invalid', 'lead' ), array( $data['maintainer_person'], $data['maintainer_email'], $data['maintainer_role'] ) );
    }

    public static function changelogProvider()
    {
        return array(
            'bullets' => array( "- First change\n* Second change", array( 'First change', 'Second change' ) ),
            'continued lines' => array( "- First change\n  goes on\n- Second", array( 'First change goes on', 'Second' ) ),
            'windows line breaks' => array( "- One\r\n- Two\r- Three", array( 'One', 'Two', 'Three' ) ),
            'no bullets' => array( "Just text", array( 'Just text' ) ),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('changelogProvider')]
    public function testChangelogTextIsSplitIntoEntries( $text, array $entries )
    {
        $data = array( 'changelog_text' => $text );
        $this->creator()->commitPackageChangelog( false, null, array(), $data, null );
        $this->assertSame( $entries, $data['changelog_entries'] );
    }

    public function testThumbnailMustBeAnImage()
    {
        $errors = array();
        $_FILES = array();
        $this->assertFalse( eZPackageCreationHandler::httpFileIsImage( 'Thumb', $errors ) );
        $_FILES = array( 'Thumb' => array( 'name' => 'a.png', 'tmp_name' => 'x', 'size' => 0 ) );
        $this->assertFalse( eZPackageCreationHandler::httpFileIsImage( 'Thumb', $errors ) );
        $_FILES = array( 'Thumb' => array( 'name' => 'a.txt', 'tmp_name' => 'x', 'size' => 5 ) );
        $this->assertFalse( eZPackageCreationHandler::httpFileIsImage( 'Thumb', $errors ) );
        $this->assertCount( 3, $errors );
        $_FILES = array( 'Thumb' => array( 'name' => 'a.png', 'tmp_name' => 'x', 'size' => 5 ) );
        $this->assertTrue( eZPackageCreationHandler::httpFileIsImage( 'Thumb', $errors ) );
        $this->assertCount( 3, $errors );
    }

    public function testLicenceDocuments()
    {
        $package = eZPackage::create( 'k1_licence', array(), $this->dir );
        eZPackageCreationHandler::appendLicence( $package );
        $this->assertSame( array( 'LICENCE' ), array_column( $package->attribute( 'documents' ), 'name' ) );
        $this->assertStringContainsString( 'GNU General Public License', file_get_contents( "$this->dir/k1_licence/documents/LICENCE" ) );

        $other = eZPackage::create( 'k1_licence2', array(), $this->dir );
        eZPackageCreationHandler::appendLicence( $other, 'GPL-3.0-only' );
        $text = file_get_contents( "$this->dir/k1_licence2/documents/LICENCE" );
        $this->assertStringContainsString( 'SPDX-License-Identifier: GPL-3.0-only', $text );

        $none = eZPackage::create( 'k1_licence3', array(), $this->dir );
        eZPackageCreationHandler::appendLicence( $none, 'k1-not-a-licence' );
        $this->assertSame( array(), $none->attribute( 'documents' ) );
    }

    public function testDefaults()
    {
        $creator = $this->creator();
        $data = array();
        $this->assertSame( 'install', $creator->packageInstallType( false, $data ) );
        $this->assertFalse( $creator->packageType( false, $data ) );
    }
}
